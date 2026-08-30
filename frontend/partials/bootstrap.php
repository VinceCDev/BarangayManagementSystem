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

require_once __DIR__ . '/../../backend/connection.php';          // $conn, $fileManagementConn, db(), constants
require_once __DIR__ . '/../../backend/helpers/auth.php'; // session + require_login()

/** Absolute URL helpers built from BASE_URL (defined in config/database.php). */
function asset(string $path): string
{
    $path = ltrim($path, '/');
    $url  = ASSETS_URL . '/' . $path;
    // Cache-bust local CSS/JS with the file's last-modified time so browsers
    // always pick up edits instead of serving a stale copy.
    if (preg_match('/\.(css|js)$/i', $path)) {
        $full = ROOT_PATH . '/frontend/assets/' . $path;
        if (is_file($full)) {
            $url .= '?v=' . filemtime($full);
        }
    }
    return $url;
}
function page_url(string $file): string { return PAGES_URL . '/' . ltrim($file, '/'); }
function action_url(string $file): string { return ACTIONS_URL . '/' . ltrim($file, '/'); }
function upload_url(string $path): string { return UPLOAD_URL . '/' . ltrim($path, '/'); }
/** The public home page (index.php now lives at the project root). */
function home_url(): string { return BASE_URL . '/'; }

/** Guard for the admin panel. Also handles the ?logout=1 link in the top bar. */
function require_admin(): void
{
    if (isset($_GET['logout'])) {
        logout(page_url('Login.php'));
    }
    require_login(page_url('Login.php'));
}

/* ===========================================================================
 *  Roles / access control
 * ------------------------------------------------------------------------- */

/** Every role slug -> human label. */
function roles_all(): array
{
    return [
        'admin'       => 'System Administrator',
        'official'    => 'Barangay Official',
        'sk_chairman' => 'SK Chairman',
        'treasurer'   => 'Barangay Treasurer',
        'resident'    => 'Resident',
    ];
}

/**
 * The signed-in user's role slug, read from users.userType.
 * Legacy values ('admin' / 'staff' / '') map to 'admin'; anything unknown
 * falls back to 'resident'.
 */
function current_role(): string
{
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }
    $role  = 'resident';
    $email = current_username();
    if ($email) {
        try {
            $st = db()->prepare('SELECT userType FROM users WHERE userName = ? LIMIT 1');
            $st->execute([$email]);
            $t = strtolower(trim((string) $st->fetchColumn()));
            $role = match ($t) {
                'admin', 'staff', ''              => 'admin',
                'official', 'barangay_official'   => 'official',
                'sk', 'sk_chairman', 'sk_chairperson' => 'sk_chairman',
                'treasurer', 'barangay_treasurer' => 'treasurer',
                default => array_key_exists($t, roles_all()) ? $t : 'resident',
            };
        } catch (Throwable $e) {
            error_log('current_role: ' . $e->getMessage());
        }
    }
    return $cache = $role;
}

/** @return string Human label for a role (defaults to the current user's). */
function role_label(?string $slug = null): string
{
    return roles_all()[$slug ?? current_role()] ?? 'User';
}

function is_admin(): bool { return current_role() === 'admin'; }

/** True if the current role has the given nav key in its filtered menu. */
function role_can(string $navKey): bool
{
    require_once __DIR__ . '/nav.php';
    foreach (nav_for_role(current_role()) as $items) {
        foreach ($items as $it) {
            if ($it['key'] === $navKey) {
                return true;
            }
        }
    }
    return false;
}

/**
 * Page guard: allow only these roles (admin is always allowed). Also runs the
 * login / logout handling from require_admin().
 */
function require_role(array $allowed): void
{
    require_admin();
    if (current_role() !== 'admin' && !in_array(current_role(), $allowed, true)) {
        http_response_code(403);
        exit('<div style="font:15px/1.6 system-ui;padding:4rem;text-align:center;color:#33414f">'
            . '<h2 style="margin:0 0 .5rem">Access denied</h2>'
            . '<p>Your account role does not have access to this page.</p>'
            . '<a href="' . page_url('AdminDashboard.php') . '" style="color:#1450b5">Return to dashboard</a></div>');
    }
}

/* ===========================================================================
 *  Reusable "View" button + modal
 * ------------------------------------------------------------------------- */

/**
 * A read-only "view" icon button for a table row. Pass an ordered map of
 * label => value; clicking opens the shared #viewModal (rendered by
 * admin_bottom.php) with those details.
 */
function view_button(array $fields, string $title = 'Details'): string
{
    $payload = json_encode(
        ['title' => $title, 'fields' => array_map(static fn ($v) => (string) $v, $fields)],
        JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE
    );
    return '<button type="button" class="btn btn-sm btn-light btn-icon" title="View" '
         . "onclick=\"showView(this)\" data-view='" . e($payload) . "'>"
         . '<i class="bi bi-eye"></i></button>';
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

        $out['role'] = role_label();

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
 * Render the "Showing X–Y of Z results" line plus the numbered page navigation,
 * placed BELOW the table. Always rendered — the page bar shows even when there
 * is only one page (or a single record); prev/next just stay disabled.
 *
 * @param int   $page     current page (1-based)
 * @param int   $pages    total number of pages (>= 1)
 * @param int   $total    total row count
 * @param array $query    extra query-string params to keep (search, filters…)
 * @param int   $perPage  rows per page (for the "X–Y of Z" range)
 */
function render_pager(int $page, int $pages, int $total, array $query = [], int $perPage = 0): string
{
    // Nothing to page through -> no footer at all (the empty-state row covers it).
    if ($total === 0) {
        return '';
    }

    $pages = max(1, $pages);
    $page  = min(max(1, $page), $pages);

    // "Showing 1–8 of 42 results"
    if ($perPage > 0) {
        $from = ($page - 1) * $perPage + 1;
        $to   = min($total, $page * $perPage);
        $summary = 'Showing <strong>' . number_format($from) . '–' . number_format($to)
                 . '</strong> of <strong>' . number_format($total) . '</strong> result' . ($total === 1 ? '' : 's');
    } else {
        $summary = '<strong>' . number_format($total) . '</strong> result' . ($total === 1 ? '' : 's');
    }

    $link = static function (int $p, string $label, bool $disabled = false, bool $active = false) use ($query): string {
        if ($disabled) return '<li class="page-item disabled"><span class="page-link">' . $label . '</span></li>';
        if ($active)   return '<li class="page-item active"><span class="page-link">' . $label . '</span></li>';
        $qs = http_build_query($query + ['page' => $p]);
        return '<li class="page-item"><a class="page-link" href="?' . e($qs) . '">' . $label . '</a></li>';
    };

    $start = max(1, $page - 2);
    $end   = min($pages, $start + 4);
    $start = max(1, $end - 4);

    $items  = $link($page - 1, '&lsaquo;', $page <= 1);
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
    $items .= $link($page + 1, '&rsaquo;', $page >= $pages);

    $nav = '<nav aria-label="Pagination"><ul class="pagination pagination-sm mb-0">' . $items . '</ul></nav>';

    return '<div class="card-ft pager">'
         . '<span class="pager__info">' . $summary . '</span>'
         . $nav
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
