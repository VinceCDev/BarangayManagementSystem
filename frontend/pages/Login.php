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
<p class="sub">Sign in to the management console.
    New here? <a href="<?= page_url('Contact.php') ?>">Contact your administrator</a>.</p>

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

<div class="divider">Or continue with</div>
<div class="socials">
    <button type="button" class="social" onclick="ssoUnavailable()">
        <svg viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg"><path fill="#EA4335" d="M24 9.5c3.5 0 6.6 1.2 9 3.6l6.7-6.7C35.6 2.4 30.2 0 24 0 14.6 0 6.5 5.4 2.6 13.2l7.8 6.1C12.3 13.3 17.6 9.5 24 9.5z"/><path fill="#4285F4" d="M46.5 24.5c0-1.6-.1-3.1-.4-4.5H24v9h12.7c-.5 3-2.2 5.5-4.7 7.2l7.3 5.7C43.8 37.7 46.5 31.6 46.5 24.5z"/><path fill="#FBBC05" d="M10.4 28.7c-.5-1.4-.8-2.9-.8-4.7s.3-3.3.8-4.7l-7.8-6.1C.9 16.4 0 20.1 0 24s.9 7.6 2.6 10.8l7.8-6.1z"/><path fill="#34A853" d="M24 48c6.2 0 11.5-2 15.3-5.6l-7.3-5.7c-2 1.4-4.7 2.3-8 2.3-6.4 0-11.7-3.8-13.6-9.3l-7.8 6.1C6.5 42.6 14.6 48 24 48z"/></svg>
        Google
    </button>
    <button type="button" class="social" onclick="ssoUnavailable()">
        <i class="bi bi-apple"></i> Apple
    </button>
</div>

<p class="foot-note"><a href="<?= page_url('index.php') ?>"><i class="bi bi-arrow-left me-1"></i>Back to public site</a></p>

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

function ssoUnavailable() {
    Swal.fire({ icon: 'info', title: 'Not available',
        text: 'Single sign-on is not configured for this system. Please use your username and password.' });
}

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
