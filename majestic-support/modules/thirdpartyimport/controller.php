<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_thirdpartyimportController {

    function __construct() {
        self::handleRequest();
    }

    function handleRequest() {
        $layout = MJTC_request::MJTC_getLayout('mjslay', null, 'importdata');
        majesticsupport::$_data['sanitized_args']['MJTC_nonce'] = esc_html(wp_create_nonce('MJTC_nonce'));
        if (self::canaddfile($layout)) {
            switch ($layout) {
                case 'admin_importdata':
                    $selected_plugin = MJTC_request::MJTC_getVar('selected_plugin', '', 0);
                    majesticsupport::$_data['count_for'] = $selected_plugin;
                    if($selected_plugin == 1){
                        // prepare data for supportcandy plugin
                        MJTC_includer::MJTC_getModel('thirdpartyimport')->getSupportCandyDataStats($selected_plugin);
                    } elseif($selected_plugin == 2){
                        // prepare data for awesome Awesome Support plugin
                        MJTC_includer::MJTC_getModel('thirdpartyimport')->getAwesomeSupportStats($selected_plugin);
                    } elseif($selected_plugin == 3){
                        // prepare data for Fluent Support plugin
                        MJTC_includer::MJTC_getModel('thirdpartyimport')->getFluentSupportDataStats($selected_plugin);
                    } 
                    // no plugin selected
                    break;
                case 'admin_importresult':
                    break;
                default:
                    exit;
            }
            $module = (is_admin()) ? 'page' : 'mjsmod';
            $module = MJTC_request::MJTC_getVar($module, null, 'thirdpartyimport');
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

    function importPluginData() {
        // $id = MJTC_request::MJTC_getVar('id');
        // $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        // if (! wp_verify_nonce( $nonce, 'save-status-'.$id) ) {
        //     die( 'Security check Failed' );
        // }
        if (!current_user_can('manage_options')) { //only admin can change it.
            return false;
        }
        $selected_plugin = MJTC_request::MJTC_getVar('selected_plugin', '', 0);
        majesticsupport::$_data['count_for'] = $selected_plugin;
        if($selected_plugin == 1){
            MJTC_includer::MJTC_getModel('thirdpartyimport')->importSupportCandyData();
        } elseif($selected_plugin == 2){
            MJTC_includer::MJTC_getModel('thirdpartyimport')->importAwesomeSupportData();
        } elseif($selected_plugin == 3){
            MJTC_includer::MJTC_getModel('thirdpartyimport')->importFluentSupportData();
        }
        $MJTC_url = admin_url("admin.php?page=majesticsupport_thirdpartyimport&mjslay=importresult&selected_plugin=".$selected_plugin);
        wp_safe_redirect($MJTC_url);
        exit;
    }

    function getSupportCandyDataStats() {
        $id = MJTC_request::MJTC_getVar('statusid');
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $nonce, 'delete-status-'.$id) ) {
            die( 'Security check Failed' );
        }
        MJTC_includer::MJTC_getModel('thirdpartyimport')->getSupportCandyDataStats($id);
        $MJTC_url = admin_url("admin.php?page=majesticsupport_thirdpartyimport&mjslay=importresult");
        wp_safe_redirect($MJTC_url);
        exit;
    }

    function getFluentSupportStats() {
        $id = MJTC_request::MJTC_getVar('statusid');
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $nonce, 'delete-status-'.$id) ) {
            die( 'Security check Failed' );
        }
        MJTC_includer::MJTC_getModel('thirdpartyimport')->getFluentSupportStats($id);
        $MJTC_url = admin_url("admin.php?page=majesticsupport_thirdpartyimport&mjslay=importresult");
        wp_safe_redirect($MJTC_url);
        exit;
    }

}

$thirdpartyimportController = new MJTC_thirdpartyimportController();
?>
