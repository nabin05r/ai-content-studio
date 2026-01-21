(function($) {
    'use strict';

    let generatedContent = null;
    let generationStartTime = null;

    let generatedImage = null;
    let imageGenerationStartTime = null;

    $(document).ready(function() {
        
        // Handle form submission
        $('#ai-content-form').on('submit', function(e) {
            e.preventDefault();
            generateContent();
        });

        // Handle regenerate button
        $('#regenerate-btn').on('click', function() {
            if (confirm(aiStudio.i18n.confirmRegenerate)) {
                generateContent();
            }
        });

        // Handle save as draft
        $('#save-draft-btn').on('click', function() {
            savePost('draft');
        });

        // Handle publish
        $('#publish-btn').on('click', function() {
            if (confirm('Are you sure you want to publish this post immediately?')) {
                savePost('publish');
            }
        });

        // Handle copy to clipboard
        $('#copy-btn').on('click', function() {
            copyToClipboard();
        });

        // Tab switching
        $('.nav-tab').on('click', function(e) {
            e.preventDefault();
            const tab = $(this).data('tab');
            
            // Update tabs
            $('.nav-tab').removeClass('nav-tab-active');
            $(this).addClass('nav-tab-active');
            
            // Update content
            $('.tab-content').removeClass('active').hide();
            $('#' + tab + '-tab').addClass('active').show();
            
            // Show/hide DALL-E specific options if on image tab
            if (tab === 'image') {
                updateImageProviderOptions();
            }
        });
        
        // Image provider change
        $('#image-provider').on('change', function() {
            updateImageProviderOptions();
        });
        
        // Image form submission
        $('#ai-image-form').on('submit', function(e) {
            e.preventDefault();
            generateImage();
        });
        
        // Regenerate image
        $('#regenerate-image-btn').on('click', function() {
            if (confirm('Generate a new image with the same prompt?')) {
                generateImage();
            }
        });
        
        // Download image
        $('#download-image-btn').on('click', function() {
            downloadImage();
        });
        
        // Upload to media library
        $('#upload-to-media-btn').on('click', function() {
            uploadToMediaLibrary();
        });
        
        // Copy image URL
        $('#copy-image-url-btn').on('click', function() {
            copyImageUrl();
        });

    });

    /**
     * Generate content via AJAX
     */
    function generateContent() {
        // Get form data
        const formData = {
            action: 'ai_studio_generate_content',
            nonce: aiStudio.nonce,
            title: $('#content-title').val().trim(),
            description: $('#content-description').val().trim(),
            provider: $('#ai-provider').val(),
            tone: $('#content-tone').val(),
            word_count: $('#word-count').val()
        };

        // Validate title
        if (!formData.title) {
            alert('Please enter a post title');
            $('#content-title').focus();
            return;
        }

        // Show loading state
        showState('loading');
        generationStartTime = Date.now();

        // Disable generate button
        $('#generate-btn').prop('disabled', true).addClass('updating-message');

        // Make AJAX request
        $.ajax({
            url: aiStudio.ajax_url,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    generatedContent = response.data;
                    displayGeneratedContent(response.data);
                    showState('success');
                } else {
                    showError(response.data.message || aiStudio.i18n.error);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                showError(aiStudio.i18n.error);
            },
            complete: function() {
                $('#generate-btn').prop('disabled', false).removeClass('updating-message');
            }
        });
    }

    /**
     * Display generated content
     */
    function displayGeneratedContent(data) {
        // Set title
        $('#generated-title').val(data.title);

        // Set content
        $('#generated-content').html(data.content);

        // Set metadata
        $('#word-count-value').text(data.word_count || 0);
        
        const generationTime = ((Date.now() - generationStartTime) / 1000).toFixed(1);
        $('#time-value').text(generationTime + 's');
    }

    /**
     * Save post to WordPress
     */
    function savePost(status) {
        if (!generatedContent) {
            alert('No content to save');
            return;
        }

        const $button = status === 'draft' ? $('#save-draft-btn') : $('#publish-btn');
        const originalText = $button.html();
        
        // Disable button and show loading
        $button.prop('disabled', true).html(
            '<span class="spinner is-active" style="float:none; margin:0 5px 0 0;"></span>' +
            (status === 'draft' ? 'Saving...' : 'Publishing...')
        );

        $.ajax({
            url: aiStudio.ajax_url,
            type: 'POST',
            data: {
                action: 'ai_studio_save_post',
                nonce: aiStudio.nonce,
                title: generatedContent.title,
                content: generatedContent.content,
                status: status
            },
            success: function(response) {
                if (response.success) {
                    // Change button to success state
                    const successText = status === 'draft' 
                        ? '<span class="dashicons dashicons-yes"></span> Saved as Draft!' 
                        : '<span class="dashicons dashicons-yes"></span> Published!';
                    
                    $button.html(successText).addClass('button-success');
                    
                    // Show notification with link to edit
                    const message = status === 'draft' 
                        ? 'Post saved as draft!' 
                        : 'Post published successfully!';
                    
                    showNotificationWithLink(
                        'success', 
                        message,
                        response.data.edit_url,
                        'View Post'
                    );
                    
                    // Reset after 3 seconds
                    setTimeout(function() {
                        $button.html(originalText).removeClass('button-success').prop('disabled', false);
                    }, 3000);
                    
                } else {
                    showNotification('error', response.data.message || 'Error saving post');
                    $button.html(originalText).prop('disabled', false);
                }
            },
            error: function() {
                showNotification('error', 'Network error. Please try again.');
                $button.html(originalText).prop('disabled', false);
            }
        });
    }

    /**
     * Copy content to clipboard
     */
    function copyToClipboard() {
        if (!generatedContent) {
            alert('No content to copy');
            return;
        }

        // Prepare text to copy
        const title = generatedContent.title;
        const content = $('#generated-content').text();
        const textToCopy = title + '\n\n' + content;

        // Try modern clipboard API first
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(textToCopy).then(function() {
                showCopySuccess();
            }).catch(function() {
                // Fallback to old method
                copyToClipboardFallback(textToCopy);
            });
        } else {
            // Use fallback for older browsers
            copyToClipboardFallback(textToCopy);
        }
    }

    /**
     * Fallback copy method for older browsers
     */
    function copyToClipboardFallback(text) {
        const $temp = $('<textarea>');
        $('body').append($temp);
        $temp.val(text).select();
        
        try {
            document.execCommand('copy');
            showCopySuccess();
        } catch (err) {
            alert('Failed to copy. Please copy manually.');
        }
        
        $temp.remove();
    }

    /**
     * Show copy success feedback
     */
    function showCopySuccess() {
        const $btn = $('#copy-btn');
        const originalText = $btn.html();
        
        $btn.html('<span class="dashicons dashicons-yes"></span> Copied!').addClass('button-primary');
        
        setTimeout(function() {
            $btn.html(originalText).removeClass('button-primary');
        }, 2000);
        
        showNotification('success', 'Content copied to clipboard!');
    }

    /**
     * Show notification message with action link
     */
    function showNotificationWithLink(type, message, url, linkText) {
        // Remove existing notifications
        $('.ai-studio-notification').remove();
        
        const iconClass = type === 'success' ? 'dashicons-yes-alt' : 'dashicons-warning';
        const bgColor = type === 'success' ? '#00a32a' : '#dc3232';
        
        const linkHtml = url 
            ? `<a href="${url}" target="_blank" style="color: white; text-decoration: underline; margin-left: 10px;">${linkText}</a>`
            : '';
        
        const $notification = $('<div>', {
            'class': 'ai-studio-notification',
            'style': `
                position: fixed;
                top: 32px;
                right: 20px;
                background: ${bgColor};
                color: white;
                padding: 15px 20px;
                border-radius: 4px;
                box-shadow: 0 2px 8px rgba(0,0,0,0.2);
                z-index: 100000;
                display: flex;
                align-items: center;
                gap: 10px;
                animation: slideIn 0.3s ease-out;
                max-width: 400px;
            `,
            'html': `
                <span class="dashicons ${iconClass}" style="font-size: 20px;"></span>
                <span>${message}${linkHtml}</span>
            `
        });
        
        $('body').append($notification);
        
        // Auto remove after 5 seconds
        setTimeout(function() {
            $notification.fadeOut(300, function() {
                $(this).remove();
            });
        }, 5000);
    }

    /**
     * Show notification (simple)
     */
    function showNotification(type, message) {
        showNotificationWithLink(type, message, null, null);
    }

    /**
     * Show different UI states
     */
    function showState(state) {
        $('#loading-state, #empty-state, #error-state, #content-output').hide();

        switch(state) {
            case 'loading':
                $('#loading-state').show();
                break;
            case 'success':
                $('#content-output').show();
                break;
            case 'empty':
                $('#empty-state').show();
                break;
            case 'error':
                $('#error-state').show();
                break;
        }
    }

    /**
     * Show error message
     */
    function showError(message) {
        $('#error-message').text(message);
        showState('error');
    }

    /**
     * IMAGE GENERATION FUNCTIONS
     */

    /**
     * Show/hide provider-specific options
     */
    function updateImageProviderOptions() {
        const provider = $('#image-provider').val();
        const isDalle = provider === 'dalle';
        
        // Show DALL-E options only for DALL-E
        $('#image-size-group').toggle(isDalle);
        $('#image-style-group').toggle(isDalle);
        $('#image-quality-group').toggle(isDalle);
        
        // Hide model selection (not needed for free AI or DALL-E)
        $('#hf-model-group').hide();
    }

    /**
     * Generate image via AJAX
     */
    function generateImage() {
        const provider = $('#image-provider').val();
        const prompt = $('#image-prompt').val().trim();
        
        // Validate prompt
        if (!prompt) {
            alert('Please enter an image description');
            $('#image-prompt').focus();
            return;
        }
        
        const formData = {
            action: 'ai_studio_generate_image',
            nonce: aiStudio.nonce,
            prompt: prompt,
            provider: provider || 'pollinations' // Default to Pollinations
        };
        
        // Add DALL-E specific parameters
        if (provider === 'dalle') {
            formData.size = $('#image-size').val() || '1024x1024';
            formData.style = $('#image-style').val() || 'vivid';
            formData.quality = $('#image-quality').val() || 'standard';
        }
        
        // Show loading state
        showImageState('loading');
        imageGenerationStartTime = Date.now();
        
        // Disable button
        $('#generate-image-btn').prop('disabled', true).addClass('updating-message');
        
        // Make AJAX request with extended timeout
        $.ajax({
            url: aiStudio.ajax_url,
            type: 'POST',
            data: formData,
            timeout: 120000, // 2 minutes for free services
            success: function(response) {
                if (response.success) {
                    generatedImage = response.data;
                    displayGeneratedImage(response.data);
                    showImageState('success');
                } else {
                    showImageError(response.data.message || 'Error generating image');
                }
            },
            error: function(xhr, status, error) {
                console.error('Image generation error:', status, error);
                
                let errorMessage = 'Image generation failed. Please try again.';
                
                if (status === 'timeout') {
                    errorMessage = 'Generation timed out. The free service might be busy. Please wait 30 seconds and try again.';
                } else if (xhr.status === 500) {
                    errorMessage = 'Server error. Please try again.';
                }
                
                showImageError(errorMessage);
            },
            complete: function() {
                $('#generate-image-btn').prop('disabled', false).removeClass('updating-message');
            }
        });
    }

    /**
     * Display generated image
     */
    function displayGeneratedImage(data) {
        // Set image
        $('#generated-image').attr('src', data.url);
        
        // Set metadata
        $('#image-provider-value').text(data.model || data.provider || 'AI Generator');
        $('#image-size-value').text('1024×1024');
        $('#image-prompt-used').text(data.prompt || '');
        
        const generationTime = ((Date.now() - imageGenerationStartTime) / 1000).toFixed(1);
        $('#image-time-value').text(generationTime + 's');
    }

    /**
     * Download image
     */
    function downloadImage() {
        if (!generatedImage || !generatedImage.url) {
            alert('No image to download');
            return;
        }
        
        // Create download link
        const link = document.createElement('a');
        link.href = generatedImage.url;
        link.download = 'ai-generated-' + Date.now() + '.png';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        showNotification('success', 'Image download started!');
    }

    /**
     * Upload image to WordPress media library
     */
    function uploadToMediaLibrary() {
        if (!generatedImage || !generatedImage.url) {
            alert('No image to upload');
            return;
        }
        
        const $btn = $('#upload-to-media-btn');
        const originalText = $btn.html();
        
        $btn.prop('disabled', true).html(
            '<span class="spinner is-active" style="float:none; margin:0 5px 0 0;"></span>Uploading...'
        );
        
        $.ajax({
            url: aiStudio.ajax_url,
            type: 'POST',
            data: {
                action: 'ai_studio_upload_image',
                nonce: aiStudio.nonce,
                image_url: generatedImage.url,
                prompt: generatedImage.prompt
            },
            success: function(response) {
                if (response.success) {
                    $btn.html('<span class="dashicons dashicons-yes"></span> Uploaded!');
                    showNotificationWithLink(
                        'success',
                        'Image uploaded to media library!',
                        response.data.edit_url,
                        'View in Media Library'
                    );
                    
                    setTimeout(function() {
                        $btn.html(originalText).prop('disabled', false);
                    }, 3000);
                } else {
                    showNotification('error', response.data.message || 'Upload failed');
                    $btn.html(originalText).prop('disabled', false);
                }
            },
            error: function() {
                showNotification('error', 'Upload failed');
                $btn.html(originalText).prop('disabled', false);
            }
        });
    }

    /**
     * Copy image URL to clipboard
     */
    function copyImageUrl() {
        if (!generatedImage || !generatedImage.url) {
            alert('No image URL to copy');
            return;
        }
        
        if (navigator.clipboard) {
            navigator.clipboard.writeText(generatedImage.url).then(function() {
                showCopyImageSuccess();
            });
        } else {
            // Fallback
            const $temp = $('<input>');
            $('body').append($temp);
            $temp.val(generatedImage.url).select();
            document.execCommand('copy');
            $temp.remove();
            showCopyImageSuccess();
        }
    }

    /**
     * Show copy success for image URL
     */
    function showCopyImageSuccess() {
        const $btn = $('#copy-image-url-btn');
        const originalText = $btn.html();
        
        $btn.html('<span class="dashicons dashicons-yes"></span> Copied!').addClass('button-primary');
        
        setTimeout(function() {
            $btn.html(originalText).removeClass('button-primary');
        }, 2000);
        
        showNotification('success', 'Image URL copied to clipboard!');
    }

    /**
     * Show different image UI states
     */
    function showImageState(state) {
        $('#image-loading-state, #image-empty-state, #image-error-state, #image-output').hide();
        
        switch(state) {
            case 'loading':
                $('#image-loading-state').show();
                break;
            case 'success':
                $('#image-output').show();
                break;
            case 'empty':
                $('#image-empty-state').show();
                break;
            case 'error':
                $('#image-error-state').show();
                break;
        }
    }

    /**
     * Show image error message
     */
    function showImageError(message) {
        $('#image-error-message').text(message);
        showImageState('error');
    }

})(jQuery);