<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Catalog
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$model = new Mage_Catalog_Model_Resource_Eav_Attribute(
    Mage::getModel('Magento_Core_Model_Context')
);
$model->setName('system_attribute')
    ->setId(3)
    ->setEntityTypeId(4)
    ->setIsUserDefined(0)
    ->setApplyTo(array('simple', 'configurable'));
$model->save();
