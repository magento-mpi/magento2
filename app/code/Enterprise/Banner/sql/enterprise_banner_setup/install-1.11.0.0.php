<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Banner
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer Enterprise_Banner_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/**
 * Create table 'enterprise_banner'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('enterprise_banner'))
    ->addColumn('banner_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Banner Id')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Name')
    ->addColumn('is_enabled', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        ), 'Is Enabled')
    ->addColumn('types', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Types')
    ->setComment('Enterprise Banner');
$installer->getConnection()->createTable($table);

/**
 * Create table 'enterprise_banner_content'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('enterprise_banner_content'))
    ->addColumn('banner_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
        ), 'Banner Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
        ), 'Store Id')
    ->addColumn('banner_content', Varien_Db_Ddl_Table::TYPE_TEXT, '2M', array(
        ), 'Banner Content')
    ->addIndex($installer->getIdxName('enterprise_banner_content', array('banner_id')),
        array('banner_id'))
    ->addIndex($installer->getIdxName('enterprise_banner_content', array('store_id')),
        array('store_id'))
    ->addForeignKey($installer->getFkName('enterprise_banner_content', 'banner_id', 'enterprise_banner', 'banner_id'),
        'banner_id', $installer->getTable('enterprise_banner'), 'banner_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('enterprise_banner_content', 'store_id', 'core_store', 'store_id'),
        'store_id', $installer->getTable('core_store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Enterprise Banner Content');
$installer->getConnection()->createTable($table);

/**
 * Create table 'enterprise_banner_catalogrule'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('enterprise_banner_catalogrule'))
    ->addColumn('banner_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Banner Id')
    ->addColumn('rule_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Rule Id')
    ->addIndex($installer->getIdxName('enterprise_banner_catalogrule', array('banner_id')),
        array('banner_id'))
    ->addIndex($installer->getIdxName('enterprise_banner_catalogrule', array('rule_id')),
        array('rule_id'))
    ->addForeignKey(
        $installer->getFkName('enterprise_banner_catalogrule', 'banner_id', 'enterprise_banner', 'banner_id'),
        'banner_id', $installer->getTable('enterprise_banner'), 'banner_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('enterprise_banner_catalogrule', 'rule_id', 'catalogrule', 'rule_id'),
        'rule_id', $installer->getTable('catalogrule'), 'rule_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Enterprise Banner Catalogrule');
$installer->getConnection()->createTable($table);

/**
 * Create table 'enterprise_banner_salesrule'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('enterprise_banner_salesrule'))
    ->addColumn('banner_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Banner Id')
    ->addColumn('rule_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Rule Id')
    ->addIndex($installer->getIdxName('enterprise_banner_salesrule', array('banner_id')),
        array('banner_id'))
    ->addIndex($installer->getIdxName('enterprise_banner_salesrule', array('rule_id')),
        array('rule_id'))
    ->addForeignKey($installer->getFkName('enterprise_banner_salesrule', 'banner_id', 'enterprise_banner', 'banner_id'),
        'banner_id', $installer->getTable('enterprise_banner'), 'banner_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('enterprise_banner_salesrule', 'rule_id', 'salesrule', 'rule_id'),
        'rule_id', $installer->getTable('salesrule'), 'rule_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Enterprise Banner Salesrule');
$installer->getConnection()->createTable($table);

$installer->endSetup();
