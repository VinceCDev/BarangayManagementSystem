<?php
/** partials/auth_bottom.php — closes auth_top.php. $foot_extra optional. */
$foot_extra   = $foot_extra   ?? '';
$visual_lines = $visual_lines ?? [];
?>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Rotate the caption on the visual card.
(function () {
    var lines = <?= json_encode(array_values($visual_lines)) ?>;
    if (lines.length < 2) return;
    var cap = document.getElementById('authCaption');
    var dots = document.querySelectorAll('#authDots span');
    var i = 0;
    setInterval(function () {
        i = (i + 1) % lines.length;
        cap.style.opacity = 0;
        setTimeout(function () { cap.textContent = lines[i]; cap.style.opacity = 1; }, 200);
        dots.forEach(function (d, j) { d.classList.toggle('on', j === i); });
    }, 4000);
    cap.style.transition = 'opacity .2s ease';
})();
</script>
<?= $foot_extra ?>
</body>
</html>
