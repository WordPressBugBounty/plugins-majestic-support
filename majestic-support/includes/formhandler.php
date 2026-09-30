<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_formhandler {

    /*
     * The core tasks a customer or visitor may start from the help-desk pages.
     * Each still verifies its own nonce and ownership; this list only decides
     * who may reach it at all. Every other core task is help-desk work and
     * needs a worker (see MJTC_access::MJTC_isWorker()).
     */
    private static $MJTC_customer_tasks = array(
        'ticket' => array(
            'saveticket',
            'showticketstatus',
            'closeticket',
            'reopenticket',
            'deleteticket',
            'downloadbyid',
            'downloadbyname',
            'downloadall',
            'downloadallforreply',
        ),
        'reply' => array(
            'savereply',
        ),
        'gdpr' => array(
            'saveusereraserequest',
            'removeusereraserequest',
            'exportusereraserequest',
        ),
    );

    function __construct() {
        add_action('init', array($this, 'MJTC_checkFormRequest'));
        add_action('init', array($this, 'MJTC_checkDeleteRequest'));
    }

    /*
     * Is this module served by this plugin rather than by a Majestic Support
     * add-on? Add-ons are separate plugins whose controllers carry their own
     * checks; MJTC_includer loads them in preference to a core module of the
     * same name, so the same test is applied here.
     */
    private function MJTC_isCoreModule($MJTC_module) {
        if (in_array($MJTC_module, majesticsupport::$_active_addons)) {
            return false;
        }
        return file_exists(MJTC_PLUGIN_PATH . 'modules/' . $MJTC_module . '/controller.php');
    }

    /*
     * May the current user reach this task at all?
     *
     * In wp-admin, only help-desk administrators, as before. On the front end
     * a core task is open to workers, and to everybody else only when it is on
     * the customer list above.
     */
    private function MJTC_mayDispatch($MJTC_module, $MJTC_task) {
        if (is_admin()) {
            return current_user_can('ms_support_ticket') || current_user_can('manage_options');
        }
        if (!$this->MJTC_isCoreModule($MJTC_module)) {
            return true;
        }
        if (isset(self::$MJTC_customer_tasks[$MJTC_module]) && in_array(strtolower($MJTC_task), self::$MJTC_customer_tasks[$MJTC_module], true)) {
            return true;
        }
        return MJTC_access::MJTC_isWorker();
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

        if (!$this->MJTC_mayDispatch($MJTC_module, $MJTC_task)) {
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
