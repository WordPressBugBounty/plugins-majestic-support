<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_systemerrorModel {

    function getSystemErrors() {
        $MJTC_inquery = '';
        // Pagination
        $MJTC_query = "SELECT COUNT(`id`) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_system_errors`";
        $MJTC_query .= $MJTC_inquery;
        $total = majesticsupport::$_db->get_var($MJTC_query);
        majesticsupport::$_data[1] = MJTC_pagination::MJTC_getPagination($total);

        // Data
        $MJTC_query = " SELECT systemerror.*
					FROM `" . majesticsupport::$_db->prefix . "mjtc_support_system_errors` AS systemerror ";
        $MJTC_query .= $MJTC_inquery;
        $MJTC_query .= " ORDER BY systemerror.created DESC LIMIT " . MJTC_pagination::MJTC_getOffset() . ", " . MJTC_pagination::MJTC_getLimit();
        majesticsupport::$_data[0] = majesticsupport::$_db->get_results($MJTC_query);
        if (majesticsupport::$_db->last_error != null) {
            $this->addSystemError();
        }
        return;
    }

    function addSystemError($MJTC_error = null) {
        if($MJTC_error == null) $MJTC_error = majesticsupport::$_db->last_error;
        $MJTC_query_array = array('error' => $MJTC_error,
            'uid' => MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid(),
            'isview' => 0,
            'created' => date_i18n('Y-m-d H:i:s')
        );
        majesticsupport::$_db->replace(majesticsupport::$_db->prefix . 'mjtc_support_system_errors', $MJTC_query_array);
        return;
    }

    function updateIsView($MJTC_id) {
        if (!is_numeric($MJTC_id))
            return false;
        $MJTC_query = "UPDATE " . majesticsupport::$_db->prefix . "`mjtc_support_system_errors` set isview = 1 WHERE id = " . esc_sql($MJTC_id);
        majesticsupport::$_db->Query($MJTC_query);
        if (majesticsupport::$_db->last_error != null) {
            $this->addSystemError();
        }
    }

    function removeSystemError($MJTC_id) {
        if ($MJTC_id == 'all') {
            $MJTC_query = "DELETE FROM `" . majesticsupport::$_db->prefix . "mjtc_support_system_errors` ";
            majesticsupport::$_db->query($MJTC_query);
            MJTC_message::MJTC_setMessage(esc_html(__('System error has been deleted', 'majestic-support')), 'updated');
        }else{
            if (!is_numeric($MJTC_id)){
                return false;
            }
            $MJTC_row = MJTC_includer::MJTC_getTable('system_errors');
            if ($MJTC_row->delete($MJTC_id)) {
                MJTC_message::MJTC_setMessage(esc_html(__('System error has been deleted', 'majestic-support')), 'updated');
            } else {
                MJTC_message::MJTC_setMessage(esc_html(__('System error has not been deleted', 'majestic-support')), 'error');
            }
        }
        return;
    }

}

?>
