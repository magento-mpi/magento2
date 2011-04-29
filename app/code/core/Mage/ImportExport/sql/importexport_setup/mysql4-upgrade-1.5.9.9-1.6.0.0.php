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
 * @package     Mage_ImportExport
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var $installer Mage_ImportExport_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/**
 * Drop foreign keys
 */
$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/product_link_attribute_int'),
    'FK_INT_PRODUCT_LINK'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('catalog/product_link_attribute_int'),
    'FK_INT_PRODUCT_LINK_ATTRIBUTE'
);


/**
 * Drop indexes
 */
$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_super_link'),
    'UNQ_product_id_parent_id'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_super_attribute'),
    'UNQ_product_id_attribute_id'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('catalog/product_super_attribute_pricing'),
    'UNQ_product_super_attribute_id_value_index_website_id'
);


/**
 * Change columns
 */
$tables = array(
    $installer->getTable('importexport/importdata') => array(
        'columns' => array(
            'id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Id'
            ),
            'entity' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 50,
                'nullable'  => false,
                'comment'   => 'Entity'
            ),
            'behavior' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 10,
                'nullable'  => false,
                'default'   => Mage_ImportExport_Model_Import::BEHAVIOR_APPEND,
                'comment'   => 'Behavior'
            ),
            'data' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'default'   => '',
                'comment'   => 'Data'
            )
        ),
        'comment' => 'Import Data Table',
        'engine'  => 'InnoDB'
    )
);

$installer->getConnection()->modifyTables($tables);


/**
 * Add indexes
 */
$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_super_link'),
    $installer->getIdxName(
        'catalog/product_super_link',
        array('product_id', 'parent_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('product_id', 'parent_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

/**
 * Add unique key for 'catalog/product_super_attribute' table
 */
$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_super_attribute'),
    $installer->getIdxName(
        'catalog/product_super_attribute',
        array('product_id', 'attribute_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('product_id', 'attribute_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

/**
 * Add unique key for 'catalog/product_super_attribute_pricing' table
 */
$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_super_attribute_pricing'),
    $installer->getIdxName(
        'catalog/product_super_attribute_pricing',
        array('product_super_attribute_id', 'value_index', 'website_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('product_super_attribute_id', 'value_index', 'website_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

/**
 * Add unique key for 'catalog/product_link_attribute_int' table
 */
$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_link_attribute_int'),
    $installer->getIdxName(
        'catalog/product_link_attribute_int',
        array('product_link_attribute_id', 'link_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('product_link_attribute_id', 'link_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);


/**
 * Add foreign keys
 */
$installer->getConnection()->addForeignKey(
    $installer->getFkName(
        'catalog/product_link_attribute_int',
        'link_id',
        'catalog/product_link',
        'link_id'
    ),
    $installer->getTable('catalog/product_link_attribute_int'),
    'link_id',
    $installer->getTable('catalog/product_link'),
    'link_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(
        'catalog/product_link_attribute_int',
        'product_link_attribute_id',
        'catalog/product_link_attribute',
        'product_link_attribute_id'
    ),
    $installer->getTable('catalog/product_link_attribute_int'),
    'product_link_attribute_id',
    $installer->getTable('catalog/product_link_attribute'),
    'product_link_attribute_id'
);

$installer->endSetup();
