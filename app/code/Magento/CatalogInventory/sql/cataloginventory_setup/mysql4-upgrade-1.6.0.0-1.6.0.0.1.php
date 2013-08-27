<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogInventory
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Magento_Eav_Model_Entity_Setup */
$installer = $this;
/** @var $connection Magento_DB_Adapter_Pdo_Mysql */
$connection = $installer->getConnection();
$connection->changeTableEngine(
    $installer->getTable('cataloginventory_stock_status_tmp'),
    Magento_DB_Adapter_Pdo_Mysql::ENGINE_MEMORY
);
