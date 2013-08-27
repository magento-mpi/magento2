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
    Mage::getModel('Magento_Core_Model_Context')
);
$model->setName('user_attribute')
    ->setId(1)
    ->setEntityTypeId(4)
    ->setIsUserDefined(1);
$model->save();
