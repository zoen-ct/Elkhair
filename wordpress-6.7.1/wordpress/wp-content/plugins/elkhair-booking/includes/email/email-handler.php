<?php
if (!defined('ABSPATH')) {
    exit;
}

add_action('elkhair_booking_send_confirmation_email', 'elkhair_booking_send_confirmation_email', 10, 3);

function elkhair_booking_send_confirmation_email($email, $name, $date) {
    $to = $email;
    $subject = __('Confirmation de votre réservation - Elkhair Studios', 'elkhair-booking');
    
    $formatted_date = date_i18n(get_option('date_format'), strtotime($date));
    
    $message = sprintf(
        __('Bonjour %s,

Nous vous confirmons votre réservation pour le %s.

Merci de votre confiance !

Cordialement,
L\'équipe Elkhair Studios', 'elkhair-booking'),
        $name,
        $formatted_date
    );
    
    $headers = array('Content-Type: text/html; charset=UTF-8');
    
    wp_mail($to, $subject, nl2br($message), $headers);
}
