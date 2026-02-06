<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_customfields {
    function MJTC_formCustomFields($field) {
        if($field->isuserfield != 1){
            return false;
        }
        // Handle adminonly case
        // Visible only on admin and agent form
        if( in_array('agent',majesticsupport::$_active_addons) ){
            $agent = MJTC_includer::MJTC_getModel('agent')->isUserStaff();
        }else{
            $agent = false;
        }
        if(!empty($field->adminonly) && !is_admin() && !$agent){
            return false;
        }
        // show termsandconditions only on user form
        if($field->userfieldtype == 'termsandconditions' && (is_admin() || $agent)){
            return false;
        }
        $cssclass = "";
        $visibleclass = "";
        if (!empty($field->visibleparams) && $field->visibleparams != '[]'){
            $visibleclass = "visible";
        }
        $html = '';
        $div1 =  ($field->size == 100 || $field->userfieldtype == 'termsandconditions') ? ' mjtc-support-from-field-wrp-full-width mjtc-support-from-field-wrp '.esc_attr($visibleclass) : 'mjtc-support-from-field-wrp '.esc_attr($visibleclass);
        $div2 = 'mjtc-support-from-field-title';
        $div3 = 'mjtc-support-from-field';
        $div4 = 'mjtc-support-from-field-description';


        if(is_admin()){
            $div1 = ($field->size == 100) ? 'mjtc-form-wrapper mjtc-form-custm-flds-wrp fullwidth '.esc_attr($visibleclass) : 'mjtc-form-wrapper mjtc-form-custm-flds-wrp '.esc_attr($visibleclass);
            $div2 = 'mjtc-form-title';
            $div3 = 'mjtc-form-value';
            $div4 = 'mjtc-form-description';
        }


        $required = $field->required;
        if($field->userfieldtype == 'termsandconditions'){
            if (isset(majesticsupport::$_data[0]->id)) {
                return false;
            }
            $required = 1;
            if (isset($field->visibleparams) && $field->visibleparams !='') {
                $required = 0;
            }
        }

        $html = '<div class="' . esc_attr($div1) .  '">';
        // hide title in case of termsandconditions
        if($field->userfieldtype != 'termsandconditions'){
            $html .= '<div class="' . esc_attr($div2) . '">';
            if ($required == 1 && $visibleclass != 'visible' && !empty($field->fieldtitle)) {
                $html .= esc_html(majesticsupport::MJTC_getVarValue($field->fieldtitle)) . '<span style="color: red;" >*</span>';
                    $cssclass = "required";
            }else {
                $html .= esc_html(majesticsupport::MJTC_getVarValue($field->fieldtitle));
                    $cssclass = "";
            }
            $html .= ' </div>';
        }
        $html .= ' <div class="' . esc_attr($div3) . '">';
        $readonlyclass = $field->readonly ? " mjtc-form-ticket-readonly " : "";
        $maxlength = $field->maxlength ? "$field->maxlength" : "";
        $fvalue = "";
        $MJTC_value = "";
        $userdataid = "";
        $specialClass="";
        if (isset(majesticsupport::$_data[0]->id)) {
            $userfielddataarray = json_decode(majesticsupport::$_data[0]->params);
            $uffield = $field->field;
            if (isset($userfielddataarray->$uffield) && !empty($userfielddataarray->$uffield)) {
                $MJTC_value = $userfielddataarray->$uffield;
                $specialClass='specialClass';
            } else {
                $MJTC_value = '';
            }
        } else {
            if (!empty(majesticsupport::$_data[0]->params)) {
                $userfielddataarray = json_decode(majesticsupport::$_data[0]->params);
            }
            $MJTC_value = $field->defaultvalue;
        }
        // Handle visible field case
        $msVisibleFunction = '';
        // For default function (default value setting)
        $MJTC_defaultFunc = '';
        if ($field->visible_field != null) {
            $visibleparams = MJTC_includer::MJTC_getModel('fieldordering')->MJTC_getDataForVisibleField($field->visible_field);
            if (!empty($visibleparams)) {
                $wpnonce = wp_create_nonce("is-field-required-".$field->visible_field);
                $jsObject = wp_json_encode($visibleparams);
                $msVisibleFunction = " MJTC_getDataForVisibleField(\"".esc_js($wpnonce)."\", this.value, \"" . esc_js($field->visible_field) . "\", " . $jsObject.");";
                if (!empty($MJTC_value) && !isset(majesticsupport::$_data[0]->id)) {
                    $MJTC_defaultFunc = " MJTC_getDataForVisibleField(\"".$wpnonce."\", '".esc_js($MJTC_value)."', \"" . esc_js($field->visible_field) . "\", " . $jsObject.");";
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
        switch ($field->userfieldtype) {
            case 'text':
                $html .= wp_kses(MJTC_formfield::MJTC_text($field->field, $MJTC_value, array('class' => 'inputbox mjtc-form-input-field mjtc-support-form-field-input one '.esc_attr($specialClass), 'data-validation' => $cssclass, 'onchange' => $msVisibleFunction, 'maxlength' => $maxlength, 'placeholder'=> majesticsupport::MJTC_getVarValue($field->placeholder)) + ($field->readonly ? ['readonly' => 'readonly'] : [])), MJTC_ALLOWED_TAGS);
                break;
            case 'email':
                $html .= wp_kses(MJTC_formfield::MJTC_email($field->field, $MJTC_value, array('class' => 'inputbox mjtc-form-input-field mjtc-support-form-field-input one '. esc_attr($specialClass), 'data-validation' => $cssclass, 'onchange' => $msVisibleFunction, 'maxlength' => $maxlength, 'placeholder'=> majesticsupport::MJTC_getVarValue($field->placeholder)) + ($field->readonly ? ['readonly' => 'readonly'] : [])), MJTC_ALLOWED_TAGS);
                break;
            case 'date':
                if(MJTC_majesticsupportphplib::MJTC_strpos($MJTC_value , '1970') !== false){
                    $MJTC_value = "";
                }
                $calendarClass = '';
                if (empty($field->readonly)) {
                    $calendarClass = ' custom_date ';
                }
                $html .= wp_kses(MJTC_formfield::MJTC_text($field->field, $MJTC_value, array('class' => esc_attr($calendarClass).'mjtc-form-date-field  mjtc-support-input-field  one '. esc_attr($specialClass), 'data-validation' => $cssclass, 'onchange' => $msVisibleFunction, 'placeholder'=> majesticsupport::MJTC_getVarValue($field->placeholder)) + ($field->readonly ? ['readonly' => 'readonly'] : [])), MJTC_ALLOWED_TAGS);
                break;
            case 'textarea':
                $html .= wp_kses(MJTC_formfield::MJTC_textarea($field->field, $MJTC_value, array('class' => 'inputbox mjtc-form-textarea-field mjtc-support-custom-textarea one '.esc_attr($specialClass), 'data-validation' => $cssclass, 'rows' => $field->rows, 'cols' => $field->cols, 'placeholder'=> majesticsupport::MJTC_getVarValue($field->placeholder)) + ($field->readonly ? ['readonly' => 'readonly'] : [])), MJTC_ALLOWED_TAGS);
                break;
            case 'checkbox':
                if (!empty($field->userfieldparams)) {
                    $comboOptions = array();
                    $obj_option = json_decode($field->userfieldparams);
                    $total_options= count($obj_option);
                    if($total_options % 2 == 0) {
                        $field_width = 'style = " width:calc(100% / 2 - 4px); margin:2px 2px;"';
                    } else {
                        $field_width = 'style = " width:calc(100% / 3 - 4px); margin:2px 2px;"';
                    }
                    $i = 0;
                    $MJTC_valuearray = array();
                    if ($MJTC_value != '') {
                        $MJTC_valuearray = MJTC_majesticsupportphplib::MJTC_explode(', ',$MJTC_value);
                    }
                    foreach ($obj_option AS $option) {
                        $check = '';
                        $option = html_entity_decode($option);
                        if(in_array($option, $MJTC_valuearray)){
                            $check = 'checked';
                        }
                        $readonly = '';
                        if($field->readonly){
                            $readonly = 'readonly';
                        }
                        $html .= '<div class="ms-formfield-radio-button-wrap mjtc-support-custom-radio-box" '. $field_width .'>';
                        $html .= '<input type="checkbox" ' . esc_attr($readonly) . ' ' . esc_attr($check) . ' class="radiobutton mjtc-support-append-radio-btn '.esc_attr($specialClass).esc_attr($readonlyclass).'" value="' . esc_attr($option) . '" id="' . esc_attr($field->field) . '_' . esc_attr($i) . '" name="' . esc_attr($field->field) . '[]" onclick = "'.esc_js($msVisibleFunction).'">';
                        $html .= '<label for="' . esc_attr($field->field) . '_' . esc_attr($i) . '" id="foruf_checkbox1">' . esc_html($option) . '</label>';
                        $html .= '</div>';
                        $i++;
                    }
                } else {
                    $comboOptions = array('1' => majesticsupport::MJTC_getVarValue($field->fieldtitle));
                    $html .= wp_kses(MJTC_formfield::MJTC_checkbox($field->field, $comboOptions, $MJTC_value, array('class' => 'radiobutton')), MJTC_ALLOWED_TAGS);
                }
                break;
            case 'radio':
                $comboOptions = array();
                if (!empty($field->userfieldparams)) {
                    $obj_option = json_decode($field->userfieldparams);
                    $total_options= count($obj_option);
                    if($total_options % 2 == 0) {
                        $field_width = 'style = " width:calc(100% / 2 - 4px); margin:2px 2px;"';
                    } else {
                        $field_width = 'style = " width:calc(100% / 3 - 4px); margin:2px 2px;"';
                    }
                    $i = 0;
                    $msFunction = '';
                    if ($field->depandant_field != null) {
                        $wpnonce = wp_create_nonce("data-for-depandant-field-".$field->depandant_field);
                        $msFunction = "MJTC_getDataForDepandantField(\"".$wpnonce."\",\"" . $field->field . "\",\"" . $field->depandant_field . "\",2);";
                        if (!isset(majesticsupport::$_data[0]->id) && !empty($field->defaultvalue)) {
                            $majesticsupport_js = "
                                jQuery(document).ready(function(){
                                    ".$msFunction."
                                });
                            ";
                            wp_add_inline_script('majestic-support-cmain-js', $majesticsupport_js);
                        }
                    }
                    $msFunction .= $msVisibleFunction;
                    $MJTC_valuearray = array();
                    if ($MJTC_value != '') {
                        $MJTC_valuearray = MJTC_majesticsupportphplib::MJTC_explode(', ',$MJTC_value);
                    }
                    foreach ($obj_option AS $option) {
                        $check = '';
                        $option = html_entity_decode($option);
                        if(in_array($option, $MJTC_valuearray)){
                            $check = 'checked';
                        }
                        $readonly = '';
                        if($field->readonly){
                            $readonly = 'tabindex=-1';
                        }
                        $html .= '<div class="ms-formfield-radio-button-wrap mjtc-support-radio-box" '. $field_width .'>';
                            $html .= '<input type="radio" ' . esc_attr($check) . ' ' . esc_attr($readonly) . ' class="radiobutton mjtc-support-radio-btn '.esc_attr($cssclass).' '.esc_attr($specialClass).esc_attr($readonlyclass).'" value="' . esc_attr($option) . '" id="' . esc_attr($field->field) . '_' . esc_attr($i) . '" name="' . esc_attr($field->field) . '" data-validation ="'.esc_attr($cssclass).'" onclick = "'.esc_js($msFunction).'"> ';
                            $html .= '<label for="' . esc_attr($field->field) . '_' . esc_attr($i) . '" id="foruf_checkbox1">' . esc_html($option) . '</label>';
                        $html .= '</div>';
                        $i++;
                    }
                }
                break;
            case 'combo':
                $comboOptions = array();
                if (!empty($field->userfieldparams)) {
                    $obj_option = json_decode($field->userfieldparams);
                    foreach ($obj_option as $opt) {
                        $opt = html_entity_decode($opt);
                        $comboOptions[] = (object) array('id' => $opt, 'text' => $opt);
                    }
                }
                //code for handling dependent field
                $msFunction = '';
                if ($field->depandant_field != null) {
                    $wpnonce = wp_create_nonce("data-for-depandant-field-".$field->depandant_field);
                    $msFunction = "MJTC_getDataForDepandantField(\"".$wpnonce."\",\"" . $field->field . "\",\"" . $field->depandant_field . "\",1);";
                    if (!isset(majesticsupport::$_data[0]->id) && !empty($field->defaultvalue)) {
                        $majesticsupport_js = "
                            jQuery(document).ready(function(){
                                ".$msFunction."
                            });
                        ";
                        wp_add_inline_script('majestic-support-cmain-js', $majesticsupport_js);
                    }
                }
                $msFunction .= $msVisibleFunction;
                //end
                $html .= wp_kses(MJTC_formfield::MJTC_select($field->field, $comboOptions, $MJTC_value, esc_html(__('Select', 'majestic-support')) . ' ' . esc_attr(majesticsupport::MJTC_getVarValue($field->fieldtitle)) , array('data-validation' => $cssclass, 'onchange' => $msFunction, 'class' => 'inputbox mjtc-form-select-field mjtc-support-custom-select one '.esc_attr($specialClass).esc_attr($readonlyclass)) + ($field->readonly ? ['tabindex' => '-1'] : [])), MJTC_ALLOWED_TAGS);
                break;
            case 'depandant_field':
                $comboOptions = array();
                if ($MJTC_value != null) {
                    if (!empty($field->userfieldparams)) {
                        $obj_option = $this->MJTC_getDataForDepandantFieldByParentField($field->field, $userfielddataarray);
                        foreach ($obj_option as $opt) {
                            $opt = html_entity_decode($opt);
                            $comboOptions[] = (object) array('id' => $opt, 'text' => $opt);
                        }
                    }
                }
                //code for handling dependent field
                $msFunction = '';
                if ($field->depandant_field != null) {
                    $wpnonce = wp_create_nonce("data-for-depandant-field-".$field->depandant_field);
                    $msFunction = "MJTC_getDataForDepandantField(\"".$wpnonce."\",\"" . $field->field . "\",\"" . $field->depandant_field . "\");";
                    if (!isset(majesticsupport::$_data[0]->id) && !empty($field->defaultvalue)) {
                        $majesticsupport_js = "
                            jQuery(document).ready(function(){
                                ".$msFunction."
                            });
                        ";
                        wp_add_inline_script('majestic-support-cmain-js', $majesticsupport_js);
                    }
                }
                $msFunction .= $msVisibleFunction;
                //end
                $html .= wp_kses(MJTC_formfield::MJTC_select($field->field, $comboOptions, $MJTC_value, esc_html(__('Select', 'majestic-support')) . ' ' . esc_attr(majesticsupport::MJTC_getVarValue($field->fieldtitle)) , array('data-validation' => $cssclass, 'onchange' => $msFunction, 'class' => 'inputbox mjtc-form-select-field mjtc-support-custom-select one '. esc_attr($specialClass). esc_attr($readonlyclass)) + ($field->readonly ? ['tabindex' => '-1'] : [])), MJTC_ALLOWED_TAGS);
                break;
            case 'multiple':
                $comboOptions = array();
                if (!empty($field->userfieldparams)) {
                    $obj_option = json_decode($field->userfieldparams);
                    foreach ($obj_option as $opt) {
                        $opt = html_entity_decode($opt);
                        $comboOptions[] = (object) array('id' => $opt, 'text' => $opt);
                    }
                }
                $array = $field->field;
                $array .= '[]';
                $MJTC_valuearray = array();
                if ($MJTC_value != '') {
                    $MJTC_valuearray = MJTC_majesticsupportphplib::MJTC_explode(', ', $MJTC_value);
                }
                $html .= wp_kses(MJTC_formfield::MJTC_select($array, $comboOptions, $MJTC_valuearray, esc_html(__('Select', 'majestic-support')) . ' ' . esc_attr(majesticsupport::MJTC_getVarValue($field->fieldtitle)) , array('data-validation' => $cssclass, 'onchange' => $msVisibleFunction, 'multiple' => 'multiple', 'class' => 'inputbox mjtc-form-input-field mjtc-form-multi-select-field one '. esc_attr($specialClass).$readonlyclass) + ($field->readonly ? ['tabindex' => '-1'] : [])), MJTC_ALLOWED_TAGS);
                break;
            case 'file':
                $html .= '<span class="mjtc-attachment-file-box">';
                    $html .= '<input type="file" name="'.esc_attr($field->field).'" id="'.esc_attr($field->field).'"/>';
                $html .= '</span>';
                if($MJTC_value != null){
                    $html .= wp_kses(MJTC_formfield::MJTC_hidden($field->field.'_1', 0), MJTC_ALLOWED_TAGS);
                    $html .= wp_kses(MJTC_formfield::MJTC_hidden($field->field.'_2',$MJTC_value), MJTC_ALLOWED_TAGS);
                    $msFunction = "MJTC_deleteCutomUploadedFile('". esc_js($field->field) ."_1')";
                    $html .='<span class='.esc_attr($field->field).'_1>'. esc_html($MJTC_value) .'( ';
                    $html .= "<a href='#' onClick=\"MJTC_deleteCutomUploadedFile('".esc_js($field->field)."_1')\"  class=".esc_attr($specialClass)." >". esc_html(__('Delete', 'majestic-support'))."</a>";
                    $html .= ' )</span>';
                }
                break;
            case 'termsandconditions':
                if (isset(majesticsupport::$_data[0]->id)) {
                    break;
                }
                if (!empty($field->userfieldparams)) {
                    $obj_option = json_decode($field->userfieldparams,true);

                    $MJTC_url = '#';
                    if( isset($obj_option['termsandconditions_linktype']) && $obj_option['termsandconditions_linktype'] == 1){
                        $MJTC_url = $obj_option['termsandconditions_link'];
                    }if( isset($obj_option['termsandconditions_linktype']) && $obj_option['termsandconditions_linktype'] == 2){
                        $MJTC_url  = get_permalink($obj_option['termsandconditions_page']);
                    }

                    $link_start = '<a href="' . esc_url($MJTC_url) . '" class="termsandconditions_link_anchor" target="_blank" >';
                    $link_end = '</a>';

                    if(MJTC_majesticsupportphplib::MJTC_strstr($obj_option['termsandconditions_text'], '[link]') && MJTC_majesticsupportphplib::MJTC_strstr($obj_option['termsandconditions_text'], '[/link]')){
                        $label_string = MJTC_majesticsupportphplib::MJTC_str_replace('[link]', $link_start, $obj_option['termsandconditions_text']);
                        $label_string = MJTC_majesticsupportphplib::MJTC_str_replace('[/link]', $link_end, $label_string);
                    }elseif($obj_option['termsandconditions_linktype'] == 3){
                        $label_string = $obj_option['termsandconditions_text'];
                    }else{
                        $label_string = wp_kses($link_start, MJTC_ALLOWED_TAGS).$obj_option['termsandconditions_text'].wp_kses($link_end, MJTC_ALLOWED_TAGS);
                    }
                    $c_field_required = '';
                    if($field->required == 1){
                        $c_field_required = 'required';
                    }
                    // ticket terms and conditonions are required.
                    if($field->fieldfor == 1){
                        if (empty(trim($field->visibleparams))) {
                            $c_field_required = 'required';
                        } else {
                            $c_field_required = '';
                        }
                    }

                    $html .= '<div class="mjtc-support-custom-terms-and-condition-box ms-formfield-radio-button-wrap">';
                    $html .= '<input type="checkbox" class="radiobutton mjtc-support-append-radio-btn '.esc_attr($specialClass).'" value="1" id="' . esc_attr($field->field) . '" name="' . esc_attr($field->field) . '" data-validation="'.esc_attr($c_field_required).'">';
                    $html .= '<label for="' . esc_attr($field->field) . '" id="foruf_checkbox1">' . wp_kses($label_string, MJTC_ALLOWED_TAGS) . '</label>';
                    $html .= '</div>';
                }
                break;
        }
        $html .= '</div>';
        if(!empty($field->description)) {
            $html .= '<div class="' . esc_attr($div4) . '">'. esc_html(majesticsupport::MJTC_getVarValue($field->description)) .'</div>';
        }
        $html .= '</div>';
        echo wp_kses($html, MJTC_ALLOWED_TAGS);

    }

    function MJTC_formCustomFieldsForSearch($field, &$i, $isadmin = 0) {
        if ($field->isuserfield != 1 || $field->userfieldtype == 'termsandconditions' || $field->userfieldtype == 'file')
            return false;
        $cssclass = "";
        $html = '';
        $i++;
        $required = $field->required;
        $div1 = 'mjtc-col-md-3 mjtc-filter-field-wrp';
        $div3 = 'mjtc-filter-value';

        $html = '<div class="' . esc_attr($div1) . '"> ';
        $html .= ' <div class="' . esc_attr($div3) . '">';
        if($isadmin == 1){
            $html = ''; // only field send
        }
        $readonly = ''; //$field->readonly ? "'readonly => 'readonly'" : "";
        $maxlength = ''; //$field->maxlength ? "'maxlength' => '".esc_html($field->maxlength) : "";
        $fvalue = "";
        $MJTC_value = null;
        $userdataid = "";
        $userfielddataarray = array();
        if (isset(majesticsupport::$_data['filter']['params'])) {
            $userfielddataarray = majesticsupport::$_data['filter']['params'];
            $uffield = $field->field;
            //had to user || oprator bcz of radio buttons

            if (isset($userfielddataarray[$uffield]) || !empty($userfielddataarray[$uffield])) {
                $MJTC_value = $userfielddataarray[$uffield];
            } else {
                $MJTC_value = '';
            }
        }
        switch ($field->userfieldtype) {
            case 'text':
            case 'email':
                $html .= wp_kses(MJTC_formfield::MJTC_text($field->field, $MJTC_value, array('class' => 'inputbox mjtc-form-input-field one', 'data-validation' => $cssclass,'placeholder' => majesticsupport::MJTC_getVarValue($field->fieldtitle) , $maxlength, $readonly)), MJTC_ALLOWED_TAGS);
                break;
            case 'date':
                $html .= wp_kses(MJTC_formfield::MJTC_text($field->field, $MJTC_value, array('class' => 'custom_date mjtc-form-date-field one mjtc-form-input-field', 'data-validation' => $cssclass,'placeholder' => majesticsupport::MJTC_getVarValue($field->fieldtitle))), MJTC_ALLOWED_TAGS);
                break;
            case 'editor':
                $html .= wp_kses_post(wp_editor(isset($MJTC_value) ? $MJTC_value : '', $field->field, array('media_buttons' => false, 'data-validation' => $cssclass)));
                break;
            case 'textarea':
                $html .= wp_kses(MJTC_formfield::MJTC_textarea($field->field, $MJTC_value, array('class' => 'inputbox mjtc-form-input-field one', 'data-validation' => $cssclass, 'rows' => $field->rows, 'cols' => $field->cols, $readonly)), MJTC_ALLOWED_TAGS);
                break;
            case 'checkbox':
                if (!empty($field->userfieldparams)) {
                    $comboOptions = array();
                    $obj_option = json_decode($field->userfieldparams);
                    $total_options= count($obj_option);
                    if($isadmin != 1){
                        if($total_options % 2 == 0) {
                            $field_width = 'style = " width:calc(100% / 2 - 4px); margin:2px;height:46px;"';
                        } else {
                            $field_width = 'style = " width:calc(100% / 3 - 4px); margin:2px;height:46px;"';
                        }
                    } else {
                        $field_width = '';
                    }
                    $i = 0;
                    if(empty($MJTC_value))
                        $MJTC_value = array();
                    $html .= '<div class="mjtc-form-cust-rad-fld-wrp mjtc-form-cust-ckb-fld-wrp">';
                    foreach ($obj_option AS $option) {
                        $option = html_entity_decode($option);
                        if( in_array($option, $MJTC_value)){
                            $check = 'checked="true"';
                        }else{
                            $check = '';
                        }
                        $html .= '<div class="mjtc-support-radio-box" '. $field_width .'>';
                        $html .= '<input type="checkbox" ' . esc_attr($check) . ' class="radiobutton" value="' . esc_attr($option) . '" id="' . esc_attr($field->field) . '_' . esc_attr($i) . '" name="' . esc_attr($field->field) . '[]">';
                        $html .= '<label for="' . esc_attr($field->field) . '_' . esc_attr($i) . '" id="foruf_checkbox1">' . esc_html($option) . '</label>';
                        $html .= '</div>';
                        $i++;
                    }
                    $html .= '</div>';
                } else {
                    $comboOptions = array('1' => majesticsupport::MJTC_getVarValue($field->fieldtitle) );
                    $html .= wp_kses(MJTC_formfield::MJTC_checkbox($field->field, $comboOptions, $MJTC_value, array('class' => 'radiobutton')), MJTC_ALLOWED_TAGS);
                }
                break;
            case 'radio':
                if($isadmin == 1){
                    $comboOptions = array();
                    if (!empty($field->userfieldparams)) {
                        $obj_option = json_decode($field->userfieldparams);
                        for ($i = 0; $i < count($obj_option); $i++) {
                            $obj_option[$i] = html_entity_decode($obj_option[$i]);
                            $comboOptions[$obj_option[$i]] = "$obj_option[$i]";
                        }
                    }
                    $msFunction = '';
                    if ($field->depandant_field != null) {
                        $wpnonce = wp_create_nonce("data-for-depandant-field-".$field->depandant_field);
                        $msFunction = "MJTC_getDataForDepandantField('". esc_js($wpnonce) ."','" . esc_js($field->field) . "','" . esc_js($field->depandant_field) . "',2);";
                    }
                    $html .= '<div class="mjtc-form-cust-rad-fld-wrp">';
                    $html .= wp_kses(MJTC_formfield::MJTC_radiobutton($field->field, $comboOptions, $MJTC_value, array('data-validation' => $cssclass, "autocomplete" => "off", 'onclick' => $msFunction)), MJTC_ALLOWED_TAGS);
                    $html .= '</div>';
                }else{
                    $comboOptions = array();
                    if (!empty($field->userfieldparams)) {
                        $obj_option = json_decode($field->userfieldparams);
                        $total_options= count($obj_option);
                        if($total_options % 2 == 0) {
                            $field_width = 'style = " width:calc(100% / 2 - 4px); margin:2px 2px;"';
                        } else {
                            $field_width = 'style = " width:calc(100% / 3 - 4px); margin:2px 2px;"';
                        }
                        $i = 0;
                        $msFunction = '';
                        if ($field->depandant_field != null) {
                            $wpnonce = wp_create_nonce("data-for-depandant-field-".$field->depandant_field);
                            $msFunction = "MJTC_getDataForDepandantField('". esc_js($wpnonce) ."','" . esc_js($field->field) . "','" . esc_js($field->depandant_field) . "',2);";
                        }
                        $MJTC_valuearray = array();
                        if ($MJTC_value != '') {
                            $MJTC_valuearray = MJTC_majesticsupportphplib::MJTC_explode(', ',$MJTC_value);
                        }
                        $html .= '<div class="mjtc-form-cust-rad-fld-wrp">';
                        foreach ($obj_option AS $option) {
                            $check = '';
                            $option = html_entity_decode($option);
                            if(in_array($option, $MJTC_valuearray)){
                                $check = 'checked';
                            }
                            $html .= '<div class="mjtc-support-radio-box" '. $field_width .'>';
                                $html .= '<input type="radio" ' . esc_attr($check) . ' class="radiobutton mjtc-support-radio-btn '.esc_attr($cssclass).'" value="' . esc_attr($option) . '" id="' . esc_attr($field->field) . '_' . esc_attr($i) . '" name="' . esc_attr($field->field) . '" data-validation ="'.esc_attr($cssclass).'" onclick = "'.$msFunction.'"> ';
                                $html .= '<label for="' . esc_attr($field->field) . '_' . esc_attr($i) . '" id="foruf_checkbox1">' . esc_html($option) . '</label>';
                            $html .= '</div>';
                            $i++;
                        }
                        $html .= '</div>';
                    }
                }

                break;
            case 'combo':
                $comboOptions = array();
                if (!empty($field->userfieldparams)) {
                    $obj_option = json_decode($field->userfieldparams);
                    foreach ($obj_option as $opt) {
                        $opt = html_entity_decode($opt);
                        $comboOptions[] = (object) array('id' => $opt, 'text' => $opt);
                    }
                }
                //code for handling dependent field
                $msFunction = '';
                if ($field->depandant_field != null) {
                    $wpnonce = wp_create_nonce("data-for-depandant-field-".$field->depandant_field);
                    $msFunction = "MJTC_getDataForDepandantField('".$wpnonce."','" . $field->field . "','" . $field->depandant_field . "',1);";
                }
                //end
                $html .= wp_kses(MJTC_formfield::MJTC_select($field->field, $comboOptions, $MJTC_value, esc_html(__('Select', 'majestic-support')) . ' ' . esc_attr(majesticsupport::MJTC_getVarValue($field->fieldtitle)) , array('data-validation' => $cssclass, 'onchange' => $msFunction, 'class' => 'inputbox mjtc-form-select-field one')), MJTC_ALLOWED_TAGS);
                break;
            case 'depandant_field':
                $comboOptions = array();
                if (!empty($field->userfieldparams)) {
                    $obj_option = $this->MJTC_getDataForDepandantFieldByParentField($field->field, $userfielddataarray);
                    if (!empty($obj_option)) {
                        foreach ($obj_option as $opt) {
                            $opt = html_entity_decode($opt);
                            $comboOptions[] = (object) array('id' => $opt, 'text' => $opt);
                        }
                    }
                }
                //code for handling dependent field
                $msFunction = '';
                if ($field->depandant_field != null) {
                    $wpnonce = wp_create_nonce("data-for-depandant-field-".$field->depandant_field);
                    $msFunction = "MJTC_getDataForDepandantField('". $wpnonce."','" . $field->field . "','" . $field->depandant_field . "');";
                }
                //end
                $html .= wp_kses(MJTC_formfield::MJTC_select($field->field, $comboOptions, $MJTC_value, esc_html(__('Select', 'majestic-support')) . ' ' . esc_attr(majesticsupport::MJTC_getVarValue($field->fieldtitle)) , array('data-validation' => $cssclass, 'onchange' => $msFunction, 'class' => 'inputbox mjtc-form-select-field one')), MJTC_ALLOWED_TAGS);
                break;
            case 'multiple':
                $comboOptions = array();
                if (!empty($field->userfieldparams)) {
                    $obj_option = json_decode($field->userfieldparams);
                    foreach ($obj_option as $opt) {
                        $opt = html_entity_decode($opt);
                        $comboOptions[] = (object) array('id' => $opt, 'text' => $opt);
                    }
                }
                $array = $field->field;
                $array .= '[]';
                $html .= wp_kses(MJTC_formfield::MJTC_select($array, $comboOptions, $MJTC_value, esc_html(__('Select', 'majestic-support')) . ' ' . esc_attr(majesticsupport::MJTC_getVarValue($field->fieldtitle)) , array('data-validation' => $cssclass, 'multiple' => 'multiple','class' => 'inputbox mjtc-form-multi-select-field')), MJTC_ALLOWED_TAGS);
                break;
        }
        if($isadmin == 1){
            echo wp_kses($html, MJTC_ALLOWED_TAGS);
            return;
        }
        $html .= '</div></div>';
        echo wp_kses($html, MJTC_ALLOWED_TAGS);

    }

    function MJTC_showCustomFields($field, $fieldfor, $params) {

        $fvalue = '';

        if(!empty($params)){
            $MJTC_data = json_decode($params,true);
            if(is_array($MJTC_data) && $MJTC_data != ''){
                if(array_key_exists($field->field, $MJTC_data)){
                    $fvalue = $MJTC_data[$field->field];
                    $fvalue = MJTC_majesticsupportphplib::MJTC_htmlspecialchars($fvalue);
                }
            }
        }
        if($field->userfieldtype=='file'){

           if($fvalue !=null){
                $path = admin_url("?page=majesticsupport_ticket&action=mstask&task=downloadbyname&id=".esc_attr(majesticsupport::$_data['custom']['ticketid'])."&name=".esc_attr($fvalue));
                $html = '
                    <div class="mjtc_supportattachment">
                        ' .  wp_kses($fvalue, MJTC_ALLOWED_TAGS) . '
                        <a class="button my-download-file-btn" target="_blank" href="' . esc_url($path) . '">' . esc_html(__('Download', 'majestic-support')) . '</a>
                    </div>';
                $fvalue = $html;
            }
        }elseif($field->userfieldtype=='date' && !empty($fvalue)){
            if(MJTC_majesticsupportphplib::MJTC_strpos($fvalue , '1970') !== false){
                $fvalue = "";
            } else {
                $fvalue = date_i18n(majesticsupport::$_config['date_format'],MJTC_majesticsupportphplib::MJTC_strtotime($fvalue));
            }
        }
        $return_array['title'] = $field->fieldtitle;
        $return_array['value'] = $fvalue;
        return $return_array;
    }

    function MJTC_userFieldsData($fieldfor, $listing = null, $multiformid = '') {
        if(!is_numeric($fieldfor)){
            return false;
        }
        if ($multiformid == '') {
            $multiformid = MJTC_includer::MJTC_getModel('ticket')->getDefaultMultiFormId();
        }
        if(!is_numeric($multiformid)){
            return false;
        }
        if (MJTC_includer::MJTC_getObjectClass('user')->MJTC_isguest()) {
            $published = ' isvisitorpublished = 1 ';
        } else {
            $published = ' published = 1 ';
        }
        $inquery = '';
        if ($listing == 1) {
            $inquery = ' AND showonlisting = 1 ';
        }
        if (!is_admin()) {
            $inquery .= ' AND adminonly != 1 ';
        }
        $query = "SELECT field,fieldtitle,isuserfield,userfieldtype,userfieldparams,multiformid  FROM " . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering WHERE isuserfield = 1 AND " . $published . " AND fieldfor =" . esc_sql($fieldfor) . $inquery. " AND multiformid =" . esc_sql($multiformid). " ORDER BY ordering";
        $MJTC_data = majesticsupport::$_db->get_results($query);
        return $MJTC_data;
    }

    function userFieldsForSearch($fieldfor) {
        if(!is_numeric($fieldfor)){
            return false;
        }
        if (MJTC_includer::MJTC_getObjectClass('user')->MJTC_isguest()) {
            $inquery = ' isvisitorpublished = 1';
        } else {
            $inquery = ' published = 1 AND search_user =1';
        }
        if(!is_admin()){
            $inquery .= " AND adminonly != 1";
        }

        $query = "SELECT `rows`,`cols`,required,field,fieldtitle,isuserfield,userfieldtype,userfieldparams,depandant_field  FROM " . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering WHERE isuserfield = 1 AND " . $inquery . " AND fieldfor =" . esc_sql($fieldfor) ." ORDER BY ordering ";
        $MJTC_data = majesticsupport::$_db->get_results($query);
        return $MJTC_data;
    }

    function adminFieldsForSearch($fieldfor) {
        if(!is_numeric($fieldfor)){
            return false;
        }

        $query = "SELECT `rows`,`cols`,required,field,fieldtitle,isuserfield,userfieldtype,userfieldparams,depandant_field  FROM " . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering WHERE isuserfield = 1 AND published = 1 AND search_admin =1 AND fieldfor =" . esc_sql($fieldfor) ." ORDER BY ordering ";
        $MJTC_data = majesticsupport::$_db->get_results($query);
        return $MJTC_data;
    }

    function MJTC_getDataForDepandantFieldByParentField($fieldfor, $MJTC_data) {
        if (MJTC_includer::MJTC_getObjectClass('user')->MJTC_isguest()) {
            $published = ' isvisitorpublished = 1 ';
        } else {
            $published = ' published = 1 ';
        }
        $MJTC_value = '';
        $returnarray = array();
        $query = "SELECT field from " . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering WHERE isuserfield = 1 AND " . $published . " AND depandant_field ='" . esc_sql($fieldfor) . "'";
        $field = majesticsupport::$_db->get_var($query);
        if ($MJTC_data != null) {
            foreach ($MJTC_data as $MJTC_key => $MJTC_val) {
                $MJTC_key = html_entity_decode($MJTC_key);
                if ($MJTC_key == $field) {
                    $MJTC_value = $MJTC_val;
                }
            }
        }
        $query = "SELECT userfieldparams from " . majesticsupport::$_db->prefix . "mjtc_support_fieldsordering WHERE isuserfield = 1 AND " . $published . " AND field ='" . esc_sql($fieldfor) . "'";
        $field = majesticsupport::$_db->get_var($query);
        $fieldarray = json_decode($field);
        foreach ($fieldarray as $MJTC_key => $MJTC_val) {
            $MJTC_key = html_entity_decode($MJTC_key);
            if ($MJTC_value == $MJTC_key)
                $returnarray = $MJTC_val;
        }
        return $returnarray;
    }

}

?>
