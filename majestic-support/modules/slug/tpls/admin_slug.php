<?php
if (!defined('ABSPATH'))
die('Restricted Access');
wp_enqueue_script('majesticsupport-responsivetablejs',MJTC_PLUGIN_URL.'includes/js/responsivetable.js', array(), '1.0.0', true);
MJTC_message::MJTC_getMessage();
?>
<!-- main wrapper -->
<div id="msadmin-wrapper">
    <div id="userpopupblack" style="display:none;"></div>
    <div id="userpopup" style="display:none;"></div>
    <!-- left menu -->
    <div id="msadmin-leftmenu">
        <?php  MJTC_includer::MJTC_getClassesInclude('msadminsidemenu'); ?>
    </div>
    <div id="msadmin-data">
        <!-- top bar -->
        <?php MJTC_includer::MJTC_getModel('majesticsupport')->getPageTitle('admin_slug'); ?>
        <?php
        $majesticsupport_js ="
            /*Function to Show popUp,Reset*/
            var slug_for_edit = 0;
            jQuery(document).ready(function () {
                jQuery('div#userpopupblack').click(function () {
                    closePopup();
                });
            });

            function resetFrom() {// Resest Form
                jQuery('input#slug').val('');
                jQuery('form#msadmin-form').submit();
            }

            function showPopupAndSetValues(nonce, id,slug) {//Showing PopUp
                slug = jQuery('span#td_'+id).html();
                slug_for_edit = id;
                jQuery.post(ajaxurl, {action: 'mjsupport_ajax', mjsmod: 'slug', task: 'getOptionsForEditSlug',id:id ,slug:slug, '_wpnonce': nonce}, function (data) {
                    if (data) {
                        var d = jQuery.parseJSON(data);
                        jQuery('div#userpopupblack').css('display', 'block');
                        jQuery('div#userpopup').html(MJTC_msDecodeHTML(d));
                        jQuery('div#userpopup').slideDown('slow');
                    }
                });
            }

            function closePopup() {// Close PopUp
                jQuery('div#userpopup').slideUp('slow');
                setTimeout(function () {
                    jQuery('div#userpopupblack').hide();
                    jQuery('div#userpopup').html('');
                }, 700);
            }

            function getFieldValue() {
                var slugvalue = jQuery('#slugedit').val();
                jQuery('input#'+slug_for_edit).val(slugvalue);
                jQuery('span#td_'+slug_for_edit).html(slugvalue);
                closePopup();
            }

        ";
        wp_add_inline_script('majestic-support-cmain-js',$majesticsupport_js);
        ?>
        <!-- page content -->
        <div id="msadmin-data-wrp">
            <div class="mjtc-filter-form-slugswrp">
                <!-- filter form -->
                <form class="mjtc-filter-form slug-configform" name="msadmin-form" id="conmsadmin-form" method="post" action="<?php echo esc_url(wp_nonce_url(admin_url("admin.php?page=majesticsupport_slug&task=savehomeprefix"),"save-home-prefix")); ?>">
                    <?php echo wp_kses(MJTC_formfield::MJTC_text('prefix', majesticsupport::$_config['home_slug_prefix'], array('class' => 'inputbox mjtc-form-input-field', 'placeholder' => esc_html(__('Home Slug','majestic-support')).' '. esc_html(__('Prefix','majestic-support')))),MJTC_ALLOWED_TAGS); ?>
                    <?php echo wp_kses(MJTC_formfield::MJTC_submitbutton('btnsubmit', esc_html(__('Save','majestic-support')), array('class' => 'button mjtc-form-search')),MJTC_ALLOWED_TAGS); ?>
                    <?php echo wp_kses(MJTC_formfield::MJTC_hidden('form_request', 'majesticsupport'),MJTC_ALLOWED_TAGS); ?>
                    <div class="mjtc-form-help-text">
                        <img src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/view-job-information.png" />
                        <?php echo esc_html(__('This prefix will be added to the slug in case of homepage links.','majestic-support'))?>
                    </div>
                </form>
                <!-- filter form -->
                <form class="mjtc-filter-form slug-configform" name="msadmin-form" id="conmsadmin-form" method="post" action="<?php echo esc_url(wp_nonce_url(admin_url("admin.php?page=majesticsupport_slug&task=saveprefix"),"save-prefix")); ?>">
                    <?php echo wp_kses(MJTC_formfield::MJTC_text('prefix', majesticsupport::$_config['slug_prefix'], array('class' => 'inputbox mjtc-form-input-field', 'placeholder' => esc_html(__('Slug','majestic-support')).' '. esc_html(__('Prefix','majestic-support')))),MJTC_ALLOWED_TAGS); ?>
                    <?php echo wp_kses(MJTC_formfield::MJTC_submitbutton('btnsubmit', esc_html(__('Save','majestic-support')), array('class' => 'button mjtc-form-search')),MJTC_ALLOWED_TAGS); ?>
                    <?php echo wp_kses(MJTC_formfield::MJTC_hidden('form_request', 'majesticsupport'),MJTC_ALLOWED_TAGS); ?>
                    <div class="mjtc-form-help-text">
                        <img src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/view-job-information.png" />
                        <?php echo esc_html(__('This prefix will be added to the slug in case of conflict.','majestic-support'))?>
                    </div>
                </form>
            </div>
            <!-- filter form -->
            <form class="mjtc-filter-form" name="msadmin-form" id="msadmin-form" method="post" action="<?php echo esc_url(wp_nonce_url(admin_url("admin.php?page=majesticsupport_slug"),"slug")); ?>">
                <?php echo wp_kses(MJTC_formfield::MJTC_text('slug', majesticsupport::$_data['slug'], array('class' => 'inputbox mjtc-form-input-field', 'placeholder' => esc_html(__('Search By Slug','majestic-support')))),MJTC_ALLOWED_TAGS); ?>
                <?php echo wp_kses(MJTC_formfield::MJTC_submitbutton('btnsubmit', esc_html(__('Search','majestic-support')), array('class' => 'button mjtc-form-search')),MJTC_ALLOWED_TAGS); ?>
                <?php echo wp_kses(MJTC_formfield::MJTC_button('reset', esc_html(__('Reset','majestic-support')), array('class' => 'button mjtc-form-reset', 'onclick' => 'resetFrom();')),MJTC_ALLOWED_TAGS); ?>
                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('MS_form_search', 'MS_SEARCH'),MJTC_ALLOWED_TAGS); ?>
            </form>
            <?php
                if (!empty(majesticsupport::$_data[0])) {
                    ?>
                    <form id="mjtc-list-form" method="post" action="<?php echo esc_url(wp_nonce_url(admin_url("admin.php?page=majesticsupport_slug&task=saveSlug"),"save-slug")); ?>">
                        <table id="majestic-support-table" class="majestic-support-table">
                            <thead>
                                <tr class="majestic-support-table-heading">
                                    <th class="left majestic-support-table-title">
                                        <?php echo esc_html(__('Slug List','majestic-support')); ?>
                                    </th>
                                    <th class="left majestic-support-table-description">
                                        <?php echo esc_html(__('Description','majestic-support')); ?>
                                    </th>
                                    <th class="majestic-support-table-actions">
                                        <?php echo esc_html(__('Action','majestic-support')); ?>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $MJTC_pagenum = MJTC_request::MJTC_getVar('pagenum', 'get', 1);
                                    $MJTC_pageid = ($MJTC_pagenum > 1) ? '&pagenum=' . $MJTC_pagenum : '';
                                    foreach (majesticsupport::$_data[0] as $MJTC_row){
                                        ?>
                                        <tr>
                                            <td class="left majestic-support-table-title">
                                                <span class="ms-slug-icon">
                                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M3.9 12c0-1.71 1.39-3.1 3.1-3.1h4V7H7c-2.76 0-5 2.24-5 5s2.24 5 5 5h4v-1.9H7c-1.71 0-3.1-1.39-3.1-3.1zM8 13h8v-2H8v2zm9-6h-4v1.9h4c1.71 0 3.1 1.39 3.1 3.1s-1.39 3.1-3.1 3.1h-4V17h4c2.76 0 5-2.24 5-5s-2.24-5-5-5z"></path></svg>
                                                </span>
                                                <span id="<?php echo esc_attr('td_').esc_attr($MJTC_row->id);?>">
                                                    <?php echo esc_html($MJTC_row->slug);?>
                                                </span>
                                            </td>
                                            <td class="left majestic-support-table-description">
                                                <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_row->description));?>
                                            </td>
                                            <td class="majestic-support-table-actions">
                                                <?php $MJTC_nonce = wp_create_nonce("get-options-for-edit-slug-".$MJTC_row->id); ?>
                                                <a class="action-btn majestic-support-table-edit-action" href="#" onclick="showPopupAndSetValues('<?php echo esc_js($MJTC_nonce); ?>' ,<?php echo esc_js($MJTC_row->id); ?>)" title="<?php echo esc_attr(__('edit','majestic-support')); ?>">
                                                    <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"></path></svg>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php echo wp_kses(MJTC_formfield::MJTC_hidden($MJTC_row->id, $MJTC_row->slug),MJTC_ALLOWED_TAGS);?>
                                        <?php
                                    }
                                ?>
                            </tbody>
                        </table>
                        <!-- Hidden Fields -->
                        <div class="mjtc-filter-form-action-wrp">
                            <?php echo wp_kses(MJTC_formfield::MJTC_submitbutton('btnsubmit', esc_html(__('Save','majestic-support')), array('class' => 'button savebutton mjtc-form-act-btn mjtc-form-act-btn')),MJTC_ALLOWED_TAGS); ?>
                            <div class="mjtc-form-act-msg">
                                <?php echo esc_html(__('This button will only save slugs on the current page','majestic-support')); ?> !
                            </div>
                        </div>
                        <?php echo wp_kses(MJTC_formfield::MJTC_hidden('task', ''),MJTC_ALLOWED_TAGS); ?>
                        <?php echo wp_kses(MJTC_formfield::MJTC_hidden('pagenum', ($MJTC_pagenum > 1) ? $MJTC_pagenum : ''),MJTC_ALLOWED_TAGS); ?>
                        <?php echo wp_kses(MJTC_formfield::MJTC_hidden('form_request', 'majesticsupport'),MJTC_ALLOWED_TAGS); ?>
                    </form>
                    <?php
                    if (majesticsupport::$_data[1]) {
                        $MJTC_data = '<div class="tablenav"><div class="tablenav-pages">' . wp_kses_post(majesticsupport::$_data[1]) . '</div></div>';
                        echo wp_kses($MJTC_data, MJTC_ALLOWED_TAGS);
                    }
                } else {
                    MJTC_layout::MJTC_getNoRecordFound();
                }
            ?>
        </div>
    </div>
</div>
