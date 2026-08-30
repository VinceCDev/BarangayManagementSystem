<?php
/**
 * ForgotPassword.php — enter the account email; if it exists, continue to
 * ResetPassword.php.
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

$page_title = 'Forgot password';
require __DIR__ . '/../partials/auth_top.php';
?>

<h1>Forgot password</h1>
<p class="sub">Enter the email on your account and we’ll take you to the reset step.
    Remembered it? <a href="<?= page_url('Login.php') ?>">Sign in</a>.</p>

<?php if ($error): ?>
    <div class="mb-3 px-3 py-2 rounded" style="background:rgba(192,57,43,.15);color:#f0a9a1;border:1px solid rgba(192,57,43,.35);font-size:.85rem">
        <?= e($error) ?>
    </div>
<?php endif; ?>

<form method="post">
    <div class="field has-icon">
        <i class="bi bi-envelope"></i>
        <input type="text" class="form-control" name="userName" placeholder="Account email" required autofocus>
    </div>
    <button type="submit" class="btn-auth">Continue</button>
</form>

<p class="foot-note"><a href="<?= page_url('Login.php') ?>"><i class="bi bi-arrow-left me-1"></i>Back to sign in</a></p>

<?php require __DIR__ . '/../partials/auth_bottom.php'; ?>
