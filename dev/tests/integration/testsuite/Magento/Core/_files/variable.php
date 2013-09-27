<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$variable = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
    ->create('Magento_Core_Model_Variable');
$variable->setCode('variable_code')
    ->setName('Variable Name')
    ->setPlainValue('Plain Value')
    ->setHtmlValue('HTML Value')
    ->save()
;
