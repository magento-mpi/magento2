<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Oauth
 * @copyright  {copyright}
 * @license    {license_link}
 */
/**
 * Installation of OAuth module tables
 */
/** @var $install Magento_Oauth_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

/** @var $adapter Magento_DB_Adapter_Pdo_Mysql */
$adapter = $installer->getConnection();

/**
 * Create table 'oauth_consumer'
 */
$table = $adapter->newTable($installer->getTable('oauth_consumer'))
    ->addColumn('entity_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary'  => true,
        ), 'Entity Id')
    ->addColumn('created_at', Magento_DB_Ddl_Table::TYPE_TIMESTAMP, null, array(
            'nullable' => false,
            'default'  => Magento_DB_Ddl_Table::TIMESTAMP_INIT
        ), 'Created At')
    ->addColumn('updated_at', Magento_DB_Ddl_Table::TYPE_TIMESTAMP, null, array(
            'nullable' => true
        ), 'Updated At')
    ->addColumn('name', Magento_DB_Ddl_Table::TYPE_TEXT, 255, array(
            'nullable' => false
        ), 'Name of consumer')
    ->addColumn('key', Magento_DB_Ddl_Table::TYPE_TEXT, Magento_Oauth_Model_Consumer::KEY_LENGTH, array(
            'nullable' => false
        ), 'Key code')
    ->addColumn('secret', Magento_DB_Ddl_Table::TYPE_TEXT, Magento_Oauth_Model_Consumer::SECRET_LENGTH, array(
            'nullable' => false
        ), 'Secret code')
    ->addColumn('callback_url', Magento_DB_Ddl_Table::TYPE_TEXT, 255, array(), 'Callback URL')
    ->addColumn('rejected_callback_url', Magento_DB_Ddl_Table::TYPE_TEXT, 255, array(
            'nullable'  => false
        ), 'Rejected callback URL')
    ->addIndex(
        $installer->getIdxName(
            $installer->getTable('oauth_consumer'),
            array('key'),
            Magento_DB_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('key'),
        array('type' => Magento_DB_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex(
        $installer->getIdxName(
            $installer->getTable('oauth_consumer'),
            array('secret'),
            Magento_DB_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('secret'),
        array('type' => Magento_DB_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName('oauth_consumer', array('created_at')), array('created_at'))
    ->addIndex($installer->getIdxName('oauth_consumer', array('updated_at')), array('updated_at'))
    ->setComment('OAuth Consumers');
$adapter->createTable($table);

/**
 * Create table 'oauth_token'
 */
$table = $adapter->newTable($installer->getTable('oauth_token'))
    ->addColumn('entity_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
            'identity' => true, 'unsigned' => true, 'nullable' => false, 'primary'  => true,
        ), 'Entity ID')
    ->addColumn('consumer_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => true,
            'nullable' => false
        ), 'Consumer ID')
    ->addColumn('admin_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => true,
            'nullable' => true
        ), 'Admin user ID')
    ->addColumn('customer_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => true,
            'nullable' => true
        ), 'Customer user ID')
    ->addColumn('type', Magento_DB_Ddl_Table::TYPE_TEXT, 16, array(
            'nullable' => false
        ), 'Token Type')
    ->addColumn('token', Magento_DB_Ddl_Table::TYPE_TEXT, Magento_Oauth_Model_Token::LENGTH_TOKEN, array(
            'nullable' => false
        ), 'Token')
    ->addColumn('secret', Magento_DB_Ddl_Table::TYPE_TEXT, Magento_Oauth_Model_Token::LENGTH_SECRET, array(
            'nullable' => false
        ), 'Token Secret')
    ->addColumn('verifier', Magento_DB_Ddl_Table::TYPE_TEXT, Magento_Oauth_Model_Token::LENGTH_VERIFIER, array(
            'nullable' => true
        ), 'Token Verifier')
    ->addColumn('callback_url', Magento_DB_Ddl_Table::TYPE_TEXT, 255, array(
            'nullable' => false
        ), 'Token Callback URL')
    ->addColumn('revoked', Magento_DB_Ddl_Table::TYPE_SMALLINT, null, array(
            'unsigned' => true,
            'nullable' => false,
            'default'  => 0,
        ), 'Is Token revoked')
    ->addColumn('authorized', Magento_DB_Ddl_Table::TYPE_SMALLINT, null, array(
            'unsigned' => true,
            'nullable' => false,
            'default'  => 0,
        ), 'Is Token authorized')
    ->addColumn('created_at', Magento_DB_Ddl_Table::TYPE_TIMESTAMP, null, array(
            'nullable' => false,
            'default'  => Magento_DB_Ddl_Table::TIMESTAMP_INIT
        ), 'Token creation timestamp')
    ->addIndex(
        $installer->getIdxName(
            $installer->getTable('oauth_token'),
            array('consumer_id'),
            Magento_DB_Adapter_Interface::INDEX_TYPE_INDEX
        ),
        array('consumer_id'),
        array('type' => Magento_DB_Adapter_Interface::INDEX_TYPE_INDEX))
    ->addIndex(
        $installer->getIdxName(
            $installer->getTable('oauth_token'),
            array('token'),
            Magento_DB_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('token'),
        array('type' => Magento_DB_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addForeignKey(
        $installer->getFkName('oauth_token', 'admin_id', $installer->getTable('admin_user'), 'user_id'),
        'admin_id',
        $installer->getTable('admin_user'),
        'user_id',
        Magento_DB_Ddl_Table::ACTION_CASCADE,
        Magento_DB_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName('oauth_token', 'consumer_id', $installer->getTable('oauth_consumer'), 'entity_id'),
        'consumer_id',
        $installer->getTable('oauth_consumer'),
        'entity_id',
        Magento_DB_Ddl_Table::ACTION_CASCADE,
        Magento_DB_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName('oauth_token', 'customer_id', $installer->getTable('customer_entity'), 'entity_id'),
        'customer_id',
        $installer->getTable('customer_entity'),
        'entity_id',
        Magento_DB_Ddl_Table::ACTION_CASCADE,
        Magento_DB_Ddl_Table::ACTION_CASCADE)
    ->setComment('OAuth Tokens');
$adapter->createTable($table);

/**
 * Create table 'oauth_nonce
 */
$table = $adapter->newTable($installer->getTable('oauth_nonce'))
    ->addColumn('nonce', Magento_DB_Ddl_Table::TYPE_TEXT, 32, array(
        'nullable' => false
        ), 'Nonce String')
    ->addColumn('timestamp', Magento_DB_Ddl_Table::TYPE_INTEGER, 10, array(
            'unsigned' => true,
            'nullable' => false
        ), 'Nonce Timestamp')
    ->addIndex(
        $installer->getIdxName(
            $installer->getTable('oauth_nonce'),
            array('nonce'),
            Magento_DB_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('nonce'),
        array('type' => Magento_DB_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->setOption('type', 'MyISAM');
$adapter->createTable($table);

$installer->endSetup();
