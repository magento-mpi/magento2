<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

require __DIR__ . '/../../../Magento/Catalog/_files/product_simple.php';

/** @var $product \Magento\Catalog\Model\Product */
$product = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Catalog\Model\Product');
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

$requestInfo = new \Magento\Framework\Object(array('qty' => 1, 'options' => $options));

/** @var $cart \Magento\Checkout\Model\Cart */
$cart = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Checkout\Model\Cart');
$cart->addProduct($product, $requestInfo);
$cart->save();

/** @var $objectManager \Magento\TestFramework\ObjectManager */
$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
$objectManager->removeSharedInstance('Magento\Checkout\Model\Session');
