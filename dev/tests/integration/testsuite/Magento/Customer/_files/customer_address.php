<?php
/**
 * Customer address fixture with entity_id = 1
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define('FIXTURE_CUSTOMER_ADDRESS_ID', 1);
/** @var \Magento\Customer\Model\Address $customerAddress */
$customerAddress = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Customer\Model\Address');
$customerAddress->isObjectNew(true);
$customerAddress
    ->setData(array(
        'entity_id' => FIXTURE_CUSTOMER_ADDRESS_ID,
        'attribute_set_id' => 2,
        'telephone' => 3468676,
        'postcode' => 75477,
        'country_id' => 'US',
        'city' => 'CityM',
        'street' => array('Green str, 67'),
        'lastname' => 'Smith',
        'firstname' => 'John',
        'parent_id' => 1,
        'region_id' => 1
    ))
    ->setCustomerId(1);
$customerAddress->save();
