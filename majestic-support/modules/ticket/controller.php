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
        $MJTC_layout = MJTC_request::MJTC_getLayout('mjslay', null, $MJTC_defaultlayout);
        majesticsupport::$_data['sanitized_args']['MJTC_nonce'] = esc_html(wp_create_nonce('MJTC_nonce'));
        // remove this in the version 1.1.3
        include_once MJTC_PLUGIN_PATH . 'includes/updates/updates.php';
        MJTC_updates::MJTC_checkUpdates('112');
        // remove this in the version 1.1.3
        if (self::canaddfile($MJTC_layout)) {
            switch ($MJTC_layout) {
                case 'admin_tickets':
                    $MJTC_list = MJTC_request::MJTC_getVar('list');
                    MJTC_includer::MJTC_getModel('ticket')->getTicketsForAdmin($MJTC_list);
                    break;
                case 'admin_addticket':
                case 'addticket':

                    $MJTC_id = MJTC_request::MJTC_getVar('majesticsupportid','',null);
                    $MJTC_formid = MJTC_request::MJTC_getVar('formid');
					
                    if($MJTC_formid == null){
                        $MJTC_formid = MJTC_includer::MJTC_getModel('ticket')->getDefaultMultiFormId();
                    }
                    // below code to is hanlde parameters for easy digital downloads and woocommerce
                    if($MJTC_id != null && MJTC_majesticsupportphplib::MJTC_strstr($MJTC_id, '_')){
                        $MJTC_id_array = MJTC_majesticsupportphplib::MJTC_explode('_', $MJTC_id);
                        if($MJTC_id_array[1] == 10){// tikcet id
                            $MJTC_id = $MJTC_id_array[0];
                        }elseif($MJTC_id_array[1] == 11){ // edd order id
                            $MJTC_id = NULL;
                            majesticsupport::$_data['edd_order_id'] = $MJTC_id_array[0];
                        }else{
                            $MJTC_id = NULL;
                        }
                    }
                    majesticsupport::$_data['permission_granted'] = true;

                    if (majesticsupport::$_data['permission_granted']) {
                        MJTC_includer::MJTC_getModel('ticket')->getTicketsForForm($MJTC_id,$MJTC_formid);

                        if(in_array('paidsupport', majesticsupport::$_active_addons) && class_exists('WooCommerce') && !is_admin() && !MJTC_includer::MJTC_getObjectClass('user')->MJTC_isguest()){
                            $MJTC_selected = false;
                            $MJTC_paidsupportid = MJTC_request::MJTC_getVar('paidsupportid',null,0);
                            if($MJTC_paidsupportid){
								$MJTC_paidsupport = MJTC_includer::MJTC_getModel('paidsupport')->getPaidSupportList(MJTC_includer::MJTC_getObjectClass('user')->MJTC_wpuid(), $MJTC_paidsupportid);
                                if($MJTC_paidsupport){
                                    majesticsupport::$_data['paidsupport'] = $MJTC_paidsupport[0];
                                    $MJTC_selected = true;
                                }
                            }
                            if(!$MJTC_selected){
								$MJTC_paidsupportitems = MJTC_includer::MJTC_getModel('paidsupport')->getPaidSupportList(MJTC_includer::MJTC_getObjectClass('user')->MJTC_wpuid());
                                if(count($MJTC_paidsupportitems) == 1){
                                    majesticsupport::$_data['paidsupport'] = $MJTC_paidsupportitems[0];
                                }else{
                                    majesticsupport::$_data['paidsupportitems'] = $MJTC_paidsupportitems;
                                }
                            }
                        }

                    }
                    MJTC_includer::MJTC_getModel('majesticsupport')->updateColorFile();
                    break;
                case 'admin_ticketdetail':
                case 'ticketdetail':
                    $MJTC_id = MJTC_request::MJTC_getVar('majesticsupportid');
                    majesticsupport::$_data['permission_granted'] = true;
                    majesticsupport::$_data['user_staff'] = false;
                    if ( in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) {
                        majesticsupport::$_data['user_staff'] = true;
                        majesticsupport::$_data['permission_granted'] = MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForTask('View Ticket');
                    }
                    if (majesticsupport::$_data['permission_granted']) {
                        MJTC_includer::MJTC_getModel('ticket')->getTicketForDetail($MJTC_id);
                        //check if envato license support has expired
                        if(in_array('envatovalidation', majesticsupport::$_active_addons) && !empty(majesticsupport::$_data[0]->envatodata)){
                            $MJTC_envlicense = json_decode(majesticsupport::$_data[0]->envatodata, true);
                            if(!empty($MJTC_envlicense['supporteduntil']) && date_i18n('Y-m-d') > date_i18n('Y-m-d',MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_envlicense['supporteduntil']))){
                                MJTC_message::MJTC_setMessage(esc_html(__('Support for this Envato license has expired', 'majestic-support')), 'error');
                            }
                            majesticsupport::$_data[0]->envatodata = $MJTC_envlicense;
                        }
                    }
                    break;
                case 'myticket':
                    $MJTC_list = MJTC_request::MJTC_getVar('list');
                    MJTC_includer::MJTC_getModel('ticket')->getMyTickets($MJTC_list);
                    break;
                case 'ticketstatus':
                    break;
                case 'visitormessagepage':
                    break;
                default:
                    exit;

            }
            $MJTC_module = (is_admin()) ? 'page' : 'mjsmod';
            $MJTC_module = MJTC_request::MJTC_getVar($MJTC_module, null, 'ticket');
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

    function closeticket() {
        $MJTC_id = MJTC_request::MJTC_getVar('ticketid');
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'close-ticket-'.$MJTC_id) ) {
            die( 'Security check Failed' );
        }
        $MJTC_internalid = MJTC_request::MJTC_getVar('internalid');
        MJTC_includer::MJTC_getModel('ticket')->closeTicket($MJTC_id, $MJTC_internalid);
        if (is_admin()) {
            $MJTC_url = admin_url("admin.php?page=majesticsupport_ticket&mjslay=tickets");
        } else {
            $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'ticket', 'mjslay'=>'ticketdetail','majesticsupportid'=>$MJTC_id));
        }
        wp_safe_redirect($MJTC_url);
        exit;
    }

    function lockticket() {
        $MJTC_id = MJTC_request::MJTC_getVar('ticketid');
        MJTC_includer::MJTC_getModel('ticket')->lockTicket($MJTC_id);
        if (is_admin()) {
            $MJTC_url = admin_url("admin.php?page=majesticsupport_ticket&mjslay=ticketdetail&majesticsupportid=" . esc_attr($MJTC_id));
        } else {
            $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'ticket', 'mjslay'=>'ticketdetail', 'majesticsupportid'=>$MJTC_id));
        }
        wp_safe_redirect($MJTC_url);
        exit;
    }

    function unlockticket() {
        $MJTC_id = MJTC_request::MJTC_getVar('ticketid');
        MJTC_includer::MJTC_getModel('ticket')->unLockTicket($MJTC_id);
        if (is_admin()) {
            $MJTC_url = admin_url("admin.php?page=majesticsupport_ticket&mjslay=ticketdetail&majesticsupportid=" . esc_attr($MJTC_id));
        } else {
            $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'ticket', 'mjslay'=>'ticketdetail', 'majesticsupportid'=>$MJTC_id));
        }
        wp_safe_redirect($MJTC_url);
        exit;
    }

    static function saveticket() {
        $MJTC_id = MJTC_request::MJTC_getVar('id');
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'save-ticket-'.$MJTC_id) ) {
            die( 'Security check Failed' );
        }
        $MJTC_data = MJTC_request::get('post');
        $MJTC_result = MJTC_includer::MJTC_getModel('ticket')->storeTickets($MJTC_data);
        if (is_admin()) {
            if($MJTC_result == false){
                $MJTC_url = admin_url("admin.php?page=majesticsupport_ticket&mjslay=addticket");
				if(in_array('multiform', majesticsupport::$_active_addons)){
					$MJTC_formid = $MJTC_data['multiformid'];
					$MJTC_url = admin_url("admin.php?page=majesticsupport_ticket&mjslay=addticket&formid=".esc_attr($MJTC_formid));
				}	
            }else{
                $MJTC_url = admin_url("admin.php?page=majesticsupport_ticket&mjslay=tickets");
            }
        } else {
            if (MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid() == 0) { // visitor
                if ($MJTC_result == false) { // error on captcha or ticket validation
                    $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'ticket', 'mjslay'=>'addticket'));
					if(in_array('multiform', majesticsupport::$_active_addons)){
						$MJTC_formid = $MJTC_data['multiformid'];
						$MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'ticket', 'mjslay'=>'addticket', 'formid'=> $MJTC_formid));
					}	
                } else { // all things perfect
                    if(in_array('actions',majesticsupport::$_active_addons)){
                        $MJTC_ticketid = $MJTC_result;
                        $MJTC_token = MJTC_includer::MJTC_getModel('ticket')->getTicketToken($MJTC_ticketid);
                        $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'ticket', 'mjslay'=>'visitormessagepage', 'majesticsupportid'=>$MJTC_token));
                    }else{
                        $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'majesticsupport', 'mjslay'=>'controlpanel'));
                    }
                }
            } else {
                if ($MJTC_result == false) { // error on captcha or ticket validation
                    $MJTC_addticket = ( in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) ? 'staffaddticket' : 'addticket';
                    $MJTC_module1 = ( in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) ? 'agent' : 'ticket';
                    $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>$MJTC_module1, 'mjslay'=>$MJTC_addticket));
					if(in_array('multiform', majesticsupport::$_active_addons)){
						$MJTC_formid = $MJTC_data['multiformid'];
						$MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>$MJTC_module1, 'mjslay'=>$MJTC_addticket, 'formid'=> $MJTC_formid));
					}	
                } else {
                    $MJTC_myticket = ( in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) ? 'staffmyticket' : 'myticket';
                    $MJTC_module1 = ( in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) ? 'agent' : 'ticket';
                    $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>$MJTC_module1, 'mjslay'=>$MJTC_myticket));
                }
            }
        }
        if($MJTC_result == false){
            MJTC_formfield::MJTC_setFormData($MJTC_data);
        }
        wp_safe_redirect($MJTC_url);
        exit;
    }

    static function changestatus() {
        $MJTC_data = MJTC_request::get('post');
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'change-status-'.$MJTC_data['ticketid']) ) {
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
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'transfer-department-'.$MJTC_data['ticketid']) ) {
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
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'assign-ticket-to-staff-'.$MJTC_data['ticketid']) ) {
            die( 'Security check Failed' );
        }
        MJTC_includer::MJTC_getModel('ticket')->assignTicketToStaff($MJTC_data);
        if (is_admin()) {
            $MJTC_ticketlisting = MJTC_request::MJTC_getVar('ticketlisting');
            if (!empty($MJTC_ticketlisting)) {
                $MJTC_url = admin_url("admin.php?page=majesticsupport_ticket");
            } else {
                $MJTC_url = admin_url("admin.php?page=majesticsupport_ticket&mjslay=ticketdetail&majesticsupportid=" . esc_attr($MJTC_data['ticketid']));
            }
        } else {
            $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'ticket', 'mjslay'=>'ticketdetail', 'majesticsupportid'=>$MJTC_data['ticketid']));
        }
        wp_safe_redirect($MJTC_url);
        exit;
    }

    static function assignmultipletickettostaff() {
        $MJTC_data = MJTC_request::get('post');
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'assign-ticket-to-staff-'.$MJTC_data['ticketid']) ) {
            die( 'Security check Failed' );
        }
        $ticketIds = explode(',', $MJTC_data['ticketIds']);
        if (!empty($ticketIds)) {
            unset($MJTC_data['ticketIds']);
            foreach ($ticketIds as $ticketId) {
                $MJTC_data['ticketid'] = $ticketId;
                MJTC_includer::MJTC_getModel('ticket')->assignTicketToStaff($MJTC_data);
            }
        }
        if (is_admin()) {
            $MJTC_url = admin_url("admin.php?page=majesticsupport_ticket");
            wp_safe_redirect($MJTC_url);
            exit;
        }
    }

    static function deleteticket() {
        $MJTC_id = MJTC_request::MJTC_getVar('ticketid');
        $MJTC_internalid = MJTC_request::MJTC_getVar('internalid');
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'delete-ticket-'.$MJTC_id) ) {
            die( 'Security check Failed' );
        }
        MJTC_includer::MJTC_getModel('ticket')->removeTicket($MJTC_id, $MJTC_internalid);
        if (is_admin()) {
            $MJTC_url = admin_url("admin.php?page=majesticsupport_ticket&mjslay=tickets");
        } elseif ( in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) {
            $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'agent', 'mjslay'=>'staffmyticket'));
        } elseif (MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid() == 0) { // visitor
            $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'ticket', 'mjslay'=>'ticketdetail', 'majesticsupportid'=>$MJTC_id));
        } else {
            $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'ticket', 'mjslay'=>'myticket'));
        }
        wp_safe_redirect($MJTC_url);
        exit;
    }

    static function enforcedeleteticket() {
        // Sanitize and validate ticket ID
        $MJTC_id = MJTC_request::MJTC_getVar('ticketid');
        if (!is_numeric($MJTC_id) || intval($MJTC_id) <= 0) {
            die('Invalid ticket ID');
        }
        $MJTC_id = absint($MJTC_id); // Ensure positive integer

        // Validate Nonce
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (!wp_verify_nonce($MJTC_nonce, 'enforce-delete-ticket-' . $MJTC_id)) {
            die('Security check Failed');
        }

        // Only allow admins to delete any ticket
        if (!current_user_can('manage_options')) {
            die('You do not have permission to delete this ticket');
        }

        // Delete the ticket securely
        MJTC_includer::MJTC_getModel('ticket')->removeEnforceTicket($MJTC_id);

        // Redirect securely
        if (is_admin()) {
            $MJTC_url = admin_url("admin.php?page=majesticsupport_ticket&mjslay=tickets");
        } else {
            $MJTC_url = majesticsupport::makeUrl(array('mjsmod' => 'ticket', 'mjslay' => 'myticket'));
        }
        
        wp_safe_redirect($MJTC_url);
        exit;
    }

    static function deleteInternalNote() {
        $MJTC_id = MJTC_request::MJTC_getVar('ticketid');
        $MJTC_internalid = MJTC_request::MJTC_getVar('internalid');
        $MJTC_internalnoteid = MJTC_request::MJTC_getVar('internalnoteid');
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'delete-internal-note-'.$MJTC_internalnoteid) ) {
            die( 'Security check Failed' );
        }
        MJTC_includer::MJTC_getModel('ticket')->removeInternalNote($MJTC_id, $MJTC_internalid, $MJTC_internalnoteid);
        if (is_admin()) {
            $MJTC_url = admin_url("admin.php?page=majesticsupport_ticket&mjslay=ticketdetail&majesticsupportid=" . esc_attr($MJTC_id));
        } else {
            $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'ticket', 'mjslay'=>'ticketdetail', 'majesticsupportid'=>$MJTC_id));
        }
        wp_safe_redirect($MJTC_url);
        exit;
    }

    static function changepriority() {
        $MJTC_id = MJTC_request::MJTC_getVar('ticketid');
        $MJTC_priorityid = MJTC_request::MJTC_getVar('priority');
        MJTC_includer::MJTC_getModel('ticket')->changeTicketPriority($MJTC_id, $MJTC_priorityid);
        if (is_admin()) {
            $MJTC_url = admin_url("admin.php?page=majesticsupport_ticket&mjslay=ticketdetail&majesticsupportid=" . esc_attr($MJTC_id));
        } else {
            $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'ticket', 'mjslay'=>'ticketdetail', 'majesticsupportid'=>$MJTC_id));
        }
        wp_safe_redirect($MJTC_url);
        exit;
    }

    static function reopenticket() { // for user
        $MJTC_ticketid = MJTC_request::MJTC_getVar('ticketid');
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'reopen-ticket-'.$MJTC_ticketid) ) {
            die( 'Security check Failed' );
        }
        $MJTC_internalid = MJTC_request::MJTC_getVar('internalid');
        $MJTC_data['ticketid'] = $MJTC_ticketid;
        $MJTC_data['internalid'] = $MJTC_internalid;
        MJTC_includer::MJTC_getModel('ticket')->reopenTicket($MJTC_data);
        $MJTC_url = "&mjslay=ticketdetail&majesticsupportid=" . esc_attr($MJTC_data['ticketid']);
        if (is_admin()) {
            $MJTC_url = admin_url("admin.php?page=majesticsupport_ticket" . esc_attr($MJTC_url));
        } else {
            $MJTC_redirect = MJTC_request::MJTC_getVar('redirect');
            if($MJTC_redirect == 2){
                $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'agent', 'mjslay'=>'staffmyticket','mspageid'=>majesticsupport::getPageid()));
            } else if($MJTC_redirect == 3){
                $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'ticket', 'mjslay'=>'myticket','mspageid'=>majesticsupport::getPageid()));
            } else {
                $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'ticket', 'mjslay'=>'ticketdetail', 'majesticsupportid'=>$MJTC_data['ticketid']));
            }
        }
        wp_safe_redirect($MJTC_url);
        exit;
    }

    static function multiactionticket() {
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'multiaction-ticket') ) {
            die( 'Security check Failed' );
        }
        // Only allow admins to do this
        if (!current_user_can('manage_options')) {
            die('You do not have permission to do this');
        }
        $MJTC_task = MJTC_request::MJTC_getVar('actionTask');
        $MJTC_raw_ids = MJTC_request::MJTC_getVar('selectedTicketIds');
        $MJTC_selected_array = !empty($MJTC_raw_ids) ? explode(',', $MJTC_raw_ids) : array();
        $MJTC_clean_ids = array_map('intval', $MJTC_selected_array);
        if (!empty($MJTC_clean_ids)) {
            /* to handle actions */
            switch ($MJTC_task) {
                case 'reopen': 
                    foreach ($MJTC_clean_ids as $MJTC_id) {
                        $MJTC_query = "SELECT *, id AS ticketid FROM `".majesticsupport::$_db->prefix."mjtc_support_tickets` WHERE (status = 5 OR status = 5) AND id=".$MJTC_id;
                        $MJTC_data = majesticsupport::$_db->get_row($MJTC_query);
                        if ($MJTC_data) {
                            $result = (array) $MJTC_data;
                            MJTC_includer::MJTC_getModel('ticket')->reopenTicket($result);
                        }
                    }
                    break;
                case 'close': 
                    foreach ($MJTC_clean_ids as $MJTC_id) {
                        MJTC_includer::MJTC_getModel('ticket')->closeTicket($MJTC_id);
                    }
                    break;
                case 'enforce-delete': 
                    foreach ($MJTC_clean_ids as $MJTC_id) {
                        // Delete the ticket securely
                        MJTC_includer::MJTC_getModel('ticket')->removeEnforceTicket($MJTC_id);
                    }
                    break;
                case 'delete': 
                    foreach ($MJTC_clean_ids as $MJTC_id) {
                        //to check internalid
                        $MJTC_query = "SELECT internalid FROM `".majesticsupport::$_db->prefix."mjtc_support_tickets` WHERE id=".$MJTC_id;
                        $MJTC_internalid = majesticsupport::$_db->get_var($MJTC_query);
                        if(!empty($MJTC_internalid)) {
                            MJTC_includer::MJTC_getModel('ticket')->removeTicket($MJTC_id, $MJTC_internalid);
                        }
                    }
                    break;
            }
        }

        if (is_admin()) {
            $MJTC_url = admin_url("admin.php?page=majesticsupport_ticket");
        }
        wp_safe_redirect($MJTC_url);
        exit;
    }

    static function actionticket() {
        $MJTC_data = MJTC_request::get('post');
        $MJTC_ticketid = MJTC_request::MJTC_getVar('ticketid');
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'action-ticket-') ) {
            die( 'Security check Failed' );
        }
        $MJTC_ticketlisting = MJTC_request::MJTC_getVar('ticketlisting');
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
            if (!empty($MJTC_ticketlisting)) {
                $MJTC_url = admin_url("admin.php?page=majesticsupport_ticket");
            } else {
                $MJTC_url = admin_url("admin.php?page=majesticsupport_ticket" . $MJTC_url);
            }
        } else {
            $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'ticket', 'mjslay'=>'ticketdetail', 'majesticsupportid'=>$MJTC_data['ticketid']));
        }
        wp_safe_redirect($MJTC_url);
        exit;
    }

    static function showticketstatus() {
        $MJTC_token = MJTC_request::MJTC_getVar('token');
        if ($MJTC_token == null) { // in case it come from ticket status form
            $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
            if (! wp_verify_nonce( $MJTC_nonce, 'show-ticket-status') ) {
                //die( 'Security check Failed' );
            }
            $MJTC_emailaddress = MJTC_request::MJTC_getVar('email');
            $trackingid = MJTC_request::MJTC_getVar('ticketid');
            $MJTC_tickettoken = MJTC_request::MJTC_getVar('tickettoken');
            if(!empty($MJTC_emailaddress) AND !empty($trackingid)){
                $MJTC_token = MJTC_includer::MJTC_getModel('ticket')->getTokenByEmailAndTrackingId($MJTC_emailaddress, $trackingid);
            }else if(!empty($MJTC_tickettoken)){
                $MJTC_token = $MJTC_tickettoken;
            }
            if($MJTC_token){
                include_once MJTC_PLUGIN_PATH . 'includes/encoder.php';
                $MJTC_encoder = new MJTC_encoder();
                $MJTC_token = $MJTC_encoder->MJTC_encrypt(wp_json_encode(array('token' => $MJTC_token, 'sitelink' => get_option('ms_encripted_site_link'))));
                MJTC_majesticsupportphplib::MJTC_setcookie('majestic-support-token-tkstatus',$MJTC_token ,0, COOKIEPATH);
                if ( SITECOOKIEPATH != COOKIEPATH ){
                    MJTC_majesticsupportphplib::MJTC_setcookie('majestic-support-token-tkstatus',$MJTC_token ,0, SITECOOKIEPATH);
                }
                $MJTC_ticketid = MJTC_includer::MJTC_getModel('ticket')->getTicketidForVisitorUsingToken($MJTC_token);
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
            MJTC_majesticsupportphplib::MJTC_setcookie('majestic-support-token-tkstatus',$MJTC_token ,0, COOKIEPATH);
            if ( SITECOOKIEPATH != COOKIEPATH ){
                MJTC_majesticsupportphplib::MJTC_setcookie('majestic-support-token-tkstatus',$MJTC_token ,0, SITECOOKIEPATH);
            }
            $MJTC_ticketid = MJTC_includer::MJTC_getModel('ticket')->getTicketidForVisitor($MJTC_token);
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
        $MJTC_id = MJTC_request::MJTC_getVar('id');
        MJTC_includer::MJTC_getModel('attachment')->getAllDownloads();
        if (is_admin()) {
            $MJTC_url = admin_url("admin.php?page=majesticsupport_ticket&mjslay=ticketdetail");
        } else {
            $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'ticket','mjslay'=>'ticketdetail','majesticsupportid'=>'$MJTC_id','mspageid'=>majesticsupport::getPageid()));
        }
        wp_safe_redirect($MJTC_url);
        exit;
    }
    static function downloadallforreply() {
        $MJTC_downloadid = MJTC_request::MJTC_getVar('downloadid');
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'download-all-for-reply-'.$MJTC_downloadid) ) {
            die( 'Security check Failed' );
        }
        MJTC_includer::MJTC_getModel('attachment')->getAllReplyDownloads();
        if (is_admin()) {
          $MJTC_url = admin_url("admin.php?page=majesticsupport_ticket&mjslay=ticketdetail");
          } else {
          $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'ticket','mjslay'=>'ticketdetail','majesticsupportid'=>'$MJTC_id','mspageid'=>majesticsupport::getPageid()));
          }
          wp_safe_redirect($MJTC_url);
          exit;
    }

    function downloadbyid(){
        $MJTC_id = MJTC_request::MJTC_getVar('id');
        MJTC_includer::MJTC_getModel('attachment')->getDownloadAttachmentById($MJTC_id);
    }


    function downloadbyname(){
        $MJTC_name = MJTC_request::MJTC_getVar('name');
        $MJTC_id = MJTC_request::MJTC_getVar('id');
        $MJTC_name = MJTC_majesticsupportphplib::MJTC_clean_file_path($MJTC_name);
        MJTC_includer::MJTC_getModel('attachment')->getDownloadAttachmentByName($MJTC_name,$MJTC_id);
    }

    function mergeticket() {
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'merge-ticket') ) {
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
