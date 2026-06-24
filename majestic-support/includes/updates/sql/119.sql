REPLACE INTO `#__mjtc_support_config` (`configname`, `configvalue`, `configfor`) VALUES ('versioncode','1.1.9','default');
REPLACE INTO `#__mjtc_support_config` (`configname`, `configvalue`, `configfor`) VALUES ('productversion','119','default');


ALTER TABLE `#__mjtc_support_tickets` ADD `sentiment` varchar(50) DEFAULT NULL;
ALTER TABLE `#__mjtc_support_tickets` ADD `upsell_opportunity` tinyint(1) NOT NULL DEFAULT '0';


ALTER TABLE `#__mjtc_support_replies` ADD `is_ai_draft` tinyint(1) DEFAULT '0';

INSERT INTO `#__mjtc_support_config` (`configname`, `configvalue`, `configfor`) VALUES 
('zywrap_enable_sentiment', '1', 'default'),
('zywrap_auto_route', '1', 'default'),
('zywrap_auto_priority', '1', 'default'),
('zywrap_detect_sales', '1', 'default'),
('zywrap_enable_policy', '1', 'default'),
('zywrap_policy_url', '', 'default'),
('zywrap_enable_followup', '1', 'default'),
('zywrap_enable_deflection', '1', 'default'),
('zywrap_deflection_sources', '2', 'default'),
('zywrap_deflection_tone', 'friendly and direct', 'default'),
('zywrap_deflection_strictness', 'strict', 'default');
INSERT INTO `#__mjtc_support_config` (`configname`, `configvalue`, `configfor`, `addon`) VALUES ('autocleanup_attachment_interval', '0', 'autocleanup', 'autocleanup');
INSERT INTO `#__mjtc_support_config` (`configname`, `configvalue`, `configfor`, `addon`) VALUES ('autocleanup_ticket_interval', '0', 'autocleanup', 'autocleanup');
INSERT INTO `#__mjtc_support_config` (`configname`, `configvalue`, `configfor`, `addon`) VALUES ('autocleanup_cron_frequency', 'daily', 'autocleanup', 'autocleanup');



CREATE TABLE IF NOT EXISTS `#__mjtc_support_zywrap_categories` (
  `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `status` tinyint(1) DEFAULT '1',
  `ordering` int(11) DEFAULT NULL,
  PRIMARY KEY (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__mjtc_support_zywrap_ai_models` (
  `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `status` tinyint(1) DEFAULT '1',
  `ordering` int(11) DEFAULT NULL,
  PRIMARY KEY (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__mjtc_support_zywrap_languages` (
  `code` varchar(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  `status` tinyint(1) DEFAULT '1',
  `ordering` int(11) DEFAULT NULL,
  PRIMARY KEY (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__mjtc_support_zywrap_use_cases` (
  `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `category_code` varchar(255) DEFAULT NULL,
  `schema_data` longtext,
  `status` tinyint(1) DEFAULT '1',
  `ordering` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`code`),
  KEY `category_code` (`category_code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__mjtc_support_zywrap_wrappers` (
  `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `use_case_code` varchar(255) DEFAULT NULL,
  `featured` tinyint(1) DEFAULT '0',
  `base` tinyint(1) DEFAULT '0',
  `status` tinyint(1) DEFAULT '1',
  `ordering` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`code`),
  KEY `use_case_code` (`use_case_code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__mjtc_support_zywrap_block_templates` (
  `type` varchar(50) NOT NULL,
  `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `status` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`type`,`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__mjtc_support_zywrap_settings` (
  `setting_key` varchar(255) NOT NULL,
  `setting_value` text,
  PRIMARY KEY (`setting_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__mjtc_support_zywrap_usage_logs` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `trace_id` varchar(255) DEFAULT NULL,
  `wrapper_code` varchar(255) DEFAULT NULL,
  `model_code` varchar(255) DEFAULT NULL,
  `prompt_tokens` int(11) DEFAULT '0',
  `completion_tokens` int(11) DEFAULT '0',
  `total_tokens` int(11) DEFAULT '0',
  `credits_used` bigint(20) DEFAULT '0',
  `latency_ms` int(11) DEFAULT '0',
  `status` varchar(50) DEFAULT 'success',
  `error_message` text,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `wrapper_idx` (`wrapper_code`),
  KEY `model_idx` (`model_code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


UPDATE `#__mjtc_support_config` 
SET `configfor` = 'instantfix', `addon` = 'instantfix' 
WHERE `configname` = 'enable_instant_fixes';

UPDATE `#__mjtc_support_config` 
SET `configfor` = 'instantfix', `addon` = 'instantfix' 
WHERE `configname` = 'instant_fixes_min_score';

UPDATE `#__mjtc_support_config` 
SET `configfor` = 'instantfix', `addon` = 'instantfix' 
WHERE `configname` = 'instant_fixes_limit';
