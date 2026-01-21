<?php 
/**
 * Plugin Name: AI Content Studio
 * Description: Generate high-quality WordPress posts and images with AI-powered content.
 * Version: 1.0.0
 * Author: Nabin Gharti Magar
 * Author URI: https://nabinmagar.com
 * License: GPL2+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: ai-content-studio
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 */

// Exit if accessed directly
if ( ! defined ( "ABSPATH" ) ){
    die;
}

// Define Constants
define ( "AI_CONTENT_STUDIO_VERSION", "1.0.0" );
define ( "AI_CONTENT_STUDIO_PLUGIN_DIR", plugin_dir_path( __FILE__ ));
define ( "AI_CONTENT_STUDIO_PLUGIN_URL", plugin_dir_url( __FILE__ ));

/**
 * Code that runs during plugin activation
 */
function activate_ai_content_studio() {
    require_once AI_CONTENT_STUDIO_PLUGIN_DIR . 'includes/class-activator.php';
    AI_Content_Studio_Activator::activate();
}

/**
 * Code that runs during plugin deactivation
 */
function deactivate_ai_content_studio() {
    require_once AI_CONTENT_STUDIO_PLUGIN_DIR . 'includes/class-deactivator.php';
    AI_Content_Studio_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_ai_content_studio');
register_deactivation_hook(__FILE__, 'deactivate_ai_content_studio');

/**
 * Core plugin class
 */
require AI_CONTENT_STUDIO_PLUGIN_DIR . 'includes/class-ai-content-studio.php';

/**
 * Begin execution of the plugin
 */
function run_ai_content_studio() {
    $plugin = new AI_CONTENT_STUDIO();
    $plugin->run();
}
run_ai_content_studio();
