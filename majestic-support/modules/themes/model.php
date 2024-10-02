<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_themesModel {

    function storeTheme($data) {
        if (!current_user_can('manage_options')){
            die('Only Administrators can perform this action.');
        }
        $data = majesticsupport::MJTC_sanitizeData($data);
        update_option('ms_set_theme_colors', wp_json_encode($data));
        $return = require(MJTC_PLUGIN_PATH . 'includes/css/style.php');

        if ($return) {
            MJTC_message::MJTC_setMessage(esc_html(__('The new theme has been applied', 'majestic-support')), 'updated');
        } else {
            MJTC_message::MJTC_setMessage(esc_html(__('Error applying the new theme', 'majestic-support')), 'error');
        }
        return;
    }

    function getCurrentTheme() {
        $color1 = "#291abc";
        $color2 = "#2b2b2b";
        $color3 = "#f5f2f5";
        $color4 = "#636363";
        $color5 = "#d1d1d1";
        $color6 = "#e7e7e7";
        $color7 = "#ffffff";
        $color8 = "#2DA1CB";
        $color9 = "#000000";
        $color_string_values = get_option("ms_set_theme_colors");
        if($color_string_values != ''){
            $json_values = json_decode($color_string_values,true);
            if(is_array($json_values) && !empty($json_values)){
                $color1 = $json_values['color1'];
                $color2 = $json_values['color2'];
                $color3 = $json_values['color3'];
                $color4 = $json_values['color4'];
                $color5 = $json_values['color5'];
                $color6 = $json_values['color6'];
                $color7 = $json_values['color7'];
            }
        }
        $theme['color1'] = esc_attr($color1);
        $theme['color2'] = esc_attr($color2);
        $theme['color3'] = esc_attr($color3);
        $theme['color4'] = esc_attr($color4);
        $theme['color5'] = esc_attr($color5);
        $theme['color6'] = esc_attr($color6);
        $theme['color7'] = esc_attr($color7);
	$theme['color8'] = esc_attr($color8);
        $theme['color9'] = esc_attr($color9);
        $theme = apply_filters('cm_theme_colors', $theme, 'majestic-support');
        majesticsupport::$_data[0] = $theme;
        return;
    }
}
?>
