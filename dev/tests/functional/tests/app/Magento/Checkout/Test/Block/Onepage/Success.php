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
use Magento\Checkout\Test\Fixture\Checkout;

/**
 * Class Success
 * One page checkout success block
 *
 */
class Success extends Block
{
    /**
     * Determine order id if checkout was performed by registered customer
     *
     * @var string
     */
    protected $orderId = 'a[href*="view/order_id"]';

    /**
     * Determine order id if checkout was performed by guest
     *
     * @var string
     */
    protected $orderIdGuest = '//div[contains(@class, "column main")]//p[1]';

    /**
     * 'Continue Shopping' link
     *
     * @var string
     */
    protected $continueShopping = '.action.continue';

    /**
     * Success message selector
     *
     * @var string
     */
    protected $successMessage = '[data-ui-id="page-title"]';

    /**
     * Get id for placed order
     *
     * @param Checkout $fixture
     * @return string
     */
    public function getOrderId(Checkout $fixture)
    {
        $continueShopping = $this->_rootElement->find($this->continueShopping);
        $this->_rootElement->waitUntil(
            function () use ($continueShopping) {
                return $continueShopping->isVisible() ? true : null;
            }
        );
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

    /**
     * Get success message
     *
     * @return array|string
     */
    public function getSuccessMessage()
    {
        return $this->_rootElement->find($this->successMessage)->getText();
    }
}
