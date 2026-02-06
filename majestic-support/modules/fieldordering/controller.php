<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_fieldorderingController {

    function __construct() {
        self::handleRequest();
    }

    function handleRequest() {
        $layout = MJTC_request::MJTC_getLayout('mjslay', null, 'fieldordering');
        majesticsupport::$_data['sanitized_args']['MJTC_nonce'] = esc_html(wp_create_nonce('MJTC_nonce'));
        if (self::canaddfile($layout)) {
            switch ($layout) {
                case 'admin_fieldordering':
                    $fieldfor = MJTC_request::MJTC_getVar('fieldfor',null,1);
                    $formid = MJTC_request::MJTC_getVar('formid');
                    majesticsupport::$_data['fieldfor'] = $fieldfor;
                    if ($fieldfor != 1) {
                        majesticsupport::$_data['formid'] = 1;
                    }
                    else{
                        majesticsupport::$_data['formid'] = $formid;
                        do_action('ms_multiform_name_for_list' , $formid);
                    }
                    MJTC_includer::MJTC_getModel('fieldordering')->getFieldOrderingForList($fieldfor);
                    break;
                case 'admin_adduserfeild':
                    $id = MJTC_request::MJTC_getVar('majesticsupportid');
                    $fieldfor = MJTC_request::MJTC_getVar('fieldfor');
                    if($fieldfor == ''){
                        $fieldfor = majesticsupport::$_data['fieldfor'];
                    }else{
                        majesticsupport::$_data['fieldfor'] = $fieldfor;
                    }
                    // formid
                    if ($fieldfor != 1) {
                        majesticsupport::$_data['formid'] = 1;
                    }
                    else{
                        $formid = MJTC_request::MJTC_getVar('formid');
                        majesticsupport::$_data['formid'] = $formid;
                        do_action('ms_multiform_name_for_list' , $formid);
                    }
                    MJTC_includer::MJTC_getModel('fieldordering')->getUserFieldbyId($id,1);
                    break;
                default:
                    exit;
            }
            $module = (is_admin()) ? 'page' : 'mjsmod';
            $module = MJTC_request::MJTC_getVar($module, null, 'fieldordering');
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

    static function changeorder() {
        $id = MJTC_request::MJTC_getVar('fieldorderingid');
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $nonce, 'change-order-'.$id) ) {
            die( 'Security check Failed' );
        }
        $fieldfor = MJTC_request::MJTC_getVar('fieldfor');
        if($fieldfor == ''){
            $fieldfor = majesticsupport::$_data['fieldfor'];
        }
        $formid = MJTC_request::MJTC_getVar('formid');
        $action = MJTC_request::MJTC_getVar('order');
        MJTC_includer::MJTC_getModel('fieldordering')->changeOrder($id, $action);
        $MJTC_url = admin_url("admin.php?page=majesticsupport_fieldordering&mjslay=fieldordering&fieldfor=".esc_attr($fieldfor)."&formid=".esc_attr($formid));
        wp_safe_redirect($MJTC_url);
        exit;
    }

    static function changepublishstatus() {
        $id = MJTC_request::MJTC_getVar('fieldorderingid');
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $nonce, 'change-publish-status-'.$id) ) {
            die( 'Security check Failed' );
        }
        $fieldfor = MJTC_request::MJTC_getVar('fieldfor');
        if($fieldfor == ''){
            $fieldfor = majesticsupport::$_data['fieldfor'];
        }
        $formid = MJTC_request::MJTC_getVar('formid');
        $status = MJTC_request::MJTC_getVar('status');
        MJTC_includer::MJTC_getModel('fieldordering')->changePublishStatus($id, $status);
        $MJTC_url = admin_url("admin.php?page=majesticsupport_fieldordering&mjslay=fieldordering&fieldfor=".esc_attr($fieldfor)."&formid=".esc_attr($formid));
        wp_safe_redirect($MJTC_url);
        exit;
    }

    static function changevisitorpublishstatus() {
        $id = MJTC_request::MJTC_getVar('fieldorderingid');
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $nonce, 'change-visitor-publish-status-'.$id) ) {
            die( 'Security check Failed' );
        }
        $fieldfor = MJTC_request::MJTC_getVar('fieldfor');
        if($fieldfor == ''){
            $fieldfor = majesticsupport::$_data['fieldfor'];
        }
        $formid = MJTC_request::MJTC_getVar('formid');
        $status = MJTC_request::MJTC_getVar('status');
        MJTC_includer::MJTC_getModel('fieldordering')->changeVisitorPublishStatus($id, $status);
        $MJTC_url = admin_url("admin.php?page=majesticsupport_fieldordering&mjslay=fieldordering&fieldfor=".esc_attr($fieldfor)."&formid=".esc_attr($formid));
        wp_safe_redirect($MJTC_url);
        exit;
    }

    static function changerequiredstatus() {
        $id = MJTC_request::MJTC_getVar('fieldorderingid');
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $nonce, 'change-required-status-'.$id) ) {
            die( 'Security check Failed' );
        }
        $fieldfor = MJTC_request::MJTC_getVar('fieldfor');
        if($fieldfor == ''){
            $fieldfor = majesticsupport::$_data['fieldfor'];
        }
        $formid = MJTC_request::MJTC_getVar('formid');
        $status = MJTC_request::MJTC_getVar('status');
        MJTC_includer::MJTC_getModel('fieldordering')->changeRequiredStatus($id, $status);
        $MJTC_url = admin_url("admin.php?page=majesticsupport_fieldordering&mjslay=fieldordering&fieldfor=".esc_attr($fieldfor)."&formid=".esc_attr($formid));
        wp_safe_redirect($MJTC_url);
        exit;
    }

    static function saveuserfeild() {
        // Validate ID: Ensure it's a numeric value to prevent injection
        $id = MJTC_request::MJTC_getVar('id');
        if (!empty($id) && (!is_numeric($id) || intval($id) < 0)) {
            return false;
        }

        // Validate Nonce
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (!wp_verify_nonce($nonce, 'save-userfeild-' . $id)) {
            die('Security check Failed');
        }
        if (!current_user_can('manage_options')) { //only admin can change it.
            return false;
        }

        // Retrieve and Sanitize Input Data
        $MJTC_data = MJTC_request::get('post');
        if (!is_array($MJTC_data)) {
            return false; // Ensure data is an array
        }
        array_walk_recursive($MJTC_data, function (&$item) {
            $item = sanitize_text_field($item);
        });

        // Validate fieldfor parameter
        $fieldfor = MJTC_request::MJTC_getVar('fieldfor');
        if (empty($fieldfor)) {
            $fieldfor = majesticsupport::$_data['fieldfor'];
        }
        $fieldfor = sanitize_text_field($fieldfor); // Prevent malicious input

        // Validate formid parameter
        $formid = MJTC_request::MJTC_getVar('formid');
        $formid = sanitize_text_field($formid);

        // Store the sanitized user field using prepared statements
        MJTC_includer::MJTC_getModel('fieldordering')->storeUserField($MJTC_data);

        // Redirect securely
        if (is_admin()) {
            $MJTC_url = admin_url("admin.php?page=majesticsupport_fieldordering&fieldfor=" . urlencode($fieldfor) . "&formid=" . urlencode($formid));
        } else {
            $MJTC_url = majesticsupport::makeUrl(array('mjsmod' => 'fieldordering', 'mjslay' => 'userfeilds'));
        }

        wp_safe_redirect($MJTC_url);
        exit;
    }

    static function savefeild() {
        $id = MJTC_request::MJTC_getVar('id');
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $nonce, 'save-feild-'.$id) ) {
            die( 'Security check Failed' );
        }
        if (!current_user_can('manage_options')) { //only admin can change it.
            return false;
        }
        $MJTC_data = MJTC_request::get('post');
        $fieldfor = MJTC_request::MJTC_getVar('fieldfor');
        if($fieldfor == ''){
            $fieldfor = majesticsupport::$_data['fieldfor'];
        }
        $formid = MJTC_request::MJTC_getVar('formid');
        MJTC_includer::MJTC_getModel('fieldordering')->updateField($MJTC_data);
        if (is_admin()) {
            $MJTC_url = admin_url("admin.php?page=majesticsupport_fieldordering&fieldfor=".esc_attr($fieldfor)."&formid=".esc_attr($formid));
        } else {
            $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'fieldordering', 'mjslay'=>'userfeilds'));
        }
        wp_safe_redirect($MJTC_url);
        exit;
    }

    static function removeuserfeild() {
        $id = MJTC_request::MJTC_getVar('majesticsupportid');
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $nonce, 'remove-userfeild-'.$id) ) {
            die( 'Security check Failed' );
        }
        $fieldfor = MJTC_request::MJTC_getVar('fieldfor');
        if($fieldfor == ''){
            $fieldfor = majesticsupport::$_data['fieldfor'];
        }
        $formid = MJTC_request::MJTC_getVar('formid');
        MJTC_includer::MJTC_getModel('fieldordering')->deleteUserField($id);
        if (is_admin()) {
            $MJTC_url = admin_url("admin.php?page=majesticsupport_fieldordering&fieldfor=".esc_attr($fieldfor)."&formid=".esc_attr($formid));
        } else {
            $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'fieldordering', 'mjslay'=>'userfeilds'));
        }
        wp_safe_redirect($MJTC_url);
        exit;
    }

}

$MJTC_fieldorderingController = new MJTC_fieldorderingController();
?>
