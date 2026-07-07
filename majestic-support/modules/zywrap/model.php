<?php
if (!defined('ABSPATH')) die('Restricted Access');

class MJTC_zywrapModel {

    /**
     * Built-in workflow data lets Majestic Support ticket AI work without the
     * Enterprise catalog SDK. Enterprise catalog sync remains available for the
     * Playground; this pack is only a small support-focused fallback.
     */
    public function ensureOfflineWorkflowPack() {
        return $this->installOfflineWorkflowPackInternal(false);
    }

    private function getOfflineWorkflowPackPath() {
        return dirname(__FILE__) . '/data/majestic-support-workflows.json';
    }

    private function getOfflineWorkflowPack() {
        static $pack = null;
        if ($pack !== null) {
            return $pack;
        }

        $file = $this->getOfflineWorkflowPackPath();
        if (!file_exists($file) || !is_readable($file)) {
            $pack = array();
            return $pack;
        }

        $raw = file_get_contents($file);
        $data = json_decode($raw, true);
        $pack = is_array($data) ? $data : array();
        return $pack;
    }

    private function getOfflineWrapperCodes() {
        $pack = $this->getOfflineWorkflowPack();
        $codes = array();
        if (!empty($pack['wrappers']) && is_array($pack['wrappers'])) {
            foreach ($pack['wrappers'] as $wrapper) {
                if (!empty($wrapper['code'])) {
                    $codes[] = sanitize_key($wrapper['code']);
                }
            }
        }
        return array_values(array_unique($codes));
    }

    private function isAllowedSupportWrapper($wrapper_code) {
        $wrapper_code = sanitize_key($wrapper_code);
        if (empty($wrapper_code)) {
            return false;
        }
        return in_array($wrapper_code, $this->getOfflineWrapperCodes(), true);
    }


    private function sanitizeRuntimeModelCode($code) {
        $code = trim((string) $code);
        $code = preg_replace('/[^A-Za-z0-9_\-\.\/]/', '', $code);
        return substr($code, 0, 255);
    }

    private function getFallbackRuntimeModels() {
        return array(
            array('code' => 'openai-gpt-4o', 'name' => 'GPT-4o', 'ordering' => 10),
            array('code' => 'openai-gpt-5-mini', 'name' => 'GPT-5 mini', 'ordering' => 11),
            array('code' => 'openai-gpt-4o-mini', 'name' => 'GPT-4o mini', 'ordering' => 12),
            array('code' => 'openai-gpt-4.1-mini', 'name' => 'GPT-4.1 Mini', 'ordering' => 13),
            array('code' => 'openai-gpt-4-nano', 'name' => 'GPT-4.1 Nano', 'ordering' => 14),
            array('code' => 'mistral-medium-latest', 'name' => 'Mistral Medium 3', 'ordering' => 15),
            array('code' => 'deepseek-chat', 'name' => 'DeepSeek-V3.2', 'ordering' => 16),
            array('code' => 'mistral-mixtral-8x22b', 'name' => 'Mixtral 8x22B', 'ordering' => 17),
            array('code' => 'google-gemini-2.5-flash', 'name' => 'Gemini 2.5 Flash', 'ordering' => 18),
            array('code' => 'google-gemini-2.5-pro', 'name' => 'Gemini 2.5 Pro', 'ordering' => 19),
            array('code' => 'mistral-large-2', 'name' => 'Mistral Large v2', 'ordering' => 20),
            array('code' => 'meta-llama-3.1-70b-instruct', 'name' => 'Llama 3.1 70B Instruct', 'ordering' => 21),
            array('code' => 'meta-llama-3.1-405b-instruct', 'name' => 'Llama 3.1 405B Instruct', 'ordering' => 22),
            array('code' => 'mistral-mixtral-8x7b', 'name' => 'Mixtral 8x7B', 'ordering' => 23),
            array('code' => 'mistral-mistral-7b', 'name' => 'Mistral 7B', 'ordering' => 24),
            array('code' => 'cohere-command-r', 'name' => 'Command R', 'ordering' => 25),
            array('code' => 'cohere-command-r-plus', 'name' => 'Command R+', 'ordering' => 26),
            array('code' => 'openai-gpt-5.2', 'name' => 'GPT-5.2', 'ordering' => 27),
            array('code' => 'openai-gpt-5.1', 'name' => 'GPT-5.1', 'ordering' => 28),
            array('code' => 'openai-gpt-5', 'name' => 'GPT-5', 'ordering' => 29),
            array('code' => 'openai-gpt-4-1106-preview', 'name' => 'GPT-4.1', 'ordering' => 30),
            array('code' => 'microsoft-phi-3.5-mini-instruct', 'name' => 'Phi-3.5 Mini Instruct', 'ordering' => 31),
            array('code' => 'aws-titan-text-g1', 'name' => 'Titan Text G1', 'ordering' => 32),
            array('code' => 'baidu-ernie-4.0', 'name' => 'ERNIE 4.0', 'ordering' => 33),
            array('code' => 'perplexity-sonar-large-online', 'name' => 'Sonar Large Online', 'ordering' => 34),
            array('code' => 'mistral-14b-2512', 'name' => 'Ministral 3 14B', 'ordering' => 35),
            array('code' => 'mistral-8b-2512', 'name' => 'Ministral 3 8B', 'ordering' => 36),
            array('code' => 'mistral-3b-2512', 'name' => 'Ministral 3 3B', 'ordering' => 37),
            array('code' => 'ai21-jamba-1.1-instruct', 'name' => 'Jamba 1.1 Instruct', 'ordering' => 38),
            array('code' => 'anthropic-claude-sonnet-4-5', 'name' => 'Claude 4.5 Sonnet', 'ordering' => 39),
            array('code' => 'anthropic-claude-opus-4-1', 'name' => 'Claude 4.1 Opus', 'ordering' => 40),
            array('code' => 'anthropic-claude-haiku-4-5', 'name' => 'Claude 4.5 Haiku', 'ordering' => 41),
            array('code' => 'anthropic-claude-sonnet-4-0', 'name' => 'Claude 4 Sonnet', 'ordering' => 42),
            array('code' => 'openai-gpt-5.4-nano', 'name' => 'GPT-5.4 nano', 'ordering' => 43),
            array('code' => 'xai-grok-code-fast-1', 'name' => 'Grok Code Fast 1', 'ordering' => 44),
            array('code' => 'xai-grok-4-1-fast-non-reasoning', 'name' => 'Grok 4.1 Fast (Non-Reasoning)', 'ordering' => 45),
            array('code' => 'xai-grok-4-1-fast-reasoning', 'name' => 'Grok 4.1 Fast', 'ordering' => 46),
            array('code' => 'xai-grok-4-0709', 'name' => 'Grok 4', 'ordering' => 47),
            array('code' => 'alibaba-qwen-math-plus', 'name' => 'Qwen-Math', 'ordering' => 48),
            array('code' => 'alibaba-qwen3-coder-plus', 'name' => 'Qwen-Coder', 'ordering' => 49),
            array('code' => 'alibaba-qwen-turbo', 'name' => 'Qwen-Turbo', 'ordering' => 50),
            array('code' => 'alibaba-qwen-flash', 'name' => 'Qwen-Flash', 'ordering' => 51),
            array('code' => 'alibaba-qwen3.5-plus', 'name' => 'Qwen-Plus', 'ordering' => 52),
            array('code' => 'alibaba-qwen3-max', 'name' => 'Qwen-Max', 'ordering' => 53),
            array('code' => 'google-gemini-3.1-pro-preview', 'name' => 'Gemini 3.1 Pro', 'ordering' => 54),
            array('code' => 'mistral-medium-2508', 'name' => 'Mistral Medium 3.1', 'ordering' => 55),
            array('code' => 'mistral-large-2512', 'name' => 'Mistral Large 3', 'ordering' => 56),
            array('code' => 'anthropic-claude-sonnet-4-6', 'name' => 'Claude Sonnet 4.6', 'ordering' => 57),
            array('code' => 'anthropic-claude-opus-4-0', 'name' => 'Claude 4 Opus', 'ordering' => 58),
            array('code' => 'anthropic-claude-opus-4-6', 'name' => 'Claude Opus 4.6', 'ordering' => 59),
            array('code' => 'mistral-small-2506', 'name' => 'Mistral Small 3.2', 'ordering' => 60),
            array('code' => 'openai-gpt-5.4-mini', 'name' => 'GPT-5.4 mini', 'ordering' => 61),
            array('code' => 'deepseek-reasoner', 'name' => 'DeepSeek-V3.2 Thinking', 'ordering' => 62),
            array('code' => 'openai-gpt-5.4', 'name' => 'GPT-5.4', 'ordering' => 63),
            array('code' => 'openai-gpt-5.5', 'name' => 'GPT-5.5', 'ordering' => 64),
            array('code' => 'anthropic-claude-opus-4-8', 'name' => 'Claude Opus 4.8', 'ordering' => 65),
            array('code' => 'google-gemini-2.5-flash-lite', 'name' => 'Gemini 2.5 Flash-Lite', 'ordering' => 66),
            array('code' => 'google-gemini-3.5-flash', 'name' => 'Gemini 3.5 Flash', 'ordering' => 67),
            array('code' => 'mistral-medium-3-5', 'name' => 'Mistral Medium 3.5', 'ordering' => 68),
            array('code' => 'alibaba-qwen3.6-plus', 'name' => 'Qwen3.6-Plus', 'ordering' => 69),
            array('code' => 'alibaba-qwen3.7-plus', 'name' => 'Qwen3.7-Plus', 'ordering' => 70),
            array('code' => 'alibaba-qwen3.7-max', 'name' => 'Qwen3.7-Max', 'ordering' => 71),
            array('code' => 'xai-grok-4.3', 'name' => 'grok-4.3', 'ordering' => 72),
            array('code' => 'deepseek-v4-flash', 'name' => 'DeepSeek-V4-Flash', 'ordering' => 73),
            array('code' => 'deepseek-v4-pro', 'name' => 'DeepSeek-V4-Pro', 'ordering' => 74),
        );
    }

