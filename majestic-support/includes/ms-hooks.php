<?php
if (!defined('ABSPATH'))
    die('Restricted Access');


// wrong username password handling
add_action('wp_login_failed', 'MJTC_login_failed', 10, 2);
function MJTC_login_failed($MJTC_username)
{
    $MJTC_referrer = wp_get_referer();
    if ($MJTC_referrer && !MJTC_majesticsupportphplib::MJTC_strstr($MJTC_referrer, 'wp-login') && !MJTC_majesticsupportphplib::MJTC_strstr($MJTC_referrer, 'wp-admin')) {
        if (isset($_POST['wp-submit'])) {
            MJTC_message::MJTC_setMessage(esc_html(__('Username / password is incorrect', 'majestic-support')), 'error');
            wp_safe_redirect(majesticsupport::makeUrl(array('mjsmod' => 'majesticsupport', 'mjslay' => 'login', 'mspageid' => majesticsupport::getPageid())));
            exit;
        } else {
            return;
        }
    }
}

// Updates authentication to return an error when one field or both are blank
add_filter('authenticate', 'MJTC_authenticate_username_password', 30, 3);

function MJTC_authenticate_username_password($MJTC_user, $MJTC_username, $MJTC_password)
{
    if (is_a($MJTC_user, 'WP_User')) {
        return $MJTC_user;
    }
    if (isset($_POST['wp-submit']) && (empty($_POST['pwd']) || empty($_POST['log']))) {
        return false;
    }
    return $MJTC_user;
}

