<?php
if (!defined('ABSPATH')) {
    exit;
}

// Enregistrement des menus
function elkair_register_menus() {
    register_nav_menus(array(
        'primary' => __('Menu Principal', 'elkair'),
        'footer' => __('Menu Footer', 'elkair')
    ));
}
add_action('init', 'elkair_register_menus');

// Ajout du support des fonctionnalités WordPress
function elkair_theme_support() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo', array(
        'height' => 60,
        'width' => 200,
        'flex-height' => true,
        'flex-width' => true
    ));
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption'
    ));
}
add_action('after_setup_theme', 'elkair_theme_support');

// Enregistrement des widgets
function elkair_widgets_init() {
    register_sidebar(array(
        'name' => __('Sidebar Principal', 'elkair'),
        'id' => 'sidebar-1',
        'description' => __('Ajoutez vos widgets ici', 'elkair'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>'
    ));

    register_sidebar(array(
        'name' => __('Footer Widget 1', 'elkair'),
        'id' => 'footer-1',
        'before_widget' => '<div class="footer-widget">',
        'after_widget' => '</div>',
        'before_title' => '<h3>',
        'after_title' => '</h3>'
    ));

    register_sidebar(array(
        'name' => __('Footer Widget 2', 'elkair'),
        'id' => 'footer-2',
        'before_widget' => '<div class="footer-widget">',
        'after_widget' => '</div>',
        'before_title' => '<h3>',
        'after_title' => '</h3>'
    ));
}
add_action('widgets_init', 'elkair_widgets_init');

// Enregistrement des scripts et styles
function elkair_enqueue_scripts() {
    // Styles
    wp_enqueue_style('elkair-style', get_stylesheet_uri());
    wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
    
    // Scripts
    wp_enqueue_script('elkair-navigation', get_template_directory_uri() . '/js/navigation.js', array('jquery'), '1.0', true);
    
    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}
add_action('wp_enqueue_scripts', 'elkair_enqueue_scripts');

// Styles personnalisés pour le formulaire de réservation
function elkair_booking_styles() {
    wp_enqueue_style('elkair-booking-styles', get_template_directory_uri() . '/assets/css/booking.css', array(), '1.0.0');
}
add_action('wp_enqueue_scripts', 'elkair_booking_styles');

// Personnalisation du thème
function elkair_customize_register($wp_customize) {
    // Section Hero
    $wp_customize->add_section('elkair_hero_section', array(
        'title' => __('Section Hero', 'elkair'),
        'priority' => 30
    ));

    // Image de fond Hero
    $wp_customize->add_setting('hero_background', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw'
    ));

    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'hero_background', array(
        'label' => __('Image de fond Hero', 'elkair'),
        'section' => 'elkair_hero_section',
        'settings' => 'hero_background'
    )));

    // Titre Hero
    $wp_customize->add_setting('hero_title', array(
        'default' => __('Bienvenue à Elkhair Studio', 'elkair'),
        'sanitize_callback' => 'sanitize_text_field'
    ));

    $wp_customize->add_control('hero_title', array(
        'label' => __('Titre Hero', 'elkair'),
        'section' => 'elkair_hero_section',
        'type' => 'text'
    ));

    // Texte Hero
    $wp_customize->add_setting('hero_text', array(
        'default' => __('Découvrez le windsurf avec nos cours personnalisés', 'elkair'),
        'sanitize_callback' => 'sanitize_textarea_field'
    ));

    $wp_customize->add_control('hero_text', array(
        'label' => __('Texte Hero', 'elkair'),
        'section' => 'elkair_hero_section',
        'type' => 'textarea'
    ));
}
add_action('customize_register', 'elkair_customize_register');

// Intégration avec le plugin Booking Manager
function elkair_check_booking_manager() {
    if (!is_plugin_active('booking-manager/booking-manager.php')) {
        add_action('admin_notices', 'elkair_booking_manager_notice');
    }
}
add_action('admin_init', 'elkair_check_booking_manager');

function elkair_booking_manager_notice() {
    ?>
    <div class="notice notice-warning is-dismissible">
        <p><?php _e('Le thème Elkair fonctionne mieux avec le plugin Booking Manager. Veuillez l\'installer et l\'activer.', 'elkair'); ?></p>
    </div>
    <?php
}

// Shortcode pour afficher la section de réservation
function elkair_booking_section_shortcode() {
    ob_start();
    ?>
    <div class="booking-section">
        <div class="booking-container">
            <h2><?php _e('Réserver un cours', 'elkair'); ?></h2>
            <?php 
            if (shortcode_exists('booking_form')) {
                echo do_shortcode('[booking_form]');
            } else {
                _e('Le plugin Booking Manager n\'est pas activé.', 'elkair');
            }
            ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('elkair_booking_section', 'elkair_booking_section_shortcode');
