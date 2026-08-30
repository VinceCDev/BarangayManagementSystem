<?php
/**
 * contact_insert.php — public "Contact us" form handler.
 * Stores the message and (best effort) emails the barangay inbox.
 * POST: name, age, email, contact, message
 */
declare(strict_types=1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as MailException;

require __DIR__ . '/../../connection.php';                 // db(), constants
$config = require __DIR__ . '/../config/config.php';

// Return to Contact.php by default, or MyMessages.php when a resident sends
// from their portal (whitelisted — never trust an arbitrary redirect target).
$return = in_array($_POST['_return'] ?? '', ['MyMessages.php', 'Contact.php'], true)
    ? $_POST['_return'] : 'Contact.php';
$back = PAGES_URL . '/' . $return;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . $back);
    exit;
}

$name    = trim($_POST['name'] ?? '');
$age     = ($_POST['age'] ?? '') === '' ? null : (int) $_POST['age'];
$email   = trim($_POST['email'] ?? '');
$contact = trim($_POST['contact'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($name === '' || $email === '' || $message === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: ' . $back . '?sent=invalid');
    exit;
}

try {
    $stmt = db()->prepare(
        'INSERT INTO receivemessages (name, age, email, contact, message) VALUES (?, ?, ?, ?, ?)'
    );
    $stmt->execute([$name, $age, $email, $contact, $message]);
} catch (Throwable $e) {
    error_log('contact_insert.php (db): ' . $e->getMessage());
    header('Location: ' . $back . '?sent=error');
    exit;
}

// Best-effort e-mail notification — never block the user if SMTP fails.
try {
    require_once __DIR__ . '/../lib/PHPmailer/src/Exception.php';
    require_once __DIR__ . '/../lib/PHPmailer/src/PHPMailer.php';
    require_once __DIR__ . '/../lib/PHPmailer/src/SMTP.php';

    $m = $config['mail'];
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = $m['host'];
    $mail->SMTPAuth   = true;
    $mail->Username   = $m['username'];
    $mail->Password   = $m['password'];
    $mail->SMTPSecure = $m['encryption'];
    $mail->Port       = (int) $m['port'];
    $mail->setFrom($m['from_email'], $m['from_name']);
    $mail->addAddress($m['to_email']);
    $mail->addReplyTo($email, $name);
    $mail->isHTML(true);
    $mail->Subject = 'New message from the barangay contact form';
    $mail->Body    = nl2br(htmlspecialchars(
        "Name: $name\nAge: " . ($age ?? '—') . "\nEmail: $email\nContact: $contact\n\n$message",
        ENT_QUOTES
    ));
    $mail->send();
} catch (Throwable $e) {
    error_log('contact_insert.php (mail): ' . $e->getMessage());
}

header('Location: ' . $back . '?sent=ok');
exit;
