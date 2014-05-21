<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Rma\Model\Resource\Setup */
$installer = $this;

/* setting is_qty_decimal field in rma_item_entity table as a static attribute */
$installer->addAttribute(
    'rma_item',
    'is_qty_decimal',
    array(
        'type' => 'static',
        'label' => 'Is item quantity decimal',
        'input' => 'text',
        'visible' => false,
        'sort_order' => 15,
        'position' => 15
    )
);
