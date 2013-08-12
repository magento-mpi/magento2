<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_UnitPrice
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $this Magento_Eav_Model_Entity_Setup */
$this->startSetup();

$types = Mage::helper('Saas_UnitPrice_Helper_Data')->isUnitPriceProInstalledAndActive()
    ? 'simple,bundle,configurable'
    : 'simple';

$this->addAttribute('catalog_product', 'unit_price_use', array(
    'group'      => 'Prices',
    'type'       => 'int',
    'label'      => "Allow displaying the unit product's price",
    'input'      => 'select',
    'source'     => 'Magento_Eav_Model_Entity_Attribute_Source_Boolean',
    'global'     => Magento_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'required'   => false,
    'visible'    => true,
    'apply_to'   => $types,
    'default'    => 0,
));

$this->addAttribute('catalog_product', 'unit_price_unit', array(
    'group'      => 'Prices',
    'label'      => 'Measurement to be used for the base product',
    'input'      => 'select',
    'source'     => 'Saas_UnitPrice_Model_Entity_Source_Unitprice_Unit',
    'frontend'   => 'Saas_UnitPrice_Model_Entity_Frontend_Unitprice_Default',
    'backend'    => 'Saas_UnitPrice_Model_Entity_Backend_Unitprice_Unit',
    'global'     => Magento_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'required'   => false,
    'apply_to'   => $types,
    'default'    => 'KG',
));

$this->addAttribute('catalog_product', 'unit_price_amount', array(
    'group'      => 'Prices',
    'type'       => 'varchar',
    'label'      => 'Volume/size of one item of the base product',
    'input'      => 'text',
    'backend'    => 'Saas_UnitPrice_Model_Entity_Backend_Unitprice_Amount',
    'frontend'   => 'Saas_UnitPrice_Model_Entity_Frontend_Unitprice_Default',
    'global'     => Magento_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'required'   => false,
    'apply_to'   => $types,
));

$this->addAttribute('catalog_product', 'unit_price_base_unit', array(
    'group'      => 'Prices',
    'label'      => 'Measurement to be used for the unit product',
    'input'      => 'select',
    'frontend'   => 'Saas_UnitPrice_Model_Entity_Frontend_Unitprice_Default',
    'source'     => 'Saas_UnitPrice_Model_Entity_Source_Unitprice_Unit',
    'global'     => Magento_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'required'   => false,
    'apply_to'   => $types,
    'default'    => 'KG',
));

$this->addAttribute('catalog_product', 'unit_price_base_amount', array(
    'group'      => 'Prices',
    'type'       => 'varchar',
    'label'      => 'Volume/size of the unit product',
    'input'      => 'text',
    'backend'    => 'Saas_UnitPrice_Model_Entity_Backend_Unitprice_Amount',
    'frontend'   => 'Saas_UnitPrice_Model_Entity_Frontend_Unitprice_Default',
    'global'     => Magento_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'required'   => false,
    'apply_to'   => $types,
    'default'    => 1,
));

$attributeUpdates = array(
    'used_in_product_listing' => array(
        'unit_price_unit'         => 1,
        'unit_price_base_unit'    => 1,
        'unit_price_amount'       => 1,
        'unit_price_base_amount'  => 1,
        'unit_price_use'          => 1,
    ),
    'is_filterable_in_search' => array(
        'unit_price_unit'         => 0,
        'unit_price_base_unit'    => 0,
        'unit_price_amount'       => 1,
        'unit_price_base_amount'  => 1,
        'unit_price_use'          => 0,
    ),
    'apply_to' => array(
        'unit_price_use'          => 'simple,configurable',
        'unit_price_unit'         => 'simple,configurable',
        'unit_price_amount'       => 'simple,configurable',
        'unit_price_base_unit'    => 'simple,configurable',
        'unit_price_base_amount'  => 'simple,configurable',
    ),
    'frontend_input_renderer' => array(
        'unit_price_amount'       => 'Saas_UnitPrice_Block_Catalog_Product_Helper_Form_Unit',
        'unit_price_base_amount'  => 'Saas_UnitPrice_Block_Catalog_Product_Helper_Form_Unit',
    ),
);

foreach ($attributeUpdates as $column => $attributes) {
    foreach ($attributes as $name => $value) {
        $this->updateAttribute('catalog_product', $name, $column, $value);
    }
}

// An ugly hack, but setting the attribute_model per attribute
// isn't supported in Magento_Eav_Model_Entity_Setup::addAttribute()
$attributeCodeToModel = array(
    'unit_price_amount'      => 'Saas_UnitPrice_Model_Entity_Resource_Eav_Attribute_Product_Amount',
    'unit_price_unit'        => 'Saas_UnitPrice_Model_Entity_Resource_Eav_Attribute_Product_Unit',
    'unit_price_base_amount' => 'Saas_UnitPrice_Model_Entity_Resource_Eav_Attribute_Reference_Amount',
    'unit_price_base_unit'   => 'Saas_UnitPrice_Model_Entity_Resource_Eav_Attribute_Reference_Unit',
);

$adapter = $this->getConnection();
foreach ($attributeCodeToModel as $code => $model) {
    $where = array($adapter->quoteIdentifier('attribute_code') . ' = ?' => $code);
    $adapter->update($this->getTable('eav_attribute'), array('attribute_model' => $model), $where);
}

$this->endSetup();
