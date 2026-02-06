<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_emailtemplateModel {

    function getTemplate($tempfor, $formid, $langcode) {
        switch ($tempfor) {
            case 'tk-nw' : $tempatefor = 'ticket-new';
                break;
            case 'sntk-tk' : $tempatefor = 'ticket-staff';
                break;
            case 'ew-md' : $tempatefor = 'department-new';
                break;
            case 'ew-gr' : $tempatefor = 'group-new';
                break;
            case 'ew-sm' : $tempatefor = 'staff-new';
                break;
            case 'ew-ht' : $tempatefor = 'helptopic-new';
                break;
            case 'rs-tk' : $tempatefor = 'reassign-tk';
                break;
            case 'cl-tk' : $tempatefor = 'close-tk';
                break;
            case 'dl-tk' : $tempatefor = 'delete-tk';
                break;
            case 'mo-tk' : $tempatefor = 'moverdue-tk';
                break;
            case 'be-tk' : $tempatefor = 'banemail-tk';
                break;
            case 'be-trtk' : $tempatefor = 'banemail-trtk';
                break;
            case 'dt-tk' : $tempatefor = 'deptrans-tk';
                break;
            case 'ebct-tk' : $tempatefor = 'banemailcloseticket-tk';
                break;
            case 'ube-tk' : $tempatefor = 'unbanemail-tk';
                break;
            case 'rsp-tk' : $tempatefor = 'responce-tk';
                break;
            case 'rpy-tk' : $tempatefor = 'reply-tk';
                break;
            case 'tk-ew-ad' : $tempatefor = 'ticket-new-admin';
                break;
            case 'lk-tk' : $tempatefor = 'lock-tk';
                break;
            case 'ulk-tk' : $tempatefor = 'unlock-tk';
                break;
            case 'minp-tk' : $tempatefor = 'minprogress-tk';
                break;
            case 'pc-tk' : $tempatefor = 'prtrans-tk';
                break;
            case 'ml-ew' : $tempatefor = 'mail-new';
                break;
            case 'ml-rp' : $tempatefor = 'mail-rpy';
                break;
            case 'fd-bk' : $tempatefor = 'mail-feedback';
                break;
            case 'no-rp' : $tempatefor = 'mail-rpy-closed';
                break;
            case 'del-data' : $tempatefor = 'delete-user-data';
                break;
            default: $tempatefor = 'ticket-new';
                break;
        }
        if (!empty($langcode)) {
            $query = "SELECT * FROM `" . majesticsupport::$_db->prefix . "mjtc_support_multilanguageemailtemplates` WHERE templatefor = '" . esc_sql($tempatefor) . "' AND language_id = '" . esc_sql($langcode) . "'";
        } else {
            $query = "SELECT * FROM `" . majesticsupport::$_db->prefix . "mjtc_support_emailtemplates` WHERE templatefor = '" . esc_sql($tempatefor) . "'";
        }
        if (!empty($formid)) {
            $query .= " AND multiformid = " . esc_sql($formid);
        } else {
            $query .= " AND (multiformid IS NULL OR multiformid = '')";
        }
        majesticsupport::$_data[0] = majesticsupport::$_db->get_row(($query));
        $multiformname = '';
        if(in_array('multiform', majesticsupport::$_active_addons) && !empty(majesticsupport::$_data[0]->multiformid)){
            $query = "SELECT title
                FROM `" . majesticsupport::$_db->prefix . "mjtc_support_multiform` WHERE id = ".esc_sql(majesticsupport::$_data[0]->multiformid);
            $multiformname = majesticsupport::$_db->get_var($query);
        }
        majesticsupport::$_data[0]->multiformname = $multiformname;

        do_action('majesticsupport_load_wp_translation_install');
        $translations = wp_get_available_translations();
        $installed = wp_get_installed_translations('core');

        $language_name = '';
        if(in_array('multilanguageemailtemplates', majesticsupport::$_active_addons) && !empty(majesticsupport::$_data[0]->language_id)){
            $language_name = isset($translations[majesticsupport::$_data[0]->language_id]['english_name']) ? $translations[majesticsupport::$_data[0]->language_id]['english_name'] : ucfirst(str_replace('_', '-', majesticsupport::$_data[0]->language_id));
        }
        majesticsupport::$_data[0]->language_name = $language_name;
        
        if (in_array('multiform', majesticsupport::$_active_addons) || in_array('multilanguageemailtemplates', majesticsupport::$_active_addons)) {
            
            $query = '';
            if(in_array('multiform', majesticsupport::$_active_addons)){
                $query = "
                    (
                        SELECT
                            tmpl.multiformid AS formid,
                            form.title AS formname,
                            department.departmentname,
                            tmpl.id AS template_id,
                            NULL AS language,
                            'main' AS source
                        FROM `" . majesticsupport::$_db->prefix . "mjtc_support_emailtemplates` AS tmpl
                        LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_multiform` AS form
                            ON tmpl.multiformid = form.id
                        LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_departments` AS department
                            ON form.departmentid = department.id
                        WHERE tmpl.templatefor = '" . esc_sql($tempatefor) . "'
                    )
                ";
            }
            if (in_array('multiform', majesticsupport::$_active_addons) && in_array('multilanguageemailtemplates', majesticsupport::$_active_addons)) {
                $query .= " UNION ALL ";
            }

            if (in_array('multilanguageemailtemplates', majesticsupport::$_active_addons)) {
                if (in_array('multiform', majesticsupport::$_active_addons)) {
                    $query .= "
                        (
                            SELECT
                                ltmpl.multiformid AS formid,
                                form.title AS formname,
                                department.departmentname,
                                ltmpl.id AS template_id,
                                ltmpl.language_id AS language,
                                'multi' AS source
                            FROM `" . majesticsupport::$_db->prefix . "mjtc_support_multilanguageemailtemplates` AS ltmpl
                            LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_multiform` AS form
                                ON ltmpl.multiformid = form.id
                            LEFT JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_departments` AS department
                                ON form.departmentid = department.id
                            WHERE ltmpl.templatefor = '" . esc_sql($tempatefor) . "'
                        )
                    ";
                } else {
                    $query .= "
                        (
                            SELECT
                                ltmpl.multiformid AS formid,
                                ltmpl.id AS template_id,
                                ltmpl.language_id AS language,
                                'multi' AS source
                            FROM `" . majesticsupport::$_db->prefix . "mjtc_support_multilanguageemailtemplates` AS ltmpl
                            WHERE ltmpl.templatefor = '" . esc_sql($tempatefor) . "'
                        )
                    ";
                }
            }

            $list = majesticsupport::$_db->get_results($query);

            $langLookup = [];

            if (!empty($installed['default'])) {
                foreach ($installed['default'] as $code => $MJTC_value) {
                    $langLookup[$code] = isset($translations[$code]['english_name']) 
                        ? $translations[$code]['english_name'] 
                        : ucfirst(str_replace('_', '-', $code));
                }
            }

            // Now enrich $list with language names
            foreach ($list as $MJTC_key => &$item) {
                if (empty($item->formname) && empty($item->language)) {
                    unset($list[$MJTC_key]); // This removes the item from the array
                    continue;
                }

                if (!empty($item->language)) {
                    $item->language_name = isset($langLookup[$item->language])
                        ? $langLookup[$item->language]
                        : ucfirst(str_replace('_', '-', $item->language));
                } else {
                    $item->language_name = ''; // or 'Default'
                }
            }

            majesticsupport::$_data[0]->multiTemplates = $list;
        }

        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        majesticsupport::$_data[2] = MJTC_includer::MJTC_getModel('fieldordering')->getUserfieldsfor(1, $formid);
        return ;
    }

    //For the Email template
    function storeEmailTemplate($MJTC_data) {
        $MJTC_data['title'] = isset($MJTC_data['title']) ? $MJTC_data['title'] : '';
        $MJTC_data['status'] = isset($MJTC_data['status']) ? $MJTC_data['status'] : 1;
        $MJTC_data['body'] = wpautop(wptexturize(MJTC_majesticsupportphplib::MJTC_stripslashes($_POST['body'])));

        $row = MJTC_includer::MJTC_getTable('emailtemplates');

        $error = 0;
        if (!$row->bind($MJTC_data)) {
            $error = 1;
        }
        if (!$row->store()) {
            $error = 1;
        }
        if ($error == 0) {
            MJTC_message::MJTC_setMessage(esc_html(__('Email template has been stored', 'majestic-support')), 'updated');
            if(isset($MJTC_data['multiformid']) && empty($MJTC_data['multiformid'])) {
                $query = "UPDATE `" . majesticsupport::$_db->prefix . "mjtc_support_emailtemplates` SET multiformid = NULL WHERE multiformid = '0' AND id = ".$row->id;
                majesticsupport::$_db->query($query);
            }
        } else {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError(); // if there is an error add it to system errorrs
            MJTC_message::MJTC_setMessage(esc_html(__('Email template has not been stored', 'majestic-support')), 'error');
        }
        return;
    }

    function getDefaultEmailTemplate() {
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $nonce, 'list-email-template') ) {
            die( 'Security check Failed' );
        }
        $templatefor = MJTC_request::MJTC_getVar('templatefor');
        $query = "SELECT * FROM `" . majesticsupport::$_db->prefix . "mjtc_support_emailtemplates` WHERE templatefor = '" . esc_sql($templatefor) . "'";
        $result = majesticsupport::$_db->get_row($query);
        $MJTC_data =  array('defaultsubject'=>MJTC_majesticsupportphplib::MJTC_htmlentities($result->subject),'defaultbody'=>MJTC_majesticsupportphplib::MJTC_htmlentities($result->body) , 'defaultid'=>MJTC_majesticsupportphplib::MJTC_htmlentities($result->id));
        return wp_json_encode($MJTC_data);

    }

    function removeFormEmailTemplate($id, $source) {
        if (!is_numeric($id))
            return false;
        
        if ($source == 'multi' && in_array('multilanguageemailtemplates', majesticsupport::$_active_addons)) {
            $row = MJTC_includer::MJTC_getTable('multilanguageemailtemplates');
        } else {
            $row = MJTC_includer::MJTC_getTable('emailtemplates');
        }
        if ($row->delete($id)) {
            MJTC_message::MJTC_setMessage(esc_html(__('Email tempate has been deleted', 'majestic-support')), 'updated');
        } else {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError(); // if there is an error add it to system errorrs
            MJTC_message::MJTC_setMessage(esc_html(__('Email tempate has not been deleted', 'majestic-support')), 'error');
        }

        return;
    }

}

?>
