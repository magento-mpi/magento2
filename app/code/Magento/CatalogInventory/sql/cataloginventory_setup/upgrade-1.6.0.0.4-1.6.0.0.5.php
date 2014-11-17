<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $this \Magento\Setup\Module\SetupModule */

$this->startSetup();

$this->getConnection()->dropForeignKey(
    $this->getTable('cataloginventory_stock_status'),
    $this->getFkName('cataloginventory_stock_status', 'stock_id', 'cataloginventory_stock', 'stock_id')
);

$this->getConnection()->dropForeignKey(
    $this->getTable('cataloginventory_stock_status'),
    $this->getFkName('cataloginventory_stock_status', 'product_id', 'catalog_product_entity', 'entity_id')
);

$this->getConnection()->dropForeignKey(
    $this->getTable('cataloginventory_stock_status'),
    $this->getFkName('cataloginventory_stock_status', 'website_id', 'store_website', 'website_id')
);

$this->endSetup();
