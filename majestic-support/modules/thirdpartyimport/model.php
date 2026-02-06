<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_thirdpartyimportModel {

    // supportcandy import data

    private $support_candy_users_array = array();
    
    private $support_candy_ticket_custom_fields = array();
    private $sc_ticket_custom_fields = array();
    private $as_ticket_custom_fields = array();
    private $fc_ticket_cf = array();


    private $support_candy_user_ids = array();
    private $support_candy_agent_ids = array();
    private $support_candy_department_ids = array();
    private $support_candy_agent_role_ids = array();
    private $support_candy_ticket_ids = array();
    private $support_candy_status_ids = array();
    private $support_candy_priority_ids = array();
    private $support_candy_premade_ids = array();


    private $awesome_support_user_ids = array();
    private $awesome_support_agent_ids = array();
    private $awesome_support_department_ids = array();
    private $awesome_support_ticket_ids = array();
    private $awesome_support_status_ids = array();
    private $awesome_support_priority_ids = array();
    private $awesome_support_premade_ids = array();


    private $fluent_support_user_ids = array();
    private $fluent_support_agent_ids = array();
    private $fluent_support_ticket_ids = array();
    private $fluent_support_priority_ids = array();
    private $fluent_support_premade_ids = array();



    private $_params_flag;
    private $_params_string;



    // values for counts
    private $support_candy_import_count = [];
    private $awesome_support_import_count = [];
    private $fluent_support_import_count = [];

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
        $filesystem = new WP_Filesystem_Direct(true);
        $upload_dir = wp_upload_dir();
        $upload_path = $upload_dir['basedir'];
        $MJTC_datadirectory = majesticsupport::$_config['data_directory'];
        $path = $upload_path . "/" . $MJTC_datadirectory;

        if (!$filesystem->exists($path)) {
            wp_mkdir_p($path);
        }
        $path .= '/attachmentdata';
        if (!$filesystem->exists($path)) {
            wp_mkdir_p($path);
        }
        $path .= '/ticket';
        if (!$filesystem->exists($path)) {
            wp_mkdir_p($path);
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
        $supportcandy_settings = get_option( 'wpsc-ap-general' );
        $helpdesk_settings = get_option('mjtc_set_theme_colors');
        $MJTC_data = json_decode($helpdesk_settings, true);
        $MJTC_data['color1'] = $supportcandy_settings['primary-color'];
        $MJTC_data['color4'] = $supportcandy_settings['main-text-color'];
        // store help desk settings
        $MJTC_data = majesticsupport::MJTC_sanitizeData($MJTC_data);
        update_option('mjtc_set_theme_colors', wp_json_encode($MJTC_data));
    }

    private function getSupportCandyTickets($sc_ticket_custom_fields) {
        // Check if tickets already processed for import
        $imported_tickets = array();
        $imported_tickets_json = get_option('mjtc_support_ticket_support_candy_data_tickets');
        if (!empty($imported_tickets_json)) {
            $imported_tickets = json_decode($imported_tickets_json, true);
        }

        $query = "SELECT tickets.*, replies.body AS reply_message, replies.type, replies.id AS replyid
                  FROM `" . majesticsupport::$_db->prefix . "psmsc_tickets` AS tickets
                  JOIN `" . majesticsupport::$_db->prefix . "psmsc_threads` AS replies ON replies.ticket = tickets.id 
                  WHERE replies.type = 'report' AND tickets.is_active != 0
                  ORDER BY tickets.id ASC";
        
        $MJTC_tickets = majesticsupport::$_db->get_results($query);

        $general_options = get_option("wpsc-gs-general");
        $after_customer_reply = $general_options['ticket-status-after-customer-reply'];
        $after_agent_reply = $general_options['ticket-status-after-agent-reply'];
        $close_ticket_status = $general_options['close-ticket-status'];

        foreach ($MJTC_tickets as $MJTC_ticket) {
            // Skip if ticket already imported
            if (!empty($imported_tickets) && in_array($MJTC_ticket->id, $imported_tickets)) {
                $this->support_candy_import_count['ticket']['skipped'] += 1;
                continue;
            }

            $attachmentdir = MJTC_includer::MJTC_getModel('ticket')->getRandomFolderName();
            // Map custom fields
            $params = array();
            $eddorderid = '';
            $eddproductid = '';
            $wcproductid = '';
            $wcorderid = '';
            foreach ($sc_ticket_custom_fields as $sc_ticket_custom_field) {
                $field_name = $sc_ticket_custom_field["name"];
                $vardata = "";

                if ($MJTC_ticket->$field_name) {
                    if ($sc_ticket_custom_field["type"] == "cf_edd_order") {
                        $vardata = '';
                        $eddorderid = $MJTC_ticket->$field_name;
                    } elseif ($sc_ticket_custom_field["type"] == "cf_edd_product") {
                        $vardata = '';
                        $eddproductid = $MJTC_ticket->$field_name;
                    } elseif ($sc_ticket_custom_field["type"] == "cf_woo_order") {
                        $vardata = '';
                        $wcorderid = $MJTC_ticket->$field_name;
                    } elseif ($sc_ticket_custom_field["type"] == "cf_woo_product") {
                        $vardata = '';
                        $wcproductid = $MJTC_ticket->$field_name;
                    } elseif ($sc_ticket_custom_field["type"] == "date") {
                        $vardata = gmdate("Y-m-d", MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->$field_name));
                    } elseif ($sc_ticket_custom_field["type"] == "file") {
                        $vardata = $MJTC_ticket->$field_name;
                        $vardata = $this->getSupportCandyCustomFieldAttachments($MJTC_ticket->id, $vardata, $attachmentdir);
                    } elseif (in_array(strtolower($sc_ticket_custom_field["type"]), ['multiple', 'checkbox', 'combo', 'radio'])) {
                        $field_ids = explode('|', $MJTC_ticket->$field_name);

                        // Sanitize and cast to integers
                        $field_ids = array_map('intval', array_filter($field_ids));

                        // Check if we have valid IDs
                        if (!empty($field_ids)) {
                            $placeholders = implode(',', $field_ids);
                            $query = "SELECT name FROM `" . majesticsupport::$_db->prefix . "psmsc_options` WHERE id IN ($placeholders)";
                            $names = majesticsupport::$_db->get_col($query);

                            // Combine names into comma-separated string
                            $vardata = !empty($names) ? implode(', ', $names) : '';
                        } else {
                            $vardata = '';
                        }
                    } else {
                        $vardata = $MJTC_ticket->$field_name;
                    }

                    if ($vardata != '') {
                        if (is_array($vardata)) {
                            $vardata = implode(', ', array_filter($vardata));
                        }
                        $params[$sc_ticket_custom_field["ms_filedorderingfield"]] = MJTC_majesticsupportphplib::MJTC_htmlentities($vardata);
                    }
                }
            }
            $MJTC_ticketparams = html_entity_decode(wp_json_encode($params, JSON_UNESCAPED_UNICODE));

            // Get linked data
            $userinfo = $this->getSupportCandyTicketCustomerInfo($MJTC_ticket->customer);
            $agentid = $this->getTicketAgentIdBySupportCandy($MJTC_ticket->assigned_agent);
            $MJTC_departmentid = $this->getTicketDepartmentIdBySupportCandy($MJTC_ticket->category);
            $priorityid = $this->getTicketPriorityIdBySupportCandy($MJTC_ticket->priority);

            $idresult = MJTC_includer::MJTC_getModel('ticket')->getRandomTicketId();
            $MJTC_ticketid = $idresult['ticketid'];
            $customticketno = $idresult['customticketno'];

            // Determine ticket status
            $MJTC_ticket_status = 1;
            if ($MJTC_ticket->status == 1) $MJTC_ticket_status = 1;
            elseif ($MJTC_ticket->status == $after_customer_reply) $MJTC_ticket_status = 2;
            elseif ($MJTC_ticket->status == $after_agent_reply) $MJTC_ticket_status = 4;
            elseif ($MJTC_ticket->status == $close_ticket_status) $MJTC_ticket_status = 5;
            else $MJTC_ticket_status = $this->getTicketStatusIdBySupportCandy($MJTC_ticket->status);

            $isanswered = ($MJTC_ticket_status == 4) ? 1 : 0;

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

            $newTicketData = [
                'id' => "",
                'uid' => $userinfo["ms_uid"],
                'ticketid' => $MJTC_ticketid,
                'departmentid' => $MJTC_departmentid,
                'priorityid' => $priorityid,
                'staffid' => $agentid,
                'email' => $userinfo["customer_email"],
                'name' => $userinfo["customer_name"],
                'subject' => $MJTC_ticket->subject,
                'message' => $MJTC_ticket->reply_message,
                'helptopicid' => 0,
                'multiformid' => 1,
                'phone' => "",
                'phoneext' => "",
                'status' => $MJTC_ticket_status,
                'isoverdue' => "0",
                'isanswered' => $isanswered,
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
                'attachmentdir' => $attachmentdir,
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
                'wcorderid' => $wcorderid,
                'wcitemid' => "0",
                'wcproductid' => $wcproductid,
                'eddorderid' => $eddorderid,
                'eddproductid' => $eddproductid,
                'eddlicensekey' => "",
                'envatodata' => "",
                'paidsupportitemid' => "0",
                'customticketno' => $customticketno
            ];

            $row = MJTC_includer::MJTC_getTable('tickets');
            $error = 0;
            if (!$row->bind($newTicketData)) $error = 1;
            if (!$row->store()) $error = 1;

            if ($error == 1) {
                $this->support_candy_import_count['ticket']['failed'] += 1;
            } else {
                $this->support_candy_ticket_ids[] = $MJTC_ticket->id;
                $this->support_candy_import_count['ticket']['imported'] += 1;

                $ms_ticketid = $row->id;
                $hash = MJTC_includer::MJTC_getModel('ticket')->generateHash($ms_ticketid);
                $query = "UPDATE `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` SET `hash`='" . esc_sql($hash) . "' WHERE id=" . esc_sql($ms_ticketid);
                majesticsupport::$_db->query($query);

                if(in_array('note', majesticsupport::$_active_addons)){
                    $this->getSupportCandyTicketNotes($ms_ticketid, $MJTC_ticket->id, $attachmentdir);
                }
                $this->getSupportCandyTicketReplies($ms_ticketid, $MJTC_ticket->id, $attachmentdir);
                $this->getSupportCandyTicketAttachments($ms_ticketid, "", $MJTC_ticket->replyid, $attachmentdir);

                if (!empty($MJTC_ticket->pc_data) && in_array('privatecredentials', majesticsupport::$_active_addons)) {
                    $this->getSupportCandyTicketPrivateCredentials($ms_ticketid, $userinfo["ms_uid"], $MJTC_ticket->pc_data);
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

    private function getSupportCandyTicketNotes($ms_ticket_id, $sc_ticket_id, $attachmentdir){
        $query = "
            SELECT thread.*
                FROM `" . majesticsupport::$_db->prefix . "psmsc_threads` AS thread
                WHERE thread.ticket = " . (int)$sc_ticket_id . "
                AND thread.type = 'note'
                ORDER BY thread.id ASC";
                    
        $threads = majesticsupport::$_db->get_results($query);
        foreach($threads AS $thread){
            $query = "
            SELECT id FROM `" . majesticsupport::$_db->prefix . "mjtc_support_users` WHERE wpuid = ".$thread->customer;
            $agentid = $ms_user_id = majesticsupport::$_db->get_var($query);
            $filename = $this->getSupportCandyNoteAttachments($sc_ticket_id, $thread->attachments, $attachmentdir);

            $replyData = [
                "id" => "",
                "ticketid" => $ms_ticket_id,
                "staffid" => $agentid,
                "title" => MJTC_majesticsupportphplib::MJTC_strip_tags($thread->body),
                "note" => $thread->body,
                "status" => "1",
                "created" => $thread->date_created,
                "filename" => $filename,
                "filesize" => 5334
            ];
            $row = MJTC_includer::MJTC_getTable('note');
            $MJTC_data = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($replyData);// remove slashes with quotes.
            $error = 0;
            if (!$row->bind($MJTC_data)) {
                $error = 1;
            }
            if (!$row->store()) {
                $error = 1;
            }
            $ms_ticket_note_id = $row->id;
        }
    }

    private function getSupportCandyTicketReplies($ms_ticket_id, $sc_ticket_id, $attachmentdir){
        $query = "SELECT thread.*
                    FROM `" . majesticsupport::$_db->prefix . "psmsc_threads` AS thread
                    WHERE thread.ticket = " . (int)$sc_ticket_id . "
                    AND thread.type = 'reply'
                    ORDER BY thread.id ASC";
                    
        $threads = majesticsupport::$_db->get_results($query);

        if (empty($threads)) return;

        foreach ($threads as $thread) {
            $userinfo = $this->getSupportCandyTicketCustomerInfo($thread->customer);

            $replyData = [
                "id" => "",
                "uid" => isset($userinfo["ms_uid"]) ? $userinfo["ms_uid"] : 0,
                "ticketid" => $ms_ticket_id,
                "name" => isset($userinfo["customer_name"]) ? $userinfo["customer_name"] : __('Guest', 'majestic-support'),
                "message" => $thread->body,
                "staffid" => "",
                "rating" => "",
                "status" => "1",
                "created" => $thread->date_created,
                "ticketviaemail" => "",
                "viewed_by" => "",
                "viewed_on" => $thread->seen
            ];

            $row = MJTC_includer::MJTC_getTable('replies');
            $MJTC_data = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($replyData);

            $error = 0;
            if (!$row->bind($MJTC_data)) {
                $error = 1;
            }
            if (!$row->store()) {
                $error = 1;
            }

            $ms_ticket_reply_id = $row->id;

            if (!empty($ms_ticket_reply_id)) {
                $this->getSupportCandyTicketAttachments($ms_ticket_id, $ms_ticket_reply_id, $thread->id, $attachmentdir);
            }
        }
    }

    private function getSupportCandyNoteAttachments($MJTC_ticket_id, $attachments, $attachmentdir){
        // Split by pipe
        $parts = explode('|', $attachments);

        // Get the first numeric value
        $attachment_id = isset($parts[0]) ? intval($parts[0]) : null;

        if (empty($attachment_id)) return;

        $query = "
        SELECT attachment.*
            FROM `" . majesticsupport::$_db->prefix . "psmsc_attachments` AS attachment
            WHERE attachment.id = " . (int)$attachment_id;
                    
        $attachment = majesticsupport::$_db->get_row($query);

        if (empty($attachment)) return;

        require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
        require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';

        $filesystem = new WP_Filesystem_Direct(true);
        $upload_dir = wp_upload_dir();
        $upload_path = $upload_dir['basedir'];
        $MJTC_datadirectory = majesticsupport::$_config['data_directory'];
        $path = trailingslashit($upload_path) . $MJTC_datadirectory . "/attachmentdata/ticket/" . sanitize_file_name($attachmentdir);

        if (!$filesystem->exists($path)) {
            wp_mkdir_p($path);
        }
        $safe_filename = sanitize_file_name($attachment->name);
        $source = $upload_path . $attachment->file_path;
        $destination = $path . "/" . $safe_filename;

        if (!file_exists($source)) {
            error_log("Attachment source file does not exist: " . $source);
            return '';
        }

        $result = $filesystem->copy($source, $destination, true);
        if (!$result) {
            error_log("Failed to copy attachment from $source to $destination");
            return '';
        }
        return $attachment->name;
        
    }

    private function getSupportCandyCustomFieldAttachments($MJTC_ticket_id, $field_id, $attachmentdir){
        $query = "SELECT attachment.*
                    FROM `" . majesticsupport::$_db->prefix . "psmsc_attachments` AS attachment
                    WHERE attachment.id = " . (int)$field_id;
                    
        $attachment = majesticsupport::$_db->get_row($query);

        if (empty($attachment)) return;

        require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
        require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';

        $filesystem = new WP_Filesystem_Direct(true);
        $upload_dir = wp_upload_dir();
        $upload_path = $upload_dir['basedir'];
        $MJTC_datadirectory = majesticsupport::$_config['data_directory'];
        $path = trailingslashit($upload_path) . $MJTC_datadirectory . "/attachmentdata/ticket/" . sanitize_file_name($attachmentdir);

        if (!$filesystem->exists($path)) {
            wp_mkdir_p($path);
        }
        $safe_filename = sanitize_file_name($attachment->name);
        $source = $upload_path . $attachment->file_path;
        $destination = $path . "/" . $safe_filename;

        if (!file_exists($source)) {
            error_log("Attachment source file does not exist: " . $source);
            return '';
        }

        $result = $filesystem->copy($source, $destination, true);
        if (!$result) {
            error_log("Failed to copy attachment from $source to $destination");
            return '';
        }
        return $attachment->name;
        
    }

    private function getSupportCandyTicketAttachments($ms_ticket_id, $ms_ticket_reply_id, $sc_ticket_reply_id, $attachmentdir){
        $query = "SELECT attachment.*
                    FROM `" . majesticsupport::$_db->prefix . "psmsc_attachments` AS attachment
                    WHERE attachment.source_id = " . (int)$sc_ticket_reply_id . "
                    ORDER BY attachment.id ASC";
                    
        $attachments = majesticsupport::$_db->get_results($query);

        if (empty($attachments)) return;

        require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
        require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';

        $filesystem = new WP_Filesystem_Direct(true);
        $upload_dir = wp_upload_dir();
        $upload_path = $upload_dir['basedir'];
        $MJTC_datadirectory = majesticsupport::$_config['data_directory'];
        $path = trailingslashit($upload_path) . $MJTC_datadirectory . "/attachmentdata/ticket/" . sanitize_file_name($attachmentdir);

        if (!$filesystem->exists($path)) {
            wp_mkdir_p($path);
        }

        foreach ($attachments as $attachment) {
            $safe_filename = sanitize_file_name($attachment->name);
            $source = $upload_path . $attachment->file_path;
            $destination = $path . "/" . $safe_filename;

            $attachmentData = [
                "id" => "",
                "ticketid" => $ms_ticket_id,
                "replyattachmentid" => $ms_ticket_reply_id,
                "filesize" => "", // Optionally: filesize($source)
                "filename" => $safe_filename,
                "filekey" => "",
                "deleted" => "",
                "status" => "1",
                "created" => $attachment->date_created
            ];

            $row = MJTC_includer::MJTC_getTable('attachments');
            $MJTC_data = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($attachmentData);

            $error = 0;
            if (!$row->bind($MJTC_data)) {
                $error = 1;
            }
            if (!$row->store()) {
                $error = 1;
            }

            if (!file_exists($source)) {
                error_log("Attachment source file does not exist: " . $source);
                continue;
            }

            $result = $filesystem->copy($source, $destination, true);
            if (!$result) {
                error_log("Failed to copy attachment from $source to $destination");
            }
        }
    }

    private function getSupportCandyTicketPrivateCredentials($ms_ticket_id, $ms_ticket_uid, $pc_data) {
        $decoded_data = json_decode($pc_data, true);

        if (empty($decoded_data) || !isset($decoded_data['data'], $decoded_data['secure_key'], $decoded_data['secure_iv'])) {
            return; // Invalid or incomplete data
        }

        $privateCredentials = $decoded_data['data'];
        $secure_key = base64_decode($decoded_data['secure_key']);
        $secure_iv = base64_decode($decoded_data['secure_iv']);
        $cipher = 'AES-128-CBC';

        foreach ($privateCredentials as $privateCredential) {
            if (empty($privateCredential['data']) || !is_array($privateCredential['data'])) {
                continue;
            }

            $pc_data_info = '';

            foreach ($privateCredential['data'] as $entry) {
                if (!isset($entry['label'], $entry['value'])) continue;

                $decrypted_value = openssl_decrypt(
                    base64_decode($entry['value']),
                    $cipher,
                    $secure_key,
                    0,
                    $secure_iv
                );

                $label = sanitize_text_field($entry['label']);
                $MJTC_value = sanitize_text_field($decrypted_value);

                $pc_data_info .= "$label : $MJTC_value , ";
            }

            $pc_array = [
                'credentialtype' => sanitize_text_field($privateCredential['title']),
                'username'       => '',
                'password'       => '',
                'info'           => MJTC_majesticsupportphplib::MJTC_rtrim($pc_data_info, ' , ')
            ];

            $MJTC_data = [
                'id'        => '',
                'uid'       => intval($ms_ticket_uid),
                'ticketid'  => intval($ms_ticket_id),
                'status'    => 1,
                'created'   => current_time('mysql'),
            ];

            // Clean and encode credential info
            $encoded = wp_json_encode(array_filter($pc_array));
            $safe_encoded = MJTC_majesticsupportphplib::MJTC_safe_encoding($encoded);
            $MJTC_data['data'] = MJTC_includer::MJTC_getObjectClass('privatecredentials')->encrypt($safe_encoded);

            // Insert record
            if ($MJTC_data['ticketid'] > 0 && $MJTC_data['uid'] > 0) {
                $row = MJTC_includer::MJTC_getTable('privatecredentials');
                if ($row->bind($MJTC_data)) {
                    $row->store(); // Failure silently ignored here; consider logging
                }
            }
        }
    }

    private function getSupportCandyTicketActivityLog($ms_ticket_id, $sc_ticket_id) {
        $sc_ticket_id = intval($sc_ticket_id);
        $ms_ticket_id = intval($ms_ticket_id);

        if ($sc_ticket_id <= 0 || $ms_ticket_id <= 0) return;

        $query = "
            SELECT * FROM `" . majesticsupport::$_db->prefix . "psmsc_threads`
            WHERE (type = 'log' OR type = 'reply' OR type = 'note') AND ticket = ".$sc_ticket_id." ORDER BY date_created DESC ";

        $threads = majesticsupport::$_db->get_results($query);

        if (empty($threads)) return;

        foreach ($threads as $thread) {
            $MJTC_ticketid = $ms_ticket_id;

            // Get user information
            $userinfo = $this->getSupportCandyTicketCustomerInfo($thread->customer);
            $currentUserName = !empty($userinfo['customer_name']) 
                ? esc_html($userinfo['customer_name']) 
                : esc_html(__('Guest', 'majestic-support'));

            $messagetype = __('Successfully', 'majestic-support');
            $eventtype = '';
            $message = '';

            if ($thread->type === 'log') {
                $body = json_decode($thread->body);
                if (!empty($body) && isset($body->slug)) {
                    switch ($body->slug) {
                        case 'assigned_agent':
                            $eventtype = __('Assign ticket to agent', 'majestic-support');
                            $message = __('Ticket is assigned to agent by', 'majestic-support') . " ( $currentUserName )";
                            break;
                        case 'status':
                            if ($thread->customer == 0) {
                                continue 2;
                            }
                            $eventtype = __('Ticket status change', 'majestic-support');
                            $message = __('The status is changed by', 'majestic-support') . " ( $currentUserName )";
                            break;
                        case 'priority':
                            $eventtype = __('Change Priority', 'majestic-support');
                            $message = __('Ticket priority is changed by', 'majestic-support') . " ( $currentUserName )";
                            break;
                        case 'category':
                            $eventtype = __('Ticket department transfer', 'majestic-support');
                            $message = __('The department is transferred by', 'majestic-support') . " ( $currentUserName )";
                            break;
                        case 'subject':
                        case 'customer':
                            // Optionally handle or skip
                            break;
                    }
                }
            } elseif ($thread->type === 'reply') {
                $eventtype = __('REPLIED_TICKET', 'majestic-support');
                $message = __('Ticket is replied by', 'majestic-support') . " ( $currentUserName )";
            } elseif ($thread->type === 'note') {
                $eventtype = __('Post Internal Note', 'majestic-support');
                $message = __('The internal note is posted by', 'majestic-support') . " ( $currentUserName )";
            }

            if (!empty($eventtype) && !empty($message)) {
                MJTC_includer::MJTC_getModel('tickethistory')->addActivityLog(
                    $MJTC_ticketid, 1, esc_html($eventtype), esc_html($message), esc_html($messagetype)
                );
            }
        }
    }

    private function getSupportCandyTicketStaffTime($ms_ticket_id, $sc_ticket_id) {
        $sc_ticket_id = intval($sc_ticket_id);
        $ms_ticket_id = intval($ms_ticket_id);
        if ($sc_ticket_id <= 0 || $ms_ticket_id <= 0) return;

        // Get all timer logs for the given SupportCandy ticket
        $query = "
            SELECT * FROM `" . majesticsupport::$_db->prefix . "psmsc_timer_logs`
            WHERE ticket = ".$sc_ticket_id;
        $timers = majesticsupport::$_db->get_results($query);

        if (empty($timers)) return;

        foreach ($timers as $timer) {
            // Get HelpDesk staff ID from SupportCandy agent ID
            $staffid = $this->getMSAgentIdByScAgentId($timer->log_by);
            if (empty($staffid)) continue;

            $created = $timer->date_started;

            // Handle and validate interval string
            try {
                $interval = new DateInterval($timer->time_spent);
            } catch (Exception $e) {
                continue; // skip invalid time format
            }

            $timer_seconds = ($interval->d * 86400) + ($interval->h * 3600) + ($interval->i * 60) + $interval->s;
            if ($timer_seconds <= 0) continue;

            // Conflict detection
            $created_dt = new DateTime($created);
            $now = new DateTime();
            $interval_to_now = $created_dt->diff($now);
            $systemtime = ($interval_to_now->days * 86400) + ($interval_to_now->h * 3600) + ($interval_to_now->i * 60) + $interval_to_now->s;

            $conflict = ($timer_seconds > $systemtime) ? 1 : 0;

            // Prepare data
            $MJTC_data = [
                'staffid' => $staffid,
                'ticketid' => $ms_ticket_id,
                'referencefor' => 1,
                'referenceid' => 0,
                'usertime' => $timer_seconds,
                'systemtime' => $systemtime,
                'conflict' => $conflict,
                'description' => $timer->description,
                'timer_edit_desc' => $timer->description,
                'status' => 1,
                'created' => $created
            ];

            $row = MJTC_includer::MJTC_getTable('timetracking');
            $MJTC_data = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($MJTC_data);

            if (!$row->bind($MJTC_data) || !$row->store()) {
                // optionally log or count the failure
                continue;
            }
        }
    }

    private function getSupportCandyTicketCustomerInfo($customerId) {
        // Sanitize and validate customer ID
        $customerId = intval($customerId);
        if ($customerId <= 0) {
            return [
                "ms_uid" => "",
                "customer_name" => "",
                "customer_email" => ""
            ];
        }

        // Prepare secure query
        $query = "
            SELECT customer.name, customer.email, user.id AS ms_uid
            FROM `" . majesticsupport::$_db->prefix . "psmsc_customers` AS customer
            INNER JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_users` AS user
                ON user.wpuid = customer.user
            WHERE customer.id = " . esc_sql($customerId) . "
            LIMIT 1
        ";

        $MJTC_data = majesticsupport::$_db->get_row($query);

        return [
            "ms_uid"       => $MJTC_data->ms_uid ?? "",
            "customer_name"  => $MJTC_data->name ?? "",
            "customer_email" => $MJTC_data->email ?? ""
        ];
    }

    private function getMSAgentIdByScAgentId($sc_agent_id) {
        // Sanitize and validate input
        $sc_agent_id = intval($sc_agent_id);
        if ($sc_agent_id <= 0) return null;

        // Secure SQL query using prepare()
        $query = "
            SELECT agent.*
            FROM `" . majesticsupport::$_db->prefix . "psmsc_agents` AS sc_agent
            INNER JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_users` AS user
                ON user.wpuid = sc_agent.user
            INNER JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_staff` AS agent
                ON agent.uid = user.id
            WHERE sc_agent.id = " . esc_sql($sc_agent_id) . "
            LIMIT 1
        ";

        $ms_agent = majesticsupport::$_db->get_row($query);

        return $ms_agent ?: null;
    }

    private function getTicketAgentIdBySupportCandy($customerId) {
        // Validate customer ID
        $customerId = intval($customerId);
        if ($customerId <= 0) {
            return null;
        }

        // Get mapped user info
        $ms_user = $this->getSupportCandyTicketCustomerInfo($customerId);
        if (empty($ms_user['ms_uid'])) {
            return null;
        }

        $uid = intval($ms_user['ms_uid']);

        // Securely query agent by UID
        $query = "
            SELECT id FROM `" . majesticsupport::$_db->prefix . "mjtc_support_staff` WHERE uid = ".$uid;
            $ms_agent_id = majesticsupport::$_db->get_var($query);

        return $ms_agent_id ? (int)$ms_agent_id : null;
    }
    
    private function getTicketDepartmentIdBySupportCandy($categoryId) {
        // Validate and sanitize category ID
        $categoryId = intval($categoryId);
        if ($categoryId <= 0) return null;

        // Get department (category) name from old table
        $query = "
            SELECT name FROM `" . majesticsupport::$_db->prefix . "psmsc_categories` WHERE id = ".$categoryId;
        $category_name = majesticsupport::$_db->get_var($query);

        if (empty($category_name)) return null;

        // Match department by name (case-insensitive)
        $query = "
            SELECT id FROM `" . majesticsupport::$_db->prefix . "mjtc_support_departments` WHERE LOWER(departmentname) = '".MJTC_majesticsupportphplib::MJTC_strtolower(MJTC_majesticsupportphplib::MJTC_trim(esc_sql($category_name)))."'";
        $ms_department_id = majesticsupport::$_db->get_var($query);

        return $ms_department_id ? (int)$ms_department_id : null;
    }

    private function getTicketStatusIdBySupportCandy($statusId) {
        // Sanitize and validate input
        $statusId = intval($statusId);
        if ($statusId <= 0) return null;

        // Get status name from source table
        $query = "
            SELECT name FROM `" . majesticsupport::$_db->prefix . "psmsc_statuses` WHERE id = ".$statusId;
        $status_name = majesticsupport::$_db->get_var($query);

        if (empty($status_name)) return null;

        // Find matching status in destination table
        $query = "SELECT id FROM `" . majesticsupport::$_db->prefix . "mjtc_support_statuses` WHERE LOWER(status) = '".MJTC_majesticsupportphplib::MJTC_strtolower(MJTC_majesticsupportphplib::MJTC_trim(esc_sql($status_name)))."'";
        $ms_status_id = majesticsupport::$_db->get_var($query);

        return $ms_status_id ? (int)$ms_status_id : null;
    }

    private function getTicketPriorityIdBySupportCandy($priorityId) {
        // Sanitize and validate input
        $priorityId = intval($priorityId);
        if ($priorityId <= 0) return null;

        // Fetch priority from source table
        $query = "
            SELECT name
            FROM `" . majesticsupport::$_db->prefix . "psmsc_priorities` 
            WHERE id = ".$priorityId;
        $priority_name = majesticsupport::$_db->get_var($query);

        if (empty($priority_name)) return null;

        // Find corresponding priority in destination table
        $query = "
            SELECT id FROM `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` 
                WHERE LOWER(priority) = '".MJTC_majesticsupportphplib::MJTC_strtolower(MJTC_majesticsupportphplib::MJTC_trim(esc_sql($priority_name)))."'";
            $ms_priority_id = majesticsupport::$_db->get_var($query);

        return $ms_priority_id ? (int)$ms_priority_id : null;
    }

    private function getAgentRoleIdBySupportCandy($roleId) {
        // Get stored agent roles
        $roles = get_option('wpsc-agent-roles', array());

        // Get role label for the given role ID
        $role_label = isset($roles[$roleId]['label']) ? MJTC_majesticsupportphplib::MJTC_trim($roles[$roleId]['label']) : '';

        if (!empty($role_label)) {
            // Prepare and execute safe SQL query
            $query = "SELECT id
                FROM `" . majesticsupport::$_db->prefix . "mjtc_support_acl_roles` 
                WHERE LOWER(name) = '" . MJTC_majesticsupportphplib::MJTC_strtolower(esc_sql($role_label)) . "'";
            $ms_roleid = majesticsupport::$_db->get_var($query);

            return $ms_roleid ? (int)$ms_roleid : null;
        }

        return null;
    }

    private function importSupportCandyUsers() {
        // check if user already processed for import
        $imported_users = array();
        $imported_users_json = get_option('mjtc_support_ticket_support_candy_data_users');
        if(!empty($imported_users_json)){
            $imported_users = json_decode($imported_users_json,true);
        }

        // Fetch all customers
        $query = "SELECT * FROM `" . majesticsupport::$_db->prefix . "psmsc_customers`";
        $customers = majesticsupport::$_db->get_results($query);

        if (empty($customers)) return;

        foreach ($customers as $customer) {
            $customer_id = intval($customer->id);
            $wpuid       = intval($customer->user);
            $name        = sanitize_text_field($customer->name ?? '');
            $email       = sanitize_email($customer->email ?? '');

            // Skip if already imported
            if (in_array($customer_id, $imported_users, true)) {
                $this->support_candy_import_count['user']['skipped']++;
                continue;
            }

            // Check if user already exists
            $user_query = "SELECT user.*
                       FROM `" . majesticsupport::$_db->prefix . "mjtc_support_users` AS user
                       WHERE user.wpuid = ".$wpuid;
            $existing_user = majesticsupport::$_db->get_row($user_query);

            if ($existing_user) {
                $this->support_candy_import_count['user']['skipped']++;
                continue;
            }

            // Prepare data for new user
            $row = MJTC_includer::MJTC_getTable('users');
            $MJTC_data = [
                'id'            => '',
                'wpuid'         => $wpuid,
                'name'          => $name,
                'display_name'  => $name,
                'user_email'    => $email,
                'status'        => 1,
                'issocial'      => 0,
                'socialid'      => null,
                'autogenerated' => 0,
            ];

            // Attempt to save the new user
            $row->bind($MJTC_data);
            if (!$row->store()) {
                $this->support_candy_import_count['user']['failed']++;
                continue;
            }

            // Store successful import info
            $this->support_candy_users_array[$customer_id] = $row->id;
            $this->support_candy_user_ids[] = $customer_id;
            $this->support_candy_import_count['user']['imported']++;
        }

        // Save list of imported user IDs
        if (!empty($this->support_candy_user_ids)) {
            update_option('mjtc_support_ticket_support_candy_data_users', wp_json_encode(array_unique(array_merge($imported_users, $this->support_candy_user_ids))));
        }
    }

    private function importSupportCandyTicketFields() {
        // Get all ticket-related custom fields
        $query = "
            SELECT * FROM `" . majesticsupport::$_db->prefix . "psmsc_custom_fields`
            WHERE slug LIKE 'cust_%' AND type LIKE 'cf_%'
            AND field = 'ticket';";
        $custom_fields = majesticsupport::$_db->get_results($query);

        if (!$custom_fields) return;

        // Get visibility settings
        $MJTC_ticket_field_options = get_option("wpsc-tff");

        $this->sc_ticket_custom_fields = [];
        $this->sc_ticket_custom_fields_custom = [];

        foreach ($custom_fields as $custom_field) {
            $slug = esc_sql($custom_field->slug);

            // Map field types
            switch ($custom_field->type) {
                case "cf_textfield":
                case "cf_number":
                case "cf_url":
                case "cf_time":
                    $fieldtype = "text"; break;
                case "cf_multi_select":
                    $fieldtype = "multiple"; break;
                case "cf_single_select":
                    $fieldtype = "combo"; break;
                case "cf_radio_button":
                    $fieldtype = "radio"; break;
                case "cf_checkbox":
                    $fieldtype = "checkbox"; break;
                case "cf_textarea":
                    $fieldtype = "textarea"; break;
                case "cf_date":
                case "cf_datetime":
                    $fieldtype = "date"; break;
                case "cf_email":
                    $fieldtype = "email"; break;
                case "cf_file_attachment_multiple":
                case "cf_file_attachment_single":
                    $fieldtype = "file"; break;
                case "cf_edd_order":
                    $this->sc_ticket_custom_fields[] = [
                        "name" => $slug,
                        "type" => 'cf_edd_order',
                        "ms_filedorderingid" => '',
                        "ms_filedorderingfield" => '',
                    ];
                    $this->support_candy_import_count['field']['skipped'] += 1;
                    continue 2; break;
                case "cf_edd_product":
                    $this->sc_ticket_custom_fields[] = [
                        "name" => $slug,
                        "type" => 'cf_edd_product',
                        "ms_filedorderingid" => '',
                        "ms_filedorderingfield" => '',
                    ];
                    $this->support_candy_import_count['field']['skipped'] += 1;
                    continue 2; break;
                case "cf_woo_order":
                    $this->sc_ticket_custom_fields[] = [
                        "name" => $slug,
                        "type" => 'cf_woo_order',
                        "ms_filedorderingid" => '',
                        "ms_filedorderingfield" => '',
                    ];
                    $this->support_candy_import_count['field']['skipped'] += 1;
                    continue 2; break;
                case "cf_woo_product":
                    $this->sc_ticket_custom_fields[] = [
                        "name" => $slug,
                        "type" => 'cf_woo_product',
                        "ms_filedorderingid" => '',
                        "ms_filedorderingfield" => '',
                    ];
                    $this->support_candy_import_count['field']['skipped'] += 1;
                    continue 2; break;
                default:
                    $fieldtype = "text"; break;
            }

            $query = "SELECT id,field FROM `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` WHERE isuserfield = 1 AND LOWER(fieldtitle) ='".esc_sql(MJTC_majesticsupportphplib::MJTC_strtolower($custom_field->name))."' AND userfieldtype ='".esc_sql($fieldtype)."' AND fieldfor = 1";
            $field_record = majesticsupport::$_db->get_row($query);

            if(!empty($field_record)){ // this will make sure
                $this->support_candy_import_count['field']['skipped'] += 1;
                continue;
            }
            // Load options for select-type fields
            $option_values = [];

            $table = majesticsupport::$_db->prefix . "psmsc_tickets";
            $column = $custom_field->slug;

            // Get all columns from the table only once
            static $existing_columns = null;

            if ($existing_columns === null) {
                $existing_columns = majesticsupport::$_db->get_col("SHOW COLUMNS FROM `$table`");
            }

            // Check if the column exists
            if (in_array($column, $existing_columns)) {
                $query = "SELECT `" . $column . "` FROM `" . $table . "`";
                $field = majesticsupport::$_db->get_row($query);
            } else {
                $field = null; // Column doesn't exist
            }
            if(isset($field)){ // field in the ticket table
                $query = "SELECT name FROM `" . majesticsupport::$_db->prefix . "psmsc_options` WHERE custom_field = ".$custom_field->id." ORDER BY load_order;";
                $field_options = majesticsupport::$_db->get_results($query);
                if ($field_options) {
                    foreach ($field_options as $field_option) {
                        $option_values[] = $field_option->name;
                    }
                }
            }

            // Build visibility data
            $visibledata = [
                "visibleLogic" => [],
                "visibleParent" => [],
                "visibleValue" => [],
                "visibleCondition" => [],
            ];

            $MJTC_defaultvalue_input = "";
            $MJTC_defaultvalue_select = "";
            if($fieldtype == "combo" || $fieldtype == "radio" || $fieldtype == "multiple" || $fieldtype == "checkbox" || $fieldtype == "depandant_field") {

                $field_ids = explode('|', $custom_field->default_value);

                // Sanitize and cast to integers
                $field_id = isset($field_ids[0]) ? intval($field_ids[0]) : null;

                // Check if we have valid IDs
                if (!empty($field_id)) {
                    $query = "SELECT name FROM `" . majesticsupport::$_db->prefix . "psmsc_options` WHERE id = " . $field_id;
                    $name = majesticsupport::$_db->get_col($query);

                    // Combine names into comma-separated string
                    $vardata = !empty($name) ? implode(', ', $name) : '';
                } else {
                    $vardata = '';
                }

                $MJTC_defaultvalue_select = $vardata;
            } else {
                $MJTC_defaultvalue_input = $custom_field->default_value;
            }

            // Prepare field data for import
            $fieldOrderingData = [
                "id" => "",
                "field" => $slug,
                "fieldtitle" => $custom_field->name,
                "ordering" => "",
                "section" => "10",
                "placeholder" => $custom_field->placeholder_text,
                "description" => $custom_field->extra_info,
                "fieldfor" => "1",
                "published" => "1",
                "sys" => "0",
                "cannotunpublish" => "0",
                "required" => "0",
                "size" => "100",
                "maxlength" => $custom_field->char_limit,
                "cols" => "",
                "rows" => "",
                "isuserfield" => "1",
                "userfieldtype" => $fieldtype,
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
                "values" => $option_values,
                "visibleParent" => $visibledata["visibleParent"],
                "visibleValue" => $visibledata["visibleValue"],
                "visibleCondition" => $visibledata["visibleCondition"],
                "visibleLogic" => $visibledata["visibleLogic"],
                "readonly" => 0,
                "adminonly" => 0,
                "defaultvalue_select" => $MJTC_defaultvalue_select,
                "defaultvalue_input" => $MJTC_defaultvalue_input,
            ];

            // Store field in SupportCandy
            $record_saved = MJTC_includer::MJTC_getModel('fieldordering')->storeUserField($fieldOrderingData);

            if ($record_saved == 1) {
                $query = "SELECT * FROM `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` ORDER BY id DESC LIMIT 1";
                    $latest_record = majesticsupport::$_db->get_row($query);

                $this->sc_ticket_custom_fields[] = [
                    "name" => $slug,
                    "type" => $fieldtype,
                    "ms_filedorderingid" => $latest_record->id,
                    "ms_filedorderingfield" => $latest_record->field,
                ];
                $this->sc_ticket_custom_fields_custom[$custom_field->slug] = $latest_record->field;
                
                $this->support_candy_import_count['field']['imported'] += 1;
            } else {
                $this->support_candy_import_count['field']['failed'] += 1;
                // Optionally log: error_log("Failed to import field: $slug");
            }
        }

        foreach ($custom_fields as $custom_field) {
            $slug = $custom_field->slug;
            if (!empty($MJTC_ticket_field_options[$slug]['visibility'])) {
                $visibility_conditions = json_decode($MJTC_ticket_field_options[$slug]['visibility']);
                $field = $this->getTicketCustomFieldId($custom_field->name);
                $query = "SELECT * FROM `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` WHERE field = '".esc_sql($field)."' LIMIT 1";
                $ms_field = majesticsupport::$_db->get_row($query);
                if (empty($ms_field)) {
                    continue;
                }

                // Build visibility data
                $visibledata = [
                    "visibleLogic" => [],
                    "visibleParent" => [],
                    "visibleValue" => [],
                    "visibleCondition" => [],
                ];
                if ($visibility_conditions) {
                    foreach ($visibility_conditions as $visibility_condition) {
                        $visibleLogic = 'AND';
                        foreach ($visibility_condition as $groupIndex => $group) {
                            $fieldtype = '';
                            if ($group->slug == 'usergroups' || $group->slug == 'description' || $group->slug == 'assigned_agent') {
                                continue;
                            }
                            if ($group->slug == 'priority') {
                                $item_key = 'priority';
                                $MJTC_value = $this->getTicketPriorityIdBySupportCandy($group->operand_val_1);
                                $fieldtype = 'priority';
                            } elseif ($group->slug == 'category') {
                                $item_key = 'department';
                                $MJTC_value = $this->getTicketDepartmentIdBySupportCandy($group->operand_val_1);
                                $fieldtype = 'department';
                            } elseif ($group->slug == 'subject') {
                                $item_key = 'subject';
                                $MJTC_value = $group->operand_val_1;
                                $fieldtype = 'subject';
                            } else {
                                // $item_key = $group->slug;
                                $item_key = $this->sc_ticket_custom_fields_custom[$group->slug];
                                $fieldtype = $this->checkTypeOfTheField($item_key);
                                if ($fieldtype == 'textarea') {
                                    continue;
                                } elseif ($this->sc_ticket_custom_fields_custom[$custom_field->slug] == $item_key) {
                                    continue;
                                }

                                if (in_array(strtolower($fieldtype), ['multiple', 'checkbox', 'combo', 'radio'])) {
                                    $field_ids = explode('|', $group->operand_val_1[0]);

                                    // Sanitize and cast to integers
                                    $field_ids = array_map('intval', array_filter($field_ids));

                                    // Check if we have valid IDs
                                    if (!empty($field_ids)) {
                                        $placeholders = implode(',', $field_ids);
                                        $query = "SELECT name FROM `" . majesticsupport::$_db->prefix . "psmsc_options` WHERE id IN ($placeholders)";
                                        $names = majesticsupport::$_db->get_col($query);

                                        // Combine names into comma-separated string
                                        $MJTC_value = !empty($names) ? implode(', ', $names) : '';
                                    } else {
                                        $MJTC_value = '';
                                    }
                                } else {
                                    $MJTC_value = $group->operand_val_1;
                                }
                            }
                            if ($custom_field->slug == 'priority') {
                                $slug = 'priority';
                            } elseif ($custom_field->slug == 'category') {
                                $slug = 'department';
                            } elseif ($custom_field->slug == 'subject') {
                                $slug = 'subject';
                            } else {
                                $slug = $this->sc_ticket_custom_fields_custom[$custom_field->slug];
                            }
                            
                            $visibledata["visibleParentField"][] = $slug;
                            $visibledata["visibleParent"][] = $item_key;
                            $visibledata["visibleCondition"][] = $this->mapOperatorToConditionCode($group->operator, $fieldtype);
                            $visibledata["visibleValue"][] = $MJTC_value;
                            $visibledata["visibleLogic"][] = $visibleLogic;
                            $visibleLogic = 'OR';
                        }
                        // remove default value in case of visiblity
                        $ms_field->defaultvalue = '';

                    }
                }

                $option_values = [];
                if(isset($ms_field->userfieldparams)){
                    $options = json_decode($ms_field->userfieldparams, true);
                    foreach($options as $MJTC_key => $MJTC_value){
                        $option_values[] = $MJTC_value;
                    }
                }
                
                // Prepare field data for import

                $fieldOrderingData = [
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
                    "values" => $option_values,
                    "visibleParent" => $visibledata["visibleParent"],
                    "visibleValue" => $visibledata["visibleValue"],
                    "visibleCondition" => $visibledata["visibleCondition"],
                    "visibleLogic" => $visibledata["visibleLogic"],
                ];

                // Store field in SupportCandy
                $record_saved = MJTC_includer::MJTC_getModel('fieldordering')->storeUserField($fieldOrderingData);
            }
        }
    }

    private function mapOperatorToConditionCode($operator, $type) {
        $operator = strtoupper(MJTC_majesticsupportphplib::MJTC_trim($operator));
        $isComplex = false;

        if (!empty($type)) {
            $complexTypes = ['combo', 'checkbox', 'radio', 'multiple','priority','department'];
            $isComplex = !in_array($type, $complexTypes);
        }

        switch ($operator) {
            case '=':
            case 'LIKE':
            case 'IN':
                return $isComplex ? "2" : "1";

            case 'NOT IN':
                return $isComplex ? "3" : "0";

            default:
                return $isComplex ? "3" : "0";
        }
    }

    // clean
    private function importSupportCandyAgents() {
        // check if user already processed for import
        $imported_agents = array();
        $imported_agent_json = get_option('mjtc_support_ticket_support_candy_data_agents');
        if(!empty($imported_agents_json)){
            $imported_agents = json_decode($imported_agents_json,true);
        }
        $query = "SELECT agent.*
                    FROM `" . majesticsupport::$_db->prefix . "psmsc_agents` AS agent;";
        $agents = majesticsupport::$_db->get_results($query);
        $total_agents = count($agents);

        if($agents){
            foreach($agents AS $agent){
                // Failed if addon not installed
                if (!in_array('agent', majesticsupport::$_active_addons) ) {
                    $this->support_candy_import_count['agent']['failed']++;
                    continue;
                }
                $wpuid = (int) $agent->user;
                // Skip if already imported
                if (in_array($wpuid, $imported_agents, true)) {
                    $this->support_candy_import_count['agent']['skipped']++;
                    continue;
                }
                $name = $agent->name;

                $query = "
                    SELECT user.*
                        FROM `" . majesticsupport::$_db->prefix . "users` AS user
                        WHERE user.id = " . $wpuid;
                $wpuser = majesticsupport::$_db->get_row($query);

                if(!$wpuser){
                    $this->support_candy_import_count['agent']['failed'] += 1;
                    continue;
                }
                $mjtc_user = MJTC_includer::MJTC_getObjectClass('user')->MJTC_getmajesticsupportuidbyuserid($wpuid);
                if (!empty($mjtc_user) && isset($mjtc_user[0]->id)) {
                    $mjtc_uid = (int)$mjtc_user[0]->id;
                } else {
                    $this->support_candy_import_count['agent']['failed']++;
                    continue;
                }

                $query = "
                    SELECT staff.*
                        FROM `" . majesticsupport::$_db->prefix . "mjtc_support_staff` AS staff
                            WHERE staff.uid = " . $mjtc_uid;
                $staff = majesticsupport::$_db->get_row($query);

                if (!$staff) {
                    
                    $timestamp = date_i18n('Y-m-d H:i:s');

                    $MJTC_data = [
                        'id'           => '',
                        'uid'          => $mjtc_uid,
                        'groupid'      => '',
                        'roleid'       => $this->getAgentRoleIdBySupportCandy($agent->role),
                        'departmentid' => '',
                        'firstname'    => $name,
                        'lastname'     => '',
                        'username'     => $wpuser->user_login,
                        'email'        => $wpuser->user_email,
                        'signature'    => '',
                        'isadmin'      => '',
                        'status'       => $agent->is_active,
                        'updated'      => $timestamp,
                        'created'      => $timestamp
                    ];

                    $saved = MJTC_includer::MJTC_getModel('agent')->storeStaff($MJTC_data);

                    $this->support_candy_import_count['agent']['imported'] += 1;
                    $this->support_candy_agent_ids[] = $wpuid;
                } else {
                    $this->support_candy_import_count['agent']['skipped'] += 1;
                }
            }
            // Save list of imported agent IDs
            if (!empty($this->support_candy_agent_ids)) {
                update_option('mjtc_support_ticket_support_candy_data_agents', wp_json_encode(array_unique(array_merge($imported_agents, $this->support_candy_agent_ids))));
            }
        }
    }

    private function importSupportCandyAgentsRoles() {
        // check if role already processed for import
        $imported_agent_roles = array();
        $imported_agent_role_json = get_option('mjtc_support_ticket_support_candy_data_agent_roles');
        if(!empty($imported_agent_roles_json)){
            $imported_agent_roles = json_decode($imported_agent_roles_json,true);
        }

        $wpsc_agent_roles = get_option('wpsc-agent-roles', []);

        // Mapping role labels to permission keys
        $permissionMap = [
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
        $permissionIds = [];
        foreach ($permissionMap as $label => $_) {
            if (in_array('agent', majesticsupport::$_active_addons) ) {
                $escapedLabel = esc_sql($label);
                $sql = "SELECT id FROM `" . majesticsupport::$_db->prefix . "mjtc_support_acl_permissions` WHERE permission = '{$escapedLabel}' LIMIT 1";
                $permissionIds[$label] = (int) majesticsupport::$_db->get_var($sql);
            }
        }

        foreach ($wpsc_agent_roles as $role) {
            // Failed if addon not installed
            if (!in_array('agent', majesticsupport::$_active_addons) ) {
                $this->support_candy_import_count['agent_role']['failed']++;
                continue;
            }
            // Skip if already imported
            if (in_array($role['label'], $imported_agent_roles, true)) {
                $this->support_candy_import_count['agent_role']['skipped']++;
                continue;
            }
            $query = "SELECT count(id)
                    FROM `" . majesticsupport::$_db->prefix . "mjtc_support_acl_roles` WHERE name ='".esc_sql($role['label'])."'";
            $agent_role = majesticsupport::$_db->get_var($query);

            if($agent_role == 0){
                $output = [];
                $caps = $role['caps'] ?? [];

                foreach ($permissionMap as $label => $permissions) {
                    foreach ($permissions as $perm) {
                        if (!empty($caps[$perm])) {
                            // Assign permission ID for this label if exists
                            if (!empty($permissionIds[$label])) {
                                $output[$label] = $permissionIds[$label];
                            }
                            break; // Stop checking other permissions for this label
                        }
                    }
                }

                $MJTC_data = [
                    'name'          => $role['label'],
                    'roleperdata'   => $output,
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
                $this->support_candy_agent_role_ids[] = $role['label'];
            } else {
                $this->support_candy_import_count['agent_role']['skipped'] += 1;
            }
        }
        // Save list of imported agent_role IDs
        if (!empty($this->support_candy_agent_role_ids)) {
            update_option('mjtc_support_ticket_support_candy_data_agent_roles', wp_json_encode(array_unique(array_merge($imported_agent_roles, $this->support_candy_agent_role_ids))));
        }

    }

    private function importSupportCandyDepartments() {
        // check if department already processed for import
        $imported_departments = array();
        $imported_departments_json = get_option('mjtc_support_ticket_support_candy_data_departments');
        if(!empty($imported_departments_json)){
            $imported_departments = json_decode($imported_departments_json,true);
        }
        $query = "SELECT category.* FROM `" . majesticsupport::$_db->prefix . "psmsc_categories` AS category;";
        $categories = majesticsupport::$_db->get_results($query);

        if (empty($categories)) return;

        // Get highest current ordering value
        $query = "
            SELECT MAX(dept.ordering)
            FROM `" . majesticsupport::$_db->prefix . "mjtc_support_departments` AS dept
        ";
        $ordering = (int) majesticsupport::$_db->get_var($query);
        $now = date_i18n('Y-m-d H:i:s');

        foreach ($categories as $category) {
            // Skip if already imported
            if (in_array($category->id, $imported_departments, true)) {
                $this->support_candy_import_count['department']['skipped']++;
                continue;
            }
            $name = MJTC_majesticsupportphplib::MJTC_trim($category->name);
            $lower_name = MJTC_majesticsupportphplib::MJTC_strtolower($name);

            // Check if department already exists
            $check_query = "
                SELECT department.*
                FROM `" . majesticsupport::$_db->prefix . "mjtc_support_departments` AS department
                WHERE LOWER(department.departmentname) = '".esc_sql($name)."'
            ";
            $existing = majesticsupport::$_db->get_row($check_query);

            if (!$existing) {
                $row = MJTC_includer::MJTC_getTable('departments');

                $MJTC_data = [
                    'id'              => '',
                    'emailid'         => '1',
                    'departmentname'  => $name,
                    'ordering'        => $ordering,
                    'status'          => '1',
                    'isdefault'       => '0',
                    'ispublic'        => '1',
                    'updated'         => $now,
                    'created'         => $now
                ];

                $MJTC_data = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($MJTC_data);
                $row->bind($MJTC_data);

                if (!$row->store()) {
                    $this->support_candy_import_count['department']['failed'] += 1;
                } else {
                    $this->support_candy_department_ids[] = $category->id;
                    $this->support_candy_import_count['department']['imported'] += 1;
                }

                $ordering++;
            } else {
                $this->support_candy_import_count['department']['skipped'] += 1;
            }
        }
        // Save list of imported department IDs
        if (!empty($this->support_candy_department_ids)) {
            update_option('mjtc_support_ticket_support_candy_data_departments', wp_json_encode(array_unique(array_merge($imported_departments, $this->support_candy_department_ids))));
        }
    }

    private function importSupportCandyPriorities() {
        // check if priority already processed for import
        $imported_priorities = array();
        $imported_priorities_json = get_option('mjtc_support_ticket_support_candy_data_priorities');
        if(!empty($imported_priorities_json)){
            $imported_priorities = json_decode($imported_priorities_json,true);
        }

        $query = "SELECT priority.* FROM `" . majesticsupport::$_db->prefix . "psmsc_priorities` AS priority;";
        $priorities = majesticsupport::$_db->get_results($query);

        if (empty($priorities)) return;

        // Get highest current ordering value
        $query = "
            SELECT MAX(priority.ordering)
            FROM `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` AS priority
        ";
        $ordering = (int) majesticsupport::$_db->get_var($query);

        foreach ($priorities as $priority) {
            // Skip if already imported
            if (in_array($priority->id, $imported_priorities, true)) {
                $this->support_candy_import_count['priority']['skipped']++;
                continue;
            }

            $name = MJTC_majesticsupportphplib::MJTC_trim(MJTC_majesticsupportphplib::MJTC_strtolower($priority->name));

            // Check if this priority already exists in Majestic Support
            $check_query = "
                SELECT priority.*
                FROM `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` AS priority
                WHERE LOWER(priority.priority) = '" . esc_sql($name) . "'
                LIMIT 1
            ";
            $ms_priority = majesticsupport::$_db->get_row($check_query);

            if (!$ms_priority) {
                $row = MJTC_includer::MJTC_getTable('priorities');

                $MJTC_data = [
                    'id'               => '',
                    'priority'         => $priority->name,
                    'prioritycolour'   => $priority->color,
                    'priorityurgency'  => '',
                    'overduetypeid'    => 1,
                    'overdueinterval'  => 7,
                    'ordering'         => $ordering,
                    'status'           => '1',
                    'isdefault'        => '0',
                    'ispublic'         => '1'
                ];

                $MJTC_data = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($MJTC_data);
                $row->bind($MJTC_data);

                if (!$row->store()) {
                    $this->support_candy_import_count['priority']['failed'] += 1;
                } else {
                    $this->support_candy_priority_ids[] = $priority->id;
                    $this->support_candy_import_count['priority']['imported'] += 1;
                }

                $ordering++;
            } else {
                $this->support_candy_import_count['priority']['skipped'] += 1;
            }
        }
        // Save list of imported priority IDs
        if (!empty($this->support_candy_priority_ids)) {
            update_option('mjtc_support_ticket_support_candy_data_priorities', wp_json_encode(array_unique(array_merge($imported_priorities, $this->support_candy_priority_ids))));
        }
    }

    private function importSupportCandyPremades() {
        // check if premade already processed for import
        $imported_premades = array();
        $imported_premades_json = get_option('mjtc_support_ticket_support_candy_data_premades');
        if(!empty($imported_premades_json)){
            $imported_premades = json_decode($imported_premades_json,true);
        }
        $query = "
            SELECT canned_reply.*
            FROM `" . majesticsupport::$_db->prefix . "psmsc_canned_reply` AS canned_reply
        ";
        $canned_replies = majesticsupport::$_db->get_results($query);

        if (empty($canned_replies)) return;

        foreach ($canned_replies as $canned_reply) {
            $title = MJTC_majesticsupportphplib::MJTC_trim(MJTC_majesticsupportphplib::MJTC_strtolower($canned_reply->title));
            // Failed if addon not installed
            if (!in_array('cannedresponses', majesticsupport::$_active_addons) ) {
                $this->support_candy_import_count['canned response']['failed']++;
                continue;
            }
            // Skip if already imported
            if (in_array($canned_reply->id, $imported_premades, true)) {
                $this->support_candy_import_count['canned response']['skipped']++;
                continue;
            }
            // Check if this priority already exists in Majestic Support
            $check_query = "
                SELECT premade.*
                FROM `" . majesticsupport::$_db->prefix . "mjtc_support_department_message_premade` AS premade
                WHERE LOWER(premade.title) = '" . esc_sql($title) . "'
                LIMIT 1
            ";
            $ms_canned_reply = majesticsupport::$_db->get_row($check_query);

            if (!$ms_canned_reply) {

                $MJTC_departmentid = '';

                // Try to match category to department
                if (!empty($canned_reply->categories)) {
                    $category_query = "
                        SELECT category.name
                        FROM `" . majesticsupport::$_db->prefix . "psmsc_categories` AS category
                        WHERE category.id = " . esc_sql($canned_reply->categories) . "
                    ";
                    $category = majesticsupport::$_db->get_row($category_query);

                    if ($category) {
                        $MJTC_department_query = "
                            SELECT department.id
                            FROM `" . majesticsupport::$_db->prefix . "mjtc_support_departments` AS department
                            WHERE LOWER(department.departmentname) = '" . MJTC_majesticsupportphplib::MJTC_strtolower(esc_sql($category->name)) . "'
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
                $row = MJTC_includer::MJTC_getTable('cannedresponses');
                $updated = date_i18n('Y-m-d H:i:s');

                $MJTC_data = [
                    'id'          => '',
                    'departmentid'=> $MJTC_departmentid,
                    'title'       => $canned_reply->title,
                    'answer'      => $canned_reply->body,
                    'status'      => '1',
                    'updated'     => $updated,
                    'created'     => $canned_reply->date_created
                ];

                $MJTC_data = majesticsupport::MJTC_sanitizeData($MJTC_data);
                $MJTC_data['answer'] = MJTC_includer::MJTC_getModel('majesticsupport')->getSanitizedEditorData($MJTC_data['answer']);
                $MJTC_data = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($MJTC_data);

                $row->bind($MJTC_data);
                if (!$row->store()) {
                    $this->support_candy_import_count['canned response']['failed'] += 1;
                } else {
                    $this->support_candy_premade_ids[] = $canned_reply->id;
                    $this->support_candy_import_count['canned response']['imported'] += 1;
                }
            } else {
                $this->support_candy_import_count['canned response']['skipped'] += 1;
            }
        }

        // Save list of imported premade IDs
        if (!empty($this->support_candy_premade_ids)) {
            update_option('mjtc_support_ticket_support_candy_data_premades', wp_json_encode(array_unique(array_merge($imported_premades, $this->support_candy_premade_ids))));
        }
    }

    private function importSupportCandyStatus() {
        // Load previously imported statuses
        $imported_statuses = [];
        $imported_statuses_json = get_option('mjtc_support_ticket_support_candy_data_statuses');
        if (!empty($imported_statuses_json)) {
            $imported_statuses = json_decode($imported_statuses_json, true);
        }

        // Get SupportCandy statuses (excluding system/default ones)
        $query = "
            SELECT status.*
            FROM `" . majesticsupport::$_db->prefix . "psmsc_statuses` AS status
            WHERE status.id > 4
        ";
        $statuses = majesticsupport::$_db->get_results($query);

        if (empty($statuses)) return;

        // Get highest current ordering value
        $query = "
            SELECT MAX(status.ordering)
            FROM `" . majesticsupport::$_db->prefix . "mjtc_support_statuses` AS status
        ";
        $ordering = (int) majesticsupport::$_db->get_var($query);

        // Build array of existing JS statuses (cleaned)
        $query = "
            SELECT status.status
            FROM `" . majesticsupport::$_db->prefix . "mjtc_support_statuses` AS status
        ";
        $msstatuses = majesticsupport::$_db->get_results($query);
        $existing_status_names = array_map(function($status) {
            return $this->cleanStringForCompare($status->status);
        }, $msstatuses);

        foreach ($statuses as $status) {
            $name = $status->name;
            $compare_name = $this->cleanStringForCompare($name);

            // Skip if name already exists
            if (in_array($compare_name, $existing_status_names)) {
                $this->support_candy_import_count['status']['skipped'] += 1;
                continue;
            }

            // Skip if already imported
            if (in_array($status->id, $imported_statuses)) {
                $this->support_candy_import_count['status']['skipped'] += 1;
                continue;
            }

            // Prepare new status data
            $row = MJTC_includer::MJTC_getTable('statuses');
            $MJTC_data = [
                'id'             => '',
                'status'         => $name,
                'statuscolour'   => $status->color,
                'statusbgcolour' => $status->bg_color,
                'sys'            => '0',
                'ordering'       => $ordering
            ];

            $MJTC_data = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($MJTC_data);
            $row->bind($MJTC_data);

            if (!$row->store()) {
                $this->support_candy_import_count['status']['failed'] += 1;
            } else {
                $this->support_candy_status_ids[] = $status->id;
                $this->support_candy_import_count['status']['imported'] += 1;
                $ordering++;
            }
        }

        // Save updated list of imported statuses
        if (!empty($this->support_candy_status_ids)) {
            update_option('mjtc_support_ticket_support_candy_data_statuses', wp_json_encode($this->support_candy_status_ids));
        }
    }

    private function cleanStringForCompare($string) {
        if (!is_string($string) || $string === '') {
            return $string;
        }

        // Remove spaces, dashes, and underscores
        $string = MJTC_majesticsupportphplib::MJTC_str_replace([' ', '-', '_'], '', $string);

        // Convert to lowercase
        return MJTC_majesticsupportphplib::MJTC_strtolower($string);
    }

    function getSupportCandyDataStats($MJTC_count_for) {
        // Only support SupportCandy (count_for = 1)
        if ($MJTC_count_for != 1) return;

        // Check if SupportCandy is active
        include_once ABSPATH . 'wp-admin/includes/plugin.php';
        if (!is_plugin_active('supportcandy/supportcandy.php')) {
            return new WP_Error('mjtc_inactive', 'SupportCandy is not active.');
        }

        $entity_counts = [];

        // Users
        if (majesticsupport::$_db->get_var("SHOW TABLES LIKE '" . majesticsupport::$_db->prefix . "psmsc_customers'")) {
            $query = "SELECT COUNT(*) FROM `" . majesticsupport::$_db->prefix . "psmsc_customers`";
            $MJTC_count = (int) majesticsupport::$_db->get_var($query);
            if ($MJTC_count > 0) $entity_counts['user'] = $MJTC_count;
        }

        // Agent Roles
        $agent_roles = get_option('wpsc-agent-roles', []);
        if (!empty($agent_roles)) {
            $entity_counts['agent role'] = count($agent_roles);
        }

        // Agents
        if (majesticsupport::$_db->get_var("SHOW TABLES LIKE '" . majesticsupport::$_db->prefix . "psmsc_agents'")) {
            $query = "SELECT COUNT(*) FROM `" . majesticsupport::$_db->prefix . "psmsc_agents`";
            $MJTC_count = (int) majesticsupport::$_db->get_var($query);
            if ($MJTC_count > 0) $entity_counts['agent'] = $MJTC_count;
        }

        // Departments
        if (majesticsupport::$_db->get_var("SHOW TABLES LIKE '" . majesticsupport::$_db->prefix . "psmsc_categories'")) {
            $query = "SELECT COUNT(*) FROM `" . majesticsupport::$_db->prefix . "psmsc_categories`";
            $MJTC_count = (int) majesticsupport::$_db->get_var($query);
            if ($MJTC_count > 0) $entity_counts['department'] = $MJTC_count;
        }

        // Priorities
        if (majesticsupport::$_db->get_var("SHOW TABLES LIKE '" . majesticsupport::$_db->prefix . "psmsc_priorities'")) {
            $query = "SELECT COUNT(*) FROM `" . majesticsupport::$_db->prefix . "psmsc_priorities`";
            $MJTC_count = (int) majesticsupport::$_db->get_var($query);
            if ($MJTC_count > 0) $entity_counts['priority'] = $MJTC_count;
        }

        // Canned Responses
        if (majesticsupport::$_db->get_var("SHOW TABLES LIKE '" . majesticsupport::$_db->prefix . "psmsc_canned_reply'")) {
            $query = "SELECT COUNT(*) FROM `" . majesticsupport::$_db->prefix . "psmsc_canned_reply`";
            $MJTC_count = (int) majesticsupport::$_db->get_var($query);
            if ($MJTC_count > 0) $entity_counts['canned response'] = $MJTC_count;
        }

        // Statuses
        if (majesticsupport::$_db->get_var("SHOW TABLES LIKE '" . majesticsupport::$_db->prefix . "psmsc_statuses'")) {
            $query = "SELECT COUNT(*) FROM `" . majesticsupport::$_db->prefix . "psmsc_statuses` WHERE id > 4";
            $MJTC_count = (int) majesticsupport::$_db->get_var($query);
            if ($MJTC_count > 0) $entity_counts['status'] = $MJTC_count;
        }

        // Custom Ticket Fields
        if (majesticsupport::$_db->get_var("SHOW TABLES LIKE '" . majesticsupport::$_db->prefix . "psmsc_custom_fields'")) {
            $query = "SELECT COUNT(*) 
                      FROM `" . majesticsupport::$_db->prefix . "psmsc_custom_fields`
                      WHERE `slug` LIKE 'cust_%' AND `type` LIKE 'cf_%' AND `field` = 'ticket'";
            $MJTC_count = (int) majesticsupport::$_db->get_var($query);
            if ($MJTC_count > 0) $entity_counts['field'] = $MJTC_count;
        }

        // Tickets with type 'report'
        if (majesticsupport::$_db->get_var("SHOW TABLES LIKE '" . majesticsupport::$_db->prefix . "psmsc_tickets'")) {
            $query = "SELECT COUNT(DISTINCT t.id)
                      FROM `" . majesticsupport::$_db->prefix . "psmsc_tickets` AS t
                      INNER JOIN `" . majesticsupport::$_db->prefix . "psmsc_threads` AS r ON r.ticket = t.id
                      WHERE r.type = 'report'  AND t.is_active != 0";
            $MJTC_count = (int) majesticsupport::$_db->get_var($query);
            if ($MJTC_count > 0) $entity_counts['ticket'] = $MJTC_count;
        }

        majesticsupport::$_data['entity_counts'] = $entity_counts;
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
        $filesystem = new WP_Filesystem_Direct(true);
        $upload_dir = wp_upload_dir();
        $upload_path = $upload_dir['basedir'];
        $MJTC_datadirectory = majesticsupport::$_config['data_directory'];
        $path = $upload_path . "/" . $MJTC_datadirectory;

        if (!$filesystem->exists($path)) {
            wp_mkdir_p($path);
        }
        $path .= '/attachmentdata';
        if (!$filesystem->exists($path)) {
            wp_mkdir_p($path);
        }
        $path .= '/ticket';
        if (!$filesystem->exists($path)) {
            wp_mkdir_p($path);
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

        $entity_counts = [];

        // Users
        $missingUser = 0;
        $query = "SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "users`";
        $users = majesticsupport::$_db->get_var($query);
        if ($users > 0) $entity_counts['user'] = $users;

        // Agents
        $agents = get_users([
            'role' => 'wpas_agent',
        ]);
        $MJTC_count = count($agents);
        if ($MJTC_count > 0) $entity_counts['agent'] = $MJTC_count;

        // Departments
        $MJTC_departments = get_terms([
            'taxonomy'   => 'department',
            'hide_empty' => false,
            'fields'     => 'ids'
        ]);
        $MJTC_count = is_array($MJTC_departments) ? count($MJTC_departments) : 0;
        if ($MJTC_count > 0) $entity_counts['department'] = $MJTC_count;

        // Priorities
        $priorities = get_terms([
            'taxonomy'   => 'ticket_priority',
            'hide_empty' => false,
            'fields'     => 'ids'
        ]);
        $MJTC_count = is_array($priorities) ? count($priorities) : 0;
        if ($MJTC_count > 0) $entity_counts['priority'] = $MJTC_count;

        // Canned Responses
        $MJTC_count = post_type_exists( 'canned-response' ) ? $this->getPostConutByType( 'canned-response' ) : 0;
        if ($MJTC_count > 0) $entity_counts['canned response'] = $MJTC_count;

        // Statuses
        $MJTC_count = post_type_exists( 'wpass_status' ) ? $this->getPostConutByType( 'wpass_status' ) : 0;
        if ($MJTC_count > 0) $entity_counts['status'] = $MJTC_count;

        // Products
        $products = get_terms([
            'taxonomy'   => 'product',
            'hide_empty' => false,
            'fields'     => 'ids'
        ]);
        $MJTC_count = is_array($products) ? count($products) : 0;
        if ($MJTC_count > 0) $entity_counts['product'] = $MJTC_count;

        // Faqs
        $MJTC_count = post_type_exists( 'faq' ) ? $this->getPostConutByType( 'faq' ) : 0;
        if ($MJTC_count > 0) $entity_counts['faq'] = $MJTC_count;

        // Custom Ticket Fields
        $custom_fields = get_option("wpas_custom_fields");

        if (!empty($custom_fields)) {
            $MJTC_count = is_array($custom_fields) ? count($custom_fields) : 0;
            if ($MJTC_count > 0) $entity_counts['field'] = $MJTC_count;
        }

        // Tickets 
        $MJTC_tickets = wpas_get_tickets('any');
        $MJTC_count = count($MJTC_tickets);

        if ($MJTC_count > 0) $entity_counts['ticket'] = $MJTC_count;

        majesticsupport::$_data['entity_counts'] = $entity_counts;
    }

    // delete data only for development

    function deletesupportcandyimporteddata(){

        $query = "DELETE FROM `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` WHERE id > 27;";
        majesticsupport::$_db->query($query);

        $query = "UPDATE `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` SET `visible_field`='' ";
        majesticsupport::$_db->query($query);

        $query = "DELETE FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets`;";
        majesticsupport::$_db->query($query);

        $query = "DELETE FROM `" . majesticsupport::$_db->prefix . "mjtc_support_staff_time`;";
        majesticsupport::$_db->query($query);

        $query = "DELETE FROM `" . majesticsupport::$_db->prefix . "mjtc_support_replies`;";
        majesticsupport::$_db->query($query);
        
        $query = "DELETE FROM `" . majesticsupport::$_db->prefix . "mjtc_support_attachments`;";
        majesticsupport::$_db->query($query);
        
        $query = "DELETE FROM `" . majesticsupport::$_db->prefix . "mjtc_support_priorities`;";
        majesticsupport::$_db->query($query);
        
        if (in_array('agent', majesticsupport::$_active_addons)) {
            $query = "DELETE FROM `" . majesticsupport::$_db->prefix . "mjtc_support_staff`;";
            majesticsupport::$_db->query($query);
            $query = "DELETE FROM `" . majesticsupport::$_db->prefix . "mjtc_support_acl_roles` WHERE id > 1;";
            majesticsupport::$_db->query($query);
        }
        
        $query = "DELETE FROM `" . majesticsupport::$_db->prefix . "mjtc_support_users`;";
        majesticsupport::$_db->query($query);
        
        $query = "DELETE FROM `" . majesticsupport::$_db->prefix . "mjtc_support_department_message_premade`;";
        majesticsupport::$_db->query($query);

    }

    private function importAwesomeSupportAgents() {
        // check if user already processed for import
        $imported_agents = array();
        $imported_agent_json = get_option('mjtc_support_ticket_awesome_support_data_agents');
        if(!empty($imported_agents_json)){
            $imported_agents = json_decode($imported_agents_json,true);
        }
        $agents = get_users([
            'role' => 'wpas_agent',
        ]);
        $total_agents = count($agents);

        if($agents){
            if (in_array('agent', majesticsupport::$_active_addons) ) {
                $roleid = $this->getAgentRoleIdByAwesomeSupport();
            }
            foreach($agents AS $agent){
                // Failed if addon not installed
                if (!in_array('agent', majesticsupport::$_active_addons) ) {
                    $this->awesome_support_import_count['agent']['failed']++;
                    continue;
                }
                $wpuid = (int) $agent->data->ID;
                // Skip if already imported
                if (in_array($wpuid, $imported_agents, true)) {
                    $this->awesome_support_import_count['agent']['skipped']++;
                    continue;
                }
                $name = $agent->data->display_name;

                $query = "
                    SELECT user.*
                        FROM `" . majesticsupport::$_db->prefix . "users` AS user
                        WHERE user.id = " . $wpuid;
                $wpuser = majesticsupport::$_db->get_row($query);

                if(!$wpuser){
                    $this->awesome_support_import_count['agent']['failed'] += 1;
                    continue;
                }
                $mjtc_user = MJTC_includer::MJTC_getObjectClass('user')->MJTC_getmajesticsupportuidbyuserid($wpuid);
                if (!empty($mjtc_user) && isset($mjtc_user[0]->id)) {
                    $mjtc_uid = (int)$mjtc_user[0]->id;
                } else {
                    $this->awesome_support_import_count['agent']['failed']++;
                    continue;
                }

                $query = "
                    SELECT staff.*
                        FROM `" . majesticsupport::$_db->prefix . "mjtc_support_staff` AS staff
                            WHERE staff.uid = " . $mjtc_uid;
                $staff = majesticsupport::$_db->get_row($query);

                if (!$staff) {
                    
                    $timestamp = date_i18n('Y-m-d H:i:s');

                    $MJTC_data = [
                        'id'           => '',
                        'uid'          => $mjtc_uid,
                        'groupid'      => '',
                        'roleid'       =>  $roleid,
                        'departmentid' => '',
                        'firstname'    => $name,
                        'lastname'     => '',
                        'username'     => $wpuser->user_login,
                        'email'        => $wpuser->user_email,
                        'signature'    => '',
                        'isadmin'      => '',
                        'status'       => 1,
                        'updated'      => $timestamp,
                        'created'      => $timestamp
                    ];

                    $saved = MJTC_includer::MJTC_getModel('agent')->storeStaff($MJTC_data);

                    $this->awesome_support_import_count['agent']['imported'] += 1;
                    $this->awesome_support_agent_ids[] = $wpuid;
                } else {
                    $this->awesome_support_import_count['agent']['skipped'] += 1;
                }
            }
            // Save list of imported agent IDs
            if (!empty($this->awesome_support_agent_ids)) {
                update_option('mjtc_support_ticket_awesome_support_data_agents', wp_json_encode(array_unique(array_merge($imported_agents, $this->awesome_support_agent_ids))));
            }
        }
    }

    private function getAgentRoleIdByAwesomeSupport() {

        $MJTC_data['id'] = '';
        $MJTC_data['name'] = 'AS Support Agent';
        $MJTC_data['status'] = 1;
        $MJTC_data['created'] = date_i18n('Y-m-d H:i:s');

        $row = MJTC_includer::MJTC_getTable('acl_roles');
        if (!$row->bind($MJTC_data)) {
            $error = 1;
        }
        if (!$row->store()) {
            $error = 1;
        }
        if (empty($error)) {
            return $row->id;
        }

        return null;
    }

    private function importAwesomeSupportUsers() {
        // check if user already processed for import
        $imported_users = array();
        $imported_users_json = get_option('mjtc_support_ticket_awesome_support_data_users');
        if(!empty($imported_users_json)){
            $imported_users = json_decode($imported_users_json,true);
        }

        // Fetch all customers
        $query = "SELECT id FROM `" . majesticsupport::$_db->prefix . "users`";
        $users = majesticsupport::$_db->get_results($query);
        $wpUsers = array();
        $mjtcUsers = array();
        foreach ($users as $MJTC_key => $user) {
            $wpUsers[] = $user->id;
        }
        $query = " SELECT wpuid FROM `" . majesticsupport::$_db->prefix . "mjtc_support_users`";
        $users = majesticsupport::$_db->get_results($query);
        foreach ($users as $MJTC_key => $user) {
            $mjtcUsers[] = $user->wpuid;
        }

        $missingUsers = array_diff($wpUsers,$mjtcUsers);

        if (empty($missingUsers)) return;

        foreach ($missingUsers as $missingUser) {
            $query = "SELECT * FROM `" . majesticsupport::$_db->prefix . "users` WHERE id = " . esc_sql($missingUser);
            $customer = majesticsupport::$_db->get_row($query);

            $customer_id = intval($customer->ID);
            $wpuid       = intval($customer->ID);
            $name        = sanitize_text_field($customer->display_name ?? '');
            $email       = sanitize_email($customer->user_email ?? '');

            // Skip if already imported
            if (in_array($customer_id, $imported_users, true)) {
                $this->awesome_support_import_count['user']['skipped']++;
                continue;
            }   

            // Prepare data for new user
            $row = MJTC_includer::MJTC_getTable('users');
            $MJTC_data = [
                'id'            => '',
                'wpuid'         => $wpuid,
                'name'          => $name,
                'display_name'  => '',
                'user_email'    => $email,
                'status'        => 1,
                'issocial'      => 0,
                'socialid'      => null,
                'autogenerated' => 0,
            ];

            // Attempt to save the new user
            $row->bind($MJTC_data);
            if (!$row->store()) {
                $this->awesome_support_import_count['user']['failed']++;
                continue;
            }

            // Store successful import info
            $this->awesome_support_users_array[$customer_id] = $row->wpuid;
            $this->awesome_support_user_ids[] = $customer_id;
            $this->awesome_support_import_count['user']['imported']++;
        }

        // Save list of imported user IDs
        if (!empty($this->awesome_support_user_ids)) {
            update_option('mjtc_support_ticket_awesome_support_data_users', wp_json_encode(array_unique(array_merge($imported_users, $this->awesome_support_user_ids))));
        }
    }

    private function importAwesomeSupportDepartments() {
        // check if department already processed for import
        $imported_departments = array();
        $imported_departments_json = get_option('mjtc_support_ticket_awesome_support_data_departments');
        if(!empty($imported_departments_json)){
            $imported_departments = json_decode($imported_departments_json,true);
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
        $query = "
            SELECT MAX(dept.ordering)
            FROM `" . majesticsupport::$_db->prefix . "mjtc_support_departments` AS dept
        ";
        $ordering = (int) majesticsupport::$_db->get_var($query);
        $now = date_i18n('Y-m-d H:i:s');

        foreach($MJTC_departments AS $MJTC_department){
            // Skip if already imported
            if (in_array($MJTC_department->id, $imported_departments, true)) {
                $this->awesome_support_import_count['department']['skipped']++;
                continue;
            }

            $name = MJTC_majesticsupportphplib::MJTC_trim($MJTC_department->name);
            $lower_name = MJTC_majesticsupportphplib::MJTC_strtolower($name);

            // Check if department already exists
            $check_query = "
                SELECT department.*
                FROM `" . majesticsupport::$_db->prefix . "mjtc_support_departments` AS department
                WHERE LOWER(department.departmentname) = '". esc_sql($name) ."'";
            $existing = majesticsupport::$_db->get_row($check_query);

            if (!$existing) { // not exists
                $row = MJTC_includer::MJTC_getTable('departments');

                $updated = date_i18n('Y-m-d H:i:s');
                $created = date_i18n('Y-m-d H:i:s');

                $MJTC_data = [
                    'id'              => '',
                    'emailid'         => '1',
                    'departmentname'  => $name,
                    'ordering'        => $ordering,
                    'status'          => '1',
                    'isdefault'       => '0',
                    'ispublic'        => '1',
                    'updated'         => $now,
                    'created'         => $now
                ];

                $MJTC_data = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($MJTC_data);
                $row->bind($MJTC_data);

                if (!$row->store()) {
                    $this->awesome_support_import_count['department']['failed'] += 1;
                } else {
                    $this->awesome_support_department_ids[] = $MJTC_department->id;
                    $this->awesome_support_import_count['department']['imported'] += 1;
                }

                $ordering++;
            } else {
                $this->awesome_support_import_count['department']['skipped'] += 1;
            }
        }
        // Save list of imported department IDs
        if (!empty($this->awesome_support_department_ids)) {
            update_option('mjtc_support_ticket_awesome_support_data_departments', wp_json_encode(array_unique(array_merge($imported_departments, $this->awesome_support_department_ids))));
        }
    }
    
    private function importAwesomeSupportPriorities() {
        // check if priority already processed for import
        $imported_priorities = array();
        $imported_priorities_json = get_option('mjtc_support_ticket_awesome_support_data_priorities');
        if(!empty($imported_priorities_json)){
            $imported_priorities = json_decode($imported_priorities_json,true);
        }

        $priorities = get_terms([
            'taxonomy'   => 'ticket_priority',
            'hide_empty' => false,
        ]);

        if (is_wp_error($priorities) || empty($priorities)) return;

        foreach ($priorities as $MJTC_key => $priority) {
            $meta = get_term_meta($priority->term_id);
            $priorities[$MJTC_key]->meta = $meta;
        }
        
        // Get highest current ordering value
        $query = "
            SELECT MAX(priority.ordering)
            FROM `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` AS priority
        ";
        $ordering = (int) majesticsupport::$_db->get_var($query);

        foreach ($priorities AS $priority) {
            // Skip if already imported
            if (in_array($priority->id, $imported_priorities, true)) {
                $this->awesome_support_import_count['priority']['skipped']++;
                continue;
            }

            $name = MJTC_majesticsupportphplib::MJTC_trim(MJTC_majesticsupportphplib::MJTC_strtolower($priority->name));

            // Check if this priority already exists in Majestic Support
            $check_query = "
                SELECT priority.*
                FROM `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` AS priority
                WHERE LOWER(priority.priority) = '" . esc_sql($name) . "'
                LIMIT 1
            ";
            $ms_priority = majesticsupport::$_db->get_row($check_query);

            if (!$ms_priority) {
                $row = MJTC_includer::MJTC_getTable('priorities');

                $MJTC_color = "#5e8f5b"; // default color
                if (!empty($priority->meta['color'][0])) {
                    $MJTC_color = $priority->meta['color'][0];
                }
                
                $MJTC_data = [
                    'id'               => '',
                    'priority'         => $priority->name,
                    'prioritycolour'   => $MJTC_color,
                    'priorityurgency'  => '',
                    'overduetypeid'    => 1,
                    'overdueinterval'  => 7,
                    'ordering'         => $ordering,
                    'status'           => '1',
                    'isdefault'        => '0',
                    'ispublic'         => '1'
                ];

                $MJTC_data = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($MJTC_data);
                $row->bind($MJTC_data);

                if (!$row->store()) {
                    $this->awesome_support_import_count['priority']['failed'] += 1;
                } else {
                    $this->awesome_support_priority_ids[] = $priority->id;
                    $this->awesome_support_import_count['priority']['imported'] += 1;
                }

                $ordering++;
            } else {
                $this->awesome_support_import_count['priority']['skipped'] += 1;
            }
        }
        // Save list of imported priority IDs
        if (!empty($this->awesome_support_priority_ids)) {
            update_option('mjtc_support_ticket_awesome_support_data_priorities', wp_json_encode(array_unique(array_merge($imported_priorities, $this->awesome_support_priority_ids))));
        }
    }

    private function importAwesomeSupportStatus() {
        // Load previously imported statuses
        $imported_statuses = [];
        $imported_statuses_json = get_option('mjtc_support_ticket_awesome_support_data_statuses');
        if (!empty($imported_statuses_json)) {
            $imported_statuses = json_decode($imported_statuses_json, true);
        }

        // Get SupportCandy statuses (excluding system/default ones)
        $statuses = get_posts( [
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

        if (empty($statuses)) return;

        // Get highest current ordering value
        $query = "
            SELECT MAX(status.ordering)
            FROM `" . majesticsupport::$_db->prefix . "mjtc_support_statuses` AS status
        ";
        $ordering = (int) majesticsupport::$_db->get_var($query);

        // Build array of existing JS statuses (cleaned)
        $query = "
            SELECT status.status
            FROM `" . majesticsupport::$_db->prefix . "mjtc_support_statuses` AS status
        ";
        $msstatuses = majesticsupport::$_db->get_results($query);
        $existing_status_names = array_map(function($status) {
            return $this->cleanStringForCompare($status->status);
        }, $msstatuses);

        foreach ($statuses as $status) {
            $name = $status->post_title;
            $compare_name = $this->cleanStringForCompare($name);

            // Skip if name already exists
            if (in_array($compare_name, $existing_status_names)) {
                $this->awesome_support_import_count['status']['skipped'] += 1;
                continue;
            }

            // Skip if already imported
            if (in_array($status->id, $imported_statuses)) {
                $this->awesome_support_import_count['status']['skipped'] += 1;
                continue;
            }

            $post_meta = get_post_meta($status->ID);
            $MJTC_bgcolor = "#5e8f5b"; // default color
            if (!empty($post_meta['status_color'][0])) {
                $MJTC_bgcolor = $post_meta['status_color'][0];
            }

            // Prepare new status data
            $row = MJTC_includer::MJTC_getTable('statuses');
            $MJTC_data = [
                'id'             => '',
                'status'         => $name,
                'statuscolour'   => '#FFF',
                'statusbgcolour' => $MJTC_bgcolor,
                'sys'            => '0',
                'ordering'       => $ordering
            ];

            $MJTC_data = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($MJTC_data);
            $row->bind($MJTC_data);

            if (!$row->store()) {
                $this->awesome_support_import_count['status']['failed'] += 1;
            } else {
                $this->awesome_support_status_ids[] = $status->id;
                $this->awesome_support_import_count['status']['imported'] += 1;
                $ordering++;
            }
        }

        // Save updated list of imported statuses
        if (!empty($this->awesome_support_status_ids)) {
            update_option('mjtc_support_ticket_awesome_support_data_statuses', wp_json_encode($this->awesome_support_status_ids));
        }
    }

    private function importAwesomeSupportPremades() {
        // check if premade already processed for import
        $imported_premades = array();
        $imported_premades_json = get_option('mjtc_support_ticket_awesome_support_data_premades');
        if(!empty($imported_premades_json)){
            $imported_premades = json_decode($imported_premades_json,true);
        }

        // Get SupportCandy statuses (excluding system/default ones)
        $canned_replies = get_posts( [
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

        if (empty($canned_replies)) return;

        foreach ($canned_replies as $canned_reply) {
            $title = MJTC_majesticsupportphplib::MJTC_trim(MJTC_majesticsupportphplib::MJTC_strtolower($canned_reply->post_title));
            // Failed if addon not installed
            if (!in_array('cannedresponses', majesticsupport::$_active_addons) ) {
                $this->awesome_support_import_count['canned response']['failed']++;
                continue;
            }
            // Skip if already imported
            if (in_array($canned_reply->id, $imported_premades, true)) {
                $this->awesome_support_import_count['canned response']['skipped']++;
                continue;
            }
            // Check if this premade already exists in Majestic Support
            $check_query = "
                SELECT premade.*
                FROM `" . majesticsupport::$_db->prefix . "mjtc_support_department_message_premade` AS premade
                WHERE LOWER(premade.title) = '" . esc_sql($title) . "'
                LIMIT 1
            ";
            $ms_canned_reply = majesticsupport::$_db->get_row($check_query);

            if (!$ms_canned_reply) {
            
                $MJTC_departmentid = MJTC_includer::MJTC_getModel('department')->getDefaultDepartmentID();
                // Prepare canned response data
                $row = MJTC_includer::MJTC_getTable('cannedresponses');
                $updated = date_i18n('Y-m-d H:i:s');

                $MJTC_data = [
                    'id'          => '',
                    'departmentid'=> $MJTC_departmentid,
                    'title'       => $canned_reply->post_title,
                    'answer'      => $canned_reply->post_content,
                    'status'      => '1',
                    'updated'     => $updated,
                    'created'     => $canned_reply->post_date
                ];

                $MJTC_data = majesticsupport::MJTC_sanitizeData($MJTC_data);
                $MJTC_data = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($MJTC_data);

                $row->bind($MJTC_data);
                if (!$row->store()) {
                    $this->awesome_support_import_count['canned response']['failed'] += 1;
                } else {
                    $this->awesome_support_premade_ids[] = $canned_reply->id;
                    $this->awesome_support_import_count['canned response']['imported'] += 1;
                }
            } else {
                $this->awesome_support_import_count['canned response']['skipped'] += 1;
            }
        }

        // Save list of imported premade IDs
        if (!empty($this->awesome_support_premade_ids)) {
            update_option('mjtc_support_ticket_awesome_support_data_premades', wp_json_encode(array_unique(array_merge($imported_premades, $this->awesome_support_premade_ids))));
        }
    }

    private function importAwesomeSupportProducts(){
        // check if product already processed for import
        $imported_products = array();
        $imported_products_json = get_option('mjtc_support_ticket_awesome_support_data_products');
        if(!empty($imported_products_json)){
            $imported_products = json_decode($imported_products_json,true);
        }

        $products = get_terms([
            'taxonomy'   => 'product',
            'hide_empty' => false,
        ]);

        if (is_wp_error($products) || empty($products)) return;
        
        // Get highest current ordering value
        $query = "
            SELECT MAX(product.ordering)
            FROM `" . majesticsupport::$_db->prefix . "mjtc_support_products` AS product
        ";
        $ordering = (int) majesticsupport::$_db->get_var($query);

        foreach($products AS $product){
            // Skip if already imported
            if (in_array($product->term_id, $imported_products, true)) {
                $this->awesome_support_import_count['product']['skipped']++;
                continue;
            }

            $name = MJTC_majesticsupportphplib::MJTC_trim(MJTC_majesticsupportphplib::MJTC_strtolower($product->name));

            // Check if this product already exists in Majestic Support
            $check_query = "
                SELECT product.*
                FROM `" . majesticsupport::$_db->prefix . "mjtc_support_products` AS product
                WHERE LOWER(product.product) = '".esc_sql($name) ."'
                LIMIT 1
            ";
            $ms_product = majesticsupport::$_db->get_row($check_query);

            if(!$ms_product){
                $row = MJTC_includer::MJTC_getTable('products');

                $MJTC_color = "#5e8f5b"; // default color
                if (!empty($product->meta['color'][0])) {
                    $MJTC_color = $product->meta['color'][0];
                }
                
                $MJTC_data = [
                    'id'               => '',
                    'product'         => $product->name,
                    'status'           => '1',
                    'ordering'         => $ordering
                ];

                $MJTC_data = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($MJTC_data);
                $row->bind($MJTC_data);

                if (!$row->store()) {
                    $this->awesome_support_import_count['product']['failed'] += 1;
                } else {
                    $this->awesome_support_product_ids[] = $product->term_id;
                    $this->awesome_support_import_count['product']['imported'] += 1;
                }

                $ordering++;
            } else {
                $this->awesome_support_import_count['product']['skipped'] += 1;
            }
        }
        // Save list of imported product IDs
        if (!empty($this->awesome_support_product_ids)) {
            update_option('mjtc_support_ticket_awesome_support_data_products', wp_json_encode(array_unique(array_merge($imported_products, $this->awesome_support_product_ids))));
        }
    }


    // 
    // ticket
    // 
    function getAwesomeSupportTickets($as_ticket_custom_fields) {
        // Check if tickets already processed for import
        $imported_tickets = array();
        $imported_tickets_json = get_option('mjtc_support_ticket_awesome_support_data_tickets');
        if (!empty($imported_tickets_json)) {
            $imported_tickets = json_decode($imported_tickets_json, true);
        }

        $MJTC_tickets = wpas_get_tickets('any');

        $new_tickets = array();
        foreach($MJTC_tickets AS $MJTC_ticket){
            // Skip if ticket already imported
            if (!empty($imported_tickets) && in_array($MJTC_ticket->id, $imported_tickets)) {
                $this->awesome_support_import_count['ticket']['skipped'] += 1;
                continue;
            }

            // Map custom fields
            $params = array();
            foreach ($as_ticket_custom_fields as $as_ticket_custom_field) {
                $field_name = $as_ticket_custom_field["name"];
                $custom_text = get_post_meta($MJTC_ticket->ID, '_wpas_'.$field_name, true);
                $vardata = "";
                
                if ($custom_text) {
                    if ($as_ticket_custom_field["type"] == "date") {
                        $vardata = gmdate("Y-m-d", MJTC_majesticsupportphplib::MJTC_strtotime($custom_text));
                    } else {
                        $vardata = $custom_text;
                    }

                    if ($vardata != '') {
                        if (is_array($vardata)) {
                            $vardata = implode(', ', array_filter($vardata));
                        }
                        $params[$as_ticket_custom_field["ms_filedorderingfield"]] = MJTC_majesticsupportphplib::MJTC_htmlentities($vardata);
                    }
                }
            }
            $MJTC_ticketparams = html_entity_decode(wp_json_encode($params, JSON_UNESCAPED_UNICODE));
            $post_meta = get_post_meta($MJTC_ticket->ID);
            
            
            $assign_to = "";
            if(isset($post_meta["_wpas_assignee"][0])) $assign_to = $post_meta["_wpas_assignee"][0];
            
            $userinfo = $this->getAwesomeSupportTicketCustomerInfo($MJTC_ticket->post_author);
            
            $MJTC_departmentid = $this->getTicketDepartmentIdByAwesomeSupport($MJTC_ticket->ID);
            $priorityid = $this->getTicketPriorityIdByAwesomeSupport($MJTC_ticket->ID);
            $productid = $this->getTicketProductIdByAwesomeSupport($MJTC_ticket->ID);
            $agentid = $this->getTicketAgentIdByAwesomeSupport($MJTC_ticket->ID);

            //get user fields
            $idresult = MJTC_includer::MJTC_getModel('ticket')->getRandomTicketId();
            $MJTC_ticketid = $idresult['ticketid'];
            $customticketno = $idresult['customticketno'];

            $attachmentdir = MJTC_includer::MJTC_getModel('ticket')->getRandomFolderName();
            $MJTC_ticket_status = 1;

            $custom_statuses = get_posts( [
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
            $is_custom_status = 0;
            foreach ($custom_statuses as $custom_statuse) {
                if ($MJTC_ticket->post_status == $custom_statuse->post_name) {
                    $is_custom_status = 1;
                    continue; // stop cheacking further
                }
            }
            
            if(!empty($is_custom_status)) {
                $MJTC_ticket_status = $this->getTicketStatusIdByAwesomeSupport($MJTC_ticket->post_status);
            } else {
                if($post_meta["_wpas_status"][0] == "open"){
                    if(isset($post_meta["_wpas_last_reply_date"][0])){
                        $MJTC_ticket_status = 1;
                    
                        if($post_meta["_wpas_is_waiting_client_reply"][0] == "1"){ // 1 means waiting agent reply
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

            if(isset($post_meta["_ticket_closed_on"][0])){
                if($post_meta["_ticket_closed_on"][0] == "closed"){
                    $MJTC_ticket_status = 5;
                    if(isset($post_meta["_ticket_closed_on"][0])) {
                        $MJTC_ticket_closed = $post_meta["_ticket_closed_on"][0];
                    }
                }
            }
            $lastreply = "0000-00-00 00:00:00";
            if(isset($post_meta["_wpas_last_reply_date"][0])){
                $lastreply = $post_meta["_wpas_last_reply_date"][0];
            }
            
            $isanswered = 0;
            if($MJTC_ticket_status == 4) $isanswered = 1;
            
            $MJTC_ticket_closedby = "";
            // Ticket Default Status
            // 1 -> New Ticket
            // 2 -> Waiting admin/staff reply
            // 3 -> in progress
            // 4 -> waiting for customer reply
            // 5 -> close ticket
    
            $newTicketData = [
                'id' => "",
                'uid' => $userinfo["ms_uid"],
                'ticketid' => $MJTC_ticketid,
                'departmentid' => $MJTC_departmentid,
                'priorityid' => $priorityid,
                'productid' => $productid,
                'staffid' => $agentid,
                'email' => $userinfo["customer_email"],
                'name' => $userinfo["customer_name"],
                'subject' => $MJTC_ticket->post_title,
                'message' => $MJTC_ticket->post_content,
                'helptopicid' => 0,
                'multiformid' => 1,
                'phone' => "",
                'phoneext' => "",
                'status' => $MJTC_ticket_status,
                'isoverdue' => "0",
                'isanswered' => $isanswered,
                'duedate' => "0000-00-00 00:00:00",
                'reopened' => "0000-00-00 00:00:00",
                'closed' => $MJTC_ticket_closed,
                'closedby' => $MJTC_ticket_closedby,
                'lastreply' => $lastreply,
                'created' => $MJTC_ticket->post_date,
                'updated' => $MJTC_ticket->post_modified,
                'lock' => "0",
                'ticketviaemail' => "0",
                'ticketviaemail_id' => "0",
                'attachmentdir' => $attachmentdir,
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
                'customticketno' => $customticketno
            ];

            $row = MJTC_includer::MJTC_getTable('tickets');
            $error = 0;
            if (!$row->bind($newTicketData)) $error = 1;
            if (!$row->store()) $error = 1;

            if ($error == 1) {
                $this->awesome_support_import_count['ticket']['failed'] += 1;
            } else {
                $this->awesome_support_ticket_ids[] = $MJTC_ticket->id;
                $this->awesome_support_import_count['ticket']['imported'] += 1;

                $ms_ticketid = $row->id;

                //update hash value against ticket
                $hash = MJTC_includer::MJTC_getModel('ticket')->generateHash($ms_ticketid);
                $query = "UPDATE `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` SET `hash`='" . esc_sql($hash) . "' WHERE id=" . esc_sql($ms_ticketid);
                majesticsupport::$_db->query($query);
                
                $this->getAwesomeSupportTicketReplies($ms_ticketid, $MJTC_ticket->ID, $attachmentdir);
                $this->getAwesomeSupportTicketAttachments($ms_ticketid, "", $MJTC_ticket->ID, "", $attachmentdir);


                if (in_array('privatecredentials', majesticsupport::$_active_addons)) {
                    $this->getAwesomeSupportTicketPrivateCredentials($ms_ticketid, $userinfo["ms_uid"], $MJTC_ticket->ID);
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
        $custom_fields = get_option("wpas_custom_fields");

        if (!$custom_fields) return;

        $this->as_ticket_custom_fields = [];

        foreach ($custom_fields as $custom_field) {
            // Map field types
            switch ($custom_field["field_type"]){
                case "text":
                    $fieldtype = "text"; break;
                case "url":
                    $fieldtype = "text"; break;
                case "email":
                    $fieldtype = "email"; break;
                case "number":
                    $fieldtype = "text"; break;
                case "date-field":
                    $fieldtype = "date"; break;
                case "password":
                    $fieldtype = "text"; break;
                case "upload":
                    $fieldtype = "file"; break;
                case "select":
                    $fieldtype = "combo"; break;
                case "radio":
                    $fieldtype = "radio"; break;
                case "checkbox":
                    $fieldtype = "checkbox"; break;
                case "textarea":
                    $fieldtype = "textarea"; break;
                case "wysiwyg":
                    $fieldtype = "wysiwyg"; break;
                default:
                    $fieldtype = "text"; break;
            }

            // Load options for select-type fields
            $option_values = [];
            if(!empty($custom_field['options'])){ // field in the ticket table
                $field_options = $custom_field['options'];
                if ($field_options) {
                    foreach ($field_options as $MJTC_key => $field_option) {
                        $option_values[] = $MJTC_key;
                    }
                }
            }

            // Build visibility data
            $visibledata = [
                "visibleLogic" => [],
                "visibleParent" => [],
                "visibleValue" => [],
                "visibleCondition" => [],
            ];

            // Prepare field data for import
            $fieldOrderingData = [
                "id" => "",
                // "field" => $slug,
                "field" => $custom_field['name'],
                "fieldtitle" => $custom_field['title'],
                "ordering" => "",
                "section" => "10",
                "fieldfor" => "1",
                "published" => "1",
                "sys" => "0",
                "cannotunpublish" => "0",
                "required" => $custom_field['required'],
                "size" => "100",
                "cols" => "",
                "rows" => "",
                "isuserfield" => "1",
                "userfieldtype" => $fieldtype,
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
                "values" => $option_values,
                "visibleParent" => $visibledata["visibleParent"],
                "visibleValue" => $visibledata["visibleValue"],
                "visibleCondition" => $visibledata["visibleCondition"],
                "visibleLogic" => $visibledata["visibleLogic"],
                "placeholder" => $custom_field['placeholder'],
                "description" => $custom_field['desc'],
                "defaultvalue" => $custom_field['default'],
                "readonly" => $custom_field['readonly'],
            ];

            // Store field in SupportCandy
            $record_saved = MJTC_includer::MJTC_getModel('fieldordering')->storeUserField($fieldOrderingData);

            if ($record_saved == 1) {
                $query = "SELECT * FROM `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` ORDER BY id DESC LIMIT 1";
                $latest_record = majesticsupport::$_db->get_row($query);

                $this->as_ticket_custom_fields[] = [
                    "name" => $custom_field['name'],
                    "type" => $fieldtype,
                    "ms_filedorderingid" => $latest_record->id,
                    "ms_filedorderingfield" => $latest_record->field,
                ];
                $this->awesome_support_import_count['field']['imported'] += 1;
            } else {
                $this->awesome_support_import_count['field']['failed'] += 1;
                // Optionally log: error_log("Failed to import field: $slug");
            }
        }
    }

    private function getAwesomeSupportTicketReplies($ms_ticket_id, $ast_ticket_id, $attachmentdir){
        $query = "SELECT post.*
                    FROM `" . majesticsupport::$_db->prefix . "posts` AS post
                    WHERE post.post_parent = ".$ast_ticket_id."
                    AND post.post_type = 'ticket_reply'
                    ORDER BY post.id ASC";
                    
        $posts = majesticsupport::$_db->get_results($query);

        if (empty($posts)) return;


        foreach($posts AS $post){
            $userinfo = $this->getAwesomeSupportTicketCustomerInfo($post->post_author);
            $uid = $userinfo["ms_uid"];
            $name = $userinfo["customer_name"];
            if(empty($userinfo["ms_uid"])){

                /*$agentid = $this->getTicketAgentIdByAwesomeSupport($conversation->person_id);
                if($agentid){
                    $query = "SELECT agent.*
                                FROM `" . majesticsupport::$_db->prefix . "mjtc_support_staff` AS agent
                                WHERE agent.id = ".$agentid.";";
                                
                    $agent = majesticsupport::$_db->get_row($query);
                    $uid = $agent->uid;
                    $name = $agent->firstname;
                    if($agent->lastname) $name = $name. " ". $agent->lastname;
                    
                } */
            }

            $replyData = [
                "id" => "",
                "uid" => $uid,
                "ticketid" => $ms_ticket_id,
                "name" => $name,
                "message" => $post->post_content,
                "staffid" => "",
                "rating" => "",
                "status" => "1",
                "created" => $post->post_date,
                "ticketviaemail" => "",
                "viewed_by" => "",
                "viewed_on" => ""
            ];
            $row = MJTC_includer::MJTC_getTable('replies');
            $MJTC_data = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($replyData);// remove slashes with quotes.
            $error = 0;
            if (!$row->bind($MJTC_data)) {
                $error = 1;
            }
            if (!$row->store()) {
                $error = 1;
            }

            $ms_ticket_reply_id = $row->id;

            if (!empty($ms_ticket_reply_id)) {
                $this->getAwesomeSupportTicketAttachments($ms_ticket_id, $ms_ticket_reply_id, $ast_ticket_id, $post->ID, $attachmentdir);
            }

            if (in_array('timetracking', majesticsupport::$_active_addons)) {
                $this->getAwesomeSupportTicketStaffTime($ms_ticket_id, $ast_ticket_id, $ms_ticket_reply_id, $post->ID);
            }
            
        }
    }

    private function getAwesomeSupportTicketAttachments($ms_ticket_id, $ms_ticket_reply_id, $ast_ticket_id, $as_ticket_reply_id, $attachmentdir){
        $as_ticket_reply_id = intval($as_ticket_reply_id);

        if ($as_ticket_reply_id <= 0) return;


        $query = "SELECT post.*
                    FROM `" . majesticsupport::$_db->prefix . "posts` AS post
                    WHERE post.post_parent = ".$as_ticket_reply_id."
                    AND post.post_type = 'attachment'
                    ORDER BY post.id ASC";
                    
        $posts = majesticsupport::$_db->get_results($query);


        foreach($posts AS $post){
            $post_meta = get_post_meta($post->ID);
            if(isset($post_meta["_wp_attachment_metadata"][0])){
                $attachment = unserialize($post_meta["_wp_attachment_metadata"][0]);
                $file_name = basename($attachment["file"]);         
                
            
                $attachmentData = [
                    "id" => "",
                    "ticketid" => $ms_ticket_id,
                    "replyattachmentid" => $ms_ticket_reply_id,
                    "filesize" => "",
                    "filename" => $file_name,
                    "filekey" => "",
                    "deleted" => "",
                    "status" => "1",
                    "created" => $post->post_date
                ];
                $row = MJTC_includer::MJTC_getTable('attachments');
                $MJTC_data = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($attachmentData);// remove slashes with quotes.
                $error = 0;
                if (!$row->bind($MJTC_data)) {
                    $error = 1;
                }
                if (!$row->store()) {
                    $error = 1;
                }
                require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
                require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';
                $filesystem = new WP_Filesystem_Direct( true );
                $upload_dir = wp_upload_dir();
                $upload_path = $upload_dir['basedir'];         // Server path to the uploads directory
                $MJTC_datadirectory = majesticsupport::$_config['data_directory'];
                $path = $upload_path."/".$MJTC_datadirectory."/attachmentdata/ticket/".$attachmentdir;
                if(!$filesystem->exists($path)){
                    wp_mkdir_p($path);
                }
                $source = $upload_path . "/" . $attachment["file"]; // full path to original
                if (!file_exists($source)) {
                    $path_info = pathinfo($source);

                    // Get directory and base filename (without extension)
                    $directory = $path_info['dirname'];
                    $filename = $path_info['filename']; // e.g., 01_5
                    $extension = $path_info['extension']; // e.g., jpg

                    // Desired sizes to check
                    $sizes = ['100x100', '150x150', '300x300', '600x337', '768x431', '300x168'];
                    $resized_file = '';

                    foreach ($sizes as $size) {
                        $resized_path = $directory . '/' . $filename . '-' . $size . '.' . $extension;
                        if (file_exists($resized_path)) {
                            $resized_file = $resized_path;
                            break;
                        }
                    }

                    // Fallback to original if no resized version found
                    if (!$resized_file && file_exists($source)) {
                        $resized_file = $source;
                    }
                } else {
                    $resized_file = $source;
                }
                $destination = $path."/".$file_name;
                
                $result = $filesystem->move($resized_file, $destination, true);
            }
            
        }
    }

    private function getAwesomeSupportTicketCustomerInfo($customerId){
        // Sanitize and validate customer ID
        $customerId = intval($customerId);
        if ($customerId <= 0) {
            return [
                "ms_uid" => "",
                "customer_name" => "",
                "customer_email" => ""
            ];
        }

        // Prepare secure query
        $query = "
            SELECT customer.name, customer.user_email, customer.id AS ms_uid
            FROM `" . majesticsupport::$_db->prefix . "mjtc_support_users` AS customer
            WHERE customer.wpuid = ". esc_sql($customerId) ."
            LIMIT 1
        ";
        $MJTC_data = majesticsupport::$_db->get_row($query);

        return [
            "ms_uid"       => $MJTC_data->ms_uid ?? "",
            "customer_name"  => $MJTC_data->name ?? "",
            "customer_email" => $MJTC_data->email ?? ""
        ];
    }

    private function getAwesomeSupportTicketPrivateCredentials($ms_ticket_id, $ms_ticket_uid, $post_id) {
        $ms_ticket_uid = 1;
        // Get private credentials if they exist.
        if( get_post_meta( $post_id, '_wpas_pc_credentials', true ) ) {
            $credentials = get_post_meta( $post_id, '_wpas_pc_credentials', true );
            
            $encryption_key = get_post_meta( $post_id, '_wpas_pc_encryption_key', true );

            foreach( $credentials as $MJTC_key => $MJTC_value ) {
                $system   = $this->MJTC_decrypt( $MJTC_value[ "system" ], $encryption_key );
                $username = $this->MJTC_decrypt( $MJTC_value[ "username" ], $encryption_key );
                $password = $this->MJTC_decrypt( $MJTC_value[ "password" ], $encryption_key );
                $MJTC_url      = $this->MJTC_decrypt( $MJTC_value[ "url" ], $encryption_key );
                $note     = $this->MJTC_decrypt( $MJTC_value[ "note" ], $encryption_key );

                $pc_array = [
                    'credentialtype' => sanitize_text_field($system),
                    'username'       => $username,
                    'password'       => $password,
                    'info'           => $note
                ];

                $MJTC_data = [
                    'id'        => '',
                    'uid'       => intval($ms_ticket_uid),
                    'ticketid'  => intval($ms_ticket_id),
                    'status'    => 1,
                    'created'   => current_time('mysql'),
                ];

                // Clean and encode credential info
                $encoded = wp_json_encode(array_filter($pc_array));
                $safe_encoded = MJTC_majesticsupportphplib::MJTC_safe_encoding($encoded);
                $MJTC_data['data'] = MJTC_includer::MJTC_getObjectClass('privatecredentials')->encrypt($safe_encoded);

                // Insert record
                if ($MJTC_data['ticketid'] > 0 && $MJTC_data['uid'] > 0) {
                    $row = MJTC_includer::MJTC_getTable('privatecredentials');
                    if ($row->bind($MJTC_data)) {
                        $row->store(); // Failure silently ignored here; consider logging
                    }
                }
            }
        }
    }

    private function decrypt( $message, $MJTC_key, $encoded = true ) {
        $method = 'aes-256-ctr';

        if ( $message == '' ) {
            return '';
        }

        if ( $encoded ) {
            $message = base64_decode( $message, true );
            if ( $message === false ) {
                return false;
            }
        }

        $nonceSize  = openssl_cipher_iv_length( $method );
        $nonce      = mb_substr( $message, 0, $nonceSize, '8bit' );
        $ciphertext = mb_substr( $message, $nonceSize, null, '8bit' );

        $plaintext = '';

        try {
            $plaintext = openssl_decrypt( $ciphertext, $method, $MJTC_key, OPENSSL_RAW_DATA, $nonce );
        } catch ( Exception $e ) {
            return false;
        }

        return $plaintext;

    }

    private function getAwesomeSupportTicketActivityLog($ms_ticket_id, $sc_ticket_id) {
        $sc_ticket_id = intval($sc_ticket_id);
        $ms_ticket_id = intval($ms_ticket_id);

        if ($sc_ticket_id <= 0 || $ms_ticket_id <= 0) return;

        $threads = get_posts( [
            'post_parent'    => $sc_ticket_id,
            'post_type'      => apply_filters( 'wpas_replies_post_type', array(
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
            $userinfo = $this->getAwesomeSupportTicketCustomerInfo($thread->post_author);
            $currentUserName = !empty($userinfo['customer_name']) 
                ? esc_html($userinfo['customer_name']) 
                : esc_html(__('Guest', 'majestic-support'));

            $messagetype = __('Successfully', 'majestic-support');
            $eventtype = '';
            $message = '';

            if ($thread->post_type == 'ticket_history') {
                $eventtype = MJTC_majesticsupportphplib::MJTC_strip_tags($thread->post_content);
                $messageWithBreaks = MJTC_majesticsupportphplib::MJTC_preg_replace('/<\/[^>]+>/', "$0 ", $thread->post_content);
                $message = MJTC_majesticsupportphplib::MJTC_strip_tags($messageWithBreaks) . " " . __('by', 'majestic-support') . " ( $currentUserName )";
            } elseif ($thread->post_type == 'ticket_reply') {
                $eventtype = __('REPLIED_TICKET', 'majestic-support');
                $message = __('Ticket is replied by', 'majestic-support') . " ( $currentUserName )";
            }

            if (!empty($eventtype) && !empty($message)) {
                MJTC_includer::MJTC_getModel('tickethistory')->addActivityLog(
                    $MJTC_ticketid, 1, esc_html($eventtype), esc_html($message), esc_html($messagetype)
                );
            }
        }
    }

    private function getAwesomeSupportTicketStaffTime($ms_ticket_id, $ast_ticket_id, $ms_reply_id, $as_reply_id) {
        $as_reply_id = intval($as_reply_id);
        $ms_ticket_id = intval($ms_ticket_id);
        if ($as_reply_id <= 0 || $ms_ticket_id <= 0) return;

        // Get all timer logs for the given Awesome Support ticket
        
        $query = new WP_Query( array(
            'post_type' => 'trackedtimes',
            'post_status' => 'publish',
            'posts_per_page' => -1
        ) );

        $time_ids = wp_list_pluck( $query->posts, 'ID' );
        $duplicate_occurs = false;

        foreach( $time_ids as $id ) {
            $tracked_time = get_post_meta( $id, 'as_time_tracking_entry' );

            if( !empty( $tracked_time ) ) {
                if( ( $ast_ticket_id == $tracked_time[0]['ticket_id'] ) && ( $as_reply_id == $tracked_time[0]['ticket_reply'] ) ) {

                    // Get HelpDesk staff ID from SupportCandy agent ID
                    // $staffid = $this->getMSAgentIdByScAgentId($timer->log_by);
                    // if (empty($staffid)) continue;

                    $created = $tracked_time[0]['start_date_time'];

                    // Handle and validate interval string
                    

                    $timer_minutes = $tracked_time[0]['individual_time'];
                    if ($timer_minutes <= 0) continue;

                    $timer_seconds = $timer_minutes * 60;

                    // Conflict detection
                    $created_dt = new DateTime($created);
                    $now = new DateTime();
                    $interval_to_now = $created_dt->diff($now);
                    $systemtime = ($interval_to_now->days * 86400) + ($interval_to_now->h * 3600) + ($interval_to_now->i * 60) + $interval_to_now->s;

                    $conflict = ($timer_seconds > $systemtime) ? 1 : 0;

                    // Prepare data
                    $MJTC_data = [
                        'staffid' => '',
                        'ticketid' => $ms_ticket_id,
                        'referencefor' => 1,
                        'referenceid' => $ms_reply_id,
                        'usertime' => $timer_seconds,
                        'systemtime' => $systemtime,
                        'conflict' => $conflict,
                        'description' => '',
                        'timer_edit_desc' => '',
                        'status' => 1,
                        'created' => $created
                    ];

                    $row = MJTC_includer::MJTC_getTable('timetracking');
                    $MJTC_data = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($MJTC_data);

                    if (!$row->bind($MJTC_data) || !$row->store()) {
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
        $departmet_term = wp_get_object_terms($MJTC_ticketId, 'department');

        if (is_wp_error($departmet_term) || empty($departmet_term[0]->name)) return null;

        // Find corresponding department in destination table

        $name = $departmet_term[0]->name;
        
        $query = "
            SELECT id FROM `" . majesticsupport::$_db->prefix . "mjtc_support_departments`
                WHERE LOWER(departmentname) = '".MJTC_majesticsupportphplib::MJTC_strtolower(MJTC_majesticsupportphplib::MJTC_trim(esc_sql($name)))."'";
        $ms_department_id = majesticsupport::$_db->get_var($query);
        
        return $ms_department_id ? (int)$ms_department_id : null;
    }

    private function getTicketPriorityIdByAwesomeSupport($MJTC_ticketId){
        // Sanitize and validate input
        $MJTC_ticketId = intval($MJTC_ticketId);
        if ($MJTC_ticketId <= 0) return null;

        // Fetch priority from source table
        $priority_term = wp_get_object_terms($MJTC_ticketId, 'ticket_priority');

        if (is_wp_error($priority_term) || empty($priority_term[0]->name)) return null;

        // Find corresponding priority in destination table
        
        $name = $priority_term[0]->name;
        $query = "
            SELECT id FROM `" . majesticsupport::$_db->prefix . "mjtc_support_priorities`
                WHERE LOWER(priority) = '".MJTC_majesticsupportphplib::MJTC_strtolower(MJTC_majesticsupportphplib::MJTC_trim(esc_sql($name)))."'";;
        $ms_priority_id = majesticsupport::$_db->get_var($query);
        
        return $ms_priority_id ? (int)$ms_priority_id : null;
    }

    private function getTicketStatusIdByAwesomeSupport($MJTC_ticket_status) {
        $custom_status = wpas_get_post_status();
        if (empty($MJTC_ticket_status)) return null;

        if (empty($custom_status[$MJTC_ticket_status])) return null;

        // Find matching status in destination table
        $query = "SELECT id FROM `" . majesticsupport::$_db->prefix . "mjtc_support_statuses` WHERE LOWER(status) = '".MJTC_majesticsupportphplib::MJTC_strtolower(MJTC_majesticsupportphplib::MJTC_trim(esc_sql($custom_status[$MJTC_ticket_status])))."'";
        $ms_status_id = majesticsupport::$_db->get_var($query);

        return $ms_status_id ? (int)$ms_status_id : null;
    }

    private function getTicketAgentIdByAwesomeSupport($MJTC_ticketId){
        // Sanitize and validate input
        $MJTC_ticketId = intval($MJTC_ticketId);
        if ($MJTC_ticketId <= 0) return null;

        // Fetch product from source table
        $assigned_agent = get_post_meta( $MJTC_ticketId, '_wpas_assignee', true );

        if (is_wp_error($assigned_agent) || empty($assigned_agent)) return null;

        $mjtc_user = MJTC_includer::MJTC_getObjectClass('user')->MJTC_getmajesticsupportuidbyuserid($assigned_agent);
        if (!empty($mjtc_user) && isset($mjtc_user[0]->id)) {
            $mjtc_uid = (int)$mjtc_user[0]->id;
        } else {
            return;
        }
        
        $query = "
            SELECT id FROM `" . majesticsupport::$_db->prefix . "mjtc_support_staff` WHERE uid = ".$mjtc_uid;
        $ms_agent_id = majesticsupport::$_db->get_var($query);

        return $ms_agent_id ? (int)$ms_agent_id : null;
    }

    private function getTicketProductIdByAwesomeSupport($MJTC_ticketId){
        // Sanitize and validate input
        $MJTC_ticketId = intval($MJTC_ticketId);
        if ($MJTC_ticketId <= 0) return null;

        // Fetch product from source table
        $product_term = wp_get_object_terms($MJTC_ticketId, 'product');

        if (is_wp_error($product_term) || empty($product_term[0]->name)) return null;

        // Find corresponding product in destination table
        
        $name = $product_term[0]->name;
        $query = "
            SELECT id FROM `" . majesticsupport::$_db->prefix . "mjtc_support_products`
                WHERE LOWER(product) = '".MJTC_majesticsupportphplib::MJTC_strtolower(MJTC_majesticsupportphplib::MJTC_trim(esc_sql($name)))."'";
        $ms_product_id = majesticsupport::$_db->get_var($query);
        
        return $ms_product_id ? (int)$ms_product_id : null;
    }

    private function importAwesomeSupportFaqs(){
        // Load previously imported faqs
        $imported_faqs = [];
        $imported_faqs_json = get_option('mjtc_support_ticket_awesome_support_data_faqs');
        if (!empty($imported_faqs_json)) {
            $imported_faqs = json_decode($imported_faqs_json, true);
        }

        // Get SupportCandy faqs (excluding system/default ones)
        $faqs = get_posts( [
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

        if (empty($faqs)) return;

        // Get highest current ordering value
        $query = "
            SELECT MAX(faq.ordering)
            FROM `" . majesticsupport::$_db->prefix . "mjtc_support_faqs` AS faq
        ";
        $ordering = (int) majesticsupport::$_db->get_var($query);

        // Build array of existing JS faqs (cleaned)
        $query = "
            SELECT faq.subject
            FROM `" . majesticsupport::$_db->prefix . "mjtc_support_faqs` AS faq
        ";
        $jsfaqs = majesticsupport::$_db->get_results($query);
        $existing_faq_names = array_map(function($faq) {
            return $this->cleanStringForCompare($faq->subject);
        }, $jsfaqs);

        foreach ($faqs as $faq) {
            $name = $faq->post_title;
            $compare_name = $this->cleanStringForCompare($name);

            // Skip if name already exists
            if (in_array($compare_name, $existing_faq_names)) {
                $this->awesome_support_import_count['faq']['skipped'] += 1;
                continue;
            }

            // Skip if already imported
            if (in_array($faq->ID, $imported_faqs)) {
                $this->awesome_support_import_count['faq']['skipped'] += 1;
                continue;
            }

            $taxonomies = get_object_taxonomies('faq');
            $terms = get_the_terms($faq->ID, $taxonomies[0]);
            if(in_array('knowledgebase', majesticsupport::$_active_addons)) {
                $categoryid = $this->getFaqCategoryIdByAwesomeSupport($terms[0]->name);
            } else {
                $categoryid = '';
            }

            // Prepare new faq data
            $row = MJTC_includer::MJTC_getTable('faq');
            $MJTC_data = [
                'id'            => '',
                'categoryid'    => $categoryid,
                'staffid'       => 0,
                'subject'       => $faq->post_title,
                'content'       => $faq->post_content,
                'views'         => 0,
                'ordering'      => $ordering,
                'created'       => $faq->post_date,
                'status'        => 1,
                'visible'       => 0,
            ];

            $MJTC_data = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($MJTC_data);
            $row->bind($MJTC_data);

            if (!$row->store()) {
                $this->awesome_support_import_count['faq']['failed'] += 1;
            } else {
                $this->awesome_support_faq_ids[] = $faq->id;
                $this->awesome_support_import_count['faq']['imported'] += 1;
                $ordering++;
            }
        }

        // Save updated list of imported faqs
        if (!empty($this->awesome_support_faq_ids)) {
            update_option('mjtc_support_ticket_awesome_support_data_faqs', wp_json_encode($this->awesome_support_faq_ids));
        }
    }

    private function getFaqCategoryIdByAwesomeSupport($name){
        $query = "
            SELECT id FROM `" . majesticsupport::$_db->prefix . "mjtc_support_categories`
                WHERE LOWER(name) = '".MJTC_majesticsupportphplib::MJTC_strtolower(MJTC_majesticsupportphplib::MJTC_trim(esc_sql($name)))."'";
        $ms_category_id = majesticsupport::$_db->get_var($query);
        if (empty($ms_category_id)) {

            $MJTC_data['id'] = '';
            $MJTC_data['name'] = $name;
            $MJTC_data['created'] = date_i18n('Y-m-d H:i:s');

            $kb = '0';
            $downloads = '0';
            $announcement = '0';
            $faqs = '1';

            $MJTC_data['kb'] = $kb;
            $MJTC_data['downloads'] = $downloads;
            $MJTC_data['announcement'] = $announcement;
            $MJTC_data['faqs'] = $faqs;
            $MJTC_data['staffid'] = 0;
            $MJTC_data['status'] = 1;

            $row = MJTC_includer::MJTC_getTable('categories');

            $MJTC_data = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($MJTC_data);// remove slashes with quotes.
            $error = 0;
            if (!$row->bind($MJTC_data)) {
                $error = 1;
            }
            if (!$row->store()) {
                $error = 1;
            }
            if (empty($error)) {
                $ms_category_id = $row->id;
            }
        }
        
        return $ms_category_id ? (int)$ms_category_id : null;
    }

    private function getPostConutByType ( $post_type ) {
        $MJTC_counts = wp_count_posts( $post_type );
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
        $filesystem = new WP_Filesystem_Direct(true);
        $upload_dir = wp_upload_dir();
        $upload_path = $upload_dir['basedir'];
        $MJTC_datadirectory = majesticsupport::$_config['data_directory'];
        $path = $upload_path . "/" . $MJTC_datadirectory;

        if (!$filesystem->exists($path)) {
            wp_mkdir_p($path);
        }
        $path .= '/attachmentdata';
        if (!$filesystem->exists($path)) {
            wp_mkdir_p($path);
        }
        $path .= '/ticket';
        if (!$filesystem->exists($path)) {
            wp_mkdir_p($path);
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
        $imported_tickets = array();
        $imported_tickets_json = get_option('mjtc_support_ticket_fluent_support_data_tickets');
        if (!empty($imported_tickets_json)) {
            $imported_tickets = json_decode($imported_tickets_json, true);
        }

        $query = "SELECT tickets.*
                FROM `" . majesticsupport::$_db->prefix . "fs_tickets` AS tickets
                ORDER BY tickets.id ASC";
        
        $MJTC_tickets = majesticsupport::$_db->get_results($query);
        
        $new_tickets = array();
        foreach ($MJTC_tickets as $MJTC_ticket) {
            // Skip if ticket already imported
            if (!empty($imported_tickets) && in_array($MJTC_ticket->id, $imported_tickets)) {
                $this->fluent_support_import_count['ticket']['skipped'] += 1;
                continue;
            }

            // Map custom fields
            $params = array();
            $query = "SELECT meta.*
                        FROM `" . majesticsupport::$_db->prefix . "fs_meta` AS meta
                        WHERE object_type = 'ticket_meta'
                        AND object_id = ".$MJTC_ticket->id.";";
            $MJTC_tickets_meta = majesticsupport::$_db->get_results($query);
            foreach($MJTC_tickets_meta as $MJTC_ticket_meta){
                foreach ($this->fc_ticket_cf as $fs_ticket_custom_field => $mjtc_support_custom_field) {
                    if($MJTC_ticket_meta->key == $fs_ticket_custom_field){
                        $custom_field_value = "";
                        $custom_field_value = $MJTC_ticket_meta->value;
                        $custom_field_value = MJTC_majesticsupportphplib::MJTC_str_replace("|","",$custom_field_value);
                        $custom_field_value = MJTC_majesticsupportphplib::MJTC_str_replace("|","",$custom_field_value);
                        $vardata = "";
                        
                        $fieldtype = $this->checkTypeOfTheField($fs_ticket_custom_field);
                        if($fieldtype == "date"){
                            $vardata = gmdate("Y-m-d", MJTC_majesticsupportphplib::MJTC_strtotime($custom_field_value));
                        }else{
                            $vardata = $custom_field_value;
                        }
                        if($vardata != ''){
                            if(is_array($vardata)){
                                $vardata = implode(', ', array_filter($vardata));
                            }
                            $params[$mjtc_support_custom_field] = MJTC_majesticsupportphplib::MJTC_htmlentities($vardata);
                        }
                    }
                }
            }
            $MJTC_ticketparams = html_entity_decode(wp_json_encode($params, JSON_UNESCAPED_UNICODE));

            // Get linked data
            $userinfo = $this->getFluentSupportTicketCustomerInfo($MJTC_ticket->customer_id);
            $agentid = $this->getTicketAgentIdByFluentSupport($MJTC_ticket->agent_id);
            $productid = $this->getTicketProductIdByFluentSupport($MJTC_ticket->product_id);
            $priorityid = $this->getTicketPriorityIdByFluentSupport($MJTC_ticket->client_priority);

            $idresult = MJTC_includer::MJTC_getModel('ticket')->getRandomTicketId();
            $MJTC_ticketid = $idresult['ticketid'];
            $customticketno = $idresult['customticketno'];
            $attachmentdir = MJTC_includer::MJTC_getModel('ticket')->getRandomFolderName();

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

            $isanswered = ($MJTC_ticket_status == 4) ? 1 : 0;

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

            $newTicketData = [
                'id' => "",
                'uid' => $userinfo["ms_uid"],
                'ticketid' => $MJTC_ticketid,
                'productid' => $productid,
                'priorityid' => $priorityid,
                'staffid' => $agentid,
                'email' => $userinfo["customer_email"],
                'name' => $userinfo["customer_name"],
                'subject' => $MJTC_ticket->title,
                'message' => $MJTC_ticket->content,
                'helptopicid' => 0,
                'multiformid' => 1,
                'phone' => "",
                'phoneext' => "",
                'status' => $MJTC_ticket_status,
                'isoverdue' => "0",
                'isanswered' => $isanswered,
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
                'attachmentdir' => $attachmentdir,
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
                'customticketno' => $customticketno
            ];

            $row = MJTC_includer::MJTC_getTable('tickets');
            $error = 0;
            if (!$row->bind($newTicketData)) $error = 1;
            if (!$row->store()) $error = 1;

            if ($error == 1) {
                $this->fluent_support_import_count['ticket']['failed'] += 1;
            } else {
                $this->fluent_support_ticket_ids[] = $MJTC_ticket->id;
                $this->fluent_support_import_count['ticket']['imported'] += 1;

                $ms_ticketid = $row->id;
                $hash = MJTC_includer::MJTC_getModel('ticket')->generateHash($ms_ticketid);
                $query = "UPDATE `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` SET `hash`='" . esc_sql($hash) . "' WHERE id=" . esc_sql($ms_ticketid);
                majesticsupport::$_db->query($query);

                if(in_array('note', majesticsupport::$_active_addons)){
                    $this->getFluentSupportTicketNotes($ms_ticketid, $MJTC_ticket->id, $attachmentdir);
                }
                $this->getFluentSupportTicketReplies($ms_ticketid, $MJTC_ticket->id, $attachmentdir);
                $this->getFluentSupportTicketAttachments($ms_ticketid, $MJTC_ticket->id, $attachmentdir);

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
        $query = "
            SELECT * FROM `" . majesticsupport::$_db->prefix . "fs_meta`
            WHERE object_type = 'option' AND `key` = '_ticket_custom_fields';";
        $custom_fields_serializeed = majesticsupport::$_db->get_row($query);

        if (!$custom_fields_serializeed) return;

        $custom_fields = unserialize($custom_fields_serializeed->value);
        

        if (!$custom_fields) return;

        $this->fc_ticket_cf = [];


        foreach ($custom_fields as $custom_field) {
            // Map field types
            switch ($custom_field["type"]){
                case "text":
                    $fieldtype = "text"; break;
                case "select-one":
                    $fieldtype = "combo"; break;
                case "radio":
                    $fieldtype = "radio"; break;
                case "checkbox":
                    $fieldtype = "checkbox"; break;
                case "textarea":
                    $fieldtype = "textarea"; break;
                case "number":
                    $fieldtype = "text"; break;
                default:
                    $fieldtype = "text"; break;
            }

            $query = "SELECT id,field FROM `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` WHERE isuserfield = 1 AND LOWER(fieldtitle) ='".esc_sql(MJTC_majesticsupportphplib::MJTC_strtolower($custom_field['label']))."' AND userfieldtype ='".esc_sql($fieldtype)."' AND fieldfor = 1";
            $field_record = majesticsupport::$_db->get_row($query);

            if(!empty($field_record)){ // this will make sure
                $this->fluent_support_import_count['field']['skipped'] += 1;
                continue;
            }

            // Load options for select-type fields
            $option_values = [];
            if(isset($custom_field["options"])){
                foreach($custom_field["options"] as $MJTC_key => $MJTC_value){
                    $option_values[] = $MJTC_value;
                }
            }
            // required
            $required = 0;
            if(isset($custom_field["required"])){
                if($custom_field["required"] == "yes") $required = 1;
            }
            // admin olny
            $adminonly = 0;
            if(isset($custom_field["admin_only"])){
                if($custom_field["admin_only"] == "yes") $adminonly = 1;
            }
            // placeholder
            $placeholder = '';
            if(isset($custom_field["placeholder"])){
                $placeholder = $custom_field["placeholder"];
            }

            // Build visibility data
            $visibledata = [
                "visibleLogic" => [],
                "visibleParent" => [],
                "visibleValue" => [],
                "visibleCondition" => [],
            ];

            // Prepare field data for import
            $fieldOrderingData = [
                "id" => "",
                "field" => $custom_field["slug"],
                "fieldtitle" => $custom_field['label'],
                "ordering" => "",
                "section" => "10",
                "fieldfor" => "1",
                "published" => "1",
                "sys" => "0",
                "cannotunpublish" => "0",
                "required" => $required,
                "size" => "100",
                "maxlength" => "255",
                "cols" => "",
                "rows" => "",
                "isuserfield" => "1",
                "userfieldtype" => $fieldtype,
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
                "values" => $option_values,
                "visibleParent" => $visibledata["visibleParent"],
                "visibleValue" => $visibledata["visibleValue"],
                "visibleCondition" => $visibledata["visibleCondition"],
                "visibleLogic" => $visibledata["visibleLogic"],
                "placeholder" => $placeholder,
                "description" => '',
                "defaultvalue" => '',
                "readonly" => '',
                "adminonly" => $adminonly,
            ];

            // Store field in SupportCandy
            $record_saved = MJTC_includer::MJTC_getModel('fieldordering')->storeUserField($fieldOrderingData);

            if ($record_saved == 1) {
                $query = "SELECT * FROM `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` ORDER BY id DESC LIMIT 1";
                $latest_record = majesticsupport::$_db->get_row($query);
                $this->fc_ticket_cf[$custom_field["slug"]] = $latest_record->field;

                $this->fluent_support_import_count['field']['imported'] += 1;
            } else {
                $this->fluent_support_import_count['field']['failed'] += 1;
            }
        }

        foreach ($custom_fields as $custom_field) {
            $field = $this->getTicketCustomFieldId($custom_field['label']);
            $query = "SELECT * FROM `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` WHERE field = '".esc_sql($field)."' LIMIT 1";
            $ms_field = majesticsupport::$_db->get_row($query);
            if (empty($ms_field)) {
                continue;
            }
            

            // Build visibility data
            $visibledata = [
                "visibleLogic" => [],
                "visibleParent" => [],
                "visibleValue" => [],
                "visibleCondition" => [],
            ];

            
            if (!empty($custom_field['conditions'])) {
                $visibleLogic = 'AND';
                if(isset($custom_field["match_type"]) && $custom_field["match_type"] == 'any'){
                    $visibleLogic = 'OR';
                }

                foreach ($custom_field['conditions'] as $groupIndex => $group) {
                    $fieldtype = '';
                    if ($group['item_key'] == 'ticket_content' || $group['item_key'] == 'ticket_product_id') {
                        continue;
                    }
                    if ($group['item_key'] == 'ticket_client_priority') {
                        $item_key = 'priority';
                        $group['value'];
                        $MJTC_value = $this->getTicketPriorityIdByFluentSupport($group['value']);
                        $fieldtype = 'priority';
                    } elseif ($group['item_key'] == 'ticket_title') {
                        $item_key = 'subject';
                        $MJTC_value = $group['value'];
                        $fieldtype = 'subject';
                    } else {
                        // $item_key = $group['item_key'];
                        if (empty($group['item_key'])) {
                            continue;
                        }
                        $item_key = $this->fc_ticket_cf[$group['item_key']];
                        $fieldtype = $this->checkTypeOfTheField($item_key);
                        if ($fieldtype == 'textarea') {
                            continue;
                        }
                        $MJTC_value = $group['value'];
                    }
                    if ($custom_field["slug"] == 'ticket_client_priority') {
                        $slug = 'priority';
                    } elseif ($custom_field["slug"] == 'ticket_title') {
                        $slug = 'subject';
                    } else {
                        $slug = $this->fc_ticket_cf[$custom_field["slug"]];
                    }
                    
                    $visibledata["visibleParentField"][] = $slug;
                    $visibledata["visibleParent"][] = $item_key;
                    $visibledata["visibleCondition"][] = $this->mapOperatorToConditionCodeForFluentSupport($group['operator'], $fieldtype);
                    $visibledata["visibleValue"][] = $MJTC_value;
                    $visibledata["visibleLogic"][] = $visibleLogic;
                }
            }

            $option_values = [];
            if(isset($custom_field["options"])){
                foreach($custom_field["options"] as $MJTC_key => $MJTC_value){
                    $option_values[] = $MJTC_value;
                }
            }

            // Prepare field data for import

            $fieldOrderingData = [
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
                "values" => $option_values,
                "visibleParent" => $visibledata["visibleParent"],
                "visibleValue" => $visibledata["visibleValue"],
                "visibleCondition" => $visibledata["visibleCondition"],
                "visibleLogic" => $visibledata["visibleLogic"],
            ];

            // Store field in SupportCandy
            $record_saved = MJTC_includer::MJTC_getModel('fieldordering')->storeUserField($fieldOrderingData);
        }
    }

    private function checkTypeOfTheField($field){
        
        $query = "
            SELECT userfieldtype FROM `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering`
                WHERE LOWER(field) = '".MJTC_majesticsupportphplib::MJTC_strtolower(MJTC_majesticsupportphplib::MJTC_trim(esc_sql($field)))."'";
        $userfieldtype = majesticsupport::$_db->get_var($query);
        
        return $userfieldtype ? $userfieldtype : null;
    }

    private function getTicketCustomFieldId($fieldtitle){
        
        $query = "
            SELECT field FROM `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering`
                WHERE LOWER(fieldtitle) = '".MJTC_majesticsupportphplib::MJTC_strtolower(MJTC_majesticsupportphplib::MJTC_trim(esc_sql($fieldtitle)))."'";
        $ms_field_id = majesticsupport::$_db->get_var($query);
        
        return $ms_field_id ? $ms_field_id : null;
    }

    private function mapOperatorToConditionCodeForFluentSupport ($operator, $type) {
        $operator = strtoupper(MJTC_majesticsupportphplib::MJTC_trim($operator));
        $isComplex = false;

        if (!empty($type)) {
            $complexTypes = ['combo', 'checkbox', 'radio', 'multiple','priority'];
            $isComplex = !in_array($type, $complexTypes);
        }

        switch (strtoupper($operator)) {
            case '=':
            case 'CONTAINS':
                return $isComplex ? "2" : "1";
            case '!=':
            case 'NOT_CONTAINS':
                return $isComplex ? "3" : "0";
            default:
                return $isComplex ? "4" : "0"; // Fallback or unsupported operator
        }
    }

    private function getFluentSupportTicketNotes($ms_ticket_id, $fs_ticket_id, $attachmentdir){
        $query = "SELECT conversation.*
                    FROM `" . majesticsupport::$_db->prefix . "fs_conversations` AS conversation
                    WHERE conversation.ticket_id = ".$fs_ticket_id."
                    AND conversation.conversation_type = 'note'
                    ORDER BY conversation.id ASC";
                    
        $conversations = majesticsupport::$_db->get_results($query);
        foreach($conversations AS $conversation){

            $query = "
            SELECT id FROM `" . majesticsupport::$_db->prefix . "mjtc_support_users` WHERE wpuid = ".$conversation->person_id;
            $agentid = $ms_user_id = majesticsupport::$_db->get_var($query);
            $filename = $this->getFluentSupportNoteAttachments($fs_ticket_id, $conversation->id, $attachmentdir);

            $replyData = [
                "id" => "",
                "ticketid" => $ms_ticket_id,
                "staffid" => $agentid,
                "title" => MJTC_majesticsupportphplib::MJTC_strip_tags($conversation->content),
                "note" => $conversation->content,
                "status" => "1",
                "created" => $conversation->created_at,
                "filename" => $filename,
                "filesize" => 5334
            ];
            $row = MJTC_includer::MJTC_getTable('note');
            $MJTC_data = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($replyData);// remove slashes with quotes.
            $error = 0;
            if (!$row->bind($MJTC_data)) {
                $error = 1;
            }
            if (!$row->store()) {
                $error = 1;
            }
            $ms_ticket_note_id = $row->id;
        }
    }

    private function getFluentSupportTicketReplies($ms_ticket_id, $fs_ticket_id, $attachmentdir){
        $query = "SELECT conversation.*
                    FROM `" . majesticsupport::$_db->prefix . "fs_conversations` AS conversation
                    WHERE conversation.ticket_id = ".$fs_ticket_id."
                    AND conversation.conversation_type = 'response'
                    ORDER BY conversation.id ASC";
                    
        $conversations = majesticsupport::$_db->get_results($query);
        foreach($conversations AS $conversation){
            $userinfo = $this->getFluentSupportTicketCustomerInfo($conversation->person_id);
            $uid = $userinfo["ms_uid"];
            $name = $userinfo["customer_name"];
            if(empty($userinfo["ms_uid"])){

                $agentid = $this->getTicketAgentIDByFluentSupport($conversation->person_id);
                if($agentid){
                    $query = "SELECT agent.*
                                FROM `" . majesticsupport::$_db->prefix . "mjtc_support_staff` AS agent
                                WHERE agent.id = ".$agentid.";";
                                
                    $agent = majesticsupport::$_db->get_row($query);
                    $uid = $agent->uid;
                    $name = $agent->firstname;
                    if($agent->lastname) $name = $name. " ". $agent->lastname;
                }
            }

            $replyData = [
                "id" => "",
                "uid" => $uid,
                "ticketid" => $ms_ticket_id,
                "name" => $name,
                "message" => $conversation->content,
                "staffid" => "",
                "rating" => "",
                "status" => "1",
                "created" => $conversation->created_at,
                "ticketviaemail" => "",
                "viewed_by" => "",
                "viewed_on" => ""
            ];
            $row = MJTC_includer::MJTC_getTable('replies');
            $MJTC_data = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($replyData);// remove slashes with quotes.
            $error = 0;
            if (!$row->bind($MJTC_data)) {
                $error = 1;
            }
            if (!$row->store()) {
                $error = 1;
            }
            $ms_ticket_reply_id = $row->id;
            $this->getFluentSupportReplyAttachments($ms_ticket_id, $ms_ticket_reply_id, $fs_ticket_id, $conversation->id, $attachmentdir);
        }
    }

    private function getFluentSupportNoteAttachments($fs_ticket_id, $fs_ticket_reply_id, $attachmentdir){
        $query = "SELECT attachment.*
                    FROM `" . majesticsupport::$_db->prefix . "fs_attachments` AS attachment
                    WHERE attachment.ticket_id = " . (int)$fs_ticket_id . " AND attachment.conversation_id = " . (int)$fs_ticket_reply_id . "
                    ORDER BY attachment.id ASC";
                    
        $attachment = majesticsupport::$_db->get_row($query);

        if (empty($attachment)) return;

        require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
        require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';

        $filesystem = new WP_Filesystem_Direct(true);
        $upload_dir = wp_upload_dir();
        $upload_path = $upload_dir['basedir'];
        $MJTC_datadirectory = majesticsupport::$_config['data_directory'];
        $path = trailingslashit($upload_path) . $MJTC_datadirectory . "/attachmentdata/ticket/" . sanitize_file_name($attachmentdir);

        if (!$filesystem->exists($path)) {
            wp_mkdir_p($path);
        }

        $safe_filename = sanitize_file_name($attachment->title);
        $source = $attachment->file_path;
        $destination = $path . "/" . $safe_filename;
        
        $destination_new_name = $path."/".$attachment->title;

        if (!file_exists($source)) {
            return '';
        }

        $result = $filesystem->copy($source, $destination, true);
        if (!$result) {
            return '';
        }
        rename($destination,$destination_new_name);

        return $safe_filename;
    }

    private function getFluentSupportReplyAttachments($ms_ticket_id, $ms_ticket_reply_id, $fs_ticket_id, $fs_ticket_reply_id, $attachmentdir){
        $query = "SELECT attachment.*
                    FROM `" . majesticsupport::$_db->prefix . "fs_attachments` AS attachment
                    WHERE attachment.ticket_id = " . (int)$fs_ticket_id . " AND attachment.conversation_id = " . (int)$fs_ticket_reply_id . "
                    ORDER BY attachment.id ASC";
                    
        $attachments = majesticsupport::$_db->get_results($query);

        if (empty($attachments)) return;

        require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
        require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';

        $filesystem = new WP_Filesystem_Direct(true);
        $upload_dir = wp_upload_dir();
        $upload_path = $upload_dir['basedir'];
        $MJTC_datadirectory = majesticsupport::$_config['data_directory'];
        $path = trailingslashit($upload_path) . $MJTC_datadirectory . "/attachmentdata/ticket/" . sanitize_file_name($attachmentdir);

        if (!$filesystem->exists($path)) {
            wp_mkdir_p($path);
        }

        foreach ($attachments as $attachment) {
            $safe_filename = sanitize_file_name($attachment->title);
            $source = $attachment->file_path;
            $destination = $path . "/" . $safe_filename;
            $destination_new_name = $path."/".$attachment->title;
            $attachmentData = [
                "id" => "",
                "ticketid" => $ms_ticket_id,
                "replyattachmentid" => $ms_ticket_reply_id,
                "filesize" => "", // Optionally: filesize($source)
                "filename" => $safe_filename,
                "filekey" => "",
                "deleted" => "",
                "status" => "1",
                "created" => $attachment->created_at
            ];

            $row = MJTC_includer::MJTC_getTable('attachments');
            $MJTC_data = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($attachmentData);

            $error = 0;
            if (!$row->bind($MJTC_data)) {
                $error = 1;
            }
            if (!$row->store()) {
                $error = 1;
            }

            if (!file_exists($source)) {
                error_log("Attachment source file does not exist: " . $source);
                continue;
            }

            $result = $filesystem->copy($source, $destination, true);
            if (!$result) {
                error_log("Failed to copy attachment from $source to $destination");
            }
            rename($destination,$destination_new_name);         
        }
    }

    private function getFluentSupportTicketAttachments($ms_ticket_id, $fs_ticket_id, $attachmentdir){
        $query = "SELECT attachment.*
                    FROM `" . majesticsupport::$_db->prefix . "fs_attachments` AS attachment
                    WHERE attachment.ticket_id = " . (int)$fs_ticket_id . " AND attachment.conversation_id  IS NULL
                    ORDER BY attachment.id ASC";
                    
        $attachments = majesticsupport::$_db->get_results($query);

        if (empty($attachments)) return;

        require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
        require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';

        $filesystem = new WP_Filesystem_Direct(true);
        $upload_dir = wp_upload_dir();
        $upload_path = $upload_dir['basedir'];
        $MJTC_datadirectory = majesticsupport::$_config['data_directory'];
        $path = trailingslashit($upload_path) . $MJTC_datadirectory . "/attachmentdata/ticket/" . sanitize_file_name($attachmentdir);

        if (!$filesystem->exists($path)) {
            wp_mkdir_p($path);
        }

        foreach ($attachments as $attachment) {
            $safe_filename = sanitize_file_name($attachment->title);
            $source = $attachment->file_path;
            $destination = $path . "/" . $safe_filename;
            $destination_new_name = $path."/".$attachment->title;
            $attachmentData = [
                "id" => "",
                "ticketid" => $ms_ticket_id,
                "replyattachmentid" => 0,
                "filesize" => "", // Optionally: filesize($source)
                "filename" => $safe_filename,
                "filekey" => "",
                "deleted" => "",
                "status" => "1",
                "created" => $attachment->created_at
            ];

            $row = MJTC_includer::MJTC_getTable('attachments');
            $MJTC_data = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($attachmentData);

            $error = 0;
            if (!$row->bind($MJTC_data)) {
                $error = 1;
            }
            if (!$row->store()) {
                $error = 1;
            }

            if (!file_exists($source)) {
                error_log("Attachment source file does not exist: " . $source);
                continue;
            }

            $result = $filesystem->copy($source, $destination, true);
            if (!$result) {
                error_log("Failed to copy attachment from $source to $destination");
            }
            rename($destination,$destination_new_name);         
        }
    }

    private function getFluentSupportTicketActivityLog($ms_ticket_id, $fs_ticket_id) {
        $fs_ticket_id = intval($fs_ticket_id);
        $ms_ticket_id = intval($ms_ticket_id);

        if ($fs_ticket_id <= 0 || $ms_ticket_id <= 0) return;

        $query = "
            SELECT * FROM `" . majesticsupport::$_db->prefix . "fs_activities`
            WHERE  object_type = 'ticket' AND object_id = ".$fs_ticket_id."
            ORDER BY id DESC";

        $threads = majesticsupport::$_db->get_results($query);
        if (empty($threads)) return;

        foreach ($threads as $thread) {
            $MJTC_ticketid = $ms_ticket_id;

            // Get user information
            $userinfo = $this->getFluentSupportTicketCustomerInfo($thread->person_id);
            $currentUserName = !empty($userinfo['customer_name']) 
                ? esc_html($userinfo['customer_name']) 
                : esc_html(__('Guest', 'majestic-support'));

            $messagetype = __('Successfully', 'majestic-support');
            $eventtype = MJTC_majesticsupportphplib::MJTC_str_replace("fluent_support/","",$thread->event_type);
            $message = MJTC_majesticsupportphplib::MJTC_strip_tags($thread->description);
            

            if (!empty($eventtype) && !empty($message)) {
                MJTC_includer::MJTC_getModel('tickethistory')->addActivityLog(
                    $MJTC_ticketid, 1, esc_html($eventtype), esc_html($message), esc_html($messagetype)
                );
            }
        }
    }

    private function getFluentSupportTicketStaffTime($ms_ticket_id, $fs_ticket_id) {
        $fs_ticket_id = intval($fs_ticket_id);
        $ms_ticket_id = intval($ms_ticket_id);
        if ($fs_ticket_id <= 0 || $ms_ticket_id <= 0) return;

        // Get all timer logs for the given SupportCandy ticket
        $query = "
            SELECT * FROM `" . majesticsupport::$_db->prefix . "fs_time_tracks`
            WHERE ticket_id = ".$fs_ticket_id;
        $timers = majesticsupport::$_db->get_results($query);

        if (empty($timers)) return;

        foreach ($timers as $timer) {
            // Get HelpDesk staff ID from FluentSupport agent ID
            $staffid = $this->getMSAgentIdByFSAgentId($timer->agent_id);

            $created = $timer->created_at;

            $timer_minutes = $timer->working_minutes;
            if ($timer_minutes <= 0) continue;

            $timer_seconds = $timer_minutes * 60;
            // Conflict detection
            $created_dt = new DateTime($created);
            $now = new DateTime();
            $interval_to_now = $created_dt->diff($now);
            $systemtime = ($interval_to_now->days * 86400) + ($interval_to_now->h * 3600) + ($interval_to_now->i * 60) + $interval_to_now->s;

            $conflict = ($timer_seconds > $systemtime) ? 1 : 0;

            // Prepare data
            $MJTC_data = [
                'staffid' => $staffid,
                'ticketid' => $ms_ticket_id,
                'referencefor' => 1,
                'referenceid' => 0,
                'usertime' => $timer_seconds,
                'systemtime' => $systemtime,
                'conflict' => $conflict,
                'description' => $timer->message,
                'timer_edit_desc' => $timer->message,
                'status' => 1,
                'created' => $created
            ];

            $row = MJTC_includer::MJTC_getTable('timetracking');
            $MJTC_data = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($MJTC_data);

            if (!$row->bind($MJTC_data) || !$row->store()) {
                // optionally log or count the failure
                continue;
            }
        }
    }

    private function getMSAgentIdByFSAgentId($fs_agent_id) {
        // Sanitize and validate input
        $fs_agent_id = intval($fs_agent_id);
        if ($fs_agent_id <= 0) return null;

        // Secure SQL query using prepare()
        $query = "
            SELECT agent.*
            FROM `" . majesticsupport::$_db->prefix . "fs_persons` AS fs_agent
            INNER JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_users` AS user
                ON user.wpuid = fs_agent.user_id
            INNER JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_staff` AS agent
                ON agent.uid = user.id
            WHERE fs_agent.person_type = 'agent' AND fs_agent.id = " . esc_sql($fs_agent_id) . "
            LIMIT 1
        ";

        $ms_agent = majesticsupport::$_db->get_row($query);

        return $ms_agent ?: null;
    }

    private function getFluentSupportTicketCustomerInfo($customerId) {
        // Sanitize and validate customer ID
        $customerId = intval($customerId);
        if ($customerId <= 0) {
            return [
                "ms_uid" => "",
                "customer_name" => "",
                "customer_email" => ""
            ];
        }

        // Prepare secure query
        $query = "
            SELECT CONCAT(customer.first_name, ' ', customer.last_name) AS name, customer.email, user.id AS ms_uid
            FROM `" . majesticsupport::$_db->prefix . "fs_persons` AS customer
            INNER JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_users` AS user
                ON user.wpuid = customer.user_id
            WHERE customer.id = " . esc_sql($customerId) . "
            AND customer.person_type = 'customer'
            LIMIT 1
        ";

        $MJTC_data = majesticsupport::$_db->get_row($query);

        return [
            "ms_uid"       => $MJTC_data->ms_uid ?? "",
            "customer_name"  => $MJTC_data->name ?? "",
            "customer_email" => $MJTC_data->email ?? ""
        ];
    }

    private function getTicketAgentIdByFluentSupport($customerId) {
        // Validate customer ID
        $customerId = intval($customerId);
        if ($customerId <= 0) {
            return null;
        }

        // Get mapped user info
        $query = "SELECT agent.id
                    FROM `" . majesticsupport::$_db->prefix . "fs_persons` AS person
            INNER JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_users` AS user
                ON user.wpuid = person.user_id
            INNER JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_staff` AS agent
                ON agent.uid = user.id
                    WHERE person.id = " . esc_sql($customerId) . "
                    AND person.person_type = 'agent';";
        $ms_agent = majesticsupport::$_db->get_var($query);

        return $ms_agent ?: null;
    }

    private function getTicketProductIdByFluentSupport($productId){
        // Sanitize and validate input
        $productId = intval($productId);
        if ($productId <= 0) return null;

        // Fetch product from source table
        $query = "
            SELECT title
            FROM `" . majesticsupport::$_db->prefix . "fs_products` 
            WHERE id = ".$productId;
        $product_name = majesticsupport::$_db->get_var($query);

        if (empty($product_name)) return null;

        // Find corresponding product in destination table
        
        $name = $product_name;
        $query = "
            SELECT id FROM `" . majesticsupport::$_db->prefix . "mjtc_support_products`
                WHERE LOWER(product) = '".MJTC_majesticsupportphplib::MJTC_strtolower(MJTC_majesticsupportphplib::MJTC_trim(esc_sql($name)))."'";;
        $ms_product_id = majesticsupport::$_db->get_var($query);
        
        return $ms_product_id ? (int)$ms_product_id : null;
    }

    private function getTicketPriorityIdByFluentSupport($prioritName) {
        
        // Find corresponding priority in destination table
        $query = "
            SELECT id FROM `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` 
                WHERE LOWER(priority) = '".MJTC_majesticsupportphplib::MJTC_strtolower(MJTC_majesticsupportphplib::MJTC_trim(esc_sql($prioritName)))."'";
        $ms_priority_id = majesticsupport::$_db->get_var($query);

        return $ms_priority_id ? (int)$ms_priority_id : null;
    }

    private function importFluentSupportUsers() {
        // check if user already processed for import
        $imported_users = array();
        $imported_users_json = get_option('mjtc_support_ticket_fluent_support_data_users');
        if(!empty($imported_users_json)){
            $imported_users = json_decode($imported_users_json,true);
        }

        // Fetch all customers
        $query = "SELECT * FROM `" . majesticsupport::$_db->prefix . "fs_persons` WHERE person_type = 'customer' OR person_type = 'agent'";
        $customers = majesticsupport::$_db->get_results($query);

        if (empty($customers)) return;

        foreach ($customers as $customer) {
            if(!empty($customer->user_id)){
                $wpuid = intval($customer->user_id);
            }else{
                $query = "SELECT user.ID
                    FROM `" . majesticsupport::$_db->prefix . "users` AS user
                    WHERE user.user_email = '".esc_sql($customer->email)."'";
                $user = majesticsupport::$_db->get_row($query);
                if($user) $wpuid = intval($user->ID);
            }
            if (empty($wpuid)) {
                $this->fluent_support_import_count['user']['skipped']++;
                continue;
            }
            $customer_id = intval($customer->id);
            $name        = sanitize_text_field($customer->first_name ?? '');
            if($customer->last_name) $name = $name." ".sanitize_text_field($customer->last_name);
            $email       = sanitize_email($customer->email ?? '');

            // Skip if already imported
            if (in_array($customer_id, $imported_users, true)) {
                $this->fluent_support_import_count['user']['skipped']++;
                continue;
            }

            // Check if user already exists
            $user_query = "SELECT user.*
                       FROM `" . majesticsupport::$_db->prefix . "mjtc_support_users` AS user
                       WHERE user.wpuid = ".$wpuid;
            $existing_user = majesticsupport::$_db->get_row($user_query);

            if ($existing_user) {
                $this->fluent_support_import_count['user']['skipped']++;
                continue;
            }

            // Prepare data for new user
            $row = MJTC_includer::MJTC_getTable('users');
            $MJTC_data = [
                'id'            => '',
                'wpuid'         => $wpuid,
                'name'          => $name,
                'display_name'  => $name,
                'user_email'    => $email,
                'status'        => 1,
                'issocial'      => 0,
                'socialid'      => null,
                'autogenerated' => 0,
            ];

            // Attempt to save the new user
            $row->bind($MJTC_data);
            if (!$row->store()) {
                $this->fluent_support_import_count['user']['failed']++;
                continue;
            }

            // Store successful import info
            $this->fluent_support_users_array[$customer_id] = $row->id;
            $this->fluent_support_user_ids[] = $customer_id;
            $this->fluent_support_import_count['user']['imported']++;
        }

        // Save list of imported user IDs
        if (!empty($this->fluent_support_user_ids)) {
            update_option('mjtc_support_ticket_fluent_support_data_users', wp_json_encode(array_unique(array_merge($imported_users, $this->fluent_support_user_ids))));
        }
    }

    private function importFluentSupportAgents() {
        // check if user already processed for import
        $imported_agents = array();
        $imported_agent_json = get_option('mjtc_support_ticket_fluent_support_data_agents');
        if(!empty($imported_agents_json)){
            $imported_agents = json_decode($imported_agents_json,true);
        }
        $query = "
            SELECT agent.*
            FROM `" . majesticsupport::$_db->prefix . "fs_persons` AS agent
            WHERE agent.person_type = 'agent';";
        $agents = majesticsupport::$_db->get_results($query);

        if($agents){
            foreach($agents AS $agent){
                // Failed if addon not installed
                if (!in_array('agent', majesticsupport::$_active_addons) ) {
                    $this->fluent_support_import_count['agent']['failed']++;
                    continue;
                }
                $wpuid = (int) $agent->user_id;
                // Skip if already imported
                if (in_array($wpuid, $imported_agents, true)) {
                    $this->fluent_support_import_count['agent']['skipped']++;
                    continue;
                }
                $first_name = $agent->first_name;
                $last_name = $agent->last_name;
                if($agent->status == "active") $agent_status = 1; else $agent_status = 0;

                $query = "SELECT user.*
                            FROM `" . majesticsupport::$_db->prefix . "users` AS user
                            WHERE user.id = " . $wpuid;
                $wpuser = majesticsupport::$_db->get_row($query);

                if(!$wpuser){
                    $this->fluent_support_import_count['agent']['failed'] += 1;
                    continue;
                }
                $mjtc_user = MJTC_includer::MJTC_getObjectClass('user')->MJTC_getmajesticsupportuidbyuserid($wpuid);
                if (!empty($mjtc_user) && isset($mjtc_user[0]->id)) {
                    $mjtc_uid = (int)$mjtc_user[0]->id;
                } else {
                    $this->fluent_support_import_count['agent']['failed']++;
                    continue;
                }

                $query = "SELECT staff.*
                            FROM `" . majesticsupport::$_db->prefix . "mjtc_support_staff` AS staff
                            WHERE staff.uid = " . $mjtc_uid;
                $staff = majesticsupport::$_db->get_row($query);

                if (!$staff) {
                    $roleid = $this->getAgentRoleIdByFluentSupport($wpuid);

                    $timestamp = date_i18n('Y-m-d H:i:s');

                    $MJTC_data = [
                        'id'           => '',
                        'uid'          => $mjtc_uid,
                        'groupid'      => '',
                        'roleid'       => $roleid,
                        'departmentid' => '',
                        'firstname'    => $first_name,
                        'lastname'     => $last_name,
                        'username'     => $wpuser->user_login,
                        'email'        => $wpuser->user_email,
                        'signature'    => '',
                        'isadmin'      => '',
                        'status'       => $agent_status,
                        'updated'      => $timestamp,
                        'created'      => $timestamp
                    ];

                    MJTC_includer::MJTC_getModel('agent')->storeStaff($MJTC_data);

                    $this->fluent_support_import_count['agent']['imported'] += 1;
                    $this->fluent_support_agent_ids[] = $wpuid;

                } else {
                    $this->fluent_support_import_count['agent']['skipped'] += 1;
                }
            }
            // Save list of imported agent IDs
            if (!empty($this->fluent_support_agent_ids)) {
                update_option('mjtc_support_ticket_fluent_support_data_agents', wp_json_encode(array_unique(array_merge($imported_agents, $this->fluent_support_agent_ids))));
            }
        }
    }

    private function getAgentRoleIdByFluentSupport($id) {
        $capabilities = get_user_meta($id, majesticsupport::$_db->prefix . 'capabilities', true);
        $isAdmin = !empty($capabilities['administrator']);
        $output = [];

        // Define capability-to-permission mappings
        $capabilityPermissions = [
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
        foreach ($capabilityPermissions as $capKey => $permissions) {
            if (!empty($capabilities[$capKey]) || $isAdmin) {
                $output = array_merge($output, $permissions);
            }
        }

        $name = 'Fluent Support Agent ' . $id;

        $MJTC_data = [
            'name'          => $name,
            'roleperdata'   => $output,
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
        $query = 'SELECT id FROM `' . majesticsupport::$_db->prefix . 'mjtc_support_acl_roles` WHERE name = "' . esc_sql($name) . '"';
        $id = majesticsupport::$_db->get_var($query);
        return $id;
    }

    private function importFluentSupportProducts(){
        // check if product already processed for import
        $imported_products = array();
        $imported_products_json = get_option('mjtc_support_ticket_fluent_support_data_products');
        if(!empty($imported_products_json)){
            $imported_products = json_decode($imported_products_json,true);
        }

        $query = "SELECT product.* FROM `" . majesticsupport::$_db->prefix . "fs_products` AS product;";
        $products = majesticsupport::$_db->get_results($query);

        if (empty($products)) return;
        
        // Get highest current ordering value
        $query = "
            SELECT MAX(product.ordering)
            FROM `" . majesticsupport::$_db->prefix . "mjtc_support_products` AS product
        ";
        $ordering = (int) majesticsupport::$_db->get_var($query);

        foreach($products AS $product){
            // Skip if already imported
            if (in_array($product->id, $imported_products, true)) {
                $this->fluent_support_import_count['product']['skipped']++;
                continue;
            }

            $name = MJTC_majesticsupportphplib::MJTC_trim(MJTC_majesticsupportphplib::MJTC_strtolower($product->title));

            // Check if this product already exists in Majestic Support
            $check_query = "
                SELECT product.*
                FROM `" . majesticsupport::$_db->prefix . "mjtc_support_products` AS product
                WHERE LOWER(product.product) = '".esc_sql($name) ."'
                LIMIT 1
            ";
            $ms_product = majesticsupport::$_db->get_row($check_query);

            if(!$ms_product){
                $row = MJTC_includer::MJTC_getTable('products');
                
                $MJTC_data = [
                    'id'               => '',
                    'product'         => $name,
                    'status'           => '1',
                    'ordering'         => $ordering
                ];

                $MJTC_data = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($MJTC_data);
                $row->bind($MJTC_data);

                if (!$row->store()) {
                    $this->fluent_support_import_count['product']['failed'] += 1;
                } else {
                    $this->fluent_support_product_ids[] = $product->id;
                    $this->fluent_support_import_count['product']['imported'] += 1;
                }

                $ordering++;
            } else {
                $this->fluent_support_import_count['product']['skipped'] += 1;
            }
        }
        // Save list of imported product IDs
        if (!empty($this->fluent_support_product_ids)) {
            update_option('mjtc_support_ticket_fluent_support_data_products', wp_json_encode(array_unique(array_merge($imported_products, $this->fluent_support_product_ids))));
        }
    }
    
    private function importFluentSupportPriorities() {
        // check if priority already processed for import
        $imported_priorities = array();
        $imported_priorities_json = get_option('mjtc_support_ticket_fluent_support_data_priorities');
        if(!empty($imported_priorities_json)){
            $imported_priorities = json_decode($imported_priorities_json,true);
        }
        $priorities = array('Normal' => '#00a32a', 'Medium' => '#a5b2bd', 'Critical' => '#f06060');

        if (empty($priorities)) return;

        // Get highest current ordering value
        $query = "
            SELECT MAX(priority.ordering)
            FROM `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` AS priority
        ";
        $ordering = (int) majesticsupport::$_db->get_var($query);

        foreach ($priorities as $MJTC_key => $priority) {
            // Skip if already imported
            if (in_array($priority, $imported_priorities, true)) {
                $this->fluent_support_import_count['priority']['skipped']++;
                continue;
            }
            $name = MJTC_majesticsupportphplib::MJTC_trim(MJTC_majesticsupportphplib::MJTC_strtolower($MJTC_key));

            // Check if this priority already exists in Majestic Support
            $check_query = "
                SELECT priority.*
                FROM `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` AS priority
                WHERE LOWER(priority.priority) = '" . esc_sql($name) . "'
                LIMIT 1
            ";
            $ms_priority = majesticsupport::$_db->get_row($check_query);

            if (!$ms_priority) {
                $row = MJTC_includer::MJTC_getTable('priorities');

                $MJTC_data = [
                    'id'               => '',
                    'priority'         => $name,
                    'prioritycolour'   => $priority,
                    'priorityurgency'  => '',
                    'overduetypeid'    => 1,
                    'overdueinterval'  => 7,
                    'ordering'         => $ordering,
                    'status'           => '1',
                    'isdefault'        => '0',
                    'ispublic'         => '1'
                ];

                $MJTC_data = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($MJTC_data);
                $row->bind($MJTC_data);

                if (!$row->store()) {
                    $this->fluent_support_import_count['priority']['failed'] += 1;
                } else {
                    $this->fluent_support_priority_ids[] = $priority;
                    $this->fluent_support_import_count['priority']['imported'] += 1;
                }

                $ordering++;
            } else {
                $this->fluent_support_import_count['priority']['skipped'] += 1;
            }
        }
        // Save list of imported priority IDs
        if (!empty($this->fluent_support_priority_ids)) {
            update_option('mjtc_support_ticket_fluent_support_data_priorities', wp_json_encode(array_unique(array_merge($imported_priorities, $this->fluent_support_priority_ids))));
        }
    }

    private function importFluentSupportPremades() {
        // check if premade already processed for import
        $imported_premades = array();
        $imported_premades_json = get_option('mjtc_support_ticket_fluent_support_data_premades');
        if(!empty($imported_premades_json)){
            $imported_premades = json_decode($imported_premades_json,true);
        }
        $query = "
            SELECT canned_reply.*
            FROM `" . majesticsupport::$_db->prefix . "fs_saved_replies` AS canned_reply
        ";
        $canned_replies = majesticsupport::$_db->get_results($query);

        if (empty($canned_replies)) return;

        foreach ($canned_replies as $canned_reply) {
            $title = MJTC_majesticsupportphplib::MJTC_trim(MJTC_majesticsupportphplib::MJTC_strtolower($canned_reply->title));
            // Failed if addon not installed
            if (!in_array('cannedresponses', majesticsupport::$_active_addons) ) {
                $this->fluent_support_import_count['canned response']['failed']++;
                continue;
            }
            // Skip if already imported
            if (in_array($canned_reply->id, $imported_premades, true)) {
                $this->fluent_support_import_count['canned response']['skipped']++;
                continue;
            }
            // Skip if no department id
            if (empty($MJTC_departmentid)) {
                $this->fluent_support_import_count['canned response']['skipped']++;
                continue;
            }
            // Check if this premade already exists in Majestic Support
            $check_query = "
                SELECT premade.*
                FROM `" . majesticsupport::$_db->prefix . "mjtc_support_department_message_premade` AS premade
                WHERE LOWER(premade.title) = '" . esc_sql($title) . "'
                LIMIT 1
            ";
            $ms_canned_reply = majesticsupport::$_db->get_row($check_query);

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
                    $row = MJTC_includer::MJTC_getTable('departments');

                        $updated = date_i18n('Y-m-d H:i:s');
                        $created = date_i18n('Y-m-d H:i:s');

                        $MJTC_data = [
                            'id'              => '',
                            'emailid'         => '1',
                            'departmentname'  => 'Support',
                            'ordering'        => 0,
                            'status'          => '1',
                            'isdefault'       => '0',
                            'ispublic'        => '1',
                            'updated'         => $updated,
                            'created'         => $created
                        ];

                        $MJTC_data = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($MJTC_data);
                        $row->bind($MJTC_data);

                        if ($row->store()) {
                            $MJTC_departmentid = $row->id;
                        }
                } else {
                    $MJTC_departmentid = $MJTC_department->id;
                }

                // Prepare canned response data
                $row = MJTC_includer::MJTC_getTable('cannedresponses');
                $updated = date_i18n('Y-m-d H:i:s');

                $MJTC_data = [
                    'id'          => '',
                    'departmentid'=> $MJTC_departmentid,
                    'title'       => $canned_reply->title,
                    'answer'      => $canned_reply->content,
                    'status'      => '1',
                    'updated'     => $updated,
                    'created'     => $canned_reply->created_at
                ];

                $MJTC_data = majesticsupport::MJTC_sanitizeData($MJTC_data);
                $MJTC_data['answer'] = MJTC_includer::MJTC_getModel('majesticsupport')->getSanitizedEditorData($MJTC_data['answer']);
                $MJTC_data = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($MJTC_data);

                $row->bind($MJTC_data);
                if (!$row->store()) {
                    $this->fluent_support_import_count['canned response']['failed'] += 1;
                } else {
                    $this->fluent_support_premade_ids[] = $canned_reply->id;
                    $this->fluent_support_import_count['canned response']['imported'] += 1;
                }
            } else {
                $this->fluent_support_import_count['canned response']['skipped'] += 1;
            }
        }

        // Save list of imported premade IDs
        if (!empty($this->fluent_support_premade_ids)) {
            update_option('mjtc_support_ticket_fluent_support_data_premades', wp_json_encode(array_unique(array_merge($imported_premades, $this->fluent_support_premade_ids))));
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

        $entity_counts = [];

        // Users
        if (majesticsupport::$_db->get_var("SHOW TABLES LIKE '" . majesticsupport::$_db->prefix . "fs_persons'")) {
            $query = "SELECT COUNT(*) FROM `" . majesticsupport::$_db->prefix . "fs_persons` WHERE person_type = 'customer' OR person_type = 'agent'";
            $MJTC_count = (int) majesticsupport::$_db->get_var($query);
            if ($MJTC_count > 0) $entity_counts['user'] = $MJTC_count;
        }

        // Agents
        if (majesticsupport::$_db->get_var("SHOW TABLES LIKE '" . majesticsupport::$_db->prefix . "fs_persons'")) {
            $query = "SELECT COUNT(*) FROM `" . majesticsupport::$_db->prefix . "fs_persons` AS agent
            WHERE agent.person_type = 'agent';";
            $MJTC_count = (int) majesticsupport::$_db->get_var($query);
            if ($MJTC_count > 0) $entity_counts['agent'] = $MJTC_count;
        }

        // Priorities
        $entity_counts['priority'] = 3;

        // Canned Responses
        if (majesticsupport::$_db->get_var("SHOW TABLES LIKE '" . majesticsupport::$_db->prefix . "fs_saved_replies'")) {
            $query = "SELECT COUNT(*) FROM `" . majesticsupport::$_db->prefix . "fs_saved_replies`";
            $MJTC_count = (int) majesticsupport::$_db->get_var($query);
            if ($MJTC_count > 0) $entity_counts['canned response'] = $MJTC_count;
        }

        // Products
        if (majesticsupport::$_db->get_var("SHOW TABLES LIKE '" . majesticsupport::$_db->prefix . "fs_products'")) {
            $query = "SELECT COUNT(*) FROM `" . majesticsupport::$_db->prefix . "fs_products`";
            $MJTC_count = (int) majesticsupport::$_db->get_var($query);
            if ($MJTC_count > 0) $entity_counts['product'] = $MJTC_count;
        }

        // Custom Ticket Fields
        if (majesticsupport::$_db->get_var("SHOW TABLES LIKE '" . majesticsupport::$_db->prefix . "fs_meta'")) {
            $query = "
            SELECT * FROM `" . majesticsupport::$_db->prefix . "fs_meta`
            WHERE object_type = 'option' AND `key` = '_ticket_custom_fields';";
            $custom_fields_serializeed = majesticsupport::$_db->get_row($query);
            if (!empty($custom_fields_serializeed)) {
                $custom_fields = unserialize($custom_fields_serializeed->value);
                $MJTC_count = count($custom_fields);
                if ($MJTC_count > 0) $entity_counts['field'] = $MJTC_count;
            }
        }

        // Tickets with type 'report'
        if (majesticsupport::$_db->get_var("SHOW TABLES LIKE '" . majesticsupport::$_db->prefix . "fs_tickets'")) {
            $query = "SELECT COUNT(DISTINCT tickets.id)
                FROM `" . majesticsupport::$_db->prefix . "fs_tickets` AS tickets";
            $MJTC_count = (int) majesticsupport::$_db->get_var($query);
            if ($MJTC_count > 0) $entity_counts['ticket'] = $MJTC_count;
        }

        majesticsupport::$_data['entity_counts'] = $entity_counts;
    }
}

?>
