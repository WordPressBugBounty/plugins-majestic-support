<?php

if (!defined('ABSPATH'))
    die('Restricted Access');
if (majesticsupport::$_config['show_header'] != 1)
    return false;
$MJTC_isUserStaff = false;
if (in_array('agent', majesticsupport::$_active_addons)) {
    $MJTC_isUserStaff = MJTC_includer::MJTC_getModel('agent')->isUserStaff();
}
$MJTC_div = '';
$MJTC_headertitle = '';
$MJTC_editid = MJTC_request::MJTC_getVar('majesticsupportid');
$MJTC_isnew = ($MJTC_editid == null) ? true : false;
$MJTC_array[] = array('link' => majesticsupport::makeUrl(array('mjsmod' => 'majesticsupport', 'mjslay' => 'controlpanel')), 'text' => esc_html(__('Control Panel', 'majestic-support')));
$MJTC_module = MJTC_request::MJTC_getVar('mjsmod', null, 'majesticsupport');
$MJTC_layout = MJTC_request::MJTC_getVar('mjslay', null, 'controlpanel');

//Layout variy for Staff Member and User
if ($MJTC_isUserStaff) {
    $MJTC_linkname = 'staff';
    $MJTC_myticket = 'staffmyticket';
    $MJTC_addticket = 'staffaddticket';
    $MJTC_announcements = 'staffannouncements';
    $MJTC_downloads = 'staffdownloads';
    $MJTC_adddownload = 'adddownload';
    $MJTC_faqs = 'stafffaqs';
    $MJTC_addfaq = 'addfaq';
    $MJTC_addcategory = 'addcategory';
    $MJTC_categories = 'stafflistarticles';
    $MJTC_addarticle = 'addarticle';
    $MJTC_articles = 'stafflistarticles';
    $MJTC_addannouncement = 'addannouncement';
    $MJTC_login = 'login';
} else {
    $MJTC_linkname = 'user';
    $MJTC_myticket = 'myticket';
    $MJTC_addticket = 'addticket';
    $MJTC_categories = 'userknowledgebase';
    $MJTC_announcements = 'announcements';
    $MJTC_downloads = 'downloads';
    $MJTC_faqs = 'faqs';
    $MJTC_login = 'login';
}
$MJTC_svgs = array(
    'dashboard' => '<svg viewBox="0 0 24 24" width="16" height="16" class="outline-icon">
        <path d="M3 3h7v7H3zM14 3h7v7h-7zM14 14h7v7h-7zM3 14h7v7H3z"/>
    </svg>',

    'submit_ticket' => '<svg viewBox="0 0 24 24" width="16" height="16" class="outline-icon">
        <path d="M12 5v14M5 12h14"/>
    </svg>',

    'my_ticket' => '<svg viewBox="0 0 24 24" width="16" height="16" class="outline-icon">
        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
        <polyline points="22,6 12,13 2,6"/>
    </svg>',
);

$MJTC_flage = true;
if (majesticsupport::$_config['tplink_home_' . $MJTC_linkname] == 1) {
    $MJTC_linkarray[] = array(
        'class' => 'mjtc-support-homeclass',
        'link' => majesticsupport::makeUrl(array('mjsmod' => 'majesticsupport', 'mjslay' => 'controlpanel')),
        'title' => esc_html(__('Dashboard', 'majestic-support')),
        'mjsmod' => '',
        'imgtitle' => 'Dashboard-icon',
        'svg' => $MJTC_svgs['dashboard'],
        'active_mod' => 'majesticsupport',
        'active_lay' => 'controlpanel',
    );
    $MJTC_flage = false;
}
if (majesticsupport::$_config['tplink_openticket_' . $MJTC_linkname] == 1) {
    // $MJTC_module = $MJTC_isUserStaff ? 'agent' : 'ticket';
    $MJTC_linkarray[] = array(
        'class' => 'mjtc-support-openticketclass',
        'link' => majesticsupport::makeUrl(array('mjsmod' => $MJTC_module, 'mjslay' => $MJTC_addticket)),
        'title' => esc_html(__('Submit Ticket', 'majestic-support')),
        'mjsmod' => 'ticket',
        'imgtitle' => 'Submit Ticket',
        'svg' => $MJTC_svgs['submit_ticket'],
        'active_mod' => $MJTC_module,
        'active_lay' => $MJTC_addticket,
    );
    $MJTC_flage = false;
}
if (majesticsupport::$_config['tplink_tickets_' . $MJTC_linkname] == 1) {
    // $MJTC_module = $MJTC_isUserStaff ? 'agent' : 'ticket';
    $MJTC_linkarray[] = array(
        'class' => 'mjtc-support-myticket ',
        'link' => majesticsupport::makeUrl(array('mjsmod' => $MJTC_module, 'mjslay' => $MJTC_myticket)),
        'title' => esc_html(__('My Tickets', 'majestic-support')),
        'mjsmod' => 'ticket',
        'imgtitle' => 'My Tickets',
        'svg' => $MJTC_svgs['my_ticket'],
        'active_mod' => $MJTC_module,
        'active_lay' => $MJTC_myticket,
    );
    $MJTC_flage = false;
}
if (majesticsupport::$_config['tplink_login_logout_' . $MJTC_linkname] == 1) {
    $MJTC_flage = false;
}
if (majesticsupport::$_config['tplink_profile_' . $MJTC_linkname] == 1) {
    $MJTC_flage = false;
}

$MJTC_userId = MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid();
$MJTC_userName = MJTC_includer::MJTC_getObjectClass('user')->MJTC_fullname();
$MJTC_userEmail = MJTC_includer::MJTC_getObjectClass('user')->MJTC_emailaddress();
$MJTC_extramargin = '';
$MJTC_displayhidden = '';
if ($MJTC_flage)
    $MJTC_displayhidden = 'display:none;';
