<?php
function elkhair_studios_enqueue_styles() {
    wp_enqueue_style('elkhair-studios-style', get_stylesheet_uri());
    wp_enqueue_style('elkhair-studios-app', get_template_directory_uri() . '/assets/style/app.css');
}
add_action('wp_enqueue_scripts', 'elkhair_studios_enqueue_styles');

function add_active_class_to_nav_menu($classes, $item) {
    // Vérifie si le lien est actif
    if (in_array('current-menu-item', $classes) || in_array('current-page-item', $classes)) {
        $classes[] = 'active';
    }
    return $classes;
}
add_filter('nav_menu_css_class', 'add_active_class_to_nav_menu', 10, 2);
