<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Framework\Module\Setup */
$installer = $this;

$ruleTable = $installer->getTable('magento_reminder_rule');
$ruleWebsiteTable = $installer->getTable('magento_reminder_rule_website');
$coreWebsiteTable = $installer->getTable('store_website');
$connection = $installer->getConnection();

$installer->startSetup();

$connection->changeColumn(
    $ruleTable,
    'active_from',
    'from_date',
    array('type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATE, 'nullable' => true, 'default' => null)
);

$connection->changeColumn(
    $ruleTable,
    'active_to',
    'to_date',
    array('type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATE, 'nullable' => true, 'default' => null)
);

/**
 * Add foreign key for rule website table onto core website table
 */
$connection->addForeignKey(
    $installer->getFkName('magento_reminder_rule_website', 'website_id', 'store_website', 'website_id'),
    $ruleWebsiteTable,
    'website_id',
    $coreWebsiteTable,
    'website_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
);

$installer->endSetup();
