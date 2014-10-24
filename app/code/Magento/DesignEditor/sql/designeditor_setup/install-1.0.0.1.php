<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

/* @var $installer Magento\Setup\Module\SetupModule */
$installer = $this;

$installer->startSetup();
$connection = $installer->getConnection();
/**
 * Modifying 'core_layout_update' table. Adding 'is_vde' column
 */
$tableCoreLayoutLink = $installer->getTable('core_layout_update');

$connection->addColumn(
    $tableCoreLayoutLink,
    'is_vde',
    array(
        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
        'nullable' => false,
        'default' => '0',
        'comment' => 'Defines whether layout update created via design editor'
    )
);

$installer->endSetup();
