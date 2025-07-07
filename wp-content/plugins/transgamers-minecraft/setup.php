<?php
// This file helps with initial setup - run once after plugin activation

// Create sample content
function transgamers_create_sample_content() {
    // Create sample FAQ
    $faq_post = array(
        'post_title' => 'How do I join the server?',
        'post_content' => 'To join our Minecraft SMP server, you need to be whitelisted first. Please join our Discord server and follow the instructions in the #whitelist channel. Our server address is mc.transgamers.org.',
        'post_status' => 'publish',
        'post_type' => 'faqs'
    );
    wp_insert_post($faq_post);
    
    // Create sample server addon
    $addon_post = array(
        'post_title' => 'Graves Datapack',
        'post_content' => 'When you die, your items will be safely stored in a grave at your death location. Right-click the grave to retrieve your items. Graves are protected and will persist until claimed.',
        'post_status' => 'publish',
        'post_type' => 'server_addons'
    );
    $addon_id = wp_insert_post($addon_post);
    
    if ($addon_id) {
        update_post_meta($addon_id, '_addon_type', 'datapack');
        update_post_meta($addon_id, '_addon_version', '1.2.0');
        update_post_meta($addon_id, '_addon_author', 'TransGamers Team');
    }
}

// Set default options
function transgamers_set_default_options() {
    update_option('transgamers_total_members', '50+');
    update_option('transgamers_uptime', '99.9');
    update_option('transgamers_minecraft_server_host', 'mc.transgamers.org');
    update_option('transgamers_minecraft_server_port', 25575);
}

// Only run if accessed directly (not included)
if (basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
    require_once('../../../wp-load.php');
    
    if (current_user_can('manage_options')) {
        transgamers_create_sample_content();
        transgamers_set_default_options();
        echo "Sample content created successfully!";
    } else {
        echo "Unauthorized access.";
    }
}
?>