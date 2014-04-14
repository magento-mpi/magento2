<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdminGws
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Module\Setup */
$installer = $this;

$installer->startSetup();

/**
 * Resource setup - add columns to roles table:
 * gws_is_all       - yes/no flag
 * gws_websites     - comma-separated
 * gws_store_groups - comma-separated
 */
$tableRoles = $installer->getTable('admin_role');
$columns = array(
    'gws_is_all' => array(
        'type' => \Magento\DB\Ddl\Table::TYPE_INTEGER,
        'length' => '1',
        'nullable' => false,
        'default' => '1',
        'comment' => 'Yes/No Flag'
    ),
    'gws_websites' => array(
        'type' => \Magento\DB\Ddl\Table::TYPE_TEXT,
        'length' => '255',
        'comment' => 'Comma-separated Website Ids'
    ),
    'gws_store_groups' => array(
        'type' => \Magento\DB\Ddl\Table::TYPE_TEXT,
        'length' => '255',
        'comment' => 'Comma-separated Store Groups Ids'
    )
);

$connection = $installer->getConnection();
foreach ($columns as $name => $definition) {
    $connection->addColumn($tableRoles, $name, $definition);
}

$installer->endSetup();
