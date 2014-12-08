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
$connection = $installer->getConnection();
$condition = $connection->prepareSqlCondition(
    'resource_id',
    [['like' => '%content_staging%'], ['like' => '%enterprise_staging%']]
);
$connection->delete($tableName, $condition);
