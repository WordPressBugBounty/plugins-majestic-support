<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_wphdnotification {

    function __construct( ) {

    }

    public function MJTC_addSessionNotificationDataToTable($message, $msgtype, $sessiondatafor = 'notification',$MJTC_ticketid = null){
        if($message == ''){
            if(!is_numeric($message))
                return false;
        }
        global $wpdb;
        $MJTC_data = array();
        $update = false;
        if(isset($_COOKIE['_wpms_session_']) && isset(majesticsupport::$_mjtcsession->sessionid)){
            if($sessiondatafor == 'notification'){
                $MJTC_data = $this->MJTC_getNotificationDatabySessionId($sessiondatafor);
                if(empty($MJTC_data)){
                    $MJTC_data['msg'][0] = $message;
                    $MJTC_data['type'][0] = $msgtype;
                }else{
                    $update = true;
                    $MJTC_count = count($MJTC_data['msg']);
                    $MJTC_data['msg'][$MJTC_count] = $message;
                    $MJTC_data['type'][$MJTC_count] = $msgtype;
                }
            }elseif($sessiondatafor == 'submitform'){
                $MJTC_data = $this->MJTC_getNotificationDatabySessionId($sessiondatafor,true);
                $MJTC_data = $message;
            }elseif($sessiondatafor == 'ticket_time_start_'){
                $MJTC_data = $this->MJTC_getNotificationDatabySessionId($sessiondatafor.$MJTC_ticketid);
                $sessiondatafor = $sessiondatafor.$MJTC_ticketid;
                if($MJTC_data != ""){
                    $update = true;
                }
                $MJTC_data = $message;
            }
            if($sessiondatafor == 'majesticsupport_spamcheckid'){
                $MJTC_data = $this->MJTC_getNotificationDatabySessionId($sessiondatafor);
                if($MJTC_data != ""){
                    $update = true;
                    $MJTC_data = $message;
                }else{
                    $MJTC_data = $message;
                }
            }
            if($sessiondatafor == 'majesticsupport_rot13'){
                $MJTC_data = $this->MJTC_getNotificationDatabySessionId($sessiondatafor);
                if($MJTC_data != ""){
                    $update = true;
                    $MJTC_data = $message;
                }else{
                    $MJTC_data = $message;
                }
            }
            if($sessiondatafor == 'majesticsupport_spamcheckresult'){
                $MJTC_data = $this->MJTC_getNotificationDatabySessionId($sessiondatafor);
                if($MJTC_data != ""){
                    $update = true;
                    $MJTC_data = $message;
                }else{
                    $MJTC_data = $message;
                }
            }
            $MJTC_data = wp_json_encode($MJTC_data , true);
            $sessionmsg = MJTC_majesticsupportphplib::MJTC_safe_encoding($MJTC_data);
            if(!$update){
                $wpdb->insert( "{$wpdb->prefix}mjtc_support_mjtcsessiondata", array("usersessionid" => majesticsupport::$_mjtcsession->sessionid, "sessionmsg" => $sessionmsg, "sessionexpire" => majesticsupport::$_mjtcsession->sessionexpire, "sessionfor" => $sessiondatafor) );
            }else{
                $wpdb->update( "{$wpdb->prefix}mjtc_support_mjtcsessiondata", array("sessionmsg" => $sessionmsg), array("usersessionid" => majesticsupport::$_mjtcsession->sessionid , 'sessionfor' => $sessiondatafor) );
            }
        }
        return false;
    }

    public function MJTC_getNotificationDatabySessionId($sessionfor , $deldata = false){
        if(majesticsupport::$_mjtcsession->sessionid == '')
            return false;
        $query = "SELECT sessionmsg FROM `" . majesticsupport::$_db->prefix . "mjtc_support_mjtcsessiondata` WHERE usersessionid = '" . esc_sql(majesticsupport::$_mjtcsession->sessionid) . "' AND sessionfor = '" . esc_sql($sessionfor) . "' AND sessionexpire > '" . time() . "'";
        $MJTC_data = majesticsupport::$_db->get_var($query);
        if(!empty($MJTC_data)){
            $MJTC_data = MJTC_majesticsupportphplib::MJTC_safe_decoding($MJTC_data);
            $MJTC_data = json_decode( $MJTC_data , true);
        }
        if($deldata){
            majesticsupport::$_db->delete(majesticsupport::$_db->prefix . "mjtc_support_mjtcsessiondata", array( 'usersessionid' => majesticsupport::$_mjtcsession->sessionid , 'sessionfor' => $sessionfor) );
        }
        return $MJTC_data;
    }

}

?>
