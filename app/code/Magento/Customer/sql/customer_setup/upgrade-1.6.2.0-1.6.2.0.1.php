<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Magento_Customer_Model_Entity_Setup */
$installer = $this;

$installer->getConnection()->addColumn($installer->getTable('customer_entity'), 'disable_auto_group_change', array(
    'type' => Magento_DB_Ddl_Table::TYPE_SMALLINT,
    'unsigned' => true,
    'nullable' => false,
    'default' => '0',
    'comment' => 'Disable automatic group change based on VAT ID'
));
