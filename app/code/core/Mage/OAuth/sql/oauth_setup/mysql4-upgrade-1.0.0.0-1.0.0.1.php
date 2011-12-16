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
$adapter = $installer->getConnection();

/**
 * Create table 'oauth/consumer'
 */
$table = $adapter->newTable($installer->getTable('oauth/consumer'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
        ), 'Entity Id')
    ->addColumn('key', Varien_Db_Ddl_Table::TYPE_VARCHAR,
        32, array('nullable'  => false), 'Key code')
    ->addColumn('secret', Varien_Db_Ddl_Table::TYPE_VARCHAR,
        64, array('nullable'  => false), 'Secret code')
    ->addColumn('call_back_url', Varien_Db_Ddl_Table::TYPE_VARCHAR,
        255, array('nullable'  => false), 'Call back URL')
    ->addIndex(
        $installer->getIdxName(
            'oauth/consumer',
            array('key'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('key'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex(
        $installer->getIdxName(
            'oauth/consumer',
            array('secret'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('secret'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->setComment('OAuth Consumers');
$adapter->createTable($table);
