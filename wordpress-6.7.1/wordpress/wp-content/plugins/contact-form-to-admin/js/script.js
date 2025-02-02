jQuery(document).ready(function($) {
    // Gestion du formulaire de contact
    $('#contact-form').on('submit', function(e) {
        e.preventDefault();
        
        var form = $(this);
        var messageDiv = $('#form-message');
        var submitButton = form.find('button[type="submit"]');
        
        console.log('Form submission started');
        
        // Désactiver le bouton pendant la soumission
        submitButton.prop('disabled', true);
        
        // Récupérer les données du formulaire
        var formData = new FormData(this);
        formData.append('action', 'cfta_submit_form');
        formData.append('nonce', $('#cfta_nonce').val());
        
        console.log('Form data:', Object.fromEntries(formData));
        
        // Envoyer la requête AJAX
        $.ajax({
            url: cfta_ajax.ajax_url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                console.log('AJAX success:', response);
                if (response.success) {
                    messageDiv
                        .removeClass('error')
                        .addClass('success')
                        .html(response.data)
                        .show();
                    form[0].reset();
                } else {
                    console.error('Form submission error:', response.data);
                    messageDiv
                        .removeClass('success')
                        .addClass('error')
                        .html(response.data)
                        .show();
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', status, error);
                messageDiv
                    .removeClass('success')
                    .addClass('error')
                    .html('Une erreur est survenue. Veuillez réessayer.')
                    .show();
            },
            complete: function() {
                submitButton.prop('disabled', false);
            }
        });
    });

    // Gestion de la suppression des messages
    $('.delete-message').on('click', function() {
        var button = $(this);
        var messageId = button.data('id');
        var nonce = button.data('nonce');
        
        if (confirm('Êtes-vous sûr de vouloir supprimer ce message ?')) {
            button.prop('disabled', true);
            
            $.ajax({
                url: cfta_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'cfta_delete_message',
                    id: messageId,
                    nonce: nonce
                },
                success: function(response) {
                    if (response.success) {
                        $('#message-' + messageId).fadeOut(400, function() {
                            $(this).remove();
                            
                            // Vérifier s'il reste des messages
                            if ($('.wp-list-table tbody tr').length === 0) {
                                $('.wp-list-table').replaceWith('<p>Aucun message pour le moment.</p>');
                            }
                        });
                        
                        $('#admin-message')
                            .removeClass('error')
                            .addClass('success')
                            .html(response.data)
                            .fadeIn();
                    } else {
                        $('#admin-message')
                            .removeClass('success')
                            .addClass('error')
                            .html(response.data)
                            .fadeIn();
                        button.prop('disabled', false);
                    }
                },
                error: function() {
                    $('#admin-message')
                        .removeClass('success')
                        .addClass('error')
                        .html('Une erreur est survenue lors de la suppression.')
                        .fadeIn();
                    button.prop('disabled', false);
                }
            });
        }
    });
});
