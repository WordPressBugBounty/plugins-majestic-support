<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if(in_array('notification', majesticsupport::$_active_addons)){
    wp_enqueue_script('majesticsupport-notify-app', MJTC_PLUGIN_URL . 'includes/js/firebase-app.js', array(), '1.0.0', true);
    wp_enqueue_script('majesticsupport-notify-message', MJTC_PLUGIN_URL . 'includes/js/firebase-messaging.js', array(), '1.0.0', true);
}
$MJTC_protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
MJTC_message::MJTC_getMessage();
if(in_array('notification', majesticsupport::$_active_addons)){
    if(majesticsupport::$_data[0]['apiKey_firebase'] != "" && majesticsupport::$_data[0]['databaseURL_firebase'] != "" && majesticsupport::$_data[0]['authDomain_firebase'] != "" && majesticsupport::$_data[0]['projectId_firebase'] != "" && majesticsupport::$_data[0]['storageBucket_firebase'] != "" && majesticsupport::$_data[0]['messagingSenderId_firebase'] != "" && majesticsupport::$_data[0]['server_key_firebase'] != ""){
        do_action('MJTC_ticket-notify-generate-token');
    }
}
?>
<?php
$majesticsupport_js ="
    jQuery(document).ready(function () {
        jQuery('.majestic-support-configurations-toggle').click(function(){
            jQuery('.majestic-support-configurations .majestic-support-configurations-left').toggle();
        });
    });
    function deleteSupportCustomImage(){
        jQuery.post(ajaxurl, {action: 'mjsupport_ajax', mjsmod: 'configuration', task: 'deleteSupportCustomImage', '_wpnonce':'". esc_attr(wp_create_nonce("delete-support-customimage"))."'}, function (data) {
            if(data){
                jQuery('.mjsupport-form-help-txt-wrp').hide();
            }
        });
    }

    jQuery(document).ready(function () {
        jQuery('select#set_login_link').change(function(){
            var value = jQuery(this).val();
            if (value == 2) {
               jQuery('.loginlink_field').attr('style','display: block');
            } else {
                jQuery('.loginlink_field').attr('style','display: none');
            }
        })

        var value = jQuery('select#set_login_link').val();
        if (value == 2) {
            jQuery('.loginlink_field').attr('style','display: block');
        } else {
            jQuery('.loginlink_field').attr('style','display: none');
        }

        jQuery('select#set_register_link').change(function(){
            var value = jQuery(this).val();
            if (value == 2) {
                jQuery('.registerlink_field').attr('style','display: block');
            } else {
                jQuery('.registerlink_field').attr('style','display: none');
            }
        });

        var value = jQuery('select#set_register_link').val();
        if (value == 2) {
           jQuery('.registerlink_field').attr('style','display: block');
        } else {
            jQuery('.registerlink_field').attr('style','display: none');
        }

    });

    // for hide and show baseb on custom fields
    jQuery(document).ready(function () {
        jQuery('select#ticketid_sequence').change(function(){
            var value = jQuery(this).val();
            if (value == 2){
                jQuery('.Ticketid-sequence-custom').slideDown('slow');
            } else {
                jQuery('.Ticketid-sequence-custom').slideUp('slow');
            }
            setpadZerosText();
        });
        var value = jQuery('select#ticketid_sequence').val();
        if (value == 2){
            jQuery('.Ticketid-sequence-custom').css('display','inline-block');
        } else {
            jQuery('.Ticketid-sequence-custom').css('display','none');
        }

        // for prefix and suffix
        jQuery('#padZeros-prefix').text(jQuery('#prefix_ticketid').val());
        jQuery('#padZeros-suffix').text(jQuery('#suffix_ticketid').val());
        jQuery('#prefix_ticketid').on('input', function(){
            jQuery('#padZeros-prefix').text(jQuery(this).val());
        });
        jQuery('#suffix_ticketid').on('input', function(){
            jQuery('#padZeros-suffix').text(jQuery(this).val());
        });

        // for pad zeroes
        jQuery('select#padding_zeros_ticketid').change(function(){
           setpadZerosText();
        });
        setpadZerosText();
        
    });

    function setpadZerosText() {
        var value = jQuery('select#ticketid_sequence').val();
        if (value == 1){
            jQuery('#padZeros').text('xxxxxxx');
        } else {
            var value = jQuery('select#padding_zeros_ticketid').val();
            if (value == 1){
                jQuery('#padZeros').text('1');
            } else if (value == 2) {
                jQuery('#padZeros').text('01');
            } else if (value == 3) {
                jQuery('#padZeros').text('001');
            } else if (value == 4) {
                jQuery('#padZeros').text('0001');
            } else if (value == 5) {
                jQuery('#padZeros').text('00001');
            } else if (value == 6) {
                jQuery('#padZeros').text('000001');
            }
        }
    }

";
wp_add_inline_script('majestic-support-cmain-js',$majesticsupport_js);
?>
<?php
$MJTC_captchaselection = array(
    (object) array('id' => '1', 'text' => esc_html(__('Google reCAPTCHA', 'majestic-support'))),
    (object) array('id' => '2', 'text' => esc_html(__('Own Captcha', 'majestic-support')))
);
$MJTC_owncaptchaoparend = array(
    (object) array('id' => '2', 'text' => '2'),
    (object) array('id' => '3', 'text' => '3')
);
$MJTC_owncaptchatype = array(
    (object) array('id' => '0', 'text' => esc_html(__('Any', 'majestic-support'))),
    (object) array('id' => '1', 'text' => esc_html(__('Addition', 'majestic-support'))),
    (object) array('id' => '2', 'text' => esc_html(__('Subtraction', 'majestic-support')))
);
$MJTC_recaptcha_version = array(
    (object) array('id' => '1', 'text' => esc_html(__('reCAPTCHA v2', 'majestic-support'))),
    (object) array('id' => '2', 'text' => esc_html(__('reCAPTCHA v3', 'majestic-support')))
);
$MJTC_yesno = array(
    (object) array('id' => '1', 'text' => esc_html(__('Yes', 'majestic-support'))),
    (object) array('id' => '2', 'text' => esc_html(__('No', 'majestic-support')))
);
$MJTC_showhide = array(
    (object) array('id' => '1', 'text' => esc_html(__('Show', 'majestic-support'))),
    (object) array('id' => '0', 'text' => esc_html(__('Hide', 'majestic-support')))
);
$MJTC_defaultcustom = array(
    (object) array('id' => '1', 'text' => esc_html(__('Majestic Support Login Page', 'majestic-support'))),
    (object) array('id' => '3', 'text' => esc_html(__('WordPress Default Login Page', 'majestic-support'))),
    (object) array('id' => '2', 'text' => esc_html(__('Custom', 'majestic-support')))
);
$MJTC_defaultregisterpage = array(
    (object) array('id' => '1', 'text' => esc_html(__('Majestic Support Register Page', 'majestic-support'))),
    (object) array('id' => '3', 'text' => esc_html(__('WordPress Default Register Page', 'majestic-support'))),
    (object) array('id' => '2', 'text' => esc_html(__('Custom', 'majestic-support')))
);
$MJTC_screentagposition = array(
    (object) array('id' => '1', 'text' => esc_html(__('Top left', 'majestic-support'))),
    (object) array('id' => '2', 'text' => esc_html(__('Top right', 'majestic-support'))),
    (object) array('id' => '3', 'text' => esc_html(__('Middle left', 'majestic-support'))),
    (object) array('id' => '4', 'text' => esc_html(__('Middle right', 'majestic-support'))),
    (object) array('id' => '5', 'text' => esc_html(__('Bottom left', 'majestic-support'))),
    (object) array('id' => '6', 'text' => esc_html(__('Bottom right', 'majestic-support')))
);
$MJTC_enableddisabled = array(
    (object) array('id' => '1', 'text' => esc_html(__('Enabled', 'majestic-support'))),
    (object) array('id' => '2', 'text' => esc_html(__('Disabled', 'majestic-support')))
);
$MJTC_mailreadtype = array(
    (object) array('id' => '1', 'text' => esc_html(__('Only New Tickets', 'majestic-support'))),
    (object) array('id' => '2', 'text' => esc_html(__('Only Replies', 'majestic-support'))),
    (object) array('id' => '3', 'text' => esc_html(__('Both', 'majestic-support')))
);

$MJTC_sequence = array(
    (object) array('id' => '1', 'text' => esc_html(__('Random', 'majestic-support'))),
    (object) array('id' => '2', 'text' => esc_html(__('Sequence', 'majestic-support')))
);

$MJTC_padZeros = array(
    (object) array('id' => '1', 'text' => esc_html('1')),
    (object) array('id' => '2', 'text' => esc_html('2')),
    (object) array('id' => '3', 'text' => esc_html('3')),
    (object) array('id' => '4', 'text' => esc_html('4')),
    (object) array('id' => '5', 'text' => esc_html('5')),
    (object) array('id' => '6', 'text' => esc_html('6'))
);

$MJTC_hosttype = array(
    (object) array('id' => '1', 'text' => esc_html(__('Gmail', 'majestic-support'))),
    (object) array('id' => '2', 'text' => esc_html(__('Yahoo', 'majestic-support'))),
    (object) array('id' => '3', 'text' => esc_html(__('Aol', 'majestic-support'))),
    (object) array('id' => '4', 'text' => esc_html(__('Other', 'majestic-support')))
);

$MJTC_ticketordering = array(
    (object) array('id' => '1', 'text' => esc_html(__('Default', 'majestic-support'))),
    (object) array('id' => '2', 'text' => esc_html(__('Created', 'majestic-support')))
);

$MJTC_repliesordering = array(
    (object) array('id' => 'ASC', 'text' => esc_html(__('Oldest First', 'majestic-support'))),
    (object) array('id' => 'DESC', 'text' => esc_html(__('Newest First', 'majestic-support')))
);
$MJTC_ticketsorting = array(
    (object) array('id' => '1', 'text' => esc_html(__('Ascending', 'majestic-support'))),
    (object) array('id' => '2', 'text' => esc_html(__('Descending', 'majestic-support')))
);
$MJTC_smartreply = array(
    (object) array('id' => '1', 'text' => esc_html('1')),
    (object) array('id' => '2', 'text' => esc_html('2')),
    (object) array('id' => '3', 'text' => esc_html('3'))
);
$MJTC_reasontype = array(
    (object) array('id' => '1', 'text' => esc_html(__('Single', 'majestic-support'))),
    (object) array('id' => '2', 'text' => esc_html(__('Multiple', 'majestic-support')))
);
$MJTC_offline = array(
    (object) array('id' => '1', 'text' => esc_html(__('Offline', 'majestic-support'))),
    (object) array('id' => '2', 'text' => esc_html(__('Online', 'majestic-support')))
);
$MJTC_delay_type = array(
    (object) array('id' => '1', 'text' => esc_html(__('Days', 'majestic-support'))),
    (object) array('id' => '2', 'text' => esc_html(__('Hours', 'majestic-support')))
);
// Define the options for the dropdown
$MJTC_delete_intervals = array(
    (object) array('id' => '0',  'text' => esc_html(__('Never (Default)', 'majestic-support'))),
    (object) array('id' => '1',  'text' => esc_html(__('1 Month', 'majestic-support'))),
    (object) array('id' => '2',  'text' => esc_html(__('2 Months', 'majestic-support'))),
    (object) array('id' => '3',  'text' => esc_html(__('3 Months', 'majestic-support'))),
    (object) array('id' => '6',  'text' => esc_html(__('6 Months', 'majestic-support'))),
    (object) array('id' => '12', 'text' => esc_html(__('1 Year', 'majestic-support'))),
    (object) array('id' => '36', 'text' => esc_html(__('3 Years', 'majestic-support'))),
    (object) array('id' => '60', 'text' => esc_html(__('5 Years', 'majestic-support'))),
    (object) array('id' => '120', 'text' => esc_html(__('10 Years', 'majestic-support')))
);
// wp roles combo for new user
global $wp_roles;
$MJTC_roles = $wp_roles->get_names();
$MJTC_userroles = array();
foreach ($MJTC_roles as $MJTC_key => $MJTC_value) {
    $MJTC_userroles[] = (object) array('id' => $MJTC_key, 'text' => $MJTC_value);
}

