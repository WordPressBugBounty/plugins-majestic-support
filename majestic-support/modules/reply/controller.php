<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_replyController {

    function __construct() {
        self::handleRequest();
    }

    function handleRequest() {
        $MJTC_layout = MJTC_request::MJTC_getLayout('mjslay', null, 'replies');
        $MJTC_task = MJTC_request::MJTC_getLayout('task', null, 'replies_replies');
        majesticsupport::$_data['sanitized_args']['MJTC_nonce'] = esc_html(wp_create_nonce('MJTC_nonce'));
        if (self::canaddfile($MJTC_layout)) {
            $MJTC_module = (is_admin()) ? 'page' : 'mjsmod';
            $MJTC_module = MJTC_request::MJTC_getVar($MJTC_module, null, 'reply');
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

    static function savereply() {
        $MJTC_ticketid = MJTC_request::MJTC_getVar('ticketid');
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'save-reply-'.$MJTC_ticketid) ) {
            die( 'Security check Failed' );
        }
        $MJTC_data = MJTC_request::get('post');
        MJTC_includer::MJTC_getModel('reply')->storeReplies($MJTC_data);
        if (is_admin()) {
            $MJTC_url = admin_url("admin.php?page=majesticsupport_ticket&mjslay=ticketdetail&majesticsupportid=" . $MJTC_ticketid);
        } else {
            $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'ticket','mjslay'=>'ticketdetail','majesticsupportid'=>$MJTC_ticketid));
        }
        wp_safe_redirect($MJTC_url);
        exit;
    }

    static function saveeditedreply() {
        $MJTC_reply_tikcetid = MJTC_request::MJTC_getVar('reply-tikcetid');
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'save-edited-reply-'.$MJTC_reply_tikcetid) ) {
            die( 'Security check Failed' );
        }
        $MJTC_data = MJTC_request::get('post');
        MJTC_includer::MJTC_getModel('reply')->editReply($MJTC_data);
        if (current_user_can('manage_options') || current_user_can('ms_support_ticket_tickets')) {
            $MJTC_url = admin_url("admin.php?page=majesticsupport_ticket&mjslay=ticketdetail&majesticsupportid=" . esc_attr($MJTC_data['reply-tikcetid']));
        } else {
            $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'ticket','mjslay'=>'ticketdetail','majesticsupportid'=>$MJTC_data['reply-tikcetid'],'mspageid'=>majesticsupport::getPageid()));
        }
        wp_safe_redirect($MJTC_url);
        exit;
    }

    static function saveeditedtime() {
        $MJTC_data = MJTC_request::get('post');
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'save-edited-time-reply-'.$MJTC_data['reply-tikcetid']) ) {
            die( 'Security check Failed' );
        }
        if(!in_array('timetracking', majesticsupport::$_active_addons)){
            return;
        }
        MJTC_includer::MJTC_getModel('timetracking')->editTime($MJTC_data);
        if (current_user_can('manage_options') || current_user_can('ms_support_ticket_tickets')) {
            $MJTC_url = admin_url("admin.php?page=majesticsupport_ticket&mjslay=ticketdetail&majesticsupportid=" . esc_attr($MJTC_data['reply-tikcetid']));
        } else {
            $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'ticket','mjslay'=>'ticketdetail','majesticsupportid'=>$MJTC_data['reply-tikcetid'],'mspageid'=>majesticsupport::getPageid()));
        }
        wp_safe_redirect($MJTC_url);
        exit;
    }

}

$MJTC_replyController = new MJTC_replyController();
?>
