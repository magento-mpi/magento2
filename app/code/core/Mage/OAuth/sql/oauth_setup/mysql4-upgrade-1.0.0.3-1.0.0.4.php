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
/** @var $adapter Varien_Db_Adapter_Interface */
$adapter = $installer->getConnection();

$table = $installer->getTable('oauth/token');

$adapter->addColumn($table, 'customer_id', array(
    'comment'     => 'Customer user ID',
    'unsigned'    => true,
    'nullable'    => true,
    'column_type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
    'after'       => 'admin_id',
));
$adapter->addForeignKey(
    $installer->getFkName('oauth/token', 'customer_id', $installer->getTable('customer/entity'), 'entity_id'),
    $table,
    'customer_id',
    $installer->getTable('customer/entity'),
    'entity_id');
