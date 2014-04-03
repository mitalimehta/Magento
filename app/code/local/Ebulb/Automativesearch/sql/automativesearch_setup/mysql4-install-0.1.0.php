<?php
$installer = $this;
$installer->startSetup();
$installer->run("

CREATE TABLE `automotive_product` (
    `car_manufacturer_product_id` INT(11) NOT NULL AUTO_INCREMENT,
    `car_manufacturer_id` INT(10) NOT NULL DEFAULT '0',
    `car_manufacturer_year_id` INT(10) NOT NULL DEFAULT '0',
    `car_manufacturer_model_id` INT(10) NOT NULL DEFAULT '0',
    `car_manufacturer_type_id` INT(10) NULL DEFAULT '0',
    `location_id` INT(10) NOT NULL DEFAULT '0',
    `product_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
    PRIMARY KEY (`car_manufacturer_product_id`),
    INDEX `car_manufacturer_id_3` (`car_manufacturer_id`),
    INDEX `car_manufacturer_model_id_1` (`car_manufacturer_model_id`),
    INDEX `car_manufacturer_year_id_2` (`car_manufacturer_year_id`),
    INDEX `location_id` (`location_id`),
    INDEX `FK1_product` (`product_id`),
    CONSTRAINT `FK1_product` FOREIGN KEY (`product_id`) REFERENCES `catalog_product_entity` (`entity_id`) ON UPDATE CASCADE ON DELETE CASCADE
)
ENGINE=InnoDB
ROW_FORMAT=DEFAULT;

");

                                                                                                                                                                            
$installer->endSetup();