CREATE TABLE `article_has_sources` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`article_id` INT(11) NOT NULL DEFAULT 0,
	`type` VARCHAR(10) NOT NULL DEFAULT '0' COMMENT 'language - library - framework - package - source',
	`name` VARCHAR(100) NOT NULL DEFAULT '0',
	`version` VARCHAR(10) NULL DEFAULT '0',
	`url` VARCHAR(250) NOT NULL DEFAULT '0',
	`visible` TINYINT(4) NULL DEFAULT 1,
	PRIMARY KEY (`id`),
	INDEX `article_id` (`article_id`)
)
ENGINE=InnoDB
;


