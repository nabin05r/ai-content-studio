<?php
/**
 * Base API Class
 *
 * @package AI_Content_Studio
 */

if (!defined('ABSPATH')) {
    exit;
}

class AI_Content_Studio_API_Base {

    /**
     * API Key
     */
    protected $api_key;

    /**
     * Constructor
     */
    public function __construct($api_key = null) {
        $this->api_key = $api_key;
    }

    /**
     * Return error response in consistent format
     * 
     * @param string $message Error message
     * @return WP_Error
     */
    protected function error_response($message) {
        return new WP_Error('api_error', $message);
    }

    /**
     * Return success response in consistent format
     * 
     * @param array $data Response data
     * @return array
     */
    protected function success_response($data) {
        return array_merge(array('success' => true), $data);
    }

    /**
     * Validate required parameters
     * 
     * @param array $params Parameters to validate
     * @param array $required Required parameter names
     * @return true|WP_Error
     */
    protected function validate_params($params, $required) {
        foreach ($required as $param) {
            if (empty($params[$param])) {
                return new WP_Error(
                    'missing_param',
                    sprintf(__('Missing required parameter: %s', 'ai-content-studio'), $param)
                );
            }
        }
        return true;
    }

    /**
     * Sanitize API key
     * 
     * @param string $key API key
     * @return string
     */
    protected function sanitize_api_key($key) {
        return sanitize_text_field(trim($key));
    }

    /**
     * Generate content (optional - override in child classes)
     * 
     * @param mixed $params Parameters for content generation
     * @return array|WP_Error
     */
    public function generate_content($params) {
        return new WP_Error('not_implemented', __('Content generation not supported by this provider', 'ai-content-studio'));
    }

    /**
     * Generate image (optional - override in child classes)
     * 
     * @param string $prompt Image description
     * @param array $options Additional options
     * @return array|WP_Error
     */
    public function generate_image($prompt, $options = array()) {
        return new WP_Error('not_implemented', __('Image generation not supported by this provider', 'ai-content-studio'));
    }
}