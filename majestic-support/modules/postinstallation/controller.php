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
        if($this->canaddfile()){
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

    function save(){
        $data = MJTC_request::get('post');
        if($data['step'] != 'translationoption'){
            $result = MJTC_includer::MJTC_getModel('postinstallation')->storeConfigurations($data);
        }
        $url = admin_url("admin.php?page=majesticsupport_postinstallation&mjslay=steptwo");
        if($data['step'] == 2){
            $url = admin_url("admin.php?page=majesticsupport_postinstallation&mjslay=translationoption");
        }
        if($data['step'] == 'translationoption'){
            $url = admin_url("admin.php?page=majesticsupport_postinstallation&mjslay=stepthree");
        }
        if($data['step'] == 3){
            $url = admin_url("admin.php?page=majesticsupport_postinstallation&mjslay=stepfour");
        }

        wp_redirect($url);
        exit();
    }

    function savesampledata(){
        $data = MJTC_request::get('post');
        $sampledata = $data['sampledata'];
        $jsmenu = $data['jsmenu'];
        $empmenu = $data['empmenu'];
        $url = admin_url("admin.php?page=majesticsupport_jslearnmanager");
        $result = MJTC_includer::MJTC_getModel('postinstallation')->installSampleData($sampledata);
        wp_redirect($url);
        exit();
    }
}
$MJTC_postinstallationController = new MJTC_postinstallationController();
?>
