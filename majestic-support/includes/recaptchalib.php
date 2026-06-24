<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

    if(! function_exists('MJTC_googleRecaptchaHTTPPost')){
        function MJTC_googleRecaptchaHTTPPost($MJTC_sharedkey , $MJTC_grresponse) {
            $MJTC_google_url = "https://www.google.com/recaptcha/api/siteverify";
            $MJTC_secret = $MJTC_sharedkey;
            $MJTC_ip = MJTC_majesticsupportphplib::MJTC_htmlspecialchars($_SERVER['REMOTE_ADDR']);
            $MJTC_post_data = array();
            $MJTC_post_data['secret'] = $MJTC_secret;
            $MJTC_post_data['response'] = $MJTC_grresponse;
            $MJTC_post_data['remoteip'] = $MJTC_ip;

            $MJTC_response = wp_remote_post( $MJTC_google_url, array('body' => $MJTC_post_data,'timeout'=>7,'sslverify'=>true));
            if( !is_wp_error($MJTC_response) && $MJTC_response['response']['code'] == 200 && isset($MJTC_response['body']) ){
                $MJTC_result = $MJTC_response['body'];
            }else{
                $MJTC_result = false;
                if(!is_wp_error($MJTC_response)){
                   $MJTC_error = $MJTC_response['response']['message'];
               }else{
                    $MJTC_error = $MJTC_response->get_error_message();
               }
            }
            if($MJTC_result){
                $MJTC_res= json_decode($MJTC_result, true);
            }else{
                return FALSE;
            }
            //reCaptcha success check
            if($MJTC_res['success']) {
                return TRUE;
            } else {
                return FALSE;
            }
        }
    }
?>
