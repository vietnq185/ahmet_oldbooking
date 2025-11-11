START TRANSACTION;

ALTER TABLE `bookings` ADD COLUMN `pickup_is_airport` tinyint(1) DEFAULT '0';
ALTER TABLE `bookings` ADD COLUMN `dropoff_is_airport` tinyint(1) DEFAULT '0';


COMMIT;