<?php
if (!defined('ABSPATH')) die('Restricted Access');

class MJTC_zywrapModel {

    function saveApiKey() {
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (!wp_verify_nonce($MJTC_nonce, 'save_api_key')) {
            wp_send_json_error(array('message' => __('Security check Failed', 'majestic-support')));
        }
        
        // SECURITY: ONLY ADMINISTRATORS CAN ACCESS
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Security Error: Unauthorized access. Administrators only.', 'majestic-support')));
            return;
        }

        $api_key = MJTC_request::MJTC_getVar('api_key');
        
        if (empty($api_key)) {
            wp_send_json_error(array('message' => __('API Key cannot be empty', 'majestic-support')));
        }

        update_option('mjtc_zywrap_api_key', sanitize_text_field($api_key));
        wp_send_json_success(array('message' => __('API Key saved successfully.', 'majestic-support')));
    }

    
    function savePreferences() {
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (!wp_verify_nonce($MJTC_nonce, 'save_preferences')) {
            wp_send_json_error(array('message' => __('Security check Failed', 'majestic-support')));
        }
        
        // SECURITY: ONLY ADMINISTRATORS CAN ACCESS
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Security Error: Unauthorized access. Administrators only.', 'majestic-support')));
            return;
        }

        // Capture fields
        $default_model = MJTC_request::MJTC_getVar('default_model');
        $default_lang = MJTC_request::MJTC_getVar('default_lang', 'English');

        // Save safely
        update_option('mjtc_zywrap_default_model', sanitize_text_field($default_model));
        update_option('mjtc_zywrap_default_lang', sanitize_text_field($default_lang));

        // Return success
        wp_send_json_success(array('message' => __('Preferences saved successfully.', 'majestic-support')));
    }

    function syncDataBundle() {
        // NONCE SECURITY CHECK
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (!wp_verify_nonce($MJTC_nonce, 'sync_data_bundle')) {
            wp_send_json_error(array('message' => __('Security check Failed', 'majestic-support')));
        }

        // SECURITY: ONLY ADMINISTRATORS CAN ACCESS
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Security Error: Unauthorized access. Administrators only.', 'majestic-support')));
            return;
        }

        $api_key = get_option('mjtc_zywrap_api_key');
        if (empty($api_key)) {
            wp_send_json_error(array('message' => __('Please save your API key first.', 'majestic-support')));
        }

        @ini_set('memory_limit', '768M');
        @set_time_limit(700);

        // Fetch the local version to see if we qualify for a Delta Update
        $local_version = get_option('mjtc_zywrap_data_version', '');
        
        $sync_url = 'https://api.zywrap.com/v1/sdk/v1/sync?fromVersion=' . urlencode($local_version);
        $response = wp_remote_get($sync_url, array(
            'timeout' => 600,
            'sslverify' => true,
            'headers' => array(
                'Authorization' => 'Bearer ' . $api_key,
                'Accept' => 'application/json'
            )
        ));

        if (is_wp_error($response)) {
            wp_send_json_error(array('message' => __('Sync failed', 'majestic-support') . ': ' . $response->get_error_message()));
        }

        $http_code = wp_remote_retrieve_response_code($response);
        if ($http_code !== 200) {
            wp_send_json_error(array('message' => __('API Error: Invalid response code ', 'majestic-support') . $http_code));
        }

        $json = json_decode(wp_remote_retrieve_body($response), true);
        if (!$json) {
            wp_send_json_error(array('message' => __('Failed to parse Sync JSON data.', 'majestic-support')));
        }

        $mode = isset($json['mode']) ? $json['mode'] : 'UNKNOWN';

        if ($mode === 'FULL_RESET') {
            // --- SCENARIO A: FULL RESET (Streaming Download & Replace All) ---
            $download_url = isset($json['wrappers']['downloadUrl']) ? $json['wrappers']['downloadUrl'] : 'https://api.zywrap.com/v1/sdk/v1/download';
            
            // 1. Define safe paths in the uploads directory (Guaranteed write permissions)
            $upload_dir = wp_upload_dir();
            $temp_file = trailingslashit($upload_dir['basedir']) . 'zywrap_bundle_' . time() . '.zip';
            $extract_path = trailingslashit($upload_dir['basedir']) . 'mjtc-zywrap-temp';

            // 2. Stream the download directly to the disk (Bypasses RAM limits)
            $zip_response = wp_remote_get($download_url, array(
                'timeout'   => 600, // Generous timeout for large files
                'sslverify' => true,
                'headers'   => array('Authorization' => 'Bearer ' . $api_key),
                'stream'    => true,
                'filename'  => $temp_file
            ));

            if (is_wp_error($zip_response)) {
                // Standardized WordPress way to delete a file
                wp_delete_file($temp_file);
                wp_send_json_error(array('message' => __('Download failed: ', 'majestic-support') . $zip_response->get_error_message()));
            }

            $response_code = wp_remote_retrieve_response_code($zip_response);
            if ($response_code !== 200) {
                wp_delete_file($temp_file);
                wp_send_json_error(array('message' => __('Download rejected. HTTP Code:', 'majestic-support') . ' ' . $response_code));
            }

            // 3. Initialize WordPress Filesystem
            global $wp_filesystem;
            if (empty($wp_filesystem)) {
                require_once(ABSPATH . 'wp-admin/includes/file.php');
                WP_Filesystem();
            }

            // 4. Prepare extraction folder
            if ($wp_filesystem->exists($extract_path)) {
                $wp_filesystem->rmdir($extract_path, true);
            }
            wp_mkdir_p($extract_path);

            // 5. Unzip the file
            $unzip_result = unzip_file($temp_file, $extract_path);
            
            // Always clean up the temp zip file immediately after extracting
            // Standardized WordPress way to delete a file
            wp_delete_file($temp_file);

            if (is_wp_error($unzip_result)) {
                wp_send_json_error(array('message' => __('Unzip failed:', 'majestic-support') . ' ' . $unzip_result->get_error_message()));
            }

            // 6. Process the JSON data
            $json_file = trailingslashit($extract_path) . 'zywrap-data.json';
            if (!file_exists($json_file)) {
                 wp_send_json_error(array('message' => __('zywrap-data.json not found in bundle.', 'majestic-support')));
            }

            $json_data = file_get_contents($json_file);
            $data = json_decode($json_data, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                wp_send_json_error(array('message' => __('Failed to parse bundle JSON data.', 'majestic-support')));
            }

            // 7. Save to Database
            $this->process_full_sync($data);
            if (isset($data['version'])) {
                update_option('mjtc_zywrap_data_version', sanitize_text_field($data['version']));
            }
            
            // Clean up the extraction folder
            $wp_filesystem->rmdir($extract_path, true);

        } elseif ($mode === 'DELTA_UPDATE') {
            // --- SCENARIO B: DELTA UPDATE (Fast Upsert & Reconcile) ---
            $this->process_delta_sync($json);
            if (!empty($json['newVersion'])) {
                update_option('mjtc_zywrap_data_version', sanitize_text_field($json['newVersion']));
            }
        } else {
             wp_send_json_error(array('message' => __('Unknown Sync Mode.', 'majestic-support')));
        }

        update_option('mjtc_zywrap_last_sync', time());

        $clean_mode = str_replace('_', ' ', $mode);
        wp_send_json_success(array('message' => __('AI Data Synced Successfully!', 'majestic-support') . ' (' . __('Mode', 'majestic-support') . ': ' . $clean_mode . ')'));
    }

    private function process_full_sync($data) {
        $prefix = majesticsupport::$_db->prefix . "mjtc_support_";

        majesticsupport::$_db->query("TRUNCATE TABLE `" . $prefix . "zywrap_categories`");
        majesticsupport::$_db->query("TRUNCATE TABLE `" . $prefix . "zywrap_use_cases`");
        majesticsupport::$_db->query("TRUNCATE TABLE `" . $prefix . "zywrap_wrappers`");
        majesticsupport::$_db->query("TRUNCATE TABLE `" . $prefix . "zywrap_ai_models`");
        majesticsupport::$_db->query("TRUNCATE TABLE `" . $prefix . "zywrap_languages`");
        majesticsupport::$_db->query("TRUNCATE TABLE `" . $prefix . "zywrap_block_templates`");

        if (!empty($data['categories'])) {
            $cats = $this->extract_tabular($data['categories']);
            foreach ($cats as $c) {
                $mjtc_query = "INSERT INTO `" . $prefix . "zywrap_categories` (`code`, `name`, `ordering`) VALUES (
                    '" . esc_sql($c['code']) . "', '" . esc_sql($c['name']) . "', " . (int)($c['ordering'] ?? 9999) . "
                )";
                majesticsupport::$_db->query($mjtc_query);
            }
        }

        // ---------------------------------------------------------
        // BATCH INSERT: USE CASES
        // ---------------------------------------------------------
        if (!empty($data['useCases'])) {
            $ucs = $this->extract_tabular($data['useCases']);
            $chunk_size = 500; // Safe chunk size to respect MySQL max_allowed_packet
            $chunks = array_chunk($ucs, $chunk_size);
            
            foreach ($chunks as $chunk) {
                $values = array();
                foreach ($chunk as $uc) {
                    $schemaJson = !empty($uc['schema']) ? wp_json_encode($uc['schema']) : null;
                    $values[] = "(
                        '" . esc_sql($uc['code']) . "', 
                        '" . esc_sql($uc['name']) . "', 
                        '" . esc_sql($uc['desc'] ?? '') . "', 
                        '" . esc_sql($uc['cat'] ?? '') . "', 
                        '" . esc_sql($schemaJson) . "', 
                        " . (int)($uc['ordering'] ?? 9999) . "
                    )";
                }
                
                // Construct a single query with multiple values
                $mjtc_query = "INSERT INTO `" . $prefix . "zywrap_use_cases` (`code`, `name`, `description`, `category_code`, `schema_data`, `ordering`) VALUES " . implode(', ', $values);
                majesticsupport::$_db->query($mjtc_query);
            }
        }

        // ---------------------------------------------------------
        // BATCH INSERT: WRAPPERS (Massive Dataset Optimization)
        // ---------------------------------------------------------
        if (!empty($data['wrappers'])) {
            $wrappers = $this->extract_tabular($data['wrappers']);
            $chunk_size = 1000; // Grouping 1000 wrappers per query
            $chunks = array_chunk($wrappers, $chunk_size);
            
            foreach ($chunks as $chunk) {
                $values = array();
                foreach ($chunk as $w) {
                    $values[] = "(
                        '" . esc_sql($w['code']) . "', 
                        '" . esc_sql($w['name']) . "', 
                        '" . esc_sql($w['desc'] ?? '') . "', 
                        '" . esc_sql($w['usecase'] ?? '') . "', 
                        " . (!empty($w['featured']) ? 1 : 0) . ", 
                        " . (!empty($w['base']) ? 1 : 0) . ", 
                        " . (int)($w['ordering'] ?? 9999) . "
                    )";
                }
                
                // Construct a single query with 1000 rows
                $mjtc_query = "INSERT INTO `" . $prefix . "zywrap_wrappers` (`code`, `name`, `description`, `use_case_code`, `featured`, `base`, `ordering`) VALUES " . implode(', ', $values);
                majesticsupport::$_db->query($mjtc_query);
            }
        }

        if (!empty($data['aiModels'])) {
            $models = $this->extract_tabular($data['aiModels']);
            foreach ($models as $m) {
                $mjtc_query = "INSERT INTO `" . $prefix . "zywrap_ai_models` (`code`, `name`, `ordering`) VALUES (
                    '" . esc_sql($m['code']) . "', '" . esc_sql($m['name']) . "', " . (int)($m['ordering'] ?? 9999) . "
                )";
                majesticsupport::$_db->query($mjtc_query);
            }
        }

        if (!empty($data['languages'])) {
            $langs = $this->extract_tabular($data['languages']);
            foreach ($langs as $l) {
                $mjtc_query = "INSERT INTO `" . $prefix . "zywrap_languages` (`code`, `name`, `ordering`) VALUES (
                    '" . esc_sql($l['code']) . "', '" . esc_sql($l['name']) . "', " . (int)($l['ordering'] ?? 9999) . "
                )";
                majesticsupport::$_db->query($mjtc_query);
            }
        }

        if (!empty($data['templates'])) {
            foreach ($data['templates'] as $type => $tabular) {
                $templates = $this->extract_tabular($tabular);
                foreach ($templates as $t) {
                    $mjtc_query = "INSERT INTO `" . $prefix . "zywrap_block_templates` (`type`, `code`, `name`) VALUES (
                        '" . esc_sql($type) . "', '" . esc_sql($t['code']) . "', '" . esc_sql($t['name']) . "'
                    )";
                    majesticsupport::$_db->query($mjtc_query);
                }
            }
        }
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
    }

    private function process_delta_sync($json) {
        $prefix = majesticsupport::$_db->prefix . "mjtc_support_";

        if (!empty($json['metadata']['categories'])) {
            foreach ($json['metadata']['categories'] as $r) {
                $status = (!isset($r['status']) || $r['status']) ? 1 : 0;
                $ordering = $r['position'] ?? $r['displayOrder'] ?? $r['ordering'] ?? 9999;
                $mjtc_query = "INSERT INTO `" . $prefix . "zywrap_categories` (`code`, `name`, `status`, `ordering`) 
                               VALUES ('" . esc_sql($r['code']) . "', '" . esc_sql($r['name']) . "', " . (int)$status . ", " . (int)$ordering . ") 
                               ON DUPLICATE KEY UPDATE `name`=VALUES(`name`), `status`=VALUES(`status`), `ordering`=VALUES(`ordering`)";
                majesticsupport::$_db->query($mjtc_query);
            }
        }

        if (!empty($json['metadata']['languages'])) {
            foreach ($json['metadata']['languages'] as $r) {
                $status = (!isset($r['status']) || $r['status']) ? 1 : 0;
                $ordering = $r['ordering'] ?? 9999;
                $mjtc_query = "INSERT INTO `" . $prefix . "zywrap_languages` (`code`, `name`, `status`, `ordering`) 
                               VALUES ('" . esc_sql($r['code']) . "', '" . esc_sql($r['name']) . "', " . (int)$status . ", " . (int)$ordering . ") 
                               ON DUPLICATE KEY UPDATE `name`=VALUES(`name`), `status`=VALUES(`status`), `ordering`=VALUES(`ordering`)";
                majesticsupport::$_db->query($mjtc_query);
            }
        }

        if (!empty($json['metadata']['aiModels'])) {
            foreach ($json['metadata']['aiModels'] as $r) {
                $status = (!isset($r['status']) || $r['status']) ? 1 : 0;
                $ordering = $r['displayOrder'] ?? $r['ordering'] ?? 9999;
                $mjtc_query = "INSERT INTO `" . $prefix . "zywrap_ai_models` (`code`, `name`, `status`, `ordering`) 
                               VALUES ('" . esc_sql($r['code']) . "', '" . esc_sql($r['name']) . "', " . (int)$status . ", " . (int)$ordering . ") 
                               ON DUPLICATE KEY UPDATE `name`=VALUES(`name`), `status`=VALUES(`status`), `ordering`=VALUES(`ordering`)";
                majesticsupport::$_db->query($mjtc_query);
            }
        }

        if (!empty($json['metadata']['templates'])) {
            foreach ($json['metadata']['templates'] as $type => $items) {
                foreach ($items as $item) {
                    $status = (!isset($item['status']) || $item['status']) ? 1 : 0;
                    $name = $item['label'] ?? $item['name'] ?? '';
                    $mjtc_query = "INSERT INTO `" . $prefix . "zywrap_block_templates` (`type`, `code`, `name`, `status`) 
                                   VALUES ('" . esc_sql($type) . "', '" . esc_sql($item['code']) . "', '" . esc_sql($name) . "', " . (int)$status . ") 
                                   ON DUPLICATE KEY UPDATE `name`=VALUES(`name`), `status`=VALUES(`status`)";
                    majesticsupport::$_db->query($mjtc_query);
                }
            }
        }

        if (!empty($json['useCases']['upserts'])) {
            foreach ($json['useCases']['upserts'] as $uc) {
                $schemaJson = !empty($uc['schema']) ? wp_json_encode($uc['schema']) : null;
                $status = (!isset($uc['status']) || $uc['status']) ? 1 : 0;
                $ordering = $uc['displayOrder'] ?? $uc['ordering'] ?? 9999;
                $mjtc_query = "INSERT INTO `" . $prefix . "zywrap_use_cases` (`code`, `name`, `description`, `category_code`, `schema_data`, `status`, `ordering`) 
                               VALUES ('" . esc_sql($uc['code']) . "', '" . esc_sql($uc['name']) . "', '" . esc_sql($uc['description'] ?? '') . "', '" . esc_sql($uc['categoryCode'] ?? '') . "', '" . esc_sql($schemaJson) . "', " . (int)$status . ", " . (int)$ordering . ") 
                               ON DUPLICATE KEY UPDATE `name`=VALUES(`name`), `description`=VALUES(`description`), `category_code`=VALUES(`category_code`), `schema_data`=VALUES(`schema_data`), `status`=VALUES(`status`), `ordering`=VALUES(`ordering`)";
                majesticsupport::$_db->query($mjtc_query);
            }
        }

        if (!empty($json['useCases']['deletes'])) {
            foreach ($json['useCases']['deletes'] as $code) {
                $mjtc_query = "DELETE FROM `" . $prefix . "zywrap_use_cases` WHERE `code` = '" . esc_sql($code) . "'";
                majesticsupport::$_db->query($mjtc_query);
            }
        }

        if (!empty($json['wrappers']['upserts'])) {
            foreach ($json['wrappers']['upserts'] as $w) {
                $featured = !empty($w['featured'] ?? $w['isFeatured']) ? 1 : 0;
                $base = !empty($w['base'] ?? $w['isBaseWrapper']) ? 1 : 0;
                $status = (!isset($w['status']) || $w['status']) ? 1 : 0;
                $ordering = $w['displayOrder'] ?? $w['ordering'] ?? 9999;
                $mjtc_query = "INSERT INTO `" . $prefix . "zywrap_wrappers` (`code`, `name`, `description`, `use_case_code`, `featured`, `base`, `status`, `ordering`) 
                               VALUES ('" . esc_sql($w['code']) . "', '" . esc_sql($w['name']) . "', '" . esc_sql($w['description'] ?? '') . "', '" . esc_sql($w['useCaseCode'] ?? $w['categoryCode'] ?? '') . "', " . (int)$featured . ", " . (int)$base . ", " . (int)$status . ", " . (int)$ordering . ") 
                               ON DUPLICATE KEY UPDATE `name`=VALUES(`name`), `description`=VALUES(`description`), `use_case_code`=VALUES(`use_case_code`), `featured`=VALUES(`featured`), `base`=VALUES(`base`), `status`=VALUES(`status`), `ordering`=VALUES(`ordering`)";
                majesticsupport::$_db->query($mjtc_query);
            }
        }

        if (!empty($json['wrappers']['deletes'])) {
            foreach ($json['wrappers']['deletes'] as $code) {
                $mjtc_query = "DELETE FROM `" . $prefix . "zywrap_wrappers` WHERE `code` = '" . esc_sql($code) . "'";
                majesticsupport::$_db->query($mjtc_query);
            }
        }
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
    }


    private function extract_tabular($tabularData) {
        if (empty($tabularData['cols']) || empty($tabularData['data'])) return array();
        $cols = $tabularData['cols'];
        $result = array();
        foreach ($tabularData['data'] as $row) {
            $result[] = array_combine($cols, $row);
        }
        return $result;
    }

    private function log_usage($body_json, $wrapper_code, $latency_ms, $status = 'success', $error_message = null) {
        global $wpdb;

        $usage = isset($body_json['usage']) && is_array($body_json['usage']) ? $body_json['usage'] : array();
        $cost = isset($body_json['cost']) && is_array($body_json['cost']) ? $body_json['cost'] : array();

        $wpdb->insert(
            $wpdb->prefix . 'mjtc_support_zywrap_usage_logs',
            array(
                'trace_id'          => isset($body_json['id']) ? sanitize_text_field($body_json['id']) : '',
                'wrapper_code'      => sanitize_text_field($wrapper_code),
                'model_code'        => isset($body_json['model']) ? sanitize_text_field($body_json['model']) : '',
                'prompt_tokens'     => absint($usage['prompt_tokens'] ?? 0),
                'completion_tokens' => absint($usage['completion_tokens'] ?? 0),
                'total_tokens'      => absint($usage['total_tokens'] ?? 0),
                'credits_used'      => (float) ($cost['credits_used'] ?? 0),
                'latency_ms'        => absint($latency_ms),
                'status'            => sanitize_key($status),
                'error_message'     => is_null($error_message) ? null : sanitize_textarea_field($error_message),
                'created_at'        => current_time('mysql', 1),
            ),
            array('%s', '%s', '%s', '%d', '%d', '%d', '%f', '%d', '%s', '%s', '%s')
        );
    }
    /**
     * Fetch all active AI Wrappers grouped by Category
     */
    /**
     * Fetch ONLY Use Cases for Customer Support
     */
    function getSupportUseCases() {
        $prefix = majesticsupport::$_db->prefix . "mjtc_support_";
        $mjtc_query = "SELECT code, name FROM `" . $prefix . "zywrap_use_cases` 
                       WHERE category_code = 'customer_support_replies' AND status = 1 
                       ORDER BY ordering ASC, name ASC";
                          
        $mjtc_results = majesticsupport::$_db->get_results($mjtc_query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        return $mjtc_results;
    }

    /**
     * Fetch Wrappers (Base + 8 Variations) for a specific Use Case
     */
    function getWrappersByUseCase($use_case_code) {
        $prefix = majesticsupport::$_db->prefix . "mjtc_support_";
        $mjtc_query = "SELECT code, name, base FROM `" . $prefix . "zywrap_wrappers` 
                       WHERE use_case_code = '" . esc_sql($use_case_code) . "' AND status = 1 
                       ORDER BY base DESC, ordering ASC"; // Base wrapper shows first
                          
        $mjtc_results = majesticsupport::$_db->get_results($mjtc_query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        return $mjtc_results;
    }

    /**
     * Fetch all active Tones from Block Templates
     */
    function getDynamicTones() {
        $prefix = majesticsupport::$_db->prefix . "mjtc_support_";
        
        $mjtc_query = "SELECT code, name FROM `" . $prefix . "zywrap_block_templates` 
                  WHERE type = 'tones' AND status = 1 
                  ORDER BY name ASC";
                          
        $mjtc_results = majesticsupport::$_db->get_results($mjtc_query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        return $mjtc_results;
    }

    static function ajaxGetWrappers() {
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (!wp_verify_nonce($MJTC_nonce, 'zywrap_ajax_action')) {
            wp_send_json_error(array('message' => __('Security check Failed', 'majestic-support')));
        }

        // SECURITY: ONLY ADMINISTRATORS CAN ACCESS
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Security Error: Unauthorized access. Administrators only.', 'majestic-support')));
            return;
        }

        /*
        // SECURITY: ONLY ADMINS OR MJTC_ STAFF CAN ACCESS
        $is_admin = current_user_can('manage_options');
        $is_staff = false;
        
        // Check if the MJTC_ Agent add-on is active and the user is an agent
        if (in_array('agent', majesticsupport::$_active_addons)) {
            $is_staff = MJTC_includer::MJTC_getModel('agent')->isUserStaff();
        }

        if (!$is_admin && !$is_staff) {
            wp_send_json_error(array('message' => 'Security Error: Unauthorized access. Staff members only.'));
            return;
        }
        */

        $use_case_code = MJTC_request::MJTC_getVar('use_case_code');
        $ticket_id = MJTC_request::MJTC_getVar('ticket_id'); // We now receive the Ticket ID

        $model_instance = new self();
        $wrappers = $model_instance->getWrappersByUseCase($use_case_code);

        $prefix = majesticsupport::$_db->prefix . "mjtc_support_";
        $mjtc_query = "SELECT schema_data FROM `" . $prefix . "zywrap_use_cases` WHERE code = '" . esc_sql($use_case_code) . "'";
        $schema_json = majesticsupport::$_db->get_var($mjtc_query);
        $schema = !empty($schema_json) ? json_decode($schema_json, true) : null;

        // Fetch Clean Ticket Data from PHP
        $ticketData = $model_instance->getTicketContext($ticket_id);

        // Native WP JSON sender automatically sets secure headers (no parse errors)
        wp_send_json_success(array(
            'wrappers' => $wrappers,
            'schema' => $schema,
            'ticketData' => $ticketData
        ));
    }

    // --- 3. UPGRADED: Uses native wp_send_json & Zywrap Stream Parser ---
    function generateReply() {
        // 1. Verify Nonce Securely
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (!wp_verify_nonce($MJTC_nonce, 'zywrap_ajax_action')) {
            wp_send_json_error(array('message' => __('Security check Failed', 'majestic-support')));
        }

        // SECURITY: ONLY ADMINISTRATORS CAN ACCESS
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Security Error: Unauthorized access. Administrators only.', 'majestic-support')));
            return;
        }

        /*
        // SECURITY: ONLY ADMINS OR MJTC_ STAFF CAN ACCESS
        $is_admin = current_user_can('manage_options');
        $is_staff = false;
        
        // Check if the MJTC_ Agent add-on is active and the user is an agent
        if (in_array('agent', majesticsupport::$_active_addons)) {
            $is_staff = MJTC_includer::MJTC_getModel('agent')->isUserStaff();
        }

        if (!$is_admin && !$is_staff) {
            wp_send_json_error(array('message' => 'Security Error: Unauthorized access. Staff members only.'));
            return;
        }
        */

        $api_key = get_option('mjtc_zywrap_api_key');
        if (empty($api_key)) {
            wp_send_json_error(array('message' => __('API Key is missing.', 'majestic-support')));
        }

        // 2. Fetch parameters directly from $_POST to bypass MJTC_ framework stripping
        $wrapper_code = sanitize_text_field(MJTC_request::MJTC_getVar('wrapper_code'));
        $prompt       = sanitize_textarea_field(MJTC_request::MJTC_getVar('prompt'));
        $model_code   = sanitize_text_field(MJTC_request::MJTC_getVar('model_code')); 
        $tone         = sanitize_text_field(MJTC_request::MJTC_getVar('tone'));
        $language     = sanitize_text_field(MJTC_request::MJTC_getVar('language'));
        
        if (empty($wrapper_code)) {
            wp_send_json_error(array('message' => __('Please select an AI action.', 'majestic-support')));
        }

        // 3. Process Dynamic Variables securely
        $variables_json = wp_unslash(MJTC_request::MJTC_getVar('variables', '{}'));
        $variables = json_decode($variables_json, true);
        if (!is_array($variables)) { $variables = array(); }

        $sanitized_vars = array();
        foreach ($variables as $k => $v) {
            $sanitized_vars[sanitize_text_field($k)] = sanitize_textarea_field($v);
        }

        // 4. Build Payload exactly matching the Zywrap SDK Docs
        $body = array(
            'wrapperCodes' => array($wrapper_code),
            'source'       => 'majestic-support'
        );

        if (!empty($prompt)) {
            $body['prompt'] = $prompt; // The Agent's Extra Instructions
        }
        if (!empty($model_code)) {
            $body['model'] = $model_code;
        }
        if (!empty($tone)) {
            $body['toneCode'] = $tone;
        }
        if (!empty($language)) {
            $body['language'] = $language;
        }
        if (!empty($sanitized_vars)) {
            $body['variables'] = (object)$sanitized_vars;
        }

        
        // (Optional) Catch any other advanced overrides if added to the UI later
        $overrides = ['styleCode', 'formatCode', 'complexityCode', 'lengthCode', 'audienceCode', 'responseGoalCode', 'outputCode'];
        foreach ($overrides as $override) {
            $val = MJTC_request::MJTC_getVar($override);
            if (!empty($val)) { $body[$override] = sanitize_text_field($val); }
        }
        
        // 5. Execute API Call
        $api_url = 'https://api.zywrap.com/v1/proxy';
        $start_time = microtime(true);

        $response = wp_remote_post($api_url, array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $api_key, 
                'Content-Type'  => 'application/json'
            ),
            'body'      => wp_json_encode($body),
            'timeout'   => 600, 
            'sslverify' => true,
        ));
        $latency_ms = round((microtime(true) - $start_time) * 1000);

        if (is_wp_error($response)) {
            wp_send_json_error(array('message' => __('Connection Error: ', 'majestic-support') . $response->get_error_message()));
        }

        $http_code = wp_remote_retrieve_response_code($response);
        $raw_response = wp_remote_retrieve_body($response);

        // 6. ZYWRAP V1 STREAM PARSER (From the PHP SDK)
        $lines = explode("\n", $raw_response);
        $finalJson = null;
        foreach ($lines as $line) {
            $line = trim($line);
            if (strpos($line, 'data: ') === 0) {
                $data = json_decode(substr($line, 6), true);
                if ($data && (isset($data['output']) || isset($data['error']))) {
                    $finalJson = substr($line, 6);
                }
            }
        }

        $body_json = $finalJson ? json_decode($finalJson, true) : null;

        // 7. Output Handling
        if ($http_code === 200 && $body_json && isset($body_json['output'])) {
            $this->log_usage($body_json, $wrapper_code, $latency_ms);
            wp_send_json_success(array('output' => $body_json['output']));
        } else {
            $error_msg = __('Unknown API Error', 'majestic-support');
            if ($body_json && isset($body_json['error'])) {
                $error_msg = is_string($body_json['error']) ? $body_json['error'] : wp_json_encode($body_json['error']);
            } elseif ($body_json && isset($body_json['message'])) {
                $error_msg = $body_json['message'];
            } elseif (!$body_json && !empty($raw_response)) {
                $error_msg = __('Parse Error:', 'majestic-support') . ' ' . substr(wp_strip_all_tags($raw_response), 0, 150);
            }
            wp_send_json_error(array('message' => $error_msg));
        }
    }
    /**
     * Fetch all active AI Models
     */
    function getDynamicModels() {
        $prefix = majesticsupport::$_db->prefix . "mjtc_support_";
        $mjtc_query = "SELECT code, name FROM `" . $prefix . "zywrap_ai_models` 
                       WHERE status = 1 
                       ORDER BY ordering ASC, name ASC";
                          
        $mjtc_results = majesticsupport::$_db->get_results($mjtc_query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        return $mjtc_results;
    }

    // --- 1. NEW: Fetch pure ticket data directly from the DB ---
    // --- 1. NEW: Fetch pure ticket data directly from the DB ---
    function getTicketContext($ticket_id) {
        if (!$ticket_id) {
            return array('subject' => '', 'initialMsg' => '', 'fullThread' => '', 'latestCustomerMsg' => '');
        }

        $prefix = majesticsupport::$_db->prefix . "mjtc_support_";

        // Fetch Main Ticket
        $mjtc_query = "SELECT subject, message, uid FROM `{$prefix}tickets` WHERE id = " . (int)$ticket_id;
        $ticket = majesticsupport::$_db->get_row($mjtc_query);
        if (!$ticket) {
            return array('subject' => '', 'initialMsg' => '', 'fullThread' => '', 'latestCustomerMsg' => '');
        }

        $subject = wp_strip_all_tags(strip_shortcodes($ticket->subject));
        $initialMsg = wp_strip_all_tags(strip_shortcodes($ticket->message));
        $latestCustomerMsg = $initialMsg;

        $history = "CUSTOMER (Initial Issue):\n" . $initialMsg . "\n\n";

        // The column name in mjtc_support_replies is 'message', not 'reply'
        $mjtc_query = "SELECT message, uid FROM `{$prefix}replies` WHERE ticketid = " . (int)$ticket_id . " ORDER BY created ASC";
        $replies = majesticsupport::$_db->get_results($mjtc_query);

        if (!empty($replies)) {
            foreach ($replies as $reply) {
                // FIX: Use $reply->message instead of $reply->reply
                $clean_reply = wp_strip_all_tags(strip_shortcodes($reply->message));
                
                // If reply UID matches ticket UID, it is the Customer. Otherwise, Support.
                if ($reply->uid == $ticket->uid) {
                    $author = "CUSTOMER";
                    if (!empty($clean_reply)) {
                        $latestCustomerMsg = $clean_reply; // Track absolute latest
                    }
                } else {
                    $author = "SUPPORT AGENT";
                }

                if (!empty($clean_reply)) {
                    $history .= "--- " . $author . " ---\n" . $clean_reply . "\n\n";
                }
            }
        }

        return array(
            'subject' => $subject,
            'initialMsg' => $initialMsg,
            'fullThread' => trim($history),
            'latestCustomerMsg' => $latestCustomerMsg
        );
    }

    function saveSettings() {
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (!wp_verify_nonce($MJTC_nonce, 'zywrap_ajax_action')) {
            wp_send_json_error(array('message' => __('Security check Failed', 'majestic-support')));
        }

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Unauthorized access.', 'majestic-support')), 403);
        }

        $api_key = sanitize_text_field(MJTC_request::MJTC_getVar('api_key'));
        $default_model = sanitize_text_field(MJTC_request::MJTC_getVar('default_model'));
        $default_lang = sanitize_text_field(MJTC_request::MJTC_getVar('default_lang', 'English'));

        update_option('mjtc_zywrap_api_key', $api_key);
        update_option('mjtc_zywrap_default_model', $default_model);
        update_option('mjtc_zywrap_default_lang', $default_lang);

        wp_send_json_success(array('message' => __('Global AI Settings saved successfully.', 'majestic-support')));
    }

    // =========================================================
    // ZYWRAP LOGS & ERRORS PAGES (MODEL)
    // =========================================================

    function getLogs() {
        // Filter variables
        $mjtc_trace_id = isset(majesticsupport::$_search['zywrap_logs']) ? majesticsupport::$_search['zywrap_logs']['trace_id'] : '';
        $mjtc_pagesize = isset(majesticsupport::$_search['zywrap_logs']) ? majesticsupport::$_search['zywrap_logs']['pagesize'] : 20;

        $mjtc_trace_id = majesticsupport::parseSpaces($mjtc_trace_id);
        $mjtc_inquery = "";
        if ($mjtc_trace_id != null) {
            $mjtc_inquery .= " WHERE trace_id LIKE '%" . esc_sql($mjtc_trace_id) . "%'";
        }

        majesticsupport::$_data['filter']['trace_id'] = $mjtc_trace_id;
        majesticsupport::$_data['filter']['pagesize'] = $mjtc_pagesize;

        // Pagination Limit
        if ($mjtc_pagesize) {
            MJTC_pagination::MJTC_setLimit($mjtc_pagesize);
        }

        // Get Total Count for Pagination
        $mjtc_query = "SELECT COUNT(`id`) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_zywrap_usage_logs`";
        $mjtc_query .= $mjtc_inquery;
        $mjtc_total = majesticsupport::$_db->get_var($mjtc_query);
        
        majesticsupport::$_data['total'] = $mjtc_total;
        majesticsupport::$_data[1] = MJTC_pagination::MJTC_getPagination($mjtc_total, 'zywrap_logs');

        // Get Paginated Data
        $mjtc_query = "SELECT * FROM `" . majesticsupport::$_db->prefix . "mjtc_support_zywrap_usage_logs`";
        $mjtc_query .= $mjtc_inquery;
        $mjtc_query .= " ORDER BY id DESC LIMIT " . MJTC_pagination::MJTC_getOffset() . ", " . MJTC_pagination::MJTC_getLimit();
        
        majesticsupport::$_data[0] = majesticsupport::$_db->get_results($mjtc_query);
        
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        return;
    }

    function getErrors() {
        // Filter variables (Only pagesize needed for errors usually)
        $mjtc_pagesize = isset(majesticsupport::$_search['zywrap_errors']) ? majesticsupport::$_search['zywrap_errors']['pagesize'] : 20;
        majesticsupport::$_data['filter']['pagesize'] = $mjtc_pagesize;

        if ($mjtc_pagesize) {
            MJTC_pagination::MJTC_setLimit($mjtc_pagesize);
        }

        // Force query to only show errors
        $mjtc_inquery = " WHERE status = 'error'";

        $mjtc_query = "SELECT COUNT(`id`) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_zywrap_usage_logs`";
        $mjtc_query .= $mjtc_inquery;
        $mjtc_total = majesticsupport::$_db->get_var($mjtc_query);
        
        majesticsupport::$_data['total'] = $mjtc_total;
        majesticsupport::$_data[1] = MJTC_pagination::MJTC_getPagination($mjtc_total, 'zywrap_errors');

        $mjtc_query = "SELECT * FROM `" . majesticsupport::$_db->prefix . "mjtc_support_zywrap_usage_logs`";
        $mjtc_query .= $mjtc_inquery;
        $mjtc_query .= " ORDER BY id DESC LIMIT " . MJTC_pagination::MJTC_getOffset() . ", " . MJTC_pagination::MJTC_getLimit();
        
        majesticsupport::$_data[0] = majesticsupport::$_db->get_results($mjtc_query);
        
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        return;
    }

    function deleteLog($mjtc_id) {
        if (!is_numeric($mjtc_id)) return false;

        $mjtc_query = "DELETE FROM `" . majesticsupport::$_db->prefix . "mjtc_support_zywrap_usage_logs` WHERE id = " . (int)$mjtc_id;
        majesticsupport::$_db->query($mjtc_query);

        if (majesticsupport::$_db->last_error == null) {
            MJTC_message::setMessage(esc_html(__('Log deleted successfully.', 'majestic-support')), 'updated');
        } else {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
            MJTC_message::setMessage(esc_html(__('Failed to delete log.', 'majestic-support')), 'error');
        }
        return;
    }

    // Required for the native Search/Filter functionality to work
    function getAdminZywrapSearchFormData() {
        $mjtc_search_array = array();
        $mjtc_layout = MJTC_request::MJTC_getVar('mjslay'); // e.g., 'logs' or 'errors'
        
        if ($mjtc_layout == 'logs') {
            $mjtc_search_array['trace_id'] = majesticsupportphplib::MJTC_addslashes(majesticsupportphplib::MJTC_trim(MJTC_request::MJTC_getVar('trace_id')));
        }
        $mjtc_search_array['pagesize'] = absint(MJTC_request::MJTC_getVar('pagesize'));
        return $mjtc_search_array;
    }

    // =========================================================
    // ADVANCED PLAYGROUND (AJAX ENDPOINTS)
    // =========================================================

    function pgGetCategories() {
        $mjtc_query = "SELECT code, name FROM `" . majesticsupport::$_db->prefix . "mjtc_support_zywrap_categories` WHERE status = 1 ORDER BY ordering ASC";
        $mjtc_results = majesticsupport::$_db->get_results($mjtc_query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        wp_send_json($mjtc_results);
    }

    function pgGetUseCases() {
        $cat = sanitize_text_field(MJTC_request::MJTC_getVar('category'));
        $mjtc_query = "SELECT code, name FROM `" . majesticsupport::$_db->prefix . "mjtc_support_zywrap_use_cases` WHERE category_code = '" . esc_sql($cat) . "' AND status = 1 ORDER BY ordering ASC";
        $mjtc_results = majesticsupport::$_db->get_results($mjtc_query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        wp_send_json($mjtc_results);
    }

    function pgGetWrappers() {
        $uc = sanitize_text_field(MJTC_request::MJTC_getVar('usecase'));
        $mjtc_query = "SELECT code, name, featured, base FROM `" . majesticsupport::$_db->prefix . "mjtc_support_zywrap_wrappers` WHERE use_case_code = '" . esc_sql($uc) . "' AND status = 1 ORDER BY ordering ASC";
        $mjtc_results = majesticsupport::$_db->get_results($mjtc_query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        wp_send_json($mjtc_results);
    }

    function pgGetSchema() {
        $w = sanitize_text_field(MJTC_request::MJTC_getVar('wrapper'));
        $mjtc_query = "SELECT uc.schema_data FROM `" . majesticsupport::$_db->prefix . "mjtc_support_zywrap_use_cases` uc JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_zywrap_wrappers` w ON w.use_case_code = uc.code WHERE w.code = '" . esc_sql($w) . "'";
        $res = majesticsupport::$_db->get_var($mjtc_query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        wp_send_json($res ? json_decode($res, true) : null);
    }

    function pgGetLanguages() {
        $mjtc_query = "SELECT code, name FROM `" . majesticsupport::$_db->prefix . "mjtc_support_zywrap_languages` WHERE status = 1 ORDER BY ordering ASC";
        $mjtc_results = majesticsupport::$_db->get_results($mjtc_query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        wp_send_json($mjtc_results);
    }

    function pgGetModels() {
        $mjtc_query = "SELECT code, name FROM `" . majesticsupport::$_db->prefix . "mjtc_support_zywrap_ai_models` WHERE status = 1 ORDER BY ordering ASC";
        $mjtc_results = majesticsupport::$_db->get_results($mjtc_query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        wp_send_json($mjtc_results);
    }

    function pgGetBlockTemplates() {
        $mjtc_query = "SELECT type, code, name FROM `" . majesticsupport::$_db->prefix . "mjtc_support_zywrap_block_templates` WHERE status = 1 ORDER BY type, name ASC";
        $res = majesticsupport::$_db->get_results($mjtc_query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        $grouped = [];
        if ($res) {
            foreach ($res as $r) { $grouped[$r->type][] = ['code' => $r->code, 'name' => $r->name]; }
        }
        wp_send_json($grouped);
    }

    function pgExecute() {
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (!wp_verify_nonce($MJTC_nonce, 'zywrap_ajax_action')) wp_send_json_error(['message' => __('Security check Failed', 'majestic-support')]);

        // SECURITY: ONLY ADMINISTRATORS CAN ACCESS
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Security Error: Unauthorized access. Administrators only.', 'majestic-support')));
            return;
        }

        $apiKey = get_option('mjtc_zywrap_api_key', '');
        if (empty($apiKey)) wp_send_json_error(['message' => __('API Key is not configured.', 'majestic-support')]);

        $model = sanitize_text_field(MJTC_request::MJTC_getVar('model'));
        $wrapperCode = sanitize_text_field(MJTC_request::MJTC_getVar('wrapperCode'));
        
        $prompt = wp_unslash(MJTC_request::MJTC_getVar('prompt'));
        $language = sanitize_text_field(MJTC_request::MJTC_getVar('language'));
        
        $variables = json_decode(wp_unslash(MJTC_request::MJTC_getVar('variables', '[]')), true);
        $overrides = json_decode(wp_unslash(MJTC_request::MJTC_getVar('overrides', '[]')), true);

        $payloadData = [
            'wrapperCodes' => [$wrapperCode], 
            'source' => 'majestic-support-playground'
        ];
        
        if (!empty($model)) $payloadData['model'] = $model;
        if (!empty($prompt)) $payloadData['prompt'] = $prompt;
        if (!empty($variables)) $payloadData['variables'] = $variables;
        if (!empty($language)) $payloadData['language'] = $language;
        if (!empty($overrides)) $payloadData = array_merge($payloadData, $overrides);

        $startTime = microtime(true);
        $ch = curl_init('https://api.zywrap.com/v1/proxy');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payloadData));
        curl_setopt($ch, CURLOPT_TIMEOUT, 600);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json', 
            'Authorization: Bearer ' . $apiKey
        ]);

        $rawResponse = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
        
        $latencyMs = round((microtime(true) - $startTime) * 1000);

        // --- IMPROVED ERROR CATCHING & PARSING ---
        if ($rawResponse === false) {
            wp_send_json_error(['message' => __('Server Connection Error: ', 'majestic-support') . $curlError]);
        }

        $finalJson = null;
        $responseData = null;

        // 1. Check if the API threw a direct JSON error (e.g. 400 Bad Request)
        $directJson = json_decode($rawResponse, true);
        if ($directJson !== null && isset($directJson['error'])) {
            $responseData = $directJson;
        } else {
            // 2. Parse it as a successful Data Stream (SSE)
            foreach (explode("\n", $rawResponse) as $line) {
                $line = trim($line);
                if (strpos($line, 'data: ') === 0) {
                    $data = json_decode(substr($line, 6), true);
                    if ($data && (isset($data['output']) || isset($data['error']))) {
                        $responseData = $data;
                    }
                }
            }
        }

        // Determine actual status
        $status = ($httpCode === 200 && $responseData && !isset($responseData['error'])) ? 'success' : 'error';
        
        // Extract the exact error message if it failed
        $errorMessage = null;
        if ($status === 'error') {
            if (isset($responseData['error'])) {
                $errorMessage = is_array($responseData['error']) ? json_encode($responseData['error']) : $responseData['error'];
            } else {
                // WordPress standard: Use wp_strip_all_tags instead of strip_tags
                // Also adjusted concatenation to avoid potential translation placeholder issues later
                $errorMessage = __('API Error (HTTP ', 'majestic-support') . $httpCode . '): ' . substr(wp_strip_all_tags($rawResponse), 0, 200);
            }
        }

        // --- LOG USAGE ---
        try {
            $mjtc_query = "INSERT INTO `" . majesticsupport::$_db->prefix . "mjtc_support_zywrap_usage_logs` (
                trace_id, wrapper_code, model_code, prompt_tokens, completion_tokens, total_tokens, credits_used, latency_ms, status, error_message, created_at
            ) VALUES (
                '" . esc_sql($responseData['id'] ?? null) . "', 
                '" . esc_sql($wrapperCode) . "', 
                '" . esc_sql($model ?: 'default') . "',
                " . (int)($responseData['usage']['prompt_tokens'] ?? 0) . ", 
                " . (int)($responseData['usage']['completion_tokens'] ?? 0) . ",
                " . (int)($responseData['usage']['total_tokens'] ?? 0) . ", 
                " . (float)($responseData['cost']['credits_used'] ?? 0) . ",
                " . (int)$latencyMs . ", 
                '" . esc_sql($status) . "', 
                '" . esc_sql($errorMessage) . "', 
                '" . current_time('mysql') . "'
            )";
            majesticsupport::$_db->query($mjtc_query);
        } catch (Exception $e) {}

        // --- RESPOND TO FRONTEND ---
        if ($status === 'success') {
            wp_send_json_success($responseData);
        } else {
            wp_send_json_error(['message' => $errorMessage]);
        }
    }
    
    // =========================================================
    // ZYWRAP MAIN DASHBOARD (MODEL)
    // =========================================================
    function getDashboardStats() {
        $prefix = majesticsupport::$_db->prefix . "mjtc_support_";
        
        $dashboard_data = array(
            'api_key'     => get_option('mjtc_zywrap_api_key', ''),
            'last_sync'   => get_option('mjtc_zywrap_last_sync'),
            'total_reqs'  => 0,
            'total_toks'  => 0,
            'total_errs'  => 0,
            'recent_logs' => array()
        );

        try {
            // Aggregate stats
            $mjtc_query = "SELECT COUNT(*) as total_reqs, SUM(total_tokens) as total_toks, SUM(CASE WHEN status='error' THEN 1 ELSE 0 END) as errors FROM `{$prefix}zywrap_usage_logs`";
            $stats = majesticsupport::$_db->get_row($mjtc_query);
            
            if ($stats) {
                $dashboard_data['total_reqs'] = (int) $stats->total_reqs;
                $dashboard_data['total_toks'] = (int) $stats->total_toks;
                $dashboard_data['total_errs'] = (int) $stats->errors;
            }
            
            // Recent logs
            $mjtc_query = "SELECT * FROM `{$prefix}zywrap_usage_logs` ORDER BY id DESC LIMIT 10";
            $dashboard_data['recent_logs'] = majesticsupport::$_db->get_results($mjtc_query);
            
        } catch (Exception $e) {
            // Fail gracefully if table doesn't exist yet
        }

        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }

        // Pass data to the View
        majesticsupport::$_data['dashboard_stats'] = $dashboard_data;
    }

    public function analyzeIncomingTicket($ticket_content, $user_provided_dept, $user_provided_priority, $multiformid = 1) {
        // 1. Validate API Key
        $api_key = get_option('mjtc_zywrap_api_key', '');
        if (empty($api_key)) {
            return false;
        }

        // 2. Read specific configurations
        $wants_sentiment = majesticsupport::$_config['zywrap_enable_sentiment'] ?? '0';
        $wants_upsell    = majesticsupport::$_config['zywrap_detect_sales'] ?? '0';
        $wants_routing   = majesticsupport::$_config['zywrap_auto_route'] ?? '0';
        $wants_priority  = majesticsupport::$_config['zywrap_auto_priority'] ?? '0';

        // 3. Verify Active System Fields
        $field_model = MJTC_includer::MJTC_getModel('fieldordering');
        $active_fields = $field_model->getFieldsForListing(1, $multiformid);

        // 4. Construct the Zero-Prompt JSON Schema Request
        $schema_request = array();

        if ($wants_sentiment == '1') {
            $schema_request['sentiment'] = "string (Strictly choose one: positive, neutral, negative, angry, frustrated)";
        }

        if ($wants_upsell == '1') {
            $schema_request['upsell_opportunity'] = "boolean (true if the user expresses interest in buying more, upgrading, or needs a premium feature)";
        }

        // ONLY ask AI for department if the user did NOT select one
        if ($wants_routing == '1' && !$user_provided_dept && isset($active_fields['department'])) {
            $departments = MJTC_includer::MJTC_getModel('department')->getDepartmentForCombobox();
            $dept_context = array();
            foreach($departments as $dept) {
                $dept_context[] = $dept->id . "='" . $dept->text . "'";
            }
            $schema_request['departmentid'] = "integer (Choose the most appropriate ID from this list based on the ticket context: " . implode(', ', $dept_context) . ". Return 0 if unsure.)";
        }

        // ONLY ask AI for priority if the user did NOT select one
        if ($wants_priority == '1' && !$user_provided_priority && isset($active_fields['priority'])) {
            $priorities = MJTC_includer::MJTC_getModel('priority')->getPriorityForCombobox();
            $pri_context = array();
            foreach($priorities as $pri) {
                $pri_context[] = $pri->id . "='" . $pri->text . "'";
            }
            $schema_request['priorityid'] = "integer (Assess urgency and choose the best ID from this list: " . implode(', ', $pri_context) . ". Return 0 if unsure.)";
        }

        if (empty($schema_request)) {
            return false; // Abort if no features are active or everything was already provided by the user
        }

        // 5. Execute API Call
        // wrapper_code
         
        $payload = array(
            'wrapper_code' => 'csr_incoming_customer_request_triage_base',
            'source'       => 'majestic-support',
            'prompt'       => "Analyze ticket. Return ONLY JSON object: " . wp_json_encode($schema_request) . 
                              "\nTicket: " . $ticket_content
        );

        $response_json = $this->callZywrapEngine($api_key, $payload);

        if (!$response_json) return false;
        
        
        // Simulate successful JSON payload return from the AI Engine
        // $response_json = '{"sentiment":"frustrated","upsell_opportunity":0,"departmentid":2,"priorityid":3}'; 
        return json_decode($response_json, true);
    }

    public function callZywrapEngine($api_key, $payload) {
        $api_url = 'https://api.zywrap.com/v1/proxy';
        $start_time = microtime(true); // Start timing

        // Match generateReply structure
        $body = array(
            'wrapperCodes' => array($payload['wrapper_code']),
            'source'       => 'majestic-support',
            'prompt'       => $payload['prompt']
        );

        $args = array(
            'headers'   => array('Authorization' => 'Bearer ' . $api_key, 'Content-Type' => 'application/json'),
            'body'      => wp_json_encode($body),
            'timeout'   => 600,
            'sslverify' => true,
        );

        $response = wp_remote_post($api_url, $args);
        $latency_ms = round((microtime(true) - $start_time) * 1000);

        if (is_wp_error($response)) {
            $error_msg = __('Zywrap API connection error', 'majestic-support') . ': ' . $response->get_error_message();
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError($error_msg);
            
            // Log as failed attempt
            $this->log_usage(array(), $payload['wrapper_code'], $latency_ms, 'failed', $error_msg);
            return false;
        }

        $raw_response = wp_remote_retrieve_body($response);

        // Stream Parser (matching generateReply)
        $lines = explode("\n", $raw_response);
        $finalJson = null;
        foreach ($lines as $line) {
            $line = trim($line);
            if (strpos($line, 'data: ') === 0) {
                $data = json_decode(substr($line, 6), true);
                if ($data && (isset($data['output']) || isset($data['error']))) {
                    $finalJson = substr($line, 6);
                }
            }
        }

        $body_json = $finalJson ? json_decode($finalJson, true) : null;

        // Handle API Structural Errors (e.g., Insufficient credits)
        if ($body_json && isset($body_json['error'])) {
            $error_msg = sprintf(esc_html__('Zywrap Engine API Error: %s', 'majestic-support'), $body_json['error']);
            
            // Push the clean string trace message to your system error logs layout view
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError($error_msg);
            
            // Log as failed attempt
            $this->log_usage($body_json, $payload['wrapper_code'], $latency_ms, 'failed', $error_msg);
            return false;
        }

        // Handle Success
        if ($body_json && isset($body_json['output'])) {
            $this->log_usage($body_json, $payload['wrapper_code'], $latency_ms, 'success');
            return $body_json['output'];
        }

        return false;
    }
    
    /**
     * Fetches real-time policy data from the configured URL.
     * Caches the response for 24 hours to prevent editor lag.
     */
    public function fetchPolicyContent() {
        $is_enabled = majesticsupport::$_config['zywrap_enable_policy'] ?? '0';
        $url = majesticsupport::$_config['zywrap_policy_url'] ?? '';

        if ($is_enabled != '1' || empty($url)) {
            return '';
        }

        $cache_key = 'zywrap_policy_content_cache';
        $cached_content = get_transient($cache_key);
        if ($cached_content !== false) {
            // return $cached_content;
        }

        $response = wp_remote_get(esc_url_raw($url), array('timeout' => 10));
        $content = '';
        
        if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) == 200) {
            $body = wp_remote_retrieve_body($response);
            $content = wp_strip_all_tags($body); 
            $content = substr($content, 0, 5000); 
        }

        set_transient($cache_key, $content, DAY_IN_SECONDS);
        return $content;
    }

    /**
     * Validates agent drafts against the dynamic policy.
     */
    public function checkToneAndPolicy($draft_text) {
        // Only proceed if feature is enabled in config
        $is_enabled = majesticsupport::$_config['zywrap_enable_policy'] ?? '0';
        $api_key = majesticsupport::$_config['zywrap_api_key'] ?? get_option('mjtc_zywrap_api_key', '');
        
        if ($is_enabled != '1' || empty($api_key) || empty(trim($draft_text))) {
            return array('passed' => true);
        }

        $policy = $this->fetchPolicyContent();
        
        $prompt = "You are a Support Quality Assurance Specialist.
                   Analyze this Draft against our Company Policy.
                   --- POLICY START ---
                   " . (!empty($policy) ? $policy : "Be professional and helpful.") . "
                   --- POLICY END ---
                   
                   Draft to check: " . sanitize_textarea_field($draft_text) . "
                   
                   INSTRUCTIONS:
                   1. If the draft is acceptable, return passed: true.
                   2. If the draft violates policy or tone, return passed: false.
                   3. If passed: false, provide a 'feedback' string that is:
                      - Maximum 25 words.
                      - Action-oriented and polite.
                      - Specifically identifies what to change (e.g., 'Tone is too aggressive; please use a softer, more collaborative opening.').
                   
                   Return ONLY a raw JSON object matching this schema: {\"passed\": boolean, \"feedback\": \"string\"}.";

        $payload = array(
            'wrapper_code' => 'csr_cust_repl_tone_poli_revi_1545_base', 
            'prompt'       => $prompt,
            'variables'    => array()
        );

        $response_json = $this->callZywrapEngine($api_key, $payload); // Existing proxy method
        
        if ($response_json) {
            $clean_response = trim($response_json);
            $json_start = strpos($clean_response, '{');
            $json_end = strrpos($clean_response, '}');
            
            if ($json_start !== false && $json_end !== false) {
                $clean_response = substr($clean_response, $json_start, ($json_end - $json_start) + 1);
                $data = json_decode($clean_response, true);
                return is_array($data) ? $data : array('passed' => true);
            }
        }
        
        return array('passed' => true);
    }

    public function checkPolicyTask() {
        // 1. Verify Nonce 
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (!wp_verify_nonce($MJTC_nonce, 'zywrap_policy_nonce')) {
            wp_send_json_error(array('message' => __('Security check Failed', 'majestic-support')));
        }

        // 2. Capabilities Check
        if (!current_user_can('ms_support_ticket')) {
            wp_send_json_error(array('message' => __('Unauthorized access.', 'majestic-support')));
        }

        // 3. Sanitization
        $draft_text = sanitize_textarea_field(MJTC_request::MJTC_getVar('draft_text'));
        
        if (empty($draft_text)) {
            wp_send_json_success(array('passed' => true));
        }

        // 4. Execution
        $zywrap_model = MJTC_includer::MJTC_getModel('zywrap');
        $result = $zywrap_model->checkToneAndPolicy($draft_text);

        wp_send_json_success($result);
    }

    /**
     * Background Task: Stale Ticket Follow-up Generator
     */
    public function processStaleTicketsTask() {
        // 1. Exit if feature is disabled
        if ((majesticsupport::$_config['zywrap_enable_followup'] ?? '0') != '1') {
            return;
        }
        
        // 2. Fetch stale tickets (using your 48-hour logic)
        $stale_tickets = $this->getStaleTickets(48);

        // 3. Process each ticket
        foreach ($stale_tickets as $ticket) {
            $prompt = "Draft a short, polite, and professional follow-up message to the customer regarding their inquiry: " . $ticket->message;
            
            $api_response = $this->callZywrapEngine(get_option('mjtc_zywrap_api_key'), [
                'wrapper_code' => 'csr_inactive_customer_case_followup_base',
                'prompt'       => $prompt
            ]);

            // 4. Handle the JSON Response
            // The API returns the output as a string (JSON). We decode it to get the object.
            $data = is_string($api_response) ? json_decode($api_response, true) : null;

            if ($data && isset($data['message'])) {
                // Save the message content to the draft system
                $this->saveAsAiDraft($ticket->id, $data['message']);
                
                // Optional: You can also use the extra data provided by the AI for your logs
                // e.g., error_log("Follow-up generated for ticket " . $ticket->id . " Type: " . $data['followuptype']);
            } else {
                // Log a system error if the AI returned invalid structure or empty message
                $error_log = __('AI follow-up generation returned invalid structure for ticket ID: ', 'majestic-support') . $ticket->id;
                MJTC_includer::MJTC_getModel('systemerror')->addSystemError($error_log);
            }
        }
    }

    /**
     * 1. Fetch tickets waiting for customer reply > 48 hours
     */
    public function getStaleTickets($hours = 48) {
        global $wpdb;

        $hours = max(1, absint($hours));
        $tickets_table = $wpdb->prefix . 'mjtc_support_tickets';
        $replies_table = $wpdb->prefix . 'mjtc_support_replies';

        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT t.id, t.message
                 FROM {$tickets_table} t
                 LEFT JOIN {$replies_table} r ON r.ticketid = t.id
                 WHERE t.status IN (1, 2)
                 GROUP BY t.id
                 HAVING COALESCE(MAX(r.created), MAX(t.created)) < (NOW() - INTERVAL %d HOUR)
                 AND NOT EXISTS (
                    SELECT 1 FROM {$replies_table} r2
                    WHERE r2.ticketid = t.id AND r2.is_ai_draft = 1
                 )",
                $hours
            )
        );
    }

    /**
     * 2. Save the AI Follow-up Draft
     */
    public function saveAsAiDraft($ticket_id, $draft_content) {
        global $wpdb;

        $ticket_id = absint($ticket_id);
        if (!$ticket_id) {
            return false;
        }

        return (bool) $wpdb->insert(
            $wpdb->prefix . 'mjtc_support_replies',
            array(
                'ticketid'    => $ticket_id,
                'message'     => wp_kses_post($draft_content),
                'is_ai_draft' => 1,
                'created'     => current_time('mysql'),
            ),
            array('%d', '%s', '%d', '%s')
        );
    }

    /**
     * 3. Flush (Delete) drafts instantly when context changes
     */
    public function flushDraftsForTicket($ticket_id) {
        global $wpdb;

        $ticket_id = absint($ticket_id);
        if (!$ticket_id) {
            return false;
        }

        return $wpdb->delete(
            $wpdb->prefix . 'mjtc_support_replies',
            array(
                'ticketid'    => $ticket_id,
                'is_ai_draft' => 1,
            ),
            array('%d', '%d')
        );
    }

    public function discardDraftTask() {
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (!wp_verify_nonce($MJTC_nonce, 'zywrap_triage_nonce')) {
            wp_send_json_error(array('message' => __('Security check Failed', 'majestic-support')), 403);
        }

        if (!current_user_can('ms_support_ticket')) {
            wp_send_json_error(array('message' => __('Unauthorized access.', 'majestic-support')), 403);
        }

        global $wpdb;
        $reply_id = absint(MJTC_request::MJTC_getVar('reply_id'));
        if (!$reply_id) {
            wp_send_json_error(array('message' => __('Invalid draft.', 'majestic-support')), 400);
        }

        $reply = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT id, ticketid FROM {$wpdb->prefix}mjtc_support_replies WHERE id = %d AND is_ai_draft = 1",
                $reply_id
            )
        );

        if (!$reply) {
            wp_send_json_error(array('message' => __('Draft not found.', 'majestic-support')), 404);
        }

        if (!current_user_can('manage_options') && class_exists('MJTC_includer')) {
            $can_access = false;
            if (in_array('agent', majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) {
                $can_access = true;
            } elseif (MJTC_includer::MJTC_getModel('ticket')->validateTicketDetailForUser($reply->ticketid)) {
                $can_access = true;
            }

            if (!$can_access) {
                wp_send_json_error(array('message' => __('Unauthorized access.', 'majestic-support')), 403);
            }
        }

        $deleted = $wpdb->delete(
            $wpdb->prefix . 'mjtc_support_replies',
            array(
                'id'          => $reply_id,
                'is_ai_draft' => 1,
            ),
            array('%d', '%d')
        );

        wp_send_json_success(array('deleted' => (bool) $deleted));
    }
}
