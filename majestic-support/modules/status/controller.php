<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_statusController {

    function __construct() {
        self::handleRequest();
    }

    function handleRequest() {
        $layout = MJTC_request::MJTC_getLayout('mjslay', null, 'statuses');
        majesticsupport::$_data['sanitized_args']['MJTC_nonce'] = esc_html(wp_create_nonce('MJTC_nonce'));
        if (self::canaddfile($layout)) {
            switch ($layout) {
                case 'admin_statuses':
                    MJTC_includer::MJTC_getModel('status')->getStatuses();
                    break;
                case 'admin_addstatus':
                    $id = MJTC_request::MJTC_getVar('majesticsupportid', 'get');
                    MJTC_includer::MJTC_getModel('status')->getStatusForForm($id);
                    break;
                default:
                    exit;
            }
            $module = (is_admin()) ? 'page' : 'mjsmod';
            $module = MJTC_request::MJTC_getVar($module, null, 'status');
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

    static function savestatus() {
        $id = MJTC_request::MJTC_getVar('id');
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $nonce, 'save-status-'.$id) ) {
            die( 'Security check Failed' );
        }
        if (!current_user_can('manage_options')) { //only admin can change it.
            return false;
        }
        $MJTC_data = MJTC_request::get('post');
        MJTC_includer::MJTC_getModel('status')->storeStatus($MJTC_data);
        if (is_admin()) {
            $MJTC_url = admin_url("admin.php?page=majesticsupport_status&mjslay=statuses");
        } else {
            $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'status','mjslay'=>'statuses'));
        }
        wp_safe_redirect($MJTC_url);
        exit;
    }

    static function deletestatus() {
        $id = MJTC_request::MJTC_getVar('statusid');
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $nonce, 'delete-status-'.$id) ) {
            die( 'Security check Failed' );
        }
        MJTC_includer::MJTC_getModel('status')->removeStatus($id);
        if (is_admin()) {
            $MJTC_url = admin_url("admin.php?page=majesticsupport_status&mjslay=statuses");
        } else {
            $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'status','mjslay'=>'statuses'));
        }
        wp_safe_redirect($MJTC_url);
        exit;
    }

    static function ordering() {
        $id = MJTC_request::MJTC_getVar('statusid');
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $nonce, 'ordering-'.$id) ) {
            die( 'Security check Failed' );
        }
        MJTC_includer::MJTC_getModel('status')->setOrdering($id);
        $MJTC_pagenum = MJTC_request::MJTC_getVar('pagenum');
        $MJTC_url = "admin.php?page=majesticsupport_status&mjslay=statuses";
        if ($MJTC_pagenum)
            $MJTC_url .= '&pagenum=' . $MJTC_pagenum;
        wp_safe_redirect($MJTC_url);
        exit;
    }

}

$statusController = new MJTC_statusController();
?>
