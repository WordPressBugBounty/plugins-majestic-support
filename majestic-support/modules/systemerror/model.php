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
        // 1. Always gather request context URL safely
        $MJTC_error_data = array(
            'url' => isset($_SERVER['REQUEST_URI']) ? esc_url_raw(wp_unslash($_SERVER['REQUEST_URI'])) : 'Unknown URL'
        );

        // 2. Differentiate between an explicit custom error string and an internal DB error
        if ($MJTC_error !== null && $MJTC_error !== '') {
            
            // Custom string error provided
            $MJTC_error_data['error'] = $MJTC_error;

        } else {
            // Internal database error: gather context signals
            $MJTC_error_msg    = majesticsupport::$_db->last_error;
            $MJTC_failed_query = majesticsupport::$_db->last_query;

            // Generate a mid-level execution trace (up to 5 levels deep)
            $MJTC_raw_backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5);
            $MJTC_trace_path    = array();

            foreach ($MJTC_raw_backtrace as $MJTC_trace) {
                $MJTC_func = isset($MJTC_trace['function']) ? $MJTC_trace['function'] : 'Unknown';
                $MJTC_file = isset($MJTC_trace['file']) ? basename($MJTC_trace['file']) : 'Unknown';

                // Skip logging this tracking function itself
                if ($MJTC_func !== 'addSystemError') {
                    $MJTC_trace_path[] = "$MJTC_func ($MJTC_file)";
                }
            }

            $MJTC_called_by_path = implode(' <- ', $MJTC_trace_path);

            $MJTC_error_data['error'] = $MJTC_error_msg ? $MJTC_error_msg : 'Unknown DB Error';
            $MJTC_error_data['query'] = $MJTC_failed_query ? $MJTC_failed_query : 'No query recorded';
            $MJTC_error_data['path']  = $MJTC_called_by_path;
        }

        // 3. Construct payload matching Majestic Support's internal architecture
        $MJTC_query_array = array(
            'error'   => wp_json_encode($MJTC_error_data),
            'uid'     => MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid(),
            'isview'  => 0,
            'created' => date_i18n('Y-m-d H:i:s')
        );

        // 4. Execute atomic database update/replace routine
        majesticsupport::$_db->replace(majesticsupport::$_db->prefix . 'mjtc_support_system_errors', $MJTC_query_array);

        // 5. Clear state parameters to break diagnostic persistence issues
        majesticsupport::$_db->last_error = '';
        majesticsupport::$_db->last_query = '';

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
