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

require __DIR__ . '/../../../Magento/Catalog/_files/product_configurable.php';
/** @var $attribute Magento_Catalog_Model_Resource_Eav_Attribute */

/** @var $product Magento_Catalog_Model_Product */
$product = Mage::getModel('Magento_Catalog_Model_Product');
$product->load(1);
/* Create simple products per each option */
/** @var $options Magento_Eav_Model_Resource_Entity_Attribute_Option_Collection */
$options = Mage::getResourceModel('Magento_Eav_Model_Resource_Entity_Attribute_Option_Collection');
$option = $options->setAttributeFilter($attribute->getId())->getFirstItem();

$requestInfo = new Magento_Object(array(
    'qty' => 1,
    'super_attribute' => array(
        $attribute->getId() => $option->getId()
    )
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
