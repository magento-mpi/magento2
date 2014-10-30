<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

$installer = $this;
/** @var $installer \Magento\Framework\Module\Setup */

$installer->startSetup();

/**
 * Create table 'magento_reminder_rule'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('magento_reminder_rule'))
    ->addColumn(
        'rule_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
        'Rule Id'
    )
    ->addColumn(
        'name',
        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        255,
        ['nullable' => true, 'default' => null],
        'Name'
    )
    ->addColumn(
        'description',
        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        '64k',
        [],
        'Description'
    )
    ->addColumn(
        'conditions_serialized',
        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        '2M',
        ['nullable' => false],
        'Conditions Serialized'
    )
    ->addColumn(
        'condition_sql',
        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        '2M',
        [],
        'Condition Sql'
    )
    ->addColumn(
        'is_active',
        \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        ['unsigned' => true, 'nullable' => false, 'default' => '0'],
        'Is Active'
    )
    ->addColumn(
        'salesrule_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        ['unsigned' => true],
        'Salesrule Id'
    )
    ->addColumn(
        'schedule',
        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        255,
        [],
        'Schedule'
    )
    ->addColumn(
        'default_label',
        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        255,
        [],
        'Default Label'
    )
    ->addColumn(
        'default_description',
        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        '64k',
        [],
        'Default Description'
    )
    ->addColumn(
        'from_date',
        \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
        null,
        ['nullable' => true, 'default' => null],
        'Active From'
    )
    ->addColumn(
        'to_date',
        \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
        null,
        ['nullable' => true, 'default' => null],
        'Active To'
    )
    ->addIndex(
        $installer->getIdxName('magento_reminder_rule', ['salesrule_id']),
        ['salesrule_id']
    )
    ->addForeignKey(
        $installer->getFkName('magento_reminder_rule', 'salesrule_id', 'salesrule', 'rule_id'),
        'salesrule_id',
        $installer->getTable('salesrule'),
        'rule_id',
        \Magento\Framework\DB\Ddl\Table::ACTION_SET_NULL,
        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
    )
    ->setComment('Enterprise Reminder Rule');
$installer->getConnection()->createTable($table);

/**
 * Create table 'magento_reminder_rule_website'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('magento_reminder_rule_website'))
    ->addColumn(
        'rule_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        ['unsigned' => true, 'nullable' => false, 'primary' => true],
        'Rule Id'
    )
    ->addColumn(
        'website_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        ['unsigned' => true, 'nullable' => false, 'primary' => true],
        'Website Id'
    )
    ->addIndex(
        $installer->getIdxName('magento_reminder_rule_website', ['website_id']),
        ['website_id']
    )
    ->addForeignKey(
        $installer->getFkName('magento_reminder_rule_website', 'rule_id', 'magento_reminder_rule', 'rule_id'),
        'rule_id',
        $installer->getTable('magento_reminder_rule'),
        'rule_id',
        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName('magento_reminder_rule_website', 'website_id', 'store_website', 'website_id'),
        'website_id',
        $installer->getTable('store_website'),
        'website_id',
        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
    )
    ->setComment('Enterprise Reminder Rule Website');
$installer->getConnection()->createTable($table);

/**
 * Create table 'magento_reminder_template'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('magento_reminder_template'))
    ->addColumn(
        'rule_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        ['unsigned' => true, 'nullable' => false, 'primary' => true],
        'Rule Id'
    )
    ->addColumn(
        'store_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        ['nullable' => false, 'primary' => true],
        'Store Id'
    )
    ->addColumn(
        'template_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        ['unsigned' => true],
        'Template Id'
    )
    ->addColumn(
        'label',
        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        255,
        [],
        'Label'
    )
    ->addColumn(
        'description',
        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        '64k',
        [],
        'Description'
    )
    ->addIndex(
        $installer->getIdxName('magento_reminder_template', ['template_id']),
        ['template_id']
    )
    ->addForeignKey(
        $installer->getFkName('magento_reminder_template', 'template_id', 'email_template', 'template_id'),
        'template_id',
        $installer->getTable('email_template'),
        'template_id',
        \Magento\Framework\DB\Ddl\Table::ACTION_SET_NULL,
        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName('magento_reminder_template', 'rule_id', 'magento_reminder_rule', 'rule_id'),
        'rule_id',
        $installer->getTable('magento_reminder_rule'),
        'rule_id',
        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
    )
    ->setComment('Enterprise Reminder Template');
$installer->getConnection()->createTable($table);

/**
 * Create table 'magento_reminder_rule_coupon'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('magento_reminder_rule_coupon'))
    ->addColumn(
        'rule_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        ['unsigned' => true, 'nullable' => false, 'primary' => true],
        'Rule Id'
    )
    ->addColumn(
        'coupon_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        ['unsigned' => true],
        'Coupon Id'
    )
    ->addColumn(
        'customer_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        ['unsigned' => true, 'nullable' => false, 'primary' => true],
        'Customer Id'
    )
    ->addColumn(
        'associated_at',
        \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
        null,
        ['nullable' => false],
        'Associated At'
    )
    ->addColumn(
        'emails_failed',
        \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        ['unsigned' => true, 'nullable' => false, 'default' => '0'],
        'Emails Failed'
    )
    ->addColumn(
        'is_active',
        \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        ['unsigned' => true, 'nullable' => false, 'default' => '1'],
        'Is Active'
    )
    ->addForeignKey(
        $installer->getFkName('magento_reminder_rule_coupon', 'rule_id', 'magento_reminder_rule', 'rule_id'),
        'rule_id',
        $installer->getTable('magento_reminder_rule'),
        'rule_id',
        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
    )
    ->setComment('Enterprise Reminder Rule Coupon');
$installer->getConnection()->createTable($table);

/**
 * Create table 'magento_reminder_rule_log'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('magento_reminder_rule_log'))
    ->addColumn(
        'log_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
        'Log Id'
    )
    ->addColumn(
        'rule_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        ['unsigned' => true, 'nullable' => false],
        'Rule Id'
    )
    ->addColumn(
        'customer_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        ['unsigned' => true, 'nullable' => false],
        'Customer Id'
    )
    ->addColumn(
        'sent_at',
        \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
        null,
        ['nullable' => false],
        'Sent At'
    )
    ->addIndex(
        $installer->getIdxName('magento_reminder_rule_log', ['rule_id']),
        ['rule_id']
    )
    ->addIndex(
        $installer->getIdxName('magento_reminder_rule_log', ['customer_id']),
        ['customer_id']
    )
    ->addForeignKey(
        $installer->getFkName('magento_reminder_rule_log', 'rule_id', 'magento_reminder_rule', 'rule_id'),
        'rule_id',
        $installer->getTable('magento_reminder_rule'),
        'rule_id',
        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
    )
    ->setComment('Enterprise Reminder Rule Log');
$installer->getConnection()->createTable($table);

$installer->endSetup();
