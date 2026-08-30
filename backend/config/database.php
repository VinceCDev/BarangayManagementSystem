<?php
/**
 * ---------------------------------------------------------------------------
 *  Database bootstrap
 * ---------------------------------------------------------------------------
 *  Reads backend/config/config.php and opens the database connections used by
 *  the whole application.
 *
 *  It exposes BOTH connection styles so the codebase can be migrated to PDO
 *  gradually without breaking the pages that still use mysqli:
 *
 *    PDO (preferred for new / refactored code)
 *      db()      -> PDO handle for `barangay_management_system`
 *      fms_pdo() -> PDO handle for `file_management_system`
 *
 *    mysqli (legacy — kept so existing pages keep working)
 *      $conn               -> mysqli handle for `barangay_management_system`
 *      $fileManagementConn -> mysqli handle for `file_management_system`
 *
 *  Every connection is configured to THROW on error, so callers can rely on
 *  try/catch instead of checking return values by hand.
 * ---------------------------------------------------------------------------
 */

declare(strict_types=1);

/** @var array $config Loaded once and cached for the whole request. */
$config = require __DIR__ . '/config.php';

// Report every error while developing; the display is controlled by app.debug.
error_reporting(E_ALL);
ini_set('display_errors', $config['app']['debug'] ? '1' : '0');

// --- URL / path constants (used by pages, actions and partials) ---------
//  BASE_URL     -> /BarangayManagementSystem-main
//  ASSETS_URL   -> .../frontend/assets      (css, js, images)
//  UPLOAD_URL   -> .../upload               (public link to uploaded files)
//  PAGES_URL    -> .../frontend/pages       (for redirects from actions)
//  ACTIONS_URL  -> .../backend/actions      (for <form action> / fetch())
//  UPLOAD_PATH / ROOT_PATH -> filesystem paths for reading/writing files
defined('BASE_URL')    || define('BASE_URL',    rtrim($config['app']['base_url'], '/'));
defined('ASSETS_URL')  || define('ASSETS_URL',  BASE_URL . '/frontend/assets');
defined('UPLOAD_URL')  || define('UPLOAD_URL',  BASE_URL . '/upload');
defined('PAGES_URL')   || define('PAGES_URL',   BASE_URL . '/frontend/pages');
defined('ACTIONS_URL') || define('ACTIONS_URL', BASE_URL . '/backend/actions');
defined('ROOT_PATH')   || define('ROOT_PATH',   $config['app']['root_path']);
defined('UPLOAD_PATH') || define('UPLOAD_PATH', $config['app']['upload_path']);

// Make mysqli raise exceptions instead of warnings (matches PDO behaviour).
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);


/**
 * Build a PDO connection from one of the config blocks ('db' or 'fms').
 *
 * @param array $c Connection settings: host, port, name, user, pass, charset.
 */
function make_pdo(array $c): PDO
{
    $dsn = sprintf(
        'mysql:host=%s;port=%d;dbname=%s;charset=%s',
        $c['host'], $c['port'], $c['name'], $c['charset']
    );

    return new PDO($dsn, $c['user'], $c['pass'], [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,      // throw on error
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,            // sensible default
        PDO::ATTR_EMULATE_PREPARES   => false,                       // real prepared statements
    ]);
}

/**
 * Build a mysqli connection from one of the config blocks.
 *
 * @param array $c Connection settings.
 */
function make_mysqli(array $c): mysqli
{
    $conn = new mysqli($c['host'], $c['user'], $c['pass'], $c['name'], (int) $c['port']);
    $conn->set_charset($c['charset']);
    return $conn;
}


// --- Lazily-created shared PDO handles ------------------------------------

/** @return PDO Primary business database. */
function db(): PDO
{
    static $pdo = null;
    global $config;
    return $pdo ??= make_pdo($config['db']);
}

/** @return PDO Document Management System database. */
function fms_pdo(): PDO
{
    static $pdo = null;
    global $config;
    return $pdo ??= make_pdo($config['fms']);
}


// NOTE: the legacy mysqli handles ($conn / $fileManagementConn) are created by
// connection.php, not here. That file is meant to be include-d repeatedly by
// the older pages (some of them call $conn->close() and then re-include it to
// get a fresh handle), so the connection must be re-opened on every include —
// which a require_once in this file could not do.
