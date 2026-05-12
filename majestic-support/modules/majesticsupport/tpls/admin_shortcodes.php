<?php
	if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	$MJTC_color1 = "#291abc";
    $MJTC_color3 = "#f5f2f5";
    $MJTC_color_string_values = get_option("ms_set_theme_colors");
    if($MJTC_color_string_values != ''){
        $MJTC_json_values = json_decode($MJTC_color_string_values,true);
        if(is_array($MJTC_json_values) && !empty($MJTC_json_values)){
            $MJTC_color1 = $MJTC_json_values['color1'];
            $MJTC_color3 = $MJTC_json_values['color3'];
        }
    }
?>
<div id="msadmin-wrapper">
    <div id="msadmin-leftmenu">
        <?php  MJTC_includer::MJTC_getClassesInclude('msadminsidemenu'); ?>
    </div>
    <div id="msadmin-data">
    	<?php MJTC_includer::MJTC_getModel('majesticsupport')->getPageTitle('shortcodes'); ?>
    	<div id="msadmin-data-wrp">
			<div id="ms-shortcode-wrapper">
				<div class="ms-shortcode-1"><?php echo esc_html(__('Majestic Support / Majestic Support Control Panel','majestic-support')); ?></div>
				<div class="ms-shortcode-2">
					<span class="ms-code-text">
						<?php echo esc_html("[majesticsupport]"); ?>
					</span>
					<button class="ms-copy-btn" title="<?php echo esc_attr(__('Copy','majestic-support')); ?>">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z"></path></svg>
                    </button>
				</div>
				<div class="ms-shortcode-3"><?php echo esc_html(__("Majestic Support / Majestic Support main control panel",'majestic-support')); ?></div>
			</div>
			<div id="ms-shortcode-wrapper">
				<div class="ms-shortcode-1"><?php echo esc_html(__('Add Ticket','majestic-support')); ?></div>
				<div class="ms-shortcode-2">
					<span class="ms-code-text">
						<?php echo esc_html("[majesticsupport_addticket]"); ?>
					</span>
					<button class="ms-copy-btn" title="<?php echo esc_attr(__('Copy','majestic-support')); ?>">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z"></path></svg>
                    </button>
						
				</div>
				<div class="ms-shortcode-3"><?php echo esc_html(__("Add new ticket form for both the user and the agent",'majestic-support')); ?></div>
			</div>
			<?php if(in_array('multiform', majesticsupport::$_active_addons)){ ?>
				<div id="ms-shortcode-wrapper">
					<div class="ms-shortcode-1"><?php echo esc_html(__('Add Ticket Using Multiform','majestic-support')); ?></div>
					<?php 
						$MJTC_multiforms = majesticsupport::$_data[0]['multiforms'];
						foreach ($MJTC_multiforms as $MJTC_multiform) {
						 	$MJTC_data = '<div class="ms-shortcode-2">
						 		<span class="ms-code-text">';
									$MJTC_data .= '
						 			[majesticsupport_addticket_multiform formid='.esc_attr($MJTC_multiform->id).']';
								$MJTC_data .= '</span>
								<button class="ms-copy-btn" title="'. esc_html(__('Copy','majestic-support')).'">
			                        <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z"></path></svg>
			                    </button>';
					 			$MJTC_data .= '<span class="ms-shortcode-name">('.esc_html(majesticsupport::MJTC_getVarValue($MJTC_multiform->title)).')</span>';
						 		if (isset($MJTC_multiform->departmentname)) {
						 			$MJTC_data .= '<span class="ms-shortcode-dept"> - '.esc_html($MJTC_multiform->departmentname).')</span>';
						 		} else {
						 			$MJTC_data .= '<span class="ms-shortcode-dept">)</span>';
						 		}
					 		$MJTC_data .= '</div>';
					 		echo wp_kses($MJTC_data, MJTC_ALLOWED_TAGS);
						} ?>
					<div class="ms-shortcode-3"><?php echo esc_html(__("Add new ticket form for both the user and the agent",'majestic-support')); ?></div>
				</div>
			<?php } ?>
			<div id="ms-shortcode-wrapper">
				<div class="ms-shortcode-1"><?php echo esc_html(__('My Tickets','majestic-support')); ?></div>
				<div class="ms-shortcode-2">
					<span class="ms-code-text">
						<?php echo esc_html("[majesticsupport_mytickets]"); ?>
					</span>
					<button class="ms-copy-btn" title="<?php echo esc_attr(__('Copy','majestic-support')); ?>">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z"></path></svg>
                    </button>
				</div>
				<div class="ms-shortcode-3"><?php echo esc_html(__("My tickets for both user and agent",'majestic-support')); ?></div>
			</div>
			<?php if(in_array('download', majesticsupport::$_active_addons)){ ?>
				<div id="ms-shortcode-wrapper">
					<div class="ms-shortcode-1"><?php echo esc_html(__('Downloads','majestic-support')); ?></div>
					<div class="ms-shortcode-2">
						<span class="ms-code-text">
							<?php echo esc_html("[majesticsupport_downloads]"); ?>
						</span>
						<button class="ms-copy-btn" title="<?php echo esc_attr(__('Copy','majestic-support')); ?>">
	                        <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z"></path></svg>
	                    </button>
					</div>
					<div class="ms-shortcode-3"><?php echo esc_html(__("List Downloads",'majestic-support')); ?></div>
				</div>
				<div id="ms-shortcode-wrapper">
					<div class="ms-shortcode-1"><?php echo esc_html(__('Latest Downloads','majestic-support')); ?></div>
					<div class="ms-shortcode-2">
						<span class="ms-code-text">
							<?php echo esc_html("[majesticsupport_downloads_latest]"); ?>
						</span>
						<button class="ms-copy-btn" title="<?php echo esc_attr(__('Copy','majestic-support')); ?>">
	                        <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z"></path></svg>
	                    </button>
					</div>
					<div class="ms-shortcode-3"><?php echo esc_html(__("Show latest downloads. Options",'majestic-support')).': text_color="'.esc_attr($MJTC_color3).'" '.esc_html(__("and",'majestic-support')).' background_color="'.esc_attr($MJTC_color1).'" '.esc_html(__("i.e.",'majestic-support')).' [majesticsupport_downloads_latest text_color="'.esc_attr($MJTC_color3).'" background_color="'.esc_attr($MJTC_color1).'"]'; ?></div>
				</div>
				<div id="ms-shortcode-wrapper">
					<div class="ms-shortcode-1"><?php echo esc_html(__('Popular Downloads','majestic-support')); ?></div>
					<div class="ms-shortcode-2">
						<span class="ms-code-text">
							<?php echo esc_html("[majesticsupport_downloads_popular]"); ?>
						</span>
						<button class="ms-copy-btn" title="<?php echo esc_attr(__('Copy','majestic-support')); ?>">
	                        <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z"></path></svg>
	                    </button>
						</div>
					<div class="ms-shortcode-3"><?php echo esc_html(__("Show popular downloads. Options",'majestic-support')).': text_color="'.esc_attr($MJTC_color3).'" '.esc_html(__("and",'majestic-support')).' background_color="'.esc_attr($MJTC_color1).'" '.esc_html(__("i.e.",'majestic-support')).' [majesticsupport_downloads_popular text_color="'.esc_attr($MJTC_color3).'" background_color="'.esc_attr($MJTC_color1).'"]'; ?></div>
				</div>
			<?php } ?>
			<?php if(in_array('knowledgebase', majesticsupport::$_active_addons)){ ?>
				<div id="ms-shortcode-wrapper">
					<div class="ms-shortcode-1"><?php echo esc_html(__('Knowledge Base','majestic-support')); ?></div>
					<div class="ms-shortcode-2">
						<span class="ms-code-text">
							<?php echo esc_html("[majesticsupport_knowledgebase]"); ?>
						</span>
						<button class="ms-copy-btn" title="<?php echo esc_attr(__('Copy','majestic-support')); ?>">
	                        <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z"></path></svg>
	                    </button>
					</div>
					<div class="ms-shortcode-3"><?php echo esc_html(__("List Knowledge Base",'majestic-support')); ?></div>
				</div>
				<div id="ms-shortcode-wrapper">
					<div class="ms-shortcode-1"><?php echo esc_html(__('Latest Knowledge Base','majestic-support')); ?></div>
					<div class="ms-shortcode-2">
						<span class="ms-code-text">
							<?php echo esc_html("[majesticsupport_knowledgebase_latest]"); ?>
						</span>
						<button class="ms-copy-btn" title="<?php echo esc_attr(__('Copy','majestic-support')); ?>">
	                        <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z"></path></svg>
	                    </button>
					</div>
					<div class="ms-shortcode-3"><?php echo esc_html(__("Show latest knowledge base. Options",'majestic-support')).': text_color="'.esc_attr($MJTC_color3).'" '.esc_html(__("and",'majestic-support')).' background_color="'.esc_attr($MJTC_color1).'" '.esc_html(__("i.e.",'majestic-support')).' [majesticsupport_knowledgebase_latest text_color="'.esc_attr($MJTC_color3).'" background_color="'.esc_attr($MJTC_color1).'"]'; ?></div>
				</div>
				<div id="ms-shortcode-wrapper">
					<div class="ms-shortcode-1"><?php echo esc_html(__('Popular knowledge base','majestic-support')); ?></div>
					<div class="ms-shortcode-2">
						<span class="ms-code-text">
							<?php echo esc_html("[majesticsupport_knowledgebase_popular]"); ?>
						</span>
						<button class="ms-copy-btn" title="<?php echo esc_attr(__('Copy','majestic-support')); ?>">
	                        <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z"></path></svg>
	                    </button>
					</div>
					<div class="ms-shortcode-3"><?php echo esc_html(__("Show popular knowledge base. Options",'majestic-support')).': text_color="'.esc_attr($MJTC_color3).'" '.esc_html(__("and",'majestic-support')).' background_color="'.esc_attr($MJTC_color1).'" '.esc_html(__("i.e.",'majestic-support')).' [majesticsupport_knowledgebase_popular text_color="'.esc_attr($MJTC_color3).'" background_color="'.esc_attr($MJTC_color1).'"]'; ?></div>
				</div>
			<?php } ?>
			<?php if(in_array('faq', majesticsupport::$_active_addons)){ ?>
				<div id="ms-shortcode-wrapper">
					<div class="ms-shortcode-1"><?php echo esc_html(__("FAQs",'majestic-support')); ?></div>
					<div class="ms-shortcode-2">
						<span class="ms-code-text">
							<?php echo esc_html("[majesticsupport_faqs]"); ?>
						</span>
						<button class="ms-copy-btn" title="<?php echo esc_attr(__('Copy','majestic-support')); ?>">
	                        <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z"></path></svg>
	                    </button>
					</div>
					<div class="ms-shortcode-3"><?php echo esc_html(__("List FAQs",'majestic-support')); ?></div>
				</div>
				<div id="ms-shortcode-wrapper">
					<div class="ms-shortcode-1"><?php echo esc_html(__("Latest FAQs",'majestic-support')); ?></div>
					<div class="ms-shortcode-2">
						<span class="ms-code-text">
							<?php echo esc_html("[majesticsupport_faqs_latest]"); ?>
						</span>
						<button class="ms-copy-btn" title="<?php echo esc_attr(__('Copy','majestic-support')); ?>">
	                        <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z"></path></svg>
	                    </button>
					</div>
					<div class="ms-shortcode-3"><?php echo esc_html(__("Show latest FAQs. Options",'majestic-support')).': text_color="'.esc_attr($MJTC_color3).'" '.esc_html(__("and",'majestic-support')).' background_color="'.esc_attr($MJTC_color1).'" '.esc_html(__("i.e.",'majestic-support')).' [majesticsupport_faqs_latest text_color="'.esc_attr($MJTC_color3).'" background_color="'.esc_attr($MJTC_color1).'"]'; ?></div>
				</div>
				<div id="ms-shortcode-wrapper">
					<div class="ms-shortcode-1"><?php echo esc_html(__("Popular FAQs",'majestic-support')); ?></div>
					<div class="ms-shortcode-2">
						<span class="ms-code-text">
							<?php echo esc_html("[majesticsupport_faqs_popular]"); ?>
						</span>
						<button class="ms-copy-btn" title="<?php echo esc_attr(__('Copy','majestic-support')); ?>">
	                        <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z"></path></svg>
	                    </button>
					</div>
					<div class="ms-shortcode-3"><?php echo esc_html(__("Show popular FAQs. Options",'majestic-support')).': text_color="'.esc_attr($MJTC_color3).'" '.esc_html(__("and",'majestic-support')).' background_color="'.esc_attr($MJTC_color1).'" '.esc_html(__("i.e.",'majestic-support')).' [majesticsupport_faqs_popular text_color="'.esc_attr($MJTC_color3).'" background_color="'.esc_attr($MJTC_color1).'"]'; ?></div>
				</div>
			<?php } ?>
			<?php if(in_array('announcement', majesticsupport::$_active_addons)){ ?>
				<div id="ms-shortcode-wrapper">
					<div class="ms-shortcode-1"><?php echo esc_html(__('Announcements','majestic-support')); ?></div>
					<div class="ms-shortcode-2">
						<span class="ms-code-text">
							<?php echo esc_html("[majesticsupport_announcements]"); ?>
						</span>
						<button class="ms-copy-btn" title="<?php echo esc_attr(__('Copy','majestic-support')); ?>">
	                        <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z"></path></svg>
	                    </button>
					</div>
					<div class="ms-shortcode-3"><?php echo esc_html(__("List Announcements",'majestic-support')); ?></div>
				</div>
				<div id="ms-shortcode-wrapper">
					<div class="ms-shortcode-1"><?php echo esc_html(__('Latest Announcements','majestic-support')); ?></div>
					<div class="ms-shortcode-2">
						<span class="ms-code-text">
							<?php echo esc_html("[majesticsupport_announcements_latest]"); ?>
						</span>
						<button class="ms-copy-btn" title="<?php echo esc_attr(__('Copy','majestic-support')); ?>">
	                        <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z"></path></svg>
	                    </button>
					</div>
					<div class="ms-shortcode-3"><?php echo esc_html(__("Show latest announcements. Options",'majestic-support')).': text_color="'.esc_attr($MJTC_color3).'" '.esc_html(__("and",'majestic-support')).' background_color="'.esc_attr($MJTC_color1).'" '.esc_html(__("i.e.",'majestic-support')).' [majesticsupport_announcements_latest text_color="'.esc_attr($MJTC_color3).'" background_color="'.esc_attr($MJTC_color1).'"]'; ?></div>
				</div>
				<div id="ms-shortcode-wrapper">
					<div class="ms-shortcode-1"><?php echo esc_html(__('Popular Announcements','majestic-support')); ?></div>
					<div class="ms-shortcode-2">
						<span class="ms-code-text">
							<?php echo esc_html("[majesticsupport_announcements_popular]"); ?>
						</span>
						<button class="ms-copy-btn" title="<?php echo esc_attr(__('Copy','majestic-support')); ?>">
	                        <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z"></path></svg>
	                    </button>
					</div>
					<div class="ms-shortcode-3"><?php echo esc_html(__("Show popular announcements. Options",'majestic-support')).': text_color="'.esc_attr($MJTC_color3).'" '.esc_html(__("and",'majestic-support')).' background_color="'.esc_attr($MJTC_color1).'" '.esc_html(__("i.e.",'majestic-support')).' [majesticsupport_announcements_popular text_color="'.esc_attr($MJTC_color3).'" background_color="'.esc_attr($MJTC_color1).'"]'; ?></div>
				</div>
			<?php } ?>
		</div>
	</div>
</div>
<?php
$majesticsupport_js = '
jQuery(document).ready(function ($) {

    jQuery(document).on("click", ".ms-copy-btn", function () {
        var shortcodeText = jQuery(this).closest(".ms-shortcode-2").find(".ms-code-text").text().trim();

        // Create temporary textarea
        var $temp = jQuery("<textarea>");
        jQuery("body").append($temp);
        $temp.val(shortcodeText).select();
        jQuery(".ms-copy-btn").removeClass("ms-copied");

        try {
            document.execCommand("copy");
            showCopied(jQuery(this));
        } catch (err) {
            alert("Copy failed. Please copy manually.");
        }

        $temp.remove();
    });

    function showCopied($btn) {
        var originalTitle = $btn.attr("title");
        $btn.attr("title", "Copied!").addClass("ms-copied");

        setTimeout(function () {
            $btn.attr("title", originalTitle).removeClass("ms-copied");
        }, 5000);
    }

});
';
wp_add_inline_script('majestic-support-cmain-js',$majesticsupport_js);
?>
