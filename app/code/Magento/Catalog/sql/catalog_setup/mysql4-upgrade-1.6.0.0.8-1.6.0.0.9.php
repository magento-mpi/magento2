<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

$installFile = __DIR__ . '/upgrade-1.6.0.0.8-1.6.0.0.9.php';
if (file_exists($installFile)) {
    include $installFile;
}

/** @var $installer \Magento\Catalog\Model\Resource\Setup */
$installer = $this;
/** @var $connection \Magento\DB\Adapter\Pdo\Mysql */
$connection = $installer->getConnection();
$memoryTables = array(
    'catalog_category_anc_categs_index_tmp',
    'catalog_category_anc_products_index_tmp',
    'catalog_category_product_index_enbl_tmp',
    'catalog_category_product_index_tmp',
    'catalog_product_index_eav_decimal_tmp',
    'catalog_product_index_eav_tmp',
    'catalog_product_index_price_cfg_opt_agr_tmp',
    'catalog_product_index_price_cfg_opt_tmp',
    'catalog_product_index_price_final_tmp',
    'catalog_product_index_price_opt_agr_tmp',
    'catalog_product_index_price_opt_tmp',
    'catalog_product_index_price_tmp',
);

foreach ($memoryTables as $table) {
    $connection->changeTableEngine($installer->getTable($table), \Magento\DB\Adapter\Pdo\Mysql::ENGINE_MEMORY);
}
