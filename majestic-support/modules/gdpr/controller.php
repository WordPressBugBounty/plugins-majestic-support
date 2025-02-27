<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_gdprController {

    function __construct() {
        self::handleRequest();
    }

    function handleRequest() {
        $layout = MJTC_request::MJTC_getLayout('mjslay', null, 'gdpr');
        majesticsupport::$_data['sanitized_args']['MJTC_nonce'] = esc_html(wp_create_nonce('MJTC_nonce'));
        if (self::canaddfile()) {
            switch ($layout) {
                case 'admin_gdprfields':
                    MJTC_includer::MJTC_getModel('gdpr')->getGDPRFeilds();
                    break;
                case 'admin_addgdprfield':
                    $id = MJTC_request::MJTC_getVar('majesticsupportid');
                    MJTC_includer::MJTC_getModel('fieldordering')->getUserFieldbyId($id,3);
                    break;
                case 'admin_erasedatarequests':
                    MJTC_includer::MJTC_getModel('gdpr')->getEraseDataRequests();
                    break;
                case 'adderasedatarequest':
                    MJTC_includer::MJTC_getModel('gdpr')->getUserEraseDataRequest();
                    break;
                default:
                    exit;
            }
            $module = (is_admin()) ? 'page' : 'mjsmod';
            $module = MJTC_request::MJTC_getVar($module, null, 'gdpr');
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

    static function savegdprfield() {
        $id = MJTC_request::MJTC_getVar('id');
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $nonce, 'save-gdprfield-'.$id) ) {
            die( 'Security check Failed' );
        }
        $data = MJTC_request::get('post');
        MJTC_includer::MJTC_getModel('fieldordering')->storeUserField($data);
        $url = admin_url("admin.php?page=majesticsupport_gdpr&mjslay=gdprfields");
        wp_redirect($url);
        exit;
    }

    static function saveusereraserequest() {
        $id = MJTC_request::MJTC_getVar('id');
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $nonce, 'save-usereraserequest-'.$id) ) {
            die( 'Security check Failed' );
        }
        $data = MJTC_request::get('post');
        if($data['subject'] == "" || $data['message'] == "") {
            MJTC_formfield::MJTC_setFormData($data);
            MJTC_message::MJTC_setMessage(esc_html(__('Please fill required fields.', 'majestic-support')), 'error');
        } else {
            MJTC_includer::MJTC_getModel('gdpr')->storeUserEraseRequest($data);
        }
        $url = majesticsupport::makeUrl(array('mjsmod'=>'gdpr', 'mjslay'=>'adderasedatarequest'));
        
        wp_redirect($url);
        exit;
    }

    static function deletegdpr() {
        $id = MJTC_request::MJTC_getVar('gdprid');
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $nonce, 'delete-gdpr-'.$id) ) {
            die( 'Security check Failed' );
        }
        MJTC_includer::MJTC_getModel('fieldordering')->deleteUserField($id);
        if (is_admin()) {
            $url = admin_url("admin.php?page=majesticsupport_gdpr&mjslay=gdprfields");
        } else {
            $url = majesticsupport::makeUrl(array('mjsmod'=>'gdpr', 'mjslay'=>'adderasedatarequest'));
        }
        wp_redirect($url);
        exit;
    }

    static function removeusereraserequest() {
        $id = MJTC_request::MJTC_getVar('majesticsupportid');
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $nonce, 'delete-usereraserequest-'.$id) ) {
            die( 'Security check Failed' );
        }
        MJTC_includer::MJTC_getModel('gdpr')->deleteUserEraseRequest($id);
        $url = majesticsupport::makeUrl(array('mjsmod'=>'gdpr', 'mjslay'=>'adderasedatarequest'));
        wp_redirect($url);
        exit;
    }

    static function exportusereraserequest() {
        // get current user ID by function due to security reasons
        $uid  = MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid();
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');

        if (! wp_verify_nonce( $nonce, 'export-usereraserequest-'.$uid) ) {
            die( 'Security check Failed' );
        }
        $return_value = MJTC_includer::MJTC_getModel('gdpr')->setUserExportByuid($uid);
        if (!empty($return_value)) {
            // Push the report now!
            $msg = esc_html(__('User Data','majestic-support'));
            $name = 'export-overalll-reports';
            header("Content-type: application/octet-stream");
            header("Content-Disposition: attachment; filename=" . esc_attr($name) . ".xls");
            header("Pragma: no-cache");
            header("Expires: 0");
            header("Lacation: excel.htm?id=yes");
            print wp_kses($return_value, MJTC_ALLOWED_TAGS);
            exit;
        }
        MJTC_message::MJTC_setMessage(esc_html(__('There was no record found', 'majestic-support')), 'error');
        $url = majesticsupport::makeUrl(array('mjsmod'=>'gdpr', 'mjslay'=>'adderasedatarequest'));
        wp_redirect($url);
        die();
    }

    static function deleteuserdata() {
        $uid  = MJTC_request::MJTC_getVar('majesticsupportid');
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');

        if (! wp_verify_nonce( $nonce, 'delete-userdata-'.$uid) ) {
            die( 'Security check Failed' );
        }
        $return_value = MJTC_includer::MJTC_getModel('gdpr')->deleteUserData($uid);
        $url = admin_url("admin.php?page=majesticsupport_gdpr&mjslay=erasedatarequests");
        wp_redirect($url);
        die();
    }

    static function eraseidentifyinguserdata() {
        $uid  = MJTC_request::MJTC_getVar('majesticsupportid');
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $nonce, 'erase-userdata-'.$uid) ) {
            die( 'Security check Failed' );
        }
        $return_value = MJTC_includer::MJTC_getModel('gdpr')->anonymizeUserData($uid);
        $url = admin_url("admin.php?page=majesticsupport_gdpr&mjslay=erasedatarequests");
        wp_redirect($url);
        die();
    }

}
$gdprController = new MJTC_gdprController();
?>
