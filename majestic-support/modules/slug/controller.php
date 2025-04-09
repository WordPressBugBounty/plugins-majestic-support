<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_slugController {

    function __construct() {
        self::handleRequest();
    }

    function handleRequest() {
        $layout = MJTC_request::MJTC_getLayout('mjslay', null, 'slug');
        majesticsupport::$_data['sanitized_args']['MJTC_nonce'] = esc_html(wp_create_nonce('MJTC_nonce'));
        if (self::canaddfile()) {
            switch ($layout) {
                case 'admin_slug':
                    MJTC_includer::MJTC_getModel('slug')->getSlug();
                    break;
                default:
                    exit;
            }
            $module = (is_admin()) ? 'page' : 'mjsmod';
            $module = MJTC_request::MJTC_getVar($module, null, 'slug');
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

    function saveSlug() {
        if(!current_user_can('manage_options')){
            return false;
        }
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $nonce, 'save-slug') ) {
            die( 'Security check Failed' );
        }
        $data = MJTC_request::get('post');
        $result = MJTC_includer::MJTC_getModel('slug')->storeSlug($data);
        if($data['pagenum'] > 0){
            $url = admin_url("admin.php?page=majesticsupport_slug&pagenum=".$data['pagenum']);
        }else{
            $url = admin_url("admin.php?page=majesticsupport_slug");
        }
        wp_redirect($url);
        exit;
    }

    function saveprefix() {
        if(!current_user_can('manage_options')){
            return false;
        }
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $nonce, 'save-prefix') ) {
            die( 'Security check Failed' );
        }
        $data = MJTC_request::get('post');
        $result = MJTC_includer::MJTC_getModel('slug')->savePrefix($data);
        $url = admin_url("admin.php?page=majesticsupport_slug");
        wp_redirect($url);
        exit;
    }

    function savehomeprefix() {
        if(!current_user_can('manage_options')){
            return false;
        }
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $nonce, 'save-home-prefix') ) {
            die( 'Security check Failed' );
        }
        $data = MJTC_request::get('post');
        $result = MJTC_includer::MJTC_getModel('slug')->saveHomePrefix($data);
        $url = admin_url("admin.php?page=majesticsupport_slug");
        wp_redirect($url);
        exit;
    }

    function resetallslugs() {
        $data = MJTC_request::get('post');
        $result = MJTC_includer::MJTC_getModel('slug')->resetAllSlugs();
        $url = admin_url("admin.php?page=majesticsupport_slug");
        wp_redirect($url);
        exit;
    }

}

$slugController = new MJTC_slugController();
?>
