-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 14, 2025 at 10:41 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT = @@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS = @@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION = @@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `wandermate`
--

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders`
(
    `id`             int(11)  NOT NULL,
    `booking_date`   datetime NOT NULL DEFAULT current_timestamp(),
    `departure_date` datetime NOT NULL,
    `amount`         int(11)  NOT NULL,
    `request`        varchar(100)      DEFAULT NULL,
    `customer_id`    int(11)           DEFAULT NULL,
    `package_id`     int(11)  NOT NULL,
    `status_id`      int(11)           DEFAULT NULL,
    `itinerary_url`  varchar(100)      DEFAULT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `booking_date`, `departure_date`, `amount`, `request`, `customer_id`, `package_id`,
                      `status_id`, `itinerary_url`)
VALUES (1, '2025-06-30 10:00:00', '2025-07-01 09:00:00', 2, 'Vegetarian meal', 1, 1001, 2, 'itinerary-1001.txt'),
       (2, '2025-07-04 11:00:00', '2025-07-05 08:00:00', 1, NULL, 2, 1002, 1, NULL),
       (3, '2025-07-09 12:00:00', '2025-07-10 10:00:00', 3, 'Extra bed', 1, 1003, NULL, ''),
       (4, '2025-07-14 13:00:00', '2025-07-15 07:00:00', 2, NULL, 2, 1004, 2, 'itinerary-1004.txt'),
       (5, '2025-07-19 14:00:00', '2025-07-20 09:00:00', 1, 'Window seat', 1, 1005, 1, NULL),
       (6, '2025-07-24 15:00:00', '2025-07-25 08:00:00', 2, NULL, 2, 1006, 2, 'itinerary-1006.txt'),
       (7, '2025-07-29 16:00:00', '2025-07-30 10:00:00', 1, NULL, 1, 1007, NULL, ''),
       (8, '2025-07-31 17:00:00', '2025-08-01 09:00:00', 2, 'Late check-in', 2, 1008, NULL, NULL),
       (9, '2025-08-04 18:00:00', '2025-08-05 08:00:00', 1, NULL, 1, 1009, 2, 'itinerary-1009.txt'),
       (10, '2025-08-09 19:00:00', '2025-08-10 09:00:00', 3, 'Allergy info', 2, 1010, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `packages`
--

CREATE TABLE `packages`
(
    `id`             int(11)     NOT NULL,
    `name`           varchar(30) NOT NULL,
    `subtitle`       varchar(50) DEFAULT NULL,
    `price`          int(11)     NOT NULL,
    `group_size`     int(11)     NOT NULL,
    `duration`       varchar(20) NOT NULL,
    `start_location` varchar(50) NOT NULL,
    `end_location`   varchar(50) NOT NULL,
    `description`    text        DEFAULT NULL,
    `highlights`     text        DEFAULT NULL,
    `includes`       text        DEFAULT NULL,
    `excludes`       text        DEFAULT NULL,
    `itinerary`      text        DEFAULT NULL,
    `images`         text        DEFAULT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;

--
-- Dumping data for table `packages`
--

INSERT INTO `packages` (`id`, `name`, `subtitle`, `price`, `group_size`, `duration`, `start_location`, `end_location`,
                        `description`, `highlights`, `includes`, `excludes`, `itinerary`, `images`)
VALUES (1, 'Bali Adventure', 'Explore Bali', 4500, 10, '3 days', 'Denpasar', 'Ubud', 'A fun trip to Bali',
        'Beaches\r\nTemples\r\nRice Terraces', 'Guide\r\nMeals\r\nTransport', 'Flights\r\nPersonal expenses',
        'Day 1: Arrival | Arrive in Bali, check-in at hotel\r\nDay 2: Beach Tour | Visit Kuta and Seminyak beaches\r\nDay 3: Ubud Tour | Explore Ubud and rice terraces',
        'bali.jpg\r\n'),
       (2, 'Yogyakarta Heritage', 'Culture & History', 2560, 8, '2 days', 'Yogyakarta', 'Borobudur',
        'Discover Yogyakarta', 'Borobudur\nPrambanan\nMalioboro', 'Guide\nTickets\nBreakfast',
        'Lunch\nPersonal expenses',
        'Day 1: City Tour | Explore Yogyakarta city\nDay 2: Temple Tour | Visit Borobudur and Prambanan',
        'yogyakarta.jpg'),
       (3, 'Lombok Escape', 'Nature & Relax', 4800, 12, '3 days', 'Mataram', 'Gili Trawangan', 'Relax in Lombok',
        'Beaches\nSnorkeling\nWaterfalls', 'Hotel\nBreakfast\nGuide', 'Lunch\nDrinks',
        'Day 1: Beach Day | Relax at Senggigi Beach\nDay 2: Snorkeling | Snorkeling trip to Gili Islands\nDay 3: Waterfall Visit | Visit Sendang Gile waterfall',
        'lombok.jpg'),
       (4, 'Komodo Expedition', 'Wildlife Adventure', 10500, 15, '4 days', 'Labuan Bajo', 'Komodo Island',
        'See Komodo dragons', 'Komodo Island\nPink Beach\nPadar Island', 'Boat\nMeals\nGuide', 'Flights\nEntrance fees',
        'Day 1: Sailing | Depart from Labuan Bajo\nDay 2: Komodo Island | Trekking and dragon spotting\nDay 3: Pink Beach | Snorkeling and relaxing\nDay 4: Padar Island | Sunrise hike and return',
        'komodo.jpg'),
       (5, 'Bandung Culinary', 'Food & Fun', 1500, 6, '2 days', 'Bandung', 'Lembang', 'Taste Bandung',
        'Street Food\nMarkets\nCoffee Shops', 'Guide\nMeals\nTransport', 'Drinks\nPersonal expenses',
        'Day 1: Food Tour | Explore Bandung street food\nDay 2: Lembang | Visit Lembang and local markets',
        'bandung.jpg'),
       (6, 'Jakarta City Tour', 'Urban Experience', 2400, 20, '1 day', 'Jakarta', 'Jakarta', 'Explore Jakarta',
        'Monas\nKota Tua\nShopping Malls', 'Guide\nTickets\nTransport', 'Meals\nPersonal expenses',
        'Day 1: City Tour | Visit Monas, Kota Tua, and malls', 'jakarta.jpg'),
       (7, 'Raja Ampat Diving', 'Underwater Paradise', 9600, 8, '5 days', 'Sorong', 'Waisai', 'Dive in Raja Ampat',
        'Coral Reefs\nTropical Fish\nIsland Hopping', 'Diving\nMeals\nBoat', 'Flights\nEquipment rental',
        'Day 1: Arrival | Arrive in Sorong, transfer to Waisai\nDay 2: Diving | First day of diving\nDay 3: Island Hopping | Explore nearby islands\nDay 4: Diving | More diving sessions\nDay 5: Departure | Return to Sorong',
        'rajaampat.jpg'),
       (8, 'Sumatra Wildlife', 'Jungle Trek', 3500, 10, '3 days', 'Medan', 'Bukit Lawang', 'See orangutans',
        'Jungle Trek\nWildlife\nRiver Tubing', 'Guide\nMeals\nAccommodation', 'Drinks\nPersonal expenses',
        'Day 1: Trek Start | Begin jungle trek\nDay 2: Wildlife Spotting | Search for orangutans\nDay 3: River Tubing | Enjoy tubing and return',
        'sumatra.jpg'),
       (9, 'Bromo Sunrise', 'Mountain Adventure', 4480, 16, '2 days', 'Malang', 'Bromo', 'Climb Bromo',
        'Sunrise View\nJeep Tour\nCrater Hike', 'Guide\nTransport\nBreakfast', 'Meals\nPersonal expenses',
        'Day 1: Sunrise Tour | Early morning jeep to Bromo\nDay 2: Crater Hike | Hike to Bromo crater and return',
        'bromo.jpg'),
       (10, 'Labuan Bajo Leisure', 'Island Hopping', 8400, 14, '3 days', 'Labuan Bajo', 'Padar Island', 'Island hop',
        'Padar Island\nKanawa Island\nSnorkeling', 'Boat\nMeals\nGuide', 'Flights\nPersonal expenses',
        'Day 1: Island Tour | Visit Padar and Kanawa islands\nDay 2: Snorkeling | Snorkeling and beach activities\nDay 3: Return | Return to Labuan Bajo',
        'labuanbajo.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles`
