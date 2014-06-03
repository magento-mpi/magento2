<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/** @var $installer \Magento\Framework\Module\Setup */
$installer = $this;
$installer->startSetup();


$tableName = $installer->getTable('admin_rule');

if ($tableName) {
    /** @var \Magento\Framework\DB\Adapter\AdapterInterface $connection */
    $connection = $installer->getConnection();
    $remove = array('Magento_Rma::rma_manage');
    $connection->delete($tableName, array('resource_id IN (?)' => $remove));
}

$installer->endSetup();
