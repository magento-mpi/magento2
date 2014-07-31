<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/** @var $installer \Magento\Tax\Model\Resource\Setup */
$installer = $this;

/**
 * Add tax_class_id attribute to the 'eav_attribute' table
 */
$catalogInstaller = $installer->getCatalogResourceSetup(array('resourceName' => 'catalog_setup'));
$catalogInstaller->addAttribute(
    \Magento\Catalog\Model\Product::ENTITY,
    'tax_class_id',
    array(
        'group' => 'Prices',
        'type' => 'int',
        'backend' => '',
        'frontend' => '',
        'label' => 'Tax Class',
        'input' => 'select',
        'class' => '',
        'source' => 'Magento\Tax\Model\TaxClass\Source\Product',
        'global' => \Magento\Catalog\Model\Resource\Eav\Attribute::SCOPE_WEBSITE,
        'visible' => true,
        'required' => true,
        'user_defined' => false,
        'default' => '',
        'searchable' => true,
        'filterable' => false,
        'comparable' => false,
        'visible_on_front' => false,
        'visible_in_advanced_search' => true,
        'used_in_product_listing' => true,
        'unique' => false,
        'apply_to' => implode($this->getTaxableItems(), ',')
    )
);

/**
 * install tax classes
 */
$data = array(
    array(
        'class_id' => 2,
        'class_name' => 'Taxable Goods',
        'class_type' => \Magento\Tax\Model\ClassModel::TAX_CLASS_TYPE_PRODUCT
    ),
    array(
        'class_id' => 3,
        'class_name' => 'Retail Customer',
        'class_type' => \Magento\Tax\Model\ClassModel::TAX_CLASS_TYPE_CUSTOMER
    )
);
foreach ($data as $row) {
    $installer->getConnection()->insertForce($installer->getTable('tax_class'), $row);
}

/**
 * install tax calculation rates
 */
$data = array(
    array(
        'tax_calculation_rate_id' => 1,
        'tax_country_id' => 'US',
        'tax_region_id' => 12,
        'tax_postcode' => '*',
        'code' => 'US-CA-*-Rate 1',
        'rate' => '8.2500'
    ),
    array(
        'tax_calculation_rate_id' => 2,
        'tax_country_id' => 'US',
        'tax_region_id' => 43,
        'tax_postcode' => '*',
        'code' => 'US-NY-*-Rate 1',
        'rate' => '8.3750'
    )
);
foreach ($data as $row) {
    $installer->getConnection()->insertForce($installer->getTable('tax_calculation_rate'), $row);
}
