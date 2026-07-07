<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_departmentModel {

    function getDepartments() {
        // Filter
        $MJTC_isadmin = is_admin();
        $MJTC_deptname = ($MJTC_isadmin) ? 'departmentname' : 'ms-dept';

        $MJTC_departmentname = isset(majesticsupport::$_search['department']) ? majesticsupport::$_search['department']['departmentname'] : '';
        $MJTC_pagesize = isset(majesticsupport::$_search['department']) ? majesticsupport::$_search['department']['pagesize'] : '';

        $MJTC_departmentname = majesticsupport::parseSpaces($MJTC_departmentname);
        $MJTC_inquery = '';
        if ($MJTC_departmentname != null)
            $MJTC_inquery .= majesticsupport::$_db->prepare(" WHERE department.departmentname LIKE %s", '%' . majesticsupport::$_db->esc_like($MJTC_departmentname) . '%');

        majesticsupport::$_data['filter'][$MJTC_deptname] = $MJTC_departmentname;
        majesticsupport::$_data['filter']['pagesize'] = $MJTC_pagesize;

        // Pagination
        if($MJTC_pagesize){
            MJTC_pagination::MJTC_setLimit($MJTC_pagesize);
        }
        $MJTC_query = "SELECT COUNT(`id`) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_departments` AS department";
        $MJTC_query .= $MJTC_inquery;
        $total = majesticsupport::$_db->get_var($MJTC_query);
        majesticsupport::$_data['total'] = $total;
        majesticsupport::$_data[1] = MJTC_pagination::MJTC_getPagination($total,'departments');

        // Data
        $MJTC_query = "SELECT department.*,email.email AS outgoingemail
                    FROM `" . majesticsupport::$_db->prefix . "mjtc_support_departments` AS department
                    LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_email` AS email ON email.id = department.emailid ";
        $MJTC_query .= $MJTC_inquery;
        $MJTC_query .= " ORDER BY department.ordering ASC,department.departmentname ASC LIMIT " . MJTC_pagination::MJTC_getOffset() . ", " . MJTC_pagination::MJTC_getLimit();
        majesticsupport::$_data[0] = majesticsupport::$_db->get_results($MJTC_query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        return;
    }

    function getDepartmentForForm($MJTC_id) {
        if ($MJTC_id) {
            if (!is_numeric($MJTC_id))
                return false;
            $MJTC_query = majesticsupport::$_db->prepare("SELECT department.*,email.email AS outgoingemail
                        FROM `" . majesticsupport::$_db->prefix . "mjtc_support_departments` AS department
                        JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_email` AS email ON email.id = department.emailid
                        WHERE department.id = %d", absint($MJTC_id));
            majesticsupport::$_data[0] = majesticsupport::$_db->get_row($MJTC_query);
            if (majesticsupport::$_db->last_error != null) {
                MJTC_includer::MJTC_getModel('systemerror')->addSystemError(); // if there is an error add it to system errorrs
            }
        }
        return;
    }

    private function getNextOrdering() {
        $MJTC_query = "SELECT MAX(ordering) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_departments`";
        $MJTC_result = majesticsupport::$_db->get_var($MJTC_query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        return $MJTC_result + 1;
    }

    function storeDepartment($MJTC_data) {
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        $MJTC_id = MJTC_request::MJTC_getVar('id');
        if (! wp_verify_nonce( $MJTC_nonce, 'save-department-'.$MJTC_id) ) {
            die( 'Security check Failed' );
        }
        if ( in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) {
            $MJTC_task_allow = ($MJTC_data['id'] == '') ? 'Add Department' : 'Edit Department';
            $MJTC_allowed = MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForTask($MJTC_task_allow);
            if ($MJTC_allowed != true) {
                MJTC_message::MJTC_setMessage(esc_html(__('You are not allowed', 'majestic-support')) . ' ' . esc_html(majesticsupport::MJTC_getVarValue($MJTC_task_allow)), 'error');
                return;
            }
        } else if (!current_user_can('manage_options')) { //only admin can change it.
            return false;
        }

        if($MJTC_data['sendmail'] == 1 && is_numeric($MJTC_data['emailid'])){
            if ( in_array('emailpiping',majesticsupport::$_active_addons)) {
                $MJTC_query = "SELECT emailaddress FROM `" . majesticsupport::$_db->prefix . "mjtc_support_ticketsemail` ";
                $MJTC_emailaddresses = majesticsupport::$_db->get_results($MJTC_query);
            }else{
                $MJTC_emailaddresses = array();
            }
            $MJTC_query = majesticsupport::$_db->prepare("SELECT email FROM `" . majesticsupport::$_db->prefix . "mjtc_support_email`
                WHERE id = %d", absint($MJTC_data['emailid']));
            $MJTC_email = majesticsupport::$_db->get_var($MJTC_query);

            foreach ($MJTC_emailaddresses as $MJTC_edata) {
                if($MJTC_email == $MJTC_edata->emailaddress){
                    MJTC_message::MJTC_setMessage(esc_html(__('You cannot use this email, it is used in email piping', 'majestic-support')), 'error');
                    return;
                }
            }
        }

        if ($MJTC_data['id'])
            $MJTC_data['updated'] = date_i18n('Y-m-d H:i:s');
        else
            $MJTC_data['created'] = date_i18n('Y-m-d H:i:s');

        $MJTC_data = majesticsupport::MJTC_sanitizeData($MJTC_data); // MJTC_sanitizeData() function uses wordpress santize functions
        $MJTC_data['departmentsignature'] = MJTC_includer::MJTC_getModel('majesticsupport')->getSanitizedEditorData($_POST['departmentsignature']);

        if (!$MJTC_data['id']) { //new
            $MJTC_data['ordering'] = $this->getNextOrdering();
        }
        if (isset($MJTC_data['canappendsignature'])) { //new
            $MJTC_data['canappendsignature'] = 1;
        }else{
            $MJTC_data['canappendsignature'] = 0;
        }

        $MJTC_row = MJTC_includer::MJTC_getTable('departments');

        $MJTC_data = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($MJTC_data);// remove slashes with quotes.
        $MJTC_error = 0;
        if (!$MJTC_row->bind($MJTC_data)) {
            $MJTC_error = 1;
        }
        if (!$MJTC_row->store()) {
            $MJTC_error = 1;
        }

        if ($MJTC_error == 0) {
            if ($MJTC_row->isdefault) {
                if ($MJTC_row->isdefault == 1) {
                    $this->changeDefault($MJTC_row->id, 0);
                } elseif ($MJTC_row->isdefault == 2) {
                    $this->changeDefault($MJTC_row->id, -1);
                }
            }
            MJTC_message::MJTC_setMessage(esc_html(__('The department has been stored', 'majestic-support')), 'updated');
        } else {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError(); // if there is an error add it to system errorrs
            MJTC_message::MJTC_setMessage(esc_html(__('The department has not been stored', 'majestic-support')), 'error');
        }

        return;
    }

    function setOrdering($MJTC_id) {
        if (!is_numeric($MJTC_id))
            return false;
        $MJTC_order = MJTC_request::MJTC_getVar('order', 'get');
        if ($MJTC_order == 'down') {
            $MJTC_order = ">";
            $MJTC_direction = "ASC";
        } else {
            $MJTC_order = "<";
            $MJTC_direction = "DESC";
        }
        $MJTC_query = majesticsupport::$_db->prepare("SELECT t.ordering,t.id,t2.ordering AS ordering2 FROM `" . majesticsupport::$_db->prefix . "mjtc_support_departments` AS t,`" . majesticsupport::$_db->prefix . "mjtc_support_departments` AS t2 WHERE t.ordering $MJTC_order t2.ordering AND t2.id = %d ORDER BY t.ordering $MJTC_direction LIMIT 1", absint($MJTC_id));
        $MJTC_result = majesticsupport::$_db->get_row($MJTC_query);

        $MJTC_row = MJTC_includer::MJTC_getTable('departments');
        if ($MJTC_row->update(array('id' => $MJTC_id, 'ordering' => $MJTC_result->ordering)) && $MJTC_row->update(array('id' => $MJTC_result->id, 'ordering' => $MJTC_result->ordering2))) {
            MJTC_message::MJTC_setMessage(esc_html(__('Departments','majestic-support')).' '.esc_html(__('ordering has been changed', 'majestic-support')), 'updated');
        } else {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError(); // if there is an error add it to system errorrs
            MJTC_message::MJTC_setMessage(esc_html(__('Departments','majestic-support')).' '. esc_html(__('ordering has not changed', 'majestic-support')), 'error');
        }
        return;
    }

    function removeDepartment($MJTC_id) {
        if (!is_numeric($MJTC_id))
            return false;
        if ( in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) {
            $MJTC_allowed = MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForTask('Delete Department');
            if ($MJTC_allowed != true) {
                MJTC_message::MJTC_setMessage(esc_html(__('You are not allowed', 'majestic-support')), 'error');
                return;
            }
        }
        if ($this->canRemoveDepartment($MJTC_id)) {

            $MJTC_row = MJTC_includer::MJTC_getTable('departments');
            if ($MJTC_row->delete($MJTC_id)) {
                if(in_array('agent',majesticsupport::$_active_addons)){
                    majesticsupport::$_db->delete(
                        majesticsupport::$_db->prefix . 'mjtc_support_acl_role_access_departments',
                        array('departmentid' => absint($MJTC_id)),
                        array('%d')
                    );
                }
                MJTC_message::MJTC_setMessage(esc_html(__('The department has been deleted', 'majestic-support')), 'updated');
            } else {
                MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
                MJTC_message::MJTC_setMessage(esc_html(__('The department has not been deleted', 'majestic-support')), 'error');
            }
        } else {
            MJTC_message::MJTC_setMessage(esc_html(__('The department in use cannot be delete', 'majestic-support')), 'error');
        }
        return;
    }

    private function canRemoveDepartment($MJTC_id) {
        if (!is_numeric($MJTC_id))
            return false;
        $MJTC_query = "SELECT (
                    (SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE departmentid = " . absint($MJTC_id) . ")
                    + (SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_departments` WHERE id = " . absint($MJTC_id) . " AND isdefault = 1) ";

                    if(in_array('agent', majesticsupport::$_active_addons)){
                        $MJTC_query .= " + (SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_acl_user_access_departments` WHERE departmentid = " . absint($MJTC_id) . ") ";
                    }

                    if(in_array('helptopic', majesticsupport::$_active_addons)){
                        $MJTC_query .= " + (SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_help_topics` WHERE departmentid = " . absint($MJTC_id) . ") ";
                    }

                    if(in_array('cannedresponses', majesticsupport::$_active_addons)){
                        $MJTC_query .= " + (SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_department_message_premade` WHERE departmentid = " . absint($MJTC_id) . ")";
                    }

                    $MJTC_query .= " ) AS total";
        $MJTC_result = majesticsupport::$_db->get_var($MJTC_query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        if ($MJTC_result == 0)
            return true;
        else
            return false;
    }

    function getDepartmentForCombobox() {
        $MJTC_query = "SELECT id, departmentname AS text FROM `" . majesticsupport::$_db->prefix . "mjtc_support_departments` WHERE status = 1";
        $MJTC_query .= " ORDER BY ordering";
        $MJTC_list = majesticsupport::$_db->get_results($MJTC_query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        return $MJTC_list;
    }

    function changeStatus($MJTC_id) {
        if (!is_numeric($MJTC_id))
            return false;
        $MJTC_query = majesticsupport::$_db->prepare("SELECT status FROM `" . majesticsupport::$_db->prefix . "mjtc_support_departments` WHERE id=%d", absint($MJTC_id));
           $MJTC_status = majesticsupport::$_db->get_var($MJTC_query);
       $MJTC_status = 1 - $MJTC_status;

       $MJTC_row = MJTC_includer::MJTC_getTable('departments');
       if ($MJTC_row->update(array('id' => $MJTC_id, 'status' => $MJTC_status))) {
            MJTC_message::MJTC_setMessage(esc_html(__('Department','majestic-support')).' '. esc_html(__('status has been changed', 'majestic-support')), 'updated');
        } else {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError(); // if there is an error add it to system errorrs
            MJTC_message::MJTC_setMessage(esc_html(__('Department','majestic-support')).' '. esc_html(__('status has not been changed', 'majestic-support')), 'error');
        }
        return;
    }

    function changeDefault($MJTC_id,$MJTC_default) {
        if (!is_numeric($MJTC_id))
            return false;

        $MJTC_query = majesticsupport::$_db->prepare("UPDATE `" . majesticsupport::$_db->prefix . "mjtc_support_departments` SET isdefault = 0 WHERE id != %d", absint($MJTC_id));
        majesticsupport::$_db->query($MJTC_query);

        $MJTC_query = majesticsupport::$_db->prepare("UPDATE `" . majesticsupport::$_db->prefix . "mjtc_support_departments` SET isdefault = 1 - %d WHERE id=%d", absint($MJTC_default), absint($MJTC_id));
        majesticsupport::$_db->query($MJTC_query);

        if (majesticsupport::$_db->last_error == null) {
            MJTC_message::MJTC_setMessage(esc_html(__('Department','majestic-support')).' '. esc_html(__('default has been changed', 'majestic-support')), 'updated');
        } else {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError(); // if there is an error add it to system errorrs
            MJTC_message::MJTC_setMessage(esc_html(__('Department','majestic-support')).' '. esc_html(__('default has not been changed', 'majestic-support')), 'error');
        }
        return;
    }

    function getHelpTopicByDepartment() {
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'get-help-topic-by-department') ) {
            die( 'Security check Failed' );
        }
        if(!in_array('helptopic', majesticsupport::$_active_addons)){
            return;
        }

        $MJTC_departmentid = MJTC_request::MJTC_getVar('val');
        if (!is_numeric($MJTC_departmentid)){
            return false;
        }

        $MJTC_query = majesticsupport::$_db->prepare("SELECT id, topic AS text FROM `" . majesticsupport::$_db->prefix . "mjtc_support_help_topics` WHERE status = 1 AND departmentid = %d ORDER BY ordering ASC", absint($MJTC_departmentid));
        $MJTC_list = majesticsupport::$_db->get_results($MJTC_query);

        $MJTC_query = "SELECT required FROM `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` WHERE field='helptopic'";
        $MJTC_isRequired = majesticsupport::$_db->get_var($MJTC_query);

        $MJTC_combobox = false;
        if(!empty($MJTC_list)){
            $MJTC_combobox = MJTC_formfield::MJTC_select('helptopicid', $MJTC_list, '', esc_html(__('Select Help Topic', 'majestic-support')), array('class' => 'inputbox mjtc-support-select-field mjtc-form-select-field','data-validation'=>($MJTC_isRequired ? 'required' : '')));
        }
        return $MJTC_combobox;
    }

    function getPremadeByDepartment() {
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'get-premade-by-department') ) {
            die( 'Security check Failed' );
        }
        if(!in_array('cannedresponses', majesticsupport::$_active_addons)){
            return false;
        }
        $MJTC_departmentid = MJTC_request::MJTC_getVar('val');
        if (!is_numeric($MJTC_departmentid))
            return false;
        $MJTC_query = majesticsupport::$_db->prepare("SELECT id, title AS text FROM `" . majesticsupport::$_db->prefix . "mjtc_support_department_message_premade` WHERE status = 1 AND departmentid = %d", absint($MJTC_departmentid));
        $MJTC_query .= " ORDER BY title ASC ";
        $MJTC_list = majesticsupport::$_db->get_results($MJTC_query);
        $MJTC_combobox = false;
        $MJTC_html = '';
        if(!empty($MJTC_list)){
            foreach($MJTC_list as $MJTC_premade){
                $MJTC_html .= '<div class="mjtc-form-perm-msg" onclick="getpremade('.esc_js($MJTC_premade->id).');">
                    <a href="javascript:void(0)" title="'. esc_html(__('Premade response','majestic-support')).'">'.wp_kses($MJTC_premade->text, MJTC_ALLOWED_TAGS).'</a>
                </div>';


            }
        }else{
            $MJTC_html = '<div class="mjtc-form-perm-msg">
                <div class = "permade-no-rec">'. esc_html(__('No Record Found','majestic-support')) .'</div>
            </div>';
        }

        return MJTC_majesticsupportphplib::MJTC_htmlentities($MJTC_html);
    }

    function getSignatureByID($MJTC_id) {
        if (!is_numeric($MJTC_id))
            return false;
        $MJTC_query = majesticsupport::$_db->prepare("SELECT departmentsignature FROM `" . majesticsupport::$_db->prefix . "mjtc_support_departments` WHERE id = %d", absint($MJTC_id));
        $MJTC_signature = majesticsupport::$_db->get_var($MJTC_query);
        return $MJTC_signature;
    }

    function getDepartmentById($MJTC_id) {
        if (!is_numeric($MJTC_id))
            return false;
        $MJTC_query = majesticsupport::$_db->prepare("SELECT departmentname FROM `" . majesticsupport::$_db->prefix . "mjtc_support_departments` WHERE id = %d", absint($MJTC_id));
        $MJTC_departmentname = majesticsupport::$_db->get_var($MJTC_query);
        return $MJTC_departmentname;
    }

    function getDefaultDepartmentID() {
        $MJTC_query = "SELECT id FROM `" . majesticsupport::$_db->prefix . "mjtc_support_departments` WHERE isdefault = 1 OR isdefault = 2";
        $MJTC_departmentid = majesticsupport::$_db->get_var($MJTC_query);
        return $MJTC_departmentid;
    }

    function getDepartmentIDForAutoAssign() {
        $MJTC_query = "SELECT id FROM `" . majesticsupport::$_db->prefix . "mjtc_support_departments` WHERE isdefault = 2 AND status = 1";
        $MJTC_departmentid = majesticsupport::$_db->get_var($MJTC_query);
        return $MJTC_departmentid;
    }

    function getAdminDepartmentSearchFormData(){
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'departments') ) {
            die( 'Security check Failed' );
        }
        $ms_search_array = array();
        $MJTC_isadmin = is_admin();
        $MJTC_deptname = ($MJTC_isadmin) ? 'departmentname' : 'ms-dept';
        $MJTC_departmentname = MJTC_request::MJTC_getVar($MJTC_deptname);
        if ($MJTC_departmentname != '') {
            $ms_search_array['departmentname'] = MJTC_majesticsupportphplib::MJTC_addslashes(trim($MJTC_departmentname));
        } else {
            $ms_search_array['departmentname'] = '';
        }
        $ms_search_array['pagesize'] = absint(MJTC_request::MJTC_getVar('pagesize'));
        $ms_search_array['search_from_department'] = 1;
        return $ms_search_array;
    }

}

?>
