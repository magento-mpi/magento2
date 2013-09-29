<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @copyright   {copyright}
 * @license     {license_link}
 */
/* @var $installer \Magento\Core\Model\Resource\Setup */
$installer = $this;
$installer->startSetup();

/**
 * Create table 'magento_versionscms_page_version'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('magento_versionscms_page_version'))
    ->addColumn('version_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Version Id')
    ->addColumn('label', \Magento\DB\Ddl\Table::TYPE_TEXT, 255, array(
        ), 'Label')
    ->addColumn('access_level', \Magento\DB\Ddl\Table::TYPE_TEXT, 9, array(
        ), 'Access Level')
    ->addColumn('page_id', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        ), 'Page Id')
    ->addColumn('user_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        ), 'User Id')
    ->addColumn('revisions_count', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        ), 'Revisions Count')
    ->addColumn('version_number', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Version Number')
    ->addColumn('created_at', \Magento\DB\Ddl\Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        ), 'Created At')
    ->addIndex($installer->getIdxName('magento_versionscms_page_version', array('page_id')),
        array('page_id'))
    ->addIndex($installer->getIdxName('magento_versionscms_page_version', array('user_id')),
        array('user_id'))
    ->addIndex($installer->getIdxName('magento_versionscms_page_version', array('version_number')),
        array('version_number'))
    ->addForeignKey($installer->getFkName('magento_versionscms_page_version', 'page_id', 'cms_page', 'page_id'),
        'page_id', $installer->getTable('cms_page'), 'page_id',
        \Magento\DB\Ddl\Table::ACTION_CASCADE, \Magento\DB\Ddl\Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('magento_versionscms_page_version', 'user_id', 'admin_user', 'user_id'),
        'user_id', $installer->getTable('admin_user'), 'user_id',
        \Magento\DB\Ddl\Table::ACTION_SET_NULL, \Magento\DB\Ddl\Table::ACTION_CASCADE)
    ->setComment('Enterprise Cms Page Version');
$installer->getConnection()->createTable($table);

/**
 * Create table 'magento_versionscms_page_revision'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('magento_versionscms_page_revision'))
    ->addColumn('revision_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Revision Id')
    ->addColumn('version_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Version Id')
    ->addColumn('page_id', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        ), 'Page Id')
    ->addColumn('root_template', \Magento\DB\Ddl\Table::TYPE_TEXT, 255, array(
        ), 'Root Template')
    ->addColumn('meta_keywords', \Magento\DB\Ddl\Table::TYPE_TEXT, '64k', array(
        ), 'Meta Keywords')
    ->addColumn('meta_description', \Magento\DB\Ddl\Table::TYPE_TEXT, '64k', array(
        ), 'Meta Description')
    ->addColumn('content_heading', \Magento\DB\Ddl\Table::TYPE_TEXT, 255, array(
        ), 'Content Heading')
    ->addColumn('content', \Magento\DB\Ddl\Table::TYPE_TEXT, '2M', array(
        ), 'Content')
    ->addColumn('created_at', \Magento\DB\Ddl\Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        ), 'Created At')
    ->addColumn('layout_update_xml', \Magento\DB\Ddl\Table::TYPE_TEXT, '64k', array(
        ), 'Layout Update Xml')
    ->addColumn('custom_theme', \Magento\DB\Ddl\Table::TYPE_TEXT, 100, array(
        ), 'Custom Theme')
    ->addColumn('custom_root_template', \Magento\DB\Ddl\Table::TYPE_TEXT, 255, array(
        ), 'Custom Root Template')
    ->addColumn('custom_layout_update_xml', \Magento\DB\Ddl\Table::TYPE_TEXT, '64k', array(
        ), 'Custom Layout Update Xml')
    ->addColumn('custom_theme_from', \Magento\DB\Ddl\Table::TYPE_DATE, null, array(
        ), 'Custom Theme From')
    ->addColumn('custom_theme_to', \Magento\DB\Ddl\Table::TYPE_DATE, null, array(
        ), 'Custom Theme To')
    ->addColumn('user_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        ), 'User Id')
    ->addColumn('revision_number', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Revision Number')
    ->addIndex($installer->getIdxName('magento_versionscms_page_revision', array('version_id')),
        array('version_id'))
    ->addIndex($installer->getIdxName('magento_versionscms_page_revision', array('page_id')),
        array('page_id'))
    ->addIndex($installer->getIdxName('magento_versionscms_page_revision', array('user_id')),
        array('user_id'))
    ->addIndex($installer->getIdxName('magento_versionscms_page_revision', array('revision_number')),
        array('revision_number'))
    ->addForeignKey($installer->getFkName('magento_versionscms_page_revision', 'page_id', 'cms_page', 'page_id'),
        'page_id', $installer->getTable('cms_page'), 'page_id',
        \Magento\DB\Ddl\Table::ACTION_CASCADE, \Magento\DB\Ddl\Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('magento_versionscms_page_revision', 'user_id', 'admin_user', 'user_id'),
        'user_id', $installer->getTable('admin_user'), 'user_id',
        \Magento\DB\Ddl\Table::ACTION_SET_NULL, \Magento\DB\Ddl\Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(
            'magento_versionscms_page_revision',
            'version_id',
            'magento_versionscms_page_version',
            'version_id'
        ),
        'version_id', $installer->getTable('magento_versionscms_page_version'), 'version_id',
        \Magento\DB\Ddl\Table::ACTION_CASCADE, \Magento\DB\Ddl\Table::ACTION_CASCADE)
    ->setComment('Enterprise Cms Page Revision');
$installer->getConnection()->createTable($table);

/**
 * Create table 'magento_versionscms_increment'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('magento_versionscms_increment'))
    ->addColumn('increment_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Increment Id')
    ->addColumn('increment_type', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        ), 'Increment Type')
    ->addColumn('increment_node', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Increment Node')
    ->addColumn('increment_level', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Increment Level')
    ->addColumn('last_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Last Id')
    ->addIndex($installer->getIdxName('magento_versionscms_increment',
        array('increment_type', 'increment_node', 'increment_level'),
        \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE),
        array('increment_type', 'increment_node', 'increment_level'),
        array('type' => \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE))
    ->setComment('Enterprise Cms Increment');
$installer->getConnection()->createTable($table);

/**
 * Create table 'magento_versionscms_hierarchy_node'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('magento_versionscms_hierarchy_node'))
    ->addColumn('node_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Node Id')
    ->addColumn('parent_node_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        ), 'Parent Node Id')
    ->addColumn('page_id', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        ), 'Page Id')
    ->addColumn('identifier', \Magento\DB\Ddl\Table::TYPE_TEXT, 100, array(
        ), 'Identifier')
    ->addColumn('label', \Magento\DB\Ddl\Table::TYPE_TEXT, 255, array(
        ), 'Label')
    ->addColumn('level', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Level')
    ->addColumn('sort_order', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        ), 'Sort Order')
    ->addColumn('request_url', \Magento\DB\Ddl\Table::TYPE_TEXT, 255, array(
        ), 'Request Url')
    ->addColumn('xpath', \Magento\DB\Ddl\Table::TYPE_TEXT, 255, array(
        ), 'Xpath')
    ->addIndex(
        $installer->getIdxName(
            'magento_versionscms_hierarchy_node',
            array('request_url'),
            \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
        ),
        array('request_url'), array('type' => \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName('magento_versionscms_hierarchy_node', array('parent_node_id')),
        array('parent_node_id'))
    ->addIndex($installer->getIdxName('magento_versionscms_hierarchy_node', array('page_id')),
        array('page_id'))
    ->addForeignKey($installer->getFkName('magento_versionscms_hierarchy_node', 'page_id', 'cms_page', 'page_id'),
        'page_id', $installer->getTable('cms_page'), 'page_id',
        \Magento\DB\Ddl\Table::ACTION_CASCADE, \Magento\DB\Ddl\Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(
            'magento_versionscms_hierarchy_node',
            'parent_node_id',
            'magento_versionscms_hierarchy_node',
            'node_id'
        ),
        'parent_node_id', $installer->getTable('magento_versionscms_hierarchy_node'), 'node_id',
        \Magento\DB\Ddl\Table::ACTION_CASCADE, \Magento\DB\Ddl\Table::ACTION_CASCADE)
    ->setComment('Enterprise Cms Hierarchy Node');
$installer->getConnection()->createTable($table);

/**
 * Create table 'magento_versionscms_hierarchy_metadata'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('magento_versionscms_hierarchy_metadata'))
    ->addColumn('node_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Node Id')
    ->addColumn('meta_first_last', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Meta First Last')
    ->addColumn('meta_next_previous', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Meta Next Previous')
    ->addColumn('meta_chapter', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Meta Chapter')
    ->addColumn('meta_section', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Meta Section')
    ->addColumn('meta_cs_enabled', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Meta Cs Enabled')
    ->addColumn('pager_visibility', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        ), 'Pager Visibility')
    ->addColumn('pager_frame', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        ), 'Pager Frame')
    ->addColumn('pager_jump', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        ), 'Pager Jump')
    ->addColumn('menu_visibility', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        ), 'Menu Visibility')
    ->addColumn('menu_excluded', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Menu Excluded')
    ->addColumn('menu_layout', \Magento\DB\Ddl\Table::TYPE_TEXT, 50, array(
        ), 'Menu Layout')
    ->addColumn('menu_brief', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Menu Brief')
    ->addColumn('menu_levels_down', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        ), 'Menu Levels Down')
    ->addColumn('menu_ordered', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        ), 'Menu Ordered')
    ->addColumn('menu_list_type', \Magento\DB\Ddl\Table::TYPE_TEXT, 50, array(
        ), 'Menu List Type')
    ->addForeignKey(
        $installer->getFkName(
            'magento_versionscms_hierarchy_metadata',
            'node_id',
            'magento_versionscms_hierarchy_node',
            'node_id'
        ),
        'node_id', $installer->getTable('magento_versionscms_hierarchy_node'), 'node_id',
        \Magento\DB\Ddl\Table::ACTION_CASCADE, \Magento\DB\Ddl\Table::ACTION_CASCADE)
    ->setComment('Enterprise Cms Hierarchy Metadata');
$installer->getConnection()->createTable($table);

/**
 * Create table 'magento_versionscms_hierarchy_lock'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('magento_versionscms_hierarchy_lock'))
    ->addColumn('lock_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Lock Id')
    ->addColumn('user_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'User Id')
    ->addColumn('user_name', \Magento\DB\Ddl\Table::TYPE_TEXT, 50, array(
        ), 'User Name')
    ->addColumn('session_id', \Magento\DB\Ddl\Table::TYPE_TEXT, 50, array(
        ), 'Session Id')
    ->addColumn('started_at', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Started At')
    ->setComment('Enterprise Cms Hierarchy Lock');
$installer->getConnection()->createTable($table);

// Add fields for cms/page table
$installer->getConnection()
    ->addColumn($installer->getTable('cms_page'), 'published_revision_id', array(
        'type'      => \Magento\DB\Ddl\Table::TYPE_INTEGER,
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        'comment'   => 'Published Revision Id'
    ));
$installer->getConnection()
    ->addColumn($installer->getTable('cms_page'), 'website_root', array(
        'type'      => \Magento\DB\Ddl\Table::TYPE_SMALLINT,
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '1',
        'comment'   => 'Website Root'
    ));
$installer->getConnection()
    ->addColumn($installer->getTable('cms_page'), 'under_version_control', array(
        'type'      => \Magento\DB\Ddl\Table::TYPE_SMALLINT,
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        'comment'   => 'Under Version Control Flag'
    ));

$installer->endSetup();
