<?php
if (!defined('ABSPATH')) die('Restricted Access');

// 1. Get Data
$mjtc_support_api_key      = get_option('mjtc_zywrap_api_key', '');
$mjtc_support_default_model = get_option('mjtc_zywrap_default_model', '');
$mjtc_support_default_lang  = get_option('mjtc_zywrap_default_lang', 'English');
$mjtc_support_last_sync     = get_option('mjtc_zywrap_last_sync');
$mjtc_support_sync_display  = $mjtc_support_last_sync ? wp_date('M j, Y - g:i A', $mjtc_support_last_sync) : __('Never', 'majestic-support');
$mjtc_support_is_connected  = !empty($mjtc_support_api_key);
MJTC_includer::MJTC_getModel('zywrap')->ensureOfflineWorkflowPack();

// 2. Database verification
$mjtc_support_db_prefix = majesticsupport::$_db->prefix . "mjtc_support_";
$mjtc_support_wrapper_count = 0;
$mjtc_support_db_models = [];
$mjtc_support_db_langs = [];

try {
    $mjtc_support_wrapper_count = (int) majesticsupport::$_db->get_var("SELECT COUNT(*) FROM `{$mjtc_support_db_prefix}zywrap_wrappers`");
    $mjtc_support_db_models = majesticsupport::$_db->get_results("SELECT code, name FROM `{$mjtc_support_db_prefix}zywrap_ai_models` WHERE status = 1 ORDER BY ordering ASC");
    $mjtc_support_db_langs = majesticsupport::$_db->get_results("SELECT code, name FROM `{$mjtc_support_db_prefix}zywrap_languages` WHERE status = 1 ORDER BY ordering ASC");
} catch (Exception $e) { }

MJTC_message::MJTC_getMessage();
?>

