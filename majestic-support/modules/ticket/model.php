<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_ticketModel {

    private $MJTC_ticketid;

    function getTicketsForAdmin($MJTC_lst=null) {
        $this->getOrdering();
        // Filter
        $MJTC_search_userfields = MJTC_includer::MJTC_getObjectClass('customfields')->adminFieldsForSearch(1);
        $MJTC_subject = majesticsupport::$_search['ticket']['subject'];
        $MJTC_name = majesticsupport::$_search['ticket']['name'];
        $MJTC_phone = majesticsupport::$_search['ticket']['phone'];
        $MJTC_email = majesticsupport::$_search['ticket']['email'];
        $MJTC_ticketid = majesticsupport::$_search['ticket']['ticketid'];
        $MJTC_datestart = majesticsupport::$_search['ticket']['datestart'];
        $MJTC_dateend = majesticsupport::$_search['ticket']['dateend'];
        $MJTC_orderid = majesticsupport::$_search['ticket']['orderid'];
        $MJTC_eddorderid = majesticsupport::$_search['ticket']['eddorderid'];
        $MJTC_priority = majesticsupport::$_search['ticket']['priority'];
        $MJTC_departmentid = majesticsupport::$_search['ticket']['departmentid'];
        $MJTC_helptopicid = majesticsupport::$_search['ticket']['helptopicid'];
        $MJTC_productid = majesticsupport::$_search['ticket']['productid'];
        $MJTC_staffid = majesticsupport::$_search['ticket']['staffid'];
        $MJTC_status = majesticsupport::$_search['ticket']['status'];
        $MJTC_sortby = majesticsupport::$_search['ticket']['sortby'];
        if (!empty($MJTC_search_userfields)) {
            foreach ($MJTC_search_userfields as $MJTC_uf) {
                $MJTC_value_array[$MJTC_uf->field] = majesticsupport::$_search['ms_ticket_custom_field'][$MJTC_uf->field];
            }
        }
        $MJTC_inquery = '';
        if($MJTC_lst != null){
            majesticsupport::$_search['ticket']['list'] = $MJTC_lst;
        }
        $MJTC_list = majesticsupport::$_search['ticket']['list'];
        switch ($MJTC_list) {
            // Ticket Default Status
            // 0 -> New Ticket
            // 1 -> Waiting admin/staff reply
            // 2 -> in progress
            // 3 -> waiting for customer reply
            // 4 -> close ticket
            case 1:$MJTC_inquery .= " AND ticket.status != 5 AND ticket.status != 6";
                break;
            case 2:$MJTC_inquery .= " AND ticket.isanswered = 1 AND ticket.status != 5 AND ticket.status != 6 AND ticket.status != 1";
                break;
            case 3:$MJTC_inquery .= " AND ticket.isoverdue = 1 AND ticket.status != 5 AND ticket.status != 6 ";
                break;
            case 4:$MJTC_inquery .= " AND (ticket.status = 5 OR ticket.status = 6) ";
                break;
            case 5://$MJTC_inquery .= " AND ticket.uid =" . MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid();
                break;
        }

        if ($MJTC_datestart != null)
            $MJTC_inquery .= " AND '".esc_sql($MJTC_datestart)."' <= DATE(ticket.created)";
        if ($MJTC_dateend != null)
            $MJTC_inquery .= " AND '".esc_sql($MJTC_dateend)."' >= DATE(ticket.created)";
        if ($MJTC_ticketid != null)
            $MJTC_inquery .= " AND ticket.ticketid LIKE '%".esc_sql($MJTC_ticketid)."%'";
        if ($MJTC_subject != null)
            $MJTC_inquery .= " AND ticket.subject LIKE '%".esc_sql($MJTC_subject)."%'";
        if ($MJTC_name != null)
            $MJTC_inquery .= " AND ticket.name LIKE '%".esc_sql($MJTC_name)."%'";
        if ($MJTC_phone != null)
            $MJTC_inquery .= " AND ticket.phone LIKE '%".esc_sql($MJTC_phone)."%'";
        if ($MJTC_email != null)
            $MJTC_inquery .= " AND ticket.email LIKE '%".esc_sql($MJTC_email)."%'";
        if ($MJTC_priority != null && is_numeric($MJTC_priority))
            $MJTC_inquery .= " AND ticket.priorityid = ".esc_sql($MJTC_priority);
        if ($MJTC_departmentid != null && is_numeric($MJTC_departmentid))
            $MJTC_inquery .= " AND ticket.departmentid = ".esc_sql($MJTC_departmentid);
        if ($MJTC_helptopicid != null && is_numeric($MJTC_helptopicid))
            $MJTC_inquery .= " AND ticket.helptopicid = ".esc_sql($MJTC_helptopicid);
        if ($MJTC_productid != null && is_numeric($MJTC_productid))
            $MJTC_inquery .= " AND ticket.productid = ".esc_sql($MJTC_productid);
        if ($MJTC_staffid != null && is_numeric($MJTC_staffid))
            $MJTC_inquery .= " AND ticket.staffid = ".esc_sql($MJTC_staffid);

        if ($MJTC_orderid != null && is_numeric($MJTC_orderid))
            $MJTC_inquery .= " AND ticket.wcorderid = ".esc_sql($MJTC_orderid);

        if ($MJTC_eddorderid != null && is_numeric($MJTC_eddorderid))
            $MJTC_inquery .= " AND ticket.eddorderid = ".esc_sql($MJTC_eddorderid);

        if ($MJTC_status != null && is_numeric($MJTC_status))
            $MJTC_inquery .= " AND ticket.status = ".esc_sql($MJTC_status);

        $MJTC_valarray = array();
        if (!empty($MJTC_search_userfields)) {
            foreach ($MJTC_search_userfields as $MJTC_uf) {
                if (MJTC_request::MJTC_getVar('pagenum', 'get', null) != null) {
                    $MJTC_valarray[$MJTC_uf->field] = $MJTC_value_array[$MJTC_uf->field];
                }else{
                    $MJTC_valarray[$MJTC_uf->field] = MJTC_request::MJTC_getVar($MJTC_uf->field, 'post');
                }
                if (isset($MJTC_valarray[$MJTC_uf->field]) && $MJTC_valarray[$MJTC_uf->field] != null) {
                    switch ($MJTC_uf->userfieldtype) {
                        case 'text':
                            $MJTC_inquery .= ' AND ticket.params REGEXP \'"' . esc_sql($MJTC_uf->field) . '":"[^"]*' . MJTC_majesticsupportphplib::MJTC_htmlspecialchars(esc_sql($MJTC_valarray[$MJTC_uf->field])) . '.*"\' ';
                            break;
                        case 'email':
                            $MJTC_inquery .= ' AND ticket.params REGEXP \'"' . esc_sql($MJTC_uf->field) . '":"[^"]*' . MJTC_majesticsupportphplib::MJTC_htmlspecialchars(esc_sql($MJTC_valarray[$MJTC_uf->field])) . '.*"\' ';
                            break;
                        case 'file':
                            $MJTC_inquery .= ' AND ticket.params REGEXP \'"' . esc_sql($MJTC_uf->field) . '":"[^"]*' . MJTC_majesticsupportphplib::MJTC_htmlspecialchars(esc_sql($MJTC_valarray[$MJTC_uf->field])) . '.*"\' ';
                            break;
                        case 'combo':
                            $MJTC_inquery .= ' AND ticket.params LIKE \'%"' . esc_sql($MJTC_uf->field) . '":"' . MJTC_majesticsupportphplib::MJTC_htmlspecialchars(esc_sql($MJTC_valarray[$MJTC_uf->field])) . '"%\' ';
                            break;
                        case 'depandant_field':
                            $MJTC_inquery .= ' AND ticket.params LIKE \'%"' . esc_sql($MJTC_uf->field) . '":"' . MJTC_majesticsupportphplib::MJTC_htmlspecialchars(esc_sql($MJTC_valarray[$MJTC_uf->field])) . '"%\' ';
                            break;
                        case 'radio':
                            $MJTC_inquery .= ' AND ticket.params LIKE \'%"' . esc_sql($MJTC_uf->field) . '":"' . MJTC_majesticsupportphplib::MJTC_htmlspecialchars(esc_sql($MJTC_valarray[$MJTC_uf->field])) . '"%\' ';
                            break;
                        case 'checkbox':
                            $MJTC_finalvalue = '';
                            foreach($MJTC_valarray[$MJTC_uf->field] AS $MJTC_value){
                                $MJTC_finalvalue .= esc_sql($MJTC_value).'.*';
                            }
                            $MJTC_inquery .= ' AND ticket.params REGEXP \'"' . esc_sql($MJTC_uf->field) . '":"[^"]*' . MJTC_majesticsupportphplib::MJTC_htmlspecialchars(esc_sql($MJTC_finalvalue)) . '.*"\' ';
                            break;
                        case 'date':
                            $MJTC_inquery .= ' AND ticket.params LIKE \'%"' . esc_sql($MJTC_uf->field) . '":"' . MJTC_majesticsupportphplib::MJTC_htmlspecialchars(esc_sql($MJTC_valarray[$MJTC_uf->field])) . '"%\' ';
                            break;
                        case 'textarea':
                            $MJTC_inquery .= ' AND ticket.params REGEXP \'"' . esc_sql($MJTC_uf->field) . '":"[^"]*' . MJTC_majesticsupportphplib::MJTC_htmlspecialchars(esc_sql($MJTC_valarray[$MJTC_uf->field])) . '.*"\' ';
                            break;
                        case 'multiple':
                            $MJTC_finalvalue = '';
                            foreach($MJTC_valarray[$MJTC_uf->field] AS $MJTC_value){
                                if($MJTC_value != null){
                                    $MJTC_finalvalue .= esc_sql($MJTC_value).'.*';
                                }
                            }
                            if($MJTC_finalvalue !=''){
                                $MJTC_inquery .= ' AND ticket.params REGEXP \'"' . esc_sql($MJTC_uf->field) . '":"[^"]*'.MJTC_majesticsupportphplib::MJTC_htmlspecialchars(esc_sql($MJTC_finalvalue)).'.*"\'';
                            }
                            break;
                    }
                    majesticsupport::$_data['filter']['params'] = $MJTC_valarray;
                }
            }
        }
        //end

        majesticsupport::$_data['filter']['subject'] = $MJTC_subject;
        majesticsupport::$_data['filter']['ticketid'] = $MJTC_ticketid;
        majesticsupport::$_data['filter']['name'] = $MJTC_name;
        majesticsupport::$_data['filter']['phone'] = $MJTC_phone;
        majesticsupport::$_data['filter']['email'] = $MJTC_email;
        majesticsupport::$_data['filter']['datestart'] = $MJTC_datestart;
        majesticsupport::$_data['filter']['dateend'] = $MJTC_dateend;
        majesticsupport::$_data['filter']['priority'] = $MJTC_priority;
        majesticsupport::$_data['filter']['departmentid'] = $MJTC_departmentid;
        majesticsupport::$_data['filter']['helptopicid'] = $MJTC_helptopicid;
        majesticsupport::$_data['filter']['productid'] = $MJTC_productid;
        majesticsupport::$_data['filter']['staffid'] = $MJTC_staffid;
        majesticsupport::$_data['filter']['sortby'] = $MJTC_sortby;
        majesticsupport::$_data['filter']['orderid'] = $MJTC_orderid;
        majesticsupport::$_data['filter']['eddorderid'] = $MJTC_eddorderid;
        majesticsupport::$_data['filter']['status'] = $MJTC_status;

        $MJTC_userquery = '';
        $MJTC_uid = MJTC_request::MJTC_getVar('uid');
        if ($MJTC_uid != '') {
            $MJTC_uid = MJTC_majesticsupportphplib::MJTC_trim($MJTC_uid);
        }
        if($MJTC_uid != null && is_numeric($MJTC_uid)){
            $MJTC_userquery = ' AND ticket.uid = '.esc_sql($MJTC_uid);
        }

        // Pagination
        $MJTC_query = "SELECT COUNT(ticket.id) "
                . "FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket "
                . "LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_departments` AS department ON ticket.departmentid = department.id "
                . "LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` AS priority ON ticket.priorityid = priority.id "
                . "WHERE 1 = 1";
        $MJTC_query .= $MJTC_inquery.$MJTC_userquery;
        $total = majesticsupport::$_db->get_var($MJTC_query);
        majesticsupport::$_data[1] = MJTC_pagination::MJTC_getPagination($total);

        /*
          list variable detail
          1=>For open ticket
          2=>For answered  ticket
          3=>For overdue ticket
          4=>For Closed tickets
          5=>For mytickets tickets
         */
        majesticsupport::$_data['list'] = $MJTC_list; // assign for reference
        // Data
        do_action('MJTC_addon_staff_admin_tickets');
        $MJTC_query = "SELECT ticket.*,department.departmentname AS departmentname ,priority.priority AS priority,priority.prioritycolour AS prioritycolour,status.status AS statustitle,status.statuscolour,status.statusbgcolour, product.product AS producttitle, COUNT(replies.id) AS reply_count ".majesticsupport::$_addon_query['select']."
                    FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket
                    LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_departments` AS department ON ticket.departmentid = department.id
                    LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` AS priority ON ticket.priorityid = priority.id
                    JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_statuses` AS status ON ticket.status = status.id
                    LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_products` AS product ON ticket.productid = product.id
                    LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_replies` AS replies ON ticket.id = replies.ticketid

                    ".majesticsupport::$_addon_query['join']."
                    WHERE 1 = 1";

        $MJTC_query .= $MJTC_inquery.$MJTC_userquery;
        $MJTC_query .= " GROUP BY ticket.id";
        $MJTC_query .= " ORDER BY " . majesticsupport::$_ordering . " LIMIT " . MJTC_pagination::MJTC_getOffset() . ", " . MJTC_pagination::MJTC_getLimit();
        majesticsupport::$_data[0] = majesticsupport::$_db->get_results($MJTC_query);
        do_action('MJTC_reset_addon_query');
        // check email is bane
        if(in_array('banemail', majesticsupport::$_active_addons)){
            if (isset(majesticsupport::$_data[0]->email))
                $MJTC_query = "SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_email_banlist` WHERE email = ' " . esc_sql(majesticsupport::$_data[0]->email) . "'";
            majesticsupport::$_data[7] = majesticsupport::$_db->get_var($MJTC_query);
        }else{
            majesticsupport::$_data[7] = 0;
        }
        //Hook action
        do_action('MJTC_ticketbeforelisting', majesticsupport::$_data[0]);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        // if(majesticsupport::$_config['count_on_myticket'] == 1){
            $MJTC_query = "SELECT COUNT(ticket.id) "
                    . "FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket "
                    . "LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_departments` AS department ON ticket.departmentid = department.id "
                    . "LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` AS priority ON ticket.priorityid = priority.id "
                    . "WHERE (ticket.status != 5 AND ticket.status != 6)".$MJTC_userquery;
            majesticsupport::$_data['count']['openticket'] = majesticsupport::$_db->get_var($MJTC_query);;

            $MJTC_query = "SELECT COUNT(ticket.id) "
                    . "FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket "
                    . "LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_departments` AS department ON ticket.departmentid = department.id "
                    . "LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` AS priority ON ticket.priorityid = priority.id "
                    . "WHERE ticket.isanswered = 1 AND ticket.status != 5 AND ticket.status != 6 AND ticket.status != 1 ".$MJTC_userquery;
            majesticsupport::$_data['count']['answeredticket'] = majesticsupport::$_db->get_var($MJTC_query);;

            $MJTC_query = "SELECT COUNT(ticket.id) "
                    . "FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket "
                    . "LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_departments` AS department ON ticket.departmentid = department.id "
                    . "LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` AS priority ON ticket.priorityid = priority.id "
                    . "WHERE ticket.isoverdue = 1 AND ticket.status != 5 AND ticket.status != 6 ".$MJTC_userquery;
            majesticsupport::$_data['count']['overdueticket'] = majesticsupport::$_db->get_var($MJTC_query);;

            $MJTC_query = "SELECT COUNT(ticket.id) "
                    . "FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket "
                    . "LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_departments` AS department ON ticket.departmentid = department.id "
                    . "LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` AS priority ON ticket.priorityid = priority.id "
                    . "WHERE (ticket.status = 5 OR ticket.status = 6)".$MJTC_userquery;
            majesticsupport::$_data['count']['closedticket'] = majesticsupport::$_db->get_var($MJTC_query);;

            $MJTC_query = "SELECT COUNT(ticket.id) "
                    . "FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket "
                    . "LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_departments` AS department ON ticket.departmentid = department.id "
                    . "LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` AS priority ON ticket.priorityid = priority.id "
                    . "WHERE 1 = 1".$MJTC_userquery;
            majesticsupport::$_data['count']['allticket'] = majesticsupport::$_db->get_var($MJTC_query);
        // }
        return;
    }

    function getOrdering() {
        $MJTC_sort = isset(majesticsupport::$_search['ticket']) ? majesticsupport::$_search['ticket']['sortby'] : '';
        if ($MJTC_sort == '') {
            $MJTC_list = majesticsupport::$_config['tickets_ordering'];
            // default sort by
            $MJTC_sortbyconfig = majesticsupport::$_config['tickets_sorting'];
            if($MJTC_sortbyconfig == 1){
                $MJTC_sortbyconfig = "asc";
            }else{
                $MJTC_sortbyconfig = "desc";
            }
            $MJTC_sort = 'status';
            if($MJTC_list == 2)
                $MJTC_sort = 'created';
            $MJTC_sort = $MJTC_sort.$MJTC_sortbyconfig;
        }
        $this->getTicketListOrdering($MJTC_sort);
        $this->getTicketListSorting($MJTC_sort);
    }

    function combineOrSingleSearch() {
        $MJTC_ticketkeys = isset(majesticsupport::$_search['ticket']) ? majesticsupport::$_search['ticket']['ticketkeys'] : false;
        $MJTC_inquery = '';
        if ($MJTC_ticketkeys) {
            if (MJTC_majesticsupportphplib::MJTC_strpos($MJTC_ticketkeys, '@') && MJTC_majesticsupportphplib::MJTC_strpos($MJTC_ticketkeys, '.')){
                $MJTC_inquery = " AND ticket.email LIKE '%".esc_sql($MJTC_ticketkeys)."%'";
            }else{
                $MJTC_inquery = " AND (ticket.ticketid = '".esc_sql($MJTC_ticketkeys)."' OR ticket.subject LIKE '%".esc_sql($MJTC_ticketkeys)."%')";
            }
            majesticsupport::$_data['filter']['ticketsearchkeys'] = $MJTC_ticketkeys;
        }else {
            $MJTC_search_userfields = MJTC_includer::MJTC_getObjectClass('customfields')->userFieldsForSearch(1);
            $MJTC_ticketid = MJTC_request::MJTC_getVar('ms-ticket', 'post');

            $MJTC_from = isset(majesticsupport::$_search['ticket']) ? majesticsupport::$_search['ticket']['name'] : '';
            $MJTC_phone = isset(majesticsupport::$_search['ticket']) ? majesticsupport::$_search['ticket']['phone'] : '';
            $MJTC_email = isset(majesticsupport::$_search['ticket']) ? majesticsupport::$_search['ticket']['email'] : '';
            $MJTC_departmentid = isset(majesticsupport::$_search['ticket']) ? majesticsupport::$_search['ticket']['departmentid'] : '';
            $MJTC_helptopicid = isset(majesticsupport::$_search['ticket']) ? majesticsupport::$_search['ticket']['helptopicid'] : '';
            $MJTC_productid = isset(majesticsupport::$_search['ticket']) ? majesticsupport::$_search['ticket']['productid'] : '';
            $MJTC_priorityid = isset(majesticsupport::$_search['ticket']) ? majesticsupport::$_search['ticket']['priority'] : '';
            $MJTC_subject = isset(majesticsupport::$_search['ticket']) ? majesticsupport::$_search['ticket']['subject'] : '';
            $MJTC_datestart = isset(majesticsupport::$_search['ticket']) ? majesticsupport::$_search['ticket']['datestart'] : '';
            $MJTC_dateend = isset(majesticsupport::$_search['ticket']) ? majesticsupport::$_search['ticket']['dateend'] : '';
            $MJTC_orderid = isset(majesticsupport::$_search['ticket']) ? majesticsupport::$_search['ticket']['orderid'] : '';
            $MJTC_eddorderid = isset(majesticsupport::$_search['ticket']) ? majesticsupport::$_search['ticket']['eddorderid'] : '';
            $MJTC_staffid = isset(majesticsupport::$_search['ticket']) ? majesticsupport::$_search['ticket']['staffid'] : '';
            $MJTC_status = isset(majesticsupport::$_search['ticket']) ? majesticsupport::$_search['ticket']['status'] : '';
            $MJTC_sortby = isset(majesticsupport::$_search['ticket']) ? majesticsupport::$_search['ticket']['sortby'] : '';
            $MJTC_assignedtome = isset(majesticsupport::$_search['ticket']) ? majesticsupport::$_search['ticket']['assignedtome'] : '';

            if (!empty($MJTC_search_userfields)) {
                foreach ($MJTC_search_userfields as $MJTC_uf) {
                    $MJTC_value_array[$MJTC_uf->field] = isset(majesticsupport::$_search['ms_ticket_custom_field']) ? majesticsupport::$_search['ms_ticket_custom_field'][$MJTC_uf->field] : '';
                }
            }

            if ($MJTC_ticketid != null) {
                $MJTC_inquery .= " AND ticket.ticketid LIKE '".esc_sql($MJTC_ticketid)."'";
                majesticsupport::$_data['filter']['ticketid'] = $MJTC_ticketid;
            }
            if ($MJTC_from != null) {
                $MJTC_inquery .= " AND ticket.name LIKE '%".esc_sql($MJTC_from)."%'";
                majesticsupport::$_data['filter']['from'] = $MJTC_from;
            }
            if ($MJTC_phone != null) {
                $MJTC_inquery .= " AND ticket.phone LIKE '%".esc_sql($MJTC_phone)."%'";
                majesticsupport::$_data['filter']['phone'] = $MJTC_phone;
            }
            if ($MJTC_email != null) {
                $MJTC_inquery .= " AND ticket.email LIKE '".esc_sql($MJTC_email)."'";
                majesticsupport::$_data['filter']['email'] = $MJTC_email;
            }
            if ($MJTC_departmentid != null && is_numeric($MJTC_departmentid)) {
                $MJTC_inquery .= " AND ticket.departmentid = '".esc_sql($MJTC_departmentid)."'";
                majesticsupport::$_data['filter']['departmentid'] = $MJTC_departmentid;
            }
            if ($MJTC_helptopicid != null && is_numeric($MJTC_helptopicid)) {
                $MJTC_inquery .= " AND ticket.helptopicid = '".esc_sql($MJTC_helptopicid)."'";
                majesticsupport::$_data['filter']['helptopicid'] = $MJTC_helptopicid;
            }
            if ($MJTC_productid != null && is_numeric($MJTC_productid)) {
                $MJTC_inquery .= " AND ticket.productid = '".esc_sql($MJTC_productid)."'";
                majesticsupport::$_data['filter']['productid'] = $MJTC_productid;
            }
            if ($MJTC_priorityid != null && is_numeric($MJTC_priorityid)) {
                $MJTC_inquery .= " AND ticket.priorityid = '".esc_sql($MJTC_priorityid)."'";
                majesticsupport::$_data['filter']['priorityid'] = $MJTC_priorityid;
            }
            if(in_array('agent', majesticsupport::$_active_addons)){
                if ($MJTC_staffid != null && is_numeric($MJTC_staffid)) {
                    $MJTC_inquery .= " AND ticket.staffid = '".esc_sql($MJTC_staffid)."'";
                    majesticsupport::$_data['filter']['staffid'] = $MJTC_staffid;
                }
            }

            if ($MJTC_subject != null) {
                $MJTC_inquery .= " AND ticket.subject LIKE '%".esc_sql($MJTC_subject)."%'";
                majesticsupport::$_data['filter']['subject'] = $MJTC_subject;
            }
            if ($MJTC_datestart != null) {
                $MJTC_inquery .= " AND '".esc_sql($MJTC_datestart)."' <= DATE(ticket.created)";
                majesticsupport::$_data['filter']['datestart'] = $MJTC_datestart;
            }
            if ($MJTC_dateend != null) {
                $MJTC_inquery .= " AND '".esc_sql($MJTC_dateend)."' >= DATE(ticket.created)";
                majesticsupport::$_data['filter']['dateend'] = $MJTC_dateend;
            }

            if ($MJTC_orderid != null && is_numeric($MJTC_orderid)) {
                $MJTC_inquery .= " AND ticket.wcorderid = ".esc_sql($MJTC_orderid);
                majesticsupport::$_data['filter']['orderid'] = $MJTC_orderid;
            }

            if ($MJTC_eddorderid != null && is_numeric($MJTC_eddorderid)) {
                $MJTC_inquery .= " AND ticket.eddorderid = ".esc_sql($MJTC_eddorderid);
                majesticsupport::$_data['filter']['eddorderid'] = $MJTC_eddorderid;
            }

            if ($MJTC_assignedtome != null) {
                if(in_array('agent',majesticsupport::$_active_addons)){
                    $MJTC_uid = MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid();
                    $MJTC_stfid = MJTC_includer::MJTC_getModel('agent')->getStaffId($MJTC_uid);
                    if(is_numeric($MJTC_stfid)){
                        $MJTC_inquery .= " AND ticket.staffid = '".esc_sql($MJTC_stfid)."'";
                        majesticsupport::$_data['filter']['assignedtome'] = $MJTC_assignedtome;
                    }
                }
            }
            if ($MJTC_status != null && is_numeric($MJTC_status)) {
                $MJTC_inquery .= " AND ticket.status = ".esc_sql($MJTC_status);
                majesticsupport::$_data['filter']['status'] = $MJTC_status;
            }
            //Custom field search


            //start
            $MJTC_data = MJTC_includer::MJTC_getObjectClass('customfields')->userFieldsForSearch(1);
            $MJTC_valarray = array();
            if (!empty($MJTC_data)) {
                foreach ($MJTC_data as $MJTC_uf) {
                    if (MJTC_request::MJTC_getVar('pagenum', 'get', null) != null) {
                        $MJTC_valarray[$MJTC_uf->field] = $MJTC_value_array[$MJTC_uf->field];
                    }else{
                        $MJTC_valarray[$MJTC_uf->field] = MJTC_request::MJTC_getVar($MJTC_uf->field, 'post');
                    }
                    if (isset($MJTC_valarray[$MJTC_uf->field]) && $MJTC_valarray[$MJTC_uf->field] != null) {
                        switch ($MJTC_uf->userfieldtype) {
                            case 'text':
                                $MJTC_inquery .= ' AND ticket.params REGEXP \'"' . esc_sql($MJTC_uf->field) . '":"[^"]*' . MJTC_majesticsupportphplib::MJTC_htmlspecialchars(esc_sql($MJTC_valarray[$MJTC_uf->field])) . '.*"\' ';
                                break;
                            case 'email':
                                $MJTC_inquery .= ' AND ticket.params REGEXP \'"' . esc_sql($MJTC_uf->field) . '":"[^"]*' . MJTC_majesticsupportphplib::MJTC_htmlspecialchars(esc_sql($MJTC_valarray[$MJTC_uf->field])) . '.*"\' ';
                                break;
                            case 'combo':
                                $MJTC_inquery .= ' AND ticket.params LIKE \'%"' . esc_sql($MJTC_uf->field) . '":"' . MJTC_majesticsupportphplib::MJTC_htmlspecialchars(esc_sql($MJTC_valarray[$MJTC_uf->field])) . '"%\' ';
                                break;
                            case 'depandant_field':
                                $MJTC_inquery .= ' AND ticket.params LIKE \'%"' . esc_sql($MJTC_uf->field) . '":"' . MJTC_majesticsupportphplib::MJTC_htmlspecialchars(esc_sql($MJTC_valarray[$MJTC_uf->field])) . '"%\' ';
                                break;
                            case 'radio':
                                $MJTC_inquery .= ' AND ticket.params LIKE \'%"' . esc_sql($MJTC_uf->field) . '":"' . MJTC_majesticsupportphplib::MJTC_htmlspecialchars(esc_sql($MJTC_valarray[$MJTC_uf->field])) . '"%\' ';
                                break;
                            case 'checkbox':
                                $MJTC_finalvalue = '';
                                foreach($MJTC_valarray[$MJTC_uf->field] AS $MJTC_value){
                                    $MJTC_finalvalue .= esc_sql($MJTC_value).'.*';
                                }
                                $MJTC_inquery .= ' AND ticket.params REGEXP \'"' . esc_sql($MJTC_uf->field) . '":"[^"]*' . MJTC_majesticsupportphplib::MJTC_htmlspecialchars(esc_sql($MJTC_finalvalue)) . '.*"\' ';
                                break;
                            case 'date':
                                $MJTC_inquery .= ' AND ticket.params LIKE \'%"' . esc_sql($MJTC_uf->field) . '":"' . MJTC_majesticsupportphplib::MJTC_htmlspecialchars(esc_sql($MJTC_valarray[$MJTC_uf->field])) . '"%\' ';
                                break;
                            case 'textarea':
                                $MJTC_inquery .= ' AND ticket.params REGEXP \'"' . esc_sql($MJTC_uf->field) . '":"[^"]*' . MJTC_majesticsupportphplib::MJTC_htmlspecialchars(esc_sql($MJTC_valarray[$MJTC_uf->field])) . '.*"\' ';
                                break;
                            case 'multiple':
                                $MJTC_finalvalue = '';
                                foreach($MJTC_valarray[$MJTC_uf->field] AS $MJTC_value){
                                    if($MJTC_value != null){
                                        $MJTC_finalvalue .= esc_sql($MJTC_value).'.*';
                                    }
                                }
                                if($MJTC_finalvalue !=''){
                                    $MJTC_inquery .= ' AND ticket.params REGEXP \'"' . esc_sql($MJTC_uf->field) . '":"[^"]*'.MJTC_majesticsupportphplib::MJTC_htmlspecialchars(esc_sql($MJTC_finalvalue)).'.*"\'';
                                }
                                break;
                        }
                        majesticsupport::$_data['filter']['params'] = $MJTC_valarray;
                    }
                }
            }
            //end

            if ($MJTC_inquery == '')
                majesticsupport::$_data['filter']['combinesearch'] = false;
            else
                majesticsupport::$_data['filter']['combinesearch'] = true;
        }
        return $MJTC_inquery;
    }

    function getMyTickets($MJTC_lst=null) {
        $this->getOrdering();
        // Filter
        /*
          list variable detail
          1=>For open ticket
          2=>For closed ticket
          3=>For open answered ticket
          4=>For all my tickets
         */
        $MJTC_inquery = $this->combineOrSingleSearch();
        if($MJTC_lst != null){
            majesticsupport::$_search['ticket']['list'] = $MJTC_lst;
        }
        $MJTC_list = isset(majesticsupport::$_search['ticket']) ? majesticsupport::$_search['ticket']['list'] : 1;
        majesticsupport::$_data['list'] = $MJTC_list; // assign for reference
        switch ($MJTC_list) {
            // Ticket Default Status
            // 0 -> New Ticket
            // 1 -> Waiting admin/staff reply
            // 2 -> in progress
            // 3 -> waiting for customer reply
            // 4 -> close ticket
           case 1:$MJTC_inquery .= " AND (ticket.status != 5 AND ticket.status != 6)";
                break;
            case 2:$MJTC_inquery .= " AND (ticket.status = 5 OR ticket.status = 6) ";
                break;
            case 3:$MJTC_inquery .= " AND ticket.status = 4 ";
                break;
            case 4:$MJTC_inquery .= " ";
                break;
            case 5:$MJTC_inquery .= " AND ticket.isoverdue = 1 AND ticket.status != 5 AND ticket.status != 6 ";
                break;
        }

        $MJTC_uid = MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid();
        if ($MJTC_uid && is_numeric($MJTC_uid)) {
            // Pagination
            $MJTC_query = "SELECT COUNT(ticket.id)
                        FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket
                        LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_departments` AS department ON ticket.departmentid = department.id
                        LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` AS priority ON ticket.priorityid = priority.id
                        JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_statuses` AS status ON ticket.status = status.id
                        WHERE ticket.uid = ".esc_sql($MJTC_uid);
            $MJTC_query .= $MJTC_inquery;
            $total = majesticsupport::$_db->get_var($MJTC_query);
            majesticsupport::$_data[1] = MJTC_pagination::MJTC_getPagination($total,'myticket');

            // Data
            do_action('MJTC_addon_user_my_tickets');

            $MJTC_query = "SELECT ticket.*,department.departmentname AS departmentname ,priority.priority AS priority,priority.prioritycolour AS prioritycolour, status.status AS statustitle, status.statuscolour, status.statusbgcolour, product.product AS producttitle, COUNT(replies.id) AS reply_count ".majesticsupport::$_addon_query['select']."
                        FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket
                        LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_departments` AS department ON ticket.departmentid = department.id
                        ".majesticsupport::$_addon_query['join']."
                        LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` AS priority ON ticket.priorityid = priority.id
                        JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_statuses` AS status ON ticket.status = status.id
                        LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_products` AS product ON ticket.productid = product.id
                        LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_replies` AS replies 
                        ON ticket.id = replies.ticketid";
            $MJTC_query .= " WHERE ticket.uid = ". esc_sql($MJTC_uid) . $MJTC_inquery;
            $MJTC_query .= " GROUP BY ticket.id ORDER BY " . majesticsupport::$_ordering . " LIMIT " . MJTC_pagination::MJTC_getOffset() . ", " . MJTC_pagination::MJTC_getLimit();
            majesticsupport::$_data[0] = majesticsupport::$_db->get_results($MJTC_query);
            do_action('MJTC_reset_addon_query');
            if (majesticsupport::$_db->last_error != null) {
                MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
            }
            // if(majesticsupport::$_config['count_on_myticket'] == 1){
                $MJTC_query = "SELECT COUNT(ticket.id) "
                        . "FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket "
                        . "LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_departments` AS department ON ticket.departmentid = department.id "
                        . "LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` AS priority ON ticket.priorityid = priority.id "
                        . "WHERE ticket.uid = ".esc_sql($MJTC_uid)." AND (ticket.status != 5 AND ticket.status != 6)";
                majesticsupport::$_data['count']['openticket'] = majesticsupport::$_db->get_var($MJTC_query);

                $MJTC_query = "SELECT COUNT(ticket.id) "
                        . "FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket "
                        . "LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_departments` AS department ON ticket.departmentid = department.id "
                        . "LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` AS priority ON ticket.priorityid = priority.id "
                        . "WHERE ticket.uid = ". esc_sql($MJTC_uid) ." AND ticket.status = 4 ";
                majesticsupport::$_data['count']['answeredticket'] = majesticsupport::$_db->get_var($MJTC_query);

                $MJTC_query = "SELECT COUNT(ticket.id) "
                        . "FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket "
                        . "LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_departments` AS department ON ticket.departmentid = department.id "
                        . "LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` AS priority ON ticket.priorityid = priority.id "
                        . "WHERE ticket.uid = ". esc_sql($MJTC_uid) ." AND (ticket.status = 5 OR ticket.status = 6)";
                majesticsupport::$_data['count']['closedticket'] = majesticsupport::$_db->get_var($MJTC_query);

                $MJTC_query = "SELECT COUNT(ticket.id) "
                        . "FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket "
                        . "LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_departments` AS department ON ticket.departmentid = department.id "
                        . "LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` AS priority ON ticket.priorityid = priority.id "
                        . "WHERE ticket.uid = ". esc_sql($MJTC_uid);
                majesticsupport::$_data['count']['allticket'] = majesticsupport::$_db->get_var($MJTC_query);
            // }
        }
        return;
    }

    function getStaffTickets($MJTC_lst=null) {
        if (! in_array('agent',majesticsupport::$_active_addons)) {
            return;
        }

        $this->getOrdering();
        // Filter
        /*
          list variable detail
          1=>For open ticket
          2=>For closed ticket
          3=>For open answered ticket
          4=>For all my tickets
         */

        $MJTC_inquery = $this->combineOrSingleSearch();
        if($MJTC_lst != null) {
            majesticsupport::$_search['ticket']['list'] = $MJTC_lst;
        }
        $MJTC_list = isset(majesticsupport::$_search['ticket']) ? majesticsupport::$_search['ticket']['list'] : 1; // assign for reference
        majesticsupport::$_data['list'] = $MJTC_list;
        switch ($MJTC_list) {
            // Ticket Default Status
            // 1 -> Open Ticket
            // 2 -> Waiting admin/staff reply
            // 3 -> in progress
            // 4 -> waiting for customer reply
            // 5 -> close ticket
            case 1:$MJTC_inquery .= " AND (ticket.status != 5 AND ticket.status != 6)";
                break;
            case 2:$MJTC_inquery .= " AND (ticket.status = 5 OR ticket.status = 6) ";
                break;
            case 3:$MJTC_inquery .= " AND ticket.status = 4 ";
                break;
            case 4:$MJTC_inquery .= " ";
                break;
            case 5:$MJTC_inquery .= " AND ticket.isoverdue = 1 AND ticket.status != 5 AND ticket.status != 6 ";
                break;
        }

        $MJTC_uid = MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid();
        if ($MJTC_uid == 0 || !is_numeric($MJTC_uid))
            return false;
        $MJTC_staffid = MJTC_includer::MJTC_getModel('agent')->getStaffId($MJTC_uid);

        //to handle all tickets permissoin
        $MJTC_allowed = MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForTask('All Tickets');
        if($MJTC_allowed == true){
            $MJTC_agent_conditions = "1 = 1";
        }else{
            if(is_numeric($MJTC_staffid)) {
                $MJTC_agent_conditions = "ticket.staffid = ".esc_sql($MJTC_staffid)." OR ticket.departmentid IN (SELECT dept.departmentid FROM `" . majesticsupport::$_db->prefix . "mjtc_support_acl_user_access_departments` AS dept WHERE dept.staffid = " .esc_sql($MJTC_staffid).")";
            } else {
                return false;
            }
        }
        //show specific user's tickets
        $MJTC_userquery = "";
        $MJTC_uid = MJTC_request::MJTC_getVar('uid');
        if(is_numeric($MJTC_uid) && $MJTC_uid > 0){
            $MJTC_userquery .= " AND ticket.uid = ".esc_sql($MJTC_uid);
        }
        // Pagination
        $MJTC_query = "SELECT COUNT(ticket.id)
                    FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket
                    LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_departments` AS department ON ticket.departmentid = department.id
                    LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` AS priority ON ticket.priorityid = priority.id
                    JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_statuses` AS status ON ticket.status = status.id
                    WHERE (".esc_sql($MJTC_agent_conditions).") ";
        $MJTC_query .= $MJTC_inquery;
        $MJTC_query .= $MJTC_userquery;
        $total = majesticsupport::$_db->get_var($MJTC_query);
        majesticsupport::$_data[1] = MJTC_pagination::MJTC_getPagination($total,'myticket');

        // Data
        do_action('MJTC_addon_staff_my_tickets');
        $MJTC_query = "SELECT DISTINCT ticket.*,department.departmentname AS departmentname ,priority.priority AS priority,priority.prioritycolour AS prioritycolour,assignstaff.photo AS staffphoto,assignstaff.id AS staffid,assignstaff.uid AS staffuid, assignstaff.firstname AS staffname, status.status AS statustitle, status.statusbgcolour, status.statuscolour, product.product AS producttitle, COUNT(replies.id) AS reply_count ".majesticsupport::$_addon_query['select']."
                    FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket
                    LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_departments` AS department ON ticket.departmentid = department.id
                    LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` AS priority ON ticket.priorityid = priority.id
                    JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_statuses` AS status ON ticket.status = status.id
                    LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_products` AS product ON ticket.productid = product.id
                    LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_staff` AS assignstaff ON ticket.staffid = assignstaff.id
                    LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_replies` AS replies 
                        ON ticket.id = replies.ticketid 
                    ".majesticsupport::$_addon_query['join']."
                    WHERE (".esc_sql($MJTC_agent_conditions).") " . $MJTC_inquery . $MJTC_userquery;
        $MJTC_query .= " GROUP BY ticket.id ORDER BY " . majesticsupport::$_ordering . " LIMIT " . MJTC_pagination::MJTC_getOffset() . ", " . MJTC_pagination::MJTC_getLimit();
        majesticsupport::$_data[0] = majesticsupport::$_db->get_results($MJTC_query);
        do_action('MJTC_reset_addon_query');
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        // if(majesticsupport::$_config['count_on_myticket'] == 1){
            $MJTC_query = "SELECT COUNT(ticket.id)
                        FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket
                        LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_departments` AS department ON ticket.departmentid = department.id
                        LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` AS priority ON ticket.priorityid = priority.id
                        WHERE (".esc_sql($MJTC_agent_conditions).") AND (ticket.status != 5 AND ticket.status !=6) ".$MJTC_userquery;
            majesticsupport::$_data['count']['openticket'] = majesticsupport::$_db->get_var($MJTC_query);

            $MJTC_query = "SELECT COUNT(ticket.id)
                        FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket
                        LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_departments` AS department ON ticket.departmentid = department.id
                        LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` AS priority ON ticket.priorityid = priority.id
                        WHERE (".esc_sql($MJTC_agent_conditions).") AND ticket.status = 4 ".$MJTC_userquery;
            majesticsupport::$_data['count']['answeredticket'] = majesticsupport::$_db->get_var($MJTC_query);;

            $MJTC_query = "SELECT COUNT(ticket.id)
                        FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket
                        LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_departments` AS department ON ticket.departmentid = department.id
                        LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` AS priority ON ticket.priorityid = priority.id
                        WHERE (".esc_sql($MJTC_agent_conditions).") AND (ticket.status = 5 OR ticket.status = 6) ".$MJTC_userquery;
            majesticsupport::$_data['count']['closedticket'] = majesticsupport::$_db->get_var($MJTC_query);;


            $MJTC_query = "SELECT COUNT(ticket.id)
                        FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket
                        LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_departments` AS department ON ticket.departmentid = department.id
                        LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` AS priority ON ticket.priorityid = priority.id
                        WHERE (".esc_sql($MJTC_agent_conditions).") AND ticket.isoverdue = 1 AND ticket.status != 5 AND ticket.status != 6 ".$MJTC_userquery;
            majesticsupport::$_data['count']['overdue'] = majesticsupport::$_db->get_var($MJTC_query);

            $MJTC_query = "SELECT COUNT(ticket.id)
                        FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket
                        LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_departments` AS department ON ticket.departmentid = department.id
                        LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` AS priority ON ticket.priorityid = priority.id
                        WHERE (".esc_sql($MJTC_agent_conditions).")  ".$MJTC_userquery;
            majesticsupport::$_data['count']['allticket'] = majesticsupport::$_db->get_var($MJTC_query);
        // }
        return;
    }

    function getTicketsForForm($MJTC_id,$MJTC_formid='') {
        if (!isset($MJTC_formid) || $MJTC_formid == '' || !is_numeric($MJTC_formid)) {
           $MJTC_formid = MJTC_includer::MJTC_getModel('ticket')->getDefaultMultiFormId();
        }
        if ($MJTC_id) {
            if (!is_numeric($MJTC_id))
                return false;
            $MJTC_query = "SELECT ticket.*,department.departmentname AS departmentname ,priority.priority AS priority,priority.prioritycolour AS prioritycolour,user.name AS user_login, product.product AS producttitle
                        FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket
                        LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_departments` AS department ON ticket.departmentid = department.id
                        LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` AS priority ON ticket.priorityid = priority.id
                        LEFT JOIN `".majesticsupport::$_wpprefixforuser."mjtc_support_users` AS user ON user.id = ticket.uid
                        LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_products` AS product ON ticket.productid = product.id
                        WHERE ticket.id = " . esc_sql($MJTC_id);
            majesticsupport::$_data[0] = majesticsupport::$_db->get_row($MJTC_query);
            if (majesticsupport::$_db->last_error != null) {
                MJTC_includer::MJTC_getModel('systemerror')->addSystemError(); // if there is an error add it to system errorrs
            }else{
                if(!empty(majesticsupport::$_data[0])){
                    //to store hash value of id against old tickets
                    if( majesticsupport::$_data[0]->hash == null ){
                        $MJTC_hash = $this->generateHash($MJTC_id);
                        $MJTC_query = "UPDATE `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` SET `hash`='".esc_sql($MJTC_hash)."' WHERE id=".esc_sql($MJTC_id);
                        majesticsupport::$_db->query($MJTC_query);
                    } //end
                }
            }
            $MJTC_formid = majesticsupport::$_data[0]->multiformid;
        }
        majesticsupport::$_data['formid'] = $MJTC_formid;
        MJTC_includer::MJTC_getModel('attachment')->getAttachmentForForm($MJTC_id);
        MJTC_includer::MJTC_getModel('fieldordering')->getFieldsOrderingforForm(1,$MJTC_formid);
        return;
    }

    function getTicketForDetail($MJTC_id) {
        if (!is_numeric($MJTC_id)){
            return $MJTC_id;
        }
        if (in_array('agent', majesticsupport::$_active_addons) && majesticsupport::$_data['user_staff']) { //staff
            if(current_user_can('ms_support_ticket')){
                majesticsupport::$_data['permission_granted'] = true;
                MJTC_includer::MJTC_getObjectClass('wphdnotification')->MJTC_addSessionNotificationDataToTable(gmdate("Y-m-d h:i:s"),'','ticket_time_start_',$MJTC_id);
                if(in_array('timetracking', majesticsupport::$_active_addons)){
                    majesticsupport::$_data['time_taken'] = MJTC_includer::MJTC_getModel('timetracking')->getTimeTakenByTicketId($MJTC_id);
                }
            }else{
                majesticsupport::$_data['permission_granted'] = $this->validateTicketDetailForStaff($MJTC_id);
                if (majesticsupport::$_data['permission_granted']) { // validation passed
                    if(in_array('timetracking', majesticsupport::$_active_addons)){
                        MJTC_includer::MJTC_getObjectClass('wphdnotification')->MJTC_addSessionNotificationDataToTable(gmdate("Y-m-d h:i:s"),'','ticket_time_start_',$MJTC_id);
                        majesticsupport::$_data['time_taken'] = MJTC_includer::MJTC_getModel('timetracking')->getTimeTakenByTicketId($MJTC_id);
                    }
                }
            }

        } else { // user
            if(current_user_can('ms_support_ticket') || current_user_can('ms_support_ticket_tickets')){
                majesticsupport::$_data['permission_granted'] = true;
                if(in_array('timetracking', majesticsupport::$_active_addons)){
                    MJTC_includer::MJTC_getObjectClass('wphdnotification')->MJTC_addSessionNotificationDataToTable(gmdate("Y-m-d h:i:s"),'','ticket_time_start_',$MJTC_id);
                    majesticsupport::$_data['time_taken'] = MJTC_includer::MJTC_getModel('timetracking')->getTimeTakenByTicketId($MJTC_id);
                }
            }
            elseif (!MJTC_includer::MJTC_getObjectClass('user')->MJTC_isguest()) {
                majesticsupport::$_data['permission_granted'] = $this->validateTicketDetailForUser($MJTC_id);
            } else {
                majesticsupport::$_data['permission_granted'] = $this->validateTicketDetailForVisitor($MJTC_id);
            }
        }
        if (!majesticsupport::$_data['permission_granted']) { // validation failed
            return;
        }

        do_action('MJTC_ticket_detail_query');// TO HANDLE ALL THE QUERIES OF ADDONS

        $MJTC_query = "SELECT ticket.*,priority.priority AS priority,priority.prioritycolour AS prioritycolour,department.departmentname AS departmentname,status.status AS statustitle,status.statuscolour,status.statusbgcolour, product.product AS producttitle, wp_user.user_login AS userlogin
             ".majesticsupport::$_addon_query['select']."
            FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket
            LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` AS priority ON ticket.priorityid = priority.id
            JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_statuses` AS status ON ticket.status = status.id
            LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_products` AS product ON ticket.productid = product.id
            LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_departments` AS department ON ticket.departmentid = department.id
            LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_users` AS ms_user
                ON ms_user.id = ticket.uid
            LEFT JOIN `" . majesticsupport::$_db->prefix . "users` AS wp_user
                ON wp_user.ID = ms_user.wpuid
            ".majesticsupport::$_addon_query['join']."
            WHERE ticket.id = " . esc_sql($MJTC_id);
        majesticsupport::$_data[0] = majesticsupport::$_db->get_row($MJTC_query);

        do_action('MJTC_reset_addon_query');
        // check email is ban
        if(in_array('banemail', majesticsupport::$_active_addons) && !empty(majesticsupport::$_data[0]->email)){
            $MJTC_query = "SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_email_banlist` WHERE email = '" . esc_sql(majesticsupport::$_data[0]->email) . "'";
            majesticsupport::$_data[7] = majesticsupport::$_db->get_var($MJTC_query);
            if (majesticsupport::$_db->last_error != null) {
                MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
            }
        }else{
            majesticsupport::$_data[7] = 0;
        }
        if(in_array('note', majesticsupport::$_active_addons)){
            MJTC_includer::MJTC_getModel('note')->getNotes($MJTC_id);
        }
        MJTC_includer::MJTC_getModel('reply')->getReplies($MJTC_id);
        majesticsupport::$_data['ticket_attachment'] = MJTC_includer::MJTC_getModel('attachment')->getAttachmentForReply($MJTC_id, 0);
        $this->getTicketHistory($MJTC_id);

        if(majesticsupport::$_data[0]->uid > 0){

            //count all ticket of user
            $MJTC_query = "SELECT COUNT(id) FROM `" .majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE `uid` = ".esc_sql(majesticsupport::$_data[0]->uid);
            majesticsupport::$_data['nticket'] = majesticsupport::$_db->get_var($MJTC_query);

            //count all active ticket of user
            $MJTC_query = "SELECT COUNT(id) FROM `" .majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE `status` != 5  AND `status` != 6  AND `uid` = ".esc_sql(majesticsupport::$_data[0]->uid);
            majesticsupport::$_data['activeticket'] = majesticsupport::$_db->get_var($MJTC_query);

            //get user tickets for right widget
            $MJTC_inquery = " WHERE ticket.id != " . esc_sql($MJTC_id) . " AND ticket.uid = " . esc_sql(majesticsupport::$_data[0]->uid);
            if(!is_admin() && in_array('agent', majesticsupport::$_active_addons) && majesticsupport::$_data['user_staff']){
                $MJTC_allowed = MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForTask('All Tickets');
                if($MJTC_allowed != true){
                    $MJTC_staffid = MJTC_includer::MJTC_getModel('agent')->getStaffId(MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid());
                    if(is_numeric($MJTC_staffid)) {
                        $MJTC_inquery .= " AND (ticket.staffid = ".esc_sql($MJTC_staffid)." OR ticket.departmentid IN (SELECT dept.departmentid FROM `" . majesticsupport::$_db->prefix . "mjtc_support_acl_user_access_departments` AS dept WHERE dept.staffid = ".esc_sql($MJTC_staffid)."))";
                    }
                }
            }
            $MJTC_query = "SELECT ticket.id,ticket.subject,ticket.status,ticket.lock,ticket.isoverdue,ticket.multiformid,priority.priority AS priority,priority.prioritycolour AS prioritycolour,department.departmentname AS departmentname,status.status AS statustitle,status.statuscolour,status.statusbgcolour
                    FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket
                    LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` AS priority ON ticket.priorityid = priority.id
                    JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_statuses` AS status ON ticket.status = status.id
                    LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_departments` AS department ON ticket.departmentid = department.id";
            $MJTC_query .= $MJTC_inquery . " LIMIT 3 ";
            majesticsupport::$_data['usertickets'] = majesticsupport::$_db->get_results($MJTC_query);
        }
        //Hooks
        do_action('MJTC_ticketbeforeview', majesticsupport::$_data);

        return;
    }

    function getTicketToken($MJTC_id) {
        if (!is_numeric($MJTC_id)){
            return $MJTC_id;
        }
        $MJTC_token = "";
        $MJTC_query = "SELECT ticket.token
                    FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket
                    WHERE ticket.id = " . esc_sql($MJTC_id);
        $MJTC_token = majesticsupport::$_db->get_var($MJTC_query);
        return $MJTC_token;
    }

    function validateUserForTicket($MJTC_id) {
        if (!is_numeric($MJTC_id)) return false;
        if (!MJTC_includer::MJTC_getObjectClass('user')->MJTC_isguest()) {

        } else {
            majesticsupport::$_data['permission_granted'] = $this->checkTokenForTicketDetail($MJTC_id);
        }
        return;
    }

    function getRandomTicketId() {
        $match = '';
        $MJTC_customticketno = '';
        $MJTC_count = 0;
        //$match = 'Y';
		do {
            $MJTC_count++;
            $MJTC_ticketid = "";
            $MJTC_length = 9;
            $MJTC_sequence = majesticsupport::$_config['ticketid_sequence'];
            if($MJTC_sequence == 1){
                $MJTC_possible = "2346789bcdfghjkmnpqrtvwxyzBCDFGHJKLMNPQRTVWXYZ";
                // we refer to the length of $MJTC_possible a few times, so let's grab it now
                $MJTC_maxlength = MJTC_majesticsupportphplib::MJTC_strlen($MJTC_possible);
                if ($MJTC_length > $MJTC_maxlength) { // check for length overflow and truncate if necessary
                    $MJTC_length = $MJTC_maxlength;
                }
                // set up a counter for how many characters are in the ticketid so far
                $MJTC_i = 0;
                // add random characters to $MJTC_password until $MJTC_length is reached
                while ($MJTC_i < $MJTC_length) {
                    // pick a random character from the possible ones
                    $MJTC_char = MJTC_majesticsupportphplib::MJTC_substr($MJTC_possible, wp_rand(0, $MJTC_maxlength - 1), 1);
                    if (!MJTC_majesticsupportphplib::MJTC_strstr($MJTC_ticketid, $MJTC_char)) {
                        if ($MJTC_i == 0) {
                            if (ctype_alpha($MJTC_char)) {
                                $MJTC_ticketid .= $MJTC_char;
                                $MJTC_i++;
                            }
                        } else {
                            $MJTC_ticketid .= $MJTC_char;
                            $MJTC_i++;
                        }
                    }
                }
            }else{ // Sequential ticketid
                if($MJTC_ticketid == ""){
                    $MJTC_ticketid = 0; // by default its set to zero
                }
                //$MJTC_maxquery = "SELECT max(convert(ticketid, SIGNED INTEGER)) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets`";
                $MJTC_maxquery = "SELECT max(customticketno) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets`";
                $MJTC_maxticketid = majesticsupport::$_db->get_var($MJTC_maxquery);
                if(is_numeric($MJTC_maxticketid)){
                    $MJTC_ticketid = $MJTC_maxticketid + $MJTC_count;
                }else{
                    $MJTC_ticketid = $MJTC_ticketid + $MJTC_count;
                }
                $MJTC_customticketno = $MJTC_ticketid;
                $MJTC_padding_zeros = MJTC_includer::MJTC_getModel('configuration')->getConfigValue('padding_zeros_ticketid');

                $MJTC_idlen = MJTC_majesticsupportphplib::MJTC_strlen($MJTC_ticketid);
                while ($MJTC_idlen < $MJTC_padding_zeros) {
                    $MJTC_ticketid = "0".esc_sql($MJTC_ticketid);
                    $MJTC_idlen = MJTC_majesticsupportphplib::MJTC_strlen($MJTC_ticketid);
                }
            }
			$MJTC_prefix = "";
			$MJTC_suffix = "";			
			$MJTC_prefix = MJTC_includer::MJTC_getModel('configuration')->getConfigValue('prefix_ticketid');
			$MJTC_suffix = MJTC_includer::MJTC_getModel('configuration')->getConfigValue('suffix_ticketid');
            if ($MJTC_prefix != '') {
			    $MJTC_prefix = MJTC_majesticsupportphplib::MJTC_trim($MJTC_prefix);
            }
            if ($MJTC_suffix != '') {
			    $MJTC_suffix = MJTC_majesticsupportphplib::MJTC_trim($MJTC_suffix);
            }
			if($MJTC_prefix) $MJTC_ticketid = $MJTC_prefix . $MJTC_ticketid;
			if($MJTC_suffix) $MJTC_ticketid = $MJTC_ticketid . $MJTC_suffix;
			
            $MJTC_query = "SELECT count(ticketid) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE ticketid = '".esc_sql($MJTC_ticketid) ."'";
            $MJTC_row = majesticsupport::$_db->get_var($MJTC_query);
            if($MJTC_row > 0)
                $match = 'Y';
            else
                $match = 'N';
            /*
            $MJTC_rows = majesticsupport::$_db->get_results($MJTC_query);
                foreach ($MJTC_rows as $MJTC_row) {
                    if ($MJTC_ticketid == $MJTC_row->MJTC_ticketid)
                        $match = 'Y';
                    else
                        $match = 'N';
                }
             */   
        }while ($match == 'Y');
        $MJTC_result = array();
        $MJTC_result['ticketid'] = $MJTC_ticketid;
        $MJTC_result['customticketno'] = $MJTC_customticketno;
        return $MJTC_result;
    }

    function getInternalTicketId() {
        $match = '';
        //$match = 'Y';
        do {
            $MJTC_internalid = "";
            $MJTC_length = 9;
            $MJTC_possible = "2346789bcdfghjkmnpqrtvwxyzBCDFGHJKLMNPQRTVWXYZ";
            // we refer to the length of $MJTC_possible a few times, so let's grab it now
            $MJTC_maxlength = MJTC_majesticsupportphplib::MJTC_strlen($MJTC_possible);
            if ($MJTC_length > $MJTC_maxlength) { // check for length overflow and truncate if necessary
                $MJTC_length = $MJTC_maxlength;
            }
            // set up a counter for how many characters are in the internalid so far
            $MJTC_i = 0;
            // add random characters to $MJTC_password until $MJTC_length is reached
            while ($MJTC_i < $MJTC_length) {
                // pick a random character from the possible ones
                $MJTC_char = MJTC_majesticsupportphplib::MJTC_substr($MJTC_possible, wp_rand(0, $MJTC_maxlength - 1), 1);
                if (!MJTC_majesticsupportphplib::MJTC_strstr($MJTC_internalid, $MJTC_char)) {
                    if ($MJTC_i == 0) {
                        if (ctype_alpha($MJTC_char)) {
                            $MJTC_internalid .= $MJTC_char;
                            $MJTC_i++;
                        }
                    } else {
                        $MJTC_internalid .= $MJTC_char;
                        $MJTC_i++;
                    }
                }
            }
            $MJTC_query = "SELECT count(internalid) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE internalid = '".esc_sql($MJTC_internalid) ."'";
            $MJTC_row = majesticsupport::$_db->get_var($MJTC_query);
            if($MJTC_row > 0)
                $match = 'Y';
            else
                $match = 'N';
        }while ($match == 'Y');
        return  $MJTC_internalid;
    }

    function countTicket($MJTC_emailorid) {
        if (is_numeric($MJTC_emailorid)) { // its UserID
            $MJTC_counts = majesticsupport::$_db->get_var("SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE uid = " . esc_sql($MJTC_emailorid));
        } else { // its EmailAddress
            $MJTC_counts = majesticsupport::$_db->get_var("SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE email = '" . esc_sql($MJTC_emailorid) . "'");
        }
        return $MJTC_counts;
    }

    function getUnresolvedAdminTicketsCount() {
        $MJTC_counts = majesticsupport::$_db->get_var("SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket WHERE ticket.status != 4 AND ticket.status != 5 AND ticket.status != 6");
        return $MJTC_counts;
    }

    function countOpenTicket($MJTC_emailorid) {
        if (is_numeric($MJTC_emailorid)) { // its UserID
            $MJTC_counts = majesticsupport::$_db->get_var("SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE uid = " . esc_sql($MJTC_emailorid) . " AND status != 5");
        } else { // its EmailAddress
            $MJTC_counts = majesticsupport::$_db->get_var("SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE email = '" . esc_sql($MJTC_emailorid) . "' AND status != 5");
        }
        return $MJTC_counts;
    }

    function checkBannedEmail($MJTC_emailaddress) {
        if(!in_array('banemail', majesticsupport::$_active_addons)){
            return true;
        }
        $MJTC_counts = majesticsupport::$_db->get_var("SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_email_banlist` WHERE email = '" . esc_sql($MJTC_emailaddress) . "'");
        if ($MJTC_counts > 0) {
            $MJTC_data['loggeremail'] = $MJTC_emailaddress;
            $MJTC_data['title'] = esc_html(__('Ban Email', 'majestic-support'));
            $MJTC_data['log'] = esc_html(__('Ban email try to create ticket', 'majestic-support'));
            $MJTC_current_user = MJTC_includer::MJTC_getObjectClass('user')->MJTC_getMSCurrentUser(); // to get current user name
            $MJTC_currentUserName = $MJTC_current_user->display_name;
            $MJTC_data['logger'] = $MJTC_currentUserName;
            $MJTC_data['ipaddress'] = $this->getIpAddress();
            MJTC_includer::MJTC_getModel('banemaillog')->storebanemaillog($MJTC_data);
            MJTC_message::MJTC_setMessage(esc_html(__('Banned email cannot create ticket', 'majestic-support')), 'error');
            return false;
        }
        return true;
    }

    function getIpAddress() {
        //if client use the direct ip
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $MJTC_ip = MJTC_majesticsupportphplib::MJTC_htmlspecialchars($_SERVER['HTTP_CLIENT_IP']);
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $MJTC_ip = MJTC_majesticsupportphplib::MJTC_htmlspecialchars($_SERVER['HTTP_X_FORWARDED_FOR']);
        } else {
            $MJTC_ip = MJTC_majesticsupportphplib::MJTC_htmlspecialchars($_SERVER['REMOTE_ADDR']);
        }
        return $MJTC_ip;
    }



    function ticketValidate($MJTC_emailaddress = '') {
        //check the banned user / email
        if($MJTC_emailaddress != '' && in_array('banemail', majesticsupport::$_active_addons)){
            if (!$this->checkBannedEmail($MJTC_emailaddress)) {
                return false;
            }
        }
        if(in_array('maxticket', majesticsupport::$_active_addons)){
            //check the Maximum Tickets
            if (!MJTC_includer::MJTC_getModel('maxticket')->checkMaxTickets($MJTC_emailaddress)) {
                return false;
            }

            //check the Maximum Open Tickets

            if (!MJTC_includer::MJTC_getModel('maxticket')->checkMaxOpenTickets($MJTC_emailaddress)) {
                return false;
            }
        }

        return true;
    }

    function captchaValidate() {
        $MJTC_nonce_id = MJTC_request::MJTC_getVar('id');
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'save-ticket-'.$MJTC_nonce_id) ) {
            die( 'Security check Failed' );
        }
        if (MJTC_includer::MJTC_getObjectClass('user')->MJTC_isguest()) {
            if (majesticsupport::$_config['show_captcha_on_visitor_from_ticket'] == 1) {
                if (majesticsupport::$_config['captcha_selection'] == 1) { // Google recaptcha
                    $MJTC_gresponse = MJTC_majesticsupportphplib::MJTC_htmlspecialchars(majesticsupport::MJTC_sanitizeData($_POST['MJTC_g-recaptcha-response'])); // MJTC_sanitizeData() function uses wordpress santize functions
                    $MJTC_resp = MJTC_googleRecaptchaHTTPPost(majesticsupport::$_config['recaptcha_privatekey'],$MJTC_gresponse);

                    if ($MJTC_resp == true) {
                        return true;
                    } else {
                        # set the error code so that we can display it
                        MJTC_message::MJTC_setMessage(esc_html(__('Incorrect Captcha Code', 'majestic-support')), 'error');
                        return false;
                    }
                } else { // own captcha
                    $MJTC_captcha = new MJTC_captcha;
                    $MJTC_result = $MJTC_captcha->MJTC_checkCaptchaUserForm();
                    if ($MJTC_result == 1) {
                        return true;
                    } else {
                        MJTC_message::MJTC_setMessage(esc_html(__('Incorrect Captcha Code', 'majestic-support')), 'error');
                        return false;
                    }
                }
            }
        }
	return true;
    }

    function storeTickets($MJTC_data) {
        $MJTC_nonce_id = isset($MJTC_data['id']) ? $MJTC_data['id'] : '';
		$MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'save-ticket-'.$MJTC_nonce_id) ) {
            die( 'Security check Failed' );
        }
        if (isset($MJTC_data['email'])) {
            $MJTC_checkduplicatetk = $this->checkIsTicketDuplicate($MJTC_data['subject'],$MJTC_data['email']);
    		if(!$MJTC_checkduplicatetk){
    			return false;
    		}
        }
        if(isset($MJTC_data['departmentid']) && $MJTC_data['departmentid'] == ''){
            // auto assign
            $MJTC_data['departmentid'] = MJTC_includer::MJTC_getModel('department')->getDepartmentIDForAutoAssign();
        }

        if (!is_admin() && ( !isset($MJTC_data['ticketviaemail']) || $MJTC_data['ticketviaemail'] != 1) ) { //if not admin or Email Piping
            if (!$this->captchaValidate()) {
                return false;
            }
            $MJTC_email = isset($MJTC_data['email']) ? $MJTC_data['email'] : '';
            if (!$this->ticketValidate($MJTC_email)) {
                return 3;
            }
        }

        //paid support validation
        if(in_array('paidsupport', majesticsupport::$_active_addons) && class_exists('WooCommerce')){
            //ignore if admin or agent or visitor
            if(!MJTC_includer::MJTC_getObjectClass('user')->MJTC_isguest() && !is_admin() && !(in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff())){
                $MJTC_paidsupport = MJTC_includer::MJTC_getModel('paidsupport')->getPaidSupportList(MJTC_includer::MJTC_getObjectClass('user')->MJTC_wpuid(),$MJTC_data['paidsupportid']);
                if(empty($MJTC_paidsupport)){
                    MJTC_message::MJTC_setMessage(esc_html(__('Please select paid support item', 'majestic-support')), 'error');
                    return false;
                }
            }
        }

        $MJTC_data['ticketviaemail'] = isset($MJTC_data['ticketviaemail']) ? $MJTC_data['ticketviaemail'] : 0;
        if($MJTC_data['ticketviaemail'] != 1){ // do not check in ticket via email case
            //envato purchase code validation
            if(in_array('envatovalidation', majesticsupport::$_active_addons)){
                $MJTC_code = isset($MJTC_data['envatopurchasecode']) ? $MJTC_data['envatopurchasecode'] : '';
                $MJTC_pcode = isset($MJTC_data['prev_envatopurchasecode']) ? $MJTC_data['prev_envatopurchasecode'] : '';
                $MJTC_required = MJTC_includer::MJTC_getModel('configuration')->getConfigValue('envato_license_required');
                if($MJTC_required!=1 && empty($MJTC_code) && !empty($MJTC_pcode)){
                    $MJTC_envatoData = '';
                }
                if( (!empty($MJTC_code) && (empty($MJTC_pcode) || $MJTC_pcode!=$MJTC_code)) || ($MJTC_required==1 && (empty($MJTC_pcode) || $MJTC_pcode!=$MJTC_code)) ){
                    $MJTC_res = MJTC_includer::MJTC_getModel('envatovalidation')->validatePurchaseCode($MJTC_code);
                    if(!$MJTC_res){
                        MJTC_message::MJTC_setMessage(esc_html(__('No purchase found with that code', 'majestic-support')), 'error');
                        return false;
                    }else{
                        $MJTC_envatoData = wp_json_encode($MJTC_res);
                    }
                }
            }
        }

        // edd license
        if($MJTC_data['ticketviaemail'] != 1){ // do not check in ticket via email case
            if(in_array('easydigitaldownloads', majesticsupport::$_active_addons)){
                if(majesticsupport::$_config['verify_license_on_ticket_creation'] == 1){
                    if(isset($MJTC_data['eddlicensekey'])){
                        if($MJTC_data['eddlicensekey'] == ''){
                            MJTC_message::MJTC_setMessage(esc_html(__('Provide a valid license key to create a ticket.', 'majestic-support')), 'error');
                            return false;
                        }else{
                            $MJTC_l_result = MJTC_includer::MJTC_getModel('easydigitaldownloads')->getEDDLicenseVerification($MJTC_data['eddlicensekey']);
                            if($MJTC_l_result == 'expired'){
                                MJTC_message::MJTC_setMessage(esc_html(__('Your license has expired.', 'majestic-support')), 'error');
                                return false;
                            }elseif($MJTC_l_result == 'inactive'){
                                MJTC_message::MJTC_setMessage(esc_html(__('Your license is not active, activate your license.', 'majestic-support')), 'error');
                                return false;
                            }
                        }
                    }
                }
            }
        }

        $MJTC_sendEmail = true;
        if (isset($MJTC_data['id']) && is_numeric($MJTC_data['id'])) {
            $MJTC_sendEmail = false;
            $MJTC_updated = date_i18n('Y-m-d H:i:s');
            $MJTC_created = $MJTC_data['created'];
            if (isset($MJTC_data['isoverdue']) &&  $MJTC_data['isoverdue'] == 1) {// for edit case to change the overdue if criteria is passed
                $MJTC_curdate = date_i18n('Y-m-d H:i:s');
                if (date_i18n('Y-m-d',MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_data['duedate'])) > date_i18n('Y-m-d',MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_curdate))){
                    $MJTC_data['isoverdue'] = 0;
                }else{
                    $MJTC_query = "SELECT ticket.duedate FROM `".majesticsupport::$_db->prefix."mjtc_support_tickets` AS ticket WHERE ticket.id = ".esc_sql($MJTC_data['id']);
                    $MJTC_duedate = majesticsupport::$_db->get_var($MJTC_query);
                    if(date_i18n('Y-m-d',MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_data['duedate'])) != date_i18n('Y-m-d',MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_duedate))){
                        MJTC_ticketModel::MJTC_setMessage(esc_html(__('Due date error is not valid','majestic-support')),'error');
                        return; //Due Date must be greater then current date
                    }
                }
            }
            //to check hash
            $MJTC_query = "SELECT hash,uid FROM `".majesticsupport::$_db->prefix."mjtc_support_tickets` WHERE ticketid='".esc_sql($MJTC_data['ticketid'])."'";
            $MJTC_row = majesticsupport::$_db->get_row($MJTC_query);
            $MJTC_edituid = $MJTC_row->uid;
            if( $MJTC_row->hash != $this->generateHash($MJTC_data['id']) ){
                return false;
            }//end
        } else {
            $MJTC_idresult = $this->getRandomTicketId();
            $MJTC_data['ticketid'] = $MJTC_idresult['ticketid'];
            $MJTC_data['token'] = $this->generateTicketToken();
            $MJTC_data['customticketno'] = $MJTC_idresult['customticketno'];
            $MJTC_data['internalid'] = $this->getInternalTicketId();

            $MJTC_data['attachmentdir'] = $this->getRandomFolderName();
            $MJTC_created = date_i18n('Y-m-d H:i:s');
            $MJTC_updated = '';
        }
        if(isset($MJTC_data['assigntome']) && $MJTC_data['assigntome'] == 1){
            if (in_array('agent',majesticsupport::$_active_addons)) {
                $MJTC_uid = MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid();
                if(is_numeric($MJTC_uid)){
                    $MJTC_staffid = MJTC_includer::MJTC_getModel('agent')->getStaffId($MJTC_uid);
                    $MJTC_data['staffid'] = $MJTC_staffid;
                }
            }
        }else{
            $MJTC_data['staffid'] = isset($MJTC_data['staffid']) ? $MJTC_data['staffid'] : '';
        }
        $MJTC_data['status'] = ( isset( $MJTC_data['status'] ) && $MJTC_data['status'] !== '' ) ? $MJTC_data['status'] : '1';
        if (isset($MJTC_data['duedate']) && $MJTC_data['duedate'] != '') {
            $MJTC_data['duedate'] = date_i18n('Y-m-d',MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_data['duedate']));
        } else {
            $MJTC_data['duedate'] = '';
        }
        $MJTC_data['lastreply'] = isset($MJTC_data['lastreply']) ? $MJTC_data['lastreply'] : '';
        if (isset($MJTC_data['mjsupport_message'])) {
            $MJTC_data['message'] = MJTC_includer::MJTC_getModel('majesticsupport')->getSanitizedEditorData($_POST['mjsupport_message']); // use mjsupport_message to avoid conflict
    		$mjsupport_message = MJTC_includer::MJTC_getModel('majesticsupport')->msremovetags($MJTC_data['message']);
            $mjsupport_message = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($mjsupport_message);
        }
        //check if message field is set as required or not
        $MJTC_isRequired = MJTC_includer::MJTC_getModel('fieldordering')->checkIsFieldRequired('issuesummary',$MJTC_data['multiformid']);
        if(empty($MJTC_data['message']) && $MJTC_isRequired == 1){
            MJTC_message::MJTC_setMessage(esc_html(__('Message field cannot be empty', 'majestic-support')), 'error');
            return false;
        }
        $MJTC_data = majesticsupport::MJTC_sanitizeData($MJTC_data); // MJTC_sanitizeData() function uses wordpress santize functions
        if(isset($MJTC_envatoData)){
            $MJTC_data['envatodata'] = $MJTC_envatoData;
        }
        //custom field code start
        $MJTC_customflagforadd = false;
        $MJTC_customflagfordelete = false;
        $MJTC_custom_field_namesforadd = array();
        $MJTC_custom_field_namesfordelete = array();
        $MJTC_userfield = MJTC_includer::MJTC_getModel('fieldordering')->getUserfieldsfor(1,$MJTC_data['multiformid']);
        $MJTC_params = array();
        $MJTC_maxfilesizeallowed = majesticsupport::$_config['file_maximum_size'];
        foreach ($MJTC_userfield AS $MJTC_ufobj) {
            $MJTC_vardata = '';
            if($MJTC_ufobj->userfieldtype == 'file'){
                if(isset($MJTC_data[$MJTC_ufobj->field.'_1']) && $MJTC_data[$MJTC_ufobj->field.'_1']== 0){
                    $MJTC_vardata = $MJTC_data[$MJTC_ufobj->field.'_2'];
                }
                $MJTC_customflagforadd=true;
                $MJTC_custom_field_namesforadd[]=$MJTC_ufobj->field;
            }else if($MJTC_ufobj->userfieldtype == 'date'){
		//gmdate makes error
                $MJTC_vardata = isset($MJTC_data[$MJTC_ufobj->field]) ? gmdate("Y-m-d", MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_data[$MJTC_ufobj->field])) : '';
            }else{
                $MJTC_vardata = isset($MJTC_data[$MJTC_ufobj->field]) ? $MJTC_data[$MJTC_ufobj->field] : '';
            }
            if(isset($MJTC_data[$MJTC_ufobj->field.'_1']) && $MJTC_data[$MJTC_ufobj->field.'_1'] == 1){
                $MJTC_customflagfordelete = true;
                $MJTC_custom_field_namesfordelete[]= $MJTC_data[$MJTC_ufobj->field.'_2'];
            }
            if($MJTC_vardata != ''){

                if(is_array($MJTC_vardata)){
                    $MJTC_vardata = implode(', ', array_filter($MJTC_vardata));
                }
                $MJTC_params[$MJTC_ufobj->field] = MJTC_majesticsupportphplib::MJTC_htmlentities($MJTC_vardata);
            }
        }
        if($MJTC_data['id'] != ''){
            if(is_numeric($MJTC_data['id'])){
                $MJTC_query = "SELECT params FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE id = " . esc_sql($MJTC_data['id']);
                $MJTC_oParams = majesticsupport::$_db->get_var($MJTC_query);

                if(!empty($MJTC_oParams)){
                    $MJTC_oParams = json_decode($MJTC_oParams,true);
                    $MJTC_unpublihsedFields = MJTC_includer::MJTC_getModel('fieldordering')->getUserUnpublishFieldsfor(1);
                    foreach($MJTC_unpublihsedFields AS $MJTC_field){
                        if(isset($MJTC_oParams[$MJTC_field->field])){
                            $MJTC_params[$MJTC_field->field] = $MJTC_oParams[$MJTC_field->field];
                        }
                    }
                }
            }
        }
        $MJTC_params = html_entity_decode(wp_json_encode($MJTC_params, JSON_UNESCAPED_UNICODE));
        $MJTC_data['params'] = $MJTC_params;
        //custom field code end

	    if (!empty($mjsupport_message)) {
            $MJTC_data['message'] = $mjsupport_message;
        }
        $MJTC_data['created'] = $MJTC_created;
        $MJTC_data['updated'] = $MJTC_updated;


        if($MJTC_data['uid'] == 0 && isset($_SESSION['majestic-support']['notificationid'])){
            $MJTC_data['notificationid'] = MJTC_majesticsupportphplib::MJTC_htmlspecialchars($_SESSION['majestic-support']['notificationid']); // MJTC_sanitizeData() function uses wordpress santize functions
        }

        if(isset($MJTC_data['id']) && is_numeric($MJTC_data['id'])){
           $MJTC_data['uid'] = $MJTC_edituid;
        }
        $MJTC_sendnotification = false;
        $MJTC_row = MJTC_includer::MJTC_getTable('tickets');
		// this line make problem with custom field data (latin words)
        $MJTC_error = 0;
        if (!$MJTC_row->bind($MJTC_data)) {
            $MJTC_error = 1;
        }
        if (!$MJTC_row->store()) {
            $MJTC_error = 1;
        }

        if ($MJTC_error == 1) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
            $MJTC_messagetype = esc_html(__('Error', 'majestic-support'));
            $MJTC_sendEmail = false;
            MJTC_message::MJTC_setMessage(esc_html(__('Ticket has not been created', 'majestic-support')), 'error');
        } else {
            $MJTC_ticketid = $MJTC_row->id;
            $MJTC_sendnotification = true;
            $MJTC_messagetype = esc_html(__('Successfully', 'majestic-support'));

            //update hash value against ticket
            $MJTC_hash = $this->generateHash($MJTC_ticketid);
            $MJTC_query = "UPDATE `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` SET `hash`='".esc_sql($MJTC_hash)."' WHERE id=".esc_sql($MJTC_ticketid);
            majesticsupport::$_db->query($MJTC_query);

            // Storing Attachments
			$MJTC_data['ticketid'] = $MJTC_ticketid;
			if($MJTC_data['ticketviaemail'] != 1){ // since ticket via emial attacments are handled saprately
			   MJTC_includer::MJTC_getModel('attachment')->storeAttachments($MJTC_data);
			   MJTC_message::MJTC_setMessage(esc_html(__('Your ticket has been submitted successfully', 'majestic-support')), 'updated');

			   //removing custom field attachments
                if($MJTC_customflagfordelete == true){
				    foreach ($MJTC_custom_field_namesfordelete as $MJTC_key) {
					   $MJTC_res = $this->removeFileCustom($MJTC_ticketid,$MJTC_key);
				    }
	            }
                //storing custom field attachments
                if($MJTC_customflagforadd == true){
			        foreach ($MJTC_custom_field_namesforadd as $MJTC_key) {
                        if ($_FILES[$MJTC_key]['size'] > 0) { // logo
	                       $MJTC_res = $this->uploadFileCustom($MJTC_ticketid,$MJTC_key);
				        }
				    }
                }

                //update paid support item tickets
                if(isset($MJTC_paidsupport)){
                    $MJTC_paidsupport = $MJTC_paidsupport[0];
                    $MJTC_res = MJTC_includer::MJTC_getModel('paidsupport')->recordTicket($MJTC_paidsupport->itemid, $MJTC_ticketid);
                    if($MJTC_res){
                        $t = MJTC_includer::MJTC_getTable('tickets');
                        if($t->bind(array('id'=>$MJTC_ticketid,'paidsupportitemid'=>$MJTC_paidsupport->itemid))){
                            $t->store();
                        }
                    }
                }

			}
        }
        do_action('MJTC_after_ticket_create',$MJTC_data,$MJTC_ticketid);
        

        /* Push Notification */
        if($MJTC_data['id'] == '' && $MJTC_sendnotification == true && in_array('notification', majesticsupport::$_active_addons)){
            $MJTC_dataarray = array();
            $MJTC_dataarray['title'] = $MJTC_data['subject'];
            $MJTC_dataarray['body'] = esc_html(__("Created",'majestic-support'));

            //send notification to admin
            $MJTC_devicetoken = MJTC_includer::MJTC_getModel('notification')->checkSubscriptionForAdmin();
            if($MJTC_devicetoken){
                $MJTC_dataarray['link'] = admin_url("admin.php?page=majesticsupport_ticket&mjslay=ticketdetail&majesticsupportid=".$MJTC_ticketid);
                $MJTC_dataarray['devicetoken'] = $MJTC_devicetoken;
                $MJTC_value = majesticsupport::$_config[MJTC_majesticsupportphplib::MJTC_md5(MSTN)];
                if($MJTC_value != ''){
                  do_action('MJTC_send_push_notification',$MJTC_dataarray);
                }else{
                  do_action('MJTC_resetnotificationvalues');
                }
            }

            $MJTC_dataarray['link'] = majesticsupport::makeUrl(array('mjsmod'=>'ticket', 'mjslay'=>'ticketdetail', "majesticsupportid"=>$MJTC_ticketid,'mspageid'=>majesticsupport::getPageid()));
            // for department staff
            if(!empty($MJTC_data['departmentid']) && is_numeric($MJTC_data['departmentid'])){
                MJTC_includer::MJTC_getModel('notification')->sendNotificationToDepartment($MJTC_data['departmentid'],$MJTC_dataarray);
            }
            // for all
            if(isset($MJTC_data['departmentid']) && $MJTC_data['departmentid'] == ''){
                MJTC_includer::MJTC_getModel('notification')->sendNotificationToAllStaff($MJTC_dataarray);
            }

            // send notification to MJTC_uidticket create for)
            if($MJTC_data['uid'] > 0 && is_numeric($MJTC_data['uid']) && ($MJTC_data['uid'] != MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid())){
                $MJTC_devicetoken = MJTC_includer::MJTC_getModel('notification')->getUserDeviceToken($MJTC_data['uid']);
                $MJTC_dataarray['devicetoken'] = $MJTC_devicetoken;
                if($MJTC_devicetoken != '' && !empty($MJTC_devicetoken)){
                    $MJTC_value = majesticsupport::$_config[MJTC_majesticsupportphplib::MJTC_md5(MSTN)];
                    if($MJTC_value != ''){
                      do_action('MJTC_send_push_notification',$MJTC_dataarray);
                    }else{
                      do_action('MJTC_resetnotificationvalues');
                    }
                }
            }else if($MJTC_data['uid'] == 0 && isset($MJTC_data['notificationid']) && $MJTC_data['notificationid'] != ""){ //visitor
                $MJTC_tokenarray['emailaddress'] = $MJTC_data['email'];
                $MJTC_tokenarray['trackingid'] = $MJTC_data['ticketid'];
                $MJTC_tokenarray['sitelink']=MJTC_includer::MJTC_getModel('majesticsupport')->getEncriptedSiteLink();
                $MJTC_token = wp_json_encode($MJTC_tokenarray);
                include_once MJTC_PLUGIN_PATH . 'includes/encoder.php';
                $MJTC_encoder = new MJTC_encoder();
                $MJTC_encryptedtext = $MJTC_encoder->MJTC_encrypt($MJTC_token);
                $MJTC_dataarray['link'] = majesticsupport::makeUrl(array('mjsmod'=>'ticket' ,'task'=>'showticketstatus','action'=>'mstask','token'=>$MJTC_encryptedtext,'mspageid'=>majesticsupport::getPageid()));
                $MJTC_devicetoken = MJTC_includer::MJTC_getModel('notification')->getUserDeviceToken($MJTC_data['notificationid'],0);
                $MJTC_dataarray['devicetoken'] = $MJTC_devicetoken;
                if($MJTC_devicetoken != '' && !empty($MJTC_devicetoken)){
                    $MJTC_value = majesticsupport::$_config[MJTC_majesticsupportphplib::MJTC_md5(MSTN)];
                    if($MJTC_value != ''){
                      do_action('MJTC_send_push_notification',$MJTC_dataarray);
                    }else{
                      do_action('MJTC_resetnotificationvalues');
                    }
                }
            }

        }


        /* for activity log */
        if (!MJTC_includer::MJTC_getObjectClass('user')->MJTC_isguest()) {
            $MJTC_current_user = MJTC_includer::MJTC_getObjectClass('user')->MJTC_getMSCurrentUser(); // to get current user name
            $MJTC_currentUserName = $MJTC_current_user->display_name;
        }else{
            $MJTC_currentUserName = esc_html(__('Guest','majestic-support'));
        }
        $MJTC_eventtype = esc_html(__('New Ticket', 'majestic-support'));
        if (isset($MJTC_data['id']) && is_numeric($MJTC_data['id'])) {
            $MJTC_message = esc_html(__('Ticket is updated by', 'majestic-support')) . " ( " . $MJTC_currentUserName . " ) ";
        } else {
            $MJTC_message = esc_html(__('Ticket is created by', 'majestic-support')) . " ( " . $MJTC_currentUserName . " ) ";
        }
        if(in_array('tickethistory', majesticsupport::$_active_addons)){
            MJTC_includer::MJTC_getModel('tickethistory')->addActivityLog($MJTC_ticketid, 1, $MJTC_eventtype, $MJTC_message, $MJTC_messagetype);
        }

        // Send Emails
        if ($MJTC_sendEmail == true) {
            MJTC_includer::MJTC_getModel('email')->sendMail(1, 1, $MJTC_ticketid); // Mailfor, Create Ticket, Ticketid
            //For Hook
            $MJTC_ticketobject = majesticsupport::$_db->get_row("SELECT * FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE id = " . esc_sql($MJTC_ticketid));
            do_action('MJTC_ticketcreate', $MJTC_ticketobject);
        }
        /* to store internal notes */
        if(in_array('note', majesticsupport::$_active_addons)){
            if (isset($MJTC_data['internalnote']) && $MJTC_data['internalnote'] != '') {
                MJTC_includer::MJTC_getModel('note')->storeTicketInternalNote($MJTC_data, $MJTC_data['internalnote']);
            }
        }
        /* agent auto assign */
        do_action('MJTC_agentautoassign', $MJTC_ticketid);
        return $MJTC_ticketid;
    }

    function uploadFileCustom($MJTC_id,$MJTC_field){
        if(is_numeric($MJTC_id))
            MJTC_includer::MJTC_getObjectClass('uploads')->MJTC_storeTicketCustomUploadFile($MJTC_id,$MJTC_field);
    }

    function storeUploadFieldValueInParams($MJTC_ticketid,$MJTC_filename,$MJTC_field){
        if(!is_numeric($MJTC_ticketid)) return false;
        $MJTC_query = "SELECT params FROM `".majesticsupport::$_db->prefix."mjtc_support_tickets` WHERE id = ".esc_sql($MJTC_ticketid);
        $MJTC_params = majesticsupport::$_db->get_var($MJTC_query);
        $MJTC_decoded_params = json_decode($MJTC_params,true);
        $MJTC_decoded_params[$MJTC_field] = $MJTC_filename;
        $MJTC_encoded_params = wp_json_encode($MJTC_decoded_params, JSON_UNESCAPED_UNICODE);
        $MJTC_query = "UPDATE `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` SET params = '" . esc_sql($MJTC_encoded_params) . "' WHERE id = " . esc_sql($MJTC_ticketid);
        majesticsupport::$_db->query($MJTC_query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        return;
    }

    function removeTicket($MJTC_id, $MJTC_internalid) {
        $MJTC_sendEmail = true;
        if (!is_numeric($MJTC_id))
            return false;
        if ( in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) {
            $MJTC_allowed = MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForTask('Delete Ticket');
            if ($MJTC_allowed != true) {
                MJTC_message::MJTC_setMessage(esc_html(__('You are not allowed', 'majestic-support')), 'error');
                return;
            }
        } else if (!$this->validateTicketAction($MJTC_id, $MJTC_internalid)) {
            MJTC_message::MJTC_setMessage(esc_html(__('You are not allowed','majestic-support')), 'error');
            return false;
        }

        if ($this->canRemoveTicket($MJTC_id)) {
            majesticsupport::$_data['ticketid'] = $this->getTrackingIdById($MJTC_id);
            majesticsupport::$_data['ticketemail'] = $this->getTicketEmailById($MJTC_id);
            majesticsupport::$_data['staffid'] = $this->getStaffIdById($MJTC_id);
            majesticsupport::$_data['ticketsubject'] = $this->getTicketSubjectById($MJTC_id);
            // delete attachments
            $this->removeTicketAttachmentsByTicketid($MJTC_id);

            $MJTC_row = MJTC_includer::MJTC_getTable('tickets');
            if ($MJTC_row->delete($MJTC_id)) {
                $MJTC_messagetype = esc_html(__('Successfully', 'majestic-support'));
                MJTC_message::MJTC_setMessage(esc_html(__('Ticket has been deleted', 'majestic-support')), 'updated');
            } else {
                MJTC_includer::MJTC_getModel('systemerror')->addSystemError(); // if there is an error add it to system errorrs
                MJTC_message::MJTC_setMessage(esc_html(__('Ticket has not been deleted', 'majestic-support')), 'error');
                $MJTC_messagetype = esc_html(__('Error', 'majestic-support'));
                $MJTC_sendEmail = false;
            }

            // Send Emails
            if ($MJTC_sendEmail == true) {
                MJTC_includer::MJTC_getModel('email')->sendMail(1, 3); // Mailfor, Delete Ticket
                $MJTC_ticketobject = (object) array('ticketid' => majesticsupport::$_data['ticketid'], 'ticketemail' => majesticsupport::$_data['ticketemail']);
                do_action('MJTC_ticketdelete', $MJTC_ticketobject);
            }
            if(in_array('note', majesticsupport::$_active_addons)){
                // delete internal notes
                MJTC_includer::MJTC_getModel('note')->removeTicketInternalNote($MJTC_id);
            }
            // delete replies
            MJTC_includer::MJTC_getModel('reply')->removeTicketReplies($MJTC_id);
        } elseif (MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid() != 0) { // Not visitor {
            MJTC_message::MJTC_setMessage(esc_html(__('Ticket','majestic-support')).' '. esc_html(__('in use cannot be deleted', 'majestic-support')), 'error');
        }

        return;
    }

    function removeEnforceTicket($MJTC_id) {
        if (!current_user_can('manage_options') || !is_numeric($MJTC_id)) { //only admin can change it.
            return false;
        }
        $MJTC_sendEmail = true;
        if (!is_numeric($MJTC_id))
            return false;
        if ( in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) {
            $MJTC_allowed = MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForTask('Delete Ticket');
            if ($MJTC_allowed != true) {
                MJTC_message::MJTC_setMessage(esc_html(__('You are not allowed', 'majestic-support')), 'error');
                return;
            }
        }

        majesticsupport::$_data['ticketid'] = $this->getTrackingIdById($MJTC_id);
        majesticsupport::$_data['ticketemail'] = $this->getTicketEmailById($MJTC_id);
        majesticsupport::$_data['staffid'] = $this->getStaffIdById($MJTC_id);
        majesticsupport::$_data['ticketsubject'] = $this->getTicketSubjectById($MJTC_id);
		// delete attachments
		$this->removeTicketAttachmentsByTicketid($MJTC_id);

        $MJTC_row = MJTC_includer::MJTC_getTable('tickets');
        if ($MJTC_row->delete($MJTC_id)) {
		// delete attachments
            $MJTC_messagetype = esc_html(__('Successfully', 'majestic-support'));
            MJTC_message::MJTC_setMessage(esc_html(__('Ticket has been deleted', 'majestic-support')), 'updated');
        } else {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError(); // if there is an error add it to system errorrs
            MJTC_message::MJTC_setMessage(esc_html(__('Ticket has not been deleted', 'majestic-support')), 'error');
            $MJTC_messagetype = esc_html(__('Error', 'majestic-support'));
            $MJTC_sendEmail = false;
        }

        // Send Emails
        if ($MJTC_sendEmail == true) {
            MJTC_includer::MJTC_getModel('email')->sendMail(1, 3); // Mailfor, Delete Ticket
            $MJTC_ticketobject = (object) array('ticketid' => majesticsupport::$_data['ticketid'], 'ticketemail' => majesticsupport::$_data['ticketemail']);
            do_action('MJTC_ticketdelete', $MJTC_ticketobject);
        }
        if(in_array('note', majesticsupport::$_active_addons)){
            // delete internal notes
            MJTC_includer::MJTC_getModel('note')->removeTicketInternalNote($MJTC_id);
        }
        // delete replies
        MJTC_includer::MJTC_getModel('reply')->removeTicketReplies($MJTC_id);

        return;
    }

    function removeInternalNote($MJTC_id, $MJTC_internalid, $MJTC_internalnoteid) {
        if (!is_numeric($MJTC_id) || !is_numeric($MJTC_internalnoteid))
            return false;
        if ( in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) {
            $MJTC_allowed = MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForTask('Delete Internal Note');
            if ($MJTC_allowed != true) {
                MJTC_message::MJTC_setMessage(esc_html(__('You are not allowed', 'majestic-support')), 'error');
                return;
            }
        } else if (!$this->validateTicketAction($MJTC_id, $MJTC_internalid)) {
            MJTC_message::MJTC_setMessage(esc_html(__('You are not allowed','majestic-support')), 'error');
            return false;
        }

        $MJTC_row = MJTC_includer::MJTC_getTable('note');
        if ($MJTC_row->delete($MJTC_internalnoteid)) {
            $MJTC_messagetype = esc_html(__('Successfully', 'majestic-support'));
            MJTC_message::MJTC_setMessage(esc_html(__('Internal Note has been deleted', 'majestic-support')), 'updated');
        } else {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError(); // if there is an error add it to system errorrs
            MJTC_message::MJTC_setMessage(esc_html(__('Internal Note has not been deleted', 'majestic-support')), 'error');
            $MJTC_messagetype = esc_html(__('Error', 'majestic-support'));
        }

        return;
    }

    private function removeTicketAttachmentsByTicketid($MJTC_id){
        if(!is_numeric($MJTC_id)) return false;

        // --- START FILESYSTEM FIX ---
        global $wp_filesystem;
        if (!function_exists('wp_handle_upload')) {
            do_action('majesticsupport_load_wp_file');
        }
        if ( ! WP_Filesystem() ) {
            return false;
        }
        $MJTC_wp_filesystem = $wp_filesystem;
        // --- END FILESYSTEM FIX ---

        $MJTC_datadirectory = majesticsupport::$_config['data_directory'];
        $MJTC_maindir = wp_upload_dir();
        $mainpath = $MJTC_maindir['basedir'];
        $mainpath = $mainpath .'/'.$MJTC_datadirectory;
        $mainpath = $mainpath . '/attachmentdata';

        $MJTC_query = "SELECT ticket.attachmentdir
                    FROM `".majesticsupport::$_db->prefix."mjtc_support_tickets` AS ticket
                    WHERE ticket.id = ".esc_sql($MJTC_id);
        $MJTC_foldername = majesticsupport::$_db->get_var($MJTC_query);

        if(!empty($MJTC_foldername)){
            $MJTC_folder = $mainpath . '/ticket/'.$MJTC_foldername;

            // Replaced file_exists, glob, unlink, and rmdir with Filesystem API
            if($MJTC_wp_filesystem->exists($MJTC_folder)){
                // The second parameter 'true' makes the deletion recursive (removes files AND folder)
                $MJTC_wp_filesystem->delete($MJTC_folder, true);

                $MJTC_query = "DELETE FROM `".majesticsupport::$_db->prefix."mjtc_support_attachments` WHERE ticketid = ".esc_sql($MJTC_id);
                majesticsupport::$_db->query($MJTC_query);
            }
        }
    }

    private function canRemoveTicket($MJTC_id) {
        if (!is_numeric($MJTC_id))
            return false;
        if (!$this->canUserPerformThisAction($MJTC_id)) {
            MJTC_message::MJTC_setMessage(esc_html(__('You are not allowed','majestic-support')), 'error');
            return false;
        }
        $MJTC_query = "SELECT (
                    (SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_replies` WHERE ticketid = " . esc_sql($MJTC_id) . ") ";
                    if(in_array('note', majesticsupport::$_active_addons)){
                        $MJTC_query .= " +(SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_notes` WHERE ticketid = " . esc_sql($MJTC_id) . ") ";
                    }
                    $MJTC_query .= "
                    ) AS total";
        $MJTC_result = majesticsupport::$_db->get_var($MJTC_query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        if ($MJTC_result == 0)
            return true;
        else
            return false;
    }

    function canUserPerformThisAction($MJTC_id) {
        if (!is_numeric($MJTC_id))
            return false;
        if (!is_admin()) {
			if ( in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) {
				$MJTC_allowed = MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForTask('Delete Ticket');
				if ($MJTC_allowed == true) {
					return true;
				}
			}
            $MJTC_query = "SELECT uid FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE id = " . esc_sql($MJTC_id);
            $MJTC_uid = majesticsupport::$_db->get_var($MJTC_query);
            if (majesticsupport::$_db->last_error != null) {
                MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
            }
            $MJTC_ticketUid = $this->getTicketUidById($MJTC_id);
            $MJTC_currentuserid = MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid();
            if ($MJTC_currentuserid != $MJTC_ticketUid){
                return false;
            }
        }
        return true;
    }

    function getTicketUidById($MJTC_id) {
        if (!is_numeric($MJTC_id))
            return false;
        $MJTC_query = "SELECT uid FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE id = " . esc_sql($MJTC_id);
        $MJTC_uid = majesticsupport::$_db->get_var($MJTC_query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        return $MJTC_uid;
    }

    function getTicketSubjectById($MJTC_id) {
        if (!is_numeric($MJTC_id))
            return false;
        $MJTC_query = "SELECT subject FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE id = " . esc_sql($MJTC_id);
        $MJTC_subject = majesticsupport::$_db->get_var($MJTC_query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        return $MJTC_subject;
    }

    function getTrackingIdById($MJTC_id) {
        if (!is_numeric($MJTC_id))
            return false;
        $MJTC_query = "SELECT ticketid FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE id = " . esc_sql($MJTC_id);
        $MJTC_ticketid = majesticsupport::$_db->get_var($MJTC_query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        return $MJTC_ticketid;
    }

    function getTicketEmailById($MJTC_id) {
        if (!is_numeric($MJTC_id))
            return false;
        $MJTC_query = "SELECT email FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE id = " . esc_sql($MJTC_id);
        $MJTC_ticketemail = majesticsupport::$_db->get_var($MJTC_query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        return $MJTC_ticketemail;
    }

    function getStaffIdById($MJTC_id) {
        if (!is_numeric($MJTC_id))
            return false;
        $MJTC_query = "SELECT staffid FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE id = " . esc_sql($MJTC_id);
        $MJTC_staffid = majesticsupport::$_db->get_var($MJTC_query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        return $MJTC_staffid;
    }

    function setStatus($MJTC_status, $MJTC_ticketid) {
        // 0 -> New Ticket
        // 1 -> Waiting admin/staff reply
        // 2 -> in progress
        // 3 -> waiting for customer reply
        // 4 -> close ticket
        if (!is_numeric($MJTC_status))
            return false;
        if (!is_numeric($MJTC_ticketid))
            return false;
        $MJTC_row = MJTC_includer::MJTC_getTable('tickets');
        if (!$MJTC_row->update(array('id' => $MJTC_ticketid, 'status' => $MJTC_status))) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        return;
    }
    function getLastReply($MJTC_id) {
        if (!is_numeric($MJTC_id))
            return false;
        $MJTC_query = "SELECT reply.message FROM `" . majesticsupport::$_db->prefix . "mjtc_support_replies` AS reply WHERE reply.ticketid = " . esc_sql($MJTC_id) . " ORDER BY reply.created DESC LIMIT 1";
        $MJTC_message =majesticsupport::$_db->query($MJTC_query);
        return $MJTC_message;
    }
    function updateLastReply($MJTC_id) {
        if (!is_numeric($MJTC_id))
            return false;
        $MJTC_date = date_i18n('Y-m-d H:i:s');
        $MJTC_isanswered = " , isanswered = 0 ";
        if ( is_admin() || ( in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) ) {
            $MJTC_isanswered = " , isanswered = 1 ";
        }
        $MJTC_query = "UPDATE `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` SET lastreply = '" . esc_sql($MJTC_date) . "' " . $MJTC_isanswered . " WHERE id = " . esc_sql($MJTC_id);
        majesticsupport::$_db->query($MJTC_query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        return;
    }

    function closeTicket($MJTC_id, $MJTC_internalid = '' ,$MJTC_cron_flag = 0) { // second parameter is for crown call(when crown job is executed to hanled close ticket configuration)
        if (!is_numeric($MJTC_id))
            return false;
        if($MJTC_cron_flag == 0){
            //Check if its allowed to close ticket
            if ( in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) {
                $MJTC_allowed = MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForTask('Close Ticket');
                if ($MJTC_allowed != true) {
                    MJTC_message::MJTC_setMessage(esc_html(__('You are not allowed', 'majestic-support')), 'error');
                    return;
                }
            } else {
                if(!current_user_can('manage_options')){
                    // in case of user check for ticket owner
                    $MJTC_current_uid = MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid();
                    $MJTC_ticket_uid = $this->getUIdById($MJTC_id);
                    if ($MJTC_current_uid != $MJTC_ticket_uid) {
                        MJTC_message::MJTC_setMessage(esc_html(__('You are not allowed','majestic-support')), 'error');
                        return;
                    }
                }
            }
        }
        if (!$this->checkActionStatusSame($MJTC_id, array('action' => 'closeticket'))) {
            MJTC_message::MJTC_setMessage(esc_html(__('Ticket already closed', 'majestic-support')), 'error');
            return;
        }
        $MJTC_sendEmail = true;
        $MJTC_date = date_i18n('Y-m-d H:i:s');
        if($MJTC_cron_flag == 0){
            $MJTC_current_user = MJTC_includer::MJTC_getObjectClass('user')->MJTC_getMSCurrentUser(); // to get current user id
            $MJTC_closedby = isset($MJTC_current_user->display_name) ? $MJTC_current_user->id : -1;
        }else{
            $MJTC_closedby = 0;
        }


        $MJTC_row = MJTC_includer::MJTC_getTable('tickets');
        if ($MJTC_row->update(array('id' => $MJTC_id, 'status' => 5, 'closed' => $MJTC_date, 'closedby' => $MJTC_closedby, 'isoverdue' => 0))) {

            MJTC_message::MJTC_setMessage(esc_html(__('Ticket has been closed', 'majestic-support')), 'updated');
            $MJTC_messagetype = esc_html(__('Successfully', 'majestic-support'));
        } else {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError(); // if there is an error add it to system errorrs
            MJTC_message::MJTC_setMessage(esc_html(__('Ticket has not been closed', 'majestic-support')), 'error');
            $MJTC_messagetype = esc_html(__('Error', 'majestic-support'));
            $MJTC_sendEmail = false;
        }

        /* for activity log */
        $MJTC_ticketid = $MJTC_id; // get the ticket id
        if($MJTC_cron_flag == 0){
            $MJTC_current_user = MJTC_includer::MJTC_getObjectClass('user')->MJTC_getMSCurrentUser(); // to get current user name
            $MJTC_currentUserName = isset($MJTC_current_user->display_name) ? $MJTC_current_user->display_name : esc_html(__('Guest', 'majestic-support'));
        }else{
            $MJTC_currentUserName = esc_html(__('System', 'majestic-support'));
        }
        $MJTC_eventtype = esc_html(__('Close Ticket', 'majestic-support'));
        $MJTC_message = esc_html(__('Ticket is closed by', 'majestic-support')) . " ( " . esc_html($MJTC_currentUserName) . " ) ";
        $MJTC_query = " SELECT closedreason FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE id = " . esc_sql($MJTC_ticketid);
        $MJTC_closedreason = majesticsupport::$_db->get_var($MJTC_query);
        if (isset($MJTC_closedreason)) {
            $MJTC_message .= esc_html(__('due to the following reasons', 'majestic-support'));
            $MJTC_reasons = json_decode($MJTC_closedreason);
            foreach ($MJTC_reasons as $MJTC_reason) {
                $MJTC_message .= '<br>';
                $MJTC_message .= $MJTC_reason;
            }
        }
        if(in_array('tickethistory', majesticsupport::$_active_addons)){
            MJTC_includer::MJTC_getModel('tickethistory')->addActivityLog($MJTC_ticketid, 1, $MJTC_eventtype, $MJTC_message, $MJTC_messagetype);
        }

        // Send Emails
        if ($MJTC_sendEmail == true) {
            MJTC_includer::MJTC_getModel('email')->sendMail(1, 2, $MJTC_ticketid); // Mailfor, Close Ticket, Ticketid
            $MJTC_ticketobject = majesticsupport::$_db->get_row("SELECT * FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE id = " . esc_sql($MJTC_ticketid));
            do_action('MJTC_ticketclose', $MJTC_ticketobject);
        }
        // on ticket close make remove credentails data and show messsage on retrive.
        if(in_array('privatecredentials',majesticsupport::$_active_addons)){
            MJTC_includer::MJTC_getModel('privatecredentials')->deleteCredentialsOnCloseTicket($MJTC_ticketid);
        }
        return;
    }

    function getTicketListOrdering($MJTC_sort) {
        switch ($MJTC_sort) {
            case "subjectdesc":
                majesticsupport::$_ordering = "ticket.subject DESC";
                majesticsupport::$_sorton = "subject";
                majesticsupport::$_sortorder = "DESC";
                break;
            case "subjectasc":
                majesticsupport::$_ordering = "ticket.subject ASC";
                majesticsupport::$_sorton = "subject";
                majesticsupport::$_sortorder = "ASC";
                break;
            case "prioritydesc":
                majesticsupport::$_ordering = "priority.ordering DESC";
                majesticsupport::$_sorton = "priority";
                majesticsupport::$_sortorder = "DESC";
                break;
            case "priorityasc":
                majesticsupport::$_ordering = "priority.ordering ASC";
                majesticsupport::$_sorton = "priority";
                majesticsupport::$_sortorder = "ASC";
                break;
            case "ticketiddesc":
                majesticsupport::$_ordering = "ticket.ticketid DESC";
                majesticsupport::$_sorton = "ticketid";
                majesticsupport::$_sortorder = "DESC";
                break;
            case "ticketidasc":
                majesticsupport::$_ordering = "ticket.ticketid ASC";
                majesticsupport::$_sorton = "ticketid";
                majesticsupport::$_sortorder = "ASC";
                break;
            case "isanswereddesc":
                majesticsupport::$_ordering = "ticket.isanswered DESC";
                majesticsupport::$_sorton = "isanswered";
                majesticsupport::$_sortorder = "DESC";
                break;
            case "isansweredasc":
                majesticsupport::$_ordering = "ticket.isanswered ASC";
                majesticsupport::$_sorton = "isanswered";
                majesticsupport::$_sortorder = "ASC";
                break;
            case "statusdesc":
                majesticsupport::$_ordering = "ticket.status DESC";
                majesticsupport::$_sorton = "status";
                majesticsupport::$_sortorder = "DESC";
                break;
            case "statusasc":
                majesticsupport::$_ordering = "ticket.status ASC";
                majesticsupport::$_sorton = "status";
                majesticsupport::$_sortorder = "ASC";
                break;
            case "createddesc":
                majesticsupport::$_ordering = "ticket.created DESC";
                majesticsupport::$_sorton = "created";
                majesticsupport::$_sortorder = "DESC";
                break;
            case "createdasc":
                majesticsupport::$_ordering = "ticket.created ASC";
                majesticsupport::$_sorton = "created";
                majesticsupport::$_sortorder = "ASC";
                break;
            default:
                $MJTC_sortbyconfig = majesticsupport::$_config['tickets_sorting'];
                if($MJTC_sortbyconfig == 1){
                    $MJTC_sortbyconfig = "ASC";
                }else{
                    $MJTC_sortbyconfig = "DESC";
                }
                majesticsupport::$_ordering = "ticket.id $MJTC_sortbyconfig";
            break;
        }
        return;
    }

    function getSortArg($type, $MJTC_sort) {
        $mat = array();
        if (MJTC_majesticsupportphplib::MJTC_preg_match("/(\w+)(asc|desc)/i", $MJTC_sort, $mat)) {
            if ($type == $mat[1]) {
                return ( $mat[2] == "asc" ) ? "{$type}desc" : "{$type}asc";
            } else {
                return $type . $mat[2];
            }
        }
        $MJTC_sortlink = "id";
        // default sorting
        $MJTC_sortbyconfig = majesticsupport::$_config['tickets_sorting'];
        if($MJTC_sortbyconfig == 1){
            $MJTC_sortbyconfig = "asc";
        }else{
            $MJTC_sortbyconfig = "desc";
        }
        $MJTC_sortlink = $MJTC_sortlink.$MJTC_sortbyconfig;

        return $MJTC_sortlink;
    }

    function getTicketListSorting($MJTC_sort) {
        majesticsupport::$_sortlinks['subject'] = $this->getSortArg("subject", $MJTC_sort);
        majesticsupport::$_sortlinks['priority'] = $this->getSortArg("priority", $MJTC_sort);
        majesticsupport::$_sortlinks['ticketid'] = $this->getSortArg("ticketid", $MJTC_sort);
        majesticsupport::$_sortlinks['isanswered'] = $this->getSortArg("isanswered", $MJTC_sort);
        majesticsupport::$_sortlinks['status'] = $this->getSortArg("status", $MJTC_sort);
        majesticsupport::$_sortlinks['created'] = $this->getSortArg("created", $MJTC_sort);
        return;
    }

    private function getTicketHistory($MJTC_id) {
        if(in_array('tickethistory', majesticsupport::$_active_addons)){
            if(!is_numeric($MJTC_id)) return false;
            $MJTC_query = "SELECT al.id,al.message,al.datetime,al.uid,al.eventtype
            from `" . majesticsupport::$_db->prefix . "mjtc_support_activity_log`  AS al
            join `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS tic on al.referenceid=tic.id
            where al.referenceid=" . esc_sql($MJTC_id) . " AND al.eventfor=1 ORDER BY al.datetime DESC ";
            majesticsupport::$_data[5] = majesticsupport::$_db->get_results($MJTC_query);
        }else{
            majesticsupport::$_data[5] = array();
        }
    }

    function tickChangeStatus($MJTC_data) {
        $MJTC_ticketid = $MJTC_data['ticketid'];
        if (!is_numeric($MJTC_ticketid))
            return false;
        if ( in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) {
            $MJTC_allow = MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForTask('Change Ticket Status');
            if ($MJTC_allow != true) {
                MJTC_message::MJTC_setMessage(esc_html(__('Your are not allowed', 'majestic-support')), 'updated');
                return;
            }
        }
        $MJTC_date = date_i18n('Y-m-d H:i:s');

        $MJTC_row = MJTC_includer::MJTC_getTable('tickets');
        if ($MJTC_row->update(array('id' => $MJTC_ticketid, 'status' => $MJTC_data['status'], 'updated' => $MJTC_date))) {
            MJTC_message::MJTC_setMessage(esc_html(__('The status has been changed', 'majestic-support')), 'updated');
            $MJTC_messagetype = esc_html(__('Successfully', 'majestic-support'));
        } else {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError(); // if there is an error add it to system errorrs
            MJTC_message::MJTC_setMessage(esc_html(__('The status has not been changed', 'majestic-support')), 'error');
            $MJTC_messagetype = esc_html(__('Error', 'majestic-support'));
        }

        /* for activity log */
        $MJTC_current_user = MJTC_includer::MJTC_getObjectClass('user')->MJTC_getMSCurrentUser(); // to get current user name
        $MJTC_currentUserName = $MJTC_current_user->display_name;
        $MJTC_eventtype = esc_html(__('Ticket status change', 'majestic-support'));
        $MJTC_message = esc_html(__('The status is changed by', 'majestic-support')) . " ( " . esc_html($MJTC_currentUserName) . " ) ";
        if(in_array('tickethistory', majesticsupport::$_active_addons)){
            MJTC_includer::MJTC_getModel('tickethistory')->addActivityLog($MJTC_ticketid, 1, $MJTC_eventtype, $MJTC_message, $MJTC_messagetype);
        }
        return;
    }

    function tickDepartmentTransfer($MJTC_data) {
        $MJTC_ticketid = $MJTC_data['ticketid'];
        if (!is_numeric($MJTC_ticketid) || !is_numeric($MJTC_data['departmentid']))
            return false;
        if ( in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) {
            $MJTC_allow = MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForTask('Ticket Department Transfer');
            if ($MJTC_allow != true) {
                MJTC_message::MJTC_setMessage(esc_html(__('Your are not allowed', 'majestic-support')), 'updated');
                return;
            }
        }
        $MJTC_sendEmail = true;
        $MJTC_date = date_i18n('Y-m-d H:i:s');

        $MJTC_row = MJTC_includer::MJTC_getTable('tickets');
        if ($MJTC_row->update(array('id' => $MJTC_ticketid, 'departmentid' => $MJTC_data['departmentid'], 'updated' => $MJTC_date))) {
            MJTC_message::MJTC_setMessage(esc_html(__('The department has been transferred', 'majestic-support')), 'updated');
            $MJTC_messagetype = esc_html(__('Successfully', 'majestic-support'));
        } else {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError(); // if there is an error add it to system errorrs
            MJTC_message::MJTC_setMessage(esc_html(__('The department has not been transferred', 'majestic-support')), 'error');
            $MJTC_messagetype = esc_html(__('Error', 'majestic-support'));
            $MJTC_sendEmail = false;
        }

        /* for activity log */
        $MJTC_current_user = MJTC_includer::MJTC_getObjectClass('user')->MJTC_getMSCurrentUser(); // to get current user name
        $MJTC_currentUserName = $MJTC_current_user->display_name;
        $MJTC_eventtype = esc_html(__('Ticket department transfer', 'majestic-support'));
        $MJTC_message = esc_html(__('The department is transferred by', 'majestic-support')) . " ( " . esc_html($MJTC_currentUserName) . " ) ";
        if(in_array('tickethistory', majesticsupport::$_active_addons)){
            MJTC_includer::MJTC_getModel('tickethistory')->addActivityLog($MJTC_ticketid, 1, $MJTC_eventtype, $MJTC_message, $MJTC_messagetype);
        }

        // Send Emails
        if ($MJTC_sendEmail == true) {
            MJTC_includer::MJTC_getModel('email')->sendMail(1, 12, $MJTC_ticketid); // Mailfor, Department Ticket, Ticketid
            $MJTC_ticketobject = majesticsupport::$_db->get_row("SELECT * FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE id = " . esc_sql($MJTC_ticketid));
            do_action('MJTC_ticketclose', $MJTC_ticketobject);
        }

        /* to store internal notes FOR department transfer  */
        if (isset($MJTC_data['departmenttranfernote']) && $MJTC_data['departmenttranfernote'] != '') {
            MJTC_includer::MJTC_getModel('note')->storeTicketInternalNote($MJTC_data, $MJTC_data['departmenttranfernote']);
        }
        return;
    }

    function assignTicketToStaff($MJTC_data) {
        $MJTC_ticketid = $MJTC_data['ticketid'];
        if (!is_numeric($MJTC_ticketid) || !is_numeric($MJTC_data['staffid']))
            return false;
        if ( in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) {
            $MJTC_allow = MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForTask('Assign Ticket To Agent');
            if ($MJTC_allow != true) {
                MJTC_message::MJTC_setMessage(esc_html(__('You are not allowed', 'majestic-support')), 'error');
                return;
            }
        }
        $MJTC_sendEmail = true;
        $MJTC_date = date_i18n('Y-m-d H:i:s');

        $MJTC_row = MJTC_includer::MJTC_getTable('tickets');
        if ($MJTC_row->update(array('id' => $MJTC_ticketid, 'staffid' => $MJTC_data['staffid'], 'updated' => $MJTC_date))) {
            MJTC_message::MJTC_setMessage(esc_html(__('Assigned to agent', 'majestic-support')), 'updated');
            $MJTC_messagetype = esc_html(__('Successfully', 'majestic-support'));
        } else {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError(); // if there is an error add it to system errorrs
            MJTC_message::MJTC_setMessage(esc_html(__('Not assigned to agent', 'majestic-support')), 'error');
            $MJTC_messagetype = esc_html(__('Error', 'majestic-support'));
            $MJTC_sendEmail = false;
        }

        /* for activity log */
        $MJTC_current_user = MJTC_includer::MJTC_getObjectClass('user')->MJTC_getMSCurrentUser(); // to get current user name
        $MJTC_currentUserName = isset($MJTC_current_user->display_name) ? $MJTC_current_user->display_name : esc_html(__('Guest', 'majestic-support'));
        $MJTC_eventtype = esc_html(__('assign Ticket To Agent', 'majestic-support'));
        $MJTC_message = esc_html(__('Ticket is assigned to agent by', 'majestic-support')) . " ( " . esc_html($MJTC_currentUserName) . " ) ";
        if(in_array('tickethistory', majesticsupport::$_active_addons)){
            MJTC_includer::MJTC_getModel('tickethistory')->addActivityLog($MJTC_ticketid, 1, $MJTC_eventtype, $MJTC_message, $MJTC_messagetype);
        }

        // Send Emails
        if ($MJTC_sendEmail == true) {
            MJTC_includer::MJTC_getModel('email')->sendMail(1, 13, $MJTC_ticketid); // Mailfor, Assign Ticket, Ticketid
            $MJTC_ticketobject = majesticsupport::$_db->get_row("SELECT * FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE id = " . esc_sql($MJTC_ticketid));
            do_action('MJTC_ticketclose', $MJTC_ticketobject);
        }

        /* to store internal notes FOR department transfer  */
        if(in_array('note', majesticsupport::$_active_addons)){
            if (isset($MJTC_data['assignnote']) && $MJTC_data['assignnote'] != '') {
                MJTC_includer::MJTC_getModel('note')->storeTicketInternalNote($MJTC_data, $MJTC_data['assignnote']);
            }
        }
        return;
    }

    function changeTicketPriority($MJTC_id, $MJTC_priorityid) {
        if (!is_numeric($MJTC_id))
            return false;
        if (!is_numeric($MJTC_priorityid))
            return false;
        if (!$this->checkActionStatusSame($MJTC_id, array('action' => 'priority', 'id' => $MJTC_priorityid))) {
            MJTC_message::MJTC_setMessage(esc_html(__('The ticket already has the same priority', 'majestic-support')), 'error');
            return;
        }
        if ( in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) {
            $MJTC_allow = MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForTask('Change Ticket Priority');
            if ($MJTC_allow == 0) {
                MJTC_message::MJTC_setMessage(esc_html(__('You are not allowed', 'majestic-support')), 'error');
                return;
            }
        }
        $MJTC_sendEmail = true;
        $MJTC_date = date_i18n('Y-m-d H:i:s');

        $MJTC_row = MJTC_includer::MJTC_getTable('tickets');
        if ($MJTC_row->update(array('id' => $MJTC_id, 'priorityid' => $MJTC_priorityid, 'updated' => $MJTC_date))) {
            MJTC_message::MJTC_setMessage(esc_html(__('Priority has been changed', 'majestic-support')), 'updated');
            $MJTC_messagetype = esc_html(__('Successfully', 'majestic-support'));
        } else {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError(); // if there is an error add it to system errorrs
            MJTC_message::MJTC_setMessage(esc_html(__('Priority has not been changed', 'majestic-support')), 'error');
            $MJTC_messagetype = esc_html(__('Error', 'majestic-support'));
            $MJTC_sendEmail = false;
        }

        /* for activity log */
        $MJTC_current_user = MJTC_includer::MJTC_getObjectClass('user')->MJTC_getMSCurrentUser(); // to get current user name
        $MJTC_currentUserName = $MJTC_current_user->display_name;
        $MJTC_eventtype = esc_html(__('Change Priority', 'majestic-support'));
        $MJTC_message = esc_html(__('Ticket Priority Is Changed By', 'majestic-support')) . " ( " . esc_html($MJTC_currentUserName) . " ) ";
        if(in_array('tickethistory', majesticsupport::$_active_addons)){
            MJTC_includer::MJTC_getModel('tickethistory')->addActivityLog($MJTC_id, 1, $MJTC_eventtype, $MJTC_message, $MJTC_messagetype);
        }
        // Send Emails
        if ($MJTC_sendEmail == true) {
            MJTC_includer::MJTC_getModel('email')->sendMail(1, 11, $MJTC_id, 'mjtc_support_tickets'); // Mailfor, Ban email, Ticketid
        }
        return;
    }

    function banEmail($MJTC_data) {
        if(!in_array('banemail', majesticsupport::$_active_addons) || !is_numeric($MJTC_data['ticketid'])) {
            return false;
        }
        $MJTC_ticketid = $MJTC_data['ticketid'];
        $MJTC_uid = MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid();
        if(in_array('agent',majesticsupport::$_active_addons)){
            $MJTC_staffid = MJTC_includer::MJTC_getModel('agent')->getstaffid($MJTC_uid);
        }else{
            $MJTC_staffid = '';
        }
        if (!is_numeric($MJTC_ticketid))
            return false;
        if(!is_admin()){
            if (!is_numeric($MJTC_staffid))
                return false;
        }

        $MJTC_email = self::getTicketEmailById($MJTC_ticketid);
        if (!$this->checkActionStatusSame($MJTC_ticketid, array('action' => 'banemail', 'email' => $MJTC_email))) {
            MJTC_message::MJTC_setMessage(esc_html(__('Email already banned', 'majestic-support')), 'error');
            return;
        }

        $MJTC_sendEmail = true;
        $MJTC_data = array(
            'email' => $MJTC_email,
            'submitter' => $MJTC_staffid,
            'uid' => $MJTC_uid,
            'created' => date_i18n('Y-m-d H:i:s')
        );

        $MJTC_row = MJTC_includer::MJTC_getTable('banemail');

        $MJTC_data = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($MJTC_data);// remove slashes with quotes.
        $MJTC_error = 0;
        if (!$MJTC_row->bind($MJTC_data)) {
            $MJTC_error = 1;
        }
        if (!$MJTC_row->store()) {
            $MJTC_error = 1;
        }
        if ($MJTC_error == 0) {

            MJTC_message::MJTC_setMessage(esc_html(__('The email has been banned', 'majestic-support')), 'updated');
            $MJTC_messagetype = esc_html(__('Successfully', 'majestic-support'));
        } else {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError(); // if there is an error add it to system errorrs
            MJTC_message::MJTC_setMessage(esc_html(__('The email has not been banned', 'majestic-support')), 'error');
            $MJTC_messagetype = esc_html(__('Error', 'majestic-support'));
            $MJTC_sendEmail = false;
        }

        /* for activity log */
        $MJTC_current_user = MJTC_includer::MJTC_getObjectClass('user')->MJTC_getMSCurrentUser(); // to get current user name
        $MJTC_currentUserName = $MJTC_current_user->display_name;
        $MJTC_eventtype = esc_html(__('Ban Email', 'majestic-support'));
        $MJTC_message = esc_html(__('Email is banned by', 'majestic-support')) . " ( " . esc_html($MJTC_currentUserName) . " ) ";
        if(in_array('tickethistory', majesticsupport::$_active_addons)){
            MJTC_includer::MJTC_getModel('tickethistory')->addActivityLog($MJTC_ticketid, 1, $MJTC_eventtype, $MJTC_message, $MJTC_messagetype);
        }

        // Send Emails
        if ($MJTC_sendEmail == true) {
            MJTC_includer::MJTC_getModel('email')->sendMail(2, 1, $MJTC_ticketid, 'mjtc_support_tickets'); // Mailfor, Ban email, Ticketid
            $MJTC_ticketobject = majesticsupport::$_db->get_row("SELECT * FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE id = " . esc_sql($MJTC_ticketid));
            do_action('MJTC_ticketclose', $MJTC_ticketobject);
        }
        return;
    }



    function sendFeedbackMailByTicketid($MJTC_ticketid) {

        if (!is_numeric($MJTC_ticketid))
            return false;

        $MJTC_date = date_i18n('Y-m-d H:i:s');

        $MJTC_row = MJTC_includer::MJTC_getTable('tickets');
        if ($MJTC_row->update(array('id' => $MJTC_ticketid, 'feedbackemail' => 1))) {
            MJTC_includer::MJTC_getModel('email')->sendMail(1, 15, $MJTC_ticketid); // Mailfor, feedback for Ticket, Ticketid
        }
        return;
    }

    function banEmailAndCloseTicket($MJTC_data) {
        if ( in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) {
            $MJTC_allow = MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForTask('Ban Email And Close Ticket');
            if ($MJTC_allow != true) {
                MJTC_message::MJTC_setMessage(esc_html(__('You are not allowed', 'majestic-support')), 'error');
                return;
            }
        }
        self::banEmail($MJTC_data);
        self::closeTicket($MJTC_data['ticketid']);
        return;
    }

    /* check can a ticket be opened with in the given days */

    function checkCanReopenTicket($MJTC_ticketid) {
        if (!is_numeric($MJTC_ticketid))
            return false;
        $MJTC_lastreply = MJTC_includer::MJTC_getModel('reply')->getLastReply($MJTC_ticketid);
        if (!$MJTC_lastreply)
            $MJTC_lastreply = date_i18n('Y-m-d H:i:s');
        $MJTC_days = majesticsupport::$_config['reopen_ticket_within_days'];
        $MJTC_date = gmdate("Y-m-d H:i:s", MJTC_majesticsupportphplib::MJTC_strtotime(gmdate("Y-m-d H:i:s", MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_lastreply)) . " +" . esc_html($MJTC_days) . " day"));
        if ($MJTC_date < date_i18n('Y-m-d H:i:s'))
            return false;
        else
            return true;
    }

    function reopenTicket($MJTC_data) {
        $MJTC_ticketid = $MJTC_data['ticketid'];
        $MJTC_internalid = $MJTC_data['internalid'];
        $MJTC_lastreply = isset($MJTC_data['lastreplydate']) ? $MJTC_data['lastreplydate'] : '';
        if (!is_numeric($MJTC_ticketid))
            return false;
        //check the permission to reopen ticket
        if ( in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) {
            $MJTC_allowed = MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForTask('Reopen Ticket');
            if ($MJTC_allowed != true) {
                MJTC_message::MJTC_setMessage(esc_html(__('You are not allowed', 'majestic-support')), 'error');
                return;
            }
        } else if (!$this->validateTicketAction($MJTC_ticketid, $MJTC_internalid)) {
            MJTC_message::MJTC_setMessage(esc_html(__('You are not allowed','majestic-support')), 'error');
            return;
        } else {
            if(!current_user_can('manage_options')){
                // in case of user check for ticket owner
                $MJTC_current_uid = MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid();
                $MJTC_ticket_uid = MJTC_includer::MJTC_getModel('ticket')->getUIdById($MJTC_ticketid);
                if ($MJTC_current_uid != $MJTC_ticket_uid) {
                    return;
                }
            }
        }
        /* check can a ticket be opened with in the given days */
        if ($this->checkCanReopenTicket($MJTC_ticketid)) {
            $MJTC_sendEmail = true;
            $MJTC_date = date_i18n('Y-m-d H:i:s');

            $MJTC_row = MJTC_includer::MJTC_getTable('tickets');
            if ($MJTC_row->update(array('id' => $MJTC_ticketid, 'status' =>1, 'closedreason' =>'', 'updated' => $MJTC_date))) {
                MJTC_message::MJTC_setMessage(esc_html(__('The ticket has been reopened', 'majestic-support')), 'updated');
                $MJTC_messagetype = esc_html(__('Successfully', 'majestic-support'));
            } else {
                MJTC_includer::MJTC_getModel('systemerror')->addSystemError(); // if there is an error add it to system errorrs
                MJTC_message::MJTC_setMessage(esc_html(__('The ticket has not been reopened', 'majestic-support')), 'error');
                $MJTC_messagetype = esc_html(__('Error', 'majestic-support'));
                $MJTC_sendEmail = false;
            }

            /* for activity log */
            $MJTC_current_user = MJTC_includer::MJTC_getObjectClass('user')->MJTC_getMSCurrentUser(); // to get current user name
            $MJTC_currentUserName = isset($MJTC_current_user->display_name) ? $MJTC_current_user->display_name : esc_html(__('Guest', 'majestic-support'));
            $MJTC_eventtype = esc_html(__('Reopen Ticket', 'majestic-support'));
            $MJTC_message = esc_html(__('The ticket is reopened by', 'majestic-support')) . " ( " . esc_html($MJTC_currentUserName) . " ) ";
            if(in_array('tickethistory', majesticsupport::$_active_addons)){
                MJTC_includer::MJTC_getModel('tickethistory')->addActivityLog($MJTC_ticketid, 1, $MJTC_eventtype, $MJTC_message, $MJTC_messagetype);
            }
        } else {
            MJTC_message::MJTC_setMessage(esc_html(__('The ticket reopens time limit end', 'majestic-support')), 'error');
        }


        return;
    }

    private function canUnbanEmail($MJTC_email) {
        $MJTC_query = " SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_email_banlist` WHERE email = '" . esc_sql($MJTC_email) . "' ";
        $MJTC_result = majesticsupport::$_db->get_var($MJTC_query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        if ($MJTC_result > 0)
            return true;
        else
            return false;
    }

    function unbanEmail($MJTC_data) {
        $MJTC_ticketid = $MJTC_data['ticketid'];
        if (!is_numeric($MJTC_ticketid))
            return false;
        if ( in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) {
            $MJTC_allow = MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForTask('Unban Email');
            if ($MJTC_allow != true) {
                MJTC_message::MJTC_setMessage(esc_html(__('You are not allowed', 'majestic-support')), 'error');
                return;
            }
        }
        $MJTC_email = self::getTicketEmailById($MJTC_ticketid);
        if ($this->canUnbanEmail($MJTC_email)) {
            $MJTC_sendEmail = true;
            $MJTC_date = date_i18n('Y-m-d H:i:s');
            $MJTC_query = "DELETE FROM `" . majesticsupport::$_db->prefix . "mjtc_support_email_banlist` WHERE email = '" . esc_sql($MJTC_email) . " ' ";
            majesticsupport::$_db->query($MJTC_query);
            if (majesticsupport::$_db->last_error == null) {
                MJTC_message::MJTC_setMessage(esc_html(__('Email has been unbanned', 'majestic-support')), 'updated');
                $MJTC_messagetype = esc_html(__('Successfully', 'majestic-support'));
            } else {
                MJTC_includer::MJTC_getModel('systemerror')->addSystemError(); // if there is an error add it to system errorrs
                MJTC_message::MJTC_setMessage(esc_html(__('Email has not been unbanned', 'majestic-support')), 'error');
                $MJTC_messagetype = esc_html(__('Error', 'majestic-support'));
                $MJTC_sendEmail = false;
            }

            /* for activity log */
            $MJTC_current_user = MJTC_includer::MJTC_getObjectClass('user')->MJTC_getMSCurrentUser(); // to get current user name
            $MJTC_currentUserName = $MJTC_current_user->display_name;
            $MJTC_eventtype = esc_html(__('Unbanned Email', 'majestic-support'));
            $MJTC_message = esc_html(__('Email is unbanned by', 'majestic-support')) . " ( " . esc_html($MJTC_currentUserName) . " ) ";
            if(in_array('tickethistory', majesticsupport::$_active_addons)){
                MJTC_includer::MJTC_getModel('tickethistory')->addActivityLog($MJTC_ticketid, 1, $MJTC_eventtype, $MJTC_message, $MJTC_messagetype);
            }

            // Send Emails
            if ($MJTC_sendEmail == true) {
                MJTC_includer::MJTC_getModel('email')->sendMail(2, 2, $MJTC_ticketid, 'mjtc_support_tickets'); // Mailfor, Unban Ticket, Ticketid
                $MJTC_ticketobject = majesticsupport::$_db->get_row("SELECT * FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE id = " . esc_sql($MJTC_ticketid));
                do_action('MJTC_ticketclose', $MJTC_ticketobject);
            }
        } else {
            MJTC_message::MJTC_setMessage(esc_html(__('Email cannot be unbanned', 'majestic-support')), 'error');
        }

        return;
    }

    function markTicketInProgress($MJTC_data) {
        $MJTC_ticketid = $MJTC_data['ticketid'];
        if (!is_numeric($MJTC_ticketid))
            return false;
        if (!$this->checkActionStatusSame($MJTC_ticketid, array('action' => 'markinprogress'))) {
            MJTC_message::MJTC_setMessage(esc_html(__('Ticket already marked in progress', 'majestic-support')), 'error');
            return;
        }
        if ( in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) {
            $MJTC_allow = MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForTask('Mark In Progress');
            if ($MJTC_allow != true) {
                MJTC_message::MJTC_setMessage(esc_html(__('You are not allowed', 'majestic-support')), 'error');
                return;
            }
        }
        $MJTC_date = date_i18n('Y-m-d H:i:s');
        $MJTC_sendEmail = true;

        $MJTC_row = MJTC_includer::MJTC_getTable('tickets');
        if ($MJTC_row->update(array('id' => $MJTC_ticketid, 'status' => 3, 'updated' => $MJTC_date))) {
            MJTC_message::MJTC_setMessage(esc_html(__('The ticket has been marked as in progress', 'majestic-support')), 'updated');
            $MJTC_messagetype = esc_html(__('Successfully', 'majestic-support'));
        } else {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError(); // if there is an error add it to system errorrs
            MJTC_message::MJTC_setMessage(esc_html(__('The ticket has not been marked as in progress', 'majestic-support')), 'error');
            $MJTC_messagetype = esc_html(__('Error', 'majestic-support'));
            $MJTC_sendEmail = false;
        }

        /* for activity log */
        $MJTC_current_user = MJTC_includer::MJTC_getObjectClass('user')->MJTC_getMSCurrentUser(); // to get current user name
        $MJTC_currentUserName = $MJTC_current_user->display_name;
        $MJTC_eventtype = esc_html(__('In Progress Ticket', 'majestic-support'));
        $MJTC_message = esc_html(__('The ticket is marked as in progress by', 'majestic-support')) . " ( " . esc_html($MJTC_currentUserName) . " ) ";
        if(in_array('tickethistory', majesticsupport::$_active_addons)){
            MJTC_includer::MJTC_getModel('tickethistory')->addActivityLog($MJTC_ticketid, 1, $MJTC_eventtype, $MJTC_message, $MJTC_messagetype);
        }

        // Send Emails
        if ($MJTC_sendEmail == true) {
            MJTC_includer::MJTC_getModel('email')->sendMail(1, 9, $MJTC_ticketid, 'mjtc_support_tickets'); // Mailfor, Unban Ticket, Ticketid
            $MJTC_ticketobject = majesticsupport::$_db->get_row("SELECT * FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE id = " . esc_sql($MJTC_ticketid));
            do_action('MJTC_ticketclose', $MJTC_ticketobject);
        }
        return;
    }

    function updateTicketStatusCron() {
        // close ticket
        if(in_array('autoclose', majesticsupport::$_active_addons)){
            MJTC_includer::MJTC_getModel('autoclose')->autoCloseTicketsCron();
        }

        if(in_array('overdue', majesticsupport::$_active_addons)){
            MJTC_includer::MJTC_getModel('overdue')->markTicketOverdueCron();
        }
    }

    function sendFeedbackMail() {
        if(!in_array('feedback', majesticsupport::$_active_addons)){
            return;
        }
        if(majesticsupport::$_config['feedback_email_delay_type'] == 1){
            $MJTC_intrval_string = " gmdate(DATE_ADD(closed,INTERVAL " . (int)majesticsupport::$_config['feedback_email_delay']." DAY)) < '".gmdate("Y-m-d")."'";
        }else{
            $MJTC_intrval_string = " DATE_ADD(closed,INTERVAL " .(int) majesticsupport::$_config['feedback_email_delay'] . " HOUR) < '".date_i18n("Y-m-d H:i:s")."'";
        }
        // select closed ticket
        $MJTC_query = "SELECT id FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE ".$MJTC_intrval_string." AND status = 5 AND (feedbackemail != 1  OR feedbackemail IS NULL) AND closed IS NOT NULL";
        $MJTC_ticketids = majesticsupport::$_db->get_results($MJTC_query);
        if(!empty($MJTC_ticketids)){
            foreach ($MJTC_ticketids as $MJTC_key) {
                if(is_numeric($MJTC_key->id)){
                    MJTC_includer::MJTC_getModel('ticket')->sendFeedbackMailByTicketid($MJTC_key->id);
                }
            }
        }
        return;
    }

    function removeFileCustom($MJTC_id,$MJTC_key){
        if(!is_numeric($MJTC_id)) return false;
        $MJTC_filename = MJTC_majesticsupportphplib::MJTC_str_replace(' ', '_', $MJTC_key);
        $MJTC_filename = MJTC_majesticsupportphplib::MJTC_clean_file_path($MJTC_filename);
        $MJTC_maindir = wp_upload_dir();
        $MJTC_basedir = $MJTC_maindir['basedir'];
        $MJTC_datadirectory = majesticsupport::$_config['data_directory'];
        $MJTC_path = $MJTC_basedir . '/' . $MJTC_datadirectory. '/attachmentdata/ticket';

        $MJTC_query = "SELECT attachmentdir FROM `".majesticsupport::$_db->prefix."mjtc_support_tickets` WHERE id = ".esc_sql($MJTC_id);
        $MJTC_foldername = majesticsupport::$_db->get_var($MJTC_query);
        $MJTC_userpath = $MJTC_path . '/' . $MJTC_foldername.'/'.$MJTC_filename;
        if ( file_exists( $MJTC_userpath ) ) {
            wp_delete_file($MJTC_userpath);
        }
        return ;
    }

    function getTicketidForVisitor($MJTC_token) {
        include_once MJTC_PLUGIN_PATH . 'includes/encoder.php';
        $MJTC_encoder = new MJTC_encoder();
        $MJTC_decryptedtext = $MJTC_encoder->MJTC_decrypt($MJTC_token);
        $MJTC_array = json_decode($MJTC_decryptedtext, true);
        $MJTC_emailaddress = $MJTC_array['emailaddress'];
        $trackingid = $MJTC_array['trackingid'];
        if (isset($MJTC_array['sitelink']) && $MJTC_array['sitelink'] != '') {
            $MJTC_siteLink = $MJTC_array['sitelink'];
            include_once MJTC_PLUGIN_PATH . 'includes/encoder.php';
            $MJTC_encoder = new MJTC_encoder();
            $MJTC_savedSiteLink = get_option('ms_encripted_site_link');
            $MJTC_decryptedSiteLink = $MJTC_encoder->MJTC_decrypt($MJTC_siteLink);
            $MJTC_decryptedSavedSiteLink = $MJTC_encoder->MJTC_decrypt($MJTC_savedSiteLink);
            if ($MJTC_decryptedSiteLink != $MJTC_decryptedSavedSiteLink) {
                return false;
            }
        }
        if($MJTC_emailaddress == '' && $trackingid == ''){
            return false;
        }
        $MJTC_query = "SELECT id FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE email = '" . esc_sql($MJTC_emailaddress) . "' AND ticketid = '" . esc_sql($trackingid) . "'";
        $MJTC_ticketid = majesticsupport::$_db->get_var($MJTC_query);
        return $MJTC_ticketid;
    }

    function getTicketidForVisitorUsingToken($MJTC_token) {
        include_once MJTC_PLUGIN_PATH . 'includes/encoder.php';
        $MJTC_encoder = new MJTC_encoder();
        $MJTC_decryptedtext = $MJTC_encoder->MJTC_decrypt($MJTC_token);
        $MJTC_array = json_decode($MJTC_decryptedtext, true);
        $MJTC_token = $MJTC_array['token'];
        if (isset($MJTC_array['sitelink']) && $MJTC_array['sitelink'] != '') {
            $MJTC_siteLink = $MJTC_array['sitelink'];
            $MJTC_savedSiteLink = get_option('ms_encripted_site_link');
            $MJTC_decryptedSiteLink = $MJTC_encoder->MJTC_decrypt($MJTC_siteLink);
            $MJTC_decryptedSavedSiteLink = $MJTC_encoder->MJTC_decrypt($MJTC_savedSiteLink);
            if ($MJTC_decryptedSiteLink != $MJTC_decryptedSavedSiteLink) {
                return false;
            }
        }
        if($MJTC_token == ''){
            return false;
        }
        $MJTC_query = "SELECT id FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE token = '" . esc_sql($MJTC_token) . "'";
        $MJTC_ticketid = majesticsupport::$_db->get_var($MJTC_query);
        return $MJTC_ticketid;
    }

    function createTokenByEmailAndTrackingId($MJTC_emailaddress, $trackingid) {
        include_once MJTC_PLUGIN_PATH . 'includes/encoder.php';
        $MJTC_encoder = new MJTC_encoder();
        $MJTC_token = $MJTC_encoder->MJTC_encrypt(wp_json_encode(array('emailaddress' => $MJTC_emailaddress, 'trackingid' => $trackingid)));
        return $MJTC_token;
    }

    function getTokenByEmailAndTrackingId($MJTC_emailaddress, $trackingid) {
        $MJTC_query = "SELECT token FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE email = '" . esc_sql($MJTC_emailaddress) . "' AND ticketid = '" . esc_sql($trackingid) . "'";
        $MJTC_token = majesticsupport::$_db->get_var($MJTC_query);
        return $MJTC_token;
    }

    function validateTicketDetailForStaff($MJTC_ticketid) {
        if(!in_array('agent', majesticsupport::$_active_addons)){
            return false;
        }
        if (!is_numeric($MJTC_ticketid))
            return false;
        $MJTC_allowed = MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForTask('All Tickets');
        if($MJTC_allowed == true){
            return true;
        }
        // check in assign department
        $MJTC_c_uid = MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid();
        $MJTC_query = "SELECT ticket.id FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket
            JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_acl_user_access_departments` AS dept ON ticket.departmentid = dept.departmentid
            JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_staff` AS staff ON dept.staffid = staff.id AND staff.uid = " . esc_sql($MJTC_c_uid) . "
            WHERE ticket.id = " . esc_sql($MJTC_ticketid);
        $MJTC_id = majesticsupport::$_db->get_var($MJTC_query);

        if ($MJTC_id) {
            return true;
        } else {
            // check in assign ticket
            $MJTC_query = "SELECT ticket.id FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket
                JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_staff` AS staff ON ticket.staffid = staff.id AND staff.uid = " . esc_sql($MJTC_c_uid);
            $MJTC_query .= " WHERE ticket.id = ". esc_sql($MJTC_ticketid);
            $MJTC_id = majesticsupport::$_db->get_var($MJTC_query);
            if ($MJTC_id)
                return true;
            else
                return false;
        }
    }

    function totalTicket() {
        $MJTC_query = "SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets`";
        $total = majesticsupport::$_db->get_var($MJTC_query);
        return $total;
    }

    function validateTicketDetailForUser($MJTC_id) {
        if (!is_numeric($MJTC_id))
            return false;
        $MJTC_query = "SELECT uid FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE id = " . esc_sql($MJTC_id);
        $MJTC_uid = majesticsupport::$_db->get_var($MJTC_query);

        if ($MJTC_uid == MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid()) {
            return true;
        }elseif($MJTC_uid != '') {
            majesticsupport::$_data['error_message'] = 2;// to prompt user that he can not view this ticket.
            return;
        }else {
            return false;
        }
    }

    function validateTicketDetailForVisitor($MJTC_id) {
        if(!is_numeric($MJTC_id)) return false;
        if (!isset($_COOKIE['majestic-support-token-tkstatus'])) {
            return false;
        }
        $MJTC_token = MJTC_majesticsupportphplib::MJTC_htmlspecialchars($_COOKIE['majestic-support-token-tkstatus']);

        include_once MJTC_PLUGIN_PATH . 'includes/encoder.php';
        $MJTC_encoder = new MJTC_encoder();
        $MJTC_decryptedtext = $MJTC_encoder->MJTC_decrypt($MJTC_token);
        $MJTC_array = json_decode($MJTC_decryptedtext, true);
        if (!empty($MJTC_array['token'])) {
            $MJTC_token = $MJTC_array['token'];
            //$trackingid = $MJTC_array['trackingid'];
            $MJTC_query = "SELECT id FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE token = '" . esc_sql($MJTC_token) . "'";
        } else {
            $MJTC_emailaddress = $MJTC_array['emailaddress'];
            $trackingid = $MJTC_array['trackingid'];
            $MJTC_query = "SELECT id FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE email = '" . esc_sql($MJTC_emailaddress) . "' AND ticketid = '" . esc_sql($trackingid) . "'";
        }
        $MJTC_ticketid = majesticsupport::$_db->get_var($MJTC_query);

        if ($MJTC_ticketid == $MJTC_id) {
            return true;
        } else {
            $MJTC_query = "SELECT id FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE id = ".esc_sql($MJTC_id);
            $MJTC_ticketid = majesticsupport::$_db->get_var($MJTC_query);
            if($MJTC_ticketid > 0){
                majesticsupport::$_data['error_message'] = 1;// to prompt user to login
            }
            majesticsupport::$_data['error_message'] = 1;
            return false;
        }
    }

    function checkActionStatusSame($MJTC_id, $MJTC_array) {
        switch ($MJTC_array['action']) {
            case 'priority':
                if(!is_numeric($MJTC_id)) return false;
                if(!is_numeric($MJTC_array['id'])) return false;
                $MJTC_result = majesticsupport::$_db->get_var('SELECT COUNT(id) FROM `' . majesticsupport::$_db->prefix . 'mjtc_support_tickets` WHERE id = ' . esc_sql($MJTC_id) . ' AND priorityid = ' . esc_sql($MJTC_array['id']));
                break;
            case 'markoverdue':
                if(!is_numeric($MJTC_id)) return false;
                $MJTC_result = majesticsupport::$_db->get_var('SELECT COUNT(id) FROM `' . majesticsupport::$_db->prefix . 'mjtc_support_tickets` WHERE id = ' . esc_sql($MJTC_id) . ' AND isoverdue = 1');
                break;
            case 'markinprogress':
                if(!is_numeric($MJTC_id)) return false;
                $MJTC_result = majesticsupport::$_db->get_var('SELECT COUNT(id) FROM `' . majesticsupport::$_db->prefix . 'mjtc_support_tickets` WHERE id = ' . esc_sql($MJTC_id) . ' AND status = 3');
                break;
            case 'closeticket':
                if(!is_numeric($MJTC_id)) return false;
                $MJTC_result = majesticsupport::$_db->get_var('SELECT COUNT(id) FROM `' . majesticsupport::$_db->prefix . 'mjtc_support_tickets` WHERE id = ' . esc_sql($MJTC_id) . ' AND status = 5');
                break;
            case 'banemail':
                $MJTC_result = majesticsupport::$_db->get_var('SELECT COUNT(id) FROM `' . majesticsupport::$_db->prefix . 'mjtc_support_email_banlist` WHERE email = "' . esc_sql($MJTC_array['email']) . '"');
                break;
        }
        if ($MJTC_result > 0) {
            return false;
        } else {
            return true;
        }
    }

    function ticketAssignToMe($MJTC_ticketid, $MJTC_staffid) {
        if (!is_numeric($MJTC_ticketid))
            return false;
        if (!is_numeric($MJTC_staffid))
            return false;
        $MJTC_row = MJTC_includer::MJTC_getTable('tickets');
        $MJTC_row->update(array('id' => $MJTC_ticketid, 'staffid' => $MJTC_staffid));

        return true;
    }

    function isTicketAssigned($MJTC_ticketid){
        if (! in_array('agent',majesticsupport::$_active_addons)) {
            return false;
        }
        if (!is_numeric($MJTC_ticketid))
            return false;
        $MJTC_query = "SELECT staffid FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE id=".esc_sql($MJTC_ticketid);
        $MJTC_staffid = majesticsupport::$_db->get_var($MJTC_query);
        if($MJTC_staffid > 0)
            return true;
        return false;
    }

    function getMyTicketInfo_Widget($MJTC_maxrecord){
        if(!is_numeric($MJTC_maxrecord)) return false;
        if(!MJTC_includer::MJTC_getObjectClass('user')->MJTC_isguest()){
            $MJTC_uid = MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid();
                // Data
            $MJTC_query = "SELECT DISTINCT ticket.id,ticket.subject,ticket.status,ticket.name,priority.priority AS priority,priority.prioritycolour AS prioritycolour
                        FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket
                        LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_departments` AS department ON ticket.departmentid = department.id
                        LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` AS priority ON ticket.priorityid = priority.id
                        WHERE ticket.uid = ".esc_sql($MJTC_uid)." AND (ticket.status = 1 OR ticket.status = 2) ORDER BY ticket.status DESC LIMIT ".esc_sql($MJTC_maxrecord);

            if(in_array('agent',majesticsupport::$_active_addons)){
                $MJTC_staffid = MJTC_includer::MJTC_getModel('agent')->getStaffId($MJTC_uid);
                if($MJTC_staffid){
                    // Data
                    $MJTC_query = "SELECT DISTINCT ticket.id,ticket.subject,ticket.status,status.statuscolour,status.statusbgcolour,ticket.name,priority.priority AS priority,priority.prioritycolour AS prioritycolour
                                FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket
                                LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_departments` AS department ON ticket.departmentid = department.id
                                LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` AS priority ON ticket.priorityid = priority.id
                                JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_statuses` AS status ON ticket.status = status.id
                                LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_staff` AS staff ON staff.uid = ticket.uid
                                WHERE (ticket.staffid = ".esc_sql($MJTC_staffid)." OR ticket.departmentid IN (SELECT dept.departmentid FROM `" . majesticsupport::$_db->prefix . "mjtc_support_acl_user_access_departments` AS dept WHERE dept.staffid = ".esc_sql($MJTC_staffid).")) AND (ticket.status = 1 OR ticket.status = 2) ORDER BY ticket.status DESC LIMIT ".esc_sql($MJTC_maxrecord);
                }
            }
            if(isset($MJTC_query)){
                majesticsupport::$_data['widget_myticket'] = majesticsupport::$_db->get_results($MJTC_query);
                if (majesticsupport::$_db->last_error != null) {
                    MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
                }
            }else{
                majesticsupport::$_data['widget_myticket'] = false;
            }
        }else{
            majesticsupport::$_data['widget_myticket'] = false;
        }
        return;
    }

    function getLatestTicketForDashboard(){
        $MJTC_query = "SELECT ticket.id,ticket.subject,ticket.name,priority.priority,priority.prioritycolour
                    FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket
                    LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` AS priority ON priority.id = ticket.priorityid
                    ORDER BY ticket.status ASC, ticket.created DESC LIMIT 0, 5";
        $MJTC_tickets = majesticsupport::$_db->get_results($MJTC_query);
        return $MJTC_tickets;
    }
    function getAttachmentByTicketId($MJTC_id, $MJTC_internalid = ''){
        if(!is_numeric($MJTC_id)) return false;
        //ignore if admin or agent
        if(!current_user_can('manage_options') && !(in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff())){
            if (!$this->validateTicketAction($MJTC_id, $MJTC_internalid)) {
                die('You are not allowed');
                return false;
            }
            // in case of user check for ticket owner
            if (!MJTC_includer::MJTC_getObjectClass('user')->MJTC_isguest()) {
                $MJTC_current_uid = MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid();
                $MJTC_ticket_uid = MJTC_includer::MJTC_getModel('ticket')->getUIdById($MJTC_id);
                if ($MJTC_current_uid != $MJTC_ticket_uid) {
                    return;
                }
            } else {
                if (!$this->validateTicketDetailForVisitor($MJTC_id)) {
                    return;
                }
            }
            
        }
        $MJTC_query = "SELECT attachment.filename , ticket.attachmentdir
                    FROM `" . majesticsupport::$_db->prefix . "mjtc_support_attachments` AS attachment
                    JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket ON ticket.id = attachment.ticketid AND ticket.id =".esc_sql($MJTC_id). " AND attachment.replyattachmentid = 0 ";
        $MJTC_attachments = majesticsupport::$_db->get_results($MJTC_query);
        return $MJTC_attachments;
    }

    function getTotalStatsForDashboard(){
        $MJTC_curdate = date_i18n('Y-m-d');
        $MJTC_fromdate = date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime("now -1 month"));

        $MJTC_query = "SELECT COUNT(id) FROM `".majesticsupport::$_db->prefix."mjtc_support_tickets` WHERE status = 1 AND (lastreply = '0000-00-00 00:00:00' OR lastreply = '') AND date(created) >= '".esc_sql($MJTC_fromdate)."'AND date(created) <= '".esc_sql($MJTC_curdate)."'";
        $MJTC_result['open'] = majesticsupport::$_db->get_var($MJTC_query);
        $MJTC_query = "SELECT COUNT(id) FROM `".majesticsupport::$_db->prefix."mjtc_support_tickets` WHERE isanswered = 1 AND status != 5 AND status != 1 AND date(created) >= '".esc_sql($MJTC_fromdate)."' AND date(created) <= '".esc_sql($MJTC_curdate)."'";
        $MJTC_result['answered'] = majesticsupport::$_db->get_var($MJTC_query);
        $MJTC_query = "SELECT COUNT(id) FROM `".majesticsupport::$_db->prefix."mjtc_support_tickets` WHERE isoverdue = 1 AND status != 5 AND date(created) >= '".esc_sql($MJTC_fromdate)."' AND date(created) <= '".esc_sql($MJTC_curdate)."'";
        $MJTC_result['overdue'] = majesticsupport::$_db->get_var($MJTC_query);
        $MJTC_query = "SELECT COUNT(id) FROM `".majesticsupport::$_db->prefix."mjtc_support_tickets` WHERE isanswered != 1 AND status != 5 AND (lastreply != '0000-00-00 00:00:00' AND lastreply != '') AND date(created) >= '".esc_sql($MJTC_fromdate)."' AND date(created) <= '".esc_sql($MJTC_curdate)."'";
        $MJTC_result['pending'] = majesticsupport::$_db->get_var($MJTC_query);

        return $MJTC_result;
    }

    function getRandomFolderName() {
        $MJTC_foldername = "";
        $MJTC_length = 7;
        $MJTC_possible = "qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM";
        // we refer to the length of $MJTC_possible a few times, so let's grab it now
        $MJTC_maxlength = MJTC_majesticsupportphplib::MJTC_strlen($MJTC_possible);
        if ($MJTC_length > $MJTC_maxlength) { // check for length overflow and truncate if necessary
            $MJTC_length = $MJTC_maxlength;
        }
        // set up a counter for how many characters are in the ticketid so far
        $MJTC_i = 0;
        // add random characters to $MJTC_password until $MJTC_length is reached
        while ($MJTC_i < $MJTC_length) {
            // pick a random character from the possible ones
            $MJTC_char = MJTC_majesticsupportphplib::MJTC_substr($MJTC_possible, wp_rand(0, $MJTC_maxlength - 1), 1);
            if (!MJTC_majesticsupportphplib::MJTC_strstr($MJTC_foldername, $MJTC_char)) {
                if ($MJTC_i == 0) {
                    if (ctype_alpha($MJTC_char)) {
                        $MJTC_foldername .= $MJTC_char;
                        $MJTC_i++;
                    }
                } else {
                    $MJTC_foldername .= $MJTC_char;
                    $MJTC_i++;
                }
            }
        }
        return $MJTC_foldername;
    }

    static function generateHash($MJTC_id){
        if(!is_numeric($MJTC_id))
            return null;
        return MJTC_majesticsupportphplib::MJTC_safe_encoding(wp_json_encode(MJTC_majesticsupportphplib::MJTC_safe_encoding($MJTC_id)));
    }

    function generateTicketToken(){
        $match = '';
        $MJTC_count = 0;
        do {
            $MJTC_count++;
            $MJTC_token = "";
            $MJTC_length = wp_rand(9,15);
            $MJTC_possible = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
            // we refer to the length of $MJTC_possible a few times, so let's grab it now
            $MJTC_maxlength = MJTC_majesticsupportphplib::MJTC_strlen($MJTC_possible);
            if ($MJTC_length > $MJTC_maxlength) { // check for length overflow and truncate if necessary
                $MJTC_length = $MJTC_maxlength;
            }
            $MJTC_i = 0;
            // add random characters to $MJTC_password until $MJTC_length is reached
            while ($MJTC_i < $MJTC_length) {
                // pick a random character from the possible ones
                $MJTC_char = MJTC_majesticsupportphplib::MJTC_substr($MJTC_possible, wp_rand(0, $MJTC_maxlength - 1), 1);
                if (!MJTC_majesticsupportphplib::MJTC_strstr($MJTC_token, $MJTC_char)) {
                    if ($MJTC_i == 0) {
                        if (ctype_alpha($MJTC_char)) {
                            $MJTC_token .= $MJTC_char;
                            $MJTC_i++;
                        }
                    } else {
                        $MJTC_token .= $MJTC_char;
                        $MJTC_i++;
                    }
                }
            }
            $MJTC_token = hash("sha256", $MJTC_token);
            
            $MJTC_query = "SELECT count(token) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE token = '".esc_sql($MJTC_token) ."'";
            $MJTC_row = majesticsupport::$_db->get_var($MJTC_query);
            if($MJTC_row > 0)
                $match = 'Y';
            else
                $match = 'N';
        }while ($match == 'Y');

        return $MJTC_token;

    }

    function getUIdById($MJTC_id) {
        if (!is_numeric($MJTC_id))
            return false;
        $MJTC_query = "SELECT uid FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE id = " . esc_sql($MJTC_id);
        $MJTC_ticketuid = majesticsupport::$_db->get_var($MJTC_query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        return $MJTC_ticketuid;
    }

    function getNotificationIdById($MJTC_id) {
        if (!is_numeric($MJTC_id))
            return false;
        $MJTC_query = "SELECT notificationid FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE id = " . esc_sql($MJTC_id);
        $MJTC_notificationid = majesticsupport::$_db->get_var($MJTC_query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        return $MJTC_notificationid;
    }

    function getAdminTicketSearchFormData($MJTC_search_userfields){
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'my-ticket') ) {
            die( 'Security check Failed' );
        }
        $ms_search_array = array();
        $MJTC_search_userfields = MJTC_includer::MJTC_getObjectClass('customfields')->adminFieldsForSearch(1);
        $ms_search_array['subject'] = MJTC_majesticsupportphplib::MJTC_trim(MJTC_request::MJTC_getVar('subject' , ''));
        $ms_search_array['name'] = MJTC_majesticsupportphplib::MJTC_trim(MJTC_request::MJTC_getVar('name' , ''));
        $ms_search_array['email'] = MJTC_majesticsupportphplib::MJTC_trim(MJTC_request::MJTC_getVar('email' , ''));
        $ms_search_array['phone'] = MJTC_majesticsupportphplib::MJTC_trim(MJTC_request::MJTC_getVar('phone' , ''));
        $ms_search_array['ticketid'] = MJTC_majesticsupportphplib::MJTC_trim(MJTC_request::MJTC_getVar('ticketid' , ''));
        $ms_search_array['datestart'] = MJTC_majesticsupportphplib::MJTC_trim(MJTC_request::MJTC_getVar('datestart' , ''));
        $ms_search_array['dateend'] = MJTC_majesticsupportphplib::MJTC_trim(MJTC_request::MJTC_getVar('dateend' , ''));
        $ms_search_array['orderid'] = MJTC_majesticsupportphplib::MJTC_trim(MJTC_request::MJTC_getVar('orderid' , ''));
        $ms_search_array['eddorderid'] = MJTC_majesticsupportphplib::MJTC_trim(MJTC_request::MJTC_getVar('eddorderid', ''));
        $ms_search_array['priority'] = MJTC_majesticsupportphplib::MJTC_trim(MJTC_request::MJTC_getVar('priority' , ''));
        $ms_search_array['departmentid'] = MJTC_majesticsupportphplib::MJTC_trim(MJTC_request::MJTC_getVar('departmentid' , ''));
        $ms_search_array['helptopicid'] = MJTC_majesticsupportphplib::MJTC_trim(MJTC_request::MJTC_getVar('helptopicid' , ''));
        $ms_search_array['productid'] = MJTC_majesticsupportphplib::MJTC_trim(MJTC_request::MJTC_getVar('productid' , ''));
        $ms_search_array['list'] = MJTC_majesticsupportphplib::MJTC_trim(MJTC_request::MJTC_getVar('list', null ,1));
        $ms_search_array['staffid'] = MJTC_majesticsupportphplib::MJTC_trim(MJTC_request::MJTC_getVar('staffid' , ''));
        $ms_search_array['status'] = MJTC_majesticsupportphplib::MJTC_trim(MJTC_request::MJTC_getVar('status' , ''));
        $ms_search_array['sortby'] = MJTC_majesticsupportphplib::MJTC_trim(MJTC_request::MJTC_getVar('sortby' , ''));
        $ms_search_array['search_from_ticket'] = 1;
        if (!empty($MJTC_search_userfields)) {
            foreach ($MJTC_search_userfields as $MJTC_uf) {
                $ms_search_array['ms_ticket_custom_field'][$MJTC_uf->field] = MJTC_request::MJTC_getVar($MJTC_uf->field, 'post');
            }
        }
        return $ms_search_array;
    }

    function getFrontSideTicketSearchFormData($MJTC_search_userfields){
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'my-ticket') ) {
            die( 'Security check Failed' );
        }$ms_search_array = array();
        $MJTC_search_userfields = MJTC_includer::MJTC_getObjectClass('customfields')->userFieldsForSearch(1);
        if(MJTC_request::MJTC_getVar('ms-subject' , '') != ''){
            $ms_search_array['subject'] = MJTC_majesticsupportphplib::MJTC_trim(MJTC_request::MJTC_getVar('ms-subject' , ''));
        } else {
            $ms_search_array['subject'] = '';
        }
        if (MJTC_request::MJTC_getVar('ms-from' , '') != '') {
            $ms_search_array['name'] = MJTC_majesticsupportphplib::MJTC_trim(MJTC_request::MJTC_getVar('ms-from' , ''));
        } else {
            $ms_search_array['name'] = '';
        }
        if (MJTC_request::MJTC_getVar('ms-email' , '') != '') {
            $ms_search_array['email'] = MJTC_majesticsupportphplib::MJTC_trim(MJTC_request::MJTC_getVar('ms-email' , ''));
        } else {
            $ms_search_array['email'] = '';
        }
        if (MJTC_request::MJTC_getVar('ms-phone' , '') != '') {
            $ms_search_array['phone'] = MJTC_majesticsupportphplib::MJTC_trim(MJTC_request::MJTC_getVar('ms-phone' , ''));
        } else {
            $ms_search_array['phone'] = '';
        }
        if (MJTC_request::MJTC_getVar('ms-ticket') != '') {
            $ms_search_array['ticketid'] = MJTC_majesticsupportphplib::MJTC_trim(MJTC_request::MJTC_getVar('ms-ticket' , ''));
        } else {
            $ms_search_array['ticketid'] = '';
        }
        if (MJTC_request::MJTC_getVar('ms-datestart' , '') != '') {
            $ms_search_array['datestart'] = MJTC_majesticsupportphplib::MJTC_trim(MJTC_request::MJTC_getVar('ms-datestart' , ''));
        } else {
            $ms_search_array['datestart'] = '';
        }
        if (MJTC_request::MJTC_getVar('ms-dateend' , '') != '') {
            $ms_search_array['dateend'] = MJTC_majesticsupportphplib::MJTC_trim(MJTC_request::MJTC_getVar('ms-dateend' , ''));
        } else {
            $ms_search_array['dateend'] = '';
        }
        if (MJTC_request::MJTC_getVar('ms-orderid' , '') != '') {
            $ms_search_array['orderid'] = MJTC_majesticsupportphplib::MJTC_trim(MJTC_request::MJTC_getVar('ms-orderid' , ''));
        } else {
            $ms_search_array['orderid'] = '';
        }
        if (MJTC_request::MJTC_getVar('ms-eddorderid' , '') != '') {
            $ms_search_array['eddorderid'] = MJTC_majesticsupportphplib::MJTC_trim(MJTC_request::MJTC_getVar('ms-eddorderid', ''));
        } else {
            $ms_search_array['eddorderid'] = '';
        }
        if (MJTC_request::MJTC_getVar('ms-priorityid' , '') != '') {
            $ms_search_array['priority'] = MJTC_majesticsupportphplib::MJTC_trim(MJTC_request::MJTC_getVar('ms-priorityid' , ''));
        } else {
            $ms_search_array['priority'] = '';
        }
        if (MJTC_request::MJTC_getVar('ms-departmentid' , '') != '') {
            $ms_search_array['departmentid'] = MJTC_majesticsupportphplib::MJTC_trim(MJTC_request::MJTC_getVar('ms-departmentid' , ''));
        } else {
            $ms_search_array['departmentid'] = '';
        }
        if (MJTC_request::MJTC_getVar('ms-helptopicid' , '') != '') {
            $ms_search_array['helptopicid'] = MJTC_majesticsupportphplib::MJTC_trim(MJTC_request::MJTC_getVar('ms-helptopicid' , ''));
        } else {
            $ms_search_array['helptopicid'] = '';
        }
        if (MJTC_request::MJTC_getVar('ms-productid' , '') != '') {
            $ms_search_array['productid'] = MJTC_majesticsupportphplib::MJTC_trim(MJTC_request::MJTC_getVar('ms-productid' , ''));
        } else {
            $ms_search_array['productid'] = '';
        }
        if (MJTC_request::MJTC_getVar('list', null ,1) != '') {
            $ms_search_array['list'] = MJTC_majesticsupportphplib::MJTC_trim(MJTC_request::MJTC_getVar('list', null ,1));
        } else {
            $ms_search_array['list'] = '';
        }
        $ms_search_array['assignedtome'] = MJTC_request::MJTC_getVar('assignedtome', 'post');
        if (MJTC_request::MJTC_getVar('staffid' , '') != '') {
            $ms_search_array['staffid'] = MJTC_majesticsupportphplib::MJTC_trim(MJTC_request::MJTC_getVar('staffid' , ''));
        } else {
            $ms_search_array['staffid'] = '';
        }
        if (MJTC_request::MJTC_getVar('ms-status' , '') != '') {
            $ms_search_array['status'] = MJTC_majesticsupportphplib::MJTC_trim(MJTC_request::MJTC_getVar('ms-status' , ''));
        } else {
            $ms_search_array['status'] = '';
        }
        if (MJTC_request::MJTC_getVar('sortby' , '') != '') {
            $ms_search_array['sortby'] = MJTC_majesticsupportphplib::MJTC_trim(MJTC_request::MJTC_getVar('sortby' , ''));
        } else {
            $ms_search_array['sortby'] = '';
        }
        if (MJTC_request::MJTC_getVar('ms-ticketsearchkeys', 'post') != '') {
            $ms_search_array['ticketkeys'] = MJTC_majesticsupportphplib::MJTC_addslashes(MJTC_majesticsupportphplib::MJTC_trim(MJTC_request::MJTC_getVar('ms-ticketsearchkeys', 'post')));
        } else {
            $ms_search_array['ticketkeys'] = '';
        }
        $ms_search_array['search_from_ticket'] = 1;
        if (!empty($MJTC_search_userfields)) {
            foreach ($MJTC_search_userfields as $MJTC_uf) {
                $ms_search_array['ms_ticket_custom_field'][$MJTC_uf->field] = MJTC_request::MJTC_getVar($MJTC_uf->field, 'post');
            }
        }
        return $ms_search_array;
    }

    function getCookiesSavedSearchDataTicket($MJTC_search_userfields){
        $ms_search_array = array();
        $MJTC_ticket_search_cookie_data = '';
        if(isset($_COOKIE['ms_ticket_search_data'])){
            $MJTC_ticket_search_cookie_data = MJTC_majesticsupportphplib::MJTC_htmlspecialchars($_COOKIE['ms_ticket_search_data']);
            $MJTC_ticket_search_cookie_data = json_decode( MJTC_majesticsupportphplib::MJTC_safe_decoding($MJTC_ticket_search_cookie_data) , true );
        }
        if($MJTC_ticket_search_cookie_data != '' && isset($MJTC_ticket_search_cookie_data['search_from_ticket']) && $MJTC_ticket_search_cookie_data['search_from_ticket'] == 1){
            $ms_search_array['subject'] = $MJTC_ticket_search_cookie_data['subject'];
            $ms_search_array['name'] = $MJTC_ticket_search_cookie_data['name'];
            $ms_search_array['email'] = $MJTC_ticket_search_cookie_data['email'];
            $ms_search_array['phone'] = $MJTC_ticket_search_cookie_data['phone'];
            $ms_search_array['ticketid'] = $MJTC_ticket_search_cookie_data['ticketid'];
            $ms_search_array['datestart'] = $MJTC_ticket_search_cookie_data['datestart'];
            $ms_search_array['dateend'] = $MJTC_ticket_search_cookie_data['dateend'];
            $ms_search_array['orderid'] = $MJTC_ticket_search_cookie_data['orderid'];
            $ms_search_array['eddorderid'] = $MJTC_ticket_search_cookie_data['eddorderid'];
            $ms_search_array['priority'] = $MJTC_ticket_search_cookie_data['priority'];
            $ms_search_array['departmentid'] = $MJTC_ticket_search_cookie_data['departmentid'];
            $ms_search_array['helptopicid'] = $MJTC_ticket_search_cookie_data['helptopicid'];
            $ms_search_array['productid'] = $MJTC_ticket_search_cookie_data['productid'];
            $ms_search_array['staffid'] = $MJTC_ticket_search_cookie_data['staffid'];
            $ms_search_array['status'] = $MJTC_ticket_search_cookie_data['status'];
            $ms_search_array['sortby'] = $MJTC_ticket_search_cookie_data['sortby'];
            $ms_search_array['list'] = $MJTC_ticket_search_cookie_data['list'];
            $ms_search_array['assignedtome'] = isset($MJTC_ticket_search_cookie_data['assignedtome']) ? $MJTC_ticket_search_cookie_data['assignedtome'] : null;
            $ms_search_array['ticketkeys'] = isset($MJTC_ticket_search_cookie_data['ticketkeys']) ? $MJTC_ticket_search_cookie_data['ticketkeys'] : false;
            if (!empty($MJTC_search_userfields)) {
                foreach ($MJTC_search_userfields as $MJTC_uf) {
                    $ms_search_array['ms_ticket_custom_field'][$MJTC_uf->field] = (isset($MJTC_ticket_search_cookie_data['ms_ticket_custom_field'][$MJTC_uf->field]) && $MJTC_ticket_search_cookie_data['ms_ticket_custom_field'][$MJTC_uf->field] != '') ? $MJTC_ticket_search_cookie_data['ms_ticket_custom_field'][$MJTC_uf->field] : null;
                }
            }
        }

        return $ms_search_array;
    }

    function setSearchVariableForTicket($ms_search_array,$MJTC_search_userfields){

        majesticsupport::$_search['ticket']['subject'] = isset($ms_search_array['subject']) ? $ms_search_array['subject'] : null;
        majesticsupport::$_search['ticket']['name'] = isset($ms_search_array['name']) ? $ms_search_array['name'] : null;
        majesticsupport::$_search['ticket']['phone'] = isset($ms_search_array['phone']) ? $ms_search_array['phone'] : null;
        majesticsupport::$_search['ticket']['email'] = isset($ms_search_array['email']) ? $ms_search_array['email'] : null;
        majesticsupport::$_search['ticket']['ticketid'] = isset($ms_search_array['ticketid']) ? $ms_search_array['ticketid'] : null;
        majesticsupport::$_search['ticket']['datestart'] = isset($ms_search_array['datestart']) ? $ms_search_array['datestart'] : null;
        majesticsupport::$_search['ticket']['dateend'] = isset($ms_search_array['dateend']) ? $ms_search_array['dateend'] : null;
        majesticsupport::$_search['ticket']['orderid'] = isset($ms_search_array['orderid']) ? $ms_search_array['orderid'] : null;
        majesticsupport::$_search['ticket']['eddorderid'] = isset($ms_search_array['eddorderid']) ? $ms_search_array['eddorderid'] : null;
        majesticsupport::$_search['ticket']['priority'] = isset($ms_search_array['priority']) ? $ms_search_array['priority'] : null;
        majesticsupport::$_search['ticket']['departmentid'] = isset($ms_search_array['departmentid']) ? $ms_search_array['departmentid'] : null;
        majesticsupport::$_search['ticket']['helptopicid'] = isset($ms_search_array['helptopicid']) ? $ms_search_array['helptopicid'] : null;
        majesticsupport::$_search['ticket']['productid'] = isset($ms_search_array['productid']) ? $ms_search_array['productid'] : null;
        majesticsupport::$_search['ticket']['staffid'] = isset($ms_search_array['staffid']) ? $ms_search_array['staffid'] : null;
        majesticsupport::$_search['ticket']['status'] = isset($ms_search_array['status']) ? $ms_search_array['status'] : null;
        majesticsupport::$_search['ticket']['sortby'] = isset($ms_search_array['sortby']) ? $ms_search_array['sortby'] : null;
        majesticsupport::$_search['ticket']['list'] = isset($ms_search_array['list']) ? $ms_search_array['list'] : 1;
        // frontend
        majesticsupport::$_search['ticket']['assignedtome'] = isset($ms_search_array['assignedtome']) ? $ms_search_array['assignedtome'] : null;
        majesticsupport::$_search['ticket']['ticketkeys'] = isset($ms_search_array['ticketkeys']) ? $ms_search_array['ticketkeys'] : false;
        if (!empty($MJTC_search_userfields)) {
            foreach ($MJTC_search_userfields as $MJTC_uf) {
                majesticsupport::$_search['ms_ticket_custom_field'][$MJTC_uf->field] = isset($ms_search_array['ms_ticket_custom_field'][$MJTC_uf->field]) ? $ms_search_array['ms_ticket_custom_field'][$MJTC_uf->field] : null;
            }
        }
    }
    function checkIsTicketDuplicate($MJTC_subject,$MJTC_email){
        if(empty($MJTC_subject)) return false;
        if(empty($MJTC_email)) return true;

        $MJTC_curdate = date_i18n('Y-m-d H:i:s');
        $MJTC_query = 'SELECT created FROM `' . majesticsupport::$_db->prefix . 'mjtc_support_tickets` WHERE email = "' . esc_sql($MJTC_email) . '" AND subject = "' . esc_sql($MJTC_subject) . '" ORDER BY created DESC LIMIT 1';
        $MJTC_datetime = majesticsupport::$_db->get_var($MJTC_query);
        if($MJTC_datetime){
            $MJTC_diff = MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_curdate) - MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_datetime);
            if($MJTC_diff <= 15){
				return false;
            }
        }
        return true;
    }
    function getDefaultMultiFormId(){
        $MJTC_query = "SHOW TABLES LIKE '%mjtc_support_multiform%'";
        $MJTC_count = majesticsupport::$_db->query($MJTC_query);
        if ($MJTC_count == 1) {
            $MJTC_query = "SELECT * FROM `" . majesticsupport::$_db->prefix . "mjtc_support_multiform` WHERE is_default = 1 ";
            $MJTC_id = majesticsupport::$_db->get_row($MJTC_query);
            if(isset($MJTC_id)) {
                return $MJTC_id->id;
            }
        }
        return 1;
    }

    function MJTC_isFieldRequired(){
        $MJTC_field = MJTC_request::MJTC_getVar('field');
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'is-field-required-'.$MJTC_field) ) {
            // die( 'Security check Failed' );
        }
        $MJTC_query = "SELECT required  FROM " . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering WHERE  field ='".esc_sql($MJTC_field)."'";
        return majesticsupport::$_db->get_var($MJTC_query);
    }

    function getClosedBy($MJTC_id){
        if(!is_numeric($MJTC_id)) return false;
        if ($MJTC_id == 0) {
            $MJTC_closedBy = esc_html(__('System', 'majestic-support'));
        } else if($MJTC_id == -1){
            $MJTC_closedBy = esc_html(__('Guest', 'majestic-support'));
        } else {
            $MJTC_query = "SELECT display_name AS name FROM `" . majesticsupport::$_wpprefixforuser . "mjtc_support_users` WHERE id = " . esc_sql($MJTC_id);
            $MJTC_closedBy = majesticsupport::$_db->get_var($MJTC_query);
        }
        return $MJTC_closedBy;
    }

    function checkAIReplyTicketsBySubject() {
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (!wp_verify_nonce($MJTC_nonce, 'check-smart-reply')) {
            die('Security check Failed');
        }

        $MJTC_id = absint(MJTC_request::MJTC_getVar('ticketId'));
        $MJTC_subject = sanitize_text_field(MJTC_request::MJTC_getVar('ticketSubject'));
        // Set a limit for smart replies, similar to how you had it in your separate query
        $MJTC_limit = 5; // You can adjust this limit as needed

        $MJTC_agentquery = "";
        if (in_array('agent', majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) {
            $MJTC_allowed = MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForTask('Limit AI Replies to Agent-Assigned Tickets');
            if ($MJTC_allowed) {
                $MJTC_staffid = absint(MJTC_includer::MJTC_getModel('agent')->getStaffId(MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid()));
                $MJTC_agentquery = " AND (t.staffid = " . esc_sql($MJTC_staffid) . " OR t.departmentid IN (
                    SELECT dept.departmentid
                    FROM `" . majesticsupport::$_db->prefix . "mjtc_support_acl_user_access_departments` AS dept
                    WHERE dept.staffid = " . esc_sql($MJTC_staffid) . ")) ";
            }
        }

        $min_relevance = 1.5; // Minimum relevance score to consider for tickets
        $min_relevance = 0; // Minimum relevance score to consider for tickets

        // Get current ticket's message (for reply-based matching)
        $MJTC_query = "
            SELECT ticket.message, ticket.uid
            FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket
            WHERE ticket.id = " . esc_sql($MJTC_id);
        $MJTC_ticket_data = majesticsupport::$_db->get_row($MJTC_query);

        if (!$MJTC_ticket_data) return json_encode([]);
        
        $MJTC_message = wp_strip_all_tags($MJTC_ticket_data->message);

        // Break the subject and message into words for partial matching
        $MJTC_subject_words = array_filter(MJTC_majesticsupportphplib::MJTC_explode(' ', MJTC_majesticsupportphplib::MJTC_trim($MJTC_subject)));
        $MJTC_subject_word_count = count($MJTC_subject_words);

        // Weighted scoring query for tickets with exact match detection
        $MJTC_query_tickets = "
            SELECT
                t_scores.id,
                t_scores.ticketid,
                t_scores.subject,
                t_scores.message,
                t_scores.created,
                t_scores.subject_score,
                t_scores.message_score,
                (t_scores.subject_score + t_scores.message_score) AS total_relevance,
                t_scores.is_exact_subject_match,
                t_scores.is_exact_message_match
            FROM (
                SELECT
                    t.id,
                    t.ticketid,
                    t.subject,
                    t.message,
                    t.created,
                    3 * IFNULL(MATCH(t.subject) AGAINST('" . esc_sql($MJTC_subject) . "' IN NATURAL LANGUAGE MODE), 0) AS subject_score,
                    1 * IFNULL(MATCH(t.message) AGAINST('" . esc_sql($MJTC_message) . "' IN NATURAL LANGUAGE MODE), 0) AS message_score,
                    t.subject LIKE '%" . esc_sql($MJTC_subject) . "%' AS is_exact_subject_match,
                    t.message LIKE '%" . esc_sql($MJTC_message) . "%' AS is_exact_message_match
                FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` t
                WHERE t.id != " . esc_sql($MJTC_id) . "
                " . $MJTC_agentquery . "
            ) AS t_scores
            HAVING total_relevance > " . esc_sql($min_relevance) . "
            ORDER BY total_relevance DESC LIMIT 50";

        $MJTC_tickets = majesticsupport::$_db->get_results($MJTC_query_tickets);

        // Query for Smart Replies
        $MJTC_query_smart_replies = 'SELECT id,title,reply, MATCH (ticketsubjects) AGAINST ("' . esc_sql($MJTC_subject) . '" IN NATURAL LANGUAGE MODE) AS relevance
                                FROM `' . majesticsupport::$_db->prefix . 'mjtc_support_smartreplies`
                                WHERE MATCH (ticketsubjects) AGAINST("' . esc_sql($MJTC_subject) . '" IN NATURAL LANGUAGE MODE)
                                HAVING relevance > ' . esc_sql($min_relevance) . '
                                ORDER BY relevance DESC LIMIT ' . esc_sql($MJTC_limit) . ';';

        $MJTC_smart_replies = majesticsupport::$_db->get_results($MJTC_query_smart_replies);

        // Prepare combined results array
        $MJTC_combined_results = [];

        // Process Tickets
        if (!empty($MJTC_tickets)) {
            // Compute custom_score and find max for tickets
            $MJTC_highest_score_tickets = 0;
            foreach ($MJTC_tickets as &$MJTC_ticket) {
                $MJTC_custom_score = 0;

                // Exact matches get highest priority
                if ($MJTC_ticket->is_exact_subject_match) {
                    $MJTC_custom_score += ($MJTC_subject_word_count * 10) + 4;
                } elseif ($MJTC_ticket->is_exact_message_match) {
                    $MJTC_custom_score += ($MJTC_subject_word_count * 10) + 0;
                } elseif ($MJTC_subject_word_count > 1) {
                    // Partial word combination matching in subject
                    for ($MJTC_i = 0; $MJTC_i < $MJTC_subject_word_count - 1; $MJTC_i++) {
                        $MJTC_wordCombination = $MJTC_subject_words[$MJTC_i] . ' ' . ($MJTC_subject_words[$MJTC_i + 1] ?? '');
                        if (stripos($MJTC_ticket->subject, $MJTC_wordCombination) !== false) {
                            $MJTC_custom_score += 10;
                        }
                    }
                }

                $MJTC_ticket->custom_score = $MJTC_custom_score;
                if ($MJTC_ticket->custom_score > $MJTC_highest_score_tickets) {
                    $MJTC_highest_score_tickets = $MJTC_ticket->custom_score;
                }

                // Add 'type' to distinguish from smart replies
                $MJTC_ticket->type = 'ticket';
            }
            unset($MJTC_ticket); // Unset reference

            // Add tickets to combined results
            $MJTC_combined_results = array_merge($MJTC_combined_results, $MJTC_tickets);
        }

        // Process Smart Replies
        if (!empty($MJTC_smart_replies)) {
            foreach ($MJTC_smart_replies as &$MJTC_reply) {
                // Assign scores for smart replies to integrate them into the sorting logic
                // You'll need to decide on a scoring strategy for smart replies.
                // For example, a high custom_score for exact subject matches,
                // or a scaled relevance from the smart reply query.
                $MJTC_custom_score_reply = 0;
                if (stripos($MJTC_reply->title, $MJTC_subject) !== false) { // Check for exact subject match in smart reply title
                    $MJTC_custom_score_reply += ($MJTC_subject_word_count * 10) + 10; // Give a very high score for direct subject match
                } elseif ($MJTC_subject_word_count > 1) {
                    for ($MJTC_i = 0; $MJTC_i < $MJTC_subject_word_count - 1; $MJTC_i++) {
                        $MJTC_wordCombination = $MJTC_subject_words[$MJTC_i] . ' ' . ($MJTC_subject_words[$MJTC_i + 1] ?? '');
                        if (stripos($MJTC_reply->title, $MJTC_wordCombination) !== false) {
                            $MJTC_custom_score_reply += 10;
                        }
                    }
                }

                $MJTC_reply->custom_score = $MJTC_custom_score_reply;

                // Add 'type' to distinguish from tickets
                $MJTC_reply->type = 'smart_reply';
                $MJTC_reply->message = $MJTC_reply->reply; // Align property names for unified processing
            }
            unset($MJTC_reply); // Unset reference

            // Add smart replies to combined results
            $MJTC_combined_results = array_merge($MJTC_combined_results, $MJTC_smart_replies);
        }

        // Now, sort the combined results
        if (!empty($MJTC_combined_results)) {
            // Find the highest scores across both types of results
            $MJTC_highest_score_combined = 0;
            $MJTC_highest_total_relevance_combined = 0;
            foreach ($MJTC_combined_results as $MJTC_item) {
                if (isset($MJTC_item->custom_score) && $MJTC_item->custom_score > $MJTC_highest_score_combined) {
                    $MJTC_highest_score_combined = $MJTC_item->custom_score;
                }
                if (isset($MJTC_item->total_relevance) && $MJTC_item->total_relevance > $MJTC_highest_total_relevance_combined) {
                    $MJTC_highest_total_relevance_combined = $MJTC_item->total_relevance;
                }
            }

            // Sort combined results by custom_score and then total_relevance
            usort($MJTC_combined_results, function ($MJTC_a, $MJTC_b) {
                // Prioritize items with custom_score if they have it
                $MJTC_a_custom_score = $MJTC_a->custom_score ?? 0;
                $MJTC_b_custom_score = $MJTC_b->custom_score ?? 0;

                if ($MJTC_a_custom_score === $MJTC_b_custom_score) {
                    // If custom scores are equal, compare total_relevance (for tickets) or relevance (for smart replies)
                    $MJTC_a_relevance = $MJTC_a->total_relevance ?? ($MJTC_a->relevance ?? 0);
                    $MJTC_b_relevance = $MJTC_b->total_relevance ?? ($MJTC_b->relevance ?? 0);
                    return $MJTC_b_relevance <=> $MJTC_a_relevance;
                }
                return $MJTC_b_custom_score <=> $MJTC_a_custom_score;
            });

            // Apply threshold filtering to combined results
            $MJTC_filtered_final_results = [];
            $threshold_percentage = 30; // 30% threshold
            // Calculate threshold values only if highest_custom_score is not zero to avoid division by zero
            $MJTC_custom_score_threshold_value = ($MJTC_highest_score_combined > 0) ? ($threshold_percentage / 100) * $MJTC_highest_score_combined : 0;
            $total_relevance_threshold_value = ($MJTC_highest_total_relevance_combined > 0) ? ($threshold_percentage / 100) * $MJTC_highest_total_relevance_combined : 0;

            foreach ($MJTC_combined_results as $MJTC_index => $MJTC_item) {
                // Always keep the top result after sorting
                if ($MJTC_index === 0) {
                    // Use a unique identifier to avoid duplicates, e.g., type_id
                    $MJTC_unique_id = $MJTC_item->type . '_' . $MJTC_item->id;
                    $MJTC_filtered_final_results[$MJTC_unique_id] = $MJTC_item;
                    continue;
                }

                $MJTC_item_custom_score = $MJTC_item->custom_score ?? 0;
                $MJTC_item_total_relevance = $MJTC_item->total_relevance ?? ($MJTC_item->relevance ?? 0); // Use relevance for smart replies if total_relevance isn't set

                // Condition 1: Check if custom_score is above its threshold
                $MJTC_is_custom_score_above_threshold = ($MJTC_item_custom_score > 0 && $MJTC_item_custom_score >= $MJTC_custom_score_threshold_value);

                // Condition 2: Check if total_relevance (or relevance for smart replies) is above its threshold
                $MJTC_is_total_relevance_above_threshold = $MJTC_item_total_relevance >= $total_relevance_threshold_value;

                // Condition 3: Handle cases where both scores are very low (similar to original code)
                // If custom_score is 0, total_relevance must meet the minimum relevance.
                // This prevents purely NLP-driven low-relevance results if no custom score is found.
                $MJTC_is_scores_too_low = ($MJTC_item_custom_score == 0 && $MJTC_item_total_relevance < $min_relevance); // Use min_relevance for both for consistency, adjust as needed

                if ($MJTC_is_scores_too_low) {
                    continue;
                }

                $MJTC_unique_id = $MJTC_item->type . '_' . $MJTC_item->id;
                if ($MJTC_is_custom_score_above_threshold || $MJTC_is_total_relevance_above_threshold) {
                    // Ensure uniqueness by type and ID, keeping the highest custom_score and then the highest total_relevance/relevance
                    if (
                        !isset($MJTC_filtered_final_results[$MJTC_unique_id]) ||
                        ($MJTC_item_custom_score > ($MJTC_filtered_final_results[$MJTC_unique_id]->custom_score ?? 0)) ||
                        (($MJTC_item_custom_score === ($MJTC_filtered_final_results[$MJTC_unique_id]->custom_score ?? 0)) && $MJTC_item_total_relevance > ($MJTC_filtered_final_results[$MJTC_unique_id]->total_relevance ?? ($MJTC_filtered_final_results[$MJTC_unique_id]->relevance ?? 0)))
                    ) {
                        $MJTC_filtered_final_results[$MJTC_unique_id] = $MJTC_item;
                    }
                }
            }
            $MJTC_combined_results = array_values($MJTC_filtered_final_results);
        } else {
            $MJTC_combined_results = [];
        }


        // Final result formatting
        $MJTC_results = [];
        foreach ($MJTC_combined_results as $MJTC_item) {
            $MJTC_data = [
                'id' => $MJTC_item->id,
                'text' => $MJTC_item->type === 'ticket' ? $MJTC_item->subject : $MJTC_item->title, // Use subject for tickets, title for smart replies
                'message' => wp_strip_all_tags($MJTC_item->message),
                'relevance' => $MJTC_item->total_relevance ?? $MJTC_item->relevance, // Use total_relevance for tickets, relevance for smart replies
                'custom_score' => $MJTC_item->custom_score ?? 0,
                'type' => $MJTC_item->type // Indicate if it's a ticket or a smart reply
            ];

            // Add ticket-specific properties if applicable
            if ($MJTC_item->type === 'ticket') {
                $MJTC_data['ticketid'] = $MJTC_item->ticketid;
            }

            $MJTC_results[] = $MJTC_data;
        }

        return json_encode($MJTC_results);
    }

    function checkForTicketOwner($MJTC_id) {
        if (!is_numeric($MJTC_id))
            return false;
        //ignore if admin or agent or visitor
        if(!MJTC_includer::MJTC_getObjectClass('user')->MJTC_isguest() && !current_user_can('manage_options') && !(in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff())){
            $MJTC_ticketUid = MJTC_includer::MJTC_getModel('ticket')->getUIdById($MJTC_id);
            $MJTC_currentuserid = MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid();
            if ($MJTC_currentuserid != $MJTC_ticketUid){
                return false;
            }
        }
        return true;
    }	

    function validateTicketAction($MJTC_ticketid, $MJTC_internalid) {
        if (!is_numeric($MJTC_ticketid)){
            return false;
        }
        //ignore if admin or agent
        if(!current_user_can('manage_options') && !(in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff())){
            $MJTC_query = "SELECT id,internalid FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE id = " . esc_sql($MJTC_ticketid);
            $MJTC_ticketData = majesticsupport::$_db->get_row($MJTC_query);
            if (!isset($MJTC_internalid) || $MJTC_internalid == ''){
                if ($MJTC_ticketData->internalid != '') {
                    // if ticket have internalid but miss in the data
                    return false;
                }
            }
            if ($MJTC_ticketData->id != $MJTC_ticketid || $MJTC_ticketData->internalid != $MJTC_internalid) {
                // if ticket stored data is not match with the sent data
                return false;
            }
        }
        if (!$this->checkForTicketOwner($MJTC_ticketid)) {
            // if the current user is not ticket owner
            return false;
        }
        return true;
    }   

    function getHtmlForAssignPopup() {
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'get-html-for-assign-popup') ) {
            die( 'Security check Failed' );
        }
        $ticketid = MJTC_request::MJTC_getVar("ticketid");
        $MJTC_staffid = MJTC_request::MJTC_getVar("staffid");
        $MJTC_selectedIds = MJTC_request::MJTC_getVar("selectedIds");
        $MJTC_ticketlisting = MJTC_request::MJTC_getVar("ticketlisting");
        if (!empty($MJTC_selectedIds) && empty($MJTC_staffid) && empty($ticketid)) {
            $MJTC_selectedIds = implode(',', $MJTC_selectedIds);
            $task = 'assignmultipletickettostaff';
        } else {
            $task = 'assigntickettostaff';
        }
        $MJTC_html = '
        <form class="mjtc-det-tkt-form" method="post" action="'. esc_url(wp_nonce_url(admin_url("admin.php?page=majesticsupport_ticket&task=$task"),"assign-ticket-to-staff-".$ticketid)).'"  enctype="multipart/form-data">
            <div class="mjtc-form-wrapper">
                <div class="mjtc-form-title">'. esc_html(__('Agent', 'majestic-support')) .'</div>
                <div class="mjtc-form-value">
                    '. wp_kses(MJTC_formfield::MJTC_select('staffid', MJTC_includer::MJTC_getModel('agent')->getstaffForCombobox(), $MJTC_staffid, esc_html(__('Select Agent', 'majestic-support')), array('class' => 'inputbox mjtc-admin-popup-select-field','required' => true)), MJTC_ALLOWED_TAGS) .'
                </div>
            </div>';

            if(in_array('note', majesticsupport::$_active_addons)){
            $MJTC_html .= '
                <div class="mjtc-form-wrapper">
                    <div class="mjtc-form-title"><label id="responcemsg" for="responce">'. esc_html(__('Internal Note', 'majestic-support')) .'</label></div>
                    <div class="mjtc-form-value">';
            
            // --- FIX STARTS HERE ---
            ob_start(); // Start catching output
            wp_editor("", "assignnote", array("media_buttons" => false, "textarea_rows" => 5));
            $MJTC_editor_html = ob_get_clean(); // Save the caught HTML to a variable
            // --- FIX ENDS HERE ---

            $MJTC_html .= $MJTC_editor_html . '</div>
                </div>';
            }

            $MJTC_html .= '
            <div class="mjtc-form-button">
                '. wp_kses(MJTC_formfield::MJTC_submitbutton('assigntostaff', esc_html(__('Assign','majestic-support')), array('class' => 'button mjtc-admin-pop-btn-block', 'onclick' => "return checktinymcebyid('assignnote');")), MJTC_ALLOWED_TAGS) .'
            </div>
            '. wp_kses(MJTC_formfield::MJTC_hidden('ticketlisting', $MJTC_ticketlisting), MJTC_ALLOWED_TAGS).'
            '. wp_kses(MJTC_formfield::MJTC_hidden('ticketid', $ticketid), MJTC_ALLOWED_TAGS).'
            '. wp_kses(MJTC_formfield::MJTC_hidden('uid', MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid()), MJTC_ALLOWED_TAGS).'
            '. wp_kses(MJTC_formfield::MJTC_hidden('ticketIds', $MJTC_selectedIds), MJTC_ALLOWED_TAGS).'
            '. wp_kses(MJTC_formfield::MJTC_hidden('action', 'ticket_assigntickettostaff'), MJTC_ALLOWED_TAGS).'
            '. wp_kses(MJTC_formfield::MJTC_hidden('form_request', 'majesticsupport'), MJTC_ALLOWED_TAGS).'
        </form>';
        $MJTC_html = MJTC_majesticsupportphplib::MJTC_htmlentities($MJTC_html);
        return wp_json_encode($MJTC_html);
    }
}
?>
