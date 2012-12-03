<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Pci
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer Enterprise_Pci_Model_Resource_Setup */

$installer = $this;
$installer->startSetup();

/**
 * Create table 'enterprise_admin_passwords'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('enterprise_admin_passwords'))
    ->addColumn('password_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Password Id')
    ->addColumn('user_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'User Id')
    ->addColumn('password_hash', Varien_Db_Ddl_Table::TYPE_TEXT, 100, array(
        ), 'Password Hash')
    ->addColumn('expires', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Expires')
    ->addColumn('last_updated', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Last Updated')
    ->addIndex($installer->getIdxName('enterprise_admin_passwords', array('user_id')),
        array('user_id'))
    ->addForeignKey($installer->getFkName('enterprise_admin_passwords', 'user_id', 'admin_user', 'user_id'),
        'user_id', $installer->getTable('admin_user'), 'user_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Enterprise Admin Passwords');
$installer->getConnection()->createTable($table);

$tableAdmins     = $installer->getTable('admin_user');

$installer->getConnection()->changeColumn($tableAdmins, 'password', 'password', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length'    => 100,
    'comment'   => 'User Password'
));

$installer->getConnection()->addColumn($tableAdmins, 'failures_num', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
    'nullable'  => true,
    'default'   => 0,
    'comment'   => 'Failure Number'
));

$installer->getConnection()->addColumn($tableAdmins, 'first_failure', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
    'comment'   => 'First Failure'
));

$installer->getConnection()->addColumn($tableAdmins, 'lock_expires', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
    'comment'   => 'Expiration Lock Dates'
));

$installer->endSetup();
