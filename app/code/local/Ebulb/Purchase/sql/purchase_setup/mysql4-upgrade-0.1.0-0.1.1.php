<?php

$installer = $this;

$installer->startSetup();

$installer->run("

    ALTER TABLE {$this->getTable('cataloginventory_stock_item')}
    ADD COLUMN `total_qty` DECIMAL(12,4) NOT NULL DEFAULT '0.0000' AFTER `qty`,
    ADD COLUMN `max_qty` DECIMAL(12,4) NOT NULL DEFAULT '0.0000' AFTER `min_qty`;

    ");

$installer->endSetup();  