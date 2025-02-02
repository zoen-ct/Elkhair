<?php
if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;
$table_name = $wpdb->prefix . 'contact_messages';

// Vérifier si la table existe
$table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name;

if (!$table_exists) {
    echo '<div class="wrap"><p>La table des messages n\'existe pas encore.</p></div>';
    return;
}

// Récupération des messages
$messages = $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC", ARRAY_A);
?>

<div class="wrap">
    <h1>Messages de contact</h1>
    
    <div id="admin-message" style="display: none;"></div>
    
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Téléphone</th>
                <th>Email</th>
                <th>Message</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($messages)) : ?>
                <tr>
                    <td colspan="7">Aucun message pour le moment.</td>
                </tr>
            <?php else : ?>
                <?php foreach ($messages as $message) : ?>
                    <tr id="message-<?php echo esc_attr($message['id']); ?>">
                        <td><?php echo esc_html($message['name']); ?></td>
                        <td><?php echo esc_html($message['prenom']); ?></td>
                        <td><?php echo esc_html($message['tel']); ?></td>
                        <td><?php echo esc_html($message['email']); ?></td>
                        <td><?php echo esc_html($message['message']); ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($message['created_at'])); ?></td>
                        <td>
                            <button class="button button-small delete-message" 
                                    data-id="<?php echo esc_attr($message['id']); ?>"
                                    data-nonce="<?php echo wp_create_nonce('cfta_nonce'); ?>">
                                Supprimer
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<style>
.admin-message {
    margin: 15px 0;
    padding: 10px;
    border-radius: 4px;
}

.admin-message.success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.admin-message.error {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.delete-message {
    color: #dc3545;
    border-color: #dc3545;
}

.delete-message:hover {
    background: #dc3545;
    color: white;
    border-color: #dc3545;
}

.wp-list-table {
    table-layout: fixed;
}
.wp-list-table td {
    word-wrap: break-word;
}
</style>
