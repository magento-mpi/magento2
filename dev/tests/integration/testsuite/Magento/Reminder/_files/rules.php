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

/** @var $rule \Magento\Reminder\Model\Rule */
$rule = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Reminder\Model\Rule');
$rule->setData(
    array(
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
        'to_date' => '1981-01-01'
    )
)->save();

$rule = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Reminder\Model\Rule');
$rule->setData(
    array(
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
        'to_date' => date('Y-m-d', time() + 172800)
    )
)->save();
