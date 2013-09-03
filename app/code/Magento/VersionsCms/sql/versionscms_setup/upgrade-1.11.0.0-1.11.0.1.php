<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer Magento_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$nodeTableName = $installer->getTable('magento_versionscms_hierarchy_node');

$installer
    ->getConnection()
    ->addColumn($nodeTableName, 'scope', array(
        'type'      => \Magento\DB\Ddl\Table::TYPE_TEXT,
        'length'    => '8',
        'comment'   => 'Scope: default|website|store',
        'nullable'  => false,
        'default'   => 'default',
    ));
$installer
    ->getConnection()
    ->addColumn($nodeTableName, 'scope_id', array(
        'type'      => \Magento\DB\Ddl\Table::TYPE_INTEGER,
        'comment'   => 'Scope Id',
        'nullable'  => false,
        'default'   => '0',
        'UNSIGNED'  => true,
    ));

$installer->endSetup();
