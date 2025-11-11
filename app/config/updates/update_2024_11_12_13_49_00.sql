START TRANSACTION;

UPDATE `options` SET `order`=21 WHERE `key`='o_allow_bank';
UPDATE `options` SET `order`=22 WHERE `key`='o_bank_account';


ALTER TABLE `bookings` ADD COLUMN `credit_card_fee` decimal(9,2) DEFAULT NULL AFTER `voucher_code`;

INSERT INTO `options` (`foreign_id`, `key`, `tab_id`, `value`, `label`, `type`, `order`, `is_visible`, `style`) VALUES
  (1, 'o_creditcard_later_fee', 2, NULL, NULL, 'float', 20, 1, NULL),
  (1, 'o_saferpay_fee', 2, NULL, NULL, 'float', 28, 1, NULL);
  
  
INSERT INTO `fields` VALUES (NULL, 'opt_o_creditcard_later_fee', 'backend', 'Options / Credit card fee', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Credit card fee x%', 'script');
  
INSERT INTO `fields` VALUES (NULL, 'opt_o_saferpay_fee', 'backend', 'Options / Credit card fee', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Credit card fee x%', 'script');
  
INSERT INTO `fields` VALUES (NULL, 'front_credit_card_fee', 'frontend', 'Label / Credit card fee', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Credit card fee %s: %s', 'script');
  
INSERT INTO `fields` VALUES (NULL, 'lblCreditCardFee', 'backend', 'Label / Credit card fee', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Credit card fee', 'script');
  
INSERT INTO `fields` VALUES (NULL, 'front_select_transfer_date_title', 'frontend', 'Label / Please select transfer date', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Please select transfer date', 'script');
  
INSERT INTO `fields` VALUES (NULL, 'front_select_return_transfer_date_title', 'frontend', 'Label / Please select return transfer date', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Please select return transfer date', 'script');
  
INSERT INTO `fields` VALUES (NULL, 'front_select_time_of_arrival_title', 'frontend', 'Label / Please select time of arrival', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Please select time of arrival', 'script');
  
INSERT INTO `fields` VALUES (NULL, 'front_select_pickup_time_title', 'frontend', 'Label / Please select pickup time', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Please select pickup time', 'script');
  
INSERT INTO `fields` VALUES (NULL, 'front_select_flight_departure_time_title', 'frontend', 'Label / Please select flight departure time', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Please select flight departure time', 'script');
  
INSERT INTO `fields` VALUES (NULL, 'front_now_to_pay', 'frontend', 'Label / Now to pay', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Now to pay', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_rest_to_pay', 'frontend', 'Label / Rest to pay', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Rest to pay', 'script');
  
INSERT INTO `fields` VALUES (NULL, 'front_total_price_all_inclusive', 'frontend', 'Label / Total price', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Total price (all inclusive)', 'script');
  


COMMIT;