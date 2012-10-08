<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$tableName = $installer->getTable('admin_rule');
if ($tableName) {
    $installer->getConnection()->delete($tableName,  array('resource_id = ?' => 'admin/system/tools/compiler'));
}

$installer->endSetup();
