<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var \Magento\GiftMessage\Model\Message $message */
$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
$quote = $objectManager->create('Magento\Sales\Model\Quote');
$quote->load('test_order_item_with_message', 'reserved_order_id');
$message = $objectManager->create('\Magento\GiftMessage\Model\Message');

foreach ($quote->getAllItems() as $item)
{
    $message->load($item->getGiftMessageId());
    $message->delete();
};
$quote->delete();
