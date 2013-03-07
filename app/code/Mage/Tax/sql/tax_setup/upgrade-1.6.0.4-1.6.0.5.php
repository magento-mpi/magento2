<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Mage_Tax_Model_Resource_Setup */
$installer = $this;

$connection = $installer->getConnection();
$adminRuleTable = $installer->getTable('admin_rule');
$aclRulesDelete = array(
    'Mage_Tax::classes_customer',
    'Mage_Tax::classes_product',
    'Mage_Tax::import_export',
    'Mage_Tax::tax_rates',
    'Mage_Tax::rules'
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
    array('resource_id' => 'Mage_Tax::manage_tax'),
    array('resource_id = ?' => 'Mage_Tax::sales_tax')
);
