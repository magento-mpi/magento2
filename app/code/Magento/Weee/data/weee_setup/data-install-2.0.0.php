<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $this \Magento\Sales\Model\Resource\Setup */

$this->addAttribute('quote_item', 'weee_tax_applied', array('type' => 'text'));
$this->addAttribute('quote_item', 'weee_tax_applied_amount', array('type' => 'decimal'));
$this->addAttribute('quote_item', 'weee_tax_applied_row_amount', array('type' => 'decimal'));
$this->addAttribute('quote_item', 'weee_tax_disposition', array('type' => 'decimal'));
$this->addAttribute('quote_item', 'weee_tax_row_disposition', array('type' => 'decimal'));
$this->addAttribute('quote_item', 'base_weee_tax_applied_amount', array('type' => 'decimal'));
$this->addAttribute('quote_item', 'base_weee_tax_applied_row_amnt', array('type' => 'decimal'));
$this->addAttribute('quote_item', 'base_weee_tax_disposition', array('type' => 'decimal'));
$this->addAttribute('quote_item', 'base_weee_tax_row_disposition', array('type' => 'decimal'));

$this->addAttribute('order_item', 'weee_tax_applied', array('type' => 'text'));
$this->addAttribute('order_item', 'weee_tax_applied_amount', array('type' => 'decimal'));
$this->addAttribute('order_item', 'weee_tax_applied_row_amount', array('type' => 'decimal'));
$this->addAttribute('order_item', 'weee_tax_disposition', array('type' => 'decimal'));
$this->addAttribute('order_item', 'weee_tax_row_disposition', array('type' => 'decimal'));
$this->addAttribute('order_item', 'base_weee_tax_applied_amount', array('type' => 'decimal'));
$this->addAttribute('order_item', 'base_weee_tax_applied_row_amnt', array('type' => 'decimal'));
$this->addAttribute('order_item', 'base_weee_tax_disposition', array('type' => 'decimal'));
$this->addAttribute('order_item', 'base_weee_tax_row_disposition', array('type' => 'decimal'));

$this->addAttribute('invoice_item', 'weee_tax_applied', array('type' => 'text'));
$this->addAttribute('invoice_item', 'weee_tax_applied_amount', array('type' => 'decimal'));
$this->addAttribute('invoice_item', 'weee_tax_applied_row_amount', array('type' => 'decimal'));
$this->addAttribute('invoice_item', 'weee_tax_disposition', array('type' => 'decimal'));
$this->addAttribute('invoice_item', 'weee_tax_row_disposition', array('type' => 'decimal'));
$this->addAttribute('invoice_item', 'base_weee_tax_applied_amount', array('type' => 'decimal'));
$this->addAttribute('invoice_item', 'base_weee_tax_applied_row_amnt', array('type' => 'decimal'));
$this->addAttribute('invoice_item', 'base_weee_tax_disposition', array('type' => 'decimal'));
$this->addAttribute('invoice_item', 'base_weee_tax_row_disposition', array('type' => 'decimal'));

$this->addAttribute('creditmemo_item', 'weee_tax_applied', array('type' => 'text'));
$this->addAttribute('creditmemo_item', 'weee_tax_applied_amount', array('type' => 'decimal'));
$this->addAttribute('creditmemo_item', 'weee_tax_applied_row_amount', array('type' => 'decimal'));
$this->addAttribute('creditmemo_item', 'weee_tax_disposition', array('type' => 'decimal'));
$this->addAttribute('creditmemo_item', 'weee_tax_row_disposition', array('type' => 'decimal'));
$this->addAttribute('creditmemo_item', 'base_weee_tax_applied_amount', array('type' => 'decimal'));
$this->addAttribute('creditmemo_item', 'base_weee_tax_applied_row_amnt', array('type' => 'decimal'));
$this->addAttribute('creditmemo_item', 'base_weee_tax_disposition', array('type' => 'decimal'));
$this->addAttribute('creditmemo_item', 'base_weee_tax_row_disposition', array('type' => 'decimal'));
