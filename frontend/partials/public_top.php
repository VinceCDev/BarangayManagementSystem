<?php
/**
 * partials/public_top.php — header + sticky navbar for the public site.
 *
 *   $page_title  string
 *   $active      string  one of: home | info | history | maps | photos |
 *                                services | faq | contact
 *   $head_extra  string  optional raw <head> HTML
 */
$page_title = $page_title ?? 'Barangay Paule 1';
$active     = $active     ?? '';
$head_extra = $head_extra ?? '';
$is = fn(string $k) => $active === $k ? 'active' : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Official portal of Barangay Paule 1, Rizal, Laguna — barangay information, services, certificate requests and contact details.">
    <title><?= e($page_title) ?> · Barangay Paule 1</title>
    <link rel="icon" href="<?= asset('images/logo1.png') ?>" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?= asset('css/app.css') ?>" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?= $head_extra ?>
</head>
<body>

<nav class="navbar navbar-expand-lg site-nav">
    <div class="container">
        <a class="navbar-brand" href="<?= page_url('index.php') ?>">
            <img src="<?= asset('images/logo1.png') ?>" alt="Barangay Paule 1 seal">
            <span>Barangay Paule 1</span>
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#pubnav">
            <i class="bi bi-list fs-3"></i>
        </button>
        <div class="collapse navbar-collapse" id="pubnav">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
                <li class="nav-item"><a class="nav-link <?= $is('home') ?>" href="<?= page_url('index.php') ?>">Home</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?= $is('info') . $is('history') . $is('maps') . $is('photos') ?>"
                       href="#" data-bs-toggle="dropdown">Our Barangay</a>
                    <ul class="dropdown-menu shadow">
                        <li><a class="dropdown-item" href="<?= page_url('GeneralInformation.php') ?>">General Information</a></li>
                        <li><a class="dropdown-item" href="<?= page_url('History.php') ?>">History</a></li>
                        <li><a class="dropdown-item" href="<?= page_url('Maps.php') ?>">Maps</a></li>
                        <li><a class="dropdown-item" href="<?= page_url('Photos.php') ?>">Photo Album</a></li>
                    </ul>
                </li>
                <li class="nav-item"><a class="nav-link <?= $is('services') ?>" href="<?= page_url('Certificate.php') ?>">Services</a></li>
                <li class="nav-item"><a class="nav-link <?= $is('faq') ?>" href="<?= page_url('FAQ.php') ?>">FAQ</a></li>
                <li class="nav-item"><a class="nav-link <?= $is('contact') ?>" href="<?= page_url('Contact.php') ?>">Contact</a></li>
                <li class="nav-item ms-lg-2">
                    <a class="btn btn-primary btn-sm px-3" href="<?= page_url('Login.php') ?>">
                        <i class="bi bi-box-arrow-in-right me-1"></i>Staff Login
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
