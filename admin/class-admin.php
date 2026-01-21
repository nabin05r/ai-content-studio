<?php 
/**
 * The admnin-specific functionality of the plugin
 * 
 * @package AI_Content_Studio
 * @subpackage AI_Content_Studio/admin
 */

class AI_Content_Studio_Admin {

    private $plugin_name;
    private $version;

    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register stylesheets for admin area
     */
    public function enqueue_styles() {
        $screen = get_current_screen();
        if ( strpos( $screen->id, 'ai-content-studio' ) !== false ) {
            wp_enqueue_style(
                $this->plugin_name,
                AI_CONTENT_STUDIO_PLUGIN_URL . 'admin/css/ai-content-studio-admin.css', 
                array(), 
                $this->version, 
                'all'
            );
        }

    }
    /**
     * Register JavaScript for admin area
     */
    public function enqueue_scripts() {
        $screen = get_current_screen();
        if ( strpos( $screen->id, 'ai-content-studio' ) !== false ) {
            wp_enqueue_script(
                $this->plugin_name, 
                AI_CONTENT_STUDIO_PLUGIN_URL . 'admin/js/ai-content-studio-admin.js', 
                array(), 
                $this->version, 
                'all'
            );

            // Localize script with data
            wp_localize_script(
                $this->plugin_name, 
                'aiStudio', 
                array(
                    'ajax_url' => admin_url( 'admin-ajax.php' ),
                    'nonce'    => wp_create_nonce( 'ai_content_studio_nonce' ),
                    'providers' => ai_studio_get_providers(),
                    'tones'     => ai_studio_get_tones(),
                    'wordCounts' => ai_studio_get_word_counts(),
                    'i18n' => array(
                        'generating' => __( 'Generating content...', 'ai-content-studio' ),
                        'success'    => __( 'Content generated successfully!', 'ai-content-studio' ),
                        'error'      => __( 'An error occurred. Please try again.', 'ai-content-studio' ),
                        'confirmRegenerate' => __( 'Are you sure you want to generate new content? Unsaved changes will be lost.', 'ai-content-studio' ),
                    )
                ));
        }
    }

    /**
     * Register the admin menu
     */
    public function add_plugin_admin_menu() {

        // Main menu
        add_menu_page(
            __( 'AI Content Studio', 'ai-content-studio' ), 
            __( 'AI Content Studio', 'ai-content-studio' ),
            'edit_posts', 
            'ai-content-studio', 
            array( $this, 'display_dashboard' ), 
            'dashicons-art', 
            30
        );

        // Dashboard Submenu
        add_submenu_page(
            'ai-content-studio', 
            __( 'Dashboard', 'ai-content-studio' ), 
            __( 'Dashboard', 'ai-content-studio' ), 
            'edit_posts', 
            'ai-content-studio', 
            array( $this, 'display_dashboard' )
        );

        // Generate content submenu
        add_submenu_page(
            'ai-content-studio', 
            __( 'Generate Content', 'ai-content-studio' ), 
            __( 'Generate Content', 'ai-content-studio' ), 
            'edit_posts', 
            'ai-content-studio-generate', 
            array( $this, 'display_generator' )
        );

         // History submenu
        add_submenu_page(
            'ai-content-studio', 
            __( 'Generate History', 'ai-content-studio' ), 
            __( 'History', 'ai-content-studio' ), 
            'edit_posts', 
            'ai-content-studio-history', 
            array( $this, 'display_history' )
        );

          // Settings submenu
        add_submenu_page(
            'ai-content-studio', 
            __( 'Settings', 'ai-content-studio' ), 
            __( 'Settings', 'ai-content-studio' ), 
            'manage_options', 
            'ai-content-studio-settings', 
            array( $this, 'display_settings' )
        );
    }

    /**
     * Render dashboard page
     */
    public function display_dashboard() {

        // Get dashboard stats
        $stats = $this->get_dashboard_stats();

        // Get rate limit info
        $rate_limit = ai_studio_check_rate_limit();

         // Check API configuration
        $gemini_configured = ai_studio_has_api_key('gemini');

        // Get recent generations
        $recent_generations = $this->get_recent_generations(5);

        // Include the dashboard template partial
        include_once AI_CONTENT_STUDIO_PLUGIN_DIR . 'admin/partials/dashboard.php';
    }

