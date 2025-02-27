<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_permissions {

    static function MJTC_checkPermission($userid, $permissionfor) {
        if(!is_numeric($userid)){
            return false;
        }
        $query = "SELECT perm_allowed.status
					FROM `" . majesticsupport::$_db->prefix . "jsjobs_permissions` AS perm
					JOIN `" . majesticsupport::$_db->prefix . "jsjobs_permissions_allowed` AS perm_allowed ON perm_allowed.permissionid = perm.id
					WHERE perm.permissions = '".esc_sql($permissionfor)."' AND perm_allowed.userid = ".esc_sql($userid);
        $result = majesticsupport::$_db->get_var($query);
        return $result;
    }

}

?>
