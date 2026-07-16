-- ---------------------------------------------------------------------------
-- Matrimonial Hub — schema
--
-- Import with:  mysql -u root -p < database/schema.sql
--
-- Notable changes from the original `matrimonial.sql`:
--   * `chat_users` is gone. It duplicated every user's name, email and password
--     hash beside `User`, was never kept in sync, and messages pointed at it
--     instead of the real user table. Messages now reference `users` directly.
--   * `request_id` is a plain AUTO_INCREMENT. The old code generated a 20-char
--     hex string and bound it as an integer, so ~37% of requests collapsed to
--     id 0 and silently failed on the duplicate key.
--   * Admin passwords are bcrypt hashes, not plaintext.
--   * Every foreign key is declared, with ON DELETE CASCADE where a child row
--     is meaningless without its parent. ON UPDATE CASCADE is deliberately
--     absent: user_id is an immutable random key, so nothing can cascade, and
--     MariaDB rejects a CHECK constraint on any column carrying that cascade
--     (which chk_request_not_self below needs).
-- ---------------------------------------------------------------------------

CREATE DATABASE IF NOT EXISTS `matrimonial`
    DEFAULT CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `matrimonial`;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `messages`;
DROP TABLE IF EXISTS `connection_requests`;
DROP TABLE IF EXISTS `preferences`;
DROP TABLE IF EXISTS `profiles`;
DROP TABLE IF EXISTS `activity_log`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `admins`;
SET FOREIGN_KEY_CHECKS = 1;

-- ---------------------------------------------------------------- users ------

