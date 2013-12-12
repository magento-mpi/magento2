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

namespace Magento\Customer\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class Address Repository
 * Customer addresses
 *
 * @package Magento\Customer\Address\Repository
 */
class Address extends AbstractRepository
{
    /**
     * {inheritdoc}
     */
    public function __construct(array $defaultConfig, array $defaultData)
    {
        $this->_data['default'] = array(
            'config' => $defaultConfig,
            'data' => $defaultData
        );

        $this->_data['address_US_1'] = $this->_getUS1();
        $this->_data['address_US_2'] = $this->_getUS2();
        $this->_data['address_data_US_1'] = $this->_getDataUS1();
        $this->_data['address_data_DE'] = $this->_getDataGermany();
        $this->_data['address_data_UK'] = $this->_getDataUnitedKingdom();
    }

    protected function _getUS1()
    {
        return array(
            'data' => array(
                'fields' => array(
                    'firstname' => array(
                        'value' => 'John'
                    ),
                    'lastname' => array(
                        'value' => 'Doe'
                    ),
                    'email' => array(
                        'value' => 'John.Doe%isolation%@example.com'
                    ),
                    'company' => array(
                        'value' => 'Magento %isolation%'
                    ),
                    'street_1' => array(
                        'value' => '6161 West Centinela Avenue'
                    ),
                    'city' => array(
                        'value' => 'Culver City'
                    ),
                    'region' => array(
                        'value' => 'California',
                        'input' => 'select'
                    ),
                    'postcode' => array(
                        'value' => '90230'
                    ),
                    'country' => array(
                        'value' => 'United States',
                        'input' => 'select'
                    ),
                    'telephone' => array(
                        'value' => '555-55-555-55'
                    )
                )
            )
        );
    }

    protected function _getBackendUS1()
    {
        return array(
            'data' => array(
                'fields' => array(
                    'save_in_address_book' => array(
                        'value' => 'Yes',
                        'input' => 'checkbox'
                    )
                )
            )
        );
    }

    protected function _getUS2()
    {
        return array(
            'data' => array(
                'fields' => array(
                    'firstname' => array(
                        'value' => 'Billy'
                    ),
                    'lastname' => array(
                        'value' => 'Holiday'
                    ),
                    'email' => array(
                        'value' => 'b.holliday@example.net'
                    ),
                    'company' => array(
                        'value' => 'Magento %isolation%'
                    ),
                    'street_1' => array(
                        'value' => '727 5th Ave'
                    ),
                    'city' => array(
                        'value' => 'New York'
                    ),
                    'region' => array(
                        'value' => 'New York',
                        'input' => 'select'
                    ),
                    'postcode' => array(
                        'value' => '10022'
                    ),
                    'country' => array(
                        'value' => 'United States',
                        'input' => 'select'
                    ),
                    'telephone' => array(
                        'value' => '777-77-77-77'
                    )
                )
            )
        );
    }

    protected function _getDataUS1()
    {
        return array(
            'data' => array(
                'fields' => array(
                    'firstname' => array(
                        'value' => 'John'
                    ),
                    'lastname' => array(
                        'value' => 'Doe'
                    ),
                    'company' => array(
                        'value' => 'Magento %isolation%'
                    ),
                    'street_1' => array(
                        'value' => '6161 West Centinela Avenue'
                    ),
                    'city' => array(
                        'value' => 'Culver City'
                    ),
                    'region' => array(
                        'value' => 'California',
                        'input' => 'select'
                    ),
                    'postcode' => array(
                        'value' => '90230'
                    ),
                    'country' => array(
                        'value' => 'United States',
                        'input' => 'select'
                    ),
                    'telephone' => array(
                        'value' => '555-55-555-55'
                    )
                )
            )
        );
    }

    protected function _getDataGermany()
    {
        return array(
            'data' => array(
                'fields' => array(
                    'firstname' => array(
                        'value' => 'Jan'
                    ),
                    'lastname' => array(
                        'value' => 'Jansen'
                    ),
                    'company' => array(
                        'value' => 'Magento %isolation%'
                    ),
                    'country' => array(
                        'value' => 'Germany',
                        'input' => 'select'
                    ),
                    'street_1' => array(
                        'value' => 'Augsburger Strabe 41'
                    ),
                    'city' => array(
                        'value' => 'Berlin'
                    ),
                    'region' => array(
                        'value' => 'Berlin',
                        'input' => 'select'
                    ),
                    'postcode' => array(
                        'value' => '10789'
                    ),
                    'telephone' => array(
                        'value' => '333-33-333-33'
                    )
                )
            )
        );
    }

    protected function _getDataUnitedKingdom()
    {
        return array(
            'data' => array(
                'fields' => array(
                    'firstname' => array(
                        'value' => 'Jane'
                    ),
                    'lastname' => array(
                        'value' => 'Doe'
                    ),
                    'company' => array(
                        'value' => 'Magento %isolation%'
                    ),
                    'country' => array(
                        'value' => 'United Kingdom',
                        'input' => 'select'
                    ),
                    'street_1' => array(
                        'value' => '42 King Street West'
                    ),
                    'city' => array(
                        'value' => 'Manchester'
                    ),
                    'province' => array(
                        'value' => 'Manchester'
                    ),
                    'postcode' => array(
                        'value' => 'M3 2WY'
                    ),
                    'telephone' => array(
                        'value' => '444-44-444-44'
                    )
                )
            )
        );
    }
}
