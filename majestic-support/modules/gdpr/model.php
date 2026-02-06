<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_gdprModel {

	function getGDPRFeilds(){
		$query = "SELECT * FROM `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` WHERE fieldfor = 3 ORDER BY ordering ";
		majesticsupport::$_data[0] = majesticsupport::$_db->get_results($query);
		if (majesticsupport::$_db->last_error != null) {
		    MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
		}
	}

	function getEraseDataRequests(){
		$query = "SELECT * FROM `" . majesticsupport::$_db->prefix . "mjtc_support_erasedatarequests`";
		majesticsupport::$_data[0] = majesticsupport::$_db->get_results($query);
		if (majesticsupport::$_db->last_error != null) {
		    MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
		}

        $email = majesticsupport::$_search['gdpr']['email'];
        $email = majesticsupport::parseSpaces($email);
        $inquery = '';
        if ($email != null)
            $inquery .= " WHERE user.user_email LIKE '%".esc_sql($email)."%'";

        majesticsupport::$_data['filter']['email'] = $email;

        // Pagination
        $query = "SELECT COUNT(request.id)
                    FROM `" . majesticsupport::$_db->prefix . "mjtc_support_erasedatarequests` AS request
                    LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_users` AS user ON user.id = request.uid
                    ";
        $query .= $inquery;
        $total = majesticsupport::$_db->get_var($query);
        majesticsupport::$_data['total'] = $total;
        majesticsupport::$_data[1] = MJTC_pagination::MJTC_getPagination($total);

        // Data
        $query = "SELECT request.*, user.user_email
                    FROM `" . majesticsupport::$_db->prefix . "mjtc_support_erasedatarequests` AS request
                    LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_users` AS user ON user.id = request.uid
                    ";
        $query .= $inquery;
        $query .= " ORDER BY request.created DESC LIMIT " . MJTC_pagination::MJTC_getOffset() . ", " . MJTC_pagination::MJTC_getLimit();
        majesticsupport::$_data[0] = majesticsupport::$_db->get_results($query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
	}

    function getUserEraseDataRequest(){
        $uid = MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid();
        if($uid == 0){
            return;
        }
        $query = "SELECT * FROM `" . majesticsupport::$_db->prefix . "mjtc_support_erasedatarequests` WHERE uid = ".esc_sql($uid);
        majesticsupport::$_data[0] = majesticsupport::$_db->get_row($query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
    }

    function storeUserEraseRequest($MJTC_data){
        $MJTC_nonce_id = isset($MJTC_data['id']) ? $MJTC_data['id'] : '';
    	$nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $nonce, 'save-usereraserequest-'.$MJTC_nonce_id) ) {
            die( 'Security check Failed' );
        }
        if (!$MJTC_data['id']) { //new
    	    $MJTC_data['created'] = date_i18n('Y-m-d H:i:s');
            $MJTC_data['uid'] = MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid();
            $MJTC_data['status'] = 1;
    	}
    	$MJTC_data = majesticsupport::MJTC_sanitizeData($MJTC_data); // MJTC_sanitizeData() function uses wordpress santize functions
    	$MJTC_data['message'] = MJTC_includer::MJTC_getModel('majesticsupport')->getSanitizedEditorData($_POST['message']);// MJTC_sanitizeData() function uses wordpress santize functions
    	$row = MJTC_includer::MJTC_getTable('erasedatarequests');
    	$MJTC_data = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($MJTC_data);// remove slashes with quotes.
    	$error = 0;
    	if (!$row->bind($MJTC_data)) {
            $error = 1;
    	}
    	if (!$row->store()) {
            $error = 1;
    	}

    	if ($error == 0) {
    	    MJTC_message::MJTC_setMessage(esc_html(__('Erasing data request has been stored', 'majestic-support')), 'updated');
    	} else {
    	    MJTC_includer::MJTC_getModel('systemerror')->addSystemError(); // if there is an error add it to system errorrs
    	    MJTC_message::MJTC_setMessage(esc_html(__('Failed while storing', 'majestic-support')), 'error');
    	}
        return;
    }

    function deleteUserEraseRequest($id){
        if(!is_numeric($id)){
            return false;
        }
        if($this->checkCanDelete($id)){
            $row = MJTC_includer::MJTC_getTable('erasedatarequests');
            if ($row->delete($id)) {
                MJTC_message::MJTC_setMessage(esc_html(__('Erase data request withdrawn', 'majestic-support')), 'updated');
            } else {
                MJTC_includer::MJTC_getModel('systemerror')->addSystemError(); // if there is an error add it to system errorrs
                MJTC_message::MJTC_setMessage(esc_html(__('Failed while performing action', 'majestic-support')), 'error');
            }
        }
        return;
    }

    function checkCanDelete($id){
        if(!is_numeric($id)) return false;
        if(current_user_can('manage_options')){ // allow admin to delete ??
            return true;
        }

        $uid = MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid();
        $query = "SELECT uid FROM `" . majesticsupport::$_db->prefix . "mjtc_support_erasedatarequests` WHERE id = ".esc_sql($id);
        $db_uid = majesticsupport::$_db->get_var($query);
        if( $db_uid == $uid){
            return true;
        }else{
            return false;
        }
    }

    private function getUserDetailReportByUserId( $uid = 0){
        $curdate = MJTC_request::MJTC_getVar('date_start', 'get');
        $fromdate = MJTC_request::MJTC_getVar('date_end', 'get');
        if($uid == 0 || $uid == ''){
            $id = MJTC_request::MJTC_getVar('uid', 'get');
        }else{
            $id = $uid;
            $query = "SELECT created FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE uid = ".esc_sql($id) ." ORDER BY created ASC LIMIT 1";
            $curdate = majesticsupport::$_db->get_var($query);

            $fromdate = date_i18n('Y-m-d h:i:s');
        }

        if( empty($curdate) OR empty($fromdate))
            return null;
        if(! is_numeric($id))
            return null;

        $result['curdate'] = $curdate;
        $result['fromdate'] = $fromdate;
        $result['id'] = $id;

        //Query to get Data
        $query = "SELECT created FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE status = 1 AND (lastreply = '0000-00-00 00:00:00' OR lastreply = '') AND created >= '" . esc_sql($curdate) . "' AND created <= '" . esc_sql($fromdate) . "'";
        if($id) $query .= " AND uid = ".esc_sql($id);
        $result['openticket'] = majesticsupport::$_db->get_results($query);

        $query = "SELECT created FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE status = 5 AND created >= '" . esc_sql($curdate) . "' AND created <= '" . esc_sql($fromdate) . "'";
        if($id) $query .= " AND uid = ".esc_sql($id);
        $result['closeticket'] = majesticsupport::$_db->get_results($query);

        $query = "SELECT created FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE isanswered = 1 AND status != 5 AND status != 1 AND created >= '" . esc_sql($curdate) . "' AND created <= '" . esc_sql($fromdate) . "'";
        if($id) $query .= " AND uid = ".esc_sql($id);
        $result['answeredticket'] = majesticsupport::$_db->get_results($query);

        $query = "SELECT created FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE isoverdue = 1 AND status != 5 AND created >= '" . esc_sql($curdate) . "' AND created <= '" . esc_sql($fromdate) . "'";
        if($id) $query .= " AND uid = ".esc_sql($id);
        $result['overdueticket'] = majesticsupport::$_db->get_results($query);

        $query = "SELECT created FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE isanswered != 1 AND status != 5 AND (lastreply IS NOT NULL AND lastreply != '0000-00-00 00:00:00' AND lastreply != '') AND created >= '" . esc_sql($curdate) . "' AND created <= '" . esc_sql($fromdate) . "'";
        if($id) $query .= " AND uid = ".esc_sql($id);
        $result['pendingticket'] = majesticsupport::$_db->get_results($query);
        //user detail
        $query = "SELECT user.display_name,user.user_email,user.user_nicename,user.id,
                    (SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE status = 1  AND (lastreply = '0000-00-00 00:00:00' OR lastreply = '') AND created >= '" . esc_sql($curdate) . "' AND created <= '" . esc_sql($fromdate) . "' AND uid = user.id) AS openticket,
                    (SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE status = 5 AND created >= '" . esc_sql($curdate) . "' AND created <= '" . esc_sql($fromdate) . "' AND uid = user.id) AS closeticket,
                    (SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE isanswered = 1 AND status != 5 AND status != 1 AND created >= '" . esc_sql($curdate) . "' AND created <= '" . esc_sql($fromdate) . "' AND uid = user.id) AS answeredticket,
                    (SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE isoverdue = 1 AND status != 5 AND created >= '" . esc_sql($curdate) . "' AND created <= '" . esc_sql($fromdate) . "' AND uid = user.id) AS overdueticket,
                    (SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE isanswered != 1 AND status != 5 AND isoverdue = 1 AND (lastreply IS NOT NULL AND lastreply != '0000-00-00 00:00:00' AND lastreply != '') AND created >= '" . esc_sql($curdate) . "' AND created <= '" . esc_sql($fromdate) . "' AND uid = user.id) AS pendingticket
                    FROM `".majesticsupport::$_wpprefixforuser."mjtc_support_users` AS user
                    WHERE user.id = ".esc_sql($id);
        $user = majesticsupport::$_db->get_row($query);
        $result['users'] = $user;
        //Tickets
        do_action('msFeedbackQueryStaff');// to prepare any addon based query
        $query = "SELECT ticket.*,priority.priority, priority.prioritycolour,status.status AS statustitle ". majesticsupport::$_addon_query['select'] ."
                    FROM `".majesticsupport::$_db->prefix."mjtc_support_tickets` AS ticket
                    LEFT JOIN `".majesticsupport::$_db->prefix."mjtc_support_priorities` AS priority ON priority.id = ticket.priorityid
                    JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_statuses` AS status ON ticket.status = status.id
                    ". majesticsupport::$_addon_query['join'] . "
                    WHERE uid = ".esc_sql($id)." AND ticket.created >= '" . esc_sql($curdate) . "' AND ticket.created <= '" . esc_sql($fromdate) . "' ";

        $result['tickets'] = majesticsupport::$_db->get_results($query);


        do_action('reset_ms_aadon_query');
        if(in_array('timetracking', majesticsupport::$_active_addons)){
            foreach ($result['tickets'] as $MJTC_ticket) {
                 $MJTC_ticket->time = MJTC_includer::MJTC_getModel('timetracking')->getTimeTakenByTicketId($MJTC_ticket->id);
            }
        }

        return $result;
    }

    function setUserExportByuid($uid = 0){
        $tb = "\t";
        $nl = "\n";
        $result = $this->getUserDetailReportByUserId($uid);

        if(empty($result))
            return '';

        $fromdate = date_i18n('Y-m-d',MJTC_majesticsupportphplib::MJTC_strtotime($result['curdate']));
        $todate = date_i18n('Y-m-d',MJTC_majesticsupportphplib::MJTC_strtotime($result['fromdate']));

        $MJTC_data = esc_html(__('User Report', 'majestic-support')).' '. esc_html(__('From', 'majestic-support')).' '.esc_attr($fromdate).' - '.esc_attr($todate).esc_attr($nl).esc_attr($nl);

        // By 1 month
        $MJTC_data .= esc_html(__('Ticket status by days', 'majestic-support')).$nl.$nl;
        $MJTC_data .= esc_html(__('Date', 'majestic-support')).$tb. esc_html(__('New', 'majestic-support')).$tb. esc_html(__('Answered', 'majestic-support')).$tb. esc_html(__('Closed', 'majestic-support')).$tb. esc_html(__('Pending', 'majestic-support')).$tb. esc_html(__('Overdue', 'majestic-support')).$nl;
        while (MJTC_majesticsupportphplib::MJTC_strtotime($fromdate) <= MJTC_majesticsupportphplib::MJTC_strtotime($todate)) {
            $openticket = 0;
            $closeticket = 0;
            $answeredticket = 0;
            $overdueticket = 0;
            $pendingticket = 0;
            foreach ($result['openticket'] as $MJTC_ticket) {
                $MJTC_ticket_date = date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created));
                if($MJTC_ticket_date == $fromdate)
                    $openticket += 1;
            }
            foreach ($result['closeticket'] as $MJTC_ticket) {
                $MJTC_ticket_date = date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created));
                if($MJTC_ticket_date == $fromdate)
                    $closeticket += 1;
            }
            foreach ($result['answeredticket'] as $MJTC_ticket) {
                $MJTC_ticket_date = date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created));
                if($MJTC_ticket_date == $fromdate)
                    $answeredticket += 1;
            }
            foreach ($result['overdueticket'] as $MJTC_ticket) {
                $MJTC_ticket_date = date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created));
                if($MJTC_ticket_date == $fromdate)
                    $overdueticket += 1;
            }
            foreach ($result['pendingticket'] as $MJTC_ticket) {
                $MJTC_ticket_date = date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created));
                if($MJTC_ticket_date == $fromdate)
                    $pendingticket += 1;
            }
            $MJTC_data .= '"'.esc_attr($fromdate).'"'.esc_attr($tb).'"'.esc_attr($openticket).'"'.esc_attr($tb).'"'.esc_attr($answeredticket).'"'.esc_attr($tb).'"'.esc_attr($closeticket).'"'.esc_attr($tb).'"'.esc_attr($pendingticket).'"'.esc_attr($tb).'"'.esc_attr($overdueticket).'"'.esc_attr($nl);
            $fromdate = date_i18n("Y-m-d", MJTC_majesticsupportphplib::MJTC_strtotime("+1 day", MJTC_majesticsupportphplib::MJTC_strtotime($fromdate)));
        }
        $MJTC_data .= $nl.$nl.$nl;
        // END By 1 month

        // by staffs
        $MJTC_data .= esc_html(__('Users Tickets', 'majestic-support')).$nl.$nl;
        if(!empty($result['users'])){
            $MJTC_data .= esc_html(__('Name', 'majestic-support')).$tb. esc_html(__('Username', 'majestic-support')).$tb. esc_html(__('Email', 'majestic-support')).$tb. esc_html(__('New', 'majestic-support')).$tb. esc_html(__('Answered', 'majestic-support')).$tb. esc_html(__('Closed', 'majestic-support')).$tb. esc_html(__('Pending', 'majestic-support')).$tb. esc_html(__('Overdue', 'majestic-support')).$nl;
            $MJTC_key = $result['users'];
            $agentname = $MJTC_key->display_name;
            $username = $MJTC_key->user_nicename;
            $email = $MJTC_key->user_email;

            $MJTC_data .= '"'.$agentname.'"'.$tb.'"'.$username.'"'.$tb.'"'.$email.'"'.$tb.'"'.$MJTC_key->openticket.'"'.$tb.'"'.$MJTC_key->answeredticket.'"'.$tb.'"'.$MJTC_key->closeticket.'"'.$tb.'"'.$MJTC_key->pendingticket.'"'.$tb.'"'.$MJTC_key->overdueticket.'"'.$nl;

            $MJTC_data .= $nl.$nl.$nl;
        }

        // by priorits tickets
        $MJTC_data .= esc_html(__('Tickets', 'majestic-support')).$nl.$nl;
        if(!empty($result['tickets'])){
            $MJTC_data .= esc_html(__('Subject', 'majestic-support')).$tb. esc_html(__('Status', 'majestic-support')).$tb. esc_html(__('Priority', 'majestic-support')).$tb. esc_html(__('Created', 'majestic-support'));

             if(in_array('feedback', majesticsupport::$_active_addons)){
                $MJTC_data .= $tb. esc_html(__('Rating', 'majestic-support'));
            }
            if(in_array('timetracking', majesticsupport::$_active_addons)){
                $MJTC_data .= $tb. esc_html(__('Time', 'majestic-support'));
            }
            $MJTC_data .= $nl;
            $status = '';
            foreach ($result['tickets'] as $MJTC_ticket) {
                if(in_array('timetracking', majesticsupport::$_active_addons)){
                    $hours = floor($MJTC_ticket->time / 3600);
                    $mins = floor($MJTC_ticket->time / 60);
                    $mins = floor($mins % 60);
                    $secs = floor($MJTC_ticket->time % 60);
                    $time = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
                }
                /*switch($MJTC_ticket->status){
                    case 0:
                        $status = esc_html(__('New','majestic-support'));
                        if($MJTC_ticket->isoverdue == 1)
                            $status = esc_html(__('Overdue','majestic-support'));
                    break;
                    case 1:
                        $status = esc_html(__('Pending','majestic-support'));
                        if($MJTC_ticket->isoverdue == 1)
                            $status = esc_html(__('Overdue','majestic-support'));
                    break;
                    case 2:
                        $status = esc_html(__('In Progress','majestic-support'));
                        if($MJTC_ticket->isoverdue == 1)
                            $status = esc_html(__('Overdue','majestic-support'));
                    break;
                    case 3:
                        $status = esc_html(__('Answered','majestic-support'));
                        if($MJTC_ticket->isoverdue == 1)
                            $status = esc_html(__('Overdue','majestic-support'));
                    break;
                    case 4:
                        $status = esc_html(__('Closed','majestic-support'));
                    break;
                    case 5:
                        $status = esc_html(__('Merged','majestic-support'));
                    break;
                }*/
                if (!in_array($MJTC_ticket->status, [5, 6]) && $MJTC_ticket->isoverdue == 1) {
                    $status = __('Overdue', 'majestic-support');
                } else {
                    $status = $MJTC_ticket->statustitle;
                }
                $created = date_i18n('Y-m-d',MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created));
                $MJTC_data .= '"'.$MJTC_ticket->subject.'"'.$tb.'"'.$status.'"'.$tb.'"'.esc_attr(majesticsupport::MJTC_getVarValue($MJTC_ticket->priority)).'"'.$tb.'"'.$created.'"';

                if(in_array('feedback', majesticsupport::$_active_addons)){
                    $MJTC_data .= $tb.'"'.$MJTC_ticket->rating.'"';
                }
                if(in_array('timetracking', majesticsupport::$_active_addons)){
                    $MJTC_data .= $tb.'"'.$time.'"';
                }
                $MJTC_data .= $nl;
            }
            $MJTC_data .= $nl.$nl.$nl;
        }
        return $MJTC_data;
    }

    function anonymizeUserData($uid){
        if(!is_numeric($uid) || $uid == 0){
            return false;
        }
        $query = "SELECT id FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE uid = ".esc_sql($uid);
        $uids = majesticsupport::$_db->get_results($query);

        foreach ($uids as $MJTC_ticket) { // erase tickets data
            // ticket data
            $row = MJTC_includer::MJTC_getTable('tickets');
            $row->update(array('id' => $MJTC_ticket->id, 'email'=>'---', 'subject'=>'---', 'message'=>'---', 'phone'=>'', 'phoneext'=>'', 'params' => ''));

            // erase replies data
            $query = "SELECT replies.id AS replyid
                        FROM `" . majesticsupport::$_db->prefix . "mjtc_support_replies` AS replies
                        WHERE replies.ticketid = ".esc_sql($MJTC_ticket->id);
            $replies = majesticsupport::$_db->get_results($query);
            foreach ($replies as $reply) {
                $row = MJTC_includer::MJTC_getTable('replies');
                $row->update(array('id' => $reply->replyid, 'message' => '---'));
            }

            // erase internal note data
            if(in_array('note', majesticsupport::$_active_addons)){
                $query = "SELECT notes.id AS noteid
                            FROM `" . majesticsupport::$_db->prefix . "mjtc_support_notes` AS notes
                            WHERE notes.ticketid = ".esc_sql($MJTC_ticket->id);
                $notes = majesticsupport::$_db->get_results($query);
                foreach ($notes as $note) {
                    $row = MJTC_includer::MJTC_getTable('note');
                    $row->update(array('id' => $note->noteid, 'title' => '---', 'note' => '---'));
                }
            }
            //activity log for ticket
            if(in_array('tickethistory', majesticsupport::$_active_addons)){
                $query = "DELETE
                        FROM `" . majesticsupport::$_db->prefix . "mjtc_support_activity_log`
                        WHERE eventfor = 1 AND referenceid = ".esc_sql($MJTC_ticket->id);
                majesticsupport::$_db->query($query);

            }
            // private credentails for ticket
            if(in_array('privatecredentials',majesticsupport::$_active_addons)){
                MJTC_includer::MJTC_getModel('privatecredentials')->deleteCredentialsOnCloseTicket($MJTC_ticket->id);
            }
            // ticket attachments.
            $MJTC_datadirectory = majesticsupport::$_config['data_directory'];
            $maindir = wp_upload_dir();
            $mainpath = $maindir['basedir'];
            $mainpath = $mainpath .'/'.$MJTC_datadirectory;
            $mainpath = $mainpath . '/attachmentdata';
            $query = "SELECT ticket.attachmentdir
                        FROM `".majesticsupport::$_db->prefix."mjtc_support_tickets` AS ticket
                        WHERE ticket.id = ".esc_sql($MJTC_ticket->id);
            $foldername = majesticsupport::$_db->get_var($query);
            if(!empty($foldername)){
                $folder = $mainpath . '/ticket/'.$foldername;
                if(file_exists($folder)){
                    $path = $mainpath . '/ticket/'.$foldername.'/*.*';
                    $files = glob($path);
                    array_map('unlink', $files);//deleting files
                    rmdir($folder);
                }
            }
            $query = "DELETE FROM `".majesticsupport::$_db->prefix."mjtc_support_attachments` WHERE ticketid = " . esc_sql($MJTC_ticket->id);
            majesticsupport::$_db->query($query);
        }
        $query = "UPDATE `" . majesticsupport::$_db->prefix . "mjtc_support_erasedatarequests` SET status = 2 WHERE uid = ".esc_sql($uid);
        majesticsupport::$_db->query($query);
        MJTC_message::MJTC_setMessage(esc_html(__('User identifying data erased', 'majestic-support')), 'updated');
        $user_data = get_user_by('ID',$uid);
        $email = $user_data->user_email;
        $name = $user_data->display_name;
        majesticsupport::$_data['mail_data']['email'] = $email;
        majesticsupport::$_data['mail_data']['name'] = $name;
        MJTC_includer::MJTC_getModel('email')->sendMail(4, 1); // Mailfor, Delete Ticket
        return;
    }

    function deleteUserData($uid){
        if(!is_numeric($uid) || $uid == 0){
            return false;
        }
        $query = "SELECT id FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE uid = ".esc_sql($uid);
        $uids = majesticsupport::$_db->get_results($query);

        foreach ($uids as $MJTC_ticket) { // erase tickets data
            // ticket data

            $row = MJTC_includer::MJTC_getTable('tickets');
            $row->delete($MJTC_ticket->id);

            if(in_array('note', majesticsupport::$_active_addons)){
                // delete internal notes
                MJTC_includer::MJTC_getModel('note')->removeTicketInternalNote($MJTC_ticket->id);
            }
            // delete replies
            MJTC_includer::MJTC_getModel('reply')->removeTicketReplies($MJTC_ticket->id);

            // private credentails for ticket
            if(in_array('privatecredentials',majesticsupport::$_active_addons)){
                MJTC_includer::MJTC_getModel('privatecredentials')->deleteCredentialsOnCloseTicket($MJTC_ticket->id);
            }
            // ticket attachments.
            $MJTC_datadirectory = majesticsupport::$_config['data_directory'];
            $maindir = wp_upload_dir();
            $mainpath = $maindir['basedir'];
            $mainpath = $mainpath .'/'.$MJTC_datadirectory;
            $mainpath = $mainpath . '/attachmentdata';
            $query = "SELECT ticket.attachmentdir
                        FROM `".majesticsupport::$_db->prefix."mjtc_support_tickets` AS ticket
                        WHERE ticket.id = ".esc_sql($MJTC_ticket->id);
            $foldername = majesticsupport::$_db->get_var($query);
            if(!empty($foldername)){
                $folder = $mainpath . '/ticket/'.$foldername;
                if(file_exists($folder)){
                    $path = $mainpath . '/ticket/'.$foldername.'/*.*';
                    $files = glob($path);
                    array_map('unlink', $files);//deleting files
                    rmdir($folder);
                }
            }
            $query = "DELETE FROM `".majesticsupport::$_db->prefix."mjtc_support_attachments` WHERE ticketid = ".esc_sql($MJTC_ticket->id);
            majesticsupport::$_db->query($query);
        }
        $query = "UPDATE `" . majesticsupport::$_db->prefix . "mjtc_support_erasedatarequests` SET status = 3 WHERE uid = " . esc_sql($uid);
        majesticsupport::$_db->query($query);

        $user_data = get_user_by('ID',$uid);

        MJTC_message::MJTC_setMessage(esc_html(__('User data Deleted', 'majestic-support')), 'updated');
        $user_data = get_user_by('ID',$uid);
        $email = $user_data->user_email;
        $name = $user_data->display_name;
        majesticsupport::$_data['mail_data']['email'] = $email;
        majesticsupport::$_data['mail_data']['name'] = $name;
        MJTC_includer::MJTC_getModel('email')->sendMail(4, 1); // Mailfor, Delete Ticket
    }

    function getAdminSearchFormDataGDPR(){
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $nonce, 'erase-data-requests') ) {
            die( 'Security check Failed' );
        }
        $ms_search_array = array();
        $email = MJTC_request::MJTC_getVar('email');
        if ($email != '') {
            $ms_search_array['email'] = MJTC_majesticsupportphplib::MJTC_addslashes(MJTC_majesticsupportphplib::MJTC_trim($email));
        } else {
            $ms_search_array['email'] = '';
        }
        $ms_search_array['search_from_gdpr'] = 1;
        return $ms_search_array;
    }
}
?>
