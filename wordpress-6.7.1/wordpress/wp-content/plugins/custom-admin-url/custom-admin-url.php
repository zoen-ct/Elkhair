<?php
/*
Plugin Name: Custom Admin URL
Description: Change the WordPress admin URL for better security
Version: 1.0
Author: Your Name
*/

// Empêcher l'accès direct au fichier
if (!defined('ABSPATH')) {
    exit;
}

// Rediriger wp-admin vers la nouvelle URL
function custom_admin_url($url) {
    return str_replace('wp-admin', 'zozo-le-dozo', $url);
}
add_filter('admin_url', 'custom_admin_url', 10, 1);
add_filter('network_admin_url', 'custom_admin_url', 10, 1);
add_filter('site_url', 'custom_admin_url', 10, 1);

// Gérer la redirection pour la nouvelle URL
function custom_admin_redirect() {
    if (strpos($_SERVER['REQUEST_URI'], 'wp-admin') !== false && !is_admin()) {
        wp_redirect(home_url('zozo-le-dozo'), 301);
        exit();
    }
}
add_action('init', 'custom_admin_redirect');
