<?php
/**
 * Save quote_with_giftcard_saved_rollback fixture
 *
 * The quote is not saved inside the original fixture. It is later saved inside child fixtures, but along with some
 * additional data which may break some tests.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
$quote = $objectManager->create('\Magento\Sales\Model\Quote');
$quote->load('test_order_1', 'reserved_order_id')->delete();
