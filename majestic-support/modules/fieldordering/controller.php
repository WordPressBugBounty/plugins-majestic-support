<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_fieldorderingController {

    function __construct() {
        self::handleRequest();
    }

    function handleRequest() {
        $MJTC_layout = MJTC_request::MJTC_getLayout('mjslay', null, 'fieldordering');
        majesticsupport::$_data['sanitized_args']['MJTC_nonce'] = esc_html(wp_create_nonce('MJTC_nonce'));
        if (self::canaddfile($MJTC_layout)) {
            switch ($MJTC_layout) {
                case 'admin_fieldordering':
                    $MJTC_fieldfor = MJTC_request::MJTC_getVar('fieldfor',null,1);
                    $MJTC_formid = MJTC_request::MJTC_getVar('formid');
                    majesticsupport::$_data['fieldfor'] = $MJTC_fieldfor;
                    if ($MJTC_fieldfor != 1) {
                        majesticsupport::$_data['formid'] = 1;
                    }
                    else{
                        majesticsupport::$_data['formid'] = $MJTC_formid;
                        do_action('MJTC_multiform_name_for_list' , $MJTC_formid);
                    }
                    MJTC_includer::MJTC_getModel('fieldordering')->getFieldOrderingForList($MJTC_fieldfor);
                    break;
                case 'admin_adduserfeild':
                    $MJTC_id = MJTC_request::MJTC_getVar('majesticsupportid');
                    $MJTC_fieldfor = MJTC_request::MJTC_getVar('fieldfor');
                    if($MJTC_fieldfor == ''){
                        $MJTC_fieldfor = majesticsupport::$_data['fieldfor'];
                    }else{
                        majesticsupport::$_data['fieldfor'] = $MJTC_fieldfor;
                    }
                    // formid
                    if ($MJTC_fieldfor != 1) {
                        majesticsupport::$_data['formid'] = 1;
                    }
                    else{
                        $MJTC_formid = MJTC_request::MJTC_getVar('formid');
                        majesticsupport::$_data['formid'] = $MJTC_formid;
                        do_action('MJTC_multiform_name_for_list' , $MJTC_formid);
                    }
                    MJTC_includer::MJTC_getModel('fieldordering')->getUserFieldbyId($MJTC_id,1);
                    break;
                default:
                    exit;
            }
            $MJTC_module = (is_admin()) ? 'page' : 'mjsmod';
            $MJTC_module = MJTC_request::MJTC_getVar($MJTC_module, null, 'fieldordering');
            $MJTC_module = MJTC_majesticsupportphplib::MJTC_str_replace('majesticsupport_', '', $MJTC_module);
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

    static function changeorder() {
        $MJTC_id = MJTC_request::MJTC_getVar('fieldorderingid');
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'change-order-'.$MJTC_id) ) {
            die( 'Security check Failed' );
        }
        $MJTC_fieldfor = MJTC_request::MJTC_getVar('fieldfor');
        if($MJTC_fieldfor == ''){
            $MJTC_fieldfor = majesticsupport::$_data['fieldfor'];
        }
        $MJTC_formid = MJTC_request::MJTC_getVar('formid');
        $MJTC_action = MJTC_request::MJTC_getVar('order');
        MJTC_includer::MJTC_getModel('fieldordering')->changeOrder($MJTC_id, $MJTC_action);
        $MJTC_url = admin_url("admin.php?page=majesticsupport_fieldordering&mjslay=fieldordering&fieldfor=".esc_attr($MJTC_fieldfor)."&formid=".esc_attr($MJTC_formid));
        wp_safe_redirect($MJTC_url);
        exit;
    }

    static function changepublishstatus() {
        $MJTC_id = MJTC_request::MJTC_getVar('fieldorderingid');
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'change-publish-status-'.$MJTC_id) ) {
            die( 'Security check Failed' );
        }
        $MJTC_fieldfor = MJTC_request::MJTC_getVar('fieldfor');
        if($MJTC_fieldfor == ''){
            $MJTC_fieldfor = majesticsupport::$_data['fieldfor'];
        }
        $MJTC_formid = MJTC_request::MJTC_getVar('formid');
        $MJTC_status = MJTC_request::MJTC_getVar('status');
        MJTC_includer::MJTC_getModel('fieldordering')->changePublishStatus($MJTC_id, $MJTC_status);
        $MJTC_url = admin_url("admin.php?page=majesticsupport_fieldordering&mjslay=fieldordering&fieldfor=".esc_attr($MJTC_fieldfor)."&formid=".esc_attr($MJTC_formid));
        wp_safe_redirect($MJTC_url);
        exit;
    }

    static function changevisitorpublishstatus() {
        $MJTC_id = MJTC_request::MJTC_getVar('fieldorderingid');
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'change-visitor-publish-status-'.$MJTC_id) ) {
            die( 'Security check Failed' );
        }
        $MJTC_fieldfor = MJTC_request::MJTC_getVar('fieldfor');
        if($MJTC_fieldfor == ''){
            $MJTC_fieldfor = majesticsupport::$_data['fieldfor'];
        }
        $MJTC_formid = MJTC_request::MJTC_getVar('formid');
        $MJTC_status = MJTC_request::MJTC_getVar('status');
        MJTC_includer::MJTC_getModel('fieldordering')->changeVisitorPublishStatus($MJTC_id, $MJTC_status);
        $MJTC_url = admin_url("admin.php?page=majesticsupport_fieldordering&mjslay=fieldordering&fieldfor=".esc_attr($MJTC_fieldfor)."&formid=".esc_attr($MJTC_formid));
        wp_safe_redirect($MJTC_url);
        exit;
    }

    static function changerequiredstatus() {
        $MJTC_id = MJTC_request::MJTC_getVar('fieldorderingid');
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'change-required-status-'.$MJTC_id) ) {
            die( 'Security check Failed' );
        }
        $MJTC_fieldfor = MJTC_request::MJTC_getVar('fieldfor');
        if($MJTC_fieldfor == ''){
            $MJTC_fieldfor = majesticsupport::$_data['fieldfor'];
        }
        $MJTC_formid = MJTC_request::MJTC_getVar('formid');
        $MJTC_status = MJTC_request::MJTC_getVar('status');
        MJTC_includer::MJTC_getModel('fieldordering')->changeRequiredStatus($MJTC_id, $MJTC_status);
        $MJTC_url = admin_url("admin.php?page=majesticsupport_fieldordering&mjslay=fieldordering&fieldfor=".esc_attr($MJTC_fieldfor)."&formid=".esc_attr($MJTC_formid));
        wp_safe_redirect($MJTC_url);
        exit;
    }

    static function saveuserfeild() {
        // Validate ID: Ensure it's a numeric value to prevent injection
        $MJTC_id = MJTC_request::MJTC_getVar('id');
        if (!empty($MJTC_id) && (!is_numeric($MJTC_id) || intval($MJTC_id) < 0)) {
            return false;
        }

        // Validate Nonce
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (!wp_verify_nonce($MJTC_nonce, 'save-userfeild-' . $MJTC_id)) {
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
        array_walk_recursive($MJTC_data, function (&$MJTC_item) {
            $MJTC_item = sanitize_text_field($MJTC_item);
        });

        // Validate fieldfor parameter
        $MJTC_fieldfor = MJTC_request::MJTC_getVar('fieldfor');
        if (empty($MJTC_fieldfor)) {
            $MJTC_fieldfor = majesticsupport::$_data['fieldfor'];
        }
        $MJTC_fieldfor = sanitize_text_field($MJTC_fieldfor); // Prevent malicious input

        // Validate formid parameter
        $MJTC_formid = MJTC_request::MJTC_getVar('formid');
        $MJTC_formid = sanitize_text_field($MJTC_formid);

        // Store the sanitized user field using prepared statements
        MJTC_includer::MJTC_getModel('fieldordering')->storeUserField($MJTC_data);

        // Redirect securely
        if (is_admin()) {
            $MJTC_url = admin_url("admin.php?page=majesticsupport_fieldordering&fieldfor=" . urlencode($MJTC_fieldfor) . "&formid=" . urlencode($MJTC_formid));
        } else {
            $MJTC_url = majesticsupport::makeUrl(array('mjsmod' => 'fieldordering', 'mjslay' => 'userfeilds'));
        }

        wp_safe_redirect($MJTC_url);
        exit;
    }

    static function savefeild() {
        $MJTC_id = MJTC_request::MJTC_getVar('id');
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'save-feild-'.$MJTC_id) ) {
            die( 'Security check Failed' );
        }
        if (!current_user_can('manage_options')) { //only admin can change it.
            return false;
        }
        $MJTC_data = MJTC_request::get('post');
        $MJTC_fieldfor = MJTC_request::MJTC_getVar('fieldfor');
        if($MJTC_fieldfor == ''){
            $MJTC_fieldfor = majesticsupport::$_data['fieldfor'];
        }
        $MJTC_formid = MJTC_request::MJTC_getVar('formid');
        MJTC_includer::MJTC_getModel('fieldordering')->updateField($MJTC_data);
        if (is_admin()) {
            $MJTC_url = admin_url("admin.php?page=majesticsupport_fieldordering&fieldfor=".esc_attr($MJTC_fieldfor)."&formid=".esc_attr($MJTC_formid));
        } else {
            $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'fieldordering', 'mjslay'=>'userfeilds'));
        }
        wp_safe_redirect($MJTC_url);
        exit;
    }

    static function removeuserfeild() {
        $MJTC_id = MJTC_request::MJTC_getVar('majesticsupportid');
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'remove-userfeild-'.$MJTC_id) ) {
            die( 'Security check Failed' );
        }
        $MJTC_fieldfor = MJTC_request::MJTC_getVar('fieldfor');
        if($MJTC_fieldfor == ''){
            $MJTC_fieldfor = majesticsupport::$_data['fieldfor'];
        }
        $MJTC_formid = MJTC_request::MJTC_getVar('formid');
        MJTC_includer::MJTC_getModel('fieldordering')->deleteUserField($MJTC_id);
        if (is_admin()) {
            $MJTC_url = admin_url("admin.php?page=majesticsupport_fieldordering&fieldfor=".esc_attr($MJTC_fieldfor)."&formid=".esc_attr($MJTC_formid));
        } else {
            $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'fieldordering', 'mjslay'=>'userfeilds'));
        }
        wp_safe_redirect($MJTC_url);
        exit;
    }

}

$MJTC_fieldorderingController = new MJTC_fieldorderingController();
?>
