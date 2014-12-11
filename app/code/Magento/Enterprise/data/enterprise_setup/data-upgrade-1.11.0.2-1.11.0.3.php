<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/** @var $installer \Magento\Enterprise\Model\Resource\Setup */
$installer = $this;
$installer->startSetup();

$tableName = $installer->getTable('authorization_rule');

if ($tableName) {
    $connection = $installer->getConnection();
    $remove = ['Magento_Rma::rma_manage'];
    $connection->delete($tableName, ['resource_id IN (?)' => $remove]);
}

$installer->endSetup();
