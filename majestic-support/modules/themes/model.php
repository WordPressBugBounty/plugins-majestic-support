<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_themesModel {

    function storeTheme($MJTC_data) {
        if (!current_user_can('manage_options')){
            die('Only Administrators can perform this action.');
        }
        $MJTC_data = majesticsupport::MJTC_sanitizeData($MJTC_data);
        update_option('ms_set_theme_colors', wp_json_encode($MJTC_data));
        $return = require(MJTC_PLUGIN_PATH . 'includes/css/style.php');

        if ($return) {
            MJTC_message::MJTC_setMessage(esc_html(__('The new theme has been applied', 'majestic-support')), 'updated');
        } else {
            MJTC_message::MJTC_setMessage(esc_html(__('Error applying the new theme', 'majestic-support')), 'error');
        }
        return;
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
        $MJTC_color9 = "#000000";
        $MJTC_color_string_values = get_option("ms_set_theme_colors");
        if($MJTC_color_string_values != ''){
            $json_values = json_decode($MJTC_color_string_values,true);
            if(is_array($json_values) && !empty($json_values)){
                $MJTC_color1 = $json_values['color1'];
                $MJTC_color2 = $json_values['color2'];
                $MJTC_color3 = $json_values['color3'];
                $MJTC_color4 = $json_values['color4'];
                $MJTC_color5 = $json_values['color5'];
                $MJTC_color6 = $json_values['color6'];
                $MJTC_color7 = $json_values['color7'];
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
        $theme['color9'] = esc_attr($MJTC_color9);
        $theme = apply_filters('cm_theme_colors', $theme, 'majestic-support');
        majesticsupport::$_data[0] = $theme;
        return;
    }
}
?>
