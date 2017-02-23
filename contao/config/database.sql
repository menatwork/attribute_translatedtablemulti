-- **********************************************************
-- *                                                        *
-- * IMPORTANT NOTE                                         *
-- *                                                        *
-- * Do not import this file manually but use the Contao    *
-- * install tool to create and maintain database tables!   *
-- *                                                        *
-- **********************************************************

--
-- Table `tl_metamodel_tabletext`
--

CREATE TABLE `tl_metamodel_translatedmulti` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `tstamp` int(10) unsigned NOT NULL default '0',
  `att_id` int(10) unsigned NOT NULL default '0',
  `item_id` int(10) unsigned NOT NULL default '0',
  `langcode` varchar(5) NOT NULL default '',
  `row` int(5) unsigned NOT NULL default '0',
  `col` varchar(255) NOT NULL default '',
  `value` text NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `attitem` (`att_id`, `item_id`),
  UNIQUE KEY `attitemrowcol` (`att_id`, `item_id`, `row`, `col`, `langcode`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
