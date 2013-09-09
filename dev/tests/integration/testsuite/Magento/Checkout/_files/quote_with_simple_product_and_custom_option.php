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

/** @var $product Magento_Catalog_Model_Product */
$product = Mage::getModel('Magento_Catalog_Model_Product');
$product->load(1);

$options = array();

/** @var $option Magento_Catalog_Model_Product_Option */
foreach ($product->getOptions() as $option) {
    switch ($option->getGroupByType()) {
        case Magento_Catalog_Model_Product_Option::OPTION_GROUP_DATE:
            $value = array('year' => 2013, 'month' => 8, 'day' => 9, 'hour' => 13, 'minute' => 35);
            break;
        case Magento_Catalog_Model_Product_Option::OPTION_GROUP_SELECT:
            $value = key($option->getValues());
            break;
        default:
            $value = 'test';
            break;

    }
    $options[$option->getId()] = $value;
}

$requestInfo = new Magento_Object(array(
    'qty' => 1,
    'options' => $options,
));

/** @var $cart Magento_Checkout_Model_Cart */
$cart = Mage::getModel('Magento_Checkout_Model_Cart');
$cart->addProduct($product, $requestInfo);
$cart->save();

/** @var $objectManager Magento_Test_ObjectManager */
$objectManager = Magento_Test_Helper_Bootstrap::getObjectManager();
$objectManager->get('Magento_Core_Model_Registry')->unregister('_singleton/Magento_Checkout_Model_Session');

/** @var $objectManager Magento_Test_ObjectManager */
$objectManager = Magento_Test_Helper_Bootstrap::getObjectManager();
$objectManager->removeSharedInstance('Magento_Checkout_Model_Session');
