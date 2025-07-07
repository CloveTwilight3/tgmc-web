<?php
/**
 * Plugin Name: TransGamers Minecraft SMP
 * Description: Custom functionality for TransGamers Minecraft SMP website
 * Version: 1.0.0
 * Author: TransGamers Team
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('TRANSGAMERS_PLUGIN_URL', plugin_dir_url(__FILE__));
define('TRANSGAMERS_PLUGIN_PATH', plugin_dir_path(__FILE__));

// Include required files
require_once TRANSGAMERS_PLUGIN_PATH . 'includes/custom-post-types.php';
require_once TRANSGAMERS_PLUGIN_PATH . 'includes/woocommerce-integration.php';
require_once TRANSGAMERS_PLUGIN_PATH . 'includes/discord-integration.php';
require_once TRANSGAMERS_PLUGIN_PATH . 'includes/minecraft-integration.php';
require_once TRANSGAMERS_PLUGIN_PATH . 'includes/admin-functions.php';

// Activation hook
register_activation_hook(__FILE__, 'transgamers_activate_plugin');

function transgamers_activate_plugin() {
    // Create custom post types
    transgamers_create_post_types();
    
    // Flush rewrite rules
    flush_rewrite_rules();
    
    // Create necessary database tables
    transgamers_create_database_tables();
}

// Initialize plugin
add_action('init', 'transgamers_init_plugin');

function transgamers_init_plugin() {
    // Load plugin text domain
    load_plugin_textdomain('transgamers', false, dirname(plugin_basename(__FILE__)) . '/languages/');
    
    // Initialize custom post types
    transgamers_create_post_types();
    
    // Initialize WooCommerce integration
    if (class_exists('WooCommerce')) {
        transgamers_init_woocommerce();
    }
}

// Create database tables for tracking donations
function transgamers_create_database_tables() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'transgamers_donations';
    
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        order_id bigint(20) NOT NULL,
        discord_username varchar(100) DEFAULT '' NOT NULL,
        minecraft_username varchar(100) DEFAULT '' NOT NULL,
        donation_amount decimal(10,2) NOT NULL,
        donation_date datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        webhook_sent tinyint(1) DEFAULT 0 NOT NULL,
        minecraft_role_added tinyint(1) DEFAULT 0 NOT NULL,
        PRIMARY KEY (id),
        UNIQUE KEY order_id (order_id)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Enqueue scripts and styles
add_action('wp_enqueue_scripts', 'transgamers_enqueue_scripts');

function transgamers_enqueue_scripts() {
    wp_enqueue_style('transgamers-style', TRANSGAMERS_PLUGIN_URL . 'assets/css/style.css', array(), '1.0.0');
    wp_enqueue_script('transgamers-script', TRANSGAMERS_PLUGIN_URL . 'assets/js/script.js', array('jquery'), '1.0.0', true);
    
    // Pass data to JavaScript
    wp_localize_script('transgamers-script', 'transgamers_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('transgamers_nonce')
    ));
}