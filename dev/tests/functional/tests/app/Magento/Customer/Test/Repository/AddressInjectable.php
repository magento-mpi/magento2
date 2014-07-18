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
 *
 * @package Magento\Customer\Test\Repository
 */
class AddressInjectable extends AbstractRepository
{
    /**
     * @param array $defaultConfig
     * @param array $defaultData
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['customer_US'] = [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'email' => 'JohnDoe_%isolation%@example.com',
            'company' => 'Magento %isolation%',
            'city' => 'Culver City',
            'street' => '6161 West Centinela Avenue',
            'postcode' => '90230',
            'country_id' => 'United States',
            'region_id' => 'California',
            'telephone' => '555-55-555-55',
            'fax' => '555-55-555-55'
        ];

        $this->_data['customer_UK'] = [
            'firstname' => 'Jane',
            'lastname' => 'Doe',
            'email' => 'JaneDoe_%isolation%@example.com',
            'company' => 'Magento %isolation%',
            'city' => 'London',
            'street' => '172, Westminster Bridge Rd',
            'postcode' => 'SE1 7RW',
            'country_id' => 'United Kingdom',
            'region' => 'London',
            'telephone' => '444-44-444-44',
            'fax' => '444-44-444-44'
        ];
    }
}
