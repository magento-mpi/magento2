<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

require __DIR__ . '/../../Checkout/_files/simple_product.php';

/** @var $bundleProduct \Magento\Catalog\Model\Product */
$bundleProduct = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Catalog\Model\Product');
$bundleProduct->setTypeId(
    \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE
)->setId(
    3
)->setAttributeSetId(
    4
)->setWebsiteIds(
    array(1)
)->setName(
    'Bundle Product'
)->setSku(
    'bundle-product'
)->setDescription(
    'Description with <b>html tag</b>'
)->setShortDescription(
    'Bundle'
)->setVisibility(
    \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH
)->setStatus(
    \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED
)->setStockData(
    array(
        'use_config_manage_stock' => 0,
        'manage_stock' => 0,
        'use_config_enable_qty_increments' => 1,
        'use_config_qty_increments' => 1,
        'is_in_stock' => 0
    )
)->setBundleOptionsData(
    array(
        array(
            'title' => 'Bundle Product Items',
            'default_title' => 'Bundle Product Items',
            'type' => 'select',
            'required' => 1,
            'delete' => '',
            'position' => 0,
            'option_id' => ''
        )
    )
)->setBundleSelectionsData(
    array(
        array(
            array(
                'product_id' => 1, // fixture product
                'selection_qty' => 1,
                'selection_can_change_qty' => 1,
                'delete' => '',
                'position' => 0,
                'selection_price_type' => 0,
                'selection_price_value' => 0.0,
                'option_id' => '',
                'selection_id' => '',
                'is_default' => 1
            )
        )
    )
)->setCanSaveBundleSelections(
    true
)->setAffectBundleProductSelections(
    true
)->save();

/** @var $product \Magento\Catalog\Model\Product */
$product = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Catalog\Model\Product');
$product->load($bundleProduct->getId());

/** @var $typeInstance \Magento\Bundle\Model\Product\Type */
//Load options
$typeInstance = $product->getTypeInstance();
$typeInstance->setStoreFilter($product->getStoreId(), $product);
$optionCollection = $typeInstance->getOptionsCollection($product);
$selectionCollection = $typeInstance->getSelectionsCollection($typeInstance->getOptionsIds($product), $product);

$bundleOptions = array();
$bundleOptionsQty = array();
/** @var $option \Magento\Bundle\Model\Option */
foreach ($optionCollection as $option) {
    /** @var $selection \Magento\Bundle\Model\Selection */
    $selection = $selectionCollection->getFirstItem();
    $bundleOptions[$option->getId()] = $selection->getSelectionId();
    $bundleOptionsQty[$option->getId()] = 1;
}

$requestInfo = new \Magento\Framework\Object(
    array('qty' => 1, 'bundle_option' => $bundleOptions, 'bundle_option_qty' => $bundleOptionsQty)
);
$product->setSkipCheckRequiredOption(true);

require __DIR__ . '/../../Checkout/_files/cart.php';
