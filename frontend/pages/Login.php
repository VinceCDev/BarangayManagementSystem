<?php
/**
 * Login.php — staff sign-in.
 * Submits to backend/actions/login_test.php via fetch(); expects {success:bool}.
 */
require __DIR__ . '/../partials/bootstrap.php';

$page_title = 'Sign in';
$rememberedUser = isset($_COOKIE['remembered_username']) ? e($_COOKIE['remembered_username']) : '';

require __DIR__ . '/../partials/auth_top.php';
?>

<h1>Welcome back</h1>
<p class="sub">Sign in to your account.
    New resident? <a href="<?= page_url('Register.php') ?>">Create an account</a>.</p>

<form id="loginForm" novalidate>
    <div class="field has-icon">
        <i class="bi bi-person"></i>
        <input type="text" class="form-control" id="username" name="username"
               placeholder="Username or email" autocomplete="username" required value="<?= $rememberedUser ?>">
    </div>

    <div class="field has-icon">
        <i class="bi bi-lock"></i>
        <input type="password" class="form-control" id="password" name="password"
               placeholder="Password" autocomplete="current-password" required style="padding-right:44px">
        <button class="toggle" type="button" id="togglePw" tabindex="-1" aria-label="Show password">
            <i class="bi bi-eye"></i>
        </button>
    </div>

    <div class="d-flex align-items-center justify-content-between mt-1 mb-3">
        <label class="form-check mb-0">
            <input class="form-check-input" type="checkbox" name="remember" id="remember"
                   <?= $rememberedUser ? 'checked' : '' ?>>
            <span class="form-check-label">Remember me</span>
        </label>
        <a href="<?= page_url('ForgotPassword.php') ?>" style="color:var(--a-accent);font-size:.85rem;font-weight:600;text-decoration:none">Forgot password?</a>
    </div>

    <button type="submit" class="btn-auth"><i class="bi bi-box-arrow-in-right"></i> Sign in</button>
</form>

<p class="foot-note"><a href="<?= home_url() ?>"><i class="bi bi-arrow-left me-1"></i>Back to public site</a></p>

<?php
$loginAction = action_url('login_test.php');
$dashUrl     = page_url('AdminDashboard.php');
$foot_extra = <<<HTML
<script>
const form = document.getElementById('loginForm');
const pw   = document.getElementById('password');
document.getElementById('togglePw').addEventListener('click', function () {
    const show = pw.type === 'password';
    pw.type = show ? 'text' : 'password';
    this.querySelector('i').className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
});

form.addEventListener('submit', function (e) {
    e.preventDefault();
    if (!form.checkValidity()) { form.reportValidity(); return; }

    Swal.fire({ title: 'Signing in…', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

    fetch('{$loginAction}', { method: 'POST', body: new FormData(form) })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                Swal.fire({ icon: 'success', title: 'Welcome!', timer: 900, showConfirmButton: false })
                    .then(() => location.href = '{$dashUrl}');
            } else {
                Swal.fire({ icon: 'error', title: 'Sign in failed',
                            text: data.error || 'Invalid username or password. Please try again.' });
            }
        })
        .catch(() => Swal.fire({ icon: 'error', title: 'Something went wrong',
                                 text: 'Please try again in a moment.' }));
});
</script>
HTML;

require __DIR__ . '/../partials/auth_bottom.php';
