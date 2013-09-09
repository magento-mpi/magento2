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

$model = new Magento_Catalog_Model_Resource_Eav_Attribute(
    Mage::getModel('Magento_Core_Model_Context'),
    Magento_Test_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Registry')
);
$model->setName('system_attribute')
    ->setId(3)
    ->setEntityTypeId(4)
    ->setIsUserDefined(0)
    ->setApplyTo(array('simple', 'configurable'));
$model->save();
