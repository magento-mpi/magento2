<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

/* @var $installer Magento\Setup\Module\SetupModule */
$installer = $this;

$installer->startSetup();
$connection = $installer->getConnection();
/**
 * Modifying 'core_layout_update' table. Removing 'is_vde' column
 */
$tableCoreLayoutLink = $installer->getTable('core_layout_update');

$connection->dropColumn($tableCoreLayoutLink, 'is_vde');

$installer->endSetup();
