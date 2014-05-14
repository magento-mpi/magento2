<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Test\Block\Onepage;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Class Review
 * One page checkout status review block
 *
 */
class Review extends Block
{
    /**
     * Continue checkout button
     *
     * @var string
     */
    protected $continue = '#review-buttons-container button';

    /**
     * Centinel authentication block
     *
     * @var string
     */
    protected $centinelBlock = '#centinel-authenticate-block';

    /**
     * Grand total search mask
     *
     * @var string
     */
    protected  $grandTotal = '//tr[normalize-space(td)="Grand Total"]//span';
    
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
     * Fill billing address
     */
    public function placeOrder()
    {
        $this->_rootElement->find($this->continue, Locator::SELECTOR_CSS)->click();
        $this->waitForElementNotVisible('#review-please-wait');
    }

    /**
     * Wait for 3D Secure card validation
     */
    public function waitForCardValidation()
    {
        $this->waitForElementNotVisible($this->centinelBlock);
    }

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
     * @return array|string
     */
    public function getSubtotal()
    {
        return $this->_rootElement->find($this->subtotal, Locator::SELECTOR_XPATH)->getText();
    }
}
