<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_DesignEditor
 * @copyright  {copyright}
 * @license    {license_link}
 */

/* @var $installer Magento_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();
$connection = $installer->getConnection();
/**
 * Modifying 'core_layout_update' table. Removing 'is_vde' column
 */
$tableCoreLayoutLink = $installer->getTable('core_layout_update');

$connection->dropColumn($tableCoreLayoutLink, 'is_vde');

$installer->endSetup();
