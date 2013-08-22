<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reward
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Magento_Reward_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

/**
 * Create table 'magento_reward'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('magento_reward'))
    ->addColumn('reward_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Reward Id')
    ->addColumn('customer_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Customer Id')
    ->addColumn('website_id', Magento_DB_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        ), 'Website Id')
    ->addColumn('points_balance', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Points Balance')
    ->addColumn('website_currency_code', Magento_DB_Ddl_Table::TYPE_TEXT, 3, array(
        ), 'Website Currency Code')
    ->addIndex($installer->getIdxName('magento_reward', array('customer_id', 'website_id'), Magento_DB_Adapter_Interface::INDEX_TYPE_UNIQUE),
        array('customer_id', 'website_id'), array('type' => Magento_DB_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName('magento_reward', array('website_id')),
        array('website_id'))
    ->addForeignKey($installer->getFkName('magento_reward', 'customer_id', 'customer_entity', 'entity_id'),
        'customer_id', $installer->getTable('customer_entity'), 'entity_id',
        Magento_DB_Ddl_Table::ACTION_CASCADE, Magento_DB_Ddl_Table::ACTION_CASCADE)
    ->setComment('Enterprise Reward');
$installer->getConnection()->createTable($table);

/**
 * Create table 'magento_reward_history'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('magento_reward_history'))
    ->addColumn('history_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'History Id')
    ->addColumn('reward_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Reward Id')
    ->addColumn('website_id', Magento_DB_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Website Id')
    ->addColumn('store_id', Magento_DB_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        ), 'Store Id')
    ->addColumn('action', Magento_DB_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Action')
    ->addColumn('entity', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        ), 'Entity')
    ->addColumn('points_balance', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Points Balance')
    ->addColumn('points_delta', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Points Delta')
    ->addColumn('points_used', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Points Used')
    ->addColumn('currency_amount', Magento_DB_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'nullable'  => false,
        'default'   => '0.0000',
        ), 'Currency Amount')
    ->addColumn('currency_delta', Magento_DB_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'nullable'  => false,
        'default'   => '0.0000',
        ), 'Currency Delta')
    ->addColumn('base_currency_code', Magento_DB_Ddl_Table::TYPE_TEXT, 5, array(
        'nullable'  => false,
        ), 'Base Currency Code')
    ->addColumn('additional_data', Magento_DB_Ddl_Table::TYPE_TEXT, '64k', array(
        'nullable'  => false,
        ), 'Additional Data')
    ->addColumn('comment', Magento_DB_Ddl_Table::TYPE_TEXT, '64k', array(
        'nullable'  => true,
        ), 'Comment')
    ->addColumn('created_at', Magento_DB_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        ), 'Created At')
    ->addColumn('expired_at_static', Magento_DB_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Expired At Static')
    ->addColumn('expired_at_dynamic', Magento_DB_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Expired At Dynamic')
    ->addColumn('is_expired', Magento_DB_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Is Expired')
    ->addColumn('is_duplicate_of', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        ), 'Is Duplicate Of')
    ->addColumn('notification_sent', Magento_DB_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Notification Sent')
    ->addIndex($installer->getIdxName('magento_reward_history', array('reward_id')),
        array('reward_id'))
    ->addIndex($installer->getIdxName('magento_reward_history', array('website_id')),
        array('website_id'))
    ->addIndex($installer->getIdxName('magento_reward_history', array('store_id')),
        array('store_id'))
    ->addForeignKey($installer->getFkName('magento_reward_history', 'reward_id', 'magento_reward', 'reward_id'),
        'reward_id', $installer->getTable('magento_reward'), 'reward_id',
        Magento_DB_Ddl_Table::ACTION_CASCADE, Magento_DB_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('magento_reward_history', 'store_id', 'core_store', 'store_id'),
        'store_id', $installer->getTable('core_store'), 'store_id',
        Magento_DB_Ddl_Table::ACTION_SET_NULL, Magento_DB_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('magento_reward_history', 'website_id', 'core_website', 'website_id'),
        'website_id', $installer->getTable('core_website'), 'website_id',
        Magento_DB_Ddl_Table::ACTION_CASCADE, Magento_DB_Ddl_Table::ACTION_CASCADE)
    ->setComment('Enterprise Reward History');
$installer->getConnection()->createTable($table);

/**
 * Create table 'magento_reward_rate'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('magento_reward_rate'))
    ->addColumn('rate_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Rate Id')
    ->addColumn('website_id', Magento_DB_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Website Id')
    ->addColumn('customer_group_id', Magento_DB_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Customer Group Id')
    ->addColumn('direction', Magento_DB_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'default'   => '1',
        ), 'Direction')
    ->addColumn('points', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Points')
    ->addColumn('currency_amount', Magento_DB_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'nullable'  => false,
        'default'   => '0.0000',
        ), 'Currency Amount')
    ->addIndex($installer->getIdxName('magento_reward_rate', array('website_id', 'customer_group_id', 'direction'), Magento_DB_Adapter_Interface::INDEX_TYPE_UNIQUE),
        array('website_id', 'customer_group_id', 'direction'), array('type' => Magento_DB_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName('magento_reward_rate', array('website_id')),
        array('website_id'))
    ->addIndex($installer->getIdxName('magento_reward_rate', array('customer_group_id')),
        array('customer_group_id'))
    ->addForeignKey($installer->getFkName('magento_reward_rate', 'website_id', 'core_website', 'website_id'),
        'website_id', $installer->getTable('core_website'), 'website_id',
        Magento_DB_Ddl_Table::ACTION_CASCADE, Magento_DB_Ddl_Table::ACTION_CASCADE)
    ->setComment('Enterprise Reward Rate');
$installer->getConnection()->createTable($table);

/**
 * Create table 'magento_reward_salesrule'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('magento_reward_salesrule'))
    ->addColumn('rule_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Rule Id')
    ->addColumn('points_delta', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Points Delta')
    ->addIndex($installer->getIdxName('magento_reward_salesrule', array('rule_id'), Magento_DB_Adapter_Interface::INDEX_TYPE_UNIQUE),
        array('rule_id'),
        array('type' => Magento_DB_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addForeignKey($installer->getFkName('magento_reward_salesrule', 'rule_id', 'salesrule', 'rule_id'),
        'rule_id', $installer->getTable('salesrule'), 'rule_id',
        Magento_DB_Ddl_Table::ACTION_CASCADE, Magento_DB_Ddl_Table::ACTION_CASCADE)
    ->setComment('Enterprise Reward Reward Salesrule');
$installer->getConnection()->createTable($table);


$installer->addAttribute('quote', 'use_reward_points',
    array('type' => Magento_DB_Ddl_Table::TYPE_INTEGER)
);
$installer->addAttribute('quote', 'reward_points_balance',
    array('type' => Magento_DB_Ddl_Table::TYPE_INTEGER)
);
$installer->addAttribute('quote', 'base_reward_currency_amount', array('type' => Magento_DB_Ddl_Table::TYPE_DECIMAL));
$installer->addAttribute('quote', 'reward_currency_amount', array('type' => Magento_DB_Ddl_Table::TYPE_DECIMAL));

$installer->addAttribute('quote_address', 'reward_points_balance',
    array('type' => Magento_DB_Ddl_Table::TYPE_INTEGER)
);
$installer->addAttribute('quote_address', 'base_reward_currency_amount', array('type' => Magento_DB_Ddl_Table::TYPE_DECIMAL));
$installer->addAttribute('quote_address', 'reward_currency_amount', array('type' => Magento_DB_Ddl_Table::TYPE_DECIMAL));

$installer->addAttribute('order', 'reward_points_balance',
    array('type' => Magento_DB_Ddl_Table::TYPE_INTEGER)
);
$installer->addAttribute('order', 'base_reward_currency_amount', array('type' => Magento_DB_Ddl_Table::TYPE_DECIMAL));
$installer->addAttribute('order', 'reward_currency_amount', array('type' => Magento_DB_Ddl_Table::TYPE_DECIMAL));
$installer->addAttribute('order', 'base_rwrd_crrncy_amt_invoiced', array('type' => Magento_DB_Ddl_Table::TYPE_DECIMAL));
$installer->addAttribute('order', 'rwrd_currency_amount_invoiced', array('type' => Magento_DB_Ddl_Table::TYPE_DECIMAL));
$installer->addAttribute('order', 'base_rwrd_crrncy_amnt_refnded', array('type' => Magento_DB_Ddl_Table::TYPE_DECIMAL));
$installer->addAttribute('order', 'rwrd_crrncy_amnt_refunded', array('type' => Magento_DB_Ddl_Table::TYPE_DECIMAL));

$installer->addAttribute('invoice', 'base_reward_currency_amount', array('type' => Magento_DB_Ddl_Table::TYPE_DECIMAL));
$installer->addAttribute('invoice', 'reward_currency_amount', array('type' => Magento_DB_Ddl_Table::TYPE_DECIMAL));

$installer->addAttribute('creditmemo', 'base_reward_currency_amount', array('type' => Magento_DB_Ddl_Table::TYPE_DECIMAL));
$installer->addAttribute('creditmemo', 'reward_currency_amount', array('type' => Magento_DB_Ddl_Table::TYPE_DECIMAL));

$installer->addAttribute('invoice', 'reward_points_balance',
    array('type' => Magento_DB_Ddl_Table::TYPE_INTEGER)
);

$installer->addAttribute('creditmemo', 'reward_points_balance',
    array('type' => Magento_DB_Ddl_Table::TYPE_INTEGER)
);

$installer->addAttribute('order', 'reward_points_balance_refund',
    array('type' => Magento_DB_Ddl_Table::TYPE_INTEGER)
);
$installer->addAttribute('creditmemo', 'reward_points_balance_refund',
    array('type' => Magento_DB_Ddl_Table::TYPE_INTEGER)
);

$installer->addAttribute('quote', 'base_reward_currency_amount', array('type' => Magento_DB_Ddl_Table::TYPE_DECIMAL));
$installer->addAttribute('quote', 'reward_currency_amount', array('type' => Magento_DB_Ddl_Table::TYPE_DECIMAL));

$installer->addAttribute('order', 'reward_points_balance_refunded',
    array('type' => Magento_DB_Ddl_Table::TYPE_INTEGER)
);

$installer->addAttribute('order', 'reward_salesrule_points',
    array('type' => Magento_DB_Ddl_Table::TYPE_INTEGER)
);

$installer->addAttribute('customer', 'reward_update_notification',
    array(
        'type' => 'int',
        'visible' => 0,
        'required' => true,
        'visible_on_front' => 1,
        'is_user_defined' => 0,
        'is_system' => 1,
        'is_hidden' => 1
    )
);

$installer->addAttribute('customer', 'reward_warning_notification',
    array(
        'type' => 'int',
        'visible' => 0,
        'required' => true,
        'visible_on_front' => 1,
        'is_user_defined' => 0,
        'is_system' => 1,
        'is_hidden' => 1
    )
);

$installer->endSetup();
