<?php

$installer = $this;

$installer->startSetup();

$installer->run("
     ALTER TABLE `po_order`  ADD COLUMN `shipping_state` VARCHAR(255) NULL DEFAULT NULL AFTER `shipping_city`;
    ");

$installer->endSetup(); 
