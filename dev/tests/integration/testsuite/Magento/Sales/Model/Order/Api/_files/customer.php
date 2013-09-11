<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
$customer = Mage::getModel('Magento\Customer\Model\Customer');
$customer->setStoreId(1)
    ->setCreatedIn('Default Store View')
    ->setDefaultBilling(1)
    ->setDefaultShipping(1)
    ->setEmail('mr.test.creditmemo.' . uniqid() . '@test.com')
    ->setFirstname('Test')
    ->setLastname('Test')
    ->setMiddlename('Test')
    ->setGroupId(1)
    ->save();
Mage::register('customer', $customer);

$customerAddress = Mage::getModel('Magento\Customer\Model\Address');
$customerAddress->setData(
    array(
        'city' => 'New York',
        'country_id' => 'US',
        'fax' => '56-987-987',
        'firstname' => 'Jacklin',
        'lastname' => 'Sparrow',
        'middlename' => 'John',
        'postcode' => '10012',
        'region' => 'New York',
        'region_id' => '43',
        'street' => 'Main Street',
        'telephone' => '718-452-9207',
        'is_default_billing' => true,
        'is_default_shipping' => true
    )
);
$customerAddress->setCustomer($customer);
$customerAddress->save();
Mage::register('customer_address', $customerAddress);

//Set customer default shipping and billing address
$customer->addAddress($customerAddress);
$customer->setDefaultShipping($customerAddress->getId());
$customer->setDefaultBilling($customerAddress->getId());
$customer->save();
