<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Test\Block\Cart;

use Mtf\Client\Element\Locator;

/**
 * Class MiniCartItem
 * Product item block on mini Cart
 */
class MiniCartItem extends AbstractCartItem
{
    /**
     * Selector for "Remove item" button
     *
     * @var string
     */
    protected $removeItem = '.action.delete';

    /**
     * Counter qty locator
     *
     * @var string
     */
    protected $counterQty = './/span[@class="counter qty"]';

    /**
     * Item product in the mimi shopping cart locator
     *
     * @var string
     */
    protected $product = '//div[contains(@class,"product-item-details")]//a[.="%s"]';

    /**
     * Remove product item from mini cart
     *
     * @param int $qty
     * @return void
     */
    public function removeItemFromMiniCart($qty)
    {
        $this->_rootElement->find($this->removeItem)->click();
        $this->_rootElement->acceptAlert();
        if ($qty > 1) {
            $this->waitCounterQty();
        }
    }

    /**
     * Wait counter qty visibility
     *
     * @return mixed
     */
    protected function waitCounterQty()
    {
        $browser = $this->browser;
        $selector = $this->counterQty;
        $strategy = Locator::SELECTOR_XPATH;
        return $browser->waitUntil(
            function () use ($browser, $selector, $strategy) {
                $counterQty = $browser->find($selector, Locator::SELECTOR_XPATH);
                return $counterQty->isVisible() ? true : null;
            }
        );
    }
}
