<?php

 
$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$installer->run("
ALTER TABLE `sales_flat_quote_item`  ADD COLUMN `is_product_upgrade` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ;
");
$installer->run("
ALTER TABLE `sales_flat_quote_item`  ADD COLUMN `upgraded_product_id` INT(10) UNSIGNED NULL DEFAULT NULL ;
");

$installer->endSetup();