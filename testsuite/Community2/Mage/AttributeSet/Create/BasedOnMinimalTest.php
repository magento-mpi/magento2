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
class Community2_Mage_AttributeSet_CreateBasedOnMinimalTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>Login to backend as admin</p>
     * <p>Navigate to Catalog - Attributes - Manage Attribute Sets</p>
     *
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_attribute_sets');

    }

    /**
     * <p>Attribute Set creation - based on Minimal</p>
     * <p>Steps</p>
     * <p>1. Press the "Add New Set" button</p>
     * <p>2. - Input unique value in "Name" field</p>
     * <p>   - Select "Minimal" in "Based On" dropdown list.</p>
     * <p>3. Click button "Save Attribute Set"</p>
     * <p>Expected result</p>
     * <p>Received the message on successful completion of the attribute set creation</p>
     *
     * @return string
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
     * <p>Steps</p>
     * <p>1. Find created attribute set in Manage Attribute Set grid</p>
     * <p>2. Click on this line
     * <p>Expected result</p>
     * <p>The same system attributes as in Minimal Attribute Set are present in Groups field set:</p>
     * <p>name, description, short_description, sku, price, status, visibility, price_view, tax_class_id, weight</p>
     * <p>allow_open_amount, giftcard_amounts</p>
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
        $this->attributeSetHelper()->verifyAttributeAssignedToSet($assignedAttributes, $unassignedAttributes);
        $this->assertEmptyVerificationErrors();
    }
}