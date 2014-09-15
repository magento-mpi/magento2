<?php
/**
 * Rollback for quote_with_payment_saved.php fixture.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $objectManager \Magento\TestFramework\ObjectManager */
$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
$quote = $objectManager->create('\Magento\Sales\Model\Quote');
$quote->load('test_order_1_with_payment', 'reserved_order_id')->delete();