<?php 
if (!defined('ABSPATH')) die('Restricted Access'); 

// 1. NONCE & MODEL LOADING
$mjtc_support_zywrap_nonce = wp_create_nonce('zywrap_ajax_action');
$mjtc_support_zywrap_model = MJTC_includer::MJTC_getModel('zywrap');

// 2. ENQUEUE SCRIPTS
if (file_exists(MJTC_PLUGIN_PATH . 'includes/js/marked.min.js')) {
    wp_enqueue_script('zywrap-marked-js', MJTC_PLUGIN_URL . 'includes/js/marked.min.js', array('jquery'), majesticsupport::$_config['productversion'], true);
}

$mjtc_support_global_default_model = get_option('mjtc_zywrap_default_model', '');
$mjtc_support_zywrap_ajax_url = admin_url('admin-ajax.php');
$mjtc_support_current_ticket_id = 0;
if (isset(majesticsupport::$_data[0]->id)) {
    $mjtc_support_current_ticket_id = absint(majesticsupport::$_data[0]->id);
}
$raw_use_cases = $mjtc_support_zywrap_model->getSupportUseCases();
$dynamic_tones = $mjtc_support_zywrap_model->getDynamicTones();
$dynamic_models = $mjtc_support_zywrap_model->getDynamicModels();

$use_cases_map = array();
if (!empty($raw_use_cases)) {
    foreach ($raw_use_cases as $uc) {
        $use_cases_map[$uc->code] = $uc;
    }
}

// Intent Groupings
$intents = array(
    'compose'  => array('ticket_thread_reply_composer', 'internal_support_note_generator', 'investigation_status_update', 'troubleshooting_first_response', 'wordpress_plugin_conflict_first_response', 'white_screen_fatal_error_initial_response', 'license_key_activation_help'),
    'ask_info' => array('debug_log_request_reply', 'reproduction_steps_request', 'technical_requirement_request', 'javascript_console_error_request', 'safe_temporary_admin_access_request', 'staging_site_request_before_investigation'),
    'escalate' => array('customer_retest_request', 'please_test_this_build_reply', 'technical_escalation_handoff', 'escalated_bug_response', 'waiting_on_engineering_update')
);
?>

<div id="mjtc-zywrap-backdrop" class="mjtc-zywrap-backdrop" style="display:none;"></div>

<div id="mjtc-zywrap-modal" class="mjtc-zywrap-modal" style="display:none;">
    <div class="mjtc-zywrap-modal-inner">
        <div class="mjtc-zywrap-modal-header">
            <div>
                <h3 class="mjtc-zywrap-modal-title">
                    <span class="dashicons dashicons-superhero-alt" aria-hidden="true"></span> 
                    <?php echo esc_html(__('Zywrap Co-Pilot', 'majestic-support')); ?>
                </h3>
                <div class="mjtc-zywrap-modal-subtitle"><?php echo esc_html(__('Call AI by Code. Zero Prompt Engineering.', 'majestic-support')); ?></div>
            </div>
            <a href="#" id="mjtc-zywrap-close" class="mjtc-zywrap-modal-close">
                <span class="dashicons dashicons-no-alt" aria-hidden="true"></span>
            </a>
        </div>

        <div class="mjtc-zywrap-modal-body">
            <div class="mjtc-zywrap-modal-sidebar">
                <div class="mjtc-zywrap-intent-header">
                    <label class="mjtc-zywrap-intent-label"><?php echo esc_html(__('Support Intent', 'majestic-support')); ?></label>
                    <div class="mjtc-zywrap-intent-tabs">
                        <button type="button" class="zywrap-intent-tab active" data-target="compose"><?php echo esc_html(__('Compose', 'majestic-support')); ?></button>
                        <button type="button" class="zywrap-intent-tab" data-target="ask_info"><?php echo esc_html(__('Ask Info', 'majestic-support')); ?></button>
                        <button type="button" class="zywrap-intent-tab" data-target="escalate"><?php echo esc_html(__('Escalate', 'majestic-support')); ?></button>
                        <button type="button" class="zywrap-intent-tab" data-target="more"><?php echo esc_html(__('Library', 'majestic-support')); ?></button>
                    </div>
                </div>

                <div class="mjtc-zywrap-action-list">
                    <div id="zywrap-intent-compose" class="zywrap-intent-panel">
                        <?php if (isset($use_cases_map['ticket_thread_reply_composer'])): ?>
                            <div class="zywrap-action-card featured selected mjtc-zywrap-card-featured" data-code="ticket_thread_reply_composer" data-title="<?php echo esc_attr(__('Ticket Thread Reply Composer', 'majestic-support')); ?>">
                                <div class="mjtc-zywrap-badge-default"><?php echo esc_html(__('Default', 'majestic-support')); ?></div>
                                <h4 class="mjtc-zywrap-action-title"><span class="dashicons dashicons-edit" aria-hidden="true"></span> <?php echo esc_html(__('Ticket Thread Reply Composer', 'majestic-support')); ?></h4>
                                <p class="mjtc-zywrap-action-desc"><?php echo esc_html(__('Reads the thread and drafts the best response.', 'majestic-support')); ?></p>
                            </div>
                        <?php endif; ?>

                        <div class="mjtc-zywrap-flex-column-gap">
                            <?php foreach($intents['compose'] as $code): if($code === 'ticket_thread_reply_composer' || !isset($use_cases_map[$code])) continue; ?>
                                <div class="zywrap-action-card list-item mjtc-zywrap-card-list" data-code="<?php echo esc_attr($code); ?>" data-title="<?php echo esc_attr($use_cases_map[$code]->name); ?>">
                                    <h4 class="mjtc-zywrap-action-title-small"><?php echo esc_html($use_cases_map[$code]->name); ?></h4>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div id="zywrap-intent-ask_info" class="zywrap-intent-panel mjtc-zywrap-hide mjtc-zywrap-flex-column-gap" style="display:none;">
                        <?php foreach($intents['ask_info'] as $code): if(!isset($use_cases_map[$code])) continue; ?>
                            <div class="zywrap-action-card list-item mjtc-zywrap-card-list" data-code="<?php echo esc_attr($code); ?>" data-title="<?php echo esc_attr($use_cases_map[$code]->name); ?>">
                                <h4 class="mjtc-zywrap-action-title-small"><?php echo esc_html($use_cases_map[$code]->name); ?></h4>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div id="zywrap-intent-escalate" class="zywrap-intent-panel mjtc-zywrap-hide mjtc-zywrap-flex-column-gap" style="display:none;">
                        <?php foreach($intents['escalate'] as $code): if(!isset($use_cases_map[$code])) continue; ?>
                            <div class="zywrap-action-card list-item mjtc-zywrap-card-list" data-code="<?php echo esc_attr($code); ?>" data-title="<?php echo esc_attr($use_cases_map[$code]->name); ?>">
                                <h4 class="mjtc-zywrap-action-title-small"><?php echo esc_html($use_cases_map[$code]->name); ?></h4>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div id="zywrap-intent-more" class="zywrap-intent-panel mjtc-zywrap-hide" style="display:none;">
                        <input type="text" id="zywrap-usecase-search" class="mjtc-zywrap-search-input" placeholder="<?php echo esc_attr(__('Search full library...', 'majestic-support')); ?>">
                        <div class="mjtc-zywrap-flex-column-gap-small">
                            <?php foreach($use_cases_map as $code => $uc): ?>
                                <div class="zywrap-action-card list-item mjtc-zywrap-card-compact" data-code="<?php echo esc_attr($code); ?>" data-title="<?php echo esc_attr($uc->name); ?>">
                                    <h4 class="mjtc-zywrap-action-title-small"><?php echo esc_html($uc->name); ?></h4>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <input type="hidden" id="zywrap-active-wrapper" value="">
            </div>

            <div class="mjtc-zywrap-modal-workspace">
                <div class="mjtc-zywrap-workspace-header">
                    <div class="mjtc-zywrap-flex-align">
                        <span class="dashicons dashicons-arrow-right-alt2" aria-hidden="true"></span>
                        <h2 id="workspace-action-title" class="mjtc-zywrap-workspace-h2"><?php echo esc_html(__('Ticket Thread Reply Composer', 'majestic-support')); ?></h2>
                    </div>
                    <div class="mjtc-zywrap-flex-gap-10">
                        <select id="zywrap-model-select" class="mjtc-zywrap-modal-select">
                            <option value=""><?php echo esc_html(__('Model: Default (Auto)', 'majestic-support')); ?></option>
                            <?php if (!empty($dynamic_models)) : foreach ($dynamic_models as $model) : ?>
                                <option value="<?php echo esc_attr($model->code); ?>" <?php selected($mjtc_support_global_default_model, $model->code); ?>><?php echo esc_html($model->name); ?></option>
                            <?php endforeach; endif; ?>
                        </select>
                        <select id="zywrap-tone-select" class="mjtc-zywrap-modal-select">
                            <option value=""><?php echo esc_html(__('Tone: Default', 'majestic-support')); ?></option>
                            <?php if (!empty($dynamic_tones)) : foreach ($dynamic_tones as $tone) : ?>
                                <option value="<?php echo esc_attr($tone->code); ?>"><?php echo esc_html__('Tone', 'majestic-support'); ?>: <?php echo esc_html($tone->name); ?></option>
                            <?php endforeach; endif; ?>
                        </select>
                    </div>
                </div>

                <div class="mjtc-zywrap-workspace-content">
                    <div id="zywrap-dynamic-playground">
                        <div class="mjtc-zywrap-empty-prompt" style="padding: 8px 12px;flex-direction: row;">
                            <span class="dashicons dashicons-arrow-left-alt" aria-hidden="true"></span>
                            <?php echo esc_html(__('Select a Support Intent from the left menu to load variables.', 'majestic-support')); ?>
                        </div>
                    </div>

                    <div id="zywrap-extra-instructions-container" class="mjtc-zywrap-hide mjtc-zywrap-instructions-card">
                        <label class="mjtc-zywrap-label-bold">
                            <?php echo esc_html(__('Key Points to Include', 'majestic-support')); ?> 
                            <span class="mjtc-zywrap-label-opt"><?php echo esc_html(__('(Optional)', 'majestic-support')); ?></span>
                        </label>
                        <p class="mjtc-zywrap-p-hint"><?php echo esc_html(__('Add any specific facts, business decisions, or links the AI should mention. These points are sent to the AI as priority instructions for this draft only.', 'majestic-support')); ?></p>
                        <textarea id="zywrap-extra-instructions" class="mjtc-zywrap-textarea-small" rows="2" placeholder="<?php echo esc_attr(__('e.g., Approve the refund, offer a 20% discount, or let them know this will be fixed in v4.0...', 'majestic-support')); ?>"></textarea>
                    </div>

                    <div class="mjtc-zywrap-draft-container">
                        <label class="mjtc-zywrap-label-bold mjtc-zywrap-flex-between">
                            <?php echo esc_html(__('AI Generated Draft', 'majestic-support')); ?>
                            <span id="zywrap-status-text" class="mjtc-zywrap-text-status"></span>
                        </label>
                        <textarea id="zywrap-draft-area" class="mjtc-zywrap-textarea-draft" readonly placeholder="<?php echo esc_attr(__('AI generated response will appear here...', 'majestic-support')); ?>"></textarea>
                    </div>
                </div>

                <div class="mjtc-zywrap-modal-footer">
                    <span id="zywrap-spinner" class="spinner"></span>
                    <div class="mjtc-zywrap-flex-gap-12">
                        <button type="button" id="zywrap-generate-btn" class="button button-large mjtc-zywrap-btn-gen" disabled>
                            <span class="dashicons dashicons-update-alt" aria-hidden="true"></span> <?php echo esc_html(__('Generate Draft', 'majestic-support')); ?>
                        </button>
                        <button type="button" id="zywrap-insert-btn" class="button button-primary button-large mjtc-zywrap-btn-insert" disabled>
                            <span class="dashicons dashicons-insert" aria-hidden="true"></span> <?php echo esc_html(__('Insert into Editor', 'majestic-support')); ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$majesticsupport_js = "
