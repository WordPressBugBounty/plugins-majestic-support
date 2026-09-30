<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_systemerrorController {

    function __construct() {
        self::handleRequest();
    }

    function handleRequest() {
        $MJTC_layout = MJTC_request::MJTC_getLayout('mjslay', null, 'systemerrors');
        if (self::canaddfile($MJTC_layout)) {
            switch ($MJTC_layout) {
                case 'admin_systemerrors':
                    MJTC_includer::MJTC_getModel('systemerror')->getSystemErrors();
                    break;

                case 'admin_addsystemerror':
                    $MJTC_id = MJTC_request::MJTC_getVar('majesticsupportid', 'get');
                    MJTC_includer::MJTC_getModel('systemerror')->getsystemerrorForForm($MJTC_id);
                    break;
                default:
                    exit;
            }
            $MJTC_module = (is_admin()) ? 'page' : 'mjsmod';
            $MJTC_module = MJTC_request::MJTC_getVar($MJTC_module, null, 'systemerror');
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

    static function savesystemerror() {
        if (!current_user_can('manage_options')) {
            MJTC_access::MJTC_deny();
        }
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'save-systemerror') ) {
            die( 'Security check Failed' );
        }
        $MJTC_data = MJTC_request::get('post');
        MJTC_includer::MJTC_getModel('systemerror')->storesystemerror($MJTC_data);
        if (is_admin()) {
            $MJTC_url = admin_url("admin.php?page=majesticsupport_systemerror&mjslay=systemerrors");
        } else {
            $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'systemerror','mjslay'=>'systemerrors'));
        }
        wp_safe_redirect($MJTC_url);
        exit;
    }

    static function deletesystemerror() {
        $MJTC_id = MJTC_request::MJTC_getVar('systemerrorid');
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'delete-systemerror-'.$MJTC_id) ) {
            die( 'Security check Failed' );
        }
        MJTC_includer::MJTC_getModel('systemerror')->removeSystemError($MJTC_id);
        if (is_admin()) {
            $MJTC_url = admin_url("admin.php?page=majesticsupport_systemerror&mjslay=systemerrors");
        } else {
            $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'systemerror','mjslay'=>'systemerrors'));
        }
        wp_safe_redirect($MJTC_url);
        exit;
    }

}

$MJTC_systemerrorController = new MJTC_systemerrorController();
?>
