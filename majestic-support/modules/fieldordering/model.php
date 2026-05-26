<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_fieldorderingModel {

    function getFieldOrderingForList($MJTC_fieldfor) {
        if(!is_numeric($MJTC_fieldfor)){
            return false;
        }
        $MJTC_formid = majesticsupport::$_data['formid'];
        if (isset($MJTC_formid) && $MJTC_formid != null) {
            $MJTC_inquery = " AND multiformid = ".intval($MJTC_formid);
        }
    	else{
            $MJTC_inquery = " AND multiformid = ".intval(MJTC_includer::MJTC_getModel('ticket')->getDefaultMultiFormId());
    	}

        // Data
        $MJTC_query = "SELECT * FROM `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` WHERE fieldfor = ".esc_sql($MJTC_fieldfor);
        $MJTC_query .= $MJTC_inquery." ORDER BY ordering ";

        majesticsupport::$_data[0] = majesticsupport::$_db->get_results($MJTC_query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        return;
    }

    function changePublishStatus($MJTC_id, $MJTC_status) {
        if (!is_numeric($MJTC_id))
            return false;
        if ($MJTC_status == 'publish') {
            $MJTC_query = "UPDATE `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` SET published = 1 WHERE id = " . esc_sql($MJTC_id) . " AND cannotunpublish = 0";
            majesticsupport::$_db->query($MJTC_query);
            if (majesticsupport::$_db->last_error != null) {
                MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
            }
            MJTC_message::MJTC_setMessage(esc_html(__('Field mark as published', 'majestic-support')),'updated');
        } elseif ($MJTC_status == 'unpublish') {
            $MJTC_query = "UPDATE `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` SET published = 0 WHERE id = " . esc_sql($MJTC_id) . " AND cannotunpublish = 0";
            majesticsupport::$_db->query($MJTC_query);
            if (majesticsupport::$_db->last_error != null) {
                MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
            }
            MJTC_message::MJTC_setMessage(esc_html(__('Field mark as unpublished', 'majestic-support')),'updated');
        }
        return;
    }

    function changeVisitorPublishStatus($MJTC_id, $MJTC_status) {
        if (!is_numeric($MJTC_id))
            return false;
        if ($MJTC_status == 'publish') {
            $MJTC_query = "SELECT adminonly FROM " . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering WHERE id = " . esc_sql($MJTC_id);
            $MJTC_adminonly = majesticsupport::$_db->get_var($MJTC_query);
            if(!empty($MJTC_adminonly)){
                MJTC_message::MJTC_setMessage(esc_html(__('Field cannot be mark as published', 'majestic-support')),'error');
            }else{
                $MJTC_query = "UPDATE `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` SET isvisitorpublished = 1 WHERE id = " . esc_sql($MJTC_id) . " AND cannotunpublish = 0";
                majesticsupport::$_db->query($MJTC_query);
                if (majesticsupport::$_db->last_error != null) {
                    MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
                }
                MJTC_message::MJTC_setMessage(esc_html(__('Field mark as published', 'majestic-support')),'updated');
            }
        } elseif ($MJTC_status == 'unpublish') {
            $MJTC_query = "UPDATE `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` SET isvisitorpublished = 0 WHERE id = " . esc_sql($MJTC_id) . " AND cannotunpublish = 0";
            majesticsupport::$_db->query($MJTC_query);
            if (majesticsupport::$_db->last_error != null) {
                MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
            }
            MJTC_message::MJTC_setMessage(esc_html(__('Field mark as unpublished', 'majestic-support')),'updated');
        }
        return;
    }

    function changeRequiredStatus($MJTC_id, $MJTC_status) {
        if (!is_numeric($MJTC_id))
            return false;

        if ($MJTC_status == 'required') {
            $MJTC_query = "UPDATE `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` SET required = 1 WHERE id = " . esc_sql($MJTC_id) . " AND cannotunpublish = 0";
            majesticsupport::$_db->query($MJTC_query);
            if (majesticsupport::$_db->last_error != null) {
                MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
            }
            MJTC_message::MJTC_setMessage(esc_html(__('Field mark as required', 'majestic-support')),'updated');
        } elseif ($MJTC_status == 'unrequired') {
            $MJTC_query = "UPDATE `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` SET required = 0 WHERE id = " . esc_sql($MJTC_id) . " AND cannotunpublish = 0";
            majesticsupport::$_db->query($MJTC_query);
            if (majesticsupport::$_db->last_error != null) {
                MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
            }
            MJTC_message::MJTC_setMessage(esc_html(__('Field mark as not required', 'majestic-support')),'updated');
        }
        return;
    }

    function changeOrder($MJTC_id, $MJTC_action) {
        if (!is_numeric($MJTC_id))
            return false;
        if ($MJTC_action == 'down') {
            $MJTC_query = "UPDATE `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` AS f1, `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` AS f2
                        SET f1.ordering = f1.ordering - 1 WHERE f1.ordering = f2.ordering + 1 AND f1.fieldfor = f2.fieldfor
                        AND f2.id = " . esc_sql($MJTC_id);
            majesticsupport::$_db->query($MJTC_query);
            $MJTC_query = " UPDATE `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` SET ordering = ordering + 1 WHERE id = " . esc_sql($MJTC_id);
            majesticsupport::$_db->query($MJTC_query);
            MJTC_message::MJTC_setMessage(esc_html(__('Field ordering down', 'majestic-support')),'updated');
        } elseif ($MJTC_action == 'up') {
            $MJTC_query = "UPDATE `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` AS f1, `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` AS f2 SET f1.ordering = f1.ordering + 1
                        WHERE f1.ordering = f2.ordering - 1 AND f1.fieldfor = f2.fieldfor AND f2.id = " . esc_sql($MJTC_id);
            majesticsupport::$_db->query($MJTC_query);
            $MJTC_query = " UPDATE `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` SET ordering = ordering - 1 WHERE id = " . esc_sql($MJTC_id);
            majesticsupport::$_db->query($MJTC_query);
            MJTC_message::MJTC_setMessage(esc_html(__('Field ordering up', 'majestic-support')),'updated');
        }
        return;
    }

    function getFieldsOrderingforForm($MJTC_fieldfor,$MJTC_formid='') {
        if (!is_numeric($MJTC_fieldfor))
            return false;
        if (MJTC_includer::MJTC_getObjectClass('user')->MJTC_isguest()) {
            $MJTC_published = ' isvisitorpublished = 1 ';
        } else {
            $MJTC_published = ' published = 1 ';
        }
	    if(!isset($MJTC_formid) || $MJTC_formid==''){
		    $MJTC_formid = MJTC_includer::MJTC_getModel('ticket')->getDefaultMultiFormId();
	    }
        if(!is_numeric($MJTC_formid)) return false;
        // admin only check
        $MJTC_adminonly = '';
        if ($MJTC_fieldfor == 1) {
            if( in_array('agent',majesticsupport::$_active_addons) ){
                $MJTC_agent = MJTC_includer::MJTC_getModel('agent')->isUserStaff();
            }else{
                $MJTC_agent = false;
            }
            if(!is_admin() && !$MJTC_agent){
                $MJTC_adminonly = ' AND adminonly != 1 ';
            }
        }
        $MJTC_query = "SELECT  * FROM `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` WHERE ".$MJTC_published." AND fieldfor =  " . esc_sql($MJTC_fieldfor);
        if ($MJTC_fieldfor == 1) {
            $MJTC_query .= " AND multiformid =  " . intval($MJTC_formid);
        }
        $MJTC_query .=  esc_sql($MJTC_adminonly) . " ORDER BY ordering ";
        majesticsupport::$_data['fieldordering'] = majesticsupport::$_db->get_results($MJTC_query);
        return;
    }

    function checkIsFieldRequired($MJTC_field,$MJTC_formid='') {
        if(!isset($MJTC_formid) || $MJTC_formid==''){
            $MJTC_formid = MJTC_includer::MJTC_getModel('ticket')->getDefaultMultiFormId();
        }
        if (MJTC_includer::MJTC_getObjectClass('user')->MJTC_isguest()) {
            $MJTC_published = ' isvisitorpublished = 1 ';
        } else {
            $MJTC_published = ' published = 1 ';
        }
        $MJTC_query = "SELECT required FROM `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` WHERE ".$MJTC_published." AND fieldfor =  1 AND  field =  '".esc_sql($MJTC_field)."' AND multiformid =  " . intval($MJTC_formid);
        $MJTC_required = majesticsupport::$_db->get_var($MJTC_query);
        return $MJTC_required;
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
                $MJTC_query = "SELECT max(ordering) FROM " . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering WHERE fieldfor=".esc_sql($MJTC_data['fieldfor']);
                $MJTC_var = majesticsupport::$_db->get_var($MJTC_query);
                $MJTC_data['ordering'] = $MJTC_var + 1;
                if(isset($MJTC_data['userfieldtype']) && ($MJTC_data['userfieldtype'] == 'file' || $MJTC_data['userfieldtype'] == 'termsandconditions' ) ){
                    $MJTC_data['cannotsearch'] = 1;
                    $MJTC_data['cannotshowonlisting'] = 1;
                }else{
                    $MJTC_data['cannotshowonlisting'] = 0;
                    $MJTC_data['cannotsearch'] = 0;
                }
                $MJTC_query = "SELECT max(id) FROM " . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering ";
                $MJTC_var = majesticsupport::$_db->get_var($MJTC_query);
                $MJTC_var = $MJTC_var + 1;
                $MJTC_fieldname = 'ufield_'.esc_attr($MJTC_var);
            }else{
                $MJTC_fieldname = !empty($MJTC_data['field']) ? $MJTC_data['field'] : '';
            }
            if ($MJTC_data['userfieldtype'] == 'termsandconditions') { // only for terms and conditions
                $MJTC_data['required'] = 1;
            }

            $MJTC_params = array();
            //code for depandetn field
            if (isset($MJTC_data['userfieldtype']) && $MJTC_data['userfieldtype'] == 'depandant_field') {
                if ($MJTC_data['id'] != '') {
                    //to handle edit case of depandat field
                    $MJTC_data['arraynames'] = $MJTC_data['arraynames2'];
                }
                if (!empty($MJTC_data['arraynames'])) {
                    $MJTC_valarrays = MJTC_majesticsupportphplib::MJTC_explode('_MS_Unique_88a9e3_', $MJTC_data['arraynames']);
                    $MJTC_empty_flag = 0;
                    $MJTC_key_flag = '';
                    foreach ($MJTC_valarrays as $MJTC_key => $MJTC_value) {
                        if($MJTC_key != $MJTC_key_flag){
                            $MJTC_key_flag = $MJTC_key;
                            $MJTC_empty_flag = 0;
                        }
                        $MJTC_keyvalue = $MJTC_value;
                        $MJTC_value = MJTC_majesticsupportphplib::MJTC_str_replace(' ','__',$MJTC_value);
                        $MJTC_value = MJTC_majesticsupportphplib::MJTC_str_replace('.','___',$MJTC_value);
                        // This check was previously commented out, but caused errors when child values were empty.
                        // Uncommented and handled properly by Hamza to avoid runtime issues with empty inputs.
                        if ( isset($MJTC_data[$MJTC_value]) && $MJTC_data[$MJTC_value] != null) {
                            $MJTC_keyvalue = MJTC_majesticsupportphplib::MJTC_htmlentities($MJTC_keyvalue);
                            $MJTC_params[$MJTC_keyvalue] = array_filter($MJTC_data[$MJTC_value]);
                            $MJTC_empty_flag = 1;
                        }
                    }
                    if($MJTC_empty_flag == 0){
                        MJTC_message::MJTC_setMessage(esc_html(__('Please Insert At least one value for every option', 'majestic-support')), 'error');
                        return 2 ;
                    }
                }
                $MJTC_flagvar = $this->updateParentField($MJTC_data['parentfield'], $MJTC_fieldname, $MJTC_data['fieldfor']);
                if ($MJTC_flagvar == false) {
                    MJTC_message::MJTC_setMessage(esc_html(__('Parent field has not been stored', 'majestic-support')), 'error');
                }
            }
            if (!empty($MJTC_data['values'])) {
                foreach ($MJTC_data['values'] as $MJTC_key => $MJTC_value) {
                    if ($MJTC_value != null) {
                        $MJTC_value = MJTC_majesticsupportphplib::MJTC_str_replace('[','',$MJTC_value);
                        $MJTC_value = MJTC_majesticsupportphplib::MJTC_str_replace(']','',$MJTC_value);
                        $MJTC_params[] = MJTC_majesticsupportphplib::MJTC_trim($MJTC_value);
                    }
                }
            }

            $MJTC_visible = [];

            if (isset($MJTC_data['visibleParent']) && is_array($MJTC_data['visibleParent'])) {

                // new start

                if (!empty($MJTC_data['id'])) {
                    $MJTC_query = "SELECT id, visible_field FROM " . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering WHERE visible_field LIKE '%" . esc_sql($MJTC_fieldname) . "%' AND multiformid = ".intval($MJTC_data['multiformid']);
                    $MJTC_query_results = majesticsupport::$_db->get_results($MJTC_query);
                    
                    if (!empty($MJTC_query_results)) {
                        foreach ($MJTC_query_results as $MJTC_query_result) {
                            $MJTC_query_fieldname = $MJTC_query_result->visible_field;
                            $MJTC_query_fieldname = MJTC_majesticsupportphplib::MJTC_str_replace(',' . $MJTC_fieldname, '', $MJTC_query_fieldname);
                            $MJTC_query_fieldname = MJTC_majesticsupportphplib::MJTC_str_replace($MJTC_fieldname, '', $MJTC_query_fieldname);
                            $MJTC_query = "UPDATE `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` SET visible_field = '" . esc_sql($MJTC_query_fieldname) . "' WHERE id = " . esc_sql($MJTC_query_result->id) . " AND multiformid = ".intval($MJTC_data['multiformid']);
                            majesticsupport::$_db->query($MJTC_query);
                        }
                    }
                }

                // new end
                $MJTC_visibleParents = $MJTC_data['visibleParent'];
                $MJTC_visibleValues = isset($MJTC_data['visibleValue']) ? $MJTC_data['visibleValue'] : [];
                $MJTC_visibleConditions = isset($MJTC_data['visibleCondition']) ? $MJTC_data['visibleCondition'] : [];
                $MJTC_visibleLogics = isset($MJTC_data['visibleLogic']) ? $MJTC_data['visibleLogic'] : [];

                $MJTC_final = []; // Final grouped structure
                $MJTC_currentAndGroup = []; // Current AND group

                foreach ($MJTC_visibleParents as $MJTC_index => $MJTC_parentFieldId) {
                    if (
                        isset($MJTC_visibleParents[$MJTC_index]) && $MJTC_visibleParents[$MJTC_index] !== '' &&
                        isset($MJTC_visibleValues[$MJTC_index]) && $MJTC_visibleValues[$MJTC_index] !== '' &&
                        isset($MJTC_visibleConditions[$MJTC_index]) && $MJTC_visibleConditions[$MJTC_index] !== ''
                    ) {
                        $MJTC_fieldname = $MJTC_fieldname ?? ''; // Just in case
                        $MJTC_logic = isset($MJTC_visibleLogics[$MJTC_index]) ? $MJTC_visibleLogics[$MJTC_index] : '';

                        // Build the current row
                        $MJTC_row = [
                            'visibleParentField' => $MJTC_fieldname,
                            'visibleParent' => $MJTC_visibleParents[$MJTC_index],
                            'visibleCondition' => $MJTC_visibleConditions[$MJTC_index],
                            'visibleValue' => $MJTC_visibleValues[$MJTC_index],
                            'visibleLogic' => $MJTC_logic
                        ];

                        if ($MJTC_logic === 'AND') {
                            // New AND group starts
                            if (!empty($MJTC_currentAndGroup)) {
                                $MJTC_final[] = $MJTC_currentAndGroup;
                                $MJTC_currentAndGroup = [];
                            }
                            $MJTC_currentAndGroup[] = $MJTC_row;
                        } else {
                            // Continue current AND group (OR rows)
                            $MJTC_currentAndGroup[] = $MJTC_row;
                        }

                        // --- your database update code ---
                        $MJTC_query = "SELECT visible_field FROM " . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering WHERE field = '" . esc_sql($MJTC_visibleParents[$MJTC_index]) . "' AND multiformid = ".intval($MJTC_data['multiformid']);
                        $MJTC_old_fieldname = majesticsupport::$_db->get_var($MJTC_query);
                        $MJTC_new_fieldname = $MJTC_fieldname;

                        if (!empty($MJTC_data['id'])) {
                            $MJTC_old_fieldname = MJTC_majesticsupportphplib::MJTC_str_replace(',' . $MJTC_fieldname, '', $MJTC_old_fieldname);
                            $MJTC_old_fieldname = MJTC_majesticsupportphplib::MJTC_str_replace($MJTC_fieldname, '', $MJTC_old_fieldname);
                        }

                        if (!empty($MJTC_old_fieldname)) {
                            $MJTC_new_fieldname = $MJTC_old_fieldname . ',' . $MJTC_new_fieldname;
                        }

                        $MJTC_query = "UPDATE `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` SET visible_field = '" . esc_sql($MJTC_new_fieldname) . "' WHERE field = '" . esc_sql($MJTC_visibleParents[$MJTC_index]) . "' AND multiformid = ".intval($MJTC_data['multiformid']);
                        majesticsupport::$_db->query($MJTC_query);

                        if (majesticsupport::$_db->last_error != null) {
                            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
                        }
                    }
                }
                // After finishing all rows
                if (!empty($MJTC_currentAndGroup)) {
                    $MJTC_final[] = $MJTC_currentAndGroup;
                }

                // Now sanitize and save the final nested array
                $MJTC_visible_array = array_map(array($this, 'sanitize_custom_field'), $MJTC_final);
                $MJTC_visibleparams = wp_json_encode(stripslashes_deep($MJTC_visible_array));

            } else if (!empty($MJTC_data['id'])) {
                if ($MJTC_data['fieldfor'] != 3) {
                    $MJTC_data['visibleparams'] = '';
                    // If editing old field
                    $MJTC_query = "SELECT id, visible_field FROM " . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering WHERE visible_field LIKE '%" . esc_sql($MJTC_fieldname) . "%' AND multiformid = ".intval($MJTC_data['multiformid']);
                    $MJTC_query_results = majesticsupport::$_db->get_results($MJTC_query);
                    if (!empty($MJTC_query_results)) {
                        foreach ($MJTC_query_results as $MJTC_query_result) {
                            if (isset($MJTC_query_result)) {
                                $MJTC_query_fieldname = $MJTC_query_result->visible_field;
                                $MJTC_query_fieldname = MJTC_majesticsupportphplib::MJTC_str_replace(',' . $MJTC_fieldname, '', $MJTC_query_fieldname);
                                $MJTC_query_fieldname = MJTC_majesticsupportphplib::MJTC_str_replace($MJTC_fieldname, '', $MJTC_query_fieldname);
                                $MJTC_query = "UPDATE `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` SET visible_field = '" . esc_sql($MJTC_query_fieldname) . "' WHERE id = " . esc_sql($MJTC_query_result->id);
                                majesticsupport::$_db->query($MJTC_query);
                            }
                        }
                    }
                }
            }
            if (isset($MJTC_data['userfieldtype']) && $MJTC_data['userfieldtype'] == 'termsandconditions') { // to manage terms and condition field
                if ($MJTC_data['termsandconditions_linktype'] == 1) {
                    $MJTC_params['termsandconditions_link'] = $MJTC_data['termsandconditions_link'];
                } else if ($MJTC_data['termsandconditions_linktype'] == 2) {
                    $MJTC_params['termsandconditions_page'] = $MJTC_data['termsandconditions_page'];
                }
                $MJTC_params['termsandconditions_text'] = $MJTC_data['termsandconditions_text'];
                $MJTC_params['termsandconditions_linktype'] = $MJTC_data['termsandconditions_linktype'];
            }

                // $MJTC_params = wp_json_encode($MJTC_params);
                $MJTC_params_array = array_map(array($this,'sanitize_custom_field'), $MJTC_params);
                $MJTC_userfieldparams = wp_json_encode(stripslashes_deep($MJTC_params_array));

            //}
            // for default value
            $MJTC_data['defaultvalue'] = '';
            if($MJTC_data['userfieldtype'] == "combo" || $MJTC_data['userfieldtype'] == "radio" || $MJTC_data['userfieldtype'] == "multiple" || $MJTC_data['userfieldtype'] == "checkbox" || $MJTC_data['userfieldtype'] == "depandant_field") {
                $MJTC_data['defaultvalue'] = !empty($MJTC_data['defaultvalue_select']) ? $MJTC_data['defaultvalue_select'] : '';
            } else {
                $MJTC_data['defaultvalue'] = !empty($MJTC_data['defaultvalue_input']) ? $MJTC_data['defaultvalue_input'] : '';
            }
        }else{
            $MJTC_fieldname = $MJTC_data['field'];
            $MJTC_data['userfieldtype'] = '';
            $MJTC_data['defaultvalue'] = !empty($MJTC_data['defaultvalue_input']) ? $MJTC_data['defaultvalue_input'] : '';
            // get data for system fields of type terms ans conditions
            if (in_array($MJTC_data['field'], ['termsandconditions1', 'termsandconditions2', 'termsandconditions3'])) { // to manage terms and condition field
                if ($MJTC_data['termsandconditions_linktype'] == 1) {
                    $MJTC_params['termsandconditions_link'] = $MJTC_data['termsandconditions_link'];
                } else if ($MJTC_data['termsandconditions_linktype'] == 2) {
                    $MJTC_params['termsandconditions_page'] = $MJTC_data['termsandconditions_page'];
                }
                $MJTC_params['termsandconditions_text'] = $MJTC_data['termsandconditions_text'];
                $MJTC_params['termsandconditions_linktype'] = $MJTC_data['termsandconditions_linktype'];
                $MJTC_params_array = array_map(array($this,'sanitize_custom_field'), $MJTC_params);
                $MJTC_userfieldparams = wp_json_encode(stripslashes_deep($MJTC_params_array));
            }
        }

        // for adminonly
        if(!empty($MJTC_data['adminonly'])){
            $MJTC_data['isvisitorpublished'] = 0;
            $MJTC_data['search_user'] = 0;
        }

        $MJTC_data['field'] = $MJTC_fieldname;
        $MJTC_data['section'] = 10;

        /*if (!empty($MJTC_data['depandant_field']) && $MJTC_data['depandant_field'] != null ) {

            $MJTC_query = "SELECT * FROM " . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering where
            field = '". esc_sql($MJTC_data['depandant_field'])."'";
            $MJTC_child = majesticsupport::$_db->get_row($MJTC_query);
            $MJTC_parent = $MJTC_data;
            $MJTC_flagvar = $this->updateChildField($MJTC_parent, $MJTC_child);
            if ($MJTC_flagvar == false) {
                MJTC_message::MJTC_setMessage(esc_html(__('Child fields has not been stored', 'majestic-support')), 'error');
            }
        }*/

        $MJTC_row = MJTC_includer::MJTC_getTable('fieldsordering');
        $MJTC_data = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($MJTC_data);// remove slashes with quotes.
        if (!empty($MJTC_userfieldparams)) {
            $MJTC_data['userfieldparams'] = $MJTC_userfieldparams;
        }
        if (!empty($MJTC_visibleparams)) {
            $MJTC_data['visibleparams'] = $MJTC_visibleparams;
        }
        $MJTC_error = 0;
        if (!$MJTC_row->bind($MJTC_data)) {
            $MJTC_error = 1;
        }
        if (!$MJTC_row->store()) {
            $MJTC_error = 1;
        }

        if ($MJTC_error == 1) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
            MJTC_message::MJTC_setMessage(esc_html(__('Field has not been stored', 'majestic-support')), 'error');
        } else {
            MJTC_message::MJTC_setMessage(esc_html(__('Field has been stored', 'majestic-support')), 'updated');
            // update the dependent fields data if exist
            if (!empty($MJTC_data['depandant_field']) && $MJTC_data['depandant_field'] != null ) {

                $MJTC_query = "SELECT * FROM " . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering where
                field = '". esc_sql($MJTC_data['depandant_field'])."'";
                $MJTC_child = majesticsupport::$_db->get_row($MJTC_query);
                
                /* get parent saved data */
                $MJTC_query = "SELECT * FROM " . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering where
                id = '". esc_sql($MJTC_data['id'])."'";
                $MJTC_parent = majesticsupport::$_db->get_row($MJTC_query);
                /* get parent saved data */
                
                // $MJTC_parent = $MJTC_data;
                $MJTC_flagvar = $this->updateChildField($MJTC_parent, $MJTC_child);
                if ($MJTC_flagvar == false) {
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
        $MJTC_inquery = '';
        $MJTC_clasue = '';
        if(isset($MJTC_data['fieldtitle']) && $MJTC_data['fieldtitle'] != null){
            $MJTC_inquery .= $MJTC_clasue." fieldtitle = '". esc_sql($MJTC_data['fieldtitle']) ."'";
            $MJTC_clasue = ' , ';
        }
        if(isset($MJTC_data['published']) && $MJTC_data['published'] != null){
            $MJTC_inquery .= $MJTC_clasue." published = ". esc_sql($MJTC_data['published']);
            $MJTC_clasue = ' , ';
        }
        if(isset($MJTC_data['isvisitorpublished']) && $MJTC_data['isvisitorpublished'] != null){
            $MJTC_inquery .= $MJTC_clasue." isvisitorpublished = ". esc_sql($MJTC_data['isvisitorpublished']);
            $MJTC_clasue = ' , ';
        }
        if(isset($MJTC_data['placeholder']) && $MJTC_data['placeholder'] != null){
            $MJTC_inquery .= $MJTC_clasue." placeholder = '". esc_sql($MJTC_data['placeholder']) ."'";
            $MJTC_clasue = ' , ';
        }
        if(isset($MJTC_data['description']) && $MJTC_data['description'] != null){
            $MJTC_inquery .= $MJTC_clasue." description = '". esc_sql($MJTC_data['description']) . "'";
            $MJTC_clasue = ' , ';
        }
        if(isset($MJTC_data['required']) && $MJTC_data['required'] != null){
            $MJTC_inquery .= $MJTC_clasue." required = ". esc_sql($MJTC_data['required']);
            $MJTC_clasue = ' , ';
        }
        if(isset($MJTC_data['search_user']) && $MJTC_data['search_user'] != null){
            $MJTC_inquery .= $MJTC_clasue." search_user = ". esc_sql($MJTC_data['search_user']);
            $MJTC_clasue = ' , ';
        }
        if(isset($MJTC_data['search_admin']) && $MJTC_data['search_admin'] != null){
            $MJTC_inquery .= $MJTC_clasue." search_admin = ". esc_sql($MJTC_data['search_admin']);
            $MJTC_clasue = ' , ';
        }
        if(isset($MJTC_data['showonlisting']) && $MJTC_data['showonlisting'] != null){
            $MJTC_inquery .= $MJTC_clasue." showonlisting = ". esc_sql($MJTC_data['showonlisting']);
            $MJTC_clasue = ' , ';
        }

        $MJTC_query = "UPDATE `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` SET ".$MJTC_inquery." WHERE id = " . esc_sql($MJTC_data['id']) ;
        majesticsupport::$_db->query($MJTC_query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        MJTC_message::MJTC_setMessage(esc_html(__('Field has been updated', 'majestic-support')),'updated');

        return;
    }

    function updateParentField($MJTC_parentfield, $MJTC_field, $MJTC_fieldfor) {
        if(!is_numeric($MJTC_fieldfor)) return false;
        if(!is_numeric($MJTC_parentfield)) return false;
        if(empty($MJTC_field)) return false;

        $MJTC_query = "UPDATE `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` SET depandant_field = '" . esc_sql($MJTC_field) . "' WHERE id = " . esc_sql($MJTC_parentfield) . " AND fieldfor = " . esc_sql($MJTC_fieldfor);
        majesticsupport::$_db->query($MJTC_query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        return true;
    }

    function updateChildField($MJTC_parent, $MJTC_child){
        if(!is_numeric($MJTC_child->id)) return false;
        $MJTC_childfieldparams = json_decode( $MJTC_child->userfieldparams,TRUE);
        $MJTC_parentfieldparams = json_decode( $MJTC_parent->userfieldparams,TRUE);

        $MJTC_childNew = [];

        foreach ($MJTC_parentfieldparams as $MJTC_parentKey => $MJTC_parentValue) {
            $MJTC_childKeys = is_array($MJTC_parentValue) ? $MJTC_parentValue : [$MJTC_parentValue];

            foreach ($MJTC_childKeys as $MJTC_childKey) {
                if (isset($MJTC_childfieldparams[$MJTC_childKey])) {
                    $MJTC_childNew[$MJTC_childKey] = $MJTC_childfieldparams[$MJTC_childKey];
                } else {
                    $MJTC_childNew[$MJTC_childKey] = '';
                }
            }
        }
        $MJTC_childNew = wp_json_encode( $MJTC_childNew );
        $MJTC_child->userfieldparams = $MJTC_childNew;
        $MJTC_query = "UPDATE `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` SET userfieldparams = '" . esc_sql($MJTC_childNew) . "' WHERE id = " . esc_sql($MJTC_child->id);
        majesticsupport::$_db->query($MJTC_query);
        if (majesticsupport::$_db->last_error != null) {

            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        return true;
    }

    function getFieldsForComboByFieldFor() {
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'get-fields-for-combo-by-fieldfor') ) {
            die( 'Security check Failed' );
        }
        $MJTC_formid = MJTC_request::MJTC_getVar('formid');
        $MJTC_fieldfor = MJTC_request::MJTC_getVar('fieldfor');
        $MJTC_parentfield = MJTC_request::MJTC_getVar('parentfield');
        if(!is_numeric($MJTC_fieldfor)) return false;
        $MJTC_wherequery = '';
        if(isset($MJTC_parentfield) && $MJTC_parentfield !='' ){
            $MJTC_query = "SELECT id FROM " . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering WHERE fieldfor = ".esc_sql($MJTC_fieldfor)." AND (userfieldtype = 'radio' OR userfieldtype = 'combo'OR userfieldtype = 'depandant_field') AND depandant_field = '" . esc_sql($MJTC_parentfield) . "' ";
            $MJTC_parent = majesticsupport::$_db->get_var($MJTC_query);
            $MJTC_wherequery = ' OR id = '.esc_sql($MJTC_parent);
        }
        $MJTC_query = "SELECT fieldtitle AS text ,id FROM " . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering WHERE fieldfor = ".esc_sql($MJTC_fieldfor)." AND multiformid = ".intval($MJTC_formid)." AND (userfieldtype = 'radio' OR userfieldtype = 'combo' OR userfieldtype = 'depandant_field') AND (depandant_field = '' ".esc_sql($MJTC_wherequery)." ) ";
        $MJTC_data = majesticsupport::$_db->get_results($MJTC_query);
        if(isset($MJTC_parentfield) && $MJTC_parentfield !='' ){
            $MJTC_query = "SELECT id FROM " . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering WHERE fieldfor = ".esc_sql($MJTC_fieldfor)." AND (userfieldtype = 'radio' OR userfieldtype = 'combo'OR userfieldtype = 'depandant_field') AND depandant_field = '" . esc_sql($MJTC_parentfield) . "' ";
            $MJTC_parent = majesticsupport::$_db->get_var($MJTC_query);
        }
        $MJTC_nonce = wp_create_nonce("get-section-to-fill-values-".$MJTC_fieldfor);
        $msFunction = 'getDataOfSelectedField("'.$MJTC_nonce.'");';
        $MJTC_html = MJTC_formfield::MJTC_select('parentfield', $MJTC_data, (isset($MJTC_parent) && $MJTC_parent !='') ? $MJTC_parent : '', esc_html(__('Select', 'majestic-support')) .'&nbsp;'. esc_html(__('Parent Field', 'majestic-support')), array('onchange' => $msFunction, 'class' => 'inputbox one mjtc-form-select-field', 'data-validation' => 'required'));
        $MJTC_html = MJTC_majesticsupportphplib::MJTC_htmlentities($MJTC_html);
        $MJTC_data = wp_json_encode($MJTC_html);
        return $MJTC_data;
    }

    function getFieldsForVisibleCombobox($MJTC_fieldfor, $MJTC_multiformid, $MJTC_field='', $MJTC_cid='') {
        if(!is_numeric($MJTC_fieldfor)) return false;
        $MJTC_wherequery = '';
        if(isset($MJTC_field) && $MJTC_field !='' ){
            $MJTC_query = "SELECT id FROM " . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering WHERE fieldfor = ".esc_sql($MJTC_fieldfor)." AND (userfieldtype IN ( 'combo', 'text', 'checkbox', 'date', 'email', 'radio', 'multiple') ) AND visible_field = '" . esc_sql($MJTC_field) . "' ";
            $MJTC_parent = majesticsupport::$_db->get_var($MJTC_query);
            if ($MJTC_parent) {
                $MJTC_wherequery = ' OR id = '.esc_sql($MJTC_parent);
            }
        }
        $MJTC_wherequeryforedit = '';
        if(isset($MJTC_cid) && $MJTC_cid !='' ){
            $MJTC_wherequeryforedit = ' AND id != '.esc_sql($MJTC_cid);
        }
        
        // Base fields always included
        $MJTC_builtin_fields = ['email', 'fullname', 'phone', 'subject', 'department', 'priority'];

        // Convert to comma-separated string for SQL IN clause
        $MJTC_builtin_fields_sql = "'" . implode("','", array_map('esc_sql', $MJTC_builtin_fields)) . "'";

        // Build the final SQL query
        $MJTC_query = "
        SELECT fieldtitle AS text, field AS id 
            FROM " . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering 
            WHERE (
                fieldfor = " . esc_sql($MJTC_fieldfor) . " 
                AND multiformid = '" . intval($MJTC_multiformid) . "' 
                AND field IN ($MJTC_builtin_fields_sql) 
                $MJTC_wherequeryforedit $MJTC_wherequery
            ) 
            OR (
                fieldfor = " . esc_sql($MJTC_fieldfor) . " 
                AND multiformid = '" . intval($MJTC_multiformid) . "' 
                AND userfieldtype IN ('combo', 'text', 'checkbox', 'date', 'email', 'radio', 'multiple') 
                $MJTC_wherequeryforedit $MJTC_wherequery
            )";
        $MJTC_data = majesticsupport::$_db->get_results($MJTC_query);
        return $MJTC_data;
    }

    function getChildForVisibleCombobox($MJTC_perentid = null , $MJTC_default = null) {
        $MJTC_isAjaxCall = MJTC_request::MJTC_getVar('isAjaxCall');
        if ($MJTC_isAjaxCall == 1) {
            $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
            if (! wp_verify_nonce( $MJTC_nonce, 'get-child-for-visible-combobox') ) {
                die( 'Security check Failed' );
            }
        }
        if ($MJTC_perentid == null) {
            $MJTC_perentid = MJTC_request::MJTC_getVar('val');
        }
        if (empty($MJTC_perentid)){
            return false;
        }

        $MJTC_query = "SELECT isuserfield, userfieldtype, field FROM `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` WHERE field = '" . esc_sql($MJTC_perentid)."'";
        $MJTC_fieldType = majesticsupport::$_db->get_row($MJTC_query);
        $MJTC_showComboBox = false;
        if (isset($MJTC_fieldType->isuserfield) && $MJTC_fieldType->isuserfield == 1) {
            $MJTC_query = "SELECT userfieldparams AS params FROM `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` WHERE field = '" . esc_sql($MJTC_perentid) . "'";
            $MJTC_options = majesticsupport::$_db->get_var($MJTC_query);
            $MJTC_options = json_decode($MJTC_options);
            foreach ($MJTC_options as $MJTC_key => $MJTC_option) {
                $MJTC_fieldtypes[$MJTC_key] = (object) array('id' => $MJTC_option, 'text' => $MJTC_option);
            }
            if (in_array($MJTC_fieldType->userfieldtype, ['combo', 'checkbox', 'radio', 'multiple'])) {
                $MJTC_showComboBox = true;
            }
        } else if ($MJTC_fieldType->field == 'department') {
            $MJTC_showComboBox = true;
            $MJTC_query = "SELECT departmentname AS text ,id FROM " . majesticsupport::$_db->prefix . "mjtc_support_departments";
            $MJTC_fieldtypes = majesticsupport::$_db->get_results($MJTC_query);
        } else if ($MJTC_fieldType->field == 'helptopic') {
            $MJTC_showComboBox = true;
            $MJTC_query = "SELECT id, topic AS text FROM `" . majesticsupport::$_db->prefix . "mjtc_support_help_topics` WHERE status = 1";
            $MJTC_query.= "  ORDER BY ordering ASC";
            $MJTC_fieldtypes = majesticsupport::$_db->get_results($MJTC_query);
        } else if ($MJTC_fieldType->field == 'priority') {
            $MJTC_showComboBox = true;
            $MJTC_query = "SELECT id, priority AS text FROM `" . majesticsupport::$_db->prefix . "mjtc_support_priorities`";
            $MJTC_query .= 'ORDER BY ordering ASC';
            $MJTC_fieldtypes = majesticsupport::$_db->get_results($MJTC_query);
        }
        //
        $MJTC_combobox = false;
        if($MJTC_showComboBox){
            $MJTC_combobox = MJTC_formfield::MJTC_select('visibleValue[]', $MJTC_fieldtypes, isset($MJTC_default) ? $MJTC_default : '', '', array('class' => 'inputbox one mjtc-form-select-field mjtc-form-input-field-visible'));
        } else {
            $MJTC_combobox = MJTC_formfield::MJTC_text('visibleValue[]', isset($MJTC_default) ? $MJTC_default : '', array('class' => 'inputbox one mjtc-form-input-field mjtc-form-input-field-visible'));
        }
        return MJTC_majesticsupportphplib::MJTC_htmlentities($MJTC_combobox);
    }

    function getConditionsForVisibleCombobox($MJTC_perentid = null , $MJTC_default = null) {
        $MJTC_isAjaxCall = MJTC_request::MJTC_getVar('isAjaxCall');
        if ($MJTC_isAjaxCall == 1) {
            $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
            if (! wp_verify_nonce( $MJTC_nonce, 'get-conditions-for-visible-combobox') ) {
                die( 'Security check Failed' );
            }
        }
        if ($MJTC_perentid == null) {
            $MJTC_perentid = MJTC_request::MJTC_getVar('val');
        }
        if (empty($MJTC_perentid)){
            return false;
        }
        $Conditions = array(
        (object) array('id' => 1, 'text' => esc_html(__('Equal', 'majestic-support'))),
        (object) array('id' => 0, 'text' => esc_html(__('Not Equal', 'majestic-support'))));

        $MJTC_query = "SELECT isuserfield, userfieldtype, field FROM `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` WHERE field = '" . esc_sql($MJTC_perentid) . "'";
        $MJTC_fieldType = majesticsupport::$_db->get_row($MJTC_query);
        if (empty($MJTC_fieldType->isuserfield)) {
            if ($MJTC_fieldType->field == 'email' || $MJTC_fieldType->field == 'fullname' || $MJTC_fieldType->field == 'phone' || $MJTC_fieldType->field == 'subject' || $MJTC_fieldType->field == 'issuesummary') {
                $Conditions = array(
                (object) array('id' => 2, 'text' => esc_html(__('Contain', 'majestic-support'))),
                (object) array('id' => 3, 'text' => esc_html(__('Not Contain', 'majestic-support'))));
            }
        } else {
            if (!in_array($MJTC_fieldType->userfieldtype, ['combo', 'checkbox', 'radio', 'multiple'])) {
                $Conditions = array(
                (object) array('id' => 2, 'text' => esc_html(__('Contain', 'majestic-support'))),
                (object) array('id' => 3, 'text' => esc_html(__('Not Contain', 'majestic-support'))));
            }
        }
        $MJTC_combobox = false;
        if(!empty($Conditions)){
            $MJTC_combobox = MJTC_formfield::MJTC_select('visibleCondition[]', $Conditions, isset($MJTC_default) ? $MJTC_default : '', '', array('class' => 'inputbox one mjtc-form-select-field mjtc-form-input-field-visible'));
        }
        return MJTC_majesticsupportphplib::MJTC_htmlentities($MJTC_combobox);
    }

    function getSectionToFillValues() {
        $MJTC_fieldfor = MJTC_request::MJTC_getVar('fieldfor');
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'get-section-to-fill-values-'.$MJTC_fieldfor) ) {
            die( 'Security check Failed' );
        }
        $MJTC_field = MJTC_request::MJTC_getVar('pfield');
        if(!is_numeric($MJTC_field)){
            return false;
        }
        $MJTC_query = "SELECT userfieldparams FROM " . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering WHERE id=".esc_sql($MJTC_field);
        $MJTC_data = majesticsupport::$_db->get_var($MJTC_query);
        $MJTC_datas = json_decode($MJTC_data);
        $MJTC_html = '';
        $MJTC_fieldsvar = '';
        $MJTC_comma = '';
        foreach ($MJTC_datas as $MJTC_data) {
            if(is_array($MJTC_data)){
                for ($MJTC_i = 0; $MJTC_i < count($MJTC_data); $MJTC_i++) {
                    $MJTC_fieldsvar .= $MJTC_comma . "$MJTC_data[$MJTC_i]";
                    $MJTC_textvar = $MJTC_data[$MJTC_i];
                    if($MJTC_textvar != ''){
                        $MJTC_textvar = MJTC_majesticsupportphplib::MJTC_str_replace(' ','__',$MJTC_textvar);
                        $MJTC_textvar = MJTC_majesticsupportphplib::MJTC_str_replace('.','___',$MJTC_textvar);
                    }
                    $MJTC_divid = $MJTC_textvar;
                    $mjtc_value = esc_js($MJTC_divid);
                    $MJTC_textvar .='[]';
                    $MJTC_html .= "<div class='ms-user-dd-field-wrap'>";
                    $MJTC_html .= "<div class='ms-user-dd-field-title'>" . esc_html($MJTC_data[$MJTC_i]) . "</div>";
                    $MJTC_html .= "<div class='ms-user-dd-field-value combo-options-fields' id=" . esc_attr($MJTC_divid) . ">
                                    <span class='input-field-wrapper'>
                                        " . wp_kses(MJTC_formfield::MJTC_text($MJTC_textvar, '', array('class' => 'inputbox one user-field')), MJTC_ALLOWED_TAGS) . "
                                        <img class='input-field-remove-img' src='" . esc_url(MJTC_PLUGIN_URL) . "includes/images/delete.png' />
                                    </span>
                                    <input type='button' class='ms-button-link button user-field-val-button' id='depandant-field-button' onClick='getNextField(\"" . $mjtc_value . "\", this);'  value='Add More' />
                                </div>";
                    $MJTC_html .= "</div>";
                    $MJTC_comma = '_MS_Unique_88a9e3_';
                }
            }else{
                $MJTC_fieldsvar .= $MJTC_comma . "$MJTC_data";
                $MJTC_textvar = $MJTC_data;
                if($MJTC_textvar != ''){
                    $MJTC_textvar = MJTC_majesticsupportphplib::MJTC_str_replace(' ','__',$MJTC_textvar);
                    $MJTC_textvar = MJTC_majesticsupportphplib::MJTC_str_replace('.','___',$MJTC_textvar);
                }
                $MJTC_divid = $MJTC_textvar;
                $mjtc_value = esc_js($MJTC_divid);
                $MJTC_textvar .='[]';
                $MJTC_html .= "<div class='ms-user-dd-field-wrap'>";
                $MJTC_html .= "<div class='ms-user-dd-field-title'>" . esc_html($MJTC_data) . "</div>";
                $MJTC_html .= "<div class='ms-user-dd-field-value combo-options-fields' id=" . esc_attr($MJTC_divid) . ">
                                <span class='input-field-wrapper'>
                                    " . wp_kses(MJTC_formfield::MJTC_text($MJTC_textvar, '', array('class' => 'inputbox one user-field')), MJTC_ALLOWED_TAGS) . "
                                    <img class='input-field-remove-img' src='" . esc_url(MJTC_PLUGIN_URL) . "includes/images/delete.png' />
                                </span>
                                <input type='button' class='ms-button-link button user-field-val-button' id='depandant-field-button' onClick=\"getNextField('" . $mjtc_value . "', this);\"  value='Add More' />
                            </div>";
                $MJTC_html .= "</div>";
                $MJTC_comma = '_MS_Unique_88a9e3_';
            }

        }
        $MJTC_html .= " <input type='hidden' name='arraynames' value=\"" . esc_attr($MJTC_fieldsvar) . "\" />";
        $MJTC_html = MJTC_majesticsupportphplib::MJTC_htmlentities($MJTC_html);
        $MJTC_html = wp_json_encode($MJTC_html);
        return $MJTC_html;
    }

    function getOptionsForFieldEdit() {
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'get-options-for-field-edit') ) {
            die( 'Security check Failed' );
        }
        $MJTC_field = MJTC_request::MJTC_getVar('field');
		if(!is_numeric($MJTC_field)) return false;
        $MJTC_yesno = array(
            (object) array('id' => 1, 'text' => esc_html(__('Yes', 'majestic-support'))),
            (object) array('id' => 0, 'text' => esc_html(__('No', 'majestic-support'))));

        $MJTC_query = "SELECT * FROM " . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering WHERE id=".esc_sql($MJTC_field);
        $MJTC_data = majesticsupport::$_db->get_row($MJTC_query);

        $MJTC_html = '<div class="userpopup-top">
                    <div class="userpopup-heading" >
                    ' . esc_html(__("Edit Field", 'majestic-support')) . '
                    </div>
                    <img id="popup_cross" class="userpopup-close" onClick="close_popup();" src="' . esc_url(MJTC_PLUGIN_URL) . 'includes/images/close-icon-white.png" alt="'. esc_html(__('Close','majestic-support')).'">
                </div>';
        $MJTC_nonce_id = isset($MJTC_data->id) ? $MJTC_data->id : '';
        $MJTC_adminurl = admin_url("?page=majesticsupport_fieldordering&task=savefeild&formid=".esc_attr($MJTC_data->multiformid));
        $MJTC_html .= '<form id="adminForm" class="popup-field-from" method="post" action="' . esc_url(wp_nonce_url($MJTC_adminurl ,"save-feild-".$MJTC_nonce_id)).'">';
        $MJTC_html .= '<div class="popup-field-wrapper">
                    <div class="popup-field-title">' . esc_html(__('Field Title', 'majestic-support')) . '<font class="required-notifier">*</font></div>
                    <div class="popup-field-obj">' . wp_kses(MJTC_formfield::MJTC_text('fieldtitle', isset($MJTC_data->fieldtitle) ? $MJTC_data->fieldtitle : 'text', array('class' => 'inputbox one', 'data-validation' => 'required')), MJTC_ALLOWED_TAGS) . '</div>
                </div>';
        if ($MJTC_data->cannotunpublish == 0 || $MJTC_data->cannotshowonlisting == 0) {
            $MJTC_html .= '<div class="popup-field-wrapper">
                        <div class="popup-field-title">' . esc_html(__('User Published', 'majestic-support')) . '</div>
                        <div class="popup-field-obj">' . wp_kses(MJTC_formfield::MJTC_select('published', $MJTC_yesno, isset($MJTC_data->published) ? $MJTC_data->published : 0, '', array('class' => 'inputbox one', 'data-validation' => 'required')), MJTC_ALLOWED_TAGS) . '</div>
                    </div>';
            $MJTC_html .= '<div class="popup-field-wrapper">
                    <div class="popup-field-title">' . esc_html(__('Visitor Published', 'majestic-support')) . '</div>
                    <div class="popup-field-obj">' . wp_kses(MJTC_formfield::MJTC_select('isvisitorpublished', $MJTC_yesno, isset($MJTC_data->isvisitorpublished) ? $MJTC_data->isvisitorpublished : 0, '', array('class' => 'inputbox one', 'data-validation' => 'required')), MJTC_ALLOWED_TAGS) . '</div>
                </div>';
        }

        $MJTC_html .= '<div class="popup-field-wrapper">
                <div class="popup-field-title">' . esc_html(__('Placeholder', 'majestic-support')) . '</div>
                <div class="popup-field-obj">' . MJTC_formfield::MJTC_text('placeholder', isset($MJTC_data->placeholder) ? $MJTC_data->placeholder : '', array('class' => 'inputbox one','maxlength'=>225)) . '</div>
            </div>';

        $MJTC_html .= '<div class="popup-field-wrapper">
                <div class="popup-field-title">' . esc_html(__('Description', 'majestic-support')) . '</div>
                <div class="popup-field-obj">' . MJTC_formfield::MJTC_text('description', isset($MJTC_data->description) ? $MJTC_data->description : '', array('class' => 'inputbox one','maxlength'=>225)) . '</div>
            </div>';
        if ($MJTC_data->cannotunpublish == 0 || $MJTC_data->cannotshowonlisting == 0) {

            $MJTC_html .= '<div class="popup-field-wrapper">
                    <div class="popup-field-title">' . esc_html(__('Required', 'majestic-support')) . '</div>
                    <div class="popup-field-obj">' . wp_kses(MJTC_formfield::MJTC_select('required', $MJTC_yesno, isset($MJTC_data->required) ? $MJTC_data->required : 0, '', array('class' => 'inputbox one', 'data-validation' => 'required')), MJTC_ALLOWED_TAGS) . '</div>
                </div>';
        }
        if ($MJTC_data->cannotsearch == 0) {
            $MJTC_html .= '<div class="popup-field-wrapper">
                        <div class="popup-field-title">' . esc_html(__('User Search', 'majestic-support')) . '</div>
                        <div class="popup-field-obj">' . wp_kses(MJTC_formfield::MJTC_select('search_user', $MJTC_yesno, isset($MJTC_data->search_user) ? $MJTC_data->search_user : 0, '', array('class' => 'inputbox one', 'data-validation' => 'required')), MJTC_ALLOWED_TAGS) . '</div>
                    </div>';
            $MJTC_html .= '<div class="popup-field-wrapper">
                        <div class="popup-field-title">' . esc_html(__('Admin Search', 'majestic-support')) . '</div>
                        <div class="popup-field-obj">' . wp_kses(MJTC_formfield::MJTC_select('search_admin', $MJTC_yesno, isset($MJTC_data->search_admin) ? $MJTC_data->search_admin : 0, '', array('class' => 'inputbox one', 'data-validation' => 'required')), MJTC_ALLOWED_TAGS) . '</div>
                    </div>';
        }
        if ($MJTC_data->isuserfield == 1 || $MJTC_data->cannotshowonlisting == 0) {
            $MJTC_html .= '<div class="popup-field-wrapper">
                        <div class="popup-field-title">' . esc_html(__('Show On Listing', 'majestic-support')) . '</div>
                        <div class="popup-field-obj">' . wp_kses(MJTC_formfield::MJTC_select('showonlisting', $MJTC_yesno, isset($MJTC_data->showonlisting) ? $MJTC_data->showonlisting : 0, '', array('class' => 'inputbox one', 'data-validation' => 'required')), MJTC_ALLOWED_TAGS) . '</div>
                    </div>';
        }
        $MJTC_html .= wp_kses(MJTC_formfield::MJTC_hidden('form_request', 'majesticsupport'), MJTC_ALLOWED_TAGS);
        $MJTC_html .= wp_kses(MJTC_formfield::MJTC_hidden('id', $MJTC_data->id), MJTC_ALLOWED_TAGS);
        $MJTC_html .= wp_kses(MJTC_formfield::MJTC_hidden('isuserfield', $MJTC_data->isuserfield), MJTC_ALLOWED_TAGS);
        $MJTC_html .= wp_kses(MJTC_formfield::MJTC_hidden('fieldfor', $MJTC_data->fieldfor), MJTC_ALLOWED_TAGS);
        $MJTC_html .='<div class="mjtc-submit-container mjtc-col-lg-10 mjtc-col-md-10 mjtc-col-md-offset-1 mjtc-col-md-offset-1">
                    ' . wp_kses(MJTC_formfield::MJTC_submitbutton('save', esc_html(__('Save', 'majestic-support')), array('class' => 'button')), MJTC_ALLOWED_TAGS);
        if ($MJTC_data->isuserfield == 1) {
            $MJTC_html .= '<a class="button" style="margin-left:10px;" id="user-field-anchor" href="?page=majesticsupport_fieldordering&mjslay=adduserfeild&majesticsupportid=' . esc_attr($MJTC_data->id) .'&fieldfor='.esc_attr($MJTC_data->fieldfor).'&formid='.esc_attr($MJTC_data->multiformid).'"> ' . esc_html(__('Advanced', 'majestic-support')) . ' </a>';
        }

        $MJTC_html .='</div>
            </form>';
        $MJTC_html = MJTC_majesticsupportphplib::MJTC_htmlentities($MJTC_html);
        return wp_json_encode($MJTC_html);
    }

    function deleteUserField($MJTC_id){
        if (is_numeric($MJTC_id) == false)
           return false;
        $MJTC_query = "SELECT field,field,fieldfor FROM `".majesticsupport::$_db->prefix."mjtc_support_fieldsordering` WHERE id = " . esc_sql($MJTC_id);
        $MJTC_result = majesticsupport::$_db->get_row($MJTC_query);
        if ($this->userFieldCanDelete($MJTC_result) == true) {
            $MJTC_row = MJTC_includer::MJTC_getTable('fieldsordering');
            if (!$MJTC_row->delete($MJTC_id)) {
                MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
                MJTC_message::MJTC_setMessage(esc_html(__('Field has not been deleted', 'majestic-support')),'error');
            } else {
                $MJTC_query = "SELECT id,visible_field FROM `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` WHERE visible_field LIKE '%".esc_sql($MJTC_result->field)."%'";
                $MJTC_results = majesticsupport::$_db->get_results($MJTC_query);
                foreach ($MJTC_results as $MJTC_value) {
                    $MJTC_visible_field =  MJTC_majesticsupportphplib::MJTC_str_replace($MJTC_result->field.',', '', $MJTC_value->visible_field);
                    $MJTC_visible_field =  MJTC_majesticsupportphplib::MJTC_str_replace(','.$MJTC_result->field, '', $MJTC_visible_field);
                    $MJTC_visible_field =  MJTC_majesticsupportphplib::MJTC_str_replace($MJTC_result->field, '', $MJTC_visible_field);

                    $MJTC_query = "UPDATE `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` SET visible_field = '".esc_sql($MJTC_visible_field)."' WHERE id = ".esc_sql($MJTC_value->id);
                    majesticsupport::$_db->query($MJTC_query);
                    if (majesticsupport::$_db->last_error != null) {

                        MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
                    }
                }
                $MJTC_query = "SELECT id FROM `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` WHERE depandant_field = '".esc_sql($MJTC_result->field)."'";
                $MJTC_result = majesticsupport::$_db->get_var($MJTC_query);
                if (isset($MJTC_result)) {
                    $MJTC_query = "UPDATE `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` SET depandant_field = '' WHERE id = ".esc_sql($MJTC_result);
                    majesticsupport::$_db->query($MJTC_query);
                }
                MJTC_message::MJTC_setMessage(esc_html(__('Field has been deleted', 'majestic-support')),'updated');
            }
        }else{
            MJTC_message::MJTC_setMessage(esc_html(__('Field has not been deleted', 'majestic-support')),'error');
        }
        return false;
    }

    function enforceDeleteUserField($MJTC_id){
        if (is_numeric($MJTC_id) == false)
           return false;
        $MJTC_query = "SELECT field,fieldfor FROM `".majesticsupport::$_db->prefix."mjtc_support_fieldsordering` WHERE id = ".esc_sql($MJTC_id);
        $MJTC_result = majesticsupport::$_db->get_row($MJTC_query);
        if ($this->userFieldCanDelete($MJTC_result) == true) {
            $MJTC_row = MJTC_includer::MJTC_getTable('fieldsordering');
            $MJTC_row->delete($MJTC_id);
        }
        return false;
    }

    function userFieldCanDelete($MJTC_field) {
        $MJTC_fieldname = $MJTC_field->field;
        $MJTC_fieldfor = $MJTC_field->fieldfor;

        $table = "tickets";
        $MJTC_query = ' SELECT
                    ( SELECT COUNT(id) FROM `' . majesticsupport::$_db->prefix . 'mjtc_support_'.$table.'` WHERE
                        params LIKE \'%"' . esc_sql($MJTC_fieldname) . '":%\'
                    )
                    AS total';
        $total = majesticsupport::$_db->get_var($MJTC_query);
        if ($total > 0)
            return false;
        else
            return true;
    }

    function getUserfieldsfor($MJTC_fieldfor, $MJTC_multiformid = '') {
        // 1. Strict numeric check for $MJTC_fieldfor (Cast to integer for safety)
        if (!is_numeric($MJTC_fieldfor)){
            return false;
        }
        $MJTC_fieldfor = esc_sql($MJTC_fieldfor);

        // 2. Determine visibility criteria
        if (MJTC_includer::MJTC_getObjectClass('user')->MJTC_isguest()) {
            $MJTC_published = ' isvisitorpublished = 1 ';
        } else {
            $MJTC_published = ' published = 1 ';
        }

        // 3. Securely handle the optional multiformid via integer casting
        $MJTC_inquery = '';
        if (isset($MJTC_multiformid) && $MJTC_multiformid !== '') {
            $MJTC_inquery = " AND multiformid = " . intval($MJTC_multiformid);
        }

        // 4. Construct the query using the safe, casted integers
        $MJTC_query = "SELECT field, userfieldparams, userfieldtype, fieldtitle FROM `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` WHERE fieldfor = " . esc_sql($MJTC_fieldfor) . " AND isuserfield = 1 AND " . $MJTC_published;
        $MJTC_query .= $MJTC_inquery . " ORDER BY field ";
        
        // 5. Execute query
        $MJTC_fields = majesticsupport::$_db->get_results($MJTC_query);
        return $MJTC_fields;
    }

    function getUserUnpublishFieldsfor($MJTC_fieldfor) {
        if (!is_numeric($MJTC_fieldfor))
            return false;
        if (MJTC_includer::MJTC_getObjectClass('user')->MJTC_isguest()) {
            $MJTC_published = ' isvisitorpublished = 0 ';
        } else {
            $MJTC_published = ' published = 0 ';
        }
        $MJTC_query = "SELECT field FROM `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` WHERE fieldfor = " . esc_sql($MJTC_fieldfor) . " AND isuserfield = 1 AND " . $MJTC_published;
        $MJTC_fields = majesticsupport::$_db->get_results($MJTC_query);
        return $MJTC_fields;
    }

    function getFieldTitleByFieldfor($MJTC_fieldfor,$MJTC_formid='') {
        if (!is_numeric($MJTC_fieldfor))
            return false;
        if (is_admin()) {
            $MJTC_published = '';
        } else if (MJTC_includer::MJTC_getObjectClass('user')->MJTC_isguest()) {
            $MJTC_published = ' AND isvisitorpublished = 1 ';
        } else {
            $MJTC_published = ' AND published = 1 ';
        }
        $MJTC_inquery = '';
        if (isset($MJTC_formid) && $MJTC_formid == 0) {
            $MJTC_defaultformid = MJTC_includer::MJTC_getModel('ticket')->getDefaultMultiFormId();
            $MJTC_inquery = " AND multiformid = ".intval($MJTC_defaultformid);
        } elseif (isset($MJTC_formid) && $MJTC_formid != '') {
            $MJTC_inquery = " AND multiformid = ".intval($MJTC_formid);
        }
        $MJTC_query = "SELECT field,fieldtitle FROM `" . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering` WHERE fieldfor = " . esc_sql($MJTC_fieldfor) . $MJTC_published;
        $MJTC_query .= $MJTC_inquery;
        $MJTC_fields = majesticsupport::$_db->get_results($MJTC_query);
        $MJTC_fielddata = array();
        foreach ($MJTC_fields as $MJTC_value) {
            $MJTC_fielddata[$MJTC_value->field] = $MJTC_value->fieldtitle;
        }
        return $MJTC_fielddata;
    }

    function getUserFieldbyId($MJTC_id,$MJTC_fieldfor) {
        if ($MJTC_id) {
            if (is_numeric($MJTC_id) == false)
                return false;
            $MJTC_query = "SELECT * FROM " . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering WHERE id = " . esc_sql($MJTC_id);
            majesticsupport::$_data[0]['userfield'] = majesticsupport::$_db->get_row($MJTC_query);
            $MJTC_params = majesticsupport::$_data[0]['userfield']->userfieldparams;
            $MJTC_visibleparams = majesticsupport::$_data[0]['userfield']->visibleparams;
            majesticsupport::$_data[0]['userfieldparams'] = !empty($MJTC_params) ? json_decode($MJTC_params, True) : '';
        }
        majesticsupport::$_data[0]['fieldfor'] = $MJTC_fieldfor;
        return;
    }
    function getFieldsForListing($MJTC_fieldfor, $MJTC_formid='') {
        if (is_numeric($MJTC_fieldfor) == false)
            return false;
        if (is_admin()) {
            $MJTC_published = '';
        } else if (MJTC_includer::MJTC_getObjectClass('user')->MJTC_isguest()) {
            $MJTC_published = ' AND isvisitorpublished = 1 ';
        } else {
            $MJTC_published = ' AND published = 1 ';
        }
        $MJTC_inquery = '';
        if (isset($MJTC_formid) && $MJTC_formid == 0) {
            $MJTC_defaultformid = MJTC_includer::MJTC_getModel('ticket')->getDefaultMultiFormId();
            $MJTC_inquery = " AND multiformid = ".intval($MJTC_defaultformid);
        } elseif (isset($MJTC_formid) && $MJTC_formid != '') {
            $MJTC_inquery = " AND multiformid = ".intval($MJTC_formid);
        }
        $MJTC_query = "SELECT field, showonlisting FROM " . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering WHERE showonlisting = 1 AND fieldfor =  " . esc_sql($MJTC_fieldfor) . esc_sql($MJTC_published);
        $MJTC_query .= $MJTC_inquery;
        $MJTC_query .= " ORDER BY ordering";
        $MJTC_fields = majesticsupport::$_db->get_results($MJTC_query);
        $MJTC_fielddata = array();
        foreach ($MJTC_fields AS $MJTC_field) {
            $MJTC_fielddata[$MJTC_field->field] = $MJTC_field->showonlisting;
        }
        return $MJTC_fielddata;
    }
    function getAdminSystemFieldsForSearch() {
        
        if(in_array('multiform', majesticsupport::$_active_addons)){
            $MJTC_query = "SELECT f.field, f.fieldtitle FROM " . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering f LEFT JOIN " . majesticsupport::$_db->prefix . "mjtc_support_multiform m ON f.multiformid = m.id WHERE f.search_admin = 1 AND f.published = 1 AND (f.isuserfield IS NULL OR f.isuserfield != 1) ";
            $MJTC_query .= " ORDER BY m.is_default DESC, f.ordering ASC";
        } else {
            $MJTC_formid = MJTC_includer::MJTC_getModel('ticket')->getDefaultMultiFormId();
            $MJTC_formFilter = " AND f.multiformid = " . intval($MJTC_formid);
            $MJTC_query = "SELECT f.field, f.fieldtitle FROM " . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering f WHERE f.search_admin = 1 AND f.published = 1 AND (f.isuserfield IS NULL OR f.isuserfield != 1) ";
            $MJTC_query .= $MJTC_formFilter;
            $MJTC_query .= " ORDER BY f.ordering ASC";
        }
        $MJTC_results = majesticsupport::$_db->get_results($MJTC_query);

        $MJTC_fielddata = array();
        foreach ($MJTC_results as $MJTC_row) {
            // Only set the field once to prioritize the first (default) occurrence
            if (!isset($MJTC_fielddata[$MJTC_row->field])) {
                $MJTC_fielddata[$MJTC_row->field] = $MJTC_row->fieldtitle;
            }
        }
        return $MJTC_fielddata;
    }
    function getUserSystemFieldsForSearch() {
                // Determine published column based on user type
        if (MJTC_includer::MJTC_getObjectClass('user')->MJTC_isguest()) {
            $MJTC_published = ' f.isvisitorpublished = 1 ';
        } else {
            $MJTC_published = ' f.published = 1 ';
        }

        if(in_array('multiform', majesticsupport::$_active_addons)){
            // Query with LEFT JOIN and ordering to prioritize default form
            $MJTC_query = "SELECT f.field, f.fieldtitle FROM " . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering f LEFT JOIN " . majesticsupport::$_db->prefix . "mjtc_support_multiform m ON f.multiformid = m.id WHERE f.search_user = 1 AND ".$MJTC_published." AND (f.isuserfield IS NULL OR f.isuserfield != 1)";
            $MJTC_query .= " ORDER BY m.is_default DESC, f.ordering ASC";
        } else {
            $MJTC_formid = MJTC_includer::MJTC_getModel('ticket')->getDefaultMultiFormId();
            $MJTC_formFilter = " AND f.multiformid = " . intval($MJTC_formid);
            // Query with LEFT JOIN and ordering to prioritize default form
            $MJTC_query = "SELECT f.field, f.fieldtitle FROM " . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering f WHERE f.search_user = 1 AND ".$MJTC_published." AND (f.isuserfield IS NULL OR f.isuserfield != 1)";
            $MJTC_query .= $MJTC_formFilter;
            $MJTC_query .= " ORDER BY f.ordering ASC";
        }

        $MJTC_results = majesticsupport::$_db->get_results($MJTC_query);

        $MJTC_fielddata = array();
        foreach ($MJTC_results as $MJTC_row) {
            // Only keep the first (preferred) version of each field
            if (!isset($MJTC_fielddata[$MJTC_row->field])) {
                $MJTC_fielddata[$MJTC_row->field] = $MJTC_row->fieldtitle;
            }
        }

        return $MJTC_fielddata;
    }
    function getPublishedFieldsForTicketDetail($MJTC_formid='') {
        if(!isset($MJTC_formid) || $MJTC_formid==''){
            $MJTC_formid = MJTC_includer::MJTC_getModel('ticket')->getDefaultMultiFormId();
        }
        if(!is_numeric($MJTC_formid)) return false;
        if (MJTC_includer::MJTC_getObjectClass('user')->MJTC_isguest()) {
            $MJTC_published = ' isvisitorpublished = 1 ';
        } else {
            $MJTC_published = ' published = 1 ';
        }
        $MJTC_query = "SELECT field, showonlisting FROM " . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering WHERE ".$MJTC_published." AND fieldfor = 1 AND multiformid =  " . intval($MJTC_formid) ;
        $MJTC_fields = majesticsupport::$_db->get_results($MJTC_query);
        $MJTC_fielddata = array();
        foreach ($MJTC_fields AS $MJTC_field) {
            $MJTC_fielddata[$MJTC_field->field] = $MJTC_field->showonlisting;
        }
        return $MJTC_fielddata;
    }

    function DataForDepandantField(){
        $MJTC_childfield = MJTC_request::MJTC_getVar('child');
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'data-for-depandant-field-'.$MJTC_childfield) ) {
            die( 'Security check Failed' );
        }
        $MJTC_val = MJTC_request::MJTC_getVar('fvalue');
        $MJTC_query = "SELECT userfieldparams,fieldtitle,depandant_field,field FROM `".majesticsupport::$_db->prefix."mjtc_support_fieldsordering` WHERE field = '".esc_sql($MJTC_childfield)."'";
        $MJTC_data = majesticsupport::$_db->get_row($MJTC_query);
        $MJTC_decoded_data = json_decode($MJTC_data->userfieldparams);
        $MJTC_comboOptions = array();
        $MJTC_flag = 0;
        foreach ($MJTC_decoded_data as $MJTC_key => $MJTC_value) {
            $MJTC_key = html_entity_decode($MJTC_key);
            if($MJTC_key==$MJTC_val){
               for ($MJTC_i=0; $MJTC_i <count($MJTC_value) ; $MJTC_i++) {
                   $MJTC_comboOptions[] = (object)array('id' => $MJTC_value[$MJTC_i], 'text' => $MJTC_value[$MJTC_i]);
                   $MJTC_flag = 1;
               }
            }
        }
        $msFunction = '';
        if ($MJTC_data->depandant_field != null) {
            $MJTC_wpnonce = wp_create_nonce("data-for-depandant-field-".$MJTC_data->depandant_field);
            $msFunction = "MJTC_getDataForDepandantField('".$MJTC_wpnonce."','" . $MJTC_data->field . "','" . $MJTC_data->depandant_field . "',1);";
        }
        $MJTC_textvar =  ($MJTC_flag == 1) ? esc_html(__('Select', 'majestic-support')).' '.esc_html($MJTC_data->fieldtitle) : '';
        $MJTC_html = MJTC_formfield::MJTC_select($MJTC_childfield, $MJTC_comboOptions, '',$MJTC_textvar, array('data-validation' => '','class' => 'inputbox one mjtc-form-select-field mjtc-support-custom-select', 'onchange' => $msFunction));
        $MJTC_html = MJTC_majesticsupportphplib::MJTC_htmlentities($MJTC_html);
        $MJTC_phtml = wp_json_encode($MJTC_html);
        return $MJTC_phtml;
    }

    function sanitize_custom_field($MJTC_arg) {
        if (is_array($MJTC_arg)) {
            return array_map(array($this,'sanitize_custom_field'), $MJTC_arg);
        }
        return MJTC_majesticsupportphplib::MJTC_htmlentities($MJTC_arg, ENT_QUOTES, 'UTF-8');
    }

    function MJTC_getDataForVisibleField($MJTC_field) {
        $MJTC_field = esc_sql($MJTC_field);
        $MJTC_field_array = MJTC_majesticsupportphplib::MJTC_str_replace(",", "','", $MJTC_field);

        $MJTC_query = "SELECT field, visibleparams FROM " . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering WHERE field IN ('" . $MJTC_field_array . "')";
        $MJTC_fields = majesticsupport::$_db->get_results($MJTC_query);
        $MJTC_data = array();

        if (!empty($MJTC_fields)) {
            foreach ($MJTC_fields as $MJTC_item) {
                $MJTC_fieldname = $MJTC_item->field;

                $MJTC_decoded = json_decode($MJTC_item->visibleparams);

                // Initialize array for this field if not set
                if (!isset($MJTC_data[$MJTC_fieldname])) {
                    $MJTC_data[$MJTC_fieldname] = array();
                }


                if (is_array($MJTC_decoded)) {
                    // New system: multiple AND/OR groups
                    foreach ($MJTC_decoded as $MJTC_group) {
                        if (isset($MJTC_group) && is_array($MJTC_group)) {
                            foreach ($MJTC_group as $MJTC_d) {
                                $MJTC_d->visibleParentField = Self::getChildForVisibleField($MJTC_d->visibleParentField);
                            }
                            $MJTC_data[$MJTC_fieldname][] = $MJTC_group; // Save group
                        } else {
                            // fallback
                            $MJTC_group->visibleParentField = self::getChildForVisibleField($MJTC_group->visibleParentField);
                            $MJTC_data[$MJTC_fieldname][] = $MJTC_group;
                        }
                    }
                } elseif (is_object($MJTC_decoded)) {
                    // Old system: simple condition
                    $MJTC_decoded->visibleParentField = self::getChildForVisibleField($MJTC_decoded->visibleParentField);
                    $MJTC_data[$MJTC_fieldname][] = $MJTC_decoded;
                }
            }
        }

        return $MJTC_data;
    }

    static function getChildForVisibleField($MJTC_field) {
        $MJTC_field = esc_sql($MJTC_field);
        $MJTC_oldField = MJTC_majesticsupportphplib::MJTC_explode(',',$MJTC_field);
        $MJTC_newField = $MJTC_oldField[sizeof($MJTC_oldField) - 1];
        $MJTC_query = "SELECT visible_field FROM ". majesticsupport::$_db->prefix ."mjtc_support_fieldsordering WHERE  field = '". $MJTC_newField ."'";
        $MJTC_queryRun = majesticsupport::$_db->get_var($MJTC_query);
        if (isset($MJTC_queryRun) && $MJTC_queryRun != '') {
            $MJTC_data = MJTC_majesticsupportphplib::MJTC_explode(',',$MJTC_queryRun);
            foreach ($MJTC_data as $MJTC_value) {
                $MJTC_field = $MJTC_field.','.$MJTC_value;
                $MJTC_field = Self::getChildForVisibleField($MJTC_field);
            }
        }        
        return $MJTC_field;
    }    

    function getHtmlForORRow() {
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'get-html-for-or-row') ) {
            die( 'Security check Failed' );
        }
        
        $MJTC_orid = MJTC_request::MJTC_getVar("nextorid");
        $MJTC_fieldfor = MJTC_request::MJTC_getVar("fieldfor");
        $MJTC_formid = MJTC_request::MJTC_getVar("formid");
        $MJTC_field = MJTC_request::MJTC_getVar("field");
        $MJTC_id = MJTC_request::MJTC_getVar("id");
        $MJTC_equalnotequal = array(
            (object) array('id' => 1, 'text' => esc_html(__('Equal', 'majestic-support'))),
            (object) array('id' => 0, 'text' => esc_html(__('Not Equal', 'majestic-support'))));
        $MJTC_html = "
        <div id='mjtc_or_row_". $MJTC_orid ."'>
            <div class='mjtc-form-visible-subheading'>
                ". esc_html(__('OR', 'majestic-support')) ."
            </div>
            <div class='mjtc-form-value'>
                ". wp_kses(MJTC_formfield::MJTC_hidden('visibleLogic[]', 'OR'), MJTC_ALLOWED_TAGS) ."
                ". wp_kses(MJTC_formfield::MJTC_select('visibleParent[]', MJTC_includer::MJTC_getModel('fieldordering')->getFieldsForVisibleCombobox($MJTC_fieldfor, $MJTC_formid,$MJTC_field,$MJTC_id), '', esc_html(__('Select Parent', 'majestic-support')), array('class' => 'inputbox mjtc-form-select-field mjtc-form-input-field-visible', 'onchange' => 'getChildForVisibleCombobox(this.value, '.$MJTC_orid.');getConditionsForVisibleCombobox(this.value, '.$MJTC_orid.');')), MJTC_ALLOWED_TAGS) ."
                <span class='visibleValueWrp'>
                    ". wp_kses(MJTC_formfield::MJTC_select('visibleValue[]', '', '', esc_html(__('Select Child', 'majestic-support')), array('class' => 'inputbox one mjtc-form-select-field mjtc-form-input-field-visible')), MJTC_ALLOWED_TAGS) ."
                </span>
                <span class='visibleConditionWrp'>
                    ". wp_kses(MJTC_formfield::MJTC_select('visibleCondition[]', $MJTC_equalnotequal, '', esc_html(__('Select Condition', 'majestic-support')), array('class' => 'inputbox one mjtc-form-select-field mjtc-form-input-field-visible')), MJTC_ALLOWED_TAGS) ."
                </span>
                <div class='mjtc-visible-conditions-body-row'>
                    <div class='mjtc-visible-conditions-body-value'>
                        <span onclick=\"deleteOrRow('mjtc_or_row_". $MJTC_orid ."')\" class='mjtc-visible-conditions-delbtn'>
                            <svg class='input-field-remove-img' viewBox=\"0 0 24 24\" width=\"18\" height=\"18\" fill=\"currentColor\"><path d=\"M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z\"></path></svg>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        ";
        $MJTC_html = MJTC_majesticsupportphplib::MJTC_htmlentities($MJTC_html);
        return wp_json_encode($MJTC_html);
    }

    function getHtmlForANDRow() {
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'get-html-for-and-row') ) {
            die( 'Security check Failed' );
        }
        
        $MJTC_andid = MJTC_request::MJTC_getVar("nextandid");
        $MJTC_orid = MJTC_request::MJTC_getVar("nextorid");
        $MJTC_fieldfor = MJTC_request::MJTC_getVar("fieldfor");
        $MJTC_formid = MJTC_request::MJTC_getVar("formid");
        $MJTC_field = MJTC_request::MJTC_getVar("field");
        $MJTC_id = MJTC_request::MJTC_getVar("id");
        $MJTC_equalnotequal = array(
            (object) array('id' => 1, 'text' => esc_html(__('Equal', 'majestic-support'))),
            (object) array('id' => 0, 'text' => esc_html(__('Not Equal', 'majestic-support'))));

        $MJTC_html = "
        <div class='mjtc-form-visible-andwrp' id='mjtc_and_row_". $MJTC_andid ."'>
            <div class='mjtc-form-visible-subheading'>
                ". esc_html(__('AND', 'majestic-support')) ."
            </div>
            <div class='mjtc-form-wrapper mjtc-form-visible-wrapper' >
                <div class='mjtc-form-value' id='mjtc_or_row_". $MJTC_orid ."'>
                    ". wp_kses(MJTC_formfield::MJTC_hidden('visibleLogic[]', 'AND'), MJTC_ALLOWED_TAGS) ."
                    ". wp_kses(MJTC_formfield::MJTC_select('visibleParent[]', MJTC_includer::MJTC_getModel('fieldordering')->getFieldsForVisibleCombobox($MJTC_fieldfor, $MJTC_formid,$MJTC_field,$MJTC_id), '', esc_html(__('Select Parent', 'majestic-support')), array('class' => 'inputbox mjtc-form-select-field mjtc-form-input-field-visible', 'onchange' => 'getChildForVisibleCombobox(this.value, '.$MJTC_orid.');getConditionsForVisibleCombobox(this.value, '.$MJTC_orid.');')), MJTC_ALLOWED_TAGS) ."
                    <span class='visibleValueWrp'>
                        ". wp_kses(MJTC_formfield::MJTC_select('visibleValue[]', '', '', esc_html(__('Select Child', 'majestic-support')), array('class' => 'inputbox one mjtc-form-select-field mjtc-form-input-field-visible')), MJTC_ALLOWED_TAGS) ."
                    </span>
                    <span class='visibleConditionWrp'>
                        ". wp_kses(MJTC_formfield::MJTC_select('visibleCondition[]', $MJTC_equalnotequal, '', esc_html(__('Select Condition', 'majestic-support')), array('class' => 'inputbox one mjtc-form-select-field mjtc-form-input-field-visible')), MJTC_ALLOWED_TAGS) ."
                    </span>
                    <div class='mjtc-visible-conditions-body-row'>
                        <div class='mjtc-visible-conditions-body-value'>
                            <span onclick=\"deleteOrRow('mjtc_or_row_". $MJTC_orid ."')\" class='mjtc-visible-conditions-delbtn'>
                                <svg class='input-field-remove-img' viewBox=\"0 0 24 24\" width=\"18\" height=\"18\" fill=\"currentColor\"><path d=\"M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z\"></path></svg>
                            </span>
                        </div>
                    </div>
                </div>
                <div class='mjtc-form-visible-or-row'></div>
                <div class='mjtc-visible-conditions-addbtn-wrp'>
                    <span class='mjtc-form-visible-addmore' onclick='getMoreORRow(this, ". esc_js($MJTC_fieldfor) .", ". esc_js($MJTC_formid) .")'>
                        <svg class='input-field-remove-img' viewBox=\"0 0 24 24\" width=\"20\" height=\"20\" fill=\"white\"><path d=\"M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z\"></path></svg>
                        ". esc_html(__('OR', 'majestic-support')) ."
                    </span>
                </div>
            </div>
        </div>
        ";
        $MJTC_html = MJTC_majesticsupportphplib::MJTC_htmlentities($MJTC_html);
        return wp_json_encode($MJTC_html);
    }

}

?>
