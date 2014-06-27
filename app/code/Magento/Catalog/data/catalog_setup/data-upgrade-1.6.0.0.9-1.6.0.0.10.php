<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Catalog\Model\Resource\Setup */
$installer = $this;

$installer->addAttribute(
    'catalog_product',
    'group_price',
    array(
        'type' => 'decimal',
        'label' => 'Group Price',
        'input' => 'text',
        'backend' => 'Magento\Catalog\Model\Product\Attribute\Backend\Groupprice',
        'required' => false,
        'sort_order' => 6,
        'global' => \Magento\Catalog\Model\Resource\Eav\Attribute::SCOPE_WEBSITE,
        'apply_to' => 'simple,virtual',
        'group' => 'Prices'
    )
);
