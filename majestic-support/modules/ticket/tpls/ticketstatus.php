<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<div class="ms-main-up-wrapper">
    <?php
if (majesticsupport::$_config['offline'] == 2) {
    MJTC_message::MJTC_getMessage();
    ?>
    <?php include_once(MJTC_PLUGIN_PATH . 'includes/header.php');?>
    <div class="mjtc-support-top-sec-header">
        <img class="mjtc-transparent-header-img1" alt="<?php echo esc_attr(__('Image', 'majestic-support')); ?>"
            src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/tp-image.png" />
        <div class="mjtc-support-top-sec-left-header">
            <div class="mjtc-support-main-heading">
                <?php echo esc_html(__("Ticket Status",'majestic-support')); ?>
            </div>
            <div class="mjtc-support-sub-heading">
                <?php echo esc_html(__("Check the status of your support ticket quickly and easily.",'majestic-support')); ?>
            </div>
        </div>
    </div>
    <div class="mjtc-support-cont-main-wrapper">
        <div class="mjtc-support-cont-checkstatus-wrapper">
            <?php if (MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid() != 0 || majesticsupport::$_config['visitor_can_create_ticket'] == 1) { ?>
            <div class="mjtc-support-checkstatus-wrp">
                <form class="mjtc-support-form form-validate" action="<?php echo esc_url(wp_nonce_url(majesticsupport::makeUrl(array('mjsmod'=>'ticket','task'=>'showticketstatus')),"show-ticket-status")); ?>"
                    method="post" id="adminForm" enctype="multipart/form-data">
                    <div class="mjtc-tab-container">
                            <button id="mjtc-tab-email" class="mjtc-tab-btn active" type="button"><?php echo esc_html(__("By Email & ID",'majestic-support')); ?></button>
                            <button id="mjtc-tab-token" class="mjtc-tab-btn" type="button"><?php echo esc_html(__("By Token",'majestic-support')); ?></button>
                    </div>
                    <div id="method-email-container"class="mjstic-support-toogle-fadein">
                        <div class="mjtc-support-checkstatus-field-wrp">
                            <div class="mjtc-support-field-title">
                                <?php echo esc_html(__('Email','majestic-support')); ?>
                            </div>
                            <div class="mjtc-support-field-wrp">
                                <input class="inputbox mjtc-support-form-input-field  validate-email" data-validation="email" type="text" name="email" id="email" size="40" maxlength="255" value="<?php if (isset(majesticsupport::$_data['0']->email)) echo esc_attr(majesticsupport::$_data['0']->email); ?>" />
                            </div>
                        </div>
                        <div class="mjtc-support-checkstatus-field-wrp">
                            <div class="mjtc-support-field-title">
                                <?php echo esc_html(__('Ticket ID','majestic-support')); ?>
                            </div>
                            <div class="mjtc-support-field-wrp">
                                <input class="inputbox mjtc-support-form-input-field " type="text" name="ticketid"
                                    id="ticketid" size="40" maxlength="255" value=""  />
                            </div>
                        </div>
                    </div>
                    <div id="method-token-container" class="mjstic-support-toogle-hidden-section mjstic-support-toogle-fadein">
                        <div class="mjtc-support-checkstatus-field-wrp">
                            <div class="mjtc-support-field-title">
                                <?php echo esc_html(__('Token','majestic-support')); ?>
                            </div>
                            <div class="mjtc-support-field-wrp">
                                <input class="inputbox mjtc-support-form-input-field " type="text" name="tickettoken"
                                    id="token" size="40" maxlength="255" value=""  />
                            </div>
                        </div>
                    </div>
                    <div class="mjtc-support-form-btn-wrp">
                          <input class="tk_dft_btn mjtc-support-save-button" type="submit" name="submit_app" value="<?php echo esc_attr(__('Check Status', 'majestic-support')); ?>" />
                    </div>
                    <?php echo wp_kses(MJTC_formfield::MJTC_hidden('form_request', 'majesticsupport'), MJTC_ALLOWED_TAGS); ?>
                    <?php echo wp_kses(MJTC_formfield::MJTC_hidden('checkstatus', 1), MJTC_ALLOWED_TAGS); ?>
                    <?php echo wp_kses(MJTC_formfield::MJTC_hidden('mspageid',get_the_ID()), MJTC_ALLOWED_TAGS); ?>
                </form>
            </div>
            <?php
    }else {// User is guest
        $MJTC_redirect_url = majesticsupport::makeUrl(array('mjsmod'=>'ticket','mjslay'=>'ticketstatus'));
        $MJTC_redirect_url = MJTC_majesticsupportphplib::MJTC_safe_encoding($MJTC_redirect_url);
        MJTC_layout::MJTC_getUserGuest($MJTC_redirect_url);
    }
} else { // System is offline
    MJTC_layout::MJTC_getSystemOffline();
}
?>
        </div>
    </div>
</div>
<?php
$majesticsupport_js = "
jQuery(document).ready(function($) {
    jQuery('#mjtc-tab-email').on('click', function() {
        jQuery('.mjtc-tab-btn').removeClass('active');
        jQuery(this).addClass('active');
        jQuery('#method-email-container').show().addClass('mjstic-support-toogle-fadein');
        jQuery('#method-token-container').hide().removeClass('mjstic-support-toogle-fadein');
        jQuery('#method-email-container input').prop('required', true);
        jQuery('#method-token-container input').prop('required', false);
    });
    jQuery('#mjtc-tab-token').on('click', function() {
        jQuery('.mjtc-tab-btn').removeClass('active');
        jQuery(this).addClass('active');
        jQuery('#method-token-container').show().addClass('mjstic-support-toogle-fadein');
        jQuery('#method-email-container').hide().removeClass('mjstic-support-toogle-fadein');
        jQuery('#method-email-container input').prop('required', false);
        jQuery('#method-token-container input').prop('required', true);
    });
});";
wp_add_inline_script('majestic-support-cmain-js',$majesticsupport_js);
    ?>