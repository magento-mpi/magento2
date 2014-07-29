<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class AddressInjectable
 * Customer address repository
 */
class AddressInjectable extends AbstractRepository
{
    /**
     * @param array $defaultConfig [optional]
     * @param array $defaultData [optional]
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['default'] = [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'email' => 'John.Doe%isolation%@example.com',
            'company' => 'Magento %isolation%',
            'street' => '6161 West Centinela Avenue',
            'city' => 'Culver City',
            'region_id' => 'California',
            'postcode' => '90230',
            'country_id' => 'United States',
            'telephone' => '555-55-555-55',
            'default_billing' => 'Yes',
            'default_shipping' => 'Yes'
        ];

        $this->_data['billing'] = [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'email' => 'John.Doe%isolation%@example.com',
            'company' => 'Magento %isolation%',
            'street' => '6161 West Centinela Avenue',
            'city' => 'Culver City',
            'region_id' => 'California',
            'postcode' => '90230',
            'country_id' => 'United States',
            'telephone' => '555-55-555-55',
            'default_billing' => 'Yes',
            'default_shipping' => 'No'
        ];

        $this->_data['shipping'] = [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'email' => 'John.Doe%isolation%@example.com',
            'company' => 'Magento %isolation%',
            'street' => '6161 West Centinela Avenue',
            'city' => 'Culver City',
            'region_id' => 'California',
            'postcode' => '90230',
            'country_id' => 'United States',
            'telephone' => '555-55-555-55',
            'default_billing' => 'Yes',
            'default_shipping' => 'No'
        ];

        $this->_data['default_US_address'] = [
            'company' => 'Magento %isolation%',
            'street' => '6161 West Centinela Avenue',
            'city' => 'Culver City',
            'region_id' => 'California',
            'postcode' => '90230',
            'country_id' => 'United States',
            'telephone' => '555-55-555-55',
            'default_billing' => 'Yes',
            'default_shipping' => 'Yes',
        ];
    }
}
