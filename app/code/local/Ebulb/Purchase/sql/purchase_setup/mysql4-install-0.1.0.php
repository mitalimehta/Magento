<?php
$installer = $this;
$installer->startSetup();
$installer->run("

CREATE TABLE IF NOT EXISTS `po_invoice` (
    `invoice_id` INT(11) NOT NULL AUTO_INCREMENT,
    `increment_id`  VARCHAR(50) NOT NULL, 
    `purchase_receipt_id` INT(11) NOT NULL,
    `shipping_price` DECIMAL(13,4) NOT NULL DEFAULT '0.0000',
    `tax` DECIMAL(13,4) NOT NULL DEFAULT '0.0000',
    `subtotal` DECIMAL(13,4) NOT NULL DEFAULT '0.0000',
    `adjust_fee` DECIMAL(13,4) NOT NULL DEFAULT '0.0000',
    `total` DECIMAL(13,4) NOT NULL DEFAULT '0.0000',
    `comments` TEXT NULL,
    `created_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `cancel_date` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`invoice_id`)
)
ENGINE=MyISAM
ROW_FORMAT=DEFAULT
AUTO_INCREMENT=456;

CREATE TABLE `po_order` (
    `order_id` INT(11) NOT NULL AUTO_INCREMENT,
    `increment_id` VARCHAR(50) NOT NULL,
    `vendor_id` INT(11) NOT NULL,
    `created_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `cancel_date` TIMESTAMP NULL DEFAULT NULL,
    `order_eta` DATETIME NULL DEFAULT NULL,
    `notify_vendor_date` DATETIME NULL DEFAULT NULL,
    `tax_rate` DECIMAL(5,2) NOT NULL DEFAULT '0.00',
    `tax` DECIMAL(13,4) NOT NULL DEFAULT '0.0000',
    `shipping_price` DECIMAL(13,4) NOT NULL DEFAULT '0.0000',
    `subtotal` DECIMAL(13,4) NOT NULL DEFAULT '0.0000',
    `total` DECIMAL(13,4) NOT NULL DEFAULT '0.0000',
    `adjust_fee` DECIMAL(13,4) NOT NULL DEFAULT '0.0000',
    `comments` TEXT NULL,
    `status` VARCHAR(25) NOT NULL DEFAULT 'new',
    `payment_terms` TEXT NULL,
    `purchase_rep` VARCHAR(255) NULL DEFAULT NULL,
    `vendor_contact_id` INT(11) NULL DEFAULT NULL,
    `store_id` CHAR(10) NULL DEFAULT NULL,
    `shipping_name` VARCHAR(255) NULL DEFAULT NULL,
    `shipping_company` VARCHAR(255) NULL DEFAULT NULL,
    `shipping_street1` VARCHAR(255) NULL DEFAULT NULL,
    `shipping_street2` VARCHAR(255) NULL DEFAULT NULL,
    `shipping_city` VARCHAR(255) NULL DEFAULT NULL,
    `shipping_zipcode` VARCHAR(255) NULL DEFAULT NULL,
    `shipping_country` VARCHAR(255) NULL DEFAULT NULL,
    `shipping_telephone1` VARCHAR(255) NULL DEFAULT NULL,
    `shipping_telephone2` VARCHAR(255) NULL DEFAULT NULL,
    `shipping_fax` VARCHAR(255) NULL DEFAULT NULL,
    `shipping_email` VARCHAR(255) NULL DEFAULT NULL,
    `shipping_type` VARCHAR(255) NULL DEFAULT NULL,
    `shipping_sales_order_id` VARCHAR(255) NULL DEFAULT NULL,
    PRIMARY KEY (`order_id`)
)
ENGINE=MyISAM
ROW_FORMAT=DEFAULT
AUTO_INCREMENT=481;

CREATE TABLE IF NOT EXISTS `po_order_item` (
    `order_item_id` INT(11) NOT NULL AUTO_INCREMENT,
    `purchase_order_id` INT(11) NOT NULL,
    `product_id` INT(11) NULL DEFAULT NULL,
    `product_name` VARCHAR(255) NULL DEFAULT NULL,
    `product_qty` INT(11) NOT NULL,
    `product_price` DECIMAL(13,4) NOT NULL DEFAULT '0.0000',
    `tax` DECIMAL(13,4) NOT NULL DEFAULT '0.0000',
    `tax_rate` DECIMAL(6,2) NOT NULL DEFAULT '0.00',
    `subtotal` DECIMAL(13,4) NOT NULL DEFAULT '0.0000',
    `adjust_fee` DECIMAL(13,4) NOT NULL DEFAULT '0.0000',
    `total` DECIMAL(13,4) NOT NULL,
    PRIMARY KEY (`order_item_id`)
)
ENGINE=MyISAM
ROW_FORMAT=DEFAULT
AUTO_INCREMENT=2400;

CREATE TABLE IF NOT EXISTS `po_receipt` (
    `receipt_id` INT(11) NOT NULL AUTO_INCREMENT,
    `increment_id`  VARCHAR(50) NOT NULL, 
    `purchase_order_id` INT(11) NOT NULL,
    `package_number` VARCHAR(255) NOT NULL,
    `created_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `cancel_date` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`receipt_id`)
)
ENGINE=MyISAM
ROW_FORMAT=DEFAULT
AUTO_INCREMENT=456;

CREATE TABLE IF NOT EXISTS `po_receipt_item` (
    `receipt_item_id` INT(11) NOT NULL AUTO_INCREMENT,
    `receipt_id` INT(11) NOT NULL,
    `product_id` INT(11) NOT NULL,
    `qty` DECIMAL(11,2) NOT NULL DEFAULT '0.00',
    `created_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `cancel_date` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`receipt_item_id`)
)
ENGINE=MyISAM
ROW_FORMAT=DEFAULT
AUTO_INCREMENT=456;

