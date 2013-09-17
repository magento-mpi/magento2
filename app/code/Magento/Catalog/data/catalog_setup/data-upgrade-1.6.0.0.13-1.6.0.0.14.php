<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright  {copyright}
 * @license    {license_link}
 */
/** @var $installer Magento_Catalog_Model_Resource_Setup */
$installer = $this;
$connection = $installer->getConnection();

$installer->startSetup();

$entityTypeId = $installer->getEntityTypeId(Magento_Catalog_Model_Category::ENTITY);
$attributeId = $installer->getAttributeId($entityTypeId, 'filter_price_range');
$attributeTableOld = $installer->getAttributeTable($entityTypeId, $attributeId);

$installer->updateAttribute($entityTypeId, $attributeId, 'backend_type', 'decimal');

$attributeTableNew = $installer->getAttributeTable($entityTypeId, $attributeId);

if ($attributeTableOld != $attributeTableNew) {
    $connection->disableTableKeys($attributeTableOld)
        ->disableTableKeys($attributeTableNew);

    $select = $connection->select()
        ->from($attributeTableOld, array('entity_type_id', 'attribute_id', 'store_id', 'entity_id', 'value'))
        ->where('entity_type_id = ?', $entityTypeId)
        ->where('attribute_id = ?', $attributeId);

    $query = $select->insertFromSelect($attributeTableNew,
        array('entity_type_id', 'attribute_id', 'store_id', 'entity_id', 'value')
    );

    $connection->query($query);

    $connection->delete($attributeTableOld,
        $connection->quoteInto('entity_type_id = ?', $entityTypeId)
            . $connection->quoteInto(' AND attribute_id = ?', $attributeId)
    );

    $connection->enableTableKeys($attributeTableOld)
        ->enableTableKeys($attributeTableNew);
}

Mage::getModel('Magento_Index_Model_Indexer')
    ->getProcessByCode(Magento_Catalog_Helper_Category_Flat::CATALOG_CATEGORY_FLAT_PROCESS_CODE)
    ->changeStatus(Magento_Index_Model_Process::STATUS_REQUIRE_REINDEX);

$installer->endSetup();
