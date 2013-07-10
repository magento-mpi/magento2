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
             'city' => 'Washington',
             'company' => 'Sound Warehouse',
             'fax' => '586-786-9753',
             'first_name' => 'Thomas',
             'last_name' => 'Keeney',
             'middle_name' => 'A.',
             'zip_code' => '48094',
             'state' => 'Michigan',
             'street_address_line_1' => '3245 Ritter Avenue',
             'street_address_line_2' => '',
             'telephone' => '586-786-9753',
         )
     ), null
 );