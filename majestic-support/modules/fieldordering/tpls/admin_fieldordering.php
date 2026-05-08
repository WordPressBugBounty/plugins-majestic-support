<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
$majesticsupport_js ="
    function resetFrom() {
        document.getElementById('title').value = '';
        document.getElementById('categoryid').value = '';
        document.getElementById('type').value = '';
        document.getElementById('majesticsupportform').submit();
    }
    jQuery(document).ready(function () {
        jQuery('a#userpopup').click(function (e) {
            e.preventDefault();
            jQuery('div#userpopupblack').show();
            var f = jQuery(this).attr('data-id');
            jQuery.post(ajaxurl, {action: 'mjsupport_ajax', mjsmod: 'fieldordering', task: 'getOptionsForFieldEdit',field:f, '_wpnonce':'". esc_attr(wp_create_nonce("get-options-for-field-edit"))."'}, function (data) {
                if(data){
                    var abc = jQuery.parseJSON(data)
                    jQuery('div#userpopup').html('');
                    jQuery('div#userpopup').html(MJTC_msDecodeHTML(abc));
                }
            });
            jQuery('div#userpopup').slideDown('slow');
        });
        jQuery('span.close, div#userpopupblack').click(function (e) {
            jQuery('div#userpopup').slideUp('slow', function () {
                jQuery('div#userpopupblack').hide();
            });

        });
        jQuery('table#majestic-support-table tbody').sortable({
            handle : '.ms-order-grab-column',
            update  : function () {
                jQuery('.mjtc-form-button').slideDown('slow');
                var abc =  jQuery('table#majestic-support-table tbody').sortable('serialize');
                jQuery('input#fields_ordering_new').val(abc);
            }
        });
    });
    function close_popup(){
        jQuery('div#userpopup').slideUp('slow', function () {
            jQuery('div#userpopupblack').hide();
        });
    }

";
wp_add_inline_script('majestic-support-cmain-js',$majesticsupport_js);
wp_enqueue_script('jquery-ui-sortable');
wp_enqueue_style('majesticsupport-jquery-ui-css', MJTC_PLUGIN_URL . 'includes/css/jquery-ui-smoothness.css', array(), '1.0.0');

