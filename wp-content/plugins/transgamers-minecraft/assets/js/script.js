// TransGamers Minecraft SMP JavaScript Functions

(function($) {
    'use strict';
    
    // Initialize when document is ready
    $(document).ready(function() {
        initDonationForm();
        initServerStatus();
        initLiveMapButton();
    });
    
    // Initialize donation form functionality
    function initDonationForm() {
        // Handle predefined amount selection
        $('.amount-option').on('click', function() {
            $('.amount-option').removeClass('selected');
            $(this).addClass('selected');
            
            const amount = $(this).data('amount');
            $('#custom-amount').val(amount);
            updateDonationButton(amount);
        });
        
        // Handle custom amount input
        $('#custom-amount').on('input', function() {
            $('.amount-option').removeClass('selected');
            const amount = parseFloat($(this).val()) || 0;
            updateDonationButton(amount);
        });
        
        // Validate usernames on blur
        $('#discord_username').on('blur', validateDiscordUsername);
        $('#minecraft_username').on('blur', validateMinecraftUsername);
    }
    
    // Update donation button text and state
    function updateDonationButton(amount) {
        const button = $('.single_add_to_cart_button');
        if (amount > 0) {
            button.text(`Donate $${amount.toFixed(2)}`);
            button.prop('disabled', false);
        } else {
            button.text('Enter Amount');
            button.prop('disabled', true);
        }
    }
    
    // Validate Discord username format
    function validateDiscordUsername() {
        const username = $(this).val().trim();
        const feedback = $('#discord-feedback');
        
        if (username && !username.match(/^.{1,32}#\d{4}$/)) {
            showFieldError($(this), 'Please use format: Username#1234');
            return false;
        } else {
            clearFieldError($(this));
            return true;
        }
    }
    
    // Validate Minecraft username format
    function validateMinecraftUsername() {
        const username = $(this).val().trim();
        
        if (username && !username.match(/^[a-zA-Z0-9_]{3,16}$/)) {
            showFieldError($(this), 'Minecraft usernames must be 3-16 characters (letters, numbers, underscore only)');
            return false;
        } else {
            clearFieldError($(this));
            return true;
        }
    }
    
    // Show field error
    function showFieldError(field, message) {
        field.addClass('error');
        let errorDiv = field.siblings('.field-error');
        if (errorDiv.length === 0) {
            errorDiv = $('<div class="field-error"></div>');
            field.after(errorDiv);
        }
        errorDiv.text(message);
    }
    
    // Clear field error
    function clearFieldError(field) {
        field.removeClass('error');
        field.siblings('.field-error').remove();
    }
    
    // Initialize server status checking
    function initServerStatus() {
        if ($('.server-status').length > 0) {
            checkServerStatus();
            // Check every 5 minutes
            setInterval(checkServerStatus, 300000);
        }
    }
    
    // Check Minecraft server status
    function checkServerStatus() {
        $.ajax({
            url: transgamers_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'check_server_status',
                nonce: transgamers_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    updateServerStatus(response.data);
                }
            },
            error: function() {
                updateServerStatus({
                    online: false,
                    players: 0,
                    max_players: 0
                });
            }
        });
    }
    
    // Update server status display
    function updateServerStatus(status) {
        const statusElement = $('.server-status');
        const indicatorElement = $('.server-status-indicator');
        
        if (status.online) {
            statusElement.removeClass('offline').addClass('online');
            statusElement.find('.status-text').text(`Server Online - ${status.players}/${status.max_players} players`);
        } else {
            statusElement.removeClass('online').addClass('offline');
            statusElement.find('.status-text').text('Server Offline');
        }
    }
    
    // Initialize live map button
    function initLiveMapButton() {
        $('.live-map-button').on('click', function(e) {
            // Add click tracking if needed
            if (typeof gtag !== 'undefined') {
                gtag('event', 'click', {
                    event_category: 'Live Map',
                    event_label: 'Navigation'
                });
            }
        });
    }
    
    // Utility function to show notifications
    window.showTransGamersNotification = function(message, type = 'info') {
        const notification = $(`
            <div class="transgamers-message ${type}">
                ${message}
                <button class="close-notification" style="float: right; background: none; border: none; font-size: 1.2rem; cursor: pointer;">&times;</button>
            </div>
        `);
        
        $('body').prepend(notification);
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
            notification.fadeOut(() => notification.remove());
        }, 5000);
        
        // Manual close
        notification.find('.close-notification').on('click', function() {
            notification.fadeOut(() => notification.remove());
        });
    };
    
    // AJAX handler for server status checking
    window.transgamersCheckServerStatus = function() {
        return $.ajax({
            url: transgamers_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'check_server_status',
                nonce: transgamers_ajax.nonce
            }
        });
    };
    
})(jQuery);

// Add styles for error states
const errorStyles = `
<style>
.field-error {
    color: #721c24;
    font-size: 0.85em;
    margin-top: 5px;
    display: block;
}

.form-control.error {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

.amount-option {
    cursor: pointer;
    user-select: none;
}

.server-status {
    transition: all 0.3s ease;
}

.notification-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 10000;
    max-width: 400px;
}

@media (max-width: 768px) {
    .notification-container {
        left: 20px;
        right: 20px;
        max-width: none;
    }
}
</style>
`;

// Inject error styles
if (!document.getElementById('transgamers-error-styles')) {
    const styleElement = document.createElement('div');
    styleElement.id = 'transgamers-error-styles';
    styleElement.innerHTML = errorStyles;
    document.head.appendChild(styleElement);
}