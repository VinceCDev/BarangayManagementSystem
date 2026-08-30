<?php
/**
 * proof_insert.php — profile setup step 3.
 * Stores the 2x2 photo + valid ID, links them in the DMS when possible,
 * then finishes the wizard.
 */
declare(strict_types=1);

require __DIR__ . '/../../connection.php';                 // db(), constants
require __DIR__ . '/../../backend/helpers/functions.php';   // save_uploaded_image()

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . PAGES_URL . '/Proof of Identity.php');
    exit;
}

try {
    $picture = save_uploaded_image($_FILES['file1'] ?? [], 'profile_pic');
    $validId = save_uploaded_image($_FILES['file2'] ?? [], 'valid_id');
} catch (RuntimeException $e) {
    exit('Upload failed: ' . $e->getMessage());
}

try {
    $pdo = db();
    $pdo->prepare('INSERT INTO proof_of_identity (picture, valid_id) VALUES (?, ?)')
        ->execute(['profile_pic/' . $picture, 'valid_id/' . $validId]);
    $profileId = (int) $pdo->lastInsertId();

    // Best-effort DMS link (needs a signed-in user; skipped otherwise).
    $email = $_SESSION['username'] ?? null;
    if ($email) {
        $st = $pdo->prepare('SELECT id, userName FROM users WHERE userName = ? LIMIT 1');
        $st->execute([$email]);
        if ($u = $st->fetch()) {
            fms_pdo()->prepare(
                'INSERT INTO profile_file (photos, profile_id, user_id, user_email) VALUES (?, ?, ?, ?)'
            )->execute(['profile_pic/' . $picture, $profileId, $u['id'], $u['userName']]);
        }
    }

    header('Location: ' . PAGES_URL . '/Login.php?setup=done');
    exit;
} catch (Throwable $e) {
    error_log('proof_insert.php: ' . $e->getMessage());
    exit('Could not save your documents. Please try again.');
}
