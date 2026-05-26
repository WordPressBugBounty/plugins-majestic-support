<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_formhandler {

    function __construct() {
        add_action('init', array($this, 'MJTC_checkFormRequest'));
        add_action('init', array($this, 'MJTC_checkDeleteRequest'));
    }

    /*
     * Handle Form request
     */

    function MJTC_checkFormRequest() {
        majesticsupport::$_data['sanitized_args']['_wpnonce'] = wp_create_nonce("VERIFY-MAJESTIC-SUPPORT-INTERNAL-NONCE");
        $MJTC_formrequest = MJTC_request::MJTC_getVar('form_request', 'post');
        if ($MJTC_formrequest == 'majesticsupport') {
            //handle the request
            $MJTC_page_id = MJTC_request::MJTC_getVar('page_id', 'GET');
            majesticsupport::setPageID($MJTC_page_id);
            $MJTC_modulename = (is_admin()) ? 'page' : 'mjsmod';
            $MJTC_module = MJTC_request::MJTC_getVar($MJTC_modulename);
            $MJTC_module = MJTC_majesticsupportphplib::MJTC_str_replace('majesticsupport_', '', $MJTC_module);
            MJTC_includer::MJTC_include_file($MJTC_module);
            $MJTC_class = 'MJTC_' . $MJTC_module . "Controller";
            $MJTC_task = MJTC_request::MJTC_getVar('task');
            $MJTC_obj = new $MJTC_class;
            $MJTC_obj->$MJTC_task();
        }
    }

    /*
     * Handle Form request
     */

    function MJTC_checkDeleteRequest() {
        majesticsupport::$_data['sanitized_args']['_wpnonce'] = wp_create_nonce("VERIFY-MAJESTIC-SUPPORT-INTERNAL-NONCE");
        
        $majesticsupport_action = MJTC_request::MJTC_getVar('action', 'get');
        
        // Early return if action does not match
        if ( 'mstask' !== $majesticsupport_action ) {
            return;
        }
        
        // Handle the request and sanitize page_id
        $MJTC_page_id = absint(
            MJTC_request::MJTC_getVar('page_id', 'GET')
        );

        majesticsupport::setPageID($MJTC_page_id);

        $MJTC_modulename_key = (is_admin()) ? 'page' : 'mjsmod';
        
        // Retrieve, replace string, and sanitize module
        $raw_module = MJTC_request::MJTC_getVar($MJTC_modulename_key, '', '');
        $raw_module = MJTC_majesticsupportphplib::MJTC_str_replace('majesticsupport_', '', $raw_module);
        $MJTC_module = sanitize_key( $raw_module );
        
        // Retrieve and sanitize action
        $MJTC_action = sanitize_key(
            MJTC_request::MJTC_getVar('task')
        );
        
        if ( empty( $MJTC_module ) || empty( $MJTC_action ) ) {
            // Kept your original error logging for empty cases
            error_log( print_r( $_REQUEST, true ) ); 
            return;
        }

        /*
         * Prevent invalid class/method names.
         */
        if (
            preg_match( '/[^a-zA-Z0-9_]/', $MJTC_module ) ||
            preg_match( '/[^a-zA-Z0-9_]/', $MJTC_action )
        ) {
            return;
        }

        MJTC_includer::MJTC_include_file($MJTC_module);

        $MJTC_class = 'MJTC_' . $MJTC_module . 'Controller';

        /*
         * Ensure controller exists.
         */
        if ( ! class_exists( $MJTC_class ) ) {
            return;
        }

        $MJTC_obj = new $MJTC_class;

        /*
         * Block magic methods and private-style methods.
         */
        if (
            0 === strpos( $MJTC_action, '__' ) ||
            0 === strpos( $MJTC_action, '_' )
        ) {
            return;
        }

        /*
         * Ensure method is callable.
         */
        if ( ! is_callable( array( $MJTC_obj, $MJTC_action ) ) ) {
            return;
        }

        /*
         * Require capability for admin requests.
         */
        if ( is_admin() && ! current_user_can( 'manage_options' ) ) {
            wp_die(
                esc_html__( 'You are not allowed to access this resource.', 'majestic-support' ),
                esc_html__( 'Access Denied', 'majestic-support' ),
                array( 'response' => 403 )
            );
        }

        // Call the method safely
        call_user_func( array( $MJTC_obj, $MJTC_action ) );
    }

}

$MJTC_formhandler = new MJTC_formhandler();
?>
