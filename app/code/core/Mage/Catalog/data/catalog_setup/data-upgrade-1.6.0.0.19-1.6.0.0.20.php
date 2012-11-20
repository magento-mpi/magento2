<?php
$applyTo = array_merge(
    explode(',', $this->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'weight', 'apply_to')),
    array('virtual', 'downloadable', 'configurable')
);

$this->updateAttribute(
    Mage_Catalog_Model_Product::ENTITY,
    'weight',
    'frontend_input_renderer',
    'Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Weight_Renderer'
);
$this->updateAttribute(Mage_Catalog_Model_Product::ENTITY, 'weight', 'apply_to', implode(',', $applyTo));
