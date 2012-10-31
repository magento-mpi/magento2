<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_RMA
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**
 * Delete RMA item attribute
 *
 * @package     Mage_RMA
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Enterprise2_Mage_RMA_ItemAttribute_DeleteTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_rma_items_attribute');
    }

    /**
     * <p>Delete custom attribute</p>
     * <p>Steps:</p>
     * <p>1. Login to Backend</p>
     * <p>2. Navigate to Sales-RMA-Manage RMA Items Attributes<p>
     * <p>3. Click on "Add New Attribute" button</p>
     * <p>4. Choose "Text Field" in 'Input Type' dropdown</p>
     * <p>5. Fill all required fields</p>
     * <p>6. Click "Save Attribute" button</p>
     * <p>7. Open previously created Attribute</p>
     * <p>8. Click "Delete Attribute" button and confirm popup message</p>
     * <p>9. Repeat step 2-8 using other attribute type </p>
     * <p>Expected result:</p>
     * <p>Success message: 'The RMA item attribute has been deleted.' is displayed.</p>
     *
     * @param array $attributeType
     *
     * @test
     * @dataProvider allTypeCustomDataProvider
     * @TestlinkId TL-MAGE-6113
     */
    public function allTypeCustomAttribute($attributeType)
    {
        //Data
        $attrData = $this->loadDataSet('RMAItemsAttribute', $attributeType);
        $this->addParameter('attribute_admin_title', $attrData['admin_title']);
        //Steps
        $this->productAttributeHelper()->createAttribute($attrData);
        $this->assertMessagePresent('success', 'success_saved_attribute');
        $this->searchAndOpen(array('filter_attribute_code' => $attrData['attribute_code']));
        $this->clickButtonAndConfirm('delete_attribute', 'delete_confirm_message');
        //Verifying
        $this->assertMessagePresent('success', 'success_deleted_attribute');
    }

    public function allTypeCustomDataProvider()
    {
        return array(
            array('rma_item_attribute_textfield'),
            array('rma_item_attribute_textarea'),
            array('rma_item_attribute_dropdown'),
            array('rma_item_attribute_image')
        );
    }

    /**
     * <p>Verify that impossible Delete system RMA attribute</p>
     * <p>Steps:</p>
     * <p>1. Login to Backend</p>
     * <p>2. Navigate to Sales-RMA-Manage RMA Items Attributes<p>
     * <p>3. Open "Resolution" system attribute</p>
     * <p>4. Check that "Delete Attribute" button is missing</p>
     * <p>9. Repeat step 2-4 using other System attribute: Condition, Reason, Reason_other</p>
     * <p>Expected result:</p>
     * <p>"Delete Attribute" button is missing</p>
     *
     * @param array $attributeLabel
     *
     * @test
     * @dataProvider systemAttributeDataProvider
     * @TestlinkId TL-MAGE-6115
     */
    public function systemAttribute($attributeLabel)
    {
        //Data
        $this->addParameter('attribute_admin_title', $attributeLabel);
        //Steps
        $this->searchAndOpen(array('filter_attribute_label' => $attributeLabel));
        //Verifying
        $this->assertFalse($this->controlIsPresent('button', 'delete_attribute'),
            'Delete button must be absent for system RMA attribute');
    }

    public function systemAttributeDataProvider()
    {
        return array(
            array('Resolution'),
            array('Item Condition'),
            array('Reason to Return'),
            array('Other')
        );
    }
}
