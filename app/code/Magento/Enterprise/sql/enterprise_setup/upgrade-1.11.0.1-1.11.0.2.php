<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Enterprise
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Core\Model\Resource\Setup */
$installer = $this;

$tableName = $installer->getTable('admin_rule');
/** @var \Magento\DB\Adapter\AdapterInterface $connection */
$connection = $installer->getConnection();
$condition = $connection->prepareSqlCondition('resource_id', array(
    array('like' => '%content_staging%'),
    array('like' => '%enterprise_staging%'),
));
$connection->delete($tableName, $condition);
