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

/** @var $helper \Magento\ScheduledImportExport\Helper\Data */
$helper = Mage::helper('Magento\ScheduledImportExport\Helper\Data');

// customer with reward points and customer balance
/** @var $customer \Magento\Customer\Model\Customer */
$customer = Mage::getModel('\Magento\Customer\Model\Customer');
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

/** @var $customerBalance \Magento\CustomerBalance\Model\Balance */
$customerBalance = Mage::getModel('\Magento\CustomerBalance\Model\Balance');
$customerBalance->setCustomerId($customer->getId());
$customerBalance->setAmountDelta(10);
$customerBalance->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
$customerBalance->save();

/** @var $rewardPoints \Magento\Reward\Model\Reward */
$rewardPoints = Mage::getModel('\Magento\Reward\Model\Reward');
$rewardPoints->setCustomerId($customer->getId());
$rewardPoints->setPointsBalance(20);
$rewardPoints->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
$rewardPoints->save();

// customer with reward points and without customer balance
/** @var $customer \Magento\Customer\Model\Customer */
$customer = Mage::getModel('\Magento\Customer\Model\Customer');
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

/** @var $rewardPoints \Magento\Reward\Model\Reward */
$rewardPoints = Mage::getModel('\Magento\Reward\Model\Reward');
$rewardPoints->setCustomerId($customer->getId());
$rewardPoints->setPointsBalance(20);
$rewardPoints->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
$rewardPoints->save();

// customer without reward points and with customer balance
/** @var $customer \Magento\Customer\Model\Customer */
$customer = Mage::getModel('\Magento\Customer\Model\Customer');
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

/** @var $customerBalance \Magento\CustomerBalance\Model\Balance */
$customerBalance = Mage::getModel('\Magento\CustomerBalance\Model\Balance');
$customerBalance->setCustomerId($customer->getId());
$customerBalance->setAmountDelta(10);
$customerBalance->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
$customerBalance->save();

// customer without reward points and customer balance
/** @var $customer \Magento\Customer\Model\Customer */
$customer = Mage::getModel('\Magento\Customer\Model\Customer');
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
