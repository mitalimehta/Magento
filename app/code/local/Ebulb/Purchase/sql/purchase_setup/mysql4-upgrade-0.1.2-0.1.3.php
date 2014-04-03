<?php

$installer = $this;

$installer->startSetup();

$installer->run("
     ALTER TABLE `po_order`  ADD COLUMN `shipping_method` VARCHAR(50) NULL AFTER `payment_terms`;
     ALTER TABLE `cataloginventory_stock_item`  ADD COLUMN `po_qty` DECIMAL(12,4) NOT NULL DEFAULT '0.0000' AFTER `min_qty`;
    ");

$installer->endSetup(); 
  

