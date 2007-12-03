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
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$this->addConfigField('cataloginventory', 'Inventory', array(
	'frontend_type'=>'text',
	'sort_order'=>'102',
	'show_in_default'=>'1',
	'show_in_website'=>'0',
	'show_in_store'=>'0',
	), '');
$this->addConfigField('cataloginventory/options', 'Stock Options', array(
	'frontend_type'=>'text',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('cataloginventory/options/backorders', 'Backorders', array(
	'frontend_type'=>'select',
	'source_model'=>'cataloginventory/source_backorders',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'0',
	'show_in_store'=>'0',
	), '0');
$this->addConfigField('cataloginventory/options/can_subtract', 'Dicrease Stock on Order Place', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_yesno',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'0',
	'show_in_store'=>'0',
	), '0');
$this->addConfigField('cataloginventory/options/max_sale_qty', 'Maximum Shopping Cart Qty Allowed', array(
	'frontend_type'=>'text',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'0',
	'show_in_store'=>'0',
	), '10000');
$this->addConfigField('cataloginventory/options/min_qty', 'Minimum Qty for Items', array(
	'frontend_type'=>'text',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'0',
	'show_in_store'=>'0',
	), '0');
$this->addConfigField('cataloginventory/options/min_sale_qty', 'Minimum Shopping Cart Qty Allowed', array(
	'frontend_type'=>'text',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'0',
	'show_in_store'=>'0',
	), '1');
