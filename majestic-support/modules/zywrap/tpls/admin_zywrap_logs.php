<?php
if(!defined('ABSPATH')) die('Restricted Access');

MJTC_message::MJTC_getMessage();
?>

<div id="msadmin-wrapper">
    <div id="msadmin-leftmenu">
        <?php MJTC_includer::MJTC_getClassesInclude('msadminsidemenu'); ?>
    </div>
    
    <div id="msadmin-data">
        <?php MJTC_includer::MJTC_getModel('majesticsupport')->getPageTitle('zywrap_logs'); ?>
        
        <div id="msadmin-data-wrp" class="bg-n bs-n mjtc-ai-app">
            <div class="mjtc-app-grid">
                
                <!-- ==========================================
                     LEFT: LOGS STREAM 
                     ========================================== -->
                <div class="mjtc-stream-column">

                    <!-- Filter Box -->
                    <div class="mjtc-filter-box" style="display: none;">
                        <form class="js-filter-form" name="majesticsupportform" id="majesticsupportform" method="post" action="<?php echo esc_url(wp_nonce_url(admin_url("admin.php?page=majesticsupport_zywrap&mjslay=zywrap_logs"),"zywrap_logs")); ?>">
                            <?php echo wp_kses(MJTC_formfield::MJTC_text('trace_id', isset(majesticsupport::$_data['filter']['trace_id']) ? majesticsupport::$_data['filter']['trace_id'] : '', array('placeholder' => esc_html(__('Search Trace ID', 'majestic-support')),'class' => 'js-form-input-field')), MJTC_ALLOWED_TAGS); ?>
                            <?php echo wp_kses(MJTC_formfield::MJTC_hidden('MJTC_form_search', 'MJTC_SEARCH'), MJTC_ALLOWED_TAGS); ?>
                            
                            <?php echo wp_kses(MJTC_formfield::MJTC_submitbutton('go', esc_html(__('Search', 'majestic-support')), array('class' => 'button js-form-search')), MJTC_ALLOWED_TAGS); ?>
                            <?php echo wp_kses(MJTC_formfield::MJTC_button('reset', esc_html(__('Reset', 'majestic-support')), array('class' => 'button js-form-reset', 'onclick' => 'document.getElementById("trace_id").value=""; document.getElementById("majesticsupportform").submit();')), MJTC_ALLOWED_TAGS); ?>
                            
                            <?php echo wp_kses(MJTC_formfield::MJTC_select('pagesize', array((object) array('id'=>20,'text'=>20), (object) array('id'=>50,'text'=>50), (object) array('id'=>100,'text'=>100)), isset(majesticsupport::$_data['filter']['pagesize']) ? majesticsupport::$_data['filter']['pagesize'] : 20 ,esc_html(__("Records per page",'majestic-support')), array('class' => 'js-form-input-field js-right','onchange'=>'document.majesticsupportform.submit();')), MJTC_ALLOWED_TAGS); ?>
                        </form>
                    </div>

                    <!-- Data Table -->
                    <?php if (!empty(majesticsupport::$_data[0])) : ?>
                        <form class="msadmin-form" method="post" action="#">
                            <div class="mjtc-table-card">
                                <div class="mjtc-table-responsive">
                                    <table class="mjtc-clean-table">
                                        <thead>
                                            <tr>
                                                <th><?php echo esc_html(__('Trace ID', 'majestic-support')); ?></th>
                                                <th><?php echo esc_html(__('Action / Wrapper', 'majestic-support')); ?></th>
                                                <th><?php echo esc_html(__('Model', 'majestic-support')); ?></th>
                                                <th><?php echo esc_html(__('Tokens', 'majestic-support')); ?></th>
                                                <th><?php echo esc_html(__('Latency', 'majestic-support')); ?></th>
                                                <th><?php echo esc_html(__('Date', 'majestic-support')); ?></th>
                                                <th><?php echo esc_html(__('Status', 'majestic-support')); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach (majesticsupport::$_data[0] AS $log) : ?>
                                                <tr>
                                                    <td class="mjtc-trace-cell" title="<?php echo esc_attr($log->trace_id); ?>">
                                                        <?php echo esc_html(substr($log->trace_id, 0, 18)) . '...'; ?>
                                                    </td>
                                                    <td>
                                                        <span class="mjtc-log-title"><?php echo esc_html(str_replace('_', ' ', ucwords($log->wrapper_code))); ?></span>
                                                        <span class="mjtc-log-code"><?php echo esc_html($log->wrapper_code); ?></span>
                                                    </td>
                                                    <td>
                                                        <span class="mjtc-model-tag">
                                                            <?php echo esc_html($log->model_code == 'default' ? __('Auto-Select', 'majestic-support') : $log->model_code); ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="mjtc-token-tag">
                                                            <?php echo esc_html(number_format($log->total_tokens)); ?>
                                                        </span>
                                                    </td>
                                                    <td class="mjtc-latency-cell"><?php echo esc_html(number_format($log->latency_ms)); ?> ms</td>
                                                    <td class="mjtc-date-cell"><?php echo esc_html(date_i18n(majesticsupport::$_config['date_format'] . ' H:i:s', MJTC_majesticsupportphplib::MJTC_strtotime($log->created_at))); ?></td>
                                                    <td>
                                                        <img alt="<?php echo esc_attr(__('Success', 'majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL) . 'includes/images/good.png'; ?>" title="<?php echo esc_attr(__('Success', 'majestic-support')); ?>" />
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </form>
                        
                        <!-- Pagination -->
                        <?php if (isset(majesticsupport::$_data[1])) : ?>
                            <div class="mjtc-pagination-wrapper">
                                <div class="tablenav"><div class="tablenav-pages"><?php echo wp_kses_post(majesticsupport::$_data[1]); ?></div></div>
                            </div>
                        <?php endif; ?>

                    <?php else : ?>
                        <!-- Empty State Wrapper (Replaces Native No-Record) -->
                        <div class="mjtc-empty-state">
                            <span class="dashicons dashicons-format-chat" style="font-size: 48px; height: 48px; width: 48px; color: #a1a1aa; margin-bottom: 16px;"></span>
                            <h2><?php echo esc_html__('No Logs Found', 'majestic-support'); ?></h2>
                            <p><?php echo esc_html__('No successful API interactions have been recorded yet.', 'majestic-support'); ?></p>
                        </div>
                    <?php endif; ?>

                </div>

                <!-- ==========================================
                     RIGHT: THE ENGINE PANEL
                     ========================================== -->
                <div class="mjtc-engine-panel">
                    
                    <div class="mjtc-engine-status">
                        <div style="color:#a1a1aa; font-weight: 500; font-size: 0.85rem; text-transform: uppercase;"><?php echo esc_html__('Audit Engine', 'majestic-support'); ?></div>
                        <div class="mjtc-status-indicator">
                            <div class="mjtc-pulse online"></div> <?php echo esc_html__('TRACKING', 'majestic-support'); ?>
                        </div>
                    </div>

                    <div class="mjtc-control-group">
                        <div class="mjtc-engine-section-title"><?php echo esc_html__('System Navigation', 'majestic-support'); ?></div>
                        
                        <a href="<?php echo esc_url(admin_url('admin.php?page=majesticsupport_zywrap&mjslay=zywrap_errors')); ?>" class="mjtc-btn-app">
                            <span><span class="dashicons dashicons-warning" style="vertical-align:middle; margin-right:8px; color: #ef4444;"></span> <?php echo esc_html__('System Exceptions', 'majestic-support'); ?></span>
                            <span style="color:#71717a;">&rarr;</span>
                        </a>

                        <a href="<?php echo esc_url(admin_url('admin.php?page=majesticsupport_zywrap&mjslay=zywrap_settings')); ?>" class="mjtc-btn-app">
                            <span><span class="dashicons dashicons-admin-settings" style="vertical-align:middle; margin-right:8px;"></span> <?php echo esc_html__('Global Settings', 'majestic-support'); ?></span>
                            <span style="color:#71717a;">&rarr;</span>
                        </a>
                        
                        <a href="<?php echo esc_url(admin_url('admin.php?page=majesticsupport_zywrap&mjslay=zywrap_playground')); ?>" class="mjtc-btn-app">
                            <span><span class="dashicons dashicons-editor-code" style="vertical-align:middle; margin-right:8px;"></span> <?php echo esc_html__('AI Playground', 'majestic-support'); ?></span>
                            <span style="color:#71717a;">&rarr;</span>
                        </a>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>