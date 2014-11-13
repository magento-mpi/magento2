<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Catalog\Model\Resource\Setup */
$installer = $this;

$attributes = array(
    'country_of_manufacture',
    'group_price',
    'minimal_price',
    'price',
    'special_price',
    'special_from_date',
    'special_to_date',
    'tier_price',
    'weight'
);
foreach ($attributes as $attributeCode) {
    $relatedProductTypes = explode(
        ',',
        $installer->getAttribute(\Magento\Catalog\Model\Product::ENTITY, $attributeCode, 'apply_to')
    );
    if (!in_array(\Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE, $relatedProductTypes)) {
        $relatedProductTypes[] = \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE;
        $installer->updateAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            $attributeCode,
            'apply_to',
            implode(',', $relatedProductTypes)
        );
    }
}
