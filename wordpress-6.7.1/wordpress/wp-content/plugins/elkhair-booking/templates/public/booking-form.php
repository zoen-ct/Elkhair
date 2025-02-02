<?php
if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;
$availability_table = $wpdb->prefix . 'elkhair_availability';
$bookings_table = $wpdb->prefix . 'elkhair_bookings';

// Récupérer toutes les dates avec leur statut
$available_dates = $wpdb->get_results("SELECT date, status FROM $availability_table");
$available_dates_array = array();
foreach ($available_dates as $date) {
    $available_dates_array[$date->date] = $date->status;
}

// Récupérer les dates réservées
$booked_dates = $wpdb->get_col("SELECT DISTINCT booking_date FROM $bookings_table");
?>
<div class="elkhair-booking-form">
    <form id="booking-form">
        <div class="form-group">
            <label for="booking-name"><?php echo esc_html__('Nom', 'elkhair-booking'); ?></label>
            <input type="text" id="booking-name" name="name" required>
        </div>
        
        <div class="form-group">
            <label for="booking-email"><?php echo esc_html__('Email', 'elkhair-booking'); ?></label>
            <input type="email" id="booking-email" name="email" required>
        </div>
        
        <div class="form-group">
            <label for="booking-date"><?php echo esc_html__('Date', 'elkhair-booking'); ?></label>
            <input type="text" id="booking-date" name="date" readonly required>
        </div>
        
        <div class="availability-legend">
            <p>
                <span class="available"></span> <?php echo esc_html__('Disponible', 'elkhair-booking'); ?>
                <span class="unavailable"></span> <?php echo esc_html__('Non disponible', 'elkhair-booking'); ?>
                <span class="booked"></span> <?php echo esc_html__('Déjà réservé', 'elkhair-booking'); ?>
            </p>
        </div>
        
        <button type="submit" class="button"><?php echo esc_html__('Réserver', 'elkhair-booking'); ?></button>
    </form>
</div>

<script>
jQuery(document).ready(function($) {
    var availableDates = <?php echo json_encode($available_dates_array); ?>;
    var bookedDates = <?php echo json_encode($booked_dates); ?>;
    
    $('#booking-date').datepicker({
        dateFormat: 'yy-mm-dd',
        firstDay: 1,
        minDate: 0,
        beforeShowDay: function(date) {
            var dateString = $.datepicker.formatDate('yy-mm-dd', date);
            
            if (bookedDates.indexOf(dateString) !== -1) {
                return [false, 'booked', 'Déjà réservé'];
            }
            
            var status = availableDates[dateString] || 'unavailable';
            var isSelectable = status === 'available';
            
            return [isSelectable, status, isSelectable ? 'Disponible' : 'Non disponible'];
        }
    });

    $('#booking-form').on('submit', function(e) {
        e.preventDefault();
        
        var formData = {
            action: 'make_booking',
            nonce: elkhairBooking.nonce,
            name: $('#booking-name').val(),
            email: $('#booking-email').val(),
            date: $('#booking-date').val()
        };

        $.ajax({
            url: elkhairBooking.ajaxurl,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    alert('<?php echo esc_js(__('Réservation effectuée avec succès !', 'elkhair-booking')); ?>');
                    $('#booking-form')[0].reset();
                    location.reload();
                } else {
                    alert(response.data);
                }
            }
        });
    });
});
</script>
