<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer Magento_Core_Model_Resource_Setup */
$installer = $this;

$tableName = 'enterprise_banner_customersegment';

$table = $installer->getConnection()
    ->newTable($installer->getTable($tableName))
    ->addColumn('banner_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
    ), 'Banner Id')
    ->addColumn('segment_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
    ), 'Segment Id')
    ->addIndex($installer->getIdxName($tableName, array('banner_id')),
        array('banner_id'))
    ->addIndex($installer->getIdxName($tableName, array('segment_id')),
        array('segment_id'))
    ->addForeignKey(
        $installer->getFkName($tableName, 'banner_id', 'enterprise_banner', 'banner_id'),
        'banner_id', $installer->getTable('enterprise_banner'), 'banner_id',
        Magento_DB_Ddl_Table::ACTION_CASCADE, Magento_DB_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName($tableName, 'segment_id', 'enterprise_customersegment_segment', 'segment_id'),
        'segment_id', $installer->getTable('enterprise_customersegment_segment'), 'segment_id',
        Magento_DB_Ddl_Table::ACTION_CASCADE, Magento_DB_Ddl_Table::ACTION_CASCADE)
    ->setComment('Enterprise Banner Customersegment');

// Table used to be part of the Enterprise_Banner module, so during upgrade it may exist already
if (!$installer->getConnection()->isTableExists($table->getName())) {
    $installer->getConnection()->createTable($table);
}
