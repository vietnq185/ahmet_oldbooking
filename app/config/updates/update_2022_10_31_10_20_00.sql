START TRANSACTION;

ALTER TABLE `bookings` MODIFY `payment_method` enum('paypal','authorize','saferpay','creditcard','creditcard_later','cash','bank') DEFAULT NULL;
ALTER TABLE `bookings_payments` MODIFY `payment_method` enum('paypal','authorize','saferpay','creditcard','creditcard_later','cash','bank') DEFAULT NULL;


INSERT INTO `options` (`foreign_id`, `key`, `tab_id`, `value`, `label`, `type`, `order`, `is_visible`, `style`) VALUES
(1, 'o_allow_saferpay', 2, 'Yes|No::No', NULL, 'enum', 22, 1, NULL),
(1, 'o_saferpay_username', 2, NULL, NULL, 'string', 23, 1, NULL),
(1, 'o_saferpay_password', 2, NULL, NULL, 'string', 24, 1, NULL),
(1, 'o_saferpay_customer_id', 2, NULL, NULL, 'string', 25, 1, NULL),
(1, 'o_saferpay_terminal_id', 2, NULL, NULL, 'string', 26, 1, NULL);

INSERT INTO `fields` VALUES (NULL, 'payment_methods_ARRAY_saferpay', 'arrays', 'payment_methods_ARRAY_saferpay', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'PaySafe', 'script');

INSERT INTO `fields` VALUES (NULL, 'payment_methods_desc_ARRAY_saferpay', 'arrays', 'payment_methods_desc_ARRAY_saferpay', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'PaySafe', 'script');

INSERT INTO `fields` VALUES (NULL, 'opt_o_allow_saferpay', 'backend', 'Options / Allow payments with PaySafe', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Allow payments with PaySafe', 'script');

INSERT INTO `fields` VALUES (NULL, 'opt_o_saferpay_username', 'backend', 'Options / Username', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Username', 'script');

INSERT INTO `fields` VALUES (NULL, 'opt_o_saferpay_password', 'backend', 'Options / Password', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Password', 'script');

INSERT INTO `fields` VALUES (NULL, 'opt_o_saferpay_customer_id', 'backend', 'Options / Customer ID', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Customer ID', 'script');

INSERT INTO `fields` VALUES (NULL, 'opt_o_saferpay_terminal_id', 'backend', 'Options / Terminal ID', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Terminal ID', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_your_booking_completed', 'frontend', 'Label / Your reservation has been completed.', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Your reservation has been completed.', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_messages_ARRAY_6', 'arrays', 'front_messages_ARRAY_6', 'script', '2021-12-07 11:28:56');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'You will be taken to PaySafe Payment Gateway to process payment. Please wait...', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_messages_ARRAY_7', 'arrays', 'front_messages_ARRAY_7', 'script', '2021-12-07 11:28:56');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'There was a problem while redirecting to payment page.', 'script');

INSERT INTO `fields` VALUES (NULL, 'payment_methods_short_ARRAY_saferpay', 'arrays', 'payment_methods_short_ARRAY_saferpay', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'PaySafe', 'script');

SET @id1 := (SELECT `id` FROM `fields` WHERE `key` = "infoConfirmationDesc");
SET @id2 := (SELECT `id` FROM `fields` WHERE `key` = "infoConfirmation2Desc");
UPDATE `multi_lang` SET `content` = 'There are three type of auto-responders you can send to both the client and the admin. The first one is sent after new booking is submitted via the software. The second one is sent to confirm a successful payment and the third one after service has been canceled. You may enable or disable all auto-responders separately as well as customize the message using the tokens below.  <br/><br/><div class="float_left w400">{Title}<br/>{FirstName}<br/>{LastName}<br/>{Email}<br/>{Phone}<br/>{Country}<br/><br/>{UniqueID}<br/>{Date}<br/>{Time}<br/>{From}<br/>{To}<br/><br/>{Passengers}<br/>{Fleet}<br/>{Duration}<br/>{Distance}<br/>{Hotel}<br/>{Notes}<br/>{Extras}</div><div class="float_left w400"><b>[FromAirport]</b><br/>{FlightNumber}<br/>{AirlineCompany}<br/>{DestinationAddress}<br/><b>[/FromAirport]</b><br/><br/><b>[FromLocation]</b><br/>{Address}<br/>{FlightDepartureTime}<br/><b>[/FromLocation]</b><br/><br/><b>[FromLocationToLocation]</b><br/>{Address}<br/>{DropoffAddress}<br/><b>[/FromLocationToLocation]</b><br/><br/>{PriceFirstTransfer}<br/><b>[HasReturn]</b><br/>{ReturnDate}<br/>{ReturnTime}<br/>{ReturnFrom}<br/>{ReturnTo}<br/>{PriceReturnTransfer}<br/>{ReturnNotes}<br/>{PassengersReturn}<br/><b>[/HasReturn]</b><br/><br/><b>[ReturnToAirport]</b><br/>{ReturnAddress}<br/>{ReturnFlightDepartureTime}<br/><b>[/ReturnToAirport]</b><br/><br/><b>[ReturnToLocation]</b><br/>{ReturnFlightNumber}<br/>{ReturnAirlineCompany}<br/><b>[/ReturnToLocation]</b><br/><br/><b>[ReturnToLocationToLocation]</b><br/>{ReturnAddress}<br/>{ReturnDropoffAddress}<br/><b>[/ReturnToLocationToLocation]</b></div><div class="float_left w400">{PaymentMethod}<br/>{SubTotal}<br/>{Tax}<br/>{Total}<br/><br/><b>[HasDeposit]</b><br/>{Deposit}<br/>{Rest}<br/>{CCOwner}<br/>{CCNum}<br/>{CCExp}<br/>{CCSec}<br/><b>[/HasDeposit]</b><br/><br/><b>[HasDiscount]</b><br/>{DiscountCode}<br/>{Discount}<br/><b>[/HasDiscount]</b><br/><br/>{CancelURL}</div>' WHERE `foreign_id` IN (@id1, @id2) AND `model` = "pjField" AND `field` = "title";


INSERT INTO `fields` VALUES (NULL, 'front_messages_ARRAY_9', 'arrays', 'front_messages_ARRAY_9', 'script', '2021-12-07 11:28:56');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Failed to make the payment. Please check your credit card information, or choose another payment method and try again!', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_messages_ARRAY_10', 'arrays', 'front_messages_ARRAY_10', 'script', '2021-12-07 11:28:56');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Your booking has been successfully booked and paid!', 'script');

COMMIT;