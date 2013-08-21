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
 * Shopping cart interface
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */

interface Magento_Checkout_Model_Cart_Interface
{
    /**
     * Add product to shopping cart (quote)
     *
     * @param   int|Magento_Catalog_Model_Product $productInfo
     * @param   mixed                          $requestInfo
     * @return  Magento_Checkout_Model_Cart_Interface
     */
    public function addProduct($productInfo, $requestInfo = null);

    /**
     * Save cart
     *
     * @abstract
     * @return Magento_Checkout_Model_Cart_Interface
     */
    public function saveQuote();

    /**
     * Associate quote with the cart
     *
     * @abstract
     * @param $quote Magento_Sales_Model_Quote
     * @return Magento_Checkout_Model_Cart_Interface
     */
    public function setQuote(Magento_Sales_Model_Quote $quote);

    /**
     * Get quote object associated with cart
     * @abstract
     * @return Magento_Sales_Model_Quote
     */
    public function getQuote();
}
