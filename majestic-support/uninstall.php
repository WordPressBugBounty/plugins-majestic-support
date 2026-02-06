<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Majestic Support Uninstall
 *
 * Uninstalling Majestic Support tables, and pages.
 *
 * @author 		Ahmed Bilal
 * @category 	Core
 * @package 	Majestic Support/Uninstaller
 * @version     1.0.1
 */
if (!defined('WP_UNINSTALL_PLUGIN'))
    exit();

global $wpdb;
include_once 'includes/deactivation.php';

if(function_exists('is_multisite') && is_multisite()){
	$MJTC_blogs = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
    foreach($MJTC_blogs as $blog_id){
        switch_to_blog( $blog_id );
		$MJTC_tablestodrop = MJTC_deactivation::MJTC_tables_to_drop();
        foreach($MJTC_tablestodrop as $MJTC_tablename){
            $wpdb->query( "DROP TABLE IF EXISTS `" . esc_sql( $MJTC_tablename ) . "`" );
        }
        restore_current_blog();
    }
}else{
	$MJTC_tablestodrop = MJTC_deactivation::MJTC_tables_to_drop();
	foreach($MJTC_tablestodrop as $MJTC_tablename){
        $wpdb->query( "DROP TABLE IF EXISTS `" . esc_sql( $MJTC_tablename ) . "`" );
    }
}
