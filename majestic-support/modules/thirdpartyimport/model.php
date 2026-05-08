<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_thirdpartyimportModel {

    // supportcandy import data

    private $MJTC_support_candy_users_array = array();
    
    private $MJTC_support_candy_ticket_custom_fields = array();
    private $MJTC_sc_ticket_custom_fields = array();
    private $MJTC_as_ticket_custom_fields = array();
    private $MJTC_fc_ticket_cf = array();


    private $MJTC_support_candy_user_ids = array();
    private $MJTC_support_candy_agent_ids = array();
    private $MJTC_support_candy_department_ids = array();
    private $MJTC_support_candy_agent_role_ids = array();
    private $MJTC_support_candy_ticket_ids = array();
    private $MJTC_support_candy_status_ids = array();
    private $MJTC_support_candy_priority_ids = array();
    private $MJTC_support_candy_premade_ids = array();


    private $MJTC_awesome_support_user_ids = array();
    private $MJTC_awesome_support_agent_ids = array();
    private $MJTC_awesome_support_department_ids = array();
    private $MJTC_awesome_support_ticket_ids = array();
    private $MJTC_awesome_support_status_ids = array();
    private $MJTC_awesome_support_priority_ids = array();
    private $MJTC_awesome_support_premade_ids = array();


    private $MJTC_fluent_support_user_ids = array();
    private $MJTC_fluent_support_agent_ids = array();
    private $MJTC_fluent_support_ticket_ids = array();
    private $MJTC_fluent_support_priority_ids = array();
    private $MJTC_fluent_support_premade_ids = array();



    private $_params_flag;
    private $_params_string;



    // values for counts
    private $MJTC_support_candy_import_count = [];
    private $MJTC_awesome_support_import_count = [];
    private $MJTC_fluent_support_import_count = [];

    function __construct() {
        $this->_params_flag = 0;
        $this->support_candy_import_count = [
            'user' => [
                'imported' => 0,
                'skipped'  => 0,
                'failed'   => 0,
            ],
            'agent_role' => [
                'imported' => 0,
                'skipped'  => 0,
                'failed'   => 0,
            ],
            'agent' => [
                'imported' => 0,
                'skipped'  => 0,
                'failed'   => 0,
            ],
            'department' => [
                'imported' => 0,
                'skipped'  => 0,
                'failed'   => 0,
            ],
            'priority' => [
                'imported' => 0,
                'skipped'  => 0,
                'failed'   => 0,
            ],
            'canned response' => [
                'imported' => 0,
                'skipped'  => 0,
                'failed'   => 0,
            ],
            'status' => [
                'imported' => 0,
                'skipped'  => 0,
                'failed'   => 0,
            ],
            'field' => [
                'imported' => 0,
                'skipped'  => 0,
                'failed'   => 0,
            ],
            'ticket' => [
                'imported' => 0,
                'skipped'  => 0,
                'failed'   => 0,
            ]
        ];
        $this->awesome_support_import_count = [
            'user' => [
                'imported' => 0,
                'skipped'  => 0,
                'failed'   => 0,
            ],
            'agent' => [
                'imported' => 0,
                'skipped'  => 0,
                'failed'   => 0,
            ],
            'department' => [
                'imported' => 0,
                'skipped'  => 0,
                'failed'   => 0,
            ],
            'priority' => [
                'imported' => 0,
                'skipped'  => 0,
                'failed'   => 0,
            ],
            'canned response' => [
                'imported' => 0,
                'skipped'  => 0,
                'failed'   => 0,
            ],
            'status' => [
                'imported' => 0,
                'skipped'  => 0,
                'failed'   => 0,
            ],
            'product' => [
                'imported' => 0,
                'skipped'  => 0,
                'failed'   => 0,
            ],
            'faq' => [
                'imported' => 0,
                'skipped'  => 0,
                'failed'   => 0,
            ],
            'field' => [
                'imported' => 0,
                'skipped'  => 0,
                'failed'   => 0,
            ],
            'ticket' => [
                'imported' => 0,
                'skipped'  => 0,
                'failed'   => 0,
            ]
        ];
        $this->fluent_support_import_count = [
            'user' => [
                'imported' => 0,
                'skipped'  => 0,
                'failed'   => 0,
            ],
            'agent' => [
                'imported' => 0,
                'skipped'  => 0,
                'failed'   => 0,
            ],
            'priority' => [
                'imported' => 0,
                'skipped'  => 0,
                'failed'   => 0,
            ],
            'canned response' => [
                'imported' => 0,
                'skipped'  => 0,
                'failed'   => 0,
            ],
            'product' => [
                'imported' => 0,
                'skipped'  => 0,
                'failed'   => 0,
            ],
            'field' => [
                'imported' => 0,
                'skipped'  => 0,
                'failed'   => 0,
            ],
            'ticket' => [
                'imported' => 0,
                'skipped'  => 0,
                'failed'   => 0,
            ]
        ];
    }


    function importSupportCandyData() {
        // Only for development – remove before pushing to production
        // $this->deletesupportcandyimporteddata();

        // Reset previously imported IDs from options
        // update_option('mjtc_support_ticket_support_candy_data_statuses', '');
        // update_option('mjtc_support_ticket_support_candy_data_priorities', '');
        // update_option('mjtc_support_ticket_support_candy_data_users', '');
        // update_option('mjtc_support_ticket_support_candy_data_departments', '');
        // update_option('mjtc_support_ticket_support_candy_data_premades', '');
        // update_option('mjtc_support_ticket_support_candy_data_agents', '');
        // update_option('mjtc_support_ticket_support_candy_data_agent_roles', '');
        // update_option('mjtc_support_ticket_support_candy_data_tickets', '');
        
        // Prepare filesystem and create necessary directories
        require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
        require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';
        $MJTC_filesystem = new WP_Filesystem_Direct(true);
        $MJTC_upload_dir = wp_upload_dir();
        $MJTC_upload_path = $MJTC_upload_dir['basedir'];
        $MJTC_datadirectory = majesticsupport::$_config['data_directory'];
        $MJTC_path = $MJTC_upload_path . "/" . $MJTC_datadirectory;

        if (!$MJTC_filesystem->exists($MJTC_path)) {
            wp_mkdir_p($MJTC_path);
        }
        $MJTC_path .= '/attachmentdata';
        if (!$MJTC_filesystem->exists($MJTC_path)) {
            wp_mkdir_p($MJTC_path);
        }
        $MJTC_path .= '/ticket';
        if (!$MJTC_filesystem->exists($MJTC_path)) {
            wp_mkdir_p($MJTC_path);
        }

        // Optional: Import theme (disabled by default)
        // $this->importSupportCandyTheme();

        if (majesticsupport::$_db->get_var("SHOW TABLES LIKE '" . majesticsupport::$_db->prefix . "psmsc_customers'")) {
            $this->importSupportCandyUsers();
        }

        if (majesticsupport::$_db->get_var("SHOW TABLES LIKE '" . majesticsupport::$_db->prefix . "psmsc_agents'")) {
            $this->importSupportCandyAgentsRoles();
            $this->importSupportCandyAgents();
        }

        if (majesticsupport::$_db->get_var("SHOW TABLES LIKE '" . majesticsupport::$_db->prefix . "psmsc_categories'")) {
            $this->importSupportCandyDepartments();
        }

        if (majesticsupport::$_db->get_var("SHOW TABLES LIKE '" . majesticsupport::$_db->prefix . "psmsc_priorities'")) {
            $this->importSupportCandyPriorities();
        }

        if (majesticsupport::$_db->get_var("SHOW TABLES LIKE '" . majesticsupport::$_db->prefix . "psmsc_canned_reply'")) {
            $this->importSupportCandyPremades();
        }

        if (majesticsupport::$_db->get_var("SHOW TABLES LIKE '" . majesticsupport::$_db->prefix . "psmsc_statuses'")) {
            $this->importSupportCandyStatus();
        }

        if (majesticsupport::$_db->get_var("SHOW TABLES LIKE '" . majesticsupport::$_db->prefix . "psmsc_tickets'")) {
            $this->importSupportCandyTicketFields();
            $this->getSupportCandyTickets($this->sc_ticket_custom_fields);
        }

        update_option('mjtc_import_counts',$this->support_candy_import_count);
        return;
    }

    private function importSupportCandyTheme() {
        $MJTC_supportcandy_settings = get_option( 'wpsc-ap-general' );
        $MJTC_helpdesk_settings = get_option('mjtc_set_theme_colors');
        $MJTC_data = json_decode($MJTC_helpdesk_settings, true);
        $MJTC_data['color1'] = $MJTC_supportcandy_settings['primary-color'];
        $MJTC_data['color4'] = $MJTC_supportcandy_settings['main-text-color'];
        // store help desk settings
        $MJTC_data = majesticsupport::MJTC_sanitizeData($MJTC_data);
        update_option('mjtc_set_theme_colors', wp_json_encode($MJTC_data));
    }

    private function getSupportCandyTickets($MJTC_sc_ticket_custom_fields) {
        // Check if tickets already processed for import
        $MJTC_imported_tickets = array();
        $MJTC_imported_tickets_json = get_option('mjtc_support_ticket_support_candy_data_tickets');
        if (!empty($MJTC_imported_tickets_json)) {
            $MJTC_imported_tickets = json_decode($MJTC_imported_tickets_json, true);
        }

        $MJTC_query = "SELECT tickets.*, replies.body AS reply_message, replies.type, replies.id AS replyid
                  FROM `" . majesticsupport::$_db->prefix . "psmsc_tickets` AS tickets
                  JOIN `" . majesticsupport::$_db->prefix . "psmsc_threads` AS replies ON replies.ticket = tickets.id 
                  WHERE replies.type = 'report' AND tickets.is_active != 0
                  ORDER BY tickets.id ASC";
        
        $MJTC_tickets = majesticsupport::$_db->get_results($MJTC_query);

        $MJTC_general_options = get_option("wpsc-gs-general");
        $MJTC_after_customer_reply = $MJTC_general_options['ticket-status-after-customer-reply'];
        $MJTC_after_agent_reply = $MJTC_general_options['ticket-status-after-agent-reply'];
        $MJTC_close_ticket_status = $MJTC_general_options['close-ticket-status'];

        foreach ($MJTC_tickets as $MJTC_ticket) {
            // Skip if ticket already imported
            if (!empty($MJTC_imported_tickets) && in_array($MJTC_ticket->id, $MJTC_imported_tickets)) {
                $this->support_candy_import_count['ticket']['skipped'] += 1;
                continue;
            }

            $MJTC_attachmentdir = MJTC_includer::MJTC_getModel('ticket')->getRandomFolderName();
            // Map custom fields
            $MJTC_params = array();
            $MJTC_eddorderid = '';
            $MJTC_eddproductid = '';
            $MJTC_wcproductid = '';
            $MJTC_wcorderid = '';
            foreach ($MJTC_sc_ticket_custom_fields as $MJTC_sc_ticket_custom_field) {
                $MJTC_field_name = $MJTC_sc_ticket_custom_field["name"];
                $MJTC_vardata = "";

                if ($MJTC_ticket->$MJTC_field_name) {
                    if ($MJTC_sc_ticket_custom_field["type"] == "cf_edd_order") {
                        $MJTC_vardata = '';
                        $MJTC_eddorderid = $MJTC_ticket->$MJTC_field_name;
                    } elseif ($MJTC_sc_ticket_custom_field["type"] == "cf_edd_product") {
                        $MJTC_vardata = '';
                        $MJTC_eddproductid = $MJTC_ticket->$MJTC_field_name;
                    } elseif ($MJTC_sc_ticket_custom_field["type"] == "cf_woo_order") {
                        $MJTC_vardata = '';
                        $MJTC_wcorderid = $MJTC_ticket->$MJTC_field_name;
                    } elseif ($MJTC_sc_ticket_custom_field["type"] == "cf_woo_product") {
                        $MJTC_vardata = '';
                        $MJTC_wcproductid = $MJTC_ticket->$MJTC_field_name;
                    } elseif ($MJTC_sc_ticket_custom_field["type"] == "date") {
                        $MJTC_vardata = gmdate("Y-m-d", MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->$MJTC_field_name));
                    } elseif ($MJTC_sc_ticket_custom_field["type"] == "file") {
                        $MJTC_vardata = $MJTC_ticket->$MJTC_field_name;
                        $MJTC_vardata = $this->getSupportCandyCustomFieldAttachments($MJTC_ticket->id, $MJTC_vardata, $MJTC_attachmentdir);
                    } elseif (in_array(strtolower($MJTC_sc_ticket_custom_field["type"]), ['multiple', 'checkbox', 'combo', 'radio'])) {
                        $MJTC_field_ids = explode('|', $MJTC_ticket->$MJTC_field_name);

                        // Sanitize and cast to integers
                        $MJTC_field_ids = array_map('intval', array_filter($MJTC_field_ids));

                        // Check if we have valid IDs
                        if (!empty($MJTC_field_ids)) {
                            $MJTC_placeholders = implode(',', $MJTC_field_ids);
                            $MJTC_query = "SELECT name FROM `" . majesticsupport::$_db->prefix . "psmsc_options` WHERE id IN ($MJTC_placeholders)";
                            $MJTC_names = majesticsupport::$_db->get_col($MJTC_query);

                            // Combine names into comma-separated string
                            $MJTC_vardata = !empty($MJTC_names) ? implode(', ', $MJTC_names) : '';
                        } else {
                            $MJTC_vardata = '';
                        }
                    } else {
                        $MJTC_vardata = $MJTC_ticket->$MJTC_field_name;
                    }

                    if ($MJTC_vardata != '') {
                        if (is_array($MJTC_vardata)) {
                            $MJTC_vardata = implode(', ', array_filter($MJTC_vardata));
                        }
                        $MJTC_params[$MJTC_sc_ticket_custom_field["ms_filedorderingfield"]] = MJTC_majesticsupportphplib::MJTC_htmlentities($MJTC_vardata);
                    }
                }
            }
            $MJTC_ticketparams = html_entity_decode(wp_json_encode($MJTC_params, JSON_UNESCAPED_UNICODE));

            // Get linked data
            $MJTC_userinfo = $this->getSupportCandyTicketCustomerInfo($MJTC_ticket->customer);
            $MJTC_agentid = $this->getTicketAgentIdBySupportCandy($MJTC_ticket->assigned_agent);
            $MJTC_departmentid = $this->getTicketDepartmentIdBySupportCandy($MJTC_ticket->category);
            $MJTC_priorityid = $this->getTicketPriorityIdBySupportCandy($MJTC_ticket->priority);

            $MJTC_idresult = MJTC_includer::MJTC_getModel('ticket')->getRandomTicketId();
            $MJTC_ticketid = $MJTC_idresult['ticketid'];
            $MJTC_customticketno = $MJTC_idresult['customticketno'];

            // Determine ticket status
            $MJTC_ticket_status = 1;
            if ($MJTC_ticket->status == 1) $MJTC_ticket_status = 1;
            elseif ($MJTC_ticket->status == $MJTC_after_customer_reply) $MJTC_ticket_status = 2;
            elseif ($MJTC_ticket->status == $MJTC_after_agent_reply) $MJTC_ticket_status = 4;
            elseif ($MJTC_ticket->status == $MJTC_close_ticket_status) $MJTC_ticket_status = 5;
            else $MJTC_ticket_status = $this->getTicketStatusIdBySupportCandy($MJTC_ticket->status);

            $MJTC_isanswered = ($MJTC_ticket_status == 4) ? 1 : 0;

            $MJTC_ticket_closed = "0000-00-00 00:00:00";
            if (!empty($MJTC_ticket->date_closed) && $MJTC_ticket->date_closed != '0000-00-00 00:00:00') {
                $MJTC_ticket_status = 5;
                $MJTC_ticket_closed = $MJTC_ticket->date_closed;
            }
            // Ticket Default Status
            // 1 -> New Ticket
            // 2 -> Waiting admin/staff reply
            // 3 -> in progress
            // 4 -> waiting for customer reply
            // 5 -> close ticket

            $MJTC_newTicketData = [
                'id' => "",
                'uid' => $MJTC_userinfo["ms_uid"],
                'ticketid' => $MJTC_ticketid,
                'departmentid' => $MJTC_departmentid,
                'priorityid' => $MJTC_priorityid,
                'staffid' => $MJTC_agentid,
                'email' => $MJTC_userinfo["customer_email"],
                'name' => $MJTC_userinfo["customer_name"],
                'subject' => $MJTC_ticket->subject,
                'message' => $MJTC_ticket->reply_message,
                'helptopicid' => 0,
                'multiformid' => 1,
                'phone' => "",
                'phoneext' => "",
                'status' => $MJTC_ticket_status,
                'isoverdue' => "0",
                'isanswered' => $MJTC_isanswered,
                'duedate' => "0000-00-00 00:00:00",
                'reopened' => "0000-00-00 00:00:00",
                'closed' => $MJTC_ticket_closed,
                'closedby' => "0",
                'lastreply' => $MJTC_ticket->last_reply_on,
                'created' => $MJTC_ticket->date_created,
                'updated' => $MJTC_ticket->date_updated,
                'lock' => "0",
                'ticketviaemail' => "0",
                'ticketviaemail_id' => "0",
                'attachmentdir' => $MJTC_attachmentdir,
                'feedbackemail' => "0",
                'mergestatus' => "0",
                'mergewith' => "0",
                'mergenote' => "",
                'mergedate' => "0000-00-00 00:00:00",
                'multimergeparams' => "",
                'mergeuid' => "0",
                'params' => $MJTC_ticketparams,
                'hash' => "",
                'notificationid' => "0",
                'wcorderid' => $MJTC_wcorderid,
                'wcitemid' => "0",
                'wcproductid' => $MJTC_wcproductid,
                'eddorderid' => $MJTC_eddorderid,
                'eddproductid' => $MJTC_eddproductid,
                'eddlicensekey' => "",
                'envatodata' => "",
                'paidsupportitemid' => "0",
                'customticketno' => $MJTC_customticketno
            ];

            $MJTC_row = MJTC_includer::MJTC_getTable('tickets');
            $MJTC_error = 0;
            if (!$MJTC_row->bind($MJTC_newTicketData)) $MJTC_error = 1;
            if (!$MJTC_row->store()) $MJTC_error = 1;

            if ($MJTC_error == 1) {
                $this->support_candy_import_count['ticket']['failed'] += 1;
            } else {
                $this->support_candy_ticket_ids[] = $MJTC_ticket->id;
                $this->support_candy_import_count['ticket']['imported'] += 1;

                $ms_ticketid = $MJTC_row->id;
                $MJTC_hash = MJTC_includer::MJTC_getModel('ticket')->generateHash($ms_ticketid);
                $MJTC_query = "UPDATE `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` SET `hash`='" . esc_sql($MJTC_hash) . "' WHERE id=" . esc_sql($ms_ticketid);
                majesticsupport::$_db->query($MJTC_query);

                if(in_array('note', majesticsupport::$_active_addons)){
                    $this->getSupportCandyTicketNotes($ms_ticketid, $MJTC_ticket->id, $MJTC_attachmentdir);
                }
                $this->getSupportCandyTicketReplies($ms_ticketid, $MJTC_ticket->id, $MJTC_attachmentdir);
                $this->getSupportCandyTicketAttachments($ms_ticketid, "", $MJTC_ticket->replyid, $MJTC_attachmentdir);

                if (!empty($MJTC_ticket->pc_data) && in_array('privatecredentials', majesticsupport::$_active_addons)) {
                    $this->getSupportCandyTicketPrivateCredentials($ms_ticketid, $MJTC_userinfo["ms_uid"], $MJTC_ticket->pc_data);
                }

                if (in_array('tickethistory', majesticsupport::$_active_addons)) {
                    $this->getSupportCandyTicketActivityLog($ms_ticketid, $MJTC_ticket->id);
                }

                if (in_array('timetracking', majesticsupport::$_active_addons)) {
                    $this->getSupportCandyTicketStaffTime($ms_ticketid, $MJTC_ticket->id);
                }
            }
        }

        if (!empty($this->support_candy_ticket_ids)) {
            update_option('mjtc_support_ticket_support_candy_data_tickets', wp_json_encode($this->support_candy_ticket_ids));
        }
    }

    private function getSupportCandyTicketNotes($ms_ticket_id, $MJTC_sc_ticket_id, $MJTC_attachmentdir){
        $MJTC_query = "
            SELECT thread.*
                FROM `" . majesticsupport::$_db->prefix . "psmsc_threads` AS thread
                WHERE thread.ticket = " . (int)$MJTC_sc_ticket_id . "
                AND thread.type = 'note'
                ORDER BY thread.id ASC";
                    
        $threads = majesticsupport::$_db->get_results($MJTC_query);
        foreach($threads AS $thread){
            $MJTC_query = "
            SELECT id FROM `" . majesticsupport::$_db->prefix . "mjtc_support_users` WHERE wpuid = ".$thread->customer;
            $MJTC_agentid = $ms_user_id = majesticsupport::$_db->get_var($MJTC_query);
            $MJTC_filename = $this->getSupportCandyNoteAttachments($MJTC_sc_ticket_id, $thread->attachments, $MJTC_attachmentdir);

            $MJTC_replyData = [
                "id" => "",
                "ticketid" => $ms_ticket_id,
                "staffid" => $MJTC_agentid,
                "title" => MJTC_majesticsupportphplib::MJTC_strip_tags($thread->body),
                "note" => $thread->body,
                "status" => "1",
                "created" => $thread->date_created,
                "filename" => $MJTC_filename,
                "filesize" => 5334
            ];
            $MJTC_row = MJTC_includer::MJTC_getTable('note');
            $MJTC_data = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($MJTC_replyData);// remove slashes with quotes.
            $MJTC_error = 0;
            if (!$MJTC_row->bind($MJTC_data)) {
                $MJTC_error = 1;
            }
            if (!$MJTC_row->store()) {
                $MJTC_error = 1;
            }
            $ms_ticket_note_id = $MJTC_row->id;
        }
    }

    private function getSupportCandyTicketReplies($ms_ticket_id, $MJTC_sc_ticket_id, $MJTC_attachmentdir){
        $MJTC_query = "SELECT thread.*
                    FROM `" . majesticsupport::$_db->prefix . "psmsc_threads` AS thread
                    WHERE thread.ticket = " . (int)$MJTC_sc_ticket_id . "
                    AND thread.type = 'reply'
                    ORDER BY thread.id ASC";
                    
        $threads = majesticsupport::$_db->get_results($MJTC_query);

        if (empty($threads)) return;

        foreach ($threads as $thread) {
            $MJTC_userinfo = $this->getSupportCandyTicketCustomerInfo($thread->customer);

            $MJTC_replyData = [
                "id" => "",
                "uid" => isset($MJTC_userinfo["ms_uid"]) ? $MJTC_userinfo["ms_uid"] : 0,
                "ticketid" => $ms_ticket_id,
                "name" => isset($MJTC_userinfo["customer_name"]) ? $MJTC_userinfo["customer_name"] : __('Guest', 'majestic-support'),
                "message" => $thread->body,
                "staffid" => "",
                "rating" => "",
                "status" => "1",
                "created" => $thread->date_created,
                "ticketviaemail" => "",
                "viewed_by" => "",
                "viewed_on" => $thread->seen
            ];

            $MJTC_row = MJTC_includer::MJTC_getTable('replies');
            $MJTC_data = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($MJTC_replyData);

            $MJTC_error = 0;
            if (!$MJTC_row->bind($MJTC_data)) {
                $MJTC_error = 1;
            }
            if (!$MJTC_row->store()) {
                $MJTC_error = 1;
            }

            $ms_ticket_reply_id = $MJTC_row->id;

            if (!empty($ms_ticket_reply_id)) {
                $this->getSupportCandyTicketAttachments($ms_ticket_id, $ms_ticket_reply_id, $thread->id, $MJTC_attachmentdir);
            }
        }
    }

    private function getSupportCandyNoteAttachments($MJTC_ticket_id, $MJTC_attachments, $MJTC_attachmentdir){
        // Split by pipe
        $MJTC_parts = explode('|', $MJTC_attachments);

        // Get the first numeric value
        $MJTC_attachment_id = isset($MJTC_parts[0]) ? intval($MJTC_parts[0]) : null;

        if (empty($MJTC_attachment_id)) return;

        $MJTC_query = "
        SELECT attachment.*
            FROM `" . majesticsupport::$_db->prefix . "psmsc_attachments` AS attachment
            WHERE attachment.id = " . (int)$MJTC_attachment_id;
                    
        $MJTC_attachment = majesticsupport::$_db->get_row($MJTC_query);

        if (empty($MJTC_attachment)) return;

        require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
        require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';

        $MJTC_filesystem = new WP_Filesystem_Direct(true);
        $MJTC_upload_dir = wp_upload_dir();
        $MJTC_upload_path = $MJTC_upload_dir['basedir'];
        $MJTC_datadirectory = majesticsupport::$_config['data_directory'];
        $MJTC_path = trailingslashit($MJTC_upload_path) . $MJTC_datadirectory . "/attachmentdata/ticket/" . sanitize_file_name($MJTC_attachmentdir);

        if (!$MJTC_filesystem->exists($MJTC_path)) {
            wp_mkdir_p($MJTC_path);
        }
        $MJTC_safe_filename = sanitize_file_name($MJTC_attachment->name);
        $MJTC_source = $MJTC_upload_path . $MJTC_attachment->file_path;
        $MJTC_destination = $MJTC_path . "/" . $MJTC_safe_filename;

        if (!file_exists($MJTC_source)) {
            die( 'Attachment source file does not exist: ' . esc_html( $MJTC_source ) );
            return '';
        }

        $MJTC_result = $MJTC_filesystem->copy($MJTC_source, $MJTC_destination, true);
        if (!$MJTC_result) {
            die( 'Failed to copy attachment from ' . esc_html( $MJTC_source ) . ' to ' . esc_html( $MJTC_destination ) );
            return '';
        }
        return $MJTC_attachment->name;
        
    }

    private function getSupportCandyCustomFieldAttachments($MJTC_ticket_id, $MJTC_field_id, $MJTC_attachmentdir){
        $MJTC_query = "SELECT attachment.*
                    FROM `" . majesticsupport::$_db->prefix . "psmsc_attachments` AS attachment
                    WHERE attachment.id = " . (int)$MJTC_field_id;
                    
        $MJTC_attachment = majesticsupport::$_db->get_row($MJTC_query);

        if (empty($MJTC_attachment)) return;

        require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
        require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';

        $MJTC_filesystem = new WP_Filesystem_Direct(true);
        $MJTC_upload_dir = wp_upload_dir();
        $MJTC_upload_path = $MJTC_upload_dir['basedir'];
        $MJTC_datadirectory = majesticsupport::$_config['data_directory'];
        $MJTC_path = trailingslashit($MJTC_upload_path) . $MJTC_datadirectory . "/attachmentdata/ticket/" . sanitize_file_name($MJTC_attachmentdir);

        if (!$MJTC_filesystem->exists($MJTC_path)) {
            wp_mkdir_p($MJTC_path);
        }
        $MJTC_safe_filename = sanitize_file_name($MJTC_attachment->name);
        $MJTC_source = $MJTC_upload_path . $MJTC_attachment->file_path;
        $MJTC_destination = $MJTC_path . "/" . $MJTC_safe_filename;

        if (!file_exists($MJTC_source)) {
            die( 'Attachment source file does not exist: ' . esc_html( $MJTC_source ) );
            return '';
        }

        $MJTC_result = $MJTC_filesystem->copy($MJTC_source, $MJTC_destination, true);
        if (!$MJTC_result) {
            die( 'Failed to copy attachment from ' . esc_html( $MJTC_source ) . ' to ' . esc_html( $MJTC_destination ) );
            return '';
        }
        return $MJTC_attachment->name;
        
    }

    private function getSupportCandyTicketAttachments($ms_ticket_id, $ms_ticket_reply_id, $MJTC_sc_ticket_reply_id, $MJTC_attachmentdir){
        $MJTC_query = "SELECT attachment.*
                    FROM `" . majesticsupport::$_db->prefix . "psmsc_attachments` AS attachment
                    WHERE attachment.source_id = " . (int)$MJTC_sc_ticket_reply_id . "
                    ORDER BY attachment.id ASC";
                    
        $MJTC_attachments = majesticsupport::$_db->get_results($MJTC_query);

        if (empty($MJTC_attachments)) return;

        require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
        require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';

        $MJTC_filesystem = new WP_Filesystem_Direct(true);
        $MJTC_upload_dir = wp_upload_dir();
        $MJTC_upload_path = $MJTC_upload_dir['basedir'];
        $MJTC_datadirectory = majesticsupport::$_config['data_directory'];
        $MJTC_path = trailingslashit($MJTC_upload_path) . $MJTC_datadirectory . "/attachmentdata/ticket/" . sanitize_file_name($MJTC_attachmentdir);

        if (!$MJTC_filesystem->exists($MJTC_path)) {
            wp_mkdir_p($MJTC_path);
        }

        foreach ($MJTC_attachments as $MJTC_attachment) {
            $MJTC_safe_filename = sanitize_file_name($MJTC_attachment->name);
            $MJTC_source = $MJTC_upload_path . $MJTC_attachment->file_path;
            $MJTC_destination = $MJTC_path . "/" . $MJTC_safe_filename;

            $MJTC_attachmentData = [
                "id" => "",
                "ticketid" => $ms_ticket_id,
                "replyattachmentid" => $ms_ticket_reply_id,
                "filesize" => "", // Optionally: filesize($MJTC_source)
                "filename" => $MJTC_safe_filename,
                "filekey" => "",
                "deleted" => "",
                "status" => "1",
                "created" => $MJTC_attachment->date_created
            ];

            $MJTC_row = MJTC_includer::MJTC_getTable('attachments');
            $MJTC_data = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($MJTC_attachmentData);

            $MJTC_error = 0;
            if (!$MJTC_row->bind($MJTC_data)) {
                $MJTC_error = 1;
            }
            if (!$MJTC_row->store()) {
                $MJTC_error = 1;
            }

            if (!file_exists($MJTC_source)) {
                die( 'Attachment source file does not exist: ' . esc_html( $MJTC_source ) );
                continue;
            }

            $MJTC_result = $MJTC_filesystem->copy($MJTC_source, $MJTC_destination, true);
            if (!$MJTC_result) {
                die( 'Failed to copy attachment from ' . esc_html( $MJTC_source ) . ' to ' . esc_html( $MJTC_destination ) );
            }
        }
    }

    private function getSupportCandyTicketPrivateCredentials($ms_ticket_id, $ms_ticket_uid, $MJTC_pc_data) {
        $MJTC_decoded_data = json_decode($MJTC_pc_data, true);

        if (empty($MJTC_decoded_data) || !isset($MJTC_decoded_data['data'], $MJTC_decoded_data['secure_key'], $MJTC_decoded_data['secure_iv'])) {
            return; // Invalid or incomplete data
        }

        $MJTC_privateCredentials = $MJTC_decoded_data['data'];
        $MJTC_secure_key = base64_decode($MJTC_decoded_data['secure_key']);
        $MJTC_secure_iv = base64_decode($MJTC_decoded_data['secure_iv']);
        $MJTC_cipher = 'AES-128-CBC';

        foreach ($MJTC_privateCredentials as $MJTC_privateCredential) {
            if (empty($MJTC_privateCredential['data']) || !is_array($MJTC_privateCredential['data'])) {
                continue;
            }

            $MJTC_pc_data_info = '';

            foreach ($MJTC_privateCredential['data'] as $MJTC_entry) {
                if (!isset($MJTC_entry['label'], $MJTC_entry['value'])) continue;

                $MJTC_decrypted_value = openssl_decrypt(
                    base64_decode($MJTC_entry['value']),
                    $MJTC_cipher,
                    $MJTC_secure_key,
                    0,
                    $MJTC_secure_iv
                );

                $MJTC_label = sanitize_text_field($MJTC_entry['label']);
                $MJTC_value = sanitize_text_field($MJTC_decrypted_value);

                $MJTC_pc_data_info .= "$MJTC_label : $MJTC_value , ";
            }

            $MJTC_pc_array = [
                'credentialtype' => sanitize_text_field($MJTC_privateCredential['title']),
                'username'       => '',
                'password'       => '',
                'info'           => MJTC_majesticsupportphplib::MJTC_rtrim($MJTC_pc_data_info, ' , ')
            ];

            $MJTC_data = [
                'id'        => '',
                'uid'       => intval($ms_ticket_uid),
                'ticketid'  => intval($ms_ticket_id),
                'status'    => 1,
                'created'   => current_time('mysql'),
            ];

            // Clean and encode credential info
            $MJTC_encoded = wp_json_encode(array_filter($MJTC_pc_array));
            $MJTC_safe_encoded = MJTC_majesticsupportphplib::MJTC_safe_encoding($MJTC_encoded);
            $MJTC_data['data'] = MJTC_includer::MJTC_getObjectClass('privatecredentials')->encrypt($MJTC_safe_encoded);

            // Insert record
            if ($MJTC_data['ticketid'] > 0 && $MJTC_data['uid'] > 0) {
                $MJTC_row = MJTC_includer::MJTC_getTable('privatecredentials');
                if ($MJTC_row->bind($MJTC_data)) {
                    $MJTC_row->store(); // Failure silently ignored here; consider logging
                }
            }
        }
    }

    private function getSupportCandyTicketActivityLog($ms_ticket_id, $MJTC_sc_ticket_id) {
        $MJTC_sc_ticket_id = intval($MJTC_sc_ticket_id);
        $ms_ticket_id = intval($ms_ticket_id);

        if ($MJTC_sc_ticket_id <= 0 || $ms_ticket_id <= 0) return;

        $MJTC_query = "
            SELECT * FROM `" . majesticsupport::$_db->prefix . "psmsc_threads`
            WHERE (type = 'log' OR type = 'reply' OR type = 'note') AND ticket = ".$MJTC_sc_ticket_id." ORDER BY date_created DESC ";

        $threads = majesticsupport::$_db->get_results($MJTC_query);

        if (empty($threads)) return;

        foreach ($threads as $thread) {
            $MJTC_ticketid = $ms_ticket_id;

            // Get user information
            $MJTC_userinfo = $this->getSupportCandyTicketCustomerInfo($thread->customer);
            $MJTC_currentUserName = !empty($MJTC_userinfo['customer_name']) 
                ? esc_html($MJTC_userinfo['customer_name']) 
                : esc_html(__('Guest', 'majestic-support'));

            $MJTC_messagetype = __('Successfully', 'majestic-support');
            $MJTC_eventtype = '';
            $MJTC_message = '';

            if ($thread->type === 'log') {
                $MJTC_body = json_decode($thread->body);
                if (!empty($MJTC_body) && isset($MJTC_body->slug)) {
                    switch ($MJTC_body->slug) {
                        case 'assigned_agent':
                            $MJTC_eventtype = __('Assign Ticket To Agent', 'majestic-support');
                            $MJTC_message = __('Ticket is assigned to agent by', 'majestic-support') . " ( $MJTC_currentUserName )";
                            break;
                        case 'status':
                            if ($thread->customer == 0) {
                                continue 2;
                            }
                            $MJTC_eventtype = __('Ticket status change', 'majestic-support');
                            $MJTC_message = __('The status is changed by', 'majestic-support') . " ( $MJTC_currentUserName )";
                            break;
                        case 'priority':
                            $MJTC_eventtype = __('Change Priority', 'majestic-support');
                            $MJTC_message = __('Ticket Priority Is Changed By', 'majestic-support') . " ( $MJTC_currentUserName )";
                            break;
                        case 'category':
                            $MJTC_eventtype = __('Ticket department transfer', 'majestic-support');
                            $MJTC_message = __('The department is transferred by', 'majestic-support') . " ( $MJTC_currentUserName )";
                            break;
                        case 'subject':
                        case 'customer':
                            // Optionally handle or skip
                            break;
                    }
                }
            } elseif ($thread->type === 'reply') {
                $MJTC_eventtype = __('REPLIED_TICKET', 'majestic-support');
                $MJTC_message = __('Ticket is replied by', 'majestic-support') . " ( $MJTC_currentUserName )";
            } elseif ($thread->type === 'note') {
                $MJTC_eventtype = __('Post Internal Note', 'majestic-support');
                $MJTC_message = __('The internal note is posted by', 'majestic-support') . " ( $MJTC_currentUserName )";
            }

            if (!empty($MJTC_eventtype) && !empty($MJTC_message)) {
                MJTC_includer::MJTC_getModel('tickethistory')->addActivityLog(
                    $MJTC_ticketid, 1, esc_html($MJTC_eventtype), esc_html($MJTC_message), esc_html($MJTC_messagetype)
                );
            }
        }
    }

    private function getSupportCandyTicketStaffTime($ms_ticket_id, $MJTC_sc_ticket_id) {
        $MJTC_sc_ticket_id = intval($MJTC_sc_ticket_id);
        $ms_ticket_id = intval($ms_ticket_id);
        if ($MJTC_sc_ticket_id <= 0 || $ms_ticket_id <= 0) return;

        // Get all timer logs for the given SupportCandy ticket
        $MJTC_query = "
            SELECT * FROM `" . majesticsupport::$_db->prefix . "psmsc_timer_logs`
            WHERE ticket = ".$MJTC_sc_ticket_id;
        $MJTC_timers = majesticsupport::$_db->get_results($MJTC_query);

        if (empty($MJTC_timers)) return;

        foreach ($MJTC_timers as $MJTC_timer) {
            // Get HelpDesk staff ID from SupportCandy agent ID
            $MJTC_staffid = $this->getMSAgentIdByScAgentId($MJTC_timer->log_by);
            if (empty($MJTC_staffid)) continue;

            $MJTC_created = $MJTC_timer->date_started;

            // Handle and validate interval string
            try {
                $MJTC_interval = new DateInterval($MJTC_timer->time_spent);
            } catch (Exception $MJTC_e) {
                continue; // skip invalid time format
            }

            $MJTC_timer_seconds = ($MJTC_interval->d * 86400) + ($MJTC_interval->h * 3600) + ($MJTC_interval->i * 60) + $MJTC_interval->s;
            if ($MJTC_timer_seconds <= 0) continue;

            // Conflict detection
            $MJTC_created_dt = new DateTime($MJTC_created);
            $MJTC_now = new DateTime();
            $MJTC_interval_to_now = $MJTC_created_dt->diff($MJTC_now);
            $MJTC_systemtime = ($MJTC_interval_to_now->days * 86400) + ($MJTC_interval_to_now->h * 3600) + ($MJTC_interval_to_now->i * 60) + $MJTC_interval_to_now->s;

            $MJTC_conflict = ($MJTC_timer_seconds > $MJTC_systemtime) ? 1 : 0;

            // Prepare data
            $MJTC_data = [
                'staffid' => $MJTC_staffid,
                'ticketid' => $ms_ticket_id,
                'referencefor' => 1,
                'referenceid' => 0,
                'usertime' => $MJTC_timer_seconds,
                'systemtime' => $MJTC_systemtime,
                'conflict' => $MJTC_conflict,
                'description' => $MJTC_timer->description,
                'timer_edit_desc' => $MJTC_timer->description,
                'status' => 1,
                'created' => $MJTC_created
            ];

            $MJTC_row = MJTC_includer::MJTC_getTable('timetracking');
            $MJTC_data = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($MJTC_data);

            if (!$MJTC_row->bind($MJTC_data) || !$MJTC_row->store()) {
                // optionally log or count the failure
                continue;
            }
        }
    }

    private function getSupportCandyTicketCustomerInfo($MJTC_customerId) {
        // Sanitize and validate customer ID
        $MJTC_customerId = intval($MJTC_customerId);
        if ($MJTC_customerId <= 0) {
            return [
                "ms_uid" => "",
                "customer_name" => "",
                "customer_email" => ""
            ];
        }

        // Prepare secure query
        $MJTC_query = "
            SELECT customer.name, customer.email, user.id AS ms_uid
            FROM `" . majesticsupport::$_db->prefix . "psmsc_customers` AS customer
            INNER JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_users` AS user
                ON user.wpuid = customer.user
            WHERE customer.id = " . esc_sql($MJTC_customerId) . "
            LIMIT 1
        ";

        $MJTC_data = majesticsupport::$_db->get_row($MJTC_query);

        return [
            "ms_uid"       => $MJTC_data->ms_uid ?? "",
            "customer_name"  => $MJTC_data->name ?? "",
            "customer_email" => $MJTC_data->email ?? ""
        ];
    }

    private function getMSAgentIdByScAgentId($MJTC_sc_agent_id) {
        // Sanitize and validate input
        $MJTC_sc_agent_id = intval($MJTC_sc_agent_id);
        if ($MJTC_sc_agent_id <= 0) return null;

        // Secure SQL query using prepare()
        $MJTC_query = "
            SELECT agent.*
            FROM `" . majesticsupport::$_db->prefix . "psmsc_agents` AS sc_agent
            INNER JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_users` AS user
                ON user.wpuid = sc_agent.user
            INNER JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_staff` AS agent
                ON agent.uid = user.id
            WHERE sc_agent.id = " . esc_sql($MJTC_sc_agent_id) . "
            LIMIT 1
        ";

        $ms_agent = majesticsupport::$_db->get_row($MJTC_query);

        return $ms_agent ?: null;
    }

    private function getTicketAgentIdBySupportCandy($MJTC_customerId) {
        // Validate customer ID
        $MJTC_customerId = intval($MJTC_customerId);
        if ($MJTC_customerId <= 0) {
            return null;
        }

        // Get mapped user info
        $ms_user = $this->getSupportCandyTicketCustomerInfo($MJTC_customerId);
        if (empty($ms_user['ms_uid'])) {
            return null;
        }

        $MJTC_uid = intval($ms_user['ms_uid']);

        // Securely query agent by UID
        $MJTC_query = "
            SELECT id FROM `" . majesticsupport::$_db->prefix . "mjtc_support_staff` WHERE uid = ".$MJTC_uid;
            $ms_agent_id = majesticsupport::$_db->get_var($MJTC_query);

        return $ms_agent_id ? (int)$ms_agent_id : null;
    }
    
    private function getTicketDepartmentIdBySupportCandy($MJTC_categoryId) {
        // Validate and sanitize category ID
        $MJTC_categoryId = intval($MJTC_categoryId);
        if ($MJTC_categoryId <= 0) return null;

        // Get department (category) name from old table
        $MJTC_query = "
            SELECT name FROM `" . majesticsupport::$_db->prefix . "psmsc_categories` WHERE id = ".$MJTC_categoryId;
        $MJTC_category_name = majesticsupport::$_db->get_var($MJTC_query);

        if (empty($MJTC_category_name)) return null;

        // Match department by name (case-insensitive)
        $MJTC_query = "
            SELECT id FROM `" . majesticsupport::$_db->prefix . "mjtc_support_departments` WHERE LOWER(departmentname) = '".MJTC_majesticsupportphplib::MJTC_strtolower(MJTC_majesticsupportphplib::MJTC_trim(esc_sql($MJTC_category_name)))."'";
        $ms_department_id = majesticsupport::$_db->get_var($MJTC_query);

        return $ms_department_id ? (int)$ms_department_id : null;
    }

    private function getTicketStatusIdBySupportCandy($MJTC_statusId) {
        // Sanitize and validate input
        $MJTC_statusId = intval($MJTC_statusId);
        if ($MJTC_statusId <= 0) return null;

        // Get status name from source table
        $MJTC_query = "
            SELECT name FROM `" . majesticsupport::$_db->prefix . "psmsc_statuses` WHERE id = ".$MJTC_statusId;
        $MJTC_status_name = majesticsupport::$_db->get_var($MJTC_query);

        if (empty($MJTC_status_name)) return null;

        // Find matching status in destination table
        $MJTC_query = "SELECT id FROM `" . majesticsupport::$_db->prefix . "mjtc_support_statuses` WHERE LOWER(status) = '".MJTC_majesticsupportphplib::MJTC_strtolower(MJTC_majesticsupportphplib::MJTC_trim(esc_sql($MJTC_status_name)))."'";
        $ms_status_id = majesticsupport::$_db->get_var($MJTC_query);

        return $ms_status_id ? (int)$ms_status_id : null;
    }

    private function getTicketPriorityIdBySupportCandy($MJTC_priorityId) {
        // Sanitize and validate input
        $MJTC_priorityId = intval($MJTC_priorityId);
        if ($MJTC_priorityId <= 0) return null;

        // Fetch priority from source table
        $MJTC_query = "
            SELECT name
            FROM `" . majesticsupport::$_db->prefix . "psmsc_priorities` 
            WHERE id = ".$MJTC_priorityId;
        $MJTC_priority_name = majesticsupport::$_db->get_var($MJTC_query);

        if (empty($MJTC_priority_name)) return null;

        // Find corresponding priority in destination table
        $MJTC_query = "
            SELECT id FROM `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` 
                WHERE LOWER(priority) = '".MJTC_majesticsupportphplib::MJTC_strtolower(MJTC_majesticsupportphplib::MJTC_trim(esc_sql($MJTC_priority_name)))."'";
            $ms_priority_id = majesticsupport::$_db->get_var($MJTC_query);

        return $ms_priority_id ? (int)$ms_priority_id : null;
    }

    private function getAgentRoleIdBySupportCandy($MJTC_roleId) {
        // Get stored agent roles
        $MJTC_roles = get_option('wpsc-agent-roles', array());

        // Get role label for the given role ID
        $MJTC_role_label = isset($MJTC_roles[$MJTC_roleId]['label']) ? MJTC_majesticsupportphplib::MJTC_trim($MJTC_roles[$MJTC_roleId]['label']) : '';

        if (!empty($MJTC_role_label)) {
            // Prepare and execute safe SQL query
            $MJTC_query = "SELECT id
                FROM `" . majesticsupport::$_db->prefix . "mjtc_support_acl_roles` 
                WHERE LOWER(name) = '" . MJTC_majesticsupportphplib::MJTC_strtolower(esc_sql($MJTC_role_label)) . "'";
            $ms_roleid = majesticsupport::$_db->get_var($MJTC_query);

            return $ms_roleid ? (int)$ms_roleid : null;
        }

        return null;
    }

    private function importSupportCandyUsers() {
        // check if user already processed for import
        $MJTC_imported_users = array();
        $MJTC_imported_users_json = get_option('mjtc_support_ticket_support_candy_data_users');
        if(!empty($MJTC_imported_users_json)){
            $MJTC_imported_users = json_decode($MJTC_imported_users_json,true);
        }

        // Fetch all customers
        $MJTC_query = "SELECT * FROM `" . majesticsupport::$_db->prefix . "psmsc_customers`";
        $MJTC_customers = majesticsupport::$_db->get_results($MJTC_query);

        if (empty($MJTC_customers)) return;

        foreach ($MJTC_customers as $MJTC_customer) {
            $MJTC_customer_id = intval($MJTC_customer->id);
            $MJTC_wpuid       = intval($MJTC_customer->user);
            $MJTC_name        = sanitize_text_field($MJTC_customer->name ?? '');
            $MJTC_email       = sanitize_email($MJTC_customer->email ?? '');

            // Skip if already imported
            if (in_array($MJTC_customer_id, $MJTC_imported_users, true)) {
                $this->support_candy_import_count['user']['skipped']++;
                continue;
            }

            // Check if user already exists
            $MJTC_user_query = "SELECT user.*
                       FROM `" . majesticsupport::$_db->prefix . "mjtc_support_users` AS user
                       WHERE user.wpuid = ".$MJTC_wpuid;
            $MJTC_existing_user = majesticsupport::$_db->get_row($MJTC_user_query);

            if ($MJTC_existing_user) {
                $this->support_candy_import_count['user']['skipped']++;
                continue;
            }

            // Prepare data for new user
            $MJTC_row = MJTC_includer::MJTC_getTable('users');
            $MJTC_data = [
                'id'            => '',
                'wpuid'         => $MJTC_wpuid,
                'name'          => $MJTC_name,
                'display_name'  => $MJTC_name,
                'user_email'    => $MJTC_email,
                'status'        => 1,
                'issocial'      => 0,
                'socialid'      => null,
                'autogenerated' => 0,
            ];

            // Attempt to save the new user
            $MJTC_row->bind($MJTC_data);
            if (!$MJTC_row->store()) {
                $this->support_candy_import_count['user']['failed']++;
                continue;
            }

            // Store successful import info
            $this->support_candy_users_array[$MJTC_customer_id] = $MJTC_row->id;
            $this->support_candy_user_ids[] = $MJTC_customer_id;
            $this->support_candy_import_count['user']['imported']++;
        }

        // Save list of imported user IDs
        if (!empty($this->support_candy_user_ids)) {
            update_option('mjtc_support_ticket_support_candy_data_users', wp_json_encode(array_unique(array_merge($MJTC_imported_users, $this->support_candy_user_ids))));
        }
    }

    private function importSupportCandyTicketFields() {
        // Get all ticket-related custom fields
        $MJTC_query = "
            SELECT * FROM `" . majesticsupport::$_db->prefix . "psmsc_custom_fields`
            WHERE slug LIKE 'cust_%' AND type LIKE 'cf_%'
            AND field = 'ticket';";
        $MJTC_custom_fields = majesticsupport::$_db->get_results($MJTC_query);

        if (!$MJTC_custom_fields) return;

        // Get visibility settings
        $MJTC_ticket_field_options = get_option("wpsc-tff");

        $this->sc_ticket_custom_fields = [];
        $this->sc_ticket_custom_fields_custom = [];

        foreach ($MJTC_custom_fields as $MJTC_custom_field) {
            $MJTC_slug = esc_sql($MJTC_custom_field->slug);

            // Map field types
            switch ($MJTC_custom_field->type) {
                case "cf_textfield":
                case "cf_number":
                case "cf_url":
                case "cf_time":
                    $MJTC_fieldtype = "text"; break;
                case "cf_multi_select":
                    $MJTC_fieldtype = "multiple"; break;
                case "cf_single_select":
                    $MJTC_fieldtype = "combo"; break;
                case "cf_radio_button":
                    $MJTC_fieldtype = "radio"; break;
                case "cf_checkbox":
                    $MJTC_fieldtype = "checkbox"; break;
                case "cf_textarea":
                    $MJTC_fieldtype = "textarea"; break;
                case "cf_date":
                case "cf_datetime":
                    $MJTC_fieldtype = "date"; break;
                case "cf_email":
                    $MJTC_fieldtype = "email"; break;
                case "cf_file_attachment_multiple":
                case "cf_file_attachment_single":
                    $MJTC_fieldtype = "file"; break;
                case "cf_edd_order":
                    $this->sc_ticket_custom_fields[] = [
                        "name" => $MJTC_slug,
                        "type" => 'cf_edd_order',
                        "ms_filedorderingid" => '',
                        "ms_filedorderingfield" => '',
                    ];
                    $this->support_candy_import_count['field']['skipped'] += 1;
                    continue 2; break;
                case "cf_edd_product":
                    $this->sc_ticket_custom_fields[] = [
                        "name" => $MJTC_slug,
                        "type" => 'cf_edd_product',
                        "ms_filedorderingid" => '',
                        "ms_filedorderingfield" => '',
                    ];
                    $this->support_candy_import_count['field']['skipped'] += 1;
                    continue 2; break;
                case "cf_woo_order":
                    $this->sc_ticket_custom_fields[] = [
                        "name" => $MJTC_slug,
                        "type" => 'cf_woo_order',
                        "ms_filedorderingid" => '',
                        "ms_filedorderingfield" => '',
                    ];
                    $this->support_candy_import_count['field']['skipped'] += 1;
                    continue 2; break;
                case "cf_woo_product":
                    $this->sc_ticket_custom_fields[] = [
                        "name" => $MJTC_slug,
                        "type" => 'cf_woo_product',
                        "ms_filedorderingid" => '',
                        "ms_filedorderingfield" => '',
                    ];
                    $this->support_candy_import_count['field']['skipped'] += 1;
                    continue 2; break;
                default:
                    $MJTC_fieldtype = "text"; break;
            }

            $MJTC_query = "SELECT id,field FROM `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` WHERE isuserfield = 1 AND LOWER(fieldtitle) ='".esc_sql(MJTC_majesticsupportphplib::MJTC_strtolower($MJTC_custom_field->name))."' AND userfieldtype ='".esc_sql($MJTC_fieldtype)."' AND fieldfor = 1";
            $MJTC_field_record = majesticsupport::$_db->get_row($MJTC_query);

            if(!empty($MJTC_field_record)){ // this will make sure
                $this->support_candy_import_count['field']['skipped'] += 1;
                continue;
            }
            // Load options for select-type fields
            $MJTC_option_values = [];

            $table = majesticsupport::$_db->prefix . "psmsc_tickets";
            $MJTC_column = $MJTC_custom_field->slug;

            // Get all columns from the table only once
            static $MJTC_existing_columns = null;

            if ($MJTC_existing_columns === null) {
                $MJTC_existing_columns = majesticsupport::$_db->get_col("SHOW COLUMNS FROM `$table`");
            }

            // Check if the column exists
            if (in_array($MJTC_column, $MJTC_existing_columns)) {
                $MJTC_query = "SELECT `" . $MJTC_column . "` FROM `" . $table . "`";
                $MJTC_field = majesticsupport::$_db->get_row($MJTC_query);
            } else {
                $MJTC_field = null; // Column doesn't exist
            }
            if(isset($MJTC_field)){ // field in the ticket table
                $MJTC_query = "SELECT name FROM `" . majesticsupport::$_db->prefix . "psmsc_options` WHERE custom_field = ".$MJTC_custom_field->id." ORDER BY load_order;";
                $MJTC_field_options = majesticsupport::$_db->get_results($MJTC_query);
                if ($MJTC_field_options) {
                    foreach ($MJTC_field_options as $MJTC_field_option) {
                        $MJTC_option_values[] = $MJTC_field_option->name;
                    }
                }
            }

            // Build visibility data
            $MJTC_visibledata = [
                "visibleLogic" => [],
                "visibleParent" => [],
                "visibleValue" => [],
                "visibleCondition" => [],
            ];

            $MJTC_defaultvalue_input = "";
            $MJTC_defaultvalue_select = "";
            if($MJTC_fieldtype == "combo" || $MJTC_fieldtype == "radio" || $MJTC_fieldtype == "multiple" || $MJTC_fieldtype == "checkbox" || $MJTC_fieldtype == "depandant_field") {

                $MJTC_field_ids = explode('|', $MJTC_custom_field->default_value);

                // Sanitize and cast to integers
                $MJTC_field_id = isset($MJTC_field_ids[0]) ? intval($MJTC_field_ids[0]) : null;

                // Check if we have valid IDs
                if (!empty($MJTC_field_id)) {
                    $MJTC_query = "SELECT name FROM `" . majesticsupport::$_db->prefix . "psmsc_options` WHERE id = " . $MJTC_field_id;
                    $MJTC_name = majesticsupport::$_db->get_col($MJTC_query);

                    // Combine names into comma-separated string
                    $MJTC_vardata = !empty($MJTC_name) ? implode(', ', $MJTC_name) : '';
                } else {
                    $MJTC_vardata = '';
                }

                $MJTC_defaultvalue_select = $MJTC_vardata;
            } else {
                $MJTC_defaultvalue_input = $MJTC_custom_field->default_value;
            }

            // Prepare field data for import
            $MJTC_fieldOrderingData = [
                "id" => "",
                "field" => $MJTC_slug,
                "fieldtitle" => $MJTC_custom_field->name,
                "ordering" => "",
                "section" => "10",
                "placeholder" => $MJTC_custom_field->placeholder_text,
                "description" => $MJTC_custom_field->extra_info,
                "fieldfor" => "1",
                "published" => "1",
                "sys" => "0",
                "cannotunpublish" => "0",
                "required" => "0",
                "size" => "100",
                "maxlength" => $MJTC_custom_field->char_limit,
                "cols" => "",
                "rows" => "",
                "isuserfield" => "1",
                "userfieldtype" => $MJTC_fieldtype,
                "depandant_field" => "",
                "visible_field" => "",
                "showonlisting" => "0",
                "cannotshowonlisting" => "0",
                "search_user" => "0",
                "search_admin" => "0",
                "cannotsearch" => "0",
                "isvisitorpublished" => "1",
                "multiformid" => "1",
                "userfieldparams" => "",
                "visibleparams" => "",
                "values" => $MJTC_option_values,
                "visibleParent" => $MJTC_visibledata["visibleParent"],
                "visibleValue" => $MJTC_visibledata["visibleValue"],
                "visibleCondition" => $MJTC_visibledata["visibleCondition"],
                "visibleLogic" => $MJTC_visibledata["visibleLogic"],
                "readonly" => 0,
                "adminonly" => 0,
                "defaultvalue_select" => $MJTC_defaultvalue_select,
                "defaultvalue_input" => $MJTC_defaultvalue_input,
            ];

            // Store field in SupportCandy
            $MJTC_record_saved = MJTC_includer::MJTC_getModel('fieldordering')->storeUserField($MJTC_fieldOrderingData);

            if ($MJTC_record_saved == 1) {
                $MJTC_query = "SELECT * FROM `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` ORDER BY id DESC LIMIT 1";
                    $MJTC_latest_record = majesticsupport::$_db->get_row($MJTC_query);

                $this->sc_ticket_custom_fields[] = [
                    "name" => $MJTC_slug,
                    "type" => $MJTC_fieldtype,
                    "ms_filedorderingid" => $MJTC_latest_record->id,
                    "ms_filedorderingfield" => $MJTC_latest_record->field,
                ];
                $this->sc_ticket_custom_fields_custom[$MJTC_custom_field->slug] = $MJTC_latest_record->field;
                
                $this->support_candy_import_count['field']['imported'] += 1;
            } else {
                $this->support_candy_import_count['field']['failed'] += 1;
                // Optionally log: die("Failed to import field: $MJTC_slug");
            }
        }

        foreach ($MJTC_custom_fields as $MJTC_custom_field) {
            $MJTC_slug = $MJTC_custom_field->slug;
            if (!empty($MJTC_ticket_field_options[$MJTC_slug]['visibility'])) {
                $MJTC_visibility_conditions = json_decode($MJTC_ticket_field_options[$MJTC_slug]['visibility']);
                $MJTC_field = $this->getTicketCustomFieldId($MJTC_custom_field->name);
                $MJTC_query = "SELECT * FROM `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` WHERE field = '".esc_sql($MJTC_field)."' LIMIT 1";
                $ms_field = majesticsupport::$_db->get_row($MJTC_query);
                if (empty($ms_field)) {
                    continue;
                }

                // Build visibility data
                $MJTC_visibledata = [
                    "visibleLogic" => [],
                    "visibleParent" => [],
                    "visibleValue" => [],
                    "visibleCondition" => [],
                ];
                if ($MJTC_visibility_conditions) {
                    foreach ($MJTC_visibility_conditions as $MJTC_visibility_condition) {
                        $MJTC_visibleLogic = 'AND';
                        foreach ($MJTC_visibility_condition as $MJTC_groupIndex => $MJTC_group) {
                            $MJTC_fieldtype = '';
                            if ($MJTC_group->slug == 'usergroups' || $MJTC_group->slug == 'description' || $MJTC_group->slug == 'assigned_agent') {
                                continue;
                            }
                            if ($MJTC_group->slug == 'priority') {
                                $MJTC_item_key = 'priority';
                                $MJTC_value = $this->getTicketPriorityIdBySupportCandy($MJTC_group->operand_val_1);
                                $MJTC_fieldtype = 'priority';
                            } elseif ($MJTC_group->slug == 'category') {
                                $MJTC_item_key = 'department';
                                $MJTC_value = $this->getTicketDepartmentIdBySupportCandy($MJTC_group->operand_val_1);
                                $MJTC_fieldtype = 'department';
                            } elseif ($MJTC_group->slug == 'subject') {
                                $MJTC_item_key = 'subject';
                                $MJTC_value = $MJTC_group->operand_val_1;
                                $MJTC_fieldtype = 'subject';
                            } else {
                                // $MJTC_item_key = $MJTC_group->slug;
                                $MJTC_item_key = $this->sc_ticket_custom_fields_custom[$MJTC_group->slug];
                                $MJTC_fieldtype = $this->checkTypeOfTheField($MJTC_item_key);
                                if ($MJTC_fieldtype == 'textarea') {
                                    continue;
                                } elseif ($this->sc_ticket_custom_fields_custom[$MJTC_custom_field->slug] == $MJTC_item_key) {
                                    continue;
                                }

                                if (in_array(strtolower($MJTC_fieldtype), ['multiple', 'checkbox', 'combo', 'radio'])) {
                                    $MJTC_field_ids = explode('|', $MJTC_group->operand_val_1[0]);

                                    // Sanitize and cast to integers
                                    $MJTC_field_ids = array_map('intval', array_filter($MJTC_field_ids));

                                    // Check if we have valid IDs
                                    if (!empty($MJTC_field_ids)) {
                                        $MJTC_placeholders = implode(',', $MJTC_field_ids);
                                        $MJTC_query = "SELECT name FROM `" . majesticsupport::$_db->prefix . "psmsc_options` WHERE id IN ($MJTC_placeholders)";
                                        $MJTC_names = majesticsupport::$_db->get_col($MJTC_query);

                                        // Combine names into comma-separated string
                                        $MJTC_value = !empty($MJTC_names) ? implode(', ', $MJTC_names) : '';
                                    } else {
                                        $MJTC_value = '';
                                    }
                                } else {
                                    $MJTC_value = $MJTC_group->operand_val_1;
                                }
                            }
                            if ($MJTC_custom_field->slug == 'priority') {
                                $MJTC_slug = 'priority';
                            } elseif ($MJTC_custom_field->slug == 'category') {
                                $MJTC_slug = 'department';
                            } elseif ($MJTC_custom_field->slug == 'subject') {
                                $MJTC_slug = 'subject';
                            } else {
                                $MJTC_slug = $this->sc_ticket_custom_fields_custom[$MJTC_custom_field->slug];
                            }
                            
                            $MJTC_visibledata["visibleParentField"][] = $MJTC_slug;
                            $MJTC_visibledata["visibleParent"][] = $MJTC_item_key;
                            $MJTC_visibledata["visibleCondition"][] = $this->mapOperatorToConditionCode($MJTC_group->operator, $MJTC_fieldtype);
                            $MJTC_visibledata["visibleValue"][] = $MJTC_value;
                            $MJTC_visibledata["visibleLogic"][] = $MJTC_visibleLogic;
                            $MJTC_visibleLogic = 'OR';
                        }
                        // remove default value in case of visiblity
                        $ms_field->defaultvalue = '';

                    }
                }

                $MJTC_option_values = [];
                if(isset($ms_field->userfieldparams)){
                    $MJTC_options = json_decode($ms_field->userfieldparams, true);
                    foreach($MJTC_options as $MJTC_key => $MJTC_value){
                        $MJTC_option_values[] = $MJTC_value;
                    }
                }
                
                // Prepare field data for import

                $MJTC_fieldOrderingData = [
                    "id" => $ms_field->id,
                    "field" => $ms_field->field,
                    "fieldtitle" => $ms_field->fieldtitle,
                    "ordering" => $ms_field->ordering,
                    "section" => $ms_field->section,
                    "placeholder" => $ms_field->placeholder,
                    "description" => $ms_field->description,
                    "fieldfor" => $ms_field->fieldfor,
                    "published" => $ms_field->published,
                    "sys" => $ms_field->sys,
                    "cannotunpublish" => $ms_field->cannotunpublish,
                    "required" => $ms_field->required,
                    "size" => $ms_field->size,
                    "maxlength" => $ms_field->maxlength,
                    "cols" => $ms_field->cols,
                    "rows" => $ms_field->rows,
                    "isuserfield" => $ms_field->isuserfield,
                    "userfieldtype" => $ms_field->userfieldtype,
                    "depandant_field" => $ms_field->depandant_field,
                    "visible_field" => $ms_field->visible_field,
                    "showonlisting" => $ms_field->showonlisting,
                    "cannotshowonlisting" => $ms_field->cannotshowonlisting,
                    "search_user" => $ms_field->search_user,
                    "search_admin" => $ms_field->search_admin,
                    "cannotsearch" => $ms_field->cannotsearch,
                    "isvisitorpublished" => $ms_field->isvisitorpublished,
                    "multiformid" => $ms_field->multiformid,
                    "userfieldparams" => $ms_field->userfieldparams,
                    "visibleparams" => $ms_field->visibleparams,
                    "readonly" => $ms_field->readonly,
                    "adminonly" => $ms_field->adminonly,
                    "defaultvalue" => $ms_field->defaultvalue,
                    "defaultvalue_select" => $ms_field->defaultvalue,
                    "defaultvalue_input" => $ms_field->defaultvalue,
                    "values" => $MJTC_option_values,
                    "visibleParent" => $MJTC_visibledata["visibleParent"],
                    "visibleValue" => $MJTC_visibledata["visibleValue"],
                    "visibleCondition" => $MJTC_visibledata["visibleCondition"],
                    "visibleLogic" => $MJTC_visibledata["visibleLogic"],
                ];

                // Store field in SupportCandy
                $MJTC_record_saved = MJTC_includer::MJTC_getModel('fieldordering')->storeUserField($MJTC_fieldOrderingData);
            }
        }
    }

    private function mapOperatorToConditionCode($MJTC_operator, $type) {
        $MJTC_operator = strtoupper(MJTC_majesticsupportphplib::MJTC_trim($MJTC_operator));
        $MJTC_isComplex = false;

        if (!empty($type)) {
            $MJTC_complexTypes = ['combo', 'checkbox', 'radio', 'multiple','priority','department'];
            $MJTC_isComplex = !in_array($type, $MJTC_complexTypes);
        }

        switch ($MJTC_operator) {
            case '=':
            case 'LIKE':
            case 'IN':
                return $MJTC_isComplex ? "2" : "1";

            case 'NOT IN':
                return $MJTC_isComplex ? "3" : "0";

            default:
                return $MJTC_isComplex ? "3" : "0";
        }
    }

    // clean
    private function importSupportCandyAgents() {
        // check if user already processed for import
        $MJTC_imported_agents = array();
        $MJTC_imported_agent_json = get_option('mjtc_support_ticket_support_candy_data_agents');
        if(!empty($MJTC_imported_agents_json)){
            $MJTC_imported_agents = json_decode($MJTC_imported_agents_json,true);
        }
        $MJTC_query = "SELECT agent.*
                    FROM `" . majesticsupport::$_db->prefix . "psmsc_agents` AS agent;";
        $MJTC_agents = majesticsupport::$_db->get_results($MJTC_query);
        $total_agents = count($MJTC_agents);

        if($MJTC_agents){
            foreach($MJTC_agents AS $MJTC_agent){
                // Failed if addon not installed
                if (!in_array('agent', majesticsupport::$_active_addons) ) {
                    $this->support_candy_import_count['agent']['failed']++;
                    continue;
                }
                $MJTC_wpuid = (int) $MJTC_agent->user;
                // Skip if already imported
                if (in_array($MJTC_wpuid, $MJTC_imported_agents, true)) {
                    $this->support_candy_import_count['agent']['skipped']++;
                    continue;
                }
                $MJTC_name = $MJTC_agent->name;

                $MJTC_query = "
                    SELECT user.*
                        FROM `" . majesticsupport::$_db->prefix . "users` AS user
                        WHERE user.id = " . $MJTC_wpuid;
                $MJTC_wpuser = majesticsupport::$_db->get_row($MJTC_query);

                if(!$MJTC_wpuser){
                    $this->support_candy_import_count['agent']['failed'] += 1;
                    continue;
                }
                $mjtc_user = MJTC_includer::MJTC_getObjectClass('user')->MJTC_getmajesticsupportuidbyuserid($MJTC_wpuid);
                if (!empty($mjtc_user) && isset($mjtc_user[0]->id)) {
                    $mjtc_uid = (int)$mjtc_user[0]->id;
                } else {
                    $this->support_candy_import_count['agent']['failed']++;
                    continue;
                }

                $MJTC_query = "
                    SELECT staff.*
                        FROM `" . majesticsupport::$_db->prefix . "mjtc_support_staff` AS staff
                            WHERE staff.uid = " . $mjtc_uid;
                $MJTC_staff = majesticsupport::$_db->get_row($MJTC_query);

                if (!$MJTC_staff) {
                    
                    $MJTC_timestamp = date_i18n('Y-m-d H:i:s');

                    $MJTC_data = [
                        'id'           => '',
                        'uid'          => $mjtc_uid,
                        'groupid'      => '',
                        'roleid'       => $this->getAgentRoleIdBySupportCandy($MJTC_agent->role),
                        'departmentid' => '',
                        'firstname'    => $MJTC_name,
                        'lastname'     => '',
                        'username'     => $MJTC_wpuser->user_login,
                        'email'        => $MJTC_wpuser->user_email,
                        'signature'    => '',
                        'isadmin'      => '',
                        'status'       => $MJTC_agent->is_active,
                        'updated'      => $MJTC_timestamp,
                        'created'      => $MJTC_timestamp
                    ];

                    $MJTC_saved = MJTC_includer::MJTC_getModel('agent')->storeStaff($MJTC_data);

                    $this->support_candy_import_count['agent']['imported'] += 1;
                    $this->support_candy_agent_ids[] = $MJTC_wpuid;
                } else {
                    $this->support_candy_import_count['agent']['skipped'] += 1;
                }
            }
            // Save list of imported agent IDs
            if (!empty($this->support_candy_agent_ids)) {
                update_option('mjtc_support_ticket_support_candy_data_agents', wp_json_encode(array_unique(array_merge($MJTC_imported_agents, $this->support_candy_agent_ids))));
            }
        }
    }

    private function importSupportCandyAgentsRoles() {
        // check if role already processed for import
        $MJTC_imported_agent_roles = array();
        $MJTC_imported_agent_role_json = get_option('mjtc_support_ticket_support_candy_data_agent_roles');
        if(!empty($MJTC_imported_agent_roles_json)){
            $MJTC_imported_agent_roles = json_decode($MJTC_imported_agent_roles_json,true);
        }

        $MJTC_wpsc_agent_roles = get_option('wpsc-agent-roles', []);

        // Mapping role labels to permission keys
        $MJTC_permissionMap = [
            'View Credentials'         => ['view-pc-unassigned', 'view-pc-assigned-me', 'view-pc-assigned-others'],
            'Edit Credentials'         => ['modify-pc-unassigned', 'modify-pc-assigned-me', 'modify-pc-assigned-others'],
            'Delete Credentials'       => ['delete-pc-unassigned', 'delete-pc-assigned-me', 'delete-pc-assigned-others'],
            'View Ticket'              => ['view-unassigned', 'view-assigned-me', 'view-assigned-others'],
            'Reply Ticket'             => ['reply-unassigned', 'reply-assigned-me', 'reply-assigned-others'],
            'Post Internal Note'       => ['pn-unassigned', 'pn-assigned-me', 'pn-assigned-others'],
            'Assign Ticket To Agent'   => ['aa-unassigned', 'aa-assigned-me', 'aa-assigned-others'],
            'Change Ticket Status'     => ['cs-unassigned', 'cs-assigned-me', 'cs-assigned-others'],
            'Delete Ticket'            => ['dtt-unassigned', 'dtt-assigned-me', 'dtt-assigned-others'],
            'Add Ticket'               => ['create-as'],
            'View Agent Reports'       => ['view-reports'],
            'View Department Reports'  => ['view-reports'],
            'Edit Own Time'            => ['modify-timer-log']
        ];

        // Pre-fetch permission IDs for all labels
        $MJTC_permissionIds = [];
        foreach ($MJTC_permissionMap as $MJTC_label => $_) {
            if (in_array('agent', majesticsupport::$_active_addons) ) {
                $MJTC_escapedLabel = esc_sql($MJTC_label);
                $MJTC_sql = "SELECT id FROM `" . majesticsupport::$_db->prefix . "mjtc_support_acl_permissions` WHERE permission = '{$MJTC_escapedLabel}' LIMIT 1";
                $MJTC_permissionIds[$MJTC_label] = (int) majesticsupport::$_db->get_var($MJTC_sql);
            }
        }

        foreach ($MJTC_wpsc_agent_roles as $MJTC_role) {
            // Failed if addon not installed
            if (!in_array('agent', majesticsupport::$_active_addons) ) {
                $this->support_candy_import_count['agent_role']['failed']++;
                continue;
            }
            // Skip if already imported
            if (in_array($MJTC_role['label'], $MJTC_imported_agent_roles, true)) {
                $this->support_candy_import_count['agent_role']['skipped']++;
                continue;
            }
            $MJTC_query = "SELECT count(id)
                    FROM `" . majesticsupport::$_db->prefix . "mjtc_support_acl_roles` WHERE name ='".esc_sql($MJTC_role['label'])."'";
            $MJTC_agent_role = majesticsupport::$_db->get_var($MJTC_query);

            if($MJTC_agent_role == 0){
                $MJTC_output = [];
                $MJTC_caps = $MJTC_role['caps'] ?? [];

                foreach ($MJTC_permissionMap as $MJTC_label => $MJTC_permissions) {
                    foreach ($MJTC_permissions as $MJTC_perm) {
                        if (!empty($MJTC_caps[$MJTC_perm])) {
                            // Assign permission ID for this label if exists
                            if (!empty($MJTC_permissionIds[$MJTC_label])) {
                                $MJTC_output[$MJTC_label] = $MJTC_permissionIds[$MJTC_label];
                            }
                            break; // Stop checking other permissions for this label
                        }
                    }
                }

                $MJTC_data = [
                    'name'          => $MJTC_role['label'],
                    'roleperdata'   => $MJTC_output,
                    'id'            => '',
                    'created'       => '',
                    'updated'       => '',
                    'action'        => 'role_saverole',
                    'form_request'  => 'majesticsupport',
                    'save'          => 'Save Role',
                ];

                // save role and role permissions
                MJTC_includer::MJTC_getModel('role')->storeRole($MJTC_data);
                $this->support_candy_import_count['agent_role']['imported'] += 1;
                $this->support_candy_agent_role_ids[] = $MJTC_role['label'];
            } else {
                $this->support_candy_import_count['agent_role']['skipped'] += 1;
            }
        }
        // Save list of imported agent_role IDs
        if (!empty($this->support_candy_agent_role_ids)) {
            update_option('mjtc_support_ticket_support_candy_data_agent_roles', wp_json_encode(array_unique(array_merge($MJTC_imported_agent_roles, $this->support_candy_agent_role_ids))));
        }

    }

    private function importSupportCandyDepartments() {
        // check if department already processed for import
        $MJTC_imported_departments = array();
        $MJTC_imported_departments_json = get_option('mjtc_support_ticket_support_candy_data_departments');
        if(!empty($MJTC_imported_departments_json)){
            $MJTC_imported_departments = json_decode($MJTC_imported_departments_json,true);
        }
        $MJTC_query = "SELECT category.* FROM `" . majesticsupport::$_db->prefix . "psmsc_categories` AS category;";
        $MJTC_categories = majesticsupport::$_db->get_results($MJTC_query);

        if (empty($MJTC_categories)) return;

        // Get highest current ordering value
        $MJTC_query = "
            SELECT MAX(dept.ordering)
            FROM `" . majesticsupport::$_db->prefix . "mjtc_support_departments` AS dept
        ";
        $MJTC_ordering = (int) majesticsupport::$_db->get_var($MJTC_query);
        $MJTC_now = date_i18n('Y-m-d H:i:s');

        foreach ($MJTC_categories as $MJTC_category) {
            // Skip if already imported
            if (in_array($MJTC_category->id, $MJTC_imported_departments, true)) {
                $this->support_candy_import_count['department']['skipped']++;
                continue;
            }
            $MJTC_name = MJTC_majesticsupportphplib::MJTC_trim($MJTC_category->name);
            $MJTC_lower_name = MJTC_majesticsupportphplib::MJTC_strtolower($MJTC_name);

            // Check if department already exists
            $MJTC_check_query = "
                SELECT department.*
                FROM `" . majesticsupport::$_db->prefix . "mjtc_support_departments` AS department
                WHERE LOWER(department.departmentname) = '".esc_sql($MJTC_name)."'
            ";
            $MJTC_existing = majesticsupport::$_db->get_row($MJTC_check_query);

            if (!$MJTC_existing) {
                $MJTC_row = MJTC_includer::MJTC_getTable('departments');

                $MJTC_data = [
                    'id'              => '',
                    'emailid'         => '1',
                    'departmentname'  => $MJTC_name,
                    'ordering'        => $MJTC_ordering,
                    'status'          => '1',
                    'isdefault'       => '0',
                    'ispublic'        => '1',
                    'updated'         => $MJTC_now,
                    'created'         => $MJTC_now
                ];

                $MJTC_data = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($MJTC_data);
                $MJTC_row->bind($MJTC_data);

                if (!$MJTC_row->store()) {
                    $this->support_candy_import_count['department']['failed'] += 1;
                } else {
                    $this->support_candy_department_ids[] = $MJTC_category->id;
                    $this->support_candy_import_count['department']['imported'] += 1;
                }

                $MJTC_ordering++;
            } else {
                $this->support_candy_import_count['department']['skipped'] += 1;
            }
        }
        // Save list of imported department IDs
        if (!empty($this->support_candy_department_ids)) {
            update_option('mjtc_support_ticket_support_candy_data_departments', wp_json_encode(array_unique(array_merge($MJTC_imported_departments, $this->support_candy_department_ids))));
        }
    }

    private function importSupportCandyPriorities() {
        // check if priority already processed for import
        $MJTC_imported_priorities = array();
        $MJTC_imported_priorities_json = get_option('mjtc_support_ticket_support_candy_data_priorities');
        if(!empty($MJTC_imported_priorities_json)){
            $MJTC_imported_priorities = json_decode($MJTC_imported_priorities_json,true);
        }

        $MJTC_query = "SELECT priority.* FROM `" . majesticsupport::$_db->prefix . "psmsc_priorities` AS priority;";
        $MJTC_priorities = majesticsupport::$_db->get_results($MJTC_query);

        if (empty($MJTC_priorities)) return;

        // Get highest current ordering value
        $MJTC_query = "
            SELECT MAX(priority.ordering)
            FROM `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` AS priority
        ";
        $MJTC_ordering = (int) majesticsupport::$_db->get_var($MJTC_query);

        foreach ($MJTC_priorities as $MJTC_priority) {
            // Skip if already imported
            if (in_array($MJTC_priority->id, $MJTC_imported_priorities, true)) {
                $this->support_candy_import_count['priority']['skipped']++;
                continue;
            }

            $MJTC_name = MJTC_majesticsupportphplib::MJTC_trim(MJTC_majesticsupportphplib::MJTC_strtolower($MJTC_priority->name));

            // Check if this priority already exists in Majestic Support
            $MJTC_check_query = "
                SELECT priority.*
                FROM `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` AS priority
                WHERE LOWER(priority.priority) = '" . esc_sql($MJTC_name) . "'
                LIMIT 1
            ";
            $ms_priority = majesticsupport::$_db->get_row($MJTC_check_query);

            if (!$ms_priority) {
                $MJTC_row = MJTC_includer::MJTC_getTable('priorities');

                $MJTC_data = [
                    'id'               => '',
                    'priority'         => $MJTC_priority->name,
                    'prioritycolour'   => $MJTC_priority->color,
                    'priorityurgency'  => '',
                    'overduetypeid'    => 1,
                    'overdueinterval'  => 7,
                    'ordering'         => $MJTC_ordering,
                    'status'           => '1',
                    'isdefault'        => '0',
                    'ispublic'         => '1'
                ];

                $MJTC_data = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($MJTC_data);
                $MJTC_row->bind($MJTC_data);

                if (!$MJTC_row->store()) {
                    $this->support_candy_import_count['priority']['failed'] += 1;
                } else {
                    $this->support_candy_priority_ids[] = $MJTC_priority->id;
                    $this->support_candy_import_count['priority']['imported'] += 1;
                }

                $MJTC_ordering++;
            } else {
                $this->support_candy_import_count['priority']['skipped'] += 1;
            }
        }
        // Save list of imported priority IDs
        if (!empty($this->support_candy_priority_ids)) {
            update_option('mjtc_support_ticket_support_candy_data_priorities', wp_json_encode(array_unique(array_merge($MJTC_imported_priorities, $this->support_candy_priority_ids))));
        }
    }

    private function importSupportCandyPremades() {
        // check if premade already processed for import
        $MJTC_imported_premades = array();
        $MJTC_imported_premades_json = get_option('mjtc_support_ticket_support_candy_data_premades');
        if(!empty($MJTC_imported_premades_json)){
            $MJTC_imported_premades = json_decode($MJTC_imported_premades_json,true);
        }
        $MJTC_query = "
            SELECT canned_reply.*
            FROM `" . majesticsupport::$_db->prefix . "psmsc_canned_reply` AS canned_reply
        ";
        $MJTC_canned_replies = majesticsupport::$_db->get_results($MJTC_query);

        if (empty($MJTC_canned_replies)) return;

        foreach ($MJTC_canned_replies as $MJTC_canned_reply) {
            $title = MJTC_majesticsupportphplib::MJTC_trim(MJTC_majesticsupportphplib::MJTC_strtolower($MJTC_canned_reply->title));
            // Failed if addon not installed
            if (!in_array('cannedresponses', majesticsupport::$_active_addons) ) {
                $this->support_candy_import_count['canned response']['failed']++;
                continue;
            }
            // Skip if already imported
            if (in_array($MJTC_canned_reply->id, $MJTC_imported_premades, true)) {
                $this->support_candy_import_count['canned response']['skipped']++;
                continue;
            }
            // Check if this priority already exists in Majestic Support
            $MJTC_check_query = "
                SELECT premade.*
                FROM `" . majesticsupport::$_db->prefix . "mjtc_support_department_message_premade` AS premade
                WHERE LOWER(premade.title) = '" . esc_sql($title) . "'
                LIMIT 1
            ";
            $ms_canned_reply = majesticsupport::$_db->get_row($MJTC_check_query);

            if (!$ms_canned_reply) {

                $MJTC_departmentid = '';

                // Try to match category to department
                if (!empty($MJTC_canned_reply->categories)) {
                    $MJTC_category_query = "
                        SELECT category.name
                        FROM `" . majesticsupport::$_db->prefix . "psmsc_categories` AS category
                        WHERE category.id = " . esc_sql($MJTC_canned_reply->categories) . "
                    ";
                    $MJTC_category = majesticsupport::$_db->get_row($MJTC_category_query);

                    if ($MJTC_category) {
                        $MJTC_department_query = "
                            SELECT department.id
                            FROM `" . majesticsupport::$_db->prefix . "mjtc_support_departments` AS department
                            WHERE LOWER(department.departmentname) = '" . MJTC_majesticsupportphplib::MJTC_strtolower(esc_sql($MJTC_category->name)) . "'
                            LIMIT 1
                        ";
                        $MJTC_department = majesticsupport::$_db->get_row($MJTC_department_query);
                        if ($MJTC_department) {
                            $MJTC_departmentid = $MJTC_department->id;
                        }
                    }
                }

                // If no matching department found, use default
                if (empty($MJTC_departmentid)) {
                    $MJTC_departmentid = MJTC_includer::MJTC_getModel('department')->getDefaultDepartmentID();
                }

                // Prepare canned response data
                $MJTC_row = MJTC_includer::MJTC_getTable('cannedresponses');
                $MJTC_updated = date_i18n('Y-m-d H:i:s');

                $MJTC_data = [
                    'id'          => '',
                    'departmentid'=> $MJTC_departmentid,
                    'title'       => $MJTC_canned_reply->title,
                    'answer'      => $MJTC_canned_reply->body,
                    'status'      => '1',
                    'updated'     => $MJTC_updated,
                    'created'     => $MJTC_canned_reply->date_created
                ];

                $MJTC_data = majesticsupport::MJTC_sanitizeData($MJTC_data);
                $MJTC_data['answer'] = MJTC_includer::MJTC_getModel('majesticsupport')->getSanitizedEditorData($MJTC_data['answer']);
                $MJTC_data = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($MJTC_data);

                $MJTC_row->bind($MJTC_data);
                if (!$MJTC_row->store()) {
                    $this->support_candy_import_count['canned response']['failed'] += 1;
                } else {
                    $this->support_candy_premade_ids[] = $MJTC_canned_reply->id;
                    $this->support_candy_import_count['canned response']['imported'] += 1;
                }
            } else {
                $this->support_candy_import_count['canned response']['skipped'] += 1;
            }
        }

        // Save list of imported premade IDs
        if (!empty($this->support_candy_premade_ids)) {
            update_option('mjtc_support_ticket_support_candy_data_premades', wp_json_encode(array_unique(array_merge($MJTC_imported_premades, $this->support_candy_premade_ids))));
        }
    }

    private function importSupportCandyStatus() {
        // Load previously imported statuses
        $MJTC_imported_statuses = [];
        $MJTC_imported_statuses_json = get_option('mjtc_support_ticket_support_candy_data_statuses');
        if (!empty($MJTC_imported_statuses_json)) {
            $MJTC_imported_statuses = json_decode($MJTC_imported_statuses_json, true);
        }

        // Get SupportCandy statuses (excluding system/default ones)
        $MJTC_query = "
            SELECT status.*
            FROM `" . majesticsupport::$_db->prefix . "psmsc_statuses` AS status
            WHERE status.id > 4
        ";
        $MJTC_statuses = majesticsupport::$_db->get_results($MJTC_query);

        if (empty($MJTC_statuses)) return;

        // Get highest current ordering value
        $MJTC_query = "
            SELECT MAX(status.ordering)
            FROM `" . majesticsupport::$_db->prefix . "mjtc_support_statuses` AS status
        ";
        $MJTC_ordering = (int) majesticsupport::$_db->get_var($MJTC_query);

        // Build array of existing JS statuses (cleaned)
        $MJTC_query = "
            SELECT status.status
            FROM `" . majesticsupport::$_db->prefix . "mjtc_support_statuses` AS status
        ";
        $msstatuses = majesticsupport::$_db->get_results($MJTC_query);
        $MJTC_existing_status_names = array_map(function($MJTC_status) {
            return $this->cleanStringForCompare($MJTC_status->status);
        }, $msstatuses);

        foreach ($MJTC_statuses as $MJTC_status) {
            $MJTC_name = $MJTC_status->name;
            $MJTC_compare_name = $this->cleanStringForCompare($MJTC_name);

            // Skip if name already exists
            if (in_array($MJTC_compare_name, $MJTC_existing_status_names)) {
                $this->support_candy_import_count['status']['skipped'] += 1;
                continue;
            }

            // Skip if already imported
            if (in_array($MJTC_status->id, $MJTC_imported_statuses)) {
                $this->support_candy_import_count['status']['skipped'] += 1;
                continue;
            }

            // Prepare new status data
            $MJTC_row = MJTC_includer::MJTC_getTable('statuses');
            $MJTC_data = [
                'id'             => '',
                'status'         => $MJTC_name,
                'statuscolour'   => $MJTC_status->color,
                'statusbgcolour' => $MJTC_status->bg_color,
                'sys'            => '0',
                'ordering'       => $MJTC_ordering
            ];

            $MJTC_data = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($MJTC_data);
            $MJTC_row->bind($MJTC_data);

            if (!$MJTC_row->store()) {
                $this->support_candy_import_count['status']['failed'] += 1;
            } else {
                $this->support_candy_status_ids[] = $MJTC_status->id;
                $this->support_candy_import_count['status']['imported'] += 1;
                $MJTC_ordering++;
            }
        }

        // Save updated list of imported statuses
        if (!empty($this->support_candy_status_ids)) {
            update_option('mjtc_support_ticket_support_candy_data_statuses', wp_json_encode($this->support_candy_status_ids));
        }
    }

    private function cleanStringForCompare($MJTC_string) {
        if (!is_string($MJTC_string) || $MJTC_string === '') {
            return $MJTC_string;
        }

        // Remove spaces, dashes, and underscores
        $MJTC_string = MJTC_majesticsupportphplib::MJTC_str_replace([' ', '-', '_'], '', $MJTC_string);

        // Convert to lowercase
        return MJTC_majesticsupportphplib::MJTC_strtolower($MJTC_string);
    }

    function getSupportCandyDataStats($MJTC_count_for) {
        // Only support SupportCandy (count_for = 1)
        if ($MJTC_count_for != 1) return;

        // Check if SupportCandy is active
        include_once ABSPATH . 'wp-admin/includes/plugin.php';
        if (!is_plugin_active('supportcandy/supportcandy.php')) {
            return new WP_Error('mjtc_inactive', 'SupportCandy is not active.');
        }

        $MJTC_entity_counts = [];

        // Users
        if (majesticsupport::$_db->get_var("SHOW TABLES LIKE '" . majesticsupport::$_db->prefix . "psmsc_customers'")) {
            $MJTC_query = "SELECT COUNT(*) FROM `" . majesticsupport::$_db->prefix . "psmsc_customers`";
            $MJTC_count = (int) majesticsupport::$_db->get_var($MJTC_query);
            if ($MJTC_count > 0) $MJTC_entity_counts['user'] = $MJTC_count;
        }

        // Agent Roles
        $MJTC_agent_roles = get_option('wpsc-agent-roles', []);
        if (!empty($MJTC_agent_roles)) {
            $MJTC_entity_counts['agent role'] = count($MJTC_agent_roles);
        }

        // Agents
        if (majesticsupport::$_db->get_var("SHOW TABLES LIKE '" . majesticsupport::$_db->prefix . "psmsc_agents'")) {
            $MJTC_query = "SELECT COUNT(*) FROM `" . majesticsupport::$_db->prefix . "psmsc_agents`";
            $MJTC_count = (int) majesticsupport::$_db->get_var($MJTC_query);
            if ($MJTC_count > 0) $MJTC_entity_counts['agent'] = $MJTC_count;
        }

        // Departments
        if (majesticsupport::$_db->get_var("SHOW TABLES LIKE '" . majesticsupport::$_db->prefix . "psmsc_categories'")) {
            $MJTC_query = "SELECT COUNT(*) FROM `" . majesticsupport::$_db->prefix . "psmsc_categories`";
            $MJTC_count = (int) majesticsupport::$_db->get_var($MJTC_query);
            if ($MJTC_count > 0) $MJTC_entity_counts['department'] = $MJTC_count;
        }

        // Priorities
        if (majesticsupport::$_db->get_var("SHOW TABLES LIKE '" . majesticsupport::$_db->prefix . "psmsc_priorities'")) {
            $MJTC_query = "SELECT COUNT(*) FROM `" . majesticsupport::$_db->prefix . "psmsc_priorities`";
            $MJTC_count = (int) majesticsupport::$_db->get_var($MJTC_query);
            if ($MJTC_count > 0) $MJTC_entity_counts['priority'] = $MJTC_count;
        }

        // Canned Responses
        if (majesticsupport::$_db->get_var("SHOW TABLES LIKE '" . majesticsupport::$_db->prefix . "psmsc_canned_reply'")) {
            $MJTC_query = "SELECT COUNT(*) FROM `" . majesticsupport::$_db->prefix . "psmsc_canned_reply`";
            $MJTC_count = (int) majesticsupport::$_db->get_var($MJTC_query);
            if ($MJTC_count > 0) $MJTC_entity_counts['canned response'] = $MJTC_count;
        }

        // Statuses
        if (majesticsupport::$_db->get_var("SHOW TABLES LIKE '" . majesticsupport::$_db->prefix . "psmsc_statuses'")) {
            $MJTC_query = "SELECT COUNT(*) FROM `" . majesticsupport::$_db->prefix . "psmsc_statuses` WHERE id > 4";
            $MJTC_count = (int) majesticsupport::$_db->get_var($MJTC_query);
            if ($MJTC_count > 0) $MJTC_entity_counts['status'] = $MJTC_count;
        }

        // Custom Ticket Fields
        if (majesticsupport::$_db->get_var("SHOW TABLES LIKE '" . majesticsupport::$_db->prefix . "psmsc_custom_fields'")) {
            $MJTC_query = "SELECT COUNT(*) 
                      FROM `" . majesticsupport::$_db->prefix . "psmsc_custom_fields`
                      WHERE `slug` LIKE 'cust_%' AND `type` LIKE 'cf_%' AND `field` = 'ticket'";
            $MJTC_count = (int) majesticsupport::$_db->get_var($MJTC_query);
            if ($MJTC_count > 0) $MJTC_entity_counts['field'] = $MJTC_count;
        }

        // Tickets with type 'report'
        if (majesticsupport::$_db->get_var("SHOW TABLES LIKE '" . majesticsupport::$_db->prefix . "psmsc_tickets'")) {
            $MJTC_query = "SELECT COUNT(DISTINCT t.id)
                      FROM `" . majesticsupport::$_db->prefix . "psmsc_tickets` AS t
                      INNER JOIN `" . majesticsupport::$_db->prefix . "psmsc_threads` AS r ON r.ticket = t.id
                      WHERE r.type = 'report'  AND t.is_active != 0";
            $MJTC_count = (int) majesticsupport::$_db->get_var($MJTC_query);
            if ($MJTC_count > 0) $MJTC_entity_counts['ticket'] = $MJTC_count;
        }

        majesticsupport::$_data['entity_counts'] = $MJTC_entity_counts;
    }


    //================
    // --------------
    //////////////////
    // Awesome Support
    //////////////////
    // ---------------
    //================

    function importAwesomeSupportData() {
        // Only for development – remove before pushing to production
        $this->deletesupportcandyimporteddata();

        // Reset previously imported IDs from options
        update_option('mjtc_support_ticket_awesome_support_data_statuses', '');
        update_option('mjtc_support_ticket_awesome_support_data_priorities', '');
        update_option('mjtc_support_ticket_awesome_support_data_users', '');
        update_option('mjtc_support_ticket_awesome_support_data_departments', '');
        update_option('mjtc_support_ticket_awesome_support_data_premades', '');
        update_option('mjtc_support_ticket_awesome_support_data_agents', '');
        update_option('mjtc_support_ticket_awesome_support_data_tickets', '');
        update_option('mjtc_support_ticket_awesome_support_data_faqs', '');
        update_option('mjtc_support_ticket_awesome_support_data_products', '');
        
        // Prepare filesystem and create necessary directories
        require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
        require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';
        $MJTC_filesystem = new WP_Filesystem_Direct(true);
        $MJTC_upload_dir = wp_upload_dir();
        $MJTC_upload_path = $MJTC_upload_dir['basedir'];
        $MJTC_datadirectory = majesticsupport::$_config['data_directory'];
        $MJTC_path = $MJTC_upload_path . "/" . $MJTC_datadirectory;

        if (!$MJTC_filesystem->exists($MJTC_path)) {
            wp_mkdir_p($MJTC_path);
        }
        $MJTC_path .= '/attachmentdata';
        if (!$MJTC_filesystem->exists($MJTC_path)) {
            wp_mkdir_p($MJTC_path);
        }
        $MJTC_path .= '/ticket';
        if (!$MJTC_filesystem->exists($MJTC_path)) {
            wp_mkdir_p($MJTC_path);
        }

        $this->importAwesomeSupportUsers();
        $this->importAwesomeSupportAgents();
        if (taxonomy_exists('department')) {
            $this->importAwesomeSupportDepartments();
        }
        $this->importAwesomeSupportPriorities();
        $this->importAwesomeSupportPremades();
        $this->importAwesomeSupportStatus();
        $this->importAwesomeSupportProducts();
        $this->importAwesomeSupportTicketFields();
        $this->getAwesomeSupportTickets($this->as_ticket_custom_fields);
        if(in_array('faq', majesticsupport::$_active_addons)){
            $this->importAwesomeSupportFaqs();
        }

        update_option('mjtc_import_counts',$this->awesome_support_import_count);

        return;
    }

    function getAwesomeSupportStats($MJTC_count_for) {
        // Only support Awesome Support (count_for = 2)
        if ($MJTC_count_for != 2) return;

        // Check if Awesome Support is active
        include_once ABSPATH . 'wp-admin/includes/plugin.php';
        if (!is_plugin_active('awesome-support/awesome-support.php')) {
            return new WP_Error('mjtc_inactive', 'Awesome Support is not active.');
        }

        $MJTC_entity_counts = [];

        // Users
        $missingUser = 0;
        $MJTC_query = "SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "users`";
        $MJTC_users = majesticsupport::$_db->get_var($MJTC_query);
        if ($MJTC_users > 0) $MJTC_entity_counts['user'] = $MJTC_users;

        // Agents
        $MJTC_agents = get_users([
            'role' => 'wpas_agent',
        ]);
        $MJTC_count = count($MJTC_agents);
        if ($MJTC_count > 0) $MJTC_entity_counts['agent'] = $MJTC_count;

        // Departments
        $MJTC_departments = get_terms([
            'taxonomy'   => 'department',
            'hide_empty' => false,
            'fields'     => 'ids'
        ]);
        $MJTC_count = is_array($MJTC_departments) ? count($MJTC_departments) : 0;
        if ($MJTC_count > 0) $MJTC_entity_counts['department'] = $MJTC_count;

        // Priorities
        $MJTC_priorities = get_terms([
            'taxonomy'   => 'ticket_priority',
            'hide_empty' => false,
            'fields'     => 'ids'
        ]);
        $MJTC_count = is_array($MJTC_priorities) ? count($MJTC_priorities) : 0;
        if ($MJTC_count > 0) $MJTC_entity_counts['priority'] = $MJTC_count;

        // Canned Responses
        $MJTC_count = post_type_exists( 'canned-response' ) ? $this->getPostConutByType( 'canned-response' ) : 0;
        if ($MJTC_count > 0) $MJTC_entity_counts['canned response'] = $MJTC_count;

        // Statuses
        $MJTC_count = post_type_exists( 'wpass_status' ) ? $this->getPostConutByType( 'wpass_status' ) : 0;
        if ($MJTC_count > 0) $MJTC_entity_counts['status'] = $MJTC_count;

        // Products
        $MJTC_products = get_terms([
            'taxonomy'   => 'product',
            'hide_empty' => false,
            'fields'     => 'ids'
        ]);
        $MJTC_count = is_array($MJTC_products) ? count($MJTC_products) : 0;
        if ($MJTC_count > 0) $MJTC_entity_counts['product'] = $MJTC_count;

        // Faqs
        $MJTC_count = post_type_exists( 'faq' ) ? $this->getPostConutByType( 'faq' ) : 0;
        if ($MJTC_count > 0) $MJTC_entity_counts['faq'] = $MJTC_count;

        // Custom Ticket Fields
        $MJTC_custom_fields = get_option("wpas_custom_fields");

        if (!empty($MJTC_custom_fields)) {
            $MJTC_count = is_array($MJTC_custom_fields) ? count($MJTC_custom_fields) : 0;
            if ($MJTC_count > 0) $MJTC_entity_counts['field'] = $MJTC_count;
        }

        // Tickets 
        $MJTC_tickets = wpas_get_tickets('any');
        $MJTC_count = count($MJTC_tickets);

        if ($MJTC_count > 0) $MJTC_entity_counts['ticket'] = $MJTC_count;

        majesticsupport::$_data['entity_counts'] = $MJTC_entity_counts;
    }

    // delete data only for development

    function deletesupportcandyimporteddata(){

        $MJTC_query = "DELETE FROM `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` WHERE id > 27;";
        majesticsupport::$_db->query($MJTC_query);

        $MJTC_query = "UPDATE `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` SET `visible_field`='' ";
        majesticsupport::$_db->query($MJTC_query);

        $MJTC_query = "DELETE FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets`;";
        majesticsupport::$_db->query($MJTC_query);

        $MJTC_query = "DELETE FROM `" . majesticsupport::$_db->prefix . "mjtc_support_staff_time`;";
        majesticsupport::$_db->query($MJTC_query);

        $MJTC_query = "DELETE FROM `" . majesticsupport::$_db->prefix . "mjtc_support_replies`;";
        majesticsupport::$_db->query($MJTC_query);
        
        $MJTC_query = "DELETE FROM `" . majesticsupport::$_db->prefix . "mjtc_support_attachments`;";
        majesticsupport::$_db->query($MJTC_query);
        
        $MJTC_query = "DELETE FROM `" . majesticsupport::$_db->prefix . "mjtc_support_priorities`;";
        majesticsupport::$_db->query($MJTC_query);
        
        if (in_array('agent', majesticsupport::$_active_addons)) {
            $MJTC_query = "DELETE FROM `" . majesticsupport::$_db->prefix . "mjtc_support_staff`;";
            majesticsupport::$_db->query($MJTC_query);
            $MJTC_query = "DELETE FROM `" . majesticsupport::$_db->prefix . "mjtc_support_acl_roles` WHERE id > 1;";
            majesticsupport::$_db->query($MJTC_query);
        }
        
        $MJTC_query = "DELETE FROM `" . majesticsupport::$_db->prefix . "mjtc_support_users`;";
        majesticsupport::$_db->query($MJTC_query);
        
        $MJTC_query = "DELETE FROM `" . majesticsupport::$_db->prefix . "mjtc_support_department_message_premade`;";
        majesticsupport::$_db->query($MJTC_query);

    }

    private function importAwesomeSupportAgents() {
        // check if user already processed for import
        $MJTC_imported_agents = array();
        $MJTC_imported_agent_json = get_option('mjtc_support_ticket_awesome_support_data_agents');
        if(!empty($MJTC_imported_agents_json)){
            $MJTC_imported_agents = json_decode($MJTC_imported_agents_json,true);
        }
        $MJTC_agents = get_users([
            'role' => 'wpas_agent',
        ]);
        $total_agents = count($MJTC_agents);

        if($MJTC_agents){
            if (in_array('agent', majesticsupport::$_active_addons) ) {
                $MJTC_roleid = $this->getAgentRoleIdByAwesomeSupport();
            }
            foreach($MJTC_agents AS $MJTC_agent){
                // Failed if addon not installed
                if (!in_array('agent', majesticsupport::$_active_addons) ) {
                    $this->awesome_support_import_count['agent']['failed']++;
                    continue;
                }
                $MJTC_wpuid = (int) $MJTC_agent->data->ID;
                // Skip if already imported
                if (in_array($MJTC_wpuid, $MJTC_imported_agents, true)) {
                    $this->awesome_support_import_count['agent']['skipped']++;
                    continue;
                }
                $MJTC_name = $MJTC_agent->data->display_name;

                $MJTC_query = "
                    SELECT user.*
                        FROM `" . majesticsupport::$_db->prefix . "users` AS user
                        WHERE user.id = " . $MJTC_wpuid;
                $MJTC_wpuser = majesticsupport::$_db->get_row($MJTC_query);

                if(!$MJTC_wpuser){
                    $this->awesome_support_import_count['agent']['failed'] += 1;
                    continue;
                }
                $mjtc_user = MJTC_includer::MJTC_getObjectClass('user')->MJTC_getmajesticsupportuidbyuserid($MJTC_wpuid);
                if (!empty($mjtc_user) && isset($mjtc_user[0]->id)) {
                    $mjtc_uid = (int)$mjtc_user[0]->id;
                } else {
                    $this->awesome_support_import_count['agent']['failed']++;
                    continue;
                }

                $MJTC_query = "
                    SELECT staff.*
                        FROM `" . majesticsupport::$_db->prefix . "mjtc_support_staff` AS staff
                            WHERE staff.uid = " . $mjtc_uid;
                $MJTC_staff = majesticsupport::$_db->get_row($MJTC_query);

                if (!$MJTC_staff) {
                    
                    $MJTC_timestamp = date_i18n('Y-m-d H:i:s');

                    $MJTC_data = [
                        'id'           => '',
                        'uid'          => $mjtc_uid,
                        'groupid'      => '',
                        'roleid'       =>  $MJTC_roleid,
                        'departmentid' => '',
                        'firstname'    => $MJTC_name,
                        'lastname'     => '',
                        'username'     => $MJTC_wpuser->user_login,
                        'email'        => $MJTC_wpuser->user_email,
                        'signature'    => '',
                        'isadmin'      => '',
                        'status'       => 1,
                        'updated'      => $MJTC_timestamp,
                        'created'      => $MJTC_timestamp
                    ];

                    $MJTC_saved = MJTC_includer::MJTC_getModel('agent')->storeStaff($MJTC_data);

                    $this->awesome_support_import_count['agent']['imported'] += 1;
                    $this->awesome_support_agent_ids[] = $MJTC_wpuid;
                } else {
                    $this->awesome_support_import_count['agent']['skipped'] += 1;
                }
            }
            // Save list of imported agent IDs
            if (!empty($this->awesome_support_agent_ids)) {
                update_option('mjtc_support_ticket_awesome_support_data_agents', wp_json_encode(array_unique(array_merge($MJTC_imported_agents, $this->awesome_support_agent_ids))));
            }
        }
    }

    private function getAgentRoleIdByAwesomeSupport() {

        $MJTC_data['id'] = '';
        $MJTC_data['name'] = 'AS Support Agent';
        $MJTC_data['status'] = 1;
        $MJTC_data['created'] = date_i18n('Y-m-d H:i:s');

        $MJTC_row = MJTC_includer::MJTC_getTable('acl_roles');
        if (!$MJTC_row->bind($MJTC_data)) {
            $MJTC_error = 1;
        }
        if (!$MJTC_row->store()) {
            $MJTC_error = 1;
        }
        if (empty($MJTC_error)) {
            return $MJTC_row->id;
        }

        return null;
    }

    private function importAwesomeSupportUsers() {
        // check if user already processed for import
        $MJTC_imported_users = array();
        $MJTC_imported_users_json = get_option('mjtc_support_ticket_awesome_support_data_users');
        if(!empty($MJTC_imported_users_json)){
            $MJTC_imported_users = json_decode($MJTC_imported_users_json,true);
        }

        // Fetch all customers
        $MJTC_query = "SELECT id FROM `" . majesticsupport::$_db->prefix . "users`";
        $MJTC_users = majesticsupport::$_db->get_results($MJTC_query);
        $MJTC_wpUsers = array();
        $mjtcUsers = array();
        foreach ($MJTC_users as $MJTC_key => $MJTC_user) {
            $MJTC_wpUsers[] = $MJTC_user->id;
        }
        $MJTC_query = " SELECT wpuid FROM `" . majesticsupport::$_db->prefix . "mjtc_support_users`";
        $MJTC_users = majesticsupport::$_db->get_results($MJTC_query);
        foreach ($MJTC_users as $MJTC_key => $MJTC_user) {
            $mjtcUsers[] = $MJTC_user->wpuid;
        }

        $missingUsers = array_diff($MJTC_wpUsers,$mjtcUsers);

        if (empty($missingUsers)) return;

        foreach ($missingUsers as $missingUser) {
            $MJTC_query = "SELECT * FROM `" . majesticsupport::$_db->prefix . "users` WHERE id = " . esc_sql($missingUser);
            $MJTC_customer = majesticsupport::$_db->get_row($MJTC_query);

            $MJTC_customer_id = intval($MJTC_customer->ID);
            $MJTC_wpuid       = intval($MJTC_customer->ID);
            $MJTC_name        = sanitize_text_field($MJTC_customer->display_name ?? '');
            $MJTC_email       = sanitize_email($MJTC_customer->user_email ?? '');

            // Skip if already imported
            if (in_array($MJTC_customer_id, $MJTC_imported_users, true)) {
                $this->awesome_support_import_count['user']['skipped']++;
                continue;
            }   

            // Prepare data for new user
            $MJTC_row = MJTC_includer::MJTC_getTable('users');
            $MJTC_data = [
                'id'            => '',
                'wpuid'         => $MJTC_wpuid,
                'name'          => $MJTC_name,
                'display_name'  => '',
                'user_email'    => $MJTC_email,
                'status'        => 1,
                'issocial'      => 0,
                'socialid'      => null,
                'autogenerated' => 0,
            ];

            // Attempt to save the new user
            $MJTC_row->bind($MJTC_data);
            if (!$MJTC_row->store()) {
                $this->awesome_support_import_count['user']['failed']++;
                continue;
            }

            // Store successful import info
            $this->awesome_support_users_array[$MJTC_customer_id] = $MJTC_row->wpuid;
            $this->awesome_support_user_ids[] = $MJTC_customer_id;
            $this->awesome_support_import_count['user']['imported']++;
        }

        // Save list of imported user IDs
        if (!empty($this->awesome_support_user_ids)) {
            update_option('mjtc_support_ticket_awesome_support_data_users', wp_json_encode(array_unique(array_merge($MJTC_imported_users, $this->awesome_support_user_ids))));
        }
    }

    private function importAwesomeSupportDepartments() {
        // check if department already processed for import
        $MJTC_imported_departments = array();
        $MJTC_imported_departments_json = get_option('mjtc_support_ticket_awesome_support_data_departments');
        if(!empty($MJTC_imported_departments_json)){
            $MJTC_imported_departments = json_decode($MJTC_imported_departments_json,true);
        }

        if (!taxonomy_exists('department')) {
            return;
        }
        $MJTC_departments = get_terms([
            'taxonomy'   => 'department',
            'hide_empty' => false,
        ]);

        if (empty($MJTC_departments)) return;

        // Get highest current ordering value
        $MJTC_query = "
            SELECT MAX(dept.ordering)
            FROM `" . majesticsupport::$_db->prefix . "mjtc_support_departments` AS dept
        ";
        $MJTC_ordering = (int) majesticsupport::$_db->get_var($MJTC_query);
        $MJTC_now = date_i18n('Y-m-d H:i:s');

        foreach($MJTC_departments AS $MJTC_department){
            // Skip if already imported
            if (in_array($MJTC_department->id, $MJTC_imported_departments, true)) {
                $this->awesome_support_import_count['department']['skipped']++;
                continue;
            }

            $MJTC_name = MJTC_majesticsupportphplib::MJTC_trim($MJTC_department->name);
            $MJTC_lower_name = MJTC_majesticsupportphplib::MJTC_strtolower($MJTC_name);

            // Check if department already exists
            $MJTC_check_query = "
                SELECT department.*
                FROM `" . majesticsupport::$_db->prefix . "mjtc_support_departments` AS department
                WHERE LOWER(department.departmentname) = '". esc_sql($MJTC_name) ."'";
            $MJTC_existing = majesticsupport::$_db->get_row($MJTC_check_query);

            if (!$MJTC_existing) { // not exists
                $MJTC_row = MJTC_includer::MJTC_getTable('departments');

                $MJTC_updated = date_i18n('Y-m-d H:i:s');
                $MJTC_created = date_i18n('Y-m-d H:i:s');

                $MJTC_data = [
                    'id'              => '',
                    'emailid'         => '1',
                    'departmentname'  => $MJTC_name,
                    'ordering'        => $MJTC_ordering,
                    'status'          => '1',
                    'isdefault'       => '0',
                    'ispublic'        => '1',
                    'updated'         => $MJTC_now,
                    'created'         => $MJTC_now
                ];

                $MJTC_data = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($MJTC_data);
                $MJTC_row->bind($MJTC_data);

                if (!$MJTC_row->store()) {
                    $this->awesome_support_import_count['department']['failed'] += 1;
                } else {
                    $this->awesome_support_department_ids[] = $MJTC_department->id;
                    $this->awesome_support_import_count['department']['imported'] += 1;
                }

                $MJTC_ordering++;
            } else {
                $this->awesome_support_import_count['department']['skipped'] += 1;
            }
        }
        // Save list of imported department IDs
        if (!empty($this->awesome_support_department_ids)) {
            update_option('mjtc_support_ticket_awesome_support_data_departments', wp_json_encode(array_unique(array_merge($MJTC_imported_departments, $this->awesome_support_department_ids))));
        }
    }
    
    private function importAwesomeSupportPriorities() {
        // check if priority already processed for import
        $MJTC_imported_priorities = array();
        $MJTC_imported_priorities_json = get_option('mjtc_support_ticket_awesome_support_data_priorities');
        if(!empty($MJTC_imported_priorities_json)){
            $MJTC_imported_priorities = json_decode($MJTC_imported_priorities_json,true);
        }

        $MJTC_priorities = get_terms([
            'taxonomy'   => 'ticket_priority',
            'hide_empty' => false,
        ]);

        if (is_wp_error($MJTC_priorities) || empty($MJTC_priorities)) return;

        foreach ($MJTC_priorities as $MJTC_key => $MJTC_priority) {
            $meta = get_term_meta($MJTC_priority->term_id);
            $MJTC_priorities[$MJTC_key]->meta = $meta;
        }
        
        // Get highest current ordering value
        $MJTC_query = "
            SELECT MAX(priority.ordering)
            FROM `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` AS priority
        ";
        $MJTC_ordering = (int) majesticsupport::$_db->get_var($MJTC_query);

        foreach ($MJTC_priorities AS $MJTC_priority) {
            // Skip if already imported
            if (in_array($MJTC_priority->id, $MJTC_imported_priorities, true)) {
                $this->awesome_support_import_count['priority']['skipped']++;
                continue;
            }

            $MJTC_name = MJTC_majesticsupportphplib::MJTC_trim(MJTC_majesticsupportphplib::MJTC_strtolower($MJTC_priority->name));

            // Check if this priority already exists in Majestic Support
            $MJTC_check_query = "
                SELECT priority.*
                FROM `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` AS priority
                WHERE LOWER(priority.priority) = '" . esc_sql($MJTC_name) . "'
                LIMIT 1
            ";
            $ms_priority = majesticsupport::$_db->get_row($MJTC_check_query);

            if (!$ms_priority) {
                $MJTC_row = MJTC_includer::MJTC_getTable('priorities');

                $MJTC_color = "#5e8f5b"; // default color
                if (!empty($MJTC_priority->meta['color'][0])) {
                    $MJTC_color = $MJTC_priority->meta['color'][0];
                }
                
                $MJTC_data = [
                    'id'               => '',
                    'priority'         => $MJTC_priority->name,
                    'prioritycolour'   => $MJTC_color,
                    'priorityurgency'  => '',
                    'overduetypeid'    => 1,
                    'overdueinterval'  => 7,
                    'ordering'         => $MJTC_ordering,
                    'status'           => '1',
                    'isdefault'        => '0',
                    'ispublic'         => '1'
                ];

                $MJTC_data = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($MJTC_data);
                $MJTC_row->bind($MJTC_data);

                if (!$MJTC_row->store()) {
                    $this->awesome_support_import_count['priority']['failed'] += 1;
                } else {
                    $this->awesome_support_priority_ids[] = $MJTC_priority->id;
                    $this->awesome_support_import_count['priority']['imported'] += 1;
                }

                $MJTC_ordering++;
            } else {
                $this->awesome_support_import_count['priority']['skipped'] += 1;
            }
        }
        // Save list of imported priority IDs
        if (!empty($this->awesome_support_priority_ids)) {
            update_option('mjtc_support_ticket_awesome_support_data_priorities', wp_json_encode(array_unique(array_merge($MJTC_imported_priorities, $this->awesome_support_priority_ids))));
        }
    }

    private function importAwesomeSupportStatus() {
        // Load previously imported statuses
        $MJTC_imported_statuses = [];
        $MJTC_imported_statuses_json = get_option('mjtc_support_ticket_awesome_support_data_statuses');
        if (!empty($MJTC_imported_statuses_json)) {
            $MJTC_imported_statuses = json_decode($MJTC_imported_statuses_json, true);
        }

        // Get SupportCandy statuses (excluding system/default ones)
        $MJTC_statuses = get_posts( [
            'post_type'      => 'wpass_status',
            'post_status'    => 'any', // includes all except 'auto-draft'
            'orderby'        => 'ID',
            'order'          => 'ASC',
            'numberposts'    => -1, // get all
            'exclude'        => get_posts([
                'post_type'   => 'wpass_status',
                'post_status'=> 'auto-draft',
                'fields'      => 'ids',
            ]),
        ] );

        if (empty($MJTC_statuses)) return;

        // Get highest current ordering value
        $MJTC_query = "
            SELECT MAX(status.ordering)
            FROM `" . majesticsupport::$_db->prefix . "mjtc_support_statuses` AS status
        ";
        $MJTC_ordering = (int) majesticsupport::$_db->get_var($MJTC_query);

        // Build array of existing JS statuses (cleaned)
        $MJTC_query = "
            SELECT status.status
            FROM `" . majesticsupport::$_db->prefix . "mjtc_support_statuses` AS status
        ";
        $msstatuses = majesticsupport::$_db->get_results($MJTC_query);
        $MJTC_existing_status_names = array_map(function($MJTC_status) {
            return $this->cleanStringForCompare($MJTC_status->status);
        }, $msstatuses);

        foreach ($MJTC_statuses as $MJTC_status) {
            $MJTC_name = $MJTC_status->post_title;
            $MJTC_compare_name = $this->cleanStringForCompare($MJTC_name);

            // Skip if name already exists
            if (in_array($MJTC_compare_name, $MJTC_existing_status_names)) {
                $this->awesome_support_import_count['status']['skipped'] += 1;
                continue;
            }

            // Skip if already imported
            if (in_array($MJTC_status->id, $MJTC_imported_statuses)) {
                $this->awesome_support_import_count['status']['skipped'] += 1;
                continue;
            }

            $MJTC_post_meta = get_post_meta($MJTC_status->ID);
            $MJTC_bgcolor = "#5e8f5b"; // default color
            if (!empty($MJTC_post_meta['status_color'][0])) {
                $MJTC_bgcolor = $MJTC_post_meta['status_color'][0];
            }

            // Prepare new status data
            $MJTC_row = MJTC_includer::MJTC_getTable('statuses');
            $MJTC_data = [
                'id'             => '',
                'status'         => $MJTC_name,
                'statuscolour'   => '#FFF',
                'statusbgcolour' => $MJTC_bgcolor,
                'sys'            => '0',
                'ordering'       => $MJTC_ordering
            ];

            $MJTC_data = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($MJTC_data);
            $MJTC_row->bind($MJTC_data);

            if (!$MJTC_row->store()) {
                $this->awesome_support_import_count['status']['failed'] += 1;
            } else {
                $this->awesome_support_status_ids[] = $MJTC_status->id;
                $this->awesome_support_import_count['status']['imported'] += 1;
                $MJTC_ordering++;
            }
        }

        // Save updated list of imported statuses
        if (!empty($this->awesome_support_status_ids)) {
            update_option('mjtc_support_ticket_awesome_support_data_statuses', wp_json_encode($this->awesome_support_status_ids));
        }
    }

    private function importAwesomeSupportPremades() {
        // check if premade already processed for import
        $MJTC_imported_premades = array();
        $MJTC_imported_premades_json = get_option('mjtc_support_ticket_awesome_support_data_premades');
        if(!empty($MJTC_imported_premades_json)){
            $MJTC_imported_premades = json_decode($MJTC_imported_premades_json,true);
        }

        // Get SupportCandy statuses (excluding system/default ones)
        $MJTC_canned_replies = get_posts( [
            'post_type'      => 'canned-response',
            'post_status'    => 'any', // includes all except 'auto-draft'
            'orderby'        => 'ID',
            'order'          => 'ASC',
            'numberposts'    => -1, // get all
            'exclude'        => get_posts([
                'post_type'   => 'canned-response',
                'post_status'=> 'auto-draft',
                'fields'      => 'ids',
            ]),
        ] );

        if (empty($MJTC_canned_replies)) return;

        foreach ($MJTC_canned_replies as $MJTC_canned_reply) {
            $title = MJTC_majesticsupportphplib::MJTC_trim(MJTC_majesticsupportphplib::MJTC_strtolower($MJTC_canned_reply->post_title));
            // Failed if addon not installed
            if (!in_array('cannedresponses', majesticsupport::$_active_addons) ) {
                $this->awesome_support_import_count['canned response']['failed']++;
                continue;
            }
            // Skip if already imported
            if (in_array($MJTC_canned_reply->id, $MJTC_imported_premades, true)) {
                $this->awesome_support_import_count['canned response']['skipped']++;
                continue;
            }
            // Check if this premade already exists in Majestic Support
            $MJTC_check_query = "
                SELECT premade.*
                FROM `" . majesticsupport::$_db->prefix . "mjtc_support_department_message_premade` AS premade
                WHERE LOWER(premade.title) = '" . esc_sql($title) . "'
                LIMIT 1
            ";
            $ms_canned_reply = majesticsupport::$_db->get_row($MJTC_check_query);

            if (!$ms_canned_reply) {
            
                $MJTC_departmentid = MJTC_includer::MJTC_getModel('department')->getDefaultDepartmentID();
                // Prepare canned response data
                $MJTC_row = MJTC_includer::MJTC_getTable('cannedresponses');
                $MJTC_updated = date_i18n('Y-m-d H:i:s');

                $MJTC_data = [
                    'id'          => '',
                    'departmentid'=> $MJTC_departmentid,
                    'title'       => $MJTC_canned_reply->post_title,
                    'answer'      => $MJTC_canned_reply->post_content,
                    'status'      => '1',
                    'updated'     => $MJTC_updated,
                    'created'     => $MJTC_canned_reply->post_date
                ];

                $MJTC_data = majesticsupport::MJTC_sanitizeData($MJTC_data);
                $MJTC_data = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($MJTC_data);

                $MJTC_row->bind($MJTC_data);
                if (!$MJTC_row->store()) {
                    $this->awesome_support_import_count['canned response']['failed'] += 1;
                } else {
                    $this->awesome_support_premade_ids[] = $MJTC_canned_reply->id;
                    $this->awesome_support_import_count['canned response']['imported'] += 1;
                }
            } else {
                $this->awesome_support_import_count['canned response']['skipped'] += 1;
            }
        }

        // Save list of imported premade IDs
        if (!empty($this->awesome_support_premade_ids)) {
            update_option('mjtc_support_ticket_awesome_support_data_premades', wp_json_encode(array_unique(array_merge($MJTC_imported_premades, $this->awesome_support_premade_ids))));
        }
    }

    private function importAwesomeSupportProducts(){
        // check if product already processed for import
        $MJTC_imported_products = array();
        $MJTC_imported_products_json = get_option('mjtc_support_ticket_awesome_support_data_products');
        if(!empty($MJTC_imported_products_json)){
            $MJTC_imported_products = json_decode($MJTC_imported_products_json,true);
        }

        $MJTC_products = get_terms([
            'taxonomy'   => 'product',
            'hide_empty' => false,
        ]);

        if (is_wp_error($MJTC_products) || empty($MJTC_products)) return;
        
        // Get highest current ordering value
        $MJTC_query = "
            SELECT MAX(product.ordering)
            FROM `" . majesticsupport::$_db->prefix . "mjtc_support_products` AS product
        ";
        $MJTC_ordering = (int) majesticsupport::$_db->get_var($MJTC_query);

        foreach($MJTC_products AS $MJTC_product){
            // Skip if already imported
            if (in_array($MJTC_product->term_id, $MJTC_imported_products, true)) {
                $this->awesome_support_import_count['product']['skipped']++;
                continue;
            }

            $MJTC_name = MJTC_majesticsupportphplib::MJTC_trim(MJTC_majesticsupportphplib::MJTC_strtolower($MJTC_product->name));

            // Check if this product already exists in Majestic Support
            $MJTC_check_query = "
                SELECT product.*
                FROM `" . majesticsupport::$_db->prefix . "mjtc_support_products` AS product
                WHERE LOWER(product.product) = '".esc_sql($MJTC_name) ."'
                LIMIT 1
            ";
            $ms_product = majesticsupport::$_db->get_row($MJTC_check_query);

            if(!$ms_product){
                $MJTC_row = MJTC_includer::MJTC_getTable('products');

                $MJTC_color = "#5e8f5b"; // default color
                if (!empty($MJTC_product->meta['color'][0])) {
                    $MJTC_color = $MJTC_product->meta['color'][0];
                }
                
                $MJTC_data = [
                    'id'               => '',
                    'product'         => $MJTC_product->name,
                    'status'           => '1',
                    'ordering'         => $MJTC_ordering
                ];

                $MJTC_data = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($MJTC_data);
                $MJTC_row->bind($MJTC_data);

                if (!$MJTC_row->store()) {
                    $this->awesome_support_import_count['product']['failed'] += 1;
                } else {
                    $this->awesome_support_product_ids[] = $MJTC_product->term_id;
                    $this->awesome_support_import_count['product']['imported'] += 1;
                }

                $MJTC_ordering++;
            } else {
                $this->awesome_support_import_count['product']['skipped'] += 1;
            }
        }
        // Save list of imported product IDs
        if (!empty($this->awesome_support_product_ids)) {
            update_option('mjtc_support_ticket_awesome_support_data_products', wp_json_encode(array_unique(array_merge($MJTC_imported_products, $this->awesome_support_product_ids))));
        }
    }


    // 
    // ticket
    // 
    function getAwesomeSupportTickets($MJTC_as_ticket_custom_fields) {
        // Check if tickets already processed for import
        $MJTC_imported_tickets = array();
        $MJTC_imported_tickets_json = get_option('mjtc_support_ticket_awesome_support_data_tickets');
        if (!empty($MJTC_imported_tickets_json)) {
            $MJTC_imported_tickets = json_decode($MJTC_imported_tickets_json, true);
        }

        $MJTC_tickets = wpas_get_tickets('any');

        $MJTC_new_tickets = array();
        foreach($MJTC_tickets AS $MJTC_ticket){
            // Skip if ticket already imported
            if (!empty($MJTC_imported_tickets) && in_array($MJTC_ticket->id, $MJTC_imported_tickets)) {
                $this->awesome_support_import_count['ticket']['skipped'] += 1;
                continue;
            }

            // Map custom fields
            $MJTC_params = array();
            foreach ($MJTC_as_ticket_custom_fields as $MJTC_as_ticket_custom_field) {
                $MJTC_field_name = $MJTC_as_ticket_custom_field["name"];
                $MJTC_custom_text = get_post_meta($MJTC_ticket->ID, '_wpas_'.$MJTC_field_name, true);
                $MJTC_vardata = "";
                
                if ($MJTC_custom_text) {
                    if ($MJTC_as_ticket_custom_field["type"] == "date") {
                        $MJTC_vardata = gmdate("Y-m-d", MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_custom_text));
                    } else {
                        $MJTC_vardata = $MJTC_custom_text;
                    }

                    if ($MJTC_vardata != '') {
                        if (is_array($MJTC_vardata)) {
                            $MJTC_vardata = implode(', ', array_filter($MJTC_vardata));
                        }
                        $MJTC_params[$MJTC_as_ticket_custom_field["ms_filedorderingfield"]] = MJTC_majesticsupportphplib::MJTC_htmlentities($MJTC_vardata);
                    }
                }
            }
            $MJTC_ticketparams = html_entity_decode(wp_json_encode($MJTC_params, JSON_UNESCAPED_UNICODE));
            $MJTC_post_meta = get_post_meta($MJTC_ticket->ID);
            
            
            $MJTC_assign_to = "";
            if(isset($MJTC_post_meta["_wpas_assignee"][0])) $MJTC_assign_to = $MJTC_post_meta["_wpas_assignee"][0];
            
            $MJTC_userinfo = $this->getAwesomeSupportTicketCustomerInfo($MJTC_ticket->post_author);
            
            $MJTC_departmentid = $this->getTicketDepartmentIdByAwesomeSupport($MJTC_ticket->ID);
            $MJTC_priorityid = $this->getTicketPriorityIdByAwesomeSupport($MJTC_ticket->ID);
            $MJTC_productid = $this->getTicketProductIdByAwesomeSupport($MJTC_ticket->ID);
            $MJTC_agentid = $this->getTicketAgentIdByAwesomeSupport($MJTC_ticket->ID);

            //get user fields
            $MJTC_idresult = MJTC_includer::MJTC_getModel('ticket')->getRandomTicketId();
            $MJTC_ticketid = $MJTC_idresult['ticketid'];
            $MJTC_customticketno = $MJTC_idresult['customticketno'];

            $MJTC_attachmentdir = MJTC_includer::MJTC_getModel('ticket')->getRandomFolderName();
            $MJTC_ticket_status = 1;

            $MJTC_custom_statuses = get_posts( [
                'post_type'      => 'wpass_status',
                'post_status'    => 'any', // includes all except 'auto-draft'
                'orderby'        => 'ID',
                'order'          => 'ASC',
                'numberposts'    => -1, // get all
                'exclude'        => get_posts([
                    'post_type'   => 'wpass_status',
                    'post_status'=> 'auto-draft',
                    'fields'      => 'ids',
                ]),
            ] );
            $MJTC_is_custom_status = 0;
            foreach ($MJTC_custom_statuses as $MJTC_custom_statuse) {
                if ($MJTC_ticket->post_status == $MJTC_custom_statuse->post_name) {
                    $MJTC_is_custom_status = 1;
                    continue; // stop cheacking further
                }
            }
            
            if(!empty($MJTC_is_custom_status)) {
                $MJTC_ticket_status = $this->getTicketStatusIdByAwesomeSupport($MJTC_ticket->post_status);
            } else {
                if($MJTC_post_meta["_wpas_status"][0] == "open"){
                    if(isset($MJTC_post_meta["_wpas_last_reply_date"][0])){
                        $MJTC_ticket_status = 1;
                    
                        if($MJTC_post_meta["_wpas_is_waiting_client_reply"][0] == "1"){ // 1 means waiting agent reply
                            $MJTC_ticket_status = 2;
                        }else{
                            $MJTC_ticket_status = 4;
                        }
                    }else{
                        $MJTC_ticket_status = 1;
                    }
                }
            }

            $MJTC_ticket_closed = "0000-00-00 00:00:00";

            if(isset($MJTC_post_meta["_ticket_closed_on"][0])){
                if($MJTC_post_meta["_ticket_closed_on"][0] == "closed"){
                    $MJTC_ticket_status = 5;
                    if(isset($MJTC_post_meta["_ticket_closed_on"][0])) {
                        $MJTC_ticket_closed = $MJTC_post_meta["_ticket_closed_on"][0];
                    }
                }
            }
            $MJTC_lastreply = "0000-00-00 00:00:00";
            if(isset($MJTC_post_meta["_wpas_last_reply_date"][0])){
                $MJTC_lastreply = $MJTC_post_meta["_wpas_last_reply_date"][0];
            }
            
            $MJTC_isanswered = 0;
            if($MJTC_ticket_status == 4) $MJTC_isanswered = 1;
            
            $MJTC_ticket_closedby = "";
            // Ticket Default Status
            // 1 -> New Ticket
            // 2 -> Waiting admin/staff reply
            // 3 -> in progress
            // 4 -> waiting for customer reply
            // 5 -> close ticket
    
            $MJTC_newTicketData = [
                'id' => "",
                'uid' => $MJTC_userinfo["ms_uid"],
                'ticketid' => $MJTC_ticketid,
                'departmentid' => $MJTC_departmentid,
                'priorityid' => $MJTC_priorityid,
                'productid' => $MJTC_productid,
                'staffid' => $MJTC_agentid,
                'email' => $MJTC_userinfo["customer_email"],
                'name' => $MJTC_userinfo["customer_name"],
                'subject' => $MJTC_ticket->post_title,
                'message' => $MJTC_ticket->post_content,
                'helptopicid' => 0,
                'multiformid' => 1,
                'phone' => "",
                'phoneext' => "",
                'status' => $MJTC_ticket_status,
                'isoverdue' => "0",
                'isanswered' => $MJTC_isanswered,
                'duedate' => "0000-00-00 00:00:00",
                'reopened' => "0000-00-00 00:00:00",
                'closed' => $MJTC_ticket_closed,
                'closedby' => $MJTC_ticket_closedby,
                'lastreply' => $MJTC_lastreply,
                'created' => $MJTC_ticket->post_date,
                'updated' => $MJTC_ticket->post_modified,
                'lock' => "0",
                'ticketviaemail' => "0",
                'ticketviaemail_id' => "0",
                'attachmentdir' => $MJTC_attachmentdir,
                'feedbackemail' => "0",
                'mergestatus' => "0",
                'mergewith' => "0",
                'mergenote' => "",
                'mergedate' => "0000-00-00 00:00:00",
                'multimergeparams' => "",
                'mergeuid' => "0",
                'params' => $MJTC_ticketparams,
                'hash' => "",
                'notificationid' => "0",
                'wcorderid' => "0",
                'wcitemid' => "0",
                'wcproductid' => "0",
                'eddorderid' => "0",
                'eddproductid' => "0",
                'eddlicensekey' => "",
                'envatodata' => "",
                'paidsupportitemid' => "0",
                'customticketno' => $MJTC_customticketno
            ];

            $MJTC_row = MJTC_includer::MJTC_getTable('tickets');
            $MJTC_error = 0;
            if (!$MJTC_row->bind($MJTC_newTicketData)) $MJTC_error = 1;
            if (!$MJTC_row->store()) $MJTC_error = 1;

            if ($MJTC_error == 1) {
                $this->awesome_support_import_count['ticket']['failed'] += 1;
            } else {
                $this->awesome_support_ticket_ids[] = $MJTC_ticket->id;
                $this->awesome_support_import_count['ticket']['imported'] += 1;

                $ms_ticketid = $MJTC_row->id;

                //update hash value against ticket
                $MJTC_hash = MJTC_includer::MJTC_getModel('ticket')->generateHash($ms_ticketid);
                $MJTC_query = "UPDATE `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` SET `hash`='" . esc_sql($MJTC_hash) . "' WHERE id=" . esc_sql($ms_ticketid);
                majesticsupport::$_db->query($MJTC_query);
                
                $this->getAwesomeSupportTicketReplies($ms_ticketid, $MJTC_ticket->ID, $MJTC_attachmentdir);
                $this->getAwesomeSupportTicketAttachments($ms_ticketid, "", $MJTC_ticket->ID, "", $MJTC_attachmentdir);


                if (in_array('privatecredentials', majesticsupport::$_active_addons)) {
                    $this->getAwesomeSupportTicketPrivateCredentials($ms_ticketid, $MJTC_userinfo["ms_uid"], $MJTC_ticket->ID);
                }

                if (in_array('tickethistory', majesticsupport::$_active_addons)) {
                    $this->getAwesomeSupportTicketActivityLog($ms_ticketid, $MJTC_ticket->ID);
                }
            }
            
        }
        if (!empty($this->awesome_support_ticket_ids)) {
            update_option('mjtc_support_ticket_awesome_support_data_tickets', wp_json_encode($this->awesome_support_ticket_ids));
        }
        
    }

    private function importAwesomeSupportTicketFields() {
        // Get all ticket-related custom fields
        $MJTC_custom_fields = get_option("wpas_custom_fields");

        if (!$MJTC_custom_fields) return;

        $this->as_ticket_custom_fields = [];

        foreach ($MJTC_custom_fields as $MJTC_custom_field) {
            // Map field types
            switch ($MJTC_custom_field["field_type"]){
                case "text":
                    $MJTC_fieldtype = "text"; break;
                case "url":
                    $MJTC_fieldtype = "text"; break;
                case "email":
                    $MJTC_fieldtype = "email"; break;
                case "number":
                    $MJTC_fieldtype = "text"; break;
                case "date-field":
                    $MJTC_fieldtype = "date"; break;
                case "password":
                    $MJTC_fieldtype = "text"; break;
                case "upload":
                    $MJTC_fieldtype = "file"; break;
                case "select":
                    $MJTC_fieldtype = "combo"; break;
                case "radio":
                    $MJTC_fieldtype = "radio"; break;
                case "checkbox":
                    $MJTC_fieldtype = "checkbox"; break;
                case "textarea":
                    $MJTC_fieldtype = "textarea"; break;
                case "wysiwyg":
                    $MJTC_fieldtype = "wysiwyg"; break;
                default:
                    $MJTC_fieldtype = "text"; break;
            }

            // Load options for select-type fields
            $MJTC_option_values = [];
            if(!empty($MJTC_custom_field['options'])){ // field in the ticket table
                $MJTC_field_options = $MJTC_custom_field['options'];
                if ($MJTC_field_options) {
                    foreach ($MJTC_field_options as $MJTC_key => $MJTC_field_option) {
                        $MJTC_option_values[] = $MJTC_key;
                    }
                }
            }

            // Build visibility data
            $MJTC_visibledata = [
                "visibleLogic" => [],
                "visibleParent" => [],
                "visibleValue" => [],
                "visibleCondition" => [],
            ];

            // Prepare field data for import
            $MJTC_fieldOrderingData = [
                "id" => "",
                // "field" => $MJTC_slug,
                "field" => $MJTC_custom_field['name'],
                "fieldtitle" => $MJTC_custom_field['title'],
                "ordering" => "",
                "section" => "10",
                "fieldfor" => "1",
                "published" => "1",
                "sys" => "0",
                "cannotunpublish" => "0",
                "required" => $MJTC_custom_field['required'],
                "size" => "100",
                "cols" => "",
                "rows" => "",
                "isuserfield" => "1",
                "userfieldtype" => $MJTC_fieldtype,
                "depandant_field" => "",
                "visible_field" => "",
                "showonlisting" => "0",
                "cannotshowonlisting" => "0",
                "search_user" => "0",
                "cannotsearch" => "0",
                "isvisitorpublished" => "1",
                "userfieldparams" => "",
                "multiformid" => "1",
                "visibleparams" => "",
                "values" => $MJTC_option_values,
                "visibleParent" => $MJTC_visibledata["visibleParent"],
                "visibleValue" => $MJTC_visibledata["visibleValue"],
                "visibleCondition" => $MJTC_visibledata["visibleCondition"],
                "visibleLogic" => $MJTC_visibledata["visibleLogic"],
                "placeholder" => $MJTC_custom_field['placeholder'],
                "description" => $MJTC_custom_field['desc'],
                "defaultvalue" => $MJTC_custom_field['default'],
                "readonly" => $MJTC_custom_field['readonly'],
            ];

            // Store field in SupportCandy
            $MJTC_record_saved = MJTC_includer::MJTC_getModel('fieldordering')->storeUserField($MJTC_fieldOrderingData);

            if ($MJTC_record_saved == 1) {
                $MJTC_query = "SELECT * FROM `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` ORDER BY id DESC LIMIT 1";
                $MJTC_latest_record = majesticsupport::$_db->get_row($MJTC_query);

                $this->as_ticket_custom_fields[] = [
                    "name" => $MJTC_custom_field['name'],
                    "type" => $MJTC_fieldtype,
                    "ms_filedorderingid" => $MJTC_latest_record->id,
                    "ms_filedorderingfield" => $MJTC_latest_record->field,
                ];
                $this->awesome_support_import_count['field']['imported'] += 1;
            } else {
                $this->awesome_support_import_count['field']['failed'] += 1;
                // Optionally log: die("Failed to import field: $MJTC_slug");
            }
        }
    }

    private function getAwesomeSupportTicketReplies($ms_ticket_id, $MJTC_ast_ticket_id, $MJTC_attachmentdir){
        $MJTC_query = "SELECT post.*
                    FROM `" . majesticsupport::$_db->prefix . "posts` AS post
                    WHERE post.post_parent = ".$MJTC_ast_ticket_id."
                    AND post.post_type = 'ticket_reply'
                    ORDER BY post.id ASC";
                    
        $MJTC_posts = majesticsupport::$_db->get_results($MJTC_query);

        if (empty($MJTC_posts)) return;


        foreach($MJTC_posts AS $MJTC_post){
            $MJTC_userinfo = $this->getAwesomeSupportTicketCustomerInfo($MJTC_post->post_author);
            $MJTC_uid = $MJTC_userinfo["ms_uid"];
            $MJTC_name = $MJTC_userinfo["customer_name"];
            if(empty($MJTC_userinfo["ms_uid"])){

                /*$MJTC_agentid = $this->getTicketAgentIdByAwesomeSupport($MJTC_conversation->person_id);
                if($MJTC_agentid){
                    $MJTC_query = "SELECT agent.*
                                FROM `" . majesticsupport::$_db->prefix . "mjtc_support_staff` AS agent
                                WHERE agent.id = ".$MJTC_agentid.";";
                                
                    $MJTC_agent = majesticsupport::$_db->get_row($MJTC_query);
                    $MJTC_uid = $MJTC_agent->uid;
                    $MJTC_name = $MJTC_agent->firstname;
                    if($MJTC_agent->lastname) $MJTC_name = $MJTC_name. " ". $MJTC_agent->lastname;
                    
                } */
            }

            $MJTC_replyData = [
                "id" => "",
                "uid" => $MJTC_uid,
                "ticketid" => $ms_ticket_id,
                "name" => $MJTC_name,
                "message" => $MJTC_post->post_content,
                "staffid" => "",
                "rating" => "",
                "status" => "1",
                "created" => $MJTC_post->post_date,
                "ticketviaemail" => "",
                "viewed_by" => "",
                "viewed_on" => ""
            ];
            $MJTC_row = MJTC_includer::MJTC_getTable('replies');
            $MJTC_data = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($MJTC_replyData);// remove slashes with quotes.
            $MJTC_error = 0;
            if (!$MJTC_row->bind($MJTC_data)) {
                $MJTC_error = 1;
            }
            if (!$MJTC_row->store()) {
                $MJTC_error = 1;
            }

            $ms_ticket_reply_id = $MJTC_row->id;

            if (!empty($ms_ticket_reply_id)) {
                $this->getAwesomeSupportTicketAttachments($ms_ticket_id, $ms_ticket_reply_id, $MJTC_ast_ticket_id, $MJTC_post->ID, $MJTC_attachmentdir);
            }

            if (in_array('timetracking', majesticsupport::$_active_addons)) {
                $this->getAwesomeSupportTicketStaffTime($ms_ticket_id, $MJTC_ast_ticket_id, $ms_ticket_reply_id, $MJTC_post->ID);
            }
            
        }
    }

    private function getAwesomeSupportTicketAttachments($ms_ticket_id, $ms_ticket_reply_id, $MJTC_ast_ticket_id, $MJTC_as_ticket_reply_id, $MJTC_attachmentdir){
        $MJTC_as_ticket_reply_id = intval($MJTC_as_ticket_reply_id);

        if ($MJTC_as_ticket_reply_id <= 0) return;


        $MJTC_query = "SELECT post.*
                    FROM `" . majesticsupport::$_db->prefix . "posts` AS post
                    WHERE post.post_parent = ".$MJTC_as_ticket_reply_id."
                    AND post.post_type = 'attachment'
                    ORDER BY post.id ASC";
                    
        $MJTC_posts = majesticsupport::$_db->get_results($MJTC_query);


        foreach($MJTC_posts AS $MJTC_post){
            $MJTC_post_meta = get_post_meta($MJTC_post->ID);
            if(isset($MJTC_post_meta["_wp_attachment_metadata"][0])){
                $MJTC_attachment = unserialize($MJTC_post_meta["_wp_attachment_metadata"][0]);
                $MJTC_file_name = basename($MJTC_attachment["file"]);         
                
            
                $MJTC_attachmentData = [
                    "id" => "",
                    "ticketid" => $ms_ticket_id,
                    "replyattachmentid" => $ms_ticket_reply_id,
                    "filesize" => "",
                    "filename" => $MJTC_file_name,
                    "filekey" => "",
                    "deleted" => "",
                    "status" => "1",
                    "created" => $MJTC_post->post_date
                ];
                $MJTC_row = MJTC_includer::MJTC_getTable('attachments');
                $MJTC_data = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($MJTC_attachmentData);// remove slashes with quotes.
                $MJTC_error = 0;
                if (!$MJTC_row->bind($MJTC_data)) {
                    $MJTC_error = 1;
                }
                if (!$MJTC_row->store()) {
                    $MJTC_error = 1;
                }
                require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
                require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';
                $MJTC_filesystem = new WP_Filesystem_Direct( true );
                $MJTC_upload_dir = wp_upload_dir();
                $MJTC_upload_path = $MJTC_upload_dir['basedir'];         // Server path to the uploads directory
                $MJTC_datadirectory = majesticsupport::$_config['data_directory'];
                $MJTC_path = $MJTC_upload_path."/".$MJTC_datadirectory."/attachmentdata/ticket/".$MJTC_attachmentdir;
                if(!$MJTC_filesystem->exists($MJTC_path)){
                    wp_mkdir_p($MJTC_path);
                }
                $MJTC_source = $MJTC_upload_path . "/" . $MJTC_attachment["file"]; // full path to original
                if (!file_exists($MJTC_source)) {
                    $MJTC_path_info = pathinfo($MJTC_source);

                    // Get directory and base filename (without extension)
                    $MJTC_directory = $MJTC_path_info['dirname'];
                    $MJTC_filename = $MJTC_path_info['filename']; // e.g., 01_5
                    $MJTC_extension = $MJTC_path_info['extension']; // e.g., jpg

                    // Desired sizes to check
                    $MJTC_sizes = ['100x100', '150x150', '300x300', '600x337', '768x431', '300x168'];
                    $MJTC_resized_file = '';

                    foreach ($MJTC_sizes as $MJTC_size) {
                        $MJTC_resized_path = $MJTC_directory . '/' . $MJTC_filename . '-' . $MJTC_size . '.' . $MJTC_extension;
                        if (file_exists($MJTC_resized_path)) {
                            $MJTC_resized_file = $MJTC_resized_path;
                            break;
                        }
                    }

                    // Fallback to original if no resized version found
                    if (!$MJTC_resized_file && file_exists($MJTC_source)) {
                        $MJTC_resized_file = $MJTC_source;
                    }
                } else {
                    $MJTC_resized_file = $MJTC_source;
                }
                $MJTC_destination = $MJTC_path."/".$MJTC_file_name;
                
                $MJTC_result = $MJTC_filesystem->move($MJTC_resized_file, $MJTC_destination, true);
            }
            
        }
    }

    private function getAwesomeSupportTicketCustomerInfo($MJTC_customerId){
        // Sanitize and validate customer ID
        $MJTC_customerId = intval($MJTC_customerId);
        if ($MJTC_customerId <= 0) {
            return [
                "ms_uid" => "",
                "customer_name" => "",
                "customer_email" => ""
            ];
        }

        // Prepare secure query
        $MJTC_query = "
            SELECT customer.name, customer.user_email, customer.id AS ms_uid
            FROM `" . majesticsupport::$_db->prefix . "mjtc_support_users` AS customer
            WHERE customer.wpuid = ". esc_sql($MJTC_customerId) ."
            LIMIT 1
        ";
        $MJTC_data = majesticsupport::$_db->get_row($MJTC_query);

        return [
            "ms_uid"       => $MJTC_data->ms_uid ?? "",
            "customer_name"  => $MJTC_data->name ?? "",
            "customer_email" => $MJTC_data->email ?? ""
        ];
    }

    private function getAwesomeSupportTicketPrivateCredentials($ms_ticket_id, $ms_ticket_uid, $MJTC_post_id) {
        $ms_ticket_uid = 1;
        // Get private credentials if they exist.
        if( get_post_meta( $MJTC_post_id, '_wpas_pc_credentials', true ) ) {
            $MJTC_credentials = get_post_meta( $MJTC_post_id, '_wpas_pc_credentials', true );
            
            $MJTC_encryption_key = get_post_meta( $MJTC_post_id, '_wpas_pc_encryption_key', true );

            foreach( $MJTC_credentials as $MJTC_key => $MJTC_value ) {
                $MJTC_system   = $this->MJTC_decrypt( $MJTC_value[ "system" ], $MJTC_encryption_key );
                $MJTC_username = $this->MJTC_decrypt( $MJTC_value[ "username" ], $MJTC_encryption_key );
                $MJTC_password = $this->MJTC_decrypt( $MJTC_value[ "password" ], $MJTC_encryption_key );
                $MJTC_url      = $this->MJTC_decrypt( $MJTC_value[ "url" ], $MJTC_encryption_key );
                $MJTC_note     = $this->MJTC_decrypt( $MJTC_value[ "note" ], $MJTC_encryption_key );

                $MJTC_pc_array = [
                    'credentialtype' => sanitize_text_field($MJTC_system),
                    'username'       => $MJTC_username,
                    'password'       => $MJTC_password,
                    'info'           => $MJTC_note
                ];

                $MJTC_data = [
                    'id'        => '',
                    'uid'       => intval($ms_ticket_uid),
                    'ticketid'  => intval($ms_ticket_id),
                    'status'    => 1,
                    'created'   => current_time('mysql'),
                ];

                // Clean and encode credential info
                $MJTC_encoded = wp_json_encode(array_filter($MJTC_pc_array));
                $MJTC_safe_encoded = MJTC_majesticsupportphplib::MJTC_safe_encoding($MJTC_encoded);
                $MJTC_data['data'] = MJTC_includer::MJTC_getObjectClass('privatecredentials')->encrypt($MJTC_safe_encoded);

                // Insert record
                if ($MJTC_data['ticketid'] > 0 && $MJTC_data['uid'] > 0) {
                    $MJTC_row = MJTC_includer::MJTC_getTable('privatecredentials');
                    if ($MJTC_row->bind($MJTC_data)) {
                        $MJTC_row->store(); // Failure silently ignored here; consider logging
                    }
                }
            }
        }
    }

    private function decrypt( $MJTC_message, $MJTC_key, $MJTC_encoded = true ) {
        $method = 'aes-256-ctr';

        if ( $MJTC_message == '' ) {
            return '';
        }

        if ( $MJTC_encoded ) {
            $MJTC_message = base64_decode( $MJTC_message, true );
            if ( $MJTC_message === false ) {
                return false;
            }
        }

        $MJTC_nonceSize  = openssl_cipher_iv_length( $method );
        $MJTC_nonce      = mb_substr( $MJTC_message, 0, $MJTC_nonceSize, '8bit' );
        $MJTC_ciphertext = mb_substr( $MJTC_message, $MJTC_nonceSize, null, '8bit' );

        $MJTC_plaintext = '';

        try {
            $MJTC_plaintext = openssl_decrypt( $MJTC_ciphertext, $method, $MJTC_key, OPENSSL_RAW_DATA, $MJTC_nonce );
        } catch ( Exception $MJTC_e ) {
            return false;
        }

        return $MJTC_plaintext;

    }

    private function getAwesomeSupportTicketActivityLog($ms_ticket_id, $MJTC_sc_ticket_id) {
        $MJTC_sc_ticket_id = intval($MJTC_sc_ticket_id);
        $ms_ticket_id = intval($ms_ticket_id);

        if ($MJTC_sc_ticket_id <= 0 || $ms_ticket_id <= 0) return;

        $threads = get_posts( [
            'post_parent'    => $MJTC_sc_ticket_id,
            'post_type'      => apply_filters( 'MJTC_wpas_replies_post_type', array(
                                'ticket_history',
                                'ticket_reply',
                                'ticket_log'
                             ) ),
            'post_status'    => 'any',
            'orderby'        => 'ID',
            'order'          => 'DESC',
            'numberposts'    => -1, // get all
            'exclude'        => get_posts([
                'post_type'  => 'wpass_status',
                'post_status'=> 'auto-draft',
                'fields'     => 'ids',
            ]),
        ]);

        if (empty($threads)) return;

        foreach ($threads as $thread) {
            $MJTC_ticketid = $ms_ticket_id;

            // Get user information
            $MJTC_userinfo = $this->getAwesomeSupportTicketCustomerInfo($thread->post_author);
            $MJTC_currentUserName = !empty($MJTC_userinfo['customer_name']) 
                ? esc_html($MJTC_userinfo['customer_name']) 
                : esc_html(__('Guest', 'majestic-support'));

            $MJTC_messagetype = __('Successfully', 'majestic-support');
            $MJTC_eventtype = '';
            $MJTC_message = '';

            if ($thread->post_type == 'ticket_history') {
                $MJTC_eventtype = MJTC_majesticsupportphplib::MJTC_strip_tags($thread->post_content);
                $MJTC_messageWithBreaks = MJTC_majesticsupportphplib::MJTC_preg_replace('/<\/[^>]+>/', "$0 ", $thread->post_content);
                $MJTC_message = MJTC_majesticsupportphplib::MJTC_strip_tags($MJTC_messageWithBreaks) . " " . __('by', 'majestic-support') . " ( $MJTC_currentUserName )";
            } elseif ($thread->post_type == 'ticket_reply') {
                $MJTC_eventtype = __('REPLIED_TICKET', 'majestic-support');
                $MJTC_message = __('Ticket is replied by', 'majestic-support') . " ( $MJTC_currentUserName )";
            }

            if (!empty($MJTC_eventtype) && !empty($MJTC_message)) {
                MJTC_includer::MJTC_getModel('tickethistory')->addActivityLog(
                    $MJTC_ticketid, 1, esc_html($MJTC_eventtype), esc_html($MJTC_message), esc_html($MJTC_messagetype)
                );
            }
        }
    }

    private function getAwesomeSupportTicketStaffTime($ms_ticket_id, $MJTC_ast_ticket_id, $ms_reply_id, $MJTC_as_reply_id) {
        $MJTC_as_reply_id = intval($MJTC_as_reply_id);
        $ms_ticket_id = intval($ms_ticket_id);
        if ($MJTC_as_reply_id <= 0 || $ms_ticket_id <= 0) return;

        // Get all timer logs for the given Awesome Support ticket
        
        $MJTC_query = new WP_Query( array(
            'post_type' => 'trackedtimes',
            'post_status' => 'publish',
            'posts_per_page' => -1
        ) );

        $MJTC_time_ids = wp_list_pluck( $MJTC_query->posts, 'ID' );
        $MJTC_duplicate_occurs = false;

        foreach( $MJTC_time_ids as $MJTC_id ) {
            $tracked_time = get_post_meta( $MJTC_id, 'as_time_tracking_entry' );

            if( !empty( $tracked_time ) ) {
                if( ( $MJTC_ast_ticket_id == $tracked_time[0]['ticket_id'] ) && ( $MJTC_as_reply_id == $tracked_time[0]['ticket_reply'] ) ) {

                    // Get HelpDesk staff ID from SupportCandy agent ID
                    // $MJTC_staffid = $this->getMSAgentIdByScAgentId($MJTC_timer->log_by);
                    // if (empty($MJTC_staffid)) continue;

                    $MJTC_created = $tracked_time[0]['start_date_time'];

                    // Handle and validate interval string
                    

                    $MJTC_timer_minutes = $tracked_time[0]['individual_time'];
                    if ($MJTC_timer_minutes <= 0) continue;

                    $MJTC_timer_seconds = $MJTC_timer_minutes * 60;

                    // Conflict detection
                    $MJTC_created_dt = new DateTime($MJTC_created);
                    $MJTC_now = new DateTime();
                    $MJTC_interval_to_now = $MJTC_created_dt->diff($MJTC_now);
                    $MJTC_systemtime = ($MJTC_interval_to_now->days * 86400) + ($MJTC_interval_to_now->h * 3600) + ($MJTC_interval_to_now->i * 60) + $MJTC_interval_to_now->s;

                    $MJTC_conflict = ($MJTC_timer_seconds > $MJTC_systemtime) ? 1 : 0;

                    // Prepare data
                    $MJTC_data = [
                        'staffid' => '',
                        'ticketid' => $ms_ticket_id,
                        'referencefor' => 1,
                        'referenceid' => $ms_reply_id,
                        'usertime' => $MJTC_timer_seconds,
                        'systemtime' => $MJTC_systemtime,
                        'conflict' => $MJTC_conflict,
                        'description' => '',
                        'timer_edit_desc' => '',
                        'status' => 1,
                        'created' => $MJTC_created
                    ];

                    $MJTC_row = MJTC_includer::MJTC_getTable('timetracking');
                    $MJTC_data = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($MJTC_data);

                    if (!$MJTC_row->bind($MJTC_data) || !$MJTC_row->store()) {
                        // optionally log or count the failure
                        continue;
                    }
                }
            }
        }
        return;
    }
    
    private function getTicketDepartmentIdByAwesomeSupport($MJTC_ticketId){
        // Validate and sanitize ticket ID
        $MJTC_ticketId = intval($MJTC_ticketId);
        if ($MJTC_ticketId <= 0) return null;

        // Fetch department from source table
        $MJTC_departmet_term = wp_get_object_terms($MJTC_ticketId, 'department');

        if (is_wp_error($MJTC_departmet_term) || empty($MJTC_departmet_term[0]->name)) return null;

        // Find corresponding department in destination table

        $MJTC_name = $MJTC_departmet_term[0]->name;
        
        $MJTC_query = "
            SELECT id FROM `" . majesticsupport::$_db->prefix . "mjtc_support_departments`
                WHERE LOWER(departmentname) = '".MJTC_majesticsupportphplib::MJTC_strtolower(MJTC_majesticsupportphplib::MJTC_trim(esc_sql($MJTC_name)))."'";
        $ms_department_id = majesticsupport::$_db->get_var($MJTC_query);
        
        return $ms_department_id ? (int)$ms_department_id : null;
    }

    private function getTicketPriorityIdByAwesomeSupport($MJTC_ticketId){
        // Sanitize and validate input
        $MJTC_ticketId = intval($MJTC_ticketId);
        if ($MJTC_ticketId <= 0) return null;

        // Fetch priority from source table
        $MJTC_priority_term = wp_get_object_terms($MJTC_ticketId, 'ticket_priority');

        if (is_wp_error($MJTC_priority_term) || empty($MJTC_priority_term[0]->name)) return null;

        // Find corresponding priority in destination table
        
        $MJTC_name = $MJTC_priority_term[0]->name;
        $MJTC_query = "
            SELECT id FROM `" . majesticsupport::$_db->prefix . "mjtc_support_priorities`
                WHERE LOWER(priority) = '".MJTC_majesticsupportphplib::MJTC_strtolower(MJTC_majesticsupportphplib::MJTC_trim(esc_sql($MJTC_name)))."'";;
        $ms_priority_id = majesticsupport::$_db->get_var($MJTC_query);
        
        return $ms_priority_id ? (int)$ms_priority_id : null;
    }

    private function getTicketStatusIdByAwesomeSupport($MJTC_ticket_status) {
        $MJTC_custom_status = wpas_get_post_status();
        if (empty($MJTC_ticket_status)) return null;

        if (empty($MJTC_custom_status[$MJTC_ticket_status])) return null;

        // Find matching status in destination table
        $MJTC_query = "SELECT id FROM `" . majesticsupport::$_db->prefix . "mjtc_support_statuses` WHERE LOWER(status) = '".MJTC_majesticsupportphplib::MJTC_strtolower(MJTC_majesticsupportphplib::MJTC_trim(esc_sql($MJTC_custom_status[$MJTC_ticket_status])))."'";
        $ms_status_id = majesticsupport::$_db->get_var($MJTC_query);

        return $ms_status_id ? (int)$ms_status_id : null;
    }

    private function getTicketAgentIdByAwesomeSupport($MJTC_ticketId){
        // Sanitize and validate input
        $MJTC_ticketId = intval($MJTC_ticketId);
        if ($MJTC_ticketId <= 0) return null;

        // Fetch product from source table
        $MJTC_assigned_agent = get_post_meta( $MJTC_ticketId, '_wpas_assignee', true );

        if (is_wp_error($MJTC_assigned_agent) || empty($MJTC_assigned_agent)) return null;

        $mjtc_user = MJTC_includer::MJTC_getObjectClass('user')->MJTC_getmajesticsupportuidbyuserid($MJTC_assigned_agent);
        if (!empty($mjtc_user) && isset($mjtc_user[0]->id)) {
            $mjtc_uid = (int)$mjtc_user[0]->id;
        } else {
            return;
        }
        
        $MJTC_query = "
            SELECT id FROM `" . majesticsupport::$_db->prefix . "mjtc_support_staff` WHERE uid = ".$mjtc_uid;
        $ms_agent_id = majesticsupport::$_db->get_var($MJTC_query);

        return $ms_agent_id ? (int)$ms_agent_id : null;
    }

    private function getTicketProductIdByAwesomeSupport($MJTC_ticketId){
        // Sanitize and validate input
        $MJTC_ticketId = intval($MJTC_ticketId);
        if ($MJTC_ticketId <= 0) return null;

        // Fetch product from source table
        $MJTC_product_term = wp_get_object_terms($MJTC_ticketId, 'product');

        if (is_wp_error($MJTC_product_term) || empty($MJTC_product_term[0]->name)) return null;

        // Find corresponding product in destination table
        
        $MJTC_name = $MJTC_product_term[0]->name;
        $MJTC_query = "
            SELECT id FROM `" . majesticsupport::$_db->prefix . "mjtc_support_products`
                WHERE LOWER(product) = '".MJTC_majesticsupportphplib::MJTC_strtolower(MJTC_majesticsupportphplib::MJTC_trim(esc_sql($MJTC_name)))."'";
        $ms_product_id = majesticsupport::$_db->get_var($MJTC_query);
        
        return $ms_product_id ? (int)$ms_product_id : null;
    }

    private function importAwesomeSupportFaqs(){
        // Load previously imported faqs
        $MJTC_imported_faqs = [];
        $MJTC_imported_faqs_json = get_option('mjtc_support_ticket_awesome_support_data_faqs');
        if (!empty($MJTC_imported_faqs_json)) {
            $MJTC_imported_faqs = json_decode($MJTC_imported_faqs_json, true);
        }

        // Get SupportCandy faqs (excluding system/default ones)
        $MJTC_faqs = get_posts( [
            'post_type'      => 'faq',
            'post_status'    => 'any', // includes all except 'auto-draft'
            'orderby'        => 'ID',
            'order'          => 'ASC',
            'numberposts'    => -1, // get all
            'exclude'        => get_posts([
                'post_type'   => 'wpass_status',
                'post_status'=> 'auto-draft',
                'fields'      => 'ids',
            ]),
        ] );

        if (empty($MJTC_faqs)) return;

        // Get highest current ordering value
        $MJTC_query = "
            SELECT MAX(faq.ordering)
            FROM `" . majesticsupport::$_db->prefix . "mjtc_support_faqs` AS faq
        ";
        $MJTC_ordering = (int) majesticsupport::$_db->get_var($MJTC_query);

        // Build array of existing JS faqs (cleaned)
        $MJTC_query = "
            SELECT faq.subject
            FROM `" . majesticsupport::$_db->prefix . "mjtc_support_faqs` AS faq
        ";
        $MJTC_jsfaqs = majesticsupport::$_db->get_results($MJTC_query);
        $MJTC_existing_faq_names = array_map(function($MJTC_faq) {
            return $this->cleanStringForCompare($MJTC_faq->subject);
        }, $MJTC_jsfaqs);

        foreach ($MJTC_faqs as $MJTC_faq) {
            $MJTC_name = $MJTC_faq->post_title;
            $MJTC_compare_name = $this->cleanStringForCompare($MJTC_name);

            // Skip if name already exists
            if (in_array($MJTC_compare_name, $MJTC_existing_faq_names)) {
                $this->awesome_support_import_count['faq']['skipped'] += 1;
                continue;
            }

            // Skip if already imported
            if (in_array($MJTC_faq->ID, $MJTC_imported_faqs)) {
                $this->awesome_support_import_count['faq']['skipped'] += 1;
                continue;
            }

            $taxonomies = get_object_taxonomies('faq');
            $terms = get_the_terms($MJTC_faq->ID, $taxonomies[0]);
            if(in_array('knowledgebase', majesticsupport::$_active_addons)) {
                $MJTC_categoryid = $this->getFaqCategoryIdByAwesomeSupport($terms[0]->name);
            } else {
                $MJTC_categoryid = '';
            }

            // Prepare new faq data
            $MJTC_row = MJTC_includer::MJTC_getTable('faq');
            $MJTC_data = [
                'id'            => '',
                'categoryid'    => $MJTC_categoryid,
                'staffid'       => 0,
                'subject'       => $MJTC_faq->post_title,
                'content'       => $MJTC_faq->post_content,
                'views'         => 0,
                'ordering'      => $MJTC_ordering,
                'created'       => $MJTC_faq->post_date,
                'status'        => 1,
                'visible'       => 0,
            ];

            $MJTC_data = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($MJTC_data);
            $MJTC_row->bind($MJTC_data);

            if (!$MJTC_row->store()) {
                $this->awesome_support_import_count['faq']['failed'] += 1;
            } else {
                $this->awesome_support_faq_ids[] = $MJTC_faq->id;
                $this->awesome_support_import_count['faq']['imported'] += 1;
                $MJTC_ordering++;
            }
        }

        // Save updated list of imported faqs
        if (!empty($this->awesome_support_faq_ids)) {
            update_option('mjtc_support_ticket_awesome_support_data_faqs', wp_json_encode($this->awesome_support_faq_ids));
        }
    }

    private function getFaqCategoryIdByAwesomeSupport($MJTC_name){
        $MJTC_query = "
            SELECT id FROM `" . majesticsupport::$_db->prefix . "mjtc_support_categories`
                WHERE LOWER(name) = '".MJTC_majesticsupportphplib::MJTC_strtolower(MJTC_majesticsupportphplib::MJTC_trim(esc_sql($MJTC_name)))."'";
        $ms_category_id = majesticsupport::$_db->get_var($MJTC_query);
        if (empty($ms_category_id)) {

            $MJTC_data['id'] = '';
            $MJTC_data['name'] = $MJTC_name;
            $MJTC_data['created'] = date_i18n('Y-m-d H:i:s');

            $MJTC_kb = '0';
            $MJTC_downloads = '0';
            $MJTC_announcement = '0';
            $MJTC_faqs = '1';

            $MJTC_data['kb'] = $MJTC_kb;
            $MJTC_data['downloads'] = $MJTC_downloads;
            $MJTC_data['announcement'] = $MJTC_announcement;
            $MJTC_data['faqs'] = $MJTC_faqs;
            $MJTC_data['staffid'] = 0;
            $MJTC_data['status'] = 1;

            $MJTC_row = MJTC_includer::MJTC_getTable('categories');

            $MJTC_data = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($MJTC_data);// remove slashes with quotes.
            $MJTC_error = 0;
            if (!$MJTC_row->bind($MJTC_data)) {
                $MJTC_error = 1;
            }
            if (!$MJTC_row->store()) {
                $MJTC_error = 1;
            }
            if (empty($MJTC_error)) {
                $ms_category_id = $MJTC_row->id;
            }
        }
        
        return $ms_category_id ? (int)$ms_category_id : null;
    }

    private function getPostConutByType ( $MJTC_post_type ) {
        $MJTC_counts = wp_count_posts( $MJTC_post_type );
        return isset( $MJTC_counts->publish ) ? (int) $MJTC_counts->publish : 0;
    }
    
    //================
    // --------------
    //////////////////
    // Fluent Support
    //////////////////
    // ---------------
    //================

    function importFluentSupportData() {
        // Only for development – remove before pushing to production
        $this->deletesupportcandyimporteddata();

        // Reset previously imported IDs from options
        update_option('mjtc_support_ticket_fluent_support_data_priorities', '');
        update_option('mjtc_support_ticket_fluent_support_data_users', '');
        update_option('mjtc_support_ticket_fluent_support_data_premades', '');
        update_option('mjtc_support_ticket_fluent_support_data_agents', '');
        update_option('mjtc_support_ticket_fluent_support_data_products', '');
        update_option('mjtc_support_ticket_fluent_support_data_tickets', '');
        
        // Prepare filesystem and create necessary directories
        require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
        require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';
        $MJTC_filesystem = new WP_Filesystem_Direct(true);
        $MJTC_upload_dir = wp_upload_dir();
        $MJTC_upload_path = $MJTC_upload_dir['basedir'];
        $MJTC_datadirectory = majesticsupport::$_config['data_directory'];
        $MJTC_path = $MJTC_upload_path . "/" . $MJTC_datadirectory;

        if (!$MJTC_filesystem->exists($MJTC_path)) {
            wp_mkdir_p($MJTC_path);
        }
        $MJTC_path .= '/attachmentdata';
        if (!$MJTC_filesystem->exists($MJTC_path)) {
            wp_mkdir_p($MJTC_path);
        }
        $MJTC_path .= '/ticket';
        if (!$MJTC_filesystem->exists($MJTC_path)) {
            wp_mkdir_p($MJTC_path);
        }

        if (majesticsupport::$_db->get_var("SHOW TABLES LIKE '" . majesticsupport::$_db->prefix . "fs_persons'")) {
            $this->importFluentSupportUsers();
        }

        if (majesticsupport::$_db->get_var("SHOW TABLES LIKE '" . majesticsupport::$_db->prefix . "fs_persons'")) {
            $this->importFluentSupportAgents();
        }

        $this->importFluentSupportPriorities();

        if (majesticsupport::$_db->get_var("SHOW TABLES LIKE '" . majesticsupport::$_db->prefix . "fs_saved_replies'")) {
            $this->importFluentSupportPremades();
        }
        if (majesticsupport::$_db->get_var("SHOW TABLES LIKE '" . majesticsupport::$_db->prefix . "fs_products'")) {
            $this->importFluentSupportProducts();
        }
        if (majesticsupport::$_db->get_var("SHOW TABLES LIKE '" . majesticsupport::$_db->prefix . "fs_meta'")) {
            $this->importFluentSupportTicketFields();
        }
        if (majesticsupport::$_db->get_var("SHOW TABLES LIKE '" . majesticsupport::$_db->prefix . "fs_tickets'")) {
            $this->getFluentSupportTickets( );
        }

        update_option('mjtc_import_counts',$this->fluent_support_import_count);
        return;
    }

    private function getFluentSupportTickets() {
        // Check if tickets already processed for import
        $MJTC_imported_tickets = array();
        $MJTC_imported_tickets_json = get_option('mjtc_support_ticket_fluent_support_data_tickets');
        if (!empty($MJTC_imported_tickets_json)) {
            $MJTC_imported_tickets = json_decode($MJTC_imported_tickets_json, true);
        }

        $MJTC_query = "SELECT tickets.*
                FROM `" . majesticsupport::$_db->prefix . "fs_tickets` AS tickets
                ORDER BY tickets.id ASC";
        
        $MJTC_tickets = majesticsupport::$_db->get_results($MJTC_query);
        
        $MJTC_new_tickets = array();
        foreach ($MJTC_tickets as $MJTC_ticket) {
            // Skip if ticket already imported
            if (!empty($MJTC_imported_tickets) && in_array($MJTC_ticket->id, $MJTC_imported_tickets)) {
                $this->fluent_support_import_count['ticket']['skipped'] += 1;
                continue;
            }

            // Map custom fields
            $MJTC_params = array();
            $MJTC_query = "SELECT meta.*
                        FROM `" . majesticsupport::$_db->prefix . "fs_meta` AS meta
                        WHERE object_type = 'ticket_meta'
                        AND object_id = ".$MJTC_ticket->id.";";
            $MJTC_tickets_meta = majesticsupport::$_db->get_results($MJTC_query);
            foreach($MJTC_tickets_meta as $MJTC_ticket_meta){
                foreach ($this->fc_ticket_cf as $MJTC_fs_ticket_custom_field => $mjtc_support_custom_field) {
                    if($MJTC_ticket_meta->key == $MJTC_fs_ticket_custom_field){
                        $MJTC_custom_field_value = "";
                        $MJTC_custom_field_value = $MJTC_ticket_meta->value;
                        $MJTC_custom_field_value = MJTC_majesticsupportphplib::MJTC_str_replace("|","",$MJTC_custom_field_value);
                        $MJTC_custom_field_value = MJTC_majesticsupportphplib::MJTC_str_replace("|","",$MJTC_custom_field_value);
                        $MJTC_vardata = "";
                        
                        $MJTC_fieldtype = $this->checkTypeOfTheField($MJTC_fs_ticket_custom_field);
                        if($MJTC_fieldtype == "date"){
                            $MJTC_vardata = gmdate("Y-m-d", MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_custom_field_value));
                        }else{
                            $MJTC_vardata = $MJTC_custom_field_value;
                        }
                        if($MJTC_vardata != ''){
                            if(is_array($MJTC_vardata)){
                                $MJTC_vardata = implode(', ', array_filter($MJTC_vardata));
                            }
                            $MJTC_params[$mjtc_support_custom_field] = MJTC_majesticsupportphplib::MJTC_htmlentities($MJTC_vardata);
                        }
                    }
                }
            }
            $MJTC_ticketparams = html_entity_decode(wp_json_encode($MJTC_params, JSON_UNESCAPED_UNICODE));

            // Get linked data
            $MJTC_userinfo = $this->getFluentSupportTicketCustomerInfo($MJTC_ticket->customer_id);
            $MJTC_agentid = $this->getTicketAgentIdByFluentSupport($MJTC_ticket->agent_id);
            $MJTC_productid = $this->getTicketProductIdByFluentSupport($MJTC_ticket->product_id);
            $MJTC_priorityid = $this->getTicketPriorityIdByFluentSupport($MJTC_ticket->client_priority);

            $MJTC_idresult = MJTC_includer::MJTC_getModel('ticket')->getRandomTicketId();
            $MJTC_ticketid = $MJTC_idresult['ticketid'];
            $MJTC_customticketno = $MJTC_idresult['customticketno'];
            $MJTC_attachmentdir = MJTC_includer::MJTC_getModel('ticket')->getRandomFolderName();

            // Determine ticket status
            $MJTC_ticket_status = 1;
            if($MJTC_ticket->status == "new") $MJTC_ticket_status = 1;
            elseif($MJTC_ticket->status == "active"){
                $MJTC_ticket_status = 2;
                if(MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->last_agent_response) == MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->waiting_since)){
                    $MJTC_ticket_status = 4;
                }
                if(MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->last_customer_response) == MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->waiting_since)){
                    $MJTC_ticket_status = 2;
                }
            }elseif($MJTC_ticket->status == "closed") $MJTC_ticket_status = 5;

            $MJTC_isanswered = ($MJTC_ticket_status == 4) ? 1 : 0;

            $MJTC_ticket_closed = "0000-00-00 00:00:00";
            $MJTC_ticket_closedby = "";
            if ($MJTC_ticket->resolved_at && $MJTC_ticket->resolved_at != '0000-00-00 00:00:00' && $MJTC_ticket->closed_by) {
                $MJTC_ticket_closed = $MJTC_ticket->resolved_at;
                $MJTC_ticket_closedby =$MJTC_ticket->closed_by;
            }
            // Ticket Default Status
            // 1 -> New Ticket
            // 2 -> Waiting admin/staff reply
            // 3 -> in progress
            // 4 -> waiting for customer reply
            // 5 -> close ticket

            $MJTC_newTicketData = [
                'id' => "",
                'uid' => $MJTC_userinfo["ms_uid"],
                'ticketid' => $MJTC_ticketid,
                'productid' => $MJTC_productid,
                'priorityid' => $MJTC_priorityid,
                'staffid' => $MJTC_agentid,
                'email' => $MJTC_userinfo["customer_email"],
                'name' => $MJTC_userinfo["customer_name"],
                'subject' => $MJTC_ticket->title,
                'message' => $MJTC_ticket->content,
                'helptopicid' => 0,
                'multiformid' => 1,
                'phone' => "",
                'phoneext' => "",
                'status' => $MJTC_ticket_status,
                'isoverdue' => "0",
                'isanswered' => $MJTC_isanswered,
                'duedate' => "0000-00-00 00:00:00",
                'reopened' => "0000-00-00 00:00:00",
                'closed' => $MJTC_ticket_closed,
                'closedby' => $MJTC_ticket_closedby,
                'lastreply' => $MJTC_ticket->waiting_since,
                'created' => $MJTC_ticket->created_at,
                'updated' => $MJTC_ticket->updated_at,
                'lock' => "0",
                'ticketviaemail' => "0",
                'ticketviaemail_id' => "0",
                'attachmentdir' => $MJTC_attachmentdir,
                'feedbackemail' => "0",
                'mergestatus' => "0",
                'mergewith' => "0",
                'mergenote' => "",
                'mergedate' => "0000-00-00 00:00:00",
                'multimergeparams' => "",
                'mergeuid' => "0",
                'params' => $MJTC_ticketparams,
                'hash' => "",
                'notificationid' => "0",
                'wcorderid' => "0",
                'wcitemid' => "0",
                'wcproductid' => "0",
                'eddorderid' => "0",
                'eddproductid' => "0",
                'eddlicensekey' => "",
                'envatodata' => "",
                'paidsupportitemid' => "0",
                'customticketno' => $MJTC_customticketno
            ];

            $MJTC_row = MJTC_includer::MJTC_getTable('tickets');
            $MJTC_error = 0;
            if (!$MJTC_row->bind($MJTC_newTicketData)) $MJTC_error = 1;
            if (!$MJTC_row->store()) $MJTC_error = 1;

            if ($MJTC_error == 1) {
                $this->fluent_support_import_count['ticket']['failed'] += 1;
            } else {
                $this->fluent_support_ticket_ids[] = $MJTC_ticket->id;
                $this->fluent_support_import_count['ticket']['imported'] += 1;

                $ms_ticketid = $MJTC_row->id;
                $MJTC_hash = MJTC_includer::MJTC_getModel('ticket')->generateHash($ms_ticketid);
                $MJTC_query = "UPDATE `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` SET `hash`='" . esc_sql($MJTC_hash) . "' WHERE id=" . esc_sql($ms_ticketid);
                majesticsupport::$_db->query($MJTC_query);

                if(in_array('note', majesticsupport::$_active_addons)){
                    $this->getFluentSupportTicketNotes($ms_ticketid, $MJTC_ticket->id, $MJTC_attachmentdir);
                }
                $this->getFluentSupportTicketReplies($ms_ticketid, $MJTC_ticket->id, $MJTC_attachmentdir);
                $this->getFluentSupportTicketAttachments($ms_ticketid, $MJTC_ticket->id, $MJTC_attachmentdir);

                if (in_array('tickethistory', majesticsupport::$_active_addons)) {
                    $this->getFluentSupportTicketActivityLog($ms_ticketid, $MJTC_ticket->id);
                }

                if (in_array('timetracking', majesticsupport::$_active_addons) && majesticsupport::$_db->get_var("SHOW TABLES LIKE '" . majesticsupport::$_db->prefix . "fs_time_tracks'")) {
                    $this->getFluentSupportTicketStaffTime($ms_ticketid, $MJTC_ticket->id);
                }
            }
        }

        if (!empty($this->fluent_support_ticket_ids)) {
            update_option('mjtc_support_ticket_fluent_support_data_tickets', wp_json_encode($this->fluent_support_ticket_ids));
        }
    }

    private function importFluentSupportTicketFields() {
        // Get all ticket-related custom fields
        $MJTC_query = "
            SELECT * FROM `" . majesticsupport::$_db->prefix . "fs_meta`
            WHERE object_type = 'option' AND `key` = '_ticket_custom_fields';";
        $MJTC_custom_fields_serializeed = majesticsupport::$_db->get_row($MJTC_query);

        if (!$MJTC_custom_fields_serializeed) return;

        $MJTC_custom_fields = unserialize($MJTC_custom_fields_serializeed->value);
        

        if (!$MJTC_custom_fields) return;

        $this->fc_ticket_cf = [];


        foreach ($MJTC_custom_fields as $MJTC_custom_field) {
            // Map field types
            switch ($MJTC_custom_field["type"]){
                case "text":
                    $MJTC_fieldtype = "text"; break;
                case "select-one":
                    $MJTC_fieldtype = "combo"; break;
                case "radio":
                    $MJTC_fieldtype = "radio"; break;
                case "checkbox":
                    $MJTC_fieldtype = "checkbox"; break;
                case "textarea":
                    $MJTC_fieldtype = "textarea"; break;
                case "number":
                    $MJTC_fieldtype = "text"; break;
                default:
                    $MJTC_fieldtype = "text"; break;
            }

            $MJTC_query = "SELECT id,field FROM `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` WHERE isuserfield = 1 AND LOWER(fieldtitle) ='".esc_sql(MJTC_majesticsupportphplib::MJTC_strtolower($MJTC_custom_field['label']))."' AND userfieldtype ='".esc_sql($MJTC_fieldtype)."' AND fieldfor = 1";
            $MJTC_field_record = majesticsupport::$_db->get_row($MJTC_query);

            if(!empty($MJTC_field_record)){ // this will make sure
                $this->fluent_support_import_count['field']['skipped'] += 1;
                continue;
            }

            // Load options for select-type fields
            $MJTC_option_values = [];
            if(isset($MJTC_custom_field["options"])){
                foreach($MJTC_custom_field["options"] as $MJTC_key => $MJTC_value){
                    $MJTC_option_values[] = $MJTC_value;
                }
            }
            // required
            $MJTC_required = 0;
            if(isset($MJTC_custom_field["required"])){
                if($MJTC_custom_field["required"] == "yes") $MJTC_required = 1;
            }
            // admin olny
            $MJTC_adminonly = 0;
            if(isset($MJTC_custom_field["admin_only"])){
                if($MJTC_custom_field["admin_only"] == "yes") $MJTC_adminonly = 1;
            }
            // placeholder
            $MJTC_placeholder = '';
            if(isset($MJTC_custom_field["placeholder"])){
                $MJTC_placeholder = $MJTC_custom_field["placeholder"];
            }

            // Build visibility data
            $MJTC_visibledata = [
                "visibleLogic" => [],
                "visibleParent" => [],
                "visibleValue" => [],
                "visibleCondition" => [],
            ];

            // Prepare field data for import
            $MJTC_fieldOrderingData = [
                "id" => "",
                "field" => $MJTC_custom_field["slug"],
                "fieldtitle" => $MJTC_custom_field['label'],
                "ordering" => "",
                "section" => "10",
                "fieldfor" => "1",
                "published" => "1",
                "sys" => "0",
                "cannotunpublish" => "0",
                "required" => $MJTC_required,
                "size" => "100",
                "maxlength" => "255",
                "cols" => "",
                "rows" => "",
                "isuserfield" => "1",
                "userfieldtype" => $MJTC_fieldtype,
                "depandant_field" => "",
                "visible_field" => "",
                "showonlisting" => "0",
                "cannotshowonlisting" => "0",
                "search_user" => "0",
                "cannotsearch" => "0",
                "isvisitorpublished" => "1",
                "userfieldparams" => "",
                "multiformid" => "1",
                "visibleparams" => "",
                "values" => $MJTC_option_values,
                "visibleParent" => $MJTC_visibledata["visibleParent"],
                "visibleValue" => $MJTC_visibledata["visibleValue"],
                "visibleCondition" => $MJTC_visibledata["visibleCondition"],
                "visibleLogic" => $MJTC_visibledata["visibleLogic"],
                "placeholder" => $MJTC_placeholder,
                "description" => '',
                "defaultvalue" => '',
                "readonly" => '',
                "adminonly" => $MJTC_adminonly,
            ];

            // Store field in SupportCandy
            $MJTC_record_saved = MJTC_includer::MJTC_getModel('fieldordering')->storeUserField($MJTC_fieldOrderingData);

            if ($MJTC_record_saved == 1) {
                $MJTC_query = "SELECT * FROM `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` ORDER BY id DESC LIMIT 1";
                $MJTC_latest_record = majesticsupport::$_db->get_row($MJTC_query);
                $this->fc_ticket_cf[$MJTC_custom_field["slug"]] = $MJTC_latest_record->field;

                $this->fluent_support_import_count['field']['imported'] += 1;
            } else {
                $this->fluent_support_import_count['field']['failed'] += 1;
            }
        }

        foreach ($MJTC_custom_fields as $MJTC_custom_field) {
            $MJTC_field = $this->getTicketCustomFieldId($MJTC_custom_field['label']);
            $MJTC_query = "SELECT * FROM `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` WHERE field = '".esc_sql($MJTC_field)."' LIMIT 1";
            $ms_field = majesticsupport::$_db->get_row($MJTC_query);
            if (empty($ms_field)) {
                continue;
            }
            

            // Build visibility data
            $MJTC_visibledata = [
                "visibleLogic" => [],
                "visibleParent" => [],
                "visibleValue" => [],
                "visibleCondition" => [],
            ];

            
            if (!empty($MJTC_custom_field['conditions'])) {
                $MJTC_visibleLogic = 'AND';
                if(isset($MJTC_custom_field["match_type"]) && $MJTC_custom_field["match_type"] == 'any'){
                    $MJTC_visibleLogic = 'OR';
                }

                foreach ($MJTC_custom_field['conditions'] as $MJTC_groupIndex => $MJTC_group) {
                    $MJTC_fieldtype = '';
                    if ($MJTC_group['item_key'] == 'ticket_content' || $MJTC_group['item_key'] == 'ticket_product_id') {
                        continue;
                    }
                    if ($MJTC_group['item_key'] == 'ticket_client_priority') {
                        $MJTC_item_key = 'priority';
                        $MJTC_group['value'];
                        $MJTC_value = $this->getTicketPriorityIdByFluentSupport($MJTC_group['value']);
                        $MJTC_fieldtype = 'priority';
                    } elseif ($MJTC_group['item_key'] == 'ticket_title') {
                        $MJTC_item_key = 'subject';
                        $MJTC_value = $MJTC_group['value'];
                        $MJTC_fieldtype = 'subject';
                    } else {
                        // $MJTC_item_key = $MJTC_group['item_key'];
                        if (empty($MJTC_group['item_key'])) {
                            continue;
                        }
                        $MJTC_item_key = $this->fc_ticket_cf[$MJTC_group['item_key']];
                        $MJTC_fieldtype = $this->checkTypeOfTheField($MJTC_item_key);
                        if ($MJTC_fieldtype == 'textarea') {
                            continue;
                        }
                        $MJTC_value = $MJTC_group['value'];
                    }
                    if ($MJTC_custom_field["slug"] == 'ticket_client_priority') {
                        $MJTC_slug = 'priority';
                    } elseif ($MJTC_custom_field["slug"] == 'ticket_title') {
                        $MJTC_slug = 'subject';
                    } else {
                        $MJTC_slug = $this->fc_ticket_cf[$MJTC_custom_field["slug"]];
                    }
                    
                    $MJTC_visibledata["visibleParentField"][] = $MJTC_slug;
                    $MJTC_visibledata["visibleParent"][] = $MJTC_item_key;
                    $MJTC_visibledata["visibleCondition"][] = $this->mapOperatorToConditionCodeForFluentSupport($MJTC_group['operator'], $MJTC_fieldtype);
                    $MJTC_visibledata["visibleValue"][] = $MJTC_value;
                    $MJTC_visibledata["visibleLogic"][] = $MJTC_visibleLogic;
                }
            }

            $MJTC_option_values = [];
            if(isset($MJTC_custom_field["options"])){
                foreach($MJTC_custom_field["options"] as $MJTC_key => $MJTC_value){
                    $MJTC_option_values[] = $MJTC_value;
                }
            }

            // Prepare field data for import

            $MJTC_fieldOrderingData = [
                "id" => $ms_field->id,
                "field" => $ms_field->field,
                "fieldtitle" => $ms_field->fieldtitle,
                "ordering" => $ms_field->ordering,
                "section" => $ms_field->section,
                "placeholder" => $ms_field->placeholder,
                "description" => $ms_field->description,
                "fieldfor" => $ms_field->fieldfor,
                "published" => $ms_field->published,
                "sys" => $ms_field->sys,
                "cannotunpublish" => $ms_field->cannotunpublish,
                "required" => $ms_field->required,
                "size" => $ms_field->size,
                "maxlength" => $ms_field->maxlength,
                "cols" => $ms_field->cols,
                "rows" => $ms_field->rows,
                "isuserfield" => $ms_field->isuserfield,
                "userfieldtype" => $ms_field->userfieldtype,
                "depandant_field" => $ms_field->depandant_field,
                "visible_field" => $ms_field->visible_field,
                "showonlisting" => $ms_field->showonlisting,
                "cannotshowonlisting" => $ms_field->cannotshowonlisting,
                "search_user" => $ms_field->search_user,
                "search_admin" => $ms_field->search_admin,
                "cannotsearch" => $ms_field->cannotsearch,
                "isvisitorpublished" => $ms_field->isvisitorpublished,
                "multiformid" => $ms_field->multiformid,
                "userfieldparams" => $ms_field->userfieldparams,
                "visibleparams" => $ms_field->visibleparams,
                "readonly" => $ms_field->readonly,
                "adminonly" => $ms_field->adminonly,
                "defaultvalue" => $ms_field->defaultvalue,
                "values" => $MJTC_option_values,
                "visibleParent" => $MJTC_visibledata["visibleParent"],
                "visibleValue" => $MJTC_visibledata["visibleValue"],
                "visibleCondition" => $MJTC_visibledata["visibleCondition"],
                "visibleLogic" => $MJTC_visibledata["visibleLogic"],
            ];

            // Store field in SupportCandy
            $MJTC_record_saved = MJTC_includer::MJTC_getModel('fieldordering')->storeUserField($MJTC_fieldOrderingData);
        }
    }

    private function checkTypeOfTheField($MJTC_field){
        
        $MJTC_query = "
            SELECT userfieldtype FROM `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering`
                WHERE LOWER(field) = '".MJTC_majesticsupportphplib::MJTC_strtolower(MJTC_majesticsupportphplib::MJTC_trim(esc_sql($MJTC_field)))."'";
        $MJTC_userfieldtype = majesticsupport::$_db->get_var($MJTC_query);
        
        return $MJTC_userfieldtype ? $MJTC_userfieldtype : null;
    }

    private function getTicketCustomFieldId($MJTC_fieldtitle){
        
        $MJTC_query = "
            SELECT field FROM `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering`
                WHERE LOWER(fieldtitle) = '".MJTC_majesticsupportphplib::MJTC_strtolower(MJTC_majesticsupportphplib::MJTC_trim(esc_sql($MJTC_fieldtitle)))."'";
        $ms_field_id = majesticsupport::$_db->get_var($MJTC_query);
        
        return $ms_field_id ? $ms_field_id : null;
    }

    private function mapOperatorToConditionCodeForFluentSupport ($MJTC_operator, $type) {
        $MJTC_operator = strtoupper(MJTC_majesticsupportphplib::MJTC_trim($MJTC_operator));
        $MJTC_isComplex = false;

        if (!empty($type)) {
            $MJTC_complexTypes = ['combo', 'checkbox', 'radio', 'multiple','priority'];
            $MJTC_isComplex = !in_array($type, $MJTC_complexTypes);
        }

        switch (strtoupper($MJTC_operator)) {
            case '=':
            case 'CONTAINS':
                return $MJTC_isComplex ? "2" : "1";
            case '!=':
            case 'NOT_CONTAINS':
                return $MJTC_isComplex ? "3" : "0";
            default:
                return $MJTC_isComplex ? "4" : "0"; // Fallback or unsupported operator
        }
    }

    private function getFluentSupportTicketNotes($ms_ticket_id, $MJTC_fs_ticket_id, $MJTC_attachmentdir){
        $MJTC_query = "SELECT conversation.*
                    FROM `" . majesticsupport::$_db->prefix . "fs_conversations` AS conversation
                    WHERE conversation.ticket_id = ".$MJTC_fs_ticket_id."
                    AND conversation.conversation_type = 'note'
                    ORDER BY conversation.id ASC";
                    
        $MJTC_conversations = majesticsupport::$_db->get_results($MJTC_query);
        foreach($MJTC_conversations AS $MJTC_conversation){

            $MJTC_query = "
            SELECT id FROM `" . majesticsupport::$_db->prefix . "mjtc_support_users` WHERE wpuid = ".$MJTC_conversation->person_id;
            $MJTC_agentid = $ms_user_id = majesticsupport::$_db->get_var($MJTC_query);
            $MJTC_filename = $this->getFluentSupportNoteAttachments($MJTC_fs_ticket_id, $MJTC_conversation->id, $MJTC_attachmentdir);

            $MJTC_replyData = [
                "id" => "",
                "ticketid" => $ms_ticket_id,
                "staffid" => $MJTC_agentid,
                "title" => MJTC_majesticsupportphplib::MJTC_strip_tags($MJTC_conversation->content),
                "note" => $MJTC_conversation->content,
                "status" => "1",
                "created" => $MJTC_conversation->created_at,
                "filename" => $MJTC_filename,
                "filesize" => 5334
            ];
            $MJTC_row = MJTC_includer::MJTC_getTable('note');
            $MJTC_data = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($MJTC_replyData);// remove slashes with quotes.
            $MJTC_error = 0;
            if (!$MJTC_row->bind($MJTC_data)) {
                $MJTC_error = 1;
            }
            if (!$MJTC_row->store()) {
                $MJTC_error = 1;
            }
            $ms_ticket_note_id = $MJTC_row->id;
        }
    }

    private function getFluentSupportTicketReplies($ms_ticket_id, $MJTC_fs_ticket_id, $MJTC_attachmentdir){
        $MJTC_query = "SELECT conversation.*
                    FROM `" . majesticsupport::$_db->prefix . "fs_conversations` AS conversation
                    WHERE conversation.ticket_id = ".$MJTC_fs_ticket_id."
                    AND conversation.conversation_type = 'response'
                    ORDER BY conversation.id ASC";
                    
        $MJTC_conversations = majesticsupport::$_db->get_results($MJTC_query);
        foreach($MJTC_conversations AS $MJTC_conversation){
            $MJTC_userinfo = $this->getFluentSupportTicketCustomerInfo($MJTC_conversation->person_id);
            $MJTC_uid = $MJTC_userinfo["ms_uid"];
            $MJTC_name = $MJTC_userinfo["customer_name"];
            if(empty($MJTC_userinfo["ms_uid"])){

                $MJTC_agentid = $this->getTicketAgentIDByFluentSupport($MJTC_conversation->person_id);
                if($MJTC_agentid){
                    $MJTC_query = "SELECT agent.*
                                FROM `" . majesticsupport::$_db->prefix . "mjtc_support_staff` AS agent
                                WHERE agent.id = ".$MJTC_agentid.";";
                                
                    $MJTC_agent = majesticsupport::$_db->get_row($MJTC_query);
                    $MJTC_uid = $MJTC_agent->uid;
                    $MJTC_name = $MJTC_agent->firstname;
                    if($MJTC_agent->lastname) $MJTC_name = $MJTC_name. " ". $MJTC_agent->lastname;
                }
            }

            $MJTC_replyData = [
                "id" => "",
                "uid" => $MJTC_uid,
                "ticketid" => $ms_ticket_id,
                "name" => $MJTC_name,
                "message" => $MJTC_conversation->content,
                "staffid" => "",
                "rating" => "",
                "status" => "1",
                "created" => $MJTC_conversation->created_at,
                "ticketviaemail" => "",
                "viewed_by" => "",
                "viewed_on" => ""
            ];
            $MJTC_row = MJTC_includer::MJTC_getTable('replies');
            $MJTC_data = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($MJTC_replyData);// remove slashes with quotes.
            $MJTC_error = 0;
            if (!$MJTC_row->bind($MJTC_data)) {
                $MJTC_error = 1;
            }
            if (!$MJTC_row->store()) {
                $MJTC_error = 1;
            }
            $ms_ticket_reply_id = $MJTC_row->id;
            $this->getFluentSupportReplyAttachments($ms_ticket_id, $ms_ticket_reply_id, $MJTC_fs_ticket_id, $MJTC_conversation->id, $MJTC_attachmentdir);
        }
    }

    private function getFluentSupportNoteAttachments($MJTC_fs_ticket_id, $MJTC_fs_ticket_reply_id, $MJTC_attachmentdir){
        $MJTC_query = "SELECT attachment.*
                    FROM `" . majesticsupport::$_db->prefix . "fs_attachments` AS attachment
                    WHERE attachment.ticket_id = " . (int)$MJTC_fs_ticket_id . " AND attachment.conversation_id = " . (int)$MJTC_fs_ticket_reply_id . "
                    ORDER BY attachment.id ASC";
                    
        $MJTC_attachment = majesticsupport::$_db->get_row($MJTC_query);

        if (empty($MJTC_attachment)) return;

        // --- START FILESYSTEM FIX ---
        global $wp_filesystem;
        if (!function_exists('wp_handle_upload')) {
            do_action('majesticsupport_load_wp_file');
        }
        if ( ! WP_Filesystem() ) {
            return false;
        }
        $MJTC_filesystem = $wp_filesystem;
        // --- END FILESYSTEM FIX ---

        $MJTC_upload_dir = wp_upload_dir();
        $MJTC_upload_path = $MJTC_upload_dir['basedir'];
        $MJTC_datadirectory = majesticsupport::$_config['data_directory'];
        $MJTC_path = trailingslashit($MJTC_upload_path) . $MJTC_datadirectory . "/attachmentdata/ticket/" . sanitize_file_name($MJTC_attachmentdir);

        if (!$MJTC_filesystem->exists($MJTC_path)) {
            wp_mkdir_p($MJTC_path);
        }

        $MJTC_safe_filename = sanitize_file_name($MJTC_attachment->title);
        $MJTC_source = $MJTC_attachment->file_path;
        $MJTC_destination = $MJTC_path . "/" . $MJTC_safe_filename;
        
        $MJTC_destination_new_name = $MJTC_path . "/" . $MJTC_attachment->title;

        // Replaced file_exists with $MJTC_filesystem->exists
        if (!$MJTC_filesystem->exists($MJTC_source)) {
            return '';
        }

        $MJTC_result = $MJTC_filesystem->copy($MJTC_source, $MJTC_destination, true);
        if (!$MJTC_result) {
            return '';
        }
        
        // Replaced rename with $MJTC_filesystem->move
        $MJTC_filesystem->move($MJTC_destination, $MJTC_destination_new_name, true);

        return $MJTC_safe_filename;
    }

    private function getFluentSupportReplyAttachments($ms_ticket_id, $ms_ticket_reply_id, $MJTC_fs_ticket_id, $MJTC_fs_ticket_reply_id, $MJTC_attachmentdir){
        $MJTC_query = "SELECT attachment.*
                    FROM `" . majesticsupport::$_db->prefix . "fs_attachments` AS attachment
                    WHERE attachment.ticket_id = " . (int)$MJTC_fs_ticket_id . " AND attachment.conversation_id = " . (int)$MJTC_fs_ticket_reply_id . "
                    ORDER BY attachment.id ASC";
                    
        $MJTC_attachments = majesticsupport::$_db->get_results($MJTC_query);

        if (empty($MJTC_attachments)) return;

        // --- START FILESYSTEM FIX ---
        global $wp_filesystem;
        if (!function_exists('wp_handle_upload')) {
            do_action('majesticsupport_load_wp_file');
        }
        if ( ! WP_Filesystem() ) {
            return false;
        }
        $MJTC_filesystem = $wp_filesystem;
        // --- END FILESYSTEM FIX ---

        $MJTC_upload_dir = wp_upload_dir();
        $MJTC_upload_path = $MJTC_upload_dir['basedir'];
        $MJTC_datadirectory = majesticsupport::$_config['data_directory'];
        $MJTC_path = trailingslashit($MJTC_upload_path) . $MJTC_datadirectory . "/attachmentdata/ticket/" . sanitize_file_name($MJTC_attachmentdir);

        if (!$MJTC_filesystem->exists($MJTC_path)) {
            wp_mkdir_p($MJTC_path);
        }

        foreach ($MJTC_attachments as $MJTC_attachment) {
            $MJTC_safe_filename = sanitize_file_name($MJTC_attachment->title);
            $MJTC_source = $MJTC_attachment->file_path;
            $MJTC_destination = $MJTC_path . "/" . $MJTC_safe_filename;
            $MJTC_destination_new_name = $MJTC_path . "/" . $MJTC_attachment->title;
            
            $MJTC_attachmentData = [
                "id" => "",
                "ticketid" => $ms_ticket_id,
                "replyattachmentid" => $ms_ticket_reply_id,
                "filesize" => "", // Optionally: filesize($MJTC_source)
                "filename" => $MJTC_safe_filename,
                "filekey" => "",
                "deleted" => "",
                "status" => "1",
                "created" => $MJTC_attachment->created_at
            ];

            $MJTC_row = MJTC_includer::MJTC_getTable('attachments');
            $MJTC_data = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($MJTC_attachmentData);

            if (!$MJTC_row->bind($MJTC_data) || !$MJTC_row->store()) {
                continue; 
            }

            // Replaced file_exists with $MJTC_filesystem->exists
            if (!$MJTC_filesystem->exists($MJTC_source)) {
                die( 'Attachment source file does not exist: ' . esc_html( $MJTC_source ) );
                continue;
            }

            $MJTC_result = $MJTC_filesystem->copy($MJTC_source, $MJTC_destination, true);
            if (!$MJTC_result) {
                die( 'Failed to copy attachment from ' . esc_html( $MJTC_source ) . ' to ' . esc_html( $MJTC_destination ) );
            } else {
                // Replaced rename with $MJTC_filesystem->move
                $MJTC_filesystem->move($MJTC_destination, $MJTC_destination_new_name, true);
            }         
        }
    }

    private function getFluentSupportTicketAttachments($ms_ticket_id, $MJTC_fs_ticket_id, $MJTC_attachmentdir){
        $MJTC_query = "SELECT attachment.*
                    FROM `" . majesticsupport::$_db->prefix . "fs_attachments` AS attachment
                    WHERE attachment.ticket_id = " . (int)$MJTC_fs_ticket_id . " AND attachment.conversation_id  IS NULL
                    ORDER BY attachment.id ASC";
                    
        $MJTC_attachments = majesticsupport::$_db->get_results($MJTC_query);

        if (empty($MJTC_attachments)) return;

        // --- START FILESYSTEM FIX ---
        global $wp_filesystem;
        if (!function_exists('wp_handle_upload')) {
            do_action('majesticsupport_load_wp_file');
        }
        if ( ! WP_Filesystem() ) {
            return false;
        }
        $MJTC_filesystem = $wp_filesystem;
        // --- END FILESYSTEM FIX ---

        $MJTC_upload_dir = wp_upload_dir();
        $MJTC_upload_path = $MJTC_upload_dir['basedir'];
        $MJTC_datadirectory = majesticsupport::$_config['data_directory'];
        $MJTC_path = trailingslashit($MJTC_upload_path) . $MJTC_datadirectory . "/attachmentdata/ticket/" . sanitize_file_name($MJTC_attachmentdir);

        if (!$MJTC_filesystem->exists($MJTC_path)) {
            wp_mkdir_p($MJTC_path);
        }

        foreach ($MJTC_attachments as $MJTC_attachment) {
            $MJTC_safe_filename = sanitize_file_name($MJTC_attachment->title);
            $MJTC_source = $MJTC_attachment->file_path;
            $MJTC_destination = $MJTC_path . "/" . $MJTC_safe_filename;
            $MJTC_destination_new_name = $MJTC_path . "/" . $MJTC_attachment->title;
            
            $MJTC_attachmentData = [
                "id" => "",
                "ticketid" => $ms_ticket_id,
                "replyattachmentid" => 0,
                "filesize" => "", // Optionally: filesize($MJTC_source)
                "filename" => $MJTC_safe_filename,
                "filekey" => "",
                "deleted" => "",
                "status" => "1",
                "created" => $MJTC_attachment->created_at
            ];

            $MJTC_row = MJTC_includer::MJTC_getTable('attachments');
            $MJTC_data = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($MJTC_attachmentData);

            $MJTC_error = 0;
            if (!$MJTC_row->bind($MJTC_data)) {
                $MJTC_error = 1;
            }
            if (!$MJTC_row->store()) {
                $MJTC_error = 1;
            }

            // Use $MJTC_filesystem->exists instead of file_exists
            if (!$MJTC_filesystem->exists($MJTC_source)) {
                die("Attachment source file does not exist: " . esc_html( $MJTC_source) );
                continue;
            }

            $MJTC_result = $MJTC_filesystem->copy($MJTC_source, $MJTC_destination, true);
            if (!$MJTC_result) {
                die( esc_html( "Failed to copy attachment from $MJTC_source to $MJTC_destination" ) );
            } else {
                // Replaced rename() with WP_Filesystem::move()
                $MJTC_filesystem->move($MJTC_destination, $MJTC_destination_new_name, true);
            }         
        }
    }

    private function getFluentSupportTicketActivityLog($ms_ticket_id, $MJTC_fs_ticket_id) {
        $MJTC_fs_ticket_id = intval($MJTC_fs_ticket_id);
        $ms_ticket_id = intval($ms_ticket_id);

        if ($MJTC_fs_ticket_id <= 0 || $ms_ticket_id <= 0) return;

        $MJTC_query = "
            SELECT * FROM `" . majesticsupport::$_db->prefix . "fs_activities`
            WHERE  object_type = 'ticket' AND object_id = ".$MJTC_fs_ticket_id."
            ORDER BY id DESC";

        $threads = majesticsupport::$_db->get_results($MJTC_query);
        if (empty($threads)) return;

        foreach ($threads as $thread) {
            $MJTC_ticketid = $ms_ticket_id;

            // Get user information
            $MJTC_userinfo = $this->getFluentSupportTicketCustomerInfo($thread->person_id);
            $MJTC_currentUserName = !empty($MJTC_userinfo['customer_name']) 
                ? esc_html($MJTC_userinfo['customer_name']) 
                : esc_html(__('Guest', 'majestic-support'));

            $MJTC_messagetype = __('Successfully', 'majestic-support');
            $MJTC_eventtype = MJTC_majesticsupportphplib::MJTC_str_replace("fluent_support/","",$thread->event_type);
            $MJTC_message = MJTC_majesticsupportphplib::MJTC_strip_tags($thread->description);
            

            if (!empty($MJTC_eventtype) && !empty($MJTC_message)) {
                MJTC_includer::MJTC_getModel('tickethistory')->addActivityLog(
                    $MJTC_ticketid, 1, esc_html($MJTC_eventtype), esc_html($MJTC_message), esc_html($MJTC_messagetype)
                );
            }
        }
    }

    private function getFluentSupportTicketStaffTime($ms_ticket_id, $MJTC_fs_ticket_id) {
        $MJTC_fs_ticket_id = intval($MJTC_fs_ticket_id);
        $ms_ticket_id = intval($ms_ticket_id);
        if ($MJTC_fs_ticket_id <= 0 || $ms_ticket_id <= 0) return;

        // Get all timer logs for the given SupportCandy ticket
        $MJTC_query = "
            SELECT * FROM `" . majesticsupport::$_db->prefix . "fs_time_tracks`
            WHERE ticket_id = ".$MJTC_fs_ticket_id;
        $MJTC_timers = majesticsupport::$_db->get_results($MJTC_query);

        if (empty($MJTC_timers)) return;

        foreach ($MJTC_timers as $MJTC_timer) {
            // Get HelpDesk staff ID from FluentSupport agent ID
            $MJTC_staffid = $this->getMSAgentIdByFSAgentId($MJTC_timer->agent_id);

            $MJTC_created = $MJTC_timer->created_at;

            $MJTC_timer_minutes = $MJTC_timer->working_minutes;
            if ($MJTC_timer_minutes <= 0) continue;

            $MJTC_timer_seconds = $MJTC_timer_minutes * 60;
            // Conflict detection
            $MJTC_created_dt = new DateTime($MJTC_created);
            $MJTC_now = new DateTime();
            $MJTC_interval_to_now = $MJTC_created_dt->diff($MJTC_now);
            $MJTC_systemtime = ($MJTC_interval_to_now->days * 86400) + ($MJTC_interval_to_now->h * 3600) + ($MJTC_interval_to_now->i * 60) + $MJTC_interval_to_now->s;

            $MJTC_conflict = ($MJTC_timer_seconds > $MJTC_systemtime) ? 1 : 0;

            // Prepare data
            $MJTC_data = [
                'staffid' => $MJTC_staffid,
                'ticketid' => $ms_ticket_id,
                'referencefor' => 1,
                'referenceid' => 0,
                'usertime' => $MJTC_timer_seconds,
                'systemtime' => $MJTC_systemtime,
                'conflict' => $MJTC_conflict,
                'description' => $MJTC_timer->message,
                'timer_edit_desc' => $MJTC_timer->message,
                'status' => 1,
                'created' => $MJTC_created
            ];

            $MJTC_row = MJTC_includer::MJTC_getTable('timetracking');
            $MJTC_data = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($MJTC_data);

            if (!$MJTC_row->bind($MJTC_data) || !$MJTC_row->store()) {
                // optionally log or count the failure
                continue;
            }
        }
    }

    private function getMSAgentIdByFSAgentId($MJTC_fs_agent_id) {
        // Sanitize and validate input
        $MJTC_fs_agent_id = intval($MJTC_fs_agent_id);
        if ($MJTC_fs_agent_id <= 0) return null;

        // Secure SQL query using prepare()
        $MJTC_query = "
            SELECT agent.*
            FROM `" . majesticsupport::$_db->prefix . "fs_persons` AS fs_agent
            INNER JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_users` AS user
                ON user.wpuid = fs_agent.user_id
            INNER JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_staff` AS agent
                ON agent.uid = user.id
            WHERE fs_agent.person_type = 'agent' AND fs_agent.id = " . esc_sql($MJTC_fs_agent_id) . "
            LIMIT 1
        ";

        $ms_agent = majesticsupport::$_db->get_row($MJTC_query);

        return $ms_agent ?: null;
    }

    private function getFluentSupportTicketCustomerInfo($MJTC_customerId) {
        // Sanitize and validate customer ID
        $MJTC_customerId = intval($MJTC_customerId);
        if ($MJTC_customerId <= 0) {
            return [
                "ms_uid" => "",
                "customer_name" => "",
                "customer_email" => ""
            ];
        }

        // Prepare secure query
        $MJTC_query = "
            SELECT CONCAT(customer.first_name, ' ', customer.last_name) AS name, customer.email, user.id AS ms_uid
            FROM `" . majesticsupport::$_db->prefix . "fs_persons` AS customer
            INNER JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_users` AS user
                ON user.wpuid = customer.user_id
            WHERE customer.id = " . esc_sql($MJTC_customerId) . "
            AND customer.person_type = 'customer'
            LIMIT 1
        ";

        $MJTC_data = majesticsupport::$_db->get_row($MJTC_query);

        return [
            "ms_uid"       => $MJTC_data->ms_uid ?? "",
            "customer_name"  => $MJTC_data->name ?? "",
            "customer_email" => $MJTC_data->email ?? ""
        ];
    }

    private function getTicketAgentIdByFluentSupport($MJTC_customerId) {
        // Validate customer ID
        $MJTC_customerId = intval($MJTC_customerId);
        if ($MJTC_customerId <= 0) {
            return null;
        }

        // Get mapped user info
        $MJTC_query = "SELECT agent.id
                    FROM `" . majesticsupport::$_db->prefix . "fs_persons` AS person
            INNER JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_users` AS user
                ON user.wpuid = person.user_id
            INNER JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_staff` AS agent
                ON agent.uid = user.id
                    WHERE person.id = " . esc_sql($MJTC_customerId) . "
                    AND person.person_type = 'agent';";
        $ms_agent = majesticsupport::$_db->get_var($MJTC_query);

        return $ms_agent ?: null;
    }

    private function getTicketProductIdByFluentSupport($MJTC_productId){
        // Sanitize and validate input
        $MJTC_productId = intval($MJTC_productId);
        if ($MJTC_productId <= 0) return null;

        // Fetch product from source table
        $MJTC_query = "
            SELECT title
            FROM `" . majesticsupport::$_db->prefix . "fs_products` 
            WHERE id = ".$MJTC_productId;
        $MJTC_product_name = majesticsupport::$_db->get_var($MJTC_query);

        if (empty($MJTC_product_name)) return null;

        // Find corresponding product in destination table
        
        $MJTC_name = $MJTC_product_name;
        $MJTC_query = "
            SELECT id FROM `" . majesticsupport::$_db->prefix . "mjtc_support_products`
                WHERE LOWER(product) = '".MJTC_majesticsupportphplib::MJTC_strtolower(MJTC_majesticsupportphplib::MJTC_trim(esc_sql($MJTC_name)))."'";;
        $ms_product_id = majesticsupport::$_db->get_var($MJTC_query);
        
        return $ms_product_id ? (int)$ms_product_id : null;
    }

    private function getTicketPriorityIdByFluentSupport($MJTC_prioritName) {
        
        // Find corresponding priority in destination table
        $MJTC_query = "
            SELECT id FROM `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` 
                WHERE LOWER(priority) = '".MJTC_majesticsupportphplib::MJTC_strtolower(MJTC_majesticsupportphplib::MJTC_trim(esc_sql($MJTC_prioritName)))."'";
        $ms_priority_id = majesticsupport::$_db->get_var($MJTC_query);

        return $ms_priority_id ? (int)$ms_priority_id : null;
    }

    private function importFluentSupportUsers() {
        // check if user already processed for import
        $MJTC_imported_users = array();
        $MJTC_imported_users_json = get_option('mjtc_support_ticket_fluent_support_data_users');
        if(!empty($MJTC_imported_users_json)){
            $MJTC_imported_users = json_decode($MJTC_imported_users_json,true);
        }

        // Fetch all customers
        $MJTC_query = "SELECT * FROM `" . majesticsupport::$_db->prefix . "fs_persons` WHERE person_type = 'customer' OR person_type = 'agent'";
        $MJTC_customers = majesticsupport::$_db->get_results($MJTC_query);

        if (empty($MJTC_customers)) return;

        foreach ($MJTC_customers as $MJTC_customer) {
            if(!empty($MJTC_customer->user_id)){
                $MJTC_wpuid = intval($MJTC_customer->user_id);
            }else{
                $MJTC_query = "SELECT user.ID
                    FROM `" . majesticsupport::$_db->prefix . "users` AS user
                    WHERE user.user_email = '".esc_sql($MJTC_customer->email)."'";
                $MJTC_user = majesticsupport::$_db->get_row($MJTC_query);
                if($MJTC_user) $MJTC_wpuid = intval($MJTC_user->ID);
            }
            if (empty($MJTC_wpuid)) {
                $this->fluent_support_import_count['user']['skipped']++;
                continue;
            }
            $MJTC_customer_id = intval($MJTC_customer->id);
            $MJTC_name        = sanitize_text_field($MJTC_customer->first_name ?? '');
            if($MJTC_customer->last_name) $MJTC_name = $MJTC_name." ".sanitize_text_field($MJTC_customer->last_name);
            $MJTC_email       = sanitize_email($MJTC_customer->email ?? '');

            // Skip if already imported
            if (in_array($MJTC_customer_id, $MJTC_imported_users, true)) {
                $this->fluent_support_import_count['user']['skipped']++;
                continue;
            }

            // Check if user already exists
            $MJTC_user_query = "SELECT user.*
                       FROM `" . majesticsupport::$_db->prefix . "mjtc_support_users` AS user
                       WHERE user.wpuid = ".$MJTC_wpuid;
            $MJTC_existing_user = majesticsupport::$_db->get_row($MJTC_user_query);

            if ($MJTC_existing_user) {
                $this->fluent_support_import_count['user']['skipped']++;
                continue;
            }

            // Prepare data for new user
            $MJTC_row = MJTC_includer::MJTC_getTable('users');
            $MJTC_data = [
                'id'            => '',
                'wpuid'         => $MJTC_wpuid,
                'name'          => $MJTC_name,
                'display_name'  => $MJTC_name,
                'user_email'    => $MJTC_email,
                'status'        => 1,
                'issocial'      => 0,
                'socialid'      => null,
                'autogenerated' => 0,
            ];

            // Attempt to save the new user
            $MJTC_row->bind($MJTC_data);
            if (!$MJTC_row->store()) {
                $this->fluent_support_import_count['user']['failed']++;
                continue;
            }

            // Store successful import info
            $this->fluent_support_users_array[$MJTC_customer_id] = $MJTC_row->id;
            $this->fluent_support_user_ids[] = $MJTC_customer_id;
            $this->fluent_support_import_count['user']['imported']++;
        }

        // Save list of imported user IDs
        if (!empty($this->fluent_support_user_ids)) {
            update_option('mjtc_support_ticket_fluent_support_data_users', wp_json_encode(array_unique(array_merge($MJTC_imported_users, $this->fluent_support_user_ids))));
        }
    }

    private function importFluentSupportAgents() {
        // check if user already processed for import
        $MJTC_imported_agents = array();
        $MJTC_imported_agent_json = get_option('mjtc_support_ticket_fluent_support_data_agents');
        if(!empty($MJTC_imported_agents_json)){
            $MJTC_imported_agents = json_decode($MJTC_imported_agents_json,true);
        }
        $MJTC_query = "
            SELECT agent.*
            FROM `" . majesticsupport::$_db->prefix . "fs_persons` AS agent
            WHERE agent.person_type = 'agent';";
        $MJTC_agents = majesticsupport::$_db->get_results($MJTC_query);

        if($MJTC_agents){
            foreach($MJTC_agents AS $MJTC_agent){
                // Failed if addon not installed
                if (!in_array('agent', majesticsupport::$_active_addons) ) {
                    $this->fluent_support_import_count['agent']['failed']++;
                    continue;
                }
                $MJTC_wpuid = (int) $MJTC_agent->user_id;
                // Skip if already imported
                if (in_array($MJTC_wpuid, $MJTC_imported_agents, true)) {
                    $this->fluent_support_import_count['agent']['skipped']++;
                    continue;
                }
                $MJTC_first_name = $MJTC_agent->first_name;
                $MJTC_last_name = $MJTC_agent->last_name;
                if($MJTC_agent->status == "active") $MJTC_agent_status = 1; else $MJTC_agent_status = 0;

                $MJTC_query = "SELECT user.*
                            FROM `" . majesticsupport::$_db->prefix . "users` AS user
                            WHERE user.id = " . $MJTC_wpuid;
                $MJTC_wpuser = majesticsupport::$_db->get_row($MJTC_query);

                if(!$MJTC_wpuser){
                    $this->fluent_support_import_count['agent']['failed'] += 1;
                    continue;
                }
                $mjtc_user = MJTC_includer::MJTC_getObjectClass('user')->MJTC_getmajesticsupportuidbyuserid($MJTC_wpuid);
                if (!empty($mjtc_user) && isset($mjtc_user[0]->id)) {
                    $mjtc_uid = (int)$mjtc_user[0]->id;
                } else {
                    $this->fluent_support_import_count['agent']['failed']++;
                    continue;
                }

                $MJTC_query = "SELECT staff.*
                            FROM `" . majesticsupport::$_db->prefix . "mjtc_support_staff` AS staff
                            WHERE staff.uid = " . $mjtc_uid;
                $MJTC_staff = majesticsupport::$_db->get_row($MJTC_query);

                if (!$MJTC_staff) {
                    $MJTC_roleid = $this->getAgentRoleIdByFluentSupport($MJTC_wpuid);

                    $MJTC_timestamp = date_i18n('Y-m-d H:i:s');

                    $MJTC_data = [
                        'id'           => '',
                        'uid'          => $mjtc_uid,
                        'groupid'      => '',
                        'roleid'       => $MJTC_roleid,
                        'departmentid' => '',
                        'firstname'    => $MJTC_first_name,
                        'lastname'     => $MJTC_last_name,
                        'username'     => $MJTC_wpuser->user_login,
                        'email'        => $MJTC_wpuser->user_email,
                        'signature'    => '',
                        'isadmin'      => '',
                        'status'       => $MJTC_agent_status,
                        'updated'      => $MJTC_timestamp,
                        'created'      => $MJTC_timestamp
                    ];

                    MJTC_includer::MJTC_getModel('agent')->storeStaff($MJTC_data);

                    $this->fluent_support_import_count['agent']['imported'] += 1;
                    $this->fluent_support_agent_ids[] = $MJTC_wpuid;

                } else {
                    $this->fluent_support_import_count['agent']['skipped'] += 1;
                }
            }
            // Save list of imported agent IDs
            if (!empty($this->fluent_support_agent_ids)) {
                update_option('mjtc_support_ticket_fluent_support_data_agents', wp_json_encode(array_unique(array_merge($MJTC_imported_agents, $this->fluent_support_agent_ids))));
            }
        }
    }

    private function getAgentRoleIdByFluentSupport($MJTC_id) {
        $MJTC_capabilities = get_user_meta($MJTC_id, majesticsupport::$_db->prefix . 'capabilities', true);
        $MJTC_isAdmin = !empty($MJTC_capabilities['administrator']);
        $MJTC_output = [];

        // Define capability-to-permission mappings
        $MJTC_capabilityPermissions = [
            'fst_manage_saved_replies' => [
                'Add Canned Response' => 75,
                'Edit Canned Response' => 76,
                'View Canned Response' => 77,
                'Delete Canned Response' => 78,
            ],
            'fst_view_all_reports' => [
                'View Agent Reports' => 59,
                'View Department Reports' => 60,
            ],
            'fst_sensitive_data' => [
                'View Credentials' => 67,
                'Delete Credentials' => 68,
                'Edit Credentials' => 69,
                'Add Credentials' => 70,
            ],
            'fst_delete_tickets' => [
                'Delete Ticket' => 12,
            ],
            'fst_assign_agents' => [
                'Assign Ticket To Agent' => 6,
            ],
            'fst_merge_tickets' => [
                'Ticket Merge' => 66,
            ],
            'fst_manage_unassigned_tickets' => [
                'All Tickets' => 61,
            ],
            'fst_manage_own_tickets' => [
                'Add Ticket' => 1,
                'Edit Ticket' => 2,
                'Close Ticket' => 3,
                'Reopen Ticket' => 4,
                'Reply Ticket' => 5,
                'Assign Ticket To Agent' => 6,
                'Ticket Department Transfer' => 7,
                'Mark Overdue' => 8,
                'Mark In Progress' => 9,
                'Change Ticket Priority' => 10,
                'Unban Email' => 11,
                'Delete Ticket' => 12,
                'Ban Email And Close Ticket' => 13,
                'Lock Ticket' => 14,
                'Attachment' => 15,
                'Post Internal Note' => 16,
                'Duedate Ticket' => 53,
                'View Ticket' => 54,
                'Release Ticket' => 55,
                'New Ticket Notification' => 56,
                'Allow Mail System' => 57,
                'Print Ticket' => 58,
                'Mark Non Premium' => 79,
                'Link To Paid Support' => 80,
                'Export Ticket' => 81,
                'Change Ticket Status' => 82,
                'Edit Own Time' => 62,
            ],
        ];

        // Loop through capabilities and add matching permissions
        foreach ($MJTC_capabilityPermissions as $MJTC_capKey => $MJTC_permissions) {
            if (!empty($MJTC_capabilities[$MJTC_capKey]) || $MJTC_isAdmin) {
                $MJTC_output = array_merge($MJTC_output, $MJTC_permissions);
            }
        }

        $MJTC_name = 'Fluent Support Agent ' . $MJTC_id;

        $MJTC_data = [
            'name'          => $MJTC_name,
            'roleperdata'   => $MJTC_output,
            'id'            => '',
            'created'       => '',
            'updated'       => '',
            'action'        => 'role_saverole',
            'form_request'  => 'majesticsupport',
            'save'          => 'Save Role',
        ];

        // Save the role and permissions
        MJTC_includer::MJTC_getModel('role')->storeRole($MJTC_data);

        // Retrieve role ID
        $MJTC_query = 'SELECT id FROM `' . majesticsupport::$_db->prefix . 'mjtc_support_acl_roles` WHERE name = "' . esc_sql($MJTC_name) . '"';
        $MJTC_id = majesticsupport::$_db->get_var($MJTC_query);
        return $MJTC_id;
    }

    private function importFluentSupportProducts(){
        // check if product already processed for import
        $MJTC_imported_products = array();
        $MJTC_imported_products_json = get_option('mjtc_support_ticket_fluent_support_data_products');
        if(!empty($MJTC_imported_products_json)){
            $MJTC_imported_products = json_decode($MJTC_imported_products_json,true);
        }

        $MJTC_query = "SELECT product.* FROM `" . majesticsupport::$_db->prefix . "fs_products` AS product;";
        $MJTC_products = majesticsupport::$_db->get_results($MJTC_query);

        if (empty($MJTC_products)) return;
        
        // Get highest current ordering value
        $MJTC_query = "
            SELECT MAX(product.ordering)
            FROM `" . majesticsupport::$_db->prefix . "mjtc_support_products` AS product
        ";
        $MJTC_ordering = (int) majesticsupport::$_db->get_var($MJTC_query);

        foreach($MJTC_products AS $MJTC_product){
            // Skip if already imported
            if (in_array($MJTC_product->id, $MJTC_imported_products, true)) {
                $this->fluent_support_import_count['product']['skipped']++;
                continue;
            }

            $MJTC_name = MJTC_majesticsupportphplib::MJTC_trim(MJTC_majesticsupportphplib::MJTC_strtolower($MJTC_product->title));

            // Check if this product already exists in Majestic Support
            $MJTC_check_query = "
                SELECT product.*
                FROM `" . majesticsupport::$_db->prefix . "mjtc_support_products` AS product
                WHERE LOWER(product.product) = '".esc_sql($MJTC_name) ."'
                LIMIT 1
            ";
            $ms_product = majesticsupport::$_db->get_row($MJTC_check_query);

            if(!$ms_product){
                $MJTC_row = MJTC_includer::MJTC_getTable('products');
                
                $MJTC_data = [
                    'id'               => '',
                    'product'         => $MJTC_name,
                    'status'           => '1',
                    'ordering'         => $MJTC_ordering
                ];

                $MJTC_data = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($MJTC_data);
                $MJTC_row->bind($MJTC_data);

                if (!$MJTC_row->store()) {
                    $this->fluent_support_import_count['product']['failed'] += 1;
                } else {
                    $this->fluent_support_product_ids[] = $MJTC_product->id;
                    $this->fluent_support_import_count['product']['imported'] += 1;
                }

                $MJTC_ordering++;
            } else {
                $this->fluent_support_import_count['product']['skipped'] += 1;
            }
        }
        // Save list of imported product IDs
        if (!empty($this->fluent_support_product_ids)) {
            update_option('mjtc_support_ticket_fluent_support_data_products', wp_json_encode(array_unique(array_merge($MJTC_imported_products, $this->fluent_support_product_ids))));
        }
    }
    
    private function importFluentSupportPriorities() {
        // check if priority already processed for import
        $MJTC_imported_priorities = array();
        $MJTC_imported_priorities_json = get_option('mjtc_support_ticket_fluent_support_data_priorities');
        if(!empty($MJTC_imported_priorities_json)){
            $MJTC_imported_priorities = json_decode($MJTC_imported_priorities_json,true);
        }
        $MJTC_priorities = array('Normal' => '#00a32a', 'Medium' => '#a5b2bd', 'Critical' => '#f06060');

        if (empty($MJTC_priorities)) return;

        // Get highest current ordering value
        $MJTC_query = "
            SELECT MAX(priority.ordering)
            FROM `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` AS priority
        ";
        $MJTC_ordering = (int) majesticsupport::$_db->get_var($MJTC_query);

        foreach ($MJTC_priorities as $MJTC_key => $MJTC_priority) {
            // Skip if already imported
            if (in_array($MJTC_priority, $MJTC_imported_priorities, true)) {
                $this->fluent_support_import_count['priority']['skipped']++;
                continue;
            }
            $MJTC_name = MJTC_majesticsupportphplib::MJTC_trim(MJTC_majesticsupportphplib::MJTC_strtolower($MJTC_key));

            // Check if this priority already exists in Majestic Support
            $MJTC_check_query = "
                SELECT priority.*
                FROM `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` AS priority
                WHERE LOWER(priority.priority) = '" . esc_sql($MJTC_name) . "'
                LIMIT 1
            ";
            $ms_priority = majesticsupport::$_db->get_row($MJTC_check_query);

            if (!$ms_priority) {
                $MJTC_row = MJTC_includer::MJTC_getTable('priorities');

                $MJTC_data = [
                    'id'               => '',
                    'priority'         => $MJTC_name,
                    'prioritycolour'   => $MJTC_priority,
                    'priorityurgency'  => '',
                    'overduetypeid'    => 1,
                    'overdueinterval'  => 7,
                    'ordering'         => $MJTC_ordering,
                    'status'           => '1',
                    'isdefault'        => '0',
                    'ispublic'         => '1'
                ];

                $MJTC_data = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($MJTC_data);
                $MJTC_row->bind($MJTC_data);

                if (!$MJTC_row->store()) {
                    $this->fluent_support_import_count['priority']['failed'] += 1;
                } else {
                    $this->fluent_support_priority_ids[] = $MJTC_priority;
                    $this->fluent_support_import_count['priority']['imported'] += 1;
                }

                $MJTC_ordering++;
            } else {
                $this->fluent_support_import_count['priority']['skipped'] += 1;
            }
        }
        // Save list of imported priority IDs
        if (!empty($this->fluent_support_priority_ids)) {
            update_option('mjtc_support_ticket_fluent_support_data_priorities', wp_json_encode(array_unique(array_merge($MJTC_imported_priorities, $this->fluent_support_priority_ids))));
        }
    }

    private function importFluentSupportPremades() {
        // check if premade already processed for import
        $MJTC_imported_premades = array();
        $MJTC_imported_premades_json = get_option('mjtc_support_ticket_fluent_support_data_premades');
        if(!empty($MJTC_imported_premades_json)){
            $MJTC_imported_premades = json_decode($MJTC_imported_premades_json,true);
        }
        $MJTC_query = "
            SELECT canned_reply.*
            FROM `" . majesticsupport::$_db->prefix . "fs_saved_replies` AS canned_reply
        ";
        $MJTC_canned_replies = majesticsupport::$_db->get_results($MJTC_query);

        if (empty($MJTC_canned_replies)) return;

        foreach ($MJTC_canned_replies as $MJTC_canned_reply) {
            $title = MJTC_majesticsupportphplib::MJTC_trim(MJTC_majesticsupportphplib::MJTC_strtolower($MJTC_canned_reply->title));
            // Failed if addon not installed
            if (!in_array('cannedresponses', majesticsupport::$_active_addons) ) {
                $this->fluent_support_import_count['canned response']['failed']++;
                continue;
            }
            // Skip if already imported
            if (in_array($MJTC_canned_reply->id, $MJTC_imported_premades, true)) {
                $this->fluent_support_import_count['canned response']['skipped']++;
                continue;
            }
            // Skip if no department id
            if (empty($MJTC_departmentid)) {
                $this->fluent_support_import_count['canned response']['skipped']++;
                continue;
            }
            // Check if this premade already exists in Majestic Support
            $MJTC_check_query = "
                SELECT premade.*
                FROM `" . majesticsupport::$_db->prefix . "mjtc_support_department_message_premade` AS premade
                WHERE LOWER(premade.title) = '" . esc_sql($title) . "'
                LIMIT 1
            ";
            $ms_canned_reply = majesticsupport::$_db->get_row($MJTC_check_query);

            if (!$ms_canned_reply) {
                $MJTC_departmentid = '';
                // Step 1: Get default department
                $MJTC_department_query = "
                    SELECT department.id 
                    FROM `" . majesticsupport::$_db->prefix . "mjtc_support_departments` AS department
                    WHERE department.isdefault = 1
                    LIMIT 1
                ";
                $MJTC_department = majesticsupport::$_db->get_row($MJTC_department_query);

                // Step 2: If no default found, get the first department
                if (!$MJTC_department) {
                    $MJTC_department_query = "
                        SELECT department.id 
                        FROM `" . majesticsupport::$_db->prefix . "mjtc_support_departments` AS department
                        ORDER BY department.id ASC
                        LIMIT 1
                    ";
                    $MJTC_department = majesticsupport::$_db->get_row($MJTC_department_query);
                }

                // Step 3: If still no department found, insert 'Support' and get its ID
                if (!$MJTC_department) {
                    $MJTC_row = MJTC_includer::MJTC_getTable('departments');

                        $MJTC_updated = date_i18n('Y-m-d H:i:s');
                        $MJTC_created = date_i18n('Y-m-d H:i:s');

                        $MJTC_data = [
                            'id'              => '',
                            'emailid'         => '1',
                            'departmentname'  => 'Support',
                            'ordering'        => 0,
                            'status'          => '1',
                            'isdefault'       => '0',
                            'ispublic'        => '1',
                            'updated'         => $MJTC_updated,
                            'created'         => $MJTC_created
                        ];

                        $MJTC_data = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($MJTC_data);
                        $MJTC_row->bind($MJTC_data);

                        if ($MJTC_row->store()) {
                            $MJTC_departmentid = $MJTC_row->id;
                        }
                } else {
                    $MJTC_departmentid = $MJTC_department->id;
                }

                // Prepare canned response data
                $MJTC_row = MJTC_includer::MJTC_getTable('cannedresponses');
                $MJTC_updated = date_i18n('Y-m-d H:i:s');

                $MJTC_data = [
                    'id'          => '',
                    'departmentid'=> $MJTC_departmentid,
                    'title'       => $MJTC_canned_reply->title,
                    'answer'      => $MJTC_canned_reply->content,
                    'status'      => '1',
                    'updated'     => $MJTC_updated,
                    'created'     => $MJTC_canned_reply->created_at
                ];

                $MJTC_data = majesticsupport::MJTC_sanitizeData($MJTC_data);
                $MJTC_data['answer'] = MJTC_includer::MJTC_getModel('majesticsupport')->getSanitizedEditorData($MJTC_data['answer']);
                $MJTC_data = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($MJTC_data);

                $MJTC_row->bind($MJTC_data);
                if (!$MJTC_row->store()) {
                    $this->fluent_support_import_count['canned response']['failed'] += 1;
                } else {
                    $this->fluent_support_premade_ids[] = $MJTC_canned_reply->id;
                    $this->fluent_support_import_count['canned response']['imported'] += 1;
                }
            } else {
                $this->fluent_support_import_count['canned response']['skipped'] += 1;
            }
        }

        // Save list of imported premade IDs
        if (!empty($this->fluent_support_premade_ids)) {
            update_option('mjtc_support_ticket_fluent_support_data_premades', wp_json_encode(array_unique(array_merge($MJTC_imported_premades, $this->fluent_support_premade_ids))));
        }
    }

    function getFluentSupportDataStats($MJTC_count_for) {
        // Only FluentSupport (count_for = 1)
        if ($MJTC_count_for != 3) return;

        // Check if FluentSupport is active
        include_once ABSPATH . 'wp-admin/includes/plugin.php';
        if (!is_plugin_active('fluent-support/fluent-support.php')) {
            return new WP_Error('mjtc_inactive', 'FluentSupport is not active.');
        }

        $MJTC_entity_counts = [];

        // Users
        if (majesticsupport::$_db->get_var("SHOW TABLES LIKE '" . majesticsupport::$_db->prefix . "fs_persons'")) {
            $MJTC_query = "SELECT COUNT(*) FROM `" . majesticsupport::$_db->prefix . "fs_persons` WHERE person_type = 'customer' OR person_type = 'agent'";
            $MJTC_count = (int) majesticsupport::$_db->get_var($MJTC_query);
            if ($MJTC_count > 0) $MJTC_entity_counts['user'] = $MJTC_count;
        }

        // Agents
        if (majesticsupport::$_db->get_var("SHOW TABLES LIKE '" . majesticsupport::$_db->prefix . "fs_persons'")) {
            $MJTC_query = "SELECT COUNT(*) FROM `" . majesticsupport::$_db->prefix . "fs_persons` AS agent
            WHERE agent.person_type = 'agent';";
            $MJTC_count = (int) majesticsupport::$_db->get_var($MJTC_query);
            if ($MJTC_count > 0) $MJTC_entity_counts['agent'] = $MJTC_count;
        }

        // Priorities
        $MJTC_entity_counts['priority'] = 3;

        // Canned Responses
        if (majesticsupport::$_db->get_var("SHOW TABLES LIKE '" . majesticsupport::$_db->prefix . "fs_saved_replies'")) {
            $MJTC_query = "SELECT COUNT(*) FROM `" . majesticsupport::$_db->prefix . "fs_saved_replies`";
            $MJTC_count = (int) majesticsupport::$_db->get_var($MJTC_query);
            if ($MJTC_count > 0) $MJTC_entity_counts['canned response'] = $MJTC_count;
        }

        // Products
        if (majesticsupport::$_db->get_var("SHOW TABLES LIKE '" . majesticsupport::$_db->prefix . "fs_products'")) {
            $MJTC_query = "SELECT COUNT(*) FROM `" . majesticsupport::$_db->prefix . "fs_products`";
            $MJTC_count = (int) majesticsupport::$_db->get_var($MJTC_query);
            if ($MJTC_count > 0) $MJTC_entity_counts['product'] = $MJTC_count;
        }

        // Custom Ticket Fields
        if (majesticsupport::$_db->get_var("SHOW TABLES LIKE '" . majesticsupport::$_db->prefix . "fs_meta'")) {
            $MJTC_query = "
            SELECT * FROM `" . majesticsupport::$_db->prefix . "fs_meta`
            WHERE object_type = 'option' AND `key` = '_ticket_custom_fields';";
            $MJTC_custom_fields_serializeed = majesticsupport::$_db->get_row($MJTC_query);
            if (!empty($MJTC_custom_fields_serializeed)) {
                $MJTC_custom_fields = unserialize($MJTC_custom_fields_serializeed->value);
                $MJTC_count = count($MJTC_custom_fields);
                if ($MJTC_count > 0) $MJTC_entity_counts['field'] = $MJTC_count;
            }
        }

        // Tickets with type 'report'
        if (majesticsupport::$_db->get_var("SHOW TABLES LIKE '" . majesticsupport::$_db->prefix . "fs_tickets'")) {
            $MJTC_query = "SELECT COUNT(DISTINCT tickets.id)
                FROM `" . majesticsupport::$_db->prefix . "fs_tickets` AS tickets";
            $MJTC_count = (int) majesticsupport::$_db->get_var($MJTC_query);
            if ($MJTC_count > 0) $MJTC_entity_counts['ticket'] = $MJTC_count;
        }

        majesticsupport::$_data['entity_counts'] = $MJTC_entity_counts;
    }
}

?>
