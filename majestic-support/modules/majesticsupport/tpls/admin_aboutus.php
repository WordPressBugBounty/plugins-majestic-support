<?php
global $wp_version;

if ( ! function_exists( 'plugins_api' ) ) {
    require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
}

$plugin_slug = 'majestic-support';

// Request information from the WordPress.org API
$api_request = plugins_api( 'plugin_information', array(
    'slug'   => $plugin_slug,
    'fields' => array(
        'active_installs' => true,
        'rating'          => true,
        'downloaded'      => true
    )
) );

// Logic to handle errors or set dynamic variables
if ( ! is_wp_error( $api_request ) ) {
	// WordPress.org returns active_installs as an integer (e.g. 10000)
    // number_format_i18n adds commas based on the site's language
    $name = $api_request->name;
    $active_installs = number_format_i18n( $api_request->active_installs ) . '+';
    $rating_val = number_format( ( $api_request->rating / 100 ) * 5, 1 );
    
    $total_downloads = number_format_i18n( $api_request->downloaded );
    $current_version = $api_request->version;
    $author = $api_request->author;
    $author_profile = $api_request->author_profile;
} else {
    // Fallback static values if the API fails
    $name = __('Majestic Support System', 'majestic-support');
    $active_installs = '10,000+';
    $rating_val      = '4.9';
    $total_downloads = '50,000';
    $current_version = '2.4.0';
}


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<div id="msadmin-wrapper">
	<div id="msadmin-leftmenu">
		<?php  MJTC_includer::MJTC_getClassesInclude('msadminsidemenu'); ?>
	</div>
	<div id="msadmin-data">
		<?php MJTC_includer::MJTC_getModel('majesticsupport')->getPageTitle('admin_aboutus'); ?>
		<div id="msadmin-data-wrp" class="msadmin-aboutus-data-wrp">
		    
		    <!-- LEFT COLUMN: MAIN CONTENT -->
		    <div class="ms-main-col">
		        
		        <!-- HERO BANNER -->
		        <div class="ms-about-hero">
		            <div class="ms-hero-bg-shapes"></div>
		            <div class="ms-hero-logo">
		                <img alt="image" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/aboutus_page/logo.png">
		            </div>
		            <h2 class="ms-hero-title"><?php echo esc_html(__('Majestic Support', 'majestic-support')); ?></h2>
		            <p class="ms-hero-subtitle"><?php echo esc_html(__('Majestic Support for WordPress is a top-tier ticket system that can significantly enhance your customers’ support experience.', 'majestic-support')); ?></p>
		            <div class="ms-version-badge"><?php echo esc_html(__('Version', 'majestic-support')).' '.esc_html($current_version); ?><?php echo esc_html(__(' (Pro)', 'majestic-support')); ?></div>
		        </div>

		        <!-- STATS GRID -->
		        <div class="ms-stats-grid">
		            <div class="ms-stat-card">
		                <span class="ms-stat-num"><?php echo esc_html($active_installs); ?></span>
		                <span class="ms-stat-label"><?php echo esc_html(__('Active Installs', 'majestic-support')); ?></span>
		            </div>
		            <div class="ms-stat-card">
		                <span class="ms-stat-num"><?php echo esc_html($rating_val); ?></span>
		                <span class="ms-stat-label"><?php echo esc_html(__('Average Rating', 'majestic-support')); ?></span>
		            </div>
		            <div class="ms-stat-card">
		                <span class="ms-stat-num"><?php echo esc_html($total_downloads); ?></span>
		                <span class="ms-stat-label"><?php echo esc_html(__('Total Downloads', 'majestic-support')); ?></span>
		            </div>
		        </div>

		        <!-- OUR MISSION / STORY -->
		        <div class="ms-card">
		            <div class="ms-card-header">
		                <h3 class="ms-card-title">
		                    <svg viewBox="0 0 24 24" width="22" height="22" fill="var(--mjtc-admin-primary)"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
		                    <?php echo esc_html(__('Our Story', 'majestic-support')); ?>
		                </h3>
		            </div>
		            <div>
		                <p class="ms-content-text">
		                    <span class="ms-highlight-text"><?php echo esc_html(__('Majestic', 'majestic-support')); ?></span> <?php echo esc_html(__("was born out of a simple frustration: customer support tools were either too clunky, too expensive, or didn't integrate well with WordPress. We believed businesses deserved a solution that lived right inside their own dashboard, keeping data secure and workflows seamless.", 'majestic-support')); ?>
		                </p>
		                <p class="ms-content-text">
		                    <?php echo esc_html(__('Today, our goal is to provide the most intuitive, fast, and customizable support portal available on the market. We are continuously listening to user feedback to shape the future of this tool.', 'majestic-support')); ?>
		                </p>

		                <!-- Social Proof Testimonial embedded in the story -->
		                <div class="ms-testimonial">
		                    <p class="ms-testimonial-quote">"<?php echo esc_html(__('We recently implemented the Majestic Support plugin for a client’s website to manage ticketing, and it has proven to be an excellent choice. This highly optimized ticket management system offers many features, including an intelligent Smart Reply feature that saves time by suggesting relevant replies. One of the standout features of Majestic Support is its exceptional support and development team, who are always ready to assist. Their commitment to collaboration significantly enhances the user experience. If I had to select a ticket management plugin again, I would choose Majestic Support. The combination of a powerful plugin, and a dedicated support team makes it a top-tier solution for any business needing effective ticket management.', 'majestic-support')); ?>"</p>
		                    <div class="ms-testimonial-author">
		                        <img src="https://secure.gravatar.com/avatar/3afad4cef8c66ffd03b574bcab21dc81c93d1675c09785b383ec891b8ed0a02e?s=60&d=retro&r=g" alt="Avatar">
		                        <a style="text-decoration: none;" href="https://wordpress.org/support/topic/why-i-love-majestic-support-for-ticketing/" class="ms-testimonial-author-info">
		                            <span class="ms-testimonial-author-name"><?php echo esc_html('mzsandhu'); ?></span>
		                            <span class="ms-testimonial-author-role"><?php echo esc_html(__('Why I Love Majestic Support for Ticketing', 'majestic-support')); ?></span>
		                        </a>
		                    </div>
		                </div>
		            </div>
		        </div>

		        <!-- FEATURES -->
		        <div class="ms-card">
		            <div class="ms-card-header">
		                <h3 class="ms-card-title">
		                    <svg viewBox="0 0 24 24" width="22" height="22" fill="var(--mjtc-admin-primary)"><path d="M12 2l-5.5 9h11L12 2zm0 3.84L13.93 9h-3.87L12 5.84zM17.5 13c-2.49 0-4.5 2.01-4.5 4.5s2.01 4.5 4.5 4.5 4.5-2.01 4.5-4.5-2.01-4.5-4.5-4.5zm0 7c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5zM3 21.5h8v-8H3v8zm2-6h4v4H5v-4z"/></svg>
		                    <?php echo esc_html(__('Why Choose Majestic?', 'majestic-support')); ?>
		                </h3>
		            </div>
		            <div class="ms-feature-grid">
		                <div class="ms-feature-item">
		                    <div class="ms-feature-icon">
		                        <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/></svg>
		                    </div>
		                    <h4 class="ms-feature-title"><?php echo esc_html(__('Lightning Fast', 'majestic-support')); ?></h4>
		                    <p class="ms-feature-desc"><?php echo esc_html(__('Built with modern code standards ensuring your admin panel and frontend load instantly.', 'majestic-support')); ?></p>
		                </div>
		                <div class="ms-feature-item">
		                    <div class="ms-feature-icon">
		                        <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><path d="M19.14,12.94c0.04-0.3,0.06-0.61,0.06-0.94c0-0.32-0.02-0.64-0.06-0.94l2.03-1.58c0.18-0.14,0.23-0.41,0.12-0.61 l-1.92-3.32c-0.12-0.22-0.37-0.29-0.59-0.22l-2.39,0.96c-0.5-0.38-1.03-0.7-1.62-0.94L14.4,2.81c-0.04-0.24-0.24-0.41-0.48-0.41 h-3.84c-0.24,0-0.43,0.17-0.47,0.41L9.25,5.35C8.66,5.59,8.12,5.92,7.63,6.29L5.24,5.33c-0.22-0.08-0.47,0-0.59,0.22L2.73,8.87 C2.62,9.08,2.66,9.34,2.86,9.48l2.03,1.58C4.84,11.36,4.8,11.69,4.8,12s0.02,0.64,0.06,0.94l-2.03,1.58 c-0.18,0.14-0.23,0.41-0.12,0.61l1.92,3.32c0.12-0.22,0.37,0.29,0.59,0.22l2.39-0.96c0.5,0.38,1.03,0.7,1.62,0.94l0.36,2.54 c0.05,0.24,0.24,0.41,0.48,0.41h3.84c0.24,0,0.43-0.17,0.47-0.41l0.36-2.54c0.59-0.24,1.13-0.56,1.62-0.94l2.39,0.96 c0.22,0.08,0.47,0,0.59-0.22l1.92-3.32c0.12-0.22,0.07-0.49-0.12-0.61L19.14,12.94z M12,15.6c-1.98,0-3.6-1.62-3.6-3.6 s1.62-3.6,3.6-3.6s3.6,1.62,3.6,3.6S13.98,15.6,12,15.6z"/></svg>
		                    </div>
		                    <h4 class="ms-feature-title"><?php echo esc_html(__('Fully Customizable', 'majestic-support')); ?></h4>
		                    <p class="ms-feature-desc"><?php echo esc_html(__("Tailor every color, status, and layout to perfectly match your brand's unique identity.", 'majestic-support')); ?></p>
		                </div>
		                <div class="ms-feature-item">
		                    <div class="ms-feature-icon">
		                        <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
		                    </div>
		                    <h4 class="ms-feature-title"><?php echo esc_html(__('Team Collaboration', 'majestic-support')); ?></h4>
		                    <p class="ms-feature-desc"><?php echo esc_html(__('Easily assign tickets, manage departments, and communicate seamlessly with internal notes.', 'majestic-support')); ?></p>
		                </div>
		            </div>
		        </div>

		        <!-- RECENT UPDATES / CHANGELOG -->
		        <div class="ms-card">
		            <div class="ms-card-header">
		                <h3 class="ms-card-title">
		                    <svg viewBox="0 0 24 24" width="22" height="22" fill="var(--mjtc-admin-text-secondary)"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/></svg>
		                    <?php echo esc_html(__('Recent Updates', 'majestic-support')); ?>
		                </h3>
		                <a target="_blank" href="https://wordpress.org/plugins/majestic-support/#developers" style="font-size: 0.85rem; font-weight: 700; color: var(--mjtc-admin-primary);"><?php echo esc_html(__('View Full Changelog', 'majestic-support')); ?></a>
		            </div>
		            <div class="ms-changelog-list">
		                <div class="ms-changelog-item">
		                    <div class="ms-changelog-version"><?php echo esc_html(__('v1.1.2', 'majestic-support')); ?></div>
		                    <div class="ms-changelog-content">
		                        <h4><?php echo esc_html(__('Major Update: Security Enhancements, AI Features & Advanced Customization', 'majestic-support')); ?></h4>
		                        <p><?php echo esc_html(__('This release focuses on strengthening security while introducing powerful new features and flexibility across the system. We’ve added configuration-based reply ordering in ticket details and significantly enhanced field ordering with new options such as placeholders, descriptions, admin-only controls, default values, and multiple visibility conditions. Product selection and custom status support have also been introduced, along with a token-based option for checking ticket status.
                                On the usability side, we’ve improved the priority form with a color preview option and updated minimum required fields for better validation. User experience has been enhanced by adding GDPR fields to the registration form and introducing configuration-based user avatars.
                                A major highlight of this release is the introduction of AI-powered reply suggestions, helping agents respond more efficiently. Additionally, we’ve added SupportCandy import functionality to simplify migration from other systems. Overall, this update delivers better security, smarter automation, and more control for administrators.', 'majestic-support')); ?></p>
		                        <span class="ms-changelog-date"><?php echo esc_html(__('Released on', 'majestic-support')).' '.esc_html(date_i18n( 'F j, Y', strtotime( '2025-01-03' ) )); ?></span>
		                    </div>
		                </div>
		                <div class="ms-changelog-item">
		                    <div class="ms-changelog-version"><?php echo esc_html(__('v1.1.1', 'majestic-support')); ?></div>
		                    <div class="ms-changelog-content">
		                        <h4><?php echo esc_html(__('Security Update', 'majestic-support')); ?></h4>
		                        <p><?php echo esc_html(__('This release fixes security issues reported during the WordPress review process and improves the overall security of the plugin. Users are strongly encouraged to update to the latest version.', 'majestic-support')); ?></p>
		                        <span class="ms-changelog-date"><?php echo esc_html(__('Released on', 'majestic-support')).' '.esc_html(date_i18n( 'F j, Y', strtotime( '2025-12-28' ) )); ?></span>
		                    </div>
		                </div>
		                <div class="ms-changelog-item">
		                    <div class="ms-changelog-version"><?php echo esc_html(__('v1.1.0', 'majestic-support')); ?></div>
		                    <div class="ms-changelog-content">
		                        <h4><?php echo esc_html(__('Security & Bug Fix Update', 'majestic-support')); ?></h4>
		                        <p><?php echo esc_html(__('This release includes important security fixes along with general bug fixes. Issues identified during the WordPress plugin review process have been resolved, and overall stability and performance have been improved.', 'majestic-support')); ?></p>
		                        <span class="ms-changelog-date"><?php echo esc_html(__('Released on', 'majestic-support')).' '.esc_html(date_i18n( 'F j, Y', strtotime( '2025-08-10' ) )); ?></span>
		                    </div>
		                </div>
		            </div>
		        </div>

		        <!-- THE TEAM / CREDITS -->
		        <div class="ms-card">
		            <div class="ms-card-header">
		                <h3 class="ms-card-title">
		                    <svg viewBox="0 0 24 24" width="22" height="22" fill="var(--mjtc-admin-text-secondary)"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
		                    <?php echo esc_html(__('Core Development', 'majestic-support')); ?>
		                </h3>
		            </div>
		            <div class="ms-team-card">
		                <div class="ms-team-avatar"><?php echo esc_html(__('MS', 'majestic-support')); ?></div>
		                <div class="ms-team-info">
		                    <h4><?php echo wp_kses_post($author); ?></h4>
		                    <p><?php echo esc_html(__('Lead Developer & Creator of Majestic', 'majestic-support')); ?></p>
		                </div>
		                <div style="margin-left: auto;">
		                    <a href="<?php echo esc_url($author_profile); ?>" class="mjtc-admin-btn mjtc-admin-btn-light" style="padding: 0.5rem 1rem; font-size: 0.8rem;"><?php echo esc_html(__('View Profile', 'majestic-support')); ?></a>
		                </div>
		            </div>
		        </div>

		    </div>

		    <!-- RIGHT COLUMN: SIDEBAR -->
		    <div class="ms-sidebar-col">
		        
		        <!-- UPGRADE TO PRO WIDGET -->
		        <div class="ms-card ms-pro-widget" style="margin-bottom: 1.5rem;">
		            <div class="ms-hero-bg-shapes" style="opacity: 0.05;"></div>
		            <div class="ms-card-header">
		                <h3 class="ms-card-title"><?php echo esc_html(__('Unlock Pro Features', 'majestic-support')); ?></h3>
		            </div>
		            <p class="ms-content-text" style="color: #cbd5e1; font-size: 0.9rem; margin-top: -0.5rem;"><?php echo esc_html(__('Take your customer support to the next level with advanced tools designed for teams.', 'majestic-support')); ?></p>
		            <ul class="ms-pro-list">
		                <li>
		                    <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
		                    <?php echo esc_html(__('Unlimited Departments', 'majestic-support')); ?>
		                </li>
		                <li>
		                    <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
		                    <?php echo esc_html(__('Automated Workflows', 'majestic-support')); ?>
		                </li>
		                <li>
		                    <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
		                    <?php echo esc_html(__('Canned Responses', 'majestic-support')); ?>
		                </li>
		                <li>
		                    <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
		                    <?php echo esc_html(__('Custom Ticket Fields', 'majestic-support')); ?>
		                </li>
		            </ul>
		            <a href="https://majesticsupport.com/pricing/" class="mjtc-admin-btn mjtc-admin-btn-primary" style="width: 100%; box-shadow: none; background: white; color: #1e293b;"><?php echo esc_html(__('Upgrade Now', 'majestic-support')); ?></a>
		        </div>

		        <!-- FEATURED VIDEO WIDGET -->
		        <div class="ms-card" style="margin-bottom: 1.5rem; padding: 1.5rem;">
		            <div class="ms-card-header" style="padding-bottom: 0.75rem; margin-bottom: 0.75rem;">
		                <h3 class="ms-card-title">
		                    <svg viewBox="0 0 24 24" width="20" height="20" fill="var(--mjtc-admin-text-secondary)"><path d="M10 16.5l6-4.5-6-4.5v9zM12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/></svg>
		                    <?php echo esc_html(__('Featured Tutorial', 'majestic-support')); ?>
		                </h3>
		            </div>
		            <a href="https://www.youtube.com/watch?v=8zdTsKbgeYE&t=1s" target="_blank" class="ms-video-wrapper">
		            	<img alt="<?php echo esc_attr(__('Majestic Support','majestic-support')); ?>" class="userpopup-close" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/aboutus_page/youtube-thumbnail.png" alt="<?php echo esc_attr(__('Video Thumbnail', 'majestic-support')); ?>" />
		                <div class="ms-play-btn">
		                    <svg viewBox="0 0 24 24" width="24" height="24"><path d="M8 5v14l11-7z"/></svg>
		                </div>
		            </a>
		            <p class="ms-content-text" style="font-size: 0.9rem; margin-top: 1rem; margin-bottom: 0; font-weight: 700; text-align: center; color: var(--mjtc-admin-text-main);"><?php echo esc_html(__('Getting Started with Majestic', 'majestic-support')); ?></p>
		        </div>

		        <!-- USEFUL LINKS WIDGET -->
		        <div class="ms-card" style="margin-bottom: 1.5rem;">
		            <div class="ms-card-header">
		                <h3 class="ms-card-title"><?php echo esc_html(__('Useful Links', 'majestic-support')); ?></h3>
		            </div>
		            <ul class="ms-link-list">
		                <li>
		                    <a target="_blank" href="https://demo.majesticsupport.com/">
		                        <?php echo esc_html(__('Live Demo', 'majestic-support')); ?>
		                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6-1.41-1.41z"/></svg>
		                    </a>
		                </li>
		                <li>
		                    <a target="_blank" href="https://wordpress.org/support/plugin/majestic-support/">
		                        <?php echo esc_html(__('Community Forum', 'majestic-support')); ?>
		                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6-1.41-1.41z"/></svg>
		                    </a>
		                </li>
		                <li>
		                    <a target="_blank" href="https://majesticsupport.com/support/">
		                        <?php echo esc_html(__('Report a Bug', 'majestic-support')); ?>
		                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6-1.41-1.41z"/></svg>
		                    </a>
		                </li>
                        <li>
                            <a target="_blank" href="https://www.youtube.com/@Majestic-Support/videos">
                                <?php echo esc_html(__('Documentation', 'majestic-support')); ?>
                                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6-1.41-1.41z"/></svg>
                            </a>
                        </li>
		            </ul>
		        </div>

		        <!-- INTEGRATIONS WIDGET -->
		        <div class="ms-card" style="margin-bottom: 1.5rem;">
		            <div class="ms-card-header">
		                <h3 class="ms-card-title"><?php echo esc_html(__('Compatible With', 'majestic-support')); ?></h3>
		            </div>
		            <div class="ms-integration-list">
		                <span class="ms-integration-badge"><?php echo esc_html(__('WooCommerce', 'majestic-support')); ?></span>
		                <span class="ms-integration-badge"><?php echo esc_html(__('Easy Digital Downloads', 'majestic-support')); ?></span>
		                <span class="ms-integration-badge"><?php echo esc_html(__('Elementor', 'majestic-support')); ?></span>
		                <span class="ms-integration-badge"><?php echo esc_html(__('WPML', 'majestic-support')); ?></span>
		                <span class="ms-integration-badge"><?php echo esc_html(__('MemberPress', 'majestic-support')); ?></span>
		            </div>
		        </div>

		        <!-- SYSTEM STATUS WIDGET -->
		        <div class="ms-card">
		            <div class="ms-card-header">
		                <h3 class="ms-card-title">
		                    <svg viewBox="0 0 24 24" width="20" height="20" fill="var(--mjtc-admin-text-secondary)"><path d="M19.14,12.94c0.04-0.3,0.06-0.61,0.06-0.94c0-0.32-0.02-0.64-0.06-0.94l2.03-1.58c0.18-0.14,0.23-0.41,0.12-0.61 l-1.92-3.32c-0.12-0.22-0.37-0.29-0.59-0.22l-2.39,0.96c-0.5-0.38-1.03-0.7-1.62-0.94L14.4,2.81c-0.04-0.24-0.24-0.41-0.48-0.41 h-3.84c-0.24,0-0.43,0.17-0.47,0.41L9.25,5.35C8.66,5.59,8.12,5.92,7.63,6.29L5.24,5.33c-0.22-0.08-0.47,0-0.59,0.22L2.73,8.87 C2.62,9.08,2.66,9.34,2.86,9.48l2.03,1.58C4.84,11.36,4.8,11.69,4.8,12s0.02,0.64,0.06,0.94l-2.03,1.58 c-0.18,0.14-0.23,0.41-0.12,0.61l1.92,3.32c0.12,0.22,0.37,0.29,0.59,0.22l2.39-0.96c0.5,0.38,1.03,0.7,1.62,0.94l0.36,2.54 c0.05,0.24,0.24,0.41,0.48,0.41h3.84c0.24,0,0.43-0.17,0.47-0.41l0.36-2.54c0.59-0.24,1.13-0.56,1.62-0.94l2.39,0.96 c0.22,0.08,0.47,0,0.59-0.22l1.92-3.32c0.12-0.22,0.07-0.49-0.12-0.61L19.14,12.94z M12,15.6c-1.98,0-3.6-1.62-3.6-3.6 s1.62-3.6,3.6-3.6s3.6,1.62,3.6,3.6S13.98,15.6,12,15.6z"/></svg>
		                    <?php echo esc_html(__('System Status', 'majestic-support')); ?>
		                </h3>
		            </div>
		            <div class="ms-sys-info">
		                <div class="ms-sys-row">
		                    <span class="ms-sys-label"><?php echo esc_html(__('WP Version', 'majestic-support')); ?></span>
		                    <span class="ms-sys-val"><div class="ms-status-dot good"></div> <?php echo esc_html($wp_version); ?></span>
		                </div>
		                <div class="ms-sys-row">
		                    <span class="ms-sys-label"><?php echo esc_html(__('PHP Version', 'majestic-support')); ?></span>
		                    <span class="ms-sys-val"><div class="ms-status-dot good"></div> <?php echo esc_html(phpversion()); ?></span>
		                </div>
		                <div class="ms-sys-row">
		                    <span class="ms-sys-label"><?php echo esc_html(__('Max Upload Size', 'majestic-support')); ?></span>
		                    <span class="ms-sys-val"><div class="ms-status-dot warn"></div> <?php echo esc_html(size_format( wp_max_upload_size() )); ?></span>
		                </div>
		                <div class="ms-sys-row">
		                    <span class="ms-sys-label"><?php echo esc_html(__('PHP Memory Limit', 'majestic-support')); ?></span>
		                    <span class="ms-sys-val"><div class="ms-status-dot good"></div> <?php echo esc_html(ini_get( 'memory_limit' )); ?></span>
		                </div>
		                <div class="ms-sys-row">
		                    <span class="ms-sys-label"><?php echo esc_html(__('Database Status', 'majestic-support')); ?></span>
		                    <span class="ms-sys-val"><div class="ms-status-dot good"></div>
                            <?php
                            global $wpdb;

                            if ( $wpdb->check_connection() ) {
                                echo esc_html(__('Connected', 'majestic-support'));
                            } else {
                                echo esc_html(__('Disconnected', 'majestic-support'));
                            } ?></span>
		                </div>
		            </div>
		        </div>
		    </div>
		</div>
	</div>
</div>
