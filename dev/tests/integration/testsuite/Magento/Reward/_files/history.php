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
$reward = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Reward\Model\Reward');
$reward->setCustomerId(1)
    ->setWebsiteId(
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\StoreManagerInterface')
            ->getStore()->getWebsiteId()
    );
$reward->save();

/** @var $history \Magento\Reward\Model\Reward\History */
$history = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Reward\Model\Reward\History');
$history->setRewardId($reward->getId())
    ->setWebsiteId(
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\StoreManagerInterface')
            ->getStore()->getWebsiteId()
    )
    ->setAdditionalData(serialize('any non-empty string'));
$history->save();
