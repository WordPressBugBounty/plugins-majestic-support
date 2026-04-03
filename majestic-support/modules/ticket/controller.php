<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_ticketController {

    function __construct() {
        self::handleRequest();
    }

    function handleRequest() {
        if (is_admin()) {
            $MJTC_defaultlayout = "tickets";
        } else
            $MJTC_defaultlayout = "myticket";
        $layout = MJTC_request::MJTC_getLayout('mjslay', null, $MJTC_defaultlayout);
        majesticsupport::$_data['sanitized_args']['MJTC_nonce'] = esc_html(wp_create_nonce('MJTC_nonce'));
        // remove this in the version 1.1.3
        include_once MJTC_PLUGIN_PATH . 'includes/updates/updates.php';
        MJTC_updates::MJTC_checkUpdates('113');
        // remove this in the version 1.1.3
        if (self::canaddfile($layout)) {
            switch ($layout) {
                case 'admin_tickets':
                    $list = MJTC_request::MJTC_getVar('list');
                    MJTC_includer::MJTC_getModel('ticket')->getTicketsForAdmin($list);
                    break;
                case 'admin_addticket':
                case 'addticket':

                    $id = MJTC_request::MJTC_getVar('majesticsupportid','',null);
                    $formid = MJTC_request::MJTC_getVar('formid');
					
                    if($formid == null){
                        $formid = MJTC_includer::MJTC_getModel('ticket')->getDefaultMultiFormId();
                    }
                    // below code to is hanlde parameters for easy digital downloads and woocommerce
                    if($id != null && MJTC_majesticsupportphplib::MJTC_strstr($id, '_')){
                        $id_array = MJTC_majesticsupportphplib::MJTC_explode('_', $id);
                        if($id_array[1] == 10){// tikcet id
                            $id = $id_array[0];
                        }elseif($id_array[1] == 11){ // edd order id
                            $id = NULL;
                            majesticsupport::$_data['edd_order_id'] = $id_array[0];
                        }else{
                            $id = NULL;
                        }
                    }
                    majesticsupport::$_data['permission_granted'] = true;

                    if (majesticsupport::$_data['permission_granted']) {
                        MJTC_includer::MJTC_getModel('ticket')->getTicketsForForm($id,$formid);

                        if(in_array('paidsupport', majesticsupport::$_active_addons) && class_exists('WooCommerce') && !is_admin() && !MJTC_includer::MJTC_getObjectClass('user')->MJTC_isguest()){
                            $selected = false;
                            $paidsupportid = MJTC_request::MJTC_getVar('paidsupportid',null,0);
                            if($paidsupportid){
								$paidsupport = MJTC_includer::MJTC_getModel('paidsupport')->getPaidSupportList(MJTC_includer::MJTC_getObjectClass('user')->MJTC_wpuid(), $paidsupportid);
                                if($paidsupport){
                                    majesticsupport::$_data['paidsupport'] = $paidsupport[0];
                                    $selected = true;
                                }
                            }
                            if(!$selected){
								$paidsupportitems = MJTC_includer::MJTC_getModel('paidsupport')->getPaidSupportList(MJTC_includer::MJTC_getObjectClass('user')->MJTC_wpuid());
                                if(count($paidsupportitems) == 1){
                                    majesticsupport::$_data['paidsupport'] = $paidsupportitems[0];
                                }else{
                                    majesticsupport::$_data['paidsupportitems'] = $paidsupportitems;
                                }
                            }
                        }

                    }
                    MJTC_includer::MJTC_getModel('majesticsupport')->updateColorFile();
                    break;
                case 'admin_ticketdetail':
                case 'ticketdetail':
                    $id = MJTC_request::MJTC_getVar('majesticsupportid');
                    majesticsupport::$_data['permission_granted'] = true;
                    majesticsupport::$_data['user_staff'] = false;
                    if ( in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) {
                        majesticsupport::$_data['user_staff'] = true;
                        majesticsupport::$_data['permission_granted'] = MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForTask('View Ticket');
                    }
                    if (majesticsupport::$_data['permission_granted']) {
                        MJTC_includer::MJTC_getModel('ticket')->getTicketForDetail($id);
                        //check if envato license support has expired
                        if(in_array('envatovalidation', majesticsupport::$_active_addons) && !empty(majesticsupport::$_data[0]->envatodata)){
                            $envlicense = json_decode(majesticsupport::$_data[0]->envatodata, true);
                            if(!empty($envlicense['supporteduntil']) && date_i18n('Y-m-d') > date_i18n('Y-m-d',MJTC_majesticsupportphplib::MJTC_strtotime($envlicense['supporteduntil']))){
                                MJTC_message::MJTC_setMessage(esc_html(__('Support for this Envato license has expired', 'majestic-support')), 'error');
                            }
                            majesticsupport::$_data[0]->envatodata = $envlicense;
                        }
                    }
                    break;
                case 'myticket':
                    $list = MJTC_request::MJTC_getVar('list');
                    MJTC_includer::MJTC_getModel('ticket')->getMyTickets($list);
                    break;
                case 'ticketstatus':
                    break;
                case 'visitormessagepage':
                    break;
                default:
                    exit;

            }
            $module = (is_admin()) ? 'page' : 'mjsmod';
            $module = MJTC_request::MJTC_getVar($module, null, 'ticket');
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

    function closeticket() {
        $id = MJTC_request::MJTC_getVar('ticketid');
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $nonce, 'close-ticket-'.$id) ) {
            die( 'Security check Failed' );
        }
        $internalid = MJTC_request::MJTC_getVar('internalid');
        MJTC_includer::MJTC_getModel('ticket')->closeTicket($id, $internalid);
        if (is_admin()) {
            $MJTC_url = admin_url("admin.php?page=majesticsupport_ticket&mjslay=tickets");
        } else {
            $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'ticket', 'mjslay'=>'ticketdetail','majesticsupportid'=>$id));
        }
        wp_safe_redirect($MJTC_url);
        exit;
    }

    function lockticket() {
        $id = MJTC_request::MJTC_getVar('ticketid');
        MJTC_includer::MJTC_getModel('ticket')->lockTicket($id);
        if (is_admin()) {
            $MJTC_url = admin_url("admin.php?page=majesticsupport_ticket&mjslay=ticketdetail&majesticsupportid=" . esc_attr($id));
        } else {
            $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'ticket', 'mjslay'=>'ticketdetail', 'majesticsupportid'=>$id));
        }
        wp_safe_redirect($MJTC_url);
        exit;
    }

    function unlockticket() {
        $id = MJTC_request::MJTC_getVar('ticketid');
        MJTC_includer::MJTC_getModel('ticket')->unLockTicket($id);
        if (is_admin()) {
            $MJTC_url = admin_url("admin.php?page=majesticsupport_ticket&mjslay=ticketdetail&majesticsupportid=" . esc_attr($id));
        } else {
            $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'ticket', 'mjslay'=>'ticketdetail', 'majesticsupportid'=>$id));
        }
        wp_safe_redirect($MJTC_url);
        exit;
    }

    static function saveticket() {
        $id = MJTC_request::MJTC_getVar('id');
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $nonce, 'save-ticket-'.$id) ) {
            die( 'Security check Failed' );
        }
        $MJTC_data = MJTC_request::get('post');
        $result = MJTC_includer::MJTC_getModel('ticket')->storeTickets($MJTC_data);
        if (is_admin()) {
            if($result == false){
                $MJTC_url = admin_url("admin.php?page=majesticsupport_ticket&mjslay=addticket");
				if(in_array('multiform', majesticsupport::$_active_addons)){
					$formid = $MJTC_data['multiformid'];
					$MJTC_url = admin_url("admin.php?page=majesticsupport_ticket&mjslay=addticket&formid=".esc_attr($formid));
				}	
            }else{
                $MJTC_url = admin_url("admin.php?page=majesticsupport_ticket&mjslay=tickets");
            }
        } else {
            if (MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid() == 0) { // visitor
                if ($result == false) { // error on captcha or ticket validation
                    $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'ticket', 'mjslay'=>'addticket'));
					if(in_array('multiform', majesticsupport::$_active_addons)){
						$formid = $MJTC_data['multiformid'];
						$MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'ticket', 'mjslay'=>'addticket', 'formid'=> $formid));
					}	
                } else { // all things perfect
                    if(in_array('actions',majesticsupport::$_active_addons)){
                        $MJTC_ticketid = $result;
                        $token = MJTC_includer::MJTC_getModel('ticket')->getTicketToken($MJTC_ticketid);
                        $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'ticket', 'mjslay'=>'visitormessagepage', 'majesticsupportid'=>$token));
                    }else{
                        $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'majesticsupport', 'mjslay'=>'controlpanel'));
                    }
                }
            } else {
                if ($result == false) { // error on captcha or ticket validation
                    $addticket = ( in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) ? 'staffaddticket' : 'addticket';
                    $module1 = ( in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) ? 'agent' : 'ticket';
                    $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>$module1, 'mjslay'=>$addticket));
					if(in_array('multiform', majesticsupport::$_active_addons)){
						$formid = $MJTC_data['multiformid'];
						$MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>$module1, 'mjslay'=>$addticket, 'formid'=> $formid));
					}	
                } else {
                    $myticket = ( in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) ? 'staffmyticket' : 'myticket';
                    $module1 = ( in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) ? 'agent' : 'ticket';
                    $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>$module1, 'mjslay'=>$myticket));
                }
            }
        }
        if($result == false){
            MJTC_formfield::MJTC_setFormData($MJTC_data);
        }
        wp_safe_redirect($MJTC_url);
        exit;
    }

    static function changestatus() {
        $MJTC_data = MJTC_request::get('post');
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $nonce, 'change-status-'.$MJTC_data['ticketid']) ) {
            die( 'Security check Failed' );
        }
        MJTC_includer::MJTC_getModel('ticket')->tickChangeStatus($MJTC_data);
        if (is_admin()) {
            $MJTC_url = admin_url("admin.php?page=majesticsupport_ticket&mjslay=ticketdetail&majesticsupportid=" . esc_attr($MJTC_data['ticketid']));
        } else {
            $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'ticket', 'mjslay'=>'ticketdetail', 'majesticsupportid'=>$MJTC_data['ticketid']));
        }
        wp_safe_redirect($MJTC_url);
        exit;
    }

    static function transferdepartment() {
        $MJTC_data = MJTC_request::get('post');
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $nonce, 'transfer-department-'.$MJTC_data['ticketid']) ) {
            die( 'Security check Failed' );
        }
        MJTC_includer::MJTC_getModel('ticket')->tickDepartmentTransfer($MJTC_data);
        if (is_admin()) {
            $MJTC_url = admin_url("admin.php?page=majesticsupport_ticket&mjslay=ticketdetail&majesticsupportid=" . esc_attr($MJTC_data['ticketid']));
        } else {
            $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'ticket', 'mjslay'=>'ticketdetail', 'majesticsupportid'=>$MJTC_data['ticketid']));
        }
        wp_safe_redirect($MJTC_url);
        exit;
    }

    static function assigntickettostaff() {
        $MJTC_data = MJTC_request::get('post');
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $nonce, 'assign-ticket-to-staff-'.$MJTC_data['ticketid']) ) {
            die( 'Security check Failed' );
        }
        MJTC_includer::MJTC_getModel('ticket')->assignTicketToStaff($MJTC_data);
        if (is_admin()) {
            $MJTC_url = admin_url("admin.php?page=majesticsupport_ticket&mjslay=ticketdetail&majesticsupportid=" . esc_attr($MJTC_data['ticketid']));
        } else {
            $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'ticket', 'mjslay'=>'ticketdetail', 'majesticsupportid'=>$MJTC_data['ticketid']));
        }
        wp_safe_redirect($MJTC_url);
        exit;
    }

    static function deleteticket() {
        $id = MJTC_request::MJTC_getVar('ticketid');
        $internalid = MJTC_request::MJTC_getVar('internalid');
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $nonce, 'delete-ticket-'.$id) ) {
            die( 'Security check Failed' );
        }
        MJTC_includer::MJTC_getModel('ticket')->removeTicket($id, $internalid);
        if (is_admin()) {
            $MJTC_url = admin_url("admin.php?page=majesticsupport_ticket&mjslay=tickets");
        } elseif ( in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) {
            $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'agent', 'mjslay'=>'staffmyticket'));
        } elseif (MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid() == 0) { // visitor
            $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'ticket', 'mjslay'=>'ticketdetail', 'majesticsupportid'=>$id));
        } else {
            $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'ticket', 'mjslay'=>'myticket'));
        }
        wp_safe_redirect($MJTC_url);
        exit;
    }

    static function enforcedeleteticket() {
        // Sanitize and validate ticket ID
        $id = MJTC_request::MJTC_getVar('ticketid');
        if (!is_numeric($id) || intval($id) <= 0) {
            die('Invalid ticket ID');
        }
        $id = absint($id); // Ensure positive integer

        // Validate Nonce
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (!wp_verify_nonce($nonce, 'enforce-delete-ticket-' . $id)) {
            die('Security check Failed');
        }

        // Only allow admins to delete any ticket
        if (!current_user_can('manage_options')) {
            die('You do not have permission to delete this ticket');
        }

        // Delete the ticket securely
        MJTC_includer::MJTC_getModel('ticket')->removeEnforceTicket($id);

        // Redirect securely
        if (is_admin()) {
            $MJTC_url = admin_url("admin.php?page=majesticsupport_ticket&mjslay=tickets");
        } else {
            $MJTC_url = majesticsupport::makeUrl(array('mjsmod' => 'ticket', 'mjslay' => 'myticket'));
        }
        
        wp_safe_redirect($MJTC_url);
        exit;
    }

    static function changepriority() {
        $id = MJTC_request::MJTC_getVar('ticketid');
        $priorityid = MJTC_request::MJTC_getVar('priority');
        MJTC_includer::MJTC_getModel('ticket')->changeTicketPriority($id, $priorityid);
        if (is_admin()) {
            $MJTC_url = admin_url("admin.php?page=majesticsupport_ticket&mjslay=ticketdetail&majesticsupportid=" . esc_attr($id));
        } else {
            $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'ticket', 'mjslay'=>'ticketdetail', 'majesticsupportid'=>$id));
        }
        wp_safe_redirect($MJTC_url);
        exit;
    }

    static function reopenticket() { // for user
        $MJTC_ticketid = MJTC_request::MJTC_getVar('ticketid');
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $nonce, 'reopen-ticket-'.$MJTC_ticketid) ) {
            die( 'Security check Failed' );
        }
        $internalid = MJTC_request::MJTC_getVar('internalid');
        $MJTC_data['ticketid'] = $MJTC_ticketid;
        $MJTC_data['internalid'] = $internalid;
        MJTC_includer::MJTC_getModel('ticket')->reopenTicket($MJTC_data);
        $MJTC_url = "&mjslay=ticketdetail&majesticsupportid=" . esc_attr($MJTC_data['ticketid']);
        if (is_admin()) {
            $MJTC_url = admin_url("admin.php?page=majesticsupport_ticket" . esc_attr($MJTC_url));
        } else {
            $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'ticket', 'mjslay'=>'ticketdetail', 'majesticsupportid'=>$MJTC_data['ticketid']));
        }
        wp_safe_redirect($MJTC_url);
        exit;
    }

    static function actionticket() {
        $MJTC_data = MJTC_request::get('post');
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $nonce, 'action-ticket-'.$MJTC_data['ticketid']) ) {
            die( 'Security check Failed' );
        }
        /* to handle actions */
        switch ($MJTC_data['actionid']) {
            case 1: /* Change Priority Ticket */
                MJTC_includer::MJTC_getModel('ticket')->changeTicketPriority($MJTC_data['ticketid'], $MJTC_data['priority']);
                $MJTC_url = "&mjslay=ticketdetail&majesticsupportid=" . esc_attr($MJTC_data['ticketid']);
                break;
            case 2: /* close ticket */
                MJTC_includer::MJTC_getModel('ticket')->closeTicket($MJTC_data['ticketid']);
                $MJTC_url = "&mjslay=ticketdetail&majesticsupportid=" . esc_attr($MJTC_data['ticketid']);
                break;
            case 3: /* Reopen Ticket */
                MJTC_includer::MJTC_getModel('ticket')->reopenTicket($MJTC_data);
                $MJTC_url = "&mjslay=ticketdetail&majesticsupportid=" . esc_attr($MJTC_data['ticketid']);
                break;
            case 4: /* Lock Ticket */
                if(in_array('actions', majesticsupport::$_active_addons)){
                    MJTC_includer::MJTC_getModel('actions')->lockTicket($MJTC_data['ticketid']);
                    $MJTC_url = "&mjslay=ticketdetail&majesticsupportid=" . esc_attr($MJTC_data['ticketid']);
                }
                break;
            case 5: /* Unlock ticket */
                if(in_array('actions', majesticsupport::$_active_addons)){
                    MJTC_includer::MJTC_getModel('actions')->unLockTicket($MJTC_data['ticketid']);
                    $MJTC_url = "&mjslay=ticketdetail&majesticsupportid=" . esc_attr($MJTC_data['ticketid']);
                }
                break;
            case 6: /* Banned Email */
                if(in_array('banemail', majesticsupport::$_active_addons)){
                    MJTC_includer::MJTC_getModel('ticket')->banEmail($MJTC_data);
                    $MJTC_url = "&mjslay=ticketdetail&majesticsupportid=" . esc_attr($MJTC_data['ticketid']);
                }
                break;
            case 7: /* Unban Email */
                if(in_array('banemail', majesticsupport::$_active_addons)){
                    MJTC_includer::MJTC_getModel('ticket')->unbanEmail($MJTC_data);
                    $MJTC_url = "&mjslay=ticketdetail&majesticsupportid=" . esc_attr($MJTC_data['ticketid']);
                }
                break;
            case 8: /* Mark over due */
                if(in_array('overdue', majesticsupport::$_active_addons)){
                    MJTC_includer::MJTC_getModel('overdue')->markOverDueTicket($MJTC_data);
                    $MJTC_url = "&mjslay=ticketdetail&majesticsupportid=" . esc_attr($MJTC_data['ticketid']);
                }
                break;
            case 9: /* In Progress */
                if(in_array('actions', majesticsupport::$_active_addons)){
                    MJTC_includer::MJTC_getModel('ticket')->markTicketInProgress($MJTC_data);
                    $MJTC_url = "&mjslay=ticketdetail&majesticsupportid=" . esc_attr($MJTC_data['ticketid']);
                }
                break;
            case 10: /* ban Email & close ticket */
                MJTC_includer::MJTC_getModel('ticket')->banEmailAndCloseTicket($MJTC_data);
                $MJTC_url = "&mjslay=ticketdetail&majesticsupportid=" . esc_attr($MJTC_data['ticketid']);
                break;
            case 11: /* unMark over due */
                if(in_array('overdue', majesticsupport::$_active_addons)){
                    MJTC_includer::MJTC_getModel('overdue')->unMarkOverDueTicket($MJTC_data);;
                    $MJTC_url = "&mjslay=ticketdetail&majesticsupportid=" . esc_attr($MJTC_data['ticketid']);
                }
                break;
        }

        if (is_admin()) {
            $MJTC_url = admin_url("admin.php?page=majesticsupport_ticket" . $MJTC_url);
        } else {
            $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'ticket', 'mjslay'=>'ticketdetail', 'majesticsupportid'=>$MJTC_data['ticketid']));
        }
        wp_safe_redirect($MJTC_url);
        exit;
    }

    static function showticketstatus() {
        $token = MJTC_request::MJTC_getVar('token');
        if ($token == null) { // in case it come from ticket status form
            $nonce = MJTC_request::MJTC_getVar('_wpnonce');
            if (! wp_verify_nonce( $nonce, 'show-ticket-status') ) {
                //die( 'Security check Failed' );
            }
            $emailaddress = MJTC_request::MJTC_getVar('email');
            $trackingid = MJTC_request::MJTC_getVar('ticketid');
            $MJTC_tickettoken = MJTC_request::MJTC_getVar('tickettoken');
            if(!empty($emailaddress) AND !empty($trackingid)){
                $token = MJTC_includer::MJTC_getModel('ticket')->getTokenByEmailAndTrackingId($emailaddress, $trackingid);
            }else if(!empty($MJTC_tickettoken)){
                $token = $MJTC_tickettoken;
            }
            if($token){
                include_once MJTC_PLUGIN_PATH . 'includes/encoder.php';
                $encoder = new MJTC_encoder();
                $token = $encoder->MJTC_encrypt(wp_json_encode(array('token' => $token, 'sitelink' => get_option('ms_encripted_site_link'))));
                MJTC_majesticsupportphplib::MJTC_setcookie('majestic-support-token-tkstatus',$token ,0, COOKIEPATH);
                if ( SITECOOKIEPATH != COOKIEPATH ){
                    MJTC_majesticsupportphplib::MJTC_setcookie('majestic-support-token-tkstatus',$token ,0, SITECOOKIEPATH);
                }
                $MJTC_ticketid = MJTC_includer::MJTC_getModel('ticket')->getTicketidForVisitorUsingToken($token);
                if ($MJTC_ticketid) {
                    $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'ticket', 'mjslay'=>'ticketdetail', 'majesticsupportid'=>$MJTC_ticketid));
                } else {
                    $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'ticket', 'mjslay'=>'ticketstatus'));
                    MJTC_message::MJTC_setMessage(esc_html(__('Record not found', 'majestic-support')), 'error');
                }
            } else {
                $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'ticket', 'mjslay'=>'ticketstatus'));
                MJTC_message::MJTC_setMessage(esc_html(__('Record not found', 'majestic-support')), 'error');
            }
        } else {
            MJTC_majesticsupportphplib::MJTC_setcookie('majestic-support-token-tkstatus',$token ,0, COOKIEPATH);
            if ( SITECOOKIEPATH != COOKIEPATH ){
                MJTC_majesticsupportphplib::MJTC_setcookie('majestic-support-token-tkstatus',$token ,0, SITECOOKIEPATH);
            }
            $MJTC_ticketid = MJTC_includer::MJTC_getModel('ticket')->getTicketidForVisitor($token);
            if ($MJTC_ticketid) {
                $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'ticket', 'mjslay'=>'ticketdetail', 'majesticsupportid'=>$MJTC_ticketid));
            } else {
                $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'ticket', 'mjslay'=>'ticketstatus'));
                MJTC_message::MJTC_setMessage(esc_html(__('Record not found', 'majestic-support')), 'error');
            }
        }
        wp_safe_redirect($MJTC_url);
        exit;
    }

    static function downloadall() {
        $id = MJTC_request::MJTC_getVar('id');
        MJTC_includer::MJTC_getModel('attachment')->getAllDownloads();
        if (is_admin()) {
            $MJTC_url = admin_url("admin.php?page=majesticsupport_ticket&mjslay=ticketdetail");
        } else {
            $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'ticket','mjslay'=>'ticketdetail','majesticsupportid'=>'$id','mspageid'=>majesticsupport::getPageid()));
        }
        wp_safe_redirect($MJTC_url);
        exit;
    }
    static function downloadallforreply() {
        $downloadid = MJTC_request::MJTC_getVar('downloadid');
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $nonce, 'download-all-for-reply-'.$downloadid) ) {
            die( 'Security check Failed' );
        }
        MJTC_includer::MJTC_getModel('attachment')->getAllReplyDownloads();
        if (is_admin()) {
          $MJTC_url = admin_url("admin.php?page=majesticsupport_ticket&mjslay=ticketdetail");
          } else {
          $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'ticket','mjslay'=>'ticketdetail','majesticsupportid'=>'$id','mspageid'=>majesticsupport::getPageid()));
          }
          wp_safe_redirect($MJTC_url);
          exit;
    }

    function downloadbyid(){
        $id = MJTC_request::MJTC_getVar('id');
        MJTC_includer::MJTC_getModel('attachment')->getDownloadAttachmentById($id);
    }


    function downloadbyname(){
        $name = MJTC_request::MJTC_getVar('name');
        $id = MJTC_request::MJTC_getVar('id');
        $name = MJTC_majesticsupportphplib::MJTC_clean_file_path($name);
        MJTC_includer::MJTC_getModel('attachment')->getDownloadAttachmentByName($name,$id);
    }

    function mergeticket() {
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $nonce, 'merge-ticket') ) {
            die( 'Security check Failed' );
        }
        $MJTC_data = MJTC_request::get('post');
        MJTC_includer::MJTC_getModel('mergeticket')->storeMergeTicket($MJTC_data);
        if(is_admin()){
             $MJTC_url = admin_url("admin.php?page=majesticsupport_ticket&mjslay=ticketdetail&majesticsupportid=" .esc_attr($MJTC_data['secondaryticket']));
        }else if( in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()){
            $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'ticket','mjslay'=>'ticketdetail','majesticsupportid'=>$MJTC_data['secondaryticket']));
        }
        wp_safe_redirect($MJTC_url);
        exit;
    }
}
$MJTC_ticketController = new MJTC_ticketController();
?>
