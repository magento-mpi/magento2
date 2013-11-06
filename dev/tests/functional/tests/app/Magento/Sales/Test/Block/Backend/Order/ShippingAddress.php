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

use Mtf\Block\Form;
use Mtf\Client\Element\Locator;

/**
 * Form with shipping address on create order page in backend
 *
 * @package Magento\Sales\Test\Block\Backend\Order
 */
class ShippingAddress extends Form
{
    /**
     * 'Same as billing address' checkbox
     *
     * @var string
     */
    private $sameAsBilling;

    /**
     * Initialize for children classes
     */
    protected function _init()
    {
        $this->sameAsBilling = 'order-shipping_same_as_billing';
    }

    /**
     * Check the 'Same as billing address' checkbox in shipping address
     */
    public function setSameAsBillingShippingAddress()
    {
        $this->_rootElement->click();
        $this->_rootElement->find($this->sameAsBilling, Locator::SELECTOR_ID, 'checkbox')->setValue('Yes');
    }

    /**
     * Uncheck the 'Same as billing address' checkbox in shipping address
     */
    public function uncheckSameAsBillingShippingAddress()
    {
        $this->_rootElement->click();
        $this->_rootElement->find($this->sameAsBilling, Locator::SELECTOR_ID, 'checkbox')->setValue('No');
    }
}
