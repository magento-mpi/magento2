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
use Magento\Checkout\Test\Fixture\Checkout;

/**
 * Class Success
 * One page checkout success block
 *
 * @package Magento\Checkout\Test\Block\Onepage
 */
class Success extends Block
{
    /**
     * Determine order id if checkout was performed by registered customer
     *
     * @var string
     */
    private $orderId;

    /**
     * Determine order id if checkout was performed by guest
     *
     * @var string
     */
    private $orderIdGuest;

    /**
     * Initialize block elements
     */
    protected function _init()
    {
        parent::_init();
        //Elements
        $this->orderId = 'a[href*="view/order_id"]';
        $this->orderIdGuest = '//div[contains(@class, "column main")]//p[1]';
    }

    /**
     * Get id for placed order
     *
     * @param Checkout $fixture
     * @return string
     */
    public function getOrderId(Checkout $fixture)
    {
        if ($fixture->getCustomer()) {
            return $this->_rootElement->find($this->orderId, Locator::SELECTOR_CSS)->getText();
        } else {
            return $this->getGuestOrderId();
        }
    }

    /**
     * Get Id of placed order for guest checkout
     *
     * @return string
     */
    public function getGuestOrderId()
    {
        $orderString = $this->_rootElement->find($this->orderIdGuest, Locator::SELECTOR_XPATH)->getText();
        preg_match('/[\d]+/', $orderString, $orderId);
        return end($orderId);
    }
}