// ------------------- ms registrationFrom request handler--------
// register a new user
function MJTC_add_new_member()
{
    if (isset($_POST["ms_user_login"]) && isset($_POST["ms_support_register_nonce"]) && wp_verify_nonce($_POST['ms_support_register_nonce'], 'ms-support-register-nonce')) {
        $MJTC_user_login = majesticsupport::MJTC_sanitizeData($_POST["ms_user_login"]);// MJTC_sanitizeData() function uses wordpress santize functions
        $MJTC_user_email = sanitize_email($_POST["MJTC_user_email"]);
        $MJTC_user_first = sanitize_text_field($_POST["MJTC_user_first"]);
        $MJTC_user_last = sanitize_text_field($_POST["MJTC_user_last"]);
        $MJTC_user_pass = sanitize_text_field($_POST["MJTC_ms_user_pass"]);
        $MJTC_pass_confirm = sanitize_text_field($_POST["MJTC_MJTC_user_pass_confirm"]);

        // this is required for username checks
        // require_once(ABSPATH . WPINC . '/registration.php');

        if (username_exists($MJTC_user_login)) {
            // Username already registered
            MJTC_errors()->add('username_unavailable', esc_html(__('Username already taken', 'majestic-support')));
        }
        if (!validate_username($MJTC_user_login)) {
            // invalid username
            MJTC_errors()->add('username_invalid', esc_html(__('Invalid username', 'majestic-support')));
        }
        if ($MJTC_user_login == '') {
            // empty username
            MJTC_errors()->add('username_empty', esc_html(__('Please enter a username', 'majestic-support')));
        }
        if (!is_email($MJTC_user_email)) {
            //invalid email
            MJTC_errors()->add('email_invalid', esc_html(__('Invalid email', 'majestic-support')));
        }
        if (email_exists($MJTC_user_email)) {
            //Email address already registered
            MJTC_errors()->add('email_used', esc_html(__('Email already registered', 'majestic-support')));
        }
        if ($MJTC_user_pass == '') {
            // passwords do not match
            MJTC_errors()->add('password_empty', esc_html(__('Please enter a password', 'majestic-support')));
        }
        if ($MJTC_user_pass != $MJTC_pass_confirm) {
            // passwords do not match
            MJTC_errors()->add('password_mismatch', esc_html(__('Passwords do not match', 'majestic-support')));
        }
        if (majesticsupport::$_config['captcha_on_registration'] == 1) {
            if (majesticsupport::$_config['captcha_selection'] == 1) { // Google recaptcha
                $MJTC_gresponse = majesticsupport::MJTC_sanitizeData($_POST['MJTC_g-recaptcha-response']);// MJTC_sanitizeData() function uses wordpress santize functions
                $MJTC_resp = MJTC_googleRecaptchaHTTPPost(majesticsupport::$_config['recaptcha_privatekey'], $MJTC_gresponse);
                if (!$MJTC_resp) {
                    MJTC_errors()->add('invalid_captcha', esc_html(__('Invalid captcha', 'majestic-support')));
                }
            } else { // own captcha
                $MJTC_captcha = new MJTC_captcha;
                $MJTC_result = $MJTC_captcha->MJTC_checkCaptchaUserForm();
                if ($MJTC_result != 1) {
                    MJTC_errors()->add('invalid_captcha', esc_html(__('Invalid captcha', 'majestic-support')));
                }
            }
        }


        $MJTC_errors = MJTC_errors()->get_error_messages();

        // only create the user in if there are no errors
        if (empty($MJTC_errors)) {
            // handled for useroptions addon
            $MJTC_default_role = majesticsupport::$_config['wp_default_role'];
            if ($MJTC_default_role == 0) {
                $MJTC_default_role = 'subscriber';
            }

            $MJTC_wperrors = register_new_user($MJTC_user_login, $MJTC_user_email);
            $MJTC_new_user_id = "";
            if (!is_wp_error($MJTC_wperrors)) {
                $MJTC_new_user_id = $MJTC_wperrors;
                wp_set_password($MJTC_user_pass, $MJTC_new_user_id);
                update_user_option($MJTC_new_user_id, 'first_name', $MJTC_user_first, true);
                update_user_option($MJTC_new_user_id, 'last_name', $MJTC_user_last, true);
                // Update the user's role according to configuration
                wp_update_user(['ID'   => $MJTC_new_user_id,'role' => $MJTC_default_role,]);
                MJTC_message::MJTC_setMessage(esc_html(__("User has been successfully registered", 'majestic-support')), 'updated');
            } else {
                //Something's wrong
                MJTC_errors()->add('email_invalid', majesticsupport::MJTC_getVarValue($MJTC_wperrors->get_error_message()));
            }
            if ($MJTC_new_user_id) {

                $MJTC_row = MJTC_includer::MJTC_getTable('users');
                $MJTC_data['id'] = '';
                $MJTC_data['wpuid'] = $MJTC_new_user_id;
                $MJTC_data['display_name'] = $MJTC_user_first . ' ' . $MJTC_user_last;
                $MJTC_data['name'] = $MJTC_user_login;
                $MJTC_data['user_email'] = $MJTC_user_email;
                $MJTC_data['issocial'] = 0;
                $MJTC_data['socialid'] = null;
                $MJTC_data['status'] = 1;
                $MJTC_data['autogenerated'] = 0;
                $MJTC_row->bind($MJTC_data);
                $MJTC_row->store();

                //mailchimp subscribe for newsletter
                if (in_array('mailchimp', majesticsupport::$_active_addons)) {
                    if (isset($_POST['ms_mailchimp_subscribe']) && $_POST['ms_mailchimp_subscribe'] == 1) {
                        $MJTC_res = MJTC_includer::MJTC_getModel('mailchimp')->subscribe($MJTC_user_email, $MJTC_user_first, $MJTC_user_last);
                        if (!$MJTC_res) {
                            MJTC_message::MJTC_setMessage(esc_html(__("Could not subscribe to the newsletter", 'majestic-support')), 'error');
                        } else {
                            $MJTC_dboptin = MJTC_includer::MJTC_getModel('configuration')->getConfigValue('mailchimp_double_optin');
                            if ($MJTC_dboptin == 1) {
                                MJTC_message::MJTC_setMessage(esc_html(__("Please check confirmation email to complete your subscription for the newsletter", 'majestic-support')), 'updated');
                            } else {
                                MJTC_message::MJTC_setMessage(esc_html(__("You have successfully subscribed to the newsletter", 'majestic-support')), 'updated');
                            }
                        }
                    }
                }


                // send an email to the admin alerting them of the registration
                wp_new_user_notification($MJTC_new_user_id);
                // log the new user in
                wp_set_current_user($MJTC_new_user_id, $MJTC_user_login);
                wp_set_auth_cookie($MJTC_new_user_id);
                $MJTC_url = majesticsupport::makeUrl(array('mjsmod' => 'majesticsupport', 'mjslay' => 'controlpanel', 'mspageid' => majesticsupport::getPageid()));
                // send the newly created user to the home page after logging them in
                wp_safe_redirect($MJTC_url);
                exit;
            }
        }
    }
}

add_action('init', 'MJTC_add_new_member');

// used for tracking error messages
function MJTC_errors()
{
    static $MJTC_wp_error; // Will hold global variable safely
    return isset($MJTC_wp_error) ? $MJTC_wp_error : ($MJTC_wp_error = new WP_Error(null, null, null));
}

