<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $this Magento_Catalog_Model_Resource_Setup */
$applyTo = array_merge(
    explode(',', $this->getAttribute(Magento_Catalog_Model_Product::ENTITY, 'weight', 'apply_to')),
    array(Magento_Catalog_Model_Product_Type::TYPE_VIRTUAL, Magento_Catalog_Model_Product_Type::TYPE_CONFIGURABLE)
);

$this->updateAttribute(
    Magento_Catalog_Model_Product::ENTITY,
    'weight',
    'frontend_input_renderer',
    'Magento_Adminhtml_Block_Catalog_Product_Helper_Form_Weight_Renderer'
);
$this->updateAttribute(Magento_Catalog_Model_Product::ENTITY, 'weight', 'apply_to', implode(',', $applyTo));
