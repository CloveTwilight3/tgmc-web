<?php
get_header();
?>

<div class="container">
    <header class="page-header">
        <h1 class="page-title">Server Addons</h1>
        <p class="page-description">Explore the datapacks and plugins that enhance our Minecraft SMP experience!</p>
    </header>

    <div class="server-addons-grid">
        <?php if (have_posts()) : ?>
            <?php while (have_posts()) : the_post(); ?>
                <div class="addon-card">
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="addon-thumbnail">
                            <?php the_post_thumbnail('medium'); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="addon-content">
                        <h2 class="addon-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h2>
                        
                        <div class="addon-meta">
                            <span class="addon-type">
                                <?php 
                                $addon_type = get_post_meta(get_the_ID(), '_addon_type', true);
                                echo $addon_type ? ucfirst($addon_type) : 'Addon';
                                ?>
                            </span>
                            
                            <?php 
                            $addon_version = get_post_meta(get_the_ID(), '_addon_version', true);
                            if ($addon_version) : ?>
                                <span class="addon-version">v<?php echo esc_html($addon_version); ?></span>
                            <?php endif; ?>
                            
                            <?php 
                            $addon_author = get_post_meta(get_the_ID(), '_addon_author', true);
                            if ($addon_author) : ?>
                                <span class="addon-author">by <?php echo esc_html($addon_author); ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="addon-excerpt">
                            <?php the_excerpt(); ?>
                        </div>
                        
                        <div class="addon-actions">
                            <a href="<?php the_permalink(); ?>" class="btn btn-primary">Learn More</a>
                            
                            <?php 
                            $download_url = get_post_meta(get_the_ID(), '_addon_download_url', true);
                            if ($download_url) : ?>
                                <a href="<?php echo esc_url($download_url); ?>" class="btn btn-secondary" target="_blank">Download</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else : ?>
            <p>No server addons found.</p>
        <?php endif; ?>
    </div>
    
    <?php
    the_posts_pagination(array(
        'prev_text' => '← Previous',
        'next_text' => 'Next →',
    ));
    ?>
</div>

<style>
.server-addons-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    margin: 2rem 0;
}

.addon-card {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    overflow: hidden;
    transition: transform 0.3s ease;
}

.addon-card:hover {
    transform: translateY(-5px);
}

.addon-thumbnail img {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.addon-content {
    padding: 1.5rem;
}

.addon-title a {
    text-decoration: none;
    color: #333;
}

.addon-meta {
    display: flex;
    gap: 1rem;
    margin: 1rem 0;
    font-size: 0.9rem;
}

.addon-type {
    background: #007cba;
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
}

.addon-version, .addon-author {
    color: #666;
}

.addon-actions {
    display: flex;
    gap: 1rem;
    margin-top: 1rem;
}

.btn {
    padding: 0.5rem 1rem;
    text-decoration: none;
    border-radius: 4px;
    font-size: 0.9rem;
}

.btn-primary {
    background: #007cba;
    color: white;
}

.btn-secondary {
    background: #666;
    color: white;
}
</style>

<?php get_footer(); ?>