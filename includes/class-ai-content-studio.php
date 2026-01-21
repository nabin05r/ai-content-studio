<?php 
/**
 * The core plugin class
 * 
 * @package AI_Content_Studio
 * @subpackage AI_Content_Studio/includes
 */

class AI_Content_Studio {

    /**
     * The loader that's responsible for maintaining and registering all hooks
     */
    protected $loader;

    /**
     * Unique identifier for the plugin
     */
    protected $plugin_name;

    /**
     * Current version of the plugin
     */
    protected $version;

    /**
     * Initialize the plugin
     */
    public function __construct() {
        $this->version = AI_CONTENT_STUDIO_VERSION;
        $this->plugin_name = "ai-content-studio";

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Load required dependencies
     */
    private function load_dependencies() {

        /**
         * Load the class responsible for arranging the actions and filters
         */
        require_once AI_CONTENT_STUDIO_PLUGIN_DIR . 'includes/class-loader.php';

        /**
         * Load the class responsible for internationalization
         */
        require_once AI_CONTENT_STUDIO_PLUGIN_DIR . 'includes/class-i18n.php';

        /**
         * Helper functions
         */
        require_once AI_CONTENT_STUDIO_PLUGIN_DIR . 'includes/helpers.php';

        /**
         * Load admin class
         */
        require_once AI_CONTENT_STUDIO_PLUGIN_DIR . 'admin/class-admin.php';

        /**
         * Load admin settings page class
         */
        require_once AI_CONTENT_STUDIO_PLUGIN_DIR . 'admin/class-settings.php';

        /**
         * Load content generation class
         */
        // require_once AI_CONTENT_STUDIO_PLUGIN_DIR . 'admin/class-generator.php';

        /**
         * AJAX handler class
         */
        require_once AI_CONTENT_STUDIO_PLUGIN_DIR . 'admin/ajax/class-ajax-handler.php';

        /**
         * Load API Base class
         */
        require_once AI_CONTENT_STUDIO_PLUGIN_DIR . 'api/class-api-base.php';

        /**
         * GEMINI API class (Default freee option)
         */
        require_once AI_CONTENT_STUDIO_PLUGIN_DIR . 'api/class-gemini-api.php';

        /**
         * Free Image Generation API class (Pollinations + Craiyon)
         */
        require_once AI_CONTENT_STUDIO_PLUGIN_DIR . 'api/class-pollinations-api.php';

        /**
         * Database handler class
         */
        // require_once AI_CONTENT_STUDIO_PLUGIN_DIR . 'database/class-db.php';

        $this->loader = new AI_Content_Studio_Loader();
    }

    /**
     * Define the locale for internationalization
     */
    private function set_locale() {
        $plugin_i18n = new AI_Content_Studio_i18n();
        $this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
    }

    /**
     * Register all admin hooks
     */
    private function define_admin_hooks() {
        $plugin_admin = new AI_Content_Studio_Admin( $this->get_plugin_name(), $this->get_version() );

        // Enqueue admin styles and scripts
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

        // Add admin menu
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_admin_menu' );

        // Settings page
        $plugin_settings = new AI_Content_Studio_Settings();
        $this->loader->add_action( 'admin_init', $plugin_settings, 'register_settings' );

        // AJAX handlers
        $ajax_handler = new AI_Content_Studio_AJAX_Handler();
        $this->loader->add_action( 'wp_ajax_ai_studio_generate_content', $ajax_handler, 'generate_content' );
        $this->loader->add_action( 'wp_ajax_ai_studio_generate_image', $ajax_handler, 'generate_image' );
        $this->loader->add_action('wp_ajax_ai_studio_upload_image', $ajax_handler, 'upload_image');
        $this->loader->add_action( 'wp_ajax_ai_studio_save_post', $ajax_handler, 'save_post');
        // $this->loader->add_action( 'wp_ajax_ai_studio_get_history', $ajax_handler, 'get_history' );
    }

    /**
     * Register all public hooks
     */
    private function define_public_hooks() {
        // Future public hooks can be added here
    }

    /**
     * Run the loader to execute all hooks
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * Get the plugin name
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * Get the plugin version
     */
    public function get_version() {
        return $this->version;
    }

    /**
     * Get the loader
     */
    public function get_loader() {
        return $this->loader;
    }
}