$MJTC_div .= '
		<div id="ms-header-main-wrapper" style="' . esc_attr($MJTC_displayhidden) . '">';
$MJTC_div .= '<div id="ms-header" class="' . esc_attr($MJTC_extramargin) . '" >';
$MJTC_div .= '<div id="ms-tabs-wrp" class="" >
<div class="ms-tabs-profile-wrp" >';
if (majesticsupport::$_config['tplink_profile_' . $MJTC_linkname] == 1) {
    $MJTC_div .= '
    
        <div class="ms-profile-img-wrp">
            '. wp_kses(MJTC_get_avatar($MJTC_userId), MJTC_ALLOWED_TAGS) .'
        </div>
        <div class="ms-profile-name-wrp">
            <span class="ms-profile-name">';
                if (!is_user_logged_in()) {
                    $MJTC_div .=  __('Hello Visitor!', 'majestic-support');
                } else {
                    $MJTC_div .=  $MJTC_userName;
                }
                $MJTC_div .= '
            </span>
            <span class="ms-profile-email">
                ' . esc_html($MJTC_userEmail) . '
            </span>
        </div>
    ';
}
$MJTC_div .= '</div>';
$MJTC_id = '';
if (isset($MJTC_linkarray)) {
    $MJTC_div .= '<div class="ms-tabs-menu-wrp">';
    foreach ($MJTC_linkarray as $MJTC_link) {

        $MJTC_id = '';
        if (
            in_array('multiform', majesticsupport::$_active_addons) &&
            majesticsupport::$_config['show_multiform_popup'] == 1 &&
            $MJTC_link['class'] === 'mjtc-support-openticketclass'
        ) {
            $MJTC_id = 'id="multiformpopup"';
        }

        // ACTIVE STATE CHECK
        $MJTC_active_class = '';
        if (
            isset($MJTC_link['active_mod'], $MJTC_link['active_lay']) &&
            $MJTC_module === $MJTC_link['active_mod'] &&
            $MJTC_layout === $MJTC_link['active_lay']
        ) {
            $MJTC_active_class = ' mjtc-support-ticketsclassctive';
        }

        $MJTC_div .= '
            <div class="ms-header-tab ' . esc_attr($MJTC_link['class'] . $MJTC_active_class) . '">
                <a ' . $MJTC_id . ' class="mjtc-cp-menu-link" href="' . esc_url($MJTC_link['link']) . '">
                    ' . $MJTC_link['svg'] . '
                    ' . esc_html($MJTC_link['title']) . '
                </a>
            </div>';
    }
    $MJTC_div .= '</div>';
}
if (majesticsupport::$_config['tplink_login_logout_' . $MJTC_linkname] == 1) {
    $MJTC_div .= '<div class="ms-tabs-menu-rightwrp">';
    $MJTC_loginval = MJTC_includer::MJTC_getModel('configuration')->getConfigValue('set_login_link');
    $MJTC_loginlink = MJTC_includer::MJTC_getModel('configuration')->getConfigValue('login_link');
    if ($MJTC_loginval == 3){
        $MJTC_hreflink = wp_login_url();
    }
    else if ($MJTC_loginval == 2 && $MJTC_loginlink != "") {
        $MJTC_hreflink = $MJTC_loginlink;
    } else {
        $MJTC_hreflink = majesticsupport::makeUrl(array('mjsmod' => 'majesticsupport', 'mjslay' => 'login'));
    }
    if (MJTC_includer::MJTC_getObjectClass('user')->MJTC_isguest()) {
        $title = esc_html(__('Login', 'majestic-support'));
    } else {
        $title = esc_html(__('Log out', 'majestic-support'));
        $MJTC_hreflink = wp_logout_url(majesticsupport::makeUrl(array('mjsmod' => 'majesticsupport', 'mjslay' => 'controlpanel')));

        if (isset($_COOKIE['majesticsupport-socialmedia']) && !empty($_COOKIE['majesticsupport-socialmedia'])) {
            switch ($_COOKIE['majesticsupport-socialmedia']) {
                case 'facebook':
                    $MJTC_hreflink = majesticsupport::makeUrl(array('mjsmod' => 'sociallogin', 'task' => 'logout', 'action' => 'mstask', 'media' => 'facebook', 'mspageid' => majesticsupport::getPageid()));
                    break;
                case 'linkedin':
                    $MJTC_hreflink = majesticsupport::makeUrl(array('mjsmod' => 'sociallogin', 'task' => 'logout', 'action' => 'mstask', 'media' => 'linkedin', 'mspageid' => majesticsupport::getPageid()));
                    break;
                default:
                    $MJTC_hreflink =  $MJTC_hreflink = wp_logout_url(majesticsupport::makeUrl(array('mjsmod' => 'majesticsupport', 'mjslay' => 'controlpanel')));
                    break;
            }
        }
    }
    $MJTC_div .= '<div class="ms-header-tab mjtc-support-loginlogoutclass">
                <a '.esc_attr($MJTC_id).' class="mjtc-cp-menu-link" href="' . esc_url($MJTC_hreflink) . '">
                    <svg viewBox="0 0 24 24" width="14" height="14" class="outline-icon"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                    ' . esc_html($title) . '
                </a>
            </div>';
    $MJTC_div .= '</div>';
}
$MJTC_div .= '</div></div>
        <div class="mjtc-transparent-header"></div>
    </div>';
echo wp_kses($MJTC_div, MJTC_ALLOWED_TAGS);
// Moved this code to a dedicated file to prevent errors when the header is hidden.
if(in_array('multiform', majesticsupport::$_active_addons)){
    include_once(MJTC_PLUGIN_PATH . 'includes/multiformpopup.php');
}
?>
