<?php
$installer = $this;
$installer->startSetup();
$installer->run("

CREATE TABLE IF NOT EXISTS `auto_feed_company` (
    `company_id` INT(11) NOT NULL AUTO_INCREMENT,
    `company_name`  VARCHAR(50) NULL DEFAULT NULL, 
    `company_website`  VARCHAR(50) NULL DEFAULT NULL, 
    PRIMARY KEY (`company_id`)
)
ENGINE=InnoDB
ROW_FORMAT=DEFAULT;

CREATE TABLE `auto_feed_info` (
    `feed_id` INT(11) NOT NULL AUTO_INCREMENT,
    `company_id` INT(11) NOT NULL,
    `store_id` INT(11) NOT NULL,
    `created_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `ftp_host` VARCHAR(255) NULL DEFAULT NULL,
    `ftp_user` VARCHAR(55) NULL DEFAULT NULL,
    `ftp_pwd` VARCHAR(55) NULL DEFAULT NULL,
    `ftp_testmode` TINYINT(2) NULL DEFAULT NULL,
    `file_name` VARCHAR(255) NULL DEFAULT NULL,
    `file_type` VARCHAR(55) NULL DEFAULT NULL,
    `logfile_name` VARCHAR(255) NULL DEFAULT NULL,
    PRIMARY KEY (`feed_id`),
    CONSTRAINT `FK_AUTOFEED_COMPANY` FOREIGN KEY (`company_id`) REFERENCES `auto_feed_company` (`company_id`) ON UPDATE CASCADE ON DELETE CASCADE
)
ENGINE=InnoDB
ROW_FORMAT=DEFAULT;

CREATE TABLE `auto_feed_attr_info` (
    `attr_info_id` INT(11) NOT NULL AUTO_INCREMENT,
    `feed_id` INT(11) NOT NULL,
    `company_attr` VARCHAR(255) NULL DEFAULT NULL,
    `attribute_id` INT(11) NULL DEFAULT NULL,
    `custom_value` VARCHAR(255) NULL DEFAULT NULL,
    PRIMARY KEY (`attr_info_id`),
    CONSTRAINT `FK_AUTOFEED_INFO` FOREIGN KEY (`feed_id`) REFERENCES `auto_feed_info` (`feed_id`) ON UPDATE CASCADE ON DELETE CASCADE
)
ENGINE=InnoDB
ROW_FORMAT=DEFAULT

");

                                                                                                                                                                            
$installer->endSetup();