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

$address = new Mage_Sales_Model_Quote_Address();
$address->setData(array(
    'city'                => 'New York',
    'country_id'          => 'US',
    'fax'                 => '56-987-987' . uniqid(),
    'firstname'           => 'Jacklin' . uniqid(),
    'lastname'            => 'Sparrow' . uniqid(),
    'middlename'          => 'John' . uniqid(),
    'postcode'            => '10012',
    'region'              => 'New York',
    'region_id'           => '43',
    'street'              => 'Main Street',
    'telephone'           => '718-452-9207' . uniqid(),
    'is_default_billing'  => true,
    'is_default_shipping' => true,
    'shipping_method'     => 'freeshipping_freeshipping'
));
return $address;
