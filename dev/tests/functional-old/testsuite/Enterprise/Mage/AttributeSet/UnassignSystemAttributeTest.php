<?php
/**
 * Magento
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_AttributeSet
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 *
 */
/**
 * Verifying the ability to unassign system attributes from attribute set
 */
class Enterprise_Mage_AttributeSet_UnassignSystemAttributeTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_attribute_sets');
    }

    /**
     * Create new attribute set based on Default.
     *
     * @return string
     *
     * @test
     */
    public function preconditionsForTests()
    {
        //Data
        $setData = $this->loadDataSet('AttributeSet', 'attribute_set');
        //Steps
        $this->attributeSetHelper()->createAttributeSet($setData);
        //Verifying
        $this->assertMessagePresent('success', 'success_attribute_set_saved');

        return $setData['set_name'];
    }

    /**
     * Remove system attributes from Default attribute set
     *
     * @param string $attributeCode
     * @param string $setName
     *
     * @test
     * @dataProvider unassignableSystemAttributesDataProvider
     * @depends preconditionsForTests
     * @TestLinkId TL-MAGE-6124
     */
    public function fromDefaultAttributeSet($attributeCode, $setName)
    {
        //Steps
        $this->attributeSetHelper()->openAttributeSet($setName);
        $this->attributeSetHelper()->unassignAttributeFromSet(array($attributeCode));
        //Verifying
        $this->attributeSetHelper()->verifyAttributeAssignment(array($attributeCode), false);
        $this->saveForm('save_attribute_set');
        $this->assertMessagePresent('success', 'success_attribute_set_saved');
    }

    /**
     * DataProvider for system attributes, which can be unassigned
     *
     * @return array
     */
    public function unassignableSystemAttributesDataProvider()
    {
        return array(
            array('gift_wrapping_available'),
            array('gift_wrapping_price'),
            array('is_returnable'),
            array('open_amount_max'),
            array('open_amount_min')
        );
    }

    /**
     * Non removable system attributes
     *
     * @param string $attributeCode
     *
     * @test
     * @dataProvider nonUnassignableSystemAttributesDataProvider
     * @TestLinkId TL-MAGE-6128
     */
    public function verifyBasicAttributes($attributeCode)
    {
        //Data
        $setName = 'Default';
        //Steps
        $this->attributeSetHelper()->openAttributeSet($setName);
        $this->attributeSetHelper()->unassignAttributeFromSet(array($attributeCode), true);
        //Verifying
        $this->attributeSetHelper()->verifyAttributeAssignment(array($attributeCode));
    }

    /**
     * DataProvider with list of non unassignable system attributes
     *
     * @return array
     */
    public function nonUnassignableSystemAttributesDataProvider()
    {
        return array(
            array('allow_open_amount'),
            array('giftcard_amounts')
        );
    }
}
