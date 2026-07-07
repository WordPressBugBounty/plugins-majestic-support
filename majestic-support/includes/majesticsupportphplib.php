<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_majesticsupportphplib {

    function __construct() {
    }

    static function MJTC_str_replace($MJTC_search,$MJTC_replace,$MJTC_content){
        if($MJTC_content == ''){
            return $MJTC_content;
        }
        if($MJTC_replace === null){
            return $MJTC_content;
        }

        $MJTC_content = str_replace($MJTC_search, $MJTC_replace, $MJTC_content);
        return $MJTC_content;
    }

    static function MJTC_safe_encoding($MJTC_string){
        if($MJTC_string == ''){
            return $MJTC_string;
        }
        $MJTC_string = base64_encode($MJTC_string);
        //return mb_convert_encoding($MJTC_string, 'UTF-8', mb_detect_encoding($MJTC_string));
        return $MJTC_string;
    }

    static function MJTC_safe_decoding($MJTC_string){
        if($MJTC_string == ''){
            return $MJTC_string;
        }
        $MJTC_string = base64_decode($MJTC_string);
        return $MJTC_string;
    }

    public static function MJTC_sql_string($MJTC_value) {
        if (!isset(majesticsupport::$_db) || !is_object(majesticsupport::$_db)) {
            return '';
        }
        $MJTC_prepared = majesticsupport::$_db->prepare('%s', sanitize_text_field($MJTC_value));
        if (MJTC_majesticsupportphplib::MJTC_strlen($MJTC_prepared) >= 2) {
            return MJTC_majesticsupportphplib::MJTC_substr($MJTC_prepared, 1, -1);
        }
        return '';
    }

    public static function MJTC_sql_like($MJTC_value) {
        if (!isset(majesticsupport::$_db) || !is_object(majesticsupport::$_db)) {
            return '';
        }
        $MJTC_prepared = majesticsupport::$_db->prepare('%s', '%' . majesticsupport::$_db->esc_like(sanitize_text_field($MJTC_value)) . '%');
        if (MJTC_majesticsupportphplib::MJTC_strlen($MJTC_prepared) >= 2) {
            return MJTC_majesticsupportphplib::MJTC_substr($MJTC_prepared, 1, -1);
        }
        return '';
    }


    public static function MJTC_strstr($MJTC_haystack, $MJTC_needle) {
        if($MJTC_haystack == '' || $MJTC_needle == ''){
            return false;
        }
        return strstr($MJTC_haystack, $MJTC_needle);
    }

    public static function MJTC_explode($MJTC_separator, $MJTC_haystack) {
        if($MJTC_separator == ''){
            return array();
        }
        if($MJTC_haystack == ''){
            return array();
        }
        return explode($MJTC_separator, $MJTC_haystack);
    }
    
    public static function MJTC_strip_tags($MJTC_string, $MJTC_allowable_tags = NULL) {
      if (!is_null($MJTC_string)) {
        return strip_tags($MJTC_string, $MJTC_allowable_tags);
      }
      return $MJTC_string;
    }


    public static function MJTC_htmlentities($MJTC_string) {
        if($MJTC_string == ''){
            return '';
        }
        return htmlentities($MJTC_string);
    }

    public static function MJTC_strtoupper($MJTC_string) {
        if($MJTC_string == ''){
            return '';
        }
        return strtoupper($MJTC_string);
    }

    public static function MJTC_basename($MJTC_string,$MJTC_suffix = '') {
        $MJTC_basename = '';
        if($MJTC_string !== ''){
           $MJTC_basename = basename($MJTC_string,$MJTC_suffix);
        }
        return $MJTC_basename;
    }

    public static function MJTC_dirname($MJTC_string,$MJTC_lvls = 1) {
        $MJTC_dirname = '';
        if($MJTC_string !== ''){
           $MJTC_dirname = dirname($MJTC_string,$MJTC_lvls);
        }
        return $MJTC_dirname;
    }


    public static function MJTC_substr($MJTC_str, $MJTC_start, $MJTC_length = null) {
        $MJTC_output = null;
        if ($MJTC_str !== null) {
            if ($MJTC_length !== null) {
                $MJTC_output = substr($MJTC_str, $MJTC_start, $MJTC_length);
            } else {
                $MJTC_output = substr($MJTC_str, $MJTC_start);
            }
        }
        return $MJTC_output;
    }


    public static function MJTC_ucwords($MJTC_str, $MJTC_delimiters = "") {
        $MJTC_output = null;
        if ($MJTC_str !== null) {
            $MJTC_output = ucwords($MJTC_str, $MJTC_delimiters);
        }
        return $MJTC_output;
    }

    // The use of function str_rot13() is forbidden

    public static function MJTC_preg_replace($MJTC_pattern, $MJTC_replacement, $MJTC_subject, $MJTC_limit = -1, &$MJTC_count = null){
        $MJTC_output = null;
        if ($MJTC_pattern !== null && $MJTC_replacement !== null && $MJTC_subject !== null) {
            $MJTC_output = preg_replace($MJTC_pattern, $MJTC_replacement, $MJTC_subject, $MJTC_limit, $MJTC_count);
        }
        return $MJTC_output;
    }

    public static function MJTC_strlen($MJTC_str){
        $MJTC_output = null;
        if ($MJTC_str !== null) {
            $MJTC_output = strlen($MJTC_str);
        }
        return $MJTC_output;
    }


    public static function MJTC_md5($MJTC_str, $MJTC_raw_output = false){
        $MJTC_output = null;
        if ($MJTC_str !== null) {
            $MJTC_output = md5($MJTC_str, $MJTC_raw_output);
        }
        return $MJTC_output;
    }

    public static function MJTC_preg_match($MJTC_pattern, $MJTC_subject, &$matches = null, $MJTC_flags = 0, $MJTC_offset = 0){
        $MJTC_output = null;
        if ($MJTC_pattern !== null && $MJTC_subject !== null) {
            $MJTC_output = preg_match($MJTC_pattern, $MJTC_subject, $matches, $MJTC_flags, $MJTC_offset);
        }
        return $MJTC_output;
    }

    public static function MJTC_strtolower($MJTC_str){
        $MJTC_output = null;
        if ($MJTC_str !== null) {
            $MJTC_output = strtolower($MJTC_str);
        }
        return $MJTC_output;
    }

    public static function MJTC_strpos($MJTC_haystack, $MJTC_needle, $MJTC_offset = 0){
        $MJTC_output = null;
        if ($MJTC_haystack !== null && $MJTC_needle !== null) {
            $MJTC_output = strpos($MJTC_haystack, $MJTC_needle, $MJTC_offset);
        }
        return $MJTC_output;
    }

    public static function MJTC_str_repeat($MJTC_input, $multiplier){
        $MJTC_output = null;
        if ($MJTC_input !== null && $multiplier !== null) {
            $MJTC_output = str_repeat($MJTC_input, $multiplier);
        }
        return $MJTC_output;
    }

    public static function MJTC_stripslashes($MJTC_str){
        $MJTC_output = null;
        if ($MJTC_str !== null) {
            $MJTC_output = stripslashes($MJTC_str);
        }
        return $MJTC_output;
    }

    public static function MJTC_htmlspecialchars($MJTC_string, $MJTC_flags = ENT_COMPAT | ENT_HTML401, $MJTC_encoding = 'UTF-8', $MJTC_double_encode = true){
        $MJTC_output = null;
        if ($MJTC_string !== null) {
            $MJTC_output = htmlspecialchars($MJTC_string, $MJTC_flags, $MJTC_encoding, $MJTC_double_encode);
        }
        return $MJTC_output;
    }

    public static function MJTC_setcookie($MJTC_name, $MJTC_value = "", $MJTC_expires = 0, $MJTC_path = "", $MJTC_domain = "", $MJTC_secure = false, $MJTC_httponly = false){
        $MJTC_output = null;
        if ($MJTC_name != null && $MJTC_domain !== null) {
            if (!headers_sent()) {
          	    $MJTC_output = setcookie($MJTC_name, $MJTC_value, $MJTC_expires, $MJTC_path, $MJTC_domain, $MJTC_secure, $MJTC_httponly);
            }
        }
        return $MJTC_output;
    }

    public static function MJTC_urlencode($MJTC_str){
        $MJTC_output = null;
        if ($MJTC_str !== null) {
            $MJTC_output = urlencode($MJTC_str);
        }
        return $MJTC_output;
    }

    public static function MJTC_crypt($MJTC_str, $MJTC_salt = null)
    {
        $MJTC_output = null;
        if ($MJTC_str !== null) {
            if ($MJTC_salt !== null) {
                $MJTC_output = crypt($MJTC_str, $MJTC_salt);
            } else {
                $MJTC_output = crypt($MJTC_str);
            }
        }
        return $MJTC_output;
    }

    public static function MJTC_urldecode($MJTC_str)
    {
        $MJTC_output = null;
        if ($MJTC_str !== null) {
            $MJTC_output = urldecode($MJTC_str);
        }
        return $MJTC_output;
    }

    public static function MJTC_trim($MJTC_str, $MJTC_charlist = ""){
        $MJTC_output = null;
        if ($MJTC_str !== null) {
            $MJTC_output = trim($MJTC_str, $MJTC_charlist);
        }
        return $MJTC_output;
    }

    public static function MJTC_rtrim($MJTC_str, $MJTC_chars = null){
        $MJTC_output = null;
        if ($MJTC_str !== null) {
            if ($MJTC_chars !== null) {
                $MJTC_output = rtrim($MJTC_str, $MJTC_chars);
            } else {
                $MJTC_output = rtrim($MJTC_str);
            }
        }
        return $MJTC_output;
    }

    public static function MJTC_addslashes($MJTC_str){
        $MJTC_output = null;
        if ($MJTC_str !== null) {
            $MJTC_output = addslashes($MJTC_str);
        }
        return $MJTC_output;
    }

    public static function MJTC_stristr($MJTC_haystack, $MJTC_needle, $MJTC_before_needle = false)
    {
        $MJTC_output = null;
        if ($MJTC_haystack !== null && $MJTC_needle !== null) {
            $MJTC_output = stristr($MJTC_haystack, $MJTC_needle, $MJTC_before_needle);
        }
        return $MJTC_output;
    }

    public static function MJTC_ucfirst($MJTC_str){
        $MJTC_output = null;
        if ($MJTC_str !== null) {
            $MJTC_output = ucfirst($MJTC_str);
        }
        return $MJTC_output;
    }

    public static function MJTC_parse_str($MJTC_str, &$MJTC_output){
        if ($MJTC_str !== null) {
            parse_str($MJTC_str, $MJTC_output);
        }
    }


    public static function MJTC_preg_split($MJTC_pattern, $MJTC_subject, $MJTC_limit = -1, $MJTC_flags = 0){
        $MJTC_output = null;
        if ($MJTC_pattern !== null && $MJTC_subject !== null) {
            $MJTC_output = preg_split($MJTC_pattern, $MJTC_subject, $MJTC_limit, $MJTC_flags);
        }
        return $MJTC_output;
    }

    public static function MJTC_number_format($MJTC_num,$MJTC_decimals = 0,$MJTC_decimal_separator = ".",$thousands_separator = ","){
        $MJTC_output = null;
        if ($MJTC_num !== null) {
            $MJTC_output = number_format($MJTC_num,$MJTC_decimals,$MJTC_decimal_separator,$thousands_separator);
        }
        return $MJTC_output;
    }

    public static function MJTC_strtotime($MJTC_datetime, $MJTC_baseTimestamp = null){
        $MJTC_output = null;
        if ($MJTC_datetime !== null) {
            $MJTC_output = strtotime($MJTC_datetime, $MJTC_baseTimestamp);
        }
        return $MJTC_output;
    }
    
    public static function MJTC_clean_file_path($MJTC_path){ // this function to remove relative path componenets from module and file name
        if($MJTC_path != ''){
            $MJTC_path = str_replace('./','',$MJTC_path);
            $MJTC_path = str_replace('..','',$MJTC_path);
        }
        return $MJTC_path;
    }


}
?>
