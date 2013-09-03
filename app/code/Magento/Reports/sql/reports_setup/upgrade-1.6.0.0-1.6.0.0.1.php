<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Magento_Core_Model_Resource_Setup */
$installer = $this;
/*
 * Prepare database for tables install
 */
$installer->startSetup();

$aggregationTables = array(
    Magento_Reports_Model_Resource_Report_Product_Viewed::AGGREGATION_DAILY,
    Magento_Reports_Model_Resource_Report_Product_Viewed::AGGREGATION_MONTHLY,
    Magento_Reports_Model_Resource_Report_Product_Viewed::AGGREGATION_YEARLY,
);
$aggregationTableComments = array(
    'Most Viewed Products Aggregated Daily',
    'Most Viewed Products Aggregated Monthly',
    'Most Viewed Products Aggregated Yearly',
);

for ($i = 0; $i < 3; ++$i) {
    $table = $installer->getConnection()
        ->newTable($installer->getTable($aggregationTables[$i]))
        ->addColumn('id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
            ), 'Id')
        ->addColumn('period', \Magento\DB\Ddl\Table::TYPE_DATE, null, array(
            ), 'Period')
        ->addColumn('store_id', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
            'unsigned'  => true,
            ), 'Store Id')
        ->addColumn('product_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
            'unsigned'  => true,
            ), 'Product Id')
        ->addColumn('product_name', \Magento\DB\Ddl\Table::TYPE_TEXT, 255, array(
            'nullable'  => true,
            ), 'Product Name')
        ->addColumn('product_price', \Magento\DB\Ddl\Table::TYPE_DECIMAL, '12,4', array(
            'nullable'  => false,
            'default'   => '0.0000',
            ), 'Product Price')
        ->addColumn('views_num', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
            'nullable'  => false,
            'default'   => '0',
            ), 'Number of Views')
        ->addColumn('rating_pos', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => '0',
            ), 'Rating Pos')
        ->addIndex(
            $installer->getIdxName(
                $aggregationTables[$i],
                array('period', 'store_id', 'product_id'),
                \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            array('period', 'store_id', 'product_id'), array('type' => \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE))
        ->addIndex($installer->getIdxName($aggregationTables[$i], array('store_id')), array('store_id'))
        ->addIndex($installer->getIdxName($aggregationTables[$i], array('product_id')), array('product_id'))
        ->addForeignKey(
            $installer->getFkName($aggregationTables[$i], 'store_id', 'core_store', 'store_id'),
            'store_id', $installer->getTable('core_store'), 'store_id',
            \Magento\DB\Ddl\Table::ACTION_CASCADE, \Magento\DB\Ddl\Table::ACTION_CASCADE)
        ->addForeignKey(
            $installer->getFkName($aggregationTables[$i], 'product_id', 'catalog_product_entity', 'entity_id'),
            'product_id', $installer->getTable('catalog_product_entity'), 'entity_id',
            \Magento\DB\Ddl\Table::ACTION_CASCADE, \Magento\DB\Ddl\Table::ACTION_CASCADE)
        ->setComment($aggregationTableComments[$i]);
    $installer->getConnection()->createTable($table);
}

$installer->endSetup();
