<?php
/**
 * {license_notice}
 *
 * @category    Paas
 * @package     tests
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

//Add customer
$customer = new Mage_Customer_Model_Customer();
$customer->setStoreId(1)
    ->setCreatedIn('Default Store View')
    ->setDefaultBilling(1)
    ->setDefaultShipping(1)
    ->setEmail('mr.test'.uniqid().'@test.com')
    ->setFirstname('Test')
    ->setLastname('Test')
    ->setMiddlename('Test')
    ->setGroupId(1)
    ->setRewardUpdateNotification(1)
    ->setRewardWarningNotification(1)
    ->save();

//Change customer balance several times to create balance with history
$customerBalance = new Enterprise_CustomerBalance_Model_Balance();
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
Api_CustomerBalance_CustomerBalanceTest::$customer = $customer;

//Add customer without balance
$customerWithoutBalance = new Mage_Customer_Model_Customer();
$customerWithoutBalance->setStoreId(1)
    ->setCreatedIn('Default Store View')
    ->setDefaultBilling(1)
    ->setDefaultShipping(1)
    ->setEmail('mr.test2'.uniqid().'@test.com')
    ->setFirstname('Test 2')
    ->setLastname('Test 2')
    ->setMiddlename('Test 2')
    ->setGroupId(1)
    ->setRewardUpdateNotification(1)
    ->setRewardWarningNotification(1)
    ->save();

//Save customer without balance ID
Api_CustomerBalance_CustomerBalanceTest::$customerWithoutBalance = $customerWithoutBalance;
