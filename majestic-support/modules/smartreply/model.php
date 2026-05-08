<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_smartreplyModel {

    function getSmartReplyies() {
        // Filter
        $MJTC_isadmin = is_admin();
        $title = ($MJTC_isadmin) ? 'title' : 'ms-title';

        $MJTC_smartreplytitle = isset(majesticsupport::$_search['smartreply'][$title]) ? (majesticsupport::$_search['smartreply'][$title]): '';
        $MJTC_pagesize = isset(majesticsupport::$_search['smartreply']['pagesize']) ? (majesticsupport::$_search['smartreply']['pagesize']): '';
        $MJTC_inquery = '';

        if ($MJTC_smartreplytitle != null){
            $MJTC_inquery .= " WHERE smartreply.title LIKE '%".esc_sql($MJTC_smartreplytitle)."%'";
        }

        majesticsupport::$_data['filter'][$title] = $MJTC_smartreplytitle;
        majesticsupport::$_data['filter']['pagesize'] = $MJTC_pagesize;

        // Pagination
        if($MJTC_pagesize){
            MJTC_pagination::MJTC_setLimit($MJTC_pagesize);
        }
        $MJTC_query = "SELECT COUNT(`id`) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_smartreplies` AS smartreply ";
        $MJTC_query .= $MJTC_inquery;
        $total = majesticsupport::$_db->get_var($MJTC_query);
        majesticsupport::$_data['total'] = $total;
        majesticsupport::$_data[1] = MJTC_pagination::MJTC_getPagination($total);

        // Data
        $MJTC_query = "SELECT smartreply.*
					FROM `" . majesticsupport::$_db->prefix . "mjtc_support_smartreplies` AS smartreply ";
        $MJTC_query .= $MJTC_inquery;
        $MJTC_query .= " ORDER BY smartreply.id DESC LIMIT " . MJTC_pagination::MJTC_getOffset() . ", " . MJTC_pagination::MJTC_getLimit();
        majesticsupport::$_data[0] = majesticsupport::$_db->get_results($MJTC_query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        return;
    }

    function getSmartReplyForForm($MJTC_id) {
        $MJTC_result=array();
        if ($MJTC_id) {
            if (!is_numeric($MJTC_id))
                return false;
            $MJTC_query = "SELECT smartreply.*
						FROM `" . majesticsupport::$_db->prefix . "mjtc_support_smartreplies` AS smartreply
						WHERE smartreply.id = " . esc_sql($MJTC_id);
            $MJTC_result = majesticsupport::$_db->get_row($MJTC_query);
            $MJTC_result->ticketsubjects = json_decode($MJTC_result->ticketsubjects);
            if (majesticsupport::$_db->last_error != null) {
                MJTC_includer::MJTC_getModel('systemerror')->addSystemError(); // if there is an error add it to system errorrs
            }
        }
        majesticsupport::$_data[0]=$MJTC_result;
        return;
    }

    function storeSmartReply($MJTC_data) {
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'save-smart-reply-'.$MJTC_data['id']) ) {
            die( 'Security check Failed' );
        }
        if ($MJTC_data['id'] == '') {
            if ($this->validateSmartReply($MJTC_data['title'])) {
                MJTC_message::MJTC_setMessage(esc_html(__('Smart Reply Title Already Exist', 'majestic-support')), 'error');
                return;
            }
        }
        $MJTC_newdata = majesticsupport::MJTC_sanitizeData($MJTC_data);// MJTC_sanitizeData() function uses wordpress santize functions
        $MJTC_ticketsubjects = [];
        foreach ($MJTC_newdata['ticketsubjects'] as $MJTC_ticketsubject) {
            if($MJTC_ticketsubject!='')
            {
                $MJTC_ticketsubjects[] = MJTC_majesticsupportphplib::MJTC_stripslashes($MJTC_ticketsubject);
            }

        }
        $MJTC_newdata['ticketsubjects'] = wp_json_encode($MJTC_ticketsubjects, true);

        $MJTC_row = MJTC_includer::MJTC_getTable('smartreplies');
        if (isset($_POST['reply'])) {
            $MJTC_newdata['reply'] = MJTC_includer::MJTC_getModel('majesticsupport')->getSanitizedEditorData($_POST['reply']);
        }
        $MJTC_newdata = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($MJTC_newdata);// remove slashes with quotes.
        $MJTC_error = 0;

        if (!$MJTC_row->bind($MJTC_newdata)) {
            $MJTC_error = 1;
        }
        if (!$MJTC_row->store()) {
            $MJTC_error = 1;
        }

        if ($MJTC_error == 0) {
            $MJTC_id = $MJTC_row->id;
            MJTC_message::MJTC_setMessage(esc_html(__('Smart Reply has been stored', 'majestic-support')), 'updated');
        } else {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError(); // if there is an error add it to system errorrs
            MJTC_message::MJTC_setMessage(esc_html(__('Smart Reply has not been stored', 'majestic-support')), 'error');
        }
        return;
    }

    private function validateSmartReply($title) {
        if (!$title)
            return false;
        $MJTC_query = 'SELECT COUNT(id) FROM `' . majesticsupport::$_db->prefix . 'mjtc_support_smartreplies` WHERE title = "' . esc_sql($title) . '"';
        $MJTC_result = majesticsupport::$_db->get_var($MJTC_query);
        if ($MJTC_result > 0)
            return true;
        else
            return false;
    }

    function removeSmartReply($MJTC_id) {
        if (!is_numeric($MJTC_id))
            return false;
        if ( in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) {
            $MJTC_allowed = MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForTask('Delete Smart Reply');
            if ($MJTC_allowed != true) {
                MJTC_message::MJTC_setMessage(__('You are not allowed', 'majestic-support'), 'error');
                return;
            }
        }
        $MJTC_row = MJTC_includer::MJTC_getTable('smartreplies');
        if ($MJTC_row->delete($MJTC_id)) {
            MJTC_message::MJTC_setMessage(esc_html(__('Smart Reply has been deleted', 'majestic-support')), 'updated');
        } else {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError(); // if there is an error add it to system errorrs
            MJTC_message::MJTC_setMessage(esc_html(__('Smart Reply has not been deleted', 'majestic-support')), 'error');
        }
        return;
    }

    function getSmartReplyById($MJTC_id) {
        if (!is_numeric($MJTC_id))
            return false;
        $MJTC_query = "SELECT smartreply FROM `" . majesticsupport::$_db->prefix . "mjtc_support_smartreplies` WHERE id = " . esc_sql($MJTC_id);
        $MJTC_smartreply = majesticsupport::$_db->get_var($MJTC_query);
        return $MJTC_smartreply;
    }

