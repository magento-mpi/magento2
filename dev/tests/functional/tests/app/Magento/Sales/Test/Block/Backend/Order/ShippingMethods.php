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

namespace Magento\Sales\Test\Block\Backend\Order;

use Magento\Sales\Test\Fixture\Order;
use Mtf\Block\Block;
use Mtf\Client\Element\Locator;
use Magento\Payment\Test\Block\Form;

/**
 * Class Methods
 * Order creation in backend payment methods
 *
 * @package Magento\Sales\Test\Block\Backend\Order
 */
class ShippingMethods extends Block
{
    /**
     * Select payment method
     *
     * @param Order $fixture
     */
    public function selectShippingMethod(Order $fixture)
    {
        $this->_rootElement->find('#order-shipping-method-summary a')->click();
        $shippingMethod = $fixture->getShippingMethod()->getData('fields');
        $selector = '//dt[contains(., "' . $shippingMethod['shipping_service']
            . '")]/following-sibling::*//input';
        $this->_rootElement->find($selector, Locator::SELECTOR_XPATH)->click();
        $this->waitForElementNotVisible('.please-wait');
    }
}
