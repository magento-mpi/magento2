<?php
/**
 * Customer address fixture with entity_id = 1
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var Mage_Customer_Model_Address $customerAddress */
$customerAddress = Mage::getModel('Mage_Customer_Model_Address');
$customerAddress->setCustomerId(1)
    ->setData(array(
        'entity_id' => 1,
        'telephone' => 3468676,
        'postcode' => 75477,
        'country_id' => 'AL',
        'city' => 'CityM',
        'street' => 'Green str, 67',
        'lastname' => 'Smith',
        'firstname' => 'John',
        'parent_id' => 1,
        'created_at' => date('YYY-MM-DD hh:mm:ss')
    ));
$customerAddress->save();
