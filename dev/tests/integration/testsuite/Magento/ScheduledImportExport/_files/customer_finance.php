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

// add new website
/** @var $website \Magento\Core\Model\Website */
$website = Mage::getModel('Magento\Core\Model\Website');
$website->setCode('finance_website')
    ->setName('Finance Website');
$website->save();
Mage::app()->reinitStores();

// create test customer
/** @var $customer \Magento\Customer\Model\Customer */
$customer = Mage::getModel('Magento\Customer\Model\Customer');
$customer->addData(array(
    'firstname' => 'Test',
    'lastname' => 'User'
));
$customerEmail = 'customer_finance_test@test.com';
$registerKey = 'customer_finance_email';
/** @var $objectManager Magento_TestFramework_ObjectManager */
$objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
$objectManager->get('Magento\Core\Model\Registry')->unregister($registerKey);
$objectManager->get('Magento\Core\Model\Registry')->register($registerKey, $customerEmail);
$customer->setEmail($customerEmail);
$customer->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
$customer->save();

// create store credit and reward points
/** @var $helper Magento_ScheduledImportExport_Helper_Data */
$helper = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento\ScheduledImportExport\Helper\Data');

// increment to modify balance values
$increment = 0;
/** @var $website \Magento\Core\Model\Website */
foreach (Mage::app()->getWebsites() as $website) {
    $increment += 10;

    /** @var $customerBalance \Magento\CustomerBalance\Model\Balance */
    $customerBalance = Mage::getModel('Magento\CustomerBalance\Model\Balance');
    $customerBalance->setCustomerId($customer->getId());
    $customerBalanceAmount = 50 + $increment;
    $registerKey = 'customer_balance_' . $website->getCode();
    $objectManager->get('Magento_Core_Model_Registry')->unregister($registerKey);
    $objectManager->get('Magento_Core_Model_Registry')->register($registerKey, $customerBalanceAmount);
    $customerBalance->setAmountDelta($customerBalanceAmount);
    $customerBalance->setWebsiteId($website->getId());
    $customerBalance->save();

    /** @var $rewardPoints \Magento\Reward\Model\Reward */
    $rewardPoints = Mage::getModel('Magento\Reward\Model\Reward');
    $rewardPoints->setCustomerId($customer->getId());
    $rewardPointsBalance = 100 + $increment;
    $registerKey = 'reward_point_balance_' . $website->getCode();
    $objectManager->get('Magento_Core_Model_Registry')->unregister($registerKey);
    $objectManager->get('Magento_Core_Model_Registry')->register($registerKey, $rewardPointsBalance);
    $rewardPoints->setPointsBalance($rewardPointsBalance);
    $rewardPoints->setWebsiteId($website->getId());
    $rewardPoints->save();
}
