<?php get_header(); ?>

<main class="site-main">
    <!-- Hero Section -->
    <section class="hero" style="background-image: url('<?php echo esc_url(get_theme_mod('hero_background')); ?>');">
        <div class="hero-content">
            <h1><?php echo esc_html(get_theme_mod('hero_title', __('Bienvenue à Elkhair Studio', 'elkair'))); ?></h1>
            <p><?php echo esc_html(get_theme_mod('hero_text', __('Découvrez le windsurf avec nos cours personnalisés', 'elkair'))); ?></p>
            <a href="#booking" class="button"><?php _e('Réserver maintenant', 'elkair'); ?></a>
        </div>
    </section>

    <!-- Services Section -->
    <section class="services">
        <div class="services-grid">
            <div class="service-card">
                <img src="<?php echo get_template_directory_uri(); ?>/images/beginner.jpg" alt="Cours débutant">
                <h3><?php _e('Cours Débutant', 'elkair'); ?></h3>
                <p><?php _e('Découvrez les bases du windsurf avec nos instructeurs expérimentés.', 'elkair'); ?></p>
            </div>
            <div class="service-card">
                <img src="<?php echo get_template_directory_uri(); ?>/images/intermediate.jpg" alt="Cours intermédiaire">
                <h3><?php _e('Cours Intermédiaire', 'elkair'); ?></h3>
                <p><?php _e('Perfectionnez votre technique et développez vos compétences.', 'elkair'); ?></p>
            </div>
            <div class="service-card">
                <img src="<?php echo get_template_directory_uri(); ?>/images/advanced.jpg" alt="Cours avancé">
                <h3><?php _e('Cours Avancé', 'elkair'); ?></h3>
                <p><?php _e('Maîtrisez les techniques avancées et relevez de nouveaux défis.', 'elkair'); ?></p>
            </div>
        </div>
    </section>

    <!-- Booking Section -->
    <section id="booking" class="booking-section">
        <div class="booking-container">
            <h2><?php _e('Réserver un cours', 'elkair'); ?></h2>
            <?php echo do_shortcode('[booking_form]'); ?>
        </div>
    </section>

    <!-- About Section -->
    <section class="about-section">
        <div class="about-container">
            <div class="about-content">
                <h2><?php _e('À propos d\'Elkhair Studio', 'elkair'); ?></h2>
                <?php
                $about_page = get_page_by_title('À propos');
                if ($about_page) {
                    $content = apply_filters('the_content', $about_page->post_content);
                    echo wp_trim_words($content, 100, '...');
                }
                ?>
                <a href="<?php echo get_permalink(get_page_by_title('À propos')); ?>" class="button"><?php _e('En savoir plus', 'elkair'); ?></a>
            </div>
        </div>
    </section>
</main>

<?php get_footer(); ?>
