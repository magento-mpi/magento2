<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */

$installer = $this;
/** @var $installer Mage_Tax_Model_Resource_Setup */

$installer->startSetup();

/**
 * Chnage field to 'tax/sales_order_tax_item'
 */

$installer->getConnection()
    ->modifyColumn(
        $installer->getTable('tax_calculation_rate'),
        'zip_from',
        array(
            'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
            'nullable'  => true,
            'length'    => 255,
            'comment'   => 'Zip From',
        )
    )
    ->modifyColumn(
        $installer->getTable('tax_calculation_rate'),
        'zip_to',
        array(
            'TYPE'      => Varien_Db_Ddl_Table::TYPE_TEXT,
            'NULLABLE'  => true,
            'LENGTH'    => 255,
            'COMMENT'   => 'Zip To',
        )
   );

$installer->endSetup();
