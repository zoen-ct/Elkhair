<?php
if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;
$availability_table = $wpdb->prefix . 'elkhair_availability';
$bookings_table = $wpdb->prefix . 'elkhair_bookings';

// Obtenir le mois et l'année actuels ou sélectionnés
$month = isset($_GET['month']) ? intval($_GET['month']) : intval(date('m'));
$year = isset($_GET['year']) ? intval($_GET['year']) : intval(date('Y'));

// Récupérer les disponibilités pour le mois
$first_day = date('Y-m-01', strtotime("$year-$month-01"));
$last_day = date('Y-m-t', strtotime("$year-$month-01"));

$availabilities = $wpdb->get_results($wpdb->prepare(
    "SELECT date, status FROM $availability_table 
    WHERE date BETWEEN %s AND %s",
    $first_day,
    $last_day
));

$bookings = $wpdb->get_results($wpdb->prepare(
    "SELECT booking_date, name, email FROM $bookings_table 
    WHERE booking_date BETWEEN %s AND %s",
    $first_day,
    $last_day
));

// Créer des tableaux associatifs pour un accès facile
$availability_data = array();
foreach ($availabilities as $availability) {
    $availability_data[$availability->date] = $availability->status;
}

$booking_data = array();
foreach ($bookings as $booking) {
    $booking_data[$booking->booking_date] = array(
        'name' => $booking->name,
        'email' => $booking->email
    );
}

// Navigation entre les mois
$prev_month = $month - 1;
$prev_year = $year;
if ($prev_month < 1) {
    $prev_month = 12;
    $prev_year--;
}

$next_month = $month + 1;
$next_year = $year;
if ($next_month > 12) {
    $next_month = 1;
    $next_year++;
}

// Obtenir le premier jour du mois
$first_day_of_month = date('w', strtotime($first_day));
$days_in_month = date('t', strtotime($first_day));
?>

<div class="wrap">
    <h1><?php echo esc_html__('Gérer les disponibilités', 'elkhair-booking'); ?></h1>
    
    <div class="availability-instructions">
        <p><?php echo esc_html__('Cliquez sur les dates pour les marquer comme disponibles ou indisponibles. Les dates en vert sont disponibles pour les réservations.', 'elkhair-booking'); ?></p>
    </div>

    <div class="calendar-navigation">
        <a href="<?php echo add_query_arg(array('month' => $prev_month, 'year' => $prev_year, 'page' => 'elkhair-booking-availability')); ?>" class="button">
            &larr; <?php echo date_i18n('F Y', strtotime("$prev_year-$prev_month-01")); ?>
        </a>
        <span class="current-month">
            <?php echo date_i18n('F Y', strtotime("$year-$month-01")); ?>
        </span>
        <a href="<?php echo add_query_arg(array('month' => $next_month, 'year' => $next_year, 'page' => 'elkhair-booking-availability')); ?>" class="button">
            <?php echo date_i18n('F Y', strtotime("$next_year-$next_month-01")); ?> &rarr;
        </a>
    </div>

    <div class="availability-calendar">
        <table class="calendar-table">
            <thead>
                <tr>
                    <?php
                    $weekdays = array(
                        __('Dimanche', 'elkhair-booking'),
                        __('Lundi', 'elkhair-booking'),
                        __('Mardi', 'elkhair-booking'),
                        __('Mercredi', 'elkhair-booking'),
                        __('Jeudi', 'elkhair-booking'),
                        __('Vendredi', 'elkhair-booking'),
                        __('Samedi', 'elkhair-booking')
                    );
                    foreach ($weekdays as $day) {
                        echo '<th>' . esc_html($day) . '</th>';
                    }
                    ?>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <?php
                    // Cases vides pour le début du mois
                    for ($i = 0; $i < $first_day_of_month; $i++) {
                        echo '<td class="empty"></td>';
                    }

                    // Jours du mois
                    $current_day = 1;
                    $column = $first_day_of_month;

                    while ($current_day <= $days_in_month) {
                        if ($column == 7) {
                            $column = 0;
                            echo '</tr><tr>';
                        }

                        $date = sprintf('%04d-%02d-%02d', $year, $month, $current_day);
                        $status = isset($availability_data[$date]) ? $availability_data[$date] : 'unavailable';
                        $booking = isset($booking_data[$date]) ? $booking_data[$date] : null;
                        $selectable = !$booking ? 'selectable' : '';

                        echo '<td class="calendar-day ' . esc_attr($status) . ' ' . $selectable . '" data-date="' . esc_attr($date) . '">';
                        echo '<div class="day-number">' . $current_day . '</div>';
                        
                        if ($booking) {
                            echo '<div class="booking-info">';
                            echo '<strong>' . esc_html($booking['name']) . '</strong><br>';
                            echo '<small>' . esc_html($booking['email']) . '</small>';
                            echo '</div>';
                        }
                        
                        echo '</td>';

                        $current_day++;
                        $column++;
                    }

                    // Cases vides pour la fin du mois
                    while ($column < 7) {
                        echo '<td class="empty"></td>';
                        $column++;
                    }
                    ?>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="calendar-actions">
        <button type="button" class="button button-primary" id="save-availability">
            <?php echo esc_html__('Enregistrer les disponibilités', 'elkhair-booking'); ?>
        </button>
    </div>

    <div class="calendar-legend">
        <div class="legend-item">
            <span class="legend-color available"></span>
            <?php echo esc_html__('Disponible', 'elkhair-booking'); ?>
        </div>
        <div class="legend-item">
            <span class="legend-color unavailable"></span>
            <?php echo esc_html__('Non disponible', 'elkhair-booking'); ?>
        </div>
        <div class="legend-item">
            <span class="legend-color booked"></span>
            <?php echo esc_html__('Réservé', 'elkhair-booking'); ?>
        </div>
    </div>
