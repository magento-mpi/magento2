<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
/** @var \Magento\Customer\Model\Customer $customer */
$customer = Mage::getModel('\Magento\Customer\Model\Customer');

$customerData = array(
    'group_id' => 1,
    'website_id' => 1,
    'firstname' => 'test firstname',
    'lastname' => 'test lastname',
    'email' => 'exmaple@domain.com',
    'default_billing' => '_item1',
    'password' => '123123q'
 );
$customer->setData($customerData);
$customer->setId(1);

/** @var \Magento\Customer\Model\Address $addressOne  */
$addressOne = Mage::getModel('\Magento\Customer\Model\Address');
$addressOneData = array(
    'firstname' => 'test firstname',
    'lastname' => 'test lastname',
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
$addressTwo = Mage::getModel('\Magento\Customer\Model\Address');
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
$addressThree = Mage::getModel('\Magento\Customer\Model\Address');
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
