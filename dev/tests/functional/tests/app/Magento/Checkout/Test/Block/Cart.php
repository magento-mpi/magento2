<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Test\Block;

use Exception;
use Mtf\Block\Block;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;
use Magento\Checkout\Test\Block\Onepage\Link;
use Mtf\Fixture\FixtureInterface;

/**
 * Class Cart
 * Shopping cart block
 */
class Cart extends Block
{
    /**
     * Selector for cart item block
     *
     * @var string
     */
    protected $cartItemByProductName = './/tr[contains(@class,"item-info") and (.//*[contains(@class,"product-item-name")]/a[.="%s"])]';

    /**
     * Proceed to checkout block
     *
     * @var string
     */
    protected $onepageLinkBlock = '.action.primary.checkout';

    /**
     * 'Clear Shopping Cart' button
     *
     * @var string
     */
    protected $clearShoppingCart = '#empty_cart_button';

    /**
     * 'Update Shopping Cart' button
     *
     * @var string
     */
    protected $updateShoppingCart = '[name="update_cart_action"]';

    /**
     * Get cart item block
     *
     * @param FixtureInterface $product
     * @return \Magento\Checkout\Test\Block\Cart\CartItem
     */
    public function getCartItem(FixtureInterface $product)
    {
        $cartItem = $this->_rootElement->find(
            sprintf($this->cartItemByProductName, $product->getName()),
            Locator::SELECTOR_XPATH
        );
        return $this->blockFactory->create(
            'Magento\Checkout\Test\Block\Cart\CartItem',
            ['element' => $cartItem]
        );
    }

    /**
     * Get proceed to checkout block
     *
     * @return Link
     */
    public function getOnepageLinkBlock()
    {
        return Factory::getBlockFactory()->getMagentoCheckoutOnepageLink(
            $this->_rootElement->find($this->onepageLinkBlock, Locator::SELECTOR_CSS)
        );
    }

    /**
     * Press 'Check out with PayPal' button
     *
     * @return void
     */
    public function paypalCheckout()
    {
        $this->_rootElement->find('[data-action=checkout-form-submit]', Locator::SELECTOR_CSS)->click();
    }

    /**
     * Returns the total discount price
     *
     * @return string
     * @throws Exception
     */
    public function getDiscountTotal()
    {
        $element = $this->_rootElement->find(
            '//table[@id="shopping-cart-totals-table"]' .
            '//tr[normalize-space(td)="Discount"]' .
            '//td[@class="amount"]//span[@class="price"]',
            Locator::SELECTOR_XPATH
        );
        if (!$element->isVisible()) {
            throw new Exception('Error could not find the Discount Total in the HTML');
        }
        return $element->getText();
    }

    /**
     * Clear shopping cart
     *
     * @return void
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
     * @param FixtureInterface $product
     * @return boolean
     */
    public function isProductInShoppingCart(FixtureInterface $product)
    {
        return $this->getCartItem($product)->isVisible();
    }

    /**
     * Update shopping cart
     *
     * @return void
     */
    public function updateShoppingCart()
    {
        $this->_rootElement->find($this->updateShoppingCart, Locator::SELECTOR_CSS)->click();
    }
}
