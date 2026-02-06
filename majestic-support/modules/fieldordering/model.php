<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_fieldorderingModel {

    function getFieldOrderingForList($fieldfor) {
        if(!is_numeric($fieldfor)){
            return false;
        }
        $formid = majesticsupport::$_data['formid'];
        if (isset($formid) && $formid != null) {
            $inquery = " AND multiformid = ".esc_sql($formid);
        }
    	else{
            $inquery = " AND multiformid = ".esc_sql(MJTC_includer::MJTC_getModel('ticket')->getDefaultMultiFormId());
    	}

        // Data
        $query = "SELECT * FROM `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` WHERE fieldfor = ".esc_sql($fieldfor);
        $query .= $inquery." ORDER BY ordering ";

        majesticsupport::$_data[0] = majesticsupport::$_db->get_results($query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        return;
    }

    function changePublishStatus($id, $status) {
        if (!is_numeric($id))
            return false;
        if ($status == 'publish') {
            $query = "UPDATE `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` SET published = 1 WHERE id = " . esc_sql($id) . " AND cannotunpublish = 0";
            majesticsupport::$_db->query($query);
            if (majesticsupport::$_db->last_error != null) {
                MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
            }
            MJTC_message::MJTC_setMessage(esc_html(__('Field mark as published', 'majestic-support')),'updated');
        } elseif ($status == 'unpublish') {
            $query = "UPDATE `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` SET published = 0 WHERE id = " . esc_sql($id) . " AND cannotunpublish = 0";
            majesticsupport::$_db->query($query);
            if (majesticsupport::$_db->last_error != null) {
                MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
            }
            MJTC_message::MJTC_setMessage(esc_html(__('Field mark as unpublished', 'majestic-support')),'updated');
        }
        return;
    }

    function changeVisitorPublishStatus($id, $status) {
        if (!is_numeric($id))
            return false;
        if ($status == 'publish') {
            $query = "SELECT adminonly FROM " . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering WHERE id = " . esc_sql($id);
            $adminonly = majesticsupport::$_db->get_var($query);
            if(!empty($adminonly)){
                MJTC_message::MJTC_setMessage(esc_html(__('Field cannot be mark as published', 'majestic-support')),'error');
            }else{
                $query = "UPDATE `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` SET isvisitorpublished = 1 WHERE id = " . esc_sql($id) . " AND cannotunpublish = 0";
                majesticsupport::$_db->query($query);
                if (majesticsupport::$_db->last_error != null) {
                    MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
                }
                MJTC_message::MJTC_setMessage(esc_html(__('Field mark as published', 'majestic-support')),'updated');
            }
        } elseif ($status == 'unpublish') {
            $query = "UPDATE `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` SET isvisitorpublished = 0 WHERE id = " . esc_sql($id) . " AND cannotunpublish = 0";
            majesticsupport::$_db->query($query);
            if (majesticsupport::$_db->last_error != null) {
                MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
            }
            MJTC_message::MJTC_setMessage(esc_html(__('Field mark as unpublished', 'majestic-support')),'updated');
        }
        return;
    }

    function changeRequiredStatus($id, $status) {
        if (!is_numeric($id))
            return false;

        if ($status == 'required') {
            $query = "UPDATE `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` SET required = 1 WHERE id = " . esc_sql($id) . " AND cannotunpublish = 0";
            majesticsupport::$_db->query($query);
            if (majesticsupport::$_db->last_error != null) {
                MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
            }
            MJTC_message::MJTC_setMessage(esc_html(__('Field mark as required', 'majestic-support')),'updated');
        } elseif ($status == 'unrequired') {
            $query = "UPDATE `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` SET required = 0 WHERE id = " . esc_sql($id) . " AND cannotunpublish = 0";
            majesticsupport::$_db->query($query);
            if (majesticsupport::$_db->last_error != null) {
                MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
            }
            MJTC_message::MJTC_setMessage(esc_html(__('Field mark as not required', 'majestic-support')),'updated');
        }
        return;
    }

    function changeOrder($id, $action) {
        if (!is_numeric($id))
            return false;
        if ($action == 'down') {
            $query = "UPDATE `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` AS f1, `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` AS f2
                        SET f1.ordering = f1.ordering - 1 WHERE f1.ordering = f2.ordering + 1 AND f1.fieldfor = f2.fieldfor
                        AND f2.id = " . esc_sql($id);
            majesticsupport::$_db->query($query);
            $query = " UPDATE `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` SET ordering = ordering + 1 WHERE id = " . esc_sql($id);
            majesticsupport::$_db->query($query);
            MJTC_message::MJTC_setMessage(esc_html(__('Field ordering down', 'majestic-support')),'updated');
        } elseif ($action == 'up') {
            $query = "UPDATE `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` AS f1, `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` AS f2 SET f1.ordering = f1.ordering + 1
                        WHERE f1.ordering = f2.ordering - 1 AND f1.fieldfor = f2.fieldfor AND f2.id = " . esc_sql($id);
            majesticsupport::$_db->query($query);
            $query = " UPDATE `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` SET ordering = ordering - 1 WHERE id = " . esc_sql($id);
            majesticsupport::$_db->query($query);
            MJTC_message::MJTC_setMessage(esc_html(__('Field ordering up', 'majestic-support')),'updated');
        }
        return;
    }

    function getFieldsOrderingforForm($fieldfor,$formid='') {
        if (!is_numeric($fieldfor))
            return false;
        if (MJTC_includer::MJTC_getObjectClass('user')->MJTC_isguest()) {
            $published = ' isvisitorpublished = 1 ';
        } else {
            $published = ' published = 1 ';
        }
	    if(!isset($formid) || $formid==''){
		    $formid = MJTC_includer::MJTC_getModel('ticket')->getDefaultMultiFormId();
	    }
        if(!is_numeric($formid)) return false;
        // admin only check
        $adminonly = '';
        if ($fieldfor == 1) {
            if( in_array('agent',majesticsupport::$_active_addons) ){
                $agent = MJTC_includer::MJTC_getModel('agent')->isUserStaff();
            }else{
                $agent = false;
            }
            if(!is_admin() && !$agent){
                $adminonly = ' AND adminonly != 1 ';
            }
        }
        $query = "SELECT  * FROM `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` WHERE ".$published." AND fieldfor =  " . esc_sql($fieldfor);
        if ($fieldfor == 1) {
            $query .= " AND multiformid =  " . esc_sql($formid);
        }
        $query .=  esc_sql($adminonly) . " ORDER BY ordering ";
        majesticsupport::$_data['fieldordering'] = majesticsupport::$_db->get_results($query);
        return;
    }

    function checkIsFieldRequired($field,$formid='') {
        if(!isset($formid) || $formid==''){
            $formid = MJTC_includer::MJTC_getModel('ticket')->getDefaultMultiFormId();
        }
        if (MJTC_includer::MJTC_getObjectClass('user')->MJTC_isguest()) {
            $published = ' isvisitorpublished = 1 ';
        } else {
            $published = ' published = 1 ';
        }
        $query = "SELECT required FROM `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` WHERE ".$published." AND fieldfor =  1 AND  field =  '".esc_sql($field)."' AND multiformid =  " . esc_sql($formid);
        $required = majesticsupport::$_db->get_var($query);
        return $required;
    }

    function storeUserField($MJTC_data) {
        if (empty($MJTC_data)) {
            return false;
        }
        if (!current_user_can('manage_options')) { //only admin can change it.
            return false;
        }
        $MJTC_data = majesticsupport::MJTC_sanitizeData($MJTC_data); // MJTC_sanitizeData() function uses wordpress santize functions
        if(!is_numeric($MJTC_data['fieldfor'])) return false;
        if ($MJTC_data['isuserfield'] == 1) {
            // value to add as field ordering
            if ($MJTC_data['id'] == '') { // only for new
                $query = "SELECT max(ordering) FROM " . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering WHERE fieldfor=".esc_sql($MJTC_data['fieldfor']);
                $var = majesticsupport::$_db->get_var($query);
                $MJTC_data['ordering'] = $var + 1;
                if(isset($MJTC_data['userfieldtype']) && ($MJTC_data['userfieldtype'] == 'file' || $MJTC_data['userfieldtype'] == 'termsandconditions' ) ){
                    $MJTC_data['cannotsearch'] = 1;
                    $MJTC_data['cannotshowonlisting'] = 1;
                }else{
                    $MJTC_data['cannotshowonlisting'] = 0;
                    $MJTC_data['cannotsearch'] = 0;
                }
                $query = "SELECT max(id) FROM " . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering ";
                $var = majesticsupport::$_db->get_var($query);
                $var = $var + 1;
                $fieldname = 'ufield_'.esc_attr($var);
            }else{
                $fieldname = !empty($MJTC_data['field']) ? $MJTC_data['field'] : '';
            }
            if ($MJTC_data['userfieldtype'] == 'termsandconditions') { // only for terms and conditions
                $MJTC_data['required'] = 1;
            }

            $params = array();
            //code for depandetn field
            if (isset($MJTC_data['userfieldtype']) && $MJTC_data['userfieldtype'] == 'depandant_field') {
                if ($MJTC_data['id'] != '') {
                    //to handle edit case of depandat field
                    $MJTC_data['arraynames'] = $MJTC_data['arraynames2'];
                }
                if (!empty($MJTC_data['arraynames'])) {
                    $MJTC_valarrays = MJTC_majesticsupportphplib::MJTC_explode('_MS_Unique_88a9e3_', $MJTC_data['arraynames']);
                    $empty_flag = 0;
                    $MJTC_key_flag = '';
                    foreach ($MJTC_valarrays as $MJTC_key => $MJTC_value) {
                        if($MJTC_key != $MJTC_key_flag){
                            $MJTC_key_flag = $MJTC_key;
                            $empty_flag = 0;
                        }
                        $MJTC_keyvalue = $MJTC_value;
                        $MJTC_value = MJTC_majesticsupportphplib::MJTC_str_replace(' ','__',$MJTC_value);
                        $MJTC_value = MJTC_majesticsupportphplib::MJTC_str_replace('.','___',$MJTC_value);
                        // This check was previously commented out, but caused errors when child values were empty.
                        // Uncommented and handled properly by Hamza to avoid runtime issues with empty inputs.
                        if ( isset($MJTC_data[$MJTC_value]) && $MJTC_data[$MJTC_value] != null) {
                            $MJTC_keyvalue = MJTC_majesticsupportphplib::MJTC_htmlentities($MJTC_keyvalue);
                            $params[$MJTC_keyvalue] = array_filter($MJTC_data[$MJTC_value]);
                            $empty_flag = 1;
                        }
                    }
                    if($empty_flag == 0){
                        MJTC_message::MJTC_setMessage(esc_html(__('Please Insert At least one value for every option', 'majestic-support')), 'error');
                        return 2 ;
                    }
                }
                $flagvar = $this->updateParentField($MJTC_data['parentfield'], $fieldname, $MJTC_data['fieldfor']);
                if ($flagvar == false) {
                    MJTC_message::MJTC_setMessage(esc_html(__('Parent field has not been stored', 'majestic-support')), 'error');
                }
            }
            if (!empty($MJTC_data['values'])) {
                foreach ($MJTC_data['values'] as $MJTC_key => $MJTC_value) {
                    if ($MJTC_value != null) {
                        $MJTC_value = MJTC_majesticsupportphplib::MJTC_str_replace('[','',$MJTC_value);
                        $MJTC_value = MJTC_majesticsupportphplib::MJTC_str_replace(']','',$MJTC_value);
                        $params[] = MJTC_majesticsupportphplib::MJTC_trim($MJTC_value);
                    }
                }
            }

            $visible = [];

            if (isset($MJTC_data['visibleParent']) && is_array($MJTC_data['visibleParent'])) {

                // new start

                if (!empty($MJTC_data['id'])) {
                    $query = "SELECT id, visible_field FROM " . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering WHERE visible_field LIKE '%" . esc_sql($fieldname) . "%' AND multiformid = ".esc_sql($MJTC_data['multiformid']);
                    $query_results = majesticsupport::$_db->get_results($query);
                    
                    if (!empty($query_results)) {
                        foreach ($query_results as $query_result) {
                            $query_fieldname = $query_result->visible_field;
                            $query_fieldname = MJTC_majesticsupportphplib::MJTC_str_replace(',' . $fieldname, '', $query_fieldname);
                            $query_fieldname = MJTC_majesticsupportphplib::MJTC_str_replace($fieldname, '', $query_fieldname);
                            $query = "UPDATE `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` SET visible_field = '" . esc_sql($query_fieldname) . "' WHERE id = " . esc_sql($query_result->id) . " AND multiformid = ".esc_sql($MJTC_data['multiformid']);
                            majesticsupport::$_db->query($query);
                        }
                    }
                }

                // new end
                $visibleParents = $MJTC_data['visibleParent'];
                $visibleValues = isset($MJTC_data['visibleValue']) ? $MJTC_data['visibleValue'] : [];
                $visibleConditions = isset($MJTC_data['visibleCondition']) ? $MJTC_data['visibleCondition'] : [];
                $MJTC_visibleLogics = isset($MJTC_data['visibleLogic']) ? $MJTC_data['visibleLogic'] : [];

                $final = []; // Final grouped structure
                $currentAndGroup = []; // Current AND group

                foreach ($visibleParents as $index => $parentFieldId) {
                    if (
                        isset($visibleParents[$index]) && $visibleParents[$index] !== '' &&
                        isset($visibleValues[$index]) && $visibleValues[$index] !== '' &&
                        isset($visibleConditions[$index]) && $visibleConditions[$index] !== ''
                    ) {
                        $fieldname = $fieldname ?? ''; // Just in case
                        $logic = isset($MJTC_visibleLogics[$index]) ? $MJTC_visibleLogics[$index] : '';

                        // Build the current row
                        $row = [
                            'visibleParentField' => $fieldname,
                            'visibleParent' => $visibleParents[$index],
                            'visibleCondition' => $visibleConditions[$index],
                            'visibleValue' => $visibleValues[$index],
                            'visibleLogic' => $logic
                        ];

                        if ($logic === 'AND') {
                            // New AND group starts
                            if (!empty($currentAndGroup)) {
                                $final[] = $currentAndGroup;
                                $currentAndGroup = [];
                            }
                            $currentAndGroup[] = $row;
                        } else {
                            // Continue current AND group (OR rows)
                            $currentAndGroup[] = $row;
                        }

                        // --- your database update code ---
                        $query = "SELECT visible_field FROM " . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering WHERE field = '" . esc_sql($visibleParents[$index]) . "' AND multiformid = ".esc_sql($MJTC_data['multiformid']);
                        $old_fieldname = majesticsupport::$_db->get_var($query);
                        $new_fieldname = $fieldname;

                        if (!empty($MJTC_data['id'])) {
                            $old_fieldname = MJTC_majesticsupportphplib::MJTC_str_replace(',' . $fieldname, '', $old_fieldname);
                            $old_fieldname = MJTC_majesticsupportphplib::MJTC_str_replace($fieldname, '', $old_fieldname);
                        }

                        if (!empty($old_fieldname)) {
                            $new_fieldname = $old_fieldname . ',' . $new_fieldname;
                        }

                        $query = "UPDATE `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` SET visible_field = '" . esc_sql($new_fieldname) . "' WHERE field = '" . esc_sql($visibleParents[$index]) . "' AND multiformid = ".esc_sql($MJTC_data['multiformid']);
                        majesticsupport::$_db->query($query);

                        if (majesticsupport::$_db->last_error != null) {
                            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
                        }
                    }
                }
                // After finishing all rows
                if (!empty($currentAndGroup)) {
                    $final[] = $currentAndGroup;
                }

                // Now sanitize and save the final nested array
                $visible_array = array_map(array($this, 'sanitize_custom_field'), $final);
                $visibleparams = wp_json_encode(stripslashes_deep($visible_array));

            } else if (!empty($MJTC_data['id'])) {
                if ($MJTC_data['fieldfor'] != 3) {
                    $MJTC_data['visibleparams'] = '';
                    // If editing old field
                    $query = "SELECT id, visible_field FROM " . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering WHERE visible_field LIKE '%" . esc_sql($fieldname) . "%' AND multiformid = ".esc_sql($MJTC_data['multiformid']);
                    $query_results = majesticsupport::$_db->get_results($query);
                    if (!empty($query_results)) {
                        foreach ($query_results as $query_result) {
                            if (isset($query_result)) {
                                $query_fieldname = $query_result->visible_field;
                                $query_fieldname = MJTC_majesticsupportphplib::MJTC_str_replace(',' . $fieldname, '', $query_fieldname);
                                $query_fieldname = MJTC_majesticsupportphplib::MJTC_str_replace($fieldname, '', $query_fieldname);
                                $query = "UPDATE `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` SET visible_field = '" . esc_sql($query_fieldname) . "' WHERE id = " . esc_sql($query_result->id);
                                majesticsupport::$_db->query($query);
                            }
                        }
                    }
                }
            }
            if (isset($MJTC_data['userfieldtype']) && $MJTC_data['userfieldtype'] == 'termsandconditions') { // to manage terms and condition field
                if ($MJTC_data['termsandconditions_linktype'] == 1) {
                    $params['termsandconditions_link'] = $MJTC_data['termsandconditions_link'];
                } else if ($MJTC_data['termsandconditions_linktype'] == 2) {
                    $params['termsandconditions_page'] = $MJTC_data['termsandconditions_page'];
                }
                $params['termsandconditions_text'] = $MJTC_data['termsandconditions_text'];
                $params['termsandconditions_linktype'] = $MJTC_data['termsandconditions_linktype'];
            }

                // $params = wp_json_encode($params);
                $params_array = array_map(array($this,'sanitize_custom_field'), $params);
                $MJTC_userfieldparams = wp_json_encode(stripslashes_deep($params_array));

            //}
            // for default value
            $MJTC_data['defaultvalue'] = '';
            if($MJTC_data['userfieldtype'] == "combo" || $MJTC_data['userfieldtype'] == "radio" || $MJTC_data['userfieldtype'] == "multiple" || $MJTC_data['userfieldtype'] == "checkbox" || $MJTC_data['userfieldtype'] == "depandant_field") {
                $MJTC_data['defaultvalue'] = !empty($MJTC_data['defaultvalue_select']) ? $MJTC_data['defaultvalue_select'] : '';
            } else {
                $MJTC_data['defaultvalue'] = !empty($MJTC_data['defaultvalue_input']) ? $MJTC_data['defaultvalue_input'] : '';
            }
        }else{
            $fieldname = $MJTC_data['field'];
            $MJTC_data['userfieldtype'] = '';
            $MJTC_data['defaultvalue'] = !empty($MJTC_data['defaultvalue_input']) ? $MJTC_data['defaultvalue_input'] : '';
            // get data for system fields of type terms ans conditions
            if (in_array($MJTC_data['field'], ['termsandconditions1', 'termsandconditions2', 'termsandconditions3'])) { // to manage terms and condition field
                if ($MJTC_data['termsandconditions_linktype'] == 1) {
                    $params['termsandconditions_link'] = $MJTC_data['termsandconditions_link'];
                } else if ($MJTC_data['termsandconditions_linktype'] == 2) {
                    $params['termsandconditions_page'] = $MJTC_data['termsandconditions_page'];
                }
                $params['termsandconditions_text'] = $MJTC_data['termsandconditions_text'];
                $params['termsandconditions_linktype'] = $MJTC_data['termsandconditions_linktype'];
                $params_array = array_map(array($this,'sanitize_custom_field'), $params);
                $MJTC_userfieldparams = wp_json_encode(stripslashes_deep($params_array));
            }
        }

        // for adminonly
        if(!empty($MJTC_data['adminonly'])){
            $MJTC_data['isvisitorpublished'] = 0;
            $MJTC_data['search_user'] = 0;
        }

        $MJTC_data['field'] = $fieldname;
        $MJTC_data['section'] = 10;

        /*if (!empty($MJTC_data['depandant_field']) && $MJTC_data['depandant_field'] != null ) {

            $query = "SELECT * FROM " . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering where
            field = '". esc_sql($MJTC_data['depandant_field'])."'";
            $child = majesticsupport::$_db->get_row($query);
            $parent = $MJTC_data;
            $flagvar = $this->updateChildField($parent, $child);
            if ($flagvar == false) {
                MJTC_message::MJTC_setMessage(esc_html(__('Child fields has not been stored', 'majestic-support')), 'error');
            }
        }*/

        $row = MJTC_includer::MJTC_getTable('fieldsordering');
        $MJTC_data = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($MJTC_data);// remove slashes with quotes.
        if (!empty($MJTC_userfieldparams)) {
            $MJTC_data['userfieldparams'] = $MJTC_userfieldparams;
        }
        if (!empty($visibleparams)) {
            $MJTC_data['visibleparams'] = $visibleparams;
        }
        $error = 0;
        if (!$row->bind($MJTC_data)) {
            $error = 1;
        }
        if (!$row->store()) {
            $error = 1;
        }

        if ($error == 1) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
            MJTC_message::MJTC_setMessage(esc_html(__('Field has not been stored', 'majestic-support')), 'error');
        } else {
            MJTC_message::MJTC_setMessage(esc_html(__('Field has been stored', 'majestic-support')), 'updated');
            // update the dependent fields data if exist
            if (!empty($MJTC_data['depandant_field']) && $MJTC_data['depandant_field'] != null ) {

                $query = "SELECT * FROM " . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering where
                field = '". esc_sql($MJTC_data['depandant_field'])."'";
                $child = majesticsupport::$_db->get_row($query);
                
                /* get parent saved data */
                $query = "SELECT * FROM " . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering where
                id = '". esc_sql($MJTC_data['id'])."'";
                $parent = majesticsupport::$_db->get_row($query);
                /* get parent saved data */
                
                // $parent = $MJTC_data;
                $flagvar = $this->updateChildField($parent, $child);
                if ($flagvar == false) {
                    MJTC_message::MJTC_setMessage(esc_html(__('Child fields has not been stored', 'majestic-support')), 'error');
                }
            }
        }
        return 1;
    }

    function updateField($MJTC_data) {
        if (empty($MJTC_data)) {
            return false;
        }
        if (!current_user_can('manage_options')) { //only admin can change it.
            return false;
        }
        $inquery = '';
        $clasue = '';
        if(isset($MJTC_data['fieldtitle']) && $MJTC_data['fieldtitle'] != null){
            $inquery .= $clasue." fieldtitle = '". esc_sql($MJTC_data['fieldtitle']) ."'";
            $clasue = ' , ';
        }
        if(isset($MJTC_data['published']) && $MJTC_data['published'] != null){
            $inquery .= $clasue." published = ". esc_sql($MJTC_data['published']);
            $clasue = ' , ';
        }
        if(isset($MJTC_data['isvisitorpublished']) && $MJTC_data['isvisitorpublished'] != null){
            $inquery .= $clasue." isvisitorpublished = ". esc_sql($MJTC_data['isvisitorpublished']);
            $clasue = ' , ';
        }
        if(isset($MJTC_data['placeholder']) && $MJTC_data['placeholder'] != null){
            $inquery .= $clasue." placeholder = '". esc_sql($MJTC_data['placeholder']) ."'";
            $clasue = ' , ';
        }
        if(isset($MJTC_data['description']) && $MJTC_data['description'] != null){
            $inquery .= $clasue." description = '". esc_sql($MJTC_data['description']) . "'";
            $clasue = ' , ';
        }
        if(isset($MJTC_data['required']) && $MJTC_data['required'] != null){
            $inquery .= $clasue." required = ". esc_sql($MJTC_data['required']);
            $clasue = ' , ';
        }
        if(isset($MJTC_data['search_user']) && $MJTC_data['search_user'] != null){
            $inquery .= $clasue." search_user = ". esc_sql($MJTC_data['search_user']);
            $clasue = ' , ';
        }
        if(isset($MJTC_data['search_admin']) && $MJTC_data['search_admin'] != null){
            $inquery .= $clasue." search_admin = ". esc_sql($MJTC_data['search_admin']);
            $clasue = ' , ';
        }
        if(isset($MJTC_data['showonlisting']) && $MJTC_data['showonlisting'] != null){
            $inquery .= $clasue." showonlisting = ". esc_sql($MJTC_data['showonlisting']);
            $clasue = ' , ';
        }

        $query = "UPDATE `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` SET ".$inquery." WHERE id = " . esc_sql($MJTC_data['id']) ;
        majesticsupport::$_db->query($query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        MJTC_message::MJTC_setMessage(esc_html(__('Field has been updated', 'majestic-support')),'updated');

        return;
    }

    function updateParentField($parentfield, $field, $fieldfor) {
        if(!is_numeric($fieldfor)) return false;
        if(!is_numeric($parentfield)) return false;
        if(empty($field)) return false;

        $query = "UPDATE `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` SET depandant_field = '" . esc_sql($field) . "' WHERE id = " . esc_sql($parentfield) . " AND fieldfor = " . esc_sql($fieldfor);
        majesticsupport::$_db->query($query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        return true;
    }

    function updateChildField($parent, $child){
        if(!is_numeric($child->id)) return false;
        $childfieldparams = json_decode( $child->userfieldparams,TRUE);
        $parentfieldparams = json_decode( $parent->userfieldparams,TRUE);

        $childNew = [];

        foreach ($parentfieldparams as $parentKey => $parentValue) {
            $childKeys = is_array($parentValue) ? $parentValue : [$parentValue];

            foreach ($childKeys as $childKey) {
                if (isset($childfieldparams[$childKey])) {
                    $childNew[$childKey] = $childfieldparams[$childKey];
                } else {
                    $childNew[$childKey] = '';
                }
            }
        }
        $childNew = wp_json_encode( $childNew );
        $child->userfieldparams = $childNew;
        $query = "UPDATE `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` SET userfieldparams = '" . esc_sql($childNew) . "' WHERE id = " . esc_sql($child->id);
        majesticsupport::$_db->query($query);
        if (majesticsupport::$_db->last_error != null) {

            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        return true;
    }

    function getFieldsForComboByFieldFor() {
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $nonce, 'get-fields-for-combo-by-fieldfor') ) {
            die( 'Security check Failed' );
        }
        $formid = MJTC_request::MJTC_getVar('formid');
        $fieldfor = MJTC_request::MJTC_getVar('fieldfor');
        $parentfield = MJTC_request::MJTC_getVar('parentfield');
        if(!is_numeric($fieldfor)) return false;
        $wherequery = '';
        if(isset($parentfield) && $parentfield !='' ){
            $query = "SELECT id FROM " . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering WHERE fieldfor = ".esc_sql($fieldfor)." AND (userfieldtype = 'radio' OR userfieldtype = 'combo'OR userfieldtype = 'depandant_field') AND depandant_field = '" . esc_sql($parentfield) . "' ";
            $parent = majesticsupport::$_db->get_var($query);
            $wherequery = ' OR id = '.esc_sql($parent);
        }
        $query = "SELECT fieldtitle AS text ,id FROM " . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering WHERE fieldfor = ".esc_sql($fieldfor)." AND multiformid = ".esc_sql($formid)." AND (userfieldtype = 'radio' OR userfieldtype = 'combo' OR userfieldtype = 'depandant_field') AND (depandant_field = '' ".esc_sql($wherequery)." ) ";
        $MJTC_data = majesticsupport::$_db->get_results($query);
        if(isset($parentfield) && $parentfield !='' ){
            $query = "SELECT id FROM " . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering WHERE fieldfor = ".esc_sql($fieldfor)." AND (userfieldtype = 'radio' OR userfieldtype = 'combo'OR userfieldtype = 'depandant_field') AND depandant_field = '" . esc_sql($parentfield) . "' ";
            $parent = majesticsupport::$_db->get_var($query);
        }
        $nonce = wp_create_nonce("get-section-to-fill-values-".$fieldfor);
        $msFunction = 'getDataOfSelectedField("'.$nonce.'");';
        $html = MJTC_formfield::MJTC_select('parentfield', $MJTC_data, (isset($parent) && $parent !='') ? $parent : '', esc_html(__('Select', 'majestic-support')) .'&nbsp;'. esc_html(__('Parent Field', 'majestic-support')), array('onchange' => $msFunction, 'class' => 'inputbox one mjtc-form-select-field', 'data-validation' => 'required'));
        $html = MJTC_majesticsupportphplib::MJTC_htmlentities($html);
        $MJTC_data = wp_json_encode($html);
        return $MJTC_data;
    }

    function getFieldsForVisibleCombobox($fieldfor, $multiformid, $field='', $cid='') {
        if(!is_numeric($fieldfor)) return false;
        $wherequery = '';
        if(isset($field) && $field !='' ){
            $query = "SELECT id FROM " . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering WHERE fieldfor = ".esc_sql($fieldfor)." AND (userfieldtype IN ( 'combo', 'text', 'checkbox', 'date', 'email', 'radio', 'multiple') ) AND visible_field = '" . esc_sql($field) . "' ";
            $parent = majesticsupport::$_db->get_var($query);
            if ($parent) {
                $wherequery = ' OR id = '.esc_sql($parent);
            }
        }
        $wherequeryforedit = '';
        if(isset($cid) && $cid !='' ){
            $wherequeryforedit = ' AND id != '.esc_sql($cid);
        }
        
        // Base fields always included
        $builtin_fields = ['email', 'fullname', 'phone', 'subject', 'department', 'priority'];

        // Convert to comma-separated string for SQL IN clause
        $builtin_fields_sql = "'" . implode("','", array_map('esc_sql', $builtin_fields)) . "'";

        // Build the final SQL query
        $query = "
        SELECT fieldtitle AS text, field AS id 
            FROM " . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering 
            WHERE (
                fieldfor = " . esc_sql($fieldfor) . " 
                AND multiformid = '" . esc_sql($multiformid) . "' 
                AND field IN ($builtin_fields_sql) 
                $wherequeryforedit $wherequery
            ) 
            OR (
                fieldfor = " . esc_sql($fieldfor) . " 
                AND multiformid = '" . esc_sql($multiformid) . "' 
                AND userfieldtype IN ('combo', 'text', 'checkbox', 'date', 'email', 'radio', 'multiple') 
                $wherequeryforedit $wherequery
            )";
        $MJTC_data = majesticsupport::$_db->get_results($query);
        return $MJTC_data;
    }

    function getChildForVisibleCombobox($perentid = null , $MJTC_default = null) {
        $isAjaxCall = MJTC_request::MJTC_getVar('isAjaxCall');
        if ($isAjaxCall == 1) {
            $nonce = MJTC_request::MJTC_getVar('_wpnonce');
            if (! wp_verify_nonce( $nonce, 'get-child-for-visible-combobox') ) {
                die( 'Security check Failed' );
            }
        }
        if ($perentid == null) {
            $perentid = MJTC_request::MJTC_getVar('val');
        }
        if (empty($perentid)){
            return false;
        }

        $query = "SELECT isuserfield, userfieldtype, field FROM `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` WHERE field = '" . esc_sql($perentid)."'";
        $fieldType = majesticsupport::$_db->get_row($query);
        $showComboBox = false;
        if (isset($fieldType->isuserfield) && $fieldType->isuserfield == 1) {
            $query = "SELECT userfieldparams AS params FROM `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` WHERE field = '" . esc_sql($perentid) . "'";
            $options = majesticsupport::$_db->get_var($query);
            $options = json_decode($options);
            foreach ($options as $MJTC_key => $option) {
                $MJTC_fieldtypes[$MJTC_key] = (object) array('id' => $option, 'text' => $option);
            }
            if (in_array($fieldType->userfieldtype, ['combo', 'checkbox', 'radio', 'multiple'])) {
                $showComboBox = true;
            }
        } else if ($fieldType->field == 'department') {
            $showComboBox = true;
            $query = "SELECT departmentname AS text ,id FROM " . majesticsupport::$_db->prefix . "mjtc_support_departments";
            $MJTC_fieldtypes = majesticsupport::$_db->get_results($query);
        } else if ($fieldType->field == 'helptopic') {
            $showComboBox = true;
            $query = "SELECT id, topic AS text FROM `" . majesticsupport::$_db->prefix . "mjtc_support_help_topics` WHERE status = 1";
            $query.= "  ORDER BY ordering ASC";
            $MJTC_fieldtypes = majesticsupport::$_db->get_results($query);
        } else if ($fieldType->field == 'priority') {
            $showComboBox = true;
            $query = "SELECT id, priority AS text FROM `" . majesticsupport::$_db->prefix . "mjtc_support_priorities`";
            $query .= 'ORDER BY ordering ASC';
            $MJTC_fieldtypes = majesticsupport::$_db->get_results($query);
        }
        //
        $combobox = false;
        if($showComboBox){
            $combobox = MJTC_formfield::MJTC_select('visibleValue[]', $MJTC_fieldtypes, isset($MJTC_default) ? $MJTC_default : '', '', array('class' => 'inputbox one mjtc-form-select-field mjtc-form-input-field-visible'));
        } else {
            $combobox = MJTC_formfield::MJTC_text('visibleValue[]', isset($MJTC_default) ? $MJTC_default : '', array('class' => 'inputbox one mjtc-form-input-field mjtc-form-input-field-visible'));
        }
        return MJTC_majesticsupportphplib::MJTC_htmlentities($combobox);
    }

    function getConditionsForVisibleCombobox($perentid = null , $MJTC_default = null) {
        $isAjaxCall = MJTC_request::MJTC_getVar('isAjaxCall');
        if ($isAjaxCall == 1) {
            $nonce = MJTC_request::MJTC_getVar('_wpnonce');
            if (! wp_verify_nonce( $nonce, 'get-conditions-for-visible-combobox') ) {
                die( 'Security check Failed' );
            }
        }
        if ($perentid == null) {
            $perentid = MJTC_request::MJTC_getVar('val');
        }
        if (empty($perentid)){
            return false;
        }
        $Conditions = array(
        (object) array('id' => 1, 'text' => esc_html(__('Equal', 'majestic-support'))),
        (object) array('id' => 0, 'text' => esc_html(__('Not Equal', 'majestic-support'))));

        $query = "SELECT isuserfield, userfieldtype, field FROM `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` WHERE field = '" . esc_sql($perentid) . "'";
        $fieldType = majesticsupport::$_db->get_row($query);
        if (empty($fieldType->isuserfield)) {
            if ($fieldType->field == 'email' || $fieldType->field == 'fullname' || $fieldType->field == 'phone' || $fieldType->field == 'subject' || $fieldType->field == 'issuesummary') {
                $Conditions = array(
                (object) array('id' => 2, 'text' => esc_html(__('Contain', 'majestic-support'))),
                (object) array('id' => 3, 'text' => esc_html(__('Not Contain', 'majestic-support'))));
            }
        } else {
            if (!in_array($fieldType->userfieldtype, ['combo', 'checkbox', 'radio', 'multiple'])) {
                $Conditions = array(
                (object) array('id' => 2, 'text' => esc_html(__('Contain', 'majestic-support'))),
                (object) array('id' => 3, 'text' => esc_html(__('Not Contain', 'majestic-support'))));
            }
        }
        $combobox = false;
        if(!empty($Conditions)){
            $combobox = MJTC_formfield::MJTC_select('visibleCondition[]', $Conditions, isset($MJTC_default) ? $MJTC_default : '', '', array('class' => 'inputbox one mjtc-form-select-field mjtc-form-input-field-visible'));
        }
        return MJTC_majesticsupportphplib::MJTC_htmlentities($combobox);
    }

    function getSectionToFillValues() {
        $fieldfor = MJTC_request::MJTC_getVar('fieldfor');
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $nonce, 'get-section-to-fill-values-'.$fieldfor) ) {
            die( 'Security check Failed' );
        }
        $field = MJTC_request::MJTC_getVar('pfield');
        if(!is_numeric($field)){
            return false;
        }
        $query = "SELECT userfieldparams FROM " . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering WHERE id=".esc_sql($field);
        $MJTC_data = majesticsupport::$_db->get_var($query);
        $MJTC_datas = json_decode($MJTC_data);
        $html = '';
        $fieldsvar = '';
        $MJTC_comma = '';
        foreach ($MJTC_datas as $MJTC_data) {
            if(is_array($MJTC_data)){
                for ($i = 0; $i < count($MJTC_data); $i++) {
                    $fieldsvar .= $MJTC_comma . "$MJTC_data[$i]";
                    $MJTC_textvar = $MJTC_data[$i];
                    if($MJTC_textvar != ''){
                        $MJTC_textvar = MJTC_majesticsupportphplib::MJTC_str_replace(' ','__',$MJTC_textvar);
                        $MJTC_textvar = MJTC_majesticsupportphplib::MJTC_str_replace('.','___',$MJTC_textvar);
                    }
                    $MJTC_divid = $MJTC_textvar;
                    $mjtc_value = esc_js($MJTC_divid);
                    $MJTC_textvar .='[]';
                    $html .= "<div class='ms-user-dd-field-wrap'>";
                    $html .= "<div class='ms-user-dd-field-title'>" . esc_html($MJTC_data[$i]) . "</div>";
                    $html .= "<div class='ms-user-dd-field-value combo-options-fields' id=" . esc_attr($MJTC_divid) . ">
                                    <span class='input-field-wrapper'>
                                        " . wp_kses(MJTC_formfield::MJTC_text($MJTC_textvar, '', array('class' => 'inputbox one user-field')), MJTC_ALLOWED_TAGS) . "
                                        <img class='input-field-remove-img' src='" . esc_url(MJTC_PLUGIN_URL) . "includes/images/delete.png' />
                                    </span>
                                    <input type='button' class='ms-button-link button user-field-val-button' id='depandant-field-button' onClick='getNextField(\"" . $mjtc_value . "\", this);'  value='Add More' />
                                </div>";
                    $html .= "</div>";
                    $MJTC_comma = '_MS_Unique_88a9e3_';
                }
            }else{
                $fieldsvar .= $MJTC_comma . "$MJTC_data";
                $MJTC_textvar = $MJTC_data;
                if($MJTC_textvar != ''){
                    $MJTC_textvar = MJTC_majesticsupportphplib::MJTC_str_replace(' ','__',$MJTC_textvar);
                    $MJTC_textvar = MJTC_majesticsupportphplib::MJTC_str_replace('.','___',$MJTC_textvar);
                }
                $MJTC_divid = $MJTC_textvar;
                $mjtc_value = esc_js($MJTC_divid);
                $MJTC_textvar .='[]';
                $html .= "<div class='ms-user-dd-field-wrap'>";
                $html .= "<div class='ms-user-dd-field-title'>" . esc_html($MJTC_data) . "</div>";
                $html .= "<div class='ms-user-dd-field-value combo-options-fields' id=" . esc_attr($MJTC_divid) . ">
                                <span class='input-field-wrapper'>
                                    " . wp_kses(MJTC_formfield::MJTC_text($MJTC_textvar, '', array('class' => 'inputbox one user-field')), MJTC_ALLOWED_TAGS) . "
                                    <img class='input-field-remove-img' src='" . esc_url(MJTC_PLUGIN_URL) . "includes/images/delete.png' />
                                </span>
                                <input type='button' class='ms-button-link button user-field-val-button' id='depandant-field-button' onClick=\"getNextField('" . $mjtc_value . "', this);\"  value='Add More' />
                            </div>";
                $html .= "</div>";
                $MJTC_comma = '_MS_Unique_88a9e3_';
            }

        }
        $html .= " <input type='hidden' name='arraynames' value=\"" . esc_attr($fieldsvar) . "\" />";
        $html = MJTC_majesticsupportphplib::MJTC_htmlentities($html);
        $html = wp_json_encode($html);
        return $html;
    }

    function getOptionsForFieldEdit() {
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $nonce, 'get-options-for-field-edit') ) {
            die( 'Security check Failed' );
        }
        $field = MJTC_request::MJTC_getVar('field');
		if(!is_numeric($field)) return false;
        $MJTC_yesno = array(
            (object) array('id' => 1, 'text' => esc_html(__('Yes', 'majestic-support'))),
            (object) array('id' => 0, 'text' => esc_html(__('No', 'majestic-support'))));

        $query = "SELECT * FROM " . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering WHERE id=".esc_sql($field);
        $MJTC_data = majesticsupport::$_db->get_row($query);

        $html = '<div class="userpopup-top">
                    <div class="userpopup-heading" >
                    ' . esc_html(__("Edit Field", 'majestic-support')) . '
                    </div>
                    <img id="popup_cross" class="userpopup-close" onClick="close_popup();" src="' . esc_url(MJTC_PLUGIN_URL) . 'includes/images/close-icon-white.png" alt="'. esc_html(__('Close','majestic-support')).'">
                </div>';
        $MJTC_nonce_id = isset($MJTC_data->id) ? $MJTC_data->id : '';
        $adminurl = admin_url("?page=majesticsupport_fieldordering&task=savefeild&formid=".esc_attr($MJTC_data->multiformid));
        $html .= '<form id="adminForm" class="popup-field-from" method="post" action="' . esc_url(wp_nonce_url($adminurl ,"save-feild-".$MJTC_nonce_id)).'">';
        $html .= '<div class="popup-field-wrapper">
                    <div class="popup-field-title">' . esc_html(__('Field Title', 'majestic-support')) . '<font class="required-notifier">*</font></div>
                    <div class="popup-field-obj">' . wp_kses(MJTC_formfield::MJTC_text('fieldtitle', isset($MJTC_data->fieldtitle) ? $MJTC_data->fieldtitle : 'text', array('class' => 'inputbox one', 'data-validation' => 'required')), MJTC_ALLOWED_TAGS) . '</div>
                </div>';
        if ($MJTC_data->cannotunpublish == 0 || $MJTC_data->cannotshowonlisting == 0) {
            $html .= '<div class="popup-field-wrapper">
                        <div class="popup-field-title">' . esc_html(__('User Published', 'majestic-support')) . '</div>
                        <div class="popup-field-obj">' . wp_kses(MJTC_formfield::MJTC_select('published', $MJTC_yesno, isset($MJTC_data->published) ? $MJTC_data->published : 0, '', array('class' => 'inputbox one', 'data-validation' => 'required')), MJTC_ALLOWED_TAGS) . '</div>
                    </div>';
            $html .= '<div class="popup-field-wrapper">
                    <div class="popup-field-title">' . esc_html(__('Visitor Published', 'majestic-support')) . '</div>
                    <div class="popup-field-obj">' . wp_kses(MJTC_formfield::MJTC_select('isvisitorpublished', $MJTC_yesno, isset($MJTC_data->isvisitorpublished) ? $MJTC_data->isvisitorpublished : 0, '', array('class' => 'inputbox one', 'data-validation' => 'required')), MJTC_ALLOWED_TAGS) . '</div>
                </div>';
        }

        $html .= '<div class="popup-field-wrapper">
                <div class="popup-field-title">' . esc_html(__('Place Holder', 'majestic-support')) . '</div>
                <div class="popup-field-obj">' . MJTC_formfield::MJTC_text('placeholder', isset($MJTC_data->placeholder) ? $MJTC_data->placeholder : '', array('class' => 'inputbox one','maxlength'=>225)) . '</div>
            </div>';

        $html .= '<div class="popup-field-wrapper">
                <div class="popup-field-title">' . esc_html(__('Description', 'majestic-support')) . '</div>
                <div class="popup-field-obj">' . MJTC_formfield::MJTC_text('description', isset($MJTC_data->description) ? $MJTC_data->description : '', array('class' => 'inputbox one','maxlength'=>225)) . '</div>
            </div>';
        if ($MJTC_data->cannotunpublish == 0 || $MJTC_data->cannotshowonlisting == 0) {

            $html .= '<div class="popup-field-wrapper">
                    <div class="popup-field-title">' . esc_html(__('Required', 'majestic-support')) . '</div>
                    <div class="popup-field-obj">' . wp_kses(MJTC_formfield::MJTC_select('required', $MJTC_yesno, isset($MJTC_data->required) ? $MJTC_data->required : 0, '', array('class' => 'inputbox one', 'data-validation' => 'required')), MJTC_ALLOWED_TAGS) . '</div>
                </div>';
        }
        if ($MJTC_data->cannotsearch == 0) {
            $html .= '<div class="popup-field-wrapper">
                        <div class="popup-field-title">' . esc_html(__('User Search', 'majestic-support')) . '</div>
                        <div class="popup-field-obj">' . wp_kses(MJTC_formfield::MJTC_select('search_user', $MJTC_yesno, isset($MJTC_data->search_user) ? $MJTC_data->search_user : 0, '', array('class' => 'inputbox one', 'data-validation' => 'required')), MJTC_ALLOWED_TAGS) . '</div>
                    </div>';
            $html .= '<div class="popup-field-wrapper">
                        <div class="popup-field-title">' . esc_html(__('Admin Search', 'majestic-support')) . '</div>
                        <div class="popup-field-obj">' . wp_kses(MJTC_formfield::MJTC_select('search_admin', $MJTC_yesno, isset($MJTC_data->search_admin) ? $MJTC_data->search_admin : 0, '', array('class' => 'inputbox one', 'data-validation' => 'required')), MJTC_ALLOWED_TAGS) . '</div>
                    </div>';
        }
        if ($MJTC_data->isuserfield == 1 || $MJTC_data->cannotshowonlisting == 0) {
            $html .= '<div class="popup-field-wrapper">
                        <div class="popup-field-title">' . esc_html(__('Show On Listing', 'majestic-support')) . '</div>
                        <div class="popup-field-obj">' . wp_kses(MJTC_formfield::MJTC_select('showonlisting', $MJTC_yesno, isset($MJTC_data->showonlisting) ? $MJTC_data->showonlisting : 0, '', array('class' => 'inputbox one', 'data-validation' => 'required')), MJTC_ALLOWED_TAGS) . '</div>
                    </div>';
        }
        $html .= wp_kses(MJTC_formfield::MJTC_hidden('form_request', 'majesticsupport'), MJTC_ALLOWED_TAGS);
        $html .= wp_kses(MJTC_formfield::MJTC_hidden('id', $MJTC_data->id), MJTC_ALLOWED_TAGS);
        $html .= wp_kses(MJTC_formfield::MJTC_hidden('isuserfield', $MJTC_data->isuserfield), MJTC_ALLOWED_TAGS);
        $html .= wp_kses(MJTC_formfield::MJTC_hidden('fieldfor', $MJTC_data->fieldfor), MJTC_ALLOWED_TAGS);
        $html .='<div class="mjtc-submit-container mjtc-col-lg-10 mjtc-col-md-10 mjtc-col-md-offset-1 mjtc-col-md-offset-1">
                    ' . wp_kses(MJTC_formfield::MJTC_submitbutton('save', esc_html(__('Save', 'majestic-support')), array('class' => 'button')), MJTC_ALLOWED_TAGS);
        if ($MJTC_data->isuserfield == 1) {
            $html .= '<a class="button" style="margin-left:10px;" id="user-field-anchor" href="?page=majesticsupport_fieldordering&mjslay=adduserfeild&majesticsupportid=' . esc_attr($MJTC_data->id) .'&fieldfor='.esc_attr($MJTC_data->fieldfor).'&formid='.esc_attr($MJTC_data->multiformid).'"> ' . esc_html(__('Advanced', 'majestic-support')) . ' </a>';
        }

        $html .='</div>
            </form>';
        $html = MJTC_majesticsupportphplib::MJTC_htmlentities($html);
        return wp_json_encode($html);
    }

    function deleteUserField($id){
        if (is_numeric($id) == false)
           return false;
        $query = "SELECT field,field,fieldfor FROM `".majesticsupport::$_db->prefix."mjtc_support_fieldsordering` WHERE id = " . esc_sql($id);
        $result = majesticsupport::$_db->get_row($query);
        if ($this->userFieldCanDelete($result) == true) {
            $row = MJTC_includer::MJTC_getTable('fieldsordering');
            if (!$row->delete($id)) {
                MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
                MJTC_message::MJTC_setMessage(esc_html(__('Field has not been deleted', 'majestic-support')),'error');
            } else {
                $query = "SELECT id,visible_field FROM `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` WHERE visible_field LIKE '%".esc_sql($result->field)."%'";
                $results = majesticsupport::$_db->get_results($query);
                foreach ($results as $MJTC_value) {
                    $visible_field =  MJTC_majesticsupportphplib::MJTC_str_replace($result->field.',', '', $MJTC_value->visible_field);
                    $visible_field =  MJTC_majesticsupportphplib::MJTC_str_replace(','.$result->field, '', $visible_field);
                    $visible_field =  MJTC_majesticsupportphplib::MJTC_str_replace($result->field, '', $visible_field);

                    $query = "UPDATE `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` SET visible_field = '".esc_sql($visible_field)."' WHERE id = ".esc_sql($MJTC_value->id);
                    majesticsupport::$_db->query($query);
                    if (majesticsupport::$_db->last_error != null) {

                        MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
                    }
                }
                $query = "SELECT id FROM `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` WHERE depandant_field = '".esc_sql($result->field)."'";
                $result = majesticsupport::$_db->get_var($query);
                if (isset($result)) {
                    $query = "UPDATE `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` SET depandant_field = '' WHERE id = ".esc_sql($result);
                    majesticsupport::$_db->query($query);
                }
                MJTC_message::MJTC_setMessage(esc_html(__('Field has been deleted', 'majestic-support')),'updated');
            }
        }else{
            MJTC_message::MJTC_setMessage(esc_html(__('Field has not been deleted', 'majestic-support')),'error');
        }
        return false;
    }

    function enforceDeleteUserField($id){
        if (is_numeric($id) == false)
           return false;
        $query = "SELECT field,fieldfor FROM `".majesticsupport::$_db->prefix."mjtc_support_fieldsordering` WHERE id = ".esc_sql($id);
        $result = majesticsupport::$_db->get_row($query);
        if ($this->userFieldCanDelete($result) == true) {
            $row = MJTC_includer::MJTC_getTable('fieldsordering');
            $row->delete($id);
        }
        return false;
    }

    function userFieldCanDelete($field) {
        $fieldname = $field->field;
        $fieldfor = $field->fieldfor;

        $table = "tickets";
        $query = ' SELECT
                    ( SELECT COUNT(id) FROM `' . majesticsupport::$_db->prefix . 'mjtc_support_'.$table.'` WHERE
                        params LIKE \'%"' . esc_sql($fieldname) . '":%\'
                    )
                    AS total';
        $total = majesticsupport::$_db->get_var($query);
        if ($total > 0)
            return false;
        else
            return true;
    }

    function getUserfieldsfor($fieldfor,$multiformid='') {
        if (!is_numeric($fieldfor))
            return false;
        if (MJTC_includer::MJTC_getObjectClass('user')->MJTC_isguest()) {
            $published = ' isvisitorpublished = 1 ';
        } else {
            $published = ' published = 1 ';
        }
        $inquery = '';
        if (isset($multiformid) && $multiformid != '') {
            $inquery = " AND multiformid = ".esc_sql($multiformid);
        }
        $query = "SELECT field,userfieldparams,userfieldtype,fieldtitle FROM `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` WHERE fieldfor = " . esc_sql($fieldfor) . " AND isuserfield = 1 AND " . $published;
        $query .= $inquery." ORDER BY field ";
        $fields = majesticsupport::$_db->get_results($query);
        return $fields;
    }

    function getUserUnpublishFieldsfor($fieldfor) {
        if (!is_numeric($fieldfor))
            return false;
        if (MJTC_includer::MJTC_getObjectClass('user')->MJTC_isguest()) {
            $published = ' isvisitorpublished = 0 ';
        } else {
            $published = ' published = 0 ';
        }
        $query = "SELECT field FROM `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` WHERE fieldfor = " . esc_sql($fieldfor) . " AND isuserfield = 1 AND " . $published;
        $fields = majesticsupport::$_db->get_results($query);
        return $fields;
    }

    function getFieldTitleByFieldfor($fieldfor,$formid='') {
        if (!is_numeric($fieldfor))
            return false;
        if (is_admin()) {
            $published = '';
        } else if (MJTC_includer::MJTC_getObjectClass('user')->MJTC_isguest()) {
            $published = ' AND isvisitorpublished = 1 ';
        } else {
            $published = ' AND published = 1 ';
        }
        $inquery = '';
        if (isset($formid) && $formid == 0) {
            $MJTC_defaultformid = MJTC_includer::MJTC_getModel('ticket')->getDefaultMultiFormId();
            $inquery = " AND multiformid = ".esc_sql($MJTC_defaultformid);
        } elseif (isset($formid) && $formid != '') {
            $inquery = " AND multiformid = ".esc_sql($formid);
        }
        $query = "SELECT field,fieldtitle FROM `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` WHERE fieldfor = " . esc_sql($fieldfor) . $published;
        $query .= $inquery;
        $fields = majesticsupport::$_db->get_results($query);
        $fielddata = array();
        foreach ($fields as $MJTC_value) {
            $fielddata[$MJTC_value->field] = $MJTC_value->fieldtitle;
        }
        return $fielddata;
    }

    function getUserFieldbyId($id,$fieldfor) {
        if ($id) {
            if (is_numeric($id) == false)
                return false;
            $query = "SELECT * FROM " . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering WHERE id = " . esc_sql($id);
            majesticsupport::$_data[0]['userfield'] = majesticsupport::$_db->get_row($query);
            $params = majesticsupport::$_data[0]['userfield']->userfieldparams;
            $visibleparams = majesticsupport::$_data[0]['userfield']->visibleparams;
            majesticsupport::$_data[0]['userfieldparams'] = !empty($params) ? json_decode($params, True) : '';
        }
        majesticsupport::$_data[0]['fieldfor'] = $fieldfor;
        return;
    }
    function getFieldsForListing($fieldfor, $formid='') {
        if (is_numeric($fieldfor) == false)
            return false;
        if (is_admin()) {
            $published = '';
        } else if (MJTC_includer::MJTC_getObjectClass('user')->MJTC_isguest()) {
            $published = ' AND isvisitorpublished = 1 ';
        } else {
            $published = ' AND published = 1 ';
        }
        $inquery = '';
        if (isset($formid) && $formid == 0) {
            $MJTC_defaultformid = MJTC_includer::MJTC_getModel('ticket')->getDefaultMultiFormId();
            $inquery = " AND multiformid = ".esc_sql($MJTC_defaultformid);
        } elseif (isset($formid) && $formid != '') {
            $inquery = " AND multiformid = ".esc_sql($formid);
        }
        $query = "SELECT field, showonlisting FROM " . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering WHERE showonlisting = 1 AND fieldfor =  " . esc_sql($fieldfor) . esc_sql($published);
        $query .= $inquery;
        $query .= " ORDER BY ordering";
        $fields = majesticsupport::$_db->get_results($query);
        $fielddata = array();
        foreach ($fields AS $field) {
            $fielddata[$field->field] = $field->showonlisting;
        }
        return $fielddata;
    }
    function getAdminSystemFieldsForSearch() {
        
        if(in_array('multiform', majesticsupport::$_active_addons)){
            $query = "SELECT f.field, f.fieldtitle FROM " . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering f LEFT JOIN " . majesticsupport::$_db->prefix . "mjtc_support_multiform m ON f.multiformid = m.id WHERE f.search_admin = 1 AND f.published = 1 AND (f.isuserfield IS NULL OR f.isuserfield != 1) ";
            $query .= " ORDER BY m.is_default DESC, f.ordering ASC";
        } else {
            $formid = MJTC_includer::MJTC_getModel('ticket')->getDefaultMultiFormId();
            $formFilter = " AND f.multiformid = " . esc_sql($formid);
            $query = "SELECT f.field, f.fieldtitle FROM " . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering f WHERE f.search_admin = 1 AND f.published = 1 AND (f.isuserfield IS NULL OR f.isuserfield != 1) ";
            $query .= $formFilter;
            $query .= " ORDER BY f.ordering ASC";
        }
        $results = majesticsupport::$_db->get_results($query);

        $fielddata = array();
        foreach ($results as $row) {
            // Only set the field once to prioritize the first (default) occurrence
            if (!isset($fielddata[$row->field])) {
                $fielddata[$row->field] = $row->fieldtitle;
            }
        }
        return $fielddata;
    }
    function getUserSystemFieldsForSearch() {
                // Determine published column based on user type
        if (MJTC_includer::MJTC_getObjectClass('user')->MJTC_isguest()) {
            $published = ' f.isvisitorpublished = 1 ';
        } else {
            $published = ' f.published = 1 ';
        }

        if(in_array('multiform', majesticsupport::$_active_addons)){
            // Query with LEFT JOIN and ordering to prioritize default form
            $query = "SELECT f.field, f.fieldtitle FROM " . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering f LEFT JOIN " . majesticsupport::$_db->prefix . "mjtc_support_multiform m ON f.multiformid = m.id WHERE f.search_user = 1 AND ".$published." AND (f.isuserfield IS NULL OR f.isuserfield != 1)";
            $query .= " ORDER BY m.is_default DESC, f.ordering ASC";
        } else {
            $formid = MJTC_includer::MJTC_getModel('ticket')->getDefaultMultiFormId();
            $formFilter = " AND f.multiformid = " . esc_sql($formid);
            // Query with LEFT JOIN and ordering to prioritize default form
            $query = "SELECT f.field, f.fieldtitle FROM " . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering f WHERE f.search_user = 1 AND ".$published." AND (f.isuserfield IS NULL OR f.isuserfield != 1)";
            $query .= $formFilter;
            $query .= " ORDER BY f.ordering ASC";
        }

        $results = majesticsupport::$_db->get_results($query);

        $fielddata = array();
        foreach ($results as $row) {
            // Only keep the first (preferred) version of each field
            if (!isset($fielddata[$row->field])) {
                $fielddata[$row->field] = $row->fieldtitle;
            }
        }

        return $fielddata;
    }
    function getPublishedFieldsForTicketDetail($formid='') {
        if(!isset($formid) || $formid==''){
            $formid = MJTC_includer::MJTC_getModel('ticket')->getDefaultMultiFormId();
        }
        if(!is_numeric($formid)) return false;
        if (MJTC_includer::MJTC_getObjectClass('user')->MJTC_isguest()) {
            $published = ' isvisitorpublished = 1 ';
        } else {
            $published = ' published = 1 ';
        }
        $query = "SELECT field, showonlisting FROM " . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering WHERE ".$published." AND fieldfor = 1 AND multiformid =  " . esc_sql($formid) ;
        $fields = majesticsupport::$_db->get_results($query);
        $fielddata = array();
        foreach ($fields AS $field) {
            $fielddata[$field->field] = $field->showonlisting;
        }
        return $fielddata;
    }

    function DataForDepandantField(){
        $childfield = MJTC_request::MJTC_getVar('child');
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $nonce, 'data-for-depandant-field-'.$childfield) ) {
            die( 'Security check Failed' );
        }
        $MJTC_val = MJTC_request::MJTC_getVar('fvalue');
        $query = "SELECT userfieldparams,fieldtitle,depandant_field,field FROM `".majesticsupport::$_db->prefix."mjtc_support_fieldsordering` WHERE field = '".esc_sql($childfield)."'";
        $MJTC_data = majesticsupport::$_db->get_row($query);
        $decoded_data = json_decode($MJTC_data->userfieldparams);
        $comboOptions = array();
        $flag = 0;
        foreach ($decoded_data as $MJTC_key => $MJTC_value) {
            $MJTC_key = html_entity_decode($MJTC_key);
            if($MJTC_key==$MJTC_val){
               for ($i=0; $i <count($MJTC_value) ; $i++) {
                   $comboOptions[] = (object)array('id' => $MJTC_value[$i], 'text' => $MJTC_value[$i]);
                   $flag = 1;
               }
            }
        }
        $msFunction = '';
        if ($MJTC_data->depandant_field != null) {
            $wpnonce = wp_create_nonce("data-for-depandant-field-".$MJTC_data->depandant_field);
            $msFunction = "MJTC_getDataForDepandantField('".$wpnonce."','" . $MJTC_data->field . "','" . $MJTC_data->depandant_field . "',1);";
        }
        $MJTC_textvar =  ($flag == 1) ? esc_html(__('Select', 'majestic-support')).' '.esc_html($MJTC_data->fieldtitle) : '';
        $html = MJTC_formfield::MJTC_select($childfield, $comboOptions, '',$MJTC_textvar, array('data-validation' => '','class' => 'inputbox one mjtc-form-select-field mjtc-support-custom-select', 'onchange' => $msFunction));
        $html = MJTC_majesticsupportphplib::MJTC_htmlentities($html);
        $phtml = wp_json_encode($html);
        return $phtml;
    }

    function sanitize_custom_field($arg) {
        if (is_array($arg)) {
            return array_map(array($this,'sanitize_custom_field'), $arg);
        }
        return MJTC_majesticsupportphplib::MJTC_htmlentities($arg, ENT_QUOTES, 'UTF-8');
    }

    function MJTC_getDataForVisibleField($field) {
        $field = esc_sql($field);
        $field_array = MJTC_majesticsupportphplib::MJTC_str_replace(",", "','", $field);

        $query = "SELECT field, visibleparams FROM " . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering WHERE field IN ('" . $field_array . "')";
        $fields = majesticsupport::$_db->get_results($query);
        $MJTC_data = array();

        if (!empty($fields)) {
            foreach ($fields as $item) {
                $fieldname = $item->field;

                $decoded = json_decode($item->visibleparams);

                // Initialize array for this field if not set
                if (!isset($MJTC_data[$fieldname])) {
                    $MJTC_data[$fieldname] = array();
                }


                if (is_array($decoded)) {
                    // New system: multiple AND/OR groups
                    foreach ($decoded as $group) {
                        if (isset($group) && is_array($group)) {
                            foreach ($group as $d) {
                                $d->visibleParentField = Self::getChildForVisibleField($d->visibleParentField);
                            }
                            $MJTC_data[$fieldname][] = $group; // Save group
                        } else {
                            // fallback
                            $group->visibleParentField = self::getChildForVisibleField($group->visibleParentField);
                            $MJTC_data[$fieldname][] = $group;
                        }
                    }
                } elseif (is_object($decoded)) {
                    // Old system: simple condition
                    $decoded->visibleParentField = self::getChildForVisibleField($decoded->visibleParentField);
                    $MJTC_data[$fieldname][] = $decoded;
                }
            }
        }

        return $MJTC_data;
    }

    static function getChildForVisibleField($field) {
        $field = esc_sql($field);
        $oldField = MJTC_majesticsupportphplib::MJTC_explode(',',$field);
        $newField = $oldField[sizeof($oldField) - 1];
        $query = "SELECT visible_field FROM ". majesticsupport::$_db->prefix ."mjtc_support_fieldsordering WHERE  field = '". $newField ."'";
        $queryRun = majesticsupport::$_db->get_var($query);
        if (isset($queryRun) && $queryRun != '') {
            $MJTC_data = MJTC_majesticsupportphplib::MJTC_explode(',',$queryRun);
            foreach ($MJTC_data as $MJTC_value) {
                $field = $field.','.$MJTC_value;
                $field = Self::getChildForVisibleField($field);
            }
        }        
        return $field;
    }    

    function getHtmlForORRow() {
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $nonce, 'get-html-for-or-row') ) {
            die( 'Security check Failed' );
        }
        
        $orid = MJTC_request::MJTC_getVar("nextorid");
        $fieldfor = MJTC_request::MJTC_getVar("fieldfor");
        $formid = MJTC_request::MJTC_getVar("formid");
        $field = MJTC_request::MJTC_getVar("field");
        $id = MJTC_request::MJTC_getVar("id");
        $MJTC_equalnotequal = array(
            (object) array('id' => 1, 'text' => esc_html(__('Equal', 'majestic-support'))),
            (object) array('id' => 0, 'text' => esc_html(__('Not Equal', 'majestic-support'))));
        $html = "
        <div id='mjtc_or_row_". $orid ."'>
            <div class='mjtc-form-visible-subheading'>
                ". esc_html(__('OR', 'majestic-support')) ."
            </div>
            <div class='mjtc-form-value'>
                ". wp_kses(MJTC_formfield::MJTC_hidden('visibleLogic[]', 'OR'), MJTC_ALLOWED_TAGS) ."
                ". wp_kses(MJTC_formfield::MJTC_select('visibleParent[]', MJTC_includer::MJTC_getModel('fieldordering')->getFieldsForVisibleCombobox($fieldfor, $formid,$field,$id), '', esc_html(__('Select Parent', 'majestic-support')), array('class' => 'inputbox mjtc-form-select-field mjtc-form-input-field-visible', 'onchange' => 'getChildForVisibleCombobox(this.value, '.$orid.');getConditionsForVisibleCombobox(this.value, '.$orid.');')), MJTC_ALLOWED_TAGS) ."
                <span class='visibleValueWrp'>
                    ". wp_kses(MJTC_formfield::MJTC_select('visibleValue[]', '', '', esc_html(__('Select Child', 'majestic-support')), array('class' => 'inputbox one mjtc-form-select-field mjtc-form-input-field-visible')), MJTC_ALLOWED_TAGS) ."
                </span>
                <span class='visibleConditionWrp'>
                    ". wp_kses(MJTC_formfield::MJTC_select('visibleCondition[]', $MJTC_equalnotequal, '', esc_html(__('Select Condition', 'majestic-support')), array('class' => 'inputbox one mjtc-form-select-field mjtc-form-input-field-visible')), MJTC_ALLOWED_TAGS) ."
                </span>
                <div class='mjtc-visible-conditions-body-row'>
                    <div class='mjtc-visible-conditions-body-value'>
                        <span onclick=\"deleteOrRow('mjtc_or_row_". $orid ."')\" class='mjtc-visible-conditions-delbtn'>
                            <img class='input-field-remove-img' src='" . MJTC_PLUGIN_URL . "includes/images/delete-2.png' />
                        </span>
                    </div>
                </div>
            </div>
        </div>
        ";
        $html = MJTC_majesticsupportphplib::MJTC_htmlentities($html);
        return wp_json_encode($html);
    }

    function getHtmlForANDRow() {
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $nonce, 'get-html-for-and-row') ) {
            die( 'Security check Failed' );
        }
        
        $andid = MJTC_request::MJTC_getVar("nextandid");
        $orid = MJTC_request::MJTC_getVar("nextorid");
        $fieldfor = MJTC_request::MJTC_getVar("fieldfor");
        $formid = MJTC_request::MJTC_getVar("formid");
        $field = MJTC_request::MJTC_getVar("field");
        $id = MJTC_request::MJTC_getVar("id");
        $MJTC_equalnotequal = array(
            (object) array('id' => 1, 'text' => esc_html(__('Equal', 'majestic-support'))),
            (object) array('id' => 0, 'text' => esc_html(__('Not Equal', 'majestic-support'))));

        $html = "
        <div class='mjtc-form-visible-andwrp' id='mjtc_and_row_". $andid ."'>
            <div class='mjtc-form-visible-subheading'>
                ". esc_html(__('AND', 'majestic-support')) ."
            </div>
            <div class='mjtc-form-wrapper mjtc-form-visible-wrapper' >
                <div class='mjtc-form-value' id='mjtc_or_row_". $orid ."'>
                    ". wp_kses(MJTC_formfield::MJTC_hidden('visibleLogic[]', 'AND'), MJTC_ALLOWED_TAGS) ."
                    ". wp_kses(MJTC_formfield::MJTC_select('visibleParent[]', MJTC_includer::MJTC_getModel('fieldordering')->getFieldsForVisibleCombobox($fieldfor, $formid,$field,$id), '', esc_html(__('Select Parent', 'majestic-support')), array('class' => 'inputbox mjtc-form-select-field mjtc-form-input-field-visible', 'onchange' => 'getChildForVisibleCombobox(this.value, '.$orid.');getConditionsForVisibleCombobox(this.value, '.$orid.');')), MJTC_ALLOWED_TAGS) ."
                    <span class='visibleValueWrp'>
                        ". wp_kses(MJTC_formfield::MJTC_select('visibleValue[]', '', '', esc_html(__('Select Child', 'majestic-support')), array('class' => 'inputbox one mjtc-form-select-field mjtc-form-input-field-visible')), MJTC_ALLOWED_TAGS) ."
                    </span>
                    <span class='visibleConditionWrp'>
                        ". wp_kses(MJTC_formfield::MJTC_select('visibleCondition[]', $MJTC_equalnotequal, '', esc_html(__('Select Condition', 'majestic-support')), array('class' => 'inputbox one mjtc-form-select-field mjtc-form-input-field-visible')), MJTC_ALLOWED_TAGS) ."
                    </span>
                    <div class='mjtc-visible-conditions-body-row'>
                        <div class='mjtc-visible-conditions-body-value'>
                            <span onclick=\"deleteOrRow('mjtc_or_row_". $orid ."')\" class='mjtc-visible-conditions-delbtn'>
                                <img class='input-field-remove-img' src='" . MJTC_PLUGIN_URL . "includes/images/delete-2.png' />
                            </span>
                        </div>
                    </div>
                </div>
                <div class='mjtc-form-visible-or-row'></div>
                <div class='mjtc-visible-conditions-addbtn-wrp'>
                    <span class='mjtc-form-visible-addmore' onclick='getMoreORRow(this, ". esc_js($fieldfor) .", ". esc_js($formid) .")'>
                        <img alt='". esc_html(__('OR', 'majestic-support')) ."' class='input-field-remove-img' src='". esc_url(MJTC_PLUGIN_URL) ."includes/images/plus-icon.png'>
                        ". esc_html(__('OR', 'majestic-support')) ."
                    </span>
                </div>
            </div>
        </div>
        ";
        $html = MJTC_majesticsupportphplib::MJTC_htmlentities($html);
        return wp_json_encode($html);
    }

}

?>
