<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pci
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer Magento_Pci_Model_Resource_Setup */

$installer = $this;
$installer->startSetup();

/**
 * Create table 'enterprise_admin_passwords'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('enterprise_admin_passwords'))
    ->addColumn('password_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Password Id')
    ->addColumn('user_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'User Id')
    ->addColumn('password_hash', Magento_DB_Ddl_Table::TYPE_TEXT, 100, array(
        ), 'Password Hash')
    ->addColumn('expires', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Expires')
    ->addColumn('last_updated', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Last Updated')
    ->addIndex($installer->getIdxName('enterprise_admin_passwords', array('user_id')),
        array('user_id'))
    ->addForeignKey($installer->getFkName('enterprise_admin_passwords', 'user_id', 'admin_user', 'user_id'),
        'user_id', $installer->getTable('admin_user'), 'user_id',
        Magento_DB_Ddl_Table::ACTION_CASCADE, Magento_DB_Ddl_Table::ACTION_CASCADE)
    ->setComment('Enterprise Admin Passwords');
$installer->getConnection()->createTable($table);

$tableAdmins     = $installer->getTable('admin_user');
$tableApiUsers   = $installer->getTable('api_user');

$installer->getConnection()->changeColumn($tableAdmins, 'password', 'password', array(
    'type'      => Magento_DB_Ddl_Table::TYPE_TEXT,
    'length'    => 100,
    'comment'   => 'User Password'
));

$installer->getConnection()->changeColumn($tableApiUsers, 'api_key', 'api_key', array(
    'type'      => Magento_DB_Ddl_Table::TYPE_TEXT,
    'length'    => 100,
    'comment'   => 'Api key'
));

$installer->getConnection()->addColumn($tableAdmins, 'failures_num', array(
    'type'      => Magento_DB_Ddl_Table::TYPE_SMALLINT,
    'nullable'  => true,
    'default'   => 0,
    'comment'   => 'Failure Number'
));

$installer->getConnection()->addColumn($tableAdmins, 'first_failure', array(
    'type'      => Magento_DB_Ddl_Table::TYPE_TIMESTAMP,
    'comment'   => 'First Failure'
));

$installer->getConnection()->addColumn($tableAdmins, 'lock_expires', array(
    'type'      => Magento_DB_Ddl_Table::TYPE_TIMESTAMP,
    'comment'   => 'Expiration Lock Dates'
));

$installer->endSetup();
