REPLACE INTO `#__mjtc_support_config` (`configname`, `configvalue`, `configfor`) VALUES ('versioncode','1.0.8','default');
REPLACE INTO `#__mjtc_support_config` (`configname`, `configvalue`, `configfor`) VALUES ('productversion','108','default');



INSERT INTO `#__mjtc_support_config` (`configname`, `configvalue`, `configfor`) 
VALUES('show_read_receipt_to_user_on_reply','2','ticket'), 
('show_read_receipt_to_agent_on_reply','2','ticket'), 
('show_read_receipt_to_admin_on_reply','2','ticket');

ALTER TABLE `#__mjtc_support_replies` ADD `viewed_by` int(11) DEFAULT NULL;
ALTER TABLE `#__mjtc_support_replies` ADD `viewed_on` datetime DEFAULT NULL;
