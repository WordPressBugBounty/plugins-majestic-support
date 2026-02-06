<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_emailtemplateController {

    function __construct() {
        self::handleRequest();
    }

    function handleRequest() {
        $layout = MJTC_request::MJTC_getLayout('mjslay', null, 'emailtemplates');
        majesticsupport::$_data['sanitized_args']['MJTC_nonce'] = esc_html(wp_create_nonce('MJTC_nonce'));
        if (self::canaddfile($layout)) {
            switch ($layout) {
                case 'admin_emailtemplates':
                    $tempfor = MJTC_request::MJTC_getVar('for', null, 'tk-nw');
                    $formid = MJTC_request::MJTC_getVar('formid', null, '');
                    $langcode = MJTC_request::MJTC_getVar('langcode', null, '');
                    majesticsupport::$_data[1] = $tempfor;
                    MJTC_includer::MJTC_getModel('emailtemplate')->getTemplate($tempfor, $formid, $langcode);
                    break;
                default:
                    exit;
            }
            $module = (is_admin()) ? 'page' : 'mjsmod';
            $module = MJTC_request::MJTC_getVar($module, null, 'emailtemplate');
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

    static function saveemailtemplate() {
        $id = MJTC_request::MJTC_getVar('id');
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $nonce, 'save-email-template-'.$id) ) {
            die( 'Security check Failed' );
        }
        if (!current_user_can('manage_options')) { //only admin can change it.
            return false;
        }
        $MJTC_data = MJTC_request::get('post');
        if (!empty($MJTC_data['lang_id'])) {
            if($MJTC_data['lang_id'] == '' || $MJTC_data['subject'] == '' || $MJTC_data['body'] == ''){
                MJTC_message::MJTC_setMessage(esc_html(__('Required Fields are not filled', 'majestic-support')), 'error');
            }else{
                MJTC_includer::MJTC_getModel('multilanguageemailtemplates')->storeMultiLanguageEmailTemplate($MJTC_data);
            }
        }else{
            MJTC_includer::MJTC_getModel('emailtemplate')->storeEmailTemplate($MJTC_data);
        }
        $MJTC_url = admin_url("admin.php?page=majesticsupport_emailtemplate&for=" . MJTC_request::MJTC_getVar('for'));
        wp_safe_redirect($MJTC_url);
        exit;
    }

    static function savecustomemailtemplate() {
        $id = MJTC_request::MJTC_getVar('id');
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $nonce, 'save-form-email-template') ) {
            die( 'Security check Failed' );
        }
        if (!current_user_can('manage_options')) { //only admin can change it.
            return false;
        }
        $MJTC_data = MJTC_request::get('post');
        if (!empty($MJTC_data['language_id']) && in_array('multilanguageemailtemplates', majesticsupport::$_active_addons)) {
            MJTC_includer::MJTC_getModel('multilanguageemailtemplates')->storeMultiLanguageEmailTemplate($MJTC_data);
        } elseif(in_array('multiform', majesticsupport::$_active_addons)) {
            MJTC_includer::MJTC_getModel('multiform')->storeFormEmailTemplate($MJTC_data);
        } else {
            MJTC_message::MJTC_setMessage(esc_html(__('Required Field(s) are not filled', 'majestic-support')), 'error');
        }
        $MJTC_url = admin_url("admin.php?page=majesticsupport_emailtemplate&for=" . MJTC_request::MJTC_getVar('for'));
        wp_safe_redirect($MJTC_url);
        exit;
    }

    static function deleteformemailtemplate() {
        $id = MJTC_request::MJTC_getVar('templateid');
        $source = MJTC_request::MJTC_getVar('source');
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $nonce, 'delete-template-'.$id) ) {
            die( 'Security check Failed' );
        }
        MJTC_includer::MJTC_getModel('emailtemplate')->removeFormEmailTemplate($id, $source);
        $MJTC_url = admin_url("admin.php?page=majesticsupport_emailtemplate&for=" . MJTC_request::MJTC_getVar('for'));
        wp_safe_redirect($MJTC_url);
        exit;
    }

}

$emailtemplateController = new MJTC_emailtemplateController();
?>