<div id="msadmin-wrapper">
    <div id="msadmin-leftmenu">
        <?php MJTC_includer::MJTC_getClassesInclude('msadminsidemenu'); ?>
    </div>
    
    <div id="msadmin-data">
        <?php MJTC_includer::MJTC_getModel('majesticsupport')->getPageTitle('zywrap_settings'); ?>
        
        <div id="msadmin-data-wrp" class="bg-n bs-n mjtc-ai-settings">
            
            <div class="mjtc-settings-grid">
                
                <div class="mjtc-stream-column">

                    <div class="mjtc-config-card">
                        <h2 class="mjtc-card-title">
                            <span class="mjtc-step-badge">1</span> <?php echo esc_html__('API Authentication', 'majestic-support'); ?>
                        </h2>
                        
                        <div class="mjtc-form-group">
                            <label class="mjtc-form-label" for="zywrap_api_key"><?php echo esc_html__('Secure API Key', 'majestic-support'); ?></label>
                            <div class="mjtc-input-wrap">
                                <input type="password" id="zywrap_api_key" class="mjtc-form-input" value="<?php echo esc_attr($mjtc_support_api_key); ?>" placeholder="sk-...">
                                <button type="button" id="zywrap_save_key" class="mjtc-btn-primary">
                                    <span class="dashicons dashicons-saved"></span> <?php echo esc_html__('Save Key', 'majestic-support'); ?>
                                </button>
                            </div>
                            <div id="zywrap_key_msg" class="mjtc-msg-box"></div>
                        </div>
                    </div>

                    <div class="mjtc-config-card <?php echo $mjtc_support_is_connected ? '' : 'dimmed'; ?>">
                        <h2 class="mjtc-card-title">
                            <span class="mjtc-step-badge">2</span> <?php echo esc_html__('AI Workflow Data', 'majestic-support'); ?>
                        </h2>
                        
                        <p style="color: #71717a; margin-bottom: 20px;">
                            <?php echo esc_html__('Majestic Support includes built-in support AI workflows for ticket tools. Enterprise users can still sync the full Zywrap catalog for the Playground; normal API users will use built-in workflows plus runtime model sync.', 'majestic-support'); ?>
                        </p>
                        
                        <div class="mjtc-warning-box">
                            <span class="dashicons dashicons-warning" style="margin-top: 2px;"></span>
                            <div>
                                <strong><?php echo esc_html__('Important:', 'majestic-support'); ?></strong> 
                                <?php echo esc_html__('For Enterprise keys, this may download the full catalog. For normal API keys, this installs built-in support workflows and syncs the current AI model list.', 'majestic-support'); ?>
                            </div>
                        </div>

                        <div>
                            <button type="button" id="zywrap_sync_bundle" class="mjtc-btn-primary mjtc-btn-sync" <?php echo empty($mjtc_support_api_key) ? 'disabled' : ''; ?>>
                                <span class="dashicons dashicons-download"></span> <?php echo esc_html__('Sync AI Data', 'majestic-support'); ?>
                            </button>
                            
                            <div id="zywrap_progress_container" class="mjtc-progress-wrap">
                                <div class="mjtc-progress-track">
                                    <div id="zywrap_progress_fill" class="mjtc-progress-fill"></div>
                                </div>
                                <div class="mjtc-progress-meta">
                                    <span id="zywrap_sync_status"><?php echo esc_html__('Initializing sync...', 'majestic-support'); ?></span>
                                    <span id="zywrap_progress_text">0%</span>
                                </div>
                            </div>
                            <div id="zywrap_quick_status" class="mjtc-msg-box" style="text-align: center;">
                                <?php if(empty($mjtc_support_api_key)) echo '<span style="color: #ef4444;">'.esc_html__('Save API Key to enable sync.', 'majestic-support').'</span>'; ?>
                            </div>
                        </div>
                    </div>

                    <div class="mjtc-config-card <?php echo $mjtc_support_is_connected ? '' : 'dimmed'; ?>">
                        <h2 class="mjtc-card-title">
                            <span class="mjtc-step-badge">3</span> <?php echo esc_html__('Global Preferences', 'majestic-support'); ?>
                        </h2>
                        
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                            <div class="mjtc-form-group">
                                <label class="mjtc-form-label" for="mjtc_support_zywrap_default_model"><?php echo esc_html__('Default AI Model', 'majestic-support'); ?></label>
                                <select id="mjtc_support_zywrap_default_model" class="mjtc-form-select inputbox mjtc-form-select-field" <?php echo empty($mjtc_support_api_key) ? 'disabled' : ''; ?>>
                                    <option value=""><?php echo esc_html__('Platform Auto-Select', 'majestic-support'); ?></option>
                                    <?php foreach ($mjtc_support_db_models as $m) : ?>
                                        <option value="<?php echo esc_attr($m->code); ?>" <?php selected($mjtc_support_default_model, $m->code); ?>><?php echo esc_html($m->name); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="mjtc-form-hint"><?php echo esc_html__('Used automatically unless overridden.', 'majestic-support'); ?></div>
                            </div>

                            <div class="mjtc-form-group">
                                <label class="mjtc-form-label" for="mjtc_support_zywrap_default_lang"><?php echo esc_html__('Translation Target Language', 'majestic-support'); ?></label>
                                <select id="mjtc_support_zywrap_default_lang" class="mjtc-form-select inputbox mjtc-form-select-field" <?php echo empty($mjtc_support_api_key) ? 'disabled' : ''; ?>>
                                    <option value="English" <?php selected($mjtc_support_default_lang, 'English'); ?>><?php echo esc_html__('English', 'majestic-support'); ?></option>
                                    <?php foreach ($mjtc_support_db_langs as $l) : if ($l->name == 'English') continue; ?>
                                        <option value="<?php echo esc_attr($l->name); ?>" <?php selected($mjtc_support_default_lang, $l->name); ?>><?php echo esc_html($l->name); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="mjtc-form-hint"><?php echo esc_html__('1-click ticket translation target.', 'majestic-support'); ?></div>
                            </div>
                        </div>

                        <div style="margin-top: 24px; padding-top: 24px; border-top: 1px solid #e4e4e7; display: flex; align-items: center; gap: 16px;">
                            <button type="button" id="zywrap_save_preferences" class="mjtc-btn-primary" <?php echo empty($mjtc_support_api_key) ? 'disabled' : ''; ?>>
                                <?php echo esc_html__('Save Preferences', 'majestic-support'); ?>
                            </button>
                            <span id="zywrap_pref_msg" class="mjtc-msg-box" style="margin: 0;"></span>
                        </div>
                    </div>
                </div>

                <div class="mjtc-engine-panel">
                    
                    <div class="mjtc-engine-status">
                        <div class="mjtc-engine-section-title" style="margin: 0;"><?php echo esc_html__('Connection Status', 'majestic-support'); ?></div>
                        <?php if ($mjtc_support_is_connected) : ?>
                            <div class="mjtc-status-indicator" style="color: #10b981;">
                                <div class="mjtc-pulse online"></div> <?php echo esc_html__('AUTHENTICATED', 'majestic-support'); ?>
                            </div>
                        <?php else : ?>
                            <div class="mjtc-status-indicator" style="color: #ef4444;">
                                <div class="mjtc-pulse offline"></div> <?php echo esc_html__('UNAUTHORIZED', 'majestic-support'); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mjtc-engine-section-title"><?php echo esc_html__('System Readiness', 'majestic-support'); ?></div>
                    <div class="mjtc-metrics-stack">
                        <div class="mjtc-metric-row">
                            <span><?php echo esc_html__('Wrappers Synced', 'majestic-support'); ?></span>
                            <span style="<?php echo $mjtc_support_wrapper_count > 0 ? 'color: #10b981;' : 'color: #ef4444;'; ?>">
                                <?php echo esc_html($mjtc_support_wrapper_count); ?>
                            </span>
                        </div>
                        <div class="mjtc-metric-row">
                            <span><?php echo esc_html__('Last Synced', 'majestic-support'); ?></span>
                            <span style="font-size: 0.85rem;"><?php echo esc_html($mjtc_support_sync_display); ?></span>
                        </div>
                    </div>

                    <div class="mjtc-docs-box">
                        <h3><span class="dashicons dashicons-media-document" style="color: #3b82f6;"></span> <?php echo esc_html__('Setup Instructions', 'majestic-support'); ?></h3>
                        <ul class="mjtc-docs-list">
                            <li><?php echo esc_html__('Create a free account at', 'majestic-support'); ?> <a href="https://zywrap.com/register" target="_blank">Zywrap.com</a>.</li>
                            <li><?php echo esc_html__('Receive 10,000 Free Credits instantly.', 'majestic-support'); ?></li>
                            <li><?php echo esc_html__('Navigate to API Keys in your dashboard to generate a secret key.', 'majestic-support'); ?></li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<?php
