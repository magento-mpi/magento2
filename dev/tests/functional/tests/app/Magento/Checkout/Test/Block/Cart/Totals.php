<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Test\Block\Cart;

use Mtf\Block\Block;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;

/**
 * Class Totals
 *
 * Cart totals block
 */
class Totals extends Block
{
    /**
     * Grand total search mask
     *
     * @var string
     */
    protected $grandTotal = '//tr[normalize-space(td)="Grand Total"]//span';

    /**
     * Subtotal search mask
     *
     * @var string
     */
    protected $subtotal = '//tr[normalize-space(td)="Subtotal"]//span';

    /**
     * Tax search mask
     *
     * @var string
     */
    protected $tax = '//tr[normalize-space(td)="Tax"]//span';

    /**
     * Get shipping price selector
     *
     * @var string
     */
    protected $shippingPriceSelector = '.shipping.excl .price';

    /**
     * Get shipping price block selector
     *
     * @var string
     */
    protected $shippingPriceBlockSelector = '.totals.shipping.excl';

    /**
     * Get Grand Total Text
     *
     * @return array|string
     */
    public function getGrandTotal()
    {
        return $this->_rootElement->find($this->grandTotal, Locator::SELECTOR_XPATH)->getText();
    }

    /**
     * Get Tax text from Order Totals
     *
     * @return array|string
     */
    public function getTax()
    {
        return $this->_rootElement->find($this->tax, Locator::SELECTOR_XPATH)->getText();
    }

    /**
     * Get Subtotal text
     *
     * @return string
     */
    public function getSubtotal()
    {
        return $this->_rootElement->find($this->subtotal, Locator::SELECTOR_XPATH)->getText();
    }

    /**
     * Get shipping price
     *
     * @return string
     */
    public function getChippingPrice(){
        return  $this->_rootElement->find($this->shippingPriceSelector, Locator::SELECTOR_CSS)->getText();
    }

    /**
     * Is visible shipping price block
     *
     * @return bool
     */
    public function isVisibleShippingPriceBlock(){
        return  $this->_rootElement->find($this->shippingPriceBlockSelector, Locator::SELECTOR_CSS)->isVisible();
    }
}
