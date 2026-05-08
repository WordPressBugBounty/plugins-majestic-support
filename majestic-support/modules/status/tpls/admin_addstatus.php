<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
wp_enqueue_script('iris');
$majesticsupport_js ="
    jQuery(document).ready(function () {
        jQuery.validate();
    });
";
wp_add_inline_script('majestic-support-cmain-js',$majesticsupport_js);
?>
<div id="msadmin-wrapper">
    <div id="msadmin-leftmenu">
        <?php  MJTC_includer::MJTC_getClassesInclude('msadminsidemenu'); ?>
    </div>
    <div id="msadmin-data">
        <?php MJTC_includer::MJTC_getModel('majesticsupport')->getPageTitle('addstatus'); ?>
        <div id="msadmin-data-wrp">
            <?php $MJTC_nonce_id = isset(majesticsupport::$_data[0]->id) ? majesticsupport::$_data[0]->id : ''; ?>
            <form class="msadmin-form" method="post" action="<?php echo esc_url(wp_nonce_url(admin_url("?page=majesticsupport_status&task=savestatus"),"save-status-".$MJTC_nonce_id)); ?>">
                <div class="mjtc-form-wrapper">
                    <div class="mjtc-form-title"><?php echo esc_html(__('Status', 'majestic-support')); ?>&nbsp;<span style="color: red;" >*</span></div>
                    <div class="mjtc-form-value"><?php echo wp_kses(MJTC_formfield::MJTC_text('status', isset(majesticsupport::$_data[0]->status) ? majesticsupport::$_data[0]->status : '', array('class' => 'inputbox mjtc-form-input-field', 'data-validation' => 'required')), MJTC_ALLOWED_TAGS) ?></div>
                    <?php if(!empty(majesticsupport::$_data[0]->custom_status)) { ?>
                        <div class="mjtc-form-desc">(<?php echo esc_html(majesticsupport::MJTC_getVarValue(majesticsupport::$_data[0]->custom_status)); ?>)</div>
                    <?php } ?>
                </div>
                <div class="mjtc-form-wrapper">
                    <div class="mjtc-form-title"><?php echo esc_html(__('Text Color', 'majestic-support')); ?>&nbsp;<span style="color: red;" >*</span></div>
                    <div class="mjtc-form-value">
                        <?php
                        $MJTC_style = '';
                        if (!empty(majesticsupport::$_data[0]->statuscolour)) {
                            $MJTC_style = "background:".majesticsupport::$_data[0]->statuscolour;
                        } ?>
                        <span style="<?php echo esc_attr($MJTC_style); ?>" class="mjtc-form-statuscolor-wrp"></span>
                        <?php echo wp_kses(MJTC_formfield::MJTC_text('statuscolor', isset(majesticsupport::$_data[0]->statuscolour) ? majesticsupport::$_data[0]->statuscolour : '', array('class' => 'inputbox mjtc-form-input-field mjtc-form-statuscolor-field', 'data-validation' => 'required', 'autocomplete' => 'off')), MJTC_ALLOWED_TAGS); ?>
                    </div>
                    <?php if(!empty(majesticsupport::$_data[0]->custom_status)) { ?>
                        <div class="mjtc-form-desc mjtc-form-status-desc"></div>
                    <?php } ?>
                </div>
                <div class="mjtc-form-wrapper">
                    <div class="mjtc-form-title"><?php echo esc_html(__('Background Color', 'majestic-support')); ?>&nbsp;<span style="color: red;" >*</span></div>
                    <div class="mjtc-form-value">
                        <?php
                        $MJTC_style = '';
                        if (!empty(majesticsupport::$_data[0]->statusbgcolour)) {
                            $MJTC_style = "background:".majesticsupport::$_data[0]->statusbgcolour;
                        } ?>
                        <span style="<?php echo esc_attr($MJTC_style); ?>" class="mjtc-form-statusbgcolor-wrp"></span>
                        <?php echo wp_kses(MJTC_formfield::MJTC_text('statusbgcolor', isset(majesticsupport::$_data[0]->statusbgcolour) ? majesticsupport::$_data[0]->statusbgcolour : '', array('class' => 'inputbox mjtc-form-input-field mjtc-form-statuscolor-field', 'data-validation' => 'required', 'autocomplete' => 'off')), MJTC_ALLOWED_TAGS); ?>
                    </div>
                </div>
                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('id', isset(majesticsupport::$_data[0]->id) ? majesticsupport::$_data[0]->id : '' ), MJTC_ALLOWED_TAGS); ?>
                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('ordering', isset(majesticsupport::$_data[0]->ordering) ? majesticsupport::$_data[0]->ordering : '' ), MJTC_ALLOWED_TAGS); ?>
                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('action', 'status_savestatus'), MJTC_ALLOWED_TAGS); ?>
                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('form_request', 'majesticsupport'), MJTC_ALLOWED_TAGS); ?>
                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('uid', MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid()), MJTC_ALLOWED_TAGS); ?>
                <div class="mjtc-form-button">
                    <?php echo wp_kses(MJTC_formfield::MJTC_submitbutton('save', esc_html(__('Save Status', 'majestic-support')), array('class' => 'button mjtc-form-save')), MJTC_ALLOWED_TAGS); ?>
                    <a href="admin.php?page=majesticsupport_status" class="mjtc-form-cancel"><?php echo esc_html(__('Cancel','majestic-support')); ?></a>
                </div>
            </form>
        </div>
        <?php
        $majesticsupport_js ="
            jQuery(document).ready(function () {
                jQuery('input#statuscolor').iris({
                    color: jQuery('input#statuscolor').val(),
                    onShow: function (colpkr) {
                        jQuery(colpkr).fadeIn(500);
                        return false;
                    },
                    onHide: function (colpkr) {
                        jQuery(colpkr).fadeOut(500);
                        return false;
                    },
                    change: function (c_event, ui) {
                        hex = ui.color.toString();
                        jQuery('.mjtc-form-statuscolor-wrp').css( 'background', hex);
                        jQuery('.mjtc-form-statuscolor-wrp').css( 'border', '1px solid #ebecec');
                        jQuery('input#statuscolor').css('backgroundColor', '#' + hex).val('#' + hex);
                    }
                });
                jQuery('input#statusbgcolor').iris({
                    color: jQuery('input#statusbgcolor').val(),
                    onShow: function (colpkr) {
                        jQuery(colpkr).fadeIn(500);
                        return false;
                    },
                    onHide: function (colpkr) {
                        jQuery(colpkr).fadeOut(500);
                        return false;
                    },
                    change: function (c_event, ui) {
                        hex = ui.color.toString();
                        jQuery('.mjtc-form-statusbgcolor-wrp').css( 'background', hex);
                        jQuery('.mjtc-form-statusbgcolor-wrp').css( 'border', '1px solid #ebecec');
                        jQuery('input#statusbgcolor').css('backgroundColor', '#' + hex).val('#' + hex);
                    }
                });
                jQuery(document).click(function (e) {
                    if (!jQuery(e.target).is('.colour-picker, .iris-picker, .iris-picker-inner')) {
                        jQuery('#statuscolor').iris('hide');
                        jQuery('#statusbgcolor').iris('hide');
                    }
                });
                jQuery('#statuscolor').click(function (event) {
                    jQuery('#statuscolor').iris('hide');
                    jQuery(this).iris('show');
                    return false;
                });
                jQuery('#statusbgcolor').click(function (event) {
                    jQuery('#statusbgcolor').iris('hide');
                    jQuery(this).iris('show');
                    return false;
                });
            });
        ";
        wp_add_inline_script('majestic-support-cmain-js',$majesticsupport_js);
        ?>
    </div>
</div>
