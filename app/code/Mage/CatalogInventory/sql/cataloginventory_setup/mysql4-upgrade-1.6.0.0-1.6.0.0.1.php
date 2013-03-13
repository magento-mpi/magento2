<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_CatalogInventory
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Mage_Eav_Model_Entity_Setup */
$installer = $this;
/** @var $connection Varien_Db_Adapter_Pdo_Mysql */
$connection = $installer->getConnection();
$connection->changeTableEngine(
    $installer->getTable('cataloginventory_stock_status_tmp'),
    Varien_Db_Adapter_Pdo_Mysql::ENGINE_MEMORY
);
