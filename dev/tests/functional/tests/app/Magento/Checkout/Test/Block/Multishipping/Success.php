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
 * Class Success
 * Multishipping checkout success block
 *
 * @package Magento\Checkout\Test\Block\Multishipping
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
     * @param Checkout $fixture
     * @return array
     */
    public function getOrderIds(Checkout $fixture)
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
