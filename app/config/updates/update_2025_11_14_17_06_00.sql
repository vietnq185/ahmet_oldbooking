START TRANSACTION;

ALTER TABLE `plugin_invoice_config` ADD COLUMN `y_company_reg_no` varchar(255) DEFAULT NULL;

INSERT INTO `fields` VALUES (NULL, 'lblInvoiceCompanyRegNo', 'backend', 'Label / Company Reg. No.', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Company Reg. No.', 'script');


COMMIT;