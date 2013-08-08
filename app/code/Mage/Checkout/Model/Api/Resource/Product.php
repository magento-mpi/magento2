<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * API Resource class for product
 */
class Mage_Checkout_Model_Api_Resource_Product extends Mage_Checkout_Model_Api_Resource
{
    /**
     * Default ignored attribute codes
     *
     * @var array
     */
    protected $_ignoredAttributeCodes = array('entity_id', 'attribute_set_id', 'entity_type_id');

    /**
     * Return loaded product instance
     *
     * @param  int|string $productId (SKU or ID)
     * @param  int|string $store
     * @param  string $identifierType
     * @return Mage_Catalog_Model_Product
     */
    protected function _getProduct($productId, $store = null, $identifierType = null)
    {
        $product = Mage::helper('Mage_Catalog_Helper_Product')->getProduct(
            $productId, $this->_getStoreId($store), $identifierType
        );
        if (is_null($product->getId())) {
            $this->_fault('product_not_exists');
        }
        return $product;
    }

    /**
     * Get request for product add to cart procedure
     *
     * @param   mixed $requestInfo
     * @return  Magento_Object
     */
    protected function _getProductRequest($requestInfo)
    {
        if ($requestInfo instanceof Magento_Object) {
            $request = $requestInfo;
        } elseif (is_numeric($requestInfo)) {
            $request = new Magento_Object();
            $request->setQty($requestInfo);
        } else {
            $request = new Magento_Object($requestInfo);
        }

        if (!$request->hasQty()) {
            $request->setQty(1);
        }
        return $request;
    }

    /**
     * Get QuoteItem by Product and request info
     *
     * @param Mage_Sales_Model_Quote $quote
     * @param Mage_Catalog_Model_Product $product
     * @param Magento_Object $requestInfo
     * @return Mage_Sales_Model_Quote_Item
     * @throw Magento_Core_Exception
     */
    protected function _getQuoteItemByProduct(Mage_Sales_Model_Quote $quote,
                            Mage_Catalog_Model_Product $product,
                            Magento_Object $requestInfo)
    {
        $cartCandidates = $product->getTypeInstance()
                        ->prepareForCartAdvanced($requestInfo,
                                $product,
                                Mage_Catalog_Model_Product_Type_Abstract::PROCESS_MODE_FULL
        );

        /**
         * Error message
         */
        if (is_string($cartCandidates)) {
            throw Mage::throwException($cartCandidates);
        }

        /**
         * If prepare process return one object
         */
        if (!is_array($cartCandidates)) {
            $cartCandidates = array($cartCandidates);
        }

        /** @var $item Mage_Sales_Model_Quote_Item */
        $item = null;
        foreach ($cartCandidates as $candidate) {
            if ($candidate->getParentProductId()) {
                continue;
            }

            $item = $quote->getItemByProduct($candidate);
        }

        if (is_null($item)) {
            $item = Mage::getModel('Mage_Sales_Model_Quote_Item');
        }

        return $item;
    }
}
