ALTER TABLE `notificacio`
	ALTER `modul_id` DROP DEFAULT;
ALTER TABLE `notificacio`
	ADD COLUMN `user_id` INT NULL AFTER `id`,
	CHANGE COLUMN `modul_id` `modul_id` TINYINT(4) NOT NULL AFTER `user_id`,
	ADD INDEX `user_id` (`user_id`),
	ADD INDEX `modul_id` (`modul_id`);