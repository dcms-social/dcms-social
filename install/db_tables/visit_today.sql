CREATE TABLE IF NOT EXISTS `visit_today` (
  `ip` bigint(11) NOT NULL,
  `ua` varchar(128) DEFAULT NULL,
  `time` int(11) NOT NULL,
  KEY `ip` (`ip`),
  KEY `ua` (`ua`),
  KEY `time` (`time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
