<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_postinstallationController {

    function __construct() {

        self::handleRequest();
    }

    function handleRequest() {
        $layout = MJTC_request::MJTC_getLayout('mjslay', null, 'stepone');
        majesticsupport::$_data['sanitized_args']['MJTC_nonce'] = esc_html(wp_create_nonce('MJTC_nonce'));
        if($this->canaddfile($layout)){
            switch ($layout) {
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
                    if(!in_array('feedback', majesticsupport::$_active_addons)){// to hanle show hide of feed back settings.
                        $layout = 'admin_settingcomplete';
                    }
                    MJTC_includer::MJTC_getModel('postinstallation')->getConfigurationValues();
                break;
                case 'admin_stepfour':
                break;
                case 'admin_settingcomplete':
                break;
                case 'admin_themedemodata':
                    majesticsupport::$_data['flag'] = MJTC_request::MJTC_getVar('flag');
                break;
                case 'admin_translationoption':
                    majesticsupport::$_data[0]['mstran'] = MJTC_includer::MJTC_getModel('majesticsupport')->getInstalledTranslationKey();
                    if(!majesticsupport::$_data[0]['mstran']){
                        if(!in_array('feedback', majesticsupport::$_active_addons)){// to handle show hide of feed back settings.
                            $layout = 'admin_settingcomplete';
                        }else{
                            $layout = 'admin_stepthree';
                        }
                    }
                break;
                default:
                    exit;
            }
            $module = (is_admin()) ? 'page' : 'mjsmod';
            $module = MJTC_request::MJTC_getVar($module, null, 'postinstallation');
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

    function save(){
        if(!current_user_can('manage_options')){
            return false;
        }
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $nonce, 'save') ) {
            die( 'Security check Failed' );
        }
        $MJTC_data = MJTC_request::get('post');
        if($MJTC_data['step'] != 'translationoption'){
            $result = MJTC_includer::MJTC_getModel('postinstallation')->storeConfigurations($MJTC_data);
        }
        $MJTC_url = admin_url("admin.php?page=majesticsupport_postinstallation&mjslay=steptwo");
        if($MJTC_data['step'] == 2){
            $MJTC_url = admin_url("admin.php?page=majesticsupport_postinstallation&mjslay=translationoption");
        }
        if($MJTC_data['step'] == 'translationoption'){
            $MJTC_url = admin_url("admin.php?page=majesticsupport_postinstallation&mjslay=stepthree");
        }
        if($MJTC_data['step'] == 3){
            $MJTC_url = admin_url("admin.php?page=majesticsupport_postinstallation&mjslay=stepfour");
        }

        wp_safe_redirect($MJTC_url);
        exit();
    }

    function savesampledata(){
        $MJTC_data = MJTC_request::get('post');
        $sampledata = $MJTC_data['sampledata'];
        $jsmenu = $MJTC_data['jsmenu'];
        $empmenu = $MJTC_data['empmenu'];
        $MJTC_url = admin_url("admin.php?page=majesticsupport_jslearnmanager");
        $result = MJTC_includer::MJTC_getModel('postinstallation')->installSampleData($sampledata);
        wp_safe_redirect($MJTC_url);
        exit();
    }
}
$MJTC_postinstallationController = new MJTC_postinstallationController();
?>
