<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Add Minecraft donor role
function transgamers_add_minecraft_role($order_id, $minecraft_username) {
    $server_host = get_option('transgamers_minecraft_server_host', 'localhost');
    $server_port = get_option('transgamers_minecraft_server_port', 25575);
    $rcon_password = get_option('transgamers_minecraft_rcon_password', '');
    
    if (empty($rcon_password)) {
        error_log('TransGamers: RCON password not configured');
        return false;
    }
    
    // Try to add role via RCON
    $success = transgamers_execute_rcon_command($server_host, $server_port, $rcon_password, 
        "lp user $minecraft_username parent add donor");
    
    if ($success) {
        // Update database to mark role as added
        global $wpdb;
        $table_name = $wpdb->prefix . 'transgamers_donations';
        $wpdb->update(
            $table_name,
            array('minecraft_role_added' => 1),
            array('order_id' => $order_id),
            array('%d'),
            array('%d')
        );
        
        // Log success
        error_log("TransGamers: Successfully added donor role to $minecraft_username");
        return true;
    } else {
        error_log("TransGamers: Failed to add donor role to $minecraft_username");
        return false;
    }
}

// Execute RCON command
function transgamers_execute_rcon_command($host, $port, $password, $command) {
    try {
        // Create socket
        $socket = fsockopen($host, $port, $errno, $errstr, 10);
        if (!$socket) {
            error_log("TransGamers RCON: Connection failed - $errstr ($errno)");
            return false;
        }
        
        // Set timeout
        stream_set_timeout($socket, 5);
        
        // Send authentication
        $auth_packet = transgamers_create_rcon_packet(1, 3, $password);
        fwrite($socket, $auth_packet);
        
        // Read auth response
        $response = fread($socket, 4096);
        if (!$response) {
            fclose($socket);
            error_log("TransGamers RCON: Authentication failed");
            return false;
        }
        
        // Send command
        $command_packet = transgamers_create_rcon_packet(2, 2, $command);
        fwrite($socket, $command_packet);
        
        // Read command response
        $response = fread($socket, 4096);
        fclose($socket);
        
        if ($response) {
            error_log("TransGamers RCON: Command executed successfully - $command");
            return true;
        } else {
            error_log("TransGamers RCON: Command execution failed - $command");
            return false;
        }
        
    } catch (Exception $e) {
        error_log("TransGamers RCON Exception: " . $e->getMessage());
        return false;
    }
}

// Create RCON packet
function transgamers_create_rcon_packet($id, $type, $body) {
    $packet = pack('VV', $id, $type) . $body . "\x00\x00";
    return pack('V', strlen($packet)) . $packet;
}

// Test Minecraft connection
function transgamers_test_minecraft_connection() {
    $server_host = get_option('transgamers_minecraft_server_host', 'localhost');
    $server_port = get_option('transgamers_minecraft_server_port', 25575);
    $rcon_password = get_option('transgamers_minecraft_rcon_password', '');
    
    if (empty($rcon_password)) {
        return array('success' => false, 'message' => 'RCON password not configured');
    }
    
    $success = transgamers_execute_rcon_command($server_host, $server_port, $rcon_password, 'list');
    
    if ($success) {
        return array('success' => true, 'message' => 'Successfully connected to Minecraft server');
    } else {
        return array('success' => false, 'message' => 'Failed to connect to Minecraft server');
    }
}

// Get online players (optional feature)
function transgamers_get_online_players() {
    $server_host = get_option('transgamers_minecraft_server_host', 'localhost');
    $server_port = get_option('transgamers_minecraft_server_port', 25575);
    $rcon_password = get_option('transgamers_minecraft_rcon_password', '');
    
    if (empty($rcon_password)) {
        return array();
    }
    
    // This would need more advanced RCON implementation to parse response
    // For now, just return empty array
    return array();
}