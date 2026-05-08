<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_wphdnotification {

    function __construct( ) {

    }

    public function MJTC_addSessionNotificationDataToTable($MJTC_message, $msgtype, $MJTC_sessiondatafor = 'notification',$MJTC_ticketid = null){
        if($MJTC_message == ''){
            if(!is_numeric($MJTC_message))
                return false;
        }
        global $wpdb;
        $MJTC_data = array();
        $MJTC_update = false;
        if(isset($_COOKIE['_wpms_session_']) && isset(majesticsupport::$_mjtcsession->sessionid)){
            if($MJTC_sessiondatafor == 'notification'){
                $MJTC_data = $this->MJTC_getNotificationDatabySessionId($MJTC_sessiondatafor);
                if(empty($MJTC_data)){
                    $MJTC_data['msg'][0] = $MJTC_message;
                    $MJTC_data['type'][0] = $msgtype;
                }else{
                    $MJTC_update = true;
                    $MJTC_count = count($MJTC_data['msg']);
                    $MJTC_data['msg'][$MJTC_count] = $MJTC_message;
                    $MJTC_data['type'][$MJTC_count] = $msgtype;
                }
            }elseif($MJTC_sessiondatafor == 'submitform'){
                $MJTC_data = $this->MJTC_getNotificationDatabySessionId($MJTC_sessiondatafor,true);
                $MJTC_data = $MJTC_message;
            }elseif($MJTC_sessiondatafor == 'ticket_time_start_'){
                $MJTC_data = $this->MJTC_getNotificationDatabySessionId($MJTC_sessiondatafor.$MJTC_ticketid);
                $MJTC_sessiondatafor = $MJTC_sessiondatafor.$MJTC_ticketid;
                if($MJTC_data != ""){
                    $MJTC_update = true;
                }
                $MJTC_data = $MJTC_message;
            }
            if($MJTC_sessiondatafor == 'majesticsupport_spamcheckid'){
                $MJTC_data = $this->MJTC_getNotificationDatabySessionId($MJTC_sessiondatafor);
                if($MJTC_data != ""){
                    $MJTC_update = true;
                    $MJTC_data = $MJTC_message;
                }else{
                    $MJTC_data = $MJTC_message;
                }
            }
            if($MJTC_sessiondatafor == 'majesticsupport_rot13'){
                $MJTC_data = $this->MJTC_getNotificationDatabySessionId($MJTC_sessiondatafor);
                if($MJTC_data != ""){
                    $MJTC_update = true;
                    $MJTC_data = $MJTC_message;
                }else{
                    $MJTC_data = $MJTC_message;
                }
            }
            if($MJTC_sessiondatafor == 'majesticsupport_spamcheckresult'){
                $MJTC_data = $this->MJTC_getNotificationDatabySessionId($MJTC_sessiondatafor);
                if($MJTC_data != ""){
                    $MJTC_update = true;
                    $MJTC_data = $MJTC_message;
                }else{
                    $MJTC_data = $MJTC_message;
                }
            }
            $MJTC_data = wp_json_encode($MJTC_data , true);
            $MJTC_sessionmsg = MJTC_majesticsupportphplib::MJTC_safe_encoding($MJTC_data);
            if(!$MJTC_update){
                $wpdb->insert( "{$wpdb->prefix}mjtc_support_mjtcsessiondata", array("usersessionid" => majesticsupport::$_mjtcsession->sessionid, "sessionmsg" => $MJTC_sessionmsg, "sessionexpire" => majesticsupport::$_mjtcsession->sessionexpire, "sessionfor" => $MJTC_sessiondatafor) );
            }else{
                $wpdb->update( "{$wpdb->prefix}mjtc_support_mjtcsessiondata", array("sessionmsg" => $MJTC_sessionmsg), array("usersessionid" => majesticsupport::$_mjtcsession->sessionid , 'sessionfor' => $MJTC_sessiondatafor) );
            }
        }
        return false;
    }

    public function MJTC_getNotificationDatabySessionId($MJTC_sessionfor , $MJTC_deldata = false){
        if(majesticsupport::$_mjtcsession->sessionid == '')
            return false;
        $MJTC_query = "SELECT sessionmsg FROM `" . majesticsupport::$_db->prefix . "mjtc_support_mjtcsessiondata` WHERE usersessionid = '" . esc_sql(majesticsupport::$_mjtcsession->sessionid) . "' AND sessionfor = '" . esc_sql($MJTC_sessionfor) . "' AND sessionexpire > '" . time() . "'";
        $MJTC_data = majesticsupport::$_db->get_var($MJTC_query);
        if(!empty($MJTC_data)){
            $MJTC_data = MJTC_majesticsupportphplib::MJTC_safe_decoding($MJTC_data);
            $MJTC_data = json_decode( $MJTC_data , true);
        }
        if($MJTC_deldata){
            majesticsupport::$_db->delete(majesticsupport::$_db->prefix . "mjtc_support_mjtcsessiondata", array( 'usersessionid' => majesticsupport::$_mjtcsession->sessionid , 'sessionfor' => $MJTC_sessionfor) );
        }
        return $MJTC_data;
    }

}

?>
