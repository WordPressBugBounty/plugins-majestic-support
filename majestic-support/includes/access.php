<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

/*
 * One place that answers "may the current user do this to this ticket?".
 *
 * The controllers used to answer it case by case, and several render paths
 * answered it by setting permission_granted = true and only narrowing it when
 * the Agents add-on happened to be active. Every check here fails closed.
 *
 * The rules mirror the ones getTicketForDetail() already applies:
 *  - site and help-desk administrators may act on any ticket;
 *  - Agents add-on staff are governed by the add-on (per-task permissions and
 *    per-department ticket access), even when they also hold a WordPress role;
 *  - otherwise the agent role capability (ms_support_ticket_tickets) reads and
 *    works every ticket, as it always has;
 *  - customers reach only their own tickets, visitors only the ticket their
 *    token cookie names.
 */
class MJTC_access {

    /* Ticket ids an upload has already been authorised for in this request.
       Filled only by server-side callers, never from request data. */
    private static $MJTC_attachment_grants = array();

    /*
     * A strictly positive integer id, or 0.
     *
     * is_numeric() lets '1e3', ' 12' and '12.0' through, and absint() alone
     * turns '12abc' into 12. Only a value that survives the round trip unchanged
     * is accepted.
     */
    static function MJTC_id($MJTC_value) {
        if (is_int($MJTC_value)) {
            return $MJTC_value > 0 ? $MJTC_value : 0;
        }
        if (!is_string($MJTC_value) || $MJTC_value === '') {
            return 0;
        }
        $MJTC_id = absint($MJTC_value);
        return ((string) $MJTC_id === $MJTC_value) ? $MJTC_id : 0;
    }

    /* Site administrator or help-desk administrator. */
    static function MJTC_isAdmin() {
        return current_user_can('manage_options') || current_user_can('ms_support_ticket');
    }

    /* On the Agents add-on's staff list. */
    static function MJTC_isStaff() {
        if (!in_array('agent', majesticsupport::$_active_addons)) {
            return false;
        }
        return (bool) MJTC_includer::MJTC_getModel('agent')->isUserStaff();
    }

    /* Anybody the plugin treats as working the help desk rather than using it. */
    static function MJTC_isWorker() {
        return self::MJTC_isAdmin() || current_user_can('ms_support_ticket_tickets') || self::MJTC_isStaff();
    }

    /* Administrators always; add-on staff only when the add-on grants $MJTC_task. */
    static function MJTC_staffMay($MJTC_task) {
        if (self::MJTC_isAdmin()) {
            return true;
        }
        if (!self::MJTC_isStaff()) {
            return false;
        }
        return (bool) MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForTask($MJTC_task);
    }

    /* May the current user work this ticket (as opposed to merely own it)? */
    static function MJTC_canServiceTicket($MJTC_ticketid) {
        $MJTC_ticketid = self::MJTC_id($MJTC_ticketid);
        if (!$MJTC_ticketid) {
            return false;
        }
        if (self::MJTC_isAdmin()) {
            return true;
        }
        if (self::MJTC_isStaff()) {
            return (bool) MJTC_includer::MJTC_getModel('ticket')->validateTicketDetailForStaff($MJTC_ticketid);
        }
        return current_user_can('ms_support_ticket_tickets');
    }

    /* Service it, or own it. */
    static function MJTC_canReadTicket($MJTC_ticketid) {
        $MJTC_ticketid = self::MJTC_id($MJTC_ticketid);
        if (!$MJTC_ticketid) {
            return false;
        }
        if (self::MJTC_canServiceTicket($MJTC_ticketid)) {
            return true;
        }
        $MJTC_user = MJTC_includer::MJTC_getObjectClass('user');
        if ($MJTC_user->MJTC_isguest()) {
            return (bool) MJTC_includer::MJTC_getModel('ticket')->validateTicketDetailForVisitor($MJTC_ticketid);
        }
        // Visitor tickets carry uid 0, so a user without a help-desk profile row
        // must not match them.
        $MJTC_uid = (int) $MJTC_user->MJTC_uid();
        if ($MJTC_uid <= 0) {
            return false;
        }
        return $MJTC_uid === (int) MJTC_includer::MJTC_getModel('ticket')->getUIdById($MJTC_ticketid);
    }

