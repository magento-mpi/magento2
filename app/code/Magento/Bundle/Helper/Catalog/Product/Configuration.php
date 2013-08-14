<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Helper for fetching properties by product configurational item
 *
 * @category   Magento
 * @package    Magento_Bundle
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Bundle_Helper_Catalog_Product_Configuration extends Magento_Core_Helper_Abstract
    implements Magento_Catalog_Helper_Product_Configuration_Interface
{
    /**
     * Get selection quantity
     *
     * @param Magento_Catalog_Model_Product $product
     * @param int $selectionId
     *
     * @return decimal
     */
    public function getSelectionQty($product, $selectionId)
    {
        $selectionQty = $product->getCustomOption('selection_qty_' . $selectionId);
        if ($selectionQty) {
            return $selectionQty->getValue();
        }
        return 0;
    }

    /**
     * Obtain final price of selection in a bundle product
     *
     * @param Magento_Catalog_Model_Product_Configuration_Item_Interface $item
     * @param Magento_Catalog_Model_Product $selectionProduct
     *
     * @return decimal
     */
    public function getSelectionFinalPrice(Magento_Catalog_Model_Product_Configuration_Item_Interface $item,
        $selectionProduct)
    {
        $selectionProduct->unsetData('final_price');
        return $item->getProduct()->getPriceModel()->getSelectionFinalTotalPrice(
            $item->getProduct(),
            $selectionProduct,
            $item->getQty() * 1,
            $this->getSelectionQty($item->getProduct(), $selectionProduct->getSelectionId()),
            false,
            true
        );
    }

    /**
     * Get bundled selections (slections-products collection)
     *
     * Returns array of options objects.
     * Each option object will contain array of selections objects
     *
     * @return array
     */
    public function getBundleOptions(Magento_Catalog_Model_Product_Configuration_Item_Interface $item)
    {
        $options = array();
        $product = $item->getProduct();

        /**
         * @var Magento_Bundle_Model_Product_Type
         */
        $typeInstance = $product->getTypeInstance();

        // get bundle options
        $optionsQuoteItemOption = $item->getOptionByCode('bundle_option_ids');
        $bundleOptionsIds = $optionsQuoteItemOption ? unserialize($optionsQuoteItemOption->getValue()) : array();
        if ($bundleOptionsIds) {
            /**
            * @var Magento_Bundle_Model_Resource_Option_Collection
            */
            $optionsCollection = $typeInstance->getOptionsByIds($bundleOptionsIds, $product);

            // get and add bundle selections collection
            $selectionsQuoteItemOption = $item->getOptionByCode('bundle_selection_ids');

            $bundleSelectionIds = unserialize($selectionsQuoteItemOption->getValue());

            if (!empty($bundleSelectionIds)) {
                $selectionsCollection = $typeInstance->getSelectionsByIds(
                    unserialize($selectionsQuoteItemOption->getValue()),
                    $product
                );

                $bundleOptions = $optionsCollection->appendSelections($selectionsCollection, true);
                foreach ($bundleOptions as $bundleOption) {
                    if ($bundleOption->getSelections()) {
                        $option = array(
                            'label' => $bundleOption->getTitle(),
                            'value' => array()
                        );

                        $bundleSelections = $bundleOption->getSelections();

                        foreach ($bundleSelections as $bundleSelection) {
                            $qty = $this->getSelectionQty($product, $bundleSelection->getSelectionId()) * 1;
                            if ($qty) {
                                $option['value'][] = $qty . ' x ' . $this->escapeHtml($bundleSelection->getName())
                                    . ' ' . Mage::helper('Magento_Core_Helper_Data')->currency(
                                        $this->getSelectionFinalPrice($item, $bundleSelection)
                                    );
                            }
                        }

                        if ($option['value']) {
                            $options[] = $option;
                        }
                    }
                }
            }
        }

        return $options;
    }

    /**
     * Retrieves product options list
     *
     * @param Magento_Catalog_Model_Product_Configuration_Item_Interface $item
     * @return array
     */
    public function getOptions(Magento_Catalog_Model_Product_Configuration_Item_Interface $item)
    {
        return array_merge(
            $this->getBundleOptions($item),
            Mage::helper('Magento_Catalog_Helper_Product_Configuration')->getCustomOptions($item)
        );
    }
}
