<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Magento\Setup\Module\SetupModule */
$installer = $this;

$installer->getConnection()->addColumn(
    $installer->getTable('magento_scheduled_operations'),
    'entity_subtype',
    array(
        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        'length' => 50,
        'comment' => 'Sub Entity',
        'nullable' => true
    )
);
