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
    /** @var $connection \Magento\Framework\DB\Adapter\AdapterInterface */
    $connection = $installer->getConnection();
    $remove = array('Magento_Rma::rma_manage');
    $connection->delete($tableName, array('resource_id IN (?)' => $remove));
}

$installer->endSetup();
