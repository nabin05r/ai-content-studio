<?php
/**
 * Settings Handler
 *
 * @package AI_Content_Studio
 */

if (!defined('ABSPATH')) {
    exit;
}

class AI_Content_Studio_Settings {
    
    /**
     * Settings option name (used for both group and database key)
     */
    const OPTION_NAME = 'ai_content_studio_settings';
    
    /**
     * Initialize settings
     */
    public function __construct() {

    }
    
    /**
     * Register plugin settings
     */
    public function register_settings() {
        register_setting(
            'ai_content_studio_settings',
            self::OPTION_NAME,
            array(
                'sanitize_callback' => array($this, 'sanitize_settings'),
                'default' => $this->get_default_settings()
            )
        );
        
        // API Settings Section
        add_settings_section(
            'ai_content_studio_api',
            __('API Configuration', 'ai-content-studio'),
            array($this, 'api_section_callback'),
            'ai-content-studio-settings'
        );
        
        // Gemini API Key (Required)
        add_settings_field(
            'gemini_api_key',
            __('Gemini API Key (Required)', 'ai-content-studio'),
            array($this, 'gemini_api_key_callback'),
            'ai-content-studio-settings',
            'ai_content_studio_api'
        );

        // Default Settings Section
        add_settings_section(
            'ai_content_studio_defaults',
            __('Default Generation Settings', 'ai-content-studio'),
            array($this, 'defaults_section_callback'),
            'ai-content-studio-settings'
        );
        
        // Default Model
        add_settings_field(
            'default_model',
            __('Default AI Model', 'ai-content-studio'),
            array($this, 'default_model_callback'),
            'ai-content-studio-settings',
            'ai_content_studio_defaults'
        );
        
        // Default Tone
        add_settings_field(
            'default_tone',
            __('Default Tone', 'ai-content-studio'),
            array($this, 'default_tone_callback'),
            'ai-content-studio-settings',
            'ai_content_studio_defaults'
        );
        
        // Default Length
        add_settings_field(
            'default_length',
            __('Default Length', 'ai-content-studio'),
            array($this, 'default_length_callback'),
            'ai-content-studio-settings',
            'ai_content_studio_defaults'
        );
        
        // Auto Save
        add_settings_field(
            'auto_save',
            __('Auto Save Generated Content', 'ai-content-studio'),
            array($this, 'auto_save_callback'),
            'ai-content-studio-settings',
            'ai_content_studio_defaults'
        );
    }
    
    /**
     * Get default settings
     */
    private function get_default_settings() {
        return array(
            'gemini_api_key' => '',
            'default_model' => 'gemini',
            'default_tone' => 'professional',
            'default_length' => 'medium',
            'auto_save' => 'draft',
            'enable_history' => true,
            'rate_limit' => 60
        );
    }
    
    /**
     * Sanitize settings
     */
    public function sanitize_settings($input) {
        $sanitized = array();
        
        // Sanitize API keys
        $sanitized['gemini_api_key'] = !empty($input['gemini_api_key'])
            ? sanitize_text_field($input['gemini_api_key'])
            : '';

        // Sanitize other settings
        $sanitized['default_model'] = sanitize_text_field($input['default_model'] ?? 'gemini');
        $sanitized['default_tone'] = sanitize_text_field($input['default_tone'] ?? 'professional');
        $sanitized['default_length'] = sanitize_text_field($input['default_length'] ?? 'medium');
        $sanitized['auto_save'] = sanitize_text_field($input['auto_save'] ?? 'draft');
        $sanitized['enable_history'] = !empty($input['enable_history']);
        $sanitized['rate_limit'] = absint($input['rate_limit'] ?? 60);
        
        return $sanitized;
    }
    
    /**
     * API Section Callback
     */
    public function api_section_callback() {
        echo '<p>' . __('Configure your AI API keys for text and image generation.', 'ai-content-studio') . '</p>';
    }
    
    /**
     * Defaults Section Callback
     */
    public function defaults_section_callback() {
        echo '<p>' . __('Set default options for content generation.', 'ai-content-studio') . '</p>';
    }
    
