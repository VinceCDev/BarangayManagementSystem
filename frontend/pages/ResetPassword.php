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

$page_title = 'Reset password';
require __DIR__ . '/../partials/auth_top.php';
?>

<h1>Create new password</h1>
<p class="sub">for <strong style="color:var(--a-text)"><?= e($email) ?></strong></p>

<form method="post" action="<?= action_url('update_password.php') ?>" id="resetForm" novalidate>
    <input type="hidden" name="email" value="<?= e($email) ?>">

    <div class="field has-icon">
        <i class="bi bi-lock"></i>
        <input type="password" class="form-control" id="password" name="password"
               placeholder="New password" minlength="8" required>
    </div>
    <div class="field has-icon">
        <i class="bi bi-lock-fill"></i>
        <input type="password" class="form-control" id="confirmPassword" name="confirmPassword"
               placeholder="Confirm new password" minlength="8" required>
    </div>
    <p class="sub" style="margin:-4px 0 16px">At least 8 characters with a letter, a number and a symbol (@ $ ! % * ? &amp;).</p>

    <button type="submit" class="btn-auth">Reset password</button>
</form>

<p class="foot-note"><a href="<?= page_url('Login.php') ?>"><i class="bi bi-arrow-left me-1"></i>Back to sign in</a></p>

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
