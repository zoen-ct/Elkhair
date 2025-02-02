<?php
if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class CFTA_List_Table extends WP_List_Table {
    
    public function __construct() {
        parent::__construct([
            'singular' => 'message',
            'plural'   => 'messages',
            'ajax'     => false
        ]);
    }
    
    public function get_columns() {
        return [
            'cb'         => '<input type="checkbox" />',
            'name'       => __('Nom', 'contact-form-to-admin'),
            'email'      => __('Email', 'contact-form-to-admin'),
            'subject'    => __('Sujet', 'contact-form-to-admin'),
            'message'    => __('Message', 'contact-form-to-admin'),
            'created_at' => __('Date', 'contact-form-to-admin'),
            'is_read'    => __('Lu', 'contact-form-to-admin')
        ];
    }
    
    public function get_sortable_columns() {
        return [
            'name'       => ['name', true],
            'email'      => ['email', true],
            'subject'    => ['subject', true],
            'created_at' => ['created_at', true],
            'is_read'    => ['is_read', true]
        ];
    }
    
    public function prepare_items() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'contact_messages';
        
        // Pagination
        $per_page = 20;
        $current_page = $this->get_pagenum();
        $total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_name");
        
        // Tri
        $orderby = isset($_REQUEST['orderby']) ? sanitize_sql_orderby($_REQUEST['orderby']) : 'created_at';
        $order = isset($_REQUEST['order']) ? sanitize_text_field($_REQUEST['order']) : 'DESC';
        
        // Recherche
        $search = isset($_REQUEST['s']) ? sanitize_text_field($_REQUEST['s']) : '';
        $where = '';
        if (!empty($search)) {
            $where = $wpdb->prepare(
                " WHERE name LIKE %s OR email LIKE %s OR subject LIKE %s OR message LIKE %s",
                '%' . $wpdb->esc_like($search) . '%',
                '%' . $wpdb->esc_like($search) . '%',
                '%' . $wpdb->esc_like($search) . '%',
                '%' . $wpdb->esc_like($search) . '%'
            );
        }
        
        // Récupération des données
        $sql = $wpdb->prepare(
            "SELECT * FROM $table_name $where ORDER BY $orderby $order LIMIT %d OFFSET %d",
            $per_page,
            ($current_page - 1) * $per_page
        );
        
        $this->items = $wpdb->get_results($sql, ARRAY_A);
        
        // Configuration de la pagination
        $this->set_pagination_args([
            'total_items' => $total_items,
            'per_page'    => $per_page,
            'total_pages' => ceil($total_items / $per_page)
        ]);
    }
    
    protected function column_default($item, $column_name) {
        switch ($column_name) {
            case 'message':
                return wp_trim_words($item[$column_name], 20);
            case 'created_at':
                return mysql2date(get_option('date_format') . ' ' . get_option('time_format'), $item[$column_name]);
            case 'is_read':
                return $item[$column_name] ? '✓' : '×';
            default:
                return $item[$column_name];
        }
    }
    
    protected function column_cb($item) {
        return sprintf(
            '<input type="checkbox" name="bulk-delete[]" value="%s" />',
            $item['id']
        );
    }
    
    protected function column_subject($item) {
        $actions = [
            'view'   => sprintf(
                '<a href="#" class="cfta-view-message" data-id="%s">%s</a>',
                $item['id'],
                __('Voir', 'contact-form-to-admin')
            ),
            'delete' => sprintf(
                '<a href="%s" class="cfta-delete-message" data-id="%s">%s</a>',
                wp_nonce_url(
                    add_query_arg(
                        ['action' => 'delete', 'message' => $item['id']],
                        admin_url('admin.php?page=cfta-messages')
                    ),
                    'delete_message_' . $item['id']
                ),
                $item['id'],
                __('Supprimer', 'contact-form-to-admin')
            )
        ];
        
        return sprintf(
            '%1$s %2$s',
            $item['subject'],
            $this->row_actions($actions)
        );
    }
    
    public function get_bulk_actions() {
        return [
            'bulk-delete' => __('Supprimer', 'contact-form-to-admin'),
            'bulk-mark-read' => __('Marquer comme lu', 'contact-form-to-admin'),
            'bulk-mark-unread' => __('Marquer comme non lu', 'contact-form-to-admin')
        ];
    }
}
