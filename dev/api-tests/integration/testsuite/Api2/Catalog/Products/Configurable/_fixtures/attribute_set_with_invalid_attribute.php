<?php
/**
 * {license_notice}
 *
 * @category    Paas
 * @package     tests
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $attributeSet Mage_Eav_Model_Entity_Attribute_Set */
$attributeSet = require TESTS_FIXTURES_DIRECTORY . '/Catalog/Product/Attribute/Set.php';
$attributeSet->save();
/** @var $entityType Mage_Eav_Model_Entity_Type */
$entityType = Mage::getModel('Mage_Eav_Model_Entity_Type')->loadByCode('catalog_product');
$attributeSet->initFromSkeleton($entityType->getDefaultAttributeSetId())->save();
Magento_Test_Webservice::setFixture('attribute_set_with_invalid_attribute', $attributeSet);

/** @var $attributeFixture Mage_Catalog_Model_Resource_Eav_Attribute */
$attributeFixture = require TESTS_FIXTURES_DIRECTORY . '/Catalog/Product/Attribute.php';

$validAttribute = clone $attributeFixture;
$validAttribute->setAttributeCode(substr('valid_attribute_' . uniqid(), 0, 30))
    ->setFrontendLabel(array(0 => 'Valid Attribute ' . uniqid()))
    ->setIsGlobal(true)
    ->setIsConfigurable(true)
    ->setFrontendInput('select')
    ->setBackendType('int')
    ->setAttributeSetId($attributeSet->getId())
    ->setAttributeGroupId($attributeSet->getDefaultGroupId());
$validAttribute->save();
Magento_Test_Webservice::setFixture('eav_valid_configurable_attribute', $validAttribute);

$invalidAttribute = clone $attributeFixture;
$invalidAttribute->setAttributeCode(substr('invalid_attribute_' . uniqid(), 0, 30))
    ->setFrontendLabel(array(0 => 'Invalid Attribute ' . uniqid()))
    ->setIsGlobal(true)
    ->setIsConfigurable(false)
    ->setFrontendInput('select')
    ->setBackendType('int')
    ->setAttributeSetId($attributeSet->getId())
    ->setAttributeGroupId($attributeSet->getDefaultGroupId());
$invalidAttribute->save();
Magento_Test_Webservice::setFixture('eav_invalid_configurable_attribute', $invalidAttribute);


