<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_majesticsupportModel {

    function getControlPanelData() {

        //determine user
        $MJTC_user_is = 'unknown';
        if(MJTC_includer::MJTC_getObjectClass('user')->MJTC_isguest()){
            $MJTC_user_is = 'visitor';
        }else{
            if(in_array('agent', majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()){
                $MJTC_user_is = 'agent';
            }else{
                $MJTC_user_is = 'user';
            }
        }
        //check if any addon is installed
        $MJTC_addon_are_installed = !empty(majesticsupport::$_active_addons) ? true : false;

        if( $MJTC_user_is == 'agent' ){

            $MJTC_uid = MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid();
            $MJTC_staffid = MJTC_includer::MJTC_getModel('agent')->getStaffId($MJTC_uid);

            $MJTC_tickets = $this->getAgentLatestTicketsForCp($MJTC_staffid);
            majesticsupport::$_data[0]['agent-tickets'] = $MJTC_tickets;

            $MJTC_tickets = $this->getAgentAssignedTicketsForCp($MJTC_staffid);
            if($MJTC_tickets){
                majesticsupport::$_data[0]['agent-assigned-tickets'] = $MJTC_tickets;
            }

            $MJTC_ticketStats = $this->getAgentTicketStats($MJTC_staffid);
            if($MJTC_ticketStats){
                majesticsupport::$_data[0]['count'] = $MJTC_ticketStats;
            }

            //data for graph
            $this->getAgentCpChartData($MJTC_staffid);

            // top used departments
            $total_tickets = (int) majesticsupport::$_data[0]['count']['allticket'];
            $MJTC_deptResults = $this->getAgentDeptResults($MJTC_staffid, $total_tickets);
            if($MJTC_deptResults){
                majesticsupport::$_data[0]['department-tickets'] = $MJTC_deptResults;
            }

            // Recent Activity
            $MJTC_recentActivity = $this->getAgentRecentActivity($MJTC_staffid);
            if($MJTC_recentActivity){
                majesticsupport::$_data['action_history'] = $MJTC_recentActivity;
            }

            // Recent Activity
            if (in_array('feedback', majesticsupport::$_active_addons)) {
                $this->getAgentRecentFeedback($MJTC_staffid);
            }

            // Weekly Leaderboard
            $MJTC_weeklyLeaderboard = $this->getAgentWeeklyLeaderboard($MJTC_staffid);
            if($MJTC_weeklyLeaderboard){
                majesticsupport::$_data['leaderboard_agents'] = $MJTC_weeklyLeaderboard;
            }

            // Weekly Leaderboard
            $MJTC_weeklyLeaderboard = $this->getAgentVipWatchlist($MJTC_staffid);
            if($MJTC_weeklyLeaderboard){
                majesticsupport::$_data['vip_watchlist'] = $MJTC_weeklyLeaderboard;
            }

            // Agent Velocity Card
            
            $this->getAgentDailyVelocity($MJTC_staffid);

            // profile
            majesticsupport::$_data[0]['agentid'] = $MJTC_uid;
            // Query in your specific style: Join staff with acl_roles
            $MJTC_agent_query = "SELECT staff.*, roles.name as role_name 
                FROM `" . majesticsupport::$_db->prefix . "mjtc_support_staff` AS staff
                LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_acl_roles` AS roles ON staff.roleid = roles.id
                WHERE staff.id = " . esc_sql($MJTC_staffid);

            $MJTC_agent_data = majesticsupport::$_db->get_row($MJTC_agent_query);

            if ($MJTC_agent_data) {
                $MJTC_role = $MJTC_agent_data->email;
                $MJTC_name = $MJTC_agent_data->firstname.' '.$MJTC_agent_data->lastname;
                majesticsupport::$_data[0]['agentname'] = $MJTC_name;
                majesticsupport::$_data[0]['agentrole'] = $MJTC_role;
            }
        }

        if( $MJTC_user_is == 'user' ){
            $MJTC_uid = MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid();

            $MJTC_tickets = $this->getUserLatestTicketsForCp($MJTC_uid);
            majesticsupport::$_data[0]['user-tickets'] = $MJTC_tickets;

            $MJTC_ticketStats = $this->getUserTicketStats($MJTC_uid);

            if($MJTC_ticketStats){
                majesticsupport::$_data[0]['count'] = $MJTC_ticketStats;
            }

            // top used departments
            $total_tickets = (int) majesticsupport::$_data[0]['count']['allticket'];
            $MJTC_deptResults = $this->getUserDeptResults($MJTC_uid, $total_tickets);
            if($MJTC_deptResults){
                majesticsupport::$_data[0]['department-tickets'] = $MJTC_deptResults;
            }

            // Recent Activity
            if(in_array('tickethistory', majesticsupport::$_active_addons)){
                $MJTC_recentActivity = $this->getUserRecentActivity($MJTC_uid);
                if($MJTC_recentActivity){
                    majesticsupport::$_data['action_history'] = $MJTC_recentActivity;
                }
            }

            // user Velocity Card
            
            $this->getUserDailyVelocity($MJTC_uid);

            // profile
            majesticsupport::$_data[0]['userid'] = $MJTC_uid;
            majesticsupport::$_data[0]['username'] = MJTC_includer::MJTC_getObjectClass('user')->MJTC_fullname();;
            majesticsupport::$_data[0]['useremail'] = MJTC_includer::MJTC_getObjectClass('user')->MJTC_emailaddress();
        }

        if( ( $MJTC_user_is == 'user' || $MJTC_user_is == 'visitor' || $MJTC_user_is == 'agent' ) && $MJTC_addon_are_installed ){

            $MJTC_downloads = $this->getLatestDownloadsForCp();
            majesticsupport::$_data[0]['latest-downloads'] = $MJTC_downloads;

            $MJTC_announcements = $this->getLatestAnnouncementsForCp();
            majesticsupport::$_data[0]['latest-announcements'] = $MJTC_announcements;

            $MJTC_articles = $this->getLatestArticlesForCp();
            majesticsupport::$_data[0]['latest-articles'] = $MJTC_articles;

            $MJTC_faqs = $this->getLatestFaqsForCp();
            majesticsupport::$_data[0]['latest-faqs'] = $MJTC_faqs;
        }
    }

    function getUserDeptResults($MJTC_uid) {
        // 1. Get the total count of active tickets for this specific user
        // We need this total to calculate the percentages accurately
        $total_query = "SELECT COUNT(id) 
                        FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` 
                        WHERE uid = " . esc_sql($MJTC_uid) . " 
                        AND (status != 5 AND status != 6)";
        
        $total_tickets = (int) majesticsupport::$_db->get_var($total_query);

        // If the user has no tickets, return an empty array early
        if ($total_tickets === 0) {
            return [];
        }

        // 2. Fetch Top 5 Departments for this specific user
        // We group by department to see where the user's tickets are assigned
        $MJTC_query = "SELECT 
                    dept.id,
                    dept.departmentname, 
                    COUNT(ticket.id) as tkt_count
                  FROM `" . majesticsupport::$_db->prefix . "mjtc_support_departments` AS dept
                  INNER JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket 
                    ON ticket.departmentid = dept.id
                  WHERE ticket.uid = " . esc_sql($MJTC_uid) . " 
                    AND (ticket.status != 5 AND ticket.status != 6)
                  GROUP BY dept.id
                  ORDER BY tkt_count DESC 
                  LIMIT 5";

        $MJTC_dept_results = majesticsupport::$_db->get_results($MJTC_query);

        // 3. Process data for the view
        $MJTC_category_data = [];
        foreach ($MJTC_dept_results as $MJTC_row) {
            $MJTC_category_data[] = [
                'name'  => $MJTC_row->departmentname,
                'perc'  => round(($MJTC_row->tkt_count / $total_tickets) * 100),
                'count' => (int)$MJTC_row->tkt_count
            ];
        }

        return $MJTC_category_data;
    }

    function getUserRecentActivity($MJTC_uid) {
        // 1. Query the activity log for tickets belonging specifically to this user ($MJTC_uid)
        // We join with the tickets table to verify ownership and get the ticket subject
        $MJTC_query = "SELECT 
                    log.message, 
                    log.datetime, 
                    log.referenceid as ticket_id,
                    ticket.subject,
                    priority.prioritycolour
                  FROM `" . majesticsupport::$_db->prefix . "mjtc_support_activity_log` AS log
                  INNER JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket 
                    ON log.referenceid = ticket.id
                  LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` AS priority 
                    ON ticket.priorityid = priority.id
                  WHERE ticket.uid = " . esc_sql($MJTC_uid) . "
                    AND log.eventfor = 1 
                  ORDER BY log.datetime DESC 
                  LIMIT 5";

        return majesticsupport::$_db->get_results($MJTC_query);
    }

    function getUserDailyVelocity($MJTC_uid){
        $today = gmdate('Y-m-d');

        // 1. Get Solved Today (Status 5 and closed today)
        $MJTC_query_solved = "SELECT COUNT(ticket.id)
                        FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket
                        WHERE ticket.uid = ".esc_sql($MJTC_uid)." 
                        AND ticket.status = 5 
                        AND DATE(ticket.closed) = '$today'";
        $MJTC_solved_today = (int) majesticsupport::$_db->get_var($MJTC_query_solved);

        // 2. Get Answered Tickets (Status 4) - As per your reference
        $MJTC_query_answered = "SELECT COUNT(ticket.id)
                        FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket
                        WHERE ticket.uid = ".esc_sql($MJTC_uid)." 
                        AND ticket.status = 4";
        $MJTC_answered_tickets = (int) majesticsupport::$_db->get_var($MJTC_query_answered);

        // 3. Get Pending/Open Tickets (Status not 5 or 6) - As per your reference
        $MJTC_query_pending = "SELECT COUNT(ticket.id)
                        FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket
                        WHERE ticket.uid = ".esc_sql($MJTC_uid)." 
                        AND (ticket.status != 5 AND ticket.status != 6)";
        $MJTC_pending_tickets = (int) majesticsupport::$_db->get_var($MJTC_query_pending);

        // 4. Calculate Velocity % (Answered / Total Active)
        $total_active = $MJTC_pending_tickets; // This includes status 4 (answered) based on your logic
        $MJTC_velocity_perc = ($total_active > 0) ? round(($MJTC_answered_tickets / $total_active) * 100) : 0;
        majesticsupport::$_data['answered_tickets'] = $MJTC_answered_tickets;
        majesticsupport::$_data['pending_tickets'] = $MJTC_pending_tickets;
        majesticsupport::$_data['solved_count'] = $MJTC_solved_today;
        majesticsupport::$_data['velocity_perc'] = $MJTC_velocity_perc;
    }

    function getAgentDeptResults($MJTC_staffid, $total_tickets){
        // 1. Run your permission check logic
        $MJTC_allowed = MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForTask('All Tickets');
        if($MJTC_allowed == true){
            $MJTC_agent_conditions = "1 = 1";
        } else {
            // Note: Ensure $MJTC_staffid is defined in your function scope (usually via MJTC_majesticsupportphplib::MJTC_getStaffId())
            $MJTC_agent_conditions = "(ticket.staffid = ".esc_sql($MJTC_staffid)." OR ticket.departmentid IN (SELECT dept.departmentid FROM `" . majesticsupport::$_db->prefix . "mjtc_support_acl_user_access_departments` AS dept WHERE dept.staffid = ".esc_sql($MJTC_staffid)."))";
        }

        // 2. Fetch the Total Tickets allowed for this specific user (to calculate accurate percentages)
        $total_allowed_query = "SELECT COUNT(ticket.id) FROM `".majesticsupport::$_db->prefix."mjtc_support_tickets` AS ticket WHERE $MJTC_agent_conditions";
        $total_tickets = (int) majesticsupport::$_db->get_var($total_allowed_query);

        // 3. Fetch Top 5 Departments with the permission filter applied
        $MJTC_query = "SELECT 
                    dept.id,
                    dept.departmentname, 
                    (SELECT COUNT(ticket.id) 
                     FROM `".majesticsupport::$_db->prefix."mjtc_support_tickets` AS ticket 
                     WHERE ticket.departmentid = dept.id
                     AND ($MJTC_agent_conditions)
                    ) as tkt_count
                  FROM `".majesticsupport::$_db->prefix."mjtc_support_departments` AS dept
                  ORDER BY tkt_count DESC 
                  LIMIT 5";

        $MJTC_dept_results = majesticsupport::$_db->get_results($MJTC_query);

        // 4. Process data for the view
        $MJTC_category_data = [];
        if ($total_tickets > 0) {
            foreach ($MJTC_dept_results as $MJTC_row) {
                // Skip departments that have 0 allowed tickets for this agent
                if($MJTC_row->tkt_count < 1) continue; 

                $MJTC_category_data[] = [
                    'name' => $MJTC_row->departmentname,
                    'perc' => round(($MJTC_row->tkt_count / $total_tickets) * 100),
                    'count' => (int)$MJTC_row->tkt_count
                ];
            }
        }

        return $MJTC_category_data;
    }

    function getAgentRecentActivity($MJTC_staffid){
        // 1. Permission Logic
        $MJTC_allowed = MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForTask('All Tickets');

        if($MJTC_allowed == true){
            $MJTC_agent_conditions = "1 = 1";
        } else {
            $MJTC_agent_conditions = "(ticket.staffid = ".esc_sql($MJTC_staffid)." OR ticket.departmentid IN (SELECT dept.departmentid FROM `" . majesticsupport::$_db->prefix . "mjtc_support_acl_user_access_departments` AS dept WHERE dept.staffid = ".esc_sql($MJTC_staffid)."))";
        }

        // 2. Query Action History with Permission Filter
        $MJTC_query = "SELECT 
                    log.message, 
                    log.datetime, 
                    log.referenceid as ticket_id,
                    ticket.subject,
                    priority.prioritycolour
                  FROM `" . majesticsupport::$_db->prefix . "mjtc_support_activity_log` AS log
                  JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket ON log.referenceid = ticket.id
                  LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` AS priority ON ticket.priorityid = priority.id
                  WHERE ($MJTC_agent_conditions)
                    AND log.eventfor = 1 
                  ORDER BY log.datetime DESC 
                  LIMIT 5";

        return majesticsupport::$_db->get_results($MJTC_query);
    }

    function getAgentRecentFeedback($MJTC_staffid){
        // --- SECTION: RECENT FEEDBACK (ASSIGNED TICKETS ONLY) ---
        $MJTC_allowed = MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForTask('All Tickets');

        if($MJTC_allowed == true){
            $MJTC_agent_conditions = "1 = 1";
        } else {
            $MJTC_agent_conditions = "(ticket.staffid = ".esc_sql($MJTC_staffid)." OR ticket.departmentid IN (SELECT dept.departmentid FROM `" . majesticsupport::$_db->prefix . "mjtc_support_acl_user_access_departments` AS dept WHERE dept.staffid = ".esc_sql($MJTC_staffid)."))";
        }

        // 1. Get average rating for allowed tickets
        $MJTC_avg_query = "SELECT AVG(feedback.rating) 
                      FROM `".majesticsupport::$_db->prefix."mjtc_support_feedbacks` AS feedback
                      JOIN `".majesticsupport::$_db->prefix."mjtc_support_tickets` AS ticket ON feedback.ticketid = ticket.id
                      WHERE $MJTC_agent_conditions";
        $MJTC_avg_rating = majesticsupport::$_db->get_var($MJTC_avg_query);

        // 2. Get 5 latest feedback entries for allowed tickets
        $MJTC_feedback_query = "SELECT feedback.*, ticket.name as customer_name
                           FROM `".majesticsupport::$_db->prefix."mjtc_support_feedbacks` AS feedback
                           JOIN `".majesticsupport::$_db->prefix."mjtc_support_tickets` AS ticket ON feedback.ticketid = ticket.id
                           WHERE $MJTC_agent_conditions
                           ORDER BY feedback.id DESC LIMIT 5";

        majesticsupport::$_data['recent_feedback'] = majesticsupport::$_db->get_results($MJTC_feedback_query);
        majesticsupport::$_data['avg_feedback']    = number_format((float)$MJTC_avg_rating, 1);
    }

    function getAgentWeeklyLeaderboard($MJTC_staffid){
        // --- SECTION: PERMISSION-BASED WEEKLY LEADERBOARD ---
        $MJTC_start_of_week = gmdate('Y-m-d H:i:s', strtotime("-7 days"));

        // Check for global permission
        $MJTC_is_admin_allowed = MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForTask('All Tickets');

        $MJTC_query = "SELECT DISTINCT 
                    staff.id,
                    staff.firstname, 
                    staff.lastname, 
                    staff.uid as staffuid,
                    (SELECT COUNT(ticket.id) 
                     FROM `".majesticsupport::$_db->prefix."mjtc_support_tickets` AS ticket 
                     WHERE ticket.staffid = staff.id 
                     AND (ticket.status = 5 OR ticket.status = 6)
                     AND ticket.created >= '$MJTC_start_of_week'
                    ) AS solved_count
                  FROM `".majesticsupport::$_db->prefix."mjtc_support_staff` AS staff
                  LEFT JOIN `".majesticsupport::$_db->prefix . "mjtc_support_acl_user_access_departments` AS dep ON dep.staffid = staff.id
                  WHERE staff.status = 1";

        if (!$MJTC_is_admin_allowed) {
            $MJTC_query .= " AND (staff.id = ".esc_sql($MJTC_staffid)." 
                         OR dep.departmentid IN (
                            SELECT dept.departmentid 
                            FROM `" . majesticsupport::$_db->prefix . "mjtc_support_acl_user_access_departments` AS dept 
                            WHERE dept.staffid = ".esc_sql($MJTC_staffid)."
                         ))";
        }

        $MJTC_query .= " ORDER BY solved_count DESC LIMIT 5";
        return majesticsupport::$_db->get_results($MJTC_query);
    }

    function getAgentVipWatchlist($MJTC_staffid){
        // --- SECTION: VIP CLIENTS WATCHLIST (WooCommerce Orders) ---
        $MJTC_allowed = MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForTask('All Tickets');

        if($MJTC_allowed == true){
            $MJTC_agent_conditions = "1 = 1";
        } else {
            $MJTC_agent_conditions = "(ticket.staffid = ".esc_sql($MJTC_staffid)." OR ticket.departmentid IN (SELECT dept.departmentid FROM `" . majesticsupport::$_db->prefix . "mjtc_support_acl_user_access_departments` AS dept WHERE dept.staffid = ".esc_sql($MJTC_staffid)."))";
        }

        $MJTC_query = "SELECT 
                    ticket.id, 
                    ticket.name, 
                    ticket.ticketid,
                    ticket.wcorderid
                  FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket
                  WHERE ($MJTC_agent_conditions)
                    AND ticket.wcorderid IS NOT NULL 
                    AND ticket.wcorderid != '' 
                    AND ticket.wcorderid != '0'
                    AND ticket.status != 5 
                  ORDER BY ticket.created DESC 
                  LIMIT 3";

        return majesticsupport::$_db->get_results($MJTC_query);
    }

    function getAgentDailyVelocity($MJTC_staffid){
        $today = gmdate('Y-m-d');

        // 1. Permission Logic (Matching your reference style)
        $MJTC_allowed = MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForTask('All Tickets');

        if($MJTC_allowed == true){
            $MJTC_agent_conditions = "1 = 1";
        } else {
            $MJTC_agent_conditions = "(ticket.staffid = ".esc_sql($MJTC_staffid)." OR ticket.departmentid IN (SELECT dept.departmentid FROM `" . majesticsupport::$_db->prefix . "mjtc_support_acl_user_access_departments` AS dept WHERE dept.staffid = ".esc_sql($MJTC_staffid)."))";
        }

        // 2. Get Total Active Tickets (Status not 5 or 6)
        $total_active_query = "SELECT COUNT(ticket.id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket WHERE ($MJTC_agent_conditions) AND (ticket.status != 5 AND ticket.status != 6)";
        $total_active = (int) majesticsupport::$_db->get_var($total_active_query);

        // 3. Get Solved Today (Status 5 and closed date is today)
        $MJTC_solved_query = "SELECT COUNT(ticket.id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket WHERE ($MJTC_agent_conditions) 
                         AND ticket.status = 5 
                         AND DATE(ticket.closed) = '$today'";
        $MJTC_solved_count = (int) majesticsupport::$_db->get_var($MJTC_solved_query);

        // Calculate Velocity Percentage: (Solved Today / (Active + Solved Today)) * 100
        $total_context = $total_active + $MJTC_solved_count;
        $MJTC_velocity_perc = ($total_context > 0) ? round(($MJTC_solved_count / $total_context) * 100) : 0;

        // 4. Calculate Total Time Spent Today from 'staff_time' table
        // Note: usertime is usually stored in seconds or minutes; adjust /60 accordingly
        $MJTC_time_query = "SELECT SUM(time.usertime) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_staff_time` AS time
                       JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket ON time.ticketid = ticket.id
                       WHERE ($MJTC_agent_conditions) 
                       AND DATE(time.created) = '$today'";
        $total_seconds = (int) majesticsupport::$_db->get_var($MJTC_time_query);

        if ($total_seconds >= 3600) {
            $MJTC_avg_time_display = round($total_seconds / 3600, 1) . "h";
        } else {
            $MJTC_avg_time_display = round($total_seconds / 60) . "m";
        }

        // 5. Get Average Rating (From your feedback reference)
        $MJTC_avg_rating_query = "SELECT AVG(feedback.rating) FROM `".majesticsupport::$_db->prefix."mjtc_support_feedbacks` AS feedback
            JOIN `".majesticsupport::$_db->prefix."mjtc_support_tickets` AS ticket ON feedback.ticketid = ticket.id
            WHERE $MJTC_agent_conditions";
        $MJTC_avg_rating = majesticsupport::$_db->get_var($MJTC_avg_rating_query);
        $MJTC_display_rating = number_format((float)$MJTC_avg_rating, 1);
        majesticsupport::$_data['display_rating'] = $MJTC_display_rating;
        majesticsupport::$_data['avg_time_display'] = $MJTC_avg_time_display;
        majesticsupport::$_data['solved_count'] = $MJTC_solved_count;
        majesticsupport::$_data['velocity_perc'] = $MJTC_velocity_perc;
    }

    function getControlPanelDataAdmin(){
        $MJTC_curdate = date_i18n('Y-m-d');
        $MJTC_fromdate = date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime("now -1 month"));

        $MJTC_query = "SELECT priority.priority,(SELECT COUNT(id) FROM `".majesticsupport::$_db->prefix."mjtc_support_tickets` WHERE priorityid = priority.id AND status = 1 AND (lastreply = '0000-00-00 00:00:00') AND date(created) >= '".esc_sql($MJTC_fromdate)."' AND date(created) <= '".esc_sql($MJTC_curdate)."' ) AS totalticket
                    FROM `".majesticsupport::$_db->prefix."mjtc_support_priorities` AS priority ORDER BY priority.priority";
        $MJTC_openticket_pr = majesticsupport::$_db->get_results($MJTC_query);

        $MJTC_query = "SELECT COUNT(id) FROM `".majesticsupport::$_db->prefix."mjtc_support_tickets`";
        $MJTC_allticket_pr = majesticsupport::$_db->get_var($MJTC_query);

        $MJTC_query = "SELECT priority.priority,(SELECT COUNT(id) FROM `".majesticsupport::$_db->prefix."mjtc_support_tickets` WHERE priorityid = priority.id AND isanswered = 1 AND status != 5 AND status != 1 AND date(created) >= '".esc_sql($MJTC_fromdate)."' AND date(created) <= '".esc_sql($MJTC_curdate)."') AS totalticket
                    FROM `".majesticsupport::$_db->prefix."mjtc_support_priorities` AS priority ORDER BY priority.priority";
        $MJTC_answeredticket_pr = majesticsupport::$_db->get_results($MJTC_query);
        $MJTC_query = "SELECT priority.priority,(SELECT COUNT(id) FROM `".majesticsupport::$_db->prefix."mjtc_support_tickets` WHERE priorityid = priority.id AND isoverdue = 1 AND status != 5 AND date(created) >= '".esc_sql($MJTC_fromdate)."' AND date(created) <= '".esc_sql($MJTC_curdate)."') AS totalticket
                    FROM `".majesticsupport::$_db->prefix."mjtc_support_priorities` AS priority ORDER BY priority.priority";
        $MJTC_overdueticket_pr = majesticsupport::$_db->get_results($MJTC_query);
        $MJTC_query = "SELECT priority.priority,(SELECT COUNT(id) FROM `".majesticsupport::$_db->prefix."mjtc_support_tickets` WHERE priorityid = priority.id  AND isanswered != 1 AND status != 5 AND (lastreply != '0000-00-00 00:00:00') AND date(created) >= '".esc_sql($MJTC_fromdate)."' AND date(created) <= '".esc_sql($MJTC_curdate)."') AS totalticket
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

        //To show priority colors on chart
        $MJTC_query = "SELECT prioritycolour FROM `".majesticsupport::$_db->prefix."mjtc_support_priorities` ORDER BY priority ";
        $MJTC_jsonColorList = "[";
        foreach(majesticsupport::$_db->get_results($MJTC_query) as $MJTC_priority){
            $MJTC_jsonColorList.= "'".$MJTC_priority->prioritycolour."',";
        }
        $MJTC_jsonColorList .= "]";
        majesticsupport::$_data['stack_chart_horizontal']['colors'] = $MJTC_jsonColorList;
        //end priority colors

        majesticsupport::$_data['ticket_total']['allticket'] = $MJTC_allticket_pr;
        majesticsupport::$_data['ticket_total']['openticket'] = 0;
        majesticsupport::$_data['ticket_total']['overdueticket'] = 0;
        majesticsupport::$_data['ticket_total']['pendingticket'] = 0;
        majesticsupport::$_data['ticket_total']['answeredticket'] = 0;

        $MJTC_count = count($MJTC_openticket_pr);
        for($MJTC_i = 0;$MJTC_i < $MJTC_count; $MJTC_i++){
            majesticsupport::$_data['ticket_total']['openticket'] += $MJTC_openticket_pr[$MJTC_i]->totalticket;
            majesticsupport::$_data['ticket_total']['overdueticket'] += $MJTC_overdueticket_pr[$MJTC_i]->totalticket;
            majesticsupport::$_data['ticket_total']['pendingticket'] += $MJTC_pendingticket_pr[$MJTC_i]->totalticket;
            majesticsupport::$_data['ticket_total']['answeredticket'] += $MJTC_answeredticket_pr[$MJTC_i]->totalticket;
        }

        do_action('MJTC_staff_admin_cp_query');

        // --- TICKET VOLUME CHART ---
        $MJTC_volume_data = array();
        $MJTC_max_count = 1; // Start at 1 to avoid division by zero

        for ($MJTC_i = 6; $MJTC_i >= 0; $MJTC_i--) {
            $MJTC_date = gmdate('Y-m-d', strtotime("-$MJTC_i days"));
            $MJTC_day_name = strtoupper(date_i18n('D', strtotime($MJTC_date)));
            
            $MJTC_count = (int) majesticsupport::$_db->get_var(
                majesticsupport::$_db->prepare(
                    "SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` 
                     WHERE DATE(created) = %s",
                    $MJTC_date
                )
            );

            if ($MJTC_count > $MJTC_max_count) { $MJTC_max_count = $MJTC_count; }
            
            $MJTC_volume_data[] = array(
                'day'   => $MJTC_day_name,
                'count' => $MJTC_count
            );
        }

        // --- SECTION: TICKET STATUS DONUT ---
        $total_tickets = (int) majesticsupport::$_data['ticket_total']['allticket'];

        // Status 1 = Open, Status 4 = Solved, Status 5 = Closed (based on activation.php)
        $MJTC_open_count   = (int) majesticsupport::$_db->get_var("SELECT COUNT(id) FROM `".majesticsupport::$_db->prefix."mjtc_support_tickets` WHERE status = 1");
        $MJTC_solved_count = (int) majesticsupport::$_db->get_var("SELECT COUNT(id) FROM `".majesticsupport::$_db->prefix."mjtc_support_tickets` WHERE isanswered = 1 AND status != 5  AND status != 6 AND status != 1");
        $MJTC_closed_count = (int) majesticsupport::$_db->get_var("SELECT COUNT(id) FROM `".majesticsupport::$_db->prefix."mjtc_support_tickets` WHERE status = 5 OR status = 6");

        // Calculate total "Active" (Open + Solved) for the center number
        $MJTC_active_total = $MJTC_open_count + $MJTC_solved_count;

        // Calculate percentages for the CSS Conic Gradient
        // Open (Primary Color), Solved (Mint Color), Closed (Gray)
        $MJTC_p_open   = ($total_tickets > 0) ? ($MJTC_open_count / $total_tickets) * 100 : 0;
        $MJTC_p_solved = ($total_tickets > 0) ? ($MJTC_solved_count / $total_tickets) * 100 : 0;

        majesticsupport::$_data['donut'] = [
            'open'    => $MJTC_open_count,
            'solved'  => $MJTC_solved_count,
            'closed'  => $MJTC_closed_count,
            'active'  => $MJTC_active_total,
            'p_open'  => $MJTC_p_open,
            'p_solved' => $MJTC_p_open + $MJTC_p_solved // Offset for the second segment
        ];

        // --- SECTION: PRIORITY DONUT CHART ---
        $total_tkt = (int) majesticsupport::$_data['ticket_total']['allticket'];

        $MJTC_query = "SELECT 
                    priority.priority, 
                    priority.prioritycolour, 
                    (SELECT COUNT(id) FROM `".majesticsupport::$_db->prefix."mjtc_support_tickets` WHERE priorityid = priority.id) as tkt_count
                  FROM `".majesticsupport::$_db->prefix."mjtc_support_priorities` AS priority
                  ORDER BY tkt_count DESC LIMIT 3";

        $MJTC_priority_results = majesticsupport::$_db->get_results($MJTC_query);

        $MJTC_priority_data = [];
        $MJTC_current_offset = 0;

        if ($total_tkt > 0) {
            foreach ($MJTC_priority_results as $MJTC_row) {
                $MJTC_percentage = ($MJTC_row->tkt_count / $total_tkt) * 100;
                $MJTC_priority_data[] = [
                    'label'  => $MJTC_row->priority,
                    'color'  => $MJTC_row->prioritycolour,
                    'perc'   => round($MJTC_percentage),
                    'start'  => $MJTC_current_offset,
                    'end'    => $MJTC_current_offset + $MJTC_percentage
                ];
                $MJTC_current_offset += $MJTC_percentage;
            }
        }

        majesticsupport::$_data['priority_donut'] = $MJTC_priority_data;

        // Store in data object
        majesticsupport::$_data['volume_stats'] = $MJTC_volume_data;
        majesticsupport::$_data['volume_max']   = $MJTC_max_count;

        // --- SECTION: TOP 3 DEPARTMENTS CHART ---
        $total_tkt = (int) majesticsupport::$_data['ticket_total']['allticket'];

        $MJTC_query = "SELECT 
                    dept.departmentname, 
                    (SELECT COUNT(id) FROM `".majesticsupport::$_db->prefix."mjtc_support_tickets` WHERE departmentid = dept.id) as tkt_count
                  FROM `".majesticsupport::$_db->prefix."mjtc_support_departments` AS dept
                  ORDER BY tkt_count DESC LIMIT 3";

        $MJTC_dept_results = majesticsupport::$_db->get_results($MJTC_query);

        $MJTC_dept_data = [];
        // We use 100 as the base for the highest bar, or scale relative to total tickets
        if ($total_tkt > 0) {
            foreach ($MJTC_dept_results as $MJTC_row) {
                $MJTC_perc = ($MJTC_row->tkt_count / $total_tkt) * 100;
                $MJTC_dept_data[] = [
                    'name' => $MJTC_row->departmentname,
                    'perc' => round($MJTC_perc),
                    'count' => $MJTC_row->tkt_count
                ];
            }
        }

        majesticsupport::$_data['dept_stats'] = $MJTC_dept_data;

        // --- SECTION: TOP 3 PRODUCTS HORIZONTAL BAR ---
        $total_tkt = (int) majesticsupport::$_data['ticket_total']['allticket'];

        $MJTC_query = "SELECT 
                    prod.product, 
                    (SELECT COUNT(id) FROM `".majesticsupport::$_db->prefix."mjtc_support_tickets` WHERE productid = prod.id) as tkt_count
                  FROM `".majesticsupport::$_db->prefix."mjtc_support_products` AS prod
                  ORDER BY tkt_count DESC LIMIT 3";

        $MJTC_product_results = majesticsupport::$_db->get_results($MJTC_query);

        $MJTC_product_data = [];
        if ($total_tkt > 0) {
            foreach ($MJTC_product_results as $MJTC_row) {
                $MJTC_perc = ($MJTC_row->tkt_count / $total_tkt) * 100;
                $MJTC_product_data[] = [
                    'name' => $MJTC_row->product,
                    'perc' => round($MJTC_perc)
                ];
            }
        }

        majesticsupport::$_data['product_stats'] = $MJTC_product_data;

        // feedback
        if (in_array('feedback', majesticsupport::$_active_addons)) {
            $MJTC_query = "SELECT feedback.rating,feedback.remarks,ticket.ticketid AS trackingid
                        FROM `" . majesticsupport::$_db->prefix . "mjtc_support_feedbacks` AS feedback
                        JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket ON ticket.id = feedback.ticketid
                        ORDER BY feedback.created DESC LIMIT 0, 5";
            majesticsupport::$_data['feedback'] = majesticsupport::$_db->get_results($MJTC_query);
        }

        // --- SECTION: ACTIVE TIMERS (GROUPED BY TICKET) ---
        $MJTC_timers = [];
        if(in_array('timetracking', majesticsupport::$_active_addons)){
            $MJTC_query = "SELECT 
                    SUM(time.usertime) AS total_usertime, 
                    ticket.ticketid, 
                    MAX(time.created) AS last_logged,
                    ticket.subject 
                  FROM `".majesticsupport::$_db->prefix."mjtc_support_staff_time` AS time
                  LEFT JOIN `".majesticsupport::$_db->prefix."mjtc_support_tickets` AS ticket ON ticket.id = time.ticketid
                  GROUP BY time.ticketid
                  ORDER BY last_logged DESC
                  LIMIT 2";

            $MJTC_timer_results = majesticsupport::$_db->get_results($MJTC_query);

            if (!empty($MJTC_timer_results)) {
                foreach ($MJTC_timer_results as $MJTC_row) {
                    // Convert the SUM of usertime (seconds) to HH:MM:SS
                    $MJTC_seconds = (int)$MJTC_row->total_usertime;
                    $MJTC_h = floor($MJTC_seconds / 3600);
                    $m = floor(($MJTC_seconds % 3600) / 60);
                    $MJTC_s = $MJTC_seconds % 60;
                    
                    $MJTC_timers[] = [
                        'subject'      => !empty($MJTC_row->subject) ? $MJTC_row->subject : __('Manual Entry', 'majestic-support'),
                        'ticket_id'    => $MJTC_row->ticketid,
                        'display_time' => esc_html(sprintf('%02d:%02d:%02d', $MJTC_h, $m, $MJTC_s)),
                        'relative'     => human_time_diff(strtotime($MJTC_row->last_logged), current_time('timestamp'))
                    ];
                }
            }
        }

        majesticsupport::$_data['active_timers'] = $MJTC_timers;

        // overdue_tickets
        $MJTC_query = "SELECT ticket.id,ticket.ticketid,ticket.subject,ticket.name,ticket.created,priority.priority,priority.prioritycolour,ticket.status,department.departmentname,ticket.uid,status.status AS statustitle, status.statuscolour, status.statusbgcolour ".majesticsupport::$_addon_query['select']."
                    FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket
                    LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` AS priority ON priority.id = ticket.priorityid
                    LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_departments` AS department ON ticket.departmentid = department.id
                    JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_statuses` AS status ON status.id = ticket.status
                    ".majesticsupport::$_addon_query['join']."
                    WHERE ticket.isoverdue = 1 AND ticket.status != 5  AND ticket.status != 6 
                    ORDER BY ticket.status ASC, ticket.created DESC LIMIT 0, 5";
        majesticsupport::$_data['overdue_tickets'] = majesticsupport::$_db->get_results($MJTC_query);

        // unassigned_tickets
        $MJTC_query = "SELECT ticket.id,ticket.subject,priority.priority,priority.prioritycolour
                    FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket
                    LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` AS priority ON priority.id = ticket.priorityid
                    WHERE ticket.staffid < 1 AND ticket.status != 5  AND ticket.status != 6 
                    ORDER BY ticket.status ASC, ticket.created DESC LIMIT 0, 3";
        majesticsupport::$_data['unassigned_tickets'] = majesticsupport::$_db->get_results($MJTC_query);

        // Define the specific priority ID you are looking for
        $target_priority_id = 4;

        $MJTC_query = "SELECT 
                    priority.priority,priority.prioritycolour,
                    COUNT(ticket.id) AS total_count
                  FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket
                  LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` AS priority ON priority.id = ticket.priorityid
                  WHERE ticket.staffid < 1 
                    AND ticket.status != 5 
                    AND ticket.status != 6 
                    AND priority.id = " . esc_sql($target_priority_id) . "
                  GROUP BY priority.id";

        $MJTC_result = majesticsupport::$_db->get_row($MJTC_query);

        // Handle the display logic
        if ($MJTC_result) {
            // Result format: "4 Urgent"
            majesticsupport::$_data['unassigned_urgent'] = $MJTC_result;
        }

        // --- SECTION: VIP TICKETS (WooCommerce Linked) ---
        $MJTC_query = "SELECT 
                    ticket.id, 
                    ticket.name, 
                    ticket.uid, 
                    ticket.subject, 
                    ticket.wcorderid
                  FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket
                  WHERE ticket.wcorderid IS NOT NULL 
                    AND ticket.wcorderid != '' 
                    AND ticket.wcorderid != '0'
                    AND ticket.status != 5 
                  ORDER BY ticket.created DESC 
                  LIMIT 3";

        majesticsupport::$_data['vip_tickets'] = majesticsupport::$_db->get_results($MJTC_query);

        // all tickets
        $MJTC_q = "SELECT ticket.id,ticket.ticketid,ticket.subject,ticket.name,ticket.created,priority.priority,priority.prioritycolour,ticket.status,department.departmentname,ticket.uid,status.status AS statustitle, status.statuscolour, status.statusbgcolour ".majesticsupport::$_addon_query['select']."
                    FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket
                    LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` AS priority ON priority.id = ticket.priorityid
                    LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_departments` AS department ON ticket.departmentid = department.id
                    JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_statuses` AS status ON status.id = ticket.status
                    ".majesticsupport::$_addon_query['join'];
        $MJTC_query = $MJTC_q ." ORDER BY ticket.status ASC, ticket.created DESC LIMIT 0, 10";
        majesticsupport::$_data['recent_all_tickets'] = majesticsupport::$_db->get_results($MJTC_query);
        // recent_open_tickets
        $MJTC_query = $MJTC_q ." WHERE (ticket.status != 5 AND ticket.status != 6) ORDER BY ticket.status ASC, ticket.created DESC LIMIT 0, 10";
        majesticsupport::$_data['recent_open_tickets'] = majesticsupport::$_db->get_results($MJTC_query);
        // recent_pending_tickets
        $MJTC_query = $MJTC_q ." WHERE ticket.isanswered != 1 AND ticket.status != 5 AND ticket.status != 6 AND ticket.status != 1  ORDER BY ticket.status ASC, ticket.created DESC LIMIT 0, 10";
        majesticsupport::$_data['recent_pending_tickets'] = majesticsupport::$_db->get_results($MJTC_query);
        // recent_answered_tickets
        $MJTC_query = $MJTC_q ." WHERE ticket.isanswered = 1 AND ticket.status != 5 AND ticket.status != 6 AND ticket.status != 1  ORDER BY ticket.status ASC, ticket.created DESC LIMIT 0, 10";
        majesticsupport::$_data['recent_answered_tickets'] = majesticsupport::$_db->get_results($MJTC_query);
        // recent_closed_tickets
        $MJTC_query = $MJTC_q ." WHERE (ticket.status = 5 OR ticket.status = 6) ORDER BY ticket.status ASC, ticket.created DESC LIMIT 0, 10";
        majesticsupport::$_data['recent_closed_tickets'] = majesticsupport::$_db->get_results($MJTC_query);

        // smart reply
        $MJTC_query = "SELECT smartreply.title,smartreply.usedby
                    FROM `" . majesticsupport::$_db->prefix . "mjtc_support_smartreplies` AS smartreply
                    ORDER BY smartreply.usedby DESC LIMIT 0, 5";
        majesticsupport::$_data['smartreply'] = majesticsupport::$_db->get_results($MJTC_query);
        // agents
        if(in_array('agent', majesticsupport::$_active_addons)){
            $MJTC_query = "SELECT 
                        CONCAT(staff.firstname ,'  ' ,staff.lastname) AS staffname, 
                        staff.id AS staffid, 
                        staff.uid AS staffuid, 
                        staff.photo AS staffphoto,
                        (SELECT COUNT(id) FROM `".majesticsupport::$_db->prefix."mjtc_support_tickets` AS ticket 
                         WHERE ticket.staffid = staff.id AND ticket.status != 5 AND ticket.status != 6) AS total_assigned,
                        (SELECT COUNT(id) FROM `".majesticsupport::$_db->prefix."mjtc_support_tickets` AS ticket 
                         WHERE ticket.staffid = staff.id AND (ticket.status = 5 OR ticket.status = 6)) AS total_resolved
                    FROM `" . majesticsupport::$_db->prefix . "mjtc_support_staff` AS staff 
                    WHERE staff.status = 1 
                    ORDER BY total_assigned DESC 
                    LIMIT 0, 5";
            majesticsupport::$_data['agents'] = majesticsupport::$_db->get_results($MJTC_query);
        }
        // Tickets By Priorities
        $MJTC_query = "SELECT priority.priority, priority.prioritycolour, (SELECT count(ticket.id) AS usedby
                    FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket
                    WHERE ticket.priorityid = priority.id GROUP BY priorityid) AS usedby
                    FROM `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` AS priority
                    ORDER BY usedby DESC LIMIT 0, 5";
        majesticsupport::$_data['tickets_by_priority'] = majesticsupport::$_db->get_results($MJTC_query);
        // Tickets By Departments
        $MJTC_query = "SELECT dept.departmentname, (SELECT count(ticket.id) AS usedby
                    FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket
                    WHERE ticket.departmentid = dept.id GROUP BY departmentid) AS usedby
                    FROM `" . majesticsupport::$_db->prefix . "mjtc_support_departments` AS dept
                    ORDER BY usedby DESC LIMIT 0, 5";
        majesticsupport::$_data['tickets_by_department'] = majesticsupport::$_db->get_results($MJTC_query);

        majesticsupport::$_data['version'] = majesticsupport::$_config['versioncode'];

        //today tickets for chart
        $MJTC_query = "SELECT priority.priority,(SELECT COUNT(id) FROM `".majesticsupport::$_db->prefix."mjtc_support_tickets` WHERE priorityid = priority.id AND date(created) = '".esc_sql($MJTC_curdate)."')  AS totalticket
                    FROM `".majesticsupport::$_db->prefix."mjtc_support_priorities` AS priority ORDER BY priority.priority";
        $MJTC_priorities = majesticsupport::$_db->get_results($MJTC_query);
        majesticsupport::$_data['today_ticket_chart']['title'] = "['". esc_html(__('Priority','majestic-support'))."',";
        majesticsupport::$_data['today_ticket_chart']['data'] = "['',";
        foreach($MJTC_priorities AS $MJTC_pr){
            majesticsupport::$_data['today_ticket_chart']['title'] .= "'".majesticsupport::MJTC_getVarValue($MJTC_pr->priority)."',";
            majesticsupport::$_data['today_ticket_chart']['data'] .= $MJTC_pr->totalticket.",";
        }
        majesticsupport::$_data['today_ticket_chart']['title'] .= "]";
        majesticsupport::$_data['today_ticket_chart']['data'] .= "]";

        //Ticket Hisotry
        if(in_array('tickethistory', majesticsupport::$_active_addons)){
            $MJTC_query = "SELECT al.id,al.message,al.datetime,al.uid,al.eventtype,pr.priority,pr.prioritycolour,dp.departmentname,status.status AS statustitle, status.statuscolour, status.statuscolour,tic.ticketid
            FROM `" . majesticsupport::$_db->prefix . "mjtc_support_activity_log`  AS al
            JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS tic ON al.referenceid=tic.id
            LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` AS pr ON pr.id = tic.priorityid
            LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_departments` AS dp ON dp.id = tic.departmentid
            JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_statuses` AS status ON status.id = tic.status
            WHERE al.eventfor=1 ORDER BY al.datetime DESC LIMIT 8 ";
            majesticsupport::$_data['tickethistory'] = majesticsupport::$_db->get_results($MJTC_query);
        }

        // Canned Responses
        if(in_array('cannedresponses', majesticsupport::$_active_addons)){
            $MJTC_query = "
                SELECT premade.*
                FROM `" . majesticsupport::$_db->prefix . "mjtc_support_department_message_premade` AS premade
                WHERE status = 1
                ORDER BY premade.updated DESC LIMIT 5
            ";
            majesticsupport::$_data['cannedresponses'] = majesticsupport::$_db->get_results($MJTC_query);
        }
        // Knowledge Base
        if( in_array('knowledgebase', majesticsupport::$_active_addons) ){
            $MJTC_query = "SELECT article.id, article.subject, article.content, category.name AS categoryname, article.views, article.created
                FROM `" . majesticsupport::$_db->prefix . "mjtc_support_articles` AS article
                LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_categories` AS category
                ON article.categoryid = category.id
                WHERE article.status = 1 ORDER BY article.created DESC LIMIT 5";
            majesticsupport::$_data['knowledgebase'] = majesticsupport::$_db->get_results($MJTC_query);
        }

        // update available alert
        majesticsupport::$_data['update_avaliable_for_addons'] = $this->showUpdateAvaliableAlert();
    }

    function getAgentLatestTicketsForCp($MJTC_staffid){
        if(!is_numeric($MJTC_staffid)){
            return false;
        }

        $MJTC_allowed = MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForTask('All Tickets');
        if($MJTC_allowed == true){
            $MJTC_agent_conditions = "1 = 1";
        }else{
            $MJTC_agent_conditions = "ticket.staffid = ".esc_sql($MJTC_staffid)." OR ticket.departmentid IN (SELECT dept.departmentid FROM `" . majesticsupport::$_db->prefix . "mjtc_support_acl_user_access_departments` AS dept WHERE dept.staffid = ".esc_sql($MJTC_staffid).")";
        }

        //latest tickets
        $MJTC_query = "SELECT DISTINCT ticket.*,department.departmentname AS departmentname ,priority.priority AS priority,
        priority.prioritycolour AS prioritycolour,staff.photo AS staffphoto,staff.id AS staffid,
        assignstaff.firstname AS staffname,status.status AS statustitle, status.statuscolour, status.statusbgcolour
        FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket
        LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` AS priority ON ticket.priorityid = priority.id
        LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_departments` AS department ON ticket.departmentid = department.id
        LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_staff` AS staff ON staff.uid = ticket.uid
        LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_staff` AS assignstaff ON ticket.staffid = assignstaff.id
        JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_statuses` AS status ON status.id = ticket.status
        WHERE (".esc_sql($MJTC_agent_conditions).") ORDER BY ticket.created DESC LIMIT 3 ";
        $MJTC_tickets = majesticsupport::$_db->get_results($MJTC_query);
        return $MJTC_tickets;
    }

    function getAgentAssignedTicketsForCp($MJTC_staffid){
        if(!is_numeric($MJTC_staffid)){
            return false;
        }

        
        $MJTC_agent_conditions = " ticket.staffid = ".esc_sql($MJTC_staffid);

        //latest tickets
        $MJTC_query = "SELECT DISTINCT ticket.*,department.departmentname AS departmentname ,priority.priority AS priority,
        priority.prioritycolour AS prioritycolour,staff.photo AS staffphoto,staff.id AS staffid,
        assignstaff.firstname AS staffname,status.status AS statustitle, status.statuscolour, status.statusbgcolour
        FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket
        LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` AS priority ON ticket.priorityid = priority.id
        LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_departments` AS department ON ticket.departmentid = department.id
        LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_staff` AS staff ON staff.uid = ticket.uid
        LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_staff` AS assignstaff ON ticket.staffid = assignstaff.id
        JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_statuses` AS status ON status.id = ticket.status
        WHERE (".esc_sql($MJTC_agent_conditions).") ORDER BY ticket.created DESC LIMIT 3 ";
        $MJTC_tickets = majesticsupport::$_db->get_results($MJTC_query);

        //Assigned pending tickets
        $MJTC_query = "SELECT COUNT(DISTINCT ticket.id)
        FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket
        WHERE (".esc_sql($MJTC_agent_conditions).") AND ticket.status = 4";
        majesticsupport::$_data['assigned_pending_tickets'] = majesticsupport::$_db->get_var($MJTC_query);
        return $MJTC_tickets;
    }

    function getAgentTicketStats($MJTC_staffid){
        if(!is_numeric($MJTC_staffid)){
            return false;
        }

        $MJTC_result = array();

        $MJTC_allowed = MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForTask('All Tickets');
        if($MJTC_allowed == true){
            $MJTC_agent_conditions = "1 = 1";
        }else{
            $MJTC_agent_conditions = "ticket.staffid = " . esc_sql($MJTC_staffid) . " OR ticket.departmentid IN (SELECT dept.departmentid FROM `" . majesticsupport::$_db->prefix . "mjtc_support_acl_user_access_departments` AS dept WHERE dept.staffid = " . esc_sql($MJTC_staffid).")";
        }

        $MJTC_query = "SELECT COUNT(ticket.id)
        FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket
        LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` AS priority ON ticket.priorityid = priority.id
        LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_departments` AS department ON ticket.departmentid = department.id
        WHERE (".esc_sql($MJTC_agent_conditions).") AND (ticket.status != 5 AND ticket.status !=6) ";
        $MJTC_result['openticket'] = majesticsupport::$_db->get_var($MJTC_query);

        $MJTC_query = "SELECT COUNT(ticket.id)
        FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket
        LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` AS priority ON ticket.priorityid = priority.id
        LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_departments` AS department ON ticket.departmentid = department.id
        WHERE (".esc_sql($MJTC_agent_conditions).") AND ticket.isanswered = 1 AND ticket.status != 5 AND ticket.status != 1 ";
        $MJTC_result['answeredticket'] = majesticsupport::$_db->get_var($MJTC_query);

        $MJTC_query = "SELECT COUNT(ticket.id)
        FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket
        LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` AS priority ON ticket.priorityid = priority.id
        LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_departments` AS department ON ticket.departmentid = department.id
        WHERE (".esc_sql($MJTC_agent_conditions).") AND (ticket.status = 5 OR ticket.status = 6) ";
        $MJTC_result['closedticket'] = majesticsupport::$_db->get_var($MJTC_query);


        $MJTC_query = "SELECT COUNT(ticket.id)
        FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket
        LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` AS priority ON ticket.priorityid = priority.id
        LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_departments` AS department ON ticket.departmentid = department.id
        WHERE (".esc_sql($MJTC_agent_conditions).") AND ticket.isoverdue = 1 ";
        $MJTC_result['overdue'] = majesticsupport::$_db->get_var($MJTC_query);

        $MJTC_query = "SELECT COUNT(ticket.id)
        FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket
        LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` AS priority ON ticket.priorityid = priority.id
        LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_departments` AS department ON ticket.departmentid = department.id
        WHERE (".esc_sql($MJTC_agent_conditions).")  ";
        $MJTC_result['allticket'] = majesticsupport::$_db->get_var($MJTC_query);

        return $MJTC_result;
    }

    function getAgentCpChartData($MJTC_staffid){
        if(!is_numeric($MJTC_staffid)){
            return false;
        }

        $MJTC_curdate = date_i18n('Y-m-d');
        $MJTC_fromdate = date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime("now -1 month"));

        $MJTC_query = "SELECT priority.priority,(SELECT COUNT(id) FROM `".majesticsupport::$_db->prefix."mjtc_support_tickets` WHERE priorityid = priority.id AND status = 0 AND (lastreply = '0000-00-00 00:00:00') AND date(created) >= '".esc_sql($MJTC_fromdate)."' AND date(created) <= '".esc_sql($MJTC_curdate)."' ) AS totalticket
                    FROM `".majesticsupport::$_db->prefix."mjtc_support_priorities` AS priority ORDER BY priority.priority";
        $MJTC_openticket_pr = majesticsupport::$_db->get_results($MJTC_query);

        $MJTC_query = "SELECT COUNT(id) FROM `".majesticsupport::$_db->prefix."mjtc_support_tickets`";
        $MJTC_allticket_pr = majesticsupport::$_db->get_var($MJTC_query);

        $MJTC_query = "SELECT priority.priority,(SELECT COUNT(id) FROM `".majesticsupport::$_db->prefix."mjtc_support_tickets` WHERE priorityid = priority.id AND isanswered = 1 AND status != 5 AND status != 1 AND date(created) >= '".esc_sql($MJTC_fromdate)."' AND date(created) <= '".esc_sql($MJTC_curdate)."') AS totalticket
                    FROM `".majesticsupport::$_db->prefix."mjtc_support_priorities` AS priority ORDER BY priority.priority";
        $MJTC_answeredticket_pr = majesticsupport::$_db->get_results($MJTC_query);
        $MJTC_query = "SELECT priority.priority,(SELECT COUNT(id) FROM `".majesticsupport::$_db->prefix."mjtc_support_tickets` WHERE priorityid = priority.id AND isoverdue = 1 AND status != 5 AND date(created) >= '".esc_sql($MJTC_fromdate)."' AND date(created) <= '".esc_sql($MJTC_curdate)."') AS totalticket
                    FROM `".majesticsupport::$_db->prefix."mjtc_support_priorities` AS priority ORDER BY priority.priority";
        $MJTC_overdueticket_pr = majesticsupport::$_db->get_results($MJTC_query);
        $MJTC_query = "SELECT priority.priority,(SELECT COUNT(id) FROM `".majesticsupport::$_db->prefix."mjtc_support_tickets` WHERE priorityid = priority.id  AND isanswered != 1 AND status != 5 AND (lastreply != '0000-00-00 00:00:00') AND date(created) >= '".esc_sql($MJTC_fromdate)."' AND date(created) <= '".esc_sql($MJTC_curdate)."') AS totalticket
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
    }

    function getUserLatestTicketsForCp($MJTC_uid){
        if(!is_numeric($MJTC_uid)){
            return false;
        }
        do_action('MJTC_addon_user_cp_tickets');
        $MJTC_query = "SELECT ticket.*,department.departmentname AS departmentname ,priority.priority AS priority,priority.prioritycolour AS prioritycolour,status.status AS statustitle, status.statuscolour, status.statusbgcolour ".majesticsupport::$_addon_query['select']."
        FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket
        LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` AS priority ON ticket.priorityid = priority.id
        LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_departments` AS department ON     ticket.departmentid = department.id
        JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_statuses` AS status ON status.id = ticket.status
        ".majesticsupport::$_addon_query['join'];
        $MJTC_query .= " WHERE ticket.uid = " . esc_sql($MJTC_uid);
        $MJTC_query .= " ORDER BY ticket.created DESC LIMIT 3";
        $MJTC_tickets = majesticsupport::$_db->get_results($MJTC_query);
        do_action('MJTC_reset_addon_query');
        return $MJTC_tickets;
    }

    function getUserTicketStats($MJTC_uid){
        if(!is_numeric($MJTC_uid)){
            return false;
        }

        $MJTC_result = array();

        $MJTC_query = "SELECT COUNT(ticket.id)
        FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket
        LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` AS priority ON ticket.priorityid = priority.id
        LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_departments` AS department ON ticket.departmentid = department.id
        WHERE ticket.uid = ".esc_sql($MJTC_uid)." AND (ticket.status != 5 AND ticket.status != 6)";
        $MJTC_result['openticket'] = majesticsupport::$_db->get_var($MJTC_query);

        $MJTC_query = "SELECT COUNT(ticket.id)
        FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket
        LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_departments` AS department ON ticket.departmentid = department.id
        LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` AS priority ON ticket.priorityid = priority.id
        WHERE ticket.uid = ".esc_sql($MJTC_uid)." AND ticket.status = 4 ";
        $MJTC_result['answeredticket'] = majesticsupport::$_db->get_var($MJTC_query);

        $MJTC_query = "SELECT COUNT(ticket.id)
        FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket
        LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_departments` AS department ON ticket.departmentid = department.id
        LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` AS priority ON ticket.priorityid = priority.id
        WHERE ticket.uid = ".esc_sql($MJTC_uid)." AND (ticket.status = 5 OR ticket.status = 6)";
        $MJTC_result['closedticket'] = majesticsupport::$_db->get_var($MJTC_query);

        $MJTC_query = "SELECT COUNT(ticket.id)
        FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket
        LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_departments` AS department ON ticket.departmentid = department.id
        LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` AS priority ON ticket.priorityid = priority.id
        WHERE ticket.uid = ".esc_sql($MJTC_uid);
        $MJTC_result['allticket'] = majesticsupport::$_db->get_var($MJTC_query);

        return $MJTC_result;
    }

    function getLatestDownloadsForCp(){
        if( in_array('download', majesticsupport::$_active_addons) ){
            $MJTC_query = "SELECT download.title, download.id AS downloadid, (SELECT count(downloadattachment.id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_downloads_attachments` AS downloadattachment WHERE download.id=downloadattachment.downloadid ) AS totalattachment
            FROM `" . majesticsupport::$_db->prefix . "mjtc_support_downloads` AS download
            WHERE download.status = 1 ORDER BY download.created DESC LIMIT 4";
            return majesticsupport::$_db->get_results($MJTC_query);
        }
        return false;
    }

    function getLatestAnnouncementsForCp(){
        if( in_array('announcement', majesticsupport::$_active_addons) ){
            $MJTC_query = "SELECT announcement.id, announcement.title, announcement.description, announcement.created
            FROM `" . majesticsupport::$_db->prefix . "mjtc_support_announcements` AS announcement
            WHERE announcement.status = 1 ORDER BY announcement.created DESC LIMIT 4";
            return majesticsupport::$_db->get_results($MJTC_query);
        }
        return false;
    }


    function getLatestArticlesForCp(){
        if( in_array('knowledgebase', majesticsupport::$_active_addons) ){
            $MJTC_query = "SELECT article.subject,article.content,article.created, article.id AS articleid
            FROM `" . majesticsupport::$_db->prefix . "mjtc_support_articles` AS article
            WHERE article.status = 1 ORDER BY article.created DESC LIMIT 4";
            return majesticsupport::$_db->get_results($MJTC_query);
        }
        return false;
    }

    function getLatestFaqsForCp(){
        if( in_array('faq', majesticsupport::$_active_addons) ){
            $MJTC_query = "SELECT faq.id, faq.subject, faq.content
            FROM `" . majesticsupport::$_db->prefix . "mjtc_support_faqs` AS faq
            WHERE faq.status = 1 ORDER BY faq.created DESC LIMIT 4";
            return majesticsupport::$_db->get_results($MJTC_query);
        }
        return false;
    }


    function getStaffControlPanelData() {

        $MJTC_query = "SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` ";
        $MJTC_allticket = majesticsupport::$_db->get_var($MJTC_query);

        $MJTC_query = "SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE status = 1 AND (lastreply = '0000-00-00 00:00:00')";
        $MJTC_openticket = majesticsupport::$_db->get_var($MJTC_query);

        $MJTC_query = "SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE status = 5";
        $MJTC_closeticket = majesticsupport::$_db->get_var($MJTC_query);

        $MJTC_query = "SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE isanswered = 1 AND status != 5 AND status != 1";
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

        $MJTC_query = "SELECT COUNT(id) FROM `".majesticsupport::$_db->prefix."mjtc_support_tickets`";
        majesticsupport::$_data['total_tickets']['total_ticket'] = majesticsupport::$_db->get_var($MJTC_query);
        $MJTC_query = "SELECT COUNT(id) FROM `".majesticsupport::$_db->prefix."mjtc_support_departments`";
        majesticsupport::$_data['total_tickets']['total_department'] = majesticsupport::$_db->get_var($MJTC_query);

        if(in_array('agent', majesticsupport::$_active_addons)){
            $MJTC_query = "SELECT COUNT(id) FROM `".majesticsupport::$_db->prefix."mjtc_support_staff`";
            majesticsupport::$_data['total_tickets']['total_staff'] = majesticsupport::$_db->get_var($MJTC_query);
        }else{
            majesticsupport::$_data['total_tickets']['total_staff'] = 0;
        }
        if(in_array('feedback', majesticsupport::$_active_addons)){
            $MJTC_query = "SELECT COUNT(id) FROM `".majesticsupport::$_db->prefix."mjtc_support_feedbacks`";
            majesticsupport::$_data['total_tickets']['total_feedback'] = majesticsupport::$_db->get_var($MJTC_query);
        }else{
            majesticsupport::$_data['total_tickets']['total_feedback'] = 0;
        }
    }

    function makeDir($MJTC_path) {
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

        // Replaced file_exists() with $MJTC_wp_filesystem->exists()
        if (!$MJTC_wp_filesystem->exists($MJTC_path)) {
            
            // Replaced mkdir() with $MJTC_wp_filesystem->mkdir()
            $MJTC_wp_filesystem->mkdir($MJTC_path, 0755);
            
            $MJTC_ourFileName = $MJTC_path . '/index.html';
            
            // Replaced fopen/fclose with put_contents to create an empty index.html
            // This is the compliant way to create/write files in WordPress
            $MJTC_wp_filesystem->put_contents($MJTC_ourFileName, '');
        }
    }

    function MJTC_checkExtension($MJTC_filename) {
        $MJTC_i = strrpos($MJTC_filename, ".");
        if (!$MJTC_i)
            return 'N';
        $MJTC_l = MJTC_majesticsupportphplib::MJTC_strlen($MJTC_filename) - $MJTC_i;
        $MJTC_ext = MJTC_majesticsupportphplib::MJTC_substr($MJTC_filename, $MJTC_i + 1, $MJTC_l);
        $MJTC_extensions = MJTC_majesticsupportphplib::MJTC_explode(",", majesticsupport::$_config['file_extension']);
        $match = 'N';
        foreach ($MJTC_extensions as $MJTC_extension) {
            if (MJTC_majesticsupportphplib::MJTC_strtolower($MJTC_extension) == MJTC_majesticsupportphplib::MJTC_strtolower($MJTC_ext)) {
                $match = 'Y';
                break;
            }
        }
        return $match;
    }

    //translation code
    function getListTranslations() {
        if(!current_user_can('manage_options')){
            return false;
        }
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'get-list-translations') ) {
            die( 'Security check Failed' );
        }

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

        $MJTC_result = array();
        $MJTC_result['error'] = false;

        // $MJTC_path = MJTC_PLUGIN_PATH.'languages';

        $MJTC_path = WP_LANG_DIR;
        
        // Replaced is_dir with $MJTC_wp_filesystem->is_dir
        if(!$MJTC_wp_filesystem->is_dir($MJTC_path)){
            $this->makeDir($MJTC_path);
        }else{
            $MJTC_path = WP_LANG_DIR . '/plugins/';
            // Replaced is_dir with $MJTC_wp_filesystem->is_dir
            if(!$MJTC_wp_filesystem->is_dir($MJTC_path)){
                $this->makeDir($MJTC_path);
            }
        }

        // Replaced is_writeable with $MJTC_wp_filesystem->is_writable
        if( ! $MJTC_wp_filesystem->is_writable($MJTC_path)){
            $MJTC_result['error'] = esc_html(__('Dir is not writable','majestic-support')).' '.esc_url($MJTC_path);

        }else{

            if($this->isConnected()){

                $MJTC_url = "https://majesticsupport.com/translations/api/1.0/index.php";
                $MJTC_post_data['product'] ='majestic-support-wp';
                $MJTC_post_data['domain'] = get_site_url();
                $MJTC_post_data['producttype'] = majesticsupport::$_config['producttype'];
                $MJTC_post_data['productcode'] = 'mjsupport';
                $MJTC_post_data['productversion'] = majesticsupport::$_config['productversion'];
                $MJTC_post_data['JVERSION'] = get_bloginfo('version');
                $MJTC_post_data['method'] = 'getTranslations';

                $MJTC_response = wp_remote_post( $MJTC_url, array('body' => $MJTC_post_data,'timeout'=>45,'sslverify'=>false));
                if( !is_wp_error($MJTC_response) && $MJTC_response['response']['code'] == 200 && isset($MJTC_response['body']) ){
                    $MJTC_call_result = $MJTC_response['body'];
                }else{
                    $MJTC_call_result = false;
                    if(!is_wp_error($MJTC_response)){
                       $MJTC_error = $MJTC_response['response']['message'];
                    }else{
                        $MJTC_error = $MJTC_response->get_error_message();
                    }
                }

                $MJTC_result['data'] = MJTC_majesticsupportphplib::MJTC_htmlentities($MJTC_call_result);
                if(!$MJTC_call_result){
                    $MJTC_result['error'] = $MJTC_error;
                }

            }else{
                $MJTC_result['error'] = esc_html(__('Unable to connect to the server','majestic-support'));
            }
        }

        $MJTC_result = wp_json_encode($MJTC_result);

        return $MJTC_result;
    }

    function makeLanguageCode($MJTC_lang_name){
        $MJTC_langarray = wp_get_installed_translations('core');
        $MJTC_langarray = isset($MJTC_langarray['default']) ? $MJTC_langarray['default'] : array();
        $match = false;
        if(array_key_exists($MJTC_lang_name, $MJTC_langarray)){
            $MJTC_lang_name = $MJTC_lang_name;
            $match = true;
        }else{
            $m_lang = '';
            foreach($MJTC_langarray AS $MJTC_k => $MJTC_v){
                if($MJTC_lang_name[0].$MJTC_lang_name[1] == $MJTC_k[0].$MJTC_k[1]){
                    $m_lang .= $MJTC_k.', ';
                }
            }

            if($m_lang != ''){
                $m_lang = MJTC_majesticsupportphplib::MJTC_substr($m_lang, 0,MJTC_majesticsupportphplib::MJTC_strlen($m_lang) - 2);
                $MJTC_lang_name = $m_lang;
                $match = 2;
            }else{
                $MJTC_lang_name = $MJTC_lang_name;
                $match = false;
            }
        }

        return array('match' => $match , 'lang_name' => $MJTC_lang_name);
    }

    function validateAndShowDownloadFileName( ){
        if(!current_user_can('manage_options')){
            return false;
        }
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'validate-and-show-download-filename') ) {
            die( 'Security check Failed' );
        }

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

        $MJTC_lang_name = MJTC_request::MJTC_getVar('langname');
        if($MJTC_lang_name == '') return '';
        $MJTC_result = array();
        $MJTC_f_result = $this->makeLanguageCode($MJTC_lang_name);
        // $MJTC_path = MJTC_PLUGIN_PATH.'languages';
        $MJTC_path = WP_LANG_DIR . '/plugins/';
        $MJTC_result['error'] = false;

        if($MJTC_f_result['match'] === false){
            $MJTC_result['error'] = $MJTC_lang_name. ' ' . esc_html(__('Language is not installed','majestic-support'));
        // Replaced is_writeable() with $MJTC_wp_filesystem->is_writable()
        }elseif( ! $MJTC_wp_filesystem->is_writable($MJTC_path)){
            $MJTC_result['error'] = $MJTC_lang_name. ' ' . esc_html(__('Language directory is not writable','majestic-support')).': '.esc_url($MJTC_path);
        }else{
            $MJTC_result['input'] = '<input id="languagecode" class="text_area" type="text" value="'.esc_attr($MJTC_lang_name).'" name="languagecode">';
            if($MJTC_f_result['match'] === 2){
                $MJTC_result['input'] .= '<div id="mjtc-emessage-wrapper-other" style="display:block;margin:20px 0px 20px;">';
                $MJTC_result['input'] .= esc_html(__('Required language is not installed but similar language like','majestic-support')).': "<b>'.esc_html($MJTC_f_result['lang_name']).'</b>" '. esc_html(__('is found in your system','majestic-support'));
                $MJTC_result['input'] .= '</div>';
            }
            $MJTC_result['input'] = MJTC_majesticsupportphplib::MJTC_htmlentities($MJTC_result['input']);
            $MJTC_result['path'] = esc_html(__('Language code','majestic-support'));
        }
        $MJTC_result = wp_json_encode($MJTC_result);
        return $MJTC_result;
    }

    function getLanguageTranslation(){
        if(!current_user_can('manage_options')){
            return false;
        }
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'get-language-translation') ) {
            die( 'Security check Failed' );
        }

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

        $MJTC_lang_name = MJTC_request::MJTC_getVar('langname');
        $MJTC_language_code = MJTC_request::MJTC_getVar('filename');

        $MJTC_result = array();
        $MJTC_result['error'] = false;
        // $MJTC_path = MJTC_PLUGIN_PATH.'languages';
        $MJTC_path = WP_LANG_DIR . '/plugins/';

        // Replaced is_dir() and mkdir()
        if(!$MJTC_wp_filesystem->is_dir($MJTC_path)){
            $MJTC_wp_filesystem->mkdir($MJTC_path);
        }

        if($MJTC_lang_name == '' || $MJTC_language_code == ''){
            $MJTC_result['error'] = esc_html(__('Empty values','majestic-support'));
            return wp_json_encode($MJTC_result);
        }

        $MJTC_final_path = $MJTC_path.'/majestic-support-'.$MJTC_language_code.'.po';

        $MJTC_langarray = wp_get_installed_translations('core');
        $MJTC_langarray = $MJTC_langarray['default'];

        if(!array_key_exists($MJTC_language_code, $MJTC_langarray)){
            $MJTC_result['error'] = $MJTC_lang_name. ' ' . esc_html(__('Language is not installed','majestic-support'));
            return wp_json_encode($MJTC_result);
        // Replaced is_writeable()
        }elseif( ! $MJTC_wp_filesystem->is_writable($MJTC_path)){
            $MJTC_result['error'] = $MJTC_lang_name. ' ' . esc_html(__('Language directory is not writable','majestic-support')).': '.esc_url($MJTC_path);
            return wp_json_encode($MJTC_result);
        }

        // Replaced file_exists() and touch()
        if( ! $MJTC_wp_filesystem->exists($MJTC_final_path)){
            $MJTC_wp_filesystem->touch($MJTC_final_path);
        }

        // Replaced is_writeable()
        if( ! $MJTC_wp_filesystem->is_writable($MJTC_final_path)){
            $MJTC_result['error'] = esc_html(__('File is not writable','majestic-support')).': '.$MJTC_final_path;
        }else{

            if($this->isConnected()){

                $MJTC_url = "https://majesticsupport.com/translations/api/1.0/index.php";
                $MJTC_post_data['product'] ='majestic-support-wp';
                $MJTC_post_data['domain'] = get_site_url();
                $MJTC_post_data['producttype'] = majesticsupport::$_config['producttype'];
                $MJTC_post_data['productcode'] = 'mjsupport';
                $MJTC_post_data['productversion'] = majesticsupport::$_config['productversion'];
                $MJTC_post_data['JVERSION'] = get_bloginfo('version');
                $MJTC_post_data['translationcode'] = $MJTC_lang_name;
                $MJTC_post_data['method'] = 'getTranslationFile';

                $MJTC_response = wp_remote_post( $MJTC_url, array('body' => $MJTC_post_data,'timeout'=>7,'sslverify'=>false));
                if( !is_wp_error($MJTC_response) && $MJTC_response['response']['code'] == 200 && isset($MJTC_response['body']) ){
                    $MJTC_result_body = $MJTC_response['body'];
                }else{
                    $MJTC_result_body = false;
                    if(!is_wp_error($MJTC_response)){
                       $MJTC_error = $MJTC_response['response']['message'];
                    }else{
                        $MJTC_error = $MJTC_response->get_error_message();
                    }
                }
                if($MJTC_result_body){
                    $MJTC_json_res = json_decode($MJTC_result_body, true);
                    $MJTC_ret = $this->writeLanguageFile( $MJTC_final_path , $MJTC_json_res['file']);
                }else{
                    $MJTC_result = array();
                }
                $MJTC_result['data'] = esc_html(__('File successfully downloaded','majestic-support'));
            }else{
                $MJTC_result['error'] = esc_html(__('Unable to connect to the server','majestic-support'));
            }
        }

        $MJTC_result = wp_json_encode($MJTC_result);

        return $MJTC_result;
    }

    function writeLanguageFile( $MJTC_path , $MJTC_url ){
        $MJTC_result = true;
        do_action('majesticsupport_load_wp_admin_file');
        $tmpfile = download_url( $MJTC_url);
        copy( $tmpfile, $MJTC_path );
        if ( file_exists( $tmpfile ) ) {
            wp_delete_file( $tmpfile ); // must unlink afterwards
        }
        //make mo for po file
        $this->phpmo_convert($MJTC_path);
        return $MJTC_result;
    }

    function isConnected() {
        $MJTC_response = wp_remote_head("https://www.google.com", array('timeout' => 5));

        if ( is_wp_error($MJTC_response) ) {
            $MJTC_is_conn = false; // Connection failure
        } else {
            $MJTC_is_conn = true; // Connected
        }
        
        return $MJTC_is_conn;
    }

    function phpmo_convert($MJTC_input, $MJTC_output = false) {
        if ( !$MJTC_output )
            $MJTC_output = MJTC_majesticsupportphplib::MJTC_str_replace( '.po', '.mo', $MJTC_input );
        $MJTC_hash = $this->phpmo_parse_po_file( $MJTC_input );
        if ( $MJTC_hash === false ) {
            return false;
        } else {
            $this->phpmo_write_mo_file( $MJTC_hash, $MJTC_output );
            return true;
        }
    }

    function phpmo_clean_helper($MJTC_x) {
        if (is_array($MJTC_x)) {
            foreach ($MJTC_x as $MJTC_k => $MJTC_v) {
                $MJTC_x[$MJTC_k] = $this->phpmo_clean_helper($MJTC_v);
            }
        } else {
            if ($MJTC_x[0] == '"')
                $MJTC_x = MJTC_majesticsupportphplib::MJTC_substr($MJTC_x, 1, -1);
            $MJTC_x = MJTC_majesticsupportphplib::MJTC_str_replace("\"\n\"", '', $MJTC_x);
            $MJTC_x = MJTC_majesticsupportphplib::MJTC_str_replace('$', '\\$', $MJTC_x);
        }
        return $MJTC_x;
    }
    /* Parse gettext .po files. */
    /* @link http://www.gnu.org/software/gettext/manual/gettext.html#PO-Files */
    function phpmo_parse_po_file($MJTC_in) {
    if (!file_exists($MJTC_in)){ return false; }
    $MJTC_ids = array();
    $MJTC_strings = array();
    $MJTC_language = array();
    $MJTC_lines = file($MJTC_in);
    foreach ($MJTC_lines as $MJTC_line_num => $MJTC_line) {
        if (MJTC_majesticsupportphplib::MJTC_strstr($MJTC_line, 'msgid')){
			$MJTC_endpos = strrpos($MJTC_line, '"',7);
			if($MJTC_endpos > 7){ // to avoid msgid ""
				$MJTC_id = MJTC_majesticsupportphplib::MJTC_substr($MJTC_line, 7, $MJTC_endpos-7);
				$MJTC_ids[] = $MJTC_id;
			}
        }elseif(MJTC_majesticsupportphplib::MJTC_strstr($MJTC_line, 'msgstr')){
			$MJTC_endpos = strrpos($MJTC_line, '"',8);
			if($MJTC_endpos > 8){ // to avoid msgstr ""
				$MJTC_string = MJTC_majesticsupportphplib::MJTC_substr($MJTC_line, 8, $MJTC_endpos-8);
				$MJTC_strings[] = array($MJTC_string);
			}
        }else{}
    }
    for ($MJTC_i=0; $MJTC_i<count($MJTC_ids); $MJTC_i++){
        //Shoaib
        if(isset($MJTC_ids[$MJTC_i]) && isset($MJTC_strings[$MJTC_i])){
            $MJTC_language[$MJTC_ids[$MJTC_i]] = array('msgid' => $MJTC_ids[$MJTC_i], 'msgstr' =>$MJTC_strings[$MJTC_i]);
        }
    }
    return $MJTC_language;
    }
    /* Write a GNU gettext style machine object. */
    /* @link http://www.gnu.org/software/gettext/manual/gettext.html#MO-Files */
    function phpmo_write_mo_file($MJTC_hash, $MJTC_out) {
        // sort by msgid
        ksort($MJTC_hash, SORT_STRING);
        // our mo file data
        $mo = '';
        // header data
        $MJTC_offsets = array ();
        $MJTC_ids = '';
        $MJTC_strings = '';
        foreach ($MJTC_hash as $MJTC_entry) {
            $MJTC_id = $MJTC_entry['msgid'];
            $MJTC_str = implode("\x00", $MJTC_entry['msgstr']);
            // keep track of offsets
            $MJTC_offsets[] = array (
                            MJTC_majesticsupportphplib::MJTC_strlen($MJTC_ids), MJTC_majesticsupportphplib::MJTC_strlen($MJTC_id), MJTC_majesticsupportphplib::MJTC_strlen($MJTC_strings), MJTC_majesticsupportphplib::MJTC_strlen($MJTC_str)
                            );
            // plural msgids are not stored (?)
            $MJTC_ids .= $MJTC_id . "\x00";
            $MJTC_strings .= $MJTC_str . "\x00";
        }
        // keys start after the header (7 words) + index tables ($#hash * 4 words)
        $MJTC_key_start = 7 * 4 + sizeof($MJTC_hash) * 4 * 4;
        // values start right after the keys
        $MJTC_value_start = $MJTC_key_start +MJTC_majesticsupportphplib::MJTC_strlen($MJTC_ids);
        // first all key offsets, then all value offsets
        $MJTC_key_offsets = array ();
        $MJTC_value_offsets = array ();
        // calculate
        foreach ($MJTC_offsets as $MJTC_v) {
            list ($MJTC_o1, $MJTC_l1, $MJTC_o2, $MJTC_l2) = $MJTC_v;
            $MJTC_key_offsets[] = $MJTC_l1;
            $MJTC_key_offsets[] = $MJTC_o1 + $MJTC_key_start;
            $MJTC_value_offsets[] = $MJTC_l2;
            $MJTC_value_offsets[] = $MJTC_o2 + $MJTC_value_start;
        }
        $MJTC_offsets = array_merge($MJTC_key_offsets, $MJTC_value_offsets);
        // write header
        $mo .= pack('Iiiiiii', 0x950412de, // magic number
        0, // version
        sizeof($MJTC_hash), // number of entries in the catalog
        7 * 4, // key index offset
        7 * 4 + sizeof($MJTC_hash) * 8, // value index offset,
        0, // hashtable size (unused, thus 0)
        $MJTC_key_start // hashtable offset
        );
        // offsets
        foreach ($MJTC_offsets as $MJTC_offset)
            $mo .= pack('i', $MJTC_offset);
        // ids
        $mo .= $MJTC_ids;
        // strings
        $mo .= $MJTC_strings;
        file_put_contents($MJTC_out, $mo);
    }

    function stripslashesFull($MJTC_input){// testing this function/.
        if($MJTC_input == ''){
            return $MJTC_input;
        }
        if (is_array($MJTC_input)) {
            $MJTC_input = array_map(array($this,'stripslashesFull'), $MJTC_input);
        } elseif (is_object($MJTC_input)) {
            $MJTC_vars = get_object_vars($MJTC_input);
            foreach ($MJTC_vars as $MJTC_k=>$MJTC_v) {
                $MJTC_input->{$MJTC_k} = $this->stripslashesFull($MJTC_v);
            }
        } else {
            $MJTC_input = MJTC_majesticsupportphplib::MJTC_stripslashes($MJTC_input);
        }
        return $MJTC_input;
    }

    function getUserNameById($MJTC_id){
        if (!is_numeric($MJTC_id))
            return false;
        $MJTC_query = "SELECT user_nicename AS name FROM `" . majesticsupport::$_wpprefixforuser . "mjtc_support_users` WHERE id = " . esc_sql($MJTC_id);
        $MJTC_username = majesticsupport::$_db->get_var($MJTC_query);
        return $MJTC_username;
    }

    function getusersearchajax() {
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'get-usersearch-ajax') ) {
            die( 'Security check Failed' );
        }
        $MJTC_username = MJTC_request::MJTC_getVar('username');
        $MJTC_name = MJTC_request::MJTC_getVar('name');
        $MJTC_emailaddress = MJTC_request::MJTC_getVar('emailaddress');
        $MJTC_canloadresult = false;
        $MJTC_query = "SELECT DISTINCT user.id AS userid, user.name AS username, user.user_email AS useremail, user.display_name AS userdisplayname
                    FROM `" . majesticsupport::$_wpprefixforuser . "mjtc_support_users` AS user ";
                    if(in_array('agent',majesticsupport::$_active_addons)){
                        $MJTC_query .= " WHERE NOT EXISTS( SELECT staff.id FROM `" . majesticsupport::$_db->prefix . "mjtc_support_staff` AS staff WHERE user.id = staff.uid) ";
                    }else{
                        $MJTC_query .= " WHERE 1 = 1 "; // to handle filter cases
                    }
        if (MJTC_majesticsupportphplib::MJTC_strlen($MJTC_name) > 0) {
            $MJTC_query .= " AND user.display_name LIKE '%".esc_sql($MJTC_name)."%'";
            $MJTC_canloadresult = true;
        }
        if (MJTC_majesticsupportphplib::MJTC_strlen($MJTC_emailaddress) > 0) {
            $MJTC_query .= " AND user.user_email LIKE '%".esc_sql($MJTC_emailaddress)."%'";
            $MJTC_canloadresult = true;
        }
        if (MJTC_majesticsupportphplib::MJTC_strlen($MJTC_username) > 0) {
            $MJTC_query .= " AND user.name LIKE '%".esc_sql($MJTC_username)."%'";
            $MJTC_canloadresult = true;
        }
        if($MJTC_canloadresult){
            $MJTC_users = majesticsupport::$_db->get_results($MJTC_query);
            if(!empty($MJTC_users)){
                $MJTC_result ='
                <div class="mjtc-support-table-wrp">
                    <div class="mjtc-support-table-header">
                        <div class="mjtc-support-table-header-col mjtc-sprt-tbl-uid">'. esc_html(__('ID', 'majestic-support')).'</div>
                        <div class="mjtc-support-table-header-col mjtc-sprt-tbl-unm">'. esc_html(__('User Name', 'majestic-support')).'</div>
                        <div class="mjtc-support-table-header-col mjtc-sprt-tbl-eml">'. esc_html(__('Email Address', 'majestic-support')).'</div>
                        <div class="mjtc-support-table-header-col mjtc-sprt-tbl-nam">'. esc_html(__('Name', 'majestic-support')).'</div>
                    </div>
                    <div class="mjtc-support-table-body">';
                        foreach($MJTC_users AS $MJTC_user){
                            $MJTC_result .='
                            <div class="mjtc-support-data-row">
                                <div class="mjtc-support-table-body-col mjtc-sprt-tbl-uid">
                                    <span class="mjtc-support-display-block">'. esc_html(__('User ID','majestic-support')).'</span>'.esc_html($MJTC_user->userid).'
                                </div>
                                <div class="mjtc-support-table-body-col mjtc-sprt-tbl-unm">
                                    <span class="mjtc-support-display-block">'. esc_html(__('User Name','majestic-support')).':</span>
                                    '.esc_html($MJTC_user->username).'
                                </div>
                                <div class="mjtc-support-table-body-col mjtc-sprt-tbl-eml">
                                    <span class="mjtc-support-display-block">'. esc_html(__('Email','majestic-support')).':</span>
                                    <span class="mjtc-support-title">
                                        <a href="#" class="mjtc-userpopup-link" data-id="'.esc_attr($MJTC_user->userid).'" data-username="'.esc_attr($MJTC_user->username).'" data-email="'.esc_attr($MJTC_user->useremail).'" data-name="'.esc_attr($MJTC_user->userdisplayname).'">
                                            '. esc_html($MJTC_user->useremail) .'
                                        </a>
                                    </span>
                                </div>
                                <div class="mjtc-support-table-body-col mjtc-sprt-tbl-nam">
                                    <span class="mjtc-support-display-block">'. esc_html(__('Name','majestic-support')).':</span>
                                    '.esc_html($MJTC_user->userdisplayname).'
                                </div>
                            </div>';
                        }
                $MJTC_result .='</div>';
            }else{
                $MJTC_result= MJTC_layout::MJTC_getNoRecordFound();
            }
        }else{ // reset button
            $MJTC_result = $this->getuserlistajax(0);
        }

        return $MJTC_result;
    }



    function getuserlistajax($MJTC_ajaxCall = 1){
        if ($MJTC_ajaxCall == 1) {
            $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
            if (! wp_verify_nonce( $MJTC_nonce, 'get-user-list-ajax') ) {
                die( 'Security check Failed' );
            }
        }
        $MJTC_userlimit = MJTC_request::MJTC_getVar('userlimit',null,0);
        $MJTC_maxrecorded = 4;
        $MJTC_query = "SELECT DISTINCT COUNT(user.id)
                    FROM `" . majesticsupport::$_wpprefixforuser . "mjtc_support_users` AS user 
					WHERE user.status = 1 ";
                    if(in_array('agent',majesticsupport::$_active_addons)){
                        $MJTC_query .= " AND NOT EXISTS( SELECT staff.id FROM `" . majesticsupport::$_db->prefix . "mjtc_support_staff` AS staff WHERE user.id = staff.uid) ";
                    }

        $total = majesticsupport::$_db->get_var($MJTC_query);
        $MJTC_limit = $MJTC_userlimit * $MJTC_maxrecorded;
        if($MJTC_limit >= $total){
            $MJTC_limit = 0;
        }
        $MJTC_query = "SELECT DISTINCT user.id AS userid, user.name AS username, user.user_email AS useremail,
                    user.display_name AS userdisplayname
                    FROM `" . majesticsupport::$_wpprefixforuser . "mjtc_support_users` AS user 
					WHERE user.status = 1";
                    if(in_array('agent',majesticsupport::$_active_addons)){
                        $MJTC_query .= " AND NOT EXISTS( SELECT staff.id FROM `" . majesticsupport::$_db->prefix . "mjtc_support_staff` AS staff WHERE user.id = staff.uid) ";
                    }
                    $MJTC_query .= " LIMIT $MJTC_limit, $MJTC_maxrecorded";
        $MJTC_users = majesticsupport::$_db->get_results($MJTC_query);
        $MJTC_html = $this->makeUserList($MJTC_users,$total,$MJTC_maxrecorded,$MJTC_userlimit);
        return $MJTC_html;

    }

    function makeUserList($MJTC_users,$total,$MJTC_maxrecorded,$MJTC_userlimit){
        $MJTC_html = '';
        if(!empty($MJTC_users)){
            if(is_array($MJTC_users)){
                $MJTC_html ='
                <div class="mjtc-support-table-wrp">
                    <div class="mjtc-support-table-header">
                        <div class="mjtc-support-table-header-col mjtc-sprt-tbl-uid">'. esc_html(__('ID', 'majestic-support')).'</div>
                        <div class="mjtc-support-table-header-col mjtc-sprt-tbl-unm">'. esc_html(__('User Name', 'majestic-support')).'</div>
                        <div class="mjtc-support-table-header-col mjtc-sprt-tbl-eml">'. esc_html(__('Email Address', 'majestic-support')).'</div>
                        <div class="mjtc-support-table-header-col mjtc-sprt-tbl-nam">'. esc_html(__('Name', 'majestic-support')).'</div>
                    </div>
                    <div class="mjtc-support-table-body">';
                        foreach($MJTC_users AS $MJTC_user){
                            $MJTC_html .='
                            <div class="mjtc-support-data-row">
                                <div class="mjtc-support-table-body-col mjtc-sprt-tbl-uid">
                                    <span class="mjtc-support-display-block">'. esc_html(__('User ID','majestic-support')).'</span>'.esc_html($MJTC_user->userid).'
                                </div>
                                <div class="mjtc-support-table-body-col mjtc-sprt-tbl-unm">
                                    <span class="mjtc-support-display-block">'. esc_html(__('User Name','majestic-support')).':</span>
                                    '.esc_html($MJTC_user->username).'
                                </div>
                                <div class="mjtc-support-table-body-col mjtc-sprt-tbl-eml">
                                    <span class="mjtc-support-display-block">'. esc_html(__('Email','majestic-support')).':</span>
                                    <span class="mjtc-support-title">
                                        <a href="#" class="mjtc-userpopup-link" data-id="'.esc_attr($MJTC_user->userid).'" data-email="'.esc_attr($MJTC_user->useremail).'" data-username="'.esc_attr($MJTC_user->username).'" data-name="'.esc_attr($MJTC_user->userdisplayname).'">
                                            '.esc_html($MJTC_user->useremail).'
                                        </a>
                                    </span>
                                </div>
                                <div class="mjtc-support-table-body-col mjtc-sprt-tbl-nam">
                                    <span class="mjtc-support-display-block">'. esc_html(__('Name','majestic-support')).':</span>
                                    '.esc_html($MJTC_user->userdisplayname).'
                                </div>
                            </div>';
                        }
                $MJTC_html .='</div>';
            }
            $MJTC_num_of_pages = ceil($total / $MJTC_maxrecorded);
            $MJTC_num_of_pages = ($MJTC_num_of_pages > 0) ? ceil($MJTC_num_of_pages) : floor($MJTC_num_of_pages);
            if($MJTC_num_of_pages > 0){
                $MJTC_page_html = '';
                $MJTC_prev = $MJTC_userlimit;
                if($MJTC_prev > 0){
                    $MJTC_page_html .= '<a class="ms_userlink" href="#" onclick="updateuserlist('.esc_js(($MJTC_prev - 1)).');">'. esc_html(__('Previous','majestic-support')).'</a>';
                }
                for($MJTC_i = 0; $MJTC_i < $MJTC_num_of_pages; $MJTC_i++){
                    if($MJTC_i == $MJTC_userlimit)
                        $MJTC_page_html .= '<span class="ms_userlink selected" >'.($MJTC_i + 1).'</span>';
                    else
                        $MJTC_page_html .= '<a class="ms_userlink" href="#" onclick="updateuserlist('.esc_js($MJTC_i).');">'.esc_js(($MJTC_i + 1)).'</a>';

                }
                $MJTC_next = $MJTC_userlimit + 1;
                if($MJTC_next < $MJTC_num_of_pages){
                    $MJTC_page_html .= '<a class="ms_userlink" href="#" onclick="updateuserlist('.esc_js($MJTC_next).');">'. esc_html(__('Next','majestic-support')).'</a>';
                }
                if($MJTC_page_html != ''){
                    $MJTC_html .= '<div class="tablenav"><div class="ms_userpages">'.wp_kses($MJTC_page_html, MJTC_ALLOWED_TAGS).'</div></div>';
                }
            }

        }else{
            $MJTC_html = MJTC_layout::MJTC_getNoRecordFound();
        }
        echo wp_kses($MJTC_html, MJTC_ALLOWED_TAGS);
        die();
        return $MJTC_html;
    }

    function storeOrderingFromPage($MJTC_data) {//
        if (empty($MJTC_data)) {
            return false;
        }
        $MJTC_sorted_array = array();
        MJTC_majesticsupportphplib::MJTC_parse_str($MJTC_data['fields_ordering_new'],$MJTC_sorted_array);
        $MJTC_sorted_array = reset($MJTC_sorted_array);
        if(!empty($MJTC_sorted_array)){

            if($MJTC_data['ordering_for'] == 'department'){
                $MJTC_row = MJTC_includer::MJTC_getTable('departments');
                $MJTC_ordering_coloumn = 'ordering';
            }elseif($MJTC_data['ordering_for'] == 'priority'){
                $MJTC_row = MJTC_includer::MJTC_getTable('priorities');
                $MJTC_ordering_coloumn = 'ordering';
            }elseif($MJTC_data['ordering_for'] == 'status'){
                $MJTC_row = MJTC_includer::MJTC_getTable('statuses');
                $MJTC_ordering_coloumn = 'ordering';
            }elseif($MJTC_data['ordering_for'] == 'product'){
                $MJTC_row = MJTC_includer::MJTC_getTable('products');
                $MJTC_ordering_coloumn = 'ordering';
            }elseif($MJTC_data['ordering_for'] == 'fieldsordering'){
                $MJTC_row = MJTC_includer::MJTC_getTable('fieldsordering');
                $MJTC_ordering_coloumn = 'ordering';
            }elseif($MJTC_data['ordering_for'] == 'announcement'){
                $MJTC_row = MJTC_includer::MJTC_getTable('announcement');
                $MJTC_ordering_coloumn = 'ordering';
            }elseif($MJTC_data['ordering_for'] == 'faq'){
                $MJTC_row = MJTC_includer::MJTC_getTable('faq');
                $MJTC_ordering_coloumn = 'ordering';
            }elseif($MJTC_data['ordering_for'] == 'helptopic'){
                $MJTC_row = MJTC_includer::MJTC_getTable('helptopic');
                $MJTC_ordering_coloumn = 'ordering';
            }elseif($MJTC_data['ordering_for'] == 'article'){
                $MJTC_row = MJTC_includer::MJTC_getTable('articles');
                $MJTC_ordering_coloumn = 'ordering';
            }elseif($MJTC_data['ordering_for'] == 'download'){
                $MJTC_row = MJTC_includer::MJTC_getTable('download');
                $MJTC_ordering_coloumn = 'ordering';
            }elseif($MJTC_data['ordering_for'] == 'fieldordering'){
                $MJTC_row = MJTC_includer::MJTC_getTable('fieldsordering');
                $MJTC_ordering_coloumn = 'ordering';
            }elseif($MJTC_data['ordering_for'] == 'multiform'){
                $MJTC_row = MJTC_includer::MJTC_getTable('multiform');
                $MJTC_ordering_coloumn = 'ordering';
            }elseif($MJTC_data['ordering_for'] == 'ticketclosereason'){
                $MJTC_row = MJTC_includer::MJTC_getTable('ticketclosereason');
                $MJTC_ordering_coloumn = 'ordering';
            }

            $MJTC_page_multiplier = 1;
            if($MJTC_data['pagenum_for_ordering'] > 1){
                $MJTC_page_multiplier = ($MJTC_data['pagenum_for_ordering'] - 1) * majesticsupport::$_config['pagination_default_page_size'] + 1;
            }
            for ($MJTC_i=0; $MJTC_i < count($MJTC_sorted_array) ; $MJTC_i++) {
                $MJTC_row->update(array('id' => $MJTC_sorted_array[$MJTC_i], $MJTC_ordering_coloumn => $MJTC_page_multiplier + $MJTC_i));
            }
        }
        MJTC_message::MJTC_setMessage(esc_html(__('Ordering updated', 'majestic-support')), 'updated');
        return ;
    }

    function updateDate($MJTC_addon_name,$MJTC_plugin_version){
        return MJTC_includer::MJTC_getModel('premiumplugin')->verfifyAddonActivation($MJTC_addon_name);
    }

    function getAddonSqlForActivation($MJTC_addon_name,$MJTC_addon_version){
        return MJTC_includer::MJTC_getModel('premiumplugin')->verifyAddonSqlFile($MJTC_addon_name,$MJTC_addon_version);
    }

    function installPluginFromAjax(){
        if(!current_user_can('manage_options')){
            return false;
        }
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'install-plugin-ajax') ) {
             die( 'Security check Failed' ); 
        }
        if(current_user_can( 'install_plugins' )){
            $MJTC_pluginslug = MJTC_request::MJTC_getVar('pluginslug');
            if(file_exists(plugins_url($MJTC_pluginslug . '/' . $MJTC_pluginslug . '.php'))){
                return false;
            }
            if($MJTC_pluginslug != ""){
                do_action('majesticsupport_load_wp_plugin_install');
                do_action('majesticsupport_load_wp_upgrader');
                do_action('majesticsupport_load_wp_ajax_upgrader_skin');
                do_action('majesticsupport_load_wp_plugin_upgrader');

                // Get Plugin Info
                $MJTC_api = plugins_api( 'plugin_information',
                    array(
                        'slug' => $MJTC_pluginslug,
                        'fields' => array(
                            'short_description' => false,
                            'sections' => false,
                            'requires' => false,
                            'rating' => false,
                            'ratings' => false,
                            'downloaded' => false,
                            'last_updated' => false,
                            'added' => false,
                            'tags' => false,
                            'compatibility' => false,
                            'homepage' => false,
                            'donate_link' => false,
                        ),
                    )
                );
                $MJTC_skin     = new WP_Ajax_Upgrader_Skin();
                $MJTC_upgrader = new Plugin_Upgrader( $MJTC_skin );
                $MJTC_upgrader->install( $MJTC_api->download_link );
                if(file_exists(plugins_url($MJTC_pluginslug . '/' . $MJTC_pluginslug . '.php'))){
                    return true;
                }
            }
        }
        return false;
    }

    function activatePluginFromAjax(){
        if(!current_user_can('manage_options')){
            return false;
        }
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'activate-plugin-ajax') ) {
             die( 'Security check Failed' ); 
        }
        if(current_user_can( 'activate_plugins')){
            $MJTC_pluginslug = MJTC_request::MJTC_getVar('pluginslug');
            do_action('majesticsupport_load_wp_plugin_file');
            if(file_exists(plugins_url($MJTC_pluginslug . '/' . $MJTC_pluginslug . '.php'))){
                $MJTC_isactivate = is_plugin_active($MJTC_pluginslug.'/'.$MJTC_pluginslug.'.php');
                if($MJTC_isactivate){
                    return false;
                }
                if($MJTC_pluginslug != ""){
                    if(!defined( 'WP_ADMIN')){
                        define( 'WP_ADMIN', TRUE );
                    }
                    // define( 'WP_NETWORK_ADMIN', TRUE ); // Need for Multisite
                    if(!defined( 'WP_USER_ADMIN')){
                        define( 'WP_USER_ADMIN', TRUE );
                    }

                    ob_get_clean();
                    do_action('majesticsupport_load_wp_admin_file');
                    do_action('majesticsupport_load_wp_plugin_file');
                    activate_plugin( $MJTC_pluginslug.'/'.$MJTC_pluginslug.'.php' );
                    $MJTC_isactivate = is_plugin_active($MJTC_pluginslug.'/'.$MJTC_pluginslug.'.php');
                    if($MJTC_isactivate){
                        return true;
                    }
                }
            }
        }
        return false;
    }

    function MJTC_getDateFormat(){
        $MJTC_dateformat = majesticsupport::$_config['date_format'];
        if ($MJTC_dateformat == 'm/d/Y' || $MJTC_dateformat == 'd/m/y' || $MJTC_dateformat == 'm/d/y' || $MJTC_dateformat == 'd/m/Y') {
            $MJTC_dash = '/';
        } else {
            $MJTC_dash = '-';
        }
        $MJTC_firstdash = MJTC_majesticsupportphplib::MJTC_strpos($MJTC_dateformat, $MJTC_dash, 0);
        $MJTC_firstvalue = MJTC_majesticsupportphplib::MJTC_substr($MJTC_dateformat, 0, $MJTC_firstdash);
        $MJTC_firstdash = $MJTC_firstdash + 1;
        $MJTC_seconddash = MJTC_majesticsupportphplib::MJTC_strpos($MJTC_dateformat, $MJTC_dash, $MJTC_firstdash);
        $MJTC_secondvalue = MJTC_majesticsupportphplib::MJTC_substr($MJTC_dateformat, $MJTC_firstdash, $MJTC_seconddash - $MJTC_firstdash);
        $MJTC_seconddash = $MJTC_seconddash + 1;
        $thirdvalue = MJTC_majesticsupportphplib::MJTC_substr($MJTC_dateformat, $MJTC_seconddash, MJTC_majesticsupportphplib::MJTC_strlen($MJTC_dateformat) - $MJTC_seconddash);
        $mjtc_dateformat = '%' . $MJTC_firstvalue . $MJTC_dash . '%' . $MJTC_secondvalue . $MJTC_dash . '%' . $thirdvalue;
        $mjtc_scriptdateformat = $MJTC_firstvalue . $MJTC_dash . $MJTC_secondvalue . $MJTC_dash . $thirdvalue;
        if($mjtc_scriptdateformat != ''){
            $mjtc_scriptdateformat = MJTC_majesticsupportphplib::MJTC_str_replace('Y', 'yy', $mjtc_scriptdateformat);
            $mjtc_scriptdateformat = MJTC_majesticsupportphplib::MJTC_str_replace('m', 'mm', $mjtc_scriptdateformat);
            $mjtc_scriptdateformat = MJTC_majesticsupportphplib::MJTC_str_replace('d', 'dd', $mjtc_scriptdateformat);
        }
        return $mjtc_scriptdateformat;
    }

    function getAddonTransationKey($MJTC_option_name){
        $MJTC_query = "SELECT `option_value` FROM " . majesticsupport::$_wpprefixforuser . "options WHERE option_name = '".esc_sql($MJTC_option_name)."'";
        $transactionKey = majesticsupport::$_db->get_var($MJTC_query);
		if($transactionKey == ""){
			$transactionKey = get_option($MJTC_option_name);
		}
        return $transactionKey;
    }

    function getInstalledTranslationKey(){
        do_action('majesticsupport_load_wp_translation_install');
        $MJTC_activated_lang = get_option('WPLANG','en_US');
        $MJTC_install_lang_name = wp_get_available_translations();
        if(isset($MJTC_install_lang_name[$MJTC_activated_lang])){
            $MJTC_lang_name = $this->makeLanguageCode($MJTC_activated_lang);
            $MJTC_install_lang_name = $MJTC_install_lang_name[$MJTC_activated_lang]['english_name'];
            if($MJTC_activated_lang == "" || $MJTC_activated_lang == 'en_US'){
                update_option( 'mjtc_tran_lang_exists', false);
                return false;
            }else{
                $MJTC_path = WP_LANG_DIR . '/plugins/';
                $MJTC_final_path = $MJTC_path.'/majestic-support-'.$MJTC_activated_lang.'.po';
                if(file_exists($MJTC_final_path)){
                    update_option( 'mjtc_tran_lang_exists', false);
                    return false;
                }
                if(get_option( 'mjtc_tran_lang_exists', '') != ''){
                    $MJTC_session = json_decode(get_option( 'mjtc_tran_lang_exists', ''));
                    if($MJTC_session->code == $MJTC_activated_lang){
                        return get_option( 'mjtc_tran_lang_exists');
                    }
                }
                $MJTC_url = "https://majesticsupport.com/translations/api/1.0/index.php";
                $MJTC_post_data['product'] ='majestic-support-wp';
                $MJTC_post_data['domain'] = get_site_url();
                $MJTC_post_data['producttype'] = majesticsupport::$_config['producttype'];
                $MJTC_post_data['productcode'] = 'mjsupport';
                $MJTC_post_data['productversion'] = majesticsupport::$_config['productversion'];
                $MJTC_post_data['JVERSION'] = get_bloginfo('version');
                $MJTC_post_data['translationcode'] = $MJTC_activated_lang;
                $MJTC_post_data['method'] = 'getTranslationFile';

                $MJTC_response = wp_remote_post( $MJTC_url, array('body' => $MJTC_post_data,'timeout'=>7,'sslverify'=>false));
                if( !is_wp_error($MJTC_response) && $MJTC_response['response']['code'] == 200 && isset($MJTC_response['body']) ){
                    $MJTC_result = $MJTC_response['body'];
                }else{
                    $MJTC_result = false;
                    if(!is_wp_error($MJTC_response)){
                       $MJTC_error = $MJTC_response['response']['message'];
                    }else{
                        $MJTC_error = $MJTC_response->get_error_message();
                    }
                }
                if($MJTC_result){
                    $MJTC_array = json_decode($MJTC_result, true);
                }else{
                    $MJTC_array = array();
                }
                if(is_array($MJTC_array) && isset($MJTC_array['file'])){
                    $mjtc_tran_lang_exists = array("code" => $MJTC_activated_lang, "lang_fullname" => $MJTC_install_lang_name , "name" => $MJTC_lang_name);
                    $mjtc_tran_lang_exists = wp_json_encode($mjtc_tran_lang_exists);
                    update_option( 'mjtc_tran_lang_exists', $mjtc_tran_lang_exists);
                    return $mjtc_tran_lang_exists;
                }else{
                    update_option( 'mjtc_tran_lang_exists', false);
                    return false;
                }
            }
        }
        return false;
    }
    function getWPUidById($MJTC_id){
        if(!is_numeric($MJTC_id)){
            return false;
        }

        $MJTC_query = "SELECT user.wpuid
                    FROM `" . majesticsupport::$_db->prefix . "mjtc_support_users` AS user 
                    WHERE id = ".esc_sql($MJTC_id);
        $MJTC_wpuid = majesticsupport::$_db->get_var($MJTC_query);
        return $MJTC_wpuid;
    }

    function reviewBoxAction(){
        if(!current_user_can('manage_options')){
            return false;
        }
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'review-box-action') ) {
            die( 'Security check Failed' );
        }
        $MJTC_days = MJTC_request::MJTC_getVar('days');
        if($MJTC_days == -1) {
            add_option("majesticsupport_hide_review_box", "1");
        } else {
            $MJTC_date = gmdate("Y-m-d", MJTC_majesticsupportphplib::MJTC_strtotime("+".$MJTC_days." days"));
            update_option("majesticsupport_show_review_box_after", $MJTC_date);
        }
        return true;
    }

    function getShortCodeData(){
        if( in_array('multiform', majesticsupport::$_active_addons) ){
            $MJTC_query = "SELECT multiform.id, multiform.title, department.departmentname FROM `" . majesticsupport::$_db->prefix . "mjtc_support_multiform` AS multiform
                LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_departments` AS department ON multiform.departmentid = department.id WHERE multiform.status = 1 ORDER BY multiform.id ASC";
            majesticsupport::$_data[0]['multiforms'] = majesticsupport::$_db->get_results($MJTC_query);
        }
        return true;
    }

    function checkIfMainCssFileIsEnqued(){
        global $wp_styles;
        wp_enqueue_style('majesticsupport-icon-css', MJTC_PLUGIN_URL . 'includes/css/majestic_support.css', array(), '1.0.0');
        if (!in_array('majesticsupport-main-css',$wp_styles->queue)) {
            // responsive style sheets
            wp_enqueue_style('majesticsupport-desktop-css', MJTC_PLUGIN_URL . 'includes/css/style_desktop.css',array(),'1.0.0','(min-width: 783px) and (max-width: 1280px)');
            wp_enqueue_style('majesticsupport-tablet-css', MJTC_PLUGIN_URL . 'includes/css/style_tablet.css',array(),'1.0.0','(min-width: 668px) and (max-width: 782px)');
            wp_enqueue_style('majesticsupport-mobile-css', MJTC_PLUGIN_URL . 'includes/css/style_mobile.css',array(),'1.0.0','(min-width: 481px) and (max-width: 667px)');
            wp_enqueue_style('majesticsupport-oldmobile-css', MJTC_PLUGIN_URL . 'includes/css/style_oldmobile.css',array(),'1.0.0','(max-width: 480px)');
            if(is_rtl()){
                wp_enqueue_style('majesticsupport-main-css-rtl', MJTC_PLUGIN_URL . 'includes/css/stylertl.css',array(),'1.0.0');
            }
        }
        return true;
    }

    function updateColorFile(){
        // restore colors data
        $MJTC_color_string_values = get_option("ms_set_theme_colors");
        if($MJTC_color_string_values != ''){
            $MJTC_json_values = json_decode($MJTC_color_string_values,true);
            if(is_array($MJTC_json_values) && !empty($MJTC_json_values)){
                MJTC_includer::MJTC_getModel('themes')->MJTC_generateColorVariablesFile($MJTC_json_values);
            }
        }
        // restore colors data end
    }

    function getSiteUrl(){
        $MJTC_site_url = site_url();
        if($MJTC_site_url != ''){
            $MJTC_site_url = MJTC_majesticsupportphplib::MJTC_str_replace("https://","",$MJTC_site_url);
            $MJTC_site_url = MJTC_majesticsupportphplib::MJTC_str_replace("http://","",$MJTC_site_url);
        }
        return $MJTC_site_url;
    }

    function getNetworkSiteUrl(){
        $MJTC_network_site_url = network_site_url();
        if($MJTC_network_site_url != ''){
            $MJTC_network_site_url = MJTC_majesticsupportphplib::MJTC_str_replace("https://","",$MJTC_network_site_url);
            $MJTC_network_site_url = MJTC_majesticsupportphplib::MJTC_str_replace("http://","",$MJTC_network_site_url);
        }
        return $MJTC_network_site_url;
    }

    function addMissingUsers($MJTC_show_message = 1){
        $missingUser = 0;
        $MJTC_query = "SELECT id FROM `" . majesticsupport::$_db->prefix . "users`";
        $MJTC_users = majesticsupport::$_db->get_results($MJTC_query);
        $MJTC_wpUsers = array();
        $msUsers = array();
        foreach ($MJTC_users as $MJTC_key => $MJTC_user) {
            $MJTC_wpUsers[] = $MJTC_user->id;
        }
        $MJTC_query = " SELECT wpuid FROM `" . majesticsupport::$_db->prefix . "mjtc_support_users`";
        $MJTC_users = majesticsupport::$_db->get_results($MJTC_query);
        foreach ($MJTC_users as $MJTC_key => $MJTC_user) {
            $msUsers[] = $MJTC_user->wpuid;
        }

        $missingUsers = array_diff($MJTC_wpUsers,$msUsers);
        foreach ($missingUsers as $missingUser) {
            $MJTC_query = "SELECT count(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_users` WHERE wpuid = " . esc_sql($missingUser);
            $total = majesticsupport::$_db->get_var($MJTC_query);
            if ($total == 0) {
                $MJTC_query = "SELECT * FROM `" . majesticsupport::$_db->prefix . "users` WHERE id = " . esc_sql($missingUser);
                $MJTC_user = majesticsupport::$_db->get_row($MJTC_query);                
                if (isset($MJTC_user)) {
                    $MJTC_row = MJTC_includer::MJTC_getTable('users');
                    $MJTC_data['wpuid'] = $MJTC_user->ID;
                    $MJTC_data['name'] = $MJTC_user->display_name;
                    $MJTC_data['display_name'] = $MJTC_user->display_name;
                    $MJTC_data['user_nicename'] = $MJTC_user->user_nicename;
                    $MJTC_data['user_email'] = $MJTC_user->user_email;
                    $MJTC_data['issocial'] = 0;
                    $MJTC_data['socialid'] = null;
                    $MJTC_data['status'] = 1;
                    $MJTC_data['created'] = date_i18n('Y-m-d H:i:s');
                    $MJTC_row->bind($MJTC_data);
                    $MJTC_row->store();
                    $missingUser = 1;
                }
            }
        }
        if ($MJTC_show_message == 1) {
            if ($missingUser == 1) {
                MJTC_message::MJTC_setMessage(esc_html(__('Missing user(s) added successfully!', 'majestic-support')), 'updated');
            } else {
                MJTC_message::MJTC_setMessage(esc_html(__('No missing user found!', 'majestic-support')), 'error');
            }
        }
        return;
    }

    function getPageTitle($MJTC_layouts){
        $MJTC_breadCrumbs = "";
        $MJTC_actionButton = "";
        $MJTC_videoButton = "";
        $title = __("About Us", 'majestic-support');
        switch ($MJTC_layouts) {
            case 'dashboard':
                if(in_array('agent', majesticsupport::$_active_addons)){
                    $MJTC_actionButton = "<a href=\"?page=majesticsupport_agent\" class=\"mjtc-admin-btn mjtc-admin-btn-primary\" title=\"". esc_html(__('Agents', 'majestic-support')) ."\">
                        <svg viewBox=\"0 0 24 24\" width=\"22\" height=\"22\" fill=\"#ffffff\">
                          <path d=\"M16 11c1.66 0 3-1.79 3-4s-1.34-4-3-4-3 1.79-3 4 1.34 4 3 4zM8 11c1.66 0 3-1.79 3-4S9.66 3 8 3 5 4.79 5 7s1.34 4 3 4zm0 2c-2.67 0-8 1.34-8 4v2h10v-2c0-1.46.8-2.74 2.09-3.63C10.94 13.13 9.39 13 8 13zm8 0c-.29 0-.62.02-.97.05C16.83 14.03 18 15.4 18 17v2h6v-2c0-2.66-5.33-4-8-4z\"></path>
                        </svg>
                        ". esc_html(__('Agents', 'majestic-support')) ."
                    </a>";
                }
                $MJTC_actionButton .= "<a href=\"?page=majesticsupport_ticket\" class=\"mjtc-admin-btn mjtc-admin-btn-primary\" title=\"". esc_html(__('All Tickets', 'majestic-support')) ."\">
                    <svg viewBox=\"0 0 24 24\" width=\"22\" height=\"22\" fill=\"#ffffff\">
                      <path d=\"M21 7h-2V5a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v2H3a1 1 0 0 0-1 1v3a1 1 0 0 1 0 2v3a1 1 0 0 0 1 1h2v2a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-2h2a1 1 0 0 0 1-1v-3a1 1 0 0 1 0-2V8a1 1 0 0 0-1-1zM9 7h2v2H9V7zm0 4h2v2H9v-2zm0 4h2v2H9v-2z\"></path>
                    </svg>
                    ". esc_html(__('All Tickets', 'majestic-support'))."
                </a>";
                $MJTC_hideBredcurms = true;
                $title = __("Dashboard", 'majestic-support');
                $description = __("Monitor tickets, track agent activity, and manage your support system from one place.", 'majestic-support');
                break;
            case 'admin_aboutus':
                $title = __("About Us", 'majestic-support');
                $description = __("Get to know Majestic Support, the powerful ticket support system designed to streamline customer communication and support workflows.", 'majestic-support');
                break;
            case 'translations':
                $MJTC_videoButton = "<a target=\"blank\" href=\"#\" class=\"msadmin-video-link mjtc-cp-video-popup\" title=\"". esc_html(__('How to translate Majestic Support', 'majestic-support'))."\">
                    <svg viewBox=\"0 0 24 24\" width=\"22\" height=\"22\" fill=\"#6b7280\">
                      <path d=\"M21.8 8.2s-.2-1.5-.8-2.2c-.8-.9-1.7-.9-2.1-1C15.9 5 12 5 12 5h0s-3.9 0-6.9.1c-.4.1-1.3.1-2.1 1C2.4 6.7 2.2 8.2 2.2 8.2S2 9.9 2 11.6v.8c0 1.7.2 3.4.2 3.4s.2 1.5.8 2.2c.8.9 1.9.8 2.4.9 1.7.1 6.6.1 6.6.1s3.9 0 6.9-.1c.4-.1 1.3-.1 2.1-1 .6-.7.8-2.2.8-2.2s.2-1.7.2-3.4v-.8c0-1.7-.2-3.4-.2-3.4zM10 14.6V9.4l5 2.6-5 2.6z\"></path>
                    </svg>
                </a>";
                $title = __("Translations", 'majestic-support');
                $description = __("Configure and manage translations for Majestic Support.", 'majestic-support');
                break;
            case 'systemerror':
                $MJTC_actionButton = "<a class=\"mjtc-admin-btn mjtc-admin-btn-primary\" onclick=\"return confirm('". esc_html(__('Are you sure you want to delete?', 'majestic-support')) ."');\" href=\"". esc_url(wp_nonce_url('?page=majesticsupport_systemerror&task=deletesystemerror&action=mstask&systemerrorid=all','delete-systemerror-all')) ."\"><svg viewBox=\"0 0 24 24\" width=\"22\" height=\"22\" fill=\"#ffffff\"><path d=\"M6 7h12l-1 14a2 2 0 0 1-2 2H9a2 2 0 0 1-2-2L6 7zm3-4h6l1 2h4v2H2V5h4l1-2z\"></path></svg>". esc_html(__('Remove All', 'majestic-support')) ."</a>";
                $title = __("System Errors", 'majestic-support');
                $description = __("Track and review system errors to maintain smooth plugin performance.", 'majestic-support');
            break;
            case 'admin_addticket':
                $MJTC_videoButton = "<a target=\"blank\" href=\"https://www.youtube.com/watch?v=dYniAnKyv-Q\" class=\"msadmin-video-link mjtc-cp-video-popup\" title=\"". esc_html(__('How to create ticket', 'majestic-support')) ."\">
                    <svg viewBox=\"0 0 24 24\" width=\"22\" height=\"22\" fill=\"#6b7280\">
                      <path d=\"M21.8 8.2s-.2-1.5-.8-2.2c-.8-.9-1.7-.9-2.1-1C15.9 5 12 5 12 5h0s-3.9 0-6.9.1c-.4.1-1.3.1-2.1 1C2.4 6.7 2.2 8.2 2.2 8.2S2 9.9 2 11.6v.8c0 1.7.2 3.4.2 3.4s.2 1.5.8 2.2c.8.9 1.9.8 2.4.9 1.7.1 6.6.1 6.6.1s3.9 0 6.9-.1c.4-.1 1.3-.1 2.1-1 .6-.7.8-2.2.8-2.2s.2-1.7.2-3.4v-.8c0-1.7-.2-3.4-.2-3.4zM10 14.6V9.4l5 2.6-5 2.6z\"></path>
                    </svg>
                </a>";
                $title = __("Create Ticket", 'majestic-support');
                $description = __("Create a new support ticket on behalf of a user and manage customer issues efficiently.", 'majestic-support');
                break;
            case 'ticketdetail':
                $title = __("Ticket Detail", 'majestic-support');
                if (isset(majesticsupport::$_data[0]->subject)) {
                    $MJTC_customTitle = esc_html(majesticsupport::MJTC_getVarValue(majesticsupport::$_data[0]->subject));

                }
                $description = __("View complete ticket details, conversation history, and manage responses efficiently.", 'majestic-support');
                break;
            case 'userfields':
                if(isset(majesticsupport::$_data['formid']) && majesticsupport::$_data['formid'] != null){
                    $MJTC_mformid = majesticsupport::$_data['formid'];
                } else {
                    $MJTC_mformid = MJTC_includer::MJTC_getModel('ticket')->getDefaultMultiFormId();
                }
                $MJTC_actionButton = "<a title=\"". esc_html(__('Add','majestic-support')) ."\" class=\"mjtc-admin-btn mjtc-admin-btn-primary\" href=\"?page=majesticsupport_fieldordering&mjslay=adduserfeild&&fieldfor=". esc_attr(majesticsupport::$_data['fieldfor']) ."&formid=". esc_attr($MJTC_mformid) ."\"><svg viewBox=\"0 0 24 24\" width=\"20\" height=\"20\" fill=\"white\"><path d=\"M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z\"></path></svg>". esc_html(__('Add Field', 'majestic-support')) ."</a>";
                $MJTC_videoButton = "<a target=\"blank\" href=\"https://www.youtube.com/watch?v=8dIMdKuTLx4\" class=\"msadmin-video-link mjtc-cp-video-popup\" title=\"". esc_html(__('How to setup user fields', 'majestic-support')) ."\">
                    <svg viewBox=\"0 0 24 24\" width=\"22\" height=\"22\" fill=\"#6b7280\">
                      <path d=\"M21.8 8.2s-.2-1.5-.8-2.2c-.8-.9-1.7-.9-2.1-1C15.9 5 12 5 12 5h0s-3.9 0-6.9.1c-.4.1-1.3.1-2.1 1C2.4 6.7 2.2 8.2 2.2 8.2S2 9.9 2 11.6v.8c0 1.7.2 3.4.2 3.4s.2 1.5.8 2.2c.8.9 1.9.8 2.4.9 1.7.1 6.6.1 6.6.1s3.9 0 6.9-.1c.4-.1 1.3-.1 2.1-1 .6-.7.8-2.2.8-2.2s.2-1.7.2-3.4v-.8c0-1.7-.2-3.4-.2-3.4zM10 14.6V9.4l5 2.6-5 2.6z\"></path>
                    </svg>
                </a>";
                if(in_array('multiform', majesticsupport::$_active_addons)){
                    $MJTC_breadCrumbs = "<li><a href=\"?page=majesticsupport_multiform\" title=\"".  esc_html(__('Multiforms','majestic-support')) ."\">". esc_html(__('Multiforms','majestic-support')) ."</a></li>";
                }
                $MJTC_customTitle = esc_html(__('Fields', 'majestic-support'));
                if(isset(majesticsupport::$_data['multiFormTitle'])){
                    $MJTC_customTitle .= "<span class=\"msadmin-head-sub-text\">
                        ". ' ('.esc_html(majesticsupport::MJTC_getVarValue(majesticsupport::$_data["multiFormTitle"])).')' ."
                    </span>";
                }
                $title = __("Fields", 'majestic-support');
                $description = __("Manage and customize user fields to collect and organize user information.", 'majestic-support');
                break;
            case 'adduserfield':
                if(in_array('multiform', majesticsupport::$_active_addons)){
                    $MJTC_breadCrumbs = "<li><a href=\"?page=majesticsupport_multiform\" title=\"".  esc_html(__('Multiforms','majestic-support')) ."\">". esc_html(__('Multiforms','majestic-support')) ."</a></li>";
                }
                $MJTC_customTitle = isset(majesticsupport::$_data[0]['fieldvalues']) ? esc_html(__('Edit Field', 'majestic-support')) : esc_html(__('Add Field', 'majestic-support'));
                if(isset(majesticsupport::$_data['multiFormTitle'])){
                    $MJTC_customTitle .= "<span class=\"msadmin-head-sub-text\">
                        ". ' ('.esc_html(majesticsupport::MJTC_getVarValue(majesticsupport::$_data["multiFormTitle"])).')' ."
                    </span>";
                }
                $title = __("Add Field", 'majestic-support');
                $description = __("Add a new custom field to capture specific information.", 'majestic-support');
                break;
            case 'admin_slug':
                $MJTC_actionButton = "<a class=\"mjtc-admin-btn mjtc-admin-btn-primary\" title=\"". esc_html(__('Reset','majestic-support')) ."\" href=\"". esc_url(admin_url("admin.php?page=majesticsupport_slug&task=resetallslugs&action=mstask")) ."\">
                    ". esc_html(__('Reset All','majestic-support')) ."
                </a>";
                $title = __("Slugs", 'majestic-support');
                $description = __("Customize the admin URL slugs for plugin pages.", 'majestic-support');
                break;
            case 'admin_tickets':
                $MJTC_id='';
                if(in_array('multiform', majesticsupport::$_active_addons) && majesticsupport::$_config['show_multiform_popup'] == 1){
                    $MJTC_id="id=multiformpopup";
                }
                $MJTC_actionButton = "<a ".esc_attr($MJTC_id)." title=\"". esc_html(__('Add', 'majestic-support'))."\" class=\"mjtc-admin-btn mjtc-admin-btn-primary\" href=\"?page=majesticsupport_ticket&mjslay=addticket&formid=". esc_attr(MJTC_includer::MJTC_getModel('ticket')->getDefaultMultiFormId()) ."\"><svg viewBox=\"0 0 24 24\" width=\"20\" height=\"20\" fill=\"white\"><path d=\"M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z\"></path></svg>". esc_html(__('Create Ticket', 'majestic-support'))."</a>";
                $title = __("Tickets", 'majestic-support');
                $description = __("View and manage all support tickets from a centralized dashboard.", 'majestic-support');
                break;
            case 'ticketclosereason':
                $MJTC_actionButton = "<a  title=\"". esc_html(__('Add', 'majestic-support'))."\" class=\"mjtc-admin-btn mjtc-admin-btn-primary\" href=\"?page=majesticsupport_ticketclosereason&mjslay=addticketclosereason\"><svg viewBox=\"0 0 24 24\" width=\"20\" height=\"20\" fill=\"white\"><path d=\"M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z\"></path></svg>". esc_html(__('Add Reason', 'majestic-support'))."</a>";
                $title = __("Ticket Close Reasons", 'majestic-support');
                $description = __("Standardize how your support requests are resolved and tracked.", 'majestic-support');
                break;
            case 'addticketclosereason':
                $title = __("Add Ticket Close Reason", 'majestic-support');
                $description = __("Improve your support analytics by adding a specific new closing category.", 'majestic-support');
                break;
            case 'admin_export':
                $title = __("Export", 'majestic-support');
                $description = __("Pull your support records and reports for backups or team meetings.", 'majestic-support');
                break;
            case 'smartreply':
                $MJTC_actionButton = "<a title=\"". esc_html(__('Add','majestic-support')) ."\" class=\"mjtc-admin-btn mjtc-admin-btn-primary\" href=\"?page=majesticsupport_smartreply&mjslay=addsmartreply\"><svg viewBox=\"0 0 24 24\" width=\"20\" height=\"20\" fill=\"white\"><path d=\"M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z\"></path></svg>". esc_html(__('Add Smart Reply', 'majestic-support')) ."</a>";
                $MJTC_videoButton = "<a target=\"blank\" href=\"https://www.youtube.com/watch?v=YDYnagRWyEU\" class=\"msadmin-video-link mjtc-cp-video-popup\" title=\"". esc_html(__('How to set smartreply', 'majestic-support')) ."\">
                    <svg viewBox=\"0 0 24 24\" width=\"22\" height=\"22\" fill=\"#6b7280\">
                      <path d=\"M21.8 8.2s-.2-1.5-.8-2.2c-.8-.9-1.7-.9-2.1-1C15.9 5 12 5 12 5h0s-3.9 0-6.9.1c-.4.1-1.3.1-2.1 1C2.4 6.7 2.2 8.2 2.2 8.2S2 9.9 2 11.6v.8c0 1.7.2 3.4.2 3.4s.2 1.5.8 2.2c.8.9 1.9.8 2.4.9 1.7.1 6.6.1 6.6.1s3.9 0 6.9-.1c.4-.1 1.3-.1 2.1-1 .6-.7.8-2.2.8-2.2s.2-1.7.2-3.4v-.8c0-1.7-.2-3.4-.2-3.4zM10 14.6V9.4l5 2.6-5 2.6z\"></path>
                    </svg>
                </a>";
                $title = __("Smart Replies", 'majestic-support');
                $description = __("Speed up resolution times by building a library of pre-written answers.", 'majestic-support');
                break;
            case 'addsmartreply':
                $title = __("Add Smart Reply", 'majestic-support');
                $description = __("Help your agents reply faster to repetitive questions.", 'majestic-support');
                break;
            case 'admin_multiform':
                $MJTC_actionButton = "<a title=\"". esc_html(__('Add','majestic-support')) ."\" class=\"mjtc-admin-btn mjtc-admin-btn-primary\" href=\"?page=majesticsupport_multiform&mjslay=addmultiform\"><svg viewBox=\"0 0 24 24\" width=\"20\" height=\"20\" fill=\"white\"><path d=\"M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z\"></path></svg>". esc_html(__('Add Multiform', 'majestic-support')) ."</a>";
                $MJTC_videoButton = "<a target=\"blank\" href=\"https://www.youtube.com/watch?v=Z5-dKDt8DJ8\" class=\"msadmin-video-link mjtc-cp-video-popup\" title=\"". esc_html(__('How to set multiforms', 'majestic-support')) ."\">
                    <svg viewBox=\"0 0 24 24\" width=\"22\" height=\"22\" fill=\"#6b7280\">
                      <path d=\"M21.8 8.2s-.2-1.5-.8-2.2c-.8-.9-1.7-.9-2.1-1C15.9 5 12 5 12 5h0s-3.9 0-6.9.1c-.4.1-1.3.1-2.1 1C2.4 6.7 2.2 8.2 2.2 8.2S2 9.9 2 11.6v.8c0 1.7.2 3.4.2 3.4s.2 1.5.8 2.2c.8.9 1.9.8 2.4.9 1.7.1 6.6.1 6.6.1s3.9 0 6.9-.1c.4-.1 1.3-.1 2.1-1 .6-.7.8-2.2.8-2.2s.2-1.7.2-3.4v-.8c0-1.7-.2-3.4-.2-3.4zM10 14.6V9.4l5 2.6-5 2.6z\"></path>
                    </svg>
                </a>";
                $title = __("Multiforms", 'majestic-support');
                $description = __("Manage custom support forms for different types of customer inquiries.", 'majestic-support');
                break;
            case 'admin_addmultiform':
                $title = __("Add Multiform", 'majestic-support');
                $description = __("Set up a new multiform with custom fields for a specific support request.", 'majestic-support');
                break;
            case 'admin_staffs':
                $MJTC_actionButton = "<a title=\"". esc_html(__('Add','majestic-support')) ."\" class=\"mjtc-admin-btn mjtc-admin-btn-primary\" href=\"?page=majesticsupport_agent&mjslay=addstaff\"><svg viewBox=\"0 0 24 24\" width=\"20\" height=\"20\" fill=\"white\"><path d=\"M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z\"></path></svg>". esc_html(__('Add Agent', 'majestic-support')) ."</a>";
                $title = __("Agents", 'majestic-support');
                $description = __("Manage your support team directory and monitor agent activity.", 'majestic-support');
                break;
            case 'admin_addstaffs':
                $title = __("Add Agent", 'majestic-support');
                $description = __("Set up a new agent account and assign their role and department.", 'majestic-support');
                break;
            case 'agentautoassign':
                $MJTC_actionButton = "<a title=\"". esc_html(__('Add','majestic-support')) ."\" class=\"mjtc-admin-btn mjtc-admin-btn-primary\" href=\"?page=majesticsupport_agentautoassign&mjslay=addagentautoassign\"><svg viewBox=\"0 0 24 24\" width=\"20\" height=\"20\" fill=\"white\"><path d=\"M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z\"></path></svg>". esc_html(__('Add Conditions', 'majestic-support')) ."</a>";
                $title = __("Conditions", 'majestic-support');
                $description = __("Eliminate manual triage by automatically routing new tickets to available agents.", 'majestic-support');
                break;
            case 'addagentautoassign':
                $MJTC_breadCrumbs = "<li><a href=\"?page=majesticsupport_agentautoassign\" title=\"". esc_html(__('Conditions','majestic-support')) ."\">". esc_html(__('Conditions','majestic-support')) ."</a></li>";
                $title = __("Add Rule", 'majestic-support');
                $description = __("Set up a new automated rule for distributing incoming tickets.", 'majestic-support');
                break;
            case 'rolepermission':
                $MJTC_customTitle = esc_html(majesticsupport::$_data[0]['role']->name) . " " . esc_html(__('Role Permission', 'majestic-support'));
                $title = __("Role Permission", 'majestic-support');
                $description = __("Manage granular access levels and security privileges for your support staff.", 'majestic-support');
                break;
            case 'admin_staffpermissions':
                if (isset(majesticsupport::$_data[0])) {
                    $MJTC_customTitle = esc_html(majesticsupport::$_data[0]['agent']->firstname) . " " .
                        esc_html(majesticsupport::$_data[0]['agent']->lastname) . " " . esc_html(__('Permissions', 'majestic-support'));
                }
                $title = __("Add Permissions", 'majestic-support');
                $description = __("Select the modules and features this new permission set can access.", 'majestic-support');
                break;
            case 'configurations':
                $title = __("Settings", 'majestic-support');
                $description = __("The control center for your support desk. Manage all your core configurations here.", 'majestic-support');
                break;
            case 'cronjob':
                $title = __("Cron Job URLs", 'majestic-support');
                $description = __("The exact trigger links required to keep your email fetching, notifications, and cleanups running on time.", 'majestic-support');
                break;
            case 'shortcodes':
                $MJTC_videoButton = "<a target=\"blank\" href=\"https://www.youtube.com/watch?v=PV-shw5Nr8Q\" class=\"msadmin-video-link mjtc-cp-video-popup\" title=\"". esc_html(__('How to add Shortcode', 'majestic-support')) ."\">
                    <svg viewBox=\"0 0 24 24\" width=\"22\" height=\"22\" fill=\"#6b7280\">
                      <path d=\"M21.8 8.2s-.2-1.5-.8-2.2c-.8-.9-1.7-.9-2.1-1C15.9 5 12 5 12 5h0s-3.9 0-6.9.1c-.4.1-1.3.1-2.1 1C2.4 6.7 2.2 8.2 2.2 8.2S2 9.9 2 11.6v.8c0 1.7.2 3.4.2 3.4s.2 1.5.8 2.2c.8.9 1.9.8 2.4.9 1.7.1 6.6.1 6.6.1s3.9 0 6.9-.1c.4-.1 1.3-.1 2.1-1 .6-.7.8-2.2.8-2.2s.2-1.7.2-3.4v-.8c0-1.7-.2-3.4-.2-3.4zM10 14.6V9.4l5 2.6-5 2.6z\"></path>
                    </svg>
                </a>";
                $title = __("Shortcodes", 'majestic-support');
                $description = __("A complete list of codes to integrate Majestic Support features directly into your site's pages.", 'majestic-support');
                break;
            case 'themes':
                $MJTC_videoButton = "<a target=\"blank\" href=\"https://www.youtube.com/watch?v=OZTabfsnVIQ\" class=\"msadmin-video-link mjtc-cp-video-popup\" title=\"". esc_html(__('How to set colors', 'majestic-support')) ."\">
                    <svg viewBox=\"0 0 24 24\" width=\"22\" height=\"22\" fill=\"#6b7280\">
                      <path d=\"M21.8 8.2s-.2-1.5-.8-2.2c-.8-.9-1.7-.9-2.1-1C15.9 5 12 5 12 5h0s-3.9 0-6.9.1c-.4.1-1.3.1-2.1 1C2.4 6.7 2.2 8.2 2.2 8.2S2 9.9 2 11.6v.8c0 1.7.2 3.4.2 3.4s.2 1.5.8 2.2c.8.9 1.9.8 2.4.9 1.7.1 6.6.1 6.6.1s3.9 0 6.9-.1c.4-.1 1.3-.1 2.1-1 .6-.7.8-2.2.8-2.2s.2-1.7.2-3.4v-.8c0-1.7-.2-3.4-.2-3.4zM10 14.6V9.4l5 2.6-5 2.6z\"></path>
                    </svg>
                </a>";
                $title = __("Colors", 'majestic-support');
                $description = __("Customize the look and feel of your support portal to match your brand.", 'majestic-support');
                break;
            case 'reports':
                $title = __("Reports", 'majestic-support');
                $description = __("Deep dive into your support metrics, ticket volume, and team performance", 'majestic-support');
                break;
            case 'overal_statistics':
                if(in_array('export', majesticsupport::$_active_addons)){
                    $MJTC_actionButton = "<a title=\"". esc_html(__('Export Data', 'majestic-support')) ."\" id=\"jsexport-link\" class=\"mjtc-admin-btn mjtc-admin-btn-primary\" href=\"?page=majesticsupport_export&task=getoverallexport&action=mstask\"><svg viewBox=\"0 0 24 24\" width=\"22\" height=\"22\" fill=\"#ffffff\"><path d=\"M5 20a2 2 0 0 1-2-2v-3h4v3h10v-3h4v3a2 2 0 0 1-2 2H5zM13 4v8h3l-4 4-4-4h3V4h2z\"></path></svg>". esc_html(__('Export Data', 'majestic-support')) ."</a>";
                }
                $title = __("Overall Statistics", 'majestic-support');
                $description = __("Get a complete, bird's-eye view of your entire support operation.", 'majestic-support');
                break;
            case 'agent_reports':
                if(in_array('export', majesticsupport::$_active_addons)){
                    $MJTC_t_name = 'getstaffmemberexport';
                    $MJTC_link_export = admin_url('admin.php?page=majesticsupport_export&task='.esc_attr($MJTC_t_name).'&action=mstask&uid='.esc_attr(majesticsupport::$_data['filter']['uid']).'&date_start='.esc_attr(majesticsupport::$_data['filter']['date_start']).'&date_end='.esc_attr(majesticsupport::$_data['filter']['date_end']));
                    $MJTC_actionButton = "<a title=\"". esc_html(__('Export Data', 'majestic-support')) ."\" id=\"jsexport-link\" class=\"mjtc-admin-btn mjtc-admin-btn-primary\" href=\"".esc_url($MJTC_link_export)."\"><svg viewBox=\"0 0 24 24\" width=\"22\" height=\"22\" fill=\"#ffffff\"><path d=\"M5 20a2 2 0 0 1-2-2v-3h4v3h10v-3h4v3a2 2 0 0 1-2 2H5zM13 4v8h3l-4 4-4-4h3V4h2z\"></path></svg>". esc_html(__('Export Data', 'majestic-support')) ."</a>";
                }
                $title = __("Agent Reports", 'majestic-support');
                $description = __("Track individual agent performance, resolution times, and daily productivity.", 'majestic-support');
                break;
            case 'agentdetail_reports':
                if(in_array('export', majesticsupport::$_active_addons)){
                    $MJTC_t_name = 'getstaffmemberexportbystaffid';
                    $MJTC_link_export = admin_url('admin.php?page=majesticsupport_export&task='.esc_attr($MJTC_t_name).'&action=mstask&uid='.esc_attr(majesticsupport::$_data['filter']['uid']).'&date_start='.esc_attr(majesticsupport::$_data['filter']['date_start']).'&date_end='.esc_attr(majesticsupport::$_data['filter']['date_end']));
                    $MJTC_actionButton = "<a title=\"". esc_html(__('Export Data', 'majestic-support')) ."\" id=\"jsexport-link\" class=\"mjtc-admin-btn mjtc-admin-btn-primary\" href=\"".esc_url($MJTC_link_export)."\"><svg viewBox=\"0 0 24 24\" width=\"22\" height=\"22\" fill=\"#ffffff\"><path d=\"M5 20a2 2 0 0 1-2-2v-3h4v3h10v-3h4v3a2 2 0 0 1-2 2H5zM13 4v8h3l-4 4-4-4h3V4h2z\"></path></svg>". esc_html(__('Export Data', 'majestic-support')) ."</a>";
                }
                $title = __("Agent Detail Report", 'majestic-support');
                $description = __("View a complete timeline of an agent's workload, resolutions, and active tickets.", 'majestic-support');
                break;
            case 'department_reports':
                if(in_array('export', majesticsupport::$_active_addons)){
                    $MJTC_t_name = 'getdepartmentexport';
                    $MJTC_link_export = admin_url('admin.php?page=majesticsupport_export&task='.esc_attr($MJTC_t_name).'&action=mstask&date_start='.esc_attr(majesticsupport::$_data['filter']['date_start']).'&date_end='.esc_attr(majesticsupport::$_data['filter']['date_end']));
                    $MJTC_actionButton = "<a title=\"". esc_html(__('Export Data', 'majestic-support')) ."\" id=\"jsexport-link\" class=\"mjtc-admin-btn mjtc-admin-btn-primary\" href=\"".esc_url($MJTC_link_export)."\"><svg viewBox=\"0 0 24 24\" width=\"22\" height=\"22\" fill=\"#ffffff\"><path d=\"M5 20a2 2 0 0 1-2-2v-3h4v3h10v-3h4v3a2 2 0 0 1-2 2H5zM13 4v8h3l-4 4-4-4h3V4h2z\"></path></svg>". esc_html(__('Export Data', 'majestic-support')) ."</a>";
                }
                $title = __("Department Reports", 'majestic-support');
                $description = __("Identify which departments are experiencing high traffic and where more resources are needed.", 'majestic-support');
                break;
            case 'departmentdetail_reports':
                if(in_array('export', majesticsupport::$_active_addons)){
                    $MJTC_t_name = 'getdepartmentmemberexportbydepartmentid';
                    $MJTC_link_export = admin_url('admin.php?page=majesticsupport_export&task='.esc_attr($MJTC_t_name).'&action=mstask&id='.esc_attr(majesticsupport::$_data['filter']['id']).'&date_start='.esc_attr(majesticsupport::$_data['filter']['date_start']).'&date_end='.esc_attr(majesticsupport::$_data['filter']['date_end']));
                    $MJTC_actionButton = "<a title=\"". esc_html(__('Export Data', 'majestic-support')) ."\" id=\"jsexport-link\" class=\"mjtc-admin-btn mjtc-admin-btn-primary\" href=\"".esc_url($MJTC_link_export)."\"><svg viewBox=\"0 0 24 24\" width=\"22\" height=\"22\" fill=\"#ffffff\"><path d=\"M5 20a2 2 0 0 1-2-2v-3h4v3h10v-3h4v3a2 2 0 0 1-2 2H5zM13 4v8h3l-4 4-4-4h3V4h2z\"></path></svg>". esc_html(__('Export Data', 'majestic-support')) ."</a>";
                }
                $title = __("Department Detail Report", 'majestic-support');
                $description = __("Analyze the full lifecycle of tickets and agent efficiency within this chosen department.", 'majestic-support');
                break;
            case 'user_reports':
                if(in_array('export', majesticsupport::$_active_addons)){
                    $MJTC_t_name = 'getusersexport';
                    $MJTC_link_export = admin_url('admin.php?page=majesticsupport_export&task='.esc_attr($MJTC_t_name).'&action=mstask&uid='.esc_attr(majesticsupport::$_data['filter']['uid']).'&date_start='.esc_attr(majesticsupport::$_data['filter']['date_start']).'&date_end='.esc_attr(majesticsupport::$_data['filter']['date_end']));
                    $MJTC_actionButton = "<a title=\"". esc_html(__('Export Data', 'majestic-support')) ."\" id=\"jsexport-link\" class=\"mjtc-admin-btn mjtc-admin-btn-primary\" href=\"".esc_url($MJTC_link_export)."\"><svg viewBox=\"0 0 24 24\" width=\"22\" height=\"22\" fill=\"#ffffff\"><path d=\"M5 20a2 2 0 0 1-2-2v-3h4v3h10v-3h4v3a2 2 0 0 1-2 2H5zM13 4v8h3l-4 4-4-4h3V4h2z\"></path></svg>". esc_html(__('Export Data', 'majestic-support')) ."</a>";
                }
                $title = __("User Reports", 'majestic-support');
                $description = __("Analyze ticket volume and support engagement across your entire user base.", 'majestic-support');
                break;
            case 'userdetail_reports':
                if(in_array('export', majesticsupport::$_active_addons)){
                    $MJTC_t_name = 'getuserexportbyuid';
                    $MJTC_link_export = admin_url('admin.php?page=majesticsupport_export&task='.esc_attr($MJTC_t_name).'&action=mstask&uid='.esc_attr(majesticsupport::$_data['filter']['uid']).'&date_start='.esc_attr(majesticsupport::$_data['filter']['date_start']).'&date_end='.esc_attr(majesticsupport::$_data['filter']['date_end']));
                    $MJTC_actionButton = "<a title=\"". esc_html(__('Export Data', 'majestic-support')) ."\" id=\"jsexport-link\" class=\"mjtc-admin-btn mjtc-admin-btn-primary\" href=\"".esc_url($MJTC_link_export)."\"><svg viewBox=\"0 0 24 24\" width=\"22\" height=\"22\" fill=\"#ffffff\"><path d=\"M5 20a2 2 0 0 1-2-2v-3h4v3h10v-3h4v3a2 2 0 0 1-2 2H5zM13 4v8h3l-4 4-4-4h3V4h2z\"></path></svg>". esc_html(__('Export Data', 'majestic-support')) ."</a>";
                }
                $title = __("User Detail Report", 'majestic-support');
                $description = __("View the complete support history, ticket timeline, and interaction data for a specific user.", 'majestic-support');
                break;
            case 'satisfaction_reports':
                $title = __("Satisfaction Reports", 'majestic-support');
                $description = __("Monitor customer happiness and review the ratings left for your support team.", 'majestic-support');
                break;
            case 'addemialpiping':
                $title = __("Add Email Piping", 'majestic-support');
                $description = __("Set up an email gateway to sync your support inbox directly with the dashboard.", 'majestic-support');
                break;
            case 'emialpiping':
                $MJTC_actionButton = "<a title=\"". esc_html(__('Add','majestic-support')) ."\" class=\"mjtc-admin-btn mjtc-admin-btn-primary\" href=\"?page=majesticsupport_emailpiping&mjslay=addticketviaemail\"><svg viewBox=\"0 0 24 24\" width=\"20\" height=\"20\" fill=\"white\"><path d=\"M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z\"></path></svg>". esc_html(__('Add Email Piping', 'majestic-support')) ."</a>";
                $title = __("Email Piping", 'majestic-support');
                $description = __("View and monitor all active email connections and their synchronization status.", 'majestic-support');
                break;
            case 'addgdpr':
                $title = __("Add GDPR Field", 'majestic-support');
                $description = __("Create a new compliance field for your forms to ensure data protection standards.", 'majestic-support');
                break;
            case 'gdpr':
                $MJTC_actionButton = "<a title=\"". esc_html(__('Add','majestic-support')) ."\" class=\"mjtc-admin-btn mjtc-admin-btn-primary\" href=\"?page=majesticsupport_gdpr&mjslay=addgdprfield\"><svg viewBox=\"0 0 24 24\" width=\"20\" height=\"20\" fill=\"white\"><path d=\"M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z\"></path></svg>". esc_html(__('Add GDPR Field', 'majestic-support')) ."</a>";
                $title = __("GDPR Fields", 'majestic-support');
                $description = __("Manage the data protection and privacy consent fields used throughout your support system.", 'majestic-support');
                break;
            case 'erasedatarequests':
                $title = __("Erase Data Requests", 'majestic-support');
                $description = __("Review and process user requests to have their personal data permanently removed.", 'majestic-support');
                break;
            case 'addonslist':
                $title = __("Add-ons List", 'majestic-support');
                $description = __("Explore and manage available extensions to power up your support dashboard.", 'majestic-support');
                break;
            case 'missingaddon':
                $title = __("Premium Addons", 'majestic-support');
                $description = __("Unlock advanced features and professional tools with our premium support extensions.", 'majestic-support');
                break;
            case 'step1':
                $title = __("Install Add-ons", 'majestic-support');
                $description = __("Follow the steps below to successfully install and activate your new features.", 'majestic-support');
                break;
            case 'feedbacks':
                $title = __("Feedback", 'majestic-support');
                $description = __("View the ratings and comments left by users to monitor your service quality.", 'majestic-support');
                break;
            case 'adddepertment':
                $title = __("Add Department", 'majestic-support');
                $description = __("Create a new team or category to help organize and route incoming tickets.", 'majestic-support');
                break;
            case 'depertments':
                $MJTC_actionButton = "<a title=\"". esc_html(__('Add','majestic-support')) ."\" class=\"mjtc-admin-btn mjtc-admin-btn-primary\" href=\"?page=majesticsupport_department&mjslay=adddepartment\"><svg viewBox=\"0 0 24 24\" width=\"20\" height=\"20\" fill=\"white\"><path d=\"M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z\"></path></svg>". esc_html(__('Add Department', 'majestic-support')) ."</a>";
                $title = __("Departments", 'majestic-support');
                $description = __("Manage your support divisions and assign agents to specific team queues.", 'majestic-support');
                break;
            case 'addpriority':
                $title = __("Add Priority", 'majestic-support');
                $description = __("Define a new urgency level to help your team prioritize critical issues.", 'majestic-support');
                break;
            case 'priorities':
                $MJTC_actionButton = "<a title=\"". esc_html(__('Add','majestic-support')) ."\" class=\"mjtc-admin-btn mjtc-admin-btn-primary\" href=\"?page=majesticsupport_priority&mjslay=addpriority\"><svg viewBox=\"0 0 24 24\" width=\"20\" height=\"20\" fill=\"white\"><path d=\"M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z\"></path></svg>". esc_html(__('Add Priority', 'majestic-support')) ."</a>";
                $title = __("Priorities", 'majestic-support');
                $description = __("Customize ticket urgency levels and visual indicators for your support queue.", 'majestic-support');
                break;
            case 'addstatus':
                $title = __("Add Status", 'majestic-support');
                $description = __("Create a new custom status to better track the lifecycle of your tickets.", 'majestic-support');
                break;
            case 'statuses':
                $MJTC_actionButton = "<a title=\"". esc_html(__('Add','majestic-support')) ."\" class=\"mjtc-admin-btn mjtc-admin-btn-primary\" href=\"?page=majesticsupport_status&mjslay=addstatus\"><svg viewBox=\"0 0 24 24\" width=\"20\" height=\"20\" fill=\"white\"><path d=\"M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z\"></path></svg>". esc_html(__('Add Status', 'majestic-support')) ."</a>";
                $title = __("Statuses", 'majestic-support');
                $description = __("Manage the different stages a ticket moves through from open to resolved.", 'majestic-support');
                break;
            case 'products':
                $MJTC_actionButton = "<a title=\"". esc_html(__('Add','majestic-support')) ."\" class=\"mjtc-admin-btn mjtc-admin-btn-primary\" href=\"?page=majesticsupport_product&mjslay=addproduct\"><svg viewBox=\"0 0 24 24\" width=\"20\" height=\"20\" fill=\"white\"><path d=\"M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z\"></path></svg>". esc_html(__('Add Product', 'majestic-support')) ."</a>";
                $title = __("Products", 'majestic-support');
                $description = __("View and manage the products or services your team provides support for.", 'majestic-support');
                break;
            case 'addproduct':
                $title = __("Add Product", 'majestic-support');
                $description = __("Register a new product to allow customers to select it when opening tickets.", 'majestic-support');
                break;
            case 'importdata':
                $title = __("Import Data", 'majestic-support');
                $description = __("Upload and migrate your existing ticket or user data into the system.", 'majestic-support');
                break;
            case 'importresult':
                $title = __("Import Data Report", 'majestic-support');
                $description = __("Review the success rate and details of your most recent data migration.", 'majestic-support');
                break;
            case 'addcategory':
                $title = __("Add Category", 'majestic-support');
                $description = __("Create a new grouping for your Knowledge Base articles or FAQs.", 'majestic-support');
                break;
            case 'categories':
                $MJTC_actionButton = "<a title=\"". esc_html(__('Add','majestic-support')) ."\" class=\"mjtc-admin-btn mjtc-admin-btn-primary\" href=\"?page=majesticsupport_knowledgebase&mjslay=addcategory\"><svg viewBox=\"0 0 24 24\" width=\"20\" height=\"20\" fill=\"white\"><path d=\"M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z\"></path></svg>". esc_html(__('Add Category', 'majestic-support')) ."</a>";
                $title = __("Categories", 'majestic-support');
                $description = __("Organize your support content into logical groups for easier browsing.", 'majestic-support');
                break;
            case 'addknowledgebase':
                $title = __("Add Knowledge Base", 'majestic-support');
                $description = __("Write and publish a new article to help users find answers themselves.", 'majestic-support');
                break;
            case 'knowledgebase':
                $MJTC_actionButton = "<a title=\"". esc_html(__('Add','majestic-support')) ."\" class=\"mjtc-admin-btn mjtc-admin-btn-primary\" href=\"?page=majesticsupport_knowledgebase&mjslay=addarticle\"><svg viewBox=\"0 0 24 24\" width=\"20\" height=\"20\" fill=\"white\"><path d=\"M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z\"></path></svg>". esc_html(__('Add Knowledge Base', 'majestic-support')) ."</a>";
                $MJTC_videoButton = "<a target=\"blank\" href=\"https://www.youtube.com/watch?v=g6l5M8hR1hE\" class=\"msadmin-video-link mjtc-cp-video-popup\" title=\"". esc_html(__('How to use Knowledge Base', 'majestic-support')) ."\">
                    <svg viewBox=\"0 0 24 24\" width=\"22\" height=\"22\" fill=\"#6b7280\">
                      <path d=\"M21.8 8.2s-.2-1.5-.8-2.2c-.8-.9-1.7-.9-2.1-1C15.9 5 12 5 12 5h0s-3.9 0-6.9.1c-.4.1-1.3.1-2.1 1C2.4 6.7 2.2 8.2 2.2 8.2S2 9.9 2 11.6v.8c0 1.7.2 3.4.2 3.4s.2 1.5.8 2.2c.8.9 1.9.8 2.4.9 1.7.1 6.6.1 6.6.1s3.9 0 6.9-.1c.4-.1 1.3-.1 2.1-1 .6-.7.8-2.2.8-2.2s.2-1.7.2-3.4v-.8c0-1.7-.2-3.4-.2-3.4zM10 14.6V9.4l5 2.6-5 2.6z\"></path>
                    </svg>
                </a>";
                $title = __("Knowledge Base", 'majestic-support');
                $description = __("Manage your self-service articles and help guides in one central location.", 'majestic-support');
                break;
            case 'adddownload':
                $title = __("Add Download", 'majestic-support');
                $description = __("Upload a new file, driver, or document for your customers to download.", 'majestic-support');
                break;
            case 'downloads':
                $MJTC_actionButton = "<a title=\"". esc_html(__('Add','majestic-support')) ."\" class=\"mjtc-admin-btn mjtc-admin-btn-primary\" href=\"?page=majesticsupport_download&mjslay=adddownload\"><svg viewBox=\"0 0 24 24\" width=\"20\" height=\"20\" fill=\"white\"><path d=\"M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z\"></path></svg>". esc_html(__('Add Download', 'majestic-support')) ."</a>";
                $MJTC_videoButton = "<a target=\"blank\" href=\"https://www.youtube.com/watch?v=jrsWVyNJm54\" class=\"msadmin-video-link mjtc-cp-video-popup\" title=\"". esc_html(__('How to use downloads', 'majestic-support')) ."\">
                    <svg viewBox=\"0 0 24 24\" width=\"22\" height=\"22\" fill=\"#6b7280\">
                      <path d=\"M21.8 8.2s-.2-1.5-.8-2.2c-.8-.9-1.7-.9-2.1-1C15.9 5 12 5 12 5h0s-3.9 0-6.9.1c-.4.1-1.3.1-2.1 1C2.4 6.7 2.2 8.2 2.2 8.2S2 9.9 2 11.6v.8c0 1.7.2 3.4.2 3.4s.2 1.5.8 2.2c.8.9 1.9.8 2.4.9 1.7.1 6.6.1 6.6.1s3.9 0 6.9-.1c.4-.1 1.3-.1 2.1-1 .6-.7.8-2.2.8-2.2s.2-1.7.2-3.4v-.8c0-1.7-.2-3.4-.2-3.4zM10 14.6V9.4l5 2.6-5 2.6z\"></path>
                    </svg>
                </a>";
                $title = __("Downloads", 'majestic-support');
                $description = __("Manage the files and resources available for your users to access and download.", 'majestic-support');
                break;
            case 'addfaq':
                $title = __("Add FAQ", 'majestic-support');
                $description = __("Create a new frequently asked question and answer pair for your site.", 'majestic-support');
                break;
            case 'faqs':
                $MJTC_actionButton = "<a title=\"". esc_html(__('Add','majestic-support')) ."\" class=\"mjtc-admin-btn mjtc-admin-btn-primary\" href=\"?page=majesticsupport_faq&mjslay=addfaq\"><svg viewBox=\"0 0 24 24\" width=\"20\" height=\"20\" fill=\"white\"><path d=\"M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z\"></path></svg>". esc_html(__('Add FAQ', 'majestic-support')) ."</a>";
                $MJTC_videoButton = "<a target=\"blank\" href=\"https://www.youtube.com/watch?v=gOgxbQjdFJg\" class=\"msadmin-video-link mjtc-cp-video-popup\" title=\"". esc_html(__('How to create FAQ', 'majestic-support')) ."\">
                    <svg viewBox=\"0 0 24 24\" width=\"22\" height=\"22\" fill=\"#6b7280\">
                      <path d=\"M21.8 8.2s-.2-1.5-.8-2.2c-.8-.9-1.7-.9-2.1-1C15.9 5 12 5 12 5h0s-3.9 0-6.9.1c-.4.1-1.3.1-2.1 1C2.4 6.7 2.2 8.2 2.2 8.2S2 9.9 2 11.6v.8c0 1.7.2 3.4.2 3.4s.2 1.5.8 2.2c.8.9 1.9.8 2.4.9 1.7.1 6.6.1 6.6.1s3.9 0 6.9-.1c.4-.1 1.3-.1 2.1-1 .6-.7.8-2.2.8-2.2s.2-1.7.2-3.4v-.8c0-1.7-.2-3.4-.2-3.4zM10 14.6V9.4l5 2.6-5 2.6z\"></path>
                    </svg>
                </a>";
                $title = __("FAQs", 'majestic-support');
                $description = __("Organize and manage quick answers to the most common customer questions.", 'majestic-support');
                break;
            case 'addinstantfix':
                $title = __("Add AI Data Source", 'majestic-support');
                $description = __("Add a new webpage or YouTube video for the AI to crawl, index, and use for instant ticket fix.", 'majestic-support');
                break;
            case 'instantfixs':
                $MJTC_actionButton = "<a title=\"". esc_html(__('Add','majestic-support')) ."\" class=\"mjtc-admin-btn mjtc-admin-btn-primary\" href=\"?page=majesticsupport_instantfix&mjslay=addinstantfix\"><svg viewBox=\"0 0 24 24\" width=\"20\" height=\"20\" fill=\"white\"><path d=\"M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z\"></path></svg>". esc_html(__('Add Data Source', 'majestic-support')) ."</a>";
                $title = __("AI Data Sources", 'majestic-support');
                $description = __("Manage synced URLs and extracted support data.", 'majestic-support');
                break;
            case 'addannouncement':
                $title = __("Add Announcement", 'majestic-support');
                $description = __("Post a new news update or system alert for your users to see.", 'majestic-support');
                break;
            case 'announcements':
                $MJTC_actionButton = "<a title=\"". esc_html(__('Add','majestic-support')) ."\" class=\"mjtc-admin-btn mjtc-admin-btn-primary\" href=\"?page=majesticsupport_announcement&mjslay=addannouncement\"><svg viewBox=\"0 0 24 24\" width=\"20\" height=\"20\" fill=\"white\"><path d=\"M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z\"></path></svg>". esc_html(__('Add Announcement', 'majestic-support')) ."</a>";
                $MJTC_videoButton = "<a target=\"blank\" href=\"https://www.youtube.com/watch?v=UJv3-FdD0Fs\" class=\"msadmin-video-link mjtc-cp-video-popup\" title=\"". esc_html(__('How to add announcement', 'majestic-support')) ."\">
                    <svg viewBox=\"0 0 24 24\" width=\"22\" height=\"22\" fill=\"#6b7280\">
                      <path d=\"M21.8 8.2s-.2-1.5-.8-2.2c-.8-.9-1.7-.9-2.1-1C15.9 5 12 5 12 5h0s-3.9 0-6.9.1c-.4.1-1.3.1-2.1 1C2.4 6.7 2.2 8.2 2.2 8.2S2 9.9 2 11.6v.8c0 1.7.2 3.4.2 3.4s.2 1.5.8 2.2c.8.9 1.9.8 2.4.9 1.7.1 6.6.1 6.6.1s3.9 0 6.9-.1c.4-.1 1.3-.1 2.1-1 .6-.7.8-2.2.8-2.2s.2-1.7.2-3.4v-.8c0-1.7-.2-3.4-.2-3.4zM10 14.6V9.4l5 2.6-5 2.6z\"></path>
                    </svg>
                </a>";
                $title = __("Announcements", 'majestic-support');
                $description = __("Manage the global notifications and news updates shown to your customers.", 'majestic-support');
                break;
            case 'helptopics':
                $MJTC_actionButton = "<a title=\"". esc_html(__('Add','majestic-support')) ."\" class=\"mjtc-admin-btn mjtc-admin-btn-primary\" href=\"?page=majesticsupport_helptopic&mjslay=addhelptopic\"><svg viewBox=\"0 0 24 24\" width=\"20\" height=\"20\" fill=\"white\"><path d=\"M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z\"></path></svg>". esc_html(__('Add Help Topics', 'majestic-support')) ."</a>";
                $MJTC_videoButton = "<a target=\"blank\" href=\"https://www.youtube.com/watch?v=8mS5EWOUl7c\" class=\"msadmin-video-link mjtc-cp-video-popup\" title=\"". esc_html(__('How to use help topic', 'majestic-support')) ."\">
                    <svg viewBox=\"0 0 24 24\" width=\"22\" height=\"22\" fill=\"#6b7280\">
                      <path d=\"M21.8 8.2s-.2-1.5-.8-2.2c-.8-.9-1.7-.9-2.1-1C15.9 5 12 5 12 5h0s-3.9 0-6.9.1c-.4.1-1.3.1-2.1 1C2.4 6.7 2.2 8.2 2.2 8.2S2 9.9 2 11.6v.8c0 1.7.2 3.4.2 3.4s.2 1.5.8 2.2c.8.9 1.9.8 2.4.9 1.7.1 6.6.1 6.6.1s3.9 0 6.9-.1c.4-.1 1.3-.1 2.1-1 .6-.7.8-2.2.8-2.2s.2-1.7.2-3.4v-.8c0-1.7-.2-3.4-.2-3.4zM10 14.6V9.4l5 2.6-5 2.6z\"></path>
                    </svg>
                </a>";
                $title = __("Help Topics", 'majestic-support');
                $description = __("Manage the subject lines users can select when opening a new ticket.", 'majestic-support');
                break;
            case 'addhelptopic':
                $title = __("Add Help Topic", 'majestic-support');
                $description = __("Create a new predefined topic to help categorize incoming user requests.", 'majestic-support');
                break;
            case 'systememails':
                $MJTC_actionButton = "<a title=\"". esc_html(__('Add','majestic-support')) ."\" class=\"mjtc-admin-btn mjtc-admin-btn-primary\" href=\"?page=majesticsupport_email&mjslay=addemail\"><svg viewBox=\"0 0 24 24\" width=\"20\" height=\"20\" fill=\"white\"><path d=\"M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z\"></path></svg>". esc_html(__('Add Email', 'majestic-support')) ."</a>";
                $MJTC_videoButton = "<a target=\"blank\" href=\"https://www.youtube.com/watch?v=JbR9MhSRH_s&t=1s\" class=\"msadmin-video-link mjtc-cp-video-popup\" title=\"". esc_html(__('How to set SMTP', 'majestic-support')) ."\">
                    <svg viewBox=\"0 0 24 24\" width=\"22\" height=\"22\" fill=\"#6b7280\">
                      <path d=\"M21.8 8.2s-.2-1.5-.8-2.2c-.8-.9-1.7-.9-2.1-1C15.9 5 12 5 12 5h0s-3.9 0-6.9.1c-.4.1-1.3.1-2.1 1C2.4 6.7 2.2 8.2 2.2 8.2S2 9.9 2 11.6v.8c0 1.7.2 3.4.2 3.4s.2 1.5.8 2.2c.8.9 1.9.8 2.4.9 1.7.1 6.6.1 6.6.1s3.9 0 6.9-.1c.4-.1 1.3-.1 2.1-1 .6-.7.8-2.2.8-2.2s.2-1.7.2-3.4v-.8c0-1.7-.2-3.4-.2-3.4zM10 14.6V9.4l5 2.6-5 2.6z\"></path>
                    </svg>
                </a>";
                $title = __("System Emails", 'majestic-support');
                $description = __("Configure the mail accounts used by the system to send notifications and replies.", 'majestic-support');
                break;
            case 'addsystememail':
                $title = __("Add Email", 'majestic-support');
                $description = __("Connect a new email account to the system for outgoing or incoming mail.", 'majestic-support');
                break;
            case 'permademessages':
                $MJTC_actionButton = "<a title=\"". esc_html(__('Add','majestic-support')) ."\" class=\"mjtc-admin-btn mjtc-admin-btn-primary\" href=\"?page=majesticsupport_cannedresponses&mjslay=addpremademessage\"><svg viewBox=\"0 0 24 24\" width=\"20\" height=\"20\" fill=\"white\"><path d=\"M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z\"></path></svg>". esc_html(__('Add Premade Response', 'majestic-support')) ."</a>";
                $title = __("Premade Responses", 'majestic-support');
                $description = __("Manage your library of 'canned responses' to answer common questions instantly.", 'majestic-support');
                break;
            case 'addpermademessage':
                $title = __("Add Premade Response", 'majestic-support');
                $description = __("Draft a new reusable message template to speed up agent reply times.", 'majestic-support');
                break;
            case 'roles':
                $MJTC_actionButton = "<a title=\"". esc_html(__('Add','majestic-support')) ."\" class=\"mjtc-admin-btn mjtc-admin-btn-primary\" href=\"?page=majesticsupport_role&mjslay=addrole\"><svg viewBox=\"0 0 24 24\" width=\"20\" height=\"20\" fill=\"white\"><path d=\"M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z\"></path></svg>". esc_html(__('Add Role', 'majestic-support')) ."</a>";
                $title = __("Roles", 'majestic-support');
                $description = __("Define agent roles and manage their access levels within the dashboard.", 'majestic-support');
                break;
            case 'addrole':
                $title = __("Add Role", 'majestic-support');
                $description = __("Create a new staff role with specific permissions and system access.", 'majestic-support');
                break;
            case 'inbox':
                $title = __("Mail", 'majestic-support');
                $description = __("View your incoming mail queue and manage communication from your customers.", 'majestic-support');
                break;
            case 'outbox':
                $title = __("Mail", 'majestic-support');
                $description = __("Review sent messages and pending outgoing communication from your system.", 'majestic-support');
                break;
            case 'form_message':
                $title = __("Compose", 'majestic-support');
                $description = __("Draft and send a new direct message or announcement to your users.", 'majestic-support');
                break;
            case 'message':
                $MJTC_customTitle = esc_html(majesticsupport::$_data[0]['message']->subject);
                $title = __("Message", 'majestic-support');
                $description = __("Review the details of this specific communication and its thread history.", 'majestic-support');
                break;
            case 'baneemiallogs':
                $title = __("Banned Email Log List", 'majestic-support');
                $description = __("View a history of blocked attempts from blacklisted email addresses.", 'majestic-support');
                break;
            case 'banned_emails':
                $MJTC_actionButton = "<a title=\"". esc_html(__('Add','majestic-support')) ."\" class=\"mjtc-admin-btn mjtc-admin-btn-primary\" href=\"?page=majesticsupport_banemail&mjslay=addbanemail\"><svg viewBox=\"0 0 24 24\" width=\"20\" height=\"20\" fill=\"white\"><path d=\"M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z\"></path></svg>". esc_html(__('Add Banned Email', 'majestic-support')) ."</a>";
                $title = __("Banned Emails", 'majestic-support');
                $description = __("Block specific email addresses or domains from creating tickets in your system.", 'majestic-support');
                break;
            case 'addbanned_email':
                $title = __("Add Banned Email", 'majestic-support');
                $description = __("Blacklist a specific address to prevent spam or unwanted ticket submissions.", 'majestic-support');
                break;
            case 'emailcc':
                $MJTC_actionButton = "<a title=\"". esc_html(__('Add','majestic-support')) ."\" class=\"mjtc-admin-btn mjtc-admin-btn-primary\" href=\"?page=majesticsupport_emailcc&mjslay=addemailcc\"><svg viewBox=\"0 0 24 24\" width=\"20\" height=\"20\" fill=\"white\"><path d=\"M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z\"></path></svg>". esc_html(__('Add Email CC', 'majestic-support')) ."</a>";
                $MJTC_videoButton = "<a target=\"blank\" href=\"https://www.youtube.com/watch?v=bu3h7LiGry0\" class=\"msadmin-video-link mjtc-cp-video-popup\" title=\"". esc_html(__('How to use email cc', 'majestic-support')) ."\">
                    <svg viewBox=\"0 0 24 24\" width=\"22\" height=\"22\" fill=\"#6b7280\">
                      <path d=\"M21.8 8.2s-.2-1.5-.8-2.2c-.8-.9-1.7-.9-2.1-1C15.9 5 12 5 12 5h0s-3.9 0-6.9.1c-.4.1-1.3.1-2.1 1C2.4 6.7 2.2 8.2 2.2 8.2S2 9.9 2 11.6v.8c0 1.7.2 3.4.2 3.4s.2 1.5.8 2.2c.8.9 1.9.8 2.4.9 1.7.1 6.6.1 6.6.1s3.9 0 6.9-.1c.4-.1 1.3-.1 2.1-1 .6-.7.8-2.2.8-2.2s.2-1.7.2-3.4v-.8c0-1.7-.2-3.4-.2-3.4zM10 14.6V9.4l5 2.6-5 2.6z\"></path>
                    </svg>
                </a>";
                $title = __("Email CC", 'majestic-support');
                $description = __("Manage the email addresses that should automatically receive a carbon copy of tickets.", 'majestic-support');
                break;
            case 'addemailcc':
                $title = __("Add Email CC", 'majestic-support');
                $description = __("Add a new recipient to be automatically included in ticket communications.", 'majestic-support');
                break;
            case 'emailtemplates':
                $title = __("Email Templates", 'majestic-support');
                $description = __("Customize the layout and design of the automated emails sent by your system.", 'majestic-support');
                break;
            case 'admin_help':
                $title = __("Help", 'majestic-support');
                $description = __("Find documentation and resources to help you master your support platform.", 'majestic-support');
                break;
            case 'admin_addons_status':
                $title = __("Addons Status", 'majestic-support');
                $description = __("Review the current health and activation status of your installed extensions.", 'majestic-support');
                break;
        }
        $MJTC_html = "<div class=\"msadmin-head-wrapper\">";
            if (isset($MJTC_customTitle)) {
                $mainTitle = $MJTC_customTitle;
            } else {
                $mainTitle = $title;
            }
            $MJTC_html .= "<div id=\"msadmin-head\">
                <div class=\"msadmin-head-top-toogle-wrp\">
                    <button class=\"mjtc-admin-btn mjtc-admin-btn-light mjtc-admin-btn-icon\" id=\"desktop-menu-toggle\" style=\"margin-right: 1rem; flex-shrink: 0;\" title=\"Toggle Sidebar\">
                        <svg viewBox=\"0 0 24 24\" width=\"22\" height=\"22\" fill=\"currentColor\"><path d=\"M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z\"></path></svg>
                    </button>
                    <div class=\"msadmin-head-text-wrapper\">
                        <h1 class=\"msadmin-head-text\">". wp_kses($mainTitle, MJTC_ALLOWED_TAGS) ."
                            <div class=\"mjtc-admin-breadcrumb\">
                                <svg viewBox=\"0 0 24 24\" fill=\"currentColor\"><path d=\"M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z\"></path></svg>
                                <span><a href=\"".esc_url(admin_url('admin.php?page=majesticsupport'))."\" title=\"". esc_html(__('Home','majestic-support')) ."\">". esc_html(__('Home','majestic-support')) ."</a></span>
                                <span class=\"mjtc-bread-sep\">/</span>
                                <span>". esc_html($title)."</span>
                            </div>
                        </h1>
                        <p class=\"msadmin-head-btm-text\">". esc_html($description)."</p>
                    </div>
                </div>
                <div class=\"mjtc-topbar-actions\">";
                    if ($MJTC_layouts == 'dashboard') {
                        $MJTC_html .= "
                        <button class=\"mjtc-admin-btn mjtc-admin-btn-light\" id=\"btn-customize\">
                            <svg viewBox=\"0 0 24 24\" width=\"20\" height=\"20\" fill=\"currentColor\"><path d=\"M3 17v2h6v-2H3zM3 5v2h10V5H3zm10 16v-2h8v-2h-8v-2h-2v6h2zM7 9v2H3v2h4v2h2V9H7zm14 4v-2H11v2h10zm-6-4h2V7h4V5h-4V3h-2v6z\"></path></svg>
                            ". esc_html(__('Design','majestic-support')) ."
                        </button>";
                    }
                    $MJTC_html .= wp_kses($MJTC_videoButton, MJTC_ALLOWED_TAGS);
                    $MJTC_html .= "
                    <a href=\"".esc_url('?page=majesticsupport&mjslay=help')."\" title=\"". esc_html(__('Help','majestic-support')) ."\" class=\"mjtc-admin-btn mjtc-admin-btn-light mjtc-admin-btn-icon 01 \">
                        <svg viewBox=\"0 0 24 24\" width=\"22\" height=\"22\" fill=\"#6b7280\"><path d=\"M12 1c-4.97 0-9 4.03-9 9v7c0 1.66 1.34 3 3 3h3v-8H5v-2c0-3.87 3.13-7 7-7s7 3.13 7 7v2h-4v8h4v1h-7v2h6c1.66 0 3-1.34 3-3V10c0-4.97-4.03-9-9-9z\"></path></svg>
                    </a>
                    ".wp_kses($MJTC_actionButton, MJTC_ALLOWED_TAGS)."
                </div>
            </div>
        </div>";
        echo wp_kses($MJTC_html, MJTC_ALLOWED_TAGS);
    }

    function getPageBreadcrumps($MJTC_layouts){
        if (majesticsupport::$_config['show_breadcrumbs'] != 1)
            return false;
        $title = "";
        switch ($MJTC_layouts) {
            case 'addrole':
                $MJTC_breadcrumps = "<span>
                    <a href=\"". esc_url(majesticsupport::makeUrl(array('mjsmod'=>'role', 'mjslay'=>'roles')))."\">
                        ". __("Roles",'majestic-support') ."
                    </a>
                </span>";
                $title = "Add Role";
                break;
            case 'roles':
                $title = "Roles";
                break;
            case 'rolepermissions':
                $MJTC_breadcrumps = "<span>
                    <a href=\"". esc_url(majesticsupport::makeUrl(array('mjsmod'=>'role', 'mjslay'=>'roles')))."\">
                        ". __("Roles",'majestic-support') ."
                    </a>
                </span>";
                $title = "Role Permissions";
                break;
            case 'addagent':
                $MJTC_breadcrumps = "<span>
                    <a href=\"". esc_url(majesticsupport::makeUrl(array('mjsmod'=>'agent', 'mjslay'=>'staffs')))."\">
                        ". __("Agents",'majestic-support') ."
                    </a>
                </span>";
                $title = "Add Agent";
                break;
            case 'agents':
                $title = "Agents";
                break;
            case 'mytickets':
                $title = "My Tickets";
                break;
            case 'agentpermissions':
                $MJTC_breadcrumps = "<span>
                    <a href=\"". esc_url(majesticsupport::makeUrl(array('mjsmod'=>'agent', 'mjslay'=>'staffs')))."\">
                        ". __("Agents",'majestic-support') ."
                    </a>
                </span>";
                $title = "Agent Permissions";
                break;
            case 'addticket':
                $MJTC_breadcrumps = "<span>
                    <a href=\"". esc_url(majesticsupport::makeUrl(array('mjsmod'=>'agent', 'mjslay'=>'staffmyticket')))."\">
                        ". __("My Tickets",'majestic-support') ."
                    </a>
                </span>";
                $title = "Submit Ticket";
                break;
            case 'ticketdetail':
                $MJTC_breadcrumps = "<span>";
                    if ( in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) {
                        $MJTC_breadcrumps .= "<a href=\"". esc_url(majesticsupport::makeUrl(array('mjsmod'=>'agent', 'mjslay'=>'staffmyticket')))."\">
                            ". __("My Tickets",'majestic-support')."
                        </a>";
                    } else {
                        $MJTC_breadcrumps .= "<a href=\"". esc_url(majesticsupport::makeUrl(array('mjsmod'=>'ticket', 'mjslay'=>'myticket')))."\">
                            ". __("My Tickets",'majestic-support')."
                        </a>";
                    }
                    $MJTC_breadcrumps .= "
                </span>";
                $title = "Ticket Detail";
                break;
            case 'addticketuser':
                $title = "Submit Ticket";
                break;
            case 'addknowledgebase':
                $MJTC_breadcrumps = "<span>
                    <a href=\"". esc_url(majesticsupport::makeUrl(array('mjsmod'=>'knowledgebase', 'mjslay'=>'stafflistarticles')))."\">
                        ". __("Knowledge Base",'majestic-support') ."
                    </a>
                </span>";
                $title = "Add Knowledge Base";
                break;
            case 'knowledgebasearticles':
                $MJTC_breadcrumps = "<span>
                    <a href=\"". esc_url(majesticsupport::makeUrl(array('mjsmod'=>'knowledgebase', 'mjslay'=>'userknowledgebase')))."\">
                        ". __("Knowledge Base",'majestic-support') ."
                    </a>
                </span>";
                $title = "Knowledge Base Articles";
                break;
            case 'knowledgebasedetail':
                $MJTC_breadcrumps = "<span>";
                    if ( in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) {
                        $MJTC_breadcrumps .= "<a href=\"". esc_url(majesticsupport::makeUrl(array('mjsmod'=>'knowledgebase', 'mjslay'=>'stafflistarticles')))."\">
                            ". __("Knowledge Base",'majestic-support')."
                        </a>";
                    } else {
                        $MJTC_breadcrumps .= "<a href=\"". esc_url(majesticsupport::makeUrl(array('mjsmod'=>'knowledgebase', 'mjslay'=>'userknowledgebase')))."\">
                            ". __("Knowledge Base",'majestic-support')."
                        </a>";
                    }
                    $MJTC_breadcrumps .= "
                </span>";
                $title = "Knowledge Base Detail";
                break;
            case 'knowledgebase':
                $title = "Knowledge Base";
                break;
            case 'categories':
                $title = "Categories";
                break;
            case 'addcategory':
                $MJTC_breadcrumps = "<span>
                    <a href=\"". esc_url(majesticsupport::makeUrl(array('mjsmod'=>'knowledgebase', 'mjslay'=>'stafflistcategories')))."\">
                        ". __("Categories",'majestic-support') ."
                    </a>
                </span>";
                $title = "Add Category";
                break;
            case 'addannouncement':
                $MJTC_breadcrumps = "<span>
                    <a href=\"". esc_url(majesticsupport::makeUrl(array('mjsmod'=>'announcement', 'mjslay'=>'staffannouncements')))."\">
                        ". __("Announcements",'majestic-support') ."
                    </a>
                </span>";
                $title = "Add Announcement";
                break;
            case 'announcements':
                $title = "Announcements";
                break;
            case 'announcementdetail':
                $MJTC_breadcrumps = "<span>";
                    if ( in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) {
                        $MJTC_breadcrumps .= "<a href=\"". esc_url(majesticsupport::makeUrl(array('mjsmod'=>'announcement', 'mjslay'=>'staffannouncements')))."\">
                            ". __("Announcements",'majestic-support')."
                        </a>";
                    } else {
                        $MJTC_breadcrumps .= "<a href=\"". esc_url(majesticsupport::makeUrl(array('mjsmod'=>'announcement', 'mjslay'=>'announcements')))."\">
                            ". __("Announcements",'majestic-support')."
                        </a>";
                    }
                    $MJTC_breadcrumps .= "
                </span>";
                $title = "Announcement Detail";
                break;
            case 'faqdetail':
                $MJTC_breadcrumps = "<span>";
                    if ( in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) {
                        $MJTC_breadcrumps .= "<a href=\"". esc_url(majesticsupport::makeUrl(array('mjsmod'=>'faq', 'mjslay'=>'stafffaqs')))."\">
                            ". __("FAQs",'majestic-support')."
                        </a>";
                    } else {
                        $MJTC_breadcrumps .= "<a href=\"". esc_url(majesticsupport::makeUrl(array('mjsmod'=>'faq', 'mjslay'=>'faqs')))."\">
                            ". __("FAQs",'majestic-support')."
                        </a>";
                    }
                    $MJTC_breadcrumps .= "
                </span>";
                $title = "FAQ Detail";
                break;
            case 'faqs':
                $title = "FAQs";
                break;
            case 'addfaqs':
                $MJTC_breadcrumps = "<span>
                    <a href=\"". esc_url(majesticsupport::makeUrl(array('mjsmod'=>'faq', 'mjslay'=>'stafffaqs')))."\">
                        ". __("FAQs",'majestic-support') ."
                    </a>
                </span>";
                $title = "Add FAQs";
                break;
            case 'addhelptopic':
                $MJTC_breadcrumps = "<span>
                    <a href=\"". esc_url(majesticsupport::makeUrl(array('mjsmod'=>'helptopic', 'mjslay'=>'agenthelptopics')))."\">
                        ". __("Help Topics",'majestic-support') ."
                    </a>
                </span>";
                $title = "Add Help Topic";
                break;
            case 'helptopics':
                $title = "Help Topics";
                break;
            case 'addannouncement':
                $MJTC_breadcrumps = "<span>
                    <a href=\"". esc_url(majesticsupport::makeUrl(array('mjsmod'=>'announcement', 'mjslay'=>'staffannouncements')))."\">
                        ". __("Announcements",'majestic-support') ."
                    </a>
                </span>";
                $title = "Add Department";
                break;
            case 'announcements':
                $title = "Announcements";
                break;
            case 'adddepartment':
                $MJTC_breadcrumps = "<span>
                    <a href=\"". esc_url(majesticsupport::makeUrl(array('mjsmod'=>'department', 'mjslay'=>'departments')))."\">
                        ". __("Departments",'majestic-support') ."
                    </a>
                </span>";
                $title = "Add Department";
                break;
            case 'departments':
                $title = "Departments";
                break;
            case 'userdata':
                $title = "User Data";
                break;
            case 'login':
                $title = "Login";
                break;
            case 'register':
                $title = "Register";
                break;
            case 'departmentreports':
                $title = "Department Reports";
                break;
            case 'agentdetailreport':
                $MJTC_breadcrumps = "<span>
                    <a href=\"". esc_url(majesticsupport::makeUrl(array('mjsmod'=>'reports', 'mjslay'=>'staffreports')))."\">
                        ". __("Agent Reports",'majestic-support') ."
                    </a>
                </span>";
                $title = "Agent Detail Report";
                break;
            case 'agentreports':
                $title = "Agent Reports";
                break;
            case 'addsmartreply':
                $MJTC_breadcrumps = "<span>
                    <a href=\"". esc_url(majesticsupport::makeUrl(array('mjsmod'=>'smartreply', 'mjslay'=>'smartreplies')))."\">
                        ". __("Smart Replies",'majestic-support') ."
                    </a>
                </span>";
                $title = "Add Smart Reply";
                break;
            case 'smartreplies':
                $title = "Smart Replies";
                break;
            case 'addemail':
                $MJTC_breadcrumps = "<span>
                    <a href=\"". esc_url(majesticsupport::makeUrl(array('mjsmod'=>'banemail', 'mjslay'=>'banemails')))."\">
                        ". __("Banned Emails",'majestic-support') ."
                    </a>
                </span>";
                $title = "Add Email";
                break;
            case 'bannedemails':
                $title = "Banned Emails";
                break;
            case 'addpremaderesponse':
                $MJTC_breadcrumps = "<span>
                    <a href=\"". esc_url(majesticsupport::makeUrl(array('mjsmod'=>'cannedresponses', 'mjslay'=>'agentcannedresponses')))."\">
                        ". __("Premade Responses",'majestic-support') ."
                    </a>
                </span>";
                $title = "Add Premade Response";
                break;
            case 'premaderesponse':
                $title = "Premade Response";
                break;
            case 'adddownload':
                $MJTC_breadcrumps = "<span>
                    <a href=\"". esc_url(majesticsupport::makeUrl(array('mjsmod'=>'download', 'mjslay'=>'staffdownloads')))."\">
                        ". __("Downloads",'majestic-support') ."
                    </a>
                </span>";
                $title = "Add Download";
                break;
            case 'downloads':
                $title = "Downloads";
                break;
            case 'addreasons':
                $MJTC_breadcrumps = "<span>
                    <a href=\"". esc_url(majesticsupport::makeUrl(array('mjsmod'=>'ticketclosereason', 'mjslay'=>'ticketclosereasons')))."\">
                        ". __("Reasons",'majestic-support') ."
                    </a>
                </span>";
                $title = "Add Reasons";
                break;
            case 'formfeedback':
                $title = "Form Feedback";
                break;
            case 'feedbacks':
                $title = "Feedbacks";
                break;
            case 'export':
                $title = "Export";
                break;
            case 'downloads':
                $title = "Downloads";
                break;
            case 'ticketstatus':
                $title = "Ticket Status";
                break;
            case 'mail':
                $title = "Mail";
                break;
            case 'message':
                $title = "Message";
                break;
            case 'ticketclosereasons':
                $title = "Ticket Close Reasons";
                break;
            case 'myprofile':
                $title = "My Profile";
                break;
            case 'admin_addons_status':
                $title = "Addons Status";
                break;
            
        }
        $MJTC_html = "
        <div class=\"mjtc-support-breadcrumps\">
            <a href=\"". esc_url(majesticsupport::makeUrl(array('mjsmod'=>'majesticsupport', 'mjslay'=>'controlpanel')))."\">
                <svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' data-lucide='home' class='lucide lucide-home w-3 h-3 text-slate-400'><path d='M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8'></path><path d='M3 10a2 2 0 0 1 .709-1.528l7-6a2 2 0 0 1 2.582 0l7 6A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z'></path></svg>
            </a>";
            if (isset($MJTC_breadcrumps)) {
                $MJTC_html .= $MJTC_breadcrumps;
            }
            $MJTC_html .= "<span>". esc_html(majesticsupport::MJTC_getVarValue($title)) ."</span>
        </div>";
        echo wp_kses($MJTC_html, MJTC_ALLOWED_TAGS);
    }

    function getSanitizedEditorData($MJTC_data){
        $MJTC_data = wp_filter_post_kses($MJTC_data);
        if ($MJTC_data != null){
            $MJTC_data = MJTC_majesticsupportphplib::MJTC_stripslashes(wpautop($MJTC_data));
        }
        return $MJTC_data;
    }

    function getEncriptedSiteLink(){
        $MJTC_siteLink = get_option('ms_encripted_site_link');
        if ($MJTC_siteLink == '') {
            include_once MJTC_PLUGIN_PATH . 'includes/encoder.php';
            $MJTC_encoder = new MJTC_encoder();
            $MJTC_siteLink = $MJTC_encoder->MJTC_encrypt(get_site_url());
            update_option('ms_encripted_site_link', $MJTC_siteLink);
        }
       return $MJTC_siteLink;
    }

    function msremovetags($MJTC_message){
        if(MJTC_majesticsupportphplib::MJTC_strpos($MJTC_message, '<script>') !== false || MJTC_majesticsupportphplib::MJTC_strpos($MJTC_message, '</script>') !== false){ // check and remove script tag from the message
            if($MJTC_message != ''){
                $MJTC_message = MJTC_majesticsupportphplib::MJTC_str_replace('<script>','&lt;script&gt;', $MJTC_message);
                $MJTC_message = MJTC_majesticsupportphplib::MJTC_str_replace('</script>','&lt;/script&gt;', $MJTC_message);
            }
        }
        return $MJTC_message;
    }
    
    function showUpdateAvaliableAlert(){
        require_once MJTC_PLUGIN_PATH.'includes/addon-updater/msupdater.php';
        $MJTC_SUPPORTTICKETUpdater  = new MJTC_SUPPORTTICKETUpdater();
        $MJTC_cdnversiondata = $MJTC_SUPPORTTICKETUpdater->MJTC_getPluginVersionDataFromCDN();
        $MJTC_not_installed = array();
        $majesticsupport_addons = MJTC_includer::MJTC_getModel('premiumplugin')->MJTC_getAddonsArray();
        $MJTC_installed_plugins = get_plugins();
        $MJTC_count = 0;
        foreach ($majesticsupport_addons as $MJTC_key1 => $MJTC_value1) {
            $MJTC_matched = 0;
            $MJTC_version = "";
            foreach ($MJTC_installed_plugins as $MJTC_name => $MJTC_value) {
                $MJTC_install_plugin_name = MJTC_majesticsupportphplib::MJTC_str_replace(".php","",MJTC_majesticsupportphplib::MJTC_basename($MJTC_name));
                if($MJTC_key1 == $MJTC_install_plugin_name){
                    $MJTC_matched = 1;
                    $MJTC_version = $MJTC_value["Version"];
                    $MJTC_install_plugin_matched_name = $MJTC_install_plugin_name;
                }
            }
            if($MJTC_matched == 1){ //installed
                $MJTC_name = $MJTC_key1;
                $title = $MJTC_value1['title'];
                $MJTC_img = MJTC_majesticsupportphplib::MJTC_str_replace("majestic-support-", "", $MJTC_key1).'.png';
                $MJTC_cdnavailableversion = "";
                foreach ($MJTC_cdnversiondata as $MJTC_cdnname => $MJTC_cdnversion) {
                    $MJTC_install_plugin_name_simple = MJTC_majesticsupportphplib::MJTC_str_replace("-", "", $MJTC_install_plugin_matched_name);
                    if($MJTC_cdnname == MJTC_majesticsupportphplib::MJTC_str_replace("-", "", $MJTC_install_plugin_matched_name)){
                        if($MJTC_cdnversion > $MJTC_version){ // new version available
                            $MJTC_count++;
                        }
                    }    
                }
            }
        }
        return $MJTC_count;
    }

    function msRemoveAddonUpdatesFolder($MJTC_dir)
    {
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

        // The delete method with the second parameter as 'true' 
        // handles recursive deletion of files and subfolders automatically.
        if ($MJTC_wp_filesystem->exists($MJTC_dir)) {
            return $MJTC_wp_filesystem->delete($MJTC_dir, true);
        }

        return false;
    }

    function generateIndexFile($MJTC_file_directory) {
        global $wp_filesystem;

        // Initialize the WP_Filesystem
        if (!is_a($wp_filesystem, 'WP_Filesystem_Base')) {
            require_once ABSPATH . 'wp-admin/includes/file.php'; // Include WP Filesystem functions
            $MJTC_creds = request_filesystem_credentials(site_url());

            if (!WP_Filesystem($MJTC_creds)) {
                wp_die('Could not initialize the filesystem.');
            }
        }

        // Get the uploads directory path
        $MJTC_uploads_dir = wp_upload_dir();
        $MJTC_uploads_path = $MJTC_uploads_dir['basedir'];

        // Normalize the paths to ensure consistency
        $MJTC_file_directory = MJTC_majesticsupportphplib::MJTC_rtrim($MJTC_file_directory, '/');
        $MJTC_uploads_path = MJTC_majesticsupportphplib::MJTC_rtrim($MJTC_uploads_path, '/');

        // Check if the given directory is within the uploads directory
        if (MJTC_majesticsupportphplib::MJTC_strpos($MJTC_file_directory, $MJTC_uploads_path) === 0) {
            // Start from the given directory and move up to the uploads directory
            $MJTC_current_dir = $MJTC_file_directory;
            while ($MJTC_current_dir !== $MJTC_uploads_path) {
                // Path to the index.php file in the current directory
                $MJTC_index_file = $MJTC_current_dir . '/index.html';

                // Create the index.php file if it does not exist
                if (!$wp_filesystem->exists($MJTC_index_file)) {
                    $wp_filesystem->put_contents($MJTC_index_file, '', FS_CHMOD_FILE); // FS_CHMOD_FILE ensures correct file permissions
                }

                // Move up to the parent directory
                $MJTC_current_dir = MJTC_majesticsupportphplib::MJTC_dirname($MJTC_current_dir);
            }

            // Finally, check and create the index.php file in the uploads directory
            $MJTC_uploads_index_file = $MJTC_uploads_path . '/index.html';
            if (!$wp_filesystem->exists($MJTC_uploads_index_file)) {
                // $wp_filesystem->put_contents($MJTC_uploads_index_file, '', FS_CHMOD_FILE);
            }
        }

        return;
    }

    function mjtc_check_license_status() {
        // Get all distinct transaction keys
        $MJTC_query = "
            SELECT DISTINCT option_value 
            FROM `" . majesticsupport::$_db->prefix . "options`
            WHERE option_name LIKE 'transaction_key_for_majestic-support%'
        ";
        $transaction_keys = majesticsupport::$_db->get_col($MJTC_query);

        if (empty($transaction_keys)) return;

        $MJTC_status_prefix = 'key_status_for_majestic-support_';
        $MJTC_site_url = MJTC_includer::MJTC_getModel('majesticsupport')->getSiteUrl();
        $MJTC_show_key_expiry_msg = 0;

        foreach ($transaction_keys as $MJTC_key) {
            // Build query string for GET request
            $MJTC_query_args = [
                'token'   => $MJTC_key,
                'domain'  => $MJTC_site_url,
                'request' => 'keyexpirycheck'
            ];

            $MJTC_url = add_query_arg($MJTC_query_args, 'https://majesticsupport.com/setup/index.php');

            // Perform GET request
            $MJTC_response = wp_remote_get($MJTC_url, [ 'timeout' => 15 ]);

            if (is_wp_error($MJTC_response)) {
                continue; // Skip on error
            }

            $MJTC_body = wp_remote_retrieve_body($MJTC_response);
            $MJTC_data = json_decode($MJTC_body, true);

            if (!is_array($MJTC_data) || !isset($MJTC_data['status'])) {
                continue; // Invalid response
            }

            // Save status
            update_option($MJTC_status_prefix . $MJTC_key, $MJTC_data['status'], false);

            // Save expiry date if available
            if ($MJTC_data['status'] == 1 && !empty($MJTC_data['expirydate'])) {
                if (MJTC_majesticsupportphplib::MJTC_strtotime(current_time('mysql')) > MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_data['expirydate'])) {
                    $MJTC_show_key_expiry_msg = 1;
                }
            } else {
                $MJTC_show_key_expiry_msg = 1;
            }
        }

        update_option('mjtc_show_key_expiry_msg', $MJTC_show_key_expiry_msg, false);
    }

    // Add actions for logged-in admins

    function mjtc_save_dashboard_layout() {
        // 1. Security Check
        // check_ajax_referer('mjtc_layout_nonce', '_wpnonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
        }

        // 2. Sanitize and Save
        // layout is an array of section_id => 1 or 0
        $MJTC_new_layout = array_map('intval', MJTC_request::MJTC_getVar('layout'));
        
        // Save to the options table
        $MJTC_updated = update_option('mjtc_dashboard_layout', $MJTC_new_layout);

        if ($MJTC_updated || get_option('mjtc_dashboard_layout') === $MJTC_new_layout) {
            return 1;
        } else {
            return 0;
        }
    }

}

?>
