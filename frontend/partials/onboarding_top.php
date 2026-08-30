<?php
/**
 * partials/onboarding_top.php — centered card layout for the 3-step
 * profile setup wizard (Personal Data → Other Info → Proof of Identity).
 *
 *   $page_title  string
 *   $step        int     1..3  (highlights the stepper)
 *   $step_title  string
 *   $step_hint   string
 */
$page_title = $page_title ?? 'Profile setup';
$step       = $step       ?? 1;
$step_title = $step_title ?? '';
$step_hint  = $step_hint  ?? '';
$steps = ['Personal data', 'Other information', 'Proof of identity'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($page_title) ?> · Barangay Paule 1</title>
    <link rel="icon" href="<?= asset('images/logo1.png') ?>" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?= asset('css/app.css') ?>" rel="stylesheet">
    <style>
        .wizard { max-width: 760px; margin-inline: auto; padding: var(--sp-6) var(--sp-4) var(--sp-8); }
        .stepper { display: flex; gap: var(--sp-2); margin-bottom: var(--sp-5); }
        .stepper .st { flex: 1; text-align: center; font-size: .8rem; font-weight: 600; color: var(--faint); }
        .stepper .st .dot { height: 4px; border-radius: var(--radius-pill); background: var(--line); margin-bottom: .4rem; }
        .stepper .st.done  { color: var(--brand-600); } .stepper .st.done .dot  { background: var(--brand-200); }
        .stepper .st.now   { color: var(--brand-700); } .stepper .st.now  .dot  { background: var(--brand-500); }
    </style>
</head>
<body>
<nav class="site-nav">
    <div class="container py-2 d-flex align-items-center gap-2">
        <img src="<?= asset('images/logo1.png') ?>" alt="" width="34" height="34">
        <strong>Barangay Paule 1</strong>
        <span class="ms-auto small text-muted-2">Profile setup</span>
    </div>
</nav>

<div class="wizard">
    <div class="stepper">
        <?php foreach ($steps as $i => $label): $n = $i + 1; ?>
            <div class="st <?= $n < $step ? 'done' : ($n === $step ? 'now' : '') ?>">
                <div class="dot"></div>Step <?= $n ?> · <?= e($label) ?>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="card">
        <div class="card-hd"><span class="card-hd__title"><i class="bi bi-person-lines-fill"></i> <?= e($step_title) ?></span></div>
        <div class="card-bd">
            <?php if ($step_hint): ?><p class="text-muted-2"><?= e($step_hint) ?></p><?php endif; ?>
