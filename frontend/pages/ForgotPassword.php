<?php
/**
 * ForgotPassword.php — enter the account email; if it exists, continue to
 * ResetPassword.php. (No e-mail delivery is configured for this flow.)
 */
require __DIR__ . '/../partials/bootstrap.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['userName'] ?? '');
    if ($email === '') {
        $error = 'Please enter your account email.';
    } else {
        $st = db()->prepare('SELECT 1 FROM users WHERE userName = ? LIMIT 1');
        $st->execute([$email]);
        if ($st->fetchColumn()) {
            header('Location: ' . page_url('ResetPassword.php') . '?userName=' . urlencode($email));
            exit;
        }
        $error = 'That email address is not associated with any account.';
    }
}

$page_title  = 'Forgot password';
$aside_title = 'Trouble signing in?';
$aside_text  = 'Enter the email address on your account and we will take you to the password reset step.';
require __DIR__ . '/../partials/auth_top.php';
?>

<h1 class="mb-1">Forgot password</h1>
<p class="text-muted-2 mb-4">We’ll help you get back into your account.</p>

<?php if ($error): ?>
    <div class="alert alert-danger py-2"><?= e($error) ?></div>
<?php endif; ?>

<form method="post">
    <label class="form-label" for="userName">Account email</label>
    <div class="input-group mb-3">
        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
        <input type="text" class="form-control" id="userName" name="userName" required autofocus>
    </div>
    <button type="submit" class="btn btn-primary w-100 py-2">Continue</button>
</form>

<p class="text-center small text-muted-2 mt-4 mb-0">
    <a href="<?= page_url('Login.php') ?>"><i class="bi bi-arrow-left me-1"></i>Back to sign in</a>
</p>

<?php require __DIR__ . '/../partials/auth_bottom.php'; ?>
