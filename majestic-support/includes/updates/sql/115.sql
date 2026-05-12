REPLACE INTO `#__mjtc_support_config` (`configname`, `configvalue`, `configfor`) VALUES ('versioncode','1.1.5','default');
REPLACE INTO `#__mjtc_support_config` (`configname`, `configvalue`, `configfor`) VALUES ('productversion','115','default');

INSERT INTO `#__mjtc_support_config` (`configname`, `configvalue`, `configfor`) 
VALUES 	('enable_instant_fixes', '1', 'default'),
		('instant_fixes_min_score', '0.5', 'default'),
		('instant_fixes_limit', '2', 'default');
UPDATE `#__mjtc_support_statuses` SET `statusbgcolour` = '#7ed7fb' WHERE `id` = 4;
