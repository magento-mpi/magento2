<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reward
 * @copyright  {copyright}
 * @license    {license_link}
 */

/** @var $installer \Magento\Reward\Model\Resource\Setup */
$installer = $this;
$installer->startSetup();
$installer->getConnection()->changeColumn(
    $installer->getTable('magento_reward_history'),
    'created_at',
    'created_at',
    array(
        'type'     => \Magento\DB\Ddl\Table::TYPE_TIMESTAMP,
        'nullable' => false,
        'default'  => \Magento\DB\Ddl\Table::TIMESTAMP_INIT,
    )
);
$installer->endSetup();
