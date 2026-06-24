<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_deactivation {

    static function MJTC_deactivate() {
        wp_clear_scheduled_hook('mjtc_process_transation_key_status');
        wp_clear_scheduled_hook('majesticsupport_updateticketstatus');
        wp_clear_scheduled_hook('majesticsupport_ticketviaemail');
        $MJTC_timestamp = wp_next_scheduled( 'ms_delete_expire_session_data' );
        wp_unschedule_event( $MJTC_timestamp, 'ms_delete_expire_session_data' );
        $MJTC_id = majesticsupport::getPageid();
        majesticsupport::$_db->get_var("UPDATE `" . majesticsupport::$_db->prefix . "posts` SET post_status = 'draft' WHERE ID = ".intval($MJTC_id));

        //Delete capabilities
        $MJTC_role = get_role( 'administrator' );
        $MJTC_role->remove_cap( 'ms_support_ticket' );
    }

    static function MJTC_tables_to_drop() {
        global $wpdb;
        $tables = array(
           $wpdb->prefix."mjtc_support_fieldsordering",
           $wpdb->prefix."mjtc_support_faqs",
           $wpdb->prefix."mjtc_support_instantfix",
           $wpdb->prefix."mjtc_support_departments",
           $wpdb->prefix."mjtc_support_attachments",
           $wpdb->prefix."mjtc_support_config",
           $wpdb->prefix."mjtc_support_email",
           $wpdb->prefix."mjtc_support_emailtemplates",
           $wpdb->prefix."mjtc_support_priorities",
           $wpdb->prefix."mjtc_support_statuses",
           $wpdb->prefix."mjtc_support_products",
           $wpdb->prefix."mjtc_support_replies",
           $wpdb->prefix."mjtc_support_system_errors",
           $wpdb->prefix."mjtc_support_tickets",
           $wpdb->prefix."mjtc_support_erasedatarequests",
           $wpdb->prefix."mjtc_support_users",
           $wpdb->prefix."mjtc_support_multiform",
           $wpdb->prefix."mjtc_support_slug",
           $wpdb->prefix."mjtc_support_mjtcsessiondata",
           $wpdb->prefix."mjtc_support_smartreplies",
           $wpdb->prefix."mjtc_support_zywrap_categories",
           $wpdb->prefix."mjtc_support_zywrap_ai_models",
           $wpdb->prefix."mjtc_support_zywrap_languages",
           $wpdb->prefix."mjtc_support_zywrap_use_cases",
           $wpdb->prefix."mjtc_support_zywrap_wrappers",
           $wpdb->prefix."mjtc_support_zywrap_block_templates",
           $wpdb->prefix."mjtc_support_zywrap_settings",
           $wpdb->prefix."mjtc_support_zywrap_usage_logs",
        );
        return $tables;
    }

}

?>
