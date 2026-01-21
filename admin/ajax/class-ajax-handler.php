<?php
/**
 * AJAX Handler
 *
 * @package AI_Content_Studio
 */

if (!defined('ABSPATH')) {
    exit;
}

class AI_Content_Studio_AJAX_Handler {

    /**
     * Generate content via AI
     */
    public function generate_content() {
        // Verify nonce
        check_ajax_referer('ai_content_studio_nonce', 'nonce');

        // Check user capability
        if (!current_user_can('edit_posts')) {
            wp_send_json_error(array(
                'message' => __('You do not have permission to generate content.', 'ai-content-studio')
            ));
        }

        // Check rate limit
        $rate_limit = ai_studio_check_rate_limit();
        if (!$rate_limit['allowed']) {
            wp_send_json_error(array(
                'message' => sprintf(
                    __('You have reached your daily limit of %d generations.', 'ai-content-studio'),
                    $rate_limit['limit']
                )
            ));
        }

        // Get and validate input
        $title = sanitize_text_field($_POST['title'] ?? '');
        $description = sanitize_textarea_field($_POST['description'] ?? '');
        $provider = sanitize_text_field($_POST['provider'] ?? 'gemini');
        $tone = sanitize_text_field($_POST['tone'] ?? 'professional');
        $word_count = sanitize_text_field($_POST['word_count'] ?? 'medium');

        // Validate
        if (empty($title)) {
            wp_send_json_error(array(
                'message' => __('Title is required.', 'ai-content-studio')
            ));
        }

        // Check API key
        if (!ai_studio_has_api_key($provider)) {
            wp_send_json_error(array(
                'message' => __('API key not configured for selected provider.', 'ai-content-studio')
            ));
        }

        try {
            // Start timer
            $start_time = microtime(true);

            // Build prompt
            $prompt_data = array(
                'title' => $title,
                'description' => $description,
                'tone' => $tone,
                'word_count' => $word_count
            );
            $prompt = ai_studio_build_prompt($prompt_data);

            // Get API instance
            $api_key = ai_studio_get_setting($provider . '_api_key');
            
            if ($provider === 'gemini') {
                $api = new AI_Content_Studio_Gemini_API($api_key);
            } else {
                wp_send_json_error(array(
                    'message' => __('Provider not supported yet.', 'ai-content-studio')
                ));
            }

            // Generate content
            $result = $api->generate_content($prompt);

            if (is_wp_error($result)) {
                wp_send_json_error(array(
                    'message' => $result->get_error_message()
                ));
            }

            // Calculate generation time
            $generation_time = microtime(true) - $start_time;

            // Parse response
            $parsed = ai_studio_parse_response($result['content']);
            
            // Prepare response data
            $response_data = array(
                'title' => $title,
                'content' => $parsed['content'],
                'word_count' => $parsed['word_count'],
                'generation_time' => round($generation_time, 2),
                'provider' => $provider,
                'tokens_used' => $result['tokens_used'] ?? 0
            );

            // Log to database
            ai_studio_log_generation(array(
                'type' => 'content', // ✅ ADD THIS LINE
                'title' => $title,
                'provider' => $provider,
                'model' => $result['model'] ?? '',
                'tone' => $tone,
                'word_count' => $parsed['word_count'],
                'tokens_used' => $result['tokens_used'] ?? 0,
                'cost' => ai_studio_calculate_cost($provider, $result['tokens_used'] ?? 0),
                'generation_time' => $generation_time,
                'status' => 'completed'
            ));

            // Send success response
            wp_send_json_success($response_data);

        } catch (Exception $e) {
            ai_studio_log_activity('Generation error: ' . $e->getMessage(), 'error');
            
            wp_send_json_error(array(
                'message' => __('An error occurred while generating content. Please try again.', 'ai-content-studio')
            ));
        }
    }

