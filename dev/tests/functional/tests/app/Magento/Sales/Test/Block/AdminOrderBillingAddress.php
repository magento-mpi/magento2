<?php

namespace Magento\Sales\Test\Block;

use \Mtf\Block\Form;

class AdminOrderBillingAddress extends Form
{
    protected function _init()
    {
        $this->_mapping = array(
            'firstname' => '#order-billing_address_firstname',
            'lastname' => '#order-billing_address_lastname',
            'telephone' => '#order-billing_address_telephone',
            'street_1' => '#order-billing_address_street0',
            'city' => '#order-billing_address_city',
            'region' => '#order-billing_address_region_id',
            'postcode' => '#order-billing_address_postcode',
            'country' => '#order-billing_address_country_id'
        );
        parent::_init();
    }
}
