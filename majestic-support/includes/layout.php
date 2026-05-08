<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_layout {

    static function MJTC_getNoRecordFound() {
        $MJTC_html = '
				<div class="mjtc-support-error-message-wrapper">
					<div class="mjtc-support-message-image-wrapper">
						<img class="mjtc-support-message-image" alt="message image" src="' . esc_url(MJTC_PLUGIN_URL) . 'includes/images/error/no-record-found.png"/>
					</div>
					<div class="mjtc-support-messages-data-wrapper">
						<span class="mjtc-support-messages-main-text">
					    	' . esc_html(__('Sorry', 'majestic-support')) . '!
						</span>
						<span class="mjtc-support-messages-block_text">
					    	' . esc_html(__('There was no record found', 'majestic-support')) . '...
						</span>
					</div>
				</div>
		';
        echo wp_kses($MJTC_html, MJTC_ALLOWED_TAGS);
    }
    static function MJTC_getNoRecordFoundForAjax() {
        $MJTC_html = '
				<div class="mjtc-support-error-message-wrapper">
					<div class="mjtc-support-message-image-wrapper">
						<img class="mjtc-support-message-image" alt="message image" src="' . esc_url(MJTC_PLUGIN_URL) . 'includes/images/error/no-record-icon.png"/>
					</div>
					<div class="mjtc-support-messages-data-wrapper">
						<span class="mjtc-support-messages-main-text">
					    	' . esc_html(__('Sorry!', 'majestic-support')) . '
						</span>
						<span class="mjtc-support-messages-block_text">
					    	' . esc_html(__('There was no record found', 'majestic-support')) . '
						</span>
					</div>
				</div>
		';
        return wp_kses($MJTC_html, MJTC_ALLOWED_TAGS);
    }

    static function MJTC_getPermissionNotGranted() {
    	$MJTC_loginval = MJTC_includer::MJTC_getModel('configuration')->getConfigValue('set_login_link');
        $MJTC_loginlink = MJTC_includer::MJTC_getModel('configuration')->getConfigValue('login_link');
        $MJTC_registerval = MJTC_includer::MJTC_getModel('configuration')->getConfigValue('set_register_link');
        $MJTC_registerlink = MJTC_includer::MJTC_getModel('configuration')->getConfigValue('register_link');
        include_once(MJTC_PLUGIN_PATH . 'includes/header.php');
        $MJTC_html = '
				<div class="mjtc-support-error-message-wrapper">
					<div class="mjtc-support-message-image-wrapper">
						<img class="mjtc-support-message-image" alt="message image" src="' . esc_url(MJTC_PLUGIN_URL) . 'includes/images/error/not-permission-icon.png"/>
					</div>
					<div class="mjtc-support-messages-data-wrapper">
						<span class="mjtc-support-messages-main-text">
					    	' . esc_html(__('Access Denied', 'majestic-support')) . '
						</span>
						<span class="mjtc-support-messages-block_text">
					    	' . esc_html(__('You have no permission to access this page', 'majestic-support')) . '
						</span>
						<span class="mjtc-support-user-login-btn-wrp">';
							if (MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid() == 0) {
								if ($MJTC_loginval == 3){
                                    $MJTC_hreflink = wp_login_url();
                                }
		                        else if($MJTC_loginval == 2 && $MJTC_loginlink != ""){
		                            $MJTC_html .= '<a class="mjtc-support-login-btn" href="'.esc_url($MJTC_loginlink).'" title="Login">' . esc_html(__('Login', 'majestic-support')) . '</a>';
		                        }else{
		                            $MJTC_html .= '<a class="mjtc-support-login-btn" href="'.esc_url(majesticsupport::makeUrl(array('mjsmod'=>'majesticsupport', 'mjslay'=>'login'))).'" title="Login">' . esc_html(__('Login', 'majestic-support')) . '</a>';
		                        }
		                        $MJTC_is_enable = get_option('users_can_register');/*check to make sure user registration is enabled*/
	                            if ($MJTC_is_enable) {
	                            	if($MJTC_registerval == 3){
		                        	    $MJTC_html .= '<a class="mjtc-support-register-btn" href="'.esc_url(wp_registration_url()).'" title="' . esc_html(__('Register', 'majestic-support')) . '">' . esc_html(__('Register', 'majestic-support')) . '</a>';
		                        	}else if($MJTC_registerval == 2 && $MJTC_registerlink != ""){
		                        	    $MJTC_html .= '<a class="mjtc-support-register-btn" href="'.esc_url($MJTC_registerlink).'" title="' . esc_html(__('Register', 'majestic-support')) . '">' . esc_html(__('Register', 'majestic-support')) . '</a>';
		                        	}else{
		                        		$MJTC_html .= '<a class="mjtc-support-register-btn" href="'.esc_url(majesticsupport::makeUrl(array('mjsmod'=>'majesticsupport', 'mjslay'=>'userregister'))).'" title="' . esc_html(__('Register', 'majestic-support')) . '">' . esc_html(__('Register', 'majestic-support')) . '</a>';
		                        	}
		                        }
	                    	}

                    $MJTC_html .= '</span>
					</div>
				</div>
		';
        echo wp_kses($MJTC_html, MJTC_ALLOWED_TAGS);
    }

    static function MJTC_getNotStaffMember() {
        $MJTC_html = '
				<div class="mjtc-support-error-message-wrapper">
					<div class="mjtc-support-message-image-wrapper">
						<img class="mjtc-support-message-image" alt="message image" src="' . esc_url(MJTC_PLUGIN_URL) . 'includes/images/error/not-permission-icon.png"/>
					</div>
					<div class="mjtc-support-messages-data-wrapper">
						<span class="mjtc-support-messages-main-text">
					    	' . esc_html(__('Access Denied', 'majestic-support')) . '
						</span>
						<span class="mjtc-support-messages-block_text">
					    	' . esc_html(__('User is not allowed to access this page.', 'majestic-support')) . '
						</span>
					</div>
				</div>
		';
        echo wp_kses($MJTC_html, MJTC_ALLOWED_TAGS);
    }

    static function MJTC_getYouAreLoggedIn() {
        $MJTC_html = '
				<div class="mjtc-support-error-message-wrapper">
					<div class="mjtc-support-message-image-wrapper">
						<img class="mjtc-support-message-image" alt="message image" src="' . esc_url(MJTC_PLUGIN_URL) . 'includes/images/error/already-loggedin.png"/>
					</div>
					<div class="mjtc-support-messages-data-wrapper">
						<span class="mjtc-support-messages-main-text">
					    	' . esc_html(__('Sorry!', 'majestic-support')) . '
						</span>
						<span class="mjtc-support-messages-block_text">
					    	' . esc_html(__('You are already Logged In.', 'majestic-support')) . '
						</span>
					</div>
				</div>
		';
        echo wp_kses($MJTC_html, MJTC_ALLOWED_TAGS);
    }

    static function MJTC_getStaffMemberDisable() {
        $MJTC_html = '
				<div class="mjtc-support-error-message-wrapper">
					<div class="mjtc-support-message-image-wrapper">
						<img class="mjtc-support-message-image" alt="message image" src="' . esc_url(MJTC_PLUGIN_URL) . 'includes/images/error/not-permission-icon.png"/>
					</div>
					<div class="mjtc-support-messages-data-wrapper">
						<span class="mjtc-support-messages-main-text">
					    	' . esc_html(__('Access Denied', 'majestic-support')) . '
						</span>
						<span class="mjtc-support-messages-block_text">
					    	' . esc_html(__('Your account has been disabled, please contact the administrator.', 'majestic-support')) . '
						</span>
					</div>
				</div>
		';
        echo wp_kses($MJTC_html, MJTC_ALLOWED_TAGS);
    }

    static function MJTC_getSystemOffline() {
        $MJTC_html = '
				<div class="mjtc-support-error-message-wrapper">
					<div class="mjtc-support-message-image-wrapper">
						<img class="mjtc-support-message-image" alt="message image" src="' . esc_url(MJTC_PLUGIN_URL) . 'includes/images/error/offline.png"/>
					</div>
					<div class="mjtc-support-messages-data-wrapper">
						<span class="mjtc-support-messages-main-text">
					    	' . esc_html(__('Offline', 'majestic-support')) . '
						</span>
						<span class="mjtc-support-messages-block_text">
					    	' . wp_kses_post(majesticsupport::$_config['offline_message'], MJTC_ALLOWED_TAGS) . '
						</span>
					</div>
				</div>
		';
        echo wp_kses($MJTC_html, MJTC_ALLOWED_TAGS);
    }

    static function MJTC_getUserGuest($MJTC_redirect_url = '') {
        $MJTC_loginval = MJTC_includer::MJTC_getModel('configuration')->getConfigValue('set_login_link');
        $MJTC_loginlink = MJTC_includer::MJTC_getModel('configuration')->getConfigValue('login_link');
        $MJTC_registerval = MJTC_includer::MJTC_getModel('configuration')->getConfigValue('set_register_link');
        $MJTC_registerlink = MJTC_includer::MJTC_getModel('configuration')->getConfigValue('register_link');
        $MJTC_html = '
                <div class="mjtc-support-error-message-wrapper">
					<div class="mjtc-support-message-image-wrapper">
						<img class="mjtc-support-message-image" alt="message image" src="' . esc_url(MJTC_PLUGIN_URL) . 'includes/images/error/not-login-icon.png"/>
					</div>
					<div class="mjtc-support-messages-data-wrapper">
						<span class="mjtc-support-messages-main-text">
					    	' . esc_html(__('You are not logged in', 'majestic-support')) . '
						</span>
						<span class="mjtc-support-messages-block_text">
					    	' . esc_html(__('To access the page, please login', 'majestic-support')) . '
						</span>
						<span class="mjtc-support-user-login-btn-wrp">';
							if ($MJTC_loginval == 3){
                                $MJTC_hreflink = wp_login_url();
                            }
	                        else if($MJTC_loginval == 2 && $MJTC_loginlink != ""){
	                            $MJTC_html .= '<a class="mjtc-support-login-btn" href="'.esc_url($MJTC_loginlink).'" title="Login">' . esc_html(__('Login', 'majestic-support')) . '</a>';
	                        }else{
	                            $MJTC_html .= '<a class="mjtc-support-login-btn" href="'.esc_url(majesticsupport::makeUrl(array('mjsmod'=>'majesticsupport', 'mjslay'=>'login', 'mjtc_redirecturl'=>$MJTC_redirect_url))).'" title="Login">' . esc_html(__('Login', 'majestic-support')) . '</a>';
	                        }
	                        $MJTC_is_enable = get_option('users_can_register');/*check to make sure user registration is enabled*/
                            if ($MJTC_is_enable) {
                            	if($MJTC_registerval == 3){
	                        	    $MJTC_html .= '<a class="mjtc-support-register-btn" href="'.esc_url(wp_registration_url()).'" title="' . esc_html(__('Register', 'majestic-support')) . '">' . esc_html(__('Register', 'majestic-support')) . '</a>';
	                        	}else if($MJTC_registerval == 2 && $MJTC_registerlink != ""){
	                        	    $MJTC_html .= '<a class="mjtc-support-register-btn" href="'.esc_url($MJTC_registerlink).'" title="' . esc_html(__('Register', 'majestic-support')) . '">' . esc_html(__('Register', 'majestic-support')) . '</a>';
	                        	}else{
	                        		$MJTC_html .= '<a class="mjtc-support-register-btn" href="'.esc_url(majesticsupport::makeUrl(array('mjsmod'=>'majesticsupport', 'mjslay'=>'userregister', 'mjtc_redirecturl'=>$MJTC_redirect_url))).'" title="' . esc_html(__('Register', 'majestic-support')) . '">' . esc_html(__('Register', 'majestic-support')) . '</a>';
	                        	}
	                        }

                    $MJTC_html .= '</span>
                    </div>

				</div>
        ';
        echo wp_kses($MJTC_html, MJTC_ALLOWED_TAGS);
    }

    static function MJTC_getYouAreNotAllowedToViewThisPage() {
        $MJTC_html = '
				<div class="mjtc-support-error-message-wrapper">
					<div class="mjtc-support-message-image-wrapper">
						<img class="mjtc-support-message-image" alt="message image" src="' . esc_url(MJTC_PLUGIN_URL) . 'includes/images/error/not-permission-icon.png"/>
					</div>
					<div class="mjtc-support-messages-data-wrapper">
						<span class="mjtc-support-messages-main-text">
					    	' . esc_html(__('Sorry!', 'majestic-support')) . '
						</span>
						<span class="mjtc-support-messages-block_text">
					    	' . esc_html(__('User is not allowed to view this Ticket', 'majestic-support')) . '
						</span>
					</div>
				</div>
		';
        echo wp_kses($MJTC_html, MJTC_ALLOWED_TAGS);
    }

    static function MJTC_getRegistrationDisabled() {
        $MJTC_html = '
				<div class="mjtc-support-error-message-wrapper">
					<div class="mjtc-support-message-image-wrapper">
						<img class="mjtc-support-message-image" alt="message image" src="' . esc_url(MJTC_PLUGIN_URL) . 'includes/images/error/ban.png"/>
					</div>
					<div class="mjtc-support-messages-data-wrapper">
						<span class="mjtc-support-messages-main-text">
					    	' . esc_html(__('Sorry!', 'majestic-support')) . '
						</span>
						<span class="mjtc-support-messages-block_text">
					    	' . esc_html(__('Registration has been disabled by admin, please contact the system administrator.', 'majestic-support')) . '
						</span>
					</div>
				</div>
		';
        echo wp_kses($MJTC_html, MJTC_ALLOWED_TAGS);
    }

    static function MJTC_getFeedbackMessages($msg_type) {
    	if($msg_type == 2){
    		$MJTC_img_var = '3.png';
    		$MJTC_text_var_1 = esc_html(__('Sorry!', 'majestic-support'));
    		$MJTC_text_var_2 = esc_html(__('You have already given the feedback for this ticket.', 'majestic-support'));
    	}elseif($msg_type == 3){
    		$MJTC_img_var = 'no-record-icon.png';
    		$MJTC_text_var_1 = esc_html(__('Sorry!', 'majestic-support'));
    		$MJTC_text_var_2 = esc_html(__('Ticket not found...!', 'majestic-support'));
    	}else{
    		$MJTC_img_var = 'not-permission-icon.png';
    		$MJTC_text_var_1 = esc_html(__('Sorry!', 'majestic-support'));
    		$MJTC_text_var_2 = esc_html(__('User is not allowed to view this page', 'majestic-support'));
    	}
    	if($msg_type == 4){
			$MJTC_html = '
					<div class="mjtc-support-error-message-wrapper">
						<div class="mjtc-support-message-image-wrapper">
							<img class="mjtc-support-message-image" alt="message image" src="' . esc_url(MJTC_PLUGIN_URL) . 'includes/images/error/success.png"/>
						</div>
						<div class="mjtc-support-messages-data-wrapper">
							<span class="mjtc-support-messages-main-text">
						    	'. esc_html(__('Thank you so much for your feedback', 'majestic-support')) .'
							</span>
							<span class="mjtc-support-messages-block_text">
						    	'. wp_kses(majesticsupport::$_config['feedback_thanks_message'], MJTC_ALLOWED_TAGS) .'
							</span>
						</div>
					</div>';
    	}else{
	        $MJTC_html = '
					<div class="mjtc-support-error-message-wrapper">
					<div class="mjtc-support-message-image-wrapper">
						<img class="mjtc-support-message-image" alt="message image" src="' . esc_url(MJTC_PLUGIN_URL) . 'includes/images/error/'.esc_attr($MJTC_img_var).'"/>
					</div>
					<div class="mjtc-support-messages-data-wrapper">
						<span class="mjtc-support-messages-main-text">
					    	' . esc_html($MJTC_text_var_1) . '
						</span>
						<span class="mjtc-support-messages-block_text">
					    	' .wp_kses($MJTC_text_var_2, MJTC_ALLOWED_TAGS). '
						</span>
					</div>
				</div>
			';
		}
        echo wp_kses($MJTC_html, MJTC_ALLOWED_TAGS);
	}

}

?>
