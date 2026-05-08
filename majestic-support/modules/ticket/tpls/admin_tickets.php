<?php
    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    wp_enqueue_script('jquery-ui-datepicker');
    wp_enqueue_style('majesticsupport-jquery-ui-css', MJTC_PLUGIN_URL . 'includes/css/jquery-ui-smoothness.css', array(), '1.0.0');
    wp_enqueue_style('majesticsupport-status-graph', MJTC_PLUGIN_URL . 'includes/css/status_graph.css', array(), '1.0.0');
?>
<?php
$majesticsupport_js ="
    jQuery(document).ready(function ($) {
        jQuery('.asgn-staff').click(function (e) {
            e.preventDefault();
            var ticketid = jQuery(this).attr('data-ticket-id');
            var staffid = jQuery(this).attr('data-staff-id');
            var selectedIds = jQuery('.ticket-checkbox:checked').map(function() {
                return jQuery(this).attr('data-id');
            }).get();

            jQuery.post(ajaxurl, {action: 'mjsupport_ajax', mjsmod: 'ticket', task: 'getHtmlForAssignPopup', selectedIds:selectedIds, ticketid:ticketid, staffid:staffid, ticketlisting:1, '_wpnonce': '". esc_attr(wp_create_nonce("get-html-for-assign-popup"))."'}, function (data) {
                data=jQuery.parseJSON(data);
               if(data !== 'undefined' && data !== '') {
                    jQuery('div#popup-record-data').html('');
                    jQuery('div#popup-record-data').html(MJTC_msDecodeHTML(data));
                    jQuery('div#assignstaff-popup').slideDown('slow');
                    jQuery('div#userpopupblack').show();
                }else{
                    jQuery('div#popup-record-data').html('');
                }
            });//jquery closed
        });
        jQuery('.userpopup-close, div#userpopupblack').click(function (e) {
            jQuery('div#assignstaff-popup').slideUp('slow', function () {
                jQuery('div#userpopupblack').hide();
            });
        });
    });
    function resetFrom() {
        var form = jQuery('form#majesticsupportform');
        form.find('input[type=text], input[type=email], input[type=password], textarea').val('');
        form.find('input:checkbox').removeAttr('checked');
        form.find('select').prop('selectedIndex', 0);
        form.find('input[type=\'radio\']').prop('checked', false);
        document.getElementById('majesticsupportform').submit();
    }
    jQuery(document).ready(function(){
        jQuery('.date,.custom_date').datepicker({dateFormat: 'yy-mm-dd'});
        jQuery('select.mjtc-admin-sort-select').on('change',function(e){
            e.preventDefault();
            var sortby = jQuery('.mjtc-admin-sort-select option:selected').val();
            jQuery('input#sortby').val(sortby);
            jQuery('form#majesticsupportform').submit();
        });
        jQuery('a.mjtc-admin-sort-btn').on('click',function(e){
            e.preventDefault();
            var sortby = jQuery('.mjtc-admin-sort-select option:selected').val();
            jQuery('input#sortby').val(sortby);
            jQuery('form#majesticsupportform').submit();
        });
        jQuery('a.mjtc-support-link').click(function(e){
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
    });

    function setDepartmentFilter( depid ){
        jQuery('#departmentid').val( depid );
        jQuery('form#majesticsupportform').submit();
    }

    function setFromNameFilter( email ){
        jQuery('#email').val( email );
        jQuery('form#majesticsupportform').submit();
    }

    function actionticket(action, ticketid, internalid) {
        /*  Action meaning
         * 1 -> Change Priority
         * 2 -> Close Ticket
         */
        if(action == 1){
            jQuery('#priority').val(jQuery('#prioritytemp').val());
        }
        jQuery('input#actionid').val(action);
        jQuery('input#ticketid').val(ticketid);
        jQuery('input#internalid').val(internalid);
        
        // 1. Get all selected checkbox IDs into an array
        var selectedIds = jQuery('.ticket-checkbox:checked').map(function() {
            return jQuery(this).attr('data-id');
        }).get();
        var total = selectedIds.length;
        if (total > 0) {
            jQuery('#selectedTicketIds').val(selectedIds.join(','));
        }

        jQuery('form#adminTicketform').submit();
    }

    function showTicketCloseReasons(id){
        jQuery('div.ms-popup-other-reason-box').hide();
        var selectedIds = jQuery('.ticket-checkbox:checked').map(function() {
            return jQuery(this).attr('data-id');
        }).get();
        jQuery.post(ajaxurl, {action: 'mjsupport_ajax', mjsmod: 'ticketclosereason', task: 'getTicketCloseReasonsForPopup', selectedIds:selectedIds,isadmin:1,id:id, '_wpnonce':'". esc_attr(wp_create_nonce("get-ticket-close-reasons-for-popup"))."'}, function (data) {
            if(data){
                data=jQuery.parseJSON(data);
                jQuery('div#popup-record-data1').html('');
                jQuery('div#popup-record-data1').html(MJTC_msDecodeHTML(data['data']));
            }
        });
    }

    function closeReasonPopup(saveReason, closeTicket){
        var close = 1;
        if (saveReason == 0 && closeTicket == 1) {
            actionticket(2);
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

    jQuery('.mjtc-support-multioperation').click(function (e) {
        e.preventDefault();
        
        // 1. Get all selected checkbox IDs into an array
        var selectedIds = jQuery('.ticket-checkbox:checked').map(function() {
            return jQuery(this).attr('data-id');
        }).get();

        var total = selectedIds.length;

        if (total > 0) {
            var actionTask = jQuery(this).attr('data-for');
            
            // 2. Add the IDs to a hidden input so the form can send them to PHP
            // Assuming you have an input with id='selectedTicketIds' in your form
            jQuery('#selectedTicketIds').val(selectedIds.join(','));

            if (actionTask.toLowerCase().indexOf('delete') >= 0) {
                if (confirmdelete(jQuery(this).attr('confirmmessage')) == true) {
                    jQuery('input#actionTask').val(actionTask);
                    jQuery('form#adminTicketMultiActionsform').submit();
                }
            } else {
                jQuery('input#actionTask').val(actionTask);
                jQuery('form#adminTicketMultiActionsform').submit();
            }
        } else {
            var message = jQuery(this).attr('message');
            alert(message);
        }
    });

    function confirmdelete(message) {
        if (confirm(message) == true) {
            return true;
        } else {
            return false;
        }
    }

";
wp_add_inline_script('majestic-support-cmain-js',$majesticsupport_js);
MJTC_message::MJTC_getMessage();
?>
<div id="msadmin-wrapper">
    <div id="msadmin-leftmenu">
        <?php
        if(current_user_can('ms_support_ticket')){
            MJTC_includer::MJTC_getClassesInclude('msadminsidemenu');
        }
        ?>
    </div>
    <div class="mjtc-support-content-wrapper" id="msadmin-data">
        <?php MJTC_includer::MJTC_getModel('majesticsupport')->getPageTitle('admin_tickets'); ?>
        <main class="mjtc-support-scroll-area" id="msadmin-data-wrp">
            <div id="popup-record-data1" style="display:inline-block;width:100%;"></div>
            <!-- assign to staff popup -->
            <div id="userpopupblack" style="display:none;"></div>
            <div id="assignstaff-popup" class="ms-popup-wrapper" style="display: none;">
                <?php if ( in_array('agent',majesticsupport::$_active_addons)) { ?>
                    <div class="userpopup-top">
                        <div class="userpopup-heading">
                            <?php echo esc_html(__('Assign To Agent','majestic-support')); ?>
                        </div>
                        <img alt="<?php echo esc_attr(__('Close','majestic-support')); ?>" class="userpopup-close" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/close-icon-white.png" />
                    </div>
                    <div id="popup-record-data" class="mjtc-admin-popup-cnt">
                        
                    </div>
                <?php } ?>
            </div>
            <?php
            $MJTC_list = MJTC_request::MJTC_getVar('list', null, null);
            if($MJTC_list == null){
                $MJTC_list = majesticsupport::$_search['ticket']['list'];
            }
            $MJTC_open = ($MJTC_list == 1) ? 'active' : '';
            $MJTC_answered = ($MJTC_list == 2) ? 'active' : '';
            $MJTC_overdue = ($MJTC_list == 3) ? 'active' : '';
            $MJTC_closed = ($MJTC_list == 4) ? 'active' : '';
            $MJTC_alltickets = ($MJTC_list == 5) ? 'active' : '';
            $MJTC_field_array = MJTC_includer::MJTC_getModel('fieldordering')->getFieldTitleByFieldfor(1);
            $MJTC_search_field_array = MJTC_includer::MJTC_getModel('fieldordering')->getAdminSystemFieldsForSearch();
            ?>
            <?php
            $MJTC_open_percentage = 0;
            $MJTC_close_percentage = 0;
            $MJTC_overdue_percentage = 0;
            $MJTC_answered_percentage = 0;
            $MJTC_allticket_percentage = 0;
            if(isset(majesticsupport::$_data['count']) && isset(majesticsupport::$_data['count']['allticket']) && majesticsupport::$_data['count']['allticket'] != 0){
                $MJTC_open_percentage = round((majesticsupport::$_data['count']['openticket'] / majesticsupport::$_data['count']['allticket']) * 100);
                $MJTC_close_percentage = round((majesticsupport::$_data['count']['closedticket'] / majesticsupport::$_data['count']['allticket']) * 100);
                $MJTC_overdue_percentage = round((majesticsupport::$_data['count']['overdueticket'] / majesticsupport::$_data['count']['allticket']) * 100);
                $MJTC_answered_percentage = round((majesticsupport::$_data['count']['answeredticket'] / majesticsupport::$_data['count']['allticket']) * 100);
            }
            if(isset(majesticsupport::$_data['count']) && isset(majesticsupport::$_data['count']['allticket']) && majesticsupport::$_data['count']['allticket'] != 0){
                $MJTC_allticket_percentage = 100;
            }
            ?>
            <div class="mjtc-support-stats-grid mjtc-support-count">

                <a href="#" class="mjtc-support-link <?php echo esc_attr($MJTC_alltickets); ?> stat-card-enhanced active-filter variant-indigo" data-filter="all" data-tab-number="5" title="<?php echo esc_attr(__('Total Tickets','majestic-support')); ?>">
                    <svg viewBox="0 0 24 24" class="stat-bg-icon"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zm6.93 6h-2.95c-.32-1.25-.78-2.45-1.38-3.56 1.84.63 3.37 1.91 4.33 3.56zM12 4.04c.83 1.2 1.48 2.53 1.91 3.96h-3.82c.43-1.43 1.08-2.76 1.91-3.96zM4.26 14C4.1 13.36 4 12.69 4 12s.1-1.36.26-2h3.38c-.08.66-.14 1.32-.14 2 0 .68.06 1.34.14 2H4.26zm.82 2h2.95c.32 1.25.78 2.45 1.38 3.56-1.84-.63-3.37-1.9-4.33-3.56zm2.95-8H5.08c.96-1.66 2.49-2.93 4.33-3.56C8.81 5.55 8.35 6.75 8.03 8zM12 19.96c-.83-1.2-1.48-2.53-1.91-3.96h3.82c-.43 1.43-1.08 2.76-1.91 3.96zM14.34 14H9.66c-.09-.66-.16-1.32-.16-2 0-.68.07-1.35.16-2h4.68c.09.65.16 1.32.16 2 0 .68-.07 1.34-.16 2zm.25 5.56c.6-1.11 1.06-2.31 1.38-3.56h2.95c-.96 1.65-2.49 2.93-4.33 3.56zM16.36 14c.08-.66.14-1.32.14-2 0-.68-.06-1.34-.14-2h3.38c.16.64.26 1.31.26 2s-.1 1.36-.26 2h-3.38z"/></svg>
                    <div class="mjtc-support-stat-content">
                        <div>
                            <p class="mjtc-support-stat-label">
                                <?php echo esc_attr(__('Total Tickets','majestic-support')); ?>
                            </p>
                            <h3 class="mjtc-support-stat-value" id="stat-total">
                                <?php
                                if(majesticsupport::$_config['count_on_myticket'] == 1){
                                    echo esc_html(majesticsupport::$_data['count']['allticket']);
                                }
                            ?>
                            </h3>
                            <div class="mjtc-support-stat-badge">
                                <svg viewBox="0 0 24 24" width="10" height="10"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zM6.5 9L10 5.5 13.5 9H11v4H9V9H6.5zm11 6L14 18.5 10.5 15H13v-4h2v4h2.5z"/></svg>
                                <?php echo esc_attr(__('Total Volume','majestic-support')); ?>
                            </div>
                        </div>
                        <div class="stat-icon-wrapper">
                             <svg viewBox="0 0 24 24" width="20" height="20"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/></svg>
                        </div>
                    </div>
                </a>
                <!-- Open Card -->
                <a href="#" class="stat-card-enhanced variant-blue mjtc-support-link <?php echo esc_attr($MJTC_open); ?>" data-tab-number="1" title="<?php echo esc_attr(__('Open Tickets','majestic-support')); ?>" data-filter="open">
                     <svg viewBox="0 0 24 24" class="stat-bg-icon"><path d="M20 6h-8l-2-2H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm0 12H4V8h16v10z"/></svg>
                    <div class="mjtc-support-stat-content">
                        <div>
                            <p class="mjtc-support-stat-label">
                                <?php echo esc_attr(__('Open Tickets','majestic-support')); ?>
                            </p>
                            <h3 class="mjtc-support-stat-value" id="stat-open">
                                <?php
                                if(majesticsupport::$_config['count_on_myticket'] == 1){
                                    echo esc_html(majesticsupport::$_data['count']['openticket']);
                                } ?>
                            </h3>
                            <div class="mjtc-support-stat-badge">
                                <svg viewBox="0 0 24 24" width="10" height="10"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                                <?php echo esc_attr(__('Action Required','majestic-support')); ?>
                            </div>
                        </div>
                        <div class="stat-icon-wrapper">
                             <svg viewBox="0 0 24 24" width="22" height="22" class="outline-icon"><path d="M20 6h-8l-2-2H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm0 12H4V8h16v10z"/></svg>
                        </div>
                    </div>
                </a>
                <?php if(in_array('overdue', majesticsupport::$_active_addons)){ ?>
                    <a href="#" class="stat-card-enhanced variant-rose mjtc-support-link <?php echo esc_attr($MJTC_overdue); ?>" data-tab-number="3" title="<?php echo esc_attr(__('Overdue Tickets','majestic-support')); ?>" data-filter="overdue">
                        <svg viewBox="0 0 24 24" class="stat-bg-icon"><path d="M12 5.99L19.53 19H4.47L12 5.99M12 2L1 21h22L12 2zm1 14h-2v2h2v-2zm0-6h-2v4h2v-4z"/></svg>
                        <div class="mjtc-support-stat-content">
                            <div>
                                <p class="mjtc-support-stat-label">
                                    <?php echo esc_html(__('Overdue','majestic-support')); ?>
                                </p>
                                <h3 class="mjtc-support-stat-value" id="stat-overdue">
                                    <?php
                                    if(majesticsupport::$_config['count_on_myticket'] == 1){
                                        echo esc_html(majesticsupport::$_data['count']['overdueticket']);
                                    }
                                ?>
                                </h3>
                                <div class="mjtc-support-stat-badge">
                                    <svg viewBox="0 0 24 24" width="10" height="10"><path d="M19.48 12.35c-1.57-4.08-7.16-4.3-5.81-10.23-2.76 1.51-4.75 4.39-4.94 7.64-.19 3.23 2.15 5.92 5.09 6.22 1.34.13 2.67-.34 3.73-1.15.54-.42 1.31.06 1.19.74-.29 1.63-1.46 2.94-2.86 3.69-1.56.84-3.32.96-4.92.35-1.59-.61-2.9-2.06-3.41-3.8-.46-1.54-.26-3.13.23-4.57.17-.5-.45-.96-.86-.67C5.17 11.83 4 13.8 4 16c0 4.42 3.58 8 8 8s8-3.58 8-8c0-1.3-.28-2.52-.76-3.62-.17-.38-.62-.42-.76-.03z"/></svg>
                                    <?php echo esc_html(__('Critical Priority','majestic-support')); ?>
                                </div>
                            </div>
                            <div class="stat-icon-wrapper">
                                 <svg viewBox="0 0 24 24" width="22" height="22"><path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/></svg>
                            </div>
                        </div>
                    </a>
                <?php } ?>
                <!-- Pending Card -->
                <a href="#" class="stat-card-enhanced variant-orange mjtc-support-link <?php echo esc_attr($MJTC_answered); ?>" data-tab-number="2" title="<?php echo esc_attr(__('Answered Tickets','majestic-support')); ?>" data-filter="pending">
                     <svg viewBox="0 0 24 24" class="stat-bg-icon"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/></svg>
                    <div class="mjtc-support-stat-content">
                        <div>
                            <p class="mjtc-support-stat-label">
                                <?php echo esc_attr(__('Answered','majestic-support')); ?>
                            </p>
                            <h3 class="mjtc-support-stat-value" id="stat-pending">
                                <?php
                                    if(majesticsupport::$_config['count_on_myticket'] == 1){
                                        echo esc_html(majesticsupport::$_data['count']['answeredticket']);
                                    }
                                ?>
                            </h3>
                            <div class="mjtc-support-stat-badge">
                                <svg viewBox="0 0 24 24" width="10" height="10"><path d="M6 2v6h.01L6 8.01 10 12l-4 4 .01.01H6V22h12v-5.99h-.01L18 16l-4-4 4-3.99-.01-.01H18V2H6zm10 14.5V20H8v-3.5l4-4 4 4zm-4-5l-4-4V4h8v3.5l-4 4z"/></svg> Awaiting Response
                            </div>
                        </div>
                        <div class="stat-icon-wrapper">
                            <svg viewBox="0 0 24 24" width="22" height="22" class="outline-icon"><path d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-2 12H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z"/></svg>
                        </div>
                    </div>
                </a>
                <!-- Closed Card -->
                <a href="#" class="stat-card-enhanced variant-emerald mjtc-support-link <?php echo esc_attr($MJTC_closed); ?>" data-tab-number="4" title="<?php echo esc_attr(__('closed ticket','majestic-support')); ?>" data-filter="closed">
                     <svg viewBox="0 0 24 24" class="stat-bg-icon"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-2 16l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z"/></svg>
                    <div class="mjtc-support-stat-content">
                        <div>
                            <p class="mjtc-support-stat-label">
                                <?php echo esc_attr(__('Resolved','majestic-support')); ?>
                            </p>
                            <h3 class="mjtc-support-stat-value" id="stat-closed">
                                <?php
                                    if(majesticsupport::$_config['count_on_myticket'] == 1){
                                        echo esc_html(majesticsupport::$_data['count']['closedticket']);
                                    }
                                ?>
                            </h3>
                            <div class="mjtc-support-stat-badge">
                                <svg viewBox="0 0 24 24" width="10" height="10"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                                <?php echo esc_attr(__('Case Closed','majestic-support')); ?>
                            </div>
                        </div>
                        <div class="stat-icon-wrapper">
                             <svg viewBox="0 0 24 24" width="22" height="22"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 13.17l7.59-7.59L19 7l-9 9z"/></svg>
                        </div>
                    </div>
                </a>
            </div>
            <?php
            $MJTC_uid = MJTC_request::MJTC_getVar('uid',null,0);
            if(is_numeric($MJTC_uid) && $MJTC_uid){
                $MJTC_formaction = wp_nonce_url(admin_url("admin.php?page=majesticsupport_ticket&mjslay=tickets&uid=".esc_attr($MJTC_uid)),"my-ticket");
            }else{
                $MJTC_formaction = wp_nonce_url(admin_url("admin.php?page=majesticsupport_ticket&mjslay=tickets"),"my-ticket");
            }
            ?>
            <form class="mjtc-filter-form mt0 mjtc-admin-ticket-filter mjtc-admin-ticket-filter-overall-wrapper search-panel-enhanced" 
                  name="majesticsupportform" id="majesticsupportform" method="post" action="<?php echo esc_url($MJTC_formaction); ?>">

                <div class="mjtc-support-search-toolbar">
                    <div class="mjtc-support-search-container group">
                        <div class="search-icon group-focus-within">
                            <svg viewBox="0 0 24 24" width="18" height="18"><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
                        </div>
                        <?php
                        // 1. SUBJECT (Order: 1)
                        if (!empty($MJTC_search_field_array['subject'])) {
                            echo wp_kses(MJTC_formfield::MJTC_text('subject', majesticsupport::$_data['filter']['subject'], array(
                                'placeholder' => majesticsupport::MJTC_getVarValue($MJTC_search_field_array['subject']),
                                'class' => 'mjtc-support-search-input mjtc-form-input-field',
                                'id' => 'searchInput'
                            )), MJTC_ALLOWED_TAGS);
                        }
                        ?>
                    </div>

                    <div class="mjtc-support-search-actions">
                         <div class="filter-btn-group">
                            <button type="button" id="resetBtn" class="mjtc-support-btn-icon mjtc-form-reset" title="<?php echo esc_attr(__('Reset', 'majestic-support')); ?>" onclick="resetFrom();">
                                <svg viewBox="0 0 24 24" width="14" height="14"><path d="M17.65 6.35C16.2 4.9 14.21 4 12 4c-4.42 0-7.99 3.58-7.99 8s3.57 8 7.99 8c3.73 0 6.84-2.55 7.73-6h-2.08c-.82 2.33-3.04 4-5.65 4-3.31 0-6-2.69-6-6s2.69-6 6-6c1.66 0 3.14.69 4.22 1.78L13 11h7V4l-2.35 2.35z"/></svg>
                            </button>
                        </div>
                        <div class="mjtc-support-separator"></div>
                        <div>
                            <?php echo wp_kses(MJTC_formfield::MJTC_submitbutton('go', esc_html(__('Search', 'majestic-support')), array('class' => 'mjtc-support-btn-search mjtc-form-search', 'id' => 'searchBtn')), MJTC_ALLOWED_TAGS); ?>
                        </div>
                    </div>
                </div>

                <div id="advancedFilters" class="mjtc-support-advanced-filters">
                    <div class="mjtc-support-filter-grid">
                        
                        <?php // 2. FULLNAME (Order: 2)
                        if (!empty($MJTC_search_field_array['fullname'])) : ?>
                        <div class="mjtc-support-filter-group">
                            <label><?php echo esc_html(__('Ticket Creator', 'majestic-support')).' '.esc_html(majesticsupport::MJTC_getVarValue($MJTC_search_field_array['fullname'])); ?></label>
                            <div class="mjtc-support-control-wrapper">
                                <div class="mjtc-support-filter-icon">
                                    <svg viewBox="0 0 24 24" width="12" height="12"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                                </div>
                                <?php echo wp_kses(MJTC_formfield::MJTC_text('name', majesticsupport::$_data['filter']['name'], array('placeholder' => majesticsupport::MJTC_getVarValue($MJTC_search_field_array['fullname']),'class' => 'mjtc-support-filter-select mjtc-form-input-field')), MJTC_ALLOWED_TAGS); ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php // 3. PHONE (Order: 3)
                        if (!empty($MJTC_search_field_array['phone'])) : ?>
                        <div class="mjtc-support-filter-group">
                            <label><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_search_field_array['phone'])); ?></label>
                            <div class="mjtc-support-control-wrapper">
                                <div class="mjtc-support-filter-icon">
                                    <svg viewBox="0 0 24 24" width="12" height="12"><path d="M6.62 10.79a15.15 15.15 0 0 0 6.59 6.59l2.2-2.2a1 1 0 0 1 1.17-.24c1.24.45 2.57.7 3.94.7a1 1 0 0 1 1 1V20a1 1 0 0 1-1 1A18 18 0 0 1 3 6a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1c0 1.37.25 2.7.7 3.94a1 1 0 0 1-.24 1.17l-2.34 2.34z"/></svg>
                                </div>
                                <?php echo wp_kses(MJTC_formfield::MJTC_text('phone', majesticsupport::$_data['filter']['phone'], array('placeholder' => majesticsupport::MJTC_getVarValue($MJTC_search_field_array['phone']),'class' => 'mjtc-support-filter-select mjtc-form-input-field')), MJTC_ALLOWED_TAGS); ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php // 4. EMAIL (Order: 4)
                        if (!empty($MJTC_search_field_array['email'])) : ?>
                        <div class="mjtc-support-filter-group">
                            <label><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_search_field_array['email'])); ?></label>
                            <div class="mjtc-support-control-wrapper">
                                <div class="mjtc-support-filter-icon">
                                    <svg viewBox="0 0 24 24" width="12" height="12"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
                                </div>
                                <?php echo wp_kses(MJTC_formfield::MJTC_text('email', majesticsupport::$_data['filter']['email'], array('placeholder' => majesticsupport::MJTC_getVarValue($MJTC_search_field_array['email']),'class' => 'mjtc-support-filter-select mjtc-form-input-field')), MJTC_ALLOWED_TAGS); ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php // 5. AGENT (Order: 5)
                        if (in_array('agent', majesticsupport::$_active_addons)) : ?>
                        <div class="mjtc-support-filter-group">
                             <label><?php echo esc_html(__('Select Agent','majestic-support')); ?></label>
                             <div class="mjtc-support-control-wrapper">
                                <div class="mjtc-support-filter-icon">
                                    <svg viewBox="0 0 24 24" width="12" height="12"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                                </div>
                                <?php echo wp_kses(MJTC_formfield::MJTC_select('staffid', MJTC_includer::MJTC_getModel('agent')->getStaffForCombobox(), majesticsupport::$_data['filter']['staffid'], esc_html(__('Select Agent','majestic-support')), array('class' => 'mjtc-support-filter-select mjtc-form-select-field')), MJTC_ALLOWED_TAGS); ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php // 6. PRODUCT (Order: 6)
                        if (!empty($MJTC_search_field_array['product'])) : ?>
                        <div class="mjtc-support-filter-group">
                            <label><?php echo esc_html(__('Select','majestic-support')).' '.esc_attr(majesticsupport::MJTC_getVarValue($MJTC_search_field_array['product'])); ?></label>
                            <div class="mjtc-support-control-wrapper">
                                <div class="mjtc-support-filter-icon">
                                    <svg viewBox="0 0 24 24" width="12" height="12"><path d="M21 16.5v-9l-9-5.25-9 5.25v9l9 5.25 9-5.25zM12 4.25l6.75 3.937L12 12.125 5.25 8.187 12 4.25z"/></svg>
                                </div>
                                <?php echo wp_kses(MJTC_formfield::MJTC_select('productid', MJTC_includer::MJTC_getModel('product')->getProductForCombobox(), majesticsupport::$_data['filter']['productid'], esc_html(__('Select','majestic-support')), array('class' => 'mjtc-support-filter-select mjtc-form-select-field')), MJTC_ALLOWED_TAGS); ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php // 7. DEPARTMENT (Order: 7)
                        if (!empty($MJTC_search_field_array['department'])) : ?>
                        <div class="mjtc-support-filter-group">
                            <label><?php echo esc_html(__('Select','majestic-support')).' '.esc_attr(majesticsupport::MJTC_getVarValue($MJTC_search_field_array['department'])); ?></label>
                            <div class="mjtc-support-control-wrapper">
                                <div class="mjtc-support-filter-icon">
                                    <svg viewBox="0 0 24 24" width="12" height="12"><path d="M12 7V3H2v18h20V7H12zM6 19H4v-2h2v2zm0-4H4v-2h2v2zm0-4H4V9h2v2zm0-4H4V5h2v2zm4 12H8v-2h2v2zm0-4H8v-2h2v2zm0-4H8V9h2v2zm0-4H8V5h2v2zm10 12h-8v-2h2v-2h-2v-2h2v-2h-2V9h8v10zm-2-8h-2v2h2v-2zm0 4h-2v2h2v-2z"/></svg>
                                </div>
                                <?php echo wp_kses(MJTC_formfield::MJTC_select('departmentid', MJTC_includer::MJTC_getModel('department')->getDepartmentForCombobox(), majesticsupport::$_data['filter']['departmentid'], esc_html(__('Select','majestic-support')), array('class' => 'mjtc-support-filter-select mjtc-form-select-field')), MJTC_ALLOWED_TAGS); ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php // 8. HELP TOPIC (Order: 8)
                        if (!empty($MJTC_search_field_array['helptopic']) && in_array('helptopic', majesticsupport::$_active_addons)) : ?>
                        <div class="mjtc-support-filter-group">
                            <label><?php echo esc_html(__('Select','majestic-support')).' '.esc_attr(majesticsupport::MJTC_getVarValue($MJTC_search_field_array['helptopic'])); ?></label>
                            <div class="mjtc-support-control-wrapper">
                                <div class="mjtc-support-filter-icon">
                                    <svg viewBox="0 0 24 24" width="12" height="12"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                                </div>
                                <?php echo wp_kses(MJTC_formfield::MJTC_select('helptopicid', MJTC_includer::MJTC_getModel('helptopic')->getHelpTopicsForCombobox(), majesticsupport::$_data['filter']['helptopicid'], esc_html(__('Select','majestic-support')), array('class' => 'mjtc-support-filter-select mjtc-form-select-field')), MJTC_ALLOWED_TAGS); ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php // 9. PRIORITY (Order: 9)
                        if (!empty($MJTC_search_field_array['priority'])) : ?>
                        <div class="mjtc-support-filter-group">
                            <label><?php echo esc_html(__('Select','majestic-support')) .' '.esc_attr(majesticsupport::MJTC_getVarValue($MJTC_search_field_array['priority'])); ?></label>
                            <div class="mjtc-support-control-wrapper">
                                <div class="mjtc-support-filter-icon">
                                    <svg viewBox="0 0 24 24" width="12" height="12"><path d="M14.4 6L14 4H5v17h2v-7h5.6l.4 2h7V6z"/></svg>
                                </div>
                                <?php echo wp_kses(MJTC_formfield::MJTC_select('priority', MJTC_includer::MJTC_getModel('priority')->getPriorityForCombobox(), majesticsupport::$_data['filter']['priority'], esc_html(__('Select','majestic-support')), array('class' => 'mjtc-support-filter-select mjtc-form-select-field')), MJTC_ALLOWED_TAGS); ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php // 10. DATES (Order: 10) ?>
                        <div class="mjtc-support-filter-group">
                            <label><?php echo esc_html(__('From Date', 'majestic-support')); ?></label>
                            <div class="mjtc-support-control-wrapper">
                                <div class="mjtc-support-filter-icon">
                                    <svg viewBox="0 0 24 24" width="12" height="12"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20a2 2 0 0 0 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10z"/></svg>
                                </div>
                                <?php echo wp_kses(MJTC_formfield::MJTC_text('datestart', majesticsupport::$_data['filter']['datestart'], array('placeholder' => esc_html(__('From Date', 'majestic-support')), 'class' => 'date mjtc-form-date-field mjtc-support-filter-date')), MJTC_ALLOWED_TAGS); ?>
                            </div>
                        </div>
                        <div class="mjtc-support-filter-group">
                            <label><?php echo esc_html(__('To Date', 'majestic-support')); ?></label>
                            <div class="mjtc-support-control-wrapper">
                                <div class="mjtc-support-filter-icon">
                                    <svg viewBox="0 0 24 24" width="12" height="12"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20a2 2 0 0 0 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10z"/></svg>
                                </div>
                                <?php echo wp_kses(MJTC_formfield::MJTC_text('dateend', majesticsupport::$_data['filter']['dateend'], array('placeholder' => esc_html(__('To Date', 'majestic-support')), 'class' => 'date mjtc-form-date-field mjtc-support-filter-date')), MJTC_ALLOWED_TAGS); ?>
                            </div>
                        </div>

                        <?php // 11. TICKET ID (Order: 11) ?>
                        <div class="mjtc-support-filter-group">
                            <label><?php echo esc_html(__('Ticket ID', 'majestic-support')); ?></label>
                            <div class="mjtc-support-control-wrapper">
                                <div class="mjtc-support-filter-icon">
                                    <svg viewBox="0 0 24 24" width="12" height="12"><path d="M4 9h16v2H4V9zm0 4h10v2H4v-2z"/></svg>
                                </div>
                                <?php echo wp_kses(MJTC_formfield::MJTC_text('ticketid', majesticsupport::$_data['filter']['ticketid'], array('placeholder' => esc_html(__('Ticket ID', 'majestic-support')),'class' => 'mjtc-support-filter-select mjtc-form-input-field')), MJTC_ALLOWED_TAGS); ?>
                            </div>
                        </div>

                        <?php // 12. WOO ORDER (Order: 12)
                        if(class_exists('WooCommerce') && in_array('woocommerce', majesticsupport::$_active_addons)) : ?>
                        <div class="mjtc-support-filter-group">
                            <label><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field_array['wcorderid'])); ?></label>
                            <div class="mjtc-support-control-wrapper">
                                <div class="mjtc-support-filter-icon">
                                    <svg viewBox="0 0 24 24" width="12" height="12"><path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49A1.003 1.003 0 0 0 20 4H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/></svg>
                                </div>
                                <?php echo wp_kses(MJTC_formfield::MJTC_text('orderid', majesticsupport::$_data['filter']['orderid'], array('placeholder' => majesticsupport::MJTC_getVarValue($MJTC_field_array['wcorderid']),'class' => 'mjtc-support-filter-select mjtc-form-input-field')), MJTC_ALLOWED_TAGS); ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php // 13. STATUS (Order: 13) ?>
                        <div class="mjtc-support-filter-group">
                            <label><?php echo esc_html(__('Select Status','majestic-support')); ?></label>
                            <div class="mjtc-support-control-wrapper">
                                <div class="mjtc-support-filter-icon">
                                    <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                                </div>
                                <?php echo wp_kses(MJTC_formfield::MJTC_select('status', MJTC_includer::MJTC_getModel('status')->getStatusForFilter(), majesticsupport::$_data['filter']['status'], esc_html(__('Select Status','majestic-support')), array('class' => 'mjtc-support-filter-select mjtc-form-select-field')), MJTC_ALLOWED_TAGS); ?>
                            </div>
                        </div>
                        <?php // Custom Fields
                            $MJTC_customfields = MJTC_includer::MJTC_getObjectClass('customfields')->adminFieldsForSearch(1);
                            foreach ($MJTC_customfields as $MJTC_field) { ?>
                                <div class="mjtc-support-filter-group mjtc-support-filter-radio-checkbox-field-wrp">
                                    <label><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->fieldtitle)); ?></label>
                                    <div class="mjtc-support-control-wrapper">
                                        <?php
                                        MJTC_includer::MJTC_getObjectClass('customfields')->MJTC_formCustomFieldsForSearch($MJTC_field, $MJTC_k, 1); ?>
                                    </div>
                                </div>
                                <?php
                            }
                        ?>
                    </div>

                </div>

                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('MS_form_search', 'MS_SEARCH'), MJTC_ALLOWED_TAGS); ?>
                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('sortby', majesticsupport::$_data['filter']['sortby']), MJTC_ALLOWED_TAGS); ?>
                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('list', $MJTC_list), MJTC_ALLOWED_TAGS); ?>

            </form>
            <?php
            $MJTC_link = '?page=majesticsupport_ticket';
            if (majesticsupport::$_sortorder == 'ASC')
                $MJTC_img = "sorting-white-1.png";
            else
                $MJTC_img = "sorting-white-2.png";
            ?>
            <div class="mjtc-support-list-header mjtc-admin-heading">
                <div class="mjtc-support-list-title mjtc-admin-head-txt">
                    <span id="currentViewLabel">
                        <?php 
                        $MJTC_list = majesticsupport::$_data['list'];
                        if($MJTC_list == 1){
                            echo esc_html(__('Open','majestic-support')).' ';
                        } elseif ($MJTC_list == 2){
                            echo esc_html(__('Answered','majestic-support')).' ';
                        } elseif ($MJTC_list == 3){
                            echo esc_html(__('Overdue','majestic-support')).' ';
                        } elseif ($MJTC_list == 5){
                            echo esc_html(__('All','majestic-support')).' ';
                        } elseif ($MJTC_list == 4){
                            echo esc_html(__('Closed','majestic-support')).' ';
                        }?>
                        <?php echo esc_html(__('Tickets', 'majestic-support')); ?>
                    </span>
                </div>
                <div class="mjtc-support-list-actions">
                    <div class="mjtc-support-select-wrapper">
                        <input type="checkbox" id="selectAllCheckbox" class="custom-checkbox" title="Select All Tickets">
                        <label for="selectAllCheckbox" class="mjtc-support-select-label"><?php echo esc_html(__('Select All', 'majestic-support')); ?></label>
                    </div>
                    <div class="mjtc-support-sort-wrapper mjtc-admin-sorting">
                        <select id="sortFilter" class="mjtc-support-sort-select mjtc-admin-sort-select">
                            <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field_array['subject'])); ?>
                            <option value="<?php echo esc_attr(majesticsupport::$_sortlinks['subject']); ?>" <?php if (majesticsupport::$_sorton == 'subject') echo esc_attr('selected') ?>><?php echo esc_html(__("Subject",'majestic-support')); ?></option>
                            <?php
                            if (!empty($MJTC_field_array['priority'])) { ?>
                                <option value="<?php echo esc_attr(majesticsupport::$_sortlinks['priority']); ?>"  <?php if (majesticsupport::$_sorton == 'priority') echo esc_attr('selected') ?>><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field_array['priority'])); ?></option>
                            <?php } ?>
                            <option value="<?php echo esc_attr(majesticsupport::$_sortlinks['ticketid']); ?>"  <?php if (majesticsupport::$_sorton == 'ticketid') echo esc_attr('selected') ?>><?php echo esc_html(__("Ticket ID",'majestic-support')); ?></option>
                            <option value="<?php echo esc_attr(majesticsupport::$_sortlinks['isanswered']); ?>"  <?php if (majesticsupport::$_sorton == 'isanswered') echo esc_attr('selected') ?>><?php echo esc_html(__("Answered",'majestic-support')); ?></option>
                            <option value="<?php echo esc_attr(majesticsupport::$_sortlinks['status']); ?>"  <?php if (majesticsupport::$_sorton == 'status') echo esc_attr('selected') ?>><?php echo esc_html(__("Status",'majestic-support')); ?></option>
                            <option value="<?php echo esc_attr(majesticsupport::$_sortlinks['created']); ?>"  <?php if (majesticsupport::$_sorton == 'created') echo esc_attr('selected') ?>><?php echo esc_html(__("Created",'majestic-support')); ?></option>
                        </select>
                        <a href="#" class="mjtc-support-sort-arrow mjtc-admin-sort-btn" title="<?php echo esc_attr(__('sort','majestic-support')); ?>">
                            <img alt="<?php echo esc_attr(__('sort','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL) . 'includes/images/' . esc_attr($MJTC_img) ?>">
                        </a>
                    </div>
                </div>
            </div>
            <?php
            if (!empty(majesticsupport::$_data[0])) {
                ?>
                <div id="ticketContainer" class="mjtc-support-ticket-list">
                    <?php
                    $MJTC_fields_array = array(); // Array for form fields
                    $MJTC_show_on_listing_arrays = array(); // Array for visible form fields
                    foreach (majesticsupport::$_data[0] AS $MJTC_ticket) { ?>
                        <div class="ticket-card-wrapper">
                            <div class="ticket-card  mjtc-support-wrapper"style="border-color: <?php echo esc_attr($MJTC_ticket->prioritycolour); ?>;">
                                <div class="mjtc-support-priority-lftbrder" style="background:<?php echo esc_attr($MJTC_ticket->prioritycolour); ?>;"></div>
                                <div class="mjtc-support-ticket-checkbox">
                                    <input type="checkbox" class="custom-checkbox ticket-checkbox" data-id="<?php echo esc_attr($MJTC_ticket->id); ?>">
                                </div>
                                <?php
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
                                
                                $MJTC_ticketviamail = '';
                                if ($MJTC_ticket->ticketviaemail == 1)
                                    $MJTC_ticketviamail = esc_html(__('Created via Email', 'majestic-support'));
                                ?>
                                <div class="mjtc-support-ticket-body mjtc-support-toparea">
                                    <div class="mjtc-support-ticket-main mjtc-support-data">
                                        <div class="mjtc-support-avatar-box mjtc-support-pic">
                                            <?php echo wp_kses(MJTC_get_avatar($MJTC_ticket->uid), MJTC_ALLOWED_TAGS); ?>
                                        </div>
                                        <div class="mjtc-support-ticket-details mjtc-support-left">
                                            <?php if (empty($MJTC_ticket->lastreply) || $MJTC_ticket->lastreply == '0000-00-00 00:00:00') { ?>
                                                <div class="mjtc-support-admin-listing-date">
                                                    <svg viewBox="0 0 24 24" width="10" height="10" class="fill-current"><path d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-2 12H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z"></path></svg>
                                                    <?php echo esc_html(__('Created', 'majestic-support')).':'; ?>
                                                    <?php echo esc_html(date_i18n(majesticsupport::$_config['date_format'], MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))); ?>
                                                </div>
                                            <?php } else { ?>
                                                <div class="mjtc-support-admin-listing-date">
                                                    <svg viewBox="0 0 24 24" width="10" height="10" class="fill-current"><path d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-2 12H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z"></path></svg>
                                                    <?php echo esc_html(__('Last Reply', 'majestic-support')).':'; ?>
                                                    <?php echo esc_html(date_i18n(majesticsupport::$_config['date_format'], MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->lastreply))); ?>
                                                </div>
                                            <?php } ?>
                                            <div class="mjtc-support-meta-row mjtc-support-data-row">
                                                <?php
                                                if (!empty($MJTC_show_on_listing_array['fullname'])) { ?>
                                                    <span class="mjtc-support-customer-name mjtc-support-user" style="cursor:pointer;" onClick="setFromNameFilter('<?php echo esc_js($MJTC_ticket->email); ?>');"><?php echo esc_html($MJTC_ticket->name); ?></span>
                                                    <?php 
                                                    if ($MJTC_ticket->status == 5 && majesticsupport::$_config['show_closedby_on_admin_tickets'] == 1) { ?>
                                                        <span class="mjtc-support-closedby-wrp">
                                                            <span class="mjtc-support-closedby">
                                                                <?php echo esc_html(MJTC_includer::MJTC_getModel('ticket')->getClosedBy($MJTC_ticket->closedby)); ?>
                                                            </span>
                                                            <?php 
                                                                if ($MJTC_ticket->closed != '0000-00-00 00:00:00') {?>
                                                                    <span class="mjtc-support-closed-date">
                                                                        <?php echo esc_html(__("Closed On", 'majestic-support')). " " . esc_html(date_i18n(majesticsupport::$_config['date_format'], MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->closed))); ?>
                                                                    </span>
                                                                    <?php 
                                                                } ?>
                                                        </span>
                                                        <div class="mjtc-support-meta-divider">•</div>
                                                        <?php
                                                    }
                                                } ?>
                                                <span class="mjtc-support-ticket-id">
                                                    <span class="mjtc-support-ticketiddot">•</span>
                                                    <?php echo esc_html($MJTC_ticket->ticketid); ?>
                                                </span>
                                                <span class="mjtc-support-status" style="background:<?php echo esc_attr($MJTC_ticket->statusbgcolour); ?>;color:<?php echo esc_attr($MJTC_ticket->statuscolour); ?>;">
                                                    <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_ticket->statustitle)); ?>
                                                </span>
                                                <?php
                                                if (!empty($MJTC_show_on_listing_array['priority'])) { ?>
                                                    <span class="priority-badge" style="background:<?php echo esc_attr($MJTC_ticket->prioritycolour); ?>;"><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_ticket->priority)); ?></span>
                                                    <?php
                                                } ?>
                                            </div>
                                                <!-- here here -->
                                            <div class="mjtc-support-ticket-subject mjtc-support-det-link">
                                                <a title="<?php echo esc_attr(__('Subject','majestic-support')); ?>" class="mjtc-support-det-link" href="?page=majesticsupport_ticket&mjslay=ticketdetail&majesticsupportid=<?php echo esc_attr($MJTC_ticket->id); ?>"><?php echo esc_html($MJTC_ticket->subject); ?></a>
                                            </div>
                                            <p class="mjtc-support-ticket-preview">
                                                <?php echo esc_html(wp_strip_all_tags($MJTC_ticket->message)); ?>
                                            </p>
                                            <div class="mjtc-support-footer-info mjtc-support-data-row">
                                                <?php
                                                foreach ($MJTC_show_on_listing_array AS $MJTC_field_field => $MJTC_field_title) {
                                                    switch ($MJTC_field_field) {
                                                        case 'department': 
                                                            if (!empty($MJTC_ticket->departmentname)) { ?>
                                                                <div class="mjtc-support-info-item mjtc-support-data-row-rec">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5 text-gray-400">
                                                                        <title><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field_array['department'])); ?></title>
                                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"></path>
                                                                    </svg>
                                                                    <span class="mjtc-support-value" style="cursor:pointer;" onClick="setDepartmentFilter('<?php echo esc_js($MJTC_ticket->departmentid); ?>');">
                                                                        <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_ticket->departmentname)); ?>
                                                                    </span>
                                                                </div>
                                                                <?php
                                                            }
                                                            break;
                                                        case 'email': 
                                                            if (!empty($MJTC_ticket->email)) { ?>
                                                                <div class="mjtc-support-info-item mjtc-support-data-row-rec">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="input-icon">
                                                                        <title><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field_array['email'])); ?></title>
                                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"></path>
                                                                    </svg>
                                                                    <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_ticket->email)); ?>
                                                                </div>
                                                                <?php
                                                            }
                                                            break;
                                                        case 'phone': 
                                                            if (!empty($MJTC_ticket->phone)) { ?>
                                                                <div class="mjtc-support-info-item mjtc-support-data-row-rec">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="input-icon">
                                                                        <title><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field_array['phone'])); ?></title>
                                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"></path>
                                                                    </svg>
                                                                    <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_ticket->phone)); ?>
                                                                </div>
                                                                <?php
                                                            }
                                                            break;
                                                        case 'product': 
                                                            if (!empty($MJTC_ticket->producttitle)) { ?>
                                                                <div class="mjtc-support-info-item mjtc-support-data-row-rec">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="input-icon">
                                                                        <title><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field_array['product'])); ?></title>
                                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9"></path>
                                                                    </svg>
                                                                    <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_ticket->producttitle)); ?>
                                                                </div>
                                                                <?php
                                                            }
                                                            break;
                                                        case 'helptopic': 
                                                            if (!empty($MJTC_ticket->topic) && in_array('helptopic', majesticsupport::$_active_addons)) { ?>
                                                                <div class="mjtc-support-info-item mjtc-support-data-row-rec">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="input-icon">
                                                                        <title><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field_array['helptopic'])); ?></title>
                                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z"></path>
                                                                    </svg>
                                                                    <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_ticket->topic)); ?>
                                                                </div>
                                                            <?php
                                                            }
                                                            break;
                                                        case 'eddorderid': 
                                                            if (!empty($MJTC_ticket->eddorderid)) { ?>
                                                                <div class="mjtc-support-info-item mjtc-support-data-row-rec">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                                        <title><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field_array['eddorderid'])); ?></title>
                                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5M12 6.75h1.5M15 6.75h1.5M9 10.5h1.5M12 10.5h1.5M15 10.5h1.5M9 14.25h1.5M12 14.25h1.5M15 14.25h1.5M9 18h1.5M12 18h1.5M15 18h1.5"></path>
                                                                    </svg>
                                                                    <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_ticket->eddorderid)); ?>
                                                                </div>
                                                                <?php
                                                            }
                                                            break;
                                                        case 'eddproductid': 
                                                            if (!empty($MJTC_ticket->eddproductid)) {
                                                                break;
                                                            }
                                                            if(!in_array('easydigitaldownloads', majesticsupport::$_active_addons)){
                                                                break;
                                                            }
                                                            if(!class_exists('Easy_Digital_Downloads')){
                                                                break;
                                                            } ?>
                                                            <div class="mjtc-support-info-item mjtc-support-data-row-rec">
                                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                                    <title><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field_array['eddproductid'])); ?></title>
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5l9-4.5 9 4.5M3 7.5l9 4.5m0 0l9-4.5M12 12v9M3 7.5v9l9 4.5 9-4.5v-9" />
                                                                </svg>
                                                                <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_ticket->eddproductid)); ?>
                                                            </div>
                                                            <?php
                                                            break;
                                                        default:
                                                            break;
                                                    }
                                                } ?>
                                                <div class="mjtc-support-custom-fields">
                                                    <?php
                                                    majesticsupport::$_data['custom']['ticketid'] = $MJTC_ticket->id;
                                                    $MJTC_customfields = MJTC_includer::MJTC_getObjectClass('customfields')->MJTC_userFieldsData(1, 1);
                                                    foreach ($MJTC_customfields as $MJTC_field) {
                                                        $MJTC_ret = MJTC_includer::MJTC_getObjectClass('customfields')->MJTC_showCustomFields($MJTC_field,1, $MJTC_ticket->params);
                                                        if (!empty($MJTC_ret['value'])) { ?>
                                                            <div class="cf-pill">
                                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                                    <path fill-rule="evenodd" d="M5.5 3A2.5 2.5 0 003 5.5v2.879a2.5 2.5 0 00.732 1.767l6.5 6.5a2.5 2.5 0 003.536 0l2.878-2.878a2.5 2.5 0 000-3.536l-6.5-6.5A2.5 2.5 0 008.38 3H5.5zM6 7a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                                                                </svg>
                                                                <?php echo wp_kses($MJTC_ret['value'], MJTC_ALLOWED_TAGS); ?>
                                                            </div>
                                                            <?php
                                                        }
                                                    }
                                                    ?>
                                                    <span class="mjtc-support-value mjtc-support-creade-via-email-spn"><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_ticketviamail)); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mjtc-support-ticket-actions mjtc-support-right">
                                        <div class="mjtc-support-action-icons">
                                            <a class="mjtc-support-icon-item" href="?page=majesticsupport_ticket&mjslay=ticketdetail&majesticsupportid=<?php echo esc_attr($MJTC_ticket->id); ?>">
                                                <svg viewBox="0 0 24 24" width="14" height="14" class="fill-current opacity-70"><path d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-2 12H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z"></path></svg>
                                                <span class="mjtc-support-replied-messages-count"><?php echo esc_html($MJTC_ticket->reply_count); ?></span>
                                            </a>
                                            <?php
                                            $MJTC_counter = 'one';
                                            if ($MJTC_ticket->lock == 1 && in_array('actions', majesticsupport::$_active_addons)) { ?>
                                                <div class="mjtc-support-icon-badge badge-rose" title="<?php echo esc_attr(__('The ticket is locked', 'majestic-support')); ?>">
                                                    <svg viewBox="0 0 24 24" width="12" height="12"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"></path></svg>
                                                </div>
                                            <?php } ?>
                                            <?php if ($MJTC_ticket->isoverdue == 1 && in_array('overdue', majesticsupport::$_active_addons)) { ?>
                                                <div class="mjtc-support-icon-badge badge-rose" title="<?php echo esc_attr(__('This ticket is marked as overdue', 'majestic-support')); ?>">
                                                    <svg viewBox="0 0 24 24" width="12" height="12"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"></path></svg>
                                                </div>
                                            <?php } ?>
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
                                            <div class="mjtc-support-data-val mjtc-support-assignee-pill">
                                                <span class="mjtc-support-staff-logo-wrp">
                                                    <?php echo wp_kses(MJTC_get_avatar($MJTC_ticket->closedby), MJTC_ALLOWED_TAGS);  ?>
                                                </span>
                                                <?php echo esc_html(MJTC_includer::MJTC_getModel('ticket')->getClosedBy($MJTC_ticket->closedby)); ?>
                                            </div>    
                                            <?php
                                        } else if (in_array('agent',majesticsupport::$_active_addons) && majesticsupport::$_config['show_assignto_on_admin_tickets'] == 1 && isset($MJTC_field_array['assignto'])) { ?>
                                            <div class="mjtc-support-assigned-label">
                                                <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field_array['assignto'])); ?>
                                            </div>
                                            <div class="mjtc-support-assignee-pill btn-stop-prop">
                                                <?php
                                                if($MJTC_ticket->staffuid > 0 ){ ?>
                                                    <?php echo wp_kses(MJTC_get_avatar($MJTC_ticket->staffuid, 'mjtc-support-assignee-avatar'), MJTC_ALLOWED_TAGS);  ?>
                                                    <div>
                                                        <span class="mjtc-support-assignee-name">
                                                            <?php echo esc_html($MJTC_ticket->staffname); ?>
                                                        </span>
                                                    </div>
                                                    <?php
                                                } else { ?>
                                                    <span class="mjtc-support-unassigned-avatar">
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            viewBox="0 0 640 512"
                                                            width="20"
                                                            height="20"
                                                            fill="currentColor"
                                                            aria-hidden="true"
                                                            focusable="false"
                                                            style="color:#8c8f94;">
                                                            <path d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm96 48h-8.7c-22.2 10.2-46.8 16-71.3 16s-49.1-5.8-71.3-16H160C71.6 304 0 375.6 0 464v16c0 17.7 14.3 32 32 32h352c17.7 0 32-14.3 32-32v-16c0-88.4-71.6-160-160-160zm288-112h-48v-48c0-17.7-14.3-32-32-32s-32 14.3-32 32v48h-48c-17.7 0-32 14.3-32 32s14.3 32 32 32h48v48c0 17.7 14.3 32 32 32s32-14.3 32-32v-48h48c17.7 0 32-14.3 32-32s-14.3-32-32-32z"/>
                                                        </svg>
                                                    </span>
                                                    <span class="mjtc-support-unassigned-text">
                                                        <?php echo esc_html(__("Unassigned", 'majestic-support'))?>
                                                    </span>
                                                    <?php
                                                } ?>
                                            </div>
                                            <?php
                                        } ?>
                                    </div>
                                </div>
                                <div class="mjtc-support-quick-actions mjtc-support-bottom-data-part">
                                    <a class="mjtc-support-btn-sm mjtc-support-datapart-action-btn button btn-action-default btn-stop-prop" title="<?php echo esc_attr(__('Edit Ticket', 'majestic-support')); ?>" href="?page=majesticsupport_ticket&mjslay=addticket&majesticsupportid=<?php echo esc_attr($MJTC_ticket->id); ?>">
                                        <?php echo esc_html(__('Edit', 'majestic-support')); ?>
                                    </a>
                                    <?php
                                    if ($MJTC_ticket->status != 6) {
                                        if ($MJTC_ticket->status != 5) {
                                            if (in_array('ticketclosereason',majesticsupport::$_active_addons)) {
                                                $MJTC_js = 'showTicketCloseReasons('.$MJTC_ticket->id.')';
                                            } else {
                                                $MJTC_js = 'actionticket(2,"'.$MJTC_ticket->id.'","'.$MJTC_ticket->internalid.'");';
                                            } ?>
                                            <a onclick="<?php echo esc_js($MJTC_js);?>" class="mjtc-support-btn-sm mjtc-support-datapart-action-btn button btn-action-success btn-stop-prop" title="<?php echo esc_attr(__('Close Ticket', 'majestic-support')); ?>">
                                                <?php echo esc_html(__('Close', 'majestic-support')); ?>
                                            </a>
                                            <?php
                                        } else { ?>
                                            <a class="mjtc-support-btn-sm mjtc-support-datapart-action-btn button btn-action-success btn-stop-prop" title="<?php echo esc_attr(__('Reopen Ticket', 'majestic-support')); ?>" onclick="actionticket(3,'<?php echo esc_js($MJTC_ticket->id); ?>','<?php echo esc_js($MJTC_ticket->internalid); ?>');">
                                                <?php echo esc_html(__('Reopen', 'majestic-support')); ?>
                                            </a>
                                            <?php
                                        }
                                    } ?>
                                    <?php if (in_array('agent', majesticsupport::$_active_addons)) { ?>
                                        <a data-ticket-id="<?php echo esc_attr($MJTC_ticket->id); ?>" data-staff-id="<?php echo esc_attr($MJTC_ticket->staffid); ?>" class="asgn-staff mjtc-support-btn-sm mjtc-support-datapart-action-btn button btn-action-default btn-stop-prop" title="<?php echo esc_attr(__('Assign Ticket', 'majestic-support')); ?>" href="<?php echo esc_url(wp_nonce_url('?page=majesticsupport_ticket&task=deleteticket&action=mstask&internalid='.esc_attr($MJTC_ticket->internalid).'&ticketid='.esc_attr($MJTC_ticket->id),'delete-ticket-'.esc_attr($MJTC_ticket->id)));?>">
                                            <?php echo esc_html(__('Assign', 'majestic-support')); ?>
                                        </a>
                                    <?php } ?>
                                    <a class="mjtc-support-btn-sm mjtc-support-datapart-action-btn button btn-action-default btn-stop-prop" title="<?php echo esc_attr(__('Delete Ticket', 'majestic-support')); ?>"  onclick="return confirm('<?php echo esc_html(__('Are you sure you want to delete?', 'majestic-support')); ?>');" href="<?php echo esc_url(wp_nonce_url('?page=majesticsupport_ticket&task=deleteticket&action=mstask&internalid='.esc_attr($MJTC_ticket->internalid).'&ticketid='.esc_attr($MJTC_ticket->id),'delete-ticket-'.esc_attr($MJTC_ticket->id)));?>">
                                        <?php echo esc_html(__('Delete', 'majestic-support')); ?>
                                    </a>
                                    <a title="<?php echo esc_attr(__('Enforce Delete', 'majestic-support')); ?>" class="mjtc-support-btn-sm mjtc-support-datapart-action-btn button btn-action-delete btn-stop-prop"  onclick="return confirm('<?php echo esc_html(__('Are you sure you want to enforce delete?', 'majestic-support')); ?>');" href="<?php echo esc_url(wp_nonce_url('?page=majesticsupport_ticket&task=enforcedeleteticket&action=mstask&ticketid='.esc_attr($MJTC_ticket->id),'enforce-delete-ticket-'.esc_attr($MJTC_ticket->id)))?>">
                                        <?php echo esc_html(__('Enforce Delete', 'majestic-support')); ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php
                    } ?>
                    <?php
                    if (majesticsupport::$_data[1]) {
                        $MJTC_data = '<div class="tablenav"><div class="tablenav-pages">' . wp_kses_post(majesticsupport::$_data[1]) . '</div></div>';
                        echo wp_kses($MJTC_data, MJTC_ALLOWED_TAGS);
                    } ?>
                </div>
                <?php
            } else {
                MJTC_layout::MJTC_getNoRecordFound();
            }
            ?>
        </main>
    </div>

    <!-- FLOATING BULK ACTIONS BAR -->
    <div id="bulkActionsBar">
        <form method="post" action="<?php echo esc_url(admin_url("admin.php?page=majesticsupport_ticket&task=multiactionticket")); ?>" id="adminTicketMultiActionsform" enctype="multipart/form-data">
            <div class="mjtc-support-bulk-container">
                <div class="mjtc-support-bulk-info">
                    <span class="mjtc-support-bulk-badge" id="selectedCount">0</span>
                    <span class="mjtc-support-bulk-label"><?php echo esc_html(__('Tickets Selected', 'majestic-support')); ?></span>
                </div>
                
                <div class="mjtc-support-bulk-buttons no-scrollbar">
                    <button class="asgn-staff mjtc-support-multioperation01 mjtc-support-btn-bulk btn-bulk-default" message="<?php echo esc_attr(__('Please first make a selection from the list', 'majestic-support')); ?>" data-for="reopen">
                        <svg viewBox="0 0 24 24" width="16" height="16"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                        <?php echo esc_html(__('Assign To', 'majestic-support')); ?>
                    </button>
                    <?php
                    if (in_array('ticketclosereason',majesticsupport::$_active_addons)) { ?>
                        <a onclick="showTicketCloseReasons()" class="mjtc-support-btn-bulk btn-bulk-success" message="<?php echo esc_attr(__('Please first make a selection from the list', 'majestic-support')); ?>" data-for="close">
                            <svg viewBox="0 0 24 24" width="16" height="16"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                            <?php echo esc_html(__('Close Tickets', 'majestic-support')); ?>
                        </a>
                        <?php
                    } else { ?>
                        <a class="mjtc-support-multioperation mjtc-support-btn-bulk btn-bulk-success" message="<?php echo esc_attr(__('Please first make a selection from the list', 'majestic-support')); ?>" data-for="close">
                            <svg viewBox="0 0 24 24" width="16" height="16"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                            <?php echo esc_html(__('Close Tickets', 'majestic-support')); ?>
                        </a>
                        <?php
                    } ?>
                    <a class="mjtc-support-multioperation mjtc-support-btn-bulk btn-bulk-success" message="<?php echo esc_attr(__('Please first make a selection from the list', 'majestic-support')); ?>" data-for="reopen">
                        <svg viewBox="0 0 24 24" width="16" height="16"><path d="M12 5V1L7 6l5 5V7c3.31 0 6 2.69 6 6 0 1.01-.25 1.97-.7 2.8l1.46 1.46C19.54 15.97 20 14.53 20 13c0-4.42-3.58-8-8-8zM6.24 6.74L4.78 5.28C3.46 6.79 2.5 8.8 2.5 11c0 4.42 3.58 8 8 8v4l5-5-5-5v4c-3.31 0-6-2.69-6-6 0-1.53.46-2.97 1.74-4.26z"/></svg>
                        <?php echo esc_html(__('Reopen Tickets', 'majestic-support')); ?>
                    </a>
                    <div style="height: 1.5rem; width: 1px; background: var(--mjtc-support-slate-300); margin: 0 0.25rem;"></div>
                    <button class="mjtc-support-multioperation mjtc-support-btn-bulk btn-bulk-danger" message="<?php echo esc_attr(__('Please first make a selection from the list', 'majestic-support')); ?>" confirmmessage="<?php echo esc_attr(__('Are you sure you want to delete?', 'majestic-support')) . ' ?'; ?>" data-for="delete">
                        <svg viewBox="0 0 24 24" width="16" height="16"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
                        <?php echo esc_html(__('Delete', 'majestic-support')); ?>
                    </button>
                    <button class="mjtc-support-multioperation mjtc-support-btn-bulk btn-bulk-critical" message="<?php echo esc_attr(__('Please first make a selection from the list', 'majestic-support')); ?>" confirmmessage="<?php echo esc_attr(__('Are you sure you want to delete?', 'majestic-support')) . ' ?'; ?>" data-for="enforce-delete">
                        <svg viewBox="0 0 24 24" width="16" height="16"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                        <?php echo esc_html(__('Enforce Delete', 'majestic-support')); ?>
                    </button>
                </div>
            </div>
            <?php
            echo wp_kses(MJTC_formfield::MJTC_hidden('uid', MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid()), MJTC_ALLOWED_TAGS);
            echo wp_kses(MJTC_formfield::MJTC_hidden('actionTask', ''), MJTC_ALLOWED_TAGS);
            echo wp_kses(MJTC_formfield::MJTC_hidden('selectedTicketIds', ''), MJTC_ALLOWED_TAGS);
            echo wp_kses(MJTC_formfield::MJTC_hidden('_wpnonce', wp_create_nonce('multiaction-ticket')), MJTC_ALLOWED_TAGS);
            echo wp_kses(MJTC_formfield::MJTC_hidden('action', 'ticket_multiactionticket'),MJTC_ALLOWED_TAGS);
            echo wp_kses(MJTC_formfield::MJTC_hidden('form_request', 'majesticsupport'), MJTC_ALLOWED_TAGS);
            ?>
        </form>
    </div>
    <form method="post" action="<?php echo esc_url(admin_url("admin.php?page=majesticsupport_ticket&task=actionticket")); ?>" id="adminTicketform" enctype="multipart/form-data" style="display: none;">
        <?php
            $MJTC_nonce = wp_create_nonce('action-ticket-');
            echo wp_kses(MJTC_formfield::MJTC_hidden('ticketlisting', '1'), MJTC_ALLOWED_TAGS);
            echo wp_kses(MJTC_formfield::MJTC_hidden('actionid', ''), MJTC_ALLOWED_TAGS);
            echo wp_kses(MJTC_formfield::MJTC_hidden('priority', ''), MJTC_ALLOWED_TAGS);
            echo wp_kses(MJTC_formfield::MJTC_hidden('ticketid', ''), MJTC_ALLOWED_TAGS);
            echo wp_kses(MJTC_formfield::MJTC_hidden('internalid', ''), MJTC_ALLOWED_TAGS);
            echo wp_kses(MJTC_formfield::MJTC_hidden('selectedTicketIds', ''), MJTC_ALLOWED_TAGS);
            echo wp_kses(MJTC_formfield::MJTC_hidden('_wpnonce', $MJTC_nonce), MJTC_ALLOWED_TAGS);
            echo wp_kses(MJTC_formfield::MJTC_hidden('uid', MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid()), MJTC_ALLOWED_TAGS);
            echo wp_kses(MJTC_formfield::MJTC_hidden('action', 'reply_savereply'),MJTC_ALLOWED_TAGS);
            echo wp_kses(MJTC_formfield::MJTC_hidden('form_request', 'majesticsupport'), MJTC_ALLOWED_TAGS);
        ?>
    </form>
    <?php
    $majesticsupport_js = "
        document.addEventListener('DOMContentLoaded', function() {
            // --- UI Interaction Logic (Sidebar, Submenus) ---
            let activeFilter = 'all';
            let selectedTickets = new Set();
            const ticketContainer = document.getElementById('ticketContainer');
            const bulkActionsBar = document.getElementById('bulkActionsBar');
            const selectAllCheckbox = document.getElementById('selectAllCheckbox');

            function updateBulkBar() {
                const count = selectedTickets.size;
                document.getElementById('selectedCount').innerText = count;
                if (count > 0) {
                    bulkActionsBar.classList.add('mjtc_ticket_barvisible');
                } else {
                    bulkActionsBar.classList.remove('mjtc_ticket_barvisible');
                    selectAllCheckbox.checked = false;
                }
            }
            // 
            // Rebind Events for new elements
            document.querySelectorAll('.ticket-checkbox').forEach(cb => {
                cb.addEventListener('change', function(e) {
                    const id = this.getAttribute('data-id');
                    const card = this.closest('.ticket-card');
                    if(this.checked) {
                        selectedTickets.add(id);
                        card.classList.add('selected');
                    } else {
                        selectedTickets.delete(id);
                        card.classList.remove('selected');
                    }
                    updateBulkBar();
                });
            });
            // Select All
            selectAllCheckbox.addEventListener('change', function() {
                if(this.checked) {
                    document.querySelectorAll('.ticket-checkbox').forEach(cb => {
                        cb.checked = true;
                        selectedTickets.add(cb.getAttribute('data-id'));
                        cb.closest('.ticket-card').classList.add('selected');
                    });
                } else {
                    document.querySelectorAll('.ticket-checkbox').forEach(cb => {
                        cb.checked = false;
                        cb.closest('.ticket-card').classList.remove('selected');
                    });
                    selectedTickets.clear();
                }
                updateBulkBar();
            });
        });
    ";
    wp_add_inline_script('majestic-support-cmain-js',$majesticsupport_js);
    ?>
</div>
