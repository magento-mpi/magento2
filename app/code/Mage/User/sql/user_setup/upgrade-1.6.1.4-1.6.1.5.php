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

$condition = $connection->prepareSqlCondition('resource_id', array(
    array(
        'in' => array(
            'admin/system/config/facebook',
            'Social_Facebook::facebook',
            'admin/system/config/feed',
            'Find_Feed::config_feed',
            'admin/catalog/feed',
            'Find_Feed::feed',
            'admin/catalog/feed/import_items',
            'Find_Feed::import_items',
            'admin/catalog/feed/import_products',
            'Find_Feed::import_products',
        ),
    ),
));

$connection->delete($tableName, $condition);
