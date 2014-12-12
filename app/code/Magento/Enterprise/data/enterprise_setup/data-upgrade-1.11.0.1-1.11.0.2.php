<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
