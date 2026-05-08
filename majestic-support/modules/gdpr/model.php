<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_gdprModel {

	function getGDPRFeilds(){
		$MJTC_query = "SELECT * FROM `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` WHERE fieldfor = 3 ORDER BY ordering ";
		majesticsupport::$_data[0] = majesticsupport::$_db->get_results($MJTC_query);
		if (majesticsupport::$_db->last_error != null) {
		    MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
		}
	}

	function getEraseDataRequests(){
		$MJTC_query = "SELECT * FROM `" . majesticsupport::$_db->prefix . "mjtc_support_erasedatarequests`";
		majesticsupport::$_data[0] = majesticsupport::$_db->get_results($MJTC_query);
		if (majesticsupport::$_db->last_error != null) {
		    MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
		}

        $MJTC_email = majesticsupport::$_search['gdpr']['email'];
        $MJTC_email = majesticsupport::parseSpaces($MJTC_email);
        $MJTC_inquery = '';
        if ($MJTC_email != null)
            $MJTC_inquery .= " WHERE user.user_email LIKE '%".esc_sql($MJTC_email)."%'";

        majesticsupport::$_data['filter']['email'] = $MJTC_email;

        // Pagination
        $MJTC_query = "SELECT COUNT(request.id)
                    FROM `" . majesticsupport::$_db->prefix . "mjtc_support_erasedatarequests` AS request
                    LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_users` AS user ON user.id = request.uid
                    ";
        $MJTC_query .= $MJTC_inquery;
        $total = majesticsupport::$_db->get_var($MJTC_query);
        majesticsupport::$_data['total'] = $total;
        majesticsupport::$_data[1] = MJTC_pagination::MJTC_getPagination($total);

        // Data
        $MJTC_query = "SELECT request.*, user.user_email
                    FROM `" . majesticsupport::$_db->prefix . "mjtc_support_erasedatarequests` AS request
                    LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_users` AS user ON user.id = request.uid
                    ";
        $MJTC_query .= $MJTC_inquery;
        $MJTC_query .= " ORDER BY request.created DESC LIMIT " . MJTC_pagination::MJTC_getOffset() . ", " . MJTC_pagination::MJTC_getLimit();
        majesticsupport::$_data[0] = majesticsupport::$_db->get_results($MJTC_query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
	}

    function getUserEraseDataRequest(){
        $MJTC_uid = MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid();
        if($MJTC_uid == 0){
            return;
        }
        $MJTC_query = "SELECT * FROM `" . majesticsupport::$_db->prefix . "mjtc_support_erasedatarequests` WHERE uid = ".esc_sql($MJTC_uid);
        majesticsupport::$_data[0] = majesticsupport::$_db->get_row($MJTC_query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
    }

    function storeUserEraseRequest($MJTC_data){
        $MJTC_nonce_id = isset($MJTC_data['id']) ? $MJTC_data['id'] : '';
    	$MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'save-usereraserequest-'.$MJTC_nonce_id) ) {
            die( 'Security check Failed' );
        }
        if (!$MJTC_data['id']) { //new
    	    $MJTC_data['created'] = date_i18n('Y-m-d H:i:s');
            $MJTC_data['uid'] = MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid();
            $MJTC_data['status'] = 1;
    	}
    	$MJTC_data = majesticsupport::MJTC_sanitizeData($MJTC_data); // MJTC_sanitizeData() function uses wordpress santize functions
    	$MJTC_data['message'] = MJTC_includer::MJTC_getModel('majesticsupport')->getSanitizedEditorData($_POST['message']);// MJTC_sanitizeData() function uses wordpress santize functions
    	$MJTC_row = MJTC_includer::MJTC_getTable('erasedatarequests');
    	$MJTC_data = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($MJTC_data);// remove slashes with quotes.
    	$MJTC_error = 0;
    	if (!$MJTC_row->bind($MJTC_data)) {
            $MJTC_error = 1;
    	}
    	if (!$MJTC_row->store()) {
            $MJTC_error = 1;
    	}

    	if ($MJTC_error == 0) {
    	    MJTC_message::MJTC_setMessage(esc_html(__('Erasing data request has been stored', 'majestic-support')), 'updated');
    	} else {
    	    MJTC_includer::MJTC_getModel('systemerror')->addSystemError(); // if there is an error add it to system errorrs
    	    MJTC_message::MJTC_setMessage(esc_html(__('Failed while storing', 'majestic-support')), 'error');
    	}
        return;
    }

    function deleteUserEraseRequest($MJTC_id){
        if(!is_numeric($MJTC_id)){
            return false;
        }
        if($this->checkCanDelete($MJTC_id)){
            $MJTC_row = MJTC_includer::MJTC_getTable('erasedatarequests');
            if ($MJTC_row->delete($MJTC_id)) {
                MJTC_message::MJTC_setMessage(esc_html(__('Erase data request withdrawn', 'majestic-support')), 'updated');
            } else {
                MJTC_includer::MJTC_getModel('systemerror')->addSystemError(); // if there is an error add it to system errorrs
                MJTC_message::MJTC_setMessage(esc_html(__('Failed while performing action', 'majestic-support')), 'error');
            }
        }
        return;
    }

    function checkCanDelete($MJTC_id){
        if(!is_numeric($MJTC_id)) return false;
        if(current_user_can('manage_options')){ // allow admin to delete ??
            return true;
        }

        $MJTC_uid = MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid();
        $MJTC_query = "SELECT uid FROM `" . majesticsupport::$_db->prefix . "mjtc_support_erasedatarequests` WHERE id = ".esc_sql($MJTC_id);
        $MJTC_db_uid = majesticsupport::$_db->get_var($MJTC_query);
        if( $MJTC_db_uid == $MJTC_uid){
            return true;
        }else{
            return false;
        }
    }

    private function getUserDetailReportByUserId( $MJTC_uid = 0){
        $MJTC_curdate = MJTC_request::MJTC_getVar('date_start', 'get');
        $MJTC_fromdate = MJTC_request::MJTC_getVar('date_end', 'get');
        if($MJTC_uid == 0 || $MJTC_uid == ''){
            $MJTC_id = MJTC_request::MJTC_getVar('uid', 'get');
        }else{
            $MJTC_id = $MJTC_uid;
            $MJTC_query = "SELECT created FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE uid = ".esc_sql($MJTC_id) ." ORDER BY created ASC LIMIT 1";
            $MJTC_curdate = majesticsupport::$_db->get_var($MJTC_query);

            $MJTC_fromdate = date_i18n('Y-m-d h:i:s');
        }

        if( empty($MJTC_curdate) OR empty($MJTC_fromdate))
            return null;
        if(! is_numeric($MJTC_id))
            return null;

        $MJTC_result['curdate'] = $MJTC_curdate;
        $MJTC_result['fromdate'] = $MJTC_fromdate;
        $MJTC_result['id'] = $MJTC_id;

        //Query to get Data
        $MJTC_query = "SELECT created FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE status = 1 AND (lastreply = '0000-00-00 00:00:00' OR lastreply = '') AND created >= '" . esc_sql($MJTC_curdate) . "' AND created <= '" . esc_sql($MJTC_fromdate) . "'";
        if($MJTC_id) $MJTC_query .= " AND uid = ".esc_sql($MJTC_id);
        $MJTC_result['openticket'] = majesticsupport::$_db->get_results($MJTC_query);

        $MJTC_query = "SELECT created FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE status = 5 AND created >= '" . esc_sql($MJTC_curdate) . "' AND created <= '" . esc_sql($MJTC_fromdate) . "'";
        if($MJTC_id) $MJTC_query .= " AND uid = ".esc_sql($MJTC_id);
        $MJTC_result['closeticket'] = majesticsupport::$_db->get_results($MJTC_query);

        $MJTC_query = "SELECT created FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE isanswered = 1 AND status != 5 AND status != 1 AND created >= '" . esc_sql($MJTC_curdate) . "' AND created <= '" . esc_sql($MJTC_fromdate) . "'";
        if($MJTC_id) $MJTC_query .= " AND uid = ".esc_sql($MJTC_id);
        $MJTC_result['answeredticket'] = majesticsupport::$_db->get_results($MJTC_query);

        $MJTC_query = "SELECT created FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE isoverdue = 1 AND status != 5 AND created >= '" . esc_sql($MJTC_curdate) . "' AND created <= '" . esc_sql($MJTC_fromdate) . "'";
        if($MJTC_id) $MJTC_query .= " AND uid = ".esc_sql($MJTC_id);
        $MJTC_result['overdueticket'] = majesticsupport::$_db->get_results($MJTC_query);

        $MJTC_query = "SELECT created FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE isanswered != 1 AND status != 5 AND (lastreply IS NOT NULL AND lastreply != '0000-00-00 00:00:00' AND lastreply != '') AND created >= '" . esc_sql($MJTC_curdate) . "' AND created <= '" . esc_sql($MJTC_fromdate) . "'";
        if($MJTC_id) $MJTC_query .= " AND uid = ".esc_sql($MJTC_id);
        $MJTC_result['pendingticket'] = majesticsupport::$_db->get_results($MJTC_query);
        //user detail
        $MJTC_query = "SELECT user.display_name,user.user_email,user.user_nicename,user.id,
                    (SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE status = 1  AND (lastreply = '0000-00-00 00:00:00' OR lastreply = '') AND created >= '" . esc_sql($MJTC_curdate) . "' AND created <= '" . esc_sql($MJTC_fromdate) . "' AND uid = user.id) AS openticket,
                    (SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE status = 5 AND created >= '" . esc_sql($MJTC_curdate) . "' AND created <= '" . esc_sql($MJTC_fromdate) . "' AND uid = user.id) AS closeticket,
                    (SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE isanswered = 1 AND status != 5 AND status != 1 AND created >= '" . esc_sql($MJTC_curdate) . "' AND created <= '" . esc_sql($MJTC_fromdate) . "' AND uid = user.id) AS answeredticket,
                    (SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE isoverdue = 1 AND status != 5 AND created >= '" . esc_sql($MJTC_curdate) . "' AND created <= '" . esc_sql($MJTC_fromdate) . "' AND uid = user.id) AS overdueticket,
                    (SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE isanswered != 1 AND status != 5 AND isoverdue = 1 AND (lastreply IS NOT NULL AND lastreply != '0000-00-00 00:00:00' AND lastreply != '') AND created >= '" . esc_sql($MJTC_curdate) . "' AND created <= '" . esc_sql($MJTC_fromdate) . "' AND uid = user.id) AS pendingticket
                    FROM `".majesticsupport::$_wpprefixforuser."mjtc_support_users` AS user
                    WHERE user.id = ".esc_sql($MJTC_id);
        $MJTC_user = majesticsupport::$_db->get_row($MJTC_query);
        $MJTC_result['users'] = $MJTC_user;
        //Tickets
        do_action('MJTC_FeedbackQueryStaff');// to prepare any addon based query
        $MJTC_query = "SELECT ticket.*,priority.priority, priority.prioritycolour,status.status AS statustitle ". majesticsupport::$_addon_query['select'] ."
                    FROM `".majesticsupport::$_db->prefix."mjtc_support_tickets` AS ticket
                    LEFT JOIN `".majesticsupport::$_db->prefix."mjtc_support_priorities` AS priority ON priority.id = ticket.priorityid
                    JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_statuses` AS status ON ticket.status = status.id
                    ". majesticsupport::$_addon_query['join'] . "
                    WHERE uid = ".esc_sql($MJTC_id)." AND ticket.created >= '" . esc_sql($MJTC_curdate) . "' AND ticket.created <= '" . esc_sql($MJTC_fromdate) . "' ";

        $MJTC_result['tickets'] = majesticsupport::$_db->get_results($MJTC_query);


        do_action('MJTC_reset_addon_query');
        if(in_array('timetracking', majesticsupport::$_active_addons)){
            foreach ($MJTC_result['tickets'] as $MJTC_ticket) {
                 $MJTC_ticket->time = MJTC_includer::MJTC_getModel('timetracking')->getTimeTakenByTicketId($MJTC_ticket->id);
            }
        }

        return $MJTC_result;
    }

    function setUserExportByuid($MJTC_uid = 0){
        $tb = "\t";
        $MJTC_nl = "\n";
        $MJTC_result = $this->getUserDetailReportByUserId($MJTC_uid);

        if(empty($MJTC_result))
            return '';

        $MJTC_fromdate = date_i18n('Y-m-d',MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_result['curdate']));
        $todate = date_i18n('Y-m-d',MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_result['fromdate']));

        $MJTC_data = esc_html(__('User Report', 'majestic-support')).' '. esc_html(__('From', 'majestic-support')).' '.esc_attr($MJTC_fromdate).' - '.esc_attr($todate).esc_attr($MJTC_nl).esc_attr($MJTC_nl);

        // By 1 month
        $MJTC_data .= esc_html(__('Ticket status by days', 'majestic-support')).$MJTC_nl.$MJTC_nl;
        $MJTC_data .= esc_html(__('Date', 'majestic-support')).$tb. esc_html(__('New', 'majestic-support')).$tb. esc_html(__('Answered', 'majestic-support')).$tb. esc_html(__('Closed', 'majestic-support')).$tb. esc_html(__('Pending', 'majestic-support')).$tb. esc_html(__('Overdue', 'majestic-support')).$MJTC_nl;
        while (MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_fromdate) <= MJTC_majesticsupportphplib::MJTC_strtotime($todate)) {
            $MJTC_openticket = 0;
            $MJTC_closeticket = 0;
            $MJTC_answeredticket = 0;
            $MJTC_overdueticket = 0;
            $MJTC_pendingticket = 0;
            foreach ($MJTC_result['openticket'] as $MJTC_ticket) {
                $MJTC_ticket_date = date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created));
                if($MJTC_ticket_date == $MJTC_fromdate)
                    $MJTC_openticket += 1;
            }
            foreach ($MJTC_result['closeticket'] as $MJTC_ticket) {
                $MJTC_ticket_date = date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created));
                if($MJTC_ticket_date == $MJTC_fromdate)
                    $MJTC_closeticket += 1;
            }
            foreach ($MJTC_result['answeredticket'] as $MJTC_ticket) {
                $MJTC_ticket_date = date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created));
                if($MJTC_ticket_date == $MJTC_fromdate)
                    $MJTC_answeredticket += 1;
            }
            foreach ($MJTC_result['overdueticket'] as $MJTC_ticket) {
                $MJTC_ticket_date = date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created));
                if($MJTC_ticket_date == $MJTC_fromdate)
                    $MJTC_overdueticket += 1;
            }
            foreach ($MJTC_result['pendingticket'] as $MJTC_ticket) {
                $MJTC_ticket_date = date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created));
                if($MJTC_ticket_date == $MJTC_fromdate)
                    $MJTC_pendingticket += 1;
            }
            $MJTC_data .= '"'.esc_attr($MJTC_fromdate).'"'.esc_attr($tb).'"'.esc_attr($MJTC_openticket).'"'.esc_attr($tb).'"'.esc_attr($MJTC_answeredticket).'"'.esc_attr($tb).'"'.esc_attr($MJTC_closeticket).'"'.esc_attr($tb).'"'.esc_attr($MJTC_pendingticket).'"'.esc_attr($tb).'"'.esc_attr($MJTC_overdueticket).'"'.esc_attr($MJTC_nl);
            $MJTC_fromdate = date_i18n("Y-m-d", MJTC_majesticsupportphplib::MJTC_strtotime("+1 day", MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_fromdate)));
        }
        $MJTC_data .= $MJTC_nl.$MJTC_nl.$MJTC_nl;
        // END By 1 month

        // by staffs
        $MJTC_data .= esc_html(__('Users Tickets', 'majestic-support')).$MJTC_nl.$MJTC_nl;
        if(!empty($MJTC_result['users'])){
            $MJTC_data .= esc_html(__('Name', 'majestic-support')).$tb. esc_html(__('Username', 'majestic-support')).$tb. esc_html(__('Email', 'majestic-support')).$tb. esc_html(__('New', 'majestic-support')).$tb. esc_html(__('Answered', 'majestic-support')).$tb. esc_html(__('Closed', 'majestic-support')).$tb. esc_html(__('Pending', 'majestic-support')).$tb. esc_html(__('Overdue', 'majestic-support')).$MJTC_nl;
            $MJTC_key = $MJTC_result['users'];
            $MJTC_agentname = $MJTC_key->display_name;
            $MJTC_username = $MJTC_key->user_nicename;
            $MJTC_email = $MJTC_key->user_email;

            $MJTC_data .= '"'.$MJTC_agentname.'"'.$tb.'"'.$MJTC_username.'"'.$tb.'"'.$MJTC_email.'"'.$tb.'"'.$MJTC_key->openticket.'"'.$tb.'"'.$MJTC_key->answeredticket.'"'.$tb.'"'.$MJTC_key->closeticket.'"'.$tb.'"'.$MJTC_key->pendingticket.'"'.$tb.'"'.$MJTC_key->overdueticket.'"'.$MJTC_nl;

            $MJTC_data .= $MJTC_nl.$MJTC_nl.$MJTC_nl;
        }

        // by priorits tickets
        $MJTC_data .= esc_html(__('Tickets', 'majestic-support')).$MJTC_nl.$MJTC_nl;
        if(!empty($MJTC_result['tickets'])){
            $MJTC_data .= esc_html(__('Subject', 'majestic-support')).$tb. esc_html(__('Status', 'majestic-support')).$tb. esc_html(__('Priority', 'majestic-support')).$tb. esc_html(__('Created', 'majestic-support'));

             if(in_array('feedback', majesticsupport::$_active_addons)){
                $MJTC_data .= $tb. esc_html(__('Rating', 'majestic-support'));
            }
            if(in_array('timetracking', majesticsupport::$_active_addons)){
                $MJTC_data .= $tb. esc_html(__('Time', 'majestic-support'));
            }
            $MJTC_data .= $MJTC_nl;
            $MJTC_status = '';
            foreach ($MJTC_result['tickets'] as $MJTC_ticket) {
                if(in_array('timetracking', majesticsupport::$_active_addons)){
                    $MJTC_hours = floor($MJTC_ticket->time / 3600);
                    $MJTC_mins = floor($MJTC_ticket->time / 60);
                    $MJTC_mins = floor($MJTC_mins % 60);
                    $MJTC_secs = floor($MJTC_ticket->time % 60);
                    $MJTC_time = esc_html(sprintf('%02d:%02d:%02d', $MJTC_hours, $MJTC_mins, $MJTC_secs));
                }
                /*switch($MJTC_ticket->status){
                    case 0:
                        $MJTC_status = esc_html(__('New','majestic-support'));
                        if($MJTC_ticket->isoverdue == 1)
                            $MJTC_status = esc_html(__('Overdue','majestic-support'));
                    break;
                    case 1:
                        $MJTC_status = esc_html(__('Pending','majestic-support'));
                        if($MJTC_ticket->isoverdue == 1)
                            $MJTC_status = esc_html(__('Overdue','majestic-support'));
                    break;
                    case 2:
                        $MJTC_status = esc_html(__('In Progress','majestic-support'));
                        if($MJTC_ticket->isoverdue == 1)
                            $MJTC_status = esc_html(__('Overdue','majestic-support'));
                    break;
                    case 3:
                        $MJTC_status = esc_html(__('Answered','majestic-support'));
                        if($MJTC_ticket->isoverdue == 1)
                            $MJTC_status = esc_html(__('Overdue','majestic-support'));
                    break;
                    case 4:
                        $MJTC_status = esc_html(__('Closed','majestic-support'));
                    break;
                    case 5:
                        $MJTC_status = esc_html(__('Merged','majestic-support'));
                    break;
                }*/
                if (!in_array($MJTC_ticket->status, [5, 6]) && $MJTC_ticket->isoverdue == 1) {
                    $MJTC_status = __('Overdue', 'majestic-support');
                } else {
                    $MJTC_status = $MJTC_ticket->statustitle;
                }
                $MJTC_created = date_i18n('Y-m-d',MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created));
                $MJTC_data .= '"'.$MJTC_ticket->subject.'"'.$tb.'"'.$MJTC_status.'"'.$tb.'"'.esc_attr(majesticsupport::MJTC_getVarValue($MJTC_ticket->priority)).'"'.$tb.'"'.$MJTC_created.'"';

                if(in_array('feedback', majesticsupport::$_active_addons)){
                    $MJTC_data .= $tb.'"'.$MJTC_ticket->rating.'"';
                }
                if(in_array('timetracking', majesticsupport::$_active_addons)){
                    $MJTC_data .= $tb.'"'.$MJTC_time.'"';
                }
                $MJTC_data .= $MJTC_nl;
            }
            $MJTC_data .= $MJTC_nl.$MJTC_nl.$MJTC_nl;
        }
        return $MJTC_data;
    }

    function anonymizeUserData($MJTC_uid){
        if(!is_numeric($MJTC_uid) || $MJTC_uid == 0){
            return false;
        }

        // --- START FILESYSTEM FIX ---
        global $wp_filesystem;
        if (!function_exists('wp_handle_upload')) {
            do_action('majesticsupport_load_wp_file');
        }
        if ( ! WP_Filesystem() ) {
            return false;
        }
        $MJTC_wp_filesystem = $wp_filesystem;
        // --- END FILESYSTEM FIX ---

        $MJTC_query = "SELECT id FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE uid = ".esc_sql($MJTC_uid);
        $MJTC_uids = majesticsupport::$_db->get_results($MJTC_query);

        foreach ($MJTC_uids as $MJTC_ticket) { // erase tickets data
            // ticket data
            $MJTC_row = MJTC_includer::MJTC_getTable('tickets');
            $MJTC_row->update(array('id' => $MJTC_ticket->id, 'email'=>'---', 'subject'=>'---', 'message'=>'---', 'phone'=>'', 'phoneext'=>'', 'params' => ''));

            // erase replies data
            $MJTC_query = "SELECT replies.id AS replyid
                        FROM `" . majesticsupport::$_db->prefix . "mjtc_support_replies` AS replies
                        WHERE replies.ticketid = ".esc_sql($MJTC_ticket->id);
            $MJTC_replies = majesticsupport::$_db->get_results($MJTC_query);
            foreach ($MJTC_replies as $MJTC_reply) {
                $MJTC_row = MJTC_includer::MJTC_getTable('replies');
                $MJTC_row->update(array('id' => $MJTC_reply->replyid, 'message' => '---'));
            }

            // erase internal note data
            if(in_array('note', majesticsupport::$_active_addons)){
                $MJTC_query = "SELECT notes.id AS noteid
                            FROM `" . majesticsupport::$_db->prefix . "mjtc_support_notes` AS notes
                            WHERE notes.ticketid = ".esc_sql($MJTC_ticket->id);
                $MJTC_notes = majesticsupport::$_db->get_results($MJTC_query);
                foreach ($MJTC_notes as $MJTC_note) {
                    $MJTC_row = MJTC_includer::MJTC_getTable('note');
                    $MJTC_row->update(array('id' => $MJTC_note->noteid, 'title' => '---', 'note' => '---'));
                }
            }
            //activity log for ticket
            if(in_array('tickethistory', majesticsupport::$_active_addons)){
                $MJTC_query = "DELETE
                        FROM `" . majesticsupport::$_db->prefix . "mjtc_support_activity_log`
                        WHERE eventfor = 1 AND referenceid = ".esc_sql($MJTC_ticket->id);
                majesticsupport::$_db->query($MJTC_query);

            }
            // private credentails for ticket
            if(in_array('privatecredentials',majesticsupport::$_active_addons)){
                MJTC_includer::MJTC_getModel('privatecredentials')->deleteCredentialsOnCloseTicket($MJTC_ticket->id);
            }
            // ticket attachments.
            $MJTC_datadirectory = majesticsupport::$_config['data_directory'];
            $MJTC_maindir = wp_upload_dir();
            $mainpath = $MJTC_maindir['basedir'] . '/' . $MJTC_datadirectory . '/attachmentdata';
            
            $MJTC_query = "SELECT ticket.attachmentdir
                        FROM `".majesticsupport::$_db->prefix."mjtc_support_tickets` AS ticket
                        WHERE ticket.id = ".esc_sql($MJTC_ticket->id);
            $MJTC_foldername = majesticsupport::$_db->get_var($MJTC_query);
            
            if(!empty($MJTC_foldername)){
                $MJTC_folder = $mainpath . '/ticket/'.$MJTC_foldername;
                
                // Replaced file_exists, glob, unlink, and rmdir with WP_Filesystem methods
                if($MJTC_wp_filesystem->exists($MJTC_folder)){
                    // The second parameter 'true' enables recursive deletion (files + folder)
                    $MJTC_wp_filesystem->delete($MJTC_folder, true);
                }
            }
            $MJTC_query = "DELETE FROM `".majesticsupport::$_db->prefix."mjtc_support_attachments` WHERE ticketid = " . esc_sql($MJTC_ticket->id);
            majesticsupport::$_db->query($MJTC_query);
        }
        $MJTC_query = "UPDATE `" . majesticsupport::$_db->prefix . "mjtc_support_erasedatarequests` SET status = 2 WHERE uid = ".esc_sql($MJTC_uid);
        majesticsupport::$_db->query($MJTC_query);
        
        MJTC_message::MJTC_setMessage(esc_html(__('User identifying data erased', 'majestic-support')), 'updated');
        
        $MJTC_user_data = get_user_by('ID',$MJTC_uid);
        if($MJTC_user_data){
            $MJTC_email = $MJTC_user_data->user_email;
            $MJTC_name = $MJTC_user_data->display_name;
            majesticsupport::$_data['mail_data']['email'] = $MJTC_email;
            majesticsupport::$_data['mail_data']['name'] = $MJTC_name;
            MJTC_includer::MJTC_getModel('email')->sendMail(4, 1);
        }
        
        return;
    }

    function deleteUserData($MJTC_uid){
        if(!is_numeric($MJTC_uid) || $MJTC_uid == 0){
            return false;
        }

        // --- START FILESYSTEM FIX ---
        global $wp_filesystem;
        if (!function_exists('wp_handle_upload')) {
            do_action('majesticsupport_load_wp_file');
        }
        if ( ! WP_Filesystem() ) {
            return false;
        }
        $MJTC_wp_filesystem = $wp_filesystem;
        // --- END FILESYSTEM FIX ---

        $MJTC_query = "SELECT id FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE uid = ".esc_sql($MJTC_uid);
        $MJTC_uids = majesticsupport::$_db->get_results($MJTC_query);

        foreach ($MJTC_uids as $MJTC_ticket) { // erase tickets data
            // ticket data
            $MJTC_row = MJTC_includer::MJTC_getTable('tickets');
            $MJTC_row->delete($MJTC_ticket->id);

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
            $MJTC_maindir = wp_upload_dir();
            $mainpath = $MJTC_maindir['basedir'] .'/'.$MJTC_datadirectory . '/attachmentdata';

            $MJTC_query = "SELECT ticket.attachmentdir
                        FROM `".majesticsupport::$_db->prefix."mjtc_support_tickets` AS ticket
                        WHERE ticket.id = ".esc_sql($MJTC_ticket->id);
            $MJTC_foldername = majesticsupport::$_db->get_var($MJTC_query);

            if(!empty($MJTC_foldername)){
                $MJTC_folder = $mainpath . '/ticket/'.$MJTC_foldername;
                
                // Replaced file_exists, glob, unlink, and rmdir with WP_Filesystem API
                if($MJTC_wp_filesystem->exists($MJTC_folder)){
                    // Setting the second parameter to true performs a recursive delete (files + folder)
                    $MJTC_wp_filesystem->delete($MJTC_folder, true);
                }
            }
            $MJTC_query = "DELETE FROM `".majesticsupport::$_db->prefix."mjtc_support_attachments` WHERE ticketid = ".esc_sql($MJTC_ticket->id);
            majesticsupport::$_db->query($MJTC_query);
        }

        $MJTC_query = "UPDATE `" . majesticsupport::$_db->prefix . "mjtc_support_erasedatarequests` SET status = 3 WHERE uid = " . esc_sql($MJTC_uid);
        majesticsupport::$_db->query($MJTC_query);

        $MJTC_user_data = get_user_by('ID',$MJTC_uid);
        if($MJTC_user_data){
            MJTC_message::MJTC_setMessage(esc_html(__('User data Deleted', 'majestic-support')), 'updated');
            $MJTC_email = $MJTC_user_data->user_email;
            $MJTC_name = $MJTC_user_data->display_name;
            majesticsupport::$_data['mail_data']['email'] = $MJTC_email;
            majesticsupport::$_data['mail_data']['name'] = $MJTC_name;
            MJTC_includer::MJTC_getModel('email')->sendMail(4, 1); // Mailfor, Delete Ticket
        }
    }

    function getAdminSearchFormDataGDPR(){
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'erase-data-requests') ) {
            die( 'Security check Failed' );
        }
        $ms_search_array = array();
        $MJTC_email = MJTC_request::MJTC_getVar('email');
        if ($MJTC_email != '') {
            $ms_search_array['email'] = MJTC_majesticsupportphplib::MJTC_addslashes(MJTC_majesticsupportphplib::MJTC_trim($MJTC_email));
        } else {
            $ms_search_array['email'] = '';
        }
        $ms_search_array['search_from_gdpr'] = 1;
        return $ms_search_array;
    }
}
?>
