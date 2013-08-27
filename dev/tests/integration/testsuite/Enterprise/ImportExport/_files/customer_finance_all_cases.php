<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_ImportExport
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $helper Enterprise_ImportExport_Helper_Data */
$helper = Mage::helper('Enterprise_ImportExport_Helper_Data');

// customer with reward points and customer balance
/** @var $customer Magento_Customer_Model_Customer */
$customer = Mage::getModel('Magento_Customer_Model_Customer');
$customer->addData(array(
    'firstname' => 'Test',
    'lastname' => 'User'
));
$customerEmail = 'customer_finance_test_rp_cb@test.com';
$registerKey = 'customer_finance_email_rp_cb';
Mage::unregister($registerKey);
Mage::register($registerKey, $customerEmail);
$customer->setEmail($customerEmail);
$customer->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
$customer->save();

/** @var $customerBalance Enterprise_CustomerBalance_Model_Balance */
$customerBalance = Mage::getModel('Enterprise_CustomerBalance_Model_Balance');
$customerBalance->setCustomerId($customer->getId());
$customerBalance->setAmountDelta(10);
$customerBalance->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
$customerBalance->save();

/** @var $rewardPoints Enterprise_Reward_Model_Reward */
$rewardPoints = Mage::getModel('Enterprise_Reward_Model_Reward');
$rewardPoints->setCustomerId($customer->getId());
$rewardPoints->setPointsBalance(20);
$rewardPoints->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
$rewardPoints->save();

// customer with reward points and without customer balance
/** @var $customer Magento_Customer_Model_Customer */
$customer = Mage::getModel('Magento_Customer_Model_Customer');
$customer->addData(array(
    'firstname' => 'Test',
    'lastname' => 'User'
));
$customerEmail = 'customer_finance_test_rp@test.com';
$registerKey = 'customer_finance_email_rp';
Mage::unregister($registerKey);
Mage::register($registerKey, $customerEmail);
$customer->setEmail($customerEmail);
$customer->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
$customer->save();

/** @var $rewardPoints Enterprise_Reward_Model_Reward */
$rewardPoints = Mage::getModel('Enterprise_Reward_Model_Reward');
$rewardPoints->setCustomerId($customer->getId());
$rewardPoints->setPointsBalance(20);
$rewardPoints->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
$rewardPoints->save();

// customer without reward points and with customer balance
/** @var $customer Magento_Customer_Model_Customer */
$customer = Mage::getModel('Magento_Customer_Model_Customer');
$customer->addData(array(
    'firstname' => 'Test',
    'lastname' => 'User'
));
$customerEmail = 'customer_finance_test_cb@test.com';
$registerKey = 'customer_finance_email_cb';
Mage::unregister($registerKey);
Mage::register($registerKey, $customerEmail);
$customer->setEmail($customerEmail);
$customer->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
$customer->save();

/** @var $customerBalance Enterprise_CustomerBalance_Model_Balance */
$customerBalance = Mage::getModel('Enterprise_CustomerBalance_Model_Balance');
$customerBalance->setCustomerId($customer->getId());
$customerBalance->setAmountDelta(10);
$customerBalance->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
$customerBalance->save();

// customer without reward points and customer balance
/** @var $customer Magento_Customer_Model_Customer */
$customer = Mage::getModel('Magento_Customer_Model_Customer');
$customer->addData(array(
    'firstname' => 'Test',
    'lastname' => 'User'
));
$customerEmail = 'customer_finance_test@test.com';
$registerKey = 'customer_finance_email';
Mage::unregister($registerKey);
Mage::register($registerKey, $customerEmail);
$customer->setEmail($customerEmail);
$customer->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
$customer->save();
