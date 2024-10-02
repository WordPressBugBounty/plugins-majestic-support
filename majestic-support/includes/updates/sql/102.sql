REPLACE INTO `#__mjtc_support_config` (`configname`, `configvalue`, `configfor`) VALUES ('versioncode','1.0.2','default');
REPLACE INTO `#__mjtc_support_config` (`configname`, `configvalue`, `configfor`) VALUES ('productversion','102','default');

ALTER TABLE `#__mjtc_support_tickets` ADD `token` VARCHAR(1000) DEFAULT NULL AFTER `internalid`; 


