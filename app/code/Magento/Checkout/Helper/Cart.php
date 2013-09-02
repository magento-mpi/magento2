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
 * Shopping cart helper
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Checkout_Helper_Cart extends Magento_Core_Helper_Url
{
    const XML_PATH_REDIRECT_TO_CART = 'checkout/cart/redirect_to_cart';

    /**
     * Maximal coupon code length according to database table definitions (longer codes are truncated)
     */
    const COUPON_CODE_MAX_LENGTH = 255;

    /**
     * Core store config
     *
     * @var Magento_Core_Model_Store_Config
     */
    protected $_coreStoreConfig = null;

    /**
     * @param Magento_Core_Helper_Context $context
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     */
    public function __construct(
        Magento_Core_Helper_Context $context,
        Magento_Core_Model_Store_Config $coreStoreConfig
    ) {
        $this->_coreStoreConfig = $coreStoreConfig;
        parent::__construct($context);
    }

    /**
     * Retrieve cart instance
     *
     * @return Magento_Checkout_Model_Cart
     */
    public function getCart()
    {
        return Mage::getSingleton('Magento_Checkout_Model_Cart');
    }

    /**
     * Retrieve url for add product to cart
     *
     * @param   Magento_Catalog_Model_Product $product
     * @return  string
     */
    public function getAddUrl($product, $additional = array())
    {
        $continueUrl    = Mage::helper('Magento_Core_Helper_Data')->urlEncode($this->getCurrentUrl());
        $urlParamName   = Magento_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED;

        $routeParams = array(
            $urlParamName   => $continueUrl,
            'product'       => $product->getEntityId()
        );

        if (!empty($additional)) {
            $routeParams = array_merge($routeParams, $additional);
        }

        if ($product->hasUrlDataObject()) {
            $routeParams['_store'] = $product->getUrlDataObject()->getStoreId();
            $routeParams['_store_to_url'] = true;
        }

        if ($this->_getRequest()->getRouteName() == 'checkout'
            && $this->_getRequest()->getControllerName() == 'cart') {
            $routeParams['in_cart'] = 1;
        }

        return $this->_getUrl('checkout/cart/add', $routeParams);
    }

    /**
     * Retrieve url for remove product from cart
     *
     * @param   Magento_Sales_Quote_Item $item
     * @return  string
     */
    public function getRemoveUrl($item)
    {
        $params = array(
            'id'=>$item->getId(),
            Magento_Core_Controller_Front_Action::PARAM_NAME_BASE64_URL => $this->getCurrentBase64Url()
        );
        return $this->_getUrl('checkout/cart/delete', $params);
    }

    /**
     * Retrieve shopping cart url
     *
     * @return unknown
     */
    public function getCartUrl()
    {
        return $this->_getUrl('checkout/cart');
    }

    /**
     * Retrieve current quote instance
     *
     * @return Magento_Sales_Model_Quote
     */
    public function getQuote()
    {
        return Mage::getSingleton('Magento_Checkout_Model_Session')->getQuote();
    }

    /**
     * Get shopping cart items count
     *
     * @return int
     */
    public function getItemsCount()
    {
        return $this->getCart()->getItemsCount();
    }

    /**
     * Get shopping cart summary qty
     *
     * @return decimal
     */
    public function getItemsQty()
    {
        return $this->getCart()->getItemsQty();
    }

    /**
     * Get shopping cart items summary (inchlude config settings)
     *
     * @return decimal
     */
    public function getSummaryCount()
    {
        return $this->getCart()->getSummaryQty();
    }

    /**
     * Check qoute for virtual products only
     *
     * @return bool
     */
    public function getIsVirtualQuote()
    {
        return $this->getQuote()->isVirtual();
    }

    /**
     * Checks if customer should be redirected to shopping cart after adding a product
     *
     * @param int|string|Magento_Core_Model_Store $store
     * @return bool
     */
    public function getShouldRedirectToCart($store = null)
    {
        return $this->_coreStoreConfig->getConfigFlag(self::XML_PATH_REDIRECT_TO_CART, $store);
    }
}
