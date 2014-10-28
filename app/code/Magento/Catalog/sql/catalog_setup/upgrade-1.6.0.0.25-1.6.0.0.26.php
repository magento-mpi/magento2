<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

/** @var Magento\Framework\DB\Adapter\Pdo\Mysql $connection */
$connection = $this->getConnection();

/** @var \Magento\Catalog\Model\Resource\Setup $this */
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
