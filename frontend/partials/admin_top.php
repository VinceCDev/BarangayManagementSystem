<?php
/**
 * partials/admin_top.php — opens an admin page: <head>, sidebar, topbar,
 * and the <main class="app-content"> wrapper. Close it with admin_bottom.php.
 *
 * Expected variables (all optional except title):
 *   $page_title    string  browser tab + topbar
 *   $page_heading  string  big H1 in the page header  (defaults to title)
 *   $page_subtitle string  small line under the H1
 *   $page_actions  string  raw HTML for buttons on the right of the header
 *   $active_nav    string  nav key to highlight
 *   $head_extra    string  raw HTML injected into <head> (page CSS / meta)
 */

$page_title    = $page_title    ?? 'Barangay Paule 1';
$page_heading  = $page_heading  ?? $page_title;
$page_subtitle = $page_subtitle ?? '';
$page_actions  = $page_actions  ?? '';
$active_nav    = $active_nav    ?? '';
$head_extra    = $head_extra    ?? '';
require_once __DIR__ . '/nav.php';                // defines nav_for_role()
$nav           = nav_for_role(current_role());   // menu filtered by role
$user          = current_user_card();
$dash_url      = page_url(current_role() === 'resident' ? 'ResidentDashboard.php' : 'AdminDashboard.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Barangay Paule 1 Management System — administration panel for resident records, blotter, certificates and barangay information.">
    <title><?= e($page_title) ?> · Barangay Paule 1</title>
    <link rel="icon" href="<?= asset('images/logo1.png') ?>" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?= asset('css/app.css') ?>" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?= $head_extra ?>
</head>
<body class="app">

<div class="app-scrim" onclick="document.body.classList.remove('nav-open')"></div>

<aside class="app-sidebar" id="appSidebar">
    <div class="app-sidebar__brand">
        <img src="<?= asset('images/logo1.png') ?>" alt="Barangay Paule 1 seal">
        <span>
            <b>Barangay Paule 1</b>
            <span><?= e(role_label()) ?></span>
        </span>
    </div>
    <nav class="app-nav" aria-label="Main">
        <?php foreach ($nav as $section => $items): ?>
            <div class="app-nav__label"><?= e($section) ?></div>
            <?php foreach ($items as $item): ?>
                <a href="<?= page_url($item['file']) ?>"
                   class="<?= $active_nav === $item['key'] ? 'is-active' : '' ?>">
                    <i class="bi <?= e($item['icon']) ?>"></i>
                    <span><?= e($item['label']) ?></span>
                </a>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </nav>
</aside>

<header class="app-topbar">
    <button class="app-hamburger" type="button" aria-label="Toggle menu"
            onclick="document.body.classList.toggle('nav-open')">
        <i class="bi bi-list"></i>
    </button>
    <span class="app-topbar__title"><?= e($page_heading) ?></span>
    <span class="app-topbar__spacer"></span>
    <div class="app-topbar__user dropdown">
        <a href="#" class="d-flex align-items-center gap-2 text-decoration-none dropdown-toggle"
           data-bs-toggle="dropdown" aria-expanded="false">
            <img src="<?= e($user['avatar']) ?>" alt="" class="avatar"
                 onerror="this.src='<?= asset('images/logo1.png') ?>'">
            <span class="d-none d-sm-block text-start lh-sm">
                <span class="fw-semibold text-ink d-block"><?= e($user['name']) ?></span>
                <small><?= e($user['role']) ?></small>
            </span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end shadow">
            <li><a class="dropdown-item" href="<?= page_url('UserProfile.php') ?>"><i class="bi bi-person me-2"></i>My Profile</a></li>
            <li><a class="dropdown-item" href="<?= page_url('ForgotPassword.php') ?>"><i class="bi bi-key me-2"></i>Reset Password</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger" href="?logout=1" onclick="return confirmLogout(event)"><i class="bi bi-box-arrow-right me-2"></i>Sign Out</a></li>
        </ul>
    </div>
</header>

<main class="app-content">
  <div class="container-inner">
    <div class="page-head">
        <div class="page-head__text">
            <div class="crumb">
                <a href="<?= $dash_url ?>">Dashboard</a>
                <?php if ($active_nav !== 'dashboard'): ?> <span class="mx-1">/</span> <?= e($page_heading) ?><?php endif; ?>
            </div>
            <h1><?= e($page_heading) ?></h1>
            <?php if ($page_subtitle): ?><p><?= e($page_subtitle) ?></p><?php endif; ?>
        </div>
        <?php if ($page_actions): ?><div class="page-head__actions"><?= $page_actions ?></div><?php endif; ?>
    </div>
