START TRANSACTION;

ALTER TABLE `bookings` ADD COLUMN (
	`pickup_address` varchar(255) DEFAULT NULL,
	`dropoff_address` varchar(255) DEFAULT NULL
);

ALTER TABLE `bookings` ADD COLUMN (
	`duration` int(10) DEFAULT NULL,
	`distance` int(10) DEFAULT NULL 
);


ALTER TABLE `locations` ADD COLUMN (
	`address` varchar(255) DEFAULT NULL,
	`lat` float(10,6) DEFAULT NULL,                      
 	`lng` float(10,6) DEFAULT NULL
);


ALTER TABLE `dropoff` ADD COLUMN (
	`address` varchar(255) DEFAULT NULL,
	`lat` float(10,6) DEFAULT NULL,                      
 	`lng` float(10,6) DEFAULT NULL
);


COMMIT;