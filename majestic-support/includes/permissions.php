<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_permissions {

    static function MJTC_checkPermission($MJTC_userid, $MJTC_permissionfor) {
        if(!is_numeric($MJTC_userid)){
            return false;
        }
        $MJTC_query = "SELECT perm_allowed.status
					FROM `" . majesticsupport::$_db->prefix . "jsjobs_permissions` AS perm
					JOIN `" . majesticsupport::$_db->prefix . "jsjobs_permissions_allowed` AS perm_allowed ON perm_allowed.permissionid = perm.id
					WHERE perm.permissions = '".esc_sql($MJTC_permissionfor)."' AND perm_allowed.userid = ".esc_sql($MJTC_userid);
        $MJTC_result = majesticsupport::$_db->get_var($MJTC_query);
        return $MJTC_result;
    }

}

?>
