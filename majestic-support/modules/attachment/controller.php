<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_attachmentController {

    function __construct() {
        self::handleRequest();
    }

    function handleRequest() {
        $MJTC_layout = MJTC_request::MJTC_getLayout('mjslay', null, 'getattachments');
        majesticsupport::$_data['sanitized_args']['MJTC_nonce'] = esc_html(wp_create_nonce('MJTC_nonce'));
        if (self::canaddfile($MJTC_layout)) {
            switch ($MJTC_layout) {
                case 'getattachments':
                    $MJTC_id = MJTC_request::MJTC_getVar('majesticsupportid', 'get', null);
                    MJTC_includer::MJTC_getModel('replies')->getrepliesForForm($MJTC_id);
                    break;
                default:
                    exit;
            }
            $MJTC_module = (is_admin()) ? 'page' : 'mjsmod';
            $MJTC_module = MJTC_request::MJTC_getVar($MJTC_module, null, 'attachment');
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

    static function saveattachments() {
        $MJTC_data = MJTC_request::get('post');
        MJTC_includer::MJTC_getModel('attachment')->storeAttachments($MJTC_data);
        if (is_admin()) {
            $MJTC_url = admin_url("admin.php?page=majesticsupport_ticket&mjslay=ticketdetail&majesticsupportid=" . MJTC_request::MJTC_getVar('ticketid'));
        } else {
            $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'replies', 'mjslay'=>'replies'));
        }
        wp_safe_redirect($MJTC_url);
        exit;
    }

    static function deleteattachment() {

        $MJTC_id        = absint( MJTC_request::MJTC_getVar( 'id' ) );
        $MJTC_ticket_id = absint( MJTC_request::MJTC_getVar( 'ticketid' ) );
        $MJTC_nonce     = sanitize_text_field( wp_unslash( MJTC_request::MJTC_getVar( '_wpnonce' ) ) );

        /*
         * Only authenticated users should be allowed
         * to perform attachment deletion.
         */
        if ( ! is_user_logged_in() ) {
            wp_die(
                esc_html__( 'You are not allowed to perform this action.', 'majestic-support' ),
                esc_html__( 'Access Denied', 'majestic-support' ),
                array( 'response' => 403 )
            );
        }

        /*
         * Verify nonce.
         * Note: The !is_admin() bypass has been removed to ensure strict verification.
         */
        if ( ! wp_verify_nonce( $MJTC_nonce, 'delete-attachement-' . $MJTC_id ) ) {
            wp_die(
                esc_html__( 'Security check failed.', 'majestic-support' ),
                esc_html__( 'Security Error', 'majestic-support' ),
                array( 'response' => 403 )
            );
        }

        $MJTC_call_from = absint( MJTC_request::MJTC_getVar( 'call_from', '', 1 ) );

        // Proceed to remove the attachment
        MJTC_includer::MJTC_getModel( 'attachment' )->removeAttachment( $MJTC_id );

        // Determine redirect URL
        if ( is_admin() ) {

            $MJTC_url = admin_url(
                'admin.php?page=majesticsupport_ticket&mjslay=addticket&majesticsupportid=' . $MJTC_ticket_id
            );

        } else {

            if ( 2 === $MJTC_call_from ) {

                $MJTC_url = majesticsupport::makeUrl(
                    array(
                        'mjsmod'             => 'agent',
                        'mjslay'             => 'staffaddticket',
                        'majesticsupportid'  => $MJTC_ticket_id,
                    )
                );

            } else {

                $MJTC_url = majesticsupport::makeUrl(
                    array(
                        'mjsmod' => 'replies',
                        'mjslay' => 'replies',
                    )
                );
            }
        }

        wp_safe_redirect( $MJTC_url );
        exit;
    }

}

$MJTC_attachmentController = new MJTC_attachmentController();
?>
