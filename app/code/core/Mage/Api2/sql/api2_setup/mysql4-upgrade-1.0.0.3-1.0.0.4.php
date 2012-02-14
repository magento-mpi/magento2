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
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Create table for ACL attributes
 */
/** @var $installer Mage_Api2_Model_Resource_Setup */
$installer = $this;
/** @var $adapter Varien_Db_Adapter_Pdo_Mysql */
$adapter = $installer->getConnection();

/**
 * Create table 'api2/acl_attribute'
 */
$table = $adapter->newTable($installer->getTable('api2/acl_attribute'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
        ), 'Entity ID')
    ->addColumn('user_type', Varien_Db_Ddl_Table::TYPE_VARCHAR,
        20, array('nullable'  => false), 'Type of user')
    ->addColumn('resource_id', Varien_Db_Ddl_Table::TYPE_VARCHAR,
        255, array('nullable'  => false), 'Resource ID')
    ->addColumn('operation', Varien_Db_Ddl_Table::TYPE_VARCHAR,
        20, array('nullable'  => false), 'Operation')
    ->addColumn('allowed_attributes', Varien_Db_Ddl_Table::TYPE_TEXT,
        null, array('nullable'  => true), 'Allowed attributes')
    ->addIndex($installer->getIdxName('api2/acl_attribute', array('user_type')), array('user_type'))
    ->setComment('Api2 Filter ACL Attributes');
$adapter->createTable($table);
