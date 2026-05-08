<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<div id="msadmin-wrapper">
    <div id="msadmin-leftmenu">
        <?php  MJTC_includer::MJTC_getClassesInclude('msadminsidemenu'); ?>
    </div>
    <div id="msadmin-data">
        <?php MJTC_includer::MJTC_getModel('majesticsupport')->getPageTitle('admin_help'); ?>
    	<div id="msadmin-data-wrp">
    		<!-- help page -->
    		<div class="msupportadmin-help-top">
    			<div class="msupportadmin-help-top-left">
    				<div class="msupportadmin-help-top-left-cnt-img">
    					<svg viewBox="0 0 24 24" fill="currentColor"><path d="M10 16.5l6-4.5-6-4.5v9zM12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8 8z"></path></svg>
    				</div>
    				<div class="msupportadmin-help-top-left-cnt-info">
    					<h2><?php echo esc_html(__('Videos Guidance','majestic-support')); ?></h2>
    					<p><?php echo esc_html(__('A reputable support system that offers step-by-step YouTube video guides to help you understand.','majestic-support')); ?></p>
    					<a href="https://www.youtube.com/@Majestic-Support/videos" target="_blank" class="msupportadmin-help-top-middle-action" title="<?php echo esc_attr(__('View All Videos','majestic-support')); ?>"><svg viewBox="0 0 24 24" fill="white" xmlns="http://www.w3.org/2000/svg"><path d="M8 5v14l11-7z"/></svg><?php echo esc_html(__('View All Videos','majestic-support')); ?></a>
    				</div>
    			</div>
    			<div class="msupportadmin-help-top-right">
    				<div class="msupportadmin-help-top-right-cnt-img">
    					<svg viewBox="0 0 24 24" fill="currentColor"><path d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-7 9h-2V5h2v6zm0 4h-2v-2h2v2z"></path></svg>
    				</div>
    				<div class="msupportadmin-help-top-right-cnt-info">
    					<h2><?php echo esc_html(__('Contact Us For Support','majestic-support')); ?></h2>
    					<p><?php echo esc_html(__("At Majestic Support, we are committed to offering prompt and helpful customer care to help you at every step.",'majestic-support')); ?></p>
    					<a target="_blank" href="https://majesticsupport.com/support/" class="msupportadmin-help-top-middle-action second" title="<?php echo esc_attr(__('Submit ticket','majestic-support')); ?>"><svg viewBox="0 0 24 24" fill="#111827" xmlns="http://www.w3.org/2000/svg"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg><?php echo esc_html(__('Submit Ticket','majestic-support')); ?></a>
    				</div>
    			</div>
    		</div>
    		<div class="msupportadmin-help-btm">
                <!-- how to setup -->
                <div class="msupportadmin-help-btm-wrp">
                    <h2 class="msupportadmin-help-btm-title"><?php echo esc_html(__('How to setup','majestic-support')); ?></h2>
                    <div class="msupportadmin-help-btm-cnt">
                        <a href="https://www.youtube.com/watch?v=lHAacpG-O0M" class="msupportadmin-help-btm-link" target="_blank">
                            <div class="msupportadmin-help-btm-cnt-img">
                                <img alt="<?php echo esc_attr(__('How to setup','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/help-page/video-icon.png" />
                            </div>
                            <div class="msupportadmin-help-btm-cnt-title">
                                <span><?php echo esc_html(__('How to setup','majestic-support')); ?></span>
                            </div>
                        </a>
                    </div>
                </div>
    			<!-- tickets -->
    			<div class="msupportadmin-help-btm-wrp">
    				<h2 class="msupportadmin-help-btm-title"><?php echo esc_html(__('Tickets','majestic-support')); ?></h2>
                    <div class="msupportadmin-help-btm-cnt">
                        <a href="https://www.youtube.com/watch?v=dYniAnKyv-Q" class="msupportadmin-help-btm-link"  target="_blank">
                            <div class="msupportadmin-help-btm-cnt-img">
                                <img alt="<?php echo esc_attr(__('Ticket Creation','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/help-page/video-icon.png" />
                            </div>
                            <div class="msupportadmin-help-btm-cnt-title">
                                <span><?php echo esc_html(__('Ticket Creation','majestic-support')); ?></span>
                            </div>
                        </a>
                    </div>
                    <div class="msupportadmin-help-btm-cnt">
                        <a href="https://www.youtube.com/watch?v=9NvBOu_ojMo" class="msupportadmin-help-btm-link"  target="_blank">
                            <div class="msupportadmin-help-btm-cnt-img">
                                <img alt="<?php echo esc_attr(__('Visitor ticket creation','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/help-page/video-icon.png" />
                            </div>
                            <div class="msupportadmin-help-btm-cnt-title">
                                <span><?php echo esc_html(__('Visitor ticket creation','majestic-support')); ?></span>
                            </div>
                        </a>
                    </div>
                    <div class="msupportadmin-help-btm-cnt">
                        <a href="https://www.youtube.com/watch?v=2iA8SuNLmMI" class="msupportadmin-help-btm-link"  target="_blank">
                            <div class="msupportadmin-help-btm-cnt-img">
                                <img alt="<?php echo esc_attr(__('How to set ticket auto close','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/help-page/video-icon.png" />
                            </div>
                            <div class="msupportadmin-help-btm-cnt-title">
                                <span><?php echo esc_html(__('How to set ticket auto close','majestic-support')); ?></span>
                            </div>
                        </a>
                    </div>
                    <div class="msupportadmin-help-btm-cnt">
                        <a href="https://www.youtube.com/watch?v=6VcIPSd6n4g" class="msupportadmin-help-btm-link"  target="_blank">
                            <div class="msupportadmin-help-btm-cnt-img">
                                <img alt="<?php echo esc_attr(__('How to reopen closed ticket','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/help-page/video-icon.png" />
                            </div>
                            <div class="msupportadmin-help-btm-cnt-title">
                                <span><?php echo esc_html(__('How to reopen closed ticket','majestic-support')); ?></span>
                            </div>
                        </a>
                    </div>
                    <div class="msupportadmin-help-btm-cnt">
                        <a href="https://www.youtube.com/watch?v=mDHvlkYbbVM" class="msupportadmin-help-btm-link" target="_blank">
                            <div class="msupportadmin-help-btm-cnt-img">
                                <img alt="<?php echo esc_attr(__('Configuration','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/help-page/video-icon.png" />
                            </div>
                            <div class="msupportadmin-help-btm-cnt-title">
                                <span><?php echo esc_html(__('How to lock a ticket','majestic-support')); ?></span>
                            </div>
                        </a>
                    </div>
                    <div class="msupportadmin-help-btm-cnt">
                        <a href="https://www.youtube.com/watch?v=WHvXkgYLtv8" class="msupportadmin-help-btm-link"  target="_blank">
                            <div class="msupportadmin-help-btm-cnt-img">
                                <img alt="<?php echo esc_attr(__('How to add private note','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/help-page/video-icon.png" />
                            </div>
                            <div class="msupportadmin-help-btm-cnt-title">
                                <span><?php echo esc_html(__('How to add private note','majestic-support')); ?></span>
                            </div>
                        </a>
                    </div>
                    <div class="msupportadmin-help-btm-cnt">
                        <a href="https://www.youtube.com/watch?v=7Cf_dae8pws" class="msupportadmin-help-btm-link"  target="_blank">
                            <div class="msupportadmin-help-btm-cnt-img">
                                <img alt="<?php echo esc_attr(__('View ticket history','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/help-page/video-icon.png" />
                            </div>
                            <div class="msupportadmin-help-btm-cnt-title">
                                <span><?php echo esc_html(__('View ticket history','majestic-support')); ?></span>
                            </div>
                        </a>
                    </div>
                    <div class="msupportadmin-help-btm-cnt">
                        <a href="https://www.youtube.com/watch?v=8dIMdKuTLx4&ab_channel=MajesticSupport" class="msupportadmin-help-btm-link" target="_blank">
                            <div class="msupportadmin-help-btm-cnt-img">
                                <img alt="<?php echo esc_attr(__('How to setup custom fields','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/help-page/video-icon.png" />
                            </div>
                            <div class="msupportadmin-help-btm-cnt-title">
                                <span><?php echo esc_html(__('How to setup custom fields','majestic-support')); ?></span>
                            </div>
                        </a>
                    </div>
                    <div class="msupportadmin-help-btm-cnt">
                        <a href="https://www.youtube.com/watch?v=9Jb0tVc0kHk" class="msupportadmin-help-btm-link"  target="_blank">
                            <div class="msupportadmin-help-btm-cnt-img">
                                <img alt="<?php echo esc_attr(__('Set ticket auto overdue','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/help-page/video-icon.png" />
                            </div>
                            <div class="msupportadmin-help-btm-cnt-title">
                                <span><?php echo esc_html(__('Set ticket auto overdue','majestic-support')); ?></span>
                            </div>
                        </a>
                    </div>
                    <div class="msupportadmin-help-btm-cnt">
                        <a href="https://www.youtube.com/watch?v=hgXrjsOwj7o" class="msupportadmin-help-btm-link"  target="_blank">
                            <div class="msupportadmin-help-btm-cnt-img">
                                <img alt="<?php echo esc_attr(__('Manually set ticket overdue','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/help-page/video-icon.png" />
                            </div>
                            <div class="msupportadmin-help-btm-cnt-title">
                                <span><?php echo esc_html(__('Manually set ticket overdue','majestic-support')); ?></span>
                            </div>
                        </a>
                    </div>
                    <div class="msupportadmin-help-btm-cnt">
                        <a href="https://www.youtube.com/watch?v=p1JJTwsfWG8" class="msupportadmin-help-btm-link"  target="_blank">
                            <div class="msupportadmin-help-btm-cnt-img">
                                <img alt="<?php echo esc_attr(__('How to merge tickets','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/help-page/video-icon.png" />
                            </div>
                            <div class="msupportadmin-help-btm-cnt-title">
                                <span><?php echo esc_html(__('How to merge tickets','majestic-support')); ?></span>
                            </div>
                        </a>
                    </div>
                    <div class="msupportadmin-help-btm-cnt">
                        <a href="https://www.youtube.com/watch?v=dOtQgNIvERY" class="msupportadmin-help-btm-link"  target="_blank">
                            <div class="msupportadmin-help-btm-cnt-img">
                                <img alt="<?php echo esc_attr(__('How to export tickets','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/help-page/video-icon.png" />
                            </div>
                            <div class="msupportadmin-help-btm-cnt-title">
                                <span><?php echo esc_html(__('How to export tickets','majestic-support')); ?></span>
                            </div>
                        </a>
                    </div>
                    <div class="msupportadmin-help-btm-cnt">
                        <a href="https://www.youtube.com/watch?v=8mS5EWOUl7c" class="msupportadmin-help-btm-link" target="_blank">
                            <div class="msupportadmin-help-btm-cnt-img">
                                <img alt="<?php echo esc_attr(__('How use help topic','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/help-page/video-icon.png" />
                            </div>
                            <div class="msupportadmin-help-btm-cnt-title">
                                <span><?php echo esc_html(__('How use help topic','majestic-support')); ?></span>
                            </div>
                        </a>
                    </div>
                    <div class="msupportadmin-help-btm-cnt">
                        <a href="https://www.youtube.com/watch?v=cakoKGssICI" class="msupportadmin-help-btm-link" target="_blank">
                            <div class="msupportadmin-help-btm-cnt-img">
                                <img alt="<?php echo esc_attr(__('How to change department','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/help-page/video-icon.png" />
                            </div>
                            <div class="msupportadmin-help-btm-cnt-title">
                                <span><?php echo esc_html(__('How to change department','majestic-support')); ?></span>
                            </div>
                        </a>
                    </div>
                    <div class="msupportadmin-help-btm-cnt">
                        <a href="https://www.youtube.com/watch?v=Z5-dKDt8DJ8" class="msupportadmin-help-btm-link" target="_blank">
                            <div class="msupportadmin-help-btm-cnt-img">
                                <img alt="<?php echo esc_attr(__('How to use multi-forms','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/help-page/video-icon.png" />
                            </div>
                            <div class="msupportadmin-help-btm-cnt-title">
                                <span><?php echo esc_html(__('How to use multi-forms','majestic-support')); ?></span>
                            </div>
                        </a>
                    </div>
                    <div class="msupportadmin-help-btm-cnt">
                        <a href="https://www.youtube.com/watch?v=WWA2y6P0HiU" class="msupportadmin-help-btm-link" target="_blank">
                            <div class="msupportadmin-help-btm-cnt-img">
                                <img alt="<?php echo esc_attr(__('How to paid support','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/help-page/video-icon.png" />
                            </div>
                            <div class="msupportadmin-help-btm-cnt-title">
                                <span><?php echo esc_html(__('How to paid support','majestic-support')); ?></span>
                            </div>
                        </a>
                    </div>
                    <div class="msupportadmin-help-btm-cnt">
                        <a href="https://www.youtube.com/watch?v=f7EDGS1d9OU" class="msupportadmin-help-btm-link" target="_blank">
                            <div class="msupportadmin-help-btm-cnt-img">
                                <img alt="<?php echo esc_attr(__('How to use premade response','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/help-page/video-icon.png" />
                            </div>
                            <div class="msupportadmin-help-btm-cnt-title">
                                <span><?php echo esc_html(__('How to use premade response','majestic-support')); ?></span>
                            </div>
                        </a>
                    </div>
                    <div class="msupportadmin-help-btm-cnt">
                        <a href="https://www.youtube.com/watch?v=qlDlPlS2QWY" class="msupportadmin-help-btm-link" target="_blank">
                            <div class="msupportadmin-help-btm-cnt-img">
                                <img alt="<?php echo esc_attr(__('How to add private credentials','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/help-page/video-icon.png" />
                            </div>
                            <div class="msupportadmin-help-btm-cnt-title">
                                <span><?php echo esc_html(__('How to add private credentials','majestic-support')); ?></span>
                            </div>
                        </a>
                    </div>
                    <div class="msupportadmin-help-btm-cnt">
                        <a href="https://www.youtube.com/watch?v=OndVFydfzZI" class="msupportadmin-help-btm-link" target="_blank">
                            <div class="msupportadmin-help-btm-cnt-img">
                                <img alt="<?php echo esc_attr(__('How to ban/unban user','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/help-page/video-icon.png" />
                            </div>
                            <div class="msupportadmin-help-btm-cnt-title">
                                <span><?php echo esc_html(__('How to ban/unban user','majestic-support')); ?></span>
                            </div>
                        </a>
                    </div>	
    			</div>
                <!-- agents -->
                <div class="msupportadmin-help-btm-wrp">
                    <h2 class="msupportadmin-help-btm-title"><?php echo esc_html(__('Agents','majestic-support')); ?></h2>
                    <div class="msupportadmin-help-btm-cnt">
                        <a href="https://www.youtube.com/watch?v=gA7XIvLX1Ko" class="msupportadmin-help-btm-link" target="_blank">
                            <div class="msupportadmin-help-btm-cnt-img">
                                <img alt="<?php echo esc_attr(__('Agent system','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/help-page/video-icon.png" />
                            </div>
                            <div class="msupportadmin-help-btm-cnt-title">
                                <span><?php echo esc_html(__('Agent system','majestic-support')); ?></span>
                            </div>
                        </a>
                    </div>
                    <div class="msupportadmin-help-btm-cnt">
                        <a href="https://www.youtube.com/watch?v=_Dl-7qci9GE" class="msupportadmin-help-btm-link" target="_blank">
                            <div class="msupportadmin-help-btm-cnt-img">
                                <img alt="<?php echo esc_attr(__('Agent Auto Assign','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/help-page/video-icon.png" />
                            </div>
                            <div class="msupportadmin-help-btm-cnt-title">
                                <span><?php echo esc_html(__('Agent Auto Assign','majestic-support')); ?></span>
                            </div>
                        </a>
                    </div>
                    <div class="msupportadmin-help-btm-cnt">
                        <a href="https://www.youtube.com/watch?v=miejXvLPuek" class="msupportadmin-help-btm-link"  target="_blank">
                            <div class="msupportadmin-help-btm-cnt-img">
                                <img alt="<?php echo esc_attr(__('Manually assign ticket to agent','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/help-page/video-icon.png" />
                            </div>
                            <div class="msupportadmin-help-btm-cnt-title">
                                <span><?php echo esc_html(__('Manually assign ticket to agent','majestic-support')); ?></span>
                            </div>
                        </a>
                    </div>
                    <div class="msupportadmin-help-btm-cnt">
                            <a href="https://www.youtube.com/watch?v=z1xjqvcT_Yc" class="msupportadmin-help-btm-link" target="_blank">
                                <div class="msupportadmin-help-btm-cnt-img">
                                    <img alt="<?php echo esc_attr(__('How to edit time','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/help-page/video-icon.png" />
                                </div>
                                <div class="msupportadmin-help-btm-cnt-title">
                                    <span><?php echo esc_html(__('How to edit time','majestic-support')); ?></span>
                                </div>
                            </a>
                    </div>
                    <div class="msupportadmin-help-btm-cnt">
                        <a href="https://www.youtube.com/watch?v=mEBkMs59rJY" class="msupportadmin-help-btm-link" target="_blank">
                            <div class="msupportadmin-help-btm-cnt-img">
                                <img alt="<?php echo esc_attr(__('How to use time tracking','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/help-page/video-icon.png" />
                            </div>
                            <div class="msupportadmin-help-btm-cnt-title">
                                <span><?php echo esc_html(__('How to use time tracking','majestic-support')); ?></span>
                            </div>
                        </a>
                    </div>
                </div>
                <!-- smart reply -->
                <div class="msupportadmin-help-btm-wrp">
                    <h2 class="msupportadmin-help-btm-title"><?php echo esc_html(__('Smart Replies','majestic-support')); ?></h2>
                    <div class="msupportadmin-help-btm-cnt">
                        <a href="https://www.youtube.com/watch?v=YDYnagRWyEU" class="msupportadmin-help-btm-link" target="_blank">
                            <div class="msupportadmin-help-btm-cnt-img">
                                <img alt="<?php echo esc_attr(__('How to use smart replies','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/help-page/video-icon.png" />
                            </div>
                            <div class="msupportadmin-help-btm-cnt-title">
                                <span><?php echo esc_html(__('How to use smart replies','majestic-support')); ?></span>
                            </div>
                        </a>
                    </div>
                </div>
    			<!-- configurations -->
    			<div class="msupportadmin-help-btm-wrp">
    				<h2 class="msupportadmin-help-btm-title"><?php echo esc_html(__('Configuration','majestic-support')); ?></h2>
    				<div class="msupportadmin-help-btm-cnt">
    					<a href="https://www.youtube.com/watch?v=gCB-wGVZph8&ab_channel=MajesticSupport" class="msupportadmin-help-btm-link" target="_blank">
                            <div class="msupportadmin-help-btm-cnt-img">
                                <img alt="<?php echo esc_attr(__('How to show counts','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/help-page/video-icon.png" />
                            </div>
                            <div class="msupportadmin-help-btm-cnt-title">
                                <span><?php echo esc_html(__('How to show counts','majestic-support')); ?></span>
                            </div>
    					</a>
    				</div>
                    <div class="msupportadmin-help-btm-cnt">
                        <a href="https://www.youtube.com/watch?v=TAQ6UtCHO6k" class="msupportadmin-help-btm-link" target="_blank">
                            <div class="msupportadmin-help-btm-cnt-img">
                                <img alt="<?php echo esc_attr(__('How to set Captcha','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/help-page/video-icon.png" />
                            </div>
                            <div class="msupportadmin-help-btm-cnt-title">
                                <span><?php echo esc_html(__('How to set Captcha','majestic-support')); ?></span>
                            </div>
                        </a>
                    </div>
    				<div class="msupportadmin-help-btm-cnt">
    					<a href="https://www.youtube.com/watch?v=6AE4ZHB9bJk&ab_channel=MajesticSupport" class="msupportadmin-help-btm-link" target="_blank">
                             <div class="msupportadmin-help-btm-cnt-img">
                                <img alt="<?php echo esc_attr(__('User Options','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/help-page/video-icon.png" />
                            </div>
                            <div class="msupportadmin-help-btm-cnt-title">
                                <span><?php echo esc_html(__('User Options','majestic-support')); ?></span>
                            </div>
    					</a>
    				</div>
    				<div class="msupportadmin-help-btm-cnt">
    					<a href="https://www.youtube.com/watch?v=bzK2IxQ0QaU" class="msupportadmin-help-btm-link" target="_blank">
                            <div class="msupportadmin-help-btm-cnt-img">
                                <img alt="<?php echo esc_attr(__('How to set login redirect','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/help-page/video-icon.png" />
                            </div>
                            <div class="msupportadmin-help-btm-cnt-title">
                                <span><?php echo esc_html(__('How to set login redirect','majestic-support')); ?></span>
                            </div>
    					</a>
    				</div>
    				<div class="msupportadmin-help-btm-cnt">
    					<a href="https://www.youtube.com/watch?v=8dIMdKuTLx4" class="msupportadmin-help-btm-link" target="_blank">
                            <div class="msupportadmin-help-btm-cnt-img">
                                <img alt="<?php echo esc_attr(__('How to set fields ordering','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/help-page/video-icon.png" />
                            </div>
                            <div class="msupportadmin-help-btm-cnt-title">
                                <span><?php echo esc_html(__('How to set fields ordering','majestic-support')); ?></span>
                            </div>
    					</a>
    				</div>
    			</div>
    			<!-- email piping -->
    			<div class="msupportadmin-help-btm-wrp">
    				<h2 class="msupportadmin-help-btm-title"><?php echo esc_html(__('Setup','majestic-support')); ?></h2>
                    <div class="msupportadmin-help-btm-cnt">
                        <a href="https://www.youtube.com/watch?v=ySZXLllQWRY" class="msupportadmin-help-btm-link" target="_blank">
                            <div class="msupportadmin-help-btm-cnt-img">
                                <img alt="<?php echo esc_attr(__('How to enable email piping','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/help-page/video-icon.png" />
                            </div>
                            <div class="msupportadmin-help-btm-cnt-title">
                                <span><?php echo esc_html(__('How to enable email piping','majestic-support')); ?></span>
                            </div>
                        </a>
                    </div>
                    <div class="msupportadmin-help-btm-cnt">
                        <a href="https://www.youtube.com/watch?v=JbR9MhSRH_s" class="msupportadmin-help-btm-link" target="_blank">
                            <div class="msupportadmin-help-btm-cnt-img">
                                <img alt="<?php echo esc_attr(__('How to set SMTP','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/help-page/video-icon.png" />
                            </div>
                            <div class="msupportadmin-help-btm-cnt-title">
                                <span><?php echo esc_html(__('How to set SMTP','majestic-support')); ?></span>
                            </div>
                        </a>
                    </div>
                    <div class="msupportadmin-help-btm-cnt">
                        <a href="https://www.youtube.com/watch?v=LoSe4aYnyBg" class="msupportadmin-help-btm-link" target="_blank">
                            <div class="msupportadmin-help-btm-cnt-img">
                                <img alt="<?php echo esc_attr(__('How to translate','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/help-page/video-icon.png" />
                            </div>
                            <div class="msupportadmin-help-btm-cnt-title">
                                <span><?php echo esc_html(__('How to translate','majestic-support')); ?></span>
                            </div>
                        </a>
                    </div>
    				<div class="msupportadmin-help-btm-cnt">
    					<a href="https://www.youtube.com/watch?v=OZTabfsnVIQ" class="msupportadmin-help-btm-link" target="_blank">
                            <div class="msupportadmin-help-btm-cnt-img">
                                <img alt="<?php echo esc_attr(__('How to set colors','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/help-page/video-icon.png" />
                            </div>
                            <div class="msupportadmin-help-btm-cnt-title">
                                <span><?php echo esc_html(__('How to set colors','majestic-support')); ?></span>
                            </div>
    					</a>
    				</div>
    				<div class="msupportadmin-help-btm-cnt">
    					<a href="https://www.youtube.com/watch?v=PV-shw5Nr8Q&ab_channel=MajesticSupport" class="msupportadmin-help-btm-link" target="_blank">
                            <div class="msupportadmin-help-btm-cnt-img">
                                <img alt="<?php echo esc_attr(__('How to add Shortcode','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/help-page/video-icon.png" />
                            </div>
                            <div class="msupportadmin-help-btm-cnt-title">
                                <span><?php echo esc_html(__('How to add Shortcode','majestic-support')); ?></span>
                            </div>
    					</a>
    				</div>
                    <div class="msupportadmin-help-btm-cnt">
                        <a href="https://www.youtube.com/watch?v=6xrHvIgRpZc" class="msupportadmin-help-btm-link" target="_blank">
                            <div class="msupportadmin-help-btm-cnt-img">
                                <img alt="<?php echo esc_attr(__('How to install add-ons','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/help-page/video-icon.png" />
                            </div>
                            <div class="msupportadmin-help-btm-cnt-title">
                                <span><?php echo esc_html(__('How to install add-ons','majestic-support')); ?></span>
                            </div>
                        </a>
                    </div>
    			</div>    
                <!-- knowledge-base,downloads,announcements,FAQ -->
                <div class="msupportadmin-help-btm-wrp msupportadmin-help-sub-category">
                    <h2 class="msupportadmin-help-btm-title"><?php echo esc_html(__('Knowledgebase','majestic-support')).', '.esc_html(__('Downloads','majestic-support')).', '.esc_html(__('Announcements','majestic-support')).', '.esc_html(__('and','majestic-support')).' '.esc_html(__('FAQs','majestic-support')); ?></h2>
                    <div class="msupportadmin-help-btm-cnt">
                        <a href="https://www.youtube.com/watch?v=g6l5M8hR1hE" class="msupportadmin-help-btm-link" target="_blank">
                            <div class="msupportadmin-help-btm-cnt-img">
                                <img alt="<?php echo esc_attr(__('How to use knowledge base','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/help-page/video-icon.png" />
                            </div>
                            <div class="msupportadmin-help-btm-cnt-title">
                                <span><?php echo esc_html(__('How to use knowledge base','majestic-support')); ?></span>
                            </div>
                        </a>
                    </div>
                    <div class="msupportadmin-help-btm-cnt">
                        <a href="https://www.youtube.com/watch?v=jrsWVyNJm54" class="msupportadmin-help-btm-link" target="_blank">
                            <div class="msupportadmin-help-btm-cnt-img">
                                <img alt="<?php echo esc_attr(__('How to use downloads','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/help-page/video-icon.png" />
                            </div>
                            <div class="msupportadmin-help-btm-cnt-title">
                                <span><?php echo esc_html(__('How to use downloads','majestic-support')); ?></span>
                            </div>
                        </a>
                    </div>
                    <div class="msupportadmin-help-btm-cnt">
                        <a href="https://www.youtube.com/watch?v=UJv3-FdD0Fs" class="msupportadmin-help-btm-link" target="_blank">
                            <div class="msupportadmin-help-btm-cnt-img">
                                <img alt="<?php echo esc_attr(__('How to add announcement','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/help-page/video-icon.png" />
                            </div>
                            <div class="msupportadmin-help-btm-cnt-title">
                                <span><?php echo esc_html(__('How to add announcement','majestic-support')); ?></span>
                            </div>
                        </a>
                    </div>
                    <div class="msupportadmin-help-btm-cnt">
                        <a href="https://www.youtube.com/watch?v=gOgxbQjdFJg" class="msupportadmin-help-btm-link" target="_blank">
                            <div class="msupportadmin-help-btm-cnt-img">
                                <img alt="<?php echo esc_attr(__('How to create FAQ','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/help-page/video-icon.png" />
                            </div>
                            <div class="msupportadmin-help-btm-cnt-title">
                                <span><?php echo esc_html(__('How to create FAQ','majestic-support')); ?></span>
                            </div>
                        </a>
                    </div>
                </div>
                <!-- misc -->
                <div class="msupportadmin-help-btm-wrp">
                    <h2 class="msupportadmin-help-btm-title"><?php echo esc_html(__('Misc','majestic-support')); ?></h2>
                    <div class="msupportadmin-help-btm-cnt">
                        <a href="https://www.youtube.com/watch?v=bu3h7LiGry0" class="msupportadmin-help-btm-link" target="_blank">
                            <div class="msupportadmin-help-btm-cnt-img">
                                <img alt="<?php echo esc_attr(__('How to use email cc','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/help-page/video-icon.png" />
                            </div>
                            <div class="msupportadmin-help-btm-cnt-title">
                                <span><?php echo esc_html(__('How to use email cc','majestic-support')); ?></span>
                            </div>
                        </a>
                    </div>
                    <div class="msupportadmin-help-btm-cnt">
                        <a href="https://www.youtube.com/watch?v=90pyFoM0nlo" class="msupportadmin-help-btm-link" target="_blank">
                            <div class="msupportadmin-help-btm-cnt-img">
                                <img alt="<?php echo esc_attr(__('How to use internal mail','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/help-page/video-icon.png" />
                            </div>
                            <div class="msupportadmin-help-btm-cnt-title">
                                <span><?php echo esc_html(__('How to use internal mail','majestic-support')); ?></span>
                            </div>
                        </a>
                    </div>
                    <div class="msupportadmin-help-btm-cnt">
                        <a href="https://www.youtube.com/watch?v=leWCkErUufo" class="msupportadmin-help-btm-link" target="_blank">
                            <div class="msupportadmin-help-btm-cnt-img">
                                <img alt="<?php echo esc_attr(__('Use front-end widgets','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/help-page/video-icon.png" />
                            </div>
                            <div class="msupportadmin-help-btm-cnt-title">
                                <span><?php echo esc_html(__('Use front-end widgets','majestic-support')); ?></span>
                            </div>
                        </a>
                    </div>
                    <div class="msupportadmin-help-btm-cnt">
                        <a href="https://www.youtube.com/watch?v=rq6-O0eIz5w" class="msupportadmin-help-btm-link" target="_blank">
                            <div class="msupportadmin-help-btm-cnt-img">
                                <img alt="<?php echo esc_attr(__('How to enable admin widgets','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/help-page/video-icon.png" />
                            </div>
                            <div class="msupportadmin-help-btm-cnt-title">
                                <span><?php echo esc_html(__('How to enable admin widgets','majestic-support')); ?></span>
                            </div>
                        </a>
                    </div>
                </div>
    		</div>
		</div>
	</div>
</div>
