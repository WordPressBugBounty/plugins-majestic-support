<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_priorityModel {

    function getPriorities() {
        // Filter
        $MJTC_prioritytitle = majesticsupport::$_search['priority']['title'];
        $MJTC_pagesize = majesticsupport::$_search['priority']['pagesize'];
        $MJTC_inquery = '';

        if ($MJTC_prioritytitle != null){
            $MJTC_inquery .= majesticsupport::$_db->prepare(" WHERE priority.priority LIKE %s", '%' . majesticsupport::$_db->esc_like($MJTC_prioritytitle) . '%');
        }

        majesticsupport::$_data['filter']['title'] = $MJTC_prioritytitle;
        majesticsupport::$_data['filter']['pagesize'] = $MJTC_pagesize;

        // Pagination
        if($MJTC_pagesize){
            MJTC_pagination::MJTC_setLimit($MJTC_pagesize);
        }
        $MJTC_query = "SELECT COUNT(`id`) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` AS priority ";
        $MJTC_query .= $MJTC_inquery;
        $total = majesticsupport::$_db->get_var($MJTC_query);
        majesticsupport::$_data['total'] = $total;
        majesticsupport::$_data[1] = MJTC_pagination::MJTC_getPagination($total);

        // Data
        $MJTC_query = "SELECT priority.*
					FROM `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` AS priority ";
        $MJTC_query .= $MJTC_inquery;
        $MJTC_query .= " ORDER BY priority.ordering ASC LIMIT " . MJTC_pagination::MJTC_getOffset() . ", " . MJTC_pagination::MJTC_getLimit();
        majesticsupport::$_data[0] = majesticsupport::$_db->get_results($MJTC_query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        return;
    }

    function getPriorityForCombobox() {
        $MJTC_query = "SELECT id, priority AS text FROM `" . majesticsupport::$_db->prefix . "mjtc_support_priorities`";
        if( in_array('agent',majesticsupport::$_active_addons) ){
            $MJTC_agent = MJTC_includer::MJTC_getModel('agent')->isUserStaff();
        }else{
            $MJTC_agent = false;
        }

        if (!is_admin() && !$MJTC_agent) {
            $MJTC_query .= ' WHERE ispublic = 1 ';
        }
        $MJTC_query .= 'ORDER BY ordering ASC';
        $MJTC_priorities = majesticsupport::$_db->get_results($MJTC_query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        return apply_filters('MJTC_priorities_for_combobox', $MJTC_priorities);
    }

    function getDefaultPriorityID() {
        $MJTC_query = "SELECT id FROM `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` WHERE isdefault = 1";
        $MJTC_id = majesticsupport::$_db->get_var($MJTC_query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        return $MJTC_id;
    }

    function getPriorityForForm($MJTC_id) {
        $MJTC_result=array();
        if ($MJTC_id) {
            if (!is_numeric($MJTC_id))
                return false;
            $MJTC_query = majesticsupport::$_db->prepare("SELECT priority.*
						FROM `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` AS priority
						WHERE priority.id = %d", absint($MJTC_id));
            $MJTC_result = majesticsupport::$_db->get_row($MJTC_query);
            if (majesticsupport::$_db->last_error != null) {
                MJTC_includer::MJTC_getModel('systemerror')->addSystemError(); // if there is an error add it to system errorrs
            }
        }
        majesticsupport::$_data[0]=$MJTC_result;
        return;
    }

    function storePriority($MJTC_data) {
        if (!$this->validatePriority($MJTC_data['priority'], $MJTC_data['id'])) {
            MJTC_message::MJTC_setMessage(esc_html(__('Priority Title Already Exist', 'majestic-support')), 'error');
            return;
        }
        if (!current_user_can('manage_options')) { //only admin can change it.
            return false;
        }
        $MJTC_data = majesticsupport::MJTC_sanitizeData($MJTC_data); // MJTC_sanitizeData() function uses wordpress santize functions
        $MJTC_data['prioritycolour'] = $MJTC_data['prioritycolor'];

        if (!$MJTC_data['id']) { //new
            $MJTC_data['ordering'] = $this->getNextOrdering();
        }
        $MJTC_row = MJTC_includer::MJTC_getTable('priorities');
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
            if ($MJTC_data['isdefault'] == 1) {
                $this->setDefaultPriority($MJTC_id);
            }
            MJTC_message::MJTC_setMessage(esc_html(__('Priority has been stored', 'majestic-support')), 'updated');
        } else {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError(); // if there is an error add it to system errorrs
            MJTC_message::MJTC_setMessage(esc_html(__('Priority has not been stored', 'majestic-support')), 'error');
        }
        return;
    }

    private function validatePriority($MJTC_priority, $MJTC_id) {
        if ($MJTC_id) {
            if (!is_numeric($MJTC_id))
                return false;
            $MJTC_query = majesticsupport::$_db->prepare("SELECT priority FROM `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` WHERE id = %d", absint($MJTC_id));
            $MJTC_result = majesticsupport::$_db->get_var($MJTC_query);
            if ($MJTC_result == $MJTC_priority) {
                return true;
            }
        }

        $MJTC_query = majesticsupport::$_db->prepare('SELECT COUNT(id) FROM `' . majesticsupport::$_db->prefix . 'mjtc_support_priorities` WHERE priority = %s', $MJTC_priority);
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
        $MJTC_query = "SELECT MAX(ordering) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_priorities`";
        $MJTC_result = majesticsupport::$_db->get_var($MJTC_query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        return $MJTC_result + 1;
    }

    function setDefaultPriority($MJTC_id) {
        if (!is_numeric($MJTC_id))
            return false;
        $MJTC_query = "UPDATE `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` SET isdefault = 2";
        majesticsupport::$_db->query($MJTC_query);
        $MJTC_query = majesticsupport::$_db->prepare("UPDATE `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` SET isdefault = 1 WHERE id = %d", absint($MJTC_id));
        majesticsupport::$_db->query($MJTC_query);
        return;
    }

    function removePriority($MJTC_id) {
        if (!is_numeric($MJTC_id))
            return false;
        $MJTC_canremove = $this->canRemovePriority($MJTC_id);
        if ($MJTC_canremove == 1) {
            $MJTC_row = MJTC_includer::MJTC_getTable('priorities');
            if ($MJTC_row->delete($MJTC_id)) {
                MJTC_message::MJTC_setMessage(esc_html(__('Priority has been deleted', 'majestic-support')), 'updated');
            } else {
                MJTC_includer::MJTC_getModel('systemerror')->addSystemError(); // if there is an error add it to system errorrs
                MJTC_message::MJTC_setMessage(esc_html(__('Priority has not been deleted', 'majestic-support')), 'error');
            }
        } elseif ($MJTC_canremove == 2)
            MJTC_message::MJTC_setMessage(esc_html(__('Priority','majestic-support')).' '. esc_html(__('in use cannot be deleted', 'majestic-support')), 'error');
        elseif ($MJTC_canremove == 3)
            MJTC_message::MJTC_setMessage(esc_html(__('Default priority cannot delete', 'majestic-support')), 'error');

        return;
    }

    function makeDefault($MJTC_id) {
        if (!is_numeric($MJTC_id))
            return false;
        //Reset all priorities to non-default
        $MJTC_query = "UPDATE `" . majesticsupport::$_db->prefix . 'mjtc_support_priorities` SET isdefault = 0';
        majesticsupport::$_db->query($MJTC_query);
        //Make the selected priority as default
        $MJTC_query = majesticsupport::$_db->prepare("UPDATE `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` SET isdefault = 1 WHERE id = %d", absint($MJTC_id));
        majesticsupport::$_db->query($MJTC_query);
        if (majesticsupport::$_db->last_error == null) {
            MJTC_message::MJTC_setMessage(esc_html(__('Priority has been make default', 'majestic-support')), 'updated');
        } else {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError(); // if there is an error add it to system errorrs
            MJTC_message::MJTC_setMessage(esc_html(__('Priority has not been make default', 'majestic-support')), 'error');
        }
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
        $MJTC_query = majesticsupport::$_db->prepare("SELECT t.ordering,t.id,t2.ordering AS ordering2 FROM `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` AS t,`" . majesticsupport::$_db->prefix . "mjtc_support_priorities` AS t2 WHERE t.ordering $MJTC_order t2.ordering AND t2.id = %d ORDER BY t.ordering $MJTC_direction LIMIT 1", absint($MJTC_id));
        $MJTC_result = majesticsupport::$_db->get_row($MJTC_query);
        $MJTC_query = majesticsupport::$_db->prepare("UPDATE `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` SET ordering = %d WHERE id = %d", absint($MJTC_result->ordering), absint($MJTC_id));
        majesticsupport::$_db->query($MJTC_query);
        $MJTC_query = majesticsupport::$_db->prepare("UPDATE `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` SET ordering = %d WHERE id = %d", absint($MJTC_result->ordering2), absint($MJTC_result->id));
        majesticsupport::$_db->query($MJTC_query);

        $MJTC_row = MJTC_includer::MJTC_getTable('priorities');
        if ($MJTC_row->update(array('id' => $MJTC_id, 'ordering' => $MJTC_result->ordering)) && $MJTC_row->update(array('id' => $MJTC_result->id, 'ordering' => $MJTC_result->ordering2))) {
            MJTC_message::MJTC_setMessage(esc_html(__('Priority','majestic-support')).' '. esc_html(__('ordering has been changed', 'majestic-support')), 'updated');
        } else {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError(); // if there is an error add it to system errorrs
            MJTC_message::MJTC_setMessage(esc_html(__('Priority','majestic-support')).' '. esc_html(__('ordering has not changed', 'majestic-support')), 'error');
        }
        return;
    }

    private function canRemovePriority($MJTC_id) {
        if (!is_numeric($MJTC_id))
            return false;
        $MJTC_query = majesticsupport::$_db->prepare("SELECT (
					(SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE priorityid = %d)
					) AS total", absint($MJTC_id));
        $MJTC_result = majesticsupport::$_db->get_var($MJTC_query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        if ($MJTC_result == 0) {
            $MJTC_query = majesticsupport::$_db->prepare("SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` WHERE isdefault = 1 AND id = %d", absint($MJTC_id));
            $MJTC_result = majesticsupport::$_db->get_var($MJTC_query);
            if (majesticsupport::$_db->last_error != null) {
                MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
            }
            if ($MJTC_result == 0)
                return 1;
            else
                return 3;
        } else
            return 2;
    }

    function getPriorityById($MJTC_id) {
        if (!is_numeric($MJTC_id))
            return false;
        $MJTC_query = majesticsupport::$_db->prepare("SELECT priority FROM `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` WHERE id = %d", absint($MJTC_id));
        $MJTC_priority = majesticsupport::$_db->get_var($MJTC_query);
        return $MJTC_priority;
    }

    function getAdminSearchFormDataPriority(){
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'priorities') ) {
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
        $ms_search_array['search_from_priority'] = 1;
        return $ms_search_array;
    }
}

?>
