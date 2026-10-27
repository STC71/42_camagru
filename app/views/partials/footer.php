        </div>
    </main>

    <footer class="site-footer">
        <div class="container">
            <p class="footer-legend">
                <?php
                if (function_exists('__')) {
                    echo __('footer.project_legend');
                } else {
                    echo 'Camagru es un proyecto Outer Core implementado en 42 Málaga por sternero — Octubre 2026.';
                }
                ?>
            </p>
        </div>
    </footer>

    <script src="<?php echo rtrim(
        (class_exists('Config') ? Config::get('APP_URL', 'http://localhost:8080') : null)
            ?: ($_ENV['APP_URL'] ?? getenv('APP_URL') ?: 'http://localhost:8080'),
        '/'
    ); ?>/js/main.js"></script>
</body>
</html>
