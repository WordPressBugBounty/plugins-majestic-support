<?php
if(!defined('ABSPATH')) die('Restricted Access');

MJTC_message::MJTC_getMessage();
?>

<div id="msadmin-wrapper">
    <div id="msadmin-leftmenu">
        <?php MJTC_includer::MJTC_getClassesInclude('msadminsidemenu'); ?>
    </div>
    
    <div id="msadmin-data">
        <?php MJTC_includer::MJTC_getModel('majesticsupport')->getPageTitle('zywrap_errors'); ?>
        
        <div id="msadmin-data-wrp" class="bg-n bs-n mjtc-ai-app">
            <div class="mjtc-app-grid">
                
                <div class="mjtc-stream-column">
                    
                    <div class="mjtc-stream-header" style="display:none;">
                        <form class="js-filter-form" name="majesticsupportform" id="majesticsupportform" method="post" action="<?php echo esc_url(wp_nonce_url(admin_url("admin.php?page=majesticsupport_zywrap&mjslay=zywrap_errors"),"zywrap_errors")); ?>">
                            <?php echo wp_kses(MJTC_formfield::MJTC_hidden('MJTC_form_search', 'MJTC_SEARCH'), MJTC_ALLOWED_TAGS); ?>
                            <?php 
                                $page_size_val = isset(majesticsupport::$_data['filter']['pagesize']) ? majesticsupport::$_data['filter']['pagesize'] : 20;
                                echo wp_kses(MJTC_formfield::MJTC_select('pagesize', array((object) array('id'=>20,'text'=>20), (object) array('id'=>50,'text'=>50), (object) array('id'=>100,'text'=>100)), $page_size_val, esc_html(__("Records per page",'majestic-support')), array('class' => 'js-form-input-field','onchange'=>'document.majesticsupportform.submit();')), MJTC_ALLOWED_TAGS); 
                            ?>
                        </form>
                    </div>

                    <div class="mjtc-feed-container">
                        <?php if (!empty(majesticsupport::$_data[0])) : ?>
                            <?php foreach (majesticsupport::$_data[0] AS $err) : ?>
                                <div class="mjtc-error-card">
                                    <div class="mjtc-error-card-header">
                                        <div class="mjtc-error-info">
                                            <div class="mjtc-error-wrapper-name">
                                                <span class="dashicons dashicons-warning" style="color: #ef4444;"></span>
                                                <?php echo esc_html($err->wrapper_code); ?>
                                            </div>
                                            <div class="mjtc-error-meta">
                                                <span title="<?php echo esc_attr__('Date Recorded', 'majestic-support'); ?>">
                                                    <span class="dashicons dashicons-calendar-alt" style="font-size:14px;width:14px;height:14px;"></span>
                                                    <?php echo esc_html(date_i18n(majesticsupport::$_config['date_format'] . ' H:i', MJTC_majesticsupportphplib::MJTC_strtotime($err->created_at))); ?>
                                                </span>
                                                <span title="<?php echo esc_attr__('Agent ID', 'majestic-support'); ?>">
                                                    <span class="dashicons dashicons-admin-users" style="font-size:14px;width:14px;height:14px;"></span>
                                                    <?php
                                                    if (!empty($err->agent_id)) {
                                                        echo esc_html__('Agent UID:', 'majestic-support') . ' ' . esc_html($err->agent_id);
                                                    }
                                                    ?>
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <a title="<?php echo esc_attr__('Delete Log', 'majestic-support'); ?>" 
                                           class="mjtc-btn-delete" 
                                           onclick="return confirm('<?php echo esc_js(__('Are you sure you want to delete this error log?', 'majestic-support')); ?>');" 
                                           href="<?php echo esc_url(wp_nonce_url(majesticsupport::makeUrl(array('mjsmod'=>'zywrap', 'task'=>'delete_log', 'action'=>'mstask', 'id'=>$err->id, 'mspageid'=>get_the_ID())),'delete_log_'.$err->id)); ?>">
                                            <span class="dashicons dashicons-trash"></span>
                                        </a>
                                    </div>
                                    
                                    <div class="mjtc-error-message-box">
                                        <?php echo esc_html($err->error_message); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <div class="mjtc-empty-state">
                                <span class="dashicons dashicons-shield" style="font-size: 48px; height: 48px; width: 48px; color: #10b981; margin-bottom: 16px;"></span>
                                <h2><?php echo esc_html__('System is Healthy', 'majestic-support'); ?></h2>
                                <p><?php echo esc_html__('No API errors logged! The engine is running perfectly without any recorded exceptions.', 'majestic-support'); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (isset(majesticsupport::$_data[1])) : ?>
                        <div class="mjtc-pagination-wrapper">
                            <div class="tablenav"><div class="tablenav-pages"><?php echo wp_kses_post(majesticsupport::$_data[1]); ?></div></div>
                        </div>
                    <?php endif; ?>

                </div>

                <div class="mjtc-engine-panel">
                    
                    <div class="mjtc-engine-status">
                        <div style="color:#a1a1aa; font-weight: 500; font-size: 0.85rem; text-transform: uppercase;"><?php echo esc_html__('Error Tracking', 'majestic-support'); ?></div>
                        <div class="mjtc-status-indicator">
                            <div class="mjtc-pulse online"></div> <?php echo esc_html__('MONITORING', 'majestic-support'); ?>
                        </div>
                    </div>

                    <div class="mjtc-control-group">
                        <div class="mjtc-engine-section-title"><?php echo esc_html__('System Navigation', 'majestic-support'); ?></div>
                        
                        <a href="<?php echo esc_url(admin_url('admin.php?page=majesticsupport_zywrap&mjslay=zywrap_logs')); ?>" class="mjtc-btn-app">
                            <span><span class="dashicons dashicons-list-view" style="vertical-align:middle; margin-right:8px;"></span> <?php echo esc_html__('All Activity Logs', 'majestic-support'); ?></span>
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
