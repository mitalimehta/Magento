<?php
$installer = $this;
$installer->startSetup();
$installer->run("

    ALTER TABLE `auto_feed_info`  ADD COLUMN `directory` VARCHAR(255) NULL DEFAULT '' AFTER `include_header`;

");

                                                                                                                                                                            
$installer->endSetup();


