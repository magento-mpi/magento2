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

define('ATTRIBUTE_OPTIONS_COUNT', 2);
/** @var $attributeSet Mage_Eav_Model_Entity_Attribute_Set */
$attributeSet = require TESTS_FIXTURES_DIRECTORY . '/_block/Catalog/Product/Attribute/Set.php';
$attributeSet->save();
/** @var $entityType Mage_Eav_Model_Entity_Type */
$entityType = Mage::getModel('Mage_Eav_Model_Entity_Type')->loadByCode('catalog_product');
$attributeSet->initFromSkeleton($entityType->getDefaultAttributeSetId())->save();
Magento_Test_Webservice::setFixture('attribute_set_with_one_attribute', $attributeSet);

/** @var $attributeFixture Mage_Catalog_Model_Resource_Eav_Attribute */
$attributeFixture = require TESTS_FIXTURES_DIRECTORY . '/_block/Catalog/Product/Attribute.php';

$attribute = clone $attributeFixture;
$attribute->setAttributeCode(substr('test_attribute_' . uniqid(), 0, 30))
    ->setFrontendLabel(array(0 => 'Test Attribute ' . uniqid()))
    ->setIsGlobal(true)
    ->setIsConfigurable(true)
    ->setFrontendInput('select')
    ->setBackendType('int')
    ->setAttributeSetId($attributeSet->getId())
    ->setAttributeGroupId($attributeSet->getDefaultGroupId());
$options = array();
for ($optionCount = 0; $optionCount < ATTRIBUTE_OPTIONS_COUNT; $optionCount++) {
    $options['option_' . $optionCount] = array(
        0 => 'Test Option #' . $optionCount
    );
}
$attribute->setOption(array(
    'value' => $options
));
$attribute->save();
Magento_Test_Webservice::setFixture('eav_configurable_attribute', $attribute);
