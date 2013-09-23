<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* Create attribute */
/** @var $installer Magento_Catalog_Model_Resource_Setup */
$installer = Mage::getResourceModel('Magento_Catalog_Model_Resource_Setup', array('resourceName' => 'catalog_setup'));
/** @var $attribute Magento_Catalog_Model_Resource_Eav_Attribute */
$attribute = Mage::getResourceModel('Magento_Catalog_Model_Resource_Eav_Attribute');
$attribute->setData(
    array(
        'attribute_code'    => 'attribute_with_option',
        'entity_type_id'    => $installer->getEntityTypeId('catalog_product'),
        'is_global'         => 1,
        'frontend_input'    => 'select',
        'is_filterable'     => 1,
        'option' => array(
            'value' => array(
                'option_0' => array(0 => 'Option Label'),
            )
        ),
        'backend_type' => 'int',
    )
);
$attribute->save();

/* Assign attribute to attribute set */
$installer->addAttributeToGroup('catalog_product', 'Default', 'General', $attribute->getId());

/* Create simple products per each option */
/** @var $options Magento_Eav_Model_Resource_Entity_Attribute_Option_Collection */
$options = Mage::getResourceModel('Magento_Eav_Model_Resource_Entity_Attribute_Option_Collection');
$options->setAttributeFilter($attribute->getId());

foreach ($options as $option) {
    /** @var $product Magento_Catalog_Model_Product */
    $product = Mage::getModel('Magento_Catalog_Model_Product');
    $product->setTypeId(Magento_Catalog_Model_Product_Type::TYPE_SIMPLE)
        ->setAttributeSetId($installer->getAttributeSetId('catalog_product', 'Default'))
        ->setWebsiteIds(array(1))
        ->setName('Simple Product ' . $option->getId())
        ->setSku('simple_product_' . $option->getId())
        ->setPrice(10)
        ->setCategoryIds(array(2))
        ->setVisibility(Magento_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
        ->setStatus(Magento_Catalog_Model_Product_Status::STATUS_ENABLED)
        ->setStockData(
            array(
                'use_config_manage_stock'   => 1,
                'qty'                       => 5,
                'is_in_stock'               => 1,
            )
        )
        ->save();

    Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Catalog_Model_Product_Action')
        ->updateAttributes(
            array($product->getId()),
            array($attribute->getAttributeCode() => $option->getId()),
            $product->getStoreId()
        );
}
