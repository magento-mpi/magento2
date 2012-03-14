<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_TargetRule
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$rule = new Enterprise_TargetRule_Model_Rule;
$data = array(
    'name' => 'related',
    'is_active' => '1',
    'apply_to' => '2',
    'use_customer_segment' => '0',
    'customer_segment_ids' => array('0' => ''),
);
$rule->loadPost($data);
$rule->save();
