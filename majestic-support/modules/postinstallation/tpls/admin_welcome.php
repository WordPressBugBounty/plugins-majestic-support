<?php
if (!defined('ABSPATH')) die('Restricted Access');
$MJTC_yesno = array(
    (object) array('id' => '1', 'text' => esc_html(__('Yes', 'majestic-support'))),
    (object) array('id' => '2', 'text' => esc_html(__('No', 'majestic-support')))
    );
$MJTC_showhide = array(
    (object) array('id' => '1', 'text' => esc_html(__('Yes', 'majestic-support'))),
    (object) array('id' => '0', 'text' => esc_html(__('No', 'majestic-support')))
    );
$MJTC_date_format = array(
    (object) array('id' => 'd-m-Y', 'text' => esc_html(__('DD-MM-YYYY' , 'majestic-support'))),
    (object) array('id' => 'm-d-Y', 'text' => esc_html(__('MM-DD-YYYY' , 'majestic-support'))),
    (object) array('id' => 'Y-m-d', 'text' => esc_html(__('YYYY-MM-DD' , 'majestic-support')))
    );
$MJTC_tran_opt = MJTC_includer::MJTC_getModel('majesticsupport')->getInstalledTranslationKey();
?>
<div id="ms-main-wrapper">
    <div class="ms-onboarding-container">
        <!-- CELEBRATION HERO -->
        <div class="ms-welcome-hero">
            <div class="ms-success-icon-wrap">
                <svg viewBox="0 0 24 24" width="48" height="48" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            </div>
            <h1 class="ms-welcome-title"><?php echo esc_attr(__('Plugin Activated Successfully!', 'majestic-support')); ?></h1>
            <p class="ms-welcome-desc"><?php echo esc_attr(__('Thank you for choosing Majestic. Your powerful, natively integrated WordPress support system is installed and ready to be configured.', 'majestic-support')); ?></p>
            
            <a href="admin.php?page=majesticsupport" class="mjtc-admin-btn mjtc-admin-btn-primary ms-dashboard-btn">
                <?php echo esc_attr(__('Go to Dashboard', 'majestic-support')); ?>
                <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
            </a>
        </div>

        <!-- NEXT STEPS SECTION -->
        <div class="ms-steps-wrapper">
            <div class="ms-steps-header">
                <h3><?php echo esc_attr(__('Recommended Next Steps', 'majestic-support')); ?></h3>
                <p><?php echo esc_attr(__('Follow these quick steps to get your support portal fully operational.', 'majestic-support')); ?></p>
            </div>
            <div class="ms-next-steps-grid">
                <!-- Step 1 -->
                <a href="?page=majesticsupport_postinstallation&mjslay=stepone" class="ms-step-card">
                    <div class="ms-step-icon">
                        <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                    </div>
                    <h4><?php echo esc_attr(__('Configure Settings', 'majestic-support')); ?></h4>
                    <p><?php echo esc_attr(__('Set up your email notifications, ticket prefixes, and general support rules.', 'majestic-support')); ?></p>
                    <span class="ms-step-link"><?php echo esc_attr(__('Settings', 'majestic-support')); ?> <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg></span>
                </a>

                <!-- Step 2 -->
                <a href="?page=majesticsupport_themes" class="ms-step-card">
                    <div class="ms-step-icon">
                        <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l-5.5 9h11L12 2zm0 3.84L13.93 9h-3.87L12 5.84zM17.5 13c-2.49 0-4.5 2.01-4.5 4.5s2.01 4.5 4.5 4.5 4.5-2.01 4.5-4.5-2.01-4.5-4.5-4.5zm0 7c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5zM3 21.5h8v-8H3v8zm2-6h4v4H5v-4z"/></svg>
                    </div>
                    <h4><?php echo esc_attr(__('Design Your Portal', 'majestic-support')); ?></h4>
                    <p><?php echo esc_attr(__('Apply preset themes or pick custom colors so the portal matches your branding.', 'majestic-support')); ?></p>
                    <span class="ms-step-link"><?php echo esc_attr(__('Customize Colors', 'majestic-support')); ?> <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg></span>
                </a>

                <!-- Step 3 -->
                <a href="?page=majesticsupport_department&mjslay=adddepartment" class="ms-step-card">
                    <div class="ms-step-icon">
                        <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"></rect><path d="M9 21V9h6v12"></path><path d="M9 3v6h6V3"></path></svg>
                    </div>
                    <h4><?php echo esc_attr(__('Add Departments', 'majestic-support')); ?></h4>
                    <p><?php echo esc_attr(__('Create specific departments (e.g. Sales, Billing) and assign agents to handle them.', 'majestic-support')); ?></p>
                    <span class="ms-step-link"><?php echo esc_attr(__('Manage Departments', 'majestic-support')); ?><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg></span>
                </a>

            </div>
        </div>

        <!-- HELP / RESOURCE FOOTER -->
        <div class="ms-help-footer">
            <div class="ms-help-text">
                <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                <div>
                    <h4><?php echo esc_attr(__('Need help getting started?', 'majestic-support')); ?></h4>
                    <p><?php echo esc_attr(__('Our comprehensive documentation covers everything from basic setup to advanced workflows.', 'majestic-support')); ?></p>
                </div>
            </div>
            <div class="ms-help-links">
                <a target="_blank" href="https://www.youtube.com/@Majestic-Support/videos" class="ms-help-link"><?php echo esc_attr(__('Video Tutorials', 'majestic-support')); ?></a>
            </div>
        </div>

    </div>
</div>
