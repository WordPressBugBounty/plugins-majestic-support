<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_encoder {

    private $MJTC_securekey, $MJTC_iv;

    function __construct($MJTC_textkey = '') {
    }

    function MJTC_encrypt($MJTC_input) {
        return MJTC_majesticsupportphplib::MJTC_safe_encoding($MJTC_input);
    }

    function MJTC_decrypt($MJTC_input) {
        return MJTC_majesticsupportphplib::MJTC_safe_decoding($MJTC_input);
    }

}

?>
