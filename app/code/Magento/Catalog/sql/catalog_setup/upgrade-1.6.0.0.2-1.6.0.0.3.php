<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Setup\Module\SetupModule */
$installer = $this;
$tableName = $installer->getTable('catalog_product_index_eav_decimal');
$indexName = $installer->getConnection()->getPrimaryKeyName($tableName);


$tableNameTmp = $installer->getTable('catalog_product_index_eav_decimal_tmp');
$indexNameTmp = $installer->getConnection()->getPrimaryKeyName($tableNameTmp);

$fields = array('entity_id', 'attribute_id', 'store_id');

$installer->getConnection()->addIndex(
    $tableName,
    $indexName,
    $fields,
    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_PRIMARY
);

$installer->getConnection()->addIndex(
    $tableNameTmp,
    $indexNameTmp,
    $fields,
    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_PRIMARY
);
