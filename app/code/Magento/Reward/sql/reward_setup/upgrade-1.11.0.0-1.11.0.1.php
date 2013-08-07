<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reward
 * @copyright  {copyright}
 * @license    {license_link}
 */

/** @var $installer Magento_Reward_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();
$installer->getConnection()->changeColumn(
    $installer->getTable('magento_reward_history'),
    'created_at',
    'created_at',
    array(
        'type'     => Magento_DB_Ddl_Table::TYPE_TIMESTAMP,
        'nullable' => false,
        'default'  => Magento_DB_Ddl_Table::TIMESTAMP_INIT,
    )
);
$installer->endSetup();
