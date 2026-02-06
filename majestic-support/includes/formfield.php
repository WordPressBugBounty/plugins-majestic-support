<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_formfield {
    /*
     * Create the form text field
     */

    static function MJTC_text($name, $MJTC_value, $extraattr = array()) {
        $textfield = '<input type="text" name="' . esc_attr($name) . '" id="' . esc_attr($name) . '" value="' . esc_attr($MJTC_value) . '" ';
        if (!empty($extraattr))
            foreach ($extraattr AS $MJTC_key => $MJTC_val)
                $textfield .= ' ' . esc_attr($MJTC_key) . '="' . esc_attr($MJTC_val) . '"';
        $textfield .= ' />';
        return $textfield;
    }
    /*
     * Create the form text field
     */

    static function MJTC_email($name, $MJTC_value, $extraattr = array()) {
        $textfield = '<input type="email" name="' . esc_attr($name) . '" id="' . esc_attr($name) . '" value="' . esc_attr($MJTC_value) . '" ';
        if (!empty($extraattr))
            foreach ($extraattr AS $MJTC_key => $MJTC_val)
                $textfield .= ' ' . esc_attr($MJTC_key) . '="' . esc_attr($MJTC_val) . '"';
        $textfield .= ' />';
        return $textfield;
    }

    /*
     * Create the form password field
     */

    static function MJTC_password($name, $MJTC_value, $extraattr = array()) {
        $textfield = '<input type="password" name="' . esc_attr($name) . '" id="' . esc_attr($name) . '" value="' . esc_attr($MJTC_value) . '" ';
        if (!empty($extraattr))
            foreach ($extraattr AS $MJTC_key => $MJTC_val)
                $textfield .= ' ' . esc_attr($MJTC_key) . '="' . esc_attr($MJTC_val) . '"';
        $textfield .= ' />';
        return $textfield;
    }

    /*
     * Create the form text area
     */

    static function MJTC_textarea($name, $MJTC_value, $extraattr = array()) {
        $textarea = '<textarea name="' . esc_attr($name) . '" id="' . esc_attr($name) . '" ';
        if (!empty($extraattr))
            foreach ($extraattr AS $MJTC_key => $MJTC_val)
                $textarea .= ' ' . esc_attr($MJTC_key) . '="' . esc_attr($MJTC_val) . '"';
        $textarea .= ' >' . esc_html($MJTC_value) . '</textarea>';
        return $textarea;
    }

    /*
     * Create the form hidden field
     */

    static function MJTC_hidden($name, $MJTC_value, $extraattr = array()) {
        $textfield = '<input type="hidden" name="' . esc_attr($name) . '" id="' . esc_attr($name) . '" value="' . esc_attr($MJTC_value) . '" ';
        if (!empty($extraattr))
            foreach ($extraattr AS $MJTC_key => $MJTC_val)
                $textfield .= ' ' . esc_attr($MJTC_key) . '="' . esc_attr($MJTC_val) . '"';
        $textfield .= ' />';
        return $textfield;
    }

    /*
     * Create the form submitbutton
     */

    static function MJTC_submitbutton($name, $MJTC_value, $extraattr = array()) {
        $textfield = '<input type="submit" name="' . esc_attr($name) . '" id="' . esc_attr($name) . '" value="' . esc_attr($MJTC_value) . '" ';
        if (!empty($extraattr))
            foreach ($extraattr AS $MJTC_key => $MJTC_val)
                $textfield .= ' ' . esc_attr($MJTC_key) . '="' . esc_attr($MJTC_val) . '"';
        $textfield .= ' />';
        return $textfield;
    }

    /*
     * Create the form button
     */

    static function MJTC_button($name, $MJTC_value, $extraattr = array()) {
        $textfield = '<input type="button" name="' . esc_attr($name) . '" id="' . esc_attr($name) . '" value="' . esc_attr($MJTC_value) . '" ';
        if (!empty($extraattr))
            foreach ($extraattr AS $MJTC_key => $MJTC_val)
                $textfield .= ' ' . esc_attr($MJTC_key) . '="' . esc_attr($MJTC_val) . '"';
        $textfield .= ' />';
        return $textfield;
    }

    /*
     * Create the form select field
     */

    static function MJTC_select($name, $list, $MJTC_defaultvalue, $title = '', $extraattr = array()) {
        $selectfield = '<select name="' . esc_attr($name) . '" id="' . esc_attr($name) . '" ';
        if (!empty($extraattr))
            foreach ($extraattr AS $MJTC_key => $MJTC_val) {
                $selectfield .= ' ' . esc_attr($MJTC_key) . '="' . esc_attr($MJTC_val) . '"';
            }
        $selectfield .= ' >';
        if ($title != '') {
            $selectfield .= '<option value="">' . esc_html(majesticsupport::MJTC_getVarValue($title)) . '</option>';
        }
        if (!empty($list))
            foreach ($list AS $record) {
                if ((is_array($MJTC_defaultvalue) && in_array($record->id, $MJTC_defaultvalue)) || $MJTC_defaultvalue == $record->id)
                    $selectfield .= '<option selected="selected" value="' . esc_attr($record->id) . '">' . esc_html(majesticsupport::MJTC_getVarValue($record->text)) . '</option>';
                else
                    $selectfield .= '<option value="' . esc_attr($record->id) . '">' . esc_html(majesticsupport::MJTC_getVarValue($record->text)) . '</option>';
            }

        $selectfield .= '</select>';
        return $selectfield;
    }

    /*
     * Create the form radio button
     */

    static function MJTC_radiobutton($name, $list, $MJTC_defaultvalue, $extraattr = array()) {
        $radiobutton = '';
        $MJTC_count = 1;
        foreach ($list AS $MJTC_value => $label) {

            $radiobutton .= '<div class="ms-formfield-radio-button-wrap" >';
            $radiobutton .= '<input type="radio" name="' . esc_attr($name) . '" id="' . esc_attr($name) . esc_attr($MJTC_count) . '" value="' . esc_attr($MJTC_value) . '"';
            if ($MJTC_defaultvalue == $MJTC_value)
                $radiobutton .= ' checked="checked"';
            if (!empty($extraattr))
                foreach ($extraattr AS $MJTC_key => $MJTC_val) {
                    $radiobutton .= ' ' . esc_attr($MJTC_key) . '="' . esc_attr($MJTC_val) . '"';
                }
            $radiobutton .= '/><label id="for' . esc_attr($name). esc_attr($MJTC_count) . '" for="' . esc_attr($name) . esc_attr($MJTC_count) . '">' . esc_html($label) . '</label>';
            $radiobutton .= '</div>';
            $MJTC_count++;
        }
        return $radiobutton;
    }

    /*
     * Create the form checkbox
     */

    static function MJTC_checkbox($name, $list, $MJTC_defaultvalue, $extraattr = array()) {
        $checkbox = '';
        $MJTC_count = 1;
        foreach ($list AS $MJTC_value => $label) {
            $checkbox .= '<input type="checkbox" name="' . esc_attr($name) . '" id="' . esc_attr($name) . esc_attr($MJTC_count) . '" value="' . esc_attr($MJTC_value) . '"';
            if(is_array($MJTC_defaultvalue)){
                if (in_array($MJTC_value, $MJTC_defaultvalue))
                    $checkbox .= ' checked="checked"';
            }else{
                if ($MJTC_defaultvalue == $MJTC_value)
                    $checkbox .= ' checked="checked"';
            }

            if (!empty($extraattr))
                foreach ($extraattr AS $MJTC_key => $MJTC_val) {
                    $checkbox .= ' ' . esc_attr($MJTC_key) . '="' . esc_attr($MJTC_val) . '"';
                }
            $checkbox .= '/><label id="for' . esc_attr($name) . '" for="' . esc_attr($name) . esc_attr($MJTC_count) . '">' . esc_html($label) . '</label>';
            $MJTC_count++;
        }
        return $checkbox;
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
