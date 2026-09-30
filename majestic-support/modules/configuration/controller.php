<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_configurationController {

    function __construct() {
        self::handleRequest();
    }

    function handleRequest() {
        $MJTC_layout = MJTC_request::MJTC_getLayout('mjslay', null, 'configurations');
        if (self::canaddfile($MJTC_layout)) {
            switch ($MJTC_layout) {
                case 'admin_configurations':
                    $msconfigid = MJTC_request::MJTC_getVar('msconfigid');
                    if (isset($msconfigid)) {
                        majesticsupport::$_data['msconfigid'] = $msconfigid;
                    }
                    $MJTC_ck = MJTC_includer::MJTC_getModel('configuration')->getCheckCronKey();
                    if ($MJTC_ck == false) {
                        MJTC_includer::MJTC_getModel('configuration')->genearateCronKey();
                    }
                    MJTC_includer::MJTC_getModel('configuration')->getConfigurations();
                    break;
                case 'admin_cronjoburl':
                    break;
                default:
                    exit;
            }
            $MJTC_module = (is_admin()) ? 'page' : 'mjsmod';
            $MJTC_module = MJTC_request::MJTC_getVar($MJTC_module, null, 'configuration');
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

    static function saveconfiguration() {
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'save-configuration') ) {
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
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'mjtc_configuration_nonce') ) {
             die( 'Security check Failed' );
        }
        if (!current_user_can('manage_options')) { //only admin can change it.
            return false;
        }
        $MJTC_result = MJTC_includer::MJTC_getModel('configuration')->storeAutoUpdateConfig();
        $MJTC_url = esc_url_raw(admin_url("admin.php?page=majesticsupport_premiumplugin&mjslay=addonstatus"));
        wp_safe_redirect($MJTC_url);
        die();
    }

}

$MJTC_configurationController = new MJTC_configurationController();
?>
