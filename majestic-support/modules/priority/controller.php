<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_priorityController {

    function __construct() {
        self::handleRequest();
    }

    function handleRequest() {
        $layout = MJTC_request::MJTC_getLayout('mjslay', null, 'priorities');
        majesticsupport::$_data['sanitized_args']['MJTC_nonce'] = esc_html(wp_create_nonce('MJTC_nonce'));
        if (self::canaddfile($layout)) {
            switch ($layout) {
                case 'admin_priorities':
                    MJTC_includer::MJTC_getModel('priority')->getPriorities();
                    break;
                case 'admin_addpriority':
                    $id = MJTC_request::MJTC_getVar('majesticsupportid', 'get');
                    MJTC_includer::MJTC_getModel('priority')->getPriorityForForm($id);
                    break;
                default:
                    exit;
            }
            $module = (is_admin()) ? 'page' : 'mjsmod';
            $module = MJTC_request::MJTC_getVar($module, null, 'priority');
            $module = MJTC_majesticsupportphplib::MJTC_str_replace('majesticsupport_', '', $module);
            MJTC_includer::MJTC_include_file($layout, $module);
        }
    }

    function canaddfile($layout) {
        $nonce_value = MJTC_request::MJTC_getVar('MJTC_nonce');
        if ( wp_verify_nonce( $nonce_value, 'MJTC_nonce') ) {
            if (isset($_POST['form_request']) && $_POST['form_request'] == 'majesticsupport') {
                return false;
            } elseif (isset($_GET['action']) && $_GET['action'] == 'mstask') {
                return false;
            } else {
                if(!is_admin() && MJTC_majesticsupportphplib::MJTC_strpos($layout, 'admin_') === 0){
                    return false;
                }
                return true;
            }
        }
    }

    static function savepriority() {
        $id = MJTC_request::MJTC_getVar('id');
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $nonce, 'save-priority-'.$id) ) {
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
        $id = MJTC_request::MJTC_getVar('priorityid');
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $nonce, 'delete-priority-'.$id) ) {
            die( 'Security check Failed' );
        }
        MJTC_includer::MJTC_getModel('priority')->removePriority($id);
        if (is_admin()) {
            $MJTC_url = admin_url("admin.php?page=majesticsupport_priority&mjslay=priorities");
        } else {
            $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'priority','mjslay'=>'priorities'));
        }
        wp_safe_redirect($MJTC_url);
        exit;
    }

    static function makedefault() {
        $id = MJTC_request::MJTC_getVar('priorityid');
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $nonce, 'make-default-'.$id) ) {
            die( 'Security check Failed' );
        }
        MJTC_includer::MJTC_getModel('priority')->makeDefault($id);
        $MJTC_pagenum = MJTC_request::MJTC_getVar('pagenum');
        $MJTC_url = "admin.php?page=majesticsupport_priority&mjslay=priorities";
        if ($MJTC_pagenum)
            $MJTC_url .= '&pagenum=' . $MJTC_pagenum;
        wp_safe_redirect($MJTC_url);
        exit;
    }

    static function ordering() {
        $id = MJTC_request::MJTC_getVar('priorityid');
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $nonce, 'ordering-'.$id) ) {
            die( 'Security check Failed' );
        }
        MJTC_includer::MJTC_getModel('priority')->setOrdering($id);
        $MJTC_pagenum = MJTC_request::MJTC_getVar('pagenum');
        $MJTC_url = "admin.php?page=majesticsupport_priority&mjslay=priorities";
        if ($MJTC_pagenum)
            $MJTC_url .= '&pagenum=' . $MJTC_pagenum;
        wp_safe_redirect($MJTC_url);
        exit;
    }

}

$priorityController = new MJTC_priorityController();
?>
