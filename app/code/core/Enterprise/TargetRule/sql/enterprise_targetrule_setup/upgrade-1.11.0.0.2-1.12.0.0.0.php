<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Enterprise
 * @package     Enterprise_TargetRule
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/* @var $installer Enterprise_TargetRule_Model_Resource_Setup */
$installer = $this;
$connection = $installer->getConnection();

$installer->startSetup();

$table = $installer->getConnection()
    ->newTable($installer->getTable('enterprise_targetrule/customersegment'))
    ->addColumn('rule_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array('unsigned'  => true, 'nullable'  => false,
        'primary'   => true,), 'Rule Id')
    ->addColumn('segment_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array('unsigned'  => true, 'nullable'  => false,
        'primary'   => true,), 'Segment Id')
    ->addIndex($installer->getIdxName('enterprise_targetrule/customersegment', array('segment_id')),
        array('segment_id'))
    ->addForeignKey($installer->getFkName('enterprise_targetrule/customersegment', 'rule_id',
        'enterprise_targetrule/rule', 'rule_id'),'rule_id', $installer->getTable('enterprise_targetrule/rule'),
        'rule_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('enterprise_targetrule/customersegment', 'segment_id',
        'enterprise_customersegment/segment', 'segment_id'),'segment_id',
        $installer->getTable('enterprise_customersegment/segment'), 'segment_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Enterprise Targetrule Customersegment');
$installer->getConnection()->createTable($table);

$installer->getConnection()
    ->addColumn($installer->getTable('enterprise_targetrule/index'),'customer_segment_id',
        array('type' => Varien_Db_Ddl_Table::TYPE_SMALLINT, 'nullable' => true, 'default' => '0',
            'comment' => 'Use Customer Segment'));
$installer->getConnection()
    ->addIndex($installer->getTable('enterprise_targetrule/index'), Varien_Db_Adapter_Interface::INDEX_TYPE_PRIMARY,
        array('entity_id', 'store_id', 'customer_group_id', 'type_id','customer_segment_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_PRIMARY);

$installer->getConnection()
    ->addColumn($installer->getTable('enterprise_targetrule/index_related'),'customer_segment_id',
        array('type' => Varien_Db_Ddl_Table::TYPE_SMALLINT, 'unsigned' => true, 'nullable' => false, 'default' => '0',
            'comment' => 'Use Customer Segment'));
$installer->getConnection()
    ->addIndex($installer->getTable('enterprise_targetrule/index_related'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_PRIMARY, array('entity_id', 'store_id', 'customer_group_id',
            'customer_segment_id'), Varien_Db_Adapter_Interface::INDEX_TYPE_PRIMARY);

$installer->getConnection()
    ->addColumn($installer->getTable('enterprise_targetrule/index_upsell'), 'customer_segment_id',
        array('type' => Varien_Db_Ddl_Table::TYPE_SMALLINT, 'unsigned' => true, 'nullable' => false, 'default' => '0',
            'comment' => 'Use Customer Segment'));
$installer->getConnection()
    ->addIndex($installer->getTable('enterprise_targetrule/index_upsell'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_PRIMARY, array('entity_id', 'store_id', 'customer_group_id',
            'customer_segment_id'), Varien_Db_Adapter_Interface::INDEX_TYPE_PRIMARY);

$installer->getConnection()
    ->addColumn($installer->getTable('enterprise_targetrule/index_crosssell'),'customer_segment_id',
        array('type' => Varien_Db_Ddl_Table::TYPE_SMALLINT, 'unsigned' => true, 'nullable' => false, 'default' => '0',
            'comment' => 'Use Customer Segment'));
$installer->getConnection()
    ->addIndex($installer->getTable('enterprise_targetrule/index_crosssell'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_PRIMARY, array('entity_id', 'store_id', 'customer_group_id',
            'customer_segment_id'), Varien_Db_Adapter_Interface::INDEX_TYPE_PRIMARY);

$installer->endSetup();
