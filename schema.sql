CREATE TABLE IF NOT EXISTS `messages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `from` varchar(16) NOT NULL,
  `to` varchar(16) NOT NULL,
  `path` varchar(255) NOT NULL,
  `message` varchar(128) NOT NULL,
  `code` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;