<?php

$installer = $this;

$installer->startSetup();

$installer->run("ALTER TABLE {$installer->getTable('xmlconnect_history')} ADD `name` VARCHAR( 255 ) NOT NULL AFTER `activation_key`;");

$installer->endSetup();
