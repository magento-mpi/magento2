<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

$installer = $this;
$tableName = $installer->getTable('catalog_product_index_eav_decimal');
$indexName = $installer->getConnection()->getPrimaryKeyName($tableName);


$tableNameTmp = $installer->getTable('catalog_product_index_eav_decimal_tmp');
$indexNameTmp = $installer->getConnection()->getPrimaryKeyName($tableNameTmp);

$fields = array('entity_id', 'attribute_id', 'store_id');

$installer->getConnection()
        ->addIndex($tableName, $indexName, $fields, Magento_DB_Adapter_Interface::INDEX_TYPE_PRIMARY);

$installer->getConnection()
        ->addIndex($tableNameTmp, $indexNameTmp, $fields, Magento_DB_Adapter_Interface::INDEX_TYPE_PRIMARY);

