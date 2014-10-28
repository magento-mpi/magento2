<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */


/** @var $this Magento\Setup\Module\SetupModule */
/** @var $connection Magento\Framework\DB\Adapter\Pdo\Mysql */
$connection = $this->getConnection();
$connection->changeTableEngine(
    $this->getTable('catalog_product_index_price_downlod_tmp'),
    \Magento\Framework\DB\Adapter\Pdo\Mysql::ENGINE_MEMORY
);
