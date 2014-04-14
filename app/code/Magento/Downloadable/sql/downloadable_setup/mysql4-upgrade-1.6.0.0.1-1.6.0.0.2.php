<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @copyright   {copyright}
 * @license     {license_link}
 */


/** @var $this \Magento\Catalog\Model\Resource\Setup */
$installFile = __DIR__ . '/upgrade-1.6.0.0.1-1.6.0.0.2.php';

/** @var \Magento\Filesystem\Directory\Read $moduleDirectory */
$moduleDirectory = $this->getFilesystem()->getDirectoryRead(\Magento\App\Filesystem::MODULES_DIR);
if ($moduleDirectory->isExist($moduleDirectory->getRelativePath($installFile))) {
    include $installFile;
}

/** @var $connection \Magento\DB\Adapter\Pdo\Mysql */
$connection = $this->getConnection();
$connection->changeTableEngine(
    $this->getTable('catalog_product_index_price_downlod_tmp'),
    \Magento\DB\Adapter\Pdo\Mysql::ENGINE_MEMORY
);
