START TRANSACTION;


INSERT INTO `fields` VALUES (NULL, 'tabInvoices', 'backend', 'Label / Invoices', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Invoices', 'script');

INSERT INTO `fields` VALUES (NULL, 'booking_create_invoice', 'backend', 'Label / Create invoice', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', '+ Create invoice', 'script');

INSERT INTO `fields` VALUES (NULL, 'booking_return_on', 'backend', 'Label / Return on', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Return on', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_invoice_booking_details', 'backend', 'Label / Booking details', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Booking details', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_invoice_extras', 'backend', 'Label / Extras', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Extras', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_invoice_credit_card_fee', 'backend', 'Label / Extras', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Credit card fee:', 'script');


COMMIT;