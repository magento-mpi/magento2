<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category    Mage
 * @package     Mage_Bundle
 * @copyright   Copyright (c) 2004-2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Shopping cart item render block
 *
 * @category    Mage
 * @package     Mage_Bundle
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Bundle_Block_Checkout_Cart_Item_Renderer extends Mage_Checkout_Block_Cart_Item_Renderer
{
    private $_getBundleOptionsCache = null;

    /**
     * Get bundled selections (slections-products collection)
     *
     * Returns array of options objects.
     * Each option object will contain array of selections objects
     *
     * @return array
     */
    public function getBundleOptions($useCache = true)
    {
        if ($useCache && (null !== $this->_getBundleOptionsCache)) {
            return $this->_getBundleOptionsCache;
        }

        $bundleOptions = array();
        Varien_Profiler::start('CART:' . __METHOD__);

        /**
         * @var Mage_Bundle_Model_Product_Type
         */
        $typeInstance = $this->getProduct()->getTypeInstance();

        // get bundle options
        $optionsQuoteItemOption =  $this->getItem()->getOptionByCode('bundle_option_ids');
        $bundleOptionsIds = $this->_unserialize($optionsQuoteItemOption->getValue(), array());
        if ($bundleOptionsIds) {
            /**
            * @var Mage_Bundle_Model_Mysql4_Option_Collection
            */
            $optionsCollection = $typeInstance->getOptionsByIds($bundleOptionsIds);

            // get and add bundle selections collection
            $selectionsQuoteItemOption = $this->getItem()->getOptionByCode('bundle_selection_ids');

            $selectionsCollection = $typeInstance->getSelectionsByIds(
                $this->_unserialize($selectionsQuoteItemOption->getValue(), array())
            );
            /**
             * @var array
             */
            $bundleOptions = $optionsCollection->appendSelections($selectionsCollection, true);
            $this->_getBundleOptionsCache = $bundleOptions;
        }

        Varien_Profiler::stop('CART:' . __METHOD__);
        return $bundleOptions;
    }

    /**
     * Obtain final price of selection in a bundle product
     *
     * @param Mage_Catalog_Model_Product $selectionProduct
     * @return decimal
     */
    public function getSelectionFinalPrice($selectionProduct)
    {
        $bundleProduct = $this->getProduct();
        return $bundleProduct->getPriceModel()->getSelectionFinalPrice(
            $bundleProduct, $selectionProduct,
            $this->getQty(),
            $this->getSelectionQty($selectionProduct->getSelectionId())
        );
    }

    /**
     * Get selection quantity
     *
     * @param int $selectionId
     * @return decimal
     */
    public function getSelectionQty($selectionId)
    {
        if ($selectionQty = $this->getProduct()->getCustomOption('selection_qty_' . $selectionId)) {
            return $selectionQty->getValue();
        }
        return 0;
    }

    /**
     * Unserialize string
     *
     * Will return default, if unserialized value will be empty
     *
     * @param string $serializedValue
     * @param mixed $defaultReturn
     * @return mixed
     */
    protected function _unserialize($serializedValue, $defaultReturn = false)
    {
        if (empty($serializedValue)) {
            return $defaultReturn;
        }
        $value = unserialize($serializedValue);
        if (empty($value)) {
            return $defaultReturn;
        }
        return $value;
    }
}
