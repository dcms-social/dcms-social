CREATE TABLE IF NOT EXISTS `forum_p` (
  `id` int(11) NOT NULL auto_increment,
  `id_forum` int(11) NOT NULL,
  `id_razdel` int(11) NOT NULL,
  `id_them` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `msg` varchar(1024) NOT NULL,
  `cit` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `id_user` (`id_user`),
  KEY `time` (`time`),
  KEY `id_forum` (`id_forum`),
  KEY `id_razdel` (`id_razdel`),
  KEY `id_them` (`id_them`),
  FULLTEXT KEY `msg` (`msg`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
