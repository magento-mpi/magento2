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
 * Attribute Set creation tests
 */
class Core_Mage_AttributeSet_Create_BasedOnMinimalTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_attribute_sets');

    }

    /**
     * <p>Attribute Set creation - based on Minimal</p>
     *
     * @return string
     *
     * @test
     * @TestlinkId TL-MAGE-5697
     */
    public function createBasedOnMinimalAttributeSetWithoutChanges()
    {
        //Data
        $setData = $this->loadDataSet('AttributeSet', 'mini_attribute_set');
        //Steps
        $this->attributeSetHelper()->createAttributeSet($setData);
        //Verifying
        $this->assertMessagePresent('success', 'success_attribute_set_saved');
        return $setData;
    }

    /**
     * <p>Verifying attributes, assigned to attribute set </p>
     *
     * @param array $setName
     *
     * @test
     * @depends createBasedOnMinimalAttributeSetWithoutChanges
     * @TestlinkId TL-MAGE-5697
     */
    public function verifySystemAttributesAssignedToSet($setName)
    {
        //Data
        $assignedAttributes = $this->loadDataSet('AttributeSet', 'system_attributes_general_minimal');
        $unassignedAttributes = $this->loadDataSet('AttributeSet', 'system_attributes_unassigned_minimal');
        //Steps
        $this->attributeSetHelper()->openAttributeSet($setName);
        //Verifying
        $this->attributeSetHelper()->verifyAttributeAssignment($assignedAttributes);
        $this->attributeSetHelper()->verifyAttributeAssignment($unassignedAttributes, false);
    }
}