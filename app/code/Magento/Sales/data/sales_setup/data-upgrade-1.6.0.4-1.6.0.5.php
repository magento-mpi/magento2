<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Sales\Model\Resource\Setup */
$installer = $this;

$subSelect = $installer->getConnection()->select()
    ->from(
        array('citem' => $installer->getTable('sales_flat_creditmemo_item')),
        array(
             'amount_refunded'        => 'SUM(citem.row_total)',
             'base_amount_refunded'   => 'SUM(citem.base_row_total)',
             'base_tax_refunded'      => 'SUM(citem.base_tax_amount)',
             'discount_refunded'      => 'SUM(citem.discount_amount)',
             'base_discount_refunded' => 'SUM(citem.base_discount_amount)',
        )
    )
    ->joinLeft(
        array('c' => $installer->getTable('sales_flat_creditmemo')),
        'c.entity_id = citem.parent_id',
        array()
    )
    ->joinLeft(
        array('o' => $installer->getTable('sales_flat_order')),
        'o.entity_id = c.order_id',
        array()
    )
    ->joinLeft(
        array('oitem' => $installer->getTable('sales_flat_order_item')),
        'oitem.order_id = o.entity_id AND oitem.product_id=citem.product_id',
        array('item_id')
    )
    ->group('oitem.item_id');

$select = $installer->getConnection()->select()
    ->from(
        array('selected' => $subSelect),
        array(
            'amount_refunded'        => 'amount_refunded',
            'base_amount_refunded'   => 'base_amount_refunded',
            'base_tax_refunded'      => 'base_tax_refunded',
            'discount_refunded'      => 'discount_refunded',
            'base_discount_refunded' => 'base_discount_refunded',
        )
    )
    ->where('main.item_id = selected.item_id');

$updateQuery = $installer->getConnection()->updateFromSelect(
    $select,
    array('main' => $installer->getTable('sales_flat_order_item'))
);

$installer->getConnection()->query($updateQuery);
