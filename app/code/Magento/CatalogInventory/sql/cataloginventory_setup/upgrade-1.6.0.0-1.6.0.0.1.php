<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $this \Magento\Eav\Model\Entity\Setup */

/** @var $connection \Magento\Framework\DB\Adapter\Pdo\Mysql */
$connection = $this->getConnection();
$connection->changeTableEngine(
    $this->getTable('cataloginventory_stock_status_tmp'),
    \Magento\Framework\DB\Adapter\Pdo\Mysql::ENGINE_MEMORY
);
