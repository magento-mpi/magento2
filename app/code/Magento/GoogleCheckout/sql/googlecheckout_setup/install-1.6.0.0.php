<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Magento_GoogleCheckout_Model_Resource_Setup */
$installer = $this;

/**
 * Prepare database for tables setup
 */
$installer->startSetup();

/**
 * Create table 'googlecheckout_notification'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('googlecheckout_notification'))
    ->addColumn('serial_number', \Magento\DB\Ddl\Table::TYPE_TEXT, 64, array(
        'nullable'  => false,
        'primary'   => true,
        ), 'Serial Number')
    ->addColumn('started_at', \Magento\DB\Ddl\Table::TYPE_TIMESTAMP, null, array(
        ), 'Started At')
    ->addColumn('status', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Status')
    ->setComment('Google Checkout Notification Table');
$installer->getConnection()->createTable($table);

/**
 * Add 'disable_googlecheckout' attribute to the 'eav_attribute' table
 */
$installer->addAttribute(Magento_Catalog_Model_Product::ENTITY, 'enable_googlecheckout', array(
    'group'             => 'Prices',
    'type'              => 'int',
    'backend'           => '',
    'frontend'          => '',
    'label'             => 'Is Product Available for Purchase with Google Checkout',
    'input'             => 'select',
    'class'             => '',
    'source'            => 'Magento_Eav_Model_Entity_Attribute_Source_Boolean',
    'global'            => Magento_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible'           => true,
    'required'          => false,
    'user_defined'      => false,
    'default'           => '1',
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'visible_on_front'  => false,
    'unique'            => false,
    'apply_to'          => '',
    'is_configurable'   => false
));

/**
 * Prepare database after tables setup
 */
$installer->endSetup();
