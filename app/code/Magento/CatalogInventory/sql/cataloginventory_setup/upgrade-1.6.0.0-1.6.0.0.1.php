<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Setup\Module\SetupModule */
$installer = $this;
$connection = $installer->getConnection();
$connection->changeTableEngine(
    $installer->getTable('cataloginventory_stock_status_tmp'),
    \Magento\Framework\DB\Adapter\Pdo\Mysql::ENGINE_MEMORY
);
