<?php
/**
 * _404.php — standalone "page not found" screen.
 * No site header or footer: just a centred error state with a "go back"
 * action (browser history, falling back to the home page).
 * Rendered by the front controller when a route does not resolve.
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?= asset('css/app.css') ?>" rel="stylesheet">
    <style>
        .nf {
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            padding: clamp(16px, 5vw, 48px);
            background:
                radial-gradient(34rem 34rem at 8% -10%, rgba(28,95,214,.10), transparent 60%),
                radial-gradient(30rem 30rem at 110% 115%, rgba(11,47,110,.12), transparent 60%),
                var(--bg);
        }
        .nf__card {
            width: min(460px, 100%);
            text-align: center;
        }
        .nf__code {
            font-weight: 800; line-height: 1; letter-spacing: -.04em;
            font-size: clamp(4.5rem, 16vw, 7rem);
            background: linear-gradient(160deg, var(--brand-500), var(--brand-800));
            -webkit-background-clip: text; background-clip: text; color: transparent;
        }
        .nf__title { font-size: 1.35rem; font-weight: 650; color: var(--ink); margin: .35rem 0 .5rem; }
        .nf__text  { color: var(--muted); font-size: .95rem; margin: 0 0 1.75rem; }
        .nf__actions { display: flex; gap: .6rem; justify-content: center; flex-wrap: wrap; }
        .nf__btn {
            display: inline-flex; align-items: center; gap: .45rem;
            padding: .62rem 1.25rem; border-radius: 11px;
            font-weight: 600; font-size: .92rem; text-decoration: none;
            border: 1px solid transparent; cursor: pointer;
        }
        .nf__btn--primary { background: var(--brand-500); color: #fff; }
        .nf__btn--primary:hover { background: var(--brand-600); }
        .nf__btn--ghost { background: var(--surface); color: var(--ink); border-color: var(--line); }
        .nf__btn--ghost:hover { background: var(--surface-2); }
    </style>
</head>
<body>
    <main class="nf">
        <div class="nf__card">
            <div class="nf__code" aria-hidden="true">404</div>
            <h1 class="nf__title">Page not found</h1>
            <p class="nf__text">
                The page you&rsquo;re looking for doesn&rsquo;t exist or may have moved.
            </p>
            <div class="nf__actions">
                <button type="button" class="nf__btn nf__btn--primary" onclick="nfBack()">
                    <i class="bi bi-arrow-left"></i>Go back
                </button>
                <a href="<?= e($home) ?>" class="nf__btn nf__btn--ghost">
                    <i class="bi bi-house-door"></i>Home page
                </a>
            </div>
        </div>
    </main>
    <script>
        function nfBack() {
            if (document.referrer && document.referrer !== location.href) {
                history.back();
            } else if (history.length > 1) {
                history.back();
            } else {
                location.href = <?= json_encode($home) ?>;
            }
        }
    </script>
</body>
</html>
