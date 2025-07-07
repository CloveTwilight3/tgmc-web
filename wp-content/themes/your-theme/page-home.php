<?php
/*
Template Name: TransGamers Homepage
*/

get_header();
?>

<div class="transgamers-hero">
    <div class="transgamers-container">
        <h1>Welcome to r/TransGamers Minecraft SMP</h1>
        <p>A safe, inclusive community for transgender gamers and allies to build, explore, and create together!</p>
        
        <div class="hero-actions">
            <a href="/server-addons" class="btn btn-primary">View Server Addons</a>
            <a href="http://mc.transgamers.org:8100" class="live-map-button" target="_blank">🗺️ View Live Map</a>
        </div>
    </div>
</div>

<div class="transgamers-container">
    <!-- Server Status Widget -->
    <div class="server-status">
        <div class="server-status-indicator"></div>
        <span class="status-text">Checking server status...</span>
    </div>
    
    <!-- Server Stats -->
    <div class="transgamers-stats">
        <div class="stat-card">
            <span class="stat-number" id="online-players">-</span>
            <span class="stat-label">Players Online</span>
        </div>
        <div class="stat-card">
            <span class="stat-number"><?php echo get_option('transgamers_total_members', '50+'); ?></span>
            <span class="stat-label">Community Members</span>
        </div>
        <div class="stat-card">
            <span class="stat-number"><?php echo get_option('transgamers_uptime', '99.9'); ?>%</span>
            <span class="stat-label">Server Uptime</span>
        </div>
        <div class="stat-card">
            <span class="stat-number"><?php echo wp_count_posts('server_addons')->publish; ?></span>
            <span class="stat-label">Server Addons</span>
        </div>
    </div>
    
    <!-- Featured Content -->
    <div class="featured-sections">
        <div class="featured-section">
            <h2>🔧 Latest Server Addons</h2>
            <div class="addon-preview">
                <?php
                $recent_addons = new WP_Query(array(
                    'post_type' => 'server_addons',
                    'posts_per_page' => 3,
                    'post_status' => 'publish'
                ));
                
                if ($recent_addons->have_posts()) :
                    while ($recent_addons->have_posts()) : $recent_addons->the_post();
                ?>
                    <div class="addon-card-mini">
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="addon-thumb">
                                <?php the_post_thumbnail('thumbnail'); ?>
                            </div>
                        <?php endif; ?>
                        <div class="addon-info">
                            <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                            <p><?php echo wp_trim_words(get_the_excerpt(), 15); ?></p>
                        </div>
                    </div>
                <?php
                    endwhile;
                    wp_reset_postdata();
                else :
                ?>
                    <p>No addons available yet. Check back soon!</p>
                <?php endif; ?>
            </div>
            <a href="/server-addons" class="view-all-link">View All Addons →</a>
        </div>
        
        <div class="featured-section">
            <h2>❓ Popular FAQs</h2>
            <div class="faq-preview">
                <?php
                $recent_faqs = new WP_Query(array(
                    'post_type' => 'faqs',
                    'posts_per_page' => 5,
                    'post_status' => 'publish'
                ));
                
                if ($recent_faqs->have_posts()) :
                    while ($recent_faqs->have_posts()) : $recent_faqs->the_post();
                ?>
                    <div class="faq-item-mini">
                        <h4><?php the_title(); ?></h4>
                        <p><?php echo wp_trim_words(get_the_content(), 20); ?></p>
                    </div>
                <?php
                    endwhile;
                    wp_reset_postdata();
                else :
                ?>
                    <p>No FAQs available yet.</p>
                <?php endif; ?>
            </div>
            <a href="/faqs" class="view-all-link">View All FAQs →</a>
        </div>
    </div>
    
    <!-- Support the Server -->
    <div class="support-section">
        <h2>💝 Support Our Community</h2>
        <p>Help us keep the server running and improve the experience for everyone!</p>
        
        <div class="donation-form-container">
            <h3>Make a Donation</h3>
            <p>Your support helps us maintain the server, add new features, and keep our community thriving!</p>
            
            <?php
            $donation_product_id = get_option('transgamers_donation_product_id', 0);
            if ($donation_product_id && function_exists('wc_get_product')) :
                $product = wc_get_product($donation_product_id);
                if ($product) :
            ?>
                <div class="donation-quick-amounts">
                    <div class="amount-option" data-amount="5">$5</div>
                    <div class="amount-option" data-amount="10">$10</div>
                    <div class="amount-option" data-amount="25">$25</div>
                    <div class="amount-option" data-amount="50">$50</div>
                </div>
                
                <a href="<?php echo get_permalink($donation_product_id); ?>" class="btn btn-primary btn-large">
                    🎁 Donate Now
                </a>
            <?php
                endif;
            else :
            ?>
                <p><em>Donation system is being set up. Check back soon!</em></p>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Recent Activity (if you want to add this later) -->
    <div class="recent-activity">
        <h2>📈 Recent Server Activity</h2>
        <div id="recent-activity-feed">
            <p>Loading recent activity...</p>
        </div>
    </div>
