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

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;
use Magento\Sales\Test\Fixture\Order;

/**
 * Class Method
 * Adminhtml sales order create shipping method block
 *
 * @package Magento\Sales\Test\Block\Adminhtml\Order\Create\Shipping
 */
class Method extends Block
{
    /**
     * 'Get shipping methods and rates' link
     *
     * @var string
     */
    protected $shippingMethodsLink = '#order-shipping-method-summary a';

    /**
     * Shipping method
     *
     * @var string
     */
    protected $shippingMethod = '//dt[contains(.,"%s")]/following-sibling::*//*[contains(text(), "%s")]';

    /**
     * Select shipping method
     *
     * @param Order $fixture
     */
    public function selectShippingMethod(Order $fixture)
    {
        $this->_rootElement->find($this->shippingMethodsLink)->click();
        $shippingMethod = $fixture->getShippingMethod()->getData('fields');
        $selector = sprintf(
            $this->shippingMethod, $shippingMethod['shipping_service'], $shippingMethod['shipping_method']
        );
        $this->_rootElement->find($selector, Locator::SELECTOR_XPATH)->click();
    }
}
