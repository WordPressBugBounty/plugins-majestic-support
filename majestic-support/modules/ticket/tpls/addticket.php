<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if (MJTC_includer::MJTC_getObjectClass('user')->MJTC_isguest() && majesticsupport::$_config['show_captcha_on_visitor_from_ticket'] == 1 && majesticsupport::$_config['captcha_selection'] == 1) {
    wp_enqueue_script( 'majesticsupport-recaptcha', 'https://www.google.com/recaptcha/api.js', array(), '1.0.0', true );
}
?>
<div class="ms-main-up-wrapper">
    <?php
if (majesticsupport::$_config['offline'] == 2) {
    if (MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid() != 0 || majesticsupport::$_config['visitor_can_create_ticket'] == 1) {
        MJTC_message::MJTC_getMessage();
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_script('majesticsupport-file_validate.js', MJTC_PLUGIN_URL . 'includes/js/file_validate.js', array(), '1.0.0', true);

		wp_enqueue_style('majesticsupport-jquery-ui-css', MJTC_PLUGIN_URL . 'includes/css/jquery-ui-smoothness.css', array(), '1.0.0');
        $majesticsupport_js ="
            var ajaxurl ='".esc_url(admin_url('admin-ajax.php'))."';
            function onSubmit(token) {
                document.getElementById('adminTicketform').submit();
            }
            jQuery(document).ready(function($) {
                $('.custom_date').datepicker({
                    dateFormat: 'yy-mm-dd'
                });
                jQuery('#tk_attachment_add').click(function() {
                    var obj = this;
                    var current_files = jQuery('input[name=\'filename[]\']').length;
                    var total_allow = ". esc_attr(majesticsupport::$_config['no_of_attachement']) .";
                    var append_text = '<span class=\'tk_attachment_value_text\'><input name=\'filename[]\' type=\"file\" onchange=\"MJTC_uploadfile(this,\"". esc_js(majesticsupport::$_config['file_maximum_size'])."\",". esc_js(majesticsupport::$_config['file_extension']) ."\'); size=\'20\' maxlenght=\'30\'  /><span  class=\'tk_attachment_remove\'></span></span>';
                    if (current_files < total_allow) {
                        jQuery('.tk_attachment_value_wrapperform').append(append_text);
                    } else if ((current_files === total_allow) || (current_files > total_allow)) {
                        alert('". esc_html(__('File upload limit exceeds', 'majestic-support'))."');
                        jQuery(obj).hide();
                    }
                });
                jQuery(document).delegate('.tk_attachment_remove', 'click', function(e) {
                    jQuery(this).parent().remove();
                    var current_files = jQuery('input[name=\'filename[]\']').length;
                    var total_allow = ". esc_attr(majesticsupport::$_config['no_of_attachement']) .";
                    if (current_files < total_allow) {
                        jQuery('#tk_attachment_add').show();
                    }
                });
                $.validate();
            });
            // to get premade and append to isssue summery
            function getHelpTopicByDepartment(val) {
                jQuery.post(ajaxurl, {
                    action: 'mjsupport_ajax',
                    val: val,
                    mjsmod: 'department',
                    task: 'getHelpTopicByDepartment',
                    '_wpnonce':'". esc_attr(wp_create_nonce('get-help-topic-by-department'))."'
                }, function(data) {
                    if (data != false) {
                        jQuery('div#helptopic').html(data);
                    } else {
                        jQuery('div#helptopic').html('<div class=\"helptopic-no-rec\">". esc_html(__('No help topic found','majestic-support'))."</div>');
                    }
                }); //jquery closed
            }

            // woocommerce
            function ms_wc_order_products() {
                var orderid = jQuery('#wcorderid').val();
                emptycombo =
                    '<select name=\"wcproductid\" id=\"wcproductid\"  class=\"inputbox mjtc-form-select-field mjtc-support-select-field\" ><option value=\"\">Select Product</option></select>';
                jQuery('#wcproductid-wrap').html(emptycombo);
                jQuery.post(
                    ajaxurl, {
                        action: 'mjsupport_ajax',
                        mjsmod: 'woocommerce',
                        task: 'getWcOrderProductsAjax',
                        orderid: orderid,
                        '_wpnonce':'". esc_attr(wp_create_nonce("get-wcorder-products-ajax"))."'
                    },
                    function(data) {
                        data1 = JSON.parse(data);
                        jQuery('#wcproductid-wrap').html(MJTC_msDecodeHTML(data1['html']));
                    }
                );
            }

            function ms_edd_order_products() {
                var orderid = jQuery('select#eddorderid').val();
                jQuery.post(ajaxurl, {
                    action: 'mjsupport_ajax',
                    mjsmod: 'easydigitaldownloads',
                    task: 'getEDDOrderProductsAjax',
                    eddorderid: orderid,
                    '_wpnonce':'". esc_attr(wp_create_nonce("get-eddorder-products-ajax"))."'
                }, function(data) {
                    jQuery('#eddproductid-wrap').html(data);
                });
            }

            function ms_eed_product_licenses() {
                var eddproductid = jQuery('select#eddproductid').val();
                var orderid = jQuery('select#eddorderid').val();
                jQuery.post(ajaxurl, {
                    action: 'mjsupport_ajax',
                    mjsmod: 'easydigitaldownloads',
                    task: 'getEDDProductlicensesAjax',
                    eddproductid: eddproductid,
                    eddorderid: orderid,
                    '_wpnonce':'". esc_attr(wp_create_nonce("get-edd-productlicenses-ajax"))."'
                }, function(data) {
                    jQuery('#eddlicensekey-wrap').html(data);
                });
            }

            jQuery(document).ready(function() {
                jQuery('select#eddorderid').change(function() {
                    ms_edd_order_products();
                });";
                if(!isset(majesticsupport::$_data[0]->id)) {
                    $majesticsupport_js .="
                    if (jQuery('select#eddorderid').val()) {
                        ms_edd_order_products();
                    }";
                }
                $majesticsupport_js .="
                jQuery(document).on('change', 'select#eddproductid', function() {
                    ms_eed_product_licenses();
                });
                if (jQuery('select#eddproductid').val()) {
                    ms_eed_product_licenses();
                }

                jQuery('#wcorderid').change(function() {
                    ms_wc_order_products();
                });
                if (jQuery('#wcorderid').val()) {
                    ms_wc_order_products();
                }
            });
        ";
        wp_add_inline_script('majestic-support-cmain-js',$majesticsupport_js);
        ?>
        <span style="display:none" id="filesize"><?php echo esc_html(__('Error file size too large', 'majestic-support')); ?></span>
        <span style="display:none" id="fileext"><?php echo esc_html(__('The uploaded file extension not valid', 'majestic-support')); ?></span>
        <?php
        $MJTC_loginuser_name = '';
        $MJTC_loginuser_email = '';
        if (!MJTC_includer::MJTC_getObjectClass('user')->MJTC_isguest()) {
            $MJTC_current_user = MJTC_includer::MJTC_getObjectClass('user')->MJTC_getMSCurrentUser();
            if(empty($MJTC_current_user->display_name) == true){
                $MJTC_loginuser_name = $MJTC_current_user->user_nicename;
            }else{
                $MJTC_loginuser_name = $MJTC_current_user->display_name;
            }
            $MJTC_loginuser_email = $MJTC_current_user->user_email;
        }
        ?>
        <?php MJTC_message::MJTC_getMessage(); ?>
        <?php $MJTC_formdata = MJTC_formfield::MJTC_getFormData(); ?>
        <?php include_once(MJTC_PLUGIN_PATH . 'includes/header.php'); ?>
        <div class="mjtc-support-top-sec-header">
        <img class="mjtc-transparent-header-img1" alt="<?php echo esc_attr(__('Image', 'majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/tp-image.png" />
        <div class="mjtc-support-top-sec-left-header">
            <div class="mjtc-support-main-heading">
                <?php echo esc_html(__("Submit Ticket",'majestic-support')); ?>
            </div>
            <div class="mjtc-support-sub-heading">
                <?php echo esc_html(__("Submit a new support ticket and get help from our team quickly.",'majestic-support')); ?>
            </div>

        </div>
    </div>
    <div class="mjtc-support-cont-main-wrapper">
        <div class="mjtc-support-cont-wrapper1">
            <?php
            if (majesticsupport::$_config['new_ticket_message']) { ?>
                <div class="mjtc-col-xs-12 mjtc-col-md-12 mjtc-support-form-instruction-message">
                    <?php echo wp_kses(majesticsupport::$_config['new_ticket_message'], MJTC_ALLOWED_TAGS); ?>
                </div>
                <?php
            } ?>
            <div class="mjtc-support-add-form-main-wrapper">
                <?php
                $MJTC_showform = true;
                if(in_array('paidsupport', majesticsupport::$_active_addons) && class_exists('WooCommerce')){
                    if(isset(majesticsupport::$_data['paidsupport'])){
                        $MJTC_row = majesticsupport::$_data['paidsupport'];
                        $MJTC_paidsupportid = $MJTC_row->itemid; ?>
                        <div class="majestic-support-paid-support-info">
                            <h3>
                                <?php echo esc_html(__("Paid support info",'majestic-support')); ?>
                            </h3>
                            <table border="1" class="majestic-support-paid-support-info-table">
                                <tr>
                                    <th><?php echo esc_html(__("Order ID",'majestic-support')); ?></th>
                                    <th><?php echo esc_html(__("Product Name",'majestic-support')); ?></th>
                                    <th><?php echo esc_html(__("Total Tickets",'majestic-support')); ?></th>
                                    <th><?php echo esc_html(__("Remaining Tickets",'majestic-support')); ?></th>
                                </tr>
                                <tr>
                                    <td>#<?php echo esc_html($MJTC_row->orderid); ?></td>
                                    <td><?php
                                        echo esc_html($MJTC_row->itemname);
                                        if($MJTC_row->qty > 1){
                                            $MJTC_tkt = '<b> x '.esc_html($MJTC_row->qty)."</b>";
                                            echo wp_kses($MJTC_tkt, MJTC_ALLOWED_TAGS);
                                        }
                                        ?></td>
                                    <td>
                                        <?php
                                        if($MJTC_row->total == -1) {
                                            echo esc_html(__("Unlimited",'majestic-support'));
                                        } else {
                                            echo esc_html($MJTC_row->total);
                                        } ?>
                                    </td>
                                    <td>
                                        <?php
                                        if($MJTC_row->total == -1){
                                            echo esc_html(__("Unlimited",'majestic-support'));
                                        } else {
                                            echo esc_html($MJTC_row->remaining);
                                        }
                                        ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <?php
                    } elseif(isset(majesticsupport::$_data['paidsupportitems'])) {
                        $MJTC_showform = false;
                        $MJTC_paidsupportitems = majesticsupport::$_data['paidsupportitems'];
                        if(empty($MJTC_paidsupportitems)){ ?>
                            <div class="mjtc-support-error-message-wrapper mjtc-support-cont-wrapper-color">
                                <div class="mjtc-support-message-image-wrapper">
                                    <img class="mjtc-support-message-image" alt="message image"
                                        src="<?php echo esc_url(MJTC_PLUGIN_URL).'/includes/images/error/not-permission-icon.png'; ?>">
                                </div>
                                <div class="mjtc-support-messages-data-wrapper">
                                    <span class="mjtc-support-messages-main-text">
                                        <?php echo esc_html(__("You have not purchased any supported item",'majestic-support')); ?>
                                    </span>
                                    <span class="mjtc-support-user-login-btn-wrp">
                                        <a class="mjtc-support-login-btn"
                                            href="<?php echo esc_url(get_permalink( wc_get_page_id( 'shop' ) )); ?>"><?php echo esc_html(__("Go to shop",'majestic-support')); ?></a>
                                    </span>
                                </div>
                            </div>
                            <?php
                        } else { ?>
                            <h3>
                                <?php echo esc_html(__("Please select paid support item",'majestic-support')); ?>
                                <span style="color:red">*</span>
                            </h3>
                            <table border="1">
                                <tr>
                                    <th><?php echo esc_html(__("Order ID",'majestic-support')); ?></th>
                                    <th><?php echo esc_html(__("Product Name",'majestic-support')); ?></th>
                                    <th><?php echo esc_html(__("Total Tickets",'majestic-support')); ?></th>
                                    <th><?php echo esc_html(__("Remaining Tickets",'majestic-support')); ?></th>
                                    <th></th>
                                </tr>
                                <?php
                                foreach($MJTC_paidsupportitems as $MJTC_row){ ?>
                                    <tr>
                                        <td>#<?php echo esc_html($MJTC_row->orderid); ?></td>
                                        <td>
                                            <?php
                                            echo esc_html($MJTC_row->itemname);
                                            if($MJTC_row->qty > 1){
                                                $MJTC_tkt = '<b> x '.esc_html($MJTC_row->qty)."</b>";
                                                echo wp_kses($MJTC_tkt, MJTC_ALLOWED_TAGS);
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            if($MJTC_row->total == -1){
                                                echo esc_html(__("Unlimited",'majestic-support'));
                                            } else {
                                                echo esc_html($MJTC_row->total);
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            if($MJTC_row->total == -1){
                                                echo esc_html(__("Unlimited",'majestic-support'));
                                            } else {
                                                echo esc_html($MJTC_row->remaining);
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <a href="<?php echo esc_url(majesticsupport::makeUrl(array('mjsmod'=>'ticket','mjslay'=>'addticket','paidsupportid'=>$MJTC_row->itemid))); ?>">
                                                <?php echo esc_html(__("Select",'majestic-support')); ?>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php
                                } ?>
                            </table>
                            <?php
                        }
                    }
                }
                if($MJTC_showform): ?>
                    <?php $MJTC_nonce_id = isset(majesticsupport::$_data[0]->id) ? majesticsupport::$_data[0]->id : ''; ?>
                    <form class="mjtc-support-form1 majestic-support-form" method="post" action="<?php echo esc_url(wp_nonce_url(majesticsupport::makeUrl(array('mjsmod'=>'ticket', 'task'=>'saveticket')),"save-ticket-".$MJTC_nonce_id)); ?>" id="adminTicketform" enctype="multipart/form-data">
                    <?php
                    $MJTC_i = '';
                    $MJTC_fieldcounter = 0;
                    $MJTC_eddorderid = '';
                        $MJTC_requiredTxt = '&nbsp;<span style="color:red">*</span>';
                        $MJTC_openingTag = '<div class="mjtc-support-add-form-wrapper">';
                        $MJTC_closingTag = '</div>';
                        apply_filters('mjtc_support_ticket_frontend_ticket_form_start',1);
                        foreach (majesticsupport::$_data['fieldordering'] AS $MJTC_field):
                            $MJTC_readonlyclass = $MJTC_field->readonly ? " mjtc-form-ticket-readonly " : "";
                        $MJTC_visibleclass = "";
                        if (!empty($MJTC_field->visibleparams) && $MJTC_field->visibleparams != '[]'){
                            $MJTC_visibleclass = ' visible ';
                        }
                        $MJTC_VisibleFunction = '';
                        if ($MJTC_field->visible_field != null) {
                            $MJTC_visibleparams = MJTC_includer::MJTC_getModel('fieldordering')->MJTC_getDataForVisibleField($MJTC_field->visible_field);
                            if (!empty($MJTC_visibleparams)) {
                                $MJTC_wpnonce = wp_create_nonce("is-field-required-".$MJTC_field->visible_field);
                                $MJTC_jsObject = wp_json_encode($MJTC_visibleparams);
                                $MJTC_VisibleFunction = " MJTC_getDataForVisibleField('".$MJTC_wpnonce."', this.value, '".esc_js($MJTC_field->visible_field)."', ".$MJTC_jsObject.");";
                            }
                        }
                            switch ($MJTC_field->field) {
                                case 'email':
                                    if($MJTC_fieldcounter % 2 == 0){
                                        if($MJTC_fieldcounter != 0){
                                            echo wp_kses($MJTC_closingTag, MJTC_ALLOWED_TAGS);
                                        }
                                        echo wp_kses($MJTC_openingTag, MJTC_ALLOWED_TAGS);
                                    }
                                $MJTC_fieldcounter++; ?>
                                <div class="mjtc-support-from-field-wrp <?php echo esc_attr($MJTC_visibleclass); ?>">
                                    <div class="mjtc-support-from-field-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->fieldtitle)); ?>&nbsp;<?php if($MJTC_field->required == 1) echo wp_kses($MJTC_requiredTxt, MJTC_ALLOWED_TAGS); ?></div>
                                    <div class="mjtc-support-from-field">
                                        <?php
                                            if(isset($MJTC_formdata['email'])) $MJTC_email = $MJTC_formdata['email'];
                                            elseif(isset(majesticsupport::$_data[0]->email)) $MJTC_email = majesticsupport::$_data[0]->email;
                                            elseif(!empty($MJTC_field->defaultvalue)) $MJTC_email = $MJTC_field->defaultvalue;
                                            else $MJTC_email = $MJTC_loginuser_email;
                                            $MJTC_email = MJTC_majesticsupportphplib::MJTC_strip_tags($MJTC_email); // in some case, p tag is attached to email
                                            echo wp_kses(MJTC_formfield::MJTC_email('email', $MJTC_email, array('class' => 'inputbox mjtc-support-form-field-input', 'data-validation' => ($MJTC_field->required) ? 'required email' : 'email', 'data-validation-optional' => ($MJTC_field->required) ? 'false' : 'true', 'onchange' => $MJTC_VisibleFunction, 'placeholder'=> majesticsupport::MJTC_getVarValue($MJTC_field->placeholder)) + ($MJTC_field->readonly ? ['readonly' => 'readonly'] : [])), MJTC_ALLOWED_TAGS);?>
                                    </div>
                                    <?php if(!empty($MJTC_field->description)): ?>
                                        <div class="mjtc-support-from-field-description">
                                            <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->description)); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php
                                break;
                            case 'fullname':
                                if($MJTC_fieldcounter % 2 == 0){
                                    if($MJTC_fieldcounter != 0){
                                        echo wp_kses($MJTC_closingTag, MJTC_ALLOWED_TAGS);
                                    }
                                    echo wp_kses($MJTC_openingTag, MJTC_ALLOWED_TAGS);
                                }
                                $MJTC_fieldcounter++; ?>
                                <div class="mjtc-support-from-field-wrp <?php echo esc_attr($MJTC_visibleclass); ?>">
                                    <div class="mjtc-support-from-field-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->fieldtitle)); ?>&nbsp;<?php if($MJTC_field->required == 1) echo wp_kses($MJTC_requiredTxt, MJTC_ALLOWED_TAGS); ?></div>
                                    <div class="mjtc-support-from-field">
                                        <?php
                                            if(isset($MJTC_formdata['name'])) $MJTC_name = $MJTC_formdata['name'];
                                            elseif(isset(majesticsupport::$_data[0]->name)) $MJTC_name = majesticsupport::$_data[0]->name;
                                            elseif(!empty($MJTC_field->defaultvalue)) $MJTC_name = $MJTC_field->defaultvalue;
                                            else $MJTC_name = $MJTC_loginuser_name;
                                            echo wp_kses(MJTC_formfield::MJTC_text('name', $MJTC_name, array('class' => 'inputbox mjtc-support-form-field-input', 'data-validation' => ($MJTC_field->required) ? 'required' : '', 'onchange' => $MJTC_VisibleFunction, 'placeholder'=> majesticsupport::MJTC_getVarValue($MJTC_field->placeholder)) + ($MJTC_field->readonly ? ['readonly' => 'readonly'] : [])), MJTC_ALLOWED_TAGS);
                                        ?>
                                    </div>
                                    <?php if(!empty($MJTC_field->description)): ?>
                                        <div class="mjtc-support-from-field-description">
                                            <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->description)); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php
                                break;
                            case 'phone':
                                if($MJTC_fieldcounter % 2 == 0){
                                    if($MJTC_fieldcounter != 0){
                                        echo wp_kses($MJTC_closingTag, MJTC_ALLOWED_TAGS);
                                    }
                                    echo wp_kses($MJTC_openingTag, MJTC_ALLOWED_TAGS);
                                }
                                $MJTC_fieldcounter++; ?>
                                <div class="mjtc-support-from-field-wrp <?php echo esc_attr($MJTC_visibleclass); ?>">
                                    <div class="mjtc-support-from-field-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->fieldtitle)); ?>&nbsp;<?php if($MJTC_field->required == 1) echo wp_kses($MJTC_requiredTxt, MJTC_ALLOWED_TAGS); ?></div>
                                    <div class="mjtc-support-from-field">
                                        <?php
                                            if(isset($MJTC_formdata['phone'])) $MJTC_phone = $MJTC_formdata['phone'];
                                            elseif(isset(majesticsupport::$_data[0]->phone)) $MJTC_phone = majesticsupport::$_data[0]->phone;
                                            else $MJTC_phone = $MJTC_field->defaultvalue;
                                            echo wp_kses(MJTC_formfield::MJTC_text('phone', $MJTC_phone, array('class' => 'inputbox mjtc-support-form-field-input', 'data-validation' => ($MJTC_field->required) ? 'required' : '', 'onchange' => $MJTC_VisibleFunction, 'placeholder'=> majesticsupport::MJTC_getVarValue($MJTC_field->placeholder)) + ($MJTC_field->readonly ? ['readonly' => 'readonly'] : [])), MJTC_ALLOWED_TAGS);
                                        ?>
                                    </div>
                                    <?php if(!empty($MJTC_field->description)): ?>
                                        <div class="mjtc-support-from-field-description">
                                            <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->description)); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php
                                break;
                            case 'phoneext':
                                if($MJTC_fieldcounter % 2 == 0){
                                    if($MJTC_fieldcounter != 0){
                                        echo wp_kses($MJTC_closingTag, MJTC_ALLOWED_TAGS);
                                    }
                                    echo wp_kses($MJTC_openingTag, MJTC_ALLOWED_TAGS);
                                }
                                $MJTC_fieldcounter++;
                                ?>
                                <div class="mjtc-support-from-field-wrp <?php echo esc_attr($MJTC_visibleclass); ?>">
                                    <div class="mjtc-support-from-field-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->fieldtitle)); ?>&nbsp;<?php if($MJTC_field->required == 1) echo wp_kses($MJTC_requiredTxt, MJTC_ALLOWED_TAGS); ?></div>
                                    <div class="mjtc-support-from-field">
                                        <?php
                                            if(isset($MJTC_formdata['phoneext'])) $MJTC_phoneext = $MJTC_formdata['phoneext'];
                                            elseif(isset(majesticsupport::$_data[0]->phoneext)) $MJTC_phoneext = majesticsupport::$_data[0]->phoneext;
                                            else $MJTC_phoneext = $MJTC_field->defaultvalue;
                                            echo wp_kses(MJTC_formfield::MJTC_text('phoneext', $MJTC_phoneext, array('class' => 'inputbox mjtc-support-form-field-input', 'data-validation' => ($MJTC_field->required) ? 'required' : '', 'placeholder'=> majesticsupport::MJTC_getVarValue($MJTC_field->placeholder)) + ($MJTC_field->readonly ? ['readonly' => 'readonly'] : [])), MJTC_ALLOWED_TAGS);
                                        ?>
                                    </div>
                                    <?php if(!empty($MJTC_field->description)): ?>
                                        <div class="mjtc-support-from-field-description">
                                            <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->description)); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php
                                break;
                            case 'department':
                                if($MJTC_fieldcounter % 2 == 0){
                                    if($MJTC_fieldcounter != 0){
                                        echo wp_kses($MJTC_closingTag, MJTC_ALLOWED_TAGS);
                                    }
                                    echo wp_kses($MJTC_openingTag, MJTC_ALLOWED_TAGS);
                                }
                                $MJTC_fieldcounter++;
                                ?>
                                <div class="mjtc-support-from-field-wrp <?php echo esc_attr($MJTC_visibleclass); ?>">
                                    <div class="mjtc-support-from-field-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->fieldtitle)); ?>&nbsp;<?php if($MJTC_field->required == 1) echo wp_kses($MJTC_requiredTxt, MJTC_ALLOWED_TAGS); ?></div>
                                    <div class="mjtc-support-from-field mjtc-support-form-field-select">
                                        <?php 
    										$MJTC_disabled ="";
                                            if(isset($MJTC_formdata['departmentid'])) $MJTC_departmentid = $MJTC_formdata['departmentid'];
                                            elseif(isset(majesticsupport::$_data[0]->departmentid)) $MJTC_departmentid = majesticsupport::$_data[0]->departmentid;
                                            elseif(MJTC_request::MJTC_getVar('departmentid','get',0) > 0) $MJTC_departmentid = MJTC_request::MJTC_getVar('departmentid','get');
                                            else $MJTC_departmentid = MJTC_includer::MJTC_getModel('department')->getDefaultDepartmentID();
    										if(isset(majesticsupport::$_data['formid'])){
    											if(in_array('multiform',majesticsupport::$_active_addons)){
    												$MJTC_departmentid = MJTC_includer::MJTC_getModel('multiform')->getDepartmentIdByFormId(majesticsupport::$_data['formid']);
    												if($MJTC_departmentid > 0){
    												}
    											}
    											
    										}
                                            // code for visible field
                                            if ($MJTC_field->visible_field != null) {
                                                $MJTC_visibleparams = MJTC_includer::MJTC_getModel('fieldordering')->MJTC_getDataForVisibleField($MJTC_field->visible_field);
                                                // For default function (initial value setting)
                                                if (!empty($MJTC_visibleparams)) {
                                                    $MJTC_wpnonce = wp_create_nonce("is-field-required-" . $MJTC_field->visible_field);
                                                    $MJTC_jsObject = wp_json_encode($MJTC_visibleparams);
                                                    // Build JS function without esc_js on JSON
                                                    $MJTC_defaultFunc = "MJTC_getDataForVisibleField('" . esc_js($MJTC_wpnonce) . "', '" . esc_js($MJTC_departmentid) . "', '" . esc_js($MJTC_field->visible_field) . "', " . $MJTC_jsObject . ");";
                                                    // Attach default function on document ready
                                                    if (!isset(majesticsupport::$_data[0]->id)) {
                                                        $majesticsupport_js = "
                                                            jQuery(document).ready(function(){
                                                                ".$MJTC_defaultFunc."
                                                            });
                                                        ";
                                                        wp_add_inline_script('majestic-support-cmain-js', $majesticsupport_js);
                                                    }
                                                }
                                            }
    										if($MJTC_disabled == ""){
    											echo wp_kses(MJTC_formfield::MJTC_select('departmentid', MJTC_includer::MJTC_getModel('department')->getDepartmentForCombobox(), $MJTC_departmentid, esc_html(__('Select', 'majestic-support')).' '.esc_html($MJTC_field->fieldtitle), array('class' => 'inputbox mjtc-support-select-field' . esc_attr($MJTC_readonlyclass), 'onchange' => $MJTC_VisibleFunction.' getHelpTopicByDepartment(this.value);', 'data-validation' => ($MJTC_field->required) ? 'required' : '') + ($MJTC_field->readonly ? ['tabindex' => '-1'] : [])), MJTC_ALLOWED_TAGS);
    										}else{
    											echo wp_kses(MJTC_formfield::MJTC_select('departmentid', MJTC_includer::MJTC_getModel('department')->getDepartmentForCombobox(), $MJTC_departmentid, esc_html(__('Select', 'majestic-support')).' '.esc_html($MJTC_field->fieldtitle), array('class' => 'inputbox mjtc-support-select-field', 'onchange' => $MJTC_VisibleFunction.' getHelpTopicByDepartment(this.value);', 'data-validation' => ($MJTC_field->required) ? 'required' : '','disabled'=>'disabled')), MJTC_ALLOWED_TAGS);
    										}
                                        ?>
                                    </div>
                                    <?php if(!empty($MJTC_field->description)): ?>
                                        <div class="mjtc-support-from-field-description">
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
                                if($MJTC_fieldcounter % 2 == 0){
                                    if($MJTC_fieldcounter != 0){
                                        echo wp_kses($MJTC_closingTag, MJTC_ALLOWED_TAGS);
                                    }
                                    echo wp_kses($MJTC_openingTag, MJTC_ALLOWED_TAGS);
                                }
                                $MJTC_fieldcounter++;
                                ?>
                                <div class="mjtc-support-from-field-wrp <?php echo esc_attr($MJTC_visibleclass); ?>">
                                    <div class="mjtc-support-from-field-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->fieldtitle)); ?>&nbsp;<?php if($MJTC_field->required == 1) echo wp_kses($MJTC_requiredTxt, MJTC_ALLOWED_TAGS); ?></div>
                                    <div class="mjtc-support-from-field mjtc-support-form-field-select" id="helptopic">
                                        <?php
                                            if(isset($MJTC_formdata['helptopicid'])) $MJTC_helptopicid = $MJTC_formdata['helptopicid'];
                                            elseif(isset(majesticsupport::$_data[0]->helptopicid)) $MJTC_helptopicid = majesticsupport::$_data[0]->helptopicid;
                                            elseif(MJTC_request::MJTC_getVar('helptopicid','get',0) > 0) $MJTC_helptopicid = MJTC_request::MJTC_getVar('helptopicid','get');
                                            else $MJTC_helptopicid = '';
                                            if (isset($MJTC_departmentid)) {
                                                $MJTC_dep_id = $MJTC_departmentid;
                                            } else{
                                                $MJTC_dep_id = 0;
                                            }
                                            echo wp_kses(MJTC_formfield::MJTC_select('helptopicid', MJTC_includer::MJTC_getModel('helptopic')->getHelpTopicsForCombobox($MJTC_dep_id), $MJTC_helptopicid, esc_html(__('Select', 'majestic-support')).' '.esc_html($MJTC_field->fieldtitle), array('class ' => 'mjtc-support-select-field' .esc_attr($MJTC_readonlyclass), 'data-validation' => ($MJTC_field->required) ? 'required' : '', 'onchange' => $MJTC_VisibleFunction) + ($MJTC_field->readonly ? ['tabindex' => '-1'] : [])), MJTC_ALLOWED_TAGS);
                                        ?>
                                    </div>
                                    <?php if(!empty($MJTC_field->description)): ?>
                                        <div class="mjtc-support-from-field-description">
                                            <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->description)); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php
                                break;
                            case 'product':
                                if($MJTC_fieldcounter % 2 == 0){
                                    if($MJTC_fieldcounter != 0){
                                        echo wp_kses($MJTC_closingTag, MJTC_ALLOWED_TAGS);
                                    }
                                    echo wp_kses($MJTC_openingTag, MJTC_ALLOWED_TAGS);
                                }
                                $MJTC_fieldcounter++;
                                ?>
                                <div class="mjtc-support-from-field-wrp <?php echo esc_attr($MJTC_visibleclass); ?>">
                                    <div class="mjtc-support-from-field-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->fieldtitle)); ?>&nbsp;<?php if($MJTC_field->required == 1) echo wp_kses($MJTC_requiredTxt, MJTC_ALLOWED_TAGS); ?></div>
                                    <div class="mjtc-support-from-field mjtc-support-form-field-select" id="product">
                                        <?php
                                            if(isset($MJTC_formdata['productid'])) $MJTC_productid = $MJTC_formdata['productid'];
                                            elseif(isset(majesticsupport::$_data[0]->productid)) $MJTC_productid = majesticsupport::$_data[0]->productid;
                                            else $MJTC_productid = '';
                                            echo wp_kses(MJTC_formfield::MJTC_select('productid', MJTC_includer::MJTC_getModel('product')->getProductForCombobox(), $MJTC_productid, esc_html(__('Select', 'majestic-support')).' '.esc_html($MJTC_field->fieldtitle), array('class ' => 'mjtc-support-select-field' .esc_attr($MJTC_readonlyclass), 'data-validation' => ($MJTC_field->required) ? 'required' : '', 'onchange' => $MJTC_VisibleFunction) + ($MJTC_field->readonly ? ['tabindex' => '-1'] : [])), MJTC_ALLOWED_TAGS);
                                        ?>
                                    </div>
                                    <?php if(!empty($MJTC_field->description)): ?>
                                        <div class="mjtc-support-from-field-description">
                                            <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->description)); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php
                                break;
                            case 'priority':
                                if($MJTC_fieldcounter % 2 == 0){
                                    if($MJTC_fieldcounter != 0){
                                        echo wp_kses($MJTC_closingTag, MJTC_ALLOWED_TAGS);
                                    }
                                    echo wp_kses($MJTC_openingTag, MJTC_ALLOWED_TAGS);
                                }
                                $MJTC_fieldcounter++;
                                ?>
                                <div class="mjtc-support-from-field-wrp <?php echo esc_attr($MJTC_visibleclass); ?>">
                                    <div class="mjtc-support-from-field-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->fieldtitle)); ?>&nbsp;<?php if($MJTC_field->required == 1) echo wp_kses($MJTC_requiredTxt, MJTC_ALLOWED_TAGS); ?></div>
                                    <div class="mjtc-support-from-field mjtc-support-form-field-select">
                                        <?php
                                            if(isset($MJTC_formdata['priorityid'])) $MJTC_priorityid = $MJTC_formdata['priorityid'];
                                            elseif(isset(majesticsupport::$_data[0]->priorityid)) $MJTC_priorityid = majesticsupport::$_data[0]->priorityid;
                                            else $MJTC_priorityid = MJTC_includer::MJTC_getModel('priority')->getDefaultPriorityID();
                                            if (!empty($MJTC_visibleparams)) {
                                                $MJTC_wpnonce = wp_create_nonce("is-field-required-" . $MJTC_field->visible_field);
                                                // Build JS function without esc_js on JSON
                                                $MJTC_jsObject = wp_json_encode($MJTC_visibleparams);
                                                $MJTC_defaultFunc = "MJTC_getDataForVisibleField('" . esc_js($MJTC_wpnonce) . "', '" . esc_js($MJTC_priorityid) . "', '" . esc_js($MJTC_field->visible_field) . "', " . $MJTC_jsObject . ");";
                                                // Attach default function on document ready
                                                if (!isset(majesticsupport::$_data[0]->id)) {
                                                    $majesticsupport_js = "
                                                        jQuery(document).ready(function(){
                                                            ".$MJTC_defaultFunc."
                                                        });
                                                    ";
                                                    wp_add_inline_script('majestic-support-cmain-js', $majesticsupport_js);
                                                }
                                            }
                                            echo wp_kses(MJTC_formfield::MJTC_select('priorityid', MJTC_includer::MJTC_getModel('priority')->getPriorityForCombobox(), $MJTC_priorityid, esc_html(__('Select', 'majestic-support')).' '.esc_html($MJTC_field->fieldtitle), array('class' => 'inputbox mjtc-support-select-field' . esc_attr($MJTC_readonlyclass), 'data-validation' => ($MJTC_field->required) ? 'required' : '', 'onchange' => $MJTC_VisibleFunction) + ($MJTC_field->readonly ? ['tabindex' => '-1'] : [])), MJTC_ALLOWED_TAGS);
                                        ?>
                                    </div>
                                    <?php if(!empty($MJTC_field->description)): ?>
                                        <div class="mjtc-support-from-field-description">
                                            <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->description)); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php
                                break;
                            case 'subject':
                                if($MJTC_fieldcounter % 2 == 0){
                                    if($MJTC_fieldcounter != 0){
                                        echo '</div>';
                                    }
                                    echo '<div class="mjtc-support-add-form-wrapper">';
                                }
                                $MJTC_fieldcounter++;
                                ?>
                                <div class="mjtc-support-from-field-wrp mjtc-support-from-field-wrp-full-width">
                                    <div class="mjtc-support-from-field-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->fieldtitle)); ?>&nbsp;<span style="color:red">*</span></div>
                                    <div class="mjtc-support-from-field">
                                        <?php
                                            if(isset($MJTC_formdata['subject'])) $MJTC_subject = $MJTC_formdata['subject'];
                                            elseif(isset(majesticsupport::$_data[0]->subject)) $MJTC_subject = majesticsupport::$_data[0]->subject;
                                            else $MJTC_subject = $MJTC_field->defaultvalue;
                                            echo wp_kses(MJTC_formfield::MJTC_text('subject', $MJTC_subject, array('class' => 'inputbox mjtc-support-form-field-input', 'data-validation' => 'required', 'onchange' => $MJTC_VisibleFunction, 'placeholder'=> majesticsupport::MJTC_getVarValue($MJTC_field->placeholder)) + ($MJTC_field->readonly ? ['readonly' => 'readonly'] : [])), MJTC_ALLOWED_TAGS);
                                        ?>
                                    </div>
                                    <?php if(!empty($MJTC_field->description)): ?>
                                        <div class="mjtc-support-from-field-description">
                                            <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->description)); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php
                                break;
                            case 'issuesummary':
                                if($MJTC_fieldcounter != 0){
                                    echo wp_kses($MJTC_closingTag, MJTC_ALLOWED_TAGS);
                                    $MJTC_fieldcounter = 0;
                                }
                                ?>
                                <div class="mjtc-support-from-field-wrp mjtc-support-from-field-wrp-full-width <?php echo esc_attr($MJTC_visibleclass); ?>">
                                    <div class="mjtc-support-from-field-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->fieldtitle)); ?>&nbsp;<?php if($MJTC_field->required == 1) echo wp_kses($MJTC_requiredTxt, MJTC_ALLOWED_TAGS); ?></div>
                                    <div class="mjtc-support-from-field">
                                        <?php
                                            if(isset($MJTC_formdata['message'])) $MJTC_message = wpautop(wptexturize(MJTC_majesticsupportphplib::MJTC_stripslashes($MJTC_formdata['message'])));
                                            elseif(isset(majesticsupport::$_data[0]->message)) $MJTC_message = majesticsupport::$_data[0]->message;
                                            else $MJTC_message = $MJTC_field->defaultvalue;
                                            if ($MJTC_field->readonly) {
                                                echo wp_kses(MJTC_formfield::MJTC_textarea('mjsupport_message', $MJTC_message, array('class' => 'inputbox mjtc-form-textarea-field one', 'rows' => 5, 'cols' => 25, 'placeholder'=> majesticsupport::MJTC_getVarValue($MJTC_field->placeholder), 'readonly'=> 'readonly')), MJTC_ALLOWED_TAGS);
                                            } else {
                                                $MJTC_message = is_string( $MJTC_message ) ? $MJTC_message : '';
                                                wp_editor($MJTC_message, 'mjsupport_message', array('media_buttons' => false));
                                            }
                                            /*
                                            * Use following settings for minimal editor as all are offering
                                            */
                                        ?>
                                    </div>
                                    <?php if(!empty($MJTC_field->description)): ?>
                                        <div class="mjtc-support-from-field-description">
                                            <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->description)); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php
                                break;
                            case 'attachments':
                                if($MJTC_fieldcounter != 0){
                                    echo wp_kses($MJTC_closingTag, MJTC_ALLOWED_TAGS);
                                    $MJTC_fieldcounter = 0;
                                }
                                ?>
                                <div class="mjtc-support-reply-attachments"><!-- Attachments -->
                                    <div class="mjtc-attachment-field-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->fieldtitle)); ?><?php if($MJTC_field->required == 1) echo wp_kses($MJTC_requiredTxt, MJTC_ALLOWED_TAGS); ?></div>
                                    <?php
                                    if(isset(majesticsupport::$_data[5]) && count(majesticsupport::$_data[5]) > 0){
                                        $MJTC_attachmentreq = '';
                                    }else{
                                        $MJTC_attachmentreq = $MJTC_field->required == 1 ? 'required' : '';
                                    }
                                    ?>
                                    <div class="mjtc-attachment-field">
                                        <div class="tk_attachment_value_wrapperform tk_attachment_user_reply_wrapper">
                                            <span class="tk_attachment_value_text">
                                                <input type="file" class="inputbox mjtc-attachment-inputbox" name="filename[]" onchange="MJTC_uploadfile(this, '<?php echo esc_js(majesticsupport::$_config['file_maximum_size']); ?>', '<?php echo esc_js(majesticsupport::$_config['file_extension']); ?>');" size="20" data-validation="<?php echo esc_attr($MJTC_attachmentreq); ?>" />
                                                <span class='tk_attachment_remove'></span>
                                            </span>
                                        </div>
                                        <span class="tk_attachments_configform">
                                             <?php 
                                             $MJTC_tktdata = esc_html(__('Maximum File Size', 'majestic-support')).' (' . esc_html(majesticsupport::$_config['file_maximum_size']).'KB)<br>'.esc_html(__('File Extension Type', 'majestic-support')).' (' . esc_html(majesticsupport::$_config['file_extension']) . ')';
                                             echo wp_kses($MJTC_tktdata, MJTC_ALLOWED_TAGS);
                                            ?>
                                        </span>
                                        <span id="tk_attachment_add" data-ident="tk_attachment_user_reply_wrapper" class="tk_attachments_addform"><?php echo esc_html(__('Add more', 'majestic-support')); ?></span>
                                    </div>
                                    <?php 
                                    if (!empty(majesticsupport::$_data[5])) {
                                        foreach (majesticsupport::$_data[5] AS $MJTC_attachment) {
                                            echo wp_kses('
                                            <div class="mjtc-support-attached-files-wrp">
                                                <div class="mjtc_supportattachment">
                                                    ' . esc_html($MJTC_attachment->filename) . ' ( ' . esc_html($MJTC_attachment->filesize) . ' ) ' . '
                                                </div>
                                                <a class="mjtc-support-delete-attachment" href="'.wp_nonce_url(majesticsupport::makeUrl(array('mjsmod'=>'attachment', 'task'=>'deleteattachment', 'action'=>'mstask', 'id'=>$MJTC_attachment->id, 'tikcetid'=>majesticsupport::$_data[0]->id, 'mspageid'=>majesticsupport::getPageid())),'delete-attachement-'.$MJTC_attachment->id) . '">' . esc_html(__('Remove','majestic-support')) . '</a>
                                            </div>', MJTC_ALLOWED_TAGS);
                                        }
                                    }
                                    if(!empty($MJTC_field->description)): ?>
                                        <div class="mjtc-support-from-field-description">
                                            <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->description)); ?>
                                        </div>
                                        <?php 
                                    endif; ?>
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
                                    if($MJTC_fieldcounter % 2 == 0){
                                        if($MJTC_fieldcounter != 0){
                                            echo wp_kses($MJTC_closingTag, MJTC_ALLOWED_TAGS);
                                        }
                                        echo wp_kses($MJTC_openingTag, MJTC_ALLOWED_TAGS);
                                    }
                                    $MJTC_fieldcounter++;

                                    $MJTC_orderlist = array();
    								if(get_current_user_id() > 0){
    									foreach(wc_get_orders(array('customer_id'=>MJTC_includer::MJTC_getObjectClass('user')->MJTC_wpuid(),'post_status' => 'wc-completed')) as $MJTC_order){ // wp uid because of woocommerce store wp uid
    										$MJTC_orderlist[] = (object) array('id' => $MJTC_order->get_id(),'text'=>'#'.esc_html($MJTC_order->get_id()).' - '.esc_html($MJTC_order->get_date_created()->date_i18n(wc_date_format())));
    									}
    								}
                                    if(isset($MJTC_formdata['wcorderid'])) $MJTC_wcorderid = $MJTC_formdata['wcorderid'];
                                    elseif(isset(majesticsupport::$_data[0]->wcorderid)) $MJTC_wcorderid = majesticsupport::$_data[0]->wcorderid;
                                    else $MJTC_wcorderid = '';  ?>
                                    <div class="mjtc-support-from-field-wrp <?php echo esc_attr($MJTC_visibleclass); ?>">
                                        <div class="mjtc-support-from-field-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->fieldtitle)); ?>&nbsp;<?php if($MJTC_field->required == 1) echo wp_kses($MJTC_requiredTxt, MJTC_ALLOWED_TAGS); ?></div>
                                        <div class="mjtc-support-from-field mjtc-support-form-field-select">
                                            <?php echo wp_kses(MJTC_formfield::MJTC_select('wcorderid', $MJTC_orderlist, $MJTC_wcorderid, esc_html(__('Select', 'majestic-support')).' '.esc_html($MJTC_field->fieldtitle), array('class' => 'inputbox mjtc-support-select-field', 'data-validation' => ($MJTC_field->required) ? 'required' : '')), MJTC_ALLOWED_TAGS); ?>
                                        </div>
                                        <?php if(!empty($MJTC_field->description)): ?>
                                            <div class="mjtc-support-from-field-description">
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
                                    if($MJTC_fieldcounter % 2 == 0){
                                        if($MJTC_fieldcounter != 0){
                                            echo wp_kses($MJTC_closingTag, MJTC_ALLOWED_TAGS);
                                        }
                                        echo wp_kses($MJTC_openingTag, MJTC_ALLOWED_TAGS);
                                    }
                                    $MJTC_fieldcounter++;

                                    $MJTC_itemlist = array();
                                    if(isset($MJTC_formdata['wcproductid'])) $MJTC_wcproductid = $MJTC_formdata['wcproductid'];
                                    elseif(isset(majesticsupport::$_data[0]->wcproductid)) $MJTC_wcproductid = majesticsupport::$_data[0]->wcproductid;
                                    else $MJTC_wcproductid = '';  ?>
                                    <div class="mjtc-support-from-field-wrp <?php echo esc_attr($MJTC_visibleclass); ?>">
                                        <div class="mjtc-support-from-field-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->fieldtitle)); ?>&nbsp;<?php if($MJTC_field->required == 1) echo wp_kses($MJTC_requiredTxt, MJTC_ALLOWED_TAGS); ?></div>
                                        <div class="mjtc-support-from-field mjtc-support-form-field-select" id="wcproductid-wrap">
                                            <?php echo wp_kses(MJTC_formfield::MJTC_select('wcproductid', $MJTC_itemlist, $MJTC_wcproductid, esc_html(__('Select', 'majestic-support')).' '.esc_html($MJTC_field->fieldtitle), array('class' => 'inputbox mjtc-support-select-field', 'data-validation' => ($MJTC_field->required) ? 'required' : '')), MJTC_ALLOWED_TAGS); ?>
                                        </div>
                                        <?php if(!empty($MJTC_field->description)): ?>
                                            <div class="mjtc-support-from-field-description">
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
                                    if($MJTC_fieldcounter % 2 == 0){
                                        if($MJTC_fieldcounter != 0){
                                            echo wp_kses($MJTC_closingTag, MJTC_ALLOWED_TAGS);
                                        }
                                        echo wp_kses($MJTC_openingTag, MJTC_ALLOWED_TAGS);
                                    }
                                    $MJTC_fieldcounter++;

                                    $MJTC_itemlist = array();

                                    if(isset($MJTC_formdata['eddorderid'])) $MJTC_eddorderid = $MJTC_formdata['eddorderid'];
                                    elseif(isset(majesticsupport::$_data[0]->eddorderid)) $MJTC_eddorderid = majesticsupport::$_data[0]->eddorderid;
                                    elseif(isset(majesticsupport::$_data['edd_order_id'])) $MJTC_eddorderid = majesticsupport::$_data['edd_order_id'];
                                    $MJTC_user_id = MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid();
                                    if(is_numeric($MJTC_user_id) && $MJTC_user_id > 0){
                                        $MJTC_user_purchases = edd_get_users_purchases($MJTC_user_id);
                                        $MJTC_user_purchase_array = array();
                                        if (is_array($MJTC_user_purchases) || $MJTC_user_purchases instanceof Countable) {
                                            foreach ($MJTC_user_purchases AS $MJTC_user_purchase) {
                                                $MJTC_user_purchase_array[] = (object) array('id' => $MJTC_user_purchase->ID, 'text' => '#'.esc_html($MJTC_user_purchase->ID).'&nbsp;('. esc_html(__('Dated','majestic-support')).':&nbsp;' .date_i18n(majesticsupport::$_config['date_format'], MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_user_purchase->post_date)).')');
                                            }
                                        }
                                         ?>
                                        <div class="mjtc-support-from-field-wrp <?php echo esc_attr($MJTC_visibleclass); ?>">
                                            <div class="mjtc-support-from-field-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->fieldtitle)); ?>&nbsp;<?php if($MJTC_field->required == 1) echo wp_kses($MJTC_requiredTxt, MJTC_ALLOWED_TAGS); ?></div>
                                            <div class="mjtc-support-from-field mjtc-support-form-field-select" id="eddorderid-wrap">
                                                <?php echo wp_kses(MJTC_formfield::MJTC_select('eddorderid', $MJTC_user_purchase_array, $MJTC_eddorderid, esc_html(__('Select', 'majestic-support')).' '.esc_html($MJTC_field->fieldtitle), array('class' => 'inputbox mjtc-support-select-field', 'data-validation' => ($MJTC_field->required) ? 'required' : '')), MJTC_ALLOWED_TAGS); ?>
                                            </div>
                                            <?php if(!empty($MJTC_field->description)): ?>
                                                <div class="mjtc-support-from-field-description">
                                                    <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->description)); ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php
                                    }else{ ?>
                                        <div class="mjtc-support-from-field-wrp <?php echo esc_attr($MJTC_visibleclass); ?>">
                                            <div class="mjtc-support-from-field-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->fieldtitle)); ?>&nbsp;<?php if($MJTC_field->required == 1) echo wp_kses($MJTC_requiredTxt, MJTC_ALLOWED_TAGS); ?></div>
                                            <div class="mjtc-support-from-field mjtc-support-form-field-select" id="eddorderid-wrap">
                                                <?php  echo wp_kses(MJTC_formfield::MJTC_text('eddorderid', $MJTC_eddorderid, array('class' => 'inputbox mjtc-support-form-field-input', 'data-validation' => ($MJTC_field->required) ? 'required' : '', 'placeholder'=> majesticsupport::MJTC_getVarValue($MJTC_field->placeholder))), MJTC_ALLOWED_TAGS); ?>
                                            </div>
                                            <?php if(!empty($MJTC_field->description)): ?>
                                                <div class="mjtc-support-from-field-description">
                                                    <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->description)); ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php
                                    }
                                    break;
                                case 'eddproductid':
                                    if(!in_array('easydigitaldownloads', majesticsupport::$_active_addons)){
                                        break;
                                    }
                                    if(!class_exists('Easy_Digital_Downloads')){
                                        break;
                                    }
                                    if(is_numeric($MJTC_user_id) && $MJTC_user_id > 0){
                                        if($MJTC_fieldcounter % 2 == 0){
                                            if($MJTC_fieldcounter != 0){
                                                echo wp_kses($MJTC_closingTag, MJTC_ALLOWED_TAGS);
                                            }
                                            echo wp_kses($MJTC_openingTag, MJTC_ALLOWED_TAGS);
                                        }
                                        $MJTC_fieldcounter++;

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
                                        <div class="mjtc-support-from-field-wrp <?php echo esc_attr($MJTC_visibleclass); ?>">
                                            <div class="mjtc-support-from-field-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->fieldtitle)); ?>&nbsp;<?php if($MJTC_field->required == 1) echo wp_kses($MJTC_requiredTxt, MJTC_ALLOWED_TAGS); ?></div>
                                            <div class="mjtc-support-from-field" id="eddproductid-wrap">
                                                <?php echo wp_kses(MJTC_formfield::MJTC_select('eddproductid', $MJTC_order_products_array, $MJTC_eddproductid, esc_html(__('Select', 'majestic-support')).' '.esc_html($MJTC_field->fieldtitle), array('class' => 'inputbox mjtc-form-select-field', 'data-validation' => ($MJTC_field->required) ? 'required' : '')), MJTC_ALLOWED_TAGS); ?>
                                            </div>
                                            <?php if(!empty($MJTC_field->description)): ?>
                                                <div class="mjtc-support-from-field-description">
                                                    <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->description)); ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php
                                }
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
                                    if($MJTC_fieldcounter % 2 == 0){
                                        if($MJTC_fieldcounter != 0){
                                            echo wp_kses($MJTC_closingTag, MJTC_ALLOWED_TAGS);
                                        }
                                        echo wp_kses($MJTC_openingTag, MJTC_ALLOWED_TAGS);
                                    }
                                    $MJTC_fieldcounter++;
                                    $MJTC_license_key_array = array();
                                    if($MJTC_eddorderid != '' && is_numeric($MJTC_eddorderid)){
                                        $MJTC_license = EDD_Software_Licensing::instance();
                                        $MJTC_result = $MJTC_license->get_licenses_of_purchase($MJTC_eddorderid);
                                        foreach ($MJTC_result AS $MJTC_license_record) {
                                            $MJTC_license_record_licensekey = $MJTC_license->get_license_key($MJTC_license_record->ID);
                                            if($MJTC_license_record_licensekey != ''){
                                                $MJTC_license_key_array[] = (object) array('id' => $MJTC_license_record_licensekey,'text' => $MJTC_license_record_licensekey);
                                            }
                                        }
                                    }

                                    $MJTC_itemlist = array();
                                    if(isset($MJTC_formdata['eddlicensekey'])) $MJTC_eddlicensekey = $MJTC_formdata['eddlicensekey'];
                                    elseif(isset(majesticsupport::$_data[0]->eddlicensekey)) $MJTC_eddlicensekey = majesticsupport::$_data[0]->eddlicensekey;
                                    else $MJTC_eddlicensekey = '';
                                    $MJTC_user_id = MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid();
                                    if(is_numeric($MJTC_user_id) && $MJTC_user_id > 0){
                                    ?>
                                        <div class="mjtc-support-from-field-wrp <?php echo esc_attr($MJTC_visibleclass); ?>">
                                            <div class="mjtc-support-from-field-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->fieldtitle)); ?>&nbsp;<?php if($MJTC_field->required == 1) echo wp_kses($MJTC_requiredTxt, MJTC_ALLOWED_TAGS); ?></div>
                                            <div class="mjtc-support-from-field mjtc-support-form-field-select" id="eddlicensekey-wrap">
                                                <?php echo wp_kses(MJTC_formfield::MJTC_select('eddlicensekey', $MJTC_license_key_array, $MJTC_eddlicensekey, esc_html(__('Select', 'majestic-support')).' '.esc_html($MJTC_field->fieldtitle), array('class' => 'inputbox mjtc-support-select-field', 'data-validation' => ($MJTC_field->required) ? 'required' : '')), MJTC_ALLOWED_TAGS); ?>
                                            </div>
                                            <?php if(!empty($MJTC_field->description)): ?>
                                                <div class="mjtc-support-from-field-description">
                                                    <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->description)); ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <?php
                                    }else{
                                        ?>
                                        <div class="mjtc-support-from-field-wrp <?php echo esc_attr($MJTC_visibleclass); ?>">
                                            <div class="mjtc-support-from-field-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->fieldtitle)); ?>&nbsp;<?php if($MJTC_field->required == 1) echo wp_kses($MJTC_requiredTxt, MJTC_ALLOWED_TAGS); ?></div>
                                            <div class="mjtc-support-from-field mjtc-support-form-field-select" id="eddlicensekey-wrap">
                                                <?php  echo wp_kses(MJTC_formfield::MJTC_text('eddlicensekey', $MJTC_eddlicensekey, array('class' => 'inputbox mjtc-support-form-field-input', 'data-validation' => ($MJTC_field->required) ? 'required' : '', 'placeholder'=> majesticsupport::MJTC_getVarValue($MJTC_field->placeholder))), MJTC_ALLOWED_TAGS); ?>
                                            </div>
                                            <?php if(!empty($MJTC_field->description)): ?>
                                                <div class="mjtc-support-from-field-description">
                                                    <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->description)); ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php
                                    }
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
                                    if($MJTC_fieldcounter % 2 == 0){
                                        if($MJTC_fieldcounter != 0){
                                            echo wp_kses($MJTC_closingTag, MJTC_ALLOWED_TAGS);
                                        }
                                        echo wp_kses($MJTC_openingTag, MJTC_ALLOWED_TAGS);
                                    }
                                    $MJTC_fieldcounter++;

                                    if(isset($MJTC_formdata['envatopurchasecode'])) $MJTC_envatopurchasecode = $MJTC_formdata['envatopurchasecode'];
                                    elseif(isset($MJTC_envlicense['license'])) $MJTC_envatopurchasecode = $MJTC_envlicense['license'];
                                    else $MJTC_envatopurchasecode = '';  ?>
                                    <div class="mjtc-support-from-field-wrp <?php echo esc_attr($MJTC_visibleclass); ?>">
                                        <div class="mjtc-support-from-field-title">
                                            <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->fieldtitle)); ?>&nbsp;<?php if($MJTC_field->required == 1) echo wp_kses($MJTC_requiredTxt, MJTC_ALLOWED_TAGS); ?>
                                        </div>
                                        <div class="mjtc-support-from-field mjtc-support-form-field-select" id="envatopurchasecode-wrap">
                                            <?php echo wp_kses(MJTC_formfield::MJTC_text('envatopurchasecode', $MJTC_envatopurchasecode, array('class' => 'inputbox mjtc-support-form-field-input','data-validation'=>($MJTC_field->required ? 'required' : ''), 'placeholder'=> majesticsupport::MJTC_getVarValue($MJTC_field->placeholder))), MJTC_ALLOWED_TAGS); ?>
                                        </div>
                                        <?php if(!empty($MJTC_field->description)): ?>
                                            <div class="mjtc-support-from-field-description">
                                                <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->description)); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <?php
                                    break;
                            default:
                                if ($MJTC_field->userfieldtype != 'termsandconditions') {
                                    $MJTC_customfields = MJTC_includer::MJTC_getObjectClass('customfields')->MJTC_formCustomFields($MJTC_field);
                                    if (isset($MJTC_customfields)) {
                                        echo wp_kses($MJTC_customfields, MJTC_ALLOWED_TAGS);
                                    }
                                }
                                break;
                        }

                    endforeach;
                    if($MJTC_fieldcounter != 0){
                        echo wp_kses($MJTC_closingTag, MJTC_ALLOWED_TAGS); // close extra div open in user field
                    }
                    $MJTC_tktufield = '<input type="hidden" id="userfeilds_total" name="userfeilds_total"  value="' . esc_attr($MJTC_i) . '"  />';
                    echo wp_kses($MJTC_tktufield, MJTC_ALLOWED_TAGS);
                    ?>
                    <?php echo wp_kses(MJTC_formfield::MJTC_hidden('id', isset(majesticsupport::$_data[0]->id) ? majesticsupport::$_data[0]->id : ''), MJTC_ALLOWED_TAGS); ?>
                    <?php echo wp_kses(MJTC_formfield::MJTC_hidden('status', isset(majesticsupport::$_data[0]->status) ? majesticsupport::$_data[0]->status : ''), MJTC_ALLOWED_TAGS); ?>
                    <?php echo wp_kses(MJTC_formfield::MJTC_hidden('multiformid', isset(majesticsupport::$_data['formid']) ? majesticsupport::$_data['formid'] : '1'), MJTC_ALLOWED_TAGS); ?>
                    <?php echo wp_kses(MJTC_formfield::MJTC_hidden('attachmentdir', isset(majesticsupport::$_data[0]->attachmentdir) ? majesticsupport::$_data[0]->attachmentdir : ''), MJTC_ALLOWED_TAGS); ?>
                    <?php echo wp_kses(MJTC_formfield::MJTC_hidden('ticketid', isset(majesticsupport::$_data[0]->ticketid) ? majesticsupport::$_data[0]->ticketid : ''), MJTC_ALLOWED_TAGS); ?>
                    <?php echo wp_kses(MJTC_formfield::MJTC_hidden('created', isset(majesticsupport::$_data[0]->created) ? majesticsupport::$_data[0]->created : ''), MJTC_ALLOWED_TAGS); ?>
                    <?php echo wp_kses(MJTC_formfield::MJTC_hidden('uid', MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid()), MJTC_ALLOWED_TAGS); ?>
                    <?php echo wp_kses(MJTC_formfield::MJTC_hidden('updated', isset(majesticsupport::$_data[0]->updated) ? majesticsupport::$_data[0]->updated : ''), MJTC_ALLOWED_TAGS); ?>
                    <?php echo wp_kses(MJTC_formfield::MJTC_hidden('form_request', 'majesticsupport'), MJTC_ALLOWED_TAGS); ?>
                    <?php echo wp_kses(MJTC_formfield::MJTC_hidden('mspageid', get_the_ID()), MJTC_ALLOWED_TAGS); ?>
                    <?php
                    if(isset($MJTC_paidsupportid)){
                        echo wp_kses(MJTC_formfield::MJTC_hidden('paidsupportid', $MJTC_paidsupportid), MJTC_ALLOWED_TAGS);
                    }
                ?>
                <?php
                foreach (majesticsupport::$_data['fieldordering'] AS $MJTC_field):
                    $MJTC_visibleclass = "";
                    if (!empty($MJTC_field->visibleparams) && $MJTC_field->visibleparams != '[]'){
                        $MJTC_visibleclass = ' visible ';
                    }
                    $MJTC_VisibleFunction = '';
                    if ($MJTC_field->visible_field != null) {
                        $MJTC_visibleparams = MJTC_includer::MJTC_getModel('fieldordering')->MJTC_getDataForVisibleField($MJTC_field->visible_field);
                        if (!empty($MJTC_visibleparams)) {
                            $MJTC_wpnonce = wp_create_nonce("is-field-required-".$MJTC_field->visible_field);
                            $MJTC_jsObject = wp_json_encode($MJTC_visibleparams);
                            $MJTC_VisibleFunction = " MJTC_getDataForVisibleField('".$MJTC_wpnonce."', this.value, '".esc_js($MJTC_field->visible_field)."', ".$MJTC_jsObject.");";
                        }
                    }
                    switch ($MJTC_field->field) {
                        case 'termsandconditions1':
                        case 'termsandconditions2':
                        case 'termsandconditions3':
                            if (isset(majesticsupport::$_data[0]->id)) {
                                break;
                            }
                            if (!empty($MJTC_field->userfieldparams)) {
                                $MJTC_obj_option = json_decode($MJTC_field->userfieldparams,true);

                                $MJTC_url = '#';
                                if( isset($MJTC_obj_option['termsandconditions_linktype']) && $MJTC_obj_option['termsandconditions_linktype'] == 1){
                                    $MJTC_url = $MJTC_obj_option['termsandconditions_link'];
                                }if( isset($MJTC_obj_option['termsandconditions_linktype']) && $MJTC_obj_option['termsandconditions_linktype'] == 2){
                                    $MJTC_url  = get_permalink($MJTC_obj_option['termsandconditions_page']);
                                }

                                $MJTC_link_start = '<a href="' . esc_url($MJTC_url) . '" class="termsandconditions_link_anchor" target="_blank" >';
                                $MJTC_link_end = '</a>';

                                if(MJTC_majesticsupportphplib::MJTC_strstr($MJTC_obj_option['termsandconditions_text'], '[link]') && MJTC_majesticsupportphplib::MJTC_strstr($MJTC_obj_option['termsandconditions_text'], '[/link]')){
                                    $MJTC_label_string = MJTC_majesticsupportphplib::MJTC_str_replace('[link]', $MJTC_link_start, $MJTC_obj_option['termsandconditions_text']);
                                    $MJTC_label_string = MJTC_majesticsupportphplib::MJTC_str_replace('[/link]', $MJTC_link_end, $MJTC_label_string);
                                }elseif($MJTC_obj_option['termsandconditions_linktype'] == 3){
                                    $MJTC_label_string = $MJTC_obj_option['termsandconditions_text'];
                                }else{
                                    $MJTC_label_string = $MJTC_link_start.$MJTC_obj_option['termsandconditions_text'].$MJTC_link_end;
                                }
                                $MJTC_c_field_required = '';
                                if($MJTC_field->required == 1){
                                    $MJTC_c_field_required = 'required';
                                }
                                // ticket terms and conditonions are required.
                                if($MJTC_field->fieldfor == 1){
                                    if (!isset($MJTC_field->visibleparams)) {
                                        $MJTC_c_field_required = 'required';
                                    } else {
                                        $MJTC_c_field_required = '';
                                    }
                                } ?>
                                <div class="mjtc-support-from-field-wrp mjtc-support-from-field-wrp-full-width mjtc-support-system-terms-and-condition-box ">
                                    <div class="mjtc-support-from-field mjtc-support-form-field-select" id="envatopurchasecode-wrap">
                                        <input type="checkbox" class="radiobutton mjtc-support-append-radio-btn" value="1" id="<?php echo esc_attr($MJTC_field->field); ?>" name="<?php echo esc_attr($MJTC_field->field) ?>" data-validation="<?php echo esc_attr($MJTC_c_field_required) ?>">
                                        <label for="<?php echo esc_attr($MJTC_field->field) ?>" id="foruf_checkbox1"><?php echo wp_kses($MJTC_label_string, MJTC_ALLOWED_TAGS) ?></label>
                                    </div>
                                </div>   
                                <?php
                            }
                            break;
                        default:
                            if ($MJTC_field->userfieldtype == 'termsandconditions') {
                                MJTC_includer::MJTC_getObjectClass('customfields')->MJTC_formCustomFields($MJTC_field);
                            }
                            break;
                    }
                    endforeach;
                    // captcha
                    $MJTC_google_recaptcha_3 = false;
                    if (MJTC_includer::MJTC_getObjectClass('user')->MJTC_isguest()) {
                        if (majesticsupport::$_config['show_captcha_on_visitor_from_ticket'] == 1) {  ?>
                        <div class="mjtc-support-from-field-wrp mjtc-support-from-field-wrp-full-width">
                            <div class="mjtc-support-from-field-title">
                                <?php echo esc_html(__('Captcha', 'majestic-support')); ?>
                            </div>
                            <div class="mjtc-support-from-field">
                                <?php
                                    if (majesticsupport::$_config['captcha_selection'] == 1) { // Google recaptcha
                                        $MJTC_error = null;
                                        if (majesticsupport::$_config['recaptcha_version'] == 1) {
                                            $MJTC_captchaTxt = '<div class="g-recaptcha" data-sitekey="'.esc_attr(majesticsupport::$_config['recaptcha_publickey']).'"></div>';
                                            echo wp_kses($MJTC_captchaTxt, MJTC_ALLOWED_TAGS);
                                        } else {
                                            $MJTC_google_recaptcha_3 = true;
                                        }
                                    } else { // own captcha
                                        $MJTC_captcha = new MJTC_captcha;
                                        echo wp_kses($MJTC_captcha->MJTC_getCaptchaForForm(), MJTC_ALLOWED_TAGS);
                                    }
                                    ?>
                            </div>
                        </div>
                        <?php
                        }
                    } ?>
                    <div class="mjtc-support-form-btn-wrp">
                        <?php
                        if($MJTC_google_recaptcha_3 == true && MJTC_includer::MJTC_getObjectClass('user')->MJTC_isguest()){ // to handle case of google recpatcha version 3
                            echo wp_kses(MJTC_formfield::MJTC_button('save', esc_html(__('Submit Ticket', 'majestic-support')), array('class' => 'mjtc-support-save-button g-recaptcha', 'data-callback' => 'onSubmit', 'data-action' => 'submit', 'data-sitekey' => esc_attr(majesticsupport::$_config['recaptcha_publickey']))), MJTC_ALLOWED_TAGS);
                        } else {
                            echo wp_kses(MJTC_formfield::MJTC_submitbutton('save', esc_html(__('Submit Ticket', 'majestic-support')), array('class' => 'mjtc-support-save-button')), MJTC_ALLOWED_TAGS);
                        } ?>
                        <a href="<?php echo esc_url(majesticsupport::makeUrl(array('mjsmod'=>'majesticsupport', 'mjslay'=>'controlpanel')));?>" class="mjtc-support-cancel-button">
                            <?php echo esc_html(__('Cancel','majestic-support'));?>
                        </a>
                    </div>
                </form>
                <?php endif; ?>
            </div>
            <?php
    } else {// User is guest
        $MJTC_redirect_url = majesticsupport::makeUrl(array('mjsmod'=>'ticket', 'mjslay'=>'addticket'));
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
