<?php
$installer = $this;
$installer->startSetup();
$installer->run("

    ALTER TABLE `auto_feed_info`  ADD COLUMN `seperator` VARCHAR(10) NULL DEFAULT 'comma' AFTER `logfile_name`,  
    ADD COLUMN `include_header` TINYINT(4) NULL DEFAULT '1' AFTER `seperator`;

");

                                                                                                                                                                            
$installer->endSetup();


