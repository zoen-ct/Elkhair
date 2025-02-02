<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap">
    <h1><?php echo esc_html__('Réservations', 'elkhair-booking'); ?></h1>
    
    <div class="elkhair-booking-list">
        <?php
        global $wpdb;
        $table_name = $wpdb->prefix . 'elkhair_bookings';
        $bookings = $wpdb->get_results("SELECT * FROM $table_name ORDER BY booking_date DESC");
        
        if ($bookings): ?>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php echo esc_html__('Nom', 'elkhair-booking'); ?></th>
                        <th><?php echo esc_html__('Email', 'elkhair-booking'); ?></th>
                        <th><?php echo esc_html__('Date', 'elkhair-booking'); ?></th>
                        <th><?php echo esc_html__('Statut', 'elkhair-booking'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td><?php echo esc_html($booking->name); ?></td>
                            <td><?php echo esc_html($booking->email); ?></td>
                            <td><?php echo esc_html(date_i18n(get_option('date_format'), strtotime($booking->booking_date))); ?></td>
                            <td><?php echo esc_html($booking->status); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p><?php echo esc_html__('Aucune réservation trouvée.', 'elkhair-booking'); ?></p>
        <?php endif; ?>
    </div>
</div>
