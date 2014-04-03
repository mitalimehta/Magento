<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('page_links')};
    
    CREATE TABLE `page_links` (
        `entity_id` INT(10) NOT NULL AUTO_INCREMENT,
        `page_title` VARCHAR(255) NULL DEFAULT '0',
        `html_link` VARCHAR(255) NULL DEFAULT '0',
        `category_id` INT(10) NULL DEFAULT '0',
        `website_id` SMALLINT(5) NULL DEFAULT '1',
        PRIMARY KEY (`entity_id`)
    )

    ");

$installer->endSetup(); 