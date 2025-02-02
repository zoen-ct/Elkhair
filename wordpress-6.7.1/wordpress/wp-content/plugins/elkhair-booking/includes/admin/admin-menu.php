<?php
if (!defined('ABSPATH')) {
    exit;
}

// Ajout du menu d'administration
add_action('admin_menu', 'elkhair_booking_admin_menu');

function elkhair_booking_admin_menu() {
    add_menu_page(
        __('Elkhair Booking', 'elkhair-booking'),
        __('Réservations', 'elkhair-booking'),
        'manage_options',
        'elkhair-booking',
        'elkhair_booking_admin_page',
        'dashicons-calendar-alt',
        30
    );

    add_submenu_page(
        'elkhair-booking',
        __('Disponibilités', 'elkhair-booking'),
        __('Disponibilités', 'elkhair-booking'),
        'manage_options',
        'elkhair-booking-availability',
        'elkhair_booking_availability_page'
    );

    add_submenu_page(
        'elkhair-booking',
        __('Calendrier Mensuel', 'elkhair-booking'),
        __('Calendrier Mensuel', 'elkhair-booking'),
        'manage_options',
        'elkhair-booking-calendar',
        'elkhair_booking_calendar_page'
    );
}

function elkhair_booking_admin_page() {
    include ELKHAIR_BOOKING_PATH . 'templates/admin/bookings-list.php';
}

function elkhair_booking_availability_page() {
    include ELKHAIR_BOOKING_PATH . 'templates/admin/availability-settings.php';
}

function elkhair_booking_calendar_page() {
    include ELKHAIR_BOOKING_PATH . 'templates/admin/monthly-calendar.php';
}
