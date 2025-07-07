<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Add admin menu
add_action('admin_menu', 'transgamers_add_admin_menu');

function transgamers_add_admin_menu() {
    add_menu_page(
        'TransGamers Settings',
        'TransGamers',
        'manage_options',
        'transgamers-settings',
        'transgamers_admin_page',
        'dashicons-games',
        30
    );
    
    add_submenu_page(
        'transgamers-settings',
        'Settings',
        'Settings',
        'manage_options',
        'transgamers-settings',
        'transgamers_admin_page'
    );
    
    add_submenu_page(
        'transgamers-settings',
        'Donations',
        'Donations',
        'manage_options',
        'transgamers-donations',
        'transgamers_donations_page'
    );
}

// Admin page
function transgamers_admin_page() {
    if (isset($_POST['submit'])) {
        // Save settings
        update_option('transgamers_donation_product_id', intval($_POST['donation_product_id']));
        update_option('transgamers_discord_webhook_url', sanitize_url($_POST['discord_webhook_url']));
        update_option('transgamers_minecraft_server_host', sanitize_text_field($_POST['minecraft_server_host']));
        update_option('transgamers_minecraft_server_port', intval($_POST['minecraft_server_port']));
        update_option('transgamers_minecraft_rcon_password', sanitize_text_field($_POST['minecraft_rcon_password']));
        
        echo '<div class="notice notice-success"><p>Settings saved successfully!</p></div>';
    }
    
    // Handle test buttons
    if (isset($_POST['test_discord'])) {
        $result = transgamers_test_discord_webhook();
        if ($result['success']) {
            echo '<div class="notice notice-success"><p>' . $result['message'] . '</p></div>';
        } else {
            echo '<div class="notice notice-error"><p>' . $result['message'] . '</p></div>';
        }
    }
    
    if (isset($_POST['test_minecraft'])) {
        $result = transgamers_test_minecraft_connection();
        if ($result['success']) {
            echo '<div class="notice notice-success"><p>' . $result['message'] . '</p></div>';
        } else {
            echo '<div class="notice notice-error"><p>' . $result['message'] . '</p></div>';
        }
    }
    
    // Get current settings
    $donation_product_id = get_option('transgamers_donation_product_id', 0);
    $discord_webhook_url = get_option('transgamers_discord_webhook_url', '');
    $minecraft_server_host = get_option('transgamers_minecraft_server_host', 'localhost');
    $minecraft_server_port = get_option('transgamers_minecraft_server_port', 25575);
    $minecraft_rcon_password = get_option('transgamers_minecraft_rcon_password', '');
    
    // Get WooCommerce products for dropdown
    $products = wc_get_products(array('limit' => -1, 'status' => 'publish'));
    ?>
    
    <div class="wrap">
        <h1>TransGamers Minecraft SMP Settings</h1>
        
        <form method="post" action="">
            <?php wp_nonce_field('transgamers_settings_nonce'); ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row">Donation Product</th>
                    <td>
                        <select name="donation_product_id" id="donation_product_id">
                            <option value="0">Select a product...</option>
                            <?php foreach ($products as $product) : ?>
                                <option value="<?php echo $product->get_id(); ?>" 
                                        <?php selected($donation_product_id, $product->get_id()); ?>>
                                    <?php echo $product->get_name(); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="description">Select the WooCommerce product that will be used for donations.</p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Discord Webhook URL</th>
                    <td>
                        <input type="url" name="discord_webhook_url" value="<?php echo esc_attr($discord_webhook_url); ?>" 
                               class="regular-text" placeholder="https://discord.com/api/webhooks/..." />
                        <p class="description">Enter your Discord webhook URL to receive donation notifications.</p>
                        <input type="submit" name="test_discord" value="Test Discord Webhook" class="button button-secondary" />
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Minecraft Server Host</th>
                    <td>
                        <input type="text" name="minecraft_server_host" value="<?php echo esc_attr($minecraft_server_host); ?>" 
                               class="regular-text" placeholder="localhost or IP address" />
                        <p class="description">Enter your Minecraft server hostname or IP address.</p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Minecraft RCON Port</th>
                    <td>
                        <input type="number" name="minecraft_server_port" value="<?php echo esc_attr($minecraft_server_port); ?>" 
                               class="small-text" placeholder="25575" />
                        <p class="description">Enter your Minecraft server RCON port (default: 25575).</p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Minecraft RCON Password</th>
                    <td>
                        <input type="password" name="minecraft_rcon_password" value="<?php echo esc_attr($minecraft_rcon_password); ?>" 
                               class="regular-text" />
                        <p class="description">Enter your Minecraft server RCON password.</p>
                        <input type="submit" name="test_minecraft" value="Test Minecraft Connection" class="button button-secondary" />
                    </td>
                </tr>
            </table>
            
            <?php submit_button(); ?>
        </form>
        
        <hr>
        
        <h2>Setup Instructions</h2>
        <div class="postbox">
            <div class="inside">
                <h3>1. WooCommerce Setup</h3>
                <ol>
                    <li>Create a new WooCommerce product for donations</li>
                    <li>Set it as a "Simple Product" and mark it as "Virtual"</li>
                    <li>Enable "Customer defined pricing" if your theme supports it</li>
                    <li>Select this product in the dropdown above</li>
                </ol>
                
                <h3>2. Discord Setup</h3>
                <ol>
                    <li>Go to your Discord server settings</li>
                    <li>Navigate to Integrations → Webhooks</li>
                    <li>Create a new webhook for your announcements channel</li>
                    <li>Copy the webhook URL and paste it above</li>
                </ol>
                
                <h3>3. Minecraft Server Setup</h3>
                <ol>
                    <li>Enable RCON in your server.properties file</li>
                    <li>Set: <code>enable-rcon=true</code></li>
                    <li>Set: <code>rcon.port=25575</code> (or your preferred port)</li>
                    <li>Set: <code>rcon.password=yourpassword</code></li>
                    <li>Restart your server and enter the details above</li>
                </ol>
                
                <h3>4. LuckPerms Setup</h3>
                <ol>
                    <li>Ensure LuckPerms is installed on your server</li>
                    <li>Create a "donor" group: <code>/lp creategroup donor</code></li>
                    <li>Add permissions to the donor group as needed</li>
                    <li>Test the connection above</li>
                </ol>
            </div>
        </div>
    </div>
    <?php
}