    /**
     * Gemini API Key Field
     */
    public function gemini_api_key_callback() {
        $settings = get_option(self::OPTION_NAME, $this->get_default_settings());
        $value = isset($settings['gemini_api_key']) ? $settings['gemini_api_key'] : '';
        $masked = !empty($value) ? str_repeat('*', strlen($value) - 4) . substr($value, -4) : '';
        
        echo '<input type="password" name="' . self::OPTION_NAME . '[gemini_api_key]" ';
        echo 'value="' . esc_attr($value) . '" class="regular-text" ';
        echo 'placeholder="AIza..." required />';
        
        if (!empty($value)) {
            echo '<p class="description" style="color: #00a32a;">';
            echo '<strong>✓ Connected:</strong> ' . sprintf(__('Key ending in ...%s (Free tier: 60 requests/min)', 'ai-content-studio'), substr($value, -4));
            echo '</p>';
        } else {
            echo '<p class="description">';
            printf(
                __('Get your FREE API key from <a href="%s" target="_blank">Google AI Studio</a> (takes 1 minute)', 'ai-content-studio'),
                'https://aistudio.google.com/'
            );
            echo '</p>';
        }
    }

    /**
     * Default Model Field
     */
    public function default_model_callback() {
        $settings = get_option(self::OPTION_NAME, $this->get_default_settings());
        $value = isset($settings['default_model']) ? $settings['default_model'] : 'gemini';
        
        $models = array(
            'gemini' => 'Gemini 2.5 Flash (Free - Recommended)'
        );
        
        echo '<select name="' . self::OPTION_NAME . '[default_model]">';
        foreach ($models as $key => $label) {
            printf(
                '<option value="%s" %s>%s</option>',
                esc_attr($key),
                selected($value, $key, false),
                esc_html($label)
            );
        }
        echo '</select>';
        echo '<p class="description">' . __('Currently only Gemini is supported for text generation', 'ai-content-studio') . '</p>';
    }
    
    /**
     * Default Tone Field
     */
    public function default_tone_callback() {
        $settings = get_option(self::OPTION_NAME, $this->get_default_settings());
        $value = isset($settings['default_tone']) ? $settings['default_tone'] : 'professional';
        
        $tones = array(
            'professional' => 'Professional',
            'casual' => 'Casual',
            'friendly' => 'Friendly',
            'formal' => 'Formal',
            'creative' => 'Creative'
        );
        
        echo '<select name="' . self::OPTION_NAME . '[default_tone]">';
        foreach ($tones as $key => $label) {
            printf(
                '<option value="%s" %s>%s</option>',
                esc_attr($key),
                selected($value, $key, false),
                esc_html($label)
            );
        }
        echo '</select>';
    }
    
    /**
     * Default Length Field
     */
    public function default_length_callback() {
        $settings = get_option(self::OPTION_NAME, $this->get_default_settings());
        $value = isset($settings['default_length']) ? $settings['default_length'] : 'medium';
        
        $lengths = array(
            'short' => 'Short (300-500 words)',
            'medium' => 'Medium (500-800 words)',
            'long' => 'Long (800-1200 words)'
        );
        
        echo '<select name="' . self::OPTION_NAME . '[default_length]">';
        foreach ($lengths as $key => $label) {
            printf(
                '<option value="%s" %s>%s</option>',
                esc_attr($key),
                selected($value, $key, false),
                esc_html($label)
            );
        }
        echo '</select>';
    }
    
    /**
     * Auto Save Field
     */
    public function auto_save_callback() {
        $settings = get_option(self::OPTION_NAME, $this->get_default_settings());
        $value = isset($settings['auto_save']) ? $settings['auto_save'] : 'draft';
        
        $options = array(
            'draft' => 'Save as Draft',
            'publish' => 'Publish Immediately',
            'none' => 'Don\'t Auto Save'
        );
        
        echo '<select name="' . self::OPTION_NAME . '[auto_save]">';
        foreach ($options as $key => $label) {
            printf(
                '<option value="%s" %s>%s</option>',
                esc_attr($key),
                selected($value, $key, false),
                esc_html($label)
            );
        }
        echo '</select>';
    }
    
    /**
     * Get setting value
     */
    public static function get($key, $default = null) {
        $settings = get_option(self::OPTION_NAME, array());
        return isset($settings[$key]) ? $settings[$key] : $default;
    }
}