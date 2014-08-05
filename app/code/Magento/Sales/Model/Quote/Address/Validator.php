<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Model\Quote\Address;

use Zend_Validate_Exception;

class Validator extends \Magento\Framework\Validator\AbstractValidator
{
    /**
     * @var \Magento\Directory\Model\CountryFactory
     */
    protected $countryFactory;

    /**
     * @var \Magento\Shipping\Model\CarrierFactory
     */
    protected $carrierFactory;

    /**
     * @param \Magento\Directory\Model\CountryFactory $countryFactory
     * @param \Magento\Shipping\Model\CarrierFactory $carrierFactory
     */
    public function __construct(
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Shipping\Model\CarrierFactory $carrierFactory
    ) {
        $this->countryFactory = $countryFactory;
        $this->carrierFactory = $carrierFactory;
    }

    /**
     * Returns true if and only if $value meets the validation requirements
     *
     * If $value fails validation, then this method returns false, and
     * getMessages() will return an array of messages that explain why the
     * validation failed.
     *
     * @param  \Magento\Sales\Model\Quote\Address $value
     * @return boolean
     * @throws Zend_Validate_Exception If validation of $value is impossible
     */
    public function isValid($value)
    {
        $messages = array();
        $email = $value->getEmail();
        if (!empty($email) && !\Zend_Validate::is($email, 'EmailAddress')) {
            $messages['invalid_email_format'] = 'Invalid email format';
        }

        $countryId = $value->getCountryId();
        if (!empty($countryId)) {
            $country = $this->countryFactory->create();
            $country->load($countryId);
            if (!$country->getId()) {
                $messages['invalid_country_code'] = 'Invalid country code';
            }
        }

        if ($value->getShippingMethod()) {
            $shippingMethodErrors = $this->validateShippingMethod($value);
            $messages = array_merge($messages, $shippingMethodErrors);
        }
        $this->_addMessages($messages);

        return empty($messages);
    }

    /**
     * @param \Magento\Sales\Model\Quote\Address $address
     * @return array error messages if any
     */
    protected function validateShippingMethod($address)
    {
        $messages = [];
        list($carrierCode, $method) = explode('_', $address->getShippingMethod());
        /** @var \Magento\Shipping\Model\Carrier\CarrierInterface $carrier */
        $carrier = $this->carrierFactory->get($carrierCode);
        if (!$carrier) {
            $messages['invalid_carrier'] = "Wrong carrier code: " . $carrierCode;
            return $messages;
        }
        $allowedMethods = array_keys($carrier->getAllowedMethods());
        if (empty($method) || !in_array($method, $allowedMethods)) {
            $messages['invalid_shipping_method'] = "Wrong method code: " . $method;
        }

        return $messages;
    }
}