$majesticsupport_js = "
    jQuery(document).ready(function (\$) {
        var ajaxurl = '" . admin_url('admin-ajax.php') . "';

        // 1. Save API Key
        \$('#zywrap_save_key').on('click', function() {
            var btn = \$(this);
            btn.prop('disabled', true).text('" . esc_js(__("Saving...", "majestic-support")) . "');
            \$('#zywrap_key_msg').text('').removeClass('error updated');
            
            \$.post(ajaxurl, {
                action: 'mjsupport_ajax',
                mjsmod: 'zywrap',
                task: 'saveApiKey', 
                api_key: \$('#zywrap_api_key').val(),
                _wpnonce: '" . esc_attr(wp_create_nonce("save_api_key")) . "'
            }, function(response) {
                btn.prop('disabled', false).html('<span class=\"dashicons dashicons-saved\"></span> " . esc_js(__("Save Key", "majestic-support")) . "');
                try {
                    var res = typeof response === 'object' ? response : JSON.parse(response);
                    var messageText = res.data && res.data.message ? res.data.message : res.message;
                    
                    \$('#zywrap_key_msg').text(messageText).css('color', res.success ? '#16a34a' : '#dc2626');
                    if(res.success) { 
                        setTimeout(function(){ location.reload(); }, 1000); 
                    }
                } catch(e) {
                    \$('#zywrap_key_msg').text('" . esc_js(__("System Error", "majestic-support")) . "').css('color', '#dc2626');
                }
            });
        });

        // 2. Save Preferences
        \$('#zywrap_save_preferences').on('click', function() {
            var btn = \$(this);
            var msg = \$('#zywrap_pref_msg');
            btn.prop('disabled', true).text('" . esc_js(__("Saving...", "majestic-support")) . "');
            msg.text('');

            \$.post(ajaxurl, {
                action: 'mjsupport_ajax',
                mjsmod: 'zywrap',
                task: 'savePreferences', 
                default_model: \$('#mjtc_support_zywrap_default_model').val(), 
                default_lang: \$('#mjtc_support_zywrap_default_lang').val(),
                _wpnonce: '" . esc_attr(wp_create_nonce("save_preferences")) . "' 
            }, function(response) {
                btn.prop('disabled', false).text('" . esc_js(__("Save Preferences", "majestic-support")) . "');
                try {
                    var res = typeof response === 'object' ? response : JSON.parse(response);
                    var messageText = res.data && res.data.message ? res.data.message : res.message;
                    
                    msg.text(messageText).css('color', res.success ? '#16a34a' : '#dc2626');
                    if(res.success) { 
                        setTimeout(function(){ msg.fadeOut(300, function(){ \$(this).text('').show(); }); }, 3000); 
                    }
                } catch(e) {
                    msg.text('" . esc_js(__("System Error", "majestic-support")) . "').css('color', '#dc2626');
                }
            });
        });

        // 3. Sync Data Bundle
        \$('#zywrap_sync_bundle').on('click', function() {
            var btn = \$(this);
            var statusContainer = \$('#zywrap_progress_container');
            var quickStatus = \$('#zywrap_quick_status');
            var progressFill = \$('#zywrap_progress_fill');
            var statusText = \$('#zywrap_sync_status');
            var percentText = \$('#zywrap_progress_text');
            
            btn.prop('disabled', true).html('<span class=\"spinner is-active\" style=\"float:none; margin:0 5px 0 0;\"></span> " . esc_js(__("Syncing AI Data...", "majestic-support")) . "');
            quickStatus.text('');
            statusContainer.show();
            progressFill.css({'width': '0%', 'background': '#3b82f6'});
            
            var width = 0;
            var progressInterval = setInterval(function() {
                var increment = (95 - width) * 0.05; 
                width += increment;
                
                progressFill.css('width', width + '%');
                percentText.text(Math.round(width) + '%');

                if (width > 10 && width < 40) { 
                    statusText.text('" . esc_js(__("Connecting to Zywrap Cloud...", "majestic-support")) . "'); 
                } 
                else if (width >= 40 && width < 70) { 
                    statusText.text('" . esc_js(__("Preparing support workflows...", "majestic-support")) . "'); 
                } 
                else if (width >= 70) { 
                    statusText.text('" . esc_js(__("Syncing runtime models...", "majestic-support")) . "'); 
                }
            }, 2000); 

            \$.post(ajaxurl, {
                action: 'mjsupport_ajax',
                mjsmod: 'zywrap',
                task: 'syncDataBundle',
                _wpnonce: '" . esc_attr(wp_create_nonce("sync_data_bundle")) . "'
            }, function(response) {
                clearInterval(progressInterval);
                try {
                    var res = typeof response === 'object' ? response : JSON.parse(response);
                    if (res.success) {
                        progressFill.css({'width': '100%', 'background': '#10b981', 'transition': 'width 0.2s ease-out'});
                        percentText.text('100%').css('color', '#10b981');
                        statusText.text(res.data.message || '" . esc_js(__("Sync Complete!", "majestic-support")) . "').css('color', '#10b981');
                        btn.html('<span class=\"dashicons dashicons-yes-alt\"></span> " . esc_js(__("Synced", "majestic-support")) . "');
                        setTimeout(function(){ location.reload(); }, 2000);
                    } else {
                        progressFill.css('background', '#ef4444');
                        statusText.text('" . esc_js(__("Sync Failed", "majestic-support")) . ".').css('color', '#ef4444');
                        quickStatus.text(res.data.message || res.message).css('color', '#ef4444');
                        btn.prop('disabled', false).html('<span class=\"dashicons dashicons-update-alt\"></span> " . esc_js(__("Try Again", "majestic-support")) . "');
                    }
                } catch(e) {
                    clearInterval(progressInterval);
                    statusContainer.hide();
                    quickStatus.text('" . esc_js(__("A system error occurred.", "majestic-support")) . "').css('color', '#ef4444');
                    btn.prop('disabled', false).html('<span class=\"dashicons dashicons-update-alt\"></span> " . esc_js(__("Try Again", "majestic-support")) . "');
                }
            }).fail(function() {
                clearInterval(progressInterval);
                statusContainer.hide();
                quickStatus.text('" . esc_js(__("Server timeout.", "majestic-support")) . "').css('color', '#ef4444');
                btn.prop('disabled', false).html('<span class=\"dashicons dashicons-update-alt\"></span> " . esc_js(__("Try Again", "majestic-support")) . "');
            });
        });
    });
";

wp_add_inline_script('majestic-support-cmain-js',$majesticsupport_js);
?>
