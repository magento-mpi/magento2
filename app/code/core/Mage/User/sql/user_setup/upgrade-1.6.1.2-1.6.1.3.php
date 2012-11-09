<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_User
 * @copyright   {copyright}
 * @license     {license_link}
 */
/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$tableName = $installer->getTable('admin_rule');
/** @var Varien_Db_Adapter_Interface $connection */
$connection = $installer->getConnection();

$connection->delete($tableName, array('resource_id LIKE ?' => '%xmlconnect%'));
