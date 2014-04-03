<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('page_clicks')};
   CREATE TABLE `page_clicks` (
    `entity_id` INT(10) NOT NULL AUTO_INCREMENT,
    `website_id` INT(10) NULL DEFAULT '1',
    `link_id` INT(10) NULL DEFAULT NULL,
    `ipaddress` VARCHAR(50) NULL DEFAULT NULL,
    `datetime` DATETIME NULL DEFAULT NULL,
    PRIMARY KEY (`entity_id`)
)

    ");

$installer->endSetup(); 