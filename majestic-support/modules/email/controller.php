<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_emailController {

    function __construct() {
        self::handleRequest();
    }

    function handleRequest() {
        $layout = MJTC_request::MJTC_getLayout('mjslay', null, 'emails');
        majesticsupport::$_data['sanitized_args']['MJTC_nonce'] = esc_html(wp_create_nonce('MJTC_nonce'));
        if (self::canaddfile($layout)) {
            switch ($layout) {
                case 'admin_emails':
                    MJTC_includer::MJTC_getModel('email')->getEmails();
                    break;

                case 'admin_addemail':
                    $id = MJTC_request::MJTC_getVar('majesticsupportid', 'get');
                    MJTC_includer::MJTC_getModel('email')->getEmailForForm($id);
                    break;
                default:
                    exit;
            }
            $module = (is_admin()) ? 'page' : 'mjsmod';
            $module = MJTC_request::MJTC_getVar($module, null, 'email');
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

    static function saveemail() {
        $id = MJTC_request::MJTC_getVar('id');
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $nonce, 'save-email-'.$id) ) {
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
        $id = MJTC_request::MJTC_getVar('emailid');
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $nonce, 'delete-email-'.$id) ) {
            die( 'Security check Failed' );
        }
        MJTC_includer::MJTC_getModel('email')->removeEmail($id);
        if (is_admin()) {
            $MJTC_url = admin_url("admin.php?page=majesticsupport_email&mjslay=emails");
        } else {
            $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'email', 'mjslay'=>'emails'));
        }
        wp_safe_redirect($MJTC_url);
        exit;
    }

}

$emailController = new MJTC_emailController();
?>
