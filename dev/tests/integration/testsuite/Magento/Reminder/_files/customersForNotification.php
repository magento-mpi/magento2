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

require __DIR__ . '/../../../Magento/Customer/_files/customer.php';
require __DIR__ . '/../../../Magento/Customer/_files/quote.php';

$conditionsSerlz = 'a:7:{s:4:"type";s:50:"Magento\Reminder\Model\Rule\Condition\Combine\Root";s:9:"attribute";N;s:8:"operator";N;s:5:"value";s:1:"1";s:18:"is_value_processed";N;s:10:"aggregator";s:3:"all";s:10:"conditions";a:1:{i:0;a:7:{s:4:"type";s:42:"Magento\Reminder\Model\Rule\Condition\Cart";s:9:"attribute";N;s:8:"operator";s:1:">";s:5:"value";s:0:"";s:18:"is_value_processed";N;s:10:"aggregator";s:3:"all";s:10:"conditions";a:1:{i:0;a:7:{s:4:"type";s:55:"Magento\Reminder\Model\Rule\Condition\Cart\Subselection";s:9:"attribute";N;s:8:"operator";s:2:"==";s:5:"value";s:2:"==";s:18:"is_value_processed";N;s:10:"aggregator";s:3:"all";s:10:"conditions";a:1:{i:0;a:5:{s:4:"type";s:46:"Magento\Reminder\Model\Rule\Condition\Cart\Sku";s:9:"attribute";b:0;s:8:"operator";s:2:"==";s:5:"value";s:6:"simple";s:18:"is_value_processed";b:0;}}}}}}}';
/** @var $rule \Magento\Reminder\Model\Rule */
$rule = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Reminder\Model\Rule');
$rule->setData(
    array(
        'name' => 'Rule 1',
        'description' => 'Rule 1 Desc',
        'conditions_serialized' => $conditionsSerlz,
        'condition_sql' => 1,
        'is_active' => 1,
        'salesrule_id' => null,
        'schedule' => null,
        'default_label' => null,
        'default_description' => null,
        'from_date' => null,
        'to_date' => null,
        'website_ids' => 1,
    )
)->save();

$beforeYesterday = date('Y-m-d h:m:s', time() - 172800);
$adapter = $rule->getResource()->getReadConnection();
$adapter->query("UPDATE sales_flat_quote SET updated_at = \"$beforeYesterday\"");