(
    `id`   int(11)     NOT NULL,
    `name` varchar(20) NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`)
VALUES (1, 'customer'),
       (2, 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `statuses`
--

CREATE TABLE `statuses`
(
    `id`   int(11)     NOT NULL,
    `name` varchar(20) NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;

--
-- Dumping data for table `statuses`
--

INSERT INTO `statuses` (`id`, `name`)
VALUES (1, 'cancelled'),
       (2, 'confirmed');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users`
(
    `id`       int(11)      NOT NULL,
    `name`     varchar(200) NOT NULL,
    `email`    varchar(200) NOT NULL,
    `phone`    varchar(20) DEFAULT NULL,
    `password` varchar(100) NOT NULL,
    `role_id`  int(11)      NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `password`, `role_id`)
VALUES (1, 'Alice Smith', 'alice@example.com', '081234567890', 'password1', 1),
       (2, 'Bob Johnson', 'bob@example.com', '081234567891', 'password2', 1),
       (3, 'John Doe', 'john@example.com', '1234567890', 'password3', 1),
       (4, 'Charlie Admin', 'charlie@admin.com', '081234567892', 'adminpass', 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
    ADD PRIMARY KEY (`id`),
    ADD KEY `orders_FK1` (`status_id`),
    ADD KEY `orders_FK2` (`package_id`),
    ADD KEY `orders_FK3` (`customer_id`);

--
-- Indexes for table `packages`
--
ALTER TABLE `packages`
    ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
    ADD PRIMARY KEY (`id`);

--
-- Indexes for table `statuses`
--
ALTER TABLE `statuses`
    ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
    ADD PRIMARY KEY (`id`),
    ADD UNIQUE KEY `users_IX1` (`email`),
    ADD KEY `users_FK1` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,
    AUTO_INCREMENT = 11;

--
-- AUTO_INCREMENT for table `packages`
--
ALTER TABLE `packages`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,
    AUTO_INCREMENT = 11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,
    AUTO_INCREMENT = 5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
    ADD CONSTRAINT `orders_FK1` FOREIGN KEY (`status_id`) REFERENCES `statuses` (`id`) ON UPDATE SET NULL,
    ADD CONSTRAINT `orders_FK2` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`) ON UPDATE CASCADE,
    ADD CONSTRAINT `orders_FK3` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
    ADD CONSTRAINT `users_FK1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT = @OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS = @OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION = @OLD_COLLATION_CONNECTION */;
