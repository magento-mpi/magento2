<?php
/**
 * Update script for Webapi module.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var \Magento\Core\Model\Resource\Setup $installer */
$installer = $this;
$installer->startSetup();
$connection = $installer->getConnection();

$connection->dropTable($this->getTable('webapi_user'));
$connection->dropTable($this->getTable('webapi_rule'));
$connection->dropTable($this->getTable('webapi_role'));

$installer->endSetup();
