<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

require __DIR__ . '/../../Checkout/_files/quote_with_address.php';

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
$quote->setReservedOrderId(
    'test_order_item_with_message'
);
$quote->collectTotals()->save();

/** @var \Magento\GiftMessage\Model\Message $message */
$message = $objectManager->create('\Magento\GiftMessage\Model\Message');
$message->setSender('John Doe');
$message->setRecipient('Jane Roe');
$message->setMessage('Gift Message Text');
$message->save();
foreach ($quote->getAllItems() as $item)
{
    $item->setGiftMessageId($message->getId())->save();
}