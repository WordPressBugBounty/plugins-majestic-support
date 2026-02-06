<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
wp_enqueue_script('jquery-ui-datepicker');
wp_enqueue_style('jquery-ui-css', MJTC_PLUGIN_URL . 'includes/css/jquery-ui-smoothness.css');
$majesticsupport_js ="
    var nextorid = 1;
    var nextandid = 1;
    ajaxurl = '". esc_url(admin_url('admin-ajax.php'))."';
    jQuery(document).ready(function ($) {
        $.validate();
    });
    function getChildForVisibleCombobox(val, no) {
        jQuery.post(ajaxurl, {action: 'mjsupport_ajax', val: val, mjsmod: 'fieldordering', task: 'getChildForVisibleCombobox', '_wpnonce':'". esc_attr(wp_create_nonce("get-child-for-visible-combobox"))."', isAjaxCall: '1'}, function (data) {
            if (data != false) {
                jQuery('#mjtc_or_row_'+no+' .visibleValueWrp').show();
                jQuery('#mjtc_or_row_'+no+' .visibleValueWrp').html(MJTC_msDecodeHTML(data));
                disableDefaultValueField();
            }else{
                jQuery('#mjtc_or_row_'+no+' .visibleValueWrp').hide();
                disableDefaultValueFieldDueToValues();
            }
        });//jquery closed
    }
    function disableDefaultValueFieldDueToValues() {
        var type = jQuery('select#userfieldtype').val()
        if(type == 'depandant_field') {
            disableDefaultValueField();
            return;
        }
        var foundSelected = false;
        // jQuery('select.mjtc-form-input-field-visible').each(function() {

        jQuery('select[name=\'visibleParent[]\']').each(function() {
            if (jQuery(this).val() !== '') {
                foundSelected = true;
                return false; // stops the loop early
            }
        });

        if (foundSelected) {
            return false;
        } else {
            // var values_count = jQuery('#values').length;
            // var values_count = jQuery('.user-field').length;
            // jQuery('input[name=\'values[]\']').each(function() {
            var values_count = 0;
            jQuery('input.user-field').each(function() {
                if (jQuery(this).val() !== '') {
                    values_count++;
                    return false; // stops the loop early
                }
            });
            if(values_count > 0) {
                makeDefaultValueVisible();
                jQuery('#defaultvalue_not_available').hide();
                jQuery('#subtitle_defaultvalue').hide();
                // jQuery('.defaultvalue_input').hide();
                // jQuery('.defaultvalue_select').show();
                // jQuery('input#defaultvalue_input').val('');
            } else {
                var type = jQuery('select#userfieldtype').val()
                if(type == 'combo' || type == 'radio' || type == 'multiple' || type == 'checkbox') {
                    makeDefaultValueVisible();
                    jQuery('#defaultvalue_not_available').hide();
                    // jQuery('.defaultvalue_input').hide();
                    // jQuery('.defaultvalue_select').show();
                    jQuery('#subtitle_defaultvalue').show();
                    // jQuery('input#defaultvalue_input').val('');
                    // jQuery('select#defaultvalue_select').val('');
                } else if(type == 'file') {
                    disableDefaultValueField();
                } else {
                    enableDefaultValueField();
                }
            }
        }
    }
    function enableDefaultValueFieldDueToValues() {
        var foundSelected = false;
        // jQuery('select.mjtc-form-input-field-visible').each(function() {

        jQuery('select[name=\'visibleParent[]\']').each(function() {
            if (jQuery(this).val() !== '') {
                foundSelected = true;
                return false; // stops the loop early
            }
        });

        if (foundSelected) {
            disableDefaultValueField();
        } else {
            // var values_count = jQuery('#values').length;
            // var values_count  = jQuery('.user-field').length;

            var values_count = 0;
            jQuery('input.user-field').each(function() {
                if (jQuery(this).val() !== '') {
                    values_count++;
                    return false; // stops the loop early
                }
            });


            if(values_count > 0) {
                makeDefaultValueVisible();
                jQuery('#defaultvalue_not_available').hide();
                // jQuery('.defaultvalue_input').hide();
                // jQuery('.defaultvalue_select').show();
                jQuery('#subtitle_defaultvalue').hide();
            } else {
                var type = jQuery('select#userfieldtype').val();
                if(type == 'combo' || type == 'radio' || type == 'multiple' || type == 'checkbox') {
                    makeDefaultValueVisible();
                    jQuery('#defaultvalue_not_available').hide();
                    // jQuery('.defaultvalue_input').hide();
                    // jQuery('.defaultvalue_select').show();
                    jQuery('#subtitle_defaultvalue').show();
                    // jQuery('input#defaultvalue_input').val('');
                    // jQuery('select#defaultvalue_select').val('');
                } else if(type == 'file') {
                    disableDefaultValueField();
                } else {
                    enableDefaultValueField();
                }
            }
        }
    }
    function disableDefaultValueField() {
        jQuery('#defaultvalue_input').removeClass('custom_date mjtc-form-date-field hasDatepicker');
        makeDefaultValueVisible();
        jQuery('#subtitle_defaultvalue').hide();
        // jQuery('select#defaultvalue_select').val('');
        // jQuery('input#defaultvalue_input').val('')
        // jQuery('.defaultvalue_select').hide('');
        // jQuery('.defaultvalue_input').show();
        jQuery('input#defaultvalue_input').prop('readonly', true);
        jQuery('select#defaultvalue_select').prop('disabled', true);
        jQuery('#defaultvalue_not_available').show();
    }
    function enableDefaultValueField() {
        makeDefaultValueVisible();
        jQuery('#subtitle_defaultvalue').hide();
        // jQuery('select#defaultvalue_select').val('');
        // jQuery('input#defaultvalue_input').val('');
        jQuery('#defaultvalue_not_available').hide();
    }
    function makeDefaultValueVisible(){
        jQuery('#defaultvalue_not_available').hide();
        jQuery('#subtitle_defaultvalue').hide();
        var type = jQuery('select#userfieldtype').val();
        if(type == 'combo' || type == 'radio' || type == 'multiple' || type == 'checkbox') {
            jQuery('.defaultvalue_select').show();
            jQuery('.defaultvalue_input').hide();
            jQuery('select#defaultvalue_select').prop('disabled', false);
        } else {
            jQuery('.defaultvalue_select').hide();
            jQuery('.defaultvalue_input').show();
            jQuery('input#defaultvalue_input').prop('readonly', false);
        }
    }
    function disablePlaceholderField() {
        jQuery('#placeholder_not_available').show();
        jQuery('input#placeholder').val('');
        jQuery('input#placeholder').prop('readonly', true);
    }
    function enablePlaceholderField() {
        jQuery('#placeholder_not_available').hide();
        // jQuery('input#placeholder').val('');
        jQuery('input#placeholder').prop('readonly', false);
    }
    function disableAdminSearchField() {
        jQuery('#subtitle_adminSearch').show();
        jQuery('select#search_admin').prop('disabled', true);
        jQuery('select#search_admin').val('0');
    }
    function disableUserSearchField() {
        jQuery('#subtitle_userSearch').show();
        jQuery('select#search_user').prop('disabled', true);
        jQuery('select#search_user').val('0');
    }
    function disableReadOnlyField() {
        jQuery('#subtitle_readOnly').show();
        jQuery('select#readonly').prop('disabled', true);
        jQuery('select#readonly').val('0');
    }
    function getConditionsForVisibleCombobox(val, no) {
        jQuery.post(ajaxurl, {action: 'mjsupport_ajax', val: val, mjsmod: 'fieldordering', task: 'getConditionsForVisibleCombobox', '_wpnonce':'". esc_attr(wp_create_nonce("get-conditions-for-visible-combobox"))."', isAjaxCall: '1'}, function (data) {
            if (data != false) {
                jQuery('#mjtc_or_row_'+no+' .visibleConditionWrp').show();
                jQuery('#mjtc_or_row_'+no+' .visibleConditionWrp').html(MJTC_msDecodeHTML(data));
            }else{
                jQuery('#mjtc_or_row_'+no+' .visibleConditionWrp').hide();
            }
        });//jquery closed
    }
    // visible 
    function getMoreORRow(el, fieldfor, formid, field, id){
        jQuery.post(ajaxurl, {action: 'mjsupport_ajax',mjsmod: 'fieldordering', task: 'getHtmlForORRow' , nextorid : nextorid, fieldfor : fieldfor, formid : formid, field : field, id : id, '_wpnonce':'". esc_attr(wp_create_nonce('get-html-for-or-row')) ."'}, function (data) {
            if(data){
                data = JSON.parse(data);
                var parent = jQuery(el).closest('div.mjtc-form-wrapper.mjtc-form-visible-wrapper');
                jQuery(parent).find('.mjtc-form-visible-or-row').append(MJTC_msDecodeHTML(data));
                nextorid++;
            }
        });
    }
    function getMoreANDRow(fieldfor, formid, field, id){
        jQuery.post(ajaxurl, {action: 'mjsupport_ajax',mjsmod: 'fieldordering', task: 'getHtmlForANDRow' , nextandid : nextandid, nextorid : nextorid, fieldfor : fieldfor, formid : formid, field : field, id : id, '_wpnonce':'". esc_attr(wp_create_nonce('get-html-for-and-row')) ."'}, function (data) {
            if(data){
                data = JSON.parse(data);
                jQuery('.mjtc-form-visible-add-row').append(MJTC_msDecodeHTML(data));
                nextandid++;
                nextorid++;
            }
        });
    }
    function deleteOrRow(id){
        var parent = jQuery('#'+id).closest('div.mjtc-form-visible-andwrp ');
        var count = parent.find('div.mjtc-form-value').length;
        jQuery('#'+id).remove();
        if(count == 1) {
            jQuery(parent).remove();
        }
        disableDefaultValueFieldDueToValues();
    }
