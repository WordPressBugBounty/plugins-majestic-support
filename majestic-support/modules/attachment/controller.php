<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_attachmentController {

    function __construct() {
        self::handleRequest();
    }

    function handleRequest() {
        $MJTC_layout = MJTC_request::MJTC_getLayout('mjslay', null, 'getattachments');
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

    static function saveattachments() {
        $MJTC_ticketid = MJTC_access::MJTC_id(MJTC_request::MJTC_getVar('ticketid', 'post'));
        $MJTC_nonce    = MJTC_request::MJTC_getVar('_wpnonce');

        if (!$MJTC_ticketid || !wp_verify_nonce($MJTC_nonce, 'save-attachment-' . $MJTC_ticketid)) {
            wp_die(
                esc_html__('Security check failed.', 'majestic-support'),
                esc_html__('Security Error', 'majestic-support'),
                array('response' => 403)
            );
        }
        if (!is_user_logged_in()) {
            MJTC_access::MJTC_deny();
        }
        // The ticket's owner, or somebody who may work it.
        if (!MJTC_access::MJTC_canReadTicket($MJTC_ticketid)) {
            MJTC_access::MJTC_deny();
        }

        $MJTC_data = MJTC_request::get('post');
        $MJTC_data['ticketid'] = $MJTC_ticketid;
        // Ticket-level attachments only: a file posted here must not be filed
        // under somebody else's reply on the same ticket.
        unset($MJTC_data['replyattachmentid']);
        MJTC_access::MJTC_allowAttachmentsFor($MJTC_ticketid);
        MJTC_includer::MJTC_getModel('attachment')->storeAttachments($MJTC_data);
        if (is_admin()) {
            $MJTC_url = admin_url("admin.php?page=majesticsupport_ticket&mjslay=ticketdetail&majesticsupportid=" . $MJTC_ticketid);
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