    /**
     * Render content generator page
     */
    public function display_generator() {

        // Check rate limit
        $rate_limit = ai_studio_check_rate_limit();

        // Get available providers
        $providers = ai_studio_get_providers();

        // Get plugin settings
        $settings = ai_studio_get_settings();

        // Get tones options
        $tones = ai_studio_get_tones();

        // Get word counts
        $word_counts = ai_studio_get_word_counts();

        // Check if the user has exceeds the rate limit
        if( ! $rate_limit['allowed'] ) {
            add_settings_error(
                'ai_studio_message', 
                'rate_limit_exceeded', 
                sprintf( __( 'You have reached your daily limit of %d generations. Please try again tomorrow.', 'ai-content-studio' ),
                $rate_limit['limit']
                ), 
                'error'
            );
        }

        // Check if at least one API key is configuered
        $has_api_key = false;
        foreach( $providers as $key => $provider ){
            if ( ai_studio_has_api_key( $key) ) {
                $has_api_key = true;
                break;
            }
        }

        if( !$has_api_key ) {
            add_settings_error(
                'ai_studio_message', 
                'no_api_keys', 
                sprintf(
                    __('Please configure at least one AI provider API key in <a href="%s">Settings</a> to start generating content.', 'ai-content-studio'),
                    admin_url('admin.php?page=ai-content-studio-settings')
                ),
                'warning'
            );
        }

        // Include the generator template partials
        include_once AI_CONTENT_STUDIO_PLUGIN_DIR . 'admin/partials/generator-page.php';
    }

    /**
     * Render history page
     */
    public function display_history() {

        global $wpdb;
        $table = $wpdb->prefix . 'ai_content_studio_history';
        $user_id = get_current_user_id();

        // Pagination setup
        $per_page = 20;
        $page = isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1 ;
        $offset = ( $page -1 ) * $per_page;

        // Get total count for pagination
        $total = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table WHERE user_id = %d",
            $user_id
        ));

        // Get history records
        $history = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table
            WHERE user_id = %d
            ORDER BY created_at DESC
            LIMIT %d OFFSET %d
            ",
            $user_id, $per_page, $offset
        ));

        // Calculate total pages
        $total_pages = ceil( $total / $per_page );


        include_once AI_CONTENT_STUDIO_PLUGIN_DIR . 'admin/partials/history.php';
    }

    /**
     * Render settings page
     */
    public function display_settings() {
        include_once AI_CONTENT_STUDIO_PLUGIN_DIR . 'admin/partials/settings-page.php';  
    }

    /**
     * Get dashboard statistics
     * 
     * @return array Statistics data
     */
    private function get_dashboard_stats() {
        global $wpdb;
        $table = $wpdb->prefix . 'ai_content_studio_history';
        $user_id = get_current_user_id();

        // Get this month's date range
        $first_day = date('Y-m-01 00:00:00');
        $last_day = date('Y-m-t 23:59:59');
        
        // Content generated this month
        $content_count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table 
            WHERE user_id = %d 
            AND type = 'content'
            AND created_at BETWEEN %s AND %s",
            $user_id, $first_day, $last_day
        )) ?: 0;
        
        // Images generated this month
        $image_count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table 
            WHERE user_id = %d 
            AND type = 'image'
            AND created_at BETWEEN %s AND %s",
            $user_id, $first_day, $last_day
        )) ?: 0;
        
        // Total cost this month
        $total_cost = $wpdb->get_var($wpdb->prepare(
            "SELECT SUM(cost) FROM $table 
            WHERE user_id = %d 
            AND created_at BETWEEN %s AND %s",
            $user_id, $first_day, $last_day
        )) ?: 0;
        
        // Average generation time
        $avg_time = $wpdb->get_var($wpdb->prepare(
            "SELECT AVG(generation_time) FROM $table 
            WHERE user_id = %d 
            AND type = 'content'
            AND created_at BETWEEN %s AND %s",
            $user_id, $first_day, $last_day
        )) ?: 0;
        
        return array(
            'content_count' => intval($content_count),
            'image_count' => intval($image_count),
            'total_cost' => floatval($total_cost),
            'avg_time' => round(floatval($avg_time), 1)
        );
    }

    /**
     * Get recent generations
     * 
     * @param int $limit Number of records to retrieve
     * @return array Recent generation records
     */
    public function get_recent_generations( $limit = 5) {
        global $wpdb;
        $table = $wpdb->prefix . 'ai_content_studio_history';
        $user_id = get_current_user_id();

        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table
            WHERE user_id = %d
            ORDER BY created_at DESC
            LIMIT %d",
            $user_id,
            $limit
        ));
    }
}
