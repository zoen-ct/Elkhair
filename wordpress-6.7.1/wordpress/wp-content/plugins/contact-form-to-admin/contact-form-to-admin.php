    <?php
/*
Plugin Name: Contact Form to Admin
Description: Un simple formulaire de contact qui envoie les messages à l'administrateur
Version: 1.0
Author: Votre Nom
*/

if (!defined('ABSPATH')) {
    exit;
}

// Activation et mise à jour du plugin
register_activation_hook(__FILE__, 'cfta_activate_plugin');
add_action('plugins_loaded', 'cfta_update_db_check');

function cfta_get_db_version() {
    return get_option('cfta_db_version', '1.0');
}

function cfta_update_db_check() {
    if (cfta_get_db_version() != '1.1') {
        cfta_activate_plugin();
        update_option('cfta_db_version', '1.1');
    }
}

function cfta_activate_plugin() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'contact_messages';
    
    $charset_collate = $wpdb->get_charset_collate();
    
    // Vérifier si la table existe
    $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name;
    
    if ($table_exists) {
        // Vérifier si les colonnes existent
        $columns = $wpdb->get_col("SHOW COLUMNS FROM $table_name");
        
        if (!in_array('prenom', $columns)) {
            $wpdb->query("ALTER TABLE $table_name ADD prenom varchar(100) NOT NULL AFTER name");
        }
        if (!in_array('tel', $columns)) {
            $wpdb->query("ALTER TABLE $table_name ADD tel varchar(20) NOT NULL AFTER prenom");
        }
    } else {
        // Créer la table si elle n'existe pas
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name varchar(100) NOT NULL,
            prenom varchar(100) NOT NULL,
            tel varchar(20) NOT NULL,
            email varchar(100) NOT NULL,
            message text NOT NULL,
            created_at timestamp DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}

// Enregistrement des scripts
function cfta_enqueue_scripts() {
    wp_enqueue_script('jquery');
    wp_enqueue_script(
        'cfta-script',
        plugins_url('js/script.js', __FILE__),
        array('jquery'),
        '1.0',
        true
    );
    
    wp_localize_script('cfta-script', 'cfta_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('cfta_nonce')
    ));
}
add_action('wp_enqueue_scripts', 'cfta_enqueue_scripts');
add_action('admin_enqueue_scripts', 'cfta_enqueue_scripts');

// Ajout du menu admin
add_action('admin_menu', 'cfta_add_admin_menu');

function cfta_add_admin_menu() {
    add_menu_page(
        'Messages de contact',
        'Messages de contact',
        'manage_options',
        'contact-messages',
        'cfta_admin_page',
        'dashicons-email',
        30
    );
}

// Page d'administration
function cfta_admin_page() {
    include plugin_dir_path(__FILE__) . 'admin/admin-page.php';
}

// Shortcode pour le formulaire
function cfta_contact_form_shortcode() {
    ob_start();
    ?>
    <div class="contact-form-container">
        <div id="form-message"></div>
        <form id="contact-form" class="contact-form">
            <?php wp_nonce_field('cfta_nonce', 'cfta_nonce'); ?>
            <div class="form-row">
                <div class="form-group">
                    <label for="name">Nom</label>
                    <input type="text" id="name" name="name" placeholder="Votre nom" required>
                </div>
                <div class="form-group">
                    <label for="prenom">Prénom</label>
                    <input type="text" id="prenom" name="prenom" placeholder="Votre prénom" required>
                </div>
            </div>
            <div class="form-group">
                <label for="tel">Téléphone</label>
                <input type="tel" id="tel" name="tel" placeholder="Votre téléphone" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Votre email" required>
            </div>
            <div class="form-group">
                <label for="message">Message</label>
                <textarea id="message" name="message" placeholder="Votre message" required></textarea>
            </div>
            <button type="submit" class="submit-button">Envoyer le message</button>
        </form>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('contact_form', 'cfta_contact_form_shortcode');

// Traitement AJAX du formulaire
add_action('wp_ajax_cfta_submit_form', 'cfta_handle_form_submission');
add_action('wp_ajax_nopriv_cfta_submit_form', 'cfta_handle_form_submission');

function cfta_handle_form_submission() {
    // Activer le débogage WordPress
    @ini_set('display_errors', 1);
    @error_reporting(E_ALL);

    // Vérification du nonce
    if (!check_ajax_referer('cfta_nonce', 'nonce', false)) {
        error_log('Erreur de vérification du nonce');
        wp_send_json_error('Erreur de sécurité. Veuillez réessayer.');
        return;
    }

    // Vérifier si les données POST sont présentes
    if (!isset($_POST['name'], $_POST['prenom'], $_POST['tel'], $_POST['email'], $_POST['message'])) {
        error_log('Données POST manquantes');
        wp_send_json_error('Données du formulaire incomplètes.');
        return;
    }
    
    $name = sanitize_text_field($_POST['name']);
    $prenom = sanitize_text_field($_POST['prenom']);
    $tel = sanitize_text_field($_POST['tel']);
    $email = sanitize_email($_POST['email']);
    $message = sanitize_textarea_field($_POST['message']);
    
    // Validation des champs
    $errors = [];
    if (empty($name)) $errors[] = 'Nom manquant';
    if (empty($prenom)) $errors[] = 'Prénom manquant';
    if (empty($tel)) $errors[] = 'Téléphone manquant';
    if (empty($email)) $errors[] = 'Email manquant';
    if (empty($message)) $errors[] = 'Message manquant';
    
    if (!empty($errors)) {
        error_log('Erreurs de validation : ' . implode(', ', $errors));
        wp_send_json_error('Tous les champs sont obligatoires : ' . implode(', ', $errors));
        return;
    }
    
    if (!is_email($email)) {
        error_log('Email invalide : ' . $email);
        wp_send_json_error('Veuillez entrer une adresse email valide.');
        return;
    }
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'contact_messages';
    
    // Débogage des informations avant l'insertion
    error_log('Tentative d\'insertion : ' . print_r([
        'name' => $name,
        'prenom' => $prenom,
        'tel' => $tel,
        'email' => $email,
        'message' => $message
    ], true));
    
    $result = $wpdb->insert(
        $table_name,
        array(
            'name' => $name,
            'prenom' => $prenom,
            'tel' => $tel,
            'email' => $email,
            'message' => $message
        ),
        array('%s', '%s', '%s', '%s', '%s')
    );
    
    if ($result === false) {
        // Log détaillé de l'erreur de base de données
        error_log('Erreur d\'insertion en base de données : ' . $wpdb->last_error);
        error_log('Dernière requête : ' . $wpdb->last_query);
        wp_send_json_error('Une erreur s\'est produite lors de l\'envoi du message. Détails : ' . $wpdb->last_error);
        return;
    }
    
    wp_send_json_success('Message envoyé avec succès !');
}

// Suppression AJAX des messages
add_action('wp_ajax_cfta_delete_message', 'cfta_delete_message');

function cfta_delete_message() {
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Permission refusée.');
        return;
    }
    
    check_ajax_referer('cfta_nonce', 'nonce');
    
    $id = intval($_POST['id']);
    if (!$id) {
        wp_send_json_error('ID de message invalide.');
        return;
    }
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'contact_messages';
    
    $result = $wpdb->delete(
        $table_name,
        array('id' => $id),
        array('%d')
    );
    
    if ($result === false) {
        wp_send_json_error('Erreur lors de la suppression du message.');
        return;
    }
    
    wp_send_json_success('Message supprimé avec succès.');
}