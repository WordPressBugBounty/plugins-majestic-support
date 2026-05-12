<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_emailModel {
    /*
      $mailfor
      For which purpose you want to send mail
      1 => Ticket

      $MJTC_action
      For which action of $mailfor you want to send the mail
      1 => New Ticket Create
      2 => Close Ticket
      3 => Delete Ticket
      4 => Reply Ticket (Admin/Staff Member)
      5 => Reply Ticket (Ticket member)
      6 => Lock Ticket

      $MJTC_id
      id required when recever emailaddress is stored in record
     */

    function sendMail($mailfor, $MJTC_action, $MJTC_id = null, $MJTC_tablename = null) {
        if (!is_numeric($mailfor))
            return false;
        if (!is_numeric($MJTC_action))
            return false;
        if ($MJTC_id != null)
            if (!is_numeric($MJTC_id))
                return false;
        $MJTC_pageid = majesticsupport::getPageid();
		$MJTC_adminEmailid = majesticsupport::$_config['default_admin_email'];
		$MJTC_adminEmail = $this->getEmailById($MJTC_adminEmailid);
		
        switch ($mailfor) {
            case 1: // Mail For Tickets
                switch ($MJTC_action) {
                    case 1: // New Ticket Created
                        $MJTC_ticketRecord = $this->getRecordByTablenameAndId('mjtc_support_tickets', $MJTC_id);
                        if (isset($MJTC_ticketRecord->name) && isset($MJTC_ticketRecord->subject) && isset($MJTC_ticketRecord->ticketid) && isset($MJTC_ticketRecord->email)) {
                        $Username = $MJTC_ticketRecord->name;
                        $Subject = $MJTC_ticketRecord->subject;
                        $TrackingId = $MJTC_ticketRecord->ticketid;
                        $Email = $MJTC_ticketRecord->email;
                        $DepName = $MJTC_ticketRecord->departmentname;
                        if(in_array('helptopic', majesticsupport::$_active_addons)){
                            $HelptopicName = $MJTC_ticketRecord->topic;
                        }else{
                            $HelptopicName = '';
                        }
                        $Message = $MJTC_ticketRecord->message;
                        $matcharray = array(
                            '{SITETITLE}' => majesticsupport::$_config['title'],
                            '{USERNAME}' => $Username,
                            '{SUBJECT}' => $Subject,
                            '{TRACKINGID}' => $TrackingId,
                            '{HELP_TOPIC}' => $HelptopicName,
                            '{EMAIL}' => $Email,
                            '{MESSAGE}' => $Message,
                            '{DEPARTMENT}' => majesticsupport::MJTC_getVarValue($MJTC_ticketRecord->departmentname),
                            '{PRIORITY}' => majesticsupport::MJTC_getVarValue($MJTC_ticketRecord->priority),
                            '{CURRENT_YEAR}' => gmdate('Y')
                        );

                        // code for handling custom fields start
                        $MJTC_fvalue = '';
                        if(!empty($MJTC_ticketRecord->params)){
                            $MJTC_data = json_decode($MJTC_ticketRecord->params,true);
                        }
                        $MJTC_fields = MJTC_includer::MJTC_getModel('fieldordering')->getUserfieldsfor(1);
                        if( isset($MJTC_data) && is_array($MJTC_data)){
                            foreach ($MJTC_fields as $MJTC_field) {
                                if($MJTC_field->userfieldtype != 'file'){
                                    $MJTC_fvalue = '';
                                    if(array_key_exists($MJTC_field->field, $MJTC_data)){
                                        $MJTC_fvalue = $MJTC_data[$MJTC_field->field];
                                    }
                                    $matcharray['{'.esc_attr($MJTC_field->field).'}'] = $MJTC_fvalue;// match array new index for custom field
                                }
                            }
                        }
                        // code for handling custom fields end
                        $MJTC_object = $this->getSenderEmailAndName($MJTC_id);
                        $MJTC_senderEmail = $MJTC_object->email;
                        $MJTC_senderName = $MJTC_object->name;

                        // New ticket mail to admin
                        if(majesticsupport::$_config['new_ticket_mail_to_admin'] == 1) {
                            $MJTC_adminEmailid = majesticsupport::$_config['default_admin_email'];
                            $MJTC_adminEmail = $this->getEmailById($MJTC_adminEmailid);
                            $template = apply_filters( 'MJTC_get_email_template_by_user_defined_language','','ticket-new-admin' , $MJTC_adminEmail ,'', $MJTC_ticketRecord->multiformid);
                            if($template == '' && empty($template)){
                                $template = $this->getTemplateForEmail('ticket-new-admin', $MJTC_ticketRecord->multiformid);
                            }
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $MJTC_link = admin_url("admin.php?page=majesticsupport_ticket&mjslay=ticketdetail&majesticsupportid=" . esc_attr($MJTC_id));
                            $matcharray['{TICKETURL}'] = $MJTC_link;
                            $msgBody .= '<input type="hidden" name="ticketid:' . esc_attr($TrackingId) . '###admin####" />';
                            $msgBody .= '<span style="display:none;" ticketid:' . esc_attr($TrackingId) . '###admin#### ></span>';
                            $MJTC_attachments = '';
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);
                            $this->sendEmail($MJTC_adminEmail, $msgSubject, $msgBody, $MJTC_senderEmail, $MJTC_senderName, $MJTC_attachments, $MJTC_action, 'ticket-new-admin');
                        }
                        //Check to send email to department
                        $MJTC_query = "SELECT dept.sendmail, email.email AS emailaddress
                                    FROM `".majesticsupport::$_db->prefix."mjtc_support_tickets` AS ticket
                                    LEFT JOIN `".majesticsupport::$_db->prefix."mjtc_support_departments` AS dept ON dept.id = ticket.departmentid
                                    LEFT JOIN `".majesticsupport::$_db->prefix."mjtc_support_email` AS email ON email.id = dept.emailid
                                    WHERE ticket.id = ".esc_sql($MJTC_id);
                        $MJTC_dept_result = majesticsupport::$_db->get_row($MJTC_query);
                        if($MJTC_dept_result){
                            if(isset($MJTC_dept_result->sendmail) && $MJTC_dept_result->sendmail == 1){
                                $MJTC_deptemail = $MJTC_dept_result->emailaddress;
                                $template = apply_filters( 'MJTC_get_email_template_by_user_defined_language','','ticket-new-admin' , $MJTC_deptemail ,'', $MJTC_ticketRecord->multiformid);
                                if($template == '' && empty($template)){
                                    $template = $this->getTemplateForEmail('ticket-new-admin', $MJTC_ticketRecord->multiformid);
                                }

                                $msgSubject = $template->subject;
                                $msgBody = $template->body;

                                $MJTC_link = admin_url("admin.php?page=majesticsupport_ticket&mjslay=ticketdetail&majesticsupportid=" . esc_attr($MJTC_id));
                                $matcharray['{TICKETURL}'] = $MJTC_link;
                                $msgBody .= '<input type="hidden" name="ticketid:' . esc_attr($TrackingId) . '###admin####" />';
                                $msgBody .= '<span style="display:none;" ticketid:' . esc_attr($TrackingId) . '###admin#### ></span>';
                                $MJTC_attachments = '';
                                $this->replaceMatches($msgSubject, $matcharray);
                                $this->replaceMatches($msgBody, $matcharray);
                                $this->sendEmail($MJTC_deptemail, $msgSubject, $msgBody, $MJTC_senderEmail, $MJTC_senderName, $MJTC_attachments, $MJTC_action, 'ticket-new-admin');
                            }
                        }
                        // New ticket mail to User
                        $template = apply_filters( 'MJTC_get_email_template_by_user_defined_language','','ticket-new' , $MJTC_ticketRecord->email , $MJTC_ticketRecord->uid, $MJTC_ticketRecord->multiformid);
                        if($template == '' && empty($template)){
                            $template = $this->getTemplateForEmail('ticket-new', $MJTC_ticketRecord->multiformid);
                        }
                        //Parsing template
                        $msgSubject = $template->subject;
                        $msgBody = $template->body;
                        //token encrption
                        $MJTC_tokenarray['emailaddress']=$Email;
                        $MJTC_tokenarray['trackingid']=$TrackingId;
                        $MJTC_tokenarray['sitelink']=MJTC_includer::MJTC_getModel('majesticsupport')->getEncriptedSiteLink();
                        $MJTC_token = wp_json_encode($MJTC_tokenarray);
                        include_once MJTC_PLUGIN_PATH . 'includes/encoder.php';
                        $MJTC_encoder = new MJTC_encoder();
                        $MJTC_encryptedtext = $MJTC_encoder->MJTC_encrypt($MJTC_token);
                        // end token encryotion
                        $MJTC_link = esc_url(majesticsupport::makeUrl(array('mjsmod'=>'ticket', 'task'=>'showticketstatus','action'=>'mstask','token'=>$MJTC_encryptedtext,'mspageid'=>majesticsupport::getPageid())));
                        $matcharray['{TICKETURL}'] = $MJTC_link;
                        $this->replaceMatches($msgSubject, $matcharray);
                        $this->replaceMatches($msgBody, $matcharray);
                        $msgBody .= '<input type="hidden" name="ticketid:' . esc_attr($TrackingId) . '###user####" />';
                        $msgBody .= '<span style="display:none;" ticketid:' . esc_attr($TrackingId) . '###user#### ></span>';
                        $MJTC_attachments = '';
                        $this->sendEmail($Email, $msgSubject, $msgBody, $MJTC_senderEmail, $MJTC_senderName, $MJTC_attachments, $MJTC_action);

                        //New ticket mail to staff member
                        if ( in_array('agent',majesticsupport::$_active_addons) && majesticsupport::$_config['new_ticket_mail_to_staff_members'] == 1) {
                            // Get All Staff member of the department of Current Ticket
                            if ( in_array('agentautoassign',majesticsupport::$_active_addons) && isset(majesticsupport::$_config['department_email_on_ticket_create']) && majesticsupport::$_config['department_email_on_ticket_create'] == 2) {
                                $MJTC_agentmembers = MJTC_includer::MJTC_getModel('agentautoassign')->getAllStaffMemberByDepId($MJTC_ticketRecord->departmentid);
                            }
                            else{
                                $MJTC_agentmembers = MJTC_includer::MJTC_getModel('agent')->getAllStaffMemberByDepId($MJTC_ticketRecord->departmentid);
                            }
                            if(is_array($MJTC_agentmembers) && !empty($MJTC_agentmembers)){
                                foreach ($MJTC_agentmembers AS $MJTC_agent) {
                                    if($MJTC_agent->canemail == 1){
                                        $MJTC_staffuid = $MJTC_agent->staffuid;
                                        if (MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForAgent('New Ticket Notification', $MJTC_staffuid) == 1) {
                                            $template = apply_filters( 'MJTC_get_email_template_by_user_defined_language','','ticket-staff' , $MJTC_agent->email , $MJTC_staffuid, $MJTC_ticketRecord->multiformid);
                                            if($template == '' && empty($template)){
                                                $template = $this->getTemplateForEmail('ticket-staff', $MJTC_ticketRecord->multiformid);
                                            }

                                            $msgSubject = $template->subject;
                                            $msgBody = $template->body;
                                            $MJTC_link = esc_url(majesticsupport::makeUrl(array('mjsmod'=>'ticket', 'mjslay'=>'ticketdetail','majesticsupportid'=>$MJTC_id,'mspageid'=>majesticsupport::getPageid())));
                                            $matcharray['{TICKETURL}'] = $MJTC_link;
                                            $this->replaceMatches($msgSubject, $matcharray);
                                            $this->replaceMatches($msgBody, $matcharray);
                                            $msgBody .= '<input type="hidden" name="ticketid:' . esc_attr($TrackingId) . '###" />';
                                            $msgBody .= '<input type="hidden" name="ticketid:' . esc_attr($TrackingId) . '###staff####" />';
                                            $msgBody .= '<span style="display:none;" ticketid:' . esc_attr($TrackingId) . '###staff#### ></span>';
                                            $MJTC_attachments = '';
                                            $this->sendEmail($MJTC_agent->email, $msgSubject, $msgBody, $MJTC_senderEmail, $MJTC_senderName, $MJTC_attachments, $MJTC_action, 'ticket-staff');
                                        }
                                    }
                                }
                            }
                        }
                        }
                        break;
                    case 2: // Close Ticket
                        $MJTC_ticketRecord = $this->getRecordByTablenameAndId('mjtc_support_tickets', $MJTC_id);
                        $Username = $MJTC_ticketRecord->name;
                        $Subject = $MJTC_ticketRecord->subject;
                        $TrackingId = $MJTC_ticketRecord->ticketid;
                        $Email = $MJTC_ticketRecord->email;
                        $DepName = $MJTC_ticketRecord->departmentname;
                        if(in_array('helptopic', majesticsupport::$_active_addons)){
                            $HelptopicName = $MJTC_ticketRecord->topic;
                        }else{
                            $HelptopicName = '';
                        }
                        $Message = $MJTC_ticketRecord->message;
                        $MJTC_ticketHistory = $this->getTicketReplyHistory($MJTC_id);
                        $matcharray = array(
                            '{SITETITLE}' => majesticsupport::$_config['title'],
                            '{USERNAME}' => $Username,
                            '{SUBJECT}' => $Subject,
                            '{TRACKINGID}' => $TrackingId,
                            '{HELP_TOPIC}' => $HelptopicName,
                            '{EMAIL}' => $Email,
                            '{MESSAGE}' => $Message,
                            '{DEPARTMENT}' => majesticsupport::MJTC_getVarValue($MJTC_ticketRecord->departmentname),
                            '{PRIORITY}' => majesticsupport::MJTC_getVarValue($MJTC_ticketRecord->priority),
                            '{TICKET_HISTORY}' => $MJTC_ticketHistory,
                            '{CURRENT_YEAR}' => gmdate('Y')

                        );
                        // code for handling custom fields start
                        $MJTC_fvalue = '';
                        if(!empty($MJTC_ticketRecord->params)){
                            $MJTC_data = json_decode($MJTC_ticketRecord->params,true);
                        }
                        $MJTC_fields = MJTC_includer::MJTC_getModel('fieldordering')->getUserfieldsfor(1);
                        if( isset($MJTC_data) && is_array($MJTC_data)){
                            foreach ($MJTC_fields as $MJTC_field) {
                                if($MJTC_field->userfieldtype != 'file'){
                                    $MJTC_fvalue = '';
                                    if(array_key_exists($MJTC_field->field, $MJTC_data)){
                                        $MJTC_fvalue = $MJTC_data[$MJTC_field->field];
                                    }
                                    $matcharray['{'.esc_attr($MJTC_field->field).'}'] = $MJTC_fvalue;// match array new index for custom field
                                }
                            }
                        }
                        // code for handling custom fields end
                        $MJTC_object = $this->getSenderEmailAndName($MJTC_id);
                        $MJTC_senderEmail = $MJTC_object->email;
                        $MJTC_senderName = $MJTC_object->name;
                        $MJTC_defaulttemplate = $this->getTemplateForEmail('close-tk', $MJTC_ticketRecord->multiformid);
                        // Close ticket mail to admin
                        if (majesticsupport::$_config['ticket_close_admin'] == 1) {
                            $MJTC_adminEmailid = majesticsupport::$_config['default_admin_email'];
                            $MJTC_adminEmail = $this->getEmailById($MJTC_adminEmailid);

                            $template = apply_filters( 'MJTC_get_email_template_by_user_defined_language','','close-tk' , $MJTC_adminEmail ,'', $MJTC_ticketRecord->multiformid);
                            if($template == '' && empty($template)){
                                $template = $MJTC_defaulttemplate;
                            }

                            $MJTC_link = admin_url("admin.php?page=majesticsupport_ticket&mjslay=ticketdetail&majesticsupportid=" . esc_attr($MJTC_id));
                            $matcharray['{TICKETURL}'] = $MJTC_link;
                            $matcharray['{FEEDBACKURL}'] = ' ';
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $MJTC_attachments = '';
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);
                            $this->sendEmail($MJTC_adminEmail, $msgSubject, $msgBody, $MJTC_senderEmail, $MJTC_senderName, $MJTC_attachments, $MJTC_action, 'close-tk-admin');
                        }
                        // Close ticket mail to staff member
                        if ( in_array('agent',majesticsupport::$_active_addons) && majesticsupport::$_config['ticket_close_staff'] == 1) {
                            $MJTC_agentEmail = $this->getStaffEmailAddressByStaffId($MJTC_ticketRecord->staffid);
                            $MJTC_staffuid = $this->getStaffUidByStaffId($MJTC_ticketRecord->staffid);
                            if (MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForAgent('Mail To Agent', $MJTC_staffuid) == 1) {
                                $template = apply_filters( 'MJTC_get_email_template_by_user_defined_language','','close-tk' , $MJTC_agentEmail ,$MJTC_staffuid, $MJTC_ticketRecord->multiformid);
                                if($template == '' && empty($template)){
                                    $template = $MJTC_defaulttemplate;
                                }

                                $MJTC_link = esc_url(majesticsupport::makeUrl(array('mjsmod'=>'ticket', 'mjslay'=>'ticketdetail','majesticsupportid'=>$MJTC_id,'mspageid'=>majesticsupport::getPageid())));
                                $matcharray['{TICKETURL}'] = $MJTC_link;
                                $matcharray['{FEEDBACKURL}'] = ' ';
                                $msgSubject = $template->subject;
                                $msgBody = $template->body;
                                $MJTC_attachments = '';
                                $this->replaceMatches($msgSubject, $matcharray);
                                $this->replaceMatches($msgBody, $matcharray);
                                $this->sendEmail($MJTC_agentEmail, $msgSubject, $msgBody, $MJTC_senderEmail, $MJTC_senderName, $MJTC_attachments, $MJTC_action, 'close-tk-staff');
                            }
                        }
                        // New ticket mail to User
                        if (majesticsupport::$_config['ticket_close_user'] == 1) {
                            $MJTC_link = esc_url(majesticsupport::makeUrl(array('mjsmod'=>'ticket', 'mjslay'=>'ticketdetail','majesticsupportid'=>$MJTC_id,'mspageid'=>majesticsupport::getPageid())));
                            $MJTC_tokenarray['emailaddress']=$Email;
                            $MJTC_tokenarray['trackingid']=$TrackingId;
                            $MJTC_token = wp_json_encode($MJTC_tokenarray);
                            include_once MJTC_PLUGIN_PATH . 'includes/encoder.php';
                            $MJTC_encoder = new MJTC_encoder();
                            $MJTC_encryptedtext = $MJTC_encoder->MJTC_encrypt($MJTC_token);
                            if(in_array('feedback', majesticsupport::$_active_addons)){
                                $MJTC_flink = "<a href=" . esc_url(majesticsupport::makeUrl(array('mjsmod'=>'feedback', 'task'=>'showfeedbackform','action'=>'mstask','token'=>$MJTC_encryptedtext,'mspageid'=>majesticsupport::getPageid()))) . ">". esc_html(__('Click here to give us feedback','majestic-support'))." </a>";
                            }else{
                                $MJTC_flink = " ";
                            }

                            $template = apply_filters( 'MJTC_get_email_template_by_user_defined_language','','close-tk' , $Email ,$MJTC_ticketRecord->uid, $MJTC_ticketRecord->multiformid);
                            if($template == '' && empty($template)){
                                $template = $MJTC_defaulttemplate;
                            }

                            $matcharray['{TICKETURL}'] = $MJTC_link;
                            $matcharray['{FEEDBACKURL}'] = $MJTC_flink;
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $MJTC_attachments = '';
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);
                            $this->sendEmail($Email, $msgSubject, $msgBody, $MJTC_senderEmail, $MJTC_senderName, $MJTC_attachments, $MJTC_action);
                        }
                        break;
                    case 3: // Delete Ticket
                        $TrackingId = majesticsupport::$_data['ticketid'];
                        $Email = majesticsupport::$_data['ticketemail'];
                        $Subject = majesticsupport::$_data['ticketsubject'];
                        $matcharray = array(
                            '{SITETITLE}' => majesticsupport::$_config['title'],
                            '{TRACKINGID}' => $TrackingId,
                            '{SUBJECT}' => $Subject,
                            '{EMAIL}' => $Email,
                            '{CURRENT_YEAR}' => gmdate('Y')
                        );
                        $MJTC_object = $this->getSenderEmailAndName(null);
                        $MJTC_senderEmail = $MJTC_object->email;
                        $MJTC_senderName = $MJTC_object->name;
                        $MJTC_defaulttemplate = $this->getTemplateForEmail('delete-tk');
                        // Delete ticket mail to admin
                        if (majesticsupport::$_config['ticket_delete_admin'] == 1) {
                            $MJTC_adminEmailid = majesticsupport::$_config['default_admin_email'];
                            $MJTC_adminEmail = $this->getEmailById($MJTC_adminEmailid);
                            $matcharray['{EMAIL}'] = $MJTC_adminEmail;

                            $template = apply_filters( 'MJTC_get_email_template_by_user_defined_language','','delete-tk' , $MJTC_adminEmail ,'');
                            if($template == '' && empty($template)){
                                $template = $MJTC_defaulttemplate;
                            }
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $MJTC_attachments = '';
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);
                            $this->sendEmail($MJTC_adminEmail, $msgSubject, $msgBody, $MJTC_senderEmail, $MJTC_senderName, $MJTC_attachments, $MJTC_action, 'delete-tk-admin');
                        }
                        // Delete ticket mail to staff
                        if ( in_array('agent',majesticsupport::$_active_addons) && majesticsupport::$_config['ticket_delete_staff'] == 1) {
                            $MJTC_agent_id = majesticsupport::$_data['staffid'];
                            $MJTC_agentEmail = $this->getStaffEmailAddressByStaffId($MJTC_agent_id);
                            $matcharray['{EMAIL}'] = $MJTC_agentEmail;
                            if( ! empty($MJTC_agentEmail)){
                                $MJTC_staffuid = $this->getStaffUidByStaffId(majesticsupport::$_data['staffid']);
                                if (MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForAgent('Mail To Agent', $MJTC_staffuid) == 1) {
                                    $template = apply_filters( 'MJTC_get_email_template_by_user_defined_language','','delete-tk' , $MJTC_agentEmail ,$MJTC_staffuid);
                                    if($template == '' && empty($template)){
                                        $template = $MJTC_defaulttemplate;
                                    }
                                    $msgSubject = $template->subject;
                                    $msgBody = $template->body;
                                    $MJTC_attachments = '';
                                    $this->replaceMatches($msgSubject, $matcharray);
                                    $this->replaceMatches($msgBody, $matcharray);
                                    $this->sendEmail($MJTC_agentEmail, $msgSubject, $msgBody, $MJTC_senderEmail, $MJTC_senderName, $MJTC_attachments, $MJTC_action, 'delete-tk-staff');
                                }
                            }
                        }
                        // New ticket mail to User
                        if (majesticsupport::$_config['ticket_delete_user'] == 1) {
                            $template = apply_filters( 'MJTC_get_email_template_by_user_defined_language','','delete-tk' , $Email , '');
                            if($template == '' && empty($template)){
                                $template = $MJTC_defaulttemplate;
                            }
                            $matcharray['{EMAIL}'] = $Email;
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $MJTC_attachments = '';
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);
                            $this->sendEmail($Email, $msgSubject, $msgBody, $MJTC_senderEmail, $MJTC_senderName, $MJTC_attachments, $MJTC_action);
                        }
                        break;
                    case 4: // Reply Ticket (Admin/Staff Member)
                        $MJTC_ticketRecord = $this->getRecordByTablenameAndId('mjtc_support_tickets', $MJTC_id);
                        $Username = $MJTC_ticketRecord->name;
                        $Subject = $MJTC_ticketRecord->subject;
                        $TrackingId = $MJTC_ticketRecord->ticketid;
                        $DepName = $MJTC_ticketRecord->departmentname;
                        if(in_array('helptopic', majesticsupport::$_active_addons)){
                            $HelptopicName = $MJTC_ticketRecord->topic;
                        }else{
                            $HelptopicName = '';
                        }
                        $Email = $MJTC_ticketRecord->email;
                        $Message = $this->getLatestReplyByTicketId($MJTC_id);
                        $MJTC_ticketHistory = $this->getTicketReplyHistory($MJTC_id);
                        $matcharray = array(
                            '{SITETITLE}' => majesticsupport::$_config['title'],
                            '{USERNAME}' => $Username,
                            '{SUBJECT}' => $Subject,
                            '{TRACKINGID}' => $TrackingId,
                            '{HELP_TOPIC}' => $HelptopicName,
                            '{EMAIL}' => $Email,
                            '{MESSAGE}' => $Message,
                            '{DEPARTMENT}' => majesticsupport::MJTC_getVarValue($MJTC_ticketRecord->departmentname),
                            '{PRIORITY}' => majesticsupport::MJTC_getVarValue($MJTC_ticketRecord->priority),
                            '{TICKET_HISTORY}' => $MJTC_ticketHistory,
                            '{CURRENT_YEAR}' => gmdate('Y')
                        );
                        // code for handling custom fields start
                        $MJTC_fvalue = '';
                        if(!empty($MJTC_ticketRecord->params)){
                            $MJTC_data = json_decode($MJTC_ticketRecord->params,true);
                        }
                        $MJTC_fields = MJTC_includer::MJTC_getModel('fieldordering')->getUserfieldsfor(1);
                        if( isset($MJTC_data) && is_array($MJTC_data)){
                            foreach ($MJTC_fields as $MJTC_field) {
                                if($MJTC_field->userfieldtype != 'file'){
                                    $MJTC_fvalue = '';
                                    if(array_key_exists($MJTC_field->field, $MJTC_data)){
                                        $MJTC_fvalue = $MJTC_data[$MJTC_field->field];
                                    }
                                    $matcharray['{'.esc_attr($MJTC_field->field).'}'] = $MJTC_fvalue;// match array new index for custom field
                                }
                            }
                        }
                        // code for handling custom fields end
                        $MJTC_object = $this->getSenderEmailAndName($MJTC_id);
                        $MJTC_senderEmail = $MJTC_object->email;
                        $MJTC_senderName = $MJTC_object->name;
                        $MJTC_defaulttemplate = $this->getTemplateForEmail('reply-tk');
                        // Reply ticket mail to admin
                        if (majesticsupport::$_config['ticket_response_to_staff_admin'] == 1) {
                            $MJTC_adminEmailid = majesticsupport::$_config['default_admin_email'];
                            $MJTC_adminEmail = $this->getEmailById($MJTC_adminEmailid);
                            $MJTC_link = admin_url("admin.php?page=majesticsupport_ticket&mjslay=ticketdetail&majesticsupportid=" . esc_attr($MJTC_id));
                            $matcharray['{TICKETURL}'] = $MJTC_link;

                            $template = apply_filters( 'MJTC_get_email_template_by_user_defined_language','','reply-tk' , $MJTC_adminEmail , '', $MJTC_ticketRecord->multiformid);
                            if($template == '' && empty($template)){
                                $template = $MJTC_defaulttemplate;
                            }

                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);
                            $msgBody .= '<input type="hidden" name="ticketid:' . esc_attr($TrackingId) . '###admin####" />';
                            $MJTC_attachments = '';
                            $this->sendEmail($MJTC_adminEmail, $msgSubject, $msgBody, $MJTC_senderEmail, $MJTC_senderName, $MJTC_attachments, $MJTC_action, 'reply-tk-admin');
                        }
                        // Reply ticket mail to staff
                        if ( in_array('agent',majesticsupport::$_active_addons) && majesticsupport::$_config['ticket_response_to_staff_staff'] == 1) {
                            $MJTC_agentEmail = $this->getStaffEmailAddressByStaffId($MJTC_ticketRecord->staffid);
                            $MJTC_link = esc_url(majesticsupport::makeUrl(array('mjsmod'=>'ticket', 'mjslay'=>'ticketdetail','majesticsupportid'=>$MJTC_id,'mspageid'=>majesticsupport::getPageid())));
                            $MJTC_staffuid = $this->getStaffUidByStaffId($MJTC_ticketRecord->staffid);
                            if (MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForAgent('Mail To Agent', $MJTC_staffuid) == 1) {
                                $template = apply_filters( 'MJTC_get_email_template_by_user_defined_language','','reply-tk' , $MJTC_agentEmail , $MJTC_staffuid, $MJTC_ticketRecord->multiformid);
                                if($template == '' && empty($template)){
                                    $template = $MJTC_defaulttemplate;
                                }

                                $matcharray['{TICKETURL}'] = $MJTC_link;
                                $msgSubject = $template->subject;
                                $msgBody = $template->body;
                                $this->replaceMatches($msgSubject, $matcharray);
                                $this->replaceMatches($msgBody, $matcharray);
                                $msgBody .= '<input type="hidden" name="ticketid:' . esc_attr($TrackingId) . '###staff####" />';
                                $MJTC_attachments = '';
                                $this->sendEmail($MJTC_agentEmail, $msgSubject, $msgBody, $MJTC_senderEmail, $MJTC_senderName, $MJTC_attachments, $MJTC_action, 'reply-tk-staff');
                            }
                        }
                        // New ticket mail to User
                        $template = $this->getTemplateForEmail('responce-tk');
                        if (majesticsupport::$_config['ticket_response_to_staff_user'] == 1) {
                            //token encrption
                            $MJTC_tokenarray['emailaddress']=$Email;
                            $MJTC_tokenarray['trackingid']=$TrackingId;
                            $MJTC_tokenarray['sitelink']=MJTC_includer::MJTC_getModel('majesticsupport')->getEncriptedSiteLink();
                            $MJTC_token = wp_json_encode($MJTC_tokenarray);
                            include_once MJTC_PLUGIN_PATH . 'includes/encoder.php';
                            $MJTC_encoder = new MJTC_encoder();
                            $MJTC_encryptedtext = $MJTC_encoder->MJTC_encrypt($MJTC_token);
                            // end token encryotion
                            $MJTC_link = esc_url(majesticsupport::makeUrl(array('mjsmod'=>'ticket', 'task'=>'showticketstatus','action'=>'mstask','token'=>$MJTC_encryptedtext,'mspageid'=>majesticsupport::getPageid())));
                            $template = apply_filters( 'MJTC_get_email_template_by_user_defined_language','','reply-tk' , $Email , $MJTC_ticketRecord->uid, $MJTC_ticketRecord->multiformid);
                            if($template == '' && empty($template)){
                                $template = $MJTC_defaulttemplate;
                            }
                            $matcharray['{TICKETURL}'] = $MJTC_link;
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);
                            $msgBody .= '<input type="hidden" name="ticketid:' . esc_attr($TrackingId) . '###user####" />';
                            $MJTC_attachments = '';
                            $this->sendEmail($Email, $msgSubject, $msgBody, $MJTC_senderEmail, $MJTC_senderName, $MJTC_attachments, $MJTC_action);
                        }
                        break;
                    case 5: // Reply Ticket (Ticket Member)
                        $MJTC_ticketRecord = $this->getRecordByTablenameAndId('mjtc_support_tickets', $MJTC_id);
                        $Username = $MJTC_ticketRecord->name;
                        $Subject = $MJTC_ticketRecord->subject;
                        $TrackingId = $MJTC_ticketRecord->ticketid;
                        $DepName = $MJTC_ticketRecord->departmentname;
                        if(in_array('helptopic', majesticsupport::$_active_addons)){
                            $HelptopicName = $MJTC_ticketRecord->topic;
                        }else{
                            $HelptopicName = '';
                        }
                        $Email = $MJTC_ticketRecord->email;
                        $Message = $this->getLatestReplyByTicketId($MJTC_id);
                        $MJTC_ticketHistory = $this->getTicketReplyHistory($MJTC_id);
                        $matcharray = array(
                            '{SITETITLE}' => majesticsupport::$_config['title'],
                            '{USERNAME}' => $Username,
                            '{SUBJECT}' => $Subject,
                            '{TRACKINGID}' => $TrackingId,
                            '{HELP_TOPIC}' => $HelptopicName,
                            '{EMAIL}' => $Email,
                            '{MESSAGE}' => $Message,
                            '{DEPARTMENT}' => majesticsupport::MJTC_getVarValue($MJTC_ticketRecord->departmentname),
                            '{PRIORITY}' => majesticsupport::MJTC_getVarValue($MJTC_ticketRecord->priority),
                            '{TICKET_HISTORY}' => $MJTC_ticketHistory,
                            '{CURRENT_YEAR}' => gmdate('Y')
                        );
                        // code for handling custom fields start
                        $MJTC_fvalue = '';
                        if(!empty($MJTC_ticketRecord->params)){
                            $MJTC_data = json_decode($MJTC_ticketRecord->params,true);
                        }
                        $MJTC_fields = MJTC_includer::MJTC_getModel('fieldordering')->getUserfieldsfor(1);
                        if( isset($MJTC_data) && is_array($MJTC_data)){
                            foreach ($MJTC_fields as $MJTC_field) {
                                if($MJTC_field->userfieldtype != 'file'){
                                    $MJTC_fvalue = '';
                                    if(array_key_exists($MJTC_field->field, $MJTC_data)){
                                        $MJTC_fvalue = $MJTC_data[$MJTC_field->field];
                                    }
                                    $matcharray['{'.esc_attr($MJTC_field->field).'}'] = $MJTC_fvalue;// match array new index for custom field
                                }
                            }
                        }
                        // code for handling custom fields end
                        $MJTC_object = $this->getSenderEmailAndName($MJTC_id);
                        $MJTC_senderEmail = $MJTC_object->email;
                        $MJTC_senderName = $MJTC_object->name;
                        $MJTC_defaulttemplate = $this->getTemplateForEmail('reply-tk');
                        // New ticket mail to admin
                        if (majesticsupport::$_config['ticket_reply_ticket_user_admin'] == 1) {
                            $MJTC_adminEmailid = majesticsupport::$_config['default_admin_email'];
                            $MJTC_adminEmail = $this->getEmailById($MJTC_adminEmailid);
                            $MJTC_link = admin_url("admin.php?page=majesticsupport_ticket&mjslay=ticketdetail&majesticsupportid=" . esc_attr($MJTC_id));
                            $matcharray['{TICKETURL}'] = $MJTC_link;

                            $template = apply_filters( 'MJTC_get_email_template_by_user_defined_language','','reply-tk' ,$MJTC_adminEmail ,'', $MJTC_ticketRecord->multiformid);
                            if($template == '' && empty($template)){
                                $template = $MJTC_defaulttemplate;
                            }
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);
                            $msgBody .= '<input type="hidden" name="ticketid:' . esc_attr($TrackingId) . '###admin####" />';
                            $MJTC_attachments = '';
                            $this->sendEmail($MJTC_adminEmail, $msgSubject, $msgBody, $MJTC_senderEmail, $MJTC_senderName, $MJTC_attachments, $MJTC_action, 'reply-tk-admin');
                        }
                        // New ticket mail to staff
                        if ( in_array('agent',majesticsupport::$_active_addons) && majesticsupport::$_config['ticket_reply_ticket_user_staff'] == 1) {
                            $MJTC_agentEmail = $this->getStaffEmailAddressByStaffId($MJTC_ticketRecord->staffid);
                            $MJTC_link = esc_url(majesticsupport::makeUrl(array('mjsmod'=>'ticket', 'mjslay'=>'ticketdetail','majesticsupportid'=>$MJTC_id,'mspageid'=>majesticsupport::getPageid())));
                            $matcharray['{TICKETURL}'] = $MJTC_link;
                            $MJTC_staffuid = $this->getStaffUidByStaffId($MJTC_ticketRecord->staffid);
                            if (isset($MJTC_staffuid) && MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForAgent('Mail To Agent', $MJTC_staffuid) == 1) {
                                $template = apply_filters( 'MJTC_get_email_template_by_user_defined_language','','reply-tk' ,$MJTC_adminEmail ,$MJTC_staffuid, $MJTC_ticketRecord->multiformid);
                                if($template == '' && empty($template)){
                                    $template = $MJTC_defaulttemplate;
                                }

                                $msgSubject = $template->subject;
                                $msgBody = $template->body;
                                $this->replaceMatches($msgSubject, $matcharray);
                                $this->replaceMatches($msgBody, $matcharray);
                                $msgBody .= '<input type="hidden" name="ticketid:' . esc_attr($TrackingId) . '###staff####" />';
                                $MJTC_attachments = '';
                                $this->sendEmail($MJTC_agentEmail, $msgSubject, $msgBody, $MJTC_senderEmail, $MJTC_senderName, $MJTC_attachments, $MJTC_action, 'reply-tk-staff');
                            }
                        }
                        // New ticket mail to User
                        if (majesticsupport::$_config['ticket_reply_ticket_user_user'] == 1) {
                            //token encrption
                            $MJTC_tokenarray['emailaddress']=$Email;
                            $MJTC_tokenarray['trackingid']=$TrackingId;
                            $MJTC_tokenarray['sitelink']=MJTC_includer::MJTC_getModel('majesticsupport')->getEncriptedSiteLink();
                            $MJTC_token = wp_json_encode($MJTC_tokenarray);
                            include_once MJTC_PLUGIN_PATH . 'includes/encoder.php';
                            $MJTC_encoder = new MJTC_encoder();
                            $MJTC_encryptedtext = $MJTC_encoder->MJTC_encrypt($MJTC_token);
                            // end token encryotion
                            $MJTC_link = esc_url(majesticsupport::makeUrl(array('mjsmod'=>'ticket' ,'task'=>'showticketstatus','action'=>'mstask','token'=>$MJTC_encryptedtext,'mspageid'=>majesticsupport::getPageid())));
                            $matcharray['{TICKETURL}'] = $MJTC_link;
                            $template = apply_filters( 'MJTC_get_email_template_by_user_defined_language','','reply-tk' ,$Email ,$MJTC_ticketRecord->uid, $MJTC_ticketRecord->multiformid);
                            if($template == '' && empty($template)){
                                $template = $MJTC_defaulttemplate;
                            }

                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);
                            $msgBody .= '<input type="hidden" name="ticketid:' . esc_attr($TrackingId) . '###user####" />';
                            $MJTC_attachments = '';
                            $this->sendEmail($Email, $msgSubject, $msgBody, $MJTC_senderEmail, $MJTC_senderName, $MJTC_attachments, $MJTC_action);
                        }
                        break;
                    case 6: // Lock Ticket
                        $MJTC_ticketRecord = $this->getRecordByTablenameAndId('mjtc_support_tickets', $MJTC_id);
                        $Username = $MJTC_ticketRecord->name;
                        $Subject = $MJTC_ticketRecord->subject;
                        $TrackingId = $MJTC_ticketRecord->ticketid;
                        $DepName = $MJTC_ticketRecord->departmentname;
                        if(in_array('helptopic', majesticsupport::$_active_addons)){
                            $HelptopicName = $MJTC_ticketRecord->topic;
                        }else{
                            $HelptopicName = '';
                        }
                        $Email = $MJTC_ticketRecord->email;
                        $MJTC_ticketHistory = $this->getTicketReplyHistory($MJTC_id);
                        $matcharray = array(
                            '{SITETITLE}' => majesticsupport::$_config['title'],
                            '{USERNAME}' => $Username,
                            '{SUBJECT}' => $Subject,
                            '{TRACKINGID}' => $TrackingId,
                            '{HELP_TOPIC}' => $HelptopicName,
                            '{EMAIL}' => $Email,
                            '{DEPARTMENT}' => majesticsupport::MJTC_getVarValue($MJTC_ticketRecord->departmentname),
                            '{PRIORITY}' => majesticsupport::MJTC_getVarValue($MJTC_ticketRecord->priority),
                            '{TICKET_HISTORY}' => $MJTC_ticketHistory,
                            '{CURRENT_YEAR}' => gmdate('Y')
                        );
                        // code for handling custom fields start
                        $MJTC_fvalue = '';
                        if(!empty($MJTC_ticketRecord->params)){
                            $MJTC_data = json_decode($MJTC_ticketRecord->params,true);
                        }
                        $MJTC_fields = MJTC_includer::MJTC_getModel('fieldordering')->getUserfieldsfor(1);
                        if( isset($MJTC_data) && is_array($MJTC_data)){
                            foreach ($MJTC_fields as $MJTC_field) {
                                if($MJTC_field->userfieldtype != 'file'){
                                    $MJTC_fvalue = '';
                                    if(array_key_exists($MJTC_field->field, $MJTC_data)){
                                        $MJTC_fvalue = $MJTC_data[$MJTC_field->field];
                                    }
                                    $matcharray['{'.esc_attr($MJTC_field->field).'}'] = $MJTC_fvalue;// match array new index for custom field
                                }
                            }
                        }
                        // code for handling custom fields end
                        $MJTC_object = $this->getSenderEmailAndName($MJTC_id);
                        $MJTC_senderEmail = $MJTC_object->email;
                        $MJTC_senderName = $MJTC_object->name;
                        $MJTC_defaulttemplate = $this->getTemplateForEmail('lock-tk', $MJTC_ticketRecord->multiformid);
                        // New ticket mail to admin
                        if (majesticsupport::$_config['ticket_lock_admin'] == 1) {
                            $MJTC_adminEmailid = majesticsupport::$_config['default_admin_email'];
                            $MJTC_adminEmail = $this->getEmailById($MJTC_adminEmailid);
                            $MJTC_link = admin_url("admin.php?page=majesticsupport_ticket&mjslay=ticketdetail&majesticsupportid=" . esc_attr($MJTC_id));
                            $matcharray['{TICKETURL}'] = $MJTC_link;
                            $template = apply_filters( 'MJTC_get_email_template_by_user_defined_language','','lock-tk' ,$MJTC_adminEmail ,'', $MJTC_ticketRecord->multiformid);
                            if($template == '' && empty($template)){
                                $template = $MJTC_defaulttemplate;
                            }
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $MJTC_attachments = '';

                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);
                            $this->sendEmail($MJTC_adminEmail, $msgSubject, $msgBody, $MJTC_senderEmail, $MJTC_senderName, $MJTC_attachments, $MJTC_action, 'lock-tk-admin');
                        }
                        // New ticket mail to staff
                        if ( in_array('agent',majesticsupport::$_active_addons) && majesticsupport::$_config['ticket_lock_staff'] == 1) {
                            $MJTC_agentEmail = $this->getStaffEmailAddressByStaffId($MJTC_ticketRecord->staffid);
                            $MJTC_link = esc_url(majesticsupport::makeUrl(array('mjsmod'=>'ticket', 'mjslay'=>'ticketdetail','majesticsupportid'=>$MJTC_id,'mspageid'=>majesticsupport::getPageid())));

                            $matcharray['{TICKETURL}'] = $MJTC_link;
                            $MJTC_staffuid = $this->getStaffUidByStaffId($MJTC_ticketRecord->staffid);
                            if (MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForAgent('Mail To Agent', $MJTC_staffuid) == 1) {
                                $template = apply_filters( 'MJTC_get_email_template_by_user_defined_language','','lock-tk' ,$MJTC_agentEmail ,$MJTC_staffuid, $MJTC_ticketRecord->multiformid);
                                if($template == '' && empty($template)){
                                    $template = $MJTC_defaulttemplate;
                                }
                                $msgSubject = $template->subject;
                                $msgBody = $template->body;
                                $MJTC_attachments = '';

                                $this->replaceMatches($msgSubject, $matcharray);
                                $this->replaceMatches($msgBody, $matcharray);
                                $this->sendEmail($MJTC_agentEmail, $msgSubject, $msgBody, $MJTC_senderEmail, $MJTC_senderName, $MJTC_attachments, $MJTC_action, 'lock-tk-staff');
                            }
                        }
                        // New ticket mail to User
                        if (majesticsupport::$_config['ticket_lock_user'] == 1) {
                            $MJTC_link = esc_url(majesticsupport::makeUrl(array('mjsmod'=>'ticket', 'mjslay'=>'ticketdetail','majesticsupportid'=>$MJTC_id,'mspageid'=>majesticsupport::getPageid())));

                            $matcharray['{TICKETURL}'] = $MJTC_link;
                            $template = apply_filters( 'MJTC_get_email_template_by_user_defined_language','','lock-tk' ,$Email ,$MJTC_ticketRecord->uid, $MJTC_ticketRecord->multiformid);
                            if($template == '' && empty($template)){
                                $template = $MJTC_defaulttemplate;
                            }
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $MJTC_attachments = '';
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);
                            $this->sendEmail($Email, $msgSubject, $msgBody, $MJTC_senderEmail, $MJTC_senderName, $MJTC_attachments, $MJTC_action);
                        }
                        break;
                    case 7: // Unlock Ticket
                        $MJTC_ticketRecord = $this->getRecordByTablenameAndId('mjtc_support_tickets', $MJTC_id);
                        $Username = $MJTC_ticketRecord->name;
                        $Subject = $MJTC_ticketRecord->subject;
                        $TrackingId = $MJTC_ticketRecord->ticketid;
                        $DepName = $MJTC_ticketRecord->departmentname;
                        if(in_array('helptopic', majesticsupport::$_active_addons)){
                            $HelptopicName = $MJTC_ticketRecord->topic;
                        }else{
                            $HelptopicName = '';
                        }
                        $Email = $MJTC_ticketRecord->email;
                        $MJTC_ticketHistory = $this->getTicketReplyHistory($MJTC_id);
                        $matcharray = array(
                            '{SITETITLE}' => majesticsupport::$_config['title'],
                            '{USERNAME}' => $Username,
                            '{SUBJECT}' => $Subject,
                            '{TRACKINGID}' => $TrackingId,
                            '{HELP_TOPIC}' => $HelptopicName,
                            '{EMAIL}' => $Email,
                            '{DEPARTMENT}' => majesticsupport::MJTC_getVarValue($MJTC_ticketRecord->departmentname),
                            '{PRIORITY}' => majesticsupport::MJTC_getVarValue($MJTC_ticketRecord->priority),
                            '{TICKET_HISTORY}' => $MJTC_ticketHistory,
                            '{CURRENT_YEAR}' => gmdate('Y')
                        );
                        // code for handling custom fields start
                        $MJTC_fvalue = '';
                        if(!empty($MJTC_ticketRecord->params)){
                            $MJTC_data = json_decode($MJTC_ticketRecord->params,true);
                        }
                        $MJTC_fields = MJTC_includer::MJTC_getModel('fieldordering')->getUserfieldsfor(1);
                        if( isset($MJTC_data) && is_array($MJTC_data)){
                            foreach ($MJTC_fields as $MJTC_field) {
                                if($MJTC_field->userfieldtype != 'file'){
                                    $MJTC_fvalue = '';
                                    if(array_key_exists($MJTC_field->field, $MJTC_data)){
                                        $MJTC_fvalue = $MJTC_data[$MJTC_field->field];
                                    }
                                    $matcharray['{'.esc_attr($MJTC_field->field).'}'] = $MJTC_fvalue;// match array new index for custom field
                                }
                            }
                        }
                        // code for handling custom fields end
                        $MJTC_object = $this->getSenderEmailAndName($MJTC_id);
                        $MJTC_senderEmail = $MJTC_object->email;
                        $MJTC_senderName = $MJTC_object->name;
                        $MJTC_defaulttemplate = $this->getTemplateForEmail('unlock-tk', $MJTC_ticketRecord->multiformid);
                        // New ticket mail to admin
                        if (majesticsupport::$_config['ticket_unlock_admin'] == 1) {
                            $MJTC_adminEmailid = majesticsupport::$_config['default_admin_email'];
                            $MJTC_adminEmail = $this->getEmailById($MJTC_adminEmailid);
                            $MJTC_link = admin_url("admin.php?page=majesticsupport_ticket&mjslay=ticketdetail&majesticsupportid=" . esc_attr($MJTC_id));

                            $matcharray['{TICKETURL}'] = $MJTC_link;
                            $template = apply_filters( 'MJTC_get_email_template_by_user_defined_language','','unlock-tk' ,$MJTC_adminEmail ,'', $MJTC_ticketRecord->multiformid);
                            if($template == '' && empty($template)){
                            $template = $MJTC_defaulttemplate;
                            }
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $MJTC_attachments = '';

                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);
                            $this->sendEmail($MJTC_adminEmail, $msgSubject, $msgBody, $MJTC_senderEmail, $MJTC_senderName, $MJTC_attachments, $MJTC_action, 'unlock-tk-admin');
                        }
                        // New ticket mail to staff
                        if ( in_array('agent',majesticsupport::$_active_addons) && majesticsupport::$_config['ticket_unlock_staff'] == 1) {
                            $MJTC_agentEmail = $this->getStaffEmailAddressByStaffId($MJTC_ticketRecord->staffid);
                            $MJTC_link = esc_url(majesticsupport::makeUrl(array('mjsmod'=>'ticket', 'mjslay'=>'ticketdetail','majesticsupportid'=>$MJTC_id,'mspageid'=>majesticsupport::getPageid())));

                            $matcharray['{TICKETURL}'] = $MJTC_link;
                            $MJTC_staffuid = $this->getStaffUidByStaffId($MJTC_ticketRecord->staffid);
                            if (MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForAgent('Mail To Agent', $MJTC_staffuid) == 1) {
                                $template = apply_filters( 'MJTC_get_email_template_by_user_defined_language','','unlock-tk' ,$MJTC_agentEmail ,$MJTC_staffuid, $MJTC_ticketRecord->multiformid);
                                if($template == '' && empty($template)){
                                $template = $MJTC_defaulttemplate;
                                }
                                $msgSubject = $template->subject;
                                $msgBody = $template->body;
                                $MJTC_attachments = '';

                                $this->replaceMatches($msgSubject, $matcharray);
                                $this->replaceMatches($msgBody, $matcharray);
                                $this->sendEmail($MJTC_agentEmail, $msgSubject, $msgBody, $MJTC_senderEmail, $MJTC_senderName, $MJTC_attachments, $MJTC_action, 'unlock-tk-staff');
                            }
                        }
                        // New ticket mail to User
                        if (majesticsupport::$_config['ticket_unlock_user'] == 1) {
                            $MJTC_link = esc_url(majesticsupport::makeUrl(array('mjsmod'=>'ticket', 'mjslay'=>'ticketdetail','majesticsupportid'=>$MJTC_id,'mspageid'=>majesticsupport::getPageid())));

                            $matcharray['{TICKETURL}'] = $MJTC_link;
                            $template = apply_filters( 'MJTC_get_email_template_by_user_defined_language','','unlock-tk' ,$Email ,$MJTC_ticketRecord->uid, $MJTC_ticketRecord->multiformid);
                            if($template == '' && empty($template)){
                                $template = $MJTC_defaulttemplate;
                            }
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $MJTC_attachments = '';

                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);
                            $this->sendEmail($Email, $msgSubject, $msgBody, $MJTC_senderEmail, $MJTC_senderName, $MJTC_attachments, $MJTC_action);
                        }
                        break;
                    case 8: // Markoverdue Ticket
                        $MJTC_ticketRecord = $this->getRecordByTablenameAndId('mjtc_support_tickets', $MJTC_id);
                        $TrackingId = $MJTC_ticketRecord->ticketid;
                        $DepName = $MJTC_ticketRecord->departmentname;
                        if(in_array('helptopic', majesticsupport::$_active_addons)){
                            $HelptopicName = $MJTC_ticketRecord->topic;
                        }else{
                            $HelptopicName = '';
                        }
                        $Email = $MJTC_ticketRecord->email;
                        $Subject = $MJTC_ticketRecord->subject;
                        $MJTC_ticketHistory = $this->getTicketReplyHistory($MJTC_id);
                        $matcharray = array(
                            '{SITETITLE}' => majesticsupport::$_config['title'],
                            '{TRACKINGID}' => $TrackingId,
                            '{HELP_TOPIC}' => $HelptopicName,
                            '{SUBJECT}' => $Subject,
                            '{DEPARTMENT}' => majesticsupport::MJTC_getVarValue($MJTC_ticketRecord->departmentname),
                            '{PRIORITY}' => majesticsupport::MJTC_getVarValue($MJTC_ticketRecord->priority),
                            '{TICKET_HISTORY}' => $MJTC_ticketHistory,
                            '{CURRENT_YEAR}' => gmdate('Y')
                        );
                        // code for handling custom fields start
                        $MJTC_fvalue = '';
                        if(!empty($MJTC_ticketRecord->params)){
                            $MJTC_data = json_decode($MJTC_ticketRecord->params,true);
                        }
                        $MJTC_fields = MJTC_includer::MJTC_getModel('fieldordering')->getUserfieldsfor(1);
                        if( isset($MJTC_data) && is_array($MJTC_data)){
                            foreach ($MJTC_fields as $MJTC_field) {
                                if($MJTC_field->userfieldtype != 'file'){
                                    $MJTC_fvalue = '';
                                    if(array_key_exists($MJTC_field->field, $MJTC_data)){
                                        $MJTC_fvalue = $MJTC_data[$MJTC_field->field];
                                    }
                                    $matcharray['{'.esc_attr($MJTC_field->field).'}'] = $MJTC_fvalue;// match array new index for custom field
                                }
                            }
                        }
                        // code for handling custom fields end
                        $MJTC_object = $this->getSenderEmailAndName($MJTC_id);
                        $MJTC_senderEmail = $MJTC_object->email;
                        $MJTC_senderName = $MJTC_object->name;
                        $MJTC_defaulttemplate = $this->getTemplateForEmail('moverdue-tk', $MJTC_ticketRecord->multiformid);
                        // New ticket mail to admin
                        if (majesticsupport::$_config['ticket_mark_overdue_admin'] == 1) {
                            $MJTC_adminEmailid = majesticsupport::$_config['default_admin_email'];
                            $MJTC_adminEmail = $this->getEmailById($MJTC_adminEmailid);
                            $matcharray['{EMAIL}'] = $MJTC_adminEmail;
                            $MJTC_link = admin_url("admin.php?page=majesticsupport_ticket&mjslay=ticketdetail&majesticsupportid=" . esc_attr($MJTC_id));

                            $matcharray['{TICKETURL}'] = $MJTC_link;
                            $template = apply_filters( 'MJTC_get_email_template_by_user_defined_language','','moverdue-tk' ,$MJTC_adminEmail ,'', $MJTC_ticketRecord->multiformid);
                            if($template == '' && empty($template)){
                                $template = $MJTC_defaulttemplate;
                            }
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $MJTC_attachments = '';
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);
                            $this->sendEmail($MJTC_adminEmail, $msgSubject, $msgBody, $MJTC_senderEmail, $MJTC_senderName, $MJTC_attachments, $MJTC_action, 'moverdue-tk-admin');
                        }
                        // New ticket mail to staff
                        if ( in_array('agent',majesticsupport::$_active_addons) && majesticsupport::$_config['ticket_mark_overdue_staff'] == 1) {
                            $MJTC_agentEmail = $this->getStaffEmailAddressByStaffId($MJTC_ticketRecord->staffid);
                            $MJTC_link = esc_url(majesticsupport::makeUrl(array('mjsmod'=>'ticket', 'mjslay'=>'ticketdetail','majesticsupportid'=>$MJTC_id,'mspageid'=>majesticsupport::getPageid())));

                            $matcharray['{TICKETURL}'] = $MJTC_link;
                            $MJTC_staffuid = $this->getStaffUidByStaffId($MJTC_ticketRecord->staffid);
                            if (MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForAgent('Mail To Agent', $MJTC_staffuid) == 1) {
                                $template = apply_filters( 'MJTC_get_email_template_by_user_defined_language','','moverdue-tk' ,$MJTC_adminEmail ,$MJTC_staffuid, $MJTC_ticketRecord->multiformid);
                                $matcharray['{EMAIL}'] = $MJTC_agentEmail;
                                if($template == '' && empty($template)){
                                    $template = $MJTC_defaulttemplate;
                                }
                                $msgSubject = $template->subject;
                                $msgBody = $template->body;
                                $MJTC_attachments = '';
                                $this->replaceMatches($msgSubject, $matcharray);
                                $this->replaceMatches($msgBody, $matcharray);
                                $this->sendEmail($MJTC_agentEmail, $msgSubject, $msgBody, $MJTC_senderEmail, $MJTC_senderName, $MJTC_attachments, $MJTC_action, 'moverdue-tk-staff');
                            }
                            // Get All Staff member of the department of Current Ticket
                            $MJTC_agentmembers = MJTC_includer::MJTC_getModel('agent')->getAllStaffMemberByDepId($MJTC_ticketRecord->departmentid);
                            if(is_array($MJTC_agentmembers) && !empty($MJTC_agentmembers)){
                                foreach ($MJTC_agentmembers AS $MJTC_agent) {
                                    if($MJTC_agent->canemail == 1){
                                        $template = apply_filters( 'MJTC_get_email_template_by_user_defined_language','','moverdue-tk' ,$MJTC_agent->email ,$MJTC_agent->staffuid, $MJTC_ticketRecord->multiformid);
                                        if($template == '' && empty($template)){
                                            $template = $MJTC_defaulttemplate;
                                        }
                                        $matcharray['{EMAIL}'] = $MJTC_agent->email;
                                        $msgSubject = $template->subject;
                                        $msgBody = $template->body;
                                        $msgBody .= '<input type="hidden" name="ticketid:' . esc_attr($TrackingId) . '###staff####" />';
                                        $msgBody .= '<span style="display:none;" ticketid:' . esc_attr($TrackingId) . '###staff#### ></span>';
                                        $MJTC_attachments = '';
                                        $this->sendEmail($MJTC_agent->email, $msgSubject, $msgBody, $MJTC_senderEmail, $MJTC_senderName, $MJTC_attachments, $MJTC_action);
                                    }
                                }
                            }
                            // send email to staff memebers with all ticket permissions
                            if( !is_numeric($MJTC_ticketRecord->staffid) && !is_numeric($MJTC_ticketRecord->departmentid)){
                                if( in_array('agent',majesticsupport::$_active_addons)){
                                    $MJTC_agentmembers = MJTC_includer::MJTC_getModel('agent')->getAllStaffMemberByAllTicketPermission();
                                    if(is_array($MJTC_agentmembers) && !empty($MJTC_agentmembers)){
                                        foreach ($MJTC_agentmembers AS $MJTC_agent) {
                                            if($MJTC_agent->canemail == 1){
                                                if (MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForAgent('Mail To Agent', $MJTC_agent->uid) == 1) {
                                                    $template = apply_filters( 'MJTC_get_email_template_by_user_defined_language','','moverdue-tk' ,$MJTC_agent->email,$MJTC_agent->uid, $MJTC_ticketRecord->multiformid);
                                                    $matcharray['{EMAIL}'] = $MJTC_agent->email;
                                                    if($template == '' && empty($template)){
                                                        $template = $MJTC_defaulttemplate;
                                                    }
                                                    $msgSubject = $template->subject;
                                                    $msgBody = $template->body;
                                                    $msgBody .= '<input type="hidden" name="ticketid:' . esc_attr($TrackingId) . '###staff####" />';
                                                    $msgBody .= '<span style="display:none;" ticketid:' . esc_attr($TrackingId) . '###staff#### ></span>';
                                                    $MJTC_attachments = '';

                                                    $this->sendEmail($MJTC_agent->email, $msgSubject, $msgBody, $MJTC_senderEmail, $MJTC_senderName, $MJTC_attachments, $MJTC_action);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        // New ticket mail to User
                        if (majesticsupport::$_config['ticket_mark_overdue_user'] == 1) {
                            $MJTC_link = esc_url(majesticsupport::makeUrl(array('mjsmod'=>'ticket', 'mjslay'=>'ticketdetail','majesticsupportid'=>$MJTC_id,'mspageid'=>majesticsupport::getPageid())));
                            $matcharray['{TICKETURL}'] = $MJTC_link;
                            $template = apply_filters( 'MJTC_get_email_template_by_user_defined_language','','moverdue-tk' ,$Email ,$MJTC_ticketRecord->uid, $MJTC_ticketRecord->multiformid);
                            if($template == '' && empty($template)){
                                $template = $MJTC_defaulttemplate;
                            }
                            $matcharray['{EMAIL}'] = $Email;
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $MJTC_attachments = '';
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);
                            $this->sendEmail($Email, $msgSubject, $msgBody, $MJTC_senderEmail, $MJTC_senderName, $MJTC_attachments, $MJTC_action);
                        }
                        break;
                    case 9: // Mark in progress Ticket
                        $MJTC_ticketRecord = $this->getRecordByTablenameAndId('mjtc_support_tickets', $MJTC_id);
                        $TrackingId = $MJTC_ticketRecord->ticketid;
                        $DepName = $MJTC_ticketRecord->departmentname;
                        if(in_array('helptopic', majesticsupport::$_active_addons)){
                            $HelptopicName = $MJTC_ticketRecord->topic;
                        }else{
                            $HelptopicName = '';
                        }
                        $Email = $MJTC_ticketRecord->email;
                        $Subject = $MJTC_ticketRecord->subject;
                        $MJTC_ticketHistory = $this->getTicketReplyHistory($MJTC_id);
                        $matcharray = array(
                            '{SITETITLE}' => majesticsupport::$_config['title'],
                            '{TRACKINGID}' => $TrackingId,
                            '{HELP_TOPIC}' => $HelptopicName,
                            '{SUBJECT}' => $Subject,
                            '{DEPARTMENT}' => majesticsupport::MJTC_getVarValue($MJTC_ticketRecord->departmentname),
                            '{PRIORITY}' => majesticsupport::MJTC_getVarValue($MJTC_ticketRecord->priority),
                            '{TICKET_HISTORY}' => $MJTC_ticketHistory,
                            '{CURRENT_YEAR}' => gmdate('Y')
                        );
                        // code for handling custom fields start
                        $MJTC_fvalue = '';
                        if(!empty($MJTC_ticketRecord->params)){
                            $MJTC_data = json_decode($MJTC_ticketRecord->params,true);
                        }
                        $MJTC_fields = MJTC_includer::MJTC_getModel('fieldordering')->getUserfieldsfor(1);
                        if( isset($MJTC_data) && is_array($MJTC_data)){
                            foreach ($MJTC_fields as $MJTC_field) {
                                if($MJTC_field->userfieldtype != 'file'){
                                    $MJTC_fvalue = '';
                                    if(array_key_exists($MJTC_field->field, $MJTC_data)){
                                        $MJTC_fvalue = $MJTC_data[$MJTC_field->field];
                                    }
                                    $matcharray['{'.esc_attr($MJTC_field->field).'}'] = $MJTC_fvalue;// match array new index for custom field
                                }
                            }
                        }
                        // code for handling custom fields end
                        $MJTC_object = $this->getSenderEmailAndName($MJTC_id);
                        $MJTC_senderEmail = $MJTC_object->email;
                        $MJTC_senderName = $MJTC_object->name;
                        $MJTC_defaulttemplate = $this->getTemplateForEmail('minprogress-tk', $MJTC_ticketRecord->multiformid);
                        // New ticket mail to admin
                        if (majesticsupport::$_config['ticket_mark_progress_admin'] == 1) {
                            $MJTC_adminEmailid = majesticsupport::$_config['default_admin_email'];
                            $MJTC_adminEmail = $this->getEmailById($MJTC_adminEmailid);
                            $matcharray['{EMAIL}'] = $MJTC_adminEmail;
                            $MJTC_link = admin_url("admin.php?page=majesticsupport_ticket&mjslay=ticketdetail&majesticsupportid=" . esc_attr($MJTC_id));

                            $matcharray['{TICKETURL}'] = $MJTC_link;

                            $template = apply_filters( 'MJTC_get_email_template_by_user_defined_language','','minprogress-tk' ,$MJTC_adminEmail ,'', $MJTC_ticketRecord->multiformid);
                            if($template == '' && empty($template)){
                                $template = $MJTC_defaulttemplate;
                            }
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $MJTC_attachments = '';
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);
                            $this->sendEmail($MJTC_adminEmail, $msgSubject, $msgBody, $MJTC_senderEmail, $MJTC_senderName, $MJTC_attachments, $MJTC_action, 'minprogress-tk-admin');
                        }
                        // New ticket mail to staff
                        if ( in_array('agent',majesticsupport::$_active_addons) && majesticsupport::$_config['ticket_mark_progress_staff'] == 1) {
                            $MJTC_agentEmail = $this->getStaffEmailAddressByStaffId($MJTC_ticketRecord->staffid);
                            $matcharray['{EMAIL}'] = $MJTC_agentEmail;
                            $MJTC_link = esc_url(majesticsupport::makeUrl(array('mjsmod'=>'ticket', 'mjslay'=>'ticketdetail','majesticsupportid'=>$MJTC_id,'mspageid'=>majesticsupport::getPageid())));

                            $matcharray['{TICKETURL}'] = $MJTC_link;
                            $MJTC_staffuid = $this->getStaffUidByStaffId($MJTC_ticketRecord->staffid);
                            if (MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForAgent('Mail To Agent', $MJTC_staffuid) == 1) {
                                $template = apply_filters( 'MJTC_get_email_template_by_user_defined_language','','minprogress-tk'
                                 ,$MJTC_agentEmail ,$MJTC_staffuid, $MJTC_ticketRecord->multiformid);
                                if($template == '' && empty($template)){
                                    $template = $MJTC_defaulttemplate;
                                }
                                $msgSubject = $template->subject;
                                $msgBody = $template->body;
                                $MJTC_attachments = '';
                                $this->replaceMatches($msgSubject, $matcharray);
                                $this->replaceMatches($msgBody, $matcharray);
                                $this->sendEmail($MJTC_agentEmail, $msgSubject, $msgBody, $MJTC_senderEmail, $MJTC_senderName, $MJTC_attachments, $MJTC_action, 'minprogress-tk-staff');
                            }
                        }
                        // New ticket mail to User
                        if (majesticsupport::$_config['ticket_mark_progress_user'] == 1) {
                            $MJTC_link = esc_url(majesticsupport::makeUrl(array('mjsmod'=>'ticket', 'mjslay'=>'ticketdetail','majesticsupportid'=>$MJTC_id,'mspageid'=>majesticsupport::getPageid())));

                            $matcharray['{TICKETURL}'] = $MJTC_link;
                            $template = apply_filters( 'MJTC_get_email_template_by_user_defined_language','','minprogress-tk' ,$Email ,$MJTC_ticketRecord->uid, $MJTC_ticketRecord->multiformid);
                            if($template == '' && empty($template)){
                                $template = $MJTC_defaulttemplate;
                            }
                            $matcharray['{EMAIL}'] = $Email;
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $MJTC_attachments = '';
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);
                            $this->sendEmail($Email, $msgSubject, $msgBody, $MJTC_senderEmail, $MJTC_senderName, $MJTC_attachments, $MJTC_action);
                        }
                        break;
                    case 10: // Ban email and close Ticket
                        $MJTC_ticketRecord = $this->getRecordByTablenameAndId('mjtc_support_tickets', $MJTC_id);
                        $TrackingId = $MJTC_ticketRecord->ticketid;
                        $DepName = $MJTC_ticketRecord->departmentname;
                        if(in_array('helptopic', majesticsupport::$_active_addons)){
                            $HelptopicName = $MJTC_ticketRecord->topic;
                        }else{
                            $HelptopicName = '';
                        }
                        $Email = $MJTC_ticketRecord->email;
                        $Subject = $MJTC_ticketRecord->subject;
                        $matcharray = array(
                            '{SITETITLE}' => majesticsupport::$_config['title'],
                            '{EMAIL_ADDRESS}' => $Email,
                            '{SUBJECT}' => $Subject,
                            '{HELP_TOPIC}' => $HelptopicName,
                            '{TRACKINGID}' => $TrackingId,
                            '{DEPARTMENT}' => majesticsupport::MJTC_getVarValue($MJTC_ticketRecord->departmentname),
                            '{PRIORITY}' => majesticsupport::MJTC_getVarValue($MJTC_ticketRecord->priority),
                            '{CURRENT_YEAR}' => gmdate('Y')
                        );
                        // code for handling custom fields start
                        $MJTC_fvalue = '';
                        if(!empty($MJTC_ticketRecord->params)){
                            $MJTC_data = json_decode($MJTC_ticketRecord->params,true);
                        }
                        $MJTC_fields = MJTC_includer::MJTC_getModel('fieldordering')->getUserfieldsfor(1);
                        if( isset($MJTC_data) && is_array($MJTC_data)){
                            foreach ($MJTC_fields as $MJTC_field) {
                                if($MJTC_field->userfieldtype != 'file'){
                                    $MJTC_fvalue = '';
                                    if(array_key_exists($MJTC_field->field, $MJTC_data)){
                                        $MJTC_fvalue = $MJTC_data[$MJTC_field->field];
                                    }
                                    $matcharray['{'.esc_attr($MJTC_field->field).'}'] = $MJTC_fvalue;// match array new index for custom field
                                }
                            }
                        }
                        // code for handling custom fields end
                        $MJTC_object = $this->getSenderEmailAndName($MJTC_id);
                        $MJTC_senderEmail = $MJTC_object->email;
                        $MJTC_senderName = $MJTC_object->name;
                        $MJTC_defaulttemplate = $this->getTemplateForEmail('banemailcloseticket-tk', $MJTC_ticketRecord->multiformid);

                        // New ticket mail to admin
                        if (majesticsupport::$_config['ticker_ban_eamil_and_close_ticktet_admin'] == 1) {
                            $MJTC_adminEmailid = majesticsupport::$_config['default_admin_email'];
                            $MJTC_adminEmail = $this->getEmailById($MJTC_adminEmailid);
                            $template = apply_filters( 'MJTC_get_email_template_by_user_defined_language','','banemailcloseticket-tk' ,$MJTC_adminEmail ,'', $MJTC_ticketRecord->multiformid);
                            if($template == '' && empty($template)){
                                $template = $MJTC_defaulttemplate;
                            }
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $MJTC_attachments = '';
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);
                            $this->sendEmail($MJTC_adminEmail, $msgSubject, $msgBody, $MJTC_senderEmail, $MJTC_senderName, $MJTC_attachments, $MJTC_action, 'banemailcloseticket-tk-admin');
                        }
                        // New ticket mail to staff
                        if ( in_array('agent',majesticsupport::$_active_addons) && majesticsupport::$_config['ticker_ban_eamil_and_close_ticktet_staff'] == 1) {
                            $MJTC_agentEmail = $this->getStaffEmailAddressByStaffId($MJTC_ticketRecord->staffid);
                            $MJTC_staffuid = $this->getStaffUidByStaffId($MJTC_ticketRecord->staffid);
                            if (MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForAgent('Mail To Agent', $MJTC_staffuid) == 1) {
                                $template = apply_filters( 'MJTC_get_email_template_by_user_defined_language','','banemailcloseticket-tk' ,$MJTC_adminEmail ,$MJTC_staffuid, $MJTC_ticketRecord->multiformid);
                                if($template == '' && empty($template)){
                                    $template = $MJTC_defaulttemplate;
                                }
                                $msgSubject = $template->subject;
                                $msgBody = $template->body;
                                $MJTC_attachments = '';

                                $this->replaceMatches($msgSubject, $matcharray);
                                $this->replaceMatches($msgBody, $matcharray);
                                $this->sendEmail($MJTC_agentEmail, $msgSubject, $msgBody, $MJTC_senderEmail, $MJTC_senderName, $MJTC_attachments, $MJTC_action, 'banemailcloseticket-tk-staff');
                            }
                        }
                        // New ticket mail to User
                        if (majesticsupport::$_config['ticker_ban_eamil_and_close_ticktet_user'] == 1) {
                            $template = apply_filters( 'MJTC_get_email_template_by_user_defined_language','','banemailcloseticket-tk' ,$Email ,$MJTC_ticketRecord->uid, $MJTC_ticketRecord->multiformid);
                            if($template == '' && empty($template)){
                                $template = $MJTC_defaulttemplate;
                            }
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $MJTC_attachments = '';
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);
                            $this->sendEmail($Email, $msgSubject, $msgBody, $MJTC_senderEmail, $MJTC_senderName, $MJTC_attachments, $MJTC_action);
                        }
                        break;
                    case 11: // Priority change ticket
                        $MJTC_ticketRecord = $this->getRecordByTablenameAndId('mjtc_support_tickets', $MJTC_id);
                        $TrackingId = $MJTC_ticketRecord->ticketid;
                        $Subject = $MJTC_ticketRecord->subject;
                        $DepName = $MJTC_ticketRecord->departmentname;
                        if(in_array('helptopic', majesticsupport::$_active_addons)){
                            $HelptopicName = $MJTC_ticketRecord->topic;
                        }else{
                            $HelptopicName = '';
                        }
                        $Email = $MJTC_ticketRecord->email;
                        $Priority = MJTC_includer::MJTC_getModel('priority')->getPriorityById($MJTC_ticketRecord->priorityid);
                        $MJTC_ticketHistory = $this->getTicketReplyHistory($MJTC_id);
                        $matcharray = array(
                            '{SITETITLE}' => majesticsupport::$_config['title'],
                            '{PRIORITY_TITLE}' => majesticsupport::MJTC_getVarValue($Priority),
                            '{SUBJECT}' => $Subject,
                            '{HELP_TOPIC}' => $HelptopicName,
                            '{TRACKINGID}' => $TrackingId,
                            '{DEPARTMENT}' => majesticsupport::MJTC_getVarValue($MJTC_ticketRecord->departmentname),
                            '{TICKET_HISTORY}' => $MJTC_ticketHistory,
                            '{CURRENT_YEAR}' => gmdate('Y')
                        );
                        // code for handling custom fields start
                        $MJTC_fvalue = '';
                        if(!empty($MJTC_ticketRecord->params)){
                            $MJTC_data = json_decode($MJTC_ticketRecord->params,true);
                        }
                        $MJTC_fields = MJTC_includer::MJTC_getModel('fieldordering')->getUserfieldsfor(1);
                        if( isset($MJTC_data) && is_array($MJTC_data)){
                            foreach ($MJTC_fields as $MJTC_field) {
                                if($MJTC_field->userfieldtype != 'file'){
                                    $MJTC_fvalue = '';
                                    if(array_key_exists($MJTC_field->field, $MJTC_data)){
                                        $MJTC_fvalue = $MJTC_data[$MJTC_field->field];
                                    }
                                    $matcharray['{'.esc_attr($MJTC_field->field).'}'] = $MJTC_fvalue;// match array new index for custom field
                                }
                            }
                        }
                        // code for handling custom fields end
                        $MJTC_object = $this->getSenderEmailAndName($MJTC_id);
                        $MJTC_senderEmail = $MJTC_object->email;
                        $MJTC_senderName = $MJTC_object->name;
                        $MJTC_defaulttemplate = $this->getTemplateForEmail('prtrans-tk', $MJTC_ticketRecord->multiformid);

                        // New ticket mail to admin
                        if (majesticsupport::$_config['ticket_priority_admin'] == 1) {
                            $MJTC_link = admin_url("admin.php?page=majesticsupport_ticket&mjslay=ticketdetail&majesticsupportid=" . esc_attr($MJTC_id));
                            $MJTC_adminEmailid = majesticsupport::$_config['default_admin_email'];
                            $MJTC_adminEmail = $this->getEmailById($MJTC_adminEmailid);
                            $matcharray['{TICKETURL}'] = $MJTC_link;
                            $matcharray['{EMAIL}'] = $MJTC_adminEmail;
                            $template = apply_filters( 'MJTC_get_email_template_by_user_defined_language','','prtrans-tk' ,$MJTC_adminEmail ,'');
                            if($template == '' && empty($template)){
                                $template = $MJTC_defaulttemplate;
                            }
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $MJTC_attachments = '';
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);
                            $this->sendEmail($MJTC_adminEmail, $msgSubject, $msgBody, $MJTC_senderEmail, $MJTC_senderName, $MJTC_attachments, $MJTC_action, 'prtrans-tk-admin');
                        }
                        $msgSubject = $template->subject;
                        $msgBody = $template->body;
                        $MJTC_link = esc_url(majesticsupport::makeUrl(array('mjsmod'=>'ticket', 'mjslay'=>'ticketdetail','majesticsupportid'=>$MJTC_id,'mspageid'=>majesticsupport::getPageid())));
                        $matcharray['{TICKETURL}'] = $MJTC_link;
                        // New ticket mail to staff
                        if ( in_array('agent',majesticsupport::$_active_addons) && majesticsupport::$_config['ticket_priority_staff'] == 1) {
                            $MJTC_agentEmail = $this->getStaffEmailAddressByStaffId($MJTC_ticketRecord->staffid);
                            $MJTC_staffuid = $this->getStaffUidByStaffId($MJTC_ticketRecord->staffid);
                            $matcharray['{EMAIL}'] = $MJTC_agentEmail;
                            if (MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForAgent('Mail To Agent', $MJTC_staffuid) == 1) {
                                $template = apply_filters( 'MJTC_get_email_template_by_user_defined_language','','prtrans-tk' ,$MJTC_agentEmail ,$MJTC_staffuid, $MJTC_ticketRecord->multiformid);
                                if($template == '' && empty($template)){
                                    $template = $MJTC_defaulttemplate;
                                }
                                $msgSubject = $template->subject;
                                $msgBody = $template->body;
                                $MJTC_attachments = '';

                                $this->replaceMatches($msgSubject, $matcharray);
                                $this->replaceMatches($msgBody, $matcharray);
                                $this->sendEmail($MJTC_agentEmail, $msgSubject, $msgBody, $MJTC_senderEmail, $MJTC_senderName, $MJTC_attachments, $MJTC_action, 'prtrans-tk-staff');
                            }
                        }
                        // New ticket mail to User
                        if (majesticsupport::$_config['ticket_priority_user'] == 1) {
                            $template = apply_filters( 'MJTC_get_email_template_by_user_defined_language','','prtrans-tk' ,$Email ,$MJTC_ticketRecord->uid, $MJTC_ticketRecord->multiformid);
                            if($template == '' && empty($template)){
                                $template = $MJTC_defaulttemplate;
                            }
                            $matcharray['{EMAIL}'] = $Email;
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $MJTC_attachments = '';
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);
                            $this->sendEmail($Email, $msgSubject, $msgBody, $MJTC_senderEmail, $MJTC_senderName, $MJTC_attachments, $MJTC_action);
                        }
                        break;
                    case 12: // DEPARTMENT TRANSFER
                        $MJTC_ticketRecord = $this->getRecordByTablenameAndId('mjtc_support_tickets', $MJTC_id);
                        $TrackingId = $MJTC_ticketRecord->ticketid;
                        $Subject = $MJTC_ticketRecord->subject;
                        $DepName = $MJTC_ticketRecord->departmentname;
                        if(in_array('helptopic', majesticsupport::$_active_addons)){
                            $HelptopicName = $MJTC_ticketRecord->topic;
                        }else{
                            $HelptopicName = '';
                        }
                        $Email = $MJTC_ticketRecord->email;
                        $Department = MJTC_includer::MJTC_getModel('department')->getDepartmentById($MJTC_ticketRecord->departmentid);
                        $matcharray = array(
                            '{SITETITLE}' => majesticsupport::$_config['title'],
                            '{SUBJECT}' => $Subject,
                            '{HELP_TOPIC}' => $HelptopicName,
                            '{TRACKINGID}' => $TrackingId,
                            '{DEPARTMENT_TITLE}' => majesticsupport::MJTC_getVarValue($MJTC_ticketRecord->departmentname),
                            '{DEPARTMENT}' => majesticsupport::MJTC_getVarValue($MJTC_ticketRecord->departmentname),
                            '{PRIORITY}' => majesticsupport::MJTC_getVarValue($MJTC_ticketRecord->priority),
                            '{CURRENT_YEAR}' => gmdate('Y')
                        );
                        // code for handling custom fields start
                        $MJTC_fvalue = '';
                        if(!empty($MJTC_ticketRecord->params)){
                            $MJTC_data = json_decode($MJTC_ticketRecord->params,true);
                        }
                        $MJTC_fields = MJTC_includer::MJTC_getModel('fieldordering')->getUserfieldsfor(1);
                        if( isset($MJTC_data) && is_array($MJTC_data)){
                            foreach ($MJTC_fields as $MJTC_field) {
                                if($MJTC_field->userfieldtype != 'file'){
                                    $MJTC_fvalue = '';
                                    if(array_key_exists($MJTC_field->field, $MJTC_data)){
                                        $MJTC_fvalue = $MJTC_data[$MJTC_field->field];
                                    }
                                    $matcharray['{'.esc_attr($MJTC_field->field).'}'] = $MJTC_fvalue;// match array new index for custom field
                                }
                            }
                        }
                        // code for handling custom fields end
                        $MJTC_object = $this->getSenderEmailAndName($MJTC_id);
                        $MJTC_senderEmail = $MJTC_object->email;
                        $MJTC_senderName = $MJTC_object->name;
                        $MJTC_defaulttemplate = $this->getTemplateForEmail('deptrans-tk', $MJTC_ticketRecord->multiformid);
                        // New ticket mail to admin
                        if (majesticsupport::$_config['ticket_department_transfer_admin'] == 1) {
                            $MJTC_adminEmailid = majesticsupport::$_config['default_admin_email'];
                            $MJTC_adminEmail = $this->getEmailById($MJTC_adminEmailid);
                            $matcharray['{EMAIL}'] = $MJTC_adminEmail;

                            $template = apply_filters( 'MJTC_get_email_template_by_user_defined_language','','deptrans-tk' ,$MJTC_adminEmail ,'', $MJTC_ticketRecord->multiformid);
                            if($template == '' && empty($template)){
                                $template = $MJTC_defaulttemplate;
                            }
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $MJTC_attachments = '';
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);
                            $this->sendEmail($MJTC_adminEmail, $msgSubject, $msgBody, $MJTC_senderEmail, $MJTC_senderName, $MJTC_attachments, $MJTC_action, 'deptrans-tk-admin');
                        }
                        // New ticket mail to staff
                        if ( in_array('agent',majesticsupport::$_active_addons) && majesticsupport::$_config['ticket_department_transfer_staff'] == 1) {
                            $MJTC_agentEmail = $this->getStaffEmailAddressByStaffId($MJTC_ticketRecord->staffid);
                            $MJTC_staffuid = $this->getStaffUidByStaffId($MJTC_ticketRecord->staffid);
                            $matcharray['{EMAIL}'] = $MJTC_agentEmail;
                            if (MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForAgent('Mail To Agent', $MJTC_staffuid) == 1) {
                                $template = apply_filters( 'MJTC_get_email_template_by_user_defined_language','','deptrans-tk' ,$MJTC_agentEmail ,$MJTC_staffuid, $MJTC_ticketRecord->multiformid);
                                if($template == '' && empty($template)){
                                    $template = $MJTC_defaulttemplate;
                                }
                                $msgSubject = $template->subject;
                                $msgBody = $template->body;
                                $MJTC_attachments = '';
                                $this->replaceMatches($msgSubject, $matcharray);
                                $this->replaceMatches($msgBody, $matcharray);
                                $this->sendEmail($MJTC_agentEmail, $msgSubject, $msgBody, $MJTC_senderEmail, $MJTC_senderName, $MJTC_attachments, $MJTC_action, 'deptrans-tk-staff');
                            }
                            // send email to all staff memebers of current ticket department
                            // Get All Staff member of the department of Current Ticket
                            $MJTC_agentmembers = MJTC_includer::MJTC_getModel('agent')->getAllStaffMemberByDepId($MJTC_ticketRecord->departmentid);
                            if(is_array($MJTC_agentmembers) && !empty($MJTC_agentmembers)){
                                foreach ($MJTC_agentmembers AS $MJTC_agent) {
                                    if($MJTC_agent->canemail == 1){
                                        if (MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForAgent('Mail To Agent', $MJTC_agent->staffuid) == 1) {
                                            $template = apply_filters( 'MJTC_get_email_template_by_user_defined_language','','deptrans-tk' ,$MJTC_agent->email ,$MJTC_agent->staffuid, $MJTC_ticketRecord->multiformid);
                                            $matcharray['{EMAIL}'] = $MJTC_agent->email;
                                            if($template == '' && empty($template)){
                                                $template = $MJTC_defaulttemplate;
                                            }
                                            $msgSubject = $template->subject;
                                            $msgBody = $template->body;
                                            $this->replaceMatches($msgSubject, $matcharray);
                                            $this->replaceMatches($msgBody, $matcharray);
                                            $msgBody .= '<input type="hidden" name="ticketid:' . esc_attr($TrackingId) . '###staff####" />';
                                            $msgBody .= '<span style="display:none;" ticketid:' . esc_attr($TrackingId) . '###staff#### ></span>';
                                            $MJTC_attachments = '';
                                            $this->sendEmail($MJTC_agent->email, $msgSubject, $msgBody, $MJTC_senderEmail, $MJTC_senderName, $MJTC_attachments, $MJTC_action);
                                        }
                                    }
                                }
                            }
                            // send email to staff memebers with all ticket permissions
                            if( !is_numeric($MJTC_ticketRecord->staffid) && !is_numeric($MJTC_ticketRecord->departmentid)){
                                if( in_array('agent',majesticsupport::$_active_addons) ){
                                    $MJTC_agentmembers = MJTC_includer::MJTC_getModel('agent')->getAllStaffMemberByAllTicketPermission();
                                    if(is_array($MJTC_agentmembers) && !empty($MJTC_agentmembers)){
                                        foreach ($MJTC_agentmembers AS $MJTC_agent) {
                                            if($MJTC_agent->canemail == 1){
                                                if (MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForAgent('Mail To Agent', $MJTC_agent->uid) == 1) {
                                                    $template = apply_filters( 'MJTC_get_email_template_by_user_defined_language','','deptrans-tk' ,$MJTC_agent->email ,$MJTC_agent->uid, $MJTC_ticketRecord->multiformid);
                                                    $matcharray['{EMAIL}'] = $MJTC_agent->email;
                                                    if($template == '' && empty($template)){
                                                        $template = $MJTC_defaulttemplate;
                                                    }
                                                    $msgSubject = $template->subject;
                                                    $msgBody = $template->body;
                                                    $this->replaceMatches($msgSubject, $matcharray);
                                                    $this->replaceMatches($msgBody, $matcharray);
                                                    $msgBody .= '<input type="hidden" name="ticketid:' . esc_attr($TrackingId) . '###staff####" />';
                                                    $msgBody .= '<span style="display:none;" ticketid:' . esc_attr($TrackingId) . '###staff#### ></span>';
                                                    $MJTC_attachments = '';
                                                    $this->sendEmail($MJTC_agent->email, $msgSubject, $msgBody, $MJTC_senderEmail, $MJTC_senderName, $MJTC_attachments, $MJTC_action);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        // New ticket mail to User
                        if (majesticsupport::$_config['ticket_department_transfer_user'] == 1) {
                            $template = apply_filters( 'MJTC_get_email_template_by_user_defined_language','','deptrans-tk' ,$Email,$MJTC_ticketRecord->uid, $MJTC_ticketRecord->multiformid);
                            if($template == '' && empty($template)){
                                $template = $MJTC_defaulttemplate;
                            }
                            $matcharray['{EMAIL}'] = $Email;
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $MJTC_attachments = '';
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);
                            $this->sendEmail($Email, $msgSubject, $msgBody, $MJTC_senderEmail, $MJTC_senderName, $MJTC_attachments, $MJTC_action);
                        }
                        break;
                    case 13: // REASSIGN TICKET TO STAFF
                        if(! in_array('agent',majesticsupport::$_active_addons) ){
                            return;
                        }
                        $MJTC_ticketRecord = $this->getRecordByTablenameAndId('mjtc_support_tickets', $MJTC_id);
                        $TrackingId = $MJTC_ticketRecord->ticketid;
                        $DepName = $MJTC_ticketRecord->departmentname;
                        if(in_array('helptopic', majesticsupport::$_active_addons)){
                            $HelptopicName = $MJTC_ticketRecord->topic;
                        }else{
                            $HelptopicName = '';
                        }
                        $Email = $MJTC_ticketRecord->email;
                        $Subject = $MJTC_ticketRecord->subject;
                        $Staff = MJTC_includer::MJTC_getModel('agent')->getMyName($MJTC_ticketRecord->staffid);
                        $MJTC_ticketHistory = $this->getTicketReplyHistory($MJTC_id);
                        $matcharray = array(
                            '{SITETITLE}' => majesticsupport::$_config['title'],
                            '{AGENT_NAME}' => $Staff,
                            '{SUBJECT}' => $Subject,
                            '{HELP_TOPIC}' => $HelptopicName,
                            '{TRACKINGID}' => $TrackingId,
                            '{DEPARTMENT}' => majesticsupport::MJTC_getVarValue($MJTC_ticketRecord->departmentname),
                            '{PRIORITY}' => majesticsupport::MJTC_getVarValue($MJTC_ticketRecord->priority),
                            '{TICKET_HISTORY}' => $MJTC_ticketHistory,
                            '{CURRENT_YEAR}' => gmdate('Y')
                        );
                        // code for handling custom fields start
                        $MJTC_fvalue = '';
                        if(!empty($MJTC_ticketRecord->params)){
                            $MJTC_data = json_decode($MJTC_ticketRecord->params,true);
                        }
                        $MJTC_fields = MJTC_includer::MJTC_getModel('fieldordering')->getUserfieldsfor(1);
                        if( isset($MJTC_data) && is_array($MJTC_data)){
                            foreach ($MJTC_fields as $MJTC_field) {
                                if($MJTC_field->userfieldtype != 'file'){
                                    $MJTC_fvalue = '';
                                    if(array_key_exists($MJTC_field->field, $MJTC_data)){
                                        $MJTC_fvalue = $MJTC_data[$MJTC_field->field];
                                    }
                                    $matcharray['{'.esc_attr($MJTC_field->field).'}'] = $MJTC_fvalue;// match array new index for custom field
                                }
                            }
                        }
                        // code for handling custom fields end
                        $MJTC_object = $this->getSenderEmailAndName($MJTC_id);
                        $MJTC_senderEmail = $MJTC_object->email;
                        $MJTC_senderName = $MJTC_object->name;
                        $MJTC_defaulttemplate = $this->getTemplateForEmail('reassign-tk', $MJTC_ticketRecord->multiformid);
                        // New ticket mail to admin
                        $MJTC_link = admin_url("admin.php?page=majesticsupport_ticket&mjslay=ticketdetail&majesticsupportid=" . esc_attr($MJTC_id));
                        $matcharray['{TICKETURL}'] = $MJTC_link;
                        if (majesticsupport::$_config['ticket_reassign_admin'] == 1) {
                            $MJTC_adminEmailid = majesticsupport::$_config['default_admin_email'];
                            $MJTC_adminEmail = $this->getEmailById($MJTC_adminEmailid);
                            $matcharray['{EMAIL}'] = $MJTC_adminEmail;

                            $template = apply_filters( 'MJTC_get_email_template_by_user_defined_language','','reassign-tk' ,$MJTC_adminEmail ,'', $MJTC_ticketRecord->multiformid);
                            if($template == '' && empty($template)){
                                $template = $MJTC_defaulttemplate;
                            }
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $MJTC_attachments = '';

                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);
                            $this->sendEmail($MJTC_adminEmail, $msgSubject, $msgBody, $MJTC_senderEmail, $MJTC_senderName, $MJTC_attachments, $MJTC_action, 'reassign-tk-admin');
                        }

                        $matcharray = array(
                            '{SITETITLE}' => majesticsupport::$_config['title'],
                            '{AGENT_NAME}' => $Staff,
                            '{SUBJECT}' => $Subject,
                            '{HELP_TOPIC}' => $HelptopicName,
                            '{TRACKINGID}' => $TrackingId,
                            '{DEPARTMENT}' => majesticsupport::MJTC_getVarValue($MJTC_ticketRecord->departmentname),
                            '{PRIORITY}' => majesticsupport::MJTC_getVarValue($MJTC_ticketRecord->priority),
                            '{TICKET_HISTORY}' => $MJTC_ticketHistory,
                            '{CURRENT_YEAR}' => gmdate('Y')
                        );
                        // code for handling custom fields start
                        $MJTC_fvalue = '';
                        if(!empty($MJTC_ticketRecord->params)){
                            $MJTC_data = json_decode($MJTC_ticketRecord->params,true);
                        }
                        $MJTC_fields = MJTC_includer::MJTC_getModel('fieldordering')->getUserfieldsfor(1);
                        if( isset($MJTC_data) && is_array($MJTC_data)){
                            foreach ($MJTC_fields as $MJTC_field) {
                                if($MJTC_field->userfieldtype != 'file'){
                                    $MJTC_fvalue = '';
                                    if(array_key_exists($MJTC_field->field, $MJTC_data)){
                                        $MJTC_fvalue = $MJTC_data[$MJTC_field->field];
                                    }
                                    $matcharray['{'.esc_attr($MJTC_field->field).'}'] = $MJTC_fvalue;// match array new index for custom field
                                }
                            }
                        }
                        // code for handling custom fields end
                        $MJTC_link = esc_url(majesticsupport::makeUrl(array('mjsmod'=>'ticket', 'mjslay'=>'ticketdetail','majesticsupportid'=>$MJTC_id,'mspageid'=>majesticsupport::getPageid())));
                        $matcharray['{TICKETURL}'] = $MJTC_link;
                        // New ticket mail to staff
                        if ( in_array('agent',majesticsupport::$_active_addons) && majesticsupport::$_config['ticket_reassign_staff'] == 1) {
                            $MJTC_agentEmail = $this->getStaffEmailAddressByStaffId($MJTC_ticketRecord->staffid);
                            $matcharray['{EMAIL}'] = $MJTC_agentEmail;
                            $MJTC_staffuid = $this->getStaffUidByStaffId($MJTC_ticketRecord->staffid);
                            if (MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForAgent('Mail To Agent', $MJTC_staffuid) == 1) {
                                $template = apply_filters( 'MJTC_get_email_template_by_user_defined_language','','reassign-tk' ,$MJTC_adminEmail ,$MJTC_staffuid, $MJTC_ticketRecord->multiformid);
                                if($template == '' && empty($template)){
                                    $template = $MJTC_defaulttemplate;
                                }

                                $msgSubject = $template->subject;
                                $msgBody = $template->body;
                                $MJTC_attachments = '';
                                $this->replaceMatches($msgSubject, $matcharray);
                                $this->replaceMatches($msgBody, $matcharray);
                                $this->sendEmail($MJTC_agentEmail, $msgSubject, $msgBody, $MJTC_senderEmail, $MJTC_senderName, $MJTC_attachments, $MJTC_action, 'reassign-tk-staff');
                            }
                        }
                        // New ticket mail to User
                        if (majesticsupport::$_config['ticket_reassign_user'] == 1) {
                            $template = apply_filters( 'MJTC_get_email_template_by_user_defined_language','','reassign-tk' ,$Email ,$MJTC_ticketRecord->uid, $MJTC_ticketRecord->multiformid);
                            if($template == '' && empty($template)){
                                $template = $MJTC_defaulttemplate;
                            }
                            $matcharray['{EMAIL}'] = $Email;
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $MJTC_attachments = '';
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);
                            $this->sendEmail($Email, $msgSubject, $msgBody, $MJTC_senderEmail, $MJTC_senderName, $MJTC_attachments, $MJTC_action);
                        }
                        break;
                    case 14: // Reply to closed ticket for Email Piping
                        $MJTC_ticketRecord = $this->getRecordByTablenameAndId('mjtc_support_tickets', $MJTC_id);
                        $Subject = $MJTC_ticketRecord->subject;
                        $Email = $MJTC_ticketRecord->email;
                        $matcharray = array(
                            '{SITETITLE}' => majesticsupport::$_config['title'],
                            '{SUBJECT}' => $Subject,
                            '{DEPARTMENT}' => majesticsupport::MJTC_getVarValue($MJTC_ticketRecord->departmentname),
                            '{PRIORITY}' => majesticsupport::MJTC_getVarValue($MJTC_ticketRecord->priority),
                            '{CURRENT_YEAR}' => gmdate('Y')
                        );
                        // code for handling custom fields start
                        $MJTC_fvalue = '';
                        if(!empty($MJTC_ticketRecord->params)){
                            $MJTC_data = json_decode($MJTC_ticketRecord->params,true);
                        }
                        $MJTC_fields = MJTC_includer::MJTC_getModel('fieldordering')->getUserfieldsfor(1);
                        if( isset($MJTC_data) && is_array($MJTC_data)){
                            foreach ($MJTC_fields as $MJTC_field) {
                                if($MJTC_field->userfieldtype != 'file'){
                                    $MJTC_fvalue = '';
                                    if(array_key_exists($MJTC_field->field, $MJTC_data)){
                                        $MJTC_fvalue = $MJTC_data[$MJTC_field->field];
                                    }
                                    $matcharray['{'.esc_attr($MJTC_field->field).'}'] = $MJTC_fvalue;// match array new index for custom field
                                }
                            }
                        }
                        // code for handling custom fields end
                        $MJTC_object = $this->getSenderEmailAndName($MJTC_id);
                        $MJTC_senderEmail = $MJTC_object->email;
                        $MJTC_senderName = $MJTC_object->name;
                        $MJTC_defaulttemplate = $this->getTemplateForEmail('mail-rpy-closed', $MJTC_ticketRecord->multiformid);
                        // New ticket mail to User
                        if (majesticsupport::$_config['ticket_reply_closed_ticket_user'] == 1) {
                            $template = apply_filters( 'MJTC_get_email_template_by_user_defined_language','','mail-rpy-closed' ,$Email ,$MJTC_ticketRecord->uid, $MJTC_ticketRecord->multiformid);
                            if($template == '' && empty($template)){
                                $template = $MJTC_defaulttemplate;
                            }
                            $msgBody = $template->body;
                            $MJTC_attachments = '';
                            $this->replaceMatches($msgBody, $matcharray);
                            $this->sendEmail($Email, $msgSubject, $msgBody, $MJTC_senderEmail, $MJTC_senderName, $MJTC_attachments, $MJTC_action);
                        }
                        break;
                    case 15: // Send feedback email to user
                        if(!in_array('feedback', majesticsupport::$_active_addons)){
                            break;
                        }
                        $MJTC_ticketRecord = $this->getRecordByTablenameAndId('mjtc_support_tickets', $MJTC_id);
                        $Subject = $MJTC_ticketRecord->subject;
                        $Email = $MJTC_ticketRecord->email;
                        $TrackingId = $MJTC_ticketRecord->ticketid;
                        $MJTC_close_date = date_i18n(majesticsupport::$_config['date_format'], MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticketRecord->closed));
                        $MJTC_username = $MJTC_ticketRecord->name;
                        $MJTC_tokenarray['emailaddress']=$Email;
                        $MJTC_tokenarray['trackingid']=$TrackingId;
                        $MJTC_tokenarray['sitelink']=MJTC_includer::MJTC_getModel('majesticsupport')->getEncriptedSiteLink();
                        $MJTC_token = wp_json_encode($MJTC_tokenarray);
                        include_once MJTC_PLUGIN_PATH . 'includes/encoder.php';
                        $MJTC_encoder = new MJTC_encoder();
                        $MJTC_encryptedtext = $MJTC_encoder->MJTC_encrypt($MJTC_token);
                        $MJTC_link = "<a href=" . esc_url(majesticsupport::makeUrl(array('mjsmod'=>'feedback', 'task'=>'showfeedbackform','action'=>'mstask','token'=>$MJTC_encryptedtext,'mspageid'=>majesticsupport::getPageid()))) . ">";
                        $MJTC_linkclosing = "</a>";
                        $tracking_url = "<a href=" . esc_url(majesticsupport::makeUrl(array('mjsmod'=>'ticket', 'task'=>'showticketstatus','action'=>'mstask','token'=>$MJTC_encryptedtext,'mspageid'=>majesticsupport::getPageid()))) . ">" . $TrackingId . "</a>";
                        $matcharray = array(
                            '{SITETITLE}' => majesticsupport::$_config['title'],
                            '{USER_NAME}' => $MJTC_username,
                            '{TICKET_SUBJECT}' => $Subject,
                            '{TRACKING_ID}' => $tracking_url,
                            '{CLOSE_DATE}' => $MJTC_close_date,
                            '{LINK}' => $MJTC_link,
                            '{/LINK}' => $MJTC_linkclosing,
                            '{DEPARTMENT}' => majesticsupport::MJTC_getVarValue($MJTC_ticketRecord->departmentname),
                            '{PRIORITY}' => majesticsupport::MJTC_getVarValue($MJTC_ticketRecord->priority),
                            '{CURRENT_YEAR}' => gmdate('Y')
                        );
                        // code for handling custom fields start
                        $MJTC_fvalue = '';
                        if(!empty($MJTC_ticketRecord->params)){
                            $MJTC_data = json_decode($MJTC_ticketRecord->params,true);
                        }
                        $MJTC_fields = MJTC_includer::MJTC_getModel('fieldordering')->getUserfieldsfor(1);
                        if( isset($MJTC_data) && is_array($MJTC_data)){
                            foreach ($MJTC_fields as $MJTC_field) {
                                if($MJTC_field->userfieldtype != 'file'){
                                    $MJTC_fvalue = '';
                                    if(array_key_exists($MJTC_field->field, $MJTC_data)){
                                        $MJTC_fvalue = $MJTC_data[$MJTC_field->field];
                                    }
                                    $matcharray['{'.esc_attr($MJTC_field->field).'}'] = $MJTC_fvalue;// match array new index for custom field
                                }
                            }
                        }
                        // code for handling custom fields end
                        $MJTC_object = $this->getSenderEmailAndName($MJTC_id);
                        $MJTC_senderEmail = $MJTC_object->email;
                        $MJTC_senderName = $MJTC_object->name;
                        $MJTC_defaulttemplate = $this->getTemplateForEmail('mail-feedback', $MJTC_ticketRecord->multiformid);
                        // New ticket mail to User
                        if (majesticsupport::$_config['ticket_feedback_user'] == 1) {
                            $template = apply_filters( 'MJTC_get_email_template_by_user_defined_language','','mail-feedback' ,$Email ,$MJTC_ticketRecord->uid);
                            if($template == '' && empty($template)){
                                $template = $MJTC_defaulttemplate;
                            }
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $MJTC_attachments = '';
                            $this->replaceMatches($msgBody, $matcharray);
                            $this->sendEmail($Email, $msgSubject, $msgBody, $MJTC_senderEmail, $MJTC_senderName, $MJTC_attachments, $MJTC_action);
                        }
                        break;
                }
                break;
            case 2: // Ban Email
                switch ($MJTC_action) {
                    case 1: // Ban Email
                        if ($MJTC_tablename != null)
                            $MJTC_banemailRecord = $this->getRecordByTablenameAndId($MJTC_tablename, $MJTC_id);
                        else
                            $MJTC_banemailRecord = $this->getRecordByTablenameAndId('mjtc_support_email_banlist', $MJTC_id);
                        $Email = $MJTC_banemailRecord->email;
                        $matcharray = array(
                            '{SITETITLE}' => majesticsupport::$_config['title'],
                            '{EMAIL_ADDRESS}' => $Email,
                            '{CURRENT_YEAR}' => gmdate('Y')
                        );
                        $MJTC_object = $this->getDefaultSenderEmailAndName();
                        $MJTC_senderEmail = $MJTC_object->email;
                        $MJTC_senderName = $MJTC_object->name;
                        $MJTC_defaulttemplate = $this->getTemplateForEmail('banemail-tk', $MJTC_ticketRecord->multiformid);

                        // New ticket mail to admin
                        if (majesticsupport::$_config['ticket_ban_email_admin'] == 1) {
                            $MJTC_adminEmailid = majesticsupport::$_config['default_admin_email'];
                            $MJTC_adminEmail = $this->getEmailById($MJTC_adminEmailid);
                            $matcharray['{EMAIL}'] = $MJTC_adminEmail;
                            $template = apply_filters( 'MJTC_get_email_template_by_user_defined_language','','banemail-tk' ,$MJTC_adminEmail ,'');
                            if($template == '' && empty($template)){
                                $template = $MJTC_defaulttemplate;
                            }
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $MJTC_attachments = '';

                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);
                            $this->sendEmail($MJTC_adminEmail, $msgSubject, $msgBody, $MJTC_senderEmail, $MJTC_senderName, $MJTC_attachments, $MJTC_action, 'banemail-tk-admin');
                        }
                        // New ticket mail to staff
                        if ( in_array('agent',majesticsupport::$_active_addons) && majesticsupport::$_config['ticket_ban_email_staff'] == 1) {
                            if ($MJTC_tablename != null){
                                $MJTC_agentEmail = $this->getStaffEmailAddressByStaffId($MJTC_banemailRecord->staffid);
                                $MJTC_staffuid = $this->getStaffUidByStaffId($MJTC_banemailRecord->staffid);
                            }else{
                                $MJTC_agentEmail = $this->getStaffEmailAddressByStaffId($MJTC_banemailRecord->submitter);
                                $MJTC_staffuid = $this->getStaffUidByStaffId($MJTC_banemailRecord->submitter);
                            }

                            if (MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForAgent('Mail To Agent', $MJTC_staffuid) == 1) {
                                $template = apply_filters( 'MJTC_get_email_template_by_user_defined_language','','banemail-tk' ,$MJTC_agentEmail ,$MJTC_staffuid);
                                $matcharray['{EMAIL}'] = $MJTC_agentEmail;
                                if($template == '' && empty($template)){
                                    $template = $MJTC_defaulttemplate;
                                }
                                $msgSubject = $template->subject;
                                $msgBody = $template->body;
                                $MJTC_attachments = '';

                                $this->replaceMatches($msgSubject, $matcharray);
                                $this->replaceMatches($msgBody, $matcharray);
                                $this->sendEmail($MJTC_agentEmail, $msgSubject, $msgBody, $MJTC_senderEmail, $MJTC_senderName, $MJTC_attachments, $MJTC_action, 'banemail-tk-staff');
                            }
                        }
                        // New ticket mail to User
                        if (majesticsupport::$_config['ticket_ban_email_user'] == 1) {
                            $template = apply_filters( 'MJTC_get_email_template_by_user_defined_language','','banemail-tk' ,$Email ,'');
                            if($template == '' && empty($template)){
                                $template = $MJTC_defaulttemplate;
                            }
                            $matcharray['{EMAIL}'] = $Email;
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $MJTC_attachments = '';

                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);
                            $this->sendEmail($Email, $msgSubject, $msgBody, $MJTC_senderEmail, $MJTC_senderName, $MJTC_attachments, $MJTC_action);
                        }
                        break;
                    case 2: // Unban Email
                        if ($MJTC_tablename != null)
                            $MJTC_ticketRecord = $this->getRecordByTablenameAndId($MJTC_tablename, $MJTC_id);
                        else
                            $MJTC_ticketRecord = $this->getRecordByTablenameAndId('mjtc_support_tickets', $MJTC_id);
                        $Email = $MJTC_ticketRecord->email;
                        $matcharray = array(
                            '{SITETITLE}' => majesticsupport::$_config['title'],
                            '{EMAIL_ADDRESS}' => $Email,
                            '{CURRENT_YEAR}' => gmdate('Y')
                        );
                        $MJTC_object = $this->getSenderEmailAndName($MJTC_id);
                        $MJTC_senderEmail = $MJTC_object->email;
                        $MJTC_senderName = $MJTC_object->name;
                        $MJTC_defaulttemplate = $this->getTemplateForEmail('unbanemail-tk');

                        // New ticket mail to admin
                        if (majesticsupport::$_config['unban_email_admin'] == 1) {
                            $MJTC_adminEmailid = majesticsupport::$_config['default_admin_email'];
                            $MJTC_adminEmail = $this->getEmailById($MJTC_adminEmailid);
                            $matcharray['{EMAIL}'] = $MJTC_adminEmail;
                            $template = apply_filters( 'MJTC_get_email_template_by_user_defined_language','','unbanemail-tk' ,$MJTC_adminEmail ,'', $MJTC_ticketRecord->multiformid);
                            if($template == '' && empty($template)){
                                $template = $MJTC_defaulttemplate;
                            }
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $MJTC_attachments = '';
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);
                            $this->sendEmail($MJTC_adminEmail, $msgSubject, $msgBody, $MJTC_senderEmail, $MJTC_senderName, $MJTC_attachments, $MJTC_action, 'unbanemail-tk-admin');
                        }
                        // New ticket mail to staff
                        if ( in_array('agent',majesticsupport::$_active_addons) && majesticsupport::$_config['unban_email_staff'] == 1) {
                            if ($MJTC_tablename != null){
                                $MJTC_agentEmail = $this->getStaffEmailAddressByStaffId($MJTC_ticketRecord->staffid);
                                $MJTC_staffuid = $this->getStaffUidByStaffId($MJTC_ticketRecord->staffid);
                            }else{
                                $MJTC_agentEmail = $this->getStaffEmailAddressByStaffId($MJTC_ticketRecord->submitter);
                                $MJTC_staffuid = $this->getStaffUidByStaffId($MJTC_ticketRecord->submitter);
                            }
                            if (MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForAgent('Mail To Agent', $MJTC_staffuid) == 1) {
                                $template = apply_filters( 'MJTC_get_email_template_by_user_defined_language','','unbanemail-tk' ,$MJTC_agentEmail ,$MJTC_staffuid, $MJTC_ticketRecord->multiformid);
                                if($template == '' && empty($template)){
                                    $template = $MJTC_defaulttemplate;
                                }
                                $matcharray['{EMAIL}'] = $MJTC_agentEmail;
                                $msgSubject = $template->subject;
                                $msgBody = $template->body;
                                $MJTC_attachments = '';
                                $this->replaceMatches($msgSubject, $matcharray);
                                $this->replaceMatches($msgBody, $matcharray);
                                $this->sendEmail($MJTC_agentEmail, $msgSubject, $msgBody, $MJTC_senderEmail, $MJTC_senderName, $MJTC_attachments, $MJTC_action, 'unbanemail-tk-staff');
                            }
                        }
                        // New ticket mail to User
                        if (majesticsupport::$_config['unban_email_user'] == 1) {
                            if ($MJTC_tablename != null){
                                $template = apply_filters( 'MJTC_get_email_template_by_user_defined_language','','unbanemail-tk' , $Email, '', $MJTC_ticketRecord->multiformid);
                            }else{
                                $template = apply_filters( 'MJTC_get_email_template_by_user_defined_language','','unbanemail-tk' ,$MJTC_ticketRecord->email , $MJTC_ticketRecord->uid, $MJTC_ticketRecord->multiformid);
                            }

                            if($template == '' && empty($template)){
                                $template = $MJTC_defaulttemplate;
                            }
                            $matcharray['{EMAIL}'] = $Email;
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $MJTC_attachments = '';
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);
                            $this->sendEmail($Email, $msgSubject, $msgBody, $MJTC_senderEmail, $MJTC_senderName, $MJTC_attachments, $MJTC_action);
                        }
                        break;
                }
                break;
            case 3: // Sending email alerts on mail system
                if(!in_array('mail', majesticsupport::$_active_addons)){ // if mail addon is not installed
                    break;
                }
                switch ($MJTC_action) {
                    case 1: // Store message
                        $mailRecord = $this->getMailRecordById($MJTC_id);
                        $matcharray = array(
                            '{SITETITLE}' => majesticsupport::$_config['title'],
                            '{AGENT_NAME}' => $mailRecord->sendername,
                            '{SUBJECT}' => $mailRecord->subject,
                            '{MESSAGE}' => $mailRecord->message,
                            '{EMAIL}' => $mailRecord,
                            '{CURRENT_YEAR}' => gmdate('Y')
                        );
                        $MJTC_object = $this->getSenderEmailAndName(null);
                        $MJTC_senderEmail = $MJTC_object->email;
                        $MJTC_senderName = $MJTC_object->name;
                        $MJTC_defaulttemplate = $this->getTemplateForEmail('mail-new');
                        $template = apply_filters( 'MJTC_get_email_template_by_user_defined_language','','mail-new' ,'' ,$mailRecord->staffuid);
                        if($template == '' && empty($template)){
                            $template = $MJTC_defaulttemplate;
                        }
                        $msgSubject = $template->subject;
                        $msgBody = $template->body;

                        $Email = isset($mailRecord->receveremail) ? $mailRecord->receveremail : '';
                        $MJTC_attachments = '';
                        $this->replaceMatches($msgSubject, $matcharray);
                        $this->replaceMatches($msgBody, $matcharray);
                        $this->sendEmail($Email, $msgSubject, $msgBody, $MJTC_senderEmail, $MJTC_senderName, $MJTC_attachments, $MJTC_action, 'mail-new');
                        break;
                    case 2: // Store reply
                        $mailRecord = $this->getMailRecordById($MJTC_id, 1);
                        $matcharray = array(
                            '{SITETITLE}' => majesticsupport::$_config['title'],
                            '{AGENT_NAME}' => $mailRecord->sendername,
                            '{SUBJECT}' => $mailRecord->subject,
                            '{MESSAGE}' => $mailRecord->message,
                            '{EMAIL}' => $mailRecord,
                            '{CURRENT_YEAR}' => gmdate('Y')
                        );
                        $MJTC_object = $this->getSenderEmailAndName(null);
                        $MJTC_senderEmail = $MJTC_object->email;
                        $MJTC_senderName = $MJTC_object->name;
                        $MJTC_defaulttemplate = $this->getTemplateForEmail('mail-rpy');
                        $template = apply_filters( 'MJTC_get_email_template_by_user_defined_language','','mail-rpy' ,'' ,$mailRecord->staffuid);
                        if($template == '' && empty($template)){
                            $template = $MJTC_defaulttemplate;
                        }
                        $msgSubject = $template->subject;
                        $msgBody = $template->body;
                        $Email = isset($mailRecord->receveremail) ? $mailRecord->receveremail : '';
                        $MJTC_attachments = '';
                        $this->replaceMatches($msgSubject, $matcharray);
                        $this->replaceMatches($msgBody, $matcharray);
                        $this->sendEmail($Email, $msgSubject, $msgBody, $MJTC_senderEmail, $MJTC_senderName, $MJTC_attachments, $MJTC_action, 'mail-rpy');
                        break;
                }
                break;
            case 4: // gdpr data erase or delte.
                switch ($MJTC_action) {
                    case 1: // erase data email
                        $Email = majesticsupport::$_data['mail_data']['email'];
                        $matcharray = array(
                            '{SITETITLE}' => majesticsupport::$_config['title'],
                            '{USERNAME}' => majesticsupport::$_data['mail_data']['name'],
                            '{EMAIL}' => $Email,
                            '{CURRENT_YEAR}' => gmdate('Y')
                        );
                        $MJTC_object = $this->getSenderEmailAndName(null);
                        $MJTC_senderEmail = $MJTC_object->email;
                        $MJTC_senderName = $MJTC_object->name;
                        $MJTC_defaulttemplate = $this->getTemplateForEmail('delete-user-data');
                        $template = apply_filters( 'MJTC_get_email_template_by_user_defined_language','','delete-user-data' ,majesticsupport::$_data['mail_data']['email'] , '');
                        if($template == '' && empty($template)){
                            $template = $MJTC_defaulttemplate;
                        }

                        $msgSubject = $template->subject;
                        $msgBody = $template->body;
                        $MJTC_attachments = '';
                        $this->replaceMatches($msgSubject, $matcharray);
                        $this->replaceMatches($msgBody, $matcharray);
                        $this->sendEmail($Email, $msgSubject, $msgBody, $MJTC_senderEmail, $MJTC_senderName, $MJTC_attachments, $MJTC_action);
                        break;
                }
                break;
            case 5: // agent emails
                switch ($MJTC_action) {
                    case 1: // new agent
                        $MJTC_staffname = MJTC_includer::MJTC_getModel('agent')->getMyName($MJTC_id);
                        $MJTC_object = $this->getSenderEmailAndName(null);
                        $MJTC_senderEmail = $MJTC_object->email;
                        $MJTC_senderName = $MJTC_object->name;
                        $matcharray = array(
                            '{SITETITLE}' => majesticsupport::$_config['title'],
                            '{AGENT_NAME}' => $MJTC_staffname,
                            '{EMAIL}' => $MJTC_object->email,
                            '{CURRENT_YEAR}' => gmdate('Y')
                        );
                        
                        $MJTC_defaulttemplate = $this->getTemplateForEmail('staff-new');

                        $MJTC_adminEmailid = majesticsupport::$_config['default_admin_email'];
                        $MJTC_adminEmail = $this->getEmailById($MJTC_adminEmailid);
                        $template = apply_filters( 'MJTC_get_email_template_by_user_defined_language','','staff-new' , $MJTC_adminEmail , '');
                        if($template == '' && empty($template)){
                            $template = $MJTC_defaulttemplate;
                        }
                        $msgSubject = $template->subject;
                        $msgBody = $template->body;
                        $MJTC_attachments = '';
                        $this->replaceMatches($msgSubject, $matcharray);
                        $this->replaceMatches($msgBody, $matcharray);
                        $this->sendEmail($MJTC_adminEmail, $msgSubject, $msgBody, $MJTC_senderEmail, $MJTC_senderName, $MJTC_attachments, $MJTC_action, 'staff-new');
                        break;
                }
                break;
        }
    }


    function getMailRecordById($MJTC_id, $MJTC_replyto = null) { // this function will not be called if the mail addon is not installed
        if (!is_numeric($MJTC_id))
            return false;
        if ($MJTC_replyto == null) {
            $MJTC_query = "SELECT mail.subject,mail.message,CONCAT(staff.firstname,' ',staff.lastname) AS sendername, staff.uid as staffuid
                        FROM `" . majesticsupport::$_db->prefix . "mjtc_support_staff_mail` AS mail
                        JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_staff` AS staff ON staff.id = mail.fromid
                        WHERE mail.id = " . esc_sql($MJTC_id);
        } else {
            $MJTC_query = "SELECT mail.subject,reply.message,CONCAT(staff.firstname,' ',staff.lastname) AS sendername, staff.uid as staffuid
                        FROM `" . majesticsupport::$_db->prefix . "mjtc_support_staff_mail` AS reply
                        JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_staff_mail` AS mail ON mail.id = reply.replytoid
                        JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_staff` AS staff ON staff.id = reply.fromid
                        WHERE reply.id = " . esc_sql($MJTC_id);
        }
        $MJTC_result = majesticsupport::$_db->get_row($MJTC_query);
            $MJTC_query = "SELECT staff.email
                        FROM `" . majesticsupport::$_db->prefix . "mjtc_support_staff_mail` AS mail
                        JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_staff` AS staff ON staff.id = mail.toid
                        WHERE mail.id = " . esc_sql($MJTC_id);
        $MJTC_email = majesticsupport::$_db->get_var($MJTC_query);
        if (isset($MJTC_email)) {
            $MJTC_result->receveremail = $MJTC_email;
        }
        return $MJTC_result;
    }

    private function getStaffEmailAddressByStaffId($MJTC_id) {
        if (!is_numeric($MJTC_id))
            return false;
        $MJTC_query = "SELECT staff.email
                    FROM `" . majesticsupport::$_db->prefix . "mjtc_support_staff` AS staff
                    WHERE staff.id = " . esc_sql($MJTC_id);
        $MJTC_emailaddress = majesticsupport::$_db->get_var($MJTC_query);
        return $MJTC_emailaddress;
    }

    private function getStaffUidByStaffId($MJTC_id) {
        if (!is_numeric($MJTC_id))
            return false;
        $MJTC_query = "SELECT staff.uid
                    FROM `" . majesticsupport::$_db->prefix . "mjtc_support_staff` AS staff
                    WHERE staff.id = " . esc_sql($MJTC_id);
        $MJTC_emailaddress = majesticsupport::$_db->get_var($MJTC_query);
        return $MJTC_emailaddress;
    }

    private function getLatestReplyByTicketId($MJTC_id) {
        if (!is_numeric($MJTC_id))
            return false;
        $MJTC_query = "SELECT reply.message FROM `" . majesticsupport::$_db->prefix . "mjtc_support_replies` AS reply WHERE reply.ticketid = " . esc_sql($MJTC_id) . " ORDER BY reply.created DESC LIMIT 1";
        $MJTC_message = majesticsupport::$_db->get_var($MJTC_query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        return $MJTC_message;
    }

    private function replaceMatches(&$MJTC_string, $matcharray) {
        foreach ($matcharray AS $MJTC_find => $MJTC_replace) {
            if($MJTC_string != '' && $MJTC_replace != ''){
                $MJTC_string = MJTC_majesticsupportphplib::MJTC_str_replace($MJTC_find, $MJTC_replace, $MJTC_string);
            }
        }
    }

    function sendEmail($MJTC_recevierEmail, $MJTC_subject, $MJTC_body, $MJTC_senderEmail, $MJTC_senderName, $MJTC_attachments, $MJTC_action, $MJTC_actionfor='') {
        if( (is_array($MJTC_recevierEmail) && empty($MJTC_recevierEmail)) || (!is_array($MJTC_recevierEmail) && MJTC_majesticsupportphplib::MJTC_trim($MJTC_recevierEmail) == '') ){ // avoid the case of trying to send email to empty email.
            return;
        }

        $MJTC_enablesmtp = $this->checkSMTPEnableOrDisable($MJTC_senderEmail);
        if ($MJTC_enablesmtp) {
            $this->sendSMTPmail($MJTC_recevierEmail, $MJTC_subject, $MJTC_body, $MJTC_senderEmail, $MJTC_senderName, $MJTC_attachments, $MJTC_action, $MJTC_actionfor);
        }else{
            $this->sendEmailDefault($MJTC_recevierEmail, $MJTC_subject, $MJTC_body, $MJTC_senderEmail, $MJTC_senderName, $MJTC_attachments, $MJTC_action, $MJTC_actionfor);
        }

    }

    private function sendEmailDefault($MJTC_recevierEmail, $MJTC_subject, $MJTC_body, $MJTC_senderEmail, $MJTC_senderName, $MJTC_attachments, $MJTC_action, $MJTC_actionfor) {
	$MJTC_senderName = majesticsupport::$_config['title']; // site name
        /*
          $MJTC_attachments = array( WP_CONTENT_DIR . '/uploads/file_to_attach.zip' );
          $MJTC_headers = 'From: My Name <myname@example.com>' . "\r\n";
          wp_mail('test@example.org', 'subject', 'message', $MJTC_headers, $MJTC_attachments );

          $MJTC_action
          For which action of $mailfor you want to send the mail
          1 => New Ticket Create
          2 => Close Ticket
          3 => Delete Ticket
          4 => Reply Ticket (Admin/Staff Member)
          5 => Reply Ticket (Ticket member)
         */
        switch ($MJTC_action) {
            case 1:
                do_action('MJTC_beforeemailticketcreate', $MJTC_recevierEmail, $MJTC_subject, $MJTC_body, $MJTC_senderEmail);
                break;
            case 2:
                do_action('MJTC_beforeemailticketreply', $MJTC_recevierEmail, $MJTC_subject, $MJTC_body, $MJTC_senderEmail);
                break;
            case 3:
                do_action('MJTC_beforeemailticketclose', $MJTC_recevierEmail, $MJTC_subject, $MJTC_body, $MJTC_senderEmail);
                break;
            case 4:
                do_action('MJTC_beforeemailticketdelete', $MJTC_recevierEmail, $MJTC_subject, $MJTC_body, $MJTC_senderEmail);
                break;
        }
        if (!$MJTC_senderName)
            $MJTC_senderName = majesticsupport::$_config['title'];
        $MJTC_headers[] = 'From: ' . $MJTC_senderName . ' <' . $MJTC_senderEmail . '>' . "\r\n";
        $MJTC_headers = apply_filters('MJTC_emailcc_send_email_to_cc' , $MJTC_headers , $MJTC_actionfor); // eg $MJTC_actionfor = ticket-new
        add_filter('wp_mail_content_type', array($this,'ms_set_html_content_type'));
		if($MJTC_recevierEmail){
			if(!wp_mail($MJTC_recevierEmail, $MJTC_subject, $MJTC_body, $MJTC_headers, $MJTC_attachments)){
				if($GLOBALS['phpmailer']->ErrorInfo)
					MJTC_includer::MJTC_getModel('systemerror')->addSystemError($GLOBALS['phpmailer']->ErrorInfo);
			}
		}else{
			MJTC_includer::MJTC_getModel('systemerror')->addSystemError("No recipient email for ".$MJTC_subject);
		}
    }

    function ms_set_html_content_type() {
        return 'text/html';
    }

    private function sendSMTPmail($MJTC_recevierEmail, $MJTC_subject, $MJTC_body, $MJTC_senderEmail, $MJTC_senderName, $MJTC_attachments, $MJTC_action, $MJTC_actionfor){
        do_action('MJTC_aadon_send_smtp_mail',$MJTC_recevierEmail, $MJTC_subject, $MJTC_body, $MJTC_senderEmail, $MJTC_senderName, $MJTC_attachments, $MJTC_action, $MJTC_actionfor);
    }

    private function getSenderEmailAndName($MJTC_id) {
        if ($MJTC_id) {
            if (!is_numeric($MJTC_id))
                return false;
            $MJTC_query = "SELECT email.email,email.name
                        FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS ticket
                        JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_departments` AS department ON department.id = ticket.departmentid
                        JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_email` AS email ON email.id = department.emailid
                        WHERE ticket.id = " . esc_sql($MJTC_id);
            $MJTC_email = majesticsupport::$_db->get_row($MJTC_query);
            if (majesticsupport::$_db->last_error != null) {
                MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
            }
        } else {
            $MJTC_email = '';
        }
        if (empty($MJTC_email)) {
            $MJTC_email = $this->getDefaultSenderEmailAndName();
        }
        return $MJTC_email;
    }

    private function getDefaultSenderEmailAndName() {
        $MJTC_emailid = majesticsupport::$_config['default_alert_email'];
        if(!is_numeric($MJTC_emailid)) return false;
        $MJTC_query = "SELECT email,name FROM `" . majesticsupport::$_db->prefix . "mjtc_support_email` WHERE id = " . esc_sql($MJTC_emailid);
        $MJTC_email = majesticsupport::$_db->get_row($MJTC_query);
        return $MJTC_email;
    }

    private function getTemplateForEmail($templatefor, $MJTC_multiformid = '') {
        $MJTC_query = "SELECT * FROM `" . majesticsupport::$_db->prefix . "mjtc_support_emailtemplates` 
                  WHERE templatefor = '" . esc_sql($templatefor) . "'";

        // If multiformid is provided
        if (!empty($MJTC_multiformid)) {
            $MJTC_query .= " AND multiformid = " . esc_sql($MJTC_multiformid);
            $template = majesticsupport::$_db->get_row($MJTC_query);

            // If no form-specific template is found, fallback to default
            if (empty($template)) {
                $MJTC_query = "SELECT * FROM `" . majesticsupport::$_db->prefix . "mjtc_support_emailtemplates` 
                          WHERE templatefor = '" . esc_sql($templatefor) . "'
                          AND (multiformid IS NULL OR multiformid = '')";
                $template = majesticsupport::$_db->get_row($MJTC_query);
            }
        } else {
            // No multiformid passed — get default template
            $MJTC_query .= " AND (multiformid IS NULL OR multiformid = '')";
            $template = majesticsupport::$_db->get_row($MJTC_query);
        }

        // Handle DB error
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }

        return $template;
    }

    private function getRecordByTablenameAndId($MJTC_tablename, $MJTC_id) {
        if (!is_numeric($MJTC_id))
            return false;
        switch($MJTC_tablename){
            case 'mjtc_support_tickets':
                do_action('MJTC_get_mail_table_record_query');// to prepare any addon based query
                $MJTC_query = "SELECT ticket.*,department.departmentname,priority.priority ".majesticsupport::$_addon_query['select']
                    . " FROM `" . majesticsupport::$_db->prefix . $MJTC_tablename . "` AS ticket "
                    . " LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_departments` AS department ON department.id = ticket.departmentid "
                    . majesticsupport::$_addon_query['join']
                    . " LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_priorities` AS priority ON priority.id = ticket.priorityid "
                    . " WHERE ticket.id = " . esc_sql($MJTC_id);
                do_action('MJTC_reset_addon_query');
            break;
            default:
                $MJTC_query = "SELECT * FROM `" . majesticsupport::$_db->prefix . $MJTC_tablename . "` WHERE id = " . esc_sql($MJTC_id);
            break;
        }
        $MJTC_record = majesticsupport::$_db->get_row($MJTC_query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        return $MJTC_record;
    }

    function getEmails() {
        // Filter
        $MJTC_email = majesticsupport::$_search['email']['email'];
        $MJTC_inquery = '';
        if ($MJTC_email != null)
            $MJTC_inquery .= " WHERE email.email LIKE '%".esc_sql($MJTC_email)."%'";

        majesticsupport::$_data['filter']['email'] = $MJTC_email;

        // Pagination
        $MJTC_query = "SELECT COUNT(email.id)
                    FROM `" . majesticsupport::$_db->prefix . "mjtc_support_email` AS email ";
        $MJTC_query .= $MJTC_inquery;
        $total = majesticsupport::$_db->get_var($MJTC_query);
        majesticsupport::$_data[1] = MJTC_pagination::MJTC_getPagination($total);

        // Data
        $MJTC_query = " SELECT email.id, email.email, email.autoresponse, email.created, email.updated,email.status
                    FROM `" . majesticsupport::$_db->prefix . "mjtc_support_email` AS email ";
        $MJTC_query .= $MJTC_inquery;
        $MJTC_query .= " ORDER BY email.email DESC LIMIT " . MJTC_pagination::MJTC_getOffset() . ", " . MJTC_pagination::MJTC_getLimit();
        majesticsupport::$_data[0] = majesticsupport::$_db->get_results($MJTC_query);
        majesticsupport::$_data['email'] = $MJTC_email;
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        return;
    }

    function getAllEmailsForCombobox() {
        $MJTC_query = "SELECT id AS id, email AS text FROM `" . majesticsupport::$_db->prefix . "mjtc_support_email` WHERE status = 1 AND autoresponse = 1";
        $MJTC_emails = majesticsupport::$_db->get_results($MJTC_query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        return $MJTC_emails;
    }

    function getEmailForForm($MJTC_id) {
        if ($MJTC_id) {
            if (!is_numeric($MJTC_id))
                return false;
            $MJTC_query = "SELECT email.id, email.email, email.autoresponse, email.created, email.updated,email.status,email.smtpemailauth,email.smtphosttype,email.smtphost,email.smtpauthencation,email.name,email.password,email.smtpsecure,email.mailport
                        FROM `" . majesticsupport::$_db->prefix . "mjtc_support_email` AS email
                        WHERE email.id = " . esc_sql($MJTC_id);
            majesticsupport::$_data[0] = majesticsupport::$_db->get_row($MJTC_query);
            if(isset(majesticsupport::$_data[0]->password) && majesticsupport::$_data[0]->password != ''){
                majesticsupport::$_data[0]->password = MJTC_majesticsupportphplib::MJTC_safe_decoding(majesticsupport::$_data[0]->password);
            }
            if (majesticsupport::$_db->last_error != null) {
                MJTC_includer::MJTC_getModel('systemerror')->addSystemError(); // if there is an error add it to system errorrs
            }
        }
        return;
    }

    function storeEmail($MJTC_data) {
        if (!current_user_can('manage_options')) { //only admin can change it.
            return false;
        }
        if(!$MJTC_data['id'])
        if($this->checkAlreadyExist($MJTC_data['email'])){
            MJTC_message::MJTC_setMessage(esc_html(__('Email Already Exist', 'majestic-support')), 'error');
            return;
        }
        if ($MJTC_data['id'])
            $MJTC_data['updated'] = date_i18n('Y-m-d H:i:s');
        else{
            $MJTC_data['updated'] = date_i18n('Y-m-d H:i:s');
            $MJTC_data['created'] = date_i18n('Y-m-d H:i:s');
        }
        if(isset($MJTC_data['password']) && $MJTC_data['password'] != ''){
            $MJTC_data['password'] = MJTC_majesticsupportphplib::MJTC_safe_encoding($MJTC_data['password']);
        }

        $MJTC_data = majesticsupport::MJTC_sanitizeData($MJTC_data); // MJTC_sanitizeData() function uses wordpress santize functions

        $MJTC_row = MJTC_includer::MJTC_getTable('email');

        $MJTC_data = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($MJTC_data);// remove slashes with quotes.
        $MJTC_error = 0;
        if (!$MJTC_row->bind($MJTC_data)) {
            $MJTC_error = 1;
        }
        if (!$MJTC_row->store()) {
            $MJTC_error = 1;
        }

        if ($MJTC_error == 0) {
            MJTC_message::MJTC_setMessage(esc_html(__('The email has been stored', 'majestic-support')), 'updated');
        } else {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError(); // if there is an error add it to system errorrs
            MJTC_message::MJTC_setMessage(esc_html(__('The email has not been stored', 'majestic-support')), 'error');
        }
        return;
    }

    function checkAlreadyExist($MJTC_email){
        $MJTC_query = "SELECT COUNT(id) FROM`" . majesticsupport::$_db->prefix . "mjtc_support_email`  WHERE email = '".esc_sql($MJTC_email)."'";
        $MJTC_result = majesticsupport::$_db->get_var($MJTC_query);
        if($MJTC_result > 0)
            return true;
        else
            return false;
    }

    function removeEmail($MJTC_id) {
        if (!is_numeric($MJTC_id))
            return false;
        if ($this->canRemoveEmail($MJTC_id)) {
            $MJTC_row = MJTC_includer::MJTC_getTable('email');
            if ($MJTC_row->delete($MJTC_id)) {
                MJTC_message::MJTC_setMessage(esc_html(__('The email has been deleted', 'majestic-support')), 'updated');
            } else {
                MJTC_includer::MJTC_getModel('systemerror')->addSystemError(); // if there is an error add it to system errorrs
                MJTC_message::MJTC_setMessage(esc_html(__('The email has not been deleted', 'majestic-support')), 'error');
            }
        } else {
            MJTC_message::MJTC_setMessage(esc_html(__('Email','majestic-support')).' '. esc_html(__('in use cannot be deleted', 'majestic-support')), 'error');
        }
        return;
    }

    private function canRemoveEmail($MJTC_id) {
        if (!is_numeric($MJTC_id))
            return false;
        $MJTC_query = "SELECT (
                        (SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_departments` WHERE emailid = " . esc_sql($MJTC_id) . ")
                        + (SELECT COUNT(*) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_config` WHERE configname = 'default_alert_email' AND configvalue = " . esc_sql($MJTC_id) . ")
                        + (SELECT COUNT(*) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_config` WHERE configname = 'default_admin_email' AND configvalue = " . esc_sql($MJTC_id) . ")
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

    function getEmailForDepartment() {
        $MJTC_query = "SELECT id, email AS text FROM `" . majesticsupport::$_db->prefix . "mjtc_support_email`";
        $MJTC_emails = majesticsupport::$_db->get_results($MJTC_query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        return $MJTC_emails;
    }

    function getEmailById($MJTC_id) {
        if (!is_numeric($MJTC_id))
            return false;
        $MJTC_query = "SELECT email  FROM `" . majesticsupport::$_db->prefix . "mjtc_support_email` WHERE id = " . esc_sql($MJTC_id);
        $MJTC_email = majesticsupport::$_db->get_var($MJTC_query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        return $MJTC_email;
    }

    function checkSMTPEnableOrDisable($MJTC_senderemail){
        if(!in_array('smtp', majesticsupport::$_active_addons)){
            return false;
        }
        if(!is_string($MJTC_senderemail))
            return false;
        $MJTC_query = "SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_email` WHERE email = '".esc_sql($MJTC_senderemail). "' AND smtpemailauth = 1"; // 1 For smtp 0 for default
        $total = majesticsupport::$_db->get_var($MJTC_query);
        if($total > 0){
            return true;
        }else{
            return false;
        }
    }

    function getSMTPEmailConfig($MJTC_senderemail){
        $MJTC_query = "SELECT * FROM  `" . majesticsupport::$_db->prefix . "mjtc_support_email` WHERE email = '".esc_sql($MJTC_senderemail)."'";
        $MJTC_emailconfig = majesticsupport::$_db->get_row($MJTC_query);
        return $MJTC_emailconfig;
    }

    function sendTestEmail(){
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'send-test-email') ) {
            die( 'Security check Failed' );
        }
        $MJTC_hosttype = MJTC_request::MJTC_getVar('hosttype');
        $MJTC_hostname = MJTC_request::MJTC_getVar('hostname');
        $MJTC_ssl = MJTC_request::MJTC_getVar('ssl');
        $MJTC_hostportnumber = MJTC_request::MJTC_getVar('hostportnumber');
        $MJTC_emailaddress = MJTC_request::MJTC_getVar('emailaddress');
        $MJTC_password = MJTC_request::MJTC_getVar('password');
        $MJTC_smtpauthencation = MJTC_request::MJTC_getVar('smtpauthencation');

        if(get_bloginfo('version') >= 5){
            require_once ABSPATH . WPINC . '/PHPMailer/PHPMailer.php';
            require_once ABSPATH . WPINC . '/PHPMailer/SMTP.php';
            require_once ABSPATH . WPINC . '/PHPMailer/Exception.php';
            $mail = new PHPMailer\PHPMailer\PHPMailer( true );
        } else {
            require_once ABSPATH . WPINC . '/class-phpmailer.php';
            require_once ABSPATH . WPINC . '/class-smtp.php';
            $mail = new PHPMailer(true);
        }
        try {

            $mail->isSMTP();
            $mail->Host = $MJTC_hostname;
            $mail->SMTPAuth = $MJTC_smtpauthencation;
            $mail->Username = $MJTC_emailaddress;
            $mail->Password = $MJTC_password;
            if($MJTC_ssl == 0){
                $mail->SMTPSecure = 'ssl';
            }else{
                $mail->SMTPSecure = 'tls';
            }
            $mail->Port = $MJTC_hostportnumber;
            //Recipients
            $mail->setFrom($MJTC_emailaddress, majesticsupport::$_config['title']);
            $MJTC_adminEmailid = majesticsupport::$_config['default_admin_email'];
            $MJTC_adminEmail = $this->getEmailById($MJTC_adminEmailid);

            $mail->addAddress($MJTC_adminEmail,'Administrator');

            $mail->isHTML(true);
            $mail->Subject = 'SMTP Test email From :'.site_url();
            $mail->Body    = 'This is body text for SMTP test email from :'.site_url();
            $mail->send();
            $MJTC_error['text'] = __('Test email has been sent on : ', 'majestic-support'). $MJTC_adminEmail;
            $MJTC_error['type'] = 0;
        } catch (Exception $MJTC_e) {
            $MJTC_error['text'] = __('Message could not be sent. Mailer Error: ', 'majestic-support'). $mail->ErrorInfo;
            $MJTC_error['type'] = 1;
        }
        return wp_json_encode($MJTC_error);;

    }

    function getAdminSearchFormDataEmails(){
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'emails') ) {
            die( 'Security check Failed' );
        }
        $ms_search_array = array();
        $ms_search_array['email'] = MJTC_request::MJTC_getVar('email');
        $ms_search_array['search_from_email'] = 1;
        return $ms_search_array;
    }

    private function getTicketReplyHistory($MJTC_id) {
        $MJTC_html = '';
        if ($MJTC_id) {
            if (!is_numeric($MJTC_id))
                return false;
            $MJTC_query = "SELECT replies.*,replies.id AS replyid,tickets.id 
                    FROM `" . majesticsupport::$_db->prefix . "mjtc_support_replies` AS replies
                    JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` AS tickets ON  replies.ticketid = tickets.id
                    WHERE tickets.id = " . esc_sql($MJTC_id) . " ORDER By replies.id DESC";
            $MJTC_replies = majesticsupport::$_db->get_results($MJTC_query);
            foreach ($MJTC_replies as $MJTC_key => $MJTC_reply) {
                if ($MJTC_key == 0) {
                    $MJTC_html .= '<div style="float:left;width:100%;padding:15px 0;border-bottom:1px solid #e0e1e0;margin-bottom:20px;">
                                <div style="font-weight:bold;font-size:18px;margin-bottom:5px;color:#4b4b4d;">'. esc_html(__('Ticket History','majestic-support')).'</div>';
                }
                $MJTC_html .= '<div style="float:left;width:100%;padding:10px 15px;border:1px solid #e0e1e0;background:#f8fafc;box-sizing:border-box;margin:10px 0;">
                            <div style="float:left;width:100%;margin:10px 0;">
                                <span style="float:left;width:auto;display:inline-block;color:#4b4b4d;font-size:14px;font-weight: 600;">'. esc_html(__('Reply By','majestic-support')).':&nbsp;</span>
                                <span style="float:left;width:auto;display:inline-block;color:#727376;">'.esc_html($MJTC_reply->name).'</span>
                            </div>
                            <div style="float:left;width:100%;margin:10px 0 0;">
                                <span style="float:left;width:auto;display:inline-block;color:#4b4b4d;font-size:14px;font-weight: 600;">'. esc_html(__('Date','majestic-support')).':&nbsp;</span>
                                <span style="float:left;width:auto;display:inline-block;color:#727376;">'.esc_html($MJTC_reply->created).'</span>
                            </div>
                            <div style="float:left;width:100%;">
                                <span style="float:left;width:auto;display:inline-block;color:#727376;">'.esc_html($MJTC_reply->message).'</span>
                            </div>
                        </div>';
            }
            if (isset($MJTC_html)) {
                $MJTC_html .= '</div>';
            }
            
        }
        return $MJTC_html;
    }
}

?>
