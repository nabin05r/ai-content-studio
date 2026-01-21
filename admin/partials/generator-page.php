<?php
/**
 * Content Generator Page Template
 *
 * @package AI_Content_Studio
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap ai-content-studio-generator">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <?php settings_errors('ai_studio_message'); ?>
    
    <!-- Tabs Navigation -->
    <nav class="nav-tab-wrapper wp-clearfix" aria-label="Generator tabs">
        <a href="#content-tab" class="nav-tab nav-tab-active" data-tab="content">
            <span class="dashicons dashicons-edit"></span>
            <?php _e('Generate Content', 'ai-content-studio'); ?>
        </a>
        <a href="#image-tab" class="nav-tab" data-tab="image">
            <span class="dashicons dashicons-format-image"></span>
            <?php _e('Generate Image', 'ai-content-studio'); ?>
        </a>
    </nav>
    
    <!-- Content Tab -->
    <div id="content-tab" class="tab-content active">
        <div class="ai-studio-container">
            
            <!-- Left Panel: Input Form -->
            <div class="ai-studio-input-panel">
                <div class="ai-studio-card">
                    <h2><?php _e('Content Details', 'ai-content-studio'); ?></h2>
                    
                    <form id="ai-content-form">
                        
                        <!-- Title Input -->
                        <div class="form-group">
                            <label for="content-title">
                                <?php _e('Post Title', 'ai-content-studio'); ?>
                                <span class="required">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="content-title" 
                                name="title" 
                                class="regular-text" 
                                placeholder="<?php esc_attr_e('Enter your post title...', 'ai-content-studio'); ?>"
                                required
                            />
                            <p class="description">
                                <?php _e('The main topic or title for your content', 'ai-content-studio'); ?>
                            </p>
                        </div>
                        
                        <!-- Description Input -->
                        <div class="form-group">
                            <label for="content-description">
                                <?php _e('Description / Keywords', 'ai-content-studio'); ?>
                            </label>
                            <textarea 
                                id="content-description" 
                                name="description" 
                                rows="4" 
                                class="large-text"
                                placeholder="<?php esc_attr_e('Provide additional context, keywords, or specific points to cover...', 'ai-content-studio'); ?>"
                            ></textarea>
                            <p class="description">
                                <?php _e('Optional: Add specific details, keywords, or points you want to include', 'ai-content-studio'); ?>
                            </p>
                        </div>
                        
                        <!-- AI Provider Selection -->
                        <div class="form-group">
                            <label for="ai-provider">
                                <?php _e('AI Provider', 'ai-content-studio'); ?>
                            </label>
                            <select id="ai-provider" name="provider" class="regular-text">
                                <?php foreach ($providers as $key => $provider): ?>
                                    <?php if (ai_studio_has_api_key($key)): ?>
                                        <option value="<?php echo esc_attr($key); ?>" 
                                            <?php selected($settings['default_model'], $key); ?>>
                                            <?php echo esc_html($provider['name']); ?>
                                            <?php if ($provider['free']): ?>
                                                (<?php _e('Free', 'ai-content-studio'); ?>)
                                            <?php endif; ?>
                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Tone Selection -->
                        <div class="form-group">
                            <label for="content-tone">
                                <?php _e('Tone', 'ai-content-studio'); ?>
                            </label>
                            <select id="content-tone" name="tone" class="regular-text">
                                <?php foreach ($tones as $key => $label): ?>
                                    <option value="<?php echo esc_attr($key); ?>"
                                        <?php selected($settings['default_tone'], $key); ?>>
                                        <?php echo esc_html($label); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Word Count Selection -->
                        <div class="form-group">
                            <label for="word-count">
                                <?php _e('Length', 'ai-content-studio'); ?>
                            </label>
                            <select id="word-count" name="word_count" class="regular-text">
                                <?php foreach ($word_counts as $key => $label): ?>
                                    <option value="<?php echo esc_attr($key); ?>"
                                        <?php selected($settings['default_length'], $key); ?>>
                                        <?php echo esc_html($label); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Generate Button -->
                        <div class="form-group">
                            <button 
                                type="submit" 
                                id="generate-btn" 
                                class="button button-primary button-large"
                                <?php echo !$has_api_key || !$rate_limit['allowed'] ? 'disabled' : ''; ?>
                            >
                                <span class="dashicons dashicons-admin-generic"></span>
                                <?php _e('Generate Content', 'ai-content-studio'); ?>
                            </button>
                            
                            <?php if ($rate_limit['allowed']): ?>
                                <p class="description rate-limit-info">
                                    <?php 
                                    printf(
                                        __('Generations today: %d / %d', 'ai-content-studio'),
                                        $rate_limit['used'],
                                        $rate_limit['limit']
                                    );
                                    ?>
                                </p>
                            <?php endif; ?>
                        </div>
                        
                    </form>
                </div>
            </div>
            
            <!-- Right Panel: Preview/Output -->
            <div class="ai-studio-output-panel">
                <div class="ai-studio-card">
                    <h2><?php _e('Generated Content', 'ai-content-studio'); ?></h2>
                    
                    <!-- Loading State -->
                    <div id="loading-state" class="ai-studio-state" style="display: none;">
                        <div class="ai-studio-spinner">
                            <span class="spinner is-active"></span>
                        </div>
                        <p><?php _e('Generating your content with AI...', 'ai-content-studio'); ?></p>
                        <p class="description"><?php _e('This may take 10-30 seconds', 'ai-content-studio'); ?></p>
                    </div>
                    
                    <!-- Empty State -->
                    <div id="empty-state" class="ai-studio-state">
                        <span class="dashicons dashicons-editor-help" style="font-size: 48px; opacity: 0.3;"></span>
                        <p><?php _e('Your generated content will appear here', 'ai-content-studio'); ?></p>
                        <p class="description">
                            <?php _e('Fill in the form and click "Generate Content" to get started', 'ai-content-studio'); ?>
                        </p>
                    </div>
                    
                    <!-- Error State -->
                    <div id="error-state" class="ai-studio-state ai-studio-error" style="display: none;">
                        <span class="dashicons dashicons-warning" style="font-size: 48px; color: #dc3232;"></span>
                        <p id="error-message"><?php _e('An error occurred', 'ai-content-studio'); ?></p>
                        <button type="button" class="button" onclick="location.reload();">
                            <?php _e('Try Again', 'ai-content-studio'); ?>
                        </button>
                    </div>
                    
                    <!-- Success State -->
                    <div id="content-output" class="ai-studio-output" style="display: none;">
                        
                        <!-- Generated Title -->
                        <div class="output-title-section">
                            <label><?php _e('Title', 'ai-content-studio'); ?></label>
                            <input 
                                type="text" 
                                id="generated-title" 
                                class="widefat"
                                readonly
                            />
                        </div>
                        
                        <!-- Generated Content -->
                        <div class="output-content-section">
                            <label><?php _e('Content', 'ai-content-studio'); ?></label>
                            <div id="generated-content" class="generated-content-preview"></div>
                        </div>
                        
                        <!-- Metadata -->
                        <div class="output-meta">
                            <span id="word-count-display">
                                <strong><?php _e('Words:', 'ai-content-studio'); ?></strong> 
                                <span id="word-count-value">0</span>
                            </span>
                            <span id="generation-time">
                                <strong><?php _e('Generated in:', 'ai-content-studio'); ?></strong> 
                                <span id="time-value">0s</span>
                            </span>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="output-actions">
                            <button type="button" id="save-draft-btn" class="button button-secondary">
                                <span class="dashicons dashicons-download"></span>
                                <?php _e('Save as Draft', 'ai-content-studio'); ?>
                            </button>
                            
                            <button type="button" id="publish-btn" class="button button-primary">
                                <span class="dashicons dashicons-yes"></span>
                                <?php _e('Publish Now', 'ai-content-studio'); ?>
                            </button>
                            
                            <button type="button" id="regenerate-btn" class="button">
                                <span class="dashicons dashicons-update"></span>
                                <?php _e('Regenerate', 'ai-content-studio'); ?>
                            </button>
                            
                            <button type="button" id="copy-btn" class="button">
                                <span class="dashicons dashicons-admin-page"></span>
                                <?php _e('Copy to Clipboard', 'ai-content-studio'); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
    
    <!-- Image Tab -->
    <div id="image-tab" class="tab-content" style="display:none;">
        <?php include AI_CONTENT_STUDIO_PLUGIN_DIR . 'admin/partials/image-generator.php'; ?>
    </div>
    
</div>

<style>
/* Tab styles */
.nav-tab-wrapper {
    margin: 20px 0;
}

