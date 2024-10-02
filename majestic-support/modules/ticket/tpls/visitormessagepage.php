<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<div class="ms-main-up-wrapper">
<?php
if (majesticsupport::$_config['offline'] == 2) {
        MJTC_message::MJTC_getMessage();
        include_once(MJTC_PLUGIN_PATH . 'includes/header.php'); ?>
        <div class="ms-visitor-message-wrapper" >
            <img alt="<?php echo esc_html(__('image', 'majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL) . 'includes/images/ms-support-icon.png'; ?>" />
            <span class="ms-visitor-message" >
                <?php echo wp_kses(majesticsupport::$_config['visitor_message'], MJTC_ALLOWED_TAGS)?>
            </span>
        </div>
        <div class="ms-visitor-token-message">
            <p class="ms-visitor-token-message-heading"><?php echo esc_html(__('Remember Your Ticket Token for Tracking (Save It!)', 'majestic-support')); ?></p>
            <p class="ms-visitor-token-message-discription"><?php echo esc_html(__("You've received a token number to track your support ticket status. This is one-time code, so please save it carefully. ", "majestic-support")); ?></p>
            <p class="ms-visitor-token-message-token-number">
                <?php $token = MJTC_request::MJTC_getVar('majesticsupportid');?>
                <?php echo esc_html($token);?>
                <a title="<?php echo esc_attr(__('Copy Token','majestic-support')); ?>" class="mjtc-sprt-det-copy-id" id="ticketidcopybtn" success="<?php echo esc_html(__('Copied','majestic-support')); ?>">
                    <?php echo esc_html(__('Copy Token','majestic-support')); ?>
                </a>
            </p>

        </div>
<?php
 echo wp_kses(MJTC_formfield::MJTC_hidden('ticketrandomid', $token), MJTC_ALLOWED_TAGS);
} else { // System is offline
    MJTC_layout::MJTC_getSystemOffline();
}
$majesticsupport_js ="
jQuery(document).delegate('#ticketidcopybtn', 'click', function() {
    var temp = jQuery('<input>');
    jQuery('body').append(temp);
    temp.val(jQuery('#ticketrandomid').val()).select();
    document.execCommand('copy');
    temp.remove();
    jQuery('#ticketidcopybtn').text(jQuery('#ticketidcopybtn').attr('success'));
});
";
wp_add_inline_script('majestic-support-cmain-js',$majesticsupport_js);
?>
</div>
