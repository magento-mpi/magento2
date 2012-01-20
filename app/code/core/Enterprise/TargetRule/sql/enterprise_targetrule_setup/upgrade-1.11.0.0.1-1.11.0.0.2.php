<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer Enterprise_TargetRule_Model_Resource_Setup */
$installer = $this;
$connection = $installer->getConnection();

$installer->startSetup();

$targetRuleCustomerSegmentTable = $this->getTable('enterprise_targetrule_customersegment');
if ($connection->isTableExists($targetRuleCustomerSegmentTable)) {
    $connection->dropTable($targetRuleCustomerSegmentTable);
}

$connection->dropIndex(
    $installer->getTable('enterprise_targetrule'),
    $installer->getIdxName('enterprise_targetrule', array('use_customer_segment'))
);
$connection->dropColumn($installer->getTable('enterprise_targetrule'), 'use_customer_segment');

$targetRuleProductTable = $installer->getTable('enterprise_targetrule_product');
// recreate primary key to exclude column 'store_id'
$primaryIndexColumns = $primaryIndexKeyName = null;
foreach($connection->getIndexList($targetRuleProductTable) as $currentIndex) {
    if (strtolower($currentIndex['INDEX_TYPE']) == Varien_Db_Adapter_Interface::INDEX_TYPE_PRIMARY) {
        $primaryIndexKeyName = $currentIndex['KEY_NAME'];
        $primaryIndexColumns = $currentIndex['COLUMNS_LIST'];
        break;
    }
}
$connection->dropIndex($targetRuleProductTable, $primaryIndexKeyName);
if ($key = array_search('store_id', $primaryIndexColumns)) {
    unset($primaryIndexColumns[$key]);
    $primaryIndexColumns = array_values($primaryIndexColumns);
}
$connection->addIndex(
    $targetRuleProductTable, $primaryIndexKeyName, $primaryIndexColumns, Varien_Db_Adapter_Interface::INDEX_TYPE_PRIMARY
);

//drop column 'store_id'
$connection->dropIndex(
    $targetRuleProductTable,
    $installer->getIdxName('enterprise_targetrule_product', array('store_id'))
);
$connection->dropColumn($installer->getTable('enterprise_targetrule_product'), 'store_id');

$installer->endSetup();
