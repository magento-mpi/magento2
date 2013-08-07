<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer Mage_Sales_Model_Resource_Setup */
$installer = $this;
$installer->getConnection()
    ->addColumn($installer->getTable('sales_flat_shipment'), 'shipping_label', array(
        'type'    => Magento_DB_Ddl_Table::TYPE_VARBINARY,
        'comment' => 'Shipping Label Content',
        'length'  => '2m'
    ));
