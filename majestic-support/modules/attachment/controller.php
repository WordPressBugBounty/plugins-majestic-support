<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_attachmentController {

    function __construct() {
        self::handleRequest();
    }

    function handleRequest() {
        $MJTC_layout = MJTC_request::MJTC_getLayout('mjslay', null, 'getattachments');
        majesticsupport::$_data['sanitized_args']['MJTC_nonce'] = esc_html(wp_create_nonce('MJTC_nonce'));
        if (self::canaddfile($MJTC_layout)) {
            switch ($MJTC_layout) {
                case 'getattachments':
                    $MJTC_id = MJTC_request::MJTC_getVar('majesticsupportid', 'get', null);
                    MJTC_includer::MJTC_getModel('replies')->getrepliesForForm($MJTC_id);
                    break;
                default:
                    exit;
            }
            $MJTC_module = (is_admin()) ? 'page' : 'mjsmod';
            $MJTC_module = MJTC_request::MJTC_getVar($MJTC_module, null, 'attachment');
            $MJTC_module = MJTC_majesticsupportphplib::MJTC_str_replace('majesticsupport_', '', $MJTC_module);
            MJTC_includer::MJTC_include_file($MJTC_layout, $MJTC_module);
        }
    }

    function canaddfile($MJTC_layout) {
        $MJTC_nonce_value = MJTC_request::MJTC_getVar('MJTC_nonce');
        if ( wp_verify_nonce( $MJTC_nonce_value, 'MJTC_nonce') ) {
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

    static function saveattachments() {
        $MJTC_data = MJTC_request::get('post');
        MJTC_includer::MJTC_getModel('attachment')->storeAttachments($MJTC_data);
        if (is_admin()) {
            $MJTC_url = admin_url("admin.php?page=majesticsupport_ticket&mjslay=ticketdetail&majesticsupportid=" . MJTC_request::MJTC_getVar('ticketid'));
        } else {
            $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'replies', 'mjslay'=>'replies'));
        }
        wp_safe_redirect($MJTC_url);
        exit;
    }

    static function deleteattachment() {
        $MJTC_id = MJTC_request::MJTC_getVar('id');
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'delete-attachement-'.$MJTC_id)  && !is_admin()) {
            die( 'Security check Failed' );
        }
        $MJTC_call_from = MJTC_request::MJTC_getVar('call_from','',1);
        MJTC_includer::MJTC_getModel('attachment')->removeAttachment($MJTC_id);
        if (is_admin()) {
            $MJTC_url = admin_url("admin.php?page=majesticsupport_ticket&mjslay=addticket&majesticsupportid=" . MJTC_request::MJTC_getVar('ticketid'));
        } else {
            if($MJTC_call_from == 2){
                $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'agent', 'mjslay'=>'staffaddticket','majesticsupportid'=>MJTC_request::MJTC_getVar('ticketid')));
            }else{
                $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'replies', 'mjslay'=>'replies'));
            }
        }
        wp_safe_redirect($MJTC_url);
        exit;
    }

}

$MJTC_attachmentController = new MJTC_attachmentController();
?>
