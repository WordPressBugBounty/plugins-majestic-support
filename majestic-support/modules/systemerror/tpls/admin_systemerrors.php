<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
$majesticsupport_js ="
    function resetFrom() {
        document.getElementById('error').value = '';
        document.getElementById('majesticsupportform').submit();
    }

";
wp_add_inline_script('majestic-support-cmain-js',$majesticsupport_js);
?>  
<?php MJTC_message::MJTC_getMessage(); ?>
<div id="msadmin-wrapper">
    <div id="msadmin-leftmenu">
        <?php  MJTC_includer::MJTC_getClassesInclude('msadminsidemenu'); ?>
    </div>
    <div id="msadmin-data">
        <?php MJTC_includer::MJTC_getModel('majesticsupport')->getPageTitle('systemerror'); ?>
        <div id="msadmin-data-wrp">
            <?php
            if (!empty(majesticsupport::$_data[0])) {
                ?>
                <table id="majestic-support-table">
                    <tr class="majestic-support-table-heading">
                        <th class="left w70 majestic-support-table-title"><?php echo esc_html(__('Error', 'majestic-support')); ?></th>
                        <th class="majestic-support-table-created-date"><?php echo esc_html(__('Created', 'majestic-support')); ?></th>
                        <th class="majestic-support-table-actions"><?php echo esc_html(__('Action', 'majestic-support')); ?></th>
                    </tr>
                    <?php
                    foreach (majesticsupport::$_data[0] AS $MJTC_systemerror) {
                        $MJTC_isview = ($MJTC_systemerror->isview == 1) ? 'close.png' : 'good.png';
                        ?>
                        <tr>
                            <td class="left w70 majestic-support-table-title">
                                <span class="majestic-support-table-responsive-heading"><?php
                                    echo esc_html(__('Error', 'majestic-support'));
                                    echo esc_html(" : ");
                                    ?></span><span class="ms-error-icon-box">
                            <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"></path></svg>
                            </span><?php echo esc_html($MJTC_systemerror->error); ?></td>
                            <td class="majestic-support-table-created-date"><span class="majestic-support-table-responsive-heading"><?php
                            echo esc_html(__('Created', 'majestic-support'));
                            echo esc_html(" : ");
                                    ?></span><?php echo esc_html(date_i18n(majesticsupport::$_config['date_format'], MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_systemerror->created))); ?></td>
                            <td class="majestic-support-table-actions">
                                <span class="majestic-support-table-responsive-heading"><?php
                            echo esc_html(__('Actions', 'majestic-support'));
                            echo esc_html(" : ");
                                    ?></span>
                                <a title="<?php echo esc_attr(__('Delete','majestic-support')); ?>" class="action-btn majestic-support-table-delte-action" onclick="return confirm('<?php echo esc_html(__('Are you sure you want to delete?', 'majestic-support')); ?>');" href="<?php echo esc_url(wp_nonce_url('?page=majesticsupport_systemerror&task=deletesystemerror&action=mstask&systemerrorid='.esc_attr($MJTC_systemerror->id),'delete-systemerror-'.esc_attr($MJTC_systemerror->id)));?>">
                                    <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"></path></svg>
                                </a>
                            </td>
                        </tr>
                <?php }
                ?>
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
