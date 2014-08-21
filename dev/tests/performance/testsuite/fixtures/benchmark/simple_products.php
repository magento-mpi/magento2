<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var \Magento\TestFramework\Application $this */
$simpleProductsCount = \Magento\TestFramework\Helper\Cli::getOption('simple_products', 100);
$distributeSimples = \Magento\TestFramework\Helper\Cli::getOption('distribute_simple_products', true);


if ($distributeSimples) {
    /** @var $helper \Magento\TestFramework\Helper\Categories */
    $helper = $this->getObjectManager()->create('Magento\TestFramework\Helper\Categories');
    $productCategory = function ($index) use ($helper) {
        return $helper->getCategoryForImport($index);
    };
} else {
    $productCategory = \Magento\TestFramework\Helper\Cli::getOption('simple_category_path', 'Category 1');
}
/**
 * Create products
 */
$pattern = array(
    '_attribute_set' => 'Default',
    '_type' => \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE,
    '_product_websites' => 'base',
    '_category' => $productCategory,
    'name' => 'Simple Product %s',
    'short_description' => 'Short simple product description %s',
    'weight' => 1,
    'description' => 'Full simple product Description %s',
    'sku' => 'product_dynamic_%s',
    'price' => 10,
    'visibility' => \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH,
    'status' => \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED,
    'tax_class_id' => 2,

    // actually it saves without stock data, but by default system won't show on the frontend products out of stock
    'is_in_stock' => 1,
    'qty' => 100500,
    'use_config_min_qty' => '1',
    'use_config_backorders' => '1',
    'use_config_min_sale_qty' => '1',
    'use_config_max_sale_qty' => '1',
    'use_config_notify_stock_qty' => '1',
    'use_config_manage_stock' => '1',
    'use_config_qty_increments' => '1',
    'use_config_enable_qty_inc' => '1',
    'stock_id' => \Magento\CatalogInventory\Model\Stock::DEFAULT_STOCK_ID
);
$generator = new \Magento\TestFramework\ImportExport\Fixture\Generator($pattern, $simpleProductsCount);
/** @var \Magento\ImportExport\Model\Import $import */
$import = $this->getObjectManager()->create(
    'Magento\ImportExport\Model\Import',
    array('data' => array('entity' => 'catalog_product', 'behavior' => 'append'))
);
// it is not obvious, but the validateSource() will actually save import queue data to DB
$import->validateSource($generator);
// this converts import queue into actual entities
$import->importSource();
