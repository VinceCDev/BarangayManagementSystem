<?php
/**
 * important_info_insert.php — profile setup step 2.
 * Inserts an importantinfo row, then continues to step 3.
 */
declare(strict_types=1);

require __DIR__ . '/../connection.php';           // db(), constants

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . PAGES_URL . '/Other Info.php');
    exit;
}

$fields = [
    'address'              => trim($_POST['address'] ?? ''),
    'barangay'             => trim($_POST['barangay'] ?? ''),
    'city'                 => trim($_POST['city'] ?? ''),
    'province'             => trim($_POST['province'] ?? ''),
    'occupation'           => trim($_POST['occupation'] ?? ''),
    'monthly_income'       => trim($_POST['monthly_income'] ?? ''),
    'number_of_years'      => trim($_POST['number_of_years'] ?? ''),
    'number_household'     => trim($_POST['number_household'] ?? ''),
    'allergies_conditions' => trim($_POST['allergies_conditions'] ?? ''),
    'education'            => trim($_POST['education'] ?? ''),
];

try {
    $cols = implode(', ', array_keys($fields));
    $ph   = implode(', ', array_fill(0, count($fields), '?'));
    db()->prepare("INSERT INTO importantinfo ($cols) VALUES ($ph)")->execute(array_values($fields));

    header('Location: ' . PAGES_URL . '/Proof of Identity.php');
    exit;
} catch (Throwable $e) {
    error_log('important_info_insert.php: ' . $e->getMessage());
    exit('Could not save your information. Please try again.');
}
