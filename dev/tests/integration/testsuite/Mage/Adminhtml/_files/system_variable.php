<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$data = array(
    'code' => 'test_variable_1',
    'name' => 'Test Variable 1',
    'html_value' => '<b>Test Variable 1 HTML Value</b>',
    'plain_value' => 'Test Variable 1 plain Value',
);
$variable = Mage::getModel('Mage_Core_Model_Variable')
    ->setData($data)
    ->save();

Mage::register('current_variable', $variable);
