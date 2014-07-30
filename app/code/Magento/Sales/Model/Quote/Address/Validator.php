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
     * @param \Magento\Directory\Model\CountryFactory $countryFactory
     */
    public function __construct(\Magento\Directory\Model\CountryFactory $countryFactory)
    {
        $this->countryFactory = $countryFactory;
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
        if (!$this->isEmpty($email) && !\Zend_Validate::is($email, 'EmailAddress')) {
            $messages['invalid_email_format'] = 'Invalid email format';
        }

        $countryId = $value->getCountryId();
        if (!$this->isEmpty($countryId)) {
            $country = $this->countryFactory->create()->load($countryId);
            if (!$country->getId()) {
                $messages['invalid_country_code'] = 'Invalid country code';
            }
        }

        $this->_addMessages($messages);

        return empty($messages);
    }

    /**
     * Check whether value is empty
     *
     * @param mixed $value
     * @return bool
     */
    protected function isEmpty($value)
    {
        return empty($value);
    }
} 
