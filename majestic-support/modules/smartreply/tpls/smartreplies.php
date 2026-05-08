<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<div class="ms-main-up-wrapper">
<?php
if (majesticsupport::$_config['offline'] == 2) {
    if (majesticsupport::$_data['permission_granted'] == 1) {
    if (MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid() != 0) {
        if ( in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) {
            if (majesticsupport::$_data['staff_enabled']) { ?>
                <?php
                $majesticsupport_js ="
                    function resetFrom() {
                        document.getElementById('ms-title').value = '';
                        return true;
                    }

                ";
                wp_add_inline_script('majestic-support-cmain-js',$majesticsupport_js);
                ?>  
                <?php MJTC_message::MJTC_getMessage(); ?>
                <?php include_once(MJTC_PLUGIN_PATH . 'includes/header.php'); ?>
                <div class="mjtc-support-top-sec-header">
                    <img class="mjtc-transparent-header-img1" alt="<?php echo esc_attr(__('Image', 'majestic-support')); ?>"
                        src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/tp-image.png" />
                    <div class="mjtc-support-top-sec-left-header">
                        <div class="mjtc-support-main-heading">
                            <?php echo esc_html(__("Smart Replies",'majestic-support')); ?>
                        </div>
                        <div class="mjtc-support-sub-heading"><?php echo esc_html(__("Define and manage predefined responses used for quick and consistent ticket replies.",'majestic-support')); ?></div>
                    </div>
                    <div class="mjtc-support-top-sec-right-header">
                        <a <?php echo esc_attr($MJTC_id); ?> href="<?php echo esc_url(majesticsupport::makeUrl(array('mjsmod'=>'smartreply', 'mjslay'=>'addsmartreply'))); ?>"
                            class="mjtc-support-button-header"><?php echo esc_html(__("Add Smart Reply",'majestic-support')); ?>
                        </a>
                    </div>
                </div>
                <div class="mjtc-support-cont-main-wrapper mjtc-support-cont-main-wrapper-with-btn">
                    <div class="mjtc-support-cont-wrapper mjtc-support-cont-wrapper-color">
                        <div class="mjtc-support-smartreplies-wrapper">
                            <div class="mjtc-support-top-search-wrp">
                                <div class="mjtc-support-search-fields-wrp">
                                    <form class="mjtc-filter-form" name="majesticsupportform" id="majesticsupportform" method="POST" action="<?php echo esc_url(wp_nonce_url(majesticsupport::makeUrl(array('mjsmod'=>'smartreply', 'mjslay'=>'smartreplies')),"smart-replies")); ?>">
                                        <div class="mjtc-support-fields-wrp">
                                            <div class="mjtc-support-form-field mjtc-support-form-field-download-search">
                                                <?php echo wp_kses(MJTC_formfield::MJTC_text('ms-title', majesticsupport::parseSpaces(majesticsupport::$_data['filter']['ms-title']), array('placeholder' => esc_html(__('Search', 'majestic-support')), 'class' => 'mjtc-support-field-input')), MJTC_ALLOWED_TAGS); ?>
                                            </div>
                                            <div class="mjtc-support-search-form-btn-wrp mjtc-support-search-form-btn-wrp-download ">
                                                <?php echo wp_kses(MJTC_formfield::MJTC_submitbutton('ms-go', esc_html(__('Search', 'majestic-support')), array('class' => 'mjtc-search-button')), MJTC_ALLOWED_TAGS); ?>
                                                <?php echo wp_kses(MJTC_formfield::MJTC_submitbutton('ms-reset', esc_html(__('Reset', 'majestic-support')), array('class' => 'mjtc-reset-button', 'onclick' => 'return resetFrom();')), MJTC_ALLOWED_TAGS); ?>
                                            </div>
                                        </div>
                                        <?php echo wp_kses(MJTC_formfield::MJTC_hidden('MS_form_search', 'MS_SEARCH'), MJTC_ALLOWED_TAGS); ?>
                                        <?php echo wp_kses(MJTC_formfield::MJTC_hidden('mspageid', get_the_ID()), MJTC_ALLOWED_TAGS); ?>
                                        <?php echo wp_kses(MJTC_formfield::MJTC_hidden('mjtcslay', 'smartreplies'), MJTC_ALLOWED_TAGS); ?>
                                    </form>
                                </div>
                            </div>
                            <?php
                            if (!empty(majesticsupport::$_data[0])) { ?>
                                <div class="mjtc-support-download-content-wrp">
                                    <div class="mjtc-support-table-wrp">
                                        <?php
                                        foreach (majesticsupport::$_data[0] AS $MJTC_smartreply) { ?>
                                            <div class="ms-smart-reply-listing-wrp">
                                                <div class="ms-smart-reply-listing-head">
                                                    <div class="ms-smart-reply-listing-head-left">
                                                        <a title="<?php echo esc_attr(__('Title','majestic-support')); ?>" href="<?php echo esc_url(majesticsupport::makeUrl(array('mjsmod'=>'smartreply', 'mjslay'=>'addsmartreply', 'majesticsupportid'=>$MJTC_smartreply->id))); ?>">
                                                            <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_smartreply->title)); ?>
                                                        </a>
                                                    </div>
                                                    <div class="ms-smart-reply-listing-head-right">
                                                        <a title="<?php echo esc_attr(__('Edit','majestic-support')); ?>" class="mjtc-support-table-action-btn" href="<?php echo esc_url(majesticsupport::makeUrl(array('mjsmod'=>'smartreply', 'mjslay'=>'addsmartreply', 'majesticsupportid'=>$MJTC_smartreply->id))); ?>">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                                        </a>
                                                        <a title="<?php echo esc_attr(__('Delete','majestic-support')); ?>" class="mjtc-support-table-action-btn" onclick="return confirm('<?php echo esc_html(__('Are you sure you want to delete?', 'majestic-support')); ?>');" href="<?php echo esc_url(wp_nonce_url(majesticsupport::makeUrl(array('mjsmod'=>'smartreply', 'task'=>'deletesmartreply', 'action'=>'mstask', 'smartreplyid'=>$MJTC_smartreply->id, 'mspageid'=>get_the_ID())),'delete-smartreply-'.$MJTC_smartreply->id)); ?>">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"></path><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path></svg>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="ms-smart-reply-listing-body">
                                                    <?php 
                                                    $MJTC_ticketsubject = MJTC_majesticsupportphplib::MJTC_preg_replace("/\\\\'/", "'", $MJTC_smartreply->ticketsubjects);

                                                    $MJTC_ticketsubjects = json_decode($MJTC_ticketsubject);
                                                    foreach ($MJTC_ticketsubjects as $MJTC_ticketsubject) { ?>
                                                        <div class="ms-smart-reply-listing-ticket-subject">
                                                            <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_ticketsubject)); ?>
                                                        </div>
                                                    <?php } ?>
                                                    <div class="ms-smart-reply-listing-ticket-reply">
                                                        <svg class="ms-reply-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M20 2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h14l4 4V4c0-1.1-.9-2-2-2zm-2 12H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z"></path></svg>
                                                        <span><?php echo esc_html(majesticsupport::MJTC_getVarValue(MJTC_majesticsupportphplib::MJTC_strip_tags($MJTC_smartreply->reply))); ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php
                                        } ?>
                                    </div>
                                </div>
                                </div>
                                <?php
                                if (majesticsupport::$_data[1]) {
                                    $MJTC_data = '<div class="tablenav"><div class="tablenav-pages" >' . wp_kses_post(majesticsupport::$_data[1]) . '</div></div>';
                                    echo wp_kses($MJTC_data, MJTC_ALLOWED_TAGS);
                                }
                            } else { // Record Not FOund
                                MJTC_layout::MJTC_getNoRecordFound();
                            }
                } else {
                    MJTC_layout::MJTC_getStaffMemberDisable();
                }
        } else { // user not Staff
            MJTC_layout::MJTC_getNotStaffMember();
        }
    } else {// User is guest
        $MJTC_redirect_url = majesticsupport::makeUrl(array('mjsmod'=>'role','mjslay'=>'roles'));
        $MJTC_redirect_url = MJTC_majesticsupportphplib::MJTC_safe_encoding($MJTC_redirect_url);
        MJTC_layout::MJTC_getUserGuest($MJTC_redirect_url);
    }
    } else { // User permission not granted
        MJTC_layout::MJTC_getPermissionNotGranted();
    }
} else { // System is offline
    MJTC_layout::MJTC_getSystemOffline();
}
?>

        </div>
    </div>
</div>