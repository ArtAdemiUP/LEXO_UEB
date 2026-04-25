    </main><!-- /.site-main -->

    <!-- FOOTER -->
    <footer class="site-footer">
        <div class="footer-inner">
            <div class="footer-brand">
                <span class="logo-mark small">L</span>
                <strong>LEXO</strong> — Forumi Shqiptar i Librave
            </div>
            <div class="footer-links">
                <a href="<?= BASE_URL ?>/pages/home.php">Kryefaqja</a>
                <a href="<?= BASE_URL ?>/pages/categories.php">Kategoritë</a>
                <?php if (isLoggedIn()): ?>
                    <a href="<?= BASE_URL ?>/pages/profile.php">Profili</a>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>/pages/login.php">Kyçu</a>
                <?php endif; ?>
            </div>
            <div class="footer-meta">
                <span>© <?= date('Y') ?> LEXO Forum</span>
                <span class="separator">·</span>
                <span>Ndërtuar me PHP</span>
                <?php if (isLoggedIn()): ?>
                    <span class="separator">·</span>
                    <span>Kyçur si <strong><?= e($_SESSION['username']) ?></strong></span>
                <?php endif; ?>
            </div>
        </div>
    </footer>

</div><!-- /.site-wrapper -->

<script src="<?= BASE_URL ?>/assets/js/main.js"></script>
</body>
</html>
