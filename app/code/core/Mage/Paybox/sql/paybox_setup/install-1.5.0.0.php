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
 * @package     Mage_Paybox
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var $installer Mage_Paybox_Model_Resource_Setup */
$installer = $this;

/**
 * Prepare database before module install
 */
$installer->startSetup();

/**
 * Create table 'paybox/question_number'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('paybox/question_number'))
    ->addColumn('account_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Account Id')
    ->addColumn('account_hash', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array(
        'nullable'  => false,
        ), 'Account Hash')
    ->addColumn('increment_value', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '1',
        ), 'Increment Value')
    ->addColumn('reset_date', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        'default'   => 'CURRENT_TIMESTAMP',
        ), 'Reset Date')
    ->setComment('Paybox Question Number Table');
$installer->getConnection()->createTable($table);

/**
 * Add attributes to the 'sales/order_payment' table
 */
$installer->addAttribute('order_payment', 'paybox_request_number', array());
$installer->addAttribute('order_payment', 'paybox_question_number', array());

/**
 * Prepare database after module install
 */
$installer->endSetup();
