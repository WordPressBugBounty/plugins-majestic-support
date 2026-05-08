<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_themesModel {

    function storeTheme($MJTC_data) {
        if (!current_user_can('manage_options')){
            die('Only Administrators can perform this action.');
        }
        $MJTC_data = majesticsupport::MJTC_sanitizeData($MJTC_data);
        $MJTC_return = $this->MJTC_generateColorVariablesFile($MJTC_data);
        update_option('ms_set_theme_colors', wp_json_encode($MJTC_data));

        if ($MJTC_return) {
            MJTC_message::MJTC_setMessage(esc_html(__('The new theme has been applied', 'majestic-support')), 'updated');
        } else {
            MJTC_message::MJTC_setMessage(esc_html(__('Error applying the new theme', 'majestic-support')), 'error');
        }
        return;
    }

    function MJTC_generateColorVariablesFile($MJTC_data) {
        if (empty($MJTC_data['color1']) || empty($MJTC_data['color2']) || empty($MJTC_data['color3'])) {
            return false; // nothing to do
        }

        // Define the file path
        $css_file = MJTC_PLUGIN_PATH . 'includes/css/majestic_support_variables.css';


        // Prepare CSS content
        $css_content = "
        :root {
            --mjtc-color-1: {$MJTC_data['color1']};
            --mjtc-color-2: {$MJTC_data['color2']};
            --mjtc-color-3: {$MJTC_data['color3']};
            --mjtc-color-4: {$MJTC_data['color4']};
            --mjtc-color-5: {$MJTC_data['color5']};
            --mjtc-color-6: {$MJTC_data['color6']};
            --mjtc-color-7: {$MJTC_data['color7']};
            --mjtc-color-8: {$MJTC_data['color8']};
        }";

        // Initialize WordPress filesystem API
        global $wp_filesystem;
        if (!function_exists('wp_handle_upload')) {
            do_action('majesticsupport_load_wp_file');
        }
        if ( ! WP_Filesystem() ) {
            return false;
        }

        if ($wp_filesystem) {
            $wp_filesystem->put_contents($css_file, $css_content, FS_CHMOD_FILE);
            return true;
        }
    }

    function getCurrentTheme() {
        $MJTC_color1 = "#291abc";
        $MJTC_color2 = "#2b2b2b";
        $MJTC_color3 = "#f5f2f5";
        $MJTC_color4 = "#636363";
        $MJTC_color5 = "#d1d1d1";
        $MJTC_color6 = "#e7e7e7";
        $MJTC_color7 = "#ffffff";
        $MJTC_color8 = "#2DA1CB";
        $MJTC_color_string_values = get_option("ms_set_theme_colors");
        if($MJTC_color_string_values != ''){
            $MJTC_json_values = json_decode($MJTC_color_string_values,true);
            if(is_array($MJTC_json_values) && !empty($MJTC_json_values)){
                $MJTC_color1 = $MJTC_json_values['color1'];
                $MJTC_color2 = $MJTC_json_values['color2'];
                $MJTC_color3 = $MJTC_json_values['color3'];
                $MJTC_color4 = $MJTC_json_values['color4'];
                $MJTC_color5 = $MJTC_json_values['color5'];
                $MJTC_color6 = $MJTC_json_values['color6'];
                $MJTC_color7 = $MJTC_json_values['color7'];
                if (!empty($MJTC_json_values['color8'])) {
                    $MJTC_color8 = $MJTC_json_values['color8'];
                }
            }
        }
        $theme['color1'] = esc_attr($MJTC_color1);
        $theme['color2'] = esc_attr($MJTC_color2);
        $theme['color3'] = esc_attr($MJTC_color3);
        $theme['color4'] = esc_attr($MJTC_color4);
        $theme['color5'] = esc_attr($MJTC_color5);
        $theme['color6'] = esc_attr($MJTC_color6);
        $theme['color7'] = esc_attr($MJTC_color7);
	    $theme['color8'] = esc_attr($MJTC_color8);
        $theme = apply_filters('MJTC_theme_colors', $theme, 'majestic-support');
        majesticsupport::$_data[0] = $theme;
        return;
    }
}
?>
