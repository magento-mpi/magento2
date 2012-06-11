<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_GiftCard
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
require __DIR__ . '/order_with_gift_card.php';
$order->afterCommitCallback();
