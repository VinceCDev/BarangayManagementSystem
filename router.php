<?php
/**
 * router.php — request router for the PHP built-in dev server.
 *
 *   php -S localhost:8000 -t . router.php     (run from the project root)
 *   then open  http://localhost:8000/pages/login
 *
 * Real files (CSS, JS, images, uploads, existing .php scripts) are served
 * directly; anything else is handed to the front controller (index.php),
 * exactly like the Apache .htaccess does in production.
 */

declare(strict_types=1);

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$file = __DIR__ . '/' . ltrim(rawurldecode($path), '/');

if ($path !== '/' && is_file($file)) {
    return false;                 // let the dev server serve / execute it
}

$_SERVER['SCRIPT_NAME']     = '/index.php';
$_SERVER['SCRIPT_FILENAME'] = __DIR__ . '/index.php';
require __DIR__ . '/index.php';
