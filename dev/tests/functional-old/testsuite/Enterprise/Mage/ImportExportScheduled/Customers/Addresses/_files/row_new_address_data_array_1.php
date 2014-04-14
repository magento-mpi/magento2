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
             'city' => 'Harrisburg',
             'company' => 'The Flying Bear',
             'fax' => '717-503-8908',
             'first_name' => 'Ronald',
             'last_name' => 'Armstrong',
             'middle_name' => 'M.',
             'zip_code' => '17111',
             'state' => 'Pennsylvania',
             'street_address_line_1' => '154 Saint James Drive',
             'street_address_line_2' => '',
             'telephone' => '717-503-8908',
         )
     ),
     $this->loadDataSet('Customers', 'generic_address', array(
             'city' => 'Boston',
             'company' => 'Quality Event Planner',
             'fax' => '617-956-7518',
             'first_name' => 'Jeremy',
             'last_name' => 'Bradbury',
             'middle_name' => 'D.',
             'zip_code' => '02109',
             'state' => 'Massachusetts',
             'street_address_line_1' => '2524 Rainy Day Drive',
             'street_address_line_2' => '',
             'telephone' => '617-956-7518',
         )
     ),
 );