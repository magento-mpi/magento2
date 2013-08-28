<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * API Resource class for product
 */
class Magento_Checkout_Model_Api_Resource_Product extends Magento_Checkout_Model_Api_Resource
{
    /**
     * Default ignored attribute codes
     *
     * @var array
     */
    protected $_ignoredAttributeCodes = array('entity_id', 'attribute_set_id', 'entity_type_id');

    /**
     * Catalog product
     *
     * @var Magento_Catalog_Helper_Product
     */
    protected $_catalogProduct = null;

    /**
     * Initialize dependencies.
     *
     *
     *
     * @param Magento_Catalog_Helper_Product $catalogProduct
     * @param Magento_Api_Helper_Data $apiHelper
     */
    public function __construct(
        Magento_Catalog_Helper_Product $catalogProduct,
        Magento_Api_Helper_Data $apiHelper
    ) {
        $this->_catalogProduct = $catalogProduct;
        parent::__construct($apiHelper);
    }

    /**
     * Return loaded product instance
     *
     * @param  int|string $productId (SKU or ID)
     * @param  int|string $store
     * @param  string $identifierType
     * @return Magento_Catalog_Model_Product
     */
    protected function _getProduct($productId, $store = null, $identifierType = null)
    {
        $product = $this->_catalogProduct->getProduct(
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
     * @param Magento_Sales_Model_Quote $quote
     * @param Magento_Catalog_Model_Product $product
     * @param Magento_Object $requestInfo
     * @return Magento_Sales_Model_Quote_Item
     * @throw Magento_Core_Exception
     */
    protected function _getQuoteItemByProduct(
        Magento_Sales_Model_Quote $quote,
        Magento_Catalog_Model_Product $product,
        Magento_Object $requestInfo
    ) {
        $cartCandidates = $product->getTypeInstance()
            ->prepareForCartAdvanced($requestInfo,
                    $product,
                    Magento_Catalog_Model_Product_Type_Abstract::PROCESS_MODE_FULL
            );

        /**
         * Error message
         */
        if (is_string($cartCandidates)) {
            Mage::throwException($cartCandidates);
        }

        /**
         * If prepare process return one object
         */
        if (!is_array($cartCandidates)) {
            $cartCandidates = array($cartCandidates);
        }

        /** @var $item Magento_Sales_Model_Quote_Item */
        $item = null;
        foreach ($cartCandidates as $candidate) {
            if ($candidate->getParentProductId()) {
                continue;
            }

            $item = $quote->getItemByProduct($candidate);
        }

        if (is_null($item)) {
            $item = Mage::getModel('Magento_Sales_Model_Quote_Item');
        }

        return $item;
    }
}
