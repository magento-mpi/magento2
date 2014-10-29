<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Setup\Module\SetupModule */
$installer = $this;

$installer->getConnection()
    ->addColumn(
        $installer->getTable('paypal_settlement_report_row'),
        'store_id',
        array(
            'type'    => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'comment' => 'Store ID',
            'length'  => '50'
        )
    );
