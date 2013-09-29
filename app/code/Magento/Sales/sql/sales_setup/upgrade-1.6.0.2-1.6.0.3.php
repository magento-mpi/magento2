<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer \Magento\Sales\Model\Resource\Setup */
$installer = $this;
$installer->getConnection()
    ->addColumn($installer->getTable('sales_flat_shipment'), 'shipping_label', array(
        'type'    => \Magento\DB\Ddl\Table::TYPE_VARBINARY,
        'comment' => 'Shipping Label Content',
        'length'  => '2m'
    ));
