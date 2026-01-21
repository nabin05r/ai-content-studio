<?php 
/**
 * Google Gemini API Integration
 * 
 * @package AI_Content_Studio
 * @subpackage AI_Content_Studio/api
 */

if (!defined('ABSPATH')) {
    exit;
}

class AI_Content_Studio_Gemini_API extends AI_Content_Studio_API_Base {

    private $model = 'gemini-2.5-flash';
    private $api_url = 'https://generativelanguage.googleapis.com/v1beta/models/';

    /**
     * Constructor - accepts API key
     */
    public function __construct($api_key = null) {
        parent::__construct($api_key); // ✅ Call parent constructor FIRST
        
        // If no API key provided, get from settings
        if (empty($this->api_key)) {
            $this->api_key = ai_studio_get_setting('gemini_api_key');
        }
    }

    /**
     * Generate content using Gemini API
     */
    public function generate_content($params) {

        // Records current time to calculate how long generation takes
        $start_time = microtime(true);

        // Validate API Key
        if (empty($this->api_key)) {
            return $this->error_response(__('Gemini API key not configured.', 'ai-content-studio'));
        }

        // Build the prompt (accept string or array)
        if (is_string($params)) {
            $prompt = $params; // Direct prompt string
        } else {
            $prompt = $this->build_prompt($params); // Build from array
        }

        // Make API request
        $response = $this->make_request($prompt, is_array($params) ? $params : array());

        if (is_wp_error($response)) {
            return $response;
        }

        // Calculates the Generation time
        $generation_time = microtime(true) - $start_time;

        // Return success response
        return array(
            'success' => true,
            'content' => $response['content'],
            'meta_description' => $response['meta_description'] ?? '',
            'tokens_used' => $response['tokens_used'] ?? 0,
            'model' => $this->model,
            'generation_time' => $generation_time,
            'cost' => 0 // Gemini is free
        );
    }

    /**
     * Build prompt for content generation
     */
    private function build_prompt($params) {

        $title = $params['title'] ?? '';
        $description = $params['description'] ?? '';
        $word_count = $params['word_count'] ?? 'medium';
        $tone = $params['tone'] ?? 'professional';
        $include_meta = $params['include_meta'] ?? true;

        // Get word count range
        if (function_exists('ai_studio_get_word_count_range')) {
            $range = ai_studio_get_word_count_range($word_count);
            $word_count_text = "{$range['min']}-{$range['max']} words";
        } else {
            $word_count_text = "500-800 words";
        }

        $prompt = "You are an expert content writer. Generate a high-quality blog post with the following specifications:\n\n";
        $prompt .= "Title: {$title}\n";
        
        if (!empty($description)) {
            $prompt .= "Description/Context: {$description}\n";
        }
        
        $prompt .= "Word Count: {$word_count_text}\n";
        $prompt .= "Tone: {$tone}\n\n";

        $prompt .= "Requirements:\n";
        $prompt .= "1. Write engaging, well-structured content with clear headings\n";
        $prompt .= "2. Use HTML formatting (h2, h3, p, ul, ol, strong, em)\n";
        $prompt .= "3. Include relevant examples and explanations\n";
        $prompt .= "4. Make it SEO-friendly with natural keyword usage\n";
        $prompt .= "5. Write in a {$tone} tone\n";
        $prompt .= "6. Include an introduction and conclusion\n\n";

        if ($include_meta) {
            $prompt .= "Also provide:\n";
            $prompt .= "- A compelling meta description (150-160 characters)\n\n";
        }

        $prompt .= "Format your response as JSON:\n";
        $prompt .= "{\n";
        $prompt .= '  "content": "HTML formatted content here"' . "\n";
        if ($include_meta) {
            $prompt .= '  "meta_description": "meta description here"' . "\n";
        }
        $prompt .= "}";

        return $prompt;
    }

