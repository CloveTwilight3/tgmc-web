<?php
get_header();
?>

<div class="container">
    <header class="page-header">
        <h1 class="page-title">Frequently Asked Questions</h1>
        <p class="page-description">Find answers to common questions about our Minecraft SMP server!</p>
    </header>

    <div class="faq-search">
        <input type="text" id="faq-search" placeholder="Search FAQs..." />
    </div>

    <div class="faq-list">
        <?php if (have_posts()) : ?>
            <?php while (have_posts()) : the_post(); ?>
                <div class="faq-item">
                    <h3 class="faq-question">
                        <button class="faq-toggle" aria-expanded="false">
                            <?php the_title(); ?>
                            <span class="faq-icon">+</span>
                        </button>
                    </h3>
                    <div class="faq-answer" style="display: none;">
                        <?php the_content(); ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else : ?>
            <p>No FAQs found.</p>
        <?php endif; ?>
    </div>
</div>

<style>
.faq-search {
    margin: 2rem 0;
}

#faq-search {
    width: 100%;
    max-width: 500px;
    padding: 1rem;
    border: 2px solid #ddd;
    border-radius: 8px;
    font-size: 1rem;
}

.faq-list {
    margin: 2rem 0;
}

.faq-item {
    border: 1px solid #ddd;
    border-radius: 8px;
    margin-bottom: 1rem;
    overflow: hidden;
}

.faq-question {
    margin: 0;
}

.faq-toggle {
    width: 100%;
    padding: 1.5rem;
    background: #f8f9fa;
    border: none;
    text-align: left;
    font-size: 1.1rem;
    font-weight: bold;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.faq-toggle:hover {
    background: #e9ecef;
}

.faq-icon {
    font-size: 1.5rem;
    transition: transform 0.3s ease;
}

.faq-toggle[aria-expanded="true"] .faq-icon {
    transform: rotate(45deg);
}

.faq-answer {
    padding: 1.5rem;
    background: white;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // FAQ Toggle functionality
    document.querySelectorAll('.faq-toggle').forEach(button => {
        button.addEventListener('click', function() {
            const answer = this.closest('.faq-item').querySelector('.faq-answer');
            const isExpanded = this.getAttribute('aria-expanded') === 'true';
            
            this.setAttribute('aria-expanded', !isExpanded);
            answer.style.display = isExpanded ? 'none' : 'block';
        });
    });
    
    // FAQ Search functionality
    const searchInput = document.getElementById('faq-search');
    const faqItems = document.querySelectorAll('.faq-item');
    
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        
        faqItems.forEach(item => {
            const question = item.querySelector('.faq-question').textContent.toLowerCase();
            const answer = item.querySelector('.faq-answer').textContent.toLowerCase();
            
            if (question.includes(searchTerm) || answer.includes(searchTerm)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });
});
</script>

<?php get_footer(); ?>