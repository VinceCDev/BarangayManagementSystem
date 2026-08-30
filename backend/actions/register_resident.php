<?php
/**
 * register_resident.php — create a resident account from the public sign-up.
 *   users      : userName = e-mail, password = bcrypt hash, userType = 'resident'
 *   profiledata: firstname / middlename / lastname / email / contact
 * On success the person is signed in and sent to the resident portal.
 */
declare(strict_types=1);

require __DIR__ . '/../connection.php';                 // db(), constants
require __DIR__ . '/../helpers/auth.php';               // session

$reg = PAGES_URL . '/Register.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . $reg);
    exit;
}

$firstname  = trim($_POST['firstname'] ?? '');
$middlename = trim($_POST['middlename'] ?? '');
$lastname   = trim($_POST['lastname'] ?? '');
$email      = trim($_POST['email'] ?? '');
$contact    = trim($_POST['contact'] ?? '');
$password   = (string) ($_POST['password'] ?? '');
$confirm    = (string) ($_POST['confirmPassword'] ?? '');
$terms      = ($_POST['terms'] ?? '') === '1';

// Keep what they typed (minus the passwords) so the form can be re-filled.
$_SESSION['reg_old'] = compact('firstname', 'middlename', 'lastname', 'email', 'contact');

$fail = static function (string $code) use ($reg): never {
    header('Location: ' . $reg . '?err=' . $code);
    exit;
};

if ($firstname === '' || $lastname === '' || $email === '' || $password === '') {
    $fail('missing');
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $fail('email');
}
if (!$terms) {
    $fail('terms');
}
if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&#._-]).{8,}$/', $password)) {
    $fail('weakpass');
}
if ($password !== $confirm) {
    $fail('mismatch');
}

try {
    $pdo = db();

    $st = $pdo->prepare('SELECT 1 FROM users WHERE userName = ? LIMIT 1');
    $st->execute([$email]);
    if ($st->fetchColumn()) {
        $fail('exists');
    }

    $pdo->beginTransaction();

    $pdo->prepare(
        'INSERT INTO users (fullName, userName, password, userType) VALUES (?, ?, ?, ?)'
    )->execute([
        trim("$firstname $lastname"),
        $email,
        password_hash($password, PASSWORD_DEFAULT),
        'resident',
    ]);

    $pdo->prepare(
        'INSERT INTO profiledata (firstname, middlename, lastname, email, contact)
         VALUES (?, ?, ?, ?, ?)'
    )->execute([$firstname, $middlename, $lastname, $email, $contact]);

    $pdo->commit();

    // Sign in and go to the portal.
    unset($_SESSION['reg_old']);
    session_regenerate_id(true);
    $_SESSION['username'] = $email;

    header('Location: ' . PAGES_URL . '/ResidentDashboard.php');
    exit;
} catch (Throwable $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('register_resident.php: ' . $e->getMessage());
    $fail('server');
}
