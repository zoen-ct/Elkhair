<?php
if (!defined('ABSPATH')) {
    exit;
}

// Shortcode pour afficher le calendrier
add_shortcode('elkhair_booking_calendar', 'elkhair_booking_calendar_shortcode');

function elkhair_booking_calendar_shortcode() {
    wp_enqueue_script('jquery-ui-datepicker');
    wp_enqueue_style('jquery-ui-css', 'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css');
    
    ob_start();
    include ELKHAIR_BOOKING_PATH . 'templates/public/booking-form.php';
    return ob_get_clean();
}

// Ajax handler pour la soumission de réservation
add_action('wp_ajax_make_booking', 'elkhair_booking_make_booking');
add_action('wp_ajax_nopriv_make_booking', 'elkhair_booking_make_booking');

function elkhair_booking_make_booking() {
    check_ajax_referer('elkhair-booking-nonce', 'nonce');

    $name = sanitize_text_field($_POST['name']);
    $email = sanitize_email($_POST['email']);
    $date = sanitize_text_field($_POST['date']);

    if (empty($name) || empty($email) || empty($date)) {
        wp_send_json_error('Veuillez remplir tous les champs');
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'elkhair_bookings';

    $result = $wpdb->insert(
        $table_name,
        array(
            'name' => $name,
            'email' => $email,
            'booking_date' => $date,
            'status' => 'pending'
        ),
        array('%s', '%s', '%s', '%s')
    );

    if ($result) {
        // Envoi de l'email de confirmation
        do_action('elkhair_booking_send_confirmation_email', $email, $name, $date);
        wp_send_json_success('Réservation effectuée avec succès');
    } else {
        wp_send_json_error('Erreur lors de la réservation');
    }
}
