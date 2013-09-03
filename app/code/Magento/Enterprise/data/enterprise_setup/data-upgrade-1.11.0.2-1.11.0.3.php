<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Enterprise
 * @copyright   {copyright}
 * @license     {license_link}
 */
/** @var $installer Magento_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();


$tableName = $installer->getTable('admin_rule');

if ($tableName) {
    /** @var \Magento\DB\Adapter\AdapterInterface $connection */
    $connection = $installer->getConnection();
    $remove = array(
        'Magento_Rma::rma_manage',
    );
    $connection->delete($tableName, array('resource_id IN (?)' => $remove));
}

$installer->endSetup();