// displays error messages from form submissions
function MJTC_show_error_messages()
{
    if ($MJTC_codes = MJTC_errors()->get_error_codes()) {
        $MJTC_html = '<div class="MJTC_errors">';
        // Loop error codes and display errors
        foreach ($MJTC_codes as $MJTC_code) {
            $MJTC_message = MJTC_errors()->get_error_message($MJTC_code);
            $MJTC_html .= '<span class="error"><strong>' . esc_html(__('Error','majestic-support')) . '</strong>: ' . wp_kses($MJTC_message, MJTC_ALLOWED_TAGS) . '</span><br/>';
        }
        $MJTC_html .= '</div>';
        echo wp_kses($MJTC_html, MJTC_ALLOWED_TAGS);
    }
}

//to give signature option for admin
add_action('show_user_profile', 'MJTC_add_admin_signature_field');
add_action('edit_user_profile', 'MJTC_add_admin_signature_field');
function MJTC_add_admin_signature_field($MJTC_user)
{
    if (!current_user_can('manage_options')) {
        return;
    }
    ?>
    <h2><?php echo esc_html(__("Majestic Support", 'majestic-support')); ?></h2>
    <table class="form-table">
        <tr>
            <th>
                <label id="mssignatureautoappend"><?php echo esc_html(__("Auto Append Signature", 'majestic-support')); ?></label>
            </th>
            <td>
				<label for="ms_signature_auto_append">
					<input name="ms_signature_auto_append" type="checkbox" id="ms_signature_auto_append" value="1" <?php if(get_user_meta($MJTC_user->ID, 'ms_signature_auto_append', true)=='1' ){ echo " checked=\"checked\""; } ?>>
					
								<?php echo esc_html(__("Signature will auto append in the ticket reply page for admin", 'majestic-support')); ?>							
				</label>
            </td>
        </tr>
        <tr>
            <th>
                <label id="mssignature"><?php echo esc_html(__("Signature", 'majestic-support')); ?></label>
            </th>
            <td>
                <?php wp_editor(get_user_meta($MJTC_user->ID, 'ms_signature', true), 'ms_signature', array('media_buttons' => false)); ?>
            </td>
        </tr>
    </table>
    <?php
}

add_action('personal_options_update', 'MJTC_save_admin_signature_field');
add_action('edit_user_profile_update', 'MJTC_save_admin_signature_field');
function MJTC_save_admin_signature_field($MJTC_uid)
{
    $MJTC_uid = absint($MJTC_uid);
    if (!$MJTC_uid || !current_user_can('edit_user', $MJTC_uid)) {
        return;
    }

    check_admin_referer('update-user_' . $MJTC_uid);

    $ms_signature_auto_append = isset($_POST['ms_signature_auto_append']) ? absint($_POST['ms_signature_auto_append']) : 0;
    update_user_meta($MJTC_uid, 'ms_signature_auto_append', $ms_signature_auto_append);

    $MJTC_raw_signature = isset($_POST['ms_signature']) ? wp_unslash($_POST['ms_signature']) : '';
    $MJTC_signature = MJTC_includer::MJTC_getModel('majesticsupport')->getSanitizedEditorData($MJTC_raw_signature);
    update_user_meta($MJTC_uid, 'ms_signature', $MJTC_signature);
}

// ---------------Remove wp user ---------------

function MJTC_remove_user($MJTC_user_id)
{
    $mjtc_class = MJTC_includer::MJTC_getObjectClass('user');
    $MJTC_userid = $mjtc_class->MJTC_getUserIDByWPUid($MJTC_user_id);

    if (isset($_POST['delete_option']) and $_POST['delete_option'] == 'delete') {

        $MJTC_row = MJTC_includer::MJTC_getTable('users');
        $MJTC_data['id'] = $MJTC_userid;
        $MJTC_data['wpuid'] = 0;
        $MJTC_data['status'] = 0;
        $MJTC_row->bind($MJTC_data);
        $MJTC_row->store();
    }
}

add_action('delete_user', 'MJTC_remove_user');

add_action('personal_options_update', 'MJTC_update_user_profile');