    /**
     * Generate image via AI
     */
    public function generate_image() {
        // Verify nonce
        check_ajax_referer('ai_content_studio_nonce', 'nonce');

        ai_studio_log_activity('=== Image Generation Request ===', 'info');
        ai_studio_log_activity('POST data: ' . print_r($_POST, true), 'info');

        // Check user capability
        if (!current_user_can('upload_files')) {
            ai_studio_log_activity('Permission denied for user', 'error');
            wp_send_json_error(array(
                'message' => __('You do not have permission to generate images.', 'ai-content-studio')
            ));
        }

        // Get and sanitize input
        $prompt = isset($_POST['prompt']) ? sanitize_textarea_field($_POST['prompt']) : '';
        $provider = isset($_POST['provider']) ? sanitize_text_field($_POST['provider']) : 'pollinations';
        
        // Validate prompt
        if (empty($prompt)) {
            ai_studio_log_activity('Empty prompt received', 'error');
            wp_send_json_error(array(
                'message' => __('Image description is required.', 'ai-content-studio')
            ));
        }

        // Validate provider
        if (empty($provider) || $provider !== 'pollinations') {
            ai_studio_log_activity('Invalid provider: ' . $provider, 'error');
            wp_send_json_error(array(
                'message' => __('Invalid provider selected.', 'ai-content-studio')
            ));
        }

        ai_studio_log_activity('Provider: ' . $provider . ', Prompt: ' . $prompt, 'info');

        try {
            $start_time = microtime(true);

            // Free AI - no API key needed!
            ai_studio_log_activity('Using Pollinations.ai (with Craiyon backup)', 'info');

            // Create API instance (doesn't require API key)
            $api = new AI_Content_Studio_Pollinations_API();
            $result = $api->generate_image($prompt);

            if (is_wp_error($result)) {
                ai_studio_log_activity('Generation failed: ' . $result->get_error_message(), 'error');
                wp_send_json_error(array(
                    'message' => $result->get_error_message()
                ));
            }

            $generation_time = microtime(true) - $start_time;
            
            ai_studio_log_activity('Image generated successfully in ' . $generation_time . 's', 'info');

            wp_send_json_success(array(
                'url' => $result['url'],
                'prompt' => $prompt,
                'provider' => $provider,
                'model' => $result['model'] ?? 'Pollinations.ai',
                'size' => '1024x1024',
                'generation_time' => round($generation_time, 2)
            ));

        } catch (Exception $e) {
            ai_studio_log_activity('Image generation exception: ' . $e->getMessage(), 'error');
            
            wp_send_json_error(array(
                'message' => __('Error generating image. Please try again.', 'ai-content-studio')
            ));
        }
    }

    /**
     * Upload generated image to media library
     */
    public function upload_image() {
        // Verify nonce
        check_ajax_referer('ai_content_studio_nonce', 'nonce');

        // Check user capability
        if (!current_user_can('upload_files')) {
            wp_send_json_error(array(
                'message' => __('You do not have permission to upload files.', 'ai-content-studio')
            ));
        }

        $image_url = isset($_POST['image_url']) ? $_POST['image_url'] : '';
        $prompt = sanitize_text_field($_POST['prompt'] ?? '');

        if (empty($image_url)) {
            wp_send_json_error(array(
                'message' => __('Image URL is required.', 'ai-content-studio')
            ));
        }

        ai_studio_log_activity('Uploading image to media library', 'info');

        // Handle data URLs (base64 images)
        if (strpos($image_url, 'data:image') === 0) {
            // It's a base64 data URL
            $result = $this->upload_base64_image($image_url, $prompt);
            
            if (is_wp_error($result)) {
                wp_send_json_error(array(
                    'message' => $result->get_error_message()
                ));
            }
            
            $attachment_id = $result;
            
        } else {
            // It's a regular URL - download it
            $image_url = esc_url_raw($image_url);
            
            $tmp = download_url($image_url);
            
            if (is_wp_error($tmp)) {
                ai_studio_log_activity('Failed to download image: ' . $tmp->get_error_message(), 'error');
                wp_send_json_error(array(
                    'message' => __('Failed to download image.', 'ai-content-studio')
                ));
            }

            // Prepare file array
            $file_array = array(
                'name' => 'ai-generated-' . time() . '.png',
                'tmp_name' => $tmp
            );

            // Upload to media library
            $attachment_id = media_handle_sideload($file_array, 0, $prompt);

            // Remove temp file
            @unlink($tmp);

            if (is_wp_error($attachment_id)) {
                ai_studio_log_activity('Failed to upload: ' . $attachment_id->get_error_message(), 'error');
                wp_send_json_error(array(
                    'message' => __('Failed to upload to media library.', 'ai-content-studio')
                ));
            }
        }

        // Get attachment URL
        $attachment_url = wp_get_attachment_url($attachment_id);
        $edit_url = admin_url('post.php?post=' . $attachment_id . '&action=edit');

        ai_studio_log_activity('Image uploaded successfully. ID: ' . $attachment_id, 'info');

        wp_send_json_success(array(
            'attachment_id' => $attachment_id,
            'url' => $attachment_url,
            'edit_url' => $edit_url,
            'message' => __('Image uploaded successfully!', 'ai-content-studio')
        ));
    }

