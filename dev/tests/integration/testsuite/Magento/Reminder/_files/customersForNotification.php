<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

require __DIR__ . '/../../../Magento/Customer/_files/customer.php';
require __DIR__ . '/../../../Magento/Customer/_files/quote.php';

$conditions = array (
    'conditions' =>
        array (
            1 =>
                array (
                    'type' => 'Magento\\Reminder\\Model\\Rule\\Condition\\Combine\\Root',
                    'aggregator' => 'all',
                    'value' => '1',
                    'new_child' => '',
                ),
            '1--1' =>
                array (
                    'type' => 'Magento\\Reminder\\Model\\Rule\\Condition\\Cart',
                    'operator' => '>',
                    'value' => '',
                    'aggregator' => 'all',
                    'new_child' => '',
                ),
            '1--1--1' =>
                array (
                    'type' => 'Magento\\Reminder\\Model\\Rule\\Condition\\Cart\\Subselection',
                    'operator' => '==',
                    'aggregator' => 'all',
                    'new_child' => '',
                ),
            '1--1--1--1' =>
                array (
                    'type' => 'Magento\\Reminder\\Model\\Rule\\Condition\\Cart\\Sku',
                    'operator' => '==',
                    'value' => 'simple',
                ),
        ),
);
/** @var $rule \Magento\Reminder\Model\Rule */
$rule = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Reminder\Model\Rule');
$rule->loadPost($conditions);
$rule->setData(
    array(
        'name' => 'Rule 1',
        'description' => 'Rule 1 Desc',
        'conditions_serialized' => serialize($rule->getConditions()->asArray()),
        'condition_sql' => 1,
        'is_active' => 1,
        'salesrule_id' => null,
        'schedule' => 2,
        'default_label' => null,
        'default_description' => null,
        'from_date' => null,
        'to_date' => null,
        'website_ids' => 1,
    )
)->save();

