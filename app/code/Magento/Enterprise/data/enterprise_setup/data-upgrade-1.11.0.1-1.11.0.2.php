<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Enterprise\Model\Resource\Setup */
$installer = $this;

$tableName = $installer->getTable('authorization_rule');
/** @var $connection \Magento\Framework\DB\Adapter\AdapterInterface */
$connection = $installer->getConnection();
$condition = $connection->prepareSqlCondition(
    'resource_id',
    array(array('like' => '%content_staging%'), array('like' => '%enterprise_staging%'))
);
$connection->delete($tableName, $condition);
