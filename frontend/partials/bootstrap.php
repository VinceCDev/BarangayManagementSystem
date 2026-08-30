<?php
/**
 * ---------------------------------------------------------------------------
 *  partials/bootstrap.php — front-end request bootstrap
 * ---------------------------------------------------------------------------
 *  Every page in frontend/pages/ requires this file FIRST. It:
 *    - starts the session
 *    - loads the database + helpers
 *    - (for admin pages) enforces login and loads the current user's
 *      display name / role / avatar for the top bar
 *
 *  A page then sets a few variables and includes the matching layout:
 *
 *      require __DIR__ . '/../partials/bootstrap.php';
 *      require_admin();                       // guard (admin pages only)
 *      $page_title   = 'Residents';
 *      $page_heading = 'Barangay Residents';
 *      $active_nav   = 'residents';
 *      require __DIR__ . '/../partials/admin_top.php';
 *      // ... page content ...
 *      require __DIR__ . '/../partials/admin_bottom.php';
 * ---------------------------------------------------------------------------
 */

declare(strict_types=1);

require_once __DIR__ . '/../../connection.php';          // $conn, $fileManagementConn, db(), constants
require_once __DIR__ . '/../../backend/helpers/auth.php'; // session + require_login()

/** Absolute URL helpers built from BASE_URL (defined in config/database.php). */
function asset(string $path): string { return ASSETS_URL . '/' . ltrim($path, '/'); }
function page_url(string $file): string { return PAGES_URL . '/' . ltrim($file, '/'); }
function action_url(string $file): string { return ACTIONS_URL . '/' . ltrim($file, '/'); }
function upload_url(string $path): string { return UPLOAD_URL . '/' . ltrim($path, '/'); }

/** Guard for the admin panel. Also handles the ?logout=1 link in the top bar. */
function require_admin(): void
{
    if (isset($_GET['logout'])) {
        logout(page_url('Login.php'));
    }
    require_login(page_url('Login.php'));
}

/**
 * Fetch the signed-in user's display info for the top bar.
 * Returns name, role and avatar URL (with sensible fallbacks).
 */
function current_user_card(): array
{
    $out = ['name' => 'Administrator', 'role' => 'Staff', 'avatar' => asset('images/logo1.png')];
    $email = current_username();
    if (!$email) {
        return $out;
    }
    try {
        $st = db()->prepare(
            'SELECT firstname, middlename, lastname FROM profiledata WHERE email = ? LIMIT 1'
        );
        $st->execute([$email]);
        if ($p = $st->fetch()) {
            $name = trim(($p['firstname'] ?? '') . ' ' . ($p['middlename'] ?? '') . ' ' . ($p['lastname'] ?? ''));
            if ($name !== '') {
                $out['name'] = $name;
            }
        }

        $st = db()->prepare('SELECT userType FROM users WHERE userName = ? LIMIT 1');
        $st->execute([$email]);
        if ($u = $st->fetch()) {
            $out['role'] = ucfirst((string) ($u['userType'] ?: 'staff'));
        }

        $st = db()->prepare(
            'SELECT picture FROM proof_of_identity WHERE id = (SELECT id FROM profiledata WHERE email = ?) LIMIT 1'
        );
        $st->execute([$email]);
        if (($pic = $st->fetchColumn()) && $pic) {
            // Older rows stored a bare filename; newer ones a path under /upload.
            $out['avatar'] = str_contains((string) $pic, '/') ? UPLOAD_URL . '/' . ltrim((string) $pic, '/')
                                                              : upload_url('profile_pic/' . $pic);
        }
    } catch (Throwable $e) {
        error_log('current_user_card: ' . $e->getMessage());
    }
    return $out;
}

/** Escape helper (short alias of the one in backend/helpers/functions.php). */
if (!function_exists('e')) {
    function e(?string $v): string { return htmlspecialchars((string) $v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
}

/**
 * Render a numbered pagination bar for a list view.
 *
 * @param int   $page   current page (1-based)
 * @param int   $pages  total number of pages
 * @param int   $total  total row count (for the summary line)
 * @param array $query  extra query-string params to keep (search, filters…)
 */
function render_pager(int $page, int $pages, int $total, array $query = []): string
{
    if ($pages <= 1) {
        return '';
    }
    $link = static function (int $p, string $label, bool $disabled = false, bool $active = false) use ($query): string {
        $cls = 'page-link';
        if ($disabled) return '<li class="page-item disabled"><span class="page-link">' . $label . '</span></li>';
        if ($active)   return '<li class="page-item active"><span class="page-link">' . $label . '</span></li>';
        $qs = http_build_query($query + ['page' => $p]);
        return '<li class="page-item"><a class="page-link" href="?' . e($qs) . '">' . $label . '</a></li>';
    };

    // window of page numbers around the current page
    $start = max(1, $page - 2);
    $end   = min($pages, $start + 4);
    $start = max(1, $end - 4);

    $items  = $link($page - 1, '&laquo;', $page <= 1);
    if ($start > 1) {
        $items .= $link(1, '1');
        if ($start > 2) $items .= '<li class="page-item disabled"><span class="page-link">…</span></li>';
    }
    for ($i = $start; $i <= $end; $i++) {
        $items .= $link($i, (string) $i, false, $i === $page);
    }
    if ($end < $pages) {
        if ($end < $pages - 1) $items .= '<li class="page-item disabled"><span class="page-link">…</span></li>';
        $items .= $link($pages, (string) $pages);
    }
    $items .= $link($page + 1, '&raquo;', $page >= $pages);

    return '<div class="card-ft pager">'
         . '<span class="pager__info">' . number_format($total) . ' record' . ($total === 1 ? '' : 's')
         . ' · page ' . $page . ' of ' . $pages . '</span>'
         . '<nav aria-label="Pagination"><ul class="pagination pagination-sm mb-0">' . $items . '</ul></nav>'
         . '</div>';
}

/**
 * Render a labelled <select> filter that auto-submits its form on change.
 *
 * @param string $name     query-string / field name
 * @param string $current  currently selected value
 * @param array  $options  value => label  (a '' key acts as the "All" option)
 */
function filter_select(string $name, string $current, array $options): string
{
    $out = '<select class="form-select form-select-sm" name="' . e($name) . '" onchange="this.form.submit()" style="width:auto">';
    foreach ($options as $val => $label) {
        $sel = ((string) $val === (string) $current) ? ' selected' : '';
        $out .= '<option value="' . e((string) $val) . '"' . $sel . '>' . e((string) $label) . '</option>';
    }
    return $out . '</select>';
}
