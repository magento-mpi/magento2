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

// create test customer
/** @var $customer Mage_Customer_Model_Customer */
$customer = Mage::getModel('Mage_Customer_Model_Customer');
$customer->setEmail('customer_finance_test@test.com');
$customer->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
$customer->save();

// create store credit and reward points
/** @var $helper Enterprise_ImportExport_Helper_Data */
$helper = Mage::helper('Enterprise_ImportExport_Helper_Data');

/** @var $customerBalance Enterprise_CustomerBalance_Model_Balance */
$customerBalance = Mage::getModel('Enterprise_CustomerBalance_Model_Balance');
$customerBalance->setCustomerId($customer->getId());
$customerBalance->setAmountDelta(100);
$customerBalance->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
$customerBalance->save();

/** @var $rewardPoints Enterprise_Reward_Model_Reward */
$rewardPoints = Mage::getModel('Enterprise_Reward_Model_Reward');
$rewardPoints->setCustomerId($customer->getId());
$rewardPoints->setPointsBalance(50);
$rewardPoints->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
$rewardPoints->save();
