<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reminder
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Magento_Core_Model_Resource_Setup */
$installer = $this;

$ruleTable  = $installer->getTable('magento_reminder_rule');
$ruleWebsiteTable = $installer->getTable('magento_reminder_rule_website');
$coreWebsiteTable = $installer->getTable('core_website');
$connection = $installer->getConnection();

$installer->startSetup();

$connection->changeColumn(
    $ruleTable,
    'active_from',
    'from_date',
    array(
        'type'      => Magento_DB_Ddl_Table::TYPE_DATE,
        'nullable'  => true,
        'default'   => null
    )
);

$connection->changeColumn(
    $ruleTable,
    'active_to',
    'to_date',
    array(
        'type'      => Magento_DB_Ddl_Table::TYPE_DATE,
        'nullable'  => true,
        'default'   => null
    )
);


/**
 * Clean relations with not existing websites
 */
$selectWebsiteIds = $connection->select()
    ->from($coreWebsiteTable, 'website_id');
$websiteIds = $connection->fetchCol($selectWebsiteIds);
if (!empty($websiteIds)) {
    $connection->delete($ruleWebsiteTable, $connection->quoteInto('website_id NOT IN (?)', $websiteIds));
}

/**
 * Add foreign key for rule website table onto core website table
 */
$connection->addForeignKey(
    $installer->getFkName('magento_reminder_rule_website', 'website_id', 'core_website', 'website_id'),
    $ruleWebsiteTable,
    'website_id',
    $coreWebsiteTable,
    'website_id',
    Magento_DB_Ddl_Table::ACTION_CASCADE, Magento_DB_Ddl_Table::ACTION_CASCADE
);

$installer->endSetup();
