<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$variable = new Mage_Core_Model_Variable;
$variable->setCode('variable_code')
    ->setName('Variable Name')
    ->setPlainValue('Plain Value')
    ->setHtmlValue('HTML Value')
    ->save()
;
