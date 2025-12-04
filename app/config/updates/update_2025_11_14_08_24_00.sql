START TRANSACTION;

ALTER TABLE `plugin_invoice_config` ADD COLUMN (
	`y_tax_number` varchar(255) DEFAULT NULL,
	`y_bank_name` varchar(255) DEFAULT NULL,
	`y_iban` varchar(255) DEFAULT NULL,
	`y_bic` varchar(255) DEFAULT NULL
);

ALTER TABLE `plugin_invoice` ADD COLUMN `b_tax_number` varchar(255) DEFAULT NULL;

DROP TABLE IF EXISTS `plugin_invoice_taxes`;
CREATE TABLE IF NOT EXISTS `plugin_invoice_taxes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tax` decimal(9,2) unsigned DEFAULT NULL,
  `is_default` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `plugin_invoice_items` ADD COLUMN `tax_id` int(10) DEFAULT NULL;



INSERT INTO `fields` VALUES (NULL, 'lblInvoiceTaxName', 'backend', 'Label / Name', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Name', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblInvoiceTax', 'backend', 'Label / Tax', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Tax', 'script');

INSERT INTO `fields` VALUES (NULL, 'btnAddTax', 'backend', 'Label / + Add tax', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', '+ Add tax', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblInvoiceTaxNumber', 'backend', 'Label / Tax number', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Tax number', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblInvoiceBankName', 'backend', 'Label / Bank name', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Bank name', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblInvoiceIban', 'backend', 'Label / IBAN', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'IBAN', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblInvoiceBic', 'backend', 'Label / BIC', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'BIC', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblInvoiceTaxes', 'backend', 'Label / Taxes', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Taxes', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblInvoiceTaxIsDefault', 'backend', 'Label / Is default?', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Is default?', 'script');


COMMIT;