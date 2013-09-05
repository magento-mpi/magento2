<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Magento_Rma_Model_Resource_Setup */
$installer = $this;

/**
 * Add new field to 'magento_rma_shipping_label'
 */
$installer->getConnection()
    ->addColumn(
        $installer->getTable('magento_rma_shipping_label'),
        'is_admin',
        array(
            'TYPE' => \Magento\DB\Ddl\Table::TYPE_SMALLINT,
            'LENGTH' => 6,
            'COMMENT' => 'Is this Label Created by Merchant',
        )
    );

/**
 * Add new field 'protect_code' to RMA table
 */
$installer->getConnection()
    ->addColumn(
        $installer->getTable('magento_rma'),
        'protect_code',
        array(
            'TYPE' => \Magento\DB\Ddl\Table::TYPE_TEXT,
            'LENGTH' => 255,
            'COMMENT' => 'Protect Code',
        )
    );
