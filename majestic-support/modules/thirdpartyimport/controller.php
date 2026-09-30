<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_thirdpartyimportController {

    function __construct() {
        self::handleRequest();
    }

    function handleRequest() {
        $MJTC_layout = MJTC_request::MJTC_getLayout('mjslay', null, 'importdata');
        if (self::canaddfile($MJTC_layout)) {
            switch ($MJTC_layout) {
                case 'admin_importdata':
                    $MJTC_selected_plugin = MJTC_request::MJTC_getVar('selected_plugin', '', 0);
                    majesticsupport::$_data['count_for'] = $MJTC_selected_plugin;
                    if($MJTC_selected_plugin == 1){
                        // prepare data for supportcandy plugin
                        MJTC_includer::MJTC_getModel('thirdpartyimport')->getSupportCandyDataStats($MJTC_selected_plugin);
                    } elseif($MJTC_selected_plugin == 2){
                        // prepare data for awesome Awesome Support plugin
                        MJTC_includer::MJTC_getModel('thirdpartyimport')->getAwesomeSupportStats($MJTC_selected_plugin);
                    } elseif($MJTC_selected_plugin == 3){
                        // prepare data for Fluent Support plugin
                        MJTC_includer::MJTC_getModel('thirdpartyimport')->getFluentSupportDataStats($MJTC_selected_plugin);
                    } 
                    // no plugin selected
                    break;
                case 'admin_importresult':
                    break;
                default:
                    exit;
            }
            $MJTC_module = (is_admin()) ? 'page' : 'mjsmod';
            $MJTC_module = MJTC_request::MJTC_getVar($MJTC_module, null, 'thirdpartyimport');
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

    function importPluginData() {
        // $MJTC_id = MJTC_request::MJTC_getVar('id');
        // $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        // if (! wp_verify_nonce( $MJTC_nonce, 'save-status-'.$MJTC_id) ) {
        //     die( 'Security check Failed' );
        // }
        if (!current_user_can('manage_options')) { //only admin can change it.
            return false;
        }
        $MJTC_selected_plugin = MJTC_request::MJTC_getVar('selected_plugin', '', 0);
        majesticsupport::$_data['count_for'] = $MJTC_selected_plugin;
        if($MJTC_selected_plugin == 1){
            MJTC_includer::MJTC_getModel('thirdpartyimport')->importSupportCandyData();
        } elseif($MJTC_selected_plugin == 2){
            MJTC_includer::MJTC_getModel('thirdpartyimport')->importAwesomeSupportData();
        } elseif($MJTC_selected_plugin == 3){
            MJTC_includer::MJTC_getModel('thirdpartyimport')->importFluentSupportData();
        }
        $MJTC_url = admin_url("admin.php?page=majesticsupport_thirdpartyimport&mjslay=importresult&selected_plugin=".$MJTC_selected_plugin);
        wp_safe_redirect($MJTC_url);
        exit;
    }

    function getSupportCandyDataStats() {
        $MJTC_id = MJTC_request::MJTC_getVar('statusid');
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'delete-status-'.$MJTC_id) ) {
            die( 'Security check Failed' );
        }
        MJTC_includer::MJTC_getModel('thirdpartyimport')->getSupportCandyDataStats($MJTC_id);
        $MJTC_url = admin_url("admin.php?page=majesticsupport_thirdpartyimport&mjslay=importresult");
        wp_safe_redirect($MJTC_url);
        exit;
    }

    function getFluentSupportStats() {
        $MJTC_id = MJTC_request::MJTC_getVar('statusid');
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'delete-status-'.$MJTC_id) ) {
            die( 'Security check Failed' );
        }
        MJTC_includer::MJTC_getModel('thirdpartyimport')->getFluentSupportStats($MJTC_id);
        $MJTC_url = admin_url("admin.php?page=majesticsupport_thirdpartyimport&mjslay=importresult");
        wp_safe_redirect($MJTC_url);
        exit;
    }

}

$MJTC_thirdpartyimportController = new MJTC_thirdpartyimportController();
?>
