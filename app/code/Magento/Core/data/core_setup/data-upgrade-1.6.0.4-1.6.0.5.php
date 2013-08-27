<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Magento_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$tableName = $installer->getTable('admin_rule');
if ($tableName) {
    $installer->getConnection()->delete($tableName, array('resource_id = ?' => 'admin/system/tools/compiler'));
}
$tableName = $installer->getTable('core_resource');
if ($tableName) {
    $installer->getConnection()->delete($tableName, array('code = ?' => 'admin_setup'));
}

$installer->endSetup();
