<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

/** @var $installer Magento\Setup\Module\SetupModule */
$installer = $this;
$installer->startSetup();
$installer->getConnection()->changeColumn(
    $installer->getTable('magento_reward_history'),
    'created_at',
    'created_at',
    array(
        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
        'nullable' => false,
        'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT
    )
);
$installer->endSetup();
