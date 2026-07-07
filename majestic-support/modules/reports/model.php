<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_reportsModel {

    function getOverallReportData(){

        //Overall Data by status
        $MJTC_query = "SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` ";
        $MJTC_allticket = majesticsupport::$_db->get_var($MJTC_query);

        $MJTC_query = "SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE status != 5 AND status != 6";
        $MJTC_openticket = majesticsupport::$_db->get_var($MJTC_query);

        $MJTC_query = "SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE (status = 5 OR status = 6)";
        $MJTC_closeticket = majesticsupport::$_db->get_var($MJTC_query);

        $MJTC_query = "SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE isanswered = 1 AND status != 5 AND status != 6 AND status != 1";
        $MJTC_answeredticket = majesticsupport::$_db->get_var($MJTC_query);

        $MJTC_query = "SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE isoverdue = 1 AND status != 5";
        $MJTC_overdueticket = majesticsupport::$_db->get_var($MJTC_query);

        $MJTC_query = "SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE isanswered != 1 AND status != 5 AND (lastreply != '0000-00-00 00:00:00')";
        $MJTC_pendingticket = majesticsupport::$_db->get_var($MJTC_query);

        majesticsupport::$_data['ticket_total']['allticket'] = $MJTC_allticket;
        majesticsupport::$_data['ticket_total']['openticket'] = $MJTC_openticket;
        majesticsupport::$_data['ticket_total']['closeticket'] = $MJTC_closeticket;
        majesticsupport::$_data['ticket_total']['answeredticket'] = $MJTC_answeredticket;
        majesticsupport::$_data['ticket_total']['overdueticket'] = $MJTC_overdueticket;
        majesticsupport::$_data['ticket_total']['pendingticket'] = $MJTC_pendingticket;

        majesticsupport::$_data['status_chart'] = "['". esc_html(__('New','majestic-support'))."',$MJTC_openticket],['". esc_html(__('Answered','majestic-support'))."',$MJTC_answeredticket],['". esc_html(__('Overdue','majestic-support'))."',$MJTC_overdueticket],['". esc_html(__('Pending','majestic-support'))."',$MJTC_pendingticket]";
        $total = $MJTC_openticket + $MJTC_closeticket + $MJTC_answeredticket + $MJTC_overdueticket + $MJTC_pendingticket;
        majesticsupport::$_data['bar_chart'] = "
        ['". esc_html(__('New','majestic-support'))."',$MJTC_openticket,'#FF9900'],
        ['". esc_html(__('Answered','majestic-support'))."',$MJTC_answeredticket,'#2168A2'],
        ['". esc_html(__('Closed','majestic-support'))."',$MJTC_closeticket,'#3D355A'],
        ['". esc_html(__('Pending','majestic-support'))."',$MJTC_pendingticket,'#f39f10'],
        ['". esc_html(__('Overdue','majestic-support'))."',$MJTC_overdueticket,'#B82B2B']
        ";

        $MJTC_query = "SELECT dept.departmentname,(SELECT COUNT(id) FROM `".majesticsupport::$_db->prefix."mjtc_support_tickets` WHERE departmentid = dept.id) AS totalticket
                    FROM `".majesticsupport::$_db->prefix."mjtc_support_departments` AS dept";
        $MJTC_department = majesticsupport::$_db->get_results($MJTC_query);
        majesticsupport::$_data['pie3d_chart1'] = "";
        foreach($MJTC_department AS $MJTC_dept){
            majesticsupport::$_data['pie3d_chart1'] .= "['".majesticsupport::MJTC_getVarValue($MJTC_dept->departmentname)."',$MJTC_dept->totalticket],";
        }

        $MJTC_query = "SELECT priority.priority,(SELECT COUNT(id) FROM `".majesticsupport::$_db->prefix."mjtc_support_tickets` WHERE priorityid = priority.id) AS totalticket
                    FROM `".majesticsupport::$_db->prefix."mjtc_support_priorities` AS priority ORDER BY priority.priority";
        $MJTC_department = majesticsupport::$_db->get_results($MJTC_query);
        majesticsupport::$_data['pie3d_chart2'] = "";
        foreach($MJTC_department AS $MJTC_dept){
            majesticsupport::$_data['pie3d_chart2'] .= "['".majesticsupport::MJTC_getVarValue($MJTC_dept->priority)."',$MJTC_dept->totalticket],";
        }
        if(in_array('emailpiping', majesticsupport::$_active_addons)){
            $MJTC_query = "SELECT COUNT(id) FROM `".majesticsupport::$_db->prefix."mjtc_support_tickets` WHERE ticketviaemail = 1";
            $MJTC_ticketviaemail = majesticsupport::$_db->get_var($MJTC_query);
            $MJTC_query = "SELECT COUNT(id) FROM `".majesticsupport::$_db->prefix."mjtc_support_replies` WHERE ticketviaemail = 1";
            $MJTC_replyviaemail = majesticsupport::$_db->get_var($MJTC_query);
        }else{
            $MJTC_ticketviaemail = '';
            $MJTC_replyviaemail = '';
        }
        $MJTC_query = "SELECT COUNT(id) FROM `".majesticsupport::$_db->prefix."mjtc_support_tickets` WHERE ticketviaemail = 0";
        $MJTC_directticket = majesticsupport::$_db->get_var($MJTC_query);
        $MJTC_query = "SELECT COUNT(id) FROM `".majesticsupport::$_db->prefix."mjtc_support_replies` WHERE ticketviaemail = 0";
        $MJTC_directreply = majesticsupport::$_db->get_var($MJTC_query);

        majesticsupport::$_data['stack_data'] = "['". esc_html(__('Tickets','majestic-support'))."',$MJTC_directticket,$MJTC_ticketviaemail,''],['". esc_html(__('Replies','majestic-support'))."',$MJTC_directreply,$MJTC_replyviaemail,'']";

        $MJTC_query = "SELECT priority.priority,(SELECT COUNT(id) FROM `".majesticsupport::$_db->prefix."mjtc_support_tickets` WHERE priorityid = priority.id AND status = 1 AND (lastreply = '0000-00-00 00:00:00') ) AS totalticket
                    FROM `".majesticsupport::$_db->prefix."mjtc_support_priorities` AS priority ORDER BY priority.priority";
        $MJTC_openticket_pr = majesticsupport::$_db->get_results($MJTC_query);
        $MJTC_query = "SELECT priority.priority,(SELECT COUNT(id) FROM `".majesticsupport::$_db->prefix."mjtc_support_tickets` WHERE priorityid = priority.id AND isanswered = 1 AND status != 5 AND status != 1 ) AS totalticket
                    FROM `".majesticsupport::$_db->prefix."mjtc_support_priorities` AS priority ORDER BY priority.priority";
        $MJTC_answeredticket_pr = majesticsupport::$_db->get_results($MJTC_query);
        $MJTC_query = "SELECT priority.priority,(SELECT COUNT(id) FROM `".majesticsupport::$_db->prefix."mjtc_support_tickets` WHERE priorityid = priority.id AND isoverdue = 1 AND status != 5 ) AS totalticket
                    FROM `".majesticsupport::$_db->prefix."mjtc_support_priorities` AS priority ORDER BY priority.priority";
        $MJTC_overdueticket_pr = majesticsupport::$_db->get_results($MJTC_query);
        $MJTC_query = "SELECT priority.priority,(SELECT COUNT(id) FROM `".majesticsupport::$_db->prefix."mjtc_support_tickets` WHERE priorityid = priority.id AND isanswered != 1 AND status != 5 AND (lastreply != '0000-00-00 00:00:00') ) AS totalticket
                    FROM `".majesticsupport::$_db->prefix."mjtc_support_priorities` AS priority ORDER BY priority.priority";
        $MJTC_pendingticket_pr = majesticsupport::$_db->get_results($MJTC_query);
        majesticsupport::$_data['stack_chart_horizontal']['title'] = "['". esc_html(__('Priority','majestic-support'))."','". esc_html(__('Overdue','majestic-support'))."','". esc_html(__('Pending','majestic-support'))."','". esc_html(__('Answered','majestic-support'))."','". esc_html(__('New','majestic-support'))."']";
        majesticsupport::$_data['stack_chart_horizontal']['data'] = "";

        foreach($MJTC_overdueticket_pr AS $MJTC_index => $MJTC_pr){
            majesticsupport::$_data['stack_chart_horizontal']['data'] .= "[";
            majesticsupport::$_data['stack_chart_horizontal']['data'] .= "'".majesticsupport::MJTC_getVarValue($MJTC_pr->priority)."',";
            majesticsupport::$_data['stack_chart_horizontal']['data'] .= $MJTC_overdueticket_pr[$MJTC_index]->totalticket.",";
            majesticsupport::$_data['stack_chart_horizontal']['data'] .= $MJTC_pendingticket_pr[$MJTC_index]->totalticket.",";
            majesticsupport::$_data['stack_chart_horizontal']['data'] .= $MJTC_answeredticket_pr[$MJTC_index]->totalticket.",";
            majesticsupport::$_data['stack_chart_horizontal']['data'] .= $MJTC_openticket_pr[$MJTC_index]->totalticket.",";
            majesticsupport::$_data['stack_chart_horizontal']['data'] .= "],";
        }

        if(in_array('agent',majesticsupport::$_active_addons)){
            $MJTC_query = "SELECT staff.firstname,staff.lastname,(SELECT COUNT(id) FROM `".majesticsupport::$_db->prefix."mjtc_support_tickets` WHERE staffid = staff.id) AS totalticket
                        FROM `".majesticsupport::$_db->prefix."mjtc_support_staff` AS staff";
            $MJTC_agenttickets = majesticsupport::$_db->get_results($MJTC_query);
            majesticsupport::$_data['slice_chart'] = '';
            if(!empty($MJTC_agenttickets))
            foreach($MJTC_agenttickets AS $MJTC_ticket){
                $MJTC_agentname = $MJTC_ticket->firstname;
                if(!empty($MJTC_ticket->lastname)){
                    $MJTC_agentname .= ' '.$MJTC_ticket->lastname;
                }
                majesticsupport::$_data['slice_chart'] .= "['".$MJTC_agentname."',$MJTC_ticket->totalticket],";
            }
        }

        //To show priority colors on chart
        $MJTC_query = "SELECT prioritycolour FROM `".majesticsupport::$_db->prefix."mjtc_support_priorities` ORDER BY priority ";
        $MJTC_jsonColorList = "[";
        foreach(majesticsupport::$_db->get_results($MJTC_query) as $MJTC_priority){
            $MJTC_jsonColorList.= "'".$MJTC_priority->prioritycolour."',";
        }
        $MJTC_jsonColorList .= "]";
        majesticsupport::$_data['priorityColorList'] = $MJTC_jsonColorList;
        //end priority colors
    }

    function getDepartmentReportsFE(){
        if( !in_array('agent',majesticsupport::$_active_addons) ){
            return;
        }
        $MJTC_uid = MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid();
        $MJTC_staffid = MJTC_includer::MJTC_getModel('agent')->getStaffId($MJTC_uid);
        $MJTC_query = "SELECT dept.departmentname,(SELECT COUNT(id) FROM `".majesticsupport::$_db->prefix."mjtc_support_tickets` WHERE departmentid = dept.id ) AS totalticket
            FROM `".majesticsupport::$_db->prefix."mjtc_support_departments` AS dept
            JOIN `".majesticsupport::$_db->prefix."mjtc_support_acl_user_access_departments` AS acl ON acl.departmentid = dept.id
            WHERE acl.staffid = ".absint($MJTC_staffid)." AND dept.status=1";

        $MJTC_department = majesticsupport::$_db->get_results($MJTC_query);
        majesticsupport::$_data['pie3d_chart1'] = "";
        $MJTC_i = 0;
        foreach($MJTC_department AS $MJTC_dept){
            if($MJTC_dept->totalticket == 0)
                $MJTC_i += 1;
            majesticsupport::$_data['pie3d_chart1'] .= "['".majesticsupport::MJTC_getVarValue($MJTC_dept->departmentname)."',$MJTC_dept->totalticket],";
        }

        if(count($MJTC_department) == $MJTC_i)
            majesticsupport::$_data['pie3d_chart1'] = '';

        // pagination
        $MJTC_query = "SELECT count(dept.id)
            FROM `".majesticsupport::$_db->prefix."mjtc_support_departments` AS dept
            JOIN `".majesticsupport::$_db->prefix."mjtc_support_acl_user_access_departments` AS acl ON acl.departmentid = dept.id
            WHERE acl.staffid = ".absint($MJTC_staffid)." AND dept.status=1";
        $total = majesticsupport::$_db->get_var($MJTC_query);
        majesticsupport::$_data['total'] = $total;
        majesticsupport::$_data[1] = MJTC_pagination::MJTC_getPagination($total);

        $MJTC_query = "SELECT dept.departmentname,
            (SELECT COUNT(id) FROM `".majesticsupport::$_db->prefix."mjtc_support_tickets` WHERE status = 1 AND (lastreply = '0000-00-00 00:00:00') AND departmentid = dept.id) AS openticket,
            (SELECT COUNT(id) FROM `".majesticsupport::$_db->prefix."mjtc_support_tickets` WHERE status = 5 AND departmentid = dept.id) AS closeticket,
            (SELECT COUNT(id) FROM `".majesticsupport::$_db->prefix."mjtc_support_tickets` WHERE isanswered = 1 AND status != 5 AND status != 1 AND departmentid = dept.id) AS answeredticket,
            (SELECT COUNT(id) FROM `".majesticsupport::$_db->prefix."mjtc_support_tickets` WHERE isoverdue = 1 AND status != 5 AND departmentid = dept.id) AS overdueticket,
            (SELECT COUNT(id) FROM `".majesticsupport::$_db->prefix."mjtc_support_tickets` WHERE isanswered != 1 AND status != 5 AND (lastreply != '0000-00-00 00:00:00') AND departmentid = dept.id) AS pendingticket
            FROM `".majesticsupport::$_db->prefix."mjtc_support_departments` AS dept
            JOIN `".majesticsupport::$_db->prefix."mjtc_support_acl_user_access_departments` AS acl ON acl.departmentid = dept.id
            WHERE acl.staffid = ".absint($MJTC_staffid)." AND dept.status=1";
        $MJTC_query .= " LIMIT " . MJTC_pagination::MJTC_getOffset() . ", " . MJTC_pagination::MJTC_getLimit();
        $MJTC_departments = majesticsupport::$_db->get_results($MJTC_query);
        majesticsupport::$_data['departments_report'] = $MJTC_departments;

        return;
    }

    function getStaffReports(){
        if( !in_array('agent',majesticsupport::$_active_addons) ){
            return;
        }
        $MJTC_date_start = isset(majesticsupport::$_search['report']) ? majesticsupport::$_search['report']['date_start'] : '';
        $MJTC_date_end = isset(majesticsupport::$_search['report']) ? majesticsupport::$_search['report']['date_end'] : '';

        if(isset($MJTC_date_start) && $MJTC_date_start != ""){
            $MJTC_date_start = date_i18n('Y-m-d',MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_date_start));
        }
        if(isset($MJTC_date_end) && $MJTC_date_end != ""){
            $MJTC_date_end = date_i18n('Y-m-d',MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_date_end));
        }
        if($MJTC_date_start > $MJTC_date_end){
            $tmp = $MJTC_date_start;
            $MJTC_date_start = $MJTC_date_end;
            $MJTC_date_end = $tmp;
        }

        $MJTC_uid = isset(majesticsupport::$_search['report']) ? majesticsupport::$_search['report']['uid'] : '';

        //Line Chart Data
        $MJTC_curdate = ($MJTC_date_start != null) ? date_i18n('Y-m-d',MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_date_start)) : date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime("now -1 month"));
        $MJTC_dates = '';
        $MJTC_fromdate = ($MJTC_date_end != null) ? date_i18n('Y-m-d',MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_date_end)) : date_i18n('Y-m-d');
        majesticsupport::$_data['filter']['date_start'] = $MJTC_curdate;
        majesticsupport::$_data['filter']['date_end'] = $MJTC_fromdate;
        majesticsupport::$_data['filter']['uid'] = $MJTC_uid;
        // forexport
        $_SESSION['forexport']['curdate'] = $MJTC_curdate;
        $_SESSION['forexport']['fromdate'] = $MJTC_fromdate;
        $_SESSION['forexport']['uid'] = $MJTC_uid;

        $MJTC_staffid = MJTC_includer::MJTC_getModel('agent')->getStaffId($MJTC_uid);
        majesticsupport::$_data['filter']['staffname'] = MJTC_includer::MJTC_getModel('agent')->getMyName($MJTC_staffid);
        $MJTC_nextdate = $MJTC_fromdate;
        //Query to get Data
        $MJTC_query = "SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` ";
        if($MJTC_uid) $MJTC_query .= " WHERE staffid = ".absint($MJTC_staffid);
        $MJTC_allticket = majesticsupport::$_db->get_var($MJTC_query);

        $MJTC_query = "SELECT created FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE status = 1 AND (lastreply = '0000-00-00 00:00:00') AND date(created) >= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_curdate) . "' AND date(created) <= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_fromdate) . "'";
        if($MJTC_uid) $MJTC_query .= " AND staffid = ".absint($MJTC_staffid);
        $MJTC_openticket = majesticsupport::$_db->get_results($MJTC_query);

        $MJTC_query = "SELECT created FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE status = 5 AND date(created) >= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_curdate) . "' AND date(created) <= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_fromdate) . "'";
        if($MJTC_uid) $MJTC_query .= " AND staffid = ".absint($MJTC_staffid);
        $MJTC_closeticket = majesticsupport::$_db->get_results($MJTC_query);

        $MJTC_query = "SELECT created FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE isanswered = 1 AND status != 5 AND status != 1 AND date(created) >= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_curdate) . "' AND date(created) <= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_fromdate) . "'";
        if($MJTC_uid) $MJTC_query .= " AND staffid = ".absint($MJTC_staffid);
        $MJTC_answeredticket = majesticsupport::$_db->get_results($MJTC_query);

        $MJTC_query = "SELECT created FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE isoverdue = 1 AND status != 5 AND date(created) >= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_curdate) . "' AND date(created) <= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_fromdate) . "'";
        if($MJTC_uid) $MJTC_query .= " AND staffid = ".absint($MJTC_staffid);
        $MJTC_overdueticket = majesticsupport::$_db->get_results($MJTC_query);

        $MJTC_query = "SELECT created FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE isanswered != 1 AND status != 5 AND (lastreply != '0000-00-00 00:00:00') AND date(created) >= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_curdate) . "' AND date(created) <= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_fromdate) . "'";
        if($MJTC_uid) $MJTC_query .= " AND staffid = ".absint($MJTC_staffid);
        $MJTC_pendingticket = majesticsupport::$_db->get_results($MJTC_query);

        $MJTC_date_openticket = array();
        $MJTC_date_closeticket = array();
        $MJTC_date_answeredticket = array();
        $MJTC_date_overdueticket = array();
        $MJTC_date_pendingticket = array();
        foreach ($MJTC_openticket AS $MJTC_ticket) {
            if (!isset($MJTC_date_openticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))]))
                $MJTC_date_openticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] = 0;
            $MJTC_date_openticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] = $MJTC_date_openticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] + 1;
        }
        foreach ($MJTC_closeticket AS $MJTC_ticket) {
            if (!isset($MJTC_date_closeticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))]))
                $MJTC_date_closeticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] = 0;
            $MJTC_date_closeticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] = $MJTC_date_closeticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] + 1;
        }
        foreach ($MJTC_answeredticket AS $MJTC_ticket) {
            if (!isset($MJTC_date_answeredticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))]))
                $MJTC_date_answeredticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] = 0;
            $MJTC_date_answeredticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] = $MJTC_date_answeredticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] + 1;
        }
        foreach ($MJTC_overdueticket AS $MJTC_ticket) {
            if (!isset($MJTC_date_overdueticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))]))
                $MJTC_date_overdueticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] = 0;
            $MJTC_date_overdueticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] = $MJTC_date_overdueticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] + 1;
        }
        foreach ($MJTC_pendingticket AS $MJTC_ticket) {
            if (!isset($MJTC_date_pendingticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))]))
                $MJTC_date_pendingticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] = 0;
            $MJTC_date_pendingticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] = $MJTC_date_pendingticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] + 1;
        }
        $MJTC_open_ticket = 0;
        $MJTC_close_ticket = 0;
        $MJTC_answered_ticket = 0;
        $MJTC_json_array = "";
        $MJTC_open_ticket = 0;
        $MJTC_close_ticket = 0;
        $MJTC_answered_ticket = 0;
        $MJTC_overdue_ticket = 0;
        $MJTC_pending_ticket = 0;

        do{
            $MJTC_year = date_i18n('Y',MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_nextdate));
            $month = date_i18n('m',MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_nextdate));
            $month = $month - 1; //js month are 0 based
            $MJTC_day = date_i18n('d',MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_nextdate));
            $MJTC_openticket_tmp = isset($MJTC_date_openticket[$MJTC_nextdate]) ? $MJTC_date_openticket[$MJTC_nextdate]  : 0;
            $MJTC_closeticket_tmp = isset($MJTC_date_closeticket[$MJTC_nextdate]) ? $MJTC_date_closeticket[$MJTC_nextdate] : 0;
            $MJTC_answeredticket_tmp = isset($MJTC_date_answeredticket[$MJTC_nextdate]) ? $MJTC_date_answeredticket[$MJTC_nextdate] : 0;
            $MJTC_overdueticket_tmp = isset($MJTC_date_overdueticket[$MJTC_nextdate]) ? $MJTC_date_overdueticket[$MJTC_nextdate] : 0;
            $MJTC_pendingticket_tmp = isset($MJTC_date_pendingticket[$MJTC_nextdate]) ? $MJTC_date_pendingticket[$MJTC_nextdate] : 0;
            $MJTC_json_array .= "[new Date($MJTC_year,$month,$MJTC_day),$MJTC_openticket_tmp,$MJTC_answeredticket_tmp,$MJTC_pendingticket_tmp,$MJTC_overdueticket_tmp,$MJTC_closeticket_tmp],";
            $MJTC_open_ticket += $MJTC_openticket_tmp;
            $MJTC_close_ticket += $MJTC_closeticket_tmp;
            $MJTC_answered_ticket += $MJTC_answeredticket_tmp;
            $MJTC_overdue_ticket += $MJTC_overdueticket_tmp;
            $MJTC_pending_ticket += $MJTC_pendingticket_tmp;
            if($MJTC_nextdate == $MJTC_curdate){
                break;
            }
                $MJTC_nextdate = date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_nextdate . " -1 days"));
        }while($MJTC_nextdate != $MJTC_curdate);

        majesticsupport::$_data['ticket_total']['allticket'] = $MJTC_allticket;
        majesticsupport::$_data['ticket_total']['openticket'] = $MJTC_open_ticket;
        majesticsupport::$_data['ticket_total']['closeticket'] = $MJTC_close_ticket;
        majesticsupport::$_data['ticket_total']['answeredticket'] = $MJTC_answered_ticket;
        majesticsupport::$_data['ticket_total']['overdueticket'] = $MJTC_overdue_ticket;
        majesticsupport::$_data['ticket_total']['pendingticket'] = $MJTC_pending_ticket;

        majesticsupport::$_data['line_chart_json_array'] = $MJTC_json_array;

        // Pagination
        $MJTC_query = "SELECT count(staff.id)
                    FROM `".majesticsupport::$_db->prefix."mjtc_support_staff` AS staff
                    JOIN `".majesticsupport::$_wpprefixforuser."mjtc_support_users` AS user ON user.id = staff.uid";
        if($MJTC_uid) $MJTC_query .= ' WHERE staff.uid = '.absint($MJTC_uid);
        $total = majesticsupport::$_db->get_var($MJTC_query);
        majesticsupport::$_data['total'] = $total;
        majesticsupport::$_data[1] = MJTC_pagination::MJTC_getPagination($total , 'staffreports');

        $MJTC_query = "SELECT staff.photo,staff.id,staff.uid,staff.firstname,staff.lastname,staff.username,staff.email,user.display_name,user.user_email,user.user_nicename,
                    (SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE status = 1 AND (lastreply = '0000-00-00 00:00:00') AND date(created) >= '" . sanitize_text_field($MJTC_curdate) . "' AND date(created) <= '" . sanitize_text_field($MJTC_fromdate) . "' AND staffid = staff.id) AS openticket,
                    (SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE status = 5 AND date(created) >= '" . sanitize_text_field($MJTC_curdate) . "' AND date(created) <= '" . sanitize_text_field($MJTC_fromdate) . "' AND staffid = staff.id) AS closeticket,
                    (SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE isanswered = 1 AND status != 5 AND status != 1 AND date(created) >= '" . sanitize_text_field($MJTC_curdate) . "' AND date(created) <= '" . sanitize_text_field($MJTC_fromdate) . "' AND staffid = staff.id) AS answeredticket,
                    (SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE isoverdue = 1 AND status != 5 AND date(created) >= '" . sanitize_text_field($MJTC_curdate) . "' AND date(created) <= '" . sanitize_text_field($MJTC_fromdate) . "' AND staffid = staff.id) AS overdueticket,
                    (SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE isanswered != 1 AND status != 5 AND (lastreply != '0000-00-00 00:00:00') AND date(created) >= '" . sanitize_text_field($MJTC_curdate) . "' AND date(created) <= '" . sanitize_text_field($MJTC_fromdate) . "' AND staffid = staff.id) AS pendingticket,
                    (SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE date(created) >= '" . sanitize_text_field($MJTC_curdate) . "' AND date(created) <= '" . sanitize_text_field($MJTC_fromdate) . "' AND staffid = staff.id) AS allticket  ";
                    if(in_array('feedback', majesticsupport::$_active_addons)){
                        $MJTC_query .=    ",(SELECT AVG(feed.rating) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_feedbacks` AS feed JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket ON ticket.id= feed.ticketid WHERE date(feed.created) >= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_curdate) . "' AND date(feed.created) <= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_fromdate) . "' AND ticket.staffid = staff.id) AS avragerating ";
                    }
                    $MJTC_query .=  "FROM `".majesticsupport::$_db->prefix."mjtc_support_staff` AS staff
                    JOIN `".majesticsupport::$_wpprefixforuser."mjtc_support_users` AS user ON user.id = staff.uid";
        if($MJTC_uid) $MJTC_query .= ' WHERE staff.uid = '.absint($MJTC_uid);
        $MJTC_query .= " LIMIT " . MJTC_pagination::MJTC_getOffset() . ", " . MJTC_pagination::MJTC_getLimit();
        $MJTC_agents = majesticsupport::$_db->get_results($MJTC_query);
        if(in_array('timetracking', majesticsupport::$_active_addons)){
            foreach ($MJTC_agents as $MJTC_agent) {
                $MJTC_agent->time = MJTC_includer::MJTC_getModel('timetracking')->getAverageTimeByStaffId($MJTC_agent->id);// time 0 contains avergage time in seconds and 1 contains wheter it is conflicted or not
            }
        }
        majesticsupport::$_data['staffs_report'] = $MJTC_agents;
        return;
    }

    function getDepartmentReports(){
        $MJTC_date_start = isset(majesticsupport::$_search['report']) ? majesticsupport::$_search['report']['date_start'] : '';
        $MJTC_date_end = isset(majesticsupport::$_search['report']) ? majesticsupport::$_search['report']['date_end'] : '';
        if(isset($MJTC_date_start) && $MJTC_date_start != ""){
            $MJTC_date_start = date_i18n('Y-m-d',MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_date_start));
        }
        if(isset($MJTC_date_end) && $MJTC_date_end != ""){
            $MJTC_date_end = date_i18n('Y-m-d',MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_date_end));
        }
        if($MJTC_date_start > $MJTC_date_end){
            $tmp = $MJTC_date_start;
            $MJTC_date_start = $MJTC_date_end;
            $MJTC_date_end = $tmp;
        }

        //Line Chart Data
        $MJTC_curdate = ($MJTC_date_start != null) ? date_i18n('Y-m-d',MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_date_start)) : date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime("now -1 month"));
        $MJTC_dates = '';
        $MJTC_fromdate = ($MJTC_date_end != null) ? date_i18n('Y-m-d',MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_date_end)) : date_i18n('Y-m-d');
        majesticsupport::$_data['filter']['date_start'] = $MJTC_curdate;
        majesticsupport::$_data['filter']['date_end'] = $MJTC_fromdate;
        // forexport
        $_SESSION['forexport']['curdate'] = $MJTC_curdate;
        $_SESSION['forexport']['fromdate'] = $MJTC_fromdate;

        $MJTC_nextdate = $MJTC_fromdate;
        $MJTC_query = "SELECT created FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE status = 1 AND (lastreply = '0000-00-00 00:00:00') AND date(created) >= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_curdate) . "' AND date(created) <= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_fromdate) . "'";
        $MJTC_openticket = majesticsupport::$_db->get_results($MJTC_query);

        $MJTC_query = "SELECT created FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE status = 5 AND date(created) >= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_curdate) . "' AND date(created) <= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_fromdate) . "'";
        $MJTC_closeticket = majesticsupport::$_db->get_results($MJTC_query);

        $MJTC_query = "SELECT created FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE isanswered = 1 AND status != 5 AND status != 1 AND date(created) >= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_curdate) . "' AND date(created) <= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_fromdate) . "'";
        $MJTC_answeredticket = majesticsupport::$_db->get_results($MJTC_query);

        $MJTC_query = "SELECT created FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE isoverdue = 1 AND status != 5 AND date(created) >= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_curdate) . "' AND date(created) <= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_fromdate) . "'";
        $MJTC_overdueticket = majesticsupport::$_db->get_results($MJTC_query);

        $MJTC_query = "SELECT created FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE isanswered != 1 AND status != 5 AND (lastreply != '0000-00-00 00:00:00') AND date(created) >= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_curdate) . "' AND date(created) <= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_fromdate) . "'";
        $MJTC_pendingticket = majesticsupport::$_db->get_results($MJTC_query);

        $MJTC_date_openticket = array();
        $MJTC_date_closeticket = array();
        $MJTC_date_answeredticket = array();
        $MJTC_date_overdueticket = array();
        $MJTC_date_pendingticket = array();
        foreach ($MJTC_openticket AS $MJTC_ticket) {
            if (!isset($MJTC_date_openticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))]))
                $MJTC_date_openticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] = 0;
            $MJTC_date_openticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] = $MJTC_date_openticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] + 1;
        }
        foreach ($MJTC_closeticket AS $MJTC_ticket) {
            if (!isset($MJTC_date_closeticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))]))
                $MJTC_date_closeticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] = 0;
            $MJTC_date_closeticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] = $MJTC_date_closeticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] + 1;
        }
        foreach ($MJTC_answeredticket AS $MJTC_ticket) {
            if (!isset($MJTC_date_answeredticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))]))
                $MJTC_date_answeredticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] = 0;
            $MJTC_date_answeredticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] = $MJTC_date_answeredticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] + 1;
        }
        foreach ($MJTC_overdueticket AS $MJTC_ticket) {
            if (!isset($MJTC_date_overdueticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))]))
                $MJTC_date_overdueticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] = 0;
            $MJTC_date_overdueticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] = $MJTC_date_overdueticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] + 1;
        }
        foreach ($MJTC_pendingticket AS $MJTC_ticket) {
            if (!isset($MJTC_date_pendingticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))]))
                $MJTC_date_pendingticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] = 0;
            $MJTC_date_pendingticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] = $MJTC_date_pendingticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] + 1;
        }
        $MJTC_open_ticket = 0;
        $MJTC_close_ticket = 0;
        $MJTC_answered_ticket = 0;
        $MJTC_json_array = "";
        $MJTC_open_ticket = 0;
        $MJTC_close_ticket = 0;
        $MJTC_answered_ticket = 0;
        $MJTC_overdue_ticket = 0;
        $MJTC_pending_ticket = 0;

        do{
            $MJTC_year = date_i18n('Y',MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_nextdate));
            $month = date_i18n('m',MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_nextdate));
            $month = $month - 1; //js month are 0 based
            $MJTC_day = date_i18n('d',MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_nextdate));
            $MJTC_openticket_tmp = isset($MJTC_date_openticket[$MJTC_nextdate]) ? $MJTC_date_openticket[$MJTC_nextdate]  : 0;
            $MJTC_closeticket_tmp = isset($MJTC_date_closeticket[$MJTC_nextdate]) ? $MJTC_date_closeticket[$MJTC_nextdate] : 0;
            $MJTC_answeredticket_tmp = isset($MJTC_date_answeredticket[$MJTC_nextdate]) ? $MJTC_date_answeredticket[$MJTC_nextdate] : 0;
            $MJTC_overdueticket_tmp = isset($MJTC_date_overdueticket[$MJTC_nextdate]) ? $MJTC_date_overdueticket[$MJTC_nextdate] : 0;
            $MJTC_pendingticket_tmp = isset($MJTC_date_pendingticket[$MJTC_nextdate]) ? $MJTC_date_pendingticket[$MJTC_nextdate] : 0;
            $MJTC_json_array .= "[new Date($MJTC_year,$month,$MJTC_day),$MJTC_openticket_tmp,$MJTC_answeredticket_tmp,$MJTC_pendingticket_tmp,$MJTC_overdueticket_tmp,$MJTC_closeticket_tmp],";
            $MJTC_open_ticket += $MJTC_openticket_tmp;
            $MJTC_close_ticket += $MJTC_closeticket_tmp;
            $MJTC_answered_ticket += $MJTC_answeredticket_tmp;
            $MJTC_overdue_ticket += $MJTC_overdueticket_tmp;
            $MJTC_pending_ticket += $MJTC_pendingticket_tmp;
             if($MJTC_nextdate == $MJTC_curdate){
                break;
            }
            $MJTC_nextdate = date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_nextdate . " -1 days"));
        }while($MJTC_nextdate != $MJTC_curdate);

        majesticsupport::$_data['ticket_total']['openticket'] = $MJTC_open_ticket;
        majesticsupport::$_data['ticket_total']['closeticket'] = $MJTC_close_ticket;
        majesticsupport::$_data['ticket_total']['answeredticket'] = $MJTC_answered_ticket;
        majesticsupport::$_data['ticket_total']['overdueticket'] = $MJTC_overdue_ticket;
        majesticsupport::$_data['ticket_total']['pendingticket'] = $MJTC_pending_ticket;

        majesticsupport::$_data['line_chart_json_array'] = $MJTC_json_array;

        // Pagination
        $MJTC_query = "SELECT count(department.id)
                    FROM `".majesticsupport::$_db->prefix."mjtc_support_departments` AS department
                    JOIN `".majesticsupport::$_db->prefix."mjtc_support_email` AS email ON department.emailid = email.id";
        $total = majesticsupport::$_db->get_var($MJTC_query);
        majesticsupport::$_data['total'] = $total;
        majesticsupport::$_data[1] = MJTC_pagination::MJTC_getPagination($total);

        $MJTC_query = "SELECT department.id,department.departmentname,email.email,
                    (SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE departmentid = department.id) AS allticket,
                    (SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE status = 1 AND (lastreply = '0000-00-00 00:00:00') AND date(created) >= '" . sanitize_text_field($MJTC_curdate) . "' AND date(created) <= '" . sanitize_text_field($MJTC_fromdate) . "' AND departmentid = department.id) AS openticket,
                    (SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE status = 5 AND date(created) >= '" . sanitize_text_field($MJTC_curdate) . "' AND date(created) <= '" . sanitize_text_field($MJTC_fromdate) . "' AND departmentid = department.id) AS closeticket,
                    (SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE isanswered = 1 AND status != 5 AND status != 1 AND date(created) >= '" . sanitize_text_field($MJTC_curdate) . "' AND date(created) <= '" . sanitize_text_field($MJTC_fromdate) . "' AND departmentid = department.id) AS answeredticket,
                    (SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE isoverdue = 1 AND status != 5 AND date(created) >= '" . sanitize_text_field($MJTC_curdate) . "' AND date(created) <= '" . sanitize_text_field($MJTC_fromdate) . "' AND departmentid = department.id) AS overdueticket,
                    (SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE isanswered != 1 AND status != 5 AND (lastreply != '0000-00-00 00:00:00') AND date(created) >= '" . sanitize_text_field($MJTC_curdate) . "' AND date(created) <= '" . sanitize_text_field($MJTC_fromdate) . "' AND departmentid = department.id) AS pendingticket
                    FROM `".majesticsupport::$_db->prefix."mjtc_support_departments` AS department
                    JOIN `".majesticsupport::$_db->prefix."mjtc_support_email` AS email ON department.emailid = email.id";
        $MJTC_query .= " LIMIT " . MJTC_pagination::MJTC_getOffset() . ", " . MJTC_pagination::MJTC_getLimit();
        $MJTC_depatments = majesticsupport::$_db->get_results($MJTC_query);
        majesticsupport::$_data['depatments_report'] =$MJTC_depatments;
        return;
    }

    function getStaffReportsFE(){
        if( !in_array('agent',majesticsupport::$_active_addons) ){
            return;
        }
        $MJTC_date_start = isset(majesticsupport::$_search['report']) ? majesticsupport::$_search['report']['ms-date-start'] : '';
        $MJTC_date_end = isset(majesticsupport::$_search['report']) ? majesticsupport::$_search['report']['ms-date-end'] : '';
        if(isset($MJTC_date_start) && $MJTC_date_start != ""){
            $MJTC_date_start = date_i18n('Y-m-d',MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_date_start));
        }
        if(isset($MJTC_date_end) && $MJTC_date_end != ""){
            $MJTC_date_end = date_i18n('Y-m-d',MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_date_end));
        }

        if($MJTC_date_start > $MJTC_date_end){
            $tmp = $MJTC_date_start;
            $MJTC_date_start = $MJTC_date_end;
            $MJTC_date_end = $tmp;
        }

        $MJTC_uid = MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid();

        //Line Chart Data
        $MJTC_curdate = ($MJTC_date_start != null) ? date_i18n('Y-m-d',MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_date_start)) : date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime("now -1 month"));
        $MJTC_dates = '';
        $MJTC_fromdate = ($MJTC_date_end != null) ? date_i18n('Y-m-d',MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_date_end)) : date_i18n('Y-m-d');
        majesticsupport::$_data['filter']['ms-date-start'] = $MJTC_curdate;
        majesticsupport::$_data['filter']['ms-date-end'] = $MJTC_fromdate;

        $MJTC_staffid = MJTC_includer::MJTC_getModel('agent')->getStaffId($MJTC_uid);

        majesticsupport::$_data['filter']['staffname'] = MJTC_includer::MJTC_getModel('agent')->getMyName($MJTC_staffid);
        $MJTC_nextdate = $MJTC_fromdate;
        // find my depats
        $MJTC_query = "SELECT dept.departmentid FROM `" . majesticsupport::$_db->prefix . "mjtc_support_acl_user_access_departments` AS dept WHERE dept.staffid = " . absint($MJTC_staffid);
        $MJTC_data = majesticsupport::$_db->get_results($MJTC_query);
        $my_depts = '';
        foreach ($MJTC_data as $MJTC_key => $MJTC_value) {
            if($my_depts)
                $my_depts .= ',';
            $my_depts .= $MJTC_value->departmentid;
        }
        // get mytickets, or all tickets with my depatments
        if($my_depts)
            $MJTC_dep_query = " AND (ticket.staffid = ".absint($MJTC_staffid)." OR ticket.departmentid IN (".$my_depts.")) ";
        else
            $MJTC_dep_query = " AND ( ticket.staffid = ".absint($MJTC_staffid)." ) ";
        //Query to get Data
        $MJTC_query = "SELECT ticket.created FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket WHERE ticket.status = 1 AND (ticket.lastreply = '0000-00-00 00:00:00') AND date(ticket.created) >= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_curdate) . "' AND date(ticket.created) <= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_fromdate) . "'";
        $MJTC_query .= $MJTC_dep_query;
        $MJTC_openticket = majesticsupport::$_db->get_results($MJTC_query);

        $MJTC_query = "SELECT ticket.created FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket WHERE ticket.status = 5 AND date(ticket.created) >= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_curdate) . "' AND date(ticket.created) <= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_fromdate) . "'";
        $MJTC_query .= $MJTC_dep_query;
        $MJTC_closeticket = majesticsupport::$_db->get_results($MJTC_query);

        $MJTC_query = "SELECT ticket.created FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket WHERE ticket.isanswered = 1 AND ticket.status != 5 AND ticket.status != 1 AND date(ticket.created) >= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_curdate) . "' AND date(ticket.created) <= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_fromdate) . "'";
        $MJTC_query .= $MJTC_dep_query;
        $MJTC_answeredticket = majesticsupport::$_db->get_results($MJTC_query);

        $MJTC_query = "SELECT ticket.created FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket WHERE ticket.isoverdue = 1 AND ticket.status != 5 AND date(ticket.created) >= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_curdate) . "' AND date(ticket.created) <= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_fromdate) . "'";
        $MJTC_query .= $MJTC_dep_query;
        $MJTC_overdueticket = majesticsupport::$_db->get_results($MJTC_query);

        $MJTC_query = "SELECT ticket.created FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket WHERE ticket.isanswered != 1 AND ticket.status != 5 AND (ticket.lastreply != '0000-00-00 00:00:00') AND date(ticket.created) >= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_curdate) . "' AND date(ticket.created) <= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_fromdate) . "'";
        $MJTC_query .= $MJTC_dep_query;
        $MJTC_pendingticket = majesticsupport::$_db->get_results($MJTC_query);

        $MJTC_date_openticket = array();
        $MJTC_date_closeticket = array();
        $MJTC_date_answeredticket = array();
        $MJTC_date_overdueticket = array();
        $MJTC_date_pendingticket = array();
        foreach ($MJTC_openticket AS $MJTC_ticket) {
            if (!isset($MJTC_date_openticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))]))
                $MJTC_date_openticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] = 0;
            $MJTC_date_openticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] = $MJTC_date_openticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] + 1;
        }
        foreach ($MJTC_closeticket AS $MJTC_ticket) {
            if (!isset($MJTC_date_closeticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))]))
                $MJTC_date_closeticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] = 0;
            $MJTC_date_closeticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] = $MJTC_date_closeticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] + 1;
        }
        foreach ($MJTC_answeredticket AS $MJTC_ticket) {
            if (!isset($MJTC_date_answeredticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))]))
                $MJTC_date_answeredticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] = 0;
            $MJTC_date_answeredticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] = $MJTC_date_answeredticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] + 1;
        }
        foreach ($MJTC_overdueticket AS $MJTC_ticket) {
            if (!isset($MJTC_date_overdueticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))]))
                $MJTC_date_overdueticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] = 0;
            $MJTC_date_overdueticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] = $MJTC_date_overdueticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] + 1;
        }
        foreach ($MJTC_pendingticket AS $MJTC_ticket) {
            if (!isset($MJTC_date_pendingticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))]))
                $MJTC_date_pendingticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] = 0;
            $MJTC_date_pendingticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] = $MJTC_date_pendingticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] + 1;
        }
        $MJTC_open_ticket = 0;
        $MJTC_close_ticket = 0;
        $MJTC_answered_ticket = 0;
        $MJTC_json_array = "";
        $MJTC_open_ticket = 0;
        $MJTC_close_ticket = 0;
        $MJTC_answered_ticket = 0;
        $MJTC_overdue_ticket = 0;
        $MJTC_pending_ticket = 0;

        do{
            $MJTC_year = date_i18n('Y',MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_nextdate));
            $month = date_i18n('m',MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_nextdate));
            $month = $month - 1; //js month are 0 based
            $MJTC_day = date_i18n('d',MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_nextdate));
            $MJTC_openticket_tmp = isset($MJTC_date_openticket[$MJTC_nextdate]) ? $MJTC_date_openticket[$MJTC_nextdate]  : 0;
            $MJTC_closeticket_tmp = isset($MJTC_date_closeticket[$MJTC_nextdate]) ? $MJTC_date_closeticket[$MJTC_nextdate] : 0;
            $MJTC_answeredticket_tmp = isset($MJTC_date_answeredticket[$MJTC_nextdate]) ? $MJTC_date_answeredticket[$MJTC_nextdate] : 0;
            $MJTC_overdueticket_tmp = isset($MJTC_date_overdueticket[$MJTC_nextdate]) ? $MJTC_date_overdueticket[$MJTC_nextdate] : 0;
            $MJTC_pendingticket_tmp = isset($MJTC_date_pendingticket[$MJTC_nextdate]) ? $MJTC_date_pendingticket[$MJTC_nextdate] : 0;
            $MJTC_json_array .= "[new Date($MJTC_year,$month,$MJTC_day),$MJTC_openticket_tmp,$MJTC_answeredticket_tmp,$MJTC_pendingticket_tmp,$MJTC_overdueticket_tmp,$MJTC_closeticket_tmp],";
            $MJTC_open_ticket += $MJTC_openticket_tmp;
            $MJTC_close_ticket += $MJTC_closeticket_tmp;
            $MJTC_answered_ticket += $MJTC_answeredticket_tmp;
            $MJTC_overdue_ticket += $MJTC_overdueticket_tmp;
            $MJTC_pending_ticket += $MJTC_pendingticket_tmp;
             if($MJTC_nextdate == $MJTC_curdate){
                break;
            }
            $MJTC_nextdate = date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_nextdate . " -1 days"));
        }while($MJTC_nextdate != $MJTC_curdate);

        majesticsupport::$_data['ticket_total']['openticket'] = $MJTC_open_ticket;
        majesticsupport::$_data['ticket_total']['closeticket'] = $MJTC_close_ticket;
        majesticsupport::$_data['ticket_total']['answeredticket'] = $MJTC_answered_ticket;
        majesticsupport::$_data['ticket_total']['overdueticket'] = $MJTC_overdue_ticket;
        majesticsupport::$_data['ticket_total']['pendingticket'] = $MJTC_pending_ticket;

        majesticsupport::$_data['line_chart_json_array'] = $MJTC_json_array;

        // Pagination staffs listing
        $MJTC_query = "SELECT COUNT(DISTINCT staff.id)
            FROM `".majesticsupport::$_db->prefix."mjtc_support_staff` AS staff
            JOIN `".majesticsupport::$_wpprefixforuser."mjtc_support_users` AS user ON user.id = staff.uid
            LEFT JOIN `".majesticsupport::$_db->prefix . "mjtc_support_acl_user_access_departments` AS dep ON dep.staffid = staff.id ";
        $MJTC_query .= " WHERE (staff.id = ".absint($MJTC_staffid)." OR dep.departmentid IN (SELECT dept.departmentid FROM `" . majesticsupport::$_db->prefix . "mjtc_support_acl_user_access_departments` AS dept WHERE dept.staffid = ".absint($MJTC_staffid)."))";
        $total = majesticsupport::$_db->get_var($MJTC_query);
        majesticsupport::$_data['total'] = $total;
        majesticsupport::$_data[1] = MJTC_pagination::MJTC_getPagination($total);
        // data
        $MJTC_query = "SELECT DISTINCT staff.photo,staff.id,staff.uid,staff.firstname,staff.lastname,staff.username,staff.email,user.display_name,user.user_email,user.user_nicename,
            (SELECT COUNT(ticket.id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket WHERE ticket.status = 1 AND (ticket.lastreply = '0000-00-00 00:00:00') AND date(ticket.created) >= '" . sanitize_text_field($MJTC_curdate) . "' AND date(ticket.created) <= '" . sanitize_text_field($MJTC_fromdate) . "' AND ticket.staffid = staff.id) AS openticket,
            (SELECT COUNT(ticket.id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket WHERE ticket.status = 5 AND date(ticket.created) >= '" . sanitize_text_field($MJTC_curdate) . "' AND date(ticket.created) <= '" . sanitize_text_field($MJTC_fromdate) . "' AND ticket.staffid = staff.id) AS closeticket,
            (SELECT COUNT(ticket.id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket WHERE ticket.isanswered = 1 AND ticket.status != 5 AND ticket.status != 1 AND date(ticket.created) >= '" . sanitize_text_field($MJTC_curdate) . "' AND date(ticket.created) <= '" . sanitize_text_field($MJTC_fromdate) . "' AND ticket.staffid = staff.id) AS answeredticket,
            (SELECT COUNT(ticket.id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket WHERE ticket.isoverdue = 1 AND ticket.status != 5 AND date(ticket.created) >= '" . sanitize_text_field($MJTC_curdate) . "' AND date(ticket.created) <= '" . sanitize_text_field($MJTC_fromdate) . "' AND ticket.staffid = staff.id) AS overdueticket,
            (SELECT COUNT(ticket.id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket WHERE ticket.isanswered != 1 AND ticket.status != 5 AND (ticket.lastreply != '0000-00-00 00:00:00') AND date(ticket.created) >= '" . sanitize_text_field($MJTC_curdate) . "' AND date(ticket.created) <= '" . sanitize_text_field($MJTC_fromdate) . "' AND ticket.staffid = staff.id) AS pendingticket
            FROM `".majesticsupport::$_db->prefix."mjtc_support_staff` AS staff
            JOIN `".majesticsupport::$_wpprefixforuser."mjtc_support_users` AS user ON user.id = staff.uid
            LEFT JOIN `".majesticsupport::$_db->prefix . "mjtc_support_acl_user_access_departments` AS dep ON dep.staffid = staff.id";
        $MJTC_query .= " WHERE (staff.id = ".absint($MJTC_staffid)." OR dep.departmentid IN (SELECT dept.departmentid FROM `" . majesticsupport::$_db->prefix . "mjtc_support_acl_user_access_departments` AS dept WHERE dept.staffid = ".absint($MJTC_staffid)."))";
        $MJTC_query .= " LIMIT " . MJTC_pagination::MJTC_getOffset() . ", " . MJTC_pagination::MJTC_getLimit();
        $MJTC_agents = majesticsupport::$_db->get_results($MJTC_query);
        majesticsupport::$_data['staffs_report'] = $MJTC_agents;
        return;
    }

    function isValidStaffid($MJTC_staffid){
        if( !in_array('agent',majesticsupport::$_active_addons) ){
            return false;
        }

        if( ! is_numeric($MJTC_staffid))
            return false;
        $MJTC_uid = MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid();
        $MJTC_id = MJTC_includer::MJTC_getModel('agent')->getStaffId($MJTC_uid);
        if( $MJTC_id == $MJTC_staffid )
            return true;
        $MJTC_query = "SELECT staff.id AS staffid
            FROM `".majesticsupport::$_db->prefix."mjtc_support_staff` AS staff
            JOIN `".majesticsupport::$_wpprefixforuser."mjtc_support_users` AS user ON user.id = staff.uid
            JOIN `".majesticsupport::$_db->prefix . "mjtc_support_acl_user_access_departments` AS dep ON dep.staffid = staff.id ";
        $MJTC_query .= " WHERE (dep.departmentid IN (SELECT dept.departmentid FROM `" . majesticsupport::$_db->prefix . "mjtc_support_acl_user_access_departments` AS dept WHERE dept.staffid = ".absint($MJTC_id)."))";
        $MJTC_result = majesticsupport::$_db->get_results($MJTC_query);
        foreach ($MJTC_result as $MJTC_agent) {
            if($MJTC_agent->staffid == $MJTC_staffid)
                return true;
        }
        return false;
    }

    function getUserReports(){
        $MJTC_date_start = isset(majesticsupport::$_search['report']) ? majesticsupport::$_search['report']['date_start'] : '';
        $MJTC_date_end = isset(majesticsupport::$_search['report']) ? majesticsupport::$_search['report']['date_end'] : '';
        if(isset($MJTC_date_start) && $MJTC_date_start != ""){
            $MJTC_date_start = date_i18n('Y-m-d',MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_date_start));
        }
        if(isset($MJTC_date_end) && $MJTC_date_end != ""){
            $MJTC_date_end = date_i18n('Y-m-d',MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_date_end));
        }
        if($MJTC_date_start > $MJTC_date_end){
            $tmp = $MJTC_date_start;
            $MJTC_date_start = $MJTC_date_end;
            $MJTC_date_end = $tmp;
        }
        $MJTC_uid = isset(majesticsupport::$_search['report']) ? majesticsupport::$_search['report']['uid'] : '';

        //Line Chart Data
        $MJTC_curdate = ($MJTC_date_start != null) ? date_i18n('Y-m-d',MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_date_start)) : date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime("now -1 month"));
        $MJTC_dates = '';
        $MJTC_fromdate = ($MJTC_date_end != null) ? date_i18n('Y-m-d',MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_date_end)) : date_i18n('Y-m-d');
        majesticsupport::$_data['filter']['date_start'] = $MJTC_curdate;
        majesticsupport::$_data['filter']['date_end'] = $MJTC_fromdate;
        majesticsupport::$_data['filter']['uid'] = $MJTC_uid;

        // forexport
        $_SESSION['forexport']['curdate'] = $MJTC_curdate;
        $_SESSION['forexport']['fromdate'] = $MJTC_fromdate;
        $_SESSION['forexport']['uid'] = $MJTC_uid;

        majesticsupport::$_data['filter']['username'] = MJTC_includer::MJTC_getModel('majesticsupport')->getUserNameById($MJTC_uid);
        $MJTC_nextdate = $MJTC_fromdate;
        //Query to get Data
        $MJTC_query = "SELECT count(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` ";
        if($MJTC_uid) $MJTC_query .= " WHERE  uid = ".absint($MJTC_uid);
        $MJTC_allticket = majesticsupport::$_db->get_var($MJTC_query);

        $MJTC_query = "SELECT created FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE status = 1  AND (lastreply = '0000-00-00 00:00:00') AND date(created) >= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_curdate) . "' AND date(created) <= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_fromdate) . "'";
        if($MJTC_uid) $MJTC_query .= " AND uid = ".absint($MJTC_uid);
        $MJTC_openticket = majesticsupport::$_db->get_results($MJTC_query);

        $MJTC_query = "SELECT created FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE status = 5 AND date(created) >= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_curdate) . "' AND date(created) <= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_fromdate) . "'";
        if($MJTC_uid) $MJTC_query .= " AND uid = ".absint($MJTC_uid);
        $MJTC_closeticket = majesticsupport::$_db->get_results($MJTC_query);

        $MJTC_query = "SELECT created FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE isanswered = 1 AND status != 5 AND status != 1 AND date(created) >= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_curdate) . "' AND date(created) <= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_fromdate) . "'";
        if($MJTC_uid) $MJTC_query .= " AND uid = ".absint($MJTC_uid);
        $MJTC_answeredticket = majesticsupport::$_db->get_results($MJTC_query);

        $MJTC_query = "SELECT created FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE isoverdue = 1 AND status != 5 AND date(created) >= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_curdate) . "' AND date(created) <= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_fromdate) . "'";
        if($MJTC_uid) $MJTC_query .= " AND uid = ". absint($MJTC_uid);
        $MJTC_overdueticket = majesticsupport::$_db->get_results($MJTC_query);

        $MJTC_query = "SELECT created FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE isanswered != 1 AND status != 5 AND (lastreply != '0000-00-00 00:00:00') AND date(created) >= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_curdate) . "' AND date(created) <= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_fromdate) . "'";
        if($MJTC_uid) $MJTC_query .= " AND uid = ".absint($MJTC_uid);
        $MJTC_pendingticket = majesticsupport::$_db->get_results($MJTC_query);

        $MJTC_date_openticket = array();
        $MJTC_date_closeticket = array();
        $MJTC_date_answeredticket = array();
        $MJTC_date_overdueticket = array();
        $MJTC_date_pendingticket = array();
        foreach ($MJTC_openticket AS $MJTC_ticket) {
            if (!isset($MJTC_date_openticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))]))
                $MJTC_date_openticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] = 0;
            $MJTC_date_openticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] = $MJTC_date_openticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] + 1;
        }
        foreach ($MJTC_closeticket AS $MJTC_ticket) {
            if (!isset($MJTC_date_closeticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))]))
                $MJTC_date_closeticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] = 0;
            $MJTC_date_closeticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] = $MJTC_date_closeticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] + 1;
        }
        foreach ($MJTC_answeredticket AS $MJTC_ticket) {
            if (!isset($MJTC_date_answeredticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))]))
                $MJTC_date_answeredticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] = 0;
            $MJTC_date_answeredticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] = $MJTC_date_answeredticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] + 1;
        }
        foreach ($MJTC_overdueticket AS $MJTC_ticket) {
            if (!isset($MJTC_date_overdueticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))]))
                $MJTC_date_overdueticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] = 0;
            $MJTC_date_overdueticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] = $MJTC_date_overdueticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] + 1;
        }
        foreach ($MJTC_pendingticket AS $MJTC_ticket) {
            if (!isset($MJTC_date_pendingticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))]))
                $MJTC_date_pendingticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] = 0;
            $MJTC_date_pendingticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] = $MJTC_date_pendingticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] + 1;
        }
        $MJTC_open_ticket = 0;
        $MJTC_close_ticket = 0;
        $MJTC_answered_ticket = 0;
        $MJTC_overdue_ticket = 0;
        $MJTC_pending_ticket = 0;
        $MJTC_json_array = "";
        do{
            $MJTC_year = date_i18n('Y',MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_nextdate));
            $month = date_i18n('m',MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_nextdate));
            $month = $month - 1; //js month are 0 based
            $MJTC_day = date_i18n('d',MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_nextdate));
            $MJTC_openticket_tmp = isset($MJTC_date_openticket[$MJTC_nextdate]) ? $MJTC_date_openticket[$MJTC_nextdate]  : 0;
            $MJTC_closeticket_tmp = isset($MJTC_date_closeticket[$MJTC_nextdate]) ? $MJTC_date_closeticket[$MJTC_nextdate] : 0;
            $MJTC_answeredticket_tmp = isset($MJTC_date_answeredticket[$MJTC_nextdate]) ? $MJTC_date_answeredticket[$MJTC_nextdate] : 0;
            $MJTC_overdueticket_tmp = isset($MJTC_date_overdueticket[$MJTC_nextdate]) ? $MJTC_date_overdueticket[$MJTC_nextdate] : 0;
            $MJTC_pendingticket_tmp = isset($MJTC_date_pendingticket[$MJTC_nextdate]) ? $MJTC_date_pendingticket[$MJTC_nextdate] : 0;
            $MJTC_json_array .= "[new Date($MJTC_year,$month,$MJTC_day),$MJTC_openticket_tmp,$MJTC_answeredticket_tmp,$MJTC_pendingticket_tmp,$MJTC_overdueticket_tmp,$MJTC_closeticket_tmp],";
            $MJTC_open_ticket += $MJTC_openticket_tmp;
            $MJTC_close_ticket += $MJTC_closeticket_tmp;
            $MJTC_answered_ticket += $MJTC_answeredticket_tmp;
            $MJTC_overdue_ticket += $MJTC_overdueticket_tmp;
            $MJTC_pending_ticket += $MJTC_pendingticket_tmp;
             if($MJTC_nextdate == $MJTC_curdate){
                break;
            }
            $MJTC_nextdate = date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_nextdate . " -1 days"));
        }while($MJTC_nextdate != $MJTC_curdate);

        majesticsupport::$_data['ticket_total']['allticket'] = $MJTC_allticket;
        majesticsupport::$_data['ticket_total']['openticket'] = $MJTC_open_ticket;
        majesticsupport::$_data['ticket_total']['closeticket'] = $MJTC_close_ticket;
        majesticsupport::$_data['ticket_total']['answeredticket'] = $MJTC_answered_ticket;
        majesticsupport::$_data['ticket_total']['overdueticket'] = $MJTC_overdue_ticket;
        majesticsupport::$_data['ticket_total']['pendingticket'] = $MJTC_pending_ticket;

        majesticsupport::$_data['line_chart_json_array'] = $MJTC_json_array;

        // Pagination
        $MJTC_query = "SELECT COUNT(user.id)
                    FROM `".majesticsupport::$_wpprefixforuser."mjtc_support_users` AS user
                    WHERE user.wpuid != 0 AND ";
                    if(in_array('agent', majesticsupport::$_active_addons)){
                        $MJTC_query .=" NOT EXISTS (SELECT id FROM `".majesticsupport::$_db->prefix."mjtc_support_staff` WHERE uid = user.id) AND  ";
                    }
                    $MJTC_query .=" NOT EXISTS (SELECT umeta_id FROM `".majesticsupport::$_wpprefixforuser."usermeta` WHERE user_id = user.id AND meta_value LIKE '%administrator%')";
        if($MJTC_uid) $MJTC_query .= " AND user.id = ".absint($MJTC_uid);
        $total = majesticsupport::$_db->get_var($MJTC_query);
        majesticsupport::$_data['total'] = $total;
        majesticsupport::$_data[1] = MJTC_pagination::MJTC_getPagination($total);

        $MJTC_query = "SELECT user.display_name,user.user_email,user.user_nicename,user.id,
                    (SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE uid = user.id) AS allticket,
                    (SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE status = 1 AND (lastreply = '0000-00-00 00:00:00') AND date(created) >= '" . sanitize_text_field($MJTC_curdate) . "' AND date(created) <= '" . sanitize_text_field($MJTC_fromdate) . "' AND uid = user.id) AS openticket,
                    (SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE status = 5 AND date(created) >= '" . sanitize_text_field($MJTC_curdate) . "' AND date(created) <= '" . sanitize_text_field($MJTC_fromdate) . "' AND uid = user.id) AS closeticket,
                    (SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE isanswered = 1 AND status != 5 AND status != 1 AND date(created) >= '" . sanitize_text_field($MJTC_curdate) . "' AND date(created) <= '" . sanitize_text_field($MJTC_fromdate) . "' AND uid = user.id) AS answeredticket,
                    (SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE isoverdue = 1 AND status != 5 AND date(created) >= '" . sanitize_text_field($MJTC_curdate) . "' AND date(created) <= '" . sanitize_text_field($MJTC_fromdate) . "' AND uid = user.id) AS overdueticket,
                    (SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE isanswered != 1 AND status != 5 AND (lastreply != '0000-00-00 00:00:00') AND date(created) >= '" . sanitize_text_field($MJTC_curdate) . "' AND date(created) <= '" . sanitize_text_field($MJTC_fromdate) . "' AND uid = user.id) AS pendingticket
                    FROM `".majesticsupport::$_wpprefixforuser."mjtc_support_users` AS user
                    WHERE user.wpuid != 0 AND ";
                    if(in_array('agent', majesticsupport::$_active_addons)){
                        $MJTC_query .=" NOT EXISTS (SELECT id FROM `".majesticsupport::$_db->prefix."mjtc_support_staff` WHERE uid = user.id) AND  ";
                    }
                    $MJTC_query .=" NOT EXISTS (SELECT umeta_id FROM `".majesticsupport::$_wpprefixforuser."usermeta` WHERE user_id = user.id AND meta_value LIKE '%administrator%')";
        if($MJTC_uid) $MJTC_query .= " AND user.id = ".absint($MJTC_uid);
        $MJTC_query .= " LIMIT " . MJTC_pagination::MJTC_getOffset() . ", " . MJTC_pagination::MJTC_getLimit();
        $MJTC_users = majesticsupport::$_db->get_results($MJTC_query);
        majesticsupport::$_data['users_report'] =$MJTC_users;
        return;
    }

    function getStaffDetailReportByStaffId($MJTC_id){
        if( !in_array('agent',majesticsupport::$_active_addons) ){
            return;
        }
        if(!is_numeric($MJTC_id)) return false;

        if( ! is_admin()){
            $MJTC_result = $this->isValidStaffid( $MJTC_id );
            if( $MJTC_result == false)
                return false;
        }

        $MJTC_start_date = is_admin() ? 'date_start' : 'ms-date-start';
        $MJTC_end_date = is_admin() ? 'date_end' : 'ms-date-end';
        if(isset($MJTC_date_start) && $MJTC_date_start != ""){
            $MJTC_date_start = date_i18n('Y-m-d',MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_date_start));
        }
        if(isset($MJTC_date_end) && $MJTC_date_end != ""){
            $MJTC_date_end = date_i18n('Y-m-d',MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_date_end));
        }
        $MJTC_date_start = isset(majesticsupport::$_search['report']) ? majesticsupport::$_search['report'][$MJTC_start_date] : '';
        $MJTC_date_end = isset(majesticsupport::$_search['report']) ? majesticsupport::$_search['report'][$MJTC_end_date] : '';
        if(isset($MJTC_date_start) && $MJTC_date_start != ""){
            $MJTC_date_start = date_i18n('Y-m-d',MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_date_start));
        }
        if(isset($MJTC_date_end) && $MJTC_date_end != ""){
            $MJTC_date_end = date_i18n('Y-m-d',MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_date_end));
        }
        if($MJTC_date_start > $MJTC_date_end){
            $tmp = $MJTC_date_start;
            $MJTC_date_start = $MJTC_date_end;
            $MJTC_date_end = $tmp;
        }

        //Line Chart Data
        $MJTC_curdate = ($MJTC_date_start != null) ? date_i18n('Y-m-d',MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_date_start)) : date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime("now -1 month"));
        $MJTC_fromdate = ($MJTC_date_end != null) ? date_i18n('Y-m-d',MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_date_end)) : date_i18n('Y-m-d');
        majesticsupport::$_data['filter'][$MJTC_start_date] = $MJTC_curdate;
        majesticsupport::$_data['filter'][$MJTC_end_date] = $MJTC_fromdate;
        majesticsupport::$_data['filter']['uid'] = $MJTC_id;

        $MJTC_nextdate = $MJTC_fromdate;

        //Query to get Data
        $MJTC_query = "SELECT created FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE status = 1 AND (lastreply = '0000-00-00 00:00:00') AND date(created) >= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_curdate) . "' AND date(created) <= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_fromdate) . "'";
        if($MJTC_id) $MJTC_query .= " AND staffid = ".absint($MJTC_id);
        $MJTC_openticket = majesticsupport::$_db->get_results($MJTC_query);

        $MJTC_query = "SELECT created FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE status = 5 AND date(created) >= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_curdate) . "' AND date(created) <= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_fromdate) . "'";
        if($MJTC_id) $MJTC_query .= " AND staffid = ".absint($MJTC_id);
        $MJTC_closeticket = majesticsupport::$_db->get_results($MJTC_query);

        $MJTC_query = "SELECT created FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE isanswered = 1 AND status != 5 AND status != 1 AND date(created) >= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_curdate) . "' AND date(created) <= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_fromdate) . "'";
        if($MJTC_id) $MJTC_query .= " AND staffid = ".absint($MJTC_id);
        $MJTC_answeredticket = majesticsupport::$_db->get_results($MJTC_query);

        $MJTC_query = "SELECT created FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE isoverdue = 1 AND status != 5 AND date(created) >= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_curdate) . "' AND date(created) <= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_fromdate) . "'";
        if($MJTC_id) $MJTC_query .= " AND staffid = ".absint($MJTC_id);
        $MJTC_overdueticket = majesticsupport::$_db->get_results($MJTC_query);

        $MJTC_query = "SELECT created FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE isanswered != 1 AND status != 5 AND (lastreply != '0000-00-00 00:00:00') AND date(created) >= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_curdate) . "' AND date(created) <= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_fromdate) . "'";
        if($MJTC_id) $MJTC_query .= " AND staffid = ".absint($MJTC_id);
        $MJTC_pendingticket = majesticsupport::$_db->get_results($MJTC_query);

        $MJTC_date_openticket = array();
        $MJTC_date_closeticket = array();
        $MJTC_date_answeredticket = array();
        $MJTC_date_overdueticket = array();
        $MJTC_date_pendingticket = array();
        foreach ($MJTC_openticket AS $MJTC_ticket) {
            if (!isset($MJTC_date_openticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))]))
                $MJTC_date_openticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] = 0;
            $MJTC_date_openticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] = $MJTC_date_openticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] + 1;
        }
        foreach ($MJTC_closeticket AS $MJTC_ticket) {
            if (!isset($MJTC_date_closeticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))]))
                $MJTC_date_closeticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] = 0;
            $MJTC_date_closeticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] = $MJTC_date_closeticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] + 1;
        }
        foreach ($MJTC_answeredticket AS $MJTC_ticket) {
            if (!isset($MJTC_date_answeredticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))]))
                $MJTC_date_answeredticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] = 0;
            $MJTC_date_answeredticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] = $MJTC_date_answeredticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] + 1;
        }
        foreach ($MJTC_overdueticket AS $MJTC_ticket) {
            if (!isset($MJTC_date_overdueticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))]))
                $MJTC_date_overdueticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] = 0;
            $MJTC_date_overdueticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] = $MJTC_date_overdueticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] + 1;
        }
        foreach ($MJTC_pendingticket AS $MJTC_ticket) {
            if (!isset($MJTC_date_pendingticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))]))
                $MJTC_date_pendingticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] = 0;
            $MJTC_date_pendingticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] = $MJTC_date_pendingticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] + 1;
        }
        $MJTC_open_ticket = 0;
        $MJTC_close_ticket = 0;
        $MJTC_answered_ticket = 0;
        $MJTC_overdue_ticket = 0;
        $MJTC_pending_ticket = 0;
        $MJTC_json_array = "";
        do{
            $MJTC_year = date_i18n('Y',MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_nextdate));
            $month = date_i18n('m',MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_nextdate));
            $month = $month - 1; //js month are 0 based
            $MJTC_day = date_i18n('d',MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_nextdate));
            $MJTC_openticket_tmp = isset($MJTC_date_openticket[$MJTC_nextdate]) ? $MJTC_date_openticket[$MJTC_nextdate]  : 0;
            $MJTC_closeticket_tmp = isset($MJTC_date_closeticket[$MJTC_nextdate]) ? $MJTC_date_closeticket[$MJTC_nextdate] : 0;
            $MJTC_answeredticket_tmp = isset($MJTC_date_answeredticket[$MJTC_nextdate]) ? $MJTC_date_answeredticket[$MJTC_nextdate] : 0;
            $MJTC_overdueticket_tmp = isset($MJTC_date_overdueticket[$MJTC_nextdate]) ? $MJTC_date_overdueticket[$MJTC_nextdate] : 0;
            $MJTC_pendingticket_tmp = isset($MJTC_date_pendingticket[$MJTC_nextdate]) ? $MJTC_date_pendingticket[$MJTC_nextdate] : 0;
            $MJTC_json_array .= "[new Date($MJTC_year,$month,$MJTC_day),$MJTC_openticket_tmp,$MJTC_answeredticket_tmp,$MJTC_pendingticket_tmp,$MJTC_overdueticket_tmp,$MJTC_closeticket_tmp],";
            $MJTC_open_ticket += $MJTC_openticket_tmp;
            $MJTC_close_ticket += $MJTC_closeticket_tmp;
            $MJTC_answered_ticket += $MJTC_answeredticket_tmp;
            $MJTC_overdue_ticket += $MJTC_overdueticket_tmp;
            $MJTC_pending_ticket += $MJTC_pendingticket_tmp;
             if($MJTC_nextdate == $MJTC_curdate){
                break;
            }
            $MJTC_nextdate = date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_nextdate . " -1 days"));
        }while($MJTC_nextdate != $MJTC_curdate);

        majesticsupport::$_data['line_chart_json_array'] = $MJTC_json_array;

        $MJTC_query = "SELECT staff.photo,staff.id,staff.uid,staff.firstname,staff.lastname,staff.username,staff.email,user.display_name,user.user_email,user.user_nicename,
                    (SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE date(created) >= '" . sanitize_text_field($MJTC_curdate) . "' AND date(created) <= '" . sanitize_text_field($MJTC_fromdate) . "' AND staffid = staff.id) AS allticket,
                    (SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE status = 1 AND (lastreply = '0000-00-00 00:00:00') AND date(created) >= '" . sanitize_text_field($MJTC_curdate) . "' AND date(created) <= '" . sanitize_text_field($MJTC_fromdate) . "' AND staffid = staff.id) AS openticket,
                    (SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE status = 5 AND date(created) >= '" . sanitize_text_field($MJTC_curdate) . "' AND date(created) <= '" . sanitize_text_field($MJTC_fromdate) . "' AND staffid = staff.id) AS closeticket,
                    (SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE isanswered = 1 AND status != 5 AND status != 1 AND date(created) >= '" . sanitize_text_field($MJTC_curdate) . "' AND date(created) <= '" . sanitize_text_field($MJTC_fromdate) . "' AND staffid = staff.id) AS answeredticket,
                    (SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE isoverdue = 1 AND status != 5 AND date(created) >= '" . sanitize_text_field($MJTC_curdate) . "' AND date(created) <= '" . sanitize_text_field($MJTC_fromdate) . "' AND staffid = staff.id) AS overdueticket,
                    (SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE isanswered != 1 AND status != 5 AND (lastreply != '0000-00-00 00:00:00') AND date(created) >= '" . sanitize_text_field($MJTC_curdate) . "' AND date(created) <= '" . sanitize_text_field($MJTC_fromdate) . "' AND staffid = staff.id) AS pendingticket   ";
                    if(in_array('feedback', majesticsupport::$_active_addons)){
                        $MJTC_query .=    ",(SELECT AVG(feed.rating) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_feedbacks` AS feed JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket ON ticket.id= feed.ticketid WHERE date(feed.created) >= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_curdate) . "' AND date(feed.created) <= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_fromdate) . "' AND ticket.staffid = staff.id) AS avragerating ";
                    }
                    $MJTC_query .=  "FROM `".majesticsupport::$_db->prefix."mjtc_support_staff` AS staff
                    JOIN `".majesticsupport::$_wpprefixforuser."mjtc_support_users` AS user ON user.id = staff.uid
                    WHERE staff.id = ".absint($MJTC_id);

        $MJTC_agent = majesticsupport::$_db->get_row($MJTC_query);
        if(!empty($MJTC_agent)){
            if(in_array('timetracking', majesticsupport::$_active_addons)){
                $MJTC_agent->time = MJTC_includer::MJTC_getModel('timetracking')->getAverageTimeByStaffId($MJTC_agent->id);// time 0 contains avergage time in seconds and 1 contains wheter it is conflicted or not
            }
        }

        majesticsupport::$_data['staff_report'] = $MJTC_agent;
        // ticket ids for staff member on which he replied but are not assigned to him
        $MJTC_ticketid_string = '';
        if(in_array('timetracking', majesticsupport::$_active_addons)){
            $MJTC_query = "SELECT DISTINCT(ticketid) AS ticketid
                        FROM `".majesticsupport::$_db->prefix."mjtc_support_staff_time`
                        WHERE staffid = ".absint($MJTC_id);
            $MJTC_all_tickets = majesticsupport::$_db->get_results($MJTC_query);
            $MJTC_comma = '';
            foreach ($MJTC_all_tickets as $MJTC_ticket) {
                $MJTC_ticketid_string .= $MJTC_comma . absint($MJTC_ticket->ticketid);
                $MJTC_comma = ', ';
            }
        }

        if($MJTC_ticketid_string == ''){
            $MJTC_q_strig = "(staffid = ".absint($MJTC_id).")";
        }else{
            $MJTC_q_strig = "(staffid = ".absint($MJTC_id)." OR ticket.id IN (" . $MJTC_ticketid_string . "))";
        }

        // Pagination
        $MJTC_query = "SELECT COUNT(ticket.id)
                    FROM `".majesticsupport::$_db->prefix."mjtc_support_tickets` AS ticket
                    LEFT JOIN `".majesticsupport::$_db->prefix."mjtc_support_priorities` AS priority ON priority.id = ticket.priorityid
                    WHERE " . $MJTC_q_strig . " AND date(ticket.created) >= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_curdate) . "' AND date(ticket.created) <= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_fromdate) . "' ";
        $total = majesticsupport::$_db->get_var($MJTC_query);
        majesticsupport::$_data['total'] = $total;
        majesticsupport::$_data[1] = MJTC_pagination::MJTC_getPagination($total,'staffdetailreport');

        //Tickets
        do_action('MJTC_FeedbackQueryStaff');
        $MJTC_query = "SELECT ticket.*,priority.priority, priority.prioritycolour,status.status AS statustitle,status.statuscolour,status.statusbgcolour ".majesticsupport::$_addon_query['select']."
                    FROM `".majesticsupport::$_db->prefix."mjtc_support_tickets` AS ticket
                    LEFT JOIN `".majesticsupport::$_db->prefix."mjtc_support_priorities` AS priority ON priority.id = ticket.priorityid
                    JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_statuses` AS status ON ticket.status = status.id
                    ".majesticsupport::$_addon_query['join']."
                    WHERE " . $MJTC_q_strig . " AND date(ticket.created) >= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_curdate) . "' AND date(ticket.created) <= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_fromdate) . "' ";
        $MJTC_query .= " LIMIT " . MJTC_pagination::MJTC_getOffset() . ", " . MJTC_pagination::MJTC_getLimit();
        do_action('MJTC_reset_addon_query');
        majesticsupport::$_data['staff_tickets'] = majesticsupport::$_db->get_results($MJTC_query);

        if(in_array('timetracking', majesticsupport::$_active_addons)){
            foreach (majesticsupport::$_data['staff_tickets'] as $MJTC_ticket) {
                 $MJTC_ticket->time = MJTC_includer::MJTC_getModel('timetracking')->getTimeTakenByTicketIdAndStaffid($MJTC_ticket->id,$MJTC_id);// second parameter is staff id
            }
        }
        return;
    }

    function getDepartmentDetailReportByDepartmentId($MJTC_id){
        if(!is_numeric($MJTC_id)) return false;

        $MJTC_start_date ='date_start';
        $MJTC_end_date ='date_end';

        $MJTC_date_start = isset(majesticsupport::$_search['report']) ? majesticsupport::$_search['report']['date_start'] : '';
        $MJTC_date_end = isset(majesticsupport::$_search['report']) ? majesticsupport::$_search['report']['date_end'] : '';
        if(isset($MJTC_date_start) && $MJTC_date_start != ""){
            $MJTC_date_start = date_i18n('Y-m-d',MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_date_start));
        }
        if(isset($MJTC_date_end) && $MJTC_date_end != ""){
            $MJTC_date_end = date_i18n('Y-m-d',MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_date_end));
        }
        if($MJTC_date_start > $MJTC_date_end){
            $tmp = $MJTC_date_start;
            $MJTC_date_start = $MJTC_date_end;
            $MJTC_date_end = $tmp;
        }

        //Line Chart Data
        $MJTC_curdate = ($MJTC_date_start != null) ? date_i18n('Y-m-d',MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_date_start)) : date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime("now -1 month"));
        $MJTC_fromdate = ($MJTC_date_end != null) ? date_i18n('Y-m-d',MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_date_end)) : date_i18n('Y-m-d');
        majesticsupport::$_data['filter'][$MJTC_start_date] = $MJTC_curdate;
        majesticsupport::$_data['filter'][$MJTC_end_date] = $MJTC_fromdate;
        majesticsupport::$_data['filter']['id'] = $MJTC_id;

        $MJTC_nextdate = $MJTC_fromdate;

        //Query to get Data
        $MJTC_query = "SELECT created FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE status = 1 AND (lastreply = '0000-00-00 00:00:00') AND date(created) >= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_curdate) . "' AND date(created) <= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_fromdate) . "'";
        if($MJTC_id) $MJTC_query .= " AND departmentid = ".absint($MJTC_id);
        $MJTC_openticket = majesticsupport::$_db->get_results($MJTC_query);

        $MJTC_query = "SELECT created FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE status = 5 AND date(created) >= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_curdate) . "' AND date(created) <= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_fromdate) . "'";
        if($MJTC_id) $MJTC_query .= " AND departmentid = ".absint($MJTC_id);
        $MJTC_closeticket = majesticsupport::$_db->get_results($MJTC_query);

        $MJTC_query = "SELECT created FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE isanswered = 1 AND status != 5 AND status != 1 AND date(created) >= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_curdate) . "' AND date(created) <= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_fromdate) . "'";
        if($MJTC_id) $MJTC_query .= " AND departmentid = ".absint($MJTC_id);
        $MJTC_answeredticket = majesticsupport::$_db->get_results($MJTC_query);

        $MJTC_query = "SELECT created FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE isoverdue = 1 AND status != 5 AND date(created) >= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_curdate) . "' AND date(created) <= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_fromdate) . "'";
        if($MJTC_id) $MJTC_query .= " AND departmentid = ".absint($MJTC_id);
        $MJTC_overdueticket = majesticsupport::$_db->get_results($MJTC_query);

        $MJTC_query = "SELECT created FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE isanswered != 1 AND status != 5 AND (lastreply != '0000-00-00 00:00:00') AND date(created) >= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_curdate) . "' AND date(created) <= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_fromdate) . "'";
        if($MJTC_id) $MJTC_query .= " AND departmentid = ".absint($MJTC_id);
        $MJTC_pendingticket = majesticsupport::$_db->get_results($MJTC_query);

        $MJTC_date_openticket = array();
        $MJTC_date_closeticket = array();
        $MJTC_date_answeredticket = array();
        $MJTC_date_overdueticket = array();
        $MJTC_date_pendingticket = array();
        foreach ($MJTC_openticket AS $MJTC_ticket) {
            if (!isset($MJTC_date_openticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))]))
                $MJTC_date_openticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] = 0;
            $MJTC_date_openticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] = $MJTC_date_openticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] + 1;
        }
        foreach ($MJTC_closeticket AS $MJTC_ticket) {
            if (!isset($MJTC_date_closeticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))]))
                $MJTC_date_closeticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] = 0;
            $MJTC_date_closeticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] = $MJTC_date_closeticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] + 1;
        }
        foreach ($MJTC_answeredticket AS $MJTC_ticket) {
            if (!isset($MJTC_date_answeredticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))]))
                $MJTC_date_answeredticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] = 0;
            $MJTC_date_answeredticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] = $MJTC_date_answeredticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] + 1;
        }
        foreach ($MJTC_overdueticket AS $MJTC_ticket) {
            if (!isset($MJTC_date_overdueticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))]))
                $MJTC_date_overdueticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] = 0;
            $MJTC_date_overdueticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] = $MJTC_date_overdueticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] + 1;
        }
        foreach ($MJTC_pendingticket AS $MJTC_ticket) {
            if (!isset($MJTC_date_pendingticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))]))
                $MJTC_date_pendingticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] = 0;
            $MJTC_date_pendingticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] = $MJTC_date_pendingticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] + 1;
        }
        $MJTC_open_ticket = 0;
        $MJTC_close_ticket = 0;
        $MJTC_answered_ticket = 0;
        $MJTC_overdue_ticket = 0;
        $MJTC_pending_ticket = 0;
        $MJTC_json_array = "";
        do{
            $MJTC_year = date_i18n('Y',MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_nextdate));
            $month = date_i18n('m',MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_nextdate));
            $month = $month - 1; //js month are 0 based
            $MJTC_day = date_i18n('d',MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_nextdate));
            $MJTC_openticket_tmp = isset($MJTC_date_openticket[$MJTC_nextdate]) ? $MJTC_date_openticket[$MJTC_nextdate]  : 0;
            $MJTC_closeticket_tmp = isset($MJTC_date_closeticket[$MJTC_nextdate]) ? $MJTC_date_closeticket[$MJTC_nextdate] : 0;
            $MJTC_answeredticket_tmp = isset($MJTC_date_answeredticket[$MJTC_nextdate]) ? $MJTC_date_answeredticket[$MJTC_nextdate] : 0;
            $MJTC_overdueticket_tmp = isset($MJTC_date_overdueticket[$MJTC_nextdate]) ? $MJTC_date_overdueticket[$MJTC_nextdate] : 0;
            $MJTC_pendingticket_tmp = isset($MJTC_date_pendingticket[$MJTC_nextdate]) ? $MJTC_date_pendingticket[$MJTC_nextdate] : 0;
            $MJTC_json_array .= "[new Date($MJTC_year,$month,$MJTC_day),$MJTC_openticket_tmp,$MJTC_answeredticket_tmp,$MJTC_pendingticket_tmp,$MJTC_overdueticket_tmp,$MJTC_closeticket_tmp],";
            $MJTC_open_ticket += $MJTC_openticket_tmp;
            $MJTC_close_ticket += $MJTC_closeticket_tmp;
            $MJTC_answered_ticket += $MJTC_answeredticket_tmp;
            $MJTC_overdue_ticket += $MJTC_overdueticket_tmp;
            $MJTC_pending_ticket += $MJTC_pendingticket_tmp;
             if($MJTC_nextdate == $MJTC_curdate){
                break;
            }
            $MJTC_nextdate = date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_nextdate . " -1 days"));
        }while($MJTC_nextdate != $MJTC_curdate);

        majesticsupport::$_data['line_chart_json_array'] = $MJTC_json_array;


        // Pagination
        $MJTC_query = "SELECT count(ticket.id)
                    FROM `".majesticsupport::$_db->prefix."mjtc_support_tickets` AS ticket
                    JOIN `".majesticsupport::$_db->prefix."mjtc_support_departments` AS department ON department.id = ticket.departmentid WHERE department.id = ".absint($MJTC_id)." AND date(ticket.created) >= '" . sanitize_text_field($MJTC_curdate) . "' AND date(ticket.created) <= '" . sanitize_text_field($MJTC_fromdate) . "' ";
        $total = majesticsupport::$_db->get_var($MJTC_query);
        majesticsupport::$_data['total'] = $total;
        majesticsupport::$_data[1] = MJTC_pagination::MJTC_getPagination($total);

        $MJTC_query = "SELECT department.id,department.departmentname,email.email,
                    (SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE departmentid = department.id) AS allticket,
                    (SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE status = 1 AND (lastreply = '0000-00-00 00:00:00') AND date(created) >= '" . sanitize_text_field($MJTC_curdate) . "' AND date(created) <= '" . sanitize_text_field($MJTC_fromdate) . "' AND departmentid = department.id) AS openticket,
                    (SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE status = 5 AND date(created) >= '" . sanitize_text_field($MJTC_curdate) . "' AND date(created) <= '" . sanitize_text_field($MJTC_fromdate) . "' AND departmentid = department.id) AS closeticket,
                    (SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE isanswered = 1 AND status != 5 AND status != 1 AND date(created) >= '" . sanitize_text_field($MJTC_curdate) . "' AND date(created) <= '" . sanitize_text_field($MJTC_fromdate) . "' AND departmentid = department.id) AS answeredticket,
                    (SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE isoverdue = 1 AND status != 5 AND date(created) >= '" . sanitize_text_field($MJTC_curdate) . "' AND date(created) <= '" . sanitize_text_field($MJTC_fromdate) . "' AND departmentid = department.id) AS overdueticket,
                    (SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE isanswered != 1 AND status != 5 AND (lastreply != '0000-00-00 00:00:00') AND date(created) >= '" . sanitize_text_field($MJTC_curdate) . "' AND date(created) <= '" . sanitize_text_field($MJTC_fromdate) . "' AND departmentid = department.id) AS pendingticket
                    FROM `".majesticsupport::$_db->prefix."mjtc_support_departments` AS department
                    JOIN `".majesticsupport::$_db->prefix."mjtc_support_email` AS email ON department.emailid = email.id
                    WHERE department.id = ".absint($MJTC_id);
        $MJTC_depatments = majesticsupport::$_db->get_row($MJTC_query);
        majesticsupport::$_data['depatments_report'] =$MJTC_depatments;

        //Tickets
        do_action('MJTC_FeedbackQueryStaff');
        $MJTC_query = "SELECT ticket.*,priority.priority, priority.prioritycolour,status.status AS statustitle,status.statuscolour,status.statusbgcolour ".majesticsupport::$_addon_query['select']."
                    FROM `".majesticsupport::$_db->prefix."mjtc_support_tickets` AS ticket
                    LEFT JOIN `".majesticsupport::$_db->prefix."mjtc_support_priorities` AS priority ON priority.id = ticket.priorityid
                    JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_statuses` AS status ON ticket.status = status.id
                    ".majesticsupport::$_addon_query['join']."
                    WHERE departmentid = ".absint($MJTC_id)." AND date(ticket.created) >= '" . sanitize_text_field($MJTC_curdate) . "' AND date(ticket.created) <= '" . sanitize_text_field($MJTC_fromdate) . "' ";
        $MJTC_query .= " LIMIT " . MJTC_pagination::MJTC_getOffset() . ", " . MJTC_pagination::MJTC_getLimit();
        do_action('MJTC_reset_addon_query');

        majesticsupport::$_data['department_tickets'] = majesticsupport::$_db->get_results($MJTC_query);
        if(in_array('timetracking', majesticsupport::$_active_addons)){
            foreach (majesticsupport::$_data['department_tickets'] as $MJTC_ticket) {
                 $MJTC_ticket->time = MJTC_includer::MJTC_getModel('timetracking')->getTimeTakenByTicketId($MJTC_ticket->id);
            }
        }
    }


    function getStaffDetailReportByUserId($MJTC_id){
        if(!is_numeric($MJTC_id)) return false;

        $MJTC_date_start = isset(majesticsupport::$_search['report']) ? majesticsupport::$_search['report']['date_start'] : '';
        $MJTC_date_end = isset(majesticsupport::$_search['report']) ? majesticsupport::$_search['report']['date_end'] : '';
        if(isset($MJTC_date_start) && $MJTC_date_start != ""){
            $MJTC_date_start = date_i18n('Y-m-d',MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_date_start));
        }
        if(isset($MJTC_date_end) && $MJTC_date_end != ""){
            $MJTC_date_end = date_i18n('Y-m-d',MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_date_end));
        }
        if($MJTC_date_start > $MJTC_date_end){
            $tmp = $MJTC_date_start;
            $MJTC_date_start = $MJTC_date_end;
            $MJTC_date_end = $tmp;
        }
        //Line Chart Data
        $MJTC_curdate = ($MJTC_date_start != null) ? date_i18n('Y-m-d',MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_date_start)) : date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime("now -1 month"));
        $MJTC_fromdate = ($MJTC_date_end != null) ? date_i18n('Y-m-d',MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_date_end)) : date_i18n('Y-m-d');
        majesticsupport::$_data['filter']['date_start'] = $MJTC_curdate;
        majesticsupport::$_data['filter']['date_end'] = $MJTC_fromdate;
        majesticsupport::$_data['filter']['uid'] = $MJTC_id;
        $MJTC_nextdate = $MJTC_fromdate;

        // forexport
        $_SESSION['forexport']['curdate'] = $MJTC_curdate;
        $_SESSION['forexport']['fromdate'] = $MJTC_fromdate;
        $_SESSION['forexport']['id'] = $MJTC_id;


        //Query to get Data
        $MJTC_query = "SELECT created FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE status = 1 AND (lastreply = '0000-00-00 00:00:00') AND date(created) >= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_curdate) . "' AND date(created) <= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_fromdate) . "'";
        if($MJTC_id) $MJTC_query .= " AND uid = ".absint($MJTC_id);
        $MJTC_openticket = majesticsupport::$_db->get_results($MJTC_query);

        $MJTC_query = "SELECT created FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE status = 5 AND date(created) >= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_curdate) . "' AND date(created) <= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_fromdate) . "'";
        if($MJTC_id) $MJTC_query .= " AND uid = ".absint($MJTC_id);
        $MJTC_closeticket = majesticsupport::$_db->get_results($MJTC_query);

        $MJTC_query = "SELECT created FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE isanswered = 1 AND status != 5 AND status != 1 AND date(created) >= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_curdate) . "' AND date(created) <= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_fromdate) . "'";
        if($MJTC_id) $MJTC_query .= " AND uid = ".absint($MJTC_id);
        $MJTC_answeredticket = majesticsupport::$_db->get_results($MJTC_query);

        $MJTC_query = "SELECT created FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE isoverdue = 1 AND status != 5 AND date(created) >= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_curdate) . "' AND date(created) <= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_fromdate) . "'";
        if($MJTC_id) $MJTC_query .= " AND uid = ".absint($MJTC_id);
        $MJTC_overdueticket = majesticsupport::$_db->get_results($MJTC_query);

        $MJTC_query = "SELECT created FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE isanswered != 1 AND status != 5 AND (lastreply != '0000-00-00 00:00:00') AND date(created) >= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_curdate) . "' AND date(created) <= '" . MJTC_majesticsupportphplib::MJTC_sql_string($MJTC_fromdate) . "'";
        if($MJTC_id) $MJTC_query .= " AND uid = ".absint($MJTC_id);
        $MJTC_pendingticket = majesticsupport::$_db->get_results($MJTC_query);

        $MJTC_date_openticket = array();
        $MJTC_date_closeticket = array();
        $MJTC_date_answeredticket = array();
        $MJTC_date_overdueticket = array();
        $MJTC_date_pendingticket = array();
        foreach ($MJTC_openticket AS $MJTC_ticket) {
            if (!isset($MJTC_date_openticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))]))
                $MJTC_date_openticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] = 0;
            $MJTC_date_openticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] = $MJTC_date_openticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] + 1;
        }
        foreach ($MJTC_closeticket AS $MJTC_ticket) {
            if (!isset($MJTC_date_closeticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))]))
                $MJTC_date_closeticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] = 0;
            $MJTC_date_closeticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] = $MJTC_date_closeticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] + 1;
        }
        foreach ($MJTC_answeredticket AS $MJTC_ticket) {
            if (!isset($MJTC_date_answeredticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))]))
                $MJTC_date_answeredticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] = 0;
            $MJTC_date_answeredticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] = $MJTC_date_answeredticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] + 1;
        }
        foreach ($MJTC_overdueticket AS $MJTC_ticket) {
            if (!isset($MJTC_date_overdueticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))]))
                $MJTC_date_overdueticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] = 0;
            $MJTC_date_overdueticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] = $MJTC_date_overdueticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] + 1;
        }
        foreach ($MJTC_pendingticket AS $MJTC_ticket) {
            if (!isset($MJTC_date_pendingticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))]))
                $MJTC_date_pendingticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] = 0;
            $MJTC_date_pendingticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] = $MJTC_date_pendingticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] + 1;
        }
        $MJTC_open_ticket = 0;
        $MJTC_close_ticket = 0;
        $MJTC_answered_ticket = 0;
        $MJTC_overdue_ticket = 0;
        $MJTC_pending_ticket = 0;
        $MJTC_json_array = "";
        do{
            $MJTC_year = date_i18n('Y',MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_nextdate));
            $month = date_i18n('m',MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_nextdate));
            $month = $month - 1; //js month are 0 based
            $MJTC_day = date_i18n('d',MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_nextdate));
            $MJTC_openticket_tmp = isset($MJTC_date_openticket[$MJTC_nextdate]) ? $MJTC_date_openticket[$MJTC_nextdate]  : 0;
            $MJTC_closeticket_tmp = isset($MJTC_date_closeticket[$MJTC_nextdate]) ? $MJTC_date_closeticket[$MJTC_nextdate] : 0;
            $MJTC_answeredticket_tmp = isset($MJTC_date_answeredticket[$MJTC_nextdate]) ? $MJTC_date_answeredticket[$MJTC_nextdate] : 0;
            $MJTC_overdueticket_tmp = isset($MJTC_date_overdueticket[$MJTC_nextdate]) ? $MJTC_date_overdueticket[$MJTC_nextdate] : 0;
            $MJTC_pendingticket_tmp = isset($MJTC_date_pendingticket[$MJTC_nextdate]) ? $MJTC_date_pendingticket[$MJTC_nextdate] : 0;
            $MJTC_json_array .= "[new Date($MJTC_year,$month,$MJTC_day),$MJTC_openticket_tmp,$MJTC_answeredticket_tmp,$MJTC_pendingticket_tmp,$MJTC_overdueticket_tmp,$MJTC_closeticket_tmp],";
            $MJTC_open_ticket += $MJTC_openticket_tmp;
            $MJTC_close_ticket += $MJTC_closeticket_tmp;
            $MJTC_answered_ticket += $MJTC_answeredticket_tmp;
            $MJTC_overdue_ticket += $MJTC_overdueticket_tmp;
            $MJTC_pending_ticket += $MJTC_pendingticket_tmp;
             if($MJTC_nextdate == $MJTC_curdate){
                break;
            }
            $MJTC_nextdate = date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_nextdate . " -1 days"));
        }while($MJTC_nextdate != $MJTC_curdate);

        majesticsupport::$_data['line_chart_json_array'] = $MJTC_json_array;

        $MJTC_query = "SELECT user.display_name,user.user_email,user.user_nicename,user.id,
                    (SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE uid = user.id) AS allticket,
                    (SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE status = 1  AND (lastreply = '0000-00-00 00:00:00') AND date(created) >= '" . sanitize_text_field($MJTC_curdate) . "' AND date(created) <= '" . sanitize_text_field($MJTC_fromdate) . "' AND uid = user.id) AS openticket,
                    (SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE status = 5 AND date(created) >= '" . sanitize_text_field($MJTC_curdate) . "' AND date(created) <= '" . sanitize_text_field($MJTC_fromdate) . "' AND uid = user.id) AS closeticket,
                    (SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE isanswered = 1 AND status != 5 AND status != 1 AND date(created) >= '" . sanitize_text_field($MJTC_curdate) . "' AND date(created) <= '" . sanitize_text_field($MJTC_fromdate) . "' AND uid = user.id) AS answeredticket,
                    (SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE isoverdue = 1 AND status != 5 AND date(created) >= '" . sanitize_text_field($MJTC_curdate) . "' AND date(created) <= '" . sanitize_text_field($MJTC_fromdate) . "' AND uid = user.id) AS overdueticket,
                    (SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE isanswered != 1 AND status != 5 AND isoverdue = 1 AND (lastreply != '0000-00-00 00:00:00') AND date(created) >= '" . sanitize_text_field($MJTC_curdate) . "' AND date(created) <= '" . sanitize_text_field($MJTC_fromdate) . "' AND uid = user.id) AS pendingticket
                    FROM `".majesticsupport::$_wpprefixforuser."mjtc_support_users` AS user
                    WHERE user.id = ".absint($MJTC_id);
        $MJTC_agent = majesticsupport::$_db->get_row($MJTC_query);
        majesticsupport::$_data['user_report'] =$MJTC_agent;
        // Pagination
        $MJTC_query = "SELECT COUNT(ticket.id)
                    FROM `".majesticsupport::$_db->prefix."mjtc_support_tickets` AS ticket
                    LEFT JOIN `".majesticsupport::$_db->prefix."mjtc_support_priorities` AS priority ON priority.id = ticket.priorityid
                    WHERE uid = ".absint($MJTC_id)." AND date(ticket.created) >= '" . sanitize_text_field($MJTC_curdate) . "' AND date(ticket.created) <= '" . sanitize_text_field($MJTC_fromdate) . "' ";
        $total = majesticsupport::$_db->get_var($MJTC_query);
        majesticsupport::$_data['total'] = $total;
        majesticsupport::$_data[1] = MJTC_pagination::MJTC_getPagination($total);
        //Tickets
        do_action('MJTC_FeedbackQueryStaff');
        $MJTC_query = "SELECT ticket.*,priority.priority, priority.prioritycolour,status.status AS statustitle,status.statuscolour,status.statusbgcolour ".majesticsupport::$_addon_query['select']."
                    FROM `".majesticsupport::$_db->prefix."mjtc_support_tickets` AS ticket
                    LEFT JOIN `".majesticsupport::$_db->prefix."mjtc_support_priorities` AS priority ON priority.id = ticket.priorityid
                    JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_statuses` AS status ON ticket.status = status.id
                    ".majesticsupport::$_addon_query['join']."
                    WHERE uid = ".absint($MJTC_id)." AND date(ticket.created) >= '" . sanitize_text_field($MJTC_curdate) . "' AND date(ticket.created) <= '" . sanitize_text_field($MJTC_fromdate) . "' ";
        $MJTC_query .= " LIMIT " . MJTC_pagination::MJTC_getOffset() . ", " . MJTC_pagination::MJTC_getLimit();
        do_action('MJTC_reset_addon_query');
        majesticsupport::$_data['user_tickets'] = majesticsupport::$_db->get_results($MJTC_query);
        if(in_array('timetracking', majesticsupport::$_active_addons)){
            foreach (majesticsupport::$_data['user_tickets'] as $MJTC_ticket) {
                 $MJTC_ticket->time = MJTC_includer::MJTC_getModel('timetracking')->getTimeTakenByTicketId($MJTC_ticket->id);
            }
        }
        return;
    }

    function getStaffTimingReportById($MJTC_id){
        if( !in_array('agent',majesticsupport::$_active_addons) ){
            return;
        }
        if(!is_numeric($MJTC_id)) return false;

        $MJTC_start_date ='date_start';
        $MJTC_end_date ='date_end';

        $MJTC_date_start = isset(majesticsupport::$_search['report']) ? majesticsupport::$_search['report'][$MJTC_start_date] : '';
        $MJTC_date_end = isset(majesticsupport::$_search['report']) ? majesticsupport::$_search['report'][$MJTC_end_date] : '';
        if($MJTC_date_start > $MJTC_date_end){
            $tmp = $MJTC_date_start;
            $MJTC_date_start = $MJTC_date_end;
            $MJTC_date_end = $tmp;
        }

        //Line Chart Data
        $MJTC_curdate = ($MJTC_date_start != null) ? date_i18n('Y-m-d',MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_date_start)) : date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime("now -1 month"));
        $MJTC_fromdate = ($MJTC_date_end != null) ? date_i18n('Y-m-d',MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_date_end)) : date_i18n('Y-m-d');
        majesticsupport::$_data['filter'][$MJTC_start_date] = $MJTC_curdate;
        majesticsupport::$_data['filter'][$MJTC_end_date] = $MJTC_fromdate;
        majesticsupport::$_data['filter']['id'] = $MJTC_id;

        $MJTC_nextdate = $MJTC_fromdate;

        //Query to get Data
        if(in_array('timetracking', majesticsupport::$_active_addons)){
            $MJTC_query = "SELECT created,usertime FROM `" . majesticsupport::$_db->prefix . "mjtc_support_staff_time` ";
            $MJTC_query .= " WHERE staffid = ".absint($MJTC_id);
            $MJTC_openticket = majesticsupport::$_db->get_results($MJTC_query);
        }else{
            $MJTC_openticket = array();
        }

        $MJTC_date_openticket = array();
        foreach ($MJTC_openticket AS $MJTC_ticket) {
            if (!isset($MJTC_date_openticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))]))
                $MJTC_date_openticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] = 0;
            $MJTC_date_openticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] = $MJTC_date_openticket[date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))] + $MJTC_ticket->usertime;
        }
        $MJTC_open_ticket = 0;
        $MJTC_json_array = "";
        do{
            $MJTC_year = date_i18n('Y',MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_nextdate));
            $month = date_i18n('m',MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_nextdate));
            $month = $month - 1; //js month are 0 based
            $MJTC_day = date_i18n('d',MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_nextdate));
            if(isset($MJTC_date_openticket[$MJTC_nextdate])){

                $MJTC_mins = floor($MJTC_date_openticket[$MJTC_nextdate] / 60);
                $MJTC_openticket_tmp =  $MJTC_mins;
            }else{
                $MJTC_openticket_tmp =  0;
            }
            $MJTC_json_array .= '[new Date('.$MJTC_year.','.$month.','.$MJTC_day.'),'.$MJTC_openticket_tmp.'],';
            $MJTC_nextdate = date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_nextdate . " -1 days"));
        }while($MJTC_nextdate != $MJTC_curdate);
        majesticsupport::$_data['line_chart_json_array'] = $MJTC_json_array;
        majesticsupport::$_data[0]['staffname'] = MJTC_includer::MJTC_getModel('agent')->getMyName($MJTC_id);

    }


}
?>
