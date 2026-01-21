<?php 
/**
 * Helper functions for AI Content Studio plugin
 * 
 * @package AI_Content_Studio
 * @subpackage AI_Content_Studio/includes
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get plugin settings
 */
function ai_studio_get_settings() {
    return get_option('ai_content_studio_settings', array());
}

/**
 * Get specific setting value
 */
function ai_studio_get_setting($key, $default = '') {
    $settings = ai_studio_get_settings();
    return isset($settings[$key]) ? $settings[$key] : $default;
}

/**
 * Update plugin settings
 */
function ai_studio_update_settings($new_settings) {
    $current_settings = ai_studio_get_settings();
    $updated_settings = array_merge($current_settings, $new_settings);
    return update_option('ai_content_studio_settings', $updated_settings);
}

/**
 * Check if API key is configured
 */
function ai_studio_has_api_key($provider = null) {
    if ($provider === null) {
        $provider = ai_studio_get_setting('default_model', 'gemini');
    }
    // Special handling for huggingface
    if ($provider === 'huggingface') {
        $key = ai_studio_get_setting('huggingface_api_key');
    } else {
        $key = ai_studio_get_setting($provider . '_api_key');
    }
    return !empty($key);
}

/**
 * Sanitize API key
 */
function ai_studio_sanitize_api_key($key) {
    return sanitize_text_field(trim($key));
}

/**
 * Format generation time
 */
function ai_studio_format_time($seconds) {
    if ($seconds < 1) {
        return round($seconds * 1000) . ' ms';
    }
    return round($seconds, 2) . ' s';
}

/**
 * Calculate estimated cost based on tokens and provider rates
 */
function ai_studio_calculate_cost($provider, $tokens) {
    $rates = array(
        'gemini' => 0,              // Free tier
        'pollinations' => 0,        // Free tier
    );

    $rate = isset($rates[$provider]) ? $rates[$provider] : 0;
    return $tokens * $rate;
}

/**
 * Get available AI providers
 */
function ai_studio_get_providers() {
    return array(
        'gemini' => array(
            'name'   => 'Google Gemini',
            'icon'   => 'dashicons-google',
            'free'   => true,
            'models' => array('gemini-2.5-flash')
        ),
        'pollinations' => array(
            'name'   => 'Pollinations.ai',
            'icon'   => 'dashicons-format-image',
            'free'   => true,
            'models' => array('pollinations')
        )
    );
}

/**
 * Get tone options
 */
function ai_studio_get_tones() {
    return array(
        'professional' => __('Professional', 'ai-content-studio'),
        'casual'       => __('Casual', 'ai-content-studio'),
        'friendly'     => __('Friendly', 'ai-content-studio'),
        'technical'    => __('Technical', 'ai-content-studio'),
        'creative'     => __('Creative', 'ai-content-studio'),
        'formal'       => __('Formal', 'ai-content-studio'),
    );
}

/**
 * Get word count options
 */
function ai_studio_get_word_counts() {
    return array(
        'short'      => __('Short (300-500 words)', 'ai-content-studio'),
        'medium'     => __('Medium (500-800 words)', 'ai-content-studio'),
        'long'       => __('Long (800-1200 words)', 'ai-content-studio'),
        'very_long'  => __('Very Long (1200-2000 words)', 'ai-content-studio'),
    );
}

/**
 * Get word count range from option key
 */
function ai_studio_get_word_count_range($length) {
    $ranges = array(
        'short'     => array('min' => 300, 'max' => 500),
        'medium'    => array('min' => 500, 'max' => 800),
        'long'      => array('min' => 800, 'max' => 1200),
        'very_long' => array('min' => 1200, 'max' => 2000)
    );
    
    return isset($ranges[$length]) ? $ranges[$length] : $ranges['medium'];
}

/**
 * Count words in text
 */
function ai_studio_count_words($text) {
    $text = strip_tags($text);
    $text = trim(preg_replace('/\s+/', ' ', $text));
    return str_word_count($text);
}

/**
 * Build AI prompt from input data
 */