</div>

<style>
.hero-actions {
    margin-top: 2rem;
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}

.btn {
    padding: 1rem 2rem;
    text-decoration: none;
    border-radius: 8px;
    font-weight: bold;
    display: inline-block;
    transition: all 0.3s ease;
}

.btn-primary {
    background: #007cba;
    color: white;
}

.btn-primary:hover {
    background: #005a87;
    transform: translateY(-2px);
}

.btn-large {
    font-size: 1.2rem;
    padding: 1.25rem 2.5rem;
}

.featured-sections {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 3rem;
    margin: 3rem 0;
}

.featured-section {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

.addon-preview, .faq-preview {
    margin: 1.5rem 0;
}

.addon-card-mini {
    display: flex;
    gap: 1rem;
    margin-bottom: 1.5rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid #eee;
}

.addon-card-mini:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.addon-thumb img {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 4px;
}

.addon-info h3 {
    margin: 0 0 0.5rem 0;
    font-size: 1rem;
}

.addon-info a {
    text-decoration: none;
    color: #333;
}

.addon-info p {
    margin: 0;
    color: #666;
    font-size: 0.9rem;
}

.faq-item-mini {
    margin-bottom: 1.5rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid #eee;
}

.faq-item-mini:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.faq-item-mini h4 {
    margin: 0 0 0.5rem 0;
    color: #333;
    font-size: 1rem;
}

.faq-item-mini p {
    margin: 0;
    color: #666;
    font-size: 0.9rem;
}

.view-all-link {
    color: #007cba;
    text-decoration: none;
    font-weight: bold;
}

.view-all-link:hover {
    text-decoration: underline;
}

.support-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 3rem;
    border-radius: 12px;
    text-align: center;
    margin: 3rem 0;
}

.donation-quick-amounts {
    display: flex;
    justify-content: center;
    gap: 1rem;
    margin: 2rem 0;
    flex-wrap: wrap;
}

.donation-quick-amounts .amount-option {
    background: rgba(255,255,255,0.2);
    color: white;
    padding: 1rem 1.5rem;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.donation-quick-amounts .amount-option:hover {
    background: rgba(255,255,255,0.3);
    transform: translateY(-2px);
}

.recent-activity {
    background: #f8f9fa;
    padding: 2rem;
    border-radius: 12px;
    margin: 3rem 0;
}

@media (max-width: 768px) {
    .featured-sections {
        grid-template-columns: 1fr;
        gap: 2rem;
    }
    
    .hero-actions {
        flex-direction: column;
        align-items: center;
    }
    
    .donation-quick-amounts {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    // Load server status on page load
    transgamersCheckServerStatus().done(function(response) {
        if (response.success && response.data) {
            $('#online-players').text(response.data.players || '0');
        }
    });
    
    // Quick donation amount selection
    $('.donation-quick-amounts .amount-option').on('click', function() {
        const amount = $(this).data('amount');
        // Store selected amount in localStorage for the donation page
        localStorage.setItem('transgamers_selected_amount', amount);
        
        // Navigate to donation page
        window.location.href = $('.btn[href*="donate"]').attr('href') || '/donate';
    });
});
</script>

<?php get_footer(); ?>