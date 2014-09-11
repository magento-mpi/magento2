<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Block\Order;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Class History
 * Order history block on My Order page
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
     * Item order
     *
     * @var string
     */
    protected $itemOrder = '//tr[td[contains(@class, "id") and normalize-space(.)="%d"]]';

    /**
     * Order total css selector
     *
     * @var string
     */
    protected $total = '.total span.price';

    /**
     * View button css selector
     *
     * @var string
     */
    protected $viewButton = '.action.view';

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

    /**
     * Get order total
     *
     * @param int $id
     * @param string $currency [optional]
     * @return string
     */
    public function getOrderTotalById($id, $currency = '$')
    {
        return trim($this->searchOrderById($id)->find($this->total)->getText(), ' '. $currency);
    }

    /**
     * Get item order block
     *
     * @param int $id
     * @return \Mtf\Client\Element
     */
    protected function searchOrderById($id)
    {
        return $this->_rootElement->find(sprintf($this->itemOrder, $id), Locator::SELECTOR_XPATH);
    }

    /**
     * Open item order
     *
     * @param int $id
     * @return void
     */
    public function openOrderById($id)
    {
        $this->searchOrderById($id)->find($this->viewButton)->click();
    }
}
