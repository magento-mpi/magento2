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
$connection->dropColumn($installer->getTable('enterprise_targetrule'), 'use_customer_segment');
$connection->dropColumn($installer->getTable('enterprise_targetrule_product'), 'store_id');

$installer->endSetup();
