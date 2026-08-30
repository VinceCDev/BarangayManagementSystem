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
$shell_class = 'auth-shell--wide';
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

<!-- step indicator -->
<div class="reg-steps" id="regSteps">
    <span class="on" data-dot="1"><b>1</b> Your details</span>
    <span data-dot="2"><b>2</b> Password</span>
</div>

<form method="post" action="<?= action_url('register_resident.php') ?>" id="regForm" novalidate>

    <!-- Step 1 -->
    <div class="reg-step" data-step="1">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">First name</label>
                <div class="field"><input class="form-control" name="firstname" value="<?= $v('firstname') ?>" required></div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Last name</label>
                <div class="field"><input class="form-control" name="lastname" value="<?= $v('lastname') ?>" required></div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Middle name <span class="text-caption">(optional)</span></label>
                <div class="field"><input class="form-control" name="middlename" value="<?= $v('middlename') ?>"></div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Contact number</label>
                <div class="field has-icon">
                    <i class="bi bi-telephone"></i>
                    <input type="tel" class="form-control" name="contact" value="<?= $v('contact') ?>" placeholder="09xx xxx xxxx">
                </div>
            </div>
            <div class="col-12">
                <label class="form-label">Email</label>
                <div class="field has-icon">
                    <i class="bi bi-envelope"></i>
                    <input type="email" class="form-control" name="email" value="<?= $v('email') ?>" placeholder="you@example.com" required>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-end mt-3">
            <button type="button" class="btn-auth" style="height:46px;width:auto;padding:0 24px" onclick="regNext()">
                Next <i class="bi bi-arrow-right ms-1"></i>
            </button>
        </div>
    </div>

    <!-- Step 2 -->
    <div class="reg-step" data-step="2" hidden>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Password</label>
                <div class="field has-icon">
                    <i class="bi bi-lock"></i>
                    <input type="password" class="form-control" id="pw" name="password" minlength="8" placeholder="At least 8 characters" required>
                    <button class="toggle" type="button" tabindex="-1" onclick="const p=document.getElementById('pw');p.type=p.type==='password'?'text':'password';this.querySelector('i').className=p.type==='password'?'bi bi-eye':'bi bi-eye-slash'"><i class="bi bi-eye"></i></button>
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Confirm password</label>
                <div class="field has-icon">
                    <i class="bi bi-lock-fill"></i>
                    <input type="password" class="form-control" id="pw2" name="confirmPassword" minlength="8" placeholder="Re-enter your password" required>
                </div>
            </div>
            <div class="col-12">
                <div class="form-text">At least 8 characters, with a letter, a number and a symbol.</div>
                <label class="form-check mt-2 mb-0">
                    <input class="form-check-input" type="checkbox" name="terms" value="1" required>
                    <span class="form-check-label">I agree to the barangay's terms and data-privacy notice.</span>
                </label>
            </div>
        </div>
        <div class="d-flex justify-content-between mt-3">
            <button type="button" class="btn btn-outline-secondary" onclick="regBack()"><i class="bi bi-arrow-left me-1"></i>Back</button>
            <button type="submit" class="btn-auth" style="height:46px;width:auto;padding:0 24px"><i class="bi bi-person-plus me-1"></i>Create account</button>
        </div>
    </div>
</form>

<p class="foot-note"><a href="<?= home_url() ?>"><i class="bi bi-arrow-left me-1"></i>Back to public site</a></p>

<?php
$foot_extra = <<<'HTML'
<script>
const regForm = document.getElementById('regForm');
const regStepEls = regForm.querySelectorAll('.reg-step');
const regDots = document.querySelectorAll('#regSteps span');

function regShow(n) {
    regStepEls.forEach(s => s.hidden = (+s.dataset.step !== n));
    regDots.forEach(d => d.classList.toggle('on', +d.dataset.dot <= n));
}
function regBack() { regShow(1); }

// Enter inside a step-1 field should advance, not try to submit the form
// (submitting would silently fail on the hidden required password field).
regForm.querySelectorAll('.reg-step[data-step="1"] input').forEach(function (i) {
    i.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') { e.preventDefault(); regNext(); }
    });
});

function regNext() {
    // validate only the visible step-1 fields
    const step1 = regForm.querySelector('.reg-step[data-step="1"]');
    let ok = true;
    step1.querySelectorAll('input[required]').forEach(i => { if (!i.reportValidity()) ok = false; });
    if (!ok) return;
    const email = regForm.querySelector('[name=email]').value.trim();
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        Swal.fire({ icon: 'warning', title: 'Check your email', text: 'Please enter a valid e-mail address.' });
        return;
    }
    regShow(2);
    document.getElementById('pw').focus();
}

regForm.addEventListener('submit', function (e) {
    const a = document.getElementById('pw').value, b = document.getElementById('pw2').value;
    const strong = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&#._-]).{8,}$/.test(a);
    if (!strong) { e.preventDefault(); Swal.fire({icon:'warning', title:'Weak password',
        text:'Use at least 8 characters with a letter, a number and a symbol.'}); return; }
    if (a !== b) { e.preventDefault(); Swal.fire({icon:'error', title:'Passwords do not match'}); }
});
</script>
HTML;
require __DIR__ . '/../partials/auth_bottom.php';
