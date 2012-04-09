<?php
/**
 * {license}
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 */

/** @var $installer Enterprise_Reward_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();
$installer->getConnection()->changeColumn(
    $installer->getTable('enterprise_reward_history'),
    'created_at',
    'created_at',
    array(
        'type'     => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        'nullable' => false,
        'default'  => Varien_Db_Ddl_Table::TIMESTAMP_INIT,
    )
);
$installer->endSetup();
