<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

/** @var $installer \Magento\Reward\Model\Resource\Setup */
$installer = $this;

$installer->getConnection()->addColumn(
    $installer->getTable('magento_reward_history'),
    'points_voided',
    array(
        'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        'comment'   => 'Points Voided',
        'after'     => 'points_used'
    )
);
