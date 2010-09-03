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
 * @package     Mage_Strikeiron
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/* @var $installer Mage_Core_Model_Resource_Setup */

$installer = $this;

$installer->startSetup();

/**
 * Create table 'strikeiron_tax_rate'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('strikeiron_tax_rate'))
    ->addColumn('tax_rate_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
	    'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Taxrate Id')
    ->addColumn('tax_country_id', Varien_Db_Ddl_Table::TYPE_TEXT, 6, array(
        'nullable'  => true,
        ), 'Tax country Id')        
    ->addColumn('tax_region_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => true,
        ), 'Tax region Id')
    ->addColumn('tax_postcode', Varien_Db_Ddl_Table::TYPE_TEXT, 12, array(
        'nullable'  => true,
        ), 'Tax postcode')        
    ->addColumn('tax_value', Varien_Db_Ddl_Table::TYPE_DECIMAL, array(12,4), array(
        'nullable'  => false,
        'default'   => '0.0000',
        ), 'Tax value')        
    ->setComment('Strikeiron Tax Rate');

$installer->getConnection()->createTable($table);

$installer->endSetup();
