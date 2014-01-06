
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- Photo
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `Photo`;

CREATE TABLE `Photo`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `uri` VARCHAR(250) NOT NULL,
    `name` VARCHAR(50) NOT NULL,
    `owner_id` INTEGER NOT NULL,
    `created_at` DATETIME NOT NULL,
    `updated_at` DATETIME NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `owner_id` (`owner_id`),
    INDEX `created_at` (`created_at`),
    CONSTRAINT `PhotoOwner`
        FOREIGN KEY (`owner_id`)
        REFERENCES `User` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- PhotoRating
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `PhotoRating`;

CREATE TABLE `PhotoRating`
(
    `photo_id` INTEGER NOT NULL,
    `plus` INTEGER(10) NOT NULL,
    `minus` INTEGER(10) NOT NULL,
    PRIMARY KEY (`photo_id`),
    INDEX `photo_id` (`photo_id`),
    CONSTRAINT `Rating`
        FOREIGN KEY (`photo_id`)
        REFERENCES `Photo` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- PhotoComment
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `PhotoComment`;

CREATE TABLE `PhotoComment`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `user_id` INTEGER NOT NULL,
    `photo_id` INTEGER NOT NULL,
    `text` VARCHAR(500) NOT NULL,
    `created_at` DATETIME NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `user_id` (`user_id`),
    INDEX `photo_id` (`photo_id`),
    CONSTRAINT `PhotoComment_User`
        FOREIGN KEY (`user_id`)
        REFERENCES `User` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT `PhotoComment_Photo`
        FOREIGN KEY (`photo_id`)
        REFERENCES `Photo` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- UserRate
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `UserRate`;

CREATE TABLE `UserRate`
(
    `user_id` INTEGER NOT NULL,
    `photo_id` INTEGER NOT NULL,
    PRIMARY KEY (`user_id`,`photo_id`),
    INDEX `userid` (`user_id`),
    INDEX `photo_id` (`photo_id`),
    CONSTRAINT `UserRate`
        FOREIGN KEY (`photo_id`)
        REFERENCES `PhotoRating` (`photo_id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT `UserRate2`
        FOREIGN KEY (`user_id`)
        REFERENCES `User` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- User
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `User`;

CREATE TABLE `User`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `uri` VARCHAR(250) NOT NULL,
    `username` VARCHAR(50) NOT NULL,
    `email` VARCHAR(150) NOT NULL,
    `password` VARCHAR(130),
    `registered_at` DATETIME NOT NULL,
    `updated_at` DATETIME NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `username` (`username`),
    UNIQUE INDEX `uri` (`uri`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- UserSettings
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `UserSettings`;

CREATE TABLE `UserSettings`
(
    `user_id` INTEGER NOT NULL,
    `settings` TEXT NOT NULL,
    `updated_at` DATETIME NOT NULL,
    PRIMARY KEY (`user_id`),
    CONSTRAINT `Settings_ibfk_1`
        FOREIGN KEY (`user_id`)
        REFERENCES `User` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- UserRole
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `UserRole`;

CREATE TABLE `UserRole`
(
    `user_id` INTEGER NOT NULL,
    `role_id` INTEGER NOT NULL,
    PRIMARY KEY (`user_id`,`role_id`),
    INDEX `role_id` (`role_id`),
    CONSTRAINT `UserRole_FK_1`
        FOREIGN KEY (`user_id`)
        REFERENCES `User` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT `UserRole_FK_2`
        FOREIGN KEY (`role_id`)
        REFERENCES `Role` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Role
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `Role`;

CREATE TABLE `Role`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(50) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
