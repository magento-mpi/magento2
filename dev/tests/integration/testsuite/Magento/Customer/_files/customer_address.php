<?php
/**
 * Customer address fixture with entity_id = 1
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
/** @var \Magento\Customer\Model\Address $customerAddress */
$customerAddress = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Customer\Model\Address');
$customerAddress->isObjectNew(true);
$customerAddress->setData(
    [
        'entity_id' => 1,
        'attribute_set_id' => 2,
        'telephone' => 3468676,
        'postcode' => 75477,
        'country_id' => 'US',
        'city' => 'CityM',
        'company' => 'CompanyName',
        'street' => ['Green str, 67'],
        'lastname' => 'Smith',
        'firstname' => 'John',
        'parent_id' => 1,
        'region_id' => 1,
    ]
)->setCustomerId(1);
$customerAddress->save();
