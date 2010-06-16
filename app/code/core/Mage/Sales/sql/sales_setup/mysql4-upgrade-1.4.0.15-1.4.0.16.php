<?php
$installer = $this;
$installer->getConnection()->addColumn($installer->getTable('sales/order_item'), 'tax_canceled', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/order_item'), 'hidden_tax_canceled', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/order_item'), 'tax_refunded', 'decimal(12,4) NULL');