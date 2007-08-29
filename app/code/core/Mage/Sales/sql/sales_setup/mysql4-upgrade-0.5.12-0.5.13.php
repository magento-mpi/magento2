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

  
$this->addConfigField('sales/totals_sort', 'Checkout totals sort order');
$this->addConfigField('sales/totals_sort/subtotal', 'Subtotal', array(), 10);
$this->addConfigField('sales/totals_sort/discount', 'Discount', array(), 20);
$this->addConfigField('sales/totals_sort/shipping', 'Shipping', array(), 30);
$this->addConfigField('sales/totals_sort/tax', 'Tax', array(), 40);
$this->addConfigField('sales/totals_sort/grand_total', 'Grand Total', array(), 100);

$this->addAttribute('quote_address', 'subtotal', array('backend'=>'', 'frontend'=>''));
$this->addAttribute('quote_address', 'discount_amount', array('backend'=>'', 'frontend'=>''));
$this->addAttribute('quote_address', 'shipping_amount', array('backend'=>'', 'frontend'=>''));
$this->addAttribute('quote_address', 'tax_amount', array('backend'=>'', 'frontend'=>''));
$this->addAttribute('quote_address', 'grand_total', array('backend'=>'', 'frontend'=>''));
$this->addAttribute('quote_address', 'custbalance_amount', array('backend'=>'', 'frontend'=>''));

