CREATE TABLE `news` (
  `id` int(11) NOT NULL auto_increment,
  `msg` varchar(10024) default NULL,
  `time` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `title` varchar(32) default NULL,
  `main_time` int(11) NOT NULL default '0',
  `link` varchar(64) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `time` (`time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;