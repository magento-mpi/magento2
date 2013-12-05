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

namespace Magento\Sales\Test\Block\Adminhtml\Order\Create\Billing;

use \Mtf\Block\Form;

/**
 * Class BillingAddress
 * Adminhtml sales order create billing address block
 *
 * @package Magento\Sales\Test\Block\Adminhtml\Order\Create\Billing
 */
class Address extends Form
{
    /**
     * {@inheritdoc}
     */
    protected $_mapping = array(
        'firstname' => '#order-billing_address_firstname',
        'lastname' => '#order-billing_address_lastname',
        'company' => '#order-billing_address_company',
        'telephone' => '#order-billing_address_telephone',
        'street_1' => '#order-billing_address_street0',
        'city' => '#order-billing_address_city',
        'region' => '#order-billing_address_region_id',
        'postcode' => '#order-billing_address_postcode',
        'country' => '#order-billing_address_country_id',
        'save_in_address_book' => '#order-billing_address_save_in_address_book'
    );
}
