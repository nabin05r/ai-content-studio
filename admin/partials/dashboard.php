<?php
/**
 * Dashboard Template
 *
 * @package AI_Content_Studio
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap ai-studio-dashboard">
    <h1><?php _e('AI Content Studio Dashboard', 'ai-content-studio'); ?></h1>
    
    <?php if (!$gemini_configured): ?>
        <!-- Setup Warning -->
        <div class="notice notice-warning">
            <p>
                <strong><?php _e('⚠️ Setup Required:', 'ai-content-studio'); ?></strong>
                <?php _e('Please configure your Gemini API key to start generating content.', 'ai-content-studio'); ?>
                <a href="<?php echo admin_url('admin.php?page=ai-content-studio-settings'); ?>" class="button button-primary">
                    <?php _e('Configure Now', 'ai-content-studio'); ?>
                </a>
            </p>
        </div>
    <?php endif; ?>
    
    <!-- Stats Cards -->
    <div class="ai-studio-stats-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin: 30px 0;">
        
        <!-- Content Generated -->
        <div class="ai-studio-stat-card" style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <p style="margin: 0; color: #666; font-size: 14px;"><?php _e('Content Generated', 'ai-content-studio'); ?></p>
                    <h2 style="margin: 10px 0 0 0; font-size: 32px; font-weight: 600;"><?php echo $stats['content_count']; ?></h2>
                    <p style="margin: 5px 0 0 0; color: #999; font-size: 12px;"><?php _e('This month', 'ai-content-studio'); ?></p>
                </div>
                <div style="font-size: 40px;">📝</div>
            </div>
        </div>
        
        <!-- Images Generated -->
        <div class="ai-studio-stat-card" style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <p style="margin: 0; color: #666; font-size: 14px;"><?php _e('Images Generated', 'ai-content-studio'); ?></p>
                    <h2 style="margin: 10px 0 0 0; font-size: 32px; font-weight: 600;"><?php echo $stats['image_count']; ?></h2>
                    <p style="margin: 5px 0 0 0; color: #999; font-size: 12px;"><?php _e('This month', 'ai-content-studio'); ?></p>
                </div>
                <div style="font-size: 40px;">🎨</div>
            </div>
        </div>
        
        <!-- Total Cost -->
        <div class="ai-studio-stat-card" style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <p style="margin: 0; color: #666; font-size: 14px;"><?php _e('Total Cost', 'ai-content-studio'); ?></p>
                    <h2 style="margin: 10px 0 0 0; font-size: 32px; font-weight: 600;">$<?php echo number_format($stats['total_cost'], 2); ?></h2>
                    <p style="margin: 5px 0 0 0; color: #999; font-size: 12px;"><?php _e('This month', 'ai-content-studio'); ?></p>
                </div>
                <div style="font-size: 40px;">💰</div>
            </div>
        </div>
        
        <!-- Average Time -->
        <div class="ai-studio-stat-card" style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <p style="margin: 0; color: #666; font-size: 14px;"><?php _e('Avg Generation Time', 'ai-content-studio'); ?></p>
                    <h2 style="margin: 10px 0 0 0; font-size: 32px; font-weight: 600;"><?php echo $stats['avg_time']; ?>s</h2>
                    <p style="margin: 5px 0 0 0; color: #999; font-size: 12px;"><?php _e('Per content', 'ai-content-studio'); ?></p>
                </div>
                <div style="font-size: 40px;">⏱️</div>
            </div>
        </div>
        
    </div>
    
    <!-- Quick Actions -->
    <div class="ai-studio-quick-actions" style="background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 30px;">
        <h2 style="margin-top: 0;"><?php _e('⚡ Quick Actions', 'ai-content-studio'); ?></h2>
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px;">
            
            <a href="<?php echo admin_url('admin.php?page=ai-content-studio-generate#content'); ?>" class="button button-primary button-hero" style="height: auto; padding: 20px; text-align: center; text-decoration: none;">
                <span class="dashicons dashicons-edit" style="font-size: 24px; width: 24px; height: 24px;"></span><br>
                <strong><?php _e('Generate Content', 'ai-content-studio'); ?></strong>
            </a>
            
            <a href="<?php echo admin_url('admin.php?page=ai-content-studio-generate#image'); ?>" class="button button-primary button-hero" style="height: auto; padding: 20px; text-align: center; text-decoration: none;">
                <span class="dashicons dashicons-format-image" style="font-size: 24px; width: 24px; height: 24px;"></span><br>
                <strong><?php _e('Generate Image', 'ai-content-studio'); ?></strong>
            </a>
            
            <a href="<?php echo admin_url('admin.php?page=ai-content-studio-settings'); ?>" class="button button-secondary button-hero" style="height: auto; padding: 20px; text-align: center; text-decoration: none;">
                <span class="dashicons dashicons-admin-settings" style="font-size: 24px; width: 24px; height: 24px;"></span><br>
                <strong><?php _e('Settings', 'ai-content-studio'); ?></strong>
            </a>
            
            <a href="<?php echo admin_url('admin.php?page=ai-content-studio-history'); ?>" class="button button-secondary button-hero" style="height: auto; padding: 20px; text-align: center; text-decoration: none;">
                <span class="dashicons dashicons-chart-bar" style="font-size: 24px; width: 24px; height: 24px;"></span><br>
                <strong><?php _e('View History', 'ai-content-studio'); ?></strong>
            </a>
            
        </div>
    </div>
    
    <!-- Two Column Layout -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
        
        <!-- Recent Activity -->
        <div class="ai-studio-recent-activity" style="background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h2 style="margin-top: 0;"><?php _e('🕒 Recent Activity', 'ai-content-studio'); ?></h2>
            
            <?php if (empty($recent_generations)): ?>
                <p style="color: #666; text-align: center; padding: 40px 0;">
                    <?php _e('No activity yet. Start by generating some content!', 'ai-content-studio'); ?>
                </p>
            <?php else: ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php _e('Type', 'ai-content-studio'); ?></th>
                            <th><?php _e('Title', 'ai-content-studio'); ?></th>
                            <th><?php _e('Provider', 'ai-content-studio'); ?></th>
                            <th><?php _e('Time', 'ai-content-studio'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_generations as $activity): ?>
                            <tr>
                                <td>
                                    <?php if (isset($activity->type) && $activity->type === 'image'): ?>
                                        <span class="dashicons dashicons-format-image"></span> <?php _e('Image', 'ai-content-studio'); ?>
                                    <?php else: ?>
                                        <span class="dashicons dashicons-edit"></span> <?php _e('Content', 'ai-content-studio'); ?>
                                    <?php endif; ?>
                                </td>
                                <td><strong><?php echo esc_html($activity->title); ?></strong></td>
                                <td><?php echo esc_html($activity->provider); ?></td>
                                <td><?php echo human_time_diff(strtotime($activity->created_at), current_time('timestamp')) . ' ' . __('ago', 'ai-content-studio'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        
        <!-- System Status -->
        <div class="ai-studio-system-status" style="background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h2 style="margin-top: 0;"><?php _e('🔧 System Status', 'ai-content-studio'); ?></h2>
            
            <div style="margin-bottom: 20px;">
                <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px; background: #f0f6fc; border-radius: 4px; margin-bottom: 10px;">
                    <span><strong>Gemini API</strong></span>
                    <?php if ($gemini_configured): ?>
                        <span style="color: #00a32a;">✓ <?php _e('Connected', 'ai-content-studio'); ?></span>
                    <?php else: ?>
                        <span style="color: #dc3232;">✗ <?php _e('Not configured', 'ai-content-studio'); ?></span>
                    <?php endif; ?>
                </div>
                
                <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px; background: #f0f6fc; border-radius: 4px;">
                    <span><strong>Pollinations.ai</strong></span>
                    <span style="color: #00a32a;">✓ <?php _e('Active (FREE)', 'ai-content-studio'); ?></span>
                </div>
            </div>
            
            <h3><?php _e('💡 Tips', 'ai-content-studio'); ?></h3>
            <ul style="margin: 0; padding-left: 20px; color: #666; font-size: 13px; line-height: 1.6;">
                <li><?php _e('Be specific with prompts for better results', 'ai-content-studio'); ?></li>
                <li><?php _e('Images take 30-90 seconds to generate', 'ai-content-studio'); ?></li>
                <li><?php _e('Save generated content as drafts first', 'ai-content-studio'); ?></li>
                <li><?php _e('Free tier: 60 requests/min with Gemini', 'ai-content-studio'); ?></li>
            </ul>
        </div>
        
    </div>
    
</div>

<style>
.ai-studio-dashboard h2 {
    font-size: 18px;
    font-weight: 600;
}

@media (max-width: 782px) {
    .ai-studio-stats-grid,
    .ai-studio-quick-actions > div {
        grid-template-columns: 1fr !important;
    }
    
    .ai-studio-dashboard > div[style*="grid-template-columns: 2fr 1fr"] {
        grid-template-columns: 1fr !important;
    }
}
</style>