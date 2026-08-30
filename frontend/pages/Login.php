<?php
/**
 * Login.php — staff sign-in.
 * Submits to backend/actions/login_test.php via fetch(); expects {success:bool}.
 */
require __DIR__ . '/../partials/bootstrap.php';

$page_title  = 'Sign in';
$aside_title = 'Your gateway to the barangay community.';
$aside_text  = 'One place to manage resident records, blotter reports, certificate requests and barangay information.';

$rememberedUser = isset($_COOKIE['remembered_username']) ? e($_COOKIE['remembered_username']) : '';

require __DIR__ . '/../partials/auth_top.php';
?>

<h1 class="mb-1">Welcome back</h1>
<p class="text-muted-2 mb-4">Sign in to the management console.</p>

<form id="loginForm" novalidate>
    <label class="form-label" for="username">Username</label>
    <div class="input-group mb-1">
        <span class="input-group-text"><i class="bi bi-person"></i></span>
        <input type="text" class="form-control" id="username" name="username"
               autocomplete="username" required value="<?= $rememberedUser ?>">
    </div>

    <label class="form-label" for="password">Password</label>
    <div class="input-group mb-2">
        <span class="input-group-text"><i class="bi bi-lock"></i></span>
        <input type="password" class="form-control" id="password" name="password"
               autocomplete="current-password" required>
        <button class="btn btn-outline-secondary" type="button" id="togglePw" tabindex="-1" aria-label="Show password">
            <i class="bi bi-eye"></i>
        </button>
    </div>

    <div class="d-flex align-items-center justify-content-between my-3">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="remember" id="remember"
                   <?= $rememberedUser ? 'checked' : '' ?>>
            <label class="form-check-label" for="remember">Remember me</label>
        </div>
        <a href="<?= page_url('ForgotPassword.php') ?>" class="small">Forgot password?</a>
    </div>

    <button type="submit" class="btn btn-primary w-100 py-2">
        <i class="bi bi-box-arrow-in-right me-1"></i> Sign in
    </button>
</form>

<p class="text-center small text-muted-2 mt-4 mb-0">
    <a href="<?= page_url('index.php') ?>"><i class="bi bi-arrow-left me-1"></i>Back to public site</a>
</p>

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
    if (!form.checkValidity()) { form.classList.add('was-validated'); return; }

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
