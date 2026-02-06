<?php
    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

    wp_enqueue_script('jquery-ui-datepicker');
    wp_enqueue_script('majesticsupport-file_validate.js', MJTC_PLUGIN_URL . 'includes/js/file_validate.js');
    wp_enqueue_style('majesticsupport-jquery-ui-css', MJTC_PLUGIN_URL . 'includes/css/jquery-ui-smoothness.css');
?>
<?php MJTC_message::MJTC_getMessage(); ?>
<?php $MJTC_formdata = MJTC_formfield::MJTC_getFormData(); ?>
<?php
$mjtc_scriptdateformat = MJTC_includer::MJTC_getModel('majesticsupport')->MJTC_getDateFormat();
?>
<?php
$majesticsupport_js ="
    function updateuserlist(pagenum){
        jQuery.post(ajaxurl, {action: 'mjsupport_ajax', mjsmod: 'majesticsupport', task: 'getuserlistajax',userlimit:pagenum, '_wpnonce':'". esc_attr(wp_create_nonce("get-user-list-ajax")) ."'}, function (data) {
            if(data){
                jQuery('div#userpopup-records').html('');
                jQuery('div#userpopup-records').html(data);
                setUserLink();
            }
        });
    }
    function setUserLink() {
        jQuery('a.mjtc-userpopup-link').each(function () {
            var anchor = jQuery(this);
            jQuery(anchor).click(function (e) {
                var id = jQuery(this).attr('data-id');
                var name = jQuery(this).attr('data-username');
                var email = jQuery(this).attr('data-email');
                var displayname = jQuery(this).attr('data-name');
                jQuery('input#username-text').val(name);
                jQuery('input#name').val(displayname);
                jQuery('input#email').val(email);
                jQuery('input#uid').val(id);
                jQuery('div#userpopup').slideUp('slow', function () {
                    jQuery('div#userpopupblack').hide();
                });
            });
        });
    }
    jQuery(document).ready(function () {
        
        jQuery('a#userpopup').click(function (e) {
            e.preventDefault();
            jQuery('div#userpopupblack').show();
            jQuery.post(ajaxurl, {action: 'mjsupport_ajax', mjsmod: 'majesticsupport', task: 'getuserlistajax', '_wpnonce':'". esc_attr(wp_create_nonce("get-user-list-ajax"))."'}, function (data) {
                if(data){
                    jQuery('div#userpopup-records').html('');
                    jQuery('div#userpopup-records').html(data);
                    setUserLink();
                }
            });
            jQuery('div#userpopup').slideDown('slow');
        });
        jQuery('form#userpopupsearch').submit(function (e) {
            e.preventDefault();
            var username = jQuery('input#username').val();
            var name = jQuery('input#name').val();
            var emailaddress = jQuery('input#emailaddress').val();
            jQuery.post(ajaxurl, {action: 'mjsupport_ajax', name: name, username: username, emailaddress: emailaddress, mjsmod: 'majesticsupport', task: 'getusersearchajax', '_wpnonce':'". esc_attr(wp_create_nonce("get-usersearch-ajax"))."'}, function (data) {
                if (data) {
                    jQuery('div#userpopup-records').html(data);
                    setUserLink();
                }
            });//jquery closed
        });
        jQuery('.userpopup-close, div#userpopupblack').click(function (e) {
            jQuery('div#userpopup').slideUp('slow', function () {
                jQuery('div#userpopupblack').hide();
            });

        });
    });
    // to get premade and append to isssue summery
    function getpremade(val) {
        jQuery.post(ajaxurl, {action: 'mjsupport_ajax', val: val, mjsmod: 'cannedresponses', task: 'getpremadeajax', '_wpnonce':'". esc_attr(wp_create_nonce("get-premade-ajax")) ."'}, function (data) {
            if (data) {
                var append = jQuery('input#append1:checked').length;
                if (append == 1) {
                    var content = tinyMCE.get('mjsupport_message').getContent();
                    content = content + data;
                    tinyMCE.get('mjsupport_message').execCommand('mceSetContent', false, content);
                }
                else {
                    tinyMCE.get('mjsupport_message').execCommand('mceSetContent', false, data);
                }
            }
        });//jquery closed
    }
    // to get premade and append to isssue summery
    function getHelpTopicByDepartment(val) {
        jQuery.post(ajaxurl, {action: 'mjsupport_ajax', val: val, mjsmod: 'department', task: 'getHelpTopicByDepartment', '_wpnonce':'". esc_attr(wp_create_nonce("get-help-topic-by-department"))."'}, function (data) {
            if (data != false) {
                jQuery('div#helptopic').html(data);
            }else{
                jQuery('div#helptopic').html( '<div class=\"helptopic-no-rec\">". esc_html(__('No help topic found','majestic-support'))."</div>');
            }
        });//jquery closed
    }

    function getPremadeByDepartment(val) {
        jQuery.post(ajaxurl, {action: 'mjsupport_ajax', val: val, mjsmod: 'department', task: 'getPremadeByDepartment', '_wpnonce':'". esc_attr(wp_create_nonce("get-premade-by-department"))."'}, function (data) {
            if (data != false) {
                jQuery('div#premade').html(MJTC_msDecodeHTML(data));
            }else{
                jQuery('div#premade').html('<div class=\"premade-no-rec\">". esc_html(__('No premade response found','majestic-support'))."</div>');
            }
        });//jquery closed
    }

    jQuery(document).ready(function ($) {
        $('.custom_date').datepicker({dateFormat: '".esc_html($mjtc_scriptdateformat)."'});
        jQuery('#tk_attachment_add').click(function () {
            var obj = this;
            var current_files = jQuery('input[name=\'filename[]\']').length;
            var total_allow =". majesticsupport::$_config['no_of_attachement'].";
            var append_text = '<span class=\"tk_attachment_value_text\"><input name=\"filename[]\" type=\"file\" onchange=\"MJTC_uploadfile(this,\"". esc_js(majesticsupport::$_config['file_maximum_size'])."\",\"". esc_js(majesticsupport::$_config['file_extension'])."\");\" size=\"20\" maxlenght=\"30\"  /><span  class=\"tk_attachment_remove\"></span></span>';

            if (current_files < total_allow) {
                jQuery('.tk_attachment_value_wrapperform').append(append_text);
            } else if ((current_files === total_allow) || (current_files > total_allow)) {
                alert(\"". esc_html(__('File upload limit exceeds', 'majestic-support'))."\");
                jQuery(obj).hide();
            }
        });

        jQuery(document).delegate('.tk_attachment_remove', 'click', function (e) {
            jQuery(this).parent().remove();
            var current_files = jQuery('input[name=\'filename[]\']').length;
            var total_allow =". majesticsupport::$_config['no_of_attachement'].";
            if (current_files < total_allow) {
                jQuery('#tk_attachment_add').show();
            }
        });
        $.validate();
    });
    // woocomerce
    function ms_wc_order_products(productid){
        var orderid = jQuery('#wcorderid').val();
        if(orderid){
            jQuery.post(ajaxurl, {action: 'mjsupport_ajax', mjsmod: 'woocommerce', task: 'getWcOrderProductsAjax',orderid: orderid,productid: productid, '_wpnonce':'". esc_attr(wp_create_nonce("get-wcorder-products-ajax"))."'},function (data) {
                    data = JSON.parse(data);
                    jQuery('#wcproductid-wrap').html(MJTC_msDecodeHTML(data.html));
                    if(data.productfound){
                        jQuery('.ms_product_found').show();
                    }else{
                        jQuery('.ms_product_not_found').show();
                    }
                }
            );
        }
    }
    function ms_edd_order_products(){
        var orderid = jQuery('select#eddorderid').val();
        jQuery.post(ajaxurl, {action: 'mjsupport_ajax', mjsmod: 'easydigitaldownloads', task: 'getEDDOrderProductsAjax', eddorderid:orderid, '_wpnonce':'". esc_attr(wp_create_nonce("get-eddorder-products-ajax"))."'}, function (data) {
                jQuery('#eddproductid-wrap').html(data);
            }
        );
    }

    function ms_eed_product_licenses(){
        var eddproductid = jQuery('select#eddproductid').val();
        var orderid = jQuery('select#eddorderid').val();
        jQuery.post(ajaxurl, {action: 'mjsupport_ajax', mjsmod: 'easydigitaldownloads', task: 'getEDDProductlicensesAjax', eddproductid:eddproductid, eddorderid:orderid, '_wpnonce':'". esc_attr(wp_create_nonce("get-edd-productlicenses-ajax"))."'}, function (data) {
                jQuery('#eddlicensekey-wrap').html(data);
            }
        );
    }

    jQuery(document).ready(function(){
        jQuery(document).on('change', 'select#eddorderid', function() {
            ms_edd_order_products();
        });";
        if(!isset(majesticsupport::$_data[0]->id)){ 
            $majesticsupport_js .="
            if(jQuery('select#eddorderid').val()){
                ms_edd_order_products();
            } ";
        }
        $majesticsupport_js .="
        jQuery(document).on('change', 'select#eddproductid', function() {
            ms_eed_product_licenses();
        });
        if(jQuery('select#eddproductid').val()){
            ms_eed_product_licenses();
        }

        jQuery('#wcorderid').focusout(function(){
            ms_wc_order_products();
            jQuery('input#wcorderid').removeClass('loading');
        });
        jQuery('#wcorderid').keyup(function(){
            jQuery('.ms_product_found').hide();
            jQuery('.ms_product_not_found').hide();
            if(jQuery('#wcorderid').val()){
                jQuery('input#wcorderid').addClass('loading');
            }else{
                jQuery('input#wcorderid').removeClass('loading');
            }
        });
        if(jQuery('#wcorderid').val()){
            ms_wc_order_products();
        }
    });

";
wp_add_inline_script('majestic-support-cmain-js',$majesticsupport_js);
?>  
<span style="display:none" id="filesize"><?php echo esc_html(__('Error file size too large', 'majestic-support')); ?></span>
<span style="display:none" id="fileext"><?php echo esc_html(__('The uploaded file extension not valid', 'majestic-support')); ?></span>
<div id="msadmin-wrapper">
    <div id="msadmin-leftmenu">
        <?php
        if(current_user_can('ms_support_ticket')){
            MJTC_includer::MJTC_getClassesInclude('msadminsidemenu');
        }
        ?>
    </div>
    <div id="msadmin-data">
        <?php MJTC_includer::MJTC_getModel('majesticsupport')->getPageTitle('admin_addticket'); ?>
        <div id="msadmin-data-wrp">
            <div id="userpopupblack" style="display:none;"></div>
            <div id="userpopup" style="display:none;">
                <div class="userpopup-top">
                    <div class="userpopup-heading">
                        <?php echo esc_html(__('Select user','majestic-support')); ?>
                    </div>
                    <img alt="<?php echo esc_html(__('Close','majestic-support')); ?>" class="userpopup-close" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/close-icon-white.png" />
                </div>
                <div class="userpopup-search">
                    <form id="userpopupsearch">
                        <div class="userpopup-fields-wrp">
                            <div class="userpopup-fields">
                                <input type="text" name="username" id="username" placeholder="<?php echo esc_html(__('Username','majestic-support')); ?>" />
                            </div>
                            <div class="userpopup-fields">
                                <input type="text" name="name" id="name" placeholder="<?php echo esc_html(__('Name','majestic-support')); ?>" />
                            </div>
                            <div class="userpopup-fields">
                                <input type="text" name="emailaddress" id="emailaddress" placeholder="<?php echo esc_html(__('Email Address','majestic-support')); ?>"/>
                            </div>
                            <div class="userpopup-btn-wrp">
                                <input class="userpopup-search-btn" type="submit" value="<?php echo esc_html(__('Search','majestic-support')); ?>" />
                                <input class="userpopup-reset-btn" type="submit" onclick="document.getElementById('name').value = '';document.getElementById('username').value = ''; document.getElementById('emailaddress').value = '';" value="<?php echo esc_html(__('Reset','majestic-support')); ?>" />
                            </div>
                        </div>
                    </form>
                </div>
                <div id="userpopup-records-wrp">
                    <div id="userpopup-records">
                        <div class="userpopup-records-desc">
                            <?php echo esc_html(__('Use search feature to select the user','majestic-support')); ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php $MJTC_nonce_id = isset(majesticsupport::$_data[0]->id) ? majesticsupport::$_data[0]->id : ''; ?>
            <form class="msadmin-form majestic-support-form" method="post" action="<?php echo esc_url(wp_nonce_url(admin_url("admin.php?page=majesticsupport_ticket&task=saveticket"),"save-ticket-".$MJTC_nonce_id)); ?>" id="adminTicketform" enctype="multipart/form-data">
                <?php
                    $i = '';
                    $requiredTxt = '&nbsp;<span style="color: red;" >*</span>';
                    foreach (majesticsupport::$_data['fieldordering'] AS $field):
                        $readonlyclass = $field->readonly ? " mjtc-form-ticket-readonly " : "";
                        $msVisibleFunction = '';
                        if ($field->visible_field != null) {
                            $visibleparams = MJTC_includer::MJTC_getModel('fieldordering')->MJTC_getDataForVisibleField($field->visible_field);
                            if (!empty($visibleparams)) {
                                $jsObject = wp_json_encode($visibleparams);
                                $wpnonce = wp_create_nonce("is-field-required-".$field->visible_field);
                                $msVisibleFunction = " MJTC_getDataForVisibleField('".$wpnonce."', this.value, '".esc_js($field->visible_field)."', ".$jsObject.");";
                            }
                        }
                        switch ($field->field) {
                            case 'users':
                                if ($field->readonly == 1) {
                                    $style = 'display:none;';
                                } else {
                                    $style = '';
                                } ?>
                                <div class="mjtc-form-wrapper">
                                    <div class="mjtc-form-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($field->fieldtitle)); ?><?php if($field->required == 1) echo wp_kses($requiredTxt, MJTC_ALLOWED_TAGS); ?></div>
                                    <div class="mjtc-form-value">
                                        <?php if (isset(majesticsupport::$_data[0]->uid)) { ?>
                                            <input class="mjtc-form-diabled-field" type="text" id="username-text" value="<?php if(isset($MJTC_formdata['username-text'])) echo esc_attr($MJTC_formdata['username-text']); else echo esc_attr(majesticsupport::$_data[0]->name); ?>" readonly="readonly" placeholder="<?php echo esc_attr(majesticsupport::MJTC_getVarValue($field->placeholder)); ?>" <?php if($field->required == 1) echo 'data-validation="required"'; ?>/><div id="username-div"></div>
                                            <?php } else {
                                            ?>
                                            <input class="mjtc-form-diabled-field" type="text" value="<?php if(isset($MJTC_formdata['username-text'])) echo esc_attr($MJTC_formdata['username-text']); ?>" id="username-text" readonly="readonly" placeholder="<?php echo esc_attr(majesticsupport::MJTC_getVarValue($field->placeholder)); ?>" <?php if($field->required == 1) echo 'data-validation="required"'; ?>/><a style="<?php echo esc_attr($style); ?>" href="javascript:void(0);" id="userpopup" title="<?php echo esc_attr(__('Select User', 'majestic-support')); ?>"><?php echo esc_html(__('Select User', 'majestic-support')); ?></a><div id="username-div"></div>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                    <?php if(!empty($field->description)): ?>
                                        <div class="mjtc-form-description">
                                            <?php echo esc_html(majesticsupport::MJTC_getVarValue($field->description)); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php
                                break;
                            case 'email':
                                ?>
                                <div class="mjtc-form-wrapper">
                                    <div class="mjtc-form-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($field->fieldtitle)); ?>&nbsp;<span style="color: red;" >*</span></div>
                                    <div class="mjtc-form-value">
                                        <?php
                                            if(isset($MJTC_formdata['email'])) $email =  $MJTC_formdata['email'];
                                            elseif(isset(majesticsupport::$_data[0]->email)) $email = majesticsupport::$_data[0]->email;
                                            else $email = $field->defaultvalue; // Admin email not appear in form
                                            echo wp_kses(MJTC_formfield::MJTC_email('email', $email, array('class' => 'inputbox mjtc-form-input-field', 'data-validation' => ($field->required) ? 'required email' : 'email', 'data-validation-optional' => ($field->required) ? 'false' : 'true', 'onchange' => $msVisibleFunction, 'placeholder'=> majesticsupport::MJTC_getVarValue($field->placeholder)) + ($field->readonly ? ['readonly' => 'readonly'] : [])), MJTC_ALLOWED_TAGS);
                                        ?>
                                    </div>
                                    <?php if(!empty($field->description)): ?>
                                        <div class="mjtc-form-description">
                                            <?php echo esc_html(majesticsupport::MJTC_getVarValue($field->description)); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php
                                break;
                            case 'fullname':
                                ?>
                                <div class="mjtc-form-wrapper">
                                    <div class="mjtc-form-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($field->fieldtitle)); ?>&nbsp;<span style="color: red;" >*</span></div>
                                    <div class="mjtc-form-value">
                                        <?php
                                            if(isset($MJTC_formdata['name'])) $name = $MJTC_formdata['name'];
                                            elseif(isset(majesticsupport::$_data[0]->name)) $name = majesticsupport::$_data[0]->name;
                                            else $name = $field->defaultvalue; // Admin full name not appear in form
                                            echo wp_kses(MJTC_formfield::MJTC_text('name', $name, array('class' => 'inputbox mjtc-form-input-field', 'data-validation' => 'required', 'onchange' => $msVisibleFunction, 'placeholder'=> majesticsupport::MJTC_getVarValue($field->placeholder)) + ($field->readonly ? ['readonly' => 'readonly'] : [])), MJTC_ALLOWED_TAGS);
                                        ?>
                                    </div>
                                    <?php if(!empty($field->description)): ?>
                                        <div class="mjtc-form-description">
                                            <?php echo esc_html(majesticsupport::MJTC_getVarValue($field->description)); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php
                                break;
                            case 'phone':
                                ?>
                                <div class="mjtc-form-wrapper">
                                    <div class="mjtc-form-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($field->fieldtitle)); ?><?php if($field->required == 1) echo wp_kses($requiredTxt, MJTC_ALLOWED_TAGS); ?></div>
                                    <div class="mjtc-form-value">
                                        <?php
                                            if(isset($MJTC_formdata['phone'])) $phone = $MJTC_formdata['phone'];
                                            elseif(isset(majesticsupport::$_data[0]->phone)) $phone = majesticsupport::$_data[0]->phone;
                                            else $phone = $field->defaultvalue;
                                            echo wp_kses(MJTC_formfield::MJTC_text('phone', $phone, array('class' => 'inputbox mjtc-form-input-field','data-validation'=>($field->required) ? 'required':'', 'onchange' => $msVisibleFunction, 'placeholder'=> majesticsupport::MJTC_getVarValue($field->placeholder)) + ($field->readonly ? ['readonly' => 'readonly'] : [])), MJTC_ALLOWED_TAGS);
                                        ?>
                                    </div>
                                    <?php if(!empty($field->description)): ?>
                                        <div class="mjtc-form-description">
                                            <?php echo esc_html(majesticsupport::MJTC_getVarValue($field->description)); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php
                                break;
                            case 'phoneext':
                                ?>
                                <div class="mjtc-form-wrapper">
                                    <div class="mjtc-form-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($field->fieldtitle)); ?></div>
                                    <div class="mjtc-form-value">
                                        <?php
                                            if(isset($MJTC_formdata['phoneext'])) $phoneext = $MJTC_formdata['phoneext'];
                                            elseif(isset(majesticsupport::$_data[0]->phoneext)) $phoneext = majesticsupport::$_data[0]->phoneext;
                                            else $phoneext = $field->defaultvalue;
                                            echo wp_kses(MJTC_formfield::MJTC_text('phoneext', $phoneext, array('class' => 'inputbox mjtc-form-input-field', 'placeholder'=> majesticsupport::MJTC_getVarValue($field->placeholder)) + ($field->readonly ? ['readonly' => 'readonly'] : [])), MJTC_ALLOWED_TAGS);
                                        ?>
                                    </div>
                                    <?php if(!empty($field->description)): ?>
                                        <div class="mjtc-form-description">
                                            <?php echo esc_html(majesticsupport::MJTC_getVarValue($field->description)); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php
                                break;
                            case 'department':
                                ?>
                                <div class="mjtc-form-wrapper">
                                    <div class="mjtc-form-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($field->fieldtitle)); ?><?php if($field->required == 1) echo wp_kses($requiredTxt, MJTC_ALLOWED_TAGS); ?></div>
                                    <div class="mjtc-form-value">
                                        <?php
                                            if(isset($MJTC_formdata['departmentid'])){
                                                $MJTC_departmentid = $MJTC_formdata['departmentid'];
                                            } elseif(isset(majesticsupport::$_data[0]->departmentid)){
                                                $MJTC_departmentid = majesticsupport::$_data[0]->departmentid;
                                            } elseif(MJTC_request::MJTC_getVar('departmentid',0) > 0){
                                                $MJTC_departmentid = MJTC_request::MJTC_getVar('departmentid');
                                            } else{
                                                $MJTC_departmentid = MJTC_includer::MJTC_getModel('department')->getDefaultDepartmentID();
                                            }
                                            // code for visible field
                                            if ($field->visible_field != null && !isset(majesticsupport::$_data[0]->id)) {
                                                // For default function (initial value setting)
                                                if (!empty($visibleparams) && !isset(majesticsupport::$_data[0]->id)) {
                                                    $wpnonce = wp_create_nonce("is-field-required-" . $field->visible_field);
                                                    $jsObject = wp_json_encode($visibleparams);
                                                    // Build JS function without esc_js on JSON
                                                    $MJTC_defaultFunc = " MJTC_getDataForVisibleField('" . esc_js($wpnonce) . "', '" . esc_js($MJTC_departmentid) . "', '" . esc_js($field->visible_field) . "', " . $jsObject . ");";
                                                    // Attach default function on document ready
                                                    $majesticsupport_js = '
                                                        jQuery(document).ready(function(){
                                                            '.$MJTC_defaultFunc.'
                                                        });
                                                    ';
                                                    wp_add_inline_script('majestic-support-cmain-js', $majesticsupport_js);
                                                }
                                            }
                                            if(in_array('cannedresponses', majesticsupport::$_active_addons)){
                                                echo wp_kses(MJTC_formfield::MJTC_select('departmentid', MJTC_includer::MJTC_getModel('department')->getDepartmentForCombobox(), $MJTC_departmentid, esc_html(__('Select', 'majestic-support')).' '.esc_html($field->fieldtitle), array('class' => 'inputbox mjtc-form-select-field' . esc_attr($readonlyclass), 'onchange' => $msVisibleFunction.' getHelpTopicByDepartment(this.value);getPremadeByDepartment(this.value);', 'data-validation' => ($field->required) ? 'required':'') + ($field->readonly ? ['tabindex' => '-1'] : [])), MJTC_ALLOWED_TAGS);
                                            }else{
                                                echo wp_kses(MJTC_formfield::MJTC_select('departmentid', MJTC_includer::MJTC_getModel('department')->getDepartmentForCombobox(), $MJTC_departmentid, esc_html(__('Select', 'majestic-support')).' '.esc_html($field->fieldtitle), array('class' => 'inputbox mjtc-form-select-field' . esc_attr($readonlyclass), 'onchange' => $msVisibleFunction.' getHelpTopicByDepartment(this.value);', 'data-validation' => ($field->required) ? 'required':'') + ($field->readonly ? ['tabindex' => '-1'] : [])), MJTC_ALLOWED_TAGS);
                                            }
                                        ?>
                                    </div>
                                    <?php if(!empty($field->description)): ?>
                                        <div class="mjtc-form-description">
                                            <?php echo esc_html(majesticsupport::MJTC_getVarValue($field->description)); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php
                                break;
                            case 'helptopic':
                                if(!in_array('helptopic', majesticsupport::$_active_addons)){
                                    break;
                                }
                                ?>
                                <div class="mjtc-form-wrapper">
                                    <div class="mjtc-form-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($field->fieldtitle)); ?><?php if($field->required == 1) echo wp_kses($requiredTxt, MJTC_ALLOWED_TAGS); ?></div>
                                    <div class="mjtc-form-value" id="helptopic">
                                        <?php
                                            if(isset($MJTC_formdata['helptopicid'])) $helptopicid = $MJTC_formdata['helptopicid'];
                                            elseif(isset(majesticsupport::$_data[0]->helptopicid)) $helptopicid = majesticsupport::$_data[0]->helptopicid;
                                            elseif(MJTC_request::MJTC_getVar('helptopicid',0) > 0) $helptopicid = MJTC_request::MJTC_getVar('helptopicid');
                                            else $helptopicid = '';
                                            if (isset($MJTC_departmentid)) {
                                                $dep_id = $MJTC_departmentid;
                                            } else{
                                                $dep_id = 0;
                                            }
                                            echo wp_kses(MJTC_formfield::MJTC_select('helptopicid', MJTC_includer::MJTC_getModel('helptopic')->getHelpTopicsForCombobox($dep_id), $helptopicid, esc_html(__('Select', 'majestic-support')).' '.esc_html($field->fieldtitle), array('class' => 'inputbox mjtc-form-select-field' . esc_attr($readonlyclass),'data-validation'=>($field->required) ? 'required': '', 'onchange' => $msVisibleFunction) + ($field->readonly ? ['tabindex' => '-1'] : [])), MJTC_ALLOWED_TAGS);
                                        ?>
                                    </div>
                                    <?php if(!empty($field->description)): ?>
                                        <div class="mjtc-form-description">
                                            <?php echo esc_html(majesticsupport::MJTC_getVarValue($field->description)); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php
                                break;
                            case 'product':
                                ?>
                                <div class="mjtc-form-wrapper">
                                    <div class="mjtc-form-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($field->fieldtitle)); ?><?php if($field->required == 1) echo wp_kses($requiredTxt, MJTC_ALLOWED_TAGS); ?></div>
                                    <div class="mjtc-form-value" id="product">
                                        <?php
                                            if(isset($MJTC_formdata['productid'])) $productid = $MJTC_formdata['productid'];
                                            elseif(isset(majesticsupport::$_data[0]->productid)) $productid = majesticsupport::$_data[0]->productid;
                                            else $productid = '';
                                            echo wp_kses(MJTC_formfield::MJTC_select('productid', MJTC_includer::MJTC_getModel('product')->getProductForCombobox(), $productid, esc_html(__('Select', 'majestic-support')).' '.esc_html($field->fieldtitle), array('class' => 'inputbox mjtc-form-select-field' . esc_attr($readonlyclass),'data-validation'=>($field->required) ? 'required': '', 'onchange' => $msVisibleFunction) + ($field->readonly ? ['tabindex' => '-1'] : [])), MJTC_ALLOWED_TAGS);
                                        ?>
                                    </div>
                                    <?php if(!empty($field->description)): ?>
                                        <div class="mjtc-form-description">
                                            <?php echo esc_html(majesticsupport::MJTC_getVarValue($field->description)); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php
                                break;
                            case 'priority':
                                ?>
                                <div class="mjtc-form-wrapper">
                                    <div class="mjtc-form-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($field->fieldtitle)); ?><?php if($field->required == 1) echo wp_kses($requiredTxt, MJTC_ALLOWED_TAGS); ?></div>
                                    <div class="mjtc-form-value">
                                        <?php
                                            if(isset($MJTC_formdata['priorityid'])) $priorityid = $MJTC_formdata['priorityid'];
                                            elseif(isset(majesticsupport::$_data[0]->priorityid)) $priorityid = majesticsupport::$_data[0]->priorityid;
                                            else $priorityid = MJTC_includer::MJTC_getModel('priority')->getDefaultPriorityID();

                                            if (!empty($visibleparams) && !isset(majesticsupport::$_data[0]->id)) {
                                                $wpnonce = wp_create_nonce("is-field-required-" . $field->visible_field);
                                                $jsObject = wp_json_encode($visibleparams);
                                                // Build JS function without esc_js on JSON
                                                $MJTC_defaultFunc = "MJTC_getDataForVisibleField('" . esc_js($wpnonce) . "', '" . esc_js($priorityid) . "', '" . esc_js($field->visible_field) . "', " . $jsObject . ");";
                                                // Attach default function on document ready
                                                $majesticsupport_js = "
                                                    jQuery(document).ready(function(){
                                                        ".$MJTC_defaultFunc."
                                                    });
                                                ";
                                                wp_add_inline_script('majestic-support-cmain-js', $majesticsupport_js);
                                            }
                                            echo wp_kses(MJTC_formfield::MJTC_select('priorityid', MJTC_includer::MJTC_getModel('priority')->getPriorityForCombobox(), $priorityid, esc_html(__('Select', 'majestic-support')).' '.esc_html($field->fieldtitle), array('class' => 'inputbox mjtc-form-select-field' . esc_attr($readonlyclass), 'data-validation' => 'required', 'onchange' => $msVisibleFunction) + ($field->readonly ? ['tabindex' => '-1'] : [])), MJTC_ALLOWED_TAGS);
                                        ?>
                                    </div>
                                    <?php if(!empty($field->description)): ?>
                                        <div class="mjtc-form-description">
                                            <?php echo esc_html(majesticsupport::MJTC_getVarValue($field->description)); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php
                                break;
                                case 'internalnotetitle':
                                    if(!in_array('note', majesticsupport::$_active_addons)){
                                        break;
                                    }
                                        ?>
                                        <div class="mjtc-form-wrapper">
                                            <div class="mjtc-form-title">
                                                <a target="blank" href="#" class="mjtc-sprt-det-hdg-img mjtc-cp-video-internal-note">
                                                    <img title="<?php echo esc_attr(__('watch video','majestic-support')); ?>" alt="<?php echo esc_html(__('watch video','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL) ?>/includes/images/watch-video-icon.png" />
                                                </a>
                                                <?php echo esc_html(majesticsupport::MJTC_getVarValue($field->fieldtitle)); ?><?php if($field->required == 1) echo wp_kses($requiredTxt, MJTC_ALLOWED_TAGS); ?></div>
                                            <div class="mjtc-form-value">
                                                <?php
                                                    if(isset($MJTC_formdata['internalnotetitle'])) $internalnotetitle = $MJTC_formdata['internalnotetitle'];
                                                    else $internalnotetitle = $field->defaultvalue;
                                                    echo wp_kses(MJTC_formfield::MJTC_text('internalnotetitle', $internalnotetitle, array('class' => 'inputbox mjtc-form-input-field','data-validation'=>($field->required == 1) ? 'required': '', 'placeholder'=> majesticsupport::MJTC_getVarValue($field->placeholder)) + ($field->readonly ? ['readonly' => 'readonly'] : [])), MJTC_ALLOWED_TAGS);
                                                ?>
                                            </div>
                                            <?php if(!empty($field->description)): ?>
                                                <div class="mjtc-form-description">
                                                    <?php echo esc_html(majesticsupport::MJTC_getVarValue($field->description)); ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="mjtc-form-wrapper fullwidth">
                                            <div class="mjtc-form-title"><?php echo esc_html(__('Internal Note', 'majestic-support')); ?></div>
                                            <div class="mjtc-form-value">
                                                <?php if (isset(majesticsupport::$_data[0]->id)) { ?>
                                                    <div class="mjtc-form-title"><?php echo esc_html(__('Reason for edit', 'majestic-support')); ?><br></div>
                                                <?php } ?>
                                                <?php
                                                    if(isset($MJTC_formdata['internalnote'])) $internalnote = $MJTC_formdata['internalnote'];
                                                    elseif(isset(majesticsupport::$_data[0]->internalnote)) $internalnote = majesticsupport::$_data[0]->internalnote;
                                                    else $internalnote = '';
                                                    wp_editor($internalnote, 'internalnote', array('media_buttons' => false));
                                                ?>
                                            </div>
                                            <?php if(!empty($field->description)): ?>
                                                <div class="mjtc-form-description">
                                                    <?php echo esc_html(majesticsupport::MJTC_getVarValue($field->description)); ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <?php
                                    break;
                            case 'duedate':
                                // remove this from admin form
                                break;
                                ?>
                                <div class="mjtc-form-wrapper">
                                    <div class="mjtc-form-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($field->fieldtitle)); ?><?php if($field->required == 1) echo wp_kses($requiredTxt, MJTC_ALLOWED_TAGS); ?></div>
                                    <div class="mjtc-form-value">
                                        <?php
                                            if(isset($MJTC_formdata['duedate'])) $duedate = date_i18n(majesticsupport::$_config['date_format'], MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_formdata['duedate']));
                                            elseif(isset(majesticsupport::$_data[0]->duedate) && majesticsupport::$_data[0]->duedate != '0000-00-00 00:00:00'){
                                                $duedate = date_i18n(majesticsupport::$_config['date_format'], MJTC_majesticsupportphplib::MJTC_strtotime(majesticsupport::$_data[0]->duedate));
                                            }elseif(!empty($field->defaultvalue)){
                                                $duedate = date_i18n(majesticsupport::$_config['date_format'], MJTC_majesticsupportphplib::MJTC_strtotime($field->defaultvalue));
                                            }else $duedate = '';
                                            echo wp_kses(MJTC_formfield::MJTC_text('duedate', $duedate, array('class' => 'custom_date mjtc-form-date-field','data-validation'=>($field->required) ? 'required': '', 'placeholder'=> majesticsupport::MJTC_getVarValue($field->placeholder)) + ($field->readonly ? ['readonly' => 'readonly'] : [])), MJTC_ALLOWED_TAGS);
                                        ?>
                                    </div>
                                    <?php if(!empty($field->description)): ?>
                                        <div class="mjtc-form-description">
                                            <?php echo esc_html(majesticsupport::MJTC_getVarValue($field->description)); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php
                                break;
                            case 'status':
                                // remove this from admin form
                                break;
                                ?>
                                <div class="mjtc-form-wrapper">
                                    <div class="mjtc-form-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($field->fieldtitle)); ?><?php if($field->required == 1) echo wp_kses($requiredTxt, MJTC_ALLOWED_TAGS); ?></div>
                                    <div class="mjtc-form-value">
                                        <?php
                                            if(isset($MJTC_formdata['status'])) $status = $MJTC_formdata['status'];
                                            elseif(isset(majesticsupport::$_data[0]->status)) $status = majesticsupport::$_data[0]->status;
                                            else $status = '1';
                                            echo wp_kses(MJTC_formfield::MJTC_select('status', MJTC_includer::MJTC_getModel('status')->getStatusForCombobox(), $status, esc_html(__('Select Status', 'majestic-support')), array('class' => 'radiobutton mjtc-form-select-field' . esc_attr($readonlyclass),'data-validation'=>($field->required) ? 'required': '') + ($field->readonly ? ['tabindex' => '-1'] : [])), MJTC_ALLOWED_TAGS);
                                        ?>
                                    </div>
                                    <?php if(!empty($field->description)): ?>
                                        <div class="mjtc-form-description">
                                            <?php echo esc_html(majesticsupport::MJTC_getVarValue($field->description)); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php
                                break;
                            case 'assignto':
                                // remove this from admin form
                                break;
                                if (! in_array('agent',majesticsupport::$_active_addons)) {
                                    break;
                                }
                                ?>
                                <div class="mjtc-form-wrapper">
                                    <div class="mjtc-form-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($field->fieldtitle)); ?><?php if($field->required == 1) echo wp_kses($requiredTxt, MJTC_ALLOWED_TAGS); ?></div>
                                    <div class="mjtc-form-value">
                                        <?php
                                            if(isset($MJTC_formdata['staffid'])) $staffid = $MJTC_formdata['staffid'];
                                            elseif(isset(majesticsupport::$_data[0]->staffid)) $staffid = majesticsupport::$_data[0]->staffid;
                                            else $staffid = '';
                                            echo wp_kses(MJTC_formfield::MJTC_select('staffid', MJTC_includer::MJTC_getModel('agent')->getStaffForCombobox(), $staffid, esc_html(__('Select Agent', 'majestic-support')), array('class' => 'inputbox mjtc-form-select-field' . esc_attr($readonlyclass),'data-validation'=>($field->required) ? 'required': '') + ($field->readonly ? ['tabindex' => '-1'] : [])), MJTC_ALLOWED_TAGS);
                                        ?>
                                    </div>
                                    <?php if(!empty($field->description)): ?>
                                        <div class="mjtc-form-description">
                                            <?php echo esc_html(majesticsupport::MJTC_getVarValue($field->description)); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php
                                break;
                            case 'subject':
                                ?>
                                <div class="mjtc-form-wrapper">
                                    <div class="mjtc-form-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($field->fieldtitle)); ?>&nbsp;<span style="color: red;" >*</span></div>
                                    <div class="mjtc-form-value">
                                        <?php
                                            if(isset($MJTC_formdata['subject'])) $MJTC_subject = $MJTC_formdata['subject'];
                                            elseif(isset(majesticsupport::$_data[0]->subject)) $MJTC_subject = majesticsupport::$_data[0]->subject;
                                            else $MJTC_subject = $field->defaultvalue;
                                            echo wp_kses(MJTC_formfield::MJTC_text('subject', $MJTC_subject, array('class' => 'inputbox mjtc-form-input-field', 'data-validation' => 'required','style'=>'width:100%;', 'onchange' => $msVisibleFunction, 'placeholder'=> majesticsupport::MJTC_getVarValue($field->placeholder)) + ($field->readonly ? ['readonly' => 'readonly'] : [])), MJTC_ALLOWED_TAGS);
                                        ?>
                                    </div>
                                    <?php if(!empty($field->description)): ?>
                                        <div class="mjtc-form-description">
                                            <?php echo esc_html(majesticsupport::MJTC_getVarValue($field->description)); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php
                                break;
                            case 'premade':
                                if(!in_array('cannedresponses', majesticsupport::$_active_addons)){
                                    break;
                                }
                                $text = MJTC_includer::MJTC_getModel('cannedresponses')->getPreMadeMessageForCombobox();
                                ?>
                                <div class="mjtc-form-wrapper fullwidth">
                                    <div class="mjtc-form-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($field->fieldtitle)); ?></div>
                                    <div class="mjtc-form-value mjtc-form-premade-wrp">
                                        <div class="mjtc-form-append">
                                            <?php echo wp_kses(MJTC_formfield::MJTC_checkbox('append', array('1' => esc_html(__('Append', 'majestic-support'))), '', array('class' => 'radiobutton mjtc-form-radio-field')), MJTC_ALLOWED_TAGS); ?>
                                        </div>
                                        <div id="premade">
                                            <?php
                                                foreach($text as $premade){
                                                    ?>
                                                    <div class="mjtc-form-perm-msg" onclick="getpremade(<?php echo esc_js($premade->id); ?>);">
                                                        <a href="javascript:void(0)" title="<?php echo esc_attr(__('premade','majestic-support')); ?>"><?php echo esc_html($premade->text); ?></a>
                                                    </div>
                                                    <?php
                                                }
                                                ?>
                                        </div>
                                    </div>
                                    <?php if(!empty($field->description)): ?>
                                        <div class="mjtc-form-description">
                                            <?php echo esc_html(majesticsupport::MJTC_getVarValue($field->description)); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php
                                break;
                            case 'issuesummary':
                                ?>
                                <div class="mjtc-form-wrapper fullwidth">
                                    <div class="mjtc-form-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($field->fieldtitle)); ?><?php if($field->required == 1) echo wp_kses($requiredTxt, MJTC_ALLOWED_TAGS); ?></div>
                                    <div class="mjtc-form-value">
                                        <?php
                                            if(isset($MJTC_formdata['message'])) $message = wpautop(wptexturize(MJTC_majesticsupportphplib::MJTC_stripslashes($MJTC_formdata['message'])));
                                            elseif(isset(majesticsupport::$_data[0]->message)) $message = majesticsupport::$_data[0]->message;
                                            else $message = $field->defaultvalue;
                                            if ($field->readonly) {
                                                echo wp_kses(MJTC_formfield::MJTC_textarea('mjsupport_message', $message, array('class' => 'inputbox mjtc-form-textarea-field one', 'rows' => 5, 'cols' => 25, 'placeholder'=> majesticsupport::MJTC_getVarValue($field->placeholder), 'readonly'=> 'readonly')), MJTC_ALLOWED_TAGS);
                                            } else {
                                                wp_editor($message, 'mjsupport_message', array('media_buttons' => false));
                                            }
                                        ?>
                                    </div>
                                    <?php if(!empty($field->description)): ?>
                                        <div class="mjtc-form-description">
                                            <?php echo esc_html(majesticsupport::MJTC_getVarValue($field->description)); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php
                                break;

                            case 'wcorderid':
                                if(!in_array('woocommerce', majesticsupport::$_active_addons)){
                                    break;
                                }
                                if(!class_exists('WooCommerce')){
                                    break;
                                }
                                ?>
                                <div class="mjtc-form-wrapper">
                                    <div class="mjtc-form-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($field->fieldtitle)); ?><?php if($field->required == 1) echo wp_kses($requiredTxt, MJTC_ALLOWED_TAGS); ?></div>
                                    <div class="mjtc-form-value">
                                        <?php
                                        if(isset($MJTC_formdata['wcorderid'])) $wcorderid = $MJTC_formdata['wcorderid'];
                                        elseif(isset(majesticsupport::$_data[0]->wcorderid)) $wcorderid = majesticsupport::$_data[0]->wcorderid;
                                        else $wcorderid = $field->defaultvalue;
                                        echo wp_kses(MJTC_formfield::MJTC_text('wcorderid', $wcorderid, array('class' => 'inputbox mjtc-form-input-field', 'data-validation' => ($field->required == 1) ? 'required' : '','style'=>'width:100%;', 'placeholder'=> majesticsupport::MJTC_getVarValue($field->placeholder)) + ($field->readonly ? ['readonly' => 'readonly'] : [])), MJTC_ALLOWED_TAGS); ?>
                                        <span class="ms_product_found" title="<?php echo esc_attr(__("Order id found",'majestic-support')); ?>" style="display: none;"></span>
                                        <span class="ms_product_not_found" title="<?php echo esc_attr(__("Order id not found",'majestic-support')); ?>" style="display: none;"></span>
                                    </div>
                                    <?php if(!empty($field->description)): ?>
                                        <div class="mjtc-form-description">
                                            <?php echo esc_html(majesticsupport::MJTC_getVarValue($field->description)); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php
                                break;
                            case 'wcproductid':
                                if(!in_array('woocommerce', majesticsupport::$_active_addons)){
                                    break;
                                }
                                if(!class_exists('WooCommerce')){
                                    break;
                                }
                                 ?>
                                <div class="mjtc-form-wrapper">
                                    <div class="mjtc-form-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($field->fieldtitle)); ?><?php if($field->required == 1) echo wp_kses($requiredTxt, MJTC_ALLOWED_TAGS); ?></div>
                                    <div class="mjtc-form-value" id="wcproductid-wrap">
                                        <?php
                                            $itemlist = array();
                                            if(isset($MJTC_formdata['wcproductid'])) $wcproductid = $MJTC_formdata['wcproductid'];
                                            elseif(isset(majesticsupport::$_data[0]->wcproductid)) $wcproductid = majesticsupport::$_data[0]->wcproductid;
                                            else $wcproductid = '';
                                            echo wp_kses(MJTC_formfield::MJTC_select('wcproductid', $itemlist, $wcproductid, esc_html(__('Select', 'majestic-support')).' '.esc_html($field->fieldtitle), array('class' => 'inputbox mjtc-form-select-field' . esc_attr($readonlyclass)) + ($field->readonly ? ['tabindex' => '-1'] : [])), MJTC_ALLOWED_TAGS); ?>
                                    </div>
                                    <?php if(!empty($field->description)): ?>
                                        <div class="mjtc-form-description">
                                            <?php echo esc_html(majesticsupport::MJTC_getVarValue($field->description)); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php
                                break;
                            case 'eddorderid':
                                if(!in_array('easydigitaldownloads', majesticsupport::$_active_addons)){
                                    break;
                                }
                                if(!class_exists('Easy_Digital_Downloads')){
                                    break;
                                }
                                $itemlist = array();

                                if(isset($MJTC_formdata['eddorderid'])) $eddorderid = $MJTC_formdata['eddorderid'];
                                elseif(isset(majesticsupport::$_data[0]->eddorderid)) $eddorderid = majesticsupport::$_data[0]->eddorderid;
                                elseif(isset(majesticsupport::$_data['edd_order_id'])) $eddorderid = majesticsupport::$_data['edd_order_id'];
                                else $eddorderid = '';
                                    $blogusers = get_users( array( 'fields' => array( 'ID' ) ) );
                                    $user_purchase_array = array();
                                    foreach ($blogusers AS $b_user) {
                                        $user_purchases = edd_get_users_purchases($b_user->ID);
                                        if($user_purchases){
                                            foreach ($user_purchases AS $user_purchase) {
                                                $user_purchase_array[] = (object) array('id' => $user_purchase->ID, 'text' => '#'.esc_html($user_purchase->ID).'&nbsp;('. esc_html(__('Dated','majestic-support')).':&nbsp;' .date_i18n(majesticsupport::$_config['date_format'], MJTC_majesticsupportphplib::MJTC_strtotime($user_purchase->post_date)).')');
                                            }
                                        }
                                    }
                                     ?>
                                    <div class="mjtc-form-wrapper">
                                        <div class="mjtc-form-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($field->fieldtitle)); ?><?php if($field->required == 1) echo wp_kses($requiredTxt, MJTC_ALLOWED_TAGS); ?></div>
                                        <div class="mjtc-form-value" id="eddorderid-wrap">
                                            <?php echo wp_kses(MJTC_formfield::MJTC_select('eddorderid', $user_purchase_array, $eddorderid, esc_html(__('Select', 'majestic-support')).' '.esc_html($field->fieldtitle), array('class' => 'inputbox mjtc-form-select-field' . esc_attr($readonlyclass)) + ($field->readonly ? ['tabindex' => '-1'] : [])), MJTC_ALLOWED_TAGS); ?>
                                        </div>
                                        <?php if(!empty($field->description)): ?>
                                            <div class="mjtc-form-description">
                                                <?php echo esc_html(majesticsupport::MJTC_getVarValue($field->description)); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php
                                break;
                            case 'eddproductid':
                                if(!in_array('easydigitaldownloads', majesticsupport::$_active_addons)){
                                    break;
                                }
                                if(!class_exists('Easy_Digital_Downloads')){
                                    break;
                                }

                                $order_products_array = array();
                                if($eddorderid != '' && is_numeric($eddorderid)){
                                    $order_products = edd_get_payment_meta_cart_details($eddorderid);
                                    foreach ($order_products as $order_product) {
                                        $order_products_array[] = (object) array('id'=>$order_product['id'], 'text'=>$order_product['name']);
                                    }
                                }

                                if(isset($MJTC_formdata['eddproductid'])) $eddproductid = $MJTC_formdata['eddproductid'];
                                elseif(isset(majesticsupport::$_data[0]->eddproductid)) $eddproductid = majesticsupport::$_data[0]->eddproductid;
                                else $eddproductid = '';  ?>
                                <div class="mjtc-form-wrapper">
                                    <div class="mjtc-form-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($field->fieldtitle)); ?><?php if($field->required == 1) echo wp_kses($requiredTxt, MJTC_ALLOWED_TAGS); ?></div>
                                    <div class="mjtc-form-value" id="eddproductid-wrap">
                                        <?php echo wp_kses(MJTC_formfield::MJTC_select('eddproductid', $order_products_array, $eddproductid, esc_html(__('Select', 'majestic-support')).' '.esc_html($field->fieldtitle), array('class' => 'inputbox mjtc-form-select-field' . esc_attr($readonlyclass)) + ($field->readonly ? ['tabindex' => '-1'] : [])), MJTC_ALLOWED_TAGS); ?>
                                    </div>
                                    <?php if(!empty($field->description)): ?>
                                        <div class="mjtc-form-description">
                                            <?php echo esc_html(majesticsupport::MJTC_getVarValue($field->description)); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php
                                break;
                            case 'eddlicensekey':
                                if(!in_array('easydigitaldownloads', majesticsupport::$_active_addons)){
                                    break;
                                }
                                if(!class_exists('Easy_Digital_Downloads')){
                                    break;
                                }
                                if(!class_exists('EDD_Software_Licensing')){
                                    break;
                                }

                                $license_key_array = array();
                                if($eddorderid != '' && is_numeric($eddorderid)){
                                    $license = EDD_Software_Licensing::instance();
                                    $result = $license->get_licenses_of_purchase($eddorderid);
                                    if($result){
                                        foreach ($result AS $license_record) {
                                            $license_record_licensekey = $license->get_license_key($license_record->ID);
                                            if($license_record_licensekey != ''){
                                                $license_key_array[] = (object) array('id' => $license_record_licensekey,'text' => $license_record_licensekey);
                                            }
                                        }
                                    }
                                }

                                $itemlist = array();
                                if(isset($MJTC_formdata['eddlicensekey'])) $eddlicensekey = $MJTC_formdata['eddlicensekey'];
                                elseif(isset(majesticsupport::$_data[0]->eddlicensekey)) $eddlicensekey = majesticsupport::$_data[0]->eddlicensekey;
                                else $eddlicensekey = '';
                                ?>
                                <div class="mjtc-form-wrapper">
                                    <div class="mjtc-form-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($field->fieldtitle));if($field->required == 1) echo wp_kses($requiredTxt, MJTC_ALLOWED_TAGS); ?></div>
                                    <div class="mjtc-form-value" id="eddlicensekey-wrap">
                                        <?php echo wp_kses(MJTC_formfield::MJTC_select('eddlicensekey', $license_key_array, $eddlicensekey, esc_html(__('Select', 'majestic-support')).' '.esc_html($field->fieldtitle), array('class' => 'inputbox mjtc-form-select-field' . esc_attr($readonlyclass)) + ($field->readonly ? ['tabindex' => '-1'] : [])), MJTC_ALLOWED_TAGS); ?>
                                    </div>
                                    <?php if(!empty($field->description)): ?>
                                        <div class="mjtc-form-description">
                                            <?php echo esc_html(majesticsupport::MJTC_getVarValue($field->description)); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php

                                break;
                            case 'attachments':
                                ?>
                                <div class="mjtc-form-wrapper fullwidth">
                                    <div class="mjtc-form-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($field->fieldtitle)); ?><?php if($field->required == 1) echo wp_kses($requiredTxt, MJTC_ALLOWED_TAGS); ?></div>
                                    <?php
                                    if(isset(majesticsupport::$_data[5]) && count(majesticsupport::$_data[5]) > 0){
                                        $attachmentreq = '';
                                    }else{
                                        $attachmentreq = $field->required == 1 ? 'required' : '';
                                    }
                                    ?>
                                    <div class="mjtc-form-value">
                                        <div class="tk_attachment_value_wrapperform">
                                            <span class="tk_attachment_value_text">
                                                <input type="file" class="inputbox" name="filename[]" onchange="MJTC_uploadfile(this, '<?php echo esc_js(majesticsupport::$_config['file_maximum_size']); ?>', '<?php echo esc_js(majesticsupport::$_config['file_extension']); ?>');" size="20" maxlenght='30' data-validation="<?php echo esc_attr($attachmentreq); ?>" />
                                                <span class='tk_attachment_remove'></span>
                                            </span>
                                        </div>
                                        <div class="tk_attachments_desc">
                                            <span class="tk_attachments_configform">
                                                <small>
                                                    <?php
                                                    $tktdata = esc_html(__('Maximum File Size', 'majestic-support')).' (' . esc_html(majesticsupport::$_config['file_maximum_size']).'KB)<br>'.esc_html(__('File Extension Type', 'majestic-support')).' (' . esc_html(majesticsupport::$_config['file_extension']) . ')';
                                                    echo wp_kses($tktdata, MJTC_ALLOWED_TAGS);
                                                    ?>
                                                </small>
                                            </span>
                                            <span id="tk_attachment_add" class="tk_attachments_addform ms-button-link ms-button-bg-link"><?php echo esc_html(__('Add More Files', 'majestic-support')); ?></span>
                                        </div>
                                        <?php
                                        if (!empty(majesticsupport::$_data[5])) {
                                            foreach (majesticsupport::$_data[5] AS $attachment) {
                                                $attachmentid = isset(majesticsupport::$_data[0]->id) ? majesticsupport::$_data[0]->id : '';
                                                echo wp_kses('
                                                    <div class="mjtc_supportattachment">
                                                            ' . esc_html($attachment->filename) . '
                                                            <a title="'. esc_html(__('Delete','majestic-support')).'" href="' . esc_url(wp_nonce_url('?page=majesticsupport_attachment&task=deleteattachment&action=mstask&id=' . $attachment->id . '&ticketid=' . $attachmentid, 'delete-attachement-'.$attachment->id)) . '"><img alt="'. esc_html(__('Delete','majestic-support')).'" src="'.esc_url(MJTC_PLUGIN_URL).'includes/images/delete.png" /></a>
                                                    </div>', MJTC_ALLOWED_TAGS);
                                            }
                                        }
                                        ?>
                                    </div>
                                    <?php if(!empty($field->description)): ?>
                                        <div class="mjtc-form-description">
                                            <?php echo esc_html(majesticsupport::MJTC_getVarValue($field->description)); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php
                                break;
                            case 'envatopurchasecode':
                                if(!in_array('envatovalidation', majesticsupport::$_active_addons)){
                                    break;
                                }
                                if(!empty(majesticsupport::$_data[0]->envatodata)){
                                    $envlicense = json_decode(majesticsupport::$_data[0]->envatodata, true);
                                }else{
                                    $envlicense = array();
                                }
                                ?>
                                <div class="mjtc-form-wrapper">
                                    <div class="mjtc-form-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($field->fieldtitle)); ?><?php if($field->required == 1) echo wp_kses($requiredTxt, MJTC_ALLOWED_TAGS); ?></div>
                                    <div class="mjtc-form-field mjtc-form-value">
                                        <?php
                                        if(isset($MJTC_formdata['envatopurchasecode'])) $envatopurchasecode = $MJTC_formdata['envatopurchasecode'];
                                        elseif(isset($envlicense['license'])) $envatopurchasecode = $envlicense['license'];
                                        else $envatopurchasecode = $field->defaultvalue;
                                        echo wp_kses(MJTC_formfield::MJTC_text('envatopurchasecode', $envatopurchasecode, array('class' => 'inputbox inputbox mjtc-form-input-field', 'data-validation' => ($field->required ? 'required' : ''), 'placeholder'=> majesticsupport::MJTC_getVarValue($field->placeholder)) + ($field->readonly ? ['readonly' => 'readonly'] : [])), MJTC_ALLOWED_TAGS);
                                        echo wp_kses(MJTC_formfield::MJTC_hidden('prev_envatopurchasecode', $envatopurchasecode), MJTC_ALLOWED_TAGS);
                                        ?>
                                    </div>
                                </div>
                                <?php
                                break;
                            default:
                                $customfields = MJTC_includer::MJTC_getObjectClass('customfields')->MJTC_formCustomFields($field);
                                if (isset($customfields)) {
                                    echo wp_kses($customfields, MJTC_ALLOWED_TAGS);
                                }
                                break;
                        }
                    endforeach;
                    echo wp_kses('<input type="hidden" id="userfeilds_total" name="userfeilds_total"  value="' . esc_attr($i) . '"  />', MJTC_ALLOWED_TAGS);
                ?>
                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('id', isset(majesticsupport::$_data[0]->id) ? majesticsupport::$_data[0]->id : ''), MJTC_ALLOWED_TAGS) ?>
                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('multiformid', isset(majesticsupport::$_data['formid']) ? majesticsupport::$_data['formid'] : '1'), MJTC_ALLOWED_TAGS); ?>
                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('attachmentdir', isset(majesticsupport::$_data[0]->attachmentdir) ? majesticsupport::$_data[0]->attachmentdir : ''), MJTC_ALLOWED_TAGS); ?>
                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('ticketid', isset(majesticsupport::$_data[0]->ticketid) ? majesticsupport::$_data[0]->ticketid : ''), MJTC_ALLOWED_TAGS); ?>
                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('created', isset(majesticsupport::$_data[0]->created) ? majesticsupport::$_data[0]->created : ''), MJTC_ALLOWED_TAGS); ?>
                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('lastreply', isset(majesticsupport::$_data[0]->lastreply) ? majesticsupport::$_data[0]->lastreply : ''), MJTC_ALLOWED_TAGS); ?>
                <?php
                    if (isset(majesticsupport::$_data[0]->uid))
                        $uid = majesticsupport::$_data[0]->uid;
                    else
                        $uid = get_current_user_id();
                    echo wp_kses(MJTC_formfield::MJTC_hidden('uid', $uid), MJTC_ALLOWED_TAGS);
                ?>
                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('updated', isset(majesticsupport::$_data[0]->updated) ? majesticsupport::$_data[0]->updated : '' ), MJTC_ALLOWED_TAGS); ?>
                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('action', 'ticket_saveticket'), MJTC_ALLOWED_TAGS); ?>
                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('form_request', 'majesticsupport'), MJTC_ALLOWED_TAGS); ?>
                <div class="mjtc-form-button">
                    <?php echo wp_kses(MJTC_formfield::MJTC_submitbutton('save', esc_html(__('Submit Ticket', 'majestic-support')), array('class' => 'button mjtc-form-save')), MJTC_ALLOWED_TAGS); ?>
                </div>
            </form>
        </div>
    </div>
</div>
