<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Test\Block;

use Mtf\Block\Block;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;

/**
 * Class Cart
 * Shopping cart block
 *
 * @package Magento\Checkout\Test\Block
 */
class Cart extends Block
{
    /**
     * 'Clear Shopping Cart' button
     *
     * @var string
     */
    protected $clearShoppingCart = '#empty_cart_button';

    /**
     * Get proceed to checkout block
     *
     * @return \Magento\Checkout\Test\Block\Onepage\Link
     */
    public function getOnepageLinkBlock()
    {
        return Factory::getBlockFactory()->getMagentoCheckoutOnepageLink(
            $this->_rootElement->find('.action.primary.checkout')
        );
    }

    /**
     * Get multishipping cart link block
     *
     * @return \Magento\Checkout\Test\Block\Multishipping\Link
     */
    public function getMultishippingLinkBlock()
    {
        return Factory::getBlockFactory()->getMagentoCheckoutMultishippingLink(
            $this->_rootElement->find('[title="Checkout with Multiple Addresses"]')
        );
    }

    /**
     * Press 'Check out with PayPal' button
     */
    public function paypalCheckout()
    {
        $this->_rootElement->find('[data-action=checkout-form-submit]', Locator::SELECTOR_CSS)->click();
    }

    /**
     * Clear shopping cart
     */
    public function clearShoppingCart()
    {
        $clearShoppingCart = $this->_rootElement->find($this->clearShoppingCart);
        if ($clearShoppingCart->isVisible()) {
            $clearShoppingCart->click();
        }
    }

    /**
     * Check if a product has been successfully added to the cart
     *
     * @param \Magento\Catalog\Test\Fixture\Product $product
     * @return mixed|\Mtf\Client\Element
     */
    public function checkAddedProduct($product)
    {
        return $this->_rootElement->find('[title="'. $product->getProductName(). '"]');
    }
}
