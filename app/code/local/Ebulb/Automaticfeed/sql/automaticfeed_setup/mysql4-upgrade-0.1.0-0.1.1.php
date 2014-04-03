<?php
$installer = $this;
$installer->startSetup();
$installer->run("

    ALTER TABLE `auto_feed_attr_info`  ADD COLUMN `sort_order` INT(11) NULL DEFAULT '0' AFTER `custom_value`;

");

                                                                                                                                                                            
$installer->endSetup();