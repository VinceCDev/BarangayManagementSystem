<?php
/**
 * ---------------------------------------------------------------------------
 *  Central configuration — SAMPLE
 * ---------------------------------------------------------------------------
 *  Copy this file to "config.php" and fill in the values for your machine.
 *  config.php holds credentials and must never be committed to version
 *  control (see .gitignore).
 * ---------------------------------------------------------------------------
 */

return [

    // --- Primary (business) database -------------------------------------
    'db' => [
        'host'    => '127.0.0.1',
        'port'    => 3306,
        'name'    => 'barangay_management_system',
        'user'    => 'root',
        'pass'    => '',
        'charset' => 'utf8mb4',
    ],

    // --- Document Management System (file) database ---------------------
    'fms' => [
        'host'    => '127.0.0.1',
        'port'    => 3306,
        'name'    => 'file_management_system',
        'user'    => 'root',
        'pass'    => '',
        'charset' => 'utf8mb4',
    ],

    // --- SMTP (used by the public contact form) -----------------------------
    'mail' => [
        'host'       => 'smtp.gmail.com',
        'port'       => 465,
        'encryption' => 'ssl',
        'username'   => 'you@example.com',
        'password'   => 'your-app-password',
        'from_email' => 'you@example.com',
        'from_name'  => 'Barangay Paule 1',
        'to_email'   => 'you@example.com',
    ],

    // --- Application ------------------------------------------------------
    'app' => [
        // Absolute filesystem path to the /upload directory.
        'upload_path' => dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'upload',
        // Public base URL of the /upload directory (used in <img src> etc.).
        'upload_url'  => '/BarangayManagementSystem-main/upload',
        // Show detailed errors on screen? Turn OFF in production.
        'debug'       => true,
    ],
];
