<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
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
