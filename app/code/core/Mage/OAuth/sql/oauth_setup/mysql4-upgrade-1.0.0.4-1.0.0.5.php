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
/** @var $table Varien_Db_Ddl_Table */
$table = $adapter->newTable($installer->getTable('oauth/nonce'));

$table->addColumn('nonce', Varien_Db_Ddl_Table::TYPE_VARCHAR, 32, array('nullable' => false), 'Nonce String');
$table->addColumn('timestamp', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
    'unsigned' => true,
    'nullable' => false
), 'Nonce Timestamp');
$table->setOption('type', 'MyISAM');

$adapter->createTable($table);
