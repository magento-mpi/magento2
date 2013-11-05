<?php
/**
 * {license_notice}
 *
 * @spi
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Block\Backend\Order;

use \Mtf\Block\Form;
use Mtf\Client\Element\Locator;

/**
 * Form with shipping address on create order page in backend
 *
 * @package Magento\Sales\Test\Block\Backend\Order
 */
class ShippingAddress extends Form
{
    /**
     * Check the 'same as billing address' checkbox in shipping address
     */
    public function setSameAsBillingShippingAddress()
    {
        $this->_rootElement->click();
        $this->_rootElement->find(
            'order-shipping_same_as_billing',
            Locator::SELECTOR_ID,
            'checkbox'
        )->setValue('Yes');
    }
}
