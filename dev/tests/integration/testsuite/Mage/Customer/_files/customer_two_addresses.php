<?php
/**
 * Customer address fixture with entity_id = 2, this fixture also creates address with entity_id = 1
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

require 'customer_address.php';

/** @var Mage_Customer_Model_Address $customerAddress */
$customerAddress = Mage::getModel('Mage_Customer_Model_Address');
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
        'parent_id' => 1,
        'created_at' => date('YYY-MM-DD hh:mm:ss')
    ));
$customerAddress->save();
