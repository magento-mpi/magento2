<?php

namespace Magento\Sales\Test\Block;

use \Mtf\Block\Form;
use Mtf\Client\Element\Locator;

class AdminOrderShippingAddress extends Form
{
    /**
     * Check the 'same as billing address' checkbox in shipping address
     */
    public function setSameAsBillingShippingAddress()
    {
        $this->_rootElement
            ->find('order-shipping_same_as_billing', Locator::SELECTOR_ID, 'checkbox')
            ->setValue('Yes');
    }
} 