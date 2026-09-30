<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_smartreplyController {

    function __construct() {
        self::handleRequest();
    }

    function handleRequest() {
        $MJTC_layout = MJTC_request::MJTC_getLayout('mjslay', null, 'smartreplies');
        if (self::canaddfile($MJTC_layout)) {
            switch ($MJTC_layout) {
                case 'admin_smartreplies':
                case 'smartreplies':
                    // Administrators, or add-on staff granted the task. Nobody else.
                    majesticsupport::$_data['permission_granted'] = MJTC_access::MJTC_staffMay('View Smart Reply');
                    if (majesticsupport::$_data['permission_granted']) {
                        MJTC_includer::MJTC_getModel('smartreply')->getSmartreplyies();
                    }
                    break;
                case 'admin_addsmartreply':
                case 'addsmartreply':
                    $MJTC_id = MJTC_request::MJTC_getVar('majesticsupportid');
                    // Administrators, or add-on staff granted the task. Nobody else.
                    $MJTC_per_task = ($MJTC_id == null) ? 'Add Smart Reply' : 'Edit Smart Reply';
                    majesticsupport::$_data['permission_granted'] = MJTC_access::MJTC_staffMay($MJTC_per_task);
                    if (majesticsupport::$_data['permission_granted']) {
                        MJTC_includer::MJTC_getModel('smartreply')->getSmartReplyForForm($MJTC_id);
                    }
                    break;
                default:
                    exit;
            }
            $MJTC_module = (is_admin()) ? 'page' : 'mjsmod';
            $MJTC_module = MJTC_request::MJTC_getVar($MJTC_module, null, 'smartreply');
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

    static function savesmartreply() {
        $MJTC_id = MJTC_request::MJTC_getVar('id');
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'save-smart-reply-'.$MJTC_id) ) {
            die( 'Security check Failed' );
        }
        $MJTC_data = MJTC_request::get('post');
        MJTC_includer::MJTC_getModel('smartreply')->storeSmartReply($MJTC_data);
        if (is_admin()) {
            $MJTC_url = admin_url("admin.php?page=majesticsupport_smartreply&mjslay=smartreplies");
        } else {
            $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'smartreply','mjslay'=>'smartreplies'));
        }
        wp_safe_redirect($MJTC_url);
        exit;
    }

    static function deletesmartreply() {
        $MJTC_id = MJTC_request::MJTC_getVar('smartreplyid');
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'delete-smartreply-'.$MJTC_id) ) {
            die( 'Security check Failed' );
        }
        MJTC_includer::MJTC_getModel('smartreply')->removeSmartreply($MJTC_id);
        if (is_admin()) {
            $MJTC_url = admin_url("admin.php?page=majesticsupport_smartreply&mjslay=smartreplies");
        } else {
            $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'smartreply','mjslay'=>'smartreplies'));
        }
        wp_safe_redirect($MJTC_url);
        exit;
    }

}

$MJTC_smartreplyController = new MJTC_smartreplyController();
?>
