<?php
/**
 * ---------------------------------------------------------------------------
 *  users_insert.php — create a new system account
 * ---------------------------------------------------------------------------
 *  Fixes vs. the original:
 *    - Password is hashed with password_hash() (was stored in plain text).
 *    - Prepared statement via PDO.
 *    - Rejects a duplicate username with a friendly message instead of a
 *      raw SQL error.
 * ---------------------------------------------------------------------------
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/auth.php';

require_login();                       // only a logged-in admin may add users

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /BarangayManagementSystem-main/frontend/pages/Users.php');
    exit;
}

// --- Collect + validate input ------------------------------------------
$fullName = trim($_POST['userFullName'] ?? '');
$userName = trim($_POST['userName'] ?? '');
$password = (string) ($_POST['password'] ?? '');
$userType = trim($_POST['usertype'] ?? 'staff');

if ($fullName === '' || $userName === '' || $password === '') {
    exit('All fields are required.');
}

try {
    $stmt = db()->prepare(
        'INSERT INTO users (fullName, userName, password, userType) VALUES (?, ?, ?, ?)'
    );
    $stmt->execute([
        $fullName,
        $userName,
        password_hash($password, PASSWORD_DEFAULT),
        $userType,
    ]);

    header('Location: /BarangayManagementSystem-main/frontend/pages/Users.php');
    exit;
} catch (PDOException $e) {
    // 23000 = integrity constraint violation (duplicate unique key).
    if ($e->getCode() === '23000') {
        exit('That username is already taken.');
    }
    error_log('users_insert.php: ' . $e->getMessage());
    exit('Could not create the user. Please try again.');
}
