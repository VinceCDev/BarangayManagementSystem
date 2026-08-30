<?php
/**
 * ---------------------------------------------------------------------------
 *  functions.php — small shared helpers
 * ---------------------------------------------------------------------------
 *  Utilities that were previously copy-pasted (or missing) across the
 *  action scripts: safe output escaping, redirects, and image upload
 *  handling with real validation.
 * ---------------------------------------------------------------------------
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

/**
 * HTML-escape a value for safe output inside a page.
 * Use everywhere the old code did `echo $row['x']` directly (stored XSS).
 */
function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Send a Location header and stop. */
function redirect(string $to): never
{
    header('Location: ' . $to);
    exit;
}

/**
 * Handle a single uploaded image.
 *
 * @param array  $file        One entry from $_FILES.
 * @param string $subdir      Folder name under /upload (e.g. "resident_photo").
 * @param int    $maxBytes    Maximum accepted size.
 * @return string             The stored file name (to save in the DB).
 * @throws RuntimeException   On any validation or move failure.
 */
function save_uploaded_image(array $file, string $subdir, int $maxBytes = 5_000_000): string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('No file was uploaded or the upload failed.');
    }
    if ($file['size'] > $maxBytes) {
        throw new RuntimeException('The file is too large.');
    }

    // Trust the real MIME type, not the client-supplied extension.
    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $mime    = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    if (!isset($allowed[$mime])) {
        throw new RuntimeException('Only JPG, PNG and WebP images are allowed.');
    }

    $config  = require __DIR__ . '/../config/config.php';
    $destDir = rtrim($config['app']['upload_path'], '/\\') . DIRECTORY_SEPARATOR . $subdir;
    if (!is_dir($destDir) && !mkdir($destDir, 0775, true) && !is_dir($destDir)) {
        throw new RuntimeException('Upload folder could not be created.');
    }

    // Unique, safe name — never reuse the client file name directly.
    $name = bin2hex(random_bytes(16)) . '.' . $allowed[$mime];
    if (!move_uploaded_file($file['tmp_name'], $destDir . DIRECTORY_SEPARATOR . $name)) {
        throw new RuntimeException('Could not store the uploaded file.');
    }

    return $name;
}