.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

/* Existing styles... */
.ai-studio-container {
    display: grid;
    grid-template-columns: 1fr 1.5fr;
    gap: 20px;
    margin-top: 20px;
}
.ai-studio-container {
    display: grid;
    grid-template-columns: 1fr 1.5fr;
    gap: 20px;
    margin-top: 20px;
}

.ai-studio-card {
    background: #fff;
    border: 1px solid #ccd0d4;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
    padding: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    font-weight: 600;
    margin-bottom: 8px;
}

.required {
    color: #dc3232;
}

.ai-studio-state {
    text-align: center;
    padding: 60px 20px;
    color: #666;
}

.ai-studio-spinner {
    margin-bottom: 20px;
}

.ai-studio-error {
    background: #fcf0f1;
    border-left: 4px solid #dc3232;
    padding: 20px;
}

.ai-studio-output {
    animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.output-title-section,
.output-content-section {
    margin-bottom: 20px;
}

.output-title-section label,
.output-content-section label {
    display: block;
    font-weight: 600;
    margin-bottom: 8px;
}

.generated-content-preview {
    background: #f9f9f9;
    border: 1px solid #ddd;
    padding: 20px;
    min-height: 300px;
    max-height: 500px;
    overflow-y: auto;
    line-height: 1.8;
}

.generated-content-preview h2,
.generated-content-preview h3 {
    margin-top: 1.5em;
    margin-bottom: 0.5em;
}

.generated-content-preview p {
    margin-bottom: 1em;
}

.output-meta {
    display: flex;
    gap: 30px;
    padding: 15px;
    background: #f0f6fc;
    border-radius: 4px;
    margin-bottom: 20px;
}

.output-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.output-actions .button {
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.rate-limit-info {
    margin-top: 10px !important;
    color: #666;
}

@media (max-width: 1280px) {
    .ai-studio-container {
        grid-template-columns: 1fr;
    }
}
</style>