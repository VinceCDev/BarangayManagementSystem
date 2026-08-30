<?php
/**
 * _404.php — "page not found" view, rendered by the front controller when a
 * route does not resolve. Kept dependency-light on purpose.
 */
require __DIR__ . '/../partials/bootstrap.php';

$page_title = 'Page not found';
$active     = '';
require __DIR__ . '/../partials/public_top.php';
?>

<section class="section">
    <div class="container" style="max-width:560px;text-align:center">
        <p class="section__eyebrow">Error 404</p>
        <h1 class="section__title">We couldn't find that page</h1>
        <p class="text-muted-2">
            The address may have changed or the page no longer exists.
            Check the link, or head back to a known place.
        </p>
        <div class="d-flex gap-2 justify-content-center mt-4">
            <a href="<?= home_url() ?>" class="btn btn-primary">Go to the home page</a>
            <a href="<?= page_url('Login.php') ?>" class="btn btn-outline-secondary">Sign in</a>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../partials/public_bottom.php'; ?>
