<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_replyController {

    function __construct() {
        self::handleRequest();
    }

    function handleRequest() {
        $task = MJTC_request::MJTC_getLayout('task', null, 'replies_replies');
        majesticsupport::$_data['sanitized_args']['MJTC_nonce'] = esc_html(wp_create_nonce('MJTC_nonce'));
        if (self::canaddfile()) {
            $module = (is_admin()) ? 'page' : 'mjsmod';
            $module = MJTC_request::MJTC_getVar($module, null, 'reply');
            $module = MJTC_majesticsupportphplib::MJTC_str_replace('majesticsupport_', '', $module);
            MJTC_includer::MJTC_include_file($layout, $module);
        }
    }

    function canaddfile() {
        $nonce_value = MJTC_request::MJTC_getVar('MJTC_nonce');
        if ( wp_verify_nonce( $nonce_value, 'MJTC_nonce') ) {
            if (isset($_POST['form_request']) && $_POST['form_request'] == 'majesticsupport')
                return false;
            elseif (isset($_GET['action']) && $_GET['action'] == 'mstask')
                return false;
            else
                return true;
        }
    }

    static function savereply() {
        $ticketid = MJTC_request::MJTC_getVar('ticketid');
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $nonce, 'save-reply-'.$ticketid) ) {
            die( 'Security check Failed' );
        }
        $data = MJTC_request::get('post');
        MJTC_includer::MJTC_getModel('reply')->storeReplies($data);
        if (is_admin()) {
            $url = admin_url("admin.php?page=majesticsupport_ticket&mjslay=ticketdetail&majesticsupportid=" . MJTC_request::MJTC_getVar('ticketid'));
        } else {
            $url = majesticsupport::makeUrl(array('mjsmod'=>'ticket','mjslay'=>'ticketdetail','majesticsupportid'=>MJTC_request::MJTC_getVar('ticketid')));
        }
        wp_redirect($url);
        exit;
    }

    static function saveeditedreply() {
        $reply_tikcetid = MJTC_request::MJTC_getVar('reply-tikcetid');
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $nonce, 'save-edited-reply-'.$reply_tikcetid) ) {
            die( 'Security check Failed' );
        }
        $data = MJTC_request::get('post');
        MJTC_includer::MJTC_getModel('reply')->editReply($data);
        if (current_user_can('manage_options') || current_user_can('ms_support_ticket_tickets')) {
            $url = admin_url("admin.php?page=majesticsupport_ticket&mjslay=ticketdetail&majesticsupportid=" . $data['reply-tikcetid']);
        } else {
            $url = majesticsupport::makeUrl(array('mjsmod'=>'ticket','mjslay'=>'ticketdetail','majesticsupportid'=>$data['reply-tikcetid'],'mspageid'=>majesticsupport::getPageid()));
        }
        wp_redirect($url);
        exit;
    }

    static function saveeditedtime() {
        $reply_tikcetid = MJTC_request::MJTC_getVar('reply-tikcetid');
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $nonce, 'save-edited-time-reply-'.$reply_tikcetid) ) {
            die( 'Security check Failed' );
        }
        if(!in_array('timetracking', majesticsupport::$_active_addons)){
            return;
        }
        $data = MJTC_request::get('post');
        MJTC_includer::MJTC_getModel('timetracking')->editTime($data);
        if (current_user_can('manage_options') || current_user_can('ms_support_ticket_tickets')) {
            $url = admin_url("admin.php?page=majesticsupport_ticket&mjslay=ticketdetail&majesticsupportid=" . $data['reply-tikcetid']);
        } else {
            $url = majesticsupport::makeUrl(array('mjsmod'=>'ticket','mjslay'=>'ticketdetail','majesticsupportid'=>$data['reply-tikcetid'],'mspageid'=>majesticsupport::getPageid()));
        }
        wp_redirect($url);
        exit;
    }

}

$replyController = new MJTC_replyController();
?>
