<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

MJTC_message::MJTC_getMessage();
wp_enqueue_script('majesticsupport-file_validate.js', MJTC_PLUGIN_URL . 'includes/js/file_validate.js');
wp_enqueue_script('jquery-ui-tabs');
wp_enqueue_style('majesticsupport-jquery-ui-css', MJTC_PLUGIN_URL . 'includes/css/jquery-ui-smoothness.css');
wp_enqueue_script('majesticsupport-timer.js', MJTC_PLUGIN_URL . 'includes/js/timer.jquery.js');
wp_enqueue_style('majesticsupport-venobox-css', MJTC_PLUGIN_URL . 'includes/css/venobox.css');
wp_enqueue_script('majesticsupport-venoboxjs',MJTC_PLUGIN_URL.'includes/js/venobox.js');
if (in_array('aipoweredreply', majesticsupport::$_active_addons)){
    $mjsmod = 'aipoweredreply';
    $jstreplymod = 'aipoweredreply';
} else {
    $mjsmod = 'ticket';
    $jstreplymod = 'reply';
}
$majesticsupport_js ="
    var timer_flag = 0;
    var seconds = 0;
    function checktinymcebyid(id) {
        var content = tinymce.get(id).getContent({format: 'text'});
        if (jQuery.trim(content) == '')
        {
            alert('". esc_html(__('Some values are not acceptable please retry', 'majestic-support')) ."');
            return false;
        }
        return true;
    }

    function getpremade(val) {
        jQuery.post(ajaxurl, {action: 'mjsupport_ajax', val: val, mjsmod: 'cannedresponses', task: 'getpremadeajax', '_wpnonce':'". esc_attr(wp_create_nonce("get-premade-ajax")) ."'}, function (data) {
            if (data) {
                var append = jQuery('input#append_premade1:checked').length;
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
    // Temporary storage for the current ticket's replies for filtering
    let currentTicketAllReplies = [];
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

    jQuery(document).ready(function ($) {
        jQuery( 'form' ).submit(function(e) {
            if(timer_flag != 0){
                jQuery('input#timer_time_in_seconds').val(jQuery('div.timer').data('seconds'));
            }
        });
        jQuery('#tabs').tabs();
        jQuery('#tk_attachment_add').click(function () {
            var obj = this;
            var att_flag = jQuery(this).attr('data-ident');
            var parentElement = jQuery(this).closest('.mjtc-form-field');
            jQuery(parentElement).addClass('mjtc-form-field-selected');
            var current_files = jQuery('div.mjtc-form-field-selected').find('.tk_attachment_value_text').length;
            var total_allow =". esc_attr(majesticsupport::$_config['no_of_attachement']) .";
            var append_text = '<span class=\"tk_attachment_value_text\"><input name=\"filename[]\" type=\"file\" onchange=\"MJTC_uploadfile(this,\"". esc_js(majesticsupport::$_config['file_maximum_size'])."\",\"". esc_js(majesticsupport::$_config['file_extension'])."\");\" size=\"20\" maxlenght=\"30\"  /><span  class=\"tk_attachment_remove\"></span></span>';
            if (current_files < total_allow) {
                jQuery('.tk_attachment_value_wrapperform.' + att_flag).append(append_text);
            } else if ((current_files === total_allow) || (current_files > total_allow)) {
                alert('". esc_html(__('File upload limit exceeds', 'majestic-support')) ."');
                obj.hide();
            }
        });
        jQuery(document).delegate('.tk_attachment_remove', 'click', function (e) {
            jQuery(this).parent().remove();
            var current_files = jQuery('input[type=\"file\"]').length;
            var total_allow =". esc_attr(majesticsupport::$_config['no_of_attachement']) .";
            if (current_files < total_allow) {
                jQuery('#tk_attachment_add').show();
            }
        });
        jQuery('a#showhidedetail').click(function (e) {
            e.preventDefault();
            var divid = jQuery(this).attr('data-divid');
            jQuery('div#' + divid).slideToggle();
            jQuery(this).find('img').toggleClass('mjtc-hidedetail');
        });

        var height = jQuery(window).height();
        jQuery('a#showhistory').click(function (e) {
            e.preventDefault();
            jQuery('div#userpopup').slideDown('slow');
            jQuery('div#userpopupblack').show();
        });
        jQuery('a#int-note').click(function (e) {
            e.preventDefault();
            jQuery('div#internalnotes-popup').slideDown('slow');
            jQuery('div#internalnotespopupblack').show();
        });
        jQuery('.internalnotespopup-close, div#internalnotespopupblack').click(function (e) {
            jQuery('div#internalnotes-popup').slideUp('slow', function () {
                jQuery('div#internalnotespopupblack').hide();
            });

        });
        jQuery('a#chng-status').click(function (e) {
            e.preventDefault();
            jQuery('div#changestatus-popup').slideDown('slow');
            jQuery('div#userpopupblack').show();
        });
        jQuery('a#chng-prority').click(function (e) {
            e.preventDefault();
            jQuery('div#changepriority-popup').slideDown('slow');
            jQuery('div#userpopupblack').show();
        });
        jQuery('.userpopup-close, div#userpopupblack').click(function (e) {
            jQuery('div#changestatus-popup').slideUp('slow', function () {
                jQuery('div#userpopupblack').hide();
            });
            jQuery('div#changepriority-popup').slideUp('slow', function () {
                jQuery('div#userpopupblack').hide();
            });
        });
        jQuery('a#chng-dept').click(function (e) {
            e.preventDefault();
            jQuery('div#changedept-popup').slideDown('slow');
            jQuery('div#userpopupblack').show();
        });
        jQuery('.userpopup-close, div#userpopupblack').click(function (e) {
            jQuery('div#changedept-popup').slideUp('slow', function () {
                jQuery('div#userpopupblack').hide();
            });

        });
        jQuery('a#asgn-staff').click(function (e) {
            e.preventDefault();
            jQuery('div#assignstaff-popup').slideDown('slow');
            jQuery('div#userpopupblack').show();
        });
        jQuery('.userpopup-close, div#userpopupblack').click(function (e) {
            jQuery('div#assignstaff-popup').slideUp('slow', function () {
                jQuery('div#userpopupblack').hide();
            });

        });
        jQuery(document).delegate('.close-merge', 'click', function (e) {
            jQuery('div#mergeticketselection').fadeOut();
            jQuery('div#popup-record-data').html('');
        });

        jQuery('div#userpopupblack,div.ms-popup-background,.close-history,.close-credentails').click(function (e) {
            jQuery('div#userpopup').slideUp('slow');
            jQuery('#usercredentailspopup').slideUp('slow');
            setTimeout(function () {
                jQuery('div#userpopupblack').hide();
                jQuery('div.ms-popup-background').hide();
            }, 700);
        });
        ";
        //print code
        if(isset(majesticsupport::$_data[0])){
            $majesticsupport_js .="
            jQuery('a#print-link').click(function (e) {
                e.preventDefault();
                var href = '". majesticsupport::makeUrl(array('mjsmod'=>'ticket','mjslay'=>'printticket','majesticsupportid'=>majesticsupport::$_data[0]->id,'mspageid'=>majesticsupport::getPageid())) ."';
                print = window.open(href, 'print_win', 'width=1024, height=800, scrollbars=yes');
            }); ";
        }
        $majesticsupport_js .="
        jQuery(document).delegate('#ticketpopupsearch','submit', function (e) {
            var ticketid = jQuery('#ticketidformerge').val();
            var nonce = jQuery('#nonce').val();
            e.preventDefault();
            var name = jQuery('input#name').val();
            var email = jQuery('input#email').val();
            jQuery.post(ajaxurl, {action: 'mjsupport_ajax', mjsmod: 'mergeticket', task: 'getTicketsForMerging', name: name, email: email,ticketid:ticketid, '_wpnonce': nonce}, function (data) {
                data=jQuery.parseJSON(data);
               if(data !== 'undefined' && data !== '') {
                    jQuery('div#popup-record-data').html('');
                    jQuery('div#popup-record-data').html(MJTC_msDecodeHTML(data['data']));
                }else{
                    jQuery('div#popup-record-data').html('');
                }
            });//jquery closed
        });

        jQuery(document).delegate('#ticketidcopybtn', 'click', function(){
            var temp = jQuery('<input>');
            jQuery('body').append(temp);
            temp.val(jQuery('#ticketrandomid').val()).select();
            document.execCommand('copy');
            temp.remove();
            jQuery('#ticketidcopybtn').text(jQuery('#ticketidcopybtn').attr('success'));
        });

        //non premium support function
        jQuery('#nonpreminumsupport').change(function(){
            if(jQuery(this).is(':checked')){
                if(1 || confirm(\"". esc_html(__('Are you sure to mark this ticket non-premium?','majestic-support')) ."\")){
                    markUnmarkTicketNonPremium(1);
                }else{
                    jQuery(this).removeAttr('checked');
                }
            }else{
                markUnmarkTicketNonPremium(0);
            }
        });

        jQuery('#paidsupportlinkticketbtn').click(function(){
            var ticketid = jQuery('#ticketid').val();
            var paidsupportitemid = jQuery('#paidsupportitemid').val();
            if(paidsupportitemid > 0){
                jQuery.post(ajaxurl, {action: 'mjsupport_ajax',mjsmod: 'paidsupport', task: 'linkTicketPaidSupportAjax', ticketid: ticketid, paidsupportitemid:paidsupportitemid, '_wpnonce':'". esc_attr(wp_create_nonce("link-ticket-paidsupport-ajax")) ."'}, function (data) {
                    window.location.reload();
                });
            }
        });";
    wp_add_inline_script('majestic-support-cmain-js',$majesticsupport_js);

    // AI-Powered Reply
    $majesticsupport_js ='
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
                    showModal("'.__("Copied to clipboard!", "majestic-support").'");    
                } else {
                    showModal("'.__("Failed to copy!", "majestic-support").'");
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
            const activeTicketItem = matchingTicketsList.find(".mjtc-support-list-item.active");
            
            if (!activeTicketItem.length) {
                showModal("'.__("No ticket selected!", "majestic-support").'");
                return;
            }
            
            const ticketId = activeTicketItem.data("ticket-id");
            const type = activeTicketItem.data("type");
            const ticketTitle = activeTicketItem.find(".mjtc-support-title").text();
            
            // Show loading message
            jsReplyShowLoading();
            
            // Fetch replies based on filter and ticket ID
            console.log(selectedFilter);
            jQuery.post(ajaxurl, {
                action: "mjsupport_ajax",
                mjsmod: "'.$jstreplymod.'",
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
        matchingTicketsList.on("click", ".mjtc-support-list-item", function() {
            // Remove active class from all items
            matchingTicketsList.find(".mjtc-support-list-item").removeClass("active");
            
            // Add active class to clicked item
            const listItem = jQuery(this);
            listItem.addClass("active");
            
            // const ticketId1 = activeTicketItem.data("ticket-id");
            const ticketId = listItem.data("ticket-id");
            const type = listItem.data("type");
            const id = listItem.data("id");
            const ticketTitle = listItem.find(".mjtc-support-title").text();
            
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
                    mjsmod: "'.$jstreplymod.'",
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
            jQuery.post(ajaxurl, {action: "mjsupport_ajax", ticketSubject: ticketSubject, ticketId: ticketId, filter: selectedFilter, mjsmod: "'.$mjsmod.'", task: "checkAIReplyTicketsBySubject", "_wpnonce":"'. esc_attr(wp_create_nonce("check-smart-reply")).'"}, function (data) {
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
                matchingTicketsList.html(`<p class="mjtc-support-id">'.__("No matching tickets found.", "majestic-support").'</p>`);
                matchingTicketsSection.removeClass("mjtc-support-hidden");
                jsReplyHideLoading();
                matchingTicketsSection.removeClass("mjtc-support-hidden");
                return;
            }

            $.each(matchingTickets, (index, ticket) => {

                // Choose ID label dynamically
                let idLabel = "";
                let idValue = "";

                if (ticket.type === "smart_reply") {
                    idLabel = "'.__("Smart Reply ID:", "majestic-support").'";
                    idValue = ticket.id;
                } else {
                    idLabel = "'.__("Ticket ID:", "majestic-support").'";
                    idValue = ticket.ticketid;
                }

                const listItem = $("<li></li>")
                    .addClass("mjtc-support-list-item")
                    .data("ticket-id", ticket.id) // Store ticket ID in data attribute
                    .data("type", ticket.type) // Store type in data attribute
                    .html(`
                        <p class="mjtc-support-id">${idLabel} ${idValue}</p>
                        <p class="mjtc-support-title">${ticket.text}</p>
                        <p class="mjtc-support-id">${ticket.message}</p>
                    `);

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
                jQuery("#mjtc-support-selected-ticket-replies-title").text(`'.__("Replies for:", "majestic-support").' ${ticket.text}`);
            } else {
                jQuery("#mjtc-support-selected-ticket-replies-title").text(`'.__("Smart Reply for:", "majestic-support").' ${ticket.text}`);
            }
            selectedTicketRepliesContent.empty(); // Clear previous replies

            // Now safe to check length
            if (replies.length === 0) {
                selectedTicketRepliesContent.html(`<p class="mjtc-support-id">'.__("No replies found for this ticket.", "majestic-support").'</p>`);
            } else {
                $.each(replies, (index, reply) => {
                    if (type === "ticket") {
                        labelText = "'.__("Reply By:", "majestic-support").'";
                        nameValue = reply?.name || "'.__("Unknown", "majestic-support").'";
                    } else {
                        labelText = "'.__("Used By:", "majestic-support").'";
                        nameValue = reply?.usedby || "'.__("Unknown", "majestic-support").'";
                    }
                    // Add null checks for reply properties
                    const replyId = reply?.id || __("N/A", "majestic-support");
                    const replyText = reply?.text || "'.__("No content", "majestic-support").'"; 
                    const replyTimestamp = reply?.timestamp ? new Date(reply.timestamp).toLocaleString() : "'.__("No date", "majestic-support").'";

                    const replyDiv = $("<div></div>")
                        .addClass("mjtc-support-reply-item")
                        .html(`
                            <div class="mjtc-support-reply-header">
                                <span class="mjtc-support-reply-id">${labelText} ${escapeHtml(nameValue)}</span>
                                <span class="mjtc-support-reply-timestamp">${replyTimestamp}</span>
                            </div>
                            <div class="mjtc-support-reply-text">
                                ${(replyText)}
                            </div>
                            <div class="mjtc-support-reply-actions">
                                <button class="mjtc-support-reply-action-btn copy-btn" data-reply-content="${escapeHtml(replyText)}">'.__('Copy', 'majestic-support').'</button>
                                <button class="mjtc-support-reply-action-btn append-btn" data-reply-content="${escapeHtml(replyText)}">'.__('Append', 'majestic-support').'</button>
                            </div>
                        `);
                    selectedTicketRepliesContent.append(replyDiv);
                });

                // Attach event listeners
                selectedTicketRepliesContent.find(".copy-btn").on("click", function(e) {
                    e.preventDefault();
                    copyToClipboard($(this).data("reply-content"));
                });
                
                selectedTicketRepliesContent.find(".append-btn").on("click", function(e) {
                    e.preventDefault();
                    appendToReplyArea($(this).data("reply-content"));
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
    function markUnmarkTicketNonPremium(mark){
        var ticketid = jQuery('#ticketid').val();
        var paidsupportitemid = jQuery('#paidsupportitemid').val();
        jQuery.post(ajaxurl, {action: 'mjsupport_ajax',mjsmod: 'paidsupport', task: 'markUnmarkTicketNonPremiumAjax', status: mark, ticketid: ticketid, paidsupportitemid:paidsupportitemid, '_wpnonce':'". esc_attr(wp_create_nonce("mark-unmark-ticket-nonpremium-ajax")) ."'}, function (data) {
            window.location.reload();
        });
    }

    function actionticket(action) {
        /*  Action meaning
         * 1 -> Change Priority
         * 2 -> Close Ticket
         */
        if(action == 1){
            jQuery('#priority').val(jQuery('#prioritytemp').val());
        }
        jQuery('input#actionid').val(action);
        jQuery('form#adminTicketform').submit();
    }
    function getmergeticketid(mergeticketid, mergewithticketid, mergeNonce){
        if(mergewithticketid == 0){
            mergewithticketid =  jQuery('#mergeticketid').val();
        }else{
            jQuery('#mergeticketid').val(mergewithticketid);
        }
        if(mergeticketid == mergewithticketid){
            alert(\"Primary id must be differ from merge ticket id\");
            return false;
        }
        jQuery('#mergeticketselection').hide();
        getTicketdataForMerging(mergeticketid,mergewithticketid, mergeNonce);
    }

    function getTicketdataForMerging(mergeticketid,mergewithticketid, mergeNonce){
        jQuery.post(ajaxurl, {action: 'mjsupport_ajax',mjsmod: 'mergeticket', task: 'getLatestReplyForMerging', mergeid:mergeticketid,mergewith:mergewithticketid,isadmin:1, '_wpnonce': mergeNonce}, function (data) {
            if(data){
                data=jQuery.parseJSON(data);
                jQuery('div#popup-record-data').html('');
                jQuery('div#popup-record-data').html(MJTC_msDecodeHTML(data['data']));
            }
        });
    }

    function closePopup(){
        setTimeout(function () {
            jQuery('div.ms-popup-background').hide();
            jQuery('div#userpopupblack').hide();
            }, 700);

        jQuery('div.ms-popup-wrapper').slideUp('slow');
        jQuery('div#userpopupforchangestatus').slideUp('slow');
        jQuery('div#userpopupforchangepriority').slideUp('slow');
        jQuery('div#userpopup').slideUp('slow');


    }
    function updateticketlist(pagenum,ticketid,nonce){
        jQuery.post(ajaxurl, {action: 'mjsupport_ajax',mjsmod: 'mergeticket', task: 'getTicketsForMerging', ticketid:ticketid,ticketlimit:pagenum, '_wpnonce': nonce}, function (data) {
            if(data){
                console.log(data);
                data=jQuery.parseJSON(data);
                jQuery('div#popup-record-data').html('');
                jQuery('div#popup-record-data').html(MJTC_msDecodeHTML(data['data']));
            }
        });
    }

    function showPopupAndFillValues(id,pfor,nonce) {
        if(pfor == 1){
            jQuery.post(ajaxurl, {action: 'mjsupport_ajax', val: id, mjsmod: 'reply', task: 'getReplyDataByID', '_wpnonce': nonce}, function (data) {
                if (data) {
                    d = jQuery.parseJSON(data);
                    tinyMCE.get('mjsupport_replytext').execCommand('mceSetContent', false, d.message);
                    jQuery('div.ms-merge-popup-wrapper div.userpopup-heading').html(\"". esc_html(__("Edit Reply",'majestic-support'))."\");
                    jQuery('form#ms-time-edit-form').hide();
                    jQuery('form#ms-note-edit-form').hide();
                    jQuery('div.edit-time-popup').hide();
                    jQuery('form#ms-reply-form').show();
                    jQuery('input#reply-replyid').val(id);
                    jQuery('div.ms-popup-background').show();
                    jQuery('div.ms-merge-popup-wrapper').slideDown('slow');
                }
            });
        }else if(pfor == 2){
            jQuery.post(ajaxurl, {action: 'mjsupport_ajax', val: id, mjsmod: 'timetracking', task: 'getTimeByReplyID', '_wpnonce': nonce}, function (data) {
                if (data) {
                    d = jQuery.parseJSON(data);
                    jQuery('div.ms-merge-popup-wrapper div.userpopup-heading').html(\"". esc_html(__("Edit Time",'majestic-support'))."\");
                    jQuery('form#ms-reply-form').hide();
                    jQuery('form#ms-note-edit-form').hide();
                    jQuery('div.system-time-div').hide();
                    jQuery('div.edit-time-popup').hide();
                    jQuery('form#ms-time-edit-form').show();
                    jQuery('input#reply-replyid').val(id);
                    jQuery('div.ms-popup-background').show();
                    jQuery('div.ms-merge-popup-wrapper').slideDown('slow');
                    jQuery('input#edited_time').val(d.time);
                    jQuery('textarea#edit_reason').text(d.desc);
                    if(d.conflict == 1){
                        jQuery('div.system-time-div').show();
                        jQuery('input#time-confilct').val(d.conflict);
                        jQuery('input#systemtime').val(d.systemtime);
                        jQuery('select#time-confilct-combo').val(0);
                    }
                }
            });
        }else if(pfor == 3){
            jQuery.post(ajaxurl, {action: 'mjsupport_ajax', val: id, mjsmod: 'note', task: 'getTimeByNoteID', '_wpnonce': nonce}, function (data) {
                if (data) {
                    d = jQuery.parseJSON(data);
                    jQuery('div.ms-merge-popup-wrapper div.userpopup-heading').html(\"". esc_html(__("Edit Time",'majestic-support'))."\");
                    jQuery('form#ms-reply-form').hide();
                    jQuery('form#ms-note-edit-form').show();
                    jQuery('form#ms-time-edit-form').hide();
                    jQuery('div.system-time-div').hide();
                    jQuery('div.edit-time-popup').hide();
                    jQuery('input#note-noteid').val(id);
                    jQuery('div.ms-popup-background').show();
                    jQuery('div.ms-merge-popup-wrapper').slideDown('slow');
                    jQuery('input#edited_time').val(d.time);
                    jQuery('textarea#edit_reason').text(d.desc);
                    if(d.conflict == 1){
                        jQuery('div.system-time-div').show();
                        jQuery('input#time-confilct').val(d.conflict);
                        jQuery('input#systemtime').val(d.systemtime);
                        jQuery('select#time-confilct-combo').val(0);
                    }
                }
            });
        }else if(pfor == 4){
            jQuery.post(ajaxurl, {action: 'mjsupport_ajax', ticketid: id, mjsmod: 'mergeticket', task: 'getTicketsForMerging', '_wpnonce': nonce}, function (data) {
                if (data) {
                    data=jQuery.parseJSON(data);
                    jQuery('div.ms-merge-popup-wrapper div.userpopup-heading').html(\"". esc_html(__("Merge Ticket",'majestic-support'))."\");
                    jQuery('div#popup-record-data').html('');
                    jQuery('div#popup-record-data').html(MJTC_msDecodeHTML(data['data']));

                }
            });
        }

         return false;
    }

    function changeTimerStatus(val) {
        if(timer_flag == 2){// to handle stopped timer
                return;
        }
        if(!jQuery('span.timer-button.cls_'+val).hasClass('selected')){
            jQuery('span.timer-button').removeClass('selected');
            jQuery('span.timer-button.cls_'+val).addClass('selected');
            if(val == 1){
                if(timer_flag == 0){
                    jQuery('div.timer').timer({format: '%H:%M:%S'});
                }
                timer_flag = 1;
                jQuery('div.timer').timer('resume');
            }else if(val == 2) {
                 jQuery('div.timer').timer('pause');
            }else{
                 jQuery('div.timer').timer('remove');
                timer_flag = 2;
            }
        }
    }

    function showEditTimerPopup(){
        jQuery('form#ms-time-edit-form').hide();
        jQuery('form#ms-reply-form').hide();
        jQuery('form#ms-note-edit-form').hide();
        jQuery('div.edit-time-popup').show();
        jQuery('span.timer-button').removeClass('selected');
        if(timer_flag != 0){
            jQuery('div.timer').timer('pause');
        }
        ex_val = jQuery('div.timer').html();
        jQuery('input#edited_time').val('');
        jQuery('input#edited_time').val(ex_val.trim());
        jQuery('div.ms-popup-background').show();
        jQuery('div.ms-merge-popup-wrapper').slideDown('slow');
        jQuery('div.ms-merge-popup-wrapper div.userpopup-heading').html(\"". esc_html(__("Edit Time",'majestic-support'))."\");
    }

    function updateTimerFromPopup(){
        val = jQuery('input#edited_time').val();
        arr = val.split(':', 3);
        jQuery('div.timer').html(val);
        jQuery('div.ms-popup-background').hide();
        jQuery('div.ms-popup-wrapper').slideUp('slow');
        seconds = parseInt(arr[0])*3600 + parseInt(arr[1])*60 + parseInt(arr[2]);
        if(seconds < 0){
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

    function showTicketCloseReasons(id){
        jQuery('div.ms-popup-other-reason-box').hide();
        jQuery.post(ajaxurl, {action: 'mjsupport_ajax', mjsmod: 'ticketclosereason', task: 'getTicketCloseReasonsForPopup',isadmin:1,id:id, '_wpnonce':'". esc_attr(wp_create_nonce("get-ticket-close-reasons-for-popup"))."'}, function (data) {
            if(data){
                data=jQuery.parseJSON(data);
                jQuery('div#popup-record-data').html('');
                jQuery('div#popup-record-data').html(MJTC_msDecodeHTML(data['data']));
            }
        });
    }

    jQuery('div.popup-header-close-img,div.ms-popup-background,input#cancel').click(function (e) {
        jQuery('div.ms-popup-wrapper:not(#internalnotes-popup)').slideUp('slow');
        jQuery('div.ms-merge-popup-wrapper').slideUp('slow');
        setTimeout(function () {
            jQuery('div.ms-popup-background').hide();
        }, 700);
    });

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

    function resetMergeFrom(nonce) {
        var ticketid = jQuery('#ticketidformerge').val();
        var name = '';
        var email = '';
        jQuery.post(ajaxurl, {action: 'mjsupport_ajax', mjsmod: 'mergeticket', task: 'getTicketsForMerging', name: name, email: email,ticketid:ticketid, '_wpnonce': nonce}, function (data) {
            data=jQuery.parseJSON(data);
           if(data !== 'undefined' && data !== '') {
                jQuery('div#popup-record-data').html('');
                jQuery('div#popup-record-data').html(MJTC_msDecodeHTML(data['data']));
            }else{
                jQuery('div#popup-record-data').html('');
            }
        });//jquery closed
    }

    // smooth scroll
    jQuery(document).ready(function(){
        jQuery('a.smooth-scroll').on('click', function(e) {
            e.preventDefault();
            var anchor = jQuery(this);
            jQuery('html, body').stop().animate({
                scrollTop: jQuery(anchor.attr('href')).offset().top - 10
            }, 1000);
        });
    })
    jQuery('span.mjtc-support-thread-read-status-wrp').hover(
        function(e){
            jQuery(this).find('span.mjtc-support-thread-read-status-detail').css('display','inline-block');
        },
        function(e){
            jQuery(this).find('span.mjtc-support-thread-read-status-detail').css('display','none');
        }
    );

";
wp_add_inline_script('majestic-support-cmain-js',$majesticsupport_js);
$MJTC_yesno = array(
    (object) array('id' => '1', 'text' => esc_html(__('Yes', 'majestic-support'))),
    (object) array('id' => '0', 'text' => esc_html(__('No', 'majestic-support')))
);
?>
<div id="black_wrapper_ai_reply" style="display:none;"></div>
<!-- add loading multiform -->
<div id="mjtc_ai_reply_loading">
    <img alt="<?php echo esc_html(__('spinning wheel','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/spinning-wheel.gif" />
</div>
<span style="display:none" id="filesize"><?php echo esc_html(__('Error file size too large', 'majestic-support')); ?></span>
<span style="display:none" id="fileext"><?php echo esc_html(__('The uploaded file extension not valid', 'majestic-support')); ?></span>
<div class="ms-popup-background" style="display:none" ></div>
<div id="popup-record-data" style="display:inline-block;width:100%;"></div>
<div id="userpopup" class="ms-popup-wrapper ms-merge-popup-wrapper" style="display:none" >
    <div class="userpopup-top" >
        <div class="userpopup-heading" >
            <?php echo esc_html(__('Edit Reply','majestic-support')); ?>
        </div>
        <img alt="<?php echo esc_html(__('Close','majestic-support')); ?>" class="close-history userpopup-close" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/close-icon-white.png" />
    </div>
    <div class="mjtc-admin-popup-cnt">
    <div class="edit-time-popup" style="display:none;" >
        <div class="mjtc-support-edit-form-wrp">
            <div class="mjtc-support-edit-form-row">
                <div class="mjtc-support-edit-field-title">
                    <?php echo esc_html(__('Time', 'majestic-support')); ?>&nbsp;<span style="color: red;" >*</span>
                </div>
                <div class="mjtc-support-edit-field-wrp">
                    <?php echo wp_kses(MJTC_formfield::MJTC_text('edited_time', '', array('class' => 'inputbox mjtc-support-edit-field-input')), MJTC_ALLOWED_TAGS) ?>
                </div>
            </div>
            <div class="mjtc-support-edit-form-row">
                <div class="mjtc-support-edit-field-title">
                    <?php echo esc_html(__('Reason For Editing the timer', 'majestic-support')); ?>
                </div>
                <div class="mjtc-support-edit-field-wrp">
                    <?php echo wp_kses(MJTC_formfield::MJTC_textarea('t_desc', '', array('class' => 'inputbox')), MJTC_ALLOWED_TAGS); ?>
                </div>
            </div>
            <div class="mjtc-support-priorty-btn-wrp">
                <?php echo wp_kses(MJTC_formfield::MJTC_submitbutton('ok', esc_html(__('Save', 'majestic-support')), array('class' => 'mjtc-support-priorty-save','onclick' => 'updateTimerFromPopup();')), MJTC_ALLOWED_TAGS); ?>
                <?php echo wp_kses(MJTC_formfield::MJTC_button('cancel', esc_html(__('Cancel', 'majestic-support')), array('class' => 'mjtc-support-priorty-cancel','onclick'=>'closePopup();')), MJTC_ALLOWED_TAGS); ?>
            </div>
        </div>
    </div>
    <?php $MJTC_nonce_id = isset(majesticsupport::$_data[0]->id) ? majesticsupport::$_data[0]->id : ''; ?>
    <form id="ms-reply-form" style="display:none" method="post" action="<?php echo esc_url(wp_nonce_url(admin_url("admin.php?page=majesticsupport_reply&task=saveeditedreply&action=mstask"),"save-edited-reply-".$MJTC_nonce_id)); ?>" >
        <div class="mjtc-form-wrapper-popup">
            <div class="mjtc-form-title-popup"><?php echo esc_html(__('Reply', 'majestic-support')); ?></div>
            <div class="mjtc-form-field-popup"><?php wp_editor('', 'mjsupport_replytext', array('media_buttons' => false,'editor_height' => 200, 'textarea_rows' => 20,)); ?></div>
        </div>
        <div class="mjtc-col-md-12 mjtc-form-button-wrapper">
            <?php echo wp_kses(MJTC_formfield::MJTC_submitbutton('ok', esc_html(__('Save', 'majestic-support')), array('class' => 'button')), MJTC_ALLOWED_TAGS); ?>
            <?php echo wp_kses(MJTC_formfield::MJTC_button('cancel', esc_html(__('Cancel', 'majestic-support')), array('class' => 'button', 'onclick'=>'closePopup();')), MJTC_ALLOWED_TAGS); ?>
        </div>
        <?php echo wp_kses(MJTC_formfield::MJTC_hidden('reply-replyid', ''), MJTC_ALLOWED_TAGS); ?>

        <?php
        if(isset(majesticsupport::$_data[0])){
            echo wp_kses(MJTC_formfield::MJTC_hidden('reply-tikcetid',majesticsupport::$_data[0]->id), MJTC_ALLOWED_TAGS);
        } ?>
    </form>
    <?php
    if(in_array('timetracking', majesticsupport::$_active_addons)){ ?>
        <form id="ms-time-edit-form" style="display:none" method="post" action="<?php echo esc_url(wp_nonce_url(admin_url("admin.php?page=majesticsupport_reply&task=saveeditedtime&action=mstask"),"save-edited-time-reply-".majesticsupport::$_data[0]->id)); ?>" >
            <div class="mjtc-form-wrapper-popup">
                <div class="mjtc-form-title-popup"><?php echo esc_html(__('Time', 'majestic-support')); ?></div>
                <div class="mjtc-form-field-popup"><?php echo wp_kses(MJTC_formfield::MJTC_text('edited_time', '', array('class' => 'inputbox')), MJTC_ALLOWED_TAGS) ?></div>
            </div>
            <div class="mjtc-form-wrapper-popup system-time-div" style="display:none;" >
                <div class="mjtc-form-title-popup"><?php echo esc_html(__('System Time', 'majestic-support')); ?></div>
                <div class="mjtc-form-field-popup"><?php echo wp_kses(MJTC_formfield::MJTC_text('systemtime', '', array('class' => 'inputbox','disabled'=>'disabled')), MJTC_ALLOWED_TAGS) ?></div>
            </div>
            <div class="mjtc-form-wrapper-popup">
                <div class="mjtc-form-title-popup"><?php echo esc_html(__('Reason For Editing', 'majestic-support')); ?></div>
                <div class="mjtc-form-field-popup"><?php echo wp_kses(MJTC_formfield::MJTC_textarea('edit_reason', '', array('class' => 'inputbox')), MJTC_ALLOWED_TAGS) ?></div>
            </div>
            <div class="mjtc-form-wrapper-popup system-time-div" style="display:none;" >
                <div class="mjtc-form-title-popup"><?php echo esc_html(__('Resolve conflict', 'majestic-support')); ?></div>
                <div class="mjtc-form-field-popup"><?php echo wp_kses(MJTC_formfield::MJTC_select('time-confilct-combo', $MJTC_yesno, ''), MJTC_ALLOWED_TAGS); ?></div>
            </div>
            <div class="mjtc-col-md-12 mjtc-form-button-wrapper">
                <?php echo wp_kses(MJTC_formfield::MJTC_submitbutton('ok', esc_html(__('Save', 'majestic-support')), array('class' => 'button')), MJTC_ALLOWED_TAGS); ?>
                <?php echo wp_kses(MJTC_formfield::MJTC_button('cancel', esc_html(__('Cancel', 'majestic-support')), array('class' => 'button', 'onclick'=>'closePopup();')), MJTC_ALLOWED_TAGS); ?>
            </div>
            <?php echo wp_kses(MJTC_formfield::MJTC_hidden('reply-replyid', ''), MJTC_ALLOWED_TAGS); ?>
            <?php echo wp_kses(MJTC_formfield::MJTC_hidden('reply-tikcetid',majesticsupport::$_data[0]->id), MJTC_ALLOWED_TAGS); ?>
            <?php echo wp_kses(MJTC_formfield::MJTC_hidden('time-confilct',''), MJTC_ALLOWED_TAGS); ?>
        </form>
        <?php if(in_array('note', majesticsupport::$_active_addons) && in_array('timetracking', majesticsupport::$_active_addons)){ ?>
        <form id="ms-note-edit-form" style="display:none" method="post" action="<?php echo esc_url(wp_nonce_url(admin_url("admin.php?page=majesticsupport_note&task=saveeditedtime&action=mstask"),"save-edited-time-note-".majesticsupport::$_data[0]->id)); ?>" >
            <div class="mjtc-form-wrapper-popup">
                <div class="mjtc-form-title-popup"><?php echo esc_html(__('Time', 'majestic-support')); ?></div>
                <div class="mjtc-form-field-popup"><?php echo wp_kses(MJTC_formfield::MJTC_text('edited_time', '', array('class' => 'inputbox')), MJTC_ALLOWED_TAGS) ?></div>
            </div>
            <div class="mjtc-form-wrapper-popup system-time-div" style="display:none;" >
                <div class="mjtc-form-title-popup"><?php echo esc_html(__('System Time', 'majestic-support')); ?></div>
                <div class="mjtc-form-field-popup"><?php echo wp_kses(MJTC_formfield::MJTC_text('systemtime', '', array('class' => 'inputbox','disabled'=>'disabled')), MJTC_ALLOWED_TAGS) ?></div>
            </div>
            <div class="mjtc-form-wrapper-popup">
                <div class="mjtc-form-title-popup"><?php echo esc_html(__('Reason For Editing', 'majestic-support')); ?></div>
                <div class="mjtc-form-field-popup"><?php echo wp_kses(MJTC_formfield::MJTC_textarea('edit_reason', '', array('class' => 'inputbox')), MJTC_ALLOWED_TAGS) ?></div>
            </div>
            <div class="mjtc-form-wrapper-popup system-time-div" style="display:none;" >
                <div class="mjtc-form-title-popup"><?php echo esc_html(__('Resolve conflict', 'majestic-support')); ?></div>
                <div class="mjtc-form-field-popup"><?php echo wp_kses(MJTC_formfield::MJTC_select('time-confilct-combo', $MJTC_yesno, ''), MJTC_ALLOWED_TAGS); ?></div>
            </div>
            <div class="mjtc-col-md-12 mjtc-form-button-wrapper">
                <?php echo wp_kses(MJTC_formfield::MJTC_submitbutton('ok', esc_html(__('Save', 'majestic-support')), array('class' => 'button')), MJTC_ALLOWED_TAGS); ?>
                <?php echo wp_kses(MJTC_formfield::MJTC_button('cancel', esc_html(__('Cancel', 'majestic-support')), array('class' => 'button', 'onclick'=>'closePopup();')), MJTC_ALLOWED_TAGS); ?>
            </div>
            <?php echo wp_kses(MJTC_formfield::MJTC_hidden('note-noteid', ''), MJTC_ALLOWED_TAGS); ?>
            <?php echo wp_kses(MJTC_formfield::MJTC_hidden('note-tikcetid',majesticsupport::$_data[0]->id), MJTC_ALLOWED_TAGS); ?>
            <?php echo wp_kses(MJTC_formfield::MJTC_hidden('time-confilct',''), MJTC_ALLOWED_TAGS); ?>
        </form>
    <?php } ?>
<?php }?>
    </div>
</div>
<div id="msadmin-wrapper">
    <div id="msadmin-leftmenu">
        <?php
        if(current_user_can('ms_support_ticket')){
            MJTC_includer::MJTC_getClassesInclude('msadminsidemenu');
        }
        ?>
    </div>
    <div id="msadmin-data">
        <?php MJTC_includer::MJTC_getModel('majesticsupport')->getPageTitle('ticketdetail'); ?>
        <div id="msadmin-data-wrp" class="p0 bg-n bs-n b0">
            <?php
            if (!empty(majesticsupport::$_data[0])) {
                $cur_uid = MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid();
                ?>

                <div id="userpopupblack" style="display:none;"> </div>
                <div id="internalnotespopupblack" style="display:none;"> </div>
                <?php
                $majesticsupport_js ="
                    jQuery(document).ready(function(){
                        jQuery(document).on('submit','#mjtc-support-usercredentails-form',function(e){
                            e.preventDefault(); // avoid to execute the actual submit of the form.
                            var fdata = jQuery(this).serialize(); // serializes the form's elements.
                            var nonce = jQuery('#nonce').val();
                            jQuery.post(ajaxurl, {action: 'mjsupport_ajax', mjsmod: 'privatecredentials', task: 'storePrivateCredentials',formdata_string:fdata, '_wpnonce': nonce}, function (data) {
                                if(data){ // ajax executed
                                    var return_data = jQuery.parseJSON(data);
                                    if(return_data.status == 1){
                                        jQuery('.mjtc-support-usercredentails-wrp').show();
                                        jQuery('.mjtc-support-usercredentails-form-wrap').hide();
                                        jQuery('.mjtc-support-usercredentails-credentails-wrp').append(MJTC_msDecodeHTML(return_data.content));
                                    }else{
                                        alert(return_data.error_message);
                                    }
                                }
                            });
                        })

                        jQuery('.venobox').venobox({
                            infinigall: true,
                            framewidth: 850,
                            titleattr: 'data-title',
                        });
                    });

                    function addEditCredentail(nonce, ticketid, internalid, uid, cred_id = 0, cred_data = ''){
                        jQuery.post(ajaxurl, {action: 'mjsupport_ajax', mjsmod: 'privatecredentials', task: 'getFormForPrivteCredentials', ticketid: ticketid, internalid: internalid, cred_id: cred_id, cred_data: cred_data, uid: uid, '_wpnonce': nonce}, function (data) {
                            if(data){ // ajax executed
                                var return_data = jQuery.parseJSON(data);
                                jQuery('.mjtc-support-usercredentails-wrp').hide();
                                jQuery('.mjtc-support-usercredentails-form-wrap').show();
                                jQuery('.mjtc-support-usercredentails-form-wrap').html(MJTC_msDecodeHTML(return_data));
                                if(cred_id != 0){
                                    jQuery('#mjtc-support-usercredentails-single-id-'+cred_id).remove();
                                }
                            }
                        });
                    }

                    function getCredentails(ticketid, nonce){
                        jQuery.post(ajaxurl, {action: 'mjsupport_ajax', mjsmod: 'privatecredentials', task: 'getPrivateCredentials',ticketid:ticketid, '_wpnonce': nonce}, function (data) {
                            if(data){ // ajax executed
                                var return_data = jQuery.parseJSON(data);
                                if(return_data.status == 1){
                                    jQuery('#userpopupblack').show();
                                    jQuery('#usercredentailspopup').slideDown('slow');
                                    jQuery('.mjtc-support-usercredentails-wrp').slideDown('slow');
                                    jQuery('.mjtc-support-usercredentails-form-wrap').hide();
                                    if(return_data.content != ''){
                                        jQuery('.mjtc-support-usercredentails-credentails-wrp').html('');
                                        jQuery('.mjtc-support-usercredentails-credentails-wrp').append(MJTC_msDecodeHTML(return_data.content));
                                    }
                                }
                            }
                        });
                        return false;
                    }

                    function removeCredentail(cred_id,internalid, nonce){
                        jQuery.post(ajaxurl, {action: 'mjsupport_ajax', mjsmod: 'privatecredentials', task: 'removePrivateCredential',cred_id:cred_id,internalid:internalid, '_wpnonce': nonce}, function (data) {
                            if(data){ // ajax executed
                                if(cred_id != 0){
                                    jQuery('#mjtc-support-usercredentails-single-id-'+cred_id).remove();
                                }
                            }
                        });
                        return false;
                    }

                    function closeCredentailsForm(ticketid, nonce){
                        getCredentails(ticketid, nonce);
                    }
                ";
                wp_add_inline_script('majestic-support-cmain-js',$majesticsupport_js);
                ?>
                <div id="usercredentailspopup" class="ms-popup-wrapper" style="display: none;">
                    <div class="userpopup-top">
                        <div class="userpopup-heading">
                            <?php echo esc_html(__('Private Credentials', 'majestic-support')); ?>
                        </div>
                        <img alt="<?php echo esc_html(__('Close','majestic-support')); ?>" class="close-credentails userpopup-close" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/close-icon-white.png" />
                    </div>
                    <div class="mjtc-support-usercredentails-wrp" style="display: none;">
                        <div class="mjtc-support-usercredentails-credentails-wrp">
                        </div>
                        <?php if(majesticsupport::$_data[0]->status != 5 && majesticsupport::$_data[0]->status != 6){ ?>
                            <div class="mjtc-support-usercredentail-data-add-new-button-wrap" >
                                <?php $nonce = wp_create_nonce('get-form-for-privte-credentials-'.majesticsupport::$_data[0]->id); ?>
                                <button type="button" class="mjtc-support-usercredentail-data-add-new-button" onclick="addEditCredentail('<?php echo esc_js($nonce);?>', <?php echo esc_js(majesticsupport::$_data[0]->id);?>,'<?php echo esc_js(majesticsupport::$_data[0]->internalid);?>',<?php echo esc_js(MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid());?>);" >
                                    <?php echo esc_html(__("Add New Credential",'majestic-support')); ?>
                                </button>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="mjtc-support-usercredentails-form-wrap" >
                    </div>
                </div>
                <div id="userpopupblack" style="display:none;"></div>
                <div id="userpopup" class="srch-hist-popup" style="display:none;">
                    <div class="userpopup-top">
                        <div class="userpopup-heading">
                            <?php echo esc_html(__('Ticket History', 'majestic-support')); ?>
                        </div>
                        <img alt="<?php echo esc_html(__('Close','majestic-support')); ?>" class="close-history userpopup-close" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/close-icon-white.png" />
                    </div>
                    <div id="userpopup-records-wrp">
                        <div id="userpopup-records">
                            <div class="userpopup-search-history">
                                <?php // data[5] holds the tickect history
                                    $field_array = MJTC_includer::MJTC_getModel('fieldordering')->getFieldTitleByFieldfor(1, majesticsupport::$_data[0]->multiformid);
                                if ((!empty(majesticsupport::$_data[5]))) {
                                    ?>
                                    <?php foreach (majesticsupport::$_data[5] AS $history) { ?>
                                        <div class="userpopup-search-history-row">
                                            <div class="userpopup-search-history-col date">
                                                <?php echo esc_html(date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($history->datetime))); ?>
                                            </div>
                                            <div class="userpopup-search-history-col time">
                                            <?php echo esc_html(date_i18n('H:i:s', MJTC_majesticsupportphplib::MJTC_strtotime($history->datetime))); ?>
                                            </div>
                                            <?php
                                            if (is_super_admin($history->uid)) {
                                                $message = 'admin';
                                            } elseif ( in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff($history->uid)) {
                                                $message = 'agent';
                                            } else {
                                                $message = 'member';
                                            }
                                            ?>
                                            <div class="userpopup-search-history-col msg <?php echo esc_attr($message); ?>">
                                                <?php echo wp_kses_post($history->message); ?>
                                            </div>
                                        </div>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- inrternal notes popup -->
                <div id="internalnotes-popup" class="ms-popup-wrapper" style="display: none;">
                    <?php if(in_array('note', majesticsupport::$_active_addons)){ ?>
                        <div class="userpopup-top">
                            <div class="userpopup-heading">
                                <?php echo esc_html(__('Add Internal Note','majestic-support')); ?>
                            </div>
                            <img alt="<?php echo esc_html(__('Close','majestic-support')); ?>" class="internalnotespopup-close" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/close-icon-white.png" />
                        </div>
                        <div class="mjtc-admin-popup-cnt">  <!--  postinternalnote Area   -->
                            <form class="mjtc-det-tkt-form" method="post" action="<?php echo esc_url(wp_nonce_url(admin_url("admin.php?page=majesticsupport_note&task=savenote"),"save-note-".majesticsupport::$_data[0]->id)); ?>"  enctype="multipart/form-data">
                                <?php if(in_array('timetracking', majesticsupport::$_active_addons)){ ?>
                                    <div class="ms-ticket-detail-timer-wrapper"> <!-- Top Timer Section -->
                                        <div class="timer-left" >
                                            <div class="timer-total-time" >
                                                <?php
                                                    $hours = floor(majesticsupport::$_data['time_taken'] / 3600);
                                                    $mins = floor(majesticsupport::$_data['time_taken'] / 60);
                                                    $mins = floor($mins % 60);
                                                    $secs = floor(majesticsupport::$_data['time_taken'] % 60);
                                                    echo esc_html(__('Time Taken','majestic-support')) . ': ' . esc_html( sprintf('%02d:%02d:%02d', $hours, $mins, $secs));
                                                ?>
                                            </div>
                                        </div>
                                        <div class="timer-right" >
                                            <div class="timer" >
                                                00:00:00
                                            </div>
                                            <div class="timer-buttons" >
                                                <?php if(in_array('agent', majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForTask('Edit Time')){ ?>
                                                    <span class="timer-button" onclick="showEditTimerPopup()" >
                                                        <img alt="<?php echo esc_html(__('Edit','majestic-support')); ?>" class="default-show" alt="<?php echo esc_html(__('image', 'majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL);?>includes/images/ticket-detail/edit-time-1.png"/>
                                                        <img alt="<?php echo esc_html(__('Edit','majestic-support')); ?>" class="default-hide" alt="<?php echo esc_html(__('image', 'majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL);?>includes/images/ticket-detail/edit-time.png"/>
                                                    </span>
                                                <?php } ?>
                                                <span class="timer-button cls_1" onclick="changeTimerStatus(1)" >
                                                    <img alt="<?php echo esc_html(__('play','majestic-support')); ?>" class="default-show" alt="<?php echo esc_html(__('image', 'majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL);?>includes/images/ticket-detail/play-time-1.png"/>
                                                    <img alt="<?php echo esc_html(__('play','majestic-support')); ?>" class="default-hide" alt="<?php echo esc_html(__('image', 'majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL);?>includes/images/ticket-detail/play-time.png"/>
                                                </span>
                                                <span class="timer-button cls_2" onclick="changeTimerStatus(2)" >
                                                    <img alt="<?php echo esc_html(__('pause','majestic-support')); ?>" class="default-show" alt="<?php echo esc_html(__('image', 'majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL);?>includes/images/ticket-detail/pause-time-1.png"/>
                                                    <img alt="<?php echo esc_html(__('pause','majestic-support')); ?>" class="default-hide" alt="<?php echo esc_html(__('image', 'majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL);?>includes/images/ticket-detail/pause-time.png"/>
                                                </span>
                                                <span class="timer-button cls_3" onclick="changeTimerStatus(3)" >
                                                    <img alt="<?php echo esc_html(__('stop','majestic-support')); ?>" class="default-show" alt="<?php echo esc_html(__('image', 'majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL);?>includes/images/ticket-detail/stop-time-1.png"/>
                                                    <img alt="<?php echo esc_html(__('stop','majestic-support')); ?>" class="default-hide" alt="<?php echo esc_html(__('image', 'majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL);?>includes/images/ticket-detail/stop-time.png"/>
                                                </span>
                                            </div>
                                        </div>
                                        <?php echo wp_kses(MJTC_formfield::MJTC_hidden('timer_time_in_seconds',''), MJTC_ALLOWED_TAGS); ?>

                                        <?php echo wp_kses(MJTC_formfield::MJTC_hidden('timer_edit_desc',''), MJTC_ALLOWED_TAGS); ?>
                                    </div>
                                <?php } ?>
                                <div class="mjtc-form-wrapper">
                                    <div class="mjtc-form-title"><?php echo esc_html(__('Note Title', 'majestic-support')); ?></div>
                                    <div class="mjtc-form-value"><?php echo wp_kses(MJTC_formfield::MJTC_text('internalnotetitle', '', array('class' => 'inputbox mjtc-admin-popup-input-field')), MJTC_ALLOWED_TAGS) ?></div>
                                </div>
                                <div class="mjtc-form-wrapper">
                                    <div class="mjtc-form-title"><label id="responcemsg" for="responce"><?php echo esc_html(__('Internal Note', 'majestic-support')); ?></label></div>
                                    <div class="mjtc-form-value"><?php wp_editor('', 'internalnote', array('media_buttons' => false)); ?></div>
                                </div>
                                <div class="mjtc-form-wrapper">
                                    <div class="mjtc-form-title"><?php echo esc_html(__('Ticket', 'majestic-support')); echo ' '; echo esc_html(__('Status', 'majestic-support')); ?></div>
                                    <div class="mjtc-form-value">
                                        <div class="ms-formfield-radio-button-wrap">
                                            <?php echo wp_kses(MJTC_formfield::MJTC_checkbox('closeonreply', array('1' => esc_html(__('Close on reply', 'majestic-support'))), '', array('class' => 'radiobutton')), MJTC_ALLOWED_TAGS); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="mjtc-form-wrapper">
                                   <div class="mjtc-form-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($field_array['attachments'])); ?></div>
                                    <div class="mjtc-form-value">
                                        <div class="tk_attachment_value_wrapperform">
                                            <span class="tk_attachment_value_text">
                                                <input type="file" class="inputbox" name="note_attachment" onchange="MJTC_uploadfile(this, '<?php echo esc_js(majesticsupport::$_config['file_maximum_size']); ?>', '<?php echo esc_js(majesticsupport::$_config['file_extension']); ?>');" size="20" maxlenght='30'/>
                                                <span class='tk_attachment_remove'></span>
                                            </span>
                                        </div>
                                        <span class="tk_attachments_configform">
                                            <small>
                                                    <?php
                                                    $tktdata =  esc_html(__('Maximum File Size','majestic-support')).' (' . esc_html(majesticsupport::$_config['file_maximum_size']).'KB)<br>'.esc_html(__('File Extension Type','majestic-support')).' (' . esc_html(majesticsupport::$_config['file_extension']) . ')';
                                                    echo wp_kses($tktdata, MJTC_ALLOWED_TAGS);
                                                     ?>
                                                    
                                                </small>
                                        </span>
                                    </div>
                                </div>
                                <div class="mjtc-form-button">
                                    <?php echo wp_kses(MJTC_formfield::MJTC_submitbutton('postinternalnote', esc_html(__('Post Internal Note','majestic-support')), array('class' => 'button mjtc-admin-pop-btn-block', 'onclick' => "return checktinymcebyid('internalnote');")), MJTC_ALLOWED_TAGS); ?>
                                </div>
                                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('ticketid', majesticsupport::$_data[0]->id), MJTC_ALLOWED_TAGS); ?>
                                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('uid', MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid()), MJTC_ALLOWED_TAGS); ?>
                                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('action', 'note_savenote'), MJTC_ALLOWED_TAGS); ?>
                                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('form_request', 'majesticsupport'), MJTC_ALLOWED_TAGS); ?>
                            </form>
                        </div>
                    <?php } ?>
                </div>
                <!-- change status popup -->
                <div id="changestatus-popup" class="ms-popup-wrapper" style="display: none;">
                    <div class="userpopup-top">
                        <div class="userpopup-heading">
                            <!-- Display heading based on field order  -->
                            <?php echo esc_html(__('Change Status','majestic-support')); ?>
                        </div>
                        <img alt="<?php echo esc_html(__('Close','majestic-support')); ?>" class="userpopup-close" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/close-icon-white.png" />
                    </div>
                    <div class="mjtc-admin-popup-cnt">
                        <form class="mjtc-det-tkt-form" method="post" action="<?php echo esc_url(wp_nonce_url(admin_url("admin.php?page=majesticsupport_ticket&task=changestatus"),"change-status-".majesticsupport::$_data[0]->id)); ?>">
                            <div class="mjtc-form-wrapper">
                                <div class="mjtc-form-title">
                                    <?php echo esc_html(__('Select Status','majestic-support')); ?>
                                </div>
                                <div class="mjtc-form-value">
                                    <?php echo wp_kses(MJTC_formfield::MJTC_select('status', MJTC_includer::MJTC_getModel('status')->getStatusForCombobox(), majesticsupport::$_data[0]->status, '', array('class' => 'inputbox mjtc-admin-popup-select-field')), MJTC_ALLOWED_TAGS); ?>
                                </div>
                            </div>
                            <div class="mjtc-form-button">
                                <?php echo wp_kses(MJTC_formfield::MJTC_submitbutton('changestatus', esc_html(__('Change Status','majestic-support')), array('class' => 'button mjtc-admin-pop-btn-block')), MJTC_ALLOWED_TAGS); ?>
                            </div>
                            <?php echo wp_kses(MJTC_formfield::MJTC_hidden('ticketid', majesticsupport::$_data[0]->id), MJTC_ALLOWED_TAGS); ?>
                            <?php echo wp_kses(MJTC_formfield::MJTC_hidden('uid', MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid()), MJTC_ALLOWED_TAGS); ?>
                            <?php echo wp_kses(MJTC_formfield::MJTC_hidden('action', 'ticket_changestatus'), MJTC_ALLOWED_TAGS); ?>
                            <?php echo wp_kses(MJTC_formfield::MJTC_hidden('form_request', 'majesticsupport'), MJTC_ALLOWED_TAGS); ?>
                        </form>
                    </div>
                </div>
                <!-- change priority popup -->
                <div id="changepriority-popup" class="ms-popup-wrapper" style="display: none;">
                    <div class="userpopup-top">
                        <div class="userpopup-heading">
                            <!-- Display heading based on field order  -->
                            <?php echo esc_html(__('Change','majestic-support')) ." ".esc_html(majesticsupport::MJTC_getVarValue($field_array['priority'])); ?>
                        </div>
                        <img alt="<?php echo esc_html(__('Close','majestic-support')); ?>" class="userpopup-close" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/close-icon-white.png" />
                    </div>
                    <div class="mjtc-admin-popup-cnt">
                        <form class="mjtc-det-tkt-form" method="post" action="#">
                            <div class="mjtc-form-wrapper">
                                <div class="mjtc-form-title">
                                    <?php echo esc_html(majesticsupport::MJTC_getVarValue($field_array['priority'])); ?>
                                </div>
                                <div class="mjtc-form-value">
                                    <?php echo wp_kses(MJTC_formfield::MJTC_select('prioritytemp', MJTC_includer::MJTC_getModel('priority')->getPriorityForCombobox(), majesticsupport::$_data[0]->priorityid, esc_html(__('Change', 'majestic-support')) ." ".esc_html(majesticsupport::MJTC_getVarValue($field_array['priority'])), array('class' => 'inputbox mjtc-admin-popup-select-field')), MJTC_ALLOWED_TAGS); ?>
                                </div>
                            </div>
                            <div class="mjtc-form-button">
                                <?php echo wp_kses(MJTC_formfield::MJTC_button('changepriority', esc_html(__('Change', 'majestic-support')) ." ".esc_html(majesticsupport::MJTC_getVarValue($field_array['priority'])), array('class' => 'button mjtc-admin-pop-btn-block changeprioritybutton', 'onclick' => 'actionticket(1);')), MJTC_ALLOWED_TAGS); ?>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- change department popup -->
                <div id="changedept-popup" class="ms-popup-wrapper" style="display: none;">
                    <?php if ( in_array('actions',majesticsupport::$_active_addons)) { ?>
                        <div class="userpopup-top">
                            <div class="userpopup-heading">
                                <?php echo esc_html(majesticsupport::MJTC_getVarValue($field_array['department'])) ." ".esc_html(__('Transfer','majestic-support')); ?>
                            </div>
                            <img alt="<?php echo esc_html(__('Close','majestic-support')); ?>" class="userpopup-close" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/close-icon-white.png" />
                        </div>
                        <form class="mjtc-det-tkt-form" method="post" action="<?php echo esc_url(wp_nonce_url(admin_url("admin.php?page=majesticsupport_ticket&task=transferdepartment"),"transfer-department-".majesticsupport::$_data[0]->id)); ?>"  enctype="multipart/form-data">
                            <div class="mjtc-admin-popup-cnt">
                                <div class="mjtc-form-wrapper">
                                    <div class="mjtc-form-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($field_array['department'])); ?></div>
                                    <div class="mjtc-form-value">
                                        <?php echo wp_kses(MJTC_formfield::MJTC_select('departmentid', MJTC_includer::MJTC_getModel('department')->getDepartmentForCombobox(), majesticsupport::$_data[0]->departmentid, esc_html(__('Select', 'majestic-support')) ." ".esc_html(majesticsupport::MJTC_getVarValue($field_array['department'])), array('class' => 'inputbox mjtc-admin-popup-select-field')), MJTC_ALLOWED_TAGS); ?>
                                    </div>
                                </div>
                                <?php if(in_array('note', majesticsupport::$_active_addons)){ ?>
                                    <div class="mjtc-form-wrapper">
                                        <div class="mjtc-form-title"><label id="responcemsg" for="responce"><?php echo esc_html(__('Reason For', 'majestic-support')) ." ".esc_html(majesticsupport::MJTC_getVarValue($field_array['department'])) ." ".esc_html(__('Transfer', 'majestic-support')); ?></label></div>
                                        <div class="mjtc-form-value"><?php wp_editor('', 'departmenttranfernote', array('media_buttons' => false)); ?></div>
                                    </div>
                                <?php } ?>
                                <div class="mjtc-form-button">
                                    <?php echo wp_kses(MJTC_formfield::MJTC_submitbutton('departmenttransfer', esc_html(__('Transfer','majestic-support')), array('class' => 'button mjtc-admin-pop-btn-block', 'onclick' => "return checktinymcebyid('departmenttranfernote');")), MJTC_ALLOWED_TAGS); ?>
                                </div>
                                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('ticketid', majesticsupport::$_data[0]->id), MJTC_ALLOWED_TAGS); ?>
                                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('uid', MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid()), MJTC_ALLOWED_TAGS); ?>
                                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('action', 'ticket_transferdepartment'), MJTC_ALLOWED_TAGS); ?>
                                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('form_request', 'majesticsupport'), MJTC_ALLOWED_TAGS); ?>
                            </div>
                        </form>
                    <?php } ?>
                </div>
                <!-- assign to staff popup -->
                <div id="assignstaff-popup" class="ms-popup-wrapper" style="display: none;">
                    <?php if ( in_array('agent',majesticsupport::$_active_addons)) { ?>
                        <div class="userpopup-top">
                            <div class="userpopup-heading">
                                <?php echo esc_html(__('Assign To Agent','majestic-support')); ?>
                            </div>
                            <img alt="<?php echo esc_html(__('Close','majestic-support')); ?>" class="userpopup-close" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/close-icon-white.png" />
                        </div>
                        <div class="mjtc-admin-popup-cnt">
                            <form class="mjtc-det-tkt-form" method="post" action="<?php echo esc_url(wp_nonce_url(admin_url("admin.php?page=majesticsupport_ticket&task=assigntickettostaff"),"assign-ticket-to-staff-".majesticsupport::$_data[0]->id)); ?>"  enctype="multipart/form-data">
                                <div class="mjtc-form-wrapper">
                                    <div class="mjtc-form-title"><?php echo esc_html(__('Agent', 'majestic-support')); ?></div>
                                    <div class="mjtc-form-value">
                                         <?php echo wp_kses(MJTC_formfield::MJTC_select('staffid', MJTC_includer::MJTC_getModel('agent')->getstaffForCombobox(), majesticsupport::$_data[0]->staffid, esc_html(__('Select Agent', 'majestic-support')), array('class' => 'inputbox mjtc-admin-popup-select-field','required' => true)), MJTC_ALLOWED_TAGS); ?>
                                    </div>
                                </div>
                                <?php if(in_array('note', majesticsupport::$_active_addons)){ ?>
                                    <div class="mjtc-form-wrapper">
                                        <div class="mjtc-form-title"><label id="responcemsg" for="responce"><?php echo esc_html(__('Internal Note', 'majestic-support')); ?></label></div>
                                        <div class="mjtc-form-value"><?php wp_editor('', 'assignnote', array('media_buttons' => false)); ?></div>
                                    </div>
                                <?php } ?>
                                <div class="mjtc-form-button">
                                    <?php echo wp_kses(MJTC_formfield::MJTC_submitbutton('assigntostaff', esc_html(__('Assign','majestic-support')), array('class' => 'button mjtc-admin-pop-btn-block', 'onclick' => "return checktinymcebyid('assignnote');")), MJTC_ALLOWED_TAGS); ?>
                                </div>
                                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('ticketid', majesticsupport::$_data[0]->id), MJTC_ALLOWED_TAGS); ?>
                                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('uid', MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid()), MJTC_ALLOWED_TAGS); ?>
                                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('action', 'ticket_assigntickettostaff'), MJTC_ALLOWED_TAGS); ?>
                                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('form_request', 'majesticsupport'), MJTC_ALLOWED_TAGS); ?>
                            </form>
                        </div>
                    <?php } ?>
                </div>
                <!-- ticket detail -->
                <div class="mjtc-support-detail-wrapper">
                    <div class="mjtc-sprt-det-left">
                        <!-- ticket top info -->
                        <div class="mjtc-sprt-det-cnt mjtc-sprt-det-info-wrp">
                            <div class="mjtc-sprt-det-user">
                                <div class="mjtc-sprt-det-user-image">
                                    <?php echo wp_kses_post(ms_get_avatar(majesticsupport::$_data[0]->uid)); ?>
                                </div>
                                <div class="mjtc-sprt-det-user-cnt">
                                    <div class="mjtc-sprt-det-user-data name"><?php echo esc_html(majesticsupport::$_data[0]->name); ?></div>
                                    <div class="mjtc-sprt-det-user-data email"><?php echo esc_html(majesticsupport::$_data[0]->email); ?></div>
                                    <div class="mjtc-sprt-det-user-data number"><?php echo esc_html(majesticsupport::$_data[0]->phone); ?></div>
                                </div>
                            </div>
                            <?php if(isset(majesticsupport::$_data['nticket'])){ ?>
                            <div class="mjtc-sprt-det-other-tkt">
                                <a href="<?php echo esc_url(admin_url('admin.php?page=majesticsupport_ticket&mjslay=tickets&uid='.esc_attr(majesticsupport::$_data[0]->uid))); ?>" class="mjtc-sprt-det-other-tkt-btn">
                                    <?php echo esc_html(__('View all','majestic-support')).' '.esc_html(majesticsupport::$_data['nticket']).' '. esc_html(__('tickets by','majestic-support')).' '.esc_html(majesticsupport::$_data[0]->name); ?>
                                </a>
                                <a href="<?php echo esc_url(admin_url('admin.php?page=majesticsupport_ticket&mjslay=tickets&uid='.esc_attr(majesticsupport::$_data[0]->uid))); ?>" class="mjtc-sprt-det-other-tkt-img">
                                    <img alt="<?php echo esc_html(__('Edit Ticket','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/ticket-detail/new-window.png" />
                                </a>
                            </div>
                            <?php } ?>
                            <div class="mjtc-sprt-det-tkt-msg">
                                <?php echo wp_kses_post(majesticsupport::$_data[0]->message); ?>
                            </div>
                            <?php
                            $formid = majesticsupport::$_data[0]->multiformid;
                            majesticsupport::$_data['custom']['ticketid'] = majesticsupport::$_data[0]->id;
                            $customfields = MJTC_includer::MJTC_getObjectClass('customfields')->MJTC_userFieldsData(1, null, $formid);
                            if (!empty($customfields)){
                                ?>
                                <div class="mjtc-sprt-det-tkt-custm-flds">
                                    <?php
                                    foreach ($customfields as $field) {
                                        $ret = MJTC_includer::MJTC_getObjectClass('customfields')->MJTC_showCustomFields($field,2, majesticsupport::$_data[0]->params);
                                        ?>
                                        <div class="mjtc-sprt-det-tkt-custm-flds-rec">
                                            <span class="mjtc-sprt-det-tkt-custm-flds-tit">
                                                <?php echo esc_html($ret['title']).' : '; ?>
                                            </span>
                                            <span class="mjtc-sprt-det-tkt-custm-flds-val">
                                                <?php echo wp_kses($ret['value'], MJTC_ALLOWED_TAGS); ?>
                                            </span>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </div>
                                <?php
                            }
                            ?>
                            <div class="mjtc-sprt-det-actn-btn-wrp">
                                <a title="<?php echo esc_attr(__('Edit Ticket','majestic-support')); ?>" class="mjtc-sprt-det-actn-btn" href="?page=majesticsupport_ticket&mjslay=addticket&majesticsupportid=<?php echo esc_attr(majesticsupport::$_data[0]->id); ?>">
                                    <img alt="<?php echo esc_html(__('Edit Ticket','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/ticket-detail/edit.png" />
                                    <span><?php echo esc_html(__('Edit Ticket','majestic-support')); ?></span>
                                </a>
                                <?php if(in_array('tickethistory', majesticsupport::$_active_addons)){ ?>
                                    <a title="<?php echo esc_attr(__('Show History','majestic-support')); ?>" class="mjtc-sprt-det-actn-btn" href="#" id="showhistory">
                                        <img alt="<?php echo esc_html(__('Show History','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/ticket-detail/history.png" />
                                        <span><?php echo esc_html(__('Show History','majestic-support')); ?></span>
                                    </a>
                                <?php } ?>
                                <form method="post" action="<?php echo esc_url(wp_nonce_url(admin_url("admin.php?page=majesticsupport_ticket&task=actionticket"),"action-ticket-".majesticsupport::$_data[0]->id)); ?>" id="adminTicketform" enctype="multipart/form-data">
                                    <?php
                                        if (majesticsupport::$_data[0]->status != 6) { // merged closed ticket can not be reopend.
                                            if (majesticsupport::$_data[0]->status != 5) {
                                                if (in_array('ticketclosereason',majesticsupport::$_active_addons)) {
                                                    $js = 'showTicketCloseReasons('.majesticsupport::$_data[0]->id.')';
                                                } else {
                                                    $js = 'actionticket(2);';
                                                }
                                            ?>
                                            <a title="<?php echo esc_attr(__('Close Ticket','majestic-support')); ?>" class="mjtc-sprt-det-actn-btn" href="#" onclick="<?php echo esc_js($js);?>">
                                                    <img alt="<?php echo esc_html(__('Close Ticket','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/ticket-detail/close.png" />
                                                    <span><?php echo esc_html(__('Close Ticket','majestic-support')); ?></span>
                                                </a>
                                            <?php } else { ?>
                                                <a title="<?php echo esc_attr(__('Reopen Ticket','majestic-support')); ?>" class="mjtc-sprt-det-actn-btn" href="#" onclick="actionticket(3);">
                                                    <img alt="<?php echo esc_html(__('Reopen Ticket','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/ticket-detail/reopen.png" />
                                                    <span><?php echo esc_html(__('Reopen Ticket','majestic-support')); ?></span>
                                                </a>
                                            <?php }
                                        }
                                        majesticsupport::$_data['custom']['ticketid'] = majesticsupport::$_data[0]->id;
                                    ?>
                                    <?php if (  in_array('actions',majesticsupport::$_active_addons) && majesticsupport::$_data[0]->status != 5 && majesticsupport::$_data[0]->status != 6 ) { ?>
                                        <a title="<?php echo esc_attr(__('Print Ticket','majestic-support')); ?>" class="mjtc-sprt-det-actn-btn" href="#" id="print-link" data-ticketid="<?php echo esc_attr(majesticsupport::$_data[0]->id); ?>">
                                            <img alt="<?php echo esc_html(__('Print Ticket','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/ticket-detail/print.png" />
                                            <span><?php echo esc_html(__('Print Ticket','majestic-support')); ?></span>
                                        </a>
                                    <?php } ?>
                                    <?php if (  in_array('mergeticket',majesticsupport::$_active_addons) && majesticsupport::$_data[0]->status != 5 && majesticsupport::$_data[0]->status != 6 ) {
                                        $nonce = wp_create_nonce("get-tickets-for-merging-".majesticsupport::$_data[0]->id) ?>
                                        <a title="<?php echo esc_attr(__('Merge Ticket','majestic-support')); ?>" class="mjtc-sprt-det-actn-btn" href="#" id="mergeticket" data-ticketid="<?php echo esc_attr(majesticsupport::$_data[0]->id); ?>" onclick="return showPopupAndFillValues(<?php echo esc_js(majesticsupport::$_data[0]->id) ?>,4, '<?php echo esc_js($nonce);?>')" >
                                            <img alt="<?php echo esc_html(__('Merge Ticket','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/ticket-detail/merge-ticket.png" />
                                            <span><?php echo esc_html(__('Merge Ticket','majestic-support')); ?></span>
                                        </a>
                                    <?php } ?>
                                    <?php if (in_array('privatecredentials',majesticsupport::$_active_addons)) { ?>
                                        <?php $nonce = wp_create_nonce('get-private-credentials-'.majesticsupport::$_data[0]->id) ?>
                                        <a title="<?php echo esc_attr(__('Private Credentials','majestic-support')); ?>" class="mjtc-sprt-det-actn-btn" href="javascript:return false;" id="privatecredentials" onclick="getCredentails(<?php echo esc_js(majesticsupport::$_data[0]->id); ?>, '<?php echo esc_js($nonce); ?>')" >
                                        <?php $query = "SELECT count(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_privatecredentials` WHERE status = 1 AND ticketid = ".esc_sql(majesticsupport::$_data[0]->id);
                                        $cred_count = majesticsupport::$_db->get_var($query);
                                        if ($cred_count>0) {
                                            $img_name = 'private-credentials-exist.png';
                                        } else {
                                            $img_name = 'private-credentials.png';
                                        } ?>
                                        <img alt="<?php echo esc_html(__('Private Credentials','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/ticket-detail/<?php echo esc_attr($img_name);?>"  />
                                        <span><?php echo esc_html(__('Private Credentials','majestic-support')); ?></span>
                                    </a>
                                    <?php } ?>
                                    <?php
                                        if(in_array('actions', majesticsupport::$_active_addons)){
                                            if (majesticsupport::$_data[0]->lock == 1) { ?>
                                                <a title="<?php echo esc_attr(__('Unlock Ticket','majestic-support')); ?>" class="mjtc-sprt-det-actn-btn" href="#" onclick="actionticket(5);">
                                                    <img alt="<?php echo esc_html(__('Unlock Ticket','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/ticket-detail/unlock.png" />
                                                    <span><?php echo esc_html(__('Unlock Ticket','majestic-support')); ?></span>
                                                </a>
                                            <?php } else { ?>
                                                <a title="<?php echo esc_attr(__('Lock Ticket','majestic-support')); ?>" class="mjtc-sprt-det-actn-btn" href="#" onclick="actionticket(4);">
                                                    <img alt="<?php echo esc_html(__('Lock Ticket','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/ticket-detail/lock.png" />
                                                    <span><?php echo esc_html(__('Lock Ticket','majestic-support')); ?></span>
                                                </a>
                                            <?php }
                                        }
                                        if(in_array('banemail', majesticsupport::$_active_addons)){
                                            if (MJTC_includer::MJTC_getModel('banemail')->isEmailBan(majesticsupport::$_data[0]->email)) { ?>
                                                <a titile="<?php echo esc_html(__('Unban Email','majestic-support')); ?>" class="mjtc-sprt-det-actn-btn" href="#" onclick="actionticket(7);">
                                                    <img alt="<?php echo esc_html(__('Unban Email','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/ticket-detail/un-ban.png" />
                                                    <span><?php echo esc_html(__('Unban Email','majestic-support')); ?></span>
                                                </a>
                                            <?php } else { ?>
                                                <a title="<?php echo esc_attr(__('Ban Email','majestic-support')); ?>" class="mjtc-sprt-det-actn-btn" href="#" onclick="actionticket(6);">
                                                    <img alt="<?php echo esc_html(__('Ban Email','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/ticket-detail/ban.png" />
                                                    <span><?php echo esc_html(__('Ban Email','majestic-support')); ?></span>
                                                </a>
                                            <?php
                                            }
                                        }
                                        if(in_array('overdue', majesticsupport::$_active_addons)){
                                            if (majesticsupport::$_data[0]->isoverdue == 1) { ?>
                                                <a title="<?php echo esc_attr(__('Unmark Overdue','majestic-support')); ?>" class="mjtc-sprt-det-actn-btn" href="#" onclick="actionticket(11);">
                                                    <img alt="<?php echo esc_html(__('Unmark Overdue','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/ticket-detail/un-over-due.png" />
                                                    <span><?php echo esc_html(__('Unmark Overdue','majestic-support')); ?></span>
                                                </a>
                                            <?php } else { ?>
                                                <a titlle="<?php echo esc_html(__('Mark overdue','majestic-support')); ?>" class="mjtc-sprt-det-actn-btn" href="#" onclick="actionticket(8);">
                                                    <img alt="<?php echo esc_html(__('Mark overdue','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/ticket-detail/over-due.png" />
                                                    <span><?php echo esc_html(__('Mark Overdue','majestic-support')); ?></span>
                                                </a>
                                            <?php }
                                        }
                                    ?>
                                    <?php if(in_array('actions', majesticsupport::$_active_addons)){ ?>
                                        <a title="<?php echo esc_attr(__('Mark in Progress','majestic-support')); ?>" class="mjtc-sprt-det-actn-btn" href="#" onclick="actionticket(9);">
                                            <img alt="<?php echo esc_html(__('Mark in Progress','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/ticket-detail/in-progress.png" />
                                            <span><?php echo esc_html(__('Mark in Progress','majestic-support')); ?></span>
                                        </a>
                                    <?php } ?>
                                    <?php
                                        if(in_array('banemail', majesticsupport::$_active_addons)){ ?>
                                            <a title="<?php echo esc_attr(__('Ban Email and Close Ticket','majestic-support')); ?>" class="mjtc-sprt-det-actn-btn" href="#" onclick="actionticket(10);">
                                                <img alt="<?php echo esc_html(__('Ban Email and Close Ticket','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/ticket-detail/ban-email-close-ticket.png" />
                                                <span><?php echo esc_html(__('Ban Email and Close Ticket','majestic-support')); ?></span>
                                            </a>
                                    <?php } ?>
                                    <?php
                                        echo wp_kses(MJTC_formfield::MJTC_hidden('actionid', ''), MJTC_ALLOWED_TAGS);
                                        echo wp_kses(MJTC_formfield::MJTC_hidden('priority', ''), MJTC_ALLOWED_TAGS);
                                        echo wp_kses(MJTC_formfield::MJTC_hidden('ticketid', majesticsupport::$_data[0]->id), MJTC_ALLOWED_TAGS);
                                        echo wp_kses(MJTC_formfield::MJTC_hidden('internalid', majesticsupport::$_data[0]->internalid), MJTC_ALLOWED_TAGS);
                                        echo wp_kses(MJTC_formfield::MJTC_hidden('uid', MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid()), MJTC_ALLOWED_TAGS);
                                         echo wp_kses(MJTC_formfield::MJTC_hidden('action', 'reply_savereply'),MJTC_ALLOWED_TAGS);
                                        echo wp_kses(MJTC_formfield::MJTC_hidden('form_request', 'majesticsupport'), MJTC_ALLOWED_TAGS);
                                    ?>
                                </form>
                            </div>
                        </div>
                        <!-- Tickect internal Note Area -->
                        <?php
                            $MJTC_colored = "colored";
                            if(in_array('note', majesticsupport::$_active_addons)){ ?>
                                <div class="mjtc-sprt-det-title"><?php echo esc_html(__('Internal Note', 'majestic-support')); ?></div>
                                <?php if (!empty(majesticsupport::$_data[6])) {
                                    foreach (majesticsupport::$_data[6] AS $note) {
                                        if ($cur_uid == isset($note->uid))
                                            $MJTC_colored = '';?>
                                        <div class="mjtc-support-thread">
                                            <div class="mjtc-support-thread-image">
                                                <?php /* if (in_array('agent',majesticsupport::$_active_addons) && $note->staffphoto) { ?>
                                                    <img alt="<?php echo esc_html(__('agent image','majestic-support')); ?>" src="<?php echo esc_url(majesticsupport::makeUrl(array('mjsmod'=>'agent','task'=>'getStaffPhoto','action'=>'mstask','majesticsupportid'=>$note->staff_id, 'mspageid'=>majesticsupport::getPageid()))); ?>">
                                                <?php } else { */
                                                    echo wp_kses(ms_get_avatar($note->userid), MJTC_ALLOWED_TAGS);
                                                // } ?>
                                            </div>
                                            <div class="mjtc-support-thread-cnt">
                                                <div class="mjtc-support-thread-data">
                                                    <span class="mjtc-support-thread-person">
                                                        <?php
                                                        if(isset($note->staffname)){
                                                            echo esc_html($note->staffname);
                                                        }elseif(isset($note->display_name)){
                                                            echo esc_html($note->display_name);
                                                        }else{
                                                            echo '--------';
                                                        }
                                                        ?>
                                                    </span>
                                                    <?php
                                                        if(in_array('timetracking', majesticsupport::$_active_addons)){
                                                            $hours = floor($note->usertime / 3600);
                                                            $mins = floor($note->usertime / 60);
                                                            $mins = floor($mins % 60);
                                                            $secs = floor($note->usertime % 60);
                                                            $time = esc_html(__('Time Taken','majestic-support')).':&nbsp;'.sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
                                                        ?>
                                                        <span class="mjtc-support-thread-time"><?php echo esc_html($time); ?></span>
                                                    <?php } ?>
                                                </div>
                                                <?php if (isset($note->title) && $note->title != '') { ?>
                                                    <div class="mjtc-support-thread-data">
                                                        <span class="mjtc-support-thread-note"><?php echo esc_html($note->title); ?></span>
                                                    </div>
                                                <?php } ?>
                                                <div class="mjtc-support-thread-data note-msg">
                                                <?php
                                                    echo wp_kses_post($note->note);
                                                    if($note->filesize > 0 && !empty($note->filename)){
                                                        echo wp_kses('<div class="mjtc_supportattachment">
                                                                <span class="mjtc_supportattachment_fname">'
                                                                    . esc_html($note->filename) . '
                                                                </span>
                                                                <a title="'. esc_html(__('Download','majestic-support')).'" class="button" target="_blank" href="'.esc_url(admin_url('?page=majesticsupport_note&action=mstask&task=downloadbyid&id='.esc_attr($note->id))).'">'. esc_html(__('Download','majestic-support')).'</a>
                                                            </div>', MJTC_ALLOWED_TAGS);
                                                    }
                                                ?>
                                                </div>
                                                <div class="mjtc-support-thread-cnt-btm">
                                                    <div class="mjtc-support-thread-date"><?php echo esc_html(date_i18n("l F d, Y, H:i:s", MJTC_majesticsupportphplib::MJTC_strtotime($note->created))); ?></div>
                                                    <div class="mjtc-support-thread-actions">
                                                        <?php
                                                            if(in_array('timetracking', majesticsupport::$_active_addons)){
                                                                $hours = floor($note->usertime / 3600);
                                                                $mins = floor($note->usertime / 60);
                                                                $mins = floor($mins % 60);
                                                                $secs = floor($note->usertime % 60);
                                                                $time = esc_html(__('Time Taken','majestic-support')).':&nbsp;'.sprintf('%02d:%02d:%02d', esc_html($hours), esc_html($mins), esc_html($secs));
                                                                $nonce = wp_create_nonce("get-time-by-note-id-".$note->id); ?>
                                                                <a title="<?php echo esc_attr(__('Edit','majestic-support')); ?>" class="mjtc-support-thread-actn-btn ticket-edit-time-button" href="#" onclick="return showPopupAndFillValues(<?php echo esc_js($note->id);?>,3, '<?php echo esc_js($nonce);?>')" >
                                                                    <img alt="<?php echo esc_html(__('Edit','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/ticket-detail/edit-reply.png" />
                                                            </a>
                                                            <?php
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                <?php } ?>
                                <div class="mjtc-support-thread-add-btn">
                                    <a title="<?php echo esc_attr(__('Post A New Internal Note','majestic-support')); ?>" href="#" class="mjtc-support-thread-add-btn-link" id="int-note">
                                        <img alt="<?php echo esc_html(__('Post A New Internal Note','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/ticket-detail/edit-time.png" />
                                        <span><?php echo esc_html(__('Post A New Internal Note','majestic-support')); ?></span>
                                    </a>
                                </div>
                            <?php } ?>
                        <!-- Tickect  Reply  Area -->
                        <div class="mjtc-sprt-det-title"><?php echo esc_html(__('Ticket Thread', 'majestic-support')); ?></div>
                        <div class="mjtc-support-thread">
                            <div class="mjtc-support-thread-image">
                                <?php /* if ( in_array('agent',majesticsupport::$_active_addons) &&  majesticsupport::$_data[0]->staffphotophoto) { ?>
                                    <img alt="<?php echo esc_html(__('agent image','majestic-support')); ?>" src="<?php echo esc_url(admin_url('?page=majesticsupport_agent&action=mstask&task=getStaffPhoto&majesticsupportid='.esc_attr(majesticsupport::$_data[0]->staffphotoid ))); ?>">
                                <?php } else { */
                                    echo wp_kses(ms_get_avatar(majesticsupport::$_data[0]->uid), MJTC_ALLOWED_TAGS);
                                // } ?>
                            </div>
                            <div class="mjtc-support-thread-cnt">
                                <div class="mjtc-support-thread-data">
                                    <span class="mjtc-support-thread-person">
                                        <?php echo esc_html(majesticsupport::$_data[0]->name); ?>
                                    </span>
                                </div>
                                <div class="mjtc-support-thread-data">
                                    <span class="mjtc-support-thread-email">
                                        <?php echo esc_html(majesticsupport::$_data[0]->email); ?>
                                    </span>
                                </div>
                                <div class="mjtc-support-thread-data note-msg">
                                    <?php echo wp_kses_post(majesticsupport::$_data[0]->message);
                                    ?>
                                </div>
                                <?php
                                if(isset($field_array['attachments'])){
                                    if (!empty(majesticsupport::$_data['ticket_attachment'])) {
                                        $MJTC_datadirectory = majesticsupport::$_config['data_directory'];
                                        $maindir = wp_upload_dir();
                                        $path = $maindir['baseurl'];

                                        $path = $path .'/' . $MJTC_datadirectory;
                                        $path = $path . '/attachmentdata';
                                        $path = $path . '/ticket/ticket_' . majesticsupport::$_data[0]->id . '/';
                                        foreach (majesticsupport::$_data['ticket_attachment'] AS $attachment) {
                                            $path = admin_url("?page=majesticsupport_ticket&action=mstask&task=downloadbyid&id=".esc_attr($attachment->id));
                                            echo wp_kses('
                                            <div class="mjtc_supportattachment">
                                                <span class="mjtc_supportattachment_fname">
                                                  ' . esc_html($attachment->filename) . '
                                                </span>
                                                <a title="'. esc_html(__('Download','majestic-support')).'" class="button" target="_blank" href="' . esc_url($path) . '">' . esc_html(__('Download', 'majestic-support')) . '</a>
                                            </div>', MJTC_ALLOWED_TAGS);
                                        }
                                    }
                                }
                                ?>
                                <div class="mjtc-support-thread-cnt-btm">
                                    <div class="mjtc-support-thread-date">
                                        <?php echo esc_html(date_i18n("l F d, Y, H:i:s", MJTC_majesticsupportphplib::MJTC_strtotime(majesticsupport::$_data[0]->created))); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Tickect  Reply  Area -->
                        <?php
                            $MJTC_colored = "colored";
                            if (!empty(majesticsupport::$_data[4]))
                                foreach (majesticsupport::$_data[4] AS $reply) {
                                if ($cur_uid == $reply->uid)
                                    $MJTC_colored = '';
                                ?>
                                <div class="mjtc-support-thread">
                                    <div class="mjtc-support-thread-image">
                                        <?php /* if (in_array('agent',majesticsupport::$_active_addons) && $reply->staffphoto) { ?>
                                            <img alt="<?php echo esc_html(__('agent image','majestic-support')); ?>"  src="<?php echo esc_url(majesticsupport::makeUrl(array('mjsmod'=>'agent','task'=>'getStaffPhoto','action'=>'mstask','majesticsupportid'=>$reply->staffid,'mspageid'=>majesticsupport::getPageid()))); ?>">
                                        <?php } else { */
                                            echo wp_kses(ms_get_avatar($reply->uid), MJTC_ALLOWED_TAGS);
                                        // } ?>
                                    </div>
                                    <div class="mjtc-support-thread-cnt">
                                        <div class="mjtc-support-thread-data">
                                            <span class="mjtc-support-thread-person"><?php echo esc_html($reply->name); ?></span>
                                            <?php
                                            if(in_array('timetracking', majesticsupport::$_active_addons)){
                                                if($reply->time > 0 ){
                                                   $hours = floor($reply->time / 3600);
                                                   $mins = floor($reply->time / 60);
                                                   $mins = floor($mins % 60);
                                                   $secs = floor($reply->time % 60);
                                                   $time = esc_html(__('Time Taken','majestic-support')).':&nbsp;'.sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
                                                    ?>
                                                    <span class="mjtc-support-thread-time"><?php echo esc_html($time); ?></span>
                                                    <?php
                                                }
                                            }
                                            if (majesticsupport::$_config['show_read_receipt_to_admin_on_reply'] == 1 && !empty($reply->viewed_by) && $cur_uid == $reply->uid) { ?>
                                                <span class="mjtc-support-thread-read-status-wrp">
                                                    <span class="mjtc-support-thread-read-status-btn">
                                                       <img alt="<?php echo esc_html(__('View Image','majestic-support')) ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/ticket-detail/view.png" />
                                                    </span>
                                                    <span class="mjtc-support-thread-read-status-detail">
                                                        <span class="mjtc-support-thread-read-status-row">
                                                            <?php 
                                                            echo '<b>'.esc_html(__('Viewed By','majestic-support').': ').'</b>';
                                                            if ($reply->viewed_by == -1) {
                                                                echo esc_html(__('Guest', 'majestic-support'));
                                                            } else {
                                                                echo esc_html($reply->viewername);
                                                            }
                                                            ?>
                                                        </span>
                                                        <span class="mjtc-support-thread-read-status-row">
                                                            <?php echo esc_html(date_i18n("l F d, Y, H:i:s", MJTC_majesticsupportphplib::MJTC_strtotime($reply->viewed_on))); ?>
                                                        </span>
                                                    </span>
                                                </span>
                                                <?php 
                                            } ?>
                                        </div>
                                        <div class="mjtc-support-thread-data">
                                            <span class="mjtc-support-via-email">
                                                <?php echo ($reply->ticketviaemail == 1) ? esc_html(__('Created via Email', 'majestic-support')) : ''; ?>
                                            </span>
                                        </div>
                                        <div class="mjtc-support-thread-data note-msg">
                                            <?php echo wp_kses_post(html_entity_decode($reply->message)); ?>
                                        </div>
                                        <?php
                                            if (!empty($reply->attachments)) {
                                                foreach ($reply->attachments AS $attachment) {
                                                    $imgpath = $attachment->filename;
                                                    $MJTC_data = wp_check_filetype($attachment->filename);
                                                    $type = $MJTC_data['type'];
                                                    $MJTC_count = 0;
                                                    $path = esc_url(admin_url("?page=majesticsupport_ticket&action=mstask&task=downloadbyid&id=".esc_attr($attachment->id)));
                                                    $tktdata = '
                                                    <div class="mjtc_supportattachment">
                                                        <span class="mjtc_supportattachment_fname">
                                                        ' . esc_html($attachment->filename) . '
                                                        </span>
                                                        <a title="'.esc_html(__('Download','majestic-support')).'" class="button" target="_blank" href="' . esc_url($path) . '">' . esc_html(__('Download', 'majestic-support')) . '</a>';
                                                        echo wp_kses($tktdata, MJTC_ALLOWED_TAGS);
                                                        if(MJTC_majesticsupportphplib::MJTC_strpos($type, "image") !== false) {
                                                            $path = MJTC_includer::MJTC_getModel('attachment')->getAttachmentImage($attachment->id);
                                                            $tktdata = '<a data-gall="gallery-'.esc_attr($reply->replyid).'" class="button venobox" data-vbtype="image" title="'.esc_html(__('View','majestic-support')).'" href="'. esc_attr($path) .'"  target="_blank">
                                                                <img alt="'.esc_html(__('View Image','majestic-support')).'" src="' . esc_url(MJTC_PLUGIN_URL) . 'includes/images/ticket-detail/view.png" />
                                                            </a>';
                                                            echo wp_kses($tktdata, MJTC_ALLOWED_TAGS);
                                                        }
                                                    echo wp_kses('</div>', MJTC_ALLOWED_TAGS);
                                                }
                                            }
                                        
                                        ?>
                                        <div class="mjtc-support-thread-cnt-btm">
                                            <?php
                                            if (in_array('aipoweredreply', majesticsupport::$_active_addons) && majesticsupport::$_data[0]->uid != $reply->uid && $reply->uid != 0) { ?>
                                                <!-- This section contains the AI Reply Feature -->
                                                <div class="mjtc-support-ai-reply-status-wrapper">
                                                    <label for="mjtc-support-ai-reply-status-control">
                                                        <?php echo esc_html__('AI-Powered Reply Mode', 'majestic-support').':'; ?>
                                                    </label>
                                                    <div class="mjtc-support-info-icon-wrapper">
                                                        <span class="mjtc-support-info-icon" data-tooltip="<?php echo esc_html(__("Control how this individual reply influences the AI search and response generation process for future queries.",'majestic-support')); ?>">
                                                            <img alt="<?php echo esc_html(__('Info','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/ticket-detail/info-icon.png" />
                                                        </span>
                                                    </div>
                                                    <div id="mjtc-support-ai-reply-status-control" class="mjtc-support-segmented-control">
                                                        <button type="button" class="mjtc-support-segmented-control-option mjtc-support-default <?php echo ($reply->aireplymode == 0) ? 'active' : ''; ?>" data-value="0" data-type="reply" data-id="<?php echo esc_attr($reply->replyid);?>" title="<?php echo esc_attr(__("Default: reply included in all AI search queries.", "majestic-support")); ?>">
                                                            <?php echo esc_html__('Default', 'majestic-support'); ?>
                                                        </button>
                                                        <button type="button" class="mjtc-support-segmented-control-option mjtc-support-enable <?php echo ($reply->aireplymode == 1) ? 'active' : ''; ?>" data-value="1" data-type="reply" data-id="<?php echo esc_attr($reply->replyid);?>" title="<?php echo esc_attr(__("Enable: reply used in AI queries only when the Enable Tickets filter is active.", "majestic-support")); ?>">
                                                            <?php echo esc_html__('Enable', 'majestic-support'); ?>
                                                        </button>
                                                        <button type="button" class="mjtc-support-segmented-control-option mjtc-support-disable <?php echo ($reply->aireplymode == 2) ? 'active' : ''; ?>" data-value="2" data-type="reply" data-id="<?php echo esc_attr($reply->replyid);?>" title="<?php echo esc_attr(__("Disable: reply excluded from AI queries.", "majestic-support")); ?>">
                                                            <?php echo esc_html__('Disable', 'majestic-support'); ?>
                                                        </button>
                                                    </div>
                                                    <!-- Hidden input to hold the current selected value -->
                                                    <input type="hidden" name="mjtc_support_ai_reply_status" id="mjtc-support-ai-reply-status-hidden" value="<?php echo esc_attr($reply->aireplymode);?>" />
                                                </div>
                                                <?php
                                            } ?>
                                            <div class="mjtc-support-thread-date"><?php echo esc_html(date_i18n("l F d, Y, H:i:s", MJTC_majesticsupportphplib::MJTC_strtotime($reply->created))); ?></div>
                                            <div class="mjtc-support-thread-actions">
                                               <?php
                                               if(in_array('timetracking', majesticsupport::$_active_addons)){
                                                    if($reply->time > 0 ){
                                                        $nonce = wp_create_nonce("get-time-by-reply-id-".$reply->replyid); ?>
                                                        <a title="<?php echo esc_attr(__('Edit Time','majestic-support')); ?>" class="mjtc-support-thread-actn-btn ticket-edit-time-button" href="#" onclick="return showPopupAndFillValues(<?php echo esc_js($reply->replyid);?>,2, '<?php echo esc_js($nonce);?>')" >
                                                           <img alt="<?php echo esc_html(__('Edit Time','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/ticket-detail/edit-reply.png" />
                                                           <span><?php echo esc_html(__('Edit Time','majestic-support')); ?></span>
                                                        </a>
                                                    <?php
                                                    }
                                                }
                                                if($reply->staffid != 0){
                                                    $nonce = wp_create_nonce('get-reply-data-by-id-'.$reply->replyid); ?>
                                                    <a ttile="<?php echo esc_html(__('Edit Reply','majestic-support')); ?>" class="mjtc-support-thread-actn-btn ticket-edit-reply-button" href="#" onclick="return showPopupAndFillValues(<?php echo esc_js($reply->replyid);?>,1, '<?php echo esc_js($nonce);?>')" >
                                                       <img alt="<?php echo esc_html(__('Edit Reply','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/ticket-detail/edit-reply.png" />
                                                       <span><?php echo esc_html(__('Edit Reply','majestic-support')); ?></span>
                                                    </a>
                                                    <?php
                                                } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        <?php } ?>
                        <!-- Post Reply Area -->
                        <div id="postreply" class="mjtc-det-tkt-rply-frm">
                            <form class="mjtc-det-tkt-form" method="post" action="<?php echo esc_url(wp_nonce_url(admin_url("admin.php?page=majesticsupport_reply&task=savereply"),"save-reply-".majesticsupport::$_data[0]->id)); ?>"  enctype="multipart/form-data">
                                <div class="mjtc-sprt-det-title"><?php echo esc_html(__('Post Reply', 'majestic-support')); ?></div>
                                <?php if(in_array('timetracking', majesticsupport::$_active_addons)){ ?>
                                    <div class="ms-ticket-detail-timer-wrapper"> <!-- Timer Wrapper -->
                                        <div class="timer-left" >
                                            <div class="timer-total-time" >
                                                <?php
                                                    $hours = floor(majesticsupport::$_data['time_taken'] / 3600);
                                                    $mins = floor(majesticsupport::$_data['time_taken'] / 60);
                                                    $mins = floor($mins % 60);
                                                    $secs = floor(majesticsupport::$_data['time_taken'] % 60);
                                                    echo esc_html(__('Time Taken','majestic-support')) . ': ' . esc_html(sprintf('%02d:%02d:%02d', $hours, $mins, $secs));
                                                ?>
                                            </div>
                                        </div>
                                        <div class="timer-right" >
                                            <div class="timer" >
                                                00:00:00
                                            </div>
                                            <div class="timer-buttons" >
                                                <?php if(in_array('agent', majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForTask('Edit Own Time')){ ?>
                                                    <span class="timer-button" onclick="showEditTimerPopup()" >
                                                        <img alt="<?php echo esc_html(__('Edit','majestic-support')); ?>" class="default-show" alt="<?php echo esc_html(__('image', 'majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL);?>includes/images/ticket-detail/edit-time-1.png"/>
                                                        <img alt="<?php echo esc_html(__('Edit','majestic-support')); ?>" class="default-hide" alt="<?php echo esc_html(__('image', 'majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL);?>includes/images/ticket-detail/edit-time.png"/>
                                                    </span>
                                                <?php } ?>
                                                <span class="timer-button cls_1" onclick="changeTimerStatus(1)" >
                                                    <img alt="<?php echo esc_html(__('play','majestic-support')); ?>" class="default-show" alt="<?php echo esc_html(__('image', 'majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL);?>includes/images/ticket-detail/play-time-1.png"/>
                                                    <img alt="<?php echo esc_html(__('play','majestic-support')); ?>" class="default-hide" alt="<?php echo esc_html(__('image', 'majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL);?>includes/images/ticket-detail/play-time.png"/>
                                                </span>
                                                <span class="timer-button cls_2" onclick="changeTimerStatus(2)" >
                                                    <img <?php echo esc_html(__('pause','majestic-support')); ?> class="default-show" alt="<?php echo esc_html(__('image', 'majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL);?>includes/images/ticket-detail/pause-time-1.png"/>
                                                    <img <?php echo esc_html(__('pause','majestic-support')); ?> class="default-hide" alt="<?php echo esc_html(__('image', 'majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL);?>includes/images/ticket-detail/pause-time.png"/>
                                                </span>
                                                <span class="timer-button cls_3" onclick="changeTimerStatus(3)" >
                                                    <img <?php echo esc_html(__('stop','majestic-support')); ?> class="default-show" alt="<?php echo esc_html(__('image', 'majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL);?>includes/images/ticket-detail/stop-time-1.png"/>
                                                    <img <?php echo esc_html(__('stop','majestic-support')); ?> class="default-hide" alt="<?php echo esc_html(__('image', 'majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL);?>includes/images/ticket-detail/stop-time.png"/>
                                                </span>
                                            </div>
                                        </div>
                                        <?php echo wp_kses(MJTC_formfield::MJTC_hidden('timer_time_in_seconds',''), MJTC_ALLOWED_TAGS); ?>
                                        <?php echo wp_kses(MJTC_formfield::MJTC_hidden('timer_edit_desc',''), MJTC_ALLOWED_TAGS); ?>
                                    </div>
                                <?php } ?>
                                <!-- Smart Reply Area -->
                                <div class="mjtc-form-wrapper">
                                    <div class="mjtc-support-ai-powered-reply-wrapper">
                                        <div class="mjtc-support-ai-powered-reply-icon">
                                            <img alt="<?php echo esc_html(__('AI Icon','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/ticket-detail/ai-icon.png" />
                                        </div>
                                        <div class="mjtc-support-ai-powered-reply-content">
                                            <div class="mjtc-support-ai-powered-reply-title">
                                                <?php echo esc_html__('AI-Powered Reply', 'majestic-support'); ?>
                                            </div>
                                            <div class="mjtc-support-ai-powered-reply-text">
                                                <?php echo esc_html__('Get context-based suggestions to effortlessly create accurate and relevant responses.', 'majestic-support'); ?>
                                            </div>
                                        </div>
                                        <div id="mjtc-support-ai-reply-btn" class="mjtc-support-ai-powered-reply-action">
                                            <a href="#" class="mjtc-support-ai-powered-reply-button">
                                                <?php echo esc_html__('Suggested Response', 'majestic-support'); ?>
                                            </a>
                                        </div>
                                    </div>
                                    <span class="mjtc-support-current-ticket-title"><?php echo esc_attr(majesticsupport::$_data[0]->subject) ?></span>
                                    <span class="mjtc-support-current-ticket-id"><?php echo esc_attr(majesticsupport::$_data[0]->id) ?></span>
                                    <div class="mjtc-support-container">
                                        <!-- Matching Tickets Section -->
                                        <div id="mjtc-support-matching-tickets-section" class="mjtc-support-section mjtc-support-matching-tickets-section mjtc-support-hidden">
                                            <div class="mjtc-support-selected-tickets-header">
                                                <h2 class="mjtc-support-section-heading"><?php echo esc_html__('Matching Results', 'majestic-support'); ?></h2>
                                                <?php if(in_array('aipoweredreply', majesticsupport::$_active_addons)){ ?>
                                                    <div class="mjtc-support-filter-group">
                                                        <label for="mjtc-support-tickets-filter" class="mjtc-support-filter-label"><?php echo esc_html__('Filter', 'majestic-support').': '; ?></label>
                                                        <select id="mjtc-support-tickets-filter" class="mjtc-support-filter-select">
                                                            <option value="all"><?php echo esc_html__('All Tickets', 'majestic-support'); ?></option>
                                                            <option value="marked"><?php echo esc_html__('Enable Tickets', 'majestic-support'); ?></option>
                                                        </select>
                                                    </div>
                                                <?php } ?>
                                                <button id="mjtc-support-close-tickets-btn" class="mjtc-support-close-button">
                                                    <?php echo esc_html__('Close', 'majestic-support'); ?>
                                                </button>
                                            </div>
                                            <ul id="mjtc-support-matching-tickets-list" class="mjtc-support-list">
                                                <!-- Matching tickets will be dynamically inserted here -->
                                            </ul>
                                        </div>

                                        <!-- Selected Ticket Replies Section -->
                                        <div id="mjtc-support-selected-ticket-replies-section" class="mjtc-support-section mjtc-support-selected-replies-section mjtc-support-hidden">
                                            <div class="mjtc-support-selected-replies-header">
                                                <h2 class="mjtc-support-section-heading" id="mjtc-support-selected-ticket-replies-title"></h2>
                                                <?php if(in_array('aipoweredreply', majesticsupport::$_active_addons)){ ?>
                                                    <div class="mjtc-support-filter-group">
                                                        <label for="mjtc-support-replies-filter" class="mjtc-support-filter-label"><?php echo esc_html__('Filter', 'majestic-support').': '; ?></label>
                                                        <select id="mjtc-support-replies-filter" class="mjtc-support-filter-select">
                                                            <option value="all"><?php echo esc_html__('All Replies', 'majestic-support'); ?></option>
                                                            <option value="marked"><?php echo esc_html__('Enable Replies', 'majestic-support'); ?></option>
                                                        </select>
                                                    </div>
                                                <?php } ?>
                                                <button id="mjtc-support-close-replies-btn" class="mjtc-support-close-button">
                                                    <?php echo esc_html__('Close', 'majestic-support'); ?>
                                                </button>
                                            </div>
                                            <div id="mjtc-support-selected-ticket-replies-content" class="mjtc-support-replies-content reply-content">
                                                <!-- Replies from selected ticket will be dynamically inserted here -->
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
                                </div>
                                <div class="mjtc-form-wrapper">
                                    <div class="mjtc-form-title"><label id="responcemsg" for="responce"><?php echo esc_html(__('Response', 'majestic-support')); ?><span style="color: red;" >*</span></label></div>
                                    <div class="mjtc-form-value"><?php wp_editor('', 'mjsupport_message', array('media_buttons' => false)); ?></div>
                                </div>
                                <?php
                                if(in_array('cannedresponses', majesticsupport::$_active_addons)){
                                    $cannedresponses = MJTC_includer::MJTC_getModel('cannedresponses')->getPreMadeMessageForCombobox();
                                    ?>
                                    <div class="mjtc-form-wrapper mjtc-premade-response-wrapper">
                                        <div class="mjtc-form-title"><label id="responcemsg" for="responce"><?php echo esc_html(__('Premade Response', 'majestic-support')).' :'; ?></label></div>
                                        <div class="mjtc-form-value">
                                            <div class="mjtc-support-detail-append-signature-xs">
                                                <?php echo wp_kses(MJTC_formfield::MJTC_checkbox('append_premade', array('1' => esc_html(__('Append', 'majestic-support'))), '', array('class' => 'radiobutton')), MJTC_ALLOWED_TAGS); ?>
                                            </div>
                                            <?php
                                            foreach($cannedresponses as $premade){
                                                ?>
                                                <div class="mjtc-sprt-det-perm-msg" onclick="getpremade(<?php echo esc_js($premade->id); ?>);">
                                                    <a href="javascript:void(0);" title="<?php echo esc_attr(__('premade','majestic-support')); ?>"><?php echo esc_html($premade->text); ?></a>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                    </div>
                                <?php } ?>
                                <div class="mjtc-form-wrapper">
                                    <div class="mjtc-form-title"><?php echo esc_html(__('Attachments', 'majestic-support')); ?></div>
                                    <div class="mjtc-form-field">
                                        <div class="tk_attachment_value_wrapperform tk_attachment_admin_reply_wrapper">
                                            <span class="tk_attachment_value_text">
                                                <input type="file" class="inputbox" name="filename[]" onchange="MJTC_uploadfile(this, '<?php echo esc_js(majesticsupport::$_config['file_maximum_size']); ?>', '<?php echo esc_js(majesticsupport::$_config['file_extension']); ?>');" size="20" maxlenght='30'/>
                                                <span class='tk_attachment_remove'></span>
                                                </span>
                                            </div>
                                            <span class="tk_attachments_configform">
                                                <small>
                                                    <?php
                                                    $tktdata = esc_html(__('Maximum File Size','majestic-support')).' (' . esc_html(majesticsupport::$_config['file_maximum_size']).'KB)<br>'. esc_html(__('File Extension Type','majestic-support')).' (' . esc_html(majesticsupport::$_config['file_extension']) . ')';
                                                    echo wp_kses($tktdata, MJTC_ALLOWED_TAGS);
                                                    ?>
                                                </small>
                                            </span>
                                            <span id="tk_attachment_add" data-ident="tk_attachment_admin_reply_wrapper" class="tk_attachments_addform ms-button-bg-link"><?php echo esc_html(__('Add More Files','majestic-support')); ?></span>
                                        </div>
                                    </div>
                                <div class="mjtc-form-wrapper">
                                    <div class="mjtc-form-title"><?php echo esc_html(__('Append Signature', 'majestic-support')); ?></div>
                                    <div class="mjtc-form-value">
                                        <div class="ms-formfield-radio-button-wrap">
                                            <?php 
												$MJTC_default_value = "";
												if (current_user_can('manage_options')) { // admin
													if(get_user_meta(MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid(), 'ms_signature_auto_append', true)=='1' ){
														$MJTC_default_value = "1";
													}
												}

											echo wp_kses(MJTC_formfield::MJTC_checkbox('ownsignature', array('1' => esc_html(__('Own Signature', 'majestic-support'))), $MJTC_default_value, array('class' => 'radiobutton')), MJTC_ALLOWED_TAGS); ?>
                                        </div>
                                        <div class="ms-formfield-radio-button-wrap">
                                            <?php echo wp_kses(MJTC_formfield::MJTC_checkbox('departmentsignature', array('1' => esc_html(majesticsupport::MJTC_getVarValue($field_array['department'])) ." ". esc_html(__('Signature', 'majestic-support'))), '', array('class' => 'radiobutton')), MJTC_ALLOWED_TAGS); ?>
                                        </div>
                                        <div class="ms-formfield-radio-button-wrap">
                                            <?php echo wp_kses(MJTC_formfield::MJTC_checkbox('nonesignature', array('1' => esc_html(__('None', 'majestic-support'))), '', array('class' => 'radiobutton')), MJTC_ALLOWED_TAGS); ?>
                                        </div>
                                    </div>
                                    <?php
                                    $signature = get_user_meta(MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid(), 'ms_signature', true);
                                    if(!$signature){
                                        ?>
                                        <a class="mjtc-add-signature" target= "_blank" href="<?php echo esc_url(admin_url('profile.php#mssignature')); ?>"><?php echo esc_html(__("Add Signature",'majestic-support')); ?></a>
                                        <?php
                                    }
                                    ?>
                                </div>
                                <?php
                                if ( in_array('agent',majesticsupport::$_active_addons) ) {
                                    $staffid = MJTC_includer::MJTC_getModel('agent')->getStaffId(MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid());
                                    if (majesticsupport::$_data[0]->staffid != $staffid && $staffid != '') {?>
                                    <div class="mjtc-form-wrapper">
                                        <div class="mjtc-form-title"><?php echo esc_html(__('Assign to me', 'majestic-support')); ?></div>
                                        <div class="ms-formfield-radio-button-wrap">
                                            <?php echo wp_kses(MJTC_formfield::MJTC_checkbox('assigntome', array('1' => esc_html(__('Assign to me', 'majestic-support'))), '', array('class' => 'radiobutton')), MJTC_ALLOWED_TAGS); ?>
                                        </div>
                                    </div>
                                    <?php }
                                } ?>
                                <div class="mjtc-form-wrapper">
                                    <div class="mjtc-form-title">
                                            <?php
                                            $tktdata = esc_html(__('Ticket', 'majestic-support')).' '.esc_html(majesticsupport::MJTC_getVarValue($field_array['status']));
                                            echo wp_kses($tktdata, MJTC_ALLOWED_TAGS);
                                            ?>
                                        </div>
                                    <div class="ms-formfield-radio-button-wrap">
                                        <?php echo wp_kses(MJTC_formfield::MJTC_checkbox('closeonreply', array('1' => esc_html(__('Close on reply', 'majestic-support'))), '', array('class' => 'radiobutton')), MJTC_ALLOWED_TAGS); ?>
                                    </div>
                                </div>
                                <div class="mjtc-form-button">
                                    <?php echo wp_kses(MJTC_formfield::MJTC_submitbutton('postreply', esc_html(__('Post Reply','majestic-support')), array('class' => 'button mjtc-form-save', 'onclick' => "return checktinymcebyid('message');")), MJTC_ALLOWED_TAGS); ?>
                                </div>
                                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('departmentid', majesticsupport::$_data[0]->departmentid), MJTC_ALLOWED_TAGS); ?>
                                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('ticketid', majesticsupport::$_data[0]->id), MJTC_ALLOWED_TAGS); ?>
                                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('internalid', majesticsupport::$_data[0]->internalid), MJTC_ALLOWED_TAGS); ?>
                                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('ticketrandomid', majesticsupport::$_data[0]->ticketid), MJTC_ALLOWED_TAGS); ?>
                                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('hash', majesticsupport::$_data[0]->hash), MJTC_ALLOWED_TAGS); ?>
                                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('uid', MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid()), MJTC_ALLOWED_TAGS); ?>
                                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('action', 'reply_savereply'), MJTC_ALLOWED_TAGS); ?>
                                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('form_request', 'majesticsupport'), MJTC_ALLOWED_TAGS); ?>
                            </form>
                        </div> <!-- end of postreply div -->
                    </div>
                    <!-- ticket detail right side -->
                    <div class="mjtc-sprt-det-right">
                        <!-- ticket detail info -->
                        <div class="mjtc-sprt-det-cnt mjtc-sprt-det-tkt-info">
                            <?php
                            if (majesticsupport::$_data[0]->status == 5 || 
                                majesticsupport::$_data[0]->status == 3 || 
                                majesticsupport::$_data[0]->status == 6) {
                                $stylecolor = majesticsupport::$_data[0]->statuscolour;
                                $stylebgcolor = majesticsupport::$_data[0]->statusbgcolour;
                                $MJTC_ticketmessage = esc_html(majesticsupport::$_data[0]->statustitle);
                            } else {
                                $MJTC_ticketmessage = esc_html(__('Open', 'majestic-support'));
                                $stylecolor = '#FFFFFF';
                                $stylebgcolor = '#5bb12f';
                            } ?>
                            <div class="mjtc-sprt-det-status" style="background-color:<?php echo esc_attr($stylebgcolor)?>;color:<?php echo esc_attr($stylecolor);?>;">
                                <?php
                                    majesticsupport::$_data['custom']['ticketid'] = majesticsupport::$_data[0]->id;
                                    echo esc_html($MJTC_ticketmessage);
                                ?>
                            </div>
                            <?php
                            if (isset(majesticsupport::$_data[0]->closedreason) && majesticsupport::$_data[0]->status == 4) { ?>
                                <?php
                                $closedreasons = json_decode(majesticsupport::$_data[0]->closedreason);
                                if (is_array($closedreasons)) { ?>
                                    <div class="mjtc-sprt-det-close-reason-wrp">
                                        <div class="mjtc-sprt-det-info-data">
                                            <span class="mjtc-sprt-det-info-tit">
                                                <?php echo esc_html(__('Ticket Closing Reason', 'majestic-support')). ': '; ?>
                                            </span>
                                        </div>
                                        <?php
                                        foreach ($closedreasons as $closedreason) { ?>
                                            <div class="mjtc-sprt-det-info-data">
                                                <span class="mjtc-sprt-det-info-val">
                                                    <?php echo esc_html(majesticsupport::MJTC_getVarValue($closedreason)); ?>
                                                </span>
                                            </div>
                                            <?php
                                        } ?>
                                    </div>
                                    <?php
                                } ?>                                
                                <?php
                            } ?>
                            <div class="mjtc-sprt-det-info-cnt">
                                <div class="mjtc-sprt-det-info-data">
                                    <span class="mjtc-sprt-det-info-tit">
                                        <?php echo esc_html(__('Created', 'majestic-support')). ': '; ?>
                                    </span>
                                    <span class="mjtc-sprt-det-info-val" title="<?php echo esc_attr(date_i18n("d F, Y, H:i:s A", MJTC_majesticsupportphplib::MJTC_strtotime(majesticsupport::$_data[0]->created))); ?>">
                                        <?php echo esc_html(human_time_diff(MJTC_majesticsupportphplib::MJTC_strtotime(majesticsupport::$_data[0]->created),MJTC_majesticsupportphplib::MJTC_strtotime(date_i18n("Y-m-d H:i:s")))).' '. esc_html(__('ago', 'majestic-support')); ?>
                                    </span>
                                </div>
                                <div class="mjtc-sprt-det-info-data">
                                    <span class="mjtc-sprt-det-info-tit">
                                        <?php echo esc_html(__('Last Reply', 'majestic-support')). ': '; ?>
                                    </span>
                                    <span class="mjtc-sprt-det-info-val">
                                        <?php
                                            if (empty(majesticsupport::$_data[0]->lastreply) || majesticsupport::$_data[0]->lastreply == '0000-00-00 00:00:00') echo esc_html(__('No Last Reply', 'majestic-support'));
                                            else echo esc_html(date_i18n(majesticsupport::$_config['date_format'], MJTC_majesticsupportphplib::MJTC_strtotime(majesticsupport::$_data[0]->lastreply)));
                                        ?>
                                    </span>
                                </div>
                                <?php if(in_array('helptopic', majesticsupport::$_active_addons)){ ?>
                                    <div class="mjtc-sprt-det-info-data">
                                        <span class="mjtc-sprt-det-info-tit">
                                            <?php echo esc_html(majesticsupport::MJTC_getVarValue($field_array['helptopic'])). ': '; ?>
                                        </span>
                                        <span class="mjtc-sprt-det-info-val">
                                            <?php if(in_array('helptopic',majesticsupport::$_active_addons)){ ?>
                                                <?php 
                                                    if (!empty(majesticsupport::$_data[0]) && isset(majesticsupport::$_data[0]->helptopic)) {
                                                        echo wp_kses_post(majesticsupport::$_data[0]->helptopic);
                                                    }
                                                ?>
                                            <?php } ?>
                                        </span>
                                    </div>
                                    <?php
                                } ?>
                                <div class="mjtc-sprt-det-info-data">
                                    <span class="mjtc-sprt-det-info-tit">
                                        <?php echo esc_html(majesticsupport::MJTC_getVarValue($field_array['product'])). ': '; ?>
                                    </span>
                                    <span class="mjtc-sprt-det-info-val">
                                        <?php 
                                            if (!empty(majesticsupport::$_data[0]) && isset(majesticsupport::$_data[0]->producttitle)) {
                                                echo wp_kses_post(majesticsupport::$_data[0]->producttitle);
                                            }
                                        ?>
                                    </span>
                                </div>
                                <div class="mjtc-sprt-det-info-data">
                                    <span class="mjtc-sprt-det-info-tit">
                                        <?php echo esc_html(majesticsupport::MJTC_getVarValue($field_array['department'])). ': '; ?>
                                    </span>
                                    <span class="mjtc-sprt-det-info-val">
                                        <?php echo esc_html(majesticsupport::MJTC_getVarValue(majesticsupport::$_data[0]->departmentname)); ?>
                                    </span>
                                </div>
                                <?php if (majesticsupport::$_config['show_closedby_on_admin_tickets'] == 1 && majesticsupport::$_data[0]->status == 5) { ?>
                                    <div class="mjtc-sprt-det-info-data">
                                        <span class="mjtc-sprt-det-info-tit">
                                            <?php echo esc_html(__('Closed By', 'majestic-support')). ' : '; ?>
                                        </span>
                                        <span class="mjtc-sprt-det-info-val">
                                            <?php echo esc_html(MJTC_includer::MJTC_getModel('ticket')->getClosedBy(majesticsupport::$_data[0]->closedby)); ?>
                                        </span>
                                    </div>
                                    <div class="mjtc-sprt-det-info-data">
                                        <span class="mjtc-sprt-det-info-tit">
                                            <?php echo esc_html(__('Closed On', 'majestic-support')). ' : '; ?>
                                        </span>
                                        <span class="mjtc-sprt-det-info-val">
                                            <?php echo esc_html(date_i18n(majesticsupport::$_config['date_format'], MJTC_majesticsupportphplib::MJTC_strtotime(majesticsupport::$_data[0]->closed))); ?>
                                        </span>
                                    </div>
                                <?php } ?>
                                <div class="mjtc-sprt-det-info-data">
                                    <span class="mjtc-sprt-det-info-tit">
                                        <?php echo esc_html(__('Ticket ID', 'majestic-support')). ': '; ?>
                                    </span>
                                    <span class="mjtc-sprt-det-info-val">
                                        <?php echo esc_html(majesticsupport::$_data[0]->ticketid); ?>
                                        <a href="javascript:void(0)" title="<?php echo esc_attr(__('Copy','majestic-support')); ?>" class="mjtc-sprt-det-copy-id" id="ticketidcopybtn" success="<?php echo esc_html(__('Copied','majestic-support')); ?>"><?php echo esc_html(__('Copy','majestic-support')); ?></a>
                                    </span>
                                </div>
                                <div class="mjtc-sprt-det-info-data">
                                    <span class="mjtc-sprt-det-info-tit">
                                        <?php echo esc_html(majesticsupport::MJTC_getVarValue($field_array['status'])). ': '; ?>
                                    </span>
                                    <span class="mjtc-sprt-det-info-val">
                                        <?php
                                            $printstatus = 1;
                                            if (majesticsupport::$_data[0]->lock == 1) {
                                                $tktdata = '<span>' . esc_html(__('Lock', 'majestic-support')) . '</span>';
                                                    echo wp_kses($tktdata, MJTC_ALLOWED_TAGS);
                                                    $printstatus = 0;
                                            }
                                            if (majesticsupport::$_data[0]->isoverdue == 1) {
                                                $tktdata = '<span>' . esc_html(__('Overdue', 'majestic-support')) . '</span>';
                                                    echo wp_kses($tktdata, MJTC_ALLOWED_TAGS);
                                                $printstatus = 0;
                                            }
                                            if ($printstatus == 1) {
                                                echo wp_kses_post($MJTC_ticketmessage);
                                            }
                                        ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <!-- ticket detail status -->
                        <div class="mjtc-sprt-det-cnt mjtc-sprt-det-tkt-prty">
                            <div class="mjtc-sprt-det-hdg">
                                <div class="mjtc-sprt-det-hdg-txt">
                                    <?php echo esc_html(__('Status','majestic-support')); ?>
                                </div>
                                <a title="<?php echo esc_html(__('Change','majestic-support')); ?>" href="#" class="mjtc-sprt-det-hdg-btn" id="chng-status">
                                    <?php echo esc_html(__('Change','majestic-support')); ?>
                                </a>
                            </div>
                            <?php
                                if (!empty(majesticsupport::$_data[0]->status)) { ?>
                                    <div class="mjtc-sprt-det-tkt-prty-txt" style="background : <?php echo esc_attr(majesticsupport::$_data[0]->statusbgcolour); ?>;color : <?php echo esc_attr(majesticsupport::$_data[0]->statuscolour); ?>;">
                                        <?php
                                        echo esc_html(majesticsupport::MJTC_getVarValue(majesticsupport::$_data[0]->statustitle)); ?>
                                    </div>
                                    <?php
                                }
                            ?>
                        </div>
                        <!-- ticket detail status -->
                        <?php if(in_array('aipoweredreply', majesticsupport::$_active_addons)){ ?>
                            <div class="mjtc-sprt-det-cnt mjtc-sprt-det-tkt-prty">
                                <div class="mjtc-sprt-det-hdg">
                                    <div class="mjtc-sprt-det-hdg-txt">
                                        <label for="mjtc-support-ai-reply-status-control">
                                            <?php echo esc_html__('AI-Powered Reply Mode', 'majestic-support'); ?>
                                        </label>
                                        <div class="mjtc-support-info-icon-wrapper">
                                            <span class="mjtc-support-info-icon" data-tooltip="<?php echo esc_html(__("Control how this ticket and its replies influence AI search and response generation for future queries.",'majestic-support')); ?>">
                                                <img alt="<?php echo esc_html(__('Info','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/ticket-detail/info-icon.png" />
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <!-- This section contains the AI Reply Feature -->
                                <div class="mjtc-support-ai-reply-status-wrapper">
                                    <div id="mjtc-support-ai-reply-status-control" class="mjtc-support-segmented-control">
                                        <button type="button" class="mjtc-support-segmented-control-option mjtc-support-default <?php echo (majesticsupport::$_data[0]->aireplymode == 0) ? 'active' : ''; ?>" data-value="0" data-type="ticket" data-id="<?php echo esc_attr(majesticsupport::$_data[0]->id);?>" title="<?php echo esc_attr(__("Default: ticket and replies included in all AI queries.", "majestic-support")); ?>">
                                            <?php echo esc_html__('Default', 'majestic-support'); ?>
                                        </button>
                                        <button data-type="ticket" type="button" class="mjtc-support-segmented-control-option mjtc-support-enable <?php echo (majesticsupport::$_data[0]->aireplymode == 1) ? 'active' : ''; ?>" data-value="1" data-type="ticket" data-id="<?php echo esc_attr(majesticsupport::$_data[0]->id);?>" title="<?php echo esc_attr(__("Enable: ticket and replies used only when the “Enable Tickets” filter is active.", "majestic-support")); ?>">
                                            <?php echo esc_html__('Enable', 'majestic-support'); ?>
                                        </button>
                                        <button data-type="ticket" type="button" class="mjtc-support-segmented-control-option mjtc-support-disable <?php echo (majesticsupport::$_data[0]->aireplymode == 2) ? 'active' : ''; ?>" data-value="2" data-type="ticket" data-id="<?php echo esc_attr(majesticsupport::$_data[0]->id);?>" title="<?php echo esc_attr(__("Disable: ticket and replies excluded from AI queries.", "majestic-support")); ?>">
                                            <?php echo esc_html__('Disable', 'majestic-support'); ?>
                                        </button>
                                    </div>
                                    <!-- Hidden input to hold the current selected value -->
                                    <input type="hidden" name="mjtc_support_ai_reply_status" id="mjtc-support-ai-reply-status-hidden" value="<?php echo esc_attr(majesticsupport::$_data[0]->aireplymode);?>" />
                                </div>
                            </div>
                            <?php
                        } ?>
                        <!-- ticket detail priority -->
                        <?php
                        if (isset($field_array['priority'])) { ?>
                            <div class="mjtc-sprt-det-cnt mjtc-sprt-det-tkt-prty">
                                <div class="mjtc-sprt-det-hdg">
                                    <a target="blank" href="https://www.youtube.com/watch?v=k9n33ao35Mg" class="mjtc-sprt-det-hdg-img mjtc-cp-video-priority">
                                        <img title="<?php echo esc_attr(__('watch video','majestic-support')); ?>" alt="<?php echo esc_html(__('watch video','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL) . '/includes/images/watch-video-icon.png'; ?>" />
                                    </a>
                                    <div class="mjtc-sprt-det-hdg-txt">
                                        <!-- Display heading based on field order  -->
                                         <?php echo esc_html(majesticsupport::MJTC_getVarValue($field_array['priority'])); ?>
                                    </div>
                                    <a title="<?php echo esc_attr(__('Change','majestic-support')); ?>" href="#" class="mjtc-sprt-det-hdg-btn" id="chng-prority">
                                        <?php echo esc_html(__('Change','majestic-support')); ?>
                                    </a>
                                </div>
                                <?php
                                    if (!empty(majesticsupport::$_data[0]->priority)) { ?>
                                        <div class="mjtc-sprt-det-tkt-prty-txt" style="background:<?php echo esc_attr(majesticsupport::$_data[0]->prioritycolour); ?>;">
                                            <?php echo esc_html(majesticsupport::MJTC_getVarValue(majesticsupport::$_data[0]->priority)); ?>
                                        </div>
                                        <?php
                                    } else { ?>
                                        <div class="mjtc-sprt-det-tkt-prty-error-txt">
                                            <?php
                                            echo esc_html(__('No','majestic-support'))." ".esc_html(majesticsupport::MJTC_getVarValue($field_array['priority']))." ".esc_html(__('set','majestic-support')); ?>
                                        </div>
                                        <?php
                                    } ?>
                            </div>
                            <?php
                        } ?>
                        <!-- ticket detail assign to staff -->
                        <?php
                        $agentflag = in_array('agent', majesticsupport::$_active_addons);
                        $MJTC_departmentflag = (in_array('actions', majesticsupport::$_active_addons) && isset($field_array['department'])) ? true : false;
                        if($agentflag || $MJTC_departmentflag){
                            ?>
                            <div class="mjtc-sprt-det-cnt mjtc-sprt-det-tkt-assign">
                                <?php if($agentflag){ ?>
                                <div class="mjtc-sprt-det-hdg">
                                    <div class="mjtc-sprt-det-hdg-txt">
                                        <?php echo esc_html(__('Assign And Transfer Ticket','majestic-support')); ?>
                                    </div>
                                </div>
                                <?php } ?>
                                <div class="mjtc-sprt-det-tkt-asgn-cnt">
                                    <?php if($agentflag){ ?>
                                    <div class="mjtc-sprt-det-hdg">
                                        <a target="blank" href="#" class="mjtc-sprt-det-hdg-img mjtc-cp-video-assign">
                                            <img title="<?php echo esc_attr(__('watch video','majestic-support')); ?>" alt="<?php echo esc_html(__('watch video','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL) . '/includes/images/watch-video-icon.png'; ?>" />
                                        </a>
                                        <div class="mjtc-sprt-det-hdg-txt">
                                            <?php
                                            if(majesticsupport::$_data[0]->staffid > 0){
                                                echo esc_html(__('Ticket assigned to','majestic-support'));
                                            }else{
                                                echo esc_html(__('Not assigned to agent','majestic-support'));
                                            }
                                            ?>
                                        </div>
                                        <a title="<?php echo esc_attr(__('Change','majestic-support')); ?>" href="#" class="mjtc-sprt-det-hdg-btn" id="asgn-staff">
                                            <?php echo esc_html(__('Change','majestic-support')); ?>
                                        </a>
                                    </div>
                                    <?php } ?>
                                    <div class="mjtc-sprt-det-info-wrp">
                                        <?php if(majesticsupport::$_data[0]->staffid > 0){ ?>
                                        <div class="mjtc-sprt-det-user">
                                            <div class="mjtc-sprt-det-user-image">
                                                <?php echo wp_kses(ms_get_avatar(majesticsupport::$_data[0]->staffuid), MJTC_ALLOWED_TAGS); ?>
                                            </div>
                                            <div class="mjtc-sprt-det-user-cnt">
                                                <div class="mjtc-sprt-det-user-data"><?php echo esc_html(majesticsupport::$_data[0]->staffname); ?></div>
                                                <div class="mjtc-sprt-det-user-data"><?php echo esc_html(majesticsupport::$_data[0]->staffemail); ?></div>
                                                <div class="mjtc-sprt-det-user-data"><?php echo esc_html(majesticsupport::$_data[0]->staffphone); ?></div>
                                            </div>
                                        </div>
                                        <?php } ?>
                                        <?php if($MJTC_departmentflag){ ?>
                                        <div class="mjtc-sprt-det-trsfer-dep">
                                            <a target="blank" href="#" class="mjtc-sprt-det-hdg-img mjtc-cp-video-department">
                                                <img title="<?php echo esc_attr(__('watch video','majestic-support')); ?>" alt="<?php echo esc_html(__('watch video','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL) . '/includes/images/watch-video-icon.png'; ?>" />
                                            </a>
                                            <div class="mjtc-sprt-det-trsfer-dep-txt">
                                                <!-- Display heading based on field order  -->
                                                <?php echo esc_html(majesticsupport::MJTC_getVarValue($field_array['department'])); ?>: <?php echo esc_html(majesticsupport::MJTC_getVarValue(majesticsupport::$_data[0]->departmentname)); ?>
                                            </div>
                                            <a title="<?php echo esc_attr(__('Change','majestic-support')); ?>" href="#" class="mjtc-sprt-det-hdg-btn" id="chng-dept">
                                                <?php echo esc_html(__('Change','majestic-support')); ?>
                                            </a>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                        <!-- ticket detail time tracking -->
                        <?php if(in_array('timetracking', majesticsupport::$_active_addons)){ ?>
                        <div class="mjtc-sprt-det-cnt mjtc-sprt-det-time-tracker">
                            <div class="mjtc-sprt-det-hdg">
                                <div class="mjtc-sprt-det-hdg-txt">
                                    <?php echo esc_html(__('Total Time Taken','majestic-support')); ?>
                                </div>
                            </div>
                            <div class="mjtc-sprt-det-timer-wrp"> <!-- Timer Wrapper -->
                                <div class="timer-total-time" >
                                    <?php
                                    $hours = floor(majesticsupport::$_data['time_taken'] / 3600);
                                    $mins = floor(majesticsupport::$_data['time_taken'] / 60);
                                    $mins = floor($mins % 60);
                                    $secs = floor(majesticsupport::$_data['time_taken'] % 60);
                                    $time =  sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
                                    ?>
                                    <div class="timer-total-time-value">
                                        <span class="timer-box">
                                            <?php echo esc_html(sprintf('%02d', $hours)); ?>
                                        </span>
                                        <span class="timer-box">
                                            <?php echo esc_html(sprintf('%02d', $mins)); ?>
                                        </span>
                                        <span class="timer-box">
                                            <?php echo esc_html(sprintf('%02d', $secs)); ?>
                                        </span>
                                    </div>
                                </div>
                                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('timer_time_in_seconds',''), MJTC_ALLOWED_TAGS); ?>
                                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('timer_edit_desc',''), MJTC_ALLOWED_TAGS); ?>
                            </div>
                        </div>
                        <?php } ?>
                        <!-- ticket detail user tickets -->
                        <?php if(isset(majesticsupport::$_data['usertickets']) && !empty(majesticsupport::$_data['usertickets'])){ ?>
                        <div class="mjtc-sprt-det-cnt mjtc-sprt-det-user-tkts" id="usr-tkt">
                            <div class="mjtc-sprt-det-hdg">
                                <div class="mjtc-sprt-det-hdg-txt">
                                    <?php
                                    if(!empty($field_array['fullname'])) {
                                        echo esc_html(majesticsupport::$_data[0]->name).' '. esc_html(__('Tickets','majestic-support'));
                                    } else {
                                        echo esc_html(__('Other Tickets','majestic-support'));
                                    } ?>
                                </div>
                            </div>
                            <div class="mjtc-sprt-det-usr-tkt-list">
                                <?php foreach (majesticsupport::$_data['usertickets'] AS $usertickets) { ?>
                                        <div class="mjtc-sprt-det-user">
                                            <div class="mjtc-sprt-det-user-image">
                                                <?php echo wp_kses(ms_get_avatar(majesticsupport::$_data[0]->uid), MJTC_ALLOWED_TAGS); ?>
                                            </div>
                                            <div class="mjtc-sprt-det-user-cnt">
                                                <div class="mjtc-sprt-det-user-data name">
                                                    <span id="usr-tkts">
                                                        <a title="<?php echo esc_attr(__('view ticket','majestic-support')); ?>" href="<?php echo esc_url(admin_url('admin.php?page=majesticsupport_ticket&mjslay=ticketdetail&majesticsupportid='.esc_attr($usertickets->id))); ?>">
                                                            <span class="mjtc-sprt-det-user-val"><?php echo esc_html($usertickets->subject); ?></span>
                                                        </a>
                                                    </span>
                                                </div>
                                                <?php
                                                if(isset($field_array['department'])){ ?>
                                                    <div class="mjtc-sprt-det-user-data">
                                                        <span class="mjtc-sprt-det-user-tit">
                                                        <!-- Display heading based on field order  --><?php echo esc_html(majesticsupport::MJTC_getVarValue($field_array['department'])). ' : '; ?></span>
                                                        <span class="mjtc-sprt-det-user-val"><?php echo esc_html(majesticsupport::MJTC_getVarValue($usertickets->departmentname)); ?></span>
                                                    </div>
                                                    <?php
                                                } ?>
                                                <div class="mjtc-sprt-det-user-data">
                                                    <?php
                                                    if(isset($field_array['priority'])){ ?>
                                                        <span class="mjtc-sprt-det-prty" style="background: <?php echo esc_attr($usertickets->prioritycolour); ?>;"><?php echo esc_html(majesticsupport::MJTC_getVarValue($usertickets->priority)); ?></span>
                                                        <?php
                                                    } ?>
                                                    <span class="mjtc-sprt-det-status">
                                                        <?php
                                                            if ($usertickets->status == 5 || 
                                                                $usertickets->status == 3 || 
                                                                $usertickets->status == 6) {
                                                                $MJTC_ticketmessage = esc_html($usertickets->statustitle);
                                                            } else {
                                                                $MJTC_ticketmessage = esc_html(__('Open', 'majestic-support'));
                                                            }
                                                            echo esc_html($MJTC_ticketmessage);
                                                        ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                <?php } ?>
                            </div>
                        </div>
                        <?php } ?>
                        <?php apply_filters( 'mjtc_support_ticket_frontend_details_right_middle', majesticsupport::$_data[0]->id); ?>
                        <!-- ticket detail woocomerece -->
                        <?php
                            if( class_exists('WooCommerce') && in_array('woocommerce', majesticsupport::$_active_addons)){
                                $order = wc_get_order(majesticsupport::$_data[0]->wcorderid);
                                $order_productid = majesticsupport::$_data[0]->wcproductid;
                                if($order){
                                ?>
                                <div class="mjtc-sprt-det-cnt mjtc-sprt-det-woocom">
                                    <div class="mjtc-sprt-det-hdg">
                                        <div class="mjtc-sprt-det-hdg-txt">
                                            <?php echo esc_html(__("Woocommerce Order",'majestic-support')); ?>
                                        </div>
                                    </div>
                                    <div class="mjtc-sprt-wc-order-box">
                                        <div class="mjtc-sprt-wc-order-item">
                                            <div class="mjtc-sprt-wc-order-item-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($field_array['wcorderid'])). ' : '; ?></div>
                                            <div class="mjtc-sprt-wc-order-item-value">
                                                <a title="<?php echo esc_attr(__('Order','majestic-support')). ' : '; ?>" href="<?php echo esc_url($order->get_edit_order_url()); ?>">
                                                    #<?php echo esc_html($order->get_id()); ?>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="mjtc-sprt-wc-order-item">
                                            <div class="mjtc-sprt-wc-order-item-title"><?php echo esc_html(__("Status",'majestic-support')). ' : '; ?></div>
                                            <div class="mjtc-sprt-wc-order-item-value"><?php echo esc_html(wc_get_order_status_name($order->get_status())); ?></div>
                                        </div>
                                        <?php
                                        if($order_productid){
												//$item = new WC_Order_Item_Product($order_productid); this line generate error if product changed
											$items = $order->get_items();
											foreach ( $items as $item ) { // get the user select product
												if($item->get_product_id() == $order_productid){
													$product_name = $item->get_name();
												}
											}
											if($product_name == ""){ // product not matched, product changed in order
												if(count($items) == 1){ // order have one product
													foreach ( $items as $item ) {
														$product_name = $item->get_name();
													}
												}												
											}
											if($product_name != ""){
													?>
													<div class="mjtc-sprt-wc-order-item">
														<div class="mjtc-sprt-wc-order-item-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($field_array['wcproductid'])). ' : '; ?></div>
														<div class="mjtc-sprt-wc-order-item-value"><?php echo esc_html($product_name); ?></div>
													</div>
													<?php
													
                                            }
                                        }?>
                                        <div class="mjtc-sprt-wc-order-item">
                                            <div class="mjtc-sprt-wc-order-item-title"><?php echo esc_html(__("Created",'majestic-support')). ' : '; ?></div>
                                            <div class="mjtc-sprt-wc-order-item-value"><?php echo esc_html($order->get_date_created()->date_i18n(wc_date_format())); ?></div>
                                        </div>
                                        <?php do_action('ms_woocommerce_order_detail_admin', $order, $order_productid); ?>
                                    </div>
                                </div>
								<?php
								}else{ ?>
									<div class="mjtc-sprt-det-cnt mjtc-sprt-det-woocom">
										<div class="mjtc-sprt-wc-order-box">
										<?php
										do_action('ms_woocommerce_order_detail_admin', $order, $order_productid,majesticsupport::$_data[0]->uid);
										?>
										</div>
									</div>
								<?php
								}
                            }
                        ?>
                        <!-- ticket detail easy digital downloads -->
                        <?php
                            if( class_exists('Easy_Digital_Downloads') && in_array('easydigitaldownloads', majesticsupport::$_active_addons)){
                                $orderid = majesticsupport::$_data[0]->eddorderid;
                                $order_product = majesticsupport::$_data[0]->eddproductid;
                                $order_license = majesticsupport::$_data[0]->eddlicensekey;
                                if($orderid != ''){ ?>
                                    <div class="mjtc-sprt-det-cnt mjtc-sprt-det-edd">
                                        <div class="mjtc-sprt-det-hdg">
                                            <div class="mjtc-sprt-det-hdg-txt">
                                                <?php echo esc_html(__("Easy Digital Downloads",'majestic-support')); ?>
                                            </div>
                                        </div>
                                        <div class="mjtc-sprt-wc-order-box">
                                            <div class="mjtc-sprt-wc-order-item">
                                                <div class="mjtc-sprt-wc-order-item-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($field_array['eddorderid'])); ?>:</div>
                                                <div class="mjtc-sprt-wc-order-item-value">#<?php echo esc_html($orderid); ?></div>
                                            </div>

                                            <div class="mjtc-sprt-wc-order-item">
                                                <div class="mjtc-sprt-wc-order-item-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($field_array['eddproductid'])); ?>:</div>
                                                <div class="mjtc-sprt-wc-order-item-value"><?php
                                                    if(is_numeric($order_product)){
                                                        $download = new EDD_Download($order_product);
                                                        echo wp_kses_post($download->post_title);
                                                    }else{
                                                        echo esc_html('-----------');
                                                    }?>
                                                </div>
                                            </div>
                                            <?php if(class_exists('EDD_Software_Licensing')){ ?>
                                                <div class="mjtc-sprt-wc-order-item">
                                                    <div class="mjtc-sprt-wc-order-item-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($field_array['eddlicensekey'])); ?>:</div>
                                                    <div class="mjtc-sprt-wc-order-item-value"><?php
                                                        if($order_license != ''){
                                                            $license = EDD_Software_Licensing::instance();
                                                            $licenseid = $license->get_license_by_key($order_license);
                                                            $result = $license->get_license_status($licenseid);
                                                            if($result == 'expired'){
                                                                $result_color = 'red';
                                                            }elseif($result == 'inactive'){
                                                                $result_color = 'orange';
                                                            }else{
                                                                $result_color = 'green';
                                                            }
                                                            echo wp_kses($order_license.'&nbsp;&nbsp;(<span style="color:'.esc_attr($result_color).';font-weight:bold;text-transform:uppercase;padding:0 3px;">'.esc_html($result).'</span>)', MJTC_ALLOWED_TAGS);
                                                        }
                                                         ?>
                                                    </div>
                                                </div>
                                            <?php }?>
                                        </div>
                                    </div><?php
                                }
                            }
                        ?>
                        <!-- ticket detail envato validation -->
                        <?php
                        
                        if(in_array('envatovalidation', majesticsupport::$_active_addons) && !empty(majesticsupport::$_data[0]->envatodata)){
                            $envlicense = majesticsupport::$_data[0]->envatodata;
                            if(!empty($envlicense)){ ?>
                                <div class="mjtc-sprt-det-cnt mjtc-sprt-det-env">
                                    <div class="mjtc-sprt-det-hdg">
                                        <div class="mjtc-sprt-det-hdg-txt">
                                            <?php echo esc_html(__("Envato License",'majestic-support')); ?>
                                        </div>
                                    </div>
                                    <div class="mjtc-sprt-wc-order-box">
                                        <?php if(!empty($envlicense['itemname']) && !empty($envlicense['itemid'])){ ?>
                                        <div class="mjtc-sprt-wc-order-item">
                                            <div class="mjtc-sprt-wc-order-item-title"><?php echo esc_html(__("Item",'majestic-support')); ?>:</div>
                                            <div class="mjtc-sprt-wc-order-item-value">
                                                <?php echo esc_html($envlicense['itemname']).' (#'.esc_html($envlicense['itemid']).')'; ?>
                                            </div>
                                        </div>
                                        <?php } ?>
                                        <?php if(!empty($envlicense['buyer'])){ ?>
                                        <div class="mjtc-sprt-wc-order-item">
                                            <div class="mjtc-sprt-wc-order-item-title"><?php echo esc_html(__("Buyer",'majestic-support')); ?>:</div>
                                            <div class="mjtc-sprt-wc-order-item-value">
                                                <?php echo esc_html($envlicense['buyer']); ?>
                                            </div>
                                        </div>
                                        <?php } ?>
                                        <?php if(!empty($envlicense['licensetype'])){ ?>
                                        <div class="mjtc-sprt-wc-order-item">
                                            <div class="mjtc-sprt-wc-order-item-title"><?php echo esc_html(__("License Type",'majestic-support')); ?>:</div>
                                            <div class="mjtc-sprt-wc-order-item-value">
                                                <?php echo esc_html($envlicense['licensetype']); ?>
                                            </div>
                                        </div>
                                        <?php } ?>
                                        <?php if(!empty($envlicense['license'])){ ?>
                                        <div class="mjtc-sprt-wc-order-item">
                                            <div class="mjtc-sprt-wc-order-item-title"><?php echo esc_html(__("License",'majestic-support')); ?>:</div>
                                            <div class="mjtc-sprt-wc-order-item-value">
                                                <?php echo esc_html($envlicense['license']); ?>
                                            </div>
                                        </div>
                                        <?php } ?>
                                        <?php if(!empty($envlicense['purchasedate'])){ ?>
                                        <div class="mjtc-sprt-wc-order-item">
                                            <div class="mjtc-sprt-wc-order-item-title"><?php echo esc_html(__("Purchase Date",'majestic-support')); ?>:</div>
                                            <div class="mjtc-sprt-wc-order-item-value">
                                                <?php echo esc_html(date_i18n("F d, Y, H:i:s", MJTC_majesticsupportphplib::MJTC_strtotime($envlicense['purchasedate']))); ?>
                                            </div>
                                        </div>
                                        <?php } ?>
                                        <?php if(!empty($envlicense['supporteduntil'])){ ?>
                                        <div class="mjtc-sprt-wc-order-item">
                                            <div class="mjtc-sprt-wc-order-item-title"><?php echo esc_html(__("Supported Until",'majestic-support')); ?>:</div>
                                            <div class="mjtc-sprt-wc-order-item-value">
                                                <?php echo esc_html(date_i18n("F d, Y", MJTC_majesticsupportphplib::MJTC_strtotime($envlicense['supporteduntil']))); ?>
                                            </div>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div><?php
                            }
                        }
                        ?>
                        <!-- ticket detail paid support -->
                        <?php
                        if(in_array('paidsupport', majesticsupport::$_active_addons) && class_exists('WooCommerce')){
                            $linktickettoorder = true;
                            if(majesticsupport::$_data[0]->paidsupportitemid > 0){
                                $paidsupport = MJTC_includer::MJTC_getModel('paidsupport')->getPaidSupportDetails(majesticsupport::$_data[0]->paidsupportitemid);
                                if($paidsupport){
                                    $linktickettoorder = false;
                                    $nonpreminumsupport = in_array(majesticsupport::$_data[0]->id,$paidsupport['ignoreticketids']) ? 1 : 0;
                                    ?>
                                    <div class="mjtc-sprt-det-cnt mjtc-sprt-det-pdsprt">
                                        <div class="mjtc-sprt-det-hdg">
                                            <div class="mjtc-sprt-det-hdg-txt">
                                                <?php echo esc_html(__("Paid Support Details",'majestic-support')); ?>
                                            </div>
                                        </div>
                                        <?php if(!$nonpreminumsupport){ ?>
                                        <div class="mjtc-sprt-wc-order-box">
                                            <div class="mjtc-sprt-wc-order-item">
                                                <div class="mjtc-sprt-wc-order-item-title"><?php echo esc_html(__("Order",'majestic-support')); ?>:</div>
                                                <div class="mjtc-sprt-wc-order-item-value">#<?php echo esc_html($paidsupport['orderid']); ?></div>
                                            </div>
                                            <div class="mjtc-sprt-wc-order-item">
                                                <div class="mjtc-sprt-wc-order-item-title"><?php echo esc_html(__("Product Name",'majestic-support')); ?>:</div>
                                                <div class="mjtc-sprt-wc-order-item-value"><?php echo esc_html($paidsupport['itemname']); ?></div>
                                            </div>
                                            <div class="mjtc-sprt-wc-order-item">
                                                <div class="mjtc-sprt-wc-order-item-title"><?php echo esc_html(__("Total Tickets",'majestic-support')); ?>:</div>
                                                <div class="mjtc-sprt-wc-order-item-value">
                                                    <?php
                                                    if ($paidsupport['totalticket']==-1) {
                                                        echo esc_html(__("Unlimited",'majestic-support'));
                                                    } else {
                                                        echo esc_html($paidsupport['totalticket']);
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="mjtc-sprt-wc-order-item">
                                                <div class="mjtc-sprt-wc-order-item-title"><?php echo esc_html(__("Remaining Tickets",'majestic-support')); ?>:</div>
                                                <div class="mjtc-sprt-wc-order-item-value">
                                                    <?php
                                                    if ($paidsupport['totalticket']==-1) {
                                                        echo esc_html(__("Unlimited",'majestic-support'));
                                                    } else {
                                                        echo esc_html($paidsupport['remainingticket']);
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                            <?php if(isset($paidsupport['subscriptionid'])){ ?>
                                            <div class="mjtc-sprt-wc-order-item">
                                                <div class="mjtc-sprt-wc-order-item-title"><?php echo esc_html(__("Subscription",'majestic-support')); ?>:</div>
                                                <div class="mjtc-sprt-wc-order-item-value">#<?php echo esc_html($paidsupport['subscriptionid']); ?></div>
                                            </div>
                                            <?php } ?>
                                            <?php if(isset($paidsupport['subscriptionstartdate'])){ ?>
                                            <div class="mjtc-sprt-wc-order-item">
                                                <div class="mjtc-sprt-wc-order-item-title"><?php echo esc_html(__("Subscribed On",'majestic-support')); ?>:</div>
                                                <div class="mjtc-sprt-wc-order-item-value"><?php echo esc_html(date_i18n("F d, Y, H:i:s", MJTC_majesticsupportphplib::MJTC_strtotime($paidsupport['subscriptionstartdate']))); ?></div>
                                            </div>
                                            <?php } ?>
                                            <?php if(isset($paidsupport['expiry'])){ ?>
                                            <div class="mjtc-sprt-wc-order-item">
                                                <div class="mjtc-sprt-wc-order-item-title"><?php echo esc_html(__("Support Expiry",'majestic-support')); ?>:</div>
                                                <div class="mjtc-sprt-wc-order-item-value">
                                                    <?php
                                                    if ($paidsupport['expiry']) {
                                                        echo esc_html(date_i18n("F d, Y", MJTC_majesticsupportphplib::MJTC_strtotime($paidsupport['expiry'])));
                                                    } else {
                                                        echo esc_html(__("No expiration",'majestic-support'));
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                            <?php } ?>
                                        </div>
                                        <?php } ?>

                                        <div class="mjtc-sprt-wc-order-box">
                                            <div class="mjtc-sprt-wc-order-item">
                                                <label>
                                                    <input type="checkbox" id="nonpreminumsupport" <?php if($nonpreminumsupport) echo esc_html('checked'); ?>>
                                                    <b><?php echo esc_html(__("Non-premium support",'majestic-support')); ?></b>
                                                </label>
                                                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('paidsupportitemid',majesticsupport::$_data[0]->paidsupportitemid), MJTC_ALLOWED_TAGS) ?>
                                                <div>
                                                    <small><i><?php echo esc_html(__("Check this box if this ticket should NOT apply against the paid support",'majestic-support')); ?></i></small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                }
                            }
                            if($linktickettoorder){
                                $paidsupportitems = MJTC_includer::MJTC_getModel('paidsupport')->getPaidSupportList(majesticsupport::$_data[0]->uid);
                                $paidsupportlist = array();
                                foreach($paidsupportitems as $row){
                                    $paidsupportlist[] = (object) array(
                                        'id' => $row->itemid,
                                        'text' => esc_html(__("Order",'majestic-support')).' #'.esc_html($row->orderid).', '.esc_html($row->itemname).', '. esc_html(__("Remaining",'majestic-support')).':'.esc_html($row->remaining).' '. esc_html(__("Out of",'majestic-support')).':'.esc_html($row->total),
                                    );
                                }
                                ?>
                                <div class="mjtc-sprt-det-cnt">
                                    <div class="mjtc-sprt-det-hdg">
                                        <div class="mjtc-sprt-det-hdg-txt">
                                            <?php echo esc_html(__("Link ticket to paid support",'majestic-support')); ?>
                                        </div>
                                    </div>
                                    <div class="mjtc-sprt-wc-order-box">
                                        <div class="mjtc-sprt-wc-order-item">
                                            <?php echo wp_kses(MJTC_formfield::MJTC_select('paidsupportitemid',$paidsupportlist,null,esc_html(__("Select",'majestic-support'))), MJTC_ALLOWED_TAGS); ?>
                                            <button type="button" class="button" id="paidsupportlinkticketbtn"><?php echo esc_html(__("Link",'majestic-support')); ?></button>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                        }
                        ?>
                        <?php apply_filters('mjtc_support_ticket_frontend_details_right_last', majesticsupport::$_data[0]->id); ?>
                    </div>
                </div>

                <?php
            } else {
                MJTC_layout::MJTC_getNoRecordFound();
            }
            ?>
        </div>
    </div>
</div>
