<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_systemerrorController {

    function __construct() {
        self::handleRequest();
    }

    function handleRequest() {
        $layout = MJTC_request::MJTC_getLayout('mjslay', null, 'systemerrors');
        majesticsupport::$_data['sanitized_args']['MJTC_nonce'] = esc_html(wp_create_nonce('MJTC_nonce'));
        if (self::canaddfile()) {
            switch ($layout) {
                case 'admin_systemerrors':
                    MJTC_includer::MJTC_getModel('systemerror')->getSystemErrors();
                    break;

                case 'admin_addsystemerror':
                    $id = MJTC_request::MJTC_getVar('majesticsupportid', 'get');
                    MJTC_includer::MJTC_getModel('systemerror')->getsystemerrorForForm($id);
                    break;
                default:
                    exit;
            }
            $module = (is_admin()) ? 'page' : 'mjsmod';
            $module = MJTC_request::MJTC_getVar($module, null, 'systemerror');
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

    static function savesystemerror() {
        $data = MJTC_request::get('post');
        MJTC_includer::MJTC_getModel('systemerror')->storesystemerror($data);
        if (is_admin()) {
            $url = admin_url("admin.php?page=majesticsupport_systemerror&mjslay=systemerrors");
        } else {
            $url = majesticsupport::makeUrl(array('mjsmod'=>'systemerror','mjslay'=>'systemerrors'));
        }
        wp_redirect($url);
        exit;
    }

    static function deletesystemerror() {
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $nonce, 'delete-systemerror') ) {
            die( 'Security check Failed' );
        }
        $id = MJTC_request::MJTC_getVar('systemerrorid');
        MJTC_includer::MJTC_getModel('systemerror')->removeSystemError($id);
        if (is_admin()) {
            $url = admin_url("admin.php?page=majesticsupport_systemerror&mjslay=systemerrors");
        } else {
            $url = majesticsupport::makeUrl(array('mjsmod'=>'systemerror','mjslay'=>'systemerrors'));
        }
        wp_redirect($url);
        exit;
    }

}

$systemerrorController = new MJTC_systemerrorController();
?>
