<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_departmentController {

    function __construct() {
        self::handleRequest();
    }

    function handleRequest() {
        $MJTC_layout = MJTC_request::MJTC_getLayout('mjslay', null, 'departments');
        if (self::canaddfile($MJTC_layout)) {
            switch ($MJTC_layout) {
                case 'admin_departments':
                case 'departments':
                    // Administrators, or add-on staff granted the task. Nobody else.
                    majesticsupport::$_data['permission_granted'] = MJTC_access::MJTC_staffMay('View Department');
                    if (majesticsupport::$_data['permission_granted']) {
                        MJTC_includer::MJTC_getModel('department')->getDepartments();
                    }
                    break;
                case 'admin_adddepartment':
                case 'adddepartment':
                    $MJTC_id = MJTC_request::MJTC_getVar('majesticsupportid');
                    // A department record carries its outgoing mail account:
                    // administrators, or add-on staff granted the task.
                    $MJTC_per_task = ($MJTC_id == null) ? 'Add Department' : 'Edit Department';
                    majesticsupport::$_data['permission_granted'] = MJTC_access::MJTC_staffMay($MJTC_per_task);
                    if (majesticsupport::$_data['permission_granted'])
                        MJTC_includer::MJTC_getModel('department')->getDepartmentForForm($MJTC_id);
                    break;
                default:
                    exit;
            }
            $MJTC_module = (is_admin()) ? 'page' : 'mjsmod';
            $MJTC_module = MJTC_request::MJTC_getVar($MJTC_module, null, 'department');
            $MJTC_module = MJTC_majesticsupportphplib::MJTC_str_replace('majesticsupport_', '', $MJTC_module);
            MJTC_includer::MJTC_include_file($MJTC_layout, $MJTC_module);
        }
    }

    function canaddfile($MJTC_layout) {
        // Decides only whether a layout is rendered: never while a task is being
        // dispatched, never an admin_ layout on the front end. Who may see a layout
        // is decided in handleRequest(). (The nonce once checked here was created
        // by the same request, so it could not fail.)
        {
            if (isset($_POST['form_request']) && $_POST['form_request'] == 'majesticsupport') {
                return false;
            } elseif (isset($_GET['action']) && $_GET['action'] == 'mstask') {
                return false;
            } else {
                if(!is_admin() && MJTC_majesticsupportphplib::MJTC_strpos($MJTC_layout, 'admin_') === 0){
                    return false;
                }
                return true;
            }
        }
    }

    static function savedepartment() {
        $MJTC_id = MJTC_request::MJTC_getVar('id');
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'save-department-'.$MJTC_id) ) {
            die( 'Security check Failed' );
        }
        $MJTC_data = MJTC_request::get('post');
        MJTC_includer::MJTC_getModel('department')->storeDepartment($MJTC_data);
        if (is_admin()) {
            $MJTC_url = admin_url("admin.php?page=majesticsupport_department&mjslay=departments");
        } else {
            $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'department', 'mjslay'=>'departments'));
        }
        wp_safe_redirect($MJTC_url);
        exit;
    }

    static function deletedepartment() {
        $MJTC_id = MJTC_request::MJTC_getVar('departmentid');
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'delete-department-'.$MJTC_id) ) {
            die( 'Security check Failed' );
        }
        MJTC_includer::MJTC_getModel('department')->removeDepartment($MJTC_id);
        if (is_admin()) {
            $MJTC_url = admin_url("admin.php?page=majesticsupport_department&mjslay=departments");
        } else {
            $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'department', 'mjslay'=>'departments'));
        }
        wp_safe_redirect($MJTC_url);
        exit;
    }

    static function changestatus() {
        $MJTC_id = MJTC_request::MJTC_getVar('departmentid');
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'change-status-'.$MJTC_id) ) {
            die( 'Security check Failed' );
        }
        MJTC_includer::MJTC_getModel('department')->changeStatus($MJTC_id);
        $MJTC_url = admin_url("admin.php?page=majesticsupport_department&mjslay=departments");
        $MJTC_pagenum = MJTC_request::MJTC_getVar('pagenum');
        if ($MJTC_pagenum)
            $MJTC_url .= '&pagenum=' . $MJTC_pagenum;
        wp_safe_redirect($MJTC_url);
        exit;
    }

    static function changedefault() {
        $MJTC_id = MJTC_request::MJTC_getVar('departmentid');
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'change-default-'.$MJTC_id) ) {
            die( 'Security check Failed' );
        }
        $MJTC_default = MJTC_request::MJTC_getVar('default',null,0);
        MJTC_includer::MJTC_getModel('department')->changeDefault($MJTC_id,$MJTC_default);
        $MJTC_url = admin_url("admin.php?page=majesticsupport_department&mjslay=departments");
        $MJTC_pagenum = MJTC_request::MJTC_getVar('pagenum');
        if ($MJTC_pagenum)
            $MJTC_url .= '&pagenum=' . $MJTC_pagenum;
        wp_safe_redirect($MJTC_url);
        exit;
    }

    static function ordering() {
        $MJTC_id = MJTC_request::MJTC_getVar('departmentid');
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'ordering-'.$MJTC_id) ) {
            die( 'Security check Failed' );
        }
        MJTC_includer::MJTC_getModel('department')->setOrdering($MJTC_id);
        $MJTC_pagenum = MJTC_request::MJTC_getVar('pagenum');
        $MJTC_url = "admin.php?page=majesticsupport_department&mjslay=departments";
        if ($MJTC_pagenum)
            $MJTC_url .= '&pagenum=' . $MJTC_pagenum;
        wp_safe_redirect($MJTC_url);
        exit;
    }

}

$MJTC_departmentController = new MJTC_departmentController();
?>
