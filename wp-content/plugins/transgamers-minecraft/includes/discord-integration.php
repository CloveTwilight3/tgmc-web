<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Send Discord notification
function transgamers_send_discord_notification($order_id, $discord_username, $minecraft_username, $donation_amount) {
    $webhook_url = get_option('transgamers_discord_webhook_url', '');
    
    if (empty($webhook_url)) {
        error_log('TransGamers: Discord webhook URL not configured');
        return false;
    }
    
    // Create embed message
    $embed = array(
        'title' => '🎉 New Donation Received!',
        'description' => 'A generous community member has made a donation to support our Minecraft SMP!',
        'color' => 0x00ff00, // Green color
        'fields' => array(
            array(
                'name' => 'Amount',
                'value' => '$' . number_format($donation_amount, 2),
                'inline' => true
            )
        ),
        'timestamp' => date('c'),
        'footer' => array(
            'text' => 'TransGamers Minecraft SMP',
            'icon_url' => get_site_url() . '/wp-content/uploads/transgamers-icon.png'
        )
    );
    
    // Add Discord username if provided
    if (!empty($discord_username)) {
        $embed['fields'][] = array(
            'name' => 'Discord User',
            'value' => $discord_username,
            'inline' => true
        );
    }
    
    // Add Minecraft username if provided
    if (!empty($minecraft_username)) {
        $embed['fields'][] = array(
            'name' => 'Minecraft User',
            'value' => $minecraft_username,
            'inline' => true
        );
    }
    
    $message = array(
        'embeds' => array($embed)
    );
    
    // Send webhook
    $response = wp_remote_post($webhook_url, array(
        'body' => json_encode($message),
        'headers' => array(
            'Content-Type' => 'application/json'
        ),
        'timeout' => 30
    ));
    
    if (is_wp_error($response)) {
        error_log('TransGamers Discord Webhook Error: ' . $response->get_error_message());
        return false;
    }
    
    $response_code = wp_remote_retrieve_response_code($response);
    if ($response_code !== 200 && $response_code !== 204) {
        error_log('TransGamers Discord Webhook Error: HTTP ' . $response_code);
        return false;
    }
    
    // Update database to mark webhook as sent
    global $wpdb;
    $table_name = $wpdb->prefix . 'transgamers_donations';
    $wpdb->update(
        $table_name,
        array('webhook_sent' => 1),
        array('order_id' => $order_id),
        array('%d'),
        array('%d')
    );
    
    return true;
}

// Test Discord webhook
function transgamers_test_discord_webhook() {
    $webhook_url = get_option('transgamers_discord_webhook_url', '');
    
    if (empty($webhook_url)) {
        return array('success' => false, 'message' => 'Webhook URL not configured');
    }
    
    $message = array(
        'embeds' => array(
            array(
                'title' => '🧪 Test Message',
                'description' => 'This is a test message from your TransGamers Minecraft SMP website!',
                'color' => 0x0099ff,
                'timestamp' => date('c'),
                'footer' => array(
                    'text' => 'TransGamers Minecraft SMP - Test'
                )
            )
        )
    );
    
    $response = wp_remote_post($webhook_url, array(
        'body' => json_encode($message),
        'headers' => array(
            'Content-Type' => 'application/json'
        ),
        'timeout' => 30
    ));
    
    if (is_wp_error($response)) {
        return array('success' => false, 'message' => 'Error: ' . $response->get_error_message());
    }
    
    $response_code = wp_remote_retrieve_response_code($response);
    if ($response_code !== 200 && $response_code !== 204) {
        return array('success' => false, 'message' => 'HTTP Error: ' . $response_code);
    }
    
    return array('success' => true, 'message' => 'Test message sent successfully!');
}