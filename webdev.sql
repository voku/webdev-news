#
# Structure for table "webdev_models_rss"
#

CREATE TABLE `webdev_models_rss` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `website_id` int(11) NOT NULL DEFAULT '0',
  `link` varchar(255) NOT NULL DEFAULT '',
  `pub_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `title` text NOT NULL,
  `description` text NOT NULL,
  `media` varchar(255) NOT NULL DEFAULT '',
  `content_html` text NOT NULL,
  `content_plaintext` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `link` (`link`(191)),
  KEY `website_id` (`website_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4148 DEFAULT CHARSET=utf8mb4;

#
# Structure for table "webdev_models_website"
#

CREATE TABLE `webdev_models_website` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(255) NOT NULL DEFAULT '',
  `rss_url` varchar(255) NOT NULL DEFAULT '',
  `category` varchar(255) NOT NULL DEFAULT '',
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `url` (`url`(191))
) ENGINE=InnoDB AUTO_INCREMENT=142 DEFAULT CHARSET=utf8mb4;
