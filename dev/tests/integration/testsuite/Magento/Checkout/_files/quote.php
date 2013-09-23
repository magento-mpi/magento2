<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$quote = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Sales_Model_Quote');
$quote->setData(array(
    'store_id' => 1,
    'is_active' => 0,
    'is_multi_shipping' => 0
));
$quote->save();