function checkSmartReply(){
    $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
    if (! wp_verify_nonce( $MJTC_nonce, 'check-smart-reply') ) {
        die( 'Security check Failed' );
    }
    $MJTC_limit = MJTC_includer::MJTC_getModel('configuration')->getConfigValue('maximum_record_for_smart_reply');
    $MJTC_subject = MJTC_request::MJTC_getVar('ticketSubject');
    $MJTC_query = 'SELECT id,title, MATCH (ticketsubjects)
                AGAINST ("'.esc_sql($MJTC_subject).'"
                IN NATURAL LANGUAGE MODE) AS relevance 
                FROM `' . majesticsupport::$_db->prefix . 'mjtc_support_smartreplies`
                WHERE MATCH (ticketsubjects)
                AGAINST("'.esc_sql($MJTC_subject).'"
                IN NATURAL LANGUAGE MODE) LIMIT '.esc_sql($MJTC_limit).';';
    $MJTC_replies = majesticsupport::$_db->get_results($MJTC_query);
    $MJTC_html = '';
    foreach ($MJTC_replies as $MJTC_reply) {
        $MJTC_html .= "<span class=\"ms-ticket-detail-smartreply-add smartReplyFound\"  onclick=\"getSmartReply(".$MJTC_reply->id.");\">
                    <span class=\"ms-smartreply-btn-text\" id=\"possible-reply\">
                        ". majesticsupport::MJTC_getVarValue($MJTC_reply->title)."
                    </span>
                </span>";
    }
    if (isset($MJTC_html) && $MJTC_html != '') {
        return wp_json_encode(MJTC_majesticsupportphplib::MJTC_htmlentities($MJTC_html));
    }
    return;
}

    function getSmartReply(){
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'get-smart-reply') ) {
            die( 'Security check Failed' );
        }
        $MJTC_id = MJTC_request::MJTC_getVar('val');
        $MJTC_query = "SELECT usedby,reply FROM `" . majesticsupport::$_db->prefix . "mjtc_support_smartreplies` WHERE id = ".esc_sql($MJTC_id);
        $MJTC_reply = majesticsupport::$_db->get_row($MJTC_query);
        $MJTC_usedby = $MJTC_reply->usedby + 1;
        $MJTC_query = "UPDATE `" . majesticsupport::$_db->prefix . "mjtc_support_smartreplies` SET `usedby`=".esc_sql($MJTC_usedby)." WHERE id=".esc_sql($MJTC_id);
        majesticsupport::$_db->query($MJTC_query);
        return $MJTC_reply->reply;
    }

    function getAdminSearchFormDataSmartReply(){
        $ms_search_array = array();
        $MJTC_param = (is_admin()) ? 'title' : 'ms-title';
        $title = MJTC_request::MJTC_getVar($MJTC_param);
        if ($title != '') {
            $ms_search_array[$MJTC_param] = MJTC_majesticsupportphplib::MJTC_addslashes(MJTC_majesticsupportphplib::MJTC_trim($title));
        } else {
            $ms_search_array[$MJTC_param] = '';
        }
        $ms_search_array['pagesize'] = absint(MJTC_request::MJTC_getVar('pagesize'));
        $ms_search_array['search_from_smartreply'] = 1;
        return $ms_search_array;
    }

    function getSmartReplyResponse() {
        // Verify nonce
        check_ajax_referer('get-smart-reply', '_wpnonce');

        $MJTC_reply_id = intval(MJTC_request::MJTC_getVar('reply_id'));

        if (!$MJTC_reply_id) {
            wp_send_json_error(['message' => __('Reply ID is required.', 'majestic-support')]);
        }

        $MJTC_query = "
        SELECT sr.*
            FROM `" . majesticsupport::$_db->prefix . "mjtc_support_smartreplies` AS sr
            WHERE sr.id = " . esc_sql($MJTC_reply_id);
        $MJTC_reply = majesticsupport::$_db->get_row($MJTC_query);


        if (majesticsupport::$_db->last_error) {
            wp_send_json_error(['message' => __('Database error occurred.', 'majestic-support')]);
        }

        $MJTC_formatted_replies[] = [
            'id'        => $MJTC_reply->id,
            'text'      => $MJTC_reply->reply,
            'usedby'      => $MJTC_reply->usedby,
            'timestamp' => $MJTC_reply->created
        ];
        wp_send_json_success([
            'replies' => $MJTC_formatted_replies,
            'count'   => count($MJTC_formatted_replies)
        ]);
    }
}

?>
