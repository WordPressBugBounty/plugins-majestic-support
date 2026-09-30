<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_priorityController {

    function __construct() {
        self::handleRequest();
    }

    function handleRequest() {
        $MJTC_layout = MJTC_request::MJTC_getLayout('mjslay', null, 'priorities');
        if (self::canaddfile($MJTC_layout)) {
            switch ($MJTC_layout) {
                case 'admin_priorities':
                    MJTC_includer::MJTC_getModel('priority')->getPriorities();
                    break;
                case 'admin_addpriority':
                    $MJTC_id = MJTC_request::MJTC_getVar('majesticsupportid', 'get');
                    MJTC_includer::MJTC_getModel('priority')->getPriorityForForm($MJTC_id);
                    break;
                default:
                    exit;
            }
            $MJTC_module = (is_admin()) ? 'page' : 'mjsmod';
            $MJTC_module = MJTC_request::MJTC_getVar($MJTC_module, null, 'priority');
            $MJTC_module = MJTC_majesticsupportphplib::MJTC_str_replace('majesticsupport_', '', $MJTC_module);
            MJTC_includer::MJTC_include_file($MJTC_layout, $MJTC_module);
        }
    }

    function canaddfile($MJTC_layout) {
        // Decides only whether a layout is rendered: never while a task is being
        // dispatched, never an admin_ layout on the front end. Who may see a layout
        // is decided in handleRequest(). (The nonce once checked here was created
        // by the same request, so it could not fail.)
        {
            if (isset($_POST['form_request']) && $_POST['form_request'] == 'majesticsupport') {
                return false;
            } elseif (isset($_GET['action']) && $_GET['action'] == 'mstask') {
                return false;
            } else {
                if(!is_admin() && MJTC_majesticsupportphplib::MJTC_strpos($MJTC_layout, 'admin_') === 0){
                    return false;
                }
                return true;
            }
        }
    }

    static function savepriority() {
        $MJTC_id = MJTC_request::MJTC_getVar('id');
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'save-priority-'.$MJTC_id) ) {
            die( 'Security check Failed' );
        }
        if (!current_user_can('manage_options')) { //only admin can change it.
            return false;
        }
        $MJTC_data = MJTC_request::get('post');
        MJTC_includer::MJTC_getModel('priority')->storePriority($MJTC_data);
        if (is_admin()) {
            $MJTC_url = admin_url("admin.php?page=majesticsupport_priority&mjslay=priorities");
        } else {
            $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'priority','mjslay'=>'priorities'));
        }
        wp_safe_redirect($MJTC_url);
        exit;
    }

    static function deletepriority() {
        $MJTC_id = MJTC_request::MJTC_getVar('priorityid');
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'delete-priority-'.$MJTC_id) ) {
            die( 'Security check Failed' );
        }
        MJTC_includer::MJTC_getModel('priority')->removePriority($MJTC_id);
        if (is_admin()) {
            $MJTC_url = admin_url("admin.php?page=majesticsupport_priority&mjslay=priorities");
        } else {
            $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'priority','mjslay'=>'priorities'));
        }
        wp_safe_redirect($MJTC_url);
        exit;
    }

    static function makedefault() {
        $MJTC_id = MJTC_request::MJTC_getVar('priorityid');
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'make-default-'.$MJTC_id) ) {
            die( 'Security check Failed' );
        }
        MJTC_includer::MJTC_getModel('priority')->makeDefault($MJTC_id);
        $MJTC_pagenum = MJTC_request::MJTC_getVar('pagenum');
        $MJTC_url = "admin.php?page=majesticsupport_priority&mjslay=priorities";
        if ($MJTC_pagenum)
            $MJTC_url .= '&pagenum=' . $MJTC_pagenum;
        wp_safe_redirect($MJTC_url);
        exit;
    }

    static function ordering() {
        $MJTC_id = MJTC_request::MJTC_getVar('priorityid');
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'ordering-'.$MJTC_id) ) {
            die( 'Security check Failed' );
        }
        MJTC_includer::MJTC_getModel('priority')->setOrdering($MJTC_id);
        $MJTC_pagenum = MJTC_request::MJTC_getVar('pagenum');
        $MJTC_url = "admin.php?page=majesticsupport_priority&mjslay=priorities";
        if ($MJTC_pagenum)
            $MJTC_url .= '&pagenum=' . $MJTC_pagenum;
        wp_safe_redirect($MJTC_url);
        exit;
    }

}

$MJTC_priorityController = new MJTC_priorityController();
?>
