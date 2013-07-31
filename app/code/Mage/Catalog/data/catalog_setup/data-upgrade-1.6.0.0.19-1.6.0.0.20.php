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
$applyTo = array_merge(
    explode(',', $this->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'weight', 'apply_to')),
    array(Mage_Catalog_Model_Product_Type::TYPE_VIRTUAL, Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE)
);

$this->updateAttribute(
    Mage_Catalog_Model_Product::ENTITY,
    'weight',
    'frontend_input_renderer',
    'Magento_Adminhtml_Block_Catalog_Product_Helper_Form_Weight_Renderer'
);
$this->updateAttribute(Mage_Catalog_Model_Product::ENTITY, 'weight', 'apply_to', implode(',', $applyTo));