function MJTC_update_user_profile($MJTC_user_id)
{
    $MJTC_user_id = absint($MJTC_user_id);
    if(!$MJTC_user_id || !current_user_can('edit_user', $MJTC_user_id)){
        return false;
    }

    check_admin_referer('update-user_' . $MJTC_user_id);

    $MJTC_query = "SELECT * FROM `" . majesticsupport::$_db->prefix . "users` WHERE id = " . esc_sql($MJTC_user_id);
    $MJTC_user = majesticsupport::$_db->get_row($MJTC_query);

    $MJTC_uid = "";
	$MJTC_post_user_id = '';
	$MJTC_id = '';
	$MJTC_post_user_login='';
    $MJTC_post_display_name='';
	$MJTC_post_nickname='';
	
	if(isset($_POST['user_id'])) $MJTC_post_user_id = majesticsupport::MJTC_sanitizeData($_POST['user_id']);// MJTC_sanitizeData() function uses wordpress santize functions
    if ($MJTC_post_user_id == $MJTC_user_id) {
        $MJTC_query = "SELECT id FROM `" . majesticsupport::$_db->prefix . "mjtc_support_users` WHERE wpuid = " . esc_sql($MJTC_user_id);
        $MJTC_id = majesticsupport::$_db->get_var($MJTC_query);
    }
	$MJTC_name = "";
	if(isset($_POST['first_name'])) $MJTC_name = majesticsupport::MJTC_sanitizeData($_POST['first_name']);// MJTC_sanitizeData() function uses wordpress santize functions
	if(isset($_POST['last_name'])) $MJTC_name = $MJTC_name. ' ' . esc_html(majesticsupport::MJTC_sanitizeData($_POST['last_name']));// MJTC_sanitizeData() function uses wordpress santize functions
	if(isset($_POST['user_login'])) $MJTC_post_user_login = majesticsupport::MJTC_sanitizeData($_POST['user_login']);// MJTC_sanitizeData() function uses wordpress santize functions
    if(isset($_POST['display_name'])) $MJTC_post_display_name = majesticsupport::MJTC_sanitizeData($_POST['display_name']);// MJTC_sanitizeData() function uses wordpress santize functions
	if(isset($_POST['nickname'])) $MJTC_post_nickname = majesticsupport::MJTC_sanitizeData($_POST['nickname']);// MJTC_sanitizeData() function uses wordpress santize functions
	
	if (isset($_POST['email'])) {
		$MJTC_row = MJTC_includer::MJTC_getTable('users');
		$MJTC_data['id'] = $MJTC_id;
		$MJTC_data['wpuid'] = $MJTC_user_id;
		$MJTC_data['name'] = $MJTC_name;
		$MJTC_data['display_name'] = $MJTC_name;
		$MJTC_data['user_nicename'] = $MJTC_post_nickname;
		$MJTC_data['user_email'] = majesticsupport::MJTC_sanitizeData($_POST['email']);// MJTC_sanitizeData() function uses wordpress santize functions
		$MJTC_data['issocial'] = 0;
		$MJTC_data['socialid'] = null;
		$MJTC_data['status'] = 1;
		$MJTC_data['created'] = date_i18n('Y-m-d H:i:s');
		$MJTC_row->bind($MJTC_data);
		$MJTC_row->store();
	}
}

add_action('edit_user_profile_update', 'MJTC_update_user_profile');
add_action('user_register', 'MJTC_update_user_profile'); // creating a new user

// Language Related Hooks

add_action('plugins_loaded', 'MJTC_check_and_download_languages');

function MJTC_check_and_download_languages() {

    if (!current_user_can('manage_options') && !wp_doing_cron()) {
        return; // Skip download attempt for non-privileged contexts
    }

    $locale = determine_locale();
    if ($locale === 'en_US') return;

    $status = get_option('majesticsupport_translation_status_' . $locale);
    if ($status === 'verified' || $status === 'failed') {
        return;
    }

    $textdomain   = 'majestic-support';
    $default_list = MJTC_DEFAULT_LANGUAGES;
    $target_dir   = MJTC_PLUGIN_PATH . 'languages/';
    $extensions   = in_array($locale, $default_list) ? array('po') : array('mo', 'po');

    $all_exist = true;
    foreach ($extensions as $ext) {
        if (!file_exists($target_dir . "{$textdomain}-{$locale}.{$ext}")) {
            $all_exist = false;
            break;
        }
    }

    if ($all_exist) {
        update_option('majesticsupport_translation_status_' . $locale, 'verified');
        return;
    }

    MJTC_execute_download_process_for_languagefiles($locale, $extensions);
}

