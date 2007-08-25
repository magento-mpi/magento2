<?php
  
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