function ai_studio_build_prompt($data) {
    $title = $data['title'];
    $description = !empty($data['description']) ? $data['description'] : '';
    $tone = $data['tone'];
    $word_count_key = $data['word_count'];
    
    // Get word count range
    $word_count = ai_studio_get_word_count_range($word_count_key);
    
    $prompt = "Write a comprehensive blog post with the following requirements:\n\n";
    $prompt .= "Title: {$title}\n\n";
    
    if (!empty($description)) {
        $prompt .= "Additional Context: {$description}\n\n";
    }
    
    $prompt .= "Requirements:\n";
    $prompt .= "- Tone: {$tone}\n";
    $prompt .= "- Length: {$word_count['min']}-{$word_count['max']} words\n";
    $prompt .= "- Format: Use HTML tags (h2, h3, p, ul, ol, li, strong, em)\n";
    $prompt .= "- Structure: Include introduction, main sections with subheadings, and conclusion\n";
    $prompt .= "- Quality: Well-researched, engaging, and informative\n\n";
    $prompt .= "Return ONLY the HTML content, no markdown, no explanations.";
    
    return $prompt;
}

/**
 * Parse AI response and extract content
 */
function ai_studio_parse_response($response) {
    // Remove markdown code blocks if present
    $response = preg_replace('/```html\s*/i', '', $response);
    $response = preg_replace('/```json\s*/i', '', $response);
    $response = preg_replace('/```\s*$/i', '', $response);
    $response = trim($response);
    
    return array(
        'content' => $response,
        'word_count' => ai_studio_count_words($response)
    );
}

/**
 * Log generation activity
 */
function ai_studio_log_activity($message, $type = 'info') {
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log("[AI Content Studio] [$type] $message");
    }
}

/**
 * Check rate limit
 */
/**
 * Check rate limit
 */
function ai_studio_check_rate_limit($user_id = null) {
    if ($user_id === null) {
        $user_id = get_current_user_id();
    }

    // Get rate limit from settings with proper default
    $max_per_day = (int) ai_studio_get_setting('rate_limit', 60); // ✅ Cast to int and default to 60
    
    // If still 0, force it to 60
    if ($max_per_day <= 0) {
        $max_per_day = 60;
    }

    global $wpdb;
    $table = $wpdb->prefix . 'ai_content_studio_history';
    
    // Check if table exists first
    $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table'") === $table;
    
    if (!$table_exists) {
        // Table doesn't exist yet, allow generation
        return array(
            'allowed'   => true,
            'used'      => 0,
            'limit'     => $max_per_day,
            'remaining' => $max_per_day
        );
    }

    $count = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table 
        WHERE user_id = %d 
        AND DATE(created_at) = CURDATE()",
        $user_id
    ));
    
    // Ensure count is a number
    $count = (int) $count;
    
    return array(
        'allowed'   => $count < $max_per_day,
        'used'      => $count,
        'limit'     => $max_per_day,
        'remaining' => max(0, $max_per_day - $count)
    );
}

/**
 * Log generation to database
 */
function ai_studio_log_generation($data) {
    global $wpdb;
    $table = $wpdb->prefix . 'ai_content_studio_history';
    
    $insert_data = array(
        'user_id' => get_current_user_id(),
        'type' => sanitize_text_field($data['type'] ?? 'content'), // ✅ ADD THIS
        'title' => sanitize_text_field($data['title']),
        'provider' => sanitize_text_field($data['provider']),
        'model' => sanitize_text_field($data['model'] ?? ''),
        'tone' => sanitize_text_field($data['tone'] ?? ''),
        'word_count' => absint($data['word_count'] ?? 0),
        'tokens_used' => absint($data['tokens_used'] ?? 0),
        'cost' => floatval($data['cost'] ?? 0),
        'generation_time' => floatval($data['generation_time'] ?? 0),
        'status' => sanitize_text_field($data['status'] ?? 'completed'),
        'created_at' => current_time('mysql')
    );
    
    $result = $wpdb->insert($table, $insert_data);
    
    if (!$result) {
        // Log error for debugging
        ai_studio_log_activity('Failed to log generation: ' . $wpdb->last_error, 'error');
    }
    
    return $result ? $wpdb->insert_id : false;
}

/**
 * Validate content requirements
 */
function ai_studio_validate_content_request($data) {
    $errors = array();

    if (empty($data['title'])) {
        $errors[] = __('Title is required.', 'ai-content-studio');
    }
    
    if (!ai_studio_has_api_key(isset($data['provider']) ? $data['provider'] : null)) {
        $errors[] = __('API key is not configured for the selected provider.', 'ai-content-studio');
    }

    return empty($errors) ? true : $errors;
}
