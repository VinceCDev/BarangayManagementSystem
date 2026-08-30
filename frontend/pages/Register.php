<?php
/**
 * Register.php — public resident sign-up.
 * Creates a users row (userType = resident) + a profiledata row, then signs
 * the person in and sends them to the resident portal.
 * Posts to backend/actions/register_resident.php.
 */
require __DIR__ . '/../partials/bootstrap.php';

// Already signed in? Go where you belong.
if (current_username()) {
    header('Location: ' . page_url(current_role() === 'resident' ? 'ResidentDashboard.php' : 'AdminDashboard.php'));
    exit;
}

$err = $_GET['err'] ?? '';
$errText = [
    'missing'   => 'Please fill in all required fields.',
    'email'     => 'Please enter a valid e-mail address.',
    'exists'    => 'An account with that e-mail already exists. Try signing in.',
    'weakpass'  => 'Password must be at least 8 characters and include a letter, a number and a symbol.',
    'mismatch'  => 'The two passwords do not match.',
    'terms'     => 'You must agree to the terms to create an account.',
    'server'    => 'Something went wrong. Please try again.',
][$err] ?? '';

$old = $_SESSION['reg_old'] ?? [];
unset($_SESSION['reg_old']);
$v = fn(string $k) => e($old[$k] ?? '');

$page_title  = 'Create an account';
$aside_title = 'Join the barangay portal.';
$aside_text  = 'Create a resident account to request documents, message the barangay and track everything in one place.';
require __DIR__ . '/../partials/auth_top.php';
?>

<h1>Create an account</h1>
<p class="sub">Already have one? <a href="<?= page_url('Login.php') ?>">Sign in</a>.</p>

<?php if ($errText): ?>
    <div class="mb-3 px-3 py-2 rounded" style="background:rgba(192,57,43,.12);color:#c0392b;border:1px solid rgba(192,57,43,.30);font-size:.85rem">
        <?= e($errText) ?>
    </div>
<?php endif; ?>

<form method="post" action="<?= action_url('register_resident.php') ?>" id="regForm" novalidate>
    <div class="row g-2">
        <div class="col-6">
            <label class="form-label">First name</label>
            <input class="form-control" name="firstname" value="<?= $v('firstname') ?>" required>
        </div>
        <div class="col-6">
            <label class="form-label">Last name</label>
            <input class="form-control" name="lastname" value="<?= $v('lastname') ?>" required>
        </div>
    </div>

    <label class="form-label">Middle name <span class="text-caption">(optional)</span></label>
    <div class="field mb-0"><input class="form-control" name="middlename" value="<?= $v('middlename') ?>"></div>

    <label class="form-label">Email</label>
    <div class="field has-icon">
        <i class="bi bi-envelope"></i>
        <input type="email" class="form-control" name="email" value="<?= $v('email') ?>" placeholder="you@example.com" required>
    </div>

    <label class="form-label">Contact number</label>
    <div class="field has-icon">
        <i class="bi bi-telephone"></i>
        <input type="tel" class="form-control" name="contact" value="<?= $v('contact') ?>" placeholder="09xx xxx xxxx">
    </div>

    <label class="form-label">Password</label>
    <div class="field has-icon">
        <i class="bi bi-lock"></i>
        <input type="password" class="form-control" id="pw" name="password" minlength="8" placeholder="At least 8 characters" required>
        <button class="toggle" type="button" tabindex="-1" onclick="const p=document.getElementById('pw');p.type=p.type==='password'?'text':'password';this.querySelector('i').className=p.type==='password'?'bi bi-eye':'bi bi-eye-slash'"><i class="bi bi-eye"></i></button>
    </div>

    <label class="form-label">Confirm password</label>
    <div class="field has-icon">
        <i class="bi bi-lock-fill"></i>
        <input type="password" class="form-control" id="pw2" name="confirmPassword" minlength="8" placeholder="Re-enter your password" required>
    </div>

    <label class="form-check mt-2">
        <input class="form-check-input" type="checkbox" name="terms" value="1" required>
        <span class="form-check-label">I agree to the barangay's terms and data-privacy notice.</span>
    </label>

    <button type="submit" class="btn-auth mt-3" style="height:46px"><i class="bi bi-person-plus me-1"></i>Create account</button>
</form>

<p class="foot-note"><a href="<?= home_url() ?>"><i class="bi bi-arrow-left me-1"></i>Back to public site</a></p>

<?php
$foot_extra = <<<'HTML'
<script>
document.getElementById('regForm').addEventListener('submit', function (e) {
    const a = document.getElementById('pw').value, b = document.getElementById('pw2').value;
    const strong = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&#._-]).{8,}$/.test(a);
    if (!strong) { e.preventDefault(); Swal.fire({icon:'warning', title:'Weak password',
        text:'Use at least 8 characters with a letter, a number and a symbol.'}); return; }
    if (a !== b) { e.preventDefault(); Swal.fire({icon:'error', title:'Passwords do not match'}); }
});
</script>
HTML;
require __DIR__ . '/../partials/auth_bottom.php';
