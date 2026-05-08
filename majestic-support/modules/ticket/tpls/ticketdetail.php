<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<div class="ms-main-up-wrapper">
    <?php
if (majesticsupport::$_config['offline'] == 2) {
    if(isset(majesticsupport::$_data['error_message'])){
        if(majesticsupport::$_data['error_message'] == 1){
            MJTC_layout::MJTC_getUserGuest();
        }elseif(majesticsupport::$_data['error_message'] == 2){
            MJTC_layout::MJTC_getYouAreNotAllowedToViewThisPage();
        }
    } elseif (MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid() != 0 || majesticsupport::$_config['visitor_can_create_ticket'] == 1) {
        $MJTC_printflag = false;
        if(isset(majesticsupport::$_data['print']) && majesticsupport::$_data['print'] == 1){
            $MJTC_printflag = true;
        }
        if($MJTC_printflag == true){
            wp_head();
        }

        if($MJTC_printflag == false){
            include_once(MJTC_PLUGIN_PATH . 'includes/header.php');
        }
        MJTC_message::MJTC_getMessage();

        if (majesticsupport::$_data['permission_granted'] == true) {
        if (!empty(majesticsupport::$_data[0])) {

        wp_enqueue_script('majesticsupport-file_validate.js', MJTC_PLUGIN_URL . 'includes/js/file_validate.js', array(), '1.0.0', true);
        wp_enqueue_script('jquery-ui-tabs');
        wp_enqueue_script('majesticsupport-jquery.cluetip.min.js', MJTC_PLUGIN_URL . 'includes/js/jquery.cluetip.min.js', array(), '1.0.0', true);
        wp_enqueue_script( 'hoverIntent' );
        wp_enqueue_style('majesticsupport-jquery.cluetip', MJTC_PLUGIN_URL . 'includes/css/jquery.cluetip.css', array(), '1.0.0');
        wp_enqueue_script('majesticsupport-timer.js', MJTC_PLUGIN_URL . 'includes/js/timer.jquery.js', array(), '1.0.0', true);
        wp_enqueue_style('majesticsupport-venobox-css', MJTC_PLUGIN_URL . 'includes/css/venobox.css', array(), '1.0.0');
        wp_enqueue_script('majesticsupport-venoboxjs',MJTC_PLUGIN_URL.'includes/js/venobox.js', array(), '1.0.0', true);
        if (in_array('aipoweredreply', majesticsupport::$_active_addons)){
            $MJTC_mod = 'aipoweredreply';
            $MJTC_jstreplymod = 'aipoweredreply';
        } else {
            $MJTC_mod = 'ticket';
            $MJTC_jstreplymod = 'reply';
        }
        $majesticsupport_js ="
            var timer_flag = 0;
            var seconds = 0;
            function getpremade(val) {
                jQuery.post(ajaxurl, {action: 'mjsupport_ajax', val: val,mjsmod: 'cannedresponses', task: 'getpremadeajax', '_wpnonce':'".esc_attr(wp_create_nonce("get-premade-ajax"))."'}, function(data) {
                    if (data) {
                        var append = jQuery('input#append_premade1:checked').length;
                        if (append == 1) {
                            var content = tinyMCE.get('mjsupport_message').getContent();
                            content = content + data;
                            tinyMCE.get('mjsupport_message').execCommand('mceSetContent', false, content);
                        } else {
                            tinyMCE.get('mjsupport_message').execCommand('mceSetContent', false, data);
                        }
                    }

                });
            }

            function changeTimerStatus(val) {
                if (timer_flag == 2) { // to handle stopped timer
                    return;
                }
                if (!jQuery('span.mjtc-support-timer-btn.cls_' + val).hasClass('selected')) {
                    jQuery('span.mjtc-support-timer-btn').removeClass('selected');
                    jQuery('span.mjtc-support-timer-btn.cls_' + val).addClass('selected');
                    if (val == 1) {
                        if (timer_flag == 0) {
                            jQuery('div.timer').timer({
                                format: '%H:%M:%S'
                            });
                        }
                        timer_flag = 1;
                        jQuery('div.timer').timer('resume');
                    } else if (val == 2) {
                        jQuery('div.timer').timer('pause');
                    } else {
                        jQuery('div.timer').timer('remove');
                        timer_flag = 2;
                    }
                }
            }

            jQuery(document).ready(function() {
                changeIconTabs();
                jQuery('.venobox').venobox({
                    infinigall: true,
                    framewidth: 850,
                    titleattr: 'data-title',
                });
            });


            jQuery(function() {
                jQuery('ul li a').click(function(e) {
                    var imgID = jQuery(this).find('img').attr('id');
                    changeIconTabs(imgID);
                });
            });

            function changeIconTabs(tabValue = '') {
                jQuery(document).ready(function() {
                    if (tabValue == '') {
                        tabValue = jQuery('#ul-nav .ui-tabs-active > a > img').attr('id');
                    }
                    if (tabValue == 'post-reply') {
                        jQuery('#internal-note').attr('src',
                            \"". esc_url(MJTC_PLUGIN_URL)."includes/images/ticketdetailicon/internal-reply-black.png\");
                        jQuery('#dept-transfer').attr('src',
                            \"". esc_url(MJTC_PLUGIN_URL)."includes/images/ticketdetailicon/department-transfer-black.png\"
                        );
                        jQuery('#assign-staff').attr('src',
                            \"". esc_url(MJTC_PLUGIN_URL)."includes/images/ticketdetailicon/assign-staff-black.png\");
                        jQuery('#' + tabValue).attr('src',
                            \"". esc_url(MJTC_PLUGIN_URL)."includes/images/ticketdetailicon/post-reply-white.png\");
                    } else if (tabValue == 'internal-note') {
                        jQuery('#dept-transfer').attr('src',
                            \"". esc_url(MJTC_PLUGIN_URL)."includes/images/ticketdetailicon/department-transfer-black.png\"
                        );
                        jQuery('#assign-staff').attr('src',
                            \"". esc_url(MJTC_PLUGIN_URL)."includes/images/ticketdetailicon/assign-staff-black.png\");
                        jQuery('#post-reply').attr('src',
                            \"". esc_url(MJTC_PLUGIN_URL)."includes/images/ticketdetailicon/post-reply-black.png\");
                        jQuery('#' + tabValue).attr('src',
                            \"". esc_url(MJTC_PLUGIN_URL)."includes/images/ticketdetailicon/internal-reply-white.png\");
                    } else if (tabValue == 'dept-transfer') {
                        jQuery('#internal-note').attr('src',
                            \"". esc_url(MJTC_PLUGIN_URL)."includes/images/ticketdetailicon/internal-reply-black.png\");
                        jQuery('#assign-staff').attr('src',
                            \"". esc_url(MJTC_PLUGIN_URL)."includes/images/ticketdetailicon/assign-staff-black.png\");
                        jQuery('#post-reply').attr('src',
                            \"". esc_url(MJTC_PLUGIN_URL)."includes/images/ticketdetailicon/post-reply-black.png\");
                        jQuery('#' + tabValue).attr('src',
                            \"". esc_url(MJTC_PLUGIN_URL)."includes/images/ticketdetailicon/department-transfer-white.png\"
                        );
                    } else if (tabValue == 'assign-staff') {
                        jQuery('#dept-transfer').attr('src',
                            \"". esc_url(MJTC_PLUGIN_URL)."includes/images/ticketdetailicon/department-transfer-black.png\"
                        );
                        jQuery('#internal-note').attr('src',
                            \"". esc_url(MJTC_PLUGIN_URL)."includes/images/ticketdetailicon/internal-reply-black.png\");
                        jQuery('#post-reply').attr('src',
                            \"". esc_url(MJTC_PLUGIN_URL)."includes/images/ticketdetailicon/post-reply-black.png\");
                        jQuery('#' + tabValue).attr('src',
                            \"". esc_url(MJTC_PLUGIN_URL)."includes/images/ticketdetailicon/assign-staff-white.png\");
                    }

                });
            }
            function changeIconTabsOnMouseover() {
                jQuery(document).ready(function() {
                    jQuery('ul li').hover(function(e) {
                        var imgID = jQuery(this).find('img').attr('id');
                        tabValue = imgID;
                        if (tabValue == '') {
                            tabValue = jQuery('#ul-nav .ui-tabs-active > a > img').attr('id');
                        }
                        if (tabValue == 'post-reply') {
                            jQuery('#' + tabValue).attr('src',
                                \"". esc_url(MJTC_PLUGIN_URL)."includes/images/ticketdetailicon/post-reply-white.png\"
                            );
                        } else if (tabValue == 'internal-note') {
                            jQuery('#' + tabValue).attr('src',
                                \"". esc_url(MJTC_PLUGIN_URL)."includes/images/ticketdetailicon/internal-reply-white.png\"
                            );
                        } else if (tabValue == 'dept-transfer') {
                            jQuery('#' + tabValue).attr('src',
                                \"". esc_url(MJTC_PLUGIN_URL)."includes/images/ticketdetailicon/department-transfer-white.png\"
                            );
                        } else if (tabValue == 'assign-staff') {
                            jQuery('#' + tabValue).attr('src',
                                \"". esc_url(MJTC_PLUGIN_URL)."includes/images/ticketdetailicon/assign-staff-white.png\"
                            );
                        }

                    });
                });
            }

            function changeIconTabsOnMouseOut() {
                jQuery(document).ready(function() {
                    jQuery('ul li').hover(function(e) {
                        var imgID = jQuery(this).find('img').attr('id');
                        tabValue = imgID;
                        if (tabValue == '') {
                            tabValue = jQuery('#ul-nav .ui-tabs-active > a > img').attr('id');
                        }
                        if (tabValue == 'post-reply' && !jQuery(this).hasClass('ui-tabs-active')) {
                            jQuery('#' + tabValue).attr('src',
                                \"". esc_url(MJTC_PLUGIN_URL)."includes/images/ticketdetailicon/post-reply-black.png\"
                            );
                        } else if (tabValue == 'internal-note' && !jQuery(this).hasClass('ui-tabs-active')) {
                            jQuery('#' + tabValue).attr('src',
                                \"". esc_url(MJTC_PLUGIN_URL)."includes/images/ticketdetailicon/internal-reply-black.png\"
                            );
                        } else if (tabValue == 'dept-transfer' && !jQuery(this).hasClass('ui-tabs-active')) {
                            jQuery('#' + tabValue).attr('src',
                                \"". esc_url(MJTC_PLUGIN_URL)."includes/images/ticketdetailicon/department-transfer-black.png\"
                            );
                        } else if (tabValue == 'assign-staff' && !jQuery(this).hasClass('ui-tabs-active')) {
                            jQuery('#' + tabValue).attr('src',
                                \"". esc_url(MJTC_PLUGIN_URL)."includes/images/ticketdetailicon/assign-staff-black.png\"
                            );
                        }

                    });
                });
            }

            function showEditTimerPopup() {
                jQuery('form#ms-time-edit-form').hide();
                jQuery('form#ms-reply-form').hide();
                jQuery('form#ms-note-edit-form').hide();
                jQuery('div.edit-time-popup').show();
                jQuery('span.mjtc-support-timer-btn').removeClass('selected');
                if (timer_flag != 0) {
                    jQuery('div.timer').timer('pause');
                }
                ex_val = jQuery('div.timer').html();
                jQuery('input#edited_time').val('');
                jQuery('input#edited_time').val(ex_val.trim());
                jQuery('div.ms-popup-background').show();
                jQuery('div#ms-popup-wrapper').slideDown('slow');
            }

            function updateTimerFromPopup() {
                val = jQuery('input#edited_time').val();
                arr = val.split(':', 3);
                jQuery('div.timer').html(val);
                jQuery('div.ms-popup-background').hide();
                jQuery('div.ms-popup-wrapper').slideUp('slow');
                seconds = parseInt(arr[0]) * 3600 + parseInt(arr[1]) * 60 + parseInt(arr[2]);
                if (seconds < 0) {
                    seconds = 0;
                }
                jQuery('div.timer').timer('remove');
                jQuery('div.timer').timer({
                    format: '%H:%M:%S',
                    seconds: seconds,
                });
                jQuery('div.timer').timer('pause');
                timer_flag = 1;
                desc = jQuery('textarea#t_desc').val();
                jQuery('input#timer_edit_desc').val(desc);
            }
            jQuery(document).ready(function($) {
                jQuery('form').submit(function(e) {
                    if (timer_flag != 0) {
                        jQuery('input#timer_time_in_seconds').val(jQuery('div.timer').data('seconds'));
                    }
                });
                jQuery('div#action-div a.button').click(function(e) {
                    e.preventDefault();
                });";
                if($MJTC_printflag != true){
                    $majesticsupport_js .=" jQuery('#tabs').tabs();";
                }
                $majesticsupport_js .="
                jQuery('#tk_attachment_add').click(function() {
                    var obj = this;
                    var att_flag = jQuery(this).attr('data-ident');
                    var parentElement = jQuery(this).closest('.mjtc-attachment-field');
                    jQuery(parentElement).addClass('mjtc-attachment-field-selected');
                    var current_files = jQuery('div.mjtc-attachment-field-selected').find('.tk_attachment_value_text').length;
                    var total_allow = ". esc_attr(majesticsupport::$_config['no_of_attachement']) .";
                    var append_text =
                        '<span class=\"tk_attachment_value_text\"><input name=\"filename[]\" type=\"file\" onchange=\"MJTC_uploadfile(this,\'". esc_js(majesticsupport::$_config['file_maximum_size'])."\',\'". esc_js(majesticsupport::$_config['file_extension'])."\');\" size=\"20\"  /><span  class=\"tk_attachment_remove\"></span></span>';
                    if (current_files < total_allow) {
                        jQuery('.tk_attachment_value_wrapperform.' + att_flag).append(append_text);
                    } else if ((current_files === total_allow) || (current_files > total_allow)) {
                        alert('". esc_html(__('File upload limit exceeds', 'majestic-support')) ."');
                    }
                });
                jQuery(document).delegate('.tk_attachment_remove', 'click', function(e) {
                    jQuery(this).parent().remove();
                    var current_files = jQuery('input[type=\"file\"]').length;
                    var total_allow = ". esc_attr(majesticsupport::$_config['no_of_attachement']) .";
                    if (current_files < total_allow) {
                        jQuery('#tk_attachment_add').show();
                    }
                });
                jQuery('a#showhidedetail').click(function(e) {
                    e.preventDefault();
                    var divid = jQuery(this).attr('data-divid');
                    jQuery('div#' + divid).slideToggle();
                    jQuery(this).find('img').toggleClass('mjtc-hidedetail');
                });

                jQuery('a#showhistory').click(function (e) {
                    e.preventDefault();
                    jQuery('div#userpopup').slideDown('slow');
                    jQuery('div#userpopupblack').show();
                });

                jQuery('div#userpopupblack,span.close-history,span.close-credentails').click(function (e) {
                    jQuery('div#userpopup').slideUp('slow');
                    jQuery('div#popupfordepartmenttransfer').slideUp('slow');
                    setTimeout(function () {
                        jQuery('div#userpopupblack').hide();
                    }, 700);
                });

                jQuery('a#departmenttransfer').click(function (e) {
                    e.preventDefault();
                    jQuery('div#popupfordepartmenttransfer').slideDown('slow');
                    jQuery('div#userpopupblack').show();
                });

                jQuery('a#agenttransfer').click(function (e) {
                    e.preventDefault();
                    jQuery('div#popupforagenttransfer').slideDown('slow');
                    jQuery('.ms-popup-background').show();
                });
                jQuery(document).delegate('div#popupforagenttransfer .popup-header-close-img', 'click', function (e) {
                    jQuery('div#popupforagenttransfer').slideUp('slow');
                    jQuery('div#popup-record-data').html('');
                });
                jQuery(document).delegate('div#popupfordepartmenttransfer .popup-header-close-img', 'click', function (e) {
                    jQuery('div#popupfordepartmenttransfer').slideUp('slow');
                    jQuery('div#popup-record-data').html('');
                });
                jQuery(document).delegate('div#popupforinternalnote .internalnote-popup-header-close-img', 'click', function (e) {
                    jQuery('div#popupforinternalnote').slideUp('slow');
                    jQuery('div#popup-record-data').html('');
                });
                jQuery('div.internalnote-popup-header-close-img,div.internalnote-popup-background').click(function (e) {
                    jQuery('div#popupforinternalnote').slideUp('slow');
                    setTimeout(function () {
                        jQuery('div.internalnote-popup-background').hide();
                    }, 700);
                });
                jQuery(document).delegate('#close-pop, .close-merge', 'click', function (e) {
                    jQuery('div#mergeticketselection').slideUp('slow');
                    jQuery('div#popup-record-data').html('');
                    setTimeout(function() {
                        jQuery('div.ms-popup-background').hide();
                    }, 700);
                });

                jQuery('div.popup-header-close-img,input#cancele,div.ms-popup-background,input#cancelee,input#canceleee,input#canceleeee,input#canceleeeee,input#canceleeeeee').click(function(e) {
                    jQuery('div.ms-popup-wrapper').slideUp('slow');
                    jQuery('div.ms-merge-popup-wrapper').slideUp('slow');
                    jQuery('div#mergeticketselection').slideUp('slow');
                    jQuery('div#popupfordepartmenttransfer').slideUp('slow');
                    jQuery('div#popupforagenttransfer').slideUp('slow');
                    setTimeout(function() {
                        jQuery('div.ms-popup-background').hide();
                        jQuery('div.ms-popup-wrapper').hide();
                        jQuery('div#userpopupblack').hide();
                    }, 700);
                });

                jQuery(document).delegate('#ticketpopupsearch', 'submit', function(e) {
                    var ticketid = jQuery('#ticketidformerge').val();
                    var nonce = jQuery('#nonce').val();
                    e.preventDefault();
                    var name = jQuery('input#name').val();
                    var email = jQuery('input#email').val();
                    jQuery.post(ajaxurl, {
                        action: 'mjsupport_ajax',
                        mjsmod: 'mergeticket',
                        task: 'getTicketsForMerging',
                        name: name,
                        email: email,
                        ticketid: ticketid,
                        '_wpnonce': nonce
                    }, function(data) {
                        data = jQuery.parseJSON(data);
                        if (data !== 'undefined') {
                            if (data !== '') {
                                jQuery('div#popup-record-data').html('');
                                jQuery('div#popup-record-data').html(MJTC_msDecodeHTML(data['data']));
                            } else {
                                jQuery('div#popup-record-data').html('');
                            }
                        } else {
                            jQuery('div#popup-record-data').html('');
                        }
                    }); //jquery closed
                });

                jQuery('a#print-link').click(function(e) {
                    e.preventDefault();
                    var href =
                        '". majesticsupport::makeUrl(array('mjsmod'=>'ticket','mjslay'=>'printticket','majesticsupportid'=>majesticsupport::$_data[0]->id)) ."';
                    print = window.open(href, 'print_win', 'width=1024, height=800, scrollbars=yes');
                });

                //non premium support function
                jQuery('#nonpreminumsupport').change(function() {
                    if (jQuery(this).is(':checked')) {
                        if (1 || confirm(
                                \"". esc_html(__('Are you sure to mark this ticket non-premium?','majestic-support')) ."\"
                            )) {
                            markUnmarkTicketNonPremium(1);
                        } else {
                            jQuery(this).removeAttr('checked');
                        }
                    } else {
                        markUnmarkTicketNonPremium(0);
                    }
                });

                jQuery('#paidsupportlinkticketbtn').click(function() {
                    var ticketid = jQuery('#ticketid').val();
                    var paidsupportitemid = jQuery('#paidsupportitemid').val();
                    if (paidsupportitemid > 0) {
                        jQuery.post(ajaxurl, {
                            action: 'mjsupport_ajax',
                            mjsmod: 'paidsupport',
                            task: 'linkTicketPaidSupportAjax',
                            ticketid: ticketid,
                            paidsupportitemid: paidsupportitemid,
                            '_wpnonce':'". esc_attr(wp_create_nonce('link-ticket-paidsupport-ajax')) ."'
                        }, function(data) {
                            window.location.reload();
                        });
                    }
                });

            });
            function editInternalNoteData(nonce, noteid) {
                jQuery.post(ajaxurl, {
                    action: 'mjsupport_ajax',
                    mjsmod: 'note',
                    task: 'getInternalNoteForEdit',
                    noteid: noteid,
                    '_wpnonce': nonce
                }, function(data) {
                    if (data) {
                        // 1. Fill the title input
                        jQuery('div#internal-note-popup-record-data #internalnotetitle').val(data.title);
                        jQuery('div#internal-note-popup-record-data #ticketid').val(data.ticketid);
                        jQuery('div#internal-note-popup-record-data #id').val(data.id);

                        // 2. Fill the TinyMCE Editor content
                        if (typeof tinymce !== 'undefined' && tinymce.get('internalnote')) {
                            tinymce.get('internalnote').setContent(data.note);
                        } else {
                            // Fallback for plain textarea if tinymce didn't load
                            jQuery('#internalnote').val(data.note);
                        }

                        jQuery('div#internal-note-popup-record-data #_wpnonce').val(data.wpnonce);
                        jQuery('div#internal-note-popup-record-data #internal_note_file_name').text(data.filename);

                        // 3. Show the popup
                        jQuery('div#popupforinternalnote').slideDown('slow');
                        jQuery('.internalnote-popup-background').show();
                    }
                });
            }

            function markUnmarkTicketNonPremium(mark) {
                var ticketid = jQuery('#ticketid').val();
                var paidsupportitemid = jQuery('#paidsupportitemid').val();
                jQuery.post(ajaxurl, {
                    action: 'mjsupport_ajax',
                    mjsmod: 'paidsupport',
                    task: 'markUnmarkTicketNonPremiumAjax',
                    status: mark,
                    ticketid: ticketid,
                    paidsupportitemid: paidsupportitemid,
                    '_wpnonce':'". esc_attr(wp_create_nonce('mark-unmark-ticket-nonpremium-ajax'))."'
                }, function (data) {
                    window.location.reload();
                });
            }

            function actionticket(action) {
                /*  Action meaning
                 * 1 -> Change Priority
                 * 2 -> Close Ticket
                 * 3 -> Reopen Ticket
                 */
                if (action == 1) {
                    jQuery('#priority').val(jQuery('#prioritytemp').val());
                }
                jQuery('input#actionid').val(action);
                jQuery('form#adminTicketform').submit();
            }

            function getmergeticketid(mergeticketid, mergewithticketid, mergeNonce) {
                if (mergewithticketid == 0) {
                    mergewithticketid = jQuery('#mergeticketid').val();
                } else {
                    jQuery('#mergeticketid').val(mergewithticketid);
                }
                if (mergeticketid == mergewithticketid) {
                    alert(\"Primary id must be differ from merge ticket id\");
                    return false;
                }
                jQuery('#mergeticketselection').hide();
                getTicketdataForMerging(mergeticketid, mergewithticketid,mergeNonce);
            }

            function getTicketdataForMerging(mergeticketid, mergewithticketid,mergeNonce) {
                jQuery.post(ajaxurl, {
                    action: 'mjsupport_ajax',
                    mjsmod: 'mergeticket',
                    task: 'getLatestReplyForMerging',
                    mergeid: mergeticketid,
                    mergewith: mergewithticketid,
                    '_wpnonce': mergeNonce
                }, function(data) {
                    if (data) {
                        data = jQuery.parseJSON(data);
                        jQuery('div#popup-record-data').html('');
                        jQuery('div#popup-record-data').html(MJTC_msDecodeHTML(data['data']));
                    }
                });
            }

            function closePopup() {
                setTimeout(function() {
                    jQuery('div.ms-popup-background').hide();
                    jQuery('div#userpopupblack').hide();
                }, 700);

                jQuery('div.ms-popup-wrapper').slideUp('slow');
                jQuery('div#popupfordepartmenttransfer').slideUp('slow');
                jQuery('div#userpopup').slideUp('slow');

            }

            function checktinymcebyid(id) {
                var content = tinymce.get(id).getContent({
                    format: 'text'
                });
                if (jQuery.trim(content) == '') {
                    alert(\"". esc_html(__('Some values are not acceptable please retry', 'majestic-support')) ."\");
                    return false;
                }
                return true;
            }

            function showTicketCloseReasons(id, internalid){
                jQuery('div.ms-popup-other-reason-box').hide();
                jQuery.post(ajaxurl, {action: 'mjsupport_ajax', mjsmod: 'ticketclosereason', task: 'getTicketCloseReasonsForPopup',isadmin:0,id:id, internalid:internalid, '_wpnonce':'". esc_attr(wp_create_nonce('get-ticket-close-reasons-for-popup'))."'}, function (data) {
                    if(data){
                        data=jQuery.parseJSON(data);
                        jQuery('div#popup-record-data').html('');
                        jQuery('div#popup-record-data').html(MJTC_msDecodeHTML(data['data']));
                    }
                });
            }

            jQuery(document).delegate('.mjtc-sprt-det-copy-id', 'click', function () {

                var temp = jQuery('<input>');
                jQuery('body').append(temp);
                temp.val(jQuery('#ticketrandomid').val()).select();
                document.execCommand('copy');
                temp.remove();

                jQuery(this).addClass('mjtc-copied-success').attr('title', \"". esc_html(__('Copied', 'majestic-support')) ."\");

                // Remove class after 5 seconds
                setTimeout(function () {
                    jQuery('.mjtc-sprt-det-copy-id').removeClass('mjtc-copied-success').attr('title', '');
                }, 5000);
            });

            function resetMergeFrom(nonce) {
                var ticketid = jQuery('#ticketidformerge').val();
                var name = '';
                var email = '';
                jQuery.post(ajaxurl, {action: 'mjsupport_ajax', mjsmod: 'mergeticket', task: 'getTicketsForMerging', name: name, email: email,ticketid:ticketid, '_wpnonce': nonce}, function (data) {
                    data=jQuery.parseJSON(data);
                   if(data !== 'undefined') {
                        if(data !== '') {
                            jQuery('div#popup-record-data').html('');
                            jQuery('div#popup-record-data').html(MJTC_msDecodeHTML(data['data']));
                        }else{
                            jQuery('div#popup-record-data').html('');
                        }
                    }else{
                        jQuery('div#popup-record-data').html('');
                    }
                });//jquery closed
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

            function hideBoxForOtherReason(){
                jQuery('div.ms-popup-other-reason-box-wrp').slideUp('slow', function () {
                    jQuery('div.ms-popup-other-reason-box').hide();
                });
            }

            function showBoxForOtherSingleReason(){
                jQuery('div.ms-popup-other-reason-box-wrp').slideDown('slow', function () {
                    jQuery('div.ms-popup-other-reason-box').show();
                });
            }

            function showBoxForOtherMultipleReason(){
                jQuery('div.ms-popup-other-reason-box-wrp').slideToggle('slow', function () {
                    jQuery('div.ms-popup-other-reason-box').show();
                });
            }

            function checkSmartReply(ticketSubject) {
                jQuery.post(ajaxurl, {action: 'mjsupport_ajax', ticketSubject: ticketSubject, mjsmod: 'smartreply', task: 'checkSmartReply', '_wpnonce':'". esc_attr(wp_create_nonce("check-smart-reply"))."'}, function (data) {
                    if(data) {
                        data=jQuery.parseJSON(data);
                        jQuery('.smartReplyNotFound').hide();
                        jQuery('.smartReplyFound').show();
                        jQuery('.ms-ticket-detail-smartreply-wrp').removeClass('add-margin');
                        jQuery('.ms-ticket-detail-smartreply-footer-wrp').show();
                        jQuery('.ms-ticket-detail-smartreply-add-wrp').html(MJTC_msDecodeHTML(data));
                    } else {
                        jQuery('.ms-ticket-detail-smartreply-wrp').removeClass('add-margin');
                        jQuery('.ms-ticket-detail-smartreply-footer-wrp').show();
                        jQuery('.smartReplyFound').hide();
                        jQuery('.smartReplyNotFound').show();
                    }
                });
            }

            function getSmartReply(val) {
                jQuery.post(ajaxurl, {action: 'mjsupport_ajax', val: val, mjsmod: 'smartreply', task: 'getSmartReply', '_wpnonce':'". esc_attr(wp_create_nonce("get-smart-reply"))."'}, function (data) {
                    if (data) {
                        var append = jQuery('input#append_smartreply1:checked').length;
                        if (append == 1) {
                            if(jQuery('#wp-mjsupport_message-wrap').hasClass('html-active')){
                                var content = jQuery('#mjsupport_message').val();
                                content = content + data;
                                jQuery('#mjsupport_message').val(content);
                            }else{
                                var content = tinyMCE.get('mjsupport_message').getContent();
                                content = content + data;
                                tinyMCE.get('mjsupport_message').execCommand('mceSetContent', true, content);
                            }


                        } else {
                            if(jQuery('#wp-mjsupport_message-wrap').hasClass('html-active')){
                                jQuery('#mjsupport_message').val(data);
                            }else{
                                tinyMCE.get('mjsupport_message').execCommand('mceSetContent', true, data);
                            }
                        }

                    }
                });
            }

            jQuery('.mjtc-support-stats-wrp a.mjtc-sprt-det-other-tkt-btn').click(function(e) {
                e.preventDefault();
                var list = jQuery(this).attr('data-tab-number');
                var oldUrl = jQuery(this).attr('href'); // Get current url
                var opt = '?';
                var found = oldUrl.search('&');
                if (found > 0) {
                    opt = '&';
                }
                var found = oldUrl.search('[\?\]');
                if (found > 0) {
                    opt = '&';
                }
                var newUrl = oldUrl + opt + 'list=' + list; // Create new url
                window.location.href = newUrl;
            });

        ";
        wp_add_inline_script('majestic-support-cmain-js',$majesticsupport_js);
        $MJTC_yesno = array(
            (object) array('id' => '1', 'text' => esc_html(__('Yes', 'majestic-support'))),
            (object) array('id' => '0', 'text' => esc_html(__('No', 'majestic-support')))
        );
        // AI-Powered Reply
        $majesticsupport_js ='
            // AI-Powered Reply
            // Temporary storage for the current tickets replies for filtering
            let currentTicketAllReplies = [];
            jQuery(document).ready(function(){
                // Get DOM elements with IDs using jQuery selectors
                const replyTextarea = jQuery("#mjtc-support-reply-textarea");
                const matchingTicketsSection = jQuery("#mjtc-support-matching-tickets-section");
                const matchingTicketsList = jQuery("#mjtc-support-matching-tickets-list");
                const selectedTicketRepliesSection = jQuery("#mjtc-support-selected-ticket-replies-section");
                const selectedTicketRepliesContent = jQuery("#mjtc-support-selected-ticket-replies-content");
                const messageModal = jQuery("#mjtc-support-message-modal");

                jQuery(".mjtc-support-info-icon-wrapper").hover(
                    function(e){
                        jQuery(this).addClass("tooltip-active");
                    },
                    function(e){
                        jQuery(this).removeClass("tooltip-active");
                    }
                );
                
                // Function to show custom modal
                function showModal(message) {
                    jQuery("#mjtc-support-modal-message").text(message);
                    messageModal.removeClass("mjtc-support-hidden");
                }

                // Function to hide custom modal
                jQuery("#mjtc-support-modal-close-btn").on("click", function(e) {
                    e.preventDefault();
                    messageModal.addClass("mjtc-support-hidden");
                    jsReplyHideLoading();
                });

                // Function to copy text to clipboard (works in iframes)
                function copyToClipboard(text) {
                    const tempTextArea = document.createElement("textarea");
                    tempTextArea.value = text;
                    document.body.appendChild(tempTextArea);
                    tempTextArea.select();
                    try {
                        const successful = document.execCommand("copy");
                        console.log(successful);
                        if(successful) {
                            showModal("'.__("Copied to clipboard", "majestic-support").'");    
                        } else {
                            showModal("'.__("Failed to copy", "majestic-support").'");
                        }
                    } catch (err) {
                        showModal("'.__("Failed to copy to clipboard. Please copy manually.", "majestic-support").'");
                    }
                    document.body.removeChild(tempTextArea);
                }

                // Function to append text to reply area
                function appendToReplyArea(textToAppend) {
                    if(jQuery("#wp-mjsupport_message-wrap").hasClass("html-active")){
                        let currentContent = jQuery("#mjsupport_message").val();
                        let newContent = currentContent + "\n" + textToAppend; // Append with a newline
                        jQuery("#mjsupport_message").val(content);
                    }else{
                        let currentContent = tinyMCE.get("mjsupport_message").getContent();
                        let newContent = currentContent + "\n" + textToAppend; // Append with a newline
                        tinyMCE.get("mjsupport_message").execCommand("mceSetContent", true, newContent);
                    }
                    showModal("'.__("Reply content appended!", "majestic-support").'");
                }

                // Function to filter and display replies based on dropdown selection
                function displayFilteredReplies(ticket, filterType) {
                    console.log(ticket);
                    console.log(filterType);

                    let filteredReplies = [];
                    if (filterType === "marked") {
                        filteredReplies = currentTicketAllReplies.filter(reply => reply.isMarked);
                    } else { // "all"
                        filteredReplies = currentTicketAllReplies;
                    }
                    displayTicketReplies(ticket, filteredReplies);
                }

                // Event listener for Replies Filter dropdown
                jQuery("#mjtc-support-replies-filter").on("change", function() {
                    const selectedFilter = jQuery(this).val();
                    const activeTicketItem = matchingTicketsList.find(".mjtc-ai-card.active");
                    
                    if (!activeTicketItem.length) {
                        showModal("'.__("No ticket selected!", "majestic-support").'");
                        return;
                    }
                    
                    const ticketId = activeTicketItem.data("ticket-id");
                    const type = activeTicketItem.data("type");
                    const ticketTitle = activeTicketItem.find(".mjtc-ai-card-title").text();
                    
                    // Show loading message
                    jsReplyShowLoading();
                    
                    // Fetch replies based on filter and ticket ID
                    console.log(selectedFilter);
                    jQuery.post(ajaxurl, {
                        action: "mjsupport_ajax",
                        mjsmod: "'.$MJTC_jstreplymod.'",
                        task: "getFilteredReplies",
                        ticket_id: ticketId,
                        filter: selectedFilter,
                        "_wpnonce": "'. esc_attr(wp_create_nonce("get-filtered-replies")).'"
                    }, function(data) {
                        jsReplyHideLoading();
                        
                        if (data.success) {
                            const ticket = {
                                id: ticketId,
                                text: ticketTitle
                            };
                            displayTicketReplies(type, ticket, data.data.replies);
                        } else {
                            showModal(data.message || "'.__("Error fetching replies.", "majestic-support").'");
                        }
                    }).fail(function() {
                        jsReplyHideLoading();
                        showModal("'.__("Failed to fetch replies. Please try again.", "majestic-support").'");
                    });
                });

                // Modify the ticket click handler to set active state and store ticket ID
                matchingTicketsList.on("click", ".mjtc-ai-card", function() {
                    // Remove active class from all items
                    matchingTicketsList.find(".mjtc-ai-card").removeClass("active");
                    
                    // Add active class to clicked item
                    const listItem = jQuery(this);
                    listItem.addClass("active");
                    
                    // const ticketId1 = activeTicketItem.data("ticket-id");
                    const ticketId = listItem.data("ticket-id");
                    const type = listItem.data("type");
                    const id = listItem.data("id");
                    const ticketTitle = listItem.find(".mjtc-ai-card-title").text();
                    
                    // Show loading message
                    jsReplyShowLoading();
                    
                    // Reset filter to "all" when selecting a new ticket
                    jQuery("#mjtc-support-replies-filter").val("all");
                    
                    if(type == "smart_reply") {
                        // Fetch all replies initially
                        jQuery.post(ajaxurl, {
                            action: "mjsupport_ajax",
                            mjsmod: "smartreply",
                            task: "getSmartReplyResponse",
                            reply_id: ticketId,
                            filter: "all",
                            "_wpnonce": "'. esc_attr(wp_create_nonce("get-smart-reply")).'"
                        }, function(data) {
                            jsReplyHideLoading();
                            
                            if (data.success) {
                                const ticket = {
                                    id: ticketId,
                                    text: ticketTitle
                                };
                                displayTicketReplies(type, ticket, data.data.replies);
                            } else {
                                showModal(data.message || "'.__("Error fetching replies.", "majestic-support").'");
                            }
                        }).fail(function() {
                            jsReplyHideLoading();
                            showModal("'.__("Failed to fetch replies. Please try again.", "majestic-support").'");
                        });
                    } else if (type == "ticket") {
                        // Fetch all replies initially
                        jQuery.post(ajaxurl, {
                            action: "mjsupport_ajax",
                            mjsmod: "'.$MJTC_jstreplymod.'",
                            task: "getFilteredReplies",
                            ticket_id: ticketId,
                            filter: "all",
                            "_wpnonce": "'. esc_attr(wp_create_nonce("get-filtered-replies")).'"
                        }, function(data) {
                            jsReplyHideLoading();
                            
                            if (data.success) {
                                const ticket = {
                                    id: ticketId,
                                    text: ticketTitle
                                };
                                displayTicketReplies(type, ticket, data.data.replies);
                            } else {
                                showModal(data.message || "'.__("Error fetching replies.", "majestic-support").'");
                            }
                        }).fail(function() {
                            jsReplyHideLoading();
                            showModal("'.__("Failed to fetch replies. Please try again.", "majestic-support").'");
                        });
                    }
                });

                jQuery(".mjtc-support-segmented-control-option").on("click", function(e) {
                    var actionType = jQuery(this).data("type");
                    var selectedValue = jQuery(this).data("value"); // Get the "data-value" attribute (default, enable, disable).
                    var selectedId = jQuery(this).data("id");
                    
                    // Remove the "active" class from all segmented control options.
                    // jQuery("#mjtc-support-ai-reply-status-control").find(".mjtc-support-segmented-control-option").removeClass("active");
                    jQuery(this).closest("#mjtc-support-ai-reply-status-control")
                   .find(".mjtc-support-segmented-control-option")
                   .removeClass("active");

                    // Add the "active" class to the currently clicked option.
                    jQuery(this).addClass("active");

                    // Update the value of the hidden input field.
                    jQuery("#mjtc-support-ai-reply-status-hidden").val(selectedValue);

                    // Perform the AJAX request using jQuery.ajax().
                    jQuery.post(ajaxurl, {action: "mjsupport_ajax", mjsmod: "reply", task: "markedAsAiPoweredReply", status:selectedValue, id: selectedId, type: actionType, "_wpnonce":"'.esc_attr(wp_create_nonce("ai-powered-reply")).'"}, function (data) {
                        if (data) {
                            jQuery(".majesticsupport-review-box-popup").remove();
                            jQuery(".majesticsupport-premio-review-box").remove();
                        }
                    });
                });

                // Event listener for AI-Powered Reply button
                jQuery("#mjtc-support-ai-reply-btn").on("click", function (e) {
                    e.preventDefault();
                    // Show loading message
                    jsReplyShowLoading();

                    const currentTitle = jQuery(".mjtc-support-current-ticket-title").text();
                    const currentTicketId = jQuery(".mjtc-support-current-ticket-id").text();
                    const tickets = fetchTicketsFromPHP(currentTicketId, currentTitle, "all");
                });

                // Event listener for Replies Filter dropdown
                jQuery("#mjtc-support-tickets-filter").on("change", function(e) {
                    e.preventDefault();
                    const selectedFilter = jQuery(this).val();
                    const currentTitle = jQuery(".mjtc-support-current-ticket-title").text();
                    const currentTicketId = jQuery(".mjtc-support-current-ticket-id").text();

                    const tickets = fetchTicketsFromPHP(currentTicketId, currentTitle, selectedFilter); 
                });

                function fetchTicketsFromPHP(ticketId, ticketSubject, selectedFilter) {
                    jQuery.post(ajaxurl, {action: "mjsupport_ajax", ticketSubject: ticketSubject, ticketId: ticketId, filter: selectedFilter, mjsmod: "'.$MJTC_mod.'", task: "checkAIReplyTicketsBySubject", "_wpnonce":"'. esc_attr(wp_create_nonce("check-smart-reply")).'"}, function (data) {
                        if(data) {
                            displayMatchingTickets(data);
                        } else {
                            showModal(`'.__('Error fetching matching tickets:', 'majestic-support').'`);
                            return [];
                            jQuery(".smartReplyTickets").hide();
                        }
                    });
                }

                // Function to display matching tickets
                function displayMatchingTickets(matchingTickets) {
                    // Parse if it is a string
                    if (typeof matchingTickets === "string") {
                        try {
                            matchingTickets = JSON.parse(matchingTickets);
                        } catch (e) {
                            console.error("Failed to parse matchingTickets:", e);
                            matchingTickets = [];
                        }
                    }
                    
                    matchingTicketsList.empty(); // Clear previous list
                    selectedTicketRepliesSection.addClass("mjtc-support-hidden"); // Hide replies section if open
                    jQuery("#mjtc-support-replies-filter").val("all"); // Reset filter when showing new tickets

                    jQuery(".mjtc-support-container").show();

                    if (matchingTickets.length === 0) {
                        matchingTicketsList.html(`<div class="mjtc-ai-card-snippet">'.__("No matching tickets found.", "majestic-support").'</div>`);
                        matchingTicketsSection.removeClass("mjtc-support-hidden");
                        jsReplyHideLoading();
                        matchingTicketsSection.removeClass("mjtc-support-hidden");
                        return;
                    }

                    jQuery.each(matchingTickets, (index, ticket) => {
                        // Choose ID label dynamically
                        let idLabel = "";
                        let idValue = "";

                        if (ticket.type === "smart_reply") {
                            idLabel = "'.__("Smart Reply ID:", "majestic-support").'";
                            idValue = ticket.id;
                        } else {
                            idLabel = "'.__("Similar to Ticket #", "majestic-support").'";
                            idValue = ticket.ticketid;
                        }

                        const listItem = jQuery("<div></div>")
                            .addClass("mjtc-ai-card")
                            .data("ticket-id", ticket.id) // Store ticket ID in data attribute
                            .data("type", ticket.type) // Store type in data attribute
                            .html(`<div class="mjtc-ai-card-header"><span class="mjtc-ai-match-badge high-match" style="display: none"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>96% Match</span><span class="mjtc-ai-card-source">${idLabel} ${idValue}</span></div><h4 class="mjtc-ai-card-title">${ticket.text}</h4><div class="mjtc-ai-card-snippet">${ticket.message}</div>`);
                        matchingTicketsList.append(listItem);
                    });
                    jsReplyHideLoading();
                    matchingTicketsSection.removeClass("mjtc-support-hidden");
                }

                function escapeHtml(unsafe) {
                    return unsafe
                        .replace(/&/g, "&amp;")
                        .replace(/</g, "&lt;")
                        .replace(/>/g, "&gt;")
                        .replace(/"/g, "&quot;")
                }

                // Function to display replies of a selected ticket
                function displayTicketReplies(type, ticket, replies) {
                    // Initialize replies as empty array if undefined
                    if (typeof replies === "undefined") {
                        replies = [];
                    }
                    // Determine label + name value based on type
                    let labelText, nameValue;
                    // Parse if it is a string
                    if (typeof replies === "string") {
                        try {
                            replies = JSON.parse(replies);
                            // Ensure it is always an array after parsing
                            if (!Array.isArray(replies)) {
                                replies = [];
                            }
                        } catch (e) {
                            console.error("Failed to parse replies:", e);
                            replies = [];
                        }
                    }
                    
                    // Additional type checking
                    if (!Array.isArray(replies)) {
                        console.error("Replies is not an array:", replies);
                        replies = [];
                    }

                    if (type === "ticket") {
                        jQuery("#mjtc-support-selected-ticket-replies-title").text(`'.__("Replies for:", "majestic-support").' `+ticket.text);
                    } else {
                        jQuery("#mjtc-support-selected-ticket-replies-title").text(`'.__("Smart Reply for:", "majestic-support").' `+ticket.text);
                    }
                    selectedTicketRepliesContent.empty(); // Clear previous replies

                    // Now safe to check length
                    if (replies.length === 0) {
                        selectedTicketRepliesContent.html(`<div class="mjtc-ai-card-snippet">'.__("No replies found for this ticket.", "majestic-support").'</div>`);
                    } else {
                        jQuery.each(replies, (index, reply) => {
                            if (type === "ticket") {
                                labelText = "'.__("Reply By", "majestic-support").':";
                                nameValue = reply?.name || "'.__("Unknown", "majestic-support").'";
                            } else {
                                labelText = "'.__("Used By", "majestic-support").':";
                                nameValue = reply?.usedby || "'.__("Unknown", "majestic-support").'";
                            }
                            // Add null checks for reply properties
                            const replyId = reply?.id || __("N/A", "majestic-support");
                            const replyText = reply?.text || "'.__("No content", "majestic-support").'"; 
                            const replyTimestamp = reply?.timestamp ? new Date(reply.timestamp).toLocaleString() : "'.__("No date", "majestic-support").'";

                            const replyDiv = jQuery("<div></div>")
                                .addClass("mjtc-support-reply-item mjtc-ai-card")
                                .html(`
                                    <div class="mjtc-support-reply-header mjtc-ai-card-title">
                                        <span class="mjtc-support-reply-id">${labelText} ${escapeHtml(nameValue)}</span>
                                        <span class="mjtc-support-reply-timestamp">`+replyTimestamp+`</span>
                                    </div>
                                    <div class="mjtc-support-reply-text mjtc-ai-card-snippet">
                                        `+replyText+`
                                    </div>
                                    <div class="mjtc-support-reply-actions mjtc-ai-card-actions">
                                        <button class="mjtc-support-reply-action-btn copy-btn mjtc-ai-action-btn primary" data-reply-content="`+escapeHtml(replyText)+`">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M12 20h9"></path>
                                                <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z">
                                                </path>
                                            </svg>
                                            '.__('Copy', 'majestic-support').'
                                        </button>
                                        <button class="mjtc-ai-action-btn secondary mjtc-support-reply-action-btn append-btn" data-reply-content="`+escapeHtml(replyText)+`">'.__('Append', 'majestic-support').'</button>
                                    </div>
                                `);
                            selectedTicketRepliesContent.append(replyDiv);
                        });

                        // Attach event listeners
                        selectedTicketRepliesContent.find(".copy-btn").on("click", function(e) {
                            e.preventDefault();
                            copyToClipboard(jQuery(this).data("reply-content"));
                        });
                        
                        selectedTicketRepliesContent.find(".append-btn").on("click", function(e) {
                            e.preventDefault();
                            appendToReplyArea(jQuery(this).data("reply-content"));
                        });
                    }

                    matchingTicketsSection.addClass("mjtc-support-hidden");
                    selectedTicketRepliesSection.removeClass("mjtc-support-hidden");
                }

                // Event listener for Close Replies button
                jQuery("#mjtc-support-close-replies-btn").on("click", function(e) {
                    e.preventDefault();
                    selectedTicketRepliesSection.addClass("mjtc-support-hidden");
                    matchingTicketsSection.removeClass("mjtc-support-hidden"); // Show matching tickets again
                });

                // Event listener for Close Tickets button
                jQuery("#mjtc-support-close-tickets-btn").on("click", function(e) {
                    e.preventDefault();
                    matchingTicketsList.empty(); // Clear previous list
                    jQuery("#mjtc-support-tickets-filter").val("all"); // Reset filter when showing new tickets
                    selectedTicketRepliesSection.addClass("mjtc-support-hidden"); // Hide replies section if open
                    jQuery("#mjtc-support-replies-filter").val("all"); // Reset filter when showing new tickets
                    jQuery(".mjtc-support-container").hide();
                    matchingTicketsSection.addClass("mjtc-support-hidden");
                });
            });';
            wp_add_inline_script('majestic-support-cmain-js',$majesticsupport_js);
            $majesticsupport_js ="
                jQuery(document).ready(function() {
                    jQuery(document).on('submit', '#mjtc-support-usercredentails-form', function(e) {
                        e.preventDefault(); // avoid to execute the actual submit of the form.
                        var fdata = jQuery(this).serialize(); // serializes the form's elements.
                        var nonce = jQuery('#nonce').val();
                        jQuery.post(ajaxurl, {
                            action: 'mjsupport_ajax',
                            mjsmod: 'privatecredentials',
                            task: 'storePrivateCredentials',
                            formdata_string: fdata,
                            '_wpnonce': nonce
                        }, function(data) {
                            if (data) { // ajax executed
                                var return_data = jQuery.parseJSON(data);
                                if (return_data.status == 1) {
                                    jQuery('.mjtc-support-usercredentails-wrp').show();
                                    jQuery('.mjtc-support-usercredentails-form-wrap').hide();
                                    jQuery('.mjtc-support-usercredentails-credentails-wrp').append(MJTC_msDecodeHTML(return_data.content));
                                } else {
                                    alert(return_data.error_message);
                                }
                            } else {
                                jQuery('#usercredentailspopup').slideUp('slow');
                                setTimeout(function() {
                                    jQuery('div#userpopupblack').hide();
                                }, 700);
                            }
                        });
                    });
                    jQuery('span.mjtc-support-thread-read-status-wrp').hover(
                        function(e){
                            jQuery(this).find('span.mjtc-support-thread-read-status-detail').css('display','inline-block');
                        },
                        function(e){
                            jQuery(this).find('span.mjtc-support-thread-read-status-detail').css('display','none');
                        }
                    );
                });

                function addEditCredentail(nonce, ticketid, internalid, uid, cred_id = 0, cred_data = '') {
                    jQuery.post(ajaxurl, {
                        action: 'mjsupport_ajax',
                        mjsmod: 'privatecredentials',
                        task: 'getFormForPrivteCredentials',
                        ticketid: ticketid,
                        internalid: internalid,
                        cred_id: cred_id,
                        cred_data: cred_data,
                        uid: uid,
                        '_wpnonce':nonce
                    }, function(data) {
                        if (data) { // ajax executed
                            var return_data = jQuery.parseJSON(data);
                            jQuery('.mjtc-support-usercredentails-wrp').hide();
                            jQuery('.mjtc-support-usercredentails-form-wrap').show();
                            jQuery('.mjtc-support-usercredentails-form-wrap').html(MJTC_msDecodeHTML(return_data));
                            if (cred_id != 0) {
                                jQuery('#mjtc-support-usercredentails-single-id-' + cred_id).remove();
                            }
                        }
                    });
                }

                function getCredentails(ticketid, internalid, nonce) {
                    jQuery.post(ajaxurl, {
                        action: 'mjsupport_ajax',
                        mjsmod: 'privatecredentials',
                        task: 'getPrivateCredentials',
                        ticketid: ticketid,
                        internalid: internalid,
                        '_wpnonce':nonce
                    }, function(data) {
                        if (data) { // ajax executed
                            var return_data = jQuery.parseJSON(data);
                            if (return_data.status == 1) {
                                jQuery('#usercredentailspopup').slideDown('slow');
                                jQuery('.mjtc-support-usercredentails-wrp').slideDown('slow');
                                jQuery('.mjtc-support-usercredentails-form-wrap').hide();
                                if (return_data.content != '') {
                                    jQuery('.mjtc-support-usercredentails-credentails-wrp').html('');
                                    jQuery('.mjtc-support-usercredentails-credentails-wrp').append(MJTC_msDecodeHTML(return_data.content));
                                }
                            } else {
                                jQuery('#usercredentailspopup').slideUp('slow');
                                setTimeout(function() {
                                    jQuery('div#userpopupblack').hide();
                                }, 700);
                            }
                        }
                    });
                    return false;
                }

                function removeCredentail(cred_id, internalid, nonce){
                    if (!confirm('" . esc_js( __('Are you sure you want to delete this credential?', 'majestic-support') ) . "')) {
                        return false;
                    }
                    var params = {
                        action: 'mjsupport_ajax',
                        mjsmod: 'privatecredentials',
                        task: 'removePrivateCredential',
                        cred_id: cred_id,
                        internalid: internalid,
                        '_wpnonce':nonce
                    };";
                    if(MJTC_includer::MJTC_getObjectClass('user')->MJTC_isguest() && isset(majesticsupport::$_data[0]->id)){
                        $majesticsupport_js .= '
                        params.email = "'. esc_attr(majesticsupport::$_data[0]->email) .'";
                        params.ticketrandomid = "'. esc_attr(majesticsupport::$_data[0]->ticketid) .'";';
                    }
                    $majesticsupport_js .= "
                    jQuery.post(ajaxurl, params, function(data) {
                        if (data) { // ajax executed
                            if (cred_id != 0) {
                                jQuery('#mjtc-support-usercredentails-single-id-' + cred_id).remove();
                            }
                        }
                    });
                    return false;
                }

                function closeCredentailsForm(ticketid, internalid, nonce) {
                    getCredentails(ticketid, internalid, nonce);
                }
            ";
            $majesticsupport_js .= "
                function showPopupAndFillValues(id, pfor, nonce) {
                    jQuery('div.edit-time-popup').hide();
                    if (pfor == 1) {
                        jQuery.post(ajaxurl, {
                            action: 'mjsupport_ajax',
                            val: id,
                            mjsmod: 'reply',
                            task: 'getReplyDataByID',
                            '_wpnonce': nonce
                        }, function(data) {
                            if (data) {
                                jQuery('div.popup-header-text').html('". esc_html(__('Edit Reply','majestic-support')) ."');
                                d = jQuery.parseJSON(data);
                                tinyMCE.get('mjsupport_replytext').execCommand('mceSetContent', false, d.message);
                                jQuery('div.edit-time-popup').hide();
                                jQuery('form#ms-time-edit-form').hide();
                                jQuery('form#ms-note-edit-form').hide();
                                jQuery('form#ms-reply-form').show();
                                jQuery('input#reply-replyid').val(id);
                                jQuery('div.ms-popup-background').show();
                                jQuery('div#ms-popup-wrapper').slideDown('slow');
                            }
                        });
                    } else if (pfor == 2) {
                        jQuery.post(ajaxurl, {
                            action: 'mjsupport_ajax',
                            val: id,
                            mjsmod: 'timetracking',
                            task: 'getTimeByReplyID',
                            '_wpnonce': nonce
                        }, function(data) {
                            if (data) {
                                jQuery('div.popup-header-text').html('". esc_html(__('Edit Time','majestic-support')) ."');
                                d = jQuery.parseJSON(data);
                                jQuery('div.edit-time-popup').hide();
                                jQuery('form#ms-reply-form').hide();
                                jQuery('form#ms-note-edit-form').hide();
                                jQuery('div.system-time-div').hide();
                                jQuery('form#ms-time-edit-form').show();
                                jQuery('input#reply-replyid').val(id);
                                jQuery('div.ms-popup-background').show();
                                jQuery('div#ms-popup-wrapper').slideDown('slow');
                                jQuery('input#edited_time').val(d.time);
                                jQuery('textarea#edit_reason').text(d.desc);
                                if (d.conflict == 1) {
                                    jQuery('div.system-time-div').show();
                                    jQuery('input#time-confilct').val(d.conflict);
                                    jQuery('input#systemtime').val(d.systemtime);
                                    jQuery('select#time-confilct-combo').val(0);
                                }
                            }
                        });
                    } else if (pfor == 3) {
                        jQuery.post(ajaxurl, {
                            action: 'mjsupport_ajax',
                            val: id,
                            mjsmod: 'note',
                            task: 'getTimeByNoteID',
                            '_wpnonce': nonce
                        }, function(data) {
                            if (data) {
                                jQuery('div.popup-header-text').html('". esc_html(__('Edit Time','majestic-support')) ."');
                                d = jQuery.parseJSON(data);
                                jQuery('div.edit-time-popup').hide();
                                jQuery('form#ms-reply-form').hide();
                                jQuery('form#ms-note-edit-form').show();
                                jQuery('form#ms-time-edit-form').hide();
                                jQuery('div.system-time-div').hide();
                                jQuery('input#note-noteid').val(id);
                                jQuery('div.ms-popup-background').show();
                                jQuery('div#ms-popup-wrapper').slideDown('slow');
                                jQuery('input#edited_time').val(d.time);
                                jQuery('textarea#edit_reason').text(d.desc);
                                if (d.conflict == 1) {
                                    jQuery('div.system-time-div').show();
                                    jQuery('input#time-confilct').val(d.conflict);
                                    jQuery('input#systemtime').val(d.systemtime);
                                    jQuery('select#time-confilct-combo').val(0);
                                }
                            }
                        });
                    } else if (pfor == 4) {
                        jQuery.post(ajaxurl, {
                            action: 'mjsupport_ajax',
                            ticketid: id,
                            mjsmod: 'mergeticket',
                            task: 'getTicketsForMerging',
                            '_wpnonce': nonce
                        }, function(data) {
                            if (data) {
                                jQuery('div.popup-header-text').html(
                                    '". esc_html(__('Merge Ticket','majestic-support')) ."');
                                data = jQuery.parseJSON(data);
                                jQuery('div#popup-record-data').html('');
                                jQuery('div#popup-record-data').slideDown('slow');
                                jQuery('div#popup-record-data').html(MJTC_msDecodeHTML(data['data']));
                            }
                        });
                    }
                    return false;
                }

                function updateticketlist(pagenum, ticketid,nonce) {
                    jQuery.post(ajaxurl, {
                        action: 'mjsupport_ajax',
                        mjsmod: 'mergeticket',
                        task: 'getTicketsForMerging',
                        ticketid: ticketid,
                        ticketlimit: pagenum,
                        '_wpnonce': nonce
                    }, function(data) {
                        if (data) {
                            data = jQuery.parseJSON(data);
                            jQuery('div#popup-record-data').html('');
                            jQuery('div#popup-record-data').html(MJTC_msDecodeHTML(data['data']));
                        }
                    });
                }
            ";
        wp_add_inline_script('majestic-support-cmain-js',$majesticsupport_js);    
    ?>
    <div id="userpopupblack" style="display:none;"> </div>
    <div id="black_wrapper_ai_reply" style="display:none;"></div>
    <div id="mjtc_ai_reply_loading" style="display: none;">
        <img alt="<?php echo esc_attr(__('spinning wheel','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/spinning-wheel.gif" />
    </div>
    <span style="display:none"
        id="filesize"><?php echo esc_html(__('Error file size too large', 'majestic-support')); ?></span>
    <span style="display:none"
        id="fileext"><?php echo esc_html(__('The uploaded file extension not valid', 'majestic-support')); ?></span>
    <div class="ms-popup-background" style="display:none"></div>
    <div id="popup-record-data" style="width:100%;"></div>
    <div id="ms-popup-wrapper" class="ms-popup-wrapper" style="display:none">
        <!-- Js Ticket Edit Time Popups -->
        <div class="ms-popup-header">
            <div class="popup-header-text">
                <?php echo esc_html(__('Edit Timer','majestic-support')); ?>
            </div>
            <div class="popup-header-close-img">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"></path><path d="m6 6 18 18"></path></svg>
            </div>
        </div>
        <div class="edit-time-popup" style="display:none;">
            <div class="mjtc-support-edit-form-wrp">
                <div class="mjtc-support-edit-field-title">
                    <?php echo esc_html(__('Time', 'majestic-support')); ?>&nbsp;<span style="color: red">*</span>
                </div>
                <div class="mjtc-support-edit-field-wrp">
                    <?php echo wp_kses(MJTC_formfield::MJTC_text('edited_time', '', array('class' => 'inputbox mjtc-support-edit-field-input')), MJTC_ALLOWED_TAGS) ?>
                </div>
                <div class="mjtc-support-edit-field-title">
                    <?php echo esc_html(__('Reason For Editing The Timer', 'majestic-support')); ?>
                </div>
                <div class="mjtc-support-edit-field-wrp">
                    <?php echo wp_kses(MJTC_formfield::MJTC_textarea('t_desc', '', array('class' => 'inputbox')), MJTC_ALLOWED_TAGS); ?>
                </div>
                <div class="mjtc-support-priorty-btn-wrp">
                    <?php echo wp_kses(MJTC_formfield::MJTC_submitbutton('pok', esc_html(__('Save', 'majestic-support')), array('class' => 'mjtc-support-priorty-save','onclick' => 'updateTimerFromPopup();')), MJTC_ALLOWED_TAGS); ?>
                    <?php echo wp_kses(MJTC_formfield::MJTC_button('canceleeee', esc_html(__('Cancel', 'majestic-support')), array('class' => 'mjtc-support-priorty-cancel','onclick'=>'closePopup();')), MJTC_ALLOWED_TAGS); ?>
                </div>
            </div>
        </div>

        <form id="ms-reply-form" style="display:none" method="post" action="<?php echo esc_url(wp_nonce_url(admin_url("admin.php?page=majesticsupport_reply&task=saveeditedreply&action=mstask"),"save-edited-reply-".majesticsupport::$_data[0]->id)); ?>">
            <div class="mjtc-support-edit-form-wrp">
                <div class="mjtc-support-form-field-wrp">
                    <?php wp_editor('', 'mjsupport_replytext', array('media_buttons' => false,'editor_height' => 200, 'textarea_rows' => 20,)); ?>
                </div>
            </div>
            <div class="mjtc-support-priorty-btn-wrp">
                <?php echo wp_kses(MJTC_formfield::MJTC_submitbutton('ppok', esc_html(__('Save', 'majestic-support')), array('class' => 'mjtc-support-priorty-save', 'onclick' => "return checktinymcebyid('mjsupport_replytext');")), MJTC_ALLOWED_TAGS); ?>
                <?php echo wp_kses(MJTC_formfield::MJTC_button('canceleeeee', esc_html(__('Cancel', 'majestic-support')), array('class' => 'mjtc-support-priorty-cancel','onclick'=>'closePopup();')), MJTC_ALLOWED_TAGS); ?>
            </div>
            <?php echo wp_kses(MJTC_formfield::MJTC_hidden('reply-replyid', ''), MJTC_ALLOWED_TAGS); ?>
            <?php echo wp_kses(MJTC_formfield::MJTC_hidden('reply-tikcetid',majesticsupport::$_data[0]->id), MJTC_ALLOWED_TAGS); ?>
        </form>
        <?php if(in_array('timetracking', majesticsupport::$_active_addons)){ ?>

        <form id="ms-time-edit-form" style="display:none" method="post"
            action="<?php echo esc_url(wp_nonce_url(admin_url("admin.php?page=majesticsupport_reply&task=saveeditedtime&action=mstask"),"save-edited-time-reply-".majesticsupport::$_data[0]->id)); ?>">
            <div class="mjtc-support-edit-form-wrp">
                <div class="mjtc-support-edit-field-title">
                    <?php echo esc_html(__('Time', 'majestic-support')); ?>&nbsp;<span style="color: red">*</span>
                </div>
                <div class="mjtc-support-edit-field-wrp">
                    <?php echo wp_kses(MJTC_formfield::MJTC_text('edited_time', '', array('class' => 'inputbox mjtc-support-edit-field-input')), MJTC_ALLOWED_TAGS) ?>
                </div>
                <div class="mjtc-support-edit-field-title">
                    <?php echo esc_html(__('System Time', 'majestic-support')); ?>
                </div>
                <div class="mjtc-support-edit-field-wrp">
                    <?php echo wp_kses(MJTC_formfield::MJTC_text('systemtime', '', array('class' => 'inputbox mjtc-support-edit-field-input','disabled'=>'disabled')), MJTC_ALLOWED_TAGS) ?>
                </div>
                <div class="mjtc-support-edit-field-title">
                    <?php echo esc_html(__('Reason For Editing', 'majestic-support')); ?>
                </div>
                <div class="mjtc-support-edit-field-wrp">
                    <?php echo wp_kses(MJTC_formfield::MJTC_textarea('edit_reason', '', array('class' => 'inputbox mjtc-support-edit-field-input')), MJTC_ALLOWED_TAGS) ?>
                </div>
                <div class="mjtc-form-wrapper system-time-div" style="display:none;">
                    <div class="mjtc-form-title"><?php echo esc_html(__('Resolve Conflict', 'majestic-support')); ?>
                    </div>
                    <div class="mjtc-form-value">
                        <?php echo wp_kses(MJTC_formfield::MJTC_select('time-confilct-combo', $MJTC_yesno, ''), MJTC_ALLOWED_TAGS); ?>
                    </div>
                </div>
                <div class="mjtc-support-priorty-btn-wrp">
                    <?php echo wp_kses(MJTC_formfield::MJTC_submitbutton('pppok', esc_html(__('Save', 'majestic-support')), array('class' => 'mjtc-support-priorty-save','onclick' => 'updateTimerFromPopup();')), MJTC_ALLOWED_TAGS); ?>
                    <?php echo wp_kses(MJTC_formfield::MJTC_button('canceleeeeee', esc_html(__('Cancel', 'majestic-support')), array('class' => 'mjtc-support-priorty-cancel','onclick'=>'closePopup();')), MJTC_ALLOWED_TAGS); ?>
                </div>
            </div>
            <?php echo wp_kses(MJTC_formfield::MJTC_hidden('reply-replyid', ''), MJTC_ALLOWED_TAGS); ?>
            <?php echo wp_kses(MJTC_formfield::MJTC_hidden('reply-tikcetid',majesticsupport::$_data[0]->id), MJTC_ALLOWED_TAGS); ?>
            <?php echo wp_kses(MJTC_formfield::MJTC_hidden('time-confilct',''), MJTC_ALLOWED_TAGS); ?>
        </form>

        <form id="ms-note-edit-form" style="display:none" method="post" action="<?php echo esc_url(wp_nonce_url(admin_url("admin.php?page=majesticsupport_note&task=saveeditedtime&action=mstask"),"save-edited-time-note-".majesticsupport::$_data[0]->id)); ?>">
            <div class="mjtc-col-md-12 mjtc-form-wrapper">
                <div class="mjtc-col-md-12 mjtc-form-title"><?php echo esc_html(__('Time', 'majestic-support')); ?>
                </div>
                <div class="mjtc-col-md-12 mjtc-form-value">
                    <?php echo wp_kses(MJTC_formfield::MJTC_text('edited_time', '', array('class' => 'inputbox')), MJTC_ALLOWED_TAGS) ?>
                </div>
            </div>
            <div class="mjtc-col-md-12 mjtc-form-wrapper system-time-div" style="display:none;">
                <div class="mjtc-col-md-12 mjtc-form-title">
                    <?php echo esc_html(__('System Time', 'majestic-support')); ?></div>
                <div class="mjtc-col-md-12 mjtc-form-value">
                    <?php echo wp_kses(MJTC_formfield::MJTC_text('systemtime', '', array('class' => 'inputbox','disabled'=>'disabled')), MJTC_ALLOWED_TAGS) ?>
                </div>
            </div>
            <div class="mjtc-col-md-12 mjtc-form-wrapper">
                <div class="mjtc-col-md-12 mjtc-form-title">
                    <?php echo esc_html(__('Reason For Editing', 'majestic-support')); ?>
                </div>
                <div class="mjtc-col-md-12 mjtc-form-value">
                    <?php echo wp_kses(MJTC_formfield::MJTC_textarea('edit_reason', '', array('class' => 'inputbox')), MJTC_ALLOWED_TAGS) ?>
                </div>
            </div>
            <div class="mjtc-col-md-12 mjtc-form-wrapper system-time-div" style="display:none;">
                <div class="mjtc-col-md-12 mjtc-form-title">
                    <?php echo esc_html(__('Resolve Conflict', 'majestic-support')); ?>
                </div>
                <div class="mjtc-col-md-12 mjtc-form-value">
                    <?php echo wp_kses(MJTC_formfield::MJTC_select('time-confilct-combo', $MJTC_yesno, ''), MJTC_ALLOWED_TAGS); ?>
                </div>
            </div>
            <div class="mjtc-col-md-12 mjtc-form-button-wrapper">
                <?php echo wp_kses(MJTC_formfield::MJTC_submitbutton('ppppok', esc_html(__('Save', 'majestic-support')), array('class' => 'button')),MJTC_ALLOWED_TAGS); ?>
                <?php echo wp_kses(MJTC_formfield::MJTC_button('cancele', esc_html(__('Cancel', 'majestic-support')), array('class' => 'button', 'onclick'=>'closePopup();')), MJTC_ALLOWED_TAGS); ?>
            </div>
            <?php echo wp_kses(MJTC_formfield::MJTC_hidden('note-noteid', ''), MJTC_ALLOWED_TAGS); ?>
            <?php echo wp_kses(MJTC_formfield::MJTC_hidden('note-tikcetid',majesticsupport::$_data[0]->id), MJTC_ALLOWED_TAGS); ?>
            <?php echo wp_kses(MJTC_formfield::MJTC_hidden('time-confilct',''), MJTC_ALLOWED_TAGS); ?>
        </form>
        <?php } ?>
    </div>
    <div class="ms-popup-wrapper ms-merge-popup-wrapper" style="display:none">
        <div class="ms-popup-header">
            <div class="popup-header-text">
                <?php echo esc_html(__('Edit Timer','majestic-support'));?>
            </div>
            <div class="popup-header-close-img">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"></path><path d="m6 6 18 18"></path></svg>
            </div>
        </div>
    </div>

    <?php
    $MJTC_field_array = MJTC_includer::MJTC_getModel('fieldordering')->getFieldTitleByFieldfor(1, majesticsupport::$_data[0]->multiformid);
    if($MJTC_printflag == false && majesticsupport::$_data['user_staff'] && majesticsupport::$_data[0]->status != 5 && majesticsupport::$_data[0]->status != 6) {
        if(in_array('actions',majesticsupport::$_active_addons)){ 
            if(MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForTask('Ticket Department Transfer')){ ?>
                <div id="popupfordepartmenttransfer" style="display:none">
                    <div class="ms-popup-header">
                        <div class="popup-header-text">
                            <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field_array['department'])) ." ". esc_html(__('Transfer', 'majestic-support')); ?>
                        </div>
                        <div class="popup-header-close-img">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"></path><path d="m6 6 18 18"></path></svg>
                        </div>
                    </div>
                    <div>
                        <form method="post" action="<?php echo esc_url(wp_nonce_url(majesticsupport::makeUrl(array('mjsmod'=>'ticket','task'=>'transferdepartment')),"transfer-department-".majesticsupport::$_data[0]->id)); ?>"
                            enctype="multipart/form-data">
                            <div class="mjtc-support-premade-msg-wrp">
                                <!-- Select Department Wrapper -->
                                <div class="mjtc-support-premade-field-title">
                                    <?php echo esc_html(__('Select', 'majestic-support')) ." ". esc_html(majesticsupport::MJTC_getVarValue($MJTC_field_array['department'])); ?></div>
                                <div class="mjtc-support-premade-field-wrp">
                                    <?php echo wp_kses(MJTC_formfield::MJTC_select('departmentid', MJTC_includer::MJTC_getModel('department')->getDepartmentForCombobox(), isset(majesticsupport::$_data[0]->departmentid) ? majesticsupport::$_data[0]->departmentid : '', esc_html(__('Select', 'majestic-support')) ." ". esc_html(majesticsupport::MJTC_getVarValue($MJTC_field_array['department'])), array('class' => 'mjtc-support-premade-select')), MJTC_ALLOWED_TAGS); ?>

                                </div>
                            </div>
                            <?php
                            if(in_array('note', majesticsupport::$_active_addons)){ ?>
                                <div class="mjtc-support-text-editor-wrp">
                                    <div class="mjtc-support-text-editor-field-title">
                                        <?php echo esc_html(__('Type Note for', 'majestic-support')) ." ". esc_html(majesticsupport::MJTC_getVarValue($MJTC_field_array['department'])); ?>
                                    </div>
                                    <div class="mjtc-support-text-editor-field">
                                        <?php wp_editor('', 'departmenttranfernote', array('media_buttons' => false)); ?>
                                    </div>
                                </div>
                                <?php
                            } ?>
                            <div class="mjtc-support-reply-form-button-wrp">
                                <?php echo wp_kses(MJTC_formfield::MJTC_submitbutton('departmenttransferbutton', esc_html(__('Transfer', 'majestic-support')), array('class' => 'button mjtc-support-save-button', 'onclick' => "return checktinymcebyid('departmenttranfernote');")), MJTC_ALLOWED_TAGS); ?>
                            </div>
                            <?php echo wp_kses(MJTC_formfield::MJTC_hidden('ticketid', majesticsupport::$_data[0]->id), MJTC_ALLOWED_TAGS); ?>
                            <?php echo wp_kses(MJTC_formfield::MJTC_hidden('uid', MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid()), MJTC_ALLOWED_TAGS); ?>
                            <?php echo wp_kses(MJTC_formfield::MJTC_hidden('action', 'ticket_transferdepartment'), MJTC_ALLOWED_TAGS); ?>
                            <?php echo wp_kses(MJTC_formfield::MJTC_hidden('form_request', 'majesticsupport'), MJTC_ALLOWED_TAGS); ?>
                            <?php echo wp_kses(MJTC_formfield::MJTC_hidden('mspageid', get_the_ID()), MJTC_ALLOWED_TAGS); ?>
                        </form>
                    </div> <!-- end of departmenttransfer div -->
                </div>
                <?php
            }
        }
        if(in_array('agent',majesticsupport::$_active_addons)){ 
            if(MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForTask('Assign Ticket To Agent')){ ?>
                <div id="popupforagenttransfer" style="display:none">
                    <div class="ms-popup-header">
                        <div class="popup-header-text">
                            <?php echo esc_html(__('Assign Ticket To Agent', 'majestic-support')); ?>
                        </div>
                        <div class="popup-header-close-img">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"></path><path d="m6 6 18 18"></path></svg>
                        </div>
                    </div>
                    <div>
                        <form class="mjtc-support-asiignagent-form" method="post" action="<?php echo esc_url(wp_nonce_url(majesticsupport::makeUrl(array('mjsmod'=>'ticket','task'=>'assigntickettostaff')),"assign-ticket-to-staff-".majesticsupport::$_data[0]->id)); ?>"
                            enctype="multipart/form-data">
                            <div class="mjtc-support-premade-msg-wrp">
                                <!-- Select Department Wrapper -->
                                <div class="mjtc-support-premade-field-title">
                                    <?php echo esc_html(__('Agent', 'majestic-support')); ?></div>
                                <div class="mjtc-support-premade-field-wrp">
                                    <?php echo wp_kses(MJTC_formfield::MJTC_select('staffid', MJTC_includer::MJTC_getModel('agent')->getStaffForCombobox(), majesticsupport::$_data[0]->staffid, esc_html(__('Select Agent', 'majestic-support')), array('class' => 'inputbox mjtc-support-premade-select')), MJTC_ALLOWED_TAGS); ?>
                                </div>
                            </div>
                            <?php if(in_array('note', majesticsupport::$_active_addons)){ ?>
                            <div class="mjtc-support-text-editor-wrp">
                                <div class="mjtc-support-text-editor-field-title">
                                    <?php echo esc_html(__('Assigning Note', 'majestic-support')); ?></div>
                                <div class="mjtc-support-text-editor-field">
                                    <?php wp_editor('', 'assignnote', array('media_buttons' => false)); ?>
                                </div>
                            </div>
                            <?php } ?>
                            <div class="mjtc-support-reply-form-button-wrp">
                                <?php echo wp_kses(MJTC_formfield::MJTC_submitbutton('assigntostaff', esc_html(__('Assign', 'majestic-support')), array('class' => 'button mjtc-support-save-button', 'onclick' => "return checktinymcebyid('assignnote');")), MJTC_ALLOWED_TAGS); ?>
                            </div>
                            <?php echo wp_kses(MJTC_formfield::MJTC_hidden('ticketid', majesticsupport::$_data[0]->id), MJTC_ALLOWED_TAGS); ?>
                            <?php echo wp_kses(MJTC_formfield::MJTC_hidden('uid', MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid()), MJTC_ALLOWED_TAGS); ?>
                            <?php echo wp_kses(MJTC_formfield::MJTC_hidden('action', 'ticket_assigntickettostaff'), MJTC_ALLOWED_TAGS); ?>
                            <?php echo wp_kses(MJTC_formfield::MJTC_hidden('form_request', 'majesticsupport'), MJTC_ALLOWED_TAGS); ?>
                            <?php echo wp_kses(MJTC_formfield::MJTC_hidden('mspageid', get_the_ID()), MJTC_ALLOWED_TAGS); ?>
                        </form>
                    </div> <!-- end of assigntostaff div -->
                </div>
                <?php
            }
        }
        if(in_array('note',majesticsupport::$_active_addons)){ ?>
            <div id="popupforinternalnote" style="display:none">
                <div class="ms-popup-header">
                    <div class="popup-header-text">
                        <?php echo esc_html(__('Internal Note', 'majestic-support')); ?>
                    </div>
                    <div class="internalnote-popup-header-close-img">
                    </div>
                </div>
                <div id="internal-note-popup-record-data">
                    <form method="post" action="<?php echo esc_url(majesticsupport::makeUrl(array('mjsmod'=>'note','task'=>'editnote'))); ?>"
                        enctype="multipart/form-data">
                        <div class="mjtc-support-internalnote-wrp">
                            <!-- Ticket Tittle -->
                            <div class="mjtc-support-internalnote-field-title">
                                <?php echo esc_html(__('Title', 'majestic-support')); ?>
                            </div>
                            <div class="mjtc-support-internalnote-field-wrp">
                                <?php echo wp_kses(MJTC_formfield::MJTC_text('internalnotetitle', '', array('class' => 'inputbox mjtc-support-internalnote-input')), MJTC_ALLOWED_TAGS) ?>
                            </div>
                        </div>
                        <div class="mjtc-support-text-editor-wrp">
                            <div class="mjtc-support-text-editor-field-title">
                                <?php echo esc_html(__('Type Internal Note', 'majestic-support')); ?></div>
                            <div class="mjtc-support-text-editor-field">
                                <?php wp_editor('', 'editinternalnote', array('media_buttons' => false)); ?>
                            </div>
                        </div>
                        <?php
                        if(!empty($MJTC_field_array['attachments'])){?>
                            <div class="mjtc-support-reply-attachments">
                                <!-- Attachments -->
                                <div class="mjtc-attachment-field-title">
                                    <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field_array['attachments'])); ?></div>
                                <div id="internal_note_file_name"></div>
                                <div class="mjtc-attachment-field">
                                    <div class="tk_attachment_value_wrapperform tk_attachment_staff_reply_wrapper">
                                        <span class="tk_attachment_value_text">
                                            <input type="file" class="inputbox mjtc-attachment-inputbox"
                                                name="note_attachment"
                                                onchange="MJTC_uploadfile(this, '<?php echo esc_js(majesticsupport::$_config['file_maximum_size']); ?>', '<?php echo esc_js(majesticsupport::$_config['file_extension']); ?>');"
                                                size="20" />
                                            <span class='tk_attachment_remove'></span>
                                        </span>
                                    </div>
                                    <span class="tk_attachments_configform">
                                        <?php
                                            $MJTC_data = esc_html(__('Maximum File Size', 'majestic-support')).' (' . esc_html(majesticsupport::$_config['file_maximum_size']).' KB)<br>'.esc_html(__('File Extension Type', 'majestic-support')).' (' . esc_html(majesticsupport::$_config['file_extension']) . ')';
                                            echo wp_kses($MJTC_data, MJTC_ALLOWED_TAGS);
                                        ?>
                                    </span>
                                </div>
                            </div>
                            <?php
                        } ?>
                        <div class="mjtc-support-reply-form-button-wrp">
                            <?php echo wp_kses(MJTC_formfield::MJTC_submitbutton('postinternalnote', esc_html(__('Post Internal Note', 'majestic-support')), array('class' => 'button mjtc-support-save-button', 'onclick' => "return checktinymcebyid('internalnote');")), MJTC_ALLOWED_TAGS); ?>
                        </div>

                        <?php echo wp_kses(MJTC_formfield::MJTC_hidden('id', ''), MJTC_ALLOWED_TAGS); ?>
                        <?php echo wp_kses(MJTC_formfield::MJTC_hidden('ticketid', ''), MJTC_ALLOWED_TAGS); ?>
                        <?php echo wp_kses(MJTC_formfield::MJTC_hidden('uid', MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid()), MJTC_ALLOWED_TAGS); ?>
                        <?php echo wp_kses(MJTC_formfield::MJTC_hidden('_wpnonce', ''), MJTC_ALLOWED_TAGS); ?>
                        <?php echo wp_kses(MJTC_formfield::MJTC_hidden('action', 'note_savenote'), MJTC_ALLOWED_TAGS); ?>
                        <?php echo wp_kses(MJTC_formfield::MJTC_hidden('form_request', 'majesticsupport'), MJTC_ALLOWED_TAGS); ?>
                        <?php echo wp_kses(MJTC_formfield::MJTC_hidden('mspageid', get_the_ID()), MJTC_ALLOWED_TAGS); ?>
                    </form>
                </div>
            </div>
        <?php }
        
    } ?>
    <div class="mjtc-support-top-sec-header mjtc-support-ticketdetail-topsec-header">
        <img decoding="async" class="mjtc-transparent-header-img1" alt="<?php echo esc_attr(__('Image','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/tp-image.png">
        <div class="mjtc-support-main-heading">
            <?php
            if (!empty($MJTC_field_array['subject'])) { ?>
                    <?php echo esc_html(majesticsupport::MJTC_getVarValue(majesticsupport::$_data[0]->subject)); ?>
                <?php
            } ?>
        </div>
        <div class="mjtc-support-top-sec-left-header">
            <div class="mjtc-support-ticket-header-topwrp">
                <div class="mjtc-support-ticket-header-leftwrp">
                    <div class="mjtc-sprt-left-status-wrp">
                        <?php
                        if (majesticsupport::$_data[0]->status == 1) {
                            $MJTC_color = "#FFFFFF;";
                            $MJTC_bgcolor = "#5bb12f";
                            $MJTC_ticketmessage = esc_html(__('Open', 'majestic-support'));
                        } else {
                            $MJTC_color = majesticsupport::$_data[0]->statuscolour;
                            $MJTC_bgcolor = majesticsupport::$_data[0]->statusbgcolour;
                            $MJTC_ticketmessage = esc_html(majesticsupport::$_data[0]->statustitle);
                        }
                        ?>
                        <div class="mjtc-sprt-det-status" style="background-color:<?php echo esc_attr($MJTC_bgcolor);?>;color :<?php echo esc_attr($MJTC_color);?>;">
                            <?php echo esc_html($MJTC_ticketmessage); ?>
                        </div>
                        <span class="mjtc-support-ticket-id mjtc-sprt-det-copy-id">
                            <?php echo esc_html(majesticsupport::$_data[0]->ticketid); ?>
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"><path d="M20 2H10c-1.1 0-2 .9-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2zM4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6z"></path></svg>
                        </span>
                    </div>
                    
                </div>
            </div>
        </div>
        <div class="mjtc-support-top-sec-right-header">
            <?php
            if(in_array('timetracking', majesticsupport::$_active_addons)) { ?>
                <div class="mjtc-timer-display-boxwrp">
                    <?php
                    if(isset(majesticsupport::$_data['time_taken'])){ ?>
                        <span class="mjtc-timer-display-boxwrp">
                            <?php
                            $MJTC_hours = floor(majesticsupport::$_data['time_taken'] / 3600);
                            $MJTC_mins = floor(majesticsupport::$_data['time_taken'] / 60);
                            $MJTC_mins = floor($MJTC_mins % 60);
                            $MJTC_secs = floor(majesticsupport::$_data['time_taken'] % 60);
                            $MJTC_time =  __('Total Time Taken','majestic-support').':&nbsp;'.esc_html(sprintf('%02d:%02d:%02d', $MJTC_hours, $MJTC_mins, $MJTC_secs));
                            echo esc_html($MJTC_time);
                            ?>
                        </span>
                        <?php
                    } ?>
                </div>
                <?php
            } ?>
            <div class="mjtc-support-ticket-header-rightwrp">
                <?php
                if(!empty($MJTC_field_array['priority']) || !empty(majesticsupport::$_data[0]->priority)) { ?>
                    <button class="mjtc-support-priority-btn" style="background: <?php echo esc_html(majesticsupport::$_data[0]->prioritycolour); ?>;">
                        <?php echo esc_html(majesticsupport::$_data[0]->priority); ?>
                    </button>
                    <?php
                } ?>
            </div>
        
        </div>
    </div>
    <!-- action end -->
    <!-- Main Workspace -->
    <div class="mjtc-support-cont-main-wrapper mjtc-support-ticket-detailcont-main-wrapper">
        <main class="mjtc-sprt-det-left">
            <!-- Action Bar -->
            <div class="mjtc-sprt-det-actn-btn-wrp">
                <div class="mjtc-sprt-det-left-actn-btnswrp">
                    <?php
                    $MJTC_layout1 = ( in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) ? 'staffmyticket':'myticket';
                    $MJTC_module1 = ( in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) ? 'agent':'ticket'; ?>
                    <a href="<?php echo esc_url(majesticsupport::makeUrl(array('mjsmod'=>$MJTC_module1, 'mjslay'=>$MJTC_layout1))); ?>" class="mjtc-sprt-det-actn-btn">
                        <svg class="mjtc-support-icon" viewBox="0 0 24 24"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg>
                        <?php echo esc_html(__('Back', 'majestic-support')); ?>
                    </a>
                    <div class="mjtc-support-separator" style="height: 20px;"></div>
                    <!-- Ticket Action Button -->
                    <?php
                    if ($MJTC_printflag == false){
                        $MJTC_printpermission = false;
                        if (in_array('agent',majesticsupport::$_active_addons) && majesticsupport::$_data['user_staff'] && majesticsupport::$_data[0]->status != 6 ) {
                            $MJTC_printpermission = MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForTask('Print Ticket'); ?>
                            <a class="mjtc-sprt-det-actn-btn mjtc-btn-info"
                                href="<?php echo esc_url(majesticsupport::makeUrl(array('mjsmod'=>'agent','mjslay'=>'staffaddticket','majesticsupportid'=>majesticsupport::$_data[0]->id))); ?>"
                                title="<?php echo esc_attr(__('Edit Ticket', 'majestic-support')); ?>">
                                <svg class="mjtc-sprt-icon" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                <span><?php echo esc_attr(__('Edit', 'majestic-support')); ?></span>
                            </a>
                            <?php
                            if (majesticsupport::$_data[0]->status != 5) {
                                if (in_array('ticketclosereason',majesticsupport::$_active_addons)) {
                                    $MJTC_js = 'showTicketCloseReasons('.esc_js(majesticsupport::$_data[0]->id).')';
                                } else {
                                    $MJTC_js = 'actionticket(2);';
                                } ?>
                                <a class="mjtc-sprt-det-actn-btn mjtc-btn-success" href="#" onclick="<?php echo esc_js($MJTC_js);?>"
                                    title="<?php echo esc_attr(__('Close Ticket', 'majestic-support')); ?>">
                                    <svg class="mjtc-sprt-icon" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                                    <span><?php echo esc_attr(__('Close', 'majestic-support')); ?></span>
                                </a>
                                <?php
                            } else { ?>
                                <a class="mjtc-sprt-det-actn-btn mjtc-btn-info" href="#" onclick="actionticket(3);"
                                    title="<?php echo esc_attr(__('Reopen Ticket', 'majestic-support')); ?>">
                                    <svg class="mjtc-sprt-icon" viewBox="0 0 24 24"><path d="M1 4v6h6"></path><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg>
                                    <span><?php echo esc_attr(__('Reopen', 'majestic-support')); ?></span>
                                </a>
                                <?php
                            }
                            if(in_array('actions',majesticsupport::$_active_addons)){ 
                                if($MJTC_printpermission && majesticsupport::$_data[0]->status != 6) { ?>
                                    <a class="mjtc-sprt-det-actn-btn" href="#" id="print-link" title="<?php echo esc_attr(__('Print Ticket', 'majestic-support')); ?>" 
                                        data-ticketid="<?php echo esc_attr(majesticsupport::$_data[0]->id); ?>">
                                        <svg class="mjtc-support-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect width="12" height="8" x="6" y="14"></rect></svg>
                                        <span><?php echo esc_attr(__('Print', 'majestic-support')); ?></span>
                                    </a>
                                    <!-- Print Ticket -->
                                    <?php
                                }
                            }
                        } else {
                            if (majesticsupport::$_data[0]->status != 6) {
                                if (majesticsupport::$_data[0]->status != 5) {
                                    if (in_array('ticketclosereason',majesticsupport::$_active_addons)) { ?>
                                        <a onclick="showTicketCloseReasons(<?php echo esc_html(majesticsupport::$_data[0]->id)?>, '<?php echo esc_html(majesticsupport::$_data[0]->internalid)?>')" title="<?php echo esc_attr(__('Close Ticket', 'majestic-support')); ?>" class="mjtc-sprt-det-actn-btn mjtc-btn-success">
                                            <svg class="mjtc-sprt-icon" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                                            <span><?php echo esc_attr(__('Close', 'majestic-support')); ?></span>
                                        </a>
                                        <?php
                                    } else { ?>
                                        <a onclick="return confirm('<?php echo esc_html(__('Are you sure to close this ticket', 'majestic-support')); ?>');" title="<?php echo esc_attr(__('Close Ticket', 'majestic-support')); ?>" class="mjtc-sprt-det-actn-btn mjtc-btn-success" href="<?php echo esc_url(wp_nonce_url(majesticsupport::makeUrl(array('mjsmod'=>'ticket','task'=>'closeticket','action'=>'mstask','ticketid'=> majesticsupport::$_data[0]->id,'internalid'=> majesticsupport::$_data[0]->internalid ,'mspageid'=>get_the_ID())),"close-ticket-".majesticsupport::$_data[0]->id)); ?>">
                                            <svg class="mjtc-sprt-icon" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                                            <span><?php echo esc_attr(__('Close', 'majestic-support')); ?></span>
                                        </a>
                                        <?php
                                    }
                                } else {
                                    if (MJTC_includer::MJTC_getModel('ticket')->checkCanReopenTicket(majesticsupport::$_data[0]->id)) {
                                        $MJTC_link = wp_nonce_url(majesticsupport::makeUrl(array('mjsmod'=>'ticket','task'=>'reopenticket','action'=>'mstask','ticketid'=> majesticsupport::$_data[0]->id,'internalid'=> majesticsupport::$_data[0]->internalid,'mspageid'=>get_the_ID())),"reopen-ticket-".majesticsupport::$_data[0]->id); ?>
                                        <a class="mjtc-sprt-det-actn-btn mjtc-btn-info" href="<?php echo esc_url($MJTC_link); ?>"
                                            title="<?php echo esc_attr(__('Reopen Ticket', 'majestic-support')); ?>">
                                            <svg class="mjtc-sprt-icon" viewBox="0 0 24 24"><path d="M1 4v6h6"></path><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg>
                                            <span><?php echo esc_attr(__('Reopen', 'majestic-support')); ?></span>
                                        </a>
                                        <?php
                                    }
                                }
                            }
                            if(majesticsupport::$_config['print_ticket_user'] == 1 ){
                                if(in_array('actions',majesticsupport::$_active_addons)){ ?>
                                    <a class="mjtc-sprt-det-actn-btn mjtc-btn-dark" title="<?php echo esc_attr(__('Print Ticket', 'majestic-support')); ?>" href="#" id="print-link" data-ticketid="<?php echo esc_attr(majesticsupport::$_data[0]->id); ?>">
                                        <svg class="mjtc-support-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect width="12" height="8" x="6" y="14"></rect></svg>
                                        <span><?php echo esc_attr(__('Print', 'majestic-support')); ?></span>
                                    </a>
                                    <?php
                                }
                            }
                            if (majesticsupport::$_config['show_ticket_delete_button'] == 1) { ?>
                                <div class="mjtc-support-separator"></div>
                                <a class="mjtc-sprt-det-actn-btn mjtc-btn-danger" title="<?php echo esc_attr(__('Delete Ticket', 'majestic-support')); ?>" onclick="return confirm('<?php echo esc_html(__('Are you sure you want to delete this ticket', 'majestic-support')); ?>');" href="<?php echo esc_url(wp_nonce_url(majesticsupport::makeUrl(array('mjsmod'=>'ticket','task'=>'deleteticket','action'=>'mstask','internalid'=> majesticsupport::$_data[0]->internalid,'ticketid'=> majesticsupport::$_data[0]->id ,'mspageid'=>get_the_ID())),'delete-ticket-'.majesticsupport::$_data[0]->id)); ?>" data-ticketid="<?php echo esc_attr(majesticsupport::$_data[0]->id); ?>">
                                    <svg class="mjtc-sprt-icon" viewBox="0 0 24 24"><path d="M3 6h18"></path><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                    <span><?php echo esc_attr(__('Delete', 'majestic-support')); ?></span>
                                </a>
                                <?php
                            }
                        }
                        if (in_array('agent',majesticsupport::$_active_addons) && majesticsupport::$_data['user_staff'] && majesticsupport::$_data[0]->status != 6) {
                            $MJTC_deletepermission = MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForTask('Delete Ticket');
                            if($MJTC_deletepermission) { ?>
                                <div class="mjtc-support-separator"></div>
                                <a class="mjtc-sprt-det-actn-btn mjtc-btn-danger" title="<?php echo esc_attr(__('Delete Ticket', 'majestic-support')); ?>" onclick="return confirm('<?php echo esc_html(__('Are you sure you want to delete this ticket', 'majestic-support')); ?>');" href="<?php echo esc_url(wp_nonce_url(majesticsupport::makeUrl(array('mjsmod'=>'ticket','task'=>'deleteticket','action'=>'mstask','internalid'=> majesticsupport::$_data[0]->internalid,'ticketid'=> majesticsupport::$_data[0]->id ,'mspageid'=>get_the_ID())),'delete-ticket-'.majesticsupport::$_data[0]->id)); ?>" data-ticketid="<?php echo esc_attr(majesticsupport::$_data[0]->id); ?>">
                                    <svg class="mjtc-sprt-icon" viewBox="0 0 24 24"><path d="M3 6h18"></path><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                    <span><?php echo esc_attr(__('Delete', 'majestic-support')); ?></span>
                                </a>
                                <?php
                            }
                        }
                    } else { ?>
                        <a class="mjtc-sprt-det-actn-btn mjtc-btn-dark" title="<?php echo esc_attr(__('Print Ticket', 'majestic-support')); ?>" href="javascript:window.print();">
                            <svg class="mjtc-support-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect width="12" height="8" x="6" y="14"></rect></svg>
                            <span><?php echo esc_attr(__('Print', 'majestic-support')); ?></span>
                        </a>
                        <?php
                    } ?>
                </div>
            </div>
            <!-- ... existing header ... -->
            <div class="mjtc-support-ticket-header">
                <div class="mjtc-support-header-tabs">
                    <button class="mjtc-support-tab-btn active" data-tab="conversation">
                        <?php echo esc_html(__('Conversation', 'majestic-support')); ?>
                        <span class="mjtc-support-count-badge">(
                            <?php 
                            $MJTC_conversation_count = count(majesticsupport::$_data[4]);
                            if (!empty(majesticsupport::$_data[0]->message)) {
                                $MJTC_conversation_count++;
                            }
                            echo esc_html($MJTC_conversation_count); ?>
                            )
                        </span>
                    </button>
                    <?php
                    if (in_array('agent',majesticsupport::$_active_addons) && majesticsupport::$_data['user_staff']) {
                        if (in_array('note', majesticsupport::$_active_addons)) { ?>
                            <button class="mjtc-support-tab-btn" data-tab="saved-notes">
                                <?php echo esc_html(__('Internal Notes', 'majestic-support')); ?>
                                <span class="mjtc-support-count-badge">( <?php echo esc_html(count(majesticsupport::$_data[6])); ?> )</span>
                            </button>
                            <?php
                        }
                    }
                    $MJTC_flagPrivateCredentials = false;
                    if (in_array('agent',majesticsupport::$_active_addons) && majesticsupport::$_data['user_staff'] && majesticsupport::$_data[0]->status != 6 ) {
                        $MJTC_credentialpermission = MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForTask('View Credentials');
                        if(in_array('privatecredentials',majesticsupport::$_active_addons) && $MJTC_credentialpermission){
                            $MJTC_flagPrivateCredentials = true;
                        }
                    } else {
                        if(in_array('privatecredentials',majesticsupport::$_active_addons) && majesticsupport::$_data[0]->status != 5 && majesticsupport::$_data[0]->status != 6){
                            $MJTC_flagPrivateCredentials = true;
                        }
                    }
                    if ($MJTC_flagPrivateCredentials) {
                        $MJTC_nonce = wp_create_nonce('get-private-credentials-'.majesticsupport::$_data[0]->id); ?>
                        <button class="mjtc-support-tab-btn" data-tab="credentials" onclick="getCredentails(<?php echo esc_js(majesticsupport::$_data[0]->id); ?>, '<?php echo esc_js(majesticsupport::$_data[0]->internalid); ?>', '<?php echo esc_js($MJTC_nonce); ?>')">
                            <?php echo esc_html(__('Private Credentials', 'majestic-support')); ?>
                            <span class="mjtc-support-count-badge">( 
                                <?php 
                                $MJTC_query = "SELECT count(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_privatecredentials` WHERE status = 1 AND ticketid = ".esc_sql(majesticsupport::$_data[0]->id);
                                $MJTC_cred_count = majesticsupport::$_db->get_var($MJTC_query);
                                echo esc_html($MJTC_cred_count); ?>
                                 )
                            </span>
                        </button>
                        <?php
                    }
                    if(in_array('tickethistory', majesticsupport::$_active_addons)){ ?>
                        <button class="mjtc-support-tab-btn" data-tab="history">
                            <?php
                            echo esc_html(__('History', 'majestic-support'));
                            if (!empty(majesticsupport::$_data[5])) { ?>
                                <span class="mjtc-support-count-badge">( <?php echo esc_html(count(majesticsupport::$_data[5])); ?> )</span>
                                <?php
                            } ?>
                        </button>
                        <?php
                    } ?>
                </div>
            </div>
            <div class="mjtc-support-thread" id="message-container">
                <!-- Customer Message -->
                <div class="mjtc-support-thread-item">
                    <div class="mjtc-support-timeline-line"></div>
                    <div class="mjtc-support-staff-img-wrapper">
                        <?php
                        echo wp_kses(MJTC_get_avatar(majesticsupport::$_data[0]->uid, 'mjtc-support-staff-img'), MJTC_ALLOWED_TAGS); ?>
                    </div>
                    <div class="mjtc-support-thread-cnt">
                        <div class="mjtc-support-thread-data-header">
                            <div class="mjtc-support-thread-data-header-leftwrp">
                                <?php
                                if(!empty($MJTC_field_array['fullname'])) { ?>
                                    <strong >
                                        <?php echo esc_html(majesticsupport::$_data[0]->name); ?>
                                    </strong>
                                    <?php 
                                }
                                if(!empty($MJTC_field_array['email'])) { ?>
                                    <span class="mjtc-support-email-badge" style="color: #64748b;">(<?php echo esc_html(majesticsupport::$_data[0]->email); ?>)</span>
                                    <?php
                                } ?>
                                <span class="mjtc-support-open-ticketaction" style="color: #64748b;"><?php echo esc_html__('opened this ticket', 'majestic-support'); ?></span>
                            </div>
                            <span class="mjtc-support-thread-time-stamp" style="color: #94a3b8;">
                                <?php echo esc_html(date_i18n("l F d, Y, H:i:s", MJTC_majesticsupportphplib::MJTC_strtotime(majesticsupport::$_data[0]->created))); ?>
                            </span>
                        </div>
                        <?php
                        if (!empty(majesticsupport::$_data['ticket_attachment']) || !empty(majesticsupport::$_data[0]->message)) { ?>
                            <div class="mjtc-support-thread-data note-msg">
                                <?php echo wp_kses_post(majesticsupport::$_data[0]->message); ?>
                                <?php
                                if (!empty(majesticsupport::$_data['ticket_attachment'])) { ?>
                                    <div class="mjtc-support-attachments-wrp">
                                        <?php
                                        foreach (majesticsupport::$_data['ticket_attachment'] AS $MJTC_attachment) {
                                            $MJTC_path = majesticsupport::makeUrl(array('mjsmod'=>'ticket','task'=>'downloadbyid','action'=>'mstask','id'=> $MJTC_attachment->id ,'mspageid'=>get_the_ID()));
                                            $MJTC_data = wp_check_filetype($MJTC_attachment->filename);
                                            $type = $MJTC_data['type'];
                                            $MJTC_ticketdata = '
                                            <div class="mjtc_supportattachment">
                                                <span class="mjtc_supportattachment_fname">
                                                    ' . esc_html($MJTC_attachment->filename) . '
                                                </span>
                                                <a class="mjtc-download-button" target="_blank" href="' . esc_url($MJTC_path) . '">
                                                    <svg class="mjtc-support-icon" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                                </a>';
                                                echo wp_kses($MJTC_ticketdata, MJTC_ALLOWED_TAGS);
                                                if(MJTC_majesticsupportphplib::MJTC_strpos($type, "image") !== false) {
                                                    $MJTC_ticketdata = '
                                                    <a data-gall="gallery-ticket-thread" class="mjtc-download-button venobox" data-vbtype="image" title="'.esc_html(__('View','majestic-support')).'" href="'. esc_url(MJTC_includer::MJTC_getModel('attachment')->getAttachmentImage($MJTC_attachment->id)) .'"  target="_blank">
                                                        <svg class="mjtc-support-icon" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                                    </a>';
                                                    echo wp_kses($MJTC_ticketdata, MJTC_ALLOWED_TAGS);
                                                }
                                            echo wp_kses('</div>', MJTC_ALLOWED_TAGS);
                                        }
                                        $MJTC_ticketdata = '<a class="mjtc-all-download-button" target="_blank" href="' . esc_url(majesticsupport::makeUrl(array('mjsmod'=>'ticket', 'task'=>'downloadall', 'internalid'=> majesticsupport::$_data[0]->internalid, 'action'=>'mstask', 'downloadid'=>majesticsupport::$_data[0]->id , 'mspageid'=>get_the_ID()))) . '" >'. esc_html(__('Download All', 'majestic-support')) . '</a>';
                                        echo wp_kses($MJTC_ticketdata, MJTC_ALLOWED_TAGS); ?>
                                    </div>
                                    <?php
                                } ?>
                            </div>
                            <?php
                        } ?>
                    </div>
                </div>
                <?php
                $MJTC_cur_uid = MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid();
                if (!empty(majesticsupport::$_data[4])) {
                    foreach (majesticsupport::$_data[4] AS $MJTC_reply) {
                        if (majesticsupport::$_data[0]->uid  != $MJTC_reply->uid) {
                            $MJTC_class_1 = 'agent';
                            $MJTC_class_2 = 'agent-bubble';
                        } else {
                            $MJTC_class_1 = '';
                            $MJTC_class_2 = '';
                        }
                        if ($MJTC_cur_uid == $MJTC_reply->uid)  ?>
                            <div class="mjtc-support-thread-item">
                                <div class="mjtc-support-timeline-line"></div>
                                <div class="mjtc-support-staff-img-wrapper <?php echo esc_attr($MJTC_class_1); ?>">
                                    <?php
                                    echo wp_kses(MJTC_get_avatar($MJTC_reply->uid, 'mjtc-support-staff-img'), MJTC_ALLOWED_TAGS); ?>
                                </div>
                                <div class="mjtc-support-thread-cnt <?php echo esc_attr($MJTC_class_2); ?>">
                                    <div class="mjtc-support-thread-data-header">
                                        <div class="mjtc-support-thread-data-header-leftwrp">
                                            <?php
                                            if(!empty($MJTC_field_array['fullname'])) { ?>
                                                <strong>
                                                    <?php
                                                    if (majesticsupport::$_config['anonymous_name_on_ticket_reply'] == 1) {
                                                        if(majesticsupport::$_data[0]->uid  != $MJTC_reply->uid){ //reply by staff, need anonymous
                                                            echo esc_html(majesticsupport::$_config['title']);
                                                        } else { // reply by user  
                                                            if($MJTC_reply->name == ""){
                                                                // name field value is empty in some old tickets
                                                                $MJTC_replyname = MJTC_includer::MJTC_getModel('reply')->getUserNameFromReplyById($MJTC_reply->replyid);
                                                                echo esc_html($MJTC_replyname); 
                                                            } else {
                                                                echo esc_html($MJTC_reply->name); 
                                                            }
                                                        }
                                                    } elseif(majesticsupport::$_config['anonymous_name_on_ticket_reply'] == 2) {
                                                        if($MJTC_reply->name == ""){
                                                            // name field value is empty in some old tickets
                                                            $MJTC_replyname = MJTC_includer::MJTC_getModel('reply')->getUserNameFromReplyById($MJTC_reply->replyid);
                                                            echo esc_html($MJTC_replyname); 
                                                        } else {
                                                            echo esc_html($MJTC_reply->name); 
                                                        }
                                                    }
                                                    ?>
                                                </strong>
                                                <?php 
                                            } ?>
                                            <?php
                                            if (majesticsupport::$_config['show_email_on_ticket_reply'] == 1 && !empty($MJTC_field_array['email'])) {
                                                if(isset($MJTC_reply->staffemail)){ ?>
                                                    <span class="mjtc-support-staff-email-badge" style="color: #64748b;">
                                                        (<?php echo esc_html($MJTC_reply->staffemail); ?>)
                                                    </span>
                                                    <?php
                                                } elseif(isset($MJTC_reply->useremail)) { ?>
                                                    <span class="mjtc-support-user-email-badge" style="color: #64748b;">
                                                        (<?php echo esc_html($MJTC_reply->useremail); ?>)
                                                    </span>
                                                    <?php
                                                }
                                            } ?>
                                            <?php
                                            if (majesticsupport::$_data[0]->uid  != $MJTC_reply->uid) {
                                                if (!empty($MJTC_reply->staffid)) { ?>
                                                    <span class="mjtc-support-staff-badge"><?php echo esc_html(__('AGENT', 'majestic-support')); ?></span>
                                                <?php } else if (is_super_admin($MJTC_reply->uid)) { ?>
                                                    <span class="mjtc-support-staff-badge"><?php echo esc_html(__('ADMIN', 'majestic-support')); ?></span>
                                                <?php } ?>
                                                <span class="mjtc-support-replid-ticket-badge" style="color: #64748b"><?php echo esc_html(__('Replied', 'majestic-support')); ?></span>
                                                <?php 
                                            } ?>
                                        </div>
                                        <?php
                                        if (in_array('agent',majesticsupport::$_active_addons) &&  majesticsupport::$_data['user_staff']) { ?>
                                            <span style="font-size: 0.85rem; color: #94a3b8;" class="mjtc-support-thread-time-stamp">
                                                <?php echo esc_html(date_i18n("l F d, Y, H:i:s", MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_reply->created))); ?>
                                            </span>
                                            <?php
                                        } ?>
                                    </div>
                                    <div class="mjtc-support-thread-data note-msg">
                                        <div class="mjtc-support-thread-data">
                                            <?php 
                                            if(in_array('timetracking', majesticsupport::$_active_addons)){
                                                if($MJTC_reply->staffid != 0){
                                                    $MJTC_hours = floor($MJTC_reply->time / 3600);
                                                    $MJTC_mins = floor($MJTC_reply->time / 60);
                                                    $MJTC_mins = floor($MJTC_mins % 60);
                                                    $MJTC_secs = floor($MJTC_reply->time % 60);
                                                    $MJTC_time = esc_html(__('Time Taken','majestic-support')).':&nbsp;'.sprintf('%02d:%02d:%02d', $MJTC_hours, $MJTC_mins, $MJTC_secs); ?>
                                                    <span class="mjtc-support-thread-time">
                                                        <?php echo esc_html($MJTC_time); ?>
                                                    </span>
                                                    <?php
                                                }
                                            }
                                            if (in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) {
                                                $MJTC_configname = 'agent';
                                            } else {
                                                $MJTC_configname = 'user';
                                            }
                                            if (majesticsupport::$_config['show_read_receipt_to_' . $MJTC_configname . '_on_reply'] == 1 && !empty($MJTC_reply->viewed_by) && (($MJTC_configname == 'user' && majesticsupport::$_data[0]->uid == $MJTC_reply->uid) || ($MJTC_configname == 'agent' && $MJTC_cur_uid == $MJTC_reply->uid))) { ?>
                                                <span class="mjtc-support-thread-read-status-wrp">
                                                    <span class="mjtc-support-thread-read-status-btn">
                                                       <svg class="mjtc-support-icon" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                                    </span>
                                                    <span class="mjtc-support-thread-read-status-detail">
                                                        <span class="mjtc-support-thread-read-status-row">
                                                            <?php 
                                                            echo '<b>'.esc_html(__('Viewed By','majestic-support').': ').'</b>';
                                                            if (majesticsupport::$_config['anonymous_name_on_ticket_reply'] == 1) {
                                                                echo esc_html(majesticsupport::$_config['title']);
                                                            }else{
                                                                echo esc_html($MJTC_reply->viewername); 
                                                            } ?>
                                                        </span>
                                                        <span class="mjtc-support-thread-read-status-row">
                                                            <?php echo esc_html(date_i18n("l F d, Y, H:i:s", MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_reply->viewed_on))); ?>
                                                        </span>
                                                    </span>
                                                </span>
                                                <?php 
                                            } ?>
                                        </div>
                                        <div class="mjtc-support-thread-data">
                                            <?php echo ($MJTC_reply->ticketviaemail == 1) ? esc_html(__('Created via Email', 'majestic-support')) : ''; ?>
                                        </div>
                                        <div class="mjtc-support-message-thread-data">
                                        <?php echo wp_kses_post(html_entity_decode($MJTC_reply->message)); ?>
                                            <?php if (!empty($MJTC_reply->attachments)) { ?>
                                                <div class="mjtc-support-attachments-wrp">
                                                    <?php
                                                    $MJTC_attachmentdata = '';
                                                    foreach ($MJTC_reply->attachments AS $MJTC_attachment) {
                                                        $MJTC_path = majesticsupport::makeUrl(array('mjsmod'=>'ticket','task'=>'downloadbyid','action'=>'mstask','id'=> $MJTC_attachment->id ,'mspageid'=>get_the_ID()));
                                                        $MJTC_data = wp_check_filetype($MJTC_attachment->filename);
                                                        $type = $MJTC_data['type'];
                                                        $MJTC_attachmentdata .= '
                                                        <div class="mjtc_supportattachment">
                                                            <span class="mjtc-support-download-file-title">
                                                                ' . esc_html($MJTC_attachment->filename) . ' ( ' . esc_html(round($MJTC_attachment->filesize,2)) . ' kb) ' . '
                                                            </span>
                                                            <a class="mjtc-download-button" target="_blank" href="' . esc_url($MJTC_path) . '">
                                                                <svg class="mjtc-support-icon" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                                            </a>';
                                                            if(MJTC_majesticsupportphplib::MJTC_strpos($type, "image") !== false) {
                                                                $MJTC_path = MJTC_includer::MJTC_getModel('attachment')->getAttachmentImage($MJTC_attachment->id);
                                                                $MJTC_attachmentdata .= '<a data-gall="gallery-'. esc_attr($MJTC_reply->replyid) .'" class="mjtc-download-button venobox" data-vbtype="image" title="'. esc_attr(__('View','majestic-support')) .'" href="'. esc_attr($MJTC_path) .'"  target="_blank">
                                                                <svg class="mjtc-support-icon" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                                                    </a>';
                                                            }
                                                            $MJTC_attachmentdata .= '
                                                        </div>';
                                                    }
                                                    $MJTC_nonce = wp_create_nonce("download-all-for-reply-".$MJTC_reply->replyid);
                                                    $MJTC_attachmentdata .= '<a class="mjtc-all-download-button" target="_blank" href="' . esc_url(majesticsupport::makeUrl(array('mjsmod'=>'ticket', 'task'=>'downloadallforreply', 'action'=>'mstask', 'downloadid'=>$MJTC_reply->replyid, 'internalid'=> majesticsupport::$_data[0]->internalid , 'mspageid'=>get_the_ID(), '_wpnonce'=> $MJTC_nonce))) . '" onclick="" target="_blank">'. esc_html(__('Download All', 'majestic-support')) . '</a>';
                                                    echo wp_kses($MJTC_attachmentdata, MJTC_ALLOWED_TAGS);
                                                    ?>
                                                </div>
                                            <?php } ?>
                                            </div>
                                            <?php
                                        if (in_array('agent',majesticsupport::$_active_addons) &&  majesticsupport::$_data['user_staff']) { ?>
                                            <div class="mjtc-support-thread-cnt-btm">
                                                <div class="mjtc-support-thread-actions">
                                                    <?php
                                                    if(MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForTask('Edit Reply') && majesticsupport::$_data[0]->status != 6){
                                                        $MJTC_nonce = wp_create_nonce('get-reply-data-by-id-'.$MJTC_reply->replyid); ?>
                                                        <a class="mjtc-support-thread-actn-btn ticket-edit-reply-button" href="#"
                                                            onclick="return showPopupAndFillValues(<?php echo esc_js($MJTC_reply->replyid);?>,1, '<?php echo esc_js($MJTC_nonce);?>')">
                                                            <svg class="mjtc-sprt-icon" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                                            <?php echo esc_html(__('Edit Reply','majestic-support'));?>
                                                        </a>
                                                        <?php
                                                    }
                                                    if(in_array('timetracking', majesticsupport::$_active_addons)){
                                                        if(MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForTask('Edit Time') && majesticsupport::$_data[0]->status != 6){
                                                            $MJTC_nonce = wp_create_nonce('get-time-by-reply-id-'.$MJTC_reply->replyid); ?>
                                                            <a class="mjtc-support-thread-actn-btn ticket-edit-time-button" href="#" onclick="return showPopupAndFillValues(<?php echo esc_js($MJTC_reply->replyid);?>,2, '<?php echo esc_js($MJTC_nonce);?>')" >
                                                                <svg class="mjtc-sprt-icon" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                                                <?php echo esc_html(__('Edit Time','majestic-support'));?>
                                                            </a>
                                                            <?php
                                                        }
                                                    } ?>
                                                </div>
                                            </div>
                                            <?php
                                            if (
                                                MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForTask('Set AI Reply Mode for Reply') &&
                                                in_array('aipoweredreply', majesticsupport::$_active_addons) && 
                                                majesticsupport::$_data[0]->uid != $MJTC_reply->uid && 
                                                $MJTC_reply->uid != 0) { ?>
                                                <div class="mjtc-support-thread-cnt-btm">
                                                    <!-- This section contains the AI Reply Feature -->
                                                    <div class="mjtc-support-ai-reply-status-wrapper">
                                                        <label for="mjtc-support-ai-reply-status-control">
                                                            <?php echo esc_html__('AI-Powered Reply Mode', 'majestic-support').':'; ?>
                                                        </label>
                                                        <div class="mjtc-support-info-icon-wrapper">
                                                            <span class="mjtc-support-info-icon" data-tooltip="<?php echo esc_attr(__("Control how this individual reply influences the AI search and response generation process for future queries.",'majestic-support')); ?>">
                                                                <img alt="<?php echo esc_attr(__('Info','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/ticket-detail/info-icon.png" />
                                                            </span>
                                                        </div>
                                                        <div id="mjtc-support-ai-reply-status-control" class="mjtc-support-segmented-control">
                                                            <button type="button" class="mjtc-support-segmented-control-option mjtc-support-default <?php echo ($MJTC_reply->aireplymode == 0) ? 'active' : ''; ?>" data-value="0" data-type="reply" data-id="<?php echo esc_attr($MJTC_reply->replyid);?>" title="<?php echo esc_attr(__("Default: reply included in all AI search queries.", "majestic-support")); ?>">
                                                                <?php echo esc_html__('Default', 'majestic-support'); ?>
                                                            </button>
                                                            <button type="button" class="mjtc-support-segmented-control-option mjtc-support-enable <?php echo ($MJTC_reply->aireplymode == 1) ? 'active' : ''; ?>" data-value="1" data-type="reply" data-id="<?php echo esc_attr($MJTC_reply->replyid);?>" title="<?php echo esc_attr(__("Enable: reply used in AI queries only when the Enable Tickets filter is active.", "majestic-support")); ?>">
                                                                <?php echo esc_html__('Enable', 'majestic-support'); ?>
                                                            </button>
                                                            <button type="button" class="mjtc-support-segmented-control-option mjtc-support-disable <?php echo ($MJTC_reply->aireplymode == 2) ? 'active' : ''; ?>" data-value="2" data-type="reply" data-id="<?php echo esc_attr($MJTC_reply->replyid); ?>">
                                                                <?php echo esc_html__('Disable', 'majestic-support'); ?>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php
                                            } ?>
                                            <?php
                                        } ?>
                                    </div>
                                </div>
                            </div>
                            <?php
                        
                    }
                } ?>

            </div>

            <!-- Saved Internal Notes Container (Hidden by default) -->
            <div id="mjtc-support-saved-notes-container" class="mjtc-support-thread" style="display: none;">
                <?php
                if (!empty(majesticsupport::$_data[6])) {
                    foreach (majesticsupport::$_data[6] AS $MJTC_note) { ?>
                        <div class="mjtc-support-thread-item">
                            <div class="mjtc-support-timeline-line"></div>
                            <div class="mjtc-support-staff-img-wrapper" style="background: #eab308; color: white;">
                                <?php
                                if (isset($MJTC_note->userid) && !empty($MJTC_note->userid)) {
                                    echo wp_kses(MJTC_get_avatar($MJTC_note->userid), MJTC_ALLOWED_TAGS);
                                } else { ?>
                                    <img alt="<?php echo esc_attr(__('Image', 'majestic-support')); ?>" class="mjtc-support-staff-img" src="<?php echo esc_url(MJTC_PLUGIN_URL) . '/includes/images/ticketmanbig.png'; ?>" />
                                    <?php
                                } ?>
                            </div>
                            <div class="mjtc-support-thread-cnt" style="background: #fefce8; border-color: #fde047;">
                                <div class="mjtc-support-thread-data-header" style="background: rgba(254, 249, 195, 0.5);">
                                    <div class="mjtc-support-internalnote-leftwrp">
                                        <strong>
                                            <?php echo !empty($MJTC_note->staffname) ? esc_html($MJTC_note->staffname) : esc_html($MJTC_note->display_name); ?>
                                        </strong>
                                        <span class="mjtc-support-staff-badge">
                                            <?php echo esc_html(__('INTERNAL', 'majestic-support')); ?>
                                        </span>
                                    </div>
                                    <div class="mjtc-support-internalnote-rightwrp">
                                        <span class="mjtc-support-thread-strttime-stamp">
                                            <?php echo esc_html(date_i18n("l F d, Y, H:i:s", MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_note->created))); ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="mjtc-support-thread-data note-msg">
                                    <?php
                                    if(in_array('timetracking', majesticsupport::$_active_addons)) {
                                        $MJTC_hours = floor($MJTC_note->usertime / 3600);
                                        $MJTC_mins = floor($MJTC_note->usertime / 60);
                                        $MJTC_mins = floor($MJTC_mins % 60);
                                        $MJTC_secs = floor($MJTC_note->usertime % 60);
                                        $MJTC_time = esc_html(__('Time Taken','majestic-support')).':&nbsp;'.sprintf('%02d:%02d:%02d', $MJTC_hours, $MJTC_mins, $MJTC_secs); ?>
                                        <span class="mjtc-support-intenal-note-thread-time">
                                            <?php echo esc_html($MJTC_time); ?>
                                        </span>
                                        <?php
                                    } ?>
                                    <?php if (isset($MJTC_note->title) && $MJTC_note->title != '') { ?>
                                        <div class="mjtc-support-rows-wrp">
                                            <div class="mjtc-support-field-value">
                                                <span class="mjtc-support-field-value-t"><?php echo esc_html($MJTC_field_array['subject']).' : '; ?></span><?php echo esc_html($MJTC_note->title); ?>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <p><?php echo wp_kses_post($MJTC_note->note); ?></p>
                                    <?php
                                    if($MJTC_note->filesize > 0 && !empty($MJTC_note->filename)){ ?>
                                        <div class="mjtc-support-attachments-wrp">
                                            <div class="mjtc_supportattachment">
                                                <span class="mjtc-support-download-file-title">
                                                    <?php
                                                        $MJTC_data =  esc_html(majesticsupport::MJTC_getVarValue($MJTC_note->filename)).'(' . esc_html(majesticsupport::MJTC_getVarValue($MJTC_note->filesize / 1024)) . ')';
                                                        echo wp_kses($MJTC_data, MJTC_ALLOWED_TAGS);
                                                    ?>
                                                </span>
                                                <a class="mjtc-download-button" target="_blank"
                                                    href="<?php echo esc_url(admin_url('?page=majesticsupport_note&action=mstask&task=downloadbyid&id='.esc_attr($MJTC_note->id))); ?>">
                                                        <svg class="mjtc-support-icon" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                                </a>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <!-- Action Buttons Group -->
                                    <div class="mjtc-support-thread-cnt-btm">
                                        <div class="mjtc-support-thread-actions">
                                            <?php
                                            if(MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForTask('Edit Internal Note') && majesticsupport::$_data[0]->status != 6){
                                                $MJTC_nonce = wp_create_nonce('get-note-data-by-id-'.$MJTC_note->id); ?>
                                                <a class="mjtc-support-thread-actn-btn ticket-edit-reply-button" href="#"
                                                    onclick="editInternalNoteData('<?php echo esc_js($MJTC_nonce);?>',<?php echo esc_js($MJTC_note->id);?>);" style="color: #854d0e;">
                                                    <svg class="mjtc-sprt-icon" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                                    <?php echo esc_html(__('Edit Internal Note','majestic-support'));?>
                                                </a>
                                                <?php
                                            }
                                            if(MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForTask('Delete Internal Note') && majesticsupport::$_data[0]->status != 6){
                                                $MJTC_nonce = wp_create_nonce('get-reply-data-by-id-'.$MJTC_note->id); ?>
                                                <a onclick="return confirm('<?php echo esc_html(__('Are you sure to Delete this internal note', 'majestic-support')); ?>');" class="mjtc-support-thread-actn-btn ticket-edit-reply-button" href="<?php echo esc_url(wp_nonce_url(majesticsupport::makeUrl(array('mjsmod'=>'ticket','task'=>'deleteInternalNote','action'=>'mstask','internalid'=> majesticsupport::$_data[0]->internalid,'ticketid'=> majesticsupport::$_data[0]->id,'internalnoteid'=> $MJTC_note->id ,'mspageid'=>get_the_ID())),'delete-internal-note-'.$MJTC_note->id)); ?>"
                                                    style="color: #854d0e;">
                                                    <svg class="mjtc-sprt-icon" viewBox="0 0 24 24"><path d="M3 6h18"></path><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                                    <?php echo esc_html(__('Delete Internal Note','majestic-support'));?>
                                                </a>
                                                <?php
                                            }
                                            if(in_array('timetracking', majesticsupport::$_active_addons)){
                                                if(MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForTask('Edit Time') && majesticsupport::$_data[0]->status != 6){
                                                    $MJTC_nonce = wp_create_nonce('get-time-by-note-id-'.$MJTC_note->id); ?>
                                                    <a href="#" class="mjtc-support-thread-actn-btn ticket-edit-time-button" title="<?php echo esc_attr(__('Edit Time', 'majestic-support')); ?>" onclick="return showPopupAndFillValues(<?php echo esc_js($MJTC_note->id);?>,3, '<?php echo esc_js($MJTC_nonce);?>')" style="color: #854d0e;">
                                                        <svg class="mjtc-sprt-icon" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                                        <?php echo esc_html(__('Edit Time','majestic-support'));?>
                                                    </a>
                                                    <?php
                                                }
                                            } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                } else { ?>
                    <div class="ms-internalnote-empty-msg">
                        <?php echo esc_html(__('No Record Found','majestic-support')); ?>
                    </div>
                    <?php
                } ?>
            </div>

            <!-- History Container (Hidden by default) -->
            <div id="mjtc-support-history-container" class="mjtc-support-thread" style="display:none;">
                <?php // data[5] holds the tickect history
                if ((!empty(majesticsupport::$_data[5]))) { ?>
                    <table class="mjtc-history-table">
                        <thead>
                            <tr>
                                <th><?php echo esc_html(__('Date','majestic-support'));?></th>
                                <th><?php echo esc_html(__('Time','majestic-support'));?></th>
                                <th><?php echo esc_html(__('Action','majestic-support'));?></th>
                                <th><?php echo esc_html(__('Details','majestic-support'));?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (majesticsupport::$_data[5] AS $MJTC_history) { ?>
                                <tr>
                                    <td data-label="Date"><?php echo esc_html(date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_history->datetime))); ?></td>
                                    <td data-label="Time"><?php echo esc_html(date_i18n('H:i:s', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_history->datetime))); ?></td>
                                    <td data-label="Action" class="mjtc-history-action">
                                        <?php echo esc_html($MJTC_history->eventtype); ?>
                                    </td>
                                    <td data-label="Details" class="mjtc-history-detail">
                                        <?php
                                            if (is_super_admin($MJTC_history->uid)) {
                                                $MJTC_message = 'admin';
                                            } elseif ( in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff($MJTC_history->uid)) {
                                                $MJTC_message = 'agent';
                                            } else {
                                                $MJTC_message = 'member';
                                            }
                                            if(in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()){ //agent
                                                echo wp_kses_post($MJTC_history->message); 
                                            }else{
                                                if($MJTC_message == 'member'){ // message by the user, so show full message to user
                                                    echo wp_kses_post($MJTC_history->message); 
                                                } else {
                                                    if (majesticsupport::$_config['anonymous_name_on_ticket_reply'] == 1) {
                                                        $MJTC_historymessage = $MJTC_history->message;
                                                        echo wp_kses_post(MJTC_majesticsupportphplib::MJTC_preg_replace("/\([^)]+\)/","( ".__("Agent","majestic-support")." )",$MJTC_historymessage));
                                                    }else{
                                                        echo wp_kses_post($MJTC_history->message); 
                                                    }
                                                }
                                            }                                           
                                        ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    <?php
                } else { ?>
                    <div class="ms-ticket-empty-msg">
                        <?php echo esc_html(__('No Record Found','majestic-support')); ?>
                    </div>
                    <?php
                } ?>
            </div>

            <!-- CREDENTIALS Tab -->
            <div id="mjtc-support-credentials-container" class="mjtc-support-thread majestice-support-credentials-container" style="display:none;">
                <!-- LIST VIEW -->
                <div class="mjtc-support-usercredentails-wrp" id="cred-list-view">
                    <div class="mjtc-support-usercredentails-credentails-wrp">
                    </div>
                    <?php
                    if(in_array('privatecredentials',majesticsupport::$_active_addons) && majesticsupport::$_data[0]->status != 5 && majesticsupport::$_data[0]->status != 6 ){
                        $MJTC_credential_add_permission = false;
                        if(in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()){
                            $MJTC_credential_add_permission = MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForTask('Add Credentials');
                        }elseif(current_user_can('manage_options')){
                            $MJTC_credential_add_permission = true;
                        }elseif (MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid() != 0) {
                            if(MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid() == majesticsupport::$_data[0]->uid){
                                $MJTC_credential_add_permission = true;
                            }
                        }elseif (MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid() == 0) {
                            $MJTC_credential_add_permission = true;
                        }
                        if($MJTC_credential_add_permission){ ?>
                            <div class="mjtc-support-usercredentail-data-add-new-button-wrap">
                                <?php $MJTC_nonce = wp_create_nonce('get-form-for-privte-credentials-'.majesticsupport::$_data[0]->id); ?>
                                <button class="mjtc-support-usercredentail-data-add-new-button" onclick="addEditCredentail('<?php echo esc_js($MJTC_nonce);?>',<?php echo esc_js(majesticsupport::$_data[0]->id);?>,'<?php echo esc_js(majesticsupport::$_data[0]->internalid);?>',<?php echo esc_js(MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid());?>);">
                                    <?php echo esc_html(__("Add New Credential",'majestic-support')); ?>
                                </button>
                            </div>
                            <?php
                        }
                    }
                    ?>
                </div>
                <!-- FORM VIEW -->
                <div class="mjtc-support-usercredentails-form-wrap" id="cred-form-view"></div>
            </div>

            <!-- Reply Area -->
            <div class="mjtc-support-reply-forms-wrapper mode-public mode-note" id="reply-container">
                <!-- Ticket Reply Forms Wrapper -->
                <?php
                if($MJTC_printflag == false){ ?>
                    <div class="mjtc-support-reply-tabs">
                        <?php
                        if (majesticsupport::$_data[0]->status != 5 && majesticsupport::$_data[0]->status != 6) { ?>
                            <button class="mjtc-support-reply-tab-btn active" id="mjtc-reply-tab" data-mode="public">
                                <svg class="mjtc-support-icon" viewBox="0 0 24 24"><path d="M10 9V5l-7 7 7 7v-4.1c5 0 8.5 1.6 11 5.1-1-5-4-10-11-11z"/></svg> <?php echo esc_html(__('Public Reply', 'majestic-support')); ?>
                            </button>
                            <?php
                        }
                        if (in_array('agent',majesticsupport::$_active_addons) && majesticsupport::$_data['user_staff']) {
                            if (in_array('note', majesticsupport::$_active_addons)) { ?>
                                <button class="mjtc-support-reply-tab-btn active" data-mode="note" id="mjtc-note-tab" style="display: none;">
                                    <svg class="mjtc-support-icon" viewBox="0 0 24 24"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/></svg> <?php echo esc_html(__('Internal Note', 'majestic-support')); ?>
                                </button>
                                <?php
                            }
                        } ?>
                    </div>
                    <?php
                    if (majesticsupport::$_data[0]->status != 5 && majesticsupport::$_data[0]->status != 6) {
                        $MJTC_id = 'mjtc-public-reply-wrp';
                    } else {
                        $MJTC_id = '';
                    } ?>
                    <div id="<?php echo esc_attr($MJTC_id); ?>">
                        <!-- revise--move this check to the start before the actions if cause any issue -->
                        <?php
                        if (in_array('agent',majesticsupport::$_active_addons) && majesticsupport::$_data['user_staff']) {
                            $MJTC_link = wp_nonce_url(majesticsupport::makeUrl(array('mjsmod'=>'ticket','task'=>'actionticket')),"action-ticket-");
                        } else {
                            $MJTC_link = wp_nonce_url(majesticsupport::makeUrl(array('mjsmod'=>'reply','task'=>'savereply')),"save-reply-".majesticsupport::$_data[0]->id);
                        }
                        if($MJTC_printflag != true) { ?>
                            <form method="post" action="<?php echo esc_url($MJTC_link); ?>" id="adminTicketform"
                            enctype="multipart/form-data" class="123">
                        <?php } ?>
                        <!-- revise -->
                        <?php
                        if (!majesticsupport::$_data['user_staff']) {
                            if (majesticsupport::$_data[0]->status != 5 && majesticsupport::$_data[0]->lock != 1 && majesticsupport::$_data[0]->status != 6): ?>
                                <div id="postreply" class="mjtc-support-post-reply">
                                    <div class="mjtc-support-reply-field-wrp">
                                        <div class="mjtc-support-reply-field">
                                            <?php wp_editor('', 'mjsupport_message', array('media_buttons' => false, 'textarea_rows' => '4')); ?>
                                        </div>
                                    </div>
                                    <?php
                                    if (!empty($MJTC_field_array['attachments'])) { ?>
                                        <div class="mjtc-support-reply-attachments">
                                            <!-- Attachments -->
                                            <div class="mjtc-attachment-field-title">
                                                <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field_array['attachments'])); ?>
                                            </div>
                                            <div class="mjtc-attachment-field">
                                                <div class="tk_attachment_value_wrapperform tk_attachment_user_reply_wrapper">
                                                    <span class="tk_attachment_value_text">
                                                        <input type="file" class="inputbox mjtc-attachment-inputbox" name="filename[]" onchange="MJTC_uploadfile(this, '<?php echo esc_js(majesticsupport::$_config['file_maximum_size']); ?>', '<?php echo esc_js(majesticsupport::$_config['file_extension']); ?>');" size="20" />
                                                        <span class='tk_attachment_remove'></span>
                                                    </span>
                                                </div>
                                                <span class="tk_attachments_configform">
                                                    <?php
                                                    $MJTC_data = esc_html(__('Maximum File Size', 'majestic-support')).' (' . esc_html(majesticsupport::$_config['file_maximum_size']).' KB)<br>'.esc_html(__('File Extension Type', 'majestic-support')).' (' . esc_html(majesticsupport::$_config['file_extension']) . ')';
                                                    echo wp_kses($MJTC_data, MJTC_ALLOWED_TAGS);
                                                    ?>
                                                </span>
                                                <span id="tk_attachment_add" data-ident="tk_attachment_user_reply_wrapper" class="tk_attachments_addform"><?php echo esc_html(__('Add more', 'majestic-support')); ?></span>
                                            </div>
                                        </div>
                                        <?php
                                    }?>
                                </div>
                                <div class="mjtc-support-closeonreply-wrp">
                                    <div class="mjtc-support-closeonreply-title">
                                        <?php echo esc_html(__('Ticket Status', 'majestic-support')); ?></div>
                                    <div class="mjtc-replyFormStatus mjtc-form-title-position-reletive-left">
                                        <?php echo wp_kses(MJTC_formfield::MJTC_checkbox('closeonreply', array('1' => esc_html(__('Close On Reply', 'majestic-support'))), '', array('class' => 'radiobutton mjtc-support-closeonreply-checkbox')), MJTC_ALLOWED_TAGS); ?>
                                    </div>
                                </div>
                                <div class="mjtc-support-reply-form-button-wrp">
                                    <?php echo wp_kses(MJTC_formfield::MJTC_submitbutton('postreplybutton', esc_html(__('Post Reply', 'majestic-support')), array('class' => 'button mjtc-support-save-button', 'onclick' => "return checktinymcebyid('message');")), MJTC_ALLOWED_TAGS); ?>
                                </div>
                            <?php endif; ?>
                            <?php echo wp_kses(MJTC_formfield::MJTC_hidden('actionid', ''), MJTC_ALLOWED_TAGS); ?>
                            <?php echo wp_kses(MJTC_formfield::MJTC_hidden('priority', ''), MJTC_ALLOWED_TAGS); ?>
                            <?php echo wp_kses(MJTC_formfield::MJTC_hidden('ticketid', majesticsupport::$_data[0]->id), MJTC_ALLOWED_TAGS); ?>
                            <?php echo wp_kses(MJTC_formfield::MJTC_hidden('internalid', majesticsupport::$_data[0]->internalid), MJTC_ALLOWED_TAGS); ?>
                            <?php echo wp_kses(MJTC_formfield::MJTC_hidden('created', majesticsupport::$_data[0]->created), MJTC_ALLOWED_TAGS); ?>
                            <?php echo wp_kses(MJTC_formfield::MJTC_hidden('uid', MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid()), MJTC_ALLOWED_TAGS); ?>
                            <?php echo wp_kses(MJTC_formfield::MJTC_hidden('ticketrandomid', majesticsupport::$_data[0]->ticketid), MJTC_ALLOWED_TAGS); ?>
                            <?php echo wp_kses(MJTC_formfield::MJTC_hidden('hash', majesticsupport::$_data[0]->hash), MJTC_ALLOWED_TAGS); ?>
                            <?php echo wp_kses(MJTC_formfield::MJTC_hidden('updated', majesticsupport::$_data[0]->updated), MJTC_ALLOWED_TAGS); ?>
                            <?php echo wp_kses(MJTC_formfield::MJTC_hidden('form_request', 'majesticsupport'), MJTC_ALLOWED_TAGS); ?>
                            <?php echo wp_kses(MJTC_formfield::MJTC_hidden('mspageid', get_the_ID()), MJTC_ALLOWED_TAGS); ?>
                            
                            </form>
                            <?php
                        } else { ?>
                            <?php echo wp_kses(MJTC_formfield::MJTC_hidden('actionid', ''), MJTC_ALLOWED_TAGS); ?>
                            <?php echo wp_kses(MJTC_formfield::MJTC_hidden('priority', ''), MJTC_ALLOWED_TAGS); ?>
                            <?php echo wp_kses(MJTC_formfield::MJTC_hidden('ticketid', majesticsupport::$_data[0]->id), MJTC_ALLOWED_TAGS); ?>
                            <?php echo wp_kses(MJTC_formfield::MJTC_hidden('internalid', majesticsupport::$_data[0]->internalid), MJTC_ALLOWED_TAGS); ?>
                            <?php echo wp_kses(MJTC_formfield::MJTC_hidden('created', majesticsupport::$_data[0]->created), MJTC_ALLOWED_TAGS); ?>
                            <?php echo wp_kses(MJTC_formfield::MJTC_hidden('uid', MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid()), MJTC_ALLOWED_TAGS); ?>
                            <?php echo wp_kses(MJTC_formfield::MJTC_hidden('updated', majesticsupport::$_data[0]->updated), MJTC_ALLOWED_TAGS); ?>
                            <?php echo wp_kses(MJTC_formfield::MJTC_hidden('form_request', 'majesticsupport'), MJTC_ALLOWED_TAGS); ?>
                            <?php echo wp_kses(MJTC_formfield::MJTC_hidden('mspageid', get_the_ID()), MJTC_ALLOWED_TAGS); ?>
                            
                            </form>
                            <?php
                            if (majesticsupport::$_data[0]->status != 5 && majesticsupport::$_data[0]->status != 6) { ?>
                                <div id="postreply" class="mjtc-det-tkt-rply-frm">
                                    <!-- Post Reply Area -->
                                    <form class="mjtc-det-tkt-form" method="post" action="<?php echo esc_url(wp_nonce_url(majesticsupport::makeUrl(array('mjsmod'=>'reply','task'=>'savereply')),"save-reply-".majesticsupport::$_data[0]->id)); ?>"
                                        enctype="multipart/form-data">
                                        <div class="mjtc-det-tkt-form-fields-wrp">
<?php if(in_array('cannedresponses', majesticsupport::$_active_addons) && isset($MJTC_field_array['premade'])){ ?>
    <div class="mjtc-support-premade-msg-wrp">
        <!-- Premade Message Wrapper -->
        <div class="mjtc-support-premade-field-title">
            <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field_array['premade'])); ?>&nbsp;<?php echo esc_html(__('Message', 'majestic-support')); ?>
        </div>
        <div class="mjtc-support-premade-field-wrp">
            <?php echo wp_kses(MJTC_formfield::MJTC_select('premadeid', MJTC_includer::MJTC_getModel('cannedresponses')->getPreMadeMessageForCombobox(), isset(majesticsupport::$_data[0]->premadeid) ? majesticsupport::$_data[0]->premadeid : '', esc_html(__('Select', 'majestic-support').' '.majesticsupport::MJTC_getVarValue($MJTC_field_array['premade'])), array('class' => 'mjtc-support-premade-select', 'onchange' => 'getpremade(this.value);')), MJTC_ALLOWED_TAGS); ?>
            <span class="mjtc-support-apend-radio-btn">
                <?php echo wp_kses(MJTC_formfield::MJTC_checkbox('append_premade', array('1' => esc_html(__('Append', 'majestic-support'))), '', array('class' => 'radiobutton mjtc-support-premade-radiobtn')), MJTC_ALLOWED_TAGS); ?>
            </span>
        </div>
    </div>
    <?php
}
                                            if (MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForTask('Use AI Powered Reply Feature')) { ?>
                                                <span style="display: none;" class="mjtc-support-current-ticket-title"><?php echo esc_html(majesticsupport::$_data[0]->subject) ?></span>
                                                <span style="display: none;" class="mjtc-support-current-ticket-id"><?php echo esc_html(majesticsupport::$_data[0]->id) ?></span>
                                                <button id="mjtc-support-ai-reply-btn" class="mjtc-sprt-det-actn-btn mjtc-support-aisuggestions-btn" title="<?php echo esc_attr(__('AI Suggestions', 'majestic-support')); ?>">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"></rect><path d="M9 12h6"></path><path d="M12 9v6"></path></svg>
                                                    <?php echo esc_html(__('Local AI Suggestions', 'majestic-support')); ?>
                                                </button>
                                                <?php
                                            } ?>
                                            <button style="display: none;" type="button" class="mjtc-ai-suggestion-btn-live">
                                                <div class="mjtc-live-indicator"></div>
                                                <?php echo esc_html(__('Live AI Suggestion', 'majestic-support')); ?>
                                            </button>
                                            <?php
                                            if(in_array('timetracking', majesticsupport::$_active_addons)) { ?>
                                                <div class="mjtc-timer-display-boxwrp">
                                                    <div class="mjtc-timer-controls-wrp">
                                                        <div class="timer">
                                                            00:00:00
                                                        </div>
                                                    </div>
                                                    <div class="mjtc-timer-buttons-wrp">
                                                        <?php if(MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForTask('Edit Time')){ ?>
                                                            <span class="mjtc-support-timer-btn" title="<?php echo esc_attr(__('Edit Log', 'majestic-support')); ?>" onclick="showEditTimerPopup()">
                                                                <svg class="mjtc-support-icon" style="color: #64748b;" viewBox="0 0 24 24"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
                                                            </span>
                                                        <?php } ?>
                                                        <span class="mjtc-support-timer-btn cls_1" onclick="changeTimerStatus(1)" title="<?php echo esc_attr(__('Start', 'majestic-support')); ?>">
                                                            <svg class="mjtc-support-icon" style="color: #10b981;" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                                        </span>
                                                        <span class="mjtc-support-timer-btn cls_2" onclick="changeTimerStatus(2)" title="<?php echo esc_attr(__('Pause', 'majestic-support')); ?>">
                                                            <svg class="mjtc-support-icon" style="color: #f59e0b;" viewBox="0 0 24 24"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>
                                                        </span>
                                                        <span class="mjtc-support-timer-btn cls_3" onclick="changeTimerStatus(3)" title="<?php echo esc_attr(__('Stop', 'majestic-support')); ?>">
                                                            <svg class="mjtc-support-icon" style="color: #ef4444;" viewBox="0 0 24 24"><path d="M6 6h12v12H6z"/></svg>
                                                        </span>
                                                    </div>
                                                </div>
                                                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('timer_time_in_seconds',''), MJTC_ALLOWED_TAGS); ?>
                                                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('timer_edit_desc',''), MJTC_ALLOWED_TAGS); ?>
                                                <?php
                                            }?>
                                        </div>
                                        <!-- AI Suggestions Panel (Hidden by default) -->
                                        <div class="mjtc-support-container" id="mjtc-ai-suggestions-panel">
                                            <div id="mjtc-support-matching-tickets-section" class="mjtc-ai-inner-panel">
                                                <div class="mjtc-ai-panel-header">
                                                    <div class="mjtc-ai-panel-title">
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                                            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z">
                                                            </path>
                                                            <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                                                            <line x1="12" y1="22.08" x2="12" y2="12"></line>
                                                        </svg>
                                                        <?php echo esc_html__('AI Analysis & Suggestions', 'majestic-support'); ?>
                                                    </div>
                                                    <?php if(in_array('aipoweredreply', majesticsupport::$_active_addons)){ ?>
                                                        <div class="mjtc-support-filter-group">
                                                            <label style="display: none;" for="mjtc-support-tickets-filter" class="mjtc-support-filter-label"><?php echo esc_html__('Filter', 'majestic-support').': '; ?></label>
                                                            <select id="mjtc-support-tickets-filter" class="mjtc-support-filter-select">
                                                                <option value="all"><?php echo esc_html__('All Tickets', 'majestic-support'); ?></option>
                                                                <option value="marked"><?php echo esc_html__('Enable Tickets', 'majestic-support'); ?></option>
                                                            </select>
                                                        </div>
                                                    <?php } ?>
                                                    <button type="button" id="mjtc-support-close-tickets-btn" class="mjtc-ai-panel-close">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                            <line x1="18" y1="6" x2="6" y2="18"></line>
                                                            <line x1="6" y1="6" x2="18" y2="18"></line>
                                                        </svg>
                                                        <?php echo esc_html__('Close', 'majestic-support'); ?>
                                                    </button>
                                                </div>
                                                <div id="mjtc-support-matching-tickets-list" class="mjtc-support-list mjtc-ai-cards-container">
                                                    
                                                </div>
                                            </div>

                                            <div id="mjtc-support-selected-ticket-replies-section" class="mjtc-support-section mjtc-support-selected-replies-section mjtc-ai-inner-panel">
                                                <div class="mjtc-ai-panel-header">
                                                    <h2 class="mjtc-ai-panel-title" id="mjtc-support-selected-ticket-replies-title"></h2>
                                                    <?php if(in_array('aipoweredreply', majesticsupport::$_active_addons)){ ?>
                                                        <div class="mjtc-support-filter-group">
                                                            <label style="display: none;" for="mjtc-support-replies-filter" class="mjtc-support-filter-label"><?php echo esc_html__('Filter', 'majestic-support').': '; ?></label>
                                                            <select id="mjtc-support-replies-filter" class="mjtc-support-filter-select">
                                                                <option value="all"><?php echo esc_html__('All Replies', 'majestic-support'); ?></option>
                                                                <option value="marked"><?php echo esc_html__('Enable Replies', 'majestic-support'); ?></option>
                                                            </select>
                                                        </div>
                                                    <?php } ?>
                                                    <button type="button" id="mjtc-support-close-replies-btn" class="mjtc-ai-panel-close">
                                                        <?php echo esc_html__('Close', 'majestic-support'); ?>
                                                    </button>
                                                </div>
                                                <div id="mjtc-support-selected-ticket-replies-content" class="mjtc-ai-cards-container mjtc-support-list mjtc-support-replies-content reply-content">
                                                    
                                                </div>
                                            </div>

                                            <!-- Custom Modal for Messages -->
                                            <div id="mjtc-support-message-modal" class="mjtc-support-modal mjtc-support-hidden">
                                                <div class="mjtc-support-modal-content">
                                                    <p id="mjtc-support-modal-message" class="mjtc-support-modal-message"></p>
                                                    <button id="mjtc-support-modal-close-btn" class="mjtc-support-modal-close-button">
                                                        <?php echo esc_html__('OK', 'majestic-support'); ?>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mjtc-support-text-editor-wrp">
                                            <div class="mjtc-support-text-editor-field-title">
                                                <?php echo esc_html(__('Type Message', 'majestic-support')); ?>
                                            </div>
                                            <div class="mjtc-support-text-editor-field">
                                                <?php wp_editor('', 'mjsupport_message', array('media_buttons' => false)); ?>
                                            </div>
                                        </div>
                                        <?php
                                        if(isset($MJTC_field_array['attachments'])){ ?>
                                            <div class="mjtc-support-reply-attachments">
                                                <!-- Attachments -->
                                                <div class="mjtc-attachment-field-title">
                                                    <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field_array['attachments'])); ?></div>
                                                <div class="mjtc-attachment-field">
                                                    <div class="tk_attachment_value_wrapperform tk_attachment_staff_reply_wrapper">
                                                        <span class="tk_attachment_value_text">
                                                            <input type="file" class="inputbox mjtc-attachment-inputbox" name="filename[]"
                                                                onchange="MJTC_uploadfile(this, '<?php echo esc_js(majesticsupport::$_config['file_maximum_size']); ?>', '<?php echo esc_js(majesticsupport::$_config['file_extension']); ?>');"
                                                                size="20" />
                                                            <span class='tk_attachment_remove'></span>
                                                        </span>
                                                    </div>
                                                    <span class="tk_attachments_configform">
                                                        <?php
                                                            $MJTC_data = esc_html(__('Maximum File Size', 'majestic-support')).' (' . esc_html(majesticsupport::$_config['file_maximum_size']).' KB)<br>'.esc_html(__('File Extension Type', 'majestic-support')).' (' . esc_html(majesticsupport::$_config['file_extension']) . ')';
                                                            echo wp_kses($MJTC_data, MJTC_ALLOWED_TAGS);
                                                        ?>
                                                    </span>
                                                    <span id="tk_attachment_add" data-ident="tk_attachment_staff_reply_wrapper"
                                                        class="tk_attachments_addform" title="<?php echo esc_attr(__('Add More', 'majestic-support')); ?>"><?php echo esc_html(__('Add more', 'majestic-support')); ?></span>
                                                </div>
                                            </div>
                                            <?php
                                        } ?>
                                        <div class="mjtc-support-append-signature-wrp">
                                            <!-- Append Signature -->
                                            <div class="mjtc-support-append-field-title">
                                                <?php echo esc_html(__('Append Signature', 'majestic-support')); ?>
                                            </div>
                                            <div class="mjtc-support-append-field-wrp">
                                                <div class="mjtc-support-signature-radio-box">
                                                    <?php echo wp_kses(MJTC_formfield::MJTC_checkbox('ownsignature', array('1' => esc_html(__('Own Signature', 'majestic-support'))), '', array('class' => 'radiobutton mjtc-support-append-radio-btn')), MJTC_ALLOWED_TAGS); ?>
                                                </div>
                                                <?php
                                                if (!empty($MJTC_field_array['department'])) { ?>
                                                   <div class="mjtc-support-signature-radio-box">
                                                        <?php echo wp_kses(MJTC_formfield::MJTC_checkbox('departmentsignature', array('1' => esc_html(majesticsupport::MJTC_getVarValue($MJTC_field_array['department'])) ." ". esc_html(__('Signature', 'majestic-support'))), '', array('class' => 'radiobutton mjtc-support-append-radio-btn')), MJTC_ALLOWED_TAGS); ?>
                                                    </div>
                                                    <?php
                                                } ?>
                                                <div class="mjtc-support-signature-radio-box">
                                                    <?php echo wp_kses(MJTC_formfield::MJTC_checkbox('nonesignature', array('1' => esc_html(__('None', 'majestic-support'))), '', array('class' => 'radiobutton mjtc-support-append-radio-btn')), MJTC_ALLOWED_TAGS); ?>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                        if(in_array('agent',majesticsupport::$_active_addons) && isset($MJTC_field_array['assignto']) && MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForTask('Assign Ticket To Agent')){
                                            $MJTC_staffid = MJTC_includer::MJTC_getModel('agent')->getStaffId(MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid());
                                            if (majesticsupport::$_data[0]->staffid != $MJTC_staffid) {
                                            ?>
                                                <div class="mjtc-support-assigntome-wrp">
                                                    <div class="mjtc-support-assigntome-field-title">
                                                        <?php echo esc_html(__('Assign Ticket', 'majestic-support')); ?>
                                                    </div>
                                                    <div class="mjtc-support-assigntome-field-wrp">
                                                        <?php
                                                            if(majesticsupport::$_data[0]->staffid){
                                                                $MJTC_checked = '';
                                                            }else{
                                                                $MJTC_checked = 1;
                                                            }
                                                            echo wp_kses(MJTC_formfield::MJTC_checkbox('assigntome', array('1' => esc_html(__('Assign To Me', 'majestic-support'))), $MJTC_checked, array('class' => 'radiobutton mjtc-support-assigntome-checkbox')), MJTC_ALLOWED_TAGS);
                                                        ?>
                                                    </div>
                                                </div><!-- Assign to me -->
                                                <?php
                                            }
                                        } ?>
                                        <div class="mjtc-support-closeonreply-wrp">
                                            <div class="mjtc-support-closeonreply-title">
                                                <?php echo esc_html(__('Ticket Status', 'majestic-support')); ?>
                                            </div>
                                            <div class="mjtc-replyFormStatus mjtc-form-title-position-reletive-left">
                                                <?php echo wp_kses(MJTC_formfield::MJTC_checkbox('closeonreply', array('1' => esc_html(__('Close On Reply', 'majestic-support'))), '', array('class' => 'radiobutton mjtc-support-closeonreply-checkbox')), MJTC_ALLOWED_TAGS); ?>
                                            </div>
                                        </div>
                                        <div class="mjtc-support-reply-form-button-wrp">
                                            <?php echo wp_kses(MJTC_formfield::MJTC_submitbutton('postreply', esc_html(__('Post Reply', 'majestic-support')), array('class' => 'button mjtc-support-save-button', 'onclick' => "return checktinymcebyid('message');")), MJTC_ALLOWED_TAGS); ?>
                                        </div>
                                        <?php echo wp_kses(MJTC_formfield::MJTC_hidden('departmentid', majesticsupport::$_data[0]->departmentid), MJTC_ALLOWED_TAGS); ?>
                                        <?php echo wp_kses(MJTC_formfield::MJTC_hidden('ticketid', majesticsupport::$_data[0]->id), MJTC_ALLOWED_TAGS); ?>
                                        <?php echo wp_kses(MJTC_formfield::MJTC_hidden('internalid', majesticsupport::$_data[0]->internalid), MJTC_ALLOWED_TAGS); ?>
                                        <?php echo wp_kses(MJTC_formfield::MJTC_hidden('uid', MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid()), MJTC_ALLOWED_TAGS); ?>
                                        <?php echo wp_kses(MJTC_formfield::MJTC_hidden('ticketrandomid',majesticsupport::$_data[0]->ticketid), MJTC_ALLOWED_TAGS); ?>
                                        <?php echo wp_kses(MJTC_formfield::MJTC_hidden('hash', majesticsupport::$_data[0]->hash), MJTC_ALLOWED_TAGS); ?>
                                        <?php echo wp_kses(MJTC_formfield::MJTC_hidden('action', 'reply_savereply'), MJTC_ALLOWED_TAGS); ?>
                                        <?php echo wp_kses(MJTC_formfield::MJTC_hidden('form_request', 'majesticsupport'), MJTC_ALLOWED_TAGS); ?>
                                        <?php echo wp_kses(MJTC_formfield::MJTC_hidden('mspageid', get_the_ID()), MJTC_ALLOWED_TAGS); ?>
                                    </form>
                                </div>
                                <?php
                            }
                        }?>
                    </div>
                    <?php
                    if(in_array('note',majesticsupport::$_active_addons)){ ?>
                        <div id="mjtc-private-note-wrp" class="visible-mjtc-private-note-wrp">
                            <form method="post" action="<?php echo esc_url(wp_nonce_url(majesticsupport::makeUrl(array('mjsmod'=>'note','task'=>'savenote')),"save-note-".majesticsupport::$_data[0]->id)); ?>"
                                enctype="multipart/form-data">
                                <div class="mjtc-support-internalnote-wrp">
                                    <div class="mjtc-support-internalnote-timer-wrp">
                                        <?php
                                        if(in_array('timetracking', majesticsupport::$_active_addons)) { ?>
                                            <div class="mjtc-timer-display-boxwrp">
                                                <div class="mjtc-timer-controls-wrp">
                                                    <div class="timer">
                                                        00:00:00
                                                    </div>
                                                </div>
                                                <div class="mjtc-timer-buttons-wrp">
                                                    <?php if(MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForTask('Edit Time')){ ?>
                                                        <span class="mjtc-support-timer-btn" title="<?php echo esc_attr(__('Edit Log', 'majestic-support')); ?>" onclick="showEditTimerPopup()">
                                                            <svg class="mjtc-support-icon" style="color: #64748b;" viewBox="0 0 24 24"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
                                                        </span>
                                                    <?php } ?>
                                                    <span class="mjtc-support-timer-btn cls_1" onclick="changeTimerStatus(1)" title="<?php echo esc_attr(__('Start', 'majestic-support')); ?>">
                                                        <svg class="mjtc-support-icon" style="color: #10b981;" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                                    </span>
                                                    <span class="mjtc-support-timer-btn cls_2" onclick="changeTimerStatus(2)" title="<?php echo esc_attr(__('Pause', 'majestic-support')); ?>">
                                                        <svg class="mjtc-support-icon" style="color: #f59e0b;" viewBox="0 0 24 24"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>
                                                    </span>
                                                    <span class="mjtc-support-timer-btn cls_3" onclick="changeTimerStatus(3)" title="<?php echo esc_attr(__('Stop', 'majestic-support')); ?>">
                                                        <svg class="mjtc-support-icon" style="color: #ef4444;" viewBox="0 0 24 24"><path d="M6 6h12v12H6z"/></svg>
                                                    </span>
                                                </div>
                                            </div>
                                            <?php echo wp_kses(MJTC_formfield::MJTC_hidden('timer_time_in_seconds',''), MJTC_ALLOWED_TAGS); ?>
                                            <?php echo wp_kses(MJTC_formfield::MJTC_hidden('timer_edit_desc',''), MJTC_ALLOWED_TAGS); ?>
                                            <?php
                                        } ?>
                                    </div>
                                    <!-- Ticket Tittle -->
                                    <div class="mjtc-support-internalnote-field-title">
                                        <?php echo esc_html(__('Title', 'majestic-support')); ?>
                                    </div>
                                    <div class="mjtc-support-internalnote-field-wrp">
                                        <?php echo wp_kses(MJTC_formfield::MJTC_text('internalnotetitle', '', array('class' => 'inputbox mjtc-support-internalnote-input')), MJTC_ALLOWED_TAGS) ?>
                                    </div>
                                </div>
                                <div class="mjtc-support-text-editor-wrp">
                                    <div class="mjtc-support-text-editor-field-title">
                                        <?php echo esc_html(__('Type Internal Note', 'majestic-support')); ?></div>
                                    <div class="mjtc-support-text-editor-field">
                                        <?php wp_editor('', 'internalnote', array('media_buttons' => false)); ?>
                                    </div>
                                </div>
                                <?php
                                if(!empty($MJTC_field_array['attachments'])){?>
                                    <div class="mjtc-support-reply-attachments">
                                        <!-- Attachments -->
                                        <div class="mjtc-attachment-field-title">
                                            <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field_array['attachments'])); ?></div>
                                        <div class="mjtc-attachment-field">
                                            <div class="tk_attachment_value_wrapperform tk_attachment_staff_reply_wrapper">
                                                <span class="tk_attachment_value_text">
                                                    <input type="file" class="inputbox mjtc-attachment-inputbox"
                                                        name="note_attachment"
                                                        onchange="MJTC_uploadfile(this, '<?php echo esc_js(majesticsupport::$_config['file_maximum_size']); ?>', '<?php echo esc_js(majesticsupport::$_config['file_extension']); ?>');"
                                                        size="20" />
                                                    <span class='tk_attachment_remove'></span>
                                                </span>
                                            </div>
                                            <span class="tk_attachments_configform">
                                                <?php
                                                    $MJTC_data = esc_html(__('Maximum File Size', 'majestic-support')).' (' . esc_html(majesticsupport::$_config['file_maximum_size']).' KB)<br>'.esc_html(__('File Extension Type', 'majestic-support')).' (' . esc_html(majesticsupport::$_config['file_extension']) . ')';
                                                    echo wp_kses($MJTC_data, MJTC_ALLOWED_TAGS);
                                                ?>
                                            </span>
                                        </div>
                                    </div>
                                    <?php
                                } ?>
                                <div class="mjtc-support-closeonreply-wrp">
                                    <div class="mjtc-support-closeonreply-title">
                                        <?php echo esc_html(__('Ticket Status', 'majestic-support')); ?>
                                    </div>
                                    <div class="mjtc-replyFormStatus mjtc-form-title-position-reletive-left">
                                        <?php echo wp_kses(MJTC_formfield::MJTC_checkbox('closeonreply', array('1' => esc_html(__('Close On Reply', 'majestic-support'))), '', array('class' => 'radiobutton mjtc-support-closeonreply-checkbox')), MJTC_ALLOWED_TAGS); ?>
                                    </div>
                                </div>
                                <div class="mjtc-support-reply-form-button-wrp">
                                    <?php echo wp_kses(MJTC_formfield::MJTC_submitbutton('postinternalnote', esc_html(__('Post Internal Note', 'majestic-support')), array('class' => 'button mjtc-support-save-button', 'onclick' => "return checktinymcebyid('internalnote');")), MJTC_ALLOWED_TAGS); ?>
                                </div>

                                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('ticketid', majesticsupport::$_data[0]->id), MJTC_ALLOWED_TAGS); ?>
                                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('uid', MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid()), MJTC_ALLOWED_TAGS); ?>
                                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('action', 'note_savenote'), MJTC_ALLOWED_TAGS); ?>
                                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('form_request', 'majesticsupport'), MJTC_ALLOWED_TAGS); ?>
                                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('mspageid', get_the_ID()), MJTC_ALLOWED_TAGS); ?>
                            </form>
                        </div>
                        <?php
                    }
                } ?>
            </div>
        </main>

        <!-- Right Sidebar (mjtc-sprt-det-right) -->
        <aside class="mjtc-sprt-det-right">
            <!-- Customer Profile -->
            <div class="mjtc-sprt-det-cnt" style="background: #fff;">
                <div class="mjtc-support-profile-row">
                    <div class="mjtc-support-profile-avatar" style="width: 48px; height: 48px; font-size: 1.1rem; border: 2px solid #e2e8f0;">
                        <?php
                        echo wp_kses(MJTC_get_avatar(majesticsupport::$_data[0]->uid, 'mjtc-support-staff-img'), MJTC_ALLOWED_TAGS); ?>
                    </div>
                    <div class="mjtc-support-profile-userinfo">
                        <?php
                        if(!empty($MJTC_field_array['fullname'])) { ?>
                            <div class="mjtc-sprt-det-user-data name" style="font-weight: 700; font-size: 1rem; color: #1e293b;">
                                <?php echo esc_html(majesticsupport::MJTC_getVarValue(majesticsupport::$_data[0]->name)); ?>
                            </div>
                            <?php
                        }
                        ?>
                        <div class="mjtc-support-profile-company">
                            <?php echo esc_html(majesticsupport::MJTC_getVarValue(majesticsupport::$_data[0]->userlogin)); ?>
                        </div>
                    </div>
                </div>
                    
                <!-- Contact Details -->
                <div class="mjtc-support-profile-contact-details">
                    <?php
                    if (!empty($MJTC_field_array['email'])) { ?>
                        <div class="mjtc-support-profile-contact-item">
                            <svg class="mjtc-support-icon" style="color: #94a3b8;" viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
                            <a href="#" class="mjtc-support-profile-contact-link">
                                <?php echo esc_html(majesticsupport::$_data[0]->email); ?>
                            </a>
                        </div>
                        <?php
                    }
                    if (!empty($MJTC_field_array['phone'])) { ?>
                        <div class="mjtc-support-profile-contact-item">
                            <svg class="mjtc-support-icon" style="color: #94a3b8;" viewBox="0 0 24 24"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/></svg>
                            <?php echo esc_html(majesticsupport::$_data[0]->phone); ?>
                        </div>
                        <?php
                    } ?>
                </div>

                <!-- Stats Grid -->
                <!-- Ticket Detail View Btn -->
                <?php
                if(isset(majesticsupport::$_data['nticket'])){
                    if(in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()){
                        $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'agent','mjslay'=>'staffmyticket','uid'=>majesticsupport::$_data[0]->uid));
                    } else {
                        $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'ticket','mjslay'=>'myticket'));
                    }
                    ?>
                    <div class="mjtc-support-stats-wrp">
                        <a class="mjtc-sprt-det-other-tkt-btn" href="<?php echo esc_url($MJTC_url); ?>" data-tab-number="1">
                            <div class="mjtc-support-stat-box">
                                <span class="mjtc-support-stat-val" style="color: #16a34a;">
                                    <?php echo esc_html(majesticsupport::$_data['activeticket']); ?>
                                </span>
                                <span class="mjtc-support-stat-label">
                                    <?php echo esc_html(__('Active','majestic-support')); ?>
                                </span>
                            </div>
                        </a>
                        <a class="mjtc-sprt-det-other-tkt-btn" href="<?php echo esc_url($MJTC_url); ?>" data-tab-number="4">
                            <div class="mjtc-support-stat-box">
                                <span class="mjtc-support-stat-val">
                                    <?php echo esc_html(majesticsupport::$_data['nticket']); ?>
                                </span>
                                <span class="mjtc-support-stat-label">
                                    <?php echo esc_html(__('All Tickets','majestic-support')); ?>
                                </span>
                            </div>
                        </a>
                        <a class="mjtc-sprt-det-other-tkt-btn" href="<?php echo esc_url($MJTC_url); ?>" data-tab-number="4">
                            <div class="mjtc-support-stat-box">
                                <span class="mjtc-support-stat-val">
                                    <?php echo esc_html(majesticsupport::$_data['nticket']); ?>
                                </span>
                                <span class="mjtc-support-stat-label">
                                    <?php echo esc_html(__('Pending','majestic-support')); ?>
                                </span>
                            </div>
                        </a>
                    </div>
                    <?php
                } ?>
            </div>

            <!-- Quick Actions -->
            <?php
            if ($MJTC_printflag == false){
                if (in_array('agent',majesticsupport::$_active_addons) && majesticsupport::$_data['user_staff'] && majesticsupport::$_data[0]->status != 6) { ?>
                    <div class="mjtc-sprt-det-cnt mjtc-support-sidebar-bg">
                        <div class="mjtc-sprt-det-hdg">
                            <?php echo esc_html(__('Quick Actions', 'majestic-support')); ?>
                        </div> <!-- Added Header -->
                        <div class="mjtc-sidebar-btn-grid">
                            <?php
                            $MJTC_mergepermission = MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForTask('Ticket Merge'); ?>
                            <?php
                            if(in_array('mergeticket',majesticsupport::$_active_addons) && $MJTC_mergepermission) {
                                if (majesticsupport::$_data[0]->status != 5 && majesticsupport::$_data[0]->status != 6) {
                                    $MJTC_nonce = wp_create_nonce("get-tickets-for-merging-".majesticsupport::$_data[0]->id) ?>
                                    <a class="mjtc-sidebar-action-btn mjtc-btn-dark" href="#" id="mergeticket" title="<?php echo esc_attr(__('Merge Ticket', 'majestic-support')); ?>" 
                                        onclick="return showPopupAndFillValues(<?php echo esc_js(majesticsupport::$_data[0]->id);?>,4, '<?php echo esc_js($MJTC_nonce);?>')">
                                        <svg class="mjtc-sprt-icon" viewBox="0 0 24 24"><circle cx="18" cy="18" r="3"></circle><circle cx="6" cy="6" r="3"></circle><path d="M6 21V9a9 9 0 0 0 9 9"></path></svg>
                                        <span><?php echo esc_attr(__('Merge', 'majestic-support')); ?></span>
                                    </a>
                                    <?php
                                } /*Merge Ticket*/
                            }
                            if (in_array('actions',majesticsupport::$_active_addons)) {
                                if (majesticsupport::$_data[0]->lock == 1) { ?>
                                    <a class="mjtc-sidebar-action-btn mjtc-btn-warning" href="#" onclick="actionticket(5);" title="<?php echo esc_attr(__('Unlock Ticket', 'majestic-support')); ?>">
                                        <svg class="mjtc-sprt-icon mjtc-icon-muted" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 9.9-1"></path></svg>
                                        <span><?php echo esc_attr(__('Unlock', 'majestic-support')); ?></span>
                                    </a>
                                    <?php
                                } else { ?>
                                    <a class="mjtc-sidebar-action-btn mjtc-btn-info" href="#" onclick="actionticket(4);" title="<?php echo esc_attr(__('Lock Ticket', 'majestic-support')); ?>">
                                        <svg class="mjtc-sprt-icon" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                        <span><?php echo esc_attr(__('Lock', 'majestic-support')); ?></span>
                                    </a>
                                    <?php
                                }
                            }
                            if(in_array('banemail', majesticsupport::$_active_addons)){
                                if (MJTC_includer::MJTC_getModel('banemail')->isEmailBan(majesticsupport::$_data[0]->email)) { ?>
                                    <a title="<?php echo esc_attr(__('Unban Email', 'majestic-support')); ?>" class="mjtc-sidebar-action-btn mjtc-btn-success" href="#" onclick="actionticket(7);">
                                        <svg class="mjtc-sprt-icon mjtc-icon-success" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><polyline points="17 11 19 13 23 9"></polyline></svg>
                                        <span><?php echo esc_attr(__('Unban', 'majestic-support')); ?></span>
                                    </a>
                                    <?php
                                } else { ?>
                                    <a class="mjtc-sidebar-action-btn mjtc-btn-danger" href="#" title="<?php echo esc_attr(__('Ban Email', 'majestic-support')); ?>" onclick="actionticket(6);">
                                        <svg class="mjtc-sprt-icon" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg>
                                        <span><?php echo esc_attr(__('Ban', 'majestic-support')); ?></span>
                                    </a>
                                    <?php
                                }
                            }
                            if(in_array('overdue', majesticsupport::$_active_addons)) {
                                if (majesticsupport::$_data[0]->isoverdue == 1) { ?>
                                    <a class="mjtc-sidebar-action-btn mjtc-btn-dark" href="#" onclick="actionticket(11);" title="<?php echo esc_attr(__('Unmark Overdue', 'majestic-support')); ?>" />
                                        <svg class="mjtc-sprt-icon mjtc-icon-success" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><path d="M9 12l2 2 4-4"></path></svg>
                                        <span><?php echo esc_attr(__('Unmark Overdue', 'majestic-support')); ?></span>
                                    </a>
                                    <?php
                                } else { ?>
                                    <a class="mjtc-sidebar-action-btn mjtc-btn-warning" title="<?php echo esc_attr(__('Mark Overdue', 'majestic-support')); ?>"  href="#" onclick="actionticket(8);">
                                        <svg class="mjtc-sprt-icon" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                        <span><?php echo esc_attr(__('Mark Overdue', 'majestic-support')); ?></span>
                                    </a>
                                    <?php
                                }
                            }
                            if (in_array('actions',majesticsupport::$_active_addons)) { ?>
                                <a class="mjtc-sidebar-action-btn mjtc-btn-info" href="#" onclick="actionticket(9);" title="<?php echo esc_attr(__('Mark In Progress', 'majestic-support')); ?>">
                                    <svg class="mjtc-sprt-icon" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><polygon points="10 8 16 12 10 16 10 8"></polygon></svg>
                                    <span><?php echo esc_attr(__('Mark In Progress', 'majestic-support')); ?></span>
                                </a>
                                <?php
                            }
                            if(in_array('banemail', majesticsupport::$_active_addons)){ ?>
                                <a class="mjtc-sidebar-action-btn mjtc-btn-danger" href="#" onclick="actionticket(10);" title="<?php echo esc_attr(__('Ban Email And Close Ticket', 'majestic-support')); ?>">
                                    <svg class="mjtc-sprt-icon" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="18" y1="8" x2="23" y2="13"></line><line x1="23" y1="8" x2="18" y2="13"></line></svg>
                                    <span><?php echo esc_attr(__('Ban Email And Close Ticket', 'majestic-support')); ?></span>
                                </a>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                    <?php
                }
            } ?>
            <?php
            if(
            in_array('agent',majesticsupport::$_active_addons) &&  
            majesticsupport::$_data['user_staff'] &&
            in_array('aipoweredreply', majesticsupport::$_active_addons) &&
            MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForTask('Set AI Reply Mode for Ticket')){ ?>
                <div class="mjtc-sprt-det-cnt mjtc-sprt-det-tkt-prty"> <!-- Ticket Status -->
                    <div class="mjtc-sprt-det-hdg">
                        <div class="mjtc-sprt-det-hdg-txt mjtc-sprt-det-hdg-txt-ai-reply">
                            <label for="mjtc-support-ai-reply-status-control">
                                <?php echo esc_html__('AI-Powered Reply Mode', 'majestic-support'); ?>
                            </label>
                            <div class="mjtc-support-info-icon-wrapper">
                                <span class="mjtc-support-info-icon" data-tooltip="<?php echo esc_attr(__("Control how this ticket and its replies influence AI search and response generation for future queries.",'majestic-support')); ?>">
                                    <img alt="<?php echo esc_attr(__('Info','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/ticket-detail/info-icon.png" />
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="mjtc-sprt-det-tkt-prty-txt mjtc-support-segmented-control-wrp">
                        <div id="mjtc-support-ai-reply-status-control" class="mjtc-support-segmented-control">
                            <button type="button" class="mjtc-support-segmented-control-option mjtc-support-default <?php echo ( absint( majesticsupport::$_data[0]->aireplymode ) === 0 ) ? 'active' : ''; ?>" data-value="0" data-type="ticket" data-id="<?php echo esc_attr( majesticsupport::$_data[0]->id ); ?>" title="<?php echo esc_attr(__( "Default: ticket and replies included in all AI queries.", "majestic-support" ) ); ?>">
                                <?php echo esc_html__('Default', 'majestic-support'); ?>
                            </button>
                            <button data-type="ticket" type="button" class="mjtc-support-segmented-control-option mjtc-support-enable <?php echo ( absint( majesticsupport::$_data[0]->aireplymode ) === 1 ) ? 'active' : ''; ?>" data-value="1" data-type="ticket" data-id="<?php echo esc_attr( majesticsupport::$_data[0]->id ); ?>" title="<?php echo esc_attr(__( "Enable: ticket and replies used only when the “Enable Tickets” filter is active.", "majestic-support" ) ); ?>">
                                <?php echo esc_html__('Enable', 'majestic-support'); ?>
                            </button>
                            <button data-type="ticket" type="button" class="mjtc-support-segmented-control-option mjtc-support-disable <?php echo ( absint( majesticsupport::$_data[0]->aireplymode ) === 2 ) ? 'active' : ''; ?>" data-value="2" data-type="ticket" data-id="<?php echo esc_attr( majesticsupport::$_data[0]->id ); ?>" title="<?php echo esc_attr(__( "Disable: ticket and replies excluded from AI queries.", "majestic-support" ) ); ?>">
                                <?php echo esc_html__('Disable', 'majestic-support'); ?>
                            </button>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>
            <!-- Status Widget -->
            <div class="mjtc-sprt-det-cnt mjtc-support-sidebar-bg">
                <div class="mjtc-support-sidebar-widget">
                    <?php
                    if (in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) {
                        $MJTC_specialClass = 'mjtc-support-sidebar-widget-item-agent';
                    } else {
                        $MJTC_specialClass = 'mjtc-support-sidebar-widget-item-user';
                    }
                    if (!empty($MJTC_field_array['status'])) { ?>
                        <div class="mjtc-support-sidebar-widget-item <?php echo esc_attr($MJTC_specialClass); ?>">
                            <form method="post" action="<?php echo esc_url(wp_nonce_url(majesticsupport::makeUrl(array('mjsmod'=>'ticket','task'=>'changestatus')),"change-status-".majesticsupport::$_data[0]->id)); ?>" enctype="multipart/form-data">
                                <label class="mjtc-support-field-label">
                                    <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field_array['status'])); ?>
                                </label>
                                <?php
                                if (!empty(majesticsupport::$_data[0]->status)) {
                                    $MJTC_readonly = true;
                                    if (
                                        in_array('agent', majesticsupport::$_active_addons) &&
                                        majesticsupport::$_data['user_staff'] &&
                                        majesticsupport::$_data[0]->status != 6
                                    ) {
                                        if (
                                            MJTC_includer::MJTC_getModel('userpermissions')
                                            ->MJTC_checkPermissionGrantedForTask('Change Ticket Status')
                                        ) {
                                            $MJTC_readonly = false;
                                        } else { ?>
                                            <span class="mjtc-support-permission-warning">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-info"><circle cx="12" cy="12" r="10"></circle><path d="M12 16v-4"></path><path d="M12 8h.01"></path></svg>
                                                <?php echo esc_attr(__('Action not permitted.', 'majestic-support')); ?>
                                            </span>
                                            <?php
                                        }
                                    } ?>
                                    <?php
                                    if (!$MJTC_readonly) {
                                        echo wp_kses(
                                            MJTC_formfield::MJTC_submitbutton(
                                                'changestatus',
                                                esc_html(__('Save Status', 'majestic-support')),
                                                array(
                                                    'class' => 'button mjtc-support-save-button',
                                                    'style' => 'display:none;'
                                                )
                                            ),
                                            MJTC_ALLOWED_TAGS
                                        );
                                    }
                                    // Build select attributes
                                    $MJTC_select_attrs = array(
                                        'class' => 'mjtc-support-form-select',
                                    );

                                    if ($MJTC_readonly) {
                                        $MJTC_select_attrs['disabled'] = 'disabled';
                                    }
                                    echo wp_kses(
                                        MJTC_formfield::MJTC_select(
                                            'status',
                                            MJTC_includer::MJTC_getModel('status')->getStatusForCombobox(),
                                            majesticsupport::$_data[0]->status,
                                            esc_html(__('Select Status', 'majestic-support')),
                                            $MJTC_select_attrs
                                        ),
                                        MJTC_ALLOWED_TAGS
                                    );
                                    ?>
                                    <?php echo wp_kses(MJTC_formfield::MJTC_hidden('ticketid', majesticsupport::$_data[0]->id), MJTC_ALLOWED_TAGS); ?>
                                    <?php echo wp_kses(MJTC_formfield::MJTC_hidden('uid', MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid()), MJTC_ALLOWED_TAGS); ?>
                                    <?php echo wp_kses(MJTC_formfield::MJTC_hidden('action', 'ticket_changestatus'), MJTC_ALLOWED_TAGS); ?>
                                    <?php echo wp_kses(MJTC_formfield::MJTC_hidden('form_request', 'majesticsupport'), MJTC_ALLOWED_TAGS); ?>
                                    <?php echo wp_kses(MJTC_formfield::MJTC_hidden('mspageid', get_the_ID()), MJTC_ALLOWED_TAGS); ?>
                                    <?php
                                } ?>
                            </form>
                        </div>
                        <?php
                    }
                    if (!empty($MJTC_field_array['priority'])) { ?>
                        <div class="mjtc-support-sidebar-widget-item <?php echo esc_attr($MJTC_specialClass); ?>">
                            <label class="mjtc-support-field-label">
                                <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field_array['priority'])); ?>
                            </label>

                            <?php
                            $MJTC_readonly = true;

                            if (
                                in_array('agent', majesticsupport::$_active_addons) &&
                                majesticsupport::$_data['user_staff'] &&
                                majesticsupport::$_data[0]->status != 6
                            ) {
                                if (
                                    MJTC_includer::MJTC_getModel('userpermissions')
                                    ->MJTC_checkPermissionGrantedForTask('Change Ticket Priority')
                                ) {
                                    $MJTC_readonly = false;
                                } else { ?>
                                    <span class="mjtc-support-permission-warning">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-info"><circle cx="12" cy="12" r="10"></circle><path d="M12 16v-4"></path><path d="M12 8h.01"></path></svg>
                                        <?php echo esc_attr(__('Action not permitted.', 'majestic-support')); ?>
                                    </span>
                                    <?php
                                }
                            }

                            /* ---- Save button (hidden initially) ---- */
                            if (!$MJTC_readonly) {
                                echo wp_kses(
                                    MJTC_formfield::MJTC_button(
                                        'changepriority',
                                        esc_html(__('Save', 'majestic-support') . ' ' . majesticsupport::MJTC_getVarValue($MJTC_field_array['priority'])),
                                        array(
                                            'class' => 'mjtc-support-priorty-save',
                                            'style' => 'display:none;',
                                            'onclick' => 'actionticket(1);'
                                        )
                                    ),
                                    MJTC_ALLOWED_TAGS
                                );
                            }

                            if (!empty(majesticsupport::$_data[0]->priority)) {

                                /* ---- Select attributes ---- */
                                $MJTC_select_attrs = array(
                                    'class' => 'mjtc-support-form-select'
                                );

                                if ($MJTC_readonly) {
                                    $MJTC_select_attrs['disabled'] = 'disabled';
                                }

                                echo wp_kses(
                                    MJTC_formfield::MJTC_select(
                                        'prioritytemp',
                                        MJTC_includer::MJTC_getModel('priority')->getPriorityForCombobox(),
                                        majesticsupport::$_data[0]->priorityid,
                                        esc_html(__('Change', 'majestic-support') . ' ' . majesticsupport::MJTC_getVarValue($MJTC_field_array['priority'])),
                                        $MJTC_select_attrs
                                    ),
                                    MJTC_ALLOWED_TAGS
                                );

                            } else { ?>
                                <div class="mjtc-sprt-det-tkt-prty-error-txt">
                                    <?php
                                    echo esc_html(__('No', 'majestic-support')) . ' ' .
                                         esc_html(majesticsupport::MJTC_getVarValue($MJTC_field_array['priority'])) . ' ' .
                                         esc_html(__('set', 'majestic-support'));
                                    ?>
                                </div>
                                <?php
                            } ?>
                        </div>
                        <?php
                    } ?>
                </div>
                <?php
                $MJTC_agentflag = in_array('agent', majesticsupport::$_active_addons) && $MJTC_printflag == false && majesticsupport::$_data[0]->status != 5 && majesticsupport::$_data[0]->status != 6;
                
                if($MJTC_agentflag){ ?>
                    <div class="mjtc-sprt-det-cnt mjtc-sprt-det-tkt-assign">
                        <!-- Ticket Assign -->
                        <?php
                        if($MJTC_agentflag){ ?>
                            <div class="mjtc-sprt-det-hdg">
                                <div class="mjtc-sprt-det-hdg-txt">
                                    <?php echo esc_html(__('Assigned To Agent','majestic-support')); ?>
                                </div>
                            </div>
                            <?php
                        } ?>
                        <div class="mjtc-sprt-det-tkt-asgn-cnt">
                            <?php
                            if($MJTC_agentflag){ ?>
                                <div class="mjtc-sprt-det-hdg">
                                    <div class="mjtc-sprt-det-hdg-txt">
                                        <?php
                                        if(majesticsupport::$_data[0]->staffid > 0 && MJTC_includer::MJTC_getModel('agent')->isUserStaff()){
                                            echo esc_html(__('Ticket assigned to','majestic-support'));
                                        } ?>
                                    </div>
                                    <?php
                                    if(majesticsupport::$_data['user_staff']){ 
                                        if(MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForTask('Assign Ticket To Agent')){ ?>
                                            <a class="mjtc-sprt-det-hdg-btn" href="#" id="agenttransfer">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-pencil"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"></path><path d="m15 5 4 4"></path></svg>
                                            </a>
                                            <?php
                                        } else { ?>
                                            <span class="mjtc-support-permission-warning">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-info"><circle cx="12" cy="12" r="10"></circle><path d="M12 16v-4"></path><path d="M12 8h.01"></path></svg>
                                                <?php echo esc_attr(__('Action not permitted.', 'majestic-support')); ?>
                                            </span>
                                            <?php
                                        }
                                    } ?>
                                </div>
                                <?php
                            } ?>
                            <div class="mjtc-sprt-det-info-wrp">
                                <?php if($MJTC_agentflag && majesticsupport::$_data[0]->staffid > 0){ ?>
                                    <div class="mjtc-sprt-det-user">
                                        <div class="mjtc-sprt-det-user-image">
                                            <?php
                                            if(majesticsupport::$_data[0]->staffphoto && majesticsupport::$_config['anonymous_name_on_ticket_reply'] == 2){
                                                echo wp_kses(MJTC_get_avatar(majesticsupport::$_data[0]->staffuid, 'mjtc-support-staff-img'), MJTC_ALLOWED_TAGS);
                                            } else { ?>
                                                <img alt="<?php echo esc_attr(__('staff photo','majestic-support')); ?>"
                                                src="<?php echo esc_url(MJTC_PLUGIN_URL) . '/includes/images/user.png'; ?>" />
                                                <?php
                                            }
                                            ?>
                                        </div>
                                        <div class="mjtc-sprt-det-user-cnt">
                                            <div class="mjtc-sprt-det-user-data">
                                                <?php 
                                                if (majesticsupport::$_config['anonymous_name_on_ticket_reply'] == 1) {
                                                    echo esc_html(majesticsupport::$_config['title']);
                                                }else{
                                                    echo esc_html(majesticsupport::$_data[0]->staffname); 
                                                } ?>
                                            </div>
                                            <div class="mjtc-sprt-det-user-data agent-email">
                                                <?php 
                                                if (majesticsupport::$_config['show_email_on_ticket_reply'] == 1) {
                                                    echo esc_html(majesticsupport::$_data[0]->staffemail); 
                                                } ?>
                                            </div>
                                            <div class="mjtc-sprt-det-user-data">
                                                <?php 
                                                if (majesticsupport::$_config['show_email_on_ticket_reply'] == 2) {
                                                    echo esc_html(majesticsupport::$_data[0]->staffphone);
                                                } ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                } else { ?>
                                    <div class="mjtc-sprt-det-hdg-txt">
                                        <?php echo esc_html(__('Not assigned to agent','majestic-support')); ?>
                                    </div>
                                    <?php
                                } ?>
                            </div>
                        </div>
                    </div>
                    <?php
                } ?>
            </div>
            <!-- Ticket Props -->
            <?php
            if (in_array('ticketclosereason',majesticsupport::$_active_addons) && isset(majesticsupport::$_data[0]->closedreason) && majesticsupport::$_data[0]->status == 5) { ?>
                <?php
                $MJTC_closedreasons = json_decode(majesticsupport::$_data[0]->closedreason);
                if (is_array($MJTC_closedreasons)) { ?>
                    <div class="mjtc-sprt-det-cnt">
                        <h3 class="mjtc-sprt-det-hdg">
                            <?php echo esc_html(__('Ticket Closing Reason','majestic-support')); ?>
                        </h3>
                        <div class="mjtc-support-closed-reason-wrp">
                            <?php
                            foreach ($MJTC_closedreasons as $MJTC_closedreason) { ?>
                                <div class="mjtc-sprt-det-info-data">
                                    <span class="mjtc-sprt-det-info-val">
                                        <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_closedreason)); ?>
                                    </span>
                                </div>
                                <?php
                            } ?>
                        </div>
                    </div>
                    <?php
                } ?>
                <?php
            } ?>
            <div class="mjtc-sprt-det-cnt">
                <h3 class="mjtc-sprt-det-hdg">
                    <?php echo esc_html(__('Ticket Properties','majestic-support')); ?>
                </h3>
                <?php
                $MJTC_departmentflag = in_array('actions', majesticsupport::$_active_addons) && $MJTC_printflag == false && majesticsupport::$_data[0]->status != 5 && majesticsupport::$_data[0]->status != 6 && isset($MJTC_field_array['department']); ?>
                <div class="mjtc-support-ticket-props-wrp">
                    <div class="mjtc-support-ticket-props-edit-notice">
                        <span class="mjtc-support-ticket-props-edit-notice-txt">
                            <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field_array['department'])).': '; ?>
                        </span>
                        <span class="mjtc-support-ticket-props-edit-notice-val">
                            <?php echo esc_html(majesticsupport::MJTC_getVarValue(majesticsupport::$_data[0]->departmentname));
                            if($MJTC_departmentflag){
                                if(majesticsupport::$_data['user_staff']){ 
                                    if(MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForTask('Ticket Department Transfer')){ ?>
                                        <a title="<?php echo esc_attr(__('Change','majestic-support')); ?>" href="#" class="mjtc-sprt-det-hdg-btn" id="departmenttransfer">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-pencil"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"></path><path d="m15 5 4 4"></path></svg>
                                        </a>
                                        <?php
                                    }
                                }
                            } ?>
                        </span>
                    </div>
                    <?php
                    if(isset($MJTC_field_array['product'])) { ?>
                        <div class="mjtc-support-ticket-props-edit-notice">
                            <span class="mjtc-support-ticket-props-edit-notice-txt">
                                <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field_array['product'])); ?><?php echo esc_html(': ');?>
                            </span>
                            <span class="mjtc-support-ticket-props-edit-notice-val">
                                <?php echo esc_html(majesticsupport::MJTC_getVarValue(majesticsupport::$_data[0]->producttitle)); ?>
                            </span>
                        </div>
                        <?php
                    }
                    if (in_array('multiform',majesticsupport::$_active_addons)) { ?>
                        <div class="mjtc-support-ticket-creation-wrp">
                            <span class="mjtc-support-ticket-creation-txt">
                                <?php echo esc_html(__('Form', 'majestic-support')) . ': '; ?>
                            </span>
                            <span class="mjtc-support-ticket-creation-val" title="<?php echo esc_attr(__('Form', 'majestic-support')); ?>">
                                <?php echo esc_html(majesticsupport::$_data[0]->multiform); ?>
                            </span>
                        </div>
                        <?php
                    } ?>
                    <div class="mjtc-support-ticket-creation-wrp">
                        <span class="mjtc-support-ticket-creation-txt">
                            <?php echo esc_html(__('Created', 'majestic-support')) . ': '; ?>
                        </span>
                        <span class="mjtc-support-ticket-creation-val" title="<?php echo esc_attr(date_i18n("d F, Y, H:i:s A", MJTC_majesticsupportphplib::MJTC_strtotime(majesticsupport::$_data[0]->created))); ?>">
                            <?php echo esc_html(human_time_diff(MJTC_majesticsupportphplib::MJTC_strtotime(majesticsupport::$_data[0]->created),MJTC_majesticsupportphplib::MJTC_strtotime(date_i18n("Y-m-d H:i:s")))).' '.esc_html(__('ago', 'majestic-support')); ?>
                        </span>
                    </div>
                    <div class="mjtc-support-last-reply-wrp">
                        <span class="mjtc-support-last-reply-txt">
                            <?php echo esc_html(__('Last Reply', 'majestic-support')); ?><?php echo esc_html(': ');?>
                        </span>
                        <span class="mjtc-support-last-reply-val">
                            <?php if (empty(majesticsupport::$_data[0]->lastreply) || majesticsupport::$_data[0]->lastreply == '0000-00-00 00:00:00') echo esc_html(__('No Last Reply', 'majestic-support'));
                                else echo esc_html(date_i18n(majesticsupport::$_config['date_format'], MJTC_majesticsupportphplib::MJTC_strtotime(majesticsupport::$_data[0]->lastreply))); ?>
                        </span>
                    </div>
                    <?php
                    if (in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) {
                        $MJTC_configname = 'agent';
                    } else {
                        $MJTC_configname = 'user';
                    }
                    if (majesticsupport::$_config['show_closedby_on_' . $MJTC_configname . '_tickets'] == 1 && majesticsupport::$_data[0]->status == 5) { ?>
                        <div class="mjtc-support-closed-by-wrp">
                            <span class="mjtc-support-closed-by-txt">
                                <?php echo esc_html(__('Closed By', 'majestic-support')). ' : '; ?>
                            </span>
                            <span class="mjtc-support-closed-by-val">
                                <?php echo esc_html(MJTC_includer::MJTC_getModel('ticket')->getClosedBy(majesticsupport::$_data[0]->closedby)); ?>
                            </span>
                        </div>
                        <div class="mjtc-support-closed-on-wrp">
                            <span class="mjtc-support-closed-on-txt">
                                <?php echo esc_html(__('Closed On', 'majestic-support')). ' : '; ?>
                            </span>
                            <span class="mjtc-support-closed-on-val">
                                <?php echo esc_html(date_i18n(majesticsupport::$_config['date_format'], MJTC_majesticsupportphplib::MJTC_strtotime(majesticsupport::$_data[0]->closed))); ?>
                            </span>
                        </div>
                    <?php } ?>
                    <div class="mjtc-support-ticket-id-wrp">
                        <span class="mjtc-support-ticket-id-txt">
                            <?php echo esc_html(__('Ticket ID', 'majestic-support')); ?><?php echo esc_html(': ');?>
                        </span>
                        <span class="mjtc-support-ticket-id-val">
                            <?php echo esc_html(majesticsupport::$_data[0]->ticketid); ?>
                            <a title="<?php echo esc_attr(__('Copy','majestic-support')); ?>" class="mjtc-sprt-det-copy-id" id="ticketidcopybtn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"><path d="M20 2H10c-1.1 0-2 .9-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2zM4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6z"></path></svg>
                            </a>
                        </span>
                    </div>
                    <?php 
                    if(in_array('helptopic', majesticsupport::$_active_addons) && isset($MJTC_field_array['helptopic'])){ ?>
                        <div class="mjtc-support-helptopic-wrp">
                            <span class="mjtc-support-helptopic-txt">
                                <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field_array['helptopic'])); ?><?php echo esc_html(': ');?>
                            </span>
                            <span class="mjtc-support-helptopic-val">
                                <?php echo esc_html(majesticsupport::MJTC_getVarValue(majesticsupport::$_data[0]->helptopic)); ?>
                            </span>
                        </div>
                        <?php
                    }
                    if (isset($MJTC_field_array['status'])) { ?>
                        <div class="mjtc-support-ticket-status-wrp">
                            <span class="mjtc-support-ticket-status-txt">
                                <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field_array['status'])); ?><?php echo esc_html(': ');?>
                            </span>
                            <span class="mjtc-support-ticket-status-val">
                                <?php
                                if (majesticsupport::$_data[0]->status == 5 || majesticsupport::$_data[0]->status == 6 )
                                    $MJTC_ticketmessage = esc_html(__('Closed', 'majestic-support'));
                                elseif (majesticsupport::$_data[0]->status == 3)
                                    $MJTC_ticketmessage = esc_html(__('In Progress', 'majestic-support'));
                                else
                                    $MJTC_ticketmessage = esc_html(__('Open', 'majestic-support'));
                                $MJTC_printstatus = 1;
                                if (majesticsupport::$_data[0]->lock == 1 && in_array('actions', majesticsupport::$_active_addons)) {
                                    $MJTC_ticketStatus = '<div class="mjtc-support-status-note">' . esc_html(__('Lock', 'majestic-support')).' '. esc_html(',') . '</div>';
                                    echo wp_kses($MJTC_ticketStatus, MJTC_ALLOWED_TAGS);
                                    $MJTC_printstatus = 0;
                                }
                                if (majesticsupport::$_data[0]->isoverdue == 1 && in_array('overdue', majesticsupport::$_active_addons)) {
                                    $MJTC_ticketStatus = '<div class="mjtc-support-status-note">' . esc_html(__('Overdue', 'majestic-support')) . '</div>';
                                    echo wp_kses($MJTC_ticketStatus, MJTC_ALLOWED_TAGS);
                                    $MJTC_printstatus = 0;
                                }
                                if ($MJTC_printstatus == 1) {
                                    echo esc_html($MJTC_ticketmessage);
                                } ?>
                            </span>
                        </div>
                        <?php
                    } ?>
                </div>
            </div>

            <!-- Custom Fields -->
            <?php
            majesticsupport::$_data['custom']['ticketid'] = majesticsupport::$_data[0]->id;
            $MJTC_customfields = MJTC_includer::MJTC_getObjectClass('customfields')->MJTC_userFieldsData(1, null, majesticsupport::$_data[0]->multiformid);
            if (!empty($MJTC_customfields)){ ?>
                <div class="mjtc-sprt-det-cnt">
                    <h3 class="mjtc-sprt-det-hdg">
                        <?php echo esc_html(__('Custom Fields','majestic-support')); ?>
                    </h3>
                    <div class="mjtc-support-custom-fields-wrp">
                        <?php
                        foreach ($MJTC_customfields as $MJTC_field) {
                            if ($MJTC_field->userfieldtype != 'termsandconditions') {
                                $MJTC_ret = MJTC_includer::MJTC_getObjectClass('customfields')->MJTC_showCustomFields($MJTC_field,2, majesticsupport::$_data[0]->params); ?>
                                <div class="mjstc-support-custom-field-item">
                                    <span class="mjtc-support-custom-field-title">
                                        <?php echo wp_kses($MJTC_ret['title'], MJTC_ALLOWED_TAGS).': '; ?>
                                    </span>
                                    <span class="mjtc-support-custom-field-value">
                                        <?php echo wp_kses($MJTC_ret['value'], MJTC_ALLOWED_TAGS); ?>
                                    </span>
                                </div>
                                <?php
                            }
                        } ?>
                    </div>
                </div>
                <?php
            } ?>

            <!-- User Tickets -->
            <?php
            if(isset(majesticsupport::$_data['usertickets']) && !empty(majesticsupport::$_data['usertickets'])){ ?>
                <div class="mjtc-sprt-det-cnt mjtc-sprt-det-user-tkts">
                    <div class="mjtc-sprt-det-hdg">
                        <?php
                        if(!empty($MJTC_field_array['fullname'])) { echo esc_html(majesticsupport::$_data[0]->name).' '. esc_html(__('Tickets','majestic-support'));
                        } else {
                            echo esc_html(__('Other Tickets','majestic-support'));
                        } ?>
                    </div>
                    <?php
                    $MJTC_fields_array = array(); // Array for form fields
                    foreach(majesticsupport::$_data['usertickets'] as $MJTC_userticket){
                        // Check if the form fields are already array
                        if (!isset($MJTC_fields_array[$MJTC_userticket->multiformid])) {
                            $MJTC_fields_array[$MJTC_userticket->multiformid] = MJTC_includer::MJTC_getModel('fieldordering')->getFieldTitleByFieldfor(1, $MJTC_userticket->multiformid);
                        }
                        // Now use the cached field array
                        $MJTC_ticket_field_array = $MJTC_fields_array[$MJTC_userticket->multiformid]; ?>
                        <div class="mjtc-sprt-det-user">
                            <div class="mjtc-sprt-det-user-image">
                                <div class="mjtc-support-avatar">
                                    <?php echo wp_kses(MJTC_get_avatar(majesticsupport::$_data[0]->uid), MJTC_ALLOWED_TAGS); ?>
                                </div>
                            </div>
                            <div class="mjtc-sprt-det-user-cnt">
                                <div class="mjtc-sprt-det-user-val">
                                    <a  title="<?php echo esc_attr(__('view ticket','majestic-support')); ?>" href="<?php echo esc_url(majesticsupport::makeUrl(array('mjsmod'=>'ticket','mjslay'=>'ticketdetail','majesticsupportid'=> $MJTC_userticket->id))); ?>">
                                        <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_userticket->subject)); ?>
                                    </a>
                                </div>
                                <div class="mjtc-sprt-det-user-data">
                                    <?php
                                    if(!empty($MJTC_ticket_field_array['priority'])) { ?>
                                        <span style="background:<?php echo esc_html($MJTC_userticket->prioritycolour);?>;" class="mjtc-sprt-det-prty">
                                            <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_userticket->priority)); ?>
                                        </span>
                                        <?php
                                    }
                                    if ($MJTC_userticket->status == 5 || $MJTC_userticket->status == 6 ||
                                    $MJTC_userticket->status == 3) {
                                        $MJTC_userticketmessage = esc_html($MJTC_userticket->statustitle);
                                    } else {
                                        $MJTC_userticketmessage = esc_html(__('Open', 'majestic-support'));
                                    } ?>
                                </div>
                            </div>
                        </div>
                        <?php
                    } ?>
                </div>
                <?php
            } ?>

            <!-- Woocomerece -->
            <?php
            apply_filters( 'mjtc_support_ticket_admin_details_right_middle', majesticsupport::$_data[0]->id );
            if( class_exists('WooCommerce') && in_array('woocommerce', majesticsupport::$_active_addons)){
                $MJTC_order = wc_get_order(majesticsupport::$_data[0]->wcorderid);
                $MJTC_order_itemid = majesticsupport::$_data[0]->wcproductid;
                if($MJTC_order){ ?>
                    <div class="mjtc-sprt-det-cnt mjtc-sprt-det-woocom">
                        <div class="mjtc-sprt-det-hdg">
                            <div class="mjtc-sprt-det-hdg-txt">
                                <?php echo esc_html(__("Woocommerce Order",'majestic-support')); ?>
                            </div>
                        </div>
                        <div class="mjtc-sprt-wc-order-box">
                            <div class="mjtc-sprt-wc-order-item">
                                <div class="mjtc-sprt-wc-order-item-title">
                                    <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field_array['wcorderid'])); ?>:</div>
                                <div class="mjtc-sprt-wc-order-item-value">#<?php echo esc_html($MJTC_order->get_id()); ?></div>
                            </div>
                            <div class="mjtc-sprt-wc-order-item">
                                <div class="mjtc-sprt-wc-order-item-title"><?php echo esc_html(__("Status",'majestic-support')); ?>:</div>
                                <div class="mjtc-sprt-wc-order-item-value">
                                    <?php echo wp_kses(wc_get_order_status_name($MJTC_order->get_status()), MJTC_ALLOWED_TAGS); ?></div>
                            </div>
                            <?php
                            if($MJTC_order_itemid){
                                $MJTC_item = new WC_Order_Item_Product($MJTC_order_itemid);
                                if($MJTC_item){ ?>
                                    <div class="mjtc-sprt-wc-order-item">
                                        <div class="mjtc-sprt-wc-order-item-title">
                                            <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field_array['wcproductid'])); ?>:</div>
                                        <div class="mjtc-sprt-wc-order-item-value"><?php echo esc_html($MJTC_item->get_name()); ?></div>
                                    </div>
                                    <?php
                                }
                            } ?>
                            <div class="mjtc-sprt-wc-order-item">
                                <div class="mjtc-sprt-wc-order-item-title"><?php echo esc_html(__("Created",'majestic-support')); ?>:</div>
                                <div class="mjtc-sprt-wc-order-item-value">
                                    <?php echo esc_html($MJTC_order->get_date_created()->date_i18n(wc_date_format())); ?></div>
                            </div>
                            <?php
                            if(in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()){
                                do_action('MJTC_woocommerce_order_detail_agent', $MJTC_order, $MJTC_order_itemid);
                            }
                            if(majesticsupport::$_data[0]->uid == MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid()){ ?>
                                <a href="<?php echo esc_url(wc_get_endpoint_url('orders','',wc_get_page_permalink('myaccount'))); ?>"
                                    class="mjtc-sprt-wc-order-item-link">
                                    <?php echo esc_html(__("View all orders",'majestic-support')); ?>
                                </a>
                                <?php
                            } ?>
                        </div>
                    </div>
                    <?php
                }
            } ?>
            <!-- Easy Digital Downloads -->
            <?php
            if( class_exists('Easy_Digital_Downloads') && in_array('easydigitaldownloads', majesticsupport::$_active_addons)){
                $MJTC_orderid = majesticsupport::$_data[0]->eddorderid;
                $MJTC_order_product = majesticsupport::$_data[0]->eddproductid;
                $MJTC_order_license = majesticsupport::$_data[0]->eddlicensekey;
                if($MJTC_orderid != '' && ((isset($MJTC_field_array['eddlicensekey']) && class_exists('EDD_Software_Licensing')) || isset($MJTC_field_array['eddorderid']) || isset($MJTC_field_array['eddorderid']))){ ?>
                    <div class="mjtc-sprt-det-cnt mjtc-sprt-det-edd">
                        <div class="mjtc-sprt-det-hdg">
                            <div class="mjtc-sprt-det-hdg-txt">
                                <?php echo esc_html(__("Easy Digital Downloads",'majestic-support')); ?>
                            </div>
                        </div>
                        <div class="mjtc-sprt-wc-order-box">
                            <?php 
                            if (isset($MJTC_field_array['eddorderid'])) {  ?>
                                <div class="mjtc-sprt-wc-order-item">
                                    <div class="mjtc-sprt-wc-order-item-title">
                                        <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field_array['eddorderid'])); ?>:</div>
                                    <div class="mjtc-sprt-wc-order-item-value">#<?php echo esc_html($MJTC_orderid); ?></div>
                                </div>
                                <?php
                            }
                            if (isset($MJTC_field_array['eddorderid'])) {  ?>
                                <div class="mjtc-sprt-wc-order-item">
                                    <div class="mjtc-sprt-wc-order-item-title">
                                        <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field_array['eddproductid'])); ?>:</div>
                                    <div class="mjtc-sprt-wc-order-item-value">
                                        <?php
                                            if(is_numeric($MJTC_order_product)){
                                                $MJTC_download = new EDD_Download($MJTC_order_product);
                                                echo wp_kses($MJTC_download->post_title, MJTC_ALLOWED_TAGS);
                                            }else{
                                                echo esc_html('-----------');
                                            }
                                        ?>
                                    </div>
                                </div>
                                <?php
                            }
                            if(isset($MJTC_field_array['eddlicensekey']) && class_exists('EDD_Software_Licensing')){ ?>
                                <div class="mjtc-sprt-wc-order-item">
                                    <div class="mjtc-sprt-wc-order-item-title">
                                        <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field_array['eddlicensekey'])); ?>:</div>
                                    <div class="mjtc-sprt-wc-order-item-value"><?php
                                        if($MJTC_order_license != ''){
                                            $MJTC_license = EDD_Software_Licensing::instance();
                                            $MJTC_licenseid = $MJTC_license->get_license_by_key($MJTC_order_license);
                                            $MJTC_result = $MJTC_license->get_license_status($MJTC_licenseid);
                                            if($MJTC_result == 'expired'){
                                                $MJTC_result_color = 'red';
                                            }elseif($MJTC_result == 'inactive'){
                                                $MJTC_result_color = 'orange';
                                            }else{
                                                $MJTC_result_color = 'green';
                                            }
                                            echo wp_kses($MJTC_order_license.'&nbsp;&nbsp;(<span style="color:'.esc_attr($MJTC_result_color).';font-weight:bold;text-transform:uppercase;padding:0 3px;">'.wp_kses($MJTC_result, MJTC_ALLOWED_TAGS).'</span>)', MJTC_ALLOWED_TAGS);
                                        }
                                        ?>
                                    </div>
                                </div>
                                <?php
                            }?>
                        </div>
                    </div>
                    <?php
                }
            } ?>
            <!-- Envato Validation -->
            <?php
            if(isset($MJTC_field_array['envatopurchasecode']) && in_array('envatovalidation', majesticsupport::$_active_addons) && !empty(majesticsupport::$_data[0]->envatodata)){
                $MJTC_envlicense = majesticsupport::$_data[0]->envatodata;
                if(!empty($MJTC_envlicense)){ ?>
                    <div class="mjtc-sprt-det-cnt mjtc-sprt-det-env">
                        <div class="mjtc-sprt-det-hdg">
                            <?php echo esc_html(__("Envato License",'majestic-support')); ?>
                        </div>
                        <div class="mjtc-sprt-wc-order-box">
                            <?php if(!empty($MJTC_envlicense['itemname']) && !empty($MJTC_envlicense['itemid'])){ ?>
                                <div class="mjtc-sprt-wc-order-item">
                                    <div class="mjtc-sprt-wc-order-item-title"><?php echo esc_html(__("Item",'majestic-support')); ?>:</div>
                                    <div class="mjtc-sprt-wc-order-item-value">
                                        <?php echo esc_html($MJTC_envlicense['itemname']).' (#'.esc_html($MJTC_envlicense['itemid']).')'; ?></div>
                                </div>
                            <?php } ?>
                            <?php if(!empty($MJTC_envlicense['buyer'])){ ?>
                                <div class="mjtc-sprt-wc-order-item">
                                    <div class="mjtc-sprt-wc-order-item-title"><?php echo esc_html(__("Buyer",'majestic-support')); ?>:</div>
                                    <div class="mjtc-sprt-wc-order-item-value"><?php echo esc_html($MJTC_envlicense['buyer']); ?></div>
                                </div>
                            <?php } ?>
                            <?php if(!empty($MJTC_envlicense['licensetype'])){ ?>
                                <div class="mjtc-sprt-wc-order-item">
                                    <div class="mjtc-sprt-wc-order-item-title"><?php echo esc_html(__("License Type",'majestic-support')); ?>:
                                    </div>
                                    <div class="mjtc-sprt-wc-order-item-value"><?php echo esc_html($MJTC_envlicense['licensetype']); ?></div>
                                </div>
                            <?php } ?>
                            <?php if(!empty($MJTC_envlicense['license'])){ ?>
                                <div class="mjtc-sprt-wc-order-item">
                                    <div class="mjtc-sprt-wc-order-item-title"><?php echo esc_html(__("License",'majestic-support')); ?>:</div>
                                    <div class="mjtc-sprt-wc-order-item-value"><?php echo esc_html($MJTC_envlicense['license']); ?></div>
                                </div>
                            <?php } ?>
                            <?php if(!empty($MJTC_envlicense['purchasedate'])){ ?>
                                <div class="mjtc-sprt-wc-order-item">
                                    <div class="mjtc-sprt-wc-order-item-title"><?php echo esc_html(__("Purchase Date",'majestic-support')); ?>:
                                    </div>
                                    <div class="mjtc-sprt-wc-order-item-value">
                                        <?php echo esc_html(date_i18n("F d, Y, H:i:s", MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_envlicense['purchasedate']))); ?>
                                    </div>
                                </div>
                            <?php } ?>
                            <?php if(!empty($MJTC_envlicense['supporteduntil'])){ ?>
                        <div class="mjtc-sprt-wc-order-item">
                            <div class="mjtc-sprt-wc-order-item-title"><?php echo esc_html(__("Supported Until",'majestic-support')); ?>:
                            </div>
                            <div class="mjtc-sprt-wc-order-item-value">
                                <?php echo esc_html(date_i18n("F d, Y", MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_envlicense['supporteduntil']))); ?></div>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                    <?php
                }
            } ?>
            <!-- Paid Support -->
            <?php
            if(in_array('paidsupport', majesticsupport::$_active_addons) && class_exists('WooCommerce')){
                $MJTC_linktickettoorder = true;
                if(majesticsupport::$_data[0]->paidsupportitemid > 0){
                    $MJTC_paidsupport = MJTC_includer::MJTC_getModel('paidsupport')->getPaidSupportDetails(majesticsupport::$_data[0]->paidsupportitemid);
                    if($MJTC_paidsupport){
                        $MJTC_linktickettoorder = false;
                        $MJTC_nonpreminumsupport = in_array(majesticsupport::$_data[0]->id,$MJTC_paidsupport['ignoreticketids']) ? 1 : 0;
                        $MJTC_agentallowed = in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff() && MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForTask('Mark Non Premium');
                        ?>
                        <div class="mjtc-sprt-det-cnt mjtc-sprt-det-pdsprt">
                            <?php if(!$MJTC_nonpreminumsupport || $MJTC_agentallowed){ ?>
                            <div class="mjtc-sprt-det-hdg">
                                <div class="mjtc-sprt-det-hdg-txt">
                                    <?php echo esc_html(__("Paid Support Details",'majestic-support')); ?>
                                </div>
                            </div>
                            <?php } ?>
                            <?php if(!$MJTC_nonpreminumsupport){ ?>
                            <div class="mjtc-sprt-wc-order-box">
                                <div class="mjtc-sprt-wc-order-item">
                                    <div class="mjtc-sprt-wc-order-item-title"><?php echo esc_html(__("Order",'majestic-support')); ?>:</div>
                                    <div class="mjtc-sprt-wc-order-item-value">#<?php echo esc_html($MJTC_paidsupport['orderid']); ?></div>
                                </div>
                                <div class="mjtc-sprt-wc-order-item">
                                    <div class="mjtc-sprt-wc-order-item-title"><?php echo esc_html(__("Product Name",'majestic-support')); ?>:
                                    </div>
                                    <div class="mjtc-sprt-wc-order-item-value"><?php echo esc_html($MJTC_paidsupport['itemname']); ?></div>
                                </div>
                                <div class="mjtc-sprt-wc-order-item">
                                    <div class="mjtc-sprt-wc-order-item-title"><?php echo esc_html(__("Total Tickets",'majestic-support')); ?>:
                                    </div>
                                    <div class="mjtc-sprt-wc-order-item-value">
                                        <?php
                                        if($MJTC_paidsupport['totalticket']==-1){
                                            echo esc_html(__("Unlimited",'majestic-support'));
                                        } else {
                                            echo esc_html($MJTC_paidsupport['totalticket']);
                                        }
                                        ?>
                                    </div>
                                </div>
                                <div class="mjtc-sprt-wc-order-item">
                                    <div class="mjtc-sprt-wc-order-item-title">
                                        <?php echo esc_html(__("Remaining Tickets",'majestic-support')); ?>:
                                    </div>
                                    <div class="mjtc-sprt-wc-order-item-value">
                                        <?php
                                        if($MJTC_paidsupport['totalticket']==-1){
                                            echo esc_html(__("Unlimited",'majestic-support'));
                                        } else {
                                            echo esc_html($MJTC_paidsupport['remainingticket']);
                                        }
                                        ?>
                                    </div>
                                </div>
                                <?php if(isset($MJTC_paidsupport['subscriptionid'])){ ?>
                                <div class="mjtc-sprt-wc-order-item">
                                    <div class="mjtc-sprt-wc-order-item-title"><?php echo esc_html(__("Subscription",'majestic-support')); ?>:
                                    </div>
                                    <div class="mjtc-sprt-wc-order-item-value">#<?php echo esc_html($MJTC_paidsupport['subscriptionid']); ?>
                                    </div>
                                </div>
                                <?php } ?>
                                <?php if(isset($MJTC_paidsupport['subscriptionstartdate'])){ ?>
                                <div class="mjtc-sprt-wc-order-item">
                                    <div class="mjtc-sprt-wc-order-item-title"><?php echo esc_html(__("Subscribed On",'majestic-support')); ?>:
                                    </div>
                                    <div class="mjtc-sprt-wc-order-item-value">
                                        <?php echo esc_html(date_i18n("F d, Y, H:i:s", MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_paidsupport['subscriptionstartdate']))); ?>
                                    </div>
                                </div>
                                <?php } ?>
                                <?php if(isset($MJTC_paidsupport['expiry'])){ ?>
                                <div class="mjtc-sprt-wc-order-item">
                                    <div class="mjtc-sprt-wc-order-item-title"><?php echo esc_html(__("Support Expiry",'majestic-support')); ?>:
                                    </div>
                                    <div class="mjtc-sprt-wc-order-item-value">
                                        <?php
                                        if($MJTC_paidsupport['expiry']){
                                             echo esc_html(date_i18n("F d, Y", MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_paidsupport['expiry'])));
                                        } else {
                                        echo  esc_html(__("No expiration",'majestic-support'));
                                        }
                                        ?>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                            <?php } ?>

                            <?php
                            // non-premium section
                            // show only if agent and has permission to mark ticket as non-premium
                            if($MJTC_agentallowed){
                                ?>
                            <div class="mjtc-sprt-wc-order-box">
                                <div class="mjtc-sprt-wc-order-item">
                                    <label>
                                        <input type="checkbox" id="nonpreminumsupport" <?php if($MJTC_nonpreminumsupport){ echo esc_attr('checked');} ?>>
                                        <b><?php echo esc_html(__("Non-premium support",'majestic-support')); ?></b>
                                    </label>
                                    <?php echo wp_kses(MJTC_formfield::MJTC_hidden('paidsupportitemid',majesticsupport::$_data[0]->paidsupportitemid), MJTC_ALLOWED_TAGS) ?>
                                    <div>
                                        <small><i><?php echo esc_html(__("Check this box if this ticket should NOT apply against the paid support",'majestic-support')); ?></i></small>
                                    </div>
                                </div>
                            </div>
                            <?php
                            }
                            ?>
                        </div>
                        <?php
                    }
                }
                if($MJTC_linktickettoorder && in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff() && MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForTask('Link To Paid Support')){
                    $MJTC_paidsupportitems = MJTC_includer::MJTC_getModel('paidsupport')->getPaidSupportList(majesticsupport::$_data[0]->uid);
                    $MJTC_paidsupportlist = array();
                    foreach($MJTC_paidsupportitems as $MJTC_row){
                        $MJTC_paidsupportlist[] = (object) array(
                            'id' => $MJTC_row->itemid,
                            'text' => esc_html(__("Order",'majestic-support')).' #'.esc_html($MJTC_row->orderid).', '.esc_html($MJTC_row->itemname).', '.esc_html(__("Remaining",'majestic-support')).':'.esc_html($MJTC_row->remaining).' '.esc_html(__("Out of",'majestic-support')).':'.esc_html($MJTC_row->total),
                        );
                    }
                    ?>
                    <div class="mjtc-sprt-det-cnt mjtc-sprt-det-pdsprt">
                        <div class="mjtc-sprt-det-hdg">
                            <div class="mjtc-sprt-det-hdg-txt">
                                <?php echo esc_html(__("Paid Support",'majestic-support')).': '.esc_html(__("Link ticket to paid support",'majestic-support')); ?>
                            </div>
                        </div>
                        <div class="mjtc-sprt-wc-order-box">
                            <div class="mjtc-sprt-wc-order-item">
                                <?php echo wp_kses(MJTC_formfield::MJTC_select('paidsupportitemid',$MJTC_paidsupportlist,null,esc_html(__("Select",'majestic-support'))), MJTC_ALLOWED_TAGS); ?>
                                <button type="button" class="btn"
                                    id="paidsupportlinkticketbtn"><?php echo esc_html(__("Link",'majestic-support')); ?></button>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } ?>
            <?php apply_filters('mjtc_support_ticket_admin_details_right_last', majesticsupport::$_data[0]->id); ?>

        </aside>

        </div>
    </div> <!-- End Main Wrapper -->
        
        <?php
    } else { // Record Not FOund
        MJTC_layout::MJTC_getNoRecordFound();
    }
} else {// User is permission
    MJTC_layout::MJTC_getPermissionNotGranted();
}
    } else {// User is guest
        $MJTC_redirect_url = majesticsupport::makeUrl(array('mjsmod'=>'ticket','mjslay'=>'ticketdetail'));
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
<?php
$majesticsupport_js = "
    jQuery(document).ready(function() {
        // show save status button
        jQuery('#status').change(function() {
            jQuery('#changestatus').fadeIn(500);
        });
        // show save status button

        jQuery('#prioritytemp').change(function () {
            jQuery('#changepriority').fadeIn(500);
        });
        // show save status button
        jQuery('#mjtc-public-reply-wrp').css('display', 'flex'); // Show timer for note
        jQuery('#mjtc-private-note-wrp').hide();

        // Toggle AI Suggestions Panel
        jQuery('#mjtc-support-ai-reply-btn').on('click', function() {
            const panel = jQuery('#mjtc-ai-suggestions-panel');
            panel.slideToggle(); // Toggle visibility with a slide effect
        });

        // Tab Switching Logic
        jQuery('.mjtc-support-header-tabs .mjtc-support-tab-btn').on('click', function() {
            const tab = jQuery(this).data('tab');
            
            // Toggle active class
            jQuery('.mjtc-support-header-tabs .mjtc-support-tab-btn').removeClass('active');
            jQuery(this).addClass('active');

            // Hide all first
            jQuery('#message-container, #mjtc-support-history-container, #mjtc-support-saved-notes-container, #mjtc-support-credentials-container').hide();
            
            // Show selected
            if (tab == 'conversation') {
                jQuery('#mjtc-support-saved-notes-container, #mjtc-support-history-container, #mjtc-support-credentials-container').hide();
                jQuery('#message-container, #reply-container, #reply-container #mjtc-public-reply-wrp, #mjtc-reply-tab').show();
                jQuery('#reply-container #mjtc-private-note-wrp, #mjtc-note-tab').hide();
            } else if (tab == 'saved-notes') {
                jQuery('#message-container, #mjtc-support-history-container, #mjtc-support-credentials-container').hide();
                jQuery('#mjtc-support-saved-notes-container, #reply-container, #reply-container #mjtc-private-note-wrp, #mjtc-note-tab').show();
                jQuery('#reply-container #mjtc-public-reply-wrp, #mjtc-reply-tab').hide();
            } else if (tab == 'history') {
                jQuery('#message-container, #reply-container, #mjtc-support-saved-notes-container, #mjtc-support-credentials-container').hide();
                jQuery('#mjtc-support-history-container').show();
            } else if (tab == 'credentials') {
                jQuery('#message-container, #reply-container, #mjtc-support-saved-notes-container, #mjtc-support-history-container').hide();
                jQuery('#mjtc-support-credentials-container').show();
            }
        });

        // Mode Switching (Public vs Internal Note)
        jQuery('.mjtc-support-reply-tab-btn').on('click', function() {
            const mode = jQuery(this).data('mode');
            
            // 1. Toggle Active Tab state
            jQuery('.mjtc-support-reply-tab-btn').removeClass('active');
            jQuery(this).addClass('active');

            // 2. Toggle Container Class (Controls colors via CSS)
            jQuery('#reply-container').removeClass('mode-public mode-note').addClass('mode-' + mode);
            
            // 3. Update Button Text & Toggle Timer Field
            if (mode === 'note') {
                jQuery('#mjtc-note-timer-field').css('display', 'flex'); // Show timer for note
                jQuery('#mjtc-public-reply-wrp').hide();
                jQuery('#mjtc-private-note-wrp').css('display', 'flex');
            } else {
                jQuery('#mjtc-note-timer-field').hide(); // Hide timer for public reply
                jQuery('#mjtc-private-note-wrp').hide();
                jQuery('#mjtc-public-reply-wrp').css('display', 'flex');
            }
        });
        // Simple Focus Effect removal on input
        jQuery('#reply-box').on('focus', function() {
            jQuery(this).css('box-shadow', ''); 
        });
    });
";
wp_add_inline_script('majestic-support-cmain-js',$majesticsupport_js);
?>
