SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `message_log`;
CREATE TABLE `message_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `message_sent` datetime DEFAULT NULL COMMENT 'Message sent',
  `message_status` enum('ok','error') COLLATE utf8_czech_ci NOT NULL DEFAULT 'error' COMMENT 'Message status',
  `message_body` text COLLATE utf8_czech_ci DEFAULT NULL COMMENT 'Message body',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Messages';


DROP TABLE IF EXISTS `records`;
CREATE TABLE `records` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `website_id` int(11) NOT NULL COMMENT 'Website',
  `occurrence_first` datetime DEFAULT NULL COMMENT 'First occurence',
  `occurrence_last` datetime DEFAULT NULL COMMENT 'Last occurence',
  `item_id` text COLLATE utf8_czech_ci DEFAULT NULL COMMENT 'Item ID',
  `item_1` text COLLATE utf8_czech_ci DEFAULT NULL COMMENT 'Item 1',
  `item_2` text COLLATE utf8_czech_ci DEFAULT NULL COMMENT 'Item 2',
  `item_3` text COLLATE utf8_czech_ci DEFAULT NULL COMMENT 'Item 3',
  `item_4` text COLLATE utf8_czech_ci DEFAULT NULL COMMENT 'Item 4',
  `item_5` text COLLATE utf8_czech_ci DEFAULT NULL COMMENT 'Item 5',
  `message_sent` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Message sent status',
  PRIMARY KEY (`id`),
  UNIQUE KEY `item_id` (`item_id`(400)),
  KEY `website_id` (`website_id`),
  CONSTRAINT `records_ibfk_1` FOREIGN KEY (`website_id`) REFERENCES `website` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Records';


DROP TABLE IF EXISTS `website`;
CREATE TABLE `website` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `label` varchar(120) COLLATE utf8_czech_ci NOT NULL COMMENT 'Label',
  `url` varchar(400) COLLATE utf8_czech_ci NOT NULL COMMENT 'Tracked URL',
  `status` enum('active','inactive') COLLATE utf8_czech_ci NOT NULL DEFAULT 'active' COMMENT 'Status',
  `tracking_last` datetime DEFAULT NULL COMMENT 'Last tracking datetime',
  `tracking_interval` enum('10m','1h','1d') COLLATE utf8_czech_ci NOT NULL DEFAULT '1h' COMMENT 'Tracking interval',
  `tracking_priority` enum('schedule','force_next') COLLATE utf8_czech_ci NOT NULL DEFAULT 'force_next' COMMENT 'Tracking priority',
  `content_wrapper` varchar(400) COLLATE utf8_czech_ci NOT NULL COMMENT 'Block wrapper',
  `content_id` varchar(400) COLLATE utf8_czech_ci NOT NULL COMMENT 'Content ID',
  `content_item_1` varchar(400) COLLATE utf8_czech_ci NOT NULL COMMENT 'Content item 1',
  `content_item_2` varchar(400) COLLATE utf8_czech_ci NOT NULL COMMENT 'Content item 2',
  `content_item_3` varchar(400) COLLATE utf8_czech_ci NOT NULL COMMENT 'Content item 3',
  `content_item_4` varchar(400) COLLATE utf8_czech_ci NOT NULL COMMENT 'Content item 4',
  `content_item_5` varchar(400) COLLATE utf8_czech_ci NOT NULL COMMENT 'Content item 5',
  `message` text COLLATE utf8_czech_ci NOT NULL COMMENT 'Message',
  PRIMARY KEY (`id`),
  KEY `status_id` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Websites';



DROP TABLE IF EXISTS `tracking_log`;
CREATE TABLE `tracking_log` (
  `id` int NOT NULL COMMENT 'ID' AUTO_INCREMENT PRIMARY KEY,
  `tracking_timestamp` datetime NULL COMMENT 'Timestamp',
  `tracking_log` text NULL COMMENT 'Log'
) COMMENT='Tracking log';


ALTER TABLE `website` ADD `default_content_item_1` varchar(400) COLLATE 'utf8_czech_ci' NULL COMMENT 'Content item 1 (default)';
ALTER TABLE `website` ADD `default_content_item_2` varchar(400) COLLATE 'utf8_czech_ci' NULL COMMENT 'Content item 2 (default)';
ALTER TABLE `website` ADD `default_content_item_3` varchar(400) COLLATE 'utf8_czech_ci' NULL COMMENT 'Content item 3 (default)';
ALTER TABLE `website` ADD `default_content_item_4` varchar(400) COLLATE 'utf8_czech_ci' NULL COMMENT 'Content item 4 (default)';
ALTER TABLE `website` ADD `default_content_item_5` varchar(400) COLLATE 'utf8_czech_ci' NULL COMMENT 'Content item 5 (default)';

ALTER TABLE `website`
ADD `content_id_context` enum('global','website') COLLATE 'utf8_czech_ci' NOT NULL DEFAULT 'website' COMMENT 'Content unique ID context' AFTER `content_id`;
ALTER TABLE `records`
DROP INDEX `item_id`;



ALTER TABLE `website`
ADD `tracking_type` enum('single','multiple') COLLATE 'utf8_czech_ci' NOT NULL DEFAULT 'multiple' COMMENT 'Tracking type' AFTER `tracking_priority`;

ALTER TABLE `records`
ADD `occurrence_count` int NULL COMMENT 'Occurence count' AFTER `occurrence_last`;
ALTER TABLE `records`
CHANGE `occurrence_count` `occurrence_count` int(11) NULL DEFAULT '1' COMMENT 'Occurence count' AFTER `occurrence_last`;


ALTER TABLE `website`
CHANGE `tracking_interval` `tracking_interval` enum('10m','1h','3h','1d') COLLATE 'utf8_czech_ci' NOT NULL DEFAULT '1h' COMMENT 'Tracking interval' AFTER `tracking_last`;


CREATE TABLE `folder` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(120) NOT NULL
) COLLATE 'utf8_czech_ci';

ALTER TABLE `website`
ADD `folder_id` int(11) NULL AFTER `label`,
ADD FOREIGN KEY (`folder_id`) REFERENCES `folder` (`id`);
ALTER TABLE `website`
CHANGE `folder_id` `folder_id` int(11) NULL COMMENT 'Folder' AFTER `label`;

-- ===================================================

CREATE OR REPLACE VIEW cron_list AS
SELECT id,label,tracking_last, tracking_interval, tracking_priority, content_id_context, tracking_type FROM website
WHERE 
status='active'
AND
(
  tracking_priority = 'force_next'
  OR
  (
      tracking_priority = 'schedule' AND
      (
          (tracking_interval='10m' AND tracking_last<(NOW()-INTERVAL 9 MINUTE)) OR
          (tracking_interval='1h' AND tracking_last<(NOW()-INTERVAL 55 MINUTE)) OR
          (tracking_interval='3h' AND tracking_last<(NOW()-INTERVAL 175 MINUTE)) OR
          (tracking_interval='1d' AND tracking_last<(NOW()-INTERVAL 1435 MINUTE)) OR
          tracking_last IS NULL
      )
  )
)
ORDER BY FIELD(tracking_priority, 'force_next','schedule'), tracking_last ASC;

