<?php
/**
 * ---------------------------------------------------------------------------
 *  user_update.php — edit an existing system account
 * ---------------------------------------------------------------------------
 *  Fixes vs. the original:
 *    - Prepared statements via PDO (was string interpolation -> SQL injection).
 *    - Password, when changed, is hashed with password_hash().
 *    - The password is only touched when a new one was actually entered
 *      and both fields match.
 * ---------------------------------------------------------------------------
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/auth.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /BarangayManagementSystem-main/frontend/pages/Users.php');
    exit;
}

// --- Input ------------------------------------------------------------
$userId          = (int) ($_POST['users_id'] ?? 0);
$fullName        = trim($_POST['userFullName'] ?? '');
$userName        = trim($_POST['userName'] ?? '');
$newPassword     = (string) ($_POST['newpassword'] ?? '');
$confirmPassword = (string) ($_POST['confirmpassword'] ?? '');
$userType        = substr(trim($_POST['usertype'] ?? ''), 0, 50);

if ($userId <= 0 || $fullName === '' || $userName === '') {
    exit('Missing required fields.');
}

try {
    if ($newPassword !== '' && $newPassword === $confirmPassword) {
        // Update including a freshly hashed password.
        $stmt = db()->prepare(
            'UPDATE users SET fullName = ?, userName = ?, password = ?, userType = ? WHERE id = ?'
        );
        $stmt->execute([
            $fullName,
            $userName,
            password_hash($newPassword, PASSWORD_DEFAULT),
            $userType,
            $userId,
        ]);
    } else {
        // Leave the existing password untouched.
        $stmt = db()->prepare(
            'UPDATE users SET fullName = ?, userName = ?, userType = ? WHERE id = ?'
        );
        $stmt->execute([$fullName, $userName, $userType, $userId]);
    }

    header('Location: /BarangayManagementSystem-main/frontend/pages/Users.php');
    exit;
} catch (PDOException $e) {
    if ($e->getCode() === '23000') {
        exit('That username is already taken by another account.');
    }
    error_log('user_update.php: ' . $e->getMessage());
    exit('Could not update the user. Please try again.');
}
