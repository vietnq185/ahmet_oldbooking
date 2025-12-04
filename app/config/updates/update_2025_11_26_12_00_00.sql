START TRANSACTION;

ALTER TABLE `bookings` ADD COLUMN (
	`pickup_lat` float(10,6) DEFAULT NULL,                      
 	`pickup_lng` float(10,6) DEFAULT NULL,
	`dropoff_lat` float(10,6) DEFAULT NULL,                      
 	`dropoff_lng` float(10,6) DEFAULT NULL
);

INSERT INTO `options` (`foreign_id`, `key`, `tab_id`, `value`, `label`, `type`, `order`, `is_visible`, `style`) VALUES
(1, 'o_google_api_key', 1, NULL, NULL, 'string', 19, 1, NULL);

INSERT INTO `fields` VALUES (NULL, 'opt_o_google_api_key', 'backend', 'Options / Google API key', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Google API key', 'script');


COMMIT;