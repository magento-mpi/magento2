<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */

//Add customer
$customer = Mage::getModel('\Magento\Customer\Model\Customer');
$customer->setStoreId(1)
    ->setWebsiteId(1)
    ->setCreatedIn('Default Store View')
    ->setDefaultBilling(1)
    ->setDefaultShipping(1)
    ->setEmail('mr.test' . uniqid() . '@test.com')
    ->setFirstname('Test')
    ->setLastname('Test')
    ->setMiddlename('Test')
    ->setGroupId(1)
    ->setRewardUpdateNotification(1)
    ->setRewardWarningNotification(1)
    ->save();

//Change customer balance several times to create balance with history
$customerBalance = Mage::getModel('\Magento\CustomerBalance\Model\Balance');
$customerBalance->setCustomerId($customer->getId())
    ->setWebsiteId(1)
    ->setAmountDelta(1000)
    ->setBaseCurrencyCode('EN')
    ->save();

$customerBalance->setCustomerId($customer->getId())
    ->setWebsiteId(1)
    ->setAmountDelta(100)
    ->setBaseCurrencyCode('EN')
    ->setAdditionalInfo('Test')
    ->save();

//Save customer ID
Magento_CustomerBalance_Model_ApiTest::$customer = $customer;

//Add customer without balance
$customerWithoutBalance = Mage::getModel('\Magento\Customer\Model\Customer');
$customerWithoutBalance->setStoreId(1)
    ->setWebsiteId(1)
    ->setCreatedIn('Default Store View')
    ->setDefaultBilling(1)
    ->setDefaultShipping(1)
    ->setEmail('mr.test2' . uniqid() . '@test.com')
    ->setFirstname('Test 2')
    ->setLastname('Test 2')
    ->setMiddlename('Test 2')
    ->setGroupId(1)
    ->setRewardUpdateNotification(1)
    ->setRewardWarningNotification(1)
    ->save();

//Save customer without balance ID
Magento_CustomerBalance_Model_ApiTest::$customerNoBalance = $customerWithoutBalance;
