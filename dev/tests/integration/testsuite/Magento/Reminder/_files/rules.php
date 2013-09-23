<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reminder
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$conditions = serialize(array());

/** @var $rule Magento_Reminder_Model_Rule */
$rule = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Reminder_Model_Rule');
$rule->setData(array(
    'name' => 'Rule 1',
    'description' => 'Rule 1 Desc',
    'conditions_serialized' => $conditions,
    'condition_sql' => 1,
    'is_active' => 1,
    'salesrule_id' => null,
    'schedule' => null,
    'default_label' => null,
    'default_description' => null,
    'from_date' => null,
    'to_date' => '1981-01-01',
))->save();

$rule = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Reminder_Model_Rule');
$rule->setData(array(
    'name' => 'Rule 2',
    'description' => 'Rule 2 Desc',
    'conditions_serialized' => $conditions,
    'condition_sql' => 1,
    'is_active' => 1,
    'salesrule_id' => null,
    'schedule' => null,
    'default_label' => null,
    'default_description' => null,
    'from_date' => null,
    /**
     * For some reason any values in columns from_date and to_date are ignored
     * This has to be fixed in scope of MAGE-5166
     *
     * Also make sure that dates will be properly formatted through Magento_DB_Adapter_*::formatDate()
     */
    'to_date' => date('Y-m-d', time() + 172800),
))->save();

//$adapter = $rule->getResource()->getReadConnection();
//print_r($adapter->fetchAll('SELECT * FROM magento_reminder_rule'));
