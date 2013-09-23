<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ScheduledImportExport
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $helper Magento_ScheduledImportExport_Helper_Data */
$helper = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_ScheduledImportExport_Helper_Data');

// customer with reward points and customer balance
/** @var $customer Magento_Customer_Model_Customer */
$customer = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Customer_Model_Customer');
$customer->addData(array(
    'firstname' => 'Test',
    'lastname' => 'User'
));
$customerEmail = 'customer_finance_test_rp_cb@test.com';
$registerKey = 'customer_finance_email_rp_cb';
/** @var $objectManager Magento_TestFramework_ObjectManager */
$objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
$objectManager->get('Magento_Core_Model_Registry')->unregister($registerKey);
$objectManager->get('Magento_Core_Model_Registry')->register($registerKey, $customerEmail);
$customer->setEmail($customerEmail);
$customer->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
$customer->save();

/** @var $customerBalance Magento_CustomerBalance_Model_Balance */
$customerBalance = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_CustomerBalance_Model_Balance');
$customerBalance->setCustomerId($customer->getId());
$customerBalance->setAmountDelta(10);
$customerBalance->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
$customerBalance->save();

/** @var $rewardPoints Magento_Reward_Model_Reward */
$rewardPoints = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Reward_Model_Reward');
$rewardPoints->setCustomerId($customer->getId());
$rewardPoints->setPointsBalance(20);
$rewardPoints->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
$rewardPoints->save();

// customer with reward points and without customer balance
/** @var $customer Magento_Customer_Model_Customer */
$customer = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Customer_Model_Customer');
$customer->addData(array(
    'firstname' => 'Test',
    'lastname' => 'User'
));
$customerEmail = 'customer_finance_test_rp@test.com';
$registerKey = 'customer_finance_email_rp';
$objectManager->get('Magento_Core_Model_Registry')->unregister($registerKey);
$objectManager->get('Magento_Core_Model_Registry')->register($registerKey, $customerEmail);
$customer->setEmail($customerEmail);
$customer->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
$customer->save();

/** @var $rewardPoints Magento_Reward_Model_Reward */
$rewardPoints = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Reward_Model_Reward');
$rewardPoints->setCustomerId($customer->getId());
$rewardPoints->setPointsBalance(20);
$rewardPoints->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
$rewardPoints->save();

// customer without reward points and with customer balance
/** @var $customer Magento_Customer_Model_Customer */
$customer = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Customer_Model_Customer');
$customer->addData(array(
    'firstname' => 'Test',
    'lastname' => 'User'
));
$customerEmail = 'customer_finance_test_cb@test.com';
$registerKey = 'customer_finance_email_cb';
$objectManager->get('Magento_Core_Model_Registry')->unregister($registerKey);
$objectManager->get('Magento_Core_Model_Registry')->register($registerKey, $customerEmail);
$customer->setEmail($customerEmail);
$customer->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
$customer->save();

/** @var $customerBalance Magento_CustomerBalance_Model_Balance */
$customerBalance = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_CustomerBalance_Model_Balance');
$customerBalance->setCustomerId($customer->getId());
$customerBalance->setAmountDelta(10);
$customerBalance->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
$customerBalance->save();

// customer without reward points and customer balance
/** @var $customer Magento_Customer_Model_Customer */
$customer = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Customer_Model_Customer');
$customer->addData(array(
    'firstname' => 'Test',
    'lastname' => 'User'
));
$customerEmail = 'customer_finance_test@test.com';
$registerKey = 'customer_finance_email';
$objectManager->get('Magento_Core_Model_Registry')->unregister($registerKey);
$objectManager->get('Magento_Core_Model_Registry')->register($registerKey, $customerEmail);
$customer->setEmail($customerEmail);
$customer->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
$customer->save();
