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

namespace Magento\Paypal\Test\Block\Express\Review;

use Mtf\Block\Form;
use Mtf\Client\Element;

/**
 * Class Billing
 * Paypal Express Onepage checkout block for Billing Address
 *
 * @package Magento\Paypal\Test\Block\Express\Review
 */
class Billing extends Form
{
    /**
     * Initialize block elements
     */
    protected function _init()
    {
        $this->_mapping = array(
            'firstname' => '[id="billing:firstname"]',
            'lastname' => '[id="billing:lastname"]',
            'telephone' => '[id="billing:telephone"]',
            'street_1' => '[id="billing:street1"]',
            'city' => '[id="billing:city"]',
            'region' => '[id="billing:region_id"]',
            'postcode' => '[id="billing:postcode"]',
            'country' => '[id="billing:country_id"]',
        );
    }

    /**
     * Verify form data. Unset 'email' field as it absent in current form
     *
     * @param array $fields
     * @param Element $element
     * @return bool
     */
    protected function _verify(array $fields, Element $element = null)
    {
        unset($fields['email']);
        return parent::_verify($fields);
    }
}
