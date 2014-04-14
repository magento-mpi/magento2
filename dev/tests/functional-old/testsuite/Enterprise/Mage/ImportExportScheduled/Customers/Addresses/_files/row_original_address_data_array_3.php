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
              'city' => 'Baltimore',
              'company' => 'Strength Gurus',
              'fax' => '443-337-8871',
              'first_name' => 'Henry',
              'last_name' => 'Page',
              'middle_name' => 'K.',
              'zip_code' => '21202',
              'state' => 'Maryland',
              'street_address_line_1' => '2715 Calvin Street',
              'street_address_line_2' => '',
              'telephone' => '443-337-8871',
          )
      ), null
  );