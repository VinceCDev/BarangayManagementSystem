<?php
/**
 * personal_data_insert.php — profile setup step 1.
 * Inserts a profiledata row, then continues to step 2.
 */
declare(strict_types=1);

require __DIR__ . '/../../connection.php';           // db(), constants

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . PAGES_URL . '/Personal Data.php');
    exit;
}

$fields = [
    'firstname'         => trim($_POST['firstname'] ?? ''),
    'middlename'        => trim($_POST['middlename'] ?? ''),
    'lastname'          => trim($_POST['lastname'] ?? ''),
    'gender'            => trim($_POST['gender'] ?? ''),
    'birthdate'         => $_POST['birthdate'] ?? null,
    'email'             => trim($_POST['email'] ?? ''),
    'contact'           => trim($_POST['contact'] ?? ''),
    'religion'          => trim($_POST['religion'] ?? ''),
    'status'            => trim($_POST['status'] ?? ''),
    'emergency_person'  => trim($_POST['emergency_name'] ?? ''),
    'emergency_contact' => trim($_POST['emergency_contact'] ?? ''),
];

if ($fields['firstname'] === '' || $fields['lastname'] === '' || $fields['email'] === '') {
    exit('First name, last name and email are required.');
}

try {
    $cols = implode(', ', array_keys($fields));
    $ph   = implode(', ', array_fill(0, count($fields), '?'));
    db()->prepare("INSERT INTO profiledata ($cols) VALUES ($ph)")->execute(array_values($fields));

    header('Location: ' . PAGES_URL . '/Other Info.php');
    exit;
} catch (Throwable $e) {
    error_log('personal_data_insert.php: ' . $e->getMessage());
    exit('Could not save your details. Please try again.');
}
