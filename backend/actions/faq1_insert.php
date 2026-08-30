<?php
/**
 * faq1_insert.php — add a FAQ entry.
 * Expects POST: question, answer, date. Redirects back to BarangayFAQ.php.
 */
declare(strict_types=1);

require __DIR__ . '/../connection.php';           // $conn, db()
require __DIR__ . '/../../backend/helpers/auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . PAGES_URL . '/BarangayFAQ.php');
    exit;
}

$question = trim($_POST['question'] ?? $_POST['Question'] ?? '');
$answer   = trim($_POST['answer'] ?? $_POST['StartofTerm'] ?? '');
$date     = $_POST['date'] ?? date('Y-m-d');

if ($question === '' || $answer === '') {
    exit('Question and answer are required.');
}

try {
    $stmt = db()->prepare('INSERT INTO faq (question, answer, date) VALUES (?, ?, ?)');
    $stmt->execute([$question, $answer, $date]);
    header('Location: ' . PAGES_URL . '/BarangayFAQ.php');
    exit;
} catch (Throwable $e) {
    error_log('faq1_insert.php: ' . $e->getMessage());
    exit('Could not save the FAQ. Please try again.');
}
