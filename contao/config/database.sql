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

CREATE TABLE `tl_metamodel_translatedtabletext` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `tstamp` int(10) unsigned NOT NULL default '0',
  `att_id` int(10) unsigned NOT NULL default '0',
  `item_id` int(10) unsigned NOT NULL default '0',
  `langcode` varchar(5) NOT NULL default '',
  `row` int(5) unsigned NOT NULL default '0',
  `col` int(5) unsigned NOT NULL default '0',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `attitem` (`att_id`, `item_id`),
  UNIQUE KEY `attitemrowcol` (`att_id`, `item_id`, `row`, `col`, `langcode`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Table `tl_metamodel_attribute`
-- 

CREATE TABLE `tl_metamodel_attribute` (
  `translatedtabletext_cols` blob NULL,
  `tabletext_quantity_cols` varchar(2) NOT NULL default ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;