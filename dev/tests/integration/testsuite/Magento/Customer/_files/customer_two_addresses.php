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

/** @var \Magento\Customer\Model\Address $customerAddress */
$customerAddress = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Customer\Model\Address');
$customerAddress->isObjectNew(true);
$customerAddress->setCustomerId(1)
    ->setData(array(
        'entity_id' => 2,
        'attribute_set_id' => 2,
        'telephone' => 3234676,
        'postcode' => 47676,
        'country_id' => 'US',
        'city' => 'CityX',
        'street' => 'Black str, 48',
        'lastname' => 'Smith',
        'firstname' => 'John',
        'parent_id' => 1,
        'region_id' => 1
    ));
$customerAddress->save();
