<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */


/** @var $this \Magento\Catalog\Model\Resource\Setup */
$installFile = __DIR__ . '/upgrade-1.6.0.0.1-1.6.0.0.2.php';

/** @var \Magento\Framework\Filesystem\Directory\Read $moduleDirectory */
$moduleDirectory = $this->getFilesystem()->getDirectoryRead(\Magento\Framework\App\Filesystem::MODULES_DIR);
if ($moduleDirectory->isExist($moduleDirectory->getRelativePath($installFile))) {
    include $installFile;
}

/** @var $connection \Magento\Framework\DB\Adapter\Pdo\Mysql */
$connection = $this->getConnection();
$connection->changeTableEngine(
    $this->getTable('catalog_product_index_price_downlod_tmp'),
    \Magento\Framework\DB\Adapter\Pdo\Mysql::ENGINE_MEMORY
);
