<?php
$installer = $this;
$installer->startSetup();
$installer->run("

 CREATE TABLE `search_attribute_entity` (
    `entity_id` INT(10) NOT NULL AUTO_INCREMENT,
    `type_id` INT(10) NOT NULL DEFAULT '0',
    `category_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
    `type_entity_id` INT(10) NOT NULL DEFAULT '0',
    `type_entity_value` INT(10) NOT NULL DEFAULT '0',
    `type_label` VARCHAR(255) NOT NULL DEFAULT '0',
    `enabled` TINYINT(1) NOT NULL DEFAULT '1',
    `date_created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`entity_id`)
)
ENGINE=InnoDB
ROW_FORMAT=DEFAULT;

");

                                                                                                                                                                            
$installer->endSetup();