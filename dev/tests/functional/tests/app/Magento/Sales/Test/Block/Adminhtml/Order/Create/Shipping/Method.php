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

namespace Magento\Sales\Test\Block\Adminhtml\Order\Create\Shipping;

use Magento\Backend\Test\Block\Template;
use Magento\Sales\Test\Fixture\Order;
use Mtf\Block\Block;
use Mtf\Client\Element\Locator;
use Mtf\Factory\Factory;

/**
 * Class Method
 * Adminhtml sales order create shipping method block
 *
 * @package Magento\Sales\Test\Block\Adminhtml\Order\Create\Shipping
 */
class Method extends Block
{
    /**
     * Select shipping method
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
    }
}
