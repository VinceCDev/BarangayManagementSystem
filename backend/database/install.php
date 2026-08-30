<?php
/**
 * ---------------------------------------------------------------------------
 *  install.php — (re)build the databases from schema.sql + seed.sql
 * ---------------------------------------------------------------------------
 *  Run from the command line:
 *      php backend/database/install.php
 *  or open it once in the browser. It is safe to run repeatedly.
 * ---------------------------------------------------------------------------
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

$config = require __DIR__ . '/../config/config.php';
$cli    = PHP_SAPI === 'cli';
$nl     = $cli ? "\n" : "<br>\n";

// Connect WITHOUT selecting a database (schema.sql creates them).
$c = new mysqli(
    $config['db']['host'], $config['db']['user'], $config['db']['pass'], '', (int) $config['db']['port']
);
$c->set_charset('utf8mb4');

foreach (['schema.sql', 'seed.sql'] as $file) {
    $sql = file_get_contents(__DIR__ . '/' . $file);
    if ($c->multi_query($sql)) {
        do {
            if ($res = $c->store_result()) {
                $res->free();
            }
        } while ($c->more_results() && $c->next_result());
    }
    if ($c->error) {
        fwrite(STDERR, "Error while importing {$file}: {$c->error}{$nl}");
        exit(1);
    }
    echo "Imported {$file}{$nl}";
}

echo "Done. Default login: admin@barangay.gov.ph / Admin@123{$nl}";