function MJTC_execute_download_process_for_languagefiles($locale, $extensions) {
    global $wp_filesystem;

    // Initialize WP_Filesystem safely
    if (empty($wp_filesystem)) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        if ( ! WP_Filesystem() ) {
            return; // Exit if Filesystem credentials are required but unavailable
        }
    }

    $textdomain = 'majestic-support';
    $cdn_base   = 'https://d2m0o2vxgtttki.cloudfront.net/'; // Retaining your secure CloudFront endpoint

    $target_dir = MJTC_PLUGIN_PATH . 'languages/';

    $locales_to_try = array($locale);
    $fallback_locale = MJTC_get_fallback_locale($locale);

    if ($fallback_locale) {
        $locales_to_try[] = $fallback_locale;
    }

    $download_successful = false;
    $downloaded_locale = '';

    foreach ($locales_to_try as $attempt_locale) {
        $all_extensions_downloaded = true;

        foreach ($extensions as $ext) {
            $remote_filename = "{$textdomain}-{$attempt_locale}.{$ext}";
            $local_filename  = "{$textdomain}-{$locale}.{$ext}";

            // Safe remote call wrapper with fallback timeouts
            $response = wp_remote_get($cdn_base . $remote_filename, array(
                'timeout' => 15
            ));

            if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
                if (!$wp_filesystem->is_dir($target_dir)) {
                    $wp_filesystem->mkdir($target_dir, FS_CHMOD_DIR);
                }
                $saved = $wp_filesystem->put_contents($target_dir . $local_filename, wp_remote_retrieve_body($response));
                if (!$saved) {
                    $all_extensions_downloaded = false;
                    break;
                }
            } else {
                $all_extensions_downloaded = false;
                break;
            }
        }

        if ($all_extensions_downloaded) {
            $download_successful = true;
            $downloaded_locale = $attempt_locale;
            break;
        }
    }

    // Determine notice type and save to transient
    if ($download_successful) {
        update_option('majesticsupport_translation_status_' . $locale, 'verified');

        $notice_type = ($downloaded_locale === $locale) ? 'exact_success' : 'fallback_success';
        set_transient('majesticsupport_lang_notice', array(
            'type'     => $notice_type,
            'original' => $locale,
            'fallback' => $downloaded_locale
        ), 60);

    } else {
        update_option('majesticsupport_translation_status_' . $locale, 'failed');

        set_transient('majesticsupport_lang_notice', array(
            'type'     => 'failed',
            'original' => $locale,
        ), 300);
    }
}

function MJTC_get_fallback_locale($locale) {
    $base_lang = substr($locale, 0, 2);

    // Comprehensive fallback map based on standard WordPress locales
    $fallbacks = array(
        'ar' => 'ar',          // Arabic
        'cs' => 'cs_CZ',       // Czech
        'de' => 'de_DE',       // German
        'el' => 'el',          // Greek
        'en' => 'en_US',       // English
        'es' => 'es_ES',       // Spanish
        'fa' => 'fa_IR',       // Persian
        'fr' => 'fr_FR',       // French
        'hu' => 'hu_HU',       // Hungarian
        'id' => 'id_ID',       // Indonesian
        'it' => 'it_IT',       // Italian
        'ja' => 'ja_JP',       // Japanese
        'ko' => 'ko_KR',       // Korean
        'ms' => 'ms_MY',       // Malay
        'nl' => 'nl_NL',       // Dutch
        'pl' => 'pl_PL',       // Polish
        'pt' => 'pt_BR',       // Brazil
        'ro' => 'ro_RO',       // Romanian
        'ru' => 'ru_RU',       // Russian
        'sv' => 'sv',          // Swedish
        'th' => 'th_TH',       // Thai
        'tl' => 'tl_PH',       // Filipino
        'tr' => 'tr_TR',       // Turkish
        'zh' => 'zh_CN'        // Chinese (Simplified)
    );

    if (isset($fallbacks[$base_lang]) && $fallbacks[$base_lang] !== $locale) {
        return $fallbacks[$base_lang];
    }

    return false;
}

add_action('admin_notices', 'MJTC_display_language_download_notice');

function MJTC_display_language_download_notice() {
    // Only show to users who can manage the site
    if (!current_user_can('manage_options')) {
        return;
    }

    $notice = get_transient('majesticsupport_lang_notice');
    if (!$notice) {
        return;
    }

    // Clear the transient immediately so it only shows once
    delete_transient('majesticsupport_lang_notice');

    $type     = $notice['type'];
    $original = esc_html($notice['original']);

    if ($type === 'exact_success') {
        echo '<div class="notice notice-success is-dismissible">';
        echo '<p><strong>' . esc_html(__('Majestic Support', 'majestic-support')) . ':</strong> ' . sprintf(esc_html(__('Language files for %s successfully downloaded.', 'majestic-support')), '<code>' . $original . '</code>') . '</p>';
        echo '</div>';
    }
    elseif ($type === 'fallback_success') {
        $fallback = esc_html($notice['fallback']);
        echo '<div class="notice notice-warning is-dismissible">';
        echo '<p><strong>' . esc_html(__('Majestic Support', 'majestic-support')) . ':</strong> ' . sprintf(esc_html(__('Alternate language file downloaded. We tried to find %1$s, but downloaded %2$s as a fallback.', 'majestic-support')), '<code>' . $original . '</code>', '<code>' . $fallback . '</code>') . '</p>';
        echo '</div>';
    }
}
?>
