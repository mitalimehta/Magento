<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('testimonial')};
 CREATE TABLE IF NOT EXISTS `testimonial` (
  `testimonial_id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL,
  `testimonial_text` text NOT NULL,
  `fullname` varchar(150) NOT NULL,
  `city` varchar(50) NOT NULL,
  `state` varchar(50) NOT NULL,
  `posted` datetime NOT NULL,
  `approved` tinyint(1) NOT NULL default '0',
  `link` varchar(255) NOT NULL,
  PRIMARY KEY  (`testimonial_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

    ");

$installer->endSetup(); 