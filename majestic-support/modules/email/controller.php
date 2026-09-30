<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_emailController {

    function __construct() {
        self::handleRequest();
    }

    function handleRequest() {
        $MJTC_layout = MJTC_request::MJTC_getLayout('mjslay', null, 'emails');
        if (self::canaddfile($MJTC_layout)) {
            switch ($MJTC_layout) {
                case 'admin_emails':
                    MJTC_includer::MJTC_getModel('email')->getEmails();
                    break;

                case 'admin_addemail':
                    $MJTC_id = MJTC_request::MJTC_getVar('majesticsupportid', 'get');
                    MJTC_includer::MJTC_getModel('email')->getEmailForForm($MJTC_id);
                    break;
                default:
                    exit;
            }
            $MJTC_module = (is_admin()) ? 'page' : 'mjsmod';
            $MJTC_module = MJTC_request::MJTC_getVar($MJTC_module, null, 'email');
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

    static function saveemail() {
        $MJTC_id = MJTC_request::MJTC_getVar('id');
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'save-email-'.$MJTC_id) ) {
            die( 'Security check Failed' );
        }
        if (!current_user_can('manage_options')) { //only admin can change it.
            return false;
        }
        $MJTC_data = MJTC_request::get('post');
        MJTC_includer::MJTC_getModel('email')->storeEmail($MJTC_data);
        if (is_admin()) {
            $MJTC_url = admin_url("admin.php?page=majesticsupport_email&mjslay=emails");
        } else {
            $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'email', 'mjslay'=>'emails'));
        }
        wp_safe_redirect($MJTC_url);
        exit;
    }

    static function deleteemail() {
        $MJTC_id = MJTC_request::MJTC_getVar('emailid');
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'delete-email-'.$MJTC_id) ) {
            die( 'Security check Failed' );
        }
        MJTC_includer::MJTC_getModel('email')->removeEmail($MJTC_id);
        if (is_admin()) {
            $MJTC_url = admin_url("admin.php?page=majesticsupport_email&mjslay=emails");
        } else {
            $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'email', 'mjslay'=>'emails'));
        }
        wp_safe_redirect($MJTC_url);
        exit;
    }

}

$MJTC_emailController = new MJTC_emailController();
?>
