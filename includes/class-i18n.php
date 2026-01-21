<?php 
/**
 * Define internationalization functionality
 * 
 * @package AI_Content_Studio
 * @subpackage AI_Content_Studio/includes
 */

class AI_Content_Studio_i18n {

    /**
     * Load the plugin text domain for translation
     */
    public function load_plugin_textdomain() {
        load_plugin_textdomain(
            'ai-content-studio',
            false,
            dirname(dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
        );
    }
}