<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $this \Magento\Catalog\Model\Resource\Setup */
$installFile = __DIR__ . '/upgrade-1.6.0.0-1.6.0.0.1.php';

/** @var \Magento\Filesystem\Directory\Read $modulesDirectory */
$modulesDirectory = $this->getFilesystem()->getDirectoryRead(\Magento\Filesystem::MODULES);

if ($modulesDirectory->isExist($modulesDirectory->getRelativePath($installFile))) {
    include $installFile;
}

/** @var $connection \Magento\DB\Adapter\Pdo\Mysql */
$connection = $installer->getConnection();
$memoryTables = array(
    'catalog_product_index_price_bundle_opt_tmp',
    'catalog_product_index_price_bundle_sel_tmp',
    'catalog_product_index_price_bundle_tmp',
);

foreach ($memoryTables as $table) {
    $connection->changeTableEngine($this->getTable($table), \Magento\DB\Adapter\Pdo\Mysql::ENGINE_MEMORY);
}
