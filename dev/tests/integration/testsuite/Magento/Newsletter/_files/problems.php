<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

require "queue.php";

$problem = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Newsletter\Model\problem');
$problem->setSubscriberId($subscriber->getSubscriberId())
    ->setQueueId($queue->getQueueId())
    ->setProblemErrorCode(11)
    ->setProblemErrorText('error text')
    ->save();


