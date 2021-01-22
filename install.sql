SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `website`;
CREATE TABLE `website` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `label` varchar(120) COLLATE utf8_czech_ci NOT NULL COMMENT 'Label',
  `url` varchar(400) COLLATE utf8_czech_ci NOT NULL COMMENT 'Tracked URL',
  `status` enum('active','inactive') COLLATE utf8_czech_ci NOT NULL DEFAULT 'active' COMMENT 'Status',
  `tracking_last` datetime DEFAULT NULL COMMENT 'Last tracking datetime',
  `tracking_interval` enum('10m','1h','1d') COLLATE utf8_czech_ci NOT NULL DEFAULT '1h' COMMENT 'Tracking interval',
  `tracking_priority` enum('shedule','force_next') COLLATE utf8_czech_ci NOT NULL DEFAULT 'shedule' COMMENT 'Tracking priority',
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Website';



CREATE OR REPLACE VIEW cron_list AS
SELECT id,label,tracking_last, tracking_interval, tracking_priority FROM website
WHERE 
status='active'
AND
tracking_priority = 'force_next'
OR
(
    tracking_priority = 'shedule' AND
    (
        (tracking_interval='10m' AND tracking_last<(NOW()-INTERVAL 10 MINUTE)) OR
        (tracking_interval='1h' AND tracking_last<(NOW()-INTERVAL 1 HOUR)) OR
        (tracking_interval='1d' AND tracking_last<(NOW()-INTERVAL 1 DAY)) OR
        tracking_last IS NULL
    )
)

ORDER BY FIELD(tracking_priority, 'force_next','shedule'), tracking_last ASC;


DROP TABLE IF EXISTS `records`;
CREATE TABLE `records` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `occurrence_first` datetime DEFAULT NULL,
  `occurrence_last` datetime DEFAULT NULL,
  `item_id` text COLLATE utf8_czech_ci DEFAULT NULL,
  `item_1` text COLLATE utf8_czech_ci DEFAULT NULL,
  `item_2` text COLLATE utf8_czech_ci DEFAULT NULL,
  `item_3` text COLLATE utf8_czech_ci DEFAULT NULL,
  `item_4` text COLLATE utf8_czech_ci DEFAULT NULL,
  `item_5` text COLLATE utf8_czech_ci DEFAULT NULL,
  `message_send` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `item_id` (`item_id`(400))
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;