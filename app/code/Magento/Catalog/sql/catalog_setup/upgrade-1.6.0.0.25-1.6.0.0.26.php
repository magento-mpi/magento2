<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

/** @var $this \Magento\Setup\Module\SetupModule */
$connection = $this->getConnection();

$connection->dropForeignKey(
    $this->getTable('catalog_category_product_index'),
    $this->getFkName('catalog_category_product_index', 'category_id', 'catalog_category_entity', 'entity_id')
)->dropForeignKey(
    $this->getTable('catalog_category_product_index'),
    $this->getFkName('catalog_category_product_index', 'product_id', 'catalog_product_entity', 'entity_id')
)->dropForeignKey(
    $this->getTable('catalog_category_product_index'),
    $this->getFkName('catalog_category_product_index', 'store_id', 'store', 'store_id')
);

$connection->dropTable($this->getTable('catalog_product_enabled_index'));
$connection->dropTable($this->getTable('catalog_category_product_index_idx'));
$connection->dropTable($this->getTable('catalog_category_product_index_enbl_idx'));
$connection->dropTable($this->getTable('catalog_category_product_index_enbl_tmp'));
$connection->dropTable($this->getTable('catalog_category_anc_categs_index_idx'));
$connection->dropTable($this->getTable('catalog_category_anc_categs_index_tmp'));
$connection->dropTable($this->getTable('catalog_category_anc_products_index_idx'));
$connection->dropTable($this->getTable('catalog_category_anc_products_index_tmp'));
