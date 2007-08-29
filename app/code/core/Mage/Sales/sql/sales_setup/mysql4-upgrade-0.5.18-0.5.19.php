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
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$this->addAttribute('quote_address_item', 'product_id', array('type'=>'int'));
$this->addAttribute('quote_address_item', 'super_product_id', array('type'=>'int'));
$this->addAttribute('quote_address_item', 'parent_product_id', array('type'=>'int'));
$this->addAttribute('quote_address_item', 'sku', array());
$this->addAttribute('quote_address_item', 'image', array());
$this->addAttribute('quote_address_item', 'name', array());
$this->addAttribute('quote_address_item', 'description', array('type'=>'text'));
$this->addAttribute('quote_address_item', 'weight', array('type'=>'decimal'));
$this->addAttribute('quote_address_item', 'free_shipping', array('type'=>'int'));
$this->addAttribute('quote_address_item', 'qty', array('type'=>'decimal'));
$this->addAttribute('quote_address_item', 'price', array('type'=>'decimal'));
$this->addAttribute('quote_address_item', 'discount_percent', array('type'=>'decimal'));
$this->addAttribute('quote_address_item', 'discount_amount', array('type'=>'decimal'));
$this->addAttribute('quote_address_item', 'tax_percent', array('type'=>'decimal'));
$this->addAttribute('quote_address_item', 'tax_amount', array('type'=>'decimal'));
$this->addAttribute('quote_address_item', 'row_total', array('type'=>'decimal'));
$this->addAttribute('quote_address_item', 'row_weight', array('type'=>'decimal'));
$this->addAttribute('quote_address_item', 'applied_rule_ids', array());