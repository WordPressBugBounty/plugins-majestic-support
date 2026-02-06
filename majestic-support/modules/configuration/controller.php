<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_configurationController {

    function __construct() {
        self::handleRequest();
    }

    function handleRequest() {
        $layout = MJTC_request::MJTC_getLayout('mjslay', null, 'configurations');
        majesticsupport::$_data['sanitized_args']['MJTC_nonce'] = esc_html(wp_create_nonce('MJTC_nonce'));
        if (self::canaddfile($layout)) {
            switch ($layout) {
                case 'admin_configurations':
                    $msconfigid = MJTC_request::MJTC_getVar('msconfigid');
                    if (isset($msconfigid)) {
                        majesticsupport::$_data['msconfigid'] = $msconfigid;
                    }
                    $ck = MJTC_includer::MJTC_getModel('configuration')->getCheckCronKey();
                    if ($ck == false) {
                        MJTC_includer::MJTC_getModel('configuration')->genearateCronKey();
                    }
                    MJTC_includer::MJTC_getModel('configuration')->getConfigurations();
                    break;
                case 'admin_cronjoburl':
                    break;
                default:
                    exit;
            }
            $module = (is_admin()) ? 'page' : 'mjsmod';
            $module = MJTC_request::MJTC_getVar($module, null, 'configuration');
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

    static function saveconfiguration() {
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $nonce, 'save-configuration') ) {
            die( 'Security check Failed' );
        }
        if (!current_user_can('manage_options')) { //only admin can change it.
            return false;
        }
        $MJTC_data = MJTC_request::get('post');
        MJTC_includer::MJTC_getModel('configuration')->storeConfiguration($MJTC_data);
        if (is_admin()) {
            $MJTC_url = admin_url("admin.php?page=majesticsupport_configuration&msconfigid=general");
        }
        if(isset($MJTC_data['call_from']) && $MJTC_data['call_from'] == 'notification' && is_admin()){
            $MJTC_url = admin_url("admin.php?page=majesticsupport_web-notification-setting");    
        }
        wp_safe_redirect($MJTC_url);
        exit;
    }

    // function to handle auto update configuration
    function saveautoupdateconfiguration() {
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $nonce, 'mjtc_configuration_nonce') ) {
             die( 'Security check Failed' );
        }
        if (!current_user_can('manage_options')) { //only admin can change it.
            return false;
        }
        $result = MJTC_includer::MJTC_getModel('configuration')->storeAutoUpdateConfig();
        $MJTC_url = esc_url_raw(admin_url("admin.php?page=majesticsupport_premiumplugin&mjslay=addonstatus"));
        wp_safe_redirect($MJTC_url);
        die();
    }

}

$configurationController = new MJTC_configurationController();
?>
