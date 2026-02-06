<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_themesController {

    function __construct() {
        self::handleRequest();
    }

    function handleRequest() {
        $layout = MJTC_request::MJTC_getLayout('mjslay', null, 'themes');
        majesticsupport::$_data['sanitized_args']['MJTC_nonce'] = esc_html(wp_create_nonce('MJTC_nonce'));
        if (self::canaddfile($layout)) {
            switch ($layout) {
                case 'admin_themes':
                    if (current_user_can('manage_options')) {    
                        MJTC_includer::MJTC_getModel('themes')->getCurrentTheme();
                    }
                    break;
                default:
                    exit;
            }
            $module = (is_admin()) ? 'page' : 'mjsmod';
            $module = MJTC_request::MJTC_getVar($module, null, 'themes');
            $module = MJTC_majesticsupportphplib::MJTC_str_replace('majesticsupport_', '', $module);

            if(MJTC_majesticsupportphplib::MJTC_strstr($layout, 'admin_')){
                if (!current_user_can('manage_options')) {
                    return false;
                }
            }
            
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
    static function savetheme() {
        if(!current_user_can('manage_options')){
            return false;
        }
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $nonce, 'save-theme') ) {
            die( 'Security check Failed' );
        }
        $MJTC_data = MJTC_request::get('post');
        MJTC_includer::MJTC_getModel('themes')->storeTheme($MJTC_data);
        $MJTC_url = admin_url("admin.php?page=majesticsupport_themes&mjslay=themes");
        wp_safe_redirect($MJTC_url);
        exit;
    }

}

$MJTC_controlpanelController = new MJTC_themesController();
?>
