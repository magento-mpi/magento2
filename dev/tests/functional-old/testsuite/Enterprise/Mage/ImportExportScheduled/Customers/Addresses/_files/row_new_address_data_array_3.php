<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
/** @var $this Mage_Selenium_TestCase */
return array(
    $this->loadDataSet('Customers', 'generic_address', array(
            'city' => 'Chattanooga',
            'company' => 'Hit or Miss',
            'fax' => '423-313-8300',
            'first_name' => 'Maureen',
            'last_name' => 'Velez',
            'middle_name' => 'G.',
            'zip_code' => '37408',
            'state' => 'Tennessee',
            'street_address_line_1' => '3059 Public Works Drive',
            'street_address_line_2' => '',
            'telephone' => '423-313-8300',
        )
    ),
    $this->loadDataSet('Customers', 'generic_address', array(
            'city' => 'Stockbridge',
            'company' => 'White Tower Hamburgers',
            'fax' => '678-565-2507',
            'first_name' => 'Lisa',
            'last_name' => 'Lewis',
            'middle_name' => 'M.',
            'zip_code' => '30281',
            'state' => 'Georgia',
            'street_address_line_1' => '3292 Hanifan Lane',
            'street_address_line_2' => '',
            'telephone' => '678-565-2507',
        )
    ),
    $this->loadDataSet('Customers', 'generic_address', array(
            'city' => 'San Diego',
            'company' => 'Security Sporting Goods',
            'fax' => '619-696-3735',
            'first_name' => 'Luis',
            'last_name' => 'Meade',
            'middle_name' => 'J.',
            'zip_code' => '92101',
            'state' => 'California',
            'street_address_line_1' => '1776 Grim Avenue',
            'street_address_line_2' => '',
            'telephone' => '619-696-3735',
        )
    )
);
