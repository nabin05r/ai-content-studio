<?php 
/**
 * Fired during plugin activation
 * 
 * @package AI_Content_Studio
 * @subpackage AI_Content_Studio/includes
 */

class AI_Content_Studio_Activator {

    /**
     * Plugin activation handler
     * 
     * Creates database tables and set default options
     */
    public static function activate() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . 'ai_content_studio_history';

        // Create table SQL
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            type varchar(20) NOT NULL DEFAULT 'content',
            title varchar(255) NOT NULL,
            provider varchar(50) NOT NULL,
            model varchar(100) DEFAULT NULL,
            tone varchar(50) DEFAULT NULL,
            word_count int(11) DEFAULT 0,
            tokens_used int(11) DEFAULT 0,
            cost decimal(10,4) DEFAULT 0,
            generation_time float DEFAULT 0,
            status varchar(20) DEFAULT 'completed',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY type (type),
            KEY provider (provider),
            KEY created_at (created_at)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        // Set default options
        $default_options = array(
            'gemini_api_key' => '',
            'default_model' => 'gemini',
            'default_tone' => 'professional',
            'default_length' => 'medium',
            'auto_save' => 'draft',
            'enable_history' => true,
            'rate_limit' => 60
        );
        
        // Only add if not exists
        if (!get_option('ai_content_studio_settings')) {
            add_option('ai_content_studio_settings', $default_options);
        }

        // Add plugin version
        update_option('ai_content_studio_version', AI_CONTENT_STUDIO_VERSION);

        // Create upload directory for AI generated images
        $upload_dir = wp_upload_dir();
        $ai_studio_dir = $upload_dir['basedir'] . '/ai-content-studio';

        if (!file_exists($ai_studio_dir)) {
            wp_mkdir_p($ai_studio_dir);
        }

        // Set activation timestamp
        if (!get_option('ai_content_studio_activation_time')) {
            add_option('ai_content_studio_activation_time', current_time('mysql'));
        }

        // Run database migration for existing installations
        self::migrate_database();

        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    /**
     * Migrate existing database to new schema
     */
    private static function migrate_database() {
        global $wpdb;
        $table = $wpdb->prefix . 'ai_content_studio_history';
        
        // Check if table exists
        if ($wpdb->get_var("SHOW TABLES LIKE '$table'") != $table) {
            return; // Table doesn't exist yet, will be created
        }
        
        // Get current columns
        $columns = $wpdb->get_col("DESCRIBE $table");
        
        // Add type column if missing
        if (!in_array('type', $columns)) {
            $wpdb->query("ALTER TABLE $table ADD COLUMN type varchar(20) NOT NULL DEFAULT 'content' AFTER user_id");
        }
        
        // Rename ai_provider to provider if needed
        if (in_array('ai_provider', $columns) && !in_array('provider', $columns)) {
            $wpdb->query("ALTER TABLE $table CHANGE ai_provider provider varchar(50) NOT NULL");
        }
        
        // Rename model_used to model if needed
        if (in_array('model_used', $columns) && !in_array('model', $columns)) {
            $wpdb->query("ALTER TABLE $table CHANGE model_used model varchar(100) DEFAULT NULL");
        }
        
        // Remove unused columns
        $unused_columns = array('post_id', 'description', 'featured_image_generated', 'updated_at');
        foreach ($unused_columns as $column) {
            if (in_array($column, $columns)) {
                $wpdb->query("ALTER TABLE $table DROP COLUMN $column");
            }
        }
    }
}