    private function seedFallbackRuntimeModelsInternal($only_if_empty = true) {
        $prefix = majesticsupport::$_db->prefix . "mjtc_support_";
        if ($only_if_empty) {
            $count = (int) majesticsupport::$_db->get_var("SELECT COUNT(*) FROM `" . $prefix . "zywrap_ai_models` WHERE status = 1");
            if ($count > 0) {
                return array('success' => true, 'message' => __('Runtime AI models already exist.', 'majestic-support'), 'seeded' => 0);
            }
        }

        $seeded = 0;
        foreach ($this->getFallbackRuntimeModels() as $m) {
            if (empty($m['code'])) {
                continue;
            }
            $model_code = $this->sanitizeRuntimeModelCode($m['code']);
            if (empty($model_code)) {
                continue;
            }
            $mjtc_query = majesticsupport::$_db->prepare(
                "INSERT INTO `" . $prefix . "zywrap_ai_models` (`code`, `name`, `status`, `ordering`) VALUES (%s, %s, %d, %d) ON DUPLICATE KEY UPDATE `name`=VALUES(`name`), `status`=VALUES(`status`), `ordering`=VALUES(`ordering`)",
                $model_code,
                sanitize_text_field($m['name']),
                1,
                (int) $m['ordering']
            );
            majesticsupport::$_db->query($mjtc_query);
            $seeded++;
        }

        update_option('mjtc_zywrap_runtime_models_fallback_seeded', time());
        return array(
            'success' => true,
            'message' => sprintf(
                /* translators: %d: Number of seeded models. */
                __( 'Fallback runtime AI models installed. Models: %d.', 'majestic-support' ),
                $seeded
            ),
            'seeded' => $seeded,
        );
    }

    public function syncRuntimeModelsCron() {
        $api_key = get_option('mjtc_zywrap_api_key');
        if (empty($api_key)) {
            return false;
        }

        $result = $this->syncRuntimeModelsInternal($api_key, true);
        if (empty($result['success']) && empty($result['auth_failed'])) {
            $this->seedFallbackRuntimeModelsInternal(true);
        }
        return $result;
    }

    private function ensureRuntimeModelsAvailable() {
        $prefix = majesticsupport::$_db->prefix . "mjtc_support_";
        $count = (int) majesticsupport::$_db->get_var("SELECT COUNT(*) FROM `" . $prefix . "zywrap_ai_models` WHERE status = 1");
        if ($count <= 0) {
            $this->seedFallbackRuntimeModelsInternal(false);
        }
    }

    private function getImportantSupportTones() {
        return array(
            array('code' => 'professional', 'name' => 'Professional', 'ordering' => 10),
            array('code' => 'empathetic', 'name' => 'Empathetic', 'ordering' => 11),
            array('code' => 'friendly', 'name' => 'Friendly', 'ordering' => 12),
            array('code' => 'calm', 'name' => 'Calm', 'ordering' => 13),
            array('code' => 'concise', 'name' => 'Concise', 'ordering' => 14),
            array('code' => 'direct', 'name' => 'Direct', 'ordering' => 15),
            array('code' => 'neutral', 'name' => 'Neutral', 'ordering' => 16),
            array('code' => 'technical', 'name' => 'Technical', 'ordering' => 17),
            array('code' => 'formal', 'name' => 'Formal', 'ordering' => 18),
            array('code' => 'warm', 'name' => 'Warm', 'ordering' => 19),
            array('code' => 'apologetic', 'name' => 'Apologetic', 'ordering' => 20),
            array('code' => 'reassuring', 'name' => 'Reassuring', 'ordering' => 21),
            array('code' => 'urgent', 'name' => 'Urgent', 'ordering' => 22),
            array('code' => 'clear', 'name' => 'Clear', 'ordering' => 23),
        );
    }

