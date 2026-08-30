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
