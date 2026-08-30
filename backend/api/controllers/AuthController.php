<?php
/**
 * ---------------------------------------------------------------------------
 *  api/controllers/AuthController.php — session auth endpoints
 * ---------------------------------------------------------------------------
 *    POST /api/v1/auth/login    { "username": "...", "password": "..." }
 *    POST /api/v1/auth/logout
 *    GET  /api/v1/auth/me
 * ---------------------------------------------------------------------------
 */

declare(strict_types=1);

require_once __DIR__ . '/../Http.php';
require_once __DIR__ . '/../Auth.php';

final class AuthController
{
    /** POST /auth/login — verify credentials and open a session. */
    public function login(): never
    {
        $body = api_body();
        $username = trim((string) ($body['username'] ?? ''));
        $password = (string) ($body['password'] ?? '');

        if ($username === '' || $password === '') {
            api_fail('Username and password are required.', 422, 'validation', [
                'username' => $username === '' ? 'Required.' : null,
                'password' => $password === '' ? 'Required.' : null,
            ]);
        }

        try {
            $st = db()->prepare('SELECT id, userName, password FROM users WHERE userName = ? LIMIT 1');
            $st->execute([$username]);
            $user = $st->fetch();
        } catch (Throwable $e) {
            error_log('AuthController::login ' . $e->getMessage());
            api_fail('A server error occurred.', 500, 'server_error');
        }

        $valid = false;
        if ($user) {
            $stored = (string) $user['password'];
            if (password_verify($password, $stored)) {
                $valid = true;
            } elseif (!str_starts_with($stored, '$2y$') && hash_equals($stored, $password)) {
                // legacy plain-text password: accept once, then upgrade to a hash
                $valid = true;
                $upd = db()->prepare('UPDATE users SET password = ? WHERE id = ?');
                $upd->execute([password_hash($password, PASSWORD_DEFAULT), $user['id']]);
            }
        }

        if (!$valid) {
            api_fail('Invalid username or password.', 401, 'bad_credentials');
        }

        session_regenerate_id(true);
        $_SESSION['username'] = $user['userName'];
        $_SESSION['user_id']  = (int) $user['id'];

        api_ok(api_user());
    }

    /** POST /auth/logout — destroy the session. */
    public function logout(): never
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
        api_ok(['message' => 'Signed out.']);
    }

    /** GET /auth/me — the current session's user. */
    public function me(): never
    {
        api_ok(require_api_auth());
    }
}