    private function currentUserCanUseTicketAi() {
        if (current_user_can('manage_options') || current_user_can('ms_support_ticket')) {
            return true;
        }

        if (in_array('agent', majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) {
            $permission_model = MJTC_includer::MJTC_getModel('userpermissions');
            if (is_object($permission_model) && method_exists($permission_model, 'MJTC_checkPermissionGrantedForTask')) {
                return (bool) $permission_model->MJTC_checkPermissionGrantedForTask('Use AI Powered Reply Feature');
            }
            return true;
        }

        return false;
    }

    private function installOfflineWorkflowPackInternal($force = false) {
        $pack = $this->getOfflineWorkflowPack();
        if (empty($pack)) {
            return array('success' => false, 'message' => __('Built-in Zywrap workflow pack is missing or invalid.', 'majestic-support'));
        }

        $version = isset($pack['version']) ? sanitize_text_field($pack['version']) : '';
        $installed_version = get_option('mjtc_zywrap_offline_workflow_version', '');

        if (!$force && !empty($version) && $installed_version === $version) {
            $count = (int) majesticsupport::$_db->get_var("SELECT COUNT(*) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_zywrap_wrappers`");
            if ($count > 0) {
                return array('success' => true, 'message' => __('Built-in Zywrap support workflows are already installed.', 'majestic-support'));
            }
        }

        $this->process_offline_workflow_pack($pack);
        if (!empty($version)) {
            update_option('mjtc_zywrap_offline_workflow_version', $version);
        }
        update_option('mjtc_zywrap_offline_workflow_last_install', time());

        return array(
            'success' => true,
            'message' => __('Built-in Zywrap support workflows installed.', 'majestic-support'),
            'version' => $version,
            'useCases' => isset($pack['useCases']) && is_array($pack['useCases']) ? count($pack['useCases']) : 0,
            'wrappers' => isset($pack['wrappers']) && is_array($pack['wrappers']) ? count($pack['wrappers']) : 0,
        );
    }

    private function process_offline_workflow_pack($data) {
        $prefix = majesticsupport::$_db->prefix . "mjtc_support_";

        if (!empty($data['categories']) && is_array($data['categories'])) {
            foreach ($data['categories'] as $c) {
                if (empty($c['code'])) continue;
                $status = (!isset($c['status']) || $c['status']) ? 1 : 0;
                $ordering = isset($c['ordering']) ? (int) $c['ordering'] : 9999;
                $mjtc_query = majesticsupport::$_db->prepare(
                    "INSERT INTO `" . $prefix . "zywrap_categories` (`code`, `name`, `status`, `ordering`) VALUES (%s, %s, %d, %d) ON DUPLICATE KEY UPDATE `name`=VALUES(`name`), `status`=VALUES(`status`), `ordering`=VALUES(`ordering`)",
                    sanitize_key($c['code']),
                    sanitize_text_field(isset($c['name']) ? $c['name'] : $c['code']),
                    (int) $status,
                    (int) $ordering
                );
                majesticsupport::$_db->query($mjtc_query);
            }
        }

        if (!empty($data['useCases']) && is_array($data['useCases'])) {
            foreach ($data['useCases'] as $uc) {
                if (empty($uc['code'])) continue;
                $schema = isset($uc['schemaData']) ? $uc['schemaData'] : (isset($uc['inputSchema']) ? $uc['inputSchema'] : array());
                $schema_json = wp_json_encode($schema);
                $status = (!isset($uc['status']) || $uc['status']) ? 1 : 0;
                $ordering = isset($uc['ordering']) ? (int) $uc['ordering'] : (isset($uc['displayOrder']) ? (int) $uc['displayOrder'] : 9999);
                $mjtc_query = majesticsupport::$_db->prepare(
                    "INSERT INTO `" . $prefix . "zywrap_use_cases` (`code`, `name`, `description`, `category_code`, `schema_data`, `status`, `ordering`) VALUES (%s, %s, %s, %s, %s, %d, %d) ON DUPLICATE KEY UPDATE `name`=VALUES(`name`), `description`=VALUES(`description`), `category_code`=VALUES(`category_code`), `schema_data`=VALUES(`schema_data`), `status`=VALUES(`status`), `ordering`=VALUES(`ordering`)",
                    sanitize_key($uc['code']),
                    sanitize_text_field(isset($uc['name']) ? $uc['name'] : (isset($uc['title']) ? $uc['title'] : $uc['code'])),
                    sanitize_textarea_field(isset($uc['description']) ? $uc['description'] : ''),
                    sanitize_key(isset($uc['categoryCode']) ? $uc['categoryCode'] : ''),
                    $schema_json,
                    (int) $status,
                    (int) $ordering
                );
                majesticsupport::$_db->query($mjtc_query);
            }
        }

        if (!empty($data['wrappers']) && is_array($data['wrappers'])) {
            foreach ($data['wrappers'] as $w) {
                if (empty($w['code'])) continue;
                $status = (!isset($w['status']) || $w['status']) ? 1 : 0;
                $ordering = isset($w['ordering']) ? (int) $w['ordering'] : (isset($w['displayOrder']) ? (int) $w['displayOrder'] : 9999);
                $featured = !empty($w['featured']) || !empty($w['isFeatured']) ? 1 : 0;
                $base = !empty($w['base']) || !empty($w['isBaseWrapper']) ? 1 : 0;
                $mjtc_query = majesticsupport::$_db->prepare(
                    "INSERT INTO `" . $prefix . "zywrap_wrappers` (`code`, `name`, `description`, `use_case_code`, `featured`, `base`, `status`, `ordering`) VALUES (%s, %s, %s, %s, %d, %d, %d, %d) ON DUPLICATE KEY UPDATE `name`=VALUES(`name`), `description`=VALUES(`description`), `use_case_code`=VALUES(`use_case_code`), `featured`=VALUES(`featured`), `base`=VALUES(`base`), `status`=VALUES(`status`), `ordering`=VALUES(`ordering`)",
                    sanitize_key($w['code']),
                    sanitize_text_field(isset($w['name']) ? $w['name'] : $w['code']),
                    sanitize_textarea_field(isset($w['description']) ? $w['description'] : ''),
                    sanitize_key(isset($w['useCaseCode']) ? $w['useCaseCode'] : (isset($w['usecase']) ? $w['usecase'] : '')),
                    (int) $featured,
                    (int) $base,
                    (int) $status,
                    (int) $ordering
                );
                majesticsupport::$_db->query($mjtc_query);
            }
        }

        if (!empty($data['languages']) && is_array($data['languages'])) {
            foreach ($data['languages'] as $l) {
                if (empty($l['code'])) continue;
                $status = (!isset($l['status']) || $l['status']) ? 1 : 0;
                $ordering = isset($l['ordering']) ? (int) $l['ordering'] : 9999;
                $mjtc_query = majesticsupport::$_db->prepare(
                    "INSERT INTO `" . $prefix . "zywrap_languages` (`code`, `name`, `status`, `ordering`) VALUES (%s, %s, %d, %d) ON DUPLICATE KEY UPDATE `name`=VALUES(`name`), `status`=VALUES(`status`), `ordering`=VALUES(`ordering`)",
                    sanitize_key($l['code']),
                    sanitize_text_field(isset($l['name']) ? $l['name'] : $l['code']),
                    (int) $status,
                    (int) $ordering
                );
                majesticsupport::$_db->query($mjtc_query);
            }
        }

        if (!empty($data['blockTemplates']) && is_array($data['blockTemplates'])) {
            foreach ($data['blockTemplates'] as $t) {
                if (empty($t['type']) || empty($t['code'])) continue;
                $status = (!isset($t['status']) || $t['status']) ? 1 : 0;
                $name = isset($t['name']) ? $t['name'] : (isset($t['label']) ? $t['label'] : $t['code']);
                $mjtc_query = majesticsupport::$_db->prepare(
                    "INSERT INTO `" . $prefix . "zywrap_block_templates` (`type`, `code`, `name`, `status`) VALUES (%s, %s, %s, %d) ON DUPLICATE KEY UPDATE `name`=VALUES(`name`), `status`=VALUES(`status`)",
                    sanitize_key($t['type']),
                    sanitize_key($t['code']),
                    sanitize_text_field($name),
                    (int) $status
                );
                majesticsupport::$_db->query($mjtc_query);
            }
        }

        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
    }

    public function syncRuntimeModels() {
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (!wp_verify_nonce($MJTC_nonce, 'sync_data_bundle')) {
            wp_send_json_error(array('message' => __('Security check Failed', 'majestic-support')));
        }
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Security Error: Unauthorized access. Administrators only.', 'majestic-support')));
        }
        $api_key = get_option('mjtc_zywrap_api_key');
        if (empty($api_key)) {
            wp_send_json_error(array('message' => __('Please save your API key first.', 'majestic-support')));
        }
        $result = $this->syncRuntimeModelsInternal($api_key, false);
        if (empty($result['success'])) {
            if (!empty($result['auth_failed'])) {
                wp_send_json_error(array('message' => $result['message']));
            }
            $fallback_result = $this->seedFallbackRuntimeModelsInternal(false);
            wp_send_json_success(array(
                'message' => __('Runtime model sync could not complete now, so fallback AI models were installed. Weekly sync will retry automatically.', 'majestic-support') . ' ' . sanitize_text_field($result['message']),
                'models' => $fallback_result,
                'model_sync_warning' => $result,
            ));
        }
        wp_send_json_success(array('message' => $result['message']));
    }

