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

namespace Magento\Checkout\Test\Block\Onepage;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Class Review
 * One page checkout status
 *
 * @package Magento\Checkout\Test\Block\Onepage
 */
class Review extends Block
{
    /**
     * Continue checkout button
     *
     * @var string
     */
    private $continue;

    /**
     * Grand total search mask
     *
     * @var string
     */
    protected  $_grandTotalMask = '//tr[normalize-space(td)="Grand Total"]//span';

    /**
     * Subtotal search mask
     *
     * @var string
     */
    protected $_subtotalMask = '//tr[normalize-space(td)="Subtotal"]//span';

    /**
     * Tax search mask
     *
     * @var string
     */
    protected $_taxMask = '//tr[normalize-space(td)="Tax"]//span';

    /**
     * Initialize block elements
     */
    protected function _init()
    {
        $this->continue = '#review-buttons-container button';
    }

    /**
     * Fill billing address
     */
    public function placeOrder()
    {
        $this->_rootElement->find($this->continue, Locator::SELECTOR_CSS)->click();
        $this->waitForElementNotVisible('.please-wait');
    }

    /**
     * Get Grand Total Text
     *
     * @return array|string
     */
    public function getGrandTotal()
    {
        return $this->_rootElement->find($this->_grandTotalMask, Locator::SELECTOR_XPATH)->getText();
    }

    /**
     * Get Tax text from Order Totals
     *
     * @return array|string
     */
    public function getTax()
    {
        return $this->_rootElement->find($this->_taxMask, Locator::SELECTOR_XPATH)->getText();
    }

    /**
     * Get Subtotal text
     *
     * @return array|string
     */
    public function getSubtotal()
    {
        return $this->_rootElement->find($this->_subtotalMask, Locator::SELECTOR_XPATH)->getText();
    }
}
