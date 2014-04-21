<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */


$website = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Store\Model\Website');
$website->setName('new Website')->setCode('newwebsite')->save();

$websiteId = $website->getId();


$customer = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Customer\Model\Customer');
/** @var Magento\Customer\Model\Customer $customer */
$customer->setWebsiteId(
    $websiteId
)->setId(
        1
    )->setEntityTypeId(
        1
    )->setAttributeSetId(
        1
    )->setEmail(
        'customer2@example.com'
    )->setPassword(
        'password'
    )->setGroupId(
        1
    )->setStoreId(
        1
    )->setIsActive(
        1
    )->setFirstname(
        'Firstname'
    )->setLastname(
        'Lastname'
    )->setDefaultBilling(
        1
    )->setDefaultShipping(
        1
    );
$customer->isObjectNew(true);

/** @var \Magento\Customer\Model\Address $addressOne  */
$addressOne = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Customer\Model\Address');
$addressOneData = array(
    'firstname' => 'Firstname',
    'lastname' => 'LastName',
    'street' => array('test street'),
    'city' => 'test city',
    'country_id' => 'US',
    'postcode' => '01001',
    'telephone' => '+7000000001',
    'entity_id' => 1
);
$addressOne->setData($addressOneData);
$customer->addAddress($addressOne);

/** @var \Magento\Customer\Model\Address $addressTwo  */
$addressTwo = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Customer\Model\Address');
$addressTwoData = array(
    'firstname' => 'test firstname',
    'lastname' => 'test lastname',
    'street' => array('test street'),
    'city' => 'test city',
    'country_id' => 'US',
    'postcode' => '01001',
    'telephone' => '+7000000001',
    'entity_id' => 2
);
$addressTwo->setData($addressTwoData);
$customer->addAddress($addressTwo);

/** @var \Magento\Customer\Model\Address $addressThree  */
$addressThree = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Customer\Model\Address');
$addressThreeData = array(
    'firstname' => 'removed firstname',
    'lastname' => 'removed lastname',
    'street' => array('removed street'),
    'city' => 'removed city',
    'country_id' => 'US',
    'postcode' => '01001',
    'telephone' => '+7000000001',
    'entity_id' => 3
);
$addressThree->setData($addressThreeData);
$customer->addAddress($addressThree);
$customer->save();
