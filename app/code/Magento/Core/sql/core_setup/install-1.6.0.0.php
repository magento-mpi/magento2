<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/* @var $installer \Magento\Module\Setup */
$installer = $this;

$installer->startSetup();

/**
 * Create table 'core_resource'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('core_resource')
)->addColumn(
    'code',
    \Magento\DB\Ddl\Table::TYPE_TEXT,
    50,
    array('nullable' => false, 'primary' => true),
    'Resource Code'
)->addColumn(
    'version',
    \Magento\DB\Ddl\Table::TYPE_TEXT,
    50,
    array(),
    'Resource Version'
)->addColumn(
    'data_version',
    \Magento\DB\Ddl\Table::TYPE_TEXT,
    50,
    array(),
    'Data Version'
)->setComment(
    'Resources'
);
$installer->getConnection()->createTable($table);

/**
 * Create table 'core_config_data'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('core_config_data')
)->addColumn(
    'config_id',
    \Magento\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
    'Config Id'
)->addColumn(
    'scope',
    \Magento\DB\Ddl\Table::TYPE_TEXT,
    8,
    array('nullable' => false, 'default' => 'default'),
    'Config Scope'
)->addColumn(
    'scope_id',
    \Magento\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('nullable' => false, 'default' => '0'),
    'Config Scope Id'
)->addColumn(
    'path',
    \Magento\DB\Ddl\Table::TYPE_TEXT,
    255,
    array('nullable' => false, 'default' => 'general'),
    'Config Path'
)->addColumn(
    'value',
    \Magento\DB\Ddl\Table::TYPE_TEXT,
    '64k',
    array(),
    'Config Value'
)->addIndex(
    $installer->getIdxName(
        'core_config_data',
        array('scope', 'scope_id', 'path'),
        \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
    ),
    array('scope', 'scope_id', 'path'),
    array('type' => \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE)
)->setComment(
    'Config Data'
);
$installer->getConnection()->createTable($table);

/**
 * Create table 'core_layout_update'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('core_layout_update')
)->addColumn(
    'layout_update_id',
    \Magento\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
    'Layout Update Id'
)->addColumn(
    'handle',
    \Magento\DB\Ddl\Table::TYPE_TEXT,
    255,
    array(),
    'Handle'
)->addColumn(
    'xml',
    \Magento\DB\Ddl\Table::TYPE_TEXT,
    '64k',
    array(),
    'Xml'
)->addColumn(
    'sort_order',
    \Magento\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('nullable' => false, 'default' => '0'),
    'Sort Order'
)->addIndex(
    $installer->getIdxName('core_layout_update', array('handle')),
    array('handle')
)->setComment(
    'Layout Updates'
);
$installer->getConnection()->createTable($table);

/**
 * Create table 'core_layout_link'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('core_layout_link')
)->addColumn(
    'layout_link_id',
    \Magento\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
    'Link Id'
)->addColumn(
    'store_id',
    \Magento\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
    'Store Id'
)->addColumn(
    'area',
    \Magento\DB\Ddl\Table::TYPE_TEXT,
    64,
    array(),
    'Area'
)->addColumn(
    'package',
    \Magento\DB\Ddl\Table::TYPE_TEXT,
    64,
    array(),
    'Package'
)->addColumn(
    'theme',
    \Magento\DB\Ddl\Table::TYPE_TEXT,
    64,
    array(),
    'Theme'
)->addColumn(
    'layout_update_id',
    \Magento\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
    'Layout Update Id'
)->addIndex(
    $installer->getIdxName(
        'core_layout_link',
        array('store_id', 'package', 'theme', 'layout_update_id'),
        \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
    ),
    array('store_id', 'package', 'theme', 'layout_update_id'),
    array('type' => \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE)
)->addIndex(
    $installer->getIdxName('core_layout_link', array('layout_update_id')),
    array('layout_update_id')
)->addForeignKey(
    $installer->getFkName('core_layout_link', 'store_id', 'store', 'store_id'),
    'store_id',
    $installer->getTable('store'),
    'store_id',
    \Magento\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $installer->getFkName('core_layout_link', 'layout_update_id', 'core_layout_update', 'layout_update_id'),
    'layout_update_id',
    $installer->getTable('core_layout_update'),
    'layout_update_id',
    \Magento\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'Layout Link'
);
$installer->getConnection()->createTable($table);

/**
 * Create table 'session'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('core_session')
)->addColumn(
    'session_id',
    \Magento\DB\Ddl\Table::TYPE_TEXT,
    255,
    array('nullable' => false, 'primary' => true),
    'Session Id'
)->addColumn(
    'session_expires',
    \Magento\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
    'Date of Session Expiration'
)->addColumn(
    'session_data',
    \Magento\DB\Ddl\Table::TYPE_BLOB,
    '2M',
    array('nullable' => false),
    'Session Data'
)->setComment(
    'Database Sessions Storage'
);
$installer->getConnection()->createTable($table);

/**
 * Create table 'core_translate'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('core_translate')
)->addColumn(
    'key_id',
    \Magento\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
    'Key Id of Translation'
)->addColumn(
    'string',
    \Magento\DB\Ddl\Table::TYPE_TEXT,
    255,
    array('nullable' => false, 'default' => \Magento\TranslateInterface::DEFAULT_STRING),
    'Translation String'
)->addColumn(
    'store_id',
    \Magento\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
    'Store Id'
)->addColumn(
    'translate',
    \Magento\DB\Ddl\Table::TYPE_TEXT,
    255,
    array(),
    'Translate'
)->addColumn(
    'locale',
    \Magento\DB\Ddl\Table::TYPE_TEXT,
    20,
    array('nullable' => false, 'default' => 'en_US'),
    'Locale'
)->addIndex(
    $installer->getIdxName(
        'core_translate',
        array('store_id', 'locale', 'string'),
        \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
    ),
    array('store_id', 'locale', 'string'),
    array('type' => \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE)
)->addIndex(
    $installer->getIdxName('core_translate', array('store_id')),
    array('store_id')
)->addForeignKey(
    $installer->getFkName('core_translate', 'store_id', 'store', 'store_id'),
    'store_id',
    $installer->getTable('store'),
    'store_id',
    \Magento\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'Translations'
);
$installer->getConnection()->createTable($table);

/**
 * Create table 'design_change'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('design_change')
)->addColumn(
    'design_change_id',
    \Magento\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('identity' => true, 'nullable' => false, 'primary' => true),
    'Design Change Id'
)->addColumn(
    'store_id',
    \Magento\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
    'Store Id'
)->addColumn(
    'design',
    \Magento\DB\Ddl\Table::TYPE_TEXT,
    255,
    array(),
    'Design'
)->addColumn(
    'date_from',
    \Magento\DB\Ddl\Table::TYPE_DATE,
    null,
    array(),
    'First Date of Design Activity'
)->addColumn(
    'date_to',
    \Magento\DB\Ddl\Table::TYPE_DATE,
    null,
    array(),
    'Last Date of Design Activity'
)->addIndex(
    $installer->getIdxName('design_change', array('store_id')),
    array('store_id')
)->addForeignKey(
    $installer->getFkName('design_change', 'store_id', 'store', 'store_id'),
    'store_id',
    $installer->getTable('store'),
    'store_id',
    \Magento\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'Design Changes'
);
$installer->getConnection()->createTable($table);

/**
 * Create table 'core_variable'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('core_variable')
)->addColumn(
    'variable_id',
    \Magento\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
    'Variable Id'
)->addColumn(
    'code',
    \Magento\DB\Ddl\Table::TYPE_TEXT,
    255,
    array(),
    'Variable Code'
)->addColumn(
    'name',
    \Magento\DB\Ddl\Table::TYPE_TEXT,
    255,
    array(),
    'Variable Name'
)->addIndex(
    $installer->getIdxName('core_variable', array('code'), \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE),
    array('code'),
    array('type' => \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE)
)->setComment(
    'Variables'
);
$installer->getConnection()->createTable($table);

/**
 * Create table 'core_variable_value'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('core_variable_value')
)->addColumn(
    'value_id',
    \Magento\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
    'Variable Value Id'
)->addColumn(
    'variable_id',
    \Magento\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
    'Variable Id'
)->addColumn(
    'store_id',
    \Magento\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
    'Store Id'
)->addColumn(
    'plain_value',
    \Magento\DB\Ddl\Table::TYPE_TEXT,
    '64k',
    array(),
    'Plain Text Value'
)->addColumn(
    'html_value',
    \Magento\DB\Ddl\Table::TYPE_TEXT,
    '64k',
    array(),
    'Html Value'
)->addIndex(
    $installer->getIdxName(
        'core_variable_value',
        array('variable_id', 'store_id'),
        \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
    ),
    array('variable_id', 'store_id'),
    array('type' => \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE)
)->addIndex(
    $installer->getIdxName('core_variable_value', array('variable_id')),
    array('variable_id')
)->addIndex(
    $installer->getIdxName('core_variable_value', array('store_id')),
    array('store_id')
)->addForeignKey(
    $installer->getFkName('core_variable_value', 'store_id', 'store', 'store_id'),
    'store_id',
    $installer->getTable('store'),
    'store_id',
    \Magento\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $installer->getFkName('core_variable_value', 'variable_id', 'core_variable', 'variable_id'),
    'variable_id',
    $installer->getTable('core_variable'),
    'variable_id',
    \Magento\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'Variable Value'
);
$installer->getConnection()->createTable($table);

/**
 * Create table 'core_cache'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('core_cache')
)->addColumn(
    'id',
    \Magento\DB\Ddl\Table::TYPE_TEXT,
    200,
    array('nullable' => false, 'primary' => true),
    'Cache Id'
)->addColumn(
    'data',
    \Magento\DB\Ddl\Table::TYPE_BLOB,
    '2M',
    array(),
    'Cache Data'
)->addColumn(
    'create_time',
    \Magento\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array(),
    'Cache Creation Time'
)->addColumn(
    'update_time',
    \Magento\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array(),
    'Time of Cache Updating'
)->addColumn(
    'expire_time',
    \Magento\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array(),
    'Cache Expiration Time'
)->addIndex(
    $installer->getIdxName('core_cache', array('expire_time')),
    array('expire_time')
)->setComment(
    'Caches'
);
$installer->getConnection()->createTable($table);

/**
 * Create table 'core_cache_tag'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('core_cache_tag')
)->addColumn(
    'tag',
    \Magento\DB\Ddl\Table::TYPE_TEXT,
    100,
    array('nullable' => false, 'primary' => true),
    'Tag'
)->addColumn(
    'cache_id',
    \Magento\DB\Ddl\Table::TYPE_TEXT,
    200,
    array('nullable' => false, 'primary' => true),
    'Cache Id'
)->addIndex(
    $installer->getIdxName('core_cache_tag', array('cache_id')),
    array('cache_id')
)->setComment(
    'Tag Caches'
);
$installer->getConnection()->createTable($table);

/**
 * Create table 'core_cache_option'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('core_cache_option')
)->addColumn(
    'code',
    \Magento\DB\Ddl\Table::TYPE_TEXT,
    32,
    array('nullable' => false, 'primary' => true),
    'Code'
)->addColumn(
    'value',
    \Magento\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array(),
    'Value'
)->setComment(
    'Cache Options'
);
$installer->getConnection()->createTable($table);

/**
 * Create table 'core_flag'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('core_flag')
)->addColumn(
    'flag_id',
    \Magento\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
    'Flag Id'
)->addColumn(
    'flag_code',
    \Magento\DB\Ddl\Table::TYPE_TEXT,
    255,
    array('nullable' => false),
    'Flag Code'
)->addColumn(
    'state',
    \Magento\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
    'Flag State'
)->addColumn(
    'flag_data',
    \Magento\DB\Ddl\Table::TYPE_TEXT,
    '64k',
    array(),
    'Flag Data'
)->addColumn(
    'last_update',
    \Magento\DB\Ddl\Table::TYPE_TIMESTAMP,
    null,
    array('nullable' => false, 'default' => \Magento\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE),
    'Date of Last Flag Update'
)->addIndex(
    $installer->getIdxName('core_flag', array('last_update')),
    array('last_update')
)->setComment(
    'Flag'
);
$installer->getConnection()->createTable($table);

$installer->endSetup();
