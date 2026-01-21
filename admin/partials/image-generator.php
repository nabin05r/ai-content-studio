<?php
/**
 * Image Generator Tab Template
 *
 * @package AI_Content_Studio
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get settings
$settings = ai_studio_get_settings();
?>

<div class="ai-studio-container">
    
    <!-- Left Panel: Image Generation Form -->
    <div class="ai-studio-input-panel">
        <div class="ai-studio-card">
            <h2><?php _e('Image Generation', 'ai-content-studio'); ?></h2>
            
            <form id="ai-image-form">
                
                <!-- Image Prompt -->
                <div class="form-group">
                    <label for="image-prompt">
                        <?php _e('Image Description', 'ai-content-studio'); ?>
                        <span class="required">*</span>
                    </label>
                    <textarea 
                        id="image-prompt" 
                        name="prompt" 
                        rows="5" 
                        class="large-text"
                        placeholder="<?php esc_attr_e('Describe the image you want to generate...', 'ai-content-studio'); ?>"
                        required
                    ></textarea>
                    <p class="description">
                        <?php _e('Be as detailed as possible. Example: "A futuristic city at sunset with flying cars and neon lights"', 'ai-content-studio'); ?>
                    </p>
                </div>
                
               <!-- AI Provider Info -->
                <div class="form-group">
                    <label>
                        <?php _e('AI Provider', 'ai-content-studio'); ?>
                    </label>
                    <div style="padding: 10px; background: #f0f6fc; border-left: 4px solid #2271b1; border-radius: 4px;">
                        <strong><?php _e('Pollinations.ai', 'ai-content-studio'); ?></strong>
                        <p class="description" style="margin: 5px 0 0 0;">
                            <?php _e('100% FREE with automatic Craiyon backup. Perfect companion to Gemini!', 'ai-content-studio'); ?><br>
                            <em><?php _e('May take 30-90 seconds. Please be patient during generation.', 'ai-content-studio'); ?></em>
                        </p>
                    </div>
                    <input type="hidden" id="image-provider" name="provider" value="pollinations" />
                </div>

                <!-- Generate Button -->
                <div class="form-group">
                    <button 
                        type="submit" 
                        id="generate-image-btn" 
                        class="button button-primary button-large"
                    >
                        <span class="dashicons dashicons-format-image"></span>
                        <?php _e('Generate Image', 'ai-content-studio'); ?>
                    </button>
                </div>
                
            </form>
        </div>
    </div>
    
    <!-- Right Panel: Image Output -->
    <div class="ai-studio-output-panel">
        <div class="ai-studio-card">
            <h2><?php _e('Generated Image', 'ai-content-studio'); ?></h2>
            
            <!-- Loading State -->
            <div id="image-loading-state" class="ai-studio-state" style="display: none;">
                <div class="ai-studio-spinner">
                    <span class="spinner is-active"></span>
                </div>
                <p><?php _e('Generating your image with AI...', 'ai-content-studio'); ?></p>
                <p class="description"><?php _e('This may take 30-90 seconds. Please be patient!', 'ai-content-studio'); ?></p>
            </div>
            
            <!-- Empty State -->
            <div id="image-empty-state" class="ai-studio-state">
                <span class="dashicons dashicons-format-image" style="font-size: 48px; opacity: 0.3;"></span>
                <p><?php _e('Your generated image will appear here', 'ai-content-studio'); ?></p>
                <p class="description">
                    <?php _e('Describe your image and click "Generate Image" to get started', 'ai-content-studio'); ?>
                </p>
            </div>
            
            <!-- Error State -->
            <div id="image-error-state" class="ai-studio-state ai-studio-error" style="display: none;">
                <span class="dashicons dashicons-warning" style="font-size: 48px; color: #dc3232;"></span>
                <p id="image-error-message"><?php _e('An error occurred', 'ai-content-studio'); ?></p>
                <button type="button" class="button" onclick="jQuery('#image-error-state').hide(); jQuery('#image-empty-state').show();">
                    <?php _e('Try Again', 'ai-content-studio'); ?>
                </button>
            </div>
            
            <!-- Success State -->
            <div id="image-output" class="ai-studio-output" style="display: none;">
                
                <!-- Generated Image Display -->
                <div class="image-preview-section">
                    <img id="generated-image" src="" alt="Generated Image" style="max-width: 100%; height: auto; border-radius: 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);" />
                </div>
                
                <!-- Image Metadata -->
                <div class="output-meta" style="margin-top: 20px;">
                    <span>
                        <strong><?php _e('Provider:', 'ai-content-studio'); ?></strong> 
                        <span id="image-provider-value"></span>
                    </span>
                    <span>
                        <strong><?php _e('Size:', 'ai-content-studio'); ?></strong> 
                        <span id="image-size-value">1024×1024</span>
                    </span>
                    <span>
                        <strong><?php _e('Generated in:', 'ai-content-studio'); ?></strong> 
                        <span id="image-time-value">0s</span>
                    </span>
                </div>
                
                <!-- Action Buttons -->
                <div class="output-actions">
                    <button type="button" id="download-image-btn" class="button button-primary">
                        <span class="dashicons dashicons-download"></span>
                        <?php _e('Download Image', 'ai-content-studio'); ?>
                    </button>
                    
                    <button type="button" id="upload-to-media-btn" class="button button-secondary">
                        <span class="dashicons dashicons-upload"></span>
                        <?php _e('Upload to Media Library', 'ai-content-studio'); ?>
                    </button>
                    
                    <button type="button" id="regenerate-image-btn" class="button">
                        <span class="dashicons dashicons-update"></span>
                        <?php _e('Regenerate', 'ai-content-studio'); ?>
                    </button>
                    
                    <button type="button" id="copy-image-url-btn" class="button">
                        <span class="dashicons dashicons-admin-links"></span>
                        <?php _e('Copy URL', 'ai-content-studio'); ?>
                    </button>
                </div>
                
                <!-- Prompt Used -->
                <div style="margin-top: 20px; padding: 15px; background: #f0f6fc; border-radius: 4px;">
                    <strong><?php _e('Prompt used:', 'ai-content-studio'); ?></strong>
                    <p id="image-prompt-used" style="margin: 5px 0 0 0; color: #666;"></p>
                </div>
                
            </div>
        </div>
    </div>
    
</div>