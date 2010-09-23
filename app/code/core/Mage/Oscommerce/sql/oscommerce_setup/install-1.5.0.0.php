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
 * @package     Mage_Oscommerce
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Oscommerce install
 *
 * @category   Mage
 * @package    Mage_Oscommerce
 * @author     Magento Core Team <core@magentocommerce.com>
 */
$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

/**
 * Create table 'oscommerce/oscommerce'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('oscommerce/oscommerce'))
    ->addColumn('import_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Import Id')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Name')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Created At')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Updated At')
    ->addColumn('host', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
        ), 'Host')
    ->addColumn('port', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => true,
        ), 'Port')
    ->addColumn('db_name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Db Name')
    ->addColumn('db_user', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Db User')
    ->addColumn('db_password', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Db Password')
    ->addColumn('db_type', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
        ), 'Db Type')
    ->addColumn('table_prefix', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
        ), 'Table Prefix')
    ->addColumn('send_subscription', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Send Subscription')
    ->setComment('Oscommerce Import');
$installer->getConnection()->createTable($table);

/**
 * Create table 'oscommerce/oscommerce_type'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('oscommerce/oscommerce_type'))
    ->addColumn('type_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Type Id')
    ->addColumn('type_code', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
        'nullable'  => false,
        ), 'Type Code')
    ->addColumn('type_name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
        ), 'Type Name')
    ->setComment('Oscommerce Import Type');
$installer->getConnection()->createTable($table);

/**
 * Create table 'oscommerce/oscommerce_ref'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('oscommerce/oscommerce_ref'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Id')
    ->addColumn('import_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        ), 'Import Id')
    ->addColumn('type_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        ), 'Type Id')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        ), 'Value')
    ->addColumn('ref_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        ), 'Ref Id')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Created At')
    ->addColumn('user_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        ), 'User Id')
    ->setComment('Oscommerce Ref');
$installer->getConnection()->createTable($table);

/**
 * Create table 'oscommerce/oscommerce_order'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('oscommerce/oscommerce_order'))
    ->addColumn('osc_magento_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Osc Magento Id')
    ->addColumn('orders_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        ), 'Orders Id')
    ->addColumn('customers_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Customers Id')
    ->addColumn('magento_customers_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Magento Customers Id')
    ->addColumn('import_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Import Id')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Website Id')
    ->addColumn('customers_name', Varien_Db_Ddl_Table::TYPE_TEXT, 64, array(
        'nullable'  => false,
        'default'   => '',
        ), 'Customers Name')
    ->addColumn('customers_company', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
        ), 'Customers Company')
    ->addColumn('customers_street_address', Varien_Db_Ddl_Table::TYPE_TEXT, 64, array(
        'nullable'  => false,
        'default'   => '',
        ), 'Customers Street Address')
    ->addColumn('customers_suburb', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
        ), 'Customers Suburb')
    ->addColumn('customers_city', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
        'nullable'  => false,
        'default'   => '',
        ), 'Customers City')
    ->addColumn('customers_postcode', Varien_Db_Ddl_Table::TYPE_TEXT, 10, array(
        'nullable'  => false,
        'default'   => '',
        ), 'Customers Postcode')
    ->addColumn('customers_state', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
        ), 'Customers State')
    ->addColumn('customers_country', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
        'nullable'  => false,
        'default'   => '',
        ), 'Customers Country')
    ->addColumn('customers_telephone', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
        'nullable'  => false,
        'default'   => '',
        ), 'Customers Telephone')
    ->addColumn('customers_email_address', Varien_Db_Ddl_Table::TYPE_TEXT, 96, array(
        'nullable'  => false,
        'default'   => '',
        ), 'Customers Email Address')
    ->addColumn('customers_address_format_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Customers Address Format Id')
    ->addColumn('delivery_name', Varien_Db_Ddl_Table::TYPE_TEXT, 64, array(
        'nullable'  => false,
        'default'   => '',
        ), 'Delivery Name')
    ->addColumn('delivery_company', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
        ), 'Delivery Company')
    ->addColumn('delivery_street_address', Varien_Db_Ddl_Table::TYPE_TEXT, 64, array(
        'nullable'  => false,
        'default'   => '',
        ), 'Delivery Street Address')
    ->addColumn('delivery_suburb', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
        ), 'Delivery Suburb')
    ->addColumn('delivery_city', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
        'nullable'  => false,
        'default'   => '',
        ), 'Delivery City')
    ->addColumn('delivery_postcode', Varien_Db_Ddl_Table::TYPE_TEXT, 10, array(
        'nullable'  => false,
        'default'   => '',
        ), 'Delivery Postcode')
    ->addColumn('delivery_state', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
        ), 'Delivery State')
    ->addColumn('delivery_country', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
        'nullable'  => false,
        'default'   => '',
        ), 'Delivery Country')
    ->addColumn('delivery_address_format_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Delivery Address Format Id')
    ->addColumn('billing_name', Varien_Db_Ddl_Table::TYPE_TEXT, 64, array(
        'nullable'  => false,
        'default'   => '',
        ), 'Billing Name')
    ->addColumn('billing_company', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
        ), 'Billing Company')
    ->addColumn('billing_street_address', Varien_Db_Ddl_Table::TYPE_TEXT, 64, array(
        'nullable'  => false,
        'default'   => '',
        ), 'Billing Street Address')
    ->addColumn('billing_suburb', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
        ), 'Billing Suburb')
    ->addColumn('billing_city', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
        'nullable'  => false,
        'default'   => '',
        ), 'Billing City')
    ->addColumn('billing_postcode', Varien_Db_Ddl_Table::TYPE_TEXT, 10, array(
        'nullable'  => false,
        'default'   => '',
        ), 'Billing Postcode')
    ->addColumn('billing_state', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
        ), 'Billing State')
    ->addColumn('billing_country', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
        'nullable'  => false,
        'default'   => '',
        ), 'Billing Country')
    ->addColumn('billing_address_format_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Billing Address Format Id')
    ->addColumn('payment_method', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
        'default'   => '',
        ), 'Payment Method')
    ->addColumn('cc_type', Varien_Db_Ddl_Table::TYPE_TEXT, 20, array(
        ), 'Cc Type')
    ->addColumn('cc_owner', Varien_Db_Ddl_Table::TYPE_TEXT, 64, array(
        ), 'Cc Owner')
    ->addColumn('cc_number', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
        ), 'Cc Number')
    ->addColumn('cc_expires', Varien_Db_Ddl_Table::TYPE_TEXT, 4, array(
        ), 'Cc Expires')
    ->addColumn('last_modified', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Last Modified')
    ->addColumn('date_purchased', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Date Purchased')
    ->addColumn('orders_status', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
        ), 'Orders Status')
    ->addColumn('orders_date_finished', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Orders Date Finished')
    ->addColumn('currency', Varien_Db_Ddl_Table::TYPE_TEXT, 3, array(
        ), 'Currency')
    ->addColumn('currency_value', Varien_Db_Ddl_Table::TYPE_DECIMAL, '14,6', array(
        ), 'Currency Value')
    ->addColumn('currency_symbol', Varien_Db_Ddl_Table::TYPE_TEXT, 3, array(
        ), 'Currency Symbol')
    ->addColumn('orders_total', Varien_Db_Ddl_Table::TYPE_DECIMAL, '14,6', array(
        ), 'Orders Total')
    ->addIndex($installer->getIdxName('oscommerce/oscommerce_order', array('customers_id')),
        array('customers_id'))
    ->setComment('Oscommerce Orders');
$installer->getConnection()->createTable($table);

/**
 * Create table 'oscommerce/oscommerce_order_products'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('oscommerce/oscommerce_order_products'))
    ->addColumn('orders_products_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Orders Products Id')
    ->addColumn('osc_magento_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Osc Magento Id')
    ->addColumn('products_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Products Id')
    ->addColumn('products_model', Varien_Db_Ddl_Table::TYPE_TEXT, 12, array(
        ), 'Products Model')
    ->addColumn('products_name', Varien_Db_Ddl_Table::TYPE_TEXT, 64, array(
        'nullable'  => false,
        'default'   => '',
        ), 'Products Name')
    ->addColumn('products_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '15,4', array(
        'nullable'  => false,
        'default'   => '0.0000',
        ), 'Products Price')
    ->addColumn('final_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '15,4', array(
        'nullable'  => false,
        'default'   => '0.0000',
        ), 'Final Price')
    ->addColumn('products_tax', Varien_Db_Ddl_Table::TYPE_DECIMAL, '7,4', array(
        'nullable'  => false,
        'default'   => '0.0000',
        ), 'Products Tax')
    ->addColumn('products_quantity', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Products Quantity')
    ->addIndex($installer->getIdxName('oscommerce/oscommerce_order_products', array('osc_magento_id')),
        array('osc_magento_id'))
    ->addIndex($installer->getIdxName('oscommerce/oscommerce_order_products', array('products_id')),
        array('products_id'))
    ->setComment('Oscommerce Orders Products');
$installer->getConnection()->createTable($table);

/**
 * Create table 'oscommerce/oscommerce_order_total'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('oscommerce/oscommerce_order_total'))
    ->addColumn('orders_total_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Orders Total Id')
    ->addColumn('osc_magento_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Osc Magento Id')
    ->addColumn('title', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
        'default'   => '',
        ), 'Title')
    ->addColumn('text', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
        'default'   => '',
        ), 'Text')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_DECIMAL, '15,4', array(
        'nullable'  => false,
        'default'   => '0.0000',
        ), 'Value')
    ->addColumn('class', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
        'nullable'  => false,
        'default'   => '',
        ), 'Class')
    ->addColumn('sort_order', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Sort Order')
    ->addIndex($installer->getIdxName('oscommerce/oscommerce_order_total', array('osc_magento_id')),
        array('osc_magento_id'))
    ->setComment('Oscommerce Orders Total');
$installer->getConnection()->createTable($table);

/**
 * Create table 'oscommerce/oscommerce_order_history'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('oscommerce/oscommerce_order_history'))
    ->addColumn('orders_status_history_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Orders Status History Id')
    ->addColumn('osc_magento_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Osc Magento Id')
    ->addColumn('orders_status_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Orders Status Id')
    ->addColumn('date_added', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        ), 'Date Added')
    ->addColumn('customer_notified', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'default'   => '0',
        ), 'Customer Notified')
    ->addColumn('comments', Varien_Db_Ddl_Table::TYPE_TEXT, '2M', array(
        ), 'Comments')
    ->addColumn('orders_status', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
        ), 'Orders Status')
    ->addIndex($installer->getIdxName('oscommerce/oscommerce_order_history', array('osc_magento_id')),
        array('osc_magento_id'))
    ->setComment('Oscommerce Orders Status History');
$installer->getConnection()->createTable($table);

$installer->endSetup();
 