// Donations admin page
function transgamers_donations_page() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'transgamers_donations';
    $donations = $wpdb->get_results("SELECT * FROM $table_name ORDER BY donation_date DESC LIMIT 50");
    ?>
    
    <div class="wrap">
        <h1>Recent Donations</h1>
        
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Order ID</th>
                    <th>Amount</th>
                    <th>Discord Username</th>
                    <th>Minecraft Username</th>
                    <th>Discord Notified</th>
                    <th>MC Role Added</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($donations)) : ?>
                    <tr>
                        <td colspan="7">No donations found.</td>
                    </tr>
                <?php else : ?>
                    <?php foreach ($donations as $donation) : ?>
                        <tr>
                            <td><?php echo date('M j, Y g:i A', strtotime($donation->donation_date)); ?></td>
                            <td>
                                <a href="<?php echo admin_url('post.php?post=' . $donation->order_id . '&action=edit'); ?>">
                                    #<?php echo $donation->order_id; ?>
                                </a>
                            </td>
                            <td>$<?php echo number_format($donation->donation_amount, 2); ?></td>
                            <td><?php echo esc_html($donation->discord_username ?: '-'); ?></td>
                            <td><?php echo esc_html($donation->minecraft_username ?: '-'); ?></td>
                            <td>
                                <?php if ($donation->webhook_sent) : ?>
                                    <span style="color: green;">✓ Yes</span>
                                <?php else : ?>
                                    <span style="color: red;">✗ No</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($donation->minecraft_role_added) : ?>
                                    <span style="color: green;">✓ Yes</span>
                                <?php else : ?>
                                    <span style="color: red;">✗ No</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php
}