    /* May the current user load this ticket into the edit form and change it? */
    static function MJTC_canEditTicket($MJTC_ticketid) {
        if (!self::MJTC_canServiceTicket($MJTC_ticketid)) {
            return false;
        }
        if (self::MJTC_isAdmin()) {
            return true;
        }
        if (self::MJTC_isStaff()) {
            return self::MJTC_staffMay('Edit Ticket');
        }
        return current_user_can('ms_support_ticket_tickets');
    }

    /*
     * May the current user rewrite a reply that has already been sent?
     *
     * Administrators, and add-on staff holding "Edit Reply" who may work the
     * ticket. Nobody else is offered the Edit Reply button, so nobody else may
     * use the task behind it.
     */
    static function MJTC_canEditReply($MJTC_ticketid) {
        if (self::MJTC_isAdmin()) {
            return self::MJTC_id($MJTC_ticketid) > 0;
        }
        return self::MJTC_staffMay('Edit Reply') && self::MJTC_canServiceTicket($MJTC_ticketid);
    }

    /*
     * Record that the caller has already authorised uploads for this ticket:
     * a ticket it has just created, or a reply that passed its ownership check.
     * A visitor creating a ticket has no token cookie yet, so without this the
     * upload layer could not tell that request from an attack.
     */
    static function MJTC_allowAttachmentsFor($MJTC_ticketid) {
        $MJTC_ticketid = self::MJTC_id($MJTC_ticketid);
        if ($MJTC_ticketid) {
            self::$MJTC_attachment_grants[$MJTC_ticketid] = true;
        }
    }

    /* May a file be attached to this ticket in this request? */
    static function MJTC_mayAttachTo($MJTC_ticketid) {
        $MJTC_ticketid = self::MJTC_id($MJTC_ticketid);
        if (!$MJTC_ticketid) {
            return false;
        }
        if (isset(self::$MJTC_attachment_grants[$MJTC_ticketid])) {
            return true;
        }
        return self::MJTC_canReadTicket($MJTC_ticketid);
    }

    /* Bump when MJTC_reconcileRoles() changes, so it runs once more everywhere. */
    const MJTC_ROLE_VERSION = 1;

    /*
     * Bring existing sites' roles in line, once per MJTC_ROLE_VERSION.
     *
     * Releases up to 1.2.0 gave ms_support_ticket_tickets to the Contributor
     * role on every activation, so every Contributor could read every ticket
     * and customer e-mail address. Activation never ran again on an upgrade,
     * which is why this runs from admin_init as well. Only the capability this
     * plugin granted is removed; the role keeps everything else.
     */
    static function MJTC_reconcileRoles($MJTC_force = false) {
        if (!$MJTC_force && (int) get_option('mjtc_role_version', 0) >= self::MJTC_ROLE_VERSION) {
            return false;
        }
        $MJTC_contributor = get_role('contributor');
        if ($MJTC_contributor && $MJTC_contributor->has_cap('ms_support_ticket_tickets')) {
            $MJTC_contributor->remove_cap('ms_support_ticket_tickets');
        }
        update_option('mjtc_role_version', self::MJTC_ROLE_VERSION, false);
        return true;
    }

    /* Stop the request with a 403. */
    static function MJTC_deny($MJTC_message = '') {
        if ($MJTC_message === '') {
            $MJTC_message = __('You are not allowed to perform this action.', 'majestic-support');
        }
        wp_die(
            esc_html($MJTC_message),
            esc_html__('Access Denied', 'majestic-support'),
            array('response' => 403)
        );
    }

}

?>
