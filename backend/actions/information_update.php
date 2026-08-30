<?php
/**
 * information_update.php — save one "Barangay Information" content block.
 *
 * POST: section = introduction | mission | vision | history | map |
 *                 statistics | population | economics | business | income
 * plus the fields for that section. Redirects back to Information.php.
 *
 * Rewritten to use PDO + prepared statements and to update exactly one block
 * per request (the old version had fall-through bugs across blocks).
 */
declare(strict_types=1);

require __DIR__ . '/../../connection.php';           // db(), constants
require __DIR__ . '/../../backend/helpers/auth.php';
require_login();

$back = PAGES_URL . '/Information.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . $back);
    exit;
}

$pdo     = db();
$section = $_POST['section'] ?? '';

/** UPDATE the single row of a one-row content table (create it if missing). */
$setSingle = static function (string $table, array $cols) use ($pdo): void {
    $set  = implode(', ', array_map(fn ($c) => "`$c` = ?", array_keys($cols)));
    $vals = array_values($cols);
    $affected = $pdo->prepare("UPDATE `$table` SET $set WHERE id = (SELECT MIN(id) FROM (SELECT id FROM `$table`) t)");
    $affected->execute($vals);
    if ($affected->rowCount() === 0 && (int) $pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn() === 0) {
        $names = implode(', ', array_map(fn ($c) => "`$c`", array_keys($cols)));
        $ph    = implode(', ', array_fill(0, count($cols), '?'));
        $pdo->prepare("INSERT INTO `$table` ($names) VALUES ($ph)")->execute($vals);
    }
};

/** Replace all rows of a "list" table with the non-empty lines of a textarea. */
$replaceList = static function (string $table, string $col, string $text) use ($pdo): void {
    $lines = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $text))));
    $pdo->beginTransaction();
    $pdo->exec("DELETE FROM `$table`");
    $ins = $pdo->prepare("INSERT INTO `$table` (`$col`) VALUES (?)");
    foreach ($lines as $line) {
        $ins->execute([$line]);
    }
    $pdo->commit();
};

try {
    switch ($section) {
        case 'introduction': $setSingle('introduction', ['paragraph' => trim($_POST['paragraph'] ?? '')]); break;
        case 'mission':      $setSingle('mission',      ['paragraph' => trim($_POST['paragraph'] ?? '')]); break;
        case 'vision':       $setSingle('vision',       ['paragraph' => trim($_POST['paragraph'] ?? '')]); break;
        case 'history':      $setSingle('history',      ['context'   => trim($_POST['context'] ?? '')]); break;

        case 'map':
            $setSingle('map_statics', [
                'total_land_area' => trim($_POST['total_land_area'] ?? ''),
                'land_used'       => trim($_POST['land_used'] ?? ''),
            ]);
            break;

        case 'statistics':
            $setSingle('statistics', [
                'founding_years'              => trim($_POST['founding_years'] ?? ''),
                'environmental_health_status' => trim($_POST['environmental_health_status'] ?? ''),
                'partnerships_organization'   => trim($_POST['partnerships_organization'] ?? ''),
                'projects_made'               => trim($_POST['projects_made'] ?? ''),
            ]);
            break;

        case 'population':
            $setSingle('population', [
                'number_of_population'   => trim($_POST['number_of_population'] ?? ''),
                'average_household_size' => trim($_POST['average_household_size'] ?? ''),
            ]);
            break;

        case 'economics': $replaceList('economics',      'message',       $_POST['economics'] ?? ''); break;
        case 'business':  $replaceList('major_business',  'business_text', $_POST['business_text'] ?? ''); break;
        case 'income':    $replaceList('major_income',    'income_text',   $_POST['income_text'] ?? ''); break;

        default:
            http_response_code(400);
            exit('Unknown section.');
    }

    header('Location: ' . $back . '?saved=' . urlencode($section));
    exit;
} catch (Throwable $ex) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('information_update.php: ' . $ex->getMessage());
    exit('Could not save changes. Please try again.');
}
