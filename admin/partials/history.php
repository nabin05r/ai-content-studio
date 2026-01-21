<?php
/**
 * Generation History Page
 *
 * @package AI_Content_Studio
 */

if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;
$table = $wpdb->prefix . 'ai_content_studio_history';
$user_id = get_current_user_id();

// Filters
$filter_type = isset($_GET['filter_type']) ? sanitize_text_field($_GET['filter_type']) : '';
$filter_provider = isset($_GET['filter_provider']) ? sanitize_text_field($_GET['filter_provider']) : '';
$search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';

// Pagination
$per_page = 20;
$paged = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
$offset = ($paged - 1) * $per_page;

// Build query
$where = "WHERE user_id = %d";
$params = array($user_id);

if ($filter_type) {
    $where .= " AND type = %s";
    $params[] = $filter_type;
}

if ($filter_provider) {
    $where .= " AND provider = %s";
    $params[] = $filter_provider;
}

if ($search) {
    $where .= " AND title LIKE %s";
    $params[] = '%' . $wpdb->esc_like($search) . '%';
}

// Get total count
$total = $wpdb->get_var($wpdb->prepare(
    "SELECT COUNT(*) FROM $table $where",
    $params
));

// Get records
$params[] = $per_page;
$params[] = $offset;

$history = $wpdb->get_results($wpdb->prepare(
    "SELECT * FROM $table $where ORDER BY created_at DESC LIMIT %d OFFSET %d",
    $params
));

$total_pages = ceil($total / $per_page);

