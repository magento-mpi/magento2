<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @copyright   {copyright}
 * @license     {license_link}
 */

$installFile = dirname(__FILE__) . DS . 'upgrade-1.6.0.0-1.6.0.0.1.php';
if (file_exists($installFile)) {
    include $installFile;
}

/** @var $installer \Magento\Catalog\Model\Resource\Setup */
$installer = $this;
/** @var $connection \Magento\DB\Adapter\Pdo\Mysql */
$connection = $installer->getConnection();
$memoryTables = array(
    'catalog_product_index_price_bundle_opt_tmp',
    'catalog_product_index_price_bundle_sel_tmp',
    'catalog_product_index_price_bundle_tmp',
);

foreach ($memoryTables as $table) {
    $connection->changeTableEngine($installer->getTable($table), \Magento\DB\Adapter\Pdo\Mysql::ENGINE_MEMORY);
}
