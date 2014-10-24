<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Weee\Test\Block\Cart;

use Magento\Checkout\Test\Block\Cart\AbstractCartItem;
use Mtf\Client\Element\Locator;

/**
 * Class Price
 * Product item fpt block on cart page
 */
class Price extends AbstractCartItem
{
    /**
     * Selector for price
     *
     * @var string
     */
    protected $price = './*[@class="price-excluding-tax"]/span';

    /**
     * Selector for fpt
     *
     * @var string
     */
    protected $fpt = './/*[@class="cart-tax-info"]/*[@class="weee"]/span';

    /**
     * Selector for fpt total
     *
     * @var string
     */
    protected $fptTotal = './/*[@class="cart-tax-total"]/*[@class="weee"]/span';

    /**
     * Get product fpt
     *
     * @return string
     */
    public function getFpt()
    {
        $cartProductFpt = $this->_rootElement->find($this->fpt, Locator::SELECTOR_XPATH);
        if (!$cartProductFpt->isVisible()) {
            $this->_rootElement->find($this->price, Locator::SELECTOR_XPATH)->click();
        }
        return str_replace(',', '', $this->escapeCurrency($cartProductFpt->getText()));
    }

    /**
     * Get product fpt total
     *
     * @return string
     */
    public function getFptTotal()
    {
        $cartProductFptTotal = $this->_rootElement->find($this->fptTotal, Locator::SELECTOR_XPATH);
        $cartProductFptTotalText = $cartProductFptTotal->isVisible() ? $cartProductFptTotal->getText() : '';
        return str_replace(',', '', $this->escapeCurrency($cartProductFptTotalText));
    }
}
