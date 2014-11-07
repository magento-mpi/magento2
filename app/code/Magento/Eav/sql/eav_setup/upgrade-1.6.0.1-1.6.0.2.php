<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Setup\Module\SetupModule */
$installer = $this;

$installer->startSetup();

$connection = $installer->getConnection();
$tableName = $installer->getTable('eav_attribute_group');

$connection->addColumn(
    $tableName,
    'attribute_group_code',
    array('type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 'length' => '255', 'comment' => 'Attribute Group Code')
);

$connection->addColumn(
    $tableName,
    'tab_group_code',
    array('type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 'length' => '255', 'comment' => 'Tab Group Code')
);

$installer->endSetup();