    /**
     * Upload base64 image to media library
     */
    private function upload_base64_image($data_url, $title = '') {
        // Extract base64 data
        if (preg_match('/^data:image\/(\w+);base64,(.+)$/', $data_url, $matches)) {
            $image_type = $matches[1];
            $image_data = base64_decode($matches[2]);
            
            if ($image_data === false) {
                return new WP_Error('invalid_base64', 'Invalid base64 data');
            }
            
            // Create temp file
            $upload_dir = wp_upload_dir();
            $filename = 'ai-generated-' . time() . '.' . $image_type;
            $file_path = $upload_dir['path'] . '/' . $filename;
            
            // Save to temp file
            file_put_contents($file_path, $image_data);
            
            // Prepare file array
            $file_array = array(
                'name' => $filename,
                'type' => 'image/' . $image_type,
                'tmp_name' => $file_path,
                'size' => filesize($file_path),
                'error' => 0
            );
            
            // Upload to media library
            $attachment_id = media_handle_sideload($file_array, 0, $title);
            
            // Clean up temp file
            @unlink($file_path);
            
            return $attachment_id;
        }
        
        return new WP_Error('invalid_format', 'Invalid image format');
    }

    /**
     * Save generated content as WordPress post
     */
    public function save_post() {
        // Verify nonce
        check_ajax_referer('ai_content_studio_nonce', 'nonce');

        // Check user capability
        if (!current_user_can('edit_posts')) {
            wp_send_json_error(array(
                'message' => __('You do not have permission to save posts.', 'ai-content-studio')
            ));
        }

        // Get input
        $title = sanitize_text_field($_POST['title'] ?? '');
        $content = wp_kses_post($_POST['content'] ?? '');
        $status = sanitize_text_field($_POST['status'] ?? 'draft');

        // Validate
        if (empty($title) || empty($content)) {
            wp_send_json_error(array(
                'message' => __('Title and content are required.', 'ai-content-studio')
            ));
        }

        // Validate status
        if (!in_array($status, array('draft', 'publish'))) {
            $status = 'draft';
        }

        // Create post
        $post_data = array(
            'post_title' => $title,
            'post_content' => $content,
            'post_status' => $status,
            'post_author' => get_current_user_id(),
            'post_type' => 'post'
        );

        $post_id = wp_insert_post($post_data);

        if (is_wp_error($post_id)) {
            wp_send_json_error(array(
                'message' => __('Failed to save post.', 'ai-content-studio')
            ));
        }

        // Get edit URL
        $edit_url = get_edit_post_link($post_id, 'raw');

        wp_send_json_success(array(
            'post_id' => $post_id,
            'edit_url' => $edit_url,
            'message' => __('Post saved successfully!', 'ai-content-studio')
        ));
    }

    /**
     * Get generation history
     */
    public function get_history() {
        // Verify nonce
        check_ajax_referer('ai_content_studio_nonce', 'nonce');

        // Check user capability
        if (!current_user_can('edit_posts')) {
            wp_send_json_error(array(
                'message' => __('You do not have permission to view history.', 'ai-content-studio')
            ));
        }

        global $wpdb;
        $table = $wpdb->prefix . 'ai_content_studio_history';
        $user_id = get_current_user_id();

        $limit = isset($_POST['limit']) ? absint($_POST['limit']) : 20;
        $offset = isset($_POST['offset']) ? absint($_POST['offset']) : 0;

        $history = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table
            WHERE user_id = %d
            ORDER BY created_at DESC
            LIMIT %d OFFSET %d",
            $user_id,
            $limit,
            $offset
        ));

        $total = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table WHERE user_id = %d",
            $user_id
        ));

        wp_send_json_success(array(
            'history' => $history,
            'total' => (int) $total,
            'limit' => $limit,
            'offset' => $offset
        ));
    }
}