<?php
/**
 * partials/auth_top.php — dark, image-left card layout for Login / Forgot / Reset.
 *
 *   $page_title   string
 *   $visual_lines string[]  rotating captions on the image (defaults provided)
 */
$page_title    = $page_title    ?? 'Sign in';
$visual_lines  = $visual_lines  ?? [
    'Serving Barangay Paule 1',
    'One community, one portal',
    'Records, services, and more',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Barangay Paule 1 Management System — sign in.">
    <title><?= e($page_title) ?> · Barangay Paule 1</title>
    <link rel="icon" href="<?= asset('images/logo1.png') ?>" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?= asset('css/app.css') ?>" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="auth-page">
<div class="auth-shell">

    <aside class="auth-visual">
        <img class="auth-visual__img" src="<?= asset('images/cover.jpeg') ?>" alt="Barangay Paule 1">
        <div class="auth-visual__top">
            <span class="auth-visual__logo">
                <img src="<?= asset('images/logo1.png') ?>" alt="">Barangay Paule 1
            </span>
            <a class="auth-visual__back" href="<?= page_url('index.php') ?>">
                Back to website <i class="bi bi-arrow-up-right"></i>
            </a>
        </div>
        <div class="auth-visual__caption">
            <h3 id="authCaption"><?= e($visual_lines[0]) ?></h3>
            <div class="auth-visual__dots" id="authDots">
                <?php foreach ($visual_lines as $i => $_): ?>
                    <span class="<?= $i === 0 ? 'on' : '' ?>"></span>
                <?php endforeach; ?>
            </div>
        </div>
    </aside>

    <main class="auth-form">
        <img src="<?= asset('images/logo1.png') ?>" alt="" class="auth-form__logo">
