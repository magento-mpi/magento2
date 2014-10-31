<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Catalog\Model\Resource\Setup */
$installer = $this;

/** @var $connection \Magento\Framework\DB\Adapter\Pdo\Mysql */
$connection = $installer->getConnection();
$memoryTables = array(
    'catalog_category_product_index_tmp',
    'catalog_product_index_eav_decimal_tmp',
    'catalog_product_index_eav_tmp',
    'catalog_product_index_price_cfg_opt_agr_tmp',
    'catalog_product_index_price_cfg_opt_tmp',
    'catalog_product_index_price_final_tmp',
    'catalog_product_index_price_opt_agr_tmp',
    'catalog_product_index_price_opt_tmp',
    'catalog_product_index_price_tmp'
);

foreach ($memoryTables as $table) {
    $connection->changeTableEngine($this->getTable($table), \Magento\Framework\DB\Adapter\Pdo\Mysql::ENGINE_MEMORY);
}
