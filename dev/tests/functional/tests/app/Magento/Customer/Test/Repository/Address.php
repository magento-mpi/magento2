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
        $this->_data['backend_address_US_1'] = $this->_getBackendUS1();
        $this->_data['address_US_2'] = $this->_getUS2();
        $this->_data['address_data_US_1'] = $this->_getDataUS1();
        $this->_data['backend_address_data_US_1'] = $this->_getBackendDataUS1();
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
                    'firstname' => array(
                        'value' => 'John'
                    ),
                    'lastname' => array(
                        'value' => 'Doe'
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
                    ),
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

    protected function _getBackendDataUS1()
    {
        return array(
            'data' => array(
                'fields' => array(
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
}
