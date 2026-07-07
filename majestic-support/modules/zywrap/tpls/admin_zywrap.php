<?php
if (!defined('ABSPATH')) die('Restricted Access');

// 1. Safe Data Retrieval
$dashboard_data = isset(majesticsupport::$_data['dashboard_stats']) ? majesticsupport::$_data['dashboard_stats'] : array();

$api_key      = isset($dashboard_data['api_key']) ? $dashboard_data['api_key'] : '';
$last_sync    = isset($dashboard_data['last_sync']) ? $dashboard_data['last_sync'] : '';
$total_reqs   = isset($dashboard_data['total_reqs']) ? $dashboard_data['total_reqs'] : 0;
$total_toks   = isset($dashboard_data['total_toks']) ? $dashboard_data['total_toks'] : 0;
$total_errs   = isset($dashboard_data['total_errs']) ? $dashboard_data['total_errs'] : 0;
$recent_logs  = isset($dashboard_data['recent_logs']) ? $dashboard_data['recent_logs'] : array();

$is_connected = !empty($api_key);

// Fetch WP messages safely
MJTC_message::MJTC_getMessage();
?>

<div id="msadmin-wrapper">
    <div id="msadmin-leftmenu">
        <?php MJTC_includer::MJTC_getClassesInclude('msadminsidemenu'); ?>
    </div>
    
    <div id="msadmin-data">
        <?php MJTC_includer::MJTC_getModel('majesticsupport')->getPageTitle('zywrap'); ?>
        
        <!-- App Container -->
        <div id="msadmin-data-wrp" class="bg-n bs-n mjtc-ai-app">
            <div class="mjtc-app-grid">                
                <!-- ==========================================
                     LEFT: ACTIVITY STREAM 
                     ========================================== -->
                    <div class="mjtc-stream-column">
                        <div class="mjtc-table-card">
                            <div class="mjtc-table-responsive">
                                <?php if (empty($recent_logs)) { ?>
                                    <div class="mjtc-empty-state">
                                        <span class="dashicons dashicons-format-chat" style="font-size: 48px; height: 48px; width: 48px; color: #a1a1aa; margin-bottom: 16px;"></span>
                                        <h2><?php echo esc_html__('No Record Found', 'majestic-support'); ?></h2>
                                        <p><?php echo esc_html__('No AI interactions have been recorded yet.', 'majestic-support'); ?></p>
                                    </div>
                                <?php } else { ?>
                                    <table class="mjtc-clean-table">
                                        <thead>
                                            <tr>
                                                <th><?php echo esc_html__('Action / Wrapper', 'majestic-support'); ?></th>
                                                <th><?php echo esc_html__('Model', 'majestic-support'); ?></th>
                                                <th><?php echo esc_html__('Tokens', 'majestic-support'); ?></th>
                                                <th><?php echo esc_html__('Latency', 'majestic-support'); ?></th>
                                                <th><?php echo esc_html__('Status', 'majestic-support'); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recent_logs as $log) : ?>
                                                <tr>
                                                    <td>
                                                        <strong class="mjtc-log-title"><?php echo esc_html(str_replace('_', ' ', ucwords($log->wrapper_code))); ?></strong>
                                                        <span class="mjtc-log-code"><?php echo esc_html($log->wrapper_code); ?></span>
                                                    </td>
                                                    <td>
                                                        <span class="mjtc-model-tag">
                                                            <?php echo esc_html($log->model_code === 'default' ? __('Auto-Model', 'majestic-support') : $log->model_code); ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="mjtc-token-tag">
                                                            <?php echo number_format($log->total_tokens); ?>
                                                        </span>
                                                    </td>
                                                    <td class="mjtc-latency-cell">
                                                        <?php echo number_format($log->latency_ms) . ' ' . esc_html__('MS', 'majestic-support'); ?>
                                                    </td>
                                                    <td>
                                                        <span class="mjtc-feed-action <?php echo $log->status === 'success' ? 'success' : 'error'; ?>">
                                                            <?php echo esc_html( strtoupper( $log->status ) ); ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                <?php } ?>
                            </div>
                        </div>

                        <?php if (!empty($recent_logs)) : ?>
                            <a href="<?php echo esc_url(admin_url('admin.php?page=majesticsupport_zywrap&mjslay=zywrap_logs')); ?>" class="mjtc-table-history-link">
                                <?php echo esc_html__('View Complete History', 'majestic-support'); ?> &rarr;
                            </a>
                        <?php endif; ?>
                    </div>

                <!-- ==========================================
                     RIGHT: THE ENGINE PANEL
                     ========================================== -->
                <div class="mjtc-engine-panel">
                    
                    <div class="mjtc-engine-status">
                        <div style="color:#a1a1aa; font-weight: 500;"><?php echo esc_html__('ENGINE STATUS', 'majestic-support'); ?></div>
                        <?php if ($is_connected) : ?>
                            <div class="mjtc-status-indicator" style="color: #10b981;">
                                <div class="mjtc-pulse online"></div> <?php echo esc_html__('ONLINE', 'majestic-support'); ?>
                            </div>
                        <?php else : ?>
                            <div class="mjtc-status-indicator" style="color: #ef4444;">
                                <div class="mjtc-pulse offline"></div> <?php echo esc_html__('OFFLINE', 'majestic-support'); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mjtc-metrics-stack">
                        <div class="mjtc-metric-row">
                            <span><?php echo esc_html__('Requests Processed', 'majestic-support'); ?></span>
                            <span><?php echo number_format($total_reqs); ?></span>
                        </div>
                        <div class="mjtc-metric-row">
                            <span><?php echo esc_html__('Tokens Consumed', 'majestic-support'); ?></span>
                            <span><?php echo number_format($total_toks); ?></span>
                        </div>
                        <div class="mjtc-metric-row">
                            <span><?php echo esc_html__('Logged Exceptions', 'majestic-support'); ?></span>
                            <span class="<?php echo $total_errs > 0 ? 'mjtc-text-error' : ''; ?>">
                                <?php echo number_format($total_errs); ?>
                            </span>
                        </div>
                    </div>

                    <div class="mjtc-control-group">
                        <div class="mjtc-engine-section-title" style="margin-top: 8px;"><?php echo esc_html__('Control Hub', 'majestic-support'); ?></div>
                        
                        <a href="<?php echo esc_url(admin_url('admin.php?page=majesticsupport_zywrap&mjslay=zywrap_settings')); ?>" class="mjtc-btn-app">
                            <span><span class="dashicons dashicons-admin-settings" style="vertical-align:middle; margin-right:8px;"></span> <?php echo esc_html__('Global Settings', 'majestic-support'); ?></span>
                            <span style="color:#71717a;">&rarr;</span>
                        </a>
                        
                        <a href="<?php echo esc_url(admin_url('admin.php?page=majesticsupport_zywrap&mjslay=zywrap_playground')); ?>" class="mjtc-btn-app">
                            <span><span class="dashicons dashicons-editor-code" style="vertical-align:middle; margin-right:8px;"></span> <?php echo esc_html__('AI Playground', 'majestic-support'); ?></span>
                            <span style="color:#71717a;">&rarr;</span>
                        </a>

                        <?php if (!$is_connected) : ?>
                            <a href="<?php echo esc_url(admin_url('admin.php?page=majesticsupport_zywrap&mjslay=zywrap_settings')); ?>" class="mjtc-btn-sync-action" style="background: #ef4444;">
                                <?php echo esc_html__('Connect API Key', 'majestic-support'); ?>
                            </a>
                        <?php else : ?>
                            <a href="<?php echo esc_url(admin_url('admin.php?page=majesticsupport_zywrap&mjslay=zywrap_settings')); ?>" class="mjtc-btn-sync-action">
                                <?php echo esc_html__('Execute Database Sync', 'majestic-support'); ?>
                            </a>
                        <?php endif; ?>
                        
                        <div class="mjtc-sync-meta">
                            <?php echo esc_html__('Last synchronization:', 'majestic-support'); ?> <?php echo empty($last_sync) ? esc_html__('Never', 'majestic-support') : esc_html(wp_date('M j, Y - g:i A', $last_sync)); ?>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>
