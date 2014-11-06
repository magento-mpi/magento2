<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Sales\Model\Resource\Setup */
$installer = $this;

$installer->addAttribute('quote_item', 'weee_tax_applied', array('type' => 'text'));
$installer->addAttribute('quote_item', 'weee_tax_applied_amount', array('type' => 'decimal'));
$installer->addAttribute('quote_item', 'weee_tax_applied_row_amount', array('type' => 'decimal'));
$installer->addAttribute('quote_item', 'weee_tax_disposition', array('type' => 'decimal'));
$installer->addAttribute('quote_item', 'weee_tax_row_disposition', array('type' => 'decimal'));
$installer->addAttribute('quote_item', 'base_weee_tax_applied_amount', array('type' => 'decimal'));
$installer->addAttribute('quote_item', 'base_weee_tax_applied_row_amnt', array('type' => 'decimal'));
$installer->addAttribute('quote_item', 'base_weee_tax_disposition', array('type' => 'decimal'));
$installer->addAttribute('quote_item', 'base_weee_tax_row_disposition', array('type' => 'decimal'));

$installer->addAttribute('order_item', 'weee_tax_applied', array('type' => 'text'));
$installer->addAttribute('order_item', 'weee_tax_applied_amount', array('type' => 'decimal'));
$installer->addAttribute('order_item', 'weee_tax_applied_row_amount', array('type' => 'decimal'));
$installer->addAttribute('order_item', 'weee_tax_disposition', array('type' => 'decimal'));
$installer->addAttribute('order_item', 'weee_tax_row_disposition', array('type' => 'decimal'));
$installer->addAttribute('order_item', 'base_weee_tax_applied_amount', array('type' => 'decimal'));
$installer->addAttribute('order_item', 'base_weee_tax_applied_row_amnt', array('type' => 'decimal'));
$installer->addAttribute('order_item', 'base_weee_tax_disposition', array('type' => 'decimal'));
$installer->addAttribute('order_item', 'base_weee_tax_row_disposition', array('type' => 'decimal'));

$installer->addAttribute('invoice_item', 'weee_tax_applied', array('type' => 'text'));
$installer->addAttribute('invoice_item', 'weee_tax_applied_amount', array('type' => 'decimal'));
$installer->addAttribute('invoice_item', 'weee_tax_applied_row_amount', array('type' => 'decimal'));
$installer->addAttribute('invoice_item', 'weee_tax_disposition', array('type' => 'decimal'));
$installer->addAttribute('invoice_item', 'weee_tax_row_disposition', array('type' => 'decimal'));
$installer->addAttribute('invoice_item', 'base_weee_tax_applied_amount', array('type' => 'decimal'));
$installer->addAttribute('invoice_item', 'base_weee_tax_applied_row_amnt', array('type' => 'decimal'));
$installer->addAttribute('invoice_item', 'base_weee_tax_disposition', array('type' => 'decimal'));
$installer->addAttribute('invoice_item', 'base_weee_tax_row_disposition', array('type' => 'decimal'));

$installer->addAttribute('creditmemo_item', 'weee_tax_applied', array('type' => 'text'));
$installer->addAttribute('creditmemo_item', 'weee_tax_applied_amount', array('type' => 'decimal'));
$installer->addAttribute('creditmemo_item', 'weee_tax_applied_row_amount', array('type' => 'decimal'));
$installer->addAttribute('creditmemo_item', 'weee_tax_disposition', array('type' => 'decimal'));
$installer->addAttribute('creditmemo_item', 'weee_tax_row_disposition', array('type' => 'decimal'));
$installer->addAttribute('creditmemo_item', 'base_weee_tax_applied_amount', array('type' => 'decimal'));
$installer->addAttribute('creditmemo_item', 'base_weee_tax_applied_row_amnt', array('type' => 'decimal'));
$installer->addAttribute('creditmemo_item', 'base_weee_tax_disposition', array('type' => 'decimal'));
$installer->addAttribute('creditmemo_item', 'base_weee_tax_row_disposition', array('type' => 'decimal'));
