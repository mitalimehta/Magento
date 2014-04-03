<?php

$installer = $this;

$installer->startSetup();

$installer->run("

    ALTER TABLE {$this->getTable('sales_flat_order')}
    ADD COLUMN `margin` DECIMAL(12,4) NULL DEFAULT '0',
    ADD COLUMN `total_cost` DECIMAL(12,4) NULL DEFAULT '0'; 

    ");

$installer->endSetup(); 

#ALTER TABLE `sales_flat_order`  
#ADD COLUMN `shipping_today` DECIMAL(12,4) NULL DEFAULT '0' 
#AFTER `base_shipping_incl_tax`,  
#ADD COLUMN `shipping_insurance` DECIMAL(12,4) NULL DEFAULT '0' AFTER `shipping_today`;

#ALTER TABLE `sales_flat_quote`  
#ADD COLUMN `shipping_today` DECIMAL(12,4) NULL DEFAULT '0' 
#AFTER `customer_industry`,  
#ADD COLUMN `shipping_insurance` DECIMAL(12,4) NULL DEFAULT '0' AFTER `shipping_today`;

#ALTER TABLE `sales_flat_quote_address`  
#ADD COLUMN `shipping_today` DECIMAL(12,4) NULL DEFAULT '0' 
#AFTER `base_shipping_incl_tax`,  
#ADD COLUMN `shipping_insurance` DECIMAL(12,4) NULL DEFAULT '0' AFTER `shipping_today`;