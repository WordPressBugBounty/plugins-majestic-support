<?php
if(!defined('ABSPATH')) die('Restricted Access');
$mjtc_support_zywrap_nonce = wp_create_nonce('zywrap_ajax_action');
MJTC_message::MJTC_getMessage();
?>

<div id="msadmin-wrapper">
    <div id="msadmin-leftmenu">
        <?php MJTC_includer::MJTC_getClassesInclude('msadminsidemenu'); ?>
    </div>
    
    <div id="msadmin-data">
        <?php MJTC_includer::MJTC_getModel('majesticsupport')->getPageTitle('zywrap_playground'); ?>        
        
        <div id="msadmin-data-wrp" class="bg-n bs-n mjtc-ai-app">
            
            <div class="mjtc-app-grid mjtc-app-grid-playground">
                
                <!-- ==========================================
                     LEFT: THE LABORATORY 
                     ========================================== -->
                <div class="mjtc-stream-column">
                    
                    <!-- Core Configuration -->
                    <div class="mjtc-pg-card">
                        <h2 class="mjtc-pg-card-title">
                            <span class="dashicons dashicons-admin-generic" style="color: #3b82f6;"></span> 
                            <?php echo esc_html__('Core Targeting', 'majestic-support'); ?>
                        </h2>
                        
                        <div class="mjtc-grid-3">
                            <div class="mjtc-form-group">
                                <label class="mjtc-form-label"><?php echo esc_html__('1. Category', 'majestic-support'); ?></label>
                                <select id="pg_category" class="mjtc-form-select inputbox mjtc-form-select-field"><option value=""><?php echo esc_html__('Loading...', 'majestic-support'); ?></option></select>
                            </div>
                            <div class="mjtc-form-group">
                                <label class="mjtc-form-label"><?php echo esc_html__('2. AI Solution', 'majestic-support'); ?></label>
                                <select id="pg_usecase" class="mjtc-form-select inputbox mjtc-form-select-field" disabled><option value=""><?php echo esc_html__('Select Category First', 'majestic-support'); ?></option></select>
                            </div>
                            <div class="mjtc-form-group">
                                <label class="mjtc-form-label"><?php echo esc_html__('3. Configuration Style', 'majestic-support'); ?></label>
                                <select id="pg_wrapper" class="mjtc-form-select inputbox mjtc-form-select-field" disabled><option value=""><?php echo esc_html__('Select Solution First', 'majestic-support'); ?></option></select>
                            </div>
                        </div>
                    </div>

                    <!-- Context & Schema Generator -->
                    <div class="mjtc-pg-card">
                        <h2 class="mjtc-pg-card-title">
                            <span class="dashicons dashicons-text-page" style="color: #10b981;"></span> 
                            <?php echo esc_html__('Dynamic Context Payload', 'majestic-support'); ?>
                        </h2>
                        
                        <!-- JS Will inject the schema fields here automatically mapped to the new CSS -->
                        <div id="pg_dynamic_schema"></div>
                        
                        <div class="mjtc-form-group" style="margin-top: 24px;">
                            <label id="pg_prompt_label" class="mjtc-form-label"><?php echo esc_html__('Free-form Instructions / Final Prompt', 'majestic-support'); ?></label>
                            <textarea id="pg_prompt" class="mjtc-form-textarea" placeholder="<?php echo esc_attr__('Type explicit instructions or inject raw testing data here...', 'majestic-support'); ?>"></textarea>
                        </div>
                    </div>

                    <!-- Advanced Parameters (The Overrides) -->
                    <div class="mjtc-pg-card">
                        <h2 class="mjtc-pg-card-title">
                            <span class="dashicons dashicons-controls-forward" style="color: #8b5cf6;"></span> 
                            <?php echo esc_html__('Advanced Engine Overrides', 'majestic-support'); ?>
                        </h2>
                        
                        <div class="mjtc-grid-2 mjtc-zywrap-pg-overrides">
                            <div class="mjtc-form-group"><label class="mjtc-form-label"><?php echo esc_html__('Tone', 'majestic-support'); ?></label><select id="toneCode" class="mjtc-form-select inputbox mjtc-form-select-field"><option value=""><?php echo esc_html__('Default', 'majestic-support'); ?></option></select></div>
                            <div class="mjtc-form-group"><label class="mjtc-form-label"><?php echo esc_html__('Style', 'majestic-support'); ?></label><select id="styleCode" class="mjtc-form-select inputbox mjtc-form-select-field"><option value=""><?php echo esc_html__('Default', 'majestic-support'); ?></option></select></div>
                            <div class="mjtc-form-group"><label class="mjtc-form-label"><?php echo esc_html__('Formatting', 'majestic-support'); ?></label><select id="formatCode" class="mjtc-form-select inputbox mjtc-form-select-field"><option value=""><?php echo esc_html__('Default', 'majestic-support'); ?></option></select></div>
                            <div class="mjtc-form-group"><label class="mjtc-form-label"><?php echo esc_html__('Complexity', 'majestic-support'); ?></label><select id="complexityCode" class="mjtc-form-select inputbox mjtc-form-select-field"><option value=""><?php echo esc_html__('Default', 'majestic-support'); ?></option></select></div>
                            <div class="mjtc-form-group"><label class="mjtc-form-label"><?php echo esc_html__('Length', 'majestic-support'); ?></label><select id="lengthCode" class="mjtc-form-select inputbox mjtc-form-select-field"><option value=""><?php echo esc_html__('Default', 'majestic-support'); ?></option></select></div>
                            <div class="mjtc-form-group"><label class="mjtc-form-label"><?php echo esc_html__('Audience', 'majestic-support'); ?></label><select id="audienceCode" class="mjtc-form-select inputbox mjtc-form-select-field"><option value=""><?php echo esc_html__('Default', 'majestic-support'); ?></option></select></div>
                            <div class="mjtc-form-group"><label class="mjtc-form-label"><?php echo esc_html__('Goal', 'majestic-support'); ?></label><select id="responseGoalCode" class="mjtc-form-select inputbox mjtc-form-select-field"><option value=""><?php echo esc_html__('Default', 'majestic-support'); ?></option></select></div>
                            <div class="mjtc-form-group"><label class="mjtc-form-label"><?php echo esc_html__('Output Type', 'majestic-support'); ?></label><select id="outputCode" class="mjtc-form-select inputbox mjtc-form-select-field"><option value=""><?php echo esc_html__('Default', 'majestic-support'); ?></option></select></div>
                        </div>
                    </div>
                </div>

                <!-- ==========================================
                     RIGHT: THE ENGINE TERMINAL
                     ========================================== -->
                <div class="mjtc-engine-panel mjtc-engine-panel-playground">
                    
                    <div class="mjtc-engine-section-title"><?php echo esc_html__('Execution Parameters', 'majestic-support'); ?></div>
                    <div class="mjtc-engine-controls">
                        <div>
                            <label style="font-size: 0.8rem; color: #a1a1aa; margin-bottom: 4px; display: block;"><?php echo esc_html__('AI Model', 'majestic-support'); ?></label>
                            <select id="pg_model" class="mjtc-engine-select inputbox mjtc-form-select-field"><option value=""><?php echo esc_html__('Loading...', 'majestic-support'); ?></option></select>
                        </div>
                        <div>
                            <label style="font-size: 0.8rem; color: #a1a1aa; margin-bottom: 4px; display: block;"><?php echo esc_html__('Language', 'majestic-support'); ?></label>
                            <select id="pg_language" class="mjtc-engine-select inputbox mjtc-form-select-field"><option value=""><?php echo esc_html__('Loading...', 'majestic-support'); ?></option></select>
                        </div>
                    </div>

                    <button id="pg_run_btn" class="mjtc-btn-run">
                        <span class="dashicons dashicons-controls-play"></span> <?php echo esc_html__('Execute Request', 'majestic-support'); ?>
                    </button>

                    <div class="mjtc-terminal-wrapper">
                        <div class="mjtc-terminal-header">
                            <div class="mjtc-terminal-dots">
                                <span class="red"></span><span class="yellow"></span><span class="green"></span>
                            </div>
                            <?php echo esc_html__('bash: zywrap --output', 'majestic-support'); ?>
                        </div>
                        <pre id="pg_output" class="mjtc-terminal-output"><?php echo esc_html__('Awaiting execution parameters...', 'majestic-support'); ?></pre>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>

<?php
// Exact JavaScript logic preserved without changes
$majesticsupport_js = "
    jQuery(document).ready(function(\$) {
        var ajaxurl = '" . admin_url('admin-ajax.php') . "';
        
        // Abstract WP AJAX
        function fetchAPI(task, params = {}) {
            return new Promise((resolve, reject) => {
                var payload = Object.assign({ 
                    action: 'mjsupport_ajax', 
                    mjsmod: 'zywrap', 
                    task: task, 
                    _wpnonce: '" . esc_js($mjtc_support_zywrap_nonce) . "' 
                }, params);
                \$.post(ajaxurl, payload, function(response) {
                    resolve(typeof response === 'object' ? response : JSON.parse(response));
                }).fail(reject);
            });
        }

        function populateSelect(el, data, placeholder = 'Select') {
            var html = '<option value=\"\">' + placeholder + '</option>';
            if(data && data.length) {
                data.forEach(item => { html += '<option value=\"' + item.code + '\">' + item.name + '</option>'; });
            }
            el.html(html).prop('disabled', false);
        }

        async function init() {
            try {
                const [categories, models, langs, templates] = await Promise.all([
                    fetchAPI('pgGetCategories'), fetchAPI('pgGetModels'), fetchAPI('pgGetLanguages'), fetchAPI('pgGetBlockTemplates')
                ]);
                
                populateSelect(\$('#pg_category'), categories);
                populateSelect(\$('#pg_model'), models, '" . esc_js(__("Default Model (Auto)", "majestic-support")) . "');
                
                var globalDefaultModel = '" . esc_js(get_option("mjtc_zywrap_default_model", "")) . "';
                if (globalDefaultModel) \$('#pg_model').val(globalDefaultModel);

                populateSelect(\$('#pg_language'), langs, '" . esc_js(__("English (Default)", "majestic-support")) . "');
                
                var globalDefaultLang = '" . esc_js(get_option("mjtc_zywrap_default_lang", "English")) . "';
                if (globalDefaultLang && globalDefaultLang !== 'English') \$('#pg_language').val(globalDefaultLang);

                const overrideMap = { tones: 'toneCode', styles: 'styleCode', formattings: 'formatCode', complexities: 'complexityCode', lengths: 'lengthCode', audienceLevels: 'audienceCode', responseGoals: 'responseGoalCode', outputTypes: 'outputCode' };
                for (const [type, elId] of Object.entries(overrideMap)) {
                    if (templates[type]) populateSelect(\$('#' + elId), templates[type], '" . esc_js(__("Default", "majestic-support")) . "');
                }
            } catch (e) { console.error('Playground Init Failed', e); }
        }

        // Event Listeners
        \$('#pg_category').on('change', async function() {
            var cat = \$(this).val();
            if (!cat) {
                \$('#pg_usecase').html('<option value=\"\">" . esc_js(__("Select Category First", "majestic-support")) . "</option>').prop('disabled', true);
                \$('#pg_wrapper').html('<option value=\"\">" . esc_js(__("Select Solution First", "majestic-support")) . "</option>').prop('disabled', true);
                \$('#pg_dynamic_schema').empty();
                return;
            }
            \$('#pg_usecase').html('<option value=\"\">" . esc_js(__("Loading...", "majestic-support")) . "</option>');
            const useCases = await fetchAPI('pgGetUseCases', {category: cat});
            populateSelect(\$('#pg_usecase'), useCases, '" . esc_js(__("Select a Solution", "majestic-support")) . "');
            \$('#pg_wrapper').html('<option value=\"\">" . esc_js(__("Select Solution First", "majestic-support")) . "</option>').prop('disabled', true);
            \$('#pg_dynamic_schema').empty();
        });

        \$('#pg_usecase').on('change', async function() {
            var uc = \$(this).val();
            if (!uc) {
                \$('#pg_wrapper').html('<option value=\"\">" . esc_js(__("Select Solution First", "majestic-support")) . "</option>').prop('disabled', true);
                \$('#pg_dynamic_schema').empty();
                return;
            }
            \$('#pg_wrapper').html('<option value=\"\">" . esc_js(__("Loading...", "majestic-support")) . "</option>');
            let wrappers = await fetchAPI('pgGetWrappers', {usecase: uc});
            var html = '<option value=\"\">" . esc_js(__("Select a Style", "majestic-support")) . "</option>';
            let autoSelectCode = null;
            if(wrappers && wrappers.length) {
                wrappers.forEach((w, index) => {
                    const parts = w.name.split('—');
                    const displayName = (w.base == 1 || w.base === '1') ? '✨ " . esc_js(__("Base Template", "majestic-support")) . " - ' + parts[0].trim() : '↳ " . esc_js(__("Variation", "majestic-support")) . ": ' + (parts.length > 1 ? parts[1].trim() : w.name);
                    html += '<option value=\"' + w.code + '\">' + displayName + '</option>';
                    if (w.base == 1 || w.base === '1') autoSelectCode = w.code;
                    else if (index === 0 && !autoSelectCode) autoSelectCode = w.code;
                });
            }
            \$('#pg_wrapper').html(html).prop('disabled', false);
            if (autoSelectCode) \$('#pg_wrapper').val(autoSelectCode).trigger('change');
        });

        \$('#pg_wrapper').on('change', async function() {
            \$('#pg_dynamic_schema').empty();
            \$('#pg_prompt_label').text('" . esc_js(__("Prompt / Additional Context", "majestic-support")) . "');
            if (!\$(this).val()) return;
            const schema = await fetchAPI('pgGetSchema', {wrapper: \$(this).val()});
            if (!schema || (!schema.req && !schema.opt)) return;
            let html = '';
            \$('#pg_prompt_label').text('" . esc_js(__("Additional Free-form Instructions", "majestic-support")) . "');
            const buildSection = (title, data) => {
                if (!data || Object.keys(data).length === 0) return '';
                let sectionHtml = '<div class=\"mjtc-zywrap-pg-schema-section\"><h3 class=\"mjtc-zywrap-pg-schema-title\">' + title + '</h3><div class=\"mjtc-zywrap-pg-grid-2\">';
                for (const [key, def] of Object.entries(data)) {
                    const isPlaceholder = def.p !== undefined ? def.p : false;
                    const defaultVal = def.d !== undefined ? def.d : '';
                    const placeholderAttr = isPlaceholder ? 'placeholder=\"'+defaultVal+'\"' : '';
                    const valueAttr = (!isPlaceholder && defaultVal) ? 'value=\"'+defaultVal+'\"' : '';
                    const label = key.replace(/([A-Z])/g, ' \$1').replace(/^./, str => str.toUpperCase());
                    sectionHtml += '<div><label class=\"mjtc-zywrap-pg-label\">' + label + '</label>' +
                        '<input type=\"text\" class=\"mjtc-zywrap-pg-input pg-schema-input\" data-key=\"' + key + '\" ' + placeholderAttr + ' ' + valueAttr + '></div>';
                }
                return sectionHtml + '</div></div>';
            };
            html += buildSection('" . esc_js(__("Core Inputs", "majestic-support")) . "', schema.req);
            html += buildSection('" . esc_js(__("Additional Context", "majestic-support")) . "', schema.opt);
            \$('#pg_dynamic_schema').html(html);
        });

        \$('#pg_run_btn').on('click', async function() {
            var btn = \$(this);
            if (!\$('#pg_wrapper').val()) return alert('" . esc_js(__("Please select a wrapper.", "majestic-support")) . "');
            \$('#pg_output').text('" . esc_js(__("Executing...", "majestic-support")) . "');
            btn.prop('disabled', true).html('<span class=\"spinner is-active\"></span> " . esc_js(__("Generating...", "majestic-support")) . "');
            let finalPrompt = \$('#pg_prompt').val().trim();
            const variables = {};
            let structuredTextParts = [];
            \$('.pg-schema-input').each(function() {
                const val = \$(this).val().trim();
                if (val !== '') {
                    const key = \$(this).data('key');
                    variables[key] = val;
                    structuredTextParts.push(key + ': ' + val);
                }
            });
            const structuredText = structuredTextParts.join('\\n');
            if (finalPrompt && structuredText) finalPrompt = finalPrompt + '\\n\\n' + structuredText;
            else if (structuredText) finalPrompt = structuredText;
            const overrides = {};
            \$('.mjtc-zywrap-pg-overrides select').each(function() {
                if (\$(this).val()) overrides[\$(this).attr('id')] = \$(this).val();
            });
            try {
                const response = await fetchAPI('pgExecute', {
                    model: \$('#pg_model').val(),
                    wrapperCode: \$('#pg_wrapper').val(),
                    language: \$('#pg_language').val(),
                    prompt: finalPrompt,
                    variables: JSON.stringify(variables),
                    overrides: JSON.stringify(overrides)
                });
                if (response.success) {
                    \$('#pg_output').text(response.data.output || JSON.stringify(response.data, null, 2));
                } else {
                    \$('#pg_output').text('" . esc_js(__("Error:", "majestic-support")) . " ' + response.data.message);
                }
            } catch (error) {
                \$('#pg_output').text('" . esc_js(__("System Error. Check Console.", "majestic-support")) . "');
            } finally {
                btn.prop('disabled', false).html('<span class=\"dashicons dashicons-controls-play\"></span> " . esc_js(__("Execute Request", "majestic-support")) . "');
            }
        });

        init();
    });
";

wp_add_inline_script('majestic-support-cmain-js',$majesticsupport_js);
?>
