<?php
/**
 * Upgrade script for integration table.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var Magento\Setup\Module\SetupModule $installer */
$installer = $this;
$installer->getConnection()->addColumn(
    $installer->getTable('integration'),
    'setup_type',
    array(
        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        'unsigned' => true,
        'nullable' => false,
        'default' => 0,
        'comment' => 'Integration type - manual or config file'
    )
);
