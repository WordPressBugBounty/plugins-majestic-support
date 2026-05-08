<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_formhandler {

    function __construct() {
        add_action('init', array($this, 'MJTC_checkFormRequest'));
        add_action('init', array($this, 'MJTC_checkDeleteRequest'));
    }

    /*
     * Handle Form request
     */

    function MJTC_checkFormRequest() {
        majesticsupport::$_data['sanitized_args']['_wpnonce'] = wp_create_nonce("VERIFY-MAJESTIC-SUPPORT-INTERNAL-NONCE");
        $MJTC_formrequest = MJTC_request::MJTC_getVar('form_request', 'post');
        if ($MJTC_formrequest == 'majesticsupport') {
            //handle the request
            $MJTC_page_id = MJTC_Request::MJTC_getVar('page_id', 'GET');
            majesticsupport::setPageID($MJTC_page_id);
            $MJTC_modulename = (is_admin()) ? 'page' : 'mjsmod';
            $MJTC_module = MJTC_request::MJTC_getVar($MJTC_modulename);
            $MJTC_module = MJTC_majesticsupportphplib::MJTC_str_replace('majesticsupport_', '', $MJTC_module);
            MJTC_includer::MJTC_include_file($MJTC_module);
            $MJTC_class = 'MJTC_' . $MJTC_module . "Controller";
            $MJTC_task = MJTC_request::MJTC_getVar('task');
            $MJTC_obj = new $MJTC_class;
            $MJTC_obj->$MJTC_task();
        }
    }

    /*
     * Handle Form request
     */

    function MJTC_checkDeleteRequest() {
        majesticsupport::$_data['sanitized_args']['_wpnonce'] = wp_create_nonce("VERIFY-MAJESTIC-SUPPORT-INTERNAL-NONCE");
        $majesticsupport_action = MJTC_request::MJTC_getVar('action', 'get');
        if ($majesticsupport_action == 'mstask') {
            //handle the request
            $MJTC_page_id = MJTC_Request::MJTC_getVar('page_id', 'GET');
            majesticsupport::setPageID($MJTC_page_id);
            $MJTC_modulename = (is_admin()) ? 'page' : 'mjsmod';
            $MJTC_module = MJTC_request::MJTC_getVar($MJTC_modulename,'','');
            $MJTC_module = MJTC_majesticsupportphplib::MJTC_str_replace('majesticsupport_', '', $MJTC_module);
            if($MJTC_module != ''){
                MJTC_includer::MJTC_include_file($MJTC_module);
                $MJTC_class = 'MJTC_' . $MJTC_module . "Controller";
                $MJTC_action = MJTC_request::MJTC_getVar('task');
                $MJTC_obj = new $MJTC_class;
                $MJTC_obj->$MJTC_action();
            }else{
                error_log( print_r( $_REQUEST, true ) );// temporary code to get the case when problem occurs(there are errors in log but no way to find the case that causes them)
            }
        }
    }

}

$MJTC_formhandler = new MJTC_formhandler();
?>
