<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_statusModel {

    function getStatuses() {
        // Filter
        $MJTC_statustitle = majesticsupport::$_search['status']['status'];
        $MJTC_pagesize = majesticsupport::$_search['status']['pagesize'];
        $MJTC_inquery = '';

        if ($MJTC_statustitle != null){
            $MJTC_inquery .= majesticsupport::$_db->prepare(" WHERE status.status LIKE %s", '%' . majesticsupport::$_db->esc_like($MJTC_statustitle) . '%');
        }

        majesticsupport::$_data['filter']['title'] = $MJTC_statustitle;
        majesticsupport::$_data['filter']['pagesize'] = $MJTC_pagesize;

        // Pagination
        if($MJTC_pagesize){
            MJTC_pagination::MJTC_setLimit($MJTC_pagesize);
        }
        $MJTC_query = "SELECT COUNT(`id`) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_statuses` AS status ";
        $MJTC_query .= $MJTC_inquery;
        $total = majesticsupport::$_db->get_var($MJTC_query);
        majesticsupport::$_data['total'] = $total;
        majesticsupport::$_data[1] = MJTC_pagination::MJTC_getPagination($total);

        // Data
        $MJTC_query = "SELECT status.*
					FROM `" . majesticsupport::$_db->prefix . "mjtc_support_statuses` AS status ";
        $MJTC_query .= $MJTC_inquery;
        $MJTC_query .= " ORDER BY status.ordering ASC LIMIT " . MJTC_pagination::MJTC_getOffset() . ", " . MJTC_pagination::MJTC_getLimit();
        majesticsupport::$_data[0] = majesticsupport::$_db->get_results($MJTC_query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        return;
    }

    function getStatusForCombobox() {
        $MJTC_query = "SELECT id, status AS text FROM `" . majesticsupport::$_db->prefix . "mjtc_support_statuses`";
        $MJTC_query .= 'ORDER BY ordering ASC';
        $MJTC_statuses = majesticsupport::$_db->get_results($MJTC_query);

        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        return $MJTC_statuses;
    }

    function getStatusForForm($MJTC_id) {
        $MJTC_result=array();
        if ($MJTC_id) {
            if (!is_numeric($MJTC_id))
                return false;
            $MJTC_query = majesticsupport::$_db->prepare("SELECT status.*
				FROM `" . majesticsupport::$_db->prefix . "mjtc_support_statuses` AS status
				WHERE status.id = %d", absint($MJTC_id));
            $MJTC_result = majesticsupport::$_db->get_row($MJTC_query);
            if ($MJTC_result) {
                $MJTC_customStatuses = [
                    1 => 'New',
                    2 => 'Waiting Reply',
                    3 => 'In Progress',
                    4 => 'Replied',
                    5 => 'Closed',
                    6 => 'Close Due To Merge'
                ];
                // add custom status
                $MJTC_result->custom_status = isset($MJTC_customStatuses[$MJTC_result->id]) ? $MJTC_customStatuses[$MJTC_result->id] : '';
            }
            if (majesticsupport::$_db->last_error != null) {
                MJTC_includer::MJTC_getModel('systemerror')->addSystemError(); // if there is an error add it to system errorrs
            }
        }
        majesticsupport::$_data[0]=$MJTC_result;
        return;
    }

    function storeStatus($MJTC_data) {
        if (!current_user_can('manage_options')) { //only admin can change it.
            return false;
        }
        if (!$this->validateStatus($MJTC_data['status'], $MJTC_data['id'])) {
            MJTC_message::MJTC_setMessage(esc_html(__('Status Title Already Exist', 'majestic-support')), 'error');
            return;
        }
        $MJTC_data = majesticsupport::MJTC_sanitizeData($MJTC_data); // MJTC_sanitizeData() function uses wordpress santize functions
        $MJTC_data['statuscolour'] = $MJTC_data['statuscolor'];
        $MJTC_data['statusbgcolour'] = $MJTC_data['statusbgcolor'];

        if (!$MJTC_data['id']) { //new
            $MJTC_data['ordering'] = $this->getNextOrdering();
        }
        $MJTC_row = MJTC_includer::MJTC_getTable('statuses');
        $MJTC_data = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($MJTC_data);// remove slashes with quotes.
        $MJTC_error = 0;
        if (!$MJTC_row->bind($MJTC_data)) {
            $MJTC_error = 1;
        }
        if (!$MJTC_row->store()) {
            $MJTC_error = 1;
        }

        if ($MJTC_error == 0) {
            $MJTC_id = $MJTC_row->id;
            MJTC_message::MJTC_setMessage(esc_html(__('Status has been stored', 'majestic-support')), 'updated');
        } else {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError(); // if there is an error add it to system errorrs
            MJTC_message::MJTC_setMessage(esc_html(__('Status has not been stored', 'majestic-support')), 'error');
        }
        return;
    }

    private function validateStatus($MJTC_status, $MJTC_id) {
        if ($MJTC_id) {
            if (!is_numeric($MJTC_id))
                return false;
            $MJTC_query = majesticsupport::$_db->prepare("SELECT status FROM `" . majesticsupport::$_db->prefix . "mjtc_support_statuses` WHERE id = %d", absint($MJTC_id));
            $MJTC_result = majesticsupport::$_db->get_var($MJTC_query);
            if ($MJTC_result == $MJTC_status) {
                return true;
            }
        }

        $MJTC_query = majesticsupport::$_db->prepare('SELECT COUNT(id) FROM `' . majesticsupport::$_db->prefix . 'mjtc_support_statuses` WHERE status = %s', $MJTC_status);
        $MJTC_result = majesticsupport::$_db->get_var($MJTC_query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        if ($MJTC_result == 0)
            return true;
        else
            return false;
    }

    private function getNextOrdering() {
        $MJTC_query = "SELECT MAX(ordering) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_statuses`";
        $MJTC_result = majesticsupport::$_db->get_var($MJTC_query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        return $MJTC_result + 1;
    }

    function removeStatus($MJTC_id) {
        if (!is_numeric($MJTC_id))
            return false;
        $MJTC_canremove = $this->canRemoveStatus($MJTC_id);
        if ($MJTC_canremove == 1) {
            $MJTC_row = MJTC_includer::MJTC_getTable('statuses');
            if ($MJTC_row->delete($MJTC_id)) {
                MJTC_message::MJTC_setMessage(esc_html(__('Status has been deleted', 'majestic-support')), 'updated');
            } else {
                MJTC_includer::MJTC_getModel('systemerror')->addSystemError(); // if there is an error add it to system errorrs
                MJTC_message::MJTC_setMessage(esc_html(__('Status has not been deleted', 'majestic-support')), 'error');
            }
        } elseif ($MJTC_canremove == 2)
            MJTC_message::MJTC_setMessage(esc_html(__('Status','majestic-support')).' '. esc_html(__('in use cannot be deleted', 'majestic-support')), 'error');

        return;
    }

    function setOrdering($MJTC_id) {
        if (!is_numeric($MJTC_id))
            return false;
        $MJTC_order = MJTC_request::MJTC_getVar('order', 'get');
        if ($MJTC_order == 'down') {
            $MJTC_order = ">";
            $MJTC_direction = "ASC";
        } else {
            $MJTC_order = "<";
            $MJTC_direction = "DESC";
        }
        $MJTC_query = majesticsupport::$_db->prepare("SELECT t.ordering,t.id,t2.ordering AS ordering2 FROM `" . majesticsupport::$_db->prefix . "mjtc_support_statuses` AS t,`" . majesticsupport::$_db->prefix . "mjtc_support_statuses` AS t2 WHERE t.ordering $MJTC_order t2.ordering AND t2.id = %d ORDER BY t.ordering $MJTC_direction LIMIT 1", absint($MJTC_id));
        $MJTC_result = majesticsupport::$_db->get_row($MJTC_query);
        $MJTC_query = majesticsupport::$_db->prepare("UPDATE `" . majesticsupport::$_db->prefix . "mjtc_support_statuses` SET ordering = %d WHERE id = %d", absint($MJTC_result->ordering), absint($MJTC_id));
        majesticsupport::$_db->query($MJTC_query);
        $MJTC_query = majesticsupport::$_db->prepare("UPDATE `" . majesticsupport::$_db->prefix . "mjtc_support_statuses` SET ordering = %d WHERE id = %d", absint($MJTC_result->ordering2), absint($MJTC_result->id));
        majesticsupport::$_db->query($MJTC_query);

        $MJTC_row = MJTC_includer::MJTC_getTable('statuses');
        if ($MJTC_row->update(array('id' => $MJTC_id, 'ordering' => $MJTC_result->ordering)) && $MJTC_row->update(array('id' => $MJTC_result->id, 'ordering' => $MJTC_result->ordering2))) {
            MJTC_message::MJTC_setMessage(esc_html(__('Status','majestic-support')).' '. esc_html(__('ordering has been changed', 'majestic-support')), 'updated');
        } else {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError(); // if there is an error add it to system errorrs
            MJTC_message::MJTC_setMessage(esc_html(__('Status','majestic-support')).' '. esc_html(__('ordering has not changed', 'majestic-support')), 'error');
        }
        return;
    }

    private function canRemoveStatus($MJTC_id) {
        if (!is_numeric($MJTC_id))
            return false;
        $MJTC_query = majesticsupport::$_db->prepare("SELECT (
					(SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE status = %d)
					) AS total", absint($MJTC_id));
        $MJTC_result = majesticsupport::$_db->get_var($MJTC_query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        if ($MJTC_result == 0) {
            return 1;
        } else
            return 2;
    }

    function getStatusById($MJTC_id) {
        if (!is_numeric($MJTC_id))
            return false;
        $MJTC_query = majesticsupport::$_db->prepare("SELECT status FROM `" . majesticsupport::$_db->prefix . "mjtc_support_statuses` WHERE id = %d", absint($MJTC_id));
        $MJTC_status = majesticsupport::$_db->get_var($MJTC_query);
        return $MJTC_status;
    }

    function getAdminSearchFormDataStatus(){
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'statuses') ) {
            die( 'Security check Failed' );
        }
        $ms_search_array = array();
        $title = MJTC_request::MJTC_getVar('title');
        if ($title != '') {
            $ms_search_array['title'] = MJTC_majesticsupportphplib::MJTC_addslashes(MJTC_majesticsupportphplib::MJTC_trim($title));
        } else {
            $ms_search_array['title'] = '';
        }
        $ms_search_array['pagesize'] = absint(MJTC_request::MJTC_getVar('pagesize'));
        $ms_search_array['search_from_status'] = 1;
        return $ms_search_array;
    }

    function getStatusForFilter() {
        $MJTC_query = "SELECT id, status AS text FROM `" . majesticsupport::$_db->prefix . "mjtc_support_statuses` WHERE id NOT IN (1 , 5, 6)";
        
            
        $MJTC_query .= 'ORDER BY ordering ASC';
        $MJTC_statuses = majesticsupport::$_db->get_results($MJTC_query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        return $MJTC_statuses;
    }
}

?>
