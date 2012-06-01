<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

require_once 'customer.php';
$customer = Magento_Test_Webservice::getFixture('creditmemo/customer');

$customerAddress = new Mage_Customer_Model_Address();
$customerAddress->setData(array(
    'city'                => 'New York',
    'country_id'          => 'US',
    'fax'                 => '56-987-987',
    'firstname'           => 'Jacklin',
    'lastname'            => 'Sparrow',
    'middlename'          => 'John',
    'postcode'            => '10012',
    'region'              => 'New York',
    'region_id'           => '43',
    'street'              => 'Main Street',
    'telephone'           => '718-452-9207',
    'is_default_billing'  => true,
    'is_default_shipping' => true
));
$customerAddress->setCustomer($customer);
$customerAddress->save();
Magento_Test_Webservice::setFixture('creditmemo/customer_address', $customerAddress);
