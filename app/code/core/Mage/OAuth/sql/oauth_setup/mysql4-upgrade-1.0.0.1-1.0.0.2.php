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
 * @package     Mage_OAuth
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
/**
 * Upgrade of OAuth module tables
 */

/** @var $installer Mage_OAuth_Model_Resource_Setup */
$installer = $this;
/** @var $adapter Varien_Db_Adapter_Pdo_Mysql */
$adapter = $installer->getConnection();

/**
 * Create table 'oauth/token'
 */
$table = $adapter->newTable($installer->getTable('oauth/token'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true, 'unsigned' => true, 'nullable' => false, 'primary'  => true,
    ), 'Entity ID')
    ->addColumn('consumer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null,
        array('unsigned' => true, 'nullable' => false),
        'Consumer ID')
    ->addColumn(
        'tmp_token', Varien_Db_Ddl_Table::TYPE_VARCHAR, 64, array('nullable' => false), 'Temporary Token'
    )
    ->addColumn(
        'tmp_token_secret', Varien_Db_Ddl_Table::TYPE_VARCHAR, 64,
        array('nullable' => false), 'Temporary token secret'
    )
    ->addColumn('tmp_verifier', Varien_Db_Ddl_Table::TYPE_VARCHAR, 64,
        array('nullable' => false), 'Verifier')
    ->addColumn('tmp_callback_url', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255,
        array('nullable' => true), 'Temporary callback URL')
    ->addColumn('tmp_created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable' => false, 'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT
    ), 'Temporary token created at')
    ->addColumn('token', Varien_Db_Ddl_Table::TYPE_VARCHAR, 64,
        array('nullable' => false), 'Permanent Token')
    ->addColumn('token_secret', Varien_Db_Ddl_Table::TYPE_VARCHAR, 64,
        array('nullable' => false), 'Permanent Secret')
    ->addColumn('is_revoked', Varien_Db_Ddl_Table::TYPE_SMALLINT, null,
        array('unsigned' => true, 'nullable' => false), 'Revoke status')
    ->addIndex(
        $installer->getIdxName('oauth/token', array('consumer_id'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        array('consumer_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX)
    )
    ->addIndex(
        $installer->getIdxName('oauth/token', array('tmp_token'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        array('tmp_token'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
    )
    ->addIndex(
        $installer->getIdxName('oauth/token', array('token'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        array('token'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
    )
    ->addForeignKey(
        $installer->getFkName('oauth/token', 'consumer_id', 'oauth/consumer', 'entity_id'),
        'consumer_id',
        $installer->getTable('oauth/consumer'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('OAuth Tokens');

$adapter->createTable($table);
