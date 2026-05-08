<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_postinstallationController {

    function __construct() {

        self::handleRequest();
    }

    function handleRequest() {
        $MJTC_layout = MJTC_request::MJTC_getLayout('mjslay', null, 'stepone');
        majesticsupport::$_data['sanitized_args']['MJTC_nonce'] = esc_html(wp_create_nonce('MJTC_nonce'));
        if($this->canaddfile($MJTC_layout)){
            switch ($MJTC_layout) {
                case 'admin_quickconfig':
                    MJTC_includer::MJTC_getModel('postinstallation')->getConfigurationValues();
                break;
                case 'admin_stepone':
                    MJTC_includer::MJTC_getModel('postinstallation')->getConfigurationValues();
                break;
                case 'admin_steptwo':
                    MJTC_includer::MJTC_getModel('postinstallation')->getConfigurationValues();
                break;
                case 'admin_stepthree':
                    MJTC_includer::MJTC_getModel('postinstallation')->getConfigurationValues();
                    majesticsupport::$_data[1] = MJTC_includer::MJTC_getModel('email')->getAllEmailsForCombobox();
                break;
                case 'admin_stepfour':
                    if(!in_array('feedback', majesticsupport::$_active_addons)){// to hanle show hide of feed back settings.
                        $MJTC_layout = 'admin_settingcomplete';
                    }
                    MJTC_includer::MJTC_getModel('postinstallation')->getConfigurationValues();
                break;
                case 'admin_settingcomplete':
                break;
                case 'admin_welcome':
                break;
                case 'admin_themedemodata':
                    majesticsupport::$_data['flag'] = MJTC_request::MJTC_getVar('flag');
                break;
                case 'admin_translationoption':
                    majesticsupport::$_data[0]['mstran'] = MJTC_includer::MJTC_getModel('majesticsupport')->getInstalledTranslationKey();
                    if(!majesticsupport::$_data[0]['mstran']){
                        if(!in_array('feedback', majesticsupport::$_active_addons)){// to handle show hide of feed back settings.
                            $MJTC_layout = 'admin_settingcomplete';
                        }else{
                            $MJTC_layout = 'admin_stepthree';
                        }
                    }
                break;
                default:
                    exit;
            }
            $MJTC_module = (is_admin()) ? 'page' : 'mjsmod';
            $MJTC_module = MJTC_request::MJTC_getVar($MJTC_module, null, 'postinstallation');
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

    function save(){
        if(!current_user_can('manage_options')){
            return false;
        }
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'save') ) {
            die( 'Security check Failed' );
        }
        $MJTC_data = MJTC_request::get('post');
        if($MJTC_data['step'] != 'translationoption'){
            $MJTC_result = MJTC_includer::MJTC_getModel('postinstallation')->storeConfigurations($MJTC_data);
        }
        $MJTC_url = admin_url("admin.php?page=majesticsupport_postinstallation&mjslay=steptwo");
        if($MJTC_data['step'] == 2){
            $MJTC_url = admin_url("admin.php?page=majesticsupport_postinstallation&mjslay=stepthree");
        }
        if($MJTC_data['step'] == 3){
            $MJTC_url = admin_url("admin.php?page=majesticsupport_postinstallation&mjslay=stepfour");
        }
        if($MJTC_data['step'] == 4){
            $MJTC_url = admin_url("admin.php?page=majesticsupport_postinstallation&mjslay=settingcomplete");
        }
        if($MJTC_data['step'] == 'translationoption'){
            $MJTC_url = admin_url("admin.php?page=majesticsupport_postinstallation&mjslay=stepfour");
        }

        wp_safe_redirect($MJTC_url);
        exit();
    }

    function savesampledata(){
        $MJTC_data = MJTC_request::get('post');
        $MJTC_sampledata = $MJTC_data['sampledata'];
        $MJTC_jsmenu = $MJTC_data['jsmenu'];
        $MJTC_empmenu = $MJTC_data['empmenu'];
        $MJTC_url = admin_url("admin.php?page=majesticsupport_jslearnmanager");
        $MJTC_result = MJTC_includer::MJTC_getModel('postinstallation')->installSampleData($MJTC_sampledata);
        wp_safe_redirect($MJTC_url);
        exit();
    }
}
$MJTC_postinstallationController = new MJTC_postinstallationController();
?>