";
wp_add_inline_script('majestic-support-cmain-js',$majesticsupport_js);
?>
<div id="msadmin-wrapper">
    <div id="msadmin-leftmenu">
        <?php MJTC_includer::MJTC_getClassesInclude('msadminsidemenu'); ?>
    </div>
    <div id="msadmin-data">
        <?php MJTC_includer::MJTC_getModel('majesticsupport')->getPageTitle('adduserfield'); ?>
        <?php
        $MJTC_yesno = array(
            (object) array('id' => 1, 'text' => esc_html(__('Yes', 'majestic-support'))),
            (object) array('id' => 0, 'text' => esc_html(__('No', 'majestic-support'))));
        $MJTC_equalnotequal = array(
            (object) array('id' => 1, 'text' => esc_html(__('Equal', 'majestic-support'))),
            (object) array('id' => 0, 'text' => esc_html(__('Not Equal', 'majestic-support'))));
        $MJTC_visibleLogics = array(
            (object) array('id' => 'AND', 'text' => esc_html(__('AND', 'majestic-support'))),
            (object) array('id' => 'OR', 'text' => esc_html(__('OR', 'majestic-support'))));
        if(isset(majesticsupport::$_data[0]['userfield']->userfieldtype) && majesticsupport::$_data[0]['userfield']->userfieldtype != 'depandant_field'){
            $MJTC_fieldtypes = array(
                (object) array('id' => 'text', 'text' => esc_html(__('Text Field', 'majestic-support'))),
                (object) array('id' => 'checkbox', 'text' => esc_html(__('Check Box', 'majestic-support'))),
                (object) array('id' => 'date', 'text' => esc_html(__('Date', 'majestic-support'))),
                (object) array('id' => 'combo', 'text' => esc_html(__('Drop Down', 'majestic-support'))),
                (object) array('id' => 'email', 'text' => esc_html(__('Email Address', 'majestic-support'))),
                (object) array('id' => 'textarea', 'text' => esc_html(__('Text Area', 'majestic-support'))),
                (object) array('id' => 'radio', 'text' => esc_html(__('Radio Button', 'majestic-support'))),
                (object) array('id' => 'file', 'text' => esc_html(__('Upload File', 'majestic-support'))),
                (object) array('id' => 'multiple', 'text' => esc_html(__('Multi Select', 'majestic-support'))),
                (object) array('id' => 'termsandconditions', 'text' => esc_html(__('Terms and Conditions', 'majestic-support'))));
        }else{
            $MJTC_fieldtypes = array(
                (object) array('id' => 'text', 'text' => esc_html(__('Text Field', 'majestic-support'))),
                (object) array('id' => 'checkbox', 'text' => esc_html(__('Check Box', 'majestic-support'))),
                (object) array('id' => 'date', 'text' => esc_html(__('Date', 'majestic-support'))),
                (object) array('id' => 'combo', 'text' => esc_html(__('Drop Down', 'majestic-support'))),
                (object) array('id' => 'email', 'text' => esc_html(__('Email Address', 'majestic-support'))),
                (object) array('id' => 'textarea', 'text' => esc_html(__('Text Area', 'majestic-support'))),
                (object) array('id' => 'radio', 'text' => esc_html(__('Radio Button', 'majestic-support'))),
                (object) array('id' => 'depandant_field', 'text' => esc_html(__('Dependent Field', 'majestic-support'))),
                (object) array('id' => 'file', 'text' => esc_html(__('Upload File', 'majestic-support'))),
                (object) array('id' => 'multiple', 'text' => esc_html(__('Multi Select', 'majestic-support'))),
                (object) array('id' => 'termsandconditions', 'text' => esc_html(__('Terms and Conditions', 'majestic-support'))));
        }
        $MJTC_fieldsize = array(
             (object) array('id' => 50, 'text' => esc_html(__('50%', 'majestic-support'))),
            (object) array('id' => 100, 'text' => esc_html(__('100%', 'majestic-support'))));
        ?>
        <div id="msadmin-data-wrp">
            <?php if(isset(majesticsupport::$_data['formid'])){ $MJTC_mformid = majesticsupport::$_data['formid']; }else{ $MJTC_mformid = MJTC_includer::MJTC_getModel('ticket')->getDefaultMultiFormId(); } ?>
            <?php $MJTC_nonce_id = isset(majesticsupport::$_data[0]['userfield']->id) ? majesticsupport::$_data[0]['userfield']->id : '';?>
            <form class="msadmin-form" id="adminForm" method="post" action="<?php echo esc_url(wp_nonce_url(admin_url("admin.php?page=majesticsupport_fieldordering&task=saveuserfeild&formid=$MJTC_mformid"),"save-userfeild-".$MJTC_nonce_id)); ?>">
                <?php if (empty(majesticsupport::$_data[0]['userfield']->id) || (!empty(majesticsupport::$_data[0]['userfield']->id) && !empty(majesticsupport::$_data[0]['userfield']->isuserfield))) { ?>
                    <div class="mjtc-form-wrapper">
                        <div class="mjtc-form-title"><?php echo esc_html(__('Field Type', 'majestic-support')); ?><font class="required-notifier">*</font></div>
                        <div class="mjtc-form-value"><?php echo wp_kses(MJTC_formfield::MJTC_select('userfieldtype', $MJTC_fieldtypes, isset(majesticsupport::$_data[0]['userfield']->userfieldtype) ? majesticsupport::$_data[0]['userfield']->userfieldtype : 'text', '', array('class' => 'inputbox one mjtc-form-select-field', 'data-validation' => 'required', 'onchange' => 'toggleType(this.options[this.selectedIndex].value);')), MJTC_ALLOWED_TAGS); ?></div>
                    </div>
                    <?php
                } ?>
                <div class="mjtc-form-wrapper">
                    <div class="mjtc-form-title"><?php echo esc_html(__('Field Title', 'majestic-support')); ?><font class="required-notifier">*</font></div>
                    <div class="mjtc-form-value"><?php echo wp_kses(MJTC_formfield::MJTC_text('fieldtitle', isset(majesticsupport::$_data[0]['userfield']->fieldtitle) ? majesticsupport::$_data[0]['userfield']->fieldtitle : '', array('class' => 'inputbox one mjtc-form-input-field', 'data-validation' => 'required')), MJTC_ALLOWED_TAGS); ?></div>
                </div>
                <div class="mjtc-form-wrapper for-terms-condtions-hide" id="for-combo-wrapper" style="display:none;">
                    <div class="mjtc-form-title"><?php echo esc_html(__('Select','majestic-support')) .esc_html('&nbsp;'). esc_html(__('Parent Field', 'majestic-support')); ?><font class="required-notifier">*</font></div>
                    <div class="mjtc-form-value" id="for-combo"></div>
                </div>
                <?php 
                if (empty(majesticsupport::$_data[0]['userfield']->id) || (!empty(majesticsupport::$_data[0]['userfield']->field) && !in_array(majesticsupport::$_data[0]['userfield']->field, ['termsandconditions1','termsandconditions2','termsandconditions3']))) { ?>
                    <div class="mjtc-form-wrapper for-terms-condtions-hide">
                        <div class="mjtc-form-title">
                            <?php echo esc_html(__('Default Value', 'majestic-support')); ?>
                            <span class="mjtc-form-subtitle" id="subtitle_defaultvalue" style="display:none;">
                                <?php echo esc_html(__('To choose a default value, first add values in the area below', 'majestic-support')); ?>
                            </span>
                            <span class="mjtc-form-subtitle" id="defaultvalue_not_available" style="display:none;">
                                <?php echo esc_html(__('This option is not available in this case', 'majestic-support')); ?>
                            </span>
                        </div>
                        <div class="mjtc-form-value">
                            <span class="defaultvalue_select" style="display:none;">
                                <?php echo wp_kses(MJTC_formfield::MJTC_select('defaultvalue_select', '', isset(majesticsupport::$_data[0]['userfield']->defaultvalue) ? majesticsupport::$_data[0]['userfield']->defaultvalue : '', esc_html(__('Select Default Value', 'majestic-support')), array('class' => 'inputbox one mjtc-form-select-field')), MJTC_ALLOWED_TAGS); ?>
                            </span>
                            <span class="defaultvalue_input">
                                <?php echo wp_kses(MJTC_formfield::MJTC_text('defaultvalue_input', isset(majesticsupport::$_data[0]['userfield']->defaultvalue) ? majesticsupport::$_data[0]['userfield']->defaultvalue : '', array('class' => 'inputbox one mjtc-form-input-field')), MJTC_ALLOWED_TAGS); ?>
                            </span>
                        </div>
                    </div>
                    <?php 
                }
                if (empty(majesticsupport::$_data[0]['userfield']->cannotunpublish)) { ?>
                    <div class="mjtc-form-wrapper for-terms-condtions-hide">
                        <div class="mjtc-form-title"><?php echo esc_html(__('User Published', 'majestic-support')); ?></div>
                        <div class="mjtc-form-value"><?php echo wp_kses(MJTC_formfield::MJTC_select('published', $MJTC_yesno, isset(majesticsupport::$_data[0]['userfield']->published) ? majesticsupport::$_data[0]['userfield']->published : 1, '', array('class' => 'inputbox one mjtc-form-select-field')), MJTC_ALLOWED_TAGS); ?></div>
                    </div>
                    <div class="mjtc-form-wrapper for-terms-condtions-hide for-admin-only-hide">
                        <div class="mjtc-form-title"><?php echo esc_html(__('Visitor Published', 'majestic-support')); ?></div>
                        <div class="mjtc-form-value"><?php echo wp_kses(MJTC_formfield::MJTC_select('isvisitorpublished', $MJTC_yesno, isset(majesticsupport::$_data[0]['userfield']->isvisitorpublished) ? majesticsupport::$_data[0]['userfield']->isvisitorpublished : 1, '', array('class' => 'inputbox one mjtc-form-select-field')), MJTC_ALLOWED_TAGS); ?></div>
                    </div>
                    <?php
                }
                if (empty(majesticsupport::$_data[0]['userfield']->cannotsearch)) { ?>
                    <div class="mjtc-form-wrapper for-terms-condtions-hide">
                        <div class="mjtc-form-title">
                            <?php echo esc_html(__('Admin Search', 'majestic-support')); ?>
                            <span class="mjtc-form-subtitle" id="subtitle_adminSearch" style="display:none;">
                                <?php echo esc_html(__('This option is not available in this case', 'majestic-support')); ?>
                            </span>
                        </div>
                        <div class="mjtc-form-value"><?php echo wp_kses(MJTC_formfield::MJTC_select('search_admin', $MJTC_yesno, isset(majesticsupport::$_data[0]['userfield']->search_admin) ? majesticsupport::$_data[0]['userfield']->search_admin : 1, '', array('class' => 'inputbox one mjtc-form-select-field')), MJTC_ALLOWED_TAGS); ?></div>
                    </div>
                    <div class="mjtc-form-wrapper for-terms-condtions-hide">
                        <div class="mjtc-form-title">
                            <?php echo esc_html(__('User Search', 'majestic-support')); ?>
                            <span class="mjtc-form-subtitle" id="subtitle_userSearch" style="display:none;">
                                <?php echo esc_html(__('This option is not available in this case', 'majestic-support')); ?>
                            </span>
                        </div>
                        <div class="mjtc-form-value"><?php echo wp_kses(MJTC_formfield::MJTC_select('search_user', $MJTC_yesno, isset(majesticsupport::$_data[0]['userfield']->search_user) ? majesticsupport::$_data[0]['userfield']->search_user : 1, '', array('class' => 'inputbox one mjtc-form-select-field')), MJTC_ALLOWED_TAGS); ?></div>
                    </div>
                    <?php
                }
                if (empty(majesticsupport::$_data[0]['userfield']->cannotshowonlisting)) { ?>
                    <div class="mjtc-form-wrapper for-terms-condtions-hide">
                        <div class="mjtc-form-title"><?php echo esc_html(__('Show On Listing', 'majestic-support')); ?></div>
                        <div class="mjtc-form-value"><?php echo wp_kses(MJTC_formfield::MJTC_select('showonlisting', $MJTC_yesno, isset(majesticsupport::$_data[0]['userfield']->showonlisting) ? majesticsupport::$_data[0]['userfield']->showonlisting : 0, '', array('class' => 'inputbox one mjtc-form-select-field')), MJTC_ALLOWED_TAGS); ?></div>
                    </div>
                    <?php
                }
                if (empty(majesticsupport::$_data[0]['userfield']->id) || (empty(majesticsupport::$_data[0]['userfield']->cannotunpublish) && !empty(majesticsupport::$_data[0]['userfield']->field) && !in_array(majesticsupport::$_data[0]['userfield']->field, ['termsandconditions1','termsandconditions2','termsandconditions3']))) { ?>
                    <div class="mjtc-form-wrapper for-terms-condtions-hide">
                        <div class="mjtc-form-title"><?php echo esc_html(__('Required', 'majestic-support')); ?></div>
                        <div class="mjtc-form-value"><?php echo wp_kses(MJTC_formfield::MJTC_select('required', $MJTC_yesno, isset(majesticsupport::$_data[0]['userfield']->required) ? majesticsupport::$_data[0]['userfield']->required : 0, '', array('class' => 'inputbox one mjtc-form-select-field')), MJTC_ALLOWED_TAGS); ?></div>
                    </div>
                    <?php
                } ?>
                <?php
                if (empty(majesticsupport::$_data[0]['userfield']->id) || (!empty(majesticsupport::$_data[0]['userfield']->field) && !in_array(majesticsupport::$_data[0]['userfield']->field, ['termsandconditions1','termsandconditions2','termsandconditions3']))) { ?>
                    <div class="mjtc-form-wrapper for-terms-condtions-hide">
                        <div class="mjtc-form-title">
                            <?php echo esc_html(__('Place Holder', 'majestic-support')); ?>
                            <span class="mjtc-form-subtitle" id="placeholder_not_available" style="display:none;">
                                <?php echo esc_html(__('This option is not available in this case', 'majestic-support')); ?>
                            </span>
                        </div>
                        <div class="mjtc-form-value"><?php echo wp_kses(MJTC_formfield::MJTC_text('placeholder', isset(majesticsupport::$_data[0]['userfield']->placeholder) ? majesticsupport::$_data[0]['userfield']->placeholder : '', array('class' => 'inputbox one mjtc-form-input-field','maxlength'=>225)), MJTC_ALLOWED_TAGS); ?></div>
                    </div>
                    <div class="mjtc-form-wrapper for-terms-condtions-hide">
                        <div class="mjtc-form-title"><?php echo esc_html(__('Description', 'majestic-support')); ?></div>
                        <div class="mjtc-form-value"><?php echo wp_kses(MJTC_formfield::MJTC_text('description', isset(majesticsupport::$_data[0]['userfield']->description) ? majesticsupport::$_data[0]['userfield']->description : '', array('class' => 'inputbox one mjtc-form-input-field','maxlength'=>225)), MJTC_ALLOWED_TAGS); ?></div>
                    </div>
                    <div class="mjtc-form-wrapper for-terms-condtions-hide">
                        <div class="mjtc-form-title">
                            <?php echo esc_html(__('Read Only', 'majestic-support')); ?>
                            <span class="mjtc-form-subtitle" id="subtitle_readOnly" style="display:none;">
                                <?php echo esc_html(__('This option is not available in this case', 'majestic-support')); ?>
                            </span>
                        </div>
                        <div class="mjtc-form-value"><?php echo wp_kses(MJTC_formfield::MJTC_select('readonly', $MJTC_yesno, isset(majesticsupport::$_data[0]['userfield']->readonly) ? majesticsupport::$_data[0]['userfield']->readonly : 0, '', array('class' => 'inputbox one mjtc-form-select-field')), MJTC_ALLOWED_TAGS); ?></div>
                    </div>
                    <?php 
                    if (empty(majesticsupport::$_data[0]['userfield']->cannotunpublish)) { ?>
                        <div class="mjtc-form-wrapper for-terms-condtions-hide">
                            <div class="mjtc-form-title"><?php echo esc_html(__('Admin/Agent Only', 'majestic-support')); ?></div>
                            <div class="mjtc-form-value"><?php echo wp_kses(MJTC_formfield::MJTC_select('adminonly', $MJTC_yesno, isset(majesticsupport::$_data[0]['userfield']->adminonly) ? majesticsupport::$_data[0]['userfield']->adminonly : 0, '', array('class' => 'inputbox one mjtc-form-select-field')), MJTC_ALLOWED_TAGS); ?></div>
                        </div>
                        <?php 
                    }
                }
                if (empty(majesticsupport::$_data[0]['userfield']->id) || (!empty(majesticsupport::$_data[0]['userfield']->id) && !empty(majesticsupport::$_data[0]['userfield']->isuserfield))) { ?>
                    <div class="mjtc-form-wrapper for-terms-condtions-hide">
                        <div class="mjtc-form-title"><?php echo esc_html(__('Field Size', 'majestic-support')); ?></div>
                        <div class="mjtc-form-value"><?php echo wp_kses(MJTC_formfield::MJTC_select('size', $MJTC_fieldsize, isset(majesticsupport::$_data[0]['userfield']->size) ? majesticsupport::$_data[0]['userfield']->size : 0, '', array('class' => 'inputbox one mjtc-form-select-field')), MJTC_ALLOWED_TAGS); ?></div>
                    </div>
                    <?php
                } ?>
                <div id="for-combo-options" >
                    <?php
                    $MJTC_arraynames = '';
                    $MJTC_comma = '';
                    if (isset(majesticsupport::$_data[0]['userfieldparams']) && majesticsupport::$_data[0]['userfield']->userfieldtype == 'depandant_field') {
                        foreach (majesticsupport::$_data[0]['userfieldparams'] as $MJTC_key => $MJTC_val) {
                            $MJTC_textvar = $MJTC_key;
                            if($MJTC_textvar != ''){
                                $MJTC_textvar = MJTC_majesticsupportphplib::MJTC_str_replace(' ','__',$MJTC_textvar);
                                $MJTC_textvar = MJTC_majesticsupportphplib::MJTC_str_replace('.','___',$MJTC_textvar);
                            }
                            $MJTC_divid = $MJTC_textvar;
                            $MJTC_textvar .='[]';
                            $MJTC_arraynames .= $MJTC_comma . "$MJTC_key";
                            $MJTC_comma = '_MS_Unique_88a9e3_';
                            ?>
                            <div class="ms-user-dd-field-wrap">
                                <div class="ms-user-dd-field-title">
                                    <?php echo esc_html($MJTC_key); ?>
                                </div>
                                <div class="ms-user-dd-field-value combo-options-fields" id="<?php echo esc_attr($MJTC_divid); ?>">
                                    <?php
                                    if (!empty($MJTC_val)) {
                                        foreach ($MJTC_val as $MJTC_each) {
                                            ?>
                                            <span class="input-field-wrapper">
                                                <input name="<?php echo esc_attr($MJTC_textvar); ?>" id="<?php echo esc_attr($MJTC_textvar); ?>" value="<?php echo esc_attr($MJTC_each); ?>" class="inputbox one user-field" type="text">
                                                <img alt="<?php echo esc_html(__('Delete', 'majestic-support')); ?>" class="input-field-remove-img" src="<?php echo esc_url(MJTC_PLUGIN_URL) ?>includes/images/delete.png">
                                            </span><?php
                                        }
                                    }
                                    // $safe_divid = wp_json_encode($MJTC_divid, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
                                    $mjtc_value = esc_js($MJTC_divid);
                                    $mjtc_value = ($MJTC_divid);
                                    ?>
                                    <input id="depandant-field-button" class="ms-button-link button user-field-val-button" onclick="getNextField('<?php echo esc_js($mjtc_value); ?>', this);" value="<?php echo esc_html(__('Add More', 'majestic-support')); ?>" type="button">
                                </div>
                            </div><?php
                        }
                    }
                    ?>
                </div>
                <?php 
                if (empty(majesticsupport::$_data[0]['userfield']->id) || (!empty(majesticsupport::$_data[0]['userfield']->id) && !empty(majesticsupport::$_data[0]['userfield']->isuserfield))) { ?>
                    <div id="divText" class="mjtc-form-wrapper">
                        <div class="mjtc-form-title"><?php echo esc_html(__('Max Length', 'majestic-support')); ?></div>
                        <div class="mjtc-form-value"><?php echo wp_kses(MJTC_formfield::MJTC_text('maxlength', isset(majesticsupport::$_data[0]['userfield']->maxlength) ? majesticsupport::$_data[0]['userfield']->maxlength : '', array('class' => 'inputbox one mjtc-form-input-field')), MJTC_ALLOWED_TAGS); ?></div>
                    </div>
                    <?php 
                } ?>
                <div class="mjtc-form-wrapper divColsRows">
                    <div class="mjtc-form-title"><?php echo esc_html(__('Columns', 'majestic-support')); ?></div>
                    <div class="mjtc-form-value"><?php echo wp_kses(MJTC_formfield::MJTC_text('cols', isset(majesticsupport::$_data[0]['userfield']->cols) ? majesticsupport::$_data[0]['userfield']->cols : '', array('class' => 'inputbox one mjtc-form-input-field')), MJTC_ALLOWED_TAGS); ?></div>
                </div>
                <div class="mjtc-form-wrapper divColsRows">
                    <div class="mjtc-form-title"><?php echo esc_html(__('Rows', 'majestic-support')); ?></div>
                    <div class="mjtc-form-value"><?php echo wp_kses(MJTC_formfield::MJTC_text('rows', isset(majesticsupport::$_data[0]['userfield']->rows) ? majesticsupport::$_data[0]['userfield']->rows : '', array('class' => 'inputbox one mjtc-form-input-field')), MJTC_ALLOWED_TAGS); ?></div>
                </div>
                <?php
                if(!empty(majesticsupport::$_data[0]['userfield']->field) && in_array(majesticsupport::$_data[0]['userfield']->field, ['termsandconditions1','termsandconditions2','termsandconditions3']) || empty(majesticsupport::$_data[0]['userfield']->id) || !empty(majesticsupport::$_data[0]['userfield']->isuserfield)) { ?>
                    <div class="for-terms-condtions-show" >
                        <?php
                        $MJTC_termsandconditions_text = '';
                        $MJTC_termsandconditions_linktype = '';
                        $MJTC_termsandconditions_link = '';
                        $MJTC_termsandconditions_page = '';
                        if( isset(majesticsupport::$_data[0]['userfieldparams']) && majesticsupport::$_data[0]['userfieldparams'] != '' && is_array(majesticsupport::$_data[0]['userfieldparams']) && !empty(majesticsupport::$_data[0]['userfieldparams'])){
                            $MJTC_termsandconditions_text = isset(majesticsupport::$_data[0]['userfieldparams']['termsandconditions_text']) ? majesticsupport::$_data[0]['userfieldparams']['termsandconditions_text'] :'' ;
                            $MJTC_termsandconditions_linktype = isset(majesticsupport::$_data[0]['userfieldparams']['termsandconditions_linktype']) ? majesticsupport::$_data[0]['userfieldparams']['termsandconditions_linktype'] :'' ;
                            $MJTC_termsandconditions_link = isset(majesticsupport::$_data[0]['userfieldparams']['termsandconditions_link']) ? majesticsupport::$_data[0]['userfieldparams']['termsandconditions_link'] :'' ;
                            $MJTC_termsandconditions_page = isset(majesticsupport::$_data[0]['userfieldparams']['termsandconditions_page']) ? majesticsupport::$_data[0]['userfieldparams']['termsandconditions_page'] :'' ;
                        } ?>
                        <div class="mjtc-form-wrapper ">
                            <div class="mjtc-form-title"><?php echo esc_html(__('Terms and Conditions Text', 'majestic-support')); ?></div>
                            <div class="mjtc-form-value"><?php echo wp_kses(MJTC_formfield::MJTC_text('termsandconditions_text', $MJTC_termsandconditions_text , array('class' => 'inputbox one mjtc-form-input-field')), MJTC_ALLOWED_TAGS); ?></div>
                            <div class="mjtc-form-desc">
                                <?php echo esc_html(__("e.g ' I have read and agree to the [link] Terms and Conditions[/link].  ' The text between [link] and [/link] will be linked to provided url or wordpress page.", 'majestic-support')); ?>
                            </div>
                        </div>
                        <div class="mjtc-form-wrapper ">
                            <div class="mjtc-form-title"><?php echo esc_html(__('Terms and Conditions Link Type', 'majestic-support')); ?></div>
                            <?php
                            $MJTC_linktype = array(
                                (object) array('id' => 1, 'text' => esc_html(__('Direct Link', 'majestic-support'))),
                                (object) array('id' => 2, 'text' => esc_html(__('Wordpress Page', 'majestic-support'))),
                                (object) array('id' => 3, 'text' => esc_html(__('None', 'majestic-support'))));
                            ?>
                            <div class="mjtc-form-value"><?php echo wp_kses(MJTC_formfield::MJTC_select('termsandconditions_linktype', $MJTC_linktype, $MJTC_termsandconditions_linktype, esc_html(__('Select Link Type', 'majestic-support')), array('class' => 'inputbox one mjtc-form-select-field')), MJTC_ALLOWED_TAGS); ?></div>
                        </div>
                        <div class="mjtc-form-wrapper for-terms-condtions-linktype1" style="display: none;">
                            <div class="mjtc-form-title"><?php echo esc_html(__('Terms and Conditions Link', 'majestic-support')); ?></div>
                            <div class="mjtc-form-value"><?php echo wp_kses(MJTC_formfield::MJTC_text('termsandconditions_link', $MJTC_termsandconditions_link , array('class' => 'inputbox one mjtc-form-input-field')), MJTC_ALLOWED_TAGS); ?></div>
                        </div>
                        <div class="mjtc-form-wrapper for-terms-condtions-linktype2" style="display: none;">
                            <div class="mjtc-form-title"><?php echo esc_html(__('Terms and Conditions Page', 'majestic-support')); ?></div>
                            <div class="mjtc-form-value"><?php echo wp_kses(MJTC_formfield::MJTC_select('termsandconditions_page', MJTC_includer::MJTC_getModel('configuration')->getPageList(), $MJTC_termsandconditions_page, esc_html(__('Select Wordpress page','majestic-support')), array('class' => 'inputbox one mjtc-form-select-field')), MJTC_ALLOWED_TAGS); ?></div>
                        </div>
                    </div>
                    <?php 
                } ?>
                <?php if (empty(majesticsupport::$_data[0]['userfield']->id) || (!empty(majesticsupport::$_data[0]['userfield']->id) && !empty(majesticsupport::$_data[0]['userfield']->isuserfield))) { ?>
                    <div class="mjtc-form-wrapper mjtc-form-visible-wrapper" style="border: unset;background: unset;font-size: 16px;margin: 0;font-weight: 500;color: #4b4b4d;">
                        <?php echo esc_html(__('Visibility Conditions', 'majestic-support')); ?>
                    </div>
                    <?php 
                    if( empty(majesticsupport::$_data[0]['userfield']->visibleparams)) { ?>
                        <div class="mjtc-form-wrapper mjtc-form-visible-wrapper" id="mjtc_and_row_0">
                            <div class="mjtc-form-value" id="mjtc_or_row_0">
                                <?php echo wp_kses(MJTC_formfield::MJTC_select('visibleParent[]', MJTC_includer::MJTC_getModel('fieldordering')->getFieldsForVisibleCombobox(majesticsupport::$_data['fieldfor'], $MJTC_mformid, isset(majesticsupport::$_data[0]['userfield']->field) ? majesticsupport::$_data[0]['userfield']->field : '', isset(majesticsupport::$_data[0]['userfield']->id) ? majesticsupport::$_data[0]['userfield']->id : ''), '', esc_html(__('Select Parent', 'majestic-support')), array('class' => 'inputbox mjtc-form-select-field mjtc-form-input-field-visible', 'onchange' => 'getChildForVisibleCombobox(this.value, 0);getConditionsForVisibleCombobox(this.value, 0);')), MJTC_ALLOWED_TAGS); ?>
                                <span class="visibleValueWrp">
                                    <?php echo wp_kses(MJTC_formfield::MJTC_select('visibleValue[]', '', '', esc_html(__('Select Child', 'majestic-support')), array('class' => 'inputbox one mjtc-form-select-field mjtc-form-input-field-visible')), MJTC_ALLOWED_TAGS); ?>
                                </span>
                                <span class="visibleConditionWrp">
                                    <?php echo wp_kses(MJTC_formfield::MJTC_select('visibleCondition[]', $MJTC_equalnotequal, '', esc_html(__('Select Condition', 'majestic-support')), array('class' => 'inputbox one mjtc-form-select-field mjtc-form-input-field-visible')), MJTC_ALLOWED_TAGS); ?>
                                </span>
                                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('visibleLogic[]', 'AND'), MJTC_ALLOWED_TAGS); ?>
                            </div>
                            <div class="mjtc-form-visible-or-row"></div>
                            <div class="mjtc-visible-conditions-addbtn-wrp">
                                <span class="mjtc-form-visible-addmore" onclick="getMoreORRow(this, <?php echo esc_js(majesticsupport::$_data['fieldfor']); ?>, <?php echo esc_js($MJTC_mformid); ?>, '<?php echo esc_js(isset(majesticsupport::$_data[0]['userfield']->field) ? majesticsupport::$_data[0]['userfield']->field : ''); ?>', '<?php echo esc_js(isset(majesticsupport::$_data[0]['userfield']->id) ? majesticsupport::$_data[0]['userfield']->id : ''); ?>')">
                                    <img alt="<?php echo esc_html(__('OR', 'majestic-support')); ?>" class="input-field-remove-img" src="<?php echo esc_url(MJTC_PLUGIN_URL) ?>includes/images/plus-icon.png">
                                    <?php echo esc_html(__('OR', 'majestic-support')); ?>
                                </span>
                            </div>
                        </div>
                        <?php 
                    } else {
                        if (!empty(majesticsupport::$_data[0]['userfield']->visibleparams)) {
                            $MJTC_androws = json_decode(majesticsupport::$_data[0]['userfield']->visibleparams);
                            $MJTC_nextorid = 0;
                            foreach ($MJTC_androws as $MJTC_androwindex => $MJTC_androw) { ?>
                                <div class="mjtc-form-visible-andwrp" id="mjtc_and_row_<?php echo esc_attr($MJTC_androwindex); ?>">
                                    <?php
                                    if ($MJTC_androwindex != 0) { ?>
                                        <div class="mjtc-form-visible-subheading">
                                            <?php echo esc_html(__('AND', 'majestic-support')); ?>
                                        </div>
                                        <?php 
                                    } ?>
                                    <div class="mjtc-form-wrapper mjtc-form-visible-wrapper">
                                        <?php 
                                        foreach ($MJTC_androw as $MJTC_orrowindex => $MJTC_orrow) { ?>
                                            <div id="mjtc_or_row_<?php echo esc_attr($MJTC_nextorid); ?>" >
                                                <?php
                                                if ($MJTC_orrowindex != 0) { ?>
                                                    <div class="mjtc-form-visible-subheading">
                                                        <?php echo esc_html(__('OR', 'majestic-support')); ?>
                                                    </div>
                                                    <?php 
                                                } ?>
                                                <div class="mjtc-form-value">
                                                    <?php echo wp_kses(MJTC_formfield::MJTC_select('visibleParent[]', MJTC_includer::MJTC_getModel('fieldordering')->getFieldsForVisibleCombobox(majesticsupport::$_data['fieldfor'], $MJTC_mformid, majesticsupport::$_data[0]['userfield']->field, majesticsupport::$_data[0]['userfield']->id), $MJTC_orrow->visibleParent, esc_html(__('Select Parent', 'majestic-support')), array('class' => 'inputbox mjtc-form-select-field mjtc-form-input-field-visible', 'onchange' => 'getChildForVisibleCombobox(this.value, '.$MJTC_nextorid.');getConditionsForVisibleCombobox(this.value, '.$MJTC_nextorid.');')), MJTC_ALLOWED_TAGS); ?>
                                                    <span class="visibleValueWrp">
                                                        <?php echo wp_kses(html_entity_decode(MJTC_includer::MJTC_getModel('fieldordering')->getChildForVisibleCombobox($MJTC_orrow->visibleParent, $MJTC_orrow->visibleValue)), MJTC_ALLOWED_TAGS); ?>
                                                    </span>
                                                    <span class="visibleConditionWrp">
                                                        <?php echo wp_kses(html_entity_decode(MJTC_includer::MJTC_getModel('fieldordering')->getConditionsForVisibleCombobox($MJTC_orrow->visibleParent, $MJTC_orrow->visibleCondition)), MJTC_ALLOWED_TAGS); ?>
                                                    </span>
                                                    <div class="mjtc-visible-conditions-body-row">
                                                        <div class="mjtc-visible-conditions-body-value">
                                                            <span onclick='deleteOrRow("mjtc_or_row_<?php echo esc_js($MJTC_nextorid); ?>")' class='mjtc-visible-conditions-delbtn'>
                                                                <img class='input-field-remove-img' src='<?php echo esc_url(MJTC_PLUGIN_URL) ?>includes/images/delete-2.png' />
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <?php echo wp_kses(MJTC_formfield::MJTC_hidden('visibleLogic[]', $MJTC_orrow->visibleLogic), MJTC_ALLOWED_TAGS); ?>
                                                </div>
                                            </div>
                                            <?php
                                            $MJTC_nextorid++;
                                            $majesticsupport_js = "
                                                nextorid++;
                                            ";
                                            wp_add_inline_script('majestic-support-cmain-js',$majesticsupport_js);
                                        } ?>
                                        <div class="mjtc-form-visible-or-row"></div>
                                        <div class="mjtc-visible-conditions-addbtn-wrp">
                                            <span class="mjtc-form-visible-addmore" onclick="getMoreORRow(this, <?php echo esc_js(majesticsupport::$_data['fieldfor']); ?>, <?php echo esc_js($MJTC_mformid); ?>, '<?php echo esc_js( isset(majesticsupport::$_data[0]['userfield']->field ) ? majesticsupport::$_data[0]['userfield']->field : '' ); ?>', '<?php echo esc_js( isset( majesticsupport::$_data[0]['userfield']->id ) ? majesticsupport::$_data[0]['userfield']->id : '' ); ?>')">
                                                <img alt="<?php echo esc_html(__('OR', 'majestic-support')); ?>" class="input-field-remove-img" src="<?php echo esc_url(MJTC_PLUGIN_URL) ?>includes/images/plus-icon.png">
                                                <?php echo esc_html(__('OR', 'majestic-support')); ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                $majesticsupport_js = "
                                    nextandid++;
                                ";
                                wp_add_inline_script('majestic-support-cmain-js',$majesticsupport_js);
                            }
                        }
                    } ?>
                    <div class="mjtc-form-visible-add-row"></div>
                    <div class="mjtc-visible-conditions-addbtn-wrp">
                        <span class="mjtc-form-visible-addmore" onclick="getMoreANDRow(<?php echo esc_js(majesticsupport::$_data['fieldfor']); ?>, <?php echo esc_js($MJTC_mformid); ?>, '<?php echo esc_js( isset(majesticsupport::$_data[0]['userfield']->field ) ? majesticsupport::$_data[0]['userfield']->field : '' ); ?>', '<?php echo esc_js( isset(majesticsupport::$_data[0]['userfield']->id) ? majesticsupport::$_data[0]['userfield']->id : ''); ?>')">
                            <img alt="<?php echo esc_html(__('AND', 'majestic-support')); ?>" class="input-field-remove-img" src="<?php echo esc_url(MJTC_PLUGIN_URL) ?>includes/images/plus-icon.png">
                            <?php echo esc_html(__('Add new', 'majestic-support')).' "'.esc_html(__('AND', 'majestic-support')).'" '.esc_html(__('visibility condition', 'majestic-support')); ?>
                        </span>
                    </div>
                <?php } ?>
                <div id="divValues" class="msadmin-add-user-fields-wrp divColsRowsno-margin">
                    <h3 class="msadmin-add-user-fields-title"><?php echo esc_html(__('Use the table below to add new values', 'majestic-support')); ?></h3>
                    <div class="page-actions no-margin">
                        <div id="user-field-values" class="white-background" class="no-padding">
                            <?php
                            if (isset(majesticsupport::$_data[0]['userfield']) && majesticsupport::$_data[0]['userfield']->userfieldtype != 'depandant_field') {
                                if (isset(majesticsupport::$_data[0]['userfieldparams']) && !empty(majesticsupport::$_data[0]['userfieldparams'])) {
                                    foreach (majesticsupport::$_data[0]['userfieldparams'] as $MJTC_key => $MJTC_val) {
                                        ?>
                                        <span class="input-field-wrapper">
                                            <?php echo wp_kses(MJTC_formfield::MJTC_text('values['.esc_attr($MJTC_val).']', isset($MJTC_val) ? $MJTC_val : '', array('class' => 'inputbox one user-field', 'onchange' => 'updateSelectOptionsForDefaultValues()')), MJTC_ALLOWED_TAGS); ?>
                                            <img alt="<?php echo esc_html(__('Delete', 'majestic-support')); ?>" class="input-field-remove-img" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/delete.png" />
                                        </span>
                                    <?php
                                    }
                                } else {
                                    $MJTC_val = isset($MJTC_val) ? $MJTC_val : ''; ?>
                                    <span class="input-field-wrapper">
                                    <?php echo wp_kses(MJTC_formfield::MJTC_text('values['.esc_attr($MJTC_val).']', $MJTC_val, array('class' => 'inputbox one user-field', 'onchange' => 'updateSelectOptionsForDefaultValues()')), MJTC_ALLOWED_TAGS); ?>
                                        <img alt="<?php echo esc_html(__('Delete', 'majestic-support')); ?>" class="input-field-remove-img" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/delete.png" />
                                    </span>
                                <?php
                                }
                            }
                            ?>
                            <a title="<?php echo esc_attr(__('Add Value', 'majestic-support')); ?>" class="ms-button-link button user-field-val-button" id="user-field-val-button" onclick="insertNewRow();"><?php echo esc_html(__('Add Value', 'majestic-support')); ?></a>
                        </div>
                    </div>
                </div>
                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('multiformid', $MJTC_mformid), MJTC_ALLOWED_TAGS); ?>
                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('id', isset(majesticsupport::$_data[0]['userfield']->id) ? majesticsupport::$_data[0]['userfield']->id : ''), MJTC_ALLOWED_TAGS); ?>
                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('fieldfor', majesticsupport::$_data['fieldfor']), MJTC_ALLOWED_TAGS); ?>
                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('ordering', isset(majesticsupport::$_data[0]['userfield']->ordering) ? majesticsupport::$_data[0]['userfield']->ordering : '' ), MJTC_ALLOWED_TAGS); ?>
                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('form_request', 'majesticsupport'), MJTC_ALLOWED_TAGS); ?>
                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('isuserfield', isset(majesticsupport::$_data[0]['userfield']->id) ? majesticsupport::$_data[0]['userfield']->isuserfield : 1), MJTC_ALLOWED_TAGS); ?>
                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('fieldname', isset(majesticsupport::$_data[0]['userfield']->field) ? majesticsupport::$_data[0]['userfield']->field : '' ), MJTC_ALLOWED_TAGS); ?>
                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('depandant_field', isset(majesticsupport::$_data[0]['userfield']->depandant_field) ? majesticsupport::$_data[0]['userfield']->depandant_field : '' ), MJTC_ALLOWED_TAGS); ?>
                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('field', isset(majesticsupport::$_data[0]['userfield']->field) ? majesticsupport::$_data[0]['userfield']->field : '' ), MJTC_ALLOWED_TAGS); ?>
                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('arraynames2', $MJTC_arraynames), MJTC_ALLOWED_TAGS); ?>
                <div class="mjtc-form-button">
                    <?php echo wp_kses(MJTC_formfield::MJTC_submitbutton('save', esc_html(__('Save Field', 'majestic-support')), array('class' => 'button mjtc-form-save')), MJTC_ALLOWED_TAGS); ?>
                </div>
            </form>
        </div>
        <?php
        $mjtc_scriptdateformat = MJTC_includer::MJTC_getModel('majesticsupport')->MJTC_getDateFormat();
        $majesticsupport_js ="
            jQuery(document).ready(function () {
                toggleType(jQuery('select#userfieldtype').val());
                updateSelectOptionsForDefaultValues();
                jQuery('#termsandconditions_linktype').on('change', function() {
                    if(this.value == 1){
                        jQuery('.for-terms-condtions-linktype1').slideDown();
                        jQuery('.for-terms-condtions-linktype2').hide();
                    }else if(this.value == 2){
                        jQuery('.for-terms-condtions-linktype1').hide();
                        jQuery('.for-terms-condtions-linktype2').slideDown();
                    }else{
                        jQuery('.for-terms-condtions-linktype1').hide();
                        jQuery('.for-terms-condtions-linktype2').hide();
                    }
                });

                var intial_val = jQuery('#termsandconditions_linktype').val();
                if(intial_val == 1){
                    jQuery('.for-terms-condtions-linktype1').slideDown();
                    jQuery('.for-terms-condtions-linktype2').hide();
                }else if(intial_val == 2){
                    jQuery('.for-terms-condtions-linktype1').hide();
                    jQuery('.for-terms-condtions-linktype2').slideDown();
                }else{
                    jQuery('.for-terms-condtions-linktype1').hide();
                    jQuery('.for-terms-condtions-linktype2').hide();
                }
            });
            function disableAll() {
                jQuery('#divValues').slideUp();
                jQuery('.divColsRows').slideUp();
                jQuery('#divText').slideUp();
            }
            function toggleType(type) {
                enableDefaultValueFieldDueToValues();
                if(type == 'combo' || type == 'radio' || type == 'multiple' || type == 'checkbox') {
                    // enableDefaultValueFieldDueToValues();
                    // jQuery('#defaultvalue_not_available').hide();
                    // jQuery('.defaultvalue_input').hide();
                    // jQuery('.defaultvalue_select').show();
                    // jQuery('#subtitle_defaultvalue').show();
                } else {
                    // recheck
                    // enableDefaultValueField();
                    if(type == 'date') {
                        jQuery('#defaultvalue_input').addClass('custom_date mjtc-form-date-field');
                        jQuery('.custom_date').datepicker({dateFormat: '". esc_html($mjtc_scriptdateformat) ."'});
                        enableDefaultValueField();
                    } else if (type == 'depandant_field') {
                        disableDefaultValueField();
                    } else if (type == 'file' || type == 'termsandconditions') {
                        disableDefaultValueField();
                        disableAdminSearchField();
                        disableUserSearchField();
                        disableReadOnlyField();
                    } else {
                        enableDefaultValueField();
                    }
                }
                // code for placeholder
                if(type == 'checkbox' || type == 'combo' || type == 'radio' || type == 'depandant_field' || type == 'file' || type == 'multiple' || type == 'termsandconditions') {
                    disablePlaceholderField();
                } else {
                    enablePlaceholderField();
                }
                disableAll();
                selType(type);
            }
            function prep4SQL(field) {
                if (field.value != '') {
                    field.value = field.value.replace('mjtc_', '');
                    field.value = 'mjtc_' + field.value.replace(/[^a-zA-Z]+/g, '');
                }
            }
            function selType(sType) {
                var elem;
                /*
                 text
                 checkbox
                 date
                 combo
                 email
                 textarea
                 radio
                 editor
                 depandant_field
                 multiple*/

                switch (sType) {
                    case 'editor':
                        jQuery('div.for-terms-condtions-hide').show();
                        jQuery('#divText').slideUp();
                        jQuery('#divValues').slideUp();
                        jQuery('.divColsRows').slideUp();
                        jQuery('div#for-combo-wrapper').hide();
                        jQuery('div#for-combo-options').hide();
                        jQuery('div#for-combo-options-head').hide();
                        jQuery('div.for-terms-condtions-show').slideUp();
                        break;
                    case 'textarea':
                        jQuery('div.for-terms-condtions-hide').show();
                        jQuery('#divText').slideUp();
                        jQuery('.divColsRows').slideDown();
                        jQuery('#divValues').slideUp();
                        jQuery('div#for-combo-wrapper').hide();
                        jQuery('div#for-combo-options').hide();
                        jQuery('div#for-combo-options-head').hide();
                        jQuery('div.for-terms-condtions-show').slideUp();
                        break;
                    case 'email':
                    case 'password':
                    case 'text':
                    case 'date':
                        jQuery('div.for-terms-condtions-hide').show();
                        jQuery('#divText').slideDown();
                        jQuery('div#for-combo-wrapper').hide();
                        jQuery('div#for-combo-options').hide();
                        jQuery('div#for-combo-options-head').hide();
                        jQuery('div.for-terms-condtions-show').slideUp();
                        break;
                    case 'file':
                        jQuery('div.for-terms-condtions-hide').show();
                        jQuery('#divText').slideUp();
                        jQuery('div#for-combo-wrapper').hide();
                        jQuery('div#for-combo-options').hide();
                        jQuery('div#for-combo-options-head').hide();
                        jQuery('div.for-terms-condtions-show').slideUp();
                        break;
                    case 'termsandconditions':
                        jQuery('div#for-combo-wrapper').hide();
                        jQuery('div#for-combo-options').hide();
                        jQuery('div#for-combo-options-head').hide();
                        jQuery('#divText').slideUp();
                        jQuery('.divColsRows').slideUp();
                        jQuery('#divValues').slideUp();
                        jQuery('div#for-combo-wrapper').hide();
                        jQuery('div#for-combo-options').hide();
                        jQuery('div#for-combo-options-head').hide();
                        jQuery('div.for-terms-condtions-hide').hide();
                        jQuery('div.for-terms-condtions-show').slideDown();
                        break;
                    case 'combo':
                    case 'multiple':
                        jQuery('div.for-terms-condtions-hide').show();
                        jQuery('#divValues').slideDown();
                        jQuery('div#for-combo-wrapper').hide();
                        jQuery('div#for-combo-options').hide();
                        jQuery('div#for-combo-options-head').hide();
                        jQuery('div.for-terms-condtions-show').slideUp();
                        break;
                    case 'depandant_field':
                        jQuery('div.for-terms-condtions-hide').show();
                        comboOfFields();
                        jQuery('div.for-terms-condtions-show').slideUp();
                        break;
                    case 'radio':
                    case 'checkbox':
                        jQuery('div.for-terms-condtions-hide').show();
                        jQuery('#divValues').slideDown();
                        jQuery('div#for-combo-wrapper').hide();
                        jQuery('div#for-combo-options').hide();
                        jQuery('div#for-combo-options-head').hide();
                        jQuery('div.for-terms-condtions-show').slideUp();
                        break;
                    case 'delimiter':
                    default:
                }
                return;
            }
            function comboOfFields() {
                ajaxurl = '". esc_url(admin_url('admin-ajax.php'))."';
                var formid = jQuery('input#multiformid').val();
                var ff = jQuery('input#fieldfor').val();
                var pf = jQuery('input#fieldname').val();
                jQuery.post(ajaxurl, {action: 'mjsupport_ajax', mjsmod: 'fieldordering', task: 'getFieldsForComboByFieldFor', formid : formid, fieldfor: ff,parentfield:pf, '_wpnonce':'". esc_attr(wp_create_nonce("get-fields-for-combo-by-fieldfor")) ."'}, function (data) {
                    if (data) {
                        console.log(data);
                        var d = jQuery.parseJSON(data);
                        jQuery('div#for-combo').html(MJTC_msDecodeHTML(d));
                        jQuery('div#for-combo-wrapper').show();
                    }
                });
            }
            function getDataOfSelectedField(nonce) {
                ajaxurl = '". esc_url(admin_url('admin-ajax.php'))."';
                var field = jQuery('select#parentfield').val();
                var ff = jQuery('input#fieldfor').val();
                jQuery.post(ajaxurl, {action: 'mjsupport_ajax', mjsmod: 'fieldordering', task: 'getSectionToFillValues', pfield: field, fieldfor: ff, '_wpnonce': nonce}, function (data) {
                    if (data) {
                        var d = jQuery.parseJSON(data);
                        jQuery('div#for-combo-options-head').show();
                        jQuery('div#for-combo-options').html(MJTC_msDecodeHTML(d));
                        jQuery('div#for-combo-options').show();
                    }else{
                        jQuery('div#for-combo-options-head').hide();
                        jQuery('div#for-combo-options').html();
                        jQuery('div#for-combo-options').hide();
                    }
                });
            }
            function getNextField(divid, object) {
                console.log(divid);
                let cleandivid = divid.replaceAll('[', '').replaceAll(']', '');
                var textvar = cleandivid + '[]';

                // Create elements safely using jQuery
                var wrapper = jQuery(\"<span class='input-field-wrapper'></span>\");
                var input = jQuery(\"<input>\", {
                    type: \"text\",
                    name: textvar,
                    class: \"inputbox one user-field\"
                });

                var img = jQuery(\"<img>\", {
                    alt: \"Delete\",
                    class: \"input-field-remove-img\",
                    src: \"". esc_url(MJTC_PLUGIN_URL) . "includes/images/delete.png\"
                });

                wrapper.append(input).append(img);

                jQuery(object).before(wrapper);
            }
            function getObject(obj) {
                var strObj;
                if (document.all) {
                    strObj = document.all.item(obj);
                } else if (document.getElementById) {
                    strObj = document.getElementById(obj);
                }
                return strObj;
            }

            function updateSelectOptionsForDefaultValues() {
                var select = jQuery('select#defaultvalue_select');
                select.empty(); // Clear existing options

                var defaultvalueRaw = jQuery('#defaultvalue_input').val();
                var defaultvalue = typeof defaultvalueRaw === 'string' ? defaultvalueRaw.trim() : '';

                select.append(jQuery('<option>', {
                    value: '',
                    text: '". esc_html(__('Select Default Value', 'majestic-support')) ."'
                }));
                var value_count = 0;

                jQuery('.user-field').each(function () {
                    var val = jQuery(this).val().trim();
                    if (val !== '') {
                        var option = jQuery('<option>', {
                            value: val,
                            text: val
                        });

                        if (val === defaultvalue) {
                            option.prop('selected', true);
                        }

                        select.append(option);
                        jQuery('#subtitle_defaultvalue').hide();
                        value_count++;
                    }
                });
                if(value_count == 0){
                    disableDefaultValueFieldDueToValues();
                    // jQuery('#subtitle_defaultvalue').show();
                }
            }

            function insertNewRow() {
                var fieldhtml = '<span class=\"input-field-wrapper\" ><input onchange=\"updateSelectOptionsForDefaultValues();\" name=\"values[]\" id=\"values[]\" value=\"\" class=\"inputbox one user-field\" type=\"text\" /><img alt=\"". esc_html(__('Delete', 'majestic-support')) ."\" class=\"input-field-remove-img\" src=\"". esc_url(MJTC_PLUGIN_URL)."includes/images/delete.png\" /></span>';
                jQuery('#user-field-val-button').before(fieldhtml);
            }
            jQuery(document).ready(function () {
                jQuery('body').delegate('img.input-field-remove-img', 'click', function () {
                    jQuery(this).parent().remove();
                    updateSelectOptionsForDefaultValues();
                });
            });

        ";
        wp_add_inline_script('majestic-support-cmain-js',$majesticsupport_js);
        // in case of system fields
        // in case of custom fields
        if (isset(majesticsupport::$_data[0]['userfield']->field)) {
            // system fields where "default value" is not in use
            $majesticsupport_js ='';
            if (in_array(majesticsupport::$_data[0]['userfield']->field, ['users', 'department', 'helptopic', 'priority', 'premade', 'attachments', 'product', 'eddorderid', 'envatopurchasecode', 'eddproductid'])) {
                $majesticsupport_js .='
                    jQuery(document).ready(function () {
                        disableDefaultValueField();
                    });
                ';
            }
            // system fields where "placeholder" is not in use
            if (in_array(majesticsupport::$_data[0]['userfield']->field, ['department', 'helptopic', 'priority', 'issuesummary', 'attachments', 'product', 'eddorderid', 'envatopurchasecode', 'eddproductid'])) {
                $majesticsupport_js .='
                    jQuery(document).ready(function () {
                        disablePlaceholderField();
                    });
                ';
            }
            // system fields where "read only" is not in use
            if (in_array(majesticsupport::$_data[0]['userfield']->field, ['premade'])) {
                $majesticsupport_js .='
                    jQuery(document).ready(function () {
                        disableReadOnlyField();
                    });
                ';
            }
            // custom fields where "placeholder" is not in use
            if (in_array(majesticsupport::$_data[0]['userfield']->userfieldtype, ['checkbox', 'combo', 'radio', 'depandant_field', 'file', 'multiple', 'termsandconditions'])) {
                $majesticsupport_js .='
                    jQuery(document).ready(function () {
                        disablePlaceholderField();
                    });
                ';
            }
            wp_add_inline_script('majestic-support-cmain-js',$majesticsupport_js);
        }
        ?>
    </div>
</div>
