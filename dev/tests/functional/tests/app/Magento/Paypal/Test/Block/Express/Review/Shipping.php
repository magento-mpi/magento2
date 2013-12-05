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
use Mtf\Client\Element\Locator;

/**
 * Class Billing
 * Paypal Express Onepage checkout block for Shipping Address
 *
 * @package Magento\Paypal\Test\Block\Express\Review
 */
class Shipping extends Form
{
    /**
     * {@inheritdoc}
     */
    protected $_mapping = array(
        'firstname' => '[id="shipping:firstname"]',
        'lastname' => '[id="shipping:lastname"]',
        'company' => '[id="shipping:company"]',
        'telephone' => '[id="shipping:telephone"]',
        'street_1' => '[id="shipping:street1"]',
        'city' => '[id="shipping:city"]',
        'region' => '[id="shipping:region_id"]',
        'postcode' => '[id="shipping:postcode"]',
        'country' => '[id="shipping:country_id"]',
    );

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
        return parent::_verify($fields, $element);
    }

    /**
     * Set telephone number for orders which was placed via PayPal Express
     *
     * @param array $field
     */
    public function setTelephoneNumber($field)
    {
        $this->_rootElement->find($this->_mapping['telephone'], Locator::SELECTOR_CSS)->setValue($field['telephone']);
    }
}
