<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_wphdsession {

    public $sessionid;
    public $sessionexpire;
    private $MJTC_sessiondata;
    private $MJTC_datafor;
    private $MJTC_nextsessionexpire;

    function __construct( ) {
        $this->init();

        if(in_array('sociallogin', majesticsupport::$_active_addons)){
            add_action( 'parse_request', array($this , 'MJTC_custom_session_handling') );

        }
    }

    function MJTC_getSessionId(){
        return $this->sessionid;
    }

    function init(){
        if (isset($_COOKIE['_wpms_session_'])) {
            $MJTC_cookie = majesticsupport::MJTC_sanitizeData(MJTC_majesticsupportphplib::MJTC_stripslashes($_COOKIE['_wpms_session_']));// MJTC_sanitizeData() function uses wordpress santize functions
            $MJTC_user_cookie = MJTC_majesticsupportphplib::MJTC_explode('/', $MJTC_cookie);
            $this->sessionid = MJTC_majesticsupportphplib::MJTC_preg_replace("/[^A-Za-z0-9_]/", '', $MJTC_user_cookie[0]);
            $this->sessionexpire = absint($MJTC_user_cookie[1]);
            $this->MJTC_nextsessionexpire = absint($MJTC_user_cookie[2]);
            // Update options session expiration
            if (time() > $this->MJTC_nextsessionexpire) {
                $this->MJTC_set_cookies_expiration();
            }
        } else {
            $MJTC_sessionid = $this->MJTC_generate_id();
            $this->sessionid = $MJTC_sessionid . get_option( '_wpms_session_', 0 );
            $this->MJTC_set_cookies_expiration();
        }
        $this->MJTC_set_user_cookies();
        return $this->sessionid;
    }

    private function MJTC_set_cookies_expiration(){
        $this->sessionexpire = time() + (int)(30*60);
        $this->MJTC_nextsessionexpire = time() + (int)(60*60);
    }

    private function MJTC_generate_id(){
        do_action('majesticsupport_load_phpass');
        $MJTC_hash = new PasswordHash( 16, false );

        return MJTC_majesticsupportphplib::MJTC_md5( $MJTC_hash->get_random_bytes( 32 ) );
    }

    private function MJTC_set_user_cookies(){
        MJTC_majesticsupportphplib::MJTC_setcookie( '_wpms_session_', $this->sessionid . '/' . $this->sessionexpire . '/' . $this->MJTC_nextsessionexpire , $this->sessionexpire, COOKIEPATH, COOKIE_DOMAIN);
        $MJTC_count = get_option( '_wpms_session_', 0 );
        update_option( '_wpms_session_', ++$MJTC_count);
    }

    public function MJTC_custom_session_handling(){
        if(function_exists('session_start')){
            if(session_status() == PHP_SESSION_NONE){
                session_start();
            }
        }
    }

}

?>
