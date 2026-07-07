<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_replyModel {

    function getReplies($MJTC_id) {
        if (!is_numeric($MJTC_id))
            return false;
        // Data

        do_action('MJTC_reset_addon_query');
        do_action('MJTC_aadon_getreplies');// to prepare any addon based query (action is defined in two addons)
        $MJTC_ordering = majesticsupport::$_config['ticket_replies_ordering'];
        $MJTC_ordering = strtoupper(trim($MJTC_ordering)); // Normalize input

        // Allow only ASC or DESC
        if (!in_array($MJTC_ordering, ['ASC', 'DESC'])) {
            $MJTC_ordering = 'ASC'; // default fallback
        }
        $MJTC_query = "SELECT replies.*,replies.id AS replyid,user.user_email AS useremail,viewer.display_name AS viewername,tickets.id,tickets.uid AS ticketsuid ".majesticsupport::$_addon_query['select']."
                    FROM `" . majesticsupport::$_db->prefix . "mjtc_support_replies` AS replies
                    JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS tickets ON  replies.ticketid = tickets.id
                    LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_users` AS user ON  replies.uid = user.id
                    LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_users` AS viewer ON  replies.viewed_by = viewer.id
                    ".majesticsupport::$_addon_query['join']."
                    WHERE tickets.id = " . absint($MJTC_id) . " ORDER BY replies.id " . $MJTC_ordering;
        majesticsupport::$_data[4] = majesticsupport::$_db->get_results($MJTC_query);
        do_action('MJTC_reset_addon_query');
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        $MJTC_attachmentmodel = MJTC_includer::MJTC_getModel('attachment');
        foreach (majesticsupport::$_data[4] AS $MJTC_reply) {
            $MJTC_reply->attachments = $MJTC_attachmentmodel->getAttachmentForReply($MJTC_reply->id, $MJTC_reply->replyid);
            $MJTC_current_user = MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid();
            $MJTC_viewed_by = isset($MJTC_current_user) ? $MJTC_current_user : -1; //-1 for handle visitor case
            $MJTC_update_required = false; // Flag to determine if the update is needed

            // Check if the reply has not been viewed
            if (empty($MJTC_reply->viewed_by) && empty($MJTC_reply->mergemessage)) {

                // If the current user is an admin
                if (is_admin()) {
                    // Admin viewing someone else's reply and it's not staff
                    if ($MJTC_reply->uid != $MJTC_current_user && empty($MJTC_reply->staffid)) {
                        $MJTC_update_required = true; // Mark update as required
                    }
                } else { // If the current user is not an admin

                    // Check if the 'agent' addon is active and the user is staff
                    if (in_array('agent', majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) {
                        // Check if the ticket owner is the reply owner
                        if ($MJTC_reply->ticketsuid == $MJTC_reply->uid) {
                            $MJTC_update_required = true; // Mark update as required
                        }
                    } else { // If the user is not staff or the agent addon is inactive
                        // Check if the ticket owner is not the reply owner
                        if ($MJTC_reply->ticketsuid != $MJTC_reply->uid) {
                            $MJTC_update_required = true; // Mark update as required
                        }
                    }
                }
            }
            // Execute the query if an update is required
            if ($MJTC_update_required) {
                $MJTC_query = majesticsupport::$_db->prepare("UPDATE `" . majesticsupport::$_db->prefix . "mjtc_support_replies` SET viewed_by = %d, viewed_on = %s WHERE id = %d", absint($MJTC_viewed_by), date_i18n('Y-m-d H:i:s'), absint($MJTC_reply->replyid));
                majesticsupport::$_db->query($MJTC_query);
            }
        }
        return;
    }

    function getTicketNameForReplies() {
        $MJTC_query = "SELECT id, ticketid AS text FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets`";
        $MJTC_list = majesticsupport::$_db->get_results($MJTC_query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        return $MJTC_list;
    }

    function getRepliesForForm($MJTC_id) {
        if ($MJTC_id) {
            if (!is_numeric($MJTC_id))
                return false;
            $MJTC_query = "SELECT replies.*,tickets.id
                        FROM `" . majesticsupport::$_db->prefix . "mjtc_support_replies` AS replies
                        JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS tickets ON  replies.ticketid = tickets.id
                        WHERE replies.id = " . absint($MJTC_id);
            majesticsupport::$_data[0] = majesticsupport::$_db->get_row($MJTC_query);
            if (majesticsupport::$_db->last_error != null) {
                MJTC_includer::MJTC_getModel('systemerror')->addSystemError(); // if there is an error add it to system errorrs
            }
        }
        return;
    }

    function storeReplies($MJTC_data) {
        $MJTC_nonce_id = $MJTC_data['ticketid'];
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'save-reply-'.$MJTC_nonce_id) ) {
            die( 'Security check Failed' );
        }
        $MJTC_checkduplicatereplies = $this->checkIsReplyDuplicate($MJTC_data);
        if(!$MJTC_checkduplicatereplies){
            return false;
        }
        //validate reply for break down
        $MJTC_ticketid   = $MJTC_data['ticketrandomid'];
        $MJTC_internalid   = $MJTC_data['internalid'];
        $MJTC_hash       = $MJTC_data['hash'];
        $MJTC_query = majesticsupport::$_db->prepare("SELECT id FROM `".majesticsupport::$_db->prefix."mjtc_support_tickets` WHERE ticketid=%s
        AND IF(`hash` is NULL,true,`hash`=%s) ", $MJTC_ticketid, $MJTC_hash);
        $MJTC_id = majesticsupport::$_db->get_var($MJTC_query);
        if($MJTC_id != $MJTC_data['ticketid']){
            return;
        }//end

        $MJTC_ticketviaemailstaffid = 0;
        // set in Email Piping
        if(isset($MJTC_data['staffid'])){
            $MJTC_ticketviaemailstaffid = $MJTC_data['staffid'];
            unset($MJTC_data['staffid']);
        }
        if ( in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) {
            $MJTC_allowed = MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForTask('Reply Ticket');
            if ($MJTC_allowed != true) {
                MJTC_message::MJTC_setMessage(esc_html(__('You are not allowed', 'majestic-support')), 'error');
                return;
            }
        } else if (!MJTC_includer::MJTC_getModel('ticket')->validateTicketAction($MJTC_id, $MJTC_internalid)) {
            MJTC_message::MJTC_setMessage(esc_html(__('You are not allowed','majestic-support')), 'error');
            return false;
        }
        // check whether ticket is closed or not incase of ticket viw email
        if(isset($MJTC_data['ticketviaemail']) && $MJTC_data['ticketviaemail'] == 1){
            if(majesticsupport::$_config['reply_to_closed_ticket'] != 1){
                $MJTC_closed = MJTC_includer::MJTC_getModel('ticket')->checkActionStatusSame($MJTC_data['ticketid'],array('action' => 'closeticket'));
                if($MJTC_closed == false){
                    MJTC_includer::MJTC_getModel('email')->sendMail(1, 14, $MJTC_data['ticketid']); // Mailfor, Reply Ticket
                    return;
                }
                // check this ticket is not assign to any one
                if( MJTC_includer::MJTC_getModel('ticket')->isTicketAssigned($MJTC_data['ticketid']) == false){
                    // if not assigned then assign to me
                    $MJTC_data['assigntome'] = 1;
                }
            }
        }
        $MJTC_sendEmail = true;
        $MJTC_staffid = 0;
        if (!MJTC_includer::MJTC_getObjectClass('user')->MJTC_isguest()) {
            //$MJTC_current_user = get_userdata(MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid());
            $MJTC_currentUserName = MJTC_includer::MJTC_getObjectClass('user')->MJTC_fullname();
            if( in_array('agent',majesticsupport::$_active_addons) ){
                //$MJTC_staffid = MJTC_includer::MJTC_getModel('agent')->getStaffId($MJTC_current_user->ID);
				$MJTC_staffid = MJTC_includer::MJTC_getModel('agent')->getStaffId(MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid());
            }
        } else {
            $MJTC_currentUserName = '';
        }

        if($MJTC_staffid == 0 && $MJTC_ticketviaemailstaffid != 0){
            $MJTC_staffid = $MJTC_ticketviaemailstaffid;
        }

        //check the assign to me on reply
        if (isset($MJTC_data['assigntome']) && $MJTC_data['assigntome'] == 1) {
            MJTC_includer::MJTC_getModel('ticket')->ticketAssignToMe($MJTC_data['ticketid'], $MJTC_staffid);
        }
        if(isset($MJTC_data['ticketviaemail'])){
            if($MJTC_data['ticketviaemail'] == 1)
                $MJTC_currentUserName = $MJTC_data['name'];
        }
        $MJTC_data['id'] = isset($MJTC_data['id']) ? $MJTC_data['id'] : '';
        $MJTC_data['status'] = isset($MJTC_data['status']) ? $MJTC_data['status'] : '';
        $MJTC_data['closeonreply'] = isset($MJTC_data['closeonreply']) ? $MJTC_data['closeonreply'] : '';
        $MJTC_data['ticketviaemail'] = isset($MJTC_data['ticketviaemail']) ? $MJTC_data['ticketviaemail'] : 0;
        $tempmessage = $MJTC_data['mjsupport_message'];
        $MJTC_data = majesticsupport::MJTC_sanitizeData($MJTC_data); // MJTC_sanitizeData() function uses wordpress santize functions
        if(isset($MJTC_data['ticketviaemail']) && $MJTC_data['ticketviaemail'] == 1){
            $MJTC_data['message'] = $tempmessage;
        }else{
            $MJTC_data['message'] = MJTC_includer::MJTC_getModel('majesticsupport')->getSanitizedEditorData($_POST['mjsupport_message']);
        }
        if(empty($MJTC_data['message'])){
            MJTC_message::MJTC_setMessage(esc_html(__('Message field cannot be empty', 'majestic-support')), 'error');
            return false;
        }
        //check signature
        if (!isset($MJTC_data['nonesignature'])) {
            if (isset($MJTC_data['ownsignature']) && $MJTC_data['ownsignature'] == 1) {
                if (is_admin()) {
                    $MJTC_data['message'] .= '<br/>' . get_user_meta(MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid(), 'ms_signature', true);
                } elseif(in_array('agent',majesticsupport::$_active_addons)) {
                    $MJTC_data['message'] .= '<br/>' . MJTC_includer::MJTC_getModel('agent')->getMySignature();
                }
            }
            if (isset($MJTC_data['departmentsignature']) && $MJTC_data['departmentsignature'] == 1) {
                $MJTC_data['message'] .= '<br/>' . MJTC_includer::MJTC_getModel('department')->getSignatureByID($MJTC_data['departmentid']);
            }
        }

        $MJTC_data['created'] = date_i18n('Y-m-d H:i:s');
        $MJTC_data['name'] = $MJTC_currentUserName;
        $MJTC_data['staffid'] = $MJTC_staffid;

        $MJTC_row = MJTC_includer::MJTC_getTable('replies');

        $MJTC_data = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($MJTC_data);// remove slashes with quotes.
        $MJTC_error = 0;
        if (!$MJTC_row->bind($MJTC_data)) {
            $MJTC_error = 1;
        }
        if (!$MJTC_row->store()) {
            $MJTC_error = 1;
        }

        if ($MJTC_error == 0) {
            $MJTC_replyid = $MJTC_row->id;
            // smart reply store
            if (isset($MJTC_data['add_smartreply']) && $MJTC_data['add_smartreply'] == 1) {
                $MJTC_samrtreplyTitle = MJTC_includer::MJTC_getModel('ticket')->getTicketSubjectById($MJTC_data['ticketid']);
                $MJTC_samrtreply['id'] = '';
                $MJTC_samrtreply['title'] = $MJTC_samrtreplyTitle;
                $MJTC_samrtreply['ticketsubjects'][0] = $MJTC_samrtreplyTitle;
                $MJTC_samrtreply['reply'] = $MJTC_data['message'];
                MJTC_includer::MJTC_getModel('smartreply')->storeSmartReply($MJTC_samrtreply);
            }
            //tickets attachments store
            $MJTC_data['replyattachmentid'] = $MJTC_replyid;
            MJTC_includer::MJTC_getModel('attachment')->storeAttachments($MJTC_data);
            //reply stored change action
            if (is_admin()){
                MJTC_includer::MJTC_getModel('ticket')->setStatus(4, $MJTC_data['ticketid']); // 4 -> waiting for customer reply
                if(in_array('timetracking', majesticsupport::$_active_addons)){
                    MJTC_includer::MJTC_getModel('timetracking')->storeTimeTaken($MJTC_data,$MJTC_replyid,1);// to store time for reply 1 is to identfy that current record is reply
                }
            }else {
                if ( in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()){
                    MJTC_includer::MJTC_getModel('ticket')->setStatus(4, $MJTC_data['ticketid']); // 4 -> waiting for customer reply
                    $MJTC_data['staffid'] = $MJTC_staffid;
                    if(in_array('timetracking', majesticsupport::$_active_addons)){
                        MJTC_includer::MJTC_getModel('timetracking')->storeTimeTaken($MJTC_data,$MJTC_replyid,1);// to store time for reply 1 is to identfy that current record is reply
                    }

                }else{
                    MJTC_includer::MJTC_getModel('ticket')->setStatus(2, $MJTC_data['ticketid']); // 2 -> waiting for admin/staff reply
                }
            }
            MJTC_includer::MJTC_getModel('ticket')->updateLastReply($MJTC_data['ticketid']);
            MJTC_message::MJTC_setMessage(esc_html(__('Reply posted', 'majestic-support')), 'updated');
            $MJTC_messagetype = esc_html(__('Successfully', 'majestic-support'));

            // Reply notification
            if(in_array('notification', majesticsupport::$_active_addons)){
                // Get Ticket Staffid
                $MJTC_ticketstaffid = MJTC_includer::MJTC_getModel('ticket')->getStaffIdById($MJTC_data['ticketid']);
                $MJTC_ticketuid = MJTC_includer::MJTC_getModel('ticket')->getUIdById($MJTC_data['ticketid']);

                // to admin
                $MJTC_dataarray = array();
                $MJTC_dataarray['title'] = esc_html(__("Reply posted on ticket",'majestic-support'));
                $MJTC_dataarray['body'] =  MJTC_includer::MJTC_getModel('ticket')->getTicketSubjectById($MJTC_data['ticketid']);

                // To admin
                $MJTC_devicetoken = MJTC_includer::MJTC_getModel('notification')->checkSubscriptionForAdmin();
                if($MJTC_devicetoken){
                    $MJTC_dataarray['link'] = admin_url("admin.php?page=majesticsupport_ticket&mjslay=ticketdetail&majesticsupportid=".esc_attr($MJTC_data['ticketid']));
                    $MJTC_dataarray['devicetoken'] = $MJTC_devicetoken;
                    $MJTC_value = majesticsupport::$_config[MJTC_majesticsupportphplib::MJTC_md5(MSTN)];
                    if($MJTC_value != ''){
                      do_action('MJTC_send_push_notification',$MJTC_dataarray);
                    }else{
                      do_action('MJTC_resetnotificationvalues');
                    }
                }

                $MJTC_dataarray['link'] = majesticsupport::makeUrl(array('mjsmod'=>'ticket', 'mjslay'=>'ticketdetail', "majesticsupportid"=>$MJTC_data['ticketid'],'mspageid'=>majesticsupport::getPageid()));
                if($MJTC_ticketuid != 0 && ($MJTC_ticketuid != MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid())){
                    $MJTC_devicetoken = MJTC_includer::MJTC_getModel('notification')->getUserDeviceToken($MJTC_ticketuid);
                    $MJTC_dataarray['devicetoken'] = $MJTC_devicetoken;
                    if($MJTC_devicetoken != '' && !empty($MJTC_devicetoken)){
                        $MJTC_value = majesticsupport::$_config[MJTC_majesticsupportphplib::MJTC_md5(MSTN)];
                        if($MJTC_value != ''){
                          do_action('MJTC_send_push_notification',$MJTC_dataarray);
                        }else{
                          do_action('MJTC_resetnotificationvalues');
                        }
                    }
                }

                if($MJTC_ticketstaffid != 0 && ($MJTC_ticketuid != $MJTC_staffid)){
                    $MJTC_devicetoken = MJTC_includer::MJTC_getModel('notification')->getUserDeviceToken($MJTC_ticketstaffid);
                    $MJTC_dataarray['devicetoken'] = $MJTC_devicetoken;
                    if($MJTC_devicetoken != '' && !empty($MJTC_devicetoken)){
                        $MJTC_value = majesticsupport::$_config[MJTC_majesticsupportphplib::MJTC_md5(MSTN)];
                        if($MJTC_value != ''){
                          do_action('MJTC_send_push_notification',$MJTC_dataarray);
                        }else{
                          do_action('MJTC_resetnotificationvalues');
                        }
                    }
                }
                if($MJTC_ticketuid == 0){ // for visitor
                    $MJTC_tokenarray['emailaddress'] = MJTC_includer::MJTC_getModel('ticket')->getTicketEmailById($MJTC_data['ticketid']);
                    $MJTC_tokenarray['trackingid'] = MJTC_includer::MJTC_getModel('ticket')->getTrackingIdById($MJTC_data['ticketid']);
                    $MJTC_tokenarray['sitelink']=MJTC_includer::MJTC_getModel('majesticsupport')->getEncriptedSiteLink();
                    $MJTC_token = wp_json_encode($MJTC_tokenarray);
                    include_once MJTC_PLUGIN_PATH . 'includes/encoder.php';
                    $MJTC_encoder = new MJTC_encoder();
                    $MJTC_encryptedtext = $MJTC_encoder->MJTC_encrypt($MJTC_token);
                    $MJTC_dataarray['link'] = majesticsupport::makeUrl(array('mjsmod'=>'ticket' ,'task'=>'showticketstatus','action'=>'mstask','token'=>$MJTC_encryptedtext,'mspageid'=>majesticsupport::getPageid()));
                    $MJTC_notificationid = MJTC_includer::MJTC_getModel('ticket')->getNotificationIdById($MJTC_data['ticketid']);
                    $MJTC_devicetoken = MJTC_includer::MJTC_getModel('notification')->getUserDeviceToken($MJTC_notificationid,0);
                    if($MJTC_devicetoken != '' && !empty($MJTC_devicetoken)){
                        $MJTC_value = majesticsupport::$_config[MJTC_majesticsupportphplib::MJTC_md5(MSTN)];
                        if($MJTC_value != ''){
                          do_action('MJTC_send_push_notification',$MJTC_dataarray);
                        }else{
                          do_action('MJTC_resetnotificationvalues');
                        }
                    }
                }
            }
            // End notification
        }else {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError(); // if there is an error add it to system errorrs
            MJTC_message::MJTC_setMessage(esc_html(__('Reply posted', 'majestic-support')), 'error');
            $MJTC_messagetype = esc_html(__('Error', 'majestic-support'));
            $MJTC_sendEmail = false;
        }

        /* for activity log */
        $MJTC_ticketid = $MJTC_data['ticketid']; // get the ticket id
        $MJTC_current_user = MJTC_includer::MJTC_getObjectClass('user')->MJTC_getMSCurrentUser(); // to get current user name
        $MJTC_currentUserName = isset($MJTC_current_user->display_name) ? $MJTC_current_user->display_name : esc_html(__('Guest', 'majestic-support'));
        $MJTC_eventtype = 'REPLIED_TICKET';
        $MJTC_message = esc_html(__('Ticket is replied by', 'majestic-support')) . " ( " . esc_html($MJTC_currentUserName) . " ) ";
        if(in_array('tickethistory', majesticsupport::$_active_addons)){
            MJTC_includer::MJTC_getModel('tickethistory')->addActivityLog($MJTC_ticketid, 1, $MJTC_eventtype, $MJTC_message, $MJTC_messagetype);
        }

        // Send Emails
        if ($MJTC_sendEmail == true) {
            if (is_admin()) {
                MJTC_includer::MJTC_getModel('email')->sendMail(1, 4, $MJTC_ticketid); // Mailfor, Reply Ticket
            } else {
                MJTC_includer::MJTC_getModel('email')->sendMail(1, 5, $MJTC_ticketid); // Mailfor, Reply Ticket
            }
            $MJTC_ticketreplyobject = majesticsupport::$_db->get_row(majesticsupport::$_db->prepare("SELECT * FROM `" . majesticsupport::$_db->prefix . "mjtc_support_replies` WHERE id = %d", absint($MJTC_replyid)));
            do_action('MJTC_ticketreply', $MJTC_ticketreplyobject);
        }
        // if Close on reply is cheked
        if ($MJTC_data['closeonreply'] == 1) {
            MJTC_includer::MJTC_getModel('ticket')->closeTicket($MJTC_ticketid, $MJTC_internalid);
        }

        return;
    }

    function checkIsReplyDuplicate($MJTC_data){
        if(empty($MJTC_data)) return false;
        
        $MJTC_curdate = date_i18n('Y-m-d H:i:s');
        $MJTC_inquery = '';
        if (isset($MJTC_data['ticketviaemail']) && $MJTC_data['ticketviaemail'] == 1) {
            $MJTC_inquery .= " AND ticketviaemail = 1";
        }
        $MJTC_query = majesticsupport::$_db->prepare("SELECT created FROM `" . majesticsupport::$_db->prefix . "mjtc_support_replies` WHERE ticketid = %d AND uid = %d ORDER BY created DESC LIMIT 1", absint($MJTC_data['ticketid']), absint($MJTC_data['uid']));
        $MJTC_query .= $MJTC_inquery;
        $MJTC_datetime = majesticsupport::$_db->get_var($MJTC_query);
        if($MJTC_datetime){
            $MJTC_diff = MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_curdate) - MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_datetime);
            if($MJTC_diff <= 7){
                return false;
            }
        }
        return true;
    }

    function getLastReply($MJTC_ticketid) {
        if (!is_numeric($MJTC_ticketid))
            return false;
        $MJTC_query = majesticsupport::$_db->prepare("SELECT created FROM `" . majesticsupport::$_db->prefix . "mjtc_support_replies` WHERE ticketid = %d ORDER BY created DESC", absint($MJTC_ticketid));
        $MJTC_lastreply = majesticsupport::$_db->get_var($MJTC_query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError(); // if there is an error add it to system errorrs
        }
        return $MJTC_lastreply;
    }

    function removeTicketReplies($MJTC_ticketid) {
        if(!is_numeric($MJTC_ticketid)) return false;
        majesticsupport::$_db->delete(majesticsupport::$_db->prefix . 'mjtc_support_replies', array('ticketid' => $MJTC_ticketid));
        return;
    }

    function getReplyDataByID() {
        $MJTC_replyid = MJTC_request::MJTC_getVar('val');
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'get-reply-data-by-id-'.$MJTC_replyid) ) {
            die( 'Security check Failed' );
        }
        if(!is_numeric($MJTC_replyid)) return false;
        $MJTC_query = "SELECT reply.id AS replyid, reply.message AS message
                    FROM `" . majesticsupport::$_db->prefix . "mjtc_support_replies` AS reply
                    WHERE reply.id =  " . absint($MJTC_replyid) ;
        $MJTC_lastreply = majesticsupport::$_db->get_row($MJTC_query);
        $MJTC_lastreply->message = MJTC_majesticsupportphplib::MJTC_htmlentities(($MJTC_lastreply->message));

        return wp_json_encode($MJTC_lastreply);
    }

    function getAttachmentByReplyId($MJTC_id ,$MJTC_internalid = ''){
        if(!is_numeric($MJTC_id)) return false;
        $MJTC_inquery = '';
        //if not admin and agent
        if(!current_user_can('manage_options') && !(in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff())){
            $MJTC_inquery = majesticsupport::$_db->prepare(" AND ticket.internalid = %s", $MJTC_internalid);
            
        }
        $MJTC_query = "SELECT attachment.filename , ticket.attachmentdir
            FROM `" . majesticsupport::$_db->prefix . "mjtc_support_attachments` AS attachment
            JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket ON ticket.id = attachment.ticketid WHERE attachment.replyattachmentid = ".absint($MJTC_id) ;
        $MJTC_query .= $MJTC_inquery;
        $MJTC_replyattachments = majesticsupport::$_db->get_results($MJTC_query);
        return $MJTC_replyattachments;
    }

    function editReply($MJTC_data) {
        if (empty($MJTC_data))
            return false;
        $MJTC_desc = wpautop(wptexturize(MJTC_majesticsupportphplib::MJTC_stripslashes($MJTC_data['mjsupport_replytext']))); // use mjsupport_message to avoid conflict

        $MJTC_row = MJTC_includer::MJTC_getTable('replies');
        if (!$MJTC_row->update(array('id' => $MJTC_data['reply-replyid'], 'message' => $MJTC_desc))) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        return;
    }

    function storeMergeTicketReplies($MJTC_reply,$MJTC_ticketid){
        if(!is_string($MJTC_reply))
            return false;
        $MJTC_id          = $MJTC_ticketid;
        $MJTC_user_id        = MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid();
        $MJTC_username       = MJTC_includer::MJTC_getModel('majesticsupport')->getUserNameById($MJTC_user_id);
        $MJTC_query_array    = array(
            'uid'       => $MJTC_user_id,
            'ticketid'  => $MJTC_id,
            'name'      => $MJTC_username,
            'message'   => $MJTC_reply,
            'status'    => 1,
            'created'   => date_i18n('Y-m-d H:i:s'),
            'mergemessage'   => 1,
        );
        majesticsupport::$_db->replace(majesticsupport::$_db->prefix . 'mjtc_support_replies', $MJTC_query_array);
        if (majesticsupport::$_db->last_error == null) {
            MJTC_message::MJTC_setMessage(esc_html(__('Reply Has been Posted', 'majestic-support')), 'updated');
            $MJTC_messagetype = esc_html(__('Successfully', 'majestic-support'));
        }else {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError(); // if there is an error add it to system errorrs
            MJTC_message::MJTC_setMessage(esc_html(__('Reply Has Not been Posted', 'majestic-support')), 'error');
            $MJTC_messagetype = esc_html(__('Error', 'majestic-support'));
        }
    }

    function getTicketLastReplyById($MJTC_ticketid) {
        if (!is_numeric($MJTC_ticketid))
            return false;
        $MJTC_query = majesticsupport::$_db->prepare("SELECT message FROM `" . majesticsupport::$_db->prefix . "mjtc_support_replies` WHERE ticketid = %d ORDER BY created DESC LIMIT 1", absint($MJTC_ticketid));
        $MJTC_lastreply = majesticsupport::$_db->get_var($MJTC_query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError(); // if there is an error add it to system errorrs
        }
        return $MJTC_lastreply;
    }
    function getUserNameFromReplyById($MJTC_replyid) { // name field value is empty in some old tickets
        if (!is_numeric($MJTC_replyid))
            return false;
		$MJTC_name = "";
        $MJTC_query = "SELECT user.* 
			FROM `" . majesticsupport::$_db->prefix . "mjtc_support_replies` AS reply
			JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_users` AS user ON reply.uid = user.id
			WHERE reply.id =  " . absint($MJTC_replyid);
        $MJTC_replyuser = majesticsupport::$_db->get_row($MJTC_query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError(); // if there is an error add it to system errorrs
        }
		if(isset($MJTC_replyuser)){
            $MJTC_name = $MJTC_replyuser->name;
			if($MJTC_name == ""){
				$MJTC_name = $MJTC_replyuser->display_name;
			}
			if($MJTC_name == ""){
				$MJTC_name = $MJTC_replyuser->user_nicename;
			}
		}
		return $MJTC_name;
    }

    function markedAsAiPoweredReply() {
        $MJTC_nonce  = MJTC_request::MJTC_getVar('_wpnonce');

        if (!wp_verify_nonce($MJTC_nonce, 'ai-powered-reply')) {
            wp_die('Security check failed');
        }
        $MJTC_status = (int) MJTC_request::MJTC_getVar('status');
        $type   = MJTC_request::MJTC_getVar('type');
        $MJTC_id     = intval(MJTC_request::MJTC_getVar('id'));

        if ($MJTC_id <= 0 || !in_array($type, ['ticket', 'reply'])) {
            return false;
        }

        $table = ($type === 'ticket') ? 'mjtc_support_tickets' : 'mjtc_support_replies';
        $MJTC_query = majesticsupport::$_db->prepare("UPDATE `" . majesticsupport::$_db->prefix . "$table` SET aireplymode = %d WHERE id = %d", absint($MJTC_status), absint($MJTC_id));

        $MJTC_result = majesticsupport::$_db->query($MJTC_query);

        return ($MJTC_result !== false);
    }

    function getFilteredReplies() {
        // Verify nonce
        check_ajax_referer('get-filtered-replies', '_wpnonce');

        $MJTC_ticket_id = MJTC_request::MJTC_getVar('ticket_id', null, 0, 'int');

        if (!$MJTC_ticket_id) {
            wp_send_json_error(['message' => __('Ticket ID is required.', 'majestic-support')]);
        }

        // 
        // 1. Check if the user is a Agent
        $is_staff = (in_array('agent', majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff());

        // 2. If they are staff, check if they LACK the specific AI permission
        if ($is_staff) {
            $has_ai_permission = MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForTask('Use AI Powered Reply Feature');
            if (!$has_ai_permission) { // Note the "!" (NOT)
                wp_send_json_error(['message' => __('You do not have permission to use AI features.', 'majestic-support')]);
            }
        } 
        // 3. If they are NOT staff, check if they are an Administrator
        else if (!current_user_can('manage_options')) {
            // If they aren't staff and aren't an admin, they are a normal user or guest
            wp_send_json_error(['message' => __('Access denied.', 'majestic-support')]);
        }

        // If it reaches here, the user is either:
        // - Staff WITH AI permissions
        // - An Administrator
        // 
        // 

        $MJTC_uids = $this->get_allowed_support_user_ids();
        if (empty($MJTC_uids)) {
            wp_send_json_success(['replies' => [], 'count' => 0]);
        }

        $MJTC_uids_str = implode(',', array_map('absint', $MJTC_uids)); // Ensure integers

        $MJTC_query = "
        SELECT r.*
            FROM `" . majesticsupport::$_db->prefix . "mjtc_support_replies` AS r
            WHERE r.ticketid = " . absint($MJTC_ticket_id) . "
            AND r.uid IN ($MJTC_uids_str)";

        $MJTC_query .= " ORDER BY r.created ASC LIMIT 50";
        $MJTC_replies = majesticsupport::$_db->get_results($MJTC_query);

        if (majesticsupport::$_db->last_error) {
            wp_send_json_error(['message' => __('Database error occurred.', 'majestic-support')]);
        }

        $MJTC_formatted_replies = [];
        foreach ($MJTC_replies as $MJTC_reply) {
            $MJTC_name = '';
            $MJTC_anon_setting = majesticsupport::$_config['anonymous_name_on_ticket_reply'];

            if ($MJTC_anon_setting == 1) {
                $MJTC_name = majesticsupport::$_config['title'];
            } elseif ($MJTC_anon_setting == 2) {
                $MJTC_name = MJTC_includer::MJTC_getModel('reply')->getUserNameFromReplyById($MJTC_reply->id);
            }

            $MJTC_formatted_replies[] = [
                'id'        => $MJTC_reply->id,
                'text'      => $MJTC_reply->message,
                'name'      => $MJTC_name,
                'timestamp' => $MJTC_reply->created,
                'isMarked'  => (bool) $MJTC_reply->aireplymode
            ];
        }

        wp_send_json_success([
            'replies' => $MJTC_formatted_replies,
            'count'   => count($MJTC_formatted_replies)
        ]);
    }

    function get_allowed_support_user_ids() {
        $MJTC_allowed_uids = [];

        // Get WordPress administrator user IDs
        $MJTC_admin_wp_ids = majesticsupport::$_db->get_col(
            "SELECT user_id
            FROM `" . majesticsupport::$_db->prefix . "usermeta`
            WHERE meta_key = '" . majesticsupport::$_db->prefix . "capabilities'
            AND meta_value LIKE '%administrator%'"
        );

        // Convert WP user IDs to Majestic Support user IDs
        if (!empty($MJTC_admin_wp_ids)) {
            foreach ($MJTC_admin_wp_ids as $MJTC_wp_id) {
                $mjtc_user = MJTC_includer::MJTC_getObjectClass('user')->MJTC_getmajesticsupportuidbyuserid($MJTC_wp_id);
                if (!empty($mjtc_user) && isset($mjtc_user[0]->id)) {
                    $MJTC_allowed_uids[] = (int)$mjtc_user[0]->id;
                }
            }
        }

        // Add agent user IDs if the 'agent' addon is active
        if (in_array('agent', majesticsupport::$_active_addons)) {
            $MJTC_agent_ids = majesticsupport::$_db->get_col(
                "SELECT uid
                FROM `" . majesticsupport::$_db->prefix . "mjtc_support_staff`"
            );
            foreach ($MJTC_agent_ids as $MJTC_id) {
                $MJTC_allowed_uids[] = (int)$MJTC_id;
            }
        }

        // Deduplicate and ensure all values are positive integers
        $MJTC_allowed_uids = array_unique(array_filter(array_map('intval', $MJTC_allowed_uids)));

        // Avoid empty IN() errors
        return !empty($MJTC_allowed_uids) ? $MJTC_allowed_uids : [0];
    }
}

?>
