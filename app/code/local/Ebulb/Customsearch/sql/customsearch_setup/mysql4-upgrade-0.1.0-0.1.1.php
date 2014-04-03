<?php

$installer = $this;

$installer->startSetup();

$installer->run("

CREATE TABLE `search_attribute_entity_int` (
    `entity_id` INT(10) NOT NULL AUTO_INCREMENT,
    `parent_id` INT(10) NOT NULL,
    `attribute_id` SMALLINT(5) UNSIGNED NOT NULL,
    `sort_order` INT(10) NOT NULL DEFAULT '0',
    `date_created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`entity_id`),
    INDEX `FK1_search_attribute_2` (`parent_id`),
    INDEX `FK2_search_atttribute_id_2` (`attribute_id`),
    CONSTRAINT `FK1_search_attribute_2` FOREIGN KEY (`parent_id`) REFERENCES `search_attribute_entity` (`entity_id`) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT `FK2_search_atttribute_id_2` FOREIGN KEY (`attribute_id`) REFERENCES `eav_attribute` (`attribute_id`) ON UPDATE CASCADE ON DELETE CASCADE
)
ENGINE=InnoDB
ROW_FORMAT=DEFAULT;

    ");

$installer->endSetup();  