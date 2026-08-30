<?php
/**
 * ===========================================================================
 *  index.php — FRONT CONTROLLER
 * ===========================================================================
 *  Apache (.htaccess) and the PHP dev-server (router.php) send every request
 *  that is not a real file here. This script turns a clean URL into an
 *  include of the matching view or API handler:
 *
 *      /BarangayManagementSystem-main/                 -> frontend/pages/Home.php
 *      /BarangayManagementSystem-main/pages/login      -> frontend/pages/Login.php
 *      /BarangayManagementSystem-main/pages/residents  -> frontend/pages/Resident.php
 *      /BarangayManagementSystem-main/api/v1/residents -> backend/api/index.php
 *      /BarangayManagementSystem-main/logout           -> ends the session
 *
 *  The original file paths still work when hit directly, so nothing that was
 *  bookmarked or hard-coded breaks.
 * ===========================================================================
 */

declare(strict_types=1);

require_once __DIR__ . '/backend/routes/pages.php';

/* --- Work out the path requested, relative to the app's base folder ------- */
$scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/'); // /BarangayManagementSystem-main
$uriPath   = rawurldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');

$route = $uriPath;
if ($scriptDir !== '' && str_starts_with($uriPath, $scriptDir)) {
    $route = substr($uriPath, strlen($scriptDir));
}
$route = trim($route, '/');                 // "", "pages/login", "api/v1/residents"

/* --- 1. Home ------------------------------------------------------------- */
if ($route === '' || $route === 'index.php') {
    require __DIR__ . '/frontend/pages/Home.php';
    return;
}

/* --- 2. REST API  (/api/...) ------------------------------------------- */
if ($route === 'api' || str_starts_with($route, 'api/')) {
    $_SERVER['API_ROUTE'] = substr($route, 3);       // strip "api" (+ slash below)
    $_SERVER['API_ROUTE'] = ltrim($_SERVER['API_ROUTE'], '/');
    require __DIR__ . '/backend/api/index.php';
    return;
}

/* --- 3. Convenience: /logout ---------------------------------------- */
if ($route === 'logout') {
    require_once __DIR__ . '/backend/helpers/auth.php';
    logout($scriptDir . '/pages/login');
}

/* --- 4. Pages  (/pages/{slug}) ------------------------------------------ */
if ($route === 'pages' || str_starts_with($route, 'pages/')) {
    $slug = trim(substr($route, 5), '/');            // after "pages/"
    $file = $slug === '' ? 'Home' : page_slug_to_file($slug);

    if ($file !== null) {
        $path = __DIR__ . '/frontend/pages/' . $file . '.php';
        if (is_file($path)) {
            require $path;
            return;
        }
    }
    http_response_code(404);
    require __DIR__ . '/frontend/pages/_404.php';
    return;
}

/* --- 5. Convenience: /actions/{slug} -> backend/actions/{slug}.php ---- */
if (str_starts_with($route, 'actions/')) {
    $name = basename(trim(substr($route, 8), '/'));   // no path traversal
    $path = __DIR__ . '/backend/actions/' . $name . (str_ends_with($name, '.php') ? '' : '.php');
    if (is_file($path)) {
        require $path;
        return;
    }
}

/* --- 6. Nothing matched ---------------------------------------------- */
http_response_code(404);
require __DIR__ . '/frontend/pages/_404.php';