$majesticsupport_settings_config = [
    'general' => [
        'label'  => __('General', 'majestic-support'),
        'icon'   => 'settings', // Based on your SVG gear icon
        'groups' => [
            'general_settings' => [
                'title'       => __('General Settings', 'majestic-support'),
                'description' => __('Basic configuration for your support portal visibility and behavior', 'majestic-support'),
                'fields'      => [
                    ['id' => 'title', 'label' => __('Title', 'majestic-support'), 'type' => 'text', 'value' => majesticsupport::$_data[0]['title'], 'tooltip' => __('Set the heading of your plugin', 'majestic-support')],
                    ['id' => 'default_pageid', 'label' => __('Ticket Default Page', 'majestic-support'), 'type' => 'select', 'value' => majesticsupport::$_data[0]['default_pageid'], 'tooltip' => __('Select the Majestic Support default page. Email links and support icons might not work if this is not set.', 'majestic-support'), 'options' => MJTC_includer::MJTC_getModel('configuration')->getPageList()],
                    ['id' => 'data_directory', 'label' => __('Data Directory', 'majestic-support'), 'type' => 'text', 'value' => majesticsupport::$_data[0]['data_directory'], 'tooltip' => __('Set the name for your data directory. You must rename the folder in the file system manually first.', 'majestic-support')],
                    ['id' => 'date_format', 'label' => __('Date Format', 'majestic-support'), 'type' => 'select', 'value' => majesticsupport::$_data[0]['date_format'], 'tooltip' => __('Set the default date format', 'majestic-support'), 'options' => array((object) array('id' => 'd-m-Y', 'text' => esc_html(__("DD-MM-YYYY", 'majestic-support'))), (object) array('id' => 'm-d-Y', 'text' => esc_html(__("MM-DD-YYYY", 'majestic-support'))), (object) array('id' => 'Y-m-d', 'text' => esc_html(__("YYYY-MM-DD", 'majestic-support'))))],
                    ['id' => 'pagination_default_page_size', 'label' => __('Pagination default page size', 'majestic-support'), 'type' => 'text', 'value' => majesticsupport::$_data[0]['pagination_default_page_size'], 'tooltip' => __('Set the number of records per page', 'majestic-support')],
                    ['id' => 'show_breadcrumbs', 'label' => __('Breadcrumbs', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['show_breadcrumbs'], 'tooltip' => __('Show or hide breadcrumbs', 'majestic-support'), 'options' => $MJTC_showhide],
                    ['id' => 'show_header', 'label' => __('Top Header', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['show_header'], 'tooltip' => __('Show or hide top header', 'majestic-support'), 'options' => $MJTC_showhide],
                    ['id' => 'show_avatar', 'label' => __('Show User Avatar', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['show_avatar'], 'tooltip' => __('Showing avatars may slightly slow down page loading', 'majestic-support'), 'options' => $MJTC_yesno],
                    ['id' => 'count_on_myticket', 'label' => __('Show count on tickets', 'majestic-support'), 'type' => 'select', 'value' => majesticsupport::$_data[0]['count_on_myticket'], 'tooltip' => __('Show the number of open, closed, and answered tickets in my tickets and dashboard', 'majestic-support'), 'options' => $MJTC_yesno, 'video' => 'gCB-wGVZph8'],
                    ['id' => 'wp_default_role', 'label' => __('Default wp role for new users', 'majestic-support'), 'type' => 'select', 'value' => majesticsupport::$_data[0]['wp_default_role'], 'tooltip' => __('Select the role you want to assign to new users', 'majestic-support'), 'options' => $MJTC_userroles, 'pro' => ['slug' => 'useroptions', 'name' => __('User Options', 'majestic-support')]],
                ]
            ],
            'attachments' => [
                'title'       => __('Attachments', 'majestic-support'),
                'description' => __('Control file upload limits and allowed types', 'majestic-support'),
                'fields'      => [
                    ['id' => 'no_of_attachement', 'label' => __('No. of attachments', 'majestic-support'), 'type' => 'text', 'value' => majesticsupport::$_data[0]['no_of_attachement'], 'tooltip' => __('Number of attachments allowed at a time', 'majestic-support')],
                    ['id' => 'file_maximum_size', 'label' => __('File maximum size', 'majestic-support'), 'type' => 'text', 'value' => majesticsupport::$_data[0]['file_maximum_size'], 'tooltip' => __('Maximum size allowed in Kb', 'majestic-support')],
                    ['id' => 'file_extension', 'label' => __('File extensions', 'majestic-support'), 'type' => 'textarea', 'value' => majesticsupport::$_data[0]['file_extension'], 'tooltip' => __('File extensions allowed to attach (comma separated)', 'majestic-support')],
                ]
            ],
            'login' => [
                'title'       => __('Login', 'majestic-support'),
                'description' => __('Configure the login behavior for your support users', 'majestic-support'),
                'fields'      => [
                    ['id' => 'set_login_link', 'label' => __('Set Login Link', 'majestic-support'), 'type' => 'select', 'value' => majesticsupport::$_data[0]['set_login_link'], 'tooltip' => __('Set login link default or custom', 'majestic-support'), 'options' => $MJTC_defaultcustom, 'video' => 'bzK2IxQ0QaU'],
                    ['id' => 'login_link', 'label' => __('Custom Login Link', 'majestic-support'), 'type' => 'text', 'value' => majesticsupport::$_data[0]['login_link'], 'tooltip' => __('Enter custom URL if custom is selected above', 'majestic-support')],
                ]
            ],
            'register' => [
                'title'       => __('Register', 'majestic-support'),
                'description' => __('Configure user registration settings', 'majestic-support'),
                'fields'      => [
                    ['id' => 'set_register_link', 'label' => __('Set register Link', 'majestic-support'), 'type' => 'select', 'value' => majesticsupport::$_data[0]['set_register_link'], 'tooltip' => __('To enable registrations, go to WordPress General Settings and enable Anyone can register', 'majestic-support'), 'options' => $MJTC_defaultregisterpage],
                    ['id' => 'register_link', 'label' => __('Custom Register Link', 'majestic-support'), 'type' => 'text', 'value' => majesticsupport::$_data[0]['register_link'], 'tooltip' => __('Enter custom registration URL', 'majestic-support')],
                ]
            ],
            'support_icon' => [
                'title'       => __('Support Icon', 'majestic-support'),
                'description' => __('Floating icon settings for the front-end', 'majestic-support'),
                'fields'      => [
                    ['id' => 'support_screentag', 'label' => __('Support Icon', 'majestic-support'), 'type' => 'select', 'value' => majesticsupport::$_data[0]['support_screentag'], 'tooltip' => __('Enable or disable your support icon', 'majestic-support'), 'options' => $MJTC_showhide],
                    ['id' => 'support_custom_img', 'label' => __('Custom Image', 'majestic-support'), 'type' => 'file', 'value' => majesticsupport::$_data[0]['support_custom_img'], 'tooltip' => __('Upload a custom image for the support icon', 'majestic-support')],
                    ['id' => 'support_custom_txt', 'label' => __('Custom Text', 'majestic-support'), 'type' => 'text', 'value' => majesticsupport::$_data[0]['support_custom_txt'], 'tooltip' => __('Set custom support text', 'majestic-support')],
                    ['id' => 'screentag_position', 'label' => __('Support Icon Position', 'majestic-support'), 'type' => 'select', 'value' => majesticsupport::$_data[0]['screentag_position'], 'tooltip' => __('Select a position for your support icon', 'majestic-support'), 'options' => $MJTC_screentagposition],
                ]
            ],
            'offline' => [
                'title'       => __('Offline', 'majestic-support'),
                'description' => __('Maintenance mode settings', 'majestic-support'),
                'fields'      => [
                    ['id' => 'offline', 'label' => __('Offline', 'majestic-support'), 'type' => 'select', 'value' => majesticsupport::$_data[0]['offline'], 'tooltip' => __('Set your plugin offline for front end', 'majestic-support'), 'options' => $MJTC_offline],
                    ['id' => 'offline_message', 'label' => __('Offline Message', 'majestic-support'), 'type' => 'wp_editor', 'value' => majesticsupport::$_data[0]['offline_message'], 'tooltip' => __('Set the offline message for your user', 'majestic-support')],
                ]
            ],
            'paid_support' => [
                'title'       => __('Paid Support', 'majestic-support'),
                'description' => __('WooCommerce integration settings', 'majestic-support'),
                'fields'      => [
                    ['id' => 'woocommerce_default_categoryid', 'label' => __('Woocommerce Category', 'majestic-support'), 'type' => 'select', 'value' => majesticsupport::$_data[0]['woocommerce_default_categoryid'], 'tooltip' => __('Select category to display only products of this category on shop page', 'majestic-support'), 'options' => MJTC_includer::MJTC_getModel('configuration')->getWooCommerceCategoryList()],
                ]
            ],
        ]
    ],
    'ticket_settings' => [
        'label'  => __('Ticket Settings', 'majestic-support'),
        'icon'   => 'briefcase',
        'groups' => [
            'ticket_setting' => [
                'title'       => __('Ticket Settings', 'majestic-support'),
                'description' => __('Core configuration for ticket ID formats, reply behavior, and user limits', 'majestic-support'),
                'fields'      => [
                    ['id' => 'prefix_ticketid', 'label' => __('Ticket ID Prefix', 'majestic-support'), 'type' => 'text', 'value' => majesticsupport::$_data[0]['prefix_ticketid'], 'tooltip' => __('Set a prefix for the custom ticket ID', 'majestic-support')],
                    ['id' => 'ticketid_sequence', 'label' => __('Ticket ID sequence', 'majestic-support'), 'type' => 'select', 'value' => majesticsupport::$_data[0]['ticketid_sequence'], 'tooltip' => __('Set the ticket ID sequentially or randomly', 'majestic-support'), 'options' => $MJTC_sequence],
                    ['id' => 'padding_zeros_ticketid', 'label' => __('Pad Zeros', 'majestic-support'), 'type' => 'select', 'value' => majesticsupport::$_data[0]['padding_zeros_ticketid'], 'tooltip' => __('To pad an integer with leading zeros to a specific length', 'majestic-support'), 'options' => $MJTC_padZeros],
                    ['id' => 'suffix_ticketid', 'label' => __('Ticket ID Suffix', 'majestic-support'), 'type' => 'text', 'value' => majesticsupport::$_data[0]['suffix_ticketid'], 'tooltip' => __('Set the suffix for your custom ticket ID', 'majestic-support')],
                    ['id' => 'maximum_tickets', 'label' => __('Maximum tickets', 'majestic-support'), 'type' => 'text', 'value' => majesticsupport::$_data[0]['maximum_tickets'], 'tooltip' => __('Maximum ticket per user', 'majestic-support'), 'pro' => ['slug' => 'maxticket', 'name' => __('Max Ticket', 'majestic-support')]],
                    ['id' => 'maximum_open_tickets', 'label' => __('Maximum open tickets', 'majestic-support'), 'type' => 'text', 'value' => majesticsupport::$_data[0]['maximum_open_tickets'], 'tooltip' => __('Maximum number of tickets opened per user', 'majestic-support'), 'pro' => ['slug' => 'maxticket', 'name' => __('Max Ticket', 'majestic-support')]],
                    ['id' => 'reopen_ticket_within_days', 'label' => __('Reopen ticket within days', 'majestic-support'), 'type' => 'text', 'value' => majesticsupport::$_data[0]['reopen_ticket_within_days'], 'tooltip' => __('The ticket will reopen within the given number of days', 'majestic-support')],
                    ['id' => 'show_multiform_popup', 'label' => __('Multiforms Popup For New Tickets', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['show_multiform_popup'], 'tooltip' => __('Show or hide the multiform popup when creating a new ticket', 'majestic-support'), 'options' => $MJTC_showhide, 'pro' => ['slug' => 'multiform', 'name' => __('Multiform', 'majestic-support')]],
                    ['id' => 'print_ticket_user', 'label' => __('User can print ticket', 'majestic-support'), 'type' => 'select', 'value' => majesticsupport::$_data[0]['print_ticket_user'], 'tooltip' => __('Can users print a ticket from the ticket detail or not?', 'majestic-support'), 'options' => $MJTC_yesno, 'pro' => ['slug' => 'actions', 'name' => __('Actions', 'majestic-support')]],
                    ['id' => 'reply_to_closed_ticket', 'label' => __('Allow Users To Reply via Email On Closed Ticket', 'majestic-support'), 'type' => 'select', 'value' => majesticsupport::$_data[0]['reply_to_closed_ticket'], 'tooltip' => __('Select whether users can reply to closed email piping tickets', 'majestic-support'), 'options' => $MJTC_yesno],
                    ['id' => 'show_email_on_ticket_reply', 'label' => __('Show Admin OR Agent Email On Ticket Reply', 'majestic-support'), 'type' => 'select', 'value' => majesticsupport::$_data[0]['show_email_on_ticket_reply'], 'tooltip' => __('Select whether users can see the email of administrator or agent on the ticket reply', 'majestic-support'), 'options' => $MJTC_yesno],
                    ['id' => 'ticket_replies_ordering', 'label' => __('Ticket Replies ordering', 'majestic-support'), 'type' => 'select', 'value' => majesticsupport::$_data[0]['ticket_replies_ordering'], 'tooltip' => __('Set the default ordering for ticket replies in the detail page.', 'majestic-support'), 'options' => $MJTC_repliesordering],
                    ['id' => 'anonymous_name_on_ticket_reply', 'label' => __('Show Anonymous Name On Ticket Reply', 'majestic-support'), 'type' => 'select', 'value' => majesticsupport::$_data[0]['anonymous_name_on_ticket_reply'], 'tooltip' => __('Select whether users can see the name of administrator or agent on ticket reply', 'majestic-support'), 'options' => $MJTC_yesno],
                    ['id' => 'show_read_receipt_to_admin_on_reply', 'label' => __('Show Message Read Icon for Admin On Ticket Detail', 'majestic-support'), 'type' => 'select', 'value' => majesticsupport::$_data[0]['show_read_receipt_to_admin_on_reply'], 'tooltip' => __('Select whether the message read icon is displayed to the administrator', 'majestic-support'), 'options' => $MJTC_yesno],
                    ['id' => 'show_read_receipt_to_agent_on_reply', 'label' => __('Show Message Read Icon For Agents on Ticket Detail', 'majestic-support'), 'type' => 'select', 'value' => majesticsupport::$_data[0]['show_read_receipt_to_agent_on_reply'], 'tooltip' => __('Select whether the message read icon is displayed to agents', 'majestic-support'), 'options' => $MJTC_yesno],
                    ['id' => 'show_read_receipt_to_user_on_reply', 'label' => __('Show Message Read Icon For Users On Ticket Detail', 'majestic-support'), 'type' => 'select', 'value' => majesticsupport::$_data[0]['show_read_receipt_to_user_on_reply'], 'tooltip' => __('Select whether the message read icon is displayed to users', 'majestic-support'), 'options' => $MJTC_yesno],
                    ['id' => 'ticket_auto_close', 'label' => __('Ticket Auto Close', 'majestic-support'), 'type' => 'text', 'value' => majesticsupport::$_data[0]['ticket_auto_close'], 'tooltip' => __('Ticket auto-close if user does not respond within given days', 'majestic-support'), 'video' => '2iA8SuNLmMI', 'pro' => ['slug' => 'autoclose', 'name' => __('Auto Close', 'majestic-support')]],
                    ['id' => 'show_ticket_delete_button', 'label' => __('Show ticket delete button', 'majestic-support'), 'type' => 'select', 'value' => majesticsupport::$_data[0]['show_ticket_delete_button'], 'tooltip' => __('Select whether users can see the ticket delete button', 'majestic-support'), 'options' => $MJTC_yesno],
                    ['id' => 'ticket_close_reason_type', 'label' => __('Ticket close reason Type', 'majestic-support'), 'type' => 'select', 'value' => majesticsupport::$_data[0]['ticket_close_reason_type'], 'tooltip' => __('Select whether users can save single or multiple reasons', 'majestic-support'), 'options' => $MJTC_reasontype, 'pro' => ['slug' => 'ticketclosereason', 'name' => __('Ticket Close Reason', 'majestic-support')]],
                    ['id' => 'maximum_record_for_smart_reply', 'label' => __('Maximum Record For Smart Reply', 'majestic-support'), 'type' => 'select', 'value' => majesticsupport::$_data[0]['maximum_record_for_smart_reply'], 'tooltip' => __('Set the number of replies to show', 'majestic-support'), 'options' => $MJTC_smartreply],
                    ['id' => 'enable_instant_fixes', 'label' => __('Enable Instant Fixes', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['enable_instant_fixes'] ?? '1', 'tooltip' => __('Enable or disable the instant fix suggestions on the ticket submission page.', 'majestic-support')],
                    ['id' => 'instant_fixes_min_score', 'label' => __('Instant Fixes Minimum Score', 'majestic-support'), 'type' => 'text', 'value' => majesticsupport::$_data[0]['instant_fixes_min_score'], 'tooltip' => __('Set the minimum NLP relevance score required to show an Instant Fix. Default is 0.5. Lower numbers show more results; higher numbers demand stricter matches.', 'majestic-support')],
                    ['id' => 'instant_fixes_limit', 'label' => __('Instant Fixes Record Limit', 'majestic-support'), 'type' => 'text', 'value' => majesticsupport::$_data[0]['instant_fixes_limit'], 'tooltip' => __('Set the maximum number of matches to pull from each source (Knowledge Base, FAQs, etc.) for the Instant Fixes section.', 'majestic-support')],
                    ['id' => 'auto_delete_attachments_interval', 'label'   => __('Auto-Delete Old Attachments', 'majestic-support'), 'type'    => 'select', 'value'   => isset(majesticsupport::$_data[0]['auto_delete_attachments_interval']) ? majesticsupport::$_data[0]['auto_delete_attachments_interval'] : '0', 'tooltip' => __('Automatically delete physical attachment files from tickets that have been closed longer than this period to save server disk space.', 'majestic-support'), 'options' => $MJTC_delete_intervals],
                    ['id' => 'new_ticket_message', 'label' => __('New ticket message', 'majestic-support'), 'type' => 'wp_editor', 'value' => majesticsupport::$_data[0]['new_ticket_message'], 'tooltip' => __('This message will show on the new ticket', 'majestic-support')],
                ]
            ],
            'ticket_listing' => [
                'title'       => __('Ticket Listing', 'majestic-support'),
                'description' => __('Manage how tickets are sorted, ordered, and what information is visible in lists', 'majestic-support'),
                'fields'      => [
                    ['id' => 'tickets_ordering', 'label' => __('Ticket listing ordering', 'majestic-support'), 'type' => 'select', 'value' => majesticsupport::$_data[0]['tickets_ordering'], 'tooltip' => __('Set default ordering for ticket listing', 'majestic-support'), 'options' => $MJTC_ticketordering],
                    ['id' => 'tickets_sorting', 'label' => __('Ticket listing sorting', 'majestic-support'), 'type' => 'select', 'value' => majesticsupport::$_data[0]['tickets_sorting'], 'tooltip' => __('Set default sorting for ticket listing', 'majestic-support'), 'options' => $MJTC_ticketsorting],
                    ['id' => 'show_closedby_on_admin_tickets', 'label' => __('Closed info. on admin closed tickets', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['show_closedby_on_admin_tickets'], 'tooltip' => __('Allows admin to see who closed the ticket and when', 'majestic-support'), 'options' => $MJTC_showhide],
                    ['id' => 'show_closedby_on_agent_tickets', 'label' => __('Closed info. on agent closed tickets', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['show_closedby_on_agent_tickets'], 'tooltip' => __('Allows agent to see who closed the ticket and when', 'majestic-support'), 'options' => $MJTC_showhide],
                    ['id' => 'show_closedby_on_user_tickets', 'label' => __('Closed info. on user closed tickets', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['show_closedby_on_user_tickets'], 'tooltip' => __('Allows user to see who closed the ticket and when', 'majestic-support'), 'options' => $MJTC_showhide],
                    ['id' => 'show_assignto_on_admin_tickets', 'label' => __('Assigned info. on admin tickets', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['show_assignto_on_admin_tickets'], 'tooltip' => __('Allows admin to see to whom the ticket has been assigned', 'majestic-support'), 'options' => $MJTC_showhide, 'pro' => ['slug' => 'agent', 'name' => __('Agent', 'majestic-support')]],
                    ['id' => 'show_assignto_on_agent_tickets', 'label' => __('Assigned info. on agent tickets', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['show_assignto_on_agent_tickets'], 'tooltip' => __('Allows agent to see to whom the ticket has been assigned', 'majestic-support'), 'options' => $MJTC_showhide, 'pro' => ['slug' => 'agent', 'name' => __('Agent', 'majestic-support')]],
                    ['id' => 'show_assignto_on_user_tickets', 'label' => __('Assigned info. on user tickets', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['show_assignto_on_user_tickets'], 'tooltip' => __('Allows user to see to whom the ticket has been assigned', 'majestic-support'), 'options' => $MJTC_showhide, 'pro' => ['slug' => 'agent', 'name' => __('Agent', 'majestic-support')]],
                ]
            ],
            'visitor_ticket_setting' => [
                'title'       => __('Visitor Ticket Setting', 'majestic-support'),
                'description' => __('Guest access and messaging configuration', 'majestic-support'),
                'fields'      => [
                    ['id' => 'visitor_can_create_ticket', 'label' => __('Visitors can create tickets', 'majestic-support'), 'type' => 'select', 'value' => majesticsupport::$_data[0]['visitor_can_create_ticket'], 'tooltip' => __('Allow visitors to create tickets or not', 'majestic-support'), 'options' => $MJTC_yesno, 'video' => '9NvBOu_ojMo'],
                    ['id' => 'visitor_message', 'label' => __('Visitor ticket creation message', 'majestic-support'), 'type' => 'wp_editor', 'value' => majesticsupport::$_data[0]['visitor_message'], 'tooltip' => __('This text will appear whenever a visitor creates a ticket', 'majestic-support')],
                ]
            ],
        ]
    ],
    'system_emails' => [
        'label'  => __('System Emails', 'majestic-support'),
        'icon'   => 'mail', // Based on your SVG envelope icon
        'groups' => [
            'system_email' => [
                'title'       => __('System Emails', 'majestic-support'),
                'description' => __('Configure default email addresses for system alerts and administrative notifications', 'majestic-support'),
                'fields'      => [
                    [
                        'id'          => 'default_alert_email', 
                        'label'       => __('Default alert email', 'majestic-support'), 
                        'type'        => 'select', 
                        'value'       => majesticsupport::$_data[0]['default_alert_email'], 
                        'tooltip'     => __('If the ticket department email is not selected, then this email is used to send emails', 'majestic-support'), 
                        'options'     => majesticsupport::$_data[1], // Assuming this contains the list of available emails
                        'action_btn'  => __('Add New Email', 'majestic-support')
                    ],
                    [
                        'id'          => 'default_admin_email', 
                        'label'       => __('Default admin email', 'majestic-support'), 
                        'type'        => 'select', 
                        'value'       => majesticsupport::$_data[0]['default_admin_email'], 
                        'tooltip'     => __('Admin email address to receive emails', 'majestic-support'), 
                        'options'     => majesticsupport::$_data[1],
                        'action_btn'  => __('Add New Email', 'majestic-support')
                    ],
                ]
            ],
        ]
    ],
    'captcha' => [
        'label'  => __('Captcha', 'majestic-support'),
        'icon'   => 'shield', // Based on your SVG shield icon
        'groups' => [
            'captcha_general' => [
                'title'       => __('Captcha Setting', 'majestic-support'),
                'description' => __('Basic security settings for registration and visitor forms', 'majestic-support'),
                'fields'      => [
                    ['id' => 'captcha_on_registration', 'label' => __('Show captcha on registration form', 'majestic-support'), 'type' => 'select', 'value' => majesticsupport::$_data[0]['captcha_on_registration'], 'tooltip' => __('Select whether you want to show captcha on the registration form or not', 'majestic-support'), 'options' => $MJTC_yesno],
                    ['id' => 'show_captcha_on_visitor_from_ticket', 'label' => __('Show captcha on the visitor ticket form', 'majestic-support'), 'type' => 'select', 'value' => majesticsupport::$_data[0]['show_captcha_on_visitor_from_ticket'], 'tooltip' => __('Show captcha when a visitor wants to create a ticket', 'majestic-support'), 'options' => $MJTC_yesno, 'video' => ''],
                    ['id' => 'captcha_selection', 'label' => __('Captcha selection', 'majestic-support'), 'type' => 'select', 'value' => majesticsupport::$_data[0]['captcha_selection'], 'tooltip' => __('Which captcha do you want to add?', 'majestic-support'), 'options' => $MJTC_captchaselection, 'video' => ''],
                ],
                'pro' => ['slug' => 'useroptions', 'name' => __('User Options', 'majestic-support')]
            ],
            'google_recaptcha' => [
                'title'       => __('Google reCAPTCHA', 'majestic-support'),
                'description' => __('Configure Google reCAPTCHA API keys and version', 'majestic-support'),
                'fields'      => [
                    ['id' => 'recaptcha_version', 'label' => __('Google reCAPTCHA version', 'majestic-support'), 'type' => 'select', 'value' => majesticsupport::$_data[0]['recaptcha_version'], 'tooltip' => __('Select the Google reCAPTCHA version', 'majestic-support'), 'options' => $MJTC_recaptcha_version],
                    ['id' => 'recaptcha_publickey', 'label' => __('Google reCAPTCHA site key', 'majestic-support'), 'type' => 'text', 'value' => majesticsupport::$_data[0]['recaptcha_publickey'], 'tooltip' => __('Please enter the site key for Google reCAPTCHA from', 'majestic-support').' https://www.google.com/recaptcha/admin'],
                    ['id' => 'recaptcha_privatekey', 'label' => __('Google reCAPTCHA secret key', 'majestic-support'), 'type' => 'text', 'value' => majesticsupport::$_data[0]['recaptcha_privatekey'], 'tooltip' => __('Please enter the secret key for Google reCAPTCHA from', 'majestic-support').' https://www.google.com/recaptcha/admin'],
                ]
            ],
            'own_captcha' => [
                'title'       => __('Own Captcha', 'majestic-support'),
                'description' => __('Custom math-based captcha configuration', 'majestic-support'),
                'fields'      => [
                    ['id' => 'owncaptcha_calculationtype', 'label' => __('Own captcha calculation type', 'majestic-support'), 'type' => 'select', 'value' => majesticsupport::$_data[0]['owncaptcha_calculationtype'], 'tooltip' => __('Select the calculation type (addition or subtraction)', 'majestic-support'), 'options' => $MJTC_owncaptchatype],
                    ['id' => 'owncaptcha_totaloperand', 'label' => __('Own captcha operands', 'majestic-support'), 'type' => 'select', 'value' => majesticsupport::$_data[0]['owncaptcha_totaloperand'], 'tooltip' => __('Select the total number of operands to be given', 'majestic-support'), 'options' => $MJTC_owncaptchaoparend],
                    ['id' => 'owncaptcha_subtractionans', 'label' => __('Positive Answer Upon Own Captcha Subtraction', 'majestic-support'), 'type' => 'select', 'value' => majesticsupport::$_data[0]['owncaptcha_subtractionans'], 'tooltip' => __('Selecting Yes ensures that the result of the subtraction will always be positive', 'majestic-support'), 'options' => $MJTC_yesno],
                ]
            ],
        ]
    ],
    'email_settings' => [
        'label'  => __('Email Settings', 'majestic-support'),
        'icon'   => 'mail-open', // Based on your SVG mail-item icon
        'groups' => [
            'banned_email_alerts' => [
                'title'       => __('Email Regarding Banned Users', 'majestic-support'),
                'description' => __('Manage notifications when banned accounts attempt to interact with the system', 'majestic-support'),
                'fields'      => [
                    ['id' => 'banemail_mail_to_admin', 'label' => __('Mail to admin', 'majestic-support'), 'type' => 'select', 'value' => majesticsupport::$_data[0]['banemail_mail_to_admin'], 'tooltip' => __('Send an email to the admin when a banned email tries to create a ticket', 'majestic-support'), 'options' => $MJTC_enableddisabled, 'pro' => ['slug' => 'banemail', 'name' => __('Ban Email', 'majestic-support')]]
                ]
            ],
            'ticket_operations_emails' => [
                'title'       => __('Ticket Operations Email Setting', 'majestic-support'),
                'description' => __('Configure who receives notifications for specific ticket lifecycle events (Admin, Agent, and User)', 'majestic-support'),
                'fields'      => [
                    // Note: These use 'type' => 'multi_toggle' to represent your matrix row
                    [
                        'id'      => 'new_ticket',
                        'label'   => __('New ticket', 'majestic-support'),
                        'type'    => 'multi_toggle',
                        'sub_fields' => [
                            'admin' => ['id' => 'new_ticket_mail_to_admin', 'value' => majesticsupport::$_data[0]['new_ticket_mail_to_admin']],
                            'agent' => ['id' => 'new_ticket_mail_to_staff_members', 'value' => majesticsupport::$_data[0]['new_ticket_mail_to_staff_members'], 'pro' => ['slug' => 'agent', 'name' => __('Agent', 'majestic-support')]],
                            'user'  => ['id' => null, 'value' => null] // Not available in your HTML
                        ]
                    ],
                    [
                        'id'      => 'ticket_reassign',
                        'label'   => __('Ticket reassign', 'majestic-support'),
                        'type'    => 'multi_toggle',
                        'sub_fields' => [
                            'admin' => ['id' => 'ticket_reassign_admin', 'value' => majesticsupport::$_data[0]['ticket_reassign_admin']],
                            'agent' => ['id' => 'ticket_reassign_staff', 'value' => majesticsupport::$_data[0]['ticket_reassign_staff'], 'pro' => ['slug' => 'agent', 'name' => __('Agent', 'majestic-support')]],
                            'user'  => ['id' => 'ticket_reassign_user', 'value' => majesticsupport::$_data[0]['ticket_reassign_user']]
                        ]
                    ],
                    [
                        'id'      => 'ticket_close',
                        'label'   => __('Ticket close', 'majestic-support'),
                        'type'    => 'multi_toggle',
                        'sub_fields' => [
                            'admin' => ['id' => 'ticket_close_admin', 'value' => majesticsupport::$_data[0]['ticket_close_admin']],
                            'agent' => ['id' => 'ticket_close_staff', 'value' => majesticsupport::$_data[0]['ticket_close_staff'], 'pro' => ['slug' => 'agent', 'name' => __('Agent', 'majestic-support')]],
                            'user'  => ['id' => 'ticket_close_user', 'value' => majesticsupport::$_data[0]['ticket_close_user']]
                        ]
                    ],
                    [
                        'id'      => 'ticket_delete',
                        'label'   => __('Ticket delete', 'majestic-support'),
                        'type'    => 'multi_toggle',
                        'sub_fields' => [
                            'admin' => ['id' => 'ticket_delete_admin', 'value' => majesticsupport::$_data[0]['ticket_delete_admin']],
                            'agent' => ['id' => 'ticket_delete_staff', 'value' => majesticsupport::$_data[0]['ticket_delete_staff'], 'pro' => ['slug' => 'agent', 'name' => __('Agent', 'majestic-support')]],
                            'user'  => ['id' => 'ticket_delete_user', 'value' => majesticsupport::$_data[0]['ticket_delete_user']]
                        ]
                    ],
                    [
                        'id'      => 'ticket_mark_overdue',
                        'label'   => __('Ticket marked as overdue', 'majestic-support'),
                        'type'    => 'multi_toggle',
                        'sub_fields' => [
                            'admin' => ['id' => 'ticket_mark_overdue_admin', 'value' => majesticsupport::$_data[0]['ticket_mark_overdue_admin']],
                            'agent' => ['id' => 'ticket_mark_overdue_staff', 'value' => majesticsupport::$_data[0]['ticket_mark_overdue_staff']],
                            'user'  => ['id' => 'ticket_mark_overdue_user', 'value' => majesticsupport::$_data[0]['ticket_mark_overdue_user']]
                        ],
                        'pro' => ['slug' => 'overdue', 'name' => __('Overdue', 'majestic-support')]
                    ],
                    [
                        'id'      => 'ticket_ban_email',
                        'label'   => __('Ticket ban email', 'majestic-support'),
                        'type'    => 'multi_toggle',
                        'sub_fields' => [
                            'admin' => ['id' => 'ticket_ban_email_admin', 'value' => majesticsupport::$_data[0]['ticket_ban_email_admin']],
                            'agent' => ['id' => 'ticket_ban_email_staff', 'value' => majesticsupport::$_data[0]['ticket_ban_email_staff']],
                            'user'  => ['id' => 'ticket_ban_email_user', 'value' => majesticsupport::$_data[0]['ticket_ban_email_user']]
                        ],
                        'pro' => ['slug' => 'banemail', 'name' => __('Ban Email', 'majestic-support')]
                    ],
                    [
                        'id'      => 'ticket_unban_email',
                        'label'   => __('Ticket unban email', 'majestic-support'),
                        'type'    => 'multi_toggle',
                        'sub_fields' => [
                            'admin' => ['id' => 'unban_email_admin', 'value' => majesticsupport::$_data[0]['unban_email_admin']],
                            'agent' => ['id' => 'unban_email_staff', 'value' => majesticsupport::$_data[0]['unban_email_staff']],
                            'user'  => ['id' => 'unban_email_user', 'value' => majesticsupport::$_data[0]['unban_email_user']]
                        ],
                        'pro' => ['slug' => 'banemail', 'name' => __('Ban Email', 'majestic-support')]
                    ],
                    [
                        'id'      => 'ticket_dept_transfer',
                        'label'   => __('Ticket department transfer', 'majestic-support'),
                        'type'    => 'multi_toggle',
                        'sub_fields' => [
                            'admin' => ['id' => 'ticket_department_transfer_admin', 'value' => majesticsupport::$_data[0]['ticket_department_transfer_admin']],
                            'agent' => ['id' => 'ticket_department_transfer_staff', 'value' => majesticsupport::$_data[0]['ticket_department_transfer_staff'], 'pro' => ['slug' => 'agent', 'name' => __('Agent', 'majestic-support')]],
                            'user'  => ['id' => 'ticket_department_transfer_user', 'value' => majesticsupport::$_data[0]['ticket_department_transfer_user']]
                        ]
                    ],
                    [
                        'id'      => 'ticket_reply_user',
                        'label'   => __('Ticket reply User', 'majestic-support'),
                        'type'    => 'multi_toggle',
                        'sub_fields' => [
                            'admin' => ['id' => 'ticket_reply_ticket_user_admin', 'value' => majesticsupport::$_data[0]['ticket_reply_ticket_user_admin']],
                            'agent' => ['id' => 'ticket_reply_ticket_user_staff', 'value' => majesticsupport::$_data[0]['ticket_reply_ticket_user_staff'], 'pro' => ['slug' => 'agent', 'name' => __('Agent', 'majestic-support')]],
                            'user'  => ['id' => 'ticket_reply_ticket_user_user', 'value' => majesticsupport::$_data[0]['ticket_reply_ticket_user_user']]
                        ]
                    ],
                    [
                        'id'      => 'ticket_response_agent',
                        'label'   => __('Ticket Response Agent', 'majestic-support'),
                        'type'    => 'multi_toggle',
                        'sub_fields' => [
                            'admin' => ['id' => 'ticket_response_to_staff_admin', 'value' => majesticsupport::$_data[0]['ticket_response_to_staff_admin']],
                            'agent' => ['id' => 'ticket_response_to_staff_staff', 'value' => majesticsupport::$_data[0]['ticket_response_to_staff_staff'], 'pro' => ['slug' => 'agent', 'name' => __('Agent', 'majestic-support')]],
                            'user'  => ['id' => 'ticket_response_to_staff_user', 'value' => majesticsupport::$_data[0]['ticket_response_to_staff_user']]
                        ]
                    ],
                    [
                        'id'      => 'ticket_lock',
                        'label'   => __('Ticket lock', 'majestic-support'),
                        'type'    => 'multi_toggle',
                        'sub_fields' => [
                            'admin' => ['id' => 'ticket_lock_admin', 'value' => majesticsupport::$_data[0]['ticket_lock_admin']],
                            'agent' => ['id' => 'ticket_lock_staff', 'value' => majesticsupport::$_data[0]['ticket_lock_staff'], 'pro' => ['slug' => 'agent', 'name' => __('Agent', 'majestic-support')]],
                            'user'  => ['id' => 'ticket_lock_user', 'value' => majesticsupport::$_data[0]['ticket_lock_user']]
                        ],
                        'pro' => ['slug' => 'actions', 'name' => __('Actions', 'majestic-support')]
                    ],
                    [
                        'id'      => 'ticket_unlock',
                        'label'   => __('Ticket unlock', 'majestic-support'),
                        'type'    => 'multi_toggle',
                        'sub_fields' => [
                            'admin' => ['id' => 'ticket_unlock_admin', 'value' => majesticsupport::$_data[0]['ticket_unlock_admin']],
                            'agent' => ['id' => 'ticket_unlock_staff', 'value' => majesticsupport::$_data[0]['ticket_unlock_staff']],
                            'user'  => ['id' => 'ticket_unlock_user', 'value' => majesticsupport::$_data[0]['ticket_unlock_user']]
                        ],
                        'pro' => ['slug' => 'actions', 'name' => __('Actions', 'majestic-support')]
                    ],
                    [
                        'id'      => 'ticket_priority_change',
                        'label'   => __('Ticket Change Priority', 'majestic-support'),
                        'type'    => 'multi_toggle',
                        'sub_fields' => [
                            'admin' => ['id' => 'ticket_priority_admin', 'value' => majesticsupport::$_data[0]['ticket_priority_admin']],
                            'agent' => ['id' => 'ticket_priority_staff', 'value' => majesticsupport::$_data[0]['ticket_priority_staff']],
                            'user'  => ['id' => 'ticket_priority_user', 'value' => majesticsupport::$_data[0]['ticket_priority_user']]
                        ],
                        'pro' => ['slug' => 'agent', 'name' => __('Agent', 'majestic-support')]
                    ],
                    [
                        'id'      => 'ticket_feedback',
                        'label'   => __('Send Feedback Email To User', 'majestic-support'),
                        'type'    => 'multi_toggle',
                        'sub_fields' => [
                            'admin' => ['id' => null, 'value' => null],
                            'agent' => ['id' => null, 'value' => null],
                            'user'  => ['id' => 'ticket_feedback_user', 'value' => majesticsupport::$_data[0]['ticket_feedback_user']]
                        ],
                        'pro' => ['slug' => 'feedback', 'name' => __('Feedback', 'majestic-support')]
                    ],
                ]
            ],
        ]
    ],
    'agent_settings' => [
        'label'  => __('Agent Settings', 'majestic-support'),
        'icon'   => 'user-check',
        'groups' => [
            'dashboard_links' => [
                'title'       => __('Dashboard Links', 'majestic-support'),
                'description' => __('Toggle which features and modules are visible to agents on their primary control panel', 'majestic-support'),
                'pro'         => ['slug' => 'agent', 'name' => __('Agent', 'majestic-support')],
                'fields'      => [
                    ['id' => 'cplink_openticket_staff', 'label' => __('Submit Ticket', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['cplink_openticket_staff'], 'options' => $MJTC_showhide],
                    ['id' => 'cplink_myticket_staff', 'label' => __('My Tickets', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['cplink_myticket_staff'], 'options' => $MJTC_showhide],
                    ['id' => 'cplink_ticketclosereasons_staff', 'label' => __('Ticket Close Reasons', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['cplink_ticketclosereasons_staff'], 'options' => $MJTC_showhide],
                    ['id' => 'cplink_smartreply_staff', 'label' => __('Smart Reply', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['cplink_smartreply_staff'], 'options' => $MJTC_showhide],
                    ['id' => 'cplink_staff_staff', 'label' => __('Agents', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['cplink_staff_staff'], 'options' => $MJTC_showhide],
                    ['id' => 'cplink_roles_staff', 'label' => __('Agent Roles', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['cplink_roles_staff'], 'options' => $MJTC_showhide],
                    ['id' => 'cplink_department_staff', 'label' => __('Departments', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['cplink_department_staff'], 'options' => $MJTC_showhide],
                    ['id' => 'cplink_category_staff', 'label' => __('Categories', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['cplink_category_staff'], 'options' => $MJTC_showhide, 'pro' => ['slug' => 'knowledgebase', 'name' => __('Knowledgebase', 'majestic-support')]],
                    ['id' => 'cplink_kbarticle_staff', 'label' => __('Knowledge Base', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['cplink_kbarticle_staff'], 'options' => $MJTC_showhide, 'pro' => ['slug' => 'knowledgebase', 'name' => __('Knowledgebase', 'majestic-support')]],
                    ['id' => 'cplink_download_staff', 'label' => __('Downloads', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['cplink_download_staff'], 'options' => $MJTC_showhide, 'pro' => ['slug' => 'download', 'name' => __('Download', 'majestic-support')]],
                    ['id' => 'cplink_announcement_staff', 'label' => __('Announcements', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['cplink_announcement_staff'], 'options' => $MJTC_showhide, 'pro' => ['slug' => 'announcement', 'name' => __('Announcement', 'majestic-support')]],
                    ['id' => 'cplink_faq_staff', 'label' => __('FAQs', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['cplink_faq_staff'], 'options' => $MJTC_showhide, 'pro' => ['slug' => 'faq', 'name' => __('FAQ', 'majestic-support')]],
                    ['id' => 'cplink_helptopic_agent', 'label' => __('Help Topics', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['cplink_helptopic_agent'], 'options' => $MJTC_showhide, 'pro' => ['slug' => 'helptopic', 'name' => __('Help Topic', 'majestic-support')]],
                    ['id' => 'cplink_cannedresponses_agent', 'label' => __('Premade Responses', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['cplink_cannedresponses_agent'], 'options' => $MJTC_showhide, 'pro' => ['slug' => 'cannedresponses', 'name' => __('Canned Responses', 'majestic-support')]],
                    ['id' => 'cplink_mail_staff', 'label' => __('Mail', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['cplink_mail_staff'], 'options' => $MJTC_showhide, 'pro' => ['slug' => 'mail', 'name' => __('Mail', 'majestic-support')]],
                    ['id' => 'cplink_banemail_staff', 'label' => __('Banned Emails', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['cplink_banemail_staff'], 'options' => $MJTC_showhide, 'pro' => ['slug' => 'banemail', 'name' => __('Ban Email', 'majestic-support')]],
                    ['id' => 'cplink_staff_report_staff', 'label' => __('Agent Reports', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['cplink_staff_report_staff'], 'options' => $MJTC_showhide],
                    ['id' => 'cplink_department_report_staff', 'label' => __('Department Reports', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['cplink_department_report_staff'], 'options' => $MJTC_showhide],
                    ['id' => 'cplink_feedback_staff', 'label' => __('Agent Feedback', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['cplink_feedback_staff'], 'options' => $MJTC_showhide, 'pro' => ['slug' => 'feedback', 'name' => __('Feedback', 'majestic-support')]],
                    ['id' => 'cplink_myprofile_staff', 'label' => __('My Profile', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['cplink_myprofile_staff'], 'options' => $MJTC_showhide],
                    ['id' => 'cplink_erasedata_staff', 'label' => __('User Data', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['cplink_erasedata_staff'], 'options' => $MJTC_showhide],
                    ['id' => 'cplink_export_ticket_staff', 'label' => __('Export Ticket', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['cplink_export_ticket_staff'], 'options' => $MJTC_showhide, 'pro' => ['slug' => 'export', 'name' => __('Export', 'majestic-support')]],
                    ['id' => 'cplink_login_logout_staff', 'label' => __('Login / Logout Button', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['cplink_login_logout_staff'], 'options' => $MJTC_showhide],
                    ['id' => 'cplink_totalcount_staff', 'label' => __('Ticket Total Count', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['cplink_totalcount_staff'], 'options' => $MJTC_showhide],
                    ['id' => 'cplink_latesttickets_staff', 'label' => __('Recent Active Tickets', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['cplink_latesttickets_staff'], 'options' => $MJTC_showhide],
                    ['id' => 'cplink_quick_actions_staff', 'label' => __('Quick Actions', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['cplink_quick_actions_staff'], 'options' => $MJTC_showhide],
                    ['id' => 'cplink_ticket_departments_staff', 'label' => __('Ticket Departments', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['cplink_ticket_departments_staff'], 'options' => $MJTC_showhide],
                    ['id' => 'cplink_recent_feedback_staff', 'label' => __('Recent Feedback', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['cplink_recent_feedback_staff'], 'options' => $MJTC_showhide, 'pro' => ['slug' => 'feedback', 'name' => __('Feedback', 'majestic-support')]],
                    ['id' => 'cplink_recent_activity_staff', 'label' => __('Recent Activity', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['cplink_recent_activity_staff'], 'options' => $MJTC_showhide],
                    ['id' => 'cplink_weekly_leaderboard_staff', 'label' => __('Weekly Leaderboard', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['cplink_weekly_leaderboard_staff'], 'options' => $MJTC_showhide],
                    ['id' => 'cplink_vip_clients_watchlist_staff', 'label' => __('VIP Clients Watchlist', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['cplink_vip_clients_watchlist_staff'], 'options' => $MJTC_showhide, 'pro' => ['slug' => 'woocommerce', 'name' => __('WooCommerce', 'majestic-support')]],
                    ['id' => 'cplink_daily_velocity_staff', 'label' => __('Daily Velocity', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['cplink_daily_velocity_staff'], 'options' => $MJTC_showhide],
                    ['id' => 'cplink_latestkb_staff', 'label' => __('Knowledge Base', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['cplink_latestkb_staff'], 'options' => $MJTC_showhide],
                    ['id' => 'cplink_latestdownloads_staff', 'label' => __('Downloads', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['cplink_latestdownloads_staff'], 'options' => $MJTC_showhide],
                    ['id' => 'cplink_latestannouncements_staff', 'label' => __('Announcements', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['cplink_latestannouncements_staff'], 'options' => $MJTC_showhide],
                    ['id' => 'cplink_latestfaqs_staff', 'label' => __('FAQs', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['cplink_latestfaqs_staff'], 'options' => $MJTC_showhide],
                ]
            ],
            'top_menu_links' => [
                'title'       => __('Top Menu Links', 'majestic-support'),
                'description' => __('Configure visibility of quick links in the top navigation bar for agents', 'majestic-support'),
                'pro'         => ['slug' => 'agent', 'name' => __('Agent', 'majestic-support')],
                'fields'      => [
                    ['id' => 'tplink_profile_staff', 'label' => __('Profile', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['tplink_profile_staff'], 'options' => $MJTC_showhide],
                    ['id' => 'tplink_home_staff', 'label' => __('Dashboard', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['tplink_home_staff'], 'options' => $MJTC_showhide],
                    ['id' => 'tplink_openticket_staff', 'label' => __('Submit Ticket', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['tplink_openticket_staff'], 'options' => $MJTC_showhide],
                    ['id' => 'tplink_tickets_staff', 'label' => __('My Tickets', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['tplink_tickets_staff'], 'options' => $MJTC_showhide],
                    ['id' => 'tplink_login_logout_staff', 'label' => __('Login / Logout Button', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['tplink_login_logout_staff'], 'options' => $MJTC_showhide],
                ],
            ],
        ]
    ],
    'user_settings' => [
        'label'  => __('User Settings', 'majestic-support'),
        'icon'   => 'users',
        'groups' => [
            'dashboard_links_user' => [
                'title'       => __('Dashboard Links', 'majestic-support'),
                'description' => __('Toggle which features and modules are visible to customers on their front-end dashboard', 'majestic-support'),
                'fields'      => [
                    ['id' => 'cplink_openticket_user', 'label' => __('Submit Ticket', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['cplink_openticket_user'], 'options' => $MJTC_showhide],
                    ['id' => 'cplink_myticket_user', 'label' => __('My Tickets', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['cplink_myticket_user'], 'options' => $MJTC_showhide],
                    ['id' => 'cplink_checkticketstatus_user', 'label' => __('Ticket Status', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['cplink_checkticketstatus_user'], 'options' => $MJTC_showhide],
                    ['id' => 'cplink_downloads_user', 'label' => __('Downloads', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['cplink_downloads_user'], 'options' => $MJTC_showhide, 'pro' => ['slug' => 'download', 'name' => __('Download', 'majestic-support')] ],
                    ['id' => 'cplink_announcements_user', 'label' => __('Announcements', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['cplink_announcements_user'], 'options' => $MJTC_showhide, 'pro' => ['slug' => 'announcement', 'name' => __('Announcement', 'majestic-support')]],
                    ['id' => 'cplink_faqs_user', 'label' => __('FAQs', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['cplink_faqs_user'], 'options' => $MJTC_showhide, 'pro' => ['slug' => 'faq', 'name' => __('FAQs', 'majestic-support')]],
                    ['id' => 'cplink_knowledgebase_user', 'label' => __('Knowledge Base', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['cplink_knowledgebase_user'], 'options' => $MJTC_showhide, 'pro' => ['slug' => 'knowledgebase', 'name' => __('Knowledgebase', 'majestic-support')]],
                    ['id' => 'cplink_erasedata_user', 'label' => __('Erase User Data', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['cplink_erasedata_user'], 'options' => $MJTC_showhide],
                    ['id' => 'cplink_login_logout_user', 'label' => __('Login / Logout Button', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['cplink_login_logout_user'], 'options' => $MJTC_showhide],
                    ['id' => 'cplink_register_user', 'label' => __('Registration', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['cplink_register_user'], 'options' => $MJTC_showhide],
                    ['id' => 'cplink_totalcount_user', 'label' => __('Ticket Total Count', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['cplink_totalcount_user'], 'options' => $MJTC_showhide],
                    ['id' => 'cplink_latesttickets_user', 'label' => __('Recent Active Tickets', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['cplink_latesttickets_user'], 'options' => $MJTC_showhide],
                    ['id' => 'cplink_quick_actions_user', 'label' => __('Quick Actions', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['cplink_quick_actions_user'], 'options' => $MJTC_showhide],
                    ['id' => 'cplink_ticket_departments_user', 'label' => __('Ticket Departments', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['cplink_ticket_departments_user'], 'options' => $MJTC_showhide],
                    ['id' => 'cplink_recent_activity_user', 'label' => __('Recent Activity', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['cplink_recent_activity_user'], 'options' => $MJTC_showhide],
                    ['id' => 'cplink_daily_velocity_user', 'label' => __('Daily Velocity', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['cplink_daily_velocity_user'], 'options' => $MJTC_showhide],
                    ['id' => 'cplink_latestdownloads_user', 'label' => __('Downloads', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['cplink_latestdownloads_user'], 'options' => $MJTC_showhide, 'pro' => ['slug' => 'download', 'name' => __('Download', 'majestic-support')]],
                    ['id' => 'cplink_latestannouncements_user', 'label' => __('Announcements', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['cplink_latestannouncements_user'], 'options' => $MJTC_showhide, 'pro' => ['slug' => 'announcement', 'name' => __('Announcement', 'majestic-support')]],
                    ['id' => 'cplink_latestkb_user', 'label' => __('Knowledge Base', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['cplink_latestkb_user'], 'options' => $MJTC_showhide, 'pro' => ['slug' => 'knowledgebase', 'name' => __('Knowledgebase', 'majestic-support')]],
                    ['id' => 'cplink_latestfaqs_user', 'label' => __('FAQs', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['cplink_latestfaqs_user'], 'options' => $MJTC_showhide, 'pro' => ['slug' => 'faq', 'name' => __('FAQ', 'majestic-support')]],
                ]
            ],
            'top_menu_links_user' => [
                'title'       => __('Top Menu Links', 'majestic-support'),
                'description' => __('Configure visibility of quick links in the top navigation bar for customers', 'majestic-support'),
                'fields'      => [
                    ['id' => 'tplink_profile_user', 'label' => __('Profile', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['tplink_profile_user'], 'options' => $MJTC_showhide],
                    ['id' => 'tplink_home_user', 'label' => __('Dashboard', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['tplink_home_user'], 'options' => $MJTC_showhide],
                    ['id' => 'tplink_tickets_user', 'label' => __('My Tickets', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['tplink_tickets_user'], 'options' => $MJTC_showhide],
                    ['id' => 'tplink_openticket_user', 'label' => __('Submit Ticket', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['tplink_openticket_user'], 'options' => $MJTC_showhide],
                    ['id' => 'tplink_login_logout_user', 'label' => __('Login/Logout Button', 'majestic-support'), 'type' => 'toggle', 'value' => majesticsupport::$_data[0]['tplink_login_logout_user'], 'options' => $MJTC_showhide],
                ]
            ],
        ]
    ],
    'feedback' => [
        'label'  => __('Feedback', 'majestic-support'),
        'icon'   => 'message-square', // Based on your SVG comment/message icon
        'groups' => [
            'feedback_settings' => [
                'title'       => __('Feedback Settings', 'majestic-support'),
                'description' => __('Configure automated feedback request timing and custom success messages', 'majestic-support'),
                'pro'         => ['slug' => 'feedback', 'name' => __('Feedback', 'majestic-support')],
                'fields'      => [
                    [
                        'id'      => 'feedback_email_delay_type', 
                        'label'   => __('Feedback Email Delay Type', 'majestic-support'), 
                        'type'    => 'select', 
                        'value'   => majesticsupport::$_data[0]['feedback_email_delay_type'], 
                        'tooltip' => __('Select delay type for feedback email (Days or Hours)', 'majestic-support'), 
                        'options' => $MJTC_delay_type
                    ],
                    [
                        'id'      => 'feedback_email_delay', 
                        'label'   => __('Feedback Email Delay', 'majestic-support'), 
                        'type'    => 'text', 
                        'value'   => majesticsupport::$_data[0]['feedback_email_delay'], 
                        'tooltip' => __('Set the number of days or hours to send a feedback email after a ticket is closed', 'majestic-support')
                    ],
                    [
                        'id'      => 'feedback_thanks_message', 
                        'label'   => __('Success message after submitting feedback', 'majestic-support'), 
                        'type'    => 'wp_editor', 
                        'value'   => majesticsupport::$_data[0]['feedback_thanks_message'], 
                        'tooltip' => __('This text will appear whenever anyone submits feedback', 'majestic-support')
                    ],
                ],
                'pro' => ['slug' => 'feedback', 'name' => __('Feedback', 'majestic-support')]
            ],
        ]
    ],
    'email_piping' => [
        'label'  => __('Email Piping', 'majestic-support'),
        'icon'   => 'mail', // Re-using the envelope icon as per your HTML SVG
        'groups' => [
            'email_piping_settings' => [
                'title'       => __('Email Piping', 'majestic-support'),
                'description' => __('Advanced settings for processing incoming emails and automated user creation', 'majestic-support'),
                'pro'         => ['slug' => 'emailpiping', 'name' => __('Email Piping', 'majestic-support')],
                'fields'      => [
                    [
                        'id'      => 'read_utf_ticket_via_email', 
                        'label'   => __('UTF Auto Switch', 'majestic-support'), 
                        'type'    => 'select', 
                        'value'   => majesticsupport::$_data[0]['read_utf_ticket_via_email'], 
                        'tooltip' => __('Enable this to automatically handle UTF-8 character encoding for incoming emails', 'majestic-support'), 
                        'options' => $MJTC_yesno
                    ],
                    [
                        'id'      => 'create_user_via_email', 
                        'label'   => __('Create User via email', 'majestic-support'), 
                        'type'    => 'select', 
                        'value'   => majesticsupport::$_data[0]['create_user_via_email'], 
                        'tooltip' => __('Allow the system to automatically create a WordPress user account if the sender does not exist', 'majestic-support'), 
                        'options' => $MJTC_yesno
                    ],
                ]
            ],
        ]
    ],
    'push_notifications' => [
        'label'  => __('Push Notifications', 'majestic-support'),
        'icon'   => 'bell', // Based on your SVG notification bell icon
        'groups' => [
            'firebase_notifications' => [
                'title'       => __('Firebase Notifications', 'majestic-support'),
                'description' => __('Configure Firebase Cloud Messaging to enable real-time desktop notifications for your support staff', 'majestic-support'),
                'pro'         => ['slug' => 'notification', 'name' => __('Firebase Notifications', 'majestic-support')],
                'alerts'      => [
                    // Logic for displaying the plugin status message
                    'plugin_status' => [
                        'installed' => file_exists(WP_PLUGIN_DIR.'/majestic-support-notification/majestic-support-notification.php'),
                        'active'    => class_exists('MJTC_Notification'),
                        'install_url' => admin_url("admin.php?page=majesticsupport_premiumplugin")
                    ],
                    'setup_guide' => [
                        'text' => __('Find and add the Firebase API keys.', 'majestic-support'),
                        'url'  => 'https://console.firebase.google.com'
                    ]
                ],
                'fields'      => [
                    ['id' => 'apiKey_firebase', 'label' => __("User's API Key", 'majestic-support'), 'type' => 'text', 'value' => majesticsupport::$_data[0]['apiKey_firebase'], 'tooltip' => __('Firebase API key for the front user', 'majestic-support')],
                    ['id' => 'authDomain_firebase', 'label' => __('Auth Domain', 'majestic-support'), 'type' => 'text', 'value' => majesticsupport::$_data[0]['authDomain_firebase'], 'tooltip' => __('Firebase Auth Domain', 'majestic-support')],
                    ['id' => 'databaseURL_firebase', 'label' => __('Database URL', 'majestic-support'), 'type' => 'text', 'value' => majesticsupport::$_data[0]['databaseURL_firebase'], 'tooltip' => __('Firebase Database URL', 'majestic-support')],
                    ['id' => 'projectId_firebase', 'label' => __('Project ID', 'majestic-support'), 'type' => 'text', 'value' => majesticsupport::$_data[0]['projectId_firebase'], 'tooltip' => __('Firebase Project ID', 'majestic-support')],
                    ['id' => 'storageBucket_firebase', 'label' => __('Bucket Storage', 'majestic-support'), 'type' => 'text', 'value' => majesticsupport::$_data[0]['storageBucket_firebase'], 'tooltip' => __('Firebase Bucket Storage', 'majestic-support')],
                    ['id' => 'messagingSenderId_firebase', 'label' => __('Message Sender ID', 'majestic-support'), 'type' => 'text', 'value' => majesticsupport::$_data[0]['messagingSenderId_firebase'], 'tooltip' => __('Firebase Message Sender ID', 'majestic-support')],
                    ['id' => 'server_key_firebase', 'label' => __('Private Server Key', 'majestic-support'), 'type' => 'text', 'value' => majesticsupport::$_data[0]['server_key_firebase'], 'tooltip' => __('Firebase Server Key', 'majestic-support')],
                    [
                        'id'      => 'logo_for_desktop_notfication_url', 
                        'label'   => __('Logo Image for Desktop Notifications', 'majestic-support'), 
                        'type'    => 'file', 
                        'value'   => majesticsupport::$_config['logo_for_desktop_notfication_url'], 
                        'tooltip' => __('Upload a logo to display in push notification alerts', 'majestic-support'),
                        'preview' => (majesticsupport::$_config['logo_for_desktop_notfication_url'] != '') ? wp_upload_dir()['baseurl'].'/'.majesticsupport::$_config['data_directory'].'/attachmentdata/'.majesticsupport::$_config['logo_for_desktop_notfication_url'] : null
                    ],
                ]
            ],
        ]
    ],
    'private_credentials' => [
        'label'  => __('Private Credentials', 'majestic-support'),
        'icon'   => 'lock', // Based on your SVG padlock icon
        'groups' => [
            'security_credentials' => [
                'title'       => __('Private Credentials', 'majestic-support'),
                'description' => __('Manage encryption keys and secondary security layers for sensitive stored data', 'majestic-support'),
                'pro'         => ['slug' => 'privatecredentials', 'name' => __('Private Credentials', 'majestic-support')],
                'fields'      => [
                    [
                        'id'      => 'private_credentials_secretkey', 
                        'label'   => __('Secret Key', 'majestic-support'), 
                        'type'    => 'text', 
                        'value'   => majesticsupport::$_data[0]['private_credentials_secretkey'], 
                        'tooltip' => __('Changing the encryption key of private credentials will cause all existing credentials to be discarded', 'majestic-support')
                    ],
                    [
                        'id'      => 'second_level_security', 
                        'label'   => __('Second Level Security', 'majestic-support'), 
                        'type'    => 'info_block', // Used since MJTC_field is empty but description contains instructions
                        'value'   => '', 
                        'tooltip' => sprintf(
                            // translators: %s: The name of the configuration file (e.g., wp-config.php).
                            __('For enhanced security, change the encryption method in %s on line 10', 'majestic-support'), 
                            '<code>' . WP_PLUGIN_DIR . '/majestic-support-privatecredentials/classes/privatecredentials.php</code>'
                        )
                    ],
                ]
            ],
        ]
    ],
    'envato_validation' => [
        'label'  => __('Envato Validation', 'majestic-support'),
        'icon'   => 'check-circle', // Based on your SVG shield with a checkmark
        'groups' => [
            'envato_settings' => [
                'title'       => __('Envato Validation', 'majestic-support'),
                'description' => __('Configure Envato API integration to verify customer purchase codes and support eligibility', 'majestic-support'),
                'pro'         => ['slug' => 'envatovalidation', 'name' => __('Envato Validation', 'majestic-support')],
                'fields'      => [
                    [
                        'id'      => 'envato_api_key', 
                        'label'   => __('Api Key', 'majestic-support'), 
                        'type'    => 'text', 
                        'value'   => majesticsupport::$_data[0]['envato_api_key'], 
                        'tooltip' => __('Enter Envato api key. You can generate a personal token at build.envato.com', 'majestic-support'),
                        'link'    => [
                            'text' => __('Click here to generate an api key', 'majestic-support'),
                            'url'  => 'https://build.envato.com/create-token/'
                        ]
                    ],
                    [
                        'id'      => 'envato_license_required', 
                        'label'   => __('License Mandatory', 'majestic-support'), 
                        'type'    => 'select', 
                        'value'   => majesticsupport::$_data[0]['envato_license_required'], 
                        'tooltip' => __('Prevent users from submitting a ticket without a valid license for one of your products', 'majestic-support'), 
                        'options' => $MJTC_yesno
                    ],
                    [
                        'id'      => 'envato_product_ids', 
                        'label'   => __('Product ID', 'majestic-support'), 
                        'type'    => 'text', 
                        'value'   => majesticsupport::$_data[0]['envato_product_ids'], 
                        'tooltip' => __('A comma-separated list of Envato product IDs (e.g., 123456, 789012)', 'majestic-support')
                    ],
                ]
            ],
        ]
    ],
    'mailchimp' => [
        'label'  => __('Mailchimp', 'majestic-support'),
        'icon'   => 'mail', // Based on your SVG envelope icon
        'groups' => [
            'mailchimp_settings' => [
                'title'       => __('Mailchimp', 'majestic-support'),
                'description' => __('Configure MailChimp API integration to sync your support users with your marketing audiences', 'majestic-support'),
                'pro'         => ['slug' => 'mailchimp', 'name' => __('Mailchimp', 'majestic-support')],
                'fields'      => [
                    [
                        'id'      => 'mailchimp_api_key', 
                        'label'   => __('Api Key', 'majestic-support'), 
                        'type'    => 'text', 
                        'value'   => majesticsupport::$_data[0]['mailchimp_api_key'], 
                        'tooltip' => __('Enter MailChimp API key', 'majestic-support')
                    ],
                    [
                        'id'      => 'mailchimp_list_id', 
                        'label'   => __('Audience ID', 'majestic-support'), 
                        'type'    => 'text', 
                        'value'   => majesticsupport::$_data[0]['mailchimp_list_id'], 
                        'tooltip' => __('Find Audience ID in your MailChimp account settings under Audience name and defaults', 'majestic-support')
                    ],
                    [
                        'id'      => 'mailchimp_double_optin', 
                        'label'   => __('Enable double opt-in', 'majestic-support'), 
                        'type'    => 'select', 
                        'value'   => majesticsupport::$_data[0]['mailchimp_double_optin'], 
                        'tooltip' => __('You must also enable double opt-in in your MailChimp account for this to work correctly', 'majestic-support'),
                        'options' => $MJTC_yesno
                    ],
                    [
                        'id'      => 'mailchimp_welcome_info', 
                        'label'   => __('Welcome email', 'majestic-support'), 
                        'type'    => 'info_block', 
                        'value'   => '', 
                        'tooltip' => __('You can enable Final Welcome Email in your MailChimp account settings', 'majestic-support')
                    ],
                ]
            ],
        ]
    ],
    'easy_digital_downloads' => [
        'label'  => __('Easy Digital Downloads', 'majestic-support'),
        'icon'   => 'download', // Based on your SVG download/arrow-down icon
        'groups' => [
            'edd_settings' => [
                'title'       => __('Easy Digital Downloads', 'majestic-support'),
                'description' => __('Configure integration with Easy Digital Downloads to verify customer licenses before providing support', 'majestic-support'),
                'pro'         => ['slug' => 'easydigitaldownloads', 'name' => __('Easy Digital Downloads', 'majestic-support')],
                'fields'      => [
                    [
                        'id'      => 'verify_license_on_ticket_creation', 
                        'label'   => __('Verify License On Ticket Creation', 'majestic-support'), 
                        'type'    => 'select', 
                        'value'   => majesticsupport::$_data[0]['verify_license_on_ticket_creation'], 
                        'tooltip' => __('Enable this to require a valid EDD license key from the user when they attempt to create a new support ticket', 'majestic-support'),
                        'options' => $MJTC_yesno
                    ],
                ]
            ],
        ]
    ],
];

// Mock pages for select fields.
$majesticsupport_mock_pages = [
    ['id' => '1', 'title' => __('Home Page', 'majestic-support')],
    ['id' => '2', 'title' => __('Sample Page', 'majestic-support')],
    ['id' => '3', 'title' => __('WordPress Default', 'majestic-support')],
    ['id' => '6', 'title' => __('Blog', 'majestic-support')],
];

/**
 * Renders a single setting field based on its configuration.
 *
 * @param array $majesticsupport_field The field configuration array.
 * @param string $majesticsupport_category_key The key of the parent category.
 * @param string $majesticsupport_group_key The key of the parent group.
 * @param array $majesticsupport_mock_pages An array of mock pages for select fields.
 * @param array $majesticsupport_installed_addons An array of slugs for installed addons.
 */
function majesticsupport_mjtc_render_setting_field($majesticsupport_field, $majesticsupport_category_key, $majesticsupport_group_key, $majesticsupport_mock_pages, $majesticsupport_installed_addons) {
    global $majesticsupport;
    $majesticsupport_field_id = 'mjtc-' . esc_attr($majesticsupport_field['id']);
    $majesticsupport_field_name = esc_attr($majesticsupport_field['id']);
    $majesticsupport_value = isset($majesticsupport::$_data[0][$majesticsupport_field['id']]) ? $majesticsupport::$_data[0][$majesticsupport_field['id']] : ($majesticsupport_field['value'] ?? '');

    $majesticsupport_control_html = '';
    $majesticsupport_pro_label_indicator = '';
    $majesticsupport_pro_field_explanation = '';
    $majesticsupport_is_field_pro = isset($majesticsupport_field['pro']);
    $majesticsupport_is_locked = $majesticsupport_is_field_pro && !in_array($majesticsupport_field['pro']['slug'], $majesticsupport_installed_addons);

    if ($majesticsupport_is_field_pro) { // This block now handles both locked and unlocked pro fields
        $majesticsupport_pro_label_indicator = '<svg class="mjtc-pro-label-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="1em" height="1em" fill="currentColor">
                                  <path d="M325.8 200.7l23.5-74.4c5.3-16.7 29.1-16.7 34.4 0l23.5 74.4c5.1 16.1 17.8 28.8 33.9 33.9l74.4 23.5c16.7 5.3 16.7 29.1 0 34.4l-74.4 23.5c-16.1 5.1-28.8 17.8-33.9 33.9l-23.5 74.4c-5.3 16.7-29.1 16.7-34.4 0l-23.5-74.4c-5.1-16.1-17.8-28.8-33.9-33.9l-74.4-23.5c-16.7-5.3-16.7-29.1 0-34.4l74.4-23.5c16.1-5.1 28.8-17.8 33.9-33.9zM107.2 92l13.6-43.1c3-9.6 16.8-9.6 19.8 0l13.6 43.1c2.9 9.3 10.3 16.6 19.6 19.6l43.1 13.6c9.6 3 9.6 16.8 0 19.8l-43.1 13.6c-9.3 2.9-16.6 10.3-19.6 19.6l-13.6 43.1c-3 9.6-16.8 9.6-19.8 0l-13.6-43.1c-2.9-9.3-10.3-16.6-19.6-19.6l-43.1-13.6c-9.6-3-9.6-16.8 0-19.8l43.1-13.6c9.3-2.9 16.6-10.3 19.6-19.6zM69.8 440.6l7.9-24.9c1.7-5.5 9.7-5.5 11.4 0l7.9 24.9c1.7 5.4 5.9 9.6 11.3 11.3l24.9 7.9c5.5 1.7 5.5 9.7 0 11.4l-24.9 7.9c-5.4 1.7-9.6 5.9-11.3 11.3l-7.9 24.9c-1.7 5.5-9.7 5.5-11.4 0l-7.9-24.9c-1.7-5.4-5.9-9.6-11.3-11.3l-24.9-7.9c-5.5-1.7-5.5-9.7 0-11.4l24.9-7.9c5.4-1.7 9.6-5.9 11.3-11.3z"/>
                                </svg>';

        if ($majesticsupport_is_locked) {
            $majesticsupport_pro_field_explanation = '<p class="mjtc-pro-field-explanation">' . esc_html__('This feature is part of a premium addon', 'majestic-support') . '</p>';

            $base_control_html = '';
            switch ($majesticsupport_field['type']) {
                case 'toggle':
                    $base_control_html = '<label class="mjtc-toggle-switch"><input type="checkbox" disabled><span class="mjtc-toggle-slider"></span></label>';
                    break;
                case 'select':
                    //$majesticsupport_first_option_label = is_array($majesticsupport_field['options']) && !empty($majesticsupport_field['options']) ? reset($majesticsupport_field['options']) : '';
                    $majesticsupport_first_option = is_array($majesticsupport_field['options']) && !empty($majesticsupport_field['options']) ? reset($majesticsupport_field['options']) : '';
                    if (is_object($majesticsupport_first_option)) {
                        // adjust based on your data structure, e.g. ->label or ->name
                        $majesticsupport_first_option_label = isset($majesticsupport_first_option->text) ? $majesticsupport_first_option->text : '';
                    } elseif (is_array($majesticsupport_first_option)) {
                        // adjust based on your array keys
                        $majesticsupport_first_option_label = isset($majesticsupport_first_option['text']) ? $majesticsupport_first_option['text'] : '';
                    } else {
                        $majesticsupport_first_option_label = $majesticsupport_first_option;
                    }
                    $base_control_html = '<select class="mjtc-form-select" disabled><option>' . esc_html($majesticsupport_first_option_label) . '</option></select>';
                    break;
                default:
                    $base_control_html = '<input type="text" class="mjtc-form-input" disabled>';
                    break;
            }

            $majesticsupport_control_html = '
                <div class="mjtc-pro-control-wrapper">
                    <div class="disabled-control-bg">' . $base_control_html . '</div>
                    <div class="upgrade-overlay">
                        <a href="#" class="mjtc-btn mjtc-btn-upgrade-overlay">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" width="1em" height="1em" fill="currentColor">
                              <path d="M400 224h-24v-72C376 68.2 307.8 0 224 0S72 68.2 72 152v72H48c-26.5 0-48 21.5-48 48v192c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48V272c0-26.5-21.5-48-48-48zm-104 0H152v-72c0-39.7 32.3-72 72-72s72 32.3 72 72v72z"/>
                            </svg>
                            <span>' . __('Unlock with', 'majestic-support').' ' .majesticsupport::MJTC_getVarValue($majesticsupport_field['pro']['name']) . '</span>
                        </a>
                    </div>
                </div>';
        }
        // If it's a pro field but NOT locked (addon is active), it will fall through to the default rendering below.
    }

    // This block now renders regular fields AND unlocked pro fields.
    if (!$majesticsupport_is_locked) {
        switch ($majesticsupport_field['type']) {
            // --- NEW CASE START ---
            case 'multi_toggle':
                $majesticsupport_control_html = '<div class="mjtc-multi-toggle-row">';
                foreach ($majesticsupport_field['sub_fields'] as $role => $sub_field) {
                    // Skip if no ID is provided (your 'null' case)
                    if (empty($sub_field['id'])) {
                        $majesticsupport_control_html .= '<div class="mjtc-multi-toggle-item is-empty"><span>------</span></div>';
                        continue;
                    }

                    // Check if this specific sub-field is locked
                    $sub_is_pro = isset($sub_field['pro']);
                    $sub_is_locked = $sub_is_pro && !in_array($sub_field['pro']['slug'], $majesticsupport_installed_addons);
                    if ($sub_is_locked) {
                        $majesticsupport_control_html .= '
                        <div class="mjtc-pro-control-wrapper">
                            <div class="disabled-control-bg"><label class="mjtc-toggle-switch"><input type="checkbox" disabled><span class="mjtc-toggle-slider"></span></label></div>
                            <div class="upgrade-overlay">
                                <a href="#" class="mjtc-btn mjtc-btn-upgrade-overlay">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" width="1em" height="1em" fill="currentColor">
                                      <path d="M400 224h-24v-72C376 68.2 307.8 0 224 0S72 68.2 72 152v72H48c-26.5 0-48 21.5-48 48v192c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48V272c0-26.5-21.5-48-48-48zm-104 0H152v-72c0-39.7 32.3-72 72-72s72 32.3 72 72v72z"/>
                                    </svg>
                                    <span>' . __('Unlock with', 'majestic-support').' ' .majesticsupport::MJTC_getVarValue($sub_field['pro']['name']) . '</span>
                                </a>
                            </div>
                        </div>';
                        continue;
                    }
                    
                    $sub_val = isset($majesticsupport::$_data[0][$sub_field['id']]) ? $majesticsupport::$_data[0][$sub_field['id']] : ($sub_field['value'] ?? '0');
                    $sub_checked = checked($sub_val, '1', false);
                    $disabled = $sub_is_locked ? 'disabled' : '';
                    $locked_class = $sub_is_locked ? 'is-sub-locked' : '';

                    $majesticsupport_control_html .= '<div class="mjtc-multi-toggle-item ' . $locked_class . '">';
                    $majesticsupport_control_html .= '<label class="mjtc-toggle-switch">';
                    if (!$sub_is_locked) {
                        $majesticsupport_control_html .= '<input type="hidden" name="' . esc_attr($sub_field['id']) . '" value="0">';
                    }
                    $majesticsupport_control_html .= '<input type="checkbox" name="' . esc_attr($sub_field['id']) . '" value="1" ' . $sub_checked . ' ' . $disabled . '>';
                    $majesticsupport_control_html .= '<span class="mjtc-toggle-slider"></span>';
                    $majesticsupport_control_html .= '</label>';
                    
                    // Show small lock icon or name if locked
                    if ($sub_is_locked) {
                        $majesticsupport_control_html .= '<span class="mjtc-sub-lock-info" title="' . esc_attr__('Requires', 'majestic-support') . ' ' . esc_attr($sub_field['pro']['name']) . '"><i class="fas fa-lock"></i></span>';
                    }
                    $majesticsupport_control_html .= '</div>';
                }
                $majesticsupport_control_html .= '</div>';
                break;
            case 'toggle':
                $majesticsupport_checked = checked($majesticsupport_value, '1', false);
                $majesticsupport_control_html = '<label class="mjtc-toggle-switch">
                <input type="hidden" name="' . $majesticsupport_field_name . '" value="0">' . '
                <input type="checkbox" id="' . $majesticsupport_field_id . '" name="' . $majesticsupport_field_name . '" value="1" ' . $majesticsupport_checked . '><span class="mjtc-toggle-slider"></span></label>';
                break;
            case 'info_text':
                $majesticsupport_control_html = '<div class="mjtc-info-text">' . esc_html($majesticsupport_field['text']) . '
                    <svg class="mjtc-copy-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" width="1em" height="1em" fill="currentColor">
                      <path d="M384 336H192c-8.8 0-16-7.2-16-16V64c0-8.8 7.2-16 16-16l140.1 0L400 115.9V320c0 8.8-7.2 16-16 16zM192 384h192c35.3 0 64-28.7 64-64V115.9c0-12.7-5.1-24.9-14.1-33.9L366.1 14.1c-9-9-21.2-14.1-33.9-14.1H192c-35.3 0-64 28.7-64 64v256c0 35.3 28.7 64 64 64zM64 128c-35.3 0-64 28.7-64 64v256c0 35.3 28.7 64 64 64h192c35.3 0 64-28.7 64-64v-32h-48v32c0 8.8-7.2 16-16 16H64c-8.8 0-16-7.2-16-16V192c0-8.8 7.2-16 16-16h32v-48H64z"/>
                    </svg>
                </div>';
                break;
            case 'select':
                // Assuming MJTC_formfield is a class you have that generates form fields
                $majesticsupport_options_obj = [];
                $current_options = $majesticsupport_field['options'] ?? [];
                 if (!empty($current_options)) {
                    if (!empty($current_options)) {
                        $majesticsupport_first_option = reset($current_options);
                        if (is_object($majesticsupport_first_option) && isset($majesticsupport_first_option->id) && isset($majesticsupport_first_option->text)) {
                            // It's already an array of objects, use it directly.
                            $majesticsupport_options_obj = $current_options;
                        } else {
                            // It's a key-value array, convert it.
                            foreach ($current_options as $majesticsupport_key => $majesticsupport_label) {
                                $majesticsupport_options_obj[] = (object)['id' => $majesticsupport_key, 'text' => $majesticsupport_label];
                            }
                        }
                    }

                }
                $majesticsupport_control_html = MJTC_formfield::MJTC_select($majesticsupport_field_name, $majesticsupport_options_obj, $majesticsupport_value, '', ['class' => 'mjtc-form-select', 'id' => $majesticsupport_field_id]);
                break;
             case 'textarea':
                $majesticsupport_control_html = MJTC_formfield::MJTC_textarea($majesticsupport_field_name, $majesticsupport_value, ['class' => 'mjtc-form-textarea', 'rows' => $majesticsupport_field['rows'] ?? 4, 'id' => $majesticsupport_field_id]);
                break;
             case 'wp_editor':
                $majesticsupport_control_html = '__WP_EDITOR__'; // placeholder
                break;
            case 'file':
                $majesticsupport_default_image = isset(majesticsupport::$_data[0]['default_image']) ? majesticsupport::$_data[0]['default_image'] : '';
                $majesticsupport_data_directory = majesticsupport::$_config['data_directory'];
                $majesticsupport_wpdir = wp_upload_dir();

                if (!empty($majesticsupport_default_image)) {
                    $majesticsupport_img_path = $majesticsupport_wpdir['baseurl'] . '/' . $majesticsupport_data_directory . '/data/default_image/' . $majesticsupport_default_image;
                    $majesticsupport_display_style = 'block';
                } else {
                    $majesticsupport_img_path = MJTC_PLUGIN_URL . 'includes/images/default_logo.png';
                    $majesticsupport_display_style = 'none';
                }

                $majesticsupport_control_html = '
                <div class="mjtc-file-input-wrapper">
                    <div class="mjtc-file-input-actions">
                        <input type="file" id="' . esc_attr($majesticsupport_field_name) . '" name="' . esc_attr($majesticsupport_field_name) . '" accept="image/*">
                    </div>

                    <div class="mjsupport-form-help-txt-wrp">';
                    if(majesticsupport::$_data[0]['support_custom_img'] != '0') {
                        $maindir = wp_upload_dir();
                        $basedir = $maindir['baseurl'];
                        $MJTC_datadirectory = majesticsupport::$_config['data_directory'];
                        $path = $basedir . '/' . $MJTC_datadirectory;
                        $path .= "/supportImg/" . majesticsupport::$_data[0]['support_custom_img'];
                        $majesticsupport_control_html .= '
                        <img alt="'. esc_html(__('Image','majestic-support')) .'" width="50px" height="50px" src="'. esc_url($path) .'">'. esc_html(majesticsupport::$_data[0]['support_custom_img']);
                        $majesticsupport_control_html .= '
                        <a style="color: red;" title="'. esc_attr(__('Delete','majestic-support')) .'" onclick="deleteSupportCustomImage()">( '. esc_html(__('Delete','majestic-support')) .' )</a>';
                    }
                    $majesticsupport_control_html .= '
                    <div class="mjsupport-form-help-txt" style="width: 100%;">' . esc_html__('This image will be shown as the default image for entities & users if no other image is provided', 'majestic-support') . '</div>
                    </div>
                </div>
                ';
                break;

            default:
                $majesticsupport_control_html = MJTC_formfield::MJTC_text($majesticsupport_field_name, $majesticsupport_value, ['class' => 'mjtc-form-input', 'type' => $majesticsupport_field['type'], 'id' => $majesticsupport_field_id]);
                break;
        }
    }


    $majesticsupport_tooltip_html = !empty($majesticsupport_field['tooltip']) ? '<span class="mjtc-tooltip-wrapper" data-tooltip="' . esc_attr($majesticsupport_field['tooltip']) . '">
        <svg class="mjtc-tooltip-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="1em" height="1em" fill="currentColor">
          <path d="M504 256c0 136.997-111.043 248-248 248S8 392.997 8 256C8 119.083 119.043 8 256 8s248 111.083 248 248zM262.655 90c-54.497 0-89.255 22.957-116.549 63.758-3.536 5.286-2.353 12.415 2.715 16.258l34.699 26.31c5.205 3.947 12.621 3.008 16.812-2.188 28.891-35.816 57.067-27.766 52.822 5.06-2.909 22.472-24.966 38.007-44.502 65.652-9.256 13.097-12.656 24.225-12.656 48.094 0 7.55 5.592 13.91 13.064 14.948l47.433 6.591c8.031 1.116 15.341-5.115 15.341-13.235 0-14.78 12.822-26.699 22.146-37.119 14.52-16.223 35.857-36.425 41.792-71.186 9.873-57.828-36.255-92.943-73.117-92.943zM256 368c-22.091 0-40 17.909-40 40s17.909 40 40 40 40-17.909 40-40-17.909-40-40-40z"/>
        </svg>
    </span>' : '';
    $conditional_attrs = !empty($majesticsupport_field['condition']) ? 'data-condition-field="' . esc_attr($majesticsupport_field['condition']['field']) . '" data-condition-value="' . esc_attr($majesticsupport_field['condition']['value']) . '"' : '';
    $wrapper_class = 'mjtc-setting-row-wrapper ' . (!empty($majesticsupport_field['condition']) ? 'mjtc-conditional-field-wrapper ' : '') . ($majesticsupport_is_locked ? 'is-pro-field' : '');

    echo '<div class="' . esc_attr($wrapper_class) . '" ' . esc_attr($conditional_attrs) . '>';
    echo '  <div class="mjtc-setting-row">';
    echo '      <div class="mjtc-setting-label">';
    echo '          <label for="' . esc_attr($majesticsupport_field_id) . '">' . esc_html(majesticsupport::MJTC_getVarValue($majesticsupport_field['label'])) . wp_kses($majesticsupport_pro_label_indicator,MJTC_ALLOWED_TAGS) . wp_kses($majesticsupport_tooltip_html,MJTC_ALLOWED_TAGS) . '</label>';
    echo '      </div>';
    echo '      <div class="mjtc-setting-control" data-field-id="' . esc_attr($majesticsupport_field['id']) . '">';
    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped // Admin side interface values are handled safelty
    if ($majesticsupport_control_html === '__WP_EDITOR__') {
        wp_editor(
            $majesticsupport_value,
            $majesticsupport_field_name,
            [
                'textarea_name' => $majesticsupport_field_name
            ]
        );
    } else {
        echo wp_kses($majesticsupport_control_html, MJTC_ALLOWED_TAGS);
    }
    echo wp_kses($majesticsupport_pro_field_explanation, MJTC_ALLOWED_TAGS); // This will be empty unless the field is locked
    echo '      </div>';
    echo '  </div>';
    echo '</div>';
}



$MJTC_plugin_array = get_option('active_plugins');
?>
<div id="msadmin-wrapper">
    <div id="msadmin-leftmenu">
        <?php  MJTC_includer::MJTC_getClassesInclude('msadminsidemenu'); ?>
    </div>
    <div id="msadmin-data">
        <?php MJTC_includer::MJTC_getModel('majesticsupport')->getPageTitle('configurations'); ?>
        <div id="mjtc-config-dashboard">
            <div id="app">
                <aside id="mjtc-sidebar">
                    <div class="mjtc-sidebar-closebtn"id="mjtc-sidebar-tpclosebtn">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6L6 18M6 6l12 12"></path></svg>
                    </div>
                    <nav id="mjtc-settings-nav"></nav>
                </aside>
                <main>
                     <header>
                        <div class="mjtc-header-left">
                             <div id="mjtc-main-panel-header">
                                <div id="mjtc-mobile-menu-toggle" class="mjtc-btn">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" width="1em" height="1em" fill="currentColor">
                                    <path d="M0 96C0 78.3 14.3 64 32 64H416c17.7 0 32 14.3 32 32s-14.3 32-32 32H32C14.3 128 0 113.7 0 96zM0 256c0-17.7 14.3-32 32-32H416c17.7 0 32 14.3 32 32s-14.3 32-32 32H32c-17.7 0-32-14.3-32-32zM448 416c0 17.7-14.3 32-32 32H32c-17.7 0-32-14.3-32-32s14.3-32 32-32H416c17.7 0 32 14.3 32 32z"/>
                                    </svg>
                                </div>
                                <span id="mjtc-category-title-span"></span>
                            </div>
                        </div>
                        <div class="mjtc-header-actions">
                            <div id="mjtc-discard-changes" class="mjtc-btn mjtc-btn-secondary" style="display: none;">
                                <span><?php echo esc_html__('Discard', 'majestic-support'); ?></span>
                            </div>
                            <div id="mjtc-save-changes" class="mjtc-btn mjtc-btn-primary" disabled>
                                <span><?php echo esc_html__('Save Changes', 'majestic-support'); ?></span>
                                <svg style="display: none;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" width="1em" height="1em" fill="currentColor">
                                  <path d="M433.1 129.1l-83.9-83.9C342.3 38.32 327.1 32 316.1 32H64C28.65 32 0 60.65 0 96v320c0 35.35 28.65 64 64 64h320c35.35 0 64-28.65 64-64V163.9C448 152.9 441.7 137.7 433.1 129.1zM224 416c-35.34 0-64-28.66-64-64s28.66-64 64-64s64 28.66 64 64S259.3 416 224 416zM320 208C320 216.8 312.8 224 304 224h-224C71.16 224 64 216.8 64 208v-96C64 103.2 71.16 96 80 96h224C312.8 96 320 103.2 320 112V208z"/>
                                </svg>
                            </div>
                        </div>
                    </header>
                    <div id="mjtc-main-panel-content">
                        <div class="mjtc-search-panel">
                            <div class="mjtc-search-wrapper">
                                 <div class="mjtc-search-input-wrapper">
                                   <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="1em" height="1em" fill="currentColor">
                                      <path d="M505 442.7L405.3 343c-4.5-4.5-10.6-7-17-7H372c27.6-35.3 44-79.7 44-128C416 93.1 322.9 0 208 0S0 93.1 0 208s93.1 208 208 208c48.3 0 92.7-16.4 128-44v16.3c0 6.4 2.5 12.5 7 17l99.7 99.7c9.4 9.4 24.6 9.4 33.9 0l28.3-28.3c9.4-9.4 9.4-24.6.1-34zM208 336c-70.7 0-128-57.2-128-128 0-70.7 57.2-128 128-128 70.7 0 128 57.2 128 128 0 70.7-57.2 128-128 128z"/>
                                    </svg>
                                    <input type="text" id="mjtc-search-box" placeholder="<?php echo esc_attr__('Quickly find any setting', 'majestic-support'); ?>">
                                 </div>
                                 <p class="mjtc-search-description"><?php echo esc_html__('Search by setting title or tooltip description', 'majestic-support'); ?></p>
                            </div>
                        </div>
                        <div id="mjtc-sticky-sub-nav-wrapper" class="mjtc-hidden">
                            <div id="mjtc-sub-nav"></div>
                        </div>
                        <form id="majesticsupport-form" class="majesticsupport-configurations" method="post" action="<?php echo esc_url_raw(admin_url("?page=majesticsupport_configuration&task=saveconfiguration")); ?>"  enctype="multipart/form-data">
                             <div class="mjtc-content-wrapper">
                                 <div id="mjtc-main-panel">
                                    <?php
                                    $majesticsupport_installed_addons = majesticsupport::$_active_addons;
                                    foreach ($majesticsupport_settings_config as $majesticsupport_category_key => $majesticsupport_category) : ?>
                                        <div class="mjtc-category-container" data-category-key="<?php echo esc_attr($majesticsupport_category_key); ?>">
                                            <?php foreach ($majesticsupport_category['groups'] as $majesticsupport_group_key => $majesticsupport_group) :
                                                $majesticsupport_is_group_pro = isset($majesticsupport_group['pro']);
                                                $majesticsupport_is_group_locked = $majesticsupport_is_group_pro && !in_array($majesticsupport_group['pro']['slug'], $majesticsupport_installed_addons);
                                            ?>
                                                <div class="mjtc-group-card <?php echo $majesticsupport_is_group_locked ? 'is-pro-group' : ''; ?>" id="mjtc-group-<?php echo esc_attr($majesticsupport_group_key); ?>" data-groupkey="<?php echo esc_attr($majesticsupport_group_key); ?>">
                                                    <div class="mjtc-group-header">
                                                        <div class="mjtc-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($majesticsupport_group['title'])); ?></div>
                                                        <p><?php echo esc_html(majesticsupport::MJTC_getVarValue($majesticsupport_group['description'])); ?></p>
                                                    </div>
                                                    <?php
                                                    if ($majesticsupport_group_key == 'ticket_operations_emails') {  ?>
                                                        <div class="mjtc-support-configuration-row-mail">
                                                            <div class="mjtc-support-conf-text-sub"><?php echo esc_html__('Admin', 'majestic-support'); ?></div>
                                                            <div class="mjtc-support-conf-text-sub"><?php echo esc_html__('Agent', 'majestic-support'); ?></div>
                                                            <div class="mjtc-support-conf-text-sub"><?php echo esc_html__('User', 'majestic-support'); ?></div>
                                                        </div>
                                                        <?php
                                                    } ?>
                                                    <?php if ($majesticsupport_is_group_locked) : ?>
                                                        <div class="mjtc-pro-group-cta">
                                                             <div class="cta-icon">
                                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" width="1em" height="1em" fill="currentColor">
                                                                  <path d="M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z"/>
                                                                </svg>
                                                             </div>
                                                             <div class="cta-text">
                                                                <h3><?php echo esc_html__('Unlock', 'majestic-support') .' ' . esc_html(majesticsupport::MJTC_getVarValue($majesticsupport_group['title'])); ?></h3>
                                                                <p><?php echo esc_html__('This and other powerful features are available in the', 'majestic-support') .' ' . '<strong>' . esc_html(majesticsupport::MJTC_getVarValue($majesticsupport_group['pro']['name'])) . '</strong>'; ?></p>
                                                             </div>
                                                             <a href="#" class="mjtc-btn mjtc-btn-primary"><?php esc_html_e('Upgrade Now', 'majestic-support'); ?></a>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div class="mjtc-fields-container">
                                                        <?php foreach ($majesticsupport_group['fields'] as $majesticsupport_field) {
                                                            majesticsupport_mjtc_render_setting_field($majesticsupport_field, $majesticsupport_category_key, $majesticsupport_group_key, $majesticsupport_mock_pages,$majesticsupport_installed_addons);
                                                        } ?>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endforeach; ?>
                                    <div id="mjtc-no-results-message" style="display: none; text-align: center; padding: 64px 0;">
                                        <?php echo esc_html__('No settings found for your search', 'majestic-support'); ?>
                                    </div>
                                 </div>
                             </div>
                             <?php echo wp_kses(MJTC_formfield::MJTC_hidden('action', 'configuration_saveconfiguration'), MJTC_ALLOWED_TAGS); ?>
                            <?php echo wp_kses(MJTC_formfield::MJTC_hidden('form_request', 'majesticsupport'), MJTC_ALLOWED_TAGS); ?>
                             <?php echo wp_kses(MJTC_formfield::MJTC_hidden('_wpnonce', esc_html(wp_create_nonce('save-configuration'))),MJTC_ALLOWED_TAGS); ?>
                        </form>
                    </div>
                </main>
            </div>
            <div id="mjtc-toast-container"></div>
        </div>
    </div>
</div>
<?php
$majesticsupport_js = '
(function($) {
    "use strict";

    const App = {
        // --- APP STATE & CONFIG ---
        settingsConfig: ' . wp_json_encode($majesticsupport_settings_config) . ',
        activeCategory: "general",
        changesToSubmit: {},

        // --- INITIALIZATION ---
        init: function() {
            this.renderSidebar();
            this.showCategory(this.activeCategory);
            this.addEventListeners();
            this.initSeoFields();
            this.initCopyButtons();
        },

        initSeoFields: function() {
            $(".seo-field-container").each(function() {
                const $container = $(this);
                const fieldId = $container.data("id");

                const initialValueStr = $container.attr("data-initial-value") || "";
                const matches = initialValueStr.match(/\[(.*?)\]/g);
                const initialTags = matches ? matches.map(tag => tag.slice(1, -1)) : [];

                const state = {
                    currentValue: initialTags,
                    allAvailableTags: $container.data("available-tags") || []
                };

                const render = () => {
                    const $usedTags = $container.find(".tags-used-container").empty();
                    const $availableTags = $container.find(".tags-available-container").empty();

                    const availableTags = state.allAvailableTags.filter(
                        tag => !state.currentValue.includes(tag)
                    );

                    // Render used tags
                    state.currentValue.forEach(tag => {
                        $usedTags.append(
                            `<span class="template-tag">
                                ${tag}
                                <span class="template-tag-remove" data-tag="${tag}">X</span>
                            </span>`
                        );
                    });

                    // Render available tags
                    availableTags.forEach(tag => {
                        $availableTags.append(
                            `<span class="template-tag-available" data-tag="${tag}">${tag}</span>`
                        );
                    });

                    // Update hidden input value
                    const formattedValue = state.currentValue.map(tag => `[${tag}]`).join("");
                    $container.find("input[type=\"hidden\"]").val(formattedValue);
                };

                const updateAndNotify = () => {
                    render();
                    const formattedValue = state.currentValue.map(tag => `[${tag}]`).join("");
                    App.handleValueChange(fieldId, formattedValue);
                };

                $container.on("click", ".template-tag-available", function() {
                    state.currentValue.push($(this).data("tag"));
                    updateAndNotify();
                });

                $container.on("click", ".template-tag-remove", function() {
                    const tagToRemove = $(this).data("tag");
                    state.currentValue = state.currentValue.filter(tag => tag !== tagToRemove);
                    updateAndNotify();
                });

                // Initial Render
                render();
            });
        },
        initCopyButtons: function() {
            $("#mjtc-main-panel").on("click", ".mjtc-copy-icon", function() {
                const textToCopy = $(this).parent().text().trim();
                navigator.clipboard.writeText(textToCopy).then(() => {
                    App.showToast("' . esc_js(__('Copied to clipboard', 'majestic-support')) . '", "info");
                }).catch(err => {
                    console.error("Could not copy text: ", err);
                });
            });
        },
        // --- HELPER FUNCTIONS ---
        updateSidebarActiveState: function(isSearching = false) {
            $("#mjtc-settings-nav a").each(function() {
                $(this).toggleClass("active", !isSearching && $(this).data("category") === App.activeCategory);
            });
        },

        updateSaveButtonState: function() {
            const hasChanges = Object.keys(this.changesToSubmit).length > 0;
            $("#mjtc-save-changes").prop("disabled", !hasChanges);
            $("#mjtc-discard-changes").toggle(hasChanges);
        },

        showToast: function(message, type = "success") {
            const icon = type === "success" ? "fa-check-circle" : "fa-info-circle";
            $("<div>", { class: `mjtc-toast ${type}`, html: `<i class="fas ${icon}"></i><span>${message}</span>`})
                .appendTo("#mjtc-toast-container")
                .on("animationend", function() { if ($(this).css("opacity") == 0) $(this).remove(); });
        },

        updateConditionalVisibility: function($wrapper) {
            const controllerId = $wrapper.data("conditionField");
            const $majesticsupport_controller = $(`#mjtc-${controllerId}`);
            let controllerValue = $majesticsupport_controller.is("[type=checkbox]") ? ($majesticsupport_controller.is(":checked") ? "1" : "0") : $majesticsupport_controller.val();
            const isVisible = String(controllerValue) === String($wrapper.data("conditionValue"));
            $wrapper.toggleClass("visible", isVisible);
        },

        updateAllConditionalFields: function() {
            $(".mjtc-conditional-field-wrapper").each(function() {
                App.updateConditionalVisibility($(this));
            });
        },

        // --- RENDER FUNCTIONS ---
        renderSidebar: function() {
            const $ul = $("<ul>");
            $.each(this.settingsConfig, (key, category) => {
                $("<li>").html(
                    $("<a>", { href: "#", "data-category": key, class: `mjtc-icon-class-${category.icon}`  })
                        //.append($("<i>", { class: `fas fa-${category.icon}` }))
                        .append($("<span>").text(category.label))
                ).appendTo($ul);
            });
            $("#mjtc-settings-nav").empty().append($ul);
        },

        renderSubNav: function(category) {
            const $wrapper = $("#mjtc-sticky-sub-nav-wrapper");
            const $container = $("#mjtc-sub-nav");
            const groupKeys = Object.keys(category.groups);

            if (groupKeys.length > 0) {
                const navLinks = groupKeys.map((key, index) =>
                    $("<a>", {
                        href: `#mjtc-group-${key}`,
                        class: `mjtc-sub-nav-link ${index === 0 ? "active" : ""}`,
                        text: category.groups[key].title
                    })
                );
                $container.empty().append($("<div>").append(navLinks));
                $wrapper.removeClass("mjtc-hidden");
            } else {
                $container.empty();
                $wrapper.addClass("mjtc-hidden");
            }
        },

        showCategory: function(categoryKey) {
            this.activeCategory = categoryKey;
            $(".mjtc-category-container").hide();
            $(`.mjtc-category-container[data-category-key="${categoryKey}"]`).show();
            $("#mjtc-category-title-span").text(this.settingsConfig[categoryKey].label);

            this.updateSidebarActiveState();
            this.renderSubNav(this.settingsConfig[categoryKey]);
            this.updateAllConditionalFields();
        },

        // --- EVENT HANDLERS ---
        handleSidebarNav: function(e) {
            e.preventDefault();
            $("#mjtc-main-panel-content").scrollTop(0);
            $("#mjtc-search-box").val("").trigger("input");
            App.showCategory($(e.currentTarget).data("category"));
            $("#mjtc-sidebar").removeClass("is-open");
        },

        handleSubNavScroll: function(e) {
            e.preventDefault();
            // Add these two lines to immediately highlight the clicked pill
            $("#mjtc-sub-nav a.mjtc-sub-nav-link").removeClass("active");
            $(e.currentTarget).addClass("active");
            const $majesticsupport_target = $($(e.currentTarget).attr("href"));
            if ($majesticsupport_target.length) {
                const $scrollContainer = $("#mjtc-main-panel-content");
                const stickyNavHeight = $("#mjtc-sticky-sub-nav-wrapper").outerHeight(true) || 0;
                const scrollTop = $scrollContainer.scrollTop() + $majesticsupport_target.position().top - stickyNavHeight - 16;
                $scrollContainer.stop().animate({ scrollTop }, 500);
            }
        },

        handleSearch: function(e) {
            const query = $(e.target).val().toLowerCase().trim();

            if (!query) {
                $("#mjtc-main-panel .mjtc-group-card, #mjtc-main-panel .mjtc-setting-row-wrapper").show();
                $("#mjtc-no-results-message").hide();
                App.showCategory(App.activeCategory);
                return;
            }

            $(".mjtc-category-container").show();
            $("#mjtc-category-title-span").text(`' . esc_js(__('Search results for', 'majestic-support')) . ' "${query}"`);
            $("#mjtc-sticky-sub-nav-wrapper").addClass("mjtc-hidden");
            let matchCount = 0;

            $(".mjtc-setting-row-wrapper").each(function() {
                const label = $(this).find("label").text().toLowerCase();
                const tooltip = $(this).find(".mjtc-tooltip-wrapper").data("tooltip")?.toLowerCase() || "";
                if (label.includes(query) || tooltip.includes(query)) {
                    $(this).show();
                    matchCount++;
                } else {
                    $(this).hide();
                }
            });

            $(".mjtc-group-card").each(function() {
                $(this).toggle($(this).find(".mjtc-setting-row-wrapper:visible").length > 0);
            });

            $(".mjtc-category-container").each(function() {
                $(this).toggle($(this).find(".mjtc-group-card:visible").length > 0);
            });

            $("#mjtc-no-results-message").toggle(matchCount === 0);
            App.updateSidebarActiveState(true);
        },

        handleFieldChange: function(e) {
            const $majesticsupport_target = $(e.target);
            const $wrapper = $majesticsupport_target.closest(".mjtc-setting-control");
            if (!$wrapper.length) return;

            const id = $wrapper.data("fieldId");
            let value;

            if ($majesticsupport_target.is("input[type=checkbox]")) {
                value = $majesticsupport_target.is(":checked") ? "1" : "0";
            } else if ($majesticsupport_target.is(".mjtc-btn-segment")) {
                value = $majesticsupport_target.data("value");
                $majesticsupport_target.addClass("active").siblings().removeClass("active");
                $wrapper.find(`#mjtc-${id}`).val(value);
            } else if ($majesticsupport_target.closest(".mjtc-redirect-control").length) {
                const $majesticsupport_select = $wrapper.find("select");
                const $customInput = $wrapper.find("input[type=text]");
                if ($majesticsupport_target.is("select")) { $customInput.toggleClass("mjtc-hidden", $majesticsupport_target.val() !== "custom"); }
                value = $majesticsupport_select.val() === "custom" ? $customInput.val() : $majesticsupport_select.val();
                $wrapper.find(`#mjtc-${id}`).val(value);
            } else if ($majesticsupport_target.data("action") === "upload") {
                $wrapper.find("input[type=file]").click(); return;
            } else if ($majesticsupport_target.is("input[type=file]")) {
                    // const file = e.target.files[0];
                    // const $majesticsupport_img = $wrapper.find("img.majesticsupport-config-default-image");
                    // const $removeInput = $wrapper.find("input[type=hidden][name=\'remove_default_image\']");
                    // const $removeBtn = $wrapper.find("#mjsupport-form-delete-image");
                    // const $majesticsupport_imageWrap = $wrapper.find(".mjsupport-form-image-wrp");

                    // if (file) {
                    //     const reader = new FileReader();
                    //     reader.onload = function(event) {
                    //         $majesticsupport_img.attr("src", event.target.result);
                    //         $majesticsupport_imageWrap.show();
                    //         if ($removeInput.length) {
                    //             $removeInput.val("0");
                    //         }
                    //         App.handleValueChange(id, event.target.result);
                    //     };
                    //     reader.readAsDataURL(file);
                    // }
                    return;
                } else if ($majesticsupport_target.data("action") === "remove" || $majesticsupport_target.is("#mjsupport-form-delete-image")) {
                    const $removeInput = $wrapper.find("input[type=hidden][name=\'remove_default_image\']");
                    if ($removeInput.length) {
                        $removeInput.val("1");
                    }
                        const $majesticsupport_imageWrap = $wrapper.find(".mjsupport-form-image-wrp");
                        $majesticsupport_imageWrap.hide();
                    // if (confirm("Remove this image?")) {
                    //     const $majesticsupport_img = $wrapper.find("img.majesticsupport-config-default-image");
                    //     const $fileInput = $wrapper.find("input[type=file]");
                    //     const $majesticsupport_imageWrap = $wrapper.find(".mjsupport-form-image-wrp");
                    //     const placeholder = "' . MJTC_PLUGIN_URL . 'includes/images/default_logo.png";

                    //     $majesticsupport_img.attr("src", placeholder);
                    //     $majesticsupport_imageWrap.hide();


                    //     // Safely reset file input without assigning value directly
                    //     if ($fileInput.length) {
                    //         $fileInput.val(null);
                    //     }

                    //     App.handleValueChange(id, "");
                    // }
                    return;
                } else {
                value = $majesticsupport_target.val();
            }
            App.handleValueChange(id, value);
        },

        handleValueChange: function(id, newValue) {
            this.changesToSubmit[id] = newValue;
            const $hiddenInput = $(`#mjtc-${id}`);
            if ($hiddenInput.length) $hiddenInput.val(newValue);

            $(`[data-field-id="${id}"]`).closest(".mjtc-setting-row").find(".mjtc-setting-label").addClass("is-modified");
            this.updateSaveButtonState();
            this.updateConditionalFields(id);
        },

        updateConditionalFields: function(changedFieldId) {
            const self = this;
            $(`.mjtc-conditional-field-wrapper[data-condition-field="${changedFieldId}"]`).each(function() {
                self.updateConditionalVisibility($(this));
            });
        },

        handleScrollSpy: function() {
            const $scrollContainer = $("#mjtc-main-panel-content");
            const $majesticsupport_groupCards = $(`.mjtc-category-container[data-category-key="${App.activeCategory}"] .mjtc-group-card:visible`);

            // Exit if theres no sub-navigation to update.
            if ($("#mjtc-sticky-sub-nav-wrapper").is(":hidden")) {
                return;
            }

            const stickyNavHeight = $("#mjtc-sticky-sub-nav-wrapper").outerHeight(true) || 0;
            const offset = stickyNavHeight + 24; // The "activation line" below the sticky nav.
            let currentSectionId = "";

            // First, try to find a section that is actively intersecting the activation line.
            // This is the most reliable way to find the current section.
            $majesticsupport_groupCards.each(function() {
                const cardTop = $(this).position().top;
                const cardBottom = cardTop + $(this).outerHeight();

                // If the line is between the cards top and bottom, this is our active section.
                if (cardTop <= offset && cardBottom > offset) {
                    currentSectionId = $(this).attr("id");
                    return false; // Exit the loop, we found the one.
                }
            });

            // If no section is intersecting the line (e.g., we are in a gap between sections),
            // fall back to the previous logic: find the last section that has scrolled past the line.
            if (!currentSectionId) {
                $majesticsupport_groupCards.each(function() {
                    if ($(this).position().top <= offset) {
                        currentSectionId = $(this).attr("id");
                    }
                });
            }

            // If still no section is found (we are at the very top), default to the first one.
            if (!currentSectionId && $majesticsupport_groupCards.length > 0) {
                currentSectionId = $majesticsupport_groupCards.first().attr("id");
            }

            // Now, update the active class on the correct pill.
            if (currentSectionId) {
                const $majesticsupport_newActiveLink = $(`#mjtc-sub-nav a.mjtc-sub-nav-link[href="#${currentSectionId}"]`);
                if (!$majesticsupport_newActiveLink.hasClass("active")) {
                    $("#mjtc-sub-nav a.mjtc-sub-nav-link").removeClass("active");
                    $majesticsupport_newActiveLink.addClass("active");
                }
            }
        },

        handleSave: function() {
            $("#majesticsupport-form").submit();
        },

        handleDiscard: function() { location.reload(); },

        addEventListeners: function() {
            $("#mjtc-settings-nav").on("click", "a", this.handleSidebarNav);
            $("#mjtc-sticky-sub-nav-wrapper").on("click", "a.mjtc-sub-nav-link", this.handleSubNavScroll);
            $("#mjtc-search-box").on("input", this.handleSearch);
            $("#mjtc-save-changes").on("click", this.handleSave);
            $("#mjtc-discard-changes").on("click", this.handleDiscard);
            $("#mjtc-mobile-menu-toggle, #mjtc-sidebar-tpclosebtn").on("click", () => $("#mjtc-sidebar").toggleClass("is-open"));
            const $majesticsupport_mainPanel = $("#mjtc-main-panel");
            $majesticsupport_mainPanel.on("change", "input, select, textarea", this.handleFieldChange);
            $majesticsupport_mainPanel.on("click", ".mjtc-btn-segment, [data-action=\'upload\'], .mjtc-btn-remove", this.handleFieldChange);
        },
    };

    App.init();

    jQuery("#mjsupport-form-delete-image").click(function(){
        jQuery(".mjsupport-form-image-wrp").slideUp("slow");
        jQuery("#remove_default_image").val(1);
    });


})(jQuery);
';
wp_add_inline_script('majestic-support-cmain-js',$majesticsupport_js);
?>
