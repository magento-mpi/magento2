<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_User
 * @copyright   {copyright}
 * @license     {license_link}
 */
/** @var $installer Magento_Core_Model_Resource_Setup */
$installer = $this;

$tableName = $installer->getTable('admin_rule');
/** @var Magento_DB_Adapter_Interface $connection */
$connection = $installer->getConnection();

$condition = $connection->prepareSqlCondition('resource_id', array(
    array('like' => '%xmlconnect%'),
    array(
        'in' => array(
            /**
             * Include both old and new identifiers, as depending on install or upgrade process there can be
             * either first or second in the database
             */
            'admin/system/convert/gui',
            'Magento_Adminhtml::gui',
            'admin/system/convert/profiles',
            'Magento_Adminhtml::profiles'
        ),
    ),
));
$connection->delete($tableName, $condition);
