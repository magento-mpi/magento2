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

require __DIR__ . '/../../Catalog/_files/product_configurable.php';
/** @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */

/** @var $product Mage_Catalog_Model_Product */
$product = Mage::getModel('Mage_Catalog_Model_Product');
$product->load(1);
/* Create simple products per each option */
/** @var $options Mage_Eav_Model_Resource_Entity_Attribute_Option_Collection */
$options = Mage::getResourceModel('Mage_Eav_Model_Resource_Entity_Attribute_Option_Collection');
$option = $options->setAttributeFilter($attribute->getId())->getFirstItem();

$requestInfo = new Magento_Object(array(
    'qty' => 1,
    'super_attribute' => array(
        $attribute->getId() => $option->getId()
    )
));

/** @var $cart Mage_Checkout_Model_Cart */
$cart = Mage::getModel('Mage_Checkout_Model_Cart');
$cart->addProduct($product, $requestInfo);
$cart->save();

Mage::unregister('_singleton/Mage_Checkout_Model_Session');

/** @var $objectManager Magento_Test_ObjectManager */
$objectManager = Magento_Test_Helper_Bootstrap::getObjectManager();
$objectManager->removeSharedInstance('Mage_Checkout_Model_Session');