// ==========================================
// ZYWRAP MASTER FORMATTER UTILITY (GLOBAL)
// ==========================================
window.ZywrapFormatter = {
    escapeHTML: function(text) {
        return String(text || '').replace(/[&<>\"']/g, function(ch) {
            return ch === '&' ? '&amp;' : (ch === '<' ? '&lt;' : (ch === '>' ? '&gt;' : (ch === '\"' ? '&quot;' : '&#039;')));
        });
    },

    cleanRawMarkdown: function(text) {
        if (!text) return '';
        return String(text)
            .replace(/\\r\\n/g, '\\n')
            .replace(/\\n{4,}/g, '\\n\\n')
            .replace(/([^\\s\\n])\\s+(#{1,6}\\s+[A-Z])/g, '\$1\\n\\n\$2')
            .replace(/([^\\s\\n])\\s+(-\\s+[A-Z0-9])/g, '\$1\\n\$2')
            .trim();
    },

    fallbackMarkdownToHtml: function(markdownText) {
        var text = this.cleanRawMarkdown(markdownText);
        if (!text) return '';

        var lines = text.split('\\n');
        var html = '';
        var inUl = false;
        var inOl = false;
        var paragraph = [];

        var flushParagraph = function() {
            if (!paragraph.length) return;
            html += '<p>' + paragraph.join('<br>') + '</p>';
            paragraph = [];
        };
        var closeLists = function() {
            if (inUl) { html += '</ul>'; inUl = false; }
            if (inOl) { html += '</ol>'; inOl = false; }
        };
        var inlineFormat = function(value) {
            value = ZywrapFormatter.escapeHTML(value);
            value = value.replace(/\\*\\*(.*?)\\*\\*/g, '<strong>\$1</strong>');
            value = value.replace(/\\*(.*?)\\*/g, '<em>\$1</em>');
            value = value.replace(/`([^`]+)`/g, '<code>\$1</code>');
            value = value.replace(/\\[([^\\]]+)\\]\\((https?:\\/\\/[^\\s)]+)\\)/g, '<a href=\"\$2\" target=\"_blank\" rel=\"noopener noreferrer\">\$1</a>');
            return value;
        };

        for (var i = 0; i < lines.length; i++) {
            var raw = lines[i];
            var line = raw.trim();

            if (!line) {
                flushParagraph();
                closeLists();
                continue;
            }

            var heading = line.match(/^(#{1,6})\\s+(.+)$/);
            if (heading) {
                flushParagraph();
                closeLists();
                var level = Math.min(4, Math.max(3, heading[1].length + 2));
                html += '<h' + level + '>' + inlineFormat(heading[2]) + '</h' + level + '>';
                continue;
            }

            var bullet = line.match(/^[-*]\\s+(.+)$/);
            if (bullet) {
                flushParagraph();
                if (inOl) { html += '</ol>'; inOl = false; }
                if (!inUl) { html += '<ul>'; inUl = true; }
                html += '<li>' + inlineFormat(bullet[1]) + '</li>';
                continue;
            }

            var numbered = line.match(/^\\d+[.)]\\s+(.+)$/);
            if (numbered) {
                flushParagraph();
                if (inUl) { html += '</ul>'; inUl = false; }
                if (!inOl) { html += '<ol>'; inOl = true; }
                html += '<li>' + inlineFormat(numbered[1]) + '</li>';
                continue;
            }

            paragraph.push(inlineFormat(line));
        }

        flushParagraph();
        closeLists();
        return html;
    },

    toHTML: function(markdownText) {
        var preppedText = this.cleanRawMarkdown(markdownText);
        if (!preppedText) return '';
        if (typeof marked !== 'undefined' && marked && typeof marked.parse === 'function') {
            return marked.parse(preppedText, { breaks: true, gfm: true });
        }
        return this.fallbackMarkdownToHtml(preppedText);
    },

    toPlainText: function(markdownText) {
        var html = this.toHTML(markdownText);
        html = html.replace(/<br\\s*[\\/ ]?>/gi, '\\n');
        html = html.replace(/<\\/p>/gi, '\\n\\n');
        html = html.replace(/<h[1-6][^>]*>(.*?)<\\/h[1-6]>/gi, '\\n\$1\\n\\n');
        html = html.replace(/<li[^>]*>(.*?)<\\/li>/gi, ' • \$1\\n');
        html = html.replace(/<\\/ul>/gi, '\\n');
        html = html.replace(/<\\/ol>/gi, '\\n');

        var temp = document.createElement('div');
        temp.innerHTML = html;
        var plainText = temp.innerText || temp.textContent;
        return plainText.trim().replace(/\\n{3,}/g, '\\n\\n');
    },

    formatJSON: function(jsonObj) {
        if (!jsonObj || typeof jsonObj !== 'object') return '';
        var html = '<div class=\"mjtc-zywrap-json-container\">';
        var hasData = false;

        for (var key in jsonObj) {
            var val = jsonObj[key];
            if (val === null || val === '' || (Array.isArray(val) && val.length === 0)) continue;
            
            if (typeof val === 'object' && !Array.isArray(val)) {
                var hasSubData = false;
                for (var sk in val) { if (val[sk] !== null && val[sk] !== '' && (!Array.isArray(val[sk]) || val[sk].length > 0)) hasSubData = true; }
                if (!hasSubData) continue;
            }

            hasData = true;
            var cleanKey = key.replace(/_/g, ' ').replace(/\\b\\w/g, function(l){ return l.toUpperCase(); });

            html += '<div class=\"mjtc-zywrap-json-card\">';
            html += '<div class=\"mjtc-zywrap-json-header\">' + cleanKey + '</div>';
            html += '<div class=\"mjtc-zywrap-json-body\">';

            if (Array.isArray(val)) {
                html += '<ul class=\"mjtc-zywrap-json-list\">';
                val.forEach(function(item) {
                    if (typeof item === 'object') {
                        for(var subK in item) {
                            var subVal = item[subK];
                            if(Array.isArray(subVal)) {
                                subVal.forEach(function(sv) { html += '<li>' + sv + '</li>'; });
                            } else if (subVal !== null && subVal !== '') {
                                html += '<li>' + subVal + '</li>';
                            }
                        }
                    } else {
                        html += '<li>' + item + '</li>';
                    }
                });
                html += '</ul>';
            } else if (typeof val === 'object') {
                html += '<div class=\"mjtc-zywrap-json-grid\">';
                for (var subKey in val) {
                    var subVal = val[subKey];
                    if (subVal === null || subVal === '' || (Array.isArray(subVal) && subVal.length===0)) continue;
                    var cleanSubKey = subKey.replace(/_/g, ' ').replace(/\\b\\w/g, function(l){ return l.toUpperCase(); });
                    var displayVal = Array.isArray(subVal) ? subVal.join(', ') : subVal;
                    html += '<div class=\"mjtc-zywrap-json-item\"><strong>' + cleanSubKey + ':</strong> ' + displayVal + '</div>';
                }
                html += '</div>';
            } else {
                html += val;
            }
            html += '</div></div>';
        }
        html += '</div>';
        if (!hasData) return '<div class=\"mjtc-zywrap-empty-json\">' + '" . esc_js(__("No specific entities found.", "majestic-support")) . "' + '</div>';
        return html;
    },

    smartInsert: function(editorId, markdownText) {
        var html = this.toHTML(markdownText);
        var plain = this.toPlainText(markdownText);
        var editor = (typeof tinyMCE !== 'undefined' && typeof tinyMCE.get === 'function') ? tinyMCE.get(editorId) : null;
        var wrap = jQuery('#wp-' + editorId + '-wrap');
        var isHtmlMode = wrap.length && wrap.hasClass('html-active');
        var textarea = jQuery(document.getElementById(editorId));

        if (editor && !isHtmlMode) {
            var currentHtml = '';
            try { currentHtml = editor.getContent() || ''; } catch(e) { currentHtml = ''; }
            var insertHtml = (currentHtml ? '<p></p>' : '') + html;
            try {
                editor.setContent(currentHtml + insertHtml);
            } catch(e1) {
                try { editor.execCommand('mceInsertContent', false, insertHtml); } catch(e2) {}
            }
            try { editor.save(); } catch(e3) {}
            if (textarea.length) {
                textarea.val(editor.getContent()).trigger('input').trigger('change');
            }
            return true;
        }

        if (textarea.length) {
            var currentContent = textarea.val() || '';
            textarea.val(currentContent + (currentContent ? String.fromCharCode(10,10) : '') + plain).trigger('input').trigger('change');
            return true;
        }

        return false;
    }
};

jQuery(document).ready(function(\$) {
    var phpTicketId = '" . esc_js((string) $mjtc_support_current_ticket_id) . "';
    var currentTicketId = (phpTicketId && phpTicketId !== '0') ? phpTicketId : (\$('#ticketid').first().val() || \$('input[name=\"majesticsupportid\"]').first().val() || new URLSearchParams(window.location.search).get('majesticsupportid') || new URLSearchParams(window.location.search).get('ticketid') || '');
    var zywrapAjaxUrl = '" . esc_js(esc_url_raw($mjtc_support_zywrap_ajax_url)) . "';
    if (!zywrapAjaxUrl || zywrapAjaxUrl.indexOf('admin-ajax.php') === -1) {
        zywrapAjaxUrl = (typeof ajaxurl !== 'undefined' && ajaxurl && ajaxurl.indexOf('admin-ajax.php') !== -1) ? ajaxurl : '/wp-admin/admin-ajax.php';
    }

    var zywrapDebugBuild = 'zywrap-agent-first-20260627-08';
    var zywrapNonce = '" . esc_js($mjtc_support_zywrap_nonce) . "';
    var zywrapDebugEnabled = false;

    function zywrapDebugSafeText(value) {
        if (value === undefined || value === null) return '';
        if (typeof value === 'object') {
            try { return JSON.stringify(value, null, 2); } catch(e) { return String(value); }
        }
        return String(value);
    }

    function zywrapEnsureDebugPanel() {
        if (!zywrapDebugEnabled) return null;
        var panel = $('#mjtc-zywrap-debug-panel');
        if (panel.length) return panel;
        panel = $('<div id=\"mjtc-zywrap-debug-panel\" style=\"margin:10px 0 14px;padding:10px 12px;border:1px solid #f59e0b;background:#fffbeb;color:#78350f;border-radius:8px;font-size:12px;line-height:1.45;white-space:normal;\"></div>');
        panel.html('<strong>Zywrap AJAX Debug</strong> <code>' + zywrapDebugBuild + '</code><div id=\"mjtc-zywrap-debug-lines\" style=\"margin-top:6px;\"></div><pre id=\"mjtc-zywrap-debug-pre\" style=\"display:none;max-height:260px;overflow:auto;background:#111827;color:#e5e7eb;padding:10px;border-radius:6px;white-space:pre-wrap;margin:8px 0 0;\"></pre>');
        var anchor = $('#zywrap-dynamic-playground');
        if (anchor.length) anchor.before(panel);
        else $('#mjtc-zywrap-modal .mjtc-zywrap-modal-workspace').prepend(panel);
        return panel;
    }

    function zywrapDebugSet(lines, details) {
        if (!zywrapDebugEnabled) return;
        zywrapEnsureDebugPanel();
        var html = '';
        $.each(lines || [], function(i, line) { html += '<div>' + line + '</div>'; });
        $('#mjtc-zywrap-debug-lines').html(html);
        if (details !== undefined) {
            $('#mjtc-zywrap-debug-pre').show().text(zywrapDebugSafeText(details));
        }
        if (window.console && console.log) { console.log('[Zywrap debug]', lines, details || ''); }
    }

    function zywrapPreviewResponseText(text) {
        text = zywrapDebugSafeText(text);
        return text.substring(0, 1500);
    }

    function zywrapRunDebugPing(reason) {
        zywrapDebugSet([
            'Debug build loaded: ' + zywrapDebugBuild,
            'Reason: ' + reason,
            'AJAX URL: ' + zywrapAjaxUrl,
            'Current ticket ID: ' + (currentTicketId || '(empty)'),
            'Sending debug ping to WordPress...'
        ]);

        $.ajax({
            url: zywrapAjaxUrl,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'mjsupport_ajax',
                task: 'debugPing',
                mjsmod: 'zywrap',
                ticketid: currentTicketId,
                _wpnonce: zywrapNonce
            }
        }).done(function(response) {
            zywrapDebugSet([
                'Debug ping: JSON OK',
                'AJAX URL: ' + zywrapAjaxUrl,
                'Handler reached: ' + (response && response.data && response.data.handler_reached ? 'YES' : 'NO'),
                'Logged in: ' + (response && response.data ? response.data.is_user_logged_in : 'unknown'),
                'Nonce valid: ' + (response && response.data ? response.data.nonce_valid_for_zywrap_ajax_action : 'unknown')
            ], response);
        }).fail(function(xhr) {
            var contentType = xhr && xhr.getResponseHeader ? xhr.getResponseHeader('content-type') : '';
            zywrapDebugSet([
                'Debug ping failed. This means the direct Zywrap endpoint is not reaching the debug handler.',
                'HTTP status: ' + (xhr ? xhr.status : 'unknown'),
                'Content-Type: ' + (contentType || 'unknown'),
                'AJAX URL: ' + zywrapAjaxUrl
            ], zywrapPreviewResponseText(xhr && xhr.responseText ? xhr.responseText : ''));
        });
    }

    $(document).ajaxSend(function(event, jqxhr, settings) {
        if (!settings || !settings.url || (settings.url.indexOf('admin-ajax.php') === -1 && settings.url.indexOf('mjtc_zywrap_direct_ajax=1') === -1 && settings.url.indexOf('admin-post.php') === -1)) return;
        var dataText = typeof settings.data === 'string' ? settings.data : $.param(settings.data || {});
        if (dataText.indexOf('mjtc_zywrap') === -1 && dataText.indexOf('ajaxGetWrappers') === -1 && dataText.indexOf('generateReply') === -1) return;
        zywrapDebugSet([
            'Sending Zywrap AJAX request...',
            'URL: ' + settings.url,
            'Method: ' + (settings.type || settings.method || 'GET'),
            'Payload uses legacy action=mjsupport_ajax: ' + (dataText.indexOf('action=mjsupport_ajax') !== -1 ? 'YES' : 'NO')
        ], decodeURIComponent(dataText.replace(/\+/g, ' ')));
    });

    $(document).ajaxComplete(function(event, xhr, settings) {
        if (!settings || !settings.url || (settings.url.indexOf('admin-ajax.php') === -1 && settings.url.indexOf('mjtc_zywrap_direct_ajax=1') === -1 && settings.url.indexOf('admin-post.php') === -1)) return;
        var dataText = typeof settings.data === 'string' ? settings.data : $.param(settings.data || {});
        if (dataText.indexOf('mjtc_zywrap') === -1 && dataText.indexOf('ajaxGetWrappers') === -1 && dataText.indexOf('generateReply') === -1) return;
        var contentType = xhr && xhr.getResponseHeader ? xhr.getResponseHeader('content-type') : '';
        zywrapDebugSet([
            'Zywrap AJAX completed.',
            'HTTP status: ' + (xhr ? xhr.status : 'unknown'),
            'Content-Type: ' + (contentType || 'unknown'),
            'Response starts with: ' + zywrapPreviewResponseText(xhr && xhr.responseText ? xhr.responseText : '').substring(0, 120).replace(/\\n/g, ' ')
        ], { payload: decodeURIComponent(dataText.replace(/\+/g, ' ')), responsePreview: zywrapPreviewResponseText(xhr && xhr.responseText ? xhr.responseText : '') });
    });

    var zy_i18n = {
        unconfigured_alert: '" . esc_js(__('Zywrap AI Co-Pilot is not configured! Please navigate to Zywrap AI Settings to connect your API key and unlock AI Auto-Replies.', 'majestic-support')) . "',
        loading_context: '" . esc_js(__('Loading context securely from database...', 'majestic-support')) . "',
        error_prefix: '" . esc_js(__('Error', 'majestic-support')) . ":',
        no_variations: '" . esc_js(__('No wrapper variations found. Please sync data.', 'majestic-support')) . "',
        optional_label: '" . esc_js(__('(Optional)', 'majestic-support')) . "',
        ready_generate: '" . esc_js(__('No extra context variables required. Ready to generate.', 'majestic-support')) . "',
        drafting: '" . esc_js(__('Drafting response...', 'majestic-support')) . "',
        sys_error: '" . esc_js(__('System Error parsing response. Check console.', 'majestic-support')) . "',
        ajax_failed: '" . esc_js(__('Request failed. Please check the browser console and WordPress error log.', 'majestic-support')) . "',
        unlock_title: '" . esc_js(__('Unlock AI Features', 'majestic-support')) . "',
        unlock_desc: '" . esc_js(__('Connect your Zywrap API key in settings to unlock 1-click summaries, translations, and data extraction.', 'majestic-support')) . "',
        configure_link: '" . esc_js(__('Configure Zywrap AI &rarr;', 'majestic-support')) . "',
        translate_to: '" . esc_js(__('Translate to', 'majestic-support')) . "',
        extract_details: '" . esc_js(__('Extract Details', 'majestic-support')) . "',
        ai_executing: '" . esc_js(__('AI is executing:', 'majestic-support')) . "',
        ai_insight: '" . esc_js(__('AI Insight:', 'majestic-support')) . "',
        current_draft_label: '" . esc_js(__('Current draft detected', 'majestic-support')) . "',
        current_draft_hint: '" . esc_js(__('Zywrap will use your current reply as the starting point and preserve the agent intent.', 'majestic-support')) . "',
        use_current_draft: '" . esc_js(__('Improve my current reply', 'majestic-support')) . "',
        advanced_options: '" . esc_js(__('Advanced options', 'majestic-support')) . "',
        advanced_hint: '" . esc_js(__('Optional schema fields are collapsed to keep Co-Pilot fast. Open them only when you need more control.', 'majestic-support')) . "',
        ticket_context_loaded: '" . esc_js(__('Ticket context loaded', 'majestic-support')) . "',
        ticket_context_hint: '" . esc_js(__('The ticket thread, latest customer message, and subject are already included. Open to review or edit before generating.', 'majestic-support')) . "',
        response_mode: '" . esc_js(__('Response mode', 'majestic-support')) . "',
        mode_new: '" . esc_js(__('Draft best reply', 'majestic-support')) . "',
        mode_improve: '" . esc_js(__('Improve current draft', 'majestic-support')) . "',
        mode_shorter: '" . esc_js(__('Make current draft shorter', 'majestic-support')) . "',
        mode_warmer: '" . esc_js(__('Make current draft warmer', 'majestic-support')) . "',
        mode_ask_info: '" . esc_js(__('Ask for missing information', 'majestic-support')) . "',
        current_draft_preview: '" . esc_js(__('Preview', 'majestic-support')) . "',
        key_points_model_hint: '" . esc_js(__('These points are sent to the AI as priority instructions for this draft only.', 'majestic-support')) . "',
        btn_generate_reply: '" . esc_js(__('Generate Reply', 'majestic-support')) . "',
        btn_improve_draft: '" . esc_js(__('Improve Draft', 'majestic-support')) . "',
        btn_shorter: '" . esc_js(__('Shorten Draft', 'majestic-support')) . "',
        btn_warmer: '" . esc_js(__('Warm Up Draft', 'majestic-support')) . "',
        btn_ask_info: '" . esc_js(__('Ask for Info', 'majestic-support')) . "'
    };


    function zywrapEscapeHtml(value) {
        return \$('<div>').text(value === undefined || value === null ? '' : String(value)).html();
    }

    function zywrapAttr(value) {
        return zywrapEscapeHtml(value).replace(/\"/g, '&quot;');
    }

    function zywrapNormalizeSchemaFieldValue(field) {
        if (!field || typeof field !== 'object') return '';
        if (field.defaultVal !== undefined && field.defaultVal !== null) {
            return Array.isArray(field.defaultVal) ? field.defaultVal.join('\\n') : String(field.defaultVal);
        }
        if (field.d !== undefined && field.d !== null) return String(field.d);
        return '';
    }

    function zywrapGetCurrentResponseDraft() {
        var candidates = ['mjsupport_message', 'mjsupport_replytext', 'mjtc-support-reply-textarea', 'jsticket_message', 'message', 'reply', 'ticketreply', 'ticketresponse', 'response', 'content'];
        var values = [];

        function pushValue(source, value, scoreBoost) {
            value = $.trim(value || '');
            if (!value) return;
            values.push({ source: source, value: value, score: value.length + (scoreBoost || 0) });
        }

        try { if (window.tinyMCE && window.tinyMCE.triggerSave) window.tinyMCE.triggerSave(); } catch(e) {}

        if (window.tinymce) {
            try {
                var active = window.tinymce.activeEditor;
                if (active && active.id && $.inArray(active.id, candidates) !== -1) {
                    var activeText = '';
                    try { activeText = active.getContent({ format: 'text' }); } catch(e) { activeText = active.getContent(); }
                    pushValue('active-editor:' + active.id, activeText, 5000);
                }
            } catch(e) {}

            if (typeof window.tinymce.get === 'function') {
                $.each(candidates, function(i, id) {
                    var ed = window.tinymce.get(id);
                    if (ed) {
                        var text = '';
                        try { text = ed.getContent({ format: 'text' }); } catch(e) { text = ed.getContent(); }
                        var wrapVisible = $('#wp-' + id + '-wrap').length ? $('#wp-' + id + '-wrap').is(':visible') : true;
                        pushValue('editor:' + id, text, wrapVisible ? 1000 : 0);
                    }
                });
            }

            try {
                var editors = window.tinymce.editors || [];
                $.each(editors, function(i, ed) {
                    if (!ed || !ed.id) return;
                    if (String(ed.id).indexOf('zywrap') !== -1) return;
                    var textarea = $('#' + ed.id);
                    if (textarea.closest('#mjtc-zywrap-modal').length) return;
                    var text = '';
                    try { text = ed.getContent({ format: 'text' }); } catch(e) { text = ed.getContent(); }
                    var likely = ($.inArray(ed.id, candidates) !== -1 || textarea.hasClass('wp-editor-area')) ? 1500 : 0;
                    pushValue('editor-any:' + ed.id, text, likely);
                });
            } catch(e) {}
        }

        var selectors = [];
        $.each(candidates, function(i, id) {
            selectors.push('textarea#' + id);
            selectors.push('textarea[name=\"' + id + '\"]');
        });
        selectors.push('textarea.wp-editor-area');
        selectors.push('textarea[name=\"reply\"]');
        selectors.push('textarea[name=\"message\"]');

        $(selectors.join(',')).each(function() {
            if ($(this).closest('#mjtc-zywrap-modal').length) return;
            var id = $(this).attr('id') || $(this).attr('name') || 'textarea';
            var wrapVisible = $('#wp-' + id + '-wrap').length ? $('#wp-' + id + '-wrap').is(':visible') : $(this).is(':visible');
            pushValue('textarea:' + id, $(this).val(), wrapVisible ? 800 : 0);
        });

        if (!values.length) return '';
        values.sort(function(a, b) { return b.score - a.score; });
        return values[0].value;
    }

    function zywrapGetCurrentDraftSummary(text) {
        text = \$.trim(text || '');
        if (!text) return '';
        return text.length > 180 ? text.substring(0, 180) + '...' : text;
    }



    function zywrapFieldLooksAutoContext(key, label, value) {
        var k = (key || '').toLowerCase();
        var l = (label || '').toLowerCase();
        var v = \$.trim(value || '');
        if (!v) return false;
        if (k.indexOf('ticketthread') !== -1 || k.indexOf('thread') !== -1) return true;
        if (k.indexOf('latestcustomer') !== -1 || k.indexOf('customermessage') !== -1 || k.indexOf('requestmessage') !== -1) return true;
        if (k.indexOf('source') !== -1 || k.indexOf('sourcetext') !== -1) return true;
        if (k.indexOf('subject') !== -1 || l.indexOf('subject') !== -1) return true;
        if (k.indexOf('issuesummary') !== -1 || k.indexOf('customersummary') !== -1 || l.indexOf('issue summary') !== -1) return true;
        if (k.indexOf('case') !== -1 && v.length > 0) return true;
        return false;
    }

    function zywrapBuildTextareaField(key, label, field, defaultVal, required, rows) {
        var placeholder = field && (field.d || field.defaultVal) ? (Array.isArray(field.defaultVal) ? field.defaultVal.join('\\n') : (field.d || field.defaultVal)) : '';
        var reqHtml = required ? ' <span class=\"mjtc-zywrap-required\">*</span>' : ' <span class=\"mjtc-zywrap-label-opt\">' + zy_i18n.optional_label + '</span>';
        return '<div class=\"mjtc-zywrap-pg-form-group\"><label class=\"mjtc-zywrap-pg-label\">' + label + reqHtml + '</label><textarea class=\"zywrap-dyn-var mjtc-zywrap-pg-textarea\" data-key=\"' + zywrapAttr(key) + '\" placeholder=\"' + zywrapAttr(placeholder) + '\" rows=\"' + (rows || 3) + '\">' + zywrapEscapeHtml(defaultVal || '') + '</textarea></div>';
    }

    function zywrapBuildContextSummary(contextFields) {
        if (!contextFields.length) return '';
        var html = '<details id=\"zywrap-ticket-context-details\" class=\"mjtc-zywrap-agent-context\" style=\"margin:0 0 16px;border:1px solid #e2e8f0;border-radius:12px;background:#f8fafc;\">';
        html += '<summary style=\"cursor:pointer;padding:12px 14px;font-weight:800;color:#0f172a;display:flex;align-items:center;justify-content:space-between;gap:12px;\">';
        html += '<span>' + zy_i18n.ticket_context_loaded + '</span><span style=\"font-size:12px;font-weight:600;color:#64748b;\">' + contextFields.length + ' fields</span></summary>';
        html += '<div style=\"padding:0 14px 14px;\">';
        html += '<p class=\"mjtc-zywrap-p-hint\" style=\"margin-top:0;\">' + zy_i18n.ticket_context_hint + '</p>';
        \$.each(contextFields, function(i, fieldHtml) { html += fieldHtml; });
        html += '</div></details>';
        return html;
    }

    function zywrapFindResponseEditorId() {
        var candidates = [
            'mjsupport_message',
            'mjsupport_replytext',
            'mjtc-support-reply-textarea',
            'jsticket_message',
            'message',
            'reply',
            'ticketreply',
            'ticketresponse',
            'response',
            'content'
        ];
        var best = { id: '', score: -1 };

        function consider(id, score) {
            if (!id) return;
            id = String(id);
            if (id.indexOf('zywrap') !== -1) return;
            var el = jQuery(document.getElementById(id));
            if (el.length && el.closest('#mjtc-zywrap-modal').length) return;
            if (score > best.score) best = { id: id, score: score };
        }

        if (window.tinymce) {
            try {
                var active = window.tinymce.activeEditor;
                if (active && active.id && jQuery.inArray(active.id, candidates) !== -1) {
                    consider(active.id, 10000);
                }
            } catch(e) {}

            if (typeof window.tinymce.get === 'function') {
                jQuery.each(candidates, function(i, id) {
                    var ed = window.tinymce.get(id);
                    if (ed) {
                        var wrapVisible = jQuery('#wp-' + id + '-wrap').length ? jQuery('#wp-' + id + '-wrap').is(':visible') : true;
                        consider(id, 8000 + (wrapVisible ? 1000 : 0) - i);
                    }
                });
            }

            try {
                jQuery.each(window.tinymce.editors || [], function(i, ed) {
                    if (!ed || !ed.id) return;
                    var el = jQuery(document.getElementById(ed.id));
                    if (el.length && el.closest('#mjtc-zywrap-modal').length) return;
                    var score = 1000;
                    if (jQuery.inArray(ed.id, candidates) !== -1) score += 5000;
                    if (el.hasClass('wp-editor-area')) score += 1000;
                    if (jQuery('#wp-' + ed.id + '-wrap').is(':visible')) score += 500;
                    consider(ed.id, score);
                });
            } catch(e) {}
        }

        jQuery.each(candidates, function(i, id) {
            if (document.getElementById(id)) {
                var wrapVisible = jQuery('#wp-' + id + '-wrap').length ? jQuery('#wp-' + id + '-wrap').is(':visible') : jQuery(document.getElementById(id)).is(':visible');
                consider(id, 6000 + (wrapVisible ? 800 : 0) - i);
            }
        });

        jQuery('textarea.wp-editor-area, textarea[name=\"mjsupport_message\"], textarea[name=\"reply\"], textarea[name=\"message\"]').each(function(i) {
            if (jQuery(this).closest('#mjtc-zywrap-modal').length) return;
            var id = jQuery(this).attr('id');
            if (id) consider(id, 5000 - i);
        });

        return best.id;
    }

    function zywrapInsertIntoResponseEditor(text) {
        var editorId = zywrapFindResponseEditorId();
        if (editorId) {
            var inserted = ZywrapFormatter.smartInsert(editorId, text);
            if (inserted) return true;
        }

        var area = jQuery('textarea#mjsupport_message, textarea[name=\"mjsupport_message\"], textarea.wp-editor-area, textarea[name=\"reply\"], textarea[name=\"message\"]').filter(function() {
            return !jQuery(this).closest('#mjtc-zywrap-modal').length;
        }).first();
        if (area.length) {
            var current = area.val() || '';
            area.val(current + (current ? String.fromCharCode(10,10) : '') + ZywrapFormatter.toPlainText(text)).trigger('input').trigger('change');
            return true;
        }
        return false;
    }

    function zywrapUpdateGenerateButtonLabel() {
        var mode = $('#zywrap-response-mode').length ? $('#zywrap-response-mode').val() : 'new';
        var text = zy_i18n.btn_generate_reply;
        if (mode === 'improve') text = zy_i18n.btn_improve_draft;
        else if (mode === 'shorter') text = zy_i18n.btn_shorter;
        else if (mode === 'warmer') text = zy_i18n.btn_warmer;
        else if (mode === 'ask_info') text = zy_i18n.btn_ask_info;
        $('#zywrap-generate-btn').html('<span class=\"dashicons dashicons-update-alt\" aria-hidden=\"true\"></span> ' + text);
    }

    function openZywrapModal(targetTab) {
        \$('#mjtc-zywrap-backdrop, #mjtc-zywrap-modal').fadeIn(200);
        zywrapEnsureDebugPanel();
        setTimeout(function() {
            if (targetTab) {
                \$('.zywrap-intent-tab[data-target=\"' + targetTab + '\"]').trigger('click');
            }
            var selected = \$('.zywrap-action-card.selected:visible').first();
            if (!selected.length) selected = \$('.zywrap-action-card[data-code=\"ticket_thread_reply_composer\"]').first();
            if (selected.length) {
                selected.trigger('click');
            }
        }, 80);
    }

    \$('#mjtc-open-zywrap-modal').off('click').on('click', function(e) {
        e.preventDefault();
        openZywrapModal(null);
    });

    \$(document).off('click', '.zywrap-open-tab-btn, .mjtc-zywrap-open-tab-btn').on('click', '.zywrap-open-tab-btn, .mjtc-zywrap-open-tab-btn', function(e) {
        e.preventDefault();
        if (\$(this).data('active') != '1') { alert(zy_i18n.unconfigured_alert); return; }
        openZywrapModal(\$(this).data('tab'));
    });

    \$('.zywrap-intent-tab').on('click', function(e) {
        e.preventDefault();
        \$('.zywrap-intent-tab').removeClass('active');
        \$(this).addClass('active');
        \$('.zywrap-intent-panel').hide();
        \$('#zywrap-intent-' + \$(this).data('target')).css('display', (\$(this).data('target') === 'compose' || \$(this).data('target') === 'more') ? 'block' : 'flex');
    });

    \$('#zywrap-usecase-search').on('keyup', function() {
        var val = \$(this).val().toLowerCase();
        \$('#zywrap-intent-more .zywrap-action-card').filter(function() {
            \$(this).toggle(\$(this).find('h4').text().toLowerCase().indexOf(val) > -1)
        });
    });

    \$('.zywrap-action-card').on('click', function() {
        var useCaseCode = \$(this).data('code');
        var cardTitle = \$(this).data('title');
        \$('.zywrap-action-card').removeClass('selected');
        \$(this).addClass('selected');
        \$('#workspace-action-title').text(cardTitle);
        \$('#zywrap-generate-btn').prop('disabled', true);
        \$('#zywrap-dynamic-playground').html('<div class=\"mjtc-zywrap-empty-prompt\"><span class=\"spinner is-active\"></span> ' + zy_i18n.loading_context + '</div>');

        \$.ajax({
            url: zywrapAjaxUrl,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'mjsupport_ajax',
                mjsmod: 'zywrap', task: 'ajaxGetWrappers',
                use_case_code: useCaseCode, ticket_id: currentTicketId, _wpnonce: zywrapNonce
            }
        }).done(function(response) {
            var res = typeof response === 'object' ? response : JSON.parse(response);
            if (!res.success) {
                \$('#zywrap-dynamic-playground').html('<div class=\"mjtc-zywrap-status-tag-error\">' + zy_i18n.error_prefix + ' ' + res.data.message + '</div>');
                return;
            }

            var apiData = res.data;
            if(apiData.wrappers && apiData.wrappers.length > 0) {
                \$('#zywrap-active-wrapper').val(apiData.wrappers[0].code);
                \$('#zywrap-generate-btn').prop('disabled', false);
            } else {
                \$('#zywrap-dynamic-playground').html('<div class=\"mjtc-zywrap-status-tag-error\">' + zy_i18n.no_variations + '</div>');
                return;
            }

            var html = '';
            var dbContext = apiData.ticketData || {};
            var currentAgentDraft = zywrapGetCurrentResponseDraft();

            function toTitleCase(str) { return str.replace(/([A-Z])/g, ' \$1').replace(/_/g, ' ').replace(/^./, function(str){ return str.toUpperCase(); }); }
            function getSmartDefaultValue(key, label, field) {
                var k = (key || '').toLowerCase(), l = (label || '').toLowerCase();
                if (k.indexOf('draft') !== -1 || k.indexOf('reply') !== -1) return currentAgentDraft;
                if (k.indexOf('subject') !== -1 || l.indexOf('subject') !== -1) return dbContext.subject || '';
                if (k.indexOf('caseid') !== -1 || k === 'caseid' || k === 'ticketid') return dbContext.ticketMask || dbContext.subject || '';
                if (l.indexOf('latest') !== -1 || k.indexOf('latest') !== -1 || l.indexOf('last message') !== -1) return dbContext.latestCustomerMsg || '';
                if (l.indexOf('thread') !== -1 || l.indexOf('history') !== -1 || k.indexOf('thread') !== -1 || l.indexOf('summary') !== -1 || l.indexOf('experience') !== -1 || l.indexOf('issue') !== -1 || l.indexOf('message') !== -1 || k.indexOf('source') !== -1) return dbContext.fullThread || dbContext.latestCustomerMsg || '';
                return zywrapNormalizeSchemaFieldValue(field);
            }

            var schemaReq = apiData.schema ? (apiData.schema.req || apiData.schema.required || {}) : {};
            var schemaOpt = apiData.schema ? (apiData.schema.opt || apiData.schema.optional || {}) : {};
            if (apiData.schema && (Object.keys(schemaReq).length || Object.keys(schemaOpt).length)) {
                html += '<div class=\"mjtc-zywrap-instructions-card\">';

                html += '<div class=\"mjtc-zywrap-pg-form-group\" style=\"margin:0 0 14px;\"><label class=\"mjtc-zywrap-pg-label\">' + zy_i18n.response_mode + '</label><select id=\"zywrap-response-mode\" class=\"mjtc-zywrap-modal-select\" style=\"width:100%;max-width:320px;\">';
                if (currentAgentDraft) {
                    html += '<option value=\"improve\">' + zy_i18n.mode_improve + '</option><option value=\"new\">' + zy_i18n.mode_new + '</option><option value=\"shorter\">' + zy_i18n.mode_shorter + '</option><option value=\"warmer\">' + zy_i18n.mode_warmer + '</option><option value=\"ask_info\">' + zy_i18n.mode_ask_info + '</option>';
                } else {
                    html += '<option value=\"new\">' + zy_i18n.mode_new + '</option><option value=\"ask_info\">' + zy_i18n.mode_ask_info + '</option>';
                }
                html += '</select></div>';

                if (currentAgentDraft) {
                    html += '<div class=\"mjtc-zywrap-current-draft\" style=\"margin:0 0 16px;padding:12px 14px;border:1px solid #bfdbfe;background:#eff6ff;border-radius:12px;color:#1e3a8a;\">';
                    html += '<div style=\"display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:6px;\">';
                    html += '<div style=\"font-weight:800;\">' + zy_i18n.current_draft_label + '</div>';
                    html += '<label style=\"display:flex;align-items:center;gap:8px;font-size:13px;font-weight:700;\"><input type=\"checkbox\" id=\"zywrap-use-current-draft\" checked> ' + zy_i18n.use_current_draft + '</label>';
                    html += '</div>';
                    html += '<div style=\"font-size:12px;line-height:1.45;margin-bottom:8px;color:#475569;\">' + zy_i18n.current_draft_hint + '</div>';
                    html += '<div id=\"zywrap-current-draft-preview\" style=\"margin-top:8px;padding:8px 10px;background:#fff;border:1px solid #dbeafe;border-radius:8px;font-size:12px;color:#334155;\"><strong>' + zy_i18n.current_draft_preview + ':</strong> ' + zywrapEscapeHtml(zywrapGetCurrentDraftSummary(currentAgentDraft)) + '</div>';
                    html += '<input type=\"hidden\" id=\"zywrap-current-agent-draft\" value=\"' + zywrapAttr(currentAgentDraft) + '\">';
                    html += '</div>';
                }

                var contextFields = [];
                var visibleRequiredHtml = '';
                var advancedHtml = '';

                if (Object.keys(schemaReq).length) {
                    \$.each(schemaReq, function(key, v) {
                        var label = toTitleCase(key), defaultVal = getSmartDefaultValue(key, label, v);
                        var fieldHtml = zywrapBuildTextareaField(key, label, v, defaultVal, true, 4);
                        if (zywrapFieldLooksAutoContext(key, label, defaultVal)) contextFields.push(fieldHtml);
                        else visibleRequiredHtml += fieldHtml;
                    });
                }

                html += zywrapBuildContextSummary(contextFields);
                html += visibleRequiredHtml;

                if (Object.keys(schemaOpt).length) {
                    var promoted = [];
                    \$.each(schemaOpt, function(key, v) {
                        var label = toTitleCase(key), defaultVal = getSmartDefaultValue(key, label, v);
                        var fieldHtml = zywrapBuildTextareaField(key, label, v, defaultVal, false, 2);
                        if (promoted.indexOf(key) !== -1 && defaultVal) html += fieldHtml;
                        else advancedHtml += fieldHtml;
                    });
                    if (advancedHtml) {
                        html += '<details id=\"zywrap-advanced-options\" style=\"margin-top:14px;border:1px solid #e2e8f0;border-radius:12px;background:#f8fafc;\">';
                        html += '<summary style=\"cursor:pointer;padding:12px 14px;font-weight:700;color:#0f172a;\">' + zy_i18n.advanced_options + '</summary>';
                        html += '<div style=\"padding:0 14px 14px;\">';
                        html += '<p class=\"mjtc-zywrap-p-hint\" style=\"margin-top:0;\">' + zy_i18n.advanced_hint + '</p>';
                        html += advancedHtml;
                        html += '</div></details>';
                    }
                }
                html += '</div>';
            }
            \$('#zywrap-dynamic-playground').html(html || '<div class=\"mjtc-zywrap-empty-prompt\">' + zy_i18n.ready_generate + '</div>');
            \$('#zywrap-extra-instructions-container').show();
            \$('#zywrap-extra-instructions').val('');
            zywrapUpdateGenerateButtonLabel();
        }).fail(function(xhr) {
            var msg = zy_i18n.ajax_failed;
            if (xhr && xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message) {
                msg = xhr.responseJSON.data.message;
            } else if (xhr && xhr.responseText) {
                var responseText = xhr.responseText || '';
                if (responseText.indexOf('<html') !== -1 || responseText.indexOf('<!DOCTYPE') !== -1 || responseText.indexOf('wp-admin') !== -1) {
                    msg = 'The request returned a WordPress page instead of JSON. Endpoint URL: ' + zywrapAjaxUrl + ' | POST action: mjsupport_ajax | Preview: ' + responseText.replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').substring(0, 220);
                } else {
                    msg = responseText.replace(/<[^>]*>/g, '').substring(0, 300);
                }
            }
            \$('#zywrap-dynamic-playground').html('<div class=\"mjtc-zywrap-status-tag-error\">' + zy_i18n.error_prefix + ' ' + msg + '</div>');
            \$('#zywrap-generate-btn').prop('disabled', true);
        });

    });

    \$(document).off('change', '#zywrap-response-mode').on('change', '#zywrap-response-mode', function() {
        zywrapUpdateGenerateButtonLabel();
    });

    \$('#zywrap-generate-btn').on('click', function() {
        var btn = \$(this), wrapperCode = \$('#zywrap-active-wrapper').val();
        if(!wrapperCode) return;

        var dynamicVars = {}, structuredTextParts = []; 
        \$('.zywrap-dyn-var').each(function() {
            var k = \$(this).data('key'), v = \$(this).val().trim(); 
            if (v) { dynamicVars[k] = v; structuredTextParts.push(k + ': ' + v); }
        });

        var currentAgentDraft = zywrapGetCurrentResponseDraft();
        var useCurrentDraft = \$('#zywrap-use-current-draft').length ? \$('#zywrap-use-current-draft').is(':checked') : !!currentAgentDraft;
        if (useCurrentDraft && currentAgentDraft) {
            dynamicVars.currentAgentDraft = currentAgentDraft;
            dynamicVars.existingDraftReply = currentAgentDraft;
            if (!dynamicVars.draftReply) dynamicVars.draftReply = currentAgentDraft;
            structuredTextParts.push('currentAgentDraft: ' + currentAgentDraft);
        }

        var finalPrompt = \$('#zywrap-extra-instructions').length ? \$('#zywrap-extra-instructions').val().trim() : '';
        if (finalPrompt) {
            dynamicVars.keyPointsToInclude = finalPrompt;
            dynamicVars.agentPriorityInstructions = finalPrompt;
            structuredTextParts.push('keyPointsToInclude: ' + finalPrompt);
        }
        var responseMode = \$('#zywrap-response-mode').length ? \$('#zywrap-response-mode').val() : (currentAgentDraft ? 'improve' : 'new');
        var modeInstruction = '';
        if (responseMode === 'improve' && useCurrentDraft && currentAgentDraft) {
            modeInstruction = 'Use currentAgentDraft as the starting point. Preserve the agent intent, improve clarity and tone, and avoid contradicting the existing draft unless it is unsafe or inaccurate.';
        } else if (responseMode === 'shorter' && useCurrentDraft && currentAgentDraft) {
            modeInstruction = 'Rewrite currentAgentDraft into a shorter, clearer customer-ready reply. Preserve the important facts and next step.';
        } else if (responseMode === 'warmer' && useCurrentDraft && currentAgentDraft) {
            modeInstruction = 'Rewrite currentAgentDraft with a warmer, more empathetic support tone while keeping it concise and accurate.';
        } else if (responseMode === 'ask_info') {
            modeInstruction = 'Draft a concise customer reply that asks only for the missing information needed to move the ticket forward. Avoid asking for unnecessary details.';
        } else {
            modeInstruction = 'Draft the best customer-ready support reply using the ticket context and latest customer message.';
        }
        dynamicVars.responseMode = responseMode;
        if (modeInstruction) {
            finalPrompt = finalPrompt ? (modeInstruction + '\\n\\nAdditional instructions:\\n' + finalPrompt) : modeInstruction;
        }
        var structuredText = structuredTextParts.join('\\n');
        if (finalPrompt && structuredText) finalPrompt += '\\n\\n' + structuredText;
        else if (structuredText) finalPrompt = structuredText;

        \$('#zywrap-spinner').addClass('is-active');
        \$('#zywrap-status-text').text(zy_i18n.drafting);
        btn.prop('disabled', true);
        \$('#zywrap-draft-area').val('');

        \$.ajax({
            url: zywrapAjaxUrl,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'mjsupport_ajax',
                mjsmod: 'zywrap', task: 'generateReply',
                tone: \$('#zywrap-tone-select').val(), model_code: \$('#zywrap-model-select').val(),
                wrapper_code: wrapperCode, prompt: finalPrompt, variables: JSON.stringify(dynamicVars),
                _wpnonce: zywrapNonce 
            }
        }).done(function(response) {
            \$('#zywrap-spinner').removeClass('is-active');
            \$('#zywrap-status-text').text('');
            btn.prop('disabled', false);
            
            try {
                var data;
                if (typeof response === 'object') { data = response; } 
                else {
                    var cleanResponse = response, jsonStart = response.indexOf('{'), jsonEnd = response.lastIndexOf('}');
                    if (jsonStart !== -1 && jsonEnd !== -1) { cleanResponse = response.substring(jsonStart, jsonEnd + 1); }
                    data = JSON.parse(cleanResponse);
                }
                
                if(data.success) {
                    \$('#zywrap-draft-area').val(data.data.output);
                    \$('#zywrap-insert-btn').prop('disabled', false);
                } else {
                    \$('#zywrap-draft-area').val(zy_i18n.error_prefix + ' ' + (data.data && data.data.message ? data.data.message : data.message));
                }
            } catch(e) {
                \$('#zywrap-draft-area').val(zy_i18n.sys_error);
            }
        }).fail(function(xhr) {
            \$('#zywrap-spinner').removeClass('is-active');
            \$('#zywrap-status-text').text('');
            btn.prop('disabled', false);
            var msg = zy_i18n.ajax_failed;
            if (xhr && xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message) {
                msg = xhr.responseJSON.data.message;
            } else if (xhr && xhr.responseText) {
                msg = xhr.responseText.replace(/<[^>]*>/g, '').substring(0, 300);
            }
            \$('#zywrap-draft-area').val(zy_i18n.error_prefix + ' ' + msg);
        });
    });

    \$('#zywrap-insert-btn').on('click', function() {
        var aiText = \$('#zywrap-draft-area').val();
        if (!aiText) return;
        var ok = zywrapInsertIntoResponseEditor(aiText);
        if (ok) {
            \$('#zywrap-status-text').text('Inserted into response editor.');
            setTimeout(function(){ \$('#mjtc-zywrap-close').trigger('click'); }, 250);
        } else {
            \$('#zywrap-status-text').text('Could not find the response editor.');
        }
    });

    \$('#mjtc-zywrap-close, #mjtc-zywrap-backdrop').on('click', function(e) { 
        e.preventDefault(); 
        \$('#mjtc-zywrap-backdrop, #mjtc-zywrap-modal').fadeOut(200); 
    });

    \$(document).off('click', '.zywrap-inline-ai-btn').on('click', '.zywrap-inline-ai-btn', function(e) {
        e.preventDefault();
        var btn = \$(this), wrapperCode = btn.data('wrapper'), actionName = btn.text().trim(); 
        
        var specificMessageText = btn.closest('.zywrap-inline-actions').prev('.note-msg, .js-tkt-det-tkt-msg').text().trim();
        if(!specificMessageText) specificMessageText = btn.closest('.mjtc-thread, .ticket-details').find('.note-msg, .js-tkt-det-tkt-msg').text().trim();

        var resultBox = btn.closest('.zywrap-inline-actions').next('.zywrap-inline-result');
        if (btn.data('active') != '1') {
            resultBox.html('<div class=\"mjtc-zywrap-status-card mjtc-zywrap-status-inactive\"><strong>' + zy_i18n.unlock_title + '</strong><br>' + zy_i18n.unlock_desc + '<br><a href=\"?page=zywrap&mjslay=zywrap_settings\">' + zy_i18n.configure_link + '</a></div>').slideDown(200);
            return;
        }

        var inlineVars = { 'ticketThread': specificMessageText, 'message': specificMessageText, 'text': specificMessageText };
        var finalPrompt = specificMessageText, reqLanguage = ''; 

        if (wrapperCode === 'tl_supp_tick_tran_loca_926d_base') {
            var targetLang = btn.data('lang') || 'English'; 
            reqLanguage = targetLang; 
            actionName = zy_i18n.translate_to + ' ' + targetLang; 
        }

        if (wrapperCode === 'ee_support_ticket_detail_extraction_base') actionName = zy_i18n.extract_details;

        btn.prop('disabled', true).css('opacity', '0.5');
        resultBox.html('<span class=\"spinner is-active\" style=\"float:none; margin:0 5px 0 0; width:14px; height:14px;\"></span> ' + zy_i18n.ai_executing + ' ' + actionName + '...').slideDown(200);

        var defaultModel = '" . esc_js($mjtc_support_global_default_model) . "';
        var ajaxPayload = {
            action: 'mjsupport_ajax',
            mjsmod: 'zywrap', 
            task: 'generateReply', 
            tone: 'professional',
            language: reqLanguage, 
            wrapper_code: wrapperCode, 
            prompt: finalPrompt, 
            variables: JSON.stringify(inlineVars), 
            _wpnonce: zywrapNonce 
        };

        if (defaultModel !== '') {
            ajaxPayload.model_code = defaultModel;
        }

        \$.post(zywrapAjaxUrl, ajaxPayload, function(response) {
            btn.prop('disabled', false).css('opacity', '1');
            try {
                var cleanResponse = response;
                if (typeof response === 'string') {
                    var jsonStart = response.indexOf('{'), jsonEnd = response.lastIndexOf('}');
                    if (jsonStart !== -1 && jsonEnd !== -1) { cleanResponse = response.substring(jsonStart, jsonEnd + 1); }
                }
                var data = typeof cleanResponse === 'object' ? cleanResponse : JSON.parse(cleanResponse);
                
                if(data.success) {
                    var headerHtml = '<div style=\"font-weight:600; margin-bottom:15px; border-bottom:1px solid #cbd5e1; padding-bottom:8px; color:#1e40af; display:flex; align-items:center; gap:6px;\">';
                    headerHtml += '<span class=\"dashicons dashicons-superhero-alt\" style=\"font-size:18px; width:18px; height:18px;\"></span>';
                    headerHtml += zy_i18n.ai_insight + ' ' + actionName + '</div>'; 
                    
                    var rawOutput = data.data.output;
                    var formattedOutput = '';

                    try {
                        var possibleJsonString = rawOutput.replace(/^```json/i, '').replace(/```\$/i, '').trim();
                        var parsedJson = JSON.parse(possibleJsonString);
                        formattedOutput = '<div class=\"zywrap-json-content\">' + ZywrapFormatter.formatJSON(parsedJson) + '</div>';
                    } catch(e) {
                        formattedOutput = '<div class=\"zywrap-markdown-content\">' + ZywrapFormatter.toHTML(rawOutput) + '</div>';
                    }
                    
                    resultBox.html(headerHtml + formattedOutput);
                } else {
                    resultBox.html('<span style=\"color:#ef4444;\">' + zy_i18n.error_prefix + ' ' + (data.data.message || data.message) + '</span>');
                }
            } catch(e) {
                resultBox.html('<span style=\"color:#ef4444;\">' + zy_i18n.sys_error + '</span>');
            }
        });
    });
});
";
wp_add_inline_script('majestic-support-cmain-js',$majesticsupport_js);
?>
