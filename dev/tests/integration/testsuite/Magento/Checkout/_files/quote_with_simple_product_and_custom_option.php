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

require __DIR__ . '/../../../Magento/Catalog/_files/product_simple.php';

/** @var $product \Magento\Catalog\Model\Product */
$product = Mage::getModel('Magento\Catalog\Model\Product');
$product->load(1);

$options = array();

/** @var $option \Magento\Catalog\Model\Product\Option */
foreach ($product->getOptions() as $option) {
    switch ($option->getGroupByType()) {
        case \Magento\Catalog\Model\Product\Option::OPTION_GROUP_DATE:
            $value = array('year' => 2013, 'month' => 8, 'day' => 9, 'hour' => 13, 'minute' => 35);
            break;
        case \Magento\Catalog\Model\Product\Option::OPTION_GROUP_SELECT:
            $value = key($option->getValues());
            break;
        default:
            $value = 'test';
            break;

    }
    $options[$option->getId()] = $value;
}

$requestInfo = new \Magento\Object(array(
    'qty' => 1,
    'options' => $options,
));

/** @var $cart \Magento\Checkout\Model\Cart */
$cart = Mage::getModel('Magento\Checkout\Model\Cart');
$cart->addProduct($product, $requestInfo);
$cart->save();

Mage::unregister('_singleton/\Magento\Checkout\Model\Session');

/** @var $objectManager Magento_TestFramework_ObjectManager */
$objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
$objectManager->removeSharedInstance('\Magento\Checkout\Model\Session');
