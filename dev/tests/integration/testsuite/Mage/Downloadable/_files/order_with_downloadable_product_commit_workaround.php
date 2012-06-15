<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Downloadable
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
require __DIR__ . '/order_with_downloadable_product.php';
$order->afterCommitCallback();
