<?php
/**
 * Plugin Name: Elkhair Booking
 * Description: Plugin de réservation pour Elkhair Studios
 * Version: 1.0.0
 * Author: Elkhair Studios
 */

// Si ce fichier est appelé directement, on sort
if (!defined('ABSPATH')) {
    exit;
}

// Définir les constantes du plugin
define('ELKHAIR_BOOKING_VERSION', '1.0.0');
define('ELKHAIR_BOOKING_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('ELKHAIR_BOOKING_PLUGIN_URL', plugin_dir_url(__FILE__));

// Inclure le gestionnaire de disponibilités
require_once ELKHAIR_BOOKING_PLUGIN_DIR . 'includes/admin/availability-handler.php';

// Activation du plugin
register_activation_hook(__FILE__, 'elkhair_booking_activate');
function elkhair_booking_activate() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'elkhair_availabilities';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        date date NOT NULL,
        status varchar(20) NOT NULL DEFAULT 'unavailable',
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        UNIQUE KEY date (date)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Désactivation du plugin
register_deactivation_hook(__FILE__, 'elkhair_booking_deactivate');
function elkhair_booking_deactivate() {
    // Vous pouvez ajouter ici du code à exécuter lors de la désactivation
}

// Désinstallation du plugin
register_uninstall_hook(__FILE__, 'elkhair_booking_uninstall');
function elkhair_booking_uninstall() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'elkhair_availabilities';
    $sql = "DROP TABLE IF EXISTS $table_name;";
    $wpdb->query($sql);
}

// Ajouter les scripts et styles nécessaires
add_action('admin_enqueue_scripts', 'elkhair_booking_admin_scripts');
function elkhair_booking_admin_scripts($hook) {
    if ('toplevel_page_elkhair-booking-availability' !== $hook) {
        return;
    }

    wp_enqueue_script('jquery-ui-datepicker');
    wp_enqueue_style('jquery-ui', 'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css');
    
    wp_localize_script('jquery-ui-datepicker', 'elkhairBooking', array(
        'nonce' => wp_create_nonce('elkhair-booking-nonce')
    ));
}

// Ajouter le menu admin
add_action('admin_menu', 'elkhair_booking_admin_menu');
function elkhair_booking_admin_menu() {
    add_menu_page(
        'Réservations',
        'Réservations',
        'manage_options',
        'elkhair-booking-availability',
        'elkhair_booking_availability_page',
        'dashicons-calendar-alt'
    );
}

// Page de gestion des disponibilités
function elkhair_booking_availability_page() {
    require_once ELKHAIR_BOOKING_PLUGIN_DIR . 'templates/admin/availability-calendar.php';
}

// Shortcode pour le calendrier de réservation public
add_shortcode('elkhair_booking_calendar', 'elkhair_booking_calendar_shortcode');
function elkhair_booking_calendar_shortcode($atts) {
    // Enregistrer les scripts nécessaires
    wp_enqueue_script('jquery-ui-datepicker');
    wp_enqueue_style('jquery-ui', 'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css');
    
    wp_localize_script('jquery-ui-datepicker', 'elkhairBooking', array(
        'nonce' => wp_create_nonce('elkhair-booking-nonce'),
        'ajaxurl' => admin_url('admin-ajax.php')
    ));

    // Démarrer la mise en mémoire tampon
    ob_start();
    
    // Inclure le template du calendrier
    require_once ELKHAIR_BOOKING_PLUGIN_DIR . 'templates/public/booking-calendar.php';
    
    // Retourner le contenu
    return ob_get_clean();
}

// Ajouter ajaxurl pour le front-end
add_action('wp_head', 'elkhair_booking_add_ajaxurl');
function elkhair_booking_add_ajaxurl() {
    ?>
    <script type="text/javascript">
    var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
    </script>
    <?php
}