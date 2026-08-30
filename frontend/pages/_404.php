<?php
/**
 * _404.php — standalone "page not found" screen.
 * No site header or footer: a centred card with the barangay seal, a big
 * 404, a short message and a "go back" action (browser history, falling
 * back to the home page). Rendered by the front controller when a route
 * does not resolve.
 */
require __DIR__ . '/../partials/bootstrap.php';

http_response_code(404);

$home = home_url();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>Page not found · Barangay Paule 1</title>
    <link rel="icon" href="<?= asset('images/logo1.png') ?>" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="<?= asset('css/app.css') ?>" rel="stylesheet">
    <style>
        .nf {
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            padding: clamp(16px, 5vw, 48px);
            background:
                radial-gradient(44rem 44rem at 4% -12%,  rgba(28, 95, 214, .55), transparent 62%),
                radial-gradient(40rem 40rem at 106% 114%, rgba(11, 47, 110, .60), transparent 60%),
                radial-gradient(28rem 28rem at 96% -6%,   rgba(10, 162, 192, .35), transparent 60%),
                linear-gradient(160deg, #c3d8f6 0%, #a9c6f0 45%, #cadcf5 100%);
        }
        .nf__card {
            width: min(560px, 100%);
            text-align: center;
            background: var(--surface);
            border: 1px solid rgba(255, 255, 255, .7);
            border-radius: 24px;
            padding: clamp(34px, 6vw, 56px) clamp(24px, 5vw, 52px);
            box-shadow: 0 34px 80px rgba(11, 32, 74, .30), 0 10px 26px rgba(11, 32, 74, .16);
        }
        .nf__logo { width: 76px; height: 76px; object-fit: contain; margin-bottom: 1.25rem; }
        .nf__code {
            font-weight: 800; line-height: 1; letter-spacing: -.05em;
            font-size: clamp(5rem, 20vw, 8.5rem);
            background: linear-gradient(180deg, var(--brand-500), var(--brand-700));
            -webkit-background-clip: text; background-clip: text; color: transparent;
        }
        .nf__rule {
            width: min(260px, 70%); height: 2px; margin: .35rem auto 1.6rem;
            border: 0;
            background: linear-gradient(90deg, transparent, var(--brand-200), transparent);
        }
        .nf__title {
            font-size: clamp(1.4rem, 4vw, 1.9rem); font-weight: 800;
            color: var(--ink); letter-spacing: -.01em; margin: 0 0 .6rem;
        }
        .nf__text {
            color: var(--muted); font-size: 1rem; line-height: 1.6;
            max-width: 42ch; margin: 0 auto 2rem;
        }
        .nf__btn {
            display: inline-flex; align-items: center; gap: .6rem;
            padding: .8rem 1.8rem; border-radius: var(--radius-pill);
            font-weight: 700; font-size: .95rem; text-decoration: none;
            color: #fff; border: 0; cursor: pointer;
            background: linear-gradient(135deg, var(--brand-500), var(--brand-700));
            box-shadow: 0 12px 26px rgba(28, 95, 214, .38);
            transition: transform .12s ease, box-shadow .12s ease;
        }
        .nf__btn:hover { transform: translateY(-1px); box-shadow: 0 16px 32px rgba(28, 95, 214, .46); }
        .nf__btn:active { transform: translateY(0); }
        .nf__home {
            display: block; margin-top: 1.1rem;
            font-size: .85rem; font-weight: 500; color: var(--muted); text-decoration: none;
        }
        .nf__home:hover { color: var(--brand-600); }
    </style>
</head>
<body>
    <main class="nf">
        <div class="nf__card">
            <img class="nf__logo" src="<?= asset('images/logo1.png') ?>" alt="Barangay Paule 1 seal">
            <div class="nf__code" aria-hidden="true">404</div>
            <hr class="nf__rule">
            <h1 class="nf__title">This page took a wrong turn</h1>
            <p class="nf__text">
                The page you&rsquo;re looking for doesn&rsquo;t exist or may have moved.
                Let&rsquo;s get you back on track.
            </p>
            <button type="button" class="nf__btn" onclick="nfBack()">
                <i class="bi bi-arrow-left"></i> Go back
            </button>
            <a class="nf__home" href="<?= e($home) ?>">Return to the home page</a>
        </div>
    </main>
    <script>
        function nfBack() {
            if (history.length > 1) {
                history.back();
            } else {
                location.href = <?= json_encode($home) ?>;
            }
        }
    </script>
</body>
</html>
