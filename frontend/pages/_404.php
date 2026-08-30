<?php
/**
 * _404.php — "page not found" view, rendered by the front controller when a
 * route does not resolve. Uses the public shell (nav + footer) so a lost
 * visitor still has a way back.
 */
require __DIR__ . '/../partials/bootstrap.php';

http_response_code(404);

$page_title = 'Page not found';
$active     = '';
$head_extra = <<<CSS
<style>
  .nf { position: relative; overflow: hidden; padding: clamp(3rem, 8vw, 6rem) 0; }
  .nf::before {
      content: ""; position: absolute; inset: 0; z-index: 0; pointer-events: none;
      background:
        radial-gradient(38rem 38rem at 12% -10%, rgba(28,95,214,.10), transparent 60%),
        radial-gradient(32rem 32rem at 108% 116%, rgba(11,47,110,.12), transparent 60%);
  }
  .nf__inner { position: relative; z-index: 1; max-width: 640px; margin-inline: auto; text-align: center; }
  .nf__code {
      font-weight: 800; line-height: .9; letter-spacing: -.04em;
      font-size: clamp(5.5rem, 20vw, 11rem);
      background: linear-gradient(160deg, var(--brand-500), var(--brand-800));
      -webkit-background-clip: text; background-clip: text; color: transparent;
      margin-bottom: .5rem;
  }
  .nf__badge {
      display: inline-flex; align-items: center; gap: .5rem;
      padding: .3rem .8rem; border-radius: var(--radius-pill);
      background: var(--brand-50); color: var(--brand-700);
      font-size: .8rem; font-weight: 600; letter-spacing: .02em;
      margin-bottom: 1.25rem;
  }
  .nf__title { font-size: clamp(1.4rem, 4vw, 2rem); margin-bottom: .6rem; }
  .nf__text { color: var(--muted); font-size: 1.02rem; margin-bottom: 1.75rem; }
  .nf__links {
      display: flex; flex-wrap: wrap; gap: .6rem 1.5rem; justify-content: center;
      margin-top: 2.25rem; padding-top: 1.5rem; border-top: 1px solid var(--line);
  }
  .nf__links a { display: inline-flex; align-items: center; gap: .4rem; font-weight: 500; }
</style>
CSS;

require __DIR__ . '/../partials/public_top.php';
?>

<section class="nf">
    <div class="container">
        <div class="nf__inner">
            <span class="nf__badge"><i class="bi bi-compass"></i>Error 404</span>
            <div class="nf__code" aria-hidden="true">404</div>
            <h1 class="nf__title">We couldn&rsquo;t find that page</h1>
            <p class="nf__text">
                The address may have changed, or the page no longer exists.
                Check the link and try again, or pick up from one of the places below.
            </p>

            <div class="d-flex gap-2 justify-content-center flex-wrap">
                <a href="<?= home_url() ?>" class="btn btn-primary btn-lg">
                    <i class="bi bi-house-door me-1"></i>Back to the home page
                </a>
                <a href="<?= page_url('Login.php') ?>" class="btn btn-outline-secondary btn-lg">
                    <i class="bi bi-box-arrow-in-right me-1"></i>Sign in
                </a>
            </div>

            <nav class="nf__links" aria-label="Helpful links">
                <a href="<?= page_url('GeneralInformation.php') ?>"><i class="bi bi-info-circle"></i>Barangay information</a>
                <a href="<?= page_url('Certificate.php') ?>"><i class="bi bi-file-earmark-text"></i>Request a document</a>
                <a href="<?= page_url('FAQ.php') ?>"><i class="bi bi-question-circle"></i>FAQ</a>
                <a href="<?= page_url('Contact.php') ?>"><i class="bi bi-envelope"></i>Contact us</a>
            </nav>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../partials/public_bottom.php'; ?>
