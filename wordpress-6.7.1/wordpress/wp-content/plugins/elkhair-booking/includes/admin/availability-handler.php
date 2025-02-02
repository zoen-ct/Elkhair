<?php
if (!defined('ABSPATH')) {
    exit;
}

function elkhair_booking_get_available_dates() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'elkhair_availabilities';
    
    return $wpdb->get_col($wpdb->prepare(
        "SELECT DATE_FORMAT(date, '%Y-%m-%d') FROM $table_name WHERE status = %s",
        'available'
    ));
}

// Ajax handler pour vérifier les disponibilités
add_action('wp_ajax_check_availability', 'elkhair_booking_check_availability');
add_action('wp_ajax_nopriv_check_availability', 'elkhair_booking_check_availability');

function elkhair_booking_check_availability() {
    check_ajax_referer('elkhair-booking-nonce', 'nonce');
    wp_send_json_success(array(
        'available_dates' => elkhair_booking_get_available_dates()
    ));
}

// Ajax handler pour sauvegarder les disponibilités
add_action('wp_ajax_save_availability', 'elkhair_booking_save_availability');
function elkhair_booking_save_availability() {
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Permission denied');
    }

    check_ajax_referer('elkhair-booking-nonce', 'nonce');

    global $wpdb;
    $table_name = $wpdb->prefix . 'elkhair_availabilities';
    
    // Récupérer et nettoyer les dates
    $dates = array();
    if (isset($_POST['dates']) && is_array($_POST['dates'])) {
        foreach ($_POST['dates'] as $date) {
            $clean_date = sanitize_text_field($date);
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $clean_date)) {
                $dates[] = $clean_date;
            }
        }
    }
    
    // Commencer la transaction
    $wpdb->query('START TRANSACTION');
    
    try {
        // Supprimer toutes les anciennes dates
        $wpdb->query($wpdb->prepare(
            "DELETE FROM $table_name WHERE status = %s",
            'available'
        ));
        
        // Insérer les nouvelles dates
        foreach ($dates as $date) {
            $wpdb->insert(
                $table_name,
                array(
                    'date' => $date,
                    'status' => 'available',
                    'created_at' => current_time('mysql')
                ),
                array('%s', '%s', '%s')
            );
        }
        
        $wpdb->query('COMMIT');
        
        wp_send_json_success(array(
            'message' => 'Disponibilités mises à jour avec succès',
            'available_dates' => elkhair_booking_get_available_dates()
        ));
        
    } catch (Exception $e) {
        $wpdb->query('ROLLBACK');
        wp_send_json_error('Erreur lors de la mise à jour des disponibilités');
    }
}
