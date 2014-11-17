<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer \Magento\Sales\Model\Resource\Setup */
$installer = $this;

$installer->startSetup();
// 0.0.1 => 0.0.2
$installer->addAttribute('quote', 'gift_cards', array('type' => 'text'));

// 0.0.2 => 0.0.3
$installer->addAttribute('quote', 'gift_cards_amount', array('type' => 'decimal'));
$installer->addAttribute('quote', 'base_gift_cards_amount', array('type' => 'decimal'));

$installer->addAttribute('quote_address', 'gift_cards_amount', array('type' => 'decimal'));
$installer->addAttribute('quote_address', 'base_gift_cards_amount', array('type' => 'decimal'));

$installer->addAttribute('quote', 'gift_cards_amount_used', array('type' => 'decimal'));
$installer->addAttribute('quote', 'base_gift_cards_amount_used', array('type' => 'decimal'));

// 0.0.3 => 0.0.4
$installer->addAttribute('quote_address', 'gift_cards', array('type' => 'text'));

// 0.0.4 => 0.0.5
$installer->addAttribute('order', 'gift_cards', array('type' => 'text'));
$installer->addAttribute('order', 'base_gift_cards_amount', array('type' => 'decimal'));
$installer->addAttribute('order', 'gift_cards_amount', array('type' => 'decimal'));

// 0.0.5 => 0.0.6
$installer->addAttribute('quote_address', 'used_gift_cards', array('type' => 'text'));

// 0.0.9 => 0.0.9
$installer->addAttribute('order', 'base_gift_cards_invoiced', array('type' => 'decimal'));
$installer->addAttribute('order', 'gift_cards_invoiced', array('type' => 'decimal'));

$installer->addAttribute('invoice', 'base_gift_cards_amount', array('type' => 'decimal'));
$installer->addAttribute('invoice', 'gift_cards_amount', array('type' => 'decimal'));

// 0.0.11 => 0.0.12
$installer->addAttribute('order', 'base_gift_cards_refunded', array('type' => 'decimal'));
$installer->addAttribute('order', 'gift_cards_refunded', array('type' => 'decimal'));

$installer->addAttribute('creditmemo', 'base_gift_cards_amount', array('type' => 'decimal'));
$installer->addAttribute('creditmemo', 'gift_cards_amount', array('type' => 'decimal'));

$installer->endSetup();
