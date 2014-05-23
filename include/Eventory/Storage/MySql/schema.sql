CREATE TABLE `events` (
  `id` MEDIUMINT(8) unsigned NOT NULL AUTO_INCREMENT,
  `key` VARCHAR(255) NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `created` datetime NOT NULL,
  `updated` timestamp NOT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `event_assets` (
	`event_id` MEDIUMINT(8) UNSIGNED NOT NULL,
	`key` varchar(255) NOT NULL,
	`type` varchar(255) DEFAULT NULL,
	`hostUrl` varchar(255) DEFAULT NULL,
	`imageUrl` varchar(255) DEFAULT NULL,
	`linkUrl` varchar(255) DEFAULT NULL,
	`text` text DEFAULT NULL,
	UNIQUE KEY `event_key` (`event_id`, `key`)
);

CREATE TABLE `performers` (
	`id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL,
	`imageUrl` varchar(255) DEFAULT NULL,
	`highlight` TINYINT(1) NOT NULL DEFAULT 0,
	`created` DATETIME NOT NULL,
	`updated` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`),
	UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `event_performers` (
	`event_id` MEDIUMINT(8) UNSIGNED NOT NULL,
	`performer_id` MEDIUMINT(8) UNSIGNED NOT NULL,
	 UNIQUE KEY `id` (`event_id`, `performer_id`)
);