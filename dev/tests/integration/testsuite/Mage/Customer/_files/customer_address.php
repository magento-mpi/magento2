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
        'parent_id' => 1
    ));
$customerAddress->save();
