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

$customerAddress = new Mage_Customer_Model_Address();
$customerAddress->setData(array(
    'city'                => 'New York',
    'country_id'          => 'US',
    'fax'                 => '56-987-987' . uniqid(),
    'firstname'           => 'Jacklin' . uniqid(),
    'lastname'            => 'Sparrow' . uniqid(),
    'middlename'          => 'John' . uniqid(),
    'postcode'            => '10012',
    'region'              => 'New York',
    'street'              => array('Main Street' . uniqid(), 'Addithional Street' . uniqid()),
    'telephone'           => '718-452-9207' . uniqid(),
    'is_default_billing'  => true,
    'is_default_shipping' => true
));
return $customerAddress;
