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

namespace Magento\Multishipping\Test\Block\Checkout;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;
use Magento\Multishipping\Test\Fixture\GuestPaypalDirect;

/**
 * Class Overview
 * Multishipping checkout overview information
 *
 * @package Magento\Multishipping\Test\Block\Checkout
 */
class Overview extends Block
{
    /**
     * 'Place Order' button
     *
     * @var string
     */
    protected $placeOrder = '#review-button';

    /**
     * Place order
     *
     * @param GuestPaypalDirect $fixture
     */
    public function placeOrder(GuestPaypalDirect $fixture = null)
    {
        $this->_rootElement->find($this->placeOrder, Locator::SELECTOR_CSS)->click();
    }
}
