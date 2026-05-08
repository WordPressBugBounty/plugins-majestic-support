<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_themesController {

    function __construct() {
        self::handleRequest();
    }

    function handleRequest() {
        $MJTC_layout = MJTC_request::MJTC_getLayout('mjslay', null, 'themes');
        majesticsupport::$_data['sanitized_args']['MJTC_nonce'] = esc_html(wp_create_nonce('MJTC_nonce'));
        if (self::canaddfile($MJTC_layout)) {
            switch ($MJTC_layout) {
                case 'admin_themes':
                    if (current_user_can('manage_options')) {    
                        MJTC_includer::MJTC_getModel('themes')->getCurrentTheme();
                    }
                    break;
                default:
                    exit;
            }
            $MJTC_module = (is_admin()) ? 'page' : 'mjsmod';
            $MJTC_module = MJTC_request::MJTC_getVar($MJTC_module, null, 'themes');
            $MJTC_module = MJTC_majesticsupportphplib::MJTC_str_replace('majesticsupport_', '', $MJTC_module);

            if(MJTC_majesticsupportphplib::MJTC_strstr($MJTC_layout, 'admin_')){
                if (!current_user_can('manage_options')) {
                    return false;
                }
            }
            
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
    static function savetheme() {
        if(!current_user_can('manage_options')){
            return false;
        }
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'save-theme') ) {
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
