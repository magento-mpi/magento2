<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Magento_Tax_Model_Resource_Setup */
$installer = $this;

$connection = $installer->getConnection();
$adminRuleTable = $installer->getTable('admin_rule');
$aclRulesDelete = array(
    'Magento_Tax::classes_customer',
    'Magento_Tax::classes_product',
    'Magento_Tax::import_export',
    'Magento_Tax::tax_rates',
    'Magento_Tax::rules'
);

/**
 * Remove unneeded ACL rules
 */
$connection->delete(
    $adminRuleTable,
    $connection->quoteInto('resource_id IN (?)', $aclRulesDelete)
);

$connection->update(
    $adminRuleTable,
    array('resource_id' => 'Magento_Tax::manage_tax'),
    array('resource_id = ?' => 'Magento_Tax::sales_tax')
);
