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

/** @var $installer Mage_OAuth_Model_Resource_Setup */
$installer = $this;
/** @var $adapter Varien_Db_Adapter_Interface */
$adapter = $installer->getConnection();

$table = $installer->getTable('oauth/token');

$adapter->dropColumn($table, 'tmp_token');
$adapter->dropColumn($table, 'tmp_token_secret');
$adapter->dropColumn($table, 'tmp_verifier');
$adapter->dropColumn($table, 'tmp_callback_url');
$adapter->dropColumn($table, 'tmp_created_at');
$adapter->dropColumn($table, 'token');
$adapter->dropColumn($table, 'token_secret');
$adapter->dropColumn($table, 'is_revoked');

$adapter->addColumn($table, 'type', array(
    'comment'  => 'Token Type',
    'nullable' => false,
    'type'     => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length'   => 16
));
$adapter->addColumn($table, 'token', array(
    'comment'  => 'Token',
    'nullable' => false,
    'type'     => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length'   => Mage_OAuth_Model_Token::LENGTH_TOKEN
));
$adapter->addColumn($table, 'secret', array(
    'comment'  => 'Token Secret',
    'nullable' => false,
    'type'     => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length'   => Mage_OAuth_Model_Token::LENGTH_SECRET
));
$adapter->addColumn($table, 'verifier', array(
    'comment'  => 'Token Verifier',
    'nullable' => true,
    'type'     => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length'   => Mage_OAuth_Model_Token::LENGTH_VERIFIER
));
$adapter->addColumn($table, 'callback_url', array(
    'comment'  => 'Token Callback URL',
    'nullable' => false,
    'type'     => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length'   => 255
));
$adapter->addColumn($table, 'revoked', array(
    'comment'  => 'Is Token revoked',
    'unsigned' => true,
    'nullable' => false,
    'default'  => 0,
    'type'     => Varien_Db_Ddl_Table::TYPE_SMALLINT
));
$adapter->addColumn($table, 'authorized', array(
    'comment'  => 'Is Token authorized',
    'unsigned' => true,
    'nullable' => false,
    'default'  => 0,
    'type'     => Varien_Db_Ddl_Table::TYPE_SMALLINT
));
$adapter->addColumn($table, 'created_at', array(
    'comment'  => 'Token creation timestamp',
    'nullable' => false,
    'default'  => Varien_Db_Ddl_Table::TIMESTAMP_INIT,
    'type'     => Varien_Db_Ddl_Table::TYPE_TIMESTAMP
));
$adapter->addIndex(
    $table,
    $installer->getIdxName('oauth/token', array('token'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
    array('token'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);
