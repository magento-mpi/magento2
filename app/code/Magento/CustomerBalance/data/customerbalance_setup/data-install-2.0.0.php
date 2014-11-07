<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer \Magento\Sales\Model\Resource\Setup */
$installer = $this;

// Modify Sales Entities
//  0.0.5 => 0.0.6
// Renamed: base_customer_balance_amount_used => base_customer_bal_amount_used
$installer->addAttribute('quote', 'customer_balance_amount_used', array('type' => 'decimal'));
$installer->addAttribute('quote', 'base_customer_bal_amount_used', array('type' => 'decimal'));


$installer->addAttribute('quote_address', 'base_customer_balance_amount', array('type' => 'decimal'));
$installer->addAttribute('quote_address', 'customer_balance_amount', array('type' => 'decimal'));

$installer->addAttribute('order', 'base_customer_balance_amount', array('type' => 'decimal'));
$installer->addAttribute('order', 'customer_balance_amount', array('type' => 'decimal'));

$installer->addAttribute('order', 'base_customer_balance_invoiced', array('type' => 'decimal'));
$installer->addAttribute('order', 'customer_balance_invoiced', array('type' => 'decimal'));

$installer->addAttribute('order', 'base_customer_balance_refunded', array('type' => 'decimal'));
$installer->addAttribute('order', 'customer_balance_refunded', array('type' => 'decimal'));

$installer->addAttribute('invoice', 'base_customer_balance_amount', array('type' => 'decimal'));
$installer->addAttribute('invoice', 'customer_balance_amount', array('type' => 'decimal'));

$installer->addAttribute('creditmemo', 'base_customer_balance_amount', array('type' => 'decimal'));
$installer->addAttribute('creditmemo', 'customer_balance_amount', array('type' => 'decimal'));

// 0.0.6 => 0.0.7
$installer->addAttribute('quote', 'use_customer_balance', array('type' => 'integer'));

// 0.0.9 => 0.0.10
// Renamed: base_customer_balance_total_refunded    => bs_customer_bal_total_refunded
// Renamed: length: customer_balance_total_refunded => customer_bal_total_refunded
$installer->addAttribute('creditmemo', 'bs_customer_bal_total_refunded', array('type' => 'decimal'));
$installer->addAttribute('creditmemo', 'customer_bal_total_refunded', array('type' => 'decimal'));

$installer->addAttribute('order', 'bs_customer_bal_total_refunded', array('type' => 'decimal'));
$installer->addAttribute('order', 'customer_bal_total_refunded', array('type' => 'decimal'));
