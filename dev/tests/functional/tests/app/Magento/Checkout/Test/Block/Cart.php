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
use Magento\AdvancedCheckout\Test\Block\Sku\Products\Info;
use Magento\Checkout\Test\Block\Cart\CartItem;

/**
 * Class Cart
 * Shopping cart block
 */
class Cart extends Block
{
    // @codingStandardsIgnoreStart
    /**
     * Selector for cart item block
     *
     * @var string
     */
    protected $cartItemByProductName = './/tbody[contains(@class,"cart item") and (.//*[contains(@class,"product-item-name")]/a[.="%s"])]';
    // @codingStandardsIgnoreEnd

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
     * Cart empty block selector
     *
     * @var string
     */
    protected $cartEmpty = '.cart-empty';

    /**
     * Failed item block selector
     *
     * @var string
     */
    protected $failedItem = '//*[@id="failed-products-table"]//tr[contains(@class,"info") and //div[contains(.,"%s")]]';

    /**
     * Selector for not editable cart item block
     *
     * @var string
     */
    protected $notEditableCartItem = './/tr[contains(@class,"item-info") and contains(.,"%s")]';

    /**
     * Get cart item block
     *
     * @param FixtureInterface $product
     * @return \Magento\Checkout\Test\Block\Cart\CartItem
     */
    public function getCartItem(FixtureInterface $product)
    {
        $dataConfig = $product->getDataConfig();
        $typeId = isset($dataConfig['type_id']) ? $dataConfig['type_id'] : null;
        $cartItem = null;

        if ($this->hasRender($typeId)) {
            $cartItem = $this->callRender($typeId, 'getCartItem', ['product' => $product]);
        } else {
            $cartItemBlock = $this->_rootElement->find(
                sprintf($this->cartItemByProductName, $product->getName()),
                Locator::SELECTOR_XPATH
            );
            $cartItem = $this->blockFactory->create(
                'Magento\Checkout\Test\Block\Cart\CartItem',
                ['element' => $cartItemBlock]
            );
        }

        return $cartItem;
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

    /**
     * Check that cart is empty
     *
     * @return bool
     */
    public function cartIsEmpty()
    {
        return $this->_rootElement->find($this->cartEmpty, Locator::SELECTOR_CSS)->isVisible();
    }

    /**
     * Get failed item block
     *
     * @param FixtureInterface $product
     * @return Info
     */
    protected function getFailedItemBlock(FixtureInterface $product)
    {
        $failedItemBlockSelector = sprintf($this->failedItem, $product->getSku());
        return $this->blockFactory->create(
            'Magento\AdvancedCheckout\Test\Block\Sku\Products\Info',
            ['element' => $this->_rootElement->find($failedItemBlockSelector, Locator::SELECTOR_XPATH)]
        );
    }

    /**
     * Get failed item error message
     *
     * @param FixtureInterface $product
     * @return string
     */
    public function getFailedItemErrorMessage(FixtureInterface $product)
    {
        $failedItemBlock = $this->getFailedItemBlock($product);

        return $failedItemBlock->getErrorMessage();
    }

    /**
     * Get not editable cart item block
     *
     * @param FixtureInterface $product
     * @return CartItem
     */
    public function getNotEditableCartItem(FixtureInterface $product)
    {
        $cartItem = $this->_rootElement->find(
            sprintf($this->notEditableCartItem, $product->getName()),
            Locator::SELECTOR_XPATH
        );
        return $this->blockFactory->create(
            'Magento\Checkout\Test\Block\Cart\CartItem',
            ['element' => $cartItem]
        );
    }

    /**
     * Check that "Specify the product's options" link is visible
     *
     * @param FixtureInterface $product
     * @return bool
     */
    public function specifyProductOptionsLinkIsVisible(FixtureInterface $product)
    {
        $failedItemBlock = $this->getFailedItemBlock($product);

        return $failedItemBlock->linkIsVisible();
    }

    /**
     * Click "Specify the product's options" link
     *
     * @param FixtureInterface $product
     * @return void
     */
    public function clickSpecifyProductOptionsLink(FixtureInterface $product)
    {
        $failedItemBlock = $this->getFailedItemBlock($product);
        $failedItemBlock->clickOptionsLink();
    }
}
