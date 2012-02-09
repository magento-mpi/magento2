<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer Enterprise_TargetRule_Model_Resource_Setup */
$installer = $this;
$connection = $installer->getConnection();

$installer->startSetup();

$table = $installer->getConnection()
    ->newTable($installer->getTable('enterprise_targetrule_customersegment'))
    ->addColumn('rule_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array('unsigned'  => true, 'nullable'  => false,
        'primary'   => true,), 'Rule Id')
    ->addColumn('segment_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array('unsigned'  => true, 'nullable'  => false,
        'primary'   => true,), 'Segment Id')
    ->addIndex($installer->getIdxName('enterprise_targetrule_customersegment', array('segment_id')),
        array('segment_id'))
    ->addForeignKey($installer->getFkName('enterprise_targetrule_customersegment', 'rule_id',
        'enterprise_targetrule', 'rule_id'),'rule_id', $installer->getTable('enterprise_targetrule'),
        'rule_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('enterprise_targetrule_customersegment', 'segment_id',
        'enterprise_customersegment_segment', 'segment_id'),'segment_id',
        $installer->getTable('enterprise_customersegment_segment'), 'segment_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Enterprise Targetrule Customersegment');
$installer->getConnection()->createTable($table);

$installer->getConnection()
    ->addColumn($installer->getTable('enterprise_targetrule_index'),'customer_segment_id',
        array('type' => Varien_Db_Ddl_Table::TYPE_SMALLINT, 'nullable' => true, 'default' => '0',
            'comment' => 'Use Customer Segment'));
$installer->getConnection()
    ->addIndex($installer->getTable('enterprise_targetrule_index'), Varien_Db_Adapter_Interface::INDEX_TYPE_PRIMARY,
        array('entity_id', 'store_id', 'customer_group_id', 'type_id','customer_segment_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_PRIMARY);

$installer->getConnection()
    ->addColumn($installer->getTable('enterprise_targetrule_index_related'),'customer_segment_id',
        array('type' => Varien_Db_Ddl_Table::TYPE_SMALLINT, 'unsigned' => true, 'nullable' => false, 'default' => '0',
            'comment' => 'Use Customer Segment'));
$installer->getConnection()
    ->addIndex($installer->getTable('enterprise_targetrule_index_related'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_PRIMARY, array('entity_id', 'store_id', 'customer_group_id',
            'customer_segment_id'), Varien_Db_Adapter_Interface::INDEX_TYPE_PRIMARY);

$installer->getConnection()
    ->addColumn($installer->getTable('enterprise_targetrule_index_upsell'), 'customer_segment_id',
        array('type' => Varien_Db_Ddl_Table::TYPE_SMALLINT, 'unsigned' => true, 'nullable' => false, 'default' => '0',
            'comment' => 'Use Customer Segment'));
$installer->getConnection()
    ->addIndex($installer->getTable('enterprise_targetrule_index_upsell'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_PRIMARY, array('entity_id', 'store_id', 'customer_group_id',
            'customer_segment_id'), Varien_Db_Adapter_Interface::INDEX_TYPE_PRIMARY);

$installer->getConnection()
    ->addColumn($installer->getTable('enterprise_targetrule_index_crosssell'),'customer_segment_id',
        array('type' => Varien_Db_Ddl_Table::TYPE_SMALLINT, 'unsigned' => true, 'nullable' => false, 'default' => '0',
            'comment' => 'Use Customer Segment'));
$installer->getConnection()
    ->addIndex($installer->getTable('enterprise_targetrule_index_crosssell'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_PRIMARY, array('entity_id', 'store_id', 'customer_group_id',
            'customer_segment_id'), Varien_Db_Adapter_Interface::INDEX_TYPE_PRIMARY);

$installer->endSetup();
