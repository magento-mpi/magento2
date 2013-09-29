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
    ->create('Magento\Catalog\Model\Resource\Eav\Attribute');
$model->setName('system_attribute')
    ->setId(3)
    ->setEntityTypeId(4)
    ->setIsUserDefined(0)
    ->setApplyTo(array('simple', 'configurable'));
$model->save();
