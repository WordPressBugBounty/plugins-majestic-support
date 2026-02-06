<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_productController {

    function __construct() {
        self::handleRequest();
    }

    function handleRequest() {
        $layout = MJTC_request::MJTC_getLayout('mjslay', null, 'products');
        majesticsupport::$_data['sanitized_args']['MJTC_nonce'] = esc_html(wp_create_nonce('MJTC_nonce'));
        if (self::canaddfile($layout)) {
            switch ($layout) {
                case 'admin_products':
                    MJTC_includer::MJTC_getModel('product')->getProducts();
                    break;
                case 'admin_addproduct':
                    $id = MJTC_request::MJTC_getVar('majesticsupportid', 'get');
                    MJTC_includer::MJTC_getModel('product')->getProductForForm($id);
                    break;
                default:
                    exit;
            }
            $module = (is_admin()) ? 'page' : 'mjsmod';
            $module = MJTC_request::MJTC_getVar($module, null, 'product');
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

    static function saveproduct() {
        $id = MJTC_request::MJTC_getVar('id');
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $nonce, 'save-product-'.$id) ) {
            die( 'Security check Failed' );
        }
        if (!current_user_can('manage_options')) { //only admin can change it.
            return false;
        }
        $MJTC_data = MJTC_request::get('post');
        MJTC_includer::MJTC_getModel('product')->storeProduct($MJTC_data);
        if (is_admin()) {
            $MJTC_url = admin_url("admin.php?page=majesticsupport_product&mjslay=products");
        } else {
            $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'product','mjslay'=>'products'));
        }
        wp_safe_redirect($MJTC_url);
        exit;
    }

    static function deleteproduct() {
        $id = MJTC_request::MJTC_getVar('productid');
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $nonce, 'delete-product-'.$id) ) {
            die( 'Security check Failed' );
        }
        MJTC_includer::MJTC_getModel('product')->removeProduct($id);
        if (is_admin()) {
            $MJTC_url = admin_url("admin.php?page=majesticsupport_product&mjslay=products");
        } else {
            $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'product','mjslay'=>'products'));
        }
        wp_safe_redirect($MJTC_url);
        exit;
    }

    static function changestatus() {
        $id = MJTC_request::MJTC_getVar('productid');
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $nonce, 'change-status-'.$id) ) {
            die( 'Security check Failed' );
        }
        MJTC_includer::MJTC_getModel('product')->changeStatus($id);
        $MJTC_url = admin_url("admin.php?page=majesticsupport_product&mjslay=products");
        $MJTC_pagenum = MJTC_request::MJTC_getVar('pagenum');
        if ($MJTC_pagenum)
            $MJTC_url .= '&pagenum=' . $MJTC_pagenum;
        wp_safe_redirect($MJTC_url);
        exit;
    }

    static function ordering() {
        $id = MJTC_request::MJTC_getVar('productid');
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $nonce, 'ordering-'.$id) ) {
            die( 'Security check Failed' );
        }
        MJTC_includer::MJTC_getModel('product')->setOrdering($id);
        $MJTC_pagenum = MJTC_request::MJTC_getVar('pagenum');
        $MJTC_url = "admin.php?page=majesticsupport_product&mjslay=products";
        if ($MJTC_pagenum)
            $MJTC_url .= '&pagenum=' . $MJTC_pagenum;
        wp_safe_redirect($MJTC_url);
        exit;
    }

}

$productController = new MJTC_productController();
?>
