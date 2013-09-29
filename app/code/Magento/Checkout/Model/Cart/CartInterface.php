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

namespace Magento\Checkout\Model\Cart;

interface CartInterface
{
    /**
     * Add product to shopping cart (quote)
     *
     * @param   int|\Magento\Catalog\Model\Product $productInfo
     * @param   mixed                          $requestInfo
     * @return  \Magento\Checkout\Model\Cart\CartInterface
     */
    public function addProduct($productInfo, $requestInfo = null);

    /**
     * Save cart
     *
     * @abstract
     * @return \Magento\Checkout\Model\Cart\CartInterface
     */
    public function saveQuote();

    /**
     * Associate quote with the cart
     *
     * @abstract
     * @param $quote \Magento\Sales\Model\Quote
     * @return \Magento\Checkout\Model\Cart\CartInterface
     */
    public function setQuote(\Magento\Sales\Model\Quote $quote);

    /**
     * Get quote object associated with cart
     * @abstract
     * @return \Magento\Sales\Model\Quote
     */
    public function getQuote();
}
