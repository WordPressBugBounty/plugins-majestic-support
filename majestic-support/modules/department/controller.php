<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_departmentController {

    function __construct() {
        self::handleRequest();
    }

    function handleRequest() {
        $layout = MJTC_request::MJTC_getLayout('mjslay', null, 'departments');
        majesticsupport::$_data['sanitized_args']['MJTC_nonce'] = esc_html(wp_create_nonce('MJTC_nonce'));
        if (self::canaddfile($layout)) {
            switch ($layout) {
                case 'admin_departments':
                case 'departments':
                    majesticsupport::$_data['permission_granted'] = true;
                    if ( in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) {
                        majesticsupport::$_data['permission_granted'] = MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForTask('View Department');
                    }
                    if (majesticsupport::$_data['permission_granted']) {
                        MJTC_includer::MJTC_getModel('department')->getDepartments();
                    }
                    break;
                case 'admin_adddepartment':
                case 'adddepartment':
                    $id = MJTC_request::MJTC_getVar('majesticsupportid');
                    majesticsupport::$_data['permission_granted'] = true;
                    if ( in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) {
                        $per_task = ($id == null) ? 'Add Department' : 'Edit Department';
                        majesticsupport::$_data['permission_granted'] = MJTC_includer::MJTC_getModel('userpermissions')->MJTC_checkPermissionGrantedForTask($per_task);
                    }
                    if (majesticsupport::$_data['permission_granted'])
                        MJTC_includer::MJTC_getModel('department')->getDepartmentForForm($id);
                    break;
                default:
                    exit;
            }
            $module = (is_admin()) ? 'page' : 'mjsmod';
            $module = MJTC_request::MJTC_getVar($module, null, 'department');
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

    static function savedepartment() {
        $id = MJTC_request::MJTC_getVar('id');
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $nonce, 'save-department-'.$id) ) {
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
        $id = MJTC_request::MJTC_getVar('departmentid');
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $nonce, 'delete-department-'.$id) ) {
            die( 'Security check Failed' );
        }
        MJTC_includer::MJTC_getModel('department')->removeDepartment($id);
        if (is_admin()) {
            $MJTC_url = admin_url("admin.php?page=majesticsupport_department&mjslay=departments");
        } else {
            $MJTC_url = majesticsupport::makeUrl(array('mjsmod'=>'department', 'mjslay'=>'departments'));
        }
        wp_safe_redirect($MJTC_url);
        exit;
    }

    static function changestatus() {
        $id = MJTC_request::MJTC_getVar('departmentid');
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $nonce, 'change-status-'.$id) ) {
            die( 'Security check Failed' );
        }
        MJTC_includer::MJTC_getModel('department')->changeStatus($id);
        $MJTC_url = admin_url("admin.php?page=majesticsupport_department&mjslay=departments");
        $MJTC_pagenum = MJTC_request::MJTC_getVar('pagenum');
        if ($MJTC_pagenum)
            $MJTC_url .= '&pagenum=' . $MJTC_pagenum;
        wp_safe_redirect($MJTC_url);
        exit;
    }

    static function changedefault() {
        $id = MJTC_request::MJTC_getVar('departmentid');
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $nonce, 'change-default-'.$id) ) {
            die( 'Security check Failed' );
        }
        $MJTC_default = MJTC_request::MJTC_getVar('default',null,0);
        MJTC_includer::MJTC_getModel('department')->changeDefault($id,$MJTC_default);
        $MJTC_url = admin_url("admin.php?page=majesticsupport_department&mjslay=departments");
        $MJTC_pagenum = MJTC_request::MJTC_getVar('pagenum');
        if ($MJTC_pagenum)
            $MJTC_url .= '&pagenum=' . $MJTC_pagenum;
        wp_safe_redirect($MJTC_url);
        exit;
    }

    static function ordering() {
        $id = MJTC_request::MJTC_getVar('departmentid');
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $nonce, 'ordering-'.$id) ) {
            die( 'Security check Failed' );
        }
        MJTC_includer::MJTC_getModel('department')->setOrdering($id);
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
