<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Customer\Model\Attribute');
$model->loadByCode('customer', 'prefix')
      ->setIsVisible('1');
$model->save();

$model->loadByCode('customer', 'middlename')
    ->setIsVisible('1');
$model->save();

$model->loadByCode('customer', 'suffix')
    ->setIsVisible('1');
$model->save();
