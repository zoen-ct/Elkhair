<?php
class Elkhair_Booking_Activator {
    public static function activate() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'elkhair_availabilities';
        
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            date DATE NOT NULL,
            status ENUM('available', 'unavailable') NOT NULL DEFAULT 'unavailable',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY date (date)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}