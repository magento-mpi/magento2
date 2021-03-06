<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/* @var $installer \Magento\Sales\Model\Resource\Setup */
$installer = $this;

$installer->startSetup();
// 0.0.1 => 0.0.2
$installer->addAttribute('quote', 'gift_cards', ['type' => 'text']);

// 0.0.2 => 0.0.3
$installer->addAttribute('quote', 'gift_cards_amount', ['type' => 'decimal']);
$installer->addAttribute('quote', 'base_gift_cards_amount', ['type' => 'decimal']);

$installer->addAttribute('quote_address', 'gift_cards_amount', ['type' => 'decimal']);
$installer->addAttribute('quote_address', 'base_gift_cards_amount', ['type' => 'decimal']);

$installer->addAttribute('quote', 'gift_cards_amount_used', ['type' => 'decimal']);
$installer->addAttribute('quote', 'base_gift_cards_amount_used', ['type' => 'decimal']);

// 0.0.3 => 0.0.4
$installer->addAttribute('quote_address', 'gift_cards', ['type' => 'text']);

// 0.0.4 => 0.0.5
$installer->addAttribute('order', 'gift_cards', ['type' => 'text']);
$installer->addAttribute('order', 'base_gift_cards_amount', ['type' => 'decimal']);
$installer->addAttribute('order', 'gift_cards_amount', ['type' => 'decimal']);

// 0.0.5 => 0.0.6
$installer->addAttribute('quote_address', 'used_gift_cards', ['type' => 'text']);

// 0.0.9 => 0.0.9
$installer->addAttribute('order', 'base_gift_cards_invoiced', ['type' => 'decimal']);
$installer->addAttribute('order', 'gift_cards_invoiced', ['type' => 'decimal']);

$installer->addAttribute('invoice', 'base_gift_cards_amount', ['type' => 'decimal']);
$installer->addAttribute('invoice', 'gift_cards_amount', ['type' => 'decimal']);

// 0.0.11 => 0.0.12
$installer->addAttribute('order', 'base_gift_cards_refunded', ['type' => 'decimal']);
$installer->addAttribute('order', 'gift_cards_refunded', ['type' => 'decimal']);

$installer->addAttribute('creditmemo', 'base_gift_cards_amount', ['type' => 'decimal']);
$installer->addAttribute('creditmemo', 'gift_cards_amount', ['type' => 'decimal']);

$installer->endSetup();
