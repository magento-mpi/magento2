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
    ->create('Magento_Catalog_Model_Resource_Eav_Attribute');
$model->setName('system_attribute')
    ->setId(2)
    ->setEntityTypeId(4)
    ->setIsUserDefined(0);
$model->save();
