<?php
/**
 * partials/auth_top.php — split-screen layout for Login / Forgot / Reset.
 *
 *   $page_title  string
 *   $aside_title string   headline on the brand panel
 *   $aside_text  string   supporting line
 */
$page_title  = $page_title  ?? 'Sign in';
$aside_title = $aside_title ?? 'Your gateway to barangay services.';
$aside_text  = $aside_text  ?? 'Manage resident records, blotter reports, certificate requests and barangay information in one place.';
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
<body>
<div class="auth">
    <aside class="auth__aside">
        <div class="brand">
            <img src="<?= asset('images/logo1.png') ?>" alt="Barangay Paule 1 seal">
            <span>Barangay Paule 1<small>Rizal, Laguna · Official Portal</small></span>
        </div>

        <div class="d-flex flex-column gap-4">
            <div class="auth__feature">
                <i class="bi bi-person-vcard"></i>
                <div><b>Resident records</b><span>Household and personal information, kept up to date.</span></div>
            </div>
            <div class="auth__feature">
                <i class="bi bi-journal-text"></i>
                <div><b>Blotter &amp; incident reports</b><span>File and track complaints from one place.</span></div>
            </div>
            <div class="auth__feature">
                <i class="bi bi-award"></i>
                <div><b>Certificate requests</b><span>Generate clearances and certificates as PDFs.</span></div>
            </div>
        </div>

        <div class="headline">
            <h2><?= e($aside_title) ?></h2>
            <p><?= e($aside_text) ?></p>
        </div>

        <p class="copyright mb-0">&copy; <?= date('Y') ?> Barangay Paule 1. All rights reserved.</p>
    </aside>

    <main class="auth__main">
        <div class="auth__card">
            <img src="<?= asset('images/logo1.png') ?>" alt="" class="auth__logo">