</div>

<style>
.availability-instructions {
    margin: 20px 0;
    padding: 15px;
    background: #fff;
    border-left: 4px solid #2271b1;
    box-shadow: 0 1px 2px rgba(0,0,0,0.1);
}

.calendar-navigation {
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 20px 0;
    gap: 20px;
}

.current-month {
    font-size: 1.2em;
    font-weight: bold;
}

.availability-calendar {
    margin: 20px 0;
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.calendar-table {
    width: 100%;
    border-collapse: collapse;
}

.calendar-table th {
    background: #f8f9fa;
    padding: 10px;
    text-align: center;
    border: 1px solid #dee2e6;
}

.calendar-table td {
    border: 1px solid #dee2e6;
    height: 120px;
    vertical-align: top;
    padding: 5px;
    position: relative;
}

.calendar-table td.empty {
    background: #f8f9fa;
}

.calendar-table td.selectable {
    cursor: pointer;
}

.calendar-table td.selectable:hover {
    opacity: 0.8;
}

.day-number {
    font-weight: bold;
    margin-bottom: 5px;
}

.calendar-day.available {
    background: #e8f5e9;
}

.calendar-day.unavailable {
    background: #f5f5f5;
}

.calendar-day.booked {
    background: #fff3e0;
}

.booking-info {
    font-size: 0.9em;
    margin-top: 5px;
    padding: 5px;
    background: rgba(255,255,255,0.8);
    border-radius: 4px;
}

.calendar-actions {
    text-align: center;
    margin: 20px 0;
}

.calendar-legend {
    display: flex;
    justify-content: center;
    gap: 20px;
    margin: 20px 0;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 5px;
}

.legend-color {
    width: 20px;
    height: 20px;
    border: 1px solid #dee2e6;
    border-radius: 4px;
}

.legend-color.available {
    background: #e8f5e9;
}

.legend-color.unavailable {
    background: #f5f5f5;
}

.legend-color.booked {
    background: #fff3e0;
}

@media (max-width: 768px) {
    .calendar-table th {
        font-size: 0.8em;
        padding: 5px;
    }

    .calendar-table td {
        height: 80px;
        font-size: 0.9em;
    }

    .booking-info {
        font-size: 0.8em;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    // Gestion de la sélection des dates
    $('.calendar-day.selectable').on('click', function() {
        var currentStatus = $(this).hasClass('available') ? 'available' : 'unavailable';
        if (currentStatus === 'available') {
            $(this).removeClass('available').addClass('unavailable');
        } else {
            $(this).removeClass('unavailable').addClass('available');
        }
    });

    // Sauvegarde des disponibilités
    $('#save-availability').on('click', function() {
        var availableDates = [];
        $('.calendar-day.available').each(function() {
            availableDates.push($(this).data('date'));
        });

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'save_availability',
                nonce: '<?php echo wp_create_nonce('elkhair-booking-nonce'); ?>',
                dates: availableDates
            },
            success: function(response) {
                if (response.success) {
                    alert('<?php echo esc_js(__('Disponibilités mises à jour avec succès', 'elkhair-booking')); ?>');
                } else {
                    alert('<?php echo esc_js(__('Erreur lors de la mise à jour des disponibilités', 'elkhair-booking')); ?>');
                }
            }
        });
    });
});
</script>
