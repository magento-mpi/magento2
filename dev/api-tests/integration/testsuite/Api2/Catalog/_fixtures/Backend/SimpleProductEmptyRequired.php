<?php
/**
 * {license_notice}
 *
 * @category    Paas
 * @package     tests
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
/** @var $entityType Mage_Eav_Model_Entity_Type */
$entityType = Mage::getModel('Mage_Eav_Model_Entity_Type')->loadByCode('catalog_product');

return array(
    'type_id' => Mage_Catalog_Model_Product_Type::TYPE_SIMPLE,
    'attribute_set_id' => $entityType->getDefaultAttributeSetId(),
    'name' => '',
    'description' => '',
    'short_description' => '',
    'sku' => '',
    'weight' => '',
    'status' => '',
    'visibility' => '',
    'price' => '',
    'tax_class_id' => '',
    'stock_data' => array(
        'manage_stock' => 1,
        'qty' => ''
    )
);