    private function syncRuntimeModelsInternal($api_key = '', $quiet = true) {
        $api_key = !empty($api_key) ? trim((string) $api_key) : get_option('mjtc_zywrap_api_key');
        if (empty($api_key)) {
            return array('success' => false, 'message' => __('Zywrap API key is missing.', 'majestic-support'));
        }

        $from_version = get_option('mjtc_zywrap_runtime_models_version', '');
        $sync_url = 'https://api.zywrap.com/v1/sdk/runtime/models/sync';
        if (!empty($from_version)) {
            $sync_url = add_query_arg('fromVersion', rawurlencode($from_version), $sync_url);
        }

        $response = wp_remote_get($sync_url, array(
            'timeout' => 120,
            'sslverify' => true,
            'headers' => array(
                'Authorization' => 'Bearer ' . $api_key,
                'Accept' => 'application/json',
            ),
        ));

        if (is_wp_error($response)) {
            return array('success' => false, 'message' => $response->get_error_message());
        }

        $http_code = (int) wp_remote_retrieve_response_code($response);
        if ($http_code === 401 || $http_code === 403) {
            return array(
                'success' => false,
                'auth_failed' => true,
                'http_code' => $http_code,
                'message' => __('Zywrap API key was rejected. Please check the key and try again.', 'majestic-support'),
            );
        }

        if ($http_code !== 200) {
            return array(
                'success' => false,
                'http_code' => $http_code,
                'message' => __('Runtime model sync failed. HTTP code:', 'majestic-support') . ' ' . $http_code,
            );
        }

        $json = json_decode(wp_remote_retrieve_body($response), true);
        if (!is_array($json)) {
            return array('success' => false, 'message' => __('Runtime model sync returned invalid JSON.', 'majestic-support'));
        }

        $payload = array();
        if (isset($json['aiModels'])) {
            $payload = $json['aiModels'];
        } elseif (isset($json['models'])) {
            $payload = $json['models'];
        } elseif (isset($json['data']['aiModels'])) {
            $payload = $json['data']['aiModels'];
        } elseif (isset($json['data']['models'])) {
            $payload = $json['data']['models'];
        }

        $upserts = array();
        $deletes = array();

        if (is_array($payload) && isset($payload['upserts'])) {
            $upserts = is_array($payload['upserts']) ? $payload['upserts'] : array();
            $deletes = isset($payload['deletes']) && is_array($payload['deletes']) ? $payload['deletes'] : array();
        } elseif (is_array($payload) && isset($payload['items'])) {
            $upserts = is_array($payload['items']) ? $payload['items'] : array();
        } elseif (is_array($payload) && isset($payload[0])) {
            $upserts = $payload;
        }

        if (empty($upserts) && empty($deletes)) {
            return array('success' => false, 'message' => __('Runtime model sync did not contain any model changes.', 'majestic-support'));
        }

        $prefix = majesticsupport::$_db->prefix . "mjtc_support_";

        foreach ($upserts as $m) {
            if (!is_array($m) || empty($m['code'])) {
                continue;
            }

            $model_code = $this->sanitizeRuntimeModelCode($m['code']);
            if (empty($model_code)) {
                continue;
            }

            $available = true;
            if (array_key_exists('available', $m)) {
                $available = (bool) $m['available'];
            }

            $status = 1;
            if (array_key_exists('status', $m)) {
                $raw_status = $m['status'];
                if ($raw_status === false || $raw_status === 0 || $raw_status === '0' || in_array(strtolower((string) $raw_status), array('inactive', 'disabled', 'deleted', 'archived'), true)) {
                    $status = 0;
                }
            }

            if (array_key_exists('providerStatus', $m)) {
                $provider_status = $m['providerStatus'];
                if ($provider_status === false || $provider_status === 0 || $provider_status === '0' || in_array(strtolower((string) $provider_status), array('inactive', 'disabled', 'down', 'unavailable'), true)) {
                    $status = 0;
                }
            }

            if (!$available) {
                $status = 0;
            }

            $ordering = isset($m['ordering']) ? (int) $m['ordering'] : (isset($m['displayOrder']) ? (int) $m['displayOrder'] : 9999);
            $model_name = isset($m['name']) ? $m['name'] : (isset($m['title']) ? $m['title'] : $model_code);

            $mjtc_query = majesticsupport::$_db->prepare(
                "INSERT INTO `" . $prefix . "zywrap_ai_models` (`code`, `name`, `status`, `ordering`) VALUES (%s, %s, %d, %d) ON DUPLICATE KEY UPDATE `name`=VALUES(`name`), `status`=VALUES(`status`), `ordering`=VALUES(`ordering`)",
                $model_code,
                sanitize_text_field($model_name),
                (int) $status,
                (int) $ordering
            );
            majesticsupport::$_db->query($mjtc_query);
        }

        foreach ($deletes as $code) {
            $model_code = is_array($code) && isset($code['code']) ? $code['code'] : $code;
            $model_code = $this->sanitizeRuntimeModelCode($model_code);
            if (empty($model_code)) {
                continue;
            }
            $mjtc_query = majesticsupport::$_db->prepare("DELETE FROM `" . $prefix . "zywrap_ai_models` WHERE `code` = %s", $model_code);
            majesticsupport::$_db->query($mjtc_query);
        }

        $new_version = '';
        if (!empty($json['newVersion'])) {
            $new_version = $json['newVersion'];
        } elseif (!empty($json['version'])) {
            $new_version = $json['version'];
        } elseif (!empty($json['aiModels']['version'])) {
            $new_version = $json['aiModels']['version'];
        } elseif (!empty($json['data']['version'])) {
            $new_version = $json['data']['version'];
        }

        if (!empty($new_version)) {
            update_option('mjtc_zywrap_runtime_models_version', sanitize_text_field($new_version));
        }
        update_option('mjtc_zywrap_runtime_models_last_sync', time());

        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
            return array('success' => false, 'message' => __('Runtime model sync database update failed.', 'majestic-support'));
        }

