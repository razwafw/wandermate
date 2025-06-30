-- Project Name : WanderMate
-- Date/Time    : 6/25/2025 3:47:49 PM
-- Author       : root
-- RDBMS Type   : MySQL
-- Application  : A5:SQL Mk-2

CREATE TABLE `statuses`
(
    `id`   INT         NOT NULL,
    `name` VARCHAR(20) NOT NULL,
    CONSTRAINT `statuses_PKC` PRIMARY KEY (`id`)
);

CREATE TABLE `orders`
(
    `id`             INT AUTO_INCREMENT NOT NULL,
    `booking_date`   TIME               NOT NULL,
    `departure_date` TIME               NOT NULL,
    `amount`         INT                NOT NULL,
    `request`        VARCHAR(100),
    `customer_id`    INT,
    `package_id`     INT                NOT NULL,
    `status_id`      INT,
    `itinerary_url`  VARCHAR(100),
    CONSTRAINT `orders_PKC` PRIMARY KEY (`id`)
);

CREATE TABLE `images`
(
    `id`         INT AUTO_INCREMENT NOT NULL,
    `package_id` INT                NOT NULL,
    `url`        VARCHAR(100)       NOT NULL,
    CONSTRAINT `images_PKC` PRIMARY KEY (`id`)
);

CREATE TABLE `packages`
(
    `id`             INT AUTO_INCREMENT NOT NULL,
    `name`           VARCHAR(30)        NOT NULL,
    `subtitle`       VARCHAR(50),
    `price`          INT                NOT NULL,
    `duration`       VARCHAR(20)        NOT NULL,
    `location`       VARCHAR(50)        NOT NULL,
    `start_location` VARCHAR(50)        NOT NULL,
    `end_location`   VARCHAR(50)        NOT NULL,
    `accomodation`   VARCHAR(50),
    `description`    TEXT,
    `highlights`     TEXT,
    `includes`       TEXT,
    `excludes`       TEXT,
    `itinerary`      TEXT,
    CONSTRAINT `packages_PKC` PRIMARY KEY (`id`)
);

CREATE TABLE `roles`
(
    `id`   INT         NOT NULL,
    `name` VARCHAR(20) NOT NULL,
    CONSTRAINT `roles_PKC` PRIMARY KEY (`id`)
);

CREATE TABLE `users`
(
    `id`       INT AUTO_INCREMENT NOT NULL,
    `name`     VARCHAR(200)       NOT NULL,
    `email`    VARCHAR(200)       NOT NULL,
    `phone`    VARCHAR(20),
    `password` VARCHAR(100)       NOT NULL,
    `role_id`  INT                NOT NULL,
    CONSTRAINT `users_PKC` PRIMARY KEY (`id`)
);

CREATE UNIQUE INDEX `users_IX1`
    ON `users` (`email`);

ALTER TABLE `orders`
    ADD CONSTRAINT `orders_FK1` FOREIGN KEY (`status_id`) REFERENCES `statuses` (`id`)
        ON DELETE RESTRICT
        ON UPDATE SET NULL;

ALTER TABLE `orders`
    ADD CONSTRAINT `orders_FK2` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`)
        ON DELETE RESTRICT
        ON UPDATE CASCADE;

ALTER TABLE `orders`
    ADD CONSTRAINT `orders_FK3` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`)
        ON DELETE SET NULL
        ON UPDATE CASCADE;

ALTER TABLE `images`
    ADD CONSTRAINT `images_FK1` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE;

ALTER TABLE `users`
    ADD CONSTRAINT `users_FK1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
        ON DELETE RESTRICT
        ON UPDATE CASCADE;

