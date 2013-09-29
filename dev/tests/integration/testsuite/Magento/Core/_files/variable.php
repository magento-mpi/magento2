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

$variable = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Core\Model\Variable');
$variable->setCode('variable_code')
    ->setName('Variable Name')
    ->setPlainValue('Plain Value')
    ->setHtmlValue('HTML Value')
    ->save()
;