CREATE TABLE `users` (
    `user_id`        VARCHAR(12)  NOT NULL,
    `email`          VARCHAR(255) NOT NULL,
    `password_hash`  VARCHAR(255) NOT NULL,
    `first_name`     VARCHAR(50)  NOT NULL,
    `middle_name`    VARCHAR(50)      NULL DEFAULT NULL,
    `last_name`      VARCHAR(50)  NOT NULL,
    `dob`            DATE         NOT NULL,
    `gender`         ENUM('Male','Female','Other') NOT NULL,
    `religion`       ENUM('Muslim','Hindu','Christian','Buddhist','Jewish','Atheist','Other') NOT NULL,
    `ethnicity`      VARCHAR(50)  NOT NULL,
    `profession`     VARCHAR(100) NOT NULL,
    `nid`            VARCHAR(30)  NOT NULL,
    `photo`          VARCHAR(255)     NULL DEFAULT NULL,
    `account_status` ENUM('Active','Inactive','Suspended') NOT NULL DEFAULT 'Active',
    `last_seen_at`   DATETIME         NULL DEFAULT NULL,
    `created_at`     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`user_id`),
    UNIQUE KEY `uniq_users_email` (`email`),
    KEY `idx_users_status`  (`account_status`),
    KEY `idx_users_gender`  (`gender`),
    KEY `idx_users_dob`     (`dob`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------- profiles ------

CREATE TABLE `profiles` (
    `user_id`             VARCHAR(12) NOT NULL,
    `phone`               VARCHAR(30)      NULL DEFAULT NULL,
    `road_number`         VARCHAR(50)      NULL DEFAULT NULL,
    `street_number`       VARCHAR(50)      NULL DEFAULT NULL,
    `building_number`     VARCHAR(50)      NULL DEFAULT NULL,
    `secondary_education` VARCHAR(255)     NULL DEFAULT NULL,
    `higher_secondary`    VARCHAR(255)     NULL DEFAULT NULL,
    `undergraduate`       VARCHAR(50)      NULL DEFAULT NULL,
    `postgraduate`        VARCHAR(50)      NULL DEFAULT NULL,
    `marital_status`      ENUM('Single','Married','Divorced','Widowed') NOT NULL DEFAULT 'Single',
    `height_cm`           DECIMAL(5,2)     NULL DEFAULT NULL,
    `weight_kg`           DECIMAL(5,2)     NULL DEFAULT NULL,
    `complexion`          ENUM('Fair','Medium','Olive','Tan','Dark') NULL DEFAULT NULL,
    `interests`           TEXT             NULL,
    `hobbies`             TEXT             NULL,
    `biography`           TEXT             NULL,
    `family_background`   TEXT             NULL,
    `updated_at`          TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`user_id`),
    CONSTRAINT `fk_profiles_user` FOREIGN KEY (`user_id`)
        REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------- preferences ------
-- The original had a `Preferences` table that registration seeded with a
-- user_id and nothing else — no page ever read it. Here it is the input to the
-- match score.

CREATE TABLE `preferences` (
    `user_id`                  VARCHAR(12) NOT NULL,
    `preferred_gender`         ENUM('Male','Female','Other') NULL DEFAULT NULL,
    `preferred_religion`       ENUM('Muslim','Hindu','Christian','Buddhist','Jewish','Atheist','Other') NULL DEFAULT NULL,
    `preferred_ethnicity`      VARCHAR(50)  NULL DEFAULT NULL,
    `preferred_profession`     VARCHAR(100) NULL DEFAULT NULL,
    `preferred_marital_status` ENUM('Single','Married','Divorced','Widowed') NULL DEFAULT NULL,
    `preferred_education`      VARCHAR(50)  NULL DEFAULT NULL,
    `min_age`                  TINYINT UNSIGNED NULL DEFAULT NULL,
    `max_age`                  TINYINT UNSIGNED NULL DEFAULT NULL,
    `min_height_cm`            DECIMAL(5,2) NULL DEFAULT NULL,
    `max_height_cm`            DECIMAL(5,2) NULL DEFAULT NULL,
    `interests`                TEXT         NULL,
    `hobbies`                  TEXT         NULL,
    `updated_at`               TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`user_id`),
    CONSTRAINT `fk_preferences_user` FOREIGN KEY (`user_id`)
        REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------- connection_requests ------

CREATE TABLE `connection_requests` (
    `request_id`   INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `sender_id`    VARCHAR(12)  NOT NULL,
    `receiver_id`  VARCHAR(12)  NOT NULL,
    `status`       ENUM('Pending','Accepted','Declined','Cancelled') NOT NULL DEFAULT 'Pending',
    `message`      VARCHAR(500)     NULL DEFAULT NULL,
    `created_at`   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `responded_at` DATETIME         NULL DEFAULT NULL,
    PRIMARY KEY (`request_id`),
    -- One request per direction per pair; re-sending updates the existing row.
    UNIQUE KEY `uniq_request_pair` (`sender_id`, `receiver_id`),
    KEY `idx_request_receiver` (`receiver_id`, `status`),
    KEY `idx_request_sender`   (`sender_id`, `status`),
    CONSTRAINT `fk_request_sender` FOREIGN KEY (`sender_id`)
        REFERENCES `users` (`user_id`) ON DELETE CASCADE,
    CONSTRAINT `fk_request_receiver` FOREIGN KEY (`receiver_id`)
        REFERENCES `users` (`user_id`) ON DELETE CASCADE,
    -- Nobody connects with themselves.
    CONSTRAINT `chk_request_not_self` CHECK (`sender_id` <> `receiver_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------- messages ------

CREATE TABLE `messages` (
    `message_id`  INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `sender_id`   VARCHAR(12)  NOT NULL,
    `receiver_id` VARCHAR(12)  NOT NULL,
    `body`        VARCHAR(2000) NOT NULL,
    `read_at`     DATETIME         NULL DEFAULT NULL,
    `created_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`message_id`),
    -- Serves the "conversation since message N" query the chat polls on.
    KEY `idx_messages_thread` (`sender_id`, `receiver_id`, `message_id`),
    KEY `idx_messages_unread` (`receiver_id`, `read_at`),
    CONSTRAINT `fk_messages_sender` FOREIGN KEY (`sender_id`)
        REFERENCES `users` (`user_id`) ON DELETE CASCADE,
    CONSTRAINT `fk_messages_receiver` FOREIGN KEY (`receiver_id`)
        REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------- admins ------

CREATE TABLE `admins` (
    `admin_id`      INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `username`      VARCHAR(100) NOT NULL,
    `password_hash` VARCHAR(255) NOT NULL,
    `created_at`    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`admin_id`),
    UNIQUE KEY `uniq_admins_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------- activity_log ------

CREATE TABLE `activity_log` (
    `log_id`     INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`    VARCHAR(12)      NULL DEFAULT NULL,
    `activity`   VARCHAR(255) NOT NULL,
    `ip_address` VARCHAR(45)      NULL DEFAULT NULL,
    `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`log_id`),
    KEY `idx_activity_user` (`user_id`, `created_at`),
    -- Keep the audit trail when a user is deleted; just null the reference.
    CONSTRAINT `fk_activity_user` FOREIGN KEY (`user_id`)
        REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
