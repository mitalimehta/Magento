<?php

$installer = $this;

$installer->startSetup();

$installer->run("

    ALTER TABLE {$this->getTable('sales_flat_shipment')}
    ADD COLUMN  `inventory_processed` TINYINT(4) NOT NULL DEFAULT '0';
    ");

$installer->endSetup();  