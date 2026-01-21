<?php 
/**
 * Fired during plugin deactivation 
 * 
 * @package AI_Content_Studio
 * @subpackage AI_Content_Studio/includes
 */

class AI_Content_Studio_Deactivator {

    /**
     * Plugin deactivation handler
     * 
     * Cleans up temporary data and scheduled events
     */
    public static function deactivate() {

        // Clear scheduled cron events if any
        wp_clear_scheduled_hook( 'ai_content_studio_cleanup' );

        // Flush rewrite rules
        flush_rewrite_rules();
    }
}