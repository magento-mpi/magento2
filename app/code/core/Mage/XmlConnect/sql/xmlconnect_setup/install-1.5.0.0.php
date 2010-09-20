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
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/* @var $installer Mage_Core_Model_Resource_Setup */

$installer = $this;

$installer->startSetup();

/**
 * Create table 'xmlconnect_application'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('xmlconnect_application'))
    ->addColumn('application_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Application Id')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Name')
    ->addColumn('code', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
        ), 'Code')
    ->addColumn('type', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
        ), 'Type')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        ), 'Store Id')
    ->addColumn('active_from', Varien_Db_Ddl_Table::TYPE_DATE, null, array(
        ), 'Active From')
    ->addColumn('active_to', Varien_Db_Ddl_Table::TYPE_DATE, null, array(
        ), 'Active To')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        ), 'Updated At')
    ->addColumn('configuration', Varien_Db_Ddl_Table::TYPE_TEXT, '64K', array(
        ), 'Configuration')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Status')
    ->addColumn('browsing_mode', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'default'   => '0',
        ), 'Browsing Mode')
    ->addIndex($installer->getIdxName('xmlconnect_application', array('code'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        array('code'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName('xmlconnect_application', array('store_id')),
        array('store_id'))
    ->addForeignKey($installer->getFkName('xmlconnect_application', 'store_id', 'core/store', 'store_id'),
            'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL, Varien_Db_Ddl_Table::ACTION_SET_NULL)
    ->setComment('Xmlconnect Application');
$installer->getConnection()->createTable($table);

/**
 * Create table 'xmlconnect_history'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('xmlconnect_history'))
    ->addColumn('history_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'History Id')
    ->addColumn('application_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Application Id')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        ), 'Created At')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        ), 'Store Id')
    ->addColumn('params', Varien_Db_Ddl_Table::TYPE_BLOB, '64K', array(
        ), 'Params')
    ->addColumn('title', Varien_Db_Ddl_Table::TYPE_TEXT, 200, array(
        ), 'Title')
    ->addColumn('activation_key', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Activation Key')
    ->addColumn('code', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Code')
    ->addIndex($installer->getIdxName('xmlconnect_history', array('application_id')),
        array('application_id'))
    ->addForeignKey($installer->getFkName('xmlconnect_history', 'application_id', 'xmlconnect_application', 'application_id'),
        'application_id', $installer->getTable('xmlconnect_application'), 'application_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Xmlconnect History');
$installer->getConnection()->createTable($table);


$entityTypeId     = $installer->getEntityTypeId('catalog_category');
$attributeSetId   = $installer->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = $installer->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

$installer->addAttribute('catalog_category', 'thumbnail', array(
    'type'              => 'varchar',
    'backend'           => 'catalog/category_attribute_backend_image',
    'frontend'          => '',
    'label'             => 'Thumbnail Image',
    'input'             => 'image',
    'class'             => '',
    'source'            => '',
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible'           => 1,
    'required'          => 0,
    'user_defined'      => 0,
    'default'           => '',
    'searchable'        => 0,
    'filterable'        => 0,
    'comparable'        => 0,
    'visible_on_front'  => 0,
    'unique'            => 0,
));

$installer->addAttributeToGroup(
    $entityTypeId,
    $attributeSetId,
    $attributeGroupId,
    'thumbnail',
    '4'
);

$installer->endSetup();
