<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_captcha {

    function MJTC_getCaptchaForForm() {
        $MJTC_rand = $this->MJTC_randomNumber();
        MJTC_includer::MJTC_getObjectClass('wphdnotification')->MJTC_addSessionNotificationDataToTable($MJTC_rand,'','majesticsupport_spamcheckid');
        $majesticsupport_rot13 = wp_rand(0, 1);
        MJTC_includer::MJTC_getObjectClass('wphdnotification')->MJTC_addSessionNotificationDataToTable($majesticsupport_rot13,'','majesticsupport_rot13');

        $MJTC_operator = 2;
        if ($MJTC_operator == 2) {
            $tcalc = majesticsupport::$_config['owncaptcha_calculationtype'];
        }
        $MJTC_max_value = 20;
        $MJTC_negativ = 1;
        $MJTC_operend_1 = wp_rand($MJTC_negativ, $MJTC_max_value);
        $MJTC_operend_2 = wp_rand($MJTC_negativ, $MJTC_max_value);
        $MJTC_operand = majesticsupport::$_config['owncaptcha_totaloperand'];
        if ($MJTC_operand == 3) {
            $MJTC_operend_3 = wp_rand($MJTC_negativ, $MJTC_max_value);
        }

        if (majesticsupport::$_config['owncaptcha_calculationtype'] == 2) { // Subtraction
            if (majesticsupport::$_config['owncaptcha_subtractionans'] == 1) {
                $MJTC_ans = $MJTC_operend_1 - $MJTC_operend_2;
                if ($MJTC_ans < 0) {
                    $MJTC_one = $MJTC_operend_2;
                    $MJTC_operend_2 = $MJTC_operend_1;
                    $MJTC_operend_1 = $MJTC_one;
                }
                if ($MJTC_operand == 3) {
                    $MJTC_ans = $MJTC_operend_1 - $MJTC_operend_2 - $MJTC_operend_3;
                    if ($MJTC_ans < 0) {
                        if ($MJTC_operend_1 < $MJTC_operend_2) {
                            $MJTC_one = $MJTC_operend_2;
                            $MJTC_operend_2 = $MJTC_operend_1;
                            $MJTC_operend_1 = $MJTC_one;
                        }
                        if ($MJTC_operend_1 < $MJTC_operend_3) {
                            $MJTC_one = $MJTC_operend_3;
                            $MJTC_operend_3 = $MJTC_operend_1;
                            $MJTC_operend_1 = $MJTC_one;
                        }
                    }
                }
            }
        }

        if ($tcalc == 0)
            $tcalc = wp_rand(1, 2);

        if ($tcalc == 1) { // Addition
            if ($majesticsupport_rot13 == 1) { // ROT13 coding
                if ($MJTC_operand == 2) {
                    // The use of function str_rot13() is forbidden
                    MJTC_includer::MJTC_getObjectClass('wphdnotification')->MJTC_addSessionNotificationDataToTable(MJTC_majesticsupportphplib::MJTC_safe_encoding($MJTC_operend_1 + $MJTC_operend_2),'','majesticsupport_spamcheckresult');
                } elseif ($MJTC_operand == 3) {
                    // The use of function str_rot13() is forbidden
                    MJTC_includer::MJTC_getObjectClass('wphdnotification')->MJTC_addSessionNotificationDataToTable(MJTC_majesticsupportphplib::MJTC_safe_encoding($MJTC_operend_1 + $MJTC_operend_2 + $MJTC_operend_3),'','majesticsupport_spamcheckresult');
                }
            } else {
                if ($MJTC_operand == 2) {
                    MJTC_includer::MJTC_getObjectClass('wphdnotification')->MJTC_addSessionNotificationDataToTable(MJTC_majesticsupportphplib::MJTC_safe_encoding($MJTC_operend_1 + $MJTC_operend_2),'','majesticsupport_spamcheckresult');
                } elseif ($MJTC_operand == 3) {
                    MJTC_includer::MJTC_getObjectClass('wphdnotification')->MJTC_addSessionNotificationDataToTable(MJTC_majesticsupportphplib::MJTC_safe_encoding($MJTC_operend_1 + $MJTC_operend_2 + $MJTC_operend_3),'','majesticsupport_spamcheckresult');
                }
            }
        } elseif ($tcalc == 2) { // Subtraction
            if ($majesticsupport_rot13 == 1) {
                if ($MJTC_operand == 2) {
                    // The use of function str_rot13() is forbidden
                    MJTC_includer::MJTC_getObjectClass('wphdnotification')->MJTC_addSessionNotificationDataToTable(MJTC_majesticsupportphplib::MJTC_safe_encoding($MJTC_operend_1 - $MJTC_operend_2),'','majesticsupport_spamcheckresult');
                } elseif ($MJTC_operand == 3) {
                    // The use of function str_rot13() is forbidden
                    MJTC_includer::MJTC_getObjectClass('wphdnotification')->MJTC_addSessionNotificationDataToTable(MJTC_majesticsupportphplib::MJTC_safe_encoding($MJTC_operend_1 - $MJTC_operend_2 - $MJTC_operend_3),'','majesticsupport_spamcheckresult');
                }
            } else {
                if ($MJTC_operand == 2) {
                    MJTC_includer::MJTC_getObjectClass('wphdnotification')->MJTC_addSessionNotificationDataToTable(MJTC_majesticsupportphplib::MJTC_safe_encoding($MJTC_operend_1 - $MJTC_operend_2),'','majesticsupport_spamcheckresult');
                } elseif ($MJTC_operand == 3) {
                    MJTC_includer::MJTC_getObjectClass('wphdnotification')->MJTC_addSessionNotificationDataToTable(MJTC_majesticsupportphplib::MJTC_safe_encoding($MJTC_operend_1 - $MJTC_operend_2 - $MJTC_operend_3),'','majesticsupport_spamcheckresult');
                }
            }
        }
        $MJTC_add_string = "";
        $MJTC_add_string .= '<div><label for="' . esc_attr($MJTC_rand) . '">';

        if ($tcalc == 1) {
            if ($MJTC_operand == 2) {
                $MJTC_add_string .= $MJTC_operend_1 . ' ' . esc_html(__('Plus', 'majestic-support')) . ' ' . $MJTC_operend_2 . ' ' . esc_html(__('Equals', 'majestic-support')) . ' ';
            } elseif ($MJTC_operand == 3) {
                $MJTC_add_string .= $MJTC_operend_1 . ' ' . esc_html(__('Plus', 'majestic-support')) . ' ' . $MJTC_operend_2 . ' ' . esc_html(__('Plus', 'majestic-support')) . ' ' . $MJTC_operend_3 . ' ' . esc_html(__('Equals', 'majestic-support')) . ' ';
            }
        } elseif ($tcalc == 2) {
            $MJTC_converttostring = 0;
            if ($MJTC_operand == 2) {
                $MJTC_add_string .= $MJTC_operend_1 . ' ' . esc_html(__('Minus', 'majestic-support')) . ' ' . $MJTC_operend_2 . ' ' . esc_html(__('Equals', 'majestic-support')) . ' ';
            } elseif ($MJTC_operand == 3) {
                $MJTC_add_string .= $MJTC_operend_1 . ' ' . esc_html(__('Minus', 'majestic-support')) . ' ' . $MJTC_operend_2 . ' ' . esc_html(__('Minus', 'majestic-support')) . ' ' . $MJTC_operend_3 . ' ' . esc_html(__('Equals', 'majestic-support')) . ' ';
            }
        }

        $MJTC_add_string .= '</label>';
        $MJTC_add_string .= '<input type="text" name="' . esc_attr($MJTC_rand) . '" id="' . esc_attr($MJTC_rand) . '" size="3" class="inputbox mjtc-support-recaptcha ' . esc_attr($MJTC_rand) . '" value="" data-validation="required" />';
        $MJTC_add_string .= '</div>';

        return $MJTC_add_string;
    }

    function MJTC_randomNumber() {
        $MJTC_pw = '';
        // first character has to be a letter
        $MJTC_characters = range('a', 'z');
        $MJTC_pw .= $MJTC_characters[wp_rand(0, 25)];

        // other characters arbitrarily
        $MJTC_numbers = range(0, 9);
        $MJTC_characters = array_merge($MJTC_characters, $MJTC_numbers);

        $MJTC_pw_length = wp_rand(4, 12);

        for ($MJTC_i = 0; $MJTC_i < $MJTC_pw_length; $MJTC_i++) {
            $MJTC_pw .= $MJTC_characters[wp_rand(0, 35)];
        }
        return $MJTC_pw;
    }

    private function MJTC_performChecks() {
        $majesticsupport_rot13 = MJTC_includer::MJTC_getObjectClass('wphdnotification')->MJTC_getNotificationDatabySessionId('majesticsupport_rot13',true);
        if($majesticsupport_rot13 == 1){
            // The use of function str_rot13() is forbidden
            $MJTC_spamcheckresult = MJTC_majesticsupportphplib::MJTC_safe_decoding(MJTC_includer::MJTC_getObjectClass('wphdnotification')->MJTC_getNotificationDatabySessionId('majesticsupport_spamcheckresult',true));
        } else {
            $majesticsupport_spamcheckresult = MJTC_includer::MJTC_getObjectClass('wphdnotification')->MJTC_getNotificationDatabySessionId('majesticsupport_spamcheckresult',true);
            if ($majesticsupport_spamcheckresult != '') {
                $MJTC_spamcheckresult = MJTC_majesticsupportphplib::MJTC_safe_decoding($majesticsupport_spamcheckresult);
            } else {
                $MJTC_spamcheckresult = '';
            }
        }
        $MJTC_spamcheck = MJTC_includer::MJTC_getObjectClass('wphdnotification')->MJTC_getNotificationDatabySessionId('majesticsupport_spamcheckid',true);
        $MJTC_spamcheck = MJTC_request::MJTC_getVar($MJTC_spamcheck, '', 'post');
        if (!is_numeric($MJTC_spamcheckresult) || $MJTC_spamcheckresult != $MJTC_spamcheck) {
            return false; // Failed
        }
        return true;
    }

    function MJTC_checkCaptchaUserForm() {
        if (!$this->MJTC_performChecks())
            $MJTC_return = 2;
        else
            $MJTC_return = 1;
        return $MJTC_return;
    }

}

?>
