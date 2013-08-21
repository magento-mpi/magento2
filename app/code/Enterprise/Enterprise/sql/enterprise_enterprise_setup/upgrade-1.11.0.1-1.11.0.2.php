<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Enterprise
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Magento_Core_Model_Resource_Setup */
$installer = $this;

$tableName = $installer->getTable('admin_rule');
/** @var Magento_DB_Adapter_Interface $connection */
$connection = $installer->getConnection();
$condition = $connection->prepareSqlCondition('resource_id', array(
    array('like' => '%content_staging%'),
    array('like' => '%enterprise_staging%'),
));
$connection->delete($tableName, $condition);
