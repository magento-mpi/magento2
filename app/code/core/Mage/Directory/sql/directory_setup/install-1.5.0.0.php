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
 * @package     Mage_Directory
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

/**
 * Create table 'directory/country'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('directory/country'))
    ->addColumn('country_id', Varien_Db_Ddl_Table::TYPE_TEXT, 2, array(
        'nullable'  => false,
        'primary'   => true,
        'default'   => '',
        ), 'country Id in iso-2')
    ->addColumn('iso2_code', Varien_Db_Ddl_Table::TYPE_TEXT, 2, array(
        'nullable'  => false,
        'default'   => '',
        ), 'country iso-2 format')
    ->addColumn('iso3_code', Varien_Db_Ddl_Table::TYPE_TEXT, 3, array(
        'nullable'  => false,
        'default'   => '',
        ), 'country iso-3')
    ->setComment('directory country');
$installer->getConnection()->createTable($table);

/**
 * Create table 'directory/country_format'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('directory/country_format'))
    ->addColumn('country_format_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'country format id')
    ->addColumn('country_id', Varien_Db_Ddl_Table::TYPE_TEXT, 2, array(
        'nullable'  => false,
        'default'   => '',
        ), 'country id in iso-2')
    ->addColumn('type', Varien_Db_Ddl_Table::TYPE_TEXT, 30, array(
        'nullable'  => false,
        'default'   => '',
        ), 'country format type')
    ->addColumn('format', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
        'nullable'  => false,
        ), 'country format')
    ->addIndex($installer->getIdxName('directory/country_format', array('country_id', 'type'), true),
        array('country_id', 'type'), array('unique' => true))
     ->setComment('directory country format');
$installer->getConnection()->createTable($table);

/**
 * Create table 'directory/country_region'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('directory/country_region'))
    ->addColumn('region_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'region id')
    ->addColumn('country_id', Varien_Db_Ddl_Table::TYPE_TEXT, 4, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'country id in iso-2')
    ->addColumn('code', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
        'nullable'  => false,
        'default'   => '',
        ), 'region code')
    ->addColumn('default_name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'region name')
    ->addIndex($installer->getIdxName('directory/country_region', array('country_id')),
        array('country_id'))
    ->setComment('directory country region');
$installer->getConnection()->createTable($table);

/**
 * Create table 'directory/country_region_name'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('directory/country_region_name'))
    ->addColumn('locale', Varien_Db_Ddl_Table::TYPE_TEXT, 8, array(
        'nullable'  => false,
        'primary'   => true,
        'default'   => '',
        ), 'locale')
    ->addColumn('region_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
        ), 'region Id')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
        'default'   => '',
        ), 'region name')
    ->addIndex($installer->getIdxName('directory/country_region_name', array('region_id')),
        array('region_id'))
    ->addForeignKey($installer->getFkName('directory/country_region_name', 'region_id', 'directory/country_region', 'region_id'),
        'region_id', $installer->getTable('directory/country_region'), 'region_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('directory country region name');
$installer->getConnection()->createTable($table);

/**
 * Create table 'directory/currency_rate'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('directory/currency_rate'))
    ->addColumn('currency_from', Varien_Db_Ddl_Table::TYPE_TEXT, 3, array(
        'nullable'  => false,
        'primary'   => true,
        'default'   => '',
        ), 'currency code convert from')
    ->addColumn('currency_to', Varien_Db_Ddl_Table::TYPE_TEXT, 3, array(
        'nullable'  => false,
        'primary'   => true,
        'default'   => '',
        ), 'currency code convert to')
    ->addColumn('rate', Varien_Db_Ddl_Table::TYPE_DECIMAL, '24,12', array(
        'nullable'  => false,
        'default'   => '0.000000000000',
        ), 'currency conversion rate')
    ->addIndex($installer->getIdxName('directory/currency_rate', array('currency_to')),
        array('currency_to'))
    ->setComment('directory currency rate');
$installer->getConnection()->createTable($table);

$installer->endSetup();