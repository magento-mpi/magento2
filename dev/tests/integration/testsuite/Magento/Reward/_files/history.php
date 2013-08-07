<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reward
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

include __DIR__ . '/../../../Magento/Customer/_files/customer.php';
/** @var $reward Magento_Reward_Model_Reward */
$reward = Mage::getModel('Magento_Reward_Model_Reward');
$reward->setCustomerId(1)
    ->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
$reward->save();

/** @var $history Magento_Reward_Model_Reward_History */
$history = Mage::getModel('Magento_Reward_Model_Reward_History');
$history->setRewardId($reward->getId())
    ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
    ->setAdditionalData(serialize('any non-empty string'));
$history->save();
