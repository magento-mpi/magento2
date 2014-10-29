<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Setup\Module\SetupModule */
$installer = $this;
$connection = $installer->getConnection();

$connection->addIndex(
    $installer->getTable('catalog_category_product_index_tmp'),
    $installer->getIdxName('catalog_category_product_index_tmp', array('product_id', 'category_id', 'store_id')),
    array('product_id', 'category_id', 'store_id')
);

$table = $installer->getTable('catalog_category_product_index_enbl_idx');
$connection->dropIndex($table, 'IDX_CATALOG_CATEGORY_PRODUCT_INDEX_ENBL_IDX_PRODUCT_ID');
$connection->addIndex(
    $table,
    $installer->getIdxName('catalog_category_product_index_enbl_idx', array('product_id', 'visibility')),
    array('product_id', 'visibility')
);


$table = $installer->getTable('catalog_category_product_index_enbl_tmp');
$connection->dropIndex($table, 'IDX_CATALOG_CATEGORY_PRODUCT_INDEX_ENBL_TMP_PRODUCT_ID');
$connection->addIndex(
    $table,
    $installer->getIdxName('catalog_category_product_index_enbl_tmp', array('product_id', 'visibility')),
    array('product_id', 'visibility')
);

$connection->addIndex(
    $installer->getTable('catalog_category_anc_products_index_idx'),
    $installer->getIdxName('catalog_category_anc_products_index_idx', array('category_id', 'product_id', 'position')),
    array('category_id', 'product_id', 'position')
);

$connection->addIndex(
    $installer->getTable('catalog_category_anc_products_index_tmp'),
    $installer->getIdxName('catalog_category_anc_products_index_tmp', array('category_id', 'product_id', 'position')),
    array('category_id', 'product_id', 'position')
);

$connection->addIndex(
    $installer->getTable('catalog_category_anc_categs_index_idx'),
    $installer->getIdxName('catalog_category_anc_categs_index_idx', array('path', 'category_id')),
    array('path', 'category_id')
);

$connection->addIndex(
    $installer->getTable('catalog_category_anc_categs_index_tmp'),
    $installer->getIdxName('catalog_category_anc_categs_index_tmp', array('path', 'category_id')),
    array('path', 'category_id')
);
