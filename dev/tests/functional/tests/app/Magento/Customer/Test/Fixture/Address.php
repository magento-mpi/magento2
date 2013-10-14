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

namespace Magento\Customer\Test\Fixture;

use Mtf\Fixture\DataFixture;

/**
 * Class Address
 * Customer addresses
 *
 * @package Magento\Customer\Address\Fixture
 */
class Address extends DataFixture
{
    /**
     * Format customer address to one line
     *
     * @return string
     */
    public function getOneLineAddress()
    {
        $data = $this->getData();
        $address = isset($data['fields']['prefix']['value']) ? $data['fields']['prefix']['value'] . ' ' : ''
            . $data['fields']['firstname']['value'] . ' '
            . (isset($data['fields']['middlename']['value']) ? $data['fields']['middlename']['value'] . ' ' : '')
            . $data['fields']['lastname']['value'] . ', '
            . (isset($data['fields']['suffix']['value']) ? $data['fields']['suffix']['value'] . ' ' : '')
            . $data['fields']['street_1']['value'] . ', '
            . $data['fields']['city']['value'] . ', '
            . $data['fields']['region']['value'] . ' '
            . $data['fields']['postcode']['value'] . ', '
            . $data['fields']['country']['value'];

        return $address;
    }

    /**
     * Get telephone number
     *
     * @return string
     */
    public function getTelephone()
    {
        return $this->getData('fields/telephone');
    }
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * {inheritdoc}
     */
    protected function _initData()
    {
        $this->_repository = array(
            'address_US_1' => array(
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
            ),

            'address_data_US_1' => array(
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
            ),

            'address_US_2' => array(
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
            )
        );

        //Default data set
        $this->switchData('address_US_1');
    }
}
