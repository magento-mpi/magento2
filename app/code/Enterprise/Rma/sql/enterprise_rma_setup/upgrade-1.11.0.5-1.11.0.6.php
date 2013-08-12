<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Enterprise_Rma_Model_Resource_Setup */
$installer = $this;

/**
 * Add new field to 'enterprise_rma_shipping_label'
 */
$installer->getConnection()
    ->addColumn(
        $installer->getTable('enterprise_rma_shipping_label'),
        'is_admin',
        array(
            'TYPE' => Magento_DB_Ddl_Table::TYPE_SMALLINT,
            'LENGTH' => 6,
            'COMMENT' => 'Is this Label Created by Merchant',
        )
    );

/**
 * Add new field 'protect_code' to RMA table
 */
$installer->getConnection()
    ->addColumn(
        $installer->getTable('enterprise_rma'),
        'protect_code',
        array(
            'TYPE' => Magento_DB_Ddl_Table::TYPE_TEXT,
            'LENGTH' => 255,
            'COMMENT' => 'Protect Code',
        )
    );
