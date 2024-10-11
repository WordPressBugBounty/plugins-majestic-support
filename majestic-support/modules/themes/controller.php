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
        if (self::canaddfile()) {
            switch ($layout) {
                case 'admin_themes':
                    MJTC_includer::MJTC_getModel('themes')->getCurrentTheme();
                    break;
                default:
                    exit;
            }
            $module = (is_admin()) ? 'page' : 'mjsmod';
            $module = MJTC_request::MJTC_getVar($module, null, 'themes');
            $module = MJTC_majesticsupportphplib::MJTC_str_replace('majesticsupport_', '', $module);

            if(strstr($layout, 'admin_')){
                if (!current_user_can('manage_options')) {
                    return false;
                }
            }
            
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
    static function savetheme() {
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $nonce, 'save-theme') ) {
            die( 'Security check Failed' );
        }
        $data = MJTC_request::get('post');
        MJTC_includer::MJTC_getModel('themes')->storeTheme($data);
        $url = admin_url("admin.php?page=majesticsupport_themes&mjslay=themes");
        wp_redirect($url);
        exit;
    }

}

$controlpanelController = new MJTC_themesController();
?>
