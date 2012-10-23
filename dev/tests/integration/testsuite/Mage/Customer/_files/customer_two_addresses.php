<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Customer
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

require 'customer_address.php';

$customerAddress = new Mage_Customer_Model_Address();
$customerAddress->setCustomerId(1)
    ->setData(array(
        'entity_id' => 2,
        'telephone' => 3234676,
        'postcode' => 47676,
        'country_id' => 'AL',
        'city' => 'CityX',
        'street' => 'Black str, 48',
        'lastname' => 'Smith',
        'firstname' => 'John',
        'parent_id' => 1
    ));
$customerAddress->save();
