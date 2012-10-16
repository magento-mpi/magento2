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
 * Edit RMA item attribute
 *
 * @package     Mage_RMA
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise2_Mage_RMA_ItemAttribute_EditTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>Navigate to System -> Manage Attributes.</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_rma_items_attribute');
    }

    /**
     * <p>Edit RMA item Attribute</p>
     * <p>Preconditions</p>
     * <p>1. All type RMA item attribute are created</p>
     * <p>Steps:</p>
     * <p>1. Navigate to Sales-RMA-Manage RMA Items Attributes<p>
     * <p>2. Open "Text Field" attribute information page</p>
     * <p>3. Change data in some fields/p>
     * <p>4. Click "Save Attribute" button</p>
     * <p>5. Verify that information is saved</p>
     * <p>6. Repeat step 1-5 with other attribute type: Text Area, Dropdown, Image File</p>
     * <p>Expected result:</p>
     * <p>Success message: 'The RMA item attribute has been saved.' is displayed.</p>
     *
     * @test
     * @dataProvider customAttributeDataProvider
     * @TestlinkId TL-MAGE-6120
     */
    public function customAttribute($attributeType)
    {
        //Data
        $attrData = $this->loadDataSet('RMAItemsAttribute', $attributeType);
        $this->addParameter('attribute_admin_title', $attrData['admin_title']);
        //Steps
        $this->productAttributeHelper()->createAttribute($attrData);
        $this->assertMessagePresent('success', 'success_saved_attribute');
        $this->searchAndOpen(array('filter_attribute_code' => $attrData['attribute_code']));
        $this->fillField('sort_order', 5);
        $attrData['sort_order'] = 5;
        $this->openTab('manage_labels_options');
        $this->fillField('admin_title', 'Title after edit');
        $attrData['admin_title'] = 'Title after edit';
        $this->clickButton('save_attribute');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_attribute');
        $this->addParameter('attribute_admin_title', $attrData['admin_title']);
        $this->searchAndOpen(array('filter_attribute_code' => $attrData['attribute_code']));
        $this->productAttributeHelper()->verifyAttribute($attrData);
    }

    public function customAttributeDataProvider()
    {
        return array(
            array('rma_item_attribute_textfield'),
            array('rma_item_attribute_textarea'),
            array('rma_item_attribute_dropdown'),
            array('rma_item_attribute_image')
        );
    }
}
