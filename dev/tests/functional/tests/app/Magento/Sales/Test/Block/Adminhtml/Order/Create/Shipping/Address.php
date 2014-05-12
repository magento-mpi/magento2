<?php
/**
 * {license_notice}
 *
 * @spi
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Block\Adminhtml\Order\Create\Shipping;

use Mtf\Block\Form;
use Mtf\Client\Element\Locator;

/**
 * Class ShippingAddress
 * Adminhtml sales order create shipping address block
 *
 */
class Address extends Form
{
    /**
     * 'Same as billing address' checkbox
     *
     * @var string
     */
    protected $sameAsBilling = '#order-shipping_same_as_billing';

    /**
     * Check the 'Same as billing address' checkbox in shipping address
     */
    public function setSameAsBillingShippingAddress()
    {
        $this->_rootElement->click();
        $this->_rootElement->find($this->sameAsBilling, Locator::SELECTOR_CSS, 'checkbox')->setValue('Yes');
    }

    /**
     * Uncheck the 'Same as billing address' checkbox in shipping address
     */
    public function uncheckSameAsBillingShippingAddress()
    {
        $this->_rootElement->click();
        $this->_rootElement->find($this->sameAsBilling, Locator::SELECTOR_CSS, 'checkbox')->setValue('No');
    }
}
