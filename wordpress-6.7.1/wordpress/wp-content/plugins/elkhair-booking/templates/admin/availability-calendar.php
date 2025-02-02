<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap">
    <h1>Gestion des disponibilités</h1>
    
    <div id="availability-calendar"></div>
    
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

    <button id="save-availability" class="button button-primary">Enregistrer les disponibilités</button>
</div>

<style>
.ui-datepicker td.available a {
    background-color: #90EE90 !important;
    color: #000;
}

.ui-datepicker td.unavailable a {
    background-color: #f0f0f0 !important;
    color: #666;
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
</style>

<script>
jQuery(document).ready(function($) {
    var selectedDates = [];

    function showNotice(message, type) {
        var notice = $('<div class="notice notice-' + type + ' is-dismissible"><p>' + message + '</p></div>');
        $('.wrap h1').after(notice);
        setTimeout(function() {
            notice.fadeOut();
        }, 3000);
    }

    $('#availability-calendar').datepicker({
        numberOfMonths: 2,
        dateFormat: 'yy-mm-dd',
        firstDay: 1,
        beforeShowDay: function(date) {
            var dateString = $.datepicker.formatDate('yy-mm-dd', date);
            var isSelected = selectedDates.indexOf(dateString) !== -1;
            return [true, isSelected ? 'available' : 'unavailable'];
        },
        onSelect: function(dateText) {
            var index = selectedDates.indexOf(dateText);
            if (index === -1) {
                selectedDates.push(dateText);
            } else {
                selectedDates.splice(index, 1);
            }
            $(this).datepicker('refresh');
        }
    });

    // Charger les dates existantes
    $.post(ajaxurl, {
        action: 'check_availability',
        nonce: '<?php echo wp_create_nonce("elkhair-booking-nonce"); ?>'
    }, function(response) {
        if (response.success && response.data.available_dates) {
            selectedDates = response.data.available_dates;
            $('#availability-calendar').datepicker('refresh');
        }
    });

    // Sauvegarder les dates
    $('#save-availability').on('click', function() {
        var $button = $(this);
        $button.prop('disabled', true).text('Enregistrement...');

        $.post(ajaxurl, {
            action: 'save_availability',
            nonce: '<?php echo wp_create_nonce("elkhair-booking-nonce"); ?>',
            dates: selectedDates
        }, function(response) {
            if (response.success) {
                showNotice('Disponibilités enregistrées avec succès', 'success');
                if (response.data.available_dates) {
                    selectedDates = response.data.available_dates;
                    $('#availability-calendar').datepicker('refresh');
                }
            } else {
                showNotice('Erreur lors de l\'enregistrement', 'error');
            }
        }).fail(function() {
            showNotice('Erreur de connexion', 'error');
        }).always(function() {
            $button.prop('disabled', false).text('Enregistrer les disponibilités');
        });
    });
});
</script>