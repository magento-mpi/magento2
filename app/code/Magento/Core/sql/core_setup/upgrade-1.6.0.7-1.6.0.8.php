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
 * Add column 'updated_at' to 'core_layout_update'
 */
$connection->addColumn(
    $installer->getTable('core_layout_update'),
    'updated_at',
    array('type' => \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, 'nullable' => true, 'comment' => 'Last Update Timestamp')
);

$installer->endSetup();
