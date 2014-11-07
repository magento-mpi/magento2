<?php
/**
 * Update script for Webapi module.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer \Magento\Setup\Module\SetupModule */
$installer = $this;
$installer->startSetup();
$connection = $installer->getConnection();

$connection->dropTable($this->getTable('webapi_user'));
$connection->dropTable($this->getTable('webapi_rule'));
$connection->dropTable($this->getTable('webapi_role'));

$installer->endSetup();
