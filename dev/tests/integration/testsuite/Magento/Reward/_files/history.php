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
/** @var $reward \Magento\Reward\Model\Reward */
$reward = Mage::getModel('Magento\Reward\Model\Reward');
$reward->setCustomerId(1)
    ->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
$reward->save();

/** @var $history \Magento\Reward\Model\Reward\History */
$history = Mage::getModel('Magento\Reward\Model\Reward\History');
$history->setRewardId($reward->getId())
    ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
    ->setAdditionalData(serialize('any non-empty string'));
$history->save();
