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
 * Cart totals block
 *
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
     * Check that Tax is visible
     *
     * @return bool
     */
    public function isTaxVisible()
    {
        return $this->_rootElement->find($this->tax, Locator::SELECTOR_XPATH)->isVisible();
    }

    /**
     * Get Subtotal text
     *
     * @return array|string
     */
    public function getSubtotal()
    {
        return $this->_rootElement->find($this->subtotal, Locator::SELECTOR_XPATH)->getText();
    }
}
