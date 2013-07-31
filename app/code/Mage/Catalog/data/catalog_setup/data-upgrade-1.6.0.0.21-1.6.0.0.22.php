<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $this Mage_Catalog_Model_Resource_Setup */

$this->updateAttribute(
    $this->getEntityTypeId(Mage_Catalog_Model_Product::ENTITY),
    'weight',
    'backend_model',
    'Mage_Catalog_Model_Product_Attribute_Backend_Weight'
);
$this->updateAttribute(
    $this->getEntityTypeId(Mage_Catalog_Model_Product::ENTITY),
    'name',
    'frontend_class',
    'validate-length maximum-length-255'
);
$this->updateAttribute(
    $this->getEntityTypeId(Mage_Catalog_Model_Product::ENTITY),
    'sku',
    'frontend_class',
    'validate-length maximum-length-64'
);
$this->updateAttribute(
    $this->getEntityTypeId(Mage_Catalog_Model_Product::ENTITY),
    'qty',
    'frontend_class',
    'validate-number'
);
$this->updateAttribute(
    Mage_Catalog_Model_Product::ENTITY,
    'weight',
    'frontend_input_renderer',
    'Magento_Adminhtml_Block_Catalog_Product_Helper_Form_Weight'
);
