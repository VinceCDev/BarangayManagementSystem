<?php
/**
 * ---------------------------------------------------------------------------
 *  login_test.php — authenticates a user and returns a JSON result
 * ---------------------------------------------------------------------------
 *  Called by the AJAX form in Login.php.
 *
 *  Response shape (unchanged, so the front-end keeps working):
 *      { "success": true }                       -> credentials OK
 *      { "success": false }                      -> wrong username / password
 *      { "error": "Username and password ..." }  -> missing input
 *      { "error": "Invalid request" }            -> not a POST request
 *
 *  Security fixes vs. the original:
 *    - Uses a prepared statement (was string-concatenated -> SQL injection).
 *    - Verifies a hashed password with password_verify() (was plain-text ==).
 *    - Transparently upgrades old plain-text passwords to a hash on login.
 *    - Regenerates the session id after login (prevents session fixation).
 * ---------------------------------------------------------------------------
 */

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';

// --- Only accept POST -----------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Invalid request']);
    exit;
}

// --- Validate input -----------------------------------------------------
$username = trim($_POST['username'] ?? '');
$password = (string) ($_POST['password'] ?? '');

if ($username === '' || $password === '') {
    echo json_encode(['error' => 'Username and password are required']);
    exit;
}

try {
    // --- Look the user up by name only, then verify the hash in PHP -------
    $stmt = db()->prepare('SELECT id, userName, password FROM users WHERE userName = ? LIMIT 1');
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    $isValid = false;

    if ($user) {
        $stored = (string) $user['password'];

        if (password_verify($password, $stored)) {
            // Normal case: already a hashed password.
            $isValid = true;
        } elseif (!str_starts_with($stored, '$2y$') && hash_equals($stored, $password)) {
            // Legacy case: password was stored in plain text. Accept it once,
            // then immediately replace it with a proper hash.
            $isValid = true;
            $upd = db()->prepare('UPDATE users SET password = ? WHERE id = ?');
            $upd->execute([password_hash($password, PASSWORD_DEFAULT), $user['id']]);
        }
    }

    if ($isValid) {
        // Prevent session fixation by issuing a fresh session id.
        session_regenerate_id(true);
        $_SESSION['username'] = $user['userName'];
        $_SESSION['user_id']  = (int) $user['id'];

        // "Remember me" -> store the username in a cookie for 30 days.
        if (($_POST['remember'] ?? '') === 'on') {
            setcookie('remembered_username', $user['userName'], [
                'expires'  => time() + 30 * 24 * 60 * 60,
                'path'     => '/',
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
        } else {
            setcookie('remembered_username', '', time() - 3600, '/');
        }

        echo json_encode(['success' => true]);
        exit;
    }

    echo json_encode(['success' => false]);
} catch (Throwable $e) {
    // Never leak SQL / stack details to the browser.
    error_log('login_test.php: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'A server error occurred. Please try again later.']);
}
