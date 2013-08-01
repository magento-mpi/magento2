<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @copyright  {copyright}
 * @license    {license_link}
 */

/** @var $installer Enterprise_Reward_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();
$installer->getConnection()->changeColumn(
    $installer->getTable('enterprise_reward_history'),
    'created_at',
    'created_at',
    array(
        'type'     => Magento_DB_Ddl_Table::TYPE_TIMESTAMP,
        'nullable' => false,
        'default'  => Magento_DB_Ddl_Table::TIMESTAMP_INIT,
    )
);
$installer->endSetup();
