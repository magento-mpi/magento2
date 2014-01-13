<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\PricePermissions\Controller\Adminhtml\Product\Initialization\Helper\Plugin\Handler\ProductType;

use Magento\PricePermissions\Controller\Adminhtml\Product\Initialization\Helper\Plugin\HandlerInterface;

class Bundle implements HandlerInterface
{
    public function handle(\Magento\Catalog\Model\Product $product)
    {
        // Handle selection data of bundle products
        if ($product->getTypeId() == \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE) {
            $bundleSelectionsData = $product->getBundleSelectionsData();
            if (is_array($bundleSelectionsData)) {
                // Retrieve original selections data
                $product->getTypeInstance()->setStoreFilter($product->getStoreId(), $product);

                $optionCollection = $product->getTypeInstance()->getOptionsCollection($product);
                $selectionCollection = $product->getTypeInstance()->getSelectionsCollection(
                    $product->getTypeInstance()->getOptionsIds($product),
                    $product
                );

                $origBundleOptions = $optionCollection->appendSelections($selectionCollection);
                $origBundleOptionsAssoc = array();
                foreach ($origBundleOptions as $origBundleOption) {
                    $optionId = $origBundleOption->getOptionId();
                    $origBundleOptionsAssoc[$optionId] = array();
                    if ($origBundleOption->getSelections()) {
                        foreach ($origBundleOption->getSelections() as $selection) {
                            $selectionProductId = $selection->getProductId();
                            $origBundleOptionsAssoc[$optionId][$selectionProductId] = array(
                                'selection_price_type' => $selection->getSelectionPriceType(),
                                'selection_price_value' => $selection->getSelectionPriceValue()
                            );
                        }
                    }
                }
                // Keep previous price and price type for selections
                foreach ($bundleSelectionsData as &$bundleOptionSelections) {
                    foreach ($bundleOptionSelections as &$bundleOptionSelection) {
                        if (!isset($bundleOptionSelection['option_id'])
                            || !isset($bundleOptionSelection['product_id'])
                        ) {
                            continue;
                        }
                        $optionId = $bundleOptionSelection['option_id'];
                        $selectionProductId = $bundleOptionSelection['product_id'];
                        $isDeleted = $bundleOptionSelection['delete'];
                        if (isset($origBundleOptionsAssoc[$optionId][$selectionProductId]) && !$isDeleted) {
                            $bundleOptionSelection['selection_price_type'] =
                                $origBundleOptionsAssoc[$optionId][$selectionProductId]['selection_price_type'];
                            $bundleOptionSelection['selection_price_value'] =
                                $origBundleOptionsAssoc[$optionId][$selectionProductId]['selection_price_value'];
                        } else {
                            // Set zero price for new bundle selections and options
                            $bundleOptionSelection['selection_price_type'] = 0;
                            $bundleOptionSelection['selection_price_value'] = 0;
                        }
                    }
                }
                $product->setData('bundle_selections_data', $bundleSelectionsData);
            }
        }
    }
} 
