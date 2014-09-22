<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Rma\Model\Resource\Setup */
$installer = $this;

$installer->addAttribute(
    'rma_item',
    'product_admin_name',
    array(
        'type' => 'static',
        'label' => 'Product Name For Backend',
        'input' => 'text',
        'visible' => false,
        'sort_order' => 46,
        'position' => 46
    )
);
$installer->addAttribute(
    'rma_item',
    'product_admin_sku',
    array(
        'type' => 'static',
        'label' => 'Product Sku For Backend',
        'input' => 'text',
        'visible' => false,
        'sort_order' => 47,
        'position' => 47
    )
);
$installer->addAttribute(
    'rma_item',
    'product_options',
    array(
        'type' => 'static',
        'label' => 'Product Options',
        'input' => 'text',
        'visible' => false,
        'sort_order' => 48,
        'position' => 48
    )
);
