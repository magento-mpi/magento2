<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Checkout
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

require __DIR__ . '/../../Catalog/_files/product_simple.php';

/** @var $product Mage_Catalog_Model_Product */
$product = Mage::getModel('Mage_Catalog_Model_Product');
$product->load(1);

$options = array();

/** @var $option Mage_Catalog_Model_Product_Option */
foreach ($product->getOptions() as $option) {
    switch ($option->getGroupByType()) {
        case Mage_Catalog_Model_Product_Option::OPTION_GROUP_DATE:
            $value = Mage::getModel('Magento_Core_Model_Date')->date();
            break;
        case Mage_Catalog_Model_Product_Option::OPTION_GROUP_SELECT:
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

/** @var $cart Mage_Checkout_Model_Cart */
$cart = Mage::getModel('Mage_Checkout_Model_Cart');
$cart->addProduct($product, $requestInfo);
$cart->save();

Mage::unregister('_singleton/Mage_Checkout_Model_Session');

/** @var $objectManager Magento_Test_ObjectManager */
$objectManager = Mage::getObjectManager();
$objectManager->removeSharedInstance('Mage_Checkout_Model_Session');
