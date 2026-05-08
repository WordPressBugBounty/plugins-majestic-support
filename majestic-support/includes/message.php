<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_message {
    /*
     * Set Message
     * @params $MJTC_message = Your message to display
     * @params $type = Messages types => 'updated','error','update-nag'
     */
    public static $ms_response_msg = array();

    static function MJTC_setMessage($MJTC_message, $type) {
        MJTC_includer::MJTC_getObjectClass('wphdnotification')->MJTC_addSessionNotificationDataToTable($MJTC_message,$type,'notification');
    }

    static function MJTC_getMessage01() {
        $MJTC_frontend = (is_admin()) ? '' : 'frontend';
        $MJTC_divHtml = '';
        $MJTC_option = get_option('majesticsupport', array());
        $MJTC_notificationdata = MJTC_includer::MJTC_getObjectClass('wphdnotification')->MJTC_getNotificationDatabySessionId('notification',true);
        if (isset($MJTC_notificationdata) && !empty($MJTC_notificationdata)) {
            $MJTC_data = $MJTC_notificationdata;
            for ($MJTC_i = 0; $MJTC_i < COUNT($MJTC_data['msg']); $MJTC_i++){
                $MJTC_divHtml .= '<div class=" ' . esc_attr($MJTC_frontend) . ' ' . esc_attr($MJTC_data['type'][$MJTC_i]) . '"><p>' . wp_kses($MJTC_data['msg'][$MJTC_i], MJTC_ALLOWED_TAGS) . '</p></div>';
            }
        }
        echo wp_kses($MJTC_divHtml, MJTC_ALLOWED_TAGS);
    }

    static function MJTC_getMessage() {
        $MJTC_frontend = (is_admin()) ? '' : 'frontend';
        $MJTC_divHtml = '';
        $MJTC_option = get_option('majesticsupport', array());
        $MJTC_notificationdata = MJTC_includer::MJTC_getObjectClass('wphdnotification')->MJTC_getNotificationDatabySessionId('notification', true);

        if (isset($MJTC_notificationdata) && !empty($MJTC_notificationdata)) {
            $MJTC_data = $MJTC_notificationdata;

            for ($MJTC_i = 0; $MJTC_i < COUNT($MJTC_data['msg']); $MJTC_i++) {
                $MJTC_type = esc_attr($MJTC_data['type'][$MJTC_i]); // 'success' or 'error'
                $MJTC_msg  = wp_kses($MJTC_data['msg'][$MJTC_i], MJTC_ALLOWED_TAGS);
                
                // Set Dynamic Icon and Title based on type
                if ($MJTC_type === 'updated') {
                    $MJTC_title = esc_html__('Success!', 'majestic-support');
                    $MJTC_icon  = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg>';
                } else {
                    $MJTC_type  = 'error'; // Ensure class is 'error' for styling
                    $MJTC_title = esc_html__('Error', 'majestic-support');
                    $MJTC_icon  = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="m15 9-6 6"></path><path d="m9 9 6 6"></path></svg>';
                }

                $MJTC_divHtml .= '
                <div class="mjtc-toast-wrapper ' . esc_attr($MJTC_frontend) . '">
                    <div class="mjtc-toast-msg ' . $MJTC_type . ' show" id="demo-success-toast">
                        <div class="mjtc-toast-icon-wrp">
                            ' . $MJTC_icon . '
                        </div>
                        <div class="mjtc-toast-content">
                            <span class="mjtc-toast-title">' . $MJTC_title . '</span>
                            <span class="mjtc-toast-desc">' . $MJTC_msg . '</span>
                        </div>
                        <button class="mjtc-toast-close" title="' . esc_attr__('Close', 'majestic-support') . '">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"></path><path d="m6 6 12 12"></path></svg>
                        </button>
                    </div>
                </div>';
            }
        }
        // We use a more permissive kses or just echo if the components are already escaped above
        echo wp_kses($MJTC_divHtml, MJTC_ALLOWED_TAGS); 
    }

}

?>
