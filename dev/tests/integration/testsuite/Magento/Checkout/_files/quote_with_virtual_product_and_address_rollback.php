<?php
/**
 * Rollback for quote_with_virtual_product_and_address.php fixture.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/** @var $objectManager \Magento\TestFramework\ObjectManager */
$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
$quote = $objectManager->create('\Magento\Sales\Model\Quote');
$quote->load('test_order_with_virtual_product', 'reserved_order_id')->delete();