MJTC_message::MJTC_getMessage(); ?>
<?php
$type = array(
    (object) array('id' => '1', 'text' => esc_html(__('Public', 'majestic-support'))),
    (object) array('id' => '2', 'text' => esc_html(__('Private', 'majestic-support')))
);
?>
<?php if(isset(majesticsupport::$_data['formid']) && majesticsupport::$_data['formid'] != null){ $MJTC_mformid = majesticsupport::$_data['formid'];}else{ $MJTC_mformid = MJTC_includer::MJTC_getModel('ticket')->getDefaultMultiFormId();} ?>
<div id="msadmin-wrapper">
    <div id="msadmin-leftmenu">
        <?php  MJTC_includer::MJTC_getClassesInclude('msadminsidemenu'); ?>
    </div>
    <div id="msadmin-data">
        <?php MJTC_includer::MJTC_getModel('majesticsupport')->getPageTitle('userfields'); ?>
        <div id="userpopupblack" style="display:none;"></div>
        <div id="userpopup" style="display:none;">
        </div>
        <div id="msadmin-data-wrp">
            <?php if (!empty(majesticsupport::$_data[0])) { ?>
                <form class="msadmin-form" method="post" action="<?php echo esc_url(wp_nonce_url(admin_url("admin.php?page=majesticsupport_majesticsupport&task=saveordering&formid=".esc_attr($MJTC_mformid)),"save-ordering")); ?>">
                <table id="majestic-support-table">
                    <thead>
                    <tr class="majestic-support-table-heading">
                        <th class="majestic-support-table-ordering"><?php echo esc_html(__('Ordering', 'majestic-support')); ?></th>
                        <th class="majestic-support-table-ordering"><?php echo esc_html(__('S.No', 'majestic-support')); ?></th>
                        <th class="left majestic-support-table-title"><?php echo esc_html(__('Field Title', 'majestic-support')); ?></th>
                        <th class="majestic-support-table-status"><?php echo esc_html(__('User Published', 'majestic-support')); ?></th>
                        <th class="majestic-support-table-status"><?php echo esc_html(__('Visitor Published', 'majestic-support')); ?></th>
                        <th class="majestic-support-table-status"><?php echo esc_html(__('Required', 'majestic-support')); ?></th>
                        <th class="majestic-support-table-actions"><?php echo esc_html(__('Action', 'majestic-support')); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $MJTC_i = 0;
                    $MJTC_count = count(majesticsupport::$_data[0]) - 1;
                    foreach (majesticsupport::$_data[0] AS $MJTC_field) {
                        if($MJTC_field->field == 'wcorderid' || $MJTC_field->field == 'wcproductid' || $MJTC_field->field == 'wcitemid'){
                            if(!in_array('woocommerce', majesticsupport::$_active_addons)){
                                continue;
                            }
                            if(!class_exists('WooCommerce')){
                                continue;
                            }
                        }

                        if($MJTC_field->field == 'eddorderid' || $MJTC_field->field == 'eddproductid'){
                            if(!in_array('easydigitaldownloads', majesticsupport::$_active_addons)){
                                continue;
                            }
                            if(!class_exists('Easy_Digital_Downloads')){
                                continue;
                            }
                        }

                        if($MJTC_field->field == 'eddlicensekey'){
                            if(!in_array('easydigitaldownloads', majesticsupport::$_active_addons)){
                                continue;
                            }
                            if(!class_exists('Easy_Digital_Downloads')){
                                continue;
                            }
                            if(!class_exists('EDD_Software_Licensing')){
                                continue;
                            }
                        }
                        // hide status and assign and duedate to field
                        if($MJTC_field->field == 'wcitemid' || $MJTC_field->field == 'status' || $MJTC_field->field == 'assignto' || $MJTC_field->field == 'duedate'){
                            continue;
                        }

                        if($MJTC_field->field == 'envatopurchasecode'){
                            if(!in_array('envatovalidation', majesticsupport::$_active_addons)){
                                continue;
                            }
                        }

                        $MJTC_alt = $MJTC_field->published ? esc_html(__('Published','majestic-support')) : esc_html(__('Unpublished','majestic-support'));
                        $MJTC_reqalt = $MJTC_field->required ? esc_html(__('Required','majestic-support')) : esc_html(__('Not required','majestic-support'));
                        ?>
                        <tr id="id_<?php echo esc_attr($MJTC_field->id); ?>">
                            <td class="mjtc-textaligncenter ms-order-grab-column majestic-support-table-ordering" >
                                <span class="majestic-support-table-responsive-heading">
                                    <?php echo esc_html(__('Ordering', 'majestic-support')); echo esc_html(" : "); ?>
                                </span>
                                <div class="ms-grab-handle" title="Drag to reorder">
                                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M11 18c0 1.1-.9 2-2 2s-2-.9-2-2 .9-2 2-2 2 .9 2 2zm-2-8c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0-6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm6 4c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"></path></svg>
                                </div>
                            </td>

                            <td class="majestic-support-table-ordering">
                            <span class="majestic-support-table-responsive-heading"><?php echo esc_html(__('S.No','majestic-support')); ?>:</span>
                            <?php echo esc_html($MJTC_field->id); ?></td>
                            <td class="left majestic-support-table-title">
                            <span class="majestic-support-table-responsive-heading"><?php echo esc_html(__('Field Title','majestic-support')); ?>:</span>
                                <?php
                                    if ($MJTC_field->fieldtitle){
                                        $MJTC_head = '<a title="'. esc_html(__('users popup','majestic-support')).'" href="?page=majesticsupport_fieldordering&mjslay=adduserfeild&majesticsupportid='.esc_attr($MJTC_field->id).'&fieldfor='.majesticsupport::$_data['fieldfor'].'&formid='.esc_attr($MJTC_field->multiformid).'" id="" data-id='.esc_attr($MJTC_field->id).'>'.esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->fieldtitle)).'</a>';
                                        echo wp_kses($MJTC_head, MJTC_ALLOWED_TAGS);
                                    } else {
                                        echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->userfieldtitle));
                                    }
                                    if($MJTC_field->cannotunpublish == 1){
                                        echo wp_kses('<font style="color:#1C6288;font-size:20px;margin:0px 5px;">*</font>', MJTC_ALLOWED_TAGS);
                                    }
                                ?>
                            </td>
                            <?php
                            if ($MJTC_field->cannotunpublish == 1) {
                                $MJTC_class = 'majestic-support-yes';
                            } elseif ($MJTC_field->published == 1) {
                                $MJTC_class = 'majestic-support-yes';
                            } else {
                                $MJTC_class = 'majestic-support-no';
                            }
                            ?>
                            <td class="majestic-support-table-status <?php echo esc_attr($MJTC_class); ?>">
                            <span class="majestic-support-table-responsive-heading"><?php echo esc_html(__('User Published','majestic-support')); ?>:</span>
                                <?php if ($MJTC_field->cannotunpublish == 1) { ?>
                                    <span class="majestice-support-unselect-field"><span title="<?php echo esc_attr(__('Can Not Unpublished','majestic-support')); ?>" class="majestic-support-table-status-dot"></span><?php echo esc_html(__('Yes', 'majestic-support')); ?></span>
                                <?php }elseif ($MJTC_field->published == 1) {
                                    $MJTC_url  = "?page=majesticsupport_fieldordering&task=changepublishstatus&action=mstask&status=unpublish&fieldorderingid=".esc_attr($MJTC_field->id).'&fieldfor='.esc_attr(majesticsupport::$_data['fieldfor']).'&formid='.esc_attr($MJTC_field->multiformid);
                                         ?>
                                        <a title="<?php echo esc_attr(__('Yes','majestic-support')); ?>" href="<?php echo esc_url(wp_nonce_url($MJTC_url, 'change-publish-status-'.esc_attr($MJTC_field->id))); ?>" ><span class="majestic-support-table-status-dot"></span><?php echo esc_html(__('Yes', 'majestic-support')); ?></a>
                                <?php }else{
                                    $MJTC_url  = "?page=majesticsupport_fieldordering&task=changepublishstatus&action=mstask&status=publish&fieldorderingid=".esc_attr($MJTC_field->id).'&fieldfor='.esc_attr(majesticsupport::$_data['fieldfor']).'&formid='.esc_attr($MJTC_field->multiformid);
                                         ?>
                                        <a title="<?php echo esc_attr(__('cross','majestic-support')); ?>" href="<?php echo esc_url(wp_nonce_url($MJTC_url, 'change-publish-status-'.esc_attr($MJTC_field->id))); ?>" ><span class="majestic-support-table-status-dot"></span><?php echo esc_html(__('No', 'majestic-support')); ?></a>
                                <?php } ?>
                            </td>
                            <?php
                            if ($MJTC_field->cannotunpublish == 1) {
                                $MJTC_class = 'majestic-support-yes';
                            } elseif ($MJTC_field->isvisitorpublished == 1) {
                                $MJTC_class = 'majestic-support-yes';
                            } else {
                                $MJTC_class = 'majestic-support-no';
                            }
                            ?>
                            <td class="majestic-support-table-status <?php echo esc_attr($MJTC_class); ?>">
                            <span class="majestic-support-table-responsive-heading"><?php echo esc_html(__('Visitor Published','majestic-support')); ?>:</span>
                                <?php if ($MJTC_field->cannotunpublish == 1) { ?>
                                    <span class="majestice-support-unselect-field"><span title="<?php echo esc_attr(__('Can Not Unpublished','majestic-support')); ?>" class="majestic-support-table-status-dot"></span><?php echo esc_html(__('Yes', 'majestic-support')); ?></span>
                                <?php }elseif ($MJTC_field->isvisitorpublished == 1) {
                                    $MJTC_url  = "?page=majesticsupport_fieldordering&task=changevisitorpublishstatus&action=mstask&status=unpublish&fieldorderingid=".esc_attr($MJTC_field->id).'&fieldfor='.esc_attr(majesticsupport::$_data['fieldfor']).'&formid='.esc_attr($MJTC_field->multiformid);
                                         ?>
                                        <a title="<?php echo esc_attr(__('Yes','majestic-support')); ?>" href="<?php echo esc_url(wp_nonce_url($MJTC_url, 'change-visitor-publish-status-'.esc_attr($MJTC_field->id))); ?>" ><span class="majestic-support-table-status-dot"></span><?php echo esc_html(__('Yes', 'majestic-support')); ?></a>
                                <?php }else{
                                    $MJTC_url  = "?page=majesticsupport_fieldordering&task=changevisitorpublishstatus&action=mstask&status=publish&fieldorderingid=".esc_attr($MJTC_field->id).'&fieldfor='.esc_attr(majesticsupport::$_data['fieldfor']).'&formid='.esc_attr($MJTC_field->multiformid);
                                         ?>
                                        <a title="<?php echo esc_attr(__('cross','majestic-support')); ?>" href="<?php echo esc_url(wp_nonce_url($MJTC_url, 'change-visitor-publish-status-'.esc_attr($MJTC_field->id))); ?>" ><span class="majestic-support-table-status-dot"></span><?php echo esc_html(__('No', 'majestic-support')); ?></a>
                                <?php } ?>
                            </td>
                            <?php
                            if ($MJTC_field->cannotunpublish == 1 || $MJTC_field->field == 'termsandconditions1' || $MJTC_field->field == 'termsandconditions2' || $MJTC_field->field == 'termsandconditions3' || ($MJTC_field->userfieldtype == 'termsandconditions' && $MJTC_field->required == 1) ) {
                                $MJTC_class = 'majestic-support-yes';
                            } elseif ($MJTC_field->required == 1) {
                                $MJTC_class = 'majestic-support-yes';
                            } else {
                                $MJTC_class = 'majestic-support-no';
                            }
                            ?>
                            <td class="majestic-support-table-status <?php echo esc_attr($MJTC_class); ?>">
                            <span class="majestic-support-table-responsive-heading"><?php echo esc_html(__('Required','majestic-support')); ?>:</span>
                                <?php if ($MJTC_field->cannotunpublish == 1 || $MJTC_field->field == 'termsandconditions1' || $MJTC_field->field == 'termsandconditions2' || $MJTC_field->field == 'termsandconditions3' || ($MJTC_field->userfieldtype == 'termsandconditions' && $MJTC_field->required == 1) ) { ?>
                                    <span class="majestice-support-unselect-field"><span title="<?php echo esc_attr(__('can not mark as not required','majestic-support')); ?>" class="majestic-support-table-status-dot"></span><?php echo esc_html(__('Yes', 'majestic-support')); ?></span>
                                <?php }elseif ($MJTC_field->required == 1) {
                                    $MJTC_url  = "?page=majesticsupport_fieldordering&task=changerequiredstatus&action=mstask&status=unrequired&fieldorderingid=".esc_attr($MJTC_field->id).'&fieldfor='.esc_attr(majesticsupport::$_data['fieldfor']).'&formid='.esc_attr($MJTC_field->multiformid);
                                         ?>
                                        <a title="<?php echo esc_attr(__('Yes','majestic-support')); ?>" href="<?php echo esc_url(wp_nonce_url($MJTC_url, 'change-required-status-'.esc_attr($MJTC_field->id))); ?>" ><span class="majestic-support-table-status-dot"></span><?php echo esc_html(__('Yes', 'majestic-support')); ?></a>
                                <?php }else{
                                    $MJTC_url  = "?page=majesticsupport_fieldordering&task=changerequiredstatus&action=mstask&status=required&fieldorderingid=".esc_attr($MJTC_field->id).'&fieldfor='.esc_attr(majesticsupport::$_data['fieldfor']).'&formid='.esc_attr($MJTC_field->multiformid);
                                         ?>
                                        <a title="<?php echo esc_attr(__('No','majestic-support')); ?>" href="<?php echo esc_url(wp_nonce_url($MJTC_url, 'change-required-status-'.esc_attr($MJTC_field->id))); ?>" ><span class="majestic-support-table-status-dot"></span><?php echo esc_html(__('No', 'majestic-support')); ?></a>
                                <?php } ?>
                            </td>
                            <td class="majestic-support-table-actions">
                            <span class="majestic-support-table-responsive-heading"><?php echo esc_html(__('Action','majestic-support')); ?>:</span>
                                <?php
                                    echo wp_kses('<a title="'. esc_html(__('Edit','majestic-support')).'" class="action-btn" href="?page=majesticsupport_fieldordering&mjslay=adduserfeild&majesticsupportid='.esc_attr($MJTC_field->id).'&fieldfor='.esc_attr(majesticsupport::$_data['fieldfor']).'&formid='.esc_attr($MJTC_field->multiformid).'">
                                     <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"></path></svg>
                                    </a>&nbsp;', MJTC_ALLOWED_TAGS);
                                    if($MJTC_field->isuserfield==1){
                                        $MJTC_fieldData = '<a title="'. esc_html(__('Delete','majestic-support')).'" class="action-btn" onclick="return confirm(\''. esc_html(__('Are you sure you want to delete?','majestic-support')).'\');" href="'.esc_url(wp_nonce_url('?page=majesticsupport_fieldordering&task=removeuserfeild&action=mstask&majesticsupportid='.esc_attr($MJTC_field->id).'&fieldfor='.esc_attr(majesticsupport::$_data['fieldfor']).'&formid='.esc_attr($MJTC_field->multiformid),'remove-userfeild-'.esc_attr($MJTC_field->id))).'">
                                            <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"></path></svg>
                                        </a>';
                                        echo wp_kses($MJTC_fieldData, MJTC_ALLOWED_TAGS);
                                    }
                                ?>
                            </td>
                        </tr>
                        <?php
                        $MJTC_i++;
                    }
                    ?>
                 </tbody>
                 </table>
                 <?php echo wp_kses(MJTC_formfield::MJTC_hidden('fields_ordering_new', '123'), MJTC_ALLOWED_TAGS); ?>
                    <?php echo wp_kses(MJTC_formfield::MJTC_hidden('form_request', 'majesticsupport'), MJTC_ALLOWED_TAGS); ?>
                    <?php echo wp_kses(MJTC_formfield::MJTC_hidden('ordering_for', 'fieldordering'), MJTC_ALLOWED_TAGS); ?>
                    <?php echo wp_kses(MJTC_formfield::MJTC_hidden('fieldfor', majesticsupport::$_data['fieldfor']), MJTC_ALLOWED_TAGS); ?>
                    <?php echo wp_kses(MJTC_formfield::MJTC_hidden('pagenum_for_ordering', MJTC_request::MJTC_getVar('pagenum', 'get', 1)), MJTC_ALLOWED_TAGS); ?>
                    <div class="mjtc-form-button" style="display: none;">
                        <?php echo wp_kses(MJTC_formfield::MJTC_submitbutton('save', esc_html(__('Save Ordering', 'majestic-support')), array('class' => 'button mjtc-form-save')), MJTC_ALLOWED_TAGS); ?>
                    </div>
                </form>
                <div class="msadmin-help-msg">
                    <?php echo wp_kses('<font style="color:#1C6288;font-size:20px;margin:0px 5px;vertical-align: middle;">*</font>'. esc_html(__('Cannot unpublished field','majestic-support')), MJTC_ALLOWED_TAGS); ?>
                </div>
                <?php
            } else {
                MJTC_layout::MJTC_getNoRecordFound();
            }
            ?>
        </div>
        <?php if (!empty(majesticsupport::$_data[0])) { ?>
            <div id="mjtc-field-ordering-notice">
                <img src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/info-icon.png"> <?php echo esc_html(__('File upload fields and Check box fields cannot be made required.', 'majestic-support')); ?>
            </div>
        <?php } ?>
    </div>
</div>
