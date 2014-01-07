<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

require __DIR__ . '/../../../Magento/Customer/_files/customer.php';

/** @var $reward \Magento\Reward\Model\Reward */
$reward = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Reward\Model\Reward');
$reward->setCustomerId(1)
    ->setWebsiteId(1);
$reward->save();

return $reward;
