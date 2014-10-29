<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer \Magento\Setup\Module\SetupModule */
$installer = $this;
$installer->startSetup();

/**
 * Create table 'magento_banner'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('magento_banner')
)->addColumn(
    'banner_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
    'Banner Id'
)->addColumn(
    'name',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    array(),
    'Name'
)->addColumn(
    'is_enabled',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('nullable' => false),
    'Is Enabled'
)->addColumn(
    'types',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    array(),
    'Types'
)->setComment(
    'Enterprise Banner'
);
$installer->getConnection()->createTable($table);

/**
 * Create table 'magento_banner_content'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('magento_banner_content')
)->addColumn(
    'banner_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true, 'nullable' => false, 'primary' => true, 'default' => '0'),
    'Banner Id'
)->addColumn(
    'store_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false, 'primary' => true, 'default' => '0'),
    'Store Id'
)->addColumn(
    'banner_content',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    '2M',
    array(),
    'Banner Content'
)->addIndex(
    $installer->getIdxName('magento_banner_content', array('store_id')),
    array('store_id')
)->addForeignKey(
    $installer->getFkName('magento_banner_content', 'banner_id', 'magento_banner', 'banner_id'),
    'banner_id',
    $installer->getTable('magento_banner'),
    'banner_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $installer->getFkName('magento_banner_content', 'store_id', 'store', 'store_id'),
    'store_id',
    $installer->getTable('store'),
    'store_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'Enterprise Banner Content'
);
$installer->getConnection()->createTable($table);

/**
 * Create table 'magento_banner_catalogrule'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('magento_banner_catalogrule')
)->addColumn(
    'banner_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true, 'nullable' => false, 'primary' => true),
    'Banner Id'
)->addColumn(
    'rule_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true, 'nullable' => false, 'primary' => true),
    'Rule Id'
)->addIndex(
    $installer->getIdxName('magento_banner_catalogrule', array('rule_id')),
    array('rule_id')
)->addForeignKey(
    $installer->getFkName('magento_banner_catalogrule', 'banner_id', 'magento_banner', 'banner_id'),
    'banner_id',
    $installer->getTable('magento_banner'),
    'banner_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $installer->getFkName('magento_banner_catalogrule', 'rule_id', 'catalogrule', 'rule_id'),
    'rule_id',
    $installer->getTable('catalogrule'),
    'rule_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'Enterprise Banner Catalogrule'
);
$installer->getConnection()->createTable($table);

/**
 * Create table 'magento_banner_salesrule'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('magento_banner_salesrule')
)->addColumn(
    'banner_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true, 'nullable' => false, 'primary' => true),
    'Banner Id'
)->addColumn(
    'rule_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true, 'nullable' => false, 'primary' => true),
    'Rule Id'
)->addIndex(
    $installer->getIdxName('magento_banner_salesrule', array('rule_id')),
    array('rule_id')
)->addForeignKey(
    $installer->getFkName('magento_banner_salesrule', 'banner_id', 'magento_banner', 'banner_id'),
    'banner_id',
    $installer->getTable('magento_banner'),
    'banner_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $installer->getFkName('magento_banner_salesrule', 'rule_id', 'salesrule', 'rule_id'),
    'rule_id',
    $installer->getTable('salesrule'),
    'rule_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'Enterprise Banner Salesrule'
);
$installer->getConnection()->createTable($table);

$installer->endSetup();
