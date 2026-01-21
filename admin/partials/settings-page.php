<?php
/**
 * Settings Page Template
 *
 * @package AI_Content_Studio
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <?php settings_errors('ai_content_studio_settings'); ?>
    
    <form method="post" action="options.php">
        <?php
        settings_fields('ai_content_studio_settings');
        do_settings_sections('ai-content-studio-settings');
        submit_button(__('Save Settings', 'ai-content-studio'));
        ?>
    </form>
    
    <!-- Getting Started Guide -->
    <div class="ai-studio-info-box" style="margin-top: 20px; padding: 20px; background: #f0f6fc; border-left: 4px solid #0073aa; border-radius: 4px;">
        <h3><?php _e('🚀 Getting Started', 'ai-content-studio'); ?></h3>
        <ol style="line-height: 1.8;">
            <li>
                <strong><?php _e('Get a FREE Gemini API key', 'ai-content-studio'); ?></strong><br>
                <?php _e('Visit', 'ai-content-studio'); ?> <a href="https://aistudio.google.com/" target="_blank">Google AI Studio</a> <?php _e('and create your free API key', 'ai-content-studio'); ?>
            </li>
            <li>
                <strong><?php _e('Paste your API key above', 'ai-content-studio'); ?></strong><br>
                <?php _e('Add your Gemini API key in the field above and click "Save Settings"', 'ai-content-studio'); ?>
            </li>
            <li>
                <strong><?php _e('Start creating!', 'ai-content-studio'); ?></strong><br>
                <?php _e('Go to "Generate Content" to create AI-powered blog posts and images', 'ai-content-studio'); ?>
            </li>
        </ol>
    </div>

    <!-- Features Info -->
    <div class="ai-studio-features" style="margin-top: 20px; padding: 20px; background: #fff; border: 1px solid #ddd; border-radius: 4px;">
        <h3><?php _e('✨ What You Get (100% FREE)', 'ai-content-studio'); ?></h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 15px;">
            <div>
                <h4 style="margin: 0 0 10px 0;">📝 <?php _e('Text Generation', 'ai-content-studio'); ?></h4>
                <ul style="margin: 0; padding-left: 20px;">
                    <li><?php _e('Google Gemini 2.5 Flash', 'ai-content-studio'); ?></li>
                    <li><?php _e('60 requests/minute (free tier)', 'ai-content-studio'); ?></li>
                    <li><?php _e('Multiple tones & lengths', 'ai-content-studio'); ?></li>
                    <li><?php _e('SEO-optimized content', 'ai-content-studio'); ?></li>
                </ul>
            </div>
            <div>
                <h4 style="margin: 0 0 10px 0;">🎨 <?php _e('Image Generation', 'ai-content-studio'); ?></h4>
                <ul style="margin: 0; padding-left: 20px;">
                    <li><?php _e('Pollinations.ai (FREE)', 'ai-content-studio'); ?></li>
                    <li><?php _e('Craiyon backup (FREE)', 'ai-content-studio'); ?></li>
                    <li><?php _e('No API key needed', 'ai-content-studio'); ?></li>
                    <li><?php _e('Unlimited generations', 'ai-content-studio'); ?></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Free Services Info -->
    <div class="ai-studio-info" style="margin-top: 20px; padding: 20px; background: #f0f6fc; border-left: 4px solid #2271b1; border-radius: 4px;">
        <h3><?php _e('✓ 100% Free AI Services', 'ai-content-studio'); ?></h3>
        <p><?php _e('This plugin uses completely free AI services:', 'ai-content-studio'); ?></p>
        <ul style="padding-left: 20px;">
            <li><strong>Gemini 2.5 Flash:</strong> <?php _e('Free text content generation (Google)', 'ai-content-studio'); ?></li>
            <li><strong>Pollinations.ai:</strong> <?php _e('Free image generation with Craiyon backup', 'ai-content-studio'); ?></li>
        </ul>
        <p>
            <a href="https://aistudio.google.com/" target="_blank" class="button button-secondary">
                <?php _e('Get Free Gemini API Key', 'ai-content-studio'); ?>
            </a>
        </p>
    </div>
</div>