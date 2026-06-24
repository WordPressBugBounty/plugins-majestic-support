<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_customfields {
    function MJTC_formCustomFields($MJTC_field) {
        if($MJTC_field->isuserfield != 1){
            return false;
        }
        // Handle adminonly case
        // Visible only on admin and agent form
        if( in_array('agent',majesticsupport::$_active_addons) ){
            $MJTC_agent = MJTC_includer::MJTC_getModel('agent')->isUserStaff();
        }else{
            $MJTC_agent = false;
        }
        if(!empty($MJTC_field->adminonly) && !is_admin() && !$MJTC_agent){
            return false;
        }
        // show termsandconditions only on user form
        if($MJTC_field->userfieldtype == 'termsandconditions' && (is_admin() || $MJTC_agent)){
            return false;
        }
        $MJTC_cssclass = "";
        $MJTC_visibleclass = "";
        if (!empty($MJTC_field->visibleparams) && $MJTC_field->visibleparams != '[]'){
            $MJTC_visibleclass = "visible";
        }
        $MJTC_html = '';
        $MJTC_div1 = 'mjtc-support-from-field-wrp ' . esc_attr($MJTC_visibleclass);

        if ($MJTC_field->size == 100 || $MJTC_field->userfieldtype === 'termsandconditions') {
            $MJTC_div1 .= ' mjtc-support-from-field-wrp-full-width';
        }

        if ($MJTC_field->userfieldtype === 'termsandconditions') {
            $MJTC_div1 .= ' mjtc-support-system-terms-and-condition-box';
        }
        $MJTC_div2 = 'mjtc-support-from-field-title';
        $MJTC_div3 = 'mjtc-support-from-field';
        $MJTC_div4 = 'mjtc-support-from-field-description';


        if(is_admin()){
            $MJTC_div1 = ($MJTC_field->size == 100) ? 'mjtc-form-wrapper mjtc-form-custm-flds-wrp fullwidth '.esc_attr($MJTC_visibleclass) : 'mjtc-form-wrapper mjtc-form-custm-flds-wrp '.esc_attr($MJTC_visibleclass);
            $MJTC_div2 = 'mjtc-form-title';
            $MJTC_div3 = 'mjtc-form-value';
            $MJTC_div4 = 'mjtc-form-description';
        }


        $MJTC_required = $MJTC_field->required;
        if($MJTC_field->userfieldtype == 'termsandconditions'){
            if (isset(majesticsupport::$_data[0]->id)) {
                return false;
            }
            $MJTC_required = 1;
            if (isset($MJTC_field->visibleparams) && $MJTC_field->visibleparams !='') {
                $MJTC_required = 0;
            }
        }

        $MJTC_html = '<div class="' . esc_attr($MJTC_div1) .  '">';
        // hide title in case of termsandconditions
        if($MJTC_field->userfieldtype != 'termsandconditions'){
            $MJTC_html .= '<div class="' . esc_attr($MJTC_div2) . '">';
            if ($MJTC_required == 1 && $MJTC_visibleclass != 'visible' && !empty($MJTC_field->fieldtitle)) {
                $MJTC_html .= esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->fieldtitle)) . '<span style="color: red;" >*</span>';
                    $MJTC_cssclass = "required";
            }else {
                $MJTC_html .= esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->fieldtitle));
                    $MJTC_cssclass = "";
            }
            $MJTC_html .= ' </div>';
        }
        $MJTC_html .= ' <div class="' . esc_attr($MJTC_div3) . '">';
        $MJTC_readonlyclass = $MJTC_field->readonly ? " mjtc-form-ticket-readonly " : "";
        $MJTC_maxlength = $MJTC_field->maxlength ? "$MJTC_field->maxlength" : "";
        $MJTC_fvalue = "";
        $MJTC_value = "";
        $MJTC_userdataid = "";
        $MJTC_specialClass="";
        if (isset(majesticsupport::$_data[0]->id)) {
            $MJTC_userfielddataarray = json_decode(majesticsupport::$_data[0]->params);
            $MJTC_uffield = $MJTC_field->field;
            if (isset($MJTC_userfielddataarray->$MJTC_uffield) && !empty($MJTC_userfielddataarray->$MJTC_uffield)) {
                $MJTC_value = $MJTC_userfielddataarray->$MJTC_uffield;
                $MJTC_specialClass='specialClass';
            } else {
                $MJTC_value = '';
            }
        } else {
            if (!empty(majesticsupport::$_data[0]->params)) {
                $MJTC_userfielddataarray = json_decode(majesticsupport::$_data[0]->params);
            }
            $MJTC_value = $MJTC_field->defaultvalue;
        }
        // Handle visible field case
        $MJTC_VisibleFunction = '';
        // For default function (default value setting)
        $MJTC_defaultFunc = '';
        if ($MJTC_field->visible_field != null) {
            $MJTC_visibleparams = MJTC_includer::MJTC_getModel('fieldordering')->MJTC_getDataForVisibleField($MJTC_field->visible_field);
            if (!empty($MJTC_visibleparams)) {
                $MJTC_wpnonce = wp_create_nonce("is-field-required-".$MJTC_field->visible_field);
                $MJTC_jsObject = wp_json_encode($MJTC_visibleparams);
                $MJTC_VisibleFunction = " MJTC_getDataForVisibleField(\"".esc_js($MJTC_wpnonce)."\", this.value, \"" . esc_js($MJTC_field->visible_field) . "\", " . $MJTC_jsObject.");";
                if (!empty($MJTC_value) && !isset(majesticsupport::$_data[0]->id)) {
                    $MJTC_defaultFunc = " MJTC_getDataForVisibleField(\"".$MJTC_wpnonce."\", '".esc_js($MJTC_value)."', \"" . esc_js($MJTC_field->visible_field) . "\", " . $MJTC_jsObject.");";
                    // Attach default function on document ready
                    $majesticsupport_js = "
                        jQuery(document).ready(function(){
                            ".$MJTC_defaultFunc."
                        });
                    ";
                    wp_add_inline_script('majestic-support-cmain-js', $majesticsupport_js);
                }
            }
        }
        switch ($MJTC_field->userfieldtype) {
            case 'text':
                $MJTC_html .= wp_kses(MJTC_formfield::MJTC_text($MJTC_field->field, $MJTC_value, array('class' => 'inputbox mjtc-form-input-field mjtc-support-form-field-input one '.esc_attr($MJTC_specialClass), 'data-validation' => $MJTC_cssclass, 'onchange' => $MJTC_VisibleFunction, 'maxlength' => $MJTC_maxlength, 'placeholder'=> majesticsupport::MJTC_getVarValue($MJTC_field->placeholder)) + ($MJTC_field->readonly ? ['readonly' => 'readonly'] : [])), MJTC_ALLOWED_TAGS);
                break;
            case 'email':
                $MJTC_html .= wp_kses(MJTC_formfield::MJTC_email($MJTC_field->field, $MJTC_value, array('class' => 'inputbox mjtc-form-input-field mjtc-support-form-field-input one '. esc_attr($MJTC_specialClass), 'data-validation' => $MJTC_cssclass, 'onchange' => $MJTC_VisibleFunction, 'maxlength' => $MJTC_maxlength, 'placeholder'=> majesticsupport::MJTC_getVarValue($MJTC_field->placeholder)) + ($MJTC_field->readonly ? ['readonly' => 'readonly'] : [])), MJTC_ALLOWED_TAGS);
                break;
            case 'date':
                if(MJTC_majesticsupportphplib::MJTC_strpos($MJTC_value , '1970') !== false){
                    $MJTC_value = "";
                }
                $MJTC_calendarClass = '';
                if (empty($MJTC_field->readonly)) {
                    $MJTC_calendarClass = ' custom_date ';
                }
                $MJTC_html .= wp_kses(MJTC_formfield::MJTC_text($MJTC_field->field, $MJTC_value, array('class' => esc_attr($MJTC_calendarClass).'mjtc-form-date-field  mjtc-support-input-field  one '. esc_attr($MJTC_specialClass), 'data-validation' => $MJTC_cssclass, 'onchange' => $MJTC_VisibleFunction, 'placeholder'=> majesticsupport::MJTC_getVarValue($MJTC_field->placeholder)) + ($MJTC_field->readonly ? ['readonly' => 'readonly'] : [])), MJTC_ALLOWED_TAGS);
                break;
            case 'textarea':
                $MJTC_html .= wp_kses(MJTC_formfield::MJTC_textarea($MJTC_field->field, $MJTC_value, array('class' => 'inputbox mjtc-form-textarea-field mjtc-support-custom-textarea one '.esc_attr($MJTC_specialClass), 'data-validation' => $MJTC_cssclass, 'rows' => $MJTC_field->rows, 'cols' => $MJTC_field->cols, 'placeholder'=> majesticsupport::MJTC_getVarValue($MJTC_field->placeholder)) + ($MJTC_field->readonly ? ['readonly' => 'readonly'] : [])), MJTC_ALLOWED_TAGS);
                break;
            case 'checkbox':
                if (!empty($MJTC_field->userfieldparams)) {
                    $MJTC_comboOptions = array();
                    $MJTC_obj_option = json_decode($MJTC_field->userfieldparams);
                    $total_options= count($MJTC_obj_option);
                    if($total_options % 2 == 0) {
                        $MJTC_field_width = 'style = " width:calc(100% / 2 - 4px); margin:2px 2px;"';
                    } else {
                        $MJTC_field_width = 'style = " width:calc(100% / 3 - 4px); margin:2px 2px;"';
                    }
                    $MJTC_i = 0;
                    $MJTC_valuearray = array();
                    if ($MJTC_value != '') {
                        $MJTC_valuearray = MJTC_majesticsupportphplib::MJTC_explode(', ',$MJTC_value);
                    }
                    foreach ($MJTC_obj_option AS $MJTC_option) {
                        $MJTC_check = '';
                        $MJTC_option = html_entity_decode($MJTC_option);
                        if(in_array($MJTC_option, $MJTC_valuearray)){
                            $MJTC_check = 'checked';
                        }
                        $MJTC_readonly = '';
                        if($MJTC_field->readonly){
                            $MJTC_readonly = 'readonly';
                        }
                        $MJTC_html .= '<div class="ms-formfield-radio-button-wrap mjtc-support-custom-radio-box" '. $MJTC_field_width .'>';
                        $MJTC_html .= '<input type="checkbox" ' . esc_attr($MJTC_readonly) . ' ' . esc_attr($MJTC_check) . ' class="radiobutton mjtc-support-append-radio-btn '.esc_attr($MJTC_specialClass).esc_attr($MJTC_readonlyclass).'" value="' . esc_attr($MJTC_option) . '" id="' . esc_attr($MJTC_field->field) . '_' . esc_attr($MJTC_i) . '" name="' . esc_attr($MJTC_field->field) . '[]" onclick = "'.esc_js($MJTC_VisibleFunction).'">';
                        $MJTC_html .= '<label for="' . esc_attr($MJTC_field->field) . '_' . esc_attr($MJTC_i) . '" id="foruf_checkbox1">' . esc_html($MJTC_option) . '</label>';
                        $MJTC_html .= '</div>';
                        $MJTC_i++;
                    }
                } else {
                    $MJTC_comboOptions = array('1' => majesticsupport::MJTC_getVarValue($MJTC_field->fieldtitle));
                    $MJTC_html .= wp_kses(MJTC_formfield::MJTC_checkbox($MJTC_field->field, $MJTC_comboOptions, $MJTC_value, array('class' => 'radiobutton')), MJTC_ALLOWED_TAGS);
                }
                break;
            case 'radio':
                $MJTC_comboOptions = array();
                if (!empty($MJTC_field->userfieldparams)) {
                    $MJTC_obj_option = json_decode($MJTC_field->userfieldparams);
                    $total_options= count($MJTC_obj_option);
                    if($total_options % 2 == 0) {
                        $MJTC_field_width = 'style = " width:calc(100% / 2 - 4px); margin:2px 2px;"';
                    } else {
                        $MJTC_field_width = 'style = " width:calc(100% / 3 - 4px); margin:2px 2px;"';
                    }
                    $MJTC_i = 0;
                    $msFunction = '';
                    if ($MJTC_field->depandant_field != null) {
                        $MJTC_wpnonce = wp_create_nonce("data-for-depandant-field-".$MJTC_field->depandant_field);
                        $msFunction = "MJTC_getDataForDepandantField(\"".$MJTC_wpnonce."\",\"" . $MJTC_field->field . "\",\"" . $MJTC_field->depandant_field . "\",2);";
                        if (!isset(majesticsupport::$_data[0]->id) && !empty($MJTC_field->defaultvalue)) {
                            $majesticsupport_js = "
                                jQuery(document).ready(function(){
                                    ".$msFunction."
                                });
                            ";
                            wp_add_inline_script('majestic-support-cmain-js', $majesticsupport_js);
                        }
                    }
                    $msFunction .= $MJTC_VisibleFunction;
                    $MJTC_valuearray = array();
                    if ($MJTC_value != '') {
                        $MJTC_valuearray = MJTC_majesticsupportphplib::MJTC_explode(', ',$MJTC_value);
                    }
                    foreach ($MJTC_obj_option AS $MJTC_option) {
                        $MJTC_check = '';
                        $MJTC_option = html_entity_decode($MJTC_option);
                        if(in_array($MJTC_option, $MJTC_valuearray)){
                            $MJTC_check = 'checked';
                        }
                        $MJTC_readonly = '';
                        if($MJTC_field->readonly){
                            $MJTC_readonly = 'tabindex=-1';
                        }
                        $MJTC_html .= '<div class="ms-formfield-radio-button-wrap mjtc-support-radio-box" '. $MJTC_field_width .'>';
                            $MJTC_html .= '<input type="radio" ' . esc_attr($MJTC_check) . ' ' . esc_attr($MJTC_readonly) . ' class="radiobutton mjtc-support-radio-btn '.esc_attr($MJTC_cssclass).' '.esc_attr($MJTC_specialClass).esc_attr($MJTC_readonlyclass).'" value="' . esc_attr($MJTC_option) . '" id="' . esc_attr($MJTC_field->field) . '_' . esc_attr($MJTC_i) . '" name="' . esc_attr($MJTC_field->field) . '" data-validation ="'.esc_attr($MJTC_cssclass).'" onclick = "'.esc_js($msFunction).'"> ';
                            $MJTC_html .= '<label for="' . esc_attr($MJTC_field->field) . '_' . esc_attr($MJTC_i) . '" id="foruf_checkbox1">' . esc_html($MJTC_option) . '</label>';
                        $MJTC_html .= '</div>';
                        $MJTC_i++;
                    }
                }
                break;
            case 'combo':
                $MJTC_comboOptions = array();
                if (!empty($MJTC_field->userfieldparams)) {
                    $MJTC_obj_option = json_decode($MJTC_field->userfieldparams);
                    foreach ($MJTC_obj_option as $MJTC_opt) {
                        $MJTC_opt = html_entity_decode($MJTC_opt);
                        $MJTC_comboOptions[] = (object) array('id' => $MJTC_opt, 'text' => $MJTC_opt);
                    }
                }
                //code for handling dependent field
                $msFunction = '';
                if ($MJTC_field->depandant_field != null) {
                    $MJTC_wpnonce = wp_create_nonce("data-for-depandant-field-".$MJTC_field->depandant_field);
                    $msFunction = "MJTC_getDataForDepandantField(\"".$MJTC_wpnonce."\",\"" . $MJTC_field->field . "\",\"" . $MJTC_field->depandant_field . "\",1);";
                    if (!isset(majesticsupport::$_data[0]->id) && !empty($MJTC_field->defaultvalue)) {
                        $majesticsupport_js = "
                            jQuery(document).ready(function(){
                                ".$msFunction."
                            });
                        ";
                        wp_add_inline_script('majestic-support-cmain-js', $majesticsupport_js);
                    }
                }
                $msFunction .= $MJTC_VisibleFunction;
                //end
                $MJTC_html .= wp_kses(MJTC_formfield::MJTC_select($MJTC_field->field, $MJTC_comboOptions, $MJTC_value, esc_html(__('Select', 'majestic-support')) . ' ' . esc_attr(majesticsupport::MJTC_getVarValue($MJTC_field->fieldtitle)) , array('data-validation' => $MJTC_cssclass, 'onchange' => $msFunction, 'class' => 'inputbox mjtc-form-select-field mjtc-support-custom-select one '.esc_attr($MJTC_specialClass).esc_attr($MJTC_readonlyclass)) + ($MJTC_field->readonly ? ['tabindex' => '-1'] : [])), MJTC_ALLOWED_TAGS);
                break;
            case 'depandant_field':
                $MJTC_comboOptions = array();
                if ($MJTC_value != null) {
                    if (!empty($MJTC_field->userfieldparams)) {
                        $MJTC_obj_option = $this->MJTC_getDataForDepandantFieldByParentField($MJTC_field->field, $MJTC_userfielddataarray);
                        foreach ($MJTC_obj_option as $MJTC_opt) {
                            $MJTC_opt = html_entity_decode($MJTC_opt);
                            $MJTC_comboOptions[] = (object) array('id' => $MJTC_opt, 'text' => $MJTC_opt);
                        }
                    }
                }
                //code for handling dependent field
                $msFunction = '';
                if ($MJTC_field->depandant_field != null) {
                    $MJTC_wpnonce = wp_create_nonce("data-for-depandant-field-".$MJTC_field->depandant_field);
                    $msFunction = "MJTC_getDataForDepandantField(\"".$MJTC_wpnonce."\",\"" . $MJTC_field->field . "\",\"" . $MJTC_field->depandant_field . "\");";
                    if (!isset(majesticsupport::$_data[0]->id) && !empty($MJTC_field->defaultvalue)) {
                        $majesticsupport_js = "
                            jQuery(document).ready(function(){
                                ".$msFunction."
                            });
                        ";
                        wp_add_inline_script('majestic-support-cmain-js', $majesticsupport_js);
                    }
                }
                $msFunction .= $MJTC_VisibleFunction;
                //end
                $MJTC_html .= wp_kses(MJTC_formfield::MJTC_select($MJTC_field->field, $MJTC_comboOptions, $MJTC_value, esc_html(__('Select', 'majestic-support')) . ' ' . esc_attr(majesticsupport::MJTC_getVarValue($MJTC_field->fieldtitle)) , array('data-validation' => $MJTC_cssclass, 'onchange' => $msFunction, 'class' => 'inputbox mjtc-form-select-field mjtc-support-custom-select one '. esc_attr($MJTC_specialClass). esc_attr($MJTC_readonlyclass)) + ($MJTC_field->readonly ? ['tabindex' => '-1'] : [])), MJTC_ALLOWED_TAGS);
                break;
            case 'multiple':
                $MJTC_comboOptions = array();
                if (!empty($MJTC_field->userfieldparams)) {
                    $MJTC_obj_option = json_decode($MJTC_field->userfieldparams);
                    foreach ($MJTC_obj_option as $MJTC_opt) {
                        $MJTC_opt = html_entity_decode($MJTC_opt);
                        $MJTC_comboOptions[] = (object) array('id' => $MJTC_opt, 'text' => $MJTC_opt);
                    }
                }
                $MJTC_array = $MJTC_field->field;
                $MJTC_array .= '[]';
                $MJTC_valuearray = array();
                if ($MJTC_value != '') {
                    $MJTC_valuearray = MJTC_majesticsupportphplib::MJTC_explode(', ', $MJTC_value);
                }
                $MJTC_html .= wp_kses(MJTC_formfield::MJTC_select($MJTC_array, $MJTC_comboOptions, $MJTC_valuearray, esc_html(__('Select', 'majestic-support')) . ' ' . esc_attr(majesticsupport::MJTC_getVarValue($MJTC_field->fieldtitle)) , array('data-validation' => $MJTC_cssclass, 'onchange' => $MJTC_VisibleFunction, 'multiple' => 'multiple', 'class' => 'inputbox mjtc-form-input-field mjtc-form-multi-select-field one '. esc_attr($MJTC_specialClass).$MJTC_readonlyclass) + ($MJTC_field->readonly ? ['tabindex' => '-1'] : [])), MJTC_ALLOWED_TAGS);
                break;
            case 'file':
                $MJTC_html .= '<span class="mjtc-attachment-file-box">';
                    $MJTC_html .= '<input type="file" name="'.esc_attr($MJTC_field->field).'" id="'.esc_attr($MJTC_field->field).'"/>';
                $MJTC_html .= '</span>';
                if($MJTC_value != null){
                    $MJTC_html .= wp_kses(MJTC_formfield::MJTC_hidden($MJTC_field->field.'_1', 0), MJTC_ALLOWED_TAGS);
                    $MJTC_html .= wp_kses(MJTC_formfield::MJTC_hidden($MJTC_field->field.'_2',$MJTC_value), MJTC_ALLOWED_TAGS);
                    $msFunction = "MJTC_deleteCutomUploadedFile('". esc_js($MJTC_field->field) ."_1')";
                    $MJTC_html .='<span class='.esc_attr($MJTC_field->field).'_1>'. esc_html($MJTC_value) .'( ';
                    $MJTC_html .= "<a href='#' onClick=\"MJTC_deleteCutomUploadedFile('".esc_js($MJTC_field->field)."_1')\"  class=".esc_attr($MJTC_specialClass)." >". esc_html(__('Delete', 'majestic-support'))."</a>";
                    $MJTC_html .= ' )</span>';
                }
                break;
            case 'termsandconditions':
                if (isset(majesticsupport::$_data[0]->id)) {
                    break;
                }
                if (!empty($MJTC_field->userfieldparams)) {
                    $MJTC_obj_option = json_decode($MJTC_field->userfieldparams,true);

                    $MJTC_url = '#';
                    if( isset($MJTC_obj_option['termsandconditions_linktype']) && $MJTC_obj_option['termsandconditions_linktype'] == 1){
                        $MJTC_url = $MJTC_obj_option['termsandconditions_link'];
                    }if( isset($MJTC_obj_option['termsandconditions_linktype']) && $MJTC_obj_option['termsandconditions_linktype'] == 2){
                        $MJTC_url  = get_permalink($MJTC_obj_option['termsandconditions_page']);
                    }

                    $MJTC_link_start = '<a href="' . esc_url($MJTC_url) . '" class="termsandconditions_link_anchor" target="_blank" >';
                    $MJTC_link_end = '</a>';

                    if(MJTC_majesticsupportphplib::MJTC_strstr($MJTC_obj_option['termsandconditions_text'], '[link]') && MJTC_majesticsupportphplib::MJTC_strstr($MJTC_obj_option['termsandconditions_text'], '[/link]')){
                        $MJTC_label_string = MJTC_majesticsupportphplib::MJTC_str_replace('[link]', $MJTC_link_start, $MJTC_obj_option['termsandconditions_text']);
                        $MJTC_label_string = MJTC_majesticsupportphplib::MJTC_str_replace('[/link]', $MJTC_link_end, $MJTC_label_string);
                    }elseif($MJTC_obj_option['termsandconditions_linktype'] == 3){
                        $MJTC_label_string = $MJTC_obj_option['termsandconditions_text'];
                    }else{
                        $MJTC_label_string = wp_kses($MJTC_link_start, MJTC_ALLOWED_TAGS).$MJTC_obj_option['termsandconditions_text'].wp_kses($MJTC_link_end, MJTC_ALLOWED_TAGS);
                    }
                    $MJTC_c_field_required = '';
                    if($MJTC_field->required == 1){
                        $MJTC_c_field_required = 'required';
                    }
                    // ticket terms and conditonions are required.
                    if($MJTC_field->fieldfor == 1 && empty($MJTC_field->isuserfield)){
                        if (empty(trim($MJTC_field->visibleparams))) {
                            $MJTC_c_field_required = 'required';
                        } else {
                            $MJTC_c_field_required = '';
                        }
                    }

                    $MJTC_html .= '<div class="mjtc-support-custom-terms-and-condition-box ">';
                    $MJTC_html .= '<input type="checkbox" class="radiobutton mjtc-support-append-radio-btn '.esc_attr($MJTC_specialClass).'" value="1" id="' . esc_attr($MJTC_field->field) . '" name="' . esc_attr($MJTC_field->field) . '" data-validation="'.esc_attr($MJTC_c_field_required).'">';
                    $MJTC_html .= '<label for="' . esc_attr($MJTC_field->field) . '" id="foruf_checkbox1">' . wp_kses($MJTC_label_string, MJTC_ALLOWED_TAGS) . '</label>';
                    $MJTC_html .= '</div>';
                }
                break;
        }
        $MJTC_html .= '</div>';
        if(!empty($MJTC_field->description)) {
            $MJTC_html .= '<div class="' . esc_attr($MJTC_div4) . '">'. esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->description)) .'</div>';
        }
        $MJTC_html .= '</div>';
        echo wp_kses($MJTC_html, MJTC_ALLOWED_TAGS);

    }

    function MJTC_formCustomFieldsForSearch($MJTC_field, &$MJTC_i, $MJTC_isadmin = 0) {
        if ($MJTC_field->isuserfield != 1 || $MJTC_field->userfieldtype == 'termsandconditions' || $MJTC_field->userfieldtype == 'file')
            return false;
        $MJTC_cssclass = "";
        $MJTC_html = '';
        $MJTC_i++;
        $MJTC_required = $MJTC_field->required;
        if ($MJTC_field->userfieldtype == 'checkbox' || $MJTC_field->userfieldtype == 'radio') {
            $MJTC_div1 = 'mjtc-filter-field-wrp mjtc-filter-radio-checkbox-field-wrp ';
        } else {
            $MJTC_div1 = 'mjtc-filter-field-wrp';
        }
        $MJTC_div2 = 'mjtc-filter-field-label';
        $MJTC_div3 = 'mjtc-filter-value';

        $MJTC_html = '<div class="' . esc_attr($MJTC_div1) . '"> ';
        $MJTC_html .= '<label class="' . esc_attr($MJTC_div2) . '">'. esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->fieldtitle)).'< /label> ';
        $MJTC_html .= ' <div class="' . esc_attr($MJTC_div3) . '">';
        if($MJTC_isadmin == 1){
            $MJTC_html = ''; // only field send
        }
        $MJTC_readonly = ''; //$MJTC_field->readonly ? "'readonly => 'readonly'" : "";
        $MJTC_maxlength = ''; //$MJTC_field->maxlength ? "'maxlength' => '".esc_html($MJTC_field->maxlength) : "";
        $MJTC_fvalue = "";
        $MJTC_value = null;
        $MJTC_userdataid = "";
        $MJTC_userfielddataarray = array();
        if (isset(majesticsupport::$_data['filter']['params'])) {
            $MJTC_userfielddataarray = majesticsupport::$_data['filter']['params'];
            $MJTC_uffield = $MJTC_field->field;
            //had to user || oprator bcz of radio buttons

            if (isset($MJTC_userfielddataarray[$MJTC_uffield]) || !empty($MJTC_userfielddataarray[$MJTC_uffield])) {
                $MJTC_value = $MJTC_userfielddataarray[$MJTC_uffield];
            } else {
                $MJTC_value = '';
            }
        }
        switch ($MJTC_field->userfieldtype) {
            case 'text':
                $MJTC_html .= '<div class="mjtc-filter-inputfield-wrp">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="input-icon">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12"></path>
                        </svg>';
                $MJTC_html .= wp_kses(MJTC_formfield::MJTC_text($MJTC_field->field, $MJTC_value, array('class' => 'inputbox mjtc-form-input-field one', 'data-validation' => $MJTC_cssclass,'placeholder' => majesticsupport::MJTC_getVarValue($MJTC_field->fieldtitle) , $MJTC_maxlength, $MJTC_readonly)), MJTC_ALLOWED_TAGS);
                $MJTC_html .= '</div>';
                break;
            case 'email':
                $MJTC_html .= '<div class="mjtc-filter-inputfield-wrp">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="input-icon">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"></path>
                        </svg>';
                $MJTC_html .= wp_kses(MJTC_formfield::MJTC_text($MJTC_field->field, $MJTC_value, array('class' => 'inputbox mjtc-form-input-field one', 'data-validation' => $MJTC_cssclass,'placeholder' => majesticsupport::MJTC_getVarValue($MJTC_field->fieldtitle) , $MJTC_maxlength, $MJTC_readonly)), MJTC_ALLOWED_TAGS);
                $MJTC_html .= '</div>';
                break;
            case 'date':
                    $MJTC_html .= '<div class="mjtc-filter-inputfield-wrp">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="input-icon">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"></path>
                        </svg>';
                    $MJTC_html .= wp_kses(MJTC_formfield::MJTC_text($MJTC_field->field, $MJTC_value, array('class' => 'custom_date mjtc-form-date-field one mjtc-form-input-field', 'data-validation' => $MJTC_cssclass,'placeholder' => majesticsupport::MJTC_getVarValue($MJTC_field->fieldtitle))), MJTC_ALLOWED_TAGS);
                    $MJTC_html .= '</div>';
                break;
            case 'editor':
                $MJTC_html .= wp_kses_post(wp_editor(isset($MJTC_value) ? $MJTC_value : '', $MJTC_field->field, array('media_buttons' => false, 'data-validation' => $MJTC_cssclass)));
                break;
            case 'textarea':
                $MJTC_html .= wp_kses(MJTC_formfield::MJTC_textarea($MJTC_field->field, $MJTC_value, array('class' => 'inputbox mjtc-form-input-field one', 'data-validation' => $MJTC_cssclass, 'rows' => $MJTC_field->rows, 'cols' => $MJTC_field->cols, $MJTC_readonly)), MJTC_ALLOWED_TAGS);
                break;
            case 'checkbox':
                if (!empty($MJTC_field->userfieldparams)) {
                    $MJTC_comboOptions = array();
                    $MJTC_obj_option = json_decode($MJTC_field->userfieldparams);
                    $total_options= count($MJTC_obj_option);
                    if($MJTC_isadmin != 1){
                        if($total_options % 2 == 0) {
                            $MJTC_field_width = 'style = " width:calc(100% / 2 - 4px); margin:2px;height:46px;"';
                        } else {
                            $MJTC_field_width = 'style = " width:calc(100% / 3 - 4px); margin:2px;height:46px;"';
                        }
                    } else {
                        $MJTC_field_width = '';
                    }
                    $MJTC_i = 0;
                    if(empty($MJTC_value))
                        $MJTC_value = array();
                    $MJTC_html .= '<div class="mjtc-form-cust-rad-fld-wrp mjtc-form-cust-ckb-fld-wrp">';
                    foreach ($MJTC_obj_option AS $MJTC_option) {
                        $MJTC_option = html_entity_decode($MJTC_option);
                        if( in_array($MJTC_option, $MJTC_value)){
                            $MJTC_check = 'checked="true"';
                        }else{
                            $MJTC_check = '';
                        }
                        $MJTC_html .= '<div class="mjtc-support-radio-box" '. $MJTC_field_width .'>';
                        $MJTC_html .= '<input type="checkbox" ' . esc_attr($MJTC_check) . ' class="radiobutton" value="' . esc_attr($MJTC_option) . '" id="' . esc_attr($MJTC_field->field) . '_' . esc_attr($MJTC_i) . '" name="' . esc_attr($MJTC_field->field) . '[]">';
                        $MJTC_html .= '<label for="' . esc_attr($MJTC_field->field) . '_' . esc_attr($MJTC_i) . '" id="foruf_checkbox1">' . esc_html($MJTC_option) . '</label>';
                        $MJTC_html .= '</div>';
                        $MJTC_i++;
                    }
                    $MJTC_html .= '</div>';
                } else {
                    $MJTC_comboOptions = array('1' => majesticsupport::MJTC_getVarValue($MJTC_field->fieldtitle) );
                    $MJTC_html .= wp_kses(MJTC_formfield::MJTC_checkbox($MJTC_field->field, $MJTC_comboOptions, $MJTC_value, array('class' => 'radiobutton')), MJTC_ALLOWED_TAGS);
                }
                break;
            case 'radio':
                if($MJTC_isadmin == 1){
                    $MJTC_comboOptions = array();
                    if (!empty($MJTC_field->userfieldparams)) {
                        $MJTC_obj_option = json_decode($MJTC_field->userfieldparams);
                        for ($MJTC_i = 0; $MJTC_i < count($MJTC_obj_option); $MJTC_i++) {
                            $MJTC_obj_option[$MJTC_i] = html_entity_decode($MJTC_obj_option[$MJTC_i]);
                            $MJTC_comboOptions[$MJTC_obj_option[$MJTC_i]] = "$MJTC_obj_option[$MJTC_i]";
                        }
                    }
                    $msFunction = '';
                    if ($MJTC_field->depandant_field != null) {
                        $MJTC_wpnonce = wp_create_nonce("data-for-depandant-field-".$MJTC_field->depandant_field);
                        $msFunction = "MJTC_getDataForDepandantField('". esc_js($MJTC_wpnonce) ."','" . esc_js($MJTC_field->field) . "','" . esc_js($MJTC_field->depandant_field) . "',2);";
                    }
                    $MJTC_html .= '<div class="mjtc-form-cust-rad-fld-wrp">';
                    $MJTC_html .= wp_kses(MJTC_formfield::MJTC_radiobutton($MJTC_field->field, $MJTC_comboOptions, $MJTC_value, array('data-validation' => $MJTC_cssclass, "autocomplete" => "off", 'onclick' => $msFunction)), MJTC_ALLOWED_TAGS);
                    $MJTC_html .= '</div>';
                }else{
                    $MJTC_comboOptions = array();
                    if (!empty($MJTC_field->userfieldparams)) {
                        $MJTC_obj_option = json_decode($MJTC_field->userfieldparams);
                        $total_options= count($MJTC_obj_option);
                        if($total_options % 2 == 0) {
                            $MJTC_field_width = 'style = " width:calc(100% / 2 - 4px); margin:2px 2px;"';
                        } else {
                            $MJTC_field_width = 'style = " width:calc(100% / 3 - 4px); margin:2px 2px;"';
                        }
                        $MJTC_i = 0;
                        $msFunction = '';
                        if ($MJTC_field->depandant_field != null) {
                            $MJTC_wpnonce = wp_create_nonce("data-for-depandant-field-".$MJTC_field->depandant_field);
                            $msFunction = "MJTC_getDataForDepandantField('". esc_js($MJTC_wpnonce) ."','" . esc_js($MJTC_field->field) . "','" . esc_js($MJTC_field->depandant_field) . "',2);";
                        }
                        $MJTC_valuearray = array();
                        if ($MJTC_value != '') {
                            $MJTC_valuearray = MJTC_majesticsupportphplib::MJTC_explode(', ',$MJTC_value);
                        }
                        $MJTC_html .= '<div class="mjtc-form-cust-rad-fld-wrp">';
                        foreach ($MJTC_obj_option AS $MJTC_option) {
                            $MJTC_check = '';
                            $MJTC_option = html_entity_decode($MJTC_option);
                            if(in_array($MJTC_option, $MJTC_valuearray)){
                                $MJTC_check = 'checked';
                            }
                            $MJTC_html .= '<div class="mjtc-support-radio-box" '. $MJTC_field_width .'>';
                                $MJTC_html .= '<input type="radio" ' . esc_attr($MJTC_check) . ' class="radiobutton mjtc-support-radio-btn '.esc_attr($MJTC_cssclass).'" value="' . esc_attr($MJTC_option) . '" id="' . esc_attr($MJTC_field->field) . '_' . esc_attr($MJTC_i) . '" name="' . esc_attr($MJTC_field->field) . '" data-validation ="'.esc_attr($MJTC_cssclass).'" onclick = "'.$msFunction.'"> ';
                                $MJTC_html .= '<label for="' . esc_attr($MJTC_field->field) . '_' . esc_attr($MJTC_i) . '" id="foruf_checkbox1">' . esc_html($MJTC_option) . '</label>';
                            $MJTC_html .= '</div>';
                            $MJTC_i++;
                        }
                        $MJTC_html .= '</div>';
                    }
                }

                break;
            case 'combo':
                $MJTC_comboOptions = array();
                if (!empty($MJTC_field->userfieldparams)) {
                    $MJTC_obj_option = json_decode($MJTC_field->userfieldparams);
                    foreach ($MJTC_obj_option as $MJTC_opt) {
                        $MJTC_opt = html_entity_decode($MJTC_opt);
                        $MJTC_comboOptions[] = (object) array('id' => $MJTC_opt, 'text' => $MJTC_opt);
                    }
                }
                //code for handling dependent field
                $msFunction = '';
                if ($MJTC_field->depandant_field != null) {
                    $MJTC_wpnonce = wp_create_nonce("data-for-depandant-field-".$MJTC_field->depandant_field);
                    $msFunction = "MJTC_getDataForDepandantField('".$MJTC_wpnonce."','" . $MJTC_field->field . "','" . $MJTC_field->depandant_field . "',1);";
                }
                //end
                $MJTC_html .= '<div class="mjtc-filter-inputfield-wrp">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="input-icon"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 5.25h16.5m-16.5 4.5h16.5m-16.5 4.5h16.5m-16.5 4.5h16.5"></path></svg>';
                $MJTC_html .= wp_kses(MJTC_formfield::MJTC_select($MJTC_field->field, $MJTC_comboOptions, $MJTC_value, esc_html(__('Select', 'majestic-support')) . ' ' . esc_attr(majesticsupport::MJTC_getVarValue($MJTC_field->fieldtitle)) , array('data-validation' => $MJTC_cssclass, 'onchange' => $msFunction, 'class' => 'inputbox mjtc-form-select-field one')), MJTC_ALLOWED_TAGS);
                $MJTC_html .= '</div>';
                break;
            case 'depandant_field':
                $MJTC_comboOptions = array();
                if (!empty($MJTC_field->userfieldparams)) {
                    $MJTC_obj_option = $this->MJTC_getDataForDepandantFieldByParentField($MJTC_field->field, $MJTC_userfielddataarray);
                    if (!empty($MJTC_obj_option)) {
                        foreach ($MJTC_obj_option as $MJTC_opt) {
                            $MJTC_opt = html_entity_decode($MJTC_opt);
                            $MJTC_comboOptions[] = (object) array('id' => $MJTC_opt, 'text' => $MJTC_opt);
                        }
                    }
                }
                //code for handling dependent field
                $msFunction = '';
                if ($MJTC_field->depandant_field != null) {
                    $MJTC_wpnonce = wp_create_nonce("data-for-depandant-field-".$MJTC_field->depandant_field);
                    $msFunction = "MJTC_getDataForDepandantField('". $MJTC_wpnonce."','" . $MJTC_field->field . "','" . $MJTC_field->depandant_field . "');";
                }
                //end
                $MJTC_html .= '<div class="mjtc-filter-inputfield-wrp">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="input-icon"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"></path></svg>';
                $MJTC_html .= wp_kses(MJTC_formfield::MJTC_select($MJTC_field->field, $MJTC_comboOptions, $MJTC_value, esc_html(__('Select', 'majestic-support')) . ' ' . esc_attr(majesticsupport::MJTC_getVarValue($MJTC_field->fieldtitle)) , array('data-validation' => $MJTC_cssclass, 'onchange' => $msFunction, 'class' => 'inputbox mjtc-form-select-field one')), MJTC_ALLOWED_TAGS);
                $MJTC_html .= '</div>';
                break;
            case 'multiple':
                $MJTC_comboOptions = array();
                if (!empty($MJTC_field->userfieldparams)) {
                    $MJTC_obj_option = json_decode($MJTC_field->userfieldparams);
                    foreach ($MJTC_obj_option as $MJTC_opt) {
                        $MJTC_opt = html_entity_decode($MJTC_opt);
                        $MJTC_comboOptions[] = (object) array('id' => $MJTC_opt, 'text' => $MJTC_opt);
                    }
                }
                $MJTC_array = $MJTC_field->field;
                $MJTC_array .= '[]';
                $MJTC_html .= '<div class="mjtc-filter-inputfield-wrp">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="input-icon"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"></path></svg>';
                    $MJTC_html .= wp_kses(MJTC_formfield::MJTC_select($MJTC_array, $MJTC_comboOptions, $MJTC_value, esc_html(__('Select', 'majestic-support')) . ' ' . esc_attr(majesticsupport::MJTC_getVarValue($MJTC_field->fieldtitle)) , array('data-validation' => $MJTC_cssclass, 'multiple' => 'multiple','class' => 'inputbox mjtc-form-multi-select-field')), MJTC_ALLOWED_TAGS);
                $MJTC_html .= '</div>';
                break;
        }
        if($MJTC_isadmin == 1){
            echo wp_kses($MJTC_html, MJTC_ALLOWED_TAGS);
            return;
        }
        $MJTC_html .= '</div></div>';
        echo wp_kses($MJTC_html, MJTC_ALLOWED_TAGS);

    }

    function MJTC_showCustomFields($MJTC_field, $MJTC_fieldfor, $MJTC_params) {

        $MJTC_fvalue = '';

        if(!empty($MJTC_params)){
            $MJTC_data = json_decode($MJTC_params,true);
            if(is_array($MJTC_data) && $MJTC_data != ''){
                if(array_key_exists($MJTC_field->field, $MJTC_data)){
                    $MJTC_fvalue = $MJTC_data[$MJTC_field->field];
                    $MJTC_fvalue = MJTC_majesticsupportphplib::MJTC_htmlspecialchars($MJTC_fvalue);
                }
            }
        }
        if($MJTC_field->userfieldtype=='file'){

            if($MJTC_fvalue !=null){
                if (is_admin()) {
                    $MJTC_path = admin_url("?page=majesticsupport_ticket&action=mstask&task=downloadbyname&id=".esc_attr(majesticsupport::$_data['custom']['ticketid'])."&name=".esc_attr($MJTC_fvalue));
                } else {
                    $MJTC_path = majesticsupport::makeUrl(array('mjsmod'=>'ticket', 'task'=>'downloadbyname','action'=>'mstask','id'=> majesticsupport::$_data['custom']['ticketid'] ,'name'=>$MJTC_fvalue ,'mspageid'=>get_the_ID()));
                }
                $MJTC_html = '
                    <div class="mjtc_supportattachment">
                        ' .  wp_kses($MJTC_fvalue, MJTC_ALLOWED_TAGS) . '
                        <a style="margin: 0 5px;" class="button my-download-file-btn" target="_blank" href="' . esc_url($MJTC_path) . '">
                            <svg class="mjtc-support-icon" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                        </a>
                    </div>';
                $MJTC_fvalue = $MJTC_html;
            }
        }elseif($MJTC_field->userfieldtype=='date' && !empty($MJTC_fvalue)){
            if(MJTC_majesticsupportphplib::MJTC_strpos($MJTC_fvalue , '1970') !== false){
                $MJTC_fvalue = "";
            } else {
                $MJTC_fvalue = date_i18n(majesticsupport::$_config['date_format'],MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_fvalue));
            }
        }
        $MJTC_return_array['title'] = $MJTC_field->fieldtitle;
        $MJTC_return_array['value'] = $MJTC_fvalue;
        return $MJTC_return_array;
    }

    function MJTC_userFieldsData($MJTC_fieldfor, $MJTC_listing = null, $MJTC_multiformid = '') {
        if(!is_numeric($MJTC_fieldfor)){
            return false;
        }
        if ($MJTC_multiformid == '') {
            $MJTC_multiformid = MJTC_includer::MJTC_getModel('ticket')->getDefaultMultiFormId();
        }
        if(!is_numeric($MJTC_multiformid)){
            return false;
        }
        if (MJTC_includer::MJTC_getObjectClass('user')->MJTC_isguest()) {
            $MJTC_published = ' isvisitorpublished = 1 ';
        } else {
            $MJTC_published = ' published = 1 ';
        }
        $MJTC_inquery = '';
        if ($MJTC_listing == 1) {
            $MJTC_inquery = ' AND showonlisting = 1 ';
        }
        if (!is_admin()) {
            $MJTC_inquery .= ' AND adminonly != 1 ';
        }
        $MJTC_query = "SELECT field,fieldtitle,isuserfield,userfieldtype,userfieldparams,multiformid  FROM " . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering WHERE isuserfield = 1 AND " . $MJTC_published . " AND fieldfor =" . esc_sql($MJTC_fieldfor) . $MJTC_inquery. " AND multiformid =" . esc_sql($MJTC_multiformid). " ORDER BY ordering";
        $MJTC_data = majesticsupport::$_db->get_results($MJTC_query);
        return $MJTC_data;
    }

    function userFieldsForSearch($MJTC_fieldfor) {
        if(!is_numeric($MJTC_fieldfor)){
            return false;
        }
        if (MJTC_includer::MJTC_getObjectClass('user')->MJTC_isguest()) {
            $MJTC_inquery = ' isvisitorpublished = 1';
        } else {
            $MJTC_inquery = ' published = 1 AND search_user =1';
        }
        if(!is_admin()){
            $MJTC_inquery .= " AND adminonly != 1";
        }

        $MJTC_query = "SELECT `rows`,`cols`,required,field,fieldtitle,isuserfield,userfieldtype,userfieldparams,depandant_field  FROM " . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering WHERE isuserfield = 1 AND " . $MJTC_inquery . " AND fieldfor =" . esc_sql($MJTC_fieldfor) ." ORDER BY ordering ";
        $MJTC_data = majesticsupport::$_db->get_results($MJTC_query);
        return $MJTC_data;
    }

    function adminFieldsForSearch($MJTC_fieldfor) {
        if(!is_numeric($MJTC_fieldfor)){
            return false;
        }
        $MJTC_formFilter = '';
        if(!in_array('multiform', majesticsupport::$_active_addons)){
            $MJTC_formid = MJTC_includer::MJTC_getModel('ticket')->getDefaultMultiFormId();
            $MJTC_formFilter = " AND multiformid = " . intval($MJTC_formid);
        }

        $MJTC_query = "SELECT `rows`,`cols`,required,field,fieldtitle,isuserfield,userfieldtype,userfieldparams,depandant_field  FROM " . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering WHERE isuserfield = 1 AND published = 1 AND search_admin =1 AND fieldfor =" . esc_sql($MJTC_fieldfor);
        $MJTC_query .= $MJTC_formFilter;
        $MJTC_query .= " ORDER BY ordering ASC";
        $MJTC_data = majesticsupport::$_db->get_results($MJTC_query);
        return $MJTC_data;
    }

    function MJTC_getDataForDepandantFieldByParentField($MJTC_fieldfor, $MJTC_data) {
        if (MJTC_includer::MJTC_getObjectClass('user')->MJTC_isguest()) {
            $MJTC_published = ' isvisitorpublished = 1 ';
        } else {
            $MJTC_published = ' published = 1 ';
        }
        $MJTC_value = '';
        $MJTC_returnarray = array();
        $MJTC_query = "SELECT field from " . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering WHERE isuserfield = 1 AND " . $MJTC_published . " AND depandant_field ='" . esc_sql($MJTC_fieldfor) . "'";
        $MJTC_field = majesticsupport::$_db->get_var($MJTC_query);
        if ($MJTC_data != null) {
            foreach ($MJTC_data as $MJTC_key => $MJTC_val) {
                $MJTC_key = html_entity_decode($MJTC_key);
                if ($MJTC_key == $MJTC_field) {
                    $MJTC_value = $MJTC_val;
                }
            }
        }
        $MJTC_query = "SELECT userfieldparams from " . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering WHERE isuserfield = 1 AND " . $MJTC_published . " AND field ='" . esc_sql($MJTC_fieldfor) . "'";
        $MJTC_field = majesticsupport::$_db->get_var($MJTC_query);
        $MJTC_fieldarray = json_decode($MJTC_field);
        foreach ($MJTC_fieldarray as $MJTC_key => $MJTC_val) {
            $MJTC_key = html_entity_decode($MJTC_key);
            if ($MJTC_value == $MJTC_key)
                $MJTC_returnarray = $MJTC_val;
        }
        return $MJTC_returnarray;
    }

}

?>
