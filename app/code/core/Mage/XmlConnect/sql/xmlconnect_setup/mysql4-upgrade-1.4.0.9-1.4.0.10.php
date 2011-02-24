<?php

$installer = $this;

$installer->startSetup();

$installer->run("ALTER TABLE {$installer->getTable('xmlconnect_queue')} CHANGE `exec_time` `exec_time` TIMESTAMP NULL DEFAULT NULL;");

$installer->endSetup();
