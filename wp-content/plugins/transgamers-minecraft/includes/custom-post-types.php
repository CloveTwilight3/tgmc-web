<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Create custom post types
function transgamers_create_post_types() {
    
    // Server Addons Post Type
    register_post_type('server_addons', array(
        'labels' => array(
            'name' => 'Server Addons',
            'singular_name' => 'Server Addon',
            'add_new' => 'Add New Addon',
            'add_new_item' => 'Add New Server Addon',
            'edit_item' => 'Edit Server Addon',
            'new_item' => 'New Server Addon',
            'view_item' => 'View Server Addon',
            'search_items' => 'Search Server Addons',
            'not_found' => 'No server addons found',
            'not_found_in_trash' => 'No server addons found in trash'
        ),
        'public' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-admin-plugins',
        'supports' => array('title', 'editor', 'thumbnail', 'custom-fields'),
        'rewrite' => array('slug' => 'server-addons'),
        'show_in_rest' => true,
        'menu_position' => 20
    ));
    
    // FAQs Post Type
    register_post_type('faqs', array(
        'labels' => array(
            'name' => 'FAQs',
            'singular_name' => 'FAQ',
            'add_new' => 'Add New FAQ',
            'add_new_item' => 'Add New FAQ',
            'edit_item' => 'Edit FAQ',
            'new_item' => 'New FAQ',
            'view_item' => 'View FAQ',
            'search_items' => 'Search FAQs',
            'not_found' => 'No FAQs found',
            'not_found_in_trash' => 'No FAQs found in trash'
        ),
        'public' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-editor-help',
        'supports' => array('title', 'editor', 'custom-fields'),
        'rewrite' => array('slug' => 'faqs'),
        'show_in_rest' => true,
        'menu_position' => 21
    ));
    
    // Add custom fields for server addons
    add_action('add_meta_boxes', 'transgamers_add_addon_meta_boxes');
    add_action('save_post', 'transgamers_save_addon_meta');
}

// Add meta boxes for server addons
function transgamers_add_addon_meta_boxes() {
    add_meta_box(
        'addon_details',
        'Addon Details',
        'transgamers_addon_details_callback',
        'server_addons',
        'normal',
        'high'
    );
}

// Meta box callback
function transgamers_addon_details_callback($post) {
    wp_nonce_field('transgamers_addon_meta_nonce', 'addon_meta_nonce');
    
    $addon_type = get_post_meta($post->ID, '_addon_type', true);
    $addon_version = get_post_meta($post->ID, '_addon_version', true);
    $addon_author = get_post_meta($post->ID, '_addon_author', true);
    $addon_download_url = get_post_meta($post->ID, '_addon_download_url', true);
    ?>
    <table class="form-table">
        <tr>
            <th><label for="addon_type">Addon Type</label></th>
            <td>
                <select name="addon_type" id="addon_type">
                    <option value="datapack" <?php selected($addon_type, 'datapack'); ?>>Datapack</option>
                    <option value="plugin" <?php selected($addon_type, 'plugin'); ?>>Plugin</option>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="addon_version">Version</label></th>
            <td><input type="text" name="addon_version" id="addon_version" value="<?php echo esc_attr($addon_version); ?>" /></td>
        </tr>
        <tr>
            <th><label for="addon_author">Author</label></th>
            <td><input type="text" name="addon_author" id="addon_author" value="<?php echo esc_attr($addon_author); ?>" /></td>
        </tr>
        <tr>
            <th><label for="addon_download_url">Download URL</label></th>
            <td><input type="url" name="addon_download_url" id="addon_download_url" value="<?php echo esc_attr($addon_download_url); ?>" /></td>
        </tr>
    </table>
    <?php
}

// Save meta box data
function transgamers_save_addon_meta($post_id) {
    if (!isset($_POST['addon_meta_nonce']) || !wp_verify_nonce($_POST['addon_meta_nonce'], 'transgamers_addon_meta_nonce')) {
        return;
    }
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    $fields = array('addon_type', 'addon_version', 'addon_author', 'addon_download_url');
    
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
        }
    }
}