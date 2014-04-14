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
 * Class Success
 * Multishipping checkout success block
 *
 * @package Magento\Multishipping\Test\Block\Checkout
 */
class Success extends Block
{
    /**
     * Continue checkout button
     *
     * @var string
     */
    protected $continue = '.button-set button';

    /**
     * Fill shipping address
     */
    public function continueShopping()
    {
        $this->_rootElement->find($this->continue, Locator::SELECTOR_CSS)->click();
        $this->waitForElementNotVisible('.please-wait');
    }

    /**
     * Get ids for placed order
     *
     * @param GuestPaypalDirect $fixture
     * @return array
     */
    public function getOrderIds(GuestPaypalDirect $fixture)
    {
        $orderIds = array();
        $ordersNumber = count($fixture->getShippingMethods());
        for ($i = 1; $i <= $ordersNumber; $i++) {
            $orderIds[] = $this->_rootElement->find('//a[' . $i . '][contains(@href, "view/order_id")]',
                Locator::SELECTOR_XPATH)->getText();
        }

        return $orderIds;
    }
}