    /**
     * Make API request to Gemini
     */
    private function make_request($prompt, $params) {

        $url = $this->api_url . $this->model . ':generateContent?key=' . $this->api_key;

        $body = array(
            'contents' => array(
                array(
                    'parts' => array(
                        array('text' => $prompt)
                    )
                )
            ),
            'generationConfig' => array(
                'temperature' => 0.7,
                'topK' => 40,
                'topP' => 0.95,
                'maxOutputTokens' => 8192
            )
        );

        $response = wp_remote_post($url, array(
            'headers' => array(
                'Content-Type' => 'application/json'
            ),
            'body' => json_encode($body),
            'timeout' => 90
        ));

        if (is_wp_error($response)) {
            ai_studio_log_activity('Gemini API Error: ' . $response->get_error_message(), 'error');
            return $this->error_response(__('Failed to connect to Gemini API', 'ai-content-studio'));
        }

        $status_code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if ($status_code !== 200) {
            $error_message = $data['error']['message'] ?? __('Unknown API error', 'ai-content-studio');
            ai_studio_log_activity('Gemini API Error (Status ' . $status_code . '): ' . $error_message, 'error');
            return $this->error_response($error_message);
        }

        // Extract content from the response
        $generated_text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';

        if (empty($generated_text)) {
            return $this->error_response(__('No content generated', 'ai-content-studio'));
        }

        // Try to parse JSON response
        $parsed_content = $this->parse_json_response($generated_text);

        // Calculate tokens (approximate)
        $tokens_used = isset($data['usageMetadata']['totalTokenCount']) 
            ? $data['usageMetadata']['totalTokenCount'] 
            : $this->estimate_tokens($prompt . $generated_text);
        
        return array(
            'content' => $parsed_content['content'] ?? $generated_text,
            'meta_description' => $parsed_content['meta_description'] ?? '',
            'tokens_used' => $tokens_used
        );
    }

    /**
     * Parse JSON response from AI
     */
    private function parse_json_response($text) {
        // Try to extract JSON from markdown code blocks
        if (preg_match('/```json\s*(.*?)\s*```/s', $text, $matches)) {
            $json_str = $matches[1];
        } elseif (preg_match('/```\s*(.*?)\s*```/s', $text, $matches)) {
            $json_str = $matches[1];
        } else {
            $json_str = $text;
        }

        // Clean up the JSON string
        $json_str = trim($json_str);

        $parsed = json_decode($json_str, true);

        if (json_last_error() === JSON_ERROR_NONE && isset($parsed['content'])) {
            return $parsed;
        }

        // If JSON parsing fails, return raw text as content
        return array('content' => $text);
    }

    /**
     * Estimate token count (rough approximation)
     */
    private function estimate_tokens($text) {
        return intval(strlen($text) / 4);
    }

    /**
     * Generate image description for image generation APIs
     */
    public function generate_image_prompt($content_title, $content_summary = '') {
        if (empty($this->api_key)) {
            return $this->error_response(__('Gemini API key not configured', 'ai-content-studio'));
        }

        $prompt = "Based on this article title: '{$content_title}'";
        if (!empty($content_summary)) {
            $prompt .= " and summary: '{$content_summary}'";
        }
        $prompt .= "\n\nGenerate a detailed, creative image description for a featured blog post image. ";
        $prompt .= "The description should be suitable for an AI image generator. ";
        $prompt .= "Keep it under 400 characters. Be specific about style, colors, and composition.";

        $url = $this->api_url . $this->model . ':generateContent?key=' . $this->api_key;

        $body = array(
            'contents' => array(
                array(
                    'parts' => array(
                        array('text' => $prompt)
                    )
                )
            ),
            'generationConfig' => array(
                'temperature' => 0.8,
                'maxOutputTokens' => 200,
            )
        );

        $response = wp_remote_post($url, array(
            'headers' => array('Content-Type' => 'application/json'),
            'body' => json_encode($body),
            'timeout' => 30,
        ));

        if (is_wp_error($response)) {
            return $this->error_response(__('Failed to generate image description', 'ai-content-studio'));
        }

        $data = json_decode(wp_remote_retrieve_body($response), true);
        $image_prompt = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';

        return array(
            'success' => true,
            'image_prompt' => trim($image_prompt),
        );
    }

    /**
     * Generate image with Gemini
     * 
     * @param string $prompt Image description
     * @param array $options Additional options (not used by Gemini)
     * @return array|WP_Error
     */
    public function generate_image($prompt, $options = array()) {
        if (empty($this->api_key)) {
            return new WP_Error('no_api_key', __('Gemini API key not configured', 'ai-content-studio'));
        }

        // Gemini free tier doesn't support image generation
        return new WP_Error(
            'not_supported',
            __('Gemini free tier does not support image generation. Please use Pollinations.ai (FREE).', 'ai-content-studio')
        );
    }
}