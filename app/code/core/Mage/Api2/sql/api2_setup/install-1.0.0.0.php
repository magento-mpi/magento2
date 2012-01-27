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
 * @package     Mage_Api2
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
/**
 * Make Global ACL tables
 */
/** @var $installer Mage_Api2_Model_Resource_Setup */
$installer = $this;
/** @var $adapter Varien_Db_Adapter_Pdo_Mysql */
$adapter = $installer->getConnection();

/**
 * Create table 'api2/acl_role'
 */
$table = $adapter->newTable($installer->getTable('api2/acl_role'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
        ), 'Entity ID')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null,
        array(
            'nullable' => false,
            'default'  => Varien_Db_Ddl_Table::TIMESTAMP_INIT
        ), 'Created At')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null,
        array(
            'nullable'  => true
        ), 'Updated At')
    ->addColumn('role_name', Varien_Db_Ddl_Table::TYPE_VARCHAR,
        255, array('nullable'  => false), 'Name of role')
    ->addIndex($installer->getIdxName('api2/acl_role', array('created_at')), array('created_at'))
    ->addIndex($installer->getIdxName('api2/acl_role', array('updated_at')), array('updated_at'))
    ->setComment('Api2 Global ACL Roles');
$adapter->createTable($table);

/**
 * Create table 'api2/acl_user'
 */
$table = $adapter->newTable($installer->getTable('api2/acl_user'))
    ->addColumn('admin_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned'  => true,
            'nullable'  => false,
        ), 'Admin ID')
    ->addColumn('role_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned'  => true,
            'nullable'  => false,
        ), 'Role ID')
    ->addForeignKey(
        $installer->getFkName('api2/acl_user', 'admin_id', 'admin/user', 'user_id'),
        'admin_id',
        $installer->getTable('admin/user'),
        'user_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName('api2/acl_user', 'role_id', 'api2/acl_role', 'entity_id'),
        'role_id',
        $installer->getTable('api2/acl_role'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Api2 Global ACL Users');
$adapter->createTable($table);

/**
 * Create table 'api2/acl_rule'
 */
$table = $adapter->newTable($installer->getTable('api2/acl_rule'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'primary'   => true,
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
        ), 'Entity ID')
    ->addColumn('role_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned'  => true,
            'nullable'  => false,
        ), 'Role ID')
    ->addColumn('resource', Varien_Db_Ddl_Table::TYPE_VARCHAR,
            255, array('nullable'  => false), 'Resource ID')
    ->addColumn('permission', Varien_Db_Ddl_Table::TYPE_SMALLINT,
            null, array('nullable'  => false), 'Permission ID')
    ->addForeignKey(
        $installer->getFkName('api2/acl_rule', 'role_id', 'api2/acl_role', 'entity_id'),
        'role_id',
        $installer->getTable('api2/acl_role'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Api2 Global ACL Rules');
$adapter->createTable($table);
