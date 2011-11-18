<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @category    Mage
 * @package     Mage_Reports
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
/*
 * Prepare database for tables install
 */
$installer->startSetup();

/**
 * Create viewed aggregated daily
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable(Mage_Reports_Model_Resource_Report_Product_Viewed::AGGREGATION_DAILY))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Id')
    ->addColumn('period', Varien_Db_Ddl_Table::TYPE_DATE, null, array(
        ), 'Period')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        ), 'Store Id')
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        ), 'Product Id')
    ->addColumn('product_name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => true,
        ), 'Product Name')
    ->addColumn('product_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'nullable'  => false,
        'default'   => '0.0000',
        ), 'Product Price')
    ->addColumn('views_num', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Number of Views')
    ->addColumn('rating_pos', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Rating Pos')
    ->addIndex(
        $installer->getIdxName(
            Mage_Reports_Model_Resource_Report_Product_Viewed::AGGREGATION_DAILY,
            array('period', 'store_id', 'product_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('period', 'store_id', 'product_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex(
        $installer->getIdxName(
            Mage_Reports_Model_Resource_Report_Product_Viewed::AGGREGATION_DAILY,
            array('store_id')
        ),
        array('store_id'))
    ->addIndex(
        $installer->getIdxName(
            Mage_Reports_Model_Resource_Report_Product_Viewed::AGGREGATION_DAILY,
            array('product_id')
        ),
        array('product_id'))
    ->addForeignKey(
        $installer->getFkName(
            Mage_Reports_Model_Resource_Report_Product_Viewed::AGGREGATION_DAILY,
            'store_id', 'core/store', 'store_id'),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(
            Mage_Reports_Model_Resource_Report_Product_Viewed::AGGREGATION_DAILY,
            'product_id',
            'catalog/product',
            'entity_id'
        ),
        'product_id', $installer->getTable('catalog/product'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Most Viewed Products Aggregated Daily');
$installer->getConnection()->createTable($table);

/**
 * Create viewed aggregated monthly
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable(Mage_Reports_Model_Resource_Report_Product_Viewed::AGGREGATION_MONTHLY))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Id')
    ->addColumn('period', Varien_Db_Ddl_Table::TYPE_DATE, null, array(
        ), 'Period')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        ), 'Store Id')
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        ), 'Product Id')
    ->addColumn('product_name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => true,
        ), 'Product Name')
    ->addColumn('product_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'nullable'  => false,
        'default'   => '0.0000',
        ), 'Product Price')
    ->addColumn('views_num', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'default'   => '0.0000',
        ), 'Number of Views')
    ->addColumn('rating_pos', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Rating Pos')
    ->addIndex(
        $installer->getIdxName(
            Mage_Reports_Model_Resource_Report_Product_Viewed::AGGREGATION_MONTHLY,
            array('period', 'store_id', 'product_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('period', 'store_id', 'product_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex(
        $installer->getIdxName(
            Mage_Reports_Model_Resource_Report_Product_Viewed::AGGREGATION_MONTHLY,
            array('store_id')),
        array('store_id'))
    ->addIndex(
        $installer->getIdxName(
            Mage_Reports_Model_Resource_Report_Product_Viewed::AGGREGATION_MONTHLY,
            array('product_id')),
        array('product_id'))
    ->addForeignKey(
        $installer->getFkName(
            Mage_Reports_Model_Resource_Report_Product_Viewed::AGGREGATION_MONTHLY,
            'store_id',
            'core/store',
            'store_id'
        ),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(
            Mage_Reports_Model_Resource_Report_Product_Viewed::AGGREGATION_MONTHLY,
            'product_id',
            'catalog/product',
            'entity_id'
        ),
        'product_id', $installer->getTable('catalog/product'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Most Viewed Products Aggregated Monthly');
$installer->getConnection()->createTable($table);

/**
 * Create viewed aggregated yearly
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable(Mage_Reports_Model_Resource_Report_Product_Viewed::AGGREGATION_YEARLY))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Id')
    ->addColumn('period', Varien_Db_Ddl_Table::TYPE_DATE, null, array(
        ), 'Period')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        ), 'Store Id')
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        ), 'Product Id')
    ->addColumn('product_name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => true,
        ), 'Product Name')
    ->addColumn('product_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'nullable'  => false,
        'default'   => '0.0000',
        ), 'Product Price')
    ->addColumn('views_num', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'default'   => '0.0000',
        ), 'Number of Views')
    ->addColumn('rating_pos', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Rating Pos')
    ->addIndex(
        $installer->getIdxName(
            Mage_Reports_Model_Resource_Report_Product_Viewed::AGGREGATION_YEARLY,
            array('period', 'store_id', 'product_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('period', 'store_id', 'product_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex(
        $installer->getIdxName(
            Mage_Reports_Model_Resource_Report_Product_Viewed::AGGREGATION_YEARLY,
            array('store_id')),
        array('store_id'))
    ->addIndex(
        $installer->getIdxName(
            Mage_Reports_Model_Resource_Report_Product_Viewed::AGGREGATION_YEARLY,
            array('product_id')),
        array('product_id'))
    ->addForeignKey(
        $installer->getFkName(
            Mage_Reports_Model_Resource_Report_Product_Viewed::AGGREGATION_YEARLY,
            'store_id',
            'core/store',
            'store_id'
        ),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(
            Mage_Reports_Model_Resource_Report_Product_Viewed::AGGREGATION_YEARLY,
            'product_id',
            'catalog/product',
            'entity_id'
        ),
        'product_id', $installer->getTable('catalog/product'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Most Viewed Products Aggregated Yearly');
$installer->getConnection()->createTable($table);

$installer->endSetup();
