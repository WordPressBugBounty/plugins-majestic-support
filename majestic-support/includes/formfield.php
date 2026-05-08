<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_formfield {
    /*
     * Create the form text field
     */

    static function MJTC_text($MJTC_name, $MJTC_value, $MJTC_extraattr = array()) {
        $MJTC_textfield = '<input type="text" name="' . esc_attr($MJTC_name) . '" id="' . esc_attr($MJTC_name) . '" value="' . esc_attr($MJTC_value) . '" ';
        if (!empty($MJTC_extraattr))
            foreach ($MJTC_extraattr AS $MJTC_key => $MJTC_val)
                $MJTC_textfield .= ' ' . esc_attr($MJTC_key) . '="' . esc_attr($MJTC_val) . '"';
        $MJTC_textfield .= ' />';
        return $MJTC_textfield;
    }
    /*
     * Create the form text field
     */

    static function MJTC_email($MJTC_name, $MJTC_value, $MJTC_extraattr = array()) {
        $MJTC_textfield = '<input type="email" name="' . esc_attr($MJTC_name) . '" id="' . esc_attr($MJTC_name) . '" value="' . esc_attr($MJTC_value) . '" ';
        if (!empty($MJTC_extraattr))
            foreach ($MJTC_extraattr AS $MJTC_key => $MJTC_val)
                $MJTC_textfield .= ' ' . esc_attr($MJTC_key) . '="' . esc_attr($MJTC_val) . '"';
        $MJTC_textfield .= ' />';
        return $MJTC_textfield;
    }

    /*
     * Create the form password field
     */

    static function MJTC_password($MJTC_name, $MJTC_value, $MJTC_extraattr = array()) {
        $MJTC_textfield = '<input type="password" name="' . esc_attr($MJTC_name) . '" id="' . esc_attr($MJTC_name) . '" value="' . esc_attr($MJTC_value) . '" ';
        if (!empty($MJTC_extraattr))
            foreach ($MJTC_extraattr AS $MJTC_key => $MJTC_val)
                $MJTC_textfield .= ' ' . esc_attr($MJTC_key) . '="' . esc_attr($MJTC_val) . '"';
        $MJTC_textfield .= ' />';
        return $MJTC_textfield;
    }

    /*
     * Create the form text area
     */

    static function MJTC_textarea($MJTC_name, $MJTC_value, $MJTC_extraattr = array()) {
        $MJTC_textarea = '<textarea name="' . esc_attr($MJTC_name) . '" id="' . esc_attr($MJTC_name) . '" ';
        if (!empty($MJTC_extraattr))
            foreach ($MJTC_extraattr AS $MJTC_key => $MJTC_val)
                $MJTC_textarea .= ' ' . esc_attr($MJTC_key) . '="' . esc_attr($MJTC_val) . '"';
        $MJTC_textarea .= ' >' . esc_html($MJTC_value) . '</textarea>';
        return $MJTC_textarea;
    }

    /*
     * Create the form hidden field
     */

    static function MJTC_hidden($MJTC_name, $MJTC_value, $MJTC_extraattr = array()) {
        $MJTC_textfield = '<input type="hidden" name="' . esc_attr($MJTC_name) . '" id="' . esc_attr($MJTC_name) . '" value="' . esc_attr($MJTC_value) . '" ';
        if (!empty($MJTC_extraattr))
            foreach ($MJTC_extraattr AS $MJTC_key => $MJTC_val)
                $MJTC_textfield .= ' ' . esc_attr($MJTC_key) . '="' . esc_attr($MJTC_val) . '"';
        $MJTC_textfield .= ' />';
        return $MJTC_textfield;
    }

    /*
     * Create the form submitbutton
     */

    static function MJTC_submitbutton($MJTC_name, $MJTC_value, $MJTC_extraattr = array()) {
        $MJTC_textfield = '<input type="submit" name="' . esc_attr($MJTC_name) . '" id="' . esc_attr($MJTC_name) . '" value="' . esc_attr($MJTC_value) . '" ';
        if (!empty($MJTC_extraattr))
            foreach ($MJTC_extraattr AS $MJTC_key => $MJTC_val)
                $MJTC_textfield .= ' ' . esc_attr($MJTC_key) . '="' . esc_attr($MJTC_val) . '"';
        $MJTC_textfield .= ' />';
        return $MJTC_textfield;
    }

    /*
     * Create the form button
     */

    static function MJTC_button($MJTC_name, $MJTC_value, $MJTC_extraattr = array()) {
        $MJTC_textfield = '<input type="button" name="' . esc_attr($MJTC_name) . '" id="' . esc_attr($MJTC_name) . '" value="' . esc_attr($MJTC_value) . '" ';
        if (!empty($MJTC_extraattr))
            foreach ($MJTC_extraattr AS $MJTC_key => $MJTC_val)
                $MJTC_textfield .= ' ' . esc_attr($MJTC_key) . '="' . esc_attr($MJTC_val) . '"';
        $MJTC_textfield .= ' />';
        return $MJTC_textfield;
    }

    /*
     * Create the form select field
     */

    static function MJTC_select($MJTC_name, $MJTC_list, $MJTC_defaultvalue, $title = '', $MJTC_extraattr = array()) {
        $MJTC_selectfield = '<select name="' . esc_attr($MJTC_name) . '" id="' . esc_attr($MJTC_name) . '" ';
        if (!empty($MJTC_extraattr))
            foreach ($MJTC_extraattr AS $MJTC_key => $MJTC_val) {
                $MJTC_selectfield .= ' ' . esc_attr($MJTC_key) . '="' . esc_attr($MJTC_val) . '"';
            }
        $MJTC_selectfield .= ' >';
        if ($title != '') {
            $MJTC_selectfield .= '<option value="">' . esc_html(majesticsupport::MJTC_getVarValue($title)) . '</option>';
        }
        if (!empty($MJTC_list))
            foreach ($MJTC_list AS $MJTC_record) {
                if ((is_array($MJTC_defaultvalue) && in_array($MJTC_record->id, $MJTC_defaultvalue)) || $MJTC_defaultvalue == $MJTC_record->id)
                    $MJTC_selectfield .= '<option selected="selected" value="' . esc_attr($MJTC_record->id) . '">' . esc_html(majesticsupport::MJTC_getVarValue($MJTC_record->text)) . '</option>';
                else
                    $MJTC_selectfield .= '<option value="' . esc_attr($MJTC_record->id) . '">' . esc_html(majesticsupport::MJTC_getVarValue($MJTC_record->text)) . '</option>';
            }

        $MJTC_selectfield .= '</select>';
        return $MJTC_selectfield;
    }

    /*
     * Create the form radio button
     */

    static function MJTC_radiobutton($MJTC_name, $MJTC_list, $MJTC_defaultvalue, $MJTC_extraattr = array()) {
        $MJTC_radiobutton = '';
        $MJTC_count = 1;
        foreach ($MJTC_list AS $MJTC_value => $MJTC_label) {

            $MJTC_radiobutton .= '<div class="ms-formfield-radio-button-wrap" >';
            $MJTC_radiobutton .= '<input type="radio" name="' . esc_attr($MJTC_name) . '" id="' . esc_attr($MJTC_name) . esc_attr($MJTC_count) . '" value="' . esc_attr($MJTC_value) . '"';
            if ($MJTC_defaultvalue == $MJTC_value)
                $MJTC_radiobutton .= ' checked="checked"';
            if (!empty($MJTC_extraattr))
                foreach ($MJTC_extraattr AS $MJTC_key => $MJTC_val) {
                    $MJTC_radiobutton .= ' ' . esc_attr($MJTC_key) . '="' . esc_attr($MJTC_val) . '"';
                }
            $MJTC_radiobutton .= '/><label id="for' . esc_attr($MJTC_name). esc_attr($MJTC_count) . '" for="' . esc_attr($MJTC_name) . esc_attr($MJTC_count) . '">' . esc_html($MJTC_label) . '</label>';
            $MJTC_radiobutton .= '</div>';
            $MJTC_count++;
        }
        return $MJTC_radiobutton;
    }

    /*
     * Create the form checkbox
     */

    static function MJTC_checkbox($MJTC_name, $MJTC_list, $MJTC_defaultvalue, $MJTC_extraattr = array()) {
        $MJTC_checkbox = '';
        $MJTC_count = 1;
        foreach ($MJTC_list AS $MJTC_value => $MJTC_label) {
            $MJTC_checkbox .= '<input type="checkbox" name="' . esc_attr($MJTC_name) . '" id="' . esc_attr($MJTC_name) . esc_attr($MJTC_count) . '" value="' . esc_attr($MJTC_value) . '"';
            if(is_array($MJTC_defaultvalue)){
                if (in_array($MJTC_value, $MJTC_defaultvalue))
                    $MJTC_checkbox .= ' checked="checked"';
            }else{
                if ($MJTC_defaultvalue == $MJTC_value)
                    $MJTC_checkbox .= ' checked="checked"';
            }

            if (!empty($MJTC_extraattr))
                foreach ($MJTC_extraattr AS $MJTC_key => $MJTC_val) {
                    $MJTC_checkbox .= ' ' . esc_attr($MJTC_key) . '="' . esc_attr($MJTC_val) . '"';
                }
            $MJTC_checkbox .= '/><label id="for' . esc_attr($MJTC_name) . '" for="' . esc_attr($MJTC_name) . esc_attr($MJTC_count) . '">' . esc_html($MJTC_label) . '</label>';
            $MJTC_count++;
        }
        return $MJTC_checkbox;
    }

    static function MJTC_setFormData($MJTC_data) {
        MJTC_includer::MJTC_getObjectClass('wphdnotification')->MJTC_addSessionNotificationDataToTable($MJTC_data,'submitform','submitform');
    }

    static function MJTC_getFormData() {
        $MJTC_data = MJTC_includer::MJTC_getObjectClass('wphdnotification')->MJTC_getNotificationDatabySessionId('submitform',true);
        return $MJTC_data;
    }
}

?>
