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
 * @package     Mage_GiftMessage
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/* @var $installer Mage_GiftMessage_Model_Resource_Setup */

$installer = $this;

$installer->startSetup();

/**
 * Create table 'gift_message'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('sitemap/gift_message'))
    ->addColumn('gift_message_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'GiftMessage Id')
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Customer id')
    ->addColumn('sender', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Sender')
    ->addColumn('recipient', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Recipient')
    ->addColumn('message', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        ), 'Message')
    ->setComment('Gift Message');

$installer->getConnection()->createTable($table);

$installer->addAttribute('quote',        'gift_message_id', array('type' => 'int', 'visible' => false, 'required' => false))
    ->addAttribute('quote_address',      'gift_message_id', array('type' => 'int', 'visible' => false, 'required' => false))
    ->addAttribute('quote_item',         'gift_message_id', array('type' => 'int', 'visible' => false, 'required' => false))
    ->addAttribute('quote_address_item', 'gift_message_id', array('type' => 'int', 'visible' => false, 'required' => false))
    ->addAttribute('order',              'gift_message_id', array('type' => 'int', 'visible' => false, 'required' => false))
    ->addAttribute('order_item',         'gift_message_id', array('type' => 'int', 'visible' => false, 'required' => false))
    ->addAttribute('order_item',         'gift_message_available', array('type' => 'int', 'visible' => false, 'required' => false))
    ->addAttribute('catalog_product',    'gift_message_available', array(
        'backend'       => 'giftmessage/entity_attribute_backend_boolean_config',
        'frontend'      => '',
        'label'         => 'Allow Gift Message',
        'input'         => 'select',
        'class'         => '',
        'source'        => 'giftmessage/entity_attribute_source_boolean_config',
        'global'        => true,
        'visible'       => true,
        'required'      => false,
        'user_defined'  => false,
        'default'       => '2',
        'is_configurable'  => 0,
        'visible_on_front' => false
    ))
    ->removeAttribute('catalog_product', 'gift_message_aviable')
    ->setConfigData('sales/gift_messages/allow', 1);

$installer->endSetup();
