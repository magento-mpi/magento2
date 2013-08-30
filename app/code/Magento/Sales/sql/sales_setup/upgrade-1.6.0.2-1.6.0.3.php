<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer Magento_Sales_Model_Resource_Setup */
$installer = $this;
$installer->getConnection()
    ->addColumn($installer->getTable('sales_flat_shipment'), 'shipping_label', array(
        'type'    => Magento_DB_Ddl_Table::TYPE_VARBINARY,
        'comment' => 'Shipping Label Content',
        'length'  => '2m'
    ));
