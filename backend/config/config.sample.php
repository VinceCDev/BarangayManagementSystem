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
        'port'    => 3306,               // XAMPP default; this machine used 3307
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
        // Web root of the project = the folder name under htdocs. Every URL
        // (pages, API, assets, uploads) is derived from this, and it must
        // match RewriteBase in the root .htaccess. This is the ONLY place
        // to change if you rename or move the project folder.
        'base_url'    => '/BarangayManagementSystem-main',

        // Absolute filesystem paths (leave as-is unless the layout changes).
        'root_path'   => dirname(__DIR__, 2),
        'upload_path' => dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'upload',

        // Show detailed errors on screen? Turn OFF in production.
        'debug'       => true,
    ],
];
