<footer class="site-footer">
    <div class="footer-content">
        <div class="footer-widget">
            <?php dynamic_sidebar('footer-1'); ?>
        </div>
        <div class="footer-widget">
            <?php dynamic_sidebar('footer-2'); ?>
        </div>
        <div class="footer-widget">
            <h3><?php _e('Contact', 'elkair'); ?></h3>
            <p>
                <?php echo esc_html(get_theme_mod('footer_address', '123 Rue de la Plage')); ?><br>
                <?php echo esc_html(get_theme_mod('footer_phone', '+212 123 456 789')); ?><br>
                <?php echo esc_html(get_theme_mod('footer_email', 'contact@elkhair-studio.com')); ?>
            </p>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="footer-content">
            <p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. <?php _e('Tous droits réservés.', 'elkair'); ?></p>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
