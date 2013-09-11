<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

$installer = $this;
$installer->addAttribute(\Magento\Catalog\Model\Product::ENTITY, 'country_of_manufacture', array(
    'group'             => 'General',
    'type'              => 'varchar',
    'backend'           => '',
    'frontend'          => '',
    'label'             => 'Country of Manufacture',
    'input'             => 'select',
    'class'             => '',
    'source'            => '\Magento\Catalog\Model\Product\Attribute\Source\Countryofmanufacture',
    'global'            => \Magento\Catalog\Model\Resource\Eav\Attribute::SCOPE_WEBSITE,
    'visible'           => true,
    'required'          => false,
    'user_defined'      => false,
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'visible_on_front'  => false,
    'unique'            => false,
    'apply_to'          => 'simple,configurable,bundle,grouped',
    'is_configurable'   => false,
));
