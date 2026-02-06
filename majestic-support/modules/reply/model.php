<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_replyModel {

    function getReplies($id) {
        if (!is_numeric($id))
            return false;
        // Data

        do_action('reset_ms_aadon_query');
        do_action('ms_aadon_getreplies');// to prepare any addon based query (action is defined in two addons)
        $ordering = majesticsupport::$_config['ticket_replies_ordering'];
        $ordering = strtoupper(trim($ordering)); // Normalize input

        // Allow only ASC or DESC
        if (!in_array($ordering, ['ASC', 'DESC'])) {
            $ordering = 'ASC'; // default fallback
        }
        $query = "SELECT replies.*,replies.id AS replyid,user.user_email AS useremail,viewer.display_name AS viewername,tickets.id,tickets.uid AS ticketsuid ".majesticsupport::$_addon_query['select']."
                    FROM `" . majesticsupport::$_db->prefix . "mjtc_support_replies` AS replies
                    JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS tickets ON  replies.ticketid = tickets.id
                    LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_users` AS user ON  replies.uid = user.id
                    LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_users` AS viewer ON  replies.viewed_by = viewer.id
                    ".majesticsupport::$_addon_query['join']."
                    WHERE tickets.id = " . esc_sql($id) . " ORDER By replies.id ".esc_sql($ordering);
        majesticsupport::$_data[4] = majesticsupport::$_db->get_results($query);
        do_action('reset_ms_aadon_query');
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        $attachmentmodel = MJTC_includer::MJTC_getModel('attachment');
        foreach (majesticsupport::$_data[4] AS $reply) {
            $reply->attachments = $attachmentmodel->getAttachmentForReply($reply->id, $reply->replyid);
            $current_user = MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid();
            $viewed_by = isset($current_user) ? $current_user : -1; //-1 for handle visitor case
            $update_required = false; // Flag to determine if the update is needed

            // Check if the reply has not been viewed
            if (empty($reply->viewed_by) && empty($reply->mergemessage)) {

                // If the current user is an admin
                if (is_admin()) {
                    // Admin viewing someone else's reply and it's not staff
                    if ($reply->uid != $current_user && empty($reply->staffid)) {
                        $update_required = true; // Mark update as required
                    }
                } else { // If the current user is not an admin

                    // Check if the 'agent' addon is active and the user is staff
                    if (in_array('agent', majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) {
                        // Check if the ticket owner is the reply owner
                        if ($reply->ticketsuid == $reply->uid) {
                            $update_required = true; // Mark update as required
                        }
                    } else { // If the user is not staff or the agent addon is inactive
                        // Check if the ticket owner is not the reply owner
                        if ($reply->ticketsuid != $reply->uid) {
                            $update_required = true; // Mark update as required
                        }
                    }
                }
            }
            // Execute the query if an update is required
            if ($update_required) {
                $query = "UPDATE `" . majesticsupport::$_db->prefix . "mjtc_support_replies` SET viewed_by = " . esc_sql($viewed_by) . ", viewed_on = '" . esc_sql(date_i18n('Y-m-d H:i:s')) . "' WHERE id = " . esc_sql($reply->replyid);
                majesticsupport::$_db->query($query);
            }
        }
        return;
    }

    function getTicketNameForReplies() {
        $query = "SELECT id, ticketid AS text FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets`";
        $list = majesticsupport::$_db->get_results($query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        return $list;
    }

    function getRepliesForForm($id) {
        if ($id) {
            if (!is_numeric($id))
                return false;
            $query = "SELECT replies.*,tickets.id
                        FROM `" . majesticsupport::$_db->prefix . "mjtc_support_replies` AS replies
                        JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS tickets ON  replies.ticketid = tickets.id
                        WHERE replies.id = " . esc_sql($id);
            majesticsupport::$_data[0] = majesticsupport::$_db->get_row($query);
            if (majesticsupport::$_db->last_error != null) {
                MJTC_includer::MJTC_getModel('systemerror')->addSystemError(); // if there is an error add it to system errorrs
            }
        }
        return;
    }

    function storeReplies($MJTC_data) {
        $MJTC_nonce_id = $MJTC_data['ticketid'];
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $nonce, 'save-reply-'.$MJTC_nonce_id) ) {
            die( 'Security check Failed' );
        }
        $checkduplicatereplies = $this->checkIsReplyDuplicate($MJTC_data);
        if(!$checkduplicatereplies){
            return false;
        }
        //validate reply for break down
        $MJTC_ticketid   = $MJTC_data['ticketrandomid'];
        $internalid   = $MJTC_data['internalid'];
        $hash       = $MJTC_data['hash'];
        $query = "SELECT id FROM `".majesticsupport::$_db->prefix."mjtc_support_tickets` WHERE ticketid='".esc_sql($MJTC_ticketid)."'
        AND IF(`hash` is NULL,true,`hash`='".esc_sql($hash)."') ";
        $id = majesticsupport::$_db->get_var($query);
        if($id != $MJTC_data['ticketid']){
            return;
        }//end

        $MJTC_ticketviaemailstaffid = 0;
        // set in Email Piping
        if(isset($MJTC_data['staffid'])){
            $MJTC_ticketviaemailstaffid = $MJTC_data['staffid'];
            unset($MJTC_data['staffid']);
        }
        if ( in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) {
            $allowed = MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForTask('Reply Ticket');
            if ($allowed != true) {
                MJTC_message::MJTC_setMessage(esc_html(__('You are not allowed', 'majestic-support')), 'error');
                return;
            }
        } else if (!MJTC_includer::MJTC_getModel('ticket')->validateTicketAction($id, $internalid)) {
            MJTC_message::MJTC_setMessage(esc_html(__('You are not allowed','majestic-support')), 'error');
            return false;
        }
        // check whether ticket is closed or not incase of ticket viw email
        if(isset($MJTC_data['ticketviaemail']) && $MJTC_data['ticketviaemail'] == 1){
            if(majesticsupport::$_config['reply_to_closed_ticket'] != 1){
                $closed = MJTC_includer::MJTC_getModel('ticket')->checkActionStatusSame($MJTC_data['ticketid'],array('action' => 'closeticket'));
                if($closed == false){
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
        $sendEmail = true;
        $staffid = 0;
        if (!MJTC_includer::MJTC_getObjectClass('user')->MJTC_isguest()) {
            //$current_user = get_userdata(MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid());
            $currentUserName = MJTC_includer::MJTC_getObjectClass('user')->MJTC_fullname();
            if( in_array('agent',majesticsupport::$_active_addons) ){
                //$staffid = MJTC_includer::MJTC_getModel('agent')->getStaffId($current_user->ID);
				$staffid = MJTC_includer::MJTC_getModel('agent')->getStaffId(MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid());
            }
        } else {
            $currentUserName = '';
        }

        if($staffid == 0 && $MJTC_ticketviaemailstaffid != 0){
            $staffid = $MJTC_ticketviaemailstaffid;
        }

        //check the assign to me on reply
        if (isset($MJTC_data['assigntome']) && $MJTC_data['assigntome'] == 1) {
            MJTC_includer::MJTC_getModel('ticket')->ticketAssignToMe($MJTC_data['ticketid'], $staffid);
        }
        if(isset($MJTC_data['ticketviaemail'])){
            if($MJTC_data['ticketviaemail'] == 1)
                $currentUserName = $MJTC_data['name'];
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
        $MJTC_data['name'] = $currentUserName;
        $MJTC_data['staffid'] = $staffid;

        $row = MJTC_includer::MJTC_getTable('replies');

        $MJTC_data = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($MJTC_data);// remove slashes with quotes.
        $error = 0;
        if (!$row->bind($MJTC_data)) {
            $error = 1;
        }
        if (!$row->store()) {
            $error = 1;
        }

        if ($error == 0) {
            $replyid = $row->id;
            // smart reply store
            if (isset($MJTC_data['add_smartreply']) && $MJTC_data['add_smartreply'] == 1) {
                $samrtreplyTitle = MJTC_includer::MJTC_getModel('ticket')->getTicketSubjectById($MJTC_data['ticketid']);
                $samrtreply['id'] = '';
                $samrtreply['title'] = $samrtreplyTitle;
                $samrtreply['ticketsubjects'][0] = $samrtreplyTitle;
                $samrtreply['reply'] = $MJTC_data['message'];
                MJTC_includer::MJTC_getModel('smartreply')->storeSmartReply($samrtreply);
            }
            //tickets attachments store
            $MJTC_data['replyattachmentid'] = $replyid;
            MJTC_includer::MJTC_getModel('attachment')->storeAttachments($MJTC_data);
            //reply stored change action
            if (is_admin()){
                MJTC_includer::MJTC_getModel('ticket')->setStatus(4, $MJTC_data['ticketid']); // 4 -> waiting for customer reply
                if(in_array('timetracking', majesticsupport::$_active_addons)){
                    MJTC_includer::MJTC_getModel('timetracking')->storeTimeTaken($MJTC_data,$replyid,1);// to store time for reply 1 is to identfy that current record is reply
                }
            }else {
                if ( in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()){
                    MJTC_includer::MJTC_getModel('ticket')->setStatus(4, $MJTC_data['ticketid']); // 4 -> waiting for customer reply
                    $MJTC_data['staffid'] = $staffid;
                    if(in_array('timetracking', majesticsupport::$_active_addons)){
                        MJTC_includer::MJTC_getModel('timetracking')->storeTimeTaken($MJTC_data,$replyid,1);// to store time for reply 1 is to identfy that current record is reply
                    }

                }else{
                    MJTC_includer::MJTC_getModel('ticket')->setStatus(2, $MJTC_data['ticketid']); // 2 -> waiting for admin/staff reply
                }
            }
            MJTC_includer::MJTC_getModel('ticket')->updateLastReply($MJTC_data['ticketid']);
            MJTC_message::MJTC_setMessage(esc_html(__('Reply posted', 'majestic-support')), 'updated');
            $messagetype = esc_html(__('Successfully', 'majestic-support'));

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
                $devicetoken = MJTC_includer::MJTC_getModel('notification')->checkSubscriptionForAdmin();
                if($devicetoken){
                    $MJTC_dataarray['link'] = admin_url("admin.php?page=majesticsupport_ticket&mjslay=ticketdetail&majesticsupportid=".esc_attr($MJTC_data['ticketid']));
                    $MJTC_dataarray['devicetoken'] = $devicetoken;
                    $MJTC_value = majesticsupport::$_config[MJTC_majesticsupportphplib::MJTC_md5(MSTN)];
                    if($MJTC_value != ''){
                      do_action('send_push_notification',$MJTC_dataarray);
                    }else{
                      do_action('resetnotificationvalues');
                    }
                }

                $MJTC_dataarray['link'] = majesticsupport::makeUrl(array('mjsmod'=>'ticket', 'mjslay'=>'ticketdetail', "majesticsupportid"=>$MJTC_data['ticketid'],'mspageid'=>majesticsupport::getPageid()));
                if($MJTC_ticketuid != 0 && ($MJTC_ticketuid != MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid())){
                    $devicetoken = MJTC_includer::MJTC_getModel('notification')->getUserDeviceToken($MJTC_ticketuid);
                    $MJTC_dataarray['devicetoken'] = $devicetoken;
                    if($devicetoken != '' && !empty($devicetoken)){
                        $MJTC_value = majesticsupport::$_config[MJTC_majesticsupportphplib::MJTC_md5(MSTN)];
                        if($MJTC_value != ''){
                          do_action('send_push_notification',$MJTC_dataarray);
                        }else{
                          do_action('resetnotificationvalues');
                        }
                    }
                }

                if($MJTC_ticketstaffid != 0 && ($MJTC_ticketuid != $staffid)){
                    $devicetoken = MJTC_includer::MJTC_getModel('notification')->getUserDeviceToken($MJTC_ticketstaffid);
                    $MJTC_dataarray['devicetoken'] = $devicetoken;
                    if($devicetoken != '' && !empty($devicetoken)){
                        $MJTC_value = majesticsupport::$_config[MJTC_majesticsupportphplib::MJTC_md5(MSTN)];
                        if($MJTC_value != ''){
                          do_action('send_push_notification',$MJTC_dataarray);
                        }else{
                          do_action('resetnotificationvalues');
                        }
                    }
                }
                if($MJTC_ticketuid == 0){ // for visitor
                    $tokenarray['emailaddress'] = MJTC_includer::MJTC_getModel('ticket')->getTicketEmailById($MJTC_data['ticketid']);
                    $tokenarray['trackingid'] = MJTC_includer::MJTC_getModel('ticket')->getTrackingIdById($MJTC_data['ticketid']);
                    $tokenarray['sitelink']=MJTC_includer::MJTC_getModel('majesticsupport')->getEncriptedSiteLink();
                    $token = wp_json_encode($tokenarray);
                    include_once MJTC_PLUGIN_PATH . 'includes/encoder.php';
                    $encoder = new MJTC_encoder();
                    $encryptedtext = $encoder->MJTC_encrypt($token);
                    $MJTC_dataarray['link'] = majesticsupport::makeUrl(array('mjsmod'=>'ticket' ,'task'=>'showticketstatus','action'=>'mstask','token'=>$encryptedtext,'mspageid'=>majesticsupport::getPageid()));
                    $notificationid = MJTC_includer::MJTC_getModel('ticket')->getNotificationIdById($MJTC_data['ticketid']);
                    $devicetoken = MJTC_includer::MJTC_getModel('notification')->getUserDeviceToken($notificationid,0);
                    if($devicetoken != '' && !empty($devicetoken)){
                        $MJTC_value = majesticsupport::$_config[MJTC_majesticsupportphplib::MJTC_md5(MSTN)];
                        if($MJTC_value != ''){
                          do_action('send_push_notification',$MJTC_dataarray);
                        }else{
                          do_action('resetnotificationvalues');
                        }
                    }
                }
            }
            // End notification
        }else {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError(); // if there is an error add it to system errorrs
            MJTC_message::MJTC_setMessage(esc_html(__('Reply posted', 'majestic-support')), 'error');
            $messagetype = esc_html(__('Error', 'majestic-support'));
            $sendEmail = false;
        }

        /* for activity log */
        $MJTC_ticketid = $MJTC_data['ticketid']; // get the ticket id
        $current_user = MJTC_includer::MJTC_getObjectClass('user')->MJTC_getMSCurrentUser(); // to get current user name
        $currentUserName = isset($current_user->display_name) ? $current_user->display_name : esc_html(__('Guest', 'majestic-support'));
        $eventtype = 'REPLIED_TICKET';
        $message = esc_html(__('Ticket is replied by', 'majestic-support')) . " ( " . esc_html($currentUserName) . " ) ";
        if(in_array('tickethistory', majesticsupport::$_active_addons)){
            MJTC_includer::MJTC_getModel('tickethistory')->addActivityLog($MJTC_ticketid, 1, $eventtype, $message, $messagetype);
        }

        // Send Emails
        if ($sendEmail == true) {
            if (is_admin()) {
                MJTC_includer::MJTC_getModel('email')->sendMail(1, 4, $MJTC_ticketid); // Mailfor, Reply Ticket
            } else {
                MJTC_includer::MJTC_getModel('email')->sendMail(1, 5, $MJTC_ticketid); // Mailfor, Reply Ticket
            }
            $MJTC_ticketreplyobject = majesticsupport::$_db->get_row("SELECT * FROM `" . majesticsupport::$_db->prefix . "mjtc_support_replies` WHERE id = " . esc_sql($replyid));
            do_action('ms-ticketreply', $MJTC_ticketreplyobject);
        }
        // if Close on reply is cheked
        if ($MJTC_data['closeonreply'] == 1) {
            MJTC_includer::MJTC_getModel('ticket')->closeTicket($MJTC_ticketid, $internalid);
        }

        return;
    }

    function checkIsReplyDuplicate($MJTC_data){
        if(empty($MJTC_data)) return false;
        
        $curdate = date_i18n('Y-m-d H:i:s');
        $inquery = '';
        if (isset($MJTC_data['ticketviaemail']) && $MJTC_data['ticketviaemail'] == 1) {
            $inquery .= " AND ticketviaemail = 1";
        }
        $query = "SELECT created FROM `" . majesticsupport::$_db->prefix . "mjtc_support_replies` WHERE ticketid = '" . esc_sql($MJTC_data['ticketid']) . "' AND uid = '" . esc_sql($MJTC_data['uid']) . "' ORDER BY created DESC LIMIT 1";
        $query .= $inquery;
        $datetime = majesticsupport::$_db->get_var($query);
        if($datetime){
            $diff = MJTC_majesticsupportphplib::MJTC_strtotime($curdate) - MJTC_majesticsupportphplib::MJTC_strtotime($datetime);
            if($diff <= 7){
                return false;
            }
        }
        return true;
    }

    function getLastReply($MJTC_ticketid) {
        if (!is_numeric($MJTC_ticketid))
            return false;
        $query = "SELECT created FROM `" . majesticsupport::$_db->prefix . "mjtc_support_replies` WHERE ticketid =  " . esc_sql($MJTC_ticketid) . " ORDER BY created desc";
        $lastreply = majesticsupport::$_db->get_var($query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError(); // if there is an error add it to system errorrs
        }
        return $lastreply;
    }

    function removeTicketReplies($MJTC_ticketid) {
        if(!is_numeric($MJTC_ticketid)) return false;
        majesticsupport::$_db->delete(majesticsupport::$_db->prefix . 'mjtc_support_replies', array('ticketid' => $MJTC_ticketid));
        return;
    }

    function getReplyDataByID() {
        $replyid = MJTC_request::MJTC_getVar('val');
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $nonce, 'get-reply-data-by-id-'.$replyid) ) {
            die( 'Security check Failed' );
        }
        if(!is_numeric($replyid)) return false;
        $query = "SELECT reply.id AS replyid, reply.message AS message
                    FROM `" . majesticsupport::$_db->prefix . "mjtc_support_replies` AS reply
                    WHERE reply.id =  " . esc_sql($replyid) ;
        $lastreply = majesticsupport::$_db->get_row($query);
        $lastreply->message = MJTC_majesticsupportphplib::MJTC_htmlentities(($lastreply->message));

        return wp_json_encode($lastreply);
    }

    function getAttachmentByReplyId($id ,$internalid = ''){
        if(!is_numeric($id)) return false;
        $inquery = '';
        //if not admin and agent
        if(!current_user_can('manage_options') && !(in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff())){
            $inquery = " AND ticket.internalid = '".esc_sql($internalid)."'";
            
        }
        $query = "SELECT attachment.filename , ticket.attachmentdir
            FROM `" . majesticsupport::$_db->prefix . "mjtc_support_attachments` AS attachment
            JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket ON ticket.id = attachment.ticketid WHERE attachment.replyattachmentid = ".esc_sql($id) ;
        $query .= $inquery;
        $replyattachments = majesticsupport::$_db->get_results($query);
        return $replyattachments;
    }

    function editReply($MJTC_data) {
        if (empty($MJTC_data))
            return false;
        $desc = wpautop(wptexturize(MJTC_majesticsupportphplib::MJTC_stripslashes($MJTC_data['mjsupport_replytext']))); // use mjsupport_message to avoid conflict

        $row = MJTC_includer::MJTC_getTable('replies');
        if (!$row->update(array('id' => $MJTC_data['reply-replyid'], 'message' => $desc))) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        return;
    }

    function storeMergeTicketReplies($reply,$MJTC_ticketid){
        if(!is_string($reply))
            return false;
        $id          = $MJTC_ticketid;
        $user_id        = MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid();
        $username       = MJTC_includer::MJTC_getModel('majesticsupport')->getUserNameById($user_id);
        $query_array    = array(
            'uid'       => $user_id,
            'ticketid'  => $id,
            'name'      => $username,
            'message'   => $reply,
            'status'    => 1,
            'created'   => date_i18n('Y-m-d H:i:s'),
            'mergemessage'   => 1,
        );
        majesticsupport::$_db->replace(majesticsupport::$_db->prefix . 'mjtc_support_replies', $query_array);
        if (majesticsupport::$_db->last_error == null) {
            MJTC_message::MJTC_setMessage(esc_html(__('Reply Has been Posted', 'majestic-support')), 'updated');
            $messagetype = esc_html(__('Successfully', 'majestic-support'));
        }else {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError(); // if there is an error add it to system errorrs
            MJTC_message::MJTC_setMessage(esc_html(__('Reply Has Not been Posted', 'majestic-support')), 'error');
            $messagetype = esc_html(__('Error', 'majestic-support'));
        }
    }

    function getTicketLastReplyById($MJTC_ticketid) {
        if (!is_numeric($MJTC_ticketid))
            return false;
        $query = "SELECT message FROM `" . majesticsupport::$_db->prefix . "mjtc_support_replies` WHERE ticketid =  " . esc_sql($MJTC_ticketid) . " ORDER BY created desc LIMIT 1";
        $lastreply = majesticsupport::$_db->get_var($query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError(); // if there is an error add it to system errorrs
        }
        return $lastreply;
    }
    function getUserNameFromReplyById($replyid) { // name field value is empty in some old tickets
        if (!is_numeric($replyid))
            return false;
		$name = "";
        $query = "SELECT user.* 
			FROM `" . majesticsupport::$_db->prefix . "mjtc_support_replies` AS reply
			JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_users` AS user ON reply.uid = user.id
			WHERE reply.id =  " . esc_sql($replyid);
        $replyuser = majesticsupport::$_db->get_row($query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError(); // if there is an error add it to system errorrs
        }
		if(isset($replyuser)){
            $name = $replyuser->name;
			if($name == ""){
				$name = $replyuser->display_name;
			}
			if($name == ""){
				$name = $replyuser->user_nicename;
			}
		}
		return $name;
    }

    function markedAsAiPoweredReply() {
        $nonce  = MJTC_request::MJTC_getVar('_wpnonce');

        if (!wp_verify_nonce($nonce, 'ai-powered-reply')) {
            wp_die('Security check failed');
        }
        $status = MJTC_request::MJTC_getVar('status');
        $type   = MJTC_request::MJTC_getVar('type');
        $id     = intval(MJTC_request::MJTC_getVar('id'));

        if ($id <= 0 || !in_array($type, ['ticket', 'reply'])) {
            return false;
        }

        $table = ($type === 'ticket') ? 'mjtc_support_tickets' : 'mjtc_support_replies';
        $query = "UPDATE `" . majesticsupport::$_db->prefix . "$table` SET aireplymode = " . esc_sql($status) . " WHERE id = " . esc_sql($id);

        $result = majesticsupport::$_db->query($query);

        return ($result !== false);
    }

    function getFilteredReplies() {
        // Verify nonce
        check_ajax_referer('get-filtered-replies', '_wpnonce');

        $MJTC_ticket_id = intval(MJTC_request::MJTC_getVar('ticket_id'));

        if (!$MJTC_ticket_id) {
            wp_send_json_error(['message' => __('Ticket ID is required.', 'majestic-support')]);
        }

        $uids = $this->get_allowed_support_user_ids();
        if (empty($uids)) {
            wp_send_json_success(['replies' => [], 'count' => 0]);
        }

        $uids_str = implode(',', array_map('intval', $uids)); // Ensure integers

        $query = "
        SELECT r.*
            FROM `" . majesticsupport::$_db->prefix . "mjtc_support_replies` AS r
            WHERE r.ticketid = " . esc_sql($MJTC_ticket_id) . "
            AND r.uid IN ($uids_str)";

        $query .= " ORDER BY r.created ASC LIMIT 50";
        $replies = majesticsupport::$_db->get_results($query);

        if (majesticsupport::$_db->last_error) {
            wp_send_json_error(['message' => __('Database error occurred.', 'majestic-support')]);
        }

        $formatted_replies = [];
        foreach ($replies as $reply) {
            $name = '';
            $anon_setting = majesticsupport::$_config['anonymous_name_on_ticket_reply'];

            if ($anon_setting == 1) {
                $name = majesticsupport::$_config['title'];
            } elseif ($anon_setting == 2) {
                $name = MJTC_includer::MJTC_getModel('reply')->getUserNameFromReplyById($reply->id);
            }

            $formatted_replies[] = [
                'id'        => $reply->id,
                'text'      => $reply->message,
                'name'      => $name,
                'timestamp' => $reply->created,
                'isMarked'  => (bool) $reply->aireplymode
            ];
        }

        wp_send_json_success([
            'replies' => $formatted_replies,
            'count'   => count($formatted_replies)
        ]);
    }

    function get_allowed_support_user_ids() {
        $allowed_uids = [];

        // Get WordPress administrator user IDs
        $admin_wp_ids = majesticsupport::$_db->get_col(
            "SELECT user_id
            FROM `" . majesticsupport::$_db->prefix . "usermeta`
            WHERE meta_key = '" . majesticsupport::$_db->prefix . "capabilities'
            AND meta_value LIKE '%administrator%'"
        );

        // Convert WP user IDs to Majestic Support user IDs
        if (!empty($admin_wp_ids)) {
            foreach ($admin_wp_ids as $wp_id) {
                $mjtc_user = MJTC_includer::MJTC_getObjectClass('user')->MJTC_getmajesticsupportuidbyuserid($wp_id);
                if (!empty($mjtc_user) && isset($mjtc_user[0]->id)) {
                    $allowed_uids[] = (int)$mjtc_user[0]->id;
                }
            }
        }

        // Add agent user IDs if the 'agent' addon is active
        if (in_array('agent', majesticsupport::$_active_addons)) {
            $agent_ids = majesticsupport::$_db->get_col(
                "SELECT uid
                FROM `" . majesticsupport::$_db->prefix . "mjtc_support_staff`"
            );
            foreach ($agent_ids as $id) {
                $allowed_uids[] = (int)$id;
            }
        }

        // Deduplicate and ensure all values are positive integers
        $allowed_uids = array_unique(array_filter(array_map('intval', $allowed_uids)));

        // Avoid empty IN() errors
        return !empty($allowed_uids) ? $allowed_uids : [0];
    }
}

?>
