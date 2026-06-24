<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
$majesticsupport_js ="
    function resetFrom() {
        document.getElementById('error').value = '';
        document.getElementById('majesticsupportform').submit();
    }
";
wp_add_inline_script('majestic-support-cmain-js', $majesticsupport_js);
?>  
<?php MJTC_message::MJTC_getMessage(); ?>
<div id="msadmin-wrapper">
    <div id="msadmin-leftmenu">
        <?php  MJTC_includer::MJTC_getClassesInclude('msadminsidemenu'); ?>
    </div>
    <div id="msadmin-data">
        <?php MJTC_includer::MJTC_getModel('majesticsupport')->getPageTitle('systemerror'); ?>
        <div id="msadmin-data-wrp" class="majestic-support-admin-system-error-layout">
            <?php
            if (!empty(majesticsupport::$_data[0])) {
                ?>
                <table id="majestic-support-table">
                    <tr class="majestic-support-table-heading">
                        <th class="left w70 majestic-support-table-title"><?php echo esc_html(__('Error Details', 'majestic-support')); ?></th>
                        <th class="majestic-support-table-created-date"><?php echo esc_html(__('Created', 'majestic-support')); ?></th>
                        <th class="majestic-support-table-actions"><?php echo esc_html(__('Action', 'majestic-support')); ?></th>
                    </tr>
                    <?php
                    foreach (majesticsupport::$_data[0] AS $MJTC_systemerror) {
                        ?>
                        <tr>
                            <td class="left w70 majestic-support-table-title">
                                <span class="majestic-support-table-responsive-heading">
                                    <?php
                                    echo esc_html(__('Error', 'majestic-support'));
                                    echo esc_html(" : ");
                                    ?>
                                </span>
                                <?php
                                $MJTC_raw_error = $MJTC_systemerror->error;
                                $MJTC_error_data = json_decode($MJTC_raw_error, true);

                                if (is_array($MJTC_error_data)) :
                                    ?>
                                    <div class="mjtc-system-error-card">
                                        <div class="mjtc-system-error-row">
                                            <span class="mjtc-support-system-error-icon-badge system-error-badge-rose"><?php echo esc_html(__('Error', 'majestic-support')); ?></span>
                                            <span class="mjtc-system-error-txt"><?php echo esc_html(isset($MJTC_error_data['error']) ? $MJTC_error_data['error'] : __('Unknown Error', 'majestic-support')); ?></span>
                                        </div>

                                        <div class="mjtc-system-error-row">
                                            <span class="mjtc-ai-badge"><?php echo esc_html(__('URL', 'majestic-support')); ?></span>
                                            <span class="mjtc-system-error-url"><?php echo esc_html(isset($MJTC_error_data['url']) ? $MJTC_error_data['url'] : __('N/A', 'majestic-support')); ?></span>
                                        </div>

                                        <details class="mjtc-system-error-details">
                                            <summary>
                                                <span><?php echo esc_html(__('View Query & Trace', 'majestic-support')); ?></span>
                                            </summary>
                                            <div class="mjtc-system-error-expanded">
                                                
                                                <div class="mjtc-system-error-group">
                                                    <div class="mjtc-system-error-title"><?php echo esc_html(__('Path Execution Trace', 'majestic-support')); ?></div>
                                                    <div class="mjtc-system-error-code"><?php echo esc_html(isset($MJTC_error_data['path']) ? $MJTC_error_data['path'] : __('N/A', 'majestic-support')); ?></div>
                                                </div>

                                                <div class="mjtc-system-error-group">
                                                    <div class="mjtc-system-error-title"><?php echo esc_html(__('Database Query', 'majestic-support')); ?></div>
                                                    <div class="mjtc-system-error-code"><?php echo esc_html(isset($MJTC_error_data['query']) ? $MJTC_error_data['query'] : __('N/A', 'majestic-support')); ?></div>
                                                </div>

                                            </div>
                                        </details>
                                    </div>
                                <?php
                                // Backward compatibility block for older plain-text errors
                                elseif (!empty($MJTC_raw_error)) :
                                    ?>
                                    <div class="mjtc-system-error-card">
                                        <div class="mjtc-system-error-row">
                                            <span class="mjtc-support-system-error-icon-badge system-error-badge-rose"><?php echo esc_html(__('Legacy Log', 'majestic-support')); ?></span>
                                        </div>
                                        <div class="mjtc-system-error-code">
                                            <?php echo esc_html($MJTC_raw_error); ?>
                                        </div>
                                    </div>
                                <?php else : ?>
                                    <span><?php echo esc_html(__('No error metadata tracking data recorded.', 'majestic-support')); ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="majestic-support-table-created-date">
                                <span class="majestic-support-table-responsive-heading">
                                    <?php
                                    echo esc_html(__('Created', 'majestic-support'));
                                    echo esc_html(" : ");
                                    ?>
                                </span>
                                <?php echo esc_html(date_i18n(majesticsupport::$_config['date_format'], MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_systemerror->created))); ?>
                            </td>
                            <td class="majestic-support-table-actions">
                                <span class="majestic-support-table-responsive-heading">
                                    <?php
                                    echo esc_html(__('Actions', 'majestic-support'));
                                    echo esc_html(" : ");
                                    ?>
                                </span>
                                <a title="<?php echo esc_attr(__('Delete', 'majestic-support')); ?>" class="action-btn majestic-support-table-delte-action" onclick="return confirm('<?php echo esc_js(__('Are you sure you want to delete this log entry?', 'majestic-support')); ?>');" href="<?php echo esc_url(wp_nonce_url('?page=majesticsupport_systemerror&task=deletesystemerror&action=mstask&systemerrorid=' . esc_attr($MJTC_systemerror->id), 'delete-systemerror-' . esc_attr($MJTC_systemerror->id))); ?>">
                                    <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"></path></svg>
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
                <?php
                if (majesticsupport::$_data[1]) {
                    $MJTC_data = '<div class="tablenav"><div class="tablenav-pages">' . wp_kses_post(majesticsupport::$_data[1]) . '</div></div>';
                    echo wp_kses($MJTC_data, MJTC_ALLOWED_TAGS);
                }
            } else {
                MJTC_layout::MJTC_getNoRecordFound();
            }
            ?>
        </div>
    </div>
</div>