// Get statistics
$stats = array(
    'total' => $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE user_id = %d", $user_id)),
    'content' => $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE user_id = %d AND type = 'content'", $user_id)),
    'images' => $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE user_id = %d AND type = 'image'", $user_id)),
    'total_cost' => $wpdb->get_var($wpdb->prepare("SELECT SUM(cost) FROM $table WHERE user_id = %d", $user_id)) ?: 0
);
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e('Generation History', 'ai-content-studio'); ?></h1>
    
    <!-- Quick Stats -->
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin: 20px 0;">
        <div style="background: #fff; padding: 15px; border-left: 4px solid #2271b1; border-radius: 4px;">
            <div style="font-size: 24px; font-weight: 600;"><?php echo $stats['total']; ?></div>
            <div style="color: #666; font-size: 13px;"><?php _e('Total Generations', 'ai-content-studio'); ?></div>
        </div>
        <div style="background: #fff; padding: 15px; border-left: 4px solid #00a32a; border-radius: 4px;">
            <div style="font-size: 24px; font-weight: 600;"><?php echo $stats['content']; ?></div>
            <div style="color: #666; font-size: 13px;"><?php _e('Blog Posts', 'ai-content-studio'); ?></div>
        </div>
        <div style="background: #fff; padding: 15px; border-left: 4px solid #f0b849; border-radius: 4px;">
            <div style="font-size: 24px; font-weight: 600;"><?php echo $stats['images']; ?></div>
            <div style="color: #666; font-size: 13px;"><?php _e('Images', 'ai-content-studio'); ?></div>
        </div>
        <div style="background: #fff; padding: 15px; border-left: 4px solid #dc3232; border-radius: 4px;">
            <div style="font-size: 24px; font-weight: 600;">$<?php echo number_format($stats['total_cost'], 2); ?></div>
            <div style="color: #666; font-size: 13px;"><?php _e('Total Cost', 'ai-content-studio'); ?></div>
        </div>
    </div>
    
    <!-- Filters & Search -->
    <div style="background: #fff; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
        <form method="get" style="display: flex; gap: 10px; align-items: end;">
            <input type="hidden" name="page" value="ai-content-studio-history">
            
            <div>
                <label style="display: block; margin-bottom: 5px; font-size: 13px;"><?php _e('Type', 'ai-content-studio'); ?></label>
                <select name="filter_type" style="width: 150px;">
                    <option value=""><?php _e('All Types', 'ai-content-studio'); ?></option>
                    <option value="content" <?php selected($filter_type, 'content'); ?>><?php _e('Content', 'ai-content-studio'); ?></option>
                    <option value="image" <?php selected($filter_type, 'image'); ?>><?php _e('Images', 'ai-content-studio'); ?></option>
                </select>
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 5px; font-size: 13px;"><?php _e('Provider', 'ai-content-studio'); ?></label>
                <select name="filter_provider" style="width: 150px;">
                    <option value=""><?php _e('All Providers', 'ai-content-studio'); ?></option>
                    <option value="gemini" <?php selected($filter_provider, 'gemini'); ?>>Gemini</option>
                    <option value="pollinations" <?php selected($filter_provider, 'pollinations'); ?>>Pollinations</option>
                </select>
            </div>
            
            <div style="flex: 1;">
                <label style="display: block; margin-bottom: 5px; font-size: 13px;"><?php _e('Search', 'ai-content-studio'); ?></label>
                <input type="search" name="s" value="<?php echo esc_attr($search); ?>" placeholder="<?php esc_attr_e('Search by title...', 'ai-content-studio'); ?>" style="width: 100%;">
            </div>
            
            <button type="submit" class="button"><?php _e('Filter', 'ai-content-studio'); ?></button>
            
            <?php if ($filter_type || $filter_provider || $search): ?>
                <a href="<?php echo admin_url('admin.php?page=ai-content-studio-history'); ?>" class="button"><?php _e('Clear', 'ai-content-studio'); ?></a>
            <?php endif; ?>
        </form>
    </div>
    
    <!-- History Table -->
    <?php if (empty($history)): ?>
        <div style="background: #fff; padding: 40px; text-align: center; border-radius: 4px;">
            <span class="dashicons dashicons-admin-generic" style="font-size: 48px; opacity: 0.3;"></span>
            <p style="color: #666; margin-top: 10px;">
                <?php _e('No generation history found.', 'ai-content-studio'); ?>
            </p>
            <?php if ($search || $filter_type || $filter_provider): ?>
                <p><?php _e('Try adjusting your filters.', 'ai-content-studio'); ?></p>
            <?php else: ?>
                <a href="<?php echo admin_url('admin.php?page=ai-content-studio-generate'); ?>" class="button button-primary">
                    <?php _e('Generate Your First Content', 'ai-content-studio'); ?>
                </a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th style="width: 40px;"><?php _e('Type', 'ai-content-studio'); ?></th>
                    <th><?php _e('Title', 'ai-content-studio'); ?></th>
                    <th style="width: 120px;"><?php _e('Provider', 'ai-content-studio'); ?></th>
                    <th style="width: 100px;"><?php _e('Words', 'ai-content-studio'); ?></th>
                    <th style="width: 80px;"><?php _e('Cost', 'ai-content-studio'); ?></th>
                    <th style="width: 80px;"><?php _e('Time', 'ai-content-studio'); ?></th>
                    <th style="width: 150px;"><?php _e('Date', 'ai-content-studio'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($history as $item): ?>
                    <tr>
                        <td style="text-align: center;">
                            <?php if ($item->type === 'image'): ?>
                                <span class="dashicons dashicons-format-image" title="<?php esc_attr_e('Image', 'ai-content-studio'); ?>"></span>
                            <?php else: ?>
                                <span class="dashicons dashicons-edit" title="<?php esc_attr_e('Content', 'ai-content-studio'); ?>"></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong><?php echo esc_html($item->title); ?></strong>
                            <?php if ($item->tone): ?>
                                <br><small style="color: #666;"><?php echo esc_html(ucfirst($item->tone)); ?></small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span style="display: inline-block; padding: 3px 8px; background: #f0f6fc; border-radius: 3px; font-size: 11px;">
                                <?php echo esc_html(ucfirst($item->provider)); ?>
                            </span>
                        </td>
                        <td><?php echo $item->word_count > 0 ? number_format($item->word_count) : '-'; ?></td>
                        <td>
                            <?php if ($item->cost > 0): ?>
                                <span style="color: #dc3232;">$<?php echo number_format($item->cost, 3); ?></span>
                            <?php else: ?>
                                <span style="color: #00a32a;"><?php _e('Free', 'ai-content-studio'); ?></span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo number_format($item->generation_time, 1); ?>s</td>
                        <td>
                            <abbr title="<?php echo esc_attr($item->created_at); ?>">
                                <?php echo human_time_diff(strtotime($item->created_at), current_time('timestamp')) . ' ' . __('ago', 'ai-content-studio'); ?>
                            </abbr>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="tablenav bottom">
                <div class="tablenav-pages">
                    <?php
                    echo paginate_links(array(
                        'base' => add_query_arg('paged', '%#%'),
                        'format' => '',
                        'prev_text' => '&laquo;',
                        'next_text' => '&raquo;',
                        'total' => $total_pages,
                        'current' => $paged
                    ));
                    ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
    
</div>

<style>
@media (max-width: 782px) {
    [style*="grid-template-columns: repeat(4, 1fr)"] {
        grid-template-columns: 1fr 1fr !important;
    }
}
</style>