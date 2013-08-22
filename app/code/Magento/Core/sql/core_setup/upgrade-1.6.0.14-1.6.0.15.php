<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright  {copyright}
 * @license    {license_link}
 */

/* @var $installer Magento_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$connection = $installer->getConnection();
$table = $installer->getTable('core_theme');

$connection->dropColumn($table, 'magento_version_from');
$connection->dropColumn($table, 'magento_version_to');

$installer->endSetup();
