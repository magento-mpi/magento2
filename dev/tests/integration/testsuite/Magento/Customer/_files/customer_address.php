<?php
/**
 * Customer address fixture with entity_id = 1
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var \Magento\Customer\Model\Address $customerAddress */
$customerAddress = Mage::getModel('\Magento\Customer\Model\Address');
$customerAddress->isObjectNew(true);
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