CREATE TABLE IF NOT EXISTS `po_stock_movement` (
    `stock_movement_id` INT(11) NOT NULL AUTO_INCREMENT,
    `doc_id` INT(11) NOT NULL,
    `product_id` VARCHAR(255) NOT NULL,
    `product_qty` DECIMAL(11,2) NOT NULL,
    `created_date` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,  
    `comments` VARCHAR(255) NULL,
    PRIMARY KEY (`stock_movement_id`)
)
ENGINE=MyISAM
ROW_FORMAT=DEFAULT
AUTO_INCREMENT=148;

CREATE TABLE IF NOT EXISTS `po_vendor` (
    `vendor_id` INT(11) NOT NULL AUTO_INCREMENT,
    `vendor_name` VARCHAR(255) NOT NULL,
    `vendor_company_name`  VARCHAR(255) NOT NULL,
    `address1` VARCHAR(255) NULL DEFAULT NULL,
    `address2` VARCHAR(255) NULL DEFAULT NULL,
    `zipcode` VARCHAR(20) NULL DEFAULT NULL,
    `city` VARCHAR(255) NULL DEFAULT NULL,
    `state` VARCHAR(255) NULL DEFAULT NULL,
    `country` VARCHAR(3) NULL DEFAULT NULL,
    `telephone1` VARCHAR(255) NULL DEFAULT NULL,
    `ext1` VARCHAR(55) NULL DEFAULT NULL,
    `telephone2` VARCHAR(255) NULL DEFAULT NULL,
    `ext2` VARCHAR(55) NULL DEFAULT NULL,
    `fax` VARCHAR(20) NULL DEFAULT NULL,
    `cellphone` VARCHAR(255) NULL DEFAULT NULL,
    `other_telephone` VARCHAR(255) NULL DEFAULT NULL,
    `email` VARCHAR(255) NULL DEFAULT NULL,
    `website` VARCHAR(255) NULL DEFAULT NULL,
    `federal_tax_number` VARCHAR(50) NULL DEFAULT NULL,
    `ship_to_address` TEXT NULL,
    `payment_terms` TEXT NULL,
    `credit_limit` VARCHAR(50) NULL DEFAULT NULL,
    `account_name_from_vendor` VARCHAR(255) NULL DEFAULT NULL,
    PRIMARY KEY (`vendor_id`)
)
ENGINE=MyISAM
ROW_FORMAT=DEFAULT
AUTO_INCREMENT=148;

CREATE TABLE IF NOT EXISTS `po_vendor_contact` (
    `vendor_contact_id` INT(11) NOT NULL AUTO_INCREMENT,
    `vendor_id` INT(11) NOT NULL,
    `first_name` VARCHAR(255) NULL DEFAULT NULL,
    `last_name` VARCHAR(255) NULL DEFAULT NULL,
    `middle_name` VARCHAR(255) NULL DEFAULT NULL,
    `telephone1` VARCHAR(255) NULL DEFAULT NULL,
    `ext1` VARCHAR(55) NULL DEFAULT NULL,
    `telephone2` VARCHAR(255) NULL DEFAULT NULL,
    `ext2` VARCHAR(55) NULL DEFAULT NULL,
    `cellphone` VARCHAR(255) NULL DEFAULT NULL,
    `fax` VARCHAR(20) NULL DEFAULT NULL,
    `email` VARCHAR(255) NULL DEFAULT NULL,
    `website` VARCHAR(255) NULL DEFAULT NULL,
    `comments` TEXT NULL,
    PRIMARY KEY (`vendor_contact_id`)
)
ENGINE=MyISAM
ROW_FORMAT=DEFAULT
AUTO_INCREMENT=148;

CREATE TABLE IF NOT EXISTS `po_vendor_product` (
    `vendor_product_id` INT(11) NOT NULL AUTO_INCREMENT,
    `vendor_id` INT(11) NOT NULL,
    `product_id` INT(11) NOT NULL,
    `vendor_sku` VARCHAR(255) NULL DEFAULT NULL,
    `unit_cost` DECIMAL(11,2) NULL DEFAULT '1.00',
    `master_carton_pack` DECIMAL(11,2) NOT NULL DEFAULT '1.00',
    `avg_cost` DECIMAL(11,2) NOT NULL DEFAULT '0.00',
    `last_addon_cost` DECIMAL(11,2) NOT NULL DEFAULT '0.00',
    `last_landed_cost` DECIMAL(11,2) NOT NULL DEFAULT '0.00',
    `lead_time` VARCHAR(255) NULL DEFAULT NULL,
    PRIMARY KEY (`vendor_product_id`)
)
ENGINE=MyISAM
ROW_FORMAT=DEFAULT
AUTO_INCREMENT=148




");

                                                                                                                                                                            
$installer->endSetup();