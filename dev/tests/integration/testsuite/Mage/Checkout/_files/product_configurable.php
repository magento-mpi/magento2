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
$options = new Mage_Eav_Model_Resource_Entity_Attribute_Option_Collection();
$option = $options->setAttributeFilter($attribute->getId())->getFirstItem();

$requestInfo = new Varien_Object(array(
    'qty' => 1,
    'super_attribute' => array(
        $attribute->getId() => $option->getId()
    )
));

require __DIR__ . '/../../Checkout/_files/cart.php';
