<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Initialize WooCommerce integration
function transgamers_init_woocommerce() {
    add_action('woocommerce_before_add_to_cart_button', 'transgamers_add_donation_fields');
    add_action('woocommerce_add_to_cart_validation', 'transgamers_validate_donation_fields', 10, 3);
    add_action('woocommerce_add_cart_item_data', 'transgamers_add_cart_item_data', 10, 3);
    add_action('woocommerce_get_item_data', 'transgamers_display_cart_item_data', 10, 2);
    add_action('woocommerce_checkout_create_order_line_item', 'transgamers_save_order_item_data', 10, 4);
    add_action('woocommerce_payment_complete', 'transgamers_process_donation', 10, 1);
    add_action('woocommerce_order_status_completed', 'transgamers_process_donation', 10, 1);
}

// Add custom fields to donation product
function transgamers_add_donation_fields() {
    global $product;
    
    // Check if this is the donation product (you'll need to set this product ID)
    $donation_product_id = get_option('transgamers_donation_product_id', 0);
    
    if ($product->get_id() == $donation_product_id) {
        ?>
        <div class="transgamers-donation-fields">
            <h3>Donation Information</h3>
            <p><em>These fields are optional but help us recognize your contribution in our community!</em></p>
            
            <div class="donation-field">
                <label for="discord_username">Discord Username</label>
                <input type="text" name="discord_username" id="discord_username" 
                       placeholder="YourDiscord#1234" class="form-control" />
                <small>Enter your Discord username to be recognized in our Discord server</small>
            </div>
            
            <div class="donation-field">
                <label for="minecraft_username">Minecraft Username</label>
                <input type="text" name="minecraft_username" id="minecraft_username" 
                       placeholder="MinecraftUser" class="form-control" />
                <small>Enter your Minecraft username to receive donor perks in-game</small>
            </div>
        </div>
        <style>
        .transgamers-donation-fields {
            margin: 20px 0;
            padding: 20px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            background: #f9f9f9;
        }
        .donation-field {
            margin-bottom: 15px;
        }
        .donation-field label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .donation-field input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .donation-field small {
            display: block;
            color: #666;
            font-size: 0.85em;
            margin-top: 5px;
        }
        </style>
        <?php
    }
}

// Validate donation fields
function transgamers_validate_donation_fields($passed, $product_id, $quantity) {
    $donation_product_id = get_option('transgamers_donation_product_id', 0);
    
    if ($product_id == $donation_product_id) {
        // Validation can be added here if needed
        // Currently allowing optional fields
    }
    
    return $passed;
}

// Add cart item data
function transgamers_add_cart_item_data($cart_item_data, $product_id, $variation_id) {
    $donation_product_id = get_option('transgamers_donation_product_id', 0);
    
    if ($product_id == $donation_product_id) {
        if (isset($_POST['discord_username']) && !empty($_POST['discord_username'])) {
            $cart_item_data['discord_username'] = sanitize_text_field($_POST['discord_username']);
        }
        
        if (isset($_POST['minecraft_username']) && !empty($_POST['minecraft_username'])) {
            $cart_item_data['minecraft_username'] = sanitize_text_field($_POST['minecraft_username']);
        }
    }
    
    return $cart_item_data;
}

// Display cart item data
function transgamers_display_cart_item_data($item_data, $cart_item) {
    if (isset($cart_item['discord_username'])) {
        $item_data[] = array(
            'key' => 'Discord Username',
            'value' => $cart_item['discord_username']
        );
    }
    
    if (isset($cart_item['minecraft_username'])) {
        $item_data[] = array(
            'key' => 'Minecraft Username',
            'value' => $cart_item['minecraft_username']
        );
    }
    
    return $item_data;
}

// Save order item data
function transgamers_save_order_item_data($item, $cart_item_key, $values, $order) {
    if (isset($values['discord_username'])) {
        $item->add_meta_data('Discord Username', $values['discord_username']);
    }
    
    if (isset($values['minecraft_username'])) {
        $item->add_meta_data('Minecraft Username', $values['minecraft_username']);
    }
}

// Process donation after payment
function transgamers_process_donation($order_id) {
    $order = wc_get_order($order_id);
    
    if (!$order) {
        return;
    }
    
    $donation_product_id = get_option('transgamers_donation_product_id', 0);
    $discord_username = '';
    $minecraft_username = '';
    $donation_amount = 0;
    
    // Check if order contains donation product
    foreach ($order->get_items() as $item) {
        if ($item->get_product_id() == $donation_product_id) {
            $discord_username = $item->get_meta('Discord Username');
            $minecraft_username = $item->get_meta('Minecraft Username');
            $donation_amount = $item->get_total();
            break;
        }
    }
    
    if ($donation_amount > 0) {
        // Save donation to database
        transgamers_save_donation($order_id, $discord_username, $minecraft_username, $donation_amount);
        
        // Send Discord notification
        if (!empty($discord_username)) {
            transgamers_send_discord_notification($order_id, $discord_username, $minecraft_username, $donation_amount);
        }
        
        // Add Minecraft role
        if (!empty($minecraft_username)) {
            transgamers_add_minecraft_role($order_id, $minecraft_username);
        }
    }
}

// Save donation to database
function transgamers_save_donation($order_id, $discord_username, $minecraft_username, $donation_amount) {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'transgamers_donations';
    
    $wpdb->insert(
        $table_name,
        array(
            'order_id' => $order_id,
            'discord_username' => $discord_username,
            'minecraft_username' => $minecraft_username,
            'donation_amount' => $donation_amount,
            'donation_date' => current_time('mysql')
        ),
        array('%d', '%s', '%s', '%f', '%s')
    );
}