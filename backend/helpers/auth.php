<?php
/**
 * ---------------------------------------------------------------------------
 *  auth.php — session / authentication helpers
 * ---------------------------------------------------------------------------
 *  Include this at the very top of any page or action that must only be
 *  reachable by a logged-in user:
 *
 *      require_once __DIR__ . '/backend/helpers/auth.php';
 *      require_login();
 *
 *  The original code repeated this block in every file:
 *      if (!isset($_SESSION['username'])) { header('Location: Login.php'); exit; }
 *  which was easy to forget (several action scripts had no check at all).
 * ---------------------------------------------------------------------------
 */

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Redirect to the login page unless somebody is logged in.
 *
 * @param string $loginPage Page to send guests to (relative to the web root
 *                           the caller lives in).
 */
function require_login(string $loginPage = 'Login.php'): void
{
    if (empty($_SESSION['username'])) {
        header('Location: ' . $loginPage);
        exit;
    }
}

/** @return string|null The logged-in account's username / e-mail. */
function current_username(): ?string
{
    return $_SESSION['username'] ?? null;
}

/** @return int|null The logged-in account's users.id, if known. */
function current_user_id(): ?int
{
    return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
}

/**
 * Destroy the session and send the visitor back to the login page.
 * Replaces the ad-hoc `?logout` handling copied across the pages.
 */
function logout(string $loginPage = 'Login.php'): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
    header('Location: ' . $loginPage);
    exit;
}
