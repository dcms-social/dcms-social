CREATE TABLE IF NOT EXISTS `visit_everyday` (
  `time` int(11) NOT NULL,
  `host` int(11) NOT NULL,
  `hit` int(11) NOT NULL,
  `host_ip_ua` int(11) NOT NULL,
  KEY `time` (`time`),
  KEY `host` (`host`),
  KEY `hit` (`hit`),
  KEY `host_ip_ua` (`host_ip_ua`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
