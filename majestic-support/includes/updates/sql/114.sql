REPLACE INTO `#__mjtc_support_config` (`configname`, `configvalue`, `configfor`) VALUES ('versioncode','1.1.4','default');
REPLACE INTO `#__mjtc_support_config` (`configname`, `configvalue`, `configfor`) VALUES ('productversion','114','default');

INSERT INTO `#__mjtc_support_config` (`configname`, `configvalue`, `configfor`, `addon`) 
VALUES('tplink_profile_user','1','tplink', NULL), 
('tplink_profile_staff','1','tplink', 'agent'),
('cplink_quick_actions_staff', '1', 'cplink', NULL),
('cplink_ticket_departments_staff', '1', 'cplink', NULL),
('cplink_recent_activity_staff', '1', 'cplink', NULL),
('cplink_daily_velocity_staff', '1', 'cplink', NULL),
('cplink_weekly_leaderboard_staff', '1', 'cplink', NULL),
('cplink_vip_clients_watchlist_staff', '1', 'cplink', 'woocommerce'),
('cplink_recent_feedback_staff', '1', 'cplink', 'feedback'),
('cplink_quick_actions_user', '1', 'cplink', NULL),
('cplink_ticket_departments_user', '1', 'cplink', NULL),
('cplink_recent_activity_user', '1', 'cplink', NULL),
('cplink_daily_velocity_user', '1', 'cplink', NULL),
('cplink_latestdownloads_staff', '1', 'cplink', 'download'),
('cplink_latestannouncements_staff', '1', 'cplink', 'announcement'),
('cplink_latestkb_staff', '1', 'cplink', 'knowledgebase'),
('cplink_latestfaqs_staff', '1', 'cplink', 'faq'),
('ticket_close_reason_type', '1', 'ticket', 'ticketclosereason');
UPDATE `#__options` SET `option_value` = '{"color1":"#291abc","color2":"#0f172a","color3":"#f3f4f6","color4":"#6c7381","color5":"#94a3b8","color6":"#e7e7e7","color7":"#ffffff","color8":"#2da1cb","form_request":"majesticsupport"}' WHERE `option_name` = 'ms_set_theme_colors';


UPDATE `#__mjtc_support_statuses` SET `statuscolour` = '#047857', `statusbgcolour` = '#a7f3d0' WHERE `id` = 1;
UPDATE `#__mjtc_support_statuses` SET `statuscolour` = '#b45309', `statusbgcolour` = '#fde68a' WHERE `id` = 2;
UPDATE `#__mjtc_support_statuses` SET `statuscolour` = '#3b82f6', `statusbgcolour` = '#edf2fd' WHERE `id` = 3;
UPDATE `#__mjtc_support_statuses` SET `statusbgcolour` = '#d6eef4' WHERE `id` = 4;