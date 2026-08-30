<?php
/**
 * ---------------------------------------------------------------------------
 *  update_password.php — set a new password from the "Reset Password" flow
 * ---------------------------------------------------------------------------
 *  Fixes vs. the original:
 *    - New password is hashed with password_hash() (was stored in plain text).
 *    - Prepared statement via PDO.
 *    - Still enforces the strength rule (>= 8 chars, letter + digit + symbol).
 * ---------------------------------------------------------------------------
 */

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /BarangayManagementSystem-main/frontend/pages/ResetPassword.php');
    exit;
}

$password = (string) ($_POST['password'] ?? '');
$email    = trim($_POST['email'] ?? '');

/**
 * Password policy: at least 8 characters and contain a letter, a digit and
 * one of @ $ ! % * ? &.
 */
function is_password_valid(string $password): bool
{
    return (bool) preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/', $password);
}

if (!is_password_valid($password)) {
    exit('error: Password must be at least 8 characters long and contain letters, numbers and a symbol.');
}

if ($email === '') {
    exit('error: Missing account e-mail.');
}

try {
    $stmt = db()->prepare('UPDATE users SET password = ? WHERE userName = ?');
    $stmt->execute([password_hash($password, PASSWORD_DEFAULT), $email]);

    if ($stmt->rowCount() === 0) {
        exit('error: No account found for that e-mail address.');
    }

    header('Location: /BarangayManagementSystem-main/frontend/pages/Login.php');
    exit;
} catch (Throwable $e) {
    error_log('update_password.php: ' . $e->getMessage());
    exit('error: A server error occurred. Please try again later.');
}
