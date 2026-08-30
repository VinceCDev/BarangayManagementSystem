<?php
/**
 * ResetPassword.php — set a new password for the account identified by
 * ?userName=. Posts to backend/actions/update_password.php.
 */
require __DIR__ . '/../partials/bootstrap.php';

$email = trim($_GET['userName'] ?? $_GET['email'] ?? '');
if ($email === '') {
    header('Location: ' . page_url('ForgotPassword.php'));
    exit;
}

$page_title  = 'Reset password';
$aside_title = 'Choose a strong password.';
$aside_text  = 'At least 8 characters with a mix of letters, a number and a symbol.';
require __DIR__ . '/../partials/auth_top.php';
?>

<h1 class="mb-1">Create new password</h1>
<p class="text-muted-2 mb-4">for <strong><?= e($email) ?></strong></p>

<form method="post" action="<?= action_url('update_password.php') ?>" id="resetForm" novalidate>
    <input type="hidden" name="email" value="<?= e($email) ?>">

    <label class="form-label" for="password">New password</label>
    <div class="input-group mb-3">
        <span class="input-group-text"><i class="bi bi-lock"></i></span>
        <input type="password" class="form-control" id="password" name="password" minlength="8" required>
    </div>

    <label class="form-label" for="confirmPassword">Confirm new password</label>
    <div class="input-group mb-2">
        <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
        <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" minlength="8" required>
    </div>
    <div class="form-text mb-3">Use at least 8 characters, including a letter, a number and a symbol (@ $ ! % * ? &amp;).</div>

    <button type="submit" class="btn btn-primary w-100 py-2">Reset password</button>
</form>

<p class="text-center small text-muted-2 mt-4 mb-0">
    <a href="<?= page_url('Login.php') ?>"><i class="bi bi-arrow-left me-1"></i>Back to sign in</a>
</p>

<?php
$foot_extra = <<<'HTML'
<script>
document.getElementById('resetForm').addEventListener('submit', function (e) {
    const a = document.getElementById('password').value;
    const b = document.getElementById('confirmPassword').value;
    const strong = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/.test(a);
    if (!strong) { e.preventDefault(); Swal.fire({icon:'warning', title:'Weak password',
        text:'Use at least 8 characters with a letter, a number and a symbol.'}); return; }
    if (a !== b) { e.preventDefault(); Swal.fire({icon:'error', title:'Passwords do not match'}); }
});
</script>
HTML;
require __DIR__ . '/../partials/auth_bottom.php';
