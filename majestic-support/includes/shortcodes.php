<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_shortcodes {

    function __construct() {
        add_shortcode('majesticsupport', array($this, 'MJTC_show_main_ticket'));
        add_shortcode('majesticsupport_addticket', array($this, 'MJTC_show_form_ticket'));
        if( in_array('multiform', majesticsupport::$_active_addons) ){
            add_shortcode('majesticsupport_addticket_multiform', array($this, 'MJTC_show_form_ticket_for_multiform'));
        }
        add_shortcode('majesticsupport_mytickets', array($this, 'MJTC_show_my_ticket'));
    }

    function MJTC_show_main_ticket($MJTC_raw_args, $MJTC_content = null) {
        //default set of parameters for the front end shortcodes
        ob_start();
        $MJTC_defaults = array(
            'mjsmod' => '',
            'mjslay' => '',
        );
        $MJTC_sanitized_args = shortcode_atts($MJTC_defaults, $MJTC_raw_args);
        if(isset(majesticsupport::$_data['sanitized_args']) && !empty(majesticsupport::$_data['sanitized_args'])){
            majesticsupport::$_data['sanitized_args'] += $MJTC_sanitized_args;
        }else{
            majesticsupport::$_data['sanitized_args'] = $MJTC_sanitized_args;
        }
        $MJTC_pageid = get_the_ID();
        majesticsupport::setPageID($MJTC_pageid);
        MJTC_includer::MJTC_include_slug('');
        $MJTC_content .= ob_get_clean();
        return $MJTC_content;
    }

    function MJTC_show_form_ticket($MJTC_raw_args, $MJTC_content = null) {
        //default set of parameters for the front end shortcodes
        ob_start();
        $MJTC_pageid = get_the_ID();
        majesticsupport::setPageID($MJTC_pageid);
        $MJTC_module = MJTC_request::MJTC_getVar('mjsmod', '', 'ticket');
        $MJTC_layout = MJTC_request::MJTC_getVar('mjslay', '', 'addticket');
        if ($MJTC_layout != 'addticket' && $MJTC_layout != 'staffaddticket') {
            $MJTC_module = MJTC_majesticsupportphplib::MJTC_str_replace('majesticsupport_', '', $MJTC_module);
            MJTC_includer::MJTC_include_file($MJTC_module);
        } else {
            $MJTC_defaults = array(
                'job_type' => '',
                'city' => '',
                'company' => '',
            );
            $MJTC_sanitized_args = shortcode_atts($MJTC_defaults, $MJTC_raw_args);
            if(isset(majesticsupport::$_data['sanitized_args']) && !empty(majesticsupport::$_data['sanitized_args'])){
                majesticsupport::$_data['sanitized_args'] += $MJTC_sanitized_args;
            }else{
                majesticsupport::$_data['sanitized_args'] = $MJTC_sanitized_args;
            }
            majesticsupport::$_data['short_code_header'] = 'addticket';
            if ( in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) {
                $MJTC_id = MJTC_request::MJTC_getVar('majesticsupportid');
                $MJTC_per_task = ($MJTC_id == null) ? 'Add Ticket' : 'Edit Ticket';
                majesticsupport::$_data['permission_granted'] = MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForTask($MJTC_per_task);
                if (majesticsupport::$_data['permission_granted']) {
                    MJTC_includer::MJTC_getModel('ticket')->getTicketsForForm($MJTC_id);
                }
                MJTC_includer::MJTC_include_file('staffaddticket', 'agent');
            } else {
                MJTC_includer::MJTC_getModel('ticket')->getTicketsForForm(null);
                MJTC_includer::MJTC_include_file('addticket', 'ticket');
            }
        }
        $MJTC_content .= ob_get_clean();
        return $MJTC_content;
    }

    function MJTC_show_form_ticket_for_multiform($MJTC_raw_args, $MJTC_content = null) {
        $MJTC_formid = $MJTC_raw_args['formid'];
        //default set of parameters for the front end shortcodes
        ob_start();
        $MJTC_pageid = get_the_ID();
        majesticsupport::setPageID($MJTC_pageid);
        $MJTC_module = MJTC_request::MJTC_getVar('mjsmod', '', 'ticket');
        $MJTC_layout = MJTC_request::MJTC_getVar('mjslay', '', 'addticket');
        if ($MJTC_layout != 'addticket' && $MJTC_layout != 'staffaddticket') {
            $MJTC_module = MJTC_majesticsupportphplib::MJTC_str_replace('majesticsupport_', '', $MJTC_module);
            MJTC_includer::MJTC_include_file($MJTC_module);
        } else {
            $MJTC_defaults = array(
                'job_type' => '',
                'city' => '',
                'company' => '',
            );
            $MJTC_sanitized_args = shortcode_atts($MJTC_defaults, $MJTC_raw_args);
            if(isset(majesticsupport::$_data['sanitized_args']) && !empty(majesticsupport::$_data['sanitized_args'])){
                majesticsupport::$_data['sanitized_args'] += $MJTC_sanitized_args;
            }else{
                majesticsupport::$_data['sanitized_args'] = $MJTC_sanitized_args;
            }
            majesticsupport::$_data['short_code_header'] = 'addticket';
            if ( in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) {
                $MJTC_id = MJTC_request::MJTC_getVar('majesticsupportid');
                $MJTC_per_task = ($MJTC_id == null) ? 'Add Ticket' : 'Edit Ticket';
                majesticsupport::$_data['permission_granted'] = MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForTask($MJTC_per_task);
                if (majesticsupport::$_data['permission_granted']) {
                    MJTC_includer::MJTC_getModel('ticket')->getTicketsForForm($MJTC_id, $MJTC_formid);
                }
                MJTC_includer::MJTC_include_file('staffaddticket', 'agent');
            } else {
                MJTC_includer::MJTC_getModel('ticket')->getTicketsForForm(null, $MJTC_formid);
                MJTC_includer::MJTC_include_file('addticket', 'ticket');
            }
        }
        $MJTC_content .= ob_get_clean();
        return $MJTC_content;
    }

    function MJTC_show_my_ticket($MJTC_raw_args, $MJTC_content = null) {
        //default set of parameters for the front end shortcodes
        ob_start();
        $MJTC_pageid = get_the_ID();
        majesticsupport::setPageID($MJTC_pageid);
        $MJTC_module = MJTC_request::MJTC_getVar('mjsmod', '', 'ticket');
        $MJTC_layout = MJTC_request::MJTC_getVar('mjslay', '', 'myticket');
        if ($MJTC_layout != 'myticket' && $MJTC_layout != 'staffmyticket') {
            $MJTC_module = MJTC_majesticsupportphplib::MJTC_str_replace('majesticsupport_', '', $MJTC_module);
            MJTC_includer::MJTC_include_file($MJTC_module);
        } else {
            $MJTC_defaults = array(
                'list' => '',
                'ticketid' => '',
            );
            $MJTC_list = MJTC_request::MJTC_getVar('list', 'get', null);
            $MJTC_ticketid = MJTC_request::MJTC_getVar('ticketid', null, null);
            $MJTC_args = shortcode_atts($MJTC_defaults, $MJTC_raw_args);
            if(isset(majesticsupport::$_data['sanitized_args']) && !empty(majesticsupport::$_data['sanitized_args'])){
                majesticsupport::$_data['sanitized_args'] += $MJTC_args;
            }else{
                majesticsupport::$_data['sanitized_args'] = $MJTC_args;
            }
            if ($MJTC_list == null)
                $MJTC_list = $MJTC_args['list'];
            if ($MJTC_ticketid == null)
                $MJTC_ticketid = $MJTC_args['ticketid'];
            majesticsupport::$_data['short_code_header'] = 'myticket';
            if ( in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) {
                MJTC_includer::MJTC_getModel('ticket')->getStaffTickets();
                MJTC_includer::MJTC_include_file('staffmyticket', 'agent');
            } else {
                MJTC_includer::MJTC_getModel('ticket')->getMyTickets($MJTC_list, $MJTC_ticketid);
                MJTC_includer::MJTC_include_file('myticket', 'ticket');
            }
        }
        $MJTC_content .= ob_get_clean();
        return $MJTC_content;
    }

}

$MJTC_shortcodes = new MJTC_shortcodes();
?>