        return array(
            'success' => true,
            'message' => sprintf(
                /* translators: 1: Number of updated models, 2: Number of deleted models. */
                __( 'Runtime AI models synced. Updated: %1$d, deleted: %2$d.', 'majestic-support' ),
                count( $upserts ),
                count( $deletes )
            ),
            'updated' => count($upserts),
            'deleted' => count($deletes),
        );
    }


    private function runOfflineWorkflowFallbackAndRespond($api_key, $reason = '') {
        $offline_result = $this->installOfflineWorkflowPackInternal(false);
        $model_result = $this->syncRuntimeModelsInternal($api_key, true);

        if (empty($model_result['success'])) {
            if (!empty($model_result['auth_failed'])) {
                wp_send_json_error(array(
                    'message' => $model_result['message'],
                    'offline' => $offline_result,
                ));
            }

            $fallback_result = $this->seedFallbackRuntimeModelsInternal(false);
            update_option('mjtc_zywrap_last_sync', time());
            update_option('mjtc_zywrap_last_sync_mode', 'offline_workflows_fallback_models');

            wp_send_json_success(array(
                'message' => __('Built-in support workflows installed. Runtime model sync could not complete now, so fallback AI models were installed and weekly sync will retry automatically.', 'majestic-support'),
                'offline' => $offline_result,
                'models' => $fallback_result,
                'model_sync_warning' => $model_result,
            ));
        }

        update_option('mjtc_zywrap_last_sync', time());
        update_option('mjtc_zywrap_last_sync_mode', 'offline_workflows_runtime_models');

        wp_send_json_success(array(
            'message' => __('Built-in support workflows installed and runtime AI models synced. Enterprise catalog sync was not used for this API key.', 'majestic-support'),
            'offline' => $offline_result,
            'models' => $model_result,
        ));
    }


    function saveApiKey() {
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (!wp_verify_nonce($MJTC_nonce, 'save_api_key')) {
            wp_send_json_error(array('message' => __('Security check Failed', 'majestic-support')));
        }

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Security Error: Unauthorized access. Administrators only.', 'majestic-support')));
            return;
        }

        $api_key = trim((string) MJTC_request::MJTC_getVar('api_key'));

        if (empty($api_key)) {
            wp_send_json_error(array('message' => __('API Key cannot be empty', 'majestic-support')));
        }

        update_option('mjtc_zywrap_api_key', sanitize_text_field($api_key));

        $offline_result = $this->installOfflineWorkflowPackInternal(false);
        $model_result = $this->syncRuntimeModelsInternal($api_key, true);

        if (!empty($model_result['auth_failed'])) {
            delete_option('mjtc_zywrap_api_key');
            wp_send_json_error(array('message' => $model_result['message']));
        }

        if (empty($model_result['success'])) {
            $fallback_result = $this->seedFallbackRuntimeModelsInternal(false);
            wp_send_json_success(array(
                'message' => __('API Key saved. Runtime model sync could not complete now, so a built-in model list was installed and weekly sync will retry automatically.', 'majestic-support') . ' ' . sanitize_text_field($model_result['message']),
                'offline' => $offline_result,
                'models' => $fallback_result,
                'model_sync_warning' => $model_result,
            ));
        }

        wp_send_json_success(array(
            'message' => __('API Key saved and runtime AI models synced successfully.', 'majestic-support'),
            'offline' => $offline_result,
            'models' => $model_result,
        ));
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
            $this->runOfflineWorkflowFallbackAndRespond($api_key, __('Enterprise catalog sync returned HTTP code', 'majestic-support') . ' ' . $http_code);
        }

        $json = json_decode(wp_remote_retrieve_body($response), true);
        if (!$json) {
            $this->runOfflineWorkflowFallbackAndRespond($api_key, __('Enterprise catalog sync returned invalid JSON.', 'majestic-support'));
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

        $model_sync_result = $this->syncRuntimeModelsInternal($api_key, true);
        update_option('mjtc_zywrap_last_sync', time());
        update_option('mjtc_zywrap_last_sync_mode', 'enterprise_catalog_' . sanitize_key($mode));

        $clean_mode = str_replace('_', ' ', $mode);
        $message = __('AI Data Synced Successfully!', 'majestic-support') . ' (' . __('Mode', 'majestic-support') . ': ' . $clean_mode . ')';
        if (!empty($model_sync_result['success'])) {
            $message .= ' ' . $model_sync_result['message'];
        }
        wp_send_json_success(array('message' => $message));
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
            $cats = $this->extract_rows($data['categories']);
            foreach ($cats as $c) {
                $mjtc_query = majesticsupport::$_db->prepare("INSERT INTO `" . $prefix . "zywrap_categories` (`code`, `name`, `ordering`) VALUES (%s, %s, %d)", $c['code'], $c['name'], (int)($c['ordering'] ?? 9999));
                majesticsupport::$_db->query($mjtc_query);
            }
        }

        // ---------------------------------------------------------
        // BATCH INSERT: USE CASES
        // ---------------------------------------------------------
        if (!empty($data['useCases'])) {
            $ucs = $this->extract_rows($data['useCases']);
            $chunk_size = 500; // Safe chunk size to respect MySQL max_allowed_packet
            $chunks = array_chunk($ucs, $chunk_size);
            
            foreach ($chunks as $chunk) {
                $values = array();
                foreach ($chunk as $uc) {
                    $schemaJson = !empty($uc['schema']) ? wp_json_encode($uc['schema']) : null;
                    $values[] = majesticsupport::$_db->prepare("(%s, %s, %s, %s, %s, %d)", $uc['code'], $uc['name'], $uc['desc'] ?? '', $uc['cat'] ?? '', $schemaJson, (int)($uc['ordering'] ?? 9999));
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
            $wrappers = $this->extract_rows($data['wrappers']);
            $chunk_size = 1000; // Grouping 1000 wrappers per query
            $chunks = array_chunk($wrappers, $chunk_size);
            
            foreach ($chunks as $chunk) {
                $values = array();
                foreach ($chunk as $w) {
                    $values[] = majesticsupport::$_db->prepare("(%s, %s, %s, %s, %d, %d, %d)", $w['code'], $w['name'], $w['desc'] ?? '', $w['usecase'] ?? '', !empty($w['featured']) ? 1 : 0, !empty($w['base']) ? 1 : 0, (int)($w['ordering'] ?? 9999));
                }
                
                // Construct a single query with 1000 rows
                $mjtc_query = "INSERT INTO `" . $prefix . "zywrap_wrappers` (`code`, `name`, `description`, `use_case_code`, `featured`, `base`, `ordering`) VALUES " . implode(', ', $values);
                majesticsupport::$_db->query($mjtc_query);
            }
        }

        if (!empty($data['aiModels'])) {
            $models = $this->extract_rows($data['aiModels']);
            foreach ($models as $m) {
                $mjtc_query = majesticsupport::$_db->prepare("INSERT INTO `" . $prefix . "zywrap_ai_models` (`code`, `name`, `ordering`) VALUES (%s, %s, %d)", $m['code'], $m['name'], (int)($m['ordering'] ?? 9999));
                majesticsupport::$_db->query($mjtc_query);
            }
        }

        if (!empty($data['languages'])) {
            $langs = $this->extract_rows($data['languages']);
            foreach ($langs as $l) {
                $mjtc_query = majesticsupport::$_db->prepare("INSERT INTO `" . $prefix . "zywrap_languages` (`code`, `name`, `ordering`) VALUES (%s, %s, %d)", $l['code'], $l['name'], (int)($l['ordering'] ?? 9999));
                majesticsupport::$_db->query($mjtc_query);
            }
        }

        if (!empty($data['templates'])) {
            foreach ($data['templates'] as $type => $tabular) {
                $templates = $this->extract_rows($tabular);
                foreach ($templates as $t) {
                    $mjtc_query = majesticsupport::$_db->prepare("INSERT INTO `" . $prefix . "zywrap_block_templates` (`type`, `code`, `name`) VALUES (%s, %s, %s)", $type, $t['code'], $t['name']);
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
                $mjtc_query = majesticsupport::$_db->prepare("INSERT INTO `" . $prefix . "zywrap_categories` (`code`, `name`, `status`, `ordering`) 
                               VALUES (%s, %s, %d, %d) 
                               ON DUPLICATE KEY UPDATE `name`=VALUES(`name`), `status`=VALUES(`status`), `ordering`=VALUES(`ordering`)", $r['code'], $r['name'], (int)$status, (int)$ordering);
                majesticsupport::$_db->query($mjtc_query);
            }
        }

        if (!empty($json['metadata']['languages'])) {
            foreach ($json['metadata']['languages'] as $r) {
                $status = (!isset($r['status']) || $r['status']) ? 1 : 0;
                $ordering = $r['ordering'] ?? 9999;
                $mjtc_query = majesticsupport::$_db->prepare("INSERT INTO `" . $prefix . "zywrap_languages` (`code`, `name`, `status`, `ordering`) 
                               VALUES (%s, %s, %d, %d) 
                               ON DUPLICATE KEY UPDATE `name`=VALUES(`name`), `status`=VALUES(`status`), `ordering`=VALUES(`ordering`)", $r['code'], $r['name'], (int)$status, (int)$ordering);
                majesticsupport::$_db->query($mjtc_query);
            }
        }

        if (!empty($json['metadata']['aiModels'])) {
            foreach ($json['metadata']['aiModels'] as $r) {
                $status = (!isset($r['status']) || $r['status']) ? 1 : 0;
                $ordering = $r['displayOrder'] ?? $r['ordering'] ?? 9999;
                $mjtc_query = majesticsupport::$_db->prepare("INSERT INTO `" . $prefix . "zywrap_ai_models` (`code`, `name`, `status`, `ordering`) 
                               VALUES (%s, %s, %d, %d) 
                               ON DUPLICATE KEY UPDATE `name`=VALUES(`name`), `status`=VALUES(`status`), `ordering`=VALUES(`ordering`)", $r['code'], $r['name'], (int)$status, (int)$ordering);
                majesticsupport::$_db->query($mjtc_query);
            }
        }

        if (!empty($json['metadata']['templates'])) {
            foreach ($json['metadata']['templates'] as $type => $items) {
                foreach ($items as $item) {
                    $status = (!isset($item['status']) || $item['status']) ? 1 : 0;
                    $name = $item['label'] ?? $item['name'] ?? '';
                    $mjtc_query = majesticsupport::$_db->prepare("INSERT INTO `" . $prefix . "zywrap_block_templates` (`type`, `code`, `name`, `status`) 
                                   VALUES (%s, %s, %s, %d) 
                                   ON DUPLICATE KEY UPDATE `name`=VALUES(`name`), `status`=VALUES(`status`)", $type, $item['code'], $name, (int)$status);
                    majesticsupport::$_db->query($mjtc_query);
                }
            }
        }

        if (!empty($json['useCases']['upserts'])) {
            foreach ($json['useCases']['upserts'] as $uc) {
                $schemaJson = !empty($uc['schema']) ? wp_json_encode($uc['schema']) : null;
                $status = (!isset($uc['status']) || $uc['status']) ? 1 : 0;
                $ordering = $uc['displayOrder'] ?? $uc['ordering'] ?? 9999;
                $mjtc_query = majesticsupport::$_db->prepare(
                    "INSERT INTO `" . $prefix . "zywrap_use_cases` (`code`, `name`, `description`, `category_code`, `schema_data`, `status`, `ordering`) 
                     VALUES (%s, %s, %s, %s, %s, %d, %d) 
                     ON DUPLICATE KEY UPDATE `name`=VALUES(`name`), `description`=VALUES(`description`), `category_code`=VALUES(`category_code`), `schema_data`=VALUES(`schema_data`), `status`=VALUES(`status`), `ordering`=VALUES(`ordering`)",
                    $uc['code'],
                    $uc['name'],
                    $uc['description'] ?? '',
                    $uc['categoryCode'] ?? '',
                    $schemaJson,
                    (int) $status,
                    (int) $ordering
                );
                majesticsupport::$_db->query($mjtc_query);
            }
        }

        if (!empty($json['useCases']['deletes'])) {
            foreach ($json['useCases']['deletes'] as $code) {
                $mjtc_query = majesticsupport::$_db->prepare("DELETE FROM `" . $prefix . "zywrap_use_cases` WHERE `code` = %s", $code);
                majesticsupport::$_db->query($mjtc_query);
            }
        }

        if (!empty($json['wrappers']['upserts'])) {
            foreach ($json['wrappers']['upserts'] as $w) {
                $featured = !empty($w['featured'] ?? $w['isFeatured']) ? 1 : 0;
                $base = !empty($w['base'] ?? $w['isBaseWrapper']) ? 1 : 0;
                $status = (!isset($w['status']) || $w['status']) ? 1 : 0;
                $ordering = $w['displayOrder'] ?? $w['ordering'] ?? 9999;
                $mjtc_query = majesticsupport::$_db->prepare(
                    "INSERT INTO `" . $prefix . "zywrap_wrappers` (`code`, `name`, `description`, `use_case_code`, `featured`, `base`, `status`, `ordering`) 
                     VALUES (%s, %s, %s, %s, %d, %d, %d, %d) 
                     ON DUPLICATE KEY UPDATE `name`=VALUES(`name`), `description`=VALUES(`description`), `use_case_code`=VALUES(`use_case_code`), `featured`=VALUES(`featured`), `base`=VALUES(`base`), `status`=VALUES(`status`), `ordering`=VALUES(`ordering`)",
                    $w['code'],
                    $w['name'],
                    $w['description'] ?? '',
                    $w['useCaseCode'] ?? ($w['categoryCode'] ?? ''),
                    (int) $featured,
                    (int) $base,
                    (int) $status,
                    (int) $ordering
                );
                majesticsupport::$_db->query($mjtc_query);
            }
        }

        if (!empty($json['wrappers']['deletes'])) {
            foreach ($json['wrappers']['deletes'] as $code) {
                $mjtc_query = majesticsupport::$_db->prepare("DELETE FROM `" . $prefix . "zywrap_wrappers` WHERE `code` = %s", $code);
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

    private function extract_rows($data) {
        if (empty($data) || !is_array($data)) return array();
        if (isset($data['cols']) && isset($data['data'])) return $this->extract_tabular($data);
        return $data;
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
        $this->ensureOfflineWorkflowPack();
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
        $this->ensureOfflineWorkflowPack();
        $prefix = majesticsupport::$_db->prefix . "mjtc_support_";
        $mjtc_query = majesticsupport::$_db->prepare("SELECT code, name, base FROM `" . $prefix . "zywrap_wrappers` 
                       WHERE use_case_code = %s AND status = 1 
                       ORDER BY base DESC, ordering ASC", $use_case_code); // Base wrapper shows first
                          
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
        // Co-Pilot should stay simple. Return only support-relevant tones
        // instead of the full Zywrap tone catalog.
        $rows = array();
        foreach ($this->getImportantSupportTones() as $tone) {
            $rows[] = (object) array(
                'code' => $tone['code'],
                'name' => $tone['name'],
            );
        }
        return $rows;
    }


    static function ajaxGetWrappers() {
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (!wp_verify_nonce($MJTC_nonce, 'zywrap_ajax_action')) {
            wp_send_json_error(array('message' => __('Security check Failed', 'majestic-support')));
        }

        $use_case_code = sanitize_key(MJTC_request::MJTC_getVar('use_case_code'));
        $ticket_id = absint(MJTC_request::MJTC_getVar('ticket_id'));

        $model_instance = new self();
        if (!$model_instance->currentUserCanUseTicketAi()) {
            wp_send_json_error(array('message' => __('You are not allowed to use Zywrap AI on tickets.', 'majestic-support')), 403);
        }
        $wrappers = $model_instance->getWrappersByUseCase($use_case_code);

        $prefix = majesticsupport::$_db->prefix . "mjtc_support_";
        $mjtc_query = majesticsupport::$_db->prepare("SELECT schema_data FROM `" . $prefix . "zywrap_use_cases` WHERE code = %s", $use_case_code);
        $schema_json = majesticsupport::$_db->get_var($mjtc_query);
        $schema = !empty($schema_json) ? json_decode($schema_json, true) : null;

        // Fetch Clean Ticket Data from PHP
        $ticketData = $model_instance->getTicketContext($ticket_id);
        if (empty($ticket_id) || (empty($ticketData['subject']) && empty($ticketData['fullThread']))) {
            wp_send_json_error(array('message' => __('Ticket context was not found. Please reopen Zywrap from a ticket detail page.', 'majestic-support')), 404);
        }

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

        if (!$this->currentUserCanUseTicketAi()) {
            wp_send_json_error(array('message' => __('You are not allowed to use Zywrap AI on tickets.', 'majestic-support')), 403);
        }

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
        if (!$this->isAllowedSupportWrapper($wrapper_code)) {
            wp_send_json_error(array('message' => __('Invalid support AI workflow.', 'majestic-support')));
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
        $this->ensureOfflineWorkflowPack();
        $this->ensureRuntimeModelsAvailable();

        $prefix = majesticsupport::$_db->prefix . "mjtc_support_";
        $mjtc_query = "SELECT code, name FROM `" . $prefix . "zywrap_ai_models`
                       WHERE status = 1
                       ORDER BY ordering ASC, name ASC";

        $mjtc_results = majesticsupport::$_db->get_results($mjtc_query);
        if (empty($mjtc_results)) {
            $this->seedFallbackRuntimeModelsInternal(false);
            $mjtc_results = majesticsupport::$_db->get_results($mjtc_query);
        }

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
        $mjtc_query = majesticsupport::$_db->prepare("SELECT subject, message, uid FROM `{$prefix}tickets` WHERE id = %d", absint($ticket_id));
        $ticket = majesticsupport::$_db->get_row($mjtc_query);
        if (!$ticket) {
            return array('subject' => '', 'initialMsg' => '', 'fullThread' => '', 'latestCustomerMsg' => '');
        }

        $subject = wp_strip_all_tags(strip_shortcodes($ticket->subject));
        $initialMsg = wp_strip_all_tags(strip_shortcodes($ticket->message));
        $latestCustomerMsg = $initialMsg;

        $history = "CUSTOMER (Initial Issue):\n" . $initialMsg . "\n\n";

        // The column name in mjtc_support_replies is 'message', not 'reply'
        $mjtc_query = majesticsupport::$_db->prepare("SELECT message, uid FROM `{$prefix}replies` WHERE ticketid = %d ORDER BY created ASC", absint($ticket_id));
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

        if (!empty($api_key)) {
            $this->installOfflineWorkflowPackInternal(false);
            $model_result = $this->syncRuntimeModelsInternal($api_key, true);
            if (!empty($model_result['auth_failed'])) {
                wp_send_json_error(array('message' => $model_result['message']));
            }
            if (empty($model_result['success'])) {
                $this->seedFallbackRuntimeModelsInternal(true);
            }
        }

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
            $mjtc_inquery .= majesticsupport::$_db->prepare(" WHERE trace_id LIKE %s", '%' . majesticsupport::$_db->esc_like($mjtc_trace_id) . '%');
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
            MJTC_message::MJTC_setMessage(esc_html(__('Log deleted successfully.', 'majestic-support')), 'updated');
        } else {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
            MJTC_message::MJTC_setMessage(esc_html(__('Failed to delete log.', 'majestic-support')), 'error');
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
        $this->ensureOfflineWorkflowPack();
        $mjtc_query = "SELECT code, name FROM `" . majesticsupport::$_db->prefix . "mjtc_support_zywrap_categories` WHERE status = 1 ORDER BY ordering ASC";
        $mjtc_results = majesticsupport::$_db->get_results($mjtc_query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        wp_send_json($mjtc_results);
    }

    function pgGetUseCases() {
        $this->ensureOfflineWorkflowPack();
        $cat = sanitize_text_field(MJTC_request::MJTC_getVar('category'));
        $mjtc_query = majesticsupport::$_db->prepare("SELECT code, name FROM `" . majesticsupport::$_db->prefix . "mjtc_support_zywrap_use_cases` WHERE category_code = %s AND status = 1 ORDER BY ordering ASC", $cat);
        $mjtc_results = majesticsupport::$_db->get_results($mjtc_query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        wp_send_json($mjtc_results);
    }

    function pgGetWrappers() {
        $this->ensureOfflineWorkflowPack();
        $uc = sanitize_text_field(MJTC_request::MJTC_getVar('usecase'));
        $mjtc_query = majesticsupport::$_db->prepare("SELECT code, name, featured, base FROM `" . majesticsupport::$_db->prefix . "mjtc_support_zywrap_wrappers` WHERE use_case_code = %s AND status = 1 ORDER BY ordering ASC", $uc);
        $mjtc_results = majesticsupport::$_db->get_results($mjtc_query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        wp_send_json($mjtc_results);
    }

    function pgGetSchema() {
        $this->ensureOfflineWorkflowPack();
        $w = sanitize_text_field(MJTC_request::MJTC_getVar('wrapper'));
        $mjtc_query = majesticsupport::$_db->prepare("SELECT uc.schema_data FROM `" . majesticsupport::$_db->prefix . "mjtc_support_zywrap_use_cases` uc JOIN `" . majesticsupport::$_db->prefix . "mjtc_support_zywrap_wrappers` w ON w.use_case_code = uc.code WHERE w.code = %s", $w);
        $res = majesticsupport::$_db->get_var($mjtc_query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        wp_send_json($res ? json_decode($res, true) : null);
    }

    function pgGetLanguages() {
        $this->ensureOfflineWorkflowPack();
        $mjtc_query = "SELECT code, name FROM `" . majesticsupport::$_db->prefix . "mjtc_support_zywrap_languages` WHERE status = 1 ORDER BY ordering ASC";
        $mjtc_results = majesticsupport::$_db->get_results($mjtc_query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        wp_send_json($mjtc_results);
    }

    function pgGetModels() {
        $this->ensureOfflineWorkflowPack();
        $mjtc_query = "SELECT code, name FROM `" . majesticsupport::$_db->prefix . "mjtc_support_zywrap_ai_models` WHERE status = 1 ORDER BY ordering ASC";
        $mjtc_results = majesticsupport::$_db->get_results($mjtc_query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        wp_send_json($mjtc_results);
    }

    function pgGetBlockTemplates() {
        $this->ensureOfflineWorkflowPack();
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
        $MJTC_response = wp_remote_post('https://api.zywrap.com/v1/proxy', array(
            'timeout'   => 600,
            'sslverify' => true,
            'headers'   => array(
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer ' . $apiKey,
                'Accept'        => 'application/json, text/event-stream',
            ),
            'body'      => wp_json_encode($payloadData),
        ));

        $latencyMs = round((microtime(true) - $startTime) * 1000);

        if (is_wp_error($MJTC_response)) {
            wp_send_json_error(array('message' => __('Server Connection Error: ', 'majestic-support') . $MJTC_response->get_error_message()));
        }

        $rawResponse = wp_remote_retrieve_body($MJTC_response);
        $httpCode = (int) wp_remote_retrieve_response_code($MJTC_response);

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
            majesticsupport::$_db->insert(
                majesticsupport::$_db->prefix . "mjtc_support_zywrap_usage_logs",
                array(
                    'trace_id' => sanitize_text_field($responseData['id'] ?? ''),
                    'wrapper_code' => sanitize_text_field($wrapperCode),
                    'model_code' => sanitize_text_field($model ?: 'default'),
                    'prompt_tokens' => (int)($responseData['usage']['prompt_tokens'] ?? 0),
                    'completion_tokens' => (int)($responseData['usage']['completion_tokens'] ?? 0),
                    'total_tokens' => (int)($responseData['usage']['total_tokens'] ?? 0),
                    'credits_used' => (float)($responseData['cost']['credits_used'] ?? 0),
                    'latency_ms' => (int)$latencyMs,
                    'status' => sanitize_key($status),
                    'error_message' => sanitize_text_field($errorMessage),
                    'created_at' => current_time('mysql'),
                ),
                array('%s','%s','%s','%d','%d','%d','%f','%d','%s','%s','%s')
            );
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
        if (empty($payload['wrapper_code']) || !$this->isAllowedSupportWrapper($payload['wrapper_code'])) {
            return array('error' => __('Invalid support AI workflow.', 'majestic-support'));
        }
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
            $error_msg = sprintf(
                /* translators: %s: The specific API error message. */
                esc_html__( 'Zywrap Engine API Error: %s', 'majestic-support' ),
                esc_html( $body_json['error'] )
            );
            
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
        if (!current_user_can('manage_options') && !current_user_can('ms_support_ticket')) {
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

        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT t.id, t.message
                 FROM {$wpdb->prefix}mjtc_support_tickets t
                 LEFT JOIN {$wpdb->prefix}mjtc_support_replies r ON r.ticketid = t.id
                 WHERE t.status IN (1, 2)
                 GROUP BY t.id
                 HAVING COALESCE(MAX(r.created), MAX(t.created)) < (NOW() - INTERVAL %d HOUR)
                 AND NOT EXISTS (
                    SELECT 1 FROM {$wpdb->prefix}mjtc_support_replies r2
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

        if (!current_user_can('manage_options') && !current_user_can('ms_support_ticket')) {
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
