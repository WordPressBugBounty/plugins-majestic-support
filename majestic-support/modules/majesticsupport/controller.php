<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_majesticsupportController {

    function __construct() {
        self::handleRequest();
    }

    function handleRequest() {
        $layout = MJTC_request::MJTC_getLayout('mjslay', null, 'controlpanel');
        majesticsupport::$_data['sanitized_args']['MJTC_nonce'] = esc_html(wp_create_nonce('MJTC_nonce'));
        if (self::canaddfile($layout)) {
            switch ($layout) {
                case 'admin_controlpanel':
			        include_once MJTC_PLUGIN_PATH . 'includes/updates/updates.php';
			        MJTC_updates::MJTC_checkUpdates();
                    MJTC_includer::MJTC_getModel('majesticsupport')->getControlPanelDataAdmin();
                    break;
                case 'controlpanel':
                    MJTC_includer::MJTC_getModel('majesticsupport')->getControlPanelData();
                    include_once MJTC_PLUGIN_PATH . 'includes/updates/updates.php';
                    MJTC_updates::MJTC_checkUpdates('110');
                    MJTC_includer::MJTC_getModel('majesticsupport')->updateColorFile();
                    break;
                case 'admin_shortcodes':
                    MJTC_includer::MJTC_getModel('majesticsupport')->getShortCodeData();
                    break;
                case 'admin_aboutus':
                    break;
                case 'admin_help':
                    break;
                case 'admin_translations':
                    break;
                case 'login':
                    break;
                case 'userregister':
                    break;
                default:
                    exit;
            }
            $module = (is_admin()) ? 'page' : 'mjsmod';
            $module = MJTC_request::MJTC_getVar($module, null, 'majesticsupport');
            $module = MJTC_majesticsupportphplib::MJTC_str_replace('majesticsupport_', '', $module);
            MJTC_includer::MJTC_include_file($layout, $module);
        }
    }

    function canaddfile($layout) {
        $nonce_value = MJTC_request::MJTC_getVar('MJTC_nonce');
        if ( wp_verify_nonce( $nonce_value, 'MJTC_nonce') ) {
            if (isset($_POST['form_request']) && $_POST['form_request'] == 'majesticsupport') {
                return false;
            } elseif (isset($_GET['action']) && $_GET['action'] == 'mstask') {
                return false;
            } else {
                if(!is_admin() && MJTC_majesticsupportphplib::MJTC_strpos($layout, 'admin_') === 0){
                    return false;
                }
                return true;
            }
        }
    }

    static function addmissingusers() {
        if(!is_admin())
            return false;
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $nonce, 'add-missing-users') ) {
            die( 'Security check Failed' );
        }
        MJTC_includer::MJTC_getModel('majesticsupport')->addMissingUsers();
        $MJTC_url = admin_url("admin.php?page=majesticsupport");
        wp_safe_redirect($MJTC_url);
        exit;
    }

    function saveordering(){
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $nonce, 'save-ordering') ) {
            die( 'Security check Failed' );
        }
        $post = MJTC_request::get('post');

        MJTC_includer::MJTC_getModel('majesticsupport')->storeOrderingFromPage($post);
        if($post['ordering_for'] == 'department'){
            if (is_admin()) {
                $MJTC_url = admin_url("admin.php?page=majesticsupport_department&mjslay=departments");
            } else {
                $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'department', 'mjslay'=>'departments'));
            }
        }elseif($post['ordering_for'] == 'priority'){
            if (is_admin()) {
                $MJTC_url = admin_url("admin.php?page=majesticsupport_priority&mjslay=priorities");
            } else {
                $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'priority', 'mjslay'=>'priorities'));
            }
        }elseif($post['ordering_for'] == 'status'){
            if (is_admin()) {
                $MJTC_url = admin_url("admin.php?page=majesticsupport_status&mjslay=statuses");
            }
        }elseif($post['ordering_for'] == 'product'){
            if (is_admin()) {
                $MJTC_url = admin_url("admin.php?page=majesticsupport_product&mjslay=products");
            }
        }elseif($post['ordering_for'] == 'fieldordering'){
            $fieldfor = MJTC_request::MJTC_getVar('fieldfor');
            if($fieldfor == ''){
                $fieldfor = majesticsupport::$_data['fieldfor'];
            }
            $formid = MJTC_request::MJTC_getVar('formid');
            if($formid == ''){
                $formid = majesticsupport::$_data['formid'];
            }
            $MJTC_url = admin_url("admin.php?page=majesticsupport_fieldordering&mjslay=fieldordering&fieldfor=".esc_attr($fieldfor)."&formid=".esc_attr($formid));
        }elseif($post['ordering_for'] == 'announcement'){
            if (is_admin()) {
            $MJTC_url = admin_url("admin.php?page=majesticsupport_announcement&mjslay=announcements");
        } else {
            $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'announcement', 'mjslay'=>'staffannouncements'));
        }
        }elseif($post['ordering_for'] == 'article'){
            if (is_admin()) {
                $MJTC_url = admin_url("admin.php?page=majesticsupport_knowledgebase&mjslay=listarticles");
            } else {
                $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'knowledgebase', 'mjslay'=>'stafflistarticles'));
            }
        }elseif($post['ordering_for'] == 'download'){
            if (is_admin()) {
                $MJTC_url = admin_url("admin.php?page=majesticsupport_download&mjslay=downloads");
            } else {
                $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'download', 'mjslay'=>'staffdownloads'));
            }
        }elseif($post['ordering_for'] == 'faq'){
            if (is_admin()) {
                $MJTC_url = admin_url("admin.php?page=majesticsupport_faq&mjslay=faqs");
            } else {
                $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'faq', 'mjslay'=>'stafffaqs'));
            }
        }elseif($post['ordering_for'] == 'helptopic'){
            if (is_admin()) {
                $MJTC_url = admin_url("admin.php?page=majesticsupport_helptopic&mjslay=helptopics");
            } else {
                $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'helptopic', 'mjslay'=>'agenthelptopics'));
            }
        }elseif($post['ordering_for'] == 'multiform'){
            if (is_admin()) {
                $MJTC_url = admin_url("admin.php?page=majesticsupport_multiform&msjlay=multiform");
            } else {
                $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'multiform', 'mjslay'=>'staffmultiform'));
            }
        }elseif($post['ordering_for'] == 'ticketclosereason'){
            if (is_admin()) {
                $MJTC_url = admin_url("admin.php?page=majesticsupport_ticketclosereason&mjslay=ticketclosereasons");
            } else {
                $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'ticketclosereason', 'mjslay'=>'agentticketclosereasons'));
            }
        }

        wp_safe_redirect($MJTC_url);
        exit;
    }
}

$MJTC_controlpanelController = new MJTC_majesticsupportController();
?>
