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
 * @package Magento\Customer\Test\Repository
 */
class Address extends AbstractRepository
{
    /**
     * {inheritdoc}
     */
    public function __construct(array $defaultConfig = array(), array $defaultData = array())
    {
        $this->_data['default'] = array(
            'config' => $defaultConfig,
            'data' => $defaultData
        );

        $this->_data['address_US_1'] = $this->_getUS1();
        $this->_data['address_US_2'] = $this->_getUS2();
        $this->_data['address_UK'] = $this->getAddressUK();
        $this->_data['address_UK_2'] = $this->getAddressUK2();
        $this->_data['address_UK_with_VAT'] = $this->getAddressUKWithVAT($this->_data['address_UK']);
        $this->_data['address_DE'] = $this->getAddressDE();
        $this->_data['address_data_US_1'] = $this->_getDataUS1();
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
                    'region_id' => array(
                        'value' => 'California',
                        'input' => 'select'
                    ),
                    'postcode' => array(
                        'value' => '90230'
                    ),
                    'country_id' => array(
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
                    'region_id' => array(
                        'value' => 'New York',
                        'input' => 'select'
                    ),
                    'postcode' => array(
                        'value' => '10022'
                    ),
                    'country_id' => array(
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
                    'country_id' => array(
                        'value' => 'United States',
                        'input' => 'select'
                    ),
                    'region_id' => array(
                        'value' => 'California',
                        'input' => 'select',
                        'selector' => '#region_id'
                    ),
                    'city' => array(
                        'value' => 'Culver City'
                    ),
                    'postcode' => array(
                        'value' => '90230'
                    ),
                    'telephone' => array(
                        'value' => '555-55-555-55'
                    )
                )
            )
        );
    }

    /**
     * Get address for UK
     *
     * @return array
     */
    protected function getAddressUK()
    {
        return array(
            'data' => array(
                'fields' => array(
                    'firstname' => array(
                        'value' => 'Jane',
                    ),
                    'lastname' => array(
                        'value' => 'Doe',
                    ),
                    'telephone' => array(
                        'value' => '444-44-444-44',
                    ),
                    'street[]' => array(
                        'value' => '172, Westminster Bridge Rd',
                    ),
                    'country_id' => array(
                        'value' => 'United Kingdom',
                        'input_value' => 'GB',
                        'input' => 'select',
                        'selector' => '#country',
                    ),
                    'region' => array(
                        'value' => 'London',
                    ),
                    'city' => array(
                        'value' => 'London',
                    ),
                    'postcode' => array(
                        'value' => 'SE1 7RW',
                        'selector' => '#zip',
                    ),
                ),
            ),
        );
    }

    /**
     * Get second address for UK
     *
     * @return array
     */
    protected function getAddressUK2()
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
                    'telephone' => array(
                        'value' => '444-44-444-44'
                    ),
                    'street_1' => array(
                        'value' => '42 King Street West'
                    ),
                    'country_id' => array(
                        'value' => 'United Kingdom',
                        'input' => 'select',
                    ),
                    'region' => array(
                        'value' => 'Manchester',
                        'selector' => '#region',
                    ),
                    'city' => array(
                        'value' => 'Manchester'
                    ),
                    'postcode' => array(
                        'value' => 'M3 2WY',
                        'selector' => '#zip',
                    )
                )
            )
        );
    }

    /**
     * Get address data for UK with VAT
     *
     * @param array $defaultData
     * @return array
     */
    protected function getAddressUKWithVAT($defaultData)
    {
        return array_replace_recursive($defaultData, array(
            'data' => array(
                'fields' => array(
                    'vat_id' => array(
                        'value' => '584451913',
                    ),
                ),
            ),
        ));
    }

    /**
     * Get address for Germany
     *
     * @return array
     */
    protected function getAddressDE()
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
                    'country_id' => array(
                        'value' => 'Germany',
                        'input' => 'select'
                    ),
                    'street_1' => array(
                        'value' => 'Augsburger Strabe 41'
                    ),
                    'city' => array(
                        'value' => 'Berlin'
                    ),
                    'region_id' => array(
                        'value' => 'Berlin',
                        'input' => 'select',
                        'selector' => '#region_id',
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
}
