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
    'qty_returned',
    array(
        'type' => 'static',
        'label' => 'Qty of returned items',
        'input' => 'text',
        'visible' => false,
        'sort_order' => 45,
        'position' => 45
    )
);
