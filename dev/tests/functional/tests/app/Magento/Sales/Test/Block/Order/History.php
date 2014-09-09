<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Block\Order;

use Mtf\Block\Block;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;

/**
 * Customer Orders block on frontend
 */
class History extends Block
{
    /**
     * Locator for order id and order status
     *
     * @var string
     */
    protected $customerOrders = '//tr[td[contains(.,"%d")] and td[contains(.,"%s")]]';

    /**
     * Check if order is visible in customer orders on frontend
     *
     * @param array $order
     * @return bool
     */
    public function isOrderVisible($order)
    {
        return $this->_rootElement->find(
            sprintf($this->customerOrders, $order['id'], $order['status']),
            Locator::SELECTOR_XPATH
        )->isVisible();
    }
}
