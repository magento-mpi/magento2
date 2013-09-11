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
    ->addColumn($installer->getTable('sales_flat_order_status_history'), 'entity_name', array(
        'type'      => \Magento\DB\Ddl\Table::TYPE_TEXT,
        'length'    => 32,
        'nullable'  => true,
        'comment'   => 'Shows what entity history is bind to.'
    ));
