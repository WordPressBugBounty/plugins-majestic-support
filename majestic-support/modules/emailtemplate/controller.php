<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_emailtemplateController {

    function __construct() {
        self::handleRequest();
    }

    function handleRequest() {
        $MJTC_layout = MJTC_request::MJTC_getLayout('mjslay', null, 'emailtemplates');
        majesticsupport::$_data['sanitized_args']['MJTC_nonce'] = esc_html(wp_create_nonce('MJTC_nonce'));
        if (self::canaddfile($MJTC_layout)) {
            switch ($MJTC_layout) {
                case 'admin_emailtemplates':
                    $tempfor = MJTC_request::MJTC_getVar('for', null, 'tk-nw');
                    $MJTC_formid = MJTC_request::MJTC_getVar('formid', null, '');
                    $MJTC_langcode = MJTC_request::MJTC_getVar('langcode', null, '');
                    majesticsupport::$_data[1] = $tempfor;
                    MJTC_includer::MJTC_getModel('emailtemplate')->getTemplate($tempfor, $MJTC_formid, $MJTC_langcode);
                    break;
                default:
                    exit;
            }
            $MJTC_module = (is_admin()) ? 'page' : 'mjsmod';
            $MJTC_module = MJTC_request::MJTC_getVar($MJTC_module, null, 'emailtemplate');
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

    static function saveemailtemplate() {
        $MJTC_id = MJTC_request::MJTC_getVar('id');
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'save-email-template-'.$MJTC_id) ) {
            die( 'Security check Failed' );
        }
        if (!current_user_can('manage_options')) { //only admin can change it.
            return false;
        }
        $MJTC_data = MJTC_request::get('post');
        if (!empty($MJTC_data['lang_id'])) {
            if($MJTC_data['lang_id'] == '' || $MJTC_data['subject'] == '' || $MJTC_data['body'] == ''){
                MJTC_message::MJTC_setMessage(esc_html(__('Required Field(s) are not filled', 'majestic-support')), 'error');
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
        $MJTC_id = MJTC_request::MJTC_getVar('id');
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'save-form-email-template') ) {
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
        $MJTC_id = MJTC_request::MJTC_getVar('templateid');
        $MJTC_source = MJTC_request::MJTC_getVar('source');
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'delete-template-'.$MJTC_id) ) {
            die( 'Security check Failed' );
        }
        MJTC_includer::MJTC_getModel('emailtemplate')->removeFormEmailTemplate($MJTC_id, $MJTC_source);
        $MJTC_url = admin_url("admin.php?page=majesticsupport_emailtemplate&for=" . MJTC_request::MJTC_getVar('for'));
        wp_safe_redirect($MJTC_url);
        exit;
    }

}

$MJTC_emailtemplateController = new MJTC_emailtemplateController();
?>
