<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Paas
 * @package     tests
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

//Add customer
$customer = new Mage_Customer_Model_Customer();
$customer->setStoreId(1)
    ->setCreatedIn('Default Store View')
    ->setDefaultBilling(1)
    ->setDefaultShipping(1)
    ->setEmail('mr.test'.time().'@test.com')
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
CustomerBalance_CustomerBalanceTest::$customer = $customer;

//Add customer without balance
$customer = new Mage_Customer_Model_Customer();
$customer->setStoreId(1)
    ->setCreatedIn('Default Store View')
    ->setDefaultBilling(1)
    ->setDefaultShipping(1)
    ->setEmail('mr.test2'.time().'@test.com')
    ->setFirstname('Test 2')
    ->setLastname('Test 2')
    ->setMiddlename('Test 2')
    ->setGroupId(1)
    ->setRewardUpdateNotification(1)
    ->setRewardWarningNotification(1)
    ->save();

//Save customer without balance ID
CustomerBalance_CustomerBalanceTest::$customerWithoutBalance = $customer;
