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

$attribute = new Mage_Catalog_Model_Resource_Eav_Attribute;

$attribute->setEntityTypeId(Mage::getModel('Mage_Eav_Model_Entity')->setType('catalog_product')->getTypeId())
    ->setAttributeCode('test_attr_' . uniqid())
    ->setIsUserDefined(true)
    ->setIsVisibleOnFront(false)
    ->setIsRequired(false)
    ->setFrontendLabel(array(0 => 'Test Attr ' . uniqid()))
    ->setApplyTo(array());

return $attribute;
