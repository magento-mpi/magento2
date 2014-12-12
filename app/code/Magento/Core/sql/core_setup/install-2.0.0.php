<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer \Magento\Setup\Module\SetupModule */
$installer = $this;

/* @var $connection \Magento\Framework\DB\Adapter\AdapterInterface */
$connection = $installer->getConnection();

$installer->startSetup();

/**
 * Create table 'core_resource'
 */
$table = $connection->newTable(
    $installer->getTable('core_resource')
)->addColumn(
    'code',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    50,
    array('nullable' => false, 'primary' => true),
    'Resource Code'
)->addColumn(
    'version',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    50,
    array(),
    'Resource Version'
)->addColumn(
    'data_version',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    50,
    array(),
    'Data Version'
)->setComment(
    'Resources'
);
$connection->createTable($table);

/**
 * Create table 'core_config_data'
 */
$table = $connection->newTable(
    $installer->getTable('core_config_data')
)->addColumn(
    'config_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
    'Config Id'
)->addColumn(
    'scope',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    8,
    array('nullable' => false, 'default' => 'default'),
    'Config Scope'
)->addColumn(
    'scope_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('nullable' => false, 'default' => '0'),
    'Config Scope Id'
)->addColumn(
    'path',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    array('nullable' => false, 'default' => 'general'),
    'Config Path'
)->addColumn(
    'value',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    '64k',
    array(),
    'Config Value'
)->addIndex(
    $installer->getIdxName(
        'core_config_data',
        array('scope', 'scope_id', 'path'),
        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
    ),
    array('scope', 'scope_id', 'path'),
    array('type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE)
)->setComment(
    'Config Data'
);
$connection->createTable($table);

/**
 * Create table 'core_layout_update'
 */
$table = $connection->newTable(
    $installer->getTable('core_layout_update')
)->addColumn(
    'layout_update_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
    'Layout Update Id'
)->addColumn(
    'handle',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    array(),
    'Handle'
)->addColumn(
    'xml',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    '64k',
    array(),
    'Xml'
)->addColumn(
    'sort_order',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('nullable' => false, 'default' => '0'),
    'Sort Order'
)->addIndex(
    $installer->getIdxName('core_layout_update', array('handle')),
    array('handle')
)->setComment(
    'Layout Updates'
);
$connection->createTable($table);

/**
 * Create table 'core_layout_link'
 */
$table = $connection->newTable(
    $installer->getTable('core_layout_link')
)->addColumn(
    'layout_link_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
    'Link Id'
)->addColumn(
    'store_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
    'Store Id'
)->addColumn(
    'area',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    64,
    array(),
    'Area'
)->addColumn(
    'package',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    64,
    array(),
    'Package'
)->addColumn(
    'theme',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    64,
    array(),
    'Theme'
)->addColumn(
    'layout_update_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
    'Layout Update Id'
)->addIndex(
    $installer->getIdxName(
        'core_layout_link',
        array('store_id', 'package', 'theme', 'layout_update_id'),
        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
    ),
    array('store_id', 'package', 'theme', 'layout_update_id'),
    array('type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE)
)->addIndex(
    $installer->getIdxName('core_layout_link', array('layout_update_id')),
    array('layout_update_id')
)->addForeignKey(
    $installer->getFkName('core_layout_link', 'store_id', 'store', 'store_id'),
    'store_id',
    $installer->getTable('store'),
    'store_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $installer->getFkName('core_layout_link', 'layout_update_id', 'core_layout_update', 'layout_update_id'),
    'layout_update_id',
    $installer->getTable('core_layout_update'),
    'layout_update_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'Layout Link'
);
$connection->createTable($table);

/**
 * Create table 'core_session'
 */
$table = $connection->newTable(
    $installer->getTable('core_session')
)->addColumn(
    'session_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    array('nullable' => false, 'primary' => true),
    'Session Id'
)->addColumn(
    'session_expires',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
    'Date of Session Expiration'
)->addColumn(
    'session_data',
    \Magento\Framework\DB\Ddl\Table::TYPE_BLOB,
    '2M',
    array('nullable' => false),
    'Session Data'
)->setComment(
    'Database Sessions Storage'
);
$connection->createTable($table);

/**
 * Create table 'design_change'
 */
$table = $connection->newTable(
    $installer->getTable('design_change')
)->addColumn(
    'design_change_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('identity' => true, 'nullable' => false, 'primary' => true),
    'Design Change Id'
)->addColumn(
    'store_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
    'Store Id'
)->addColumn(
    'design',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    array(),
    'Design'
)->addColumn(
    'date_from',
    \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
    null,
    array(),
    'First Date of Design Activity'
)->addColumn(
    'date_to',
    \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
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
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'Design Changes'
);
$connection->createTable($table);

/**
 * Create table 'core_variable'
 */
$table = $connection->newTable(
    $installer->getTable('core_variable')
)->addColumn(
    'variable_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
    'Variable Id'
)->addColumn(
    'code',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    array(),
    'Variable Code'
)->addColumn(
    'name',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    array(),
    'Variable Name'
)->addIndex(
    $installer->getIdxName(
        'core_variable',
        array('code'),
        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
    ),
    array('code'),
    array('type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE)
)->setComment(
    'Variables'
);
$connection->createTable($table);

/**
 * Create table 'core_variable_value'
 */
$table = $connection->newTable(
    $installer->getTable('core_variable_value')
)->addColumn(
    'value_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
    'Variable Value Id'
)->addColumn(
    'variable_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
    'Variable Id'
)->addColumn(
    'store_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
    'Store Id'
)->addColumn(
    'plain_value',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    '64k',
    array(),
    'Plain Text Value'
)->addColumn(
    'html_value',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    '64k',
    array(),
    'Html Value'
)->addIndex(
    $installer->getIdxName(
        'core_variable_value',
        array('variable_id', 'store_id'),
        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
    ),
    array('variable_id', 'store_id'),
    array('type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE)
)->addIndex(
    $installer->getIdxName('core_variable_value', array('store_id')),
    array('store_id')
)->addForeignKey(
    $installer->getFkName('core_variable_value', 'store_id', 'store', 'store_id'),
    'store_id',
    $installer->getTable('store'),
    'store_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $installer->getFkName('core_variable_value', 'variable_id', 'core_variable', 'variable_id'),
    'variable_id',
    $installer->getTable('core_variable'),
    'variable_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'Variable Value'
);
$connection->createTable($table);

/**
 * Create table 'core_cache'
 */
$table = $connection->newTable(
    $installer->getTable('core_cache')
)->addColumn(
    'id',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    200,
    array('nullable' => false, 'primary' => true),
    'Cache Id'
)->addColumn(
    'data',
    \Magento\Framework\DB\Ddl\Table::TYPE_BLOB,
    '2M',
    array(),
    'Cache Data'
)->addColumn(
    'create_time',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array(),
    'Cache Creation Time'
)->addColumn(
    'update_time',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array(),
    'Time of Cache Updating'
)->addColumn(
    'expire_time',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array(),
    'Cache Expiration Time'
)->addIndex(
    $installer->getIdxName('core_cache', array('expire_time')),
    array('expire_time')
)->setComment(
    'Caches'
);
$connection->createTable($table);

/**
 * Create table 'core_cache_tag'
 */
$table = $connection->newTable(
    $installer->getTable('core_cache_tag')
)->addColumn(
    'tag',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    100,
    array('nullable' => false, 'primary' => true),
    'Tag'
)->addColumn(
    'cache_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    200,
    array('nullable' => false, 'primary' => true),
    'Cache Id'
)->addIndex(
    $installer->getIdxName('core_cache_tag', array('cache_id')),
    array('cache_id')
)->setComment(
    'Tag Caches'
);
$connection->createTable($table);

/**
 * Create table 'core_cache_option'
 */
$table = $connection->newTable(
    $installer->getTable('core_cache_option')
)->addColumn(
    'code',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    32,
    array('nullable' => false, 'primary' => true),
    'Code'
)->addColumn(
    'value',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array(),
    'Value'
)->setComment(
    'Cache Options'
);
$connection->createTable($table);

/**
 * Create table 'core_flag'
 */
$table = $connection->newTable(
    $installer->getTable('core_flag')
)->addColumn(
    'flag_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
    'Flag Id'
)->addColumn(
    'flag_code',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    array('nullable' => false),
    'Flag Code'
)->addColumn(
    'state',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
    'Flag State'
)->addColumn(
    'flag_data',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    '64k',
    array(),
    'Flag Data'
)->addColumn(
    'last_update',
    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
    null,
    array('nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE),
    'Date of Last Flag Update'
)->addIndex(
    $installer->getIdxName('core_flag', array('last_update')),
    array('last_update')
)->setComment(
    'Flag'
);
$connection->createTable($table);

/**
 * Drop Foreign Key on core_cache_tag.cache_id
 */
$connection->dropForeignKey(
    $installer->getTable('core_cache_tag'),
    $installer->getFkName('core_cache_tag', 'cache_id', 'core_cache', 'id')
);

/**
 * Create table 'core_theme'
 */
$table = $connection->newTable(
    $installer->getTable('core_theme')
)->addColumn(
    'theme_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
    'Theme identifier'
)->addColumn(
    'parent_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('nullable' => true),
    'Parent Id'
)->addColumn(
    'theme_path',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    array('nullable' => true),
    'Theme Path'
)->addColumn(
    'theme_version',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    array('nullable' => false),
    'Theme Version'
)->addColumn(
    'theme_title',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    array('nullable' => false),
    'Theme Title'
)->addColumn(
    'preview_image',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    array('nullable' => true),
    'Preview Image'
)->addColumn(
    'magento_version_from',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    array('nullable' => false),
    'Magento Version From'
)->addColumn(
    'magento_version_to',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    array('nullable' => false),
    'Magento Version To'
)->addColumn(
    'is_featured',
    \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
    null,
    array('nullable' => false, 'default' => 0),
    'Is Theme Featured'
)->setComment(
    'Core theme'
);
$connection->createTable($table);

/**
 * Modifying 'core_layout_link' table. Replace columns area, package, theme to theme_id
 */
$tableCoreLayoutLink = $installer->getTable('core_layout_link');

$connection->dropForeignKey(
    $tableCoreLayoutLink,
    $installer->getFkName('core_layout_link', 'store_id', 'store', 'store_id')
);

$connection->dropIndex(
    $tableCoreLayoutLink,
    $installer->getIdxName(
        'core_layout_link',
        array('store_id', 'package', 'theme', 'layout_update_id'),
        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
    )
);

$connection->dropColumn($tableCoreLayoutLink, 'area');

$connection->dropColumn($tableCoreLayoutLink, 'package');

$connection->changeColumn(
    $tableCoreLayoutLink,
    'theme',
    'theme_id',
    array(
        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        'unsigned' => true,
        'nullable' => false,
        'comment' => 'Theme id'
    )
);

$connection->addIndex(
    $tableCoreLayoutLink,
    $installer->getIdxName(
        'core_layout_link',
        array('store_id', 'theme_id', 'layout_update_id'),
        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
    ),
    array('store_id', 'theme_id', 'layout_update_id'),
    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
);

$connection->addForeignKey(
    $installer->getFkName('core_layout_link', 'store_id', 'store', 'store_id'),
    $tableCoreLayoutLink,
    'store_id',
    $installer->getTable('store'),
    'store_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
);

$connection->addForeignKey(
    $installer->getFkName('core_layout_link', 'theme_id', 'core_theme', 'theme_id'),
    $tableCoreLayoutLink,
    'theme_id',
    $installer->getTable('core_theme'),
    'theme_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
);

/**
 * Add column 'area' to 'core_theme'
 */
$connection->addColumn(
    $installer->getTable('core_theme'),
    'area',
    array(
        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        'length' => '255',
        'nullable' => false,
        'comment' => 'Theme Area'
    )
);

/**
 * Modifying 'core_layout_link' table. Adding 'is_temporary' column
 */
$tableCoreLayoutLink = $installer->getTable('core_layout_link');

$connection->addColumn(
    $tableCoreLayoutLink,
    'is_temporary',
    array(
        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
        'nullable' => false,
        'default' => '0',
        'comment' => 'Defines whether Layout Update is Temporary'
    )
);

// we must drop next 2 foreign keys to have an ability to drop index
$connection->dropForeignKey(
    $tableCoreLayoutLink,
    $installer->getFkName($tableCoreLayoutLink, 'store_id', 'store', 'store_id')
);
$connection->dropForeignKey(
    $tableCoreLayoutLink,
    $installer->getFkName($tableCoreLayoutLink, 'theme_id', 'core_theme', 'theme_id')
);

$connection->dropIndex(
    $tableCoreLayoutLink,
    $installer->getIdxName(
        $tableCoreLayoutLink,
        array('store_id', 'theme_id', 'layout_update_id'),
        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
    )
);

$connection->addIndex(
    $tableCoreLayoutLink,
    $installer->getIdxName(
        $tableCoreLayoutLink,
        array('store_id', 'theme_id', 'layout_update_id', 'is_temporary'),
        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
    ),
    array('store_id', 'theme_id', 'layout_update_id', 'is_temporary'),
    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
);

// recreate 2 dropped foreign keys to have an ability to drop index
$connection->addForeignKey(
    $installer->getFkName($tableCoreLayoutLink, 'store_id', 'store', 'store_id'),
    $tableCoreLayoutLink,
    'store_id',
    $installer->getTable('store'),
    'store_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
);
$connection->addForeignKey(
    $installer->getFkName($tableCoreLayoutLink, 'theme_id', 'core_theme', 'theme_id'),
    $tableCoreLayoutLink,
    'theme_id',
    $installer->getTable('core_theme'),
    'theme_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
);

/**
 * Add column 'updated_at' to 'core_layout_update'
 */
$connection->addColumn(
    $installer->getTable('core_layout_update'),
    'updated_at',
    array(
        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
        'nullable' => true,
        'comment' => 'Last Update Timestamp'
    )
);

/**
 * Create table 'core_theme_files'
 */
$table = $connection->newTable(
    $installer->getTable('core_theme_files')
)->addColumn(
    'theme_files_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
    'Theme files identifier'
)->addColumn(
    'theme_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('nullable' => false, 'unsigned' => true),
    'Theme Id'
)->addColumn(
    'file_name',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    array('nullable' => false),
    'File Name'
)->addColumn(
    'file_type',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    32,
    array('nullable' => false),
    'File Type'
)->addColumn(
    'content',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    \Magento\Framework\DB\Ddl\Table::MAX_TEXT_SIZE,
    array('nullable' => false),
    'File Content'
)->addColumn(
    'order',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('nullable' => false, 'default' => 0),
    'Order'
)->addForeignKey(
    $installer->getFkName('core_theme_files', 'theme_id', 'core_theme', 'theme_id'),
    'theme_id',
    $installer->getTable('core_theme'),
    'theme_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'Core theme files'
);
$connection->createTable($table);

/**
 * Add and change columns in 'core_theme_files'
 */
$connection->addColumn(
    $installer->getTable('core_theme_files'),
    'is_temporary',
    array(
        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
        'nullable' => false,
        'default' => 0,
        'comment' => 'Is Temporary File'
    )
);

$connection->changeColumn(
    $installer->getTable('core_theme_files'),
    'file_name',
    'file_path',
    array(
        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        'length' => 255,
        'nullable' => true,
        'comment' => 'Relative path to file'
    )
);

$connection->changeColumn(
    $installer->getTable('core_theme_files'),
    'order',
    'sort_order',
    array('type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT)
);

/**
 * Create table 'core_theme_files_link'
 */
$table = $connection->newTable(
    $installer->getTable('core_theme_files_link')
)->addColumn(
    'files_link_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('identity' => true, 'nullable' => false, 'unsigned' => true, 'primary' => true),
    'Customization link id'
)->addColumn(
    'theme_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('nullable' => false, 'unsigned' => true),
    'Theme Id'
)->addColumn(
    'layout_link_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('nullable' => false, 'unsigned' => true),
    'Theme layout link id'
)->addForeignKey(
    $installer->getFkName('core_theme_files_link', 'theme_id', 'core_theme', 'theme_id'),
    'theme_id',
    $installer->getTable('core_theme'),
    'theme_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'Core theme link on layout update'
);
$installer->getConnection()->createTable($table);

/**
 * Add column 'type' to 'core_theme'
 */
$connection->addColumn(
    $installer->getTable('core_theme'),
    'type',
    array(
        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        'nullable' => false,
        'comment' => 'Theme type: 0:physical, 1:virtual, 2:staging'
    )
);

/**
 * Rename table
 */
$wrongName = 'core_theme_files';
$rightName = 'core_theme_file';
if ($installer->tableExists($wrongName)) {
    $connection->renameTable($installer->getTable($wrongName), $installer->getTable($rightName));
}

$oldName = 'core_theme_files_link';
$newName = 'core_theme_file_update';

$oldTableName = $installer->getTable($oldName);

/**
 * Drop foreign key and index
 */
$connection->dropForeignKey($oldTableName, $installer->getFkName($oldName, 'theme_id', 'core_theme', 'theme_id'));
$connection->dropIndex($oldTableName, $installer->getFkName($oldName, 'theme_id', 'core_theme', 'theme_id'));

/**
 * Rename table
 */
if ($installer->tableExists($oldName)) {
    $connection->renameTable($installer->getTable($oldName), $installer->getTable($newName));
}

$newTableName = $installer->getTable($newName);

/**
 * Rename column
 */
$oldColumn = 'files_link_id';
$newColumn = 'file_update_id';
$connection->changeColumn(
    $newTableName,
    $oldColumn,
    $newColumn,
    array(
        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        'primary' => true,
        'nullable' => false,
        'unsigned' => true,
        'comment' => 'Customization file update id'
    )
);

/**
 * Rename column
 */
$oldColumn = 'layout_link_id';
$newColumn = 'layout_update_id';
$connection->changeColumn(
    $newTableName,
    $oldColumn,
    $newColumn,
    array(
        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        'nullable' => false,
        'unsigned' => true,
        'comment' => 'Theme layout update id'
    )
);

/**
 * Add foreign keys and indexes
 */
$connection->addIndex(
    $newTableName,
    $installer->getIdxName(
        $newTableName,
        'theme_id',
        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
    ),
    'theme_id',
    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
);
$connection->addForeignKey(
    $installer->getFkName($newTableName, 'theme_id', 'core_theme', 'theme_id'),
    $newTableName,
    'theme_id',
    $installer->getTable('core_theme'),
    'theme_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
);
$connection->addIndex(
    $newTableName,
    $installer->getIdxName(
        $newTableName,
        'layout_update_id',
        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX
    ),
    'layout_update_id',
    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX
);
$connection->addForeignKey(
    $installer->getFkName($newTableName, 'layout_update_id', 'core_layout_update', 'layout_update_id'),
    $newTableName,
    'layout_update_id',
    $installer->getTable('core_layout_update'),
    'layout_update_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
);

/**
 * Change data
 */
$select = $connection->select()->from(
    $newTableName
)->join(
    array('link' => $installer->getTable('core_layout_link')),
    sprintf('link.layout_link_id = %s.layout_update_id', $newTableName)
);
$rows = $connection->fetchAll($select);
foreach ($rows as $row) {
    $connection->update(
        $newTableName,
        array('layout_update_id' => $row['layout_update_id']),
        'file_update_id = ' . $row['file_update_id']
    );
}

/**
 * Add column 'code' into 'core_theme'
 */
$connection->addColumn(
    $installer->getTable('core_theme'),
    'code',
    array('type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 'comment' => 'Full theme code, including package')
);

/**
 * Drop table 'core_theme_file_update'
 */
$connection->dropTable('core_theme_file_update');

/**
 * Drop columns 'magento_version_from' and 'magento_version_to' in 'core_theme'
 */
$table = $installer->getTable('core_theme');
$connection->dropColumn($table, 'magento_version_from');
$connection->dropColumn($table, 'magento_version_to');

$installer->endSetup();
