<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_User
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

// Add column "is_test"
$installer->getConnection()->addColumn($installer->getTable('sales_flat_order'), 'is_test', array(
    'type' => Varien_Db_Ddl_Table::TYPE_SMALLINT,
    'length' => 1,
    'unsigned'  => true,
    'nullable' => false,
    'default' => 0,
    'comment' => 'Flag for test orders'
));

$installer->endSetup();
