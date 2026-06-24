<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_formhandler {

    function __construct() {
        add_action('init', array($this, 'MJTC_checkFormRequest'));
        add_action('init', array($this, 'MJTC_checkDeleteRequest'));
    }

    private function MJTC_dispatch_controller($MJTC_module, $MJTC_task) {
        $MJTC_module = is_string($MJTC_module) ? sanitize_key(MJTC_majesticsupportphplib::MJTC_str_replace('majesticsupport_', '', $MJTC_module)) : '';
        $MJTC_task = is_string($MJTC_task) ? trim($MJTC_task) : '';

        if (empty($MJTC_module) || empty($MJTC_task)) {
            return false;
        }

        if (!preg_match('/^[A-Za-z0-9_]+$/', $MJTC_module) || !preg_match('/^[A-Za-z0-9_]+$/', $MJTC_task)) {
            return false;
        }

        if (0 === strpos($MJTC_task, '__') || 0 === strpos($MJTC_task, '_')) {
            return false;
        }

        MJTC_includer::MJTC_include_file($MJTC_module);
        $MJTC_class = 'MJTC_' . $MJTC_module . 'Controller';

        if (!class_exists($MJTC_class)) {
            return false;
        }

        $MJTC_obj = new $MJTC_class;
        if (!is_callable(array($MJTC_obj, $MJTC_task))) {
            return false;
        }

        if (is_admin() && !current_user_can('ms_support_ticket') && !current_user_can('manage_options')) {
            wp_die(
                esc_html__('You are not allowed to access this resource.', 'majestic-support'),
                esc_html__('Access Denied', 'majestic-support'),
                array('response' => 403)
            );
        }

        call_user_func(array($MJTC_obj, $MJTC_task));
        return true;
    }

    /*
     * Handle POST form requests.
     */
    function MJTC_checkFormRequest() {
        $MJTC_formrequest = MJTC_request::MJTC_getVar('form_request', 'post');
        if ($MJTC_formrequest !== 'majesticsupport') {
            return;
        }

        $MJTC_page_id = absint(MJTC_request::MJTC_getVar('page_id', 'get'));
        majesticsupport::setPageID($MJTC_page_id);

        $MJTC_modulename = (is_admin()) ? 'page' : 'mjsmod';
        $MJTC_module = MJTC_request::MJTC_getVar($MJTC_modulename);
        $MJTC_task = MJTC_request::MJTC_getVar('task');

        $this->MJTC_dispatch_controller($MJTC_module, $MJTC_task);
    }

    /*
     * Handle GET task requests.
     */
    function MJTC_checkDeleteRequest() {
        $majesticsupport_action = MJTC_request::MJTC_getVar('action', 'get');
        if ('mstask' !== $majesticsupport_action) {
            return;
        }

        $MJTC_page_id = absint(MJTC_request::MJTC_getVar('page_id', 'get'));
        majesticsupport::setPageID($MJTC_page_id);

        $MJTC_modulename_key = (is_admin()) ? 'page' : 'mjsmod';
        $MJTC_module = MJTC_request::MJTC_getVar($MJTC_modulename_key, '', '');
        $MJTC_task = MJTC_request::MJTC_getVar('task');

        $this->MJTC_dispatch_controller($MJTC_module, $MJTC_task);
    }
}

$MJTC_formhandler = new MJTC_formhandler();
?>
