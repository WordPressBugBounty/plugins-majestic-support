<?php
    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

    wp_enqueue_script('jquery-ui-datepicker');
    wp_enqueue_script('majesticsupport-file_validate.js', MJTC_PLUGIN_URL . 'includes/js/file_validate.js', array(), '1.0.0', true);
    wp_enqueue_style('majesticsupport-jquery-ui-css', MJTC_PLUGIN_URL . 'includes/css/jquery-ui-smoothness.css', array(), '1.0.0');
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
                        <?php echo esc_html(__('Select User','majestic-support')); ?>
                    </div>
                    <div class="multiformpopup-header-close-img">
                        <img alt="<?php echo esc_attr(__('Close','majestic-support')); ?>" class="userpopup-close" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/close-icon-white.png" />
                    </div>
                </div>
                <div class="userpopup-search">
                    <form id="userpopupsearch">
                        <div class="userpopup-fields-wrp">
                            <div class="userpopup-fields">
                                <input type="text" name="username" id="username" placeholder="<?php echo esc_attr(__('Username','majestic-support')); ?>" />
                            </div>
                            <div class="userpopup-fields">
                                <input type="text" name="name" id="name" placeholder="<?php echo esc_attr(__('Name','majestic-support')); ?>" />
                            </div>
                            <div class="userpopup-fields">
                                <input type="text" name="emailaddress" id="emailaddress" placeholder="<?php echo esc_attr(__('Email Address','majestic-support')); ?>"/>
                            </div>
                            <div class="userpopup-btn-wrp">
                                <input class="userpopup-search-btn" type="submit" value="<?php echo esc_attr(__('Search','majestic-support')); ?>" />
                                <input class="userpopup-reset-btn" type="submit" onclick="document.getElementById('name').value = '';document.getElementById('username').value = ''; document.getElementById('emailaddress').value = '';" value="<?php echo esc_attr(__('Reset','majestic-support')); ?>" />
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
                    $MJTC_i = '';
                    $MJTC_requiredTxt = '&nbsp;<span style="color: red;" >*</span>';
                    foreach (majesticsupport::$_data['fieldordering'] AS $MJTC_field):
                        $MJTC_readonlyclass = $MJTC_field->readonly ? " mjtc-form-ticket-readonly " : "";
                        $MJTC_VisibleFunction = '';
                        if ($MJTC_field->visible_field != null) {
                            $MJTC_visibleparams = MJTC_includer::MJTC_getModel('fieldordering')->MJTC_getDataForVisibleField($MJTC_field->visible_field);
                            if (!empty($MJTC_visibleparams)) {
                                $MJTC_jsObject = wp_json_encode($MJTC_visibleparams);
                                $MJTC_wpnonce = wp_create_nonce("is-field-required-".$MJTC_field->visible_field);
                                $MJTC_VisibleFunction = " MJTC_getDataForVisibleField('".$MJTC_wpnonce."', this.value, '".esc_js($MJTC_field->visible_field)."', ".$MJTC_jsObject.");";
                            }
                        }
                        switch ($MJTC_field->field) {
                            case 'users':
                                if ($MJTC_field->readonly == 1) {
                                    $MJTC_style = 'display:none;';
                                } else {
                                    $MJTC_style = '';
                                } ?>
                                <div class="mjtc-form-wrapper">
                                    <div class="mjtc-form-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->fieldtitle)); ?><?php if($MJTC_field->required == 1) echo wp_kses($MJTC_requiredTxt, MJTC_ALLOWED_TAGS); ?></div>
                                    <div class="mjtc-form-value">
                                        <?php if (isset(majesticsupport::$_data[0]->uid)) { ?>
                                            <input class="mjtc-form-diabled-field" type="text" id="username-text" value="<?php if(isset($MJTC_formdata['username-text'])) echo esc_attr($MJTC_formdata['username-text']); else echo esc_attr(majesticsupport::$_data[0]->name); ?>" readonly="readonly" placeholder="<?php echo esc_attr(majesticsupport::MJTC_getVarValue($MJTC_field->placeholder)); ?>" <?php if($MJTC_field->required == 1) echo 'data-validation="required"'; ?>/><div id="username-div"></div>
                                            <?php } else {
                                            ?>
                                            <input class="mjtc-form-diabled-field" type="text" value="<?php if(isset($MJTC_formdata['username-text'])) echo esc_attr($MJTC_formdata['username-text']); ?>" id="username-text" readonly="readonly" placeholder="<?php echo esc_attr(majesticsupport::MJTC_getVarValue($MJTC_field->placeholder)); ?>" <?php if($MJTC_field->required == 1) echo 'data-validation="required"'; ?>/><a style="<?php echo esc_attr($MJTC_style); ?>" href="javascript:void(0);" id="userpopup" title="<?php echo esc_attr(__('Select User', 'majestic-support')); ?>"><?php echo esc_html(__('Select User', 'majestic-support')); ?></a><div id="username-div"></div>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                    <?php if(!empty($MJTC_field->description)): ?>
                                        <div class="mjtc-form-description">
                                            <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->description)); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php
                                break;
                            case 'email':
                                ?>
                                <div class="mjtc-form-wrapper">
                                    <div class="mjtc-form-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->fieldtitle)); ?>&nbsp;<span style="color: red;" >*</span></div>
                                    <div class="mjtc-form-value">
                                        <?php
                                            if(isset($MJTC_formdata['email'])) $MJTC_email =  $MJTC_formdata['email'];
                                            elseif(isset(majesticsupport::$_data[0]->email)) $MJTC_email = majesticsupport::$_data[0]->email;
                                            else $MJTC_email = $MJTC_field->defaultvalue; // Admin email not appear in form
                                            echo wp_kses(MJTC_formfield::MJTC_email('email', $MJTC_email, array('class' => 'inputbox mjtc-form-input-field', 'data-validation' => ($MJTC_field->required) ? 'required email' : 'email', 'data-validation-optional' => ($MJTC_field->required) ? 'false' : 'true', 'onchange' => $MJTC_VisibleFunction, 'placeholder'=> majesticsupport::MJTC_getVarValue($MJTC_field->placeholder)) + ($MJTC_field->readonly ? ['readonly' => 'readonly'] : [])), MJTC_ALLOWED_TAGS);
                                        ?>
                                    </div>
                                    <?php if(!empty($MJTC_field->description)): ?>
                                        <div class="mjtc-form-description">
                                            <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->description)); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php
                                break;
                            case 'fullname':
                                ?>
                                <div class="mjtc-form-wrapper">
                                    <div class="mjtc-form-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->fieldtitle)); ?>&nbsp;<span style="color: red;" >*</span></div>
                                    <div class="mjtc-form-value">
                                        <?php
                                            if(isset($MJTC_formdata['name'])) $MJTC_name = $MJTC_formdata['name'];
                                            elseif(isset(majesticsupport::$_data[0]->name)) $MJTC_name = majesticsupport::$_data[0]->name;
                                            else $MJTC_name = $MJTC_field->defaultvalue; // Admin full name not appear in form
                                            echo wp_kses(MJTC_formfield::MJTC_text('name', $MJTC_name, array('class' => 'inputbox mjtc-form-input-field', 'data-validation' => 'required', 'onchange' => $MJTC_VisibleFunction, 'placeholder'=> majesticsupport::MJTC_getVarValue($MJTC_field->placeholder)) + ($MJTC_field->readonly ? ['readonly' => 'readonly'] : [])), MJTC_ALLOWED_TAGS);
                                        ?>
                                    </div>
                                    <?php if(!empty($MJTC_field->description)): ?>
                                        <div class="mjtc-form-description">
                                            <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->description)); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php
                                break;
                            case 'phone':
                                ?>
                                <div class="mjtc-form-wrapper">
                                    <div class="mjtc-form-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->fieldtitle)); ?><?php if($MJTC_field->required == 1) echo wp_kses($MJTC_requiredTxt, MJTC_ALLOWED_TAGS); ?></div>
                                    <div class="mjtc-form-value">
                                        <?php
                                            if(isset($MJTC_formdata['phone'])) $MJTC_phone = $MJTC_formdata['phone'];
                                            elseif(isset(majesticsupport::$_data[0]->phone)) $MJTC_phone = majesticsupport::$_data[0]->phone;
                                            else $MJTC_phone = $MJTC_field->defaultvalue;
                                            echo wp_kses(MJTC_formfield::MJTC_text('phone', $MJTC_phone, array('class' => 'inputbox mjtc-form-input-field','data-validation'=>($MJTC_field->required) ? 'required':'', 'onchange' => $MJTC_VisibleFunction, 'placeholder'=> majesticsupport::MJTC_getVarValue($MJTC_field->placeholder)) + ($MJTC_field->readonly ? ['readonly' => 'readonly'] : [])), MJTC_ALLOWED_TAGS);
                                        ?>
                                    </div>
                                    <?php if(!empty($MJTC_field->description)): ?>
                                        <div class="mjtc-form-description">
                                            <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->description)); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php
                                break;
                            case 'phoneext':
                                ?>
                                <div class="mjtc-form-wrapper">
                                    <div class="mjtc-form-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->fieldtitle)); ?></div>
                                    <div class="mjtc-form-value">
                                        <?php
                                            if(isset($MJTC_formdata['phoneext'])) $MJTC_phoneext = $MJTC_formdata['phoneext'];
                                            elseif(isset(majesticsupport::$_data[0]->phoneext)) $MJTC_phoneext = majesticsupport::$_data[0]->phoneext;
                                            else $MJTC_phoneext = $MJTC_field->defaultvalue;
                                            echo wp_kses(MJTC_formfield::MJTC_text('phoneext', $MJTC_phoneext, array('class' => 'inputbox mjtc-form-input-field', 'placeholder'=> majesticsupport::MJTC_getVarValue($MJTC_field->placeholder)) + ($MJTC_field->readonly ? ['readonly' => 'readonly'] : [])), MJTC_ALLOWED_TAGS);
                                        ?>
                                    </div>
                                    <?php if(!empty($MJTC_field->description)): ?>
                                        <div class="mjtc-form-description">
                                            <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->description)); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php
                                break;
                            case 'department':
                                ?>
                                <div class="mjtc-form-wrapper">
                                    <div class="mjtc-form-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->fieldtitle)); ?><?php if($MJTC_field->required == 1) echo wp_kses($MJTC_requiredTxt, MJTC_ALLOWED_TAGS); ?></div>
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
                                            if ($MJTC_field->visible_field != null && !isset(majesticsupport::$_data[0]->id)) {
                                                // For default function (initial value setting)
                                                if (!empty($MJTC_visibleparams) && !isset(majesticsupport::$_data[0]->id)) {
                                                    $MJTC_wpnonce = wp_create_nonce("is-field-required-" . $MJTC_field->visible_field);
                                                    $MJTC_jsObject = wp_json_encode($MJTC_visibleparams);
                                                    // Build JS function without esc_js on JSON
                                                    $MJTC_defaultFunc = " MJTC_getDataForVisibleField('" . esc_js($MJTC_wpnonce) . "', '" . esc_js($MJTC_departmentid) . "', '" . esc_js($MJTC_field->visible_field) . "', " . $MJTC_jsObject . ");";
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
                                                echo wp_kses(MJTC_formfield::MJTC_select('departmentid', MJTC_includer::MJTC_getModel('department')->getDepartmentForCombobox(), $MJTC_departmentid, esc_html(__('Select', 'majestic-support')).' '.esc_html($MJTC_field->fieldtitle), array('class' => 'inputbox mjtc-form-select-field' . esc_attr($MJTC_readonlyclass), 'onchange' => $MJTC_VisibleFunction.' getHelpTopicByDepartment(this.value);getPremadeByDepartment(this.value);', 'data-validation' => ($MJTC_field->required) ? 'required':'') + ($MJTC_field->readonly ? ['tabindex' => '-1'] : [])), MJTC_ALLOWED_TAGS);
                                            }else{
                                                echo wp_kses(MJTC_formfield::MJTC_select('departmentid', MJTC_includer::MJTC_getModel('department')->getDepartmentForCombobox(), $MJTC_departmentid, esc_html(__('Select', 'majestic-support')).' '.esc_html($MJTC_field->fieldtitle), array('class' => 'inputbox mjtc-form-select-field' . esc_attr($MJTC_readonlyclass), 'onchange' => $MJTC_VisibleFunction.' getHelpTopicByDepartment(this.value);', 'data-validation' => ($MJTC_field->required) ? 'required':'') + ($MJTC_field->readonly ? ['tabindex' => '-1'] : [])), MJTC_ALLOWED_TAGS);
                                            }
                                        ?>
                                    </div>
                                    <?php if(!empty($MJTC_field->description)): ?>
                                        <div class="mjtc-form-description">
                                            <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->description)); ?>
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
                                    <div class="mjtc-form-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->fieldtitle)); ?><?php if($MJTC_field->required == 1) echo wp_kses($MJTC_requiredTxt, MJTC_ALLOWED_TAGS); ?></div>
                                    <div class="mjtc-form-value" id="helptopic">
                                        <?php
                                            if(isset($MJTC_formdata['helptopicid'])) $MJTC_helptopicid = $MJTC_formdata['helptopicid'];
                                            elseif(isset(majesticsupport::$_data[0]->helptopicid)) $MJTC_helptopicid = majesticsupport::$_data[0]->helptopicid;
                                            elseif(MJTC_request::MJTC_getVar('helptopicid',0) > 0) $MJTC_helptopicid = MJTC_request::MJTC_getVar('helptopicid');
                                            else $MJTC_helptopicid = '';
                                            if (isset($MJTC_departmentid)) {
                                                $MJTC_dep_id = $MJTC_departmentid;
                                            } else{
                                                $MJTC_dep_id = 0;
                                            }
                                            echo wp_kses(MJTC_formfield::MJTC_select('helptopicid', MJTC_includer::MJTC_getModel('helptopic')->getHelpTopicsForCombobox($MJTC_dep_id), $MJTC_helptopicid, esc_html(__('Select', 'majestic-support')).' '.esc_html($MJTC_field->fieldtitle), array('class' => 'inputbox mjtc-form-select-field' . esc_attr($MJTC_readonlyclass),'data-validation'=>($MJTC_field->required) ? 'required': '', 'onchange' => $MJTC_VisibleFunction) + ($MJTC_field->readonly ? ['tabindex' => '-1'] : [])), MJTC_ALLOWED_TAGS);
                                        ?>
                                    </div>
                                    <?php if(!empty($MJTC_field->description)): ?>
                                        <div class="mjtc-form-description">
                                            <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->description)); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php
                                break;
                            case 'product':
                                ?>
                                <div class="mjtc-form-wrapper">
                                    <div class="mjtc-form-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->fieldtitle)); ?><?php if($MJTC_field->required == 1) echo wp_kses($MJTC_requiredTxt, MJTC_ALLOWED_TAGS); ?></div>
                                    <div class="mjtc-form-value" id="product">
                                        <?php
                                            if(isset($MJTC_formdata['productid'])) $MJTC_productid = $MJTC_formdata['productid'];
                                            elseif(isset(majesticsupport::$_data[0]->productid)) $MJTC_productid = majesticsupport::$_data[0]->productid;
                                            else $MJTC_productid = '';
                                            echo wp_kses(MJTC_formfield::MJTC_select('productid', MJTC_includer::MJTC_getModel('product')->getProductForCombobox(), $MJTC_productid, esc_html(__('Select', 'majestic-support')).' '.esc_html($MJTC_field->fieldtitle), array('class' => 'inputbox mjtc-form-select-field' . esc_attr($MJTC_readonlyclass),'data-validation'=>($MJTC_field->required) ? 'required': '', 'onchange' => $MJTC_VisibleFunction) + ($MJTC_field->readonly ? ['tabindex' => '-1'] : [])), MJTC_ALLOWED_TAGS);
                                        ?>
                                    </div>
                                    <?php if(!empty($MJTC_field->description)): ?>
                                        <div class="mjtc-form-description">
                                            <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->description)); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php
                                break;
                            case 'priority':
                                ?>
                                <div class="mjtc-form-wrapper">
                                    <div class="mjtc-form-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->fieldtitle)); ?><?php if($MJTC_field->required == 1) echo wp_kses($MJTC_requiredTxt, MJTC_ALLOWED_TAGS); ?></div>
                                    <div class="mjtc-form-value">
                                        <?php
                                            if(isset($MJTC_formdata['priorityid'])) $MJTC_priorityid = $MJTC_formdata['priorityid'];
                                            elseif(isset(majesticsupport::$_data[0]->priorityid)) $MJTC_priorityid = majesticsupport::$_data[0]->priorityid;
                                            else $MJTC_priorityid = MJTC_includer::MJTC_getModel('priority')->getDefaultPriorityID();

                                            if (!empty($MJTC_visibleparams) && !isset(majesticsupport::$_data[0]->id)) {
                                                $MJTC_wpnonce = wp_create_nonce("is-field-required-" . $MJTC_field->visible_field);
                                                $MJTC_jsObject = wp_json_encode($MJTC_visibleparams);
                                                // Build JS function without esc_js on JSON
                                                $MJTC_defaultFunc = "MJTC_getDataForVisibleField('" . esc_js($MJTC_wpnonce) . "', '" . esc_js($MJTC_priorityid) . "', '" . esc_js($MJTC_field->visible_field) . "', " . $MJTC_jsObject . ");";
                                                // Attach default function on document ready
                                                $majesticsupport_js = "
                                                    jQuery(document).ready(function(){
                                                        ".$MJTC_defaultFunc."
                                                    });
                                                ";
                                                wp_add_inline_script('majestic-support-cmain-js', $majesticsupport_js);
                                            }
                                            echo wp_kses(MJTC_formfield::MJTC_select('priorityid', MJTC_includer::MJTC_getModel('priority')->getPriorityForCombobox(), $MJTC_priorityid, esc_html(__('Select', 'majestic-support')).' '.esc_html($MJTC_field->fieldtitle), array('class' => 'inputbox mjtc-form-select-field' . esc_attr($MJTC_readonlyclass), 'data-validation' => 'required', 'onchange' => $MJTC_VisibleFunction) + ($MJTC_field->readonly ? ['tabindex' => '-1'] : [])), MJTC_ALLOWED_TAGS);
                                        ?>
                                    </div>
                                    <?php if(!empty($MJTC_field->description)): ?>
                                        <div class="mjtc-form-description">
                                            <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->description)); ?>
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
                                                    <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor">
                                                        <path d="M21.582,6.186c-0.23-0.86-0.908-1.538-1.768-1.768C18.254,4,12,4,12,4S5.746,4,4.186,4.418c-0.86,0.23-1.538,0.908-1.768,1.768C2,7.746,2,12,2,12s0,4.254,0.418,5.814c0.23,0.86,0.908,1.538,1.768,1.768C5.746,20,12,20,12,20s6.254,0,7.814-0.418c0.86-0.23,1.538-0.908,1.768-1.768C22,16.254,22,12,22,12S22,7.746,21.582,6.186z M10,15.464V8.536L16,12L10,15.464z"></path>
                                                    </svg>
                                                </a>
                                                <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->fieldtitle)); ?><?php if($MJTC_field->required == 1) echo wp_kses($MJTC_requiredTxt, MJTC_ALLOWED_TAGS); ?></div>
                                            <div class="mjtc-form-value">
                                                <?php
                                                    if(isset($MJTC_formdata['internalnotetitle'])) $MJTC_internalnotetitle = $MJTC_formdata['internalnotetitle'];
                                                    else $MJTC_internalnotetitle = $MJTC_field->defaultvalue;
                                                    echo wp_kses(MJTC_formfield::MJTC_text('internalnotetitle', $MJTC_internalnotetitle, array('class' => 'inputbox mjtc-form-input-field','data-validation'=>($MJTC_field->required == 1) ? 'required': '', 'placeholder'=> majesticsupport::MJTC_getVarValue($MJTC_field->placeholder)) + ($MJTC_field->readonly ? ['readonly' => 'readonly'] : [])), MJTC_ALLOWED_TAGS);
                                                ?>
                                            </div>
                                            <?php if(!empty($MJTC_field->description)): ?>
                                                <div class="mjtc-form-description">
                                                    <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->description)); ?>
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
                                                    if(isset($MJTC_formdata['internalnote'])) $MJTC_internalnote = $MJTC_formdata['internalnote'];
                                                    elseif(isset(majesticsupport::$_data[0]->internalnote)) $MJTC_internalnote = majesticsupport::$_data[0]->internalnote;
                                                    else $MJTC_internalnote = '';
                                                    wp_editor($MJTC_internalnote, 'internalnote', array('media_buttons' => false));
                                                ?>
                                            </div>
                                            <?php if(!empty($MJTC_field->description)): ?>
                                                <div class="mjtc-form-description">
                                                    <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->description)); ?>
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
                                    <div class="mjtc-form-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->fieldtitle)); ?><?php if($MJTC_field->required == 1) echo wp_kses($MJTC_requiredTxt, MJTC_ALLOWED_TAGS); ?></div>
                                    <div class="mjtc-form-value">
                                        <?php
                                            if(isset($MJTC_formdata['duedate'])) $MJTC_duedate = date_i18n(majesticsupport::$_config['date_format'], MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_formdata['duedate']));
                                            elseif(isset(majesticsupport::$_data[0]->duedate) && majesticsupport::$_data[0]->duedate != '0000-00-00 00:00:00'){
                                                $MJTC_duedate = date_i18n(majesticsupport::$_config['date_format'], MJTC_majesticsupportphplib::MJTC_strtotime(majesticsupport::$_data[0]->duedate));
                                            }elseif(!empty($MJTC_field->defaultvalue)){
                                                $MJTC_duedate = date_i18n(majesticsupport::$_config['date_format'], MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_field->defaultvalue));
                                            }else $MJTC_duedate = '';
                                            echo wp_kses(MJTC_formfield::MJTC_text('duedate', $MJTC_duedate, array('class' => 'custom_date mjtc-form-date-field','data-validation'=>($MJTC_field->required) ? 'required': '', 'placeholder'=> majesticsupport::MJTC_getVarValue($MJTC_field->placeholder)) + ($MJTC_field->readonly ? ['readonly' => 'readonly'] : [])), MJTC_ALLOWED_TAGS);
                                        ?>
                                    </div>
                                    <?php if(!empty($MJTC_field->description)): ?>
                                        <div class="mjtc-form-description">
                                            <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->description)); ?>
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
                                    <div class="mjtc-form-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->fieldtitle)); ?><?php if($MJTC_field->required == 1) echo wp_kses($MJTC_requiredTxt, MJTC_ALLOWED_TAGS); ?></div>
                                    <div class="mjtc-form-value">
                                        <?php
                                            if(isset($MJTC_formdata['status'])) $MJTC_status = $MJTC_formdata['status'];
                                            elseif(isset(majesticsupport::$_data[0]->status)) $MJTC_status = majesticsupport::$_data[0]->status;
                                            else $MJTC_status = '1';
                                            echo wp_kses(MJTC_formfield::MJTC_select('status', MJTC_includer::MJTC_getModel('status')->getStatusForCombobox(), $MJTC_status, esc_html(__('Select Status', 'majestic-support')), array('class' => 'radiobutton mjtc-form-select-field' . esc_attr($MJTC_readonlyclass),'data-validation'=>($MJTC_field->required) ? 'required': '') + ($MJTC_field->readonly ? ['tabindex' => '-1'] : [])), MJTC_ALLOWED_TAGS);
                                        ?>
                                    </div>
                                    <?php if(!empty($MJTC_field->description)): ?>
                                        <div class="mjtc-form-description">
                                            <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->description)); ?>
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
                                    <div class="mjtc-form-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->fieldtitle)); ?><?php if($MJTC_field->required == 1) echo wp_kses($MJTC_requiredTxt, MJTC_ALLOWED_TAGS); ?></div>
                                    <div class="mjtc-form-value">
                                        <?php
                                            if(isset($MJTC_formdata['staffid'])) $MJTC_staffid = $MJTC_formdata['staffid'];
                                            elseif(isset(majesticsupport::$_data[0]->staffid)) $MJTC_staffid = majesticsupport::$_data[0]->staffid;
                                            else $MJTC_staffid = '';
                                            echo wp_kses(MJTC_formfield::MJTC_select('staffid', MJTC_includer::MJTC_getModel('agent')->getStaffForCombobox(), $MJTC_staffid, esc_html(__('Select Agent', 'majestic-support')), array('class' => 'inputbox mjtc-form-select-field' . esc_attr($MJTC_readonlyclass),'data-validation'=>($MJTC_field->required) ? 'required': '') + ($MJTC_field->readonly ? ['tabindex' => '-1'] : [])), MJTC_ALLOWED_TAGS);
                                        ?>
                                    </div>
                                    <?php if(!empty($MJTC_field->description)): ?>
                                        <div class="mjtc-form-description">
                                            <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->description)); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php
                                break;
                            case 'subject':
                                ?>
                                <div class="mjtc-form-wrapper">
                                    <div class="mjtc-form-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->fieldtitle)); ?>&nbsp;<span style="color: red;" >*</span></div>
                                    <div class="mjtc-form-value">
                                        <?php
                                            if(isset($MJTC_formdata['subject'])) $MJTC_subject = $MJTC_formdata['subject'];
                                            elseif(isset(majesticsupport::$_data[0]->subject)) $MJTC_subject = majesticsupport::$_data[0]->subject;
                                            else $MJTC_subject = $MJTC_field->defaultvalue;
                                            echo wp_kses(MJTC_formfield::MJTC_text('subject', $MJTC_subject, array('class' => 'inputbox mjtc-form-input-field', 'data-validation' => 'required','style'=>'width:100%;', 'onchange' => $MJTC_VisibleFunction, 'placeholder'=> majesticsupport::MJTC_getVarValue($MJTC_field->placeholder)) + ($MJTC_field->readonly ? ['readonly' => 'readonly'] : [])), MJTC_ALLOWED_TAGS);
                                        ?>
                                    </div>
                                    <?php if(!empty($MJTC_field->description)): ?>
                                        <div class="mjtc-form-description">
                                            <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->description)); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php
                                break;
                            case 'premade':
                                if(!in_array('cannedresponses', majesticsupport::$_active_addons)){
                                    break;
                                }
                                $MJTC_text = MJTC_includer::MJTC_getModel('cannedresponses')->getPreMadeMessageForCombobox();
                                ?>
                                <div class="mjtc-form-wrapper fullwidth">
                                    <div class="mjtc-form-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->fieldtitle)); ?></div>
                                    <div class="mjtc-form-value mjtc-form-premade-wrp">
                                        <div class="mjtc-form-append">
                                            <?php echo wp_kses(MJTC_formfield::MJTC_checkbox('append', array('1' => esc_html(__('Append', 'majestic-support'))), '', array('class' => 'radiobutton mjtc-form-radio-field')), MJTC_ALLOWED_TAGS); ?>
                                        </div>
                                        <div id="premade">
                                            <?php
                                                foreach($MJTC_text as $MJTC_premade){
                                                    ?>
                                                    <div class="mjtc-form-perm-msg" onclick="getpremade(<?php echo esc_js($MJTC_premade->id); ?>);">
                                                        <a href="javascript:void(0)" title="<?php echo esc_attr(__('premade','majestic-support')); ?>"><?php echo esc_html($MJTC_premade->text); ?></a>
                                                    </div>
                                                    <?php
                                                }
                                                ?>
                                        </div>
                                    </div>
                                    <?php if(!empty($MJTC_field->description)): ?>
                                        <div class="mjtc-form-description">
                                            <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->description)); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php
                                break;
                            case 'issuesummary':
                                ?>
                                <div class="mjtc-form-wrapper fullwidth">
                                    <div class="mjtc-form-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->fieldtitle)); ?><?php if($MJTC_field->required == 1) echo wp_kses($MJTC_requiredTxt, MJTC_ALLOWED_TAGS); ?></div>
                                    <div class="mjtc-form-value">
                                        <?php
                                            if(isset($MJTC_formdata['message'])) $MJTC_message = wpautop(wptexturize(MJTC_majesticsupportphplib::MJTC_stripslashes($MJTC_formdata['message'])));
                                            elseif(isset(majesticsupport::$_data[0]->message)) $MJTC_message = majesticsupport::$_data[0]->message;
                                            else $MJTC_message = $MJTC_field->defaultvalue;
                                            if ($MJTC_field->readonly) {
                                                echo wp_kses(MJTC_formfield::MJTC_textarea('mjsupport_message', $MJTC_message, array('class' => 'inputbox mjtc-form-textarea-field one', 'rows' => 5, 'cols' => 25, 'placeholder'=> majesticsupport::MJTC_getVarValue($MJTC_field->placeholder), 'readonly'=> 'readonly')), MJTC_ALLOWED_TAGS);
                                            } else {
                                                wp_editor($MJTC_message, 'mjsupport_message', array('media_buttons' => false));
                                            }
                                        ?>
                                    </div>
                                    <?php if(!empty($MJTC_field->description)): ?>
                                        <div class="mjtc-form-description">
                                            <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->description)); ?>
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
                                    <div class="mjtc-form-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->fieldtitle)); ?><?php if($MJTC_field->required == 1) echo wp_kses($MJTC_requiredTxt, MJTC_ALLOWED_TAGS); ?></div>
                                    <div class="mjtc-form-value">
                                        <?php
                                        if(isset($MJTC_formdata['wcorderid'])) $MJTC_wcorderid = $MJTC_formdata['wcorderid'];
                                        elseif(isset(majesticsupport::$_data[0]->wcorderid)) $MJTC_wcorderid = majesticsupport::$_data[0]->wcorderid;
                                        else $MJTC_wcorderid = $MJTC_field->defaultvalue;
                                        echo wp_kses(MJTC_formfield::MJTC_text('wcorderid', $MJTC_wcorderid, array('class' => 'inputbox mjtc-form-input-field', 'data-validation' => ($MJTC_field->required == 1) ? 'required' : '','style'=>'width:100%;', 'placeholder'=> majesticsupport::MJTC_getVarValue($MJTC_field->placeholder)) + ($MJTC_field->readonly ? ['readonly' => 'readonly'] : [])), MJTC_ALLOWED_TAGS); ?>
                                        <span class="ms_product_found" title="<?php echo esc_attr(__("Order id found",'majestic-support')); ?>" style="display: none;"></span>
                                        <span class="ms_product_not_found" title="<?php echo esc_attr(__("Order id not found",'majestic-support')); ?>" style="display: none;"></span>
                                    </div>
                                    <?php if(!empty($MJTC_field->description)): ?>
                                        <div class="mjtc-form-description">
                                            <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->description)); ?>
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
                                    <div class="mjtc-form-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->fieldtitle)); ?><?php if($MJTC_field->required == 1) echo wp_kses($MJTC_requiredTxt, MJTC_ALLOWED_TAGS); ?></div>
                                    <div class="mjtc-form-value" id="wcproductid-wrap">
                                        <?php
                                            $MJTC_itemlist = array();
                                            if(isset($MJTC_formdata['wcproductid'])) $MJTC_wcproductid = $MJTC_formdata['wcproductid'];
                                            elseif(isset(majesticsupport::$_data[0]->wcproductid)) $MJTC_wcproductid = majesticsupport::$_data[0]->wcproductid;
                                            else $MJTC_wcproductid = '';
                                            echo wp_kses(MJTC_formfield::MJTC_select('wcproductid', $MJTC_itemlist, $MJTC_wcproductid, esc_html(__('Select', 'majestic-support')).' '.esc_html($MJTC_field->fieldtitle), array('class' => 'inputbox mjtc-form-select-field' . esc_attr($MJTC_readonlyclass)) + ($MJTC_field->readonly ? ['tabindex' => '-1'] : [])), MJTC_ALLOWED_TAGS); ?>
                                    </div>
                                    <?php if(!empty($MJTC_field->description)): ?>
                                        <div class="mjtc-form-description">
                                            <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->description)); ?>
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
                                $MJTC_itemlist = array();

                                if(isset($MJTC_formdata['eddorderid'])) $MJTC_eddorderid = $MJTC_formdata['eddorderid'];
                                elseif(isset(majesticsupport::$_data[0]->eddorderid)) $MJTC_eddorderid = majesticsupport::$_data[0]->eddorderid;
                                elseif(isset(majesticsupport::$_data['edd_order_id'])) $MJTC_eddorderid = majesticsupport::$_data['edd_order_id'];
                                else $MJTC_eddorderid = '';
                                    $MJTC_blogusers = get_users( array( 'fields' => array( 'ID' ) ) );
                                    $MJTC_user_purchase_array = array();
                                    foreach ($MJTC_blogusers AS $MJTC_b_user) {
                                        $MJTC_user_purchases = edd_get_users_purchases($MJTC_b_user->ID);
                                        if($MJTC_user_purchases){
                                            foreach ($MJTC_user_purchases AS $MJTC_user_purchase) {
                                                $MJTC_user_purchase_array[] = (object) array('id' => $MJTC_user_purchase->ID, 'text' => '#'.esc_html($MJTC_user_purchase->ID).'&nbsp;('. esc_html(__('Dated','majestic-support')).':&nbsp;' .date_i18n(majesticsupport::$_config['date_format'], MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_user_purchase->post_date)).')');
                                            }
                                        }
                                    }
                                     ?>
                                    <div class="mjtc-form-wrapper">
                                        <div class="mjtc-form-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->fieldtitle)); ?><?php if($MJTC_field->required == 1) echo wp_kses($MJTC_requiredTxt, MJTC_ALLOWED_TAGS); ?></div>
                                        <div class="mjtc-form-value" id="eddorderid-wrap">
                                            <?php echo wp_kses(MJTC_formfield::MJTC_select('eddorderid', $MJTC_user_purchase_array, $MJTC_eddorderid, esc_html(__('Select', 'majestic-support')).' '.esc_html($MJTC_field->fieldtitle), array('class' => 'inputbox mjtc-form-select-field' . esc_attr($MJTC_readonlyclass)) + ($MJTC_field->readonly ? ['tabindex' => '-1'] : [])), MJTC_ALLOWED_TAGS); ?>
                                        </div>
                                        <?php if(!empty($MJTC_field->description)): ?>
                                            <div class="mjtc-form-description">
                                                <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->description)); ?>
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

                                $MJTC_order_products_array = array();
                                if($MJTC_eddorderid != '' && is_numeric($MJTC_eddorderid)){
                                    $MJTC_order_products = edd_get_payment_meta_cart_details($MJTC_eddorderid);
                                    foreach ($MJTC_order_products as $MJTC_order_product) {
                                        $MJTC_order_products_array[] = (object) array('id'=>$MJTC_order_product['id'], 'text'=>$MJTC_order_product['name']);
                                    }
                                }

                                if(isset($MJTC_formdata['eddproductid'])) $MJTC_eddproductid = $MJTC_formdata['eddproductid'];
                                elseif(isset(majesticsupport::$_data[0]->eddproductid)) $MJTC_eddproductid = majesticsupport::$_data[0]->eddproductid;
                                else $MJTC_eddproductid = '';  ?>
                                <div class="mjtc-form-wrapper">
                                    <div class="mjtc-form-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->fieldtitle)); ?><?php if($MJTC_field->required == 1) echo wp_kses($MJTC_requiredTxt, MJTC_ALLOWED_TAGS); ?></div>
                                    <div class="mjtc-form-value" id="eddproductid-wrap">
                                        <?php echo wp_kses(MJTC_formfield::MJTC_select('eddproductid', $MJTC_order_products_array, $MJTC_eddproductid, esc_html(__('Select', 'majestic-support')).' '.esc_html($MJTC_field->fieldtitle), array('class' => 'inputbox mjtc-form-select-field' . esc_attr($MJTC_readonlyclass)) + ($MJTC_field->readonly ? ['tabindex' => '-1'] : [])), MJTC_ALLOWED_TAGS); ?>
                                    </div>
                                    <?php if(!empty($MJTC_field->description)): ?>
                                        <div class="mjtc-form-description">
                                            <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->description)); ?>
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

                                $MJTC_license_key_array = array();
                                if($MJTC_eddorderid != '' && is_numeric($MJTC_eddorderid)){
                                    $MJTC_license = EDD_Software_Licensing::instance();
                                    $MJTC_result = $MJTC_license->get_licenses_of_purchase($MJTC_eddorderid);
                                    if($MJTC_result){
                                        foreach ($MJTC_result AS $MJTC_license_record) {
                                            $MJTC_license_record_licensekey = $MJTC_license->get_license_key($MJTC_license_record->ID);
                                            if($MJTC_license_record_licensekey != ''){
                                                $MJTC_license_key_array[] = (object) array('id' => $MJTC_license_record_licensekey,'text' => $MJTC_license_record_licensekey);
                                            }
                                        }
                                    }
                                }

                                $MJTC_itemlist = array();
                                if(isset($MJTC_formdata['eddlicensekey'])) $MJTC_eddlicensekey = $MJTC_formdata['eddlicensekey'];
                                elseif(isset(majesticsupport::$_data[0]->eddlicensekey)) $MJTC_eddlicensekey = majesticsupport::$_data[0]->eddlicensekey;
                                else $MJTC_eddlicensekey = '';
                                ?>
                                <div class="mjtc-form-wrapper">
                                    <div class="mjtc-form-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->fieldtitle));if($MJTC_field->required == 1) echo wp_kses($MJTC_requiredTxt, MJTC_ALLOWED_TAGS); ?></div>
                                    <div class="mjtc-form-value" id="eddlicensekey-wrap">
                                        <?php echo wp_kses(MJTC_formfield::MJTC_select('eddlicensekey', $MJTC_license_key_array, $MJTC_eddlicensekey, esc_html(__('Select', 'majestic-support')).' '.esc_html($MJTC_field->fieldtitle), array('class' => 'inputbox mjtc-form-select-field' . esc_attr($MJTC_readonlyclass)) + ($MJTC_field->readonly ? ['tabindex' => '-1'] : [])), MJTC_ALLOWED_TAGS); ?>
                                    </div>
                                    <?php if(!empty($MJTC_field->description)): ?>
                                        <div class="mjtc-form-description">
                                            <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->description)); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php

                                break;
                            case 'attachments':
                                ?>
                                <div class="mjtc-form-wrapper fullwidth">
                                    <div class="mjtc-form-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->fieldtitle)); ?><?php if($MJTC_field->required == 1) echo wp_kses($MJTC_requiredTxt, MJTC_ALLOWED_TAGS); ?></div>
                                    <?php
                                    if(isset(majesticsupport::$_data[5]) && count(majesticsupport::$_data[5]) > 0){
                                        $MJTC_attachmentreq = '';
                                    }else{
                                        $MJTC_attachmentreq = $MJTC_field->required == 1 ? 'required' : '';
                                    }
                                    ?>
                                    <div class="mjtc-form-value">
                                        <div class="tk_attachment_value_wrapperform">
                                            <span class="tk_attachment_value_text">
                                                <input type="file" class="inputbox" name="filename[]" onchange="MJTC_uploadfile(this, '<?php echo esc_js(majesticsupport::$_config['file_maximum_size']); ?>', '<?php echo esc_js(majesticsupport::$_config['file_extension']); ?>');" size="20" maxlenght='30' data-validation="<?php echo esc_attr($MJTC_attachmentreq); ?>" />
                                                <span class='tk_attachment_remove'></span>
                                            </span>
                                        </div>
                                        <div class="tk_attachments_desc">
                                            <span class="tk_attachments_configform">
                                                <small>
                                                    <?php
                                                    $MJTC_tktdata = esc_html(__('Maximum File Size', 'majestic-support')).' (' . esc_html(majesticsupport::$_config['file_maximum_size']).'KB)<br>'.esc_html(__('File Extension Type', 'majestic-support')).' (' . esc_html(majesticsupport::$_config['file_extension']) . ')';
                                                    echo wp_kses($MJTC_tktdata, MJTC_ALLOWED_TAGS);
                                                    ?>
                                                </small>
                                            </span>
                                            <span id="tk_attachment_add" class="tk_attachments_addform ms-button-link ms-button-bg-link"><?php echo esc_html(__('Add More Files', 'majestic-support')); ?></span>
                                        </div>
                                        <?php
                                        if (!empty(majesticsupport::$_data[5])) {
                                            foreach (majesticsupport::$_data[5] AS $MJTC_attachment) {
                                                $MJTC_attachmentid = isset(majesticsupport::$_data[0]->id) ? majesticsupport::$_data[0]->id : '';
                                                echo wp_kses('
                                                    <div class="mjtc_supportattachment">
                                                            ' . esc_html($MJTC_attachment->filename) . '
                                                            <a title="'. esc_html(__('Delete','majestic-support')).'" href="' . esc_url(wp_nonce_url('?page=majesticsupport_attachment&task=deleteattachment&action=mstask&id=' . $MJTC_attachment->id . '&ticketid=' . $MJTC_attachmentid, 'delete-attachement-'.$MJTC_attachment->id)) . '"><img alt="'. esc_html(__('Delete','majestic-support')).'" src="'.esc_url(MJTC_PLUGIN_URL).'includes/images/delete.png" /></a>
                                                    </div>', MJTC_ALLOWED_TAGS);
                                            }
                                        }
                                        ?>
                                    </div>
                                    <?php if(!empty($MJTC_field->description)): ?>
                                        <div class="mjtc-form-description">
                                            <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->description)); ?>
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
                                    $MJTC_envlicense = json_decode(majesticsupport::$_data[0]->envatodata, true);
                                }else{
                                    $MJTC_envlicense = array();
                                }
                                ?>
                                <div class="mjtc-form-wrapper">
                                    <div class="mjtc-form-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->fieldtitle)); ?><?php if($MJTC_field->required == 1) echo wp_kses($MJTC_requiredTxt, MJTC_ALLOWED_TAGS); ?></div>
                                    <div class="mjtc-form-field mjtc-form-value">
                                        <?php
                                        if(isset($MJTC_formdata['envatopurchasecode'])) $MJTC_envatopurchasecode = $MJTC_formdata['envatopurchasecode'];
                                        elseif(isset($MJTC_envlicense['license'])) $MJTC_envatopurchasecode = $MJTC_envlicense['license'];
                                        else $MJTC_envatopurchasecode = $MJTC_field->defaultvalue;
                                        echo wp_kses(MJTC_formfield::MJTC_text('envatopurchasecode', $MJTC_envatopurchasecode, array('class' => 'inputbox inputbox mjtc-form-input-field', 'data-validation' => ($MJTC_field->required ? 'required' : ''), 'placeholder'=> majesticsupport::MJTC_getVarValue($MJTC_field->placeholder)) + ($MJTC_field->readonly ? ['readonly' => 'readonly'] : [])), MJTC_ALLOWED_TAGS);
                                        echo wp_kses(MJTC_formfield::MJTC_hidden('prev_envatopurchasecode', $MJTC_envatopurchasecode), MJTC_ALLOWED_TAGS);
                                        ?>
                                    </div>
                                </div>
                                <?php
                                break;
                            default:
                                $MJTC_customfields = MJTC_includer::MJTC_getObjectClass('customfields')->MJTC_formCustomFields($MJTC_field);
                                if (isset($MJTC_customfields)) {
                                    echo wp_kses($MJTC_customfields, MJTC_ALLOWED_TAGS);
                                }
                                break;
                        }
                    endforeach;
                    echo wp_kses('<input type="hidden" id="userfeilds_total" name="userfeilds_total"  value="' . esc_attr($MJTC_i) . '"  />', MJTC_ALLOWED_TAGS);
                ?>
                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('id', isset(majesticsupport::$_data[0]->id) ? majesticsupport::$_data[0]->id : ''), MJTC_ALLOWED_TAGS) ?>
                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('multiformid', isset(majesticsupport::$_data['formid']) ? majesticsupport::$_data['formid'] : '1'), MJTC_ALLOWED_TAGS); ?>
                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('attachmentdir', isset(majesticsupport::$_data[0]->attachmentdir) ? majesticsupport::$_data[0]->attachmentdir : ''), MJTC_ALLOWED_TAGS); ?>
                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('ticketid', isset(majesticsupport::$_data[0]->ticketid) ? majesticsupport::$_data[0]->ticketid : ''), MJTC_ALLOWED_TAGS); ?>
                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('created', isset(majesticsupport::$_data[0]->created) ? majesticsupport::$_data[0]->created : ''), MJTC_ALLOWED_TAGS); ?>
                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('lastreply', isset(majesticsupport::$_data[0]->lastreply) ? majesticsupport::$_data[0]->lastreply : ''), MJTC_ALLOWED_TAGS); ?>
                <?php
                    if (isset(majesticsupport::$_data[0]->uid))
                        $MJTC_uid = majesticsupport::$_data[0]->uid;
                    else
                        $MJTC_uid = get_current_user_id();
                    echo wp_kses(MJTC_formfield::MJTC_hidden('uid', $MJTC_uid), MJTC_ALLOWED_TAGS);
                ?>
                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('updated', isset(majesticsupport::$_data[0]->updated) ? majesticsupport::$_data[0]->updated : '' ), MJTC_ALLOWED_TAGS); ?>
                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('action', 'ticket_saveticket'), MJTC_ALLOWED_TAGS); ?>
                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('form_request', 'majesticsupport'), MJTC_ALLOWED_TAGS); ?>
                <div class="mjtc-form-button">
                    <?php echo wp_kses(MJTC_formfield::MJTC_submitbutton('save', esc_html(__('Submit Ticket', 'majestic-support')), array('class' => 'button mjtc-form-save')), MJTC_ALLOWED_TAGS); ?>
                    <a href="admin.php?page=majesticsupport_ticket" class="mjtc-form-cancel"><?php echo esc_html(__('Cancel','majestic-support')); ?></a>
                </div>
            </form>
        </div>
    </div>
</div>
