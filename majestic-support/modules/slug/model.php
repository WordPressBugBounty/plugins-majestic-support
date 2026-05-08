<?php
if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_slugModel {

    private $_params_flag;
    private $_params_string;

    function __construct() {
        $this->_params_flag = 0;
    }

    function getSlug() {
        // Filter
        $MJTC_slug = majesticsupport::$_search['slug']['slug'];

        $MJTC_inquery = '';
        if ($MJTC_slug != null){
            $MJTC_inquery .= " AND slug.slug LIKE '%".esc_sql($MJTC_slug)."%'";
        }
        majesticsupport::$_data['slug'] = $MJTC_slug;

        // Pagination
        $MJTC_query = "SELECT COUNT(id) FROM ".majesticsupport::$_db->prefix."mjtc_support_slug AS slug WHERE slug.status = 1 ";
        $MJTC_query .= $MJTC_inquery;
        $total = majesticsupport::$_db->get_var($MJTC_query);

        majesticsupport::$_data['total'] = $total;
        majesticsupport::$_data[1] = MJTC_pagination::MJTC_getPagination($total);

        //Data
        $MJTC_query = "SELECT *
                  FROM ".majesticsupport::$_db->prefix ."mjtc_support_slug AS slug WHERE slug.status = 1 ";
        $MJTC_query .= $MJTC_inquery;
        $MJTC_query .= " LIMIT " . MJTC_pagination::MJTC_getOffset() . ", " . MJTC_pagination::MJTC_getLimit();
        majesticsupport::$_data[0] = majesticsupport::$_db->get_results($MJTC_query);

        return;
    }


    function storeSlug($MJTC_data) {
        if (empty($MJTC_data)) {
            return false;
        }
        if (!current_user_can('manage_options')) { //only admin can change it.
            return false;
        }
        $MJTC_row = MJTC_includer::MJTC_getTable('slug');
        foreach ($MJTC_data as $MJTC_id => $MJTC_slug) {
            if($MJTC_id != '' && is_numeric($MJTC_id)){
                $MJTC_slug = sanitize_title($MJTC_slug);
                if($MJTC_slug != ''){
                    $MJTC_query = "SELECT COUNT(id) FROM " . majesticsupport::$_db->prefix . "mjtc_support_slug
                            WHERE slug = '" . esc_sql($MJTC_slug)."' ";
                    $MJTC_slug_flag = majesticsupport::$_db->get_var($MJTC_query);
                    if($MJTC_slug_flag > 0){
                        continue;
                    }else{
                        $MJTC_row->update(array('id' => $MJTC_id, 'slug' => $MJTC_slug));
                    }
                }
            }
        }
        update_option('rewrite_rules', '');
        MJTC_message::MJTC_setMessage(esc_html(__('Slugs/Slug has been stored', 'majestic-support')), 'updated');
        return;
    }

    function savePrefix($MJTC_data) {
        if (empty($MJTC_data)) {
            return false;
        }
        $MJTC_data['prefix'] = ($MJTC_data['prefix']);
        if($MJTC_data['prefix'] == ''){
            MJTC_message::MJTC_setMessage(esc_html(__('Prefix has not been stored', 'majestic-support')), 'error');
            return;
        }
        $MJTC_query = "UPDATE " . majesticsupport::$_db->prefix . "mjtc_support_config
                    SET configvalue = '".esc_sql($MJTC_data['prefix'])."'
                    WHERE configname = 'slug_prefix'";
        if(majesticsupport::$_db->query($MJTC_query)){
            update_option('rewrite_rules', '');
            MJTC_message::MJTC_setMessage(esc_html(__('Prefix has been stored', 'majestic-support')), 'updated');
            return;
        }else{
            update_option('rewrite_rules', '');
        	MJTC_message::MJTC_setMessage(esc_html(__('Prefix has not been stored', 'majestic-support')), 'error');
            return;
        }
    }

    function saveHomePrefix($MJTC_data) {
        if (empty($MJTC_data)) {
            return false;
        }
        $MJTC_data['prefix'] = ($MJTC_data['prefix']);
        if($MJTC_data['prefix'] == ''){
            MJTC_message::MJTC_setMessage(esc_html(__('Prefix has not been stored', 'majestic-support')), 'error');
            return;
        }
        $MJTC_query = "UPDATE " . majesticsupport::$_db->prefix . "mjtc_support_config
                    SET configvalue = '".esc_sql($MJTC_data['prefix'])."'
                    WHERE configname = 'home_slug_prefix'";
        if(majesticsupport::$_db->query($MJTC_query)){
            update_option('rewrite_rules', '');
            MJTC_message::MJTC_setMessage(esc_html(__('Prefix has been stored', 'majestic-support')), 'updated');
            return;
        }else{
            update_option('rewrite_rules', '');
            MJTC_message::MJTC_setMessage(esc_html(__('Prefix has not been stored', 'majestic-support')), 'error');
            return;
        }
    }

    function resetAllSlugs() {
        $MJTC_query = "UPDATE " . majesticsupport::$_db->prefix . "mjtc_support_slug
                    SET slug = defaultslug ";
        if(majesticsupport::$_db->query($MJTC_query)){
            update_option('rewrite_rules', '');
            MJTC_message::MJTC_setMessage(esc_html(__('Slugs/Slug has been stored', 'majestic-support')), 'updated');
            return;
        }else{
            update_option('rewrite_rules', '');
            MJTC_message::MJTC_setMessage(esc_html(__('Slugs/Slug has been stored', 'majestic-support')), 'updated');
            return;
        }
    }

    function getOptionsForEditSlug() {
        if(!current_user_can('manage_options')){
            return false;
        }
        $MJTC_id = MJTC_request::MJTC_getVar('id');
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'get-options-for-edit-slug-'.$MJTC_id) ) {
            die( 'Security check Failed' );
        }
        $MJTC_slug = MJTC_request::MJTC_getVar('slug');
        $MJTC_html = '<span class="userpopup-top">
                    <span id="userpopup-heading" class="userpopup-heading" >' . esc_html(__("Edit",'majestic-support'))." ". esc_html(__("Slug", 'majestic-support')) . '</span>
                        <img alt="'. esc_html(__("Close",'majestic-support')).'" onClick="closePopup();" class="userpopup-close" src="'. esc_url(MJTC_PLUGIN_URL).'includes/images/close-icon-white.png" />
                    </span>';
        $MJTC_html .= '<div class="userpopup-search">
                    <div class="popup-field-title">' . esc_html(__('Slug','majestic-support')).' '. esc_html(__('Name','majestic-support')) . ' <span style="color: red;"> *</span></div>
                         <div class="popup-field-obj">' . wp_kses(MJTC_formfield::MJTC_text('slugedit', isset($MJTC_slug) ? MJTC_majesticsupportphplib::MJTC_trim($MJTC_slug) : 'text', '', array('class' => 'inputbox one', 'data-validation' => 'required')), MJTC_ALLOWED_TAGS) . '</div>
                    </div>';
        $MJTC_html .='<div class="popup-act-btn-wrp">
                    ' . wp_kses(MJTC_formfield::MJTC_button('save', esc_html(__('Save', 'majestic-support')), array('class' => 'button savebutton popup-act-btn','onClick'=>'getFieldValue();')), MJTC_ALLOWED_TAGS);
        $MJTC_html .='</div>';
        $MJTC_html = MJTC_majesticsupportphplib::MJTC_htmlentities($MJTC_html);
        return wp_json_encode($MJTC_html);
    }

    function getDefaultSlugFromSlug($MJTC_layout) {
        $MJTC_query = "SELECT  defaultslug FROM `".majesticsupport::$_db->prefix."mjtc_support_slug` WHERE defaultslug = '".esc_sql($MJTC_layout)."'";
        $MJTC_val = majesticsupport::$_db->get_var($MJTC_query);
        return sanitize_title($MJTC_val);
    }

    function getSlugFromFileName($MJTC_layout,$MJTC_module) {
        $MJTC_query = "SELECT slug FROM `".majesticsupport::$_db->prefix."mjtc_support_slug` WHERE filename = '".esc_sql($MJTC_layout)."'";
        $MJTC_val = majesticsupport::$_db->get_var($MJTC_query);
        return $MJTC_val;
    }

    function getSlugString($MJTC_home_page = 0) {
        global $wp_rewrite;
        $MJTC_rules = wp_json_encode($wp_rewrite->rules);
        $MJTC_query = "SELECT slug AS value FROM `".majesticsupport::$_db->prefix."mjtc_support_slug`";
        $MJTC_val = majesticsupport::$_db->get_results($MJTC_query);
        $MJTC_string = '';
        $MJTC_bstring = '';
        //$MJTC_rules = wp_json_encode($MJTC_rules);
        $MJTC_prefix = MJTC_includer::MJTC_getModel('configuration')->getConfigValue('slug_prefix');
        $MJTC_homeprefix = MJTC_includer::MJTC_getModel('configuration')->getConfigValue('home_slug_prefix');
        foreach ($MJTC_val as $MJTC_slug) {
            if($MJTC_home_page == 1){
                $MJTC_slug->value = $MJTC_homeprefix.$MJTC_slug->value;
            }
            if(MJTC_majesticsupportphplib::MJTC_strpos($MJTC_rules,$MJTC_slug->value) === false){
                $MJTC_string .= $MJTC_bstring. $MJTC_slug->value;
            }else{
                $MJTC_string .= $MJTC_bstring.$MJTC_prefix. $MJTC_slug->value;
            }
            $MJTC_bstring = '|';
        }
        return $MJTC_string;
    }

    function getRedirectCanonicalArray() {
        global $wp_rewrite;
        $MJTC_slug_prefix = MJTC_includer::MJTC_getModel('configuration')->getConfigValue('slug_prefix');
        $MJTC_homeprefix = MJTC_includer::MJTC_getModel('configuration')->getConfigValue('home_slug_prefix');
        $MJTC_rules = wp_json_encode($wp_rewrite->rules);
        $MJTC_query = "SELECT slug AS value FROM `".majesticsupport::$_db->prefix."mjtc_support_slug`";
        $MJTC_val = majesticsupport::$_db->get_results($MJTC_query);
        $MJTC_string = array();
        $MJTC_bstring = '';
        foreach ($MJTC_val as $MJTC_slug) {
            $MJTC_slug->value = $MJTC_homeprefix.$MJTC_slug->value;
            $MJTC_string[] = $MJTC_bstring.$MJTC_slug->value;
            $MJTC_bstring = '/';
        }
        return $MJTC_string;
    }

    function getAdminSearchFormDataSlug(){
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'slug') ) {
            die( 'Security check Failed' );
        }
        $ms_search_array = array();
        $ms_search_array['slug'] = MJTC_request::MJTC_getVar('slug');
        $ms_search_array['search_from_slug'] = 1;
        return $ms_search_array;
    }

}

?>
