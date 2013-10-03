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
$installer->startSetup();
$installer->getConnection()
    ->addColumn($installer->getTable('sales_flat_shipment'), 'packages', array(
        'type'    => \Magento\DB\Ddl\Table::TYPE_TEXT,
        'comment' => 'Packed Products in Packages',
        'length'  => '20000'
    ));
$installer->endSetup();
