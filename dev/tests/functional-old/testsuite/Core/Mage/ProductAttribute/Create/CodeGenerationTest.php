<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Create new product attribute with autogenerated attribute code
 *
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_ProductAttribute_Create_CodeGenerationTest extends Mage_Selenium_TestCase
{
    /**
     * Preconditions:
     * Navigate to System -> Manage Attributes.
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_attributes');
    }

    /**
     * Checking of generation attribute code from attribute label
     *
     * @param string $label
     * @param string $attributeCode
     *
     * @test
     * @dataProvider attributeCodeGenerationDataProvider
     * @TestlinkId TL-MAGETWO-14
     */
    public function verifyGeneratedValue($label, $attributeCode)
    {
        //Data
        $attrData = $this->loadDataSet('ProductAttribute', 'product_attribute_textfield',
            array(
                 'attribute_code' => '%noValue%',
                 'attribute_label' => $label
            )
        );
        //Steps
        $this->productAttributeHelper()->createAttribute($attrData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_attribute');
        $this->productAttributeHelper()->openAttribute(array('attribute_code' => $attributeCode));
        $attrData['advanced_properties']['attribute_code'] = $attributeCode;
        $this->productAttributeHelper()->verifyAttribute($attrData);
    }

    /**
     * DataProvider for generated according to rules attribute code
     */
    public function attributeCodeGenerationDataProvider()
    {
        $index = $this->generate('string', 5, ':lower:');
        $number = $this->generate('string', 5, ':digit:');
        $punct = str_replace(array('@', '&'), '', $this->generate('string', 30, ':punct:'));

        return array(
//            array('Skład' . $index, 'sklad'. $index),
//            array('Размер' . $index, 'razmer' . $index),
//            array('@&™©' . $index, 'at_tmc'. $index),
            array('Size' . $index, 'size' . $index),
            array('Size UK' . $index, 'size_uk' . $index),
            array($number, 'attr_' . $number),
            array($punct . $index, $index),
        );
    }

    /**
     * Checking of generation attribute code from attribute label with invalid length value
     *
     * @return string
     *
     * @test
     * @TestlinkId TL-MAGETWO-14
     */
    public function verifyGeneratedLongValue()
    {
        //Data
        $value = $this->generate('string', 45, ':lower:');
        $attrData = $verify = $this->loadDataSet('ProductAttribute', 'product_attribute_textfield', array(
            'attribute_code' => '%noValue%',
            'attribute_label' => $value
        ));
        $verify['advanced_properties']['attribute_code'] = substr($value, 0, 29);
        //Steps
        $this->productAttributeHelper()->createAttribute($attrData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_attribute');
        $this->productAttributeHelper()->openAttribute(array('attribute_label' => $value));
        $this->productAttributeHelper()->verifyAttribute($verify);

        return $attrData;
    }

    /**
     * Checking generated from attribute label attribute code which already exist
     *
     * @param array $attrData
     *
     * @test
     * @depends verifyGeneratedLongValue
     * @TestlinkId TL-MAGETWO-15
     */
    public function verifyExistValue($attrData)
    {
        $this->markTestIncomplete('MAGETWO-8909');
        //Steps
        $this->productAttributeHelper()->createAttribute($attrData);
        //Verifying
        $this->addParameter('code', substr($attrData['attribute_properties']['attribute_label'], 0, 29));
        $this->assertMessagePresent('validation', 'with_same_code');
    }

    /**
     * Checking of generation attribute code from attribute label if label contains only unsupported characters
     *
     * @test
     * @TestlinkId TL-MAGETWO-16
     */
    public function verifyInvalidValue()
    {
        //Data
        $invalidValue = str_replace(array('@', '&'), '', $this->generate('string', 30, ':punct:'));
        $attrData = $this->loadDataSet('ProductAttribute', 'product_attribute_textfield',
            array(
                 'attribute_code' => '%noValue%',
                 'attribute_label' => $invalidValue
            )
        );
        //Steps
        $this->clickButton('add_new_attribute');
        $this->productAttributeHelper()->fillAttributeTabs($attrData);
        $this->saveAndContinueEdit('button', 'save_and_continue_edit');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_attribute');
        $this->openTab('properties');
        if (!$this->isControlExpanded(self::FIELD_TYPE_PAGEELEMENT, 'advanced_attribute_properties_section')) {
            $this->clickControl(self::FIELD_TYPE_PAGEELEMENT, 'advanced_attribute_properties_section', false);
        }
        $generatedCode = $this->getControlAttribute('field', 'attribute_code', 'value');
        $this->assertStringStartsWith('attr_', $generatedCode, 'Attribute code is not generated according ro rules');
    }
}
