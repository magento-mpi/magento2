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

if (!Magento_Test_Webservice::getFixture('attribute_set_with_configurable')) {
    define('ATTRIBUTES_COUNT', 2);
    define('ATTRIBUTE_OPTIONS_COUNT', 3);

    /** @var $attributeSet Mage_Eav_Model_Entity_Attribute_Set */
    $attributeSet = require TESTS_FIXTURES_DIRECTORY . '/_block/Catalog/Product/Attribute/Set.php';
    $attributeSet->save();
    /** @var $entityType Mage_Eav_Model_Entity_Type */
    $entityType = Mage::getModel('Mage_Eav_Model_Entity_Type')->loadByCode('catalog_product');
    $attributeSet->initFromSkeleton($entityType->getDefaultAttributeSetId())->save();
    Magento_Test_Webservice::setFixture('attribute_set_with_configurable', $attributeSet);

    /** @var $attributeFixture Mage_Catalog_Model_Resource_Eav_Attribute */
    $attributeFixture = require TESTS_FIXTURES_DIRECTORY . '/_block/Catalog/Product/Attribute.php';

    for ($attributeCount = 1; $attributeCount <= ATTRIBUTES_COUNT; $attributeCount++) {
        $attribute = clone $attributeFixture;
        $attribute->setAttributeCode('test_attr_' . uniqid())
            ->setFrontendLabel(array(0 => 'Test Attr ' . uniqid()))
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
        Magento_Test_Webservice::setFixture('eav_configurable_attribute_' . $attributeCount, $attribute);
        unset($attribute);
    }
}


