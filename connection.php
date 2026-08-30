<?php
/**
 * ---------------------------------------------------------------------------
 *  connection.php  (compatibility shim)
 * ---------------------------------------------------------------------------
 *  Historically every page did `include 'connection.php'` and then used the
 *  mysqli handle $conn (and sometimes $fileManagementConn). A few pages even
 *  include this file several times — they call $conn->close() partway through
 *  and then re-include it to obtain a fresh connection.
 *
 *  All real configuration now lives in ONE place:
 *      backend/config/config.php     <- credentials / ports / paths
 *      backend/config/database.php   <- connection factory helpers
 *
 *  So this file:
 *    - loads that bootstrap once (require_once), then
 *    - (re)creates $conn and $fileManagementConn every time it is included,
 *      preserving the old "include again for a fresh handle" behaviour.
 * ---------------------------------------------------------------------------
 */

require_once __DIR__ . '/backend/config/database.php';

$__cfg = require __DIR__ . '/backend/config/config.php';

/** @var mysqli $conn                Primary business database. */
$conn = make_mysqli($__cfg['db']);

/** @var mysqli $fileManagementConn  Document Management System database. */
$fileManagementConn = make_mysqli($__cfg['fms']);

unset($__cfg);
