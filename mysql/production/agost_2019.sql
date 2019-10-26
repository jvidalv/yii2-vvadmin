CREATE TABLE session
(
    id CHAR(40) NOT NULL PRIMARY KEY,
    expire INTEGER,
    data BLOB
)
ALTER TABLE `session` ADD `user_id` INT NOT NULL AFTER `data`, ADD `last_write` INT NOT NULL AFTER `user_id`;
ALTER TABLE `session` ADD INDEX(`expire`);
ALTER TABLE `session` ADD INDEX(`user_id`);
ALTER TABLE `poble` ADD FOREIGN KEY (`media_id`) REFERENCES `media`(`id`) ON DELETE SET NULL ON UPDATE NO ACTION;
CREATE TABLE `fempoble`.`poble_push` ( `id` INT NOT NULL AUTO_INCREMENT , `poble_id` INT NOT NULL , `token` VARCHAR(1000) NOT NULL , `tipo` TINYINT NOT NULL , PRIMARY KEY (`id`), INDEX (`poble_id`)) ENGINE = InnoDB;
CREATE TABLE `fempoble`.`festivitat_push` ( `id` INT NOT NULL AUTO_INCREMENT , `festivitat_id` INT NOT NULL , `token` VARCHAR(1000) NOT NULL, PRIMARY KEY (`id`), INDEX (`festivitat_id`)) ENGINE = InnoDB;
ALTER TABLE `festivitat_push` ADD FOREIGN KEY (`festivitat_id`) REFERENCES `festivitat`(`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
ALTER TABLE `poble_push` ADD FOREIGN KEY (`poble_id`) REFERENCES `poble`(`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
ALTER TABLE `poble_push` CHANGE `poble_id` `poble_id` INT(11) NULL;
ALTER TABLE `poble_push` ADD `created_at` INT NULL AFTER `tipo`, ADD `updated_at` INT NULL AFTER `created_at`;
ALTER TABLE `festivitat_push` ADD `created_at` INT NULL AFTER `token`, ADD `updated_at` INT NULL AFTER `created_at`;
////////////////////////////// no fet
ALTER TABLE `festivitat` ADD `noti_setmana` TINYINT NULL DEFAULT '0' AFTER `dia_fi`, ADD `notia_dema` TINYINT NULL DEFAULT '0' AFTER `noti_setmana`;
ALTER TABLE `festivitat` ADD `noti_avui` TINYINT NULL DEFAULT '0' AFTER `notia_dema`;
ALTER TABLE `festivitat_has_event` ADD `noti` TINYINT NULL DEFAULT '0' AFTER `hora_fi`;
CREATE TABLE `fempoble`.`notificacio` ( `id` INT NOT NULL AUTO_INCREMENT , `enviat` TINYINT NULL DEFAULT '0' , `token` VARCHAR(200) NOT NULL , `extra` BLOB NULL , `contingut` VARCHAR(150) NOT NULL , `updated_at` INT NULL , `created_at` INT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;
ALTER TABLE `notificacio` ADD `modul` VARCHAR(20) NOT NULL AFTER `id`;
ALTER TABLE `notificacio` CHANGE `modul` `modul_id` TINYINT NOT NULL;
ALTER TABLE `festivitat` ADD `noti_activa` TINYINT NULL DEFAULT '0' AFTER `dia_fi`;
ALTER TABLE `notificacio` ADD `titol` VARCHAR(200) NULL AFTER `extra`;
////////////////////////////// fet
ALTER TABLE `notificacio` CHANGE `titol` `titol` VARCHAR(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;
ALTER TABLE `notificacio` ADD `subtitol` VARCHAR(100) NULL AFTER `titol`;
ALTER TABLE `notificacio` CHANGE `contingut` `contingut` VARCHAR(200) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;
ALTER TABLE `notificacio` CHANGE `contingut` `contingut` BLOB NOT NULL;
ALTER TABLE `notificacio` CHANGE `subtitol` `subtitol` VARCHAR(200) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;
ALTER TABLE `notificacio` CHANGE `titol` `titol` VARCHAR(200) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;
