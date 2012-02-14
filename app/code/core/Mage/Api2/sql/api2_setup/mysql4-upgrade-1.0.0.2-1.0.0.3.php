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
 * Delete unnecessary attribute, add new attributes and index
 */
/** @var $installer Mage_Api2_Model_Resource_Setup */
$installer = $this;
/** @var $adapter Varien_Db_Adapter_Pdo_Mysql */
$adapter = $installer->getConnection();

$table = $installer->getTable('api2/acl_rule');

// Delete 'permission' column
$adapter->dropColumn($table, 'permission');

// Add 'privilege' column
$adapter->addColumn($table, 'privilege', array(
    'comment'     => 'ACL Privilege',
    'nullable'    => true,
    'column_type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length'      => 20
));

$adapter->addIndex(
    $table,
    $installer->getIdxName($table,
        array('role_id', 'resource_id', 'privilege'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('role_id', 'resource_id', 'privilege'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);
