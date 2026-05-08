<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
$majesticsupport_js ="
    function resetFrom() {
        document.getElementById('email').value = '';
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
        <?php MJTC_includer::MJTC_getModel('majesticsupport')->getPageTitle('systememails'); ?>
        <div id="msadmin-data-wrp">
            <form class="mjtc-filter-form" name="majesticsupportform" id="majesticsupportform" method="post" action="<?php echo esc_url(wp_nonce_url(admin_url("admin.php?page=majesticsupport_email&mjslay=emails"),"emails")); ?>">
                <?php echo wp_kses(MJTC_formfield::MJTC_text('email', majesticsupport::$_data['filter']['email'], array('placeholder' => esc_html(__('Email', 'majestic-support')),'class' => 'mjtc-form-input-field')), MJTC_ALLOWED_TAGS); ?>
                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('MS_form_search', 'MS_SEARCH'), MJTC_ALLOWED_TAGS); ?>
                <?php echo wp_kses(MJTC_formfield::MJTC_submitbutton('go', esc_html(__('Search', 'majestic-support')), array('class' => 'button mjtc-form-search')), MJTC_ALLOWED_TAGS); ?>
                <?php echo wp_kses(MJTC_formfield::MJTC_button('reset', esc_html(__('Reset', 'majestic-support')), array('class' => 'button mjtc-form-reset', 'onclick' => 'resetFrom();')), MJTC_ALLOWED_TAGS); ?>
            </form>
            <span id="mjtc-systemail" class="mjtc-admin-infotitle"><img alt="<?php echo esc_attr(__('Info','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/infoicon.png" /><?php echo esc_html(__('System email used for sending email', 'majestic-support')); ?></span>
            <?php if (!empty(majesticsupport::$_data[0])) { ?>
            <table id="majestic-support-table">
                <tr class="majestic-support-table-heading">
                    <th class="left w60 majestic-support-table-email"><?php echo esc_html(__('Email Address', 'majestic-support')); ?></th>
                    <th class="majestic-support-table-status"><?php echo esc_html(__('Auto Response', 'majestic-support')); ?></th>
                    <th class="majestic-support-table-created-date"><?php echo esc_html(__('Created', 'majestic-support')); ?></th>
                    <th class="majestic-support-table-actions"><?php echo esc_html(__('Action', 'majestic-support')); ?></th>
                </tr>
                <?php
                foreach (majesticsupport::$_data[0] AS $MJTC_email) {
                    $MJTC_autoresponse = ($MJTC_email->autoresponse == 1) ? __('Yes', 'majestic-support') : __('No', 'majestic-support');
                    $MJTC_autoresponseclass = ($MJTC_email->autoresponse == 1) ? 'majestic-support-yes' : 'majestic-support-no';
                    ?>
                    <tr>
                        <td class="left w60 majestic-support-table-email"><span class="majestic-support-table-responsive-heading"><?php echo esc_html(__('Email Address', 'majestic-support'));
                echo esc_html(" : "); ?></span><a title="<?php echo esc_attr(__('Email','majestic-support')); ?>" href="?page=majesticsupport_email&mjslay=addemail&majesticsupportid=<?php echo esc_attr($MJTC_email->id); ?>"><?php echo esc_html($MJTC_email->email); ?></a></td>
                        <td class="majestic-support-table-status <?php echo esc_attr($MJTC_autoresponseclass); ?>">
                            <span class="majestic-support-table-autoresponse-status-wrp"><span class="majestic-support-table-responsive-heading"><?php echo esc_html(__('Auto Response', 'majestic-support'));
                echo esc_html(" : "); ?></span>
                <span class="majestic-support-table-status-dot"></span><?php echo esc_html($MJTC_autoresponse); ?></span>
                </td>
                        <td class="majestic-support-table-created-date">
                            <span class="majestic-support-table-responsive-heading"><?php echo esc_html(__('Created', 'majestic-support'));
                echo esc_html(" : "); ?></span>
                <?php echo esc_html(date_i18n(majesticsupport::$_config['date_format'], MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_email->created))); ?></td>
                        <td class="majestic-support-table-actions">
                            <a title="<?php echo esc_attr(__('Edit','majestic-support')); ?>" class="action-btn" href="?page=majesticsupport_email&mjslay=addemail&majesticsupportid=<?php echo esc_attr($MJTC_email->id); ?>">
                                <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"></path></svg>
                            </a>
                            <a title="<?php echo esc_attr(__('Delete','majestic-support')); ?>" class="action-btn" onclick="return confirm('<?php echo esc_html(__('Are you sure you want to delete?', 'majestic-support')); ?>');" href="<?php echo esc_url(wp_nonce_url('?page=majesticsupport_email&task=deleteemail&action=mstask&emailid=' .esc_attr($MJTC_email->id),'delete-email-'.esc_attr($MJTC_email->id))); ?>">
                                <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"></path></svg>
                            </a>
                        </td>
                    </tr>
                <?php }
            ?>
            </table>
            <?php
            if (majesticsupport::$_data[1]) {
                $MJTC_emailData = '<div class="tablenav"><div class="tablenav-pages">' . wp_kses_post(majesticsupport::$_data[1]) . '</div></div>';
                echo wp_kses($MJTC_emailData, MJTC_ALLOWED_TAGS);
            }
        } else {// User is guest
            MJTC_layout::MJTC_getNoRecordFound();
        }
        ?>
        </div>
    </div>
</div>
