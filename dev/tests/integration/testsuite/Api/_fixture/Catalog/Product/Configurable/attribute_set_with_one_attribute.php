<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

define('ATTRIBUTE_OPTIONS_COUNT', 2);
/** @var $attributeSet Mage_Eav_Model_Entity_Attribute_Set */
$attributeSet = require '_fixture/_block/Catalog/Product/Attribute/Set.php';
$attributeSet->save();
/** @var $entityType Mage_Eav_Model_Entity_Type */
$entityType = Mage::getModel('Mage_Eav_Model_Entity_Type')->loadByCode('catalog_product');
$attributeSet->initFromSkeleton($entityType->getDefaultAttributeSetId())->save();
PHPUnit_Framework_TestCase::setFixture('attribute_set_with_one_attribute', $attributeSet);

/** @var $attributeFixture Mage_Catalog_Model_Resource_Eav_Attribute */
$attributeFixture = require '_fixture/_block/Catalog/Product/Attribute.php';

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
$attribute->setOption(
    array(
        'value' => $options
    )
);
$attribute->save();
PHPUnit_Framework_TestCase::setFixture('eav_configurable_attribute', $attribute);
