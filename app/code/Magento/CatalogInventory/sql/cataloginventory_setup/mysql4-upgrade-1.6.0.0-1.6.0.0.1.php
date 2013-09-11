<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogInventory
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Eav\Model\Entity\Setup */
$installer = $this;
/** @var $connection \Magento\DB\Adapter\Pdo\Mysql */
$connection = $installer->getConnection();
$connection->changeTableEngine(
    $installer->getTable('cataloginventory_stock_status_tmp'),
    \Magento\DB\Adapter\Pdo\Mysql::ENGINE_MEMORY
);
