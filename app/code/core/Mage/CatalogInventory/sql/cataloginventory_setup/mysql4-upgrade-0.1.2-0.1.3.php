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
 * @package    Mage_CatalogInventory
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
$this->addConfigField('cataloginventory/options/min_qty', 'Minimum Qty for Items\' Status to be In Stock', array(
    'frontend_type'     => 'text',
    'show_in_website'   => 0,
    'show_in_store'     => 0,
), 0);
$this->addConfigField('cataloginventory/options/backorders', 'Backorders', array(
    'frontend_type'     => 'select',
    'source_model'      => 'cataloginventory/source_backorders',
    'show_in_website'   => 0,
    'show_in_store'     => 0,
), 0);

$this->addConfigField('cataloginventory/options/min_sale_qty', 'Minimum Qty Allowed in Shopping Cart', array(
    'frontend_type'     => 'text',
    'show_in_website'   => 0,
    'show_in_store'     => 0,
), 1);

$this->addConfigField('cataloginventory/options/max_sale_qty', 'Maximum Qty Allowed in Shopping Cart', array(
    'frontend_type'     => 'text',
    'show_in_website'   => 0,
    'show_in_store'     => 0,
), 10000);


$this->addConfigField('cataloginventory/options/can_subtract', 'Decrease Stock When Order is Placed', array(
    'frontend_type'     => 'select',
    'source_model'      => 'adminhtml/system_config_source_yesno',
    'show_in_website'   => 0,
    'show_in_store'     => 0,
), 0);
