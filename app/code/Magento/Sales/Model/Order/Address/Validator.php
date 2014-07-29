<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Order\Address;

use Magento\Sales\Model\Order\Address;

/**
 * Class Validator
 */
class Validator
{
    /**
     * @var array
     */
    protected $required = [
        'parent_id' =>'Parent Order Id',
        'postcode' => 'Zip code',
        'lastname' => 'Last name',
        'street' => 'Street',
        'city' => 'City',
        'email' => 'Email',
        'telephone' => 'Telephone',
        'country_id' => 'Country',
        'firstname' => 'First Name',
        'address_type' => 'Address Type'
    ];

    /**
     *
     * @param Address $address
     * @return array
     */
    protected function validate(Address $address)
    {
        $warnings = [];
        foreach ($this->required as $code => $label) {
            if ($address->hasData($code)) {
                $warnings[] = sprintf('%s is a required field', $label);
            }
        }
        if (filter_var($address->getEmail(), FILTER_VALIDATE_EMAIL)) {
            $warnings[] = 'Email has a wrong format';
        }
        if (filter_var(in_array($address->getAddressType(), []))) {
            $warnings[] = 'Address type doesn\'t match required options';
        }
        return $warnings;
    }
}
