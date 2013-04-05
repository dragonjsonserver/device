CREATE TABLE `devices` (
  `device_id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `account_id` BIGINT(20) UNSIGNED NOT NULL,
  `platform` VARCHAR(255) NOT NULL,
  `credentials` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`device_id`),
  UNIQUE KEY (`platform`, `credentials`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
