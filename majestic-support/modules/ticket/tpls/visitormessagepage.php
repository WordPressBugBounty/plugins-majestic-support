<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<div class="ms-main-up-wrapper">
<?php
if (majesticsupport::$_config['offline'] == 2) {
        MJTC_message::MJTC_getMessage();
        include_once(MJTC_PLUGIN_PATH . 'includes/header.php'); ?>
        <div class="mjtc-support-top-sec-header">
            <img class="mjtc-transparent-header-img1" alt="<?php echo esc_attr(__('Image', 'majestic-support')); ?>"
                src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/tp-image.png" />
            <div class="mjtc-support-top-sec-left-header">
                <div class="mjtc-support-main-heading">
                    <?php echo esc_html(__("Ticket Submitted",'majestic-support')); ?>
                </div>
                <div class="mjtc-support-sub-heading">
                    <?php echo esc_html(__("Your support ticket has been received and logged.",'majestic-support')); ?>
                </div>
            </div>
        </div>
        <div class="mjtc-support-visitor-message-mainwrp">
            <div class="ms-visitor-message-wrapper" >
                <div class="mjtc-support-checkmark-circle">
                    <svg class="mjtc-support-checkmark-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                    </svg>
                </div>
                <div class="mjtc-support-visitor-contentheading">
                    <?php echo esc_html(__("Ticket Submission Confirmed!",'majestic-support')); ?>
                </div>
                <span class="ms-visitor-message" >
                    <?php echo wp_kses(majesticsupport::$_config['visitor_message'], MJTC_ALLOWED_TAGS)?>
                </span>
            </div>
            <div class="ms-visitor-token-message">
                <p class="ms-visitor-token-message-heading"><?php echo esc_html(__('Remember Your Ticket Token for Tracking (Save It!)', 'majestic-support')); ?></p>
                <p class="ms-visitor-token-message-discription"><?php echo esc_html(__("You've received a token number to track your support ticket status. This is one-time code, so please save it carefully. ", "majestic-support")); ?></p>
                <p class="ms-visitor-token-message-token-number">
                    <?php $MJTC_token = MJTC_request::MJTC_getVar('majesticsupportid');?>
                    <?php echo esc_html($MJTC_token);?>
                    <div class="mjstc-supprt-token-copy-overlay">
                        <span class="mjstc-supprt-token-hover-text">
                            <!-- SVG Copy Icon (Inline replacement for fa-copy) -->
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5" />
                            </svg>
                            <?php echo esc_html(__('Click button below to copy', 'majestic-support')); ?>
                        </span>
                    </div>
                </p>
            </div>
            <a title="<?php echo esc_attr(__('Copy Token','majestic-support')); ?>" class="mjtc-sprt-det-copy-id" id="ticketidcopybtn" success="<?php echo esc_attr(__('Copied','majestic-support')); ?>">
                <?php echo esc_html(__('Copy Token','majestic-support')); ?>
            </a>
        </div>
<?php
echo wp_kses(MJTC_formfield::MJTC_hidden('ticketrandomid', $MJTC_token), MJTC_ALLOWED_TAGS);
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
