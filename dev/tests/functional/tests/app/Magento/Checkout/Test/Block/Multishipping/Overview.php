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

namespace Magento\Checkout\Test\Block\Multishipping;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;
use Magento\Checkout\Test\Fixture\Checkout;

/**
 * Class Overview
 * Multishipping checkout overview information
 *
 * @package Magento\Checkout\Test\Block\Multishipping
 */
class Overview extends Block
{
    /**
     * 'Place Order' button
     *
     * @var string
     */
    private $placeOrder;

    /**
     * Initialize block elements
     */
    protected function _init()
    {
        //Elements
        $this->placeOrder = '#review-button';
    }

    /**
     * Place order
     *
     * @param Checkout $fixture
     */
    public function placeOrder(Checkout $fixture)
    {
        //some review part
        $this->_rootElement->find($this->placeOrder, Locator::SELECTOR_CSS)->click();
        // TODO assert constraints
    }
}
