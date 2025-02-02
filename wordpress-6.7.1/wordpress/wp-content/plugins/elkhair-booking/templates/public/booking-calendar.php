<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="booking-calendar-container">
    <div id="booking-calendar"></div>
    
    <div class="calendar-legend">
        <div class="legend-item">
            <span class="color-box available"></span>
            <span class="legend-text">Disponible</span>
        </div>
        <div class="legend-item">
            <span class="color-box unavailable"></span>
            <span class="legend-text">Indisponible</span>
        </div>
    </div>

    <button id="make-booking" class="button button-primary" style="display: none;">Réserver ce créneau</button>
</div>

<style>
#booking-calendar {
    margin: 20px 0;
    max-width: 800px;
}

.ui-datepicker {
    width: 100%;
    background: #fff;
    border: 1px solid #ddd;
    padding: 15px;
    border-radius: 4px;
}

.ui-datepicker-header {
    background: #f7f7f7;
    padding: 10px;
    margin: -15px -15px 10px;
    border-bottom: 1px solid #ddd;
}

.ui-datepicker-title {
    text-align: center;
    font-weight: bold;
}

.ui-datepicker-prev, .ui-datepicker-next {
    cursor: pointer;
    position: absolute;
    top: 15px;
    width: 30px;
    height: 30px;
    text-indent: -9999px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.ui-datepicker-prev {
    left: 15px;
}

.ui-datepicker-next {
    right: 15px;
}

.ui-datepicker-calendar {
    width: 100%;
    border-collapse: collapse;
}

.ui-datepicker-calendar th {
    padding: 5px;
    text-align: center;
    background: #f7f7f7;
}

.ui-datepicker-calendar td {
    padding: 0;
    border: 1px solid #ddd;
}

.ui-datepicker-calendar td a {
    display: block;
    padding: 5px;
    text-align: center;
    text-decoration: none;
    color: #333;
}

.ui-datepicker td.available a {
    background-color: #90EE90 !important;
    color: #000;
    cursor: pointer;
}

.ui-datepicker td.unavailable a,
.ui-datepicker td.unavailable span {
    background-color: #f0f0f0 !important;
    color: #999;
    cursor: not-allowed;
}

.calendar-legend {
    margin: 20px 0;
    display: flex;
    gap: 20px;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 5px;
}

.color-box {
    width: 20px;
    height: 20px;
    border: 1px solid #ddd;
    border-radius: 3px;
}

.color-box.available {
    background-color: #90EE90;
}

.color-box.unavailable {
    background-color: #f0f0f0;
}

#make-booking {
    margin-top: 20px;
}
</style>

<script>
jQuery(document).ready(function($) {
    var availableDates = [];
    var selectedDate = null;

    // Charger les dates disponibles
    function loadAvailableDates() {
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'check_availability',
                nonce: elkhairBooking.nonce
            },
            success: function(response) {
                if (response.success && response.data && response.data.available_dates) {
                    availableDates = response.data.available_dates;
                    $('#booking-calendar').datepicker('refresh');
                }
            }
        });
    }

    // Initialiser le calendrier
    $('#booking-calendar').datepicker({
        numberOfMonths: 1,
        dateFormat: 'yy-mm-dd',
        firstDay: 1,
        minDate: 0, // Empêcher la sélection de dates passées
        beforeShowDay: function(date) {
            var dateString = $.datepicker.formatDate('yy-mm-dd', date);
            var isAvailable = availableDates.indexOf(dateString) !== -1;
            
            // Retourner [selectable, class, tooltip]
            return [isAvailable, isAvailable ? 'available' : 'unavailable', 
                   isAvailable ? 'Disponible' : 'Indisponible'];
        },
        onSelect: function(dateText, inst) {
            // Vérifier si la date est disponible
            if (availableDates.indexOf(dateText) !== -1) {
                selectedDate = dateText;
                $('#make-booking').show();
            } else {
                selectedDate = null;
                $('#make-booking').hide();
            }
        }
    });

    // Charger les dates disponibles au démarrage
    loadAvailableDates();

    // Gérer le clic sur le bouton de réservation
    $('#make-booking').on('click', function() {
        if (!selectedDate) {
            alert('Veuillez sélectionner une date disponible');
            return;
        }

        // Ici vous pouvez ajouter le code pour gérer la réservation
        alert('Réservation pour le ' + selectedDate);
    });
});
</script>
