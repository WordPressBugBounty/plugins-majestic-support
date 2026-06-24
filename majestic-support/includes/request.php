<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_request {
    /*
     * Check Request from both the Get and post method
     */

    static function MJTC_getVar($MJTC_variable_name, $method = null, $MJTC_defaultvalue = null, $typecast = null) {
        $MJTC_value = null;
        if ($method == null) {
            if (isset($_GET[$MJTC_variable_name])) {
                if(is_array($_GET[$MJTC_variable_name])){
                    $MJTC_value = majesticsupport::MJTC_sanitizeData($_GET[$MJTC_variable_name]);// MJTC_sanitizeData() function uses wordpress santize functions
                }else{
                    $MJTC_value = majesticsupport::MJTC_sanitizeData(wp_unslash($_GET[$MJTC_variable_name]));// MJTC_sanitizeData() function uses wordpress santize functions
                }
            } elseif (isset($_POST[$MJTC_variable_name])) {
                if(is_array($_POST[$MJTC_variable_name])){
                    $MJTC_value = majesticsupport::MJTC_sanitizeData($_POST[$MJTC_variable_name]);// MJTC_sanitizeData() function uses wordpress santize functions
                }else{
                    $MJTC_value = majesticsupport::MJTC_sanitizeData($_POST[$MJTC_variable_name]);// MJTC_sanitizeData() function uses wordpress santize functions
                }
            } elseif (get_query_var($MJTC_variable_name)) {
                $MJTC_value = get_query_var($MJTC_variable_name);
            } elseif (isset(majesticsupport::$_data['sanitized_args'][$MJTC_variable_name]) && majesticsupport::$_data['sanitized_args'][$MJTC_variable_name] != '') {
                $MJTC_value = majesticsupport::$_data['sanitized_args'][$MJTC_variable_name];
            }
        } else {
            $method = MJTC_majesticsupportphplib::MJTC_strtolower($method);
            switch ($method) {
                case 'post':
                    if (isset($_POST[$MJTC_variable_name]))
                        if (is_array($_POST[$MJTC_variable_name])) {
                            $MJTC_value = majesticsupport::MJTC_sanitizeData($_POST[$MJTC_variable_name]);// MJTC_sanitizeData() function uses wordpress santize functions
                        }else{
                            $MJTC_value = majesticsupport::MJTC_sanitizeData($_POST[$MJTC_variable_name]);// MJTC_sanitizeData() function uses wordpress santize functions
                        }
                    break;
                case 'get':
                    if (isset($_GET[$MJTC_variable_name]))
                        if (is_array($_GET[$MJTC_variable_name])) {
                            $MJTC_value = majesticsupport::MJTC_sanitizeData($_GET[$MJTC_variable_name]);// MJTC_sanitizeData() function uses wordpress santize functions
                        }else{
                            $MJTC_value = majesticsupport::MJTC_sanitizeData(wp_unslash($_GET[$MJTC_variable_name]));// MJTC_sanitizeData() function uses wordpress santize functions
                        }
                    break;
            }
        }
        if ($typecast != null) {
            $typecast = MJTC_majesticsupportphplib::MJTC_strtolower($typecast);
            switch ($typecast) {
                case "int":
                    $MJTC_value = (int) $MJTC_value;
                    break;
                case "string":
                    $MJTC_value = (string) $MJTC_value;
                    break;
            }
        }
        if ($MJTC_value == null)
            $MJTC_value = $MJTC_defaultvalue;
        if(!is_array($MJTC_value)){
            if ($MJTC_value != null){
                $MJTC_value = MJTC_majesticsupportphplib::MJTC_stripslashes($MJTC_value);
            }
        }
        
        return $MJTC_value;
    }

    /*
     * Check Request from both the Get and post method
     */

    static function get($method = null) {
        $MJTC_array = null;
        if ($method != null) {
            $method = MJTC_majesticsupportphplib::MJTC_strtolower($method);
            switch ($method) {
                case 'post':
                    $MJTC_array = majesticsupport::MJTC_sanitizeData($_POST);// MJTC_sanitizeData() function uses wordpress santize functions
                    break;
                case 'get':
                    $MJTC_array = majesticsupport::MJTC_sanitizeData($_GET);// MJTC_sanitizeData() function uses wordpress santize functions
                    break;
            }
            foreach($MJTC_array as $MJTC_key=>$MJTC_value){
                if(is_string($MJTC_value)){
                    $MJTC_array[$MJTC_key] = MJTC_majesticsupportphplib::MJTC_stripslashes($MJTC_value);
                }
            }
        }
        return $MJTC_array;
    }

    /*
     * Check Request from both the Get and post method
     */

    static function MJTC_getLayout($MJTC_layout, $method, $MJTC_defaultvalue) {
        $MJTC_layoutname = null;
        if ($method != null) {
            $method = MJTC_majesticsupportphplib::MJTC_strtolower($method);
            switch ($method) {
                case 'post':
                    $MJTC_layoutname = majesticsupport::MJTC_sanitizeData($_POST[$MJTC_layout]);// MJTC_sanitizeData() function uses wordpress santize functions
                    break;
                case 'get':
                    $MJTC_layoutname = majesticsupport::MJTC_sanitizeData($_GET[$MJTC_layout]);// MJTC_sanitizeData() function uses wordpress santize functions
                    break;
            }
        } else {
            if (isset($_POST[$MJTC_layout]))
                $MJTC_layoutname = majesticsupport::MJTC_sanitizeData($_POST[$MJTC_layout]);// MJTC_sanitizeData() function uses wordpress santize functions
            elseif (isset($_GET[$MJTC_layout]))
                $MJTC_layoutname = majesticsupport::MJTC_sanitizeData($_GET[$MJTC_layout]);// MJTC_sanitizeData() function uses wordpress santize functions
            elseif (get_query_var($MJTC_layout))
                $MJTC_layoutname = get_query_var($MJTC_layout);
            elseif (isset(majesticsupport::$_data['sanitized_args'][$MJTC_layout]) && majesticsupport::$_data['sanitized_args'][$MJTC_layout] != '')
              $MJTC_layoutname = majesticsupport::$_data['sanitized_args'][$MJTC_layout];
        }
        if ($MJTC_layoutname == null) {
            $MJTC_layoutname = $MJTC_defaultvalue;
        }
        if (is_admin()) {
            $MJTC_layoutname = 'admin_' . $MJTC_layoutname;
        }
        return $MJTC_layoutname;
    }

}

?>
