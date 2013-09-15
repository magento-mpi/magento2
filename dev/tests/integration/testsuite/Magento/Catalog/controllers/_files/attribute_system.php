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

$model = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
    ->create('Magento\Catalog\Model\Resource\Eav\Attribute');
$model->setName('system_attribute')
    ->setId(2)
    ->setEntityTypeId(4)
    ->setIsUserDefined(0);
$model->save();
