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

use \Mtf\Block\Form;

/**
 * Form for billing address during order creation on backend
 *
 * @package Magento\Sales\Test\Block\Backend\Order
 */
class BillingAddress extends Form
{
    /**
     * @inheritdoc
     */
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
