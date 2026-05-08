<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_includer {

    function __construct() {

    }

    /*
     * Includes files
     */

    public static function MJTC_include_file($MJTC_filename, $MJTC_module_name = null) {
        $MJTC_module_name = MJTC_majesticsupportphplib::MJTC_clean_file_path($MJTC_module_name);
        $MJTC_filename = MJTC_majesticsupportphplib::MJTC_clean_file_path($MJTC_filename);
        if ($MJTC_module_name != null) {
            $MJTC_file_path = MJTC_includer::MJTC_getPluginPath($MJTC_module_name,'file',$MJTC_filename);
            if (!is_admin() && file_exists(MJTC_PLUGIN_PATH . 'includes/css/style.css')) {
                wp_enqueue_style('majesticsupport-main-css', MJTC_PLUGIN_URL . 'includes/css/style.css', array(), '1.0.0');
            }
			if (locate_template('majestic-support/' . $MJTC_module_name . '-' . $MJTC_filename . '.php', 1, 1)) {
			   return;
			}

            if(file_exists($MJTC_file_path)){
                include_once $MJTC_file_path;
            }else{
                $MJTC_file_path = MJTC_includer::MJTC_getPluginPath('premiumplugin','file','missingaddon');
                include_once $MJTC_file_path;
            }
        } else {
            $MJTC_file_path = MJTC_includer::MJTC_getPluginPath($MJTC_filename,'file');
            if(file_exists($MJTC_file_path)){
                include_once $MJTC_file_path;
            }else{
                $MJTC_file_path = MJTC_includer::MJTC_getPluginPath('premiumplugin','file');
                include_once $MJTC_file_path;
            }
        }
        return;
    }

    /*
     * Static function to handle the page slugs
     */

    public static function MJTC_include_slug($MJTC_page_slug) {
        include_once MJTC_PLUGIN_PATH . 'modules/majestic-support-controller.php';
    }

    /*
     * Static function for the model object
     */

    public static function MJTC_getModel($modelname) {
        $MJTC_file_path = MJTC_includer::MJTC_getPluginPath($modelname,'model');
        include_once $MJTC_file_path;
        $MJTC_classname = "MJTC_" . $modelname . 'Model';
        $MJTC_obj = new $MJTC_classname();
        return $MJTC_obj;
    }

    /*
     * Static function for the classes objects
     */

    public static function MJTC_getObjectClass($MJTC_classname) {
        $MJTC_file_path = MJTC_includer::MJTC_getPluginPath($MJTC_classname,'class');

        include_once $MJTC_file_path;
        $MJTC_classname = 'MJTC_'.esc_attr($MJTC_classname);
        $MJTC_obj = new $MJTC_classname();
        return $MJTC_obj;
    }

    public static function MJTC_getClassesInclude($MJTC_classname) {
        $MJTC_file_path = MJTC_includer::MJTC_getPluginPath($MJTC_classname,'class');
        include_once $MJTC_file_path;
    }

    /*
     * Static function for the controller object
     */

    public static function MJTC_getController($MJTC_controllername) {
        $MJTC_file_path = MJTC_includer::MJTC_getPluginPath($MJTC_controllername,'controller');

        include_once $MJTC_file_path;
        $MJTC_classname = "MJTC_".$MJTC_controllername . "Controller";
        $MJTC_obj = new $MJTC_classname();
        return $MJTC_obj;
    }

    /*
     * Static function for the Table Class Object
     */

    public static function MJTC_getTable($tableclass) {
        $MJTC_file_path = MJTC_includer::MJTC_getPluginPath($tableclass,'table');
        require_once MJTC_PLUGIN_PATH . 'includes/tables/table.php';
        include_once $MJTC_file_path;
        $MJTC_classname = "MJTC_" . $tableclass . 'Table';
        $MJTC_obj = new $MJTC_classname();
        return $MJTC_obj;
    }

    /*
     *  Identify file path to include or require this fucntion helps to accommodate addon calls
     */

    public static function MJTC_getPluginPath($MJTC_module,$type,$MJTC_file_name = '') {
        $MJTC_module = MJTC_majesticsupportphplib::MJTC_clean_file_path($MJTC_module);
        $MJTC_file_name = MJTC_majesticsupportphplib::MJTC_clean_file_path($MJTC_file_name);

        $MJTC_addons_secondry = array('articles','articleattachmet','banemaillog','downloadattachment','roleaccessdepartments','rolepermissions','useraccessdepartments','userpermissions', 'role', 'acl_roles', 'acl_role_access_departments', 'acl_role_permissions', 'categories' ,'email_banlist', 'acl_user_access_departments','articles_attachments','email_banlist','acl_user_permissions', 'facebook', 'linkedin','socialUser');
		$MJTC_new_addon_entry = "";
		$MJTC_new_addon_entry = apply_filters('ms_ticket_include_thirdparty_addon_in_array',$MJTC_addons_secondry);
		if($MJTC_new_addon_entry){
			$MJTC_addons_secondry[] = $MJTC_new_addon_entry;
		}
		$MJTC_new_addon_layoutname = "";
		$MJTC_new_addon_layoutname = apply_filters('ms_ticket_include_thirdparty_addon_layoutname',false);

        if(in_array($MJTC_module, majesticsupport::$_active_addons)){
            $MJTC_path = WP_PLUGIN_DIR.'/'.'majestic-support-'.$MJTC_module.'/';
            switch ($type) {
                case 'file':
                    if($MJTC_file_name != ''){
                        $MJTC_file_path = $MJTC_path . 'module/tpls/' . $MJTC_file_name . '.php';
                    }else{
                        $MJTC_file_path = $MJTC_path . 'module/controller.php';
                    }
                    break;
                case 'model':
                    $MJTC_file_path = $MJTC_path . 'module/model.php';
                    break;
                case 'class':
                    $MJTC_file_path = $MJTC_path . 'classes/' . $MJTC_module . '.php';
                    break;
                case 'controller':
                    $MJTC_file_path = $MJTC_path . 'module/controller.php';
                    break;
                case 'table':
                    $MJTC_file_path = $MJTC_path . 'includes/' . $MJTC_module . '-table.php';
                    break;
            }

        }elseif(in_array($MJTC_module, $MJTC_addons_secondry)){ // to handle the case of modules that are submodules for some addon
            $MJTC_parent_module = '';
            switch ($MJTC_module) {// to identify addon for submodules.
                case 'articles':
                case 'articleattachmet':
                case 'articles_attachments':
                case 'categories':
                    $MJTC_parent_module = 'knowledgebase';
                    break;
                case 'banemaillog':
                case 'email_banlist':
                case 'email_banlist':
                    $MJTC_parent_module = 'banemail';
                    break;
                case 'downloadattachment':
                    $MJTC_parent_module = 'download';
                    break;
                case 'roleaccessdepartments':
                case 'rolepermissions':
                case 'useraccessdepartments':
                case 'userpermissions':
                case 'role':
                case 'acl_roles':
                case 'acl_role_access_departments':
                case 'acl_user_access_departments':
                case 'acl_role_permissions':
                case 'acl_user_permissions':
                    $MJTC_parent_module = 'agent';
                    break;
                case 'facebook':
                case 'linkedin':
                case 'socialUser':
                    $MJTC_parent_module = 'sociallogin';
                    break;
                case $MJTC_new_addon_entry:
                    $MJTC_parent_module = $MJTC_new_addon_layoutname;
            }

            $MJTC_path = WP_PLUGIN_DIR.'/'.'majestic-support-'.$MJTC_parent_module.'/';
            if(in_array($MJTC_parent_module, majesticsupport::$_active_addons)){
                switch ($type) {
                    case 'file':
                        if($MJTC_file_name != ''){
                            $MJTC_file_path = $MJTC_path . $MJTC_module.'/tpls/' . $MJTC_file_name . '.php';
                        }else{
                            $MJTC_file_path = $MJTC_path . $MJTC_module.'/controller.php';
                        }
                        break;
                    case 'model':
                        $MJTC_file_path = $MJTC_path . $MJTC_module.'/model.php';
                        break;

                    case 'class':
                        $MJTC_file_path = $MJTC_path . 'classes/' . $MJTC_module . '.php';
                        break;
                    case 'controller':
                        $MJTC_file_path = $MJTC_path . $MJTC_module.'/controller.php';
                        break;
                    case 'table':
                        $MJTC_file_path = $MJTC_path . 'includes/' . $MJTC_module . '-table.php';
                        break;
                }
            }else{
                $MJTC_file_path = MJTC_includer::MJTC_getPluginPath('premiumplugin','file');
            }
        }else{
            $MJTC_path = MJTC_PLUGIN_PATH;
            switch ($type) {
                case 'file':
                    if($MJTC_file_name != ''){
                        $MJTC_file_path = $MJTC_path . 'modules/' . $MJTC_module . '/tpls/' . $MJTC_file_name . '.php';
                    }else{
                        $MJTC_file_path = $MJTC_path . 'modules/' . $MJTC_module . '/controller.php';
                    }
                    break;
                case 'model':
                        $MJTC_file_path = $MJTC_path . 'modules/' . $MJTC_module . '/model.php';
                    break;

                case 'class':
                    $MJTC_file_path = $MJTC_path . 'includes/classes/' . $MJTC_module . '.php';
                    break;
                case 'controller':
                        $MJTC_file_path = $MJTC_path . 'modules/' . $MJTC_module . '/controller.php';
                    break;
                case 'table':
                    $MJTC_file_path = $MJTC_path . 'includes/tables/' . $MJTC_module . '.php';;
                    break;
            }
        }
        return $MJTC_file_path;
    }

}

$MJTC_includer = new MJTC_includer();
?>
