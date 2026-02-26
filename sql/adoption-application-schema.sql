-- Add columns for adoption application form
-- These columns store applicant information when submitting adoption requests

ALTER TABLE `adoptions` ADD COLUMN `animal_id` INT(11) AFTER `id`;
ALTER TABLE `adoptions` ADD COLUMN `email` VARCHAR(100) AFTER `applicant_name`;
ALTER TABLE `adoptions` ADD COLUMN `phone` VARCHAR(20) AFTER `email`;
ALTER TABLE `adoptions` ADD COLUMN `address` VARCHAR(255) AFTER `phone`;
ALTER TABLE `adoptions` ADD COLUMN `city` VARCHAR(100) AFTER `address`;
ALTER TABLE `adoptions` ADD COLUMN `postal_code` VARCHAR(20) AFTER `city`;
ALTER TABLE `adoptions` ADD COLUMN `employment` VARCHAR(100) AFTER `postal_code`;
ALTER TABLE `adoptions` ADD COLUMN `home_type` VARCHAR(50) AFTER `employment`;
ALTER TABLE `adoptions` ADD COLUMN `home_ownership` VARCHAR(50) AFTER `home_type`;
ALTER TABLE `adoptions` ADD COLUMN `rental_permission` TINYINT(1) DEFAULT 0 AFTER `home_ownership`;
ALTER TABLE `adoptions` ADD COLUMN `have_yard` VARCHAR(50) AFTER `rental_permission`;
ALTER TABLE `adoptions` ADD COLUMN `other_pets_info` TEXT AFTER `have_yard`;
ALTER TABLE `adoptions` ADD COLUMN `has_children` TINYINT(1) DEFAULT 0 AFTER `other_pets_info`;
ALTER TABLE `adoptions` ADD COLUMN `children_ages` VARCHAR(100) AFTER `has_children`;
ALTER TABLE `adoptions` ADD COLUMN `adoption_reason` TEXT AFTER `children_ages`;
ALTER TABLE `adoptions` ADD COLUMN `reference1_name` VARCHAR(100) AFTER `adoption_reason`;
ALTER TABLE `adoptions` ADD COLUMN `reference1_phone` VARCHAR(20) AFTER `reference1_name`;
ALTER TABLE `adoptions` ADD COLUMN `reference2_name` VARCHAR(100) AFTER `reference1_phone`;
ALTER TABLE `adoptions` ADD COLUMN `reference2_phone` VARCHAR(20) AFTER `reference2_name`;
