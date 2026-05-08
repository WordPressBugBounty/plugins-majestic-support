<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<div class="ms-main-up-wrapper">
    <div id="popup-record-data" style="width:100%;"></div>
<?php
wp_enqueue_style('status-graph', MJTC_PLUGIN_URL . 'includes/css/status_graph.css', array(), '1.0.0');
if (majesticsupport::$_config['offline'] == 2) {
    if (MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid() != 0) {
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_style('majesticsupport-jquery-ui-css', MJTC_PLUGIN_URL . 'includes/css/jquery-ui-smoothness.css', array(), '1.0.0');

        $majesticsupport_js ="
        ajaxurl = '". esc_url(admin_url('admin-ajax.php')) ."';
        function showTicketCloseReasons(id, internalid){
            jQuery('div.ms-popup-other-reason-box').hide();
            jQuery.post(ajaxurl, {action: 'mjsupport_ajax', mjsmod: 'ticketclosereason', task: 'getTicketCloseReasonsForPopup',isadmin:0,redirect:3,id:id, internalid:internalid, '_wpnonce':'". esc_attr(wp_create_nonce('get-ticket-close-reasons-for-popup'))."'}, function (data) {
                if(data){
                    data=jQuery.parseJSON(data);
                    jQuery('div#popup-record-data').html('');
                    jQuery('div#popup-record-data').html(MJTC_msDecodeHTML(data['data']));
                }
            });
        }

        function closeReasonPopup(saveReason, closeTicket){
            var close = 1;
            if (saveReason == 0 && closeTicket == 1) {
                jQuery('input[name=ms_skip]').val(1);
                jQuery('form#ms-ticket-close-reason-form').submit();
            }
            if (saveReason == 1 && closeTicket == 1) {
                if (jQuery('.reason_rb').is(':checked')) {
                    jQuery('input[type=checkbox]:checked').each(function() {
                        var selectedValue = jQuery(this).val();
                        if (selectedValue == 'reason_other') {
                            var other = jQuery('textarea#other_reason_box').val();
                            if (other == '') {
                                close = 0;
                                alert(\"". esc_html(__('Add reason in the other box','majestic-support'))."\");
                                jQuery('div#ticketclosereason').slideDown('slow');
                                return;
                            }
                        }
                    });
                    jQuery('input[type=radio]:checked').each(function() {
                        var selectedValue = jQuery(this).val();
                        if (selectedValue == 'reason_other') {
                            var other = jQuery('textarea#other_reason_box').val();
                            if (other == '') {
                                close = 0;
                                alert(\"". esc_html(__('Add reason in the other box','majestic-support'))."\");
                                jQuery('div#ticketclosereason').slideDown('slow');
                                return;
                            }
                        }
                    });
                    if (close == 1) {
                        jQuery('form#ms-ticket-close-reason-form').submit();
                    }
                } else {
                    alert(\"". esc_html(__('First make some selection!','majestic-support'))."\");
                    jQuery('div#ticketclosereason').slideDown('slow');
                    return;
                }
            }
            if (close == 1) {
                jQuery('div#ticketclosereason').slideUp('slow', function () {
                    jQuery('div.ms-popup-background').hide();
                });
            }
        }
        jQuery(document).ready(function($) {
            $('.custom_date').datepicker({
                dateFormat: 'yy-mm-dd'
            });";
            if(isset(majesticsupport::$_data['filter']['combinesearch'])){
                $MJTC_combinesearch = majesticsupport::$_data['filter']['combinesearch'];
            } else {
                $MJTC_combinesearch = '';
            }
            $majesticsupport_js .= "
            var combinesearch = '". $MJTC_combinesearch ."';
            if (combinesearch == true) {
                doVisible();
                $('#mjtc-filter-wrapper-toggle-area, .mjtc-filter-form-dynamic-fields').show();
            }
            jQuery('#mjtc-search-filter-toggle-btn').click(function(event) {
                event.preventDefault();
                jQuery('#mjtc-filter-wrapper-toggle-search').toggle();
                jQuery('#mjtc-filter-wrapper-toggle-area').toggle();
                jQuery('.mjtc-filter-form-dynamic-fields').slideToggle(400);
                jQuery('#mjtc-filterArrow').toggleClass('mjtc-rotate-arrow');
            });

            jQuery('select.mjtc-support-sorting-select').on('change', function(e) {
                e.preventDefault();
                var sortby = jQuery('.mjtc-support-sorting-select option:selected').val();
                jQuery('input#sortby').val(sortby);
                jQuery('form#majesticsupportform').submit();
            });
            jQuery('a.mjtc-admin-sort-btn').on('click', function(e) {
                e.preventDefault();
                var sortby = jQuery('.mjtc-support-sorting-select option:selected').val();
                jQuery('input#sortby').val(sortby);
                jQuery('form#majesticsupportform').submit();
            });
            jQuery('a.mjtc-myticket-link').click(function(e) {
                e.preventDefault();
                var list = jQuery(this).attr('data-tab-number');
                jQuery('input#list').val(list);
                jQuery('form#majesticsupportform').submit();
            });
            jQuery('span.mjtc-support-closedby-wrp').hover(
                function(e){
                    jQuery(this).find('span.mjtc-support-closed-date').css('display','inline-block');
                },
                function(e){
                    jQuery(this).find('span.mjtc-support-closed-date').css('display','none');
                }
            );

            function doVisible() {
                $('#mjtc-filter-wrapper-toggle-search').hide();
                $('.mjtc-filter-wrapper-toggle-ticketid').show();
                $('#mjtc-filter-wrapper-toggle-area, .mjtc-filter-form-dynamic-fields').show();
                $('#mjtc-filter-wrapper-toggle-minus').show();
                $('#mjtc-filter-wrapper-toggle-plus').hide();
            }
        });

        function resetForm() {
            var form = jQuery('form#majesticsupportform');
            form.find('input[type=text], input[type=email], input[type=password], textarea').val('');
            form.find('input:checkbox').removeAttr('checked');
            form.find('select').prop('selectedIndex', 0);
            form.find('input[type=\'radio\']').prop('checked', false);
            return true;
        }";

        wp_add_inline_script('majestic-support-cmain-js',$majesticsupport_js);
    }
        MJTC_message::MJTC_getMessage();
        include_once(MJTC_PLUGIN_PATH . 'includes/header.php');
    if (MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid() != 0) {
        $MJTC_list = isset(majesticsupport::$_search['ticket']) ? majesticsupport::$_search['ticket']['list'] : 1;
        $MJTC_open = ($MJTC_list == 1) ? 'active' : '';
        $MJTC_answered = ($MJTC_list == 2) ? 'active' : '';
        $MJTC_overdue = ($MJTC_list == 3) ? 'active' : '';
        $MJTC_myticket = ($MJTC_list == 4) ? 'active' : '';
        $MJTC_field_array = MJTC_includer::MJTC_getModel('fieldordering')->getFieldTitleByFieldfor(1);
        $MJTC_search_field_array = MJTC_includer::MJTC_getModel('fieldordering')->getUserSystemFieldsForSearch();
        $MJTC_open_percentage = 0;
        $MJTC_close_percentage = 0;
        $MJTC_answered_percentage = 0;
        $MJTC_allticket_percentage = 0;
        if(isset(majesticsupport::$_data['count']) && isset(majesticsupport::$_data['count']['allticket']) && majesticsupport::$_data['count']['allticket'] != 0){
            $MJTC_open_percentage = round((majesticsupport::$_data['count']['openticket'] / majesticsupport::$_data['count']['allticket']) * 100);
            $MJTC_close_percentage = round((majesticsupport::$_data['count']['closedticket'] / majesticsupport::$_data['count']['allticket']) * 100);
            $MJTC_answered_percentage = round((majesticsupport::$_data['count']['answeredticket'] / majesticsupport::$_data['count']['allticket']) * 100);
        }
        if(isset(majesticsupport::$_data['count']) && isset(majesticsupport::$_data['count']['allticket']) && majesticsupport::$_data['count']['allticket'] != 0){
            $MJTC_allticket_percentage = 100;
        }
    }
        ?>
        <div class="mjtc-support-top-sec-header">
            <img class="mjtc-transparent-header-img1" alt="<?php echo esc_attr(__('Image', 'majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/tp-image.png" />
            <div class="mjtc-support-top-sec-left-header">
                <div class="mjtc-support-main-heading">
                    <?php echo esc_html(__("My Tickets",'majestic-support')); ?>
                </div>
                <?php /* MJTC_includer::MJTC_getModel('majesticsupport')->getPageBreadcrumps('mytickets'); */?>
                <div class="mjtc-support-sub-heading"><?php echo esc_html(__("Manage, track, and resolve all your support tickets in one place.",'majestic-support')); ?></div>
            </div>
            <div class="mjtc-support-top-sec-right-header">
                <?php
                $MJTC_id = "";
                if(in_array('multiform',majesticsupport::$_active_addons) && majesticsupport::$_config['show_multiform_popup'] == 1){
                    //show popup in case of multiform
                    $MJTC_id = "id=multiformpopup";
                }?>
                <a <?php echo esc_attr($MJTC_id); ?> href="<?php echo esc_url(majesticsupport::makeUrl(array('mjsmod'=>'ticket', 'mjslay'=>'addticket'))); ?>" class="mjtc-support-button-header">
                    <svg viewBox="0 0 24 24" width="16" height="16" class="outline-icon"><path d="M12 5v14M5 12h14"></path></svg>
                    <?php echo esc_html(__("Submit Ticket", 'majestic-support')); ?>
                </a>
            </div>
        </div>
        <div class="mjtc-support-cont-main-wrapper mjtc-support-cont-main-wrapper-with-btn">    
        <?php
        if (MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid() != 0) { ?>
            <div class="mjtc-support-cont-wrapper1 mjtc-support-tickets-main-wrapper">
                <!-- Top Circle Count Boxes -->
                <div class="mjtc-row mjtc-support-top-cirlce-count-wrp">
                    <div class="mjtc-myticket-link-wrap mjtc-support-myticket-link-myticket">
                        <a class="mjtc-support-blue mjtc-myticket-link <?php echo esc_attr($MJTC_myticket); ?>" href="#"
                            data-tab-number="4">
                            <svg viewBox="0 0 24 24" class="mjtc-support-card-bgsvg"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zm6.93 6h-2.95c-.32-1.25-.78-2.45-1.38-3.56 1.84.63 3.37 1.91 4.33 3.56zM12 4.04c.83 1.2 1.48 2.53 1.91 3.96h-3.82c.43-1.43 1.08-2.76 1.91-3.96zM4.26 14C4.1 13.36 4 12.69 4 12s.1-1.36.26-2h3.38c-.08.66-.14 1.32-.14 2 0 .68.06 1.34.14 2H4.26zm.82 2h2.95c.32 1.25.78 2.45 1.38 3.56-1.84-.63-3.37-1.9-4.33-3.56zm2.95-8H5.08c.96-1.66 2.49-2.93 4.33-3.56C8.81 5.55 8.35 6.75 8.03 8zM12 19.96c-.83-1.2-1.48-2.53-1.91-3.96h3.82c-.43 1.43-1.08 2.76-1.91 3.96zM14.34 14H9.66c-.09-.66-.16-1.32-.16-2 0-.68.07-1.35.16-2h4.68c.09.65.16 1.32.16 2 0 .68-.07 1.34-.16 2zm.25 5.56c.6-1.11 1.06-2.31 1.38-3.56h2.95c-.96 1.65-2.49 2.93-4.33 3.56zM16.36 14c.08-.66.14-1.32.14-2 0-.68-.06-1.34-.14-2h3.38c.16.64.26 1.31.26 2s-.1 1.36-.26 2h-3.38z"></path></svg>
                            <div class="mjtc-support-card-left-wrp">
                                <div class="mjtc-support-card-title">
                                    <?php echo esc_html(__("All Tickets",'majestic-support')); ?>
                                </div>
                                <span class="mjtc-support-circle-count-text mjtc-support-blue">
                                    <?php
                                        if(majesticsupport::$_config['count_on_myticket'] == 1){
                                            $MJTC_data =  esc_html(majesticsupport::$_data['count']['allticket']) ;
                                            echo wp_kses($MJTC_data, MJTC_ALLOWED_TAGS);
                                        }
                                    ?>
                                </span>
                            </div>
                            <div class="mjtc-support-card-right-wrp">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                                    <path d="M11.644 1.59a.75.75 0 01.712 0l9.75 5.25a.75.75 0 010 1.32l-9.75 5.25a.75.75 0 01-.712 0l-9.75-5.25a.75.75 0 010-1.32l9.75-5.25z"></path>
                                    <path d="M3.265 10.602l7.668 4.129a2.25 2.25 0 002.134 0l7.668-4.13 1.37.739a.75.75 0 010 1.32l-9.75 5.25a.75.75 0 01-.71 0l-9.75-5.25a.75.75 0 010-1.32l1.37-.738z"></path>
                                    <path d="M10.933 19.231l-7.668-4.13-1.37.739a.75.75 0 000 1.32l9.75 5.25c.221.12.489.12.71 0l9.75-5.25a.75.75 0 000-1.32l-1.37-.738-7.668 4.13a2.25 2.25 0 01-2.134 0z"></path>
                                </svg>
                            </div>
                            <div class="mjtc-support-card-percentage-duration-wrp">
                                <span class="mjtc-support-card-percentage">
                                    <svg viewBox="0 0 24 24" width="10" height="10"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zM6.5 9L10 5.5 13.5 9H11v4H9V9H6.5zm11 6L14 18.5 10.5 15H13v-4h2v4h2.5z"></path></svg>
                                    <?php echo esc_html(__("All Records",'majestic-support')); ?>
                                </span>
                            </div>
                        </a>
                    </div>
                    <div class="mjtc-myticket-link-wrap mjtc-support-myticket-link-myticket">
                        <a class="mjtc-support-green mjtc-myticket-link <?php echo esc_attr($MJTC_open); ?>" href="#" data-tab-number="1">
                            <svg class="mjtc-support-card-bgsvg" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9.776c.112-.017.227-.026.344-.026h15.812c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 00-1.883 2.542l.857 6a2.25 2.25 0 002.227 1.932H19.05a2.25 2.25 0 002.227-1.932l.857-6a2.25 2.25 0 00-1.883-2.542m-16.5 0V6A2.25 2.25 0 016 3.75h3.879a1.5 1.5 0 011.06.44l2.122 2.12a1.5 1.5 0 001.06.44H18A2.25 2.25 0 0120.25 9v.776"></path>
                            </svg>
                            <div class="mjtc-support-card-left-wrp">
                                <div class="mjtc-support-card-title">
                                    <?php echo esc_html(__("Open Tickets",'majestic-support')); ?>
                                </div>
                                <span class="mjtc-support-circle-count-text mjtc-support-green">
                                    <?php
                                        if(majesticsupport::$_config['count_on_myticket'] == 1) {
                                        $MJTC_data =  esc_html(majesticsupport::$_data['count']['openticket']) ;
                                        echo wp_kses($MJTC_data, MJTC_ALLOWED_TAGS);
                                    }
                                    ?>
                                </span>
                            </div>
                            <div class="mjtc-support-card-right-wrp">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9.776c.112-.017.227-.026.344-.026h15.812c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 00-1.883 2.542l.857 6a2.25 2.25 0 002.227 1.932H19.05a2.25 2.25 0 002.227-1.932l.857-6a2.25 2.25 0 00-1.883-2.542m-16.5 0V6A2.25 2.25 0 016 3.75h3.879a1.5 1.5 0 011.06.44l2.122 2.12a1.5 1.5 0 001.06.44H18A2.25 2.25 0 0120.25 9v.776"></path>
                                </svg>
                            </div>
                            <div class="mjtc-support-card-percentage-duration-wrp">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3 h-3 text-yellow-300">
                                    <path d="M11.983 1.907a.75.75 0 00-1.292-.657l-8.5 9.5A.75.75 0 002.75 12h6.572l-1.283 6.093a.75.75 0 001.292.657l8.5-9.5A.75.75 0 0017.25 8h-6.572l1.305-6.093z"></path>
                                </svg>
                                <?php echo ' '.esc_html(__("Action Required",'majestic-support')); ?>
                            </div>
                        </a>
                    </div>
                    <div class="mjtc-myticket-link-wrap mjtc-support-myticket-link-myticket">
                        <a class="mjtc-support-brown mjtc-myticket-link <?php echo esc_attr($MJTC_overdue); ?>" href="#" data-tab-number="3">
                            <svg viewBox="0 0 24 24" class="mjtc-support-card-bgsvg"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"></path></svg>
                            <div class="mjtc-support-card-left-wrp">
                                <div class="mjtc-support-card-title">
                                    <?php echo esc_html(__("Answered Tickets",'majestic-support')); ?>
                                </div>
                                <span class="mjtc-support-circle-count-text mjtc-support-brown">
                                    <?php
                                        if(majesticsupport::$_config['count_on_myticket'] == 1){
                                            $MJTC_data =esc_html(majesticsupport::$_data['count']['answeredticket']);
                                            echo wp_kses($MJTC_data, MJTC_ALLOWED_TAGS);
                                        }
                                    ?>
                                </span>
                            </div>
                            <div class="mjtc-support-card-right-wrp">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z"></path>
                                </svg>
                            </div>
                            <div class="mjtc-support-card-percentage-duration-wrp">
                                <span class="mjtc-support-card-percentage">
                                    <svg viewBox="0 0 24 24" width="10" height="10"><path d="M6 2v6h.01L6 8.01 10 12l-4 4 .01.01H6V22h12v-5.99h-.01L18 16l-4-4 4-3.99-.01-.01H18V2H6zm10 14.5V20H8v-3.5l4-4 4 4zm-4-5l-4-4V4h8v3.5l-4 4z"></path></svg>
                                    <?php echo esc_html(__("Waiting Response",'majestic-support')); ?>
                                </span>
                            </div>
                        </a>
                    </div>
                    <div class="mjtc-myticket-link-wrap mjtc-support-myticket-link-myticket">
                        <a class="mjtc-support-red mjtc-myticket-link <?php echo esc_attr($MJTC_answered); ?>" href="#" data-tab-number="2">
                            <svg viewBox="0 0 24 24" class="mjtc-support-card-bgsvg"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-2 16l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z"></path></svg>
                            <div class="mjtc-support-card-left-wrp">
                                <div class="mjtc-support-card-title">
                                    <?php echo esc_html(__("Closed Tickets",'majestic-support')); ?>
                                </div>
                                <span class="mjtc-support-circle-count-text mjtc-support-red">
                                    <?php
                                        if(majesticsupport::$_config['count_on_myticket'] == 1){
                                        $MJTC_data =esc_html(majesticsupport::$_data['count']['closedticket']);
                                        echo wp_kses($MJTC_data, MJTC_ALLOWED_TAGS);
                                        }
                                    ?>
                                </span>
                            </div>
                            <div class="mjtc-support-card-right-wrp">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                                    <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="mjtc-support-card-percentage-duration-wrp">
                                <span class="mjtc-support-card-percentage">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3 h-3">
                                        <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"></path>
                                    </svg>
                                    <?php echo esc_html(__("Case Closed",'majestic-support')); ?>
                                </span>
                            </div>
                        </a>
                    </div>
                </div>
                <!-- Search Form -->
                <div class="mjtc-support-search-wrp">
                    <div class="mjtc-support-form-wrp">
                        <form class="mjtc-filter-form" name="majesticsupportform" id="majesticsupportform" method="POST" action="<?php echo esc_url(wp_nonce_url(majesticsupport::makeUrl(array('mjsmod'=>'ticket','mjslay'=>'myticket')),"my-ticket")); ?>">
                            <div class="mjtc-filter-wrapper">
                                <div class="mjtc-filter-wrapper-toggle-search-wrapper">
                                    <div class="mjtc-filter-form-fields-wrp" id="mjtc-filter-wrapper-toggle-search">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="absolute left-5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 group-focus-within:text-indigo-500 transition-colors">
                                            <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd"></path>
                                        </svg>
                                        <?php
                                        $MJTC_emailaddress = '';
                                        if(!empty($MJTC_search_field_array['email'])) {
                                            $MJTC_emailaddress = ' ' . esc_html(__('Or', 'majestic-support')) . ' ' . esc_html(majesticsupport::MJTC_getVarValue($MJTC_search_field_array['email']));
                                        }
                                        $MJTC_subject = '';
                                        if(!empty($MJTC_search_field_array['subject'])) {
                                            $MJTC_subject = ' ' . esc_html(__('Or', 'majestic-support')) . ' ' . esc_html(majesticsupport::MJTC_getVarValue($MJTC_search_field_array['subject']));
                                        }
                                        echo wp_kses(MJTC_formfield::MJTC_text('ms-ticketsearchkeys', isset(majesticsupport::$_data['filter']['ticketsearchkeys']) ? majesticsupport::$_data['filter']['ticketsearchkeys'] : '', array('class' => 'mjtc-support-input-field','placeholder' => esc_html(__('Ticket ID', 'majestic-support')) . $MJTC_emailaddress . $MJTC_subject)), MJTC_ALLOWED_TAGS); ?>
                                    </div>
                                    <div id="mjtc-filter-wrapper-toggle-area" class="mjtc-filter-wrapper-toggle-ticketid">
                                        <div class="mjtc-filter-form-fields-wrp">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="input-icon"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 8.25h15m-16.5 7.5h15m-1.8-13.5l-3.9 19.5m-2.1-19.5l-3.9 19.5"></path></svg>
                                            <?php echo wp_kses(MJTC_formfield::MJTC_text('ms-ticket', isset(majesticsupport::$_data['filter']['ticketid']) ? majesticsupport::$_data['filter']['ticketid'] : '', array('class' => 'mjtc-support-input-field', 'placeholder' => esc_html(__('Ticket ID', 'majestic-support')))), MJTC_ALLOWED_TAGS); ?>
                                        </div>
                                    </div>
                                    <div class="mjtc-filter-button-wrp">
                                        <div class="mjtc-filter-button-lftwrp">
                                            <a href="#" class="mjtc-search-filter-btn" id="mjtc-search-filter-toggle-btn">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M2.628 1.601C5.028 1.206 7.49 1 10 1s4.973.206 7.372.601a.75.75 0 01.628.74v2.288a2.25 2.25 0 01-.659 1.59l-4.682 4.683a2.25 2.25 0 00-.659 1.59v3.037c0 .684-.31 1.33-.844 1.757l-1.937 1.55A.75.75 0 018 18.25v-5.757a2.25 2.25 0 00-.659-1.591L2.659 6.22A2.25 2.25 0 012 4.629V2.34a.75.75 0 01.628-.74z" clip-rule="evenodd"></path>
                                                </svg>
                                                <?php echo esc_html(__('Advanced Filters','majestic-support')); ?>
                                                <svg id="mjtc-filterArrow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"></path>
                                                </svg>
                                            </a>
                                            <div class="mjtc-support-filter-reset-btn-wrp">
                                                <?php echo wp_kses(MJTC_formfield::MJTC_submitbutton('ms-reset', esc_html(__('Reset', 'majestic-support')), array('class' => 'mjtc-support-filter-button mjtc-support-reset-btn', 'onclick' => 'return resetForm();')), MJTC_ALLOWED_TAGS); ?>
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="mjtc-support-filter-reset-btnicon"><path fill-rule="evenodd" d="M15.312 11.424a5.5 5.5 0 01-9.201 2.466l-.312-.311h2.433a.75.75 0 000-1.5H3.989a.75.75 0 00-.75.75v4.242a.75.75 0 001.5 0v-2.43l.31.31a7 7 0 0011.712-3.138.75.75 0 00-1.449-.39zm1.23-3.723a.75.75 0 00.219-.53V2.929a.75.75 0 00-1.5 0V5.36l-.31-.31A7 7 0 003.239 8.188a.75.75 0 101.448.389A5.5 5.5 0 0113.89 6.11l.311.31h-2.432a.75.75 0 000 1.5h4.243a.75.75 0 00.53-.219z" clip-rule="evenodd"></path></svg>
                                            </div>
                                        </div>
                                        <?php echo wp_kses(MJTC_formfield::MJTC_submitbutton('ms-go', esc_html(__('Search', 'majestic-support')), array('class' => 'mjtc-support-filter-button mjtc-support-search-btn')), MJTC_ALLOWED_TAGS); ?>
                                    </div>
                                </div>
                                <div class="mjtc-filter-form-dynamic-fields">
                                    <div class="mjtc-filter-form-dynamic-fields-inner-wrp">
                                        <?php 
                                        if (!empty($MJTC_search_field_array['subject'])) { ?>
                                            <div class="mjtc-filter-field-wrp">
                                                <label class="mjtc-filter-field-label">
                                                    <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_search_field_array['subject'])); ?>
                                                </label>
                                                <div class="mjtc-filter-inputfield-wrp">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="input-icon">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12"></path>
                                                    </svg>
                                                    <?php echo wp_kses(MJTC_formfield::MJTC_text('ms-subject', isset(majesticsupport::$_data['filter']['subject']) ? majesticsupport::$_data['filter']['subject'] : '', array('class' => 'mjtc-support-input-field', 'placeholder' => majesticsupport::MJTC_getVarValue($MJTC_search_field_array['subject']))), MJTC_ALLOWED_TAGS); ?>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                        if (!empty($MJTC_search_field_array['fullname'])) { ?>
                                            <div class="mjtc-filter-field-wrp">
                                                <label class="mjtc-filter-field-label"><?php echo esc_html(__("From",'majestic-support')); ?></label>
                                                <div class="mjtc-filter-inputfield-wrp">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="input-icon">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"></path>
                                                    </svg>
                                                    <?php echo wp_kses(MJTC_formfield::MJTC_text('ms-from', isset(majesticsupport::$_data['filter']['from']) ? majesticsupport::$_data['filter']['from'] : '', array('class' => 'mjtc-support-input-field', 'placeholder' => esc_html(__('From', 'majestic-support')))), MJTC_ALLOWED_TAGS); ?>
                                                </div>
                                            </div>
                                            <?php 
                                        }
                                        if (!empty($MJTC_search_field_array['phone'])) { ?>
                                            <div class="mjtc-filter-field-wrp">
                                                <label class="mjtc-filter-field-label">
                                                    <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_search_field_array['phone'])); ?>
                                                </label>
                                                <div class="mjtc-filter-inputfield-wrp">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="input-icon">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"></path>
                                                    </svg>
                                                    <?php echo wp_kses(MJTC_formfield::MJTC_text('ms-phone', isset(majesticsupport::$_data['filter']['phone']) ? majesticsupport::$_data['filter']['phone'] : '', array('class' => 'mjtc-support-input-field', 'placeholder' => esc_html(majesticsupport::MJTC_getVarValue($MJTC_search_field_array['phone'])))), MJTC_ALLOWED_TAGS); ?>
                                                </div>
                                            </div>
                                            <?php 
                                        }
                                        if (!empty($MJTC_search_field_array['product'])) { ?>
                                            <div class="mjtc-filter-field-wrp">
                                                <label class="mjtc-filter-field-label">
                                                    <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_search_field_array['product'])); ?>
                                                </label>
                                                <div class="mjtc-filter-inputfield-wrp">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="input-icon"><path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9"></path></svg>
                                                    <?php echo wp_kses(MJTC_formfield::MJTC_select('ms-productid', MJTC_includer::MJTC_getModel('product')->getProductForCombobox(), isset(majesticsupport::$_data['filter']['productid']) ? majesticsupport::$_data['filter']['productid'] : '', esc_html(__('Select', 'majestic-support')).' '.esc_attr(majesticsupport::MJTC_getVarValue($MJTC_search_field_array['product']))), MJTC_ALLOWED_TAGS); ?>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                        if (!empty($MJTC_search_field_array['department'])) { ?>
                                            <div class="mjtc-filter-field-wrp">
                                                <label class="mjtc-filter-field-label">
                                                    <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_search_field_array['department'])); ?>
                                                </label>
                                                <div class="mjtc-filter-inputfield-wrp">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5M12 6.75h1.5M15 6.75h1.5M9 10.5h1.5M12 10.5h1.5M15 10.5h1.5M9 14.25h1.5M12 14.25h1.5M15 14.25h1.5M9 18h1.5M12 18h1.5M15 18h1.5"></path>
                                                    </svg>
                                                    <?php echo wp_kses(MJTC_formfield::MJTC_select('ms-departmentid', MJTC_includer::MJTC_getModel('department')->getDepartmentForCombobox(), isset(majesticsupport::$_data['filter']['departmentid']) ? majesticsupport::$_data['filter']['departmentid'] : '', esc_html(__('Select', 'majestic-support')).' '.esc_attr(majesticsupport::MJTC_getVarValue($MJTC_search_field_array['department']))), MJTC_ALLOWED_TAGS); ?>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                        if(!empty($MJTC_search_field_array['helptopic']) && in_array('helptopic', majesticsupport::$_active_addons)) { ?>
                                            <div class="mjtc-filter-field-wrp">
                                                <label class="mjtc-filter-field-label">
                                                    <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_search_field_array['helptopic'])); ?>
                                                </label>
                                                <div class="mjtc-filter-inputfield-wrp">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="input-icon">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z"></path>
                                                    </svg>
                                                    <?php echo wp_kses(MJTC_formfield::MJTC_select('ms-helptopicid', MJTC_includer::MJTC_getModel('helptopic')->getHelpTopicsForCombobox(), isset(majesticsupport::$_data['filter']['helptopicid']) ? majesticsupport::$_data['filter']['helptopicid'] : '', esc_html(__('Select', 'majestic-support')).' '.majesticsupport::MJTC_getVarValue($MJTC_search_field_array['helptopic'])), MJTC_ALLOWED_TAGS); ?>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                        if(!empty($MJTC_search_field_array['email'])) { ?>
                                            <div class="mjtc-filter-field-wrp">
                                                <label class="mjtc-filter-field-label">
                                                    <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_search_field_array['email'])); ?>
                                                </label>
                                                <div class="mjtc-filter-inputfield-wrp">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="input-icon">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"></path>
                                                    </svg>
                                                    <?php echo wp_kses(MJTC_formfield::MJTC_text('ms-email', isset(majesticsupport::$_data['filter']['email']) ? majesticsupport::$_data['filter']['email'] : '', array('class' => 'mjtc-support-input-field', 'placeholder' => majesticsupport::MJTC_getVarValue($MJTC_search_field_array['email']))), MJTC_ALLOWED_TAGS); ?>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                        if(!empty($MJTC_search_field_array['priority'])) { ?>
                                            <div class="mjtc-filter-field-wrp">
                                                <label class="mjtc-filter-field-label">
                                                    <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_search_field_array['priority'])); ?>
                                                </label>
                                                <div class="mjtc-filter-inputfield-wrp">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="input-icon">
                                                        <path d="M3.5 2.75a.75.75 0 00-1.5 0v14.5a.75.75 0 001.5 0v-4.392l1.657-.348a6.449 6.449 0 014.271.572 7.948 7.948 0 005.965.524l2.078-.643a.75.75 0 00.529-.716V4.75a.75.75 0 00-.529-.716l-2.078-.643a6.449 6.449 0 01-4.271-.572 7.948 7.948 0 00-5.965-.524l-1.657.348V2.75z"></path>
                                                    </svg>
                                                    <?php echo wp_kses(MJTC_formfield::MJTC_select('ms-priorityid', MJTC_includer::MJTC_getModel('priority')->getPriorityForCombobox(), isset(majesticsupport::$_data['filter']['priorityid']) ? majesticsupport::$_data['filter']['priorityid'] : '', esc_html(__('Select', 'majestic-support')).' '.esc_attr(majesticsupport::MJTC_getVarValue($MJTC_search_field_array['priority']))), MJTC_ALLOWED_TAGS); ?>
                                                </div>
                                            </div>
                                            <?php
                                        } ?>
                                        <div class="mjtc-filter-field-wrp">
                                            <label class="mjtc-filter-field-label">
                                                <?php echo esc_html(__("Start Date",'majestic-support')); ?>
                                            </label>
                                            <div class="mjtc-filter-inputfield-wrp">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="input-icon">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"></path>
                                                </svg>
                                                <?php echo wp_kses(MJTC_formfield::MJTC_text('ms-datestart', isset(majesticsupport::$_data['filter']['datestart']) ? majesticsupport::$_data['filter']['datestart'] : '', array('class' => 'custom_date mjtc-support-input-field', 'placeholder' => esc_html(__('Start Date', 'majestic-support')))), MJTC_ALLOWED_TAGS); ?>
                                            </div>
                                        </div>
                                        <div class="mjtc-filter-field-wrp">
                                            <label class="mjtc-filter-field-label">
                                                <?php echo esc_html(__("End Date",'majestic-support')); ?>
                                            </label>
                                            <div class="mjtc-filter-inputfield-wrp">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="input-icon">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"></path>
                                                </svg>
                                                <?php echo wp_kses(MJTC_formfield::MJTC_text('ms-dateend', isset(majesticsupport::$_data['filter']['dateend']) ? majesticsupport::$_data['filter']['dateend'] : '', array('class' => 'custom_date mjtc-support-input-field', 'placeholder' => esc_html(__('End Date', 'majestic-support')))), MJTC_ALLOWED_TAGS); ?>
                                            </div>
                                        </div>
                                        <?php if(class_exists('WooCommerce') && in_array('woocommerce', majesticsupport::$_active_addons) && !empty($MJTC_search_field_array['wcorderid'])){  ?>
                                            <div class="mjtc-filter-field-wrp">
                                                <label class="mjtc-filter-field-label">
                                                    <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_search_field_array['wcorderid'])); ?>
                                                </label>
                                                <div class="mjtc-filter-inputfield-wrp">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5M12 6.75h1.5M15 6.75h1.5M9 10.5h1.5M12 10.5h1.5M15 10.5h1.5M9 14.25h1.5M12 14.25h1.5M15 14.25h1.5M9 18h1.5M12 18h1.5M15 18h1.5"></path>
                                                    </svg>
                                                    <?php echo wp_kses(MJTC_formfield::MJTC_text('ms-orderid', isset(majesticsupport::$_data['filter']['orderid']) ? majesticsupport::$_data['filter']['orderid'] : '', array('class' => 'mjtc-support-input-field', 'placeholder' => majesticsupport::MJTC_getVarValue($MJTC_search_field_array['wcorderid']))), MJTC_ALLOWED_TAGS); ?>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                        if(!empty($MJTC_field_array['eddorderid']) && in_array('easydigitaldownloads', majesticsupport::$_active_addons) && class_exists('Easy_Digital_Downloads') && !empty($MJTC_search_field_array['eddorderid'])){  ?>
                                            <div class="mjtc-filter-field-wrp">
                                                <label class="mjtc-filter-field-label">
                                                    <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_search_field_array['eddorderid'])); ?>
                                                </label>
                                                <div class="mjtc-filter-inputfield-wrp">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5M12 6.75h1.5M15 6.75h1.5M9 10.5h1.5M12 10.5h1.5M15 10.5h1.5M9 14.25h1.5M12 14.25h1.5M15 14.25h1.5M9 18h1.5M12 18h1.5M15 18h1.5"></path>
                                                    </svg>
                                                    <?php echo wp_kses(MJTC_formfield::MJTC_text('ms-eddorderid', isset(majesticsupport::$_data['filter']['eddorderid']) ? majesticsupport::$_data['filter']['eddorderid'] : '', array('class' => 'mjtc-support-input-field', 'placeholder' => majesticsupport::MJTC_getVarValue($MJTC_search_field_array['eddorderid']))), MJTC_ALLOWED_TAGS); ?>
                                                </div>
                                            </div>
                                            <?php
                                        } ?>
                                        <div class="mjtc-filter-field-wrp">
                                            <label class="mjtc-filter-field-label">
                                                <?php echo esc_html(__("Status",'majestic-support')); ?>
                                            </label>
                                            <div class="mjtc-filter-inputfield-wrp">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="input-icon">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <?php echo wp_kses(MJTC_formfield::MJTC_select('ms-status', MJTC_includer::MJTC_getModel('status')->getStatusForFilter(), isset(majesticsupport::$_data['filter']['status']) ? majesticsupport::$_data['filter']['status'] : '', esc_html(__('Select Status', 'majestic-support'))), MJTC_ALLOWED_TAGS); ?>
                                            </div>
                                        </div>
                                        <?php
                                        $MJTC_customfields = MJTC_includer::MJTC_getObjectClass('customfields')->userFieldsForSearch(1);
                                            foreach ($MJTC_customfields as $MJTC_field) {
                                                MJTC_includer::MJTC_getObjectClass('customfields')->MJTC_formCustomFieldsForSearch($MJTC_field, $MJTC_k);
                                            }
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <?php echo wp_kses(MJTC_formfield::MJTC_hidden('sortby', isset(majesticsupport::$_data['filter']['sortby']) ? majesticsupport::$_data['filter']['sortby'] :'' ), MJTC_ALLOWED_TAGS); ?>
                            <?php echo wp_kses(MJTC_formfield::MJTC_hidden('list', $MJTC_list), MJTC_ALLOWED_TAGS); ?>
                            <?php echo wp_kses(MJTC_formfield::MJTC_hidden('MS_form_search', 'MS_SEARCH'), MJTC_ALLOWED_TAGS); ?>
                            <?php echo wp_kses(MJTC_formfield::MJTC_hidden('mspageid', get_the_ID()), MJTC_ALLOWED_TAGS); ?>
                            <?php echo wp_kses(MJTC_formfield::MJTC_hidden('mjtcslay', 'myticket'), MJTC_ALLOWED_TAGS); ?>
                        </form>
                    </div>
                </div>
                <!-- Sorting Wrapper -->
                <?php
                $MJTC_link = majesticsupport::makeUrl(array('mjsmod'=>'ticket','mjslay'=>'myticket','list'=> majesticsupport::$_data['list']));
                if (majesticsupport::$_sortorder == 'ASC')
                    $MJTC_img = "sorting-1.png";
                else
                    $MJTC_img = "sorting-2.png";
                ?>
                <div class="mjtc-support-sorting">
                    <div class="mjtc-support-sorting-left">
                        <div class="mjtc-support-sorting-heading">
                            <?php 
                            $MJTC_list = majesticsupport::$_data['list'];
                            if($MJTC_list == 1){
                                echo esc_html(__('Open','majestic-support')).' ';
                            } elseif ($MJTC_list == 2){
                                echo esc_html(__('Closed','majestic-support')).' ';
                            } elseif ($MJTC_list == 3){
                                echo esc_html(__('Answered','majestic-support')).' ';
                            } elseif ($MJTC_list == 5){
                                echo esc_html(__('Overdue','majestic-support')).' ';
                            } elseif ($MJTC_list == 4){
                                echo esc_html(__('All','majestic-support')).' ';
                            }?>
                            <?php echo esc_html(__('Tickets','majestic-support')); ?>
                        </div>
                    </div>
                    <div class="mjtc-support-sorting-right">
                        <div class="mjtc-support-sort">
                            <select class="mjtc-support-sorting-select">
                                <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field_array['subject'])); ?>
                                <option value="<?php echo esc_attr(majesticsupport::$_sortlinks['subject']); ?>"
                                    <?php if (majesticsupport::$_sorton == 'subject') echo esc_html('selected') ?>>
                                    <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field_array['subject'])); ?></option>
                                <?php
                                if (!empty($MJTC_field_array['priority'])) { ?>
                                    <option value="<?php echo esc_attr(majesticsupport::$_sortlinks['priority']); ?>"
                                    <?php if (majesticsupport::$_sorton == 'priority') echo esc_html('selected') ?>>
                                    <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field_array['priority'])); ?></option>
                                <?php } ?>
                                <option value="<?php echo esc_attr(majesticsupport::$_sortlinks['ticketid']); ?>"
                                    <?php if (majesticsupport::$_sorton == 'ticketid') echo esc_html('selected') ?>>
                                    <?php echo esc_html(__("Ticket ID",'majestic-support')); ?></option>
                                <option value="<?php echo esc_attr(majesticsupport::$_sortlinks['isanswered']); ?>"
                                    <?php if (majesticsupport::$_sorton == 'isanswered') echo esc_html('selected') ?>>
                                    <?php echo esc_html(__("Answered",'majestic-support')); ?></option>
                                <option value="<?php echo esc_attr(majesticsupport::$_sortlinks['status']); ?>"
                                    <?php if (majesticsupport::$_sorton == 'status') echo esc_html('selected') ?>>
                                    <?php echo esc_html(__("Status",'majestic-support')); ?></option>
                                <option value="<?php echo esc_attr(majesticsupport::$_sortlinks['created']); ?>"
                                    <?php if (majesticsupport::$_sorton == 'created') echo esc_html('selected') ?>>
                                    <?php echo esc_html(__("Created",'majestic-support')); ?></option>
                            </select>
                            <a href="#" class="mjtc-admin-sort-btn" title="<?php echo esc_attr(__('sort','majestic-support')); ?>">
                                <img alt="<?php echo esc_attr(__('sort','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL) . 'includes/images/' . esc_attr($MJTC_img) ?>">
                            </a>
                        </div>
                    </div>
                </div>
            <?php
            if (!empty(majesticsupport::$_data[0])) {
                $MJTC_fields_array = array(); // Array for form fields
                $MJTC_show_on_listing_arrays = array(); // Array for visible form fields
                foreach (majesticsupport::$_data[0] AS $MJTC_ticket) {
                    // Check if the form fields are already array
                    if (!isset($MJTC_fields_array[$MJTC_ticket->multiformid])) {
                        $MJTC_fields_array[$MJTC_ticket->multiformid] = MJTC_includer::MJTC_getModel('fieldordering')->getFieldTitleByFieldfor(1, $MJTC_ticket->multiformid);
                    }
                    if (!isset($MJTC_show_on_listing_arrays[$MJTC_ticket->multiformid])) {
                        $MJTC_show_on_listing_arrays[$MJTC_ticket->multiformid] = MJTC_includer::MJTC_getModel('fieldordering')->getFieldsForListing(1, $MJTC_ticket->multiformid);
                    }
                    // Now use the cached field array
                    $MJTC_field_array = $MJTC_fields_array[$MJTC_ticket->multiformid];
                    $MJTC_show_on_listing_array = $MJTC_show_on_listing_arrays[$MJTC_ticket->multiformid];

                    $MJTC_closedTicketClass = "";
                    if ($MJTC_ticket->status == 5 || $MJTC_ticket->status == 6) {
                        $MJTC_closedTicketClass = "mjtc-support-closed-ticket-wrapper";
                    }
                    $MJTC_ticketviamail = '';
                    if ($MJTC_ticket->ticketviaemail == 1)
                        $MJTC_ticketviamail = esc_html(__('Created via Email', 'majestic-support'));
                    ?>
                    <div class="mjtc-support-ticket-wrapper <?php echo esc_attr($MJTC_closedTicketClass); ?>">
                        <div class="mjtc-support-priority-lftbrder"style="background:<?php echo esc_attr($MJTC_ticket->prioritycolour); ?>;"></div>
                        <div class="mjtc-support-toparea">
                            
                            <div class="mjtc-support-pic">
                                <?php 
                                    echo wp_kses(MJTC_get_avatar($MJTC_ticket->uid), MJTC_ALLOWED_TAGS);
                                ?>
                            </div>
                            <div class="mjtc-support-data mjtc-nullpadding">
                                <?php 
                                    if ($MJTC_ticket->closed != '0000-00-00 00:00:00' && $MJTC_ticket->status == 5 && majesticsupport::$_config['show_closedby_on_user_tickets'] == 1) {?>
                                        <span class="mjtc-support-closed-date">
                                            <?php echo esc_html(__("Closed On", 'majestic-support')). " " . esc_html(date_i18n(majesticsupport::$_config['date_format'], MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->closed))); ?>
                                        </span>
                                        <?php 
                                    } else if ($MJTC_ticket->lastreply != '0000-00-00 00:00:00' && !empty($MJTC_ticket->lastreply)) { ?>
                                        <div class="mjtc-support-last-reply-badge mjtc-badge-default">
                                            <svg viewBox="0 0 24 24" width="10" height="10" class="fill-current"><path d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-2 12H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z"></path></svg>
                                            <?php echo esc_html(__('Last Reply', 'majestic-support')) . ': '; ?>
                                            <?php echo esc_html(date_i18n(majesticsupport::$_config['date_format'], MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->lastreply))); ?>
                                        </div>
                                        <?php
                                    } ?>
                                <div class="mjtc-support-padding-xs mjtc-support-body-data-elipses">
                                    <div class="mjtc-support-data-row">
                                        <?php 
                                        if (!empty($MJTC_show_on_listing_array['fullname'])) {
                                            if (isset($MJTC_field_array['fullname'])) { ?>
                                                <span class="mjtc-support-value mjtc-support-name" style="cursor:pointer;" onClick="setFromNameFilter('<?php echo esc_js($MJTC_ticket->email); ?>');"><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_ticket->name)); ?></span>
                                                <?php
                                            }
                                        } ?>
                                        <span class="mjtc-support-data-val mjtc-support-ticketid">
                                            <span class="mjtc-support-ticketiddot">•</span>
                                            <?php echo esc_html($MJTC_ticket->ticketid); ?>
                                        </span>
                                        <span class="mjtc-support-status" style="background-color: <?php echo esc_attr($MJTC_ticket->statusbgcolour); ?>;color:<?php echo esc_attr($MJTC_ticket->statuscolour); ?>;">
                                            <?php
                                            echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_ticket->statustitle)); ?>
                                        </span>
                                        <?php 
                                        if (!empty($MJTC_show_on_listing_array['priority'])) { ?>
                                            <span class="mjtc-support-wrapper-textcolor" style="background:<?php echo esc_attr($MJTC_ticket->prioritycolour); ?>;">
                                                <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_ticket->priority)); ?>
                                            </span>
                                            <?php
                                        } 
                                        ?>
                                    </div>
                                </div>
                                <div class="mjtc-support-padding-xs mjtc-support-body-data-elipses">
                                    <a class="mjtc-support-title-anchor" href="<?php echo esc_url(majesticsupport::makeUrl(array('mjsmod'=>'ticket','mjslay'=>'ticketdetail','majesticsupportid'=> $MJTC_ticket->id))); ?>"><?php echo esc_html($MJTC_ticket->subject); ?></a>
                                </div>
                                <div class="mjtc-support-body-data-discription">
                                    <?php echo esc_html(wp_strip_all_tags($MJTC_ticket->message)); ?>
                                </div>
                                <div class="mjtc-support-body-data-btmwrp">
                                    <div class="mjtc-support-data-row">
                                        <div class="mjtc-support-data-val">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5 text-gray-400">
                                                <title><?php echo esc_html(__('Created', 'majestic-support')); ?></title>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"></path>
                                            </svg>
                                            <?php echo esc_html(date_i18n(majesticsupport::$_config['date_format'], MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))); ?>
                                        </div>
                                    </div>
                                    <?php 
                                    foreach ($MJTC_show_on_listing_array AS $MJTC_field_field => $MJTC_field_title) {
                                        switch ($MJTC_field_field) {
                                            case 'department': 
                                                if (!empty($MJTC_ticket->departmentname)) { ?>
                                                    <div class="mjtc-support-padding-xs mjtc-support-body-data-elipses">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5 text-gray-400">
                                                            <title><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field_array['department'])); ?></title>
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"></path>
                                                        </svg>
                                                        <span class="mjtc-support-value"><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_ticket->departmentname)); ?></span>
                                                    </div>
                                                    <?php
                                                }
                                                break;
                                            case 'email': 
                                                if (!empty($MJTC_ticket->email)) { ?>
                                                    <div class="mjtc-support-padding-xs mjtc-support-body-data-elipses">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="input-icon">
                                                            <title><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field_array['email'])); ?></title>
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"></path>
                                                        </svg>
                                                        <span class="mjtc-support-value"><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_ticket->email)); ?></span>
                                                    </div>
                                                    <?php
                                                }
                                                break;
                                            case 'phone':
                                                if (!empty($MJTC_ticket->phone)) { ?>
                                                    <div class="mjtc-support-padding-xs mjtc-support-body-data-elipses">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="input-icon">
                                                            <title><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field_array['phone'])); ?></title>
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"></path>
                                                        </svg>
                                                        <span class="mjtc-support-value"><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_ticket->phone)); ?></span>
                                                    </div>
                                                    <?php
                                                }
                                                break;
                                            case 'product': 
                                                if (!empty($MJTC_ticket->producttitle)) {?>
                                                    <div class="mjtc-support-padding-xs mjtc-support-body-data-elipses">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="input-icon">
                                                            <title><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field_array['product'])); ?></title>
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9"></path>
                                                        </svg>
                                                        <span class="mjtc-support-value"><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_ticket->producttitle)); ?></span>
                                                    </div>
                                                    <?php
                                                }
                                                break;
                                            case 'helptopic': 
                                                if (in_array('helptopic', majesticsupport::$_active_addons) && !empty($MJTC_ticket->departmentname)) { ?>
                                                    <div class="mjtc-support-padding-xs mjtc-support-body-data-elipses">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="input-icon">
                                                            <title><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field_array['helptopic'])); ?></title>
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z"></path>
                                                        </svg>
                                                        <span class="mjtc-support-value"><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_ticket->topic)); ?></span>
                                                    </div>
                                                <?php
                                                }
                                                break;
                                            case 'eddorderid':
                                                if (!empty($MJTC_ticket->eddorderid)) {?>
                                                    <div class="mjtc-support-padding-xs mjtc-support-body-data-elipses">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                            <title><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field_array['eddorderid'])); ?></title>
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5M12 6.75h1.5M15 6.75h1.5M9 10.5h1.5M12 10.5h1.5M15 10.5h1.5M9 14.25h1.5M12 14.25h1.5M15 14.25h1.5M9 18h1.5M12 18h1.5M15 18h1.5"></path>
                                                        </svg>
                                                        <span class="mjtc-support-value"><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_ticket->eddorderid)); ?></span>
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
                                                if (empty($MJTC_ticket->eddproductid)) {
                                                    break;
                                                } ?>
                                                <div class="mjtc-support-padding-xs mjtc-support-body-data-elipses">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <title><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field_array['eddproductid'])); ?></title>
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5l9-4.5 9 4.5M3 7.5l9 4.5m0 0l9-4.5M12 12v9M3 7.5v9l9 4.5 9-4.5v-9" />
                                                    </svg>
                                                    <span class="mjtc-support-value"><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_ticket->eddproductid)); ?></span>
                                                </div>
                                                <?php
                                                break;
                                            default:
                                                break;
                                        }
                                    }
                                    majesticsupport::$_data['custom']['ticketid'] = $MJTC_ticket->id;
                                    $MJTC_customfields = MJTC_includer::MJTC_getObjectClass('customfields')->MJTC_userFieldsData(1, 1);
                                    foreach ($MJTC_customfields as $MJTC_field) {
                                        if ($MJTC_field->userfieldtype != 'termsandconditions') {
                                            $MJTC_ret = MJTC_includer::MJTC_getObjectClass('customfields')->MJTC_showCustomFields($MJTC_field,1, $MJTC_ticket->params);
                                            if (!empty($MJTC_ret['value'])) {
                                                ?>
                                                <div class="mjtc-support-padding-xs mjtc-support-body-data-elipses mjtc-support-custom-field-wrp">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M5.5 3A2.5 2.5 0 003 5.5v2.879a2.5 2.5 0 00.732 1.767l6.5 6.5a2.5 2.5 0 003.536 0l2.878-2.878a2.5 2.5 0 000-3.536l-6.5-6.5A2.5 2.5 0 008.38 3H5.5zM6 7a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    <span class="mjtc-support-field-title"><?php echo esc_html($MJTC_ret['title']); ?>:</span>
                                                    <span class="mjtc-support-value"><?php echo wp_kses($MJTC_ret['value'], MJTC_ALLOWED_TAGS); ?></span>
                                                </div>
                                                <?php
                                            }
                                        }
                                    }
                                    if ($MJTC_ticket->ticketviaemail == 1){  ?>
                                        <span class="mjtc-support-value mjtc-support-creade-via-email-spn"><?php echo esc_html($MJTC_ticketviamail); ?></span>
                                    <?php }?>
                                </div>
                            </div>
                            <div class="mjtc-support-data1 mjtc-support-padding-left-xs">
                                <div class="mjtc-support-data-row">
                                    <div class="flex items-center gap-1.5 mb-2">
                                        <a class="mjtc-support-replied-messages" href="<?php echo esc_url(majesticsupport::makeUrl(array('mjsmod' => 'ticket', 'mjslay' => 'ticketdetail', 'majesticsupportid' => $MJTC_ticket->id))); ?>">
                                            <svg viewBox="0 0 24 24" width="14" height="14" class="fill-current opacity-70"><path d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-2 12H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z"></path></svg>
                                            <span class="mjtc-support-replied-messages-count"><?php echo esc_html($MJTC_ticket->reply_count); ?></span>
                                        </a>
                                        <div class="mjtc-support-ticket-status-icon-wrp">
                                            <?php
                                            $MJTC_counter = 'one';
                                            if ($MJTC_ticket->lock == 1 && in_array('actions', majesticsupport::$_active_addons)) { ?>
                                                <span class="mjtc-support-ticket-status-icon-wrp <?php echo esc_attr($MJTC_counter);
                                                $MJTC_counter = 'two'; ?>" title="<?php echo esc_attr(__('The ticket is locked', 'majestic-support')); ?>">
                                                    <svg viewBox="0 0 24 24" width="12" height="12"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"></path></svg>
                                                </span>
                                            <?php }
                                            if ($MJTC_ticket->isoverdue == 1 && in_array('overdue', majesticsupport::$_active_addons)) { ?>
                                                <span class="mjtc-support-ticket-status-icon-wrp <?php echo esc_attr($MJTC_counter); ?>" title="<?php echo esc_attr(__('The ticket marks as overdue', 'majestic-support')); ?>">
                                                    <svg viewBox="0 0 24 24" width="12" height="12"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"></path></svg>
                                                </span>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <?php if ($MJTC_ticket->status == 5 && majesticsupport::$_config['show_closedby_on_user_tickets'] == 1) { ?>
                                        <div class="mjtc-support-data-tit">
                                            <?php
                                            if ($MJTC_ticket->closedby == 0) {
                                                echo esc_html(__('Closed By', 'majestic-support'));
                                            } else {
                                                echo esc_html(__('Resolved By', 'majestic-support'));
                                            } ?>
                                        </div>
                                        <div class="mjtc-support-data-val">
                                            <span class="mjtc-support-staff-logo-wrp">
                                                <?php echo wp_kses(MJTC_get_avatar($MJTC_ticket->closedby), MJTC_ALLOWED_TAGS);  ?>
                                            </span>
                                            <?php echo esc_html(MJTC_includer::MJTC_getModel('ticket')->getClosedBy($MJTC_ticket->closedby)); ?>
                                        </div>    
                                        <?php
                                    } else if (in_array('agent',majesticsupport::$_active_addons) && majesticsupport::$_config['show_assignto_on_user_tickets'] == 1 && isset($MJTC_field_array['assignto'])) { ?>
                                        <div class="mjtc-support-data-tit">
                                            <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field_array['assignto'])); ?>
                                        </div>
                                        <div class="mjtc-support-data-val">
                                            <?php
                                            if($MJTC_ticket->staffuid > 0 ){ ?>
                                                <span class="mjtc-support-staff-logo-wrp">
                                                    <?php echo wp_kses(MJTC_get_avatar($MJTC_ticket->staffuid), MJTC_ALLOWED_TAGS);  ?>
                                                </span>
                                                <?php echo esc_html($MJTC_ticket->staffname); ?>
                                                <?php
                                            } else { ?>
                                                <span class="mjtc-support-staff-logo-wrp mjtc-support-staff-empty-logo-wrp">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 640 512"
                                                        width="20"
                                                        height="20"
                                                        fill="currentColor"
                                                        aria-hidden="true"
                                                        focusable="false">
                                                        <path d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm96 48h-8.7c-22.2 10.2-46.8 16-71.3 16s-49.1-5.8-71.3-16H160C71.6 304 0 375.6 0 464v16c0 17.7 14.3 32 32 32h352c17.7 0 32-14.3 32-32v-16c0-88.4-71.6-160-160-160zm288-112h-48v-48c0-17.7-14.3-32-32-32s-32 14.3-32 32v48h-48c-17.7 0-32 14.3-32 32s14.3 32 32 32h48v48c0 17.7 14.3 32 32 32s32-14.3 32-32v-48h48c17.7 0 32-14.3 32-32s-14.3-32-32-32z"/>
                                                    </svg>
                                                </span>
                                                <span class="mjtc-support-unassign-value"><?php echo esc_html(__("Unassigned", 'majestic-support'))?></span>
                                                <?php
                                            } ?>
                                        </div>
                                        <?php
                                    } ?>
                                    <div class="mjtc-support-hover-actions">
                                        <?php
                                        if (majesticsupport::$_config['show_ticket_delete_button'] == 1) { ?>
                                            <a class="mjtc-support-action-btn-sm mjtc-support-danger" title="<?php echo esc_attr(__('Delete Ticket', 'majestic-support')); ?>" onclick="return confirm('<?php echo esc_html(__('Are you sure you want to delete this ticket', 'majestic-support')); ?>');" href="<?php echo esc_url(wp_nonce_url(majesticsupport::makeUrl(array('mjsmod'=>'ticket','task'=>'deleteticket','action'=>'mstask','internalid'=> $MJTC_ticket->internalid,'ticketid'=> $MJTC_ticket->id ,'mspageid'=>get_the_ID())),'delete-ticket-'.$MJTC_ticket->id)); ?>" data-ticketid="<?php echo esc_attr($MJTC_ticket->id); ?>">
                                                <svg viewBox="0 0 24 24" width="14" height="14">
                                                    <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"></path>
                                                </svg>
                                            </a>
                                            <?php
                                        }
                                        if ($MJTC_ticket->status != 6) {
                                            if ($MJTC_ticket->status != 5) {
                                                if (in_array('ticketclosereason',majesticsupport::$_active_addons)) { ?>
                                                    <a onclick="showTicketCloseReasons(<?php echo esc_html($MJTC_ticket->id)?>, '<?php echo esc_html($MJTC_ticket->internalid)?>')" title="<?php echo esc_attr(__('Close Ticket', 'majestic-support')); ?>" class="mjtc-support-action-btn-sm mjtc-support-success">
                                                        <svg viewBox="0 0 24 24" width="14" height="14"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 13.17l7.59-7.59L19 7l-9 9z"></path></svg>
                                                    </a>
                                                    <?php
                                                } else { ?>
                                                    <a onclick="return confirm('<?php echo esc_html(__('Are you sure to close this ticket', 'majestic-support')); ?>');" title="<?php echo esc_attr(__('Close Ticket', 'majestic-support')); ?>" class="mjtc-support-action-btn-sm mjtc-support-success" href="<?php echo esc_url(wp_nonce_url(majesticsupport::makeUrl(array('mjsmod'=>'ticket','task'=>'closeticket','action'=>'mstask','ticketid'=> $MJTC_ticket->id,'internalid'=> $MJTC_ticket->internalid ,'mspageid'=>get_the_ID())),"close-ticket-".$MJTC_ticket->id)); ?>">
                                                        <svg viewBox="0 0 24 24" width="14" height="14"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 13.17l7.59-7.59L19 7l-9 9z"></path></svg>
                                                    </a>
                                                    <?php
                                                }
                                            } else {
                                                if (MJTC_includer::MJTC_getModel('ticket')->checkCanReopenTicket($MJTC_ticket->id)) {
                                                    $MJTC_link = wp_nonce_url(majesticsupport::makeUrl(array('mjsmod'=>'ticket','task'=>'reopenticket','action'=>'mstask','ticketid'=> $MJTC_ticket->id,'internalid'=> $MJTC_ticket->internalid,'redirect'=> 3,'mspageid'=>get_the_ID())),"reopen-ticket-".$MJTC_ticket->id); ?>
                                                    <a class="mjtc-support-action-btn-sm mjtc-support-danger" href="<?php echo esc_url($MJTC_link); ?>"
                                                        title="<?php echo esc_attr(__('Reopen Ticket', 'majestic-support')); ?>">
                                                        <svg class="mjtc-sprt-icon" viewBox="0 0 24 24"><path d="M1 4v6h6"></path><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg>
                                                    </a>
                                                    <?php
                                                }
                                            }
                                        } ?>
                                        <div class="mjtc-support-divider-vertical mjtc-support-hidden-mobile" style="height: 12px; margin: 0 4px;"></div>
                                        <a href="<?php echo esc_url(majesticsupport::makeUrl(array('mjsmod' => 'ticket', 'mjslay' => 'ticketdetail', 'majesticsupportid' => $MJTC_ticket->id))); ?>" class="mjtc-support-action-btn-sm mjtc-support-hidden-mobile" title="<?php echo esc_attr(__("View Details", 'majestic-support'))?>">
                                            <svg viewBox="0 0 24 24" width="12" height="12"><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"></path></svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                if (majesticsupport::$_data[1]) {
                    $MJTC_data = '<div class="tablenav"><div class="tablenav-pages">' . wp_kses_post(majesticsupport::$_data[1]) . '</div></div>';
                    echo wp_kses($MJTC_data, MJTC_ALLOWED_TAGS);
                }
            } else { // Record Not FOund
                MJTC_layout::MJTC_getNoRecordFound();
            }
        } else {// User is guest
            ?>
            <div class="mjtc-support-cont-wrapper mjtc-support-cont-wrapper-color">
            <?php
                $MJTC_redirect_url = majesticsupport::makeUrl(array('mjsmod'=>'ticket','mjslay'=>'myticket'));
                $MJTC_redirect_url = MJTC_majesticsupportphplib::MJTC_safe_encoding($MJTC_redirect_url);
                MJTC_layout::MJTC_getUserGuest($MJTC_redirect_url); ?>
            </div>
            <?php
        }
    } else { // System is offline
        MJTC_layout::MJTC_getSystemOffline();
    }?>
    </div>
</div>
</div>
