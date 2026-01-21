<?php
/**
 * Pollinations.ai Image Generation API (with Craiyon Backup)
 * 
 * @package AI_Content_Studio
 * @subpackage AI_Content_Studio/api
 */

if (!defined('ABSPATH')) {
    exit;
}

class AI_Content_Studio_Pollinations_API extends AI_Content_Studio_API_Base {

    /**
     * Constructor
     */
    public function __construct($api_key = null) {
        parent::__construct($api_key);
        // No API key needed for Pollinations!
    }

    /**
     * Generate image using Pollinations.ai with Craiyon backup
     */
    public function generate_image($prompt, $model = 'pollinations') {
        
        ai_studio_log_activity('=== Image Generation Started ===', 'info');
        ai_studio_log_activity('Service: Pollinations.ai (Free)', 'info');
        ai_studio_log_activity('Prompt: ' . $prompt, 'info');

        // Clean and optimize prompt
        $clean_prompt = $this->optimize_prompt($prompt);
        
        // Try primary service (Pollinations.ai)
        $result = $this->try_pollinations($clean_prompt);
        
        if (!is_wp_error($result)) {
            return $result;
        }
        
        ai_studio_log_activity('Pollinations failed: ' . $result->get_error_message(), 'error');
        ai_studio_log_activity('Trying Craiyon backup service...', 'info');
        
        // Fallback to backup service (Craiyon)
        $result = $this->try_craiyon($clean_prompt);
        
        if (!is_wp_error($result)) {
            return $result;
        }
        
        ai_studio_log_activity('All services failed', 'error');
        
        // All services failed
        return new WP_Error(
            'all_failed',
            __('Image generation services are temporarily busy. Please wait 30 seconds and try again.', 'ai-content-studio')
        );
    }

    /**
     * Optimize prompt for better results
     */
    private function optimize_prompt($prompt) {
        // Remove extra spaces
        $prompt = preg_replace('/\s+/', ' ', trim($prompt));
        
        // Limit length to prevent issues
        if (strlen($prompt) > 500) {
            $prompt = substr($prompt, 0, 497) . '...';
        }
        
        return $prompt;
    }

    /**
     * Primary: Pollinations.ai (Fast and reliable)
     */
    private function try_pollinations($prompt) {
        ai_studio_log_activity('Attempting Pollinations.ai...', 'info');
        
        $encoded_prompt = urlencode($prompt);
        $seed = time(); // Unique seed for each generation
        
        // Build Pollinations.ai URL with optimal parameters
        $image_url = "https://image.pollinations.ai/prompt/{$encoded_prompt}";
        $image_url .= "?width=1024&height=1024&seed={$seed}&nologo=true&enhance=true";
        
        // Retry logic for better reliability
        $max_retries = 2;
        
        for ($i = 0; $i <= $max_retries; $i++) {
            if ($i > 0) {
                ai_studio_log_activity('Pollinations: Retry attempt ' . $i, 'info');
                sleep(3); // Wait 3 seconds before retry
            }
            
            $response = wp_remote_get($image_url, array(
                'timeout' => 60,
                'sslverify' => true,
                'headers' => array(
                    'User-Agent' => 'WordPress/' . get_bloginfo('version') . '; ' . get_bloginfo('url')
                )
            ));
            
            if (is_wp_error($response)) {
                ai_studio_log_activity('Pollinations: WP Error - ' . $response->get_error_message(), 'error');
                continue;
            }
            
            $status_code = wp_remote_retrieve_response_code($response);
            
            if ($status_code !== 200) {
                ai_studio_log_activity('Pollinations: HTTP Status ' . $status_code, 'error');
                continue;
            }
            
            $image_data = wp_remote_retrieve_body($response);
            
            // Validate image data (should be at least 1KB)
            if (empty($image_data) || strlen($image_data) < 1000) {
                ai_studio_log_activity('Pollinations: Invalid image data (size: ' . strlen($image_data) . ' bytes)', 'error');
                continue;
            }
            
            // Success! Convert to base64 data URL
            $base64_image = base64_encode($image_data);
            $data_url = 'data:image/jpeg;base64,' . $base64_image;
            
            ai_studio_log_activity('Pollinations: Success! Image size: ' . strlen($image_data) . ' bytes', 'info');
            
            return array(
                'url' => $data_url,
                'prompt' => $prompt,
                'model' => 'Pollinations.ai',
                'provider' => 'pollinations'
            );
        }
        
        return new WP_Error('pollinations_failed', 'Pollinations.ai service temporarily unavailable after ' . ($max_retries + 1) . ' attempts');
    }

    /**
     * Backup: Craiyon API (Slower but reliable fallback)
     */
    private function try_craiyon($prompt) {
        ai_studio_log_activity('Attempting Craiyon backup service...', 'info');
        
        $api_url = 'https://api.craiyon.com/v3';
        
        $body = array(
            'prompt' => $prompt,
            'version' => '35s5hfwn9n78gb06',
            'model' => 'art'
        );
        
        $response = wp_remote_post($api_url, array(
            'headers' => array(
                'Content-Type' => 'application/json'
            ),
            'body' => json_encode($body),
            'timeout' => 120 // Craiyon takes longer
        ));
        
        if (is_wp_error($response)) {
            ai_studio_log_activity('Craiyon: WP Error - ' . $response->get_error_message(), 'error');
            return $response;
        }
        
        $status_code = wp_remote_retrieve_response_code($response);
        
        if ($status_code !== 200) {
            ai_studio_log_activity('Craiyon: HTTP Status ' . $status_code, 'error');
            return new WP_Error('craiyon_error', 'Craiyon returned status: ' . $status_code);
        }
        
        $data = json_decode(wp_remote_retrieve_body($response), true);
        
        if (!isset($data['images']) || empty($data['images'])) {
            ai_studio_log_activity('Craiyon: No images in response', 'error');
            return new WP_Error('no_image', 'No image data from Craiyon');
        }
        
        // Get first image (already base64 encoded)
        $base64_image = $data['images'][0];
        $data_url = 'data:image/jpeg;base64,' . $base64_image;
        
        ai_studio_log_activity('Craiyon: Success!', 'info');
        
        return array(
            'url' => $data_url,
            'prompt' => $prompt,
            'model' => 'Craiyon AI',
            'provider' => 'craiyon-backup'
        );
    }

    /**
     * Get available models
     */
    public function get_available_models() {
        return array(
            'pollinations' => array(
                'name' => 'Pollinations.ai',
                'description' => '100% FREE with automatic Craiyon backup'
            )
        );
    }

    /**
     * Check API status
     */
    public function check_api_status() {
        return array(
            'status' => true,
            'message' => 'Pollinations.ai is ready (no API key needed)'
        );
    }
}