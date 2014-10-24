<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $this Magento\Setup\Module\SetupModule */

$this->startSetup();

$this->getConnection()->dropForeignKey(
    $this->getTable('catalog_product_index_eav'),
    $this->getFkName('catalog_product_index_eav', 'attribute_id', 'eav_attribute', 'attribute_id')
);

$this->getConnection()->dropForeignKey(
    $this->getTable('catalog_product_index_eav'),
    $this->getFkName('catalog_product_index_eav', 'entity_id', 'catalog_product_entity', 'entity_id')
);

$this->getConnection()->dropForeignKey(
    $this->getTable('catalog_product_index_eav'),
    $this->getFkName('catalog_product_index_eav', 'store_id', 'store', 'store_id')
);

$this->getConnection()->dropForeignKey(
    $this->getTable('catalog_product_index_eav_decimal'),
    $this->getFkName('catalog_product_index_eav_decimal', 'attribute_id', 'eav_attribute', 'attribute_id')
);

$this->getConnection()->dropForeignKey(
    $this->getTable('catalog_product_index_eav_decimal'),
    $this->getFkName('catalog_product_index_eav_decimal', 'entity_id', 'catalog_product_entity', 'entity_id')
);

$this->getConnection()->dropForeignKey(
    $this->getTable('catalog_product_index_eav_decimal'),
    $this->getFkName('catalog_product_index_eav_decimal', 'store_id', 'store', 'store_id')
);

$this->endSetup();
