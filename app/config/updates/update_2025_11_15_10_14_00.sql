START TRANSACTION;

ALTER TABLE `plugin_invoice` ADD COLUMN `voucher_code` varchar(255) DEFAULT NULL;


COMMIT;