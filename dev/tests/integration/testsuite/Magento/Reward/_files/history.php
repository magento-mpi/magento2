<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/** @var $reward \Magento\Reward\Model\Reward */
$reward = require __DIR__ . '/../../../Magento/Reward/_files/reward.php';

/** @var $history \Magento\Reward\Model\Reward\History */
$history = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Reward\Model\Reward\History');
$history->setRewardId($reward->getId())->setWebsiteId(1)->addAdditionalData(['email' => 'test@example.com']);
$history->